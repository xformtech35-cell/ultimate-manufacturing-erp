/* === LIVE SQL MIGRATION QUERIES FOR phpMyAdmin === */
SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';
SET FOREIGN_KEY_CHECKS = 0;

/* Table: sameepaccounting_advance_amount (PK: advance_id) */
ALTER TABLE `sameepaccounting_advance_amount` MODIFY `advance_id` INT NOT NULL;
ALTER TABLE `sameepaccounting_advance_amount` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_advance_amount` SET `advance_id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_advance_amount` MODIFY `advance_id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`advance_id`);

/* Table: sameepaccounting_amendment_approvals (PK: approval_id) */
ALTER TABLE `sameepaccounting_amendment_approvals` MODIFY `approval_id` INT NOT NULL;
ALTER TABLE `sameepaccounting_amendment_approvals` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_amendment_approvals` SET `approval_id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_amendment_approvals` MODIFY `approval_id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`approval_id`);

/* Table: sameepaccounting_amendment_items (PK: item_id) */
ALTER TABLE `sameepaccounting_amendment_items` MODIFY `item_id` INT NOT NULL;
ALTER TABLE `sameepaccounting_amendment_items` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_amendment_items` SET `item_id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_amendment_items` MODIFY `item_id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`item_id`);

/* Table: sameepaccounting_approval_matrix (PK: id) */
ALTER TABLE `sameepaccounting_approval_matrix` MODIFY `id` INT NOT NULL;
ALTER TABLE `sameepaccounting_approval_matrix` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_approval_matrix` SET `id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_approval_matrix` MODIFY `id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`id`);

/* Table: sameepaccounting_asset (PK: asset_id) */
ALTER TABLE `sameepaccounting_asset` MODIFY `asset_id` INT NOT NULL;
ALTER TABLE `sameepaccounting_asset` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_asset` SET `asset_id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_asset` MODIFY `asset_id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`asset_id`);

/* Table: sameepaccounting_bank_details (PK: bank_id) */
ALTER TABLE `sameepaccounting_bank_details` MODIFY `bank_id` INT NOT NULL;
ALTER TABLE `sameepaccounting_bank_details` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_bank_details` SET `bank_id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_bank_details` MODIFY `bank_id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`bank_id`);

/* Table: sameepaccounting_bank_transaction (PK: bank_transaction_id) */
ALTER TABLE `sameepaccounting_bank_transaction` MODIFY `bank_transaction_id` INT NOT NULL;
ALTER TABLE `sameepaccounting_bank_transaction` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_bank_transaction` SET `bank_transaction_id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_bank_transaction` MODIFY `bank_transaction_id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`bank_transaction_id`);

/* Table: sameepaccounting_barcode_master (PK: barcode_master_id) */
ALTER TABLE `sameepaccounting_barcode_master` MODIFY `barcode_master_id` INT NOT NULL;
ALTER TABLE `sameepaccounting_barcode_master` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_barcode_master` SET `barcode_master_id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_barcode_master` MODIFY `barcode_master_id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`barcode_master_id`);

/* Table: sameepaccounting_bom (PK: bom_id) */
ALTER TABLE `sameepaccounting_bom` MODIFY `bom_id` INT NOT NULL;
ALTER TABLE `sameepaccounting_bom` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_bom` SET `bom_id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_bom` MODIFY `bom_id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`bom_id`);

/* Table: sameepaccounting_bom_items (PK: id) */
ALTER TABLE `sameepaccounting_bom_items` MODIFY `id` INT NOT NULL;
ALTER TABLE `sameepaccounting_bom_items` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_bom_items` SET `id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_bom_items` MODIFY `id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`id`);

/* Table: sameepaccounting_bom_total (PK: id) */
ALTER TABLE `sameepaccounting_bom_total` MODIFY `id` INT NOT NULL;
ALTER TABLE `sameepaccounting_bom_total` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_bom_total` SET `id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_bom_total` MODIFY `id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`id`);

/* Table: sameepaccounting_category (PK: category_id) */
ALTER TABLE `sameepaccounting_category` MODIFY `category_id` INT NOT NULL;
ALTER TABLE `sameepaccounting_category` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_category` SET `category_id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_category` MODIFY `category_id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`category_id`);

/* Table: sameepaccounting_cheque_details (PK: cheque_id) */
ALTER TABLE `sameepaccounting_cheque_details` MODIFY `cheque_id` INT NOT NULL;
ALTER TABLE `sameepaccounting_cheque_details` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_cheque_details` SET `cheque_id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_cheque_details` MODIFY `cheque_id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`cheque_id`);

/* Table: sameepaccounting_customer (PK: customer_id) */
ALTER TABLE `sameepaccounting_customer` MODIFY `customer_id` INT NOT NULL;
ALTER TABLE `sameepaccounting_customer` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_customer` SET `customer_id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_customer` MODIFY `customer_id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`customer_id`);

/* Table: sameepaccounting_customer_wise_rate (PK: customer_wise_rate_id) */
ALTER TABLE `sameepaccounting_customer_wise_rate` MODIFY `customer_wise_rate_id` INT NOT NULL;
ALTER TABLE `sameepaccounting_customer_wise_rate` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_customer_wise_rate` SET `customer_wise_rate_id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_customer_wise_rate` MODIFY `customer_wise_rate_id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`customer_wise_rate_id`);

