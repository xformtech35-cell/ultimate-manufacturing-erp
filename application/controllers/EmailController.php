<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class EmailController extends MY_Controller {

    protected $user_id;

    function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this->load->model('login', '', TRUE);
        $this->load->model('email', '', TRUE);
        $this->load->library('form_validation');
        $this->load->library('image_lib');

        $session_data_head = $this->session->userdata('session_data_head');
        $this->user_id = $session_data_head['result']['user_id'];
        
        if($this->user_id === NULL) { 
            $this->session->sess_destroy();
            $this->session->set_flashdata('SUCCESSMSG', "You have been Logged Out !!");
            redirect('LoginController/logout');
        }
        
    }

    public function email_setting() {
        $data['email_set'] = $this->email->get_email_settings($this->user_id);
        $email = $data['email_set'];
        $this->session->set_userdata(array('session_data_head2' => $email));
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('email/email', $data);
    }

//    public function get_email_settings() {
//        $data['email_set'] = $this->email->get_email_settings($this->user_id);
//        $session_data_head = $this->session->userdata('session_data_head');
//        $this->load->view('admin/header_side_bar', $session_data_head);
//        $this->load->view('email/email', $data);
//    }

    public function add_email_settings() {
        $email_setting_id = $this->input->post('email_setting_id');
        $company_name = $this->input->post('company_name');
        $company_website = $this->input->post('company_website');
        $from_email = $this->input->post('from_email');
        $password_email = $this->input->post('password_email');
        $cc_email = $this->input->post('cc_email');
       
        // for file upload start
        if ($this->input->post('company_logo')) {
            //'company_logo' => $config['new_image'], 
            $data_settings = array('company_name' => $company_name, 'company_website' => $company_website,
               'from_email' => $from_email, 'password_email' => $password_email, 'cc_email' => $cc_email, 'uid' => $this->user_id);
        } else {
            // for file upload start
            $config['new_image'] = null;
            $horoscope_check = $_FILES['company_logo'];
            $config['image_library'] = 'gd2';
            $config['source_image'] = $horoscope_check['tmp_name'];
            $config['new_image'] = './uploads/' . $horoscope_check['name'];
            $config['allowed_types'] = 'jpg|png|gif|jpeg';
            $config['overwrite'] = TRUE;
            $config['width'] = 800;
            $config['height'] = 500;

            $this->image_lib->initialize($config);
            $this->image_lib->resize();

            // for file upload end

            if (!empty($horoscope_check['tmp_name'])) {
                $data_settings = array('company_logo' => $config['new_image'], 'company_name' => $company_name, 'company_website' => $company_website,
                    'from_email' => $from_email, 'password_email' => $password_email, 'cc_email' => $cc_email,  'uid' => $this->user_id);
            } else {
                $data_settings = array('company_name' => $company_name, 'company_website' => $company_website,
                    'from_email' => $from_email, 'password_email' => $password_email, 'cc_email' => $cc_email, 'uid' => $this->user_id);
            }
        }

        if (!empty($_POST)) {

            if ($email_setting_id) {
                $this->email->add_email_settings($email_setting_id, $data_settings, $this->user_id);
            } else {
                $this->email->add_new_user_email_settings($data_settings);
            }
            $this->session->set_flashdata('SUCCESSMSG', "Email Setting added successfully!!");
            redirect('EmailController/email_setting/');
        } else {
            $this->load->view('email/email');
        }
    }

}
