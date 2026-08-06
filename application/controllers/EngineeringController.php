<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class EngineeringController extends MY_Controller {

    protected $user_id;

    public function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this->load->helper('file');
        $this->load->helper('form');
        $this->load->helper('url');
        $this->load->library('form_validation');
        $this->load->model('Engineering_model');

        $session_data_head = $this->session->userdata('session_data_head');
        $this->user_id = (int)($session_data_head['result']['user_id'] ?? 1);

        if (!isset($session_data_head['result']['user_id'])) {
            $this->session->sess_destroy();
            redirect('LoginController/logout');
        }
    }

    // ================= DATASHEET UPLOAD =================

    public function datasheets() {
        $data['sales_orders'] = $this->Engineering_model->get_sales_orders();
        $data['datasheets']   = $this->Engineering_model->get_all_datasheets();

        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('engineering/datasheets', $data);
    }

    public function get_bom_items() {
        $so_number = $this->input->post('so_number');
        $items = $this->Engineering_model->get_bom_items_by_so($so_number);
        echo json_encode($items);
    }

    public function upload_datasheet() {
        $this->form_validation->set_rules('salesorder_id_fk', 'Sales Order', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->datasheets();
            return;
        }

        $so_id = $this->input->post('salesorder_id_fk');
        $so_info = $this->Engineering_model->get_sales_order_by_id($so_id);
        if (!$so_info) {
            $this->session->set_flashdata('INFOMSG', 'Invalid Sales Order selected.');
            redirect('EngineeringController/datasheets');
            return;
        }

        if (empty($_FILES['datasheet_file']['name'])) {
            $this->session->set_flashdata('INFOMSG', 'Please select a file to upload.');
            redirect('EngineeringController/datasheets');
            return;
        }

        $file_ext = strtolower(pathinfo($_FILES['datasheet_file']['name'], PATHINFO_EXTENSION));
        if (!in_array($file_ext, ['pdf', 'xls', 'xlsx'])) {
            $this->session->set_flashdata('INFOMSG', 'Invalid file type. Only Excel (.xls, .xlsx) and PDF (.pdf) files are allowed.');
            redirect('EngineeringController/datasheets');
            return;
        }

        $upload_dir = 'uploads/engineering_datasheets/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $config['upload_path']   = $upload_dir;
        $config['allowed_types'] = 'pdf|xls|xlsx';
        $config['max_size']      = 20480; // 20MB max
        $config['file_name']     = 'Datasheet_' . preg_replace('/[^a-zA-Z0-9_\-]/', '_', $so_info->so_number) . '_' . time();

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('datasheet_file')) {
            $this->session->set_flashdata('INFOMSG', 'File Upload Error: ' . $this->upload->display_errors('', ''));
            redirect('EngineeringController/datasheets');
            return;
        }

        $upload_data = $this->upload->data();
        $session_data = $this->session->userdata('session_data_head');
        $uploaded_by = $session_data['result']['user_name'] ?? 'Admin';

        $file_type_cat = ($file_ext === 'pdf') ? 'pdf' : 'excel';

        $data_insert = array(
            'salesorder_id_fk' => $so_id,
            'so_number'        => $so_info->so_number,
            'equipment_name'   => $this->input->post('equipment_name'),
            'bom_item_id_fk'   => $this->input->post('bom_item_id_fk') ? $this->input->post('bom_item_id_fk') : NULL,
            'file_name'        => $upload_data['file_name'],
            'original_name'    => $_FILES['datasheet_file']['name'],
            'file_path'        => $upload_dir . $upload_data['file_name'],
            'file_type'        => $file_type_cat,
            'file_size'        => $upload_data['file_size'],
            'uploaded_by'      => $uploaded_by,
            'uploaded_at'      => date('Y-m-d H:i:s'),
            'remarks'          => $this->input->post('remarks')
        );

        $this->Engineering_model->insert_datasheet($data_insert);
        $this->session->set_flashdata('SUCCESSMSG', 'Datasheet uploaded successfully.');
        redirect('EngineeringController/datasheets');
    }

    public function delete_datasheet($id) {
        $this->Engineering_model->delete_datasheet($id);
        $this->session->set_flashdata('SUCCESSMSG', 'Datasheet deleted successfully.');
        redirect('EngineeringController/datasheets');
    }

    // ================= BUDGET SHEET UPLOAD =================

    public function budget_sheets() {
        $data['sales_orders'] = $this->Engineering_model->get_sales_orders();
        $data['budgets']      = $this->Engineering_model->get_all_budgets();

        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('engineering/budget_sheets', $data);
    }

    public function upload_budget() {
        $this->form_validation->set_rules('salesorder_id_fk', 'Sales Order', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->budget_sheets();
            return;
        }

        $so_id = $this->input->post('salesorder_id_fk');
        $so_info = $this->Engineering_model->get_sales_order_by_id($so_id);
        if (!$so_info) {
            $this->session->set_flashdata('INFOMSG', 'Invalid Sales Order selected.');
            redirect('EngineeringController/budget_sheets');
            return;
        }

        if (empty($_FILES['budget_file']['name'])) {
            $this->session->set_flashdata('INFOMSG', 'Please select a file to upload.');
            redirect('EngineeringController/budget_sheets');
            return;
        }

        $file_ext = strtolower(pathinfo($_FILES['budget_file']['name'], PATHINFO_EXTENSION));
        if (!in_array($file_ext, ['pdf', 'xls', 'xlsx'])) {
            $this->session->set_flashdata('INFOMSG', 'Invalid file type. Only Excel (.xls, .xlsx) and PDF (.pdf) files are allowed.');
            redirect('EngineeringController/budget_sheets');
            return;
        }

        $upload_dir = 'uploads/engineering_budgets/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $config['upload_path']   = $upload_dir;
        $config['allowed_types'] = 'pdf|xls|xlsx';
        $config['max_size']      = 20480; // 20MB max
        $config['file_name']     = 'Budget_' . preg_replace('/[^a-zA-Z0-9_\-]/', '_', $so_info->so_number) . '_' . time();

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('budget_file')) {
            $this->session->set_flashdata('INFOMSG', 'File Upload Error: ' . $this->upload->display_errors('', ''));
            redirect('EngineeringController/budget_sheets');
            return;
        }

        $upload_data = $this->upload->data();
        $session_data = $this->session->userdata('session_data_head');
        $uploaded_by = $session_data['result']['user_name'] ?? 'Admin';

        $file_type_cat = ($file_ext === 'pdf') ? 'pdf' : 'excel';

        $data_insert = array(
            'salesorder_id_fk' => $so_id,
            'so_number'        => $so_info->so_number,
            'budget_title'     => $this->input->post('budget_title'),
            'file_name'        => $upload_data['file_name'],
            'original_name'    => $_FILES['budget_file']['name'],
            'file_path'        => $upload_dir . $upload_data['file_name'],
            'file_type'        => $file_type_cat,
            'file_size'        => $upload_data['file_size'],
            'uploaded_by'      => $uploaded_by,
            'uploaded_at'      => date('Y-m-d H:i:s'),
            'remarks'          => $this->input->post('remarks')
        );

        $this->Engineering_model->insert_budget($data_insert);
        $this->session->set_flashdata('SUCCESSMSG', 'Budget sheet uploaded successfully.');
        redirect('EngineeringController/budget_sheets');
    }

    public function delete_budget($id) {
        $this->Engineering_model->delete_budget($id);
        $this->session->set_flashdata('SUCCESSMSG', 'Budget sheet deleted successfully.');
        redirect('EngineeringController/budget_sheets');
    }

    // ================= FILE DOWNLOAD =================

    public function download_file($type, $id) {
        $this->load->helper('download');

        if ($type === 'datasheet') {
            $row = $this->Engineering_model->get_datasheet_by_id($id);
        } else if ($type === 'budget') {
            $row = $this->Engineering_model->get_budget_by_id($id);
        } else {
            show_404();
            return;
        }

        if ($row && !empty($row->file_path) && file_exists($row->file_path)) {
            force_download($row->file_path, NULL);
        } else {
            $this->session->set_flashdata('INFOMSG', 'Requested file does not exist or has been removed.');
            if ($type === 'datasheet') {
                redirect('EngineeringController/datasheets');
            } else {
                redirect('EngineeringController/budget_sheets');
            }
        }
    }
}
