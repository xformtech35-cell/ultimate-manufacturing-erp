<?php
$conn = new mysqli('localhost', 'root', '', 'ultimate-manufacturing-erp');
if ($conn->connect_error) { die("Conn error"); }

$res = $conn->query("SELECT * FROM sameepaccounting_permission WHERE grp_perm = 'Engineering'");
echo "Engineering permission records count: " . $res->num_rows . "\n";
while($row = $res->fetch_assoc()) {
    print_r($row);
}
