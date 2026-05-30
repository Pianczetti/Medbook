<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

class Medbook_bookingBookingModuleFrontController extends ModuleFrontController
{
    public $ssl = true;

    public function initContent(): void
    {
        parent::initContent();

        $provider = $this->module->get('MedBook\\Booking\\Service\\BookingFrontProvider');
        $pricingService = $this->module->get('MedBook\\Booking\\Service\\PricingService');
        $refundService = $this->module->get('MedBook\\Booking\\Service\\RefundService');
        $langId = (int) $this->context->language->id;

        // Handle form submission
        if (Tools::isSubmit('submitBooking')) {
            $this->processBookingSubmission($provider);
        }

        $resources = $provider->getActiveResources($langId);
        $selectedResourceId = (int) Tools::getValue('resource_id', 0);
        $selectedDate = (string) Tools::getValue('date', '');

        $availableSlots = [];
        if ($selectedResourceId && $selectedDate) {
            $availableSlots = $provider->getAvailableSlots($selectedResourceId, $selectedDate, $langId);
        }

        // Calendar data
        $today = new \DateTime();
        $calendarMonth = (int) Tools::getValue('month', (int) $today->format('n'));
        $calendarYear = (int) Tools::getValue('year', (int) $today->format('Y'));

        // Pricing and advanced features
        $showPrices = (bool) Configuration::get('MEDBOOK_BOOKING_SHOW_PRICES');
        $cartIntegrationActive = (int) Configuration::get('MEDBOOK_BOOKING_PRODUCT_ID') > 0;

        // Addons for selected resource (or empty if none selected)
        $addons = [];
        $refundPolicy = [];
        $depositRule = null;
        $resourceData = null;

        if ($selectedResourceId) {
            $addons = $provider->getAddonsForResource($selectedResourceId, $langId);
            $refundRules = $refundService->getRefundPolicy($selectedResourceId);
            $refundPolicy = $refundService->formatPolicyForDisplay($refundRules);
            $depositRule = $provider->getDepositRule($selectedResourceId);
            $resourceData = $provider->getResourceWithPricing($selectedResourceId, $langId);
        }

        // Expose the cart secure_key for CSRF validation on the AJAX cart submission
        $cartSecureKey = '';
        if ($this->context->cart && $this->context->cart->id) {
            $cartSecureKey = $this->context->cart->secure_key ?? '';
        }

        $this->context->smarty->assign([
            'resources' => $resources,
            'selected_resource_id' => $selectedResourceId,
            'selected_date' => $selectedDate,
            'available_slots' => $availableSlots,
            'calendar_month' => $calendarMonth,
            'calendar_year' => $calendarYear,
            'today' => $today->format('Y-m-d'),
            'max_days_ahead' => (int) Configuration::get('MEDBOOK_BOOKING_MAX_DAYS_AHEAD'),
            'allow_guests' => (bool) Configuration::get('MEDBOOK_BOOKING_ALLOW_GUESTS'),
            'customer_logged' => (bool) $this->context->customer->isLogged(),
            'customer_name' => $this->context->customer->isLogged()
                ? $this->context->customer->firstname . ' ' . $this->context->customer->lastname
                : '',
            'customer_email' => $this->context->customer->isLogged()
                ? $this->context->customer->email
                : '',
            'slots_ajax_url' => $this->context->link->getModuleLink('medbook_booking', 'slots'),
            'booking_url' => $this->context->link->getModuleLink('medbook_booking', 'booking'),
            'cartbooking_url' => $this->context->link->getModuleLink('medbook_booking', 'cartbooking'),
            'show_prices' => $showPrices,
            'cart_integration_active' => $cartIntegrationActive,
            'cart_secure_key' => $cartSecureKey,
            'addons' => $addons,
            'refund_policy' => $refundPolicy,
            'deposit_rule' => $depositRule,
            'resource_min_duration' => $resourceData ? (int) $resourceData['min_duration_minutes'] : 0,
            'resource_max_duration' => $resourceData ? (int) $resourceData['max_duration_minutes'] : 0,
            'resource_base_price' => $resourceData ? (float) $resourceData['base_price'] : 0.0,
        ]);

        $this->setTemplate('module:medbook_booking/views/templates/front/booking.tpl');
    }

    private function processBookingSubmission($provider): void
    {
        $resourceId = (int) Tools::getValue('resource_id', 0);
        $date = (string) Tools::getValue('booking_date', '');
        $timeStart = (string) Tools::getValue('time_start', '');
        $timeEnd = (string) Tools::getValue('time_end', '');
        $customerName = (string) Tools::getValue('customer_name', '');
        $customerEmail = (string) Tools::getValue('customer_email', '');
        $customerPhone = (string) Tools::getValue('customer_phone', '');
        $notes = (string) Tools::getValue('notes', '');

        $errors = [];

        if (!$resourceId) {
            $errors[] = 'Please select a resource.';
        }
        if (!$date) {
            $errors[] = 'Please select a date.';
        }
        if (!$timeStart) {
            $errors[] = 'Please select a time slot.';
        }
        if (!$customerName) {
            $errors[] = 'Please enter your name.';
        }
        if (!$customerEmail || !Validate::isEmail($customerEmail)) {
            $errors[] = 'Please enter a valid email address.';
        }

        if (!empty($errors)) {
            $this->context->smarty->assign('booking_errors', $errors);

            return;
        }

        // Cart integration flow
        $productId = (int) Configuration::get('MEDBOOK_BOOKING_PRODUCT_ID');
        if ($productId > 0) {
            $cartService = $this->module->get('MedBook\\Booking\\Service\\CartIntegrationService');
            $cartId = (int) $this->context->cart->id;

            if (!$cartId) {
                $this->context->cart->add();
                $cartId = (int) $this->context->cart->id;
            }

            $addonIds = Tools::getValue('addon_ids', []);
            if (!is_array($addonIds)) {
                $addonIds = [];
            }
            $addonIds = array_map('intval', $addonIds);

            $customerId = $this->context->customer->isLogged()
                ? (int) $this->context->customer->id
                : 0;

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
                $cartUrl = $this->context->link->getPageLink('cart', true, null, ['action' => 'show']);
                Tools::redirect($cartUrl);
            } else {
                $this->context->smarty->assign('booking_errors', [$result['error']]);
            }

            return;
        }

        // Fallback: direct booking creation (no cart integration)
        $customerId = $this->context->customer->isLogged()
            ? (int) $this->context->customer->id
            : null;

        $result = $provider->createBooking([
            'id_resource' => $resourceId,
            'booking_date' => $date,
            'time_start' => $timeStart,
            'customer_name' => $customerName,
            'customer_email' => $customerEmail,
            'customer_phone' => $customerPhone ?: null,
            'notes' => $notes ?: null,
            'id_customer' => $customerId,
        ]);

        if ($result['success']) {
            $this->context->smarty->assign([
                'booking_success' => true,
                'reference_code' => $result['reference_code'],
            ]);
        } else {
            $this->context->smarty->assign('booking_errors', [$result['error']]);
        }
    }
}
