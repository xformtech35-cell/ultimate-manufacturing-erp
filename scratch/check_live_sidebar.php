<?php
$host = 'p3nlmysql7plsk.secureserver.net';
$user = 'uwsenvirotech';
$pass = '754br8~rO';
$db   = 'xformtech_employee';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) { die("Conn error"); }

$res = $conn->query("SELECT id, parent_id, title, permission FROM uwsaccounting_sidebar_menu");
while($row = $res->fetch_assoc()) {
    echo "ID: " . $row['id'] . " | Parent: " . var_export($row['parent_id'], true) . " | Title: " . $row['title'] . " | Perm: " . $row['permission'] . "\n";
}