/* Table: sameepaccounting_customers (PK: id) */
ALTER TABLE `sameepaccounting_customers` MODIFY `id` INT NOT NULL;
ALTER TABLE `sameepaccounting_customers` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_customers` SET `id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_customers` MODIFY `id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`id`);

/* Table: sameepaccounting_customers_opening_balances (PK: id) */
ALTER TABLE `sameepaccounting_customers_opening_balances` MODIFY `id` INT NOT NULL;
ALTER TABLE `sameepaccounting_customers_opening_balances` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_customers_opening_balances` SET `id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_customers_opening_balances` MODIFY `id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`id`);

/* Table: sameepaccounting_delivery_challan (PK: invoice_id) */
ALTER TABLE `sameepaccounting_delivery_challan` MODIFY `invoice_id` INT NOT NULL;
ALTER TABLE `sameepaccounting_delivery_challan` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_delivery_challan` SET `invoice_id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_delivery_challan` MODIFY `invoice_id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`invoice_id`);

/* Table: sameepaccounting_delivery_challan_invoice_payment_gst (PK: invocie_pay_id) */
ALTER TABLE `sameepaccounting_delivery_challan_invoice_payment_gst` MODIFY `invocie_pay_id` INT NOT NULL;
ALTER TABLE `sameepaccounting_delivery_challan_invoice_payment_gst` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_delivery_challan_invoice_payment_gst` SET `invocie_pay_id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_delivery_challan_invoice_payment_gst` MODIFY `invocie_pay_id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`invocie_pay_id`);

/* Table: sameepaccounting_delivery_challan_payment_gst (PK: invocie_pay_id) */
ALTER TABLE `sameepaccounting_delivery_challan_payment_gst` MODIFY `invocie_pay_id` INT NOT NULL;
ALTER TABLE `sameepaccounting_delivery_challan_payment_gst` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_delivery_challan_payment_gst` SET `invocie_pay_id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_delivery_challan_payment_gst` MODIFY `invocie_pay_id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`invocie_pay_id`);

/* Table: sameepaccounting_delivery_challan_total (PK: id) */
ALTER TABLE `sameepaccounting_delivery_challan_total` MODIFY `id` INT NOT NULL;
ALTER TABLE `sameepaccounting_delivery_challan_total` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_delivery_challan_total` SET `id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_delivery_challan_total` MODIFY `id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`id`);

/* Table: sameepaccounting_department_master (PK: department_id) */
ALTER TABLE `sameepaccounting_department_master` MODIFY `department_id` INT NOT NULL;
ALTER TABLE `sameepaccounting_department_master` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_department_master` SET `department_id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_department_master` MODIFY `department_id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`department_id`);

/* Table: sameepaccounting_direct_individual (PK: id) */
ALTER TABLE `sameepaccounting_direct_individual` MODIFY `id` INT NOT NULL;
ALTER TABLE `sameepaccounting_direct_individual` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_direct_individual` SET `id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_direct_individual` MODIFY `id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`id`);

/* Table: sameepaccounting_dispatch_table (PK: id) */
ALTER TABLE `sameepaccounting_dispatch_table` MODIFY `id` INT NOT NULL;
ALTER TABLE `sameepaccounting_dispatch_table` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_dispatch_table` SET `id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_dispatch_table` MODIFY `id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`id`);

/* Table: sameepaccounting_drawing_files (PK: file_id) */
ALTER TABLE `sameepaccounting_drawing_files` MODIFY `file_id` INT NOT NULL;
ALTER TABLE `sameepaccounting_drawing_files` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_drawing_files` SET `file_id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_drawing_files` MODIFY `file_id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`file_id`);

/* Table: sameepaccounting_drawing_master (PK: drawing_id) */
ALTER TABLE `sameepaccounting_drawing_master` MODIFY `drawing_id` INT NOT NULL;
ALTER TABLE `sameepaccounting_drawing_master` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_drawing_master` SET `drawing_id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_drawing_master` MODIFY `drawing_id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`drawing_id`);

/* Table: sameepaccounting_drawing_revisions (PK: revision_id) */
ALTER TABLE `sameepaccounting_drawing_revisions` MODIFY `revision_id` INT NOT NULL;
ALTER TABLE `sameepaccounting_drawing_revisions` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_drawing_revisions` SET `revision_id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_drawing_revisions` MODIFY `revision_id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`revision_id`);

/* Table: sameepaccounting_email_setting (PK: email_setting_id) */
ALTER TABLE `sameepaccounting_email_setting` MODIFY `email_setting_id` INT NOT NULL;
ALTER TABLE `sameepaccounting_email_setting` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_email_setting` SET `email_setting_id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_email_setting` MODIFY `email_setting_id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`email_setting_id`);

/* Table: sameepaccounting_expense (PK: expense_id) */
ALTER TABLE `sameepaccounting_expense` MODIFY `expense_id` INT NOT NULL;
ALTER TABLE `sameepaccounting_expense` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_expense` SET `expense_id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_expense` MODIFY `expense_id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`expense_id`);

/* Table: sameepaccounting_expense_category (PK: exp_cat_id) */
ALTER TABLE `sameepaccounting_expense_category` MODIFY `exp_cat_id` INT NOT NULL;
ALTER TABLE `sameepaccounting_expense_category` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_expense_category` SET `exp_cat_id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_expense_category` MODIFY `exp_cat_id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`exp_cat_id`);

