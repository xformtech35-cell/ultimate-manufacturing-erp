<?php

Class Role extends CI_Model {

    public function get_role() {
        $this->db->select('*');
        $this->db->from('role');
        $this->db->order_by("role_id", "desc");
        $query = $this->db->get();
        return $query->result();
    }

    public function save_role($data) {
        return $this->db->insert('role', $data);
    }

    public function get_role_id($id) {
        $this->db->select('*');
        $this->db->from('role');
        $this->db->where('role_id', $id);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function edit_role($id, $data) {
        $this->db->where('role_id', $id);
        $this->db->update('role', $data);
        if ($this->db->affected_rows() == 1) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function delete_role($id) {
        $this->db->where('role_id', $id);
        $this->db->delete('role');
        if ($this->db->affected_rows() == '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function check_role($role_name) {
        $this->db->where('role_name', $role_name);
        $this->db->from('role');
        $query = $this->db->get();
        return $query->result();
    }
    
     public function check_role_groups($role_id) {
        $this->db->where('role_id_fk', $role_id);
        $this->db->from('permission');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
     public function add_permission($database_data) {



    //  var_dump($database_data);
    //  die();
        return $this->db->insert('permission', $database_data);
    }

    public function edit_permission($role_id, $database_data) {
        $this->db->where('role_id_fk', $role_id);
        $this->db->update('permission', $database_data);
        if ($this->db->affected_rows() >= '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
     public function delete_permission($role_id) {
        $this->db->where('role_id_fk', $role_id);
        $this->db->delete('permission');
        if ($this->db->affected_rows() >= '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    public function get_groups_by_role_id_fk($role_id_fk) {
        $this->db->select('grp_perm');
        $this->db->from('permission');
        $this->db->where("role_id_fk",$role_id_fk);
        $query = $this->db->get();
        return $query->result();
    }

    /**
     * Get role by ID
     */
    public function get_role_by_id($role_id)
    {
        $this->db->where('role_id', $role_id);
        $query = $this->db->get('role');
        return $query->row();
    }

    /**
     * Get role by name
     */
    public function get_role_by_name($role_name)
    {
        $this->db->where('role_name', $role_name);
        $query = $this->db->get('role');
        return $query->row();
    }
    
}
    