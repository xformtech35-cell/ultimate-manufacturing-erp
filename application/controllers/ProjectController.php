<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Font;

class ProjectController extends MY_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this->load->model('Project_model');
        $this->load->model('Customer');
        $this->load->helper('file');
        $this->load->helper('form');
        $this->load->library('form_validation');
        $this->load->library('image_lib'); // Added for image handling if needed

        if (!$this->session->userdata('session_data_head')) {
            redirect('LoginController/logout');
        }
    }

    public function index() {
        $data['projects'] = $this->Project_model->get_all_projects();
        $data['customers'] = $this->Customer->get_customer();
        $this->load->view('project/add_project', $data);
    }

    public function add_project() {
        // Set validation rules - using callback instead of is_unique
        $this->form_validation->set_rules('project_code', 'Project Code', 'required|callback_check_duplicate_project_code');
        $this->form_validation->set_rules('project_name', 'Project Name', 'required');
        $this->form_validation->set_rules('system', 'System', 'required');
        $this->form_validation->set_rules('project_status', 'Project Status', 'required');
        $this->form_validation->set_rules('project_start_date', 'Start Date', 'required');

        if ($this->form_validation->run() == FALSE) {
            // Validation failed - get projects for the table and show form with errors
            $data['projects'] = $this->Project_model->get_all_projects();
            $data['customers'] = $this->Customer->get_customer();
            $this->load->view('project/add_project', $data);
        } else {
            // Handle file upload - similar to stamp approach
            $document_path = '';
            
            if (isset($_FILES['upload_project_doc']) && $_FILES['upload_project_doc']['name'] != '') {
                // Create upload directory if not exists
                $upload_dir = './uploads/projects/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, TRUE);
                }
                
                $file = $_FILES['upload_project_doc'];
                $file_name = time() . '_' . preg_replace('/[^a-zA-Z0-9\._-]/', '', $file['name']);
                $target_path = $upload_dir . $file_name;
                
                // Move uploaded file
                if (move_uploaded_file($file['tmp_name'], $target_path)) {
                    $document_path = $target_path;
                } else {
                    $this->session->set_flashdata('INFOMSG', 'Failed to upload document.');
                    redirect('ProjectController/index');
                }
            }

            // Prepare data for insertion - store file path instead of binary content
            $data = array(
                'project_code' => $this->input->post('project_code'),
                'project_name' => $this->input->post('project_name'),
                'system' => $this->input->post('system'),
                'opportunity_name' => $this->input->post('opportunity_name') ?: '',
                'project_status' => $this->input->post('project_status'),
                'project_start_date' => $this->input->post('project_start_date'),
                'project_completed_date' => $this->input->post('project_completed_date') ?: '1970-01-01',
                'forecast_completed_date' => $this->input->post('forecast_completed_date') ?: '1970-01-01',
                'project_description' => $this->input->post('project_description') ?: '',
                'upload_project_doc' => $document_path, // Store file path instead of binary
                'organisation_name' => $this->input->post('organisation_name')
            );




        //    var_dump($data);
        //     die();

            // Insert into database
            $result = $this->Project_model->insert_project($data);

            if ($result) {
                $this->session->set_flashdata('SUCCESSMSG', 'Project added successfully!');
            } else {
                $this->session->set_flashdata('INFOMSG', 'Failed to add project. Please try again.');
            }

            redirect('ProjectController/index');
        }
    }

