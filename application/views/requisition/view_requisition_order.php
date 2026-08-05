<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (isset($session_data_head1)) {
} else {
    header($this->config->item('header'));
}

$session_data_head2 = $this->session->userdata('session_data_head2');
$set_cc_email = $session_data_head2['cc_email'];
defined('BASEPATH') or exit('No direct script access allowed');
$_has_project_master = isset($session_data_head1['permission']) && in_array('Projects', $session_data_head1['permission']);

$_col_checkbox     = '40px';
$_col_pr_no        = '240px';
$_col_project      = '130px';
$_col_sales_order  = '180px';
$_col_status       = '110px';
$_col_item         = '320px';
$_col_unit         = '70px';
$_col_qty          = '90px';
$_col_date         = '120px';
$_col_dept         = '140px';
$_col_req_by       = '150px';
$_col_remarks      = '100px';
$_col_actions      = '120px';

$_total_table_width = 1680;
if ($_has_project_master) {
    $_total_table_width += 130;
}
?>

    <!-- Add CSS for styling -->
    <style>
        /* Fix Vertical Scrolling & Force Full Width Fluid Layout */
        html, body {
            height: auto !important;
            min-height: 100% !important;
            overflow-y: auto !important;
            background-color: #ecf0f5 !important;
            width: 100% !important;
            max-width: none !important;
            margin: 0 !important;
        }
        .wrapper {
            height: auto !important;
            min-height: 100% !important;
            overflow-y: auto !important;
            width: 100% !important;
            max-width: none !important;
            margin: 0 !important;
            box-shadow: none !important;
        }
        .content-wrapper {
            min-height: calc(100vh - 50px) !important;
            height: auto !important;
            overflow-y: auto !important;
            width: auto !important;
            max-width: none !important;
        }
        .main-header, .main-sidebar {
            max-width: none !important;
        }

        /* Fix page header text color to be dark and readable */
        .content-header h1 {
            color: #333333 !important;
            font-weight: 600;
        }
        .content-header .breadcrumb li,
        .content-header .breadcrumb li a {
            color: #777777 !important;
        }
        .content-header .breadcrumb li.active {
            color: #333333 !important;
            font-weight: bold;
        }

        /* Flexbox wrapper for responsive filters and action buttons */
        .pr-actions-wrapper {
            display: inline-flex !important;
            flex-wrap: wrap !important;
            gap: 8px !important;
            justify-content: flex-end !important;
            align-items: center !important;
            width: 100%;
        }
        @media (max-width: 991px) {
            .pr-actions-wrapper {
                justify-content: center !important;
                margin-top: 10px !important;
            }
            .text-right-custom {
                justify-content: center !important;
                width: 100% !important;
                margin-top: 10px !important;
            }
            .text-right {
                text-align: center !important;
            }
            .box-header .row {
                flex-direction: column !important;
                align-items: center !important;
            }
        }

        /* Main Table Header & Cells Styling */
        #example3view_requisition_order > thead > tr > th {
            background-color: #3c8dbc !important;
            color: #ffffff !important;
            font-weight: 700 !important;
            vertical-align: middle !important;
            padding: 10px 8px !important;
            background-image: none !important;
            border: 1px solid #d2d6de !important;
        }
        #example3view_requisition_order > thead > tr > th:not(.col-sales-order):not(.col-item) {
            white-space: nowrap !important;
        }
        #example3view_requisition_order > thead > tr > th.col-sales-order,
        #example3view_requisition_order > thead > tr > th.col-item {
            white-space: normal !important;
        }

        #example3view_requisition_order > tbody > tr > td {
            color: #111111 !important;
            vertical-align: middle !important;
            padding: 8px 8px !important;
            border: 1px solid #d2d6de !important;
        }
        #example3view_requisition_order > tbody > tr > td:not(.col-sales-order):not(.col-item) {
            white-space: nowrap !important;
        }
        #example3view_requisition_order > tbody > tr > td.col-sales-order,
        #example3view_requisition_order > tbody > tr > td.col-item {
            white-space: normal !important;
            word-break: break-word !important;
        }
        #example3view_requisition_order > tbody > tr > td.col-sales-order {
            min-width: 180px !important;
        }
        #example3view_requisition_order > tbody > tr > td.col-item {
            min-width: 320px !important;
        }

        /* Opaque Sticky Actions Column Overrides to prevent scroll bleeding */
        #example3view_requisition_order th.global-sticky-col,
        .table-details th.global-sticky-col {
            position: sticky !important;
            right: 0 !important;
            z-index: 25 !important;
            background: linear-gradient(135deg, #1e6fa8 0%, #3c8dbc 100%) !important;
            box-shadow: -4px 0 8px rgba(0,0,0,0.15) !important;
        }

        #example3view_requisition_order td.global-sticky-col,
        .table-details td.global-sticky-col {
            position: sticky !important;
            right: 0 !important;
            background-color: #ffffff !important;
            z-index: 20 !important;
            box-shadow: -4px 0 8px rgba(0,0,0,0.08) !important;
        }

        #example3view_requisition_order tbody tr:nth-of-type(odd) td.global-sticky-col,
        .table-details tbody tr:nth-of-type(odd) td.global-sticky-col {
            background-color: #fafbfc !important;
        }

        #example3view_requisition_order tbody tr.warning-row td.global-sticky-col {
            background-color: #fdf6e2 !important;
        }

        #example3view_requisition_order tbody tr.danger-row td.global-sticky-col {
            background-color: #fce8e6 !important;
        }

        #example3view_requisition_order tbody tr:hover td.global-sticky-col,
        .table-details tbody tr:hover td.global-sticky-col {
            background-color: #f1f7fc !important;
        }

        /* Enforce absolute minimum widths on checkbox and PR number columns */
        #example3view_requisition_order th:nth-child(1),
        #example3view_requisition_order td:nth-child(1),
        .table-details th:nth-child(1),
        .table-details td:nth-child(1) {
            width: 40px !important;
            min-width: 40px !important;
            text-align: center !important;
        }

        #example3view_requisition_order th:nth-child(2),
        #example3view_requisition_order td:nth-child(2),
        .table-details th:nth-child(2),
        .table-details td:nth-child(2) {
            width: 240px !important;
            min-width: 240px !important;
            white-space: nowrap !important;
        }

        /* Specific classes for child table columns */
        .col-sales-order {
            white-space: normal !important;
            word-break: break-word !important;
            min-width: 180px !important;
        }
        .col-item {
            white-space: normal !important;
            word-break: break-word !important;
            min-width: 320px !important;
        }

        /* DataTables Controls & Label Visibility Fixes */
        body .dataTables_wrapper,
        body .dataTables_wrapper label,
        body .dataTables_wrapper .dataTables_length,
        body .dataTables_wrapper .dataTables_length label,
        body .dataTables_wrapper .dataTables_filter,
        body .dataTables_wrapper .dataTables_filter label,
        body .dataTables_wrapper .dataTables_info,
        body .dataTables_wrapper .dataTables_paginate {
            color: #111111 !important;
            font-weight: 700 !important;
            font-size: 13px !important;
            opacity: 1 !important;
            visibility: visible !important;
        }

        /* Force dark text color on all DataTables wrapper children (excluding active links) */
        #example3view_requisition_order_wrapper,
        #example3view_requisition_order_wrapper * {
            color: #111111 !important;
        }

        body .dataTables_wrapper select,
        body .dataTables_wrapper option,
        body .dataTables_wrapper input,
        body .dataTables_wrapper .form-control,
        body .dataTables_wrapper select option {
            color: #111111 !important;
            background-color: #ffffff !important;
            border: 1px solid #777777 !important;
            font-weight: 600 !important;
            font-size: 13px !important;
            opacity: 1 !important;
        }

        body .dataTables_wrapper input::placeholder,
        body .dataTables_wrapper textarea::placeholder,
        body .dataTables_wrapper select::placeholder {
            color: #555555 !important;
            opacity: 1 !important;
        }

        .tooltip-inner {
            background-color: #1e293b !important;
            color: #ffffff !important;
            font-size: 11px !important;
            font-weight: 600 !important;
            padding: 6px 10px !important;
            border-radius: 4px !important;
        }

        /* Beautiful Pagination Styles */
        body .dataTables_wrapper .dataTables_paginate {
            margin-top: 15px !important;
            margin-bottom: 25px !important;
            padding-bottom: 15px !important;
            text-align: right !important;
            float: right !important;
            visibility: visible !important;
            display: block !important;
        }
        body .dataTables_wrapper .dataTables_info {
            margin-top: 15px !important;
            margin-bottom: 25px !important;
            padding-bottom: 15px !important;
            visibility: visible !important;
            display: block !important;
        }
        body .dataTables_wrapper .pagination {
            margin: 0 !important;
            display: inline-flex !important;
            border-radius: 4px !important;
            padding-left: 0 !important;
        }
        body .dataTables_wrapper .pagination > li {
            display: inline !important;
        }

        /* Reset list item wrapper styles in Bootstrap DataTables */
        body .dataTables_wrapper li.paginate_button {
            background: none !important;
            border: none !important;
            padding: 0 !important;
            margin: 0 !important;
            display: inline !important;
        }

        /* Direct pagination link styling */
        body .dataTables_wrapper .pagination > li > a,
        body .dataTables_wrapper .pagination > li > span,
        body .dataTables_wrapper .dataTables_paginate a.paginate_button,
        body .dataTables_wrapper .dataTables_paginate span.paginate_button,
        body .dataTables_wrapper .dataTables_paginate .paginate_button:not(li) {
            display: inline-block !important;
            padding: 6px 12px !important;
            margin: 0 2px !important;
            border: 1px solid #d2d6de !important;
            background-color: #ffffff !important;
            color: #333333 !important;
            cursor: pointer !important;
            font-weight: 700 !important;
            border-radius: 4px !important;
            text-align: center !important;
            text-decoration: none !important;
            font-size: 13px !important;
            line-height: 1.42857143 !important;
            width: auto !important;
            height: auto !important;
            min-width: 35px !important;
            min-height: 35px !important;
            box-sizing: border-box !important;
            visibility: visible !important;
            opacity: 1 !important;
        }

        /* Hover state */
        body .dataTables_wrapper .pagination > li > a:hover,
        body .dataTables_wrapper .pagination > li > span:hover,
        body .dataTables_wrapper .dataTables_paginate a.paginate_button:hover,
        body .dataTables_wrapper .dataTables_paginate .paginate_button:not(li):hover {
            color: #23527c !important;
            background-color: #eeeeee !important;
            border-color: #d2d6de !important;
            text-decoration: none !important;
            z-index: 2 !important;
        }

        /* Active button state */
        body .dataTables_wrapper .pagination > .active > a,
        body .dataTables_wrapper .pagination > .active > span,
        body .dataTables_wrapper .dataTables_paginate a.paginate_button.current,
        body .dataTables_wrapper .dataTables_paginate a.paginate_button.active,
        body .dataTables_wrapper .dataTables_paginate .paginate_button.current:not(li) {
            background-color: #3c8dbc !important;
            color: #ffffff !important;
            border-color: #3c8dbc !important;
            cursor: default !important;
            z-index: 3 !important;
        }

        /* Force active button child text to be white */
        #example3view_requisition_order_wrapper .paginate_button.current,
        #example3view_requisition_order_wrapper .paginate_button.current *,
        #example3view_requisition_order_wrapper .active a,
        #example3view_requisition_order_wrapper .active span {
            color: #ffffff !important;
        }

        /* Disabled state */
        body .dataTables_wrapper .pagination > .disabled > span,
        body .dataTables_wrapper .pagination > .disabled > a,
        body .dataTables_wrapper .dataTables_paginate a.paginate_button.disabled,
        body .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:not(li) {
            color: #999999 !important;
            background-color: #ffffff !important;
            border-color: #d2d6de !important;
            cursor: not-allowed !important;
            opacity: 0.6 !important;
        }

        /* Ensure nested child table aligns perfectly without offset */
        body .dataTables_wrapper tr.shown + tr > td,
        body .dataTables_wrapper tr.pr-child-row-injected > td {
            padding: 0 !important;
            margin: 0 !important;
            border-top: none !important;
            border-bottom: none !important;
            border-left: none !important;
            border-right: none !important;
            background-color: #fafbfc !important;
        }

        #example3view_requisition_order {
            table-layout: fixed !important;
        }
        
        .table-details {
            table-layout: fixed !important;
            width: 100% !important;
        }

        .table-details {
            margin: 0 !important;
            border-collapse: collapse !important;
            border: none !important;
        }

        .table-details tbody td {
            color: #333333 !important;
            background-color: #fafbfc !important;
            vertical-align: middle !important;
            font-size: 12px !important;
            padding: 8px 10px !important;
            border: 1px solid #d2d6de !important;
        }

        /* Force high contrast dark text inside nested table cells and small tags */
        .table-details tbody td small,
        .table-details tbody td .text-muted {
            color: #333333 !important;
            font-weight: 500 !important;
        }

        .table-details tbody tr:hover td {
            background-color: #f1f5f9 !important;
        }

        .disabled-checkbox {
            opacity: 0.5;
            cursor: not-allowed !important;
        }

        .approved-checkbox {
            cursor: pointer;
        }

        .warning-row {
            background-color: #fff8e1 !important;
        }

        .danger-row {
            background-color: #ffebee !important;
        }

        .pr-parent-row:hover {
            background-color: #eef5fc !important;
        }

        .pr-child-row:hover {
            background-color: #eaeff5 !important;
        }

        .label {
            padding: 3px 8px;
            font-size: 12px;
            border-radius: 3px;
            display: inline-block;
        }

        .label-success {
            background-color: #4CAF50;
            color: white;
        }

        .label-warning {
            background-color: #ff9800;
            color: white;
        }

        .label-danger {
            background-color: #f44336;
            color: white;
        }

        .btn-group-sm .btn {
            padding: 3px 10px;
            font-size: 12px;
        }

        .filter-btn.active {
            font-weight: bold;
            box-shadow: inset 0 3px 5px rgba(0, 0, 0, .125);
        }

        /* For action buttons */
        .btn-group .btn-xs {
            padding: 2px 6px;
            font-size: 10px;
            border-radius: 2px;
            margin: 1px;
        }

        .btn-group {
            white-space: nowrap;
        }

        /* Override global custom.js scrolling hijack to prevent scroll bleeding */
        body .box-body .col-sm-12 {
            overflow-x: visible !important;
            overflow-y: visible !important;
        }

        .custom-table-scroll {
            position: relative !important;
            overflow-x: auto !important;
            overflow-y: visible !important;
            border: none !important;
            padding: 0 !important;
            margin-bottom: 25px !important;
            -webkit-overflow-scrolling: touch !important;
            width: 100% !important;
        }

        .box-body {
            padding-bottom: 40px !important;
        }
    </style>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    <i class="fa fa-shopping-cart"></i> Purchase Requisition
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="#">Purchase Requisition</a></li>
                    <li class="active">Purchase Requisition Details</li>
                </ol>
            </section>
            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-info">
                            <div class="box-header with-border" style="padding: 12px 15px;">
                                <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 15px; width: 100%;">
                                    <div style="display: flex; align-items: center; min-width: 250px;">
                                        <h3 class="box-title" style="font-weight: 600; margin: 0; font-size: 18px; color: #1e293b; display: inline-flex; align-items: center; gap: 8px;">
                                            <i class="fa fa-table" style="color: #3b82f6;"></i> Purchase Requisition Details
                                        </h3>
                                    </div>
                                    <div class="text-right-custom" style="display: flex; align-items: center; justify-content: flex-end; flex-wrap: wrap; gap: 10px; min-width: 300px;">
                                        <form action="<?php echo base_url(); ?>RequisitionController/view_requisition_order" method="post" class="form-inline" style="margin: 0;">
                                            <div class="input-group input-group-sm" style="width: 230px; display: table;">
                                                <input type="text" class="form-control onlymonth input-sm" name="month_year" id="month_year" onkeydown="return false;" autocomplete="off" required="" value="<?php echo htmlspecialchars(isset($selected_month) && $selected_month !== 'All' ? $selected_month : ''); ?>" placeholder="Select Month" style="height: 30px; border-radius: 4px 0 0 4px !important;">
                                                <span class="input-group-btn" style="width: 1%;">
                                                    <button class="btn btn-primary btn-sm btn-flat" name="submit" value="" type="submit" style="height: 30px; padding: 5px 15px; border-radius: 0 4px 4px 0 !important; font-weight: 600; border: none;">Submit</button>
                                                </span>
                                            </div>
                                        </form>
                                        <a href="<?php echo base_url(); ?>RequisitionController/view_requisition_order?str=All" class="btn btn-success btn-sm" style="height: 30px; line-height: 20px; font-weight: 600; padding: 5px 15px; border-radius: 4px; display: inline-flex; align-items: center; gap: 6px; border: none;">
                                            <i class="fa fa-list"></i> Show All Purchase Requisition
                                        </a>
                                        <a href="<?php echo base_url(); ?>RequisitionController/create_purchase_requisition" class="btn btn-success btn-sm" style="height: 30px; line-height: 20px; font-weight: 600; padding: 5px 15px; border-radius: 4px; display: inline-flex; align-items: center; gap: 6px; border: none;">
                                            <i class="fa fa-plus"></i> Create Requisition
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <!-- /.box-header -->

                            <div class="box-body no-padding" style="padding: 15px;">
                                <!-- Flash Messages here -->
                                <!-- Start of Form for Convert to RFQ -->
                                <form action="<?php echo base_url(); ?>RFQController/convert_to_rfq" method="post" id="convertToRfqForm">
                                    <!-- Compact Action & Filter Row -->
                                    <div class="row mb-2" style="margin-bottom: 15px;">
                                        <div class="col-md-6 col-sm-12" style="margin-bottom: 10px;">
                                            <div class="btn-group btn-group-sm" role="group">
                                                <button type="button" class="btn btn-default filter-btn active" data-filter="all">All Status</button>
                                                <button type="button" class="btn btn-success filter-btn" data-filter="Approved">Approved Only</button>
                                                <button type="button" class="btn btn-warning filter-btn" data-filter="Pending">Pending Only</button>
                                                <button type="button" class="btn btn-danger filter-btn" data-filter="Rejected">Rejected Only</button>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-12 text-right">
                                            <button type="submit" class="btn btn-primary btn-sm" id="convertBtn">
                                                <i class="fa fa-exchange"></i> Convert to RFQ
                                            </button>
                                        </div>
                                    </div>

                                        <table id="example3view_requisition_order" class="table table-bordered table-striped table-hover" style="width: <?php echo $_total_table_width; ?>px !important; min-width: <?php echo $_total_table_width; ?>px !important; table-layout: fixed !important;">
                                            <thead>
                                                <tr>
                                                     <th style="width: <?php echo $_col_checkbox; ?>; text-align: center; white-space: nowrap;"><input type="checkbox" id="selectAll"></th>
                                                     <th style="width: <?php echo $_col_pr_no; ?>; white-space: nowrap;">PR Number</th>
                                                     <?php if ($_has_project_master): ?>
                                                         <th style="width: <?php echo $_col_project; ?>; white-space: nowrap;">Project Code</th>
                                                     <?php endif; ?>
                                                     <th style="width: <?php echo $_col_sales_order; ?>;" class="col-sales-order">Sales Order</th>
                                                     <th style="width: <?php echo $_col_status; ?>; white-space: nowrap;">Status</th>
                                                     <th style="width: <?php echo $_col_item; ?>;" class="col-item">Item</th>
                                                     <th style="width: <?php echo $_col_unit; ?>; text-align: center;">Unit</th>
                                                     <th style="width: <?php echo $_col_qty; ?>; text-align: right; white-space: nowrap;">Qty</th>
                                                     <th style="width: <?php echo $_col_date; ?>; white-space: nowrap;">PR Date</th>
                                                     <th style="width: <?php echo $_col_dept; ?>;">Department</th>
                                                     <th style="width: <?php echo $_col_req_by; ?>;">Requested By</th>
                                                     <th style="width: <?php echo $_col_remarks; ?>; text-align: center;">Remarks</th>
                                                     
                                                     <th style="width: <?php echo $_col_actions; ?>; text-align: center; white-space: nowrap;">Actions</th>
                                                </tr>
                                            </thead>
                                        <tbody>
                                            <?php
                                            // Group purchase requisitions by PR ID / PR Number
                                            $grouped_requisitions = array();
                                            if (!empty($purchase_requisition)) {
                                                foreach ($purchase_requisition as $req) {
                                                    $pr_key = !empty($req->pr_id) ? $req->pr_id : $req->pr_no;
                                                    if (!isset($grouped_requisitions[$pr_key])) {
                                                        $grouped_requisitions[$pr_key] = array(
                                                            'info' => $req,
                                                            'items' => array()
                                                        );
                                                    }
                                                    $grouped_requisitions[$pr_key]['items'][] = $req;
                                                }
                                            }
                                            ?>
                                            <?php if (!empty($grouped_requisitions)): ?>
                                                <?php foreach ($grouped_requisitions as $pr_key => $pr_group):
                                                    $req = $pr_group['info'];
                                                    $items = $pr_group['items'];
                                                    $item_count = count($items);
                                                    $pr_id = $req->pr_id;
                                                    $status = $req->approval_status;
                                                    $isApproved = ($status == 'Approved');
                                                    $isPending = ($status == 'Pending');
                                                    $isRejected = ($status == 'Rejected');

                                                    $workflowStatus = isset($req->workflow_status) ? $req->workflow_status : 'Draft';
                                                    $isCreator = (isset($req->created_by) && $req->created_by == $session_data_head1['result']['user_id']);
                                                    $canEdit = ($workflowStatus == 'Draft' || $status == 'Rejected') && $isCreator;

                                                    $rowClass = '';
                                                    if ($isPending) $rowClass = 'warning-row';
                                                    elseif ($isRejected) $rowClass = 'danger-row';

                                                    // Count approved items for parent checkbox
                                                    $approved_items_count = 0;
                                                    foreach ($items as $item) {
                                                        if ($item->approval_status == 'Approved') $approved_items_count++;
                                                    }
                                                ?>
                                                    <!-- Parent Header Row (PR Summary) -->
                                                    <tr id="pr-row-<?php echo $pr_id; ?>" class="pr-parent-row <?php echo $rowClass; ?>" data-prid="<?php echo $pr_id; ?>" style="font-weight: 500;">
                                                        <td>
                                                            <?php if ($approved_items_count > 0): ?>
                                                                <input type="checkbox" class="parent-pr-checkbox" data-prid="<?php echo $pr_id; ?>" title="Select/Deselect all approved items in this PR" />
                                                            <?php else: ?>
                                                                <input type="checkbox" disabled class="disabled-checkbox" title="No approved items in this PR" />
                                                            <?php endif; ?>
                                                        </td>
                                                        <td class="toggle-pr-btn" data-prid="<?php echo $pr_id; ?>" style="cursor: pointer;">
                                                            <i class="fa fa-plus-circle text-primary pr-icon-<?php echo $pr_id; ?>" style="margin-right: 6px; font-size: 14px;"></i>
                                                            <strong><?php echo htmlspecialchars($req->pr_no); ?></strong>
                                                            <span class="badge bg-blue" style="font-size: 10px; margin-left: 4px; border-radius: 10px; padding: 2px 7px;">
                                                                <?php echo $item_count; ?> <?php echo ($item_count > 1) ? 'Items' : 'Item'; ?>
                                                            </span>
                                                        </td>
                                                        <?php if ($_has_project_master): ?>
                                                            <td><?php echo isset($req->project_code) ? htmlspecialchars($req->project_code) : 'N/A'; ?></td>
                                                        <?php endif; ?>
                                                        <td class="toggle-pr-btn col-sales-order" data-prid="<?php echo $pr_id; ?>" style="cursor: pointer;">
                                                            <strong><?php echo isset($req->so_no) ? htmlspecialchars($req->so_no) : 'N/A'; ?></strong>
                                                        </td>
                                                        <td>
                                                            <?php
                                                            switch ($status) {
                                                                case 'Pending':
                                                                    echo '<span class="label label-warning">Pending</span>';
                                                                    break;
                                                                case 'Approved':
                                                                    echo '<span class="label label-success">Approved</span>';
                                                                    break;
                                                                case 'Rejected':
                                                                    echo '<span class="label label-danger">Rejected</span>';
                                                                    break;
                                                                default:
                                                                    echo '<span class="label label-default">' . htmlspecialchars($status) . '</span>';
                                                                    break;
                                                            }
                                                            if (isset($req->workflow_status) && $req->workflow_status != 'Draft') {
                                                                echo '<br><small class="text-muted">' . htmlspecialchars($req->workflow_status) . '</small>';
                                                            }
                                                            ?>
                                                        </td>
                                                        <td class="toggle-pr-btn col-item" data-prid="<?php echo $pr_id; ?>" style="cursor: pointer;">
                                                            <span class="text-primary" style="font-weight: 600;">
                                                                <i class="fa fa-cubes"></i> <?php echo htmlspecialchars($items[0]->item_code); ?><?php echo ($item_count > 1) ? ' <small class="text-muted">(+' . ($item_count - 1) . ' more)</small>' : ''; ?>
                                                            </span>
                                                        </td>
                                                        <td style="text-align: center;">-</td>
                                                        <td style="text-align: right;">
                                                            <?php
                                                            $total_qty = 0;
                                                            foreach ($items as $it) { $total_qty += (float)$it->quantity; }
                                                            echo number_format($total_qty, 2);
                                                            ?>
                                                        </td>
                                                        <td><?php echo date('d-m-Y', strtotime($req->pr_date)); ?></td>
                                                        <td><?php echo htmlspecialchars($req->department_name ?: 'Store'); ?></td>
                                                        <td><?php echo htmlspecialchars($req->requested_by_name ?: 'Admin'); ?></td>
                                                         <td style="text-align: center; vertical-align: middle;">
                                                             <?php if (!empty(trim($req->remarks ?? ''))): ?>
                                                                 <button type="button" class="btn btn-info btn-xs" data-remark="<?php echo htmlspecialchars($req->remarks); ?>" onclick="viewRemark(this)" style="padding: 2px 8px; font-size: 10px; line-height: 1.2; height: 22px; font-weight: 600; display: inline-flex; align-items: center; gap: 4px; border: none; border-radius: 3px;">
                                                                     <i class="fa fa-eye"></i> View
                                                                 </button>
                                                             <?php else: ?>
                                                                 -
                                                             <?php endif; ?>
                                                         </td>
                                                         <td style="text-align: center; vertical-align: middle; white-space: nowrap;">
                                                             <div style="display: inline-flex; align-items: center; justify-content: center; gap: 4px;">
                                                                 <button type="button" class="btn btn-default btn-xs toggle-pr-btn" data-prid="<?php echo $pr_id; ?>"  style="padding: 2px 6px; font-size: 10px; line-height: 1.2; margin: 0; display: inline-flex; align-items: center; justify-content: center; height: 22px; width: 22px;">
                                                                     <i class="fa fa-chevron-down pr-chevron-<?php echo $pr_id; ?>"></i>
                                                                 </button>
                                                                 <a href="<?php echo base_url('RequisitionController/show_requisition/' . $req->pr_id); ?>" class="btn btn-info btn-xs"  style="padding: 2px 6px; font-size: 10px; line-height: 1.2; margin: 0; display: inline-flex; align-items: center; justify-content: center; height: 22px; width: 22px; background-color: #00c0ef !important; border-color: #00c0ef !important;">
                                                                     <i class="fa fa-eye"></i>
                                                                 </a>
                                                                 <a href="<?php echo base_url('RequisitionController/edit_requisition/' . $req->pr_id); ?>" class="btn btn-primary btn-xs"  style="padding: 2px 6px; font-size: 10px; line-height: 1.2; margin: 0; display: inline-flex; align-items: center; justify-content: center; height: 22px; width: 22px;">
                                                                     <i class="fa fa-edit"></i>
                                                                 </a>
                                                                 <?php if ($canEdit && $workflowStatus == 'Draft'): ?>
                                                                     <a href="<?php echo base_url('RequisitionController/delete_requisition/' . $req->pr_id); ?>" class="btn btn-danger btn-xs" onclick="return confirm('Are you sure you want to delete this requisition?')" style="padding: 2px 6px; font-size: 10px; line-height: 1.2; margin: 0; display: inline-flex; align-items: center; justify-content: center; height: 22px; width: 22px;">
                                                                         <i class="fa fa-trash"></i>
                                                                     </a>
                                                                 <?php endif; ?>
                                                             </div>
                                                         </td>
                                                    </tr>

                                                 <?php endforeach; ?>
                                             <?php endif; ?>
                                         </tbody>
                                      </table>
                                  </form>
                                 <!-- End of Form -->

                                 <!-- Hidden Child Item Templates -->
                                 <div id="pr-child-templates" style="display: none;">
                                     <?php if (!empty($grouped_requisitions)): ?>
                                         <?php foreach ($grouped_requisitions as $pr_key => $pr_group):
                                             $req = $pr_group['info'];
                                             $items = $pr_group['items'];
                                             $pr_id = $req->pr_id;
                                         ?>
                                             <div id="child-template-<?php echo $pr_id; ?>">
                                                 <div style="padding: 0; margin: 0; background: transparent; border: none; box-shadow: none;">
                                                     <div class="table-responsive" style="border: none; width: 100%;">
                                                         <table class="table table-bordered table-condensed table-hover no-datatable table-details" style="background: #fff; margin-bottom: 0; font-size: 12px;">
                                                             <tbody>
                                                                 <?php foreach ($items as $item):
                                                                     $itemStatus = $item->approval_status;
                                                                     $itemApproved = ($itemStatus == 'Approved');
                                                                     $itemPending = ($itemStatus == 'Pending');
                                                                     $itemRejected = ($itemStatus == 'Rejected');
                                                                 ?>
                                                                     <tr>
                                                                         <td style="text-align: center; width: <?php echo $_col_checkbox; ?> !important; vertical-align: middle;">
                                                                             <?php if ($itemApproved): ?>
                                                                                 <input type="checkbox" name="item_id[]" value="<?php echo $item->item_id; ?>" class="item-checkbox approved-checkbox child-cb-<?php echo $pr_id; ?>" />
                                                                             <?php else: ?>
                                                                                 <input type="checkbox" disabled class="disabled-checkbox" />
                                                                             <?php endif; ?>
                                                                         </td>
                                                                         <td style="width: <?php echo $_col_pr_no; ?> !important; vertical-align: middle;"><small class="text-muted"><?php echo htmlspecialchars($item->pr_no); ?></small></td>
                                                                         <?php if ($_has_project_master): ?>
                                                                             <td style="width: <?php echo $_col_project; ?> !important; vertical-align: middle;"><small class="text-muted"><?php echo isset($item->project_code) ? htmlspecialchars($item->project_code) : '-'; ?></small></td>
                                                                         <?php endif; ?>
                                                                         <td class="col-sales-order" style="width: <?php echo $_col_sales_order; ?> !important; vertical-align: middle;"><small class="text-muted"><?php echo isset($item->so_no) ? htmlspecialchars($item->so_no) : '-'; ?></small></td>
                                                                         <td style="width: <?php echo $_col_status; ?> !important; vertical-align: middle;">
                                                                             <?php
                                                                             switch ($itemStatus) {
                                                                                 case 'Pending': echo '<span class="label label-warning" style="font-size:10px;">Pending</span>'; break;
                                                                                 case 'Approved': echo '<span class="label label-success" style="font-size:10px;">Approved</span>'; break;
                                                                                 case 'Rejected': echo '<span class="label label-danger" style="font-size:10px;">Rejected</span>'; break;
                                                                                 default: echo '<span class="label label-default" style="font-size:10px;">' . htmlspecialchars($itemStatus) . '</span>'; break;
                                                                             }
                                                                             ?>
                                                                         </td>
                                                                         <td class="col-item" style="font-weight: 600; color: #2c3e50; width: <?php echo $_col_item; ?> !important; vertical-align: middle;">
                                                                             <i class="fa fa-cube text-info" style="margin-right: 4px;"></i>
                                                                             <?php echo htmlspecialchars($item->item_code); ?>
                                                                             <?php if (!empty($item->description)): ?>
                                                                                 <br><small class="text-muted" style="font-weight: normal;"><?php echo htmlspecialchars($item->description); ?></small>
                                                                             <?php endif; ?>
                                                                         </td>
                                                                         <td style="text-align: center; width: <?php echo $_col_unit; ?> !important; vertical-align: middle;"><?php echo htmlspecialchars($item->unit); ?></td>
                                                                         <td style="text-align: right; font-weight: 600; color: #00a65a; width: <?php echo $_col_qty; ?> !important; vertical-align: middle;"><?php echo number_format((float)$item->quantity, 2); ?></td>
                                                                         <td style="width: <?php echo $_col_date; ?> !important; vertical-align: middle;"><small class="text-muted"><?php echo date('d-m-Y', strtotime($item->pr_date)); ?></small></td>
                                                                         <td style="width: <?php echo $_col_dept; ?> !important; vertical-align: middle;"><small class="text-muted"><?php echo htmlspecialchars($item->department_name ?: 'Store'); ?></small></td>
                                                                         <td style="width: <?php echo $_col_req_by; ?> !important; vertical-align: middle;"><small class="text-muted"><?php echo htmlspecialchars($item->requested_by_name ?: 'Admin'); ?></small></td>
                                                                         <td style="text-align: center; vertical-align: middle; width: <?php echo $_col_remarks; ?> !important;">
                                                                             <?php if (!empty(trim($item->remarks ?? ''))): ?>
                                                                                 <button type="button" class="btn btn-info btn-xs" data-remark="<?php echo htmlspecialchars($item->remarks); ?>" onclick="viewRemark(this)" style="padding: 2px 8px; font-size: 10px; line-height: 1.2; height: 22px; font-weight: 600; display: inline-flex; align-items: center; gap: 4px; border: none; border-radius: 3px;">
                                                                                     <i class="fa fa-eye"></i> View
                                                                                 </button>
                                                                             <?php else: ?>
                                                                                 -
                                                                             <?php endif; ?>
                                                                         </td>
                                                                         <td style="text-align: center; vertical-align: middle; width: <?php echo $_col_actions; ?> !important;">-</td>
                                                                     </tr>
                                                                 <?php endforeach; ?>
                                                             </tbody>
                                                         </table>
                                                     </div>
                                                 </div>
                                             </div>
                                         <?php endforeach; ?>
                                     <?php endif; ?>
                                 </div>

                            </div>
                            <!-- /.box-body -->
                        </div>
                        <!-- /.box -->
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->
            </section>

            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->

        <!-- Control Sidebar -->

        <?php $this->load->view('admin/footer'); ?>
        <div class="control-sidebar-bg"></div>



    <script>
        $(document).ready(function() {
            // Initialize DataTable if available
            if ($.fn.DataTable) {
                var tableElement = $('#example3view_requisition_order');
                if ($.fn.DataTable.isDataTable(tableElement)) {
                    tableElement.DataTable().destroy();
                }
                
                var table = tableElement.DataTable({
                    "pageLength": 20,
                    "autoWidth": false,
                    "ordering": false,
                    "scrollX": false,
                    "columnDefs": [{
                        "targets": [0, -1],
                        "orderable": false,
                        "searchable": false
                    }],
                    "dom": "<'row'<'col-sm-6'l><'col-sm-6 text-right'f>>" +
                           "<'row'<'col-sm-12' <'custom-table-scroll' t > r >>" +
                           "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                    "language": {
                        "search": "Search Requisitions:",
                        "lengthMenu": "_MENU_ PRs per page"
                    }
                });
            }

            // Expand / Collapse PR child item rows using DataTables row.child()
            $(document).on('click', '.toggle-pr-btn', function(e) {
                e.preventDefault();
                e.stopPropagation();

                var prid = $(this).attr('data-prid') || $(this).closest('[data-prid]').attr('data-prid');
                if (!prid) return;

                var $tr = $('#pr-row-' + prid);
                if ($tr.length === 0) {
                    $tr = $('tr[data-prid="' + prid + '"]');
                }
                if ($tr.length === 0) return;

                var $icon = $('.pr-icon-' + prid);
                var $chevron = $('.pr-chevron-' + prid);
                var $template = $('#child-template-' + prid);
                var childHtml = $template.length > 0 ? $template.html() : '';
                if (!childHtml) return;

                if (typeof table !== 'undefined' && table && table.row) {
                    var row = table.row($tr);
                    if (row && row.node()) {
                        if (row.child.isShown()) {
                            var $childContainer = $tr.next('tr').find('.collapse');
                            if ($childContainer.length > 0) {
                                $childContainer.collapse('hide');
                                $tr.removeClass('shown');
                                $icon.removeClass('fa-minus-circle text-danger').addClass('fa-plus-circle text-primary');
                                $chevron.removeClass('fa-chevron-up').addClass('fa-chevron-down');
                                setTimeout(function() {
                                    row.child.hide();
                                }, 350); // Wait for transition to complete
                            } else {
                                row.child.hide();
                                $tr.removeClass('shown');
                                $icon.removeClass('fa-minus-circle text-danger').addClass('fa-plus-circle text-primary');
                                $chevron.removeClass('fa-chevron-up').addClass('fa-chevron-down');
                            }
                        } else {
                            var collapsibleHtml = '<div id="collapse-' + prid + '" class="collapse" style="height: 0; overflow: hidden;">' + childHtml + '</div>';
                            row.child(collapsibleHtml).show();
                            
                            var $childContainer = $tr.next('tr').find('.collapse');
                            syncChildWidths($tr);
                            
                            $childContainer.collapse('show');
                            $tr.addClass('shown');
                            $icon.removeClass('fa-plus-circle text-primary').addClass('fa-minus-circle text-danger');
                            $chevron.removeClass('fa-chevron-down').addClass('fa-chevron-up');
                        }
                        return;
                    }
                }

                // Fallback if DataTables row object is not available
                var $existingChild = $tr.next('.pr-child-row-injected');
                if ($existingChild.length > 0) {
                    var $childContainer = $existingChild.find('.collapse');
                    if ($childContainer.hasClass('in') || $childContainer.hasClass('show')) {
                        $childContainer.collapse('hide');
                        $icon.removeClass('fa-plus-circle text-primary').addClass('fa-minus-circle text-danger');
                        $chevron.removeClass('fa-chevron-down').addClass('fa-chevron-up');
                    } else {
                        $childContainer.collapse('show');
                        $icon.removeClass('fa-minus-circle text-danger').addClass('fa-plus-circle text-primary');
                        $chevron.removeClass('fa-chevron-up').addClass('fa-chevron-down');
                        syncChildWidths($tr);
                    }
                } else {
                    var injectedRow = '<tr class="pr-child-row-injected"><td colspan="13" style="padding: 0 !important;"><div class="collapse" style="height: 0; overflow: hidden;">' + childHtml + '</div></td></tr>';
                    $tr.after(injectedRow);
                    var $childContainer = $tr.next('tr').find('.collapse');
                    syncChildWidths($tr);
                    $childContainer.collapse('show');
                    $icon.removeClass('fa-plus-circle text-primary').addClass('fa-minus-circle text-danger');
                    $chevron.removeClass('fa-chevron-down').addClass('fa-chevron-up');
                }
            });

            // Dynamically synchronize widths of child row columns with parent columns
            function syncChildWidths($tr) {
                var $parentCells = $tr.find('> td');
                var $childRow = $tr.next('tr');
                var $childRows = $childRow.find('.table-details tbody tr');
                var $firstRowCells = $childRow.find('.table-details tbody tr:first > td');

                if ($parentCells.length === $firstRowCells.length) {
                    var parentTableWidth = $tr.closest('table').outerWidth();
                    $childRow.find('.table-details').css({
                        'width': parentTableWidth + 'px',
                        'min-width': parentTableWidth + 'px',
                        'max-width': parentTableWidth + 'px'
                    });

                    $parentCells.each(function(index) {
                        var $parentCell = $(this);
                        var isHidden = $parentCell.css('display') === 'none' || $parentCell.hasClass('hidden') || $parentCell.hasClass('d-none');
                        
                        var $colCells = $childRows.map(function() {
                            return $(this).find('> td').eq(index)[0];
                        });

                        if (isHidden) {
                            $($colCells).css({
                                'display': 'none'
                            });
                        } else {
                            var width = $parentCell.outerWidth();
                            $($colCells).css({
                                'display': '',
                                'width': width + 'px',
                                'min-width': width + 'px',
                                'max-width': width + 'px'
                            });
                        }
                    });
                }
            }

            // Sync on window resize
            $(window).on('resize', function() {
                $('tr.shown').each(function() {
                    syncChildWidths($(this));
                });
                $('.pr-child-row-injected:visible').each(function() {
                    var $parent = $(this).prev('tr');
                    if ($parent.length > 0) {
                        syncChildWidths($parent);
                    }
                });
            });

            // Sync on DataTables draw event
            if (typeof table !== 'undefined' && table) {
                table.on('draw', function() {
                    $('tr.shown').each(function() {
                        syncChildWidths($(this));
                    });
                });
            }

            // Helper to get unique checked item IDs (handling dynamic rows and hidden templates)
            function getCheckedItemIds() {
                var checkedIds = [];
                $('.approved-checkbox:checked').each(function() {
                    var val = $(this).val();
                    if (val && checkedIds.indexOf(val) === -1) {
                        checkedIds.push(val);
                    }
                });
                return checkedIds;
            }

            // Sync state between visible checkboxes and template checkboxes
            $(document).on('change', '.approved-checkbox', function() {
                var itemId = $(this).val();
                var isChecked = $(this).prop('checked');
                $('.approved-checkbox[value="' + itemId + '"]').not(this).prop('checked', isChecked);
                updateConvertButton();
            });

            // Master PR Parent Checkbox - toggles all child item checkboxes for that PR
            $(document).on('change', '.parent-pr-checkbox', function() {
                var prid = $(this).data('prid');
                var isChecked = $(this).prop('checked');
                $('.child-cb-' + prid + ':not(:disabled)').prop('checked', isChecked).trigger('change');
            });

            // Select All functionality - checks all approved checkboxes across all PRs
            $('#selectAll').click(function() {
                var isChecked = $(this).prop('checked');
                $('.approved-checkbox:not(:disabled)').prop('checked', isChecked).trigger('change');
                $('.parent-pr-checkbox:not(:disabled)').prop('checked', isChecked);
            });

            // Update Convert button state
            function updateConvertButton() {
                var checkedCount = getCheckedItemIds().length;
                var convertBtn = $('#convertBtn');

                if (checkedCount > 0) {
                    convertBtn.prop('disabled', false);
                    convertBtn.html('<i class="fa fa-exchange"></i> Convert to RFQ (' + checkedCount + ')');
                } else {
                    convertBtn.prop('disabled', false);
                    convertBtn.html('<i class="fa fa-exchange"></i> Convert to RFQ');
                }
            }

            // Form validation and dynamic input gathering before submission
            $('#convertToRfqForm').submit(function(e) {
                e.preventDefault(); // Prevent standard submission first

                var checkedItemIds = getCheckedItemIds();

                if (checkedItemIds.length === 0) {
                    alert('Please select at least one Approved item to convert to RFQ.');
                    return false;
                }

                if (!confirm('Are you sure you want to convert ' + checkedItemIds.length + ' approved item(s) to RFQ?')) {
                    return false;
                }

                // Clear any dynamic hidden item_id inputs to avoid duplicates
                $('.dynamic-rfq-item-id').remove();

                // Append the collected checked item IDs as hidden inputs to the form
                var $form = $(this);
                $.each(checkedItemIds, function(index, value) {
                    $form.append('<input type="hidden" name="item_id[]" class="dynamic-rfq-item-id" value="' + value + '">');
                });

                // Submit the form natively
                this.submit();
            });

            // Status filter buttons
            $('.filter-btn').click(function() {
                var filter = $(this).data('filter');

                $('.filter-btn').removeClass('active');
                $(this).addClass('active');

                if (filter === 'all') {
                    $('.pr-parent-row').show();
                    $('.pr-child-row').hide();
                    $('.pr-parent-row .fa-minus-circle').removeClass('fa-minus-circle text-danger').addClass('fa-plus-circle text-primary');
                } else {
                    $('.pr-parent-row').hide();
                    $('.pr-child-row').hide();

                    $('.pr-parent-row').each(function() {
                        var prid = $(this).data('prid');
                        var statusText = $(this).find('td:nth-child(5)').text().trim();

                        if (statusText.includes(filter)) {
                            $(this).show();
                        }
                    });
                }
            });

            // Initialize button state
            updateConvertButton();

            // Tooltip
            $('[title]').tooltip();
        });
    </script>
    <!-- Rest of your modal HTML remains the same -->
    <div id="modal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header btn-danger">
                    <center>
                        <h4 class="modal-title">Send Purchase Order<button type="button" class="close" data-dismiss="modal">&times;</button></h4>
                    </center>
                </div>
                <form class="form-horizontal form_overlay" method="post" action="<?php echo base_url(); ?>SupplierController/send_po_email" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="card-body ">
                            <!-- form start -->
                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label">To<span style="color: red;">*</span></label>
                                <div class="col-sm-7">
                                    <input type="hidden" class="form-control" name="supplier_id" id="supplier_id" value="" required="">
                                    <input type="hidden" class="form-control" name="number" id="number" value="" required="">
                                    <input type="email" class="form-control input-sm" name="to_email" id="to_email" required="">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label">Subject </label>
                                <div class="col-sm-7">
                                    <textarea class="form-control input-sm" name="subject" id="subject" rows="2"></textarea>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label">Message </label>
                                <div class="col-sm-7">
                                    <textarea class="form-control input-sm" name="message" id="message" rows="3"></textarea>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label">Send a copy to</label>
                                <div class="col-sm-7">
                                    <input type="checkbox" name="copy_email" id="copy_email"> <?php echo $set_cc_email; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" id="btnSave" class="btn btn-success"><i class="fa fa-paper-plane" aria-hidden="true"></i> Send</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="glyphicon glyphicon-remove"></i> Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="modal1" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header btn-danger">
                    <center>
                        <h4 class="modal-title">Enter Payment<button type="button" class="close" data-dismiss="modal">&times;</button></h4>
                    </center>
                </div>
                <form class="form-horizontal balance-check form_overlay" method="post" action="<?php echo base_url(); ?>SupplierController/edit_purchase_payment">
                    <div class="modal-body">
                        <div class="card-body ">
                            <!-- form start -->
                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label">Balance<span style="color: red;">*</span></label>
                                <div class="col-sm-7">
                                    <input type="hidden" class="form-control" name="id" id="id" value="">
                                    <input type="hidden" class="form-control" name="total" id="total" value="">
                                    <input type="hidden" class="form-control" name="number" id="number_fk" value="">
                                    <input type="hidden" class="form-control" name="supplier_id_fk" id="supplier_id_fk">
                                    <input type="text" readonly="" class="form-control input-sm" name="balance" id="balance" required="">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label">Payment Type<span style="color: red;">*</span></label>
                                <div class="col-sm-7">
                                    <select class="form-control input-sm " name="payment_type" id="payment_type" required="">
                                        <option value="">Select Payment Type</option>
                                        <option value="Advance">Advance</option>
                                        <option value="Partial">Partial</option>
                                        <option value="Final">Final</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label">Paying Amount<span style="color: red;">*</span></label>
                                <div class="col-sm-7">
                                    <input type="text" class="form-control allownumericwithdecimal" name="paid" id="paid" value="" required="">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label">Payment Method<span style="color: red;">*</span></label>
                                <div class="col-sm-7">
                                    <select class="form-control input-sm " name="payment_method" id="payment_method" required="">
                                        <option value="">Select Payment Method</option>
                                        <option value="1">Cash</option>
                                        <option value="2">Cheque</option>
                                        <option value="3">NetBanking</option>
                                        <option value="4">Credit Card</option>
                                        <option value="5">Other</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label">Note<span style="color: red;">*</span></label>
                                <div class="col-sm-7">
                                    <textarea class="form-control input-sm" required="" name="note" id="note" rows="3"></textarea>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label">Payment Date<span style="color: red;">*</span></label>
                                <div class="col-sm-7">
                                    <input type="text" class="form-control backdate" name="date" id="date" value="" required="" onkeydown="return false;" autocomplete="off">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" id="btnSave" class="btn btn-success">Submit</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="glyphicon glyphicon-remove"></i> Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Remark Modal -->
    <div class="modal fade" id="remarkModal" tabindex="-1" role="dialog" aria-labelledby="remarkModalLabel">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="remarkModalLabel">Requisition Remarks</h4>
                </div>
                <div class="modal-body">
                    <p id="remarkModalBodyText" style="word-wrap: break-word; white-space: pre-wrap;"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
    function viewRemark(btn) {
        var remarkText = $(btn).data('remark');
        $('#remarkModalBodyText').text(remarkText);
        $('#remarkModal').modal('show');
    }
    </script>