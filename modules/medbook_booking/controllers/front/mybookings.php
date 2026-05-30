<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

class Medbook_bookingMybookingsModuleFrontController extends ModuleFrontController
{
    public $auth = true;
    public $ssl = true;

    public function initContent(): void
    {
        parent::initContent();

        $provider = $this->module->get('MedBook\\Booking\\Service\\BookingFrontProvider');
        $customerId = (int) $this->context->customer->id;

        // Handle cancel action
        if (Tools::isSubmit('cancelBooking')) {
            $bookingId = (int) Tools::getValue('id_booking', 0);
            if ($bookingId) {
                $result = $provider->cancelBooking($bookingId, $customerId);
                if ($result['success']) {
                    $this->context->smarty->assign('cancel_success', true);
                } else {
                    $this->context->smarty->assign('cancel_error', $result['error']);
                }
            }
        }

        $bookings = $provider->getCustomerBookings($customerId, (int) $this->context->language->id);

        $this->context->smarty->assign([
            'bookings' => $bookings,
            'booking_page_url' => $this->context->link->getModuleLink('medbook_booking', 'booking'),
        ]);

        $this->setTemplate('module:medbook_booking/views/templates/front/mybookings.tpl');
    }
}
