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
                <h1 class="gst">
                    Edit SGST/CGST Sales Order
                </h1>

                <h1 class="igst_edit_hide_show">
                    Edit IGST Sales Order
                </h1>

                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="<?php echo base_url() . 'SalesOrderController/index/' ?>">Sales Order</a></li>
                    <li class="active">Edit Sales Order</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-info">
                            <div class="box-header">
                                <h3 class="box-title">Edit Sales Order</h3>
                                <a href="javascript:void(0);" onclick="window.history.back()" class="btn btn-primary pull-right"><i class="fa fa-close"></i> Close</a>

                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">

                               

                                <form class="form-horizontal form_overlay" name="add_name" id="add_name" method="post" action="<?php echo base_url(); ?>SalesOrderController/edit_salesorder_salesorder" enctype="multipart/form-data">
                                    <div class="row">

                                         <div class="col-md-12">
                                             <div class="form-group row ">
                                                 <label class="col-sm-12 control-label"><h2> Sales Order:<b> <?php echo $salesorders_data_group['number_fk']; ?></b></h2></label>
                                             </div>
                                         </div>

                                        <div class="col-md-3">

                                           <?php if (in_array('Projects', $this->session->userdata('session_data_head')['permission'] ?? [])): ?>
                                            <div class="form-group row ">
                                                <label for="project_code" class="col-sm-4 control-label">Project Code</label>
                                                <div class="col-sm-8">
                                                     <select class="form-control input-sm select2 company_search_name" name="project_code" id="project_code">
                                                        <option value="">Select project code</option>
                                                        <?php 
                                                         $project_code = $salesorders_data_group['project_code'] ?? '';
                                                        foreach ($project_code_result as $key) { ?>
                                                        <option value="<?php echo htmlspecialchars($key->project_code); ?>" <?php if ($key->project_code == $project_code) { echo 'selected="selected"'; } ?>><?php echo htmlspecialchars($key->project_code); ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <?php else: ?>
                                                <input type="hidden" name="project_code" id="project_code" value="<?php echo htmlspecialchars($salesorders_data_group['project_code'] ?? ''); ?>">
                                            <?php endif; ?>

                                            <div class="form-group row">
                                                <label for="customer_id" class="col-sm-4 control-label">Company<span style="color: red;">*</span></label>
                                                <div class="col-sm-8">
                                                    <select class="form-control input-sm select2 company_search_name" required name="customer_id" id="customer_id">
                                                        <option value="">Select Company</option>
                                                        <?php
                                                        $company_name = $salesorders_data_group['company_name'];
                                                        foreach ($customer_result as $row) { ?>
                                                            <option value="<?php echo $row->customer_id ?>" <?php if ($company_name == $row->company_name) { echo 'selected="selected"'; } ?>><?php echo $row->company_name . " - " . $row->c_code; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                    <span class="btn btn-success btn-sm btn-block" data-toggle="modal" data-target="#myModal" style="margin-top: 5px;"><i class="glyphicon glyphicon-plus"></i>Add Company</span>
                                                </div>
                                            </div>
                                            <input type="hidden" name="customer_name" id="customer_name" value="<?php echo htmlspecialchars($salesorders_data_group['company_name'] ?? ''); ?>">

                                            <div class="form-group row ">
                                                <label for="enquiry" class="col-sm-4 control-label">Enquiry</label>
                                                <div class="col-sm-8">
                                                    <select class="form-control input-sm" name="enquiry" id="enquiry">
                                                        <option value="1" <?php if ($salesorders_data_group['enquiry'] == 1) { echo 'selected="selected"'; } ?>>Mail</option>
                                                        <option value="2" <?php if ($salesorders_data_group['enquiry'] == 2) { echo 'selected="selected"'; } ?>>Verbal</option>
                                                        <option value="3" <?php if ($salesorders_data_group['enquiry'] == 3) { echo 'selected="selected"'; } ?>>Just Dial</option>
                                                        <option value="4" <?php if ($salesorders_data_group['enquiry'] == 4) { echo 'selected="selected"'; } ?>>India Mart</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="form-group row ">
                                                <label for="po_date" class="col-sm-4 control-label">PO Date</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control alldate input-sm created-date" name="po_date" id="po_date" onkeydown="return false;" value="<?php if (!empty($salesorders_data_group['po_date']) && $salesorders_data_group['po_date'] !== '0000-00-00') { echo date('d-m-Y', strtotime($salesorders_data_group['po_date'])); } ?>">
                                                </div>
                                            </div>

                                            <input type="hidden" name="customer_code" id="customer_code" value="<?php echo htmlspecialchars($salesorders_data_group['customer_code'] ?? ''); ?>">

                                        </div>

                                        <div class="col-md-3">

                                            <div class="form-group row required">
                                                <label for="date" class="col-sm-4 control-label">Date</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control date input-sm created-date" name="date" id="date" required="" value="<?php if (!empty($salesorders_data_group['date']) && $salesorders_data_group['date'] !== '0000-00-00') { echo date('d-m-Y', strtotime($salesorders_data_group['date'])); } ?>" onkeydown="return false;">
                                                </div>
                                            </div>

                                            <div class="form-group row required">
                                                <label for="expires_date" class="col-sm-4 control-label">Delivery Date</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control date input-sm payment-due-date-check" name="expires_date" id="expires_date" required="" value="<?php if (!empty($salesorders_data_group['exp_date']) && $salesorders_data_group['exp_date'] !== '0000-00-00') { echo date('d-m-Y', strtotime($salesorders_data_group['exp_date'])); } ?>" onkeydown="return false;">
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="status" class="col-sm-4 control-label">Status</label>
                                                <div class="col-sm-8">
                                                    <select class="form-control input-sm " name="status" id="status">
                                                        <option value="1" <?php if (($status_result[0]->status ?? '') == 1) { echo 'selected="selected"'; } ?>>Draft</option>
                                                        <option value="2" <?php if (($status_result[0]->status ?? '') == 2) { echo 'selected="selected"'; } ?>>Sent</option>
                                                        <option value="3" <?php if (($status_result[0]->status ?? '') == 3) { echo 'selected="selected"'; } ?>>Viewed</option>
                                                        <option value="4" <?php if (($status_result[0]->status ?? '') == 4) { echo 'selected="selected"'; } ?>>Approved</option> 
                                                        <option value="5" <?php if (($status_result[0]->status ?? '') == 5) { echo 'selected="selected"'; } ?>>Hold</option>
                                                        <option value="6" <?php if (($status_result[0]->status ?? '') == 6) { echo 'selected="selected"'; } ?>>Canceled</option> 
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="form-group row ">
                                                <label for="attachment" class="col-sm-4 control-label">PO Attachment</label>
                                                <div class="col-sm-8">
                                                    <input type="file" class="form-control input-sm" name="attachment" id="attachment" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.txt">
                                                    <?php if (!empty($salesorders_data_group['attachment'])) { ?>
                                                        <br><strong>Current File:</strong> 
                                                        <a href="<?php echo base_url() . 'uploads/' . $salesorders_data_group['attachment']; ?>" target="_blank" class="btn btn-xs btn-info"><i class="fa fa-download"></i> Download</a>
                                                    <?php } ?>
                                                </div>
                                                <small class="text-muted col-sm-8 col-sm-offset-4">Supported formats: PDF, DOC, DOCX, XLS, XLSX, JPG, JPEG, PNG, TXT</small>
                                            </div>

                                        </div>

                                        <div class="col-md-6">

                                             <!-- <div class="form-group row">
                                                 <label for="transportation" class="col-sm-4 control-label">Transportation </label>
                                                 <div class="col-sm-8">
                                                     <select class="form-control input-sm " name="transportation" id="transportation">
                                                         <option value="">Select transportation</option>
                                                         <option value="CS" <?php if (($salesorders_data_group['transportation'] ?? '') == 'CS') { echo 'selected="selected"'; } ?>>Customer Scope</option> 
                                                         <option value="OS" <?php if (($salesorders_data_group['transportation'] ?? '') == 'OS') { echo 'selected="selected"'; } ?>>Our Scope</option> 
                                                     </select>
                                                 </div>
                                             </div>

                                             <div class="form-group row">
                                                 <label for="installation" class="col-sm-4 control-label">Installation </label>
                                                 <div class="col-sm-8">
                                                     <select class="form-control input-sm " name="installation" id="installation">
                                                         <option value="">Select installation</option>
                                                         <option value="CS" <?php if (($salesorders_data_group['installation'] ?? '') == 'CS') { echo 'selected="selected"'; } ?>>Customer Scope</option> 
                                                         <option value="OS" <?php if (($salesorders_data_group['installation'] ?? '') == 'OS') { echo 'selected="selected"'; } ?>>Our Scope</option> 
                                                         <option value="OSV" <?php if (($salesorders_data_group['installation'] ?? '') == 'OSV') { echo 'selected="selected"'; } ?>>Only Supervision</option> 
                                                     </select>
                                                 </div>
                                             </div> -->

                                            <div class="form-group row">
                                                <label for="number" class="col-sm-4 control-label">SO Number<span style="color: red;">*</span></label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control input-sm" name="number" id="number" required="" value="<?php echo htmlspecialchars($salesorders_data_group['number_fk'] ?? ''); ?>">
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="system" class="col-sm-4 control-label">System<span style="color: red;">*</span></label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control input-sm" name="system" id="system" required value="<?php echo htmlspecialchars($salesorders_data_group['system'] ?? ''); ?>">
                                                </div>
                                            </div>

                                            <div class="form-group row ">
                                                <label for="po_number" class="col-sm-4 control-label">PO Number</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control input-sm" name="po_number" id="po_number" value="<?php echo htmlspecialchars($salesorders_data_group['po_number'] ?? ''); ?>">
                                                </div>
                                            </div>

                                            <div class="form-group row ">
                                                <label for="po_status" class="col-sm-4 control-label">PO Status</label>
                                                <div class="col-sm-8">
                                                    <select class="form-control input-sm" name="po_status" id="po_status">
                                                        <option value="open" <?php if (($salesorders_data_group['po_status'] ?? 'open') == 'open') { echo 'selected="selected"'; } ?>>Open</option>
                                                        <option value="close" <?php if (($salesorders_data_group['po_status'] ?? 'open') == 'close') { echo 'selected="selected"'; } ?>>Close</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="location" class="col-sm-4 control-label">Location</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control input-sm" name="location" id="location" value="<?php echo htmlspecialchars($salesorders_data_group['location'] ?? ''); ?>">
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="capacity" class="col-sm-4 control-label">Capacity</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control input-sm" name="capacity" id="capacity" value="<?php echo htmlspecialchars($salesorders_data_group['capacity'] ?? ''); ?>">
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="project_qty" class="col-sm-4 control-label">Project Quantity</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control input-sm" name="project_qty" id="project_qty" value="<?php echo htmlspecialchars($salesorders_data_group['project_qty'] ?? ''); ?>">
                                                </div>
                                            </div>

                                            <div class="form-group row hide">
                                                <label for="salesorder_subheading" class="col-sm-4 control-label">Subheading</label>
                                                <div class="col-sm-8">
                                                    <input type="text" value="<?php echo htmlspecialchars($salesorders_data_group['salesorder_subheading'] ?? ''); ?>" class="form-control" name="salesorder_subheading" id="salesorder_subheading">
                                                </div>
                                            </div>

                                            <div class="form-group row hide">
                                                <label for="salesorder_footer" class="col-sm-4 control-label">Footer</label>
                                                <div class="col-sm-8">
                                                    <textarea class="form-control" name="salesorder_footer" id="salesorder_footer" rows="3"><?php echo htmlspecialchars($salesorders_data_group['salesorder_footer'] ?? ''); ?></textarea>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="table-responsive">  
                                         <button type="button" id="add_so" class="hide"></button>
                                         <button type="button" id="add_so_heading_row" class="hide"></button>
                                        <table class="table table-bordered" id="dynamic_field">  
                                            <thead>
                                                <tr>
                                                    <th>Item</th>
                                                    <th>Description</th>
                                                    <th>HSN Code</th>
                                                    <th>QTY</th>
                                                    <th>Unit</th>
                                                    <th class="gst_per">TAX(%)</th>
                                                    <th class="gst">SGST</th>
                                                    <th class="gst">CGST</th>
                                                    <th class="igst_edit_hide_show">IGST</th>
                                                    <th>Price</th>
                                                    <th class="hide">Discount(%)</th>
                                                    <th>Amount</th>
                                                      <th width="12%">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                // Extract GST configuration inputs once at the top of the tbody
                                                $first_item = reset($show_salesorder);
                                                if ($first_item) {
                                                    if ($first_item->gst_type != 'S') {
                                                        echo '<input type="hidden" name="salesorder_igst_check" value="igst" id="salesorder_igst_check">';
                                                        echo '<input type="hidden" name="igst_edit_hide_show" value="igst_edit_hide_show" id="igst_edit_hide_show">';
                                                    } else if ($first_item->gst_type != 'I') {
                                                        echo '<input type="hidden" name="gst" value="gst" id="gst">';
                                                        echo '<input type="hidden" name="edit_sgst_cgst_check" value="edit_sgst_cgst_check" id="edit_sgst_cgst_check">';
                                                        echo '<input type="hidden" name="gst_discount_check" value="gst" id="gst_discount_check">';
                                                    }
                                                }
                                                ?>
                                                <?php
                                                $i = 1;
                                                foreach ($show_salesorder as $key) {
                                                    // ---- Render Section Heading Row ----
                                                    if (isset($key->product_name) && $key->product_name === '__HEADING__'):
                                                ?>
                                                <tr id="row<?php echo $i; ?>" class="so-heading-row">
                                                    <td colspan="11">
                                                        <input type="hidden" name="product_name[]" value="__HEADING__">
                                                        <input type="hidden" name="quantity[]" value="0">
                                                        <input type="hidden" name="unit[]" value="">
                                                        <?php
                                                        $isMain = true;
                                                        if (isset($key->tag_no) && ($key->tag_no === 'MAIN' || $key->tag_no === 'SUB')) {
                                                            $isMain = ($key->tag_no === 'MAIN');
                                                        }
                                                        ?>
                                                        <select name="tag_no[]" class="heading-type-select form-control input-sm" style="width: auto; display: inline-block; margin-right: 8px;">
                                                            <option value="MAIN" <?php echo ($isMain ? 'selected' : ''); ?>>Main Heading</option>
                                                            <option value="SUB" <?php echo (!$isMain ? 'selected' : ''); ?>>Sub Heading</option>
                                                        </select>
                                                        <input type="hidden" name="scope[]" value="">
                                                        <input type="hidden" name="stores_remark[]" value="">
                                                        <input type="hidden" name="remark[]" value="">
                                                        <input type="hidden" name="price[]" value="0">
                                                        <input type="hidden" name="product_code[]" value="__HEADING__">
                                                        <input type="hidden" name="hsn[]" value="">
                                                        <input type="hidden" name="gst_per[]" value="0">
                                                        <input type="hidden" name="sgst[]" value="0">
                                                        <input type="hidden" name="cgst[]" value="0">
                                                        <input type="hidden" name="igst[]" value="0">
                                                        <input type="hidden" name="amount[]" value="0">
                                                         <input type="hidden" name="salesorder_id[]" id="quotation_id<?php echo $i; ?>" value="<?php echo $key->salesorder_id; ?>">
                                                         <input type="hidden" name="discount[]" value="<?php echo $key->discount ?? 0; ?>">
                                                         <input type="hidden" name="amount_temp[]" value="0">
                                                         <input type="hidden" name="gst_amount[]" value="0">
                                                         <i class="fa fa-tag" style="margin-right:8px;opacity:0.7;"></i>
                                                         <input type="text" name="description[]" class="form-control input-sm heading-text-input" value="<?php echo htmlspecialchars($key->description ?? ''); ?>" placeholder="ENTER SECTION HEADING (E.G. INSTRUMENTS, CIVIL WORKS)..." style="width: 80%; display: inline-block;">
                                                     </td>
                                                      <td style="white-space: nowrap; vertical-align: middle; text-align: center;">
                                                           <button type="button" class="btn btn-success btn-xs insert-so-row-below" title="Insert Row Below" style="padding: 2px 7px;"><i class="fa fa-plus"></i></button>
                                                           <button type="button" class="btn btn-danger btn-xs btn-remove-so-row" title="Delete Row" style="padding: 2px 7px;"><i class="fa fa-times"></i></button>
                                                      </td>
                                                </tr>
                                                <?php
                                                        $i++;
                                                        continue;
                                                    endif;
                                                    // ---- End Heading Row ----
                                                ?>
                                                    <tr id="row<?php echo $i; ?>"> 
                                                        <td>
                                                            <select style="width: 150px" class="form-control input-sm product_name_auto item_search_name name_list"  name="product_name[]" id="item_name<?php echo $i; ?>" onchange="myFunction1(this.id)" required="" data-live-search="true">
                                                                <option></option>

                                                                <?php
                                                                foreach ($item_name as $row) {
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
                                                       
                                                            <input type="hidden" class="form-control input-sm" name="salesorder_id[]" id="quotation_id<?php echo $i; ?>"  value="<?php echo $key->salesorder_id; ?>">
                                                        </td> 
                                                        <td>
                                                            <button type="button" class="btn btn-info description-modal-btn" onClick="descButton(this.id)" id="btnDescriptionId<?php echo $i; ?>">Description</button>


                                                            <textarea style="width: 150px; display: none;" class="form-control input-sm  description_auto hide" name="description[]" id="description<?php echo $i; ?>" rows="7"><?php echo $key->description; ?></textarea>



                                                        </td> 
                                                        <td><input type="text" name="hsn[]" value="<?php echo $key->hsn_code; ?>" id="hsn<?php echo $i; ?>" required="" class="form-control input-sm name_list" readonly="" /></td> 

                                                        <td class="hide"> <span id="total_quantity<?php echo $i; ?>" name="total_quantity[]"></span></td>
                                                        <td><input type="text" name="quantity[]" value="<?php echo $key->quantity; ?>" id="quantity<?php echo $i; ?>" required="" class="form-control input-sm name_list quantity_auto number-only-validation" value="1" /></td>
                                                        <td>
                                                <select style="width: 100px" class="form-control input-sm  item_search_unit"  name="unit[]" id="unit1"  required="" data-live-search="true">
                                                <option value="<?php echo $key->unit ?>" ><?php echo $key->unit; ?></option>
                                                    </select>
                                                </td> 
                                                        <td class="gst_per"><input type="text" readonly="" name="gst_per[]" value="<?php echo $key->gst; ?>" id="gst_per<?php echo $i; ?>" class="form-control input-sm name_list" /></td> 
                                                        <td class="gst"><input type="text" readonly="" name="sgst[]" value="<?php echo $key->sgst; ?>"  id="sgst<?php echo $i; ?>" class="form-control input-sm sgst_list" /></td> 
                                                        <td class="gst"><input type="text" readonly="" name="cgst[]" value="<?php echo $key->cgst; ?>" id="cgst<?php echo $i; ?>" class="form-control input-sm cgst_list" /></td> 
                                                        <td class="igst_edit_hide_show"><input type="text" readonly="" name="igst[]" value="<?php echo $key->igst; ?>" id="igst<?php echo $i; ?>" class="form-control input-sm igst_list" /></td> 
                                                        <td><input type="number" name="price[]" value="<?php echo $key->price; ?>" id="price<?php echo $i; ?>" required="" class="form-control input-sm price_auto"  /></td>
                                                        <td class="hide"><input type="number" min="0" maxlength="2" name="discount[]" value="<?php echo $key->discount; ?>" id="discount<?php echo $i; ?>" class="form-control input-sm name_list discount_auto number-only-validation" value="" /></td>
                                                        <td><input type="hidden" name="amount[]" id="amount<?php echo $i; ?>" class="form-control input-sm name_list amount_auto" value="<?php echo $key->amount; ?>"/>
                                                            <input type="hidden" name="amount_temp[]" id="amount_temp<?php echo $i; ?>" class="form-control input-sm name_list amount_auto" value="<?php echo $key->price * $key->quantity; ?>"/>
                                                            <input type="hidden" name="gst_amount[]" id="gst_amount<?php echo $i; ?>" class="form-control input-sm name_list gst_amount_auto" value="0.00" />
                                                            <span id="span_amount<?php echo $i; ?>" name="span_amount[]">₹<?php echo $key->amount; ?></span>
                                                        </td>
                                                        <td style="white-space: nowrap; vertical-align: middle; text-align: center;">
                                                            <input type="hidden" name="tag_no[]" id="so_tag_no<?php echo $i; ?>" value="<?php echo htmlspecialchars($key->tag_no ?? ''); ?>">
                                                            <button type="button" class="btn btn-success btn-xs insert-so-row-below" title="Insert Row Below" style="padding: 2px 7px;"><i class="fa fa-plus"></i></button>
                                                            <button type="button" class="btn btn-danger btn-xs btn-remove-so-row" title="Delete Row" style="padding: 2px 7px;"><i class="fa fa-times"></i></button>
                                                        </td>
                                                    </tr>  
                                                    <?php
                                                    $i++;
                                                }
                                                ?>
                                            </tbody>
                                        </table>  
                                        <div align="center">

                                            <button type="submit" name="submit" id="submit"  class="btn btn-success">Save</button>
                                        </div>
                                        <div align="right" style="margin: 10px">

                                            <input type="hidden" name="temp_total" id="temp_total" class="form-control input-sm temp_total" value="0.00" /><br>

                                            <span id="total_item_qty_display" style="font-weight: bold; color: #333;">Total Item Qty: 0</span><br>
                                            <span id="total_amount" name="total_amount[]" class="total_span_auto_amount">Grand Total: ₹0.00</span><br>
                                            <input type="hidden" name="basic_total" id="basic_total" class="form-control input-sm basic_total" value="0.00" />
                                            <span class="gst" id="sgst_amount" name="sgst_amount[]">SGST Amount: ₹0.00</span><br>
                                            <span class="gst" id="cgst_amount" name="cgst_amount[]">CGST Amount: ₹0.00</span><br>
                                            <span class="igst igst_edit_hide_show" id="igst_amount" name="igst_amount">IGST Amount: ₹0.00</span><br>
                                            <span id="grand_total_amount">Grand Total: ₹0.00</span><br>
                                            <span id="grand_total_words" style="font-weight: bold; color: #555;">Grand Total in Words: Zero Rupees Only</span>
                                            <input type="hidden" name="total_salesorder_amount" id="total_quotation_amount" class="form-control input-sm name_list" />
                                        </div>
                                    </div>  
                                    
                                    <div class="row">
                                        <div class="col-xs-12 col-sm-6">
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-3 control-label">Terms & Conditions</label>
                                                <div class="col-sm-9">
                                                    <textarea class="form-control" name="terms_and_conditions" id="terms_and_conditions" rows="3"><?php echo $salesorders_data_group['terms_and_conditions']; ?></textarea>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-3 control-label">Payment Terms</label>
                                                <div class="col-sm-9">
                                                    <textarea class="form-control" name="payment_terms" id="payment_terms" rows="3"><?php echo $salesorders_data_group['payment_terms']; ?></textarea>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-3 control-label">Process Schedule</label>
                                                <div class="col-sm-9">
                                                    <textarea class="form-control" name="process_schedule" id="process_schedule" rows="3"><?php echo $salesorders_data_group['process_schedule']; ?></textarea>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-xs-12 col-sm-6">
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-3 control-label">Taxes</label>
                                                <div class="col-sm-9">
                                                    <textarea class="form-control" name="taxes" id="taxes" rows="3"><?php echo $salesorders_data_group['taxes']; ?></textarea>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-3 control-label">Exclusions</label>
                                                <div class="col-sm-9">
                                                    <textarea class="form-control" name="exclusions" id="exclusions" rows="3"><?php echo $salesorders_data_group['exclusions']; ?></textarea>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-3 control-label">Note</label>
                                                <div class="col-sm-9">
                                                    <textarea class="form-control" name="salesorder_memo" id="salesorder_memo" rows="3"><?php echo $salesorders_data_group['salesorder_memo']; ?></textarea>
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
        <?php $this->load->view('admin/footer'); ?>
    </div>
    <!-- ./wrapper -->

    <!-- Add Company Modal -->
    <div id="myModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <center>
                        <h4 class="modal-title">Add Company
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </h4>
                    </center>
                </div>
                <div class="modal-body">
                    <div class="card-body">
                        <div class="form-group row required">
                            <label class="col-sm-4 control-label">Company Name</label>
                            <div class="col-sm-7">
                                <input type="hidden" name="gst_check_customer" value="gst_check_customer" id="gst_check_customer">
                                <input type="text" class="form-control" name="company_name" id="company_name" required="">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 control-label">Customer Name</label>
                            <div class="col-sm-7">
                                <input type="text" class="form-control" name="fullname" id="fullname">
                            </div>
                        </div>

                         <div class="form-group row">
                            <label class="col-sm-4 control-label">GST No</label>
                            <div class="col-sm-7">
                                <input type="text" maxlength="15" class="form-control" name="gst" id="gst" style="text-transform: uppercase;" placeholder="e.g., 27AAPFU0205R1Z0">
                            </div>
                            <small class="text-muted col-sm-7 col-sm-offset-4">15-digit GST number</small>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 control-label">PAN No</label>
                            <div class="col-sm-7">
                                <input type="text" class="form-control" name="pancard" id="pancard" style="text-transform: uppercase;" maxlength="10" placeholder="e.g., AAPFU0205R">
                            </div>
                            <small class="text-muted col-sm-7 col-sm-offset-4">10-digit PAN (auto-filled from GST if available)</small>
                        </div>
                       
                        <div class="form-group row">
                            <label class="col-sm-4 control-label">Email</label>
                            <div class="col-sm-7">
                                <input type="text" class="form-control" name="email" id="email" pattern="^([0-9a-zA-Z]([-.\w]*[0-9a-zA-Z])*@([0-9a-zA-Z][-\w]*[0-9a-zA-Z]\.)+[a-zA-Z]{2,9})$" />
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 control-label">Mobile</label>
                            <div class="col-sm-7">
                                <input type="text" class="form-control" name="mobile" id="mobile" maxlength="10" onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g, '')" />
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 control-label">State Code</label>
                            <div class="col-sm-7">
                                <input type="text" class="form-control" name="state_code" id="state_code">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 control-label">Address</label>
                            <div class="col-sm-7">
                                <textarea class="form-control input-sm" name="address" id="address"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="btnSave" class="btn btn-success performa_submit">Submit</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="glyphicon glyphicon-remove"></i> Close</button>
                </div>
            </div>
        </div>
    </div>

    <style>
        .description-modal-btn {
            width: 100%;
            white-space: normal;
            word-wrap: break-word;
        }

        /* Modal styles */
        .modal-header.bg-info {
            background-color: #5bc0de;
            color: white;
        }
        .modal-header.bg-success {
            background-color: #5cb85c;
            color: white;
        }
        .modal-header.bg-warning {
            background-color: #f0ad4e;
            color: white;
        }
        .modal-header.bg-primary {
            background-color: #337ab7;
            color: white;
        }
        .modal-header.bg-danger {
            background-color: #d9534f;
            color: white;
        }

        /* CKEditor modal styling */
        .cke_chrome {
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        /* Prevent body scroll when modal is open */
        body.modal-open {
            overflow: hidden;
        }

        /* Table styling */
        .table-responsive {
            overflow-x: auto;
        }

        #dynamic_field td {
            vertical-align: middle;
            padding: 8px;
        }

        #dynamic_field th {
            padding: 10px 8px;
            background-color: #3c8dbc;
            color: #fff;
            font-weight: bold;
        }

        .select2-container {
            width: 100% !important;
        }

        .btn-xs {
            margin: 2px;
            padding: 3px 8px;
            font-size: 12px;
        }

        /* Action header button styling */
        .action-header-btn {
            margin-left: 5px;
        }

        .hide {
            display: none;
        }

        /* ===== Section Heading Row Styling ===== */
        .so-heading-row td {
            background: linear-gradient(135deg, #e8e0f0 0%, #d4c8e8 100%) !important;
            color: #5a3d8a !important;
            font-weight: 700 !important;
            font-size: 13px !important;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 8px 12px !important;
            border: 1px solid #b8a8d4 !important;
            vertical-align: middle !important;
        }
        .so-heading-row .heading-text-input {
            text-transform: uppercase !important;
            font-weight: bold !important;
            letter-spacing: 1px;
        }
        .btn-heading {
            background: linear-gradient(135deg, #7e57c2, #5a3d8a);
            color: white;
            border: none;
        }
        .btn-heading:hover {
            background: linear-gradient(135deg, #9575cd, #7e57c2);
        }
    </style>

    <script>
        // Function to convert number to words (Indian numbering system)
        function numberToWords(num) {
            const ones = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine'];
            const tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];
            const teens = ['Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];
            
            if (num === 0) return 'Zero';
            
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

        // Calculate and update all totals
        function calculateAllTotals() {
            let basicTotal = 0;
            let sgstTotal = 0;
            let cgstTotal = 0;
            let igstTotal = 0;
            let totalQty = 0;
            
            // Sum all amounts and taxes
            $('input[name="price[]"]').each(function(index) {
                let price = parseFloat($(this).val()) || 0;
                let quantity = parseFloat($('input[name="quantity[]"]').eq(index).val()) || 0;
                let amount = price * quantity;
                basicTotal += amount;
            });

            // Sum all quantity values
            $('input[name="quantity[]"]').each(function() {
                totalQty += parseFloat($(this).val()) || 0;
            });
            
            // Sum SGST values
            $('input[name="sgst[]"]').each(function() {
                sgstTotal += parseFloat($(this).val()) || 0;
            });
            
            // Sum CGST values
            $('input[name="cgst[]"]').each(function() {
                cgstTotal += parseFloat($(this).val()) || 0;
            });
            
            // Sum IGST values
            $('input[name="igst[]"]').each(function() {
                igstTotal += parseFloat($(this).val()) || 0;
            });
            
            // Calculate grand total (use SGST+CGST OR IGST, never both — IGST = SGST+CGST)
            let grandTotal;
            var igstEditCheck = $("#igst_edit_hide_show").val();
            if (igstEditCheck === "igst_edit_hide_show") {
                grandTotal = basicTotal + igstTotal;
            } else {
                grandTotal = basicTotal + sgstTotal + cgstTotal;
            }
            
            // Update display elements
            $('#total_item_qty_display').text('Total Item Qty: ' + totalQty);
            $('#total_amount').text('Total before Taxl: ₹' + basicTotal.toFixed(2));
            $('#basic_total').val(basicTotal.toFixed(2));
            $('#temp_total').val(basicTotal.toFixed(2));
            
            $('#sgst_amount').text('SGST Amount: ₹' + sgstTotal.toFixed(2));
            $('#cgst_amount').text('CGST Amount: ₹' + cgstTotal.toFixed(2));
            $('#igst_amount').text('IGST Amount: ₹' + igstTotal.toFixed(2));
            
            $('#grand_total_amount').text('Grand Total: ₹' + grandTotal.toFixed(2));
            $('#total_quotation_amount').val(grandTotal.toFixed(2));
            
            // Convert to words
            let grandTotalAmount = Math.floor(grandTotal);
            let grandTotalWords = numberToWords(grandTotalAmount);
            $('#grand_total_words').text('Grand Total in Words: ' + grandTotalWords);
        }

        $(document).ready(function () {
            // Initial calculation
            calculateAllTotals();
            
            // Monitor for changes
            $(document).on('change input', 'input[name="price[]"], input[name="quantity[]"], input[name="sgst[]"], input[name="cgst[]"], input[name="igst[]"]', function() {
                calculateAllTotals();
            });
            
            // Periodic check
            setInterval(calculateAllTotals, 1000);

            var igst = $("#igst_edit_hide_show").val();
            var gst = $("#gst").val();
            var non_gst = $("#non_gst").val();

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
          

        });
    </script>
    
    <script>
        // Global flag to prevent multiple modal openings
        var isModalOpen = false;

        function descButton(buttonId) {
            // Prevent if modal is already open
            if (isModalOpen) {
                return;
            }
            
            isModalOpen = true;
            var rowNum = buttonId.replace('btnDescriptionId', '');
            var textarea = document.getElementById('description' + rowNum);
            var textareaContent = textarea ? textarea.value : '';
            
            // Remove existing modal if any
            $('#descriptionModal').remove();
            
            // Clean up any existing CKEditor instances
            if (CKEDITOR.instances['modalDescription' + rowNum]) {
                CKEDITOR.instances['modalDescription' + rowNum].destroy(true);
            }
            
            var modalHtml = '<div id="descriptionModal" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false">' +
                '<div class="modal-dialog modal-lg">' +
                '<div class="modal-content">' +
                '<div class="modal-header bg-info">' +
                '<button type="button" class="close" data-dismiss="modal">&times;</button>' +
                '<h4 class="modal-title">Item Description - Row ' + rowNum + '</h4>' +
                '</div>' +
                '<div class="modal-body">' +
                '<textarea id="modalDescription' + rowNum + '" class="form-control" rows="10">' + escapeHtml(textareaContent) + '</textarea>' +
                '</div>' +
                '<div class="modal-footer">' +
                '<button type="button" class="btn btn-success" onclick="saveDescription(' + rowNum + ')">Save</button>' +
                '<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>' +
                '</div>' +
                '</div>' +
                '</div>' +
                '</div>';
            
            $('body').append(modalHtml);
            
            // Handle modal hidden event to reset flag
            $('#descriptionModal').on('hidden.bs.modal', function () {
                isModalOpen = false;
                // Clean up CKEditor when modal is hidden
                if (CKEDITOR.instances['modalDescription' + rowNum]) {
                    CKEDITOR.instances['modalDescription' + rowNum].destroy(true);
                }
                $(this).remove();
            });
            
            // Initialize CKEditor after modal is shown
            $('#descriptionModal').on('shown.bs.modal', function () {
                setTimeout(function() {
                    if (!CKEDITOR.instances['modalDescription' + rowNum]) {
                        CKEDITOR.replace('modalDescription' + rowNum, {
                            height: '300px'
                        });
                    }
                }, 100);
            });
            
            $('#descriptionModal').modal('show');
        }

        function saveDescription(rowNum) {
            if (CKEDITOR.instances['modalDescription' + rowNum]) {
                var editorContent = CKEDITOR.instances['modalDescription' + rowNum].getData();
                $('#description' + rowNum).val(editorContent);
            }
            
            $('#descriptionModal').modal('hide');
            isModalOpen = false;
        }

        function escapeHtml(text) {
            if (!text) return '';
            var map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, function(m) { return map[m]; });
        }
    </script>
    
    <script>
        CKEDITOR.replace('terms_and_conditions');
        CKEDITOR.replace('payment_terms');
        CKEDITOR.replace('process_schedule');
        CKEDITOR.replace('taxes');
        CKEDITOR.replace('exclusions');
        CKEDITOR.replace('salesorder_memo');

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

    <script>
        $(document).ready(function() {
            // Save all initial project code options
            window.allProjectOptions = $('#project_code').html();

            $('#project_code').on('change', function(e, isAutoTriggered) {
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
                                $('#customer_id').val(response.customer.customer_id).trigger('change', [true]);
                            }
                            $('#customer_code').val(response.client_code);
                            if (response.project) {
                                $('#system').val(response.project.system || '').trigger('change');
                            }
                        } else {
                            console.log(response.message || 'No details found');
                        }
                    },
                    error: function(xhr) {
                        console.error('Error fetching project details:', xhr.responseText);
                    }
                });
            });

            // Parse parameters on load
            var urlCompanyId = getUrlParameter('company_id');
            var urlProjectCode = getUrlParameter('project_code');
            if (urlCompanyId) {
                $('#customer_id').val(urlCompanyId).trigger('change', [true]);
            }
            if (urlProjectCode) {
                $('#project_code').val(urlProjectCode).trigger('change', [true]);
        });
    </script>
    
    <script>
        // Add Section Heading Row for Sales Order
        function addSoHeadingRow() {
            var rowCount = $('#dynamic_field tr').length;
            var headingHtml = '<tr id="row' + rowCount + '" class="so-heading-row">' +
                '<td colspan="11">' +
                '  <input type="hidden" name="product_name[]" value="__HEADING__">' +
                '  <input type="hidden" name="quantity[]" value="0">' +
                '  <input type="hidden" name="unit[]" value="">' +
                '  <select name="tag_no[]" class="heading-type-select form-control input-sm" style="width: auto; display: inline-block; margin-right: 8px;">' +
                '    <option value="MAIN" selected>Main Heading</option>' +
                '    <option value="SUB">Sub Heading</option>' +
                '  </select>' +
                '  <input type="hidden" name="scope[]" value="">' +
                '  <input type="hidden" name="stores_remark[]" value="">' +
                '  <input type="hidden" name="remark[]" value="">' +
                '  <input type="hidden" name="price[]" value="0">' +
                '  <input type="hidden" name="product_code[]" value="__HEADING__">' +
                '  <input type="hidden" name="hsn[]" value="">' +
                '  <input type="hidden" name="gst_per[]" value="0">' +
                '  <input type="hidden" name="sgst[]" value="0">' +
                '  <input type="hidden" name="cgst[]" value="0">' +
                '  <input type="hidden" name="igst[]" value="0">' +
                '  <input type="hidden" name="amount[]" value="0">' +
                '  <input type="hidden" name="salesorder_id[]" value="">' +
                '  <input type="hidden" name="discount[]" value="0">' +
                '  <input type="hidden" name="amount_temp[]" value="0">' +
                '  <input type="hidden" name="gst_amount[]" value="0">' +
                '  <i class="fa fa-tag" style="margin-right:8px;opacity:0.7;"></i>' +
                '  <input type="text" name="description[]" class="form-control input-sm heading-text-input" placeholder="ENTER SECTION HEADING (E.G. INSTRUMENTS, CIVIL WORKS)..." style="width: 80%; display: inline-block;">' +
                '</td>' +
                '<td style="white-space: nowrap; vertical-align: middle; text-align: center;">' +
                '  <input type="hidden" name="tag_no[]" value="">' +
                '  <button type="button" class="btn btn-success btn-xs insert-so-row-below" title="Insert Row Below" style="padding: 2px 7px;"><i class="fa fa-plus"></i></button>' +
                '  <button type="button" class="btn btn-danger btn-xs btn-remove-so-row" title="Delete Row" style="padding: 2px 7px;"><i class="fa fa-times"></i></button>' +
                '</td>' +
                '</tr>';
            $('#dynamic_field tbody').append(headingHtml);

            var $newRow = $('#dynamic_field tr:last');
            var $input = $newRow.find('.heading-text-input');

            styleHeadingRow($newRow);
            updateHeadingAssociations();
            $input.focus();
        }

        // Bind Add Heading button
        $(document).on('click', '#add_so_heading_row', function() {
            addSoHeadingRow();
        });

        // Bind Add Heading Row Below button click
        $(document).on('click', '.add-so-heading-row-below', function(e) {
            e.preventDefault();
            var $currentRow = $(this).closest('tr');
            addSoHeadingRow();
            setTimeout(function() {
                var $newRow = $('#dynamic_field tr:last');
                $newRow.insertAfter($currentRow);
                updateHeadingAssociations();
            }, 300);
        });

        // Bind Insert Row Below button click (debounced to prevent duplicate row creation)
        $(document).off('click', '.insert-so-row-below').on('click', '.insert-so-row-below', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var $btn = $(this);
            if ($btn.data('adding')) return;
            $btn.data('adding', true);
            
            var $currentRow = $btn.closest('tr');
            insertSoRowBelow($currentRow);
            
            setTimeout(function() {
                $btn.data('adding', false);
            }, 400);
        });

        function insertSoRowBelow($currentRow) {
            $('#add_so').trigger('click');
            setTimeout(function() {
                var $newRow = $('#dynamic_field tr:last');
                $newRow.insertAfter($currentRow);
                updateHeadingAssociations();
            }, 300);
        }

        // Style heading row based on type
        function styleHeadingRow($tr) {
            var headingType = $tr.find('.heading-type-select').val();
            if (headingType === 'MAIN') {
                $tr.find('td').css({
                    'background': 'linear-gradient(135deg, #e8e0f0 0%, #d4c8e8 100%)',
                    'color': '#5a3d8a'
                });
                $tr.find('.heading-text-input').css('color', '#5a3d8a');
            } else if (headingType === 'SUB') {
                $tr.find('td').css({
                    'background': '#fdeada',
                    'color': '#000000'
                });
                $tr.find('.heading-text-input').css('color', '#000000');
            }
        }

        // Update section styles dynamically
        function updateHeadingAssociations() {
            $('#dynamic_field tr').each(function() {
                var $row = $(this);
                if ($row.hasClass('so-heading-row')) {
                    styleHeadingRow($row);
                }
            });
        }

        // Listen for dynamic heading type select change
        $(document).on('change', '.heading-type-select', function() {
            styleHeadingRow($(this).closest('tr'));
            updateHeadingAssociations();
        });

        // Listen for dynamic heading input changes
        $(document).on('input', '.heading-text-input', function() {
            styleHeadingRow($(this).closest('tr'));
            updateHeadingAssociations();
        });

        // Row reordering: Move Up
        $(document).on('click', '.move-row-up', function(e) {
            e.preventDefault();
            var $row = $(this).closest('tr');
            var $prev = $row.prev();
            if ($prev.length) {
                $row.insertBefore($prev);
                updateHeadingAssociations();
            }
        });

        // Row reordering: Move Down
        $(document).on('click', '.move-row-down', function(e) {
            e.preventDefault();
            var $row = $(this).closest('tr');
            var $next = $row.next();
            if ($next.length) {
                $row.insertAfter($next);
                updateHeadingAssociations();
            }
        });

        // Remove row functionality (local helper for heading rows and newly added rows)
        $(document).on('click', '.btn-remove-so-row', function(e) {
            e.preventDefault();
            $(this).closest('tr').remove();
            if (typeof calculateAllTotals === 'function') {
                calculateAllTotals();
            }
            updateHeadingAssociations();
        });

        $(document).ready(function() {
            updateHeadingAssociations();
        });
    </script>
</body>