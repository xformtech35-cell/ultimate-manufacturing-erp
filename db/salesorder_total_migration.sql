-- SQL Migration & Recovery Script to fix Sales Order AUTO_INCREMENT & display issues
-- Run this entire script in your phpMyAdmin SQL tab for database `xformtech_gym`

-- 1. DELETE THE DUPLICATES WITH ID = 0
DELETE FROM `sameepaccounting_salesorder_total` WHERE `id` = 0;

-- 2. ENSURE THE TABLE HAS A PRIMARY KEY ON ID (USING STORED PROCEDURE)
DELIMITER $$
DROP PROCEDURE IF EXISTS MakePrimaryKeyIfNotExist$$
CREATE PROCEDURE MakePrimaryKeyIfNotExist()
BEGIN
    IF NOT EXISTS (
        SELECT * FROM information_schema.TABLE_CONSTRAINTS 
        WHERE CONSTRAINT_SCHEMA = DATABASE() 
        AND TABLE_NAME = 'sameepaccounting_salesorder_total' 
        AND CONSTRAINT_TYPE = 'PRIMARY KEY'
    ) THEN
        ALTER TABLE `sameepaccounting_salesorder_total` ADD PRIMARY KEY (`id`);
    END IF;
END$$
DELIMITER ;
CALL MakePrimaryKeyIfNotExist();
DROP PROCEDURE IF EXISTS MakePrimaryKeyIfNotExist;

-- 3. ENABLE AUTO_INCREMENT ON ID COLUMN
ALTER TABLE `sameepaccounting_salesorder_total` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

-- 4. SAFELY ADD MISSING COLUMNS (USING STORED PROCEDURE)
DELIMITER $$
DROP PROCEDURE IF EXISTS AddColumnIfNotExist$$
CREATE PROCEDURE AddColumnIfNotExist(
    IN tableName VARCHAR(64),
    IN columnName VARCHAR(64),
    IN columnDesc VARCHAR(255)
)
BEGIN
    IF NOT EXISTS (
        SELECT * FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE()
        AND TABLE_NAME = tableName
        AND COLUMN_NAME = columnName
    ) THEN
        SET @sql = CONCAT('ALTER TABLE `', tableName, '` ADD COLUMN `', columnName, '` ', columnDesc);
        PREPARE stmt FROM @sql;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;
    END IF;
END$$
DELIMITER ;

CALL AddColumnIfNotExist('sameepaccounting_salesorder_total', 'date', 'date DEFAULT NULL AFTER pay_terms');
CALL AddColumnIfNotExist('sameepaccounting_salesorder_total', 'exp_date', 'date DEFAULT NULL AFTER date');
CALL AddColumnIfNotExist('sameepaccounting_salesorder_total', 'po_date', 'date DEFAULT NULL AFTER exp_date');
CALL AddColumnIfNotExist('sameepaccounting_salesorder_total', 'po_status', 'varchar(50) DEFAULT \'open\' AFTER po_date');
CALL AddColumnIfNotExist('sameepaccounting_salesorder_total', 'attachment', 'varchar(255) DEFAULT NULL AFTER po_status');
CALL AddColumnIfNotExist('sameepaccounting_salesorder_total', 'system', 'varchar(100) DEFAULT NULL AFTER attachment');
CALL AddColumnIfNotExist('sameepaccounting_salesorder_total', 'location', 'varchar(100) DEFAULT NULL AFTER system');
CALL AddColumnIfNotExist('sameepaccounting_salesorder_total', 'capacity', 'varchar(100) DEFAULT NULL AFTER location');
CALL AddColumnIfNotExist('sameepaccounting_salesorder_total', 'project_qty', 'varchar(50) DEFAULT NULL AFTER capacity');
CALL AddColumnIfNotExist('sameepaccounting_salesorder_total', 'oc_number', 'varchar(100) DEFAULT NULL AFTER project_qty');

DROP PROCEDURE IF EXISTS AddColumnIfNotExist;


-- 5. RE-RUN RECOVERY TO ASSIGN UNIQUE AUTO_INCREMENTED IDs
INSERT INTO `sameepaccounting_salesorder_total` (
    `number_fk`,
    `customer_id_fk`,
    `basic_total`,
    `total`,
    `uid`,
    `status`,
    `date`,
    `exp_date`,
    `po_status`
)
SELECT 
    s.number,
    COALESCE(MIN(s.customer_id), 0),
    SUM(s.amount),
    SUM(s.amount + s.sgst + s.cgst + s.igst),
    COALESCE(MIN(s.uid), 1),
    1, -- status = 1 (Draft)
    '2026-07-15', -- default date
    '2026-07-30', -- default exp_date
    'open'
FROM `sameepaccounting_salesorder` s
LEFT JOIN `sameepaccounting_salesorder_total` t ON t.number_fk = s.number
WHERE t.number_fk IS NULL
GROUP BY s.number;
