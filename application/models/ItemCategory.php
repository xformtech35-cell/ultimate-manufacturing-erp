<?php
class ItemCategory extends CI_Model
{
    public function add_item_category($data)
    {
        return $this->db->insert('item_category_master', $data);
    }

    public function get_categories($uid = null)
    {
        $this->db->select('*');
        $this->db->from('item_category_master');
        // Category is company-wide master data — no uid filter
        $query = $this->db->get();
        return $query->result();
    }

    public function category_check($category_name, $uid)
    {
        $this->db->select('*');
        $this->db->from('item_category_master');
        $this->db->where('category_name', $category_name);
        $this->db->where('uid', $uid);

        $query = $this->db->get();
        return $query->num_rows() > 0;
    }

    public function update_category($id, $data)
    {
        $this->db->where('category_id', $id);
        return $this->db->update('item_category_master', $data);
    }

    public function delete_category($id)
    {
        $this->db->where('category_id', $id);
        $this->db->delete('item_category_master');

        return ($this->db->affected_rows() == 1);
    }
}
