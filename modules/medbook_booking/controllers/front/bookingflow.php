<?php

declare(strict_types=1);

if (!defined('_PS_VERSION_')) {
    exit;
}

class Medbook_bookingBookingflowModuleFrontController extends ModuleFrontController
{
    public $ssl = true;

    public function initContent(): void
    {
        parent::initContent();

        $step = (int) Tools::getValue('step', 1);
        $langId = (int) $this->context->language->id;

        switch ($step) {
            case 2:
                $this->stepDoctor($langId);
                break;
            case 3:
                $this->stepDatetime($langId);
                break;
            case 4:
                $this->stepConfirm($langId);
                break;
            default:
                $this->stepSpecialization($langId);
                break;
        }
    }

    private function stepSpecialization(int $langId): void
    {
        $specializations = $this->getSpecializations($langId);

        $this->context->smarty->assign([
            'specializations' => $specializations,
            'selected_specialization' => (int) Tools::getValue('specialization_id', 0),
        ]);

        $this->setTemplate('module:medbook_booking/views/templates/front/booking-step1.tpl');
    }

    private function stepDoctor(int $langId): void
    {
        $specializationId = (int) Tools::getValue('specialization_id', 0);
        $visitType = (string) Tools::getValue('visit_type', 'stationary');
        $insuranceType = (string) Tools::getValue('insurance_type', '');

        $doctors = $this->getDoctorsBySpecialization($specializationId, $langId);
        $specializationName = $this->getSpecializationName($specializationId, $langId);

        $this->context->smarty->assign([
            'doctors' => $doctors,
            'selected_specialization' => $specializationId,
            'selected_specialization_name' => $specializationName,
            'selected_visit_type' => $visitType,
            'selected_insurance_type' => $insuranceType,
        ]);

        $this->setTemplate('module:medbook_booking/views/templates/front/booking-step2.tpl');
    }

    private function stepDatetime(int $langId): void
    {
        $doctorId = (int) Tools::getValue('doctor_id', 0);
        $specializationId = (int) Tools::getValue('specialization_id', 0);
        $visitType = (string) Tools::getValue('visit_type', 'stationary');
        $insuranceType = (string) Tools::getValue('insurance_type', '');

        $doctorName = $this->getDoctorName($doctorId);
        $resourceId = $this->getDoctorResourceId($doctorId);

        $today = new \DateTime();
        $calendarDays = $this->buildCalendarDays($today);

        $this->context->smarty->assign([
            'selected_doctor_id' => $doctorId,
            'selected_doctor_name' => $doctorName,
            'selected_specialization' => $specializationId,
            'selected_visit_type' => $visitType,
            'selected_insurance_type' => $insuranceType,
            'resource_id' => $resourceId,
            'slots_ajax_url' => $this->context->link->getModuleLink('medbook_booking', 'slots'),
            'selected_date' => $today->format('Y-m-d'),
            'available_slots' => [],
            'calendar_days' => $calendarDays,
        ]);

        $this->setTemplate('module:medbook_booking/views/templates/front/booking-step3.tpl');
    }

    private function stepConfirm(int $langId): void
    {
        if (Tools::isSubmit('submitBooking')) {
            $this->processBooking();

            return;
        }

        $doctorId = (int) Tools::getValue('doctor_id', 0);
        $specializationId = (int) Tools::getValue('specialization_id', 0);
        $date = (string) Tools::getValue('booking_date', '');
        $time = (string) Tools::getValue('time_start', '');
        $visitType = (string) Tools::getValue('visit_type', 'stationary');
        $insuranceType = (string) Tools::getValue('insurance_type', '');

        $doctorName = $this->getDoctorName($doctorId);
        $specializationName = $this->getSpecializationName($specializationId, $langId);
        $resourceId = $this->getDoctorResourceId($doctorId);

        $this->context->smarty->assign([
            'selected_doctor_id' => $doctorId,
            'selected_doctor_name' => $doctorName,
            'selected_specialization' => $specializationId,
            'selected_specialization_name' => $specializationName,
            'selected_date' => $date,
            'selected_time' => $time,
            'selected_visit_type' => $visitType,
            'selected_insurance_type' => $insuranceType,
            'resource_id' => $resourceId,
        ]);

        $this->setTemplate('module:medbook_booking/views/templates/front/booking-step4.tpl');
    }

