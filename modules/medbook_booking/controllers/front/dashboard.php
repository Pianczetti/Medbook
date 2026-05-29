<?php

declare(strict_types=1);

if (!defined('_PS_VERSION_')) {
    exit;
}

class Medbook_bookingDashboardModuleFrontController extends ModuleFrontController
{
    public $ssl = true;
    public $auth = true;

    public function initContent(): void
    {
        parent::initContent();

        $customerId = (int) $this->context->customer->id;
        $langId = (int) $this->context->language->id;

        // Load upcoming appointments
        $upcomingAppointments = $this->loadUpcomingAppointments($customerId, $langId);

        // Load history
        $historyAppointments = $this->loadHistoryAppointments($customerId, $langId);

        // Load documents
        $documents = $this->loadDocuments($customerId);

        $this->context->smarty->assign([
            'upcoming_appointments' => $upcomingAppointments,
            'history_appointments' => $historyAppointments,
            'favorite_doctors' => [],
            'documents' => $documents,
        ]);

        $this->setTemplate('module:medbook_booking/views/templates/front/dashboard.tpl');
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function loadUpcomingAppointments(int $customerId, int $langId): array
    {
        $sql = 'SELECT b.`id_booking`,
                       b.`booking_date`,
                       b.`time_start`,
                       b.`time_end`,
                       b.`status`,
                       b.`visit_type`,
                       b.`reference_code`,
                       rl.`name` as `doctor_name`,
                       c.`name` as `clinic_name`
                FROM `' . _DB_PREFIX_ . 'medbook_booking` b
                LEFT JOIN `' . _DB_PREFIX_ . 'medbook_resource_lang` rl
                    ON rl.`id_resource` = b.`id_resource` AND rl.`id_lang` = ' . $langId . '
                LEFT JOIN `' . _DB_PREFIX_ . 'medbook_doctor_clinic` dc
                    ON dc.`id_resource` = b.`id_resource` AND dc.`is_primary` = 1
                LEFT JOIN `' . _DB_PREFIX_ . 'medbook_clinic` c
                    ON c.`id_clinic` = dc.`id_clinic`
                WHERE b.`id_customer` = ' . $customerId . '
                  AND b.`booking_date` >= CURDATE()
                  AND b.`status` IN (\'confirmed\', \'pending\')
                ORDER BY b.`booking_date` ASC, b.`time_start` ASC
                LIMIT 20';

        $results = \Db::getInstance()->executeS($sql);

        if (!is_array($results)) {
            return [];
        }

        foreach ($results as &$appointment) {
            $date = new \DateTime($appointment['booking_date']);
            $appointment['day'] = $date->format('d');
            $appointment['month'] = $this->getPolishMonth((int) $date->format('n'));
            $appointment['time'] = $appointment['time_start'];
            $appointment['specialization'] = '';
        }

        return $results;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function loadHistoryAppointments(int $customerId, int $langId): array
    {
        $sql = 'SELECT b.`id_booking`,
                       b.`booking_date`,
                       b.`time_start`,
                       b.`status`,
                       b.`reference_code`,
                       rl.`name` as `doctor_name`
                FROM `' . _DB_PREFIX_ . 'medbook_booking` b
                LEFT JOIN `' . _DB_PREFIX_ . 'medbook_resource_lang` rl
                    ON rl.`id_resource` = b.`id_resource` AND rl.`id_lang` = ' . $langId . '
                WHERE b.`id_customer` = ' . $customerId . '
                  AND (b.`booking_date` < CURDATE() OR b.`status` IN (\'completed\', \'cancelled\'))
                ORDER BY b.`booking_date` DESC
                LIMIT 50';

        $results = \Db::getInstance()->executeS($sql);

        if (!is_array($results)) {
            return [];
        }

        foreach ($results as &$appointment) {
            $date = new \DateTime($appointment['booking_date']);
            $appointment['day'] = $date->format('d');
            $appointment['month'] = $this->getPolishMonth((int) $date->format('n'));
            $appointment['time'] = $appointment['time_start'];
            $appointment['specialization'] = '';
        }

        return $results;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function loadDocuments(int $customerId): array
    {
        $sql = 'SELECT doc.`id_document`,
                       doc.`original_name` as `name`,
                       doc.`document_type` as `type`,
                       doc.`date_add` as `date`,
                       doc.`mime_type`
                FROM `' . _DB_PREFIX_ . 'medbook_document` doc
                WHERE doc.`id_customer` = ' . $customerId . '
                ORDER BY doc.`date_add` DESC
                LIMIT 50';

        $results = \Db::getInstance()->executeS($sql);

        if (!is_array($results)) {
            return [];
        }

        foreach ($results as &$doc) {
            $doc['download_url'] = $this->context->link->getModuleLink(
                'medbook_booking',
                'documents',
                ['action' => 'download', 'id' => (int) $doc['id_document']]
            );
        }

        return $results;
    }

    private function getPolishMonth(int $month): string
    {
        $months = [
            1 => 'STY',
            2 => 'LUT',
            3 => 'MAR',
            4 => 'KWI',
            5 => 'MAJ',
            6 => 'CZE',
            7 => 'LIP',
            8 => 'SIE',
            9 => 'WRZ',
            10 => 'PAZ',
            11 => 'LIS',
            12 => 'GRU',
        ];

        return $months[$month] ?? '';
    }
}
