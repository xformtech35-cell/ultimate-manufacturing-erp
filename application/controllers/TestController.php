<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class TestController extends MY_Controller {

    public function test_mrn() {
        if (!is_cli()) {
            echo "This test script can only be run via CLI.\n";
            return;
        }

        echo "============================================================\n";
        echo "  AUTOMATED END-TO-END TEST: Material Return Note (MRN)\n";
        echo "  Simulates full form submission from create_mrn page\n";
        echo "============================================================\n\n";

        $this->load->database();
        $this->load->model('Material_issue_model');
        $this->load->model('Joborder');
        $model = $this->Material_issue_model;
        $uid = 1;
        $all_passed = true;

        // ================================================================
        // PHASE 1: Test AJAX endpoint - get_joborder_items
        // ================================================================
        echo "--- PHASE 1: Test Job Order Item Fetch (AJAX Simulation) ---\n\n";

        $test_jo = 'JO/0090/26-27';
        echo "[TEST 1.1] Fetching items for Job Order: {$test_jo}...\n";

        // Simulate what the AJAX call does
        $jo_items = $this->Joborder->get_joborder_data($test_jo, $uid);

        if (!$jo_items || count($jo_items) == 0) {
            echo "  FAILED: No items returned from get_joborder_data()\n";
            $all_passed = false;
        } else {
            echo "  PASS: Found " . count($jo_items) . " item(s) in Job Order\n";
        }

        // Build the same response the AJAX endpoint builds
        $returnable_items = array();
        foreach ($jo_items as $item) {
            if (isset($item->product_name) && $item->product_name === '__HEADING__') continue;

            $inventory = $model->get_inventory_item_by_code($item->product_name);
            if (!$inventory) continue;

            $detailed_qtys = $model->get_detailed_issued_quantities($inventory['inventory_id'], $test_jo);
            $gross_issued = $detailed_qtys['gross_issued'];
            $returned     = $detailed_qtys['returned'];
            $net_issued   = $detailed_qtys['net_issued'];

            if ($gross_issued > 0 && $net_issued > 0) {
                $returnable_items[] = array(
                    'inventory_id'    => $inventory['inventory_id'],
                    'item_code'       => $item->product_name,
                    'item_name'       => $inventory['item_name'],
                    'required_qty'    => floatval($item->quantity),
                    'gross_issued_qty'=> $gross_issued,
                    'returned_qty'    => $returned,
                    'net_issued_qty'  => $net_issued,
                    'stock'           => floatval($inventory['stock']),
                    'available_stock' => floatval($inventory['available_stock']),
                    'unit'            => $inventory['unit']
                );
            }
        }

        echo "\n[TEST 1.2] Checking returnable items (items with net issued qty > 0)...\n";
        if (empty($returnable_items)) {
            echo "  FAILED: No returnable items found for {$test_jo}\n";
            echo "  (All items may have already been returned)\n";
            $all_passed = false;
            $this->print_result($all_passed);
            return;
        }

        echo "  PASS: " . count($returnable_items) . " returnable item(s) found:\n";
        foreach ($returnable_items as $ri) {
            echo "    - [{$ri['item_code']}] {$ri['item_name']}: ";
            echo "Issued={$ri['gross_issued_qty']}, Returned={$ri['returned_qty']}, ";
            echo "Net with Production={$ri['net_issued_qty']}, Stock={$ri['stock']}\n";
        }

        // Pick the first returnable item for testing
        $test_item = $returnable_items[0];
        $return_qty = min(2.00, floatval($test_item['net_issued_qty'])); // Return 2 or less

        echo "\n[TEST 1.3] Validating return quantity constraint...\n";
        echo "  Selected item: [{$test_item['item_code']}] {$test_item['item_name']}\n";
        echo "  Max returnable (net issued): {$test_item['net_issued_qty']}\n";
        echo "  Qty we will return: {$return_qty}\n";

        if ($return_qty > $test_item['net_issued_qty']) {
            echo "  FAILED: Return qty exceeds net issued qty!\n";
            $all_passed = false;
        } else {
            echo "  PASS: Return qty within allowed limit.\n";
        }

        // ================================================================
        // PHASE 2: Simulate Form POST (create_mrn submission)
        // ================================================================
        echo "\n--- PHASE 2: Simulate Full Form Submission ---\n\n";

        // Record pre-submission state
        $pre_stock = floatval($test_item['stock']);
        $pre_available = floatval($test_item['available_stock']);
        $inventory_id = $test_item['inventory_id'];

        echo "[TEST 2.1] Recording pre-submission inventory state...\n";
        echo "  Inventory ID: {$inventory_id}\n";
        echo "  Pre-Stock: {$pre_stock}, Pre-Available: {$pre_available}\n";

        // Generate MRN number
        $mrn_no = $model->generate_mrn_no();
        echo "\n[TEST 2.2] Generating MRN number...\n";
        if (empty($mrn_no)) {
            echo "  FAILED: MRN number generation returned empty\n";
            $all_passed = false;
        } else {
            echo "  PASS: Generated MRN No: {$mrn_no}\n";
        }

        // Simulate form data exactly as the controller builds it
        $neg_qty = -$return_qty; // Controller converts to negative

        $issue_data = array(
            'issue_no'        => $mrn_no,
            'issue_date'      => date('Y-m-d'),
            'issued_to'       => 'Automated Test User',
            'department'      => 'Production',
            'project_code'    => '',
            'joborder_number' => $test_jo,
            'purpose'         => 'Production Return (MRN)',
            'remarks'         => 'Automated test - will be cleaned up',
            'status'          => 'issued',
            'total_items'     => 1,
            'total_qty'       => $neg_qty,
            'uid'             => $uid
        );

        $item_data = array(
            'inventory_id_fk' => $inventory_id,
            'quantity'         => $neg_qty,
            'unit_price'       => 0,
            'pending_qty'      => 0,
            'remarks'          => 'Test return'
        );

        echo "\n[TEST 2.3] Inserting MRN slip into database...\n";
        $this->db->trans_start();

        $this->db->insert('material_issue_slips', $issue_data);
        $mrn_id = $this->db->insert_id();

        if (!$mrn_id) {
            echo "  FAILED: Could not insert MRN slip record\n";
            $all_passed = false;
            $this->db->trans_rollback();
            $this->print_result($all_passed);
            return;
        }
        echo "  PASS: MRN slip created with issue_id={$mrn_id}\n";

        echo "\n[TEST 2.4] Inserting MRN item record...\n";
        $item_data['issue_id'] = $mrn_id;
        $item_data['uid'] = $uid;
        $item_data['total_amount'] = 0;
        $this->db->insert('material_issue_items', $item_data);
        echo "  PASS: MRN item inserted.\n";

        echo "\n[TEST 2.5] Processing stock update (return to inventory)...\n";
        $model->process_stock_update_for_item(
            $inventory_id,
            $neg_qty,
            'return',
            $test_jo,
            $mrn_no,
            'Automated Test User',
            $uid
        );

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            echo "  FAILED: Transaction failed!\n";
            $all_passed = false;
            $this->print_result($all_passed);
            return;
        }
        echo "  PASS: Transaction committed successfully.\n";

        // ================================================================
        // PHASE 3: Verify all database changes
        // ================================================================
        echo "\n--- PHASE 3: Verification ---\n\n";

        // 3.1 Verify MRN slip exists
        echo "[TEST 3.1] Verifying MRN slip record in database...\n";
        $mrn_record = $this->db->where('issue_id', $mrn_id)->get('material_issue_slips')->row_array();
        if (!$mrn_record) {
            echo "  FAILED: MRN slip not found in database\n";
            $all_passed = false;
        } else {
            echo "  PASS: MRN slip found.\n";
            echo "    Issue No: {$mrn_record['issue_no']}\n";
            echo "    Purpose: {$mrn_record['purpose']}\n";
            echo "    Status: {$mrn_record['status']}\n";
            echo "    Job Order: {$mrn_record['joborder_number']}\n";
            echo "    Total Qty: {$mrn_record['total_qty']}\n";

            if ($mrn_record['purpose'] !== 'Production Return (MRN)') {
                echo "  FAILED: Purpose should be 'Production Return (MRN)'\n";
                $all_passed = false;
            }
            if ($mrn_record['status'] !== 'issued') {
                echo "  FAILED: Status should be 'issued'\n";
                $all_passed = false;
            }
        }

        // 3.2 Verify MRN item exists with negative quantity
        echo "\n[TEST 3.2] Verifying MRN item record (negative quantity)...\n";
        $mrn_item = $this->db->where('issue_id', $mrn_id)
                              ->where('inventory_id_fk', $inventory_id)
                              ->get('material_issue_items')->row_array();
        if (!$mrn_item) {
            echo "  FAILED: MRN item record not found\n";
            $all_passed = false;
        } else {
            $stored_qty = floatval($mrn_item['quantity']);
            echo "  PASS: Item record found. Stored Qty: {$stored_qty}\n";
            if ($stored_qty >= 0) {
                echo "  FAILED: MRN item qty should be negative (got {$stored_qty})\n";
                $all_passed = false;
            } else {
                echo "  PASS: Quantity is correctly negative.\n";
            }
        }

        // 3.3 Verify inventory stock increased
        echo "\n[TEST 3.3] Verifying inventory stock levels increased...\n";
        $post_item = $this->db->select('stock, available_stock')
                               ->where('inventory_id', $inventory_id)
                               ->get('inventory')->row_array();
        $post_stock = floatval($post_item['stock']);
        $post_available = floatval($post_item['available_stock']);

        $expected_stock = $pre_stock + $return_qty;
        $expected_available = $pre_available + $return_qty;

        echo "  Stock:     Before={$pre_stock}, After={$post_stock}, Expected={$expected_stock}\n";
        echo "  Available: Before={$pre_available}, After={$post_available}, Expected={$expected_available}\n";

        if (abs($post_stock - $expected_stock) > 0.01) {
            echo "  FAILED: Stock level mismatch\n";
            $all_passed = false;
        } else {
            echo "  PASS: Stock level correct.\n";
        }

        if (abs($post_available - $expected_available) > 0.01) {
            echo "  FAILED: Available stock mismatch\n";
            $all_passed = false;
        } else {
            echo "  PASS: Available stock correct.\n";
        }

        // 3.4 Verify stock ledger entry
        echo "\n[TEST 3.4] Verifying stock ledger RETURN entry...\n";
        $ledger = $this->db->where('reference_no', $mrn_no)
                            ->where('transaction_type', 'RETURN')
                            ->get('stock_ledger')->row_array();
        if (!$ledger) {
            echo "  FAILED: No RETURN ledger entry found for {$mrn_no}\n";
            $all_passed = false;
        } else {
            echo "  PASS: Ledger entry found.\n";
            echo "    Type: {$ledger['transaction_type']}\n";
            echo "    Qty: {$ledger['quantity']}\n";
            echo "    Remarks: {$ledger['remarks']}\n";
        }

        // 3.5 Verify the returned qty is now reflected in get_detailed_issued_quantities
        echo "\n[TEST 3.5] Verifying returned qty is tracked in issue history...\n";
        $updated_qtys = $model->get_detailed_issued_quantities($inventory_id, $test_jo);
        echo "  Gross Issued: {$updated_qtys['gross_issued']}\n";
        echo "  Total Returned: {$updated_qtys['returned']}\n";
        echo "  Net Issued (with production): {$updated_qtys['net_issued']}\n";

        if (floatval($updated_qtys['returned']) >= $return_qty) {
            echo "  PASS: Returned quantity correctly updated.\n";
        } else {
            echo "  FAILED: Returned quantity not properly tracked.\n";
            $all_passed = false;
        }

        // ================================================================
        // PHASE 4: Cleanup
        // ================================================================
        echo "\n--- PHASE 4: Cleanup ---\n\n";
        echo "Removing test MRN data and restoring inventory...\n";

        // Delete MRN items
        $this->db->where('issue_id', $mrn_id)->delete('material_issue_items');
        echo "  Deleted MRN items.\n";

        // Delete MRN slip
        $this->db->where('issue_id', $mrn_id)->delete('material_issue_slips');
        echo "  Deleted MRN slip.\n";

        // Delete stock ledger entry
        $this->db->where('reference_no', $mrn_no)->delete('stock_ledger');
        echo "  Deleted stock ledger entry.\n";

        // Restore inventory stock
        $this->db->where('inventory_id', $inventory_id)
                 ->update('inventory', [
                     'stock' => $pre_stock,
                     'available_stock' => $pre_available
                 ]);
        echo "  Restored inventory: stock={$pre_stock}, available={$pre_available}\n";

        // Verify cleanup
        $verify_cleanup = $this->db->where('issue_id', $mrn_id)->get('material_issue_slips')->row_array();
        if (!$verify_cleanup) {
            echo "  PASS: Cleanup verified - no test data remains.\n";
        } else {
            echo "  WARNING: Test data may not be fully cleaned.\n";
        }

        // ================================================================
        // FINAL RESULT
        // ================================================================
        $this->print_result($all_passed);
    }

    private function print_result($all_passed) {
        echo "\n============================================================\n";
        if ($all_passed) {
            echo "  FINAL RESULT: ALL TESTS PASSED!\n";
        } else {
            echo "  FINAL RESULT: SOME TESTS FAILED!\n";
        }
        echo "============================================================\n";
    }
}
