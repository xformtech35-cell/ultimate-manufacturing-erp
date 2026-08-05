<?php
Class Liabilities extends CI_Model {

     public function add_liabilities($data_gst) {
        return $this->db->insert('liabilities', $data_gst);
    }

    
     public function add_subliabilities($data_gst) {
        return $this->db->insert('subliabilities', $data_gst);
    }
    
    public function get_liabilities($uid) {
        $this->db->select('*');
        $this->db->from('liabilities');
         
        //$this->db->where('uid', $uid);
        $query = $this->db->get();
        return $query->result();
    }
    
    
     public function get_subliabilities($uid) {
        $this->db->select('*,liabilities.liabilities');
        $this->db->from('subliabilities');
        $this->db->join('liabilities', 'subliabilities.liabilities_id=liabilities.liabilities_id');
        //$this->db->where('uid', $uid);
        $query = $this->db->get();
        return $query->result();
    }

      
     public function get_subliabilities1($uid) {
        $this->db->select('*,liabilities.liabilities');
        $this->db->from('subliabilities');
        $this->db->join('liabilities', 'subliabilities.liabilities_id=liabilities.liabilities_id');
      
        $query = $this->db->get();
        return $query->result();
    }
    
    
    public function liabilities_check($liabilities, $uid) {
        $this->db->select('*');
        $this->db->from('liabilities');
        $this->db->where('liabilities', $liabilities);
        //$this->db->where('uid', $uid);
//        $this->db->limit(1);
        $query = $this->db->get();
        if ($query->num_rows() == 1) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    public function delete_liabilities_by_id($id) {
        $this->db->where('liabilities_id', $id);
        $this->db->delete('liabilities');
        if ($this->db->affected_rows() == '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
       public function delete_subliabilities_by_id($id) {
        $this->db->where('liabilities_id', $id);
        $this->db->delete('subliabilities');
        if ($this->db->affected_rows() == '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    
     
     public function get_liabilities_name($uid) {
        $this->db->select('*');
        $this->db->from('liabilities');
        
        //  $this->db->where('uid', $uid);
        $this->db->order_by("liabilities", "asc");
        $query = $this->db->get();
        return $query->result();
    }
    
     
    
    
    
    
     public function get_liabilities_id($liabilities_id, $uid) {
         
       $this->db->select('*');
        $this->db->from('subliabilities');
       $this->db->where('liabilities_id',$liabilities_id);
        $query = $this->db->get();
      
        return $query->result();
    } 
    
    

    
    
    
    
    
}
