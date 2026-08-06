<?php
$conn = new mysqli('localhost', 'root', '', 'ultimate-manufacturing-erp');
if ($conn->connect_error) { die("Conn error"); }

// 1. Delete any leftover individual entries if any
$conn->query("DELETE FROM sameepaccounting_sidebar_menu WHERE permission IN ('Datasheet_Upload', 'Budget_Sheet_Upload')");
echo "Deleted extra rows: " . $conn->affected_rows . "\n";

// 2. Check Engineering parent ID (it is ID 14)
$res = $conn->query("SELECT id FROM sameepaccounting_sidebar_menu WHERE (title = 'Engineering' OR permission = 'Engineering') AND (parent_id = 0 OR parent_id IS NULL OR parent_id = '')");
$row = $res->fetch_assoc();
$eng_id = $row['id'];
echo "Engineering Parent ID: " . $eng_id . "\n";

// 3. Insert Datasheet Upload under ID 14 if not exists
$check1 = $conn->query("SELECT id FROM sameepaccounting_sidebar_menu WHERE permission = 'Datasheet_Upload'");
if ($check1->num_rows == 0) {
    $conn->query("INSERT INTO sameepaccounting_sidebar_menu (parent_id, title, icon, url, permission, sort_order, active_cond) VALUES ($eng_id, 'Datasheet Upload', 'fa fa-file-text-o', 'EngineeringController/datasheets', 'Datasheet_Upload', 1, '{\"controllers\":[\"EngineeringController\"],\"pages\":[\"datasheets\"]}')");
    echo "Inserted Datasheet Upload under parent " . $eng_id . "\n";
}

// 4. Insert Budget Sheet Upload under ID 14 if not exists
$check2 = $conn->query("SELECT id FROM sameepaccounting_sidebar_menu WHERE permission = 'Budget_Sheet_Upload'");
if ($check2->num_rows == 0) {
    $conn->query("INSERT INTO sameepaccounting_sidebar_menu (parent_id, title, icon, url, permission, sort_order, active_cond) VALUES ($eng_id, 'Budget Sheet Upload', 'fa fa-line-chart', 'EngineeringController/budget_sheets', 'Budget_Sheet_Upload', 2, '{\"controllers\":[\"EngineeringController\"],\"pages\":[\"budget_sheets\"]}')");
    echo "Inserted Budget Sheet Upload under parent " . $eng_id . "\n";
}
