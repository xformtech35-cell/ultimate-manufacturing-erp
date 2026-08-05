<?php
/**
 * One-time script to add sample opening balance data
 * Run this ONCE: http://localhost/sameep-accounting/add_sample_opening_balance.php
 * Then DELETE the file
 */

defined('BASEPATH') OR exit('Direct access forbidden');

require_once './application/config/database.php'; // Load DB config

$config['hostname'] = 'localhost';
$config['username'] = 'root';
$config['password'] = '';
$config['database'] = 'ultimate-manufacturing-erp'; // Adjust if different
$config['dbdriver'] = 'mysqli';

$dsn = 'mysql:host='.$config['hostname'].';dbname='.$config['database'].';charset=utf8mb4';
$pdo = new PDO($dsn, $config['username'], $config['password'], [
PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

// Sample customer opening balance (match dropdown account_name)
try {
    $pdo->exec("
        INSERT INTO opening_balance (account_name, opening_balance_amount, balance_date, description, uid, created_at) 
        VALUES 
            ('Demo Customer Pvt Ltd - CUST001', 50000.00, '2024-04-01', 'Test opening balance for sales ledger', 1, NOW()),
            ('Demo Supplier Pvt Ltd - SUP001', 75000.00, '2024-04-01', 'Test opening balance for purchase ledger', 1, NOW())
        ON DUPLICATE KEY UPDATE opening_balance_amount = VALUES(opening_balance_amount)
    ");
    
    $count = $pdo->exec("SELECT COUNT(*) FROM opening_balance WHERE uid=1");
    
    echo "<h2>✅ Sample opening balance data added!</h2>
    <p>2 test entries created for uid=1:</p>
    <ul>
        <li><strong>Sales:</strong> 'Demo Customer Pvt Ltd - CUST001' = ₹50,000 (04-01-2024)</li>
        <li><strong>Purchase:</strong> 'Demo Supplier Pvt Ltd - SUP001' = ₹75,000 (04-01-2024)</li>
    </ul>
    <p><a href='BalanceController/opening_balance_index'>View Opening Balances</a></p>
    <p><strong>Next:</strong> Go to Ledger Report → Select matching company → Generate → See OB first row!</p>
    <hr>
    <p><em>DELETE this file after use for security.</em></p>";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>

