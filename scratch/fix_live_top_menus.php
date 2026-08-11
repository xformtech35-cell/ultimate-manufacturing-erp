<?php
$conn = new mysqli('p3nlmysql7plsk.secureserver.net', 'uwsenvirotech', '754br8~rO', 'xformtech_employee');
if ($conn->connect_error) { die("Conn error"); }

$conn->query("UPDATE uwsaccounting_sidebar_menu SET parent_id = NULL WHERE id IN (75, 90, 91)");
echo "Updated parent_id for IDs 75, 90, 91 to NULL. Affected: " . $conn->affected_rows . "\n";
