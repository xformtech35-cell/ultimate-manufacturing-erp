<?php

Class Joborder extends CI_Model {

    public function add_customer($data_customer) {
        return $this->db->insert('customer', $data_customer);
    }

    public function get_last_joborder_number($uid) {
        
        $financial_year = '';
        if (date('m') <= 3) {//Upto March
            $financial_year = (date('y') - 1) . '-' . date('y');
        } else {//After March
            $financial_year = date('y') . '-' . (date('y') + 1);
        }

        $this->db->select('count(number_fk) as id');
        $this->db->from('joborder_total');
        $this->db->like('number_fk', $financial_year, "before");
        $this->db->order_by('id', 'DESC');

        $query = $this->db->get();
        $result = $query->row();

        return $result->id ;
    }

    public function get_customer($uid) {
        $this->db->select('*');
        $this->db->from('customer');
        $query = $this->db->get();
        return $query->result();
    }

    public function customer_check($company_name, $uid) {
        $this->db->select('company_name');
        $this->db->from('customer');
        $this->db->where('company_name', $company_name);
        $this->db->limit(1);
        $query = $this->db->get();
        if ($query->num_rows() == 1) {
            return true;
        } else {
            return false;
        }
    }

    public function get_company_name($uid) {
        $this->db->select('*');
        $this->db->from('customer');
        $this->db->order_by("company_name", "asc");
        $query = $this->db->get();
        return $query->result();
    }

    public function get_customer_by_id($id) {
        $this->db->select('*');
        $this->db->from('customer');
        $this->db->where('customer_id', $id);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function get_customer_email($number, $uid) {
        $this->db->select('customer.customer_id, customer.email');
        $this->db->from('joborder_total');
        $this->db->join('customer', 'customer.customer_id = joborder_total.customer_id_fk', 'left');
        $this->db->where('joborder_total.number_fk', $number);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function get_customer_mobile($number, $uid) {
        $this->db->select('customer.mobile');
        $this->db->from('joborder_total');
        $this->db->join('customer', 'customer.customer_id = joborder_total.customer_id_fk', 'left');
        $this->db->where('joborder_total.number_fk', $number);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function delete_customer_by_id($id) {
        $this->db->where('customer_id', $id);
        $this->db->delete('customer');
        if ($this->db->affected_rows() == '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function edit_customer($data_customer, $customer_id) {
        $this->db->where('customer_id', $customer_id);
        $this->db->update('customer', $data_customer);
        if ($this->db->affected_rows() == '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }

public function get_joborders($uid, $limit = 0) {
    $fy_year = $this->session->userdata('fy_year');
    if (!empty($fy_year) && $fy_year !== 'all') {
        $fy_from = $fy_year . '-04-01';
        $fy_to   = ($fy_year + 1) . '-03-31';
        $this->db->where('joborder_total.date >=', $fy_from);
        $this->db->where('joborder_total.date <=', $fy_to);
    }

    $this->db->select('joborder_total.id, joborder_total.number_fk, joborder_total.date, joborder_total.status,
                      joborder_total.note, joborder_total.project_code, joborder_total.customer_code,
                      joborder_total.system, joborder_total.location, joborder_total.capacity,
                      joborder_total.project_qty, joborder_total.oc_number, joborder_total.so_reference,
                      customer.company_name, customer.fullname, u1.username as prepare_by, u2.username as approved_by_name');
    $this->db->from('joborder_total');
    $this->db->join('customer', 'customer.customer_id = joborder_total.customer_id_fk', 'left');
    $this->db->join('user u1', 'joborder_total.uid = u1.user_id', 'left');
    $this->db->join('user u2', 'joborder_total.approved_by = u2.user_id', 'left');
    // Removed uid filter — fetch all Job Orders across all users
    $this->db->order_by('joborder_total.id', 'desc');
    if ($limit > 0) {
        $this->db->limit($limit);
    }
    $query = $this->db->get();

    return $query->result();
}

/**
 * Get job orders that still have pending items (required qty not fully issued).
 * Excludes job orders where every item's issued qty >= required qty.
 */
public function get_joborders_with_pending($uid) {
   
    
    $this->db->select('number_fk');
    $this->db->from('joborder_total jt');
    $this->db->where('jt.material_issue_status', 0);
    $this->db->order_by('id DESC');
    $query = $this->db->get();
    
   $result = $query->result();
    
    return $result;
}

    // Get Job Orders that have already had material issued (for MRN dropdown)
    public function get_joborders_with_issued_material($uid) {
        // Fetch JOs that have at least one issued material slip (not MRN returns)
        $this->db->select('mis.joborder_number AS number_fk');
        $this->db->from('material_issue_slips mis');
        $this->db->where('mis.status', 'issued');
        $this->db->where('mis.joborder_number IS NOT NULL', null, false);
        $this->db->where("mis.joborder_number != ''", null, false);
        $this->db->where("mis.purpose != 'Production Return (MRN)'", null, false);
        $this->db->group_by('mis.joborder_number');
        $this->db->order_by('MAX(mis.issue_id)', 'DESC');
        return $this->db->get()->result();
    }
    public function get_joborder_data_by_status($status, $uid) {
        $fy_year = $this->session->userdata('fy_year');
        if (!empty($fy_year) && $fy_year !== 'all') {
            $fy_from = $fy_year . '-04-01';
            $fy_to   = ($fy_year + 1) . '-03-31 23:59:59';
            $this->db->where('qt.date >=', $fy_from);
            $this->db->where('qt.date <=', $fy_to);
        }

        $this->db->select('qt.id, qt.number_fk, qt.date, c.company_name, c.fullname, qt.status,
                          qt.project_code, qt.customer_code, qt.system, qt.location,
                          qt.capacity, qt.project_qty, qt.oc_number, qt.so_reference');
        $this->db->from('joborder_total qt');
        $this->db->join('customer c', 'c.customer_id=qt.customer_id_fk', 'Left Join');
        $this->db->where('qt.status', $status);
        // Removed uid filter — fetch all Job Orders across all users
        $this->db->group_by('qt.number_fk');
        $query = $this->db->get();

        return $query->result();
    }

    public function get_joborder_data($number, $uid) {
        $this->db->select('joborder.* , inventory.item_name');
        $this->db->from('joborder');
        $this->db->join('customer', 'customer.customer_id=joborder.customer_id', 'Left Join');
        $this->db->join('inventory', 'inventory.code=joborder.product_name', 'Left Join');
        $this->db->where('joborder.number', $number);
        $query = $this->db->get();
        return $query->result();
    }

    public function get_joborder_data_group_by($number, $uid) {
        
        $this->db->select('joborder_total.id, joborder_total.number_fk as number, joborder_total.date, joborder_total.status, 
                          joborder_total.note, joborder_total.project_code, joborder_total.customer_code, joborder_total.system, 
                          joborder_total.location, joborder_total.capacity, joborder_total.project_qty, joborder_total.oc_number,
                          customer.company_name, customer.fullname, u.username as prepare_by, joborder_total.customer_id_fk');
        $this->db->from('joborder_total');
        $this->db->where('joborder_total.number_fk', $number);
        $this->db->join('customer', 'customer.customer_id=joborder_total.customer_id_fk', 'Left Join');
        $this->db->join('user u', 'joborder_total.uid=u.user_id', 'Left Join');
        $query = $this->db->get();
        
        $arr = $query->row_array();
        
        // Get approved by if exists
        // $this->db->select('u1.username as approved_by');
        // $this->db->from('joborder_total qt');
        // $this->db->where('qt.number_fk', $number);
        // $this->db->join('user u1', 'qt.approved_by=u1.user_id', 'Left Join');
        // $query1 = $this->db->get();
        
        //$app_by = $query1->row_array();
        
        // if ($app_by && isset($app_by['approved_by'])) {
        //     $arr['approved_by'] = $app_by['approved_by'];
        // } else {
        //     $arr['approved_by'] = '';
        // }
        
        return $arr;
    }

    public function delete_joborder_by_joborder_number($joborder_number, $uid) {
        $this->db->where('number', $joborder_number);
        $this->db->delete('joborder');
        if ($this->db->affected_rows() >= '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    public function delete_joborder_total_by_joborder_number($joborder_number, $uid) {
        $this->db->where('number_fk', $joborder_number);
        $this->db->delete('joborder_total');
        if ($this->db->affected_rows() >= '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function get_settings($uid) {
        $this->db->select('*');
        $this->db->from('settings');
        $query = $this->db->get();
        return $query->row_array();
    }

    public function add_total_amount($data_total_amount) {
        return $this->db->insert('joborder_total', $data_total_amount);
    }

    public function edit_total_amount($data_total_amount, $number, $uid) {
        $this->db->where('number_fk', $number);
        $this->db->update('joborder_total', $data_total_amount);
        if ($this->db->affected_rows() == '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function delete_item($joborder_id) {
        $this->db->where('joborder_id', $joborder_id);
        $this->db->delete('joborder');
        if ($this->db->affected_rows() == '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function get_joborder_count($uid) {
        $this->db->from('joborder_total');
        // Removed uid filter — count all JOs across all users
        $query = $this->db->get();
        return $query->num_rows();
    }
   
    public function get_status($number, $uid) {
        $this->db->select('status');
        $this->db->from('joborder_total');
        $this->db->where('number_fk', $number);
        $query = $this->db->get();
        return $query->result();
    }

    public function edit_gst_joborder_status($data_status, $quote_number, $uid) {
        $this->db->where('number_fk', $quote_number);
        $this->db->update('joborder_total', $data_status);
        if ($this->db->affected_rows() == '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function get_joborder_number_from_joborder_total($id, $uid) {
        $this->db->select('number_fk');
        $this->db->from('joborder_total');
        $this->db->where('id', $id);
        $query = $this->db->get();
        return $query->row_array();
    }
    
    public function get_joborder_draft_count($status, $uid) {
        $this->db->from('joborder_total');
        $this->db->where('status', $status);
        // Removed uid filter — count all JOs across all users
        $query = $this->db->get();
        return $query->num_rows();
    }
    public function get_salesorders_for_joborder($uid) {
        $this->db->select('joborder_total.id, joborder_total.number_fk, joborder_total.date, 
                          joborder_total.project_code, joborder_total.customer_code,
                          joborder_total.system, joborder_total.location, joborder_total.capacity,
                          joborder_total.oc_number, joborder_total.project_qty,
                          customer.company_name');
        $this->db->from('joborder_total');
        $this->db->join('customer', 'customer.customer_id = joborder_total.customer_id_fk', 'left');
        // Removed uid filter — show all JOs across all users
        $this->db->order_by('joborder_total.id', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }
    
    // Add this method if you want to mark SO as converted
    public function mark_salesorder_converted($so_number, $uid) {
        $this->db->where('number_fk', $so_number);
        $this->db->where('uid', $uid);
        return $this->db->update('joborder_total', array('converted_to_joborder' => 1));
    }

    public function get_datewise_record($from_date, $to_date, $uid) {
        $f_date = date('Y-m-d', strtotime($from_date));
        $t_date = date('Y-m-d', strtotime($to_date));

        $this->db->select('joborder_total.id, joborder_total.number_fk, joborder_total.date, joborder_total.status, 
                          customer.company_name, joborder_total.project_code, joborder_total.customer_code, joborder_total.system, 
                          joborder_total.location, joborder_total.capacity, joborder_total.project_qty, joborder_total.oc_number, joborder_total.so_reference, u.username as prepare_by, u2.username as approved_by_name, joborder_total.note');
        $this->db->from('joborder_total');
        $this->db->where('joborder_total.date >=', $f_date);
        $this->db->where('joborder_total.date <=', $t_date);
        $this->db->join('customer', 'customer.customer_id=joborder_total.customer_id_fk', 'Left Join');
        $this->db->join('user u', 'joborder_total.uid=u.user_id', 'Left Join');
        $this->db->join('user u2', 'joborder_total.approved_by=u2.user_id', 'Left Join');
        $this->db->group_by('joborder_total.number_fk');
        $this->db->order_by("joborder_total.id", "desc");
        $query = $this->db->get();
        
        return $query->result();
    }

    public function get_monthyearwise_record($month_year, $uid) {
        $monthyear_arr = explode('-', $month_year);
        $nmonth = date('m', strtotime($monthyear_arr[0]));
        $newmonthyear_str = $monthyear_arr[1] . '-' . $nmonth;
        
        $this->db->select('joborder_total.id, joborder_total.number_fk, joborder_total.date, joborder_total.status, 
                          customer.company_name, joborder_total.project_code, joborder_total.customer_code, joborder_total.system, 
                          joborder_total.location, joborder_total.capacity, joborder_total.project_qty, joborder_total.oc_number, joborder_total.so_reference, u.username as prepare_by, u2.username as approved_by_name, joborder_total.note');
        $this->db->from('joborder_total');
        $this->db->like('joborder_total.date', $newmonthyear_str, 'both');
        $this->db->join('customer', 'customer.customer_id=joborder_total.customer_id_fk', 'Left Join');
        $this->db->join('user u', 'joborder_total.uid=u.user_id', 'Left Join');
        $this->db->join('user u2', 'joborder_total.approved_by=u2.user_id', 'Left Join');
        $this->db->group_by('joborder_total.number_fk');
        $this->db->order_by("joborder_total.id", "desc");
        $query = $this->db->get();
        
        return $query->result();
    }

    public function get_units() {
        $this->db->select('*');
        $this->db->from('units');
        $query = $this->db->get();
        return $query->result();
    }
}