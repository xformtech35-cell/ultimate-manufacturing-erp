<?php
$conn = new mysqli('localhost', 'root', '', 'ultimate-manufacturing-erp');
if ($conn->connect_error) { die("Conn failed: " . $conn->connect_error); }

echo "=== APPROVAL MATRIX ENTRIES ===\n";
$res = $conn->query("SELECT * FROM sameepaccounting_approval_matrix");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        print_r($row);
    }
} else {
    echo "Error querying approval_matrix: " . $conn->error . "\n";
}

echo "\n=== SALES ORDER STATUS DISTINCT VALUES & COUNTS ===\n";
$res2 = $conn->query("SELECT status, COUNT(*) as cnt FROM sameepaccounting_salesorder_total GROUP BY status");
if ($res2) {
    while ($row = $res2->fetch_assoc()) {
        echo "Status: " . $row['status'] . " -> Count: " . $row['cnt'] . "\n";
    }
}

echo "\n=== SALESORDER_TOTAL COLUMNS ===\n";
$res3 = $conn->query("SHOW COLUMNS FROM sameepaccounting_salesorder_total");
if ($res3) {
    while ($row = $res3->fetch_assoc()) {
        echo $row['Field'] . " (" . $row['Type'] . ")\n";
    }
}
