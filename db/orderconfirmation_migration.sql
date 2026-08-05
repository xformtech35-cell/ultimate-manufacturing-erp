-- Create Order Confirmation (OC) tables
-- Run this SQL script in your database to add the necessary tables for Order Confirmation functionality

-- Create orderconfirmation_total table (Header/Summary)
CREATE TABLE IF NOT EXISTS `sameepaccounting_orderconfirmation_total` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `number_fk` varchar(100) DEFAULT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `po_reference` varchar(100) DEFAULT NULL COMMENT 'Purchase Order Reference',
  `date` date DEFAULT NULL,
  `delivery_date` date DEFAULT NULL COMMENT 'Expected Delivery Date',
  `payment_terms` varchar(100) DEFAULT NULL,
  `project_code` varchar(100) DEFAULT NULL,
  `remarks` longtext,
  `sub_total` decimal(15,2) DEFAULT 0.00,
  `tax_amount` decimal(15,2) DEFAULT 0.00,
  `total` decimal(15,2) DEFAULT 0.00,
  `status` int(11) DEFAULT 1 COMMENT '1=Draft, 2=Sent/Confirmed, 3=Accepted, 4=Rejected, 5=Cancelled',
  `uid` int(11) NOT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `number_fk` (`number_fk`),
  KEY `supplier_id` (`supplier_id`),
  KEY `po_reference` (`po_reference`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Create orderconfirmation table (Line items)
CREATE TABLE IF NOT EXISTS `sameepaccounting_orderconfirmation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `number` varchar(100) DEFAULT NULL,
  `description` longtext,
  `hsn_code` varchar(50) DEFAULT NULL,
  `quantity` decimal(10,2) DEFAULT 0.00,
  `unit` varchar(20) DEFAULT NULL,
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

-- Add OrderConfirmation permission to the permission table
INSERT INTO `sameepaccounting_permission` (`permission_id`, `grp_perm`, `is_assign`) VALUES 
(NULL, 'OrderConfirmation', 1);

