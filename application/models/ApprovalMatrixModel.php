<?php
defined('BASEPATH') or exit('No direct script access allowed');

class ApprovalMatrixModel extends CI_Model
{

    private $table = 'approval_matrix'; // updated table name

    public function __construct()
    {
        parent::__construct();
        $this->load->database(); // ensure DB is loaded
        $this->_ensure_schema();
    }

    private function _ensure_schema()
    {
        if ($this->db->table_exists('approval_matrix')) {
            if (!$this->db->field_exists('notify_message', 'approval_matrix')) {
                $this->db->query("ALTER TABLE `approval_matrix` ADD COLUMN `notify_message` VARCHAR(500) DEFAULT NULL COMMENT 'Custom approval message' AFTER `status`");
            }
        }

        if (!$this->db->table_exists('user_notifications')) {
            $this->db->query("
                CREATE TABLE IF NOT EXISTS `user_notifications` (
                    `id`         INT(11)      NOT NULL AUTO_INCREMENT,
                    `user_id`    INT(11)      NOT NULL,
                    `title`      VARCHAR(255) NOT NULL,
                    `message`    TEXT         NOT NULL,
                    `type`       ENUM('success','info','warning','error') DEFAULT 'info',
                    `module`     VARCHAR(100) DEFAULT NULL,
                    `ref_id`     INT(11)      DEFAULT NULL,
                    `is_read`    TINYINT(1)   DEFAULT 0,
                    `created_at` DATETIME     DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id`),
                    KEY `idx_user_read` (`user_id`, `is_read`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            ");
        }

        if ($this->db->table_exists('inventory_approval_requests')) {
            if (!$this->db->field_exists('requester_notified', 'inventory_approval_requests')) {
                $this->db->query("ALTER TABLE `inventory_approval_requests` ADD COLUMN `requester_notified` TINYINT(1) DEFAULT 0 AFTER `updated_at`");
            }
        }
    }

    // Get all approval rules
    public function getAll()
    {
        return $this->db->select('approval_matrix.*, department_master.department_name')
            ->from($this->table)
            ->join('department_master', 'department_master.department_id = approval_matrix.department_id', 'left')
            ->order_by('approval_matrix.document_type', 'ASC')
            ->order_by('approval_matrix.level', 'ASC')
            ->get()
            ->result();
    }

    // Get approval rule by ID
    public function getById($id)
    {
        return $this->db->where('id', $id)
            ->get($this->table)
            ->row();
    }

    // Get approvers for a document type and amount
    public function getApprovers($document_type, $amount = 0)
    {
        $this->db->where('document_type', $document_type)
            ->where('status', 'active');

        if (in_array($document_type, ['PO', 'SO', 'PR', 'PA'])) {
            $this->db->where('min_amount <=', $amount);
            $this->db->group_start();
            $this->db->where('max_amount >=', $amount);
            $this->db->or_where('max_amount', 0);
            $this->db->group_end();
        }

        return $this->db->order_by('level', 'ASC')
            ->get($this->table)
            ->result();
    }

    // Insert new approval rule
    public function insert($data)
    {
        return $this->db->insert($this->table, $data);
    }

    // Update existing approval rule
    public function update($id, $data)
    {
        return $this->db->where('id', $id)
            ->update($this->table, $data);
    }

    // Delete approval rule
    public function delete($id)
    {
        return $this->db->where('id', $id)
            ->delete($this->table);
    }
}
