<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (isset($session_data_head1)) {
} else {
    header($this->config->item('header'));
}
defined('BASEPATH') or exit('No direct script access allowed');
$_has_project_master = isset($session_data_head1['permission']) && in_array('Projects', $session_data_head1['permission']);

// Column widths configurations for MIS and MRN tables
$_col_sr_no      = '70px';
$_col_issue_no   = '180px';
$_col_date       = '120px';
$_col_issued_to  = '150px';
$_col_dept       = '150px';
$_col_items      = '80px';
$_col_qty        = '100px';
$_col_status     = '110px';
$_col_actions    = '100px';
$_total_table_width = 1060;
?>

    <style>
        /* Force scroll containment on .custom-table-scroll */
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

        /* Enforce table fixed layout */
        #tableMIS, #tableMRN {
            table-layout: fixed !important;
        }

        /* Opaque Sticky Actions Column Overrides to prevent scroll bleeding */
        #tableMIS th.global-sticky-col,
        #tableMRN th.global-sticky-col {
            position: sticky !important;
            right: 0 !important;
            z-index: 25 !important;
            background: linear-gradient(135deg, #1e6fa8 0%, #3c8dbc 100%) !important;
            box-shadow: -4px 0 8px rgba(0,0,0,0.15) !important;
        }

        #tableMIS td.global-sticky-col,
        #tableMRN td.global-sticky-col {
            position: sticky !important;
            right: 0 !important;
            background-color: #ffffff !important;
            z-index: 20 !important;
            box-shadow: -4px 0 8px rgba(0,0,0,0.08) !important;
        }

        #tableMIS tbody tr:odd td.global-sticky-col,
        #tableMRN tbody tr:odd td.global-sticky-col {
            background-color: #fafbfc !important;
        }

        #tableMIS tbody tr:hover td.global-sticky-col,
        #tableMRN tbody tr:hover td.global-sticky-col {
            background-color: #f1f7fc !important;
        }

        /* Premium headers style match */
        #tableMIS > thead > tr > th,
        #tableMRN > thead > tr > th {
            background-color: #3c8dbc !important;
            color: #ffffff !important;
            font-weight: 700 !important;
            vertical-align: middle !important;
            padding: 10px 30px 10px 8px !important;
            border: 1px solid #d2d6de !important;
            white-space: nowrap !important;
            position: relative !important;
        }

        /* Ensure sorting icons are white and visible */
        #tableMIS thead th.sorting:after,
        #tableMIS thead th.sorting_asc:after,
        #tableMIS thead th.sorting_desc:after,
        #tableMRN thead th.sorting:after,
        #tableMRN thead th.sorting_asc:after,
        #tableMRN thead th.sorting_desc:after {
            color: #ffffff !important;
            opacity: 0.8 !important;
            right: 8px !important;
            display: block !important;
        }

        #tableMIS > tbody > tr > td,
        #tableMRN > tbody > tr > td {
            color: #111111 !important;
            vertical-align: middle !important;
            padding: 8px 8px !important;
            border: 1px solid #d2d6de !important;
            white-space: nowrap !important;
        }

        /* Ensure content header title is clearly visible (preventing white-on-white) */
        .content-header h1 {
            color: #222d32 !important;
            font-weight: 600 !important;
            font-size: 24px !important;
            text-shadow: none !important;
        }

        /* Ensure content header breadcrumbs are highly readable and dark grey */
        .content-header .breadcrumb > li,
        .content-header .breadcrumb > li.active,
        .content-header .breadcrumb > li > a {
            color: #444444 !important;
            font-weight: 500 !important;
        }
        .content-header .breadcrumb > li > a:hover {
            color: #3c8dbc !important;
            text-decoration: none !important;
        }
    </style>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    Material Issue
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="#">Inventory</a></li>
                    <li class="active">Material Issue Slips</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-info">
                            <div class="box-header with-border">
                                <div class="row">
                                    <div class="col-md-4">
                                        <h3 class="box-title">Material Issue Slips</h3>
                                    </div>
                                    <div class="col-md-4">
                                        <form action="<?php echo base_url(); ?>MaterialIssueController/get_monthyearwise_record" method="post">
                                            <div class="form-group row">
                                                <div class="col-xs-12 col-sm-8">
                                                    <input type="text" class="form-control onlymonth input-sm" name="month_year" id="month_year" onkeydown="return false;" autocomplete="off" placeholder="Select Month/Year">
                                                </div>
                                                <div class="col-xs-12 col-sm-4">
                                                    <button type="submit" class="btn btn-primary btn-block" name="submit" value="">Submit</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="col-md-4 text-right">
                                        <a href="<?php echo base_url('MaterialIssueController/index?str=All') ?>" class="btn btn-default btn-sm">Show All Issues</a>
                                        <a href="<?php echo base_url('MaterialIssueController/create_mrn') ?>" class="btn btn-warning btn-sm" style="margin-right: 5px;">
                                            <i class="fa fa-undo"></i> Create MRN
                                        </a>
                                        <a href="<?php echo base_url('MaterialIssueController/create') ?>" class="btn btn-success btn-sm">
                                            <i class="glyphicon glyphicon-plus"></i> Create Issue Slip
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="box-body">

                                

                                <!-- Filter Form -->
                                <div class="box box-default collapsed-box" id="filterBox">
                                    <div class="box-header with-border">
                                        <h3 class="box-title"><i class="fa fa-filter"></i> Filters</h3>
                                        <div class="box-tools pull-right">
                                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
                                        </div>
                                    </div>
                                    <div class="box-body" style="display:none;">
                                        <form method="get" action="<?php echo base_url('MaterialIssueController/index') ?>" class="form-horizontal" id="filterForm">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label class="control-label">From Date:</label>
                                                        <input type="text" name="date_from" class="form-control input-sm datepicker"
                                                            id="date_from"
                                                            value="<?= isset($filters['date_from']) ? date('d-m-Y', strtotime($filters['date_from'])) : '' ?>"
                                                            placeholder="DD-MM-YYYY">
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label class="control-label">To Date:</label>
                                                        <input type="text" name="date_to" class="form-control input-sm datepicker"
                                                            id="date_to"
                                                            value="<?= isset($filters['date_to']) ? date('d-m-Y', strtotime($filters['date_to'])) : '' ?>"
                                                            placeholder="DD-MM-YYYY">
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label class="control-label">Status:</label>
                                                        <select name="status" class="form-control input-sm">
                                                            <option value="">All</option>
                                                            <option value="draft"
                                                                <?= (isset($filters['status']) && $filters['status'] == 'draft') ? 'selected' : '' ?>>
                                                                Draft</option>
                                                            <option value="issued"
                                                                <?= (isset($filters['status']) && $filters['status'] == 'issued') ? 'selected' : '' ?>>
                                                                Issued</option>
                                                            <option value="cancelled"
                                                                <?= (isset($filters['status']) && $filters['status'] == 'cancelled') ? 'selected' : '' ?>>
                                                                Cancelled</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label class="control-label">Department:</label>
                                                        <select name="department" class="form-control input-sm">
                                                            <option value="">All</option>
                                                            <?php if (!empty($departments)): ?>
                                                                <?php foreach ($departments as $dept): ?>
                                                                <option value="<?= $dept ?>"
                                                                    <?= (isset($filters['department']) && $filters['department'] == $dept) ? 'selected' : '' ?>>
                                                                    <?= $dept ?>
                                                                </option>
                                                                <?php endforeach; ?>
                                                            <?php endif; ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label class="control-label">Issued To:</label>
                                                        <select name="issued_to" class="form-control input-sm">
                                                            <option value="">All</option>
                                                            <?php if (!empty($users)): ?>
                                                                <?php foreach ($users as $user): ?>
                                                                <option value="<?= $user ?>"
                                                                    <?= (isset($filters['issued_to']) && $filters['issued_to'] == $user) ? 'selected' : '' ?>>
                                                                    <?= $user ?>
                                                                </option>
                                                                <?php endforeach; ?>
                                                            <?php endif; ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                 <?php if ($_has_project_master): ?>
                                                 <div class="col-md-3">
                                                     <div class="form-group">
                                                         <label class="control-label">Project Code:</label>
                                                         <select name="project_code" class="form-control input-sm">
                                                             <option value="">All</option>
                                                             <?php if (!empty($projects)): ?>
                                                                 <?php foreach ($projects as $project): ?>
                                                                 <option value="<?= $project->code ?>"
                                                                     <?= (isset($filters['project_code']) && $filters['project_code'] == $project->code) ? 'selected' : '' ?>>
                                                                     <?= $project->code ?> - <?= $project->name ?>
                                                                 </option>
                                                                 <?php endforeach; ?>
                                                             <?php endif; ?>
                                                         </select>
                                                     </div>
                                                 </div>
                                                 <?php endif; ?>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>&nbsp;</label>
                                                        <div>
                                                            <button type="submit" class="btn btn-primary btn-sm">
                                                                <i class="fa fa-search"></i> Apply Filter
                                                            </button>
                                                            <a href="<?php echo base_url('MaterialIssueController/index') ?>" class="btn btn-default btn-sm">
                                                                <i class="fa fa-refresh"></i> Reset
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                <?php
                                $mis_slips = array();
                                $mrn_slips = array();
                                if (!empty($issue_slips)) {
                                    foreach ($issue_slips as $slip) {
                                        if (strpos($slip['issue_no'], 'MRN-') === 0) {
                                            $mrn_slips[] = $slip;
                                        } else {
                                            $mis_slips[] = $slip;
                                        }
                                    }
                                }
                                ?>

                                <!-- Nav tabs -->
                                <ul class="nav nav-tabs" role="tablist" style="margin-bottom: 20px;">
                                    <li role="presentation" class="active">
                                        <a href="#tab_mis" aria-controls="tab_mis" role="tab" data-toggle="tab">
                                            <strong>Material Issues (MIS)</strong>
                                            <span class="label label-primary"><?= count($mis_slips) ?></span>
                                        </a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#tab_mrn" aria-controls="tab_mrn" role="tab" data-toggle="tab">
                                            <strong>Material Returns (MRN)</strong>
                                            <span class="label" style="background-color: #ea580c;"><?= count($mrn_slips) ?></span>
                                        </a>
                                    </li>
                                </ul>

                                <!-- Tab panes -->
                                <div class="tab-content">
                                    <!-- MIS Tab -->
                                    <div role="tabpanel" class="tab-pane active" id="tab_mis">
                                        <table id="tableMIS" class="table table-bordered table-striped" style="width: 100% !important; min-width: <?php echo $_total_table_width; ?>px !important; table-layout: fixed !important;">
                                            <thead>
                                                <tr>
                                                    <th style="width: <?php echo $_col_sr_no; ?>; text-align: center; white-space: nowrap;">Sr.No.</th>
                                                    <th style="width: <?php echo $_col_issue_no; ?>; white-space: nowrap;">Issue No.</th>
                                                    <th style="width: <?php echo $_col_date; ?>; white-space: nowrap;">Date</th>
                                                    <th style="width: <?php echo $_col_issued_to; ?>;">Issued To</th>
                                                    <th style="width: <?php echo $_col_dept; ?>;">Department</th>
                                                    <th style="width: <?php echo $_col_items; ?>; text-align: center;">Items</th>
                                                    <th style="width: <?php echo $_col_qty; ?>; text-align: right; white-space: nowrap;">Total Qty</th>
                                                    <th style="width: <?php echo $_col_status; ?>; text-align: center; white-space: nowrap;">Status</th>
                                                    <th style="width: <?php echo $_col_actions; ?>; text-align: center; white-space: nowrap;">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (!empty($mis_slips)): ?>
                                                <?php $i = 1; ?>
                                                <?php foreach ($mis_slips as $slip): ?>
                                                <tr>
                                                    <td style="text-align: center;"><?= $i; ?></td>
                                                    <td><strong><?= $slip['issue_no'] ?></strong></td>
                                                    <td><?= date('d-m-Y', strtotime($slip['issue_date'])) ?></td>
                                                    <td><?= htmlspecialchars($slip['issued_to'] ?? '') ?></td>
                                                    <td><?= htmlspecialchars($slip['department'] ?? '') ?></td>
                                                    <td style="text-align: center;"><?= $slip['total_items'] ?></td>
                                                    <td style="text-align: right; font-weight: 600;"><?= number_format($slip['total_qty'], 2) ?></td>
                                                    <td style="text-align: center;">
                                                        <?php if ($slip['status'] == 'draft'): ?>
                                                            <span style="background-color: #fef7e0; color: #b06000; border: 1px solid #fbe09c; padding: 3px 8px; border-radius: 4px; font-weight: 600; font-size: 11px; text-transform: uppercase; display: inline-block; line-height: 1.2;">Draft</span>
                                                        <?php elseif ($slip['status'] == 'issued'): ?>
                                                            <span style="background-color: #e6f4ea; color: #137333; border: 1px solid #c2e7c9; padding: 3px 8px; border-radius: 4px; font-weight: 600; font-size: 11px; text-transform: uppercase; display: inline-block; line-height: 1.2;">Issued</span>
                                                        <?php elseif ($slip['status'] == 'cancelled'): ?>
                                                            <span style="background-color: #fce8e6; color: #c5221f; border: 1px solid #fad2cf; padding: 3px 8px; border-radius: 4px; font-weight: 600; font-size: 11px; text-transform: uppercase; display: inline-block; line-height: 1.2;">Cancelled</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td style="text-align: center; vertical-align: middle;">
                                                        <div class="dropdown">
                                                            <button class="btn btn-primary btn-xs dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="padding: 2px 6px; font-size: 10px; line-height: 1.2; height: 22px; width: 26px; display: inline-flex; align-items: center; justify-content: center; border-radius: 3px;">
                                                                <span class="caret" style="margin: 0;"></span>
                                                            </button>
                                                            <ul class="dropdown-menu pull-right" role="menu">
                                                                <li><a href="<?= base_url('MaterialIssueController/view/' . $slip['issue_id']) ?>" target="_blank">View Details</a></li>
                                                                <?php if ($slip['status'] == 'draft'): ?>
                                                                    <li><a href="<?= base_url('MaterialIssueController/print_slip/' . $slip['issue_id']) ?>" target="_blank">Print Slip</a></li>
                                                                    <li role="separator" class="divider"></li>
                                                                    <li><a href="<?= base_url('MaterialIssueController/approve/' . $slip['issue_id']) ?>" onclick="return confirm('Approve this issue slip? This will mark it as Issued.')"><strong style="color:green;">&#10003; Approve / Issue</strong></a></li>
                                                                    <li><a href="#" class="cancel-link" data-id="<?= $slip['issue_id'] ?>">Cancel</a></li>
                                                                <?php endif; ?>
                                                                <li role="separator" class="divider"></li>
                                                                <li><a href="#" class="delete-link" data-id="<?= $slip['issue_id'] ?>" onclick="return confirm('Are you sure you want to delete this issue slip?')">Delete</a></li>
                                                            </ul>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <?php $i++; ?>
                                                <?php endforeach; ?>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- MRN Tab -->
                                    <div role="tabpanel" class="tab-pane" id="tab_mrn">
                                        <table id="tableMRN" class="table table-bordered table-striped" style="width: 100% !important; min-width: <?php echo $_total_table_width; ?>px !important; table-layout: fixed !important;">
                                            <thead>
                                                <tr>
                                                    <th style="width: <?php echo $_col_sr_no; ?>; text-align: center; white-space: nowrap;">Sr.No.</th>
                                                    <th style="width: <?php echo $_col_issue_no; ?>; white-space: nowrap;">Return No.</th>
                                                    <th style="width: <?php echo $_col_date; ?>; white-space: nowrap;">Date</th>
                                                    <th style="width: <?php echo $_col_issued_to; ?>;">Returned By</th>
                                                    <th style="width: <?php echo $_col_dept; ?>;">Department</th>
                                                    <th style="width: <?php echo $_col_items; ?>; text-align: center;">Items</th>
                                                    <th style="width: <?php echo $_col_qty; ?>; text-align: right; white-space: nowrap;">Returned Qty</th>
                                                    <th style="width: <?php echo $_col_status; ?>; text-align: center; white-space: nowrap;">Status</th>
                                                    <th style="width: <?php echo $_col_actions; ?>; text-align: center; white-space: nowrap;">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (!empty($mrn_slips)): ?>
                                                <?php $i = 1; ?>
                                                <?php foreach ($mrn_slips as $slip): ?>
                                                <tr>
                                                    <td style="text-align: center;"><?= $i; ?></td>
                                                    <td><strong><span style="color:#ea580c;"><i class="fa fa-undo"></i> <?= $slip['issue_no'] ?></span></strong></td>
                                                    <td><?= date('d-m-Y', strtotime($slip['issue_date'])) ?></td>
                                                    <td><?= htmlspecialchars($slip['issued_to'] ?? '') ?></td>
                                                    <td><?= htmlspecialchars($slip['department'] ?? '') ?></td>
                                                    <td style="text-align: center;"><?= $slip['total_items'] ?></td>
                                                    <td style="text-align: right; font-weight: 600; color:#ea580c;">-<?= number_format(abs($slip['total_qty']), 2) ?></td>
                                                    <td style="text-align: center;">
                                                        <span style="background-color: #fef7e0; color: #b06000; border: 1px solid #fbe09c; padding: 3px 8px; border-radius: 4px; font-weight: 600; font-size: 11px; text-transform: uppercase; display: inline-block; line-height: 1.2;">Returned</span>
                                                    </td>
                                                    <td style="text-align: center; vertical-align: middle;">
                                                        <div class="dropdown">
                                                            <button class="btn btn-primary btn-xs dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="padding: 2px 6px; font-size: 10px; line-height: 1.2; height: 22px; width: 26px; display: inline-flex; align-items: center; justify-content: center; border-radius: 3px;">
                                                                <span class="caret" style="margin: 0;"></span>
                                                            </button>
                                                            <ul class="dropdown-menu pull-right" role="menu">
                                                                <li><a href="<?= base_url('MaterialIssueController/view/' . $slip['issue_id']) ?>" target="_blank">View Details</a></li>
                                                                <li role="separator" class="divider"></li>
                                                                <li><a href="#" class="delete-link" data-id="<?= $slip['issue_id'] ?>" onclick="return confirm('Are you sure you want to delete this return note?')">Delete</a></li>
                                                            </ul>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <?php $i++; ?>
                                                <?php endforeach; ?>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
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
        <?php $this->load->view('admin/footer'); ?>
        <div class="control-sidebar-bg"></div>
    </div>
    <!-- ./wrapper -->

    <!-- Cancel Modal -->
    <div class="modal fade" id="cancelModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Cancel Issue Slip</h4>
                </div>
                <form id="cancelForm" method="post">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Reason for Cancellation</label>
                            <textarea name="cancel_remarks" class="form-control" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-danger">Cancel Issue Slip</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    $(document).ready(function() {
        // Check if any filters are active
        var hasActiveFilters = false;
        <?php if (!empty($filters)): ?>
            hasActiveFilters = true;
        <?php endif; ?>

        // If filters are active, expand the filter box
        if (hasActiveFilters) {
            $('#filterBox').removeClass('collapsed-box');
            $('#filterBox .box-body').show();
            $('#filterBox .btn-box-tool i').removeClass('fa-plus').addClass('fa-minus');
        }

        // Filter box toggle
        $('#filterBox').on('click', '.btn-box-tool', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var box = $('#filterBox');
            box.toggleClass('collapsed-box');
            box.find('.box-body').first().slideToggle();
            
            var icon = $(this).find('i');
            if (box.hasClass('collapsed-box')) {
                icon.removeClass('fa-minus').addClass('fa-plus');
            } else {
                icon.removeClass('fa-plus').addClass('fa-minus');
            }
        });

        // Initialize datepickers for filter inputs with DMY format
        $('.datepicker').datepicker({
            format: 'dd-mm-yyyy',
            autoclose: true,
            todayHighlight: true,
            orientation: 'bottom'
        });

        // DataTable initialization for both tables
        var dtConfigMIS = {
            "order": [[1, "desc"]],
            "pageLength": 25,
            "autoWidth": false,
            "ordering": true,
            "columnDefs": [{
                "targets": [0, -1],
                "orderable": false,
                "searchable": false
            }],
            "dom": "<'row'<'col-sm-6'l><'col-sm-6 text-right'f>>" +
                   "<'row'<'col-sm-12' <'custom-table-scroll' t > r >>" +
                   "<'row'<'col-sm-5'i><'col-sm-7'p>>",
            "language": {
                "emptyTable": "No records found",
                "search": "Search Material Issue Slips Filters:"
            }
        };

        var dtConfigMRN = {
            "order": [[1, "desc"]],
            "pageLength": 25,
            "autoWidth": false,
            "ordering": true,
            "columnDefs": [{
                "targets": [0, -1],
                "orderable": false,
                "searchable": false
            }],
            "dom": "<'row'<'col-sm-6'l><'col-sm-6 text-right'f>>" +
                   "<'row'<'col-sm-12' <'custom-table-scroll' t > r >>" +
                   "<'row'<'col-sm-5'i><'col-sm-7'p>>",
            "language": {
                "emptyTable": "No records found",
                "search": "Search Material Issue Slips Filters:"
            }
        };

        if (!$.fn.DataTable.isDataTable('#tableMIS')) {
            $('#tableMIS').DataTable(dtConfigMIS);
        }
        if (!$.fn.DataTable.isDataTable('#tableMRN')) {
            $('#tableMRN').DataTable(dtConfigMRN);
        }

        // Adjust DataTables when switching tabs
        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
        });

        // Cancel link click
        $('.cancel-link').click(function(e) {
            e.preventDefault();
            var issueId = $(this).data('id');
            $('#cancelForm').attr('action', '<?= base_url("MaterialIssueController/cancel/") ?>' +
                issueId);
            $('#cancelModal').modal('show');
        });

        // Delete link click
        $('.delete-link').click(function(e) {
            e.preventDefault();
            var issueId = $(this).data('id');
            window.location.href = '<?= base_url("MaterialIssueController/delete/") ?>' + issueId;
        });
    });
    </script>
</body>