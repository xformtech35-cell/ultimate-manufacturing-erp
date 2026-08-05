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
                    Edit Order Confirmation
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/'?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="<?php echo base_url(); ?>OrderConfirmationController/index">Order Confirmation</a></li>
                    <li class="active">Edit OC</li>
                </ol>
            </section>
            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <h3 class="box-title">Edit Order Confirmation</h3>
                            </div>
                            <!-- /.box-header -->
                            <!-- form start -->
                            <form role="form" action="<?php echo base_url(); ?>OrderConfirmationController/update_order_confirmation" method="POST" enctype="multipart/form-data">
                                <div class="box-body">
                                    <!-- Flash Messages -->
                                    <?php if ($this->session->flashdata('SUCCESSMSG')) { ?>
                                        <div role="alert" class="alert alert-success">
                                            <button data-dismiss="alert" class="close" type="button"><span aria-hidden="true">x</span></button>
                                            <strong>Well done!!</strong> <?= $this->session->flashdata('SUCCESSMSG') ?>
                                        </div>
                                    <?php } ?>

                                    <?php if ($this->session->flashdata('INFOMSG')) { ?>
                                        <div role="alert" class="alert alert-info">
                                            <button data-dismiss="alert" class="close" type="button"><span aria-hidden="true">x</span></button>
                                            <strong>Oops!!</strong> <?= $this->session->flashdata('INFOMSG') ?>
                                        </div>
                                    <?php } ?>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="number">OC Number</label>
                                                <input type="text" class="form-control" id="number" name="number" value="<?php echo $oc['number_fk']; ?>" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="date">Date <span style="color: red;">*</span></label>
                                                <input type="date" class="form-control" id="date" name="date" value="<?php echo $oc['date']; ?>" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="supplier_id">Supplier <span style="color: red;">*</span></label>
                                                <select class="form-control" id="supplier_id" name="supplier_id" required>
                                                    <option value="">-- Select Supplier --</option>
                                                    <?php if(isset($supplier_result) && !empty($supplier_result)) {
                                                        foreach($supplier_result as $supplier) {
                                                            $selected = ($supplier->supplier_id == $oc['supplier_id']) ? 'selected' : '';
                                                            echo '<option value="'.$supplier->supplier_id.'" '.$selected.'>'.$supplier->company_name . " - " . $supplier->s_code.'</option>';
                                                        }
                                                    } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="po_reference">PO Reference</label>
                                                <input type="text" class="form-control" id="po_reference" name="po_reference" value="<?php echo $oc['po_reference']; ?>" placeholder="Enter PO Reference">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="delivery_date">Expected Delivery Date</label>
                                                <input type="date" class="form-control" id="delivery_date" name="delivery_date" value="<?php echo $oc['delivery_date']; ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="payment_terms">Payment Terms</label>
                                                <input type="text" class="form-control" id="payment_terms" name="payment_terms" value="<?php echo $oc['payment_terms']; ?>" placeholder="e.g., Net 30">
                                            </div>
                                        </div>
                                    </div>

                                    <?php if ($_has_project_master): ?>
                                     <div class="row">
                                         <div class="col-md-6">
                                             <div class="form-group">
                                                 <label for="project_code">Project Code</label>
                                                 <input type="text" class="form-control" id="project_code" name="project_code" value="<?php echo $oc['project_code']; ?>" placeholder="Enter project code">
                                             </div>
                                         </div>
                                     </div>
                                     <?php endif; ?>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="remarks">Remarks</label>
                                                <textarea class="form-control" id="remarks" name="remarks" rows="3" placeholder="Enter any remarks..."><?php echo $oc['remarks']; ?></textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <hr>
                                    <h4>OC Items</h4>
                                    <hr>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <table class="table table-bordered" id="ocTable">
                                                <thead>
                                                    <tr>
                                                        <th width="25%">Description</th>
                                                        <th width="10%">HSN Code</th>
                                                        <th width="10%">Quantity</th>
                                                        <th width="8%">Unit</th>
                                                        <th width="12%">Unit Price</th>
                                                        <th width="8%">Tax Rate %</th>
                                                        <th width="12%">Amount</th>
                                                        <th width="10%">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php 
                                                    if(isset($oc_detail) && !empty($oc_detail)) {
                                                        foreach($oc_detail as $detail) {
                                                    ?>
                                                        <tr class="oc-item">
                                                            <td><input type="text" class="form-control description" name="description[]" value="<?php echo $detail['description']; ?>" placeholder="Item description"></td>
                                                            <td><input type="text" class="form-control hsn_code" name="hsn_code[]" value="<?php echo $detail['hsn_code']; ?>" placeholder="HSN"></td>
                                                            <td><input type="number" class="form-control quantity" name="quantity[]" value="<?php echo $detail['quantity']; ?>" placeholder="Qty" step="0.01"></td>
                                                            <td><input type="text" class="form-control unit" name="unit[]" value="<?php echo $detail['unit']; ?>" placeholder="Unit"></td>
                                                            <td><input type="number" class="form-control unit_price" name="unit_price[]" value="<?php echo $detail['unit_price']; ?>" placeholder="Price" step="0.01"></td>
                                                            <td><input type="number" class="form-control tax_rate" name="tax_rate[]" value="<?php echo $detail['tax_rate']; ?>" placeholder="%" step="0.01"></td>
                                                            <td><input type="number" class="form-control amount" name="amount[]" value="<?php echo $detail['amount']; ?>" placeholder="Amount" readonly></td>
                                                            <td><button type="button" class="btn btn-danger btn-sm remove-row"><i class="fa fa-trash"></i></button></td>
                                                        </tr>
                                                    <?php 
                                                        }
                                                    } else {
                                                    ?>
                                                        <tr class="oc-item">
                                                            <td><input type="text" class="form-control description" name="description[]" placeholder="Item description"></td>
                                                            <td><input type="text" class="form-control hsn_code" name="hsn_code[]" placeholder="HSN"></td>
                                                            <td><input type="number" class="form-control quantity" name="quantity[]" placeholder="Qty" step="0.01"></td>
                                                            <td><input type="text" class="form-control unit" name="unit[]" placeholder="Unit"></td>
                                                            <td><input type="number" class="form-control unit_price" name="unit_price[]" placeholder="Price" step="0.01"></td>
                                                            <td><input type="number" class="form-control tax_rate" name="tax_rate[]" placeholder="%" step="0.01" value="0"></td>
                                                            <td><input type="number" class="form-control amount" name="amount[]" placeholder="Amount" readonly></td>
                                                            <td><button type="button" class="btn btn-danger btn-sm remove-row"><i class="fa fa-trash"></i></button></td>
                                                        </tr>
                                                    <?php } ?>
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td colspan="7"><button type="button" class="btn btn-success btn-sm" id="addRowBtn"><i class="fa fa-plus"></i> Add Row</button></td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-8"></div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Sub Total</label>
                                                <input type="text" class="form-control" id="sub_total" name="sub_total" value="<?php echo $oc['sub_total']; ?>" placeholder="0.00" readonly>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-8"></div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Tax Amount</label>
                                                <input type="text" class="form-control" id="tax_amount" name="tax_amount" value="<?php echo $oc['tax_amount']; ?>" placeholder="0.00" readonly>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-8"></div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Total Amount</label>
                                                <input type="text" class="form-control" id="total_amount" name="total_amount" value="<?php echo $oc['total']; ?>" placeholder="0.00" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.box-body -->
                                <div class="box-footer">
                                    <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Update Order Confirmation</button>
                                    <a href="<?php echo base_url(); ?>OrderConfirmationController/index" class="btn btn-default"><i class="fa fa-ban"></i> Cancel</a>
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
            // Calculate initial totals
            calculateTotal();
            
            // Add new row
            $('#addRowBtn').click(function() {
                var newRow = '<tr class="oc-item">' +
                    '<td><input type="text" class="form-control description" name="description[]" placeholder="Item description"></td>' +
                    '<td><input type="text" class="form-control hsn_code" name="hsn_code[]" placeholder="HSN"></td>' +
                    '<td><input type="number" class="form-control quantity" name="quantity[]" placeholder="Qty" step="0.01"></td>' +
                    '<td><input type="text" class="form-control unit" name="unit[]" placeholder="Unit"></td>' +
                    '<td><input type="number" class="form-control unit_price" name="unit_price[]" placeholder="Price" step="0.01"></td>' +
                    '<td><input type="number" class="form-control tax_rate" name="tax_rate[]" placeholder="%" step="0.01" value="0"></td>' +
                    '<td><input type="number" class="form-control amount" name="amount[]" placeholder="Amount" readonly></td>' +
                    '<td><button type="button" class="btn btn-danger btn-sm remove-row"><i class="fa fa-trash"></i></button></td>' +
                    '</tr>';
                $('#ocTable tbody').append(newRow);
            });

            // Remove row
            $(document).on('click', '.remove-row', function() {
                $(this).closest('tr').remove();
                calculateTotal();
            });

            // Calculate amount when quantity, price or tax changes
            $(document).on('change', '.quantity, .unit_price, .tax_rate', function() {
                var row = $(this).closest('tr');
                var quantity = parseFloat(row.find('.quantity').val()) || 0;
                var unit_price = parseFloat(row.find('.unit_price').val()) || 0;
                var tax_rate = parseFloat(row.find('.tax_rate').val()) || 0;
                
                var subtotal = quantity * unit_price;
                var tax_amount = subtotal * (tax_rate / 100);
                var amount = subtotal + tax_amount;
                
                row.find('.amount').val(amount.toFixed(2));
                calculateTotal();
            });

            // Calculate total
            function calculateTotal() {
                var subtotal = 0;
                var total_tax = 0;
                var grand_total = 0;
                
                $('.oc-item').each(function() {
                    var row = $(this);
                    var quantity = parseFloat(row.find('.quantity').val()) || 0;
                    var unit_price = parseFloat(row.find('.unit_price').val()) || 0;
                    var tax_rate = parseFloat(row.find('.tax_rate').val()) || 0;
                    
                    var row_subtotal = quantity * unit_price;
                    var row_tax = row_subtotal * (tax_rate / 100);
                    var row_amount = row_subtotal + row_tax;
                    
                    subtotal += row_subtotal;
                    total_tax += row_tax;
                    grand_total += row_amount;
                });
                
                $('#sub_total').val(subtotal.toFixed(2));
                $('#tax_amount').val(total_tax.toFixed(2));
                $('#total_amount').val(grand_total.toFixed(2));
            }
        });
    </script>
</body>
</html>

