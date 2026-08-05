<?php 

   class Welcome extends MY_Controller {
       
       function __construct() {
        parent::__construct();
               $this->load->model('login');

        $this->load->library('session');
    }

  
      public function index() { 
       $data['company_logo'] = $this->login->get_logo_settings();

         $this->load->view('login', $data);
      }
      
       public function database_backup() {
            $session_data_head = $this->session->userdata('session_data_head');
            if (empty($session_data_head['result']['user_id'])) {
                redirect('LoginController/logout');
                return;
            }
            $this->load->dbutil();
        
        $app_name = $this->config->item('app_name');
        $db_format = array('format' => 'zip', 'filename' => $app_name . '_' . date('d-M-Y') .'.sql');
        $backup = $this->dbutil->backup($db_format);
        $this->load->helper('file');
        $dbname = $app_name . date('Y-m-d') . '.zip';
        $save = 'assets/db/' . $dbname;
        write_file($save, $backup);
         $this->load->helper('download');
        force_download($dbname, $backup);
    }
   } 
?>

