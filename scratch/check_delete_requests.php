<?php
$conn = new mysqli('localhost', 'root', '', 'ultimate-manufacturing-erp');
if ($conn->connect_error) { die("Conn error: " . $conn->connect_error); }

$res = $conn->query("SELECT * FROM sameepaccounting_item_delete_requests");
if ($res->num_rows === 0) {
    echo "No deletion requests found in table sameepaccounting_item_delete_requests.\n";
} else {
    while ($row = $res->fetch_assoc()) {
        print_r($row);
    }
}
