<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (isset($session_data_head1)) {
    
} else {
    header($this->config->item('header'));
}
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!-- Search and Dropdown -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/js/select2.min.js"></script>
<!-- ./Search and Dropdown -->
<body class="hold-transition skin-blue sidebar-mini">
    <div class="wrapper">

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    Delivered Raw Item
                </h1>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box">
                            <div class="box-header">
                                <h3 class="box-title">Add Delivered Raw Item</h3><br><br>
                                <span id="error" style="color:red;display:none">Plese Enter Only Alphabets...</span>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">

                               

                                <?php if ($this->session->flashdata('INFOMSG')) { ?>
                                    <div role="alert" class="alert alert-info">
                                        <button data-dismiss="alert" class="close" type="button"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                                        <strong>Ooh!!</strong> <?= $this->session->flashdata('INFOMSG') ?>
                                    </div>
                                <?php } ?>

                                <form class="form-horizontal" id="form_overlay" method="post" action="<?php echo base_url(); ?>PlanningController/add_delivered_item">

                                    <div class="row">
                                        <div class="col-md-6">
                                            <input type="text" id="batch_date" class="alldate input-sm" name="batch_date" required="">-Batch-<?php echo $batch['batch'] + 1; ?> is processing
                                        </div>
                                        <div class="col-md-6">
                                            <label for="inputEmail3" class="col-sm-4 control-label">Batch Description</label>
                                            <input type="text" id="batch_description" class="input-sm col-sm-8" name="batch_description" required="">
                                        </div>
                                    
                                    </div>
                                    
                                    
                                        <br>
                                        <input type="hidden" name="batch" value="<?php echo $batch['batch'] + 1; ?>">
                                        <div class="table-responsive">  

                                            <table class="table table-bordered" id="dynamic_field">  
                                                <th>Item Name</th>
                                                <th>Item Quantity</th>
                                                <th>Item Unit</th>
                                                <th>Action</th>
                                                <tr>  
                                                    <td>
                                                        <select style="width: 150px" class="form-control input-sm item_search_name_planning"  name="raw_item_name[]" id="item_name_plan1" required="" data-live-search="true">
                                                            <option></option>
                                                            <?php foreach ($raw_items as $key) { ?>
                                                                <option value="<?php echo $key->raw_item_master_name; ?>"><?php echo $key->raw_item_master_name; ?></option> 
                                                            <?php } ?>  
                                                        </select>
                                                        <!--<input type="text" name="term[]" required="" id="item_name1" class="form-control input-sm required_list name_list product_name_auto" />-->
                                                    </td> 
                                                    <td><input type="text" name="raw_item_qty[]"  class="form-control input-sm" /></td> 

                                                    <td><input type="text" name="raw_item_unit[]" required="" class="form-control input-sm "/></td> 

                                                    <td><button type="button" accesskey="n" name="plus_button" id="plus_button" class="btn btn-success"><i class="fa fa-plus-circle" aria-hidden="true"></i></button></td>  
                                                </tr>  
                                            </table>  
                                            <div align="center">

                                                <button type="submit" name="submit" id="submit"  class="btn btn-success">Save</button>
                                            </div>

                                        </div> 
                                </form>

                            </div>
                        </div>
                        <!-- /.box-body -->
                    </div>
                    <!-- /.box -->
                </div>
                <!-- /.col -->

            </section>
            <!-- /.content -->
        </div>
        <!-- /.row -->

    </div>

    <div class="control-sidebar-bg">

        <?php $this->load->view('admin/footer'); ?>
    </div>

    <!-- ./wrapper -->
    <script>

        $(document).ready(function () {
            var available_item_name;
            var dataString;

            $(function () {

                $(".item_search_name_planning").select2({
                    data: available_item_name,
                    placeholder: "Select Item"
                });
            });
            //var available_item_name;
            var i = 1;
            var base_url = window.location.origin + '/' + window.location.pathname.split('/') [1] + '/';
            //var base_url = 'http://lakshmi.xformtechnologies.com/';

            $('#plus_button').click(function () {
                //       alert('hii');
                i++;
                var product_code_result;
                var product_code = '';

                $.ajax({
                    type: "GET",
                    url: base_url + "PlanningController/get_products_for_json",
                    data: dataString,
                    cache: false,
                    success: function (data)
                    {
                        //                alert(data);
                        product_code_result = jQuery.parseJSON(data);
                        product_code = '<option></option>';
                        for (var n = 0; n < product_code_result.length; n++) {

                            product_code += '<option value="' + product_code_result[n].raw_item_master_name + '">' + product_code_result[n].raw_item_master_name + '</option>';

                        }

                        $(document).ready(function () {
                            $(".item_search_name_planning").select2({
                                data: available_item_name,
                                placeholder: "Select PartCode"
                            });
                        });

                        $('#dynamic_field').append('<tr id="row' + i + '"><td><select style="width: 150px" class="form-control input-sm item_search_name_planning"  name="raw_item_name[]" id="item_name_plan' + i + '"required=""> ' +
                                product_code +
                                '</select>' +
                                '</td>' +
                                '<td><input type="text" name="raw_item_qty[]" class="form-control input-sm" /></td>' +
                                '<td><input type="text" name="raw_item_unit[]" required="" class="form-control input-sm"/></td>' +
                                '<td><button type="button" name="remove" id="remove' + i + '" class="btn btn-danger btn_remove">X</button></td></tr>');
                    }
                });
            });
        });

    </script>

