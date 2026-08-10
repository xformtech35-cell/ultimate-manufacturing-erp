<?php
class Home extends MY_Controller {

    protected $user_id;           // actual logged-in user's DB id
    protected $role;              // lowercase role name e.g. 'admin','sales'
    protected $is_admin;          // bool shortcut
    private $months = ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'];

    function __construct() {
        parent::__construct();

        // Load libraries
        $this->load->library(['session', 'form_validation']);

        // Load models
        $models = ['login', 'email', 'supplier', 'customer', 'estimate', 'grn', 'invoice', 'user', 'dashboard'];
        foreach ($models as $model) {
            $this->load->model($model, '', TRUE);
        }

        $session_data_head = $this->session->userdata('session_data_head');

        if (($session_data_head['result']['user_id'] ?? null) === null) {
            $this->session->sess_destroy();
            $this->session->set_flashdata('SUCCESSMSG', "You have been Logged Out !!");
            redirect('LoginController/logout');
        }

        // Read actual logged-in user from session
        $this->user_id  = (int)($session_data_head['result']['user_id'] ?? 1);
        $this->role     = strtolower(trim($session_data_head['result']['role_name'] ?? 'admin'));
        $this->is_admin = ($this->role === 'admin');
    }

    public function index() {
        // Financial Year (April-March)
        $currentMonth  = (int)date('m');
        $currentYear   = (int)date('Y');
        $defaultFyYear = ($currentMonth >= 4) ? $currentYear : $currentYear - 1;

        // Persist FY selection in session
        $fy_get = $this->input->get('fy');
        if ($fy_get !== null && $fy_get !== '') {
            $fy_year = ($fy_get === 'all') ? 'all' : (int)$fy_get;
            $this->session->set_userdata('fy_year', $fy_year);
        } else {
            $fy_year = $this->session->userdata('fy_year') ?: 'all';
        }

        if ($fy_year === 'all') {
            $fy_from  = '2000-01-01';
            $fy_to    = '2099-12-31 23:59:59';
            $fy_label = 'All Financial Years';
        } else {
            $fy_year = (int)$fy_year;
            if ($fy_year < 2015 || $fy_year > $currentYear + 1) {
                $fy_year = $defaultFyYear;
                $this->session->set_userdata('fy_year', $fy_year);
            }
            $fy_from  = $fy_year . '-04-01';
            $fy_to    = ($fy_year + 1) . '-03-31 23:59:59';
            $fy_label = 'FY ' . $fy_year . '-' . substr($fy_year + 1, -2);
        }

        // Base data array
        $data = [
            'fy_year'         => $fy_year,
            'fy_label'        => $fy_label,
            'fy_from'         => $fy_from,
            'fy_to'           => $fy_to,
            'default_fy_year' => $defaultFyYear,
            'logged_user_id'  => $this->user_id,
            'logged_role'     => $this->role,
            'is_admin'        => $this->is_admin,
        ];

        // Include common FY metrics for all roles so top summary strip renders for any user
        $data = array_merge($data, $this->_getCommonFyMetrics($fy_from, $fy_to));

        // Email settings (all roles need this)
        $data['email_set'] = $this->email->get_email_settings(1);
        $this->session->set_userdata(['session_data_head2' => $data['email_set']]);

        // Route to role-specific data fetch using substring matching
        $r = strtolower($this->role);
        if ($r === 'admin' || strpos($r, 'admin') !== false) {
            $data = array_merge($data, $this->_getAdminDashboardData($fy_from, $fy_to));
        } elseif (strpos($r, 'sales') !== false) {
            $data = array_merge($data, $this->_getSalesDashboardData($fy_from, $fy_to));
        } elseif (strpos($r, 'purchase') !== false || strpos($r, 'buyer') !== false || strpos($r, 'procurement') !== false) {
            $data = array_merge($data, $this->_getPurchaseDashboardData($fy_from, $fy_to));
        } elseif (strpos($r, 'production') !== false) {
            $data = array_merge($data, $this->_getProductionDashboardData($fy_from, $fy_to));
        } elseif (strpos($r, 'quality') !== false || strpos($r, 'qc') !== false || strpos($r, 'qa') !== false) {
            $data = array_merge($data, $this->_getQualityDashboardData($fy_from, $fy_to));
        } elseif (strpos($r, 'store') !== false || strpos($r, 'inventory') !== false) {
            $data = array_merge($data, $this->_getStoreDashboardData($fy_from, $fy_to));
        } elseif (strpos($r, 'design') !== false || strpos($r, 'engineering') !== false) {
            $data = array_merge($data, $this->_getDesignDashboardData($fy_from, $fy_to));
        } elseif (strpos($r, 'hr') !== false) {
            $data = array_merge($data, $this->_getHRDashboardData($fy_from, $fy_to));
        } else {
            $data = array_merge($data, $this->_getMinimalData());
        }

        // Load views
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('admin/home', $data);
    }

