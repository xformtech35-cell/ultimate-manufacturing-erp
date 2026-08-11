<?php
$conn = new mysqli('localhost', 'root', '', 'ultimate-manufacturing-erp');
if ($conn->connect_error) { die("Conn error: " . $conn->connect_error); }

$tables = ['sameepaccounting_engineering_datasheets', 'sameepaccounting_engineering_budgets', 'sameepaccounting_item_delete_requests'];
foreach ($tables as $t) {
    $res = $conn->query("SHOW TABLES LIKE '$t'");
    echo "Table '$t' exists: " . ($res->num_rows > 0 ? "YES" : "NO") . "\n";
}
