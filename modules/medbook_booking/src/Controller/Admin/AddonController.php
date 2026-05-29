<?php

declare(strict_types=1);

namespace MedBook\Booking\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use PrestaShop\PrestaShop\Core\Grid\GridFactoryInterface;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use MedBook\Booking\Entity\Addon;
use MedBook\Booking\Entity\AddonLang;
use MedBook\Booking\Form\AddonType;
use MedBook\Booking\Grid\Definition\Factory\AddonGridDefinitionFactory;
use MedBook\Booking\Grid\Filters\AddonFilters;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AddonController extends PrestaShopAdminController
{
    public function indexAction(
        AddonFilters $filters,
        #[Autowire(service: 'medbook_booking.grid.factory.addons')]
        GridFactoryInterface $addonGridFactory,
    ): Response {
        return $this->render('@Modules/medbook_booking/views/templates/admin/addon/index.html.twig', [
            'addonGrid' => $this->presentGrid($addonGridFactory->getGrid($filters)),
        ]);
    }

    public function searchAction(
        Request $request,
        AddonGridDefinitionFactory $definition,
    ): RedirectResponse {
        return $this->buildSearchResponse(
            $definition,
            $request,
            AddonGridDefinitionFactory::GRID_ID,
            'admin_medbook_booking_addon_index'
        );
    }

    public function createAction(Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AddonType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $addon = new Addon();
            $idResource = (int) ($data['id_resource'] ?? 0);
            $addon->setIdResource($idResource > 0 ? $idResource : null);
            $addon->setPrice((string) ($data['price'] ?? '0.00'));
            $addon->setIsActive((bool) ($data['is_active'] ?? true));
            $addon->setPosition((int) ($data['position'] ?? 0));

            $addonLang = new AddonLang(
                $addon,
                (int) \Configuration::get('PS_LANG_DEFAULT'),
                $data['name'] ?? ''
            );
            $addonLang->setDescription($data['description'] ?? null);
            $addon->addAddonLang($addonLang);

            $entityManager->persist($addon);
            $entityManager->flush();

            $this->addFlash('success', $this->trans('Add-on created successfully.', 'Modules.Medbookbooking.Admin'));

            return $this->redirectToRoute('admin_medbook_booking_addon_index');
        }

        return $this->render('@Modules/medbook_booking/views/templates/admin/addon/form.html.twig', [
            'addonForm' => $form->createView(),
        ]);
    }

    public function editAction(int $addonId, Request $request, EntityManagerInterface $entityManager): Response
    {
        $addon = $entityManager->getRepository(Addon::class)->find($addonId);

        if (!$addon) {
            $this->addFlash('error', $this->trans('Add-on not found.', 'Modules.Medbookbooking.Admin'));

            return $this->redirectToRoute('admin_medbook_booking_addon_index');
        }

        $addonLang = $addon->getAddonLangs()->first() ?: null;

        $form = $this->createForm(AddonType::class, [
            'name' => $addonLang?->getName() ?? '',
            'description' => $addonLang?->getDescription() ?? '',
            'id_resource' => $addon->getIdResource() ?? 0,
            'price' => (float) $addon->getPrice(),
            'is_active' => $addon->isActive(),
            'position' => $addon->getPosition(),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $idResource = (int) ($data['id_resource'] ?? 0);
            $addon->setIdResource($idResource > 0 ? $idResource : null);
            $addon->setPrice((string) ($data['price'] ?? $addon->getPrice()));
            $addon->setIsActive((bool) ($data['is_active'] ?? $addon->isActive()));
            $addon->setPosition((int) ($data['position'] ?? $addon->getPosition()));

            if ($addonLang) {
                $addonLang->setName($data['name'] ?? '');
                $addonLang->setDescription($data['description'] ?? null);
            }

            $entityManager->flush();

            $this->addFlash('success', $this->trans('Add-on updated successfully.', 'Modules.Medbookbooking.Admin'));

            return $this->redirectToRoute('admin_medbook_booking_addon_index');
        }

        return $this->render('@Modules/medbook_booking/views/templates/admin/addon/form.html.twig', [
            'addonForm' => $form->createView(),
        ]);
    }

    public function deleteAction(int $addonId, EntityManagerInterface $entityManager): RedirectResponse
    {
        $addon = $entityManager->getRepository(Addon::class)->find($addonId);

        if ($addon) {
            $entityManager->remove($addon);
            $entityManager->flush();
            $this->addFlash('success', $this->trans('Add-on deleted successfully.', 'Modules.Medbookbooking.Admin'));
        } else {
            $this->addFlash('error', $this->trans('Add-on not found.', 'Modules.Medbookbooking.Admin'));
        }

        return $this->redirectToRoute('admin_medbook_booking_addon_index');
    }

    public function toggleActiveAction(int $addonId, EntityManagerInterface $entityManager): JsonResponse
    {
        $addon = $entityManager->getRepository(Addon::class)->find($addonId);

        if (!$addon) {
            return new JsonResponse(['status' => false, 'message' => 'Add-on not found.']);
        }

        $addon->setIsActive(!$addon->isActive());
        $entityManager->flush();

        return new JsonResponse(['status' => true, 'message' => 'Status updated.']);
    }
}
