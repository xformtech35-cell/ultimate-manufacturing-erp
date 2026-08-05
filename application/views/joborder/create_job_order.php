<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (isset($session_data_head1)) {
} else {
    header($this->config->item('header'));
}

defined('BASEPATH') OR exit('No direct script access allowed');
$_has_project_master = isset($session_data_head1['permission']) && in_array('Projects', $session_data_head1['permission']);
?>
<body class="hold-transition skin-blue sidebar-mini">
    <div id="loader" class="center"></div>
    <div class="wrapper">
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    Create Job Order
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/'?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="<?php echo base_url(); ?>JobOrderController/index">Job Order</a></li>
                    <li class="active">Create Job Order</li>
                </ol>
            </section>
            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <h3 class="box-title">Job Order Details</h3>
                            </div>
                            <!-- /.box-header -->
                            <!-- form start -->
                            <form role="form" action="<?php echo base_url(); ?>JobOrderController/save_job_order" method="POST" enctype="multipart/form-data">
                                <div class="box-body">
                                    <!-- Flash Messages -->
                                   

                                    <?php if ($this->session->flashdata('INFOMSG')) { ?>
                                        <div role="alert" class="alert alert-info">
                                            <button data-dismiss="alert" class="close" type="button"><span aria-hidden="true">x</span></button>
                                            <strong>Oops!!</strong> <?= $this->session->flashdata('INFOMSG') ?>
                                        </div>
                                    <?php } ?>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="number">Job Order Number</label>
                                                <input type="text" class="form-control" id="number" name="number" value="<?php echo isset($joborder_id) ? $joborder_id : ''; ?>" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="date">Date <span style="color: red;">*</span></label>
                                                <input type="date" class="form-control" id="date" name="date" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="customer_id">Customer <span style="color: red;">*</span></label>
                                                <select class="form-control" id="customer_id" name="customer_id" required>
                                                    <option value="">-- Select Customer --</option>
                                                    <?php if(isset($result) && !empty($result)) {
                                                        foreach($result as $customer) {
                                                            echo '<option value="'.$customer->customer_id.'">'.$customer->company_name.'</option>';
                                                        }
                                                    } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="due_date">Due Date</label>
                                                <input type="date" class="form-control" id="due_date" name="due_date">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="payment_terms">Payment Terms</label>
                                                <input type="text" class="form-control" id="payment_terms" name="payment_terms" placeholder="e.g., Net 30">
                                            </div>
                                        </div>
                                         <?php if ($_has_project_master): ?>
                                         <div class="col-md-6">
                                             <div class="form-group">
                                                 <label for="project_code">Project Code</label>
                                                 <input type="text" class="form-control" id="project_code" name="project_code" placeholder="Enter project code">
                                             </div>
                                         </div>
                                         <?php endif; ?>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="remarks">Remarks</label>
                                                <textarea class="form-control" id="remarks" name="remarks" rows="3" placeholder="Enter any remarks..."></textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <hr>
                                    <h4><strong>Job Order Line Items</strong></h4>
                                    <hr>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <table class="table table-bordered" id="jobOrderTable">
                                                <thead>
                                                    <tr>
                                                        <th width="5%">Sr.No.</th>
                                                        <th width="12%">Item Code</th>
                                                        <th width="18%">Equipment</th>
                                                        <th width="8%">Qty</th>
                                                        <th width="6%">Unit</th>
                                                        <th width="10%">Tag No.</th>
                                                        <th width="13%">Scope</th>
                                                        <th width="13%">Stores Remark<br>(If Material is Stock Y/N)</th>
                                                        <th width="12%">Remark</th>
                                                        <th width="3%">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr class="job-item">
                                                        <td class="sr-no">1</td>
                                                        <td><input type="text" class="form-control item_code" name="item_code[]" placeholder="Item Code"></td>
                                                        <td><input type="text" class="form-control equipment" name="equipment[]" placeholder="Equipment"></td>
                                                        <td><input type="number" class="form-control quantity" name="quantity[]" placeholder="Qty" step="0.01"></td>
                                                        <td><input type="text" class="form-control unit" name="unit[]" placeholder="Unit"></td>
                                                        <td><input type="text" class="form-control tag_no" name="tag_no[]" placeholder="Tag No."></td>
                                                        <td><input type="text" class="form-control scope" name="scope[]" placeholder="Scope"></td>
                                                        <td><input type="text" class="form-control stores_remark" name="stores_remark[]" placeholder="Y/N"></td>
                                                        <td><input type="text" class="form-control remark" name="remark[]" placeholder="Remark"></td>
                                                        <td class="text-center"><button type="button" class="btn btn-danger btn-xs remove-row" title="Delete Row"><i class="fa fa-trash"></i></button></td>
                                                    </tr>
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td colspan="9"></td>
                                                        <td class="text-center">
                                                            <button type="button" class="btn btn-success btn-sm" id="addRowBtn" title="Add New Row">
                                                                <i class="fa fa-plus-circle"></i> Add
                                                            </button>
                                                        </td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>


                                </div>
                                <!-- /.box-body -->
                                <div class="box-footer">
                                    <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Save Job Order</button>
                                    <a href="<?php echo base_url(); ?>JobOrderController/index" class="btn btn-default"><i class="fa fa-ban"></i> Cancel</a>
                                </div>
                            </form>
                        </div>
                        <!-- /.box -->
                    </div>
                </div>
                <!-- /.row -->
            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->
        <footer class="main-footer">
            <div class="pull-right hidden-xs">
                <b>Version</b> 1.0.0
            </div>
            <strong>Copyright &copy; 2024</strong> All rights reserved.
        </footer>
    </div>
    <!-- ./wrapper -->
    
    <!-- jQuery -->
    <script src="<?php echo base_url(); ?>bower_components/jquery/dist/jquery.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="<?php echo base_url(); ?>bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Add new row
            $('#addRowBtn').click(function() {
                var rowCount = $('.job-item').length + 1;
                var newRow = '<tr class="job-item">' +
                    '<td class="sr-no">' + rowCount + '</td>' +
                    '<td><input type="text" class="form-control item_code" name="item_code[]" placeholder="Item Code"></td>' +
                    '<td><input type="text" class="form-control equipment" name="equipment[]" placeholder="Equipment"></td>' +
                    '<td><input type="number" class="form-control quantity" name="quantity[]" placeholder="Qty" step="0.01"></td>' +
                    '<td><input type="text" class="form-control unit" name="unit[]" placeholder="Unit"></td>' +
                    '<td><input type="text" class="form-control tag_no" name="tag_no[]" placeholder="Tag No."></td>' +
                    '<td><input type="text" class="form-control scope" name="scope[]" placeholder="Scope"></td>' +
                    '<td><input type="text" class="form-control stores_remark" name="stores_remark[]" placeholder="Y/N"></td>' +
                    '<td><input type="text" class="form-control remark" name="remark[]" placeholder="Remark"></td>' +
                    '<td class="text-center"><button type="button" class="btn btn-danger btn-xs remove-row" title="Delete Row"><i class="fa fa-trash"></i></button></td>' +
                    '</tr>';
                $('#jobOrderTable tbody').append(newRow);
                updateSerialNumbers();
            });

            // Remove row
            $(document).on('click', '.remove-row', function() {
                if (confirm('Are you sure you want to remove this item?')) {
                    $(this).closest('tr').remove();
                    updateSerialNumbers();
                }
            });

            // Update serial numbers
            function updateSerialNumbers() {
                $('.job-item').each(function(index) {
                    $(this).find('.sr-no').text(index + 1);
                });
            }


        });
    </script>
</body>
</html>

