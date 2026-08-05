<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (isset($session_data_head1)) {
    
} else {
    header($this->config->item('header'));
}
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<body class="hold-transition skin-blue sidebar-mini">
    <div class="wrapper">

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                     Finished Product Production
                </h1>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box">
                            <div class="box-header" style="background: linear-gradient(135deg, #3c8dbc 0%, #224abe 100%); color: white;">
                                <h3 class="box-title" style="color: white; font-weight: 600;"><i class="fa fa-cubes"></i> Add Finished Product Output</h3>
                                <span id="error" style="color:red;display:none">Please Enter Only Alphabets...</span>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body" style="padding: 20px;">

                                <?php if ($this->session->flashdata('INFOMSG')) { ?>
                                    <div role="alert" class="alert alert-info">
                                        <button data-dismiss="alert" class="close" type="button"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                                        <strong>Ooh!!</strong> <?= $this->session->flashdata('INFOMSG') ?>
                                    </div>
                                <?php } ?>

                                <form class="form-horizontal" id="form_overlay" method="post" action="<?php echo base_url(); ?>PlanningController/add_finished_product">

                                    <div class="modal-body">
                                        <div class="card-body">
                                            
                                            <!-- Job Order Link Selector (Optional) -->
                                            <div class="form-group row">
                                                <label for="jo_number" class="col-sm-2 control-label">Link Job Order</label>
                                                <div class="col-sm-7">
                                                    <select name="jo_number" id="jo_number" class="form-control input-sm select2">
                                                        <option value="">-- Select Job Order (Auto-fills Product Details) --</option>
                                                        <?php if(!empty($joborders)): ?>
                                                            <?php foreach($joborders as $jo): ?>
                                                                <option value="<?php echo htmlspecialchars($jo['number_fk']); ?>">
                                                                    <?php echo htmlspecialchars($jo['number_fk']); ?> (SO Reference: <?php echo htmlspecialchars($jo['so_reference']); ?>)
                                                                </option>
                                                            <?php endforeach; ?>
                                                        <?php endif; ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="batch_fk" class="col-sm-2 control-label">Batch Number</label>
                                                <div class="col-sm-7">
                                                    <select name="batch_fk" id="batch_fk" class="form-control input-sm" required="">
                                                        <option value="">Select Batch</option>
                                                        <?php foreach($batch as $key){ ?>
                                                        <option value="<?php echo $key->batch; ?>"><?php echo date('d-M-Y', strtotime($key->raw_item_deliver_date)); ?>-Batch-<?php echo $key->batch; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="product_name" class="col-sm-2 control-label">Product Name</label>
                                                <div class="col-sm-7">
                                                    <select name="product_name" id="product_name" class="form-control input-sm select2" required="">
                                                        <option value="">Select Product</option>
                                                        <?php foreach($products as $key){ ?>
                                                        <option value="<?php echo $key->product_master_name; ?>"><?php echo $key->product_master_name; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="product_qty" class="col-sm-2 control-label">Product Quantity</label>
                                                <div class="col-sm-7">
                                                    <input type="text" class="form-control input-sm" name="product_qty" id="product_qty" maxlength="40" required="">
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="product_unit" class="col-sm-2 control-label">Product Unit</label>
                                                <div class="col-sm-7">
                                                    <input type="text" class="form-control input-sm" name="product_unit" id="product_unit" maxlength="40" required="">
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="batch_status" class="col-sm-2 control-label">Batch Status</label>
                                                <div class="col-sm-7">
                                                    <select name="batch_status" id="batch_status" class="form-control input-sm" required="">
                                                        <option value="">Select Batch Status</option>
                                                        <option value="0">Pending</option>
                                                        <option value="1">Completed</option>
                                                    </select>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" id="btnSave" class="btn btn-success pull-left"><i class="fa fa-save"></i> Save Production Output</button>
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
        <?php $this->load->view('admin/footer'); ?>
        <div class="control-sidebar-bg"></div>
    </div>
    <!-- ./wrapper -->

    <script>
        $(document).ready(function() {
            $('.select2').select2({
                width: '100%'
            });

            // Auto-populate form from selected Job Order
            $('#jo_number').on('change', function() {
                var joNumber = $(this).val();
                if (!joNumber) return;

                // Show dynamic loading state
                var $joSelect = $(this);
                $joSelect.prop('disabled', true);

                $.ajax({
                    url: '<?php echo base_url(); ?>PlanningController/ajax_get_jo_details',
                    type: 'POST',
                    data: { jo_number: joNumber },
                    dataType: 'json',
                    success: function(res) {
                        if (res.success && res.items.length > 0) {
                            var item = res.items[0]; // Pre-fill using the primary item of the Job Order
                            
                            // 1. Auto-select product name
                            var productName = item.product_name || item.product_code;
                            $('#product_name').val(productName).trigger('change');
                            
                            // 2. Pre-fill quantity
                            $('#product_qty').val(parseFloat(item.quantity) || 1);
                            
                            // 3. Pre-fill unit
                            $('#product_unit').val(item.unit || 'PCS');
                        }
                    },
                    error: function() {
                        alert('Error fetching Job Order details.');
                    },
                    complete: function() {
                        $joSelect.prop('disabled', false);
                    }
                });
            });
        });
    </script>
</body>
</html>
