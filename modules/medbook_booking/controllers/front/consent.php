<?php

declare(strict_types=1);

if (!defined('_PS_VERSION_')) {
    exit;
}

class Medbook_bookingConsentModuleFrontController extends ModuleFrontController
{
    public $ssl = true;
    public $auth = true;

    public function initContent(): void
    {
        parent::initContent();

        $customerId = (int) $this->context->customer->id;

        /** @var \MedBook\Booking\Service\ConsentService $consentService */
        $consentService = $this->module->get('MedBook\\Booking\\Service\\ConsentService');

        $action = Tools::getValue('action', '');

        if ($action === 'grant') {
            $this->handleGrant($consentService, $customerId);
        } elseif ($action === 'revoke') {
            $this->handleRevoke($consentService, $customerId);
        } elseif ($action === 'export') {
            $this->handleExport($consentService, $customerId);

            return;
        }

        $consents = [
            'medical_data' => $consentService->hasConsent($customerId, 'medical_data'),
            'marketing' => $consentService->hasConsent($customerId, 'marketing'),
            'third_party' => $consentService->hasConsent($customerId, 'third_party'),
        ];

        $consentHistory = $consentService->getConsentHistory($customerId);

        $this->context->smarty->assign([
            'consents' => $consents,
            'consent_history' => $consentHistory,
            'consent_action_url' => $this->context->link->getModuleLink('medbook_booking', 'consent'),
        ]);

        $this->setTemplate('module:medbook_booking/views/templates/front/consent.tpl');
    }

    private function handleGrant(\MedBook\Booking\Service\ConsentService $consentService, int $customerId): void
    {
        $consentType = Tools::getValue('consent_type', '');
        $allowedTypes = ['medical_data', 'marketing', 'third_party'];

        if (in_array($consentType, $allowedTypes, true)) {
            $ipAddress = Tools::getRemoteAddr();
            $consentService->recordConsent($customerId, $consentType, $ipAddress);
            $this->success[] = 'Zgoda zostala udzielona.';
        }
    }

    private function handleRevoke(\MedBook\Booking\Service\ConsentService $consentService, int $customerId): void
    {
        $consentType = Tools::getValue('consent_type', '');
        $allowedTypes = ['medical_data', 'marketing', 'third_party'];

        if (in_array($consentType, $allowedTypes, true)) {
            $consentService->revokeConsent($customerId, $consentType);
            $this->success[] = 'Zgoda zostala wycofana.';
        }
    }

    private function handleExport(\MedBook\Booking\Service\ConsentService $consentService, int $customerId): void
    {
        $exportData = $consentService->generateDataExport($customerId);
        $json = json_encode($exportData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="moje_dane_medbook.json"');
        echo $json;
        exit;
    }
}
