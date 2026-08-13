-- ============================================================
-- OVERRUN CONTROL PHASE 2 MIGRATION
-- Database: ultimate-manufacturing-erp
-- Tables prefix: sameepaccounting_
-- ============================================================

-- Step 1: Add cumulative MI tracking columns to material_issue_items
ALTER TABLE `sameepaccounting_material_issue_items`
    ADD COLUMN IF NOT EXISTS `previous_issued_qty` DECIMAL(15,4) NOT NULL DEFAULT 0.0000 COMMENT 'Previous non-cancelled issued qty for this JO + Item',
    ADD COLUMN IF NOT EXISTS `total_mi_qty`         DECIMAL(15,4) NOT NULL DEFAULT 0.0000 COMMENT 'previous_issued_qty + current quantity';

-- Step 2: Add allowed_overrun_pct override column to BOM items table (sameepaccounting_bom)
ALTER TABLE `sameepaccounting_bom`
    ADD COLUMN IF NOT EXISTS `allowed_overrun_pct` DECIMAL(8,4) NULL DEFAULT NULL COMMENT 'Overrun % override for this material in BOM. NULL = use inventory default';
