<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * @param Medbook_booking $module
 *
 * @return bool
 */
function upgrade_module_1_2_0($module): bool
{
    $sql = (string) file_get_contents(__DIR__ . '/../sql/upgrade-1.2.0.sql');
    $sql = str_replace(['PREFIX_', 'ENGINE_TYPE'], [_DB_PREFIX_, _MYSQL_ENGINE_], $sql);

    foreach (array_filter(array_map('trim', explode(';', $sql))) as $statement) {
        try {
            \Db::getInstance()->execute($statement);
        } catch (\Throwable $e) {
            // If it is a CREATE TABLE IF NOT EXISTS, failure is unexpected
            // If it is an ALTER TABLE (e.g. column already exists), skip gracefully
            if (stripos($statement, 'ALTER TABLE') === false) {
                return false;
            }
        }
    }

    // Register new hooks
    $module->registerHook(['actionBookingCancelled', 'actionBookingCompleted']);

    // Set new configuration values
    Configuration::updateValue('MEDBOOK_BOOKING_REMINDER_HOURS_BEFORE', 24);
    Configuration::updateValue('MEDBOOK_BOOKING_WAITLIST_NOTIFICATION_HOURS', 4);

    return true;
}