    /**
     * Common Financial Summary figures for the top header strip across all dashboards
     */
    private function _getCommonFyMetrics($fy_from, $fy_to) {
        $invoice_count               = $this->dashboard->get_fy_invoice_count($fy_from, $fy_to);
        $total_invoice_amount        = $this->dashboard->get_fy_invoice_total_amount($fy_from, $fy_to);
        $purchase_order_count        = $this->dashboard->get_fy_po_count($fy_from, $fy_to);
        $purchase_order_total_amount = $this->dashboard->get_fy_po_total_amount($fy_from, $fy_to);
        $po_count                    = $this->dashboard->get_fy_purchase_bill_count($fy_from, $fy_to);
        $total_po_amount             = $this->dashboard->get_fy_purchase_bill_total_amount($fy_from, $fy_to);
        $quotation_count             = $this->dashboard->get_fy_quotation_count($fy_from, $fy_to);
        $proforma_count              = $this->dashboard->get_fy_proforma_count($fy_from, $fy_to);

        $fy_direct_expense   = (float)$this->dashboard->get_fy_direct_expense($fy_from, $fy_to);
        $fy_indirect_expense = (float)$this->dashboard->get_fy_indirect_expense($fy_from, $fy_to);
        $fy_total_expense    = $fy_direct_expense + $fy_indirect_expense;

        return [
            'invoice_count'               => $invoice_count,
            'total_invoice_amount'        => $total_invoice_amount,
            'purchase_order_count'        => $purchase_order_count,
            'purchase_order_total_amount' => $purchase_order_total_amount,
            'po_count'                    => $po_count,
            'total_po_amount'             => $total_po_amount,
            'quotation_count'             => $quotation_count,
            'proforma_count'              => $proforma_count,
            'fy_direct_expense'           => $fy_direct_expense,
            'fy_indirect_expense'         => $fy_indirect_expense,
            'fy_total_expense'            => $fy_total_expense,
        ];
    }

    // ===========================================================================
    //  ROLE DATA METHODS
    // ===========================================================================

