<?php
$conn = new mysqli('localhost', 'root', '', 'ultimate-manufacturing-erp');
if ($conn->connect_error) { die("Conn error"); }

$res = $conn->query("SELECT id, parent_id, title, url, permission FROM sameepaccounting_sidebar_menu WHERE title IN ('Datasheet Upload', 'Budget Sheet Upload')");
while($row = $res->fetch_assoc()) {
    echo "ID: " . $row['id'] . " | Title: " . $row['title'] . " | URL: " . $row['url'] . " | Perm: " . $row['permission'] . "\n";
}
