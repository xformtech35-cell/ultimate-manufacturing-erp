-- BOM Table Migration
-- Add new columns to sameepaccounting_bom table to support product_name and other fields

ALTER TABLE `sameepaccounting_bom` 
ADD COLUMN `product_name` varchar(255) DEFAULT NULL AFTER `item_name`,
ADD COLUMN `product_code` varchar(100) DEFAULT NULL AFTER `product_name`,
ADD COLUMN `equipment` longtext DEFAULT NULL AFTER `product_code`;

-- Update existing data to move item_name to product_name if needed
UPDATE `sameepaccounting_bom` SET `product_name` = `item_name` WHERE `product_name` IS NULL;

-- Add missing BOM header fields to bom_total table if they don't exist
ALTER TABLE `sameepaccounting_bom_total` 
ADD COLUMN IF NOT EXISTS `system` varchar(255) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS `location` varchar(255) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS `capacity` varchar(255) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS `project_qty` int(11) DEFAULT 0,
ADD COLUMN IF NOT EXISTS `oc_number` varchar(100) DEFAULT NULL;
