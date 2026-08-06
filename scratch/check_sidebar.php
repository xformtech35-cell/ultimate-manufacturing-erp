<?php
$conn = new mysqli('localhost', 'root', '', 'ultimate-manufacturing-erp');
if ($conn->connect_error) { die("Conn error"); }

$res = $conn->query("SELECT id, parent_id, title, icon, url, permission, sort_order FROM sameepaccounting_sidebar_menu");
while($row = $res->fetch_assoc()) {
    echo "ID: " . $row['id'] . " | Parent: " . $row['parent_id'] . " | Title: " . $row['title'] . " | Perm: " . $row['permission'] . " | URL: " . $row['url'] . "\n";
}
