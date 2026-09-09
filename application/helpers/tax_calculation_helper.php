<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * CANONICAL TAX CALCULATION HELPER
 * Standardizes tax base, GST rates, tax split, and totals across the entire Manufacturing ERP.
 * 
 * Rules:
 * - Taxable Subtotal = Quantity * Unit Price * (1 - Discount% / 100)
 * - GST Amount = Taxable Subtotal * GST% / 100
 * - Line Total = Taxable Subtotal + GST Amount
 * - GST Type 'I' = 100% IGST, 0% SGST, 0% CGST
 * - GST Type 'S' = 50% SGST, 50% CGST, 0% IGST
 */

if (!function_exists('parse_tax_rate')) {
    function parse_tax_rate($gst_value)
    {
        if (is_numeric($gst_value)) {
            return (float) $gst_value;
        }
        if (is_string($gst_value)) {
            $cleaned = trim(str_replace('%', '', $gst_value));
            return is_numeric($cleaned) ? (float) $cleaned : 0.00;
        }
        return 0.00;
    }
}

if (!function_exists('calculate_taxable_subtotal')) {
    function calculate_taxable_subtotal($quantity, $unit_price, $discount_pct = 0)
    {
        $qty = (float) $quantity;
        $price = (float) $unit_price;
        $discount = (float) $discount_pct;

        if ($qty <= 0 || $price <= 0) {
            return 0.00;
        }

        $gross = $qty * $price;
        if ($discount > 0) {
            $discount_multiplier = max(0.0, 1.0 - ($discount / 100.0));
            return round($gross * $discount_multiplier, 2);
        }

        return round($gross, 2);
    }
}

if (!function_exists('calculate_gst_amount')) {
    function calculate_gst_amount($taxable_subtotal, $gst_rate)
    {
        $subtotal = (float) $taxable_subtotal;
        $rate = parse_tax_rate($gst_rate);

        if ($subtotal <= 0 || $rate <= 0) {
            return 0.00;
        }

        return round(($subtotal * $rate) / 100.0, 2);
    }
}

if (!function_exists('calculate_gst_split')) {
    function calculate_gst_split($gst_amount, $gst_type = 'S')
    {
        $tax = (float) $gst_amount;
        $type = strtoupper(trim((string)$gst_type));

        if ($type === 'I') {
            return array(
                'is_igst' => true,
                'igst'    => $tax,
                'sgst'    => 0.00,
                'cgst'    => 0.00,
                'total_tax' => $tax
            );
        }

        $half = round($tax / 2.0, 2);
        return array(
            'is_igst' => false,
            'igst'    => 0.00,
            'sgst'    => $half,
            'cgst'    => $half,
            'total_tax' => $half * 2
        );
    }
}

if (!function_exists('calculate_line_item_tax')) {
    function calculate_line_item_tax($quantity, $unit_price, $discount_pct = 0, $gst_rate = 0, $gst_type = 'S')
    {
        $subtotal = calculate_taxable_subtotal($quantity, $unit_price, $discount_pct);
        $gst_amount = calculate_gst_amount($subtotal, $gst_rate);
        $split = calculate_gst_split($gst_amount, $gst_type);
        $line_total = round($subtotal + $split['total_tax'], 2);

        return array(
            'taxable_subtotal' => $subtotal,
            'gst_rate'         => parse_tax_rate($gst_rate),
            'gst_amount'       => $split['total_tax'],
            'is_igst'          => $split['is_igst'],
            'igst'             => $split['igst'],
            'sgst'             => $split['sgst'],
            'cgst'             => $split['cgst'],
            'line_total'       => $line_total
        );
    }
}
