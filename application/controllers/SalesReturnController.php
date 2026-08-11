<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class SalesReturnController extends MY_Controller {

    protected $user_id;

    function __construct() {
        parent::__construct();

        $this->load->library('session');
        $this->load->model('login', '', TRUE);
         $this->load->model('customer', '', TRUE);
         $this->load->model('salesreturn', '', TRUE);
          $this->load->model('inventory', '', TRUE);
        $session_data_head = $this->session->userdata('session_data_head');
        $this->user_id = $session_data_head['result']['user_id'];

        if ($this->user_id === NULL) {
            $this->session->sess_destroy();
            $this->session->set_flashdata('SUCCESSMSG', "You have been Logged Out !!");
            redirect('LoginController/logout');
        }
    }

        public function create_central_gst_purchase() {
        $data['quotation_id'] = $this->estimate->get_last_quotation_number($this->user_id);
        $data['po_id'] = $this->salesreturn->get_last_po_number($this->user_id);
        $data['result'] = $this->customer->get_customer($this->user_id);
        $data['product_name'] = $this->inventory->get_product_part_name($this->user_id);
        $data['item_name'] = $this->inventory->get_item_name($this->user_id);
       // print_r($data['item_name']);        die();
        $data['settings'] = $this->login->get_settings($this->user_id);
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('sales_return/create_central_gst_purchase', $data);
    }
    
    
    public function delete_item_sales_return() {
        $sr_return_id = $this->input->post('sr_return_id');
        $result = $this->salesreturn->delete_item_sales_return($sr_return_id, $this->user_id);
        echo json_encode($result);
    }

   
   
    
    
     public function sales_return(){
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('sales_return/view_sales_return');
     }
     public function create_sales_return() {
        $data['sales_return_id'] = $this->salesreturn->get_last_sales_return_number($this->user_id);
        
        $data['result'] = $this->customer->get_customer($this->user_id);
        $data['product_name'] = $this->inventory->get_product_part_name($this->user_id);
        $data['item_name'] = $this->inventory->get_item_name($this->user_id);
       // print_r($data['item_name']);        die();
        $data['settings'] = $this->login->get_settings($this->user_id);
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('sales_return/create_sales_return', $data);
    }
    
       public function create_central_gst_sales_return() {
        $data['sales_return_id'] = $this->salesreturn->get_last_sales_return_number($this->user_id);
        $data['result'] = $this->customer->get_customer($this->user_id);
        $data['product_name'] = $this->inventory->get_product_part_name($this->user_id);
        $data['item_name'] = $this->inventory->get_item_name($this->user_id);
       // print_r($data['item_name']);        die();
        $data['settings'] = $this->login->get_settings($this->user_id);
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('sales_return/create_central_gst_sales_return', $data);
    }
    
    
    
    
    
        public function add_sales_return() {
            
     $customer_id = $this->input->post('customer_id');
        $number = $this->input->post('number');

        $date = date("Y-m-d", strtotime($this->input->post('date')));

        $delivery_date = $this->input->post('delivery_date');
        $po = $this->input->post('po');
        $item = $this->input->post('product_name');
        $quantity = $this->input->post('quantity');
        $unit = $this->input->post('unit');
        $hsn = $this->input->post('hsn');
        $gst_per = $this->input->post('gst_per');
        
        $price = $this->input->post('price');
        $discount = $this->input->post('discount');
        $amount = $this->input->post('amount');
        $gst_amount = $this->input->post('gst_amount');
        $gst_check = $this->input->post('gst_check');
        $ref_no = $this->input->post('ref_no');
        $status = $this->input->post('status');
        
        
          $sgst = $this->input->post('sgst');
            $cgst = $this->input->post('cgst');
             $igst = $this->input->post('igst');

        $sgst = '0';
        $igst = '0';
        if ($gst_check == 'gst_check') {
            $igst = '1';
            $sgst = $this->input->post('sgst');
            $cgst = $this->input->post('cgst');
            $gst_type = "S";
        } else {
            $sgst = '1';
            $cgst = '1';
            $igst = $this->input->post('igst');
            $gst_type = "I";
        }
        
        
        
        

        $payment_method = $this->input->post('payment_method');
        $total_quotation_amount = round($this->input->post('total_quotation_amount'));

        $subheading = $this->input->post('subheading');
        $footer = $this->input->post('footer');
        $memo = $this->input->post('memo');
        $reasons = $this->input->post('reasons');
        $description = $this->input->post('description');
        
        
        $po_terms_and_conditions = $this->input->post('po_terms_and_conditions');
        $po_payment_terms = $this->input->post('po_payment_terms');
        $po_process_schedule = $this->input->post('po_process_schedule');
        $po_taxes = $this->input->post('po_taxes');
        $po_exclusions = $this->input->post('po_exclusions');
        $po_note = $this->input->post('po_note');


        $item_count = count($item);

        
        
        for ($i = 0; $i < $item_count; $i++) {
            
             if ($gst_check == 'central_gst_check') {
                  
                    $igst1 = $igst[$i];
                        $sgst1 = '0';
                        $cgst1 = '0'; 
              }else{
                    $igst1 = '0';
                        $sgst1 = $sgst[$i];
                        $cgst1 = $cgst[$i];
              }
              
                if ($item[$i] != ''){
              
                  $data[] = array(
                    'customer_id_fk' => $customer_id,
                    'number' => $number,
                    'date' => $date,
                    
                    'product_name' => $item[$i],
                    'quantity' => $quantity[$i],
                    'unit' => $unit[$i],
                    'hsn_code' => $hsn[$i],
                    'gst' => $gst_per[$i],
                      'sgst' => $sgst1,
                        'cgst' => $cgst1,
                        'igst' => $igst1,
                        'gst_type' => $gst_type,
                    'price' => $price[$i],
                    'discount' => $discount[$i],
                    'amount' => $amount[$i],
                  'description' => $description[$i],
                    'uid' => $this->user_id,
                );
            } else {
                $flag = 1;
            }   
        }
      
        if ($flag == 0) {
            
            $this->db->insert_batch('sales_return', $data);
            
            $data_toatl_amount = array('total' => $total_quotation_amount, 'number_fk' => $number, 'status' => $status, 'delivery_date' => $this->input->post('delivery_date'), 'footer' => $footer, 'memo' => $memo, 'customer_id_fk' => $customer_id, 'po_date' => $this->input->post('date'),
               'ref_no' => $ref_no, 'payment_method' => $payment_method, 'uid' => $this->user_id);
                
            $result = $this->salesreturn->add_sales_return_total_amount($data_toatl_amount);

            if ($result == TRUE) {
                $this->session->set_flashdata('SUCCESSMSG', "Purchase Return added successfully!!");
                redirect('SalesReturnController/view_sales_return');
            } else {
                $this->session->set_flashdata('INFOMSG', "Purchase Return not added successfully!!");
                redirect('SalesReturnController/view_sales_return');
            }
        }

    }
    
    public function view_sales_return() {
        $str = $this->input->get('str');
      
      // print_r($str);die();
       if($str=="All"){  
           
            $data['sales_return'] = $this->salesreturn->get_sales_return($this->user_id);
        
        }else{
            $month_year = date('M-Y');
            $data['sales_return'] = $this->salesreturn->get_sales_return_purmonthyearwise_record($month_year, $this->user_id);
        }
       // print_r($data['sales_return']);die();
        
        
        $data['settings'] = $this->login->get_settings($this->user_id);
        $data['sales_return_id'] = $this->salesreturn->get_last_sales_return_number($this->user_id);
        $data['result'] = $this->customer->get_customer($this->user_id);
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('sales_return/view_sales_return', $data);
    }
    
        public function delete_sales_return_by_po_return_number() {
        $id = $this->uri->segment(3);
        $id1 = $this->uri->segment(4);
        $id2 = $this->uri->segment(5);
        $id3 = $this->uri->segment(6);
        $po_number = $id . "/" . $id1 . "/" . $id2 . "/" . $id3;
        $result = $this->salesreturn->delete_sales_return_by_po_return_number($po_number, $this->user_id);
        if ($result == TRUE) {
            $this->session->set_flashdata('SUCCESSMSG', "Purchase Order deleted successfully!!");
            redirect('SalesReturnController/view_sales_return');
        } else {
            $this->session->set_flashdata('INFOMSG', "Purchase Order not deleted successfully!!");
            redirect('SalesReturnController/view_sales_return');
        }
    }
    
    
    public function edit_sales_return_details() {
    $number = $this->input->get('number');
    $gst_type = $this->input->get('gst_type');
    
    if($gst_type == 'I'){
        $data['gst_check'] = "central_gst_check";
    }else{
        $data['gst_check'] = "gst_check";
    }

    $data['item_name'] = $this->inventory->get_item_name($this->user_id);
    $data['show_sales_return'] = $this->salesreturn->get_sales_return_data($number, $this->user_id);
    $data['sales_return_data_group'] = $this->salesreturn->get_sales_return_data_group_by($number, $this->user_id);
    $data['result'] = $this->customer->get_customer($this->user_id);
    $data['product_code_list'] = $this->inventory->get_product_part_name($this->user_id);
    
    // Add settings for company details if needed in edit view
    $data['settings'] = $this->login->get_settings($this->user_id);
    
    $session_data_head = $this->session->userdata('session_data_head');
    $this->load->view('admin/header_side_bar', $session_data_head);
    $this->load->view('sales_return/edit_sales_return', $data);
}
    
    
    
       public function edit_sales_return() {
            
        $customer_id = $this->input->post('customer_id');
        $number = $this->input->post('number');

        $date = date("Y-m-d", strtotime($this->input->post('date')));

        $delivery_date = $this->input->post('delivery_date');
        $po = $this->input->post('po');
        $item = $this->input->post('product_name');
        $quantity = $this->input->post('quantity');
        $unit = $this->input->post('unit');
        $hsn = $this->input->post('hsn');
        $gst_per = $this->input->post('gst_per');
        $price = $this->input->post('price');
        $discount = $this->input->post('discount');
        $amount = $this->input->post('amount');
        $gst_amount = $this->input->post('gst_amount');
        $gst_check = $this->input->post('gst_check');
        $ref_no = $this->input->post('ref_no');
        $status = $this->input->post('status');
        
   
        $sgst = $this->input->post('sgst');
        $cgst = $this->input->post('cgst');
         $igst = $this->input->post('igst');

    $sgst = '0';
    $igst = '0';
    if ($gst_check == 'gst_check') {
        $igst = '1';
        $sgst = $this->input->post('sgst');
        $cgst = $this->input->post('cgst');
        $gst_type = "S";
    } else {
        $sgst = '1';
        $cgst = '1';
        $igst = $this->input->post('igst');
        $gst_type = "I";
    }


    // var_dump($sgst);
    // var_dump($cgst);
    
    //  die();

        $payment_method = $this->input->post('payment_method');
        $total_quotation_amount = round($this->input->post('total_quotation_amount'));

        $subheading = $this->input->post('subheading');
        $footer = $this->input->post('footer');
        $memo = $this->input->post('memo');
        $reasons = $this->input->post('reasons');
        $description = $this->input->post('description');
        
        
        $po_terms_and_conditions = $this->input->post('po_terms_and_conditions');
        $po_payment_terms = $this->input->post('po_payment_terms');
        $po_process_schedule = $this->input->post('po_process_schedule');
        $po_taxes = $this->input->post('po_taxes');
        $po_exclusions = $this->input->post('po_exclusions');
        $po_note = $this->input->post('po_note');
        
        $sr_return_id = $this->input->post('sr_return_id');

        //print_r($sr_return_id);die();
        $item_count = is_array($item) ? count($item) : 0;

// echo $item_count;
//         die();
        
        for ($i = 0; $i < $item_count; $i++) {





            if ($gst_check == 'central_gst_check') {
                  
                $igst1 = $igst[$i];
                    $sgst1 = '0';
                    $cgst1 = '0'; 
          }else{
                $igst1 = '0';
                    $sgst1 = $sgst[$i];
                    $cgst1 = $cgst[$i];
          }
          
            if (isset($sr_return_id[$i]) != ''){
          
              $data = array(
                    'customer_id_fk' => $customer_id,
                    'number' => $number,
                    'date' => $date,
                  
                    'product_name' => $item[$i],
                    'quantity' => $quantity[$i],
                    'unit' => $unit[$i],
                    'hsn_code' => $hsn[$i],
                    'gst' => $gst_per[$i],
                      'sgst' => $sgst1,
                        'cgst' => $cgst1,
                        'igst' => $igst1,
                        'gst_type' => $gst_type,
                    'price' => $price[$i],
                    'discount' => $discount[$i],
                    'amount' => $amount[$i],
                
                    //'reasons' => $reasons,
                  'description' => $description[$i],
                  //  'po_pending_quantity' => 'Y',
                    'uid' => $this->user_id,
                );
             // print_r($data); //die();
                  $this->db->where('uid', $this->user_id);
                    $this->db->where('number', $number);
                    $this->db->where('sr_return_id', $sr_return_id[$i]);
                    $this->db->update('sales_return', $data);

                 //   echo "YES $$";
                
            }else{
               // echo "NO $$";
                
                 $data_insert = array(
                    'customer_id_fk' => $customer_id,
                    'number' => $number,
                    'date' => $date,
                    'product_name' => $item[$i],
                    'quantity' => $quantity[$i],
                    'unit' => $unit[$i],
                    'hsn_code' => $hsn[$i],
                    'gst' => $gst_per[$i],
                      'sgst' => $sgst1,
                        'cgst' => $cgst1,
                        'igst' => $igst1,
                        'gst_type' => $gst_type,
                    'price' => $price[$i],
                    'discount' => $discount[$i],
                    'amount' => $amount[$i],
                  //  'subheading' => $subheading,
                    
                    //'reasons' => $reasons,
                  'description' => $description[$i],
                  //  'po_pending_quantity' => 'Y',
                    'uid' => $this->user_id,
                );
               $this->db->insert('sales_return', $data_insert);
            } 
        }


   
    
             $data_toatl_amount = array('total' => $total_quotation_amount, 'number_fk' => $number, 'status' => $status, 'delivery_date' => $this->input->post('delivery_date'), 'footer' => $footer, 'memo' => $memo, 'customer_id_fk' => $customer_id,  'po_date' => $this->input->post('date'),
              'ref_no' => $ref_no,  'payment_method' => $payment_method, 'uid' => $this->user_id);
            
            $result = $this->salesreturn->edit_sales_return_total_amount($data_toatl_amount, $number);
            

            if ($result == TRUE) {
                $this->session->set_flashdata('SUCCESSMSG', "Sales Return updated successfully!!");
                redirect('SalesReturnController/view_sales_return');
            } else {
                $this->session->set_flashdata('INFOMSG', "Sales Return not updated successfully!!");
                redirect('SalesReturnController/view_sales_return');
            }
        
    
       }
        public function show_sales_return() {
            $number = $this->input->get('number');
            $gst_type = $this->input->get('gst_type');
            
            // Get the data
            $data['show_sales_return'] = $this->salesreturn->get_sales_return_data($number, $this->user_id);
            $data['sales_return_data_group'] = $this->salesreturn->get_sales_return_data_group_by($number, $this->user_id);
            $data['gst_type'] = $gst_type;
            
            // IMPORTANT: Get settings for company details
            $data['settings'] = $this->login->get_settings($this->user_id);
            
            // Check if data exists
            if(empty($data['show_sales_return']) || empty($data['sales_return_data_group'])) {
                $this->session->set_flashdata('ERRORMSG', "Sales Return not found!!");
                redirect('SalesReturnController/view_sales_return');
            }
            
            $session_data_head = $this->session->userdata('session_data_head');
            $this->load->view('admin/header_side_bar', $session_data_head);
            $this->load->view('sales_return/show_sales_return', $data);
        }
    
    public function get_sales_return_datewise_record(){
        $from_date = $this->input->post('from_date');
        $to_date = $this->input->post('to_date');
        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
        $data['sales_return'] = $this->salesreturn->get_sales_return_datewise_record($from_date, $to_date, $this->user_id);
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('sales_return/view_sales_return', $data);
    }

    public function get_sales_return_purmonthyearwise_record(){
        $month_year = $this->input->post('month_year');
        $data['sales_return'] = $this->salesreturn->get_sales_return_purmonthyearwise_record($month_year, $this->user_id);
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('sales_return/view_sales_return', $data);
    }

    public function get_sales_return_customer_contact()
    {
        $number = $this->input->post('number');
        $result = $this->salesreturn->get_sales_return_data_group_by($number, $this->user_id);

        $response = array(
            'email' => '',
            'mobile' => ''
        );

        if (!empty($result) && is_array($result)) {
            $response['email'] = isset($result['customer_email']) ? $result['customer_email'] : '';
            if (empty($response['email']) && isset($result['email'])) {
                $response['email'] = $result['email'];
            }

            $response['mobile'] = isset($result['customer_mobile']) ? $result['customer_mobile'] : '';
            if (empty($response['mobile']) && isset($result['mobile'])) {
                $response['mobile'] = $result['mobile'];
            }
        }

        echo json_encode($response);
    }

    public function send_sales_return_email()
    {
        $session_data_head2 = $this->session->userdata('session_data_head2');
        $set_company_name = $session_data_head2['company_name'];
        $set_company_logo = base_url() . '/' . $session_data_head2['company_logo'];
        $set_from_email = $session_data_head2['from_email'];
        $set_cc_email = $session_data_head2['cc_email'];

        $return_number = $this->input->post('number');
        $return_data_group = $this->salesreturn->get_sales_return_data_group_by($return_number, $this->user_id);

        if (empty($return_data_group)) {
            $this->session->set_flashdata('INFOMSG', "Credit Note not found.");
            redirect('SalesReturnController/view_sales_return');
            return;
        }

        $to_email = $this->input->post('to_email');
        $subject = $this->input->post('subject');
        $message = $this->input->post('message');
        $copy_email = $this->input->post('copy_email');
        $download_url = base_url() . 'Pdf/download_sales_return/' . $return_number . '/' . $this->user_id;

        $pdf_file_path = $this->generate_sales_return_pdf($return_number);
        if (!$pdf_file_path || !file_exists($pdf_file_path)) {
            $this->session->set_flashdata('INFOMSG', "Failed to generate Credit Note PDF.");
            redirect('SalesReturnController/view_sales_return');
            return;
        }

        $this->load->library('email');
        $this->email->set_mailtype("html");
        if (strpos(base_url(), 'localhost') !== false) {

    // Local server
    $this->email->from($set_from_email, $set_company_name);

} else {

    // Live server
    $this->email->from("noreply@uwsenvirotech.com", $set_company_name);

}
        $this->email->to($to_email);
        $this->email->subject($subject);

        if ($copy_email) {
            $this->email->cc($set_cc_email);
        }

        $this->email->attach($pdf_file_path);
        $this->email->message($this->create_sales_return_email_html($return_number, $return_data_group, $message, $set_company_name, $set_company_logo, $download_url));

        if ($this->email->send()) {
            $this->db->where('number_fk', $return_number);
            $this->db->where('uid', $this->user_id);
            $this->db->update('sales_return_total', array('status' => '2'));

            if (file_exists($pdf_file_path)) {
                unlink($pdf_file_path);
            }
            $this->session->set_flashdata('SUCCESSMSG', "Credit Note email sent successfully!!");
        } else {
            if (file_exists($pdf_file_path)) {
                unlink($pdf_file_path);
            }
            $this->session->set_flashdata('INFOMSG', "Credit Note email not sent successfully!!");
        }

        redirect('SalesReturnController/view_sales_return');
    }
  
   public function delete_sales_return_item() {
        $sr_return_id = $this->input->post('sr_return_id');
        $result = $this->salesreturn->delete_sales_return_item($sr_return_id);
        echo json_encode($result);
    }

    private function generate_sales_return_pdf($return_number)
    {
        $data['show_sales_return'] = $this->salesreturn->get_sales_return_data($return_number, $this->user_id);
        $data['sales_return_data_group'] = $this->salesreturn->get_sales_return_data_group_by($return_number, $this->user_id);
        $data['settings'] = $this->login->get_settings($this->user_id);

        $html = $this->load->view('admin/sales_return_print', $data, true);

        $uploads_dir = FCPATH . 'uploads/sales_return/';
        if (!is_dir($uploads_dir)) {
            mkdir($uploads_dir, 0777, true);
        }

        $file_name = 'Credit_Note_' . preg_replace('/[^A-Za-z0-9._-]/', '_', $return_number) . '.pdf';
        $pdf_file_path = $uploads_dir . $file_name;

        if (!class_exists('\Mpdf\Mpdf')) {
            require_once APPPATH . '../vendor/autoload.php';
        }

        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 20,
            'margin_bottom' => 20,
            'margin_header' => 10,
            'margin_footer' => 10
        ]);

        $mpdf->WriteHTML($html);
        $mpdf->Output($pdf_file_path, 'F');

        return $pdf_file_path;
    }

    private function create_sales_return_email_html($return_number, $return_data_group, $custom_message, $company_name, $company_logo, $download_url = '')
    {
        $customer_name = $return_data_group['fullname'] ?? ($return_data_group['company_name'] ?? 'Customer');
        $return_date = !empty($return_data_group['date']) ? date('d-m-Y', strtotime($return_data_group['date'])) : date('d-m-Y');
        $grand_total = isset($return_data_group['total']) ? (float) $return_data_group['total'] : 0;

        return '
    <!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Credit Note</title>
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <style>
            .boxs { padding:2% 10%; margin:0 auto; max-width:760px; }
            .shadows1 {
                padding:2% 4%;
                border-radius: 2px;
                line-height: 2;
                text-align: center;
                border: 1px solid grey;
                box-shadow: 0 0 19px rgba(0,0,0,0.18);
                background: #fff;
            }
        </style>
    </head>
    <body style="background:#f8f8f8;">
        <div class="boxs">
            <div class="shadows1">
                <center><img alt="' . $company_name . '" src="' . $company_logo . '" width="30%"></center>
                <span style="color:#2f2f36;font-weight:bold;font-size:32px;"><center>Credit Note</center></span><br>
                <span style="color:#2f2f36;font-weight:bold;font-size:32px;">' . $return_number . '</span><br>
                <span style="color:#a0a0a5;">for <b>' . $customer_name . '</b></span><br>
                <span style="color:#a0a0a5;">date : <b>' . $return_date . '</b></span><br>
                <span style="color:#a0a0a5;">from <b>' . $company_name . '</b></span>
                <hr>
                <span style="color:#2f2f36;">Please find attached our Credit Note PDF.</span>
                <hr>
                <span style="color:#2f2f36;"><b>Message :</b> ' . nl2br(htmlspecialchars($custom_message)) . '</span>
                <hr>
                <span style="color:#2f2f36;font-size:18px">Grand Total : <b>' . number_format($grand_total, 2) . ' INR</b></span>
                <hr>
                ' . (!empty($download_url) ? '<a href="' . $download_url . '" style="background-color:#00929f;border-radius:4px;color:#ffffff;display:inline-block;font-family:sans-serif;font-size:16px;font-weight:bold;line-height:40px;text-align:center;text-decoration:none;width:200px" target="_blank">Download in browser</a><br><hr>' : '') . '
                <span style="color:#2f2f36; font-size:12px;"><b>Note:</b> The Credit Note PDF is attached to this email for your convenience.</span>
                <hr>
                <span style="color:#2f2f36;">Thanks for your business.</span>
            </div>
            <center><span style="color:#2f2f36;">Powered by ' . $company_name . '</span></center>
        </div>
    </body>
</html>';
    }
 
  }