    /** ADMIN - full company-wide data */
    private function _getAdminDashboardData($fy_from, $fy_to) {
        $uid = 1; // admin always uses global context

        // Monthly chart data
        $monthlyData = $this->getFyMonthlyData($fy_from, $fy_to);
        $data = [];
        $suffixes = ['8' => 'monthly_quotations', '5' => 'monthly_sales', '6' => 'monthly_purchase', '7' => 'monthly_expenses'];
        foreach ($suffixes as $suffix => $method) {
            $data = $this->assignMonthlyDataToArray($data, $monthlyData[$method], $suffix);
        }

        // Quotation + invoice status (legacy charts)
        $data = array_merge($data,
            $this->processDocumentCounts($this->dashboard->get_quotation_count($uid), 'number_fk', 'quotation'),
            $this->processDocumentCounts($this->dashboard->get_invoice_count($uid), 'date', 'invoice')
        );

        // All-time counts
        $data['supplier_count']   = $this->supplier->get_supplier_count($uid);
        $data['customer_count']   = $this->customer->get_customer_count($uid);
        $data['grn_count']        = $this->grn->get_grn_count($uid);

        // Admin user data
        $data['user_count']          = $this->user->get_user_count('user');
        $data['invoice_data']        = $this->user->get_invoice_count_user_wise();
        $data['total_invoice_count'] = $this->user->get_total_invoice_count();

        // FY-filtered counts and amounts
        $data['invoice_count']               = $this->dashboard->get_fy_invoice_count($fy_from, $fy_to);
        $data['total_invoice_amount']        = $this->dashboard->get_fy_invoice_total_amount($fy_from, $fy_to);
        $data['purchase_order_count']        = $this->dashboard->get_fy_po_count($fy_from, $fy_to);
        $data['purchase_order_total_amount'] = $this->dashboard->get_fy_po_total_amount($fy_from, $fy_to);
        $data['po_count']                    = $this->dashboard->get_fy_purchase_bill_count($fy_from, $fy_to);
        $data['total_po_amount']             = $this->dashboard->get_fy_purchase_bill_total_amount($fy_from, $fy_to);
        $data['quotation_count']             = $this->dashboard->get_fy_quotation_count($fy_from, $fy_to);
        $data['proforma_count']              = $this->dashboard->get_fy_proforma_count($fy_from, $fy_to);
        $data['salesorder_count']            = $this->db->where('date >=', $fy_from)->where('date <=', $fy_to)->count_all_results('salesorder_total');

        // Expense
        $data['fy_direct_expense']   = (float)$this->dashboard->get_fy_direct_expense($fy_from, $fy_to);
        $data['fy_indirect_expense'] = (float)$this->dashboard->get_fy_indirect_expense($fy_from, $fy_to);
        $data['fy_total_expense']    = $data['fy_direct_expense'] + $data['fy_indirect_expense'];

        // Inventory
        $data['total_inventory_amount'] = $this->dashboard->get_total_inventory_amount();
        $data['total_sale_value']       = $this->dashboard->get_total_sale_value();
        $data['inventory_count']        = $this->dashboard->get_inventory_count();

        // Production / Engineering (FY filtered)
        $data['total_boms_count']     = $this->db->where('date >=', $fy_from)->where('date <=', $fy_to)->count_all_results('bom_total');
        $data['pending_boms_count']   = $this->db->where('status', 0)->where('date >=', $fy_from)->where('date <=', $fy_to)->count_all_results('bom_total');
        $data['total_drawings_count'] = $this->db->where('DATE(created_at) >=', $fy_from)->where('DATE(created_at) <=', $fy_to)->count_all_results('drawing_master');

        // GRN / Quality
        $data['pending_grn_inspections']  = $this->db->where('inspection_status', 'PENDING')->count_all_results('grn');
        $data['approved_grn_inspections'] = $this->db->where('inspection_status', 'PASSED')->count_all_results('grn');

        // Job orders (FY filtered)
        $data['total_job_orders']     = $this->db->where('date >=', $fy_from)->where('date <=', $fy_to)->count_all_results('joborder_total');
        $data['pending_job_orders']   = $this->db->where('status', 0)->where('date >=', $fy_from)->where('date <=', $fy_to)->count_all_results('joborder_total');
        $data['completed_job_orders'] = $this->db->where('status', 2)->where('date >=', $fy_from)->where('date <=', $fy_to)->count_all_results('joborder_total');

        // Store
        $data['pending_grns']          = $this->db->where('approval_status', 'pending')->where('DATE(created_at) >=', $fy_from)->where('DATE(created_at) <=', $fy_to)->count_all_results('grn_total');
        $data['total_material_issues'] = $this->db->where('DATE(created_at) >=', $fy_from)->where('DATE(created_at) <=', $fy_to)->count_all_results('material_issue_slips');
        $data['low_stock_count']       = $this->db->where('stock <=', 5)->count_all_results('inventory');

        // Procurement overview
        $data = array_merge($data, $this->getProcurementData());

        return $data;
    }

    /** SALES ROLE */
    private function _getSalesDashboardData($fy_from, $fy_to) {
        $uid = 1;
        $monthlyData = $this->getFyMonthlyData($fy_from, $fy_to);
        $data = [];
        $data = $this->assignMonthlyDataToArray($data, $monthlyData['monthly_quotations'], '8');
        $data = $this->assignMonthlyDataToArray($data, $monthlyData['monthly_sales'], '5');

        $data['invoice_count']        = $this->dashboard->get_fy_invoice_count($fy_from, $fy_to);
        $data['total_invoice_amount'] = $this->dashboard->get_fy_invoice_total_amount($fy_from, $fy_to);
        $data['quotation_count']      = $this->dashboard->get_fy_quotation_count($fy_from, $fy_to);
        $data['proforma_count']       = $this->dashboard->get_fy_proforma_count($fy_from, $fy_to);
        $data['salesorder_count']     = $this->db->where('date >=', $fy_from)->where('date <=', $fy_to)->count_all_results('salesorder_total');
        $data['customer_count']       = $this->customer->get_customer_count($uid);

        return array_merge($this->_getZeroDefaults(), $data);
    }

