<?php
defined('BASEPATH') or exit('No direct script access allowed');

class UserController extends MY_Controller
{
    protected $user_id;
    protected $role_name;

    function __construct()
    {
        parent::__construct();

        $this->load->library('session');
        $this->load->library('form_validation');
        $this->load->helper('download');

        $this->load->model('login', '', TRUE);
        $this->load->model('user', '', TRUE);
        $this->load->model('role', '', TRUE);
        $this->load->model('department');
        $this->load->model('LocationModel');

        $session_data_head = $this->session->userdata('session_data_head');
        $this->user_id = $session_data_head['result']['user_id'];
        $this->role_id = $session_data_head['result']['role_id'];

        if ($this->user_id === NULL) {
            $this->session->sess_destroy();
            $this->session->set_flashdata('SUCCESSMSG', "You have been Logged Out !!");
            redirect('LoginController/logout');
        }
    }

    // ===========================================
    // BASIC CRUD METHODS
    // ===========================================

    /**
     * Show all users
     */
    public function index()
    {
        $data['users'] = $this->user->get_all_users_with_department_location();
        $data['role'] = $this->role->get_role();
        $data['department_result'] = $this->department->get_departments();
        $data['location_result'] = $this->LocationModel->get_locations();

        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('user/user_list', $data);
    }

    /**
     * Show add user form
     */
    public function add_user_form()
    {
        $data['role'] = $this->role->get_role();
        $data['department_result'] = $this->department->get_departments();
        $data['location_result'] = $this->LocationModel->get_locations();

        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('user/add_user', $data);
    }

    /**
     * Process add user form (POST)
     */
    public function add_user()
    {
        $this->form_validation->set_rules('username', 'Username', 'required|trim');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');
        $this->form_validation->set_rules('user_email', 'Email', 'required|valid_email|trim');
        $this->form_validation->set_rules('role', 'Role', 'required|numeric');
        $this->form_validation->set_rules('department_id_fk', 'Department', 'numeric');
        $this->form_validation->set_rules('location_id', 'Location', 'numeric');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('ERRORMSG', validation_errors());
            redirect('UserController/add_user_form');
        }

        $username = $this->input->post('username');
        $password = $this->input->post('password');
        $user_email = $this->input->post('user_email');
        $role_id = $this->input->post('role');
        $department_id_fk = $this->input->post('department_id_fk');
        $location_id = $this->input->post('location_id');
        $created_date = date("Y-m-d");

        // Check if email already exists
        if ($this->user->get_user_by_email($user_email)) {
            $this->session->set_flashdata('ERRORMSG', "Email already exists!");
            redirect('UserController/add_user_form');
        }

        // Get role name from role_id - Use direct database query since model method doesn't exist
        $this->db->where('role_id', $role_id);
        $role_query = $this->db->get('role');
        if ($role_query->num_rows() == 0) {
            $this->session->set_flashdata('ERRORMSG', "Invalid role selected!");
            redirect('UserController/add_user_form');
        }
        $role_data = $role_query->row();
        $role_name = $role_data->role_name;

        // Generate secure password hash
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        // Start transaction
        $this->db->trans_start();

        $data_user = array(
            'username' => $username,
            'password' => $password_hash,
            'user_email' => $user_email,
            'role' => $role_id,
            'department_id_fk' => $department_id_fk,
            'location_id' => $location_id,
            'created_date' => $created_date
        );

        $result = $this->user->add_user($data_user);
        $user_id = $this->db->insert_id();

