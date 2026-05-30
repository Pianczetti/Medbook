<?php

declare(strict_types=1);

namespace MedBook\Booking\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use PrestaShop\PrestaShop\Core\Grid\GridFactoryInterface;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use MedBook\Booking\Entity\Resource;
use MedBook\Booking\Entity\ResourceLang;
use MedBook\Booking\Form\ResourceType;
use MedBook\Booking\Grid\Definition\Factory\ResourceGridDefinitionFactory;
use MedBook\Booking\Grid\Filters\ResourceFilters;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ResourceController extends PrestaShopAdminController
{
    public function indexAction(
        ResourceFilters $filters,
        #[Autowire(service: 'medbook_booking.grid.factory.resources')]
        GridFactoryInterface $resourceGridFactory,
    ): Response {
        return $this->render('@Modules/medbook_booking/views/templates/admin/resource/index.html.twig', [
            'resourceGrid' => $this->presentGrid($resourceGridFactory->getGrid($filters)),
        ]);
    }

    public function searchAction(
        Request $request,
        ResourceGridDefinitionFactory $definition,
    ): RedirectResponse {
        return $this->buildSearchResponse(
            $definition,
            $request,
            ResourceGridDefinitionFactory::GRID_ID,
            'admin_medbook_booking_resource_index'
        );
    }

    public function createAction(Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ResourceType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $resource = new Resource();
            $resource->setResourceType($data['resource_type'] ?? 'service');
            $resource->setCapacity((int) ($data['capacity'] ?? 1));
            $resource->setDurationMinutes((int) ($data['duration_minutes'] ?? 30));
            $resource->setBasePrice((string) ($data['base_price'] ?? '0.00'));
            $resource->setMinDurationMinutes((int) ($data['min_duration_minutes'] ?? 0));
            $resource->setMaxDurationMinutes((int) ($data['max_duration_minutes'] ?? 0));
            $resource->setBufferMinutes((int) ($data['buffer_minutes'] ?? 0));
            $resource->setColor($data['color'] ?? '#3498db');
            $resource->setIsActive((bool) ($data['is_active'] ?? true));
            $resource->setIdProduct(isset($data['id_product']) && $data['id_product'] !== '' ? (int) $data['id_product'] : null);

            $resourceLang = new ResourceLang(
                $resource,
                (int) \Configuration::get('PS_LANG_DEFAULT'),
                $data['name'] ?? ''
            );
            $resource->addResourceLang($resourceLang);

            $entityManager->persist($resource);
            $entityManager->flush();

            $this->addFlash('success', $this->trans('Resource created successfully.', 'Modules.Medbookbooking.Admin'));

            return $this->redirectToRoute('admin_medbook_booking_resource_index');
        }

        return $this->render('@Modules/medbook_booking/views/templates/admin/resource/form.html.twig', [
            'resourceForm' => $form->createView(),
        ]);
    }

    public function editAction(int $resourceId, Request $request, EntityManagerInterface $entityManager): Response
    {
        $resource = $entityManager->getRepository(Resource::class)->find($resourceId);

        if (!$resource) {
            $this->addFlash('error', $this->trans('Resource not found.', 'Modules.Medbookbooking.Admin'));

            return $this->redirectToRoute('admin_medbook_booking_resource_index');
        }

        $resourceLang = $resource->getResourceLangs()->first() ?: null;

        $form = $this->createForm(ResourceType::class, [
            'name' => $resourceLang?->getName() ?? '',
            'resource_type' => $resource->getResourceType(),
            'capacity' => $resource->getCapacity(),
            'duration_minutes' => $resource->getDurationMinutes(),
            'base_price' => (float) $resource->getBasePrice(),
            'min_duration_minutes' => $resource->getMinDurationMinutes(),
            'max_duration_minutes' => $resource->getMaxDurationMinutes(),
            'buffer_minutes' => $resource->getBufferMinutes(),
            'color' => $resource->getColor(),
            'is_active' => $resource->isActive(),
            'id_product' => $resource->getIdProduct(),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $resource->setResourceType($data['resource_type'] ?? $resource->getResourceType());
            $resource->setCapacity((int) ($data['capacity'] ?? $resource->getCapacity()));
            $resource->setDurationMinutes((int) ($data['duration_minutes'] ?? $resource->getDurationMinutes()));
            $resource->setBasePrice((string) ($data['base_price'] ?? $resource->getBasePrice()));
            $resource->setMinDurationMinutes((int) ($data['min_duration_minutes'] ?? $resource->getMinDurationMinutes()));
            $resource->setMaxDurationMinutes((int) ($data['max_duration_minutes'] ?? $resource->getMaxDurationMinutes()));
            $resource->setBufferMinutes((int) ($data['buffer_minutes'] ?? $resource->getBufferMinutes()));
            $resource->setColor($data['color'] ?? $resource->getColor());
            $resource->setIsActive((bool) ($data['is_active'] ?? $resource->isActive()));
            $resource->setIdProduct(isset($data['id_product']) && $data['id_product'] !== '' ? (int) $data['id_product'] : null);
            $resource->setDateUpd(new \DateTime());

            if ($resourceLang) {
                $resourceLang->setName($data['name'] ?? '');
            }

            $entityManager->flush();

            $this->addFlash('success', $this->trans('Resource updated successfully.', 'Modules.Medbookbooking.Admin'));

            return $this->redirectToRoute('admin_medbook_booking_resource_index');
        }

        return $this->render('@Modules/medbook_booking/views/templates/admin/resource/form.html.twig', [
            'resourceForm' => $form->createView(),
        ]);
    }

    public function deleteAction(int $resourceId, EntityManagerInterface $entityManager): RedirectResponse
    {
        $resource = $entityManager->getRepository(Resource::class)->find($resourceId);

        if ($resource) {
            $entityManager->remove($resource);
            $entityManager->flush();
            $this->addFlash('success', $this->trans('Resource deleted successfully.', 'Modules.Medbookbooking.Admin'));
        } else {
            $this->addFlash('error', $this->trans('Resource not found.', 'Modules.Medbookbooking.Admin'));
        }

        return $this->redirectToRoute('admin_medbook_booking_resource_index');
    }

    public function toggleActiveAction(int $resourceId, EntityManagerInterface $entityManager): JsonResponse
    {
        $resource = $entityManager->getRepository(Resource::class)->find($resourceId);

        if (!$resource) {
            return new JsonResponse(['status' => false, 'message' => 'Resource not found.']);
        }

        $resource->setIsActive(!$resource->isActive());
        $resource->setDateUpd(new \DateTime());
        $entityManager->flush();

        return new JsonResponse(['status' => true, 'message' => 'Status updated.']);
    }
}