    /** PURCHASE ROLE */
    private function _getPurchaseDashboardData($fy_from, $fy_to) {
        $uid = 1;
        $data = [];

        $data['purchase_order_count']        = $this->dashboard->get_fy_po_count($fy_from, $fy_to);
        $data['purchase_order_total_amount'] = $this->dashboard->get_fy_po_total_amount($fy_from, $fy_to);
        $data['po_count']                    = $this->dashboard->get_fy_purchase_bill_count($fy_from, $fy_to);
        $data['total_po_amount']             = $this->dashboard->get_fy_purchase_bill_total_amount($fy_from, $fy_to);
        $data['supplier_count']              = $this->supplier->get_supplier_count($uid);
        $data['grn_count']                   = $this->db->where('DATE(created_at) >=', $fy_from)->where('DATE(created_at) <=', $fy_to)->count_all_results('grn_total');
        $data['pending_grns']                = $this->db->where('approval_status', 'pending')->where('DATE(created_at) >=', $fy_from)->where('DATE(created_at) <=', $fy_to)->count_all_results('grn_total');

        $data['pr_count']             = $this->db->where('DATE(created_at) >=', $fy_from)->where('DATE(created_at) <=', $fy_to)->count_all_results('purchase_requisition');
        $data['rfq_count']            = $this->db->where('DATE(created_at) >=', $fy_from)->where('DATE(created_at) <=', $fy_to)->count_all_results('rfq');
        $data['pr_status_data']       = $this->dashboard->get_pr_status_data($uid);
        $data['rfq_status_data']      = $this->dashboard->get_rfq_status_data($uid);
        $data['po_status_data']       = $this->dashboard->get_po_status_data($uid);
        $data['monthly_pr_data']      = $this->dashboard->get_monthly_pr_data($uid);
        $data['monthly_rfq_data']     = $this->dashboard->get_monthly_rfq_data($uid);
        $data['monthly_po_data']      = $this->dashboard->get_monthly_po_data($uid);
        $data['recent_pr']            = $this->dashboard->get_recent_pr($uid, 5);
        $data['recent_rfq']           = $this->dashboard->get_recent_rfq($uid, 5);
        $data['recent_po']            = $this->dashboard->get_recent_po($uid, 5);
        $data['pending_pr_approvals'] = $this->dashboard->get_pending_pr_approvals($uid);
        $data['pending_po_approvals'] = $this->dashboard->get_pending_po_approvals($uid);

        $data['low_stock_count'] = $this->db->where('stock <=', 5)->count_all_results('inventory');
        
        $p = $this->db->dbprefix;
        $sql_group = "
            SELECT po.number
            FROM {$p}purchase_order po
            JOIN {$p}po_total pot ON pot.number_fk = po.number
            WHERE pot.approval_status = 'approved'
              AND IFNULL(po.is_archived, 0) != 1
            GROUP BY po.number
            HAVING SUM(
                CASE
                    WHEN po.po_pending_quantity = 'Y' THEN po.quantity
                    ELSE COALESCE(CAST(NULLIF(po.po_pending_quantity, '') AS DECIMAL(15, 4)), 0)
                END
            ) > 0
        ";
        $data['pending_po_deliveries_count'] = count($this->db->query($sql_group)->result_array());
        $data['grn_today_count'] = $this->db->where('DATE(created_at)', date('Y-m-d'))->count_all_results('grn_total');
        
        $query_val = $this->db->select('SUM(COALESCE(stock, 0) * COALESCE(cost_price, 0)) as total_val', FALSE)->get('inventory');
        $res_val = $query_val->row();
        $data['inventory_total_value'] = (float)($res_val->total_val ?? 0);

        return array_merge($this->_getZeroDefaults(), $data);
    }

