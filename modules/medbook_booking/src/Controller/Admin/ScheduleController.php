<?php

declare(strict_types=1);

namespace MedBook\Booking\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use PrestaShop\PrestaShop\Core\Grid\GridFactoryInterface;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use MedBook\Booking\Entity\Schedule;
use MedBook\Booking\Form\ScheduleType;
use MedBook\Booking\Grid\Definition\Factory\ScheduleGridDefinitionFactory;
use MedBook\Booking\Grid\Filters\ScheduleFilters;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ScheduleController extends PrestaShopAdminController
{
    public function indexAction(
        ScheduleFilters $filters,
        #[Autowire(service: 'medbook_booking.grid.factory.schedules')]
        GridFactoryInterface $scheduleGridFactory,
    ): Response {
        return $this->render('@Modules/medbook_booking/views/templates/admin/schedule/index.html.twig', [
            'scheduleGrid' => $this->presentGrid($scheduleGridFactory->getGrid($filters)),
        ]);
    }

    public function searchAction(
        Request $request,
        ScheduleGridDefinitionFactory $definition,
    ): RedirectResponse {
        return $this->buildSearchResponse(
            $definition,
            $request,
            ScheduleGridDefinitionFactory::GRID_ID,
            'admin_medbook_booking_schedule_index'
        );
    }

    public function createAction(Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ScheduleType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $schedule = new Schedule();
            $schedule->setIdResource((int) ($data['id_resource'] ?? 0));
            $schedule->setDayOfWeek(isset($data['day_of_week']) && '' !== $data['day_of_week'] ? (int) $data['day_of_week'] : null);
            $schedule->setSpecificDate($data['specific_date'] ?? null);
            $schedule->setTimeStart($data['time_start'] ?? '09:00');
            $schedule->setTimeEnd($data['time_end'] ?? '17:00');
            $schedule->setIsAvailable((bool) ($data['is_available'] ?? true));

            $entityManager->persist($schedule);
            $entityManager->flush();

            $this->addFlash('success', $this->trans('Schedule created successfully.', 'Modules.Medbookbooking.Admin'));

            return $this->redirectToRoute('admin_medbook_booking_schedule_index');
        }

        return $this->render('@Modules/medbook_booking/views/templates/admin/schedule/form.html.twig', [
            'scheduleForm' => $form->createView(),
        ]);
    }

    public function editAction(int $scheduleId, Request $request, EntityManagerInterface $entityManager): Response
    {
        $schedule = $entityManager->getRepository(Schedule::class)->find($scheduleId);

        if (!$schedule) {
            $this->addFlash('error', $this->trans('Schedule not found.', 'Modules.Medbookbooking.Admin'));

            return $this->redirectToRoute('admin_medbook_booking_schedule_index');
        }

        $form = $this->createForm(ScheduleType::class, [
            'id_resource' => $schedule->getIdResource(),
            'day_of_week' => $schedule->getDayOfWeek(),
            'specific_date' => $schedule->getSpecificDate(),
            'time_start' => $schedule->getTimeStart(),
            'time_end' => $schedule->getTimeEnd(),
            'is_available' => $schedule->isAvailable(),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $schedule->setIdResource((int) ($data['id_resource'] ?? $schedule->getIdResource()));
            $schedule->setDayOfWeek(isset($data['day_of_week']) && '' !== $data['day_of_week'] ? (int) $data['day_of_week'] : null);
            $schedule->setSpecificDate($data['specific_date'] ?? null);
            $schedule->setTimeStart($data['time_start'] ?? $schedule->getTimeStart());
            $schedule->setTimeEnd($data['time_end'] ?? $schedule->getTimeEnd());
            $schedule->setIsAvailable((bool) ($data['is_available'] ?? $schedule->isAvailable()));

            $entityManager->flush();

            $this->addFlash('success', $this->trans('Schedule updated successfully.', 'Modules.Medbookbooking.Admin'));

            return $this->redirectToRoute('admin_medbook_booking_schedule_index');
        }

        return $this->render('@Modules/medbook_booking/views/templates/admin/schedule/form.html.twig', [
            'scheduleForm' => $form->createView(),
        ]);
    }

    public function deleteAction(int $scheduleId, EntityManagerInterface $entityManager): RedirectResponse
    {
        $schedule = $entityManager->getRepository(Schedule::class)->find($scheduleId);

        if ($schedule) {
            $entityManager->remove($schedule);
            $entityManager->flush();
            $this->addFlash('success', $this->trans('Schedule deleted successfully.', 'Modules.Medbookbooking.Admin'));
        } else {
            $this->addFlash('error', $this->trans('Schedule not found.', 'Modules.Medbookbooking.Admin'));
        }

        return $this->redirectToRoute('admin_medbook_booking_schedule_index');
    }
}
