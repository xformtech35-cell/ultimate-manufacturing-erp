<?php
// print_r($invoice_data_group['type']);
// die();




$session_data_head1 = $this->session->userdata('session_data_head');
if (isset($session_data_head1)) {
    
} else {
    header($this->config->item('header'));
}
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(APPPATH."views/admin/modal.php");

?>

<body class="hold-transition skin-blue sidebar-mini">
    <div id="loader" class="center"></div>
    <div class="wrapper">

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    Edit Notes
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <!-- <li><a href="#">GST Invoice</a></li>
                    <li class="active">GST Invoice Details</li> -->
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-info">
                            <div class="box-header">

                                <h3 class="box-title">Edit Notes</h3>
                                <!--<button class="btn btn-success btn-sm pull-right"  data-toggle="modal" data-target="#myModal"><i class="glyphicon glyphicon-plus"></i>Add Customer</button>-->
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">

                                <!-- Start Flash Message -->
                                <?php if ($this->session->flashdata('SUCCESSMSG')) { ?>
                                <div role="alert" class="alert alert-success">
                                    <button data-dismiss="alert" class="close" type="button"><span
                                            aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                                    <strong>Well done!!</strong> <?= $this->session->flashdata('SUCCESSMSG') ?>
                                </div>
                                <?php } ?>

                                <?php if ($this->session->flashdata('INFOMSG')) { ?>
                                <div role="alert" class="alert alert-info">
                                    <button data-dismiss="alert" class="close" type="button"><span
                                            aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                                    <strong>Oops!!</strong> <?= $this->session->flashdata('INFOMSG') ?>
                                </div>
                                <?php } ?>
                                <!-- End Flash Message -->

                                <form class="form-horizontal form_overlay" name="add_name" id="add_name" method="post"
                                    action="<?php echo base_url(); ?>CreditnoteController/edit_invoice"
                                    enctype="multipart/form-data">
                                    <div class="row">

                                        <div class="col-md-12">
                                            <div class="form-group row ">
                                                <input type="hidden" name="edit_invoice_stock_check"
                                                    value="edit_invoice_stock_check" id="edit_invoice_stock_check">

                                                <input type="hidden" class="form-control input-sm" name="invoice_number"
                                                    id="invoice_number" required=""
                                                    value="<?php echo $invoice_data_group['invoice_number']; ?>">
                                                <label for="inputEmail3" name="invoice_number" id="invoice_number"
                                                    class="col-sm-12 control-label">
                                                    <h2> Voucher:<b>
                                                            <?php echo $invoice_data_group['invoice_number']; ?></b>
                                                    </h2>
                                                </label>
                                            </div>
                                        </div>


                                        <div class="col-md-3">

                                            <div class="form-group row ">
                                                <label for="inputEmail3" class="col-sm-4 control-label"> Date<span
                                                        style="color: red;">*</span></label>
                                                <div class="col-sm-8">
                                                    <input type="text"
                                                        class="form-control alldate input-sm created-date"
                                                        name="credit_date" id="credit_date"
                                                        value="<?php echo $invoice_data_group['credit_date']; ?>"
                                                        required="" onkeydown="return false;">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group row ">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Doc
                                                    Date</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control date input-sm"
                                                        name="doc_date" id="doc_date"
                                                        value="<?php echo $invoice_data_group['doc_date']; ?>"
                                                        onkeydown="return false;">
                                                </div>
                                            </div>


                                        </div>
                                    </div>


                                    <div class="col-md-3">
                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-4 control-label">Doc No</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control input-sm" name="doc_no"
                                                    id="doc_no" value="<?php echo $invoice_data_group['doc_no']; ?>">
                                            </div>
                                        </div>






                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group row">
                                            <label for="type" class="col-sm-4 control-label">Type</label>
                                            <div class="col-sm-8">
                                                <select class="form-control input-sm" name="type" id="type">
                                                    <option value="None"
                                                        <?php if ($invoice_data_group['type'] == 'None') echo 'selected'; ?>>
                                                        None</option>
                                                    <option value="GST"
                                                        <?php if ($invoice_data_group['type'] == 'GST') echo 'selected'; ?>>
                                                        GST</option>
                                                   
                                                    <option value="VAT"
                                                        <?php if ($invoice_data_group['type'] == 'VAT') echo 'selected'; ?>>
                                                        VAT</option>
                                                    <option value="Export"
                                                        <?php if ($invoice_data_group['type'] == 'Export') echo 'selected'; ?>>
                                                        Export</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>




                                    <div class="row">
                                        <div class="col-xs-12">
                                            <div class="box-header">
                                                <div class="table-responsive">

                                                    <table class="table table-bordered" id="dynamic_field">
                                                        <th>Voucher Type</th>
                                                        <th>Account Name</th>
                                                        <th>Credit</th>
                                                        <th>Debit</th>
                                                        <th>No</th>
                                                        <th>Action</th>
                                                        <tr>
                                                            <td>
                                                                <select style="width: 150px"
                                                                    class="form-control input-sm" name="acc_type"
                                                                    id="acc_type" required="">
                                                                    <option
                                                                        value="<?php echo $invoice_data_group['acc_type']; ?>"
                                                                        <?php if ($invoice_data_group['acc_type'] == 'Cr') echo 'selected'; ?>>
                                                                        Cr</option>
                                                                    <option
                                                                        value="<?php echo $invoice_data_group['acc_type']; ?>"
                                                                        <?php if ($invoice_data_group['acc_type'] == 'Db') echo 'selected'; ?>>
                                                                        Db</option>
                                                                    <option
                                                                        value="<?php echo $invoice_data_group['acc_type']; ?>"
                                                                        <?php if ($invoice_data_group['acc_type'] == 'Voucher') echo 'selected'; ?>>
                                                                        Journal Voucher</option>

                                                                </select>

                                                            </td>
                                                            <td>
                                                                <select class="col-md-12 company_search_name"
                                                                    name="company_name" id="company_name" required
                                                                    id="">
                                                                    <option value="">Select Company</option>
                                                                    <?php
                                                        $company_name = $invoice_data_group['company_name'];

                                                        foreach ($customer_result as $row) {
                                                            ?>
                                                                    <option value="<?php echo $row->company_name ?>" <?php
                                                            if ($company_name == $row->company_name) {
                                                                echo 'selected="selected"';
                                                            }
                                                            ?>><?php echo $row->company_name; ?></option>
                                                                    <?php }
                                                                ?>
                                                                </select>
                                                                <!-- <span data-toggle="modal" data-target="#myModal"><i
                                                                            class="glyphicon glyphicon-plus"></i>Add
                                                                    </span> -->

                                                            </td>
                                                            <td><input type="text" name="credit_amt" id="credit_amt"
                                                                    value="<?php echo $invoice_data_group['credit_amt']; ?>"
                                                                    class="form-control input-sm"></td>

                                                            <td> <input type="text" id="debit_amt" name="debit_amt"
                                                                    value="<?php echo $invoice_data_group['debit_amt']; ?>"
                                                                    class="form-control input-sm"></span>
                                                            </td>
                                                            <td><input type="text" name="credit_no" id="credit_no"
                                                                    value="<?php echo $invoice_data_group['credit_no']; ?>"
                                                                    required=""
                                                                    class="form-control input-sm required_list name_list quantity_auto number-only-validation"
                                                                    value="" autocomplete="off" /></td>

                                                            <td><button type="button" name="add_new" id="add_new"
                                                                    class="btn btn-success"><i class="fa fa-plus-circle"
                                                                        aria-hidden="true"></i></button></td>
                                                        </tr>
                                                    </table>
                                                </div>
                                                <div align="center">

                                                    <button type="submit" name="submit" id="submit"
                                                        class="btn btn-success">Save</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>






                                    <!-- <div class="col-md-3">
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Company<span
                                                        style="color: red;">*</span></label>
                                                <div class="col-sm-8">
                                                    <select class="col-md-12 company_search_name" name="customer_id"
                                                        id="customer_id" required id="">
                                                        <option value="">Select Company</option>
                                                        <?php
                                                        $company_name = $invoice_data_group['company_name'];

                                                        foreach ($customer_result as $row) {
                                                            ?>
                                                        <option value="<?php echo $row->company_name ?>" <?php
                                                            if ($company_name == $row->company_name) {
                                                                echo 'selected="selected"';
                                                            }
                                                            ?>><?php echo $row->company_name; ?></option>
                                                        <?php }
                                                                ?>
                                                    </select>

                                                </div>

                                            </div> -->

                                    <!-- <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Status</label>

                                                <div class="col-sm-8">
                                                    <select class="form-control input-sm " name="status" id="status">
                                                        <?php if ($status_result[0]->status == 1) { ?>
                                                        <option value="1" selected="">Draft</option>
                                                        <option value="2">Sent</option>
                                                        <option value="3">Viewed</option>
                                                        <option value="4">Approved</option>
                                                        <option value="5">Rejected</option>
                                                        <option value="6">Canceled</option>
                                                        <?php } ?>
                                                        <?php if ($status_result[0]->status == 2) { ?>
                                                        <option value="1">Draft</option>
                                                        <option value="2" selected="">Sent</option>
                                                        <option value="3">Viewed</option>
                                                        <option value="4">Approved</option>
                                                        <option value="5">Rejected</option>
                                                        <option value="6">Canceled</option>
                                                        <?php } ?>
                                                        <?php if ($status_result[0]->status == 3) { ?>
                                                        <option value="1">Draft</option>
                                                        <option value="2">Sent</option>
                                                        <option value="3" selected="">Viewed</option>
                                                        <option value="4">Approved</option>
                                                        <option value="5">Rejected</option>
                                                        <option value="6">Canceled</option>
                                                        <?php } ?>
                                                        <?php if ($status_result[0]->status == 4) { ?>
                                                        <option value="1">Draft</option>
                                                        <option value="2">Sent</option>
                                                        <option value="3">Viewed</option>
                                                        <option value="4" selected="">Approved</option>
                                                        <option value="5">Rejected</option>
                                                        <option value="6">Canceled</option>
                                                        <?php } ?>
                                                        <?php if ($status_result[0]->status == 5) { ?>
                                                        <option value="1" selected="">Draft</option>
                                                        <option value="2">Sent</option>
                                                        <option value="3">Viewed</option>
                                                        <option value="4">Approved</option>
                                                        <option value="5" selected="">Rejected</option>
                                                        <option value="6">Canceled</option>
                                                        <?php } ?>
                                                        <?php if ($status_result[0]->status == 6) { ?>
                                                        <option value="1">Draft</option>
                                                        <option value="2">Sent</option>
                                                        <option value="3">Viewed</option>
                                                        <option value="4">Approved</option>
                                                        <option value="5">Rejected</option>
                                                        <option value="6" selected="">Canceled</option>
                                                        <?php } ?>

                                                    </select>
                                                </div>
                                            </div>

                                            <div class="form-group row hide">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Payment
                                                    Method</label>
                                                <div class="col-sm-8">
                                                    <select class="form-control input-sm " name="payment_method"
                                                        id="payment_method">
                                                        <option value="">Select Payment Method</option>
                                                        <option value="1">Cash</option>
                                                        <option value="2">Cheque</option>
                                                        <option value="3">NetBanking</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="form-group row ">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Customer
                                                    PO</label>
                                                <div class="col-sm-8">
                                                    <input type="text" style="text-transform: uppercase;"
                                                        class="form-control input-sm" name="invoice_customer_po"
                                                        id="invoice_customer_po"
                                                        value="<?php echo $invoice_data_group['customer_po']; ?>">
                                                </div>
                                            </div>

                                            <div class="form-group row ">
                                                <label for="inputEmail3" class="col-sm-4 control-label">PO Date</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control date input-sm"
                                                        name="invoice_po_date" id="invoice_po_date"
                                                        value="<?php echo $invoice_data_group['po_date']; ?>"
                                                        onkeydown="return false;">
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Dispatch
                                                    through</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control input-sm"
                                                        name="despatch_through" id="despatch_through"
                                                        value="<?php echo $invoice_data_group['despatch_through']; ?>">
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Vehicle
                                                    No</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control input-sm" name="vehicle_no"
                                                        id="vehicle_no"
                                                        value="<?php echo $invoice_data_group['vehicle_no']; ?>">
                                                </div>
                                            </div>

                                        </div>
                                        <div class="col-md-3">

                                            <div class="form-group row ">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Invoice
                                                    Date<span style="color: red;">*</span></label>
                                                <div class="col-sm-8">
                                                    <input type="text"
                                                        class="form-control holedate input-sm created-date"
                                                        name="invoice_date" id="invoice_date" required=""
                                                        value="<?php $newDate = date("d-m-Y", strtotime($invoice_data_group['invoice_date']));  echo $newDate ;?>"
                                                        onkeydown="return false;">
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Due Date<span
                                                        style="color: red;">*</span></label>
                                                <div class="col-sm-8">
                                                    <input type="text"
                                                        class="form-control holedate input-sm payment-due-date-check "
                                                        name="payment_due_date" id="payment_due_date" autocomplete="off"
                                                        required=""
                                                        value="<?php echo $invoice_data_group['payment_due_date']; ?>"
                                                        onkeydown="return false;">
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Delivery
                                                    Date</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control holedate input-sm"
                                                        name="delivery_date" id="delivery_date"
                                                        value="<?php echo $invoice_data_group['delivery_date']; ?>"
                                                        onkeydown="return false;">
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Note</label>
                                                <div class="col-sm-7">
                                                    <textarea class="form-control" name="note" id="note"
                                                        rows="2"><?php echo $invoice_data_group['note']; ?></textarea>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Delivery Note
                                                    No</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control input-sm"
                                                        name="delivery_note_no" id="delivery_note_no"
                                                        value="<?php echo $invoice_data_group['delivery_note_no']; ?>">
                                                </div>
                                            </div>

                                        </div>

                                        <div class="col-md-6">

                                            <div class="form-group row hide">
                                                <label for="inputEmail3" id="subheading1"
                                                    class="col-sm-4 control-label">Subheading</label>
                                                <div class="col-sm-8">
                                                    <input type="text"
                                                        value="<?php echo $invoice_data_group['invoice_subheading']; ?>"
                                                        class="form-control" name="invoice_subheading"
                                                        id="invoice_subheading">
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Footer</label>
                                                <div class="col-sm-8">
                                                    <textarea class="form-control" name="invoice_footer"
                                                        id="invoice_footer"
                                                        rows="3"><?php echo $invoice_data_group['invoice_footer']; ?></textarea>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Shipping
                                                    To</label>
                                                <div class="col-sm-8">
                                                    <textarea class="form-control" name="shipping_address"
                                                        id="shipping_address"
                                                        rows="3"><?php echo $invoice_data_group['shipping_address']; ?></textarea>
                                                </div>
                                            </div>


                                            <div class="form-group row hide">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Sales Person
                                                    Name</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control input-sm" name="sales_person"
                                                        id="sales_person"
                                                        value="<?php echo $invoice_data_group['sales_person']; ?>">
                                                </div>
                                            </div>

                                            <div class="form-group row hide">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Memo</label>
                                                <div class="col-sm-8">
                                                    <textarea class="form-control" name="invoice_memo" id="invoice_memo"
                                                        rows="3"><?php echo $invoice_data_group['invoice_memo']; ?></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div> -->
                                    <!-- <div class="table-responsive">


                                        <table class="table table-bordered" id="dynamic_field">
                                            <tr>
                                                <th>Item</th>
                                                <th>Description</th>
                                                <th>QTY</th>
                                                <th>UNIT</th>
                                                <th>HSN Code</th>
                                                <th class="gst_per">TAX(%)</th>
                                                <th class="gst">SGST</th>
                                                <th class="gst">CGST</th>
                                                <th class="igst_edit_hide_show">IGST</th>
                                                <th>Price/Unit</th>
                                                <th>Discount(%)</th>
                                                <th>Amount</th>
                                                <th>Action</th>
                                            </tr>

                                            <?php
                                            $i = 1;
                                            foreach ($show_invoice as $key) {
                                                ?>
                                            <tr>
                                                <td>
                                                    <select style="width: 150px"
                                                        class="form-control input-sm product_name_auto item_search_name name_list"
                                                        name="term[]" id="item_name<?php echo $i; ?>"
                                                        onchange="myFunction1(this.id)" required=""
                                                        data-live-search="true">
                                                        <option></option>
   <option value="NEW">+ Add New Product</option>
                                                        <?php
                                                            foreach ($item_name as $row) {
                                                                ?>
                                                        <option value="<?php echo $row->code ?>" <?php
                                                                if ($key->product_name == $row->code) {
                                                                    echo 'selected="selected"';
                                                                }
                                                                ?>><?php echo $row->code . " - " . $row->item_name; ?></option>
                                                        <?php }
                                                                    ?>

                                                    </select>

                                                    <input type="hidden" class="form-control input-sm"
                                                        name="invoice_id[]" id="quotation_id<?php echo $i; ?>"
                                                        value="<?php echo $key->invoice_id; ?>">
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-info"
                                                        onClick="descButton(this.id)"
                                                        id="btnDescriptionId<?php echo $i; ?>">description</button>

                                                    <textarea style="width: 150px"
                                                        class="form-control input-sm name_list description_auto hide"
                                                        name="description[]" id="description<?php echo $i; ?>"
                                                        rows="7"><?php echo $key->description; ?></textarea>
                                                </td>
                                                <td class="hide"> <span id="total_quantity<?php echo $i; ?>"
                                                        name="total_quantity[]"></span></td>
                                                <td><input type="text" name="quantity[]"
                                                        value="<?php echo $key->quantity; ?>"
                                                        id="quantity<?php echo $i; ?>" required=""
                                                        class="form-control input-sm name_list quantity_auto number-only-validation"
                                                        value="1" /></td>
                                                <td>
                                                    <select style="width: 100px"
                                                        class="form-control input-sm  item_search_unit" name="unit[]"
                                                        id="unit1" required="" data-live-search="true">
                                                        <option value="<?php echo $key->unit ?>">
                                                            <?php echo $key->unit; ?></option>
                                                    </select>
                                                </td>
                                                <td><input type="text" name="hsn[]"
                                                        value="<?php echo $key->hsn_code; ?>" id="hsn<?php echo $i; ?>"
                                                        required="" class="form-control input-sm name_list"
                                                        readonly="" /></td>
                                                <td class="gst_per"><input type="text" readonly="" name="gst_per[]"
                                                        value="<?php echo $key->gst; ?>" id="gst_per<?php echo $i; ?>"
                                                        class="form-control input-sm name_list" /></td>
                                                <td class="gst"><input type="text" readonly="" name="sgst[]"
                                                        value="<?php echo $key->sgst; ?>" id="sgst<?php echo $i; ?>"
                                                        class="form-control input-sm sgst_list" /></td>
                                                <td class="gst"><input type="text" readonly="" name="cgst[]"
                                                        value="<?php echo $key->cgst; ?>" id="cgst<?php echo $i; ?>"
                                                        class="form-control input-sm cgst_list" /></td>
                                                <td class="igst_edit_hide_show"><input type="text" readonly=""
                                                        name="igst[]" value="<?php echo $key->igst; ?>"
                                                        id="igst<?php echo $i; ?>"
                                                        class="form-control input-sm igst_list" /></td>
                                                <td><input type="text" name="price[]" value="<?php echo $key->price; ?>"
                                                        id="price<?php echo $i; ?>" required=""
                                                        class="form-control input-sm price_auto" /></td>
                                                <td><input type="number" min="0" maxlength="2" name="discount[]"
                                                        value="<?php echo $key->discount; ?>"
                                                        id="discount<?php echo $i; ?>"
                                                        class="form-control input-sm name_list discount_auto number-only-validation"
                                                        value="" /></td>
                                                <td><input type="hidden" name="amount[]" id="amount<?php echo $i; ?>"
                                                        class="form-control input-sm name_list amount_auto"
                                                        value="<?php echo $key->amount; ?>" />
                                                    <input type="hidden" name="amount_temp[]"
                                                        id="amount_temp<?php echo $i; ?>"
                                                        class="form-control input-sm name_list amount_auto"
                                                        value="<?php echo $key->price * $key->quantity; ?>" />
                                                    <input type="hidden" name="gst_amount[]"
                                                        id="gst_amount<?php echo $i; ?>"
                                                        class="form-control input-sm name_list gst_amount_auto"
                                                        value="0.00" />
                                                    <span id="span_amount<?php echo $i; ?>"
                                                        name="span_amount[]">₹<?php echo $key->amount; ?></span>
                                                </td>
                                                <td>
                                                    <?php if ($i == 1) { ?>


                                                    <?php if ($key->gst_type != 'S') { ?>
                                                    <input type="hidden" name="quotation_igst_check"
                                                        value="igst_edit_hide_show" id="quotation_igst_check">

                                                    <input type="hidden" name="igst_edit_hide_show"
                                                        value="igst_edit_hide_show" id="igst_edit_hide_show">
                                                    <button type="button" name="edit_igst" id="edit_igst"
                                                        class="btn btn-success"><i class="fa fa-plus-circle"
                                                            aria-hidden="true"></i></button>

                                                    <?php } else if ($key->gst_type != 'I') { ?>
                                                    <input type="hidden" name="gst" value="gst" id="gst">
                                                    <input type="hidden" name="edit_sgst_cgst_check"
                                                        value="edit_sgst_cgst_check" id="edit_sgst_cgst_check">
                                                    <input type="hidden" name="gst_discount_check" value="gst"
                                                        id="gst_discount_check">
                                                    <button type="button" name="edit_gst" id="edit_gst"
                                                        class="btn btn-success"><i class="fa fa-plus-circle"
                                                            aria-hidden="true"></i></button>

                                                    <?php } else if (($key->gst_type != 'S') || ($key->gst_type != 'I')) { ?>
                                                    <input type="hidden" name="non_gst" value="non_gst" id="non_gst">
                                                    <button type="button" name="edit_non_gst" id="edit_non_gst"
                                                        class="btn btn-success"><i class="fa fa-plus-circle"
                                                            aria-hidden="true"></i></button>
                                                    <?php } ?>


                                                    <?php } else { ?>
                                                    <button type="button" name="remove" title="edit_invoice"
                                                        id="remove<?php echo $i; ?>"
                                                        class="btn btn-danger btn_remove">X</button>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                            <?php
                                                $i++;
                                            }
                                            ?>

                                        </table>
                                        <div align="center">
                                            <button type="submit" name="submit" id="submit"
                                                class="btn btn-success">Save</button>
                                        </div> -->


                                    <!-- <div align="right" style="margin: 10px">

                                            <input type="hidden" name="total_before_tax" id="total_before_tax"
                                                class="form-control input-sm name_list" value="0.00" />
                                            <input type="hidden" name="total_gst_amount" id="total_gst_amount"
                                                class="form-control input-sm name_list" value="0.00" />

                                            total_gst_amount
                                            <span id="total_amount" name="total_amount[]"
                                                class="total_span_auto_amount">Total: ₹0.00</span><br>
                                            <span class="gst" id="sgst_amount" name="sgst_amount[]">SGST Amount:
                                                ₹0.00</span><br>
                                            <span class="gst" id="cgst_amount" name="cgst_amount[]">CGST Amount:
                                                ₹0.00</span><br>
                                            <span class="igst_hide" id="igst_amount" name="igst_amount[]">IGST Amount:
                                                ₹0.00</span><br>
                                            <span id="grand_total_amount"><b>Grand Total:</b> ₹0.00</span><br>
                                            <b> Grand Total in Words:<span id="word2" name="word2"></span>Only.</b><br>

                                            <input type="hidden" name="total_quotation_amount"
                                                id="total_quotation_amount" class="form-control input-sm name_list"
                                                value="0.00" />
                                        </div> -->
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

    <div class="control-sidebar-bg"></div>
    <?php $this->load->view('admin/footer'); ?>
    </div>
    <!-- ./wrapper -->