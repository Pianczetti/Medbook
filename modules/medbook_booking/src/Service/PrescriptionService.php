<?php

declare(strict_types=1);

namespace MedBook\Booking\Service;

use Doctrine\DBAL\Connection;
use TCPDF;

final class PrescriptionService
{
    private const CIPHER_METHOD = 'aes-256-cbc';

    public function __construct(
        private readonly Connection $connection,
        private readonly string $dbPrefix,
        private readonly string $encryptionKey = '',
    ) {
    }

    /**
     * Encrypt a PESEL value for secure storage.
     */
    private function encryptPesel(string $pesel): string
    {
        if ($pesel === '' || $this->getEncryptionKey() === '') {
            return $pesel;
        }

        $iv = random_bytes((int) openssl_cipher_iv_length(self::CIPHER_METHOD));
        $encrypted = openssl_encrypt($pesel, self::CIPHER_METHOD, $this->getEncryptionKey(), OPENSSL_RAW_DATA, $iv);

        if ($encrypted === false) {
            return $pesel;
        }

        return base64_encode($iv . $encrypted);
    }

    /**
     * Decrypt a PESEL value from storage.
     */
    private function decryptPesel(string $encryptedPesel): string
    {
        if ($encryptedPesel === '' || $this->getEncryptionKey() === '') {
            return $encryptedPesel;
        }

        $data = base64_decode($encryptedPesel, true);
        if ($data === false) {
            // Not encrypted (legacy plaintext), return as-is
            return $encryptedPesel;
        }

        $ivLength = (int) openssl_cipher_iv_length(self::CIPHER_METHOD);
        if (strlen($data) <= $ivLength) {
            // Too short to be encrypted data, return as-is
            return $encryptedPesel;
        }

        $iv = substr($data, 0, $ivLength);
        $ciphertext = substr($data, $ivLength);

        $decrypted = openssl_decrypt($ciphertext, self::CIPHER_METHOD, $this->getEncryptionKey(), OPENSSL_RAW_DATA, $iv);

        if ($decrypted === false) {
            // Decryption failed (possibly legacy plaintext), return as-is
            return $encryptedPesel;
        }

        return $decrypted;
    }

    /**
     * Get the encryption key, falling back to PS cookie key if none configured.
     */
    private function getEncryptionKey(): string
    {
        if ($this->encryptionKey !== '') {
            return $this->encryptionKey;
        }

        // Fall back to _COOKIE_KEY_ which is always available in PrestaShop
        if (defined('_COOKIE_KEY_')) {
            return (string) _COOKIE_KEY_;
        }

        return '';
    }

