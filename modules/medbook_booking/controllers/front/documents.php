<?php

declare(strict_types=1);

if (!defined('_PS_VERSION_')) {
    exit;
}

class Medbook_bookingDocumentsModuleFrontController extends ModuleFrontController
{
    public $ssl = true;
    public $auth = true;

    public function initContent(): void
    {
        parent::initContent();

        $customerId = (int) $this->context->customer->id;

        /** @var \MedBook\Booking\Service\DocumentService $documentService */
        $documentService = $this->module->get('MedBook\\Booking\\Service\\DocumentService');

        $action = Tools::getValue('action', '');

        if ($action === 'download') {
            $this->handleDownload($documentService, $customerId);

            return;
        }

        $documents = $documentService->listForPatient($customerId);

        $prescriptions = [];
        $referrals = [];
        $results = [];
        $other = [];

        foreach ($documents as $doc) {
            $doc['download_url'] = $this->context->link->getModuleLink('medbook_booking', 'documents', [
                'action' => 'download',
                'id_document' => (int) $doc['id_document'],
            ]);

            switch ($doc['document_type']) {
                case 'prescription':
                    $prescriptions[] = $doc;
                    break;
                case 'referral':
                    $referrals[] = $doc;
                    break;
                case 'result':
                    $results[] = $doc;
                    break;
                default:
                    $other[] = $doc;
                    break;
            }
        }

        $this->context->smarty->assign([
            'prescriptions' => $prescriptions,
            'referrals' => $referrals,
            'results' => $results,
            'other_documents' => $other,
        ]);

        $this->setTemplate('module:medbook_booking/views/templates/front/documents.tpl');
    }

    private function handleDownload(\MedBook\Booking\Service\DocumentService $documentService, int $customerId): void
    {
        $documentId = (int) Tools::getValue('id_document', 0);

        if ($documentId <= 0) {
            Tools::redirect($this->context->link->getModuleLink('medbook_booking', 'documents'));

            return;
        }

        $fileData = $documentService->download($documentId, $customerId);

        if (!$fileData) {
            $this->errors[] = 'Dokument nie zostal znaleziony.';
            $this->initContent();

            return;
        }

        header('Content-Type: ' . $fileData['mime_type']);
        header('Content-Disposition: attachment; filename="' . $fileData['original_name'] . '"');
        header('Content-Length: ' . filesize($fileData['path']));
        readfile($fileData['path']);
        exit;
    }
}
