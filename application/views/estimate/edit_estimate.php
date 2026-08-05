<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (isset($session_data_head1)) {
} else {
    header($this->config->item('header'));
}


defined('BASEPATH') or exit('No direct script access allowed');
require_once(APPPATH . "views/admin/modal.php");

?>

<body class="hold-transition skin-blue sidebar-mini">
    <div id="loader" class="center"></div>
    <div class="wrapper">

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1 class="gst">
                    Edit SGST/CGST Quotation
                </h1>

                <h1 class="igst_edit_hide_show">
                    Edit IGST Quotation
                </h1>

                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="<?php echo base_url() . 'EstimateController/show_quotation/' ?>">Quotation</a></li>
                    <li class="active">Edit Quotation</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-info">
                            <div class="box-header">
                                <h3 class="box-title">Edit Quotation</h3>
                                <a href="javascript:void(0);" onclick="window.history.back()" class="btn btn-primary pull-right"><i class="fa fa-close"></i> Close</a>

                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">

                                <!-- Start Flash Message -->
                             

                                <form class="form-horizontal form_overlay" name="add_name" id="add_name" method="post" action="<?php echo base_url(); ?>EstimateController/edit_estimate_quotation" enctype="multipart/form-data">
                                    <div class="row">

                                        <div class="col-md-12">
                                            <div class="form-group row ">
                                                <input type="hidden" class="form-control input-sm" name="number" id="number" required="" value="<?php echo $estimates_data_group['number']; ?>">

                                                <label for="inputEmail3" name="number" id="number" class="col-sm-12 control-label">
                                                    <h2> Quotation:<b> <?php echo $estimates_data_group['number']; ?></b></h2>
                                                </label>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <?php if (in_array('Projects', $this->session->userdata('session_data_head')['permission'] ?? [])): ?>
                                             <div class="form-group row">
                                                 <label for="project_code" class="col-sm-4 control-label">Project Code</label>
                                                 <div class="col-sm-8">
                                                     <select class="form-control input-sm select2" name="project_code" id="project_code">
                                                         <option value="">Select Project Code</option>
                                                         <?php if(isset($project_code_result) && !empty($project_code_result)): ?>
                                                             <?php foreach($project_code_result as $pc): ?>
                                                                 <option value="<?php echo htmlspecialchars($pc->project_code); ?>" <?php if(isset($estimates_data_group['project_code']) && $estimates_data_group['project_code'] == $pc->project_code) echo 'selected="selected"'; ?>><?php echo htmlspecialchars($pc->project_code); ?></option>
                                                             <?php endforeach; ?>
                                                         <?php endif; ?>
                                                     </select>
                                                 </div>
                                             </div>
                                             <?php else: ?>
                                                 <input type="hidden" name="project_code" id="project_code" value="<?php echo htmlspecialchars($estimates_data_group['project_code'] ?? ''); ?>">
                                             <?php endif; ?>

                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Company<span style="color: red;">*</span></label>
                                                <div class="col-sm-8" id="append_to_dropdown">
                                                    <select class="col-md-12 company_search_name" name="customer_id" id="customer_id" required id="">
                                                        <option value="">Select Company</option>
                                                        <?php
                                                        $company_name = $estimates_data_group['company_name'];
                                                        foreach ($customer_result as $row) {
                                                        ?>
                                                            <option value="<?php echo $row->customer_id ?>"
                                                                <?php
                                                                if ($company_name == $row->company_name) {
                                                                    echo 'selected="selected"';
                                                                }
                                                                ?>><?php echo $row->company_name . " - " . $row->c_code; ?></option>
                                                        <?php }
                                                        ?>
                                                    </select>
                                                    <span class="btn btn-success btn-sm" data-toggle="modal" data-target="#myModal" style="margin-top: 10%"><i class="glyphicon glyphicon-plus"></i>Add Company</span>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Enquiry</label>
                                                <div class="col-sm-8">
                                                    <select class="form-control input-sm " name="enquiry" id="enquiry">
                                                        <?php if ($enquiry_status[0]->enquiry == 1) { ?>
                                                            <option value="1" selected="">Mail</option>
                                                            <option value="2">Verbal</option>
                                                            <option value="3">Just Dial</option>
                                                            <option value="4">India Mart</option>
                                                        <?php } ?>
                                                        <?php if ($enquiry_status[0]->enquiry == 2) { ?>
                                                            <option value="1">Mail</option>
                                                            <option value="2" selected="">Verbal</option>
                                                            <option value="3">Just Dial</option>
                                                            <option value="4">India Mart</option>
                                                        <?php } ?>
                                                        <?php if ($enquiry_status[0]->enquiry == 3) { ?>
                                                            <option value="1">Mail</option>
                                                            <option value="2">Verbal</option>
                                                            <option value="3" selected="">Just Dial</option>
                                                            <option value="4">India Mart</option>
                                                        <?php } ?>
                                                        <?php if ($enquiry_status[0]->enquiry == 4) { ?>
                                                            <option value="1">Mail</option>
                                                            <option value="2">Verbal</option>
                                                            <option value="3">Just Dial</option>
                                                            <option value="4" selected="">India Mart</option>
                                                        <?php } ?>


                                                    </select>
                                                </div>
                                            </div>


                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Revisit</label>
                                                <div class="col-sm-8">
                                                    <select class="form-control input-sm" name="revision" id="revision">

                                                        <option value="N" selected="">No</option>
                                                        <option value="Y">Yes</option>


                                                    </select>
                                                </div>
                                            </div>


                                        </div>
                                        <div class="col-md-3">

                                            <div class="form-group row ">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Date<span style="color: red;">*</span></label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control alldate input-sm created-date" name="date" id="date" required="" value="<?php echo date('d-m-Y', strtotime($estimates_data_group['date'])); ?>" onkeydown="return false;">
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Expires on<span style="color: red;">*</span></label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control date input-sm payment-due-date-check currentDateWithSevendays" name="expires_date" id="expires_date" required="" value="<?php echo $estimates_data_group['exp_date']; ?>" onkeydown="return false;">
                                                </div>
                                            </div>

                                            <div class="form-group row">
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

                                         </div>

                                         <div class="col-md-6">
                                             <div class="form-group row">
                                                 <label for="system" class="col-sm-4 control-label">System<span style="color: red;">*</span></label>
                                                 <div class="col-sm-8">
                                                     <input type="text" class="form-control input-sm" name="system" id="system" required value="<?php echo htmlspecialchars($estimates_data_group['system'] ?? ''); ?>">
                                                 </div>
                                             </div>

                                             <div class="form-group row">
                                                 <label for="location" class="col-sm-4 control-label">Location</label>
                                                 <div class="col-sm-8">
                                                     <input type="text" class="form-control input-sm" name="location" id="location" value="<?php echo htmlspecialchars($estimates_data_group['location'] ?? ''); ?>">
                                                 </div>
                                             </div>

                                             <div class="form-group row">
                                                 <label for="capacity" class="col-sm-4 control-label">Capacity</label>
                                                 <div class="col-sm-8">
                                                     <input type="text" class="form-control input-sm" name="capacity" id="capacity" value="<?php echo htmlspecialchars($estimates_data_group['capacity'] ?? ''); ?>">
                                                 </div>
                                             </div>

                                             <div class="form-group row ">
                                                 <label for="inputEmail3" id="subheading1" class="col-sm-4 control-label">Subheading</label>
                                                 <div class="col-sm-8">
                                                     <input type="text" value="<?php echo $estimates_data_group['quotation_subheading']; ?>" class="form-control" name="quotation_subheading" id="quotation_subheading">
                                                 </div>
                                             </div>

                                             <div class="form-group row">
                                                 <label for="inputEmail3" class="col-sm-4 control-label">Footer</label>
                                                 <div class="col-sm-8">
                                                     <textarea class="form-control" name="quotation_footer" id="quotation_footer" rows="3"><?php echo $estimates_data_group['quotation_footer']; ?></textarea>
                                                 </div>
                                             </div>


                                         </div>
                                    </div>
                                    <div class="table-responsive">

                                        <table class="table table-bordered" id="dynamic_field">
                                            <tr>
                                                <th>Item</th>
                                                <th>Description</th>
                                                <th>HSN Code</th>
                                                <th>QTY</th>
                                                <th>UNIT</th>
                                                
                                                <th class="gst_per">TAX(%)</th>
                                                <th class="gst">SGST</th>
                                                <th class="gst">CGST</th>
                                                <th class="igst_edit_hide_show">IGST</th>
                                                <th>Price/Unit</th>
                                                <th>Discount(%)</th>
                                                <th>Amount</th>
                                                <th>Action
                                                    <!-- <button type="button" name="edit_non_gst" id="edit_non_gst" class="btn btn-success"><i class="fa fa-plus-circle" aria-hidden="true"></i></button>  -->

                                                </th>
                                            </tr>

                                            <?php
                                            $i = 1;
                                            foreach ($show_quotation as $key) {
                                            ?>
                                                <tr>
                                                    <td>
                                                        <select style="width: 150px" class="form-control input-sm product_name_auto item_search_name name_list" name="product_name[]" id="item_name<?php echo $i; ?>" onchange="myFunction1(this.id)" required="" data-live-search="true">
                                                            <option></option>

                                                            <?php
                                                            foreach ($item_name as $row) {
                                                            ?>
                                                                <option value="<?php echo $row->code ?>"
                                                                    <?php
                                                                    if ($key->product_name == $row->code) {
                                                                        echo 'selected="selected"';
                                                                    }
                                                                    ?>><?php echo $row->code . " - " . $row->item_name; ?></option>
                                                            <?php }
                                                            ?>

                                                        </select>

                                                        <input type="hidden" class="form-control input-sm" name="quotation_id[]" id="quotation_id<?php echo $i; ?>" value="<?php echo $key->quotation_id; ?>">
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-info" onClick="descButton(this.id)" id="btnDescriptionId<?php echo $i; ?>">description</button>

                                                        <textarea style="width: 150px" class="form-control input-sm name_list description_auto hide" name="description[]" id="description<?php echo $i; ?>" rows="7"><?php echo $key->description; ?></textarea>
                                                    </td>
                                                    <td class="hide"> <span id="total_quantity<?php echo $i; ?>" name="total_quantity[]"></span></td>
                                                    <td><input type="text" name="hsn[]" value="<?php echo $key->hsn_code; ?>" id="hsn<?php echo $i; ?>" required="" class="form-control input-sm name_list" readonly="" /></td>

                                                    <td><input type="text" name="quantity[]" value="<?php echo $key->quantity; ?>" id="quantity<?php echo $i; ?>" required="" class="form-control input-sm name_list quantity_auto number-only-validation" value="1" /></td>
                                                    <td>
                                                        <select style="width: 100px" class="form-control input-sm  item_search_unit" name="unit[]" id="unit<?php echo $i; ?>" required="" data-live-search="true">
                                                            <option value="<?php echo $key->unit ?>" selected="selected"><?php echo $key->unit; ?></option>
                                                        </select>
                                                    </td>
                                                    <td class="gst_per"><input type="text" readonly="" name="gst_per[]" value="<?php echo $key->gst; ?>" id="gst_per<?php echo $i; ?>" class="form-control input-sm name_list" /></td>
                                                    <td class="gst"><input type="text" readonly="" name="sgst[]" value="<?php echo $key->sgst; ?>" id="sgst<?php echo $i; ?>" class="form-control input-sm sgst_list" /></td>
                                                    <td class="gst"><input type="text" readonly="" name="cgst[]" value="<?php echo $key->cgst; ?>" id="cgst<?php echo $i; ?>" class="form-control input-sm cgst_list" /></td>
                                                    <td class="igst_edit_hide_show"><input type="text" readonly="" name="igst[]" value="<?php echo $key->igst; ?>" id="igst<?php echo $i; ?>" class="form-control input-sm igst_list" /></td>
                                                    <td><input type="text" name="price[]" value="<?php echo $key->price; ?>" id="price<?php echo $i; ?>" required="" class="form-control input-sm price_auto" /></td>
                                                    <td><input type="text" maxlength="5" name="discount[]" value="<?php echo $key->discount; ?>" id="discount<?php echo $i; ?>" class="form-control input-sm name_list discount_auto number-only-validation" value="" /></td>
                                                    <td><input type="hidden" name="amount[]" id="amount<?php echo $i; ?>" class="form-control input-sm name_list amount_auto" value="<?php echo $key->amount; ?>" />
                                                        <input type="hidden" name="amount_temp[]" id="amount_temp<?php echo $i; ?>" class="form-control input-sm name_list amount_auto" value="<?php echo $key->price * $key->quantity; ?>" />
                                                        <input type="hidden" name="gst_amount[]" id="gst_amount<?php echo $i; ?>" class="form-control input-sm name_list gst_amount_auto" value="0.00" />
                                                        <span id="span_amount<?php echo $i; ?>" name="span_amount[]">₹<?php echo $key->amount; ?></span>
                                                    </td>
                                                    <td>
                                                        <?php if ($i == 1) { ?>


                                                            <?php if ($key->gst_type != 'S') { ?>
                                                                <input type="hidden" name="quotation_igst_check" value="igst" id="quotation_igst_check">

                                                                <input type="hidden" name="igst_edit_hide_show" value="igst_edit_hide_show" id="igst_edit_hide_show">
                                                                <button type="button" name="edit_gst" id="edit_gst" class="btn btn-xs btn-success"><i class="fa fa-plus-circle" aria-hidden="true"></i></button>

                                                            <?php } else if ($key->gst_type != 'I') { ?>
                                                                <input type="hidden" name="gst" value="gst" id="gst">
                                                                <input type="hidden" name="edit_sgst_cgst_check" value="edit_sgst_cgst_check" id="edit_sgst_cgst_check">
                                                                <input type="hidden" name="gst_discount_check" value="gst" id="gst_discount_check">
                                                                <button type="button" name="edit_gst" id="edit_gst" class="btn btn-xs btn-success"><i class="fa fa-plus-circle" aria-hidden="true"></i></button>

                                                            <?php } else if (($key->gst_type != 'S') || ($key->gst_type != 'I')) { ?>
                                                                <input type="hidden" name="non_gst" value="non_gst" id="non_gst">
                                                            <?php } ?>


                                                        <?php } else { ?>
                                                            <button type="button" name="remove" title="edit_estimate" id="remove<?php echo $i; ?>" class="btn btn-danger btn-xs btn_remove">X</button>
                                                        <?php } ?>
                                                    </td>
                                                    <!-- <td>
                                                    <button type="button" name="remove" title="edit_estimate" id="remove<?php echo $i; ?>" class="btn btn-danger btn_remove">X</button>  

                                                    </td> -->
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

                                            <span id="total_item_qty_display" style="font-weight: bold; color: #333;">Total Item Qty: 0</span><br>
                                            <span id="total_amount" name="total_amount[]" class="total_span_auto_amount">Grand Total: ₹0.00</span><br>
                                            <input type="hidden" name="basic_total" id="basic_total" class="form-control input-sm basic_total" value="0.00" />
                                            <span class="gst" id="sgst_amount" name="sgst_amount[]">SGST Amount: ₹0.00</span><br>
                                            <span class="gst" id="cgst_amount" name="cgst_amount[]">CGST Amount: ₹0.00</span><br>
                                            <span class="igst igst_edit_hide_show" id="igst_amount" name="igst_amount">IGST Amount: ₹0.00</span><br>
                                            <span id="grand_total_amount">Grand Total: ₹0.00</span><br>
                                            <span id="grand_total_words" style="font-weight: bold; color: #555;">Grand Total in Words: Zero Rupees Only</span><br>
                                            <input type="hidden" name="total_quotation_amount" id="total_quotation_amount" readonly="" class="form-control input-sm name_list" />
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-xs-6">
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-2 control-label">Terms & Conditions</label>
                                                <div class="col-sm-10">
                                                    <textarea class="form-control" name="terms_and_conditions" id="terms_and_conditions" rows="3"><?php echo $estimates_data_group['terms_and_conditions']; ?></textarea>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-2 control-label">Payment Terms</label>
                                                <div class="col-sm-10">
                                                    <textarea class="form-control" name="payment_terms" id="payment_terms" rows="3"><?php echo $estimates_data_group['payment_terms']; ?></textarea>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-2 control-label">Process Schedule</label>
                                                <div class="col-sm-10">
                                                    <textarea class="form-control" name="process_schedule" id="process_schedule" rows="3"><?php echo $estimates_data_group['process_schedule']; ?></textarea>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-xs-6">

                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-2 control-label">Taxes</label>
                                                <div class="col-sm-10">
                                                    <textarea class="form-control" name="taxes" id="taxes" rows="3"><?php echo $estimates_data_group['taxes']; ?></textarea>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-2 control-label">Exclusions</label>
                                                <div class="col-sm-10">
                                                    <textarea class="form-control" name="exclusions" id="exclusions" rows="3"><?php echo $estimates_data_group['exclusions']; ?></textarea>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-2 control-label">Note</label>
                                                <div class="col-sm-10">
                                                    <textarea class="form-control" name="quotation_memo" id="quotation_memo" rows="3"><?php echo $estimates_data_group['quotation_memo']; ?></textarea>
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
        <div class="control-sidebar-bg"></div>

        <!-- ./Customer modal -->
        <div id="myModal" class="modal fade" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header btn-danger">
                        <center><h4 class="modal-title">Add Company
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </h4></center>
                    </div>
                    <div class="modal-body">
                        <div class="card-body ">
                            <div class="form-group row required">
                                <label for="inputEmail3" class="col-sm-4 control-label">Company  Name</label>
                                <div class="col-sm-7">
                                    <input type="text" class="form-control" name="company_name" id="company_name" required="">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label">Customer Name</label>
                                <div class="col-sm-7">
                                    <input type="text" class="form-control " name="fullname" id="fullname">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label"> GST No</label>
                                <div class="col-sm-7">
                                    <input type="text" maxlength="15" class="form-control " name="gst" id="gst" style="text-transform: uppercase;" placeholder="e.g., 27AAPFU0205R1Z0">
                                </div>
                                <small class="text-muted col-sm-7 col-sm-offset-4">15-digit GST number</small>
                            </div>

                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label"> PAN No</label>
                                <div class="col-sm-7">
                                    <input type="text" class="form-control" name="pancard" id="pancard" style="text-transform: uppercase;" maxlength="10" placeholder="e.g., AAPFU0205R">
                                </div>
                                <small class="text-muted col-sm-7 col-sm-offset-4">10-digit PAN (auto-filled from GST if available)</small>
                            </div>

                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label"> Email</label>
                                <div class="col-sm-7">
                                    <input type="text" class="form-control " name="email" id="email" pattern="^([0-9a-zA-Z]([-.\.\w]*[0-9a-zA-Z])*@([0-9a-zA-Z][-\w]*[0-9a-zA-Z]\.)+[a-zA-Z]{2,9})$" />
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label"> Mobile</label>
                                <div class="col-sm-7">                                            
                                    <input type="text" class="form-control " name="mobile" id="mobile" maxlength="10" onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g, '')"  />
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label"> State Code</label>
                                <div class="col-sm-7">
                                    <input type="text" class="form-control " name="state_code" id="state_code" >
                                </div>
                            </div>
                            
                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label"> Address</label>
                                <div class="col-sm-7">
                                    <textarea class="form-control input-sm" name="address" id="address"></textarea>
                                </div>
                            </div>
                           
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" id="gst_check_customer" value="gst_check_customer">
                        <button type="button" id="btnSave"  class="btn btn-success performa_submit">Submit</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="glyphicon glyphicon-remove"></i> Close</button>
                    </div>
                </div>
            </div>
        </div>

        <?php $this->load->view('admin/footer'); ?>
    </div>
    <!-- ./wrapper -->
    <script>
        $(document).ready(function() {

            // ensure row counter matches existing items on edit
            if (typeof i !== 'undefined') {
                i = <?php echo count($show_quotation); ?>;
            }

            var igst = $("#igst_edit_hide_show").val();
            var gst = $("#gst").val();
            var non_gst = $("#non_gst").val();
            //alert(igst);
            if (igst == "igst_edit_hide_show") {
                $(".gst").hide();
                $(".igst_edit_hide_show").show();
                $(".gst_per").show();

            }
            if (gst == "gst") {
                $(".gst").show();
                $(".igst_edit_hide_show").hide();
                $(".gst_per").show();
            }
            if (non_gst == "non_gst") {
                $(".gst").hide();
                $(".igst_edit_hide_show").hide();
                $(".gst_per").hide();
            }

        });
    </script>
    <script>
        // Delegate select2:select event for item selects to work with dynamically added rows
        $(document).on('select2:select', '.item_search_name', function() {
            myFunction1(this.id);
        });
    </script>
    <script>
        // Load all units for existing unit dropdowns
        $(document).ready(function() {
            var base_url = window.location.origin + "/" + window.location.pathname.split("/")[1] + "/sameep-accounting/";
            
            // Store current unit values before replacement
            var currentunitValues = {};
            $(".item_search_unit").each(function() {
                currentunitValues[$(this).attr('id')] = $(this).find('option:selected').val() || $(this).val();
            });
            
            $.ajax({
                type: "GET",
                url: base_url + "UnitController/get_unit_name",
                cache: false,
                success: function (data) {
                    console.log('Unit data received:', data);
                    var unit_result = jQuery.parseJSON(data);
                    var unit_options = "<option></option>";
                    
                    // Build options
                    for (var n = 0; n < unit_result.length; n++) {
                        unit_options +=
                            '<option value="' +
                            unit_result[n].unit +
                            '">' +
                            unit_result[n].unit +
                            "</option>";
                    }

                    // Set options for all existing unit selects and restore selected values
                    $(".item_search_unit").each(function() {
                        var dropdownId = $(this).attr('id');
                        var savedValue = currentunitValues[dropdownId];
                        
                        // Set all options
                        $(this).html(unit_options);
                        
                        // Restore the selected value
                        if (savedValue) {
                            $(this).val(savedValue);
                        }
                        
                        console.log('Set unit options for:', dropdownId, 'with value:', savedValue);
                    });

                    // Initialize/reinitialize Select2 on unit selects
                    $(".item_search_unit").select2({
                        placeholder: "Select Unit",
                    }).trigger('change');
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error loading units:', status, error);
                }
            });
        });
    </script>
    <script>
        function numberToWords(num) {
            const ones = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine'];
            const tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];
            const teens = ['Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];

            if (num === 0) return 'Zero Rupees Only';

            function convertLessThanThousand(n) {
                let result = '';

                if (n >= 100) {
                    result += ones[Math.floor(n / 100)] + ' Hundred ';
                    n %= 100;
                }

                if (n >= 20) {
                    result += tens[Math.floor(n / 10)] + ' ';
                    n %= 10;
                } else if (n >= 10) {
                    result += teens[n - 10] + ' ';
                    n = 0;
                }

                if (n > 0) {
                    result += ones[n] + ' ';
                }

                return result;
            }

            if (num < 0) num = -num;

            let crore = Math.floor(num / 10000000);
            num %= 10000000;

            let lakh = Math.floor(num / 100000);
            num %= 100000;

            let thousand = Math.floor(num / 1000);
            num %= 1000;

            let result = '';

            if (crore > 0) {
                result += convertLessThanThousand(crore) + 'Crore ';
            }

            if (lakh > 0) {
                result += convertLessThanThousand(lakh) + 'Lakh ';
            }

            if (thousand > 0) {
                result += convertLessThanThousand(thousand) + 'Thousand ';
            }

            if (num > 0) {
                result += convertLessThanThousand(num);
            }

            return result.trim() + ' Rupees Only';
        }

        function updateGrandTotalWords() {
            let grandTotal = parseFloat($('#total_quotation_amount').val()) || 0;
            let grandTotalAmount = Math.floor(grandTotal);
            let grandTotalWords = numberToWords(grandTotalAmount);
            $('#grand_total_words').text('Grand Total in Words: ' + grandTotalWords);
        }

        function updateTotalQty() {
            var totalQty = 0;
            $('input[name="quantity[]"]').each(function() {
                totalQty += parseFloat($(this).val()) || 0;
            });
            $('#total_item_qty_display').text('Total Item Qty: ' + totalQty);
        }
        updateTotalQty();
        updateGrandTotalWords();
        $(document).on('input change', 'input[name="quantity[]"], input[name="price[]"], input[name="discount[]"], input[name="gst_per[]"], input[name="sgst[]"], input[name="cgst[]"], input[name="igst[]"]', function() {
            updateTotalQty();
            setTimeout(updateGrandTotalWords, 100);
        });
        setInterval(function() {
            updateTotalQty();
            updateGrandTotalWords();
        }, 500);

        CKEDITOR.replace('terms_and_conditions');
        CKEDITOR.replace('payment_terms');
        CKEDITOR.replace('process_schedule');
        CKEDITOR.replace('taxes');
        CKEDITOR.replace('exclusions');
        CKEDITOR.replace('quotation_memo');

        // Auto-fetch project details (System, Location, Capacity) when project_code is selected
        $('#project_code').on('change', function() {
            var projectCode = $(this).val();
            if (!projectCode) {
                return;
            }
            $.ajax({
                url: '<?php echo base_url(); ?>SalesOrderController/ajax_get_project_details',
                type: 'POST',
                data: { project_code: projectCode },
                dataType: 'json',
                success: function(response) {
                    if (response && response.success) {
                        if (response.customer) {
                            $('#customer_id').val(response.customer.customer_id).trigger('change');
                        }
                        if (response.project) {
                            $('#system').val(response.project.system || '');
                            $('#location').val(response.project.location || '');
                            $('#capacity').val(response.project.capacity || '');
                        }
                    }
                }
            });
        });

        // GST to PAN and State Code conversion
        $(document).off('blur', '#gst').on('blur', '#gst', function() {
            var gstNo = $(this).val().trim().toUpperCase();
            
            if (gstNo.length === 0) {
                return; // Allow empty field
            }
            
            // GST validation: must be 15 characters
            if (gstNo.length !== 15) {
                alert('GST No must be 15 characters long. Example: 27AAPFU0205R1Z0');
                $(this).val('');
                $('#pancard').val('');
                $('#state_code').val('');
                $(this).focus();
                return;
            }
            
            // Validate GST format: 2 digits + 10 char PAN + 1 digit + 1 char + 1 digit
              //var gstRegex = /^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[0-9]{1}[A-Z]{1}[0-9]{1}$/;
              var gstRegex = /^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[0-9]{1}[A-Z]{1}[A-Z0-9]{1}$/;
            if (!gstRegex.test(gstNo)) {
                alert('Invalid GST format. Expected: 2 digits + PAN + 1 digit + 1 letter + 1 digit\nExample: 27AAPFU0205R1Z0');
                $(this).val('');
                $('#pancard').val('');
                $('#state_code').val('');
                $(this).focus();
                return;
            }
            
            // Extract State Code (first 2 digits) from GST No and auto-fill
            var stateCode = gstNo.substring(0, 2);
            $('#state_code').val(stateCode);
            
            // Extract PAN (characters 2-11) from GST No and auto-fill
            var panNo = gstNo.substring(2, 12);
            $('#pancard').val(panNo);
        });

        // PAN validation
        $(document).off('blur', '#pancard').on('blur', '#pancard', function() {
            var panNo = $(this).val().trim().toUpperCase();
            
            if (panNo.length === 0) {
                return; // Allow empty field
            }
            
            // PAN validation: must be 10 characters
            if (panNo.length !== 10) {
                alert('PAN No must be 10 characters long');
                $(this).focus();
                return;
            }
            
            // Validate PAN format: 5 letters, 4 numbers, 1 letter
            var panRegex = /^[A-Z]{5}[0-9]{4}[A-Z]{1}$/;
            if (!panRegex.test(panNo)) {
                alert('Invalid PAN format.\nExpected: 5 letters + 4 numbers + 1 letter\nExample: AAPFU0205R');
                $(this).focus();
                return;
            }
        });
    </script>
