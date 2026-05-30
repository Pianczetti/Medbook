<?php

declare(strict_types=1);

namespace MedBook\Booking\Form;

use Configuration;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;

class SettingsDataProvider implements FormDataProviderInterface
{
    public function getData(): array
    {
        return [
            'confirm_mode' => (string) Configuration::get('MEDBOOK_BOOKING_CONFIRM_MODE') ?: 'auto',
            'max_days_ahead' => (int) Configuration::get('MEDBOOK_BOOKING_MAX_DAYS_AHEAD') ?: 30,
            'min_hours_advance' => (int) Configuration::get('MEDBOOK_BOOKING_MIN_HOURS_ADVANCE') ?: 2,
            'allow_guests' => (bool) Configuration::get('MEDBOOK_BOOKING_ALLOW_GUESTS'),
            'slot_duration' => (int) Configuration::get('MEDBOOK_BOOKING_SLOT_DURATION') ?: 30,
            'email_notifications' => (bool) Configuration::get('MEDBOOK_BOOKING_EMAIL_NOTIFICATIONS'),
            'pricing_mode' => (string) Configuration::get('MEDBOOK_BOOKING_PRICING_MODE') ?: 'highest_priority',
            'show_prices' => (bool) Configuration::get('MEDBOOK_BOOKING_SHOW_PRICES'),
        ];
    }

    public function setData(array $data): array
    {
        $errors = [];

        if (isset($data['max_days_ahead']) && (int) $data['max_days_ahead'] < 1) {
            $errors[] = [
                'key' => 'Max days ahead must be a positive number.',
                'domain' => 'Modules.Medbookbooking.Admin',
                'parameters' => [],
            ];
        }

        if (isset($data['min_hours_advance']) && (int) $data['min_hours_advance'] < 0) {
            $errors[] = [
                'key' => 'Min hours in advance cannot be negative.',
                'domain' => 'Modules.Medbookbooking.Admin',
                'parameters' => [],
            ];
        }

        if (!empty($errors)) {
            return $errors;
        }

        Configuration::updateValue('MEDBOOK_BOOKING_CONFIRM_MODE', (string) ($data['confirm_mode'] ?? 'auto'));
        Configuration::updateValue('MEDBOOK_BOOKING_MAX_DAYS_AHEAD', (int) ($data['max_days_ahead'] ?? 30));
        Configuration::updateValue('MEDBOOK_BOOKING_MIN_HOURS_ADVANCE', (int) ($data['min_hours_advance'] ?? 2));
        Configuration::updateValue('MEDBOOK_BOOKING_ALLOW_GUESTS', (bool) ($data['allow_guests'] ?? false));
        Configuration::updateValue('MEDBOOK_BOOKING_SLOT_DURATION', (int) ($data['slot_duration'] ?? 30));
        Configuration::updateValue('MEDBOOK_BOOKING_EMAIL_NOTIFICATIONS', (bool) ($data['email_notifications'] ?? true));
        Configuration::updateValue('MEDBOOK_BOOKING_PRICING_MODE', (string) ($data['pricing_mode'] ?? 'highest_priority'));
        Configuration::updateValue('MEDBOOK_BOOKING_SHOW_PRICES', (bool) ($data['show_prices'] ?? true));

        return [];
    }
}
