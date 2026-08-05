<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class RoleController extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this->load->model('role', '', TRUE);
        $this->load->library('form_validation');
        $this->load->library('image_lib');
        if (!$this->session->userdata('session_data_head')) {
            redirect('Welcome/index');
        }
    }

    public function index() {
        $session_data_head = $this->session->userdata('session_data_head');
        $data['role'] = $this->role->get_role();
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('role/role', $data);
    }

    public function show_group() {
        $session_data_head = $this->session->userdata('session_data_head');
        $data['role'] = $this->role->get_role();
        $data['sidebar_menu'] = $this->db->order_by('sort_order', 'ASC')->get('sidebar_menu')->result_array();
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('role/groups', $data);
    }

    public function save_role() {
        $role_name = $this->input->post('role_name');
        $data = array(
            'role_name' => $role_name,
        );
        if ($this->role->check_role($role_name)) {
            $this->session->set_flashdata('INFOMSG', "Role Already Added");
            redirect('RoleController/index');
        } else {
            $result = $this->role->save_role($data);
            if ($result == TRUE) {
                $this->session->set_flashdata('SUCCESSMSG', "Role Added Successfully!!");
                redirect('RoleController/index');
            } else {
                $this->session->set_flashdata('INFOMSG', "Role Not Added Successfully!!");
                redirect('RoleController/index');
            }
        }
    }

    public function delete_role() {
        $id = $this->uri->segment(3);
        $result = $this->role->delete_role($id);
        if ($result == TRUE) {
            $this->session->set_flashdata('SUCCESSMSG', "Role Deleted Successfully.");
            redirect('RoleController/index');
        } else {
            $this->session->set_flashdata('INFOMSG', "Role Not Deleted Successfully.Please try again!!");
            redirect('RoleController/index');
        }
    }

    public function get_role_id() {
        $roleid = $this->input->post('roleid');
        $data = $this->role->get_role_id($roleid);
        echo json_encode($data);
    }

    public function edit_role() {
        $role_id = $this->input->post('role_id');
        $role_name = $this->input->post('role_name');
        $data = array(
            'role_name' => $role_name,
        );
        $result = $this->role->edit_role($role_id, $data);
        if ($result == TRUE) {
            $this->session->set_flashdata('SUCCESSMSG', "Role Updated Successfully!!");
            redirect('RoleController/index');
        } else {
            $this->session->set_flashdata('INFOMSG', "Role Not Updated Successfully!!");
            redirect('RoleController/index');
        }
    }

    public function permission_save() {
        $role_id = $this->input->post('role');
        $grp_perm = $this->input->post('grp_perm');
        if (!is_array($grp_perm)) {
            $grp_perm = array();
        }
        $grp_perm = array_unique($grp_perm);



// var_dump($grp_perm);
//         die();

        $result = $this->role->check_role_groups($role_id);
        if ($result == FALSE) {
            $data_permission = array();
            foreach ($grp_perm as $perm) {
                $data_permission[] = array(
                    'role_id_fk' => $role_id,
                    'grp_perm' => $perm,
                );
            }
            $result1 = TRUE;
            foreach ($data_permission as $data) {
                $result1 = $this->role->add_permission($data);
            }
            if ($result1 == TRUE) {
                 $this->session->set_flashdata('SUCCESSMSG', "Permission Added Successfully!!");
            } else {
                 $this->session->set_flashdata('INFOMSG', "Permission Not Added Successfully!!");
            }
        } else {
            $resultd = $this->role->delete_permission($role_id);
            if ($resultd == TRUE) {
                $data_permission = array();
                foreach ($grp_perm as $perm) {
                    $data_permission[] = array(
                        'role_id_fk' => $role_id,
                        'grp_perm' => $perm,
                    );
                }
                $result1 = TRUE;
                foreach ($data_permission as $database_data) {
                    $result1 = $this->role->add_permission($database_data);
                }
                if ($result1 == TRUE) {
                  $this->session->set_flashdata('SUCCESSMSG', "Permission Updated Successfully!!");
                } else {
                     $this->session->set_flashdata('INFOMSG', "Permission Not Updated Successfully!!");
                }
            }
        }

        // Refresh session if the current user's role matches the saved role
        $session_data_head = $this->session->userdata('session_data_head');
        if (isset($session_data_head['result']['role']) && $session_data_head['result']['role'] == $role_id) {
            $perm_result = $this->role->get_groups_by_role_id_fk($role_id);
            $permission = array();
            foreach ($perm_result as $key) {
                array_push($permission, $key->grp_perm);
            }
            $session_data_head['permission'] = $permission;
            $this->session->set_userdata(array('session_data_head' => $session_data_head));
        }

        redirect('RoleController/show_group', 'refresh');
    }

    public function get_groups_by_role_id_fk() {
        $role_id_fk=$this->input->post('role_id_fk');
        $data=$this->role->get_groups_by_role_id_fk($role_id_fk);
        echo json_encode($data);
    }
}
