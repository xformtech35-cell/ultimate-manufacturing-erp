<?php
$conn = new mysqli('localhost', 'root', '', 'ultimate-manufacturing-erp');
if ($conn->connect_error) { die("Conn error: " . $conn->connect_error); }

$res = $conn->query("SELECT u.user_id, u.username, u.user_email, r.role_name 
                     FROM sameepaccounting_user u 
                     LEFT JOIN sameepaccounting_role r ON r.role_id = u.role");
while ($row = $res->fetch_assoc()) {
    print_r($row);
}
