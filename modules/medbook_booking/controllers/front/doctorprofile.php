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
        $doctorProfileId = (int) Tools::getValue('id', 0);

        if ($doctorProfileId <= 0) {
            Tools::redirect($this->context->link->getModuleLink('medbook_booking', 'doctorsearch'));

            return;
        }

        $doctor = $this->loadDoctorProfile($doctorProfileId, $langId);

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
    private function loadDoctorProfile(int $doctorProfileId, int $langId): array
    {
        $sql = 'SELECT dp.`id_doctor_profile` as `id`,
                       rl.`name`,
                       dp.`photo`,
                       dp.`experience_years`,
                       dp.`pwz_number`,
                       dp.`education`,
                       dp.`certifications`,
                       dp.`languages`,
                       dp.`consultation_online`,
                       dp.`consultation_inperson`,
                       r.`base_price` as `price`,
                       r.`id_resource` as `resource_id`,
                       s.`name` as `specialization_name`
                FROM `' . _DB_PREFIX_ . 'medbook_doctor_profile` dp
                INNER JOIN `' . _DB_PREFIX_ . 'medbook_resource` r
                    ON r.`id_resource` = dp.`id_resource`
                LEFT JOIN `' . _DB_PREFIX_ . 'medbook_resource_lang` rl
                    ON rl.`id_resource` = r.`id_resource` AND rl.`id_lang` = ' . $langId . '
                LEFT JOIN `' . _DB_PREFIX_ . 'medbook_specialization` s
                    ON s.`id_specialization` = dp.`id_specialization`
                WHERE dp.`id_doctor_profile` = ' . $doctorProfileId . '
                  AND r.`is_active` = 1';

        $result = \Db::getInstance()->getRow($sql);

        if (!is_array($result) || empty($result)) {
            return [];
        }

        // Load specialization
        $result['specialization'] = $result['specialization_name'] ?? '';
        $result['specializations'] = [];
        if (!empty($result['specialization_name'])) {
            $result['specializations'][] = ['name' => $result['specialization_name']];
        }

        // Load clinics
        $result['clinics'] = $this->loadDoctorClinics((int) $result['resource_id']);

        // Parse education and certifications (stored as TEXT, may be JSON)
        $result['education_list'] = $this->parseTextOrJson($result['education'] ?? '');
        $result['certifications_list'] = $this->parseTextOrJson($result['certifications'] ?? '');

        // Reviews placeholder
        $result['reviews'] = [];

        return $result;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function loadDoctorClinics(int $resourceId): array
    {
        $sql = 'SELECT c.`name`, CONCAT(c.`address`, \', \', c.`postal_code`, \' \', c.`city`) as `full_address`, c.`phone`
                FROM `' . _DB_PREFIX_ . 'medbook_doctor_clinic` dc
                INNER JOIN `' . _DB_PREFIX_ . 'medbook_clinic` c
                    ON dc.`id_clinic` = c.`id_clinic`
                WHERE dc.`id_resource` = ' . $resourceId;

        $result = \Db::getInstance()->executeS($sql);

        return is_array($result) ? $result : [];
    }

    /**
     * @return array<int, string>
     */
    private function parseTextOrJson(string $value): array
    {
        if (empty($value)) {
            return [];
        }

        $decoded = json_decode($value, true);
        if (is_array($decoded)) {
            return $decoded;
        }

        // Treat as plain text - return as single-element array
        return [$value];
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