/* Table: sameepaccounting_finished_products (PK: product_id) */
ALTER TABLE `sameepaccounting_finished_products` MODIFY `product_id` INT NOT NULL;
ALTER TABLE `sameepaccounting_finished_products` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_finished_products` SET `product_id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_finished_products` MODIFY `product_id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`product_id`);

/* Table: sameepaccounting_grn (PK: grn_id) */
ALTER TABLE `sameepaccounting_grn` MODIFY `grn_id` INT NOT NULL;
ALTER TABLE `sameepaccounting_grn` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_grn` SET `grn_id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_grn` MODIFY `grn_id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`grn_id`);

/* Table: sameepaccounting_grn_approvals (PK: approval_id) */
ALTER TABLE `sameepaccounting_grn_approvals` MODIFY `approval_id` INT NOT NULL;
ALTER TABLE `sameepaccounting_grn_approvals` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_grn_approvals` SET `approval_id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_grn_approvals` MODIFY `approval_id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`approval_id`);

/* Table: sameepaccounting_grn_inspection (PK: inspection_id) */
ALTER TABLE `sameepaccounting_grn_inspection` MODIFY `inspection_id` INT NOT NULL;
ALTER TABLE `sameepaccounting_grn_inspection` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_grn_inspection` SET `inspection_id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_grn_inspection` MODIFY `inspection_id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`inspection_id`);

/* Table: sameepaccounting_grn_inspection_log (PK: inspection_id) */
ALTER TABLE `sameepaccounting_grn_inspection_log` MODIFY `inspection_id` INT NOT NULL;
ALTER TABLE `sameepaccounting_grn_inspection_log` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_grn_inspection_log` SET `inspection_id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_grn_inspection_log` MODIFY `inspection_id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`inspection_id`);

/* Table: sameepaccounting_grn_total (PK: id) */
ALTER TABLE `sameepaccounting_grn_total` MODIFY `id` INT NOT NULL;
ALTER TABLE `sameepaccounting_grn_total` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_grn_total` SET `id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_grn_total` MODIFY `id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`id`);

/* Table: sameepaccounting_gst_classes (PK: id) */
ALTER TABLE `sameepaccounting_gst_classes` MODIFY `id` INT NOT NULL;
ALTER TABLE `sameepaccounting_gst_classes` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_gst_classes` SET `id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_gst_classes` MODIFY `id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`id`);

/* Table: sameepaccounting_indirect_individual (PK: id) */
ALTER TABLE `sameepaccounting_indirect_individual` MODIFY `id` INT NOT NULL;
ALTER TABLE `sameepaccounting_indirect_individual` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_indirect_individual` SET `id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_indirect_individual` MODIFY `id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`id`);

/* Table: sameepaccounting_inventory (PK: inventory_id) */
ALTER TABLE `sameepaccounting_inventory` MODIFY `inventory_id` INT NOT NULL;
ALTER TABLE `sameepaccounting_inventory` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_inventory` SET `inventory_id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_inventory` MODIFY `inventory_id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`inventory_id`);

/* Table: sameepaccounting_invocie_payment_gst (PK: invocie_pay_id) */
ALTER TABLE `sameepaccounting_invocie_payment_gst` MODIFY `invocie_pay_id` INT NOT NULL;
ALTER TABLE `sameepaccounting_invocie_payment_gst` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_invocie_payment_gst` SET `invocie_pay_id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_invocie_payment_gst` MODIFY `invocie_pay_id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`invocie_pay_id`);

/* Table: sameepaccounting_invoice (PK: invoice_id) */
ALTER TABLE `sameepaccounting_invoice` MODIFY `invoice_id` INT NOT NULL;
ALTER TABLE `sameepaccounting_invoice` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_invoice` SET `invoice_id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_invoice` MODIFY `invoice_id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`invoice_id`);

/* Table: sameepaccounting_invoice_total (PK: id) */
ALTER TABLE `sameepaccounting_invoice_total` MODIFY `id` INT NOT NULL;
ALTER TABLE `sameepaccounting_invoice_total` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_invoice_total` SET `id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_invoice_total` MODIFY `id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`id`);

/* Table: sameepaccounting_item_category_master (PK: category_id) */
ALTER TABLE `sameepaccounting_item_category_master` MODIFY `category_id` INT NOT NULL;
ALTER TABLE `sameepaccounting_item_category_master` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_item_category_master` SET `category_id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_item_category_master` MODIFY `category_id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`category_id`);

/* Table: sameepaccounting_item_group_master (PK: group_id) */
ALTER TABLE `sameepaccounting_item_group_master` MODIFY `group_id` INT NOT NULL;
ALTER TABLE `sameepaccounting_item_group_master` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_item_group_master` SET `group_id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_item_group_master` MODIFY `group_id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`group_id`);

/* Table: sameepaccounting_joborder (PK: joborder_id) */
ALTER TABLE `sameepaccounting_joborder` MODIFY `joborder_id` INT NOT NULL;
ALTER TABLE `sameepaccounting_joborder` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_joborder` SET `joborder_id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_joborder` MODIFY `joborder_id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`joborder_id`);

