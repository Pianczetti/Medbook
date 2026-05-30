<?php

declare(strict_types=1);

namespace MedBook\Booking\Controller\Admin;

use Doctrine\DBAL\Connection;
use PrestaShop\PrestaShop\Core\Grid\GridFactoryInterface;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use MedBook\Booking\Grid\Definition\Factory\WaitlistGridDefinitionFactory;
use MedBook\Booking\Grid\Filters\WaitlistFilters;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class WaitlistController extends PrestaShopAdminController
{
    public function __construct(
        private readonly Connection $connection,
        private readonly string $dbPrefix,
    ) {
    }

    public function indexAction(
        WaitlistFilters $filters,
        #[Autowire(service: 'medbook_booking.grid.factory.waitlist')]
        GridFactoryInterface $waitlistGridFactory,
    ): Response {
        return $this->render('@Modules/medbook_booking/views/templates/admin/waitlist/index.html.twig', [
            'waitlistGrid' => $this->presentGrid($waitlistGridFactory->getGrid($filters)),
        ]);
    }

    public function searchAction(
        Request $request,
        WaitlistGridDefinitionFactory $definition,
    ): RedirectResponse {
        return $this->buildSearchResponse(
            $definition,
            $request,
            WaitlistGridDefinitionFactory::GRID_ID,
            'admin_medbook_booking_waitlist_index'
        );
    }

    public function assignAction(int $waitlistId): RedirectResponse
    {
        $this->connection->update(
            $this->dbPrefix . 'medbook_waitlist',
            ['status' => 'booked'],
            ['id_waitlist' => $waitlistId]
        );

        $this->addFlash('success', $this->trans('Waitlist entry marked as booked.', 'Modules.Medbookbooking.Admin'));

        return $this->redirectToRoute('admin_medbook_booking_waitlist_index');
    }

    public function removeAction(int $waitlistId): RedirectResponse
    {
        $this->connection->update(
            $this->dbPrefix . 'medbook_waitlist',
            ['status' => 'cancelled'],
            ['id_waitlist' => $waitlistId]
        );

        $this->addFlash('success', $this->trans('Waitlist entry cancelled.', 'Modules.Medbookbooking.Admin'));

        return $this->redirectToRoute('admin_medbook_booking_waitlist_index');
    }
}
