<?php

declare(strict_types=1);

namespace MedBook\Booking\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use MedBook\Booking\Entity\Clinic;
use MedBook\Booking\Form\ClinicType;
use MedBook\Booking\Grid\Definition\Factory\ClinicGridDefinitionFactory;
use MedBook\Booking\Grid\Filters\ClinicFilters;
use PrestaShop\PrestaShop\Core\Grid\GridFactoryInterface;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ClinicController extends PrestaShopAdminController
{
    public function indexAction(
        ClinicFilters $filters,
        #[Autowire(service: 'medbook_booking.grid.factory.clinics')]
        GridFactoryInterface $gridFactory,
    ): Response {
        return $this->render('@Modules/medbook_booking/views/templates/admin/clinic/index.html.twig', [
            'clinicGrid' => $this->presentGrid($gridFactory->getGrid($filters)),
        ]);
    }

    public function searchAction(
        Request $request,
        ClinicGridDefinitionFactory $definition,
    ): RedirectResponse {
        return $this->buildSearchResponse(
            $definition,
            $request,
            ClinicGridDefinitionFactory::GRID_ID,
            'admin_medbook_booking_clinic_index'
        );
    }

    public function createAction(Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ClinicType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $clinic = new Clinic();
            $clinic->setName($data['name'] ?? '');
            $clinic->setAddress($data['address'] ?? '');
            $clinic->setCity($data['city'] ?? '');
            $clinic->setPostalCode($data['postal_code'] ?? '');
            $clinic->setVoivodeship($data['voivodeship'] ?? null);
            $clinic->setPhone($data['phone'] ?? null);
            $clinic->setEmail($data['email'] ?? null);
            $clinic->setOpeningHoursJson($data['opening_hours_json'] ?? null);
            $clinic->setIsActive((bool) ($data['is_active'] ?? true));

            $entityManager->persist($clinic);
            $entityManager->flush();

            $this->addFlash('success', $this->trans('Przychodnia utworzona.', 'Modules.Medbookbooking.Admin'));

            return $this->redirectToRoute('admin_medbook_booking_clinic_index');
        }

        return $this->render('@Modules/medbook_booking/views/templates/admin/clinic/create.html.twig', [
            'clinicForm' => $form->createView(),
        ]);
    }

    public function editAction(int $clinicId, Request $request, EntityManagerInterface $entityManager): Response
    {
        $clinic = $entityManager->getRepository(Clinic::class)->find($clinicId);

        if (!$clinic) {
            $this->addFlash('error', $this->trans('Przychodnia nie znaleziona.', 'Modules.Medbookbooking.Admin'));

            return $this->redirectToRoute('admin_medbook_booking_clinic_index');
        }

        $form = $this->createForm(ClinicType::class, [
            'name' => $clinic->getName(),
            'address' => $clinic->getAddress(),
            'city' => $clinic->getCity(),
            'postal_code' => $clinic->getPostalCode(),
            'voivodeship' => $clinic->getVoivodeship(),
            'phone' => $clinic->getPhone(),
            'email' => $clinic->getEmail(),
            'opening_hours_json' => $clinic->getOpeningHoursJson(),
            'is_active' => $clinic->isActive(),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $clinic->setName($data['name'] ?? $clinic->getName());
            $clinic->setAddress($data['address'] ?? $clinic->getAddress());
            $clinic->setCity($data['city'] ?? $clinic->getCity());
            $clinic->setPostalCode($data['postal_code'] ?? $clinic->getPostalCode());
            $clinic->setVoivodeship($data['voivodeship'] ?? null);
            $clinic->setPhone($data['phone'] ?? null);
            $clinic->setEmail($data['email'] ?? null);
            $clinic->setOpeningHoursJson($data['opening_hours_json'] ?? null);
            $clinic->setIsActive((bool) ($data['is_active'] ?? true));
            $clinic->setDateUpd(new \DateTime());

            $entityManager->flush();

            $this->addFlash('success', $this->trans('Przychodnia zaktualizowana.', 'Modules.Medbookbooking.Admin'));

            return $this->redirectToRoute('admin_medbook_booking_clinic_index');
        }

        return $this->render('@Modules/medbook_booking/views/templates/admin/clinic/edit.html.twig', [
            'clinicForm' => $form->createView(),
        ]);
    }

    public function deleteAction(int $clinicId, EntityManagerInterface $entityManager): RedirectResponse
    {
        $clinic = $entityManager->getRepository(Clinic::class)->find($clinicId);

        if ($clinic) {
            $entityManager->remove($clinic);
            $entityManager->flush();
            $this->addFlash('success', $this->trans('Przychodnia usunieta.', 'Modules.Medbookbooking.Admin'));
        } else {
            $this->addFlash('error', $this->trans('Przychodnia nie znaleziona.', 'Modules.Medbookbooking.Admin'));
        }

        return $this->redirectToRoute('admin_medbook_booking_clinic_index');
    }

    public function toggleActiveAction(int $clinicId, EntityManagerInterface $entityManager): JsonResponse
    {
        $clinic = $entityManager->getRepository(Clinic::class)->find($clinicId);

        if (!$clinic) {
            return new JsonResponse(['status' => false, 'message' => 'Przychodnia nie znaleziona.']);
        }

        $clinic->setIsActive(!$clinic->isActive());
        $clinic->setDateUpd(new \DateTime());
        $entityManager->flush();

        return new JsonResponse(['status' => true, 'message' => 'Status zaktualizowany.']);
    }
}
