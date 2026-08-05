<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (isset($session_data_head1)) {
} else {
    header($this->config->item('header'));
}
defined('BASEPATH') or exit('No direct script access allowed');

// Check if GRN data exists
if (empty($grn_data_group)) {
    echo '<div class="alert alert-danger">GRN data not found. Please go back to GRN list.</div>';
    echo '<a href="' . base_url() . 'GrnController/grn_index" class="btn btn-primary">Back to GRN List</a>';
    exit();
}
?>

<body class="hold-transition skin-blue sidebar-mini">
    <div id="loader" class="center"></div>
    <div class="wrapper">

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    GRN Edit
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="<?php echo base_url() . 'GrnController/grn_index' ?>">GRN</a></li>
                    <li class="active">Edit GRN</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-warning">
                            <div class="box-header">
                                <h3 class="box-title">Edit GRN Details</h3>
                                <a href="javascript:void(0);" onclick="window.history.back()" class="btn btn-primary pull-right"><i class="fa fa-close"></i> Close</a>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">
                                
                                <!-- Start Flash Message -->
                                <?php if ($this->session->flashdata('SUCCESSMSG')) { ?>
                                    <div role="alert" class="alert alert-success">
                                        <button data-dismiss="alert" class="close" type="button"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                                        <strong>Success!!</strong> <?= $this->session->flashdata('SUCCESSMSG') ?>
                                    </div>
                                <?php } ?>

                                <?php if ($this->session->flashdata('INFOMSG')) { ?>
                                    <div role="alert" class="alert alert-info">
                                        <button data-dismiss="alert" class="close" type="button"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                                        <strong>Warning!!</strong> <?= $this->session->flashdata('INFOMSG') ?>
                                    </div>
                                <?php } ?>
                                <!-- End Flash Message -->

                                <form class="form-horizontal form_overlay" name="edit_grn_form" id="edit_grn_form" method="post" action="<?php echo base_url(); ?>GrnController/update_grn_data" enctype="multipart/form-data">
                                    
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group row">
                                                <input type="hidden" name="grn_id" id="grn_id" value="<?php echo isset($grn_data_group['grn_id']) ? $grn_data_group['grn_id'] : ''; ?>">
                                                <label for="inputEmail3" name="number" id="number" class="col-sm-12 control-label">
                                                    <h2>GRN: <b><?php echo isset($grn_data_group['grn_number']) ? $grn_data_group['grn_number'] : 'N/A'; ?></b></h2>
                                                </label>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">PO No.<span style="color: red;">*</span></label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control input-sm" id="po_number_display" value="<?php echo isset($grn_data_group['po_number']) ? $grn_data_group['po_number'] : ''; ?>" readonly>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Invoice No.<span style="color: red;">*</span></label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control input-sm" name="invoice_number" id="invoice_number" value="<?php echo isset($grn_data_group['invoice_number']) ? $grn_data_group['invoice_number'] : ''; ?>" required="">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Date<span style="color: red;">*</span></label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control alldate input-sm" name="date" id="date" value="<?php echo isset($grn_data_group['date']) ? $grn_data_group['date'] : ''; ?>" required="" autocomplete="off">
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Invoice Date<span style="color: red;">*</span></label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control date input-sm" name="invoice_date" id="invoice_date" value="<?php echo isset($grn_data_group['invoice_date']) ? $grn_data_group['invoice_date'] : ''; ?>" required="" autocomplete="off">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <hr>

                                    <!-- Items Table -->
                                    <div class="table-responsive">
                                        <h4>GRN Line Items</h4>
                                        <table class="table table-bordered table-striped" id="grn_items_table">
                                            <thead>
                                                <tr>
                                                    <th>Sr.No.</th>
                                                    <th>Item</th>
                                                    <th>Description</th>
                                                    <th>Quantity</th>
                                                    <th>HSN Code</th>
                                                    <th>GST</th>
                                                    <th>Received</th>
                                                    <th>Pending</th>
                                                    <th>Price/Unit</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $i = 1;
                                                if (!empty($show_grn)) {
                                                    foreach ($show_grn as $key) {
                                                ?>
                                                        <tr>
                                                            <td><?php echo $i; ?></td>
                                                            <td><?php echo isset($key->product_name) ? $key->product_name : ''; ?></td>
                                                            <td>
                                                                <input type="hidden" name="item_id[]" value="<?php echo isset($key->grn_id) ? $key->grn_id : ''; ?>">
                                                                <input type="text" class="form-control input-sm" name="description[]" value="<?php echo isset($key->description) ? $key->description : ''; ?>">
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control input-sm" name="quantity[]" value="<?php echo isset($key->quantity) ? $key->quantity : 0; ?>" onkeypress="return isNumberKey(event)">
                                                            </td>
                                                            <td><?php echo isset($key->hsn_code) ? $key->hsn_code : ''; ?></td>
                                                            <td><?php echo isset($key->gst) ? $key->gst : ''; ?></td>
                                                            <td>
                                                                <input type="text" class="form-control input-sm" name="received_quantity[]" value="<?php echo isset($key->received_quantity) ? $key->received_quantity : 0; ?>" onkeypress="return isNumberKey(event)">
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control input-sm" name="pending_quantity[]" value="<?php echo isset($key->pending_quantity) ? $key->pending_quantity : 0; ?>" onkeypress="return isNumberKey(event)" readonly>
                                                            </td>
                                                            <td><?php echo isset($key->price) ? number_format($key->price, 2) : '0.00'; ?></td>
                                                        </tr>
                                                <?php
                                                        $i++;
                                                    }
                                                } else {
                                                    echo '<tr><td colspan="9" class="text-center">No items found</td></tr>';
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>

                                    <hr>

                                    <div class="form-group row">
                                        <div class="col-sm-12">
                                            <button type="submit" class="btn btn-success" name="submit" id="submit">
                                                <i class="glyphicon glyphicon-ok"></i> Update GRN
                                            </button>
                                            <button type="button" class="btn btn-danger" onclick="window.history.back()">
                                                <i class="glyphicon glyphicon-remove"></i> Cancel
                                            </button>
                                        </div>
                                    </div>

                                </form>

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

    <script>
        $(document).ready(function() {
            // Initialize date pickers
            $('.alldate').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                todayHighlight: true
            });

            $('.date').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                todayHighlight: true
            });

            // Form validation
            $('#edit_grn_form').on('submit', function(e) {
                var grnDate = $('#date').val();
                var invoiceDate = $('#invoice_date').val();
                var invoiceNumber = $('#invoice_number').val();

                if (!grnDate) {
                    e.preventDefault();
                    alert('Please select GRN Date');
                    return false;
                }

                if (!invoiceDate) {
                    e.preventDefault();
                    alert('Please select Invoice Date');
                    return false;
                }

                if (!invoiceNumber) {
                    e.preventDefault();
                    alert('Please enter Invoice Number');
                    return false;
                }

                if (confirm('Are you sure you want to update this GRN?')) {
                    return true;
                } else {
                    e.preventDefault();
                    return false;
                }
            });
        });

        // Number validation
        function isNumberKey(evt) {
            var charCode = (evt.which) ? evt.which : evt.keyCode;
            if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 46) {
                return false;
            }
            return true;
        }
    </script>

</body>
