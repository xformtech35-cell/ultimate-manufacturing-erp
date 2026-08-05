<?php
Class Assetbalancesheet extends CI_Model {

     public function add_asset($data_gst) {
        return $this->db->insert('asset', $data_gst);
    }
 public function add_subasset($data_gst) {
    
        // print_r($data_gst); die();
        return $this->db->insert('subasset', $data_gst);
    }
    
    
    
    public function get_asset($uid) {
        $this->db->select('*');
        $this->db->from('asset');
        //$this->db->where('uid', $uid);
        $query = $this->db->get();
        return $query->result();
    }

     public function get_subasset($uid) {
        $this->db->select('*,asset.asset');
        $this->db->from('subasset');
        
           $this->db->join('asset', 'subasset.asset_id=asset.asset_id');
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
    
       public function delete_subasset_by_id($id) {
        $this->db->where('subasset_id', $id);
        $this->db->delete('subasset');
        if ($this->db->affected_rows() == '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    } 
    
    
    
     public function get_asset_name($uid) {
        $this->db->select('*');
        $this->db->from('asset');
        //  $this->db->where('uid', $uid);
        $this->db->order_by("asset", "asc");
        $query = $this->db->get();
        return $query->result();
    }
    
     public function get_subasset_name($uid) {
        $this->db->select('*');
        $this->db->from('subasset');
        //  $this->db->where('uid', $uid);
        $this->db->order_by("subasset_name", "asc");
        $query = $this->db->get();
        return $query->result();
    }
    
    
    public function get_subasset_id($asset_id, $uid) {
       $this->db->select('*');
        $this->db->from('subasset');
       $this->db->where('asset_id',$asset_id);
        $query = $this->db->get();
        return $query->result();
    } 
    
    
    
    
    
    
    
    
    
}
