<?php

declare(strict_types=1);

namespace MedBook\Booking\Service;

use Doctrine\DBAL\Connection;

final class ConsentService
{
    public function __construct(
        private readonly Connection $connection,
        private readonly string $dbPrefix,
    ) {
    }

    /**
     * Record a patient consent.
     */
    public function recordConsent(int $customerId, string $consentType, string $ipAddress, string $version = '1.0'): int
    {
        $this->connection->insert($this->dbPrefix . 'medbook_consent', [
            'id_customer' => $customerId,
            'consent_type' => $consentType,
            'granted_at' => (new \DateTime())->format('Y-m-d H:i:s'),
            'revoked_at' => null,
            'ip_address' => $ipAddress,
            'version' => $version,
        ]);

        return (int) $this->connection->lastInsertId();
    }

    /**
     * Check if a customer has an active consent for a given type.
     */
    public function hasConsent(int $customerId, string $consentType): bool
    {
        $result = $this->connection->createQueryBuilder()
            ->select('COUNT(*)')
            ->from($this->dbPrefix . 'medbook_consent')
            ->where('id_customer = :customer_id')
            ->andWhere('consent_type = :consent_type')
            ->andWhere('revoked_at IS NULL')
            ->setParameter('customer_id', $customerId)
            ->setParameter('consent_type', $consentType)
            ->executeQuery()
            ->fetchOne();

        return (int) $result > 0;
    }

    /**
     * Revoke a consent for a customer.
     */
    public function revokeConsent(int $customerId, string $consentType): void
    {
        $this->connection->createQueryBuilder()
            ->update($this->dbPrefix . 'medbook_consent')
            ->set('revoked_at', ':revoked_at')
            ->where('id_customer = :customer_id')
            ->andWhere('consent_type = :consent_type')
            ->andWhere('revoked_at IS NULL')
            ->setParameter('revoked_at', (new \DateTime())->format('Y-m-d H:i:s'))
            ->setParameter('customer_id', $customerId)
            ->setParameter('consent_type', $consentType)
            ->executeQuery();
    }

    /**
     * Get full consent history for a customer.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getConsentHistory(int $customerId): array
    {
        return $this->connection->createQueryBuilder()
            ->select('*')
            ->from($this->dbPrefix . 'medbook_consent')
            ->where('id_customer = :customer_id')
            ->setParameter('customer_id', $customerId)
            ->orderBy('granted_at', 'DESC')
            ->executeQuery()
            ->fetchAllAssociative();
    }

    /**
     * Generate a RODO-compliant data export for a customer (all consents + related data).
     *
     * @return array<string, mixed>
     */
    public function generateDataExport(int $customerId): array
    {
        $consents = $this->getConsentHistory($customerId);

        $bookings = $this->connection->createQueryBuilder()
            ->select('id_booking', 'booking_date', 'time_start', 'time_end', 'status', 'customer_name', 'customer_email', 'date_add')
            ->from($this->dbPrefix . 'medbook_booking')
            ->where('id_customer = :customer_id')
            ->setParameter('customer_id', $customerId)
            ->orderBy('date_add', 'DESC')
            ->executeQuery()
            ->fetchAllAssociative();

        $documents = $this->connection->createQueryBuilder()
            ->select('id_document', 'document_type', 'original_name', 'date_add')
            ->from($this->dbPrefix . 'medbook_document')
            ->where('id_customer = :customer_id')
            ->setParameter('customer_id', $customerId)
            ->orderBy('date_add', 'DESC')
            ->executeQuery()
            ->fetchAllAssociative();

        return [
            'export_date' => (new \DateTime())->format('Y-m-d H:i:s'),
            'customer_id' => $customerId,
            'consents' => $consents,
            'bookings' => $bookings,
            'documents' => $documents,
        ];
    }
}