/* Table: sameepaccounting_joborder_items (PK: id) */
ALTER TABLE `sameepaccounting_joborder_items` MODIFY `id` INT NOT NULL;
ALTER TABLE `sameepaccounting_joborder_items` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_joborder_items` SET `id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_joborder_items` MODIFY `id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`id`);

/* Table: sameepaccounting_joborder_total (PK: id) */
ALTER TABLE `sameepaccounting_joborder_total` MODIFY `id` INT NOT NULL;
ALTER TABLE `sameepaccounting_joborder_total` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_joborder_total` SET `id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_joborder_total` MODIFY `id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`id`);

/* Table: sameepaccounting_liabilities (PK: liabilities_id) */
ALTER TABLE `sameepaccounting_liabilities` MODIFY `liabilities_id` INT NOT NULL;
ALTER TABLE `sameepaccounting_liabilities` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_liabilities` SET `liabilities_id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_liabilities` MODIFY `liabilities_id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`liabilities_id`);

/* Table: sameepaccounting_loan_account (PK: loan_id) */
ALTER TABLE `sameepaccounting_loan_account` MODIFY `loan_id` INT NOT NULL;
ALTER TABLE `sameepaccounting_loan_account` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_loan_account` SET `loan_id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_loan_account` MODIFY `loan_id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`loan_id`);

/* Table: sameepaccounting_location_master (PK: location_id) */
ALTER TABLE `sameepaccounting_location_master` MODIFY `location_id` INT NOT NULL;
ALTER TABLE `sameepaccounting_location_master` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_location_master` SET `location_id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_location_master` MODIFY `location_id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`location_id`);

/* Table: sameepaccounting_material_issue_items (PK: issue_item_id) */
ALTER TABLE `sameepaccounting_material_issue_items` MODIFY `issue_item_id` INT NOT NULL;
ALTER TABLE `sameepaccounting_material_issue_items` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_material_issue_items` SET `issue_item_id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_material_issue_items` MODIFY `issue_item_id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`issue_item_id`);

/* Table: sameepaccounting_material_issue_slips (PK: issue_id) */
ALTER TABLE `sameepaccounting_material_issue_slips` MODIFY `issue_id` INT NOT NULL;
ALTER TABLE `sameepaccounting_material_issue_slips` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_material_issue_slips` SET `issue_id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_material_issue_slips` MODIFY `issue_id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`issue_id`);

/* Table: sameepaccounting_moc (PK: moc_id) */
ALTER TABLE `sameepaccounting_moc` MODIFY `moc_id` INT NOT NULL;
ALTER TABLE `sameepaccounting_moc` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_moc` SET `moc_id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_moc` MODIFY `moc_id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`moc_id`);

/* Table: sameepaccounting_notifications (PK: notification_id) */
ALTER TABLE `sameepaccounting_notifications` MODIFY `notification_id` INT NOT NULL;
ALTER TABLE `sameepaccounting_notifications` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_notifications` SET `notification_id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_notifications` MODIFY `notification_id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`notification_id`);

/* Table: sameepaccounting_opening_balance (PK: balance_id) */
ALTER TABLE `sameepaccounting_opening_balance` MODIFY `balance_id` INT NOT NULL;
ALTER TABLE `sameepaccounting_opening_balance` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_opening_balance` SET `balance_id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_opening_balance` MODIFY `balance_id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`balance_id`);

/* Table: sameepaccounting_opening_balances (PK: id) */
ALTER TABLE `sameepaccounting_opening_balances` MODIFY `id` INT NOT NULL;
ALTER TABLE `sameepaccounting_opening_balances` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_opening_balances` SET `id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_opening_balances` MODIFY `id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`id`);

/* Table: sameepaccounting_payment_in (PK: payment_id) */
ALTER TABLE `sameepaccounting_payment_in` MODIFY `payment_id` INT NOT NULL;
ALTER TABLE `sameepaccounting_payment_in` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_payment_in` SET `payment_id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_payment_in` MODIFY `payment_id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`payment_id`);

/* Table: sameepaccounting_payment_methods (PK: payment_method_id) */
ALTER TABLE `sameepaccounting_payment_methods` MODIFY `payment_method_id` INT NOT NULL;
ALTER TABLE `sameepaccounting_payment_methods` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_payment_methods` SET `payment_method_id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_payment_methods` MODIFY `payment_method_id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`payment_method_id`);

/* Table: sameepaccounting_payment_out (PK: payment_id) */
ALTER TABLE `sameepaccounting_payment_out` MODIFY `payment_id` INT NOT NULL;
ALTER TABLE `sameepaccounting_payment_out` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_payment_out` SET `payment_id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_payment_out` MODIFY `payment_id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`payment_id`);

/* Table: sameepaccounting_payment_terms (PK: payment_term_id) */
ALTER TABLE `sameepaccounting_payment_terms` MODIFY `payment_term_id` INT NOT NULL;
ALTER TABLE `sameepaccounting_payment_terms` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_payment_terms` SET `payment_term_id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_payment_terms` MODIFY `payment_term_id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`payment_term_id`);

/* Table: sameepaccounting_permission (PK: permission_id) */
ALTER TABLE `sameepaccounting_permission` MODIFY `permission_id` INT NOT NULL;
ALTER TABLE `sameepaccounting_permission` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_permission` SET `permission_id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_permission` MODIFY `permission_id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`permission_id`);

