<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (isset($session_data_head1)) {
} else {
    header($this->config->item('header'));
}
defined('BASEPATH') or exit('No direct script access allowed');
require_once(APPPATH . "views/admin/modal.php");

// Calculate the next GRN number safely
$grn_count = isset($grn_id['count']) ? $grn_id['count'] : 0;
$next_grn_number = $grn_count + 1;

// Financial year for GRN (same as PO/PR format)
if (date('m') <= 3) {
    $grn_financial_year = (date('y') - 1) . '-' . date('y');
} else {
    $grn_financial_year = date('y') . '-' . (date('y') + 1);
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
                    GRN
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="#">GRN</a></li>
                    <li class="active">GRN Details</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-info">
                            <div class="box-header">
                                <h3 class="box-title">Create GRN</h3>
                                <a href="javascript:void(0);" onclick="window.history.back()" class="btn btn-primary pull-right"><i class="fa fa-close"></i> Close</a>

                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">
                                <form class="form-horizontal form form_overlay" name="add_name" id="add_name" method="post" action="<?php echo base_url(); ?>GrnController/add_grn" enctype="multipart/form-data">
                                    <div class="row">

                                        <div class="col-md-12">
                                            <div class="form-group row ">
                                                <input type="hidden" class="form-control date input-sm" value="grn_hide" name="grn_hide" id="grn_hide" required="">
                                                <input type="hidden" class="form-control input-sm" name="grn_number" id="grn_number" required="" value="GRN/<?php echo $grn_financial_year; ?>/<?php printf("%04d", $next_grn_number); ?>">
                                                <label for="inputEmail3" name="number" id="number" class="col-sm-12 control-label">
                                                    <h2>GRN: <b>GRN/<?php echo $grn_financial_year; ?>/<?php printf("%04d", $next_grn_number); ?></b></h2>
                                                </label>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-3 control-label">PO No.<span style="color: red;">*</span></label>
                                                <div class="col-sm-9">
                                                    <select class="form-control input-sm po_number reload-po company_search_name" name="po_number" id="po_number" data-placeholder="Select PO" required="">
                                                        <option value=""></option>
                                                        <?php foreach ($po_number as $key) { ?>
                                                            <option value="<?php echo $key->number; ?>"><?php echo $key->number; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="form-group row ">
                                                <label for="inputEmail3" class="col-sm-3 control-label">Invoice No.<span style="color: red;">*</span></label>
                                                <div class="col-sm-9">
                                                    <input type="text" class="form-control input-sm" name="invoice_number" id="invoice_number" required="">
                                                </div>
                                            </div>

                                        </div>

                                        <div class="col-md-3">

                                            <div class="form-group row ">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Date<span style="color: red;">*</span></label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control alldate input-sm created-date" name="date" id="date" required="" autocomplete="off">
                                                    <input type="hidden" class="form-control date input-sm" name="supplier_id" id="supplier_id">
                                                    <input type="hidden" class="form-control date input-sm" name="po_number_fk" id="po_number_fk">
                                                </div>
                                            </div>
                                            <div class="form-group row ">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Invoice Date<span style="color: red;">*</span></label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control date input-sm" name="invoice_date" id="invoice_date" required="" autocomplete="off">
                                                </div>
                                            </div>

                                        </div>

                                        <div class="col-md-6 hide">
                                            <div class="form-group row ">
                                                <label for="inputEmail3" id="subheading1" class="col-sm-4 control-label">Subheading</label>
                                                <div class="col-sm-8">
                                                    <input type="text" value="<?php //echo $settings['quotation_subheading']; 
                                                                                ?>" class="form-control" name="quotation_subheading" id="quotation_subheading">
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Footer</label>
                                                <div class="col-sm-8">
                                                    <textarea class="form-control" name="quotation_footer" id="quotation_footer" rows="3"><?php //echo $settings['quotation_footer']; 
                                                                                                                                            ?></textarea>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Memo</label>
                                                <div class="col-sm-8">
                                                    <textarea class="form-control" name="quotation_memo" id="quotation_memo" rows="3"><?php //echo $settings['quotation_memo']; 
                                                                                                                                        ?></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="table-responsive">

                                        <table class="table table-bordered" id="dynamic_field">
                                            <thead>
                                                <th style="width: 12%;">Item</th>
                                                <th>Description</th>
                                                <th>Quantity</th>
                                                <th>HSN Code</th>
                                                <th>GST</th>
                                                <th>SGST</th>
                                                <th>CGST</th>
                                                <th>Received</th>
                                                <th>Pending</th>
                                                <th>Price/Unit</th>
                                                <th>Amount</th>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <div align="center">
                                            <button type="submit" name="submit" id="submit" class="btn btn-success">Save</button>
                                        </div>
                                        <div align="right" style="margin: 10px">
                                            <!--<span id="total_grn_amount" name="total_grn_amount">Grand Total: ₹0.00</span><br>-->
                                            <input type="hidden" name="total_grn_amount1" id="total_grn_amount1" class="form-control input-sm basic_total" value="0.00" />
                                            <div style="font-size:10px; margin-bottom:2px;">
                                                <span class="text" id="sgst_amount" name="sgst_amount[]"><b>SGST Amount:</b> ₹0.00</span><br> &nbsp;
                                                <span class="text" id="cgst_amount" name="cgst_amount[]"><b>CGST Amount:</b> ₹0.00</span><br> &nbsp;
                                                <span class="text" id="grand_total_amount"><b>Grand Total:</b> ₹0.00</span>
                                            </div>
                                            <input type="hidden" name="total_quotation_amount" id="total_quotation_amount" class="form-control input-sm name_list" value="0.00" />
                                            <div style="font-size:9px; margin-top:5px;">
                                                <b>Amount in Words:</b> <span id="amount_in_words">Zero Rupees Only</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <label for="inputEmail3" class="col-sm-1 control-label">Note</label>
                                        <div class="col-sm-6">
                                            <textarea class="form-control" name="note" id="note" rows="2"></textarea>
                                        </div>
                                        <div class="col-sm-5">
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


                <script src="<?php echo base_url(); ?>application/views/grn/create_grn.js"></script>
                <script>
                    $(document).ready(function() {
                        updateAmountInWords(); // Initial call
                    });
                </script>
            <?php $this->load->view('admin/footer'); ?>
        <div class="control-sidebar-bg"></div>
    </div>
    <!-- ./wrapper -->
