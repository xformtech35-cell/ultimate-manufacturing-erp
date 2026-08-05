<?php
Class Gst extends CI_Model {

    public function add_gst_class($data_gst) {
        return $this->db->insert('gst_classes', $data_gst);
    }

    public function get_gst_classes($uid) {
        $this->db->select('*');
        $this->db->from('gst_classes');
        //$this->db->where('uid', $uid);
        $query = $this->db->get();
        return $query->result();
    }

    public function gst_check($gst, $uid) {
        $this->db->select('*');
        $this->db->from('gst_classes');
        $this->db->where('gst_class', $gst);
        //$this->db->where('uid', $uid);
//        $this->db->limit(1);
        $query = $this->db->get();
        if ($query->num_rows() == 1) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    public function delete_gst_class_by_id($id) {
        $this->db->where('id', $id);
        $this->db->delete('gst_classes');
        if ($this->db->affected_rows() == '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
}
