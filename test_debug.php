<?php
$conn = new mysqli('localhost', 'root', '', 'ultimate-manufacturing-erp');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$prefix = 'sameepaccounting_';

// 1. Get the BOM items
$bom_total_res = $conn->query("SELECT * FROM {$prefix}bom_total WHERE id = 60");
if ($bom_total_res && $bom_total_res->num_rows > 0) {
    $bom_total = $bom_total_res->fetch_assoc();
    $number = $bom_total['number_fk'];
    echo "BOM Number: $number\n";
    
    $bom_items_res = $conn->query("SELECT * FROM {$prefix}bom WHERE number = '" . $conn->real_escape_string($number) . "'");
    $bom_items = [];
    while ($row = $bom_items_res->fetch_assoc()) {
        $bom_items[] = $row;
    }
    echo "Total BOM items in DB: " . count($bom_items) . "\n";
    
    // 2. Get all inventory items
    $inventory_res = $conn->query("SELECT code, item_name FROM {$prefix}inventory");
    $inventory = [];
    while ($row = $inventory_res->fetch_assoc()) {
        $inventory[] = $row;
    }
    
    // Get all units
    $unit_res = $conn->query("SELECT * FROM {$prefix}units");
    $units = [];
    while ($row = $unit_res->fetch_assoc()) {
        $units[] = $row;
    }
    echo "Total units in DB: " . count($units) . "\n";
    
    // 3. Simulate the loop in edit_bom.php
    foreach ($bom_items as $key_idx => $key) {
        echo "\nRow " . ($key_idx + 1) . ":\n";
        echo "  BOM Product Name: '" . $key['product_name'] . "' (Type: " . gettype($key['product_name']) . ")\n";
        echo "  BOM Unit: '" . $key['unit'] . "' (Type: " . gettype($key['unit']) . ")\n";
        
        $match_found = false;
        foreach ($inventory as $item) {
            if ($key['product_name'] == $item['code']) {
                echo "  -> Product MATCH FOUND: '" . $item['code'] . "'\n";
                $match_found = true;
                $selected = 'selected="selected"';
                echo "  -> Product Option HTML: <option value=\"" . $item['code'] . "\" " . $selected . ">" . $item['code'] . " - " . $item['item_name'] . "</option>\n";
                break;
            }
        }
        if (!$match_found) {
            echo "  -> NO Product MATCH FOUND for '" . $key['product_name'] . "'!\n";
        }
        
        $unit_match_found = false;
        foreach ($units as $u) {
            if ($key['unit'] == $u['unit']) {
                echo "  -> Unit MATCH FOUND: '" . $u['unit'] . "'\n";
                $unit_match_found = true;
                $selected = 'selected="selected"';
                echo "  -> Unit Option HTML: <option value=\"" . $u['unit'] . "\" " . $selected . ">" . $u['unit'] . "</option>\n";
                break;
            }
        }
        if (!$unit_match_found) {
            echo "  -> NO Unit MATCH FOUND for '" . $key['unit'] . "'!\n";
        }
    }
} else {
    echo "BOM Total ID 60 not found!\n";
}

$conn->close();
