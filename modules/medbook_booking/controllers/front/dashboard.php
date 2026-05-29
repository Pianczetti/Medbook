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

        // Load favorite doctors
        $favoriteDoctors = $this->loadFavoriteDoctors($customerId, $langId);

        // Load documents
        $documents = $this->loadDocuments($customerId);

        $this->context->smarty->assign([
            'upcoming_appointments' => $upcomingAppointments,
            'history_appointments' => $historyAppointments,
            'favorite_doctors' => $favoriteDoctors,
            'documents' => $documents,
        ]);

        $this->setTemplate('module:medbook_booking/views/templates/front/dashboard.tpl');
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function loadUpcomingAppointments(int $customerId, int $langId): array
    {
        $sql = 'SELECT b.`id_medbook_booking`,
                       b.`booking_date`,
                       b.`time_start`,
                       b.`status`,
                       b.`visit_type`,
                       CONCAT(d.`title`, \' \', d.`firstname`, \' \', d.`lastname`) as `doctor_name`,
                       c.`name` as `clinic_name`
                FROM `' . _DB_PREFIX_ . 'medbook_booking` b
                LEFT JOIN `' . _DB_PREFIX_ . 'medbook_doctor` d ON b.`id_medbook_doctor` = d.`id_medbook_doctor`
                LEFT JOIN `' . _DB_PREFIX_ . 'medbook_clinic` c ON b.`id_medbook_clinic` = c.`id_medbook_clinic`
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
            $appointment['visit_type'] = $appointment['visit_type'] ?? 'stationary';
        }

        return $results;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function loadHistoryAppointments(int $customerId, int $langId): array
    {
        $sql = 'SELECT b.`id_medbook_booking`,
                       b.`booking_date`,
                       b.`time_start`,
                       b.`status`,
                       CONCAT(d.`title`, \' \', d.`firstname`, \' \', d.`lastname`) as `doctor_name`
                FROM `' . _DB_PREFIX_ . 'medbook_booking` b
                LEFT JOIN `' . _DB_PREFIX_ . 'medbook_doctor` d ON b.`id_medbook_doctor` = d.`id_medbook_doctor`
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
    private function loadFavoriteDoctors(int $customerId, int $langId): array
    {
        $sql = 'SELECT d.`id_medbook_doctor` as `id`,
                       CONCAT(d.`title`, \' \', d.`firstname`, \' \', d.`lastname`) as `name`,
                       d.`photo_url`,
                       d.`rating`,
                       d.`reviews_count`,
                       d.`consultation_price` as `price`
                FROM `' . _DB_PREFIX_ . 'medbook_doctor_favorite` f
                INNER JOIN `' . _DB_PREFIX_ . 'medbook_doctor` d ON f.`id_medbook_doctor` = d.`id_medbook_doctor`
                WHERE f.`id_customer` = ' . $customerId . '
                ORDER BY f.`date_add` DESC';

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

    /**
     * @return array<int, array<string, mixed>>
     */
    private function loadDocuments(int $customerId): array
    {
        $sql = 'SELECT doc.`id_medbook_document`,
                       doc.`name`,
                       doc.`type`,
                       doc.`date_add` as `date`,
                       doc.`file_path`
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
                'dashboard',
                ['action' => 'download', 'id' => (int) $doc['id_medbook_document']]
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
