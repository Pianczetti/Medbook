<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * @param Medbook_booking $module
 *
 * @return bool
 */
function upgrade_module_1_2_1($module): bool
{
    $sql = (string) file_get_contents(__DIR__ . '/../sql/upgrade-1.2.1.sql');
    $sql = str_replace('PREFIX_', _DB_PREFIX_, $sql);

    foreach (array_filter(array_map('trim', explode(';', $sql))) as $statement) {
        if ($statement !== '') {
            try {
                \Db::getInstance()->execute($statement);
            } catch (\Throwable $e) {
                // Column or index may already exist, skip gracefully
            }
        }
    }

    $module->registerHook('displayProductAdditionalInfo');

    return true;
}
