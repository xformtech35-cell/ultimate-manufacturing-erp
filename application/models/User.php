<?php
defined('BASEPATH') or exit('No direct script access allowed');

class User extends CI_Model
{
    // Get all users with department information
    public function get_all_users_with_department()
    {
        $this->db->select('user.*, role.role_name, department_master.department_name');
        $this->db->from('user');
        $this->db->join('role', 'role.role_id = user.role', 'left');
        $this->db->join('department_master', 'department_master.department_id = user.department_id_fk', 'left');
        $this->db->order_by('user.user_id', 'desc');

        $query = $this->db->get();
        return $query->result();
    }

    // Get all users with department and location information
    public function get_all_users_with_department_location()
    {
        $this->db->select('user.*, role.role_name, department_master.department_name, location.location_name');
        $this->db->from('user');
        $this->db->join('role', 'role.role_id = user.role', 'left');
        $this->db->join('department_master', 'department_master.department_id = user.department_id_fk', 'left');
        $this->db->join('location_master as location', 'location.location_id = user.location_id', 'left');
        $this->db->order_by('user.user_id', 'desc');

        $query = $this->db->get();
        return $query->result();
    }

    // Get user by ID with location information
    public function get_user_by_id_with_location($user_id)
    {
        $this->db->select('user.*, role.role_name, department_master.department_name, location.location_name');
        $this->db->from('user');
        $this->db->join('role', 'role.role_id = user.role', 'left');
        $this->db->join('department_master', 'department_master.department_id = user.department_id_fk', 'left');
        $this->db->join('location_master as location', 'location.location_id = user.location_id', 'left');
        $this->db->where('user.user_id', $user_id);
        $query = $this->db->get();
        return $query->row_array();
    }

    // Original method - keep for backward compatibility
    public function get_user($role = null)
    {
        $this->db->select('user.*, role.role_name');
        $this->db->from('user');
        $this->db->join('role', 'role.role_id = user.role', 'left');

        if ($role) {
            $this->db->where('user.role', $role);
        }
        $this->db->order_by('user.user_id', 'desc');

        $query = $this->db->get();
        return $query->result();
    }

    // Add new user
    public function add_user($data)
    {
        return $this->db->insert('user', $data);
    }

    // Get user by ID (without joins)
    public function get_user_by_id($id)
    {
        $this->db->select('*');
        $this->db->from('user');
        $this->db->where('user_id', $id);
        $query = $this->db->get();
        return $query->row_array();
    }

    // Delete user by ID
    public function delete_user_by_id($id)
    {
        $this->db->where('user_id', $id);
        $this->db->delete('user');
        return $this->db->affected_rows() > 0;
    }

    // Edit/Update user
    public function edit_user($data_user, $user_id)
    {
        $this->db->where('user_id', $user_id);
        $this->db->update('user', $data_user);
        return $this->db->affected_rows() > 0;
    }

    // Get user count by role
    public function get_user_count($role)
    {
        $this->db->select('*');
        $this->db->from('user');
        $this->db->where('role', $role);
        $query = $this->db->get();
        return $query->num_rows();
    }

    // Get invoice count user wise (GST)
    public function get_invoice_count_user_wise()
    {
        $this->db->select('COUNT(uid) as invoice_count, SUM(total) as total_amount, username, user_email');
        $this->db->from('invoice_total invt');
        $this->db->join('user', 'user.user_id = invt.uid');
        $this->db->group_by('invt.uid');
        $this->db->order_by("uid", "desc");
        $query = $this->db->get();
        return $query->result();
    }

    // Get invoice count user wise (Non-GST)
    public function get_non_gst_invoice_count_user_wise()
    {
        $this->db->select('COUNT(uid) as invoice_count, SUM(total) as total_amount, username, user_email');
        $this->db->from('non_gst_invoice_total invt');
        $this->db->join('user', 'user.user_id = invt.uid');
        $this->db->group_by('invt.uid');
        $this->db->order_by("uid", "desc");
        $query = $this->db->get();
        return $query->result();
    }

    // Get total invoice count (GST)
    public function get_total_invoice_count()
    {
        $this->db->select('uid');
        $this->db->from('invoice_total');
        $query = $this->db->get();
        return $query->num_rows();
    }

    // Get total invoice count (Non-GST)
    public function get_total_non_gst_invoice_count()
    {
        $this->db->select('uid');
        $this->db->from('non_gst_invoice_total');
        $query = $this->db->get();
        return $query->num_rows();
    }

