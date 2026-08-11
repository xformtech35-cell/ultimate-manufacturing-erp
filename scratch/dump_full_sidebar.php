<?php
$conn = new mysqli('localhost', 'root', '', 'ultimate-manufacturing-erp');
if ($conn->connect_error) { die("Conn error"); }

$res = $conn->query("SELECT id, parent_id, title, permission FROM sameepaccounting_sidebar_menu");
while($row = $res->fetch_assoc()) {
    echo "ID: " . $row['id'] . " | Parent: " . var_export($row['parent_id'], true) . " | Title: " . $row['title'] . " | Perm: " . $row['permission'] . "\n";
}
