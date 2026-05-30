ALTER TABLE `PREFIX_medbook_resource` ADD `id_product` INT UNSIGNED NULL DEFAULT NULL AFTER `is_active`;
ALTER TABLE `PREFIX_medbook_resource` ADD KEY `idx_resource_product` (`id_product`);
