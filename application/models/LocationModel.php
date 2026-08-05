<?php
defined('BASEPATH') or exit('No direct script access allowed');

class LocationModel extends CI_Model
{
    protected $table = 'location_master';

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    // Get all locations
    public function get_locations()
    {
        $this->db->order_by('location_name', 'ASC');
        $query = $this->db->get($this->table);
        return $query->result();
    }

    // Add a new location
    public function add_location($data)
    {
        return $this->db->insert($this->table, $data);
    }

    // Check if a location exists by name
    public function location_exists($location_name, $exclude_id = null)
    {
        $this->db->where('location_name', $location_name);
        if ($exclude_id) {
            $this->db->where('location_id !=', $exclude_id);
        }
        $query = $this->db->get($this->table);
        return ($query->num_rows() > 0);
    }

    // Get a single location by ID
    public function get_location_by_id($id)
    {
        $this->db->where('location_id', $id);
        $query = $this->db->get($this->table);
        return $query->row();
    }

    // Update a location
    public function update_location($id, $data)
    {
        $this->db->where('location_id', $id);
        return $this->db->update($this->table, $data);
    }

    // Delete a location by ID
    public function delete_location_by_id($id)
    {
        // First, set location_id_fk to NULL in purchase_requisition table to avoid foreign key constraint
        $this->db->where('location_id_fk', $id);
        $this->db->update('purchase_requisition', array('location_id_fk' => NULL));

        // Now delete the location
        $this->db->where('location_id', $id);
        $this->db->delete($this->table);
        return ($this->db->affected_rows() == 1);
    }

    // Get location count
    public function get_location_count()
    {
        return $this->db->count_all($this->table);
    }
}
