<?php
$conn = new mysqli('p3nlmysql7plsk.secureserver.net', 'uwsenvirotech', '754br8~rO', 'xformtech_employee');
if ($conn->connect_error) { die("Conn error"); }

$res = $conn->query("SHOW TABLES LIKE 'item_delete_requests'");
if ($res) {
    echo "Table matches count: " . $res->num_rows . "\n";
} else {
    echo "Query failed\n";
}