        // Add user role to user_roles table
        if ($result && $user_id) {
            $user_role_data = array(
                'user_id' => $user_id,
                'role_name' => $role_name,
                'department_id' => $department_id_fk,
                'location_id' => $location_id,
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s')
            );
            $this->user->add_or_update_user_role($user_id, $role_name, $user_role_data);
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('ERRORMSG', "Failed to add user!");
            redirect('UserController/add_user_form');
        } else {
            $this->session->set_flashdata('SUCCESSMSG', "User added successfully!!");
            redirect('UserController/index');
        }
    }

    /**
     * Show edit user form (GET)
     */
    public function get_user_by_id($id = null)
    {
        if (empty($id)) {
            $id = $this->input->get('id');
        }

        if (empty($id) || !is_numeric($id)) {
            $this->session->set_flashdata('ERRORMSG', 'Invalid User ID');
            redirect('UserController/index');
        }

        $data['user'] = $this->user->get_user_by_id_with_location($id);
        $data['user_roles'] = $this->user->get_user_roles_by_user_id($id);

        if (!$data['user']) {
            $this->session->set_flashdata('ERRORMSG', 'User not found');
            redirect('UserController/index');
        }

        $data['role'] = $this->role->get_role();
        $data['department_result'] = $this->department->get_departments();
        $data['location_result'] = $this->LocationModel->get_locations();

        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('user/edit_user', $data);
    }

    /**
     * Process edit user form (POST)
     */
    public function edit_user()
    {
        $user_id = $this->input->post('user_id');

        $this->form_validation->set_rules('username', 'Username', 'required|trim');
        $this->form_validation->set_rules('user_email', 'Email', 'required|valid_email|trim');
        $this->form_validation->set_rules('role', 'Role', 'required|numeric');
        $this->form_validation->set_rules('department_id_fk', 'Department', 'numeric');
        $this->form_validation->set_rules('location_id', 'Location', 'numeric');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('ERRORMSG', validation_errors());
            redirect('UserController/get_user_by_id/' . $user_id);
        }

        $username = $this->input->post('username');
        $password = $this->input->post('password');
        $user_email = $this->input->post('user_email');
        $role_id = $this->input->post('role');
        $department_id_fk = $this->input->post('department_id_fk');
        $location_id = $this->input->post('location_id');

        // Get role name from role_id - Use direct database query
        $this->db->where('role_id', $role_id);
        $role_query = $this->db->get('role');
        if ($role_query->num_rows() == 0) {
            $this->session->set_flashdata('ERRORMSG', "Invalid role selected!");
            redirect('UserController/get_user_by_id/' . $user_id);
        }
        $role_data = $role_query->row();
        $role_name = $role_data->role_name;

        // Check if email already exists for another user
        $existing_user = $this->user->get_user_by_email($user_email);
        if ($existing_user && $existing_user['user_id'] != $user_id) {
            $this->session->set_flashdata('ERRORMSG', "Email already exists for another user!");
            redirect('UserController/get_user_by_id/' . $user_id);
        }

        // Start transaction
        $this->db->trans_start();

        $data_user = array(
            'username' => $username,
            'user_email' => $user_email,
            'role' => $role_id,
            'department_id_fk' => $department_id_fk,
            'location_id' => $location_id
        );

        // Only update password if it's provided
        if (!empty($password)) {
            $encrypted_password = password_hash($password, PASSWORD_DEFAULT);
            $data_user['password'] = $encrypted_password;
        }

        $result = $this->user->edit_user($data_user, $user_id);

        // Update user role in user_roles table
        $user_role_data = array(
            'user_id' => $user_id,
            'role_name' => $role_name,
            'department_id' => $department_id_fk,
            'location_id' => $location_id,
            'is_active' => 1,
            'created_at' => date('Y-m-d H:i:s')
        );
        $this->user->add_or_update_user_role($user_id, $role_name, $user_role_data);

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('ERRORMSG', "User not updated successfully!!");
        } else {
            $this->session->set_flashdata('SUCCESSMSG', "User updated successfully!!");
        }
        redirect('UserController/index');
    }

    /**
     * Delete user (GET)
     */
    public function delete_user_by_id($id = null)
    {
        if (empty($id)) {
            $id = $this->input->get('id');
        }

        if (empty($id) || !is_numeric($id)) {
            $this->session->set_flashdata('ERRORMSG', 'Invalid User ID');
            redirect('UserController/index');
        }

        // Prevent deleting your own account
        if ($id == $this->user_id) {
            $this->session->set_flashdata('ERRORMSG', 'You cannot delete your own account!');
            redirect('UserController/index');
        }

        // Start transaction
        $this->db->trans_start();

        // Delete user roles first
        $this->user->delete_user_roles($id);

        // Then delete user
        $result = $this->user->delete_user_by_id($id);

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('ERRORMSG', "Failed to delete user!");
        } else {
            $this->session->set_flashdata('SUCCESSMSG', "User deleted successfully!!");
        }
        redirect('UserController/index');
    }

    /**
     * Manage user roles
     */
    public function manage_roles($user_id = null)
    {
        if (empty($user_id)) {
            $user_id = $this->input->get('id');
        }

        if (empty($user_id) || !is_numeric($user_id)) {
            $this->session->set_flashdata('ERRORMSG', 'Invalid User ID');
            redirect('UserController/index');
        }

        $data['user'] = $this->user->get_user_by_id($user_id);
        $data['user_roles'] = $this->user->get_user_roles_by_user_id($user_id);
        $data['all_roles'] = $this->role->get_role();

        if (!$data['user']) {
            $this->session->set_flashdata('ERRORMSG', 'User not found');
            redirect('UserController/index');
        }

        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('user/manage_roles', $data);
    }

    /**
     * Save user roles
     */
    public function save_roles()
    {
        $user_id = $this->input->post('user_id');
        $roles = $this->input->post('roles');

        if (empty($user_id) || !is_numeric($user_id)) {
            $this->session->set_flashdata('ERRORMSG', 'Invalid User ID');
            redirect('UserController/index');
        }

        // Get user details
        $user = $this->user->get_user_by_id($user_id);
        if (!$user) {
            $this->session->set_flashdata('ERRORMSG', 'User not found');
            redirect('UserController/index');
        }

        // Start transaction
        $this->db->trans_start();

        // Delete existing roles
        $this->user->delete_user_roles($user_id);

        // Add new roles
        if (!empty($roles) && is_array($roles)) {
            foreach ($roles as $role_name) {
                $role_data = array(
                    'user_id' => $user_id,
                    'role_name' => $role_name,
                    'department_id' => $user['department_id_fk'],
                    'location_id' => $user['location_id'],
                    'is_active' => 1,
                    'created_at' => date('Y-m-d H:i:s')
                );
                $this->user->add_user_role($role_data);
            }

            // Update primary role in user table
            if (!empty($roles[0])) {
                // Get role ID by name
                $this->db->where('role_name', $roles[0]);
                $role_query = $this->db->get('role');
                if ($role_query->num_rows() > 0) {
                    $primary_role = $role_query->row();
                    $this->user->edit_user(array('role' => $primary_role->role_id), $user_id);
                }
            }
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('ERRORMSG', "Failed to update roles!");
        } else {
            $this->session->set_flashdata('SUCCESSMSG', "User roles updated successfully!");
        }

        redirect('UserController/get_user_by_id/' . $user_id);
    }

    // ===========================================
    // EXPORT/IMPORT FUNCTIONALITY
    // ===========================================

    /**
     * Show import form
     */
    public function import_form()
    {
        $data['role'] = $this->role->get_role();
        $data['department_result'] = $this->department->get_departments();
        $data['location_result'] = $this->LocationModel->get_locations();

        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('user/import_form', $data);
    }

    /**
     * Export users to Excel
     */
    public function export_excel()
    {
        $users = $this->user->get_all_users_with_department_location();

        // Create HTML table that Excel can open
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="users_export_' . date('Ymd_His') . '.xls"');
        header('Pragma: no-cache');
        header('Expires: 0');

        echo '<!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Users Export</title>
            <style>
                table { border-collapse: collapse; width: 100%; font-family: Arial, sans-serif; }
                th { background-color: #4CAF50; color: white; padding: 12px; text-align: left; font-weight: bold; }
                td { border: 1px solid #ddd; padding: 10px; }
                tr:nth-child(even) { background-color: #f9f9f9; }
                tr:hover { background-color: #f5f5f5; }
                .header { background-color: #337ab7; color: white; padding: 15px; text-align: center; }
            </style>
        </head>
        <body>
            <div class="header">
                <h2>Users Export</h2>
                <p>Generated on: ' . date('d-m-Y') . ' | Total Users: ' . count($users) . '</p>
            </div>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Department</th>
                    <th>Location</th>
                    <th>Created Date</th>
                </tr>';

        foreach ($users as $user) {
            echo '<tr>
                    <td>' . $user->user_id . '</td>
                    <td>' . htmlspecialchars($user->username) . '</td>
                    <td>' . htmlspecialchars($user->user_email) . '</td>
                    <td>' . htmlspecialchars($user->role_name) . '</td>
                    <td>' . htmlspecialchars($user->department_name ?: 'Not Assigned') . '</td>
                    <td>' . htmlspecialchars($user->location_name ?: 'Not Assigned') . '</td>
                    <td>' . date('d-m-Y', strtotime($user->created_date)) . '</td>
                </tr>';
        }

        echo '</table></body></html>';
        exit;
    }

    /**
     * Export users to CSV
     */
    public function export_csv()
    {
        $users = $this->user->get_all_users_with_department_location();

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="users_export_' . date('Ymd_His') . '.csv"');

        $output = fopen('php://output', 'w');
        fputs($output, "\xEF\xBB\xBF"); // UTF-8 BOM

        fputcsv($output, array('ID', 'Username', 'Email', 'Role', 'Department', 'Location', 'Created Date'));

        foreach ($users as $user) {
            fputcsv($output, array(
                $user->user_id,
                $user->username,
                $user->user_email,
                $user->role_name,
                $user->department_name ?: 'Not Assigned',
                $user->location_name ?: 'Not Assigned',
                date('d-m-Y', strtotime($user->created_date))
            ));
        }

        fclose($output);
        exit;
    }

    /**
     * Process CSV import
     */
    public function import_csv()
    {
        $this->load->library('upload');

        $config['upload_path'] = './uploads/temp/';
        $config['allowed_types'] = 'csv|xls|xlsx';
        $config['max_size'] = 5120;
        $config['encrypt_name'] = TRUE;

        if (!is_dir($config['upload_path'])) {
            mkdir($config['upload_path'], 0777, TRUE);
        }

        $this->upload->initialize($config);

        if (!$this->upload->do_upload('csv_file')) {
            $this->session->set_flashdata('ERRORMSG', 'Upload failed: ' . $this->upload->display_errors());
            redirect('UserController/import_form');
        }

        $upload_data = $this->upload->data();
        $file_path = $upload_data['full_path'];

        $result = $this->process_csv_file($file_path);

        unlink($file_path);

        if ($result['success']) {
            $this->session->set_flashdata(
                'SUCCESSMSG',
                'Import completed: ' .
                    $result['inserted'] . ' inserted, ' .
                    $result['updated'] . ' updated, ' .
                    $result['skipped'] . ' skipped'
            );
        } else {
            $this->session->set_flashdata('ERRORMSG', 'Import failed: ' . $result['message']);
        }

        redirect('UserController/index');
    }

    /**
     * Process CSV file
     */
    private function process_csv_file($file_path)
    {
        $result = [
            'success' => false,
            'message' => '',
            'inserted' => 0,
            'updated' => 0,
            'skipped' => 0
        ];

        try {
            $file = fopen($file_path, 'r');
            if (!$file) {
                throw new Exception('Cannot open file');
            }

            // Skip header row
            $header = fgetcsv($file);

            while (($row = fgetcsv($file)) !== FALSE) {
                if (empty($row) || (count($row) == 1 && empty($row[0]))) {
                    continue;
                }

                $import_result = $this->import_user_row($row);

                if ($import_result === 'inserted') {
                    $result['inserted']++;
                } elseif ($import_result === 'updated') {
                    $result['updated']++;
                } else {
                    $result['skipped']++;
                }
            }

            fclose($file);

            $result['success'] = true;
            $result['message'] = 'File processed successfully';
        } catch (Exception $e) {
            $result['message'] = $e->getMessage();
        }

        return $result;
    }

    /**
     * Import a single user row
     */
    private function import_user_row($row)
    {
        $user_id = isset($row[0]) ? trim($row[0]) : '';
        $username = isset($row[1]) ? trim($row[1]) : '';
        $email = isset($row[2]) ? trim($row[2]) : '';
        $role_name = isset($row[3]) ? trim($row[3]) : '';
        $department_name = isset($row[4]) ? trim($row[4]) : '';
        $location_name = isset($row[5]) ? trim($row[5]) : '';
        $created_date = isset($row[6]) ? trim($row[6]) : '';

        // Validate required fields
        if (empty($username) || empty($email) || empty($role_name)) {
            return 'skipped';
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return 'skipped';
        }

        // Get role ID
        $role_id = $this->get_role_id_by_name($role_name);
        if (!$role_id) {
            return 'skipped';
        }

        // Get department ID
        $department_id = null;
        if (!empty($department_name) && $department_name != 'Not Assigned') {
            $department_id = $this->get_department_id_by_name($department_name);
        }

        // Get location ID
        $location_id = null;
        if (!empty($location_name) && $location_name != 'Not Assigned') {
            $location_id = $this->get_location_id_by_name($location_name);
        }

        // Parse date
        $date = $this->parse_date_string($created_date);
        if (!$date) {
            $date = date('Y-m-d');
        }

        // Check if user exists
        $existing_user = false;
        if (!empty($user_id) && is_numeric($user_id)) {
            $existing_user = $this->user->get_user_by_id($user_id);
        }

        if (!$existing_user) {
            $existing_user = $this->user->get_user_by_email($email);
        }

        // Prepare user data
        $user_data = [
            'username' => $username,
            'user_email' => $email,
            'role' => $role_id,
            'department_id_fk' => $department_id,
            'location_id' => $location_id,
            'created_date' => $date
        ];

        if ($existing_user) {
            // Update user
            $this->user->edit_user($user_data, $existing_user['user_id']);

            // Update user role
            $user_role_data = array(
                'user_id' => $existing_user['user_id'],
                'role_name' => $role_name,
                'department_id' => $department_id,
                'location_id' => $location_id,
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s')
            );
            $this->user->add_or_update_user_role($existing_user['user_id'], $role_name, $user_role_data);

            return 'updated';
        } else {
            // Insert new user
            $user_data['password'] = password_hash('password123', PASSWORD_DEFAULT);
            $this->user->add_user($user_data);
            $new_user_id = $this->db->insert_id();

            // Add user role
            $user_role_data = array(
                'user_id' => $new_user_id,
                'role_name' => $role_name,
                'department_id' => $department_id,
                'location_id' => $location_id,
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s')
            );
            $this->user->add_user_role($user_role_data);

            return 'inserted';
        }
    }

    /**
     * Download import template
     */
    public function download_template()
    {
        $template = "ID,Username,Email,Role,Department,Location,Created Date\n";
        $template .= ",John Doe,john@example.com,Admin,IT Department,Mumbai," . date('Y-m-d') . "\n";
        $template .= ",Jane Smith,jane@example.com,User,Sales Department,Pune," . date('Y-m-d') . "\n";
        $template .= ",,user@example.com,Manager,Accounting,Dahej," . date('Y-m-d') . "\n";

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="user_import_template.csv"');
        echo $template;
        exit;
    }

    /**
     * Helper: Get role ID by name
     */
    private function get_role_id_by_name($role_name)
    {
        $this->db->select('role_id');
        $this->db->from('role');
        $this->db->where('role_name', $role_name);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->row()->role_id;
        }

        return false;
    }

    /**
     * Helper: Get department ID by name
     */
    private function get_department_id_by_name($department_name)
    {
        $this->db->select('department_id');
        $this->db->from('department_master');
        $this->db->where('department_name', $department_name);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->row()->department_id;
        }

        return null;
    }

    /**
     * Helper: Get location ID by name
     */
    private function get_location_id_by_name($location_name)
    {
        $this->db->select('location_id');
        $this->db->from('location_master');
        $this->db->where('location_name', $location_name);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->row()->location_id;
        }

        return null;
    }

    /**
     * Helper: Parse date string
     */
    private function parse_date_string($date_string)
    {
        if (empty($date_string)) {
            return false;
        }

        $timestamp = strtotime($date_string);
        if ($timestamp === false) {
            return false;
        }

        return date('Y-m-d', $timestamp);
    }
}
