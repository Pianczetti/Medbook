<?php

declare(strict_types=1);

namespace MedBook\Booking\Service;

use Doctrine\DBAL\Connection;

class WaitlistService
{
    public function __construct(
        private readonly Connection $connection,
        private readonly string $dbPrefix,
    ) {
    }

    /**
     * Find matching waitlist entries for a freed slot, sorted by priority DESC then date_add ASC.
     * Fetches all active entries for the resource and filters in PHP by decoding the JSON blobs.
     *
     * @return array<int, array<string, mixed>>
     */
    public function findMatchingEntries(int $resourceId, string $date, string $timeStart, string $timeEnd): array
    {
        $entries = $this->connection->createQueryBuilder()
            ->select('w.*')
            ->from($this->dbPrefix . 'medbook_waitlist', 'w')
            ->where('w.id_resource = :resource_id')
            ->andWhere('w.status = :status')
            ->setParameter('resource_id', $resourceId)
            ->setParameter('status', 'active')
            ->orderBy('w.is_priority', 'DESC')
            ->addOrderBy('w.date_add', 'ASC')
            ->executeQuery()
            ->fetchAllAssociative();

        $matching = [];

        foreach ($entries as $entry) {
            $preferredDates = json_decode($entry['preferred_dates_json'], true);
            if (!is_array($preferredDates) || !in_array($date, $preferredDates, true)) {
                continue;
            }

            if (!empty($entry['preferred_times_json'])) {
                $preferredTimes = json_decode($entry['preferred_times_json'], true);
                if (is_array($preferredTimes) && !empty($preferredTimes)) {
                    $timeMatches = false;
                    foreach ($preferredTimes as $timeRange) {
                        $prefStart = $timeRange['start'] ?? $timeRange['time_start'] ?? null;
                        $prefEnd = $timeRange['end'] ?? $timeRange['time_end'] ?? null;
                        if ($prefStart !== null && $prefEnd !== null) {
                            // Check if freed slot overlaps with preferred time range
                            if ($timeStart < $prefEnd && $timeEnd > $prefStart) {
                                $timeMatches = true;
                                break;
                            }
                        }
                    }
                    if (!$timeMatches) {
                        continue;
                    }
                }
            }

            $matching[] = $entry;
        }

        return $matching;
    }

    /**
     * Notify a waitlist entry: set status to notified and record the notification time.
     */
    public function notifyEntry(int $waitlistId): void
    {
        $this->connection->update(
            $this->dbPrefix . 'medbook_waitlist',
            [
                'status' => 'notified',
                'notified_at' => (new \DateTime())->format('Y-m-d H:i:s'),
            ],
            ['id_waitlist' => $waitlistId]
        );
    }

    /**
     * Expire stale notifications: entries notified more than $hours ago that have not been booked.
     * Moves them to expired and attempts to notify the next matching entry for each resource.
     */
    public function expireStaleNotifications(int $hours): int
    {
        $cutoff = (new \DateTime())->modify("-{$hours} hours")->format('Y-m-d H:i:s');

        $staleEntries = $this->connection->createQueryBuilder()
            ->select('w.id_waitlist', 'w.id_resource', 'w.preferred_dates_json', 'w.preferred_times_json')
            ->from($this->dbPrefix . 'medbook_waitlist', 'w')
            ->where('w.status = :status')
            ->andWhere('w.notified_at < :cutoff')
            ->setParameter('status', 'notified')
            ->setParameter('cutoff', $cutoff)
            ->executeQuery()
            ->fetchAllAssociative();

        $count = 0;
        foreach ($staleEntries as $entry) {
            $this->connection->update(
                $this->dbPrefix . 'medbook_waitlist',
                ['status' => 'expired'],
                ['id_waitlist' => (int) $entry['id_waitlist']]
            );

            // Try to notify next matching entry using the first preferred date/time
            $preferredDates = json_decode($entry['preferred_dates_json'], true);
            if (is_array($preferredDates) && !empty($preferredDates)) {
                $date = $preferredDates[0];
                $timeStart = '00:00';
                $timeEnd = '23:59';

                if (!empty($entry['preferred_times_json'])) {
                    $preferredTimes = json_decode($entry['preferred_times_json'], true);
                    if (is_array($preferredTimes) && !empty($preferredTimes)) {
                        $timeStart = $preferredTimes[0]['start'] ?? $preferredTimes[0]['time_start'] ?? '00:00';
                        $timeEnd = $preferredTimes[0]['end'] ?? $preferredTimes[0]['time_end'] ?? '23:59';
                    }
                }

                $nextEntries = $this->findMatchingEntries(
                    (int) $entry['id_resource'],
                    $date,
                    $timeStart,
                    $timeEnd
                );

                if (!empty($nextEntries)) {
                    $this->notifyEntry((int) $nextEntries[0]['id_waitlist']);
                }
            }

            ++$count;
        }

        return $count;
    }
}
