<?php
$conn = new mysqli('localhost', 'root', '', 'ultimate-manufacturing-erp');
if ($conn->connect_error) { die("Conn error"); }

$res = $conn->query("SELECT * FROM sameepaccounting_permission WHERE role_id_fk = 1 AND grp_perm IN ('Datasheet_Upload', 'Budget_Sheet_Upload')");
echo "Local admin permission count: " . $res->num_rows . "\n";
while($row = $res->fetch_assoc()) {
    print_r($row);
}
