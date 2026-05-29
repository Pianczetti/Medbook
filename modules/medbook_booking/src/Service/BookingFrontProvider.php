<?php

declare(strict_types=1);

namespace MedBook\Booking\Service;

use Doctrine\DBAL\Connection;

class BookingFrontProvider
{
    public function __construct(
        private readonly Connection $connection,
        private readonly string $dbPrefix,
        private readonly SlotCalculator $slotCalculator,
    ) {
    }

    /**
     * Get all active resources with their lang data.
     */
    public function getActiveResources(int $langId): array
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->select('r.id_resource', 'r.resource_type', 'r.capacity', 'r.duration_minutes', 'r.buffer_minutes', 'r.color', 'r.position', 'r.base_price', 'r.min_duration_minutes', 'r.max_duration_minutes', 'rl.name', 'rl.description')
            ->from($this->dbPrefix . 'medbook_resource', 'r')
            ->leftJoin('r', $this->dbPrefix . 'medbook_resource_lang', 'rl', 'r.id_resource = rl.id_resource AND rl.id_lang = :langId')
            ->where('r.is_active = 1')
            ->orderBy('r.position', 'ASC')
            ->setParameter('langId', $langId);

        return $qb->executeQuery()->fetchAllAssociative();
    }

    /**
     * Get available slots for a given resource and date.
     */
    public function getAvailableSlots(int $resourceId, string $date, int $langId): array
    {
        $resource = $this->getResource($resourceId);
        if (!$resource || !$resource['is_active']) {
            return [];
        }

        $dateObj = new \DateTime($date);
        $dayOfWeek = (int) $dateObj->format('N'); // 1=Monday, 7=Sunday

        // Get schedule entries for this resource on this day
        $scheduleEntries = $this->getScheduleEntries($resourceId, $dayOfWeek, $date);

        // Check if date is blocked
        $isBlocked = $this->isDateBlocked($resourceId, $date);

        // Get existing bookings for this resource on this date (non-cancelled)
        $existingBookings = $this->getExistingBookings($resourceId, $date);

        // Use variable-duration method when min and max duration differ
        $minDuration = (int) ($resource['min_duration_minutes'] ?? 0);
        $maxDuration = (int) ($resource['max_duration_minutes'] ?? 0);

        if ($minDuration > 0 && $maxDuration > 0 && $minDuration !== $maxDuration) {
            $slotsWithDuration = $this->slotCalculator->calculateAvailableSlotsWithDuration(
                $scheduleEntries,
                $existingBookings,
                $minDuration,
                $maxDuration,
                (int) $resource['buffer_minutes'],
                (int) $resource['capacity'],
                $isBlocked
            );

            // Extract just the time strings for backward compatibility
            $slots = array_map(function (array $slot): string {
                return $slot['time'];
            }, $slotsWithDuration);
        } else {
            $slots = $this->slotCalculator->calculateAvailableSlots(
                $scheduleEntries,
                $existingBookings,
                (int) $resource['duration_minutes'],
                (int) $resource['buffer_minutes'],
                (int) $resource['capacity'],
                $isBlocked
            );
        }

        // Enforce min_hours_advance: filter out slots that are too close to now
        $minHoursAdvance = (int) \Configuration::get('MEDBOOK_BOOKING_MIN_HOURS_ADVANCE');
        $today = (new \DateTime())->format('Y-m-d');

        if ($minHoursAdvance > 0 && $date === $today) {
            $earliestMinutes = $this->timeToMinutes((new \DateTime("+{$minHoursAdvance} hours"))->format('H:i'));
            $slots = array_values(array_filter($slots, function (string $slot) use ($earliestMinutes): bool {
                return $this->timeToMinutes($slot) >= $earliestMinutes;
            }));
        }

        return $slots;
    }

    /**
     * Get available slots with duration options for variable-duration resources.
     * Returns the rich structure: [{time: string, durations: int[]}]
     * Falls back to simple slot format (durations = [duration_minutes]) for fixed-duration resources.
     */
    public function getAvailableSlotsWithDurations(int $resourceId, string $date, int $langId): array
    {
        $resource = $this->getResource($resourceId);
        if (!$resource || !$resource['is_active']) {
            return [];
        }

        $dateObj = new \DateTime($date);
        $dayOfWeek = (int) $dateObj->format('N');

        $scheduleEntries = $this->getScheduleEntries($resourceId, $dayOfWeek, $date);
        $isBlocked = $this->isDateBlocked($resourceId, $date);
        $existingBookings = $this->getExistingBookings($resourceId, $date);

        $minDuration = (int) ($resource['min_duration_minutes'] ?? 0);
        $maxDuration = (int) ($resource['max_duration_minutes'] ?? 0);

        if ($minDuration > 0 && $maxDuration > 0 && $minDuration !== $maxDuration) {
            $slots = $this->slotCalculator->calculateAvailableSlotsWithDuration(
                $scheduleEntries,
                $existingBookings,
                $minDuration,
                $maxDuration,
                (int) $resource['buffer_minutes'],
                (int) $resource['capacity'],
                $isBlocked
            );
        } else {
            // Fixed duration: wrap each slot in the rich format with a single duration option
            $fixedDuration = (int) $resource['duration_minutes'];
            $plainSlots = $this->slotCalculator->calculateAvailableSlots(
                $scheduleEntries,
                $existingBookings,
                $fixedDuration,
                (int) $resource['buffer_minutes'],
                (int) $resource['capacity'],
                $isBlocked
            );

            $slots = array_map(function (string $time) use ($fixedDuration): array {
                return ['time' => $time, 'durations' => [$fixedDuration]];
            }, $plainSlots);
        }

        // Enforce min_hours_advance
        $minHoursAdvance = (int) \Configuration::get('MEDBOOK_BOOKING_MIN_HOURS_ADVANCE');
        $today = (new \DateTime())->format('Y-m-d');

        if ($minHoursAdvance > 0 && $date === $today) {
            $earliestMinutes = $this->timeToMinutes((new \DateTime("+{$minHoursAdvance} hours"))->format('H:i'));
            $slots = array_values(array_filter($slots, function (array $slot) use ($earliestMinutes): bool {
                return $this->timeToMinutes($slot['time']) >= $earliestMinutes;
            }));
        }

        return $slots;
    }

    /**
     * Create a booking with auto-generated reference code.
     * Uses SELECT ... FOR UPDATE to lock the resource row during slot validation,
     * preventing race conditions while still allowing rebooking of cancelled slots.
     *
     * @return array{success: bool, reference_code?: string, error?: string}
     */
    public function createBooking(array $data): array
    {
        $resourceId = (int) ($data['id_resource'] ?? 0);
        $date = (string) ($data['booking_date'] ?? '');
        $timeStart = (string) ($data['time_start'] ?? '');

        if (!$resourceId || !$date || !$timeStart) {
            return ['success' => false, 'error' => 'Missing required fields.'];
        }

        $resource = $this->getResource($resourceId);
        if (!$resource || !$resource['is_active']) {
            return ['success' => false, 'error' => 'Resource not available.'];
        }

        $this->connection->beginTransaction();

        try {
            // Lock the resource row to serialize concurrent booking attempts
            $this->connection->executeQuery(
                'SELECT id_resource FROM ' . $this->dbPrefix . 'medbook_resource WHERE id_resource = :id FOR UPDATE',
                ['id' => $resourceId]
            );

            // Verify slot is still available (only non-cancelled bookings block the slot)
            $availableSlots = $this->getAvailableSlots($resourceId, $date, 0);
            if (!in_array($timeStart, $availableSlots, true)) {
                $this->connection->rollBack();

                return ['success' => false, 'error' => 'Selected time slot is no longer available.'];
            }

            $duration = (int) $resource['duration_minutes'];
            $startMinutes = $this->timeToMinutes($timeStart);
            $timeEnd = $this->minutesToTime($startMinutes + $duration);

            $referenceCode = $this->generateReferenceCode();

            $now = (new \DateTime())->format('Y-m-d H:i:s');

            $this->connection->insert($this->dbPrefix . 'medbook_booking', [
                'id_resource' => $resourceId,
                'id_customer' => $data['id_customer'] ?? null,
                'booking_date' => $date,
                'time_start' => $timeStart,
                'time_end' => $timeEnd,
                'status' => 'pending',
                'customer_name' => (string) ($data['customer_name'] ?? ''),
                'customer_email' => (string) ($data['customer_email'] ?? ''),
                'customer_phone' => $data['customer_phone'] ?? null,
                'notes' => $data['notes'] ?? null,
                'reference_code' => $referenceCode,
                'date_add' => $now,
                'date_upd' => $now,
            ]);

            $this->connection->commit();

            return ['success' => true, 'reference_code' => $referenceCode];
        } catch (\Throwable $e) {
            $this->connection->rollBack();

            return ['success' => false, 'error' => 'An error occurred while creating the booking.'];
        }
    }

    /**
     * Get bookings for a customer.
     */
    public function getCustomerBookings(int $customerId, int $langId = 1): array
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->select('b.*', 'rl.name as resource_name')
            ->from($this->dbPrefix . 'medbook_booking', 'b')
            ->leftJoin('b', $this->dbPrefix . 'medbook_resource_lang', 'rl', 'b.id_resource = rl.id_resource AND rl.id_lang = :langId')
            ->where('b.id_customer = :customerId')
            ->orderBy('b.booking_date', 'DESC')
            ->addOrderBy('b.time_start', 'DESC')
            ->setParameter('customerId', $customerId)
            ->setParameter('langId', $langId);

        return $qb->executeQuery()->fetchAllAssociative();
    }

    /**
     * Cancel a booking (only if it belongs to the customer and is pending/confirmed).
     *
     * @return array{success: bool, error?: string}
     */
    public function cancelBooking(int $bookingId, int $customerId): array
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->select('b.id_booking', 'b.status', 'b.id_customer')
            ->from($this->dbPrefix . 'medbook_booking', 'b')
            ->where('b.id_booking = :bookingId')
            ->setParameter('bookingId', $bookingId);

        $booking = $qb->executeQuery()->fetchAssociative();

        if (!$booking) {
            return ['success' => false, 'error' => 'Booking not found.'];
        }

        if ((int) $booking['id_customer'] !== $customerId) {
            return ['success' => false, 'error' => 'Unauthorized.'];
        }

        if (!in_array($booking['status'], ['pending', 'confirmed'], true)) {
            return ['success' => false, 'error' => 'Booking cannot be cancelled.'];
        }

        $this->connection->update(
            $this->dbPrefix . 'medbook_booking',
            [
                'status' => 'cancelled',
                'date_upd' => (new \DateTime())->format('Y-m-d H:i:s'),
            ],
            ['id_booking' => $bookingId]
        );

        return ['success' => true];
    }

    /**
     * Get next N available slots across all resources.
     */
    public function getNextAvailableSlots(int $limit, int $langId): array
    {
        $resources = $this->getActiveResources($langId);
        $slots = [];
        $today = new \DateTime();

        for ($dayOffset = 0; $dayOffset < 14 && count($slots) < $limit; ++$dayOffset) {
            $date = (clone $today)->modify("+{$dayOffset} days")->format('Y-m-d');

            foreach ($resources as $resource) {
                $available = $this->getAvailableSlots((int) $resource['id_resource'], $date, $langId);

                foreach ($available as $time) {
                    $slots[] = [
                        'resource_name' => $resource['name'] ?? '',
                        'resource_id' => (int) $resource['id_resource'],
                        'date' => $date,
                        'time' => $time,
                        'color' => $resource['color'],
                    ];

                    if (count($slots) >= $limit) {
                        break 3;
                    }
                }
            }
        }

        return $slots;
    }

    /**
     * Get active add-ons for a resource (resource-specific + global where id_resource IS NULL).
     */
    public function getAddonsForResource(int $resourceId, int $langId): array
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->select('a.id_addon', 'a.price', 'a.position', 'al.name', 'al.description')
            ->from($this->dbPrefix . 'medbook_addon', 'a')
            ->leftJoin('a', $this->dbPrefix . 'medbook_addon_lang', 'al', 'a.id_addon = al.id_addon AND al.id_lang = :langId')
            ->where('(a.id_resource = :resourceId OR a.id_resource IS NULL)')
            ->andWhere('a.is_active = 1')
            ->orderBy('a.position', 'ASC')
            ->setParameter('resourceId', $resourceId)
            ->setParameter('langId', $langId);

        return $qb->executeQuery()->fetchAllAssociative();
    }

    /**
     * Get the applicable deposit rule for a resource (resource-specific first, global fallback).
     */
    public function getDepositRule(int $resourceId): ?array
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->select('dr.*')
            ->from($this->dbPrefix . 'medbook_deposit_rule', 'dr')
            ->where('(dr.id_resource = :resourceId OR dr.id_resource IS NULL)')
            ->andWhere('dr.is_active = 1')
            ->orderBy('dr.id_resource', 'DESC') // resource-specific first (non-null)
            ->setMaxResults(1)
            ->setParameter('resourceId', $resourceId);

        $result = $qb->executeQuery()->fetchAssociative();

        return $result ?: null;
    }

    /**
     * Get resource data including pricing and duration fields.
     */
    public function getResourceWithPricing(int $resourceId, int $langId): ?array
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->select('r.id_resource', 'r.resource_type', 'r.capacity', 'r.duration_minutes', 'r.buffer_minutes', 'r.color', 'r.is_active', 'r.base_price', 'r.min_duration_minutes', 'r.max_duration_minutes', 'rl.name', 'rl.description')
            ->from($this->dbPrefix . 'medbook_resource', 'r')
            ->leftJoin('r', $this->dbPrefix . 'medbook_resource_lang', 'rl', 'r.id_resource = rl.id_resource AND rl.id_lang = :langId')
            ->where('r.id_resource = :resourceId')
            ->setParameter('resourceId', $resourceId)
            ->setParameter('langId', $langId);

        $result = $qb->executeQuery()->fetchAssociative();

        return $result ?: null;
    }

    private function getResource(int $resourceId): ?array
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->select('*')
            ->from($this->dbPrefix . 'medbook_resource')
            ->where('id_resource = :id')
            ->setParameter('id', $resourceId);

        $result = $qb->executeQuery()->fetchAssociative();

        return $result ?: null;
    }

    private function getScheduleEntries(int $resourceId, int $dayOfWeek, string $date): array
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->select('time_start', 'time_end', 'is_available')
            ->from($this->dbPrefix . 'medbook_schedule')
            ->where('id_resource = :resourceId')
            ->andWhere('(day_of_week = :dayOfWeek OR specific_date = :date)')
            ->setParameter('resourceId', $resourceId)
            ->setParameter('dayOfWeek', $dayOfWeek)
            ->setParameter('date', $date);

        return $qb->executeQuery()->fetchAllAssociative();
    }

    private function isDateBlocked(int $resourceId, string $date): bool
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->select('COUNT(*)')
            ->from($this->dbPrefix . 'medbook_blocked_date')
            ->where('(id_resource = :resourceId OR id_resource IS NULL)')
            ->andWhere('blocked_date = :date')
            ->setParameter('resourceId', $resourceId)
            ->setParameter('date', $date);

        return (int) $qb->executeQuery()->fetchOne() > 0;
    }

    private function getExistingBookings(int $resourceId, string $date): array
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->select('time_start', 'time_end')
            ->from($this->dbPrefix . 'medbook_booking')
            ->where('id_resource = :resourceId')
            ->andWhere('booking_date = :date')
            ->andWhere('status NOT IN (:cancelled)')
            ->setParameter('resourceId', $resourceId)
            ->setParameter('date', $date)
            ->setParameter('cancelled', 'cancelled');

        return $qb->executeQuery()->fetchAllAssociative();
    }

    private function generateReferenceCode(): string
    {
        return 'BK' . strtoupper(bin2hex(random_bytes(5)));
    }

    private function timeToMinutes(string $time): int
    {
        $parts = explode(':', $time);

        return ((int) $parts[0]) * 60 + ((int) ($parts[1] ?? 0));
    }

    private function minutesToTime(int $minutes): string
    {
        $hours = intdiv($minutes, 60);
        $mins = $minutes % 60;

        return sprintf('%02d:%02d', $hours, $mins);
    }
}
