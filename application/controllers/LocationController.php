<?php
defined('BASEPATH') or exit('No direct script access allowed');

class LocationController extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->model('LocationModel');

        $session_data_head = $this->session->userdata('session_data_head');
        if (empty($session_data_head['result']['user_id'])) {
            $this->session->sess_destroy();
            $this->session->set_flashdata('SUCCESSMSG', "You have been Logged Out !!");
            redirect('LoginController/logout');
        }
    }

    public function index()
    {
        $data['location_result'] = $this->LocationModel->get_locations();

        $this->load->view('admin/header_side_bar');
        $this->load->view('location/add_location', $data);
        $this->load->view('admin/footer');
    }

    public function add_location()
    {
        $location_name = $this->input->post('location_name');
        $address = $this->input->post('address');
        $city = $this->input->post('city');
        $state = $this->input->post('state');
        $country = $this->input->post('country');
        $zip_code = $this->input->post('zip_code');
        $phone = $this->input->post('phone');
        $email = $this->input->post('email');

        $data = array(
            'location_name' => $location_name,
            'address' => $address,
            'city' => $city,
            'state' => $state,
            'country' => $country,
            'zip_code' => $zip_code,
            'phone' => $phone,
            'email' => $email,
            'created_date' => date('Y-m-d H:i:s')
        );

        if (!$this->LocationModel->location_exists($location_name)) {
            $this->LocationModel->add_location($data);
            $this->session->set_flashdata('SUCCESSMSG', "Location added successfully!");
        } else {
            $this->session->set_flashdata('INFOMSG', "Location already exists!");
        }
        redirect('LocationController/index');
    }

    public function edit_location()
    {
        $id = $this->input->get('location_id');
        $data['location'] = $this->LocationModel->get_location_by_id($id);
        $data['location_result'] = $this->LocationModel->get_locations();

        $this->load->view('admin/header_side_bar');
        $this->load->view('location/edit_location', $data);
        $this->load->view('admin/footer');
    }

    public function update_location()
    {
        $id = $this->input->post('location_id');
        $location_name = $this->input->post('location_name');
        $address = $this->input->post('address');
        $city = $this->input->post('city');
        $state = $this->input->post('state');
        $country = $this->input->post('country');
        $zip_code = $this->input->post('zip_code');
        $phone = $this->input->post('phone');
        $email = $this->input->post('email');

        $data = array(
            'location_name' => $location_name,
            'address' => $address,
            'city' => $city,
            'state' => $state,
            'country' => $country,
            'zip_code' => $zip_code,
            'phone' => $phone,
            'email' => $email,
            'updated_date' => date('Y-m-d H:i:s')
        );

        if (!$this->LocationModel->location_exists($location_name, $id)) {
            $this->LocationModel->update_location($id, $data);
            $this->session->set_flashdata('SUCCESSMSG', "Location updated successfully!");
        } else {
            $this->session->set_flashdata('INFOMSG', "Location name already exists!");
        }
        redirect('LocationController/index');
    }

    public function delete_location_by_id()
    {
        $id = $this->input->get('location_id');

        if ($this->LocationModel->delete_location_by_id($id)) {
            $this->session->set_flashdata('SUCCESSMSG', "Location deleted successfully!");
        } else {
            $this->session->set_flashdata('INFOMSG', "Location deletion failed!");
        }
        redirect('LocationController/index');
    }
}
