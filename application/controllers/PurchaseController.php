<?php
defined('BASEPATH') or exit('No direct script access allowed');

class PurchaseController extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('login');
        $this->load->model('supplier');
        $this->load->model('inventory');
        $this->load->model('estimate');
        $this->load->library('session');
    }

    public function create()
    {
        $queryString = !empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '';
        redirect('SupplierController/create_purchase_order' . $queryString);
    }

    public function index()
    {
        redirect('SupplierController/view_purchase_order');
    }

    public function _remap($method, $params = array())
    {
        if (method_exists($this, $method)) {
            return call_user_func_array(array($this, $method), $params);
        }
        $queryString = !empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '';
        redirect('SupplierController/create_purchase_order' . $queryString);
    }
}
