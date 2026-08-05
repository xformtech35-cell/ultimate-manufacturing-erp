<?php
defined('BASEPATH') or exit('No direct script access allowed');

class ItemCategoryController extends MY_Controller
{
    protected $user_id;

    function __construct()
    {
        parent::__construct();

        $this->load->library('session');
        $this->load->model('ItemCategory', '', TRUE);

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
        $data['category_result'] = $this->ItemCategory->get_categories($this->user_id);

        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('add_item_category', $data);
    }

    public function add_item_category()
    {
        $cat = trim($this->input->post('category_name') ?? '');
        if ($cat === '') {
            $this->session->set_flashdata('INFOMSG', "Category name cannot be empty!");
            redirect('ItemCategoryController/index');
            return;
        }
        $data_cat = array('category_name' => $cat, 'uid' => $this->user_id);

        $exists = $this->ItemCategory->category_check($cat, $this->user_id);

        if ($exists == FALSE) {
            $this->ItemCategory->add_item_category($data_cat);
            $this->session->set_flashdata('SUCCESSMSG', "Category added successfully!");
        } else {
            $this->session->set_flashdata('INFOMSG', "Category already exists!");
        }
        redirect('ItemCategoryController/index');
    }

    public function delete_category($id)
    {
        // Safety Check: Check if category is used in inventory to prevent foreign key exception
        $in_use = $this->db->where('category_id', $id)->count_all_results('inventory');
        if ($in_use > 0) {
            $this->session->set_flashdata('INFOMSG', "Cannot delete category! It is currently assigned to items in the inventory.");
            redirect('ItemCategoryController/index');
            return;
        }

        $result = $this->ItemCategory->delete_category($id);

        if ($result) {
            $this->session->set_flashdata('SUCCESSMSG', "Category deleted successfully!");
        } else {
            $this->session->set_flashdata('INFOMSG', "Category not deleted!");
        }
        redirect('ItemCategoryController/index');
    }

    public function get_categories()
    {
        $category_result = $this->ItemCategory->get_categories($this->user_id);
        echo json_encode($category_result);
    }
}
