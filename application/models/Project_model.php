<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Project_model extends CI_Model {
    
    private $primary_key = 'project_id';
    private $table = 'project';  // Just the table name, prefix will be auto-added
    
    public function __construct() {
        parent::__construct();
    }
    
    // 1. Get all rows
    public function get_all_projects() {
        return $this->db->order_by($this->primary_key, 'DESC')
                        ->get($this->table)  // CI auto-adds prefix
                        ->result();
    }

    // 2. Get single row
    public function get_project_by_id($project_id) {
        return $this->db->where($this->primary_key, $project_id)
                        ->get($this->table)  // CI auto-adds prefix
                        ->row();
    }

    // 3. Insert
    public function insert_project($data) {
        return $this->db->insert($this->table, $data);  // CI auto-adds prefix
    }

    // 4. Update
    public function update_project($project_id, $data) {
        return $this->db->where($this->primary_key, $project_id)
                        ->update($this->table, $data);  // CI auto-adds prefix
    }

    // 5. Delete
    public function delete_project($project_id) {
        return $this->db->where($this->primary_key, $project_id)
                        ->delete($this->table);  // CI auto-adds prefix
    }

    // 6. Count
    public function count_projects() {
        return $this->db->count_all_results($this->table);  // CI auto-adds prefix
    }
}