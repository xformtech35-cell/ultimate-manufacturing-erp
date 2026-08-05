<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (!isset($session_data_head1)) {
    header($this->config->item('header'));
    exit;
}
defined('BASEPATH') or exit('No direct script access allowed');
?>

<body class="hold-transition skin-blue sidebar-mini">
    <div id="loader" class="center"></div>
    <div class="wrapper">
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    Log History & Audit Trail
                    <small>System Transaction & Activity Logs</small>
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li class="active">Log History</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-md-12">
                        <!-- Custom Tabs -->
                        <div class="nav-tabs-custom">
                            <ul class="nav nav-tabs">
                                <li class="active"><a href="#tab_activity" data-toggle="tab"><i class="fa fa-list-alt text-blue"></i> General Activity Logs</a></li>
                                <li><a href="#tab_ai_gov" data-toggle="tab"><i class="fa fa-android text-purple"></i> AI Governance Logs</a></li>
                                <li><a href="#tab_pr_approval" data-toggle="tab"><i class="fa fa-file-text-o text-info"></i> PR Approval History</a></li>
                                <li><a href="#tab_grn_inspect" data-toggle="tab"><i class="fa fa-shield text-success"></i> GRN Inspection Logs</a></li>
                                <li><a href="#tab_po_email" data-toggle="tab"><i class="fa fa-envelope text-warning"></i> PO Email Logs</a></li>
                            </ul>
                            
                            <div class="tab-content">
                                <!-- TAB 0: GENERAL ACTIVITY LOGS -->
                                <div class="tab-pane active" id="tab_activity">

                                    <!-- ===== FILTER PANEL ===== -->
                                    <?php
                                    $active_filters = 0;
                                    if (!empty($filter_user_id))     $active_filters++;
                                    if (!empty($filter_module))      $active_filters++;
                                    if (!empty($filter_action_type)) $active_filters++;
                                    if (!empty($filter_date_from))   $active_filters++;
                                    if (!empty($filter_date_to))     $active_filters++;
                                    if (!empty($filter_keyword))     $active_filters++;
                                    ?>
                                    <div class="box box-default" style="margin-bottom: 15px;">
                                        <div class="box-header with-border" style="background-color: #f7f7f7; cursor:pointer;" data-toggle="collapse" data-target="#filterPanel">
                                            <h3 class="box-title" style="color: #333; font-weight: 600;">
                                                <i class="fa fa-filter text-blue"></i>&nbsp; Filter Logs
                                                <?php if ($active_filters > 0): ?>
                                                    <span class="badge bg-blue" style="margin-left:8px; font-size:12px;"><?php echo $active_filters; ?> Active</span>
                                                <?php endif; ?>
                                            </h3>
                                            <div class="box-tools pull-right">
                                                <button type="button" class="btn btn-box-tool" style="color:#555;">
                                                    <i class="fa fa-<?php echo ($active_filters > 0) ? 'minus' : 'plus'; ?>"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="box-body <?php echo ($active_filters > 0) ? '' : 'collapse'; ?>" id="filterPanel" style="background-color: #fff; border-top: 1px solid #e0e0e0;">
                                            <form method="GET" action="<?php echo base_url('LogHistoryController/index'); ?>" id="activityFilterForm">
                                                <div class="row" style="margin: 10px 0;">

                                                    <!-- User Filter -->
                                                    <div class="col-md-3 col-sm-6" style="margin-bottom:10px;">
                                                        <label style="font-size:12px; font-weight:600; color:#555; text-transform:uppercase; letter-spacing:0.5px;">
                                                            <i class="fa fa-user text-blue"></i> Filter by User
                                                        </label>
                                                        <select name="filter_user_id" id="filter_user_id" class="form-control select2" style="width:100%; border-radius:4px;">
                                                            <option value="">-- All Users --</option>
                                                            <?php foreach ($all_users as $u): ?>
                                                                <option value="<?php echo $u['user_id']; ?>" <?php echo ($filter_user_id == $u['user_id']) ? 'selected' : ''; ?>>
                                                                    <?php echo htmlspecialchars($u['username']); ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>

                                                    <!-- Module Filter -->
                                                    <div class="col-md-3 col-sm-6" style="margin-bottom:10px;">
                                                        <label style="font-size:12px; font-weight:600; color:#555; text-transform:uppercase; letter-spacing:0.5px;">
                                                            <i class="fa fa-cubes text-green"></i> Filter by Module
                                                        </label>
                                                        <select name="filter_module" id="filter_module" class="form-control select2" style="width:100%; border-radius:4px;">
                                                            <option value="">-- All Modules --</option>
                                                            <?php foreach ($all_modules as $mod): ?>
                                                                <option value="<?php echo htmlspecialchars($mod['controller']); ?>" <?php echo ($filter_module === $mod['controller']) ? 'selected' : ''; ?>>
                                                                    <?php echo htmlspecialchars($mod['title']); ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>

                                                    <!-- Action Type Filter -->
                                                    <div class="col-md-2 col-sm-6" style="margin-bottom:10px;">
                                                        <label style="font-size:12px; font-weight:600; color:#555; text-transform:uppercase; letter-spacing:0.5px;">
                                                            <i class="fa fa-tag text-orange"></i> Action Type
                                                        </label>
                                                        <select name="filter_action_type" id="filter_action_type" class="form-control" style="border-radius:4px;">
                                                            <option value="">-- All Actions --</option>
                                                            <option value="create"     <?php echo ($filter_action_type === 'create')     ? 'selected' : ''; ?>>&#43; Created</option>
                                                            <option value="update"     <?php echo ($filter_action_type === 'update')     ? 'selected' : ''; ?>>&#9998; Updated</option>
                                                            <option value="delete"     <?php echo ($filter_action_type === 'delete')     ? 'selected' : ''; ?>>&#10006; Deleted</option>
                                                            <option value="approval"   <?php echo ($filter_action_type === 'approval')   ? 'selected' : ''; ?>>&#10003; Approval</option>
                                                            <option value="login"      <?php echo ($filter_action_type === 'login')      ? 'selected' : ''; ?>>&#128274; Login/Logout</option>
                                                            <option value="grn"        <?php echo ($filter_action_type === 'grn')        ? 'selected' : ''; ?>>&#128230; GRN</option>
                                                            <option value="inspection" <?php echo ($filter_action_type === 'inspection') ? 'selected' : ''; ?>>&#128270; Inspection</option>
                                                        </select>
                                                    </div>

                                                    <!-- Date From -->
                                                    <div class="col-md-2 col-sm-6" style="margin-bottom:10px;">
                                                        <label style="font-size:12px; font-weight:600; color:#555; text-transform:uppercase; letter-spacing:0.5px;">
                                                            <i class="fa fa-calendar text-purple"></i> Date From
                                                        </label>
                                                        <input type="date" name="filter_date_from" id="filter_date_from"
                                                               class="form-control" style="border-radius:4px;"
                                                               value="<?php echo htmlspecialchars($filter_date_from ?? ''); ?>">
                                                    </div>

                                                    <!-- Date To -->
                                                    <div class="col-md-2 col-sm-6" style="margin-bottom:10px;">
                                                        <label style="font-size:12px; font-weight:600; color:#555; text-transform:uppercase; letter-spacing:0.5px;">
                                                            <i class="fa fa-calendar-check-o text-purple"></i> Date To
                                                        </label>
                                                        <input type="date" name="filter_date_to" id="filter_date_to"
                                                               class="form-control" style="border-radius:4px;"
                                                               value="<?php echo htmlspecialchars($filter_date_to ?? ''); ?>">
                                                    </div>

                                                </div>

                                                <!-- Keyword Search Row -->
                                                <div class="row" style="margin: 0 0 10px 0;">
                                                    <div class="col-md-6 col-sm-12">
                                                        <label style="font-size:12px; font-weight:600; color:#555; text-transform:uppercase; letter-spacing:0.5px;">
                                                            <i class="fa fa-search text-red"></i> Search in Activity Description
                                                        </label>
                                                        <input type="text" name="filter_keyword" id="filter_keyword"
                                                               class="form-control" placeholder="e.g. SO/26-27/0999, PR/26-27/0122, Mr Shivansh..."
                                                               style="border-radius:4px;"
                                                               value="<?php echo htmlspecialchars($filter_keyword ?? ''); ?>">
                                                    </div>
                                                    <div class="col-md-6 col-sm-12" style="padding-top:23px;">
                                                        <button type="submit" class="btn btn-primary" style="border-radius:4px; padding: 6px 22px;">
                                                            <i class="fa fa-search"></i>&nbsp; Apply Filters
                                                        </button>
                                                        &nbsp;
                                                        <a href="<?php echo base_url('LogHistoryController/index'); ?>" class="btn btn-default" style="border-radius:4px; padding: 6px 18px;">
                                                            <i class="fa fa-times"></i>&nbsp; Reset
                                                        </a>
                                                        &nbsp;
                                                        <?php if ($active_filters > 0): ?>
                                                            <span class="text-muted" style="font-size:12px;">
                                                                <i class="fa fa-info-circle"></i>
                                                                Showing filtered results — <?php echo count($activity_logs); ?> record(s) found.
                                                            </span>
                                                        <?php else: ?>
                                                            <span class="text-muted" style="font-size:12px;">
                                                                <?php echo count($activity_logs); ?> total record(s).
                                                            </span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    <!-- ===== END FILTER PANEL ===== -->

                                    <div class="table-responsive">
                                        <table id="activityTable" class="table table-bordered table-striped table-hover table-condensed" style="width:100%">

                                            <thead>
                                                <tr class="bg-gray">
                                                    <th style="width: 5%">Audit ID</th>
                                                    <th style="width: 15%">Operator (User)</th>
                                                    <th style="width: 30%">Action Activity</th>
                                                    <th style="width: 10%">Module</th>
                                                    <th style="width: 10%">IP Address</th>
                                                    <th style="width: 15%">Timestamp</th>
                                                    <th style="width: 15%">Payload Details</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (!empty($activity_logs)): ?>
                                                    <?php foreach ($activity_logs as $act): ?>
                                                        <tr>
                                                            <td><?php echo $act['audit_id']; ?></td>
                                                            <td><strong><?php echo htmlspecialchars($act['operator_name'] ?: 'System/Guest'); ?></strong></td>
                                                            <td><span class="text-bold text-navy"><?php echo htmlspecialchars($act['action']); ?></span></td>
                                                            <?php
                                                            // Map controller class names to friendly module names + badge colours
                                                            $module_map = [
                                                                'SalesOrderController'        => ['label' => 'Sales Order',        'class' => 'bg-blue'],
                                                                'RequisitionController'       => ['label' => 'Purchase Request',   'class' => 'bg-purple'],
                                                                'SupplierController'          => ['label' => 'Purchase Order',     'class' => 'bg-orange'],
                                                                'GrnController'               => ['label' => 'GRN',                'class' => 'bg-teal'],
                                                                'BomController'               => ['label' => 'BOM',                'class' => 'bg-maroon'],
                                                                'JobOrderController'          => ['label' => 'Job Order',          'class' => 'bg-navy'],
                                                                'InvoiceController'           => ['label' => 'Sales Invoice',      'class' => 'bg-green'],
                                                                'ProformaInvoiceController'   => ['label' => 'Proforma Invoice',   'class' => 'bg-green'],
                                                                'DeliveryChallanController'   => ['label' => 'Delivery Challan',   'class' => 'bg-olive'],
                                                                'SalesReturnController'       => ['label' => 'Credit Note',        'class' => 'bg-red'],
                                                                'MaterialIssueController'     => ['label' => 'Material Issue',     'class' => 'bg-yellow'],
                                                                'RFQController'               => ['label' => 'RFQ',                'class' => 'bg-aqua'],
                                                                'CustomerController'          => ['label' => 'Customer Master',    'class' => 'bg-light-blue'],
                                                                'DrawingController'           => ['label' => 'Drawing',            'class' => 'bg-gray'],
                                                                'EstimateController'          => ['label' => 'Quotation',          'class' => 'bg-aqua'],
                                                                'ProjectController'           => ['label' => 'Projects',           'class' => 'bg-navy'],
                                                                'UserController'              => ['label' => 'Users',              'class' => 'bg-purple'],
                                                                'RoleController'              => ['label' => 'Roles & Groups',     'class' => 'bg-maroon'],
                                                                'ApprovalMatrixController'    => ['label' => 'Approval Matrix',    'class' => 'bg-olive'],
                                                                'InventoryController'         => ['label' => 'Inventory',          'class' => 'bg-teal'],
                                                                'BankentryController'         => ['label' => 'Bank Entry',         'class' => 'bg-green'],
                                                                'LoginController'             => ['label' => 'System Login',       'class' => 'bg-gray'],
                                                                'General'                     => ['label' => 'System / Login',     'class' => 'bg-gray'],
                                                            ];
                                                            $tn = $act['table_name'] ?: 'General';
                                                            $mod_info = $module_map[$tn] ?? ['label' => str_replace('Controller', '', $tn), 'class' => 'bg-gray'];
                                                            ?>
                                                            <td>
                                                                <span class="badge <?php echo $mod_info['class']; ?>" style="font-size:11px; padding:4px 7px; border-radius:3px; white-space:nowrap;">
                                                                    <?php echo htmlspecialchars($mod_info['label']); ?>
                                                                </span>
                                                            </td>
                                                            <td style="font-size:12px; color:#555;">
                                                                <?php
                                                                $ip = $act['ip_address'];
                                                                // Normalize any remaining ::1 from old records
                                                                if ($ip === '::1' || $ip === '::ffff:127.0.0.1') {
                                                                    $ip = '127.0.0.1 (localhost)';
                                                                }
                                                                echo '<i class="fa fa-globe" style="color:#aaa; margin-right:4px;"></i>' . htmlspecialchars($ip);
                                                                ?>
                                                            </td>
                                                            <td><?php echo date('d-m-Y H:i A', strtotime($act['created_at'])); ?></td>
                                                            <td class="text-center">
                                                                <?php if (!empty($act['new_values'])): ?>
                                                                    <button class="btn btn-xs btn-primary view-payload-btn" 
                                                                            data-payload="<?php echo htmlspecialchars($act['new_values'], ENT_QUOTES, 'UTF-8'); ?>"
                                                                            data-action="<?php echo htmlspecialchars($act['action'], ENT_QUOTES, 'UTF-8'); ?>">
                                                                        <i class="fa fa-search-plus"></i> View Data
                                                                    </button>
                                                                <?php else: ?>
                                                                    <span class="label label-default" style="color: #fff; background-color: #d2d6de;">No Data</span>
                                                                <?php endif; ?>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <!-- /.tab-pane -->

                                <!-- TAB 1: AI GOVERNANCE LOGS -->
                                <div class="tab-pane" id="tab_ai_gov">
                                    <div class="table-responsive">
                                        <table id="aiGovernanceTable" class="table table-bordered table-striped table-hover table-condensed" style="width:100%">
                                            <thead>
                                                <tr class="bg-gray">
                                                    <th style="width: 5%">ID</th>
                                                    <th style="width: 15%">Action Type</th>
                                                    <th style="width: 10%">BOM Number</th>
                                                    <th style="width: 40%">Recommendation & Context</th>
                                                    <th style="width: 10%">Human Decision</th>
                                                    <th style="width: 10%">Executed By</th>
                                                    <th style="width: 10%">Executed At</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (!empty($ai_logs)): ?>
                                                    <?php foreach ($ai_logs as $log): ?>
                                                        <tr>
                                                            <td><?php echo $log['id']; ?></td>
                                                            <td><span class="label label-primary"><?php echo htmlspecialchars($log['action_type']); ?></span></td>
                                                            <td>
                                                                <?php if (!empty($log['bom_number'])): ?>
                                                                    <a href="<?php echo base_url('BomController/show_bom/' . $log['record_id']); ?>" target="_blank">
                                                                        <strong><?php echo htmlspecialchars($log['bom_number']); ?></strong>
                                                                    </a>
                                                                <?php else: ?>
                                                                    <span class="text-muted">N/A</span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td style="font-size: 13px; max-width: 400px; word-wrap: break-word; white-space: normal;">
                                                                <?php echo htmlspecialchars($log['recommendation_text']); ?>
                                                            </td>
                                                            <td>
                                                                <?php if ($log['human_decision'] == 'approved'): ?>
                                                                    <span class="label label-success"><i class="fa fa-check"></i> Approved</span>
                                                                <?php elseif ($log['human_decision'] == 'rejected'): ?>
                                                                    <span class="label label-danger"><i class="fa fa-close"></i> Rejected</span>
                                                                <?php else: ?>
                                                                    <span class="label label-warning"><?php echo ucfirst($log['human_decision']); ?></span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td><strong><?php echo htmlspecialchars($log['username'] ?? 'System'); ?></strong></td>
                                                            <td><?php echo date('d-m-Y H:i A', strtotime($log['executed_at'])); ?></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <!-- /.tab-pane -->

                                <!-- TAB 2: PR APPROVAL HISTORY -->
                                <div class="tab-pane" id="tab_pr_approval">
                                    <div class="table-responsive">
                                        <table id="prApprovalTable" class="table table-bordered table-striped table-hover table-condensed" style="width:100%">
                                            <thead>
                                                <tr class="bg-gray">
                                                    <th style="width: 5%">ID</th>
                                                    <th style="width: 15%">PR Number</th>
                                                    <th style="width: 10%">Approval Level</th>
                                                    <th style="width: 15%">Approver Role</th>
                                                    <th style="width: 10%">Action Status</th>
                                                    <th style="width: 25%">Comments / Justification</th>
                                                    <th style="width: 10%">Approver Name</th>
                                                    <th style="width: 10%">Action Date</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (!empty($pr_logs)): ?>
                                                    <?php foreach ($pr_logs as $log): ?>
                                                        <tr>
                                                            <td><?php echo $log['history_id']; ?></td>
                                                            <td>
                                                                <a href="<?php echo base_url('RequisitionController/show_requisition_order/' . $log['pr_id']); ?>" target="_blank">
                                                                    <strong><?php echo htmlspecialchars($log['pr_no']); ?></strong>
                                                                </a>
                                                            </td>
                                                            <td><span class="badge bg-purple">Lvl <?php echo $log['approval_level']; ?></span></td>
                                                            <td><?php echo htmlspecialchars($log['approver_role']); ?></td>
                                                            <td>
                                                                <?php 
                                                                $action_label = 'label-default';
                                                                $icon = 'fa-info-circle';
                                                                if ($log['action'] == 'Approved') { $action_label = 'label-success'; $icon = 'fa-check-circle'; }
                                                                elseif ($log['action'] == 'Rejected') { $action_label = 'label-danger'; $icon = 'fa-ban'; }
                                                                elseif ($log['action'] == 'Returned') { $action_label = 'label-warning'; $icon = 'fa-reply'; }
                                                                elseif ($log['action'] == 'Submitted') { $action_label = 'label-info'; $icon = 'fa-arrow-circle-right'; }
                                                                ?>
                                                                <span class="label <?php echo $action_label; ?>"><i class="fa <?php echo $icon; ?>"></i> <?php echo $log['action']; ?></span>
                                                            </td>
                                                            <td style="font-size: 13px; max-width: 300px; word-wrap: break-word; white-space: normal;">
                                                                <?php echo htmlspecialchars($log['comments'] ?: '-'); ?>
                                                            </td>
                                                            <td><strong><?php echo htmlspecialchars($log['approver_name'] ?: 'System'); ?></strong></td>
                                                            <td><?php echo date('d-m-Y H:i A', strtotime($log['action_date'])); ?></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <!-- /.tab-pane -->

                                <!-- TAB 3: GRN INSPECTION LOGS -->
                                <div class="tab-pane" id="tab_grn_inspect">
                                    <div class="table-responsive">
                                        <table id="grnInspectionTable" class="table table-bordered table-striped table-hover table-condensed" style="width:100%">
                                            <thead>
                                                <tr class="bg-gray">
                                                    <th style="width: 5%">ID</th>
                                                    <th style="width: 12%">GRN Number</th>
                                                    <th style="width: 10%">Item Code</th>
                                                    <th style="width: 20%">Qty (Inspected/Acc/Rej)</th>
                                                    <th style="width: 10%">Quality Rating</th>
                                                    <th style="width: 10%">Packaging</th>
                                                    <th style="width: 18%">Remarks</th>
                                                    <th style="width: 10%">Inspector</th>
                                                    <th style="width: 10%">Inspection Date</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (!empty($grn_logs)): ?>
                                                    <?php foreach ($grn_logs as $log): ?>
                                                        <tr>
                                                            <td><?php echo $log['inspection_id']; ?></td>
                                                            <td>
                                                                <a href="<?php echo base_url('GrnController/show_grn/' . $log['grn_number']); ?>" target="_blank">
                                                                    <strong><?php echo htmlspecialchars($log['grn_number']); ?></strong>
                                                                </a>
                                                            </td>
                                                            <td><code><?php echo htmlspecialchars($log['item_code']); ?></code></td>
                                                            <td>
                                                                <span class="text-info">Insp: <strong><?php echo $log['inspected_quantity']; ?></strong></span> | 
                                                                <span class="text-success">Acc: <strong><?php echo $log['accepted_quantity']; ?></strong></span> | 
                                                                <span class="text-danger">Rej: <strong><?php echo $log['rejected_quantity']; ?></strong></span>
                                                            </td>
                                                            <td>
                                                                <?php 
                                                                $rating_lbl = 'bg-gray';
                                                                if ($log['quality_rating'] == 'EXCELLENT') $rating_lbl = 'bg-green';
                                                                elseif ($log['quality_rating'] == 'GOOD') $rating_lbl = 'bg-blue';
                                                                elseif ($log['quality_rating'] == 'FAIR') $rating_lbl = 'bg-yellow';
                                                                elseif ($log['quality_rating'] == 'POOR') $rating_lbl = 'bg-red';
                                                                ?>
                                                                <span class="badge <?php echo $rating_lbl; ?>"><?php echo $log['quality_rating']; ?></span>
                                                            </td>
                                                            <td>
                                                                <?php 
                                                                $pkg_lbl = 'label-default';
                                                                if ($log['packaging_condition'] == 'INTACT') $pkg_lbl = 'label-success';
                                                                elseif ($log['packaging_condition'] == 'MINOR_DAMAGE') $pkg_lbl = 'label-warning';
                                                                elseif ($log['packaging_condition'] == 'MAJOR_DAMAGE') $pkg_lbl = 'label-danger';
                                                                ?>
                                                                <span class="label <?php echo $pkg_lbl; ?>"><?php echo htmlspecialchars($log['packaging_condition']); ?></span>
                                                            </td>
                                                            <td style="font-size: 13px; max-width: 250px; word-wrap: break-word; white-space: normal;">
                                                                <?php echo htmlspecialchars($log['inspection_notes'] ?: '-'); ?>
                                                            </td>
                                                            <td><strong><?php echo htmlspecialchars($log['inspector_name'] ?: 'System'); ?></strong></td>
                                                            <td><?php echo date('d-m-Y H:i A', strtotime($log['inspection_date'])); ?></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <!-- /.tab-pane -->

                                <!-- TAB 4: PO EMAIL LOGS -->
                                <div class="tab-pane" id="tab_po_email">
                                    <div class="table-responsive">
                                        <table id="poEmailTable" class="table table-bordered table-striped table-hover table-condensed" style="width:100%">
                                            <thead>
                                                <tr class="bg-gray">
                                                    <th style="width: 5%">ID</th>
                                                    <th style="width: 15%">PO Number</th>
                                                    <th style="width: 20%">Recipient Vendor</th>
                                                    <th style="width: 10%">Type</th>
                                                    <th style="width: 20%">Subject</th>
                                                    <th style="width: 10%">Status</th>
                                                    <th style="width: 10%">Sender Name</th>
                                                    <th style="width: 10%">Sent At</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (!empty($po_email_logs)): ?>
                                                    <?php foreach ($po_email_logs as $log): ?>
                                                        <tr>
                                                            <td><?php echo $log['log_id']; ?></td>
                                                            <td>
                                                                <a href="<?php echo base_url('SupplierController/show_po/' . $log['po_number']); ?>" target="_blank">
                                                                    <strong><?php echo htmlspecialchars($log['po_number']); ?></strong>
                                                                </a>
                                                            </td>
                                                            <td><code><?php echo htmlspecialchars($log['vendor_email']); ?></code></td>
                                                            <td><span class="badge bg-navy"><?php echo ucfirst($log['email_type']); ?></span></td>
                                                            <td><?php echo htmlspecialchars($log['email_subject']); ?></td>
                                                            <td>
                                                                <?php if ($log['email_status'] == 'sent'): ?>
                                                                    <span class="label label-success"><i class="fa fa-envelope-o"></i> Sent</span>
                                                                <?php elseif ($log['email_status'] == 'failed'): ?>
                                                                    <span class="label label-danger" title="<?php echo htmlspecialchars($log['error_message']); ?>"><i class="fa fa-exclamation-triangle"></i> Failed</span>
                                                                <?php else: ?>
                                                                    <span class="label label-warning"><?php echo ucfirst($log['email_status']); ?></span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td><strong><?php echo htmlspecialchars($log['sender_name'] ?? 'System'); ?></strong></td>
                                                            <td><?php echo date('d-m-Y H:i A', strtotime($log['sent_date'])); ?></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <!-- /.tab-pane -->
                            </div>
                            <!-- /.tab-content -->
                        </div>
                        <!-- nav-tabs-custom -->
                    </div>
                </div>
            </section>
        </div>
    </div>

    <!-- Modal for Payload Details -->
    <div class="modal fade" id="payloadModal" tabindex="-1" role="dialog" aria-labelledby="payloadModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content" style="border-radius: 6px; box-shadow: 0 5px 15px rgba(0,0,0,0.5);">
                <div class="modal-header" style="background-color: #fff; border-bottom: 1px solid #ddd;">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: #333; opacity: 0.8;"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="payloadModalLabel" style="color: #000; font-weight: bold;"><i class="fa fa-database"></i> Activity Data Payload</h4>
                </div>
                <div class="modal-body" style="background-color: #f9f9f9; max-height: 450px; overflow-y: auto;">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="payloadDetailTable">
                            <thead>
                                <tr style="background-color: #f5f5f5;">
                                    <th style="width: 30%; color: #000;">Field Key</th>
                                    <th style="width: 70%; color: #000;">Value</th>
                                </tr>
                            </thead>
                            <tbody id="payloadDetailBody">
                                <!-- Populated dynamically -->
                            </tbody>
                        </table>
                    </div>
                    <div id="payloadRawContainer" style="display: none; margin-top: 15px;">
                        <h5><strong>Raw JSON:</strong></h5>
                        <pre id="payloadRawJson" style="background-color: #f4f4f4; border: 1px solid #ddd; padding: 10px; border-radius: 4px; font-family: monospace; white-space: pre-wrap; word-wrap: break-word;"></pre>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" id="toggleRawBtn"><i class="fa fa-code"></i> Show Raw JSON</button>
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- DataTables Script Integration -->
    <script>
        $(document).ready(function() {
            $('#activityTable').DataTable({
                "order": [[ 0, "desc" ]],
                "pageLength": 10
            });
            $('#aiGovernanceTable').DataTable({
                "order": [[ 6, "desc" ]],
                "pageLength": 10
            });
            $('#prApprovalTable').DataTable({
                "order": [[ 7, "desc" ]],
                "pageLength": 10
            });
            $('#grnInspectionTable').DataTable({
                "order": [[ 8, "desc" ]],
                "pageLength": 10
            });
            $('#poEmailTable').DataTable({
                "order": [[ 7, "desc" ]],
                "pageLength": 10
            });

            // Payload Detail Modal Trigger
            $(document).on('click', '.view-payload-btn', function() {
                var rawPayload = $(this).attr('data-payload');
                var actionTitle = $(this).attr('data-action');
                $('#payloadModalLabel').html('<i class="fa fa-database" style="color:#000;"></i> <span style="color:#000;">Data Payload: ' + actionTitle + '</span>');
                
                var bodyHtml = '';
                var isValidJson = false;
                var parsedJson = {};
                
                try {
                    parsedJson = JSON.parse(rawPayload);
                    isValidJson = true;
                } catch (e) {
                    isValidJson = false;
                }
                
                if (isValidJson && parsedJson !== null && typeof parsedJson === 'object') {
                    $.each(parsedJson, function(key, val) {
                        if (typeof val === 'object' && val !== null) {
                            val = JSON.stringify(val);
                        }
                        bodyHtml += '<tr><td style="color: #000;">' + htmlEncode(key) + '</td><td style="color: #000;">' + htmlEncode(val) + '</td></tr>';
                    });
                    $('#payloadRawJson').text(JSON.stringify(parsedJson, null, 4));
                } else {
                    bodyHtml = '<tr><td colspan="2" class="text-center text-muted">Invalid or empty payload data</td></tr>';
                    $('#payloadRawJson').text(rawPayload || '');
                }
                
                $('#payloadDetailBody').html(bodyHtml);
                $('#payloadRawContainer').hide();
                $('#toggleRawBtn').text('Show Raw JSON').removeClass('btn-info').addClass('btn-default');
                $('#payloadModal').modal('show');
            });

            $('#toggleRawBtn').click(function() {
                var container = $('#payloadRawContainer');
                if (container.is(':visible')) {
                    container.slideUp();
                    $(this).text('Show Raw JSON').removeClass('btn-info').addClass('btn-default');
                } else {
                    container.slideDown();
                    $(this).text('Hide Raw JSON').removeClass('btn-default').addClass('btn-info');
                }
            });

            function htmlEncode(value) {
                return $('<div/>').text(value).html();
            }
        });
    </script>
</body>
