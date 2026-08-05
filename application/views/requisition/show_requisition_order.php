<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (isset($session_data_head1)) {
} else {
    header($this->config->item('header'));
}
defined('BASEPATH') or exit('No direct script access allowed');
$_has_project_master = isset($session_data_head1['permission']) && in_array('Projects', $session_data_head1['permission']);
?>

<body class="hold-transition skin-blue sidebar-mini">
    <div id="loader" class="center"></div>
    <div class="wrapper">

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <div class="row">
                    <div class="col-md-8">
                        <h1>
                            <i class="fa fa-file-text-o"></i> Purchase Requisition Details
                        </h1>
                        <ol class="breadcrumb" style="background: transparent; padding: 0; margin-top: 5px;">
                            <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                            <li><a href="<?php echo base_url() . 'SupplierController/view_purchase_order/' ?>"><i class="fa fa-shopping-cart"></i> Purchase Requisitions</a></li>
                            <li class="active"><i class="fa fa-eye"></i> View Requisition</li>
                        </ol>
                    </div>
                    <div class="col-md-4">
                        <div class="pull-right">
                            <span class="po-number-display">
                                <strong>PR Number:</strong>
                                <span class="badge bg-blue" style="font-size: 16px; padding: 5px 12px;">
                                    <?php echo $requisition->pr_no; ?>
                                </span>
                            </span>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Main content -->
            <section class="content">
                <!-- Action Bar -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="box box-solid" style="border-top: 3px solid #3c8dbc; margin-bottom: 20px;">
                            <div class="box-body">
                                <div class="action-buttons" style="display: flex; flex-wrap: wrap; gap: 10px; align-items: center;">
                                    <a href="<?php echo base_url(); ?>RequisitionController/edit_requisition/<?php echo $requisition->pr_id; ?>"
                                        class="btn btn-primary btn-lg btn-flat">
                                        <i class="fa fa-edit"></i> Edit
                                    </a>

                                    <?php if (strtolower($requisition->approval_status) === 'approved'): ?>
                                        <a href="<?php echo base_url(); ?>RequisitionController/convert_pr_to_po/<?php echo $requisition->pr_id; ?>"
                                            class="btn btn-success btn-lg btn-flat" style="background: linear-gradient(135deg, #10b981, #059669); color: white; border: none;">
                                            <i class="fa fa-shopping-cart"></i> Convert to PO
                                        </a>
                                        <a href="<?php echo base_url(); ?>RFQController/convert_to_rfq/<?php echo $requisition->pr_id; ?>"
                                            class="btn btn-warning btn-lg btn-flat" style="background: linear-gradient(135deg, #f59e0b, #d97706); color: white; border: none;">
                                            <i class="fa fa-exchange"></i> Convert to RFQ
                                        </a>
                                    <?php endif; ?>

                                    <a id="exportpdf"
                                        href="<?php echo base_url(); ?>Pdf/download_requisition/<?php echo $requisition->pr_id; ?>"
                                        class="btn btn-info btn-lg btn-flat" target="_blank">
                                        <i class="fa fa-file-pdf-o"></i> Export As PDF
                                    </a>

                                    <a href="<?php echo base_url(); ?>RequisitionController/view_requisition_order?str=All"
                                        class="btn btn-default btn-lg btn-flat">
                                        <i class="fa fa-list"></i> All Requisitions
                                    </a>

                                    <a href="<?php echo base_url(); ?>RequisitionController/create_purchase_requisition"
                                        class="btn btn-success btn-lg btn-flat">
                                        <i class="fa fa-plus"></i> Create New
                                    </a>

                                    <button onclick="window.history.back()" class="btn btn-default btn-lg btn-flat pull-right">
                                        <i class="fa fa-close"></i> Close
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Card -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="box box-primary">
                            <div class="box-header with-border" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                <h3 class="box-title" style="color: white;">
                                    <i class="fa fa-file-text-o"></i> Purchase Requisition Details
                                </h3>
                            </div>

                            <div class="box-body" style="padding: 20px;">
                                <!-- Header Section -->
                                <div class="row" style="margin-bottom: 20px;">
                                    <div class="col-md-6">
                                        <div class="company-details-card">
                                            <div class="card-header">
                                                <h4><i class="fa fa-building"></i> Company Details</h4>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <img src="<?php echo base_url() . $settings['company_logo'] ?>"
                                                            class="img-responsive img-thumbnail"
                                                            style="max-height: 120px; border: none;">
                                                    </div>
                                                    <div class="col-md-8">
                                                        <h3 style="color: #3c8dbc; margin-top: 0;">
                                                            <strong><?php echo $settings['company_name']; ?></strong>
                                                        </h3>
                                                        <div class="company-info">
                                                            <p><i class="fa fa-map-marker text-primary"></i>
                                                                <span><?php echo $settings['address']; ?></span>
                                                            </p>
                                                            <p><i class="fa fa-id-card text-success"></i>
                                                                <span>GST: <?php echo $settings['company_gst']; ?></span>
                                                            </p>
                                                            <p><i class="fa fa-credit-card text-warning"></i>
                                                                <span>PAN: <?php echo $settings['company_pan']; ?></span>
                                                            </p>
                                                            <p><i class="fa fa-phone text-info"></i>
                                                                <span><?php echo $settings['mobile']; ?></span>
                                                            </p>
                                                            <p><i class="fa fa-envelope text-danger"></i>
                                                                <span><?php echo $settings['email']; ?></span>
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="requisition-info-card">
                                            <div class="card-header">
                                                <h4><i class="fa fa-info-circle"></i> Requisition Information</h4>
                                            </div>
                                            <div class="card-body">
                                                <div class="info-grid">
                                                    <div class="info-item">
                                                        <span class="info-label">PR Number:</span>
                                                        <span class="info-value badge bg-blue"><?php echo $requisition->pr_no; ?></span>
                                                    </div>
                                                    <div class="info-item">
                                                        <span class="info-label">Department:</span>
                                                        <span class="info-value">
                                                            <?php
                                                            if (!empty($department_result)) {
                                                                foreach ($department_result as $dept) {
                                                                    if ($dept->department_id == $requisition->department_id_fk) {
                                                                        echo $dept->department_name;
                                                                        break;
                                                                    }
                                                                }
                                                            }
                                                            ?>
                                                        </span>
                                                    </div>
                                                    <div class="info-item">
                                                        <span class="info-label">Requested By:</span>
                                                        <span class="info-value">
                                                            <?php
                                                            if (!empty($users)) {
                                                                foreach ($users as $user) {
                                                                    if ($user->user_id == $requisition->requested_by) {
                                                                        echo htmlspecialchars($user->username);
                                                                        break;
                                                                    }
                                                                }
                                                            }
                                                            ?>
                                                        </span>
                                                    </div>
                                                    <div class="info-item">
                                                        <span class="info-label">Urgency Level:</span>
                                                        <span class="info-value">
                                                            <?php
                                                            $urgency = isset($requisition->urgency_level) ? $requisition->urgency_level : 'N/A';
                                                            $urgency_class = '';
                                                            switch (strtolower($urgency)) {
                                                                case 'high':
                                                                    $urgency_class = 'danger';
                                                                    break;
                                                                case 'medium':
                                                                    $urgency_class = 'warning';
                                                                    break;
                                                                case 'low':
                                                                    $urgency_class = 'success';
                                                                    break;
                                                                default:
                                                                    $urgency_class = 'default';
                                                            }
                                                            ?>
                                                            <span class="label label-<?php echo $urgency_class; ?>">
                                                                <?php echo $urgency; ?>
                                                            </span>
                                                        </span>
                                                    </div>
                                                    <div class="info-item">
                                                        <span class="info-label">Requisition Date:</span>
                                                        <span class="info-value">
                                                            <?php echo !empty($requisition->pr_date) ? date('d-m-Y', strtotime($requisition->pr_date)) : 'N/A'; ?>
                                                        </span>
                                                    </div>
                                                    <div class="info-item">
                                                        <span class="info-label">Required Date:</span>
                                                        <span class="info-value">
                                                            <?php echo !empty($requisition->required_date) ? date('d-m-Y', strtotime($requisition->required_date)) : 'N/A'; ?>
                                                        </span>
                                                    </div>
                                                    <div class="info-item">
                                                        <span class="info-label">Approval Status:</span>
                                                        <span class="info-value">
                                                            <?php
                                                            $status = isset($requisition->approval_status) ? $requisition->approval_status : 'N/A';
                                                            $status_class = '';
                                                            switch (strtolower($status)) {
                                                                case 'approved':
                                                                    $status_class = 'success';
                                                                    break;
                                                                case 'pending':
                                                                    $status_class = 'warning';
                                                                    break;
                                                                case 'rejected':
                                                                    $status_class = 'danger';
                                                                    break;
                                                                default:
                                                                    $status_class = 'default';
                                                            }
                                                            ?>
                                                            <span class="label label-<?php echo $status_class; ?>">
                                                                <?php echo $status; ?>
                                                            </span>
                                                        </span>
                                                    </div>
                                                     <?php if ($_has_project_master): ?>
                                                     <div class="info-item">
                                                         <span class="info-label">Project Code:</span>
                                                         <span class="info-value"><?php echo isset($requisition->project_code) ? htmlspecialchars($requisition->project_code) : 'N/A'; ?></span>
                                                     </div>
                                                     <?php endif; ?>
                                                    <?php if (isset($requisition->so_no) && isset($requisition->oc_no) && !empty($requisition->so_no) && $requisition->so_no === $requisition->oc_no): ?>
                                                     <div class="info-item">
                                                         <span class="info-label">SO:</span>
                                                         <span class="info-value"><?php echo htmlspecialchars($requisition->so_no); ?></span>
                                                     </div>
                                                     <?php else: ?>
                                                     <div class="info-item">
                                                         <span class="info-label">Sales Order:</span>
                                                         <span class="info-value"><?php echo isset($requisition->so_no) ? htmlspecialchars($requisition->so_no) : 'N/A'; ?></span>
                                                     </div>
                                                     <div class="info-item">
                                                         <span class="info-label">OC Number:</span>
                                                         <span class="info-value"><?php echo isset($requisition->oc_no) ? htmlspecialchars($requisition->oc_no) : 'N/A'; ?></span>
                                                     </div>
                                                     <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Remarks Section -->
                                <?php if (!empty($requisition->remarks)): ?>
                                    <div class="row" style="margin-bottom: 20px;">
                                        <div class="col-md-12">
                                            <div class="box box-default">
                                                <div class="box-header with-border">
                                                    <h3 class="box-title"><i class="fa fa-comment"></i> Remarks</h3>
                                                </div>
                                                <div class="box-body">
                                                    <div class="well" style="background: #f8f9fa; border-left: 4px solid #3c8dbc;">
                                                        <?php echo nl2br($requisition->remarks); ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <!-- Items Table -->
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="box box-solid">
                                            <div class="box-header with-border" style="background: #f5f5f5;">
                                                <h3 class="box-title"><i class="fa fa-list"></i> Requisition Items</h3>
                                            </div>
                                            <div class="box-body">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-hover table-striped">
                                                        <thead style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                                                            <tr>
                                                                <th width="5%" class="text-center">#</th>
                                                                <th width="15%">Item Code</th>
                                                                <th width="20%">Description</th>
                                                                <th width="10%">HSN Code</th>
                                                                <th width="8%" class="text-center">Quantity</th>
                                                                <th width="8%" class="text-center">Unit</th>
                                                                <th width="12%" class="text-right">Estimated Cost</th>
                                                                <th width="22%">Specification</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                            $i = 1;
                                                            $total_estimated_cost = 0;
                                                            if (!empty($requisition_items)) {
                                                                foreach ($requisition_items as $item) {
                                                                    $total_estimated_cost += isset($item->estimated_cost) ? $item->estimated_cost : 0;
                                                            ?>
                                                                    <tr>
                                                                        <td class="text-center"><?php echo $i; ?></td>
                                                                        <td>
                                                                            <strong><?php echo isset($item->item_code) ? $item->item_code : ''; ?></strong>
                                                                        </td>
                                                                        <td>
                                                                            <?php echo isset($item->description) ? nl2br($item->description) : ''; ?>
                                                                        </td>
                                                                        <td class="text-center">
                                                                            <span class="label label-default"><?php echo isset($item->hsn) ? $item->hsn : 'N/A'; ?></span>
                                                                        </td>
                                                                        <td class="text-center">
                                                                            <span class="badge bg-blue"><?php echo isset($item->quantity) ? $item->quantity : ''; ?></span>
                                                                        </td>
                                                                        <td class="text-center">
                                                                            <?php echo isset($item->unit) ? $item->unit : ''; ?>
                                                                        </td>
                                                                        <td class="text-right">
                                                                            <strong>₹<?php echo isset($item->estimated_cost) ? number_format($item->estimated_cost, 2) : '0.00'; ?></strong>
                                                                        </td>
                                                                        <td>
                                                                            <small class="text-muted"><?php echo isset($item->specification) ? nl2br($item->specification) : ''; ?></small>
                                                                        </td>
                                                                    </tr>
                                                            <?php
                                                                    $i++;
                                                                }
                                                            } else {
                                                                echo '<tr><td colspan="8" class="text-center"><div class="alert alert-info">No items found in this requisition</div></td></tr>';
                                                            }
                                                            ?>
                                                        </tbody>
                                                        <?php if (!empty($requisition_items)): ?>
                                                            <tfoot style="background: #f8f9fa;">
                                                                <tr>
                                                                    <td colspan="6" class="text-right">
                                                                        <strong>Total Estimated Cost:</strong>
                                                                    </td>
                                                                    <td class="text-right">
                                                                        <h4 style="margin: 0; color: #3c8dbc;">
                                                                            <strong>₹<?php echo number_format($total_estimated_cost, 2); ?></strong>
                                                                        </h4>
                                                                    </td>
                                                                    <td></td>
                                                                </tr>
                                                            </tfoot>
                                                        <?php endif; ?>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Terms and Conditions -->
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="box box-default">
                                            <div class="box-header with-border">
                                                <h3 class="box-title"><i class="fa fa-file-text"></i> Terms & Conditions</h3>
                                            </div>
                                            <div class="box-body">
                                                <div class="well" style="background: #f8f9fa; min-height: 150px;">
                                                    <?php echo $settings['purchase_requisition_notes']; ?>
                                                </div>
                                                <div class="row" style="margin-top: 30px;">
                                                    <div class="col-md-6">
                                                        <div class="text-center">
                                                            <h4><strong>Receiver's Signatory</strong></h4>
                                                            <div style="height: 1px; background: #333; width: 200px; margin: 10px auto;"></div>
                                                            <p>Name, Designation & Signature</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="text-center">
                                                            <h4><strong>Authorized Signatory</strong></h4>
                                                            <div style="height: 1px; background: #333; width: 200px; margin: 10px auto;"></div>
                                                            <p>For <?php echo $settings['company_name']; ?></p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="text-center text-muted" style="margin-top: 30px; padding-top: 15px; border-top: 1px dashed #ddd;">
                                                    <i class="fa fa-info-circle"></i> This is Computer Generated Purchase Requisition
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <!-- /.row -->
        <?php $this->load->view('admin/footer'); ?>
        <div class="control-sidebar-bg"></div>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    <style>
        /* Custom Card Styles */
        .company-details-card,
        .requisition-info-card {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            overflow: hidden;
        }

        .card-header {
            background: #f8f9fa;
            padding: 15px 20px;
            border-bottom: 1px solid #e0e0e0;
        }

        .card-header h4 {
            margin: 0;
            color: #333;
            font-size: 16px;
            font-weight: 600;
        }

        .card-header h4 i {
            margin-right: 10px;
            color: #3c8dbc;
        }

        .card-body {
            padding: 20px;
        }

        .company-info p {
            margin-bottom: 8px;
            font-size: 14px;
        }

        .company-info i {
            width: 20px;
            text-align: center;
            margin-right: 8px;
        }

        /* Info Grid */
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 15px;
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px dashed #eee;
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: 600;
            color: #555;
            font-size: 14px;
        }

        .info-value {
            text-align: right;
            color: #333;
            font-size: 14px;
        }

        /* Button Styling */
        .btn-lg.btn-flat {
            padding: 8px 20px;
            font-size: 14px;
            border-radius: 4px;
        }

        .btn-flat {
            border: none;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .btn-flat:hover {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
            transform: translateY(-1px);
        }

        /* Table Improvements */
        .table-hover tbody tr:hover {
            background-color: #f5f5f5;
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #f9f9f9;
        }

        /* Status Badges */
        .label-lg {
            padding: 8px 15px;
            font-size: 14px;
            border-radius: 4px;
        }

        /* Animation */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .box-primary {
            animation: fadeIn 0.5s ease-out;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .info-grid {
                grid-template-columns: 1fr;
            }

            .btn-lg.btn-flat {
                width: 100%;
                margin-bottom: 10px;
            }

            .action-buttons {
                flex-direction: column;
            }

            .action-buttons .btn {
                width: 100%;
            }
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
    </style>

    <script>
        $(document).ready(function() {
            // Add fade in effect to table rows
            $('table tbody tr').each(function(i) {
                $(this).delay(i * 50).animate({
                    opacity: 1
                }, 200);
            });

            // Smooth scroll to top
            $('a[href="#"]').on('click', function(e) {
                e.preventDefault();
                $('html, body').animate({
                    scrollTop: 0
                }, 500);
            });

            // Print button functionality
            $('.print-btn').on('click', function() {
                window.print();
            });
        });
    </script>
</body>