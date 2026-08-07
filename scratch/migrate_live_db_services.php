<?php
/**
 * Migration Script to sync Live Database (xformtech_employee) tables & sidebar menus for Services Module
 */

$host = 'p3nlmysql7plsk.secureserver.net';
$user = 'uwsenvirotech';
$pass = '754br8~rO';
$db   = 'xformtech_employee';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Live Connection failed: " . $conn->connect_error . "\n");
}
echo "Connected to Live Database: $db\n";

// 1. Add service_type column to uwsaccounting_service_order and uwsaccounting_service_order_total if not exist
$tables = ['uwsaccounting_service_order', 'uwsaccounting_service_order_total'];
foreach ($tables as $t) {
    $res = $conn->query("SHOW COLUMNS FROM $t LIKE 'service_type'");
    if ($res->num_rows == 0) {
        $alterSql = "ALTER TABLE $t ADD COLUMN service_type VARCHAR(50) NOT NULL DEFAULT 'order'";
        if ($conn->query($alterSql) === TRUE) {
            echo "Added service_type column to live table $t.\n";
        } else {
            echo "Error adding service_type to live table $t: " . $conn->error . "\n";
        }
    } else {
        echo "service_type column already exists in live table $t.\n";
    }
}

// 2. Ensure parent Service menu (Permission = Services) exists
$res = $conn->query("SELECT id FROM uwsaccounting_sidebar_menu WHERE title = 'Service' AND parent_id IS NULL");
if ($res->num_rows > 0) {
    $parent = $res->fetch_assoc();
    $parentId = $parent['id'];
    echo "Found parent Service menu ID on live: $parentId\n";
} else {
    // Insert parent Service menu
    $conn->query("INSERT INTO uwsaccounting_sidebar_menu (title, icon, url, permission, sort_order, active_cond) VALUES ('Service', 'fa fa-wrench', '', 'Services', 9, '{\"currentPage\":\"ServiceOrderController\"}')");
    $parentId = $conn->insert_id;
    echo "Created parent Service menu on live. Parent ID is: $parentId\n";
}

// 3. Define the submenus
$submenus = [
    [
        'title' => 'Service Order',
        'url' => 'ServiceOrderController/index',
        'permission' => NULL,
        'sort_order' => 0,
        'active_cond' => '{"currentPage":"ServiceOrderController","pages":["index","create_service_order","edit_service_order","show_service_order"]}'
    ],
    [
        'title' => 'AMC',
        'url' => 'ServiceOrderController/amc_index',
        'permission' => NULL,
        'sort_order' => 1,
        'active_cond' => '{"currentPage":"ServiceOrderController","pages":["amc_index","create_amc","edit_amc","show_amc"]}'
    ],
    [
        'title' => 'One Time Service',
        'url' => 'ServiceOrderController/one_time_index',
        'permission' => NULL,
        'sort_order' => 2,
        'active_cond' => '{"currentPage":"ServiceOrderController","pages":["one_time_index","create_one_time","edit_one_time","show_one_time"]}'
    ],
    [
        'title' => 'FOC',
        'url' => 'ServiceOrderController/foc_index',
        'permission' => NULL,
        'sort_order' => 3,
        'active_cond' => '{"currentPage":"ServiceOrderController","pages":["foc_index","create_foc","edit_foc","show_foc"]}'
    ],
    [
        'title' => 'E&C Project',
        'url' => 'ServiceOrderController/ec_project_index',
        'permission' => NULL,
        'sort_order' => 4,
        'active_cond' => '{"currentPage":"ServiceOrderController","pages":["ec_project_index","create_ec_project","edit_ec_project","show_ec_project"]}'
    ],
    [
        'title' => 'Service Proforma Invoice',
        'url' => 'ServiceOrderController/proforma_index',
        'permission' => NULL,
        'sort_order' => 5,
        'active_cond' => '{"currentPage":"ServiceOrderController","pages":["proforma_index","create_proforma","edit_proforma","show_proforma"]}'
    ],
    [
        'title' => 'Service Quotation',
        'url' => 'ServiceOrderController/quotation_index',
        'permission' => NULL,
        'sort_order' => 6,
        'active_cond' => '{"currentPage":"ServiceOrderController","pages":["quotation_index","create_quotation","edit_quotation","show_quotation"]}'
    ]
];

foreach ($submenus as $m) {
    // Check if menu item already exists under parent
    $stmt = $conn->prepare("SELECT id FROM uwsaccounting_sidebar_menu WHERE parent_id = ? AND title = ?");
    $stmt->bind_param("is", $parentId, $m['title']);
    $stmt->execute();
    $chk_res = $stmt->get_result();
    if ($chk_res->num_rows > 0) {
        $row = $chk_res->fetch_assoc();
        // Update existing item's url and active_cond to make sure it matches
        $updateStmt = $conn->prepare("UPDATE uwsaccounting_sidebar_menu SET url = ?, active_cond = ?, sort_order = ? WHERE id = ?");
        $updateStmt->bind_param("ssii", $m['url'], $m['active_cond'], $m['sort_order'], $row['id']);
        $updateStmt->execute();
        echo "Updated live menu item: {$m['title']}\n";
    } else {
        // Insert new item
        $insertStmt = $conn->prepare("INSERT INTO uwsaccounting_sidebar_menu (parent_id, title, icon, url, permission, sort_order, active_cond) VALUES (?, ?, 'fa fa-circle-o', ?, ?, ?, ?)");
        $insertStmt->bind_param("isssis", $parentId, $m['title'], $m['url'], $m['permission'], $m['sort_order'], $m['active_cond']);
        $insertStmt->execute();
        echo "Inserted live menu item: {$m['title']}\n";
    }
}

// 4. Ensure Admin has 'Services' permission on live
$chk_perm = $conn->query("SELECT * FROM uwsaccounting_permission WHERE role_id_fk = 1 AND grp_perm = 'Services'");
if ($chk_perm->num_rows == 0) {
    $conn->query("INSERT INTO uwsaccounting_permission (role_id_fk, grp_perm) VALUES (1, 'Services')");
    echo "Granted Services permission to live Admin role.\n";
} else {
    echo "Admin role already has Services permission on live.\n";
}

$conn->close();
echo "Live DB Migration Complete for Services Module!\n";
?>
