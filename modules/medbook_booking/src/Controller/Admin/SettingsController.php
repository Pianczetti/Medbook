<?php

declare(strict_types=1);

namespace MedBook\Booking\Controller\Admin;

use PrestaShop\PrestaShop\Core\Form\FormHandlerInterface;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SettingsController extends PrestaShopAdminController
{
    public function indexAction(
        Request $request,
        #[Autowire(service: 'medbook_booking.form.settings_form_handler')]
        FormHandlerInterface $formHandler,
    ): Response {
        $form = $formHandler->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $errors = $formHandler->save($form->getData());

            if (!empty($errors)) {
                foreach ($errors as $error) {
                    $this->addFlash('error', $this->trans($error['key'], $error['domain'], $error['parameters'] ?? []));
                }
            } else {
                $this->addFlash('success', $this->trans('Settings saved successfully.', 'Modules.Medbookbooking.Admin'));
            }

            return $this->redirectToRoute('admin_medbook_booking_settings');
        }

        return $this->render('@Modules/medbook_booking/views/templates/admin/settings/index.html.twig', [
            'settingsForm' => $form->createView(),
        ]);
    }
}
