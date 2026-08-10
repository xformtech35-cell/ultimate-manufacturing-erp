<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (isset($session_data_head1)) {
} else {
    header($this->config->item('header'));
}

$_has_project_master = isset($session_data_head1['permission']) && in_array('Projects', $session_data_head1['permission']);
$session_data_head2 = $this->session->userdata('session_data_head2');
$set_cc_email = $session_data_head2['cc_email'];
defined('BASEPATH') or exit('No direct script access allowed');

// Get selected month-year if exists
$selected_month_year = isset($selected_month_year) ? $selected_month_year : date('m-Y');
?>

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

    /* Disabled locked dropdown item */
    .dropdown-item.disabled-lock {
        display: block;
        padding: 10px 20px;
        color: #a94442;
        /* Dark red text */
        background-color: #f8d7da;
        /* Light red background */
        cursor: not-allowed;
        border-radius: 4px;
        font-weight: 500;
        text-align: left;
        margin: 2px 0;
    }

    /* Hover effect stays the same but clearly disabled */
    .dropdown-item.disabled-lock:hover {
        background-color: #f8d7da;
        /* Keep light red on hover */
        color: #a94442;
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
    .po-actions-wrapper {
        display: inline-flex !important;
        flex-wrap: wrap !important;
        gap: 8px !important;
        justify-content: flex-end !important;
        align-items: center !important;
        width: 100%;
    }
    @media (max-width: 991px) {
        .po-actions-wrapper {
            justify-content: center !important;
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

    #view_purchase_order > thead > tr > th, 
    #view_purchase_order > tbody > tr > td {
        white-space: nowrap !important;
        vertical-align: middle !important;
    }
    #view_purchase_order > thead > tr > th {
        background-color: #3c8dbc;
        color: #ffffff;
        font-weight: 600;
    }

    /* DataTables Controls & Label Visibility Fixes */
    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_length label,
    .dataTables_wrapper .dataTables_filter,
    .dataTables_wrapper .dataTables_filter label,
    .dataTables_wrapper .dataTables_info,
    .dataTables_wrapper .dataTables_paginate {
        color: #333333 !important;
        font-weight: 600 !important;
    }

    .dataTables_wrapper .dataTables_filter input,
    .dataTables_wrapper .dataTables_length select {
        color: #333333 !important;
        background-color: #ffffff !important;
        border: 1px solid #cbd5e1 !important;
        border-radius: 4px !important;
        padding: 4px 8px !important;
    }

    @media (max-width: 991px) {
        .text-right-custom {
            justify-content: center !important;
            width: 100% !important;
            margin-top: 10px !important;
        }
    }
</style>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <div class="row">
                    <div class="col-md-8">
                        <h1>
                            <i class="fa fa-shopping-cart"></i> Purchase Orders
                        </h1>
                    </div>
                    <div class="col-md-4">
                        <ol class="breadcrumb pull-right">
                            <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                            <li><a href="#"><i class="fa fa-shopping-cart"></i> Purchase</a></li>
                            <li class="active"><i class="fa fa-list"></i> Purchase Orders</li>
                        </ol>
                    </div>
                </div>
            </section>

            <!-- Main content -->
            <section class="content">
              

                <!-- Purchase Orders Table -->
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-info">
                            <div class="box-header with-border" style="padding: 12px 15px; display: block !important; clear: both !important;">
                                <div style="float: left !important; display: inline-block;">
                                    <h3 class="box-title" style="float: left !important; font-weight: 600; margin: 0; font-size: 18px; color: #1e293b; display: inline-flex; align-items: center; gap: 8px; line-height: 30px;">
                                        <i class="fa fa-table" style="color: #3b82f6;"></i> Purchase Orders List
                                    </h3>
                                </div>
                                <div style="float: right !important; display: flex; align-items: center; justify-content: flex-end; flex-wrap: wrap; gap: 10px;">
                                    <form action="<?php echo base_url(); ?>SupplierController/view_purchase_order" method="post" class="form-inline" style="margin: 0; display: inline-block;">
                                        <div class="input-group input-group-sm" style="width: 250px; display: table;">
                                            <span class="input-group-addon" style="height: 30px; padding: 5px 10px; border-radius: 4px 0 0 4px !important;"><i class="fa fa-calendar"></i></span>
                                            <input type="text" class="form-control month-year-picker input-sm" name="month_year" id="month_year" value="<?php echo htmlspecialchars($selected_month_year); ?>" autocomplete="off" required placeholder="Select Month" style="height: 30px; border-radius: 0 !important;">
                                            <span class="input-group-btn" style="width: 1%;">
                                                <button class="btn btn-primary btn-sm btn-flat" name="submit" value="" type="submit" style="height: 30px; padding: 5px 15px; border-radius: 0 4px 4px 0 !important; font-weight: 600; border: none;">Filter</button>
                                            </span>
                                        </div>
                                    </form>
                                    <a href="<?php echo base_url(); ?>SupplierController/view_purchase_order?str=All" class="btn btn-success btn-sm" style="height: 30px; line-height: 20px; font-weight: 600; padding: 5px 15px; border-radius: 4px; display: inline-flex; align-items: center; gap: 6px; border: none;">
                                        <i class="fa fa-list"></i> Show All
                                    </a>
                                    <a href="<?php echo base_url(); ?>SupplierController/create_purchase_order" class="btn btn-primary btn-sm" style="height: 30px; line-height: 20px; font-weight: 600; padding: 5px 15px; border-radius: 4px; display: inline-flex; align-items: center; gap: 6px; border: none;">
                                        <i class="fa fa-plus"></i> Create New PO
                                    </a>
                                </div>
                                <div style="clear: both !important;"></div>
                            </div>
                            <!-- /.box-header -->
                              <div class="box-body no-padding">
                                  <table id="view_purchase_order" class="table table-hover table-striped" style="margin-bottom: 0;">
                                    <thead>
                                        <tr>
                                            <th width="5%">#</th>
                                            <th width="10%">Status</th>
                                            <th width="10%">PO Date</th>
                                            <th width="12%">Due Date</th>
                                            <th width="15%">PO Number</th>
                                            <?php if ($_has_project_master): ?>
                                            <th width="10%">Project Code</th>
                                            <?php endif; ?>
                                            <th width="10%">SO Number</th>
                                            <th width="15%">Supplier</th>
                                            <th width="10%">Vendor Code</th>
                                            <th width="8%">Type</th>
                                            <th width="10%">Amount</th>
                                            <th width="10%" class="hide">Balance</th>
                                            <th width="5%" class="hide">PDF</th>
                                            <th width="5%" class="hide">Approve</th>
                                            <th width="15%">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $unpaid = 0; ?>
                                        <?php $i = 1;
                                        foreach ($purchase_order as $key) {
                                            // Status colors
                                            $status_class = '';
                                            $status_text = '';
                                            switch ($key->status) {
                                                case 1:
                                                    $status_class = 'label-default';
                                                    $status_text = 'Draft';
                                                    break;
                                                case 2:
                                                    $status_class = 'label-info';
                                                    $status_text = 'Pending';
                                                    break;
                                                case 3:
                                                    $status_class = 'label-primary';
                                                    $status_text = 'Viewed';
                                                    break;
                                                case 4:
                                                    $status_class = 'label-success';
                                                    $status_text = 'Approved';
                                                    break;
                                                case 5:
                                                    $status_class = 'label-danger';
                                                    $status_text = 'Rejected';
                                                    break;
                                                case 6:
                                                    $status_class = 'label-warning';
                                                    $status_text = 'Cancelled';
                                                    break;
                                                case 7:
                                                    $status_class = 'label-success';
                                                    $status_text = 'Accepted';
                                                    break;
                                                default:
                                                    $status_class = 'label-default';
                                                    $status_text = 'Pending';
                                                    break;
                                            }

                                            // Due date status
                                            $balance_to_pay = $key->balance;
                                            $today = date('Y-m-d');
                                            $date = new DateTime($key->payment_due_date);
                                            $currentdate = new DateTime($today);
                                            $due_status_class = 'label-default';
                                            if (($date < $currentdate) && ($balance_to_pay != 0.00)) {
                                                $due_status_class = 'label-danger';
                                            } else if (($date >= $currentdate) && ($balance_to_pay != 0.00)) {
                                                $due_status_class = 'label-warning';
                                            } else if ($balance_to_pay == 0.00) {
                                                $due_status_class = 'label-success';
                                            }
                                        ?>
                                            <tr>
                                                <td><?php echo $i; ?></td>
                                                <td>
                                                    <span class="label <?php echo $status_class; ?>"><?php echo $status_text; ?></span>
                                                </td>
                                                <td>
                                                    <span class="text-muted"><i class="fa fa-calendar"></i> <?php echo date('d-m-Y', strtotime($key->purchase_date)); ?></span>
                                                </td>
                                                <td>
                                                    <span class="label <?php echo $due_status_class; ?>">
                                                        <i class="fa fa-clock-o"></i> <?php echo date('d-m-Y', strtotime($key->payment_due_date)); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="<?php echo base_url() . 'SupplierController/show_po/' . $key->number; ?>" class="text-primary">
                                                        <i class="fa fa-file-text-o"></i> <?php echo $key->number; ?>
                                                    </a>
                                                </td>
                                                <?php if ($_has_project_master): ?>
                                                <td>
                                                    <span class="label label-default"><?php echo isset($key->project_code) ? htmlspecialchars($key->project_code) : 'N/A'; ?></span>
                                                </td>
                                                <?php endif; ?>
                                                <td>
                                                    <span>
                                                    <?php 
                                                    if (!empty($key->so_no) && $key->so_no !== 'N/A' && $key->so_no !== '') {
                                                        echo htmlspecialchars($key->so_no);
                                                    } elseif (!empty($key->oc_no) && $key->oc_no !== 'N/A' && $key->oc_no !== '') {
                                                        echo htmlspecialchars($key->oc_no);
                                                    } else {
                                                        echo 'N/A';
                                                    }
                                                    ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="text-truncate" style="max-width: 150px;" title="<?php echo htmlspecialchars($key->company_name); ?>">
                                                        <i class="fa fa-building"></i> <?php echo $key->company_name; ?>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="text-muted"><?php echo $key->s_code ?? 'N/A'; ?></span>
                                                </td>
                                                <td>
                                                    <?php
                                                    $po_gst_type_label = 'CGST/ SGST';
                                                    $po_gst_type_class = 'label-info';
                                                    if ($key->gst_type === 'I') {
                                                        $po_gst_type_label = 'IGST';
                                                        $po_gst_type_class = 'label-primary';
                                                    } else if ($key->gst_type === 'N') {
                                                        $po_gst_type_label = 'NON GST';
                                                        $po_gst_type_class = 'label-default';
                                                    }
                                                    ?>
                                                    <span class="label <?php echo $po_gst_type_class; ?>">
                                                        <?php echo $po_gst_type_label; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <strong class="text-success">₹<?php require_once(APPPATH . '/third_party/amount_convert.php'); echo indian_number_format(round($key->total), 0); ?></strong>
                                                </td>
                                                <td class="hide">
                                                    <?php if ($key->balance > 0): ?>
                                                        <span class="text-danger"><strong>₹<?php echo indian_number_format(round($key->balance), 0); ?></strong></span>
                                                    <?php else: ?>
                                                        <span class="text-success"><strong>₹<?php echo indian_number_format(round($key->balance), 0); ?></strong></span>
                                                    <?php endif; ?>
                                                    <input type="hidden" id="get_purchase_no<?php echo $i; ?>" value="<?php echo $key->number; ?>">
                                                </td>
                                                <td class="hide">
                                                    <?php if ($key->po_upload): ?>
                                                        <a href="<?php echo base_url() . 'uploads/' . $key->po_upload ?>" class="btn btn-xs btn-default" title="Download Attachment" download>
                                                            <i class="fa fa-download"></i>
                                                        </a>
                                                    <?php else: ?>
                                                        <span class="text-muted">-</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="hide">
                                                    <?php if ($key->status == 4): ?>
                                                        <span class="text-success"><i class="fa fa-check-circle"></i></span>
                                                    <?php else: ?>
                                                        <input type="checkbox" class="approved-purchase" id="approved<?php echo $i; ?>" name="approved" value="4" data-id="<?php echo $key->id; ?>">
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <div class="btn-group">
                                                        <button class="btn btn-primary dropdown-toggle" id="menu<?php echo $i; ?>" type="button" data-toggle="dropdown">
                                                            <span class="caret"></span>
                                                        </button>
                                                        <ul class="dropdown-menu dropdown-menu-right">
                                                            <li>
                                                                <a href="#" class="view-modal-po-draft-send" data-toggle="modal" data-target="#modalDraft"
                                                                    data-po-number="<?php echo htmlspecialchars($key->number); ?>"
                                                                    data-vendor-email="<?php echo htmlspecialchars($key->vendor_email ?? ''); ?>"
                                                                    data-supplier-email="<?php echo htmlspecialchars($key->email ?? ''); ?>"
                                                                    data-delivery-date="<?php echo htmlspecialchars(date('d-m-Y', strtotime($key->payment_due_date ?? $key->delivery_date ?? ''))); ?>"
                                                                    data-total-amount="<?php echo htmlspecialchars(indian_number_format(round($key->total), 0)); ?>">
                                                                    <i class="fa fa-envelope-o text-warning"></i> Send Draft
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a href="#" class="view-modal-po-email-send" data-toggle="modal" data-target="#modal"
                                                                    data-id="<?php echo $key->number; ?>">
                                                                    <i class="fa fa-envelope text-primary"></i> Send PO
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a href="#" class="view-modal-po-whatsapp-send" data-id="<?php echo $key->number; ?>" data-pdf="<?php echo base_url() . 'Pdf/download_po/' . $key->number; ?>">
                                                                    <i class="fa fa-whatsapp text-success"></i> WhatsApp
                                                                </a>
                                                            </li>
                                                            <li role="separator" class="divider"></li>
                                                            <li>
                                                                <a href="<?php echo base_url() . 'Pdf/download_po/' . $key->number ?>" target="_blank">
                                                                    <i class="fa fa-file-pdf-o text-danger"></i> Export PDF
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a href="<?php echo base_url() . 'SupplierController/show_po/' . $key->number; ?>">
                                                                    <i class="fa fa-eye text-info"></i> View Details
                                                                </a>
                                                            </li>
                                                            <?php if ($key->status == 4 && $key->balance != 0): ?>
                                                                <li>
                                                                    <a href="#" class="modal-purchase-payment" data-toggle="modal" data-target="#modal1"
                                                                        data-id="<?php echo $key->id; ?>">
                                                                        <i class="fa fa-money text-success"></i> Enter Payment
                                                                    </a>
                                                                </li>
                                                            <?php endif; ?>

                                                            <!-- Edit/Amend with red lock UI -->
                                                            <li>
                                                                <?php if ($key->status != 4) : ?>
                                                                    <a href="<?php echo base_url() . 'SupplierController/edit_po_details/' . $key->number; ?>">
                                                                        <i class="fa fa-edit text-warning"></i> Edit/Amend
                                                                    </a>
                                                                <?php else: ?>
                                                                    <span class="dropdown-item disabled-lock" title="PO Approved - Editing Disabled">
                                                                        <i class="fa fa-lock text-danger"></i> Locked
                                                                    </span>
                                                                <?php endif; ?>
                                                            </li>

                                                            <li>
                                                                <a href="#" class="change-po-status-btn" data-id="<?php echo $key->id; ?>" data-number="<?php echo $key->number; ?>" data-status="<?php echo $key->status; ?>" data-remarks="<?php echo htmlspecialchars($key->remarks ?? ''); ?>">
                                                                    <i class="fa fa-refresh text-primary"></i> Change Status
                                                                </a>
                                                            </li>

                                                            <li role="separator" class="divider"></li>
                                                            <li>
                                                                <a href="<?php echo base_url() . 'SupplierController/delete_po_by_po_number/' .  $key->number; ?>"
                                                                    onclick="return confirm('Are you sure you want to delete this PO?');">
                                                                    <i class="fa fa-trash text-danger"></i> Delete
                                                                </a>
                                                            </li>
                                                        </ul>


                                                    </div>
                                                </td>
                                            </tr>
                                        <?php $i++;
                                            $unpaid++;
                                        } ?>
                                    </tbody>
                                   </table>
                             </div>
                             <!-- /.box-body -->
                            <div class="box-footer clearfix">
                                <div class="pull-left">
                                    <span class="label label-primary">Total: <?php echo count($purchase_order); ?> POs</span>
                                    <?php if ($unpaid > 0): ?>
                                        <span class="label label-danger ml-2">Unpaid: <?php echo $unpaid; ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="pull-right">
                                    <button class="btn btn-default btn-sm" onclick="window.print()">
                                        <i class="fa fa-print"></i> Print
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->
        <?php $this->load->view('admin/footer'); ?>

    <!-- Send PO WhatsApp Modal -->
    <div id="poWhatsappModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Send Purchase Order WhatsApp</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="po_whatsapp_mobile">Mobile Number <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="po_whatsapp_mobile" placeholder="Enter mobile number">
                    </div>
                    <div class="form-group">
                        <label for="po_whatsapp_message">Message <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="po_whatsapp_message" rows="5" placeholder="Enter your message"></textarea>
                    </div>
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" id="po_whatsapp_send_btn"><i class="fa fa-whatsapp"></i> Send WatsApp</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Send PO Email Modal -->
    <div id="modal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><i class="fa fa-paper-plane"></i> Send Purchase Order</h4>
                </div>
                <form class="form-horizontal" method="post" action="<?php echo base_url(); ?>SupplierController/send_po_email" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="control-label col-sm-3">To <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="hidden" name="supplier_id" id="supplier_id" value="">
                                <input type="hidden" name="number" id="number" value="">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                                    <input type="text" class="form-control" name="to_email" id="to_email" required placeholder="recipient@example.com, cc@example.com">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-3">Subject</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="subject" id="subject" placeholder="Purchase Order Subject">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-3">Message</label>
                            <div class="col-sm-9">
                                <textarea class="form-control" name="message" id="message" rows="4" placeholder="Add your message here..."></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-3">Copy to (CC)</label>
                            <div class="col-sm-9">
                                <div style="border: 1px solid #d2d6de; border-radius: 4px; padding: 10px; background: #fafafa;">
                                    <div style="max-height: 140px; overflow-y: auto; padding-right: 5px; margin-bottom: 6px;">
                                        <?php if (!empty($set_cc_email)): ?>
                                            <div style="margin-bottom: 6px;">
                                                <label style="font-weight: 600; cursor: pointer; margin-bottom: 0;">
                                                    <input type="checkbox" name="cc_emails[]" value="<?php echo htmlspecialchars($set_cc_email); ?>" checked>
                                                    <i class="fa fa-envelope-o text-primary"></i> <?php echo htmlspecialchars($set_cc_email); ?>
                                                    <small class="text-muted">(Default CC)</small>
                                                </label>
                                            </div>
                                        <?php endif; ?>

                                        <?php if (!empty($team_users)): ?>
                                            <?php foreach ($team_users as $t_user): ?>
                                                <?php if (!empty($t_user['user_email']) && $t_user['user_email'] !== $set_cc_email): ?>
                                                    <div style="margin-bottom: 6px;">
                                                        <label style="font-weight: normal; cursor: pointer; margin-bottom: 0;">
                                                            <input type="checkbox" name="cc_emails[]" value="<?php echo htmlspecialchars($t_user['user_email']); ?>">
                                                            <?php echo htmlspecialchars($t_user['username']); ?> <small class="text-muted">(<?php echo htmlspecialchars($t_user['user_email']); ?>)</small>
                                                        </label>
                                                    </div>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        <?php endif; ?>

                                        <div id="vendor_cc_container"></div>
                                        <div id="custom_cc_container"></div>
                                    </div>

                                    <div style="padding-top: 8px; border-top: 1px dashed #ccc; display: flex; gap: 6px; align-items: center;">
                                        <input type="email" id="new_custom_cc_email" class="form-control input-sm" placeholder="Add custom CC email (e.g. name@company.com)" onkeydown="if(event.key==='Enter'){event.preventDefault();addCustomCcEmail('custom_cc_container','new_custom_cc_email');}">
                                        <button type="button" class="btn btn-sm btn-info" onclick="addCustomCcEmail('custom_cc_container','new_custom_cc_email')" style="white-space: nowrap;">
                                            <i class="fa fa-plus"></i> Add
                                        </button>
                                    </div>
                                </div>
                                <small class="help-block" style="margin-top: 3px; font-size: 11px; margin-bottom: 0;">Select team emails, vendor secondary emails, or add custom addresses to CC on this PO.</small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary"><i class="fa fa-send"></i> Send </button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Change PO Status Modal -->
    <div id="poStatusModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><i class="fa fa-refresh"></i> Change PO Status</h4>
                </div>
                <form method="post" action="<?php echo base_url(); ?>SupplierController/update_po_status">
                    <div class="modal-body">
                        <input type="hidden" name="po_id" id="po_status_id">
                        <input type="hidden" name="po_number" id="po_status_number">
                        <div class="form-group">
                            <label>Status<span style="color:red;">*</span></label>
                            <select name="status" id="po_status_select" class="form-control" required>
                                <option value="1">Draft</option>
                                <option value="2">Sent</option>
                                <option value="3">Viewed</option>
                                <option value="4">Approved</option>
                                <option value="5">Rejected</option>
                                <option value="6">Canceled</option>
                                <option value="7">Accepted</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Remark / Note</label>
                            <textarea name="remarks" id="po_status_remarks" class="form-control" rows="2" placeholder="Enter status change remark..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-check"></i> Update</button>
                        <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Enter Payment Modal -->
    <div id="modal1" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><i class="fa fa-money"></i> Enter Payment</h4>
                </div>
                <form class="form-horizontal balance-check" method="post" action="<?php echo base_url(); ?>SupplierController/edit_purchase_payment">
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="control-label col-sm-4">Balance <span class="text-danger">*</span></label>
                            <div class="col-sm-8">
                                <input type="hidden" name="id" id="id" value="">
                                <input type="hidden" name="total" id="total" value="">
                                <input type="hidden" name="number" id="number_fk" value="">
                                <input type="hidden" name="supplier_id_fk" id="supplier_id_fk" value="">
                                <div class="input-group">
                                    <span class="input-group-addon">₹</span>
                                    <input type="text" class="form-control" name="balance" id="balance" required readonly style="font-weight: bold; background-color: #f8f9fa;">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-4">Payment Type <span class="text-danger">*</span></label>
                            <div class="col-sm-8">
                                <select class="form-control" name="payment_type" id="payment_type" required>
                                    <option value="">Select Type</option>
                                    <option value="Advance">Advance</option>
                                    <option value="Partial">Partial</option>
                                    <option value="Final">Final</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-4">Amount <span class="text-danger">*</span></label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <span class="input-group-addon">₹</span>
                                    <input type="text" class="form-control allownumericwithdecimal" name="paid" id="paid" required placeholder="0.00">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-4">Payment Method <span class="text-danger">*</span></label>
                            <div class="col-sm-8">
                                <select class="form-control" name="payment_method" id="payment_method" required>
                                    <option value="">Select Method</option>
                                    <option value="1">Cash</option>
                                    <option value="2">Cheque</option>
                                    <option value="3">NetBanking</option>
                                    <option value="4">Credit Card</option>
                                    <option value="5">Other</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-4">Notes</label>
                            <div class="col-sm-8">
                                <textarea class="form-control" name="note" id="note" rows="3" placeholder="Add payment notes..."></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-4">Payment Date <span class="text-danger">*</span></label>
                            <div class="col-sm-8">
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" class="form-control backdate" name="date" id="date" required onkeydown="return false;" autocomplete="off">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success"><i class="fa fa-check"></i> Submit Payment</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Draft PO Modal -->
    <div id="modalDraft" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><i class="fa fa-file-text-o"></i> Send Draft PO for Review</h4>
                </div>
                <form id="draftPoForm" class="form-horizontal" method="post" action="<?php echo base_url(); ?>SupplierController/send_draft_po" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="alert alert-warning alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <h4><i class="icon fa fa-info"></i> Draft Mode</h4>
                            This will send a <strong>DRAFT</strong> version for vendor review and confirmation before final approval.
                        </div>

                        <input type="hidden" name="number" id="draft_number" value="">

                        <div class="form-group">
                            <label class="control-label col-sm-3">To <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-user"></i></span>
                                    <input type="email" class="form-control" name="to_email" id="draft_to_email" required placeholder="vendor@company.com">
                                </div>
                                <small class="help-block">Vendor's email address</small>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-3">Subject</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="subject" id="draft_subject" value="DRAFT Purchase Order for Review">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-3">Message</label>
                            <div class="col-sm-9">
                                <textarea class="form-control" name="message" id="draft_message" rows="4" placeholder="Please review the attached draft purchase order and confirm the details..."></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-3">Delivery Date</label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                    <input type="text" class="form-control" id="draft_delivery_date" readonly style="background-color: #f8f9fa;">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-3">Total Amount</label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <span class="input-group-addon">₹</span>
                                    <input type="text" class="form-control" id="draft_total_amount" readonly style="background-color: #f8f9fa; font-weight: bold;">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-3">Copy to (CC)</label>
                            <div class="col-sm-9">
                                <div style="border: 1px solid #d2d6de; border-radius: 4px; padding: 10px; background: #fafafa;">
                                    <div style="max-height: 140px; overflow-y: auto; padding-right: 5px; margin-bottom: 6px;">
                                        <?php if (!empty($set_cc_email)): ?>
                                            <div style="margin-bottom: 6px;">
                                                <label style="font-weight: 600; cursor: pointer; margin-bottom: 0;">
                                                    <input type="checkbox" name="cc_emails[]" value="<?php echo htmlspecialchars($set_cc_email); ?>" checked>
                                                    <i class="fa fa-envelope-o text-primary"></i> <?php echo htmlspecialchars($set_cc_email); ?>
                                                    <small class="text-muted">(Default CC)</small>
                                                </label>
                                            </div>
                                        <?php endif; ?>

                                        <?php if (!empty($team_users)): ?>
                                            <?php foreach ($team_users as $t_user): ?>
                                                <?php if (!empty($t_user['user_email']) && $t_user['user_email'] !== $set_cc_email): ?>
                                                    <div style="margin-bottom: 6px;">
                                                        <label style="font-weight: normal; cursor: pointer; margin-bottom: 0;">
                                                            <input type="checkbox" name="cc_emails[]" value="<?php echo htmlspecialchars($t_user['user_email']); ?>">
                                                            <?php echo htmlspecialchars($t_user['username']); ?> <small class="text-muted">(<?php echo htmlspecialchars($t_user['user_email']); ?>)</small>
                                                        </label>
                                                    </div>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        <?php endif; ?>

                                        <div id="draft_vendor_cc_container"></div>
                                        <div id="draft_custom_cc_container"></div>
                                    </div>

                                    <div style="padding-top: 8px; border-top: 1px dashed #ccc; display: flex; gap: 6px; align-items: center;">
                                        <input type="email" id="draft_new_custom_cc_email" class="form-control input-sm" placeholder="Add custom CC email (e.g. name@company.com)" onkeydown="if(event.key==='Enter'){event.preventDefault();addCustomCcEmail('draft_custom_cc_container','draft_new_custom_cc_email');}">
                                        <button type="button" class="btn btn-sm btn-info" onclick="addCustomCcEmail('draft_custom_cc_container','draft_new_custom_cc_email')" style="white-space: nowrap;">
                                            <i class="fa fa-plus"></i> Add
                                        </button>
                                    </div>
                                </div>
                                <small class="help-block" style="margin-top: 3px; font-size: 11px; margin-bottom: 0;">Select team emails or add custom addresses to CC on this draft PO.</small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-warning"><i class="fa fa-paper-plane"></i> Send Draft</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Month-Year Picker Styles -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css">
    <style>
        .ui-datepicker-calendar {
            display: none;
        }

        .month-year-picker {
            background-color: white !important;
            cursor: pointer !important;
        }
    </style>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    <script>
        $(document).ready(function() {
            // Inject runtime style tag to guarantee dark text for DataTables controls and labels
            $('head').append(`
                <style id="po_dynamic_override_css">
                    select, 
                    option, 
                    input, 
                    label,
                    .dataTables_wrapper,
                    .dataTables_wrapper label,
                    .dataTables_wrapper *,
                    .dataTables_length,
                    .dataTables_length label,
                    .dataTables_filter,
                    .dataTables_filter label,
                    .dataTables_info,
                    .dataTables_paginate {
                        color: #111111 !important;
                        font-weight: 700 !important;
                        font-size: 13px !important;
                        opacity: 1 !important;
                        visibility: visible !important;
                    }
                    select,
                    option,
                    input,
                    .form-control,
                    select option {
                        color: #111111 !important;
                        background-color: #ffffff !important;
                        border: 1px solid #777777 !important;
                        font-weight: 600 !important;
                        font-size: 13px !important;
                        opacity: 1 !important;
                    }
                </style>
            `);

            // Initialize DataTables
            var table;
            if (!$.fn.DataTable.isDataTable('#view_purchase_order')) {
                table = $('#view_purchase_order').DataTable({
                    "paging": true,
                    "lengthChange": true,
                    "searching": true,
                    "ordering": true,
                    "info": true,
                    "autoWidth": false,
                    "scrollX": false,
                    "pageLength": 25,
                    "language": {
                        "search": "Search Purchase Orders:"
                    }
                });
            } else {
                table = $('#view_purchase_order').DataTable();
            }

            // Bind search input
            $('#table_search').on('keyup', function() {
                table.search($(this).val()).draw();
            });

            // Initialize datepicker for regular dates
            $('.backdate').datepicker({
                format: 'dd-mm-yyyy',
                autoclose: true,
                todayHighlight: true
            });

            // Initialize Month-Year picker
            $('#month_year').datepicker({
                dateFormat: 'mm-yy',
                changeMonth: true,
                changeYear: true,
                showButtonPanel: true,
                onClose: function(dateText, inst) {
                    var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
                    var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
                    $(this).datepicker('setDate', new Date(year, month, 1));
                }
            }).focus(function() {
                $(".ui-datepicker-calendar").hide();
                $(".ui-datepicker-current").hide();
                $(".ui-datepicker-buttonpane").find('button').each(function() {
                    if ($(this).text() === 'Today') {
                        $(this).hide();
                    }
                });
            });

            // Alternative Month-Year picker if above doesn't work
            /*
            $('#month_year').datepicker({
                format: "mm/yyyy",
                startView: "months", 
                minViewMode: "months",
                autoclose: true,
                todayHighlight: true
            });
            */

            // Payment modal handler
            $('.modal-purchase-payment').click(function() {
                var id = $(this).data('id');
                $.ajax({
                    url: '<?php echo base_url(); ?>SupplierController/get_purchase_payment_details',
                    type: 'POST',
                    data: {
                        id: id
                    },
                    dataType: 'json',
                    success: function(response) {
                        $('#id').val(id);
                        $('#total').val(response.total);
                        $('#number_fk').val(response.number_fk);
                        $('#supplier_id_fk').val(response.supplier_id_fk);
                        $('#balance').val(response.balance);
                        $('#modal1').modal('show');
                    }
                });
            });

            // Email modal handler
            $(document).on('click', '.view-modal-po-email-send', function() {
                var number = $(this).data('id');
                $('#number').val(number);
                $('#subject').val('Purchase Order ' + number);
                $('#modal').attr('data-current-number', number);
                $.ajax({
                    url: '<?php echo base_url(); ?>SupplierController/get_supplier_email',
                    type: 'POST',
                    data: { number: number },
                    dataType: 'json',
                    success: function(response) {
                        if ($('#modal').attr('data-current-number') !== String(number)) return;
                        $('#to_email').val(response && response.email ? response.email : '');
                        $('#modal').modal('show');
                    },
                    error: function() {
                        if ($('#modal').attr('data-current-number') !== String(number)) return;
                        $('#to_email').val('');
                        $('#modal').modal('show');
                    }
                });
            });

            // WhatsApp URL builder
            function buildPoWhatsAppUrl() {
                var mobile = $('#po_whatsapp_mobile').val().replace(/[^0-9]/g, '');
                var message = $('#po_whatsapp_message').val();
                if (mobile && message.trim()) {
                    $('#po_whatsapp_send_link').attr('href', 'https://wa.me/' + mobile + '?text=' + encodeURIComponent(message));
                    $('#po_whatsapp_url_info').show(); 
                } else {
                    $('#po_whatsapp_url_info').hide();
                }
            }

            // WhatsApp modal handler
            $(document).on('click', '.view-modal-po-whatsapp-send', function(event) {
                event.preventDefault();
                var poNumber = $(this).data('id');
                var pdfUrl = $(this).data('pdf');
                $('#poWhatsappModal').attr('data-current-number', poNumber);
                $('#po_whatsapp_mobile').val('');
                $('#po_whatsapp_message').val('');
                $('#po_whatsapp_url_info').hide();
                $('#poWhatsappModal').modal('show');
                $.ajax({
                    url: '<?php echo base_url(); ?>SupplierController/get_supplier_email',
                    type: 'POST',
                    data: { number: poNumber },
                    dataType: 'json',
                    success: function(result) {
                        if ($('#poWhatsappModal').attr('data-current-number') !== String(poNumber)) return;
                        if (typeof result === 'string') { try { result = $.parseJSON(result); } catch(e) { result = null; } }
                        result = result || {};
                        var rawMobile = result.mobile || result.supplier_mobile || result.mobile_number || result.phone || '';
                        var mobile = String(rawMobile).replace(/[^0-9]/g, '');
                        $('#po_whatsapp_mobile').val(mobile);
                        var message = 'Dear Sir/Madam,\n\nPurchase Order ' + poNumber + ' is shared with you.\n\nPlease check and confirm.\n\nPDF: ' + pdfUrl + '\n\nThanks.';
                        $('#po_whatsapp_message').val(message);
                        buildPoWhatsAppUrl();
                    },
                    error: function() {
                        if ($('#poWhatsappModal').attr('data-current-number') !== String(poNumber)) return;
                        var message = 'Dear Sir/Madam,\n\nPurchase Order ' + poNumber + ' is shared with you.\n\nPlease check and confirm.\n\nPDF: ' + pdfUrl + '\n\nThanks.';
                        $('#po_whatsapp_message').val(message);
                        buildPoWhatsAppUrl();
                    }
                });
            });

            $(document).on('input', '#po_whatsapp_mobile, #po_whatsapp_message', function() {
                buildPoWhatsAppUrl();
            });

            $(document).on('click', '#po_whatsapp_send_btn', function() {
                var mobile = $('#po_whatsapp_mobile').val().replace(/[^0-9]/g, '');
                var message = $('#po_whatsapp_message').val();
                if (!mobile || !message.trim()) {
                    alert('Please enter both mobile number and message');
                    return;
                }
                window.open('https://wa.me/' + mobile + '?text=' + encodeURIComponent(message), '_blank', 'noopener');
            });

            // Draft modal handler
            $(document).on('click', '.view-modal-po-draft-send', function(e) {
                e.preventDefault();

                // Store the data before dropdown closes
                var poNumber = $(this).data('po-number');
                var vendorEmail = $(this).data('vendor-email') || $(this).data('supplier-email');
                var deliveryDate = $(this).data('delivery-date');
                var totalAmount = $(this).data('total-amount');

                // Prevent dropdown from interfering
                $(this).closest('.dropdown-menu').removeClass('open');
                $('body').removeClass('dropdown-open');

                // Use a timeout to ensure dropdown is closed
                setTimeout(function() {
                    $('#draft_number').val(poNumber);
                    $('#draft_to_email').val(vendorEmail || '');
                    $('#draft_delivery_date').val(deliveryDate || '');
                    $('#draft_total_amount').val('₹ ' + (totalAmount || '0.00'));
                    $('#draft_subject').val('DRAFT Purchase Order ' + poNumber + ' for Review');

                    // Force modal to show
                    $('#modalDraft').modal({
                        backdrop: 'static',
                        keyboard: false
                    });
                    $('#modalDraft').modal('show');

                    // Prevent hiding
                    $('#modalDraft').off('hide.bs.modal');
                }, 50);

                return false;
            });

            // Change PO Status modal handler
            $(document).on('click', '.change-po-status-btn', function(e) {
                e.preventDefault();
                var id = $(this).data('id');
                var number = $(this).data('number');
                var status = $(this).data('status');
                var remarks = $(this).data('remarks') || '';
                setTimeout(function() {
                    $('#po_status_id').val(id);
                    $('#po_status_number').val(number);
                    $('#po_status_select').val(status);
                    $('#po_status_remarks').val(remarks);
                    $('#poStatusModal').modal('show');
                }, 50);
            });

            // Auto-approve checkbox handler
            $('.approved-purchase').change(function() {
                if ($(this).is(':checked')) {
                    var id = $(this).data('id');
                    var element = $(this);
                    $.ajax({
                        url: '<?php echo base_url(); ?>SupplierController/approve_purchase_status',
                        type: 'POST',
                        data: {
                            number_fk: id
                        },
                        success: function(response) {
                            if (response == 'True') {
                                element.hide();
                                element.next('i').show();
                                element.closest('tr').find('.label-default').removeClass('label-default').addClass('label-success').text('Approved');
                            }
                        }
                    });
                }
            });

            // Input formatting
            $('.allownumericwithdecimal').on('keypress', function(e) {
                var charCode = (e.which) ? e.which : e.keyCode;
                if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57)) {
                    return false;
                }
                return true;
            });
        });

        // Dynamic Custom CC Email addition (similar to RFQ view)
        function addCustomCcEmail(containerId, inputId) {
            containerId = containerId || 'custom_cc_container';
            inputId = inputId || 'new_custom_cc_email';
            
            var emailInput = $('#' + inputId);
            var emailVal = $.trim(emailInput.val());
            
            if (!emailVal) {
                alert('Please enter an email address.');
                return;
            }
            
            var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(emailVal)) {
                alert('Please enter a valid email address.');
                return;
            }
            
            var exists = false;
            $('#' + containerId).closest('.col-sm-9').find('input[name="cc_emails[]"]').each(function() {
                if ($(this).val().toLowerCase() === emailVal.toLowerCase()) {
                    exists = true;
                    $(this).prop('checked', true);
                }
            });
            
            if (exists) {
                emailInput.val('');
                return;
            }
            
            var newHtml = '<div style="margin-bottom: 6px;">' +
                '<label style="font-weight: 600; color: #007bff; cursor: pointer; margin-bottom: 0;">' +
                    '<input type="checkbox" name="cc_emails[]" value="' + emailVal + '" checked> ' +
                    '<i class="fa fa-user-plus text-info"></i> ' + emailVal + ' <small class="text-success">(Added)</small>' +
                '</label>' +
            '</div>';
            
            $('#' + containerId).append(newHtml);
            emailInput.val('');
        }
    </script>

    <style>
        .table-hover tbody tr:hover {
            background-color: #f5f5f5;
            cursor: pointer;
        }

        .label {
            font-size: 12px;
            padding: 4px 8px;
            border-radius: 12px;
        }

        .text-truncate {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .dropdown-menu>li>a {
            padding: 8px 15px;
        }

        .dropdown-menu>li>a:hover {
            background-color: #f5f5f5;
        }

        .modal-header {
            border-radius: 5px 5px 0 0;
        }

        .input-group-addon {
            background-color: #f8f9fa;
        }

        .box-header .box-title i {
            margin-right: 8px;
        }

        .btn-group .btn {
            border-radius: 4px !important;
        }

        .modal-content {
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, .1);
        }

        /* Month-Year Picker Styles */
        .ui-datepicker {
            width: 300px;
        }

        .ui-datepicker-calendar {
            display: none !important;
        }

        .ui-datepicker-month,
        .ui-datepicker-year {
            width: 45%;
            margin: 0 2%;
        }
    </style>
