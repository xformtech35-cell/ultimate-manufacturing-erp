<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Login extends CI_Model
{
    function __construct() {
        parent::__construct();
		 // Disable ONLY_FULL_GROUP_BY for this DB session to avoid
        // "Expression ... is not in GROUP BY clause" errors on older queries.
        $this->db->query('SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode, "ONLY_FULL_GROUP_BY", ""))');
    }
    public function login_user($user_email, $password)
    {
        

        if ($user_email && $password) {

            $this->db->select('*, lm.location_id, lm.location_name, d.department_id, d.department_name');
            $this->db->from('user u');
            $this->db->where('user_email', $user_email);
            $this->db->join('role', 'role.role_id=u.role', 'Left Join');
            $this->db->join('location_master lm', 'lm.location_id = u.location_id', 'left');
            $this->db->join('department_master d', 'd.department_id = u.department_id_fk', 'left');

            $query = $this->db->get();

            if ($query->num_rows() == 1) {
                $result = $query->row_array();
                //print_r($password);die();
                $hash_password = password_verify($password, $result['password']);

                if ($hash_password === true) {
                    // echo "hello";die();
                    return $result;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }
    }


    public function forgot_password($mobile)
    {

        $this->db->select('*');
        $this->db->from('user');
        $this->db->where('user_id', $mobile);
        $this->db->limit(1);
        $query = $this->db->get();

        if ($query->num_rows() == 1) {
            $password = mt_rand(10000, 99999);
            $forgotpassword = hash("sha256", $password);
            $data = array('user_password' => $forgotpassword);
            $this->db->where('user_id', $mobile);
            $this->db->update('user', $data);
            return $query->result();
        } else {
            return false;
        }
    }

    public function change_password($user_id, $password)
    {

        //$oldpassword1 = hash("sha256", $oldpassword);
        $this->db->select('*');
        $this->db->from('user');
        $this->db->where('user_id', $user_id);
        $this->db->limit(1);
        $query = $this->db->get();
        if ($query->num_rows() == 1) {
            //$newpassword = hash("sha256", $password);
            $data = array('password' => $password);
            $this->db->where('user_id', $user_id);
            $this->db->update('user', $data);
            return $query->result();
        } else {
            return false;
        }
    }

    public function email_update_password($to_email, $encrypted_password)
    {

        $this->db->select('*');
        $this->db->from('user');
        $this->db->where('user_email', $to_email);
        $this->db->limit(1);
        $query = $this->db->get();

        if ($query->num_rows() == 1) {
            //$newpassword = hash("sha256", $password);
            $data = array('password' => $encrypted_password);
            $this->db->where('user_email', $to_email);
            $this->db->update('user', $data);
            return $query->result();
        } else {
            return false;
        }
    }



    public function get_user_info_by_email($user_email)
    {
        $this->db->select('*');
        $this->db->from('user');
        $this->db->where('user_email', $user_email);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->row_array();
        } else {
            return false;
        }
    }


    public function mobile_number_check($mobile, $password)
    {

        $password1 = hash("sha256", $password);
        $this->db->select('customer_name, customer_mobile');
        $this->db->from('customer');
        $this->db->where('customer_mobile', $mobile);
        $this->db->where('password', $password1);
        $this->db->limit(1);

        $query = $this->db->get();

        if ($query->num_rows() == 1) {
            return $query->result();
        } else {
            return false;
        }
    }

    public function mobile_number_exist($customer_mobile)
    {
        $this->db->select('customer_mobile');
        $this->db->from('customer');
        $this->db->where('customer_mobile', $customer_mobile);
        $this->db->limit(1);

        $query = $this->db->get();

        if ($query->num_rows() == 1) {
            return $query->result();
        } else {
            return false;
        }
    }

    public function add_settings($setting_id, $data_settings, $uid)
    {
        //$this->db->where('uid', $uid);
        $this->db->where('setting_id', $setting_id);
        $this->db->update('settings', $data_settings);
        if ($this->db->affected_rows() == '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function get_settings($uid)
    {
        $this->db->select('*');
        $this->db->from('settings');
        $query = $this->db->get();
        return $query->row_array();
    }


    public function get_settings_state_code($uid)
    {
        $this->db->select('state_code');
        $this->db->from('settings');
        $this->db->where('uid', '1');
        $query = $this->db->get();
        return $query->row_array();
    }

    public function guest_visible()
    {
        $this->filter_where_in('quotation', array(2, 3, 4));
        return $this;
    }

    public function check_email_address($to_email)
    {
        $this->db->select('user_email');
        $this->db->from('user');
        $this->db->where('user_email', $to_email);
        $this->db->limit(1);
        $query = $this->db->get();
        if ($query->num_rows() == 1) {
            return $query->result();
        } else {
            return false;
        }
    }

    public function add_new_user_settings($data_settings)
    {
        return $this->db->insert('settings', $data_settings);
    }

    public function get_user_permission($role_id)
    {
        $this->db->select('grp_perm');
        $this->db->from('permission');
        $this->db->where('role_id_fk', $role_id);
        $query = $this->db->get();
        return $query->result();
    }

  public function get_logo_settings()
{
    $this->db->select('company_logo');
    $this->db->from('settings');
    $this->db->order_by('setting_id', 'DESC'); // Order by id descending to get the last item
    $this->db->limit(1); // Limit to only 1 record
    $query = $this->db->get();
    return $query->row_array();
}
}
