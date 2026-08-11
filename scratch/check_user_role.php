<?php
$conn = new mysqli('localhost', 'root', '', 'ultimate-manufacturing-erp');
if ($conn->connect_error) { die("Conn error: " . $conn->connect_error); }

$res = $conn->query("SELECT * FROM sameepaccounting_user WHERE username LIKE '%Shivansh%' OR user_email LIKE '%Shivansh%'");
while ($row = $res->fetch_assoc()) {
    print_r($row);
}
