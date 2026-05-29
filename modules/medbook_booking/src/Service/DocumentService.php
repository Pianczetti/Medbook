<?php

declare(strict_types=1);

namespace MedBook\Booking\Service;

use Doctrine\DBAL\Connection;

final class DocumentService
{
    private const ALLOWED_MIME_TYPES = [
        'application/pdf',
        'image/jpeg',
        'image/png',
    ];

    private const MAX_FILE_SIZE = 10 * 1024 * 1024; // 10 MB

    public function __construct(
        private readonly Connection $connection,
        private readonly string $dbPrefix,
        private readonly string $uploadDir,
    ) {
    }

    /**
     * Upload a medical document.
     *
     * @param array{name: string, type: string, tmp_name: string, size: int} $file
     *
     * @throws \InvalidArgumentException
     */
    public function upload(
        int $customerId,
        array $file,
        string $documentType = 'other',
        ?int $bookingId = null,
        string $uploadedBy = 'patient'
    ): int {
        $this->validateFile($file);

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = bin2hex(random_bytes(16)) . '.' . $extension;

        $targetDir = $this->uploadDir . '/' . $customerId;
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $targetPath = $targetDir . '/' . $filename;
        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            throw new \RuntimeException('Nie udalo sie przeniesc pliku.');
        }

        $this->connection->insert($this->dbPrefix . 'medbook_document', [
            'id_customer' => $customerId,
            'id_booking' => $bookingId,
            'document_type' => $documentType,
            'filename' => $filename,
            'original_name' => $file['name'],
            'mime_type' => $file['type'],
            'file_size' => $file['size'],
            'uploaded_by' => $uploadedBy,
            'date_add' => (new \DateTime())->format('Y-m-d H:i:s'),
        ]);

        return (int) $this->connection->lastInsertId();
    }

    /**
     * Download a document - returns file path and metadata.
     *
     * @return array{path: string, original_name: string, mime_type: string}|null
     */
    public function download(int $documentId, int $customerId): ?array
    {
        $document = $this->connection->createQueryBuilder()
            ->select('*')
            ->from($this->dbPrefix . 'medbook_document')
            ->where('id_document = :id')
            ->andWhere('id_customer = :customer_id')
            ->setParameter('id', $documentId)
            ->setParameter('customer_id', $customerId)
            ->executeQuery()
            ->fetchAssociative();

        if (!$document) {
            return null;
        }

        $filePath = $this->uploadDir . '/' . $customerId . '/' . $document['filename'];
        if (!file_exists($filePath)) {
            return null;
        }

        return [
            'path' => $filePath,
            'original_name' => $document['original_name'],
            'mime_type' => $document['mime_type'],
        ];
    }

    /**
     * List documents for a specific booking.
     *
     * @return array<int, array<string, mixed>>
     */
    public function listForBooking(int $bookingId): array
    {
        return $this->connection->createQueryBuilder()
            ->select('*')
            ->from($this->dbPrefix . 'medbook_document')
            ->where('id_booking = :booking_id')
            ->setParameter('booking_id', $bookingId)
            ->orderBy('date_add', 'DESC')
            ->executeQuery()
            ->fetchAllAssociative();
    }

    /**
     * List all documents for a patient.
     *
     * @return array<int, array<string, mixed>>
     */
    public function listForPatient(int $customerId): array
    {
        return $this->connection->createQueryBuilder()
            ->select('*')
            ->from($this->dbPrefix . 'medbook_document')
            ->where('id_customer = :customer_id')
            ->setParameter('customer_id', $customerId)
            ->orderBy('date_add', 'DESC')
            ->executeQuery()
            ->fetchAllAssociative();
    }

    /**
     * Delete a document.
     */
    public function delete(int $documentId, int $customerId): bool
    {
        $document = $this->connection->createQueryBuilder()
            ->select('filename')
            ->from($this->dbPrefix . 'medbook_document')
            ->where('id_document = :id')
            ->andWhere('id_customer = :customer_id')
            ->setParameter('id', $documentId)
            ->setParameter('customer_id', $customerId)
            ->executeQuery()
            ->fetchAssociative();

        if (!$document) {
            return false;
        }

        $filePath = $this->uploadDir . '/' . $customerId . '/' . $document['filename'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        $this->connection->delete($this->dbPrefix . 'medbook_document', [
            'id_document' => $documentId,
            'id_customer' => $customerId,
        ]);

        return true;
    }

    /**
     * @param array{name: string, type: string, tmp_name: string, size: int} $file
     */
    private function validateFile(array $file): void
    {
        if (empty($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            throw new \InvalidArgumentException('Nieprawidlowy plik.');
        }

        if (!in_array($file['type'], self::ALLOWED_MIME_TYPES, true)) {
            throw new \InvalidArgumentException('Niedozwolony typ pliku. Dozwolone: PDF, JPG, PNG.');
        }

        if ($file['size'] > self::MAX_FILE_SIZE) {
            throw new \InvalidArgumentException('Plik przekracza maksymalny rozmiar 10 MB.');
        }
    }
}
