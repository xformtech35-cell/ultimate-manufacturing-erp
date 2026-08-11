<?php
$conn = new mysqli('localhost', 'root', '', 'ultimate-manufacturing-erp');
if ($conn->connect_error) { die("Conn error: " . $conn->connect_error); }

$res = $conn->query("SELECT * FROM sameepaccounting_role WHERE role_id = 1");
while ($row = $res->fetch_assoc()) {
    print_r($row);
}
