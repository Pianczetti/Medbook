<?php

declare(strict_types=1);

namespace MedBook\Booking\Service;

use Doctrine\DBAL\Connection;

class RecurrenceService
{
    public function __construct(
        private readonly Connection $connection,
        private readonly string $dbPrefix,
    ) {
    }

    /**
     * Create a suggested next visit based on recurrence configuration for the resource.
     */
    public function createSuggestion(int $bookingId): ?int
    {
        $booking = $this->connection->createQueryBuilder()
            ->select('b.id_resource', 'b.id_customer', 'b.booking_date', 'b.time_start', 'b.time_end')
            ->from($this->dbPrefix . 'medbook_booking', 'b')
            ->where('b.id_booking = :id')
            ->setParameter('id', $bookingId)
            ->executeQuery()
            ->fetchAssociative();

        if (!$booking) {
            return null;
        }

        $config = $this->connection->createQueryBuilder()
            ->select('*')
            ->from($this->dbPrefix . 'medbook_recurrence_config')
            ->where('id_resource = :resource_id')
            ->andWhere('is_active = 1')
            ->setParameter('resource_id', (int) $booking['id_resource'])
            ->executeQuery()
            ->fetchAssociative();

        if (!$config) {
            return null;
        }

        $interval = (int) $config['interval_days'];
        $suggestedDate = (new \DateTime($booking['booking_date']))->modify("+{$interval} days");

        $this->connection->insert($this->dbPrefix . 'medbook_suggested_visit', [
            'id_booking' => $bookingId,
            'id_resource' => (int) $booking['id_resource'],
            'id_customer' => (int) $booking['id_customer'],
            'suggested_date_from' => $suggestedDate->format('Y-m-d'),
            'suggested_date_to' => $suggestedDate->format('Y-m-d'),
            'status' => 'pending',
            'date_add' => (new \DateTime())->format('Y-m-d H:i:s'),
        ]);

        return (int) $this->connection->lastInsertId();
    }

    /**
     * Get suggested visits with optional filters (for grid data provider).
     *
     * @return array<int, array<string, mixed>>
     */
    public function getSuggestedVisits(array $filters = []): array
    {
        $qb = $this->connection->createQueryBuilder()
            ->select('sv.*')
            ->from($this->dbPrefix . 'medbook_suggested_visit', 'sv')
            ->orderBy('sv.suggested_date_from', 'ASC');

        if (!empty($filters['status'])) {
            $qb->andWhere('sv.status = :status')
                ->setParameter('status', $filters['status']);
        }

        return $qb->executeQuery()->fetchAllAssociative();
    }

    /**
     * Dismiss a suggested visit.
     */
    public function dismissSuggestion(int $id): void
    {
        $this->connection->update(
            $this->dbPrefix . 'medbook_suggested_visit',
            ['status' => 'dismissed'],
            ['id_suggested_visit' => $id]
        );
    }

    /**
     * Mark a suggested visit as booked.
     */
    public function markBooked(int $id): void
    {
        $this->connection->update(
            $this->dbPrefix . 'medbook_suggested_visit',
            ['status' => 'booked'],
            ['id_suggested_visit' => $id]
        );
    }
}
