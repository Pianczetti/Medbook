<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

use PrestaShop\PrestaShop\Core\Module\WidgetInterface;

class Medbook_booking extends Module implements WidgetInterface
{
    public const HOOKS = [
        'displayHome',
        'displayCustomerAccount',
        'displayHeader',
        'actionFrontControllerSetMedia',
        'actionValidateOrder',
        'actionCartSave',
        'displayShoppingCartFooter',
        'actionBookingCancelled',
        'actionBookingCompleted',
        'actionBookingConfirmed',
        'actionBookingReminder',
        'displayProductAdditionalInfo',
    ];

    public function __construct()
    {
        $this->name = 'medbook_booking';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'MedBook';
        $this->need_instance = 0;
        $this->bootstrap = true;
        $this->ps_versions_compliancy = ['min' => '9.0.0', 'max' => _PS_VERSION_];

        parent::__construct();

        $this->displayName = $this->trans('MedBook - Rezerwacja Wizyt', [], 'Modules.Medbookbooking.Admin');
        $this->description = $this->trans('Platforma do rezerwacji wizyt lekarskich', [], 'Modules.Medbookbooking.Admin');
    }

    /**
     * @return array[]
     */
    public function getTabs(): array
    {
        return [
            [
                'class_name' => 'AdminMedbookBookingResources',
                'visible' => true,
                'name' => 'MedBook',
                'route_name' => 'admin_medbook_booking_resource_index',
                'parent_class_name' => 'AdminParentThemes',
            ],
            [
                'class_name' => 'AdminMedbookBookingBookings',
                'visible' => true,
                'name' => 'Rezerwacje',
                'route_name' => 'admin_medbook_booking_booking_index',
                'parent_class_name' => 'AdminMedbookBookingResources',
            ],
            [
                'class_name' => 'AdminMedbookBookingDoctors',
                'visible' => true,
                'name' => 'Lekarze',
                'route_name' => 'admin_medbook_booking_doctor_profile_index',
                'parent_class_name' => 'AdminMedbookBookingResources',
            ],
            [
                'class_name' => 'AdminMedbookBookingClinics',
                'visible' => true,
                'name' => 'Przychodnie',
                'route_name' => 'admin_medbook_booking_clinic_index',
                'parent_class_name' => 'AdminMedbookBookingResources',
            ],
            [
                'class_name' => 'AdminMedbookBookingSpecializations',
                'visible' => true,
                'name' => 'Specjalizacje',
                'route_name' => 'admin_medbook_booking_specialization_index',
                'parent_class_name' => 'AdminMedbookBookingResources',
            ],
            [
                'class_name' => 'AdminMedbookBookingDocuments',
                'visible' => true,
                'name' => 'Dokumenty',
                'route_name' => 'admin_medbook_booking_document_index',
                'parent_class_name' => 'AdminMedbookBookingResources',
            ],
            [
                'class_name' => 'AdminMedbookBookingSchedules',
                'visible' => true,
                'name' => 'Harmonogram',
                'route_name' => 'admin_medbook_booking_schedule_index',
                'parent_class_name' => 'AdminMedbookBookingResources',
            ],
            [
                'class_name' => 'AdminMedbookBookingPriceRules',
                'visible' => true,
                'name' => 'Reguly Cenowe',
                'route_name' => 'admin_medbook_booking_price_rule_index',
                'parent_class_name' => 'AdminMedbookBookingResources',
            ],
            [
                'class_name' => 'AdminMedbookBookingAddons',
                'visible' => true,
                'name' => 'Dodatki',
                'route_name' => 'admin_medbook_booking_addon_index',
                'parent_class_name' => 'AdminMedbookBookingResources',
            ],
            [
                'class_name' => 'AdminMedbookBookingRefundRules',
                'visible' => true,
                'name' => 'Reguly Zwrotow',
                'route_name' => 'admin_medbook_booking_refund_rule_index',
                'parent_class_name' => 'AdminMedbookBookingResources',
            ],
            [
                'class_name' => 'AdminMedbookBookingDepositRules',
                'visible' => true,
                'name' => 'Reguly Zaliczek',
                'route_name' => 'admin_medbook_booking_deposit_rule_index',
                'parent_class_name' => 'AdminMedbookBookingResources',
            ],
            [
                'class_name' => 'AdminMedbookBookingSettings',
                'visible' => true,
                'name' => 'Ustawienia',
                'route_name' => 'admin_medbook_booking_settings',
                'parent_class_name' => 'AdminMedbookBookingResources',
            ],
            [
                'class_name' => 'AdminMedbookBookingCalendar',
                'visible' => true,
                'name' => 'Kalendarz',
                'route_name' => 'admin_medbook_booking_calendar',
                'parent_class_name' => 'AdminMedbookBookingResources',
            ],
            [
                'class_name' => 'AdminMedbookBookingRecurring',
                'visible' => true,
                'name' => 'Wizyty Cykliczne',
                'route_name' => 'admin_medbook_booking_recurring_index',
                'parent_class_name' => 'AdminMedbookBookingResources',
            ],
            [
                'class_name' => 'AdminMedbookBookingWaitlist',
                'visible' => true,
                'name' => 'Lista Oczekujacych',
                'route_name' => 'admin_medbook_booking_waitlist_index',
                'parent_class_name' => 'AdminMedbookBookingResources',
            ],
        ];
    }

