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
        $specializations = $this->getSpecializations($langId);

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
    private function searchDoctors(int $langId, int $specializationId, string $city, string $availability, string $visitType, string $insurance): array
    {
        $where = ['d.`active` = 1'];
        $joins = '';

        if ($specializationId > 0) {
            $joins .= ' INNER JOIN `' . _DB_PREFIX_ . 'medbook_doctor_specialization` ds
                        ON d.`id_medbook_doctor` = ds.`id_medbook_doctor`
                        AND ds.`id_medbook_specialization` = ' . $specializationId;
        }

        if ($city !== '') {
            $joins .= ' INNER JOIN `' . _DB_PREFIX_ . 'medbook_doctor_clinic` dc
                        ON d.`id_medbook_doctor` = dc.`id_medbook_doctor`
                        INNER JOIN `' . _DB_PREFIX_ . 'medbook_clinic` c
                        ON dc.`id_medbook_clinic` = c.`id_medbook_clinic`';
            $where[] = 'c.`city` LIKE \'%' . pSQL($city) . '%\'';
        }

        $sql = 'SELECT d.`id_medbook_doctor` as `id`,
                       CONCAT(d.`title`, \' \', d.`firstname`, \' \', d.`lastname`) as `name`,
                       d.`photo_url`,
                       d.`rating`,
                       d.`reviews_count`,
                       d.`consultation_price` as `price`
                FROM `' . _DB_PREFIX_ . 'medbook_doctor` d
                ' . $joins . '
                WHERE ' . implode(' AND ', $where) . '
                ORDER BY d.`rating` DESC
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
            $doctor['specialization'] = '';
            $doctor['next_slot'] = '';
            $doctor['city'] = $city;
        }

        return $results;
    }
}
