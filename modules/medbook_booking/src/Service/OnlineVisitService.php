<?php

declare(strict_types=1);

namespace MedBook\Booking\Service;

use Doctrine\DBAL\Connection;

final class OnlineVisitService
{
    public function __construct(
        private readonly Connection $connection,
        private readonly string $dbPrefix,
    ) {
    }

    /**
     * Generate a unique meeting room URL for an online visit.
     */
    public function generateMeetingUrl(int $bookingId): string
    {
        $token = bin2hex(random_bytes(16));
        $url = '/meeting/' . $bookingId . '/' . $token;

        $this->connection->update(
            $this->dbPrefix . 'medbook_booking',
            ['video_call_url' => $url],
            ['id_booking' => $bookingId]
        );

        return $url;
    }

    /**
     * Check if a booking is an online visit.
     */
    public function isOnlineVisit(int $bookingId): bool
    {
        $visitType = $this->connection->createQueryBuilder()
            ->select('visit_type')
            ->from($this->dbPrefix . 'medbook_booking')
            ->where('id_booking = :id')
            ->setParameter('id', $bookingId)
            ->executeQuery()
            ->fetchOne();

        return $visitType === 'online';
    }

    /**
     * Get the video call URL for a booking.
     */
    public function getMeetingUrl(int $bookingId): ?string
    {
        $url = $this->connection->createQueryBuilder()
            ->select('video_call_url')
            ->from($this->dbPrefix . 'medbook_booking')
            ->where('id_booking = :id')
            ->setParameter('id', $bookingId)
            ->executeQuery()
            ->fetchOne();

        return $url ?: null;
    }

    /**
     * Generate meeting URL on booking confirmation if visit type is online.
     */
    public function handleBookingConfirmed(int $bookingId): ?string
    {
        if (!$this->isOnlineVisit($bookingId)) {
            return null;
        }

        $existingUrl = $this->getMeetingUrl($bookingId);
        if ($existingUrl) {
            return $existingUrl;
        }

        return $this->generateMeetingUrl($bookingId);
    }
}
