<?php
$conn = new mysqli('p3nlmysql7plsk.secureserver.net', 'uwsenvirotech', '754br8~rO', 'xformtech_employee');
if ($conn->connect_error) { die("Conn error"); }

$conn->query("UPDATE uwsaccounting_sidebar_menu SET parent_id = 75 WHERE id IN (76, 77, 78, 79)");
echo "Updated AI Insights submenus parent_id to 75. Affected: " . $conn->affected_rows . "\n";