    /** PRODUCTION ROLE */
    private function _getProductionDashboardData($fy_from, $fy_to) {
        $data = [];
        $data['total_job_orders']      = $this->db->where('date >=', $fy_from)->where('date <=', $fy_to)->count_all_results('joborder_total');
        $data['pending_job_orders']    = $this->db->where('status', 0)->where('date >=', $fy_from)->where('date <=', $fy_to)->count_all_results('joborder_total');
        $data['completed_job_orders']  = $this->db->where('status', 2)->where('date >=', $fy_from)->where('date <=', $fy_to)->count_all_results('joborder_total');
        $data['total_boms_count']      = $this->db->where('date >=', $fy_from)->where('date <=', $fy_to)->count_all_results('bom_total');
        $data['pending_boms_count']    = $this->db->where('status', 0)->where('date >=', $fy_from)->where('date <=', $fy_to)->count_all_results('bom_total');
        $data['total_material_issues'] = $this->db->where('DATE(created_at) >=', $fy_from)->where('DATE(created_at) <=', $fy_to)->count_all_results('material_issue_slips');
        
        $monthlyData = $this->getFyMonthlyData($fy_from, $fy_to);
        $data = $this->assignMonthlyDataToArray($data, $monthlyData['monthly_sales'], '5');

        return array_merge($this->_getZeroDefaults(), $data);
    }

    /** QUALITY ROLE */
    private function _getQualityDashboardData($fy_from, $fy_to) {
        $data = [];
        $data['pending_grn_inspections']  = $this->db->where('inspection_status', 'PENDING')->where('DATE(created_at) >=', $fy_from)->where('DATE(created_at) <=', $fy_to)->count_all_results('grn');
        $data['approved_grn_inspections'] = $this->db->where('inspection_status', 'PASSED')->where('DATE(created_at) >=', $fy_from)->where('DATE(created_at) <=', $fy_to)->count_all_results('grn');
        $data['total_job_orders']         = $this->db->where('date >=', $fy_from)->where('date <=', $fy_to)->count_all_results('joborder_total');
        $data['pending_job_orders']       = $this->db->where('status', 0)->where('date >=', $fy_from)->where('date <=', $fy_to)->count_all_results('joborder_total');
        $data['completed_job_orders']     = $this->db->where('status', 2)->where('date >=', $fy_from)->where('date <=', $fy_to)->count_all_results('joborder_total');

        $monthlyData = $this->getFyMonthlyData($fy_from, $fy_to);
        $data = $this->assignMonthlyDataToArray($data, $monthlyData['monthly_sales'], '5');

        return array_merge($this->_getZeroDefaults(), $data);
    }

    /** STORE ROLE */
    private function _getStoreDashboardData($fy_from, $fy_to) {
        $data = [];
        $data['inventory_count']        = $this->dashboard->get_inventory_count();
        $data['total_inventory_amount'] = $this->dashboard->get_total_inventory_amount();
        $data['total_sale_value']       = $this->dashboard->get_total_sale_value();
        $data['low_stock_count']        = $this->db->where('stock <=', 5)->count_all_results('inventory');
        $data['total_material_issues']  = $this->db->where('DATE(created_at) >=', $fy_from)->where('DATE(created_at) <=', $fy_to)->count_all_results('material_issue_slips');
        $data['pending_grns']           = $this->db->where('approval_status', 'pending')->where('DATE(created_at) >=', $fy_from)->where('DATE(created_at) <=', $fy_to)->count_all_results('grn_total');
        $data['grn_count']              = $this->db->where('DATE(created_at) >=', $fy_from)->where('DATE(created_at) <=', $fy_to)->count_all_results('grn_total');

        $monthlyData = $this->getFyMonthlyData($fy_from, $fy_to);
        $data = $this->assignMonthlyDataToArray($data, $monthlyData['monthly_purchase'], '6');

        return array_merge($this->_getZeroDefaults(), $data);
    }

