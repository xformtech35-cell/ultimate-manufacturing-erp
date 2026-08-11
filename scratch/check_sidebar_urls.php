<?php
$conn = new mysqli('localhost', 'root', '', 'ultimate-manufacturing-erp');
if ($conn->connect_error) { die("Conn error: " . $conn->connect_error); }

$res = $conn->query("SELECT id, parent_id, title, url, permission FROM sameepaccounting_sidebar_menu ORDER BY id ASC");
while ($row = $res->fetch_assoc()) {
    echo "ID: " . $row['id'] . " | Parent: " . $row['parent_id'] . " | Title: " . $row['title'] . " | URL: " . $row['url'] . " | Perm: " . $row['permission'] . "\n";
}
