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
    
    /* SO Selection Row */
    .so-selection-row {
        background-color: #f9f9f9;
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 5px;
        border: 1px solid #ddd;
    }
    
    .loading-spinner {
        display: none;
        margin-left: 10px;
    }
    
    .so-select-container {
        position: relative;
    }

    /* ===== Section Heading Row Styling ===== */
    .jo-heading-row td {
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
    .jo-heading-row .heading-text-input {
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
                    Job Order
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="#"> JOB ORDER</a></li>
                    <li class="active"> Create Job Order</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">

                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-info">
                            <div class="box-header">
                                <h3 class="box-title">Create Job Order</h3>
                                <a href="javascript:void(0);" onclick="window.history.back()" class="btn btn-primary pull-right"><i class="fa fa-close"></i> Close</a>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">
                                <?php if ($this->session->flashdata('error')) { ?>
                                    <div class="alert alert-danger alert-dismissible" style="margin-bottom: 20px;">
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                        <h4><i class="icon fa fa-ban"></i> Error!</h4>
                                        <?php echo $this->session->flashdata('error'); ?>
                                    </div>
                                <?php } ?>
                                <?php if ($this->session->flashdata('SUCCESSMSG')) { ?>
                                    <div class="alert alert-success alert-dismissible" style="margin-bottom: 20px;">
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                        <h4><i class="icon fa fa-check"></i> Success!</h4>
                                        <?php echo $this->session->flashdata('SUCCESSMSG'); ?>
                                    </div>
                                <?php } ?>
                                <form class="form-horizontal form_overlay" name="add_name" id="add_name" method="post" action="<?php echo base_url(); ?>JobOrderController/add_joborder" enctype="multipart/form-data">
                                    
                                    <!-- Sales Order Selection Section -->
                                    <div class="row so-selection-row">
                                        <div class="col-md-12">
                                            <div class="form-group row">
                                                <label class="col-sm-2 control-label">Select SO / Number</label>
                                                <div class="col-sm-6 so-select-container">
                                                    <select class="form-control input-sm select2" name="so_number_select" id="so_number_select">
                                                        <option value="">-- Select Sales Order Number --</option>
                                                        <?php if(isset($salesorder_list) && !empty($salesorder_list)): ?>
                                                            <?php foreach($salesorder_list as $so): ?>
                                                                <option value="<?php echo htmlspecialchars($so->number); ?>">
                                                                    <?php echo $so->number; ?> - <?php echo htmlspecialchars($so->customer_name ?? ''); ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        <?php endif; ?>
                                                    </select>
                                                    <span class="loading-spinner" id="so_loading_spinner">
                                                        <i class="fa fa-spinner fa-spin"></i> Loading...
                                                    </span>
                                                </div>
                                                <div class="col-sm-4">
                                                    <button type="button" class="btn btn-primary" id="load_so_items_btn" onclick="loadSalesOrderItems()">
                                                        <i class="fa fa-download"></i> Load Items
                                                    </button>
                                                    <button type="button" class="btn btn-default" id="clear_so_btn" onclick="clearSalesOrderSelection()">
                                                        <i class="fa fa-refresh"></i> Clear
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group row">
                                                <?php
                                                if (date('m') <= 3) {
                                                    $financial_year = (date('y') - 1) . '-' . date('y');
                                                } else {
                                                    $financial_year = date('y') . '-' . (date('y') + 1);
                                                }
                                                ?>
                                                <input type="hidden" name="gst_discount_check" value="gst_discount_check" id="gst_discount_check">
                                                <input type="hidden" class="form-control input-sm" name="number" id="number" required="" value="JO/<?php printf("%04d", $joborder_id + 1); ?>/<?php echo $financial_year; ?>">
                                                <input type="hidden" name="joborder_gst_check" value="gst" id="joborder_gst_check">
                                                <input type="hidden" name="so_number" id="so_number" value="">
                                                <label class="col-sm-12 control-label"><h2>Job Order: <b>JO/<?php printf("%04d", $joborder_id + 1); ?>/<?php echo $financial_year; ?></b></h2></label>
                                            </div>
                                        </div>    
                                    </div>

                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group row">
                                                <label class="col-sm-4 control-label">Company<span style="color: red;">*</span></label>
                                                <div class="col-sm-8">
                                                    <select class="form-control input-sm select2 company_search_name" required name="customer_id" id="customer_id">
                                                        <option value="" data-ccode="">Select Company</option>
                                                        <?php foreach ($company_name as $key) { ?>
                                                            <option value="<?php echo $key->customer_id; ?>" data-ccode="<?php echo htmlspecialchars($key->c_code ?? ''); ?>"><?php echo $key->company_name . " - " . ($key->c_code ?? ''); ?></option> 
                                                        <?php } ?>  
                                                    </select>
                                                    <input type="hidden" name="customer_code" id="customer_code">

                                                    <span class="btn btn-success btn-sm" data-toggle="modal" data-target="#myModal" style="margin-top: 10%"><i class="glyphicon glyphicon-plus"></i>Add Company</span>
                                                </div>
                                            </div>
                                            
                                            <div class="form-group row hide">
                                                <label for="oc_number" class="col-sm-4 control-label">SO Number</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control input-sm" name="oc_number" id="oc_number"
                                                        autocomplete="off">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-3">
                                            <div class="form-group row">
                                                <label class="col-sm-4 control-label">Date<span style="color: red;">*</span></label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control alldate input-sm created-date" name="date" id="date" required="">
                                                </div>
                                            </div>



                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group row">
                                                <label class="col-sm-4 control-label">Status</label>
                                                <div class="col-sm-8">
                                                    <select class="form-control input-sm" name="status_main" id="status_main">
                                                        <option value="1">Draft</option>
                                                        <option value="2">Sent</option>
                                                        <option value="3">Viewed</option>
                                                        <option value="4">Approved</option> 
                                                        <option value="5">Rejected</option>
                                                        <option value="6">Canceled</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group row hide ">
                                                <label for="project_qty" class="col-sm-4 control-label ">Project Quantity</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control input-sm" name="project_qty" id="project_qty">
                                                </div>
                                            </div>
                                            <div class="form-group row hide ">
                                                <label class="col-sm-4 control-label">System</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control input-sm" name="system" id="system">
                                                </div>
                                            </div>
                                            <div class="form-group row hide">
                                                <label class="col-sm-4 control-label">Location</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control input-sm" name="location" id="location">
                                                </div>
                                            </div>
                                            <!-- ★ SO Number Selector ★ -->
                                            <!-- <div class="form-group row" style="background: #f0f8ff; border: 1px solid #b8d9f5; border-radius: 6px; padding: 8px 12px; margin-bottom: 6px;">
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
                                            </div> -->
                                            <!-- /SO Number Selector -->
                                            <?php
$_sess_perm = $this->session->userdata('session_data_head');
$_has_project_master = isset($_sess_perm['permission']) && in_array('Projects', $_sess_perm['permission']);
if ($_has_project_master): ?>
<div class="form-group row">
                                                <label class="col-sm-4 control-label">Project Code</label>
                                                <div class="col-sm-8">
                                                    <select class="form-control input-sm" name="project_code" id="project_code">
                                                        <option value="">Select Project Code</option>
                                                        <?php if(isset($project_code_result) && !empty($project_code_result)): ?>
                                                            <?php foreach($project_code_result as $pc): ?>
                                                                <option value="<?php echo htmlspecialchars($pc->project_code); ?>"><?php echo htmlspecialchars($pc->project_code); ?></option>
                                                            <?php endforeach; ?>
                                                        <?php endif; ?>
                                                    </select>
                                                </div>
                                            </div>
<?php endif; // Project Master permission ?>

                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group row">
                                                <label class="col-sm-4 control-label">Capacity</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control input-sm" name="capacity" id="capacity">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Linked Sales Order Items Reference Card -->
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
                                                    <th>price</th>
                                                    <th>Remark</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="table-body">
                                                <tr id="row1">  
                                                    <td>
                                                        <select class="form-control input-sm product_name_auto item_search_name name_list" name="product_name[]" id="item_name1" onchange="myFunction1(this.id)" required="" data-live-search="true">
                                                            <option value="">Select Product</option>
                                                            <option value="NEW">+ Add New Product</option>
                                                            <?php foreach ($item_name as $key) { ?>
                                                                <option value="<?php echo $key->code; ?>"> <?php echo $key->code . " - " . $key->item_name; ?></option>
                                                            <?php } ?>  
                                                        </select>
                                                        <input type="hidden" name="product_code[]" id="product_code1" class="product_code">
                                                    </td> 
                                                    <td>
                                                        <button type="button" class="btn btn-info btn-xs" onclick="descButton(this.id)" id="btnDescriptionId1" title="Edit Description">Description</button>
                                                        <textarea style="width: 150px;" class="form-control input-sm name_list description_auto hide" name="description[]" id="description1" rows="4"></textarea>
                                                    </td> 
                                                    <td>
                                                        <input type="text" min="1" name="quantity[]" id="quantity1" class="form-control input-sm" value="1" />
                                                    </td> 
                                                    <td>
                                                        <select class="form-control input-sm item_search_unit" name="unit[]" id="unit1">
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
                                                        <input type="text" name="price[]" id="price1" class="form-control input-sm" />
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-info btn-xs" onclick="remarkButton(this.id)" id="btnRemarkId1" title="Edit Remark">Remark</button>
                                                        <textarea class="form-control input-sm hide" name="remark[]" id="remark1" rows="4"></textarea>
                                                    </td> 
                                                    <td class="text-center" style="white-space: nowrap; vertical-align: middle;">
                                                        <button type="button" name="add_joborder_item" id="add_gst_bom" class="btn btn-success btn-xs action-header-btn" title="Add New Row" data-local-row-handler="1"><i class="fa fa-plus-circle"></i></button>
                                                        <button type="button" id="add_heading_row" class="btn btn-heading btn-xs action-header-btn" title="Add Section Heading"><i class="fa fa-tag" style="font-size:13px;"></i></button>
                                                        <button type="button" class="btn btn-danger btn-xs action-header-btn" title="Delete Row" onclick="deleteRow('row1')">X</button>
                                                    </td>  

                                                </tr>  
                                        </table>
                                        
                                        <!-- Table Pagination Controls -->
                                        <div id="table-pagination-container" class="row" style="margin-top: 15px; margin-bottom: 15px; background: #f8fafc; padding: 10px 15px; border-radius: 6px; border: 1px solid #e2e8f0; display: none;">
                                            <div class="col-sm-4" style="line-height: 30px;">
                                                <span id="pagination-info" style="font-size: 13px; color: #475569; font-weight: 600;">
                                                    Showing 1 to 25 of 0 items
                                                </span>
                                            </div>
                                            <div class="col-sm-4 text-center" style="line-height: 30px;">
                                                <label style="font-size: 12px; margin-right: 6px; color: #475569; font-weight: 500;">Per Page:</label>
                                                <select id="items_per_page" class="input-sm" style="border-radius: 4px; padding: 2px 8px; border: 1px solid #cbd5e1; background: #fff;" onchange="changePageSize(this.value)">
                                                    <option value="25" selected>25 items</option>
                                                    <option value="50">50 items</option>
                                                    <option value="100">100 items</option>
                                                    <option value="ALL">All items</option>
                                                </select>
                                            </div>
                                            <div class="col-sm-4 text-right">
                                                <div class="btn-group" id="pagination-buttons">
                                                    <button type="button" class="btn btn-default btn-sm" id="btn-prev-page" onclick="prevPage()"><i class="fa fa-chevron-left"></i> Previous</button>
                                                    <span id="page-num-display" class="btn btn-default btn-sm disabled" style="background: #fff; color: #1e293b; font-weight: 600;">Page 1 of 1</span>
                                                    <button type="button" class="btn btn-default btn-sm" id="btn-next-page" onclick="nextPage()">Next <i class="fa fa-chevron-right"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-xs-12">
                                                <div class="form-group row">
                                                    <label class="col-sm-2 control-label">Note</label>
                                                    <div class="col-sm-8">
                                                        <textarea class="form-control" name="note" id="note" rows="3" placeholder="Enter any additional notes..."></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-xs-12 text-right" style="margin-bottom: 10px;">
                                                <span id="total_item_qty_display" style="font-weight: bold; color: #333;">Total Item Qty: 0</span>
                                            </div>
                                            <div class="col-xs-10 text-center">
                                                <button type="submit" name="submit" id="submit" class="btn btn-success btn-lg"> Save </button>
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
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title text-center">Add New Company</h4>
                </div>
                <form method="post" action="<?php echo base_url(); ?>JobOrderController/add_customer">
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
                            <label class="col-sm-3 control-label">GST No</label>
                            <div class="col-sm-9">
                                <input type="text" maxlength="15" class="form-control input-sm" name="gst" id="gst" placeholder="e.g., 27AAPFU0205R1Z0">
                            </div>
                            <small class="text-muted col-sm-9 col-sm-offset-3">15-digit GST number</small>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 control-label">PAN Card</label>
                            <div class="col-sm-9">
                                <input type="text" maxlength="10" class="form-control input-sm" name="pancard" id="pancard" placeholder="e.g., AAPFU0205R">
                            </div>
                            <small class="text-muted col-sm-9 col-sm-offset-3">10-digit PAN (auto-filled from GST if available)</small>
                        </div>

                         <div class="form-group row">
                            <label class="col-sm-3 control-label">State Code</label>
                            <div class="col-sm-9">
                                <input type="number" class="form-control input-sm" name="state_code" id="state_code">
                            </div>
                        </div>

                          <div class="form-group row">
                            <label class="col-sm-3 control-label">Email</label>
                            <div class="col-sm-9">
                                <input type="email" class="form-control input-sm" name="email" id="email">
                            </div>
                        </div>

                          
                        <div class="form-group row">
                            <label class="col-sm-3 control-label">Mobile</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control input-sm" name="mobile" id="mobile">
                            </div>
                        </div>


                      

                        <div class="form-group row">
                            <label class="col-sm-3 control-label">Address</label>
                            <div class="col-sm-9">
                                <textarea class="form-control input-sm" name="address" id="address" rows="3"></textarea>
                            </div>
                        </div>

                       
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Submit</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
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
var currentRowCount = 1;

// Track removed rows for deletion tracking
var deletedRows = [];

function updateTotalQty() {
    var totalQty = 0;
    $('input[name="quantity[]"]').each(function() {
        totalQty += parseFloat($(this).val()) || 0;
    });
    $('#total_item_qty_display').text('Total Item Qty: ' + totalQty);
}

$(document).ready(function () {
    updateTotalQty();
    $(document).on('input change', 'input[name="quantity[]"]', function() { updateTotalQty(); });
    setInterval(updateTotalQty, 500);

    // Ensure dynamic row appending uses the same addNewRow() function
    $('#add_gst_bom').on('click', function (e) {
        e.preventDefault();
        addNewRow();
    });
    
    // Initialize date picker
    var today = new Date();
    var dateString = today.getDate() + '-' + String(today.getMonth() + 1).padStart(2, '0') + '-' + today.getFullYear();
    $('#date').val(dateString);
    
    $('.alldate').datepicker({
        dateFormat: 'dd-mm-yy',
        changeMonth: true,
        changeYear: true,
        maxDate: 0
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

    // Auto-populate customer_code from selected company's c_code
    $('#customer_id').on('change', function() {
        var ccode = $(this).find('option:selected').data('ccode') || '';
        $('#customer_code').val(ccode);
    });
    

    // Remove row functionality (delegated)
    $(document).on('click', '.remove-tr', function() {
        var rowId = $(this).closest('tr').attr('id');
        if(rowId) {
            deletedRows.push(rowId);
        }
        $(this).closest('tr').remove();
        reindexRows();
        updateTotalQty();
    });

    // Parse URL parameter so_no to auto-select and load Sales Order
    var urlParams = new URLSearchParams(window.location.search);
    var soNo = urlParams.get('so_no');
    if (soNo) {
        setTimeout(function() {
            if ($('#so_number_select option[value="' + soNo + '"]').length > 0) {
                $('#so_number_select').val(soNo).trigger('change');
                loadSalesOrderItems();
            }
        }, 500);
    }
});



/**
 * Load Sales Order items via AJAX
 */
function loadSalesOrderItems() {
    var soNumber = $('#so_number_select').val();
    
    if (!soNumber) {
        alert('Please select a Sales Order number first');
        return;
    }
    
    // Show loading spinner on button and table body
    var $btn = $('#load_so_items_btn');
    $btn.html('<i class="fa fa-spinner fa-spin"></i> Loading...').prop('disabled', true);
    $('#so_loading_spinner').show();

    var $tbody = $('#table-body');
    $tbody.html('<tr id="table_loading_row"><td colspan="10" class="text-center" style="padding: 35px; background: #f8fafc;"><i class="fa fa-spinner fa-spin fa-2x" style="color: #2b7bba;"></i><br><br><strong style="font-size: 14px; color: #2b7bba;">Loading Sales Order & BOM Components...</strong><br><span style="color: #64748b; font-size: 12px;">Please wait while the requirements list is fetched...</span></td></tr>');
    
    $.ajax({
        url: '<?php echo base_url(); ?>JobOrderController/get_salesorder_items',
        type: 'POST',
        data: { so_number: soNumber },
        dataType: 'json',
        success: function(response) {
            $('#so_loading_spinner').hide();
            $btn.html('<i class="fa fa-download"></i> Load Items').prop('disabled', false);
            
            // Debug: Log the response to see what's being returned
            console.log('Sales Order Response:', response);
            
            if (response.success) {
                // Store the SO reference
                $('#so_number').val(soNumber);
                
                // Populate header fields
                if (response.customer_id) {
                    $('#customer_id').val(response.customer_id).trigger('change');
                }
                if (response.company_name) {
                    console.log('Company Name from SO: ' + response.company_name);
                    // Auto-select the company from dropdown based on the fetched company name
                    var found = false;
                    $('#customer_id option').each(function() {
                        if ($(this).text().indexOf(response.company_name) !== -1) {
                            $('#customer_id').val($(this).val()).trigger('change');
                            found = true;
                            return false; // Break the loop
                        }
                    });
                    
                    // Display company name in the fetched company display box
                    $('#fetched_company_name').text(response.company_name);
                    $('#fetched_company_display').show();
                }
                if (response.customer_code) {
                    $('#customer_code').val(response.customer_code);
                }
                if (response.status) {
                    $('#status_main').val(response.status);
                }
                if (response.project_code) {
                    var pc = $.trim(response.project_code);
                    if (pc !== '') {
                        if ($('#project_code option[value="' + pc + '"]').length === 0) {
                            $('#project_code').append(new Option(pc, pc));
                        }
                        $('#project_code').val(pc).trigger('change');
                    }
                }
                if (response.project_qty) {
                    $('#project_qty').val(response.project_qty);
                }
                if (response.system) {
                    $('#system').val(response.system);
                }
                if (response.location) {
                    $('#location').val(response.location);
                }
                if (response.capacity) {
                    $('#capacity').val(response.capacity);
                }
                if (response.so_number) {
                    // Store the SO number in both the hidden field and the visible OC/SO Number field
                    $('#so_number').val(response.so_number);
                    $('#oc_number').val(response.so_number);
                } else if (response.oc_number) {
                    $('#oc_number').val(response.oc_number);
                    $('#so_number').val(response.oc_number);
                }
                if (response.note) {
                    $('#note').val(response.note);
                }
                
                // Clear existing rows completely
                var $tbody = $('#table-body');
                $tbody.empty();
                currentRowCount = 0;
                
                // Filter and keep valid BOM items only
                var validBOMItems = (response.bom_items || []).filter(function(item) {
                    var q = (item.quantity !== undefined && item.quantity !== null) ? item.quantity : '';
                    var hasQuantity = String(q).toString().trim() !== '';
                    var hasAnyText = [item.product_name, item.product_code, item.description, item.tag_no, item.scope, item.stores_remark, item.remark]
                        .some(function(v){ return v !== undefined && v !== null && $.trim(String(v)) !== ''; });
                    return hasAnyText || hasQuantity || (item.product_code === '__HEADING__' || item.product_name === '__HEADING__');
                });

                var soItems = response.items || [];

                // Load ONLY BOM items into the main editable table
                if (validBOMItems.length > 0) {
                    for (var i = 0; i < validBOMItems.length; i++) {
                        var item = validBOMItems[i];
                        var isHeading = (item.product_code === '__HEADING__' || item.product_name === '__HEADING__');
                        if (isHeading) {
                            addLoadedHeadingRow(item);
                        } else {
                            addNewRow();
                            loadItemIntoRow(currentRowCount, item);
                        }
                    }
                    updateTotalQty();
                    currentPage = 1;
                    renderPagination();
                } else if (soItems.length > 0) {
                    // No BOM linked — fallback message, keep table empty with one default row
                    addNewRow();
                    alert('No BOM items found for this Sales Order. Please create a BOM linked to this SO first.\n\nSO items are shown in the reference panel above for your reference.');
                } else {
                    // Re-add one default row if no items
                    addNewRow();
                    alert('No items found for this Sales Order.');
                }

                // Populate reference SO items pills
                var $soItemsList = $('#so_items_list');
                $soItemsList.empty();
                
                if (soItems.length > 0) {
                    $.each(soItems, function(idx, item) {
                        // Pills layout
                        var unitStr = item.unit ? ' ' + item.unit : '';
                        var itemHtml = '<div style="background: #fff; border: 1px solid #e9ecef; border-radius: 6px; padding: 6px 12px; display: inline-flex; align-items: center; gap: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.02); margin-right: 5px; margin-bottom: 5px;">' +
                                       '<span style="color: #495057; font-weight: 500;">' + (item.product_name || item.product_code || '') + '</span>' +
                                       '<span class="label label-default" style="background: #e9ecef; color: #495057; border-radius: 4px; padding: 2px 6px; font-size: 11px; font-weight: 600;">Qty: ' + (item.quantity || 0) + unitStr + '</span>' +
                                       '</div>';
                        $soItemsList.append(itemHtml);
                    });
                    $('#so_ref_badge').text(soItems.length + (soItems.length === 1 ? ' Item' : ' Items'));
                }
                
                // Populate reference Drawings pills
                var drawings = response.drawings || [];
                var $drawingsRefList = $('#drawings_ref_list');
                $drawingsRefList.empty();
                
                if (drawings.length > 0) {
                    $.each(drawings, function(idx, d) {
                        var fileLinks = [];
                        if (d.files && d.files.length > 0) {
                            $.each(d.files, function(fidx, f) {
                                var downloadUrl = '<?php echo base_url(); ?>DrawingController/download_file/' + f.file_id;
                                var viewUrl = '<?php echo base_url(); ?>DrawingController/view_file/' + f.file_id;
                                var fName = (f.file_name || 'File');
                                fileLinks.push(
                                    '<span style="font-size: 12px; color: #6c757d; max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="' + fName + '">' + fName + '</span> ' +
                                    '<a href="' + viewUrl + '" class="btn btn-info btn-xs" target="_blank" style="padding: 1px 4px; font-size: 10px; margin-left: 4px;"><i class="fa fa-eye"></i> View</a>' +
                                    '<a href="' + downloadUrl + '" class="btn btn-default btn-xs" target="_blank" style="padding: 1px 4px; font-size: 10px; margin-left: 2px;"><i class="fa fa-download"></i></a>'
                                );
                            });
                        }
                        var filesHtml = fileLinks.length > 0 ? '<div style="display: flex; align-items: center; gap: 4px; border-left: 1px solid #ddd; padding-left: 10px; margin-left: 5px;">' + fileLinks.join('') + '</div>' : '';

                        // Add pill tag
                        var revStr = d.latest_revision ? ' Rev: ' + d.latest_revision : '';
                        var drawHtml = '<div style="background: #fff; border: 1px solid #e9ecef; border-radius: 6px; padding: 6px 12px; display: inline-flex; align-items: center; box-shadow: 0 2px 4px rgba(0,0,0,0.02); margin-right: 5px; margin-bottom: 5px;">' +
                                       '<span style="color: #495057; font-weight: 500;"><i class="fa fa-file-picture-o" style="color: #17a2b8; margin-right: 6px;"></i>' + (d.drawing_name || d.drawing_no || '') + '</span>' +
                                       '<span class="label label-default" style="background: #e9ecef; color: #495057; border-radius: 4px; padding: 2px 6px; font-size: 11px; font-weight: 600; margin-left: 8px;">' + d.drawing_no + revStr + '</span>' +
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
                
                // Show/Hide Reference Container based on content
                if (soItems.length > 0 || drawings.length > 0) {
                    $('#so_items_reference_container').slideDown(300);
                } else {
                    $('#so_items_reference_container').slideUp(200);
                }
            } else {
                alert('Error: ' + (response.message || 'Failed to load Sales Order items'));
            }
        },
        error: function(xhr, status, error) {
            $('#so_loading_spinner').hide();
            $('#load_so_items_btn').html('<i class="fa fa-download"></i> Load Items').prop('disabled', false);
            $('#table-body').empty();
            addNewRow();
            console.error('AJAX Error:', error);
            alert('Error loading Sales Order items. Please try again.');
        }
    });
}

function resetRow1() {
    $('#item_name1').val('');
    $('#description1').val('');
    $('#quantity1').val(1);
    $('#unit1').val('').trigger('change');
    $('#tag_no1').val('');
    $('#scope1').val('');
    $('#stores_remark1').val('');
    $('#remark1').val('');
    
    // Reset any CKEditor instances if needed
    if (CKEDITOR.instances.description1) {
        CKEDITOR.instances.description1.setData('');
    }
    if (CKEDITOR.instances.scope1) {
        CKEDITOR.instances.scope1.setData('');
    }
    if (CKEDITOR.instances.remark1) {
        CKEDITOR.instances.remark1.setData('');
    }
}

function loadItemIntoRow(rowNum, item) {
    var $itemSelect = $('#item_name' + rowNum);
    var $unitSelect = $('#unit' + rowNum);
    
    // Get product code/value - check multiple possible field names from backend
    var productValue = '';
    if (item.product_code) {
        productValue = item.product_code;
    } else if (item.code) {
        productValue = item.code;
    } else if (item.item_code) {
        productValue = item.item_code;
    } else if (item.product_name) {
        productValue = item.product_name;
    } else {
        productValue = '';
    }
    
    // Trim and clean the value
    productValue = $.trim(String(productValue));
    
    // Set product selection
    if (productValue !== '') {
        // Check if option exists, if not, add it temporarily
        if ($itemSelect.find('option[value="' + productValue + '"]').length === 0) {
            $itemSelect.append(new Option(item.product_display_name || item.item_name || productValue, productValue));
        }
        $itemSelect.val(productValue).trigger('change');
    }
    
    // Set hidden product_code field
    $('#product_code' + rowNum).val(productValue);
    
    // Set Description
    var descriptionValue = item.description || item.item_description || '';
    $('#description' + rowNum).val(descriptionValue);
    
    // Set Quantity (ensure it's a number and not empty)
    var qtyValue = item.quantity || item.qty || 1;
    if (qtyValue === '' || qtyValue === null || isNaN(qtyValue)) {
        qtyValue = 1;
    }
    $('#quantity' + rowNum).val(qtyValue);
    
    // Set Unit
    var unitValue = item.unit || item.uom || '';
    if (unitValue !== '') {
        // Check if option exists, if not add it
        if ($unitSelect.find('option[value="' + unitValue + '"]').length === 0) {
            $unitSelect.append(new Option(unitValue, unitValue));
        }
        $unitSelect.val(unitValue).trigger('change');
    }
    
    // Set Tag NO
    $('#tag_no' + rowNum).val(item.tag_no || item.tag || '');
    
    // Set Scope
    $('#scope' + rowNum).val(item.scope || '');
    
    // Set Stores Remark
    var storesRemark = item.stores_remark || item.remark_status || '';
    $('#stores_remark' + rowNum).val(storesRemark);
    
    // Set Price
    var priceValue = item.price || item.rate || '';
    $('#price' + rowNum).val(priceValue);
    
    // Set Remark
    $('#remark' + rowNum).val(item.remark || item.remarks || '');
    
    // Update any CKEditor instances if they exist
    if (typeof CKEDITOR !== 'undefined') {
        if (CKEDITOR.instances['description' + rowNum]) {
            CKEDITOR.instances['description' + rowNum].setData(descriptionValue);
        }
        if (CKEDITOR.instances['scope' + rowNum]) {
            CKEDITOR.instances['scope' + rowNum].setData(item.scope || '');
        }
        if (CKEDITOR.instances['remark' + rowNum]) {
            CKEDITOR.instances['remark' + rowNum].setData(item.remark || '');
        }
    }
    
    // Log for debugging - remove in production
    console.log('Row ' + rowNum + ' loaded with:', {
        product: productValue,
        quantity: qtyValue,
        unit: unitValue,
        tag: item.tag_no,
        price: priceValue
    });
}

function clearSalesOrderSelection() {
    $('#so_number_select').val('');
    $('#so_number').val('');
    $('#customer_id').val('').trigger('change');
    $('#customer_code').val('');
    $('#fetched_company_display').hide();
    $('#project_code').val('');
    $('#project_qty').val('');
    $('#system').val('');
    $('#location').val('');
    $('#capacity').val('');
    $('#oc_number').val('');
    $('#note').val('');
    
    // Clear all rows except first
    var $tbody = $('#table-body');
    $tbody.find('tr:not(#row1)').remove();
    currentRowCount = 1;
    resetRow1();
    updateTotalQty();

    // Reset and hide reference pills container
    $('#so_items_reference_container').slideUp(200);
    $('#so_items_list').empty();
    $('#so_ref_badge').text('0 Items');
    $('#drawings_ref_list').empty();
    $('#drawings_ref_badge').text('0 Drawings');
    $('#drawings_ref_header').hide();
    $('#drawings_ref_list').hide();
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

// Placeholder for myFunction1 - you can implement as needed
function myFunction1(id) {
    // Handle product selection logic
    var selectedValue = $('#' + id).val();

    if (selectedValue === 'NEW') {
        // Redirect to MasterController add product page (then user returns back)
        // This fixes the currently stubbed functionality.
        // Open MasterController product form in a modal/iframe-like flow.
        // The global footer already contains the “productModal” UI used across inventory pages.
        // For JobOrder, we trigger that modal and let user save the item from the same page.
        // IMPORTANT: this sets up the modal to accept “new inventory item”.
        if (typeof window.openProductModalForJobOrder === 'function') {
            window.openProductModalForJobOrder();
        } else {
            // Fallback: open productModal that exists in admin/footer
            if ($('#productModal').length) {
                $('#productModal').modal('show');
            } else {
                // If productModal is not available, fallback to redirect
                window.location.href = base_url + 'MasterController/add_product_form';
            }
        }
    }
}



// Track current row count
var currentRowCount = 1;

// Add new row to the table
function addNewRow() {
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
    $('#table-body').append(newRow);
    $('#item_name' + currentRowCount).select2({
        placeholder: "Select Item",
    });
    $('#unit' + currentRowCount).select2({
        placeholder: "Select Unit",
    });
    updateHeadingAssociations();
    renderPagination();
}

// Delete row from the table
function deleteRow(rowId) {
    $('#' + rowId).remove();
    updateTotalQty();
    updateHeadingAssociations();
    renderPagination();
}

// Update total quantity display
function updateTotalQty() {
    var totalQty = 0;
    $('input[name="quantity[]"]').each(function() {
        var qty = parseInt($(this).val()) || 0;
        totalQty += qty;
    });
    $('#total_item_qty_display').text('Total Item Qty: ' + totalQty);
}

// ===== TABLE PAGINATION LOGIC =====
var currentPage = 1;
var pageSize = 25;

function renderPagination() {
    var $rows = $('#table-body tr').not('#table_loading_row');
    var totalRows = $rows.length;
    
    if (totalRows <= 10) {
        $('#table-pagination-container').hide();
        $rows.show();
        return;
    }
    
    $('#table-pagination-container').css('display', 'flex');
    
    if (pageSize === 'ALL') {
        $rows.show();
        $('#pagination-info').text('Showing all ' + totalRows + ' items');
        $('#page-num-display').text('Page 1 of 1');
        $('#btn-prev-page').prop('disabled', true);
        $('#btn-next-page').prop('disabled', true);
        return;
    }
    
    var size = parseInt(pageSize);
    var totalPages = Math.ceil(totalRows / size) || 1;
    if (currentPage > totalPages) currentPage = totalPages;
    if (currentPage < 1) currentPage = 1;
    
    var start = (currentPage - 1) * size;
    var end = start + size;
    
    $rows.each(function(index) {
        if (index >= start && index < end) {
            $(this).show();
        } else {
            $(this).hide();
        }
    });
    
    var displayStart = totalRows > 0 ? (start + 1) : 0;
    var displayEnd = Math.min(end, totalRows);
    
    $('#pagination-info').text('Showing ' + displayStart + ' to ' + displayEnd + ' of ' + totalRows + ' items');
    $('#page-num-display').text('Page ' + currentPage + ' of ' + totalPages);
    
    $('#btn-prev-page').prop('disabled', currentPage <= 1);
    $('#btn-next-page').prop('disabled', currentPage >= totalPages);
}

function prevPage() {
    if (currentPage > 1) {
        currentPage--;
        renderPagination();
        scrollToTable();
    }
}

function nextPage() {
    var $rows = $('#table-body tr').not('#table_loading_row');
    var totalRows = $rows.length;
    var size = parseInt(pageSize);
    var totalPages = Math.ceil(totalRows / size);
    if (currentPage < totalPages) {
        currentPage++;
        renderPagination();
        scrollToTable();
    }
}

function changePageSize(val) {
    pageSize = val;
    currentPage = 1;
    renderPagination();
}

function scrollToTable() {
    $('html, body').animate({
        scrollTop: $('#table-body').offset().top - 120
    }, 150);
}

// Update total qty on quantity change
$(document).on('change keyup', 'input[name="quantity[]"]', function() {
    updateTotalQty();
});

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

// Add Section Heading Row when loading BOM from Sales Order
function addLoadedHeadingRow(item) {
    currentRowCount++;
    var isMain = (item.tag_no !== 'SUB');
    var headingHtml = '<tr id="row' + currentRowCount + '" class="jo-heading-row">' +
        '<td colspan="9">' +
        '  <input type="hidden" name="product_name[]" value="__HEADING__">' +
        '  <input type="hidden" name="quantity[]" value="0">' +
        '  <input type="hidden" name="unit[]" value="">' +
        '  <select name="tag_no[]" class="heading-type-select form-control input-sm" style="width: auto; display: inline-block; margin-right: 8px;">' +
        '    <option value="MAIN"' + (isMain ? ' selected' : '') + '>Main Heading</option>' +
        '    <option value="SUB"' + (!isMain ? ' selected' : '') + '>Sub Heading</option>' +
        '  </select>' +
        '  <input type="hidden" name="scope[]" value="">' +
        '  <input type="hidden" name="stores_remark[]" value="">' +
        '  <input type="hidden" name="remark[]" value="">' +
        '  <input type="hidden" name="price[]" value="0">' +
        '  <input type="hidden" name="product_code[]" value="__HEADING__">' +
        '  <i class="fa fa-tag" style="margin-right:8px;opacity:0.7;"></i>' +
        '  <input type="text" name="description[]" class="form-control input-sm heading-text-input" placeholder="ENTER SECTION HEADING (E.G. INSTRUMENTS, CIVIL WORKS)..." style="width: 80%; display: inline-block;" value="' + (item.description || '').replace(/"/g, '&quot;') + '">' +
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
    styleHeadingRow($newRow);
    updateHeadingAssociations();
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
    $('#item_name' + currentRowCount).select2({
        placeholder: "Select Item",
    });
    $('#unit' + currentRowCount).select2({
        placeholder: "Select Unit",
    });
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

// Update section path labels and styles dynamically
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

// Remove heading row
$(document).on('click', '.btn_remove', function() {
    $(this).closest('tr').remove();
    updateHeadingAssociations();
    updateTotalQty();
});

</script>
