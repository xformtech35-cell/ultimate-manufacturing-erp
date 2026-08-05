<?php
class ItemGroup extends CI_Model
{
    public function add_item_group($data)
    {
        return $this->db->insert('item_group_master', $data);
    }

    public function get_groups($uid = null)
    {
        $this->db->select('*');
        $this->db->from('item_group_master');
        // Group is company-wide master data — no uid filter
        $query = $this->db->get();
        return $query->result();
    }

    public function group_check($group_name, $uid)
    {
        $this->db->select('*');
        $this->db->from('item_group_master');
        $this->db->where('group_name', $group_name);
        $this->db->where('uid', $uid);

        $query = $this->db->get();
        return $query->num_rows() > 0;
    }

    public function delete_group($id)
    {
        $this->db->where('group_id', $id);
        $this->db->delete('item_group_master');

        return ($this->db->affected_rows() == 1);
    }
}
