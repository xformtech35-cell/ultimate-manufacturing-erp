<?php
$conn = new mysqli('p3nlmysql7plsk.secureserver.net', 'uwsenvirotech', '754br8~rO', 'xformtech_employee');
if ($conn->connect_error) { die("Conn error"); }

$res = $conn->query("SELECT * FROM uwsaccounting_permission WHERE grp_perm IN ('Datasheet_Upload', 'Budget_Sheet_Upload')");
echo "Matches found on live: " . $res->num_rows . "\n";
while($row = $res->fetch_assoc()) {
    print_r($row);
}
