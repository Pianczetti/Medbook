<?php

declare(strict_types=1);

namespace MedBook\Booking\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use PrestaShop\PrestaShop\Core\Grid\GridFactoryInterface;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use MedBook\Booking\Entity\BlockedDate;
use MedBook\Booking\Form\BlockedDateType;
use MedBook\Booking\Grid\Definition\Factory\BlockedDateGridDefinitionFactory;
use MedBook\Booking\Grid\Filters\BlockedDateFilters;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BlockedDateController extends PrestaShopAdminController
{
    public function indexAction(
        BlockedDateFilters $filters,
        #[Autowire(service: 'medbook_booking.grid.factory.blocked_dates')]
        GridFactoryInterface $blockedDateGridFactory,
    ): Response {
        return $this->render('@Modules/medbook_booking/views/templates/admin/blocked_date/index.html.twig', [
            'blockedDateGrid' => $this->presentGrid($blockedDateGridFactory->getGrid($filters)),
        ]);
    }

    public function searchAction(
        Request $request,
        BlockedDateGridDefinitionFactory $definition,
    ): RedirectResponse {
        return $this->buildSearchResponse(
            $definition,
            $request,
            BlockedDateGridDefinitionFactory::GRID_ID,
            'admin_medbook_booking_blocked_date_index'
        );
    }

    public function createAction(Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(BlockedDateType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $blockedDate = new BlockedDate();
            $blockedDate->setIdResource(!empty($data['id_resource']) ? (int) $data['id_resource'] : null);
            $blockedDate->setBlockedDate($data['blocked_date'] ?? new \DateTime());
            $blockedDate->setReason($data['reason'] ?? null);

            $entityManager->persist($blockedDate);
            $entityManager->flush();

            $this->addFlash('success', $this->trans('Blocked date created successfully.', 'Modules.Medbookbooking.Admin'));

            return $this->redirectToRoute('admin_medbook_booking_blocked_date_index');
        }

        return $this->render('@Modules/medbook_booking/views/templates/admin/blocked_date/form.html.twig', [
            'blockedDateForm' => $form->createView(),
        ]);
    }

    public function deleteAction(int $blockedDateId, EntityManagerInterface $entityManager): RedirectResponse
    {
        $blockedDate = $entityManager->getRepository(BlockedDate::class)->find($blockedDateId);

        if ($blockedDate) {
            $entityManager->remove($blockedDate);
            $entityManager->flush();
            $this->addFlash('success', $this->trans('Blocked date deleted successfully.', 'Modules.Medbookbooking.Admin'));
        } else {
            $this->addFlash('error', $this->trans('Blocked date not found.', 'Modules.Medbookbooking.Admin'));
        }

        return $this->redirectToRoute('admin_medbook_booking_blocked_date_index');
    }
}
