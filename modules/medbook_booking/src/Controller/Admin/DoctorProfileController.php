<?php

declare(strict_types=1);

namespace MedBook\Booking\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use MedBook\Booking\Entity\DoctorProfile;
use MedBook\Booking\Form\DoctorProfileType;
use MedBook\Booking\Grid\Definition\Factory\DoctorProfileGridDefinitionFactory;
use MedBook\Booking\Grid\Filters\DoctorProfileFilters;
use PrestaShop\PrestaShop\Core\Grid\GridFactoryInterface;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DoctorProfileController extends PrestaShopAdminController
{
    public function indexAction(
        DoctorProfileFilters $filters,
        #[Autowire(service: 'medbook_booking.grid.factory.doctor_profiles')]
        GridFactoryInterface $gridFactory,
    ): Response {
        return $this->render('@Modules/medbook_booking/views/templates/admin/doctor_profile/index.html.twig', [
            'doctorProfileGrid' => $this->presentGrid($gridFactory->getGrid($filters)),
        ]);
    }

    public function searchAction(
        Request $request,
        DoctorProfileGridDefinitionFactory $definition,
    ): RedirectResponse {
        return $this->buildSearchResponse(
            $definition,
            $request,
            DoctorProfileGridDefinitionFactory::GRID_ID,
            'admin_medbook_booking_doctor_profile_index'
        );
    }

    public function createAction(Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(DoctorProfileType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $doctorProfile = new DoctorProfile();
            $doctorProfile->setIdResource((int) ($data['id_resource'] ?? 0));
            $doctorProfile->setIdSpecialization(isset($data['id_specialization']) && $data['id_specialization'] !== '' ? (int) $data['id_specialization'] : null);
            $doctorProfile->setEducation($data['education'] ?? null);
            $doctorProfile->setExperienceYears((int) ($data['experience_years'] ?? 0));
            $doctorProfile->setLanguages($data['languages'] ?? null);
            $doctorProfile->setCertifications($data['certifications'] ?? null);
            $doctorProfile->setPhoto($data['photo'] ?? null);
            $doctorProfile->setNip($data['nip'] ?? null);
            $doctorProfile->setPwzNumber($data['pwz_number'] ?? null);
            $doctorProfile->setConsultationOnline((bool) ($data['consultation_online'] ?? false));
            $doctorProfile->setConsultationInperson((bool) ($data['consultation_inperson'] ?? true));

            $entityManager->persist($doctorProfile);
            $entityManager->flush();

            $this->addFlash('success', $this->trans('Profil lekarza utworzony.', 'Modules.Medbookbooking.Admin'));

            return $this->redirectToRoute('admin_medbook_booking_doctor_profile_index');
        }

        return $this->render('@Modules/medbook_booking/views/templates/admin/doctor_profile/create.html.twig', [
            'doctorProfileForm' => $form->createView(),
        ]);
    }

    public function editAction(int $doctorProfileId, Request $request, EntityManagerInterface $entityManager): Response
    {
        $doctorProfile = $entityManager->getRepository(DoctorProfile::class)->find($doctorProfileId);

        if (!$doctorProfile) {
            $this->addFlash('error', $this->trans('Profil lekarza nie znaleziony.', 'Modules.Medbookbooking.Admin'));

            return $this->redirectToRoute('admin_medbook_booking_doctor_profile_index');
        }

        $form = $this->createForm(DoctorProfileType::class, [
            'id_resource' => $doctorProfile->getIdResource(),
            'id_specialization' => $doctorProfile->getIdSpecialization(),
            'education' => $doctorProfile->getEducation(),
            'experience_years' => $doctorProfile->getExperienceYears(),
            'languages' => $doctorProfile->getLanguages(),
            'certifications' => $doctorProfile->getCertifications(),
            'photo' => $doctorProfile->getPhoto(),
            'nip' => $doctorProfile->getNip(),
            'pwz_number' => $doctorProfile->getPwzNumber(),
            'consultation_online' => $doctorProfile->isConsultationOnline(),
            'consultation_inperson' => $doctorProfile->isConsultationInperson(),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $doctorProfile->setIdResource((int) ($data['id_resource'] ?? $doctorProfile->getIdResource()));
            $doctorProfile->setIdSpecialization(isset($data['id_specialization']) && $data['id_specialization'] !== '' ? (int) $data['id_specialization'] : null);
            $doctorProfile->setEducation($data['education'] ?? null);
            $doctorProfile->setExperienceYears((int) ($data['experience_years'] ?? 0));
            $doctorProfile->setLanguages($data['languages'] ?? null);
            $doctorProfile->setCertifications($data['certifications'] ?? null);
            $doctorProfile->setPhoto($data['photo'] ?? null);
            $doctorProfile->setNip($data['nip'] ?? null);
            $doctorProfile->setPwzNumber($data['pwz_number'] ?? null);
            $doctorProfile->setConsultationOnline((bool) ($data['consultation_online'] ?? false));
            $doctorProfile->setConsultationInperson((bool) ($data['consultation_inperson'] ?? true));
            $doctorProfile->setDateUpd(new \DateTime());

            $entityManager->flush();

            $this->addFlash('success', $this->trans('Profil lekarza zaktualizowany.', 'Modules.Medbookbooking.Admin'));

            return $this->redirectToRoute('admin_medbook_booking_doctor_profile_index');
        }

        return $this->render('@Modules/medbook_booking/views/templates/admin/doctor_profile/edit.html.twig', [
            'doctorProfileForm' => $form->createView(),
        ]);
    }

    public function deleteAction(int $doctorProfileId, EntityManagerInterface $entityManager): RedirectResponse
    {
        $doctorProfile = $entityManager->getRepository(DoctorProfile::class)->find($doctorProfileId);

        if ($doctorProfile) {
            $entityManager->remove($doctorProfile);
            $entityManager->flush();
            $this->addFlash('success', $this->trans('Profil lekarza usuniety.', 'Modules.Medbookbooking.Admin'));
        } else {
            $this->addFlash('error', $this->trans('Profil lekarza nie znaleziony.', 'Modules.Medbookbooking.Admin'));
        }

        return $this->redirectToRoute('admin_medbook_booking_doctor_profile_index');
    }
}
