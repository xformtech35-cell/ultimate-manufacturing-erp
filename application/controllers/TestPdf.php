<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

Class TestPdf extends MY_Controller {

    protected $user_id;

    function __construct() {
        parent::__construct();

        $this->load->library('session');



        $session_data_head = $this->session->userdata('session_data_head');
        $this->user_id = $session_data_head['result']['user_id'];

//        if($this->user_id === NULL) { 
//            $this->session->sess_destroy();
//            $this->session->set_flashdata('SUCCESSMSG', "You have been Logged Out !!");
//            redirect('LoginController/logout');
//        }
    }

    public function index() {
        $mpdf = new \Mpdf\Mpdf();
        
$mpdf->SetTitle('My title');

$mpdf->WriteHTML('This is the beginning...', \Mpdf\HTMLParserMode::HTML_BODY, true, false);
$mpdf->WriteHTML('...this is the middle...', \Mpdf\HTMLParserMode::HTML_BODY, false, false);
$mpdf->WriteHTML('...and this is the end', \Mpdf\HTMLParserMode::HTML_BODY, false, true);

$file_name = 'Niketan.pdf';
$mpdf->Output($file_name, 'D');
    }

    
}