// Callback function to check duplicate project code
public function check_duplicate_project_code($code) {
    // Using CI3 Query Builder
    $this->db->where('project_code', $code);
    $query = $this->db->get('project');
    
    if ($query->num_rows() > 0) {
        $this->form_validation->set_message('check_duplicate_project_code', 'The {field} must be unique. This project code already exists.');
        return FALSE;
    }
    
    return TRUE;
}

    public function edit_project($project_id) {
        $data['project'] = $this->Project_model->get_project_by_id($project_id);
        $data['projects'] = $this->Project_model->get_all_projects();
        $data['customers'] = $this->Customer->get_customer();
        $this->load->view('project/edit_project', $data);
    }

    public function update_project() {
        $project_id = $this->input->post('project_id');
        
        // Validate project_code uniqueness except for current project
        $this->form_validation->set_rules('project_code', 'Project Code', 'required|callback_check_project_code_update['.$project_id.']');
        $this->form_validation->set_rules('project_name', 'Project Name', 'required');
        $this->form_validation->set_rules('system', 'System', 'required');
        $this->form_validation->set_rules('project_status', 'Project Status', 'required');
        $this->form_validation->set_rules('project_start_date', 'Start Date', 'required');

        if ($this->form_validation->run() == FALSE) {
            // Validation failed
            $data['project'] = $this->Project_model->get_project_by_id($project_id);
            $data['projects'] = $this->Project_model->get_all_projects();
            $data['customers'] = $this->Customer->get_customer();
            $this->load->view('project/edit_project', $data);
        } else {
            $data = array(
                'project_code' => $this->input->post('project_code'),
                'project_name' => $this->input->post('project_name'),
                'system' => $this->input->post('system'),
                'opportunity_name' => $this->input->post('opportunity_name'),
                'project_status' => $this->input->post('project_status'),
                'project_start_date' => $this->input->post('project_start_date'),
                'project_completed_date' => $this->input->post('project_completed_date') ?: '1970-01-01',
                'forecast_completed_date' => $this->input->post('forecast_completed_date') ?: '1970-01-01',
                'project_description' => $this->input->post('project_description'),
                'organisation_name' => $this->input->post('organisation_name')
            );

            // Handle file upload if new file is selected - similar to stamp approach
            if (isset($_FILES['upload_project_doc']) && $_FILES['upload_project_doc']['name'] != '') {
                // Create upload directory if not exists
                $upload_dir = './uploads/projects/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, TRUE);
                }
                
                // Delete old file if exists
                $old_project = $this->Project_model->get_project_by_id($project_id);
                if ($old_project && !empty($old_project->upload_project_doc) && file_exists($old_project->upload_project_doc)) {
                    unlink($old_project->upload_project_doc);
                }
                
                $file = $_FILES['upload_project_doc'];
                $file_name = time() . '_' . preg_replace('/[^a-zA-Z0-9\._-]/', '', $file['name']);
                $target_path = $upload_dir . $file_name;
                
                // Move uploaded file
                if (move_uploaded_file($file['tmp_name'], $target_path)) {
                    $data['upload_project_doc'] = $target_path;
                } else {
                    $this->session->set_flashdata('INFOMSG', 'Failed to upload document.');
                    redirect('ProjectController/edit_project/' . $project_id);
                }
            }

            $result = $this->Project_model->update_project($project_id, $data);

            if ($result) {
                $this->session->set_flashdata('SUCCESSMSG', 'Project updated successfully!');
            } else {
                $this->session->set_flashdata('INFOMSG', 'Failed to update project.');
            }

            redirect('ProjectController/index');
        }
    }

    // Callback function to check unique project code during update
