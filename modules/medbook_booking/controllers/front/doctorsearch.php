<?php

declare(strict_types=1);

if (!defined('_PS_VERSION_')) {
    exit;
}

class Medbook_bookingDoctorsearchModuleFrontController extends ModuleFrontController
{
    public $ssl = true;

    public function initContent(): void
    {
        parent::initContent();

        $langId = (int) $this->context->language->id;

        // Get filter parameters
        $specializationId = (int) Tools::getValue('specialization', 0);
        $city = (string) Tools::getValue('city', '');
        $availability = (string) Tools::getValue('availability', '');
        $visitType = (string) Tools::getValue('visit_type', '');
        $insurance = (string) Tools::getValue('insurance', '');

        // Load specializations for the filter dropdown
        $specializations = $this->getSpecializations();

        // Perform search if any filter is set
        $doctors = [];
        $searchPerformed = false;

        if ($specializationId || $city || $availability || $visitType || $insurance) {
            $searchPerformed = true;
            $doctors = $this->searchDoctors($langId, $specializationId, $city, $availability, $visitType, $insurance);
        }

        $this->context->smarty->assign([
            'specializations' => $specializations,
            'doctors' => $doctors,
            'search_performed' => $searchPerformed,
            'selected_specialization' => $specializationId,
            'selected_city' => $city,
            'selected_date' => $availability,
            'selected_visit_type' => $visitType,
            'selected_insurance' => $insurance,
        ]);

        $this->setTemplate('module:medbook_booking/views/templates/front/doctor-search.tpl');
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
    private function searchDoctors(int $langId, int $specializationId, string $city, string $availability, string $visitType, string $insurance): array
    {
        $where = ['r.`is_active` = 1', 'r.`resource_type` = \'doctor\''];
        $joins = '';

        if ($specializationId > 0) {
            $where[] = 'dp.`id_specialization` = ' . $specializationId;
        }

        if ($city !== '') {
            $joins .= ' INNER JOIN `' . _DB_PREFIX_ . 'medbook_doctor_clinic` dc
                        ON dc.`id_resource` = r.`id_resource`
                        INNER JOIN `' . _DB_PREFIX_ . 'medbook_clinic` c
                        ON dc.`id_clinic` = c.`id_clinic`';
            $where[] = 'c.`city` LIKE \'%' . pSQL($city) . '%\'';
        }

        if ($visitType === 'online') {
            $where[] = 'dp.`consultation_online` = 1';
        } elseif ($visitType === 'stacjonarna') {
            $where[] = 'dp.`consultation_inperson` = 1';
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
                ' . $joins . '
                WHERE ' . implode(' AND ', $where) . '
                ORDER BY rl.`name` ASC
                LIMIT 50';

        $results = \Db::getInstance()->executeS($sql);

        if (!is_array($results)) {
            return [];
        }

        // Enrich with profile URLs
        foreach ($results as &$doctor) {
            $doctor['profile_url'] = $this->context->link->getModuleLink(
                'medbook_booking',
                'doctorprofile',
                ['id' => (int) $doctor['id']]
            );
            $doctor['specialization'] = $doctor['specialization_name'] ?? '';
            $doctor['next_slot'] = '';
            $doctor['city'] = $city;
        }

        return $results;
    }
}