    /**
     * Generate a Polish e-prescription PDF.
     *
     * @param array{
     *     patient_name: string,
     *     pesel: string,
     *     doctor_name: string,
     *     pwz_number: string,
     *     prescription_date: string,
     *     medications: array<int, array{name: string, dosage: string, quantity: string, refunded: bool}>,
     *     notes: string,
     *     diagnosis_code: string,
     *     clinic_name: string,
     *     clinic_address: string,
     * } $data
     */
    public function generatePdf(array $data): string
    {
        $pdf = new TCPDF('P', 'mm', 'A5', true, 'UTF-8', false);
        $pdf->SetCreator('MedBook');
        $pdf->SetAuthor($data['doctor_name']);
        $pdf->SetTitle('Recepta');
        $pdf->SetSubject('E-Recepta');

        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(true, 15);
        $pdf->AddPage();

        // Header - clinic stamp area
        $pdf->SetFont('dejavusans', 'B', 12);
        $pdf->Cell(0, 8, 'RECEPTA', 0, 1, 'C');
        $pdf->Ln(2);

        // Clinic info
        $pdf->SetFont('dejavusans', '', 8);
        $pdf->Cell(0, 5, $data['clinic_name'], 0, 1, 'L');
        $pdf->Cell(0, 5, $data['clinic_address'], 0, 1, 'L');
        $pdf->Ln(3);

        // Prescription date
        $pdf->SetFont('dejavusans', '', 9);
        $pdf->Cell(0, 5, 'Data wystawienia: ' . $data['prescription_date'], 0, 1, 'R');
        $pdf->Ln(3);

        // Patient info
        $pdf->SetFont('dejavusans', 'B', 9);
        $pdf->Cell(0, 5, 'Pacjent:', 0, 1, 'L');
        $pdf->SetFont('dejavusans', '', 9);
        $pdf->Cell(0, 5, $data['patient_name'], 0, 1, 'L');
        $pdf->Cell(0, 5, 'PESEL: ' . $data['pesel'], 0, 1, 'L');
        $pdf->Ln(3);

        // Diagnosis code
        if (!empty($data['diagnosis_code'])) {
            $pdf->SetFont('dejavusans', '', 9);
            $pdf->Cell(0, 5, 'Rozpoznanie (ICD-10): ' . $data['diagnosis_code'], 0, 1, 'L');
            $pdf->Ln(2);
        }

        // Medications
        $pdf->SetFont('dejavusans', 'B', 9);
        $pdf->Cell(0, 5, 'Rp.', 0, 1, 'L');
        $pdf->Ln(1);

        $pdf->SetFont('dejavusans', '', 9);
        $counter = 1;
        foreach ($data['medications'] as $medication) {
            $refundLabel = $medication['refunded'] ? ' [Refundacja]' : ' [100%]';
            $line = $counter . '. ' . $medication['name'];
            $pdf->Cell(0, 5, $line, 0, 1, 'L');
            $pdf->Cell(0, 5, '   Dawkowanie: ' . $medication['dosage'], 0, 1, 'L');
            $pdf->Cell(0, 5, '   Ilosc: ' . $medication['quantity'] . $refundLabel, 0, 1, 'L');
            $pdf->Ln(2);
            ++$counter;
        }

        // Notes
        if (!empty($data['notes'])) {
            $pdf->Ln(3);
            $pdf->SetFont('dejavusans', '', 8);
            $pdf->Cell(0, 5, 'Uwagi: ' . $data['notes'], 0, 1, 'L');
        }

        // Doctor signature area
        $pdf->Ln(10);
        $pdf->SetFont('dejavusans', '', 9);
        $pdf->Cell(0, 5, '........................................', 0, 1, 'R');
        $pdf->Cell(0, 5, $data['doctor_name'], 0, 1, 'R');
        $pdf->Cell(0, 5, 'Nr PWZ: ' . $data['pwz_number'], 0, 1, 'R');

        return $pdf->Output('recepta.pdf', 'S');
    }

    /**
     * Save prescription data to the database.
     *
     * @param array<string, mixed> $data
     */
    public function savePrescription(int $bookingId, array $data): int
    {
        $pesel = $data['pesel'] ?? '';
        $encryptedPesel = $this->encryptPesel($pesel);

        $this->connection->insert($this->dbPrefix . 'medbook_prescription', [
            'id_booking' => $bookingId,
            'patient_name' => $data['patient_name'],
            'pesel' => $encryptedPesel,
            'doctor_name' => $data['doctor_name'],
            'pwz_number' => $data['pwz_number'] ?? '',
            'diagnosis_code' => $data['diagnosis_code'] ?? '',
            'medications_json' => json_encode($data['medications'] ?? []),
            'notes' => $data['notes'] ?? '',
            'date_add' => (new \DateTime())->format('Y-m-d H:i:s'),
        ]);

        return (int) $this->connection->lastInsertId();
    }

    /**
     * Get prescriptions for a booking.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getForBooking(int $bookingId): array
    {
        $results = $this->connection->createQueryBuilder()
            ->select('*')
            ->from($this->dbPrefix . 'medbook_prescription')
            ->where('id_booking = :booking_id')
            ->setParameter('booking_id', $bookingId)
            ->orderBy('date_add', 'DESC')
            ->executeQuery()
            ->fetchAllAssociative();

        foreach ($results as &$row) {
            if (isset($row['pesel'])) {
                $row['pesel'] = $this->decryptPesel($row['pesel']);
            }
        }

        return $results;
    }

    /**
     * Get a single prescription by ID.
     *
     * @return array<string, mixed>|null
     */
    public function getById(int $prescriptionId): ?array
    {
        $result = $this->connection->createQueryBuilder()
            ->select('*')
            ->from($this->dbPrefix . 'medbook_prescription')
            ->where('id_prescription = :id')
            ->setParameter('id', $prescriptionId)
            ->executeQuery()
            ->fetchAssociative();

        if (!$result) {
            return null;
        }

        if (isset($result['pesel'])) {
            $result['pesel'] = $this->decryptPesel($result['pesel']);
        }

        return $result;
    }
}
