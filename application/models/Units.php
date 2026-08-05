<?php
Class Units extends CI_Model {

     public function add_unit($data_gst) {
        return $this->db->insert('units', $data_gst);
    }

    public function get_units($uid) {
        $this->db->select('*');
        $this->db->from('units');
        //$this->db->where('uid', $uid);
        $query = $this->db->get();
        return $query->result();
    }

    public function unit_check($unit, $uid) {
        $this->db->select('*');
        $this->db->from('units');
        $this->db->where('unit', $unit);
        //$this->db->where('uid', $uid);
//        $this->db->limit(1);
        $query = $this->db->get();
        if ($query->num_rows() == 1) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    public function delete_unit_by_id($id) {
        $this->db->where('unit_id', $id);
        $this->db->delete('units');
        if ($this->db->affected_rows() == '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }


    public function get_unit_name($uid) {
        $this->db->select('unit');
        $this->db->from('units');
        //$this->db->where('uid', $uid);
        $query = $this->db->get();
        return $query->result();
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
}
