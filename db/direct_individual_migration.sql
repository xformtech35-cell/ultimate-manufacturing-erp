-- Direct Individual Master table
-- Run this script in your database (sameep_erp)

CREATE TABLE IF NOT EXISTS `sameepaccounting_direct_individual` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `employee_name` varchar(150) NOT NULL,
  `type` varchar(50) NOT NULL,
  `uid` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