    public function install(): bool
    {
        if (!parent::install()) {
            return false;
        }

        if (!$this->registerHook(self::HOOKS)) {
            return false;
        }

        $sql = (string) file_get_contents(__DIR__ . '/sql/install.sql');
        $sql = str_replace(['PREFIX_', 'ENGINE_TYPE'], [_DB_PREFIX_, _MYSQL_ENGINE_], $sql);

        foreach (array_filter(array_map('trim', explode(';', $sql))) as $statement) {
            if (!\Db::getInstance()->execute($statement)) {
                return false;
            }
        }

        // Core booking configuration
        Configuration::updateValue('MEDBOOK_BOOKING_CONFIRM_MODE', 'auto');
        Configuration::updateValue('MEDBOOK_BOOKING_MAX_DAYS_AHEAD', 30);
        Configuration::updateValue('MEDBOOK_BOOKING_MIN_HOURS_ADVANCE', 2);
        Configuration::updateValue('MEDBOOK_BOOKING_ALLOW_GUESTS', 1);
        Configuration::updateValue('MEDBOOK_BOOKING_SLOT_DURATION', 30);
        Configuration::updateValue('MEDBOOK_BOOKING_EMAIL_NOTIFICATIONS', 1);
        Configuration::updateValue('MEDBOOK_BOOKING_PRICING_MODE', 'highest_priority');
        Configuration::updateValue('MEDBOOK_BOOKING_PRODUCT_ID', 0);
        Configuration::updateValue('MEDBOOK_BOOKING_SHOW_PRICES', 1);
        Configuration::updateValue('MEDBOOK_BOOKING_REMINDER_HOURS_BEFORE', 24);
        Configuration::updateValue('MEDBOOK_BOOKING_WAITLIST_NOTIFICATION_HOURS', 4);

        // MedBook platform configuration
        Configuration::updateValue('PS_SHOP_NAME', 'MedBook');
        Configuration::updateValue('MEDBOOK_DEFAULT_RESOURCE_TYPE', 'doctor');
        Configuration::updateValue('MEDBOOK_PLATFORM_MODE', 'medical');
        Configuration::updateValue('MEDBOOK_DEFAULT_DURATION', 30);
        Configuration::updateValue('MEDBOOK_REMINDER_HOURS', 24);
        Configuration::updateValue('MEDBOOK_BOOKING_CONFIRMATION', 'email');
        Configuration::updateValue('MEDBOOK_ALLOW_ONLINE_VISITS', 1);
        Configuration::updateValue('MEDBOOK_INSURANCE_TYPES', 'NFZ,prywatne,pakiet');

        // Install default fixtures (specializations, sample clinic)
        $this->installMedbookFixtures();

        return true;
    }

    /**
     * Load and execute the fixture installer for default MedBook data.
     */
    private function installMedbookFixtures(): void
    {
        $fixturesFile = __DIR__ . '/install/fixtures.php';
        if (file_exists($fixturesFile)) {
            require_once $fixturesFile;
            if (function_exists('medbook_install_fixtures')) {
                medbook_install_fixtures();
            }
        }
    }

