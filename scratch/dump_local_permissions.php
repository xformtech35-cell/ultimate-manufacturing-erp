<?php
$conn = new mysqli('localhost', 'root', '', 'ultimate-manufacturing-erp');
if ($conn->connect_error) { die("Conn error"); }

$res = $conn->query("SELECT grp_perm FROM sameepaccounting_permission WHERE role_id_fk = 1");
$perms = [];
while($row = $res->fetch_assoc()) {
    $perms[] = $row['grp_perm'];
}
print_r($perms);