/* Table: sameepaccounting_po_amendments (PK: amendment_id) */
ALTER TABLE `sameepaccounting_po_amendments` MODIFY `amendment_id` INT NOT NULL;
ALTER TABLE `sameepaccounting_po_amendments` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_po_amendments` SET `amendment_id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_po_amendments` MODIFY `amendment_id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`amendment_id`);

/* Table: sameepaccounting_po_approvals (PK: approval_id) */
ALTER TABLE `sameepaccounting_po_approvals` MODIFY `approval_id` INT NOT NULL;
ALTER TABLE `sameepaccounting_po_approvals` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_po_approvals` SET `approval_id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_po_approvals` MODIFY `approval_id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`approval_id`);

/* Table: sameepaccounting_po_email_logs (PK: log_id) */
ALTER TABLE `sameepaccounting_po_email_logs` MODIFY `log_id` INT NOT NULL;
ALTER TABLE `sameepaccounting_po_email_logs` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_po_email_logs` SET `log_id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_po_email_logs` MODIFY `log_id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`log_id`);

/* Table: sameepaccounting_po_emails (PK: email_id) */
ALTER TABLE `sameepaccounting_po_emails` MODIFY `email_id` INT NOT NULL;
ALTER TABLE `sameepaccounting_po_emails` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_po_emails` SET `email_id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_po_emails` MODIFY `email_id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`email_id`);

/* Table: sameepaccounting_po_total (PK: id) */
ALTER TABLE `sameepaccounting_po_total` MODIFY `id` INT NOT NULL;
ALTER TABLE `sameepaccounting_po_total` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_po_total` SET `id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_po_total` MODIFY `id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`id`);

/* Table: sameepaccounting_pr_approval_history (PK: history_id) */
ALTER TABLE `sameepaccounting_pr_approval_history` MODIFY `history_id` INT NOT NULL;
ALTER TABLE `sameepaccounting_pr_approval_history` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_pr_approval_history` SET `history_id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_pr_approval_history` MODIFY `history_id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`history_id`);

/* Table: sameepaccounting_product_master (PK: product_master_id) */
ALTER TABLE `sameepaccounting_product_master` MODIFY `product_master_id` INT NOT NULL;
ALTER TABLE `sameepaccounting_product_master` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_product_master` SET `product_master_id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_product_master` MODIFY `product_master_id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`product_master_id`);

/* Table: sameepaccounting_proforma_invoice (PK: invoice_id) */
ALTER TABLE `sameepaccounting_proforma_invoice` MODIFY `invoice_id` INT NOT NULL;
ALTER TABLE `sameepaccounting_proforma_invoice` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_proforma_invoice` SET `invoice_id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_proforma_invoice` MODIFY `invoice_id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`invoice_id`);

/* Table: sameepaccounting_proforma_invoice_payment_gst (PK: invocie_pay_id) */
ALTER TABLE `sameepaccounting_proforma_invoice_payment_gst` MODIFY `invocie_pay_id` INT NOT NULL;
ALTER TABLE `sameepaccounting_proforma_invoice_payment_gst` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_proforma_invoice_payment_gst` SET `invocie_pay_id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_proforma_invoice_payment_gst` MODIFY `invocie_pay_id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`invocie_pay_id`);

/* Table: sameepaccounting_proforma_invoice_total (PK: id) */
ALTER TABLE `sameepaccounting_proforma_invoice_total` MODIFY `id` INT NOT NULL;
ALTER TABLE `sameepaccounting_proforma_invoice_total` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_proforma_invoice_total` SET `id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_proforma_invoice_total` MODIFY `id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`id`);

/* Table: sameepaccounting_proforma_payment_gst (PK: invocie_pay_id) */
ALTER TABLE `sameepaccounting_proforma_payment_gst` MODIFY `invocie_pay_id` INT NOT NULL;
ALTER TABLE `sameepaccounting_proforma_payment_gst` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_proforma_payment_gst` SET `invocie_pay_id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_proforma_payment_gst` MODIFY `invocie_pay_id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`invocie_pay_id`);

/* Table: sameepaccounting_project (PK: project_id) */
ALTER TABLE `sameepaccounting_project` MODIFY `project_id` INT NOT NULL;
ALTER TABLE `sameepaccounting_project` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_project` SET `project_id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_project` MODIFY `project_id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`project_id`);

/* Table: sameepaccounting_purchase_bill (PK: po_bill_id) */
ALTER TABLE `sameepaccounting_purchase_bill` MODIFY `po_bill_id` INT NOT NULL;
ALTER TABLE `sameepaccounting_purchase_bill` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_purchase_bill` SET `po_bill_id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_purchase_bill` MODIFY `po_bill_id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`po_bill_id`);

/* Table: sameepaccounting_purchase_bill_payment_gst (PK: purchase_pay_id) */
ALTER TABLE `sameepaccounting_purchase_bill_payment_gst` MODIFY `purchase_pay_id` INT NOT NULL;
ALTER TABLE `sameepaccounting_purchase_bill_payment_gst` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_purchase_bill_payment_gst` SET `purchase_pay_id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_purchase_bill_payment_gst` MODIFY `purchase_pay_id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`purchase_pay_id`);

