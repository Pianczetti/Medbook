<?php

declare(strict_types=1);

namespace MedBook\Booking\Service;

use Doctrine\DBAL\Connection;
use TCPDF;

final class PrescriptionService
{
    public function __construct(
        private readonly Connection $connection,
        private readonly string $dbPrefix,
    ) {
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
        $this->connection->insert($this->dbPrefix . 'medbook_prescription', [
            'id_booking' => $bookingId,
            'patient_name' => $data['patient_name'],
            'pesel' => $data['pesel'] ?? '',
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
        return $this->connection->createQueryBuilder()
            ->select('*')
            ->from($this->dbPrefix . 'medbook_prescription')
            ->where('id_booking = :booking_id')
            ->setParameter('booking_id', $bookingId)
            ->orderBy('date_add', 'DESC')
            ->executeQuery()
            ->fetchAllAssociative();
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

        return $result ?: null;
    }
}
