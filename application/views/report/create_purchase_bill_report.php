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
                <h1>
                    Purchase Voucher Report 
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i>Home</a></li>
                    <li><a href="#">Report</a></li>
                    <li class="active">Report</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <!-- left column -->
                    <div class="col-md-12">
                        <!-- Horizontal Form -->
                        <div class="box box-info">
                            
                            <!-- /.box-header -->
                            <!-- form start -->
                            <form class="form-horizontal form_overlay" method="post" action="<?php echo base_url(); ?>ReportController/create_purchase_bill_report">
                                <div class="box-body">
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-3 control-label">From Date<span style="color: red;">*</span></label>
                                        <div class="col-sm-4">
                                            <input type="text" id="from_date" autocomplete="off" class="form-control backdate created-date" value="<?php echo isset($from_date) ? $from_date : ''; ?>" name="from_date" required="" onkeydown="return false;">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-3 control-label">To Date<span style="color: red;">*</span></label>
                                        <div class="col-sm-4">
                                            <input type="text" id="to_date" autocomplete="off" class="form-control payment-due-date-check" value="<?php echo isset($to_date) ? $to_date : ''; ?>" name="to_date" required="" onkeydown="return false;"> 
                                        </div>
                                    </div>
                                </div>
                                <center>
                                    <button type="reset" class="btn btn-default">Cancel</button>
                                    <button type="submit" class="btn btn-success">Submit</button>
                                </center>
                                <!-- /.box-body -->
                                <div class="box-footer">
                                    
                                </div>
                                <!-- /.box-footer -->
                            </form>
                            
                            <?php if(isset($result) && !empty($result)): ?>
                            <a href="<?php echo base_url(); ?>ReportController/create_purchase_bill_hsn_report"><button class="btn-sm btn btn-info pull-right" style="margin-right: 8px;">Purchase Voucher HSN Report</button></a>
                            <a href="<?php echo base_url(); ?>ReportController/get_purchase_bill_report_by_date_xlsx" class="btn-sm btn btn-success pull-right">Export to Excel</a>
                            <?php endif; ?>
                            
                            <table id="example3" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Sr.No.</th>
                                        <th>Voucher No</th>
                                        <th>Voucher Date</th>
                                        <th>Supplier Name</th>
                                        <th>Type</th>
                                        <th>Total Before Tax</th>
                                        <th>SGST</th>
                                        <th>CGST</th>
                                        <th>IGST</th>
                                        <th>Total GST</th>
                                        <th>Grand Total</th>
                                        <th>Balance</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if(isset($result) && !empty($result)):
                                        $i = 1;
                                        $total_before_tax = 0;
                                        $total_sgst = 0;
                                        $total_cgst = 0;
                                        $total_igst = 0;
                                        $total_gst = 0;
                                        $total_grand = 0;
                                        $total_balance = 0;
                                        
                                        // Group by bill number
                                        $grouped_data = [];
                                        foreach ($result as $row) {
                                            $bill_no = $row->number;
                                            if (!isset($grouped_data[$bill_no])) {
                                                $grouped_data[$bill_no] = [
                                                    'number' => $row->number,
                                                    'date' => $row->date,
                                                    'supplier_name' => !empty($row->company_name) ? $row->company_name : (!empty($row->supplier_fullname) ? $row->supplier_fullname : 'Supplier ID: ' . $row->supplier_id_fk),
                                                    'total_before_tax' => 0,
                                                    'total_sgst' => 0,
                                                    'total_cgst' => 0,
                                                    'total_igst' => 0,
                                                    'total_gst' => 0,
                                                    'grand_total' => 0,
                                                    'balance' => $row->balance,
                                                    'gst_type' => $row->gst_type
                                                ];
                                            }
                                            
                                            $amount = floatval($row->amount);
                                            $sgst = floatval($row->sgst);
                                            $cgst = floatval($row->cgst);
                                            $igst = floatval($row->igst);
                                            $total_gst_amount = $sgst + $cgst + $igst;
                                            $grand_total = $amount + $total_gst_amount - floatval($row->discount);
                                            
                                            $grouped_data[$bill_no]['total_before_tax'] += $amount;
                                            $grouped_data[$bill_no]['total_sgst'] += $sgst;
                                            $grouped_data[$bill_no]['total_cgst'] += $cgst;
                                            $grouped_data[$bill_no]['total_igst'] += $igst;
                                            $grouped_data[$bill_no]['total_gst'] += $total_gst_amount;
                                            $grouped_data[$bill_no]['grand_total'] += $grand_total;
                                        }
                                        
                                        foreach ($grouped_data as $bill):
                                            $gst_type_display = ($bill['gst_type'] != 'I') ? 'SGST' : 'IGST';
                                            $sgst_display = ($bill['gst_type'] != 'I') ? $bill['total_gst'] / 2 : 0;
                                            $cgst_display = ($bill['gst_type'] != 'I') ? $bill['total_gst'] / 2 : 0;
                                            $igst_display = ($bill['gst_type'] == 'I') ? $bill['total_gst'] : 0;
                                            
                                            $total_before_tax += $bill['total_before_tax'];
                                            $total_sgst += $sgst_display;
                                            $total_cgst += $cgst_display;
                                            $total_igst += $igst_display;
                                            $total_gst += $bill['total_gst'];
                                            $total_grand += $bill['grand_total'];
                                            $total_balance += $bill['balance'];
                                            ?>
                                            <tr>
                                                <td><?php echo $i; ?></td>
                                                <td><?php echo $bill['number']; ?></td>
                                                <td><?php echo date("d-m-Y", strtotime($bill['date'])); ?></td>
                                                <td><?php echo $bill['supplier_name']; ?></td>
                                                <td class="text-center"><?php echo $gst_type_display; ?></td>
                                                <td class="text-right"><?php echo number_format($bill['total_before_tax'], 2); ?></td>
                                                <td class="text-right"><?php echo number_format($sgst_display, 2); ?></td>
                                                <td class="text-right"><?php echo number_format($cgst_display, 2); ?></td>
                                                <td class="text-right"><?php echo number_format($igst_display, 2); ?></td>
                                                <td class="text-right"><?php echo number_format($bill['total_gst'], 2); ?></td>
                                                <td class="text-right"><?php echo number_format($bill['grand_total'], 2); ?></td>
                                                <td class="text-right"><?php echo number_format($bill['balance'], 2); ?></td>
                                            </tr>
                                        <?php 
                                            $i++;
                                        endforeach; 
                                        ?>
                                        <tfoot>
                                            <tr style="background-color: #f9f9f9; font-weight: bold;">
                                                <td colspan="5" class="text-right"><strong>Total:</strong></td>
                                                <td class="text-right"><strong><?php echo number_format($total_before_tax, 2); ?></strong></td>
                                                <td class="text-right"><strong><?php echo number_format($total_sgst, 2); ?></strong></td>
                                                <td class="text-right"><strong><?php echo number_format($total_cgst, 2); ?></strong></td>
                                                <td class="text-right"><strong><?php echo number_format($total_igst, 2); ?></strong></td>
                                                <td class="text-right"><strong><?php echo number_format($total_gst, 2); ?></strong></td>
                                                <td class="text-right"><strong><?php echo number_format($total_grand, 2); ?></strong></td>
                                                <td class="text-right"><strong><?php echo number_format($total_balance, 2); ?></strong></td>
                                            </tr>
                                        </tfoot>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="12" class="text-center">No data available</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <!-- /.box -->
                    </div>
                    <!--/.col (left ) -->
                </div>
                <!-- /.row -->
            </section>
            <!-- /.content -->
        </div>

        <?php $this->load->view('admin/footer'); ?>
        <div class="control-sidebar-bg"></div>
    </div>
    <!-- ./wrapper -->