<?php
$conn = new mysqli('localhost', 'root', '', 'ultimate-manufacturing-erp');
if ($conn->connect_error) { die("Conn error"); }

$perms_to_add = ['Engineering', 'Bom', 'BOM_Approvals', 'Datasheet_Upload', 'Budget_Sheet_Upload'];
foreach ($perms_to_add as $p) {
    $chk_p = $conn->query("SELECT * FROM sameepaccounting_permission WHERE role_id_fk = 1 AND grp_perm = '$p'");
    if ($chk_p->num_rows == 0) {
        $conn->query("INSERT INTO sameepaccounting_permission (role_id_fk, grp_perm) VALUES (1, '$p')");
        echo "Granted permission '$p' to local Admin.\n";
    }
}
echo "Local permissions alignment complete.\n";
