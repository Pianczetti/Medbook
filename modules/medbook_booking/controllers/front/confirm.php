<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

class Medbook_bookingConfirmModuleFrontController extends ModuleFrontController
{
    public $auth = false;
    public $ssl = true;

    public function initContent(): void
    {
        parent::initContent();

        $token = (string) Tools::getValue('token', '');
        $success = false;
        $action = '';
        $errorMessage = '';

        if (!$token) {
            $errorMessage = 'No token provided.';
        } else {
            /** @var \MedBook\Booking\Service\ReminderService $reminderService */
            $reminderService = $this->module->get('MedBook\\Booking\\Service\\ReminderService');

            $tokenData = $reminderService->validateToken($token);

            if ($tokenData === null) {
                $errorMessage = 'This link is invalid, expired, or has already been used.';
            } else {
                try {
                    $consumed = $reminderService->consumeToken($token);
                    $action = $consumed['action'];
                    $bookingId = (int) $consumed['id_booking'];

                    if ($action === 'confirm') {
                        Db::getInstance()->update(
                            'medbook_booking',
                            [
                                'status' => 'confirmed',
                                'confirmed_at' => date('Y-m-d H:i:s'),
                            ],
                            'id_booking = ' . $bookingId
                        );
                    } elseif ($action === 'cancel') {
                        Db::getInstance()->update(
                            'medbook_booking',
                            [
                                'status' => 'cancelled',
                            ],
                            'id_booking = ' . $bookingId
                        );
                    }

                    $success = true;
                } catch (\RuntimeException $e) {
                    $errorMessage = $e->getMessage();
                }
            }
        }

        $this->context->smarty->assign([
            'confirm_success' => $success,
            'confirm_action' => $action,
            'confirm_error' => $errorMessage,
        ]);

        $this->setTemplate('module:medbook_booking/views/templates/front/confirm.tpl');
    }
}
