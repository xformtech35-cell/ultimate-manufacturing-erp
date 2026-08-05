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
        display: inline;
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

    #dynamic_field {
        table-layout: fixed;
        width: 100%;
    }

    #dynamic_field th {
        padding: 12px 8px;
        background-color: #3c8dbc;
        color: #fff;
        font-weight: bold;
        text-align: center;
        border: 1px solid #ddd;
        font-size: 13px;
    }

    #dynamic_field td {
        vertical-align: middle;
        padding: 8px 6px;
        border: 1px solid #ddd;
        text-align: center;
    }

    #dynamic_field td:first-child {
        text-align: left;
    }

    #dynamic_field td:nth-child(2) {
        text-align: center;
    }

    /* Column Widths */
    #dynamic_field th:nth-child(1), #dynamic_field td:nth-child(1) { width: 13%; }  /* Item */
    #dynamic_field th:nth-child(2), #dynamic_field td:nth-child(2) { width: 9%; }  /* Description */
    #dynamic_field th:nth-child(3), #dynamic_field td:nth-child(3) { width: 8%; }  /* HSN Code */
    #dynamic_field th:nth-child(4), #dynamic_field td:nth-child(4) { width: 6%; }   /* QTY */
    #dynamic_field th:nth-child(5), #dynamic_field td:nth-child(5) { width: 8%; }   /* UNIT */
    #dynamic_field th:nth-child(6), #dynamic_field td:nth-child(6) { width: 8%; }   /* GST */
    #dynamic_field th:nth-child(7), #dynamic_field td:nth-child(7) { width: 8%; }  /* IGST */
    #dynamic_field th:nth-child(8), #dynamic_field td:nth-child(8) { width: 8%; }  /* Price */
    #dynamic_field th:nth-child(9), #dynamic_field td:nth-child(9) { width: 0%; }   /* Discount (hidden) */
    #dynamic_field th:nth-child(10), #dynamic_field td:nth-child(10) { width: 12%; } /* Amount */
    #dynamic_field th:nth-child(11), #dynamic_field td:nth-child(11) { width: 18%; } /* Action */

    #dynamic_field .form-control {
        height: 34px;
        padding: 6px 8px;
        font-size: 13px;
        width: 100%;
        border-radius: 3px;
    }

    #dynamic_field .form-control.input-sm {
        height: 32px;
        font-size: 12px;
    }

    #dynamic_field .btn {
        padding: 5px 10px;
        font-size: 12px;
        height: 32px;
        margin: 0;
    }

    #dynamic_field .btn-xs {
        padding: 4px 8px;
        font-size: 11px;
        height: 28px;
        margin: 0 2px;
    }

    .select2-container {
        width: 100% !important;
    }

    .action-header-btn {
        margin-left: 5px;
    }

    /* Action header button styling */
    .action-header-btn {
        margin-left: 5px;
    }

    .hide {
        display: none;
    }

    /* Responsive form adjustment */
    @media (max-width: 768px) {
        .col-sm-4.control-label {
            text-align: left;
        }
        .form-group {
            margin-bottom: 12px;
        }
    }
    
    /* Disabled select styling */
    .disabled-select {
        background-color: #e9ecef;
        color: #6c757d;
        cursor: not-allowed;
        opacity: 0.7;
    }
    
    select:disabled {
        background-color: #e9ecef;
        color: #6c757d;
        cursor: not-allowed;
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
                    IGST Sales Order
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="<?php echo base_url() . 'SalesOrderController/index/' ?>"> Sales Order</a></li>
                    <li class="active"> Sales Order Details</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">

                <div class="row" style="padding:2%">
                    <div class="pull-left">

                        <div class="col-md-2">
                            <!--<a href="<?php //echo base_url();   ?>SalesOrderController/create_salesorder" id="non_gst" name="non_gst" class="btn btn-primary" role="button">Non GST</a>-->
                        </div>
                        <div class="pull-right">
                            <div class="col-md-6">
                                <a href="<?php echo base_url(); ?>SalesOrderController/create_gst_salesorder" id="local_gst" name="local_gst" class="btn btn-primary" role="button">GST</a>
                            </div>

                        </div>
                    </div>
                                                    <a href="javascript:void(0);" onclick="window.history.back()" class="btn btn-primary pull-right"><i class="fa fa-close"></i> Close</a>

                </div>



                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-info">
                            <div class="box-header">
                                <h3 class="box-title">Create IGST Sales Order</h3>

                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">

                               


                                 <form class="form-horizontal form_overlay" name="add_name" id="add_name" method="post" action="<?php echo base_url(); ?>SalesOrderController/add_salesorder_salesorder" enctype="multipart/form-data">
                                     <?php
                                     $month = (int)date('m');
                                     $fy_start = ($month <= 3) ? ((int)date('y') - 1) : (int)date('y');
                                     $fy_end = $fy_start + 1;
                                     $fy_code = sprintf('%02d%02d', $fy_start, $fy_end);

                                     $comp_name = $settings['company_name'] ?? 'UWS';
                                     $words = preg_split('/[\s\-]+/', trim($comp_name));
                                     $company_prefix = strtoupper(substr($words[0] ?? 'UWS', 0, 5));

                                     $row_max = $this->db->query("SELECT MAX(CAST(SUBSTRING_INDEX(number_fk, '-OC-', -1) AS UNSIGNED)) AS max_seq FROM {$this->db->dbprefix}salesorder_total WHERE number_fk LIKE '%-OC-%'")->row();
                                     $max_seq = (int)($row_max->max_seq ?? 0);
                                     $next_so_seq = max($salesorder_id + 1, $max_seq + 1);
                                     ?>
                                     <input type="hidden" id="next_so_seq" value="<?php echo $next_so_seq; ?>">
                                     <input type="hidden" id="so_company_prefix" value="<?php echo $company_prefix; ?>">
                                     <input type="hidden" id="so_financial_year" value="<?php echo $fy_code; ?>">

                                     <div class="row">

                                          <div class="col-md-12">
                                               <div class="form-group row ">
                                                    <input type="hidden" name="salesorder_igst_check" value="igst" id="salesorder_igst_check">
                                                    <label class="col-sm-12 control-label" style="text-align: right;">
                                                        <h2>IGST Sales Order: <b id="so_number_display"></b></h2>
                                                    </label>
                                               </div>
                                          </div>

                                         <div class="col-md-3">
                                            <?php if (in_array('Projects', $this->session->userdata('session_data_head')['permission'] ?? [])): ?>
                                             <div class="form-group row ">
                                                 <label for="project_code" class="col-sm-4 control-label">Project Code</label>
                                                 <div class="col-sm-8">
                                                     <select class="form-control input-sm select2 company_search_name" name="project_code" id="project_code">
                                                         <option value="">Select project code</option>
                                                         <?php if (isset($project_code_result) && !empty($project_code_result)) {
                                                             foreach ($project_code_result as $key) { ?>
                                                                 <option value="<?php echo htmlspecialchars($key->project_code); ?>"><?php echo htmlspecialchars($key->project_code); ?></option>
                                                             <?php }
                                                         } ?>
                                                     </select>
                                                 </div>
                                             </div>
                                             <?php else: ?>
                                                 <input type="hidden" name="project_code" id="project_code" value="">
                                             <?php endif; ?>

                                             <div class="form-group row">
                                                 <label for="inputEmail3" class="col-sm-4 control-label">Company<span style="color: red;">*</span></label>
                                                 <div class="col-sm-8">
                                                     <select class="form-control input-sm select2 company_search_name" required name="customer_id" id="customer_id">
                                                         <option value="">Select Company</option>
                                                         <?php foreach ($company_name as $key) { ?>
                                                             <option value="<?php echo $key->customer_id; ?>" data-fullname="<?php echo htmlspecialchars($key->fullname); ?>"><?php echo $key->company_name . " - " . $key->c_code; ?> - ( <?php echo $key->state_code; ?> )</option>
                                                         <?php } ?>
                                                     </select>
                                                 </div>
                                             </div>
                                             <div class="form-group row">
                                                 <div class="col-sm-4"></div>
                                                 <div class="col-sm-6">
                                                     <span class="btn btn-success btn-sm btn-block" data-toggle="modal" data-target="#myModal"><i class="glyphicon glyphicon-plus"></i>Add Company</span>
                                                 </div>
                                             </div>

                                              <input type="hidden" name="customer_name" id="customer_name">

                                            <div class="form-group row ">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Enquiry</label>
                                                <div class="col-sm-8">
                                                    <select class="form-control input-sm" name="enquiry" id="enquiry">
                                                        <option value="1">Mail</option>
                                                        <option value="2">Verbal</option>
                                                        <option value="3">Just Dial</option>
                                                        <option value="4">India Mart</option>
                                                    </select>
                                                </div>

                                            </div>

                                              <div class="form-group row ">
                                                <label for="inputEmail3" class="col-sm-4 control-label"> PO Date<span style="color: red;"></span></label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control alldate input-sm created-date" name="po_date" id="po_date" onkeydown="return false;">
                                                </div>
                                            </div>


                                            
                                              
                                          
                                            
                                          
                                            
                                            
                                            <div class="form-group row hide">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Customer Code</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control  input-sm" name="customer_code" id="customer_code">
                                                </div>
                                            </div>

                                        </div>
                                        <div class="col-md-3">

                                            <div class="form-group row required">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Date</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control alldate input-sm created-date" name="date" id="date" required="" onkeydown="return false;">
                                                </div>
                                            </div>

                                            <div class="form-group row required">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Delivery Date</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control currentDateWithSevendays input-sm payment-due-date-check" name="expires_date" id="expires_date" required="" onkeydown="return false;">
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Status</label>
                                                <div class="col-sm-8">
                                                    <select class="form-control input-sm "  name="status" id="status">
                                                        <option value="1">Draft</option>
                                                        <option value="2">Sent</option>
                                                        <option value="3">Viewed</option>
                                                        <option value="4">Approved</option> 
                                                        <option value="5">Rejected</option>
                                                        <option value="6">Canceled</option>
                                                    </select>
                                                </div>
                                            </div>


                                         <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label"> PO Attachment</label>
                                                <div class="col-sm-8">
                                                    <input type="file" class="form-control input-sm" name="attachment" id="attachment" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.txt">
                                                </div>
                                                <small class="text-muted col-sm-8 col-sm-offset-4">Supported formats: PDF, DOC, DOCX, XLS, XLSX, JPG, JPEG, PNG, TXT</small>
                                            </div>

                                            
                                         
                                
                                            
                                        </div>

                                         <div class="col-md-6">
                                              <div class="form-group row">
                                                 <label for="number" class="col-sm-4 control-label">SO Number<span style="color: red;">*</span></label>
                                                 <div class="col-sm-8">
                                                     <input type="text" class="form-control input-sm" name="number" id="number" required="" placeholder="e.g. UWS-2526-DS-PIL-OC-242" oninput="this.value=this.value.toUpperCase()" autocomplete="off">
                                                 </div>
                                             </div>
                                                       <div class="form-group row">
                                     <!-- <label for="inputEmail3" class="col-sm-4 control-label">Transportation </label>
                                     <div class="col-sm-8">
                                         <select class="form-control input-sm "  name="transportation" id="transportation" >
                                         <option value="">Select transportation</option>
                                         <option value="CS">Customer Scope</option>
                                         <option value="OS">Our Scope</option>
                                     </select>
                                 </div> -->
                                </div>
                                             
                                             
                                 <!-- <div class="form-group row">
                                     <label for="inputEmail3" class="col-sm-4 control-label">Installation </label>
                                     <div class="col-sm-8">
                                         <select class="form-control input-sm "  name="installation" id="installation">
                                         <option value="">Select installation</option>
                                         <option value="CS">Customer Scope</option>
                                         <option value="OS">Our Scope</option>
                                         <option value="OSV">Only Supervision</option>
                                     </select>
                                 </div>
                                 
                                 </div>   -->

                                 <div class="form-group row">
                                                  <label for="system" class="col-sm-4 control-label">System<span style="color: red;">*</span></label>
                                                  <div class="col-sm-8">
                                                      <input type="text" class="form-control input-sm" name="system" id="system" required>
                                                  </div>
                                              </div>
                                                                                         <div class="form-group row ">
                                                 <label for="inputEmail3" class="col-sm-4 control-label">PO Number</label>
                                                 <div class="col-sm-8">
                                                     <input type="text" class="form-control  input-sm" name="po_number" id="po_number">
                                                 </div>
                                             </div>
                                             

  <div class="form-group row">
                                                 <label for="inputEmail3" class="col-sm-4 control-label">PO Status</label>
                                                 <div class="col-sm-8">
                                                     <select class="form-control input-sm " name="po_status" id="po_status">
                                                         <option value="open" selected>Open</option>
                                                         <option value="close">Close</option>
                                                     </select>
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
                                                 <label for="project_qty" class="col-sm-4 control-label">Project Quantity</label>
                                                 <div class="col-sm-8">
                                                     <input type="text" class="form-control input-sm" name="project_qty" id="project_qty">
                                                 </div>
                                             </div>

                                            

                                             <div class="form-group row hide">
                                                 <label for="inputEmail3" id="subheading1" class="col-sm-4 control-label">Subheading</label>
                                                 <div class="col-sm-8">
                                                     <input type="text" value="<?php echo $settings['so_subheading']; ?>" class="form-control" name="salesorder_subheading" id="salesorder_subheading">
                                                 </div>
                                             </div>

                                             <div class="form-group row hide">
                                                 <label for="inputEmail3" class="col-sm-4 control-label">Footer</label>
                                                 <div class="col-sm-8">
                                                     <textarea class="form-control" name="salesorder_footer" id="salesorder_footer" rows="3"><?php echo $settings['so_footer']; ?></textarea>
                                                 </div>
                                             </div>
                                                <div class="form-group row">
                                   
                                    
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
                                                     <th>UNIT</th>
                                                     <th>GST</th>
                                                     <th>IGST</th>
                                                     <th>Price</th>
                                                     <th class="hide">Discount(%)</th>
                                                     <th>Amount</th>
                                                     <th width="12%">Action</th>
                                                 </tr>
                                             </thead>
                                             <tbody id="table-body">
                                                 <tr id="row1">
                                                     <td>
                                                         <select class="form-control input-sm product_name_auto item_search_name name_list" name="product_name[]" id="item_name1" onchange="myFunction1(this.id)" required="" data-live-search="true" style="max-width: 100%;">
                                                             <option></option>
                                                             <option value="NEW">+ Add New Product</option>
                                                             <?php foreach ($item_name as $key) { ?>
                                                                 <option value="<?php echo $key->code; ?>"><?php echo $key->code . " - " . $key->item_name; ?></option>
                                                             <?php } ?>
                                                         </select>
                                                     </td>
                                                     <td>
                                                         <button type="button" class="btn btn-info btn-xs" onclick="descButton(this.id)" id="btnDescriptionId1" title="Edit Description">Description</button>
                                                         <textarea style="width: 150px;" class="form-control input-sm name_list description_auto hide" name="description[]" id="description1" rows="4"></textarea>
                                                     </td>
                                                     <td><input type="text" name="hsn[]" id="hsn1" required="" readonly class="form-control input-sm required_list name_list" /></td>
                                                     <td><input type="text" name="quantity[]" id="quantity1" required="" class="form-control input-sm required_list name_list quantity_auto number-only-validation" value="1" /></td>
                                                     <td>
                                                         <select class="form-control input-sm item_search_unit" name="unit[]" id="unit1" required="" data-live-search="true">
                                                             <option></option>
                                                         </select>
                                                     </td>
                                                     <td><input type="text" readonly="" name="gst_per[]" id="gst_per1" class="form-control input-sm name_list" /></td>
                                                     <td><input type="text" name="igst[]" readonly="" id="igst1" class="form-control input-sm igst_list" /></td>
                                                     <td><input type="number" step="any" name="price[]" id="price1" required="" class="form-control input-sm required_list name_list price_auto" value="0.00" /></td>
                                                     <td class="hide"><input type="number" min="0" maxlength="2" name="discount[]" id="discount1" class="form-control input-sm name_list discount_auto number-only-validation" value="" /></td>
                                                     <td><input type="hidden" name="amount[]" id="amount1" class="form-control input-sm name_list amount_auto" value="0.00" />
                                                         <input type="hidden" name="amount_temp[]" id="amount_temp1" value="0.00" />
                                                         <input type="hidden" name="gst_amount[]" id="gst_amount1" class="form-control input-sm name_list gst_amount_auto" value="0.00" />
                                                         <span id="span_amount1" name="span_amount[]">₹0.00</span>
                                                     </td>
                                                     <td style="white-space: nowrap; vertical-align: middle; text-align: center;">
                                                          <input type="hidden" name="tag_no[]" id="so_tag_no1" value="">
                                                          <button type="button" class="btn btn-success btn-xs insert-so-row-below" title="Insert Row Below" style="padding: 2px 7px;"><i class="fa fa-plus"></i></button>
                                                          <button type="button" class="btn btn-danger btn-xs btn-remove-so-row" title="Delete Row" style="padding: 2px 7px;"><i class="fa fa-times"></i></button>
                                                      </td>
                                                 </tr>
                                             </tbody>
                                        </table>  
                                        <div align="center">

                                            <button type="submit" name="submit" id="submit"  class="btn btn-success">Save</button>
                                        </div>
                                        <div align="right" style="margin: 10px">

<!--                                            Discount: <input type="text" name="discount"  id="discount" size="3" value="0" />%<br>-->
                                            <input type="hidden" name="temp_total" id="temp_total" class="form-control input-sm temp_total" value="0.00" /><br>
                                            <span id="total_item_qty_display" style="font-weight: bold; color: #333;">Total Item Qty: 0</span><br>
                                            <span id="total_amount" name="total_amount"> Total before Tax:</span><br>

                                            <input type="hidden" name="basic_total" id="basic_total" class="form-control input-sm basic_total" value="0.00" />
                                            <span id="igst_amount" class="hide_igst" name="igst_amount[]">Total IGST Amount: ₹0.00</span><br>
                                            <b> <span id="grand_total_amount1" name="grand_total_amount1[]">Grand Total: ₹0.00</span></b><br>
                                            <span id="grand_total_words_igst" style="font-weight: bold; color: #555;">Grand Total in Words: Zero Rupees Only</span>
                                            <span class="hide" id="total_gst_amount" name="total_gst_amount[]">Total GST Amount: ₹0.00</span><br>

                                            <input type="hidden" name="total_salesorder_amount" id="total_quotation_amount" class="form-control input-sm name_list" value="0.00" />
                                        </div>
                                    </div>  
                                    
                                    <div class="row">
                                        <div class="col-xs-12 col-sm-6">
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-3 control-label">Terms & Conditions</label>
                                                <div class="col-sm-9">
                                                    <textarea class="form-control" name="terms_and_conditions" id="terms_and_conditions" rows="3"><?php echo $settings['so_terms_and_conditions']; ?></textarea>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-3 control-label">Payment Terms</label>
                                                <div class="col-sm-9">
                                                    <textarea class="form-control" name="payment_terms" id="payment_terms" rows="3"><?php echo $settings['so_payment_terms']; ?></textarea>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-3 control-label">Process Schedule</label>
                                                <div class="col-sm-9">
                                                    <textarea class="form-control" name="process_schedule" id="process_schedule" rows="3"><?php echo $settings['so_process_schedule']; ?></textarea>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-xs-12 col-sm-6">
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-3 control-label">Taxes</label>
                                                <div class="col-sm-9">
                                                    <textarea class="form-control" name="taxes" id="taxes" rows="3"><?php echo $settings['so_taxes']; ?></textarea>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-3 control-label">Exclusions</label>
                                                <div class="col-sm-9">
                                                    <textarea class="form-control" name="exclusions" id="exclusions" rows="3"><?php echo $settings['so_exclusions']; ?></textarea>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-3 control-label">Note</label>
                                                <div class="col-sm-9">
                                                    <textarea class="form-control" name="salesorder_memo" id="salesorder_memo" rows="3"><?php echo $settings['so_memo']; ?></textarea>
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

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header bg-primary">

                    <center>
                        <h4 class="modal-title">Add Company
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </h4>
                    </center>

                </div>
                <!--<form class="form-horizontal form_overlay" method="post" action="<?php echo base_url(); ?>SalesOrderController/add_customer" enctype="multipart/form-data">-->
                    <div class="modal-body">

                        <div class="card-body ">

                            <!-- form start -->
                            <div class="form-group row required">
                                <label for="inputEmail3" class="col-sm-4 control-label">Company  Name</label>
                                <div class="col-sm-7">
                                    <input type="hidden" name="gst_check_customer" value="gst_check_customer" id="gst_check_customer">
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
                                <label for="inputEmail3" class="col-sm-4 control-label"> State Code</label>
                                <div class="col-sm-7">
                                    <input type="number" class="form-control " name="state_code" id="state_code" >
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label"> Email</label>
                                <div class="col-sm-7">
                                    <input type="text" class="form-control " name="email" id="email" pattern="^([0-9a-zA-Z]([-.\w]*[0-9a-zA-Z])*@([0-9a-zA-Z][-\w]*[0-9a-zA-Z]\.)+[a-zA-Z]{2,9})$" />
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label"> Mobile</label>
                                <div class="col-sm-7">                                            
                                    <input type="text" class="form-control " name="mobile" id="mobile" maxlength="10" onkeyup="if (/\D/g.test(this.value))
                                                               this.value = this.value.replace(/\D/g, '')"  />                                             
                                </div>
                            </div>

                          
                            
                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label"> Address</label>
                                <div class="col-sm-7">
<!--                                    <input type="text" class="form-control " name="address" id="address" >-->
                                    <textarea class="form-control input-sm" name="address" id="address"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" id="btnSave"  class="btn btn-success performa_submit">Submit</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="glyphicon glyphicon-remove"></i> Close</button>
                    </div>
                <!--</form>-->
            </div>
        </div>
    </div>
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

// Initialize CKEditor for other fields
$(document).ready(function() {
    // Destroy any existing instances first
    for (var name in CKEDITOR.instances) {
        CKEDITOR.instances[name].destroy(true);
    }
    
    // Reinitialize
    CKEDITOR.replace('terms_and_conditions');
    CKEDITOR.replace('payment_terms');
    CKEDITOR.replace('process_schedule');
    CKEDITOR.replace('taxes');
    CKEDITOR.replace('exclusions');
    CKEDITOR.replace('salesorder_memo');
    
    // Prevent event bubbling on description buttons
    $(document).off('click', '[id^="btnDescriptionId"]').on('click', '[id^="btnDescriptionId"]', function(e) {
        e.preventDefault();
        e.stopPropagation();
        descButton(this.id);
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
});

// Also fix the static modal to prevent conflicts
$(document).ready(function() {
    $('#myModal').on('show.bs.modal', function() {
        // If description modal is open, close it
        if ($('#descriptionModal').is(':visible')) {
            $('#descriptionModal').modal('hide');
        }
    });
});

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

// Function to calculate and display total before tax
function updateTotalBeforeTax() {
    let total = 0;
    let totalQty = 0;
    
    // Sum row amounts using quantity * price or input[name="amount[]"] / input[name="amount_temp[]"]
    $('#dynamic_field tbody tr, #table-body tr').each(function() {
        let $row = $(this);
        let qty = parseFloat($row.find('input[name="quantity[]"]').val()) || 0;
        let price = parseFloat($row.find('input[name="price[]"]').val()) || 0;
        let amtInput = parseFloat($row.find('input[name="amount[]"]').val()) || 0;
        let amtTempInput = parseFloat($row.find('input[name="amount_temp[]"]').val()) || 0;

        let amount = (qty * price);
        if (amount === 0 && amtInput > 0) amount = amtInput;
        if (amount === 0 && amtTempInput > 0) amount = amtTempInput;
        
        // Update row amount inputs & display span if row has valid product/price
        if (price > 0 || qty > 0) {
            $row.find('input[name="amount[]"]').val(amount.toFixed(2));
            $row.find('input[name="amount_temp[]"]').val(amount.toFixed(2));
            $row.find('span[id^="span_amount"]').text('₹' + amount.toFixed(2));
        }

        total += amount;
        totalQty += qty;
    });

    // Update the display with formatted currency
    $('#total_item_qty_display').text('Total Item Qty: ' + totalQty);
    $('#total_amount').text('Total before Tax: ₹' + total.toFixed(2));
    $('#basic_total').val(total.toFixed(2));
    $('#temp_total').val(total.toFixed(2));
    
    // Trigger other calculations
    updateIGSTAmount();
    updateGrandTotalIGST();
}

// Function to calculate and display IGST amount
function updateIGSTAmount() {
    let igstTotal = 0;
    
    // Sum all igst[] values
    $('input[name="igst[]"]').each(function() {
        let igstValue = parseFloat($(this).val()) || 0;
        igstTotal += igstValue;
    });
    
    // Update the display with formatted currency
    $('#igst_amount').text('Total IGST Amount: ₹' + igstTotal.toFixed(2));
}

// Function to calculate and display grand total for IGST
function updateGrandTotalIGST() {
    let basicTotal = parseFloat($('#basic_total').val()) || 0;
    let igstTotal = 0;
    
    // Sum IGST values
    $('input[name="igst[]"]').each(function() {
        igstTotal += parseFloat($(this).val()) || 0;
    });
    
    // Calculate grand total
    let grandTotal = basicTotal + igstTotal;
    
    // Update the display with formatted currency
    $('#grand_total_amount1').text('Grand Total: ₹' + grandTotal.toFixed(2));
    $('#total_quotation_amount').val(grandTotal.toFixed(2));
    
    // Convert to words and display
    let grandTotalAmount = Math.floor(grandTotal);
    let grandTotalWords = numberToWords(grandTotalAmount);
    $('#grand_total_words_igst').text('Grand Total in Words: ' + grandTotalWords);
}

// Initialize on document ready and monitor for changes
$(document).ready(function() {
    // Update on page load
    updateTotalBeforeTax();
    
    // Update every time quantity, price, amount, or igst values change
    $(document).on('change input', 'input[name="quantity[]"], input[name="price[]"], input[name="amount[]"], input[name="amount_temp[]"], input[name="igst[]"]', function() {
        updateTotalBeforeTax();
    });
    
    // Update when rows are added or removed (every 500ms check)
    setInterval(updateTotalBeforeTax, 500);

    // Function to parse query parameters
    function getUrlParameter(name) {
        name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
        var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
        var results = regex.exec(location.search);
        return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
    }
    
    $('#customer_id').on('change', function(e, isAutoTriggered) {
        var fullname = $(this).find('option:selected').data('fullname') || '';
        $('#customer_name').val(fullname);

        if (!isAutoTriggered) {
            var selectedText = $(this).find('option:selected').text();
            if (selectedText) {
                var parts = selectedText.split(' - ');
                if (parts.length >= 2) {
                    var companyName = parts[0].trim();
                    var clean = companyName.replace(/[^a-zA-Z0-9\s]/g, '');
                    var words = clean.trim().split(/\s+/);
                    var initials = '';
                    for (var k = 0; k < words.length; k++) {
                        if (words[k]) {
                            initials += words[k].charAt(0).toUpperCase();
                        }
                    }
                    $('#customer_code').val(initials).trigger('change');
                }
            }
        }

        if (isAutoTriggered) {
            return;
        }
        var customerId = $(this).val();
        var selectedText = $(this).find('option:selected').text();
        var companyStateCode = (selectedText.match(/\(\s*(\d+)\s*\)/) || [])[1];

        setupCompanySelectValidation(
            customerId,
            companyStateCode,
            <?php echo json_encode($settings['state_code']); ?>,
            '<?php echo base_url(); ?>SalesOrderController/create_gst_salesorder',
            '<?php echo base_url(); ?>SalesOrderController/create_igst_salesorder',
            'igst'
        );
    });

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
                    $('#customer_code').val(response.client_code);
                    if (response.customer) {
                        $('#customer_id').val(response.customer.customer_id).trigger('change', [true]);
                        $('#customer_name').val(response.customer.fullname || '');
                    } else {
                        $('#customer_name').val('');
                    }
                    if (response.project) {
                        $('#system').val(response.project.system || '').trigger('change');
                    }
                    if (response.next_seq) {
                        $('#next_so_seq').val(response.next_seq);
                    }
                    updateSalesOrderNumber();
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
    }

    // Dynamic SO Number Prefill Logic
    $('#system').on('input change', function() {
        updateSalesOrderNumber();
    });

    $('#customer_id').on('change', function() {
        updateSalesOrderNumber();
    });

    $('#customer_code').on('change input', function() {
        updateSalesOrderNumber();
    });

    $('#date').on('change', function() {
        updateSalesOrderNumber();
    });

    $('#number').on('input', function() {
        if ($(this).val().trim() === '') {
            isManualInput = false;
            updateSalesOrderNumber();
        } else {
            isManualInput = true;
        }
    });

    // Run initial prefill on load
    updateSalesOrderNumber();
});

function extractSystemCode(systemName) {
    if (!systemName) return 'XX';
    var cleaned = systemName.replace(/[^a-zA-Z0-9\s]/g, ' ');
    var words = cleaned.trim().split(/\s+/);
    if (words.length === 1) {
        var word = words[0];
        if (word.length >= 2) {
            return word.substring(0, 2).toUpperCase();
        }
        return word.toUpperCase() || 'XX';
    } else {
        var code = words.map(function(w) { return w.charAt(0).toUpperCase(); }).join('').substring(0, 4);
        return code || 'XX';
    }
}

function getFinancialYearFromDate() {
    var dateVal = $('#date').val() || '';
    if (dateVal) {
        var parts = dateVal.split('-');
        if (parts.length === 3) {
            var d = parseInt(parts[0], 10);
            var m = parseInt(parts[1], 10);
            var y = parseInt(parts[2], 10);
            if (y > 2000) y = y - 2000;
            var fy_start, fy_end;
            if (m <= 3) {
                fy_start = y - 1;
                fy_end = y;
            } else {
                fy_start = y;
                fy_end = y + 1;
            }
            return String(fy_start).padStart(2, '0') + String(fy_end).padStart(2, '0');
        }
    }
    return $('#so_financial_year').val() || '2526';
}

var isManualInput = false;

function updateSalesOrderNumber() {
    if (isManualInput) {
        return;
    }
    var prefix = $('#so_company_prefix').val() || 'UWS';
    var year = getFinancialYearFromDate();
    var systemVal = $('#system').val() || '';
    var systemCode = extractSystemCode(systemVal);
    
    var clientCode = ($('#customer_code').val() || '').trim().toUpperCase();
    if (!clientCode || clientCode === 'XXX' || /^\d+$/.test(clientCode) || /[\(\)]/.test(clientCode)) {
        var selectedText = $('#customer_id').find('option:selected').text();
        if (selectedText) {
            var parts = selectedText.split(' - ');
            if (parts.length >= 1) {
                var companyName = parts[0].trim();
                var clean = companyName.replace(/[^a-zA-Z0-9\s]/g, '');
                var words = clean.trim().split(/\s+/);
                var initials = '';
                for (var k = 0; k < words.length; k++) {
                    if (words[k]) {
                        initials += words[k].charAt(0).toUpperCase();
                    }
                }
                clientCode = initials || 'XXX';
            }
        }
    }
    if (!clientCode) {
        clientCode = 'XXX';
    }
    
    var seq = $('#next_so_seq').val() || '1';

    var soNum = prefix + '-' + year + '-' + systemCode + '-' + clientCode + '-OC-' + seq;
    $('#number').val(soNum);
    $('#so_number_display').text(soNum);
}

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
        '  <i class="fa fa-tag" style="margin-right:8px;opacity:0.7;"></i>' +
        '  <input type="text" name="description[]" class="form-control input-sm heading-text-input" placeholder="ENTER SECTION HEADING (E.G. INSTRUMENTS, CIVIL WORKS)..." style="width: 80%; display: inline-block;">' +
        '</td>' +
        '<td style="white-space: nowrap; vertical-align: middle; text-align: center;">' +
        '  <input type="hidden" name="tag_no[]" id="so_tag_no' + rowCount + '" value="">' +
        '  <button type="button" class="btn btn-success btn-xs insert-so-row-below" title="Insert Row Below" style="padding: 2px 7px;"><i class="fa fa-plus"></i></button>' +
        '  <button type="button" name="remove" title="Delete Row" id="remove' + rowCount + '" class="btn btn-danger btn-xs btn-remove-so-row" style="padding: 2px 7px;"><i class="fa fa-times"></i></button>' +
        '</td>' +
        '</tr>';
    $('#dynamic_field').append(headingHtml);

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

// Insert standard row after specific row (by triggering click and moving)
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
    if (typeof calculateSum1 === 'function') {
        calculateSum1();
    }
    updateHeadingAssociations();
});

$(document).ready(function() {
    updateHeadingAssociations();
});
</script>