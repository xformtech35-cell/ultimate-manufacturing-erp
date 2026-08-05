<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

Class Roll extends CI_Model {

    public function add_finish_goods($data_finish_goods) {
        return $this->db->insert('raw_mat_roll_stock', $data_finish_goods);
    }

    public function add_user($data) {
        return $this->db->insert('user', $data);
    }

    public function get_finish_goods_by_id($id) {
        $this->db->select('*');
        $this->db->from('raw_mat_roll_stock');
        $this->db->where('id', $id);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function delete_finish_stock_by_id($id) {
        $this->db->where('id', $id);
        $this->db->delete('raw_mat_roll_stock');
        if ($this->db->affected_rows() == '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function edit_finish_goods($data_finish_goods, $id, $uid) {
        $this->db->where('id', $id);
        //$this->db->where('uid', $uid);
        $this->db->update('raw_mat_roll_stock', $data_finish_goods);
        if ($this->db->affected_rows() == '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function get_finish_goods_stock($uid) {
        $this->db->select('*');
        $this->db->from('raw_mat_roll_stock');
        //$this->db->where('uid', $uid);
        $this->db->order_by("id", "desc");
        $query = $this->db->get();
        return $query->result();
    }

    public function get_product_name($keyword) {
        $this->db->distinct();
        $this->db->select("code");
        $this->db->like('code', $keyword, 'after');
        $query = $this->db->get('inventory');
        return $query->result();
    }

    public function get_product_part_name($uid) {
        $this->db->distinct();
        $this->db->select("code");
        //$this->db->where('uid', $uid);
//        $this->db->like('code', $keyword, 'after');
        $query = $this->db->get('inventory');
        return $query->result();
    }

    public function get_stock_by_item_name($item_name, $uid) {
        $this->db->select('stock');
        $this->db->from('inventory');
        //$this->db->where('uid', $uid);
        $this->db->where('code', $item_name);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function get_last_inventory_number($uid) {
        $this->db->select('COUNT(uid)');
        $this->db->from('inventory');
        //$this->db->where('uid', $uid);
        $this->db->order_by("inventory_id", "DESC");
        $query = $this->db->get();
        $result = $query->row_array();
        return $result;
    }

    public function get_code_list($uid) {
        $this->db->select('*');
        $this->db->from('inventory');
        //$this->db->where('uid', $uid);
        $this->db->order_by("code", "asc");
        $query = $this->db->get();
        return $query->result();
    }

    public function get_inventory_stock_count($product_name, $uid) {
        $this->db->select('stock');
        $this->db->from('inventory');
        //$this->db->where('uid', $uid);
        $this->db->where('code', $product_name);
        $query = $this->db->get();
        return $query->row_array();
    }
    
    public function get_codes($uid) {
        $this->db->select('*');
        $this->db->from('raw_mat_roll_stock');
        //$this->db->where('uid', $uid);
        $this->db->order_by("id", "desc");
        $query = $this->db->get();
        return $query->result();
    }
    public function get_finish_stock_count($code, $id, $uid) {
        $this->db->select('bags_created');
        $this->db->from('raw_mat_roll_stock');
        //$this->db->where('uid', $uid);
         $this->db->where('id', $id);
        $this->db->where('code', $code);
        $query = $this->db->get();
        return $query->row_array();
    }
    

}
