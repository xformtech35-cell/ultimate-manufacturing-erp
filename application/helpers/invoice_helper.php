<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('increment_invoice_name')) {
    function increment_invoice_name($invoiceName)
    {
        if (preg_match('/^(.*?)(\d+)(\/\d{2}-\d{2})$/', $invoiceName, $matches)) {
            $prefix = $matches[1];
            $numberText = $matches[2];
            $suffix = $matches[3];
            $incrementedNumber = str_pad((string) ((int) $numberText + 1), strlen($numberText), '0', STR_PAD_LEFT);

            return $prefix . $incrementedNumber . $suffix;
        }

        if (preg_match('/^(.*?)(\d+)(\D*)$/', $invoiceName, $matches)) {
            $prefix = $matches[1];
            $numberText = $matches[2];
            $suffix = $matches[3];
            $incrementedNumber = str_pad((string) ((int) $numberText + 1), strlen($numberText), '0', STR_PAD_LEFT);

            return $prefix . $incrementedNumber . $suffix;
        }

        return $invoiceName;
    }
}

if (!function_exists('extract_invoice_sequence')) {
    function extract_invoice_sequence($invoiceName)
    {
        if (preg_match('/^(.*?)(\d+)(\/\d{2}-\d{2})$/', $invoiceName, $matches)) {
            return (int) $matches[2];
        }

        if (preg_match('/^(.*?)(\d+)(\D*)$/', $invoiceName, $matches)) {
            return (int) $matches[2];
        }

        return 0;
    }
}

