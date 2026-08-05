<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (isset($session_data_head1)) {
    
} else {
    header($this->config->item('header'));
}
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(APPPATH."views/admin/modal.php"); 

?>
<style>
.required label {
    font-weight: bold;
}

.required label:after {
    color: #e32;
    content: ' *';
    display: inline;
}
</style>

<body class="hold-transition skin-blue sidebar-mini">
    <div id="loader" class="center"></div>
    <div class="wrapper">

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <!-- <h1>
                    Invoice
                </h1> -->
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/'?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <!-- <li><a href="#">Invoice</a></li>
                    <li class="active">Invoice Details</li> -->
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">

                <div class="row" style="padding:2%">
                    <div class="pull-left">

                        <!--                        <div class="col-md-2">
                            <a href="<?php //echo base_url(); ?>InvoiceController/create_non_gst_invoice"  class="btn btn-primary" role="button">Non GST</a>
                        </div>-->
                        <div class="pull-right">
                            <!-- <div class="col-md-6">
                                <a href="<?php echo base_url(); ?>InvoiceController/create_central_gst_invoice" id="central_gst" name="central_gst" class="btn btn-primary" role="button">IGST</a>
                            </div> -->

                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-info">
                            <div class="box-header">
                                <h3 class="box-title">Credit/Debit Note</h3>

                                <!-- <div class="col-md-3"> -->
                                        
                                    <!-- </div> -->
                            </div>
                            <!-- /.box-header -->
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
                            <?php } ?><div class="box-body">

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


                                <a href="<?php echo base_url(); ?>CreditnoteController/index?str=All"><button
                                                class="btn btn-success btn-sm" style="margin-left:500px;"> Show All Invoice</button></a>
                                        <!-- <a href="<?php echo base_url(); ?>CreditnoteController/create_note"><button
                                                class="btn btn-success btn-sm"><i
                                                    class="glyphicon glyphicon-plus"></i>Create Invoice</button></a> -->

                                <form class="form-horizontal form_overlay" name="add_name" id="add_name" method="post"
                                    action="<?php echo base_url(); ?>CreditnoteController/add_invoice"
                                    enctype="multipart/form-data">
                                    <div class="row">

                                        <div class="col-md-12">
                                            <div class="form-group row ">



                                                <input type="hidden" class="form-control input-sm" name="invoice_number"
                                                    id="invoice_number" required=""
                                                    value="<?php printf($credit_id + 1); ?>">


                                                <label for="inputEmail3" name="invoice_number" id="invoice_number"
                                                    class="col-sm-12 control-label">
                                                    <h2>Voucher No:<b><?php printf( $credit_id + 1); ?></b>
                                                    </h2>
                                                </label>


                                            </div>

                                            <div class="col-md-3">

                                                <div class="form-group row ">
                                                    <label for="inputEmail3" class="col-sm-4 control-label"> Date<span
                                                            style="color: red;">*</span></label>
                                                    <div class="col-sm-8">
                                                        <input type="text"
                                                            class="form-control alldate input-sm created-date"
                                                            name="credit_date" id="credit_date" required=""
                                                            onkeydown="return false;">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="form-group row ">
                                                    <label for="inputEmail3" class="col-sm-4 control-label">Doc
                                                        Date</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" class="form-control date input-sm"
                                                            name="doc_date" id="doc_date" onkeydown="return false;">
                                                    </div>
                                                </div>


                                            </div>
                                        </div>


                                        <div class="col-md-3">
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Doc No</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control input-sm" name="doc_no"
                                                        id="doc_no">
                                                </div>
                                            </div>


                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Type</label>
                                                <div class="col-sm-8">
                                                    <select class="form-control input-sm" name="type" id="type">
                                                        <option value="None">None</option>
                                                        <option value="GST">GST</option>
                                                       
                                                        <option value="VAT">VAT</option>
                                                        <option value="Export">Export</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>



                                        <div class="row">
                                            <div class="col-xs-12">
                                                <div class="box-header">
                                                    <div class="table-responsive">

                                                        <table class="table table-bordered" id="dynamic_field">
                                                            <th>Cr/Db</th>
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
                                                                        <option value="Cr">Cr</option>
                                                                        <option value="Db">Db</option>
                                                                        <option value="Voucher">Journal Voucher</option>

                                                                    </select>

                                                                </td>
                                                                <td>
                                                                    <select
                                                                        class="form-control input-sm company_search_name"
                                                                        name="company_name" id="company_name" required="">
                                                                        <option value=""></option>
                                                                        <option value="">Select Account</option>
                                                                        <?php foreach ($company_name as $key) { ?>
                                                                        <option
                                                                            value="<?php echo $key->company_name; ?>">
                                                                            <?php echo $key->company_name; ?></option>
                                                                        <?php } ?>
                                                                    </select><br>
                                                                    <span data-toggle="modal" data-target="#myModal"><i
                                                                            class="glyphicon glyphicon-plus"></i>Add
                                                                    </span>

                                                                </td>
                                                                <td><input type="text" name="credit_amt" id="credit_amt"
                                                                         class="form-control input-sm"></td>

                                                                <td> <input type="text" id="debit_amt" name="debit_amt"
                                                                        
                                                                        class="form-control input-sm"></span></td>
                                                                <td><input type="text" name="credit_no" id="credit_no"
                                                                        required=""
                                                                        class="form-control input-sm required_list name_list quantity_auto number-only-validation"
                                                                        value="" autocomplete="off" /></td>

                                                                <td><button type="button" name="add_new" id="add_new"
                                                                        class="btn btn-success"><i
                                                                            class="fa fa-plus-circle"
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
    <!-- ./Customer modal -->
    <div id="myModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
                <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <center>                    <h4 class="modal-title">Add Company</h4>
                    </center>

                </div>
                <!--<form class="form-horizontal form_overlay" method="post" action="<?php echo base_url(); ?>InvoiceController/add_customer" enctype="multipart/form-data">-->
                <div class="modal-body">

                    <div class="card-body ">

                        <!-- form start -->
                        <div class="form-group row required">
                            <label for="inputEmail3" class="col-sm-4 control-label">Company Name</label>
                            <div class="col-sm-5">
                                <input type="hidden" name="gst_check_customer" value="gst_check_customer"
                                    id="gst_check_customer">
                                <input type="text" class="form-control input-sm" name="company_name" id="company_name"
                                    required="">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="inputEmail3" class="col-sm-4 control-label">Customer Name</label>
                            <div class="col-sm-5">
                                <input type="text" class="form-control input-sm name-validation" name="fullname"
                                    id="fullname">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="inputEmail3" class="col-sm-4 control-label"> PAN No</label>
                            <div class="col-sm-5">
                                <input type="text" maxlength="10" class="form-control input-sm " name="pancard"
                                    id="pancard" style="text-transform: uppercase;">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="inputEmail3" class="col-sm-4 control-label"> GST No</label>
                            <div class="col-sm-5">
                                <input type="text" maxlength="15" class="form-control input-sm" name="gst" id="gst"
                                    style="text-transform: uppercase;">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="inputEmail3" class="col-sm-4 control-label"> Email</label>
                            <div class="col-sm-5">
                                <input type="text" class="form-control input-sm" name="email" id="email" />
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="inputEmail3" class="col-sm-4 control-label">Mobile</label>
                            <div class="col-sm-5">
                                <input type="tel" class="form-control input-sm" name="mobile" id="mobile" maxlength="10"
                                    onkeyup="if (/\D/g.test(this.value))
                                                   this.value = this.value.replace(/\D/g, '')" />
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="inputEmail3" class="col-sm-4 control-label"> State Code</label>
                            <div class="col-sm-5">
                                <input type="text" class="form-control input-sm" name="state_code" id="state_code">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="inputEmail3" class="col-sm-4 control-label"> Address</label>
                            <div class="col-sm-5">
                                <textarea class="form-control input-sm" name="address" id="address"></textarea>
                            </div>
                        </div>

                    </div>

                </div>
                <div class="modal-footer">
                    <button type="submit" id="btnSave" class="btn btn-success performa_submit">Submit</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i
                            class="glyphicon glyphicon-remove"></i> Close</button>
                </div>
                <!--</form>-->

            </div>

        </div>
    </div>


    <script>
    $('#add_new').click(function() {
        i++;
        var product_code_result;
        var product_code = '';

        $.ajax({
            type: "GET",
            url: base_url + "InventoryController/get_all_product", //get_all_products_code
            data: dataString,
            cache: false,
            success: function(data) {
                product_code_result = jQuery.parseJSON(data);
                product_code = '<option></option>';
                product_code += '<option value="NEW">Add new type</option>';
                for (var n = 0; n < product_code_result.length; n++) {

                    product_code += '<option value="' + product_code_result[n].code + '">' +
                        product_code_result[n].code + '</option>';

                }

                $(document).ready(function() {
                    $(".item_search_name").select2({
                        data: available_item_name,
                        placeholder: "Select Item"
                    });
                });

                var unit_name;

                $(".item_search_unit").select2({
                    data: unit_name,
                    placeholder: "Select Unit"
                });

                $.ajax({
                    type: "GET",
                    url: base_url + "UnitController/get_unit_name",
                    cache: false,
                    success: function(data) {
                        unit_result = jQuery.parseJSON(data);


                        unit_name = '<option></option>';

                        for (var n = 0; n < unit_result.length; n++) {


                            unit_name += '<option value="' + unit_result[n].unit +
                                '">' + unit_result[n].unit + '</option>';
                        }

                        $(".item_search_unit").append(unit_name).trigger('change');

                    }
                });


                $('#dynamic_field').append('<tr id="row' + i +
                    '"><td><select style="width: 150px" class="form-control input-sm product_name_auto item_search_name name_list add_new_product"  name="term[]" id="item_name' +
                    i +
                    '" onchange="myFunction1(this.id)" required="" data-live-search="true"> ' +
                    product_code +
                    '</select>' +
                    '<input type="hidden" class="form-control input-sm" name="quotation_id[]" id="quotation_id' +
                    i + '"  value=""></td>' +
                    '<td>      <button type="button" class="btn btn-info " onClick="descButton(this.id)" id="btnDescriptionId' +
                    i +
                    '">description</button><textarea style="width: 150px;"  class="form-control input-sm name_list description_auto hide" name="description[]" id="description' +
                    i + '" rows="4">  </textarea></td>' +
                    '<td><input type="text" id="hsn' + i +
                    '" name="hsn[]" required="" class="form-control input-sm name_list hsn_auto" /></td>' +
                    '<td> <span class="hide" id="total_quantity' + i +
                    '" name="total_quantity[]"></span> <input type="hidden" name="total_quantity1[]" id="total_quantity" class="form-control input-sm name_list total_quantity_auto"  /> <input type="text" id="quantity' +
                    i +
                    '" name="quantity[]" required="" class="form-control input-sm name_list quantity_auto number-only-validation" value="1" /></td>' +
                    '<td><select style="width: 100px" class="form-control input-sm item_search_unit"  name="unit[]" id="unit' +
                    i + '"  required="" data-live-search="true"> </select></td>' +
                    '<td><input type="text" readonly="" id="gst_per' + i +
                    '" name="gst_per[]"  class="form-control input-sm name_list gst_per_auto" /></td>' +
                    '<td><input type="text" readonly="" name="sgst[]" id="sgst' + i +
                    '" class="form-control input-sm sgst_list" /></td>' +
                    '<td><input type="text" readonly="" name="cgst[]" id="cgst' + i +
                    '" class="form-control input-sm cgst_list" /></td>' +
                    '<td  class="hide"><input type="text" readonly="" name="igst[]" id="igst' +
                    i + '" class="form-control input-sm igst_list" /></td>' +
                    '<td><input type="text"  id="price' + i +
                    '" name="price[]" required="" class="form-control input-sm name_list price_auto" value="0.00" /></td>' +
                    '<td><input type="number" min="0" maxlength="2" name="discount[]" id="discount' +
                    i +
                    '" class="form-control input-sm name_list discount_auto number-only-validation" value="" /></td>' +
                    '<td><input type="hidden" id="amount' + i +
                    '" name="amount[]"  class="form-control input-sm name_list amount_auto" value="0.00" />' +
                    '<input type="hidden" name="amount_temp[]" id="amount_temp' + i +
                    '" class="amount_auto" value="0.00" />' +
                    '<input type="hidden" name="gst_amount[]" id="gst_amount' + i +
                    '" class="form-control input-sm name_list gst_amount_auto" value="0.00" />' +
                    '<span id="span_amount' + i +
                    '" name="span_amount[]" class="total_span_auto_amount">₹0.00</span></td><td><button type="button" name="remove" id="remove' +
                    i + '" class="btn btn-danger btn_remove">X</button></td></tr>');

            }
        });
    });
    </script>

    <script type="text/javascript">
    document.getElementById('item_name1').open();
    </script>