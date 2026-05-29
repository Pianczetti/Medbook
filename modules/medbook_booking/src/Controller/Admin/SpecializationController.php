<?php

declare(strict_types=1);

namespace MedBook\Booking\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use MedBook\Booking\Entity\Specialization;
use MedBook\Booking\Form\SpecializationType;
use MedBook\Booking\Grid\Definition\Factory\SpecializationGridDefinitionFactory;
use MedBook\Booking\Grid\Filters\SpecializationFilters;
use PrestaShop\PrestaShop\Core\Grid\GridFactoryInterface;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SpecializationController extends PrestaShopAdminController
{
    public function indexAction(
        SpecializationFilters $filters,
        #[Autowire(service: 'medbook_booking.grid.factory.specializations')]
        GridFactoryInterface $gridFactory,
    ): Response {
        return $this->render('@Modules/medbook_booking/views/templates/admin/specialization/index.html.twig', [
            'specializationGrid' => $this->presentGrid($gridFactory->getGrid($filters)),
        ]);
    }

    public function searchAction(
        Request $request,
        SpecializationGridDefinitionFactory $definition,
    ): RedirectResponse {
        return $this->buildSearchResponse(
            $definition,
            $request,
            SpecializationGridDefinitionFactory::GRID_ID,
            'admin_medbook_booking_specialization_index'
        );
    }

    public function createAction(Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SpecializationType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $specialization = new Specialization();
            $specialization->setName($data['name'] ?? '');
            $specialization->setSlug($data['slug'] ?? '');
            $specialization->setIcon($data['icon'] ?? null);
            $specialization->setPosition((int) ($data['position'] ?? 0));
            $specialization->setIsActive((bool) ($data['is_active'] ?? true));

            $entityManager->persist($specialization);
            $entityManager->flush();

            $this->addFlash('success', $this->trans('Specjalizacja utworzona.', 'Modules.Medbookbooking.Admin'));

            return $this->redirectToRoute('admin_medbook_booking_specialization_index');
        }

        return $this->render('@Modules/medbook_booking/views/templates/admin/specialization/create.html.twig', [
            'specializationForm' => $form->createView(),
        ]);
    }

    public function editAction(int $specializationId, Request $request, EntityManagerInterface $entityManager): Response
    {
        $specialization = $entityManager->getRepository(Specialization::class)->find($specializationId);

        if (!$specialization) {
            $this->addFlash('error', $this->trans('Specjalizacja nie znaleziona.', 'Modules.Medbookbooking.Admin'));

            return $this->redirectToRoute('admin_medbook_booking_specialization_index');
        }

        $form = $this->createForm(SpecializationType::class, [
            'name' => $specialization->getName(),
            'slug' => $specialization->getSlug(),
            'icon' => $specialization->getIcon(),
            'position' => $specialization->getPosition(),
            'is_active' => $specialization->isActive(),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $specialization->setName($data['name'] ?? $specialization->getName());
            $specialization->setSlug($data['slug'] ?? $specialization->getSlug());
            $specialization->setIcon($data['icon'] ?? null);
            $specialization->setPosition((int) ($data['position'] ?? 0));
            $specialization->setIsActive((bool) ($data['is_active'] ?? true));

            $entityManager->flush();

            $this->addFlash('success', $this->trans('Specjalizacja zaktualizowana.', 'Modules.Medbookbooking.Admin'));

            return $this->redirectToRoute('admin_medbook_booking_specialization_index');
        }

        return $this->render('@Modules/medbook_booking/views/templates/admin/specialization/edit.html.twig', [
            'specializationForm' => $form->createView(),
        ]);
    }

    public function deleteAction(int $specializationId, EntityManagerInterface $entityManager): RedirectResponse
    {
        $specialization = $entityManager->getRepository(Specialization::class)->find($specializationId);

        if ($specialization) {
            $entityManager->remove($specialization);
            $entityManager->flush();
            $this->addFlash('success', $this->trans('Specjalizacja usunieta.', 'Modules.Medbookbooking.Admin'));
        } else {
            $this->addFlash('error', $this->trans('Specjalizacja nie znaleziona.', 'Modules.Medbookbooking.Admin'));
        }

        return $this->redirectToRoute('admin_medbook_booking_specialization_index');
    }

    public function toggleActiveAction(int $specializationId, EntityManagerInterface $entityManager): JsonResponse
    {
        $specialization = $entityManager->getRepository(Specialization::class)->find($specializationId);

        if (!$specialization) {
            return new JsonResponse(['status' => false, 'message' => 'Specjalizacja nie znaleziona.']);
        }

        $specialization->setIsActive(!$specialization->isActive());
        $entityManager->flush();

        return new JsonResponse(['status' => true, 'message' => 'Status zaktualizowany.']);
    }
}
