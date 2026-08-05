-- Add expense_type and expense_month columns to sameepaccounting_expense table
-- Run this script in your database (sameep_erp)

ALTER TABLE `sameepaccounting_expense`
  ADD COLUMN IF NOT EXISTS `expense_type` VARCHAR(50) NOT NULL DEFAULT '' AFTER `expense_category`,
  ADD COLUMN IF NOT EXISTS `expense_month` VARCHAR(20) NOT NULL DEFAULT '' AFTER `expense_type`;
