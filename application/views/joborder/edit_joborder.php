<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (isset($session_data_head1)) {
    
} else {
    header($this->config->item('header'));
}
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<style>
    .required label {
        font-weight: bold;
    }
    .required label:after {
        color: #e32;
        content: '*';
        display:inline;
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
    }
    
    .select2-container {
        width: 100% !important;
    }
    
    .btn-xs {
        margin: 2px;
    }
    
    /* Action header button styling */
    .action-header-btn {
        margin-left: 5px;
    }
    
    .hide {
        display: none;
    }

    /* ===== Section Heading Row Styling ===== */
    .jo-heading-row {
        display: table-row !important;
        visibility: visible !important;
    }
    .jo-heading-row td {
        background: linear-gradient(135deg, #e8e0f0 0%, #d4c8e8 100%) !important;
        color: #5a3d8a !important;
        font-weight: bold;
        font-family: 'Calibri', sans-serif;
        min-height: 38px !important;
        padding: 8px !important;
        vertical-align: middle !important;
    }
    
    .jo-heading-row .heading-text-input {
        text-transform: uppercase !important;
        font-weight: bold !important;
        letter-spacing: 1px;
    }
    
    .jo-heading-row .heading-type-select {
        background: rgba(255, 255, 255, 0.6) !important;
        border: 1px solid #5a3d8a !important;
        color: #5a3d8a !important;
        font-weight: bold !important;
        border-radius: 4px !important;
        padding: 2px 6px !important;
    }
    
    .btn-heading {
        background: linear-gradient(135deg, #7e57c2 0%, #5a3d8a 100%) !important;
        color: #ffffff !important;
        border: none !important;
        box-shadow: 0 2px 4px rgba(90, 61, 138, 0.3) !important;
        transition: all 0.3s ease !important;
    }
    
    .btn-heading:hover {
        background: linear-gradient(135deg, #9575cd 0%, #7e57c2 100%) !important;
        box-shadow: 0 4px 8px rgba(90, 61, 138, 0.4) !important;
        transform: translateY(-1px) !important;
    }
</style>  

<body class="hold-transition skin-blue sidebar-mini">
    <div id="loader" class="center"></div> 
    <div class="wrapper">

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    Job Order
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="<?php echo base_url() . 'JobOrderController/index/' ?>"> Job Order</a></li>
                    <li class="active"> Edit Job Order Details</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">

                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-info">
                            <div class="box-header">
                                <h3 class="box-title">Edit Job Order</h3>
                                <a href="javascript:void(0);" onclick="window.history.back()" class="btn btn-primary pull-right"><i class="fa fa-close"></i> Close</a>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">

                                

                                <form class="form-horizontal form_overlay" name="add_name" id="add_name" method="post" action="<?php echo base_url(); ?>JobOrderController/add_joborder" enctype="multipart/form-data">
                                    <div class="row">

                                        <div class="col-md-12">
                                            <div class="form-group row">
                                                <input type="hidden" name="edit_joborder" value="edit_joborder">
                                                <input type="hidden" name="gst_discount_check" value="gst_discount_check" id="gst_discount_check">
                                                <input type="hidden" class="form-control input-sm" name="number" id="number" required="" value="<?php echo isset($joborder_data_group['number']) ? $joborder_data_group['number'] : ''; ?>">
                                                <input type="hidden" name="joborder_gst_check" value="gst" id="joborder_gst_check">
                                                <input type="hidden" name="system" id="system" value="<?php echo isset($joborder_data_group['system']) ? htmlspecialchars($joborder_data_group['system']) : ''; ?>">
                                                <input type="hidden" name="location" id="location" value="<?php echo isset($joborder_data_group['location']) ? htmlspecialchars($joborder_data_group['location']) : ''; ?>">
                                                <input type="hidden" name="capacity" id="capacity" value="<?php echo isset($joborder_data_group['capacity']) ? htmlspecialchars($joborder_data_group['capacity']) : ''; ?>">
                                                <input type="hidden" name="so_number" id="so_number" value="<?php echo isset($joborder_data_group['oc_number']) && !empty($joborder_data_group['oc_number']) ? htmlspecialchars($joborder_data_group['oc_number']) : ''; ?>">
                                                <label class="col-sm-12 control-label"><h2>Edit Job Order: <b><?php echo isset($joborder_data_group['number']) ? $joborder_data_group['number'] : ''; ?></b></h2></label>
                                            </div>
                                        </div>    

                                        <div class="col-md-3">
                                            <div class="form-group row">
                                                <label class="col-sm-4 control-label">Company<span style="color: red;">*</span></label>
                                                <div class="col-sm-8">
                                                    <select class="form-control input-sm company_search_name" name="customer_id" id="customer_id" required="">
                                                        <option value="" data-ccode="">Select Company</option>
                                                        <?php foreach ($company_name as $key) { ?>
                                                            <option value="<?php echo $key->customer_id; ?>" data-ccode="<?php echo htmlspecialchars($key->c_code); ?>" <?php echo (isset($joborder_data_group['customer_id_fk']) && $joborder_data_group['customer_id_fk'] == $key->customer_id) ? 'selected="selected"' : ''; ?>><?php echo $key->company_name . " - " . $key->c_code; ?></option> 
                                                        <?php } ?>  
                                                    </select>
                                                    <input type="hidden" name="customer_code" id="customer_code" value="<?php echo isset($joborder_data_group['customer_code']) ? htmlspecialchars($joborder_data_group['customer_code']) : ''; ?>">
                                                    <span class="btn btn-success btn-sm" data-toggle="modal" data-target="#myModal" style="margin-top: 10%"><i class="glyphicon glyphicon-plus"></i>Add Company</span>
 
                                                </div>
                                            </div>
                                            
                                            <!-- ★ SO Number Selector ★ -->
                                            <div class="form-group row" style="background: #f0f8ff; border: 1px solid #b8d9f5; border-radius: 6px; padding: 8px 12px; margin-bottom: 6px;">
                                                <label class="col-sm-4 control-label" style="color: #1565C0; font-weight: 600;">
                                                    <i class="fa fa-file-text-o"></i> SO Number
                                                </label>
                                                <div class="col-sm-8">
                                                    <select class="form-control input-sm" name="" id="bom_so_number_select">
                                                        <option value="">-- Select SO Number --</option>
                                                        <?php if(isset($so_list_for_selector) && !empty($so_list_for_selector)): ?>
                                                            <?php foreach($so_list_for_selector as $so): ?>
                                                                <option value="<?php echo htmlspecialchars($so->so_number); ?>">
                                                                    <?php echo htmlspecialchars($so->so_number); ?>
                                                                    <?php if(!empty($so->customer_name)) echo ' - ' . htmlspecialchars($so->customer_name); ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        <?php endif; ?>
                                                    </select>
                                                    <span id="bom_so_fetch_status" style="color:#666;font-size:11px;display:block;margin-top:2px;"></span>
                                                    <span id="bom_so_loading" style="display:none;color:#1565C0;font-size:11px;"><i class="fa fa-spinner fa-spin"></i> Fetching...</span>
                                                </div>
                                            </div>
                                            <!-- /SO Number Selector -->
                                            
                                             <?php
$_sess_perm = $this->session->userdata('session_data_head');
$_has_project_master = isset($_sess_perm['permission']) && in_array('Projects', $_sess_perm['permission']);
if ($_has_project_master): ?>
<div class="form-group row hide">
                                               <label class="col-sm-4 control-label">Project Code</label>
                                                <div class="col-sm-8">
                                                    <select class="form-control input-sm company_search_name" name="project_code" id="project_code">
                                                        <option value="">Select project code</option>
                                                        <?php foreach ($project_code_result as $key) { ?>
                                                            <option value="<?php echo $key->project_code; ?>" <?php echo (isset($joborder_data_group['project_code']) && $joborder_data_group['project_code'] == $key->project_code) ? 'selected="selected"' : ''; ?>><?php echo $key->project_code; ?></option> 
                                                        <?php } ?>   
                                                    </select>
                                                </div>
                                            </div>
<?php endif; // Project Master permission ?>

                                            


                                            
                                        </div>
                                        
                                        <div class="col-md-3">
                                            <div class="form-group row">
                                                <label class="col-sm-4 control-label">Date<span style="color: red;">*</span></label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control alldate input-sm created-date" name="date" id="date" required=""  value="<?php echo isset($joborder_data_group['date']) && $joborder_data_group['date'] != '0000-00-00' ? date('d-m-Y', strtotime($joborder_data_group['date'])) : ''; ?>">
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label class="col-sm-4 control-label">Status</label>
                                                <div class="col-sm-8">
                                                    <select class="form-control input-sm" name="status_main" id="status_main">
                                                        <option value="1" <?php echo (isset($joborder_data_group['status']) && $joborder_data_group['status'] == '1') ? 'selected="selected"' : ''; ?>>Draft</option>
                                                        <option value="2" <?php echo (isset($joborder_data_group['status']) && $joborder_data_group['status'] == '2') ? 'selected="selected"' : ''; ?>>Sent</option>
                                                        <option value="3" <?php echo (isset($joborder_data_group['status']) && $joborder_data_group['status'] == '3') ? 'selected="selected"' : ''; ?>>Viewed</option>
                                                        <option value="4" <?php echo (isset($joborder_data_group['status']) && $joborder_data_group['status'] == '4') ? 'selected="selected"' : ''; ?>>Approved</option>
                                                        <option value="5" <?php echo (isset($joborder_data_group['status']) && $joborder_data_group['status'] == '5') ? 'selected="selected"' : ''; ?>>Rejected</option>
                                                        <option value="6" <?php echo (isset($joborder_data_group['status']) && $joborder_data_group['status'] == '6') ? 'selected="selected"' : ''; ?>>Canceled</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group row hide">
                                                <label for="project_qty" class="col-sm-4 control-label">Project Quantity</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control input-sm" name="project_qty" id="project_qty" value="<?php echo isset($joborder_data_group['project_qty']) ? $joborder_data_group['project_qty'] : ''; ?>">
                                                </div>
                                            </div>

                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group row">
                                                <label for="oc_number" class="col-sm-4 control-label">SO Number</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control input-sm" name="oc_number" id="oc_number"
                                                        value="<?php echo isset($joborder_data_group['oc_number']) ? $joborder_data_group['oc_number'] : ''; ?>"
                                                        autocomplete="off">
                                                </div>
                                            </div>

                                        

                                            
                                        </div>
                                        </div>
                                    </div>

                                    <!-- Linked Sales Order Items Reference Card (auto-loaded from stored SO) -->
                                    <div class="row" id="so_items_reference_container" style="display: none; margin-bottom: 20px;">
                                        <div class="col-md-12">
                                            <div class="so-ref-card" style="background: rgba(23, 162, 184, 0.05); border: 1px solid rgba(23, 162, 184, 0.2); border-left: 5px solid #17a2b8; border-radius: 8px; padding: 15px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05); transition: all 0.3s ease;">
                                                <!-- SO Items header & list -->
                                                <div style="display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid rgba(23, 162, 184, 0.1); padding-bottom: 8px; margin-bottom: 12px;">
                                                    <h4 style="margin: 0; color: #17a2b8; font-size: 15px; font-weight: 600; display: flex; align-items: center; gap: 8px;">
                                                        <i class="fa fa-shopping-cart" style="font-size: 16px;"></i>
                                                        Linked Sales Order Items <span style="font-size: 11px; font-weight: normal; color: #6c757d; margin-left: 5px;">(Reference Finished Goods)</span>
                                                    </h4>
                                                    <span class="label label-info" id="so_ref_badge" style="border-radius: 12px; padding: 4px 8px; font-size: 11px;">0 Items</span>
                                                </div>
                                                <div class="so-ref-body" style="display: flex; flex-wrap: wrap; gap: 10px;" id="so_items_list">
                                                    <!-- Items will be loaded dynamically here -->
                                                </div>

                                                <!-- Drawings header & list -->
                                                <div style="display: none; align-items: center; justify-content: space-between; border-bottom: 1px solid rgba(23, 162, 184, 0.1); padding-bottom: 8px; margin-bottom: 12px; margin-top: 15px;" id="drawings_ref_header">
                                                    <h4 style="margin: 0; color: #17a2b8; font-size: 15px; font-weight: 600; display: flex; align-items: center; gap: 8px;">
                                                        <i class="fa fa-file-picture-o" style="font-size: 16px;"></i>
                                                        Linked Drawings <span style="font-size: 11px; font-weight: normal; color: #6c757d; margin-left: 5px;">(Latest Revisions)</span>
                                                    </h4>
                                                    <span class="label label-info" id="drawings_ref_badge" style="border-radius: 12px; padding: 4px 8px; font-size: 11px;">0 Drawings</span>
                                                </div>
                                                <div class="so-ref-body" style="display: none; flex-wrap: wrap; gap: 10px;" id="drawings_ref_list">
                                                    <!-- Drawings will be loaded dynamically here -->
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="table-responsive">  
                                        <table class="table table-bordered" id="dynamic_field">  
                                            <thead>
                                                <tr>
                                                    <th>Product Name</th>
                                                    <th>Description</th>
                                                    <th>QTY</th>
                                                    <th>Unit</th>
                                                    <th>Tag NO.</th>
                                                    <th>Scope</th>
                                                    <th>Stores Remark</th>
                                                    <th>Price</th>
                                                    <th>Remark</th>
                                                    <th>Action 
                                                       
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody id="table-body">
                                                <?php
                                                    $i = 1;
                                                    foreach ($show_joborder as $key):
                                                        // ---- Render Section Heading Row ----
                                                        if (isset($key->product_name) && $key->product_name === '__HEADING__'):
                                                 ?>
                                                 <tr id="row<?php echo $i; ?>" class="jo-heading-row">
                                                     <td colspan="9">
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
                                                         <i class="fa fa-tag" style="margin-right:8px;opacity:0.7;"></i>
                                                         <input type="text" name="description[]" class="form-control input-sm heading-text-input" value="<?php echo htmlspecialchars($key->description ?? ''); ?>" placeholder="ENTER SECTION HEADING (E.G. INSTRUMENTS, CIVIL WORKS)..." style="width: 80%; display: inline-block;">
                                                     </td>
                                                     <td class="text-center" style="white-space: nowrap; vertical-align: middle;">
                                                         <button type="button" class="btn btn-success btn-xs insert-row-below" title="Insert Row Below" style="padding: 1px 5px; margin-right: 2px;"><i class="fa fa-plus"></i></button>
                                                         <button type="button" class="btn btn-default btn-xs move-row-up" title="Move Up" style="padding: 1px 5px; margin-right: 2px;"><i class="fa fa-arrow-up"></i></button>
                                                         <button type="button" class="btn btn-default btn-xs move-row-down" title="Move Down" style="padding: 1px 5px; margin-right: 2px;"><i class="fa fa-arrow-down"></i></button>
                                                         <button type="button" class="btn btn-danger btn-xs btn_remove" title="Remove" style="padding: 1px 5px;">X</button>
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
                                                        <select style="width: 150px;" class="form-control input-sm item_search_name" name="product_name[]" id="item_name<?php echo $i; ?>" onchange="myFunction1(this.id)" required="">
                                                            <?php if(isset($key->product_name) && !empty($key->product_name)): ?>
                                                                <option value="<?php echo $key->product_name; ?>" selected="selected"><?php echo $key->product_name . " - " . (isset($key->item_name) ? $key->item_name : ''); ?></option>
                                                            <?php else: ?>
                                                                <option value="">Select Product</option>
                                                            <?php endif; ?>
                                                            <?php foreach ($item_name as $row) { ?>
                                                                <option value="<?php echo $row->code; ?>"    <?php
                                                                if ($key->product_name == $row->code) {
                                                                    echo 'selected="selected"';
                                                                }
                                                                ?>><?php echo $row->code . " - " . $row->item_name; ?></option>
                                                            <?php } ?>  
                                                        </select>
                                                    </td> 
                                                    <td>
                                                        <button type="button" class="btn btn-info btn-xs" onclick="descButton(this.id)" id="btnDescriptionId<?php echo $i; ?>" title="Edit Description">Description</button>
                                                        <textarea style="width: 150px; " class="form-control input-sm name_list description_auto hide" name="description[]" id="description<?php echo $i; ?>" rows="4"><?php echo isset($key->description) ? $key->description : ''; ?></textarea>
                                                    </td> 
                                                    <td>
                                                        <input type="text" name="quantity[]" id="quantity<?php echo $i; ?>" class="form-control input-sm" value="<?php echo isset($key->quantity) ? $key->quantity : '1'; ?>" />
                                                    </td> 
                                                    <td>
                                                        <select class="form-control input-sm item_search_unit" name="unit[]" id="unit<?php echo $i; ?>">
                                                            <option value="">Select Unit</option>
                                                            <?php if(isset($unit_result) && !empty($unit_result)): ?>
                                                                <?php foreach ($unit_result as $unit): ?>
                                                                    <option value="<?php echo $unit->unit; ?>" <?php echo (isset($key->unit) && $key->unit == $unit->unit) ? 'selected="selected"' : ''; ?>><?php echo $unit->unit; ?></option>
                                                                <?php endforeach; ?>
                                                            <?php endif; ?>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="tag_no[]" id="tag_no<?php echo $i; ?>" class="form-control input-sm" value="<?php echo isset($key->tag_no) ? $key->tag_no : ''; ?>" />
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-info btn-xs" onclick="scopeButton(this.id)" id="btnScopeId<?php echo $i; ?>" title="Edit Scope">Scope</button>
                                                        <textarea class="form-control input-sm hide" name="scope[]" id="scope<?php echo $i; ?>" rows="4"><?php echo isset($key->scope) ? $key->scope : ''; ?></textarea>
                                                    </td> 
                                                    <td>
                                                        <select class="form-control input-sm" name="stores_remark[]" id="stores_remark<?php echo $i; ?>">
                                                            <option value="">Select</option>
                                                            <option value="Y" <?php echo (isset($key->stores_remark) && $key->stores_remark == 'Y') ? 'selected="selected"' : ''; ?>>Yes</option>
                                                            <option value="N" <?php echo (isset($key->stores_remark) && $key->stores_remark == 'N') ? 'selected="selected"' : ''; ?>>No</option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="price[]" id="price<?php echo $i; ?>" class="form-control input-sm" value="<?php echo isset($key->price) ? $key->price : ''; ?>" />
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-info btn-xs" onclick="remarkButton(this.id)" id="btnRemarkId<?php echo $i; ?>" title="Edit Remark">Remark</button>
                                                        <textarea class="form-control input-sm hide" name="remark[]" id="remark<?php echo $i; ?>" rows="4"><?php echo isset($key->remark) ? $key->remark : ''; ?></textarea>
                                                    </td> 
                                                    <td class="text-center" style="white-space: nowrap; vertical-align: middle;">
                                                        <?php if ($i == 1) { ?>
                                                            <button type="button" name="add_joborder_item" id="add_gst_bom" class="btn btn-success btn-xs action-header-btn" title="Add New Row" data-local-row-handler="1"><i class="fa fa-plus-circle"></i></button>
                                                            <button type="button" id="add_heading_row" class="btn btn-heading btn-xs action-header-btn" title="Add Section Heading"><i class="fa fa-tag" style="font-size:13px;"></i></button>
                                                            <button type="button" class="btn btn-danger btn-xs action-header-btn" title="Delete Row" onclick="deleteRow('row1')">X</button>
                                                        <?php } else { ?>
                                                            <button type="button" class="btn btn-default btn-xs move-row-up" title="Move Up" style="padding: 1px 5px; margin-right: 2px;"><i class="fa fa-arrow-up"></i></button>
                                                            <button type="button" class="btn btn-default btn-xs move-row-down" title="Move Down" style="padding: 1px 5px; margin-right: 2px;"><i class="fa fa-arrow-down"></i></button>
                                                            <button type="button" name="remove_joborder_item" id="remove<?php echo $i; ?>" class="btn btn-danger btn-xs action-header-btn btn_remove" title="Remove Row">X</button>
                                                        <?php } ?>
                                                    </td>  
                                                </tr>  
                                                <?php
                                                        $i++;
                                                    endforeach;
                                              
                                                ?>
                                          
                                            </tbody>
                                        </table>
                                        
                                        <br>
                                        
                                        <div class="row">
                                            <div class="col-xs-12">
                                                <div class="form-group row">
                                                    <label class="col-sm-1 control-label">Note</label>
                                                    <div class="col-sm-11">
                                                        <textarea class="form-control" name="note" id="note" rows="3" placeholder="Enter any additional notes..."><?php echo isset($joborder_data_group['note']) ? $joborder_data_group['note'] : ''; ?></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-xs-12 text-right" style="margin-bottom: 10px;">
                                                <span id="total_item_qty_display" style="font-weight: bold; color: #333;">Total Item Qty: 0</span>
                                            </div>
                                            <div class="col-xs-12 text-center">
                                                <button type="submit" name="submit" id="submit" class="btn btn-success btn-lg"><i class="fa fa-save"></i> Save </button>
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

    <!-- Description Modal -->
    <div class="modal fade" id="ModalDescriptionId" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Edit Description for: <span id="item_name_modal"></span></h4>
                </div>
                <div class="modal-body">
                    <textarea name="descriptionmodal" id="descriptionmodal" rows="10"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="saveDescription()">Save</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scope Modal -->
    <div class="modal fade" id="ModalScopeId" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Edit Scope</h4>
                </div>
                <div class="modal-body">
                    <textarea name="scopemodal" id="scopemodal" rows="10"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="saveScope()">Save</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Remark Modal -->
    <div class="modal fade" id="ModalRemarkId" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Edit Remark</h4>
                </div>
                <div class="modal-body">
                    <textarea name="remarkmodal" id="remarkmodal" rows="10"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="saveRemark()">Save</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Customer Modal -->
    <div id="myModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header btn-danger">
                    <center>
                        <h4 class="modal-title">Add Company
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </h4>
                    </center>
                </div>
                <form method="post" action="<?php echo base_url(); ?>JobOrderController/add_customer">
                    <div class="modal-body">
                        <div class="card-body">
                            <input type="hidden" name="redirect_joborder" value="gst">
                            
                            <div class="form-group row required">
                                <label class="col-sm-4 control-label">Company Name</label>
                                <div class="col-sm-7">
                                    <input type="text" class="form-control" name="company_name" required="">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-4 control-label">Customer Name</label>
                                <div class="col-sm-7">
                                    <input type="text" class="form-control" name="fullname">
                                </div>
                            </div>

                                 <div class="form-group row">
                                <label class="col-sm-4 control-label">GST No</label>
                                <div class="col-sm-7">
                                    <input type="text" maxlength="15" class="form-control" name="gst" style="text-transform: uppercase;">
                                </div>
                            </div>


                            <div class="form-group row">
                                <label class="col-sm-4 control-label">PAN No</label>
                                <div class="col-sm-7">
                                    <input type="text" class="form-control" name="pancard" style="text-transform: uppercase;">
                                </div>
                            </div>

                       
                            <div class="form-group row">
                                <label class="col-sm-4 control-label">Email</label>
                                <div class="col-sm-7">
                                    <input type="email" class="form-control" name="email">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-4 control-label">Mobile</label>
                                <div class="col-sm-7">
                                    <input type="text" class="form-control" name="mobile" maxlength="10" onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g, '')">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-4 control-label">State Code</label>
                                <div class="col-sm-7">
                                    <input type="text" class="form-control" name="state_code">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-4 control-label">Address</label>
                                <div class="col-sm-7">
                                    <textarea class="form-control input-sm" name="address" rows="3"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Submit</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</body>
</html>

<script>
var intIdVal = "";
var item_id_new = "";
var currentRowCount = <?php echo isset($show_joborder) && is_array($show_joborder) ? count($show_joborder) : 1; ?>;

function updateTotalQty() {
    var totalQty = 0;
    $('input[name="quantity[]"]').each(function() {
        totalQty += parseFloat($(this).val()) || 0;
    });
    $('#total_item_qty_display').text('Total Item Qty: ' + totalQty);
}

$(document).ready(function () {
    updateHeadingAssociations();
    updateTotalQty();
    $(document).on('input change', 'input[name="quantity[]"]', function() { updateTotalQty(); });
    setInterval(updateTotalQty, 500);

    if ($.fn.select2) {
        // Exclude heading rows from select2 to prevent layout issues
        $('#table-body tr:not(.jo-heading-row) .item_search_name, #table-body tr:not(.jo-heading-row) .item_search_unit, .company_search_name').select2();
        // Force heading rows visible after select2 may have affected layout
        $('.jo-heading-row').show();
    }

    if ($.fn.datepicker) {
        $('.alldate').datepicker({
            dateFormat: 'dd-mm-yy',
            changeMonth: true,
            changeYear: true
        });
    }

    $('#customer_id').on('change', function() {
        var ccode = $(this).find('option:selected').data('ccode') || '';
        $('#customer_code').val(ccode);
    });
    $('#customer_id').trigger('change');

    $('#add_gst_bom').on('click', function() {
        addNewRow();
    });

    // Auto-load SO & BOM reference panels on page load using stored SO number
    var storedSoNumber = $('#so_number').val();
    // Fallback: read from the visible OC/SO Number text input if hidden field is empty
    if (!storedSoNumber || storedSoNumber.trim() === '') {
        storedSoNumber = $('#oc_number').val();
    }
    if (storedSoNumber && storedSoNumber.trim() !== '') {
        loadEditReferenceData(storedSoNumber.trim());
    }

    // Final safety: ensure heading rows are always visible after all init
    setTimeout(function() {
        $('.jo-heading-row').show().css('display', 'table-row');
        updateHeadingAssociations();
    }, 500);
});

/**
 * Loads SO items and Drawings into the reference panels for edit page.
 * Re-uses the same AJAX endpoint as create page.
 */
function loadEditReferenceData(soNumber) {
    $.ajax({
        url: '<?php echo base_url(); ?>JobOrderController/get_salesorder_items',
        type: 'POST',
        data: { so_number: soNumber },
        dataType: 'json',
        success: function(response) {
            if (!response.success) return;

            var soItems  = response.items    || [];
            var drawings = response.drawings || [];
            var bomItems = response.bom_items || [];

            // ---- Populate SO Items reference pills ----
            var $soItemsList = $('#so_items_list');
            $soItemsList.empty();
            if (soItems.length > 0) {
                $.each(soItems, function(idx, item) {
                    var unitStr  = item.unit ? ' ' + item.unit : '';
                    var itemHtml = '<div style="background:#fff;border:1px solid #e9ecef;border-radius:6px;padding:6px 12px;display:inline-flex;align-items:center;gap:10px;box-shadow:0 2px 4px rgba(0,0,0,0.02);margin-right:5px;margin-bottom:5px;">' +
                                   '<span style="color:#495057;font-weight:500;">' + (item.product_name || item.product_code || '') + '</span>' +
                                   '<span class="label label-default" style="background:#e9ecef;color:#495057;border-radius:4px;padding:2px 6px;font-size:11px;font-weight:600;">Qty: ' + (item.quantity || 0) + unitStr + '</span>' +
                                   '</div>';
                    $soItemsList.append(itemHtml);
                });
                $('#so_ref_badge').text(soItems.length + (soItems.length === 1 ? ' Item' : ' Items'));
            }


            // ---- Populate Drawings reference pills ----
            var $drawingsRefList = $('#drawings_ref_list');
            $drawingsRefList.empty();
            if (drawings.length > 0) {
                $.each(drawings, function(idx, d) {
                    var fileLinks = [];
                    if (d.files && d.files.length > 0) {
                        $.each(d.files, function(fidx, f) {
                            var downloadUrl = '<?php echo base_url(); ?>DrawingController/download_file/' + f.file_id;
                            var viewUrl     = '<?php echo base_url(); ?>DrawingController/view_file/'     + f.file_id;
                            var fName = (f.file_name || 'File');
                            fileLinks.push(
                                '<span style="font-size:12px;color:#6c757d;max-width:150px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="' + fName + '">' + fName + '</span> ' +
                                '<a href="' + viewUrl     + '" class="btn btn-info btn-xs"    target="_blank" style="padding:1px 4px;font-size:10px;margin-left:4px;"><i class="fa fa-eye"></i> View</a>' +
                                '<a href="' + downloadUrl + '" class="btn btn-default btn-xs" target="_blank" style="padding:1px 4px;font-size:10px;margin-left:2px;"><i class="fa fa-download"></i></a>'
                            );
                        });
                    }
                    var filesHtml = fileLinks.length > 0
                        ? '<div style="display:flex;align-items:center;gap:4px;border-left:1px solid #ddd;padding-left:10px;margin-left:5px;">' + fileLinks.join('') + '</div>'
                        : '';
                    var revStr   = d.latest_revision ? ' Rev: ' + d.latest_revision : '';
                    var drawHtml = '<div style="background:#fff;border:1px solid #e9ecef;border-radius:6px;padding:6px 12px;display:inline-flex;align-items:center;box-shadow:0 2px 4px rgba(0,0,0,0.02);margin-right:5px;margin-bottom:5px;">' +
                                   '<span style="color:#495057;font-weight:500;"><i class="fa fa-file-picture-o" style="color:#17a2b8;margin-right:6px;"></i>' + (d.drawing_name || d.drawing_no || '') + '</span>' +
                                   '<span class="label label-default" style="background:#e9ecef;color:#495057;border-radius:4px;padding:2px 6px;font-size:11px;font-weight:600;margin-left:8px;">' + d.drawing_no + revStr + '</span>' +
                                   filesHtml +
                                   '</div>';
                    $drawingsRefList.append(drawHtml);
                });
                $('#drawings_ref_badge').text(drawings.length + (drawings.length === 1 ? ' Drawing' : ' Drawings'));
                $('#drawings_ref_header').css('display', 'flex');
                $drawingsRefList.css('display', 'flex');
            } else {
                $('#drawings_ref_header').hide();
                $drawingsRefList.hide();
            }

            // Show the reference container if there is any content
            if (soItems.length > 0 || drawings.length > 0) {
                $('#so_items_reference_container').slideDown(300);
            }
        },
        error: function() {
            // Silently fail – reference panel just won't appear
        }
    });
}

function descButton(id) {
    intIdVal = parseInt(id.replace(/[^0-9.]/g, ""));
    $("#ModalDescriptionId").modal();
    var ckValueDesc = $("#description" + intIdVal).val();
    var item_name = $("#item_name" + intIdVal + " option:selected").text();
    
    $("#item_name_modal").text(item_name);
    
    if (CKEDITOR.instances.descriptionmodal) {
        CKEDITOR.instances.descriptionmodal.destroy();
    }
    
    CKEDITOR.replace("descriptionmodal");
    CKEDITOR.instances.descriptionmodal.setData(ckValueDesc);
}

function saveDescription() {
    $("#description" + intIdVal).val(CKEDITOR.instances.descriptionmodal.getData());
    CKEDITOR.instances.descriptionmodal.destroy();
    $("#ModalDescriptionId").modal('hide');
}

function scopeButton(id) {
    intIdVal = parseInt(id.replace(/[^0-9.]/g, ""));
    $("#ModalScopeId").modal();
    var ckValueScope = $("#scope" + intIdVal).val();
    
    if (CKEDITOR.instances.scopemodal) {
        CKEDITOR.instances.scopemodal.destroy();
    }
    
    CKEDITOR.replace("scopemodal");
    CKEDITOR.instances.scopemodal.setData(ckValueScope);
}

function saveScope() {
    $("#scope" + intIdVal).val(CKEDITOR.instances.scopemodal.getData());
    CKEDITOR.instances.scopemodal.destroy();
    $("#ModalScopeId").modal('hide');
}



function remarkButton(id) {
    intIdVal = parseInt(id.replace(/[^0-9.]/g, ""));
    $("#ModalRemarkId").modal();
    var ckValueRemark = $("#remark" + intIdVal).val();
    
    if (CKEDITOR.instances.remarkmodal) {
        CKEDITOR.instances.remarkmodal.destroy();
    }
    
    CKEDITOR.replace("remarkmodal");
    CKEDITOR.instances.remarkmodal.setData(ckValueRemark);
}

function saveRemark() {
    $("#remark" + intIdVal).val(CKEDITOR.instances.remarkmodal.getData());
    CKEDITOR.instances.remarkmodal.destroy();
    $("#ModalRemarkId").modal('hide');
}

// Reset modal when closed
$('#ModalDescriptionId, #ModalScopeId, #ModalRemarkId').on('hidden.bs.modal', function () {
    if (CKEDITOR.instances.descriptionmodal) {
        CKEDITOR.instances.descriptionmodal.destroy();
    }
    if (CKEDITOR.instances.scopemodal) {
        CKEDITOR.instances.scopemodal.destroy();
    }
    if (CKEDITOR.instances.remarkmodal) {
        CKEDITOR.instances.remarkmodal.destroy();
    }
});


// Remove row functionality
$(document).on('click', '.btn_remove', function() {
    if ($('#table-body tr').length === 1) {
        alert('You must keep at least one row in the job order.');
        return false;
    }
    $(this).closest('tr').remove();
    updateTotalQty();
    updateHeadingAssociations();
});

function myFunction1(id) {
    if ($('#' + id).val() === 'NEW') {
        alert('Add new product functionality is not configured on this page.');
        $('#' + id).val('').trigger('change');
    }
}

function addNewRow() {
    currentRowCount++;
    var newRow = `
        <tr id="row${currentRowCount}">
            <td>
                <select style="width: 150px;" class="form-control input-sm item_search_name" name="product_name[]" id="item_name${currentRowCount}" onchange="myFunction1(this.id)" required="">
                    <option value="">Select Product</option>
                    <option value="NEW">+ Add New Product</option>
                    <?php foreach ($item_name as $row) { ?>
                        <option value="<?php echo $row->code; ?>"><?php echo $row->code . " - " . $row->item_name; ?></option>
                    <?php } ?>
                </select>
            </td>
            <td>
                <button type="button" class="btn btn-info btn-xs" onclick="descButton(this.id)" id="btnDescriptionId${currentRowCount}" title="Edit Description">Description</button>
                <textarea style="width: 150px;" class="form-control input-sm name_list description_auto hide" name="description[]" id="description${currentRowCount}" rows="4"></textarea>
            </td>
            <td>
                <input type="number" min="1" name="quantity[]" id="quantity${currentRowCount}" class="form-control input-sm" value="1" />
            </td>
            <td>
                <select class="form-control input-sm item_search_unit" name="unit[]" id="unit${currentRowCount}">
                    <option value="">Select Unit</option>
                    <?php if(isset($unit_result) && !empty($unit_result)): ?>
                        <?php foreach ($unit_result as $unit): ?>
                            <option value="<?php echo $unit->unit; ?>"><?php echo $unit->unit; ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </td>
            <td>
                <input type="text" name="tag_no[]" id="tag_no${currentRowCount}" class="form-control input-sm" />
            </td>
            <td>
                <button type="button" class="btn btn-info btn-xs" onclick="scopeButton(this.id)" id="btnScopeId${currentRowCount}" title="Edit Scope">Scope</button>
                <textarea class="form-control input-sm hide" name="scope[]" id="scope${currentRowCount}" rows="4"></textarea>
            </td>
            <td>
                <select class="form-control input-sm" name="stores_remark[]" id="stores_remark${currentRowCount}">
                    <option value="">Select</option>
                    <option value="Y">Yes</option>
                    <option value="N">No</option>
                </select>
            </td>
            <td>
                <input type="text" name="price[]" id="price${currentRowCount}" class="form-control input-sm" />
            </td>
            <td>
                <button type="button" class="btn btn-info btn-xs" onclick="remarkButton(this.id)" id="btnRemarkId${currentRowCount}" title="Edit Remark">Remark</button>
                <textarea class="form-control input-sm hide" name="remark[]" id="remark${currentRowCount}" rows="4"></textarea>
            </td>
            <td class="text-center" style="white-space: nowrap; vertical-align: middle;">
                <button type="button" class="btn btn-default btn-xs move-row-up" title="Move Up" style="padding: 1px 5px; margin-right: 2px;"><i class="fa fa-arrow-up"></i></button>
                <button type="button" class="btn btn-default btn-xs move-row-down" title="Move Down" style="padding: 1px 5px; margin-right: 2px;"><i class="fa fa-arrow-down"></i></button>
                <button type="button" name="remove_joborder_item" id="remove${currentRowCount}" class="btn btn-danger btn-xs action-header-btn btn_remove" title="Remove Row">X</button>
            </td>
        </tr>
    `;

    $('#table-body').append(newRow);

    if ($.fn.select2) {
        $('#item_name' + currentRowCount + ', #unit' + currentRowCount).select2();
    }

    updateTotalQty();
    updateHeadingAssociations();
}

// ===== HEADING ROW FUNCTIONS =====

// Add Section Heading Row
function addHeadingRow() {
    currentRowCount++;
    var headingHtml = '<tr id="row' + currentRowCount + '" class="jo-heading-row">' +
        '<td colspan="9">' +
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
        '  <i class="fa fa-tag" style="margin-right:8px;opacity:0.7;"></i>' +
        '  <input type="text" name="description[]" class="form-control input-sm heading-text-input" placeholder="ENTER SECTION HEADING (E.G. INSTRUMENTS, CIVIL WORKS)..." style="width: 80%; display: inline-block;">' +
        '</td>' +
        '<td class="text-center" style="white-space: nowrap; vertical-align: middle;">' +
        '  <button type="button" class="btn btn-success btn-xs insert-row-below" title="Insert Row Below" style="padding: 1px 5px; margin-right: 2px;"><i class="fa fa-plus"></i></button>' +
        '  <button type="button" class="btn btn-default btn-xs move-row-up" title="Move Up" style="padding: 1px 5px; margin-right: 2px;"><i class="fa fa-arrow-up"></i></button>' +
        '  <button type="button" class="btn btn-default btn-xs move-row-down" title="Move Down" style="padding: 1px 5px; margin-right: 2px;"><i class="fa fa-arrow-down"></i></button>' +
        '  <button type="button" class="btn btn-danger btn-xs btn_remove" title="Remove" style="padding: 1px 5px;">X</button>' +
        '</td>' +
        '</tr>';
    $('#table-body').append(headingHtml);

    var $newRow = $('#table-body tr:last');
    var $input = $newRow.find('.heading-text-input');

    styleHeadingRow($newRow);
    updateHeadingAssociations();
    $input.focus();
}

// Bind Add Section Heading button
$(document).on('click', '#add_heading_row', function() {
    addHeadingRow();
});

// Bind Insert Row Below button click
$(document).on('click', '.insert-row-below', function(e) {
    e.preventDefault();
    var $currentRow = $(this).closest('tr');
    insertRowBelow($currentRow);
});

// Insert standard item row after a specific row
function insertRowBelow($currentRow) {
    currentRowCount++;
    var newRow = `
        <tr id="row${currentRowCount}">  
            <td>
                <select class="form-control input-sm product_name_auto item_search_name name_list" name="product_name[]" id="item_name${currentRowCount}" onchange="myFunction1(this.id)" required="" data-live-search="true">
                    <option value="">Select Product</option>
                    <option value="NEW">+ Add New Product</option>
                    <?php foreach ($item_name as $key) { ?>
                        <option value="<?php echo $key->code; ?>"> <?php echo $key->code . " - " . $key->item_name; ?></option>
                    <?php } ?>  
                </select>
                <input type="hidden" name="product_code[]" id="product_code${currentRowCount}" class="product_code">
            </td> 
            <td>
                <button type="button" class="btn btn-info btn-xs" onclick="descButton(this.id)" id="btnDescriptionId${currentRowCount}" title="Edit Description">Description</button>
                <textarea style="width: 150px;" class="form-control input-sm name_list description_auto hide" name="description[]" id="description${currentRowCount}" rows="4"></textarea>
            </td> 
            <td>
                <input type="text" name="quantity[]" id="quantity${currentRowCount}" class="form-control input-sm" value="1" />
            </td> 
            <td>
                <select class="form-control input-sm item_search_unit" name="unit[]" id="unit${currentRowCount}">
                    <option value="">Select Unit</option>
                    <?php if(isset($unit_result) && !empty($unit_result)): ?>
                        <?php foreach ($unit_result as $unit): ?>
                            <option value="<?php echo $unit->unit; ?>"><?php echo $unit->unit; ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </td>
            <td>
                <input type="text" name="tag_no[]" id="tag_no${currentRowCount}" class="form-control input-sm" />
            </td>
            <td>
                <button type="button" class="btn btn-info btn-xs" onclick="scopeButton(this.id)" id="btnScopeId${currentRowCount}" title="Edit Scope">Scope</button>
                <textarea class="form-control input-sm hide" name="scope[]" id="scope${currentRowCount}" rows="4"></textarea>
            </td> 
            <td>
                <select class="form-control input-sm" name="stores_remark[]" id="stores_remark${currentRowCount}">
                    <option value="">Select</option>
                    <option value="Y">Yes</option>
                    <option value="N">No</option>
                </select>
            </td>
            <td>
                <input type="text" name="price[]" id="price${currentRowCount}" class="form-control input-sm" />
            </td>
            <td>
                <button type="button" class="btn btn-info btn-xs" onclick="remarkButton(this.id)" id="btnRemarkId${currentRowCount}" title="Edit Remark">Remark</button>
                <textarea class="form-control input-sm hide" name="remark[]" id="remark${currentRowCount}" rows="4"></textarea>
            </td>
            <td class="text-center" style="white-space: nowrap; vertical-align: middle;">
                <button type="button" class="btn btn-default btn-xs move-row-up" title="Move Up" style="padding: 1px 5px; margin-right: 2px;"><i class="fa fa-arrow-up"></i></button>
                <button type="button" class="btn btn-default btn-xs move-row-down" title="Move Down" style="padding: 1px 5px; margin-right: 2px;"><i class="fa fa-arrow-down"></i></button>
                <button type="button" name="delete_joborder_item" id="delete_row${currentRowCount}" class="btn btn-danger btn-xs action-header-btn" title="Delete Row" onclick="deleteRow('row${currentRowCount}')">X</button>
            </td>  
        </tr>
    `;
    $currentRow.after(newRow);
    
    if ($.fn.select2) {
        $('#item_name' + currentRowCount + ', #unit' + currentRowCount).select2();
    }
    
    updateHeadingAssociations();
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
    $('#table-body tr').each(function() {
        var $row = $(this);
        if ($row.hasClass('jo-heading-row')) {
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

// Delete specific row helper
function deleteRow(rowId) {
    $('#' + rowId).remove();
    updateTotalQty();
    updateHeadingAssociations();
}
</script>
