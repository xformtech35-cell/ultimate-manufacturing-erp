-- ============================================================
-- OVERRUN CONTROL MIGRATION
-- Database: ultimate-manufacturing-erp
-- Tables use prefix: sameepaccounting_
-- Run once in phpMyAdmin or MySQL CLI
-- Adds columns ONLY - no existing data is changed
-- ============================================================

-- Step 1: Add allowed_overrun_pct to inventory table
-- Each item gets its own configurable overrun tolerance (default 2%)
ALTER TABLE `sameepaccounting_inventory`
    ADD COLUMN IF NOT EXISTS `allowed_overrun_pct` DECIMAL(8,4) NOT NULL DEFAULT 2.00
        COMMENT 'Allowed overrun percentage for Material Issue (default 2%)';

-- Step 2: Add overrun tracking columns to material_issue_items table
ALTER TABLE `sameepaccounting_material_issue_items`
    ADD COLUMN IF NOT EXISTS `bom_qty`               DECIMAL(15,4) NOT NULL DEFAULT 0.0000 COMMENT 'BOM required quantity (baseline)',
    ADD COLUMN IF NOT EXISTS `allowed_overrun_pct`   DECIMAL(8,4)  NOT NULL DEFAULT 0.0000 COMMENT 'Allowed overrun % copied from inventory at time of issue',
    ADD COLUMN IF NOT EXISTS `allowed_overrun_qty`   DECIMAL(15,4) NOT NULL DEFAULT 0.0000 COMMENT 'bom_qty x allowed_overrun_pct / 100',
    ADD COLUMN IF NOT EXISTS `max_allowed_qty`       DECIMAL(15,4) NOT NULL DEFAULT 0.0000 COMMENT 'bom_qty + allowed_overrun_qty',
    ADD COLUMN IF NOT EXISTS `overrun_qty`           DECIMAL(15,4) NOT NULL DEFAULT 0.0000 COMMENT 'quantity - bom_qty (0 if no overrun)',
    ADD COLUMN IF NOT EXISTS `overrun_pct_actual`    DECIMAL(8,4)  NOT NULL DEFAULT 0.0000 COMMENT '(quantity - bom_qty) / bom_qty * 100',
    ADD COLUMN IF NOT EXISTS `overrun_value`         DECIMAL(15,4) NOT NULL DEFAULT 0.0000 COMMENT 'overrun_qty x unit_price',
    ADD COLUMN IF NOT EXISTS `overrun_status`        ENUM('none','within_limit','approval_required','approved','rejected') NOT NULL DEFAULT 'none'
                                                        COMMENT 'Overrun approval state',
    ADD COLUMN IF NOT EXISTS `overrun_remarks`       TEXT          NULL DEFAULT NULL COMMENT 'Manager remarks on overrun approval/rejection',
    ADD COLUMN IF NOT EXISTS `overrun_approved_by`   INT(11)       NULL DEFAULT NULL COMMENT 'user_id of approver';

-- Step 3: Add index for overrun approval queries
ALTER TABLE `sameepaccounting_material_issue_items`
    ADD INDEX IF NOT EXISTS `idx_overrun_status` (`overrun_status`);
