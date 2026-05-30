<?php

declare(strict_types=1);

namespace MedBook\Booking\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use PrestaShop\PrestaShop\Core\Grid\GridFactoryInterface;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use MedBook\Booking\Entity\DepositRule;
use MedBook\Booking\Form\DepositRuleType;
use MedBook\Booking\Grid\Definition\Factory\DepositRuleGridDefinitionFactory;
use MedBook\Booking\Grid\Filters\DepositRuleFilters;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DepositRuleController extends PrestaShopAdminController
{
    public function indexAction(
        DepositRuleFilters $filters,
        #[Autowire(service: 'medbook_booking.grid.factory.deposit_rules')]
        GridFactoryInterface $depositRuleGridFactory,
    ): Response {
        return $this->render('@Modules/medbook_booking/views/templates/admin/deposit_rule/index.html.twig', [
            'depositRuleGrid' => $this->presentGrid($depositRuleGridFactory->getGrid($filters)),
        ]);
    }

    public function searchAction(
        Request $request,
        DepositRuleGridDefinitionFactory $definition,
    ): RedirectResponse {
        return $this->buildSearchResponse(
            $definition,
            $request,
            DepositRuleGridDefinitionFactory::GRID_ID,
            'admin_medbook_booking_deposit_rule_index'
        );
    }

    public function createAction(Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(DepositRuleType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $depositRule = new DepositRule();
            $idResource = (int) ($data['id_resource'] ?? 0);
            $depositRule->setIdResource($idResource > 0 ? $idResource : null);
            $depositRule->setDepositType($data['deposit_type'] ?? 'percent');
            $depositRule->setDepositValue((string) ($data['deposit_value'] ?? '0.00'));
            $depositRule->setIsActive((bool) ($data['is_active'] ?? true));

            $entityManager->persist($depositRule);
            $entityManager->flush();

            $this->addFlash('success', $this->trans('Deposit rule created successfully.', 'Modules.Medbookbooking.Admin'));

            return $this->redirectToRoute('admin_medbook_booking_deposit_rule_index');
        }

        return $this->render('@Modules/medbook_booking/views/templates/admin/deposit_rule/form.html.twig', [
            'depositRuleForm' => $form->createView(),
        ]);
    }

    public function editAction(int $depositRuleId, Request $request, EntityManagerInterface $entityManager): Response
    {
        $depositRule = $entityManager->getRepository(DepositRule::class)->find($depositRuleId);

        if (!$depositRule) {
            $this->addFlash('error', $this->trans('Deposit rule not found.', 'Modules.Medbookbooking.Admin'));

            return $this->redirectToRoute('admin_medbook_booking_deposit_rule_index');
        }

        $form = $this->createForm(DepositRuleType::class, [
            'id_resource' => $depositRule->getIdResource() ?? 0,
            'deposit_type' => $depositRule->getDepositType(),
            'deposit_value' => (float) $depositRule->getDepositValue(),
            'is_active' => $depositRule->isActive(),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $idResource = (int) ($data['id_resource'] ?? 0);
            $depositRule->setIdResource($idResource > 0 ? $idResource : null);
            $depositRule->setDepositType($data['deposit_type'] ?? $depositRule->getDepositType());
            $depositRule->setDepositValue((string) ($data['deposit_value'] ?? $depositRule->getDepositValue()));
            $depositRule->setIsActive((bool) ($data['is_active'] ?? $depositRule->isActive()));

            $entityManager->flush();

            $this->addFlash('success', $this->trans('Deposit rule updated successfully.', 'Modules.Medbookbooking.Admin'));

            return $this->redirectToRoute('admin_medbook_booking_deposit_rule_index');
        }

        return $this->render('@Modules/medbook_booking/views/templates/admin/deposit_rule/form.html.twig', [
            'depositRuleForm' => $form->createView(),
        ]);
    }

    public function deleteAction(int $depositRuleId, EntityManagerInterface $entityManager): RedirectResponse
    {
        $depositRule = $entityManager->getRepository(DepositRule::class)->find($depositRuleId);

        if ($depositRule) {
            $entityManager->remove($depositRule);
            $entityManager->flush();
            $this->addFlash('success', $this->trans('Deposit rule deleted successfully.', 'Modules.Medbookbooking.Admin'));
        } else {
            $this->addFlash('error', $this->trans('Deposit rule not found.', 'Modules.Medbookbooking.Admin'));
        }

        return $this->redirectToRoute('admin_medbook_booking_deposit_rule_index');
    }

    public function toggleActiveAction(int $depositRuleId, EntityManagerInterface $entityManager): JsonResponse
    {
        $depositRule = $entityManager->getRepository(DepositRule::class)->find($depositRuleId);

        if (!$depositRule) {
            return new JsonResponse(['status' => false, 'message' => 'Deposit rule not found.']);
        }

        $depositRule->setIsActive(!$depositRule->isActive());
        $entityManager->flush();

        return new JsonResponse(['status' => true, 'message' => 'Status updated.']);
    }
}
