<?php
$conn = new mysqli('localhost', 'root', '', 'ultimate-manufacturing-erp');
if ($conn->connect_error) { die("Conn error: " . $conn->connect_error); }

$res = $conn->query("DESCRIBE sameepaccounting_notifications");
while ($row = $res->fetch_assoc()) {
    print_r($row);
}
