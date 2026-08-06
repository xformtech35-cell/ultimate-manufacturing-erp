<?php
/**
 * Migration Script: Engineering Tables & Permissions
 * Run via CLI: C:\xampp\php\php.exe db/engineering_sheets_tables.php
 */

$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'ultimate-manufacturing-erp';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 1. Create sameepaccounting_engineering_datasheets table
$sql1 = "CREATE TABLE IF NOT EXISTS `sameepaccounting_engineering_datasheets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `salesorder_id_fk` int(11) NOT NULL,
  `so_number` varchar(100) NOT NULL,
  `equipment_name` varchar(255) DEFAULT NULL,
  `bom_item_id_fk` int(11) DEFAULT NULL,
  `file_name` varchar(255) NOT NULL,
  `original_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_type` varchar(20) NOT NULL,
  `file_size` int(11) NOT NULL,
  `uploaded_by` varchar(100) DEFAULT NULL,
  `uploaded_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `remarks` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `salesorder_id_fk` (`salesorder_id_fk`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

if ($conn->query($sql1) === TRUE) {
    echo "Table sameepaccounting_engineering_datasheets created or already exists.\n";
} else {
    echo "Error creating datasheets table: " . $conn->error . "\n";
}

// 2. Create sameepaccounting_engineering_budgets table
$sql2 = "CREATE TABLE IF NOT EXISTS `sameepaccounting_engineering_budgets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `salesorder_id_fk` int(11) NOT NULL,
  `so_number` varchar(100) NOT NULL,
  `budget_title` varchar(255) DEFAULT NULL,
  `file_name` varchar(255) NOT NULL,
  `original_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_type` varchar(20) NOT NULL,
  `file_size` int(11) NOT NULL,
  `uploaded_by` varchar(100) DEFAULT NULL,
  `uploaded_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `remarks` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `salesorder_id_fk` (`salesorder_id_fk`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

if ($conn->query($sql2) === TRUE) {
    echo "Table sameepaccounting_engineering_budgets created or already exists.\n";
} else {
    echo "Error creating budgets table: " . $conn->error . "\n";
}

$conn->close();
echo "Migration complete.\n";
