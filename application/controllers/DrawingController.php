<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DrawingController extends MY_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->model('Drawing_model');
        $this->load->helper('file');
        $this->load->helper('form');
        $this->load->helper('url');
        $this->load->library('form_validation');
        $this->load->library('session');
        
        // Check session
        $session_data_head = $this->session->userdata('session_data_head');
        if (!isset($session_data_head)) {
            redirect('LoginController/logout');
        }
    }
    
    // ==================== DRAWING MASTER METHODS ====================
    
    /**
     * Index page - List all drawings
     */
    public function index() {
        $data['drawings'] = $this->Drawing_model->get_all_drawings();
        $data['projects'] = $this->Drawing_model->get_active_projects();
        $this->load->view('drawing/add_drawing', $data);
    }
    
    /**
     * Add new drawing
     */
    public function add_drawing() {
        // Set validation rules
        $this->form_validation->set_rules('project_id_fk', 'Project', 'required');
        $this->form_validation->set_rules('drawing_no', 'Drawing Number', 'required|callback_check_duplicate_drawing_no');
        $this->form_validation->set_rules('drawing_name', 'Drawing Name', 'required');
        
        if ($this->form_validation->run() == FALSE) {
            // Validation failed
            $data['drawings'] = $this->Drawing_model->get_all_drawings();
            $data['projects'] = $this->Drawing_model->get_active_projects();
            $this->load->view('drawing/add_drawing', $data);
        } else {
            $project_id_fk = $this->input->post('project_id_fk');
            if (strpos($project_id_fk, 'SO_') === 0) {
                $so_id = substr($project_id_fk, 3);
                $project_id_fk = $this->Drawing_model->create_project_for_so($so_id);
            }
            
            // Prepare data for insertion
            $data = array(
                'project_id_fk' => $project_id_fk,
                'drawing_no' => $this->input->post('drawing_no'),
                'drawing_name' => $this->input->post('drawing_name'),
                'current_revision' => '001', // Start with revision 001
                'status' => 'active'
            );
            
            // Insert drawing
            $drawing_id = $this->Drawing_model->insert_drawing($data);
            
             if ($drawing_id) {
                // Check if initial files were uploaded
                $has_file = false;
                if (isset($_FILES['drawing_files'])) {
                    foreach ($_FILES['drawing_files']['name'] as $name) {
                        if (!empty($name)) {
                            $has_file = true;
                            break;
                        }
                    }
                }

                if ($has_file) {
                    // Create initial revision with the uploaded files
                    $this->_create_initial_revision_with_file($drawing_id);
                } else {
                    // Create initial revision without file
                    $this->_create_initial_revision($drawing_id);
                }
                
                $this->session->set_flashdata('SUCCESSMSG', 'Drawing added successfully!');
            } else {
                $this->session->set_flashdata('INFOMSG', 'Failed to add drawing.');
            }
            
            redirect('DrawingController/index');
        }
    }
    
    /**
     * Create initial revision for new drawing with multiple files
     */
    private function _create_initial_revision_with_file($drawing_id) {
        $session_data = $this->session->userdata('session_data_head');
        $uploaded_by = isset($session_data['result']['user_name']) ? $session_data['result']['user_name'] : '';
        
        $revision_data = array(
            'drawing_id_fk' => $drawing_id,
            'revision_no' => '001',
            'revision_date' => date('Y-m-d'),
            'change_description' => 'Initial version',
            'revision_note' => 'First revision',
            'uploaded_by' => $uploaded_by,
            'status' => 'active'
        );
        
        $revision_id = $this->Drawing_model->insert_revision($revision_data);
        
        if ($revision_id) {
            // Handle multiple file uploads for initial revision
            $this->_upload_multiple_files($drawing_id, '001', $revision_id);
        }
    }
    
    /**
     * Create initial revision for new drawing without file
     */
    private function _create_initial_revision($drawing_id) {
        $session_data = $this->session->userdata('session_data_head');
        $uploaded_by = isset($session_data['result']['user_name']) ? $session_data['result']['user_name'] : '';
        
        $revision_data = array(
            'drawing_id_fk' => $drawing_id,
            'revision_no' => '001',
            'revision_date' => date('Y-m-d'),
            'change_description' => 'Initial version',
            'revision_note' => 'First revision',
            'uploaded_by' => $uploaded_by,
            'status' => 'active'
        );
        
        $revision_id = $this->Drawing_model->insert_revision($revision_data);
    }
    
    /**
     * Upload single file (for initial revision)
     */
    private function _upload_single_file($drawing_id, $revision_no, $revision_id) {
        // Create directory structure: uploads/drawings/drawing_{id}/rev_{revision_no}/
        $upload_dir = './uploads/drawings/drawing_' . $drawing_id . '/rev_' . $revision_no . '/';
        
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, TRUE);
        }
        
        if (isset($_FILES['drawing_file']) && $_FILES['drawing_file']['error'] == 0 && !empty($_FILES['drawing_file']['name'])) {
            $file_name = $_FILES['drawing_file']['name'];
            $file_tmp = $_FILES['drawing_file']['tmp_name'];
            $file_size = $_FILES['drawing_file']['size'];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            
            // Create unique filename
            $timestamp = time();
            $safe_name = preg_replace('/[^a-zA-Z0-9\._-]/', '', $file_name);
            $new_file_name = $timestamp . '_' . $safe_name;
            $target_path = $upload_dir . $new_file_name;
            
            // Validate file type
            $allowed_types = array('pdf', 'jpg', 'jpeg', 'png', 'gif', 'dwg', 'dxf', 'doc', 'docx', 'xls', 'xlsx');
            if (in_array($file_ext, $allowed_types) && $file_size <= 5242880) { // 5MB max
                if (move_uploaded_file($file_tmp, $target_path)) {
                    $file_data = array(
                        'revision_id_fk' => $revision_id,
                        'file_name' => $file_name,
                        'file_path' => $target_path,
                        'file_type' => $file_ext,
                        'file_size' => $file_size,
                        'description' => 'Initial revision file'
                    );
                    $this->Drawing_model->insert_file($file_data);
                }
            }
        }
    }
    
    /**
     * Edit drawing
     */
    public function edit_drawing($drawing_id) {
        $data['drawing'] = $this->Drawing_model->get_drawing_by_id($drawing_id);
        $data['drawings'] = $this->Drawing_model->get_all_drawings();
        $data['projects'] = $this->Drawing_model->get_active_projects();
        $data['revisions'] = $this->Drawing_model->get_revisions_by_drawing($drawing_id);
        
        if (!$data['drawing']) {
            $this->session->set_flashdata('INFOMSG', 'Drawing not found.');
            redirect('DrawingController/index');
        }
        
        $this->load->view('drawing/edit_drawing', $data);
    }
    
    /**
     * Update drawing
     */
    public function update_drawing() {
        $drawing_id = $this->input->post('drawing_id');
        
        // Set validation rules
        $this->form_validation->set_rules('project_id_fk', 'Project', 'required');
        $this->form_validation->set_rules('drawing_no', 'Drawing Number', 'required|callback_check_duplicate_drawing_no_update[' . $drawing_id . ']');
        $this->form_validation->set_rules('drawing_name', 'Drawing Name', 'required');
        
        if ($this->form_validation->run() == FALSE) {
            $data['drawing'] = $this->Drawing_model->get_drawing_by_id($drawing_id);
            $data['drawings'] = $this->Drawing_model->get_all_drawings();
            $data['projects'] = $this->Drawing_model->get_active_projects();
            $data['revisions'] = $this->Drawing_model->get_revisions_by_drawing($drawing_id);
            $this->load->view('drawing/edit_drawing', $data);
        } else {
            $project_id_fk = $this->input->post('project_id_fk');
            if (strpos($project_id_fk, 'SO_') === 0) {
                $so_id = substr($project_id_fk, 3);
                $project_id_fk = $this->Drawing_model->create_project_for_so($so_id);
            }
            
            $data = array(
                'project_id_fk' => $project_id_fk,
                'drawing_no' => $this->input->post('drawing_no'),
                'drawing_name' => $this->input->post('drawing_name')
            );
            
            $result = $this->Drawing_model->update_drawing($drawing_id, $data);
            
            if ($result) {
                $this->session->set_flashdata('SUCCESSMSG', 'Drawing updated successfully!');
            } else {
                $this->session->set_flashdata('INFOMSG', 'Failed to update drawing.');
            }
            
            redirect('DrawingController/index');
        }
    }
    
    /**
     * Delete drawing
     */
    public function delete_drawing($drawing_id) {
        $drawing = $this->Drawing_model->get_drawing_by_id($drawing_id);
        
        if ($drawing) {
            // Hard delete drawing and all associated data
            $result = $this->Drawing_model->hard_delete_drawing($drawing_id);
            
            if ($result) {
                // Delete the drawing folder
                $drawing_dir = './uploads/drawings/drawing_' . $drawing_id . '/';
                if (is_dir($drawing_dir)) {
                    $this->_delete_directory($drawing_dir);
                }
                
                $this->session->set_flashdata('SUCCESSMSG', 'Drawing deleted successfully!');
            } else {
                $this->session->set_flashdata('INFOMSG', 'Failed to delete drawing.');
            }
        } else {
            $this->session->set_flashdata('INFOMSG', 'Drawing not found.');
        }
        
        redirect('DrawingController/index');
    }
    
    /**
     * Show drawing details with all revisions
     */
    public function show_drawing($drawing_id) {
        $drawing = $this->Drawing_model->get_drawing_by_id($drawing_id);
        
        if (!$drawing) {
            $this->session->set_flashdata('INFOMSG', 'Drawing not found.');
            redirect('DrawingController/index');
        }
        
        // Get all revisions for this drawing
        $revisions = $this->Drawing_model->get_revisions_by_drawing($drawing_id);
        
        // Get files for each revision and format dates
        foreach ($revisions as $rev) {
            $rev->files = $this->Drawing_model->get_files_by_revision($rev->revision_id);
            // Format revision date for display
            $rev->revision_date_display = $this->format_date_for_display($rev->revision_date);
        }
        
        $data['drawing'] = $drawing;
        $data['revisions'] = $revisions;
        $data['projects'] = $this->Drawing_model->get_active_projects();
        
        $this->load->view('drawing/show_drawing', $data);
    }
    
    // ==================== REVISION METHODS ====================
    
    /**
     * Add revision form
     */
    public function add_revision($drawing_id) {
        $drawing = $this->Drawing_model->get_drawing_by_id($drawing_id);
        
        if (!$drawing) {
            $this->session->set_flashdata('INFOMSG', 'Drawing not found.');
            redirect('DrawingController/index');
        }
        
        $data['drawing'] = $drawing;
        $data['next_revision'] = $this->Drawing_model->get_next_revision_number($drawing_id);
        $this->load->view('drawing/add_revision', $data);
    }
    
    /**
     * Save revision with multiple files
     */
    public function save_revision() {
        $drawing_id = $this->input->post('drawing_id');
        $revision_no = $this->input->post('revision_no');
        $revision_date = $this->input->post('revision_date');
        
        // Set validation rules
        $this->form_validation->set_rules('drawing_id', 'Drawing', 'required');
        $this->form_validation->set_rules('revision_no', 'Revision Number', 'required');
        $this->form_validation->set_rules('revision_date', 'Revision Date', 'required');
        
        if ($this->form_validation->run() == FALSE) {
            $drawing = $this->Drawing_model->get_drawing_by_id($drawing_id);
            $data['drawing'] = $drawing;
            $data['next_revision'] = $this->Drawing_model->get_next_revision_number($drawing_id);
            $this->load->view('drawing/add_revision', $data);
        } else {
            $session_data = $this->session->userdata('session_data_head');
            $uploaded_by = isset($session_data['result']['user_name']) ? $session_data['result']['user_name'] : '';
            
            // Automatically convert date to YYYY-MM-DD format
            $formatted_date = $this->convert_to_db_date($revision_date);
            
            // Insert revision record first
            $revision_data = array(
                'drawing_id_fk' => $drawing_id,
                'revision_no' => $revision_no,
                'revision_date' => $formatted_date,
                'change_description' => $this->input->post('change_description'),
                'revision_note' => $this->input->post('revision_note'),
                'uploaded_by' => $uploaded_by,
                'approved_by' => $this->input->post('approved_by'),
                'status' => 'active'
            );
            
            $revision_id = $this->Drawing_model->insert_revision($revision_data);
            
            if ($revision_id) {
                // Handle multiple file uploads
                $upload_count = $this->_upload_multiple_files($drawing_id, $revision_no, $revision_id);
                
                // Update drawing current revision
                $this->Drawing_model->update_drawing($drawing_id, array('current_revision' => $revision_no));
                
                // Supersede old revisions
                $this->Drawing_model->supersede_old_revisions($drawing_id, $revision_no);
                
                if ($upload_count > 0) {
                    $this->session->set_flashdata('SUCCESSMSG', 'Revision ' . $revision_no . ' added successfully with ' . $upload_count . ' file(s)!');
                } else {
                    $this->session->set_flashdata('SUCCESSMSG', 'Revision ' . $revision_no . ' added successfully!');
                }
            } else {
                $this->session->set_flashdata('INFOMSG', 'Failed to add revision.');
            }
            
            redirect('DrawingController/edit_drawing/' . $drawing_id);
        }
    }
    
    /**
     * Convert any date format to YYYY-MM-DD for database storage
     * This function automatically detects and converts various date formats
     */
    private function convert_to_db_date($date_string) {
        // Trim whitespace
        $date_string = trim($date_string);
        
        // If empty, return current date
        if (empty($date_string)) {
            return date('Y-m-d');
        }
        
        // Check if it's already in YYYY-MM-DD format
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date_string)) {
            $parts = explode('-', $date_string);
            if (checkdate($parts[1], $parts[2], $parts[0])) {
                return $date_string;
            }
        }
        
        // Remove any extra characters and try to parse
        $date_string = preg_replace('/[^0-9\/\-\. ]/', '', $date_string);
        
        // Try to detect and convert various formats
        $formats = array(
            // European formats (DD/MM/YYYY)
            'd/m/Y', 'd-m-Y', 'd.m.Y', 'd M Y', 'd M, Y',
            // US formats (MM/DD/YYYY)
            'm/d/Y', 'm-d-Y', 'm.d.Y', 'M d Y', 'M d, Y',
            // Other formats
            'Y/m/d', 'Y-m-d', 'Y.m.d',
            // With time
            'Y-m-d H:i:s', 'd/m/Y H:i:s', 'm/d/Y H:i:s'
        );
        
        foreach ($formats as $format) {
            $date_obj = DateTime::createFromFormat($format, $date_string);
            if ($date_obj && $date_obj->format($format) === $date_string) {
                return $date_obj->format('Y-m-d');
            }
        }
        
        // Try strtotime as last resort
        $timestamp = strtotime($date_string);
        if ($timestamp !== false && $timestamp > 0) {
            return date('Y-m-d', $timestamp);
        }
        
        // If all fails, return current date
        return date('Y-m-d');
    }
    
    /**
     * Convert database date to display format (DD-MM-YYYY)
     */
    private function format_date_for_display($date_string) {
        if (empty($date_string) || $date_string == '0000-00-00') {
            return '';
        }
        
        $date_obj = DateTime::createFromFormat('Y-m-d', $date_string);
        if ($date_obj) {
            return $date_obj->format('d-m-Y');
        }
        
        // If not in YYYY-MM-DD format, try to convert
        $converted = $this->convert_to_db_date($date_string);
        if ($converted != $date_string) {
            $date_obj = DateTime::createFromFormat('Y-m-d', $converted);
            if ($date_obj) {
                return $date_obj->format('d-m-Y');
            }
        }
        
        return $date_string;
    }
    
    /**
     * View revision details with all files
     */
    public function view_revision($revision_id) {
        $revision = $this->Drawing_model->get_revision_with_files($revision_id);
        
        if (!$revision) {
            $this->session->set_flashdata('INFOMSG', 'Revision not found.');
            redirect('DrawingController/index');
        }
        
        // Format dates for display
        $revision->revision_date_display = $this->format_date_for_display($revision->revision_date);
        $revision->created_at_display = date('d-m-Y H:i:s', strtotime($revision->created_at));
        
        $data['revision'] = $revision;
        $data['drawing'] = $this->Drawing_model->get_drawing_by_id($revision->drawing_id_fk);
        $data['files'] = $revision->files;
        $this->load->view('drawing/view_revision', $data);
    }
    
    /**
     * Delete revision
     */
    public function delete_revision($revision_id) {
        $revision = $this->Drawing_model->get_revision_by_id($revision_id);
        
        if ($revision) {
            $drawing_id = $revision->drawing_id_fk;
            
            // Delete revision and its files
            $result = $this->Drawing_model->delete_revision($revision_id);
            
            if ($result) {
                // Update drawing current revision to latest available
                $latest_rev = $this->Drawing_model->get_latest_revision($drawing_id);
                if ($latest_rev) {
                    $this->Drawing_model->update_drawing($drawing_id, array('current_revision' => $latest_rev->revision_no));
                } else {
                    // No revisions left, set current revision to null
                    $this->Drawing_model->update_drawing($drawing_id, array('current_revision' => null));
                }
                
                // Delete the revision folder
                $rev_dir = './uploads/drawings/drawing_' . $drawing_id . '/rev_' . $revision->revision_no . '/';
                if (is_dir($rev_dir)) {
                    $this->_delete_directory($rev_dir);
                }
                
                $this->session->set_flashdata('SUCCESSMSG', 'Revision ' . $revision->revision_no . ' deleted successfully!');
            } else {
                $this->session->set_flashdata('INFOMSG', 'Failed to delete revision.');
            }
            
            redirect('DrawingController/edit_drawing/' . $drawing_id);
        } else {
            $this->session->set_flashdata('INFOMSG', 'Revision not found.');
            redirect('DrawingController/index');
        }
    }
    
    /**
     * Get revisions for AJAX request
     */
    public function get_revisions_ajax($drawing_id) {
        $revisions = $this->Drawing_model->get_revisions_by_drawing($drawing_id);
        
        $result = array();
        foreach ($revisions as $rev) {
            // Get files for this revision
            $files = $this->Drawing_model->get_files_by_revision($rev->revision_id);
            
            $result[] = array(
                'revision_id' => $rev->revision_id,
                'revision_no' => $rev->revision_no,
                'revision_date' => $this->format_date_for_display($rev->revision_date),
                'revision_date_db' => $rev->revision_date,
                'change_description' => $rev->change_description,
                'revision_note' => $rev->revision_note,
                'uploaded_by' => $rev->uploaded_by,
                'approved_by' => $rev->approved_by,
                'status' => $rev->status,
                'created_at' => date('d-m-Y H:i:s', strtotime($rev->created_at)),
                'files' => $files
            );
        }
        
        echo json_encode(array(
            'status' => !empty($result) ? 'success' : 'empty',
            'data' => $result
        ));
    }
    
    // ==================== FILE METHODS ====================
    
    /**
     * Download file
     */
    public function download_file($file_id) {
        $file = $this->Drawing_model->get_file_by_id($file_id);
        
        if ($file && !empty($file->file_path) && file_exists($file->file_path)) {
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $file->file_name . '"');
            header('Content-Length: ' . filesize($file->file_path));
            header('Cache-Control: private, max-age=0, must-revalidate');
            header('Pragma: public');
            
            readfile($file->file_path);
            exit;
        } else {
            $this->session->set_flashdata('INFOMSG', 'File not found.');
            redirect('DrawingController/index');
        }
    }
    
    /**
     * View file inline
     */
    public function view_file($file_id) {
        $file = $this->Drawing_model->get_file_by_id($file_id);
        
        if ($file && !empty($file->file_path) && file_exists($file->file_path)) {
            $file_ext = strtolower(pathinfo($file->file_name, PATHINFO_EXTENSION));
            
            // Map common extensions to content types for browser rendering
            $content_types = array(
                'pdf'  => 'application/pdf',
                'jpg'  => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'png'  => 'image/png',
                'gif'  => 'image/gif',
                'txt'  => 'text/plain',
                'html' => 'text/html',
                'htm'  => 'text/html'
            );
            
            $content_type = isset($content_types[$file_ext]) ? $content_types[$file_ext] : 'application/octet-stream';
            
            header('Content-Type: ' . $content_type);
            header('Content-Disposition: inline; filename="' . $file->file_name . '"');
            header('Content-Length: ' . filesize($file->file_path));
            header('Cache-Control: private, max-age=0, must-revalidate');
            header('Pragma: public');
            
            readfile($file->file_path);
            exit;
        } else {
            $this->session->set_flashdata('INFOMSG', 'File not found.');
            redirect('DrawingController/index');
        }
    }
    
    /**
     * Delete file
     */
    public function delete_file($file_id) {
        $file = $this->Drawing_model->get_file_by_id($file_id);
        
        if ($file) {
            // Get revision to redirect back
            $revision = $this->Drawing_model->get_revision_by_id($file->revision_id_fk);
            
            // Delete physical file
            if (file_exists($file->file_path)) {
                @unlink($file->file_path);
            }
            
            // Delete database record
            $result = $this->Drawing_model->delete_file($file_id);
            
            if ($result) {
                $this->session->set_flashdata('SUCCESSMSG', 'File deleted successfully!');
            } else {
                $this->session->set_flashdata('INFOMSG', 'Failed to delete file.');
            }
            
            if ($revision) {
                redirect('DrawingController/view_revision/' . $revision->revision_id);
            } else {
                redirect('DrawingController/index');
            }
        } else {
            $this->session->set_flashdata('INFOMSG', 'File not found.');
            redirect('DrawingController/index');
        }
    }
    
    // ==================== UPLOAD METHODS ====================
    
    /**
     * Upload multiple files for a revision
     */
    private function _upload_multiple_files($drawing_id, $revision_no, $revision_id) {
        $upload_count = 0;
        $files_data = array();
        
        // Create directory structure: uploads/drawings/drawing_{id}/rev_{revision_no}/
        $upload_dir = './uploads/drawings/drawing_' . $drawing_id . '/rev_' . $revision_no . '/';
        
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, TRUE);
        }
        
        // Handle multiple file uploads
        if (isset($_FILES['drawing_files'])) {
            $files = $_FILES['drawing_files'];
            $file_count = count($files['name']);
            
            for ($i = 0; $i < $file_count; $i++) {
                if ($files['error'][$i] == 0 && !empty($files['name'][$i])) {
                    $file_name = $files['name'][$i];
                    $file_tmp = $files['tmp_name'][$i];
                    $file_size = $files['size'][$i];
                    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                    
                    // Create unique filename
                    $timestamp = time();
                    $safe_name = preg_replace('/[^a-zA-Z0-9\._-]/', '', $file_name);
                    $new_file_name = $timestamp . '_' . $i . '_' . $safe_name;
                    $target_path = $upload_dir . $new_file_name;
                    
                    // File description (if provided via separate array)
                    $file_description = isset($_POST['file_description'][$i]) ? $_POST['file_description'][$i] : '';
                    
                    // Validate file type
                    $allowed_types = array('pdf', 'jpg', 'jpeg', 'png', 'gif', 'dwg', 'dxf', 'doc', 'docx', 'xls', 'xlsx');
                    if (in_array($file_ext, $allowed_types) && $file_size <= 5242880) { // 5MB max
                        if (move_uploaded_file($file_tmp, $target_path)) {
                            $files_data[] = array(
                                'revision_id_fk' => $revision_id,
                                'file_name' => $file_name,
                                'file_path' => $target_path,
                                'file_type' => $file_ext,
                                'file_size' => $file_size,
                                'description' => $file_description
                            );
                            $upload_count++;
                        }
                    }
                }
            }
            
            // Insert all file records
            if (!empty($files_data)) {
                $this->Drawing_model->insert_files($files_data);
            }
        }
        
        return $upload_count;
    }
    
    /**
     * Delete directory recursively
     */
    private function _delete_directory($dir) {
        if (!file_exists($dir)) {
            return true;
        }
        
        if (!is_dir($dir)) {
            return unlink($dir);
        }
        
        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }
            
            if (!$this->_delete_directory($dir . DIRECTORY_SEPARATOR . $item)) {
                return false;
            }
        }
        
        return rmdir($dir);
    }
    
    // ==================== VALIDATION CALLBACKS ====================
    
    /**
     * Check duplicate drawing number for new drawing
     */
    public function check_duplicate_drawing_no($drawing_no) {
        $project_id = $this->input->post('project_id_fk');
        if (strpos($project_id, 'SO_') === 0) {
            return TRUE;
        }
        if ($this->Drawing_model->check_duplicate_drawing_no($drawing_no, $project_id)) {
            $this->form_validation->set_message('check_duplicate_drawing_no', 'Drawing number already exists for this project.');
            return FALSE;
        }
        return TRUE;
    }
    
    /**
     * Check duplicate drawing number for update
     */
    public function check_duplicate_drawing_no_update($drawing_no, $drawing_id) {
        $project_id = $this->input->post('project_id_fk');
        if (strpos($project_id, 'SO_') === 0) {
            return TRUE;
        }
        if ($this->Drawing_model->check_duplicate_drawing_no($drawing_no, $project_id, $drawing_id)) {
            $this->form_validation->set_message('check_duplicate_drawing_no_update', 'Drawing number already exists for this project.');
            return FALSE;
        }
        return TRUE;
    }
}
?>