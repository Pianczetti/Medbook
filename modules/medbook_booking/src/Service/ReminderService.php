<?php

declare(strict_types=1);

namespace MedBook\Booking\Service;

use Doctrine\DBAL\Connection;
use PrestaShop\PrestaShop\Core\Context\LanguageContext;

class ReminderService
{
    public function __construct(
        private readonly Connection $connection,
        private readonly string $dbPrefix,
        private readonly LanguageContext $languageContext,
    ) {
    }

    /**
     * Find bookings that should receive a reminder (booked for the next $hoursBefore hours, not yet reminded).
     *
     * @return array<int, array<string, mixed>>
     */
    public function findBookingsToRemind(int $hoursBefore): array
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->select('b.*', 'rl.name AS resource_name')
            ->from($this->dbPrefix . 'medbook_booking', 'b')
            ->innerJoin('b', $this->dbPrefix . 'medbook_resource_lang', 'rl', 'b.id_resource = rl.id_resource AND rl.id_lang = :id_lang')
            ->leftJoin('b', $this->dbPrefix . 'medbook_reminder', 'rem', 'b.id_booking = rem.id_booking')
            ->where('rem.id_booking IS NULL')
            ->andWhere('b.status IN (:statuses)')
            ->andWhere('CONCAT(b.booking_date, \' \', b.time_start) BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL :hours HOUR)')
            ->setParameter('statuses', ['pending', 'confirmed'], Connection::PARAM_STR_ARRAY)
            ->setParameter('hours', $hoursBefore)
            ->setParameter('id_lang', $this->languageContext->getId());

        return $qb->executeQuery()->fetchAllAssociative();
    }

    /**
     * Generate confirm + cancel tokens for a booking. Each token is a 64-char hex string.
     * Tokens expire 2 hours after the appointment time.
     *
     * @return array{confirm_token: string, cancel_token: string}
     */
    public function generateTokens(int $bookingId): array
    {
        $confirmToken = bin2hex(random_bytes(32));
        $cancelToken = bin2hex(random_bytes(32));

        // Get the booking appointment time to set expiry 2h after
        $booking = $this->connection->createQueryBuilder()
            ->select('booking_date', 'time_start')
            ->from($this->dbPrefix . 'medbook_booking')
            ->where('id_booking = :id')
            ->setParameter('id', $bookingId)
            ->executeQuery()
            ->fetchAssociative();

        $expiresAt = new \DateTime($booking['booking_date'] . ' ' . $booking['time_start']);
        $expiresAt->modify('+2 hours');
        $expiresFormatted = $expiresAt->format('Y-m-d H:i:s');

        $this->connection->insert($this->dbPrefix . 'medbook_confirm_token', [
            'id_booking' => $bookingId,
            'token' => $confirmToken,
            'action' => 'confirm',
            'expires_at' => $expiresFormatted,
            'date_add' => (new \DateTime())->format('Y-m-d H:i:s'),
        ]);

        $this->connection->insert($this->dbPrefix . 'medbook_confirm_token', [
            'id_booking' => $bookingId,
            'token' => $cancelToken,
            'action' => 'cancel',
            'expires_at' => $expiresFormatted,
            'date_add' => (new \DateTime())->format('Y-m-d H:i:s'),
        ]);

        return [
            'confirm_token' => $confirmToken,
            'cancel_token' => $cancelToken,
        ];
    }

    /**
     * Mark a booking as reminded by inserting into the reminder table.
     */
    public function markReminded(int $bookingId): void
    {
        $this->connection->insert($this->dbPrefix . 'medbook_reminder', [
            'id_booking' => $bookingId,
            'sent_at' => (new \DateTime())->format('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Validate a token: returns token data if the token exists, is not expired, and has not been used.
     *
     * @return array<string, mixed>|null
     */
    public function validateToken(string $token): ?array
    {
        $qb = $this->connection->createQueryBuilder();
        $row = $qb->select('*')
            ->from($this->dbPrefix . 'medbook_confirm_token')
            ->where('token = :token')
            ->andWhere('expires_at > NOW()')
            ->andWhere('used_at IS NULL')
            ->setParameter('token', $token)
            ->executeQuery()
            ->fetchAssociative();

        return $row ?: null;
    }

    /**
     * Consume a token: mark it as used and return the token data including the action.
     *
     * @return array<string, mixed>
     */
    public function consumeToken(string $token): array
    {
        $tokenData = $this->validateToken($token);

        if ($tokenData === null) {
            throw new \RuntimeException('Token is invalid, expired, or already used.');
        }

        $this->connection->update(
            $this->dbPrefix . 'medbook_confirm_token',
            ['used_at' => (new \DateTime())->format('Y-m-d H:i:s')],
            ['token' => $token]
        );

        return $tokenData;
    }
}
