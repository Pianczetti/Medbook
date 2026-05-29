<?php
/**
 * MedBook - Medical Booking Platform
 * Install fixtures for default data.
 *
 * @author    MedBook
 * @copyright MedBook
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 */

declare(strict_types=1);

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Installs default MedBook fixture data: specializations, sample clinic, configuration values.
 */
function medbook_install_fixtures(): bool
{
    $db = \Db::getInstance();

    // Install default Polish medical specializations
    $specializations = [
        ['name' => 'Kardiologia', 'slug' => 'kardiologia', 'icon' => 'fa-heartbeat'],
        ['name' => 'Dermatologia', 'slug' => 'dermatologia', 'icon' => 'fa-hand-paper'],
        ['name' => 'Endokrynologia', 'slug' => 'endokrynologia', 'icon' => 'fa-flask'],
        ['name' => 'Gastroenterologia', 'slug' => 'gastroenterologia', 'icon' => 'fa-medkit'],
        ['name' => 'Ginekologia', 'slug' => 'ginekologia', 'icon' => 'fa-female'],
        ['name' => 'Neurologia', 'slug' => 'neurologia', 'icon' => 'fa-brain'],
        ['name' => 'Okulistyka', 'slug' => 'okulistyka', 'icon' => 'fa-eye'],
        ['name' => 'Ortopedia', 'slug' => 'ortopedia', 'icon' => 'fa-bone'],
        ['name' => 'Pediatria', 'slug' => 'pediatria', 'icon' => 'fa-child'],
        ['name' => 'Psychiatria', 'slug' => 'psychiatria', 'icon' => 'fa-comments'],
        ['name' => 'Pulmonologia', 'slug' => 'pulmonologia', 'icon' => 'fa-lungs'],
        ['name' => 'Radiologia', 'slug' => 'radiologia', 'icon' => 'fa-x-ray'],
        ['name' => 'Reumatologia', 'slug' => 'reumatologia', 'icon' => 'fa-hand-holding-medical'],
        ['name' => 'Urologia', 'slug' => 'urologia', 'icon' => 'fa-procedures'],
        ['name' => 'Stomatologia', 'slug' => 'stomatologia', 'icon' => 'fa-tooth'],
        ['name' => 'Laryngologia', 'slug' => 'laryngologia', 'icon' => 'fa-head-side-cough'],
        ['name' => 'Alergologia', 'slug' => 'alergologia', 'icon' => 'fa-allergies'],
        ['name' => 'Onkologia', 'slug' => 'onkologia', 'icon' => 'fa-ribbon'],
        ['name' => 'Medycyna rodzinna', 'slug' => 'medycyna-rodzinna', 'icon' => 'fa-home'],
        ['name' => 'Chirurgia ogolna', 'slug' => 'chirurgia-ogolna', 'icon' => 'fa-cut'],
    ];

    $position = 0;
    foreach ($specializations as $spec) {
        $db->insert('medbook_specialization', [
            'name' => pSQL($spec['name']),
            'slug' => pSQL($spec['slug']),
            'icon' => pSQL($spec['icon']),
            'is_active' => 1,
            'position' => $position++,
        ]);
    }

    // Install sample clinic
    $db->insert('medbook_clinic', [
        'name' => pSQL('Przychodnia MedBook'),
        'address' => pSQL('ul. Przykladowa 1'),
        'city' => pSQL('Warszawa'),
        'postal_code' => pSQL('00-001'),
        'voivodeship' => pSQL('mazowieckie'),
        'lat' => '52.22977000',
        'lng' => '21.01178000',
        'phone' => pSQL('+48 22 123 45 67'),
        'email' => pSQL('kontakt@medbook.pl'),
        'opening_hours_json' => pSQL(json_encode([
            'mon' => '08:00-18:00',
            'tue' => '08:00-18:00',
            'wed' => '08:00-18:00',
            'thu' => '08:00-18:00',
            'fri' => '08:00-16:00',
            'sat' => '09:00-13:00',
            'sun' => '',
        ])),
        'is_active' => 1,
        'date_add' => date('Y-m-d H:i:s'),
        'date_upd' => date('Y-m-d H:i:s'),
    ]);

    // Install default module configuration values
    \Configuration::updateValue('MEDBOOK_DEFAULT_DURATION', 30);
    \Configuration::updateValue('MEDBOOK_DEFAULT_RESOURCE_TYPE', 'doctor');
    \Configuration::updateValue('MEDBOOK_REMINDER_HOURS', 24);
    \Configuration::updateValue('MEDBOOK_BOOKING_CONFIRMATION', 'email');
    \Configuration::updateValue('MEDBOOK_ALLOW_ONLINE_VISITS', 1);
    \Configuration::updateValue('MEDBOOK_INSURANCE_TYPES', 'NFZ,prywatne,pakiet');
    \Configuration::updateValue('MEDBOOK_PLATFORM_MODE', 'medical');

    return true;
}
