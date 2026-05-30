<?php

declare(strict_types=1);

namespace MedBook\Booking\Controller\Admin;

use MedBook\Booking\Form\MedicationType;
use MedBook\Booking\Form\PrescriptionType;
use MedBook\Booking\Service\PrescriptionService;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PrescriptionController extends PrestaShopAdminController
{
    public function __construct(
        private readonly PrescriptionService $prescriptionService,
    ) {
    }

    public function createAction(Request $request, int $bookingId): Response
    {
        $form = $this->createForm(PrescriptionType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $data['pesel'] = $data['pesel'] ?? '';
            $this->prescriptionService->savePrescription($bookingId, $data);

            $this->addFlash('success', $this->trans('Recepta zapisana.', 'Modules.Medbookbooking.Admin'));

            return $this->redirectToRoute('admin_medbook_booking_prescription_list', ['bookingId' => $bookingId]);
        }

        return $this->render('@Modules/medbook_booking/views/templates/admin/prescription/create.html.twig', [
            'prescriptionForm' => $form->createView(),
            'bookingId' => $bookingId,
        ]);
    }

    public function generatePdfAction(int $bookingId, int $prescriptionId): Response
    {
        $prescription = $this->prescriptionService->getById($prescriptionId);

        if (!$prescription) {
            $this->addFlash('error', $this->trans('Recepta nie znaleziona.', 'Modules.Medbookbooking.Admin'));

            return $this->redirectToRoute('admin_medbook_booking_prescription_list', ['bookingId' => $bookingId]);
        }

        $medications = json_decode($prescription['medications_json'], true) ?: [];

        $pdfData = [
            'patient_name' => $prescription['patient_name'],
            'pesel' => $prescription['pesel'],
            'doctor_name' => $prescription['doctor_name'],
            'pwz_number' => $prescription['pwz_number'],
            'prescription_date' => (new \DateTime($prescription['date_add']))->format('d.m.Y'),
            'medications' => $medications,
            'notes' => $prescription['notes'],
            'diagnosis_code' => $prescription['diagnosis_code'],
            'clinic_name' => 'MedBook',
            'clinic_address' => '',
        ];

        $pdfContent = $this->prescriptionService->generatePdf($pdfData);

        return new Response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="recepta_' . $prescriptionId . '.pdf"',
        ]);
    }

    public function listAction(int $bookingId): Response
    {
        $prescriptions = $this->prescriptionService->getForBooking($bookingId);

        return $this->render('@Modules/medbook_booking/views/templates/admin/prescription/list.html.twig', [
            'prescriptions' => $prescriptions,
            'bookingId' => $bookingId,
        ]);
    }
}
