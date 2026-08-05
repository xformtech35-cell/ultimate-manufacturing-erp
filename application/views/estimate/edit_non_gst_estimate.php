<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (isset($session_data_head1)) {
    
} else {
    header($this->config->item('header'));
}

defined('BASEPATH') OR exit('No direct script access allowed');
?>

<body class="hold-transition skin-blue sidebar-mini">
    <div id="loader" class="center"></div> 
    <div class="wrapper">

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                   Edit Non GST Quotation 
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/'?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="#">Non GSTQuotation</a></li>
                    <li class="active">Non GST Quotation Details</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-info">
                            <div class="box-header">
                                <h3 class="box-title">Edit Non GST Quotation</h3>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">

                                <!-- Start Flash Message -->
                               

                                <?php if ($this->session->flashdata('INFOMSG')) { ?>
                                    <div role="alert" class="alert alert-info">
                                        <button data-dismiss="alert" class="close" type="button"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                                        <strong>Oops!!</strong> <?= $this->session->flashdata('INFOMSG') ?>
                                    </div>
                                <?php } ?>
                                <!-- End Flash Message -->

                                <form class="form-horizontal form_overlay" name="add_name" id="add_name" method="post" action="<?php echo base_url(); ?>EstimateController/edit_estimate_quotation" enctype="multipart/form-data">
                                    <div class="row">

                                        <div class="col-md-12">
                                            <div class="form-group row ">
                                                <input type="hidden" class="form-control input-sm"   name="number" id="number" required="" value="<?php echo $non_gst_estimates_data_group['number']; ?>">

                                                <label for="inputEmail3" name="number" id="number" class="col-sm-12 control-label"><h2> Quotation:<b> <?php echo $non_gst_estimates_data_group['number']; ?></b></h2></label>
                                            </div>
                                        </div>    

                                        <div class="col-md-3">
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Company<span style="color: red;">*</span></label>
                                                <div class="col-sm-8">
                                                    <select class="col-md-12 company_search_name" name="customer_id" id="customer_id" required id="">
                                                        <option value="">Select Company</option>
                                                        <?php
                                                        $company_name = $non_gst_estimates_data_group['company_name'];

                                                        foreach ($customer_result as $row) {
                                                            ?>
                                                            <option value="<?php echo $row->customer_id ?>"  
                                                            <?php
                                                            if ($company_name == $row->company_name) {
                                                                echo 'selected="selected"';
                                                            }
                                                            ?> ><?php echo $row->company_name . " - " . $row->c_code; ?></option>

                                                        <?php }
                                                        ?>
                                                    </select>  
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Enquiry</label>
                                                <div class="col-sm-8">
                                                    <select class="form-control input-sm "  name="enquiry" id="enquiry">
                                                        <?php
                                                            if ($non_gst_enquiry_status[0]->enquiry == 1) { ?>
                                                        <option value="1" selected="">Mail</option>
                                                        <option value="2">Verbal</option>
                                                            <?php } ?>
                                                        <?php
                                                            if ($non_gst_enquiry_status[0]->enquiry == 2) { ?>
                                                        <option value="1" >Mail</option>
                                                        <option value="2" selected="">Verbal</option>
                                                            <?php } ?>
                                                        
