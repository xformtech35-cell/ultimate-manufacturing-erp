<?php

defined('BASEPATH') or exit('No direct script access allowed');

class LoginController extends MY_Controller
{

    protected $user_id;

    function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->model('login', '', TRUE);
        $this->load->library('form_validation');
        $this->load->library('image_lib');

        $session_data_head = $this->session->userdata('session_data_head');

        // Fix: Check if session_data_head exists and is an array with the expected structure
        if (empty($session_data_head) || !is_array($session_data_head) || !isset($session_data_head['result']) || !isset($session_data_head['result']['user_id'])) {
            $this->user_id = 0;
        } else {
            $this->user_id = $session_data_head['result']['user_id'];
        }
    }

    public function index()
    {
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('change_password');
    }

    public function get_settings()
    {
        $data['settings'] = $this->login->get_settings($this->user_id);
        //        $data["result"] = $data['settings'];
        //        $this->session->set_userdata(array('session_data_head' => $data));
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('admin/settings', $data);
    }

    public function login_user()
    {
        $user_email = $this->input->post('user_email');
        $password = $this->input->post('password');


        // echo $user_email . $password;
        //        
        //   die();

        $result = $this->login->login_user($user_email, $password);
        $data["result"] = $result;

        if ($result == false) {
            $this->session->set_flashdata('INFOMSG', "Username or Password is wrong!!");
            $this->load->view('login');
        } else {


            $data['password_str'] = $password;

            //        var_dump($result);
            //        die();

            $data['settings'] = $this->login->get_settings($this->user_id);
            $perm_result = $this->login->get_user_permission($result['role']);

            $permission = array();
            foreach ($perm_result as $key) {
                array_push($permission, $key->grp_perm);
            }

            $data['permission'] = $permission;
            //var_dump($permission);
            //die();

            $this->session->set_userdata(array('session_data_head' => $data));
            $this->session->unset_userdata('INFOMSG');

            if ($data["result"]) {
                $this->log_activity("User logged in - " . $result['username']);
                redirect('Home/index');
            } else {
                $this->session->set_flashdata('INFOMSG', "Username or Password is wrong!!");
                $this->load->view('login');
            }
        }
    }

    public function logout()
    {
        $data['company_logo'] = $this->login->get_logo_settings();
        $this->log_activity("User logged out");

        $this->session->sess_destroy();
        $this->session->set_flashdata('SUCCESSMSG', "Logout Successfully!!");
        $this->load->view('login', $data);
    }

    public function change_password()
    {

        $email_id = $this->input->post('email_id');
        $password = $this->input->post('new_password');
        $encrypted_password = password_hash($password, PASSWORD_DEFAULT);


        $check_email = $this->login->check_email_address($email_id);

        if ($check_email == TRUE) {
            $result = $this->login->email_update_password($email_id, $encrypted_password);
            if ($result == TRUE) {
                $this->log_activity("Changed password - " . $email_id);
                $this->session->set_flashdata('SUCCESSMSG', "Password change Successfully!!");
                $this->session->sess_destroy();
                $this->load->view('login');
            } else {
                $this->session->set_flashdata('INFOMSG', "Password not change Successfully!!");
                redirect('Home/index');
            }
        } else {
            $this->session->set_flashdata('INFOMSG', "Email ID not exist!!");
            redirect('LoginController/logout');
        }
    }

    public function mobile_number_check()
    {
        $customer_mobile = $this->input->post('customer_mobile');

        $result = $this->login->mobile_number_exist($customer_mobile);

        if ($result == TRUE) {
            $this->session->set_flashdata('SUCCESSMSG', "Customer added successfully!!");
            redirect('CustomerController/index');
        } else {
            $this->session->set_flashdata('INFOMSG', "Customer not added successfully!!");
            redirect('CustomerController/index');
        }
    }

    public function add_settings()
    {
        //$company_logo = $this->input->post('company_logo');

        $setting_id = $this->input->post('setting_id');

        $invoice_default_payment_term = $this->input->post('invoice_default_payment_term');
        $invoice_title = $this->input->post('invoice_title');
        $invoice_subheading = $this->input->post('invoice_subheading');
        $invoice_footer = $this->input->post('invoice_footer');
        $invoice_memo = $this->input->post('invoice_memo');
        $invoice_notes = $this->input->post('invoice_notes');
        $invoice_terms_and_conditions = $this->input->post('invoice_terms_and_conditions');
        $invoice_payment_terms = $this->input->post('invoice_payment_terms');
        $invoice_process_schedule = $this->input->post('invoice_process_schedule');
        $invoice_taxes = $this->input->post('invoice_taxes');
        $invoice_exclusions = $this->input->post('invoice_exclusions');

        $address = $this->input->post('address');
        $state_code = $this->input->post('state_code');
        $cin = $this->input->post('cin');
        $company_name = $this->input->post('company_name');
        $company_gst = $this->input->post('company_gst');
        $company_pan = $this->input->post('company_pan');

        $mobile = $this->input->post('mobile');
        $email = $this->input->post('email');

        $quotation_title = $this->input->post('quotation_title');
        $quotation_subheading = $this->input->post('quotation_subheading');
        $quotation_footer = $this->input->post('quotation_footer');
        $quotation_memo = $this->input->post('quotation_memo');
        $notes = $this->input->post('notes');
        $terms_and_conditions = $this->input->post('terms_and_conditions');
        $payment_terms = $this->input->post('payment_terms');
        $process_schedule = $this->input->post('process_schedule');
        $taxes = $this->input->post('taxes');
        $exclusions = $this->input->post('exclusions');

        $purchase_requisition_notes = $this->input->post('purchase_requisition_notes');


        //For po
        $po_title = $this->input->post('po_title');
        $po_subheading = $this->input->post('po_subheading');
        $po_footer = $this->input->post('po_footer');
        $po_memo = $this->input->post('po_memo');
        $po_note = $this->input->post('po_note');
        $po_terms_and_conditions = $this->input->post('po_terms_and_conditions');
        $po_payment_terms = $this->input->post('po_payment_terms');
        $po_process_schedule = $this->input->post('po_process_schedule');
        $po_taxes = $this->input->post('po_taxes');
        $po_exclusions = $this->input->post('po_exclusions');

        //For proforma invoice
        $proforma_title = $this->input->post('proforma_title');
        $proforma_subheading = $this->input->post('proforma_subheading');
        $proforma_footer = $this->input->post('proforma_footer');
        $proforma_memo = $this->input->post('proforma_memo');
        $proforma_note = $this->input->post('proforma_note');
        $proforma_terms_and_conditions = $this->input->post('proforma_terms_and_conditions');
        $proforma_payment_terms = $this->input->post('proforma_payment_terms');
        $proforma_process_schedule = $this->input->post('proforma_process_schedule');
        $proforma_taxes = $this->input->post('proforma_taxes');
        $proforma_exclusions = $this->input->post('proforma_exclusions');

        //For sales order
        $so_title = $this->input->post('so_title');
        $so_subheading = $this->input->post('so_subheading');
        $so_footer = $this->input->post('so_footer');
        $so_memo = $this->input->post('so_memo');
        $so_note = $this->input->post('so_note');
        $so_terms_and_conditions = $this->input->post('so_terms_and_conditions');
        $so_payment_terms = $this->input->post('so_payment_terms');
        $so_process_schedule = $this->input->post('so_process_schedule');
        $so_taxes = $this->input->post('so_taxes');
        $so_exclusions = $this->input->post('so_exclusions');

        $dc_title = $this->input->post('dc_title');
        $dc_subheading = $this->input->post('dc_subheading');
        $dc_footer = $this->input->post('dc_footer');
        $dc_memo = $this->input->post('dc_memo');
        $dc_note = $this->input->post('dc_note');
        $dc_terms_and_conditions = $this->input->post('dc_terms_and_conditions');
        $dc_payment_terms = $this->input->post('dc_payment_terms');
        $dc_process_schedule = $this->input->post('dc_process_schedule');
        $dc_taxes = $this->input->post('dc_taxes');
        $dc_exclusions = $this->input->post('dc_exclusions');

        $pv_title = $this->input->post('pv_title');
        $pv_subheading = $this->input->post('pv_subheading');
        $pv_footer = $this->input->post('pv_footer');
        $pv_memo = $this->input->post('pv_memo');
        $pv_note = $this->input->post('pv_note');
        $pv_terms_and_conditions = $this->input->post('pv_terms_and_conditions');
        $pv_payment_terms = $this->input->post('pv_payment_terms');
        $pv_process_schedule = $this->input->post('pv_process_schedule');
        $pv_taxes = $this->input->post('pv_taxes');
        $pv_exclusions = $this->input->post('pv_exclusions');




        $data_settings = array(
            'invoice_default_payment_term' => $invoice_default_payment_term,
            'invoice_title' => $invoice_title,
            'invoice_subheading' => $invoice_subheading,
            'invoice_footer' => $invoice_footer,
            'invoice_memo' => $invoice_memo,
            'invoice_notes' => $invoice_notes,
            'invoice_terms_and_conditions' => $invoice_terms_and_conditions,
            'invoice_payment_terms' => $invoice_payment_terms,
            'invoice_process_schedule' => $invoice_process_schedule,
            'invoice_taxes' => $invoice_taxes,
            'invoice_exclusions' => $invoice_exclusions,
            'quotation_title' => $quotation_title,
            'quotation_subheading' => $quotation_subheading,
            'quotation_footer' => $quotation_footer,
            'quotation_memo' => $quotation_memo,
            'notes' => $notes,
            'terms_and_conditions' => $terms_and_conditions,
            'payment_terms' => $payment_terms,
            'process_schedule' => $process_schedule,
            'taxes' => $taxes,
            'exclusions' => $exclusions,
            'po_terms_and_conditions' => $po_terms_and_conditions,
            'po_payment_terms' => $po_payment_terms,
            'po_process_schedule' => $po_process_schedule,
            'po_taxes' => $po_taxes,
            'po_exclusions' => $po_exclusions,
            'address' => $address,
            'state_code' => $state_code,
            'cin' => $cin,
            'company_name' => $company_name,
            'company_gst' => $company_gst,
            'company_pan' => $company_pan,
            'po_title' => $po_title,
            'po_subheading' => $po_subheading,
            'po_footer' => $po_footer,
            'po_memo' => $po_memo,
            'po_note' => $po_note,
            'purchase_requisition_notes' => $purchase_requisition_notes,
            'mobile' => $mobile,
            'email' => $email,
            'uid' => $this->user_id,
            'proforma_title' => $proforma_title,
            'proforma_subheading' => $proforma_subheading,
            'proforma_footer' => $proforma_footer,
            'proforma_memo' => $proforma_memo,
            'proforma_note' => $proforma_note,
            'proforma_terms_and_conditions' => $proforma_terms_and_conditions,
            'proforma_payment_terms' => $proforma_payment_terms,
            'proforma_process_schedule' => $proforma_process_schedule,
            'proforma_taxes' => $proforma_taxes,
            'proforma_exclusions' => $proforma_exclusions,
            'so_title' => $so_title,
            'so_subheading' => $so_subheading,
            'so_footer' => $so_footer,
            'so_memo' => $so_memo,
            'so_note' => $so_note,
            'so_terms_and_conditions' => $so_terms_and_conditions,
            'so_payment_terms' => $so_payment_terms,
            'so_process_schedule' => $so_process_schedule,
            'so_taxes' => $so_taxes,
            'so_exclusions' => $so_exclusions,
            'dc_title' => $dc_title,
            'dc_subheading' => $dc_subheading,
            'dc_footer' => $dc_footer,
            'dc_memo' => $dc_memo,
            'dc_note' => $dc_note,
            'dc_terms_and_conditions' => $dc_terms_and_conditions,
            'dc_payment_terms' => $dc_payment_terms,
            'dc_process_schedule' => $dc_process_schedule,
            'dc_taxes' => $dc_taxes,
            'dc_exclusions' => $dc_exclusions,
            'pv_title' => $pv_title,
            'pv_subheading' => $pv_subheading,
            'pv_footer' => $pv_footer,
            'pv_memo' => $pv_memo,
            'pv_note' => $pv_note,
            'pv_terms_and_conditions' => $pv_terms_and_conditions,
            'pv_payment_terms' => $pv_payment_terms,
            'pv_process_schedule' => $pv_process_schedule,
            'pv_taxes' => $pv_taxes,
            'pv_exclusions' => $pv_exclusions,
        );

        if (file_exists($_FILES['company_stamp']['tmp_name']) || is_uploaded_file($_FILES['company_stamp']['tmp_name'])) {
            $config1['new_image'] = NULL;
            $company_stamp = $_FILES['company_stamp'];
            $config1['image_library'] = 'gd2';
            $config1['source_image'] = $company_stamp['tmp_name'];
            $config1['new_image'] = './uploads/' . $company_stamp['name'];
            $config1['allowed_types'] = 'jpg|png|gif|jpeg';
            $config1['overwrite'] = TRUE;
            // $config1['width'] = 100;
            // $config1['height'] = 100;

            $this->image_lib->initialize($config1);
            $this->image_lib->resize();
            $data_settings['company_stamp'] = $config1['new_image'];
        }

        if (file_exists($_FILES['company_logo']['tmp_name']) || is_uploaded_file($_FILES['company_logo']['tmp_name'])) {
            $config1['new_image'] = NULL;
            $company_logo = $_FILES['company_logo'];
            $config1['image_library'] = 'gd2';
            $config1['source_image'] = $company_logo['tmp_name'];
            $config1['new_image'] = './uploads/' . $company_logo['name'];
            $config1['allowed_types'] = 'jpg|png|gif|jpeg';
            $config1['overwrite'] = TRUE;
            // $config1['width'] = 100;
            // $config1['height'] = 100;

            $this->image_lib->initialize($config1);
            $this->image_lib->resize();
            $data_settings['company_logo'] = $config1['new_image'];
        }

        if (!empty($_POST)) {

            if ($setting_id) {
                $this->login->add_settings($setting_id, $data_settings, $this->user_id);
            } else {
                $this->login->add_new_user_settings($data_settings);
            }
            $this->session->set_flashdata('SUCCESSMSG', "Setting added successfully!!");
            redirect('LoginController/get_settings/');
        } else {
            $this->load->view('admin/settings');
        }
    }
    public function password_hash($pass = '')
    {
        if ($pass) {
            $password = password_hash($pass, PASSWORD_DEFAULT);
            return $password;
        }
    }
    public function forgot_password()
    {

        $this->load->helper('string');
        // $newpassword = random_string('alnum', 8);
        $to_email = $this->input->post('to_email');

        $newpassword = rand(10000, 99999);
        $encrypted_password = $this->password_hash($newpassword);



        //  print_r($encrypted_password);die();
        //        $salt1 = sha1(md5($newpassword));
        //        $encrypted_password = md5($newpassword . $salt1);
        //   print_r($encrypted_password);die();
        $result = $this->login->check_email_address($to_email);

        if ($result == TRUE) {
            $result_email_check = $this->login->email_update_password($to_email, $encrypted_password);

            // Email sending
            $this->load->library('email');

            $this->email->set_mailtype("html");
            $email_setting = $this->db->get('email_setting')->row_array();
            $from_email = !empty($email_setting['from_email']) ? $email_setting['from_email'] : 'xformtech20@gmail.com';
            $company_name = !empty($email_setting['company_name']) ? $email_setting['company_name'] : 'Xform Technologies';

            $this->email->from($from_email, $company_name);
            $this->email->to($to_email);
            $this->email->subject('Reset password from ' . $company_name);

            // Set content-type header for sending HTML email
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= 'From: Xform <contact@xform.in>' . "\r\n";
            //print_r($newpassword);die();

            $this->email->message($newpassword);
            $this->email->message('Hello Sir/Madam, <br> ' . PHP_EOL . 'Your new password : <b>' . $newpassword . '</b>');

            if ($result_email_check == TRUE) {
                $this->email->send();
                $this->session->set_flashdata('SUCCESSMSG', "Email Sent successfully on your mail account");
                redirect('LoginController/logout');
            } else {
                $this->session->set_flashdata('INFOMSG', "Email not Sent Successfully!!");
                redirect('LoginController/logout');
            }
        } else {
            $this->session->set_flashdata('INFOMSG', "Email ID not exist!!");
            redirect('LoginController/logout');
        }
    }
}
