<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class ReportController extends MY_Controller {

    protected $user_id;

    function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this->load->model('report', '', TRUE);
        $this->load->model('invoice', '', TRUE);
        $this->load->model('expense', '', TRUE);
        $this->load->model('payment', '', TRUE);
        $this->load->model('inventory', '', TRUE);
        $this->load->model('login', '', TRUE);
         $this->load->model('bankdetail', '', TRUE);
         $this->load->model('salesorder', '', TRUE);
        $this->load->model('supplier', '', TRUE);
        $this->load->model('login', '', TRUE);
         $this->load->model('asset', '', TRUE);
         $this->load->model('assetbalancesheet', '', TRUE);
          $this->load->model('Liabilities', '', TRUE);
         $this->load->model('JobOrder', 'joborder', TRUE);
         $this->load->model('Material_issue_model', 'material_issue_model', TRUE);
         

         
         
        $session_data_head = $this->session->userdata('session_data_head');
        // $this->user_id = 1; // All users share main data context of user 1
        $this->user_id = $session_data_head['result']['user_id'] ?? 1;
        if (($session_data_head['result']['user_id'] ?? NULL) === NULL) {
            if (!is_cli()) {
                $this->session->sess_destroy();
                $this->session->set_flashdata('SUCCESSMSG', "You have been Logged Out !!");
                redirect('LoginController/logout');
            }
        }
    }

    private function normalize_expense_mode($mode)
      {
        $mode = strtolower(trim((string) $mode));
        if ($mode === 'direct') {
          return 'direct';
        }
        if ($mode === 'indirect') {
          return 'indirect';
        }
        return '';
      }

      private function get_expense_mode_prefix($mode)
      {
        if ($mode === 'direct') {
          return 'Direct - ';
        }
        if ($mode === 'indirect') {
          return 'Indirect - ';
        }
        return '';
      }

      private function apply_expense_mode_prefix($category, $mode)
      {
        $category = trim((string) $category);
        if ($category === '') {
          return '';
        }
        $prefix = $this->get_expense_mode_prefix($mode);

        if ($prefix === '') {
          return $category;
        }

        if (stripos($category, $prefix) === 0) {
          return $category;
        }

        return $prefix . $category;
      }

      private function filter_expense_categories_by_mode($categories, $mode)
      {
        $prefix = $this->get_expense_mode_prefix($mode);
        if ($prefix === '') {
          return $categories;
        }

        $filtered = array();
        foreach ((array) $categories as $row) {
          if (isset($row->exp_cat) && stripos($row->exp_cat, $prefix) === 0) {
            $filtered[] = $row;
          }
        }

        return $filtered;
      }

      private function filter_expense_entries_by_mode($entries, $mode)
      {
        $prefix = $this->get_expense_mode_prefix($mode);
        if ($prefix === '') {
          return $entries;
        }

        $filtered = array();
        foreach ((array) $entries as $row) {
          if (isset($row->expense_category) && stripos($row->expense_category, $prefix) === 0) {
            $filtered[] = $row;
          }
        }

        return $filtered;
      }

      private function parse_expense_category_for_report($stored_category)
      {
        $stored_category = trim((string) $stored_category);
        $mode = '';
        $label = $stored_category;

        if (stripos($stored_category, 'Direct - ') === 0) {
          $mode = 'direct';
          $label = trim(substr($stored_category, strlen('Direct - ')));
        } elseif (stripos($stored_category, 'Indirect - ') === 0) {
          $mode = 'indirect';
          $label = trim(substr($stored_category, strlen('Indirect - ')));
        }

        return array(
          'mode' => $mode,
          'label' => $label,
        );
      }

      private function get_report_expense_type_options()
      {
        return array(
          'direct' => 'Direct',
          'indirect' => 'Indirect',
        );
      }

      private function prepare_report_expense_categories($categories)
      {
        $options = array();

        foreach ((array) $categories as $row) {
          if (!isset($row->exp_cat)) {
            continue;
          }

          $parsed = $this->parse_expense_category_for_report($row->exp_cat);
          $options[] = array(
            'value' => $row->exp_cat,
            'label' => $parsed['label'],
            'mode' => $parsed['mode'],
          );
        }

        return $options;
      }

    private function get_indirect_expense_category_type($stored_category)
      {
        $stored_category = trim((string) $stored_category);
        if ($stored_category === '') {
          return '';
        }

        $parsed = $this->parse_expense_category_for_report($stored_category);
        $label = trim((string) $parsed['label']);

        // If mode parsing fails, fall back to direct prefix checks.
        if ($parsed['mode'] !== 'indirect') {
          if (stripos($stored_category, 'Indirect - ') === 0) {
            $parsed_mode_label = trim(substr($stored_category, strlen('Indirect - ')));
            $label = $parsed_mode_label;
          } else {
            return '';
          }
        }

        // Normalize label for reliable matching.
        // Expected DB formats (examples):
        // - "Individual - Transport"
        // - "Corporate - Office Rent"
        if (preg_match('/^(individual|corporate)\s*-\s*/i', $label, $matches)) {
          return strtolower(trim($matches[1]));
        }

        // Some data might include "Indirect - Individual - X" inside already-trimmed label
        // (double Indirect prefix). Handle by extracting after the second 'Indirect - '.
        if (stripos($label, 'Indirect - ') === 0) {
          $label2 = trim(substr($label, strlen('Indirect - ')));
          if (preg_match('/^(individual|corporate)\s*-\s*/i', $label2, $matches)) {
            return strtolower(trim($matches[1]));
          }
        }

        return '';
      }


      private function is_indirect_individual_category($stored_category)
      {
        return $this->get_indirect_expense_category_type($stored_category) === 'individual';
      }

      private function filter_expense_rows_by_employee($rows, $employee_name)
      {
        $employee_name = trim((string) $employee_name);
        if ($employee_name === '') {
          return (array) $rows;
        }

        $filtered = array();
        foreach ((array) $rows as $row) {
          $row_employee = isset($row->employee_name) ? trim((string) $row->employee_name) : '';
          if (strcasecmp($row_employee, $employee_name) === 0) {
            $filtered[] = $row;
          }
        }

        return $filtered;
      }


    public function report() {
        $session_data_head = $this->session->userdata('session_data_head');
        $data['company_name'] = $this->invoice->get_company_name($this->user_id);
        $data['item_name'] = $this->invoice->get_item_name($this->user_id);
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('admin/report', $data);
    }

    public function get_po_report_by_date() {
      
        $month_year = $this->input->post('month_year');
        
         $monthyear_arr = explode('-', $month_year);
        $nmonth = date('m', strtotime($monthyear_arr[0]));
        $newmonthyear_str = $monthyear_arr[1] . '-' . $nmonth;
              $month = $monthyear_arr[0];
              $year =  $monthyear_arr[1];
            
         $this->session->set_userdata('month',$month);
          $this->session->set_userdata('year',$year);
     //  print_r($month_year);die();
//        $po_dte1 = strtotime($from_date);
//        $po_date1 = date('Y-m-d', $po_dte1);
//        $po_dte2 = strtotime($to_date);
//        $po_date2 = date('Y-m-d', $po_dte2);
         $data['month'] = $month;
          $data['year'] = $year;
       //  print_r($data['month']);die();
        $data['result'] = $this->report->get_po_report_by_date($month_year, $month, $this->user_id);
      
        $session_data_head = $this->session->userdata('session_data_head');
        
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('admin/excel_report_po', $data);
    }

    public function get_inventory_report_by_date() {
        $from_date = $this->input->post('from_date');
        $to_date = $this->input->post('to_date');
        $po_date1 = !empty($from_date) ? date('Y-m-d', strtotime($from_date)) : date('Y-m-d', strtotime('-30 days'));
        $po_date2 = !empty($to_date) ? date('Y-m-d', strtotime($to_date)) : date('Y-m-d');
        $data['result'] = $this->report->get_inventory_report_by_date($po_date1, $po_date2, $this->user_id);
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('admin/excel_inventory_report', $data);
    }

    public function get_non_gst_invoice_report_by_date() {
        $from_date = $this->input->post('from_date');
        $to_date = $this->input->post('to_date');
        $po_date1 = !empty($from_date) ? date('Y-m-d', strtotime($from_date)) : date('Y-m-d', strtotime('-30 days'));
        $po_date2 = !empty($to_date) ? date('Y-m-d', strtotime($to_date)) : date('Y-m-d');

        $company_name = $this->input->post('company_name');
        $product_name = $this->input->post('product_name');

        if ($product_name) {
            $data['from_date'] = $po_date1;
            $data['to_date'] = $po_date2;
            $data['item_wise_report'] = $this->report->get_non_gst_itemwise_report_by_date($po_date1, $po_date2, $this->user_id, $product_name, $company_name);

            $html = $this->load->view('admin/non_gst_item_wise_invoice_report', $data, true);
            //$pdfFilePath = "invoice.pdf";
            $pdfFilePath = "NON-GST-InvoiceItemWiseReport.pdf";
            //load mPDF library
            $this->load->library('M_pdf');
            //generate the PDF from the given html
            $this->m_pdf->pdf->WriteHTML($html);
            //download it.
            $this->m_pdf->pdf->Output($pdfFilePath, "D");
        } else {
            $data['result'] = $this->report->get_non_gst_invoice_report_by_date($po_date1, $po_date2, $company_name, $this->user_id);
            $session_data_head = $this->session->userdata('session_data_head');
            $this->load->view('admin/header_side_bar', $session_data_head);
            $this->load->view('admin/excel_report_non_gst_invoice', $data);
        }
    }

    public function get_report_by_date() {
        $from_date = $this->input->post('from_date');
        $to_date = $this->input->post('to_date');
        $company_name = $this->input->post('company_name');
        $product_name = $this->input->post('product_name');
        $to_date = $this->input->post('to_date');
        $po_date1 = !empty($from_date) ? date('Y-m-d', strtotime($from_date)) : date('Y-m-d', strtotime('-30 days'));
        $po_date2 = !empty($to_date) ? date('Y-m-d', strtotime($to_date)) : date('Y-m-d');
        if ($product_name) {
            $data['from_date'] = $po_date1;
            $data['to_date'] = $po_date2;
            $data['item_wise_report'] = $this->report->get_itemwise_report_by_date($po_date1, $po_date2, $this->user_id, $product_name, $company_name);

            $html = $this->load->view('admin/item_wise_invoice_report', $data, true);
            //$pdfFilePath = "invoice.pdf";
            $pdfFilePath = "InvoiceItemWiseReport.pdf";
            //load mPDF library
            $this->load->library('M_pdf');
            //generate the PDF from the given html
            $this->m_pdf->pdf->WriteHTML($html);
            //download it.
            $this->m_pdf->pdf->Output($pdfFilePath, "D");
        } else {
            $data['result'] = $this->report->get_report_by_date($po_date1, $po_date2, $company_name, $this->user_id);
            $session_data_head = $this->session->userdata('session_data_head');
            $this->load->view('admin/header_side_bar', $session_data_head);
            $this->load->view('admin/excel_report', $data);
        }
    }
    
    
    public function create_inventory_report() {
         $filters = array(
             'item_name' => trim((string) $this->input->get('item_name')),
             'unit' => trim((string) $this->input->get('unit')),
             'item_type' => trim((string) $this->input->get('item_type'))
         );

         $data['selected_filters'] = $filters;
         $data['filter_options'] = $this->inventory->get_inventory_filter_options($this->user_id);
         $data['result'] = $this->inventory->get_inventory_report($this->user_id, $filters);
        
        $session_data_head = $this->session->userdata('session_data_head');
       
       $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('report/create_inventory_report',$data);
        
    }
    
      public function get_inventory_report() {
        $filters = array(
            'item_name' => trim((string) $this->input->get('item_name')),
            'unit' => trim((string) $this->input->get('unit')),
            'item_type' => trim((string) $this->input->get('item_type'))
        );

        $data['selected_filters'] = $filters;
        $data['result'] = $this->inventory->get_inventory_report($this->user_id, $filters);
       
        // DO NOT load header - this is an Excel export
        $this->load->view('report/inventory_report', $data);
       
    }
    
    
    
       public function create_quotation_report() {
      
         $from_date = $this->input->post('from_date');
        $to_date = $this->input->post('to_date');
        $company_name = $this->input->post('company_name');
        $product_name = $this->input->post('product_name');
        
        // Add null checks before strtotime
        $po_dte1 = !empty($from_date) ? strtotime($from_date) : strtotime('now');
        $po_date1 = date('Y-m-d', $po_dte1);
        $po_dte2 = !empty($to_date) ? strtotime($to_date) : strtotime('now');
        $po_date2 = date('Y-m-d', $po_dte2);
        
        $data['result'] = $this->report->get_quotation_report_by_date($po_date1, $po_date2, $company_name, $this->user_id);

        //print_r($data['result']);die();
        
        $this->session->set_userdata('from_date', $from_date);
        $this->session->set_userdata('to_date', $to_date);
        
        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
        
        $session_data_head = $this->session->userdata('session_data_head');
        $data['company_name'] = $this->invoice->get_company_name($this->user_id);
        $data['item_name'] = $this->invoice->get_item_name($this->user_id);
       $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('report/create_quotation_report',$data);
        
    }
     public function get_quotation_report() {
        //echo "jjjjjjj" ;die();
      
        $from_date = $this->session->userdata('from_date');
        $to_date = $this->session->userdata('to_date');
        $company_name = $this->input->post('company_name');
        $product_name = $this->input->post('product_name');
        
      //  print_r($to_date);die();
        $po_date1 = !empty($from_date) ? date('Y-m-d', strtotime($from_date)) : date('Y-m-d', strtotime('-30 days'));
        $po_date2 = !empty($to_date) ? date('Y-m-d', strtotime($to_date)) : date('Y-m-d');
        
        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
        $data['result'] = $this->report->get_quotation_report_by_date($po_date1, $po_date2, $company_name, $this->user_id);
        //var_dump( $data['result']);die();
        $this->session->unset_userdata('from_date');
        $this->session->unset_userdata('to_date');
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('report/quotation_report', $data);
       
    }
    
    

    public function create_sales_report() {
        
        $from_date = $this->input->post('from_date');
        $to_date = $this->input->post('to_date');
       
        // Add null checks before strtotime
        $po_dte1 = !empty($from_date) ? strtotime($from_date) : strtotime('now');
        $po_date1 = date('Y-m-d', $po_dte1);
        $po_dte2 = !empty($to_date) ? strtotime($to_date) : strtotime('now');
        $po_date2 = date('Y-m-d', $po_dte2);
        $data['result'] = $this->report->get_report_by_date($po_date1, $po_date2, $this->user_id);

        //print_r($data['result']);die();create_sales_report
        
        $this->session->set_userdata('from_date', $from_date);
        $this->session->set_userdata('to_date', $to_date);
        
        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
        
        $session_data_head = $this->session->userdata('session_data_head');
        $data['company_name'] = $this->invoice->get_company_name($this->user_id);
        $data['item_name'] = $this->invoice->get_item_name($this->user_id);
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('report/create_sales_report', $data);
    }

    public function create_sales_hsn_report() {
        
        $from_date = $this->input->post('from_date');
        $to_date = $this->input->post('to_date');
       
        // Add null checks before strtotime
        $po_dte1 = !empty($from_date) ? strtotime($from_date) : strtotime('now');
        $po_date1 = date('Y-m-d', $po_dte1);
        $po_dte2 = !empty($to_date) ? strtotime($to_date) : strtotime('now');
        $po_date2 = date('Y-m-d', $po_dte2);
        $data['result'] = $this->report->get_sales_report_by_hsn($po_date1, $po_date2, $this->user_id);

        //print_r($data['result']);die();create_sales_hsn_report
        
        $this->session->set_userdata('from_date', $from_date);
        $this->session->set_userdata('to_date', $to_date);
        
        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
        
        $session_data_head = $this->session->userdata('session_data_head');
        $data['company_name'] = $this->invoice->get_company_name($this->user_id);
        $data['item_name'] = $this->invoice->get_item_name($this->user_id);
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('report/create_sales_hsn_report', $data);
    }

    public function get_sales_report_by_date_xlsx() {
       // echo "djjdjf";die();
        $from_date = $this->session->userdata('from_date');
        $to_date = $this->session->userdata('to_date');
        $company_name = $this->input->post('company_name');
        $product_name = $this->input->post('product_name');
        $po_date1 = !empty($from_date) ? date('Y-m-d', strtotime($from_date)) : date('Y-m-d', strtotime('-30 days'));
        $po_date2 = !empty($to_date) ? date('Y-m-d', strtotime($to_date)) : date('Y-m-d');
        
        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
        $data['result'] = $this->report->get_report_by_date($po_date1, $po_date2, $this->user_id);
      //  print_r( $data['result']);die();
        $this->session->unset_userdata('from_date');
        $this->session->unset_userdata('to_date');
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('report/sales_report', $data);
    }

      public function get_sales_hsn_report_by_date_xlsx() {
         // echo "djjdjf";die();
        $from_date = $this->session->userdata('from_date');
        $to_date = $this->session->userdata('to_date');
        $po_date1 = !empty($from_date) ? date('Y-m-d', strtotime($from_date)) : date('Y-m-d', strtotime('-30 days'));
        $po_date2 = !empty($to_date) ? date('Y-m-d', strtotime($to_date)) : date('Y-m-d');

        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
        $data['result'] = $this->report->get_sales_report_by_hsn($po_date1, $po_date2, $this->user_id);
        $this->session->unset_userdata('from_date');
        $this->session->unset_userdata('to_date');
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('report/sales_hsn_report', $data);
      }

    public function create_purchase_report() {
        
         $from_date = $this->input->post('from_date');
        $to_date = $this->input->post('to_date');
        $company_name = $this->input->post('company_name');
        
        // Add null checks before strtotime
        $po_dte1 = !empty($from_date) ? strtotime($from_date) : strtotime('now');
        $po_date1 = date('Y-m-d', $po_dte1);
        $po_dte2 = !empty($to_date) ? strtotime($to_date) : strtotime('now');
        $po_date2 = date('Y-m-d', $po_dte2);
        $data['result'] = $this->report->get_purchase_bill_report_by_date($po_date1, $po_date2, $this->user_id);

       // print_r($data['result']);die();
        
        $this->session->set_userdata('from_date', $from_date);
        $this->session->set_userdata('to_date', $to_date);
        
        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
        
        $session_data_head = $this->session->userdata('session_data_head');
        $data['company_name'] = $this->invoice->get_company_name($this->user_id);
        $data['item_name'] = $this->invoice->get_item_name($this->user_id);
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('report/create_purchase_report', $data);
        
      }

      public function create_joborder_report() {
          $from_date = $this->input->post('from_date');
          $to_date = $this->input->post('to_date');

          $po_dte1 = !empty($from_date) ? strtotime($from_date) : strtotime('now');
          $po_date1 = date('Y-m-d', $po_dte1);
          $po_dte2 = !empty($to_date) ? strtotime($to_date) : strtotime('now');
          $po_date2 = date('Y-m-d', $po_dte2);

          $data['result'] = $this->report->get_joborder_report_by_date($po_date1, $po_date2, $this->user_id);

          $this->session->set_userdata('from_date', $from_date);
          $this->session->set_userdata('to_date', $to_date);
          $data['from_date'] = $from_date;
          $data['to_date'] = $to_date;

          $session_data_head = $this->session->userdata('session_data_head');
          $data['company_name'] = $this->invoice->get_company_name($this->user_id);
          $data['item_name'] = $this->invoice->get_item_name($this->user_id);
          $this->load->view('admin/header_side_bar', $session_data_head);
          $this->load->view('report/create_joborder_report', $data);
      }

      public function get_joborder_report_by_date_xlsx() {
        $from_date = $this->session->userdata('from_date');
        $to_date = $this->session->userdata('to_date');

        $po_date1 = !empty($from_date) ? date('Y-m-d', strtotime($from_date)) : date('Y-m-d', strtotime('-30 days'));
        $po_date2 = !empty($to_date) ? date('Y-m-d', strtotime($to_date)) : date('Y-m-d');

        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
        $data['result'] = $this->report->get_joborder_report_by_date($po_date1, $po_date2, $this->user_id);

        $this->session->unset_userdata('from_date');
        $this->session->unset_userdata('to_date');

        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('report/export_joborder_report', $data);
      }

      public function create_joborder_item_report() {
          $from_date = $this->input->post('from_date');
          $to_date = $this->input->post('to_date');

          $data['result'] = [];
          $data['from_date'] = $from_date;
          $data['to_date'] = $to_date;
          $data['is_filtered'] = false;

          if (!empty($from_date) && !empty($to_date)) {
            $po_date1 = date('Y-m-d', strtotime($from_date));
            $po_date2 = date('Y-m-d', strtotime($to_date));

            $data['result'] = $this->report->get_joborder_items_by_date($po_date1, $po_date2, $this->user_id);
            $data['is_filtered'] = true;

            $this->session->set_userdata('from_date', $from_date);
            $this->session->set_userdata('to_date', $to_date);
          } else {
            $this->session->unset_userdata('from_date');
            $this->session->unset_userdata('to_date');
          }

          $session_data_head = $this->session->userdata('session_data_head');
          $data['company_name'] = $this->invoice->get_company_name($this->user_id);
          $data['item_name'] = $this->invoice->get_item_name($this->user_id);
          $this->load->view('admin/header_side_bar', $session_data_head);
          $this->load->view('report/create_joborder_item_report', $data);
      }

      public function get_joborder_item_report_by_date_xlsx() {
        $from_date = $this->session->userdata('from_date');
        $to_date = $this->session->userdata('to_date');

        $po_date1 = !empty($from_date) ? date('Y-m-d', strtotime($from_date)) : date('Y-m-d', strtotime('-30 days'));
        $po_date2 = !empty($to_date) ? date('Y-m-d', strtotime($to_date)) : date('Y-m-d');

        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
        $data['result'] = $this->report->get_joborder_items_by_date($po_date1, $po_date2, $this->user_id);

        $this->session->unset_userdata('from_date');
        $this->session->unset_userdata('to_date');

        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('report/export_joborder_items', $data);
      }

      public function export_joborder_items() {
        // Get job order number from query parameter
        $joborder_number = $this->input->get('jo_number');

        // URL decode in case it was encoded
        $joborder_number = urldecode($joborder_number);

        $data['result'] = $this->report->get_joborder_items_by_number($joborder_number, $this->user_id);
        $data['joborder_number'] = $joborder_number;

        $this->load->view('report/export_joborder_items', $data);
      }

      public function create_joborder_wise_report()
      {
        $data['joborder_list'] = $this->joborder->get_joborders($this->user_id);
        $data['selected_joborder_id'] = '';
        $data['joborder'] = null;
        $data['joborder_details'] = array();

        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('report/create_joborder_wise_report', $data);
      }

      public function get_joborder_wise_report()
      {
        $selected_joborder_id = (int) $this->input->post('joborder_id');
        $export_type = trim((string) $this->input->post('export_type'));

        $data['joborder_list'] = $this->joborder->get_joborders($this->user_id);
        $data['selected_joborder_id'] = $selected_joborder_id;
        $data['joborder'] = null;
        $data['joborder_details'] = array();

        if ($selected_joborder_id > 0) {
          $this->db->select('jt.id, jt.number_fk, jt.date, jt.status, jt.note, jt.project_code, jt.customer_code,
            jt.system, jt.location, jt.capacity, jt.project_qty, jt.oc_number, jt.customer_id_fk,
            c.company_name, c.fullname, c.email, c.mobile, c.gst, c.pancard, c.state_code, c.address');
          $this->db->from('joborder_total jt');
          $this->db->join('customer c', 'c.customer_id = jt.customer_id_fk', 'left');
          $this->db->where('jt.id', $selected_joborder_id);
          $this->db->where('jt.uid', $this->user_id);
          $joborder = $this->db->get()->row();

          if ($joborder) {
            $data['joborder'] = $joborder;
            $data['joborder_details'] = $this->joborder->get_joborder_data($joborder->number_fk, $this->user_id);
          }
        }

        if ($export_type === 'excel' && !empty($data['joborder'])) {
          $this->load->view('report/export_joborder_wise_report_xlsx', $data);
          return;
        }

        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('report/create_joborder_wise_report', $data);
      }

       public function create_material_issue_report()
      {
        $from_date = $this->input->post('from_date');
        $to_date = $this->input->post('to_date');
        $is_post = ($this->input->server('REQUEST_METHOD') === 'POST');

        if (empty($from_date) && empty($to_date)) {
            $current_year = date('Y');
            $from_date = (date('m') >= 4) ? '01-04-' . $current_year : '01-04-' . ($current_year - 1);
            $to_date = date('d-m-Y');
        }

        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
        $data['result'] = array();
        $data['is_filtered'] = true;

        $date1 = !empty($from_date) ? date('Y-m-d', strtotime($from_date)) : '';
        $date2 = !empty($to_date) ? date('Y-m-d', strtotime($to_date)) : '';
        $this->session->set_userdata('material_issue_report_from_date', $from_date);
        $this->session->set_userdata('material_issue_report_to_date', $to_date);

        $data['result'] = $this->material_issue_model->get_material_issue_report($date1, $date2, null);

        $session_data_head = $this->session->userdata('session_data_head');
        $data['show_project_cols'] = $this->_has_project_permission();
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('report/create_material_issue_report', $data);
      }

      public function get_material_issue_report_by_date_xlsx()
      {
        $from_date = $this->session->userdata('material_issue_report_from_date');
        $to_date = $this->session->userdata('material_issue_report_to_date');

        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
        $data['result'] = array();

        $date1 = !empty($from_date) ? date('Y-m-d', strtotime($from_date)) : '';
        $date2 = !empty($to_date) ? date('Y-m-d', strtotime($to_date)) : '';
        $data['result'] = $this->material_issue_model->get_material_issue_report($date1, $date2, null);

        $data['show_project_cols'] = $this->_has_project_permission();
        $this->load->view('report/export_material_issue_report', $data);
      }

      public function create_purchase_bill_report() {
          
          $from_date = $this->input->post('from_date');
          $to_date = $this->input->post('to_date');
          $company_name = $this->input->post('company_name');
          
          // Add null checks before strtotime
          $po_dte1 = !empty($from_date) ? strtotime($from_date) : strtotime('now');
          $po_date1 = date('Y-m-d', $po_dte1);
          $po_dte2 = !empty($to_date) ? strtotime($to_date) : strtotime('now');
          $po_date2 = date('Y-m-d', $po_dte2);
          
          // You need to create this method in your report model
          $data['result'] = $this->report->get_purchase_bill_report_by_date($po_date1, $po_date2, $this->user_id);
          
          $this->session->set_userdata('from_date', $from_date);
          $this->session->set_userdata('to_date', $to_date);
          
          $data['from_date'] = $from_date;
          $data['to_date'] = $to_date;
          
          $session_data_head = $this->session->userdata('session_data_head');
          $data['company_name'] = $this->invoice->get_company_name($this->user_id);
          $data['item_name'] = $this->invoice->get_item_name($this->user_id);
          
          $this->load->view('admin/header_side_bar', $session_data_head);
          $this->load->view('report/create_purchase_bill_report', $data);
      }

      public function create_purchase_bill_hsn_report() {
          
          $from_date = $this->input->post('from_date');
          $to_date = $this->input->post('to_date');
          
          // Add null checks before strtotime
          $po_dte1 = !empty($from_date) ? strtotime($from_date) : strtotime('now');
          $po_date1 = date('Y-m-d', $po_dte1);
          $po_dte2 = !empty($to_date) ? strtotime($to_date) : strtotime('now');
          $po_date2 = date('Y-m-d', $po_dte2);
          
          $data['result'] = $this->report->get_purchase_bill_report_by_hsn($po_date1, $po_date2, $this->user_id);
          
          $this->session->set_userdata('from_date', $from_date);
          $this->session->set_userdata('to_date', $to_date);
          
          $data['from_date'] = $from_date;
          $data['to_date'] = $to_date;
          
          $session_data_head = $this->session->userdata('session_data_head');
          $data['company_name'] = $this->invoice->get_company_name($this->user_id);
          $data['item_name'] = $this->invoice->get_item_name($this->user_id);
          
          $this->load->view('admin/header_side_bar', $session_data_head);
          $this->load->view('report/create_purchase_bill_hsn_report', $data);
      }

    public function get_purchase_report_by_date_xlsx() {
        $from_date = $this->session->userdata('from_date');
        $to_date = $this->session->userdata('to_date');
        $company_name = $this->input->post('company_name');
        $po_date1 = !empty($from_date) ? date('Y-m-d', strtotime($from_date)) : date('Y-m-d', strtotime('-30 days'));
        $po_date2 = !empty($to_date) ? date('Y-m-d', strtotime($to_date)) : date('Y-m-d');
        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;

        $data['result'] = $this->report->get_purchase_bill_report_by_date($po_date1, $po_date2, $this->user_id);
        $this->session->unset_userdata('from_date');
        $this->session->unset_userdata('to_date');
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('report/export_purchase_bill_report', $data);
    }

    public function get_purchase_bill_report_by_date_xlsx() {
        $from_date = $this->session->userdata('from_date');
        $to_date = $this->session->userdata('to_date');

        $po_date1 = !empty($from_date) ? date('Y-m-d', strtotime($from_date)) : date('Y-m-d', strtotime('-30 days'));
        $po_date2 = !empty($to_date) ? date('Y-m-d', strtotime($to_date)) : date('Y-m-d');

        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
        $data['result'] = $this->report->get_purchase_bill_report_by_date($po_date1, $po_date2, $this->user_id);

        $this->session->unset_userdata('from_date');
        $this->session->unset_userdata('to_date');

        // Load the export view directly (XLSX download)
        $this->load->view('report/export_purchase_bill_report', $data);
    }

    public function export_purchase_bill_report() {
        return $this->get_purchase_bill_report_by_date_xlsx();
    }

    public function get_purchase_bill_hsn_report_by_date_xlsx() {
        $from_date = $this->session->userdata('from_date');
        $to_date = $this->session->userdata('to_date');

        $po_date1 = !empty($from_date) ? date('Y-m-d', strtotime($from_date)) : date('Y-m-d', strtotime('-30 days'));
        $po_date2 = !empty($to_date) ? date('Y-m-d', strtotime($to_date)) : date('Y-m-d');

        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
        $data['result'] = $this->report->get_purchase_bill_report_by_hsn($po_date1, $po_date2, $this->user_id);

        $this->session->unset_userdata('from_date');
        $this->session->unset_userdata('to_date');

        // Load the export view directly (XLSX download)
        $this->load->view('report/purchase_bill_hsn_report', $data);
    }

    public function export_purchase_bill_hsn_report() {
        return $this->get_purchase_bill_hsn_report_by_date_xlsx();
    }

    public function create_customer_report() {
         $from_date = $this->input->post('from_date');
        $to_date = $this->input->post('to_date');
        $company_name = $this->input->post('company_name');
        $product_name = $this->input->post('product_name');
        
        // Add null checks before strtotime
        $po_dte1 = !empty($from_date) ? strtotime($from_date) : strtotime('now');
        $po_date1 = date('Y-m-d', $po_dte1);
        $po_dte2 = !empty($to_date) ? strtotime($to_date) : strtotime('now');
        $po_date2 = date('Y-m-d', $po_dte2);
        $data['result'] = $this->report->get_customer_report_by_date($po_date1, $po_date2, $this->user_id);
        $this->session->set_userdata('from_date', $from_date);
        $this->session->set_userdata('to_date', $to_date);
        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('report/create_customer_report',$data);
    }

    public function get_customer_report_by_date_xlsx() {
        $from_date = $this->session->userdata('from_date');
        $to_date = $this->session->userdata('to_date');
        
        // Check if dates are not null before processing
        $po_date1 = !empty($from_date) ? date('Y-m-d', strtotime($from_date)) : date('Y-m-d', strtotime('-30 days'));
        $po_date2 = !empty($to_date) ? date('Y-m-d', strtotime($to_date)) : date('Y-m-d');
      
        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
        $data['result'] = $this->report->get_customer_report_by_date($po_date1, $po_date2, $this->user_id);
      
        $this->session->unset_userdata('from_date');
        $this->session->unset_userdata('to_date');
        
        // DO NOT load header - this is an Excel export
        $this->load->view('report/customer_report', $data);
    }

    public function create_supplier_report() {
       
        
        $from_date = $this->input->post('from_date');
        $to_date = $this->input->post('to_date');
        
        // Add null checks before strtotime
        $po_dte1 = !empty($from_date) ? strtotime($from_date) : strtotime('now');
        $po_date1 = date('Y-m-d', $po_dte1);
        $po_dte2 = !empty($to_date) ? strtotime($to_date) : strtotime('now');
        $po_date2 = date('Y-m-d', $po_dte2);
        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
          $this->session->set_userdata('from_date', $from_date);
        $this->session->set_userdata('to_date', $to_date);
        
        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
       // print_r($to_date);die();
      $data['result'] = $this->report->get_supplier_report_by_date($po_date1, $po_date2);
        //print_r($data['result']);die();
     
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('report/create_supplier_report',$data);
    }

    public function get_supplier_report_by_date() {
  $from_date = $this->session->userdata('from_date');
        $to_date = $this->session->userdata('to_date');


          // If not in POST, try to get from session
        if (empty($from_date1)) {
            $from_date1 = $this->session->userdata('from_date');
        }
        if (empty($to_date1)) {
            $to_date1 = $this->session->userdata('to_date');
        }
        // if (empty($company_name)) {
        //     $company_name = $this->session->userdata('company_name');
        // }
        
       // print_r($from_date);die();
        $po_dte1 = strtotime($from_date);
        $po_date1 = date('Y-m-d', $po_dte1);
        $po_dte2 = strtotime($to_date);
        $po_date2 = date('Y-m-d', $po_dte2);
        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
        $data['result'] = $this->report->get_supplier_report_by_date($po_date1, $po_date2);
        
         $this->session->unset_userdata('from_date');
        $this->session->unset_userdata('to_date');
          
         $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('report/supplier_report', $data);
    }

    public function create_itemwise_report() {
         $from_date = $this->input->post('from_date');
        $to_date = $this->input->post('to_date');
       //  $customer_id = $this->input->post('customer_id');
         $company_name = $this->input->post('company_name');
        $product_name = $this->input->post('product_name');
      // print_r($company_name);die();
//           
//        $str= explode("$", $company_name);
//        $company_name = isset($str[0]); 
//      
//       $str= explode("$", $product_name);
//        $product_name = isset($product_name);
         //print_r($product_name);die();
         
        // Add null checks before strtotime
        $po_dte1 = !empty($from_date) ? strtotime($from_date) : strtotime('now');
        $po_date1 = date('Y-m-d', $po_dte1);
        $po_dte2 = !empty($to_date) ? strtotime($to_date) : strtotime('now');
        $po_date2 = date('Y-m-d', $po_dte2);
           $this->session->set_userdata('from_date', $from_date);
        $this->session->set_userdata('to_date', $to_date);
     //  $this->session->set_userdata('customer_id', $customer_id);
        $this->session->set_userdata('company_name', $company_name);
         $this->session->set_userdata('product_name', $product_name);
        // print_r($company_name);die();
          $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
      //  $data['customer_id'] = $customer_id;
        $data['company_name_str'] = $company_name;
        $data['product_name'] = $product_name;
       
        $data['company_name'] = $this->invoice->get_company_name($this->user_id);
        $data['item_name'] = $this->invoice->get_item_name($this->user_id);
       // print_r($data['company_name']);die();
        $data['result'] = $this->report->get_itemwise_report_by_date($po_date1, $po_date2, $this->user_id, $company_name, $product_name);

        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('report/create_itemwise_report', $data);
    }

    public function get_itemwise_report_by_date() {
        
        
       $from_date = $this->session->userdata('from_date');
        $to_date = $this->session->userdata('to_date');
    // $customer_id = $this->session->userdata('customer_id');
        $company_name =  $this->session->userdata('company_name');
        $product_name =  $this->session->userdata('product_name');
        $data['company_name'] = $this->session->userdata('company_name');
              $data['product_name'] = $this->session->userdata('product_name');
           // print_r($data['product_name']);die();
        $po_dte1 = strtotime($from_date);
        $po_date1 = date('Y-m-d', $po_dte1);
        $po_dte2 = strtotime($to_date);
        $po_date2 = date('Y-m-d', $po_dte2);
        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
        $data['result'] = $this->report->get_itemwise_report_by_date($po_date1, $po_date2,$this->user_id, $company_name, $product_name);
        //print_r($data['result']);die();
         
         $this->session->unset_userdata('from_date');
        $this->session->unset_userdata('to_date');
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('report/itemwise_report', $data);
    }

    public function create_expenditure_report() {
        
        $from_date = $this->input->post('from_date');
        $to_date = $this->input->post('to_date');
      $exp_cat = trim((string) $this->input->post('expense_category'));
      $employee_name = trim((string) $this->input->post('employee_name'));
      $expense_month = trim((string) $this->input->post('expense_month'));
      $gst_class = trim((string) $this->input->post('gst_class'));
      $status = trim((string) $this->input->post('status'));
        
        // Add null checks before strtotime
        $po_date1 = !empty($from_date) ? date('Y-m-d', strtotime($from_date)) : date('Y-m-d', strtotime('-30 days'));
        $po_date2 = !empty($to_date) ? date('Y-m-d', strtotime($to_date)) : date('Y-m-d');
        
        $this->session->set_userdata('from_date', $from_date);
        $this->session->set_userdata('to_date', $to_date);
      $this->session->set_userdata('exp_rpt_cat', $exp_cat);
      $this->session->set_userdata('exp_rpt_employee', $employee_name);
      $this->session->set_userdata('exp_rpt_month', $expense_month);
      $this->session->set_userdata('exp_rpt_gst', $gst_class);
      $this->session->set_userdata('exp_rpt_status', $status);
        
        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
      $data['expense_category_str'] = $exp_cat;
      $data['employee_name_str'] = $employee_name;
      $data['expense_month_str'] = $expense_month;
      $data['gst_class_str'] = $gst_class;
      $data['status_str'] = $status;
      $data['expense_categories'] = $this->expense->get_expense_catgory($this->user_id);
      $data['gst_class_result'] = $this->inventory->get_gst_class($this->user_id);

      $filters = array(
        'employee_name' => $employee_name,
        'expense_month' => $expense_month,
        'gst_class' => $gst_class,
        'status' => $status,
      );
         
      $data['result'] = $this->report->get_expenditure_report_by_date(
        $po_date1,
        $po_date2,
        $exp_cat !== '' ? $exp_cat : NULL,
        $this->user_id,
        '',
        $filters
      );
       
        
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('report/create_expenditure_report',$data);
    }

    public function get_expenditure_report_by_date() {
        $from_date = $this->session->userdata('from_date');
        $to_date = $this->session->userdata('to_date');
      $exp_cat = trim((string) $this->session->userdata('exp_rpt_cat'));
      $employee_name = trim((string) $this->session->userdata('exp_rpt_employee'));
      $expense_month = trim((string) $this->session->userdata('exp_rpt_month'));
      $gst_class = trim((string) $this->session->userdata('exp_rpt_gst'));
      $status = trim((string) $this->session->userdata('exp_rpt_status'));
        
        // Check if dates are not null before processing
        $po_date1 = !empty($from_date) ? date('Y-m-d', strtotime($from_date)) : date('Y-m-d', strtotime('-30 days'));
        $po_date2 = !empty($to_date) ? date('Y-m-d', strtotime($to_date)) : date('Y-m-d');
        
        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
      $data['expense_category'] = $exp_cat;
      $data['employee_name'] = $employee_name;
      $data['expense_month'] = $expense_month;
      $data['gst_class'] = $gst_class;
      $data['status'] = $status;

      $filters = array(
        'employee_name' => $employee_name,
        'expense_month' => $expense_month,
        'gst_class' => $gst_class,
        'status' => $status,
      );

      $data['result'] = $this->report->get_expenditure_report_by_date(
        $po_date1,
        $po_date2,
        $exp_cat !== '' ? $exp_cat : NULL,
        $this->user_id,
        '',
        $filters
      );
         
      foreach (array('from_date', 'to_date', 'exp_rpt_cat', 'exp_rpt_employee', 'exp_rpt_month', 'exp_rpt_gst', 'exp_rpt_status') as $session_key) {
        $this->session->unset_userdata($session_key);
      }
        
        // DO NOT load header - this is an Excel export
        $this->load->view('report/expenditure_report', $data);
    }

    public function create_expenditure_item_report() {
        $expense_mode = $this->normalize_expense_mode($this->input->post('expense_mode'));
        if ($expense_mode === '') {
            $expense_mode = $this->normalize_expense_mode($this->input->get('expense_mode'));
        }

        $from_date = $this->input->post('from_date');
        $to_date = $this->input->post('to_date');
        $exp_cat = trim((string) $this->input->post('expense_category'));

        $po_date1 = !empty($from_date) ? date('Y-m-d', strtotime($from_date)) : date('Y-m-d', strtotime('-30 days'));
        $po_date2 = !empty($to_date) ? date('Y-m-d', strtotime($to_date)) : date('Y-m-d');

        $this->session->set_userdata('from_date', $from_date);
        $this->session->set_userdata('to_date', $to_date);
        $this->session->set_userdata('expense_category', $exp_cat);
        $this->session->set_userdata('expense_mode', $expense_mode);

        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
        $data['expense_category_str'] = $exp_cat;
        $data['expense_mode'] = $expense_mode;
        $data['expense_type_options'] = $this->get_report_expense_type_options();
        $all_categories = $this->expense->get_expense_catgory($this->user_id);
        $filtered_categories = $this->filter_expense_categories_by_mode($all_categories, $expense_mode);
        $data['expense_categories'] = $this->prepare_report_expense_categories($filtered_categories);

        if ($this->input->method() != 'post') {
            $data['result'] = array();
        } else {
            $data['result'] = $this->report->get_expenditure_report_by_date(
                $po_date1,
                $po_date2,
                $exp_cat !== '' ? $exp_cat : NULL,
                $this->user_id,
                $expense_mode
            );
        }

        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('report/create_expenditure_item_report', $data);
    }

    public function get_expenditure_item_report_by_date() {
        $from_date = $this->session->userdata('from_date');
        $to_date = $this->session->userdata('to_date');
        $exp_cat = trim((string) $this->session->userdata('expense_category'));
        $expense_mode = $this->normalize_expense_mode($this->session->userdata('expense_mode'));

        $po_date1 = !empty($from_date) ? date('Y-m-d', strtotime($from_date)) : date('Y-m-d', strtotime('-30 days'));
        $po_date2 = !empty($to_date) ? date('Y-m-d', strtotime($to_date)) : date('Y-m-d');

        $data['expense_category'] = $exp_cat;
        $data['expense_mode'] = $expense_mode;
        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
        $data['result'] = $this->report->get_expenditure_report_by_date(
            $po_date1,
            $po_date2,
            $exp_cat !== '' ? $exp_cat : NULL,
            $this->user_id,
            $expense_mode
        );

        $this->session->unset_userdata('from_date');
        $this->session->unset_userdata('to_date');
        $this->session->unset_userdata('expense_category');
        $this->session->unset_userdata('expense_mode');

        $this->load->view('report/expenditure_item_report', $data);
    }

    public function export_direct_expense_excel() {
        $from_date = $this->input->get('from_date');
        $to_date = $this->input->get('to_date');
        $category = trim((string) $this->input->get('category'));
        $show_all = $this->input->get('show_all');

        $po_date1 = !empty($from_date) ? date('Y-m-d', strtotime($from_date)) : date('Y-m-d', strtotime('-30 days'));
        $po_date2 = !empty($to_date) ? date('Y-m-d', strtotime($to_date)) : date('Y-m-d');

        $data['expense_category'] = $category;
        $data['expense_mode'] = 'direct';
        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
        $data['result'] = $this->report->get_expenditure_report_by_date(
            $po_date1,
            $po_date2,
            $category !== '' ? $category : NULL,
            $this->user_id,
            ''
        );

        // DO NOT load header - this is an Excel export
        $this->load->view('report/expenditure_item_report', $data);
    }

    public function export_indirect_expense_excel() {
        $from_date = $this->input->get('from_date');
        $to_date = $this->input->get('to_date');
        $category = trim((string) $this->input->get('category'));
        $employee_name = trim((string) $this->input->get('employee_name'));
        $show_all = $this->input->get('show_all');
        $is_individual_category = $this->is_indirect_individual_category($category);

        if (!$is_individual_category) {
            $employee_name = '';
        }

        $po_date1 = !empty($from_date) ? date('Y-m-d', strtotime($from_date)) : date('Y-m-d', strtotime('-30 days'));
        $po_date2 = !empty($to_date) ? date('Y-m-d', strtotime($to_date)) : date('Y-m-d');

        $data['expense_category'] = $category;
        $data['expense_mode'] = 'indirect';
        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
        $data['employee_name'] = $employee_name;
        $data['is_individual_category'] = $is_individual_category;

        if ($show_all == 1) {
            $all_expenses = $this->inventory->get_expense_data($this->user_id);
            $data['result'] = $this->filter_expense_entries_by_mode($all_expenses, 'indirect');

            if ($category !== '') {
                $data['result'] = array_filter($data['result'], function($item) use ($category) {
                    return $item->expense_category == $category;
                });
            }
        } else {
            $data['result'] = $this->inventory->get_expense_by_date_range_with_category(
                $po_date1,
                $po_date2,
                $category,
                'indirect',
                $this->user_id
            );
        }

        if ($is_individual_category && $employee_name !== '') {
            $data['result'] = $this->filter_expense_rows_by_employee($data['result'], $employee_name);
        }

        // DO NOT load header - this is an Excel export
        $this->load->view('report/expenditure_item_report', $data);
    }
     
    public function create_customer_statement_report() {
     
        $from_date1 = $this->input->post('from_date');
        $to_date1 = $this->input->post('to_date');
        $company_name = $this->input->post('company_name');



        // If not a POST request, just load the form
        if ($this->input->method() != 'post') {
            $data['company_name'] = $this->invoice->get_company_name($this->user_id);
            // ensure variables initialized for view
            $data['result'] = array();
            $data['from_date'] = '';
            $data['to_date'] = '';
            $data['customer_id'] = '';
            
            $session_data_head = $this->session->userdata('session_data_head');
            $this->load->view('admin/header_side_bar', $session_data_head);
            $this->load->view('report/create_customer_statement_report', $data);
            return;
        }

        // Check if dates are provided before processing
        if (empty($from_date1) || empty($to_date1)) {
            $this->session->set_flashdata('INFOMSG', "Please select date range!");
            redirect('ReportController/create_customer_statement_report');
            return;
        }

        $from_date = date('Y-m-d', strtotime($from_date1));
        $to_date = date('Y-m-d', strtotime($to_date1));

        $data['customer_id'] = $company_name;
        $this->session->set_userdata('from_date', $from_date1);
        $this->session->set_userdata('to_date', $to_date1);
        $this->session->set_userdata('company_name', $company_name);


        $data['from_date'] = $from_date1;
        $data['to_date'] = $to_date1;
        
        $invoice = $this->payment->get_gst_ledger($from_date, $to_date, $company_name);
        $payments = $this->payment->get_payment_ledger($from_date, $to_date, $company_name);

        $ledger_array1 = array();
        $ledger_array2 = array();

        //     Declare two dates 
        $Date1 = $from_date1;
        $Date2 = $to_date1;

// Declare an empty array 
        $array = array();

// Use strtotime function 
        $Variable1 = strtotime($Date1);
        $Variable2 = strtotime($Date2);

// Use for loop to store dates into array 
// 86400 sec = 24 hrs = 60*60*24 = 1 day 
        for ($currentDate = $Variable1; $currentDate <= $Variable2; $currentDate += (86400)) {

            $Store = date('Y-m-d', $currentDate);
            $array[] = $Store;
        }
 

        foreach ($array as $date) {

            $timestamp = strtotime($date);

// Creating new date format from that timestamp
            $current_date1 = date("d-m-Y", $timestamp);
            $i = 1;
            foreach ($invoice as $key) {

                if (date('d-m-Y', strtotime($key->invoice_date)) == $current_date1) {
                    $ledger_array1[] = array("invoice_date" => date('d-m-Y', strtotime($key->invoice_date)), "invoice_number" => $key->invoice_number, "total" => $key->total, "invocie_pay_amount" => '', "company_name" => $key->company_name, "balance" => $key->balance);
                    
                    }

                $i++;
            }
          
            foreach ($payments as $key1) {

                if ($key1->invoice_pay_date == $current_date1) {

                    $ledger_array2[] = array("invoice_date" => $key1->invoice_pay_date, "total" => $key1->total, "payment_type" => $key1->payment_type,
                        "invocie_pay_amount" => $key1->invocie_pay_amount, "invoice_number" =>  $key1->invoice_number_fk, "voucher_number" => $key1->invoice_number_fk . ' / V-' . $key1->invocie_pay_id, "company_name" => $key1->company_name,
                        "balance" => $key1->rem_balance
                        );


                }
            }
        }
        $data['result'] = array_merge( $ledger_array2);
       //print_r($data['result']);die();
        //print_r( $key1->invoice_pay_date);die();
         
        $data['company_name'] = $this->invoice->get_company_name($this->user_id);
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('report/create_customer_statement_report', $data);
    }

     public function get_customer_statement_report_xlsx() {
        $from_date1 = $this->input->post('from_date');
        $to_date1 = $this->input->post('to_date');
        $company_name = $this->input->post('company_name');

        // If not in POST, try to get from session
        if (empty($from_date1)) {
            $from_date1 = $this->session->userdata('from_date');
        }
        if (empty($to_date1)) {
            $to_date1 = $this->session->userdata('to_date');
        }
        if (empty($company_name)) {
            $company_name = $this->session->userdata('company_name');
        }

        $from_date = date('Y-m-d', strtotime($from_date1));
        $to_date   = date('Y-m-d', strtotime($to_date1));

        $data['from_date'] = $from_date1;
        $data['to_date']   = $to_date1;

        $invoices = $this->payment->get_gst_ledger($from_date, $to_date, $company_name);
        $payments = $this->payment->get_payment_ledger($from_date, $to_date, $company_name);

        // Index payments by invoice_number_fk so we can look them up quickly
        $payments_by_invoice = array();
        foreach ($payments as $pmt) {
            $inv_key = $pmt->invoice_number_fk;
            if (!isset($payments_by_invoice[$inv_key])) {
                $payments_by_invoice[$inv_key] = array();
            }
            $payments_by_invoice[$inv_key][] = $pmt;
        }

        // Build result: invoice row followed immediately by its payment rows
        $result = array();
        foreach ($invoices as $inv) {
            $inv_payments = !empty($payments_by_invoice[$inv->invoice_number])
                ? $payments_by_invoice[$inv->invoice_number]
                : array();

            // Sort payments by date ascending
            if (!empty($inv_payments)) {
                usort($inv_payments, function($a, $b) {
                    return strtotime($a->invoice_pay_date) - strtotime($b->invoice_pay_date);
                });
            }

            $has_payments = !empty($inv_payments);

            // Invoice row — show balance here only when there are no payments
            $result[] = array(
                'row_type'           => 'invoice',
                'invoice_date'       => date('d-m-Y', strtotime($inv->invoice_date)),
                'invoice_number'     => $inv->invoice_number,
                'company_name'       => $inv->company_name,
                'total'              => $inv->total,
                'invocie_pay_amount' => '',
                'balance'            => $has_payments ? '' : $inv->balance,
            );

            // Payment rows — show balance only on the LAST payment row
            if ($has_payments) {
                $last_idx = count($inv_payments) - 1;
                foreach ($inv_payments as $idx => $pmt) {
                    $result[] = array(
                        'row_type'           => 'payment',
                        'invoice_date'       => $pmt->invoice_pay_date,
                        'invoice_number'     => $pmt->invoice_number_fk,
                        'voucher_number'     => $pmt->invoice_number_fk . ' / V-' . $pmt->invocie_pay_id,
                        'company_name'       => isset($pmt->company_name) ? $pmt->company_name : '',
                        'total'              => '',
                        'invocie_pay_amount' => $pmt->invocie_pay_amount,
                        'balance'            => ($idx === $last_idx)
                            ? (isset($pmt->rem_balance) ? $pmt->rem_balance : '')
                            : '',
                    );
                }
            }
        }

        $data['result'] = $result;
        $this->load->view('report/customer_statement_report', $data);
    }
     public function create_gstr1_report() {
              $from_date = $this->input->post('from_date');
        $to_date = $this->input->post('to_date');
        $po_date1 = !empty($from_date) ? date('Y-m-d', strtotime($from_date)) : date('Y-m-d', strtotime('-30 days'));
        $po_date2 = !empty($to_date)   ? date('Y-m-d', strtotime($to_date))   : date('Y-m-d');
        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
        $data['invoices'] = $this->invoice->get_invoices($this->user_id);
        $data['company_name'] = $this->invoice->get_company_name($this->user_id);
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('report/create_gstr1_report', $data);
    }
     public function get_gstr1_report() {
         $from_date = $this->input->post('from_date');
        $to_date = $this->input->post('to_date');
        $po_date1 = !empty($from_date) ? date('Y-m-d', strtotime($from_date)) : date('Y-m-d', strtotime('-30 days'));
        $po_date2 = !empty($to_date)   ? date('Y-m-d', strtotime($to_date))   : date('Y-m-d');
        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
        $data['gstr1_report'] = $this->report->get_gstr_report($po_date1, $po_date2);
        $session_data_head = $this->session->userdata('session_data_head');
            $this->load->view('admin/header_side_bar', $session_data_head);
            $this->load->view('report/gstr1_report', $data);
        }
        
        public function create_gstr2_report() {
             $from_date = $this->input->post('from_date');
        $to_date = $this->input->post('to_date');
        $po_date1 = !empty($from_date) ? date('Y-m-d', strtotime($from_date)) : date('Y-m-d', strtotime('-30 days'));
        $po_date2 = !empty($to_date)   ? date('Y-m-d', strtotime($to_date))   : date('Y-m-d');
        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
        $data['invoices'] = $this->invoice->get_invoices($this->user_id);
        $data['company_name'] = $this->invoice->get_company_name($this->user_id);
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('report/create_gstr2_report', $data);
    }
     public function get_gstr2_report() {
          $from_date = $this->input->post('from_date');
        $to_date = $this->input->post('to_date');
        $po_date1 = !empty($from_date) ? date('Y-m-d', strtotime($from_date)) : date('Y-m-d', strtotime('-30 days'));
        $po_date2 = !empty($to_date)   ? date('Y-m-d', strtotime($to_date))   : date('Y-m-d');
        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
        $data['gstr2_report'] = $this->report->get_gstr2_report($po_date1, $po_date2);
         $session_data_head = $this->session->userdata('session_data_head');
            $this->load->view('admin/header_side_bar', $session_data_head);
            $this->load->view('report/gstr2_report', $data);
        }
        
      public function create_stock_summary(){
         $from_date = $this->input->post('from_date');
        $to_date = $this->input->post('to_date');
       
        // Add null checks before strtotime
        $po_dte1 = !empty($from_date) ? strtotime($from_date) : strtotime('now');
        $po_date1 = date('Y-m-d', $po_dte1);
        $po_dte2 = !empty($to_date) ? strtotime($to_date) : strtotime('now');
        $po_date2 = date('Y-m-d', $po_dte2);
             
         $this->session->set_userdata('from_date', $from_date);
        $this->session->set_userdata('to_date', $to_date);
        
        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
        $data['result'] = $this->report->get_inventory_report_by_date($po_date1, $po_date2, $this->user_id);

        //print_r($data['result']);die();
        
        $this->session->set_userdata('from_date', $from_date);
        $this->session->set_userdata('to_date', $to_date);
        
        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
          
         $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('report/create_stock_summary', $data);
      }
      public function get_stock_summary_report_xlsx() {
             $from_date = $this->session->userdata('from_date');
        $to_date = $this->session->userdata('to_date');
      //  print_r($to_date);die();
        $po_dte1 = strtotime($from_date);
        $po_date1 = date('Y-m-d', $po_dte1);
        $po_dte2 = strtotime($to_date);
        $po_date2 = date('Y-m-d', $po_dte2);
        
        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
        $data['result'] = $this->report->get_inventory_report_by_date($po_date1, $po_date2, $this->user_id);
    
         $this->session->unset_userdata('from_date');
        $this->session->unset_userdata('to_date');
        //  print_r($data['result']);die();
         $session_data_head = $this->session->userdata('session_data_head');
            $this->load->view('admin/header_side_bar', $session_data_head);
            $this->load->view('report/stock_summary_report', $data);
        }
    public function create_expenditure_category_report(){
        $from_date = $this->input->post('from_date');
        $to_date = $this->input->post('to_date');
        $expense_category = $this->input->post('expense_category');
       
        // Add null checks before strtotime
        $po_date1 = !empty($from_date) ? date('Y-m-d', strtotime($from_date)) : date('Y-m-d', strtotime('-30 days'));
        $po_date2 = !empty($to_date) ? date('Y-m-d', strtotime($to_date)) : date('Y-m-d');
             
        $this->session->set_userdata('from_date', $from_date);
        $this->session->set_userdata('to_date', $to_date);
        $this->session->set_userdata('expense_category', $expense_category);
        
        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
        $data['expense_category_str'] = $expense_category;
        
        // Pass the selected category to the model - NULL means all categories
        $exp_cat = ($expense_category && $expense_category != 'All') ? $expense_category : NULL;
        $data['result'] = $this->report->get_expenditure_report_by_date($po_date1, $po_date2, $exp_cat, $this->user_id);
        
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('report/create_expenditure_category_report', $data);
      }
      public function get_expenditure_category_report() {
        
        $from_date = $this->session->userdata('from_date');
        $to_date = $this->session->userdata('to_date');
        $expense_category = $this->session->userdata('expense_category');
        
        // Check if dates are not null before processing
        $po_date1 = !empty($from_date) ? date('Y-m-d', strtotime($from_date)) : date('Y-m-d', strtotime('-30 days'));
        $po_date2 = !empty($to_date) ? date('Y-m-d', strtotime($to_date)) : date('Y-m-d');
        
        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
        $data['expense_category'] = $expense_category;
        
        // Use the selected category for filtering - NULL means all categories
        $exp_cat = ($expense_category && $expense_category != 'All') ? $expense_category : NULL;
        $data['result'] = $this->report->get_expenditure_report_by_date($po_date1, $po_date2, $exp_cat, $this->user_id);
        
        $this->session->unset_userdata('from_date');
        $this->session->unset_userdata('to_date');
        $this->session->unset_userdata('expense_category');
        
        // DO NOT load header - this is an Excel export
        $this->load->view('report/expenditure_category_report', $data);
      }
         public function create_bank_statement_report(){
        $from_date = $this->input->post('from_date');
        $to_date = $this->input->post('to_date');
       
        // Add null checks before strtotime
        $po_dte1 = !empty($from_date) ? strtotime($from_date) : strtotime('now');
        $po_date1 = date('Y-m-d', $po_dte1);
        $po_dte2 = !empty($to_date) ? strtotime($to_date) : strtotime('now');
        $po_date2 = date('Y-m-d', $po_dte2);
             
         $this->session->set_userdata('from_date', $from_date);
        $this->session->set_userdata('to_date', $to_date);
        
        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
         $data['result'] = $this->bankdetail->get_banktransaction($po_date1, $po_date2, $this->user_id);
          $session_data_head = $this->session->userdata('session_data_head');
          $this->load->view('admin/header_side_bar', $session_data_head);
          $this->load->view('report/create_bank_statement_report',$data);
      }
      public function get_bank_statement_report() {
      
           $from_date = $this->session->userdata('from_date');
        $to_date = $this->session->userdata('to_date');
      
        
      //  print_r($to_date);die();
        $po_dte1 = strtotime($from_date);
        $po_date1 = date('Y-m-d', $po_dte1);
        $po_dte2 = strtotime($to_date);
        $po_date2 = date('Y-m-d', $po_dte2);
        
        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
          
        $data['result'] = $this->bankdetail->get_banktransaction($po_date1, $po_date2, $this->user_id);
        
        $this->session->unset_userdata('from_date');
        $this->session->unset_userdata('to_date');
        
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('report/bank_statement_report', $data);
    }
    
                       
        public function create_all_transaction_report(){
            
          $from_date = $this->input->post('from_date');
        $to_date = $this->input->post('to_date');
        $company_name = $this->input->post('company_name');
         $transaction_type = $this->input->post('transaction_type');
     //  print_r($transaction_type);die();
     
     // Add null checks before strtotime
     $po_dte1 = !empty($from_date) ? strtotime($from_date) : strtotime('now');
     $po_date1 = date('Y-m-d', $po_dte1);
     $po_dte2 = !empty($to_date) ? strtotime($to_date) : strtotime('now');
     $po_date2 = date('Y-m-d', $po_dte2);
             
         $this->session->set_userdata('from_date', $from_date);
        $this->session->set_userdata('to_date', $to_date);
         $this->session->set_userdata('company_name', $company_name);
          $this->session->set_userdata('transaction_type', $transaction_type);
         
       $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
         $data['company_name_str'] = $company_name;
          $data['transaction_type'] = $transaction_type;
          
     
         if ('quotation' == $transaction_type) {
            $data['result_quotation'] = $this->report->get_quotation_report_by_date($po_date1, $po_date2, $company_name = null, $this->user_id);
        } elseif ('purchase' == $transaction_type) {
            $data['result_purchase'] = $this->report->get_po_report_by_date1($po_date1, $po_date2, $company_name = null, $this->user_id);
        } elseif ('sales' == $transaction_type) {
            $data['result_sales'] = $this->report->get_report_by_date($po_date1, $po_date2, $company_name = null, $this->user_id);
        } else {
            $data['result_quotation'] = $this->report->get_quotation_report_by_date($po_date1, $po_date2, $company_name = null, $this->user_id);
            $data['result_purchase'] = $this->report->get_po_report_by_date1($po_date1, $po_date2, $company_name = null, $this->user_id);
            $data['result_sales'] = $this->report->get_report_by_date($po_date1, $po_date2, $company_name = null, $this->user_id);
        }

        // $data['company_name'] = $this->invoice->get_company_name($this->user_id);
          $session_data_head = $this->session->userdata('session_data_head');
          $this->load->view('admin/header_side_bar', $session_data_head);
          $this->load->view('report/create_all_transaction_report', $data);
          
      }
      public function get_all_transaction_report() {
           
         $from_date = $this->session->userdata('from_date');
        $to_date = $this->session->userdata('to_date');
       $transaction_type = $this->session->userdata('transaction_type');
        $data['transaction_type'] = $this->session->userdata('transaction_type');
      
     
        $po_dte1 = strtotime($from_date);
        $po_date1 = date('Y-m-d', $po_dte1);
        $po_dte2 = strtotime($to_date);
        $po_date2 = date('Y-m-d', $po_dte2);
      
        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
        $data['transaction_type'] = $transaction_type;

        
          if ('quotation' == $transaction_type) {
             // echo "djk";die();
            $data['result_quotation'] = $this->report->get_quotation_report_by_date($po_date1, $po_date2, $company_name = null, $this->user_id);
        } elseif ('purchase' == $transaction_type) {
            $data['result_purchase'] = $this->report->get_po_report_by_date1($po_date1, $po_date2, $company_name = null, $this->user_id);
        } elseif ('sales' == $transaction_type) {
            $data['result_sales'] = $this->report->get_report_by_date($po_date1, $po_date2, $company_name = null, $this->user_id);
        } else {
            $data['result_quotation'] = $this->report->get_quotation_report_by_date($po_date1, $po_date2, $company_name = null, $this->user_id);
            $data['result_purchase'] = $this->report->get_po_report_by_date1($po_date1, $po_date2, $company_name = null, $this->user_id);
            $data['result_sales'] = $this->report->get_report_by_date($po_date1, $po_date2, $company_name = null, $this->user_id);
        }

      
        //$data['result'] = $this->bankdetail->get_banktransaction($this->user_id);
        
         
        $this->session->unset_userdata('from_date');
        $this->session->unset_userdata('to_date');
        
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('report/all_transaction_report',$data);
    }
    
    public function create_customer_report_item(){
        //echo"ffffff";die();
          $from_date = $this->input->post('from_date');
          $to_date = $this->input->post('to_date');
          $item_name = $this->input->post('product_name');
        //  
     
        // Add null checks before strtotime
        $po_dte1 = !empty($from_date) ? strtotime($from_date) : strtotime('now');
        $po_date1 = date('Y-m-d', $po_dte1);
        $po_dte2 = !empty($to_date) ? strtotime($to_date) : strtotime('now');
        $po_date2 = date('Y-m-d', $po_dte2);
        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
        
          $this->session->set_userdata('from_date', $from_date);
        $this->session->set_userdata('to_date', $to_date);
      $this->session->set_userdata('product_name', $item_name);
     // print_r($company_name);die();
      
      
        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
        $data['product_name_str'] = $item_name;
      //  print($data['company_name_str']);die();
       
       $data['product_name'] = $this->invoice->get_company_name1($this->user_id);

      $data['item_report'] = $this->report->get_item_report_by_customer($po_date1, $po_date2, $item_name);
      
      
      //var_dump($data['item_report']) ;die();
          $session_data_head = $this->session->userdata('session_data_head');
          $this->load->view('admin/header_side_bar', $session_data_head);
          $this->load->view('report/create_customer_report_item', $data);
    }
    public function get_item_report_by_customer() {
       
         $from_date = $this->session->userdata('from_date');
        $to_date = $this->session->userdata('to_date');
         $item_name = $this->session->userdata('product_name');
       
         $customer_id = $this->session->userdata('customer_id');
        $data['company_name'] = $this->session->userdata('company_name');
        
        $po_dte1 = strtotime($from_date);
        $po_date1 = date('Y-m-d', $po_dte1);
        $po_dte2 = strtotime($to_date);
        $po_date2 = date('Y-m-d', $po_dte2);
        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
         $data['product_name'] = $item_name;
       
        $data['item_report'] = $this->report->get_item_report_by_customer($po_date1, $po_date2, $item_name);
       // print_r($data['item_report']);die();
          $this->session->unset_userdata('from_date');
        $this->session->unset_userdata('to_date');
        // var_dump($data);die();
         $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
       
        $this->load->view('report/customer_report_by_item', $data);
    }
    public function create_sale_by_customer() {
         $from_date = $this->input->post('from_date');
        $to_date = $this->input->post('to_date');
        
        // Add null checks before strtotime
        $po_dte1 = !empty($from_date) ? strtotime($from_date) : strtotime('now');
        $po_date1 = date('Y-m-d', $po_dte1);
     
        $po_dte2 = !empty($to_date) ? strtotime($to_date) : strtotime('now');
        $po_date2 = date('Y-m-d', $po_dte2);
           $this->session->set_userdata('from_date', $from_date);
        $this->session->set_userdata('to_date', $to_date);
      
        
          $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
        
          $data['result'] = $this->report->get_sale_report_by_customer ($po_date1, $po_date2, $this->user_id);
       // $data['company_name'] = $this->invoice->get_company_name($this->user_id);
       
          $session_data_head = $this->session->userdata('session_data_head');
          $this->load->view('admin/header_side_bar', $session_data_head);
          $this->load->view('report/create_sale_by_customer', $data);
    }
    public function get_sale_report_by_customer() {
          
       $from_date = $this->session->userdata('from_date');
        $to_date = $this->session->userdata('to_date');
     
        $po_dte1 = strtotime($from_date);
       
        $po_date1 = date('Y-m-d', $po_dte1);
      
        $po_dte2 = strtotime($to_date);
        $po_date2 = date('Y-m-d', $po_dte2);
        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
        
     //   $data['sale_report'] = $this->report->get_sale_report_by_customer($po_date1, $po_date2);
                 $data['result'] = $this->report->get_sale_report_by_customer ($po_date1, $po_date2, $this->user_id);
           //  print_r($data['result']);die();   
         $this->session->unset_userdata('from_date');
        $this->session->unset_userdata('to_date');
        //print_r($data['sale_report']);die();
        $this->load->view('report/sale_report_by_customer', $data);
    }
    public function create_purchase_report_by_supplier() {
        // Show empty form with supplier list
        $data['supplier_list'] = $this->supplier->get_supplier($this->user_id);
        $data['result']       = array();
        $data['from_date']    = '';
        $data['to_date']      = '';
        $data['supplier_id']  = '';
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('report/create_purchase_by_customer', $data);
    }

    public function create_supplier_statement_report() {
        $from_date1  = $this->input->post('from_date');
        $to_date1    = $this->input->post('to_date');
        $supplier_id = $this->input->post('supplier_id');

        if ($this->input->method() != 'post') {
            redirect('ReportController/create_purchase_report_by_supplier');
            return;
        }

        if (empty($from_date1) || empty($to_date1)) {
            $this->session->set_flashdata('INFOMSG', "Please select date range!");
            redirect('ReportController/create_purchase_report_by_supplier');
            return;
        }

        $from_date = date('Y-m-d', strtotime($from_date1));
        $to_date   = date('Y-m-d', strtotime($to_date1));

        $this->session->set_userdata('from_date',   $from_date1);
        $this->session->set_userdata('to_date',     $to_date1);
        $this->session->set_userdata('supplier_id', $supplier_id);

        $bills    = $this->payment->get_purchse_bill_ledger($from_date, $to_date, $supplier_id);
        $payments = $this->payment->get_purchse_bill_payment_history($from_date, $to_date, $supplier_id);

        // Index payments by purchase_number_fk
        $payments_by_bill = array();
        foreach ($payments as $pmt) {
            $key = $pmt->purchase_number_fk;
            if (!isset($payments_by_bill[$key])) {
                $payments_by_bill[$key] = array();
            }
            $payments_by_bill[$key][] = $pmt;
        }

        $result = array();
        foreach ($bills as $bill) {
            $bill_payments = !empty($payments_by_bill[$bill->number]) ? $payments_by_bill[$bill->number] : array();

            if (!empty($bill_payments)) {
                usort($bill_payments, function($a, $b) {
                    return strtotime($a->purchase_pay_date) - strtotime($b->purchase_pay_date);
                });
            }

            $has_payments = !empty($bill_payments);

            $result[] = array(
                'row_type'     => 'bill',
                'bill_date'    => date('d-m-Y', strtotime($bill->date)),
                'bill_number'  => $bill->number,
                'voucher_number'=> '',
                'company_name' => isset($bill->company_name) ? $bill->company_name : '',
                'total'        => $bill->total,
                'pay_amount'   => '',
                'balance'      => $has_payments ? '' : $bill->balance,
            );

            if ($has_payments) {
                $last_idx = count($bill_payments) - 1;
                foreach ($bill_payments as $idx => $pmt) {
                    $result[] = array(
                        'row_type'     => 'payment',
                        'bill_date'    => $pmt->purchase_pay_date,
                        'bill_number'  => $pmt->purchase_number_fk,
                        'voucher_number'=> 'V-' . $pmt->purchase_pay_id,
                        'company_name' => '',
                        'total'        => '',
                        'pay_amount'   => $pmt->purchase_pay_amount,
                        'balance'      => ($idx === $last_idx) ? $bill->balance : '',
                    );
                }
            }
        }

        $data['result']       = $result;
        $data['from_date']    = $from_date1;
        $data['to_date']      = $to_date1;
        $data['supplier_id']  = $supplier_id;
        $data['supplier_list']= $this->supplier->get_supplier($this->user_id);
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('report/create_purchase_by_customer', $data);
    }

    public function get_supplier_statement_report_xlsx() {
        $from_date1  = $this->input->post('from_date');
        $to_date1    = $this->input->post('to_date');
        $supplier_id = $this->input->post('supplier_id');

        if (empty($from_date1))  { $from_date1  = $this->session->userdata('from_date'); }
        if (empty($to_date1))    { $to_date1    = $this->session->userdata('to_date'); }
        if (empty($supplier_id)) { $supplier_id = $this->session->userdata('supplier_id'); }

        $from_date = date('Y-m-d', strtotime($from_date1));
        $to_date   = date('Y-m-d', strtotime($to_date1));

        $data['from_date'] = $from_date1;
        $data['to_date']   = $to_date1;

        $bills    = $this->payment->get_purchse_bill_ledger($from_date, $to_date, $supplier_id);
        $payments = $this->payment->get_purchse_bill_payment_history($from_date, $to_date, $supplier_id);

        $payments_by_bill = array();
        foreach ($payments as $pmt) {
            $key = $pmt->purchase_number_fk;
            if (!isset($payments_by_bill[$key])) {
                $payments_by_bill[$key] = array();
            }
            $payments_by_bill[$key][] = $pmt;
        }

        $result = array();
        foreach ($bills as $bill) {
            $bill_payments = !empty($payments_by_bill[$bill->number]) ? $payments_by_bill[$bill->number] : array();

            if (!empty($bill_payments)) {
                usort($bill_payments, function($a, $b) {
                    return strtotime($a->purchase_pay_date) - strtotime($b->purchase_pay_date);
                });
            }

            $has_payments = !empty($bill_payments);

            $result[] = array(
                'row_type'     => 'bill',
                'bill_date'    => date('d-m-Y', strtotime($bill->date)),
                'bill_number'  => $bill->number,
                'voucher_number'=> '',
                'company_name' => isset($bill->company_name) ? $bill->company_name : '',
                'total'        => $bill->total,
                'pay_amount'   => '',
                'balance'      => $has_payments ? '' : $bill->balance,
            );

            if ($has_payments) {
                $last_idx = count($bill_payments) - 1;
                foreach ($bill_payments as $idx => $pmt) {
                    $result[] = array(
                        'row_type'     => 'payment',
                        'bill_date'    => $pmt->purchase_pay_date,
                        'bill_number'  => $pmt->purchase_number_fk,
                        'voucher_number'=> 'V-' . $pmt->purchase_pay_id,
                        'company_name' => '',
                        'total'        => '',
                        'pay_amount'   => $pmt->purchase_pay_amount,
                        'balance'      => ($idx === $last_idx) ? $bill->balance : '',
                    );
                }
            }
        }

        $data['result'] = $result;
        $this->load->view('report/supplier_statement_report', $data);
    }

    public function get_purchase_report_by_supplier() {
        $from_date = $this->session->userdata('from_date');
        $to_date   = $this->session->userdata('to_date');
        $po_date1  = !empty($from_date) ? date('Y-m-d', strtotime($from_date)) : date('Y-m-d', strtotime('-30 days'));
        $po_date2  = !empty($to_date)   ? date('Y-m-d', strtotime($to_date))   : date('Y-m-d');
        $data['from_date']      = $from_date;
        $data['to_date']        = $to_date;
        $data['purchase_report'] = $this->report->get_purchase_report_by_supplier($po_date1, $po_date2);
        $this->session->unset_userdata('from_date');
        $this->session->unset_userdata('to_date');
        $this->load->view('report/purchase_report_by_customer', $data);
    }
     public function create_discount_report() {
         
          $from_date = $this->input->post('from_date');
        $to_date = $this->input->post('to_date');
        
        // Add null checks before strtotime
        $po_dte1 = !empty($from_date) ? strtotime($from_date) : strtotime('now');
        $po_date1 = date('Y-m-d', $po_dte1);
        $po_dte2 = !empty($to_date) ? strtotime($to_date) : strtotime('now');
        $po_date2 = date('Y-m-d', $po_dte2);
        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
          $this->session->set_userdata('from_date', $from_date);
        $this->session->set_userdata('to_date', $to_date);
        
        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
        
          $data['discount_report'] = $this->report->get_discount_report($po_date1,$po_date2);
         
       // $data['company_name'] = $this->invoice->get_company_name($this->user_id);
          $session_data_head = $this->session->userdata('session_data_head');
          $this->load->view('admin/header_side_bar', $session_data_head);
          $this->load->view('report/create_discount_report',$data);
    }
    public function get_discount_report() {

           
       $from_date = $this->session->userdata('from_date');
        $to_date = $this->session->userdata('to_date');
        
       // print_r($from_date);die();
        $po_dte1 = strtotime($from_date);
        $po_date1 = date('Y-m-d', $po_dte1);
        $po_dte2 = strtotime($to_date);
        $po_date2 = date('Y-m-d', $po_dte2);
        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
        
        $data['discount_report'] = $this->report->get_discount_report($po_date1,$po_date2);
        
          $this->session->unset_userdata('from_date');
        $this->session->unset_userdata('to_date');
        
        //print_r($data['discount_report']);die();
        $this->load->view('report/discount_report', $data);
    }
   public function create_sale_tax_report(){
       
       
          $from_date = $this->input->post('from_date');
        $to_date = $this->input->post('to_date');
        
        // Add null checks before strtotime
        $po_dte1 = !empty($from_date) ? strtotime($from_date) : strtotime('now');
        $po_date1 = date('Y-m-d', $po_dte1);
        $po_dte2 = !empty($to_date) ? strtotime($to_date) : strtotime('now');
        $po_date2 = date('Y-m-d', $po_dte2);
        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
          $this->session->set_userdata('from_date', $from_date);
        $this->session->set_userdata('to_date', $to_date);
        
        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
        $data['sale_tax_report'] = $this->report->get_sale_tax_report($po_date1, $po_date2);
       
        $session_data_head = $this->session->userdata('session_data_head');
          $this->load->view('admin/header_side_bar', $session_data_head);
          $this->load->view('report/create_sale_tax_report',$data);
    }
    public function get_sale_tax_report() {
       $from_date = $this->session->userdata('from_date');
        $to_date = $this->session->userdata('to_date');
       $po_dte1 = strtotime($from_date);
        $po_date1 = date('Y-m-d', $po_dte1);
        $po_dte2 = strtotime($to_date);
        $po_date2 = date('Y-m-d', $po_dte2);
        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
        
        $data['sale_tax_report'] = $this->report->get_sale_tax_report($po_date1, $po_date2);
        
         $this->session->unset_userdata('from_date');
        $this->session->unset_userdata('to_date');
        //print_r($data['discount_report']);die();
        $this->load->view('report/sale_tax_report', $data);
    }
    public function create_purchase_tax_report(){
        
        
          $from_date = $this->input->post('from_date');
        $to_date = $this->input->post('to_date');
        
        // Add null checks before strtotime
        $po_dte1 = !empty($from_date) ? strtotime($from_date) : strtotime('now');
        $po_date1 = date('Y-m-d', $po_dte1);
        $po_dte2 = !empty($to_date) ? strtotime($to_date) : strtotime('now');
        $po_date2 = date('Y-m-d', $po_dte2);
        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
          $this->session->set_userdata('from_date', $from_date);
        $this->session->set_userdata('to_date', $to_date);
        
        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
          $data['purchase_tax_report'] = $this->report->get_purchase_tax_report($po_date1, $po_date2);
          
        $session_data_head = $this->session->userdata('session_data_head');
          $this->load->view('admin/header_side_bar', $session_data_head);
          $this->load->view('report/create_purchase_tax_report',$data);
    }
    public function get_purchase_tax_report() {
        $from_date = $this->session->userdata('from_date');
        $to_date = $this->session->userdata('to_date');
       $po_dte1 = strtotime($from_date);
        $po_date1 = date('Y-m-d', $po_dte1);
        $po_dte2 = strtotime($to_date);
        $po_date2 = date('Y-m-d', $po_dte2);
        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
        $data['purchase_tax_report'] = $this->report->get_purchase_tax_report($po_date1, $po_date2);
         $this->session->unset_userdata('from_date');
        $this->session->unset_userdata('to_date');
        //print_r($data['discount_report']);die();
        $this->load->view('report/purchase_tax_report', $data);
    }
    
    public function create_loan_statement(){
        
         $from_date = $this->input->post('from_date');
        $to_date = $this->input->post('to_date');
      
        $po_date1 = !empty($from_date) ? date('Y-m-d', strtotime($from_date)) : date('Y-m-d', strtotime('-30 days'));
        $po_date2 = !empty($to_date) ? date('Y-m-d', strtotime($to_date)) : date('Y-m-d');
      
        $this->session->set_userdata('from_date', $from_date);
        $this->session->set_userdata('to_date', $to_date);
        
        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
      
      
        $data['loan_report'] = $this->report->get_loan_statement_report($po_date1, $po_date2);
        $session_data_head = $this->session->userdata('session_data_head');
          $this->load->view('admin/header_side_bar', $session_data_head);
          $this->load->view('report/create_loan_statement',$data);
    }
    public function get_loan_statement_report() {
           $from_date = $this->session->userdata('from_date');
        $to_date = $this->session->userdata('to_date');
       $po_dte1 = strtotime($from_date);
        $po_date1 = date('Y-m-d', $po_dte1);
        // print_r($po_date1);die();
        $po_dte2 = strtotime($to_date);
        $po_date2 = date('Y-m-d', $po_dte2);
        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
        $data['loan_report'] = $this->report->get_loan_statement_report($po_date1, $po_date2);
       
         $this->session->unset_userdata('from_date');
        $this->session->unset_userdata('to_date');
        //print_r($data['discount_report']);die();
        $this->load->view('report/loan_statement', $data);
    }
    public function create_sale_order_report(){
       // echo "kkkkkkkkk";die();
         $from_date = $this->input->post('from_date');
        $to_date = $this->input->post('to_date');
        $fullname = $this->input->post('fullname');
       // print_r($fullname1);die();
       //  $customer_id = $this->input->post('customer_id');
         
//        $str= explode("$", $fullname1);
//        $customer_id = $str[0]; 
//        $fullname= $str[1];
//      
//       
      // print_r($fullname);die();
      
        // Add null checks before strtotime
        $po_dte1 = !empty($from_date) ? strtotime($from_date) : strtotime('now');
        $po_date1 = date('Y-m-d', $po_dte1);
        $po_dte2 = !empty($to_date) ? strtotime($to_date) : strtotime('now');
        $po_date2 = date('Y-m-d', $po_dte2);
        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
          $this->session->set_userdata('from_date', $from_date);
        $this->session->set_userdata('to_date', $to_date);
         $this->session->set_userdata('fullname', $fullname);
        
        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
        $data['fullname_str'] = $fullname;
       // print_r($fullname);die();
         $data['sale_order_report'] = $this->report->get_sale_order_report($po_date1, $po_date2, $fullname);
        
        $data['fullname'] = $this->salesorder->get_company_name($this->user_id);
       $session_data_head = $this->session->userdata('session_data_head');
          $this->load->view('admin/header_side_bar', $session_data_head);
          $this->load->view('report/create_sale_order_report' , $data); 
    }
    public function get_sale_order_report() {
        
       $from_date = $this->session->userdata('from_date');
        $to_date = $this->session->userdata('to_date');
        
        $fullname =  $this->session->userdata('fullname');
        $data['fullname'] = $this->session->userdata('fullname');
      //  print_r($data['fullname']);die();
        $po_dte1 = strtotime($from_date);
        $po_date1 = date('Y-m-d', $po_dte1);
        $po_dte2 = strtotime($to_date);
        $po_date2 = date('Y-m-d', $po_dte2);
        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
        
        $data['sale_order_report'] = $this->report->get_sale_order_report($po_date1, $po_date2, $fullname);
        
        
         $this->session->unset_userdata('from_date');
        $this->session->unset_userdata('to_date');
       // print_r($data['sale_order_report']);die();
        $this->load->view('report/sale_order_report', $data);
    }
    public function create_purchase_order_report(){
       $from_date = $this->input->post('from_date');
        $to_date = $this->input->post('to_date');
        $supplier_name = $this->input->post('fullname');
        
//         
//       $str= explode("$", $supplier_name);
//        $supplier_name = $str[0]; 
//        $supplier_name= $str[1];
        
        
      // print_r($supplier_name);die();
      
        // Add null checks before strtotime
        $po_dte1 = !empty($from_date) ? strtotime($from_date) : strtotime('now');
        $po_date1 = date('Y-m-d', $po_dte1);
        $po_dte2 = !empty($to_date) ? strtotime($to_date) : strtotime('now');
        $po_date2 = date('Y-m-d', $po_dte2);
        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
        
          $this->session->set_userdata('from_date', $from_date);
        $this->session->set_userdata('to_date', $to_date);
         $this->session->set_userdata('fullname', $supplier_name);
        
        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
        $data['fullname_str'] = $supplier_name;
       
        
      $data['purchase_order_report'] = $this->report->get_purchase_order_report($po_date1, $po_date2,$supplier_name);
        $data['fullname'] = $this->supplier->get_supplier($this->user_id);

        //print_r($data['fullname']);die();
        $session_data_head = $this->session->userdata('session_data_head');
          $this->load->view('admin/header_side_bar', $session_data_head);
          $this->load->view('report/create_purchase_order_report' , $data); 
    }
    public function get_purchase_order_report() {
        
        
         $from_date = $this->session->userdata('from_date');
        $to_date = $this->session->userdata('to_date');
         $fullname = $this->session->userdata('fullname');
         //print_r($fullname);die();
       // $data['company_name'] = $this->session->userdata('company_name');
        $po_dte1 = strtotime($from_date);
        $po_date1 = date('Y-m-d', $po_dte1);
        $po_dte2 = strtotime($to_date);
        $po_date2 = date('Y-m-d', $po_dte2);
        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
       $data['fullname'] = $fullname;
       
        $data['purchase_order_report'] = $this->report->get_purchase_order_report($po_date1, $po_date2, $fullname);
        $this->session->unset_userdata('from_date');
        $this->session->unset_userdata('to_date');       
//print_r($data['purchase_order_report']);die();
        $this->load->view('report/purchases_order_report', $data);
    }
    public function create_sale_order_item_report(){
        
         $from_date = $this->input->post('from_date');
        $to_date = $this->input->post('to_date');
      
      
        $product_name = $this->input->post('product_name');
    
     //    $customer_id = $this->input->post('customer_id');
       
//        $str= explode("$", $product_name);
//       
//        $customer_id = $str[0]; 
//        $fullname = isset($str[1]);
           $fullname  = $product_name;
       //print_r($fullname);die();
       
        // Add null checks before strtotime
        $po_dte1 = !empty($from_date) ? strtotime($from_date) : strtotime('now');
        $po_date1 = date('Y-m-d', $po_dte1);
        $po_dte2 = !empty($to_date) ? strtotime($to_date) : strtotime('now');
        $po_date2 = date('Y-m-d', $po_dte2);
        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
        
          $this->session->set_userdata('from_date', $from_date);
        $this->session->set_userdata('to_date', $to_date);
         $this->session->set_userdata('fullname', $fullname);
         
       //  print_r($fullname);die();
        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
        $data['fullname_str'] = $fullname;
        
       // print_r($data['fullname']);die();
         $data['sale_order_item_report'] = $this->report->get_sale_order_item_report($po_date1, $po_date2, $fullname, $this->user_id);
        $data['product_name'] = $this->salesorder->get_company_name1($po_date1, $po_date2, $fullname, $this->user_id);
        $data['item_name'] = $this->salesorder->get_item_name1($po_date1, $po_date2, $fullname, $this->user_id);
     //    print_r($data['product_name']); die();
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('report/create_sale_order_item_report' , $data); 
    }
    public function get_sale_order_item_report() {
       // echo "jjjjjjjjj";die();
       $from_date = $this->session->userdata('from_date');
        $to_date = $this->session->userdata('to_date');
        $fullname =  $this->session->userdata('fullname');
        $data['fullname'] = $this->session->userdata('fullname');
       // print_r($data['fullname']);die();
        $po_dte1 = strtotime($from_date);
        $po_date1 = date('Y-m-d', $po_dte1);
        $po_dte2 = strtotime($to_date);
        $po_date2 = date('Y-m-d', $po_dte2);
        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
        
        $data['sale_order_item_report'] = $this->report->get_sale_order_item_report($po_date1, $po_date2, $fullname, $this->user_id);
      
         $this->session->unset_userdata('from_date');
        $this->session->unset_userdata('to_date');
        
        //print_r($data['sale_order_item_report']);die();
        $this->load->view('report/sale_order_item_report', $data);
    }
    public function create_purchase_order_item_report(){
        
        
         $from_date = $this->input->post('from_date');
        $to_date = $this->input->post('to_date');
        $fullname = $this->input->post('fullname');
      //  print_r($fullname);die();
 
        
               
        // Add null checks before strtotime
        $po_dte1 = !empty($from_date) ? strtotime($from_date) : strtotime('now');
        $po_date1 = date('Y-m-d', $po_dte1);
        $po_dte2 = !empty($to_date) ? strtotime($to_date) : strtotime('now');
        $po_date2 = date('Y-m-d', $po_dte2);
        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
        
          $this->session->set_userdata('from_date', $from_date);
        $this->session->set_userdata('to_date', $to_date);
         $this->session->set_userdata('fullname', $fullname);
         
       //  print_r($fullname);die();
        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
        $data['fullname_str'] = $fullname;
      
       $data['purchase_order_item_report'] = $this->report->get_purchase_order_item_report($po_date1, $po_date2, $fullname);
  $data['purchase_order_item_report1'] = $this->report->purchase_order_item_report($po_date1, $po_date2, $this->user_id);
  //print_r($data['purchase_order_item_report123']);die();     
  $data['fullname'] = $this->supplier->get_supplier($this->user_id);
          $session_data_head = $this->session->userdata('session_data_head');
          $this->load->view('admin/header_side_bar', $session_data_head);
          $this->load->view('report/create_purchase_order_item_report' , $data); 
    }
    public function get_purchase_order_item_report() {
          $from_date = $this->session->userdata('from_date');
        $to_date = $this->session->userdata('to_date');
        $fullname =  $this->session->userdata('fullname');
        $data['fullname'] = $this->session->userdata('fullname');
       // print_r($data['fullname']);die();
        $po_dte1 = strtotime($from_date);
        $po_date1 = date('Y-m-d', $po_dte1);
        $po_dte2 = strtotime($to_date);
        $po_date2 = date('Y-m-d', $po_dte2);
        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
        
        $data['purchase_order_item_report'] = $this->report->get_purchase_order_item_report($po_date1, $po_date2, $fullname);
         $data['purchase_order_item_report1'] = $this->report->purchase_order_item_report($po_date1, $po_date2, $this->user_id);
         //print_r($data['purchase_order_item_report1']); die();
         $this->session->unset_userdata('from_date');
        $this->session->unset_userdata('to_date');

//print_r($data['purchase_order_report']);die();
        $this->load->view('report/purchases_order_item_report', $data);
    }
    
    public function create_profit_loss_report(){
        
         $from_date = $this->input->post('from_date');
        $to_date = $this->input->post('to_date');
        
        // Add null checks before strtotime
        $po_dte1 = !empty($from_date) ? strtotime($from_date) : strtotime('now');
        $po_date1 = date('Y-m-d', $po_dte1);
        $po_dte2 = !empty($to_date) ? strtotime($to_date) : strtotime('now');
        $po_date2 = date('Y-m-d', $po_dte2);
        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
        
        $data['stock_in_hand'] = $this->input->post('stock_in_hand');
        $data['scrap'] = $this->input->post('scrap');
//        print_r($data['scrap']); 
//        print_r($data['stock_in_hand']);
//        die();
        $data['purchase_report'] = $this->report->get_profit_loss_report($po_date1, $po_date2);
       // print_r($data['purchase_report']);die();
          $session_data_head = $this->session->userdata('session_data_head');
          $this->load->view('admin/header_side_bar', $session_data_head);
          $this->load->view('report/create_profit_loss_report'); 
    }
    public function get_profit_loss_report() {
        $from_date = $this->input->post('from_date');
        $to_date = $this->input->post('to_date');
        $po_dte1 = strtotime($from_date);
        $po_date1 = date('Y-m-d', $po_dte1);
        $po_dte2 = strtotime($to_date);
        $po_date2 = date('Y-m-d', $po_dte2);
        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
        
        $data['stock_in_hand'] = $this->input->post('stock_in_hand');
        $data['scrap'] = $this->input->post('scrap');
        $data['purchase_report'] = $this->report->get_profit_loss_report($po_date1, $po_date2);
        
           $this->session->unset_userdata('from_date');
        $this->session->unset_userdata('to_date'); 
        
        $data['settings'] = $this->login->get_settings($this->user_id);
        $this->load->view('report/profit_loss_report', $data);
    }
    
    public function create_balance_sheet_report(){
        
        
         $from_date = $this->input->post('from_date');
         $to_date = $this->input->post('to_date');
         $Liabilities = $this->input->post('Liabilities_id');
         $subLiabilities = $this->input->post('Liabilities_sub_category');
        // print_r($subLiabilities); die();
        
        
        
        // Add null checks before strtotime
        $po_dte1 = !empty($from_date) ? strtotime($from_date) : strtotime('now');
        $po_date1 = date('d-m-Y', $po_dte1);
        //  print_r($po_date1);die();
        $po_dte2 = !empty($to_date) ? strtotime($to_date) : strtotime('now');
        $po_date2 = date('d-m-Y', $po_dte2);
      
        $this->session->set_userdata('from_date', $from_date);
        $this->session->set_userdata('to_date', $to_date);
        $this->session->set_userdata('Liabilities_id', $Liabilities);
        $this->session->set_userdata('Liabilities_sub_category', $subLiabilities);
   
       //  print_r($fullname);die();
        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
        $data['Liabilities_id'] = $Liabilities;
        $data['Liabilities_sub_category'] = $subLiabilities;
        
       $data['asset_name'] = $this->assetbalancesheet->get_asset_name($this->user_id);
       $data['liabilities_name'] = $this->Liabilities->get_Liabilities_name($this->user_id);
       $data['subasset_name'] = $this->assetbalancesheet->get_subasset_name($this->user_id);
        
        
       $data['subliabilities_name_excel'] = $this->Liabilities->get_subliabilities($this->user_id);
      // print_r($data['subliabilities_name_excel']); die();
        $data['balance_report'] = $this->report->get_balance_sheet_report($po_date1, $po_date2);
   //   print_r($data['balance_report']);die();
       // $data['asset_sub_category'] = $this->asset->get_asset_sub_category($this->user_id);
       // print_r($data); die();
          $session_data_head = $this->session->userdata('session_data_head');
          $this->load->view('admin/header_side_bar', $session_data_head);
          $this->load->view('report/create_balance_sheet_report', $data); 
    }
    public function get_balance_sheet_report() {
         $from_date = $this->session->userdata('from_date');
        $to_date = $this->session->userdata('to_date');
        
          $Liabilities = $this->session->userdata('Liabilities_id');
          
          
          $subLiabilities = $this->session->userdata('Liabilities_sub_category');
        
      
      $po_dte1 = !empty($from_date) ? strtotime($from_date) : strtotime('now');
        $po_date1 = date('Y-m-d', $po_dte1);
        $po_dte2 = !empty($to_date) ? strtotime($to_date) : strtotime('now');
        $po_date2 = date('Y-m-d', $po_dte2);
        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
       
          $data['balance_report'] = $this->report->get_balance_sheet_report($po_date1, $po_date2);
          print_r( $data['balance_report']); die();
        
        
          $data['subliabilities_name_excel'] = $this->Liabilities->get_subliabilities1( $this->user_id);
           // $data['purchase'] = $this->Liabilities->purchase_total( $this->user_id);
          
        //  print_r($data['subliabilities_name_excel']);die();
                                                   
       
         
            $this->session->unset_userdata('from_date');
        $this->session->unset_userdata('to_date');

         
        $data['settings'] = $this->login->get_settings($this->user_id);
        $this->load->view('report/balance_sheet_report', $data);
    }


    public function create_sales_purchase_report() {
        
      $from_date = $this->input->post('from_date');
      $to_date = $this->input->post('to_date');
     
      // Add null checks before strtotime
      $po_dte1 = !empty($from_date) ? strtotime($from_date) : strtotime('now');
      $po_date1 = date('Y-m-d', $po_dte1);
      $po_dte2 = !empty($to_date) ? strtotime($to_date) : strtotime('now');
      $po_date2 = date('Y-m-d', $po_dte2);

      


      $data['result_sales'] = $this->report->get_report_by_date($po_date1, $po_date2, $this->user_id);
      $data['result_purchase_bill'] = $this->report->get_pb_report_by_date1($po_date1, $po_date2, $this->user_id);


      //print_r($data['result']);die();create_sales_report
      
      $this->session->set_userdata('from_date', $from_date);
      $this->session->set_userdata('to_date', $to_date);
      
      $data['from_date'] = $from_date;
      $data['to_date'] = $to_date;
      
      $session_data_head = $this->session->userdata('session_data_head');
      $this->load->view('admin/header_side_bar', $session_data_head);
      $this->load->view('report/create_sales_purchase_report', $data);
  }
public function create_expenditure_item_report_direct()
{
    $data['from_date'] = '';
    $data['to_date'] = '';
    $data['expense_mode'] = 'direct';
    
    // Get expense categories to populate the dropdown on initial load
    $all_categories = $this->expense->get_expense_catgory($this->user_id);
    $data['expense_catgory'] = $this->filter_expense_categories_by_mode($all_categories, 'direct');
    
    $session_data_head = $this->session->userdata('session_data_head');
    $this->load->view('admin/header_side_bar', $session_data_head);
    $this->load->view('expense/create_expenditure_item_report_direct', $data);
}

public function create_expenditure_item_report_indirect()
{
    $data['from_date'] = '';
    $data['to_date'] = '';
    $data['expense_mode'] = 'indirect';
    $data['selected_employee'] = '';
    $data['is_individual_category'] = false;
    
    // Get expense categories to populate the dropdown on initial load
    $all_categories = $this->expense->get_expense_catgory($this->user_id);
    $data['expense_catgory'] = $this->filter_expense_categories_by_mode($all_categories, 'indirect');
    $data['individuals'] = $this->expense->get_indirect_individuals($this->user_id);
    
    $session_data_head = $this->session->userdata('session_data_head');
    $this->load->view('admin/header_side_bar', $session_data_head);
    $this->load->view('expense/create_expenditure_item_report_indirect', $data);
}

public function get_direct_expense_report()
{
    $from_date = $this->input->post('from_date');
    $to_date = $this->input->post('to_date');
    $expense_category = $this->input->post('expense_category');
    $show_all = $this->input->post('show_all');
    
    $data['from_date'] = $from_date;
    $data['to_date'] = $to_date;
    $data['selected_category'] = $expense_category;
    $data['expense_mode'] = 'direct';
    
    // Get expense categories for filter dropdown
    $all_categories = $this->expense->get_expense_catgory($this->user_id);
    $data['expense_catgory'] = $this->filter_expense_categories_by_mode($all_categories, 'direct');
    
    // Get report data
    if ($show_all == 1) {
        // Show all records without date filter
        $all_expenses = $this->inventory->get_expense_data($this->user_id);
        $data['result'] = $this->filter_expense_entries_by_mode($all_expenses, 'direct');
        
        // Apply category filter if selected
        if (!empty($expense_category)) {
            $data['result'] = array_filter($data['result'], function($item) use ($expense_category) {
                return $item->expense_category == $expense_category;
            });
        }
    } else {
        // Apply date filter
        $from = !empty($from_date) ? date('Y-m-d', strtotime($from_date)) : '';
        $to = !empty($to_date) ? date('Y-m-d', strtotime($to_date)) : '';
        
        $data['result'] = $this->inventory->get_expense_by_date_range_with_category(
            $from, 
            $to, 
            $expense_category, 
            'direct', 
            $this->user_id
        );
    }
    
    $session_data_head = $this->session->userdata('session_data_head');
    $this->load->view('admin/header_side_bar', $session_data_head);
    $this->load->view('expense/expenditure_report_direct', $data);
}

    public function get_indirect_expense_report()
{
    $from_date = $this->input->post('from_date');
    $to_date = $this->input->post('to_date');
    $expense_category = trim((string) $this->input->post('expense_category'));
    $show_all = $this->input->post('show_all');
    $employee_name = trim((string) $this->input->post('employee_name'));

    // Decide employee filter only for Individual indirect categories.
    $is_individual_category = $this->is_indirect_individual_category($expense_category);
    if (!$is_individual_category) {
        $employee_name = '';
    }

    $data['from_date'] = $from_date;
    $data['to_date'] = $to_date;
    $data['selected_category'] = $expense_category;
    $data['expense_mode'] = 'indirect';
    $data['selected_employee'] = $employee_name;
    $data['is_individual_category'] = $is_individual_category;

    // Get expense categories for filter dropdown
    $all_categories = $this->expense->get_expense_catgory($this->user_id);
    $data['expense_catgory'] = $this->filter_expense_categories_by_mode($all_categories, 'indirect');
    $data['individuals'] = $this->expense->get_indirect_individuals($this->user_id);

    // Get report data
    if ($show_all == 1) {
        // Show all records without date filter
        $all_expenses = $this->inventory->get_expense_data($this->user_id);
        $data['result'] = $this->filter_expense_entries_by_mode($all_expenses, 'indirect');

        // Apply category filter if selected
        if (!empty($expense_category)) {
            $data['result'] = array_filter($data['result'], function($item) use ($expense_category) {
                return isset($item->expense_category) && $item->expense_category == $expense_category;
            });
        }
    } else {
        // Apply date filter
        $from = !empty($from_date) ? date('Y-m-d', strtotime($from_date)) : '';
        $to = !empty($to_date) ? date('Y-m-d', strtotime($to_date)) : '';

        // If category not selected (empty), pass NULL so model won't force exact match.
        $category_for_query = !empty($expense_category) ? $expense_category : NULL;

        $data['result'] = $this->inventory->get_expense_by_date_range_with_category(
            $from,
            $to,
            $category_for_query,
            'indirect',
            $this->user_id
        );
    }

    // Apply employee filter only when user selected an employee for Individual indirect.
    if ($is_individual_category && $employee_name !== '' && !empty($data['result'])) {
        $data['result'] = $this->filter_expense_rows_by_employee($data['result'], $employee_name);
    }

    $session_data_head = $this->session->userdata('session_data_head');
    $this->load->view('admin/header_side_bar', $session_data_head);
    $this->load->view('expense/expenditure_report_indirect', $data);
}


    private function parseDateDMY($dateStr) {
        if (empty($dateStr)) {
            return '';
        }
        $dateObj = DateTime::createFromFormat('d-m-Y', trim($dateStr));
        return $dateObj ? $dateObj->format('Y-m-d') : '';
    }

    public function create_grn_report() {
        $from_date = $this->input->post('from_date');
        $to_date = $this->input->post('to_date');
        
        $date1 = $this->parseDateDMY($from_date);
        $date2 = $this->parseDateDMY($to_date);
        
        $data['result'] = $this->report->get_grn_report_by_date($date1, $date2, $this->user_id);
        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
        
        $this->session->set_userdata('from_date', $from_date);
        $this->session->set_userdata('to_date', $to_date);

        if ($this->input->is_ajax_request()) {
            header('Content-Type: application/json');
            echo json_encode(['result' => $data['result']]);
            return;
        }
        
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('report/create_grn_report', $data);
    }
    
    public function get_grn_report_by_date_xlsx() {
        $from_date = $this->session->userdata('from_date');
        $to_date = $this->session->userdata('to_date');
        
        if (empty($from_date) || empty($to_date)) {
            $from_date = date('d-m-Y', strtotime('first day of this month'));
            $to_date = date('d-m-Y', strtotime('last day of this month'));
        }
        
        $date1 = $this->parseDateDMY($from_date);
        $date2 = $this->parseDateDMY($to_date);
        
        $data['result'] = $this->report->get_grn_report_by_date($date1, $date2, $this->user_id);
        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
        
        $this->session->unset_userdata('from_date');
        $this->session->unset_userdata('to_date');
        
        // Direct Excel view load - no header
        $this->load->view('report/grn_report', $data);
    }

    public function get_sales_purchase_report_by_date_xlsx() {
    // echo "djjdjf";die();
     $from_date = $this->session->userdata('from_date');
     $to_date = $this->session->userdata('to_date');
      $po_dte1 = !empty($from_date) ? strtotime($from_date) : strtotime('now');
     $po_date1 = date('Y-m-d', $po_dte1);
     $po_dte2 = !empty($to_date) ? strtotime($to_date) : strtotime('now');
     $po_date2 = date('Y-m-d', $po_dte2);
     
     $data['from_date'] = $from_date;
     $data['to_date'] = $to_date;
     $data['result_sales'] = $this->report->get_report_by_date($po_date1, $po_date2, $this->user_id);
     $data['result_purchase_bill'] = $this->report->get_pb_report_by_date1($po_date1, $po_date2, $this->user_id);

    
    //  $this->session->unset_userdata('from_date');
    //  $this->session->unset_userdata('to_date');
     $session_data_head = $this->session->userdata('session_data_head');
     $this->load->view('admin/header_side_bar', $session_data_head);
    // print_r( $data['result_sales']);die();
      $this->load->view('report/sales_purchase_report', $data);
  }

    public function material_allocation_report()
    {
        $from_date = $this->input->post('from_date');
        $to_date = $this->input->post('to_date');
        $is_post = ($this->input->server('REQUEST_METHOD') === 'POST');

        if (empty($from_date) && empty($to_date)) {
            $current_year = date('Y');
            $from_date = (date('m') >= 4) ? '01-04-' . $current_year : '01-04-' . ($current_year - 1);
            $to_date = date('d-m-Y');
        }

        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
        $data['result'] = array();
        $data['is_filtered'] = true;

        $date1 = !empty($from_date) ? date('Y-m-d', strtotime($from_date)) : '';
        $date2 = !empty($to_date) ? date('Y-m-d', strtotime($to_date)) : '';
        $this->session->set_userdata('material_alloc_report_from_date', $from_date);
        $this->session->set_userdata('material_alloc_report_to_date', $to_date);

        $data['result'] = $this->material_issue_model->get_material_allocation_report($date1, $date2, null);

        $session_data_head = $this->session->userdata('session_data_head');
        $data['show_project_cols'] = $this->_has_project_permission();
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('report/material_allocation_report', $data);
    }

    public function get_material_allocation_report_xlsx()
    {
        $from_date = $this->session->userdata('material_alloc_report_from_date');
        $to_date = $this->session->userdata('material_alloc_report_to_date');

        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
        $data['result'] = array();

        $date1 = !empty($from_date) ? date('Y-m-d', strtotime($from_date)) : '';
        $date2 = !empty($to_date) ? date('Y-m-d', strtotime($to_date)) : '';
        $data['result'] = $this->material_issue_model->get_material_allocation_report($date1, $date2, null);

        $data['show_project_cols'] = $this->_has_project_permission();
        $this->load->view('report/export_material_allocation_report', $data);
    }

    public function get_material_allocation_report_pdf()
    {
        $from_date = $this->session->userdata('material_alloc_report_from_date');
        $to_date = $this->session->userdata('material_alloc_report_to_date');

        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
        $data['result'] = array();

        $date1 = !empty($from_date) ? date('Y-m-d', strtotime($from_date)) : '';
        $date2 = !empty($to_date) ? date('Y-m-d', strtotime($to_date)) : '';
        $data['result'] = $this->material_issue_model->get_material_allocation_report($date1, $date2, null);

        $data['show_project_cols'] = $this->_has_project_permission();
        $html = $this->load->view('report/pdf_material_allocation_report', $data, true);
        $pdfFilePath = "Material_Allocation_Report_" . date('Ymd_His') . ".pdf";
        $this->load->library('M_pdf');
        $this->m_pdf->pdf->WriteHTML($html);
        $this->m_pdf->pdf->Output($pdfFilePath, "D");
    }

    public function material_reversal_report()
    {
        $from_date = $this->input->post('from_date');
        $to_date = $this->input->post('to_date');
        $is_post = ($this->input->server('REQUEST_METHOD') === 'POST');

        if (empty($from_date) && empty($to_date)) {
            $current_year = date('Y');
            $from_date = (date('m') >= 4) ? '01-04-' . $current_year : '01-04-' . ($current_year - 1);
            $to_date = date('d-m-Y');
        }

        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
        $data['result'] = array();
        $data['is_filtered'] = true;

        $date1 = !empty($from_date) ? date('Y-m-d', strtotime($from_date)) : '';
        $date2 = !empty($to_date) ? date('Y-m-d', strtotime($to_date)) : '';
        $this->session->set_userdata('material_reversal_report_from_date', $from_date);
        $this->session->set_userdata('material_reversal_report_to_date', $to_date);

        $data['result'] = $this->material_issue_model->get_material_reversal_report($date1, $date2, null);

        $session_data_head = $this->session->userdata('session_data_head');
        $data['show_project_cols'] = $this->_has_project_permission();
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('report/material_reversal_report', $data);
    }

    public function get_material_reversal_report_xlsx()
    {
        $from_date = $this->session->userdata('material_reversal_report_from_date');
        $to_date = $this->session->userdata('material_reversal_report_to_date');

        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
        $data['result'] = array();

        $date1 = !empty($from_date) ? date('Y-m-d', strtotime($from_date)) : '';
        $date2 = !empty($to_date) ? date('Y-m-d', strtotime($to_date)) : '';
        $data['result'] = $this->material_issue_model->get_material_reversal_report($date1, $date2, null);

        $data['show_project_cols'] = $this->_has_project_permission();
        $this->load->view('report/export_material_reversal_report', $data);
    }

    public function get_material_reversal_report_pdf()
    {
        $from_date = $this->session->userdata('material_reversal_report_from_date');
        $to_date = $this->session->userdata('material_reversal_report_to_date');

        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
        $data['result'] = array();

        $date1 = !empty($from_date) ? date('Y-m-d', strtotime($from_date)) : '';
        $date2 = !empty($to_date) ? date('Y-m-d', strtotime($to_date)) : '';
        $data['result'] = $this->material_issue_model->get_material_reversal_report($date1, $date2, null);

        $data['show_project_cols'] = $this->_has_project_permission();

        $html = $this->load->view('report/pdf_material_reversal_report', $data, true);
        $pdfFilePath = "Material_Reversal_Report_" . date('Ymd_His') . ".pdf";
        $this->load->library('M_pdf');
        $this->m_pdf->pdf->WriteHTML($html);
        $this->m_pdf->pdf->Output($pdfFilePath, "D");
    }

    public function get_material_issue_report_by_date_pdf()
    {
        $from_date = $this->session->userdata('material_issue_report_from_date');
        $to_date = $this->session->userdata('material_issue_report_to_date');

        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
        $data['result'] = array();

        $date1 = !empty($from_date) ? date('Y-m-d', strtotime($from_date)) : '';
        $date2 = !empty($to_date) ? date('Y-m-d', strtotime($to_date)) : '';
        $data['result'] = $this->material_issue_model->get_material_issue_report($date1, $date2, null);

        $data['show_project_cols'] = $this->_has_project_permission();

        $html = $this->load->view('report/pdf_material_issue_report', $data, true);
        $pdfFilePath = "Material_Issue_Report_" . date('Ymd_His') . ".pdf";
        $this->load->library('M_pdf');
        $this->m_pdf->pdf->WriteHTML($html);
        $this->m_pdf->pdf->Output($pdfFilePath, "D");
    }

    private function _has_project_permission()
    {
        return false;
    }
}
