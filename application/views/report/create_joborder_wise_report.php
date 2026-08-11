<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (isset($session_data_head1)) {
    
} else {
    header($this->config->item('header'));
}
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<style>
    .joborder-details {
        margin-top: 20px;
    }
    .joborder-header {
        background-color: #f5f5f5;
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 20px;
    }
    .section-title {
        background-color: #31a3dd;
        color: white;
        padding: 8px 15px;
        margin: 15px 0;
        border-radius: 3px;
        font-weight: bold;
    }
    .info-table {
        width: 100%;
        margin-bottom: 15px;
    }
    .info-table td {
        padding: 8px;
        border: 1px solid #ddd;
    }
    .info-table td:first-child {
        font-weight: bold;
        width: 30%;
        background-color: #f9f9f9;
    }
    .items-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }
    .items-table th,
    .items-table td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: left;
    }
    .items-table th {
        background-color: #31a3dd;
        color: white;
        font-weight: bold;
    }
    .items-table tr:nth-child(even) {
        background-color: #f9f9f9;
    }
    .status-badge {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 3px;
        font-size: 12px;
        font-weight: bold;
    }
    .status-draft { background-color: #f0ad4e; color: #fff; }
    .status-sent { background-color: #5bc0de; color: #fff; }
    .status-viewed { background-color: #5bc0de; color: #fff; }
    .status-approved { background-color: #5cb85c; color: #fff; }
    .status-rejected { background-color: #d9534f; color: #fff; }
    .status-canceled { background-color: #777; color: #fff; }
    .btn-action {
        margin-right: 5px;
    }
    .print-btn {
        margin-bottom: 15px;
    }
    @media print {
        .no-print {
            display: none;
        }
        .print-btn {
            display: none;
        }
        .main-footer {
            display: none;
        }
    }
</style>

<body class="hold-transition skin-blue sidebar-mini">
    <div id="loader" class="center"></div> 
    <div class="wrapper">
        <div class="content-wrapper">
            <!-- Content Header -->
            <section class="content-header">
                <h1>
                    Job Order Wise Report
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i>Home</a></li>
                    <li><a href="#">Report</a></li>
                    <li class="active">Job Order Wise Report</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="box box-info">
                            <div class="box-header with-border">
                                <h3 class="box-title">Job Order Wise Report</h3>
                            </div>
                            <!-- /.box-header -->
                            
                            <!-- Form to select Job Order -->
                            <form class="form-horizontal" method="post" action="<?php echo base_url(); ?>ReportController/get_joborder_wise_report" id="joborder_form">
                                <div class="box-body">
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Select Job Order<span style="color: red;">*</span></label>
                                        <div class="col-sm-5">
                                            <select class="form-control select2" name="joborder_id" id="joborder_id" required style="width: 100%;">
                                                <option value="">-- Select Job Order --</option>
                                                <?php if(isset($joborder_list) && !empty($joborder_list)): ?>
                                                    <?php foreach($joborder_list as $jo): ?>
                                                        <?php $joborder_row_id = isset($jo->joborder_id) ? $jo->joborder_id : (isset($jo->id) ? $jo->id : ''); ?>
                                                        <option value="<?php echo $joborder_row_id; ?>" <?php echo (isset($selected_joborder_id) && $selected_joborder_id == $joborder_row_id) ? 'selected' : ''; ?>>
                                                            <?php echo $jo->number_fk . " - " . date("d-m-Y", strtotime($jo->date)); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="box-footer text-center">
                                    <button type="button" class="btn btn-default" onclick="window.history.back()">Cancel</button>
                                    <button type="submit" name="export_type" value="pdf" class="btn btn-success"><i class="fa fa-file-pdf-o"></i> Generate PDF</button>
                                    <button type="submit" name="export_type" value="excel" class="btn btn-primary"><i class="fa fa-file-excel-o"></i> Export to Excel</button>
                                    <?php if(isset($joborder) && !empty($joborder)): ?>
                                        <button type="button" class="btn btn-info" onclick="window.print();"><i class="fa fa-print"></i> Print</button>
                                    <?php endif; ?>
                                </div>
                            </form>
                            
                            <?php if(isset($joborder) && !empty($joborder)): ?>
                            <!-- Job Order Details Display -->
                            <div class="box-body joborder-details" id="print_area">
                                <!-- Header -->
                                <div class="text-center" style="margin-bottom: 20px;">
                                    <h2 style="margin: 0; padding: 0;">JOB ORDER REPORT</h2>
                                    <h4 style="margin: 5px 0; color: #666;"><?php echo $joborder->number_fk; ?></h4>
                                    <hr>
                                </div>
                                
                                <!-- Job Order Information -->
                                <div class="section-title">Job Order Information</div>
                                <table class="info-table">
                                    <tr>
                                        <td>Job Order Number</td>
                                        <td><?php echo $joborder->number_fk; ?></td>
                                        <td>Date</td>
                                        <td><?php echo !empty($joborder->date) && $joborder->date != '0000-00-00' ? date("d-m-Y", strtotime($joborder->date)) : ''; ?></td>
                                    </tr>
                                    <tr>
                                        <td>Company Name</td>
                                        <td><?php echo isset($joborder->company_name) ? $joborder->company_name : ''; ?></td>
                                        <td>Project Code</td>
                                        <td><?php echo isset($joborder->project_code) ? $joborder->project_code : ''; ?></td>
                                    </tr>
                                    <tr>
                                        <td>Customer Code</td>
                                        <td><?php echo isset($joborder->customer_code) ? $joborder->customer_code : ''; ?></td>
                                        <td>Project Quantity</td>
                                        <td><?php echo isset($joborder->project_qty) ? $joborder->project_qty : ''; ?></td>
                                    </tr>
                                    <tr>
                                        <td>System</td>
                                        <td><?php echo isset($joborder->system) ? $joborder->system : ''; ?></td>
                                        <td>Location</td>
                                        <td><?php echo isset($joborder->location) ? $joborder->location : ''; ?></td>
                                    </tr>
                                    <tr>
                                        <td>Capacity</td>
                                        <td><?php echo isset($joborder->capacity) ? $joborder->capacity : ''; ?></td>
                                        <td>OC Number</td>
                                        <td><?php echo isset($joborder->oc_number) ? $joborder->oc_number : ''; ?></td>
                                    </tr>
                                    <tr>
                                        <td>Status</td>
                                        <td colspan="3">
                                            <?php
                                            $status_label = 'Unknown';
                                            $status_class = '';
                                            if ($joborder->status == 1) {
                                                $status_label = 'Draft';
                                                $status_class = 'status-draft';
                                            } elseif ($joborder->status == 2) {
                                                $status_label = 'Sent';
                                                $status_class = 'status-sent';
                                            } elseif ($joborder->status == 3) {
                                                $status_label = 'Viewed';
                                                $status_class = 'status-viewed';
                                            } elseif ($joborder->status == 4) {
                                                $status_label = 'Approved';
                                                $status_class = 'status-approved';
                                            } elseif ($joborder->status == 5) {
                                                $status_label = 'Rejected';
                                                $status_class = 'status-rejected';
                                            } elseif ($joborder->status == 6) {
                                                $status_label = 'Canceled';
                                                $status_class = 'status-canceled';
                                            }
                                            ?>
                                            <span class="status-badge <?php echo $status_class; ?>"><?php echo $status_label; ?></span>
                                        </td>
                                    </tr>
                                    <?php if(!empty($joborder->note)): ?>
                                    <tr>
                                        <td>Note</td>
                                        <td colspan="3"><?php echo nl2br($joborder->note); ?></td>
                                    </tr>
                                    <?php endif; ?>
                                </table>
                                
                                <!-- Customer Details -->
                                <div class="section-title">Customer Details</div>
                                <table class="info-table">
                                    <tr>
                                        <td>Full Name</td>
                                        <td><?php echo isset($joborder->fullname) ? $joborder->fullname : ''; ?></td>
                                        <td>Email</td>
                                        <td><?php echo isset($joborder->email) ? $joborder->email : ''; ?></td>
                                    </tr>
                                    <tr>
                                        <td>Mobile</td>
                                        <td><?php echo isset($joborder->mobile) ? $joborder->mobile : ''; ?></td>
                                        <td>GST No</td>
                                        <td><?php echo isset($joborder->gst) ? $joborder->gst : ''; ?></td>
                                    </tr>
                                    <tr>
                                        <td>PAN Card</td>
                                        <td><?php echo isset($joborder->pancard) ? $joborder->pancard : ''; ?></td>
                                        <td>State Code</td>
                                        <td><?php echo isset($joborder->state_code) ? $joborder->state_code : ''; ?></td>
                                    </tr>
                                    <?php if(!empty($joborder->address)): ?>
                                    <tr>
                                        <td>Address</td>
                                        <td colspan="3"><?php echo nl2br($joborder->address); ?></td>
                                    </tr>
                                    <?php endif; ?>
                                </table>
                                
                                <!-- Job Order Items -->
                                <div class="section-title">Job Order Items</div>
                                <div class="table-responsive">
                                    <table class="items-table">
                                        <thead>
                                            <tr>
                                                <th width="5%">Sr.No.</th>
                                                <th width="10%">Product Code</th>
                                                <th width="15%">Product Name</th>
                                                <th width="20%">Description</th>
                                                <th width="5%">QTY</th>
                                                <th width="8%">Unit</th>
                                                <th width="10%">Tag No.</th>
                                                <th width="15%">Scope</th>
                                                <th width="6%">Stores Remark</th>
                                                <th width="6%">Remark</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if(isset($joborder_details) && !empty($joborder_details)): ?>
                                                <?php $i=1; foreach($joborder_details as $detail): ?>
                                                <tr>
                                                    <td class="text-center"><?php echo $i; ?></td>
                                                    <td><?php echo isset($detail->product_code) ? $detail->product_code : ''; ?></td>
                                                    <td><?php echo isset($detail->item_name) ? $detail->item_name : ''; ?></td>
                                                    <td><?php echo isset($detail->description) ? $detail->description : ''; ?></td>
                                                    <td class="text-center"><?php echo isset($detail->quantity) ? $detail->quantity : ''; ?></td>
                                                    <td><?php echo isset($detail->unit) ? $detail->unit : ''; ?></td>
                                                    <td><?php echo isset($detail->tag_no) ? $detail->tag_no : ''; ?></td>
                                                    <td><?php echo isset($detail->scope) ? $detail->scope : ''; ?></td>
                                                    <td class="text-center">
                                                        <?php 
                                                        if(isset($detail->stores_remark)) {
                                                            echo ($detail->stores_remark == 'Y') ? 'Yes' : (($detail->stores_remark == 'N') ? 'No' : $detail->stores_remark);
                                                        }
                                                        ?>
                                                    </td>
                                                    <td><?php echo isset($detail->remark) ? $detail->remark : ''; ?></td>
                                                </tr>
                                                <?php $i++; endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="10" class="text-center">No items found for this Job Order</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                                
                                <!-- Footer -->
                                <div class="text-center" style="margin-top: 30px; font-size: 10px; color: #999;">
                                    Generated on: <?php echo date("d-m-Y"); ?>
                                </div>
                            </div>
                            <?php endif; ?>
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
</body>
</html>

<script>
$(document).ready(function() {
    // Initialize select2 for better dropdown
    $('.select2').select2({
        placeholder: '-- Select Job Order --',
        allowClear: true,
        width: '100%'
    });
    
    // Form validation
    $('#joborder_form').on('submit', function(e) {
        var joborder_id = $('#joborder_id').val();
        if(joborder_id == '' || joborder_id == null) {
            e.preventDefault();
            alert('Please select a Job Order');
            return false;
        }
        return true;
    });
});

// Print function
function printReport() {
    window.print();
}
</script>