/* Table: sameepaccounting_purchase_bill_total (PK: id) */
ALTER TABLE `sameepaccounting_purchase_bill_total` MODIFY `id` INT NOT NULL;
ALTER TABLE `sameepaccounting_purchase_bill_total` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_purchase_bill_total` SET `id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_purchase_bill_total` MODIFY `id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`id`);

/* Table: sameepaccounting_purchase_booked (PK: purchase_booked_id) */
ALTER TABLE `sameepaccounting_purchase_booked` MODIFY `purchase_booked_id` INT NOT NULL;
ALTER TABLE `sameepaccounting_purchase_booked` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_purchase_booked` SET `purchase_booked_id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_purchase_booked` MODIFY `purchase_booked_id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`purchase_booked_id`);

/* Table: sameepaccounting_purchase_order (PK: po_id) */
ALTER TABLE `sameepaccounting_purchase_order` MODIFY `po_id` INT NOT NULL;
ALTER TABLE `sameepaccounting_purchase_order` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_purchase_order` SET `po_id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_purchase_order` MODIFY `po_id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`po_id`);

/* Table: sameepaccounting_purchase_payment_gst (PK: purchase_pay_id) */
ALTER TABLE `sameepaccounting_purchase_payment_gst` MODIFY `purchase_pay_id` INT NOT NULL;
ALTER TABLE `sameepaccounting_purchase_payment_gst` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_purchase_payment_gst` SET `purchase_pay_id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_purchase_payment_gst` MODIFY `purchase_pay_id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`purchase_pay_id`);

/* Table: sameepaccounting_purchase_payment_history (PK: purchase_payment_id) */
ALTER TABLE `sameepaccounting_purchase_payment_history` MODIFY `purchase_payment_id` INT NOT NULL;
ALTER TABLE `sameepaccounting_purchase_payment_history` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_purchase_payment_history` SET `purchase_payment_id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_purchase_payment_history` MODIFY `purchase_payment_id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`purchase_payment_id`);

/* Table: sameepaccounting_purchase_requisition (PK: pr_id) */
ALTER TABLE `sameepaccounting_purchase_requisition` MODIFY `pr_id` INT NOT NULL;
ALTER TABLE `sameepaccounting_purchase_requisition` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_purchase_requisition` SET `pr_id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_purchase_requisition` MODIFY `pr_id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`pr_id`);

/* Table: sameepaccounting_purchase_requisition_items (PK: item_id) */
ALTER TABLE `sameepaccounting_purchase_requisition_items` MODIFY `item_id` INT NOT NULL;
ALTER TABLE `sameepaccounting_purchase_requisition_items` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_purchase_requisition_items` SET `item_id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_purchase_requisition_items` MODIFY `item_id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`item_id`);

/* Table: sameepaccounting_purchase_return (PK: po_return_id) */
ALTER TABLE `sameepaccounting_purchase_return` MODIFY `po_return_id` INT NOT NULL;
ALTER TABLE `sameepaccounting_purchase_return` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_purchase_return` SET `po_return_id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_purchase_return` MODIFY `po_return_id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`po_return_id`);

/* Table: sameepaccounting_purchase_return_total (PK: id) */
ALTER TABLE `sameepaccounting_purchase_return_total` MODIFY `id` INT NOT NULL;
ALTER TABLE `sameepaccounting_purchase_return_total` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_purchase_return_total` SET `id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_purchase_return_total` MODIFY `id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`id`);

/* Table: sameepaccounting_purchase_stock (PK: purchase_stock_id) */
ALTER TABLE `sameepaccounting_purchase_stock` MODIFY `purchase_stock_id` INT NOT NULL;
ALTER TABLE `sameepaccounting_purchase_stock` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_purchase_stock` SET `purchase_stock_id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_purchase_stock` MODIFY `purchase_stock_id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`purchase_stock_id`);

/* Table: sameepaccounting_quotation (PK: quotation_id) */
ALTER TABLE `sameepaccounting_quotation` MODIFY `quotation_id` INT NOT NULL;
ALTER TABLE `sameepaccounting_quotation` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_quotation` SET `quotation_id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_quotation` MODIFY `quotation_id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`quotation_id`);

/* Table: sameepaccounting_quotation_total (PK: id) */
ALTER TABLE `sameepaccounting_quotation_total` MODIFY `id` INT NOT NULL;
ALTER TABLE `sameepaccounting_quotation_total` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_quotation_total` SET `id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_quotation_total` MODIFY `id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`id`);

/* Table: sameepaccounting_raw_items_delivery (PK: raw_item_delivery_id) */
ALTER TABLE `sameepaccounting_raw_items_delivery` MODIFY `raw_item_delivery_id` INT NOT NULL;
ALTER TABLE `sameepaccounting_raw_items_delivery` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_raw_items_delivery` SET `raw_item_delivery_id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_raw_items_delivery` MODIFY `raw_item_delivery_id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`raw_item_delivery_id`);

/* Table: sameepaccounting_raw_items_master (PK: raw_item_master_id) */
ALTER TABLE `sameepaccounting_raw_items_master` MODIFY `raw_item_master_id` INT NOT NULL;
ALTER TABLE `sameepaccounting_raw_items_master` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_raw_items_master` SET `raw_item_master_id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_raw_items_master` MODIFY `raw_item_master_id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`raw_item_master_id`);

