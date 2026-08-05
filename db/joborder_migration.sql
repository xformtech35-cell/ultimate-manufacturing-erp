-- Create JobOrder tables
-- Run this SQL script in your database to add the necessary tables for Job Order functionality

-- Create joborder_total table (Header/Summary)
CREATE TABLE IF NOT EXISTS `sameepaccounting_joborder_total` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `number_fk` varchar(100) DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `payment_terms` int(11) DEFAULT NULL,
  `project_code` int(11) DEFAULT NULL,
  `remarks` longtext,
  `total` decimal(15,2) DEFAULT 0.00,
  `status` int(11) DEFAULT 1 COMMENT '1=Draft, 2=Sent',
  `uid` int(11) NOT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `number_fk` (`number_fk`),
  KEY `customer_id` (`customer_id`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Create joborder table (Line items)
CREATE TABLE IF NOT EXISTS `sameepaccounting_joborder` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `number` varchar(100) DEFAULT NULL,
  `item_code` varchar(50) DEFAULT NULL,
  `equipment` longtext,
  `quantity` decimal(10,2) DEFAULT 0.00,
  `unit` varchar(20) DEFAULT NULL,
  `tag_no` varchar(50) DEFAULT NULL,
  `scope` longtext,
  `stores_remark` varchar(255) DEFAULT NULL,
  `remark` longtext,
  `hsn_code` varchar(20) DEFAULT NULL,
  `unit_price` decimal(15,2) DEFAULT 0.00,
  `tax_rate` decimal(5,2) DEFAULT 0.00,
  `tax_amount` decimal(15,2) DEFAULT 0.00,
  `amount` decimal(15,2) DEFAULT 0.00,
  `uid` int(11) NOT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `number` (`number`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Add sub_total and tax_amount columns to joborder_total table
ALTER TABLE `sameepaccounting_joborder_total` 
ADD COLUMN `sub_total` decimal(15,2) DEFAULT 0.00 AFTER `total`,
ADD COLUMN `tax_amount` decimal(15,2) DEFAULT 0.00 AFTER `sub_total`;

-- Add JobOrder permission to the permission table
-- First, get the permission_id for the next JobOrder permission
INSERT INTO `sameepaccounting_permission` (`permission_id`, `grp_perm`, `is_assign`) VALUES 
(NULL, 'JobOrder', 1);

-- Add JobOrder permission to admin or all roles (optional - uncomment based on your needs)
-- INSERT INTO `permission` (`permission_id`, `grp_perm`, `role_id_fk`) 
-- SELECT NULL, 'JobOrder', `role_id` FROM `sameepaccounting_role` WHERE role_name = 'Admin';
