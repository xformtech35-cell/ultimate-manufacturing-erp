<?php
class TestDbController extends MY_Controller {
    public function __construct() {
        parent::__construct();
    }

    public function test() {
        $project_code = 'GH-PP110';
        $_POST['project_code'] = $project_code;
        
        // Call SalesOrderController's ajax_get_project_details method
        require_once 'application/controllers/SalesOrderController.php';
        $so = new SalesOrderController();
        
        echo "--- Testing ajax_get_project_details for GH-PP110 ---\n";
        $so->ajax_get_project_details();
    }
}
