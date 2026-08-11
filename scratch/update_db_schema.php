<?php
$conn = new mysqli('localhost', 'root', '', 'ultimate-manufacturing-erp');
if ($conn->connect_error) { die("Conn error: " . $conn->connect_error); }

// Alter table sameepaccounting_item_delete_requests
// 1. Modify status column to include 'deleted'
$sql1 = "ALTER TABLE sameepaccounting_item_delete_requests MODIFY COLUMN status ENUM('pending','approved','rejected','deleted') NOT NULL DEFAULT 'pending'";
if ($conn->query($sql1)) {
    echo "Altered status column successfully.\n";
} else {
    echo "Error altering status column: " . $conn->error . "\n";
}

// 2. Add user_notified column
$sql2 = "ALTER TABLE sameepaccounting_item_delete_requests ADD COLUMN user_notified TINYINT(1) NOT NULL DEFAULT 0 AFTER review_remarks";
if ($conn->query($sql2)) {
    echo "Added user_notified column successfully.\n";
} else {
    echo "Error adding user_notified column: " . $conn->error . "\n";
}