    public function uninstall(): bool
    {
        $sql = (string) file_get_contents(__DIR__ . '/sql/uninstall.sql');
        $sql = str_replace('PREFIX_', _DB_PREFIX_, $sql);

        foreach (array_filter(array_map('trim', explode(';', $sql))) as $statement) {
            \Db::getInstance()->execute($statement);
        }

        Configuration::deleteByName('MEDBOOK_BOOKING_CONFIRM_MODE');
        Configuration::deleteByName('MEDBOOK_BOOKING_MAX_DAYS_AHEAD');
        Configuration::deleteByName('MEDBOOK_BOOKING_MIN_HOURS_ADVANCE');
        Configuration::deleteByName('MEDBOOK_BOOKING_ALLOW_GUESTS');
        Configuration::deleteByName('MEDBOOK_BOOKING_SLOT_DURATION');
        Configuration::deleteByName('MEDBOOK_BOOKING_EMAIL_NOTIFICATIONS');
        Configuration::deleteByName('MEDBOOK_BOOKING_PRICING_MODE');
        Configuration::deleteByName('MEDBOOK_BOOKING_PRODUCT_ID');
        Configuration::deleteByName('MEDBOOK_BOOKING_SHOW_PRICES');
        Configuration::deleteByName('MEDBOOK_BOOKING_REMINDER_HOURS_BEFORE');
        Configuration::deleteByName('MEDBOOK_BOOKING_WAITLIST_NOTIFICATION_HOURS');
        Configuration::deleteByName('MEDBOOK_DEFAULT_RESOURCE_TYPE');
        Configuration::deleteByName('MEDBOOK_PLATFORM_MODE');
        Configuration::deleteByName('MEDBOOK_DEFAULT_DURATION');
        Configuration::deleteByName('MEDBOOK_REMINDER_HOURS');
        Configuration::deleteByName('MEDBOOK_BOOKING_CONFIRMATION');
        Configuration::deleteByName('MEDBOOK_ALLOW_ONLINE_VISITS');
        Configuration::deleteByName('MEDBOOK_INSURANCE_TYPES');

        return parent::uninstall();
    }

    public function getWidgetVariables($hookName = null, array $configuration = [])
    {
        try {
            $provider = $this->get('MedBook\\Booking\\Service\\BookingFrontProvider');
        } catch (\Throwable $e) {
            return [];
        }

        if (!$provider) {
            return [];
        }

        $langId = (int) $this->context->language->id;
        $slots = $provider->getNextAvailableSlots(5, $langId);

        return [
            'booking_slots' => $slots,
            'booking_link' => $this->context->link->getModuleLink($this->name, 'booking'),
        ];
    }

    public function renderWidget($hookName = null, array $configuration = [])
    {
        $variables = $this->getWidgetVariables($hookName, $configuration);
        if (empty($variables)) {
            return '';
        }

        $this->smarty->assign($variables);

        return $this->fetch('module:' . $this->name . '/views/templates/hook/booking_widget.tpl');
    }

    public function hookDisplayHome(array $params)
    {
        return $this->renderWidget('displayHome');
    }

    public function hookDisplayCustomerAccount(array $params)
    {
        return $this->fetch('module:' . $this->name . '/views/templates/hook/customer_account_link.tpl');
    }

    public function hookActionFrontControllerSetMedia(array $params): void
    {
        $controller = $this->context->controller;
        $isBookingPage = ($controller instanceof \ModuleFrontController && $controller->module && $controller->module->name === $this->name);

        if ($isBookingPage) {
            $this->context->controller->registerStylesheet(
                'medbook-booking-css',
                'modules/' . $this->name . '/views/css/medbook-booking.css',
                ['priority' => 200, 'media' => 'all']
            );

            $this->context->controller->registerJavascript(
                'medbook-booking-js',
                'modules/' . $this->name . '/views/js/medbook-booking.js',
                ['priority' => 200, 'position' => 'bottom']
            );
        }
    }

    public function hookActionValidateOrder(array $params): void
    {
        $order = $params['order'] ?? null;
        if (!$order || !$order->id_cart) {
            return;
        }

        $cartId = (int) $order->id_cart;
        $orderId = (int) $order->id;

        $cartService = $this->get('MedBook\\Booking\\Service\\CartIntegrationService');
        $cartData = $cartService->getCartBookingData($cartId);

        if ($cartData) {
            $cartService->finalizeBooking($cartId, $orderId);
        }
    }

