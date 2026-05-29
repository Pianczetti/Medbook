CREATE TABLE IF NOT EXISTS `PREFIX_medbook_resource` (
    `id_resource` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `resource_type` ENUM('doctor','table','service','custom') NOT NULL DEFAULT 'doctor',
    `capacity` INT(11) UNSIGNED NOT NULL DEFAULT 1,
    `duration_minutes` INT(11) UNSIGNED NOT NULL DEFAULT 30,
    `base_price` DECIMAL(10,2) NOT NULL DEFAULT 0,
    `min_duration_minutes` INT(11) UNSIGNED NOT NULL DEFAULT 0,
    `max_duration_minutes` INT(11) UNSIGNED NOT NULL DEFAULT 0,
    `buffer_minutes` INT(11) UNSIGNED NOT NULL DEFAULT 0,
    `is_active` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
    `id_product` INT UNSIGNED NULL DEFAULT NULL,
    `position` INT(11) UNSIGNED NOT NULL DEFAULT 0,
    `color` VARCHAR(7) NOT NULL DEFAULT '#3498db',
    `date_add` DATETIME NOT NULL,
    `date_upd` DATETIME NOT NULL,
    PRIMARY KEY (`id_resource`),
    INDEX `idx_medbook_resource_active` (`is_active`),
    INDEX `idx_resource_product` (`id_product`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `PREFIX_medbook_resource_lang` (
    `id_resource` INT(11) UNSIGNED NOT NULL,
    `id_lang` INT(11) UNSIGNED NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `description` TEXT,
    PRIMARY KEY (`id_resource`, `id_lang`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `PREFIX_medbook_schedule` (
    `id_schedule` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_resource` INT(11) UNSIGNED NOT NULL,
    `day_of_week` TINYINT(1) UNSIGNED DEFAULT NULL,
    `specific_date` DATE DEFAULT NULL,
    `time_start` TIME NOT NULL,
    `time_end` TIME NOT NULL,
    `is_available` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
    `date_add` DATETIME NOT NULL,
    PRIMARY KEY (`id_schedule`),
    INDEX `idx_medbook_schedule_resource` (`id_resource`),
    INDEX `idx_medbook_schedule_day` (`day_of_week`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `PREFIX_medbook_booking` (
    `id_booking` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_resource` INT(11) UNSIGNED NOT NULL,
    `id_customer` INT(11) UNSIGNED DEFAULT NULL,
    `id_order` INT(11) UNSIGNED DEFAULT NULL,
    `booking_date` DATE NOT NULL,
    `time_start` TIME NOT NULL,
    `time_end` TIME NOT NULL,
    `status` ENUM('pending','confirmed','cancelled','completed','no_show') NOT NULL DEFAULT 'pending',
    `no_show` TINYINT(1) NOT NULL DEFAULT 0,
    `customer_name` VARCHAR(128) NOT NULL,
    `customer_email` VARCHAR(128) NOT NULL,
    `customer_phone` VARCHAR(32) DEFAULT NULL,
    `notes` TEXT DEFAULT NULL,
    `admin_notes` TEXT DEFAULT NULL,
    `total_price` DECIMAL(10,2) NOT NULL DEFAULT 0,
    `deposit_paid` DECIMAL(10,2) NOT NULL DEFAULT 0,
    `id_employee` INT(11) UNSIGNED DEFAULT NULL,
    `reference_code` VARCHAR(16) NOT NULL,
    `visit_type` ENUM('stacjonarna','online') NOT NULL DEFAULT 'stacjonarna',
    `insurance_type` ENUM('NFZ','prywatne','pakiet') NOT NULL DEFAULT 'prywatne',
    `video_call_url` VARCHAR(512) DEFAULT NULL,
    `date_add` DATETIME NOT NULL,
    `date_upd` DATETIME NOT NULL,
    `confirmed_at` DATETIME DEFAULT NULL,
    PRIMARY KEY (`id_booking`),
    UNIQUE KEY `uniq_medbook_reference` (`reference_code`),
    INDEX `idx_medbook_resource_date` (`id_resource`, `booking_date`),
    INDEX `idx_medbook_status` (`status`),
    INDEX `idx_medbook_customer` (`id_customer`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `PREFIX_medbook_blocked_date` (
    `id_blocked_date` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_resource` INT(11) UNSIGNED DEFAULT NULL,
    `blocked_date` DATE NOT NULL,
    `reason` VARCHAR(255) DEFAULT NULL,
    `date_add` DATETIME NOT NULL,
    PRIMARY KEY (`id_blocked_date`),
    INDEX `idx_medbook_blocked_resource` (`id_resource`, `blocked_date`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `PREFIX_medbook_price_rule` (
    `id_price_rule` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_resource` INT(11) UNSIGNED NOT NULL,
    `name` VARCHAR(128) NOT NULL,
    `date_from` DATE DEFAULT NULL,
    `date_to` DATE DEFAULT NULL,
    `day_of_week` TINYINT(1) UNSIGNED DEFAULT NULL,
    `time_from` TIME DEFAULT NULL,
    `time_to` TIME DEFAULT NULL,
    `modifier_type` ENUM('percent','fixed') NOT NULL,
    `modifier_value` DECIMAL(10,2) NOT NULL,
    `priority` INT(11) NOT NULL DEFAULT 0,
    `is_active` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
    `date_add` DATETIME NOT NULL,
    PRIMARY KEY (`id_price_rule`),
    INDEX `idx_medbook_price_rule_resource` (`id_resource`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `PREFIX_medbook_deposit_rule` (
    `id_deposit_rule` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_resource` INT(11) UNSIGNED DEFAULT NULL,
    `deposit_type` ENUM('percent','fixed') NOT NULL,
    `deposit_value` DECIMAL(10,2) NOT NULL,
    `is_active` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
    `date_add` DATETIME NOT NULL,
    PRIMARY KEY (`id_deposit_rule`),
    INDEX `idx_medbook_deposit_rule_resource` (`id_resource`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `PREFIX_medbook_addon` (
    `id_addon` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_resource` INT(11) UNSIGNED DEFAULT NULL,
    `price` DECIMAL(10,2) NOT NULL DEFAULT 0,
    `is_active` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
    `position` INT(11) UNSIGNED NOT NULL DEFAULT 0,
    `date_add` DATETIME NOT NULL,
    PRIMARY KEY (`id_addon`),
    INDEX `idx_medbook_addon_resource` (`id_resource`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `PREFIX_medbook_addon_lang` (
    `id_addon` INT(11) UNSIGNED NOT NULL,
    `id_lang` INT(11) UNSIGNED NOT NULL,
    `name` VARCHAR(128) NOT NULL,
    `description` TEXT DEFAULT NULL,
    PRIMARY KEY (`id_addon`, `id_lang`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `PREFIX_medbook_booking_addon` (
    `id_booking` INT(11) UNSIGNED NOT NULL,
    `id_addon` INT(11) UNSIGNED NOT NULL,
    `price` DECIMAL(10,2) NOT NULL DEFAULT 0,
    PRIMARY KEY (`id_booking`, `id_addon`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `PREFIX_medbook_refund_rule` (
    `id_refund_rule` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_resource` INT(11) UNSIGNED DEFAULT NULL,
    `hours_before` INT(11) UNSIGNED NOT NULL,
    `refund_percent` DECIMAL(5,2) NOT NULL,
    `date_add` DATETIME NOT NULL,
    PRIMARY KEY (`id_refund_rule`),
    INDEX `idx_medbook_refund_rule_resource` (`id_resource`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `PREFIX_medbook_cart_data` (
    `id_cart_data` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_cart` INT(11) UNSIGNED NOT NULL,
    `id_resource` INT(11) UNSIGNED NOT NULL,
    `booking_date` DATE NOT NULL,
    `time_start` TIME NOT NULL,
    `time_end` TIME NOT NULL,
    `addons_json` TEXT DEFAULT NULL,
    `total_price` DECIMAL(10,2) NOT NULL DEFAULT 0,
    `deposit_amount` DECIMAL(10,2) NOT NULL DEFAULT 0,
    `date_add` DATETIME NOT NULL,
    PRIMARY KEY (`id_cart_data`),
    INDEX `idx_medbook_cart_data_cart` (`id_cart`),
    INDEX `idx_medbook_cart_data_resource` (`id_resource`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `PREFIX_medbook_confirm_token` (
    `id_token` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_booking` INT(11) UNSIGNED NOT NULL,
    `token` VARCHAR(64) NOT NULL,
    `action` VARCHAR(16) NOT NULL,
    `expires_at` DATETIME NOT NULL,
    `used_at` DATETIME DEFAULT NULL,
    `date_add` DATETIME NOT NULL,
    PRIMARY KEY (`id_token`),
    UNIQUE KEY `uniq_medbook_confirm_token` (`token`),
    INDEX `idx_medbook_confirm_token_booking` (`id_booking`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `PREFIX_medbook_reminder` (
    `id_reminder` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_booking` INT(11) UNSIGNED NOT NULL,
    `reminder_type` VARCHAR(32) NOT NULL DEFAULT 'email',
    `sent_at` DATETIME NOT NULL,
    `status` VARCHAR(16) NOT NULL DEFAULT 'sent',
    PRIMARY KEY (`id_reminder`),
    INDEX `idx_medbook_reminder_booking` (`id_booking`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `PREFIX_medbook_recurrence_config` (
    `id_recurrence_config` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_resource` INT(11) UNSIGNED NOT NULL,
    `interval_days` INT(11) NOT NULL DEFAULT 30,
    `tolerance_days` INT(11) NOT NULL DEFAULT 3,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `date_add` DATETIME NOT NULL,
    PRIMARY KEY (`id_recurrence_config`),
    INDEX `idx_medbook_recurrence_config_resource` (`id_resource`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `PREFIX_medbook_suggested_visit` (
    `id_suggested_visit` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_booking` INT(11) UNSIGNED NOT NULL,
    `id_resource` INT(11) UNSIGNED NOT NULL,
    `id_customer` INT(11) UNSIGNED NOT NULL,
    `suggested_date_from` DATE NOT NULL,
    `suggested_date_to` DATE NOT NULL,
    `status` VARCHAR(16) NOT NULL DEFAULT 'pending',
    `date_add` DATETIME NOT NULL,
    PRIMARY KEY (`id_suggested_visit`),
    INDEX `idx_medbook_suggested_visit_booking` (`id_booking`),
    INDEX `idx_medbook_suggested_visit_customer` (`id_customer`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `PREFIX_medbook_waitlist` (
    `id_waitlist` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_resource` INT(11) UNSIGNED NOT NULL,
    `id_customer` INT(11) UNSIGNED DEFAULT NULL,
    `customer_name` VARCHAR(128) NOT NULL,
    `customer_email` VARCHAR(255) NOT NULL,
    `customer_phone` VARCHAR(32) DEFAULT NULL,
    `preferred_dates_json` TEXT NOT NULL,
    `preferred_times_json` TEXT DEFAULT NULL,
    `is_priority` TINYINT(1) NOT NULL DEFAULT 0,
    `status` VARCHAR(16) NOT NULL DEFAULT 'active',
    `notified_at` DATETIME DEFAULT NULL,
    `date_add` DATETIME NOT NULL,
    PRIMARY KEY (`id_waitlist`),
    INDEX `idx_medbook_waitlist_resource` (`id_resource`),
    INDEX `idx_medbook_waitlist_status` (`status`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `PREFIX_medbook_specialization` (
    `id_specialization` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(128) NOT NULL,
    `slug` VARCHAR(128) NOT NULL,
    `icon` VARCHAR(64) DEFAULT NULL,
    `is_active` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
    `position` INT(11) UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (`id_specialization`),
    INDEX `idx_medbook_specialization_active` (`is_active`),
    INDEX `idx_medbook_specialization_slug` (`slug`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `PREFIX_medbook_clinic` (
    `id_clinic` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `address` VARCHAR(255) NOT NULL,
    `city` VARCHAR(128) NOT NULL,
    `postal_code` VARCHAR(6) NOT NULL,
    `voivodeship` VARCHAR(64) DEFAULT NULL,
    `lat` DECIMAL(10,8) DEFAULT NULL,
    `lng` DECIMAL(11,8) DEFAULT NULL,
    `phone` VARCHAR(32) DEFAULT NULL,
    `email` VARCHAR(128) DEFAULT NULL,
    `opening_hours_json` TEXT DEFAULT NULL,
    `is_active` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
    `date_add` DATETIME NOT NULL,
    `date_upd` DATETIME NOT NULL,
    PRIMARY KEY (`id_clinic`),
    INDEX `idx_medbook_clinic_active` (`is_active`),
    INDEX `idx_medbook_clinic_city` (`city`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `PREFIX_medbook_doctor_profile` (
    `id_doctor_profile` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_resource` INT(11) UNSIGNED NOT NULL,
    `id_specialization` INT(11) UNSIGNED DEFAULT NULL,
    `education` TEXT DEFAULT NULL,
    `experience_years` INT(11) UNSIGNED NOT NULL DEFAULT 0,
    `languages` VARCHAR(255) DEFAULT NULL,
    `certifications` TEXT DEFAULT NULL,
    `photo` VARCHAR(255) DEFAULT NULL,
    `nip` VARCHAR(10) DEFAULT NULL,
    `pwz_number` VARCHAR(7) DEFAULT NULL,
    `consultation_online` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
    `consultation_inperson` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
    `date_add` DATETIME NOT NULL,
    `date_upd` DATETIME NOT NULL,
    PRIMARY KEY (`id_doctor_profile`),
    INDEX `idx_medbook_doctor_profile_resource` (`id_resource`),
    INDEX `idx_medbook_doctor_profile_specialization` (`id_specialization`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `PREFIX_medbook_doctor_clinic` (
    `id_doctor_clinic` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_resource` INT(11) UNSIGNED NOT NULL,
    `id_clinic` INT(11) UNSIGNED NOT NULL,
    `room_number` VARCHAR(32) DEFAULT NULL,
    `is_primary` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (`id_doctor_clinic`),
    INDEX `idx_medbook_doctor_clinic_resource` (`id_resource`),
    INDEX `idx_medbook_doctor_clinic_clinic` (`id_clinic`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `PREFIX_medbook_document` (
    `id_document` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_customer` INT(11) UNSIGNED NOT NULL,
    `id_booking` INT(11) UNSIGNED DEFAULT NULL,
    `document_type` ENUM('prescription','referral','result','other') NOT NULL DEFAULT 'other',
    `filename` VARCHAR(255) NOT NULL,
    `original_name` VARCHAR(255) NOT NULL,
    `mime_type` VARCHAR(64) NOT NULL,
    `file_size` INT(11) UNSIGNED NOT NULL DEFAULT 0,
    `uploaded_by` ENUM('patient','doctor','admin') NOT NULL DEFAULT 'patient',
    `date_add` DATETIME NOT NULL,
    PRIMARY KEY (`id_document`),
    INDEX `idx_medbook_document_customer` (`id_customer`),
    INDEX `idx_medbook_document_booking` (`id_booking`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8mb4;
