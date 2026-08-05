<?php
Class Asset extends CI_Model {

     public function add_asset($data_asset) {
        return $this->db->insert('asset', $data_asset);
    }

    public function get_asset($uid) {
        $this->db->select('*');
        $this->db->from('asset');
        //$this->db->where('uid', $uid);
        $query = $this->db->get();
        return $query->result();
    }

    public function asset_check($asset, $uid) {
        $this->db->select('*');
        $this->db->from('asset');
        $this->db->where('asset', $asset);
        //$this->db->where('uid', $uid);
//        $this->db->limit(1);
        $query = $this->db->get();
        if ($query->num_rows() == 1) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    public function delete_asset_by_id($id) {
        $this->db->where('asset_id', $id);
        $this->db->delete('asset');
        if ($this->db->affected_rows() == '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    
    
    
    
    
    
    
    
    
     public function add_asset_sub_category($data_asset_sub_category) {
        return $this->db->insert('asset_sub_category', $data_asset_sub_category);
    }

    public function get_asset_sub_category($uid) {
        $this->db->select('*');
        $this->db->from('asset_sub_category');
        //$this->db->where('uid', $uid);
        $query = $this->db->get();
        
        
        var_dump($query->result());
        die();
        return $query->result();
    }

    public function asset_sub_category_check($asset_sub_category, $uid) {
        $this->db->select('*');
        $this->db->from('asset_sub_category');
        $this->db->where('asset_sub_category', $asset_sub_category);
        //$this->db->where('uid', $uid);
//        $this->db->limit(1);
        $query = $this->db->get();
        if ($query->num_rows() == 1) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    public function delete_asset_sub_category_by_id($id) {
        $this->db->where('asset_sub_category_id', $id);
        $this->db->delete('asset_sub_category');
        if ($this->db->affected_rows() == '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    
    
    
    
    
    
    
}
