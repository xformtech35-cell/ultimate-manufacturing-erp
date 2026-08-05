<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Department extends CI_Model
{
    protected $table = 'department_master';



    function __construct()
    {
        parent::__construct();
        //load our second db and put in $db2
        // $this->crm = $this->load->database('crm', TRUE);

        $this->load->database(); // ✅ Add this line

    }


    // Get all departments
    public function get_departments()
    {
        $query = $this->db->get($this->table);
        return $query->result();
    }

    // Add a new department
    public function add_department($data)
    {
        return $this->db->insert($this->table, $data);
    }

    // Check if a department exists by name
    public function department_exists($department_name, $exclude_id = null)
    {
        $this->db->where('department_name', $department_name);
        if ($exclude_id) {
            $this->db->where('department_id !=', $exclude_id);
        }
        $query = $this->db->get($this->table);
        return ($query->num_rows() > 0);
    }

    // Get a single department by ID
    public function get_department_by_id($id)
    {
        $this->db->where('department_id', $id);
        $query = $this->db->get($this->table);
        return $query->row();
    }

    // Update a department
    public function update_department($id, $data)
    {
        $this->db->where('department_id', $id);
        $this->db->update($this->table, $data);
        return ($this->db->affected_rows() >= 0);
    }

    // Delete a department by ID
    public function delete_department_by_id($id)
    {
        $this->db->where('department_id', $id);
        $this->db->delete($this->table);
        return ($this->db->affected_rows() == 1);
    }
}