    /** DESIGN ROLE */
    private function _getDesignDashboardData($fy_from, $fy_to) {
        $data = [];
        $data['total_boms_count']     = $this->db->where('date >=', $fy_from)->where('date <=', $fy_to)->count_all_results('bom_total');
        $data['pending_boms_count']   = $this->db->where('status', 0)->where('date >=', $fy_from)->where('date <=', $fy_to)->count_all_results('bom_total');
        $data['total_drawings_count'] = $this->db->where('DATE(created_at) >=', $fy_from)->where('DATE(created_at) <=', $fy_to)->count_all_results('drawing_master');

        $monthlyData = $this->getFyMonthlyData($fy_from, $fy_to);
        $data = $this->assignMonthlyDataToArray($data, $monthlyData['monthly_quotations'], '8');

        return array_merge($this->_getZeroDefaults(), $data);
    }

    /** HR ROLE */
    private function _getHRDashboardData($fy_from, $fy_to) {
        $data = [];
        $data['user_count']          = $this->user->get_user_count('user');
        $data['invoice_data']        = [];
        $data['total_invoice_count'] = 0;
        return array_merge($this->_getZeroDefaults(), $data);
    }

    /** Minimal safe defaults so view never crashes on undefined variable */
    private function _getMinimalData() {
        return $this->_getZeroDefaults();
    }

    /**
     * Zero-value defaults for all dashboard variables
     */
    private function _getZeroDefaults() {
        $monthDefaults = [];
        foreach ($this->months as $m) {
            $monthDefaults[$m . '5'] = 0;
            $monthDefaults[$m . '6'] = 0;
            $monthDefaults[$m . '7'] = 0;
            $monthDefaults[$m . '8'] = 0;
            $monthDefaults[$m]       = 0;
            $monthDefaults[$m . '1'] = 0;
        }
        $statusDefaults = [];
        foreach (['draft','sent','viewed','approved','rejected','canceled'] as $k) {
            $statusDefaults[$k]       = 0;
            $statusDefaults[$k . '1'] = 0;
        }
        return array_merge($monthDefaults, $statusDefaults, [
            'supplier_count'              => 0,
            'customer_count'              => 0,
            'quotation_count'             => 0,
            'invoice_count'               => 0,
            'grn_count'                   => 0,
            'proforma_count'              => 0,
            'po_count'                    => 0,
            'user_count'                  => 0,
            'invoice_data'                => [],
            'total_invoice_count'         => 0,
            'total_invoice_amount'        => 0,
            'purchase_order_count'        => 0,
            'purchase_order_total_amount' => 0,
            'total_po_amount'             => 0,
            'fy_direct_expense'           => 0,
            'fy_indirect_expense'         => 0,
            'fy_total_expense'            => 0,
            'total_inventory_amount'      => 0,
            'total_sale_value'            => 0,
            'inventory_count'             => 0,
            'total_boms_count'            => 0,
            'pending_boms_count'          => 0,
            'total_drawings_count'        => 0,
            'pending_grn_inspections'     => 0,
            'approved_grn_inspections'    => 0,
            'total_job_orders'            => 0,
            'pending_job_orders'          => 0,
            'completed_job_orders'        => 0,
            'pending_grns'                => 0,
            'total_material_issues'       => 0,
            'low_stock_count'             => 0,
            'pr_count'                    => 0,
            'rfq_count'                   => 0,
            'pr_status_data'              => [],
            'rfq_status_data'             => [],
            'po_status_data'              => [],
            'monthly_pr_data'             => array_fill(0, 12, 0),
            'monthly_rfq_data'            => array_fill(0, 12, 0),
            'monthly_po_data'             => array_fill(0, 12, 0),
            'recent_pr'                   => [],
            'recent_rfq'                  => [],
            'recent_po'                   => [],
            'pending_pr_approvals'        => 0,
            'pending_po_approvals'        => 0,
        ]);
    }

    // ===========================================================================
    //  SHARED HELPER METHODS
    // ===========================================================================

    private function getFyMonthlyData($from_date, $to_date) {
        return [
            'monthly_quotations' => $this->dashboard->get_fy_monthly_quotations($from_date, $to_date),
            'monthly_sales'      => $this->dashboard->get_fy_monthly_sales($from_date, $to_date),
            'monthly_purchase'   => $this->dashboard->get_fy_monthly_purchase($from_date, $to_date),
            'monthly_expenses'   => $this->dashboard->get_fy_monthly_expenses($from_date, $to_date),
        ];
    }