/* Table: sameepaccounting_raw_items_stock (PK: raw_item_stock_id) */
ALTER TABLE `sameepaccounting_raw_items_stock` MODIFY `raw_item_stock_id` INT NOT NULL;
ALTER TABLE `sameepaccounting_raw_items_stock` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_raw_items_stock` SET `raw_item_stock_id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_raw_items_stock` MODIFY `raw_item_stock_id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`raw_item_stock_id`);

/* Table: sameepaccounting_raw_mat_roll_stock (PK: id) */
ALTER TABLE `sameepaccounting_raw_mat_roll_stock` MODIFY `id` INT NOT NULL;
ALTER TABLE `sameepaccounting_raw_mat_roll_stock` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_raw_mat_roll_stock` SET `id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_raw_mat_roll_stock` MODIFY `id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`id`);

/* Table: sameepaccounting_rfq (PK: rfq_id) */
ALTER TABLE `sameepaccounting_rfq` MODIFY `rfq_id` INT NOT NULL;
ALTER TABLE `sameepaccounting_rfq` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_rfq` SET `rfq_id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_rfq` MODIFY `rfq_id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`rfq_id`);

/* Table: sameepaccounting_rfq_items (PK: rfq_item_id) */
ALTER TABLE `sameepaccounting_rfq_items` MODIFY `rfq_item_id` INT NOT NULL;
ALTER TABLE `sameepaccounting_rfq_items` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_rfq_items` SET `rfq_item_id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_rfq_items` MODIFY `rfq_item_id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`rfq_item_id`);

/* Table: sameepaccounting_rfq_suppliers (PK: id) */
ALTER TABLE `sameepaccounting_rfq_suppliers` MODIFY `id` INT NOT NULL;
ALTER TABLE `sameepaccounting_rfq_suppliers` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_rfq_suppliers` SET `id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_rfq_suppliers` MODIFY `id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`id`);

/* Table: sameepaccounting_rfq_vendor (PK: rfq_vendor_id) */
ALTER TABLE `sameepaccounting_rfq_vendor` MODIFY `rfq_vendor_id` INT NOT NULL;
ALTER TABLE `sameepaccounting_rfq_vendor` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_rfq_vendor` SET `rfq_vendor_id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_rfq_vendor` MODIFY `rfq_vendor_id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`rfq_vendor_id`);

/* Table: sameepaccounting_role (PK: role_id) */
ALTER TABLE `sameepaccounting_role` MODIFY `role_id` INT NOT NULL;
ALTER TABLE `sameepaccounting_role` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_role` SET `role_id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_role` MODIFY `role_id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`role_id`);

/* Table: sameepaccounting_sales_return (PK: sr_return_id) */
ALTER TABLE `sameepaccounting_sales_return` MODIFY `sr_return_id` INT NOT NULL;
ALTER TABLE `sameepaccounting_sales_return` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_sales_return` SET `sr_return_id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_sales_return` MODIFY `sr_return_id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`sr_return_id`);

/* Table: sameepaccounting_sales_return_total (PK: id) */
ALTER TABLE `sameepaccounting_sales_return_total` MODIFY `id` INT NOT NULL;
ALTER TABLE `sameepaccounting_sales_return_total` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_sales_return_total` SET `id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_sales_return_total` MODIFY `id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`id`);

/* Table: sameepaccounting_salesorder (PK: salesorder_id) */
ALTER TABLE `sameepaccounting_salesorder` MODIFY `salesorder_id` INT NOT NULL;
ALTER TABLE `sameepaccounting_salesorder` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_salesorder` SET `salesorder_id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_salesorder` MODIFY `salesorder_id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`salesorder_id`);

/* Table: sameepaccounting_salesorder_total (PK: id) */
ALTER TABLE `sameepaccounting_salesorder_total` MODIFY `id` INT NOT NULL;
ALTER TABLE `sameepaccounting_salesorder_total` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_salesorder_total` SET `id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_salesorder_total` MODIFY `id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`id`);

/* Table: sameepaccounting_service_order (PK: service_order_id) */
ALTER TABLE `sameepaccounting_service_order` MODIFY `service_order_id` INT NOT NULL;
ALTER TABLE `sameepaccounting_service_order` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_service_order` SET `service_order_id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_service_order` MODIFY `service_order_id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`service_order_id`);

/* Table: sameepaccounting_service_order_total (PK: id) */
ALTER TABLE `sameepaccounting_service_order_total` MODIFY `id` INT NOT NULL;
ALTER TABLE `sameepaccounting_service_order_total` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_service_order_total` SET `id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_service_order_total` MODIFY `id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`id`);

/* Table: sameepaccounting_settings (PK: setting_id) */
ALTER TABLE `sameepaccounting_settings` MODIFY `setting_id` INT NOT NULL;
ALTER TABLE `sameepaccounting_settings` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_settings` SET `setting_id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_settings` MODIFY `setting_id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`setting_id`);

/* Table: sameepaccounting_sidebar_menu (PK: id) */
ALTER TABLE `sameepaccounting_sidebar_menu` MODIFY `id` INT NOT NULL;
ALTER TABLE `sameepaccounting_sidebar_menu` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_sidebar_menu` SET `id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_sidebar_menu` MODIFY `id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`id`);

