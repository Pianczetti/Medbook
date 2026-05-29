<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

class Medbook_bookingCartbookingModuleFrontController extends ModuleFrontController
{
    public $ssl = true;

    public function init(): void
    {
        parent::init();
        $this->ajax = true;
    }

    public function displayAjax(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        // Ensure cart exists before CSRF validation (guest users may not have one yet)
        $cart = $this->context->cart;
        if (!$cart->id) {
            $cart->add();
            $this->context->cookie->id_cart = (int) $cart->id;
        }

        // CSRF validation: verify the cart secure_key matches the posted token
        $token = (string) Tools::getValue('token', '');
        $secureKey = $cart->secure_key ?? '';

        // If both token and secure_key are empty (edge case: newly created cart for guest),
        // skip validation. Otherwise, require a match.
        if ($secureKey !== '' || $token !== '') {
            if (!$token || $token !== $secureKey) {
                $this->ajaxRender(json_encode([
                    'success' => false,
                    'error' => 'Invalid security token.',
                ]));

                return;
            }
        }

        $resourceId = (int) Tools::getValue('resource_id', 0);
        $date = (string) Tools::getValue('date', '');
        $timeStart = (string) Tools::getValue('time_start', '');
        $timeEnd = (string) Tools::getValue('time_end', '');
        $addonIdsRaw = Tools::getValue('addon_ids', '');

        // Parse addon_ids: accept JSON array or comma-separated string
        $addonIds = [];
        if (!empty($addonIdsRaw)) {
            if (is_array($addonIdsRaw)) {
                $addonIds = array_map('intval', $addonIdsRaw);
            } else {
                $decoded = json_decode($addonIdsRaw, true);
                if (is_array($decoded)) {
                    $addonIds = array_map('intval', $decoded);
                } else {
                    $addonIds = array_map('intval', explode(',', $addonIdsRaw));
                }
            }
            $addonIds = array_filter($addonIds, function ($id) {
                return $id > 0;
            });
        }

        // Validation
        $errors = [];
        if (!$resourceId) {
            $errors[] = 'Missing resource_id.';
        }
        if (!$date) {
            $errors[] = 'Missing date.';
        }
        if (!$timeStart) {
            $errors[] = 'Missing time_start.';
        }
        if (!$timeEnd) {
            $errors[] = 'Missing time_end.';
        }

        if (!empty($errors)) {
            $this->ajaxRender(json_encode([
                'success' => false,
                'error' => implode(' ', $errors),
            ]));

            return;
        }

        // Validate date format
        $dateObj = \DateTime::createFromFormat('Y-m-d', $date);
        if (!$dateObj || $dateObj->format('Y-m-d') !== $date) {
            $this->ajaxRender(json_encode([
                'success' => false,
                'error' => 'Invalid date format. Use YYYY-MM-DD.',
            ]));

            return;
        }

        // Validate time format
        if (!preg_match('/^\d{2}:\d{2}$/', $timeStart) || !preg_match('/^\d{2}:\d{2}$/', $timeEnd)) {
            $this->ajaxRender(json_encode([
                'success' => false,
                'error' => 'Invalid time format. Use HH:MM.',
            ]));

            return;
        }

        // Cart is already created above during CSRF validation
        $cart = $this->context->cart;

        $cartId = (int) $cart->id;
        $customerId = $this->context->customer->isLogged()
            ? (int) $this->context->customer->id
            : 0;

        $cartService = $this->module->get('MedBook\\Booking\\Service\\CartIntegrationService');

        $result = $cartService->addBookingToCart(
            $cartId,
            $resourceId,
            $date,
            $timeStart,
            $timeEnd,
            $addonIds,
            $customerId
        );

        if ($result['success']) {
            $redirectUrl = $this->context->link->getPageLink('cart', true, null, ['action' => 'show']);

            $this->ajaxRender(json_encode([
                'success' => true,
                'redirect_url' => $redirectUrl,
                'total_price' => $result['total_price'],
                'deposit_amount' => $result['deposit_amount'],
            ]));
        } else {
            $this->ajaxRender(json_encode([
                'success' => false,
                'error' => $result['error'],
            ]));
        }
    }
}
