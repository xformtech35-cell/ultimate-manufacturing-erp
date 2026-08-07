-- Service Module schema updates
-- Add service_type column to local tables if not exists
ALTER TABLE `sameepaccounting_service_order` ADD COLUMN `service_type` VARCHAR(50) NOT NULL DEFAULT 'order';
ALTER TABLE `sameepaccounting_service_order_total` ADD COLUMN `service_type` VARCHAR(50) NOT NULL DEFAULT 'order';

-- Add service_type column to live tables if not exists
ALTER TABLE `uwsaccounting_service_order` ADD COLUMN `service_type` VARCHAR(50) NOT NULL DEFAULT 'order';
ALTER TABLE `uwsaccounting_service_order_total` ADD COLUMN `service_type` VARCHAR(50) NOT NULL DEFAULT 'order';
