<?php
$conn = new mysqli('localhost', 'root', '', 'ultimate-manufacturing-erp');
if ($conn->connect_error) { die("Conn error"); }

$conn->query("UPDATE sameepaccounting_sidebar_menu SET parent_id = NULL WHERE parent_id = 0 OR parent_id = '0'");
echo "Updated root parent_id to NULL. Affected rows: " . $conn->affected_rows . "\n";