    private function processBooking(): void
    {
        $provider = $this->module->get('MedBook\\Booking\\Service\\BookingFrontProvider');

        $resourceId = (int) Tools::getValue('resource_id', 0);
        $date = (string) Tools::getValue('booking_date', '');
        $timeStart = (string) Tools::getValue('time_start', '');
        $customerName = (string) Tools::getValue('customer_name', '');
        $customerEmail = (string) Tools::getValue('customer_email', '');
        $customerPhone = (string) Tools::getValue('customer_phone', '');
        $notes = (string) Tools::getValue('notes', '');

        $customerId = $this->context->customer->isLogged()
            ? (int) $this->context->customer->id
            : null;

        if ($this->context->customer->isLogged()) {
            $customerName = $this->context->customer->firstname . ' ' . $this->context->customer->lastname;
            $customerEmail = $this->context->customer->email;
        }

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

        $this->setTemplate('module:medbook_booking/views/templates/front/booking-step4.tpl');
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function getSpecializations(int $langId): array
    {
        $sql = 'SELECT s.`id_medbook_specialization` as `id`, sl.`name`
                FROM `' . _DB_PREFIX_ . 'medbook_specialization` s
                LEFT JOIN `' . _DB_PREFIX_ . 'medbook_specialization_lang` sl
                    ON s.`id_medbook_specialization` = sl.`id_medbook_specialization`
                    AND sl.`id_lang` = ' . $langId . '
                WHERE s.`active` = 1
                ORDER BY sl.`name` ASC';

        $result = \Db::getInstance()->executeS($sql);

        return is_array($result) ? $result : [];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function getDoctorsBySpecialization(int $specializationId, int $langId): array
    {
        $where = 'd.`active` = 1';
        $join = '';

        if ($specializationId > 0) {
            $join = ' INNER JOIN `' . _DB_PREFIX_ . 'medbook_doctor_specialization` ds
                      ON d.`id_medbook_doctor` = ds.`id_medbook_doctor`
                      AND ds.`id_medbook_specialization` = ' . $specializationId;
        }

        $sql = 'SELECT d.`id_medbook_doctor` as `id`,
                       CONCAT(d.`title`, \' \', d.`firstname`, \' \', d.`lastname`) as `name`,
                       d.`photo_url`,
                       d.`rating`,
                       d.`reviews_count`,
                       d.`consultation_price` as `price`
                FROM `' . _DB_PREFIX_ . 'medbook_doctor` d
                ' . $join . '
                WHERE ' . $where . '
                ORDER BY d.`rating` DESC
                LIMIT 50';

        $results = \Db::getInstance()->executeS($sql);

        if (!is_array($results)) {
            return [];
        }

        foreach ($results as &$doctor) {
            $doctor['profile_url'] = $this->context->link->getModuleLink(
                'medbook_booking',
                'doctorprofile',
                ['id' => (int) $doctor['id']]
            );
            $doctor['specialization'] = '';
            $doctor['next_slot'] = '';
            $doctor['city'] = '';
        }

        return $results;
    }

    private function getDoctorName(int $doctorId): string
    {
        if ($doctorId <= 0) {
            return '';
        }

        $sql = 'SELECT CONCAT(`title`, \' \', `firstname`, \' \', `lastname`)
                FROM `' . _DB_PREFIX_ . 'medbook_doctor`
                WHERE `id_medbook_doctor` = ' . $doctorId;

        $result = \Db::getInstance()->getValue($sql);

        return is_string($result) ? $result : '';
    }

    private function getSpecializationName(int $specializationId, int $langId): string
    {
        if ($specializationId <= 0) {
            return '';
        }

        $sql = 'SELECT `name`
                FROM `' . _DB_PREFIX_ . 'medbook_specialization_lang`
                WHERE `id_medbook_specialization` = ' . $specializationId . '
                  AND `id_lang` = ' . $langId;

        $result = \Db::getInstance()->getValue($sql);

        return is_string($result) ? $result : '';
    }

    private function getDoctorResourceId(int $doctorId): int
    {
        if ($doctorId <= 0) {
            return 0;
        }

        $sql = 'SELECT `id_medbook_resource`
                FROM `' . _DB_PREFIX_ . 'medbook_doctor`
                WHERE `id_medbook_doctor` = ' . $doctorId;

        $result = \Db::getInstance()->getValue($sql);

        return (int) $result;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function buildCalendarDays(\DateTime $startDate): array
    {
        $days = [];
        $today = new \DateTime();

        for ($i = 0; $i < 14; ++$i) {
            $date = clone $startDate;
            $date->modify('+' . $i . ' days');
            $days[] = [
                'date' => $date->format('Y-m-d'),
                'day_number' => (int) $date->format('j'),
                'is_today' => $date->format('Y-m-d') === $today->format('Y-m-d'),
                'is_selected' => $i === 0,
                'is_available' => ((int) $date->format('N')) < 7,
            ];
        }

        return $days;
    }
}
