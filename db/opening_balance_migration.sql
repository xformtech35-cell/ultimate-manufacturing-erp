-- Create Opening Balance Table
-- This table stores opening balances for various accounts

CREATE TABLE IF NOT EXISTS `opening_balance` (
  `balance_id` int(11) NOT NULL AUTO_INCREMENT,
  `account_name` varchar(255) NOT NULL,
  `opening_balance_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `balance_date` date NOT NULL,
  `description` longtext,
  `uid` int(11) NOT NULL COMMENT 'User ID',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`balance_id`),
  KEY `uid` (`uid`),
  KEY `balance_date` (`balance_date`),
  KEY `account_name` (`account_name`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add index for faster searches
ALTER TABLE `opening_balance` ADD INDEX `idx_uid_account_name` (`uid`, `account_name`);
ALTER TABLE `opening_balance` ADD INDEX `idx_uid_balance_date` (`uid`, `balance_date`);

-- Sample insert (optional - for testing)
-- INSERT INTO `opening_balance` (`account_name`, `opening_balance_amount`, `balance_date`, `description`, `uid`, `created_at`)
-- VALUES ('Cash', 50000.00, '2024-01-01', 'Opening balance for cash account', 1, NOW());

-- Make the sidebar permission available to default Admin and Accounts roles.
INSERT INTO `sameepaccounting_permission` (`role_id_fk`, `grp_perm`)
SELECT 1, 'Balance'
WHERE NOT EXISTS (
  SELECT 1 FROM `sameepaccounting_permission`
  WHERE `role_id_fk` = 1 AND `grp_perm` = 'Balance'
);

INSERT INTO `sameepaccounting_permission` (`role_id_fk`, `grp_perm`)
SELECT 5, 'Balance'
WHERE NOT EXISTS (
  SELECT 1 FROM `sameepaccounting_permission`
  WHERE `role_id_fk` = 5 AND `grp_perm` = 'Balance'
);
