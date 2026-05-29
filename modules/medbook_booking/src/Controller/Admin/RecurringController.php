<?php

declare(strict_types=1);

namespace MedBook\Booking\Controller\Admin;

use PrestaShop\PrestaShop\Core\Grid\GridFactoryInterface;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use MedBook\Booking\Grid\Definition\Factory\RecurringGridDefinitionFactory;
use MedBook\Booking\Grid\Filters\RecurringFilters;
use MedBook\Booking\Service\RecurrenceService;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RecurringController extends PrestaShopAdminController
{
    public function indexAction(
        RecurringFilters $filters,
        #[Autowire(service: 'medbook_booking.grid.factory.recurring')]
        GridFactoryInterface $recurringGridFactory,
    ): Response {
        return $this->render('@Modules/medbook_booking/views/templates/admin/recurring/index.html.twig', [
            'recurringGrid' => $this->presentGrid($recurringGridFactory->getGrid($filters)),
        ]);
    }

    public function searchAction(
        Request $request,
        RecurringGridDefinitionFactory $definition,
    ): RedirectResponse {
        return $this->buildSearchResponse(
            $definition,
            $request,
            RecurringGridDefinitionFactory::GRID_ID,
            'admin_medbook_booking_recurring_index'
        );
    }

    public function dismissAction(int $suggestedVisitId, RecurrenceService $recurrenceService): RedirectResponse
    {
        $recurrenceService->dismissSuggestion($suggestedVisitId);

        $this->addFlash('success', $this->trans('Suggested visit dismissed.', 'Modules.Medbookbooking.Admin'));

        return $this->redirectToRoute('admin_medbook_booking_recurring_index');
    }
}
