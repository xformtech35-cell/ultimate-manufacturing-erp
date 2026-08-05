<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Format a number using Indian numbering system (e.g. 12,34,567.00)
 */
if (!function_exists('indian_number_format')) {
    function indian_number_format($number, $decimals = 2) {
        $number = number_format((float)$number, $decimals, '.', '');
        $parts = explode('.', $number);
        $integer = $parts[0];
        $decimal = isset($parts[1]) ? '.' . $parts[1] : '';
        $negative = '';
        if (strlen($integer) > 0 && $integer[0] === '-') {
            $negative = '-';
            $integer = substr($integer, 1);
        }
        $len = strlen($integer);
        if ($len <= 3) {
            return $negative . $integer . $decimal;
        }
        $last3 = substr($integer, -3);
        $remaining = substr($integer, 0, $len - 3);
        $result = '';
        while (strlen($remaining) > 2) {
            $result = ',' . substr($remaining, -2) . $result;
            $remaining = substr($remaining, 0, strlen($remaining) - 2);
        }
        return $negative . $remaining . $result . ',' . $last3 . $decimal;
    }
}

function number_to_word($number = '')
{
    // Convert input to float for proper handling
    $number = (float)$number;

    $no = floor($number);
    $point = round(($number - $no) * 100);

    // Ensure point is an integer
    $point = (int)$point;

    $hundred = null;
    $digits_1 = strlen((string)$no);
    $i = 0;
    $str = array();
    $words = array(
        '0' => '',
        '1' => 'One',
        '2' => 'Two',
        '3' => 'Three',
        '4' => 'Four',
        '5' => 'Five',
        '6' => 'Six',
        '7' => 'Seven',
        '8' => 'Eight',
        '9' => 'Nine',
        '10' => 'Ten',
        '11' => 'Eleven',
        '12' => 'Twelve',
        '13' => 'Thirteen',
        '14' => 'Fourteen',
        '15' => 'Fifteen',
        '16' => 'Sixteen',
        '17' => 'Seventeen',
        '18' => 'Eighteen',
        '19' => 'Nineteen',
        '20' => 'Twenty',
        '30' => 'Thirty',
        '40' => 'Forty',
        '50' => 'Fifty',
        '60' => 'Sixty',
        '70' => 'Seventy',
        '80' => 'Eighty',
        '90' => 'Ninety'
    );

    $digits = array('', 'hundred', 'thousand', 'lakh', 'crore');

    while ($i < $digits_1) {
        $divider = ($i == 2) ? 10 : 100;
        $number = floor($no % $divider);
        $no = floor($no / $divider);
        $i += ($divider == 10) ? 1 : 2;
        if ($number) {
            $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
            $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
            $str[] = ($number < 21) ? $words[(string)$number] .
                " " . $digits[$counter] . $plural . " " . $hundred
                :
                $words[(string)(floor($number / 10) * 10)]
                . " " . $words[(string)($number % 10)] . " "
                . $digits[$counter] . $plural . " " . $hundred;
        } else {
            $str[] = null;
        }
    }

    $str = array_reverse($str);
    $result = implode('', $str);

    // Fix for the point calculation - handle paise properly
    $points = '';
    if ($point > 0) {
        $tens = floor($point / 10);
        $ones = $point % 10;

        if ($point < 21) {
            $points = " . " . $words[(string)$point] . " Paise";
        } else {
            $points = " . " . $words[(string)($tens * 10)];
            if ($ones > 0) {
                $points .= " " . $words[(string)$ones];
            }
            $points .= " Paise";
        }
    }

    // Clean up the result
    $result = trim_all($result);

    if ($points) {
        return $result . " Rupees " . $points;
    }

    return $result . " Rupees";
}



function trim_all( $str , $what = NULL , $with = ' ' )
{
    if( $what === NULL )
    {
        //  Character      Decimal      Use
        //  "\0"            0           Null Character
        //  "\t"            9           Tab
        //  "\n"           10           New line
        //  "\x0B"         11           Vertical Tab
        //  "\r"           13           New Line in Mac
        //  " "            32           Space
       
        $what   = "\\x00-\\x20";    //all white-spaces and control chars
    }
   
    return trim( preg_replace( "/[".$what."]+/" , $with , $str ) , $what );
}


function str_replace_last( $search , $replace , $str ) {
    if( ( $pos = strrpos( $str , $search ) ) !== false ) {
        $search_length  = strlen( $search );
        $str    = substr_replace( $str , $replace , $pos , $search_length );
    }
    return $str;
}