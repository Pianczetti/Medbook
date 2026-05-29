<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * @param Medbook_booking $module
 *
 * @return bool
 */
function upgrade_module_1_1_0($module): bool
{
    $sql = (string) file_get_contents(__DIR__ . '/../sql/upgrade-1.1.0.sql');
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

    return true;
}
