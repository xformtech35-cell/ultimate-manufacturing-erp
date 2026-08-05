<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

Class Master extends CI_Model {

    public function get_categories() {
        $this->db->select('*');
        $this->db->from('category');
//        $this->db->order_by("category_id", "desc");
        $query = $this->db->get();
        return $query->result();
    }

    public function add_category($data_category){
        return $this->db->insert('category', $data_category);
    }
    
    public function get_category_by_id($id){
        $this->db->select('*');
        $this->db->from('category');
        $this->db->where('category_id', $id);
        $query = $this->db->get();
        return $query->row_array();
    }
    
    public function edit_category($category_id, $data_category){
        $this->db->where('category_id', $category_id);
        $this->db->update('category', $data_category);
        if ($this->db->affected_rows() == '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    public function delete_category_by_id($id){
        $this->db->where('category_id', $id);
        $this->db->delete('category');
        if ($this->db->affected_rows() == '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function get_products(){
        $this->db->select('*');
        $this->db->from('product_master');
        $this->db->join('category','product_master.category_id_fk = category.category_id');
        $query = $this->db->get();
        return $query->result();
    }
    
    public function add_product($data_product){
        return $this->db->insert('product_master', $data_product);
    }

    public function get_product_by_id($id){
        $this->db->select('*');
        $this->db->from('product_master');
        $this->db->where('product_master_id', $id);
        $query = $this->db->get();
        return $query->row_array();
    }
    
    public function edit_product($product_master_id, $data_product){
        $this->db->where('product_master_id', $product_master_id);
        $this->db->update('product_master', $data_product);
        if ($this->db->affected_rows() == '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    public function delete_product_by_id($id){
        $this->db->where('product_master_id', $id);
        $this->db->delete('product_master');
        if ($this->db->affected_rows() == '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    public function get_raw_items(){
        $this->db->select('*');
        $this->db->from('raw_items_master');
        $query = $this->db->get();
        return $query->result();
    }
    
    public function add_raw_item($data_raw_item){
        return $this->db->insert('raw_items_master', $data_raw_item);
    }
    
    public function get_raw_item_by_id($id){
        $this->db->select('*');
        $this->db->from('raw_items_master');
        $this->db->where('raw_item_master_id', $id);
        $query = $this->db->get();
        return $query->row_array();
    }
    
    public function edit_raw_item($raw_items_master_id, $data_raw_item){
        $this->db->where('raw_item_master_id', $raw_items_master_id);
        $this->db->update('raw_items_master', $data_raw_item);
        if ($this->db->affected_rows() == '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    public function delete_raw_item_by_id($id){
        $this->db->where('raw_item_master_id', $id);
        $this->db->delete('raw_items_master');
        if ($this->db->affected_rows() == '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    public function get_existing_stock($raw_item_id){
        $this->db->select('*');
        $this->db->from('vtechAccounting_raw_items_stock');
        $this->db->where('raw_item_id_fk', $raw_item_id);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function raw_item_id_check($id) {
        $this->db->select('raw_item_id_fk');
        $this->db->from('raw_items_stock');
//        $this->db->where('uid', $uid);
        $this->db->where('raw_item_id_fk', $id);
        $this->db->limit(1);
        $query = $this->db->get();
        if ($query->num_rows() == 1) {
            return true;
        } else {
            return false;
        }
    }

    public function get_raw_item_stock(){
        $this->db->select('*');
        $this->db->from('raw_items_stock');
        $this->db->join('raw_items_master', 'raw_items_stock.raw_item_id_fk=raw_items_master.raw_item_master_id');
//        $this->db->where('raw_item_stock_id', $raw_item_id);
        $query = $this->db->get();
        return $query->result();
    }
    
    public function get_purchase_stock($uid,$from_date,$to_date) {
        //echo $from_date;die();
        $this->db->select('*');
        $this->db->from('purchase_stock');
        $this->db->where('purchase_stock.uid', $uid);
        if($from_date && $from_date!='01-01-1970' && $from_date!=''){
            $this->db->where('purchase_stock.purchase_date >=', $from_date);
            $this->db->where('purchase_stock.purchase_date <=', $to_date);
            
        }
        $this->db->join('raw_items_master', 'raw_items_master.raw_item_master_id=purchase_stock.inventory_id_fk');
        $this->db->join('supplier', 'supplier.supplier_id=purchase_stock.supplier_id_fk');
        $this->db->order_by("purchase_stock.purchase_date","desc");
        $query = $this->db->get();
        return $query->result();
    }
}