    private function assignMonthlyDataToArray($data, $results, $suffix) {
        $monthlyValues = array_fill_keys(range(1, 12), 0);
        foreach ($results as $item) {
            $monthlyValues[$item->month] = $item->amount;
        }
        foreach ($this->months as $index => $month) {
            $data[$month . $suffix] = $monthlyValues[$index + 1];
        }
        return $data;
    }

    private function processDocumentCounts($results, $dateField, $type) {
        $monthly = array_fill_keys($this->months, 0);
        $status  = array_fill_keys(range(1, 6), 0);
        $prefix  = ($type == 'quotation') ? '' : '1';
        foreach ($results as $item) {
            if (isset($item->status) && isset($item->total)) {
                $status[$item->status] += $item->total;
            }
            $value = $item->$dateField;
            if ($dateField == 'number_fk') {
                foreach ($this->months as $month) {
                    if (strpos($value, $month) !== false) { $monthly[$month]++; break; }
                }
            } else {
                foreach ($this->months as $index => $month) {
                    $monthNum = str_pad($index + 1, 2, '0', STR_PAD_LEFT);
                    if (strpos($value, "-$monthNum-") !== false) { $monthly[$month]++; break; }
                }
            }
        }
        $result = [];
        foreach ($this->months as $month) { $result[$month . $prefix] = $monthly[$month]; }
        $statusKeys = ['draft', 'sent', 'viewed', 'approved', 'rejected', 'canceled'];
        foreach ($statusKeys as $index => $key) { $result[$key . $prefix] = $status[$index + 1]; }
        return $result;
    }

    private function getProcurementData() {
        $uid = 1;
        return [
            'pr_count'             => $this->dashboard->get_pr_count($uid),
            'rfq_count'            => $this->dashboard->get_rfq_count($uid),
            'po_count'             => $this->dashboard->get_purchase_bill_count($uid),
            'pr_status_data'       => $this->dashboard->get_pr_status_data($uid),
            'rfq_status_data'      => $this->dashboard->get_rfq_status_data($uid),
            'po_status_data'       => $this->dashboard->get_po_status_data($uid),
            'monthly_pr_data'      => $this->dashboard->get_monthly_pr_data($uid),
            'monthly_rfq_data'     => $this->dashboard->get_monthly_rfq_data($uid),
            'monthly_po_data'      => $this->dashboard->get_monthly_po_data($uid),
            'recent_pr'            => $this->dashboard->get_recent_pr($uid, 5),
            'recent_rfq'           => $this->dashboard->get_recent_rfq($uid, 5),
            'recent_po'            => $this->dashboard->get_recent_po($uid, 5),
            'pending_pr_approvals' => $this->dashboard->get_pending_pr_approvals($uid),
            'pending_po_approvals' => $this->dashboard->get_pending_po_approvals($uid),
        ];
    }

    // ===========================================================================
    //  GST REPORTS (unchanged)
    // ===========================================================================

    private function processDateRange($from_date, $to_date) {
        return [
            'from' => date('Y-m-d', strtotime($from_date)),
            'to'   => date('Y-m-d', strtotime($to_date))
        ];
    }

    private function loadGstReportView($data) {
        $session_data_head = $this->session->userdata('session_data_head');
        $this->load->view('admin/header_side_bar', $session_data_head);
        $this->load->view('admin/gst_reports', $data);
    }

    public function calculate_gst() {
        $dates = $this->processDateRange($this->input->post('from_date'), $this->input->post('to_date'));
        $data  = $this->getGstData($dates['from'], $dates['to']);
        $this->loadGstReportView($data);
    }

    public function get_gst_report_by_date() {
        $dates = $this->processDateRange($this->input->post('from_date'), $this->input->post('to_date'));
        $data  = $this->getGstData($dates['from'], $dates['to']);
        $this->loadGstReportView($data);
    }

    private function getGstData($from_date, $to_date) {
        return [
            'sgst' => $this->dashboard->get_sgst_report_by_date($from_date, $to_date, 1),
            'cgst' => $this->dashboard->get_cgst_report_by_date($from_date, $to_date, 1),
            'igst' => $this->dashboard->get_igst_report_by_date($from_date, $to_date, 1),
        ];
    }
}