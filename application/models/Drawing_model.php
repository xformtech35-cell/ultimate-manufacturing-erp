<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Drawing_model extends CI_Model {
    
    private $primary_key = 'drawing_id';
    private $table = 'drawing_master';
    private $revision_table = 'drawing_revisions';
    private $files_table = 'drawing_files';
    
    public function __construct() {
        parent::__construct();
    }
    
    // ==================== DRAWING MASTER METHODS ====================
    
    /**
     * Get all drawings with project details
     */
    public function get_all_drawings() {
        $fy_year = $this->session->userdata('fy_year');
        if (!empty($fy_year)) {
            $fy_from = $fy_year . '-04-01';
            $fy_to   = ($fy_year + 1) . '-03-31 23:59:59';
            $this->db->where('dm.created_at >=', $fy_from);
            $this->db->where('dm.created_at <=', $fy_to);
        }

        $this->db->select('dm.*, pm.project_name, pm.project_code, (
            SELECT GROUP_CONCAT(so.number_fk SEPARATOR ", ") 
            FROM ' . $this->db->dbprefix . 'salesorder_total so 
            WHERE so.project_code = pm.project_code
        ) as so_numbers');
        $this->db->from($this->table . ' dm');
        $this->db->join('project pm', 'pm.project_id = dm.project_id_fk', 'left');
        $this->db->order_by('dm.drawing_id', 'DESC');
        return $this->db->get()->result();
    }
    
    /**
     * Get drawings by project
     */
    public function get_drawings_by_project($project_id) {
        $this->db->select('dm.*, pm.project_name, pm.project_code, (
            SELECT GROUP_CONCAT(so.number_fk SEPARATOR ", ") 
            FROM ' . $this->db->dbprefix . 'salesorder_total so 
            WHERE so.project_code = pm.project_code
        ) as so_numbers');
        $this->db->from($this->table . ' dm');
        $this->db->join('project pm', 'pm.project_id = dm.project_id_fk', 'left');
        $this->db->where('dm.project_id_fk', $project_id);
        $this->db->order_by('dm.drawing_id', 'DESC');
        return $this->db->get()->result();
    }
    
    /**
     * Get single drawing by ID
     */
    public function get_drawing_by_id($drawing_id) {
        $this->db->select('dm.*, pm.project_name, pm.project_code, (
            SELECT GROUP_CONCAT(so.number_fk SEPARATOR ", ") 
            FROM ' . $this->db->dbprefix . 'salesorder_total so 
            WHERE so.project_code = pm.project_code
        ) as so_numbers');
        $this->db->from($this->table . ' dm');
        $this->db->join('project pm', 'pm.project_id = dm.project_id_fk', 'left');
        $this->db->where('dm.' . $this->primary_key, $drawing_id);
        return $this->db->get()->row();
    }
    
    /**
     * Insert drawing
     */
    public function insert_drawing($data) {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }
    
    /**
     * Update drawing
     */
    public function update_drawing($drawing_id, $data) {
        $this->db->where($this->primary_key, $drawing_id);
        return $this->db->update($this->table, $data);
    }
    
    /**
     * Soft delete drawing (set status to obsolete)
     */
    public function delete_drawing($drawing_id) {
        $this->db->where($this->primary_key, $drawing_id);
        return $this->db->update($this->table, array('status' => 'obsolete'));
    }
    
    /**
     * Hard delete drawing (with revisions and files)
     */
    public function hard_delete_drawing($drawing_id) {
        // First get all revisions to delete their files
        $revisions = $this->get_revisions_by_drawing($drawing_id);
        foreach ($revisions as $rev) {
            // Delete files for this revision
            $files = $this->get_files_by_revision($rev->revision_id);
            foreach ($files as $file) {
                if (file_exists($file->file_path)) {
                    @unlink($file->file_path);
                }
            }
            // Delete files records
            $this->db->where('revision_id_fk', $rev->revision_id);
            $this->db->delete($this->files_table);
        }
        
        // Delete revisions
        $this->db->where('drawing_id_fk', $drawing_id);
        $this->db->delete($this->revision_table);
        
        // Delete drawing master
        $this->db->where($this->primary_key, $drawing_id);
        return $this->db->delete($this->table);
    }
    
    /**
     * Check duplicate drawing number
     */
    public function check_duplicate_drawing_no($drawing_no, $project_id, $drawing_id = null) {
        $this->db->where('drawing_no', $drawing_no);
        $this->db->where('project_id_fk', $project_id);
        if ($drawing_id) {
            $this->db->where('drawing_id !=', $drawing_id);
        }
        $query = $this->db->get($this->table);
        return $query->num_rows() > 0;
    }
    
    /**
     * Get all active projects for dropdown
     */
    public function get_active_projects() {
        $CI =& get_instance();
        $session_data = $CI->session->userdata('session_data_head');
        $has_project_master = isset($session_data['result']['has_project_master']) && $session_data['result']['has_project_master'] == 1;

        if (!$has_project_master) {
            $this->db->select('so.id as salesorder_id, so.number_fk as so_numbers, so.project_code, pm.project_id, pm.project_name');
            $this->db->from('salesorder_total so');
            $this->db->join('project pm', 'pm.project_code = so.number_fk OR (so.project_code != "" AND pm.project_code = so.project_code)', 'left');
            $this->db->where('so.number_fk !=', '');
            $this->db->order_by('so.id', 'DESC');
            $results = $this->db->get()->result();
            
            $formatted_results = array();
            foreach ($results as $row) {
                $proj = new stdClass();
                $proj->project_id = $row->project_id ? $row->project_id : 'SO_' . $row->salesorder_id;
                $proj->project_code = $row->project_code ? $row->project_code : $row->so_numbers;
                $proj->project_name = $row->project_name ? $row->project_name : $row->so_numbers;
                $proj->so_numbers = $row->so_numbers;
                $formatted_results[] = $proj;
            }
            return $formatted_results;
        }

        $this->db->select('pm.project_id, pm.project_code, pm.project_name, (
            SELECT GROUP_CONCAT(so.number_fk SEPARATOR ", ") 
            FROM ' . $this->db->dbprefix . 'salesorder_total so 
            WHERE so.project_code = pm.project_code
        ) as so_numbers');
        $this->db->from('project pm');
        $this->db->order_by('pm.project_code');
        return $this->db->get()->result();
    }
    
    public function create_project_for_so($so_id) {
        $this->db->where('id', $so_id);
        $so = $this->db->get('salesorder_total')->row();
        if ($so) {
            $project_code = $so->number_fk;
            $this->db->where('project_code', $project_code);
            $existing = $this->db->get('project')->row();
            if ($existing) {
                $this->db->where('id', $so_id);
                $this->db->update('salesorder_total', array('project_code' => $project_code));
                return $existing->project_id;
            }
            
            $data = array(
                'project_code' => $project_code,
                'project_name' => $project_code,
                'project_status' => 'In Progress',
                'project_start_date' => $so->date ? $so->date : date('Y-m-d')
            );
            $this->db->insert('project', $data);
            $project_id = $this->db->insert_id();
            
            $this->db->where('id', $so_id);
            $this->db->update('salesorder_total', array('project_code' => $project_code));
            
            return $project_id;
        }
        return null;
    }
    
    /**
     * Get next revision number
     */
    public function get_next_revision_number($drawing_id) {
        $this->db->select_max('revision_no');
        $this->db->where('drawing_id_fk', $drawing_id);
        $query = $this->db->get($this->revision_table);
        $result = $query->row();
        
        if ($result && $result->revision_no) {
            $current_max = intval($result->revision_no);
            $next = $current_max + 1;
            return str_pad($next, 3, '0', STR_PAD_LEFT);
        }
        
        return '001';
    }
    
    // ==================== DRAWING REVISION METHODS ====================
    
    /**
     * Get all revisions for a drawing
     */
    public function get_revisions_by_drawing($drawing_id) {
        $this->db->where('drawing_id_fk', $drawing_id);
        $this->db->order_by('CAST(revision_no AS UNSIGNED)', 'DESC');
        return $this->db->get($this->revision_table)->result();
    }
    
    /**
     * Get latest revision for a drawing
     */
    public function get_latest_revision($drawing_id) {
        $this->db->where('drawing_id_fk', $drawing_id);
        $this->db->where('status', 'active');
        $this->db->order_by('CAST(revision_no AS UNSIGNED)', 'DESC');
        $this->db->limit(1);
        return $this->db->get($this->revision_table)->row();
    }
    
    /**
     * Get single revision by ID
     */
    public function get_revision_by_id($revision_id) {
        $this->db->where('revision_id', $revision_id);
        return $this->db->get($this->revision_table)->row();
    }
    
    /**
     * Get revision with all files
     */
    public function get_revision_with_files($revision_id) {
        $revision = $this->get_revision_by_id($revision_id);
        if ($revision) {
            $revision->files = $this->get_files_by_revision($revision_id);
        }
        return $revision;
    }
    
    /**
     * Insert revision
     */
    public function insert_revision($data) {
        $this->db->insert($this->revision_table, $data);
        return $this->db->insert_id();
    }
    
    /**
     * Update revision
     */
    public function update_revision($revision_id, $data) {
        $this->db->where('revision_id', $revision_id);
        return $this->db->update($this->revision_table, $data);
    }
    
    /**
     * Supersede old revisions
     */
    public function supersede_old_revisions($drawing_id, $current_revision_no) {
        $this->db->where('drawing_id_fk', $drawing_id);
        $this->db->where('revision_no !=', $current_revision_no);
        $this->db->update($this->revision_table, array('status' => 'superseded'));
    }
    
    /**
     * Delete revision and its files
     */
    public function delete_revision($revision_id) {
        // Get all files for this revision
        $files = $this->get_files_by_revision($revision_id);
        foreach ($files as $file) {
            if (file_exists($file->file_path)) {
                @unlink($file->file_path);
            }
        }
        
        // Delete files records
        $this->db->where('revision_id_fk', $revision_id);
        $this->db->delete($this->files_table);
        
        // Delete revision
        $this->db->where('revision_id', $revision_id);
        return $this->db->delete($this->revision_table);
    }
    
    // ==================== FILE METHODS ====================
    
    /**
     * Get all files for a revision
     */
    public function get_files_by_revision($revision_id) {
        $this->db->where('revision_id_fk', $revision_id);
        $this->db->order_by('file_id', 'ASC');
        return $this->db->get($this->files_table)->result();
    }
    
    /**
     * Get single file by ID
     */
    public function get_file_by_id($file_id) {
        $this->db->where('file_id', $file_id);
        return $this->db->get($this->files_table)->row();
    }
    
    /**
     * Insert file record
     */
    public function insert_file($data) {
        return $this->db->insert($this->files_table, $data);
    }
    
    /**
     * Insert multiple files at once
     */
    public function insert_files($files_data) {
        if (empty($files_data)) return false;
        return $this->db->insert_batch($this->files_table, $files_data);
    }
    
    /**
     * Delete file record
     */
    public function delete_file($file_id) {
        $this->db->where('file_id', $file_id);
        return $this->db->delete($this->files_table);
    }
    
    /**
     * Delete all files for a revision
     */
    public function delete_files_by_revision($revision_id) {
        $this->db->where('revision_id_fk', $revision_id);
        return $this->db->delete($this->files_table);
    }
    
    /**
     * Count files for a revision
     */
    public function count_files_by_revision($revision_id) {
        $this->db->where('revision_id_fk', $revision_id);
        return $this->db->count_all_results($this->files_table);
    }
}
?>