public function check_project_code_update($code, $id) {
    // Using CI3 Query Builder
    $this->db->select('*');
    $this->db->from('project');
    $this->db->where('project_code', $code);
    $this->db->where('project_id !=', $id);
    $query = $this->db->get();
    
    if ($query->num_rows() > 0) {
        $this->form_validation->set_message('check_project_code_update', 'The {field} must be unique. This project code already exists.');
        return FALSE;
    } else {
        return TRUE;
    }
}

    public function delete_project($project_id) {
        // Get project details to delete file
        $project = $this->Project_model->get_project_by_id($project_id);
        
        // Delete file if exists
        if ($project && !empty($project->upload_project_doc) && file_exists($project->upload_project_doc)) {
            unlink($project->upload_project_doc);
        }
        
        $result = $this->Project_model->delete_project($project_id);
        
        if ($result) {
            $this->session->set_flashdata('SUCCESSMSG', 'Project deleted successfully!');
        } else {
            $this->session->set_flashdata('INFOMSG', 'Failed to delete project.');
        }
        
        redirect('ProjectController/index');
    }

    public function download_document($project_id) {
        $project = $this->Project_model->get_project_by_id($project_id);
        
        if ($project && !empty($project->upload_project_doc) && file_exists($project->upload_project_doc)) {
            // Get file info
            $file_path = $project->upload_project_doc;
            $file_name = basename($file_path);
            
            // Set headers for download
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $file_name . '"');
            header('Content-Length: ' . filesize($file_path));
            
            // Read file and output
            readfile($file_path);
            exit;
        } else {
            $this->session->set_flashdata('INFOMSG', 'No document found');
            redirect('ProjectController/index');
        }
    }

    public function export_projects() {
        // Load PHPSpreadsheet library (modern replacement for PHPExcel)
        require_once APPPATH . '../vendor/autoload.php';
        
        // Get all projects
        $projects = $this->Project_model->get_all_projects();
        
        // Create new Spreadsheet object
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set document properties
        $spreadsheet->getProperties()
            ->setCreator("Sameep Accounting")
            ->setLastModifiedBy("System")
            ->setTitle("Projects Export")
            ->setSubject("Projects List")
            ->setDescription("Exported projects data");
        
        // Set column headers
        $headers = array('Sr.No.', 'Project Code', 'Project Name', 'System', 'Opportunity Name', 'Status', 'Start Date', 'Completed Date', 'Organization', 'Description');
        $sheet->fromArray($headers, NULL, 'A1');
        
        // Style header row
        for ($col = 'A'; $col <= 'J'; $col++) {
            $style = $sheet->getStyle($col . '1');
            
            // Set fill color (blue background)
            $style->getFill()->setFillType(Fill::FILL_SOLID);
            $style->getFill()->getStartColor()->setARGB('FF4472C4');
            
            // Set font (bold white text)
            $style->getFont()->setBold(true);
            $style->getFont()->getColor()->setARGB('FFFFFFFF');
            
            // Set alignment (center)
            $style->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $style->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        }
        
        // Add data rows
        $row = 2;
        $i = 1;
        if (!empty($projects)) {
            foreach ($projects as $project) {
                $sheet->setCellValue('A' . $row, $i);
                $sheet->setCellValue('B' . $row, isset($project->project_code) ? $project->project_code : '');
                $sheet->setCellValue('C' . $row, isset($project->project_name) ? $project->project_name : '');
                $sheet->setCellValue('D' . $row, isset($project->system) ? $project->system : '');
                $sheet->setCellValue('E' . $row, isset($project->opportunity_name) ? $project->opportunity_name : '');
                $sheet->setCellValue('F' . $row, isset($project->project_status) ? $project->project_status : '');
                
                // Format date
                if (!empty($project->project_start_date) && $project->project_start_date != '0000-00-00' && $project->project_start_date != '1970-01-01') {
                    $sheet->setCellValue('G' . $row, date('d-m-Y', strtotime($project->project_start_date)));
                } else {
                    $sheet->setCellValue('G' . $row, '');
                }
                
                if (!empty($project->project_completed_date) && $project->project_completed_date != '0000-00-00' && $project->project_completed_date != '1970-01-01') {
                    $sheet->setCellValue('H' . $row, date('d-m-Y', strtotime($project->project_completed_date)));
                } else {
                    $sheet->setCellValue('H' . $row, '');
                }
                
                $sheet->setCellValue('I' . $row, isset($project->organisation_name) ? $project->organisation_name : '');
                $sheet->setCellValue('J' . $row, isset($project->project_description) ? $project->project_description : '');
                
                $row++;
                $i++;
            }
        }
        
        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(8);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(15);
        $sheet->getColumnDimension('G')->setWidth(15);
        $sheet->getColumnDimension('H')->setWidth(15);
        $sheet->getColumnDimension('I')->setWidth(20);
        $sheet->getColumnDimension('J')->setWidth(25);
        
        // Set sheet name
        $sheet->setTitle('Projects');
        
        // Create Excel file writer object
        $writer = new Xlsx($spreadsheet);
        
        // Send to browser
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="Projects_' . date('d-m-Y') . '.xlsx"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }
}