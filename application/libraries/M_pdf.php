<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

// Load mPDF via Composer autoloader
require_once APPPATH . '../vendor/autoload.php';

class M_pdf {

    public $param;
    public $pdf;
    
    public function __construct($param = [])
    {
        $defaults = [
            'mode'   => 'utf-8',
            'format' => 'A4-L',
        ];
        $this->param = array_merge($defaults, (array) $param);
        $this->pdf = new \Mpdf\Mpdf($this->param);
    }
    
    public function load()
    {
        return $this->pdf;
    }
}