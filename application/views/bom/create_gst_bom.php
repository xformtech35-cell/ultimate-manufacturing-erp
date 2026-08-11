<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (isset($session_data_head1)) {
    $_has_project_master = isset($session_data_head1['permission']) && in_array('Projects', $session_data_head1['permission']);
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
    
    select.select2-hidden-accessible {
        display: none !important;
        visibility: hidden !important;
        position: absolute !important;
        width: 1px !important;
        height: 1px !important;
        opacity: 0 !important;
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
    
    /* Section Heading Row Styling */
    .bom-heading-row td {
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
    .bom-heading-row .heading-text-input {
        background: transparent;
        border: none;
        color: #5a3d8a;
        font-weight: 700;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 1px;
        width: 100%;
        outline: none;
        padding: 0;
    }
    .bom-heading-row .heading-text-input:focus {
        background: rgba(255,255,255,0.4);
        border-radius: 3px;
        padding: 2px 4px;
    }
    .btn-heading {
        background: linear-gradient(135deg, #7e57c2, #5a3d8a);
        color: white;
        border: none;
    }
    .btn-heading:hover {
        background: linear-gradient(135deg, #9575cd, #7e57c2);
        color: white;
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
                    BOM
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="#"> BOM</a></li>
                    <li class="active"> BOM Details</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">

                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-info">
                            <div class="box-header">
                                <h3 class="box-title">Create BOM</h3>
                                <div class="pull-right" style="display: flex; gap: 8px; align-items: center;">
                                    <button type="button" id="btn_import_excel" class="btn btn-success"><i class="fa fa-file-excel-o"></i> Import Excel</button>
                                    <input type="file" id="import_excel_file" style="display: none;" accept=".xlsx, .xls">
                                    <a href="javascript:void(0);" onclick="window.history.back()" class="btn btn-primary"><i class="fa fa-close"></i> Close</a>
                                </div>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">

                               

                                <?php if ($this->session->flashdata('INFOMSG')) { ?>
                                    <div role="alert" class="alert alert-info">
                                        <button data-dismiss="alert" class="close" type="button"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                                        <strong>Oops!!</strong> <?= $this->session->flashdata('INFOMSG') ?>
                                    </div>
                                <?php } ?>

                                <form class="form-horizontal form_overlay" name="add_name" id="add_name" method="post" action="<?php echo base_url(); ?>BomController/add_bom_bom" enctype="multipart/form-data" novalidate>
                                    <?php if (!$_has_project_master): ?>
                                    <!-- ★ SO Number Selector ★ -->
                                    <div class="row" id="bom_so_selector_row" style="margin-bottom: 10px;">
                                        <div class="col-md-12">
                                            <div class="form-group row" style="background: #f0f8ff; border: 1px solid #b8d9f5; border-radius: 6px; padding: 10px 15px; margin: 0;">
                                                <label class="col-sm-2 control-label" style="color: #1565C0; font-weight: 600;">
                                                    <i class="fa fa-file-text-o"></i> Select SO Number
                                                </label>
                                                <div class="col-sm-6">
                                                    <select class="form-control input-sm select2" name="" id="bom_so_number_select">
                                                        <option value="">-- Select Sales Order Number --</option>
                                                        <?php if(isset($salesorder_list) && !empty($salesorder_list)): ?>
                                                            <?php foreach($salesorder_list as $so): ?>
                                                                 <option value="<?php echo htmlspecialchars($so->so_number); ?>">
                                                                    <?php echo htmlspecialchars($so->so_number); ?>
                                                                    <?php if(!empty($so->customer_name)) echo " - " . htmlspecialchars($so->customer_name); ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        <?php endif; ?>
                                                    </select>
                                                </div>
                                                <div class="col-sm-4" style="line-height: 30px;">
                                                    <span id="bom_so_fetch_status" style="color: #666; font-size: 12px;"></span>
                                                    <span id="bom_so_loading" style="display:none; color:#1565C0;">
                                                        <i class="fa fa-spinner fa-spin"></i> Fetching SO data...
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /SO Number Selector -->
                                    <?php endif; ?>

                                    <div class="row">

                                        <div class="col-md-12">
                                            <div class="form-group row">
                                                <?php
                                                if (date('m') <= 3) {//Upto June 2014-2015
                                                    $financial_year =  (date('y') - 1) . '-' . date('y');
                                                } else {//After June 2015-2016
                                                    $financial_year =  date('y') . '-' . (date('y') + 1);
                                                }
                                                ?>
                                                <input type="hidden" name="gst_discount_check" value="gst_discount_check" id="gst_discount_check">
                                                <input type="hidden" class="form-control input-sm" name="number" id="number" required="" value="">
                                                <input type="hidden" name="bom_gst_check" value="gst" id="bom_gst_check">
                                                <input type="hidden" id="bom_financial_year" value="<?php echo $financial_year; ?>">
                                                <label class="col-sm-12 control-label" id="bom_number_label"><h2>BOM: <b id="bom_number_display" style="color: #dd4b39;"><?php echo $_has_project_master ? 'Please select Project Code' : 'Please select SO Number'; ?></b></h2></label>
                                            </div>
                                        </div>    

                                        <div class="col-md-3">
                                            <div class="form-group row">
                                                <label class="col-sm-4 control-label">Company<span style="color: red;">*</span></label>
                                                <div class="col-sm-8">
                                                    <select class="form-control input-sm company_search_name" name="customer_id" id="customer_id" required="">
                                                        <option value="">Select Company</option>
                                                        <?php foreach ($company_name as $key) { ?>
                                                            <option value="<?php echo $key->customer_id; ?>"><?php echo $key->company_name . " - " . $key->c_code; ?></option> 
                                                        <?php } ?>  
                                                    </select>
                                                    <span class="btn btn-success btn-sm" data-toggle="modal" data-target="#myModal" style="margin-top: 10%"><i class="glyphicon glyphicon-plus"></i>Add Company</span>
 
                                                </div>
                                            </div>
                                            
<?php
$_sess_perm = $this->session->userdata('session_data_head');
$_has_project_master = isset($_sess_perm['permission']) && in_array('Projects', $_sess_perm['permission']);
if ($_has_project_master): ?>
                                            <div class="form-group row">
                                               <label class="col-sm-4 control-label">Project Code<span style="color: red;">*</span></label>
                                                <div class="col-sm-8">
                                                    <select class="form-control input-sm company_search_name" name="project_code" id="project_code">
                                                        <option value="">Select project code</option>
                                                        <?php foreach ($project_code_result as $key) { ?>
                                                            <option value="<?php echo $key->project_code; ?>"><?php echo $key->project_code; ?></option> 
                                                        <?php } ?>   
                                                    </select>
                                                </div>
                                            </div>
<?php endif; // Project Master permission ?>
                                            
                                            <div class="form-group row">
                                                <label class="col-sm-4 control-label">Customer Code</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control input-sm" name="customer_code" id="customer_code">
                                                </div>
                                            </div>

                                            
                                        </div>
                                        
                                        <div class="col-md-3">
                                            <div class="form-group row">
                                                <label class="col-sm-4 control-label">Date<span style="color: red;">*</span></label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control alldate input-sm created-date" name="date" id="date" required="" readonly>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label class="col-sm-4 control-label">Status</label>
                                                <div class="col-sm-8">
                                                    <select class="form-control input-sm" name="status_main" id="status_main">
                                                        <option value="0" selected>Pending</option>
                                                        <option value="1">Draft</option>
                                                        <option value="2">Sent</option>
                                                        <option value="3">Viewed</option>
                                                        <option value="4">Approved</option> 
                                                        <option value="5">Rejected</option>
                                                        <option value="6">Canceled</option>
                                                        <option value="7">Under Review</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="project_qty" class="col-sm-4 control-label">Project Quantity</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control input-sm" name="project_qty" id="project_qty">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group row">
                                                <label for="system" class="col-sm-4 control-label">System</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control input-sm" name="system" id="system">
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="location" class="col-sm-4 control-label">Location</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control input-sm" name="location" id="location">
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="capacity" class="col-sm-4 control-label">Capacity</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control input-sm" name="capacity" id="capacity">
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="oc_number" class="col-sm-4 control-label">OC Number</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control input-sm" name="oc_number" id="oc_number">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Linked Sales Order Items Reference Card -->
                                    <div class="row" id="so_items_reference_container" style="display: none; margin-bottom: 20px;">
                                        <div class="col-md-12">
                                            <div class="so-ref-card" style="background: rgba(23, 162, 184, 0.05); border: 1px solid rgba(23, 162, 184, 0.2); border-left: 5px solid #17a2b8; border-radius: 8px; padding: 15px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05); transition: all 0.3s ease;">
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
                                            </div>
                                        </div>
                                    </div>

                                    <div class="table-responsive">  
                                        <table class="table table-bordered" id="dynamic_field">  
                                            <thead>
                                                 <tr>
                                                     <th width="12%">Item Code</th>
                                                     <th width="15%">Product Name</th>
                                                     <th width="10%">Description</th>
                                                     <th width="8%">QTY</th>
                                                     <th width="8%">Unit</th>
                                                     <th width="8%">Tag NO.</th>
                                                     <th width="10%">Scope</th>
                                                     <th width="10%">Stores Remark</th>
                                                     <th width="10%">Remark</th>
                                                     <th width="12%">Action
                                                     </th>
                                                 </tr>
                                             </thead>
                                             <tbody id="table-body">
                                                 <tr id="row1">  
                                                     <td>
                                                       <select class="form-control input-sm product_name_auto bom_item_search_name name_list " name="product_name[]" id="item_name1" onchange="myFunction1(this.id)" required="" data-live-search="true" style="width: 100%;">
                                                             <option value="">Select Product</option>
                                                             <option value="NEW">Add new item</option>
                                                            <?php foreach ($item_name as $key) { ?>
                                                                <option value="<?php echo $key->code; ?>"> <?php echo (strcasecmp(trim($key->code), trim($key->item_name)) === 0) ? $key->item_name : ($key->code . " - " . $key->item_name); ?></option>
                                                            <?php } ?>  
                                                         </select>
                                                         <input type="hidden" name="product_code[]" id="product_code1" class="product_code">
                                                         <div class="heading-path text-muted" style="font-size: 10px; margin-top: 4px; background: #f8f9fa; padding: 2px 6px; border-radius: 4px; border-left: 3px solid #7e57c2; display: inline-block;">
                                                             <i class="fa fa-folder-open-o" style="color: #7e57c2;"></i> Section: <strong class="path-text" style="color: #495057;">None</strong>
                                                         </div>
                                                     </td> 
                                                     <td>
                                                         <input type="text" name="item_name_display[]" id="item_name_display1" class="form-control input-sm item_name_display" readonly style="background-color: #eee; width: 100%;">
                                                     </td>
                                                     <td>
                                                         <button type="button" class="btn btn-info btn-xs" onclick="descButton(this.id)" id="btnDescriptionId1" title="Edit Description">Description</button>
                                                         <textarea style="width: 150px; " class="form-control input-sm name_list description_auto hide" name="description[]" id="description1" rows="4"></textarea>
                                                     </td>
                                                     <td>
                                                         <input type="number" min="1" name="quantity[]" id="quantity1" class="form-control input-sm" value="1" />
                                                     </td> 
                                                     <td>
                                                         <select class="form-control input-sm bom_item_search_unit" name="unit[]" id="unit1">
                                                             <option value="">Select Unit</option>
                                                             <?php if(isset($unit_result) && !empty($unit_result)): ?>
                                                                 <?php foreach ($unit_result as $unit): ?>
                                                                     <option value="<?php echo $unit->unit; ?>"><?php echo $unit->unit; ?></option>
                                                                 <?php endforeach; ?>
                                                             <?php endif; ?>
                                                         </select>
                                                     </td>
                                                     <td>
                                                         <input type="text" name="tag_no[]" id="tag_no1" class="form-control input-sm" />
                                                     </td>
                                                     <td>
                                                         <button type="button" class="btn btn-info btn-xs" onclick="scopeButton(this.id)" id="btnScopeId1" title="Edit Scope">Scope</button>
                                                         <textarea class="form-control input-sm hide" name="scope[]" id="scope1" rows="4"></textarea>
                                                     </td> 
                                                     <td>
                                                         <select class="form-control input-sm" name="stores_remark[]" id="stores_remark1">
                                                             <option value="">Select</option>
                                                             <option value="Y">Yes</option>
                                                             <option value="N">No</option>
                                                         </select>
                                                     </td>
                                                     <td>
                                                         <button type="button" class="btn btn-info btn-xs" onclick="remarkButton(this.id)" id="btnRemarkId1" title="Edit Remark">Remark</button>
                                                         <textarea class="form-control input-sm hide" name="remark[]" id="remark1" rows="4"></textarea>
                                                     </td> 
                                                     <td class="text-center" style="white-space: nowrap; vertical-align: middle;">
                                                            <button type="button" class="btn btn-success btn-xs insert-row-below" title="Insert Row Below" style="padding: 2px 7px;"><i class="fa fa-plus"></i></button>
                                                            <button type="button" class="btn btn-danger btn-xs btn-remove-bom-row" data-row="1" title="Remove Row" style="padding: 2px 7px;"><i class="fa fa-times"></i></button>
                                                            <div class="dropdown" style="display:inline-block;">
                                                                <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" title="More Options" style="padding: 2px 6px;"><span class="caret"></span></button>
                                                                <ul class="dropdown-menu dropdown-menu-right" style="min-width: 160px;">
                                                                    <li><a href="#" class="move-row-up"><i class="fa fa-arrow-up"></i>&nbsp; Move Up</a></li>
                                                                    <li><a href="#" class="move-row-down"><i class="fa fa-arrow-down"></i>&nbsp; Move Down</a></li>
                                                                    <li role="separator" class="divider"></li>
                                                                    <li><a href="#" class="add-heading-below"><i class="fa fa-tag"></i>&nbsp; Add Heading</a></li>
                                                                </ul>
                                                            </div>
                                                        </td>  
                                                 </tr>  
                                             </tbody>
                                         </table>

                                         <div id="bom-pagination-container" class="text-center" style="margin-top: 15px; margin-bottom: 15px; display: flex; justify-content: center; align-items: center; gap: 5px; flex-wrap: wrap;">
                                             <!-- Pagination controls will be rendered dynamically here -->
                                         </div>
                                        
                                        <br>
                                        
                                        <div class="row">
                                            <div class="col-xs-12">
                                                <div class="form-group row">
                                                    <label class="col-sm-1 control-label">Note</label>
                                                    <div class="col-sm-11">
                                                        <textarea class="form-control" name="note" id="note" rows="3" placeholder="Enter any additional notes..."></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
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

    <!-- Add Company Modal -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-success">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title text-center">Add New Company</h4>
                </div>
                <form method="post" action="<?php echo base_url(); ?>BomController/add_customer">
                    <div class="modal-body">
                        <div class="form-group row">
                            <label class="col-sm-3 control-label">Company Name<span style="color: red;">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control input-sm" name="company_name" id="company_name" required="">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 control-label">Full Name</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control input-sm" name="fullname" id="fullname">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 control-label">Email<span style="color: red;">*</span></label>
                            <div class="col-sm-9">
                                <input type="email" class="form-control input-sm" name="email" id="email" required="">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 control-label">Mobile<span style="color: red;">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control input-sm" name="mobile" id="mobile" required="">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 control-label">PAN Card</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control input-sm" name="pancard" id="pancard">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 control-label">GST No</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control input-sm" name="gst" id="gst">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 control-label">Address</label>
                            <div class="col-sm-9">
                                <textarea class="form-control input-sm" name="address" id="address" rows="3"></textarea>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 control-label">State Code</label>
                            <div class="col-sm-9">
                                <input type="number" class="form-control input-sm" name="state_code" id="state_code">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Add Company</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add New Product Modal -->
    <div class="modal fade" id="productModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title text-center">Add New Product/Item</h4>
                </div>
                <form method="post" action="<?php echo base_url(); ?>BomController/add_product">
                    <div class="modal-body">
                        <div class="form-group row">
                            <label class="col-sm-3 control-label">Item Code <span style="color: red;">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control input-sm" name="item_code" required="">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 control-label">Item Name <span style="color: red;">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control input-sm" name="item_name" required="">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 control-label">Unit</label>
                            <div class="col-sm-9">
                                <select class="form-control input-sm" name="unit">
                                    <option value="">Select Unit</option>
                                    <?php if(isset($unit_result) && !empty($unit_result)): ?>
                                        <?php foreach ($unit_result as $unit): ?>
                                            <option value="<?php echo $unit->unit; ?>"><?php echo $unit->unit; ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 control-label">HSN Code</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control input-sm" name="hsn">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 control-label">GST %</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control input-sm" name="gst_per">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 control-label">Description</label>
                            <div class="col-sm-9">
                                <textarea class="form-control input-sm" name="description" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Add Product</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</body>
</html>

<!-- jQuery UI (required for datepicker - like sales order footer) -->
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<script>
var intIdVal = "";
var item_id_new = "";

// Handle multiple open modals layering and scrollbar focus correctly
$(document).on('show.bs.modal', '.modal', function () {
    var zIndex = 1040 + (10 * $('.modal:visible').length);
    $(this).css('z-index', zIndex);
    setTimeout(function() {
        $('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack');
    }, 0);
});

$(document).on('hidden.bs.modal', '.modal', function () {
    if ($('.modal:visible').length > 0) {
        setTimeout(function() {
            $('body').addClass('modal-open');
        }, 0);
    }
});

function descButton(id) {
    intIdVal = parseInt(id.replace(/[^0-9.]/g, ""));
    $("#ModalDescriptionId").modal();
    var ckValueDesc = $("#description" + intIdVal).val();
    var item_name = ($("#item_name" + intIdVal).length ? $("#item_name" + intIdVal) : $("#product_name" + intIdVal)).find("option:selected").text();
    $("#item_name_modal").text(item_name);
    
    if (CKEDITOR.instances.descriptionmodal) {
        CKEDITOR.instances.descriptionmodal.destroy();
    }
    
    CKEDITOR.replace("descriptionmodal");
    CKEDITOR.instances.descriptionmodal.setData(ckValueDesc);
}

function saveDescription() {
    var val = CKEDITOR.instances.descriptionmodal.getData();
    $("#description" + intIdVal).val(val);
    CKEDITOR.instances.descriptionmodal.destroy();
    $("#ModalDescriptionId").modal('hide');
    if (val && val.replace(/<[^>]*>/g, '').trim() !== '') {
        $("#btnDescriptionId" + intIdVal).removeClass('btn-info').addClass('btn-success').text('Description (Set)');
    } else {
        $("#btnDescriptionId" + intIdVal).removeClass('btn-success').addClass('btn-info').text('Description');
    }
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
    var val = CKEDITOR.instances.scopemodal.getData();
    $("#scope" + intIdVal).val(val);
    CKEDITOR.instances.scopemodal.destroy();
    $("#ModalScopeId").modal('hide');
    if (val && val.replace(/<[^>]*>/g, '').trim() !== '') {
        $("#btnScopeId" + intIdVal).removeClass('btn-info').addClass('btn-success').text('Scope (Set)');
    } else {
        $("#btnScopeId" + intIdVal).removeClass('btn-success').addClass('btn-info').text('Scope');
    }
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
    var val = CKEDITOR.instances.remarkmodal.getData();
    $("#remark" + intIdVal).val(val);
    CKEDITOR.instances.remarkmodal.destroy();
    $("#ModalRemarkId").modal('hide');
    if (val && val.replace(/<[^>]*>/g, '').trim() !== '') {
        $("#btnRemarkId" + intIdVal).removeClass('btn-info').addClass('btn-success').text('Remark (Set)');
    } else {
        $("#btnRemarkId" + intIdVal).removeClass('btn-success').addClass('btn-info').text('Remark');
    }
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

// Initialize date picker
$(document).ready(function() {
    var today = new Date();
    var dateString = today.getDate() + '-' + String(today.getMonth() + 1).padStart(2, '0') + '-' + today.getFullYear();
    $('#date').val(dateString);
    
    // jQuery UI Datepicker initialization for alldate class
    $('.alldate').datepicker({
        dateFormat: 'dd-mm-yy',
        changeMonth: true,
        changeYear: true,
        maxDate: 0
    });

    // Save all initial project code options (only if Project Master field is visible)
    if ($('#project_code').length > 0) {
        window.allProjectOptions = $('#project_code').html();
        // Auto-trigger if Project Code is already loaded
        if ($('#project_code').val()) {
            $('#project_code').trigger('change');
        }
    }

    // Auto-dismiss alerts
    setTimeout(function() {
        $('.alert').fadeOut(500, function() {
            $(this).remove();
        });
    }, 2000);

    // Manual alert close
    $('.alert').on('click', '.close', function() {
        $(this).closest('.alert').fadeOut(500);
    });
});

// Handle Add New Product form submission
$(document).on('submit', '#productModal form', function(e) {
    e.preventDefault();
    var form = $(this);
    var submitBtn = form.find('button[type="submit"]');
    submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');
    
    $.ajax({
        url: form.attr('action'),
        method: 'POST',
        data: form.serialize(),
        dataType: 'json',
        success: function(response) {
            if (response && response.success) {
                // Get the input values from the modal form
                var itemCode = form.find('input[name="item_code"]').val();
                var itemName = form.find('input[name="item_name"]').val();
                
                // Append the new option to all select dropdowns that have "NEW" option on the page
                $('select').each(function() {
                    var $select = $(this);
                    if ($select.find('option[value="NEW"]').length > 0) {
                        // Check if option already exists to prevent duplicates
                        if ($select.find('option[value="' + itemCode + '"]').length === 0) {
                            var newOption = $("<option>")
                              .text(itemCode + " - " + itemName)
                              .attr("value", itemCode);
                            $select.append(newOption);
                        }
                    }
                });

                // Update productOptionsHtml and loadedProductCodes set so that future rows get it
                if (!loadedProductCodes[itemCode]) {
                    loadedProductCodes[itemCode] = true;
                    var displayVal = (itemCode.toUpperCase().trim() === itemName.toUpperCase().trim()) ? itemName : (itemCode + ' - ' + itemName);
                    productOptionsHtml += '<option value="' + itemCode + '">' + displayVal + '</option>';
                }

                // Check if we need to select it for the active row dropdown
                var activeDropdownId = (typeof window.item_id_new !== "undefined" && window.item_id_new) ? window.item_id_new : (typeof item_id_new !== "undefined" ? item_id_new : "");
                if (activeDropdownId) {
                    var $dropdown = $("#" + activeDropdownId);
                    $dropdown.val(itemCode).trigger("change");
                    window.item_id_new = "";
                    if (typeof item_id_new !== "undefined") {
                        item_id_new = "";
                    }
                }

                // Check if this was opened from the Excel Resolver modal
                if (typeof window.activeResolveRowIdx !== "undefined" && window.activeResolveRowIdx !== null) {
                    var idx = window.activeResolveRowIdx;
                    var $row = $('#resolve_items_table tbody tr[data-index="' + idx + '"]');
                    
                    // Update the Excel item details
                    currentExcelItems[idx].product_name = itemCode;
                    currentExcelItems[idx].item_code = itemCode;
                    currentExcelItems[idx].raw_name = itemName;
                    
                    // Append and select option in resolver's select
                    var $dbSelect = $row.find('.db-match-select');
                    var escapedCode = $('<div>').text(itemCode).html();
                    var escapedName = $('<div>').text(itemName).html();
                    
                    var newOptHtml = '<option value="' + escapedCode + '" selected>' + escapedCode + ' - ' + escapedName + '</option>';
                    $dbSelect.append(newOptHtml);
                    
                    // Update action options
                    $row.find('.resolve-match-option').show();
                    $row.find('input[value="USE_DB"]').prop('checked', true);
                    
                    // Trigger select change to update layout comparison
                    $dbSelect.trigger('change');
                    
                    window.activeResolveRowIdx = null;
                }
                
                // Close modal
                $("#productModal").modal("hide");
                
                // Reset form
                form[0].reset();
            } else {
                alert('Failed to save product: ' + (response.error || response.message || 'Unknown error'));
            }
        },
        error: function(xhr) {
            alert('Error saving product. Please try again.');
            console.error('Product save error:', xhr.responseText);
        },
        complete: function() {
            submitBtn.prop('disabled', false).html('<i class="fa fa-save"></i> Add Product');
        }
    });
});

// Pre-rendered options from PHP for Excel import
var rawProducts = <?php echo json_encode(isset($item_name) ? $item_name : []); ?>;
var loadedProductCodes = {};
var productOptionsHtml = '<option value="">Select Product</option><option value="NEW">Add new item</option>';
if (Array.isArray(rawProducts)) {
    for (var idx = 0; idx < rawProducts.length; idx++) {
        var p = rawProducts[idx];
        loadedProductCodes[p.code] = true;
        var escapedCode = $('<div>').text(p.code).html();
        var escapedName = $('<div>').text(p.item_name).html();
        var displayVal = (escapedCode.toUpperCase().trim() === escapedName.toUpperCase().trim()) ? escapedName : (escapedCode + ' - ' + escapedName);
        productOptionsHtml += '<option value="' + escapedCode + '">' + displayVal + '</option>';
    }
}

var rawUnits = <?php echo json_encode(isset($unit_result) ? $unit_result : []); ?>;
var unitOptionsHtml = '<option value="">Select Unit</option>';
if (Array.isArray(rawUnits)) {
    for (var idx = 0; idx < rawUnits.length; idx++) {
        var u = rawUnits[idx];
        var escapedUnit = $('<div>').text(u.unit).html();
        unitOptionsHtml += '<option value="' + escapedUnit + '">' + escapedUnit + '</option>';
    }
}

function resetSelect2Instance($select) {
    if ($select.hasClass('select2-hidden-accessible') && $select.data('select2')) {
        $select.select2('destroy');
    }
    $select.siblings('.select2').remove();
}

function getNextBomRowIndex() {
    var maxIndex = 0;
    $('#table-body tr[id^="row"]').each(function() {
        var match = (this.id || '').match(/^row(\d+)$/);
        if (match) {
            var rowIndex = parseInt(match[1], 10);
            if (!isNaN(rowIndex) && rowIndex > maxIndex) {
                maxIndex = rowIndex;
            }
        }
    });
    return maxIndex + 1;
}

// Import Excel Event Handlers  
$(document).on('click', '#btn_import_excel', function() {
    $('#import_excel_file').trigger('click');
});

$(document).on('change', '#import_excel_file', function(e) {
    var file = e.target.files[0];
    if (!file) return;
    
    // Validate file type
    var ext = file.name.split('.').pop().toLowerCase();
    if (ext !== 'xlsx' && ext !== 'xls') {
        alert('Please select a valid Excel file (.xlsx or .xls)');
        $(this).val('');
        return;
    }
    
    var formData = new FormData();
    formData.append('file', file);
    
    var $btn = $('#btn_import_excel');
    var originalHtml = $btn.html();
    $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Importing...');
    
    $.ajax({
        url: base_url + 'BomController/ajax_import_bom_excel',
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        dataType: 'json',
        success: function(response) {
            if (response && response.success) {
                currentExcelItems = response.items;
                var unresolvedCount = 0;
                var autoMatchedCount = 0;
                
                for (var k = 0; k < currentExcelItems.length; k++) {
                    // Skip heading/subheading rows — they don't need product matching
                    if (currentExcelItems[k].type === 'subheading' || currentExcelItems[k].product_name === '__HEADING__') {
                        continue;
                    }
                    var isDetailsMatch = (currentExcelItems[k].details_match === true || currentExcelItems[k].details_match === 'true' || currentExcelItems[k].details_match === 1 || currentExcelItems[k].details_match === '1');
                    if (isDetailsMatch) {
                        autoMatchedCount++;
                    } else {
                        unresolvedCount++;
                    }
                }
                
                if (unresolvedCount === 0) {
                    if (confirm('Importing will clear the current items table and load ' + currentExcelItems.length + ' items from Excel. Do you want to proceed?')) {
                        var autoMappings = currentExcelItems.map(function(item, idx) {
                            var isHeading = (item.type === 'subheading' || item.product_name === '__HEADING__');
                            return {
                                excel_index: idx,
                                action: isHeading ? 'SUBHEADING' : 'USE_DB',
                                db_code: isHeading ? '__HEADING__' : item.product_name,
                                excel_item: item
                            };
                        });
                        submitResolveMappings(autoMappings);
                    }
                } else {
                    showExcelResolveModal(unresolvedCount, autoMatchedCount);
                }
            } else {
                alert('Import failed: ' + (response.message || 'Unknown error'));
            }
        },
        error: function(xhr) {
            var errMsg = 'An error occurred during import. Please try again.';
            if (xhr.status) {
                errMsg += '\nHTTP Status: ' + xhr.status + ' (' + xhr.statusText + ')';
            }
            if (xhr.responseText) {
                try {
                    var errObj = JSON.parse(xhr.responseText);
                    if (errObj && errObj.message) {
                        errMsg += '\nMessage: ' + errObj.message;
                    }
                } catch(e) {
                    var cleanText = xhr.responseText.replace(/<[^>]*>/g, '').replace(/\s+/g, ' ').trim();
                    if (cleanText) {
                        errMsg += '\nDetails: ' + cleanText.substring(0, 300);
                    }
                }
            }
            alert(errMsg);
            console.error('BOM Import Error:', xhr.responseText);
        },
        complete: function() {
            $btn.prop('disabled', false).html(originalHtml);
            $('#import_excel_file').val(''); // Reset file input
        }
    });
});

function importBOMRows(items) {
    console.log('importBOMRows rendering items:', items);
    window.isExcelImporting = true;
    $('#table-body').empty();
    
    // Reset or safely initialize the global row counter i
    if (typeof i !== 'undefined') {
        i = 0;
    } else {
        window.i = 0;
    }
    
    for (var k = 0; k < items.length; k++) {
        var item = items[k];
        i++; // Increment global counter from custom.js
        
        // ---- Sub-heading / Section Row ----
        if (item.type === 'subheading' || item.product_name === '__HEADING__') {
            var headingText = item.heading_text || item.description || item.raw_name || '';
            var isSub = false;
            if (item.tag_no === 'SUB' || item.type === 'subheading') {
                isSub = true;
            } else if (item.tag_no === 'MAIN') {
                isSub = false;
            } else {
                if (/PIPING|FITTINGS|VALVES|FLANGE|ELBOW|TEE|PIPE|CPVC|UPVC/i.test(headingText)) {
                    isSub = true;
                }
            }
            var headingHtml = '<tr id="row' + i + '" class="bom-heading-row">' +
                '<td colspan="9">' +
                '  <input type="hidden" name="product_name[]" value="__HEADING__">' +
                '  <input type="hidden" name="quantity[]" value="0">' +
                '  <input type="hidden" name="unit[]" value="">' +
                '  <select name="tag_no[]" class="heading-type-select form-control input-sm" style="width: auto; display: inline-block; margin-right: 8px;">' +
                '    <option value="MAIN"' + (!isSub ? ' selected' : '') + '>Main Heading</option>' +
                '    <option value="SUB"' + (isSub ? ' selected' : '') + '>Sub Heading</option>' +
                '  </select>' +
                '  <input type="hidden" name="scope[]" value="">' +
                '  <input type="hidden" name="stores_remark[]" value="">' +
                '  <input type="hidden" name="remark[]" value="">' +
                '  <input type="hidden" name="product_code[]" value="__HEADING__">' +
                '  <i class="fa fa-tag" style="margin-right:8px;opacity:0.7;"></i>' +
                '  <input type="text" name="description[]" class="heading-text-input" value="' + $('<div>').text(headingText).html() + '" placeholder="Section Heading...">' +
                '</td>' +
                '<td class="text-center" style="white-space: nowrap; vertical-align: middle;">' +
        '  <button type="button" class="btn btn-success btn-xs insert-row-below" title="Insert Row Below" style="padding: 2px 7px;"><i class="fa fa-plus"></i></button>' +
'  <button type="button" class="btn btn-danger btn-xs btn-remove-bom-row" title="Remove" style="padding: 2px 7px;"><i class="fa fa-times"></i></button>' +
        '  <div class="dropdown" style="display:inline-block;">' +
        '    <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" title="More Options" style="padding: 2px 6px;"><span class="caret"></span></button>' +
        '    <ul class="dropdown-menu dropdown-menu-right" style="min-width: 160px;">' +
        '      <li><a href="#" class="move-row-up"><i class="fa fa-arrow-up"></i>&nbsp; Move Up</a></li>' +
        '      <li><a href="#" class="move-row-down"><i class="fa fa-arrow-down"></i>&nbsp; Move Down</a></li>' +
        '      <li role="separator" class="divider"></li>' +
        '      <li><a href="#" class="add-heading-below"><i class="fa fa-tag"></i>&nbsp; Add Heading</a></li>' +
        '    </ul>' +
        '  </div>' +
        '</td>' +
        '</tr>';
            $('#table-body').append(headingHtml);
            continue;
        }
        // ---- End heading row ----
        
        var rowHtml = '<tr id="row' + i + '">' +
            '<td>' +
            '  <select class="form-control input-sm product_name_auto bom_item_search_name name_list" name="product_name[]" id="item_name' + i + '" onchange="myFunction1(this.id)" required="" data-live-search="true" style="width: 100%;">' +
                 productOptionsHtml +
            '  </select>' +
            '  <input type="hidden" name="product_code[]" id="product_code' + i + '" class="product_code">' +
            '  <div class="heading-path text-muted" style="font-size: 10px; margin-top: 4px; background: #f8f9fa; padding: 2px 6px; border-radius: 4px; border-left: 3px solid #7e57c2; display: inline-block;">' +
            '      <i class="fa fa-folder-open-o" style="color: #7e57c2;"></i> Section: <strong class="path-text" style="color: #495057;">None</strong>' +
            '  </div>' +
            '</td>' +
            '<td>' +
            '  <input type="text" name="item_name_display[]" id="item_name_display' + i + '" class="form-control input-sm item_name_display" readonly style="background-color: #eee; width: 100%;">' +
            '</td>' +
            '<td>' +
            '  <button type="button" class="btn btn-info btn-xs" onclick="descButton(this.id)" id="btnDescriptionId' + i + '" title="Edit Description">Description</button>' +
            '  <textarea style="width: 150px;" class="form-control input-sm name_list description_auto hide" name="description[]" id="description' + i + '" rows="4"></textarea>' +
            '</td>' +
            '<td>' +
            '  <input type="number" min="1" name="quantity[]" id="quantity' + i + '" class="form-control input-sm" value="1" />' +
            '</td>' +
            '<td>' +
            '  <select class="form-control input-sm bom_item_search_unit" name="unit[]" id="unit' + i + '">' +
                 unitOptionsHtml +
            '  </select>' +
            '</td>' +
            '<td>' +
            '  <input type="text" name="tag_no[]" id="tag_no' + i + '" class="form-control input-sm" />' +
            '</td>' +
            '<td>' +
            '  <button type="button" class="btn btn-info btn-xs" onclick="scopeButton(this.id)" id="btnScopeId' + i + '" title="Edit Scope">Scope</button>' +
            '  <textarea class="form-control input-sm hide" name="scope[]" id="scope' + i + '" rows="4"></textarea>' +
            '</td>' +
            '<td>' +
            '  <select class="form-control input-sm" name="stores_remark[]" id="stores_remark' + i + '">' +
            '    <option value="">Select</option>' +
            '    <option value="Y">Yes</option>' +
            '    <option value="N">No</option>' +
            '  </select>' +
            '</td>' +
            '<td>' +
            '  <button type="button" class="btn btn-info btn-xs" onclick="remarkButton(this.id)" id="btnRemarkId' + i + '" title="Edit Remark">Remark</button>' +
            '  <textarea class="form-control input-sm hide" name="remark[]" id="remark' + i + '" rows="4"></textarea>' +
            '</td>' +
            '<td class="text-center" style="white-space: nowrap; vertical-align: middle;">' +
        '  <button type="button" class="btn btn-success btn-xs insert-row-below" title="Insert Row Below" style="padding: 2px 7px;"><i class="fa fa-plus"></i></button>' +
'  <button type="button" class="btn btn-danger btn-xs btn-remove-bom-row" id="remove' + i + '" title="Remove Row" style="padding: 2px 7px;"><i class="fa fa-times"></i></button>' +
        '  <div class="dropdown" style="display:inline-block;">' +
        '    <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" title="More Options" style="padding: 2px 6px;"><span class="caret"></span></button>' +
        '    <ul class="dropdown-menu dropdown-menu-right" style="min-width: 160px;">' +
        '      <li><a href="#" class="move-row-up"><i class="fa fa-arrow-up"></i>&nbsp; Move Up</a></li>' +
        '      <li><a href="#" class="move-row-down"><i class="fa fa-arrow-down"></i>&nbsp; Move Down</a></li>' +
        '      <li role="separator" class="divider"></li>' +
        '      <li><a href="#" class="add-heading-below"><i class="fa fa-tag"></i>&nbsp; Add Heading</a></li>' +
        '    </ul>' +
        '  </div>' +
        '</td>' +
        '</tr>';
            
        $('#table-body').append(rowHtml);
        
        // Populate the values
        var $row = $('#row' + i);
        
        // Product Selection (Strict Matching by Item Code / Product Name Code)
        var productMatchedVal = '';
        if (item.product_name && item.product_name !== 'NEW') {
            var prodCodeUpper = item.product_name.toUpperCase().trim();
            
            // 1. First pass: Exact case-sensitive match on option value (item code)
            $row.find('#item_name' + i + ' option').each(function() {
                var optVal = $(this).val();
                if (optVal === item.product_name) {
                    productMatchedVal = optVal;
                    return false;
                }
            });
            
            // 2. Second pass: Case-insensitive match on option value
            if (!productMatchedVal) {
                $row.find('#item_name' + i + ' option').each(function() {
                    var optVal = $(this).val().toUpperCase().trim();
                    if (optVal === prodCodeUpper) {
                        productMatchedVal = $(this).val();
                        return false;
                    }
                });
            }
            
            // 3. Third pass: Case-insensitive match on option code prefix (before " - ")
            if (!productMatchedVal) {
                $row.find('#item_name' + i + ' option').each(function() {
                    var optText = $(this).text();
                    var parts = optText.split(' - ');
                    if (parts.length > 0) {
                        var codePart = parts[0].toUpperCase().trim();
                        if (codePart === prodCodeUpper) {
                            productMatchedVal = $(this).val();
                            return false;
                        }
                    }
                });
            }
        }
        
        var targetVal = productMatchedVal || item.product_name;
        if (targetVal) {
            var optionExists = $row.find('#item_name' + i + ' option[value="' + targetVal + '"]').length > 0;
            if (!optionExists) {
                var displayLabel = targetVal;
                if (item.raw_name && item.raw_name !== targetVal) {
                    displayLabel = targetVal + ' - ' + item.raw_name;
                }
                var newOption = new Option(displayLabel, targetVal, true, true);
                $row.find('#item_name' + i).append(newOption);
            }
            $row.find('#item_name' + i).val(targetVal);
            $row.find('#product_code' + i).val(targetVal);
        } else {
            $row.find('#item_name' + i).val('');
            $row.find('#product_code' + i).val('');
        }
        
        $row.find('.item_name_display').val(item.raw_name || '');
        
        // Description
        var descVal = item.description || item.raw_name;
        $row.find('#description' + i).val(descVal);
        if (descVal && descVal.trim() !== '') {
            $row.find('#btnDescriptionId' + i).removeClass('btn-info').addClass('btn-success').text('Description (Set)');
        }
        
        // Quantity
        $row.find('#quantity' + i).val(item.quantity || 1);
        
        // Unit (Case-insensitive matching)
        if (item.unit) {
            var unitValUpper = item.unit.toUpperCase().trim();
            var matchedVal = '';
            $row.find('#unit' + i + ' option').each(function() {
                var optVal = $(this).val().toUpperCase().trim();
                if (optVal === unitValUpper) {
                    matchedVal = $(this).val();
                    return false;
                }
            });
            if (matchedVal) {
                $row.find('#unit' + i).val(matchedVal);
            }
        }
        
        // Tag No
        $row.find('#tag_no' + i).val(item.tag_no);
        
        // Scope
        $row.find('#scope' + i).val(item.scope);
        if (item.scope && item.scope.trim() !== '') {
            $row.find('#btnScopeId' + i).removeClass('btn-info').addClass('btn-success').text('Scope (Set)');
        }
        
        // Stores Remark
        if (item.stores_remark) {
            var storeVal = item.stores_remark.toUpperCase();
            if (storeVal === 'Y' || storeVal === 'YES') {
                $row.find('#stores_remark' + i).val('Y');
            } else if (storeVal === 'N' || storeVal === 'NO') {
                $row.find('#stores_remark' + i).val('N');
            }
        }
        
        // Remark
        $row.find('#remark' + i).val(item.remark);
        if (item.remark && item.remark.trim() !== '') {
            $row.find('#btnRemarkId' + i).removeClass('btn-info').addClass('btn-success').text('Remark (Set)');
        }
        
        // Initialize Select2 on product and unit
        $row.find('.bom_item_search_name').select2({
            placeholder: "Select Item",
            allowClear: true,
            width: '100%',
            templateSelection: function(state) {
                if (!state.id || state.id === 'NEW') {
                    return state.text;
                }
                return state.id;
            }
        });
        
        $row.find('.bom_item_search_unit').select2({
            placeholder: "Select Unit",
            allowClear: true
        });
        
        // Trigger change to update Select2 visuals
        $row.find('#item_name' + i).trigger('change');
        $row.find('#unit' + i).trigger('change');
    }
    
    window.isExcelImporting = false;
    updateHeadingAssociations();
    currentPage = 1;
    updatePagination();
}

// Global backup remove row handler
$(document).off('click', '.btn-remove-bom-row').on('click', '.btn-remove-bom-row', function() {
    $(this).closest('tr').remove();
    updateHeadingAssociations();
    updatePagination();
});

function buildBomHeadingRowHtml(i) {
    return '<tr id="row' + i + '" class="bom-heading-row">' +
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
        '  <input type="hidden" name="product_code[]" value="__HEADING__">' +
        '  <i class="fa fa-tag" style="margin-right:8px;opacity:0.7;"></i>' +
        '  <input type="text" name="description[]" class="heading-text-input" placeholder="Enter section heading (e.g. Instruments, Civil Works)...">' +
        '</td>' +
        '<td class="text-center" style="white-space: nowrap; vertical-align: middle;">' +
        '  <button type="button" class="btn btn-success btn-xs insert-row-below" title="Insert Row Below" style="padding: 2px 7px;"><i class="fa fa-plus"></i></button>' +
'  <button type="button" class="btn btn-danger btn-xs btn-remove-bom-row" title="Remove" style="padding: 2px 7px;"><i class="fa fa-times"></i></button>' +
        '  <div class="dropdown" style="display:inline-block;">' +
        '    <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" title="More Options" style="padding: 2px 6px;"><span class="caret"></span></button>' +
        '    <ul class="dropdown-menu dropdown-menu-right" style="min-width: 160px;">' +
        '      <li><a href="#" class="move-row-up"><i class="fa fa-arrow-up"></i>&nbsp; Move Up</a></li>' +
        '      <li><a href="#" class="move-row-down"><i class="fa fa-arrow-down"></i>&nbsp; Move Down</a></li>' +
        '      <li role="separator" class="divider"></li>' +
        '      <li><a href="#" class="add-heading-below"><i class="fa fa-tag"></i>&nbsp; Add Heading</a></li>' +
        '    </ul>' +
        '  </div>' +
        '</td>' +
        '</tr>';
}

function finalizeBomHeadingRow($newRow) {
    var $input = $newRow.find('.heading-text-input');
    updateHeadingAssociations();
    var newRowIndex = $('#table-body tr').index($newRow);
    currentPage = Math.floor(newRowIndex / pageSize) + 1;
    updatePagination();
    $input.focus();
}

function addHeadingRow() {
    var i = getNextBomRowIndex();
    $('#table-body').append(buildBomHeadingRowHtml(i));
    finalizeBomHeadingRow($('#row' + i));
}

function insertHeadingRowAfter($currentRow) {
    var i = getNextBomRowIndex();
    $currentRow.after(buildBomHeadingRowHtml(i));
    finalizeBomHeadingRow($('#row' + i));
}

// Bind Add Section Heading button
$(document).off('click', '#add_heading_row').on('click', '#add_heading_row', function() {
    addHeadingRow();
});

// Bind Add Heading below current row
$(document).off('click', '.add-heading-below').on('click', '.add-heading-below', function(e) {
    e.preventDefault();
    var $row = $(this).closest('tr');
    if (!$row.length || $row.is('body')) {
        var $menu = $(this).closest('.dropdown-menu');
        var $parentDropdown = $menu.data('table-dd-parent');
        if ($parentDropdown && $parentDropdown.length) {
            $row = $parentDropdown.closest('tr');
        }
    }
    insertHeadingRowAfter($row);
});

// Style heading row based on text content (lavender, peach, green, light blue)
function styleHeadingRow($tr) {
    var val = $tr.find('.heading-text-input').val() || '';
    var trimmedVal = val.trim();
    
    // Check dropdown value first
    var headingType = $tr.find('.heading-type-select').val();
    
    if (headingType === 'MAIN') {
        $tr.css({
            'background': 'linear-gradient(135deg, #e8e0f0 0%, #d4c8e8 100%)',
            'color': '#5a3d8a',
            'font-family': 'Calibri, sans-serif'
        });
        $tr.find('td').css({
            'background': 'linear-gradient(135deg, #e8e0f0 0%, #d4c8e8 100%)',
            'color': '#5a3d8a'
        });
        $tr.find('.heading-text-input').css('color', '#5a3d8a');
    } else if (headingType === 'SUB') {
        $tr.css({
            'background': '#fdeada',
            'color': '#000000',
            'font-family': 'Calibri, sans-serif'
        });
        $tr.find('td').css({
            'background': '#fdeada',
            'color': '#000000'
        });
        $tr.find('.heading-text-input').css('color', '#000000');
    } else {
        if (trimmedVal === '') {
            $tr.css({
                'background': 'linear-gradient(135deg, #e8e0f0 0%, #d4c8e8 100%)',
                'color': '#5a3d8a'
            });
            $tr.find('.heading-text-input').css('color', '#5a3d8a');
            return;
        }
        
        if (/EQUIPMENTS/i.test(trimmedVal)) {
            $tr.css({
                'background': '#c3d69b',
                'color': '#000000',
                'font-family': 'Calibri, sans-serif'
            });
            $tr.find('.heading-text-input').css({
                'color': '#000000',
                'font-family': 'Calibri, sans-serif'
            });
        } else if (/SYSTEM|SPARES|COMMISSIONING|TANK FOR/i.test(trimmedVal)) {
            $tr.css({
                'background': '#e6e0ed',
                'color': '#ff0000',
                'font-family': 'Cambria, serif'
            });
            $tr.find('.heading-text-input').css({
                'color': '#ff0000',
                'font-family': 'Cambria, serif'
            });
        } else if (/PIPING|FITTINGS|VALVES|FLANGE|ELBOW|TEE|PIPE|CPVC|UPVC/i.test(trimmedVal)) {
            $tr.css({
                'background': '#fdeada',
                'color': '#000000',
                'font-family': 'Calibri, sans-serif'
            });
            $tr.find('.heading-text-input').css({
                'color': '#000000',
                'font-family': 'Calibri, sans-serif'
            });
        } else {
            $tr.css({
                'background': '#dbeff4',
                'color': '#000000',
                'font-family': 'Calibri, sans-serif'
            });
            $tr.find('.heading-text-input').css({
                'color': '#000000',
                'font-family': 'Calibri, sans-serif'
            });
        }
    }
}

// Update section path labels and styles dynamically
function updateHeadingAssociations() {
    var currentHeading = 'None';
    $('#table-body tr').each(function() {
        var $row = $(this);
        if ($row.hasClass('bom-heading-row')) {
            var headingText = $row.find('.heading-text-input').val() || '';
            currentHeading = headingText.trim() || 'Heading (Empty)';
            styleHeadingRow($row);
        } else {
            $row.find('.path-text').text(currentHeading);
        }
    });
}

// Listen for dynamic heading input to update styles immediately
$(document).on('input', '.heading-text-input', function() {
    styleHeadingRow($(this).closest('tr'));
    updateHeadingAssociations();
});

// Row reordering: Move Up
$(document).off('click', '.move-row-up').on('click', '.move-row-up', function(e) {
    e.preventDefault();
    var $row = $(this).closest('tr');
    if (!$row.length || $row.is('body')) {
        var $menu = $(this).closest('.dropdown-menu');
        var $parentDropdown = $menu.data('table-dd-parent');
        if ($parentDropdown && $parentDropdown.length) {
            $row = $parentDropdown.closest('tr');
        }
    }
    var $prev = $row.prev();
    if ($prev.length) {
        $row.insertBefore($prev);
        updateHeadingAssociations();
        updatePagination();
    }
});

// Row reordering: Move Down
$(document).off('click', '.move-row-down').on('click', '.move-row-down', function(e) {
    e.preventDefault();
    var $row = $(this).closest('tr');
    if (!$row.length || $row.is('body')) {
        var $menu = $(this).closest('.dropdown-menu');
        var $parentDropdown = $menu.data('table-dd-parent');
        if ($parentDropdown && $parentDropdown.length) {
            $row = $parentDropdown.closest('tr');
        }
    }
    var $next = $row.next();
    if ($next.length) {
        $row.insertAfter($next);
        updateHeadingAssociations();
        updatePagination();
    }
});

// Client-side pagination variables and control
var currentPage = 1;
var pageSize = 10;

function updatePagination() {
    var $rows = $('#table-body tr');
    var totalRows = $rows.length;
    var totalPages = Math.ceil(totalRows / pageSize) || 1;
    
    if (currentPage > totalPages) {
        currentPage = totalPages;
    }
    if (currentPage < 1) {
        currentPage = 1;
    }
    
    $rows.hide();
    
    var startIndex = (currentPage - 1) * pageSize;
    var endIndex = startIndex + pageSize;
    
    $rows.slice(startIndex, endIndex).show();
    
    // Re-render pagination buttons
    var paginationHtml = '';
    
    paginationHtml += '<button type="button" class="btn btn-default btn-sm pag-prev" ' + (currentPage === 1 ? 'disabled' : '') + ' style="border-radius: 4px;"><i class="fa fa-chevron-left"></i> Prev</button>';
    
    var startPage = Math.max(1, currentPage - 2);
    var endPage = Math.min(totalPages, currentPage + 2);
    
    if (startPage > 1) {
        paginationHtml += '<button type="button" class="btn btn-default btn-sm pag-page" data-page="1" style="border-radius: 4px;">1</button>';
        if (startPage > 2) {
            paginationHtml += '<span style="padding: 5px 8px; color: #777;">...</span>';
        }
    }
    
    for (var p = startPage; p <= endPage; p++) {
        paginationHtml += '<button type="button" class="btn ' + (p === currentPage ? 'btn-primary' : 'btn-default') + ' btn-sm pag-page" data-page="' + p + '" style="border-radius: 4px;">' + p + '</button>';
    }
    
    if (endPage < totalPages) {
        if (endPage < totalPages - 1) {
            paginationHtml += '<span style="padding: 5px 8px; color: #777;">...</span>';
        }
        paginationHtml += '<button type="button" class="btn btn-default btn-sm pag-page" data-page="' + totalPages + '" style="border-radius: 4px;">' + totalPages + '</button>';
    }
    
    paginationHtml += '<button type="button" class="btn btn-default btn-sm pag-next" ' + (currentPage === totalPages ? 'disabled' : '') + ' style="border-radius: 4px;">Next <i class="fa fa-chevron-right"></i></button>';
    
    var showingStart = totalRows > 0 ? startIndex + 1 : 0;
    var showingEnd = Math.min(endIndex, totalRows);
    paginationHtml += '<span style="margin-left: 15px; color: #666; font-size: 13px; display: inline-block; vertical-align: middle; margin-top: 5px;">Showing ' + showingStart + ' to ' + showingEnd + ' of ' + totalRows + ' entries</span>';
    
    $('#bom-pagination-container').html(paginationHtml);
}

$(document).on('click', '.pag-prev', function(e) {
    e.preventDefault();
    if (currentPage > 1) {
        currentPage--;
        updatePagination();
    }
});

$(document).on('click', '.pag-next', function(e) {
    e.preventDefault();
    var totalRows = $('#table-body tr').length;
    var totalPages = Math.ceil(totalRows / pageSize) || 1;
    if (currentPage < totalPages) {
        currentPage++;
        updatePagination();
    }
});

$(document).on('click', '.pag-page', function(e) {
    e.preventDefault();
    var page = parseInt($(this).data('page'));
    if (page && page !== currentPage) {
        currentPage = page;
        updatePagination();
    }
});

// Local addProductRow function (optimized to avoid slow AJAX requests since variables are preloaded)
function addProductRow() {
    var i = getNextBomRowIndex();
    
    var rowHtml = '<tr id="row' + i + '">' +
        '<td>' +
        '  <select class="form-control input-sm product_name_auto bom_item_search_name name_list" name="product_name[]" id="item_name' + i + '" onchange="myFunction1(this.id)" required="" data-live-search="true" style="width: 100%;">' +
             productOptionsHtml +
        '  </select>' +
        '  <input type="hidden" name="product_code[]" id="product_code' + i + '" class="product_code">' +
        '  <div class="heading-path text-muted" style="font-size: 10px; margin-top: 4px; background: #f8f9fa; padding: 2px 6px; border-radius: 4px; border-left: 3px solid #7e57c2; display: inline-block;">' +
        '      <i class="fa fa-folder-open-o" style="color: #7e57c2;"></i> Section: <strong class="path-text" style="color: #495057;">None</strong>' +
        '  </div>' +
        '</td>' +
        '<td>' +
        '  <input type="text" name="item_name_display[]" id="item_name_display' + i + '" class="form-control input-sm item_name_display" readonly style="background-color: #eee; width: 100%;">' +
        '</td>' +
        '<td>' +
        '  <button type="button" class="btn btn-info btn-xs" onclick="descButton(this.id)" id="btnDescriptionId' + i + '" title="Edit Description">Description</button>' +
        '  <textarea style="width: 150px;" class="form-control input-sm name_list description_auto hide" name="description[]" id="description' + i + '" rows="4"></textarea>' +
        '</td>' +
        '<td>' +
        '  <input type="number" min="1" name="quantity[]" id="quantity' + i + '" class="form-control input-sm" value="1" />' +
        '</td>' +
        '<td>' +
        '  <select class="form-control input-sm bom_item_search_unit" name="unit[]" id="unit' + i + '">' +
             unitOptionsHtml +
        '  </select>' +
        '</td>' +
        '<td>' +
        '  <input type="text" name="tag_no[]" id="tag_no' + i + '" class="form-control input-sm" />' +
        '</td>' +
        '<td>' +
        '  <button type="button" class="btn btn-info btn-xs" onclick="scopeButton(this.id)" id="btnScopeId' + i + '" title="Edit Scope">Scope</button>' +
        '  <textarea class="form-control input-sm hide" name="scope[]" id="scope' + i + '" rows="4"></textarea>' +
        '</td>' +
        '<td>' +
        '  <select class="form-control input-sm" name="stores_remark[]" id="stores_remark' + i + '">' +
        '    <option value="">Select</option>' +
        '    <option value="Y">Yes</option>' +
        '    <option value="N">No</option>' +
        '  </select>' +
        '</td>' +
        '<td>' +
        '  <button type="button" class="btn btn-info btn-xs" onclick="remarkButton(this.id)" id="btnRemarkId' + i + '" title="Edit Remark">Remark</button>' +
        '  <textarea class="form-control input-sm hide" name="remark[]" id="remark' + i + '" rows="4"></textarea>' +
        '</td>' +
        '<td class="text-center" style="white-space: nowrap; vertical-align: middle;">' +
        '  <button type="button" class="btn btn-success btn-xs insert-row-below" title="Insert Row Below" style="padding: 2px 7px;"><i class="fa fa-plus"></i></button>' +
'  <button type="button" class="btn btn-danger btn-xs btn-remove-bom-row" data-row="' + i + '" title="Remove Row" style="padding: 2px 7px;"><i class="fa fa-times"></i></button>' +
        '  <div class="dropdown" style="display:inline-block;">' +
        '    <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" title="More Options" style="padding: 2px 6px;"><span class="caret"></span></button>' +
        '    <ul class="dropdown-menu dropdown-menu-right" style="min-width: 160px;">' +
        '      <li><a href="#" class="move-row-up"><i class="fa fa-arrow-up"></i>&nbsp; Move Up</a></li>' +
        '      <li><a href="#" class="move-row-down"><i class="fa fa-arrow-down"></i>&nbsp; Move Down</a></li>' +
        '      <li role="separator" class="divider"></li>' +
        '      <li><a href="#" class="add-heading-below"><i class="fa fa-tag"></i>&nbsp; Add Heading</a></li>' +
        '    </ul>' +
        '  </div>' +
        '</td>' +
        '</tr>';
        
    $('#table-body').append(rowHtml);
    
    var $row = $('#row' + i);
    $row.find('.bom_item_search_name').select2({
        placeholder: "Select Item",
        allowClear: true,
        width: '100%',
        templateSelection: function(state) {
            if (!state.id || state.id === 'NEW') {
                return state.text;
            }
            return state.id;
        }
    });
    $row.find('.bom_item_search_unit').select2({
        placeholder: "Select Unit",
        allowClear: true
    });
    
    updateHeadingAssociations();
    
    // Switch page to show this newly added row
    var totalRows = $('#table-body tr').length;
    currentPage = Math.ceil(totalRows / pageSize) || 1;
    updatePagination();
}

function insertProductRowAfter($currentRow) {
    var i = getNextBomRowIndex();
    
    var rowHtml = '<tr id="row' + i + '">' +
        '<td>' +
        '  <select class="form-control input-sm product_name_auto bom_item_search_name name_list" name="product_name[]" id="item_name' + i + '" onchange="myFunction1(this.id)" required="" data-live-search="true" style="width: 100%;">' +
             productOptionsHtml +
        '  </select>' +
        '  <input type="hidden" name="product_code[]" id="product_code' + i + '" class="product_code">' +
        '  <div class="heading-path text-muted" style="font-size: 10px; margin-top: 4px; background: #f8f9fa; padding: 2px 6px; border-radius: 4px; border-left: 3px solid #7e57c2; display: inline-block;">' +
        '      <i class="fa fa-folder-open-o" style="color: #7e57c2;"></i> Section: <strong class="path-text" style="color: #495057;">None</strong>' +
        '  </div>' +
        '</td>' +
        '<td>' +
        '  <input type="text" name="item_name_display[]" id="item_name_display' + i + '" class="form-control input-sm item_name_display" readonly style="background-color: #eee; width: 100%;">' +
        '</td>' +
        '<td>' +
        '  <button type="button" class="btn btn-info btn-xs" onclick="descButton(this.id)" id="btnDescriptionId' + i + '" title="Edit Description">Description</button>' +
        '  <textarea style="width: 150px;" class="form-control input-sm name_list description_auto hide" name="description[]" id="description' + i + '" rows="4"></textarea>' +
        '</td>' +
        '<td>' +
        '  <input type="number" min="1" name="quantity[]" id="quantity' + i + '" class="form-control input-sm" value="1" />' +
        '</td>' +
        '<td>' +
        '  <select class="form-control input-sm bom_item_search_unit" name="unit[]" id="unit' + i + '">' +
             unitOptionsHtml +
        '  </select>' +
        '</td>' +
        '<td>' +
        '  <input type="text" name="tag_no[]" id="tag_no' + i + '" class="form-control input-sm" />' +
        '</td>' +
        '<td>' +
        '  <button type="button" class="btn btn-info btn-xs" onclick="scopeButton(this.id)" id="btnScopeId' + i + '" title="Edit Scope">Scope</button>' +
        '  <textarea class="form-control input-sm hide" name="scope[]" id="scope' + i + '" rows="4"></textarea>' +
        '</td>' +
        '<td>' +
        '  <select class="form-control input-sm" name="stores_remark[]" id="stores_remark' + i + '">' +
        '    <option value="">Select</option>' +
        '    <option value="Y">Yes</option>' +
        '    <option value="N">No</option>' +
        '  </select>' +
        '</td>' +
        '<td>' +
        '  <button type="button" class="btn btn-info btn-xs" onclick="remarkButton(this.id)" id="btnRemarkId' + i + '" title="Edit Remark">Remark</button>' +
        '  <textarea class="form-control input-sm hide" name="remark[]" id="remark' + i + '" rows="4"></textarea>' +
        '</td>' +
        '<td class="text-center" style="white-space: nowrap; vertical-align: middle;">' +
        '  <button type="button" class="btn btn-success btn-xs insert-row-below" title="Insert Row Below" style="padding: 2px 7px;"><i class="fa fa-plus"></i></button>' +
'  <button type="button" class="btn btn-danger btn-xs btn-remove-bom-row" data-row="' + i + '" title="Remove Row" style="padding: 2px 7px;"><i class="fa fa-times"></i></button>' +
        '  <div class="dropdown" style="display:inline-block;">' +
        '    <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" title="More Options" style="padding: 2px 6px;"><span class="caret"></span></button>' +
        '    <ul class="dropdown-menu dropdown-menu-right" style="min-width: 160px;">' +
        '      <li><a href="#" class="move-row-up"><i class="fa fa-arrow-up"></i>&nbsp; Move Up</a></li>' +
        '      <li><a href="#" class="move-row-down"><i class="fa fa-arrow-down"></i>&nbsp; Move Down</a></li>' +
        '      <li role="separator" class="divider"></li>' +
        '      <li><a href="#" class="add-heading-below"><i class="fa fa-tag"></i>&nbsp; Add Heading</a></li>' +
        '    </ul>' +
        '  </div>' +
        '</td>' +
        '</tr>';
        
    $currentRow.after(rowHtml);
    
    var $row = $('#row' + i);
    $row.find('.bom_item_search_name').select2({
        placeholder: "Select Item",
        allowClear: true,
        width: '100%',
        templateSelection: function(state) {
            if (!state.id || state.id === 'NEW') {
                return state.text;
            }
            return state.id;
        }
    });
    $row.find('.bom_item_search_unit').select2({
        placeholder: "Select Unit",
        allowClear: true
    });
    
    updateHeadingAssociations();
    
    // Switch page to show this newly added row
    var newRowIndex = $('#table-body tr').index($row);
    currentPage = Math.floor(newRowIndex / pageSize) + 1;
    updatePagination();
}

// Bind insert-row-below button click (debounced to prevent duplicate row creation)
$(document).off('click', '.insert-row-below').on('click', '.insert-row-below', function(e) {
    e.preventDefault();
    e.stopPropagation();
    var $btn = $(this);
    if ($btn.data('adding')) return;
    $btn.data('adding', true);
    
    var $currentRow = $btn.closest('tr');
    insertProductRowAfter($currentRow);
    
    setTimeout(function() {
        $btn.data('adding', false);
    }, 300);
});

// Bind heading-type-select change to immediately style row
$(document).on('change', '.heading-type-select', function() {
    var $tr = $(this).closest('tr');
    styleHeadingRow($tr);
    updateHeadingAssociations();
});

// Bind custom submit validation on form
$(document).ready(function() {
    $('.bom_item_search_name').each(function() {
        resetSelect2Instance($(this));
    });

    // Destroy existing Select2 instances to avoid double select UI issues
    $('.bom_item_search_name').filter('.select2-hidden-accessible').select2('destroy');
    // Initialize Select2 on existing table rows
    $('.bom_item_search_name').select2({
        placeholder: "Select Item",
        allowClear: true,
        templateSelection: function(state) {
            if (!state.id || state.id === 'NEW') {
                return state.text;
            }
            return state.id;
        }
    });
    
    $('.bom_item_search_unit').each(function() {
        resetSelect2Instance($(this));
    });
    $('.bom_item_search_unit').filter('.select2-hidden-accessible').select2('destroy');
    $('.bom_item_search_unit').select2({
        placeholder: "Select Unit",
        allowClear: true
    });

    // Unbind global add_gst_bom handler to prevent AJAX loading conflict
    $('#add_gst_bom').off('click');
    $('#add_gst_bom').on('click', function(e) {
        e.preventDefault();
        addProductRow();
    });
    
    // SO Number change handler — auto-fills SO details & matching BOM sequence number
    $(document).on('change', '#bom_so_number_select', function() {
        var soNo = $(this).val();
        if (!soNo) return;
        
        $('#bom_so_loading').show();
        $('#bom_so_fetch_status').text('');
        
        $.ajax({
            url: base_url + 'BomController/get_salesorder_details',
            type: 'POST',
            data: { so_number: soNo },
            dataType: 'json',
            success: function(res) {
                $('#bom_so_loading').hide();
                if (res && res.success) {
                    var statusText = '✓ Status: Loaded (' + res.so_number + ')';
                    if (res.is_revision) {
                        statusText = '✓ Status: Creating Revision (' + res.suggested_bom_number + ') for ' + res.so_number;
                    }
                    $('#bom_so_fetch_status').css('color', '#2e7d32').text(statusText);
                    if (res.customer_id) $('#customer_id').val(res.customer_id).trigger('change');
                    if (res.system) $('input[name="system"]').val(res.system);
                    if (res.location) $('input[name="location"]').val(res.location);
                    if (res.capacity) $('input[name="capacity"]').val(res.capacity);
                    if (res.oc_number) $('input[name="oc_number"]').val(res.oc_number);
                    if (res.customer_code) $('input[name="customer_code"]').val(res.customer_code);
                    if (res.project_qty) $('input[name="project_qty"]').val(res.project_qty);

                    // Set suggested matching BOM number (e.g. BOM/00162/26-27 for SO 162)
                    if (res.suggested_bom_number) {
                        $('#number').val(res.suggested_bom_number);
                        $('#bom_number_display').text(res.suggested_bom_number).css('color', '#00a65a');
                    }
                } else {
                    $('#bom_so_fetch_status').css('color', '#c62828').text('⚠ ' + (res.message || 'SO data error'));
                }
            },
            error: function() {
                $('#bom_so_loading').hide();
                $('#bom_so_fetch_status').css('color', '#c62828').text('⚠ Server error fetching SO');
            }
        });
    });

    // Initialize first row visual styling
    updateHeadingAssociations();
    updatePagination();
    
    $('#add_name').on('submit', function(e) {
        var isValid = true;
        var firstInvalidRowIndex = -1;
        var $rows = $('#table-body tr');

        // Check for duplicate item codes in BOM table
        var selectedCodes = [];
        var hasDuplicate = false;
        var duplicateCode = '';
        
        $rows.each(function() {
            var $row = $(this);
            if (!$row.hasClass('bom-heading-row')) {
                var code = $row.find('.bom_item_search_name').val();
                if (code && code !== 'NEW') {
                    if (selectedCodes.indexOf(code) !== -1) {
                        hasDuplicate = true;
                        duplicateCode = code;
                        return false;
                    }
                    selectedCodes.push(code);
                }
            }
        });
        
        if (hasDuplicate) {
            // alert('Duplicate Item Code: "' + duplicateCode + '" is not allowed!');
            return false;
        }
        
        if (!$('#customer_id').val()) {
            alert('Please select a Company.');
            $('#customer_id').focus();
            return false;
        }
        if ($('#project_code').length > 0 && !$('#project_code').val()) {
            alert('Please select a Project Code.');
            $('#project_code').focus();
            return false;
        }
        
        $rows.each(function(index) {
            var $row = $(this);
            if ($row.hasClass('bom-heading-row')) {
                var $headingInput = $row.find('.heading-text-input');
                if (!$headingInput.val().trim()) {
                    isValid = false;
                    if (firstInvalidRowIndex === -1) {
                        firstInvalidRowIndex = index;
                    }
                    $headingInput.css('border', '1px solid #a94442').addClass('has-error');
                } else {
                    $headingInput.css('border', '').removeClass('has-error');
                }
            } else {
                var $productSelect = $row.find('.bom_item_search_name');
                if (!$productSelect.val()) {
                    isValid = false;
                    if (firstInvalidRowIndex === -1) {
                        firstInvalidRowIndex = index;
                    }
                    $row.find('.select2-selection').css('border', '1px solid #a94442');
                } else {
                    $row.find('.select2-selection').css('border', '');
                }
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            alert('Please fill all required fields in the table (Product Names and Heading Texts).');
            if (firstInvalidRowIndex !== -1) {
                var targetPage = Math.floor(firstInvalidRowIndex / pageSize) + 1;
                currentPage = targetPage;
                updatePagination();
                
                var $invalidRow = $rows.eq(firstInvalidRowIndex);
                if ($invalidRow.hasClass('bom-heading-row')) {
                    $invalidRow.find('.heading-text-input').focus();
                } else {
                    $invalidRow.find('.bom_item_search_name').select2('open');
                }
            }
            return false;
        }
        
        // Remove native required attributes from hidden rows so form submit doesn't block
        $(this).find('[required]').removeAttr('required');
    });
});

var currentExcelItems = [];

function submitResolveMappings(mappings) {
    console.log('submitResolveMappings called with mappings:', mappings);
    var $btn = $('#btn_confirm_resolve_import');
    var originalText = $btn.html() || '<i class="fa fa-check"></i> Proceed & Import BOM';
    if ($btn.length) {
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Importing...');
    }
    
    $.ajax({
        url: base_url + 'BomController/ajax_confirm_import_bom',
        type: 'POST',
        data: { mappings: JSON.stringify(mappings) },
        dataType: 'json',
        success: function(response) {
            console.log('ajax_confirm_import_bom response:', response);
            if (response && response.success) {
                // Update productOptionsHtml with new products
                if (response.new_products && response.new_products.length > 0) {
                    for (var n = 0; n < response.new_products.length; n++) {
                        var np = response.new_products[n];
                        var escapedCode = $('<div>').text(np.code).html();
                        var escapedName = $('<div>').text(np.item_name).html();
                        
                        if (!loadedProductCodes[np.code]) {
                            loadedProductCodes[np.code] = true;
                            var displayVal = (escapedCode.toUpperCase().trim() === escapedName.toUpperCase().trim()) ? escapedName : (escapedCode + ' - ' + escapedName);
                            productOptionsHtml += '<option value="' + escapedCode + '">' + displayVal + '</option>';
                        }
                    }
                }
                importBOMRows(response.items);
                $('#bomExcelResolveModal').modal('hide');
            } else {
                alert('Import confirmation failed: ' + (response.message || 'Unknown error'));
            }
        },
        error: function(xhr) {
            alert('An error occurred while confirming import. Please try again.');
            console.error('BOM Resolve Import Error:', xhr.responseText);
        },
        complete: function() {
            if ($btn.length) {
                $btn.prop('disabled', false).html(originalText);
            }
        }
    });
}

function showExcelResolveModal(unresolvedCount, autoMatchedCount) {
    if ($('#bomExcelResolveModal').length === 0) {
        var modalHtml = `
        <div class="modal fade" id="bomExcelResolveModal" tabindex="-1" role="dialog" aria-labelledby="bomExcelResolveModalLabel" aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog modal-lg" style="width: 95%;">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white; opacity: 1;"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="bomExcelResolveModalLabel"><i class="fa fa-exchange"></i> Resolve Products from Excel Import</h4>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info" style="margin-bottom: 15px;">
                            <i class="fa fa-info-circle"></i> We found <strong>\${unresolvedCount}</strong> item(s) in your Excel sheet that are new, have similar matches, or have detail mismatches. Please choose how to handle each one.
                        </div>
                        
                        <div style="margin-bottom: 15px;">
                            <button type="button" class="btn btn-primary btn-sm" id="btn_resolve_use_db_all" style="display: none;"><i class="fa fa-database"></i> Use DB Matches for All</button>
                            <button type="button" class="btn btn-warning btn-sm" id="btn_resolve_update_db_all" style="display: none;"><i class="fa fa-edit"></i> Update DB with Excel Details for All</button>
                            <button type="button" class="btn btn-danger btn-sm" id="btn_resolve_create_new_all"><i class="fa fa-plus-circle"></i> Create New Product for All</button>
                        </div>

                        <div style="max-height: 400px; overflow-y: auto; border: 1px solid #ddd;">
                            <table class="table table-bordered table-striped" id="resolve_items_table" style="margin-bottom: 0;">
                                <thead>
                                    <tr class="bg-gray">
                                        <th style="width: 5%;">Excel Row</th>
                                        <th style="width: 25%;">Excel Item Details</th>
                                        <th style="width: 35%;">Database Matches / Search</th>
                                        <th style="width: 35%;">Action & Comparison Details</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <span id="auto_matched_count_label" class="pull-left text-success" style="margin-top: 8px; font-weight: bold;"></span>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-success" id="btn_confirm_resolve_import"><i class="fa fa-check"></i> Proceed & Import BOM</button>
                    </div>
                </div>
            </div>
        </div>
        `;
        $('body').append(modalHtml);
    }
    
    $('#auto_matched_count_label').html('<i class="fa fa-check-circle"></i> ' + autoMatchedCount + ' other items matched perfectly and will be imported automatically.');
    
    var tbody = $('#resolve_items_table tbody');
    tbody.empty();
    
    for (var k = 0; k < currentExcelItems.length; k++) {
        var item = currentExcelItems[k];
        if (item.type === 'subheading' || item.product_name === '__HEADING__') continue;
        var isDetailsMatch = (item.details_match === true || item.details_match === 'true' || item.details_match === 1 || item.details_match === '1');
        if (isDetailsMatch) continue;
        
        var escapedRawName = $('<div>').text(item.raw_name).html();
        var escapedExcelCode = $('<div>').text(item.item_code || 'N/A').html();
        var escapedExcelUnit = $('<div>').text(item.unit || 'N/A').html();
        var escapedExcelDesc = $('<div>').text(item.description || 'N/A').html();
        
        var dbMatchesOptionsHtml = '<option value="">-- Create New Product --</option>';
        
        if (item.matches && item.matches.length > 0) {
            dbMatchesOptionsHtml += '<optgroup label="Suggested Matches">';
            for (var m = 0; m < item.matches.length; m++) {
                var match = item.matches[m];
                var isSelected = (item.product_name === match.code) ? 'selected' : '';
                dbMatchesOptionsHtml += '<option value="' + $('<div>').text(match.code).html() + '" data-name="' + $('<div>').text(match.item_name).html() + '" data-unit="' + $('<div>').text(match.unit).html() + '" data-desc="' + $('<div>').text(match.prod_description).html() + '" ' + isSelected + '>';
                dbMatchesOptionsHtml += $('<div>').text(match.code + ' - ' + match.item_name).html();
                dbMatchesOptionsHtml += '</option>';
            }
            dbMatchesOptionsHtml += '</optgroup>';
        }
        
        dbMatchesOptionsHtml += '<optgroup label="All Inventory Products">';
        if (Array.isArray(rawProducts)) {
            for (var idx = 0; idx < rawProducts.length; idx++) {
                var p = rawProducts[idx];
                var isSuggested = false;
                if (item.matches) {
                    for (var m = 0; m < item.matches.length; m++) {
                        if (item.matches[m].code === p.code) {
                            isSuggested = true;
                            break;
                        }
                    }
                }
                if (!isSuggested) {
                    dbMatchesOptionsHtml += '<option value="' + $('<div>').text(p.code).html() + '" data-name="' + $('<div>').text(p.item_name).html() + '" data-unit="' + $('<div>').text(p.unit).html() + '" data-desc="' + $('<div>').text(p.prod_description).html() + '">';
                    dbMatchesOptionsHtml += $('<div>').text(p.code + ' - ' + p.item_name).html();
                    dbMatchesOptionsHtml += '</option>';
                }
            }
        }
        dbMatchesOptionsHtml += '</optgroup>';
        
        var rowHtml = `
        <tr data-index="${k}">
            <td><strong>#${item.excel_row}</strong></td>
            <td>
                <strong>Code:</strong> ${escapedExcelCode}<br/>
                <strong>Name:</strong> ${escapedRawName}<br/>
                <small class="text-muted">
                    <strong>Unit:</strong> ${escapedExcelUnit}<br/>
                    <strong>Desc:</strong> ${escapedExcelDesc}
                </small>
            </td>
            <td>
                <span class="text-muted"><i class="fa fa-plus-circle"></i> New Product</span>
            </td>
            <td>
                <div class="resolution-options" style="margin-bottom: 5px;">
                    <label class="radio-inline" style="margin-left: 0; margin-right: 10px;">
                        <input type="radio" name="action_${k}" value="CREATE" checked> Create New
                    </label>
                    <button type="button" class="btn btn-xs btn-primary btn-fill-details" data-index="${k}" style="margin-left: 5px; padding: 2px 5px; font-size: 10px;" title="Fill new item details"><i class="fa fa-pencil"></i> Fill Details</button>
                    <label class="radio-inline" style="margin-left: 0; margin-right: 10px; color: #a94442;">
                        <input type="radio" name="action_${k}" value="SKIP"> Skip Row
                    </label>
                </div>
                <div class="comparison-details text-info" style="font-size: 11px; line-height: 1.2;">
                </div>
            </td>
        </tr>
        `;
        tbody.append(rowHtml);
        tbody.find('tr[data-index="' + k + '"] .db-match-select').trigger('change');
    }
    
    // Check if any imported item is missing an item code
    var hasEmptyItemCode = false;
    for (var k = 0; k < currentExcelItems.length; k++) {
        var item = currentExcelItems[k];
        if (item.type === 'subheading' || item.product_name === '__HEADING__') continue;
        var isDetailsMatch = (item.details_match === true || item.details_match === 'true' || item.details_match === 1 || item.details_match === '1');
        if (isDetailsMatch) continue;
        if (!item.item_code || item.item_code.trim() === '') {
            hasEmptyItemCode = true;
        }
    }
    
    var alertContainer = $('#bomExcelResolveModal .modal-body .alert-info');
    $('#resolve_warning_alert').remove(); // remove any previous warning
    if (hasEmptyItemCode) {
        var warningAlertHtml = `
        <div class="alert alert-warning animate-alert" id="resolve_warning_alert" style="margin-top: 10px; margin-bottom: 10px; border-left: 5px solid #d9534f; background-color: #fcf8e3; color: #8a6d3b;">
            <i class="fa fa-warning" style="font-size: 16px; margin-right: 8px;"></i>
            <strong>Warning:</strong> Some new items in your Excel sheet do not have an Item Code. You must click the <strong>"Fill Details"</strong> button next to "Create New" to specify a unique Item Code for those rows, or select <strong>"Skip Row"</strong> to exclude them from the import.
        </div>
        `;
        alertContainer.after(warningAlertHtml);
    }
    
    $('#bomExcelResolveModal').modal('show');
}

$(document).on('change', '.db-match-select', function() {
    var $select = $(this);
    var $row = $select.closest('tr');
    var idx = $row.data('index');
    var item = currentExcelItems[idx];
    
    var selectedCode = $select.val();
    var $actionCreate = $row.find('input[value="CREATE"]');
    var $actionUseDb = $row.find('input[value="USE_DB"]');
    var $actionUpdateDb = $row.find('input[value="UPDATE_DB"]');
    var $matchOptions = $row.find('.resolve-match-option');
    var $compDiv = $row.find('.comparison-details');
    var $btnFill = $row.find('.btn-fill-details');
    
    if (!selectedCode) {
        $matchOptions.hide();
        $actionCreate.prop('checked', true);
        $btnFill.show();
        $compDiv.html('');
    } else {
        // Check if this code is already selected in any other row
        var isDuplicate = false;
        $('#resolve_items_table tbody tr').each(function() {
            var $otherRow = $(this);
            if ($otherRow.data('index') !== idx) {
                var $otherSelect = $otherRow.find('.db-match-select');
                var otherAction = $otherRow.find('input[type="radio"]:checked').val();
                if ((otherAction === 'USE_DB' || otherAction === 'UPDATE_DB') && $otherSelect.val() === selectedCode) {
                    isDuplicate = true;
                    return false;
                }
            }
        });
        
        if (isDuplicate) {
            alert('Duplicate Item Code: "' + selectedCode + '" is already selected for another row in this import.');
            $select.val('').trigger('change');
            return;
        }

        $matchOptions.show();
        $btnFill.hide();
        var currentAction = $row.find('input[type="radio"]:checked').val();
        if (currentAction === 'CREATE' || currentAction === 'SKIP') {
            $actionUseDb.prop('checked', true);
        }
        
        var $opt = $select.find('option:selected');
        var dbUnit = $opt.data('unit') || '';
        var dbDesc = $opt.data('desc') || '';
        
        var compHtml = '<strong>Diffs:</strong> ';
        var diffs = [];
        
        if ((item.unit || '').toString().trim() !== (dbUnit || '').toString().trim()) {
            diffs.push('Unit ("' + (item.unit || 'N/A') + '" vs "' + (dbUnit || 'N/A') + '")');
        }
        if ((item.description || '').toString().trim() !== (dbDesc || '').toString().trim()) {
            var truncatedExcelDesc = (item.description || '').length > 20 ? (item.description || '').substring(0, 20) + '...' : (item.description || '');
            var truncatedDbDesc = (dbDesc || '').length > 20 ? (dbDesc || '').substring(0, 20) + '...' : (dbDesc || '');
            diffs.push('Desc ("' + truncatedExcelDesc + '" vs "' + truncatedDbDesc + '")');
        }
        
        if (diffs.length > 0) {
            compHtml += '<span class="text-warning"><i class="fa fa-warning"></i> ' + diffs.join(', ') + '</span>';
        } else {
            compHtml += '<span class="text-success"><i class="fa fa-check"></i> Details match perfectly</span>';
        }
        
        $compDiv.html(compHtml);
    }
});

$(document).on('click', '#btn_resolve_use_db_all', function() {
    $('#resolve_items_table tbody tr').each(function() {
        var $row = $(this);
        var $select = $row.find('.db-match-select');
        if ($select.val()) {
            $row.find('input[value="USE_DB"]').prop('checked', true);
        } else {
            $row.find('input[value="CREATE"]').prop('checked', true);
        }
    });
});

$(document).on('click', '#btn_resolve_update_db_all', function() {
    $('#resolve_items_table tbody tr').each(function() {
        var $row = $(this);
        var $select = $row.find('.db-match-select');
        if ($select.val()) {
            $row.find('input[value="UPDATE_DB"]').prop('checked', true);
        } else {
            $row.find('input[value="CREATE"]').prop('checked', true);
        }
    });
});

$(document).on('click', '#btn_resolve_create_new_all', function() {
    $('#resolve_items_table tbody tr').each(function() {
        var $row = $(this);
        $row.find('.db-match-select').val('').trigger('change');
        $row.find('input[value="CREATE"]').prop('checked', true);
    });
});

$(document).on('click', '#btn_confirm_resolve_import', function() {
    var mappings = [];
    var hasError = false;
    var errorMsg = '';
    
    $('#resolve_items_table tbody tr').each(function() {
        var $row = $(this);
        var idx = $row.data('index');
        var excelItem = currentExcelItems[idx];
        var action = $row.find('input[type="radio"]:checked').val();
        var dbCode = $row.find('.db-match-select').val();
        
        if (action === 'CREATE' && (!excelItem.item_code || excelItem.item_code.trim() === '')) {
            hasError = true;
            errorMsg = 'Row #' + excelItem.excel_row + ' is missing an Item Code. Please click "Fill Details" next to "Create New" to add a unique Item Code, or select "Skip Row" to exclude it.';
            return false;
        }
        
        mappings.push({
            excel_index: idx,
            action: action,
            db_code: dbCode,
            excel_item: excelItem
        });
    });
    
    if (hasError) {
        alert(errorMsg);
        return;
    }
    
    for (var k = 0; k < currentExcelItems.length; k++) {
        var item = currentExcelItems[k];
        if (item.type === 'subheading' || item.product_name === '__HEADING__') {
            mappings.push({
                excel_index: k,
                action: 'SUBHEADING',
                db_code: '__HEADING__',
                excel_item: item
            });
        } else {
            var isDetailsMatch = (item.details_match === true || item.details_match === 'true' || item.details_match === 1 || item.details_match === '1');
            if (isDetailsMatch) {
                if (!item.product_name || item.product_name.trim() === '') {
                    hasError = true;
                    errorMsg = 'Auto-matched row #' + item.excel_row + ' is missing an Item Code. Please click "Fill Details" to add a unique Item Code.';
                    break;
                }
                mappings.push({
                    excel_index: k,
                    action: 'USE_DB',
                    db_code: item.product_name,
                    excel_item: item
                });
            }
        }
    }
    
    if (hasError) {
        alert(errorMsg);
        return;
    }
    
    // Sort mappings by original index to preserve Excel row order
    mappings.sort(function(a, b) {
        return a.excel_index - b.excel_index;
    });
    
    submitResolveMappings(mappings);
});

// Bind click handler for Fill Details buttons inside resolve modal
$(document).on('click', '.btn-fill-details', function(e) {
    e.preventDefault();
    var idx = $(this).data('index');
    var item = currentExcelItems[idx];
    
    window.activeResolveRowIdx = idx;
    
    // Reset productModal form
    $('#productModal form').trigger('reset');
    
    // Pre-fill Name, Description, Unit
    $('#productModal form input[name="item_name"]').val(item.raw_name || '');
    $('#productModal form textarea[name="description"]').val(item.description || '');
    
    if (item.unit) {
        var matchedUnit = '';
        $('#productModal form select[name="unit"] option').each(function() {
            if ($(this).val().toUpperCase().trim() === item.unit.toUpperCase().trim()) {
                matchedUnit = $(this).val();
                return false;
            }
        });
        if (matchedUnit) {
            $('#productModal form select[name="unit"]').val(matchedUnit);
        }
    }
    
    $('#productModal form input[name="item_code"]').val(item.item_code || '');
    $('#productModal').modal('show');
});

// Bind change handler on resolve modal radio buttons to check for duplicates
$(document).on('change', '#resolve_items_table input[type="radio"]', function() {
    var $radio = $(this);
    var action = $radio.val();
    if (action === 'USE_DB' || action === 'UPDATE_DB') {
        var $row = $radio.closest('tr');
        var idx = $row.data('index');
        var $select = $row.find('.db-match-select');
        var val = $select.val();
        if (!val) return;
        
        var isDuplicate = false;
        $('#resolve_items_table tbody tr').each(function() {
            var $otherRow = $(this);
            if ($otherRow.data('index') !== idx) {
                var $otherSelect = $otherRow.find('.db-match-select');
                var otherAction = $otherRow.find('input[type="radio"]:checked').val();
                if ((otherAction === 'USE_DB' || otherAction === 'UPDATE_DB') && $otherSelect.val() === val) {
                    isDuplicate = true;
                    return false;
                }
            }
        });
        
        if (isDuplicate) {
            alert('Duplicate Item Code: "' + val + '" is already selected for another row in this import.');
            $row.find('input[value="CREATE"]').prop('checked', true);
            $select.val('').trigger('change');
        }
    }
});

// Bind change check on item code dropdown in main BOM table
$(document).on('change', '.bom_item_search_name', function() {
    var $select = $(this);
    var val = $select.val();
    if (!val || val === 'NEW') return;
    
    var count = 0;
    $('.bom_item_search_name').each(function() {
        if ($(this).val() === val) {
            count++;
        }
    });
    
    if (count > 1) {
        alert('Duplicate Item Code: "' + val + '" is not allowed!');
        $select.val('').trigger('change');
    }
});
</script>