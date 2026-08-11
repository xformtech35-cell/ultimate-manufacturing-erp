<?php
/**
 * Migration Script to sync Live Database (xformtech_employee) tables & sidebar menus
 */

$host = 'p3nlmysql7plsk.secureserver.net';
$user = 'uwsenvirotech';
$pass = '754br8~rO';
$db   = 'xformtech_employee';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Live Connection failed: " . $conn->connect_error . "\n");
}
echo "Connected to Live Database: $db\n";

// 1. Create uwsaccounting_engineering_datasheets
$sql1 = "CREATE TABLE IF NOT EXISTS `uwsaccounting_engineering_datasheets` (
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
    echo "Live Table uwsaccounting_engineering_datasheets ready.\n";
} else {
    echo "Error creating live datasheets table: " . $conn->error . "\n";
}

// 2. Create uwsaccounting_engineering_budgets
$sql2 = "CREATE TABLE IF NOT EXISTS `uwsaccounting_engineering_budgets` (
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
    echo "Live Table uwsaccounting_engineering_budgets ready.\n";
} else {
    echo "Error creating live budgets table: " . $conn->error . "\n";
}

// 3. Inspect Engineering parent menu on Live sidebar_menu
$res = $conn->query("SELECT id, parent_id, title FROM uwsaccounting_sidebar_menu WHERE (title = 'Engineering' OR permission = 'Engineering') AND (parent_id IS NULL OR parent_id = 0 OR parent_id = '')");

if ($res && $res->num_rows > 0) {
    $parent_row = $res->fetch_assoc();
    $eng_live_id = $parent_row['id'];
    echo "Live Engineering Parent ID: $eng_live_id\n";
    // Ensure parent_id is NULL for root Engineering
    $conn->query("UPDATE uwsaccounting_sidebar_menu SET parent_id = NULL WHERE id = $eng_live_id");
} else {
    // Insert top-level Engineering menu
    $conn->query("INSERT INTO uwsaccounting_sidebar_menu (parent_id, title, icon, url, permission, sort_order, active_cond) VALUES (NULL, 'Engineering', 'fa fa-sitemap', NULL, 'Engineering', 4, '{\"controllers\":[\"BomController\",\"EngineeringController\"]}')");
    $eng_live_id = $conn->insert_id;
    echo "Inserted Live Engineering Parent ID: $eng_live_id\n";
}

// Remove any duplicate top-level Engineering rows on live
$conn->query("DELETE FROM uwsaccounting_sidebar_menu WHERE (title = 'Engineering' OR permission = 'Engineering') AND id != $eng_live_id");
echo "Cleaned duplicate Engineering parent rows on live: " . $conn->affected_rows . "\n";

// 4. Update parent_id of all sub-items under Engineering to $eng_live_id
$conn->query("UPDATE uwsaccounting_sidebar_menu SET parent_id = $eng_live_id WHERE permission IN ('Bom', 'BOM_Approvals', 'Datasheet_Upload', 'Budget_Sheet_Upload')");
echo "Updated submenus parent_id to $eng_live_id. Affected: " . $conn->affected_rows . "\n";

// 5. Ensure Datasheet Upload child menu exists
$chk1 = $conn->query("SELECT id FROM uwsaccounting_sidebar_menu WHERE permission = 'Datasheet_Upload'");
if ($chk1->num_rows == 0) {
    $conn->query("INSERT INTO uwsaccounting_sidebar_menu (parent_id, title, icon, url, permission, sort_order, active_cond) VALUES ($eng_live_id, 'Datasheet Upload', 'fa fa-file-text-o', 'EngineeringController/datasheets', 'Datasheet_Upload', 1, '{\"controllers\":[\"EngineeringController\"],\"pages\":[\"datasheets\"]}')");
    echo "Inserted Datasheet Upload under live parent $eng_live_id\n";
}

// 6. Ensure Budget Sheet Upload child menu exists
$chk2 = $conn->query("SELECT id FROM uwsaccounting_sidebar_menu WHERE permission = 'Budget_Sheet_Upload'");
if ($chk2->num_rows == 0) {
    $conn->query("INSERT INTO uwsaccounting_sidebar_menu (parent_id, title, icon, url, permission, sort_order, active_cond) VALUES ($eng_live_id, 'Budget Sheet Upload', 'fa fa-line-chart', 'EngineeringController/budget_sheets', 'Budget_Sheet_Upload', 2, '{\"controllers\":[\"EngineeringController\"],\"pages\":[\"budget_sheets\"]}')");
    echo "Inserted Budget Sheet Upload under live parent $eng_live_id\n";
}

// 7. Auto-assign permissions to Admin role (role_id 1) in uwsaccounting_permission
$perms_to_add = ['Engineering', 'Bom', 'BOM_Approvals', 'Datasheet_Upload', 'Budget_Sheet_Upload'];
foreach ($perms_to_add as $p) {
    $chk_p = $conn->query("SELECT * FROM uwsaccounting_permission WHERE role_id_fk = 1 AND grp_perm = '$p'");
    if ($chk_p->num_rows == 0) {
        $conn->query("INSERT INTO uwsaccounting_permission (role_id_fk, grp_perm) VALUES (1, '$p')");
        echo "Granted permission '$p' to Admin on live.\n";
    }
}

$conn->close();
echo "Live DB Migration Complete!\n";
