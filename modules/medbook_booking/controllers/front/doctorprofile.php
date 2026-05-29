<?php

declare(strict_types=1);

if (!defined('_PS_VERSION_')) {
    exit;
}

class Medbook_bookingDoctorprofileModuleFrontController extends ModuleFrontController
{
    public $ssl = true;

    public function initContent(): void
    {
        parent::initContent();

        $langId = (int) $this->context->language->id;
        $doctorId = (int) Tools::getValue('id', 0);

        if ($doctorId <= 0) {
            Tools::redirect($this->context->link->getModuleLink('medbook_booking', 'doctorsearch'));

            return;
        }

        $doctor = $this->loadDoctorProfile($doctorId, $langId);

        if (empty($doctor)) {
            Tools::redirect($this->context->link->getModuleLink('medbook_booking', 'doctorsearch'));

            return;
        }

        // Load available slots for the slot picker
        $today = new \DateTime();
        $calendarDays = $this->buildCalendarDays($today);

        $this->context->smarty->assign([
            'doctor' => $doctor,
            'resource_id' => (int) ($doctor['resource_id'] ?? 0),
            'slots_ajax_url' => $this->context->link->getModuleLink('medbook_booking', 'slots'),
            'selected_date' => $today->format('Y-m-d'),
            'available_slots' => [],
            'calendar_days' => $calendarDays,
        ]);

        $this->setTemplate('module:medbook_booking/views/templates/front/doctor-profile.tpl');
    }

    /**
     * @return array<string, mixed>
     */
    private function loadDoctorProfile(int $doctorId, int $langId): array
    {
        $sql = 'SELECT d.`id_medbook_doctor` as `id`,
                       CONCAT(d.`title`, \' \', d.`firstname`, \' \', d.`lastname`) as `name`,
                       d.`photo_url`,
                       d.`rating`,
                       d.`reviews_count`,
                       d.`consultation_price` as `price`,
                       d.`pwz_number`,
                       d.`bio`,
                       d.`experience`,
                       d.`id_medbook_resource` as `resource_id`
                FROM `' . _DB_PREFIX_ . 'medbook_doctor` d
                WHERE d.`id_medbook_doctor` = ' . $doctorId . '
                  AND d.`active` = 1';

        $result = \Db::getInstance()->getRow($sql);

        if (!is_array($result) || empty($result)) {
            return [];
        }

        // Load specializations
        $result['specializations'] = $this->loadDoctorSpecializations($doctorId, $langId);
        $result['specialization'] = !empty($result['specializations']) ? $result['specializations'][0]['name'] : '';

        // Load clinics
        $result['clinics'] = $this->loadDoctorClinics($doctorId);

        // Load education and certifications (stored as JSON)
        $result['education'] = $this->loadDoctorMeta($doctorId, 'education');
        $result['certifications'] = $this->loadDoctorMeta($doctorId, 'certifications');

        // Reviews placeholder
        $result['reviews'] = [];

        return $result;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function loadDoctorSpecializations(int $doctorId, int $langId): array
    {
        $sql = 'SELECT sl.`name`
                FROM `' . _DB_PREFIX_ . 'medbook_doctor_specialization` ds
                LEFT JOIN `' . _DB_PREFIX_ . 'medbook_specialization_lang` sl
                    ON ds.`id_medbook_specialization` = sl.`id_medbook_specialization`
                    AND sl.`id_lang` = ' . $langId . '
                WHERE ds.`id_medbook_doctor` = ' . $doctorId;

        $result = \Db::getInstance()->executeS($sql);

        return is_array($result) ? $result : [];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function loadDoctorClinics(int $doctorId): array
    {
        $sql = 'SELECT c.`name`, CONCAT(c.`street`, \', \', c.`postal_code`, \' \', c.`city`) as `address`, c.`phone`
                FROM `' . _DB_PREFIX_ . 'medbook_doctor_clinic` dc
                INNER JOIN `' . _DB_PREFIX_ . 'medbook_clinic` c
                    ON dc.`id_medbook_clinic` = c.`id_medbook_clinic`
                WHERE dc.`id_medbook_doctor` = ' . $doctorId;

        $result = \Db::getInstance()->executeS($sql);

        return is_array($result) ? $result : [];
    }

    /**
     * @return array<int, string>
     */
    private function loadDoctorMeta(int $doctorId, string $key): array
    {
        $sql = 'SELECT `value`
                FROM `' . _DB_PREFIX_ . 'medbook_doctor_meta`
                WHERE `id_medbook_doctor` = ' . $doctorId . '
                  AND `meta_key` = \'' . pSQL($key) . '\'';

        $result = \Db::getInstance()->getValue($sql);

        if (!$result) {
            return [];
        }

        $decoded = json_decode((string) $result, true);

        return is_array($decoded) ? $decoded : [];
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
