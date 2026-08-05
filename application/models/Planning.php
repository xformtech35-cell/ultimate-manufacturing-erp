<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

Class Planning extends CI_Model {

    public function get_delivered_items() {
        $this->db->select('*');
        $this->db->from('raw_items_delivery');
        $this->db->order_by("raw_item_delivery_id", "desc");
        $this->db->group_by("batch");
        $query = $this->db->get();
        return $query->result();
    }

    public function add_delivered_item($data_delivered_item) {
       return $this->db->insert('raw_items_delivery', $data_delivered_item);
    }
   
    public function delete_raw_item_by_id($id) {
        $this->db->where('raw_item_delivery_id', $id);
        $this->db->delete('raw_items_delivery');
        if ($this->db->affected_rows() == '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function get_raw_item_by_id($id) {
        $this->db->select('*');
        $this->db->from('raw_items_delivery');
        $this->db->where('raw_item_delivery_id', $id);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function update_delivered_item($raw_item_delivery_id, $data_delivered_item) {
        $this->db->where('raw_item_delivery_id', $raw_item_delivery_id);
        $this->db->update('raw_items_delivery', $data_delivered_item);
        if ($this->db->affected_rows() == '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    public function get_finished_products() {
        $this->db->select('*');
        $this->db->from('finished_products');
        $this->db->order_by("product_id", "desc");
        $this->db->group_by("batch_fk");
        $query = $this->db->get();
        return $query->result();
    }
    
    public function get_batch_no(){
        $this->db->select('batch');
        $this->db->from('raw_items_delivery');
        $this->db->order_by("raw_item_delivery_id", "desc");
        $this->db->limit(1);
        $query = $this->db->get();
        return $query->row_array();
    }
    
    
    public function get_all_batch_numbers(){
        $this->db->select('batch, raw_item_deliver_date');
        $this->db->from('raw_items_delivery');
        $this->db->where('batch_status', 0);
        $this->db->order_by("raw_item_delivery_id", "desc");
        $this->db->group_by('batch');
        $query = $this->db->get();
        return $query->result();
    }
    
    public function get_all_batch_numbers_edit(){
        $this->db->select('batch, raw_item_deliver_date');
        $this->db->from('raw_items_delivery');
        $this->db->order_by("raw_item_delivery_id", "desc");
        $this->db->group_by('batch');
        $query = $this->db->get();
        return $query->result();
    }
    
    public function add_finished_product($data_finished_product) {
       return $this->db->insert('finished_products', $data_finished_product);
    }
    
    public function get_product_by_id($id) {
        $this->db->select('*');
        $this->db->from('finished_products');
        $this->db->where('product_id', $id);
        $query = $this->db->get();
        return $query->row_array();
    }
    
    public function update_finished_product($product_id,$data_finished_product) {
        $this->db->where('product_id', $product_id);
        $this->db->update('finished_products', $data_finished_product);
        if ($this->db->affected_rows() == '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    public function delete_product_by_id($id) {
        $this->db->where('product_id', $id);
        $this->db->delete('finished_products');
        if ($this->db->affected_rows() == '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    public function update_batch_status($batch_fk,$data_batch){
        $this->db->where('batch', $batch_fk);
        $this->db->update('raw_items_delivery', $data_batch);
        if ($this->db->affected_rows() == '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    public function get_inventory_id_by_code($product_name) {
        $this->db->select('inventory_id, stock');
        $this->db->from('inventory');
        $this->db->where('code', $product_name);
        $query = $this->db->get();
        return $query->row_array();
    }
    
    public function get_row_stock_item_count($raw_item_name) {
        $this->db->select('raw_item_stock');
        $this->db->from('raw_items_stock');
        $this->db->where('raw_item_id_fk', $raw_item_name);
        $query = $this->db->get();
        return $query->row_array();
    }
     public function get_row_stock_item_id($raw_item_name) {
        $this->db->select('*');
        $this->db->from('raw_items_master');
        $this->db->where('raw_item_master_name', $raw_item_name);
        $query = $this->db->get();
        return $query->row_array();
    }
    public function update_row_item_stock($raw_item_id, $data_stock_item) {
        $this->db->where('raw_item_id_fk', $raw_item_id);
        $this->db->update('raw_items_stock', $data_stock_item);
        if ($this->db->affected_rows() == '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    public function get_row_item_name() {
        $this->db->distinct('raw_item_name');
        $this->db->select('raw_item_name');
        $this->db->from('raw_items_delivery');
        $query = $this->db->get();
        return $query->result();
    }
    
    public function get_total_row_item_delivered($from_date, $to_date, $raw_item_name) {
        $this->db->select_sum('raw_item_qty');
        $this->db->select('raw_item_name');
        $this->db->from('raw_items_delivery');
        $this->db->where('raw_item_deliver_date >=', $from_date);
        $this->db->where('raw_item_deliver_date <=', $to_date);
        $this->db->where('raw_item_name', $raw_item_name);
        $this->db->group_by('raw_items_delivery.raw_item_name');
        $query = $this->db->get();
        return $query->result();
    }
    
    public function get_row_item_batchwise($batch_no) {
        $this->db->select('*');
        $this->db->from('raw_items_delivery');
        $this->db->where('batch', $batch_no);
        $query = $this->db->get();
        return $query->result();
    }
    
    public function get_product_by_batch_wise($batch_fk) {
        $this->db->select('*');
        $this->db->from('finished_products');
        $this->db->where('batch_fk', $batch_fk);
        $query = $this->db->get();
        return $query->result();
    }
}