    public function hookActionCartSave(array $params): void
    {
        // Throttle: only run cleanup if last run was more than 1 hour ago
        $lastCleanup = (int) Configuration::get('MEDBOOK_BOOKING_LAST_CART_CLEANUP');
        $now = time();

        if ($lastCleanup > 0 && ($now - $lastCleanup) < 3600) {
            return;
        }

        Configuration::updateValue('MEDBOOK_BOOKING_LAST_CART_CLEANUP', $now);

        $cartService = $this->get('MedBook\\Booking\\Service\\CartIntegrationService');
        $cartService->cleanExpiredCartData(24);
    }

    public function hookActionBookingCancelled(array $params): void
    {
        $bookingId = (int) ($params['id_booking'] ?? 0);
        if ($bookingId <= 0) {
            return;
        }

        $booking = \Db::getInstance()->getRow(
            'SELECT id_resource, booking_date, time_start, time_end FROM ' . _DB_PREFIX_ . 'medbook_booking WHERE id_booking = ' . $bookingId
        );

        if (!$booking) {
            return;
        }

        /** @var \MedBook\Booking\Service\WaitlistService $waitlistService */
        $waitlistService = $this->get('MedBook\\Booking\\Service\\WaitlistService');

        $entries = $waitlistService->findMatchingEntries(
            (int) $booking['id_resource'],
            $booking['booking_date'],
            $booking['time_start'],
            $booking['time_end']
        );

        if (!empty($entries)) {
            $waitlistService->notifyEntry((int) $entries[0]['id_waitlist']);
        }
    }

    public function hookActionBookingCompleted(array $params): void
    {
        $bookingId = (int) ($params['id_booking'] ?? 0);
        if ($bookingId <= 0) {
            return;
        }

        /** @var \MedBook\Booking\Service\RecurrenceService $recurrenceService */
        $recurrenceService = $this->get('MedBook\\Booking\\Service\\RecurrenceService');
        $recurrenceService->createSuggestion($bookingId);
    }

    public function hookActionBookingConfirmed(array $params): void
    {
        $bookingId = (int) ($params['id_booking'] ?? 0);
        if ($bookingId <= 0) {
            return;
        }

        // Generate meeting URL for online visits
        /** @var \MedBook\Booking\Service\OnlineVisitService $onlineVisitService */
        $onlineVisitService = $this->get('MedBook\\Booking\\Service\\OnlineVisitService');
        $onlineVisitService->handleBookingConfirmed($bookingId);

        // Send confirmation email
        $booking = \Db::getInstance()->getRow(
            'SELECT * FROM ' . _DB_PREFIX_ . 'medbook_booking WHERE id_booking = ' . $bookingId
        );

        if (!$booking || empty($booking['customer_email'])) {
            return;
        }

        $resourceName = \Db::getInstance()->getValue(
            'SELECT rl.name FROM ' . _DB_PREFIX_ . 'medbook_resource_lang rl
             WHERE rl.id_resource = ' . (int) $booking['id_resource'] . '
             AND rl.id_lang = ' . (int) Configuration::get('PS_LANG_DEFAULT')
        );

        $visitType = $booking['visit_type'] === 'online' ? 'Online' : 'Stacjonarna';

        $templateVars = [
            '{customer_name}' => $booking['customer_name'],
            '{resource_name}' => $resourceName ?: '',
            '{booking_date}' => $booking['booking_date'],
            '{booking_time}' => $booking['time_start'] . ' - ' . $booking['time_end'],
            '{visit_type}' => $visitType,
            '{reference_code}' => $booking['reference_code'],
            '{video_call_section}' => '',
        ];

        if ($booking['visit_type'] === 'online' && !empty($booking['video_call_url'])) {
            $templateVars['{video_call_section}'] = '<p style="font-size:14px;color:#333333;margin:0 0 10px;"><strong>Link do wideokonferencji:</strong> ' . $booking['video_call_url'] . '</p>';
        }

        \Mail::send(
            (int) Configuration::get('PS_LANG_DEFAULT'),
            'booking_confirmation',
            'Potwierdzenie rezerwacji wizyty',
            $templateVars,
            $booking['customer_email'],
            $booking['customer_name'],
            null,
            null,
            null,
            null,
            __DIR__ . '/mails/',
            false,
            (int) Configuration::get('PS_SHOP_DEFAULT')
        );
    }

