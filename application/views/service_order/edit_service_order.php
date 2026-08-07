<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (!isset($session_data_head1)) {
    header($this->config->item('header'));
}
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(APPPATH . "views/admin/modal.php");
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
    .service-row td {
        padding: 5px !important;
    }
</style>
<body class="hold-transition skin-blue sidebar-mini">
    <div id="loader" class="center"></div> 
    <div class="wrapper">
        <div class="content-wrapper">
            <section class="content-header">
                <h1>Edit <?php echo $config['title']; ?></h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="<?php echo base_url() . 'ServiceOrderController/' . $config['url_prefix']; ?>"><?php echo $config['title']; ?></a></li>
                    <li class="active">Edit</li>
                </ol>
            </section>

            <section class="content">
                <div class="row" style="padding: 10px 15px;">
                    <a href="javascript:void(0);" onclick="window.history.back()" class="btn btn-primary pull-right"><i class="fa fa-close"></i> Close</a>
                </div>

                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-info">
                            <div class="box-header">
                                <h3 class="box-title">Edit <?php echo $config['title']; ?> Details</h3>
                            </div>
                            
                            <div class="box-body">
                                <form class="form-horizontal" name="edit_service_order_form" id="edit_service_order_form" method="post" action="<?php echo base_url(); ?>ServiceOrderController/edit_service_order">
                                    <input type="hidden" name="service_type" value="<?php echo $invoice_data_group['service_type']; ?>">
                                    <input type="hidden" name="doc_number" id="doc_number" value="<?php echo $invoice_data_group['number_fk']; ?>">

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group row">
                                                <label class="col-sm-12 control-label" style="text-align: right; padding-right: 30px;">
                                                    <h2><?php echo $config['title']; ?>: <b><?php echo $invoice_data_group['number_fk']; ?></b></h2>
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group row required">
                                                <label class="col-sm-4 control-label">Company</label>
                                                <div class="col-sm-8">
                                                    <select class="form-control input-sm select2" name="customer_id" id="customer_id" required style="width:100%">
                                                        <option value="">Select Company</option>
                                                        <?php foreach ($customer_result as $key) { ?>
                                                            <option value="<?php echo $key->customer_id; ?>" data-state="<?php echo $key->state_code; ?>" <?php echo ($key->customer_id == $invoice_data_group['customer_id_fk']) ? 'selected' : ''; ?>>
                                                                <?php echo $key->company_name; ?> - <?php echo $key->c_code; ?> (<?php echo $key->state_code; ?>)
                                                            </option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label class="col-sm-4 control-label">Status</label>
                                                <div class="col-sm-8">
                                                    <select class="form-control input-sm" name="status" id="status">
                                                        <option value="1" <?php echo ($invoice_data_group['status'] == 1) ? 'selected' : ''; ?>>Draft</option>
                                                        <option value="2" <?php echo ($invoice_data_group['status'] == 2) ? 'selected' : ''; ?>>Sent</option>
                                                        <option value="3" <?php echo ($invoice_data_group['status'] == 3) ? 'selected' : ''; ?>>Viewed</option>
                                                        <option value="4" <?php echo ($invoice_data_group['status'] == 4) ? 'selected' : ''; ?>>Approved</option>
                                                        <option value="5" <?php echo ($invoice_data_group['status'] == 5) ? 'selected' : ''; ?>>Rejected</option>
                                                        <option value="6" <?php echo ($invoice_data_group['status'] == 6) ? 'selected' : ''; ?>>Canceled</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group row required">
                                                <label class="col-sm-4 control-label">Date</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control input-sm datepicker" name="doc_date" id="doc_date" required value="<?php echo $invoice_data_group['date']; ?>">
                                                </div>
                                            </div>
                                            
                                            <div class="form-group row">
                                                <label class="col-sm-4 control-label">Customer PO</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control input-sm" name="po_number" id="po_number" placeholder="PO Number" value="<?php echo $invoice_data_group['po_number']; ?>">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group row">
                                                <label class="col-sm-4 control-label">Client Code</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control input-sm" name="customer_code" id="customer_code" placeholder="Client Code" value="<?php echo $invoice_data_group['customer_code']; ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <h4 class="box-title" style="margin-top:20px; border-bottom:1px solid #ddd; padding-bottom:5px;">Service items</h4>
                                            
                                            <table class="table table-bordered" id="items_table">
                                                <thead>
                                                    <tr style="background:#f4f4f4;">
                                                        <th style="width: 25%;">Service Name</th>
                                                        <th style="width: 20%;">Description</th>
                                                        <th style="width: 8%;">SAC Code</th>
                                                        <th style="width: 6%;">Qty</th>
                                                        <th style="width: 6%;">Unit</th>
                                                        <th style="width: 10%;">Price</th>
                                                        <th style="width: 8%;">GST %</th>
                                                        <th style="width: 10%;">Amount</th>
                                                        <th style="width: 7%;">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="items_tbody">
                                                    <?php 
                                                    $rowCounter = 0;
                                                    foreach ($show_invoice as $item) { 
                                                        ?>
                                                        <tr class="service-row" id="row_<?php echo $rowCounter; ?>">
                                                            <td>
                                                                <input type="text" name="service_name[]" class="form-control input-sm" required placeholder="Service Name" value="<?php echo htmlspecialchars($item->service_name); ?>">
                                                            </td>
                                                            <td>
                                                                <textarea name="description[]" class="form-control input-sm" rows="1" placeholder="Description"><?php echo htmlspecialchars($item->description); ?></textarea>
                                                            </td>
                                                            <td>
                                                                <input type="text" name="sac_code[]" class="form-control input-sm" placeholder="SAC" value="<?php echo htmlspecialchars($item->sac_code); ?>">
                                                            </td>
                                                            <td>
                                                                <input type="number" step="any" name="quantity[]" class="form-control input-sm qty-input" value="<?php echo $item->quantity; ?>" required>
                                                            </td>
                                                            <td>
                                                                <input type="text" name="unit[]" class="form-control input-sm" value="<?php echo htmlspecialchars($item->unit); ?>">
                                                            </td>
                                                            <td>
                                                                <input type="number" step="any" name="price[]" class="form-control input-sm price-input" value="<?php echo $item->price; ?>" required>
                                                            </td>
                                                            <td>
                                                                <select name="gst[]" class="form-control input-sm gst-input">
                                                                    <option value="0" <?php echo ($item->gst == 0) ? 'selected' : ''; ?>>0%</option>
                                                                    <option value="5" <?php echo ($item->gst == 5) ? 'selected' : ''; ?>>5%</option>
                                                                    <option value="12" <?php echo ($item->gst == 12) ? 'selected' : ''; ?>>12%</option>
                                                                    <option value="18" <?php echo ($item->gst == 18) ? 'selected' : ''; ?>>18%</option>
                                                                    <option value="28" <?php echo ($item->gst == 28) ? 'selected' : ''; ?>>28%</option>
                                                                </select>
                                                                <input type="hidden" name="sgst[]" class="sgst-val" value="<?php echo $item->sgst; ?>">
                                                                <input type="hidden" name="cgst[]" class="cgst-val" value="<?php echo $item->cgst; ?>">
                                                                <input type="hidden" name="igst[]" class="igst-val" value="<?php echo $item->igst; ?>">
                                                                <input type="hidden" name="gst_type[]" class="gst-type-val" value="<?php echo $item->gst_type; ?>">
                                                                <input type="hidden" name="discount[]" value="<?php echo $item->discount; ?>">
                                                            </td>
                                                            <td>
                                                                <input type="number" step="any" name="amount[]" class="form-control input-sm amount-val" readonly value="<?php echo $item->amount; ?>">
                                                            </td>
                                                            <td>
                                                                <button type="button" class="btn btn-danger btn-sm remove-row"><i class="fa fa-trash"></i></button>
                                                            </td>
                                                        </tr>
                                                        <?php 
                                                        $rowCounter++;
                                                    } 
                                                    ?>
                                                </tbody>
                                            </table>
                                            <button type="button" class="btn btn-info btn-sm" id="add_row_btn"><i class="fa fa-plus"></i> Add Row</button>
                                        </div>
                                    </div>

                                    <div class="row" style="margin-top: 20px;">
                                        <div class="col-md-6">
                                            <div class="form-group row">
                                                <label class="col-sm-3 control-label">Subheading</label>
                                                <div class="col-sm-9">
                                                    <input type="text" class="form-control input-sm" name="subheading" value="<?php echo htmlspecialchars($invoice_data_group['service_order_subheading']); ?>">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-sm-3 control-label">Footer Notes</label>
                                                <div class="col-sm-9">
                                                    <textarea class="form-control input-sm" name="footer" rows="2"><?php echo htmlspecialchars($invoice_data_group['service_order_footer']); ?></textarea>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-sm-3 control-label">Memo</label>
                                                <div class="col-sm-9">
                                                    <textarea class="form-control input-sm" name="memo" rows="2"><?php echo htmlspecialchars($invoice_data_group['service_order_memo']); ?></textarea>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6 text-right" style="padding-right: 30px;">
                                            <h3>Basic Total: <b id="basic_total_display"><?php echo number_format($invoice_data_group['basic_total'], 2); ?></b></h3>
                                            <input type="hidden" name="basic_total" id="basic_total" value="<?php echo $invoice_data_group['basic_total']; ?>">
                                            
                                            <div id="tax_breakdown_div" style="margin: 10px 0; color: #555;">
                                                <!-- Calculated tax breaks listed here -->
                                            </div>
                                            
                                            <h2>Grand Total: <b id="grand_total_display"><?php echo number_format($invoice_data_group['total'], 2); ?></b></h2>
                                            <input type="hidden" name="total" id="grand_total" value="<?php echo $invoice_data_group['total']; ?>">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <h4 class="box-title" style="margin-top:20px; border-bottom:1px solid #ddd; padding-bottom:5px;">Terms & Conditions</h4>
                                            
                                            <div class="col-md-6">
                                                <div class="form-group row">
                                                    <label class="col-sm-3 control-label">T&C</label>
                                                    <div class="col-sm-9">
                                                        <textarea class="form-control input-sm" name="terms" rows="2"><?php echo htmlspecialchars($invoice_data_group['terms_and_conditions']); ?></textarea>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-sm-3 control-label">Payment Terms</label>
                                                    <div class="col-sm-9">
                                                        <textarea class="form-control input-sm" name="payment_terms" rows="2"><?php echo htmlspecialchars($invoice_data_group['payment_terms']); ?></textarea>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-sm-3 control-label">Transportation</label>
                                                    <div class="col-sm-9">
                                                        <textarea class="form-control input-sm" name="transportation" rows="2"><?php echo htmlspecialchars($invoice_data_group['transportation']); ?></textarea>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group row">
                                                    <label class="col-sm-3 control-label">Installation</label>
                                                    <div class="col-sm-9">
                                                        <textarea class="form-control input-sm" name="installation" rows="2"><?php echo htmlspecialchars($invoice_data_group['installation']); ?></textarea>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-sm-3 control-label">Process Schedule</label>
                                                    <div class="col-sm-9">
                                                        <textarea class="form-control input-sm" name="process_schedule" rows="2"><?php echo htmlspecialchars($invoice_data_group['process_schedule']); ?></textarea>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-sm-3 control-label">Taxes</label>
                                                    <div class="col-sm-9">
                                                        <textarea class="form-control input-sm" name="taxes" rows="2"><?php echo htmlspecialchars($invoice_data_group['taxes']); ?></textarea>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-sm-3 control-label">Exclusions</label>
                                                    <div class="col-sm-9">
                                                        <textarea class="form-control input-sm" name="exclusions" rows="2"><?php echo htmlspecialchars($invoice_data_group['exclusions']); ?></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="box-footer text-center" style="margin-top: 20px;">
                                        <button type="submit" class="btn btn-success btn-lg"><i class="fa fa-save"></i> Update <?php echo $config['title']; ?></button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <?php $this->load->view('admin/footer'); ?>
    </div>

    <script>
        $(document).ready(function() {
            var myStateCode = '<?php echo $settings['state_code'] ?? ''; ?>';
            var rowCounter = <?php echo $rowCounter; ?>;

            $('.datepicker').datepicker({
                dateFormat: 'yy-mm-dd',
                autoclose: true
            });

            // Initialize select2
            $('.select2').select2();

            // Add Row
            $('#add_row_btn').on('click', function() {
                var newRow = `
                    <tr class="service-row" id="row_${rowCounter}">
                        <td>
                            <input type="text" name="service_name[]" class="form-control input-sm" required placeholder="Service Name">
                        </td>
                        <td>
                            <textarea name="description[]" class="form-control input-sm" rows="1" placeholder="Description"></textarea>
                        </td>
                        <td>
                            <input type="text" name="sac_code[]" class="form-control input-sm" placeholder="SAC">
                        </td>
                        <td>
                            <input type="number" step="any" name="quantity[]" class="form-control input-sm qty-input" value="1" required>
                        </td>
                        <td>
                            <input type="text" name="unit[]" class="form-control input-sm" value="NOS">
                        </td>
                        <td>
                            <input type="number" step="any" name="price[]" class="form-control input-sm price-input" value="0" required>
                        </td>
                        <td>
                            <select name="gst[]" class="form-control input-sm gst-input">
                                <option value="0">0%</option>
                                <option value="5">5%</option>
                                <option value="12">12%</option>
                                <option value="18" selected>18%</option>
                                <option value="28">28%</option>
                            </select>
                            <input type="hidden" name="sgst[]" class="sgst-val" value="0">
                            <input type="hidden" name="cgst[]" class="cgst-val" value="0">
                            <input type="hidden" name="igst[]" class="igst-val" value="0">
                            <input type="hidden" name="gst_type[]" class="gst-type-val" value="S">
                            <input type="hidden" name="discount[]" value="0">
                        </td>
                        <td>
                            <input type="number" step="any" name="amount[]" class="form-control input-sm amount-val" readonly value="0">
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger btn-sm remove-row"><i class="fa fa-trash"></i></button>
                        </td>
                    </tr>
                `;
                $('#items_tbody').append(newRow);
                rowCounter++;
                calculateTotals();
            });

            // Remove Row
            $(document).on('click', '.remove-row', function() {
                if ($('.service-row').length > 1) {
                    $(this).closest('tr').remove();
                    calculateTotals();
                } else {
                    alert("At least one item row is required.");
                }
            });

            // Recalculate on inputs change
            $(document).on('input change', '.qty-input, .price-input, .gst-input, #customer_id', function() {
                calculateTotals();
            });

            // Run initial totals calculation
            calculateTotals();

            function calculateTotals() {
                var selectedCustomer = $('#customer_id').val();
                var customerState = $('#customer_id').find('option:selected').data('state') || '';
                customerState = String(customerState).trim();
                var localState = String(myStateCode).trim();
                
                var isIGST = false;
                if (selectedCustomer && customerState !== '' && localState !== '') {
                    isIGST = (customerState !== localState);
                }

                var basicTotal = 0;
                var grandTotal = 0;
                var totalCGST = 0;
                var totalSGST = 0;
                var totalIGST = 0;

                $('.service-row').each(function() {
                    var $row = $(this);
                    var qty = parseFloat($row.find('.qty-input').val()) || 0;
                    var price = parseFloat($row.find('.price-input').val()) || 0;
                    var gstRate = parseFloat($row.find('.gst-input').val()) || 0;

                    var lineBasic = qty * price;
                    var taxAmount = lineBasic * (gstRate / 100);
                    var lineTotal = lineBasic + taxAmount;

                    $row.find('.amount-val').val(lineTotal.toFixed(2));
                    basicTotal += lineBasic;
                    grandTotal += lineTotal;

                    // Update hidden fields
                    var $cgstVal = $row.find('.cgst-val');
                    var $sgstVal = $row.find('.sgst-val');
                    var $igstVal = $row.find('.igst-val');
                    var $gstType = $row.find('.gst-type-val');

                    if (isIGST) {
                        $cgstVal.val(0);
                        $sgstVal.val(0);
                        $igstVal.val(taxAmount.toFixed(2));
                        $gstType.val('I');
                        totalIGST += taxAmount;
                    } else {
                        var halfTax = taxAmount / 2;
                        $cgstVal.val(halfTax.toFixed(2));
                        $sgstVal.val(halfTax.toFixed(2));
                        $igstVal.val(0);
                        $gstType.val('S');
                        totalCGST += halfTax;
                        totalSGST += halfTax;
                    }
                });

                $('#basic_total_display').text(basicTotal.toFixed(2));
                $('#basic_total').val(basicTotal.toFixed(2));

                // Tax breakdown display
                var taxHtml = '';
                if (isIGST) {
                    taxHtml = `<div>IGST: <b>${totalIGST.toFixed(2)}</b></div>`;
                } else {
                    taxHtml = `
                        <div>CGST: <b>${totalCGST.toFixed(2)}</b></div>
                        <div>SGST: <b>${totalSGST.toFixed(2)}</b></div>
                    `;
                }
                $('#tax_breakdown_div').html(taxHtml);

                $('#grand_total_display').text(grandTotal.toFixed(2));
                $('#grand_total').val(grandTotal.toFixed(2));
            }
        });
    </script>
</body>
