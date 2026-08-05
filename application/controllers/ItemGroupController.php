<?php
defined('BASEPATH') or exit('No direct script access allowed');

class ItemGroupController extends MY_Controller
{
    protected $user_id;

    function __construct()
    {
        parent::__construct();

        $this->load->library('session');
        $this->load->model('ItemGroup', '', TRUE);

        $session_data_head = $this->session->userdata('session_data_head');
        $this->user_id = $session_data_head['result']['user_id'];

        if ($this->user_id === NULL) {
            $this->session->sess_destroy();
            $this->session->set_flashdata('SUCCESSMSG', "You have been Logged Out !!");
            redirect('LoginController/logout');
        }
    }

    public function index()
    {
        $data['group_result'] = $this->ItemGroup->get_groups($this->user_id);

        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('add_item_group', $data);
    }

    public function add_item_group()
    {
        $group = trim($this->input->post('group_name') ?? '');
        if ($group === '') {
            $this->session->set_flashdata('INFOMSG', "Group name cannot be empty!");
            redirect('ItemGroupController/index');
            return;
        }

        $data_group = array(
            'group_name' => $group,
            'uid' => $this->user_id
        );

        $exists = $this->ItemGroup->group_check($group, $this->user_id);

        if ($exists == FALSE) {
            $this->ItemGroup->add_item_group($data_group);
            $this->session->set_flashdata('SUCCESSMSG', "Group added successfully!");
        } else {
            $this->session->set_flashdata('INFOMSG', "Group already exists!");
        }
        redirect('ItemGroupController/index');
    }

    public function delete_group($id)
    {
        // Safety Check: Check if group is used in inventory to prevent foreign key exception
        $in_use = $this->db->where('group_id', $id)->count_all_results('inventory');
        if ($in_use > 0) {
            $this->session->set_flashdata('INFOMSG', "Cannot delete group! It is currently assigned to items in the inventory.");
            redirect('ItemGroupController/index');
            return;
        }

        $result = $this->ItemGroup->delete_group($id);

        if ($result) {
            $this->session->set_flashdata('SUCCESSMSG', "Group deleted successfully!");
        } else {
            $this->session->set_flashdata('INFOMSG', "Group not deleted!");
        }
        redirect('ItemGroupController/index');
    }

    public function get_groups()
    {
        $group_result = $this->ItemGroup->get_groups($this->user_id);
        echo json_encode($group_result);
    }
}
