<?php

declare(strict_types=1);

namespace MedBook\Booking\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use PrestaShop\PrestaShop\Core\Grid\GridFactoryInterface;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use MedBook\Booking\Entity\PriceRule;
use MedBook\Booking\Form\PriceRuleType;
use MedBook\Booking\Grid\Definition\Factory\PriceRuleGridDefinitionFactory;
use MedBook\Booking\Grid\Filters\PriceRuleFilters;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PriceRuleController extends PrestaShopAdminController
{
    public function indexAction(
        PriceRuleFilters $filters,
        #[Autowire(service: 'medbook_booking.grid.factory.price_rules')]
        GridFactoryInterface $priceRuleGridFactory,
    ): Response {
        return $this->render('@Modules/medbook_booking/views/templates/admin/price_rule/index.html.twig', [
            'priceRuleGrid' => $this->presentGrid($priceRuleGridFactory->getGrid($filters)),
        ]);
    }

    public function searchAction(
        Request $request,
        PriceRuleGridDefinitionFactory $definition,
    ): RedirectResponse {
        return $this->buildSearchResponse(
            $definition,
            $request,
            PriceRuleGridDefinitionFactory::GRID_ID,
            'admin_medbook_booking_price_rule_index'
        );
    }

    public function createAction(Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PriceRuleType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $priceRule = new PriceRule();
            $priceRule->setIdResource((int) ($data['id_resource'] ?? 0));
            $priceRule->setName($data['name'] ?? '');
            $priceRule->setDateFrom($data['date_from'] ?? null);
            $priceRule->setDateTo($data['date_to'] ?? null);
            $priceRule->setDayOfWeek(isset($data['day_of_week']) && '' !== $data['day_of_week'] ? (int) $data['day_of_week'] : null);
            $priceRule->setTimeFrom($data['time_from'] ?? null);
            $priceRule->setTimeTo($data['time_to'] ?? null);
            $priceRule->setModifierType($data['modifier_type'] ?? 'percent');
            $priceRule->setModifierValue((string) ($data['modifier_value'] ?? '0.00'));
            $priceRule->setPriority((int) ($data['priority'] ?? 0));
            $priceRule->setIsActive((bool) ($data['is_active'] ?? true));

            $entityManager->persist($priceRule);
            $entityManager->flush();

            $this->addFlash('success', $this->trans('Price rule created successfully.', 'Modules.Medbookbooking.Admin'));

            return $this->redirectToRoute('admin_medbook_booking_price_rule_index');
        }

        return $this->render('@Modules/medbook_booking/views/templates/admin/price_rule/form.html.twig', [
            'priceRuleForm' => $form->createView(),
        ]);
    }

    public function editAction(int $priceRuleId, Request $request, EntityManagerInterface $entityManager): Response
    {
        $priceRule = $entityManager->getRepository(PriceRule::class)->find($priceRuleId);

        if (!$priceRule) {
            $this->addFlash('error', $this->trans('Price rule not found.', 'Modules.Medbookbooking.Admin'));

            return $this->redirectToRoute('admin_medbook_booking_price_rule_index');
        }

        $form = $this->createForm(PriceRuleType::class, [
            'name' => $priceRule->getName(),
            'id_resource' => $priceRule->getIdResource(),
            'date_from' => $priceRule->getDateFrom(),
            'date_to' => $priceRule->getDateTo(),
            'day_of_week' => $priceRule->getDayOfWeek(),
            'time_from' => $priceRule->getTimeFrom(),
            'time_to' => $priceRule->getTimeTo(),
            'modifier_type' => $priceRule->getModifierType(),
            'modifier_value' => (float) $priceRule->getModifierValue(),
            'priority' => $priceRule->getPriority(),
            'is_active' => $priceRule->isActive(),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $priceRule->setIdResource((int) ($data['id_resource'] ?? $priceRule->getIdResource()));
            $priceRule->setName($data['name'] ?? $priceRule->getName());
            $priceRule->setDateFrom($data['date_from'] ?? null);
            $priceRule->setDateTo($data['date_to'] ?? null);
            $priceRule->setDayOfWeek(isset($data['day_of_week']) && '' !== $data['day_of_week'] ? (int) $data['day_of_week'] : null);
            $priceRule->setTimeFrom($data['time_from'] ?? null);
            $priceRule->setTimeTo($data['time_to'] ?? null);
            $priceRule->setModifierType($data['modifier_type'] ?? $priceRule->getModifierType());
            $priceRule->setModifierValue((string) ($data['modifier_value'] ?? $priceRule->getModifierValue()));
            $priceRule->setPriority((int) ($data['priority'] ?? $priceRule->getPriority()));
            $priceRule->setIsActive((bool) ($data['is_active'] ?? $priceRule->isActive()));

            $entityManager->flush();

            $this->addFlash('success', $this->trans('Price rule updated successfully.', 'Modules.Medbookbooking.Admin'));

            return $this->redirectToRoute('admin_medbook_booking_price_rule_index');
        }

        return $this->render('@Modules/medbook_booking/views/templates/admin/price_rule/form.html.twig', [
            'priceRuleForm' => $form->createView(),
        ]);
    }

    public function deleteAction(int $priceRuleId, EntityManagerInterface $entityManager): RedirectResponse
    {
        $priceRule = $entityManager->getRepository(PriceRule::class)->find($priceRuleId);

        if ($priceRule) {
            $entityManager->remove($priceRule);
            $entityManager->flush();
            $this->addFlash('success', $this->trans('Price rule deleted successfully.', 'Modules.Medbookbooking.Admin'));
        } else {
            $this->addFlash('error', $this->trans('Price rule not found.', 'Modules.Medbookbooking.Admin'));
        }

        return $this->redirectToRoute('admin_medbook_booking_price_rule_index');
    }

    public function toggleActiveAction(int $priceRuleId, EntityManagerInterface $entityManager): JsonResponse
    {
        $priceRule = $entityManager->getRepository(PriceRule::class)->find($priceRuleId);

        if (!$priceRule) {
            return new JsonResponse(['status' => false, 'message' => 'Price rule not found.']);
        }

        $priceRule->setIsActive(!$priceRule->isActive());
        $entityManager->flush();

        return new JsonResponse(['status' => true, 'message' => 'Status updated.']);
    }
}
