<?php
$conn = new mysqli('localhost', 'root', '', 'ultimate-manufacturing-erp');
if ($conn->connect_error) { die("Conn error"); }

$conn->query("UPDATE sameepaccounting_sidebar_menu SET parent_id = 0 WHERE id = 14");
echo "Updated Engineering parent_id to 0. Affected rows: " . $conn->affected_rows . "\n";