<!--                                                        <option value="1">Mail</option>
                                                        <option value="2">Verbal</option>-->
                                                    </select>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="col-md-3">

                                            <div class="form-group row ">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Date<span style="color: red;">*</span></label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control date input-sm created-date" name="date" id="date" required="" value="<?php echo $non_gst_estimates_data_group['date']; ?>" onkeydown="return false;">
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Expires on<span style="color: red;">*</span></label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control date input-sm payment-due-date-check" name="expires_date" id="expires_date" required="" value="<?php echo $non_gst_estimates_data_group['exp_date']; ?>" onkeydown="return false;">
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Status</label>
                                                <div class="col-sm-8">
                                                    <select class="form-control input-sm "  name="status" id="status">
                                                         <?php
                                                            if ($status_result[0]->status == 1) { ?>
                                                        <option value="1" selected="">Draft</option>
                                                        <option value="2">Sent</option>
                                                        <option value="3">Viewed</option>
                                                        <option value="4">Approved</option> 
                                                        <option value="5">Rejected</option>
                                                        <option value="6">Canceled</option> 
                                                            <?php } ?>
                                                        <?php
                                                            if ($status_result[0]->status == 2) { ?>
                                                        <option value="1" >Draft</option>
                                                        <option value="2" selected="">Sent</option>
                                                        <option value="3">Viewed</option>
                                                        <option value="4">Approved</option> 
                                                        <option value="5">Rejected</option>
                                                        <option value="6">Canceled</option> 
                                                            <?php } ?>
                                                        <?php
                                                            if ($status_result[0]->status == 3) { ?>
                                                        <option value="1" >Draft</option>
                                                        <option value="2">Sent</option>
                                                        <option value="3" selected="">Viewed</option>
                                                        <option value="4">Approved</option> 
                                                        <option value="5">Rejected</option>
                                                        <option value="6">Canceled</option> 
                                                            <?php } ?>
                                                        <?php
                                                            if ($status_result[0]->status == 4) { ?>
                                                        <option value="1">Draft</option>
                                                        <option value="2">Sent</option>
                                                        <option value="3">Viewed</option>
                                                        <option value="4" selected="">Approved</option> 
                                                        <option value="5">Rejected</option>
                                                        <option value="6">Canceled</option> 
                                                            <?php } ?>
                                                        <?php
                                                            if ($status_result[0]->status == 5) { ?>
                                                        <option value="1" selected="">Draft</option>
                                                        <option value="2">Sent</option>
                                                        <option value="3">Viewed</option>
                                                        <option value="4">Approved</option> 
                                                        <option value="5" selected="">Rejected</option>
                                                        <option value="6">Canceled</option> 
                                                            <?php } ?>
                                                        <?php
                                                            if ($status_result[0]->status == 6) { ?>
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

                                        </div>

                                        <div class="col-md-6">

                                            <div class="form-group row ">
                                                <label for="inputEmail3" id="subheading1" class="col-sm-4 control-label">Subheading</label>
                                                <div class="col-sm-8">
                                                    <input type="text" value="<?php echo $non_gst_estimates_data_group['quotation_subheading']; ?>" class="form-control" name="quotation_subheading" id="quotation_subheading">
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Footer</label>
                                                <div class="col-sm-8">
                                                    <textarea class="form-control" name="quotation_footer" id="quotation_footer" rows="3"><?php echo $non_gst_estimates_data_group['quotation_footer']; ?></textarea>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Memo</label>
                                                <div class="col-sm-8">
                                                    <textarea class="form-control" name="quotation_memo" id="quotation_memo" rows="3"><?php echo $non_gst_estimates_data_group['quotation_memo']; ?></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="table-responsive">  

                                        <table class="table table-bordered" id="dynamic_field">  
                                            <tr>
                                                <th>Item</th>
                                                <th>Description</th>
                                                <th>Part Image</th>
                                                <th>MOQ</th>
                                                <th>HSN Code</th>
                                                <th>Price/Unit</th>
                                                <th>Discount(%)</th>
                                                <th>Amount</th>
                                                <th>Action</th>
                                            </tr>

                                            <?php
                                            $i = 1;
                                            foreach ($show_non_gst_quotation as $key) {
                                                ?>
                                                <tr> 
                                                    <td>
                                                        <select style="width: 150px" class="form-control input-sm product_name_auto item_search_name name_list"  name="term[]" id="item_name<?php echo $i; ?>" onchange="myFunction(this.id)" required="" data-live-search="true">
                                                            <option></option>
                                                            <?php
                                                            // $company_name = $non_gst_estimates_data_group['product_name'];

                                                            foreach ($product_code_list as $row) {
                                                                ?>
                                                                <option value="<?php echo $row->code ?>"  
                                                                <?php
                                                                if ($key->product_name == $row->code) {
                                                                    echo 'selected="selected"';
                                                                }
                                                                ?> ><?php echo $row->code . " - " . $row->item_name; ?></option>

                                                            <?php }
                                                            ?>


                                                        </select>

    <!--<input type="text" name="term[]" value="<?php //echo $key->product_name;  ?>" id="item_name<?php //echo $i;  ?>" required="" class="form-control input-sm name_list product_name_auto" />-->

                                                        <input type="hidden" class="form-control input-sm" name="quotation_id[]" id="quotation_id<?php echo $i; ?>"  value="<?php echo $key->quotation_id; ?>"></td> 
                                                    <td><input type="text" name="description[]" value="<?php echo $key->description; ?>" id="description<?php echo $i; ?>"  class="form-control input-sm name_list description_auto" /></td> 
                                                    <td>
                                                        <input type="text" name="upload_part_img[]" id="upload_part_img<?php echo $i; ?>" class="hide">
                                                        <img id="part_image<?php echo $i; ?>" src="<?php echo base_url() . $key->part_image; ?>" name="part_image[]" width="80%" height="60%"/>
                                                    </td> 
                                                    <td class="hide"> <span id="total_quantity<?php echo $i; ?>" name="total_quantity[]"></span></td>
                                                    <td><input type="number" min="1" name="quantity[]" value="<?php echo $key->quantity; ?>" id="quantity<?php echo $i; ?>" required="" class="form-control input-sm name_list quantity_auto number-only-validation" value="1" /></td> 
                                                    <td><input type="text" readonly="" name="hsn[]" value="<?php echo $key->hsn_code; ?>" id="hsn<?php echo $i; ?>" required="" class="form-control input-sm name_list" /></td> 
                                                    <td class="hide"><input type="text" readonly="" name="gst_per[]" value="<?php echo $key->gst; ?>" id="gst_per<?php echo $i; ?>" class="form-control input-sm name_list" /></td> 
                                                    <td><input type="text" readonly="" name="price[]" value="<?php echo $key->price; ?>" id="price<?php echo $i; ?>" required="" class="form-control input-sm name_list"/></td>
                                                    <td><input type="number" min="0" maxlength="2" name="discount[]" value="<?php echo $key->discount; ?>" id="discount<?php echo $i; ?>" class="form-control input-sm name_list discount_auto number-only-validation" value="" /></td>
                                                    <td><input type="hidden" name="amount[]" id="amount<?php echo $i; ?>" class="form-control input-sm name_list amount_auto" value="<?php echo $key->amount; ?>"/>
                                                        <input type="hidden" name="amount_temp[]" id="amount_temp<?php echo $i; ?>" class="form-control input-sm name_list amount_auto" value="<?php echo $key->price; ?>"/>
                                                        <input type="hidden" name="gst_amount[]" id="gst_amount<?php echo $i; ?>" class="form-control input-sm name_list gst_amount_auto" value="0.00" />
                                                        <span id="span_amount<?php echo $i; ?>" name="span_amount[]">₹<?php echo $key->amount; ?></span>
                                                    </td>
                                                    <td>
                                                        <?php if ($i == 1) { ?>

                                                            <input type="hidden" name="non_gst" value="non_gst" id="non_gst">
                                                            <button type="button" name="edit_non_gst" id="edit_non_gst" class="btn btn-success"><i class="fa fa-plus-circle" aria-hidden="true"></i></button> 

                                                        <?php } else { ?>
                                                            <button type="button" name="remove" id="remove<?php echo $i; ?>" class="btn btn-danger btn_remove">X</button>  
                                                        <?php } ?>
                                                    </td> 
                                                </tr>  
                                                <?php
                                                $i++;
                                            }
                                            ?>

                                        </table>  
                                         <div align="center" style="margin-bottom: 20px;">
                                             <div class="form-group" style="margin-bottom: 15px; display: inline-flex; align-items: center; gap: 10px; background: #f9f9f9; border: 1px solid #ddd; padding: 6px 15px; border-radius: 4px;">
                                                 <label style="font-weight: bold; margin-bottom: 0; color: #555;">Create Revision?</label>
                                                 <label class="radio-inline" style="margin-top: 0; font-weight: normal; color: #333;">
                                                     <input type="radio" name="revision" value="Y" style="margin-top: 1px;"> Yes
                                                 </label>
                                                 <label class="radio-inline" style="margin-left: 10px; margin-top: 0; font-weight: normal; color: #333;">
                                                     <input type="radio" name="revision" value="N" checked style="margin-top: 1px;"> No
                                                 </label>
                                             </div>
                                             <br>
                                             <button type="submit" name="submit" id="submit" class="btn btn-success" style="padding: 6px 20px; font-weight: 600;">Save</button>
                                         </div>
                                        <div align="right" style="margin: 10px">

 <!--Discount: <input type="text" name="discount"  id="discount" size="5" value="0" />%<br>-->
                                            <input type="hidden" name="temp_total" id="temp_total" class="form-control input-sm temp_total" value="0.00" /><br>

                                            <span id="total_amount" name="total_amount[]" class="total_span_auto_amount">Grand Total: ₹0.00</span><br>
                                            <input type="hidden" name="basic_total" id="basic_total" class="form-control input-sm basic_total" value="0.00" />
                                           <!--<span class="hide" id="total_gst_amount" name="total_gst_amount[]">Total GST Amount: ₹0.00</span><br>-->
                                            <span class="hide" id="grand_total_amount">Grand Total: ₹0.00</span>
                                            <input type="hidden" name="total_quotation_amount" id="total_quotation_amount" class="form-control input-sm name_list" value="0.00" />
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
        <div class="control-sidebar-bg"></div>
<?php $this->load->view('admin/footer'); ?>
    </div>
    <!-- ./wrapper -->
