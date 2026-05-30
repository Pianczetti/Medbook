<?php

declare(strict_types=1);

namespace MedBook\Booking\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use PrestaShop\PrestaShop\Core\Grid\GridFactoryInterface;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use MedBook\Booking\Entity\RefundRule;
use MedBook\Booking\Form\RefundRuleType;
use MedBook\Booking\Grid\Definition\Factory\RefundRuleGridDefinitionFactory;
use MedBook\Booking\Grid\Filters\RefundRuleFilters;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RefundRuleController extends PrestaShopAdminController
{
    public function indexAction(
        RefundRuleFilters $filters,
        #[Autowire(service: 'medbook_booking.grid.factory.refund_rules')]
        GridFactoryInterface $refundRuleGridFactory,
    ): Response {
        return $this->render('@Modules/medbook_booking/views/templates/admin/refund_rule/index.html.twig', [
            'refundRuleGrid' => $this->presentGrid($refundRuleGridFactory->getGrid($filters)),
        ]);
    }

    public function searchAction(
        Request $request,
        RefundRuleGridDefinitionFactory $definition,
    ): RedirectResponse {
        return $this->buildSearchResponse(
            $definition,
            $request,
            RefundRuleGridDefinitionFactory::GRID_ID,
            'admin_medbook_booking_refund_rule_index'
        );
    }

    public function createAction(Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RefundRuleType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $refundRule = new RefundRule();
            $idResource = (int) ($data['id_resource'] ?? 0);
            $refundRule->setIdResource($idResource > 0 ? $idResource : null);
            $refundRule->setHoursBefore((int) ($data['hours_before'] ?? 0));
            $refundRule->setRefundPercent((string) ($data['refund_percent'] ?? '0.00'));

            $entityManager->persist($refundRule);
            $entityManager->flush();

            $this->addFlash('success', $this->trans('Refund rule created successfully.', 'Modules.Medbookbooking.Admin'));

            return $this->redirectToRoute('admin_medbook_booking_refund_rule_index');
        }

        return $this->render('@Modules/medbook_booking/views/templates/admin/refund_rule/form.html.twig', [
            'refundRuleForm' => $form->createView(),
        ]);
    }

    public function editAction(int $refundRuleId, Request $request, EntityManagerInterface $entityManager): Response
    {
        $refundRule = $entityManager->getRepository(RefundRule::class)->find($refundRuleId);

        if (!$refundRule) {
            $this->addFlash('error', $this->trans('Refund rule not found.', 'Modules.Medbookbooking.Admin'));

            return $this->redirectToRoute('admin_medbook_booking_refund_rule_index');
        }

        $form = $this->createForm(RefundRuleType::class, [
            'id_resource' => $refundRule->getIdResource() ?? 0,
            'hours_before' => $refundRule->getHoursBefore(),
            'refund_percent' => (float) $refundRule->getRefundPercent(),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $idResource = (int) ($data['id_resource'] ?? 0);
            $refundRule->setIdResource($idResource > 0 ? $idResource : null);
            $refundRule->setHoursBefore((int) ($data['hours_before'] ?? $refundRule->getHoursBefore()));
            $refundRule->setRefundPercent((string) ($data['refund_percent'] ?? $refundRule->getRefundPercent()));

            $entityManager->flush();

            $this->addFlash('success', $this->trans('Refund rule updated successfully.', 'Modules.Medbookbooking.Admin'));

            return $this->redirectToRoute('admin_medbook_booking_refund_rule_index');
        }

        return $this->render('@Modules/medbook_booking/views/templates/admin/refund_rule/form.html.twig', [
            'refundRuleForm' => $form->createView(),
        ]);
    }

    public function deleteAction(int $refundRuleId, EntityManagerInterface $entityManager): RedirectResponse
    {
        $refundRule = $entityManager->getRepository(RefundRule::class)->find($refundRuleId);

        if ($refundRule) {
            $entityManager->remove($refundRule);
            $entityManager->flush();
            $this->addFlash('success', $this->trans('Refund rule deleted successfully.', 'Modules.Medbookbooking.Admin'));
        } else {
            $this->addFlash('error', $this->trans('Refund rule not found.', 'Modules.Medbookbooking.Admin'));
        }

        return $this->redirectToRoute('admin_medbook_booking_refund_rule_index');
    }
}
