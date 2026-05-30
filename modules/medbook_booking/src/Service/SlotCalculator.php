<?php

declare(strict_types=1);

namespace MedBook\Booking\Service;

/**
 * Pure logic service for calculating available time slots.
 * No DB access - receives all data from the provider.
 */
class SlotCalculator
{
    /**
     * Calculate available slot start times for a given date.
     *
     * @param array $scheduleEntries Array of schedule rows with keys: time_start, time_end, is_available
     * @param array $existingBookings Array of booking rows with keys: time_start, time_end
     * @param int $durationMinutes Slot duration in minutes
     * @param int $bufferMinutes Buffer between slots in minutes
     * @param int $capacity Max simultaneous bookings per slot
     * @param bool $isBlockedDate Whether the entire date is blocked
     *
     * @return array Array of available slot strings (H:i format)
     */
    public function calculateAvailableSlots(
        array $scheduleEntries,
        array $existingBookings,
        int $durationMinutes,
        int $bufferMinutes,
        int $capacity,
        bool $isBlockedDate = false,
    ): array {
        if ($isBlockedDate) {
            return [];
        }

        if (empty($scheduleEntries)) {
            return [];
        }

        $availableSlots = [];

        foreach ($scheduleEntries as $schedule) {
            if (empty($schedule['is_available'])) {
                continue;
            }

            $windowStart = $this->timeToMinutes($schedule['time_start']);
            $windowEnd = $this->timeToMinutes($schedule['time_end']);

            $cursor = $windowStart;

            while (($cursor + $durationMinutes) <= $windowEnd) {
                $slotStart = $this->minutesToTime($cursor);
                $slotEnd = $this->minutesToTime($cursor + $durationMinutes);

                $bookingsInSlot = $this->countOverlappingBookings(
                    $existingBookings,
                    $slotStart,
                    $slotEnd
                );

                if ($bookingsInSlot < $capacity) {
                    $availableSlots[] = $slotStart;
                }

                $cursor += $durationMinutes + $bufferMinutes;
            }
        }

        $availableSlots = array_unique($availableSlots);
        sort($availableSlots);

        return $availableSlots;
    }

    /**
     * Calculate available slot start times with variable durations.
     * Returns slots grouped by possible durations when min != max.
     *
     * @param array $scheduleEntries Array of schedule rows with keys: time_start, time_end, is_available
     * @param array $existingBookings Array of booking rows with keys: time_start, time_end
     * @param int $minDuration Minimum slot duration in minutes
     * @param int $maxDuration Maximum slot duration in minutes
     * @param int $bufferMinutes Buffer between slots in minutes
     * @param int $capacity Max simultaneous bookings per slot
     * @param bool $isBlockedDate Whether the entire date is blocked
     *
     * @return array Array of slots with 'time' and 'durations' keys
     */
    public function calculateAvailableSlotsWithDuration(
        array $scheduleEntries,
        array $existingBookings,
        int $minDuration,
        int $maxDuration,
        int $bufferMinutes,
        int $capacity,
        bool $isBlockedDate = false,
    ): array {
        if ($isBlockedDate) {
            return [];
        }

        if (empty($scheduleEntries)) {
            return [];
        }

        if ($minDuration <= 0) {
            $minDuration = 30;
        }

        if ($maxDuration <= 0 || $maxDuration < $minDuration) {
            $maxDuration = $minDuration;
        }

        $step = $minDuration; // step in minutes between slot start times
        $availableSlots = [];

        foreach ($scheduleEntries as $schedule) {
            if (empty($schedule['is_available'])) {
                continue;
            }

            $windowStart = $this->timeToMinutes($schedule['time_start']);
            $windowEnd = $this->timeToMinutes($schedule['time_end']);

            $cursor = $windowStart;

            while (($cursor + $minDuration) <= $windowEnd) {
                $slotStart = $this->minutesToTime($cursor);

                // Find all possible durations for this start time
                $possibleDurations = [];
                for ($dur = $minDuration; $dur <= $maxDuration; $dur += $minDuration) {
                    if (($cursor + $dur) > $windowEnd) {
                        break;
                    }

                    $slotEnd = $this->minutesToTime($cursor + $dur);

                    $bookingsInSlot = $this->countOverlappingBookings(
                        $existingBookings,
                        $slotStart,
                        $slotEnd
                    );

                    if ($bookingsInSlot < $capacity) {
                        $possibleDurations[] = $dur;
                    }
                }

                if (!empty($possibleDurations)) {
                    $availableSlots[] = [
                        'time' => $slotStart,
                        'durations' => $possibleDurations,
                    ];
                }

                $cursor += $step + $bufferMinutes;
            }
        }

        // Remove duplicate start times (keep first occurrence)
        $seen = [];
        $uniqueSlots = [];
        foreach ($availableSlots as $slot) {
            if (!isset($seen[$slot['time']])) {
                $seen[$slot['time']] = true;
                $uniqueSlots[] = $slot;
            }
        }

        usort($uniqueSlots, function (array $a, array $b): int {
            return strcmp($a['time'], $b['time']);
        });

        return $uniqueSlots;
    }

    /**
     * Count how many existing bookings overlap with a given time range.
     */
    private function countOverlappingBookings(array $bookings, string $slotStart, string $slotEnd): int
    {
        $count = 0;
        $slotStartMin = $this->timeToMinutes($slotStart);
        $slotEndMin = $this->timeToMinutes($slotEnd);

        foreach ($bookings as $booking) {
            $bookingStart = $this->timeToMinutes($booking['time_start']);
            $bookingEnd = $this->timeToMinutes($booking['time_end']);

            // Overlap exists when one starts before the other ends
            if ($bookingStart < $slotEndMin && $bookingEnd > $slotStartMin) {
                ++$count;
            }
        }

        return $count;
    }

    /**
     * Convert H:i or H:i:s time string to minutes since midnight.
     */
    private function timeToMinutes(string $time): int
    {
        $parts = explode(':', $time);

        return ((int) $parts[0]) * 60 + ((int) ($parts[1] ?? 0));
    }

    /**
     * Convert minutes since midnight to H:i format.
     */
    private function minutesToTime(int $minutes): string
    {
        $hours = intdiv($minutes, 60);
        $mins = $minutes % 60;

        return sprintf('%02d:%02d', $hours, $mins);
    }
}
