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
        $specializations = $this->getSpecializations();

        $this->context->smarty->assign([
            'specializations' => $specializations,
            'selected_specialization' => (int) Tools::getValue('specialization_id', 0),
        ]);

        $this->setTemplate('module:medbook_booking/views/templates/front/booking-step1.tpl');
    }

    private function stepDoctor(int $langId): void
    {
        $specializationId = (int) Tools::getValue('specialization_id', 0);
        $visitType = (string) Tools::getValue('visit_type', 'stacjonarna');
        $insuranceType = (string) Tools::getValue('insurance_type', '');

        $doctors = $this->getDoctorsBySpecialization($specializationId, $langId);
        $specializationName = $this->getSpecializationName($specializationId);

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
        $doctorProfileId = (int) Tools::getValue('doctor_id', 0);
        $specializationId = (int) Tools::getValue('specialization_id', 0);
        $visitType = (string) Tools::getValue('visit_type', 'stacjonarna');
        $insuranceType = (string) Tools::getValue('insurance_type', '');

        $doctorName = $this->getDoctorName($doctorProfileId, $langId);
        $resourceId = $this->getDoctorResourceId($doctorProfileId);

        $today = new \DateTime();
        $calendarDays = $this->buildCalendarDays($today);

        $this->context->smarty->assign([
            'selected_doctor_id' => $doctorProfileId,
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

        $doctorProfileId = (int) Tools::getValue('doctor_id', 0);
        $specializationId = (int) Tools::getValue('specialization_id', 0);
        $date = (string) Tools::getValue('booking_date', '');
        $time = (string) Tools::getValue('time_start', '');
        $visitType = (string) Tools::getValue('visit_type', 'stacjonarna');
        $insuranceType = (string) Tools::getValue('insurance_type', '');

        $doctorName = $this->getDoctorName($doctorProfileId, $langId);
        $specializationName = $this->getSpecializationName($specializationId);
        $resourceId = $this->getDoctorResourceId($doctorProfileId);

        $this->context->smarty->assign([
            'selected_doctor_id' => $doctorProfileId,
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
    private function getSpecializations(): array
    {
        $sql = 'SELECT s.`id_specialization` as `id`, s.`name`
                FROM `' . _DB_PREFIX_ . 'medbook_specialization` s
                WHERE s.`is_active` = 1
                ORDER BY s.`name` ASC';

        $result = \Db::getInstance()->executeS($sql);

        return is_array($result) ? $result : [];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function getDoctorsBySpecialization(int $specializationId, int $langId): array
    {
        $where = ['r.`is_active` = 1', 'r.`resource_type` = \'doctor\''];

        if ($specializationId > 0) {
            $where[] = 'dp.`id_specialization` = ' . $specializationId;
        }

        $sql = 'SELECT dp.`id_doctor_profile` as `id`,
                       rl.`name`,
                       dp.`photo`,
                       dp.`experience_years`,
                       r.`base_price` as `price`,
                       r.`id_resource` as `resource_id`,
                       s.`name` as `specialization_name`
                FROM `' . _DB_PREFIX_ . 'medbook_resource` r
                INNER JOIN `' . _DB_PREFIX_ . 'medbook_doctor_profile` dp
                    ON dp.`id_resource` = r.`id_resource`
                LEFT JOIN `' . _DB_PREFIX_ . 'medbook_resource_lang` rl
                    ON rl.`id_resource` = r.`id_resource` AND rl.`id_lang` = ' . $langId . '
                LEFT JOIN `' . _DB_PREFIX_ . 'medbook_specialization` s
                    ON s.`id_specialization` = dp.`id_specialization`
                WHERE ' . implode(' AND ', $where) . '
                ORDER BY rl.`name` ASC
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
            $doctor['specialization'] = $doctor['specialization_name'] ?? '';
            $doctor['next_slot'] = '';
            $doctor['city'] = '';
        }

        return $results;
    }

    private function getDoctorName(int $doctorProfileId, int $langId): string
    {
        if ($doctorProfileId <= 0) {
            return '';
        }

        $sql = 'SELECT rl.`name`
                FROM `' . _DB_PREFIX_ . 'medbook_doctor_profile` dp
                INNER JOIN `' . _DB_PREFIX_ . 'medbook_resource_lang` rl
                    ON rl.`id_resource` = dp.`id_resource` AND rl.`id_lang` = ' . $langId . '
                WHERE dp.`id_doctor_profile` = ' . $doctorProfileId;

        $result = \Db::getInstance()->getValue($sql);

        return is_string($result) ? $result : '';
    }

    private function getSpecializationName(int $specializationId): string
    {
        if ($specializationId <= 0) {
            return '';
        }

        $sql = 'SELECT `name`
                FROM `' . _DB_PREFIX_ . 'medbook_specialization`
                WHERE `id_specialization` = ' . $specializationId;

        $result = \Db::getInstance()->getValue($sql);

        return is_string($result) ? $result : '';
    }

    private function getDoctorResourceId(int $doctorProfileId): int
    {
        if ($doctorProfileId <= 0) {
            return 0;
        }

        $sql = 'SELECT `id_resource`
                FROM `' . _DB_PREFIX_ . 'medbook_doctor_profile`
                WHERE `id_doctor_profile` = ' . $doctorProfileId;

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
