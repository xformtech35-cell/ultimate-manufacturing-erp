<?php
Class Moc extends CI_Model {

     public function add_moc($data_gst) {
        return $this->db->insert('moc', $data_gst);
    }

    public function get_moc($uid) {
        $this->db->select('*');
        $this->db->from('moc');
        //$this->db->where('uid', $uid);
        $query = $this->db->get();
        return $query->result();
    }

    public function moc_check($moc, $uid) {
        $this->db->select('*');
        $this->db->from('moc');
        $this->db->where('moc', $moc);
        //$this->db->where('uid', $uid);
//        $this->db->limit(1);
        $query = $this->db->get();
        if ($query->num_rows() == 1) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    public function delete_moc_by_id($id) {
        $this->db->where('moc_id', $id);
        $this->db->delete('moc');
        if ($this->db->affected_rows() == '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
}
