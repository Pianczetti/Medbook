<?php

declare(strict_types=1);

namespace MedBook\Booking\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use PrestaShop\PrestaShop\Core\Grid\GridFactoryInterface;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use MedBook\Booking\Entity\Booking;
use MedBook\Booking\Form\BookingType;
use MedBook\Booking\Grid\Definition\Factory\BookingGridDefinitionFactory;
use MedBook\Booking\Grid\Filters\BookingFilters;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BookingController extends PrestaShopAdminController
{
    public function indexAction(
        BookingFilters $filters,
        #[Autowire(service: 'medbook_booking.grid.factory.bookings')]
        GridFactoryInterface $bookingGridFactory,
    ): Response {
        return $this->render('@Modules/medbook_booking/views/templates/admin/booking/index.html.twig', [
            'bookingGrid' => $this->presentGrid($bookingGridFactory->getGrid($filters)),
        ]);
    }

    public function searchAction(
        Request $request,
        BookingGridDefinitionFactory $definition,
    ): RedirectResponse {
        return $this->buildSearchResponse(
            $definition,
            $request,
            BookingGridDefinitionFactory::GRID_ID,
            'admin_medbook_booking_booking_index'
        );
    }

    public function createAction(Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(BookingType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $booking = new Booking();
            $booking->setIdResource((int) ($data['id_resource'] ?? 0));
            $booking->setBookingDate($data['booking_date'] ?? new \DateTime());
            $booking->setTimeStart($data['time_start'] ?? '09:00');
            $booking->setTimeEnd($data['time_end'] ?? '09:30');
            $booking->setStatus($data['status'] ?? 'pending');
            $booking->setCustomerName($data['customer_name'] ?? '');
            $booking->setCustomerEmail($data['customer_email'] ?? '');
            $booking->setCustomerPhone($data['customer_phone'] ?? null);
            $booking->setNotes($data['notes'] ?? null);
            $booking->setNoShow(!empty($data['no_show']));
            $booking->setAdminNotes($data['admin_notes'] ?? null);
            $booking->setReferenceCode('BK' . strtoupper(bin2hex(random_bytes(5))));

            $entityManager->persist($booking);
            $entityManager->flush();

            $this->addFlash('success', $this->trans('Booking created successfully.', 'Modules.Medbookbooking.Admin'));

            return $this->redirectToRoute('admin_medbook_booking_booking_index');
        }

        return $this->render('@Modules/medbook_booking/views/templates/admin/booking/form.html.twig', [
            'bookingForm' => $form->createView(),
        ]);
    }

    public function editAction(int $bookingId, Request $request, EntityManagerInterface $entityManager): Response
    {
        $booking = $entityManager->getRepository(Booking::class)->find($bookingId);

        if (!$booking) {
            $this->addFlash('error', $this->trans('Booking not found.', 'Modules.Medbookbooking.Admin'));

            return $this->redirectToRoute('admin_medbook_booking_booking_index');
        }

        $form = $this->createForm(BookingType::class, [
            'id_resource' => $booking->getIdResource(),
            'booking_date' => $booking->getBookingDate(),
            'time_start' => $booking->getTimeStart(),
            'time_end' => $booking->getTimeEnd(),
            'status' => $booking->getStatus(),
            'customer_name' => $booking->getCustomerName(),
            'customer_email' => $booking->getCustomerEmail(),
            'customer_phone' => $booking->getCustomerPhone(),
            'notes' => $booking->getNotes(),
            'no_show' => $booking->isNoShow(),
            'admin_notes' => $booking->getAdminNotes(),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $booking->setIdResource((int) ($data['id_resource'] ?? $booking->getIdResource()));
            $booking->setBookingDate($data['booking_date'] ?? $booking->getBookingDate());
            $booking->setTimeStart($data['time_start'] ?? $booking->getTimeStart());
            $booking->setTimeEnd($data['time_end'] ?? $booking->getTimeEnd());
            $booking->setStatus($data['status'] ?? $booking->getStatus());
            $booking->setCustomerName($data['customer_name'] ?? $booking->getCustomerName());
            $booking->setCustomerEmail($data['customer_email'] ?? $booking->getCustomerEmail());
            $booking->setCustomerPhone($data['customer_phone'] ?? null);
            $booking->setNotes($data['notes'] ?? null);
            $booking->setNoShow(!empty($data['no_show']));
            $booking->setAdminNotes($data['admin_notes'] ?? null);
            $booking->setDateUpd(new \DateTime());

            $entityManager->flush();

            $this->addFlash('success', $this->trans('Booking updated successfully.', 'Modules.Medbookbooking.Admin'));

            return $this->redirectToRoute('admin_medbook_booking_booking_index');
        }

        return $this->render('@Modules/medbook_booking/views/templates/admin/booking/form.html.twig', [
            'bookingForm' => $form->createView(),
        ]);
    }

    public function deleteAction(int $bookingId, EntityManagerInterface $entityManager): RedirectResponse
    {
        $booking = $entityManager->getRepository(Booking::class)->find($bookingId);

        if ($booking) {
            $entityManager->remove($booking);
            $entityManager->flush();
            $this->addFlash('success', $this->trans('Booking deleted successfully.', 'Modules.Medbookbooking.Admin'));
        } else {
            $this->addFlash('error', $this->trans('Booking not found.', 'Modules.Medbookbooking.Admin'));
        }

        return $this->redirectToRoute('admin_medbook_booking_booking_index');
    }

    public function bulkDeleteAction(Request $request, EntityManagerInterface $entityManager): RedirectResponse
    {
        $bookingIds = $request->request->all('medbook_booking_bulk');

        if (!empty($bookingIds)) {
            $repository = $entityManager->getRepository(Booking::class);

            foreach ($bookingIds as $bookingId) {
                $booking = $repository->find((int) $bookingId);
                if ($booking) {
                    $entityManager->remove($booking);
                }
            }

            $entityManager->flush();
            $this->addFlash('success', $this->trans('Selected bookings deleted successfully.', 'Modules.Medbookbooking.Admin'));
        }

        return $this->redirectToRoute('admin_medbook_booking_booking_index');
    }

    public function updateStatusAction(int $bookingId, Request $request, EntityManagerInterface $entityManager, HookDispatcherInterface $hookDispatcher): JsonResponse
    {
        $booking = $entityManager->getRepository(Booking::class)->find($bookingId);

        if (!$booking) {
            return new JsonResponse(['status' => false, 'message' => 'Booking not found.']);
        }

        $newStatus = $request->request->get('status', '');
        $allowed = ['pending', 'confirmed', 'cancelled', 'completed', 'no_show'];

        if (!in_array($newStatus, $allowed, true)) {
            return new JsonResponse(['status' => false, 'message' => 'Invalid status.']);
        }

        $booking->setStatus($newStatus);
        $booking->setDateUpd(new \DateTime());
        $entityManager->flush();

        if ('cancelled' === $newStatus) {
            $hookDispatcher->dispatchWithParameters('actionBookingCancelled', [
                'id_booking' => $bookingId,
                'id_resource' => $booking->getIdResource(),
            ]);
        }

        if ('completed' === $newStatus) {
            $hookDispatcher->dispatchWithParameters('actionBookingCompleted', [
                'id_booking' => $bookingId,
                'id_resource' => $booking->getIdResource(),
            ]);
        }

        return new JsonResponse(['status' => true, 'message' => 'Status updated.']);
    }
}