    // Get users without role filter
    public function get_user_without_role()
    {
        $this->db->select('*');
        $this->db->from('user');
        $this->db->order_by("user_id", "desc");
        $query = $this->db->get();
        return $query->result();
    }

    // Get user by email
    public function get_user_by_email($email)
    {
        $this->db->select('*');
        $this->db->from('user');
        $this->db->where('user_email', $email);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->row_array();
        }

        return false;
    }

    // Get users by location
    public function get_users_by_location($location_id)
    {
        $this->db->select('user.*, role.role_name, department_master.department_name');
        $this->db->from('user');
        $this->db->join('role', 'role.role_id = user.role', 'left');
        $this->db->join('department_master', 'department_master.department_id = user.department_id_fk', 'left');
        $this->db->where('user.location_id', $location_id);
        $this->db->order_by('user.user_id', 'desc');

        $query = $this->db->get();
        return $query->result();
    }

    // Get users by department
    public function get_users_by_department($department_id)
    {
        $this->db->select('user.*, role.role_name, department_master.department_name');
        $this->db->from('user');
        $this->db->join('role', 'role.role_id = user.role', 'left');
        $this->db->join('department_master', 'department_master.department_id = user.department_id_fk', 'left');
        $this->db->where('user.department_id_fk', $department_id);
        $this->db->order_by('user.user_id', 'desc');

        $query = $this->db->get();
        return $query->result();
    }

    // Get users by role
    public function get_users_by_role($role_id)
    {
        $this->db->select('user.*, role.role_name, department_master.department_name');
        $this->db->from('user');
        $this->db->join('role', 'role.role_id = user.role', 'left');
        $this->db->join('department_master', 'department_master.department_id = user.department_id_fk', 'left');
        $this->db->where('user.role', $role_id);
        $this->db->order_by('user.user_id', 'desc');

        $query = $this->db->get();
        return $query->result();
    }

    // Search users by name or email
    public function search_users($search_term)
    {
        $this->db->select('user.*, role.role_name, department_master.department_name');
        $this->db->from('user');
        $this->db->join('role', 'role.role_id = user.role', 'left');
        $this->db->join('department_master', 'department_master.department_id = user.department_id_fk', 'left');
        $this->db->group_start();
        $this->db->like('user.username', $search_term);
        $this->db->or_like('user.user_email', $search_term);
        $this->db->group_end();
        $this->db->order_by('user.user_id', 'desc');

        $query = $this->db->get();
        return $query->result();
    }

    // Check if user exists by email excluding current user
    public function is_email_exists_except($email, $user_id)
    {
        $this->db->select('*');
        $this->db->from('user');
        $this->db->where('user_email', $email);
        $this->db->where('user_id !=', $user_id);
        $query = $this->db->get();

        return $query->num_rows() > 0;
    }

    // Update user password
    public function update_password($user_id, $new_password)
    {
        $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
        $data = array('password' => $password_hash);

        $this->db->where('user_id', $user_id);
        return $this->db->update('user', $data);
    }

    // Verify user password
    public function verify_password($user_id, $password)
    {
        $this->db->select('password');
        $this->db->from('user');
        $this->db->where('user_id', $user_id);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $user = $query->row_array();
            return password_verify($password, $user['password']);
        }

        return false;
    }

    // Get active users count
    public function get_active_users_count()
    {
        $this->db->select('COUNT(*) as count');
        $this->db->from('user');
        $this->db->where('is_active', 1); // Assuming you have an is_active field
        $query = $this->db->get();
        return $query->row()->count;
    }

    // Get latest users
    public function get_latest_users($limit = 10)
    {
        $this->db->select('user.*, role.role_name');
        $this->db->from('user');
        $this->db->join('role', 'role.role_id = user.role', 'left');
        $this->db->order_by('user.created_date', 'desc');
        $this->db->limit($limit);

        $query = $this->db->get();
        return $query->result();
    }

    // Bulk update users
    public function bulk_update_users($user_ids, $data)
    {
        $this->db->where_in('user_id', $user_ids);
        return $this->db->update('user', $data);
    }

    // Get users with filters
    public function get_users_with_filters($filters = array())
    {
        $this->db->select('user.*, role.role_name, department_master.department_name, location.location_name');
        $this->db->from('user');
        $this->db->join('role', 'role.role_id = user.role', 'left');
        $this->db->join('department_master', 'department_master.department_id = user.department_id_fk', 'left');
        $this->db->join('location_master as location', 'location.location_id = user.location_id', 'left');

        // Apply filters
        if (!empty($filters['role'])) {
            $this->db->where('user.role', $filters['role']);
        }

        if (!empty($filters['department'])) {
            $this->db->where('user.department_id_fk', $filters['department']);
        }

        if (!empty($filters['location'])) {
            $this->db->where('user.location_id', $filters['location']);
        }

        if (!empty($filters['search'])) {
            $this->db->group_start();
            $this->db->like('user.username', $filters['search']);
            $this->db->or_like('user.user_email', $filters['search']);
            $this->db->group_end();
        }

        $this->db->order_by('user.user_id', 'desc');

        // Limit and offset for pagination
        if (isset($filters['limit'])) {
            $this->db->limit($filters['limit'], isset($filters['offset']) ? $filters['offset'] : 0);
        }

        $query = $this->db->get();
        return $query->result();
    }

    // Count users with filters
    public function count_users_with_filters($filters = array())
    {
        $this->db->select('COUNT(*) as count');
        $this->db->from('user');

        // Apply filters
        if (!empty($filters['role'])) {
            $this->db->where('role', $filters['role']);
        }

        if (!empty($filters['department'])) {
            $this->db->where('department_id_fk', $filters['department']);
        }

        if (!empty($filters['location'])) {
            $this->db->where('location_id', $filters['location']);
        }

        if (!empty($filters['search'])) {
            $this->db->group_start();
            $this->db->like('username', $filters['search']);
            $this->db->or_like('user_email', $filters['search']);
            $this->db->group_end();
        }

        $query = $this->db->get();
        return $query->row()->count;
    }

    // ===========================================
    // USER ROLES METHODS - ADD THESE
    // ===========================================

    /**
     * Add user role to user_roles table
     */
    public function add_user_role($data)
    {
        return $this->db->insert('user_roles', $data);
    }

    /**
     * Get user roles by user ID
     */
    public function get_user_roles_by_user_id($user_id)
    {
        $this->db->select('*');
        $this->db->from('user_roles');
        $this->db->where('user_id', $user_id);
        $this->db->where('is_active', 1);
        $this->db->order_by('created_at', 'desc');

        $query = $this->db->get();
        return $query->result();
    }

    /**
     * Get user role by user ID and role name
     */
    public function get_user_role_by_name($user_id, $role_name)
    {
        $this->db->where('user_id', $user_id);
        $this->db->where('role_name', $role_name);
        $query = $this->db->get('user_roles');
        return $query->row_array();
    }

    /**
     * Add or update user role
     */
    public function add_or_update_user_role($user_id, $role_name, $data)
    {
        $existing = $this->get_user_role_by_name($user_id, $role_name);

        if ($existing) {
            // Update existing role
            return $this->update_user_role_by_name($user_id, $role_name, $data);
        } else {
            // Add new role
            return $this->add_user_role($data);
        }
    }

    /**
     * Update user role by user ID and role name
     */
    public function update_user_role_by_name($user_id, $role_name, $data)
    {
        $this->db->where('user_id', $user_id);
        $this->db->where('role_name', $role_name);
        return $this->db->update('user_roles', $data);
    }

    /**
     * Update user role by ID
     */
    public function update_user_role($role_id, $data)
    {
        $this->db->where('user_role_id', $role_id);
        return $this->db->update('user_roles', $data);
    }

    /**
     * Delete all roles for a user
     */
    public function delete_user_roles($user_id)
    {
        $this->db->where('user_id', $user_id);
        return $this->db->delete('user_roles');
    }

    /**
     * Delete specific user role
     */
    public function delete_user_role($role_id)
    {
        $this->db->where('user_role_id', $role_id);
        return $this->db->delete('user_roles');
    }

    /**
     * Deactivate user role (soft delete)
     */
    public function deactivate_user_role($role_id)
    {
        $this->db->where('user_role_id', $role_id);
        return $this->db->update('user_roles', array('is_active' => 0));
    }

    /**
     * Activate user role
     */
    public function activate_user_role($role_id)
    {
        $this->db->where('user_role_id', $role_id);
        return $this->db->update('user_roles', array('is_active' => 1));
    }

    /**
     * Check if user has specific role
     */
    public function user_has_role($user_id, $role_name)
    {
        $this->db->where('user_id', $user_id);
        $this->db->where('role_name', $role_name);
        $this->db->where('is_active', 1);

        $query = $this->db->get('user_roles');
        return $query->num_rows() > 0;
    }
}
