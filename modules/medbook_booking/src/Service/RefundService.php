<?php

declare(strict_types=1);

namespace MedBook\Booking\Service;

use Doctrine\DBAL\Connection;

class RefundService
{
    public function __construct(
        private readonly Connection $connection,
        private readonly string $dbPrefix,
    ) {
    }

    /**
     * Get refund policy rules for a resource (resource-specific + global fallback).
     * Ordered by hours_before DESC.
     *
     * @return array Array of refund rule rows
     */
    public function getRefundPolicy(int $resourceId): array
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->select('rr.*')
            ->from($this->dbPrefix . 'medbook_refund_rule', 'rr')
            ->where('(rr.id_resource = :resourceId OR rr.id_resource IS NULL)')
            ->orderBy('rr.hours_before', 'DESC')
            ->setParameter('resourceId', $resourceId);

        return $qb->executeQuery()->fetchAllAssociative();
    }

    /**
     * Calculate the refund amount for a booking based on time remaining before appointment.
     *
     * @return array{refund_percent: float, refund_amount: float, rule_applied: ?array}
     */
    public function calculateRefundAmount(int $bookingId): array
    {
        $booking = $this->getBooking($bookingId);

        if (!$booking) {
            return [
                'refund_percent' => 0.0,
                'refund_amount' => 0.0,
                'rule_applied' => null,
            ];
        }

        $totalPrice = (float) $booking['total_price'];
        $appointmentDateTime = new \DateTime($booking['booking_date'] . ' ' . $booking['time_start']);
        $now = new \DateTime();

        $hoursRemaining = ($appointmentDateTime->getTimestamp() - $now->getTimestamp()) / 3600;

        if ($hoursRemaining <= 0) {
            return [
                'refund_percent' => 0.0,
                'refund_amount' => 0.0,
                'rule_applied' => null,
            ];
        }

        $rules = $this->getRefundPolicy((int) $booking['id_resource']);

        // Find the applicable rule: highest hours_before that is <= actual hours remaining
        $applicableRule = null;
        foreach ($rules as $rule) {
            if ((int) $rule['hours_before'] <= $hoursRemaining) {
                $applicableRule = $rule;
                break; // rules are ordered by hours_before DESC, first match wins
            }
        }

        if (!$applicableRule) {
            return [
                'refund_percent' => 0.0,
                'refund_amount' => 0.0,
                'rule_applied' => null,
            ];
        }

        $refundPercent = (float) $applicableRule['refund_percent'];
        $refundAmount = round($totalPrice * ($refundPercent / 100), 2);

        return [
            'refund_percent' => $refundPercent,
            'refund_amount' => $refundAmount,
            'rule_applied' => $applicableRule,
        ];
    }

    /**
     * Format refund policy rules into human-readable strings.
     *
     * @return array Array of formatted strings
     */
    public function formatPolicyForDisplay(array $rules): array
    {
        $formatted = [];

        foreach ($rules as $rule) {
            $hours = (int) $rule['hours_before'];
            $percent = (float) $rule['refund_percent'];

            if ($hours >= 24) {
                $days = intdiv($hours, 24);
                $remainingHours = $hours % 24;
                if ($remainingHours > 0) {
                    $timeLabel = sprintf('%d day(s) %d hour(s)', $days, $remainingHours);
                } else {
                    $timeLabel = sprintf('%d day(s)', $days);
                }
            } else {
                $timeLabel = sprintf('%d hour(s)', $hours);
            }

            $formatted[] = sprintf(
                'Cancellation %s or more before: %s%% refund',
                $timeLabel,
                number_format($percent, 0)
            );
        }

        return $formatted;
    }

    private function getBooking(int $bookingId): ?array
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->select('b.*')
            ->from($this->dbPrefix . 'medbook_booking', 'b')
            ->where('b.id_booking = :bookingId')
            ->setParameter('bookingId', $bookingId);

        $result = $qb->executeQuery()->fetchAssociative();

        return $result ?: null;
    }
}
