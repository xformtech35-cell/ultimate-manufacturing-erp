<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

Class Email extends CI_Model {

    public function add_email_settings($email_setting_id, $data_settings, $uid) {
        //$this->db->where('uid', $uid);
        $this->db->where('email_setting_id', $email_setting_id);
        $this->db->update('email_setting', $data_settings);
        if ($this->db->affected_rows() == '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function get_email_settings($uid) {
        $this->db->select('*');
        $this->db->from('email_setting');
        //$this->db->where('uid', $uid);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function add_new_user_email_settings($data_settings) {
        return $this->db->insert('email_setting', $data_settings);
    }

}