/* Table: sameepaccounting_sold_stock (PK: sold_stock_id) */
ALTER TABLE `sameepaccounting_sold_stock` MODIFY `sold_stock_id` INT NOT NULL;
ALTER TABLE `sameepaccounting_sold_stock` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_sold_stock` SET `sold_stock_id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_sold_stock` MODIFY `sold_stock_id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`sold_stock_id`);

/* Table: sameepaccounting_stock_allocations (PK: id) */
ALTER TABLE `sameepaccounting_stock_allocations` MODIFY `id` INT NOT NULL;
ALTER TABLE `sameepaccounting_stock_allocations` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_stock_allocations` SET `id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_stock_allocations` MODIFY `id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`id`);

/* Table: sameepaccounting_stock_ledger (PK: ledger_id) */
ALTER TABLE `sameepaccounting_stock_ledger` MODIFY `ledger_id` INT NOT NULL;
ALTER TABLE `sameepaccounting_stock_ledger` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_stock_ledger` SET `ledger_id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_stock_ledger` MODIFY `ledger_id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`ledger_id`);

/* Table: sameepaccounting_stock_verification_items (PK: verification_item_id) */
ALTER TABLE `sameepaccounting_stock_verification_items` MODIFY `verification_item_id` INT NOT NULL;
ALTER TABLE `sameepaccounting_stock_verification_items` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_stock_verification_items` SET `verification_item_id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_stock_verification_items` MODIFY `verification_item_id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`verification_item_id`);

/* Table: sameepaccounting_stock_verifications (PK: verification_id) */
ALTER TABLE `sameepaccounting_stock_verifications` MODIFY `verification_id` INT NOT NULL;
ALTER TABLE `sameepaccounting_stock_verifications` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_stock_verifications` SET `verification_id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_stock_verifications` MODIFY `verification_id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`verification_id`);

/* Table: sameepaccounting_storage_locations (PK: location_id) */
ALTER TABLE `sameepaccounting_storage_locations` MODIFY `location_id` INT NOT NULL;
ALTER TABLE `sameepaccounting_storage_locations` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_storage_locations` SET `location_id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_storage_locations` MODIFY `location_id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`location_id`);

/* Table: sameepaccounting_subasset (PK: subasset_id) */
ALTER TABLE `sameepaccounting_subasset` MODIFY `subasset_id` INT NOT NULL;
ALTER TABLE `sameepaccounting_subasset` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_subasset` SET `subasset_id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_subasset` MODIFY `subasset_id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`subasset_id`);

/* Table: sameepaccounting_subliabilities (PK: subliabilities_id) */
ALTER TABLE `sameepaccounting_subliabilities` MODIFY `subliabilities_id` INT NOT NULL;
ALTER TABLE `sameepaccounting_subliabilities` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_subliabilities` SET `subliabilities_id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_subliabilities` MODIFY `subliabilities_id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`subliabilities_id`);

/* Table: sameepaccounting_supplier (PK: supplier_id) */
ALTER TABLE `sameepaccounting_supplier` MODIFY `supplier_id` INT NOT NULL;
ALTER TABLE `sameepaccounting_supplier` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_supplier` SET `supplier_id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_supplier` MODIFY `supplier_id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`supplier_id`);

/* Table: sameepaccounting_supplier_backup (PK: supplier_id) */
ALTER TABLE `sameepaccounting_supplier_backup` MODIFY `supplier_id` INT NOT NULL;
ALTER TABLE `sameepaccounting_supplier_backup` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_supplier_backup` SET `supplier_id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_supplier_backup` MODIFY `supplier_id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`supplier_id`);

/* Table: sameepaccounting_units (PK: unit_id) */
ALTER TABLE `sameepaccounting_units` MODIFY `unit_id` INT NOT NULL;
ALTER TABLE `sameepaccounting_units` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_units` SET `unit_id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_units` MODIFY `unit_id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`unit_id`);

/* Table: sameepaccounting_user (PK: user_id) */
ALTER TABLE `sameepaccounting_user` MODIFY `user_id` INT NOT NULL;
ALTER TABLE `sameepaccounting_user` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_user` SET `user_id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_user` MODIFY `user_id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`user_id`);

/* Table: sameepaccounting_user_roles (PK: user_role_id) */
ALTER TABLE `sameepaccounting_user_roles` MODIFY `user_role_id` INT NOT NULL;
ALTER TABLE `sameepaccounting_user_roles` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_user_roles` SET `user_role_id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_user_roles` MODIFY `user_role_id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`user_role_id`);

/* Table: sameepaccounting_vendor_quotation_items (PK: id) */
ALTER TABLE `sameepaccounting_vendor_quotation_items` MODIFY `id` INT NOT NULL;
ALTER TABLE `sameepaccounting_vendor_quotation_items` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_vendor_quotation_items` SET `id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_vendor_quotation_items` MODIFY `id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`id`);

/* Table: sameepaccounting_vendor_quotations (PK: quotation_id) */
ALTER TABLE `sameepaccounting_vendor_quotations` MODIFY `quotation_id` INT NOT NULL;
ALTER TABLE `sameepaccounting_vendor_quotations` DROP PRIMARY KEY;
SET @count = 0;
UPDATE `sameepaccounting_vendor_quotations` SET `quotation_id` = (@count:=@count+1);
ALTER TABLE `sameepaccounting_vendor_quotations` MODIFY `quotation_id` INT NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`quotation_id`);

SET FOREIGN_KEY_CHECKS = 1;