    public function hookActionBookingReminder(array $params): void
    {
        $bookingId = (int) ($params['id_booking'] ?? 0);
        if ($bookingId <= 0) {
            return;
        }

        $booking = \Db::getInstance()->getRow(
            'SELECT * FROM ' . _DB_PREFIX_ . 'medbook_booking WHERE id_booking = ' . $bookingId
        );

        if (!$booking || empty($booking['customer_email'])) {
            return;
        }

        $resourceName = \Db::getInstance()->getValue(
            'SELECT rl.name FROM ' . _DB_PREFIX_ . 'medbook_resource_lang rl
             WHERE rl.id_resource = ' . (int) $booking['id_resource'] . '
             AND rl.id_lang = ' . (int) Configuration::get('PS_LANG_DEFAULT')
        );

        $visitType = $booking['visit_type'] === 'online' ? 'Online' : 'Stacjonarna';

        $templateVars = [
            '{customer_name}' => $booking['customer_name'],
            '{resource_name}' => $resourceName ?: '',
            '{booking_date}' => $booking['booking_date'],
            '{booking_time}' => $booking['time_start'] . ' - ' . $booking['time_end'],
            '{visit_type}' => $visitType,
        ];

        \Mail::send(
            (int) Configuration::get('PS_LANG_DEFAULT'),
            'booking_reminder',
            'Przypomnienie o wizycie',
            $templateVars,
            $booking['customer_email'],
            $booking['customer_name'],
            null,
            null,
            null,
            null,
            __DIR__ . '/mails/',
            false,
            (int) Configuration::get('PS_SHOP_DEFAULT')
        );
    }

    public function hookDisplayProductAdditionalInfo(array $params): string
    {
        $idProduct = (int) ($params['product']['id_product'] ?? $params['product']->id ?? 0);
        if ($idProduct <= 0) {
            return '';
        }

        $resource = \Db::getInstance()->getRow(
            'SELECT r.id_resource, rl.name FROM `' . _DB_PREFIX_ . 'medbook_resource` r
             LEFT JOIN `' . _DB_PREFIX_ . 'medbook_resource_lang` rl ON r.id_resource = rl.id_resource AND rl.id_lang = ' . (int) $this->context->language->id . '
             WHERE r.id_product = ' . $idProduct . ' AND r.is_active = 1'
        );

        if (!$resource) {
            return '';
        }

        $bookingUrl = $this->context->link->getModuleLink($this->name, 'booking', [
            'id_resource' => (int) $resource['id_resource'],
        ]);

        $this->context->smarty->assign([
            'booking_resource_name' => $resource['name'] ?? '',
            'booking_url' => $bookingUrl,
        ]);

        return $this->fetch('module:' . $this->name . '/views/templates/hook/product_booking_button.tpl');
    }

    public function hookDisplayShoppingCartFooter(array $params): string
    {
        $cartId = (int) $this->context->cart->id;
        if (!$cartId) {
            return '';
        }

        $cartService = $this->get('MedBook\\Booking\\Service\\CartIntegrationService');
        $cartData = $cartService->getCartBookingData($cartId);

        if (!$cartData) {
            return '';
        }

        // Get resource name
        $provider = $this->get('MedBook\\Booking\\Service\\BookingFrontProvider');
        $langId = (int) $this->context->language->id;
        $resourceData = $provider->getResourceWithPricing((int) $cartData['id_resource'], $langId);

        // Parse addons
        $addonNames = [];
        if (!empty($cartData['addons_json'])) {
            $addonIds = json_decode($cartData['addons_json'], true);
            if (is_array($addonIds)) {
                $addons = $provider->getAddonsForResource((int) $cartData['id_resource'], $langId);
                foreach ($addons as $addon) {
                    if (in_array((int) $addon['id_addon'], $addonIds, true)) {
                        $addonNames[] = [
                            'name' => $addon['name'],
                            'price' => $addon['price'],
                        ];
                    }
                }
            }
        }

        $this->smarty->assign([
            'booking_resource_name' => $resourceData ? $resourceData['name'] : '',
            'booking_date' => $cartData['booking_date'],
            'booking_time_start' => $cartData['time_start'],
            'booking_time_end' => $cartData['time_end'],
            'booking_addons' => $addonNames,
            'booking_total_price' => (float) $cartData['total_price'],
            'booking_deposit_amount' => (float) $cartData['deposit_amount'],
        ]);

        return $this->fetch('module:' . $this->name . '/views/templates/hook/cart_booking_summary.tpl');
    }
}
