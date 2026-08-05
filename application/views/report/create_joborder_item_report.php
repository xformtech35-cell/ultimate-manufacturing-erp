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
                   Job Order Item Report
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
                            <form class="form-horizontal form_overlay" method="post" action="<?php echo base_url(); ?>ReportController/create_joborder_item_report">
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
                                    <button type="submit" class="btn btn-default">Cancel</button>
                                    <button type="submit" class="btn btn-success">Submit</button>
                                </center>
                                <!-- /.box-body -->
                                <div class="box-footer"></div>
                                <!-- /.box-footer -->
                            </form>
                            <a href="<?php echo base_url('ReportController/get_joborder_item_report_by_date_xlsx'); ?>">
                                <button class="btn-sm btn btn-success pull-right">Export to Excel</button>
                            </a>
<?php
                            $grand_total = 0;
                            foreach ($result as $key) {
                                $price_val = isset($key->price) ? (float) $key->price : 0;
                                $qty_val = isset($key->quantity) ? (float) $key->quantity : 0;
                                        $grand_total += $qty_val * $price_val;
                            }
                            ?>
                            <table id="example3" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Sr.No.</th>
                                        <th>Job Order No</th>
                                        <th>Product Name</th>
                                        <th>Description</th>
                                        <th>QTY</th>
                                        <th>UNIT</th>
                                        <th>TAG NO.</th>
                                        <th>SCOPE</th>
                                        <th>REMARK</th>
                                        <th>Company Name</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th> Price</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = 1;
                                    foreach ($result as $key) {
                                        $status_label = 'Unknown';
                                        if ($key->status == 1) {
                                            $status_label = 'Draft';
                                        } elseif ($key->status == 2) {
                                            $status_label = 'Sent';
                                        } elseif ($key->status == 3) {
                                            $status_label = 'Viewed';
                                        } elseif ($key->status == 4) {
                                            $status_label = 'Approved';
                                        } elseif ($key->status == 5) {
                                            $status_label = 'Rejected';
                                        } elseif ($key->status == 6) {
                                            $status_label = 'Canceled';
                                        }
                                        $price = isset($key->price) ? (float) $key->price : 0;
                                        $qty = isset($key->quantity) ? (float) $key->quantity : 0;
                                        $amount = $qty * $price;
                                        ?>
                                        <tr>
                                            <td><?php echo $i; ?></td>
                                            <td><?php echo $key->number; ?></td>
                                            <td><?php echo !empty($key->product_description) ? $key->product_description : (!empty($key->product_name) ? $key->product_name : ''); ?></td>
                                            <td><?php echo isset($key->description) ? strip_tags($key->description) : ''; ?></td>
                                            <td><?php echo isset($key->quantity) ? $key->quantity : ''; ?></td>
                                            <td><?php echo isset($key->unit) ? $key->unit : ''; ?></td>
                                            <td><?php echo isset($key->tag_no) ? $key->tag_no : ''; ?></td>
                                            <td><?php echo isset($key->scope) ? strip_tags($key->scope) : ''; ?></td>
                                            <td><?php echo isset($key->remark) ? strip_tags($key->remark) : ''; ?></td>
                                            <td><?php echo isset($key->company_name) ? $key->company_name : ''; ?></td>
                                            <td><?php echo !empty($key->date) && $key->date != '0000-00-00' ? date("d-m-Y", strtotime($key->date)) : ''; ?></td>
                                            <td><?php echo $status_label; ?></td>
                                            <td><?php echo number_format($price, 2); ?></td>
                                            <td><?php echo number_format($amount, 2); ?></td>
                                        </tr>
                                        <?php
                                        $i++;
                                    }
                                    ?>
                                </tbody>
                                <?php if (!empty($result)) { ?>
                                <tfoot>
                                    <tr style="background-color: #f5f5f5; font-weight: 600;">
                                        <td colspan="14" class="text-right">Grand Total</td>
                                        <td class="text-right"><?php echo number_format($grand_total, 2); ?></td>
                                    </tr>
                                </tfoot>
                                <?php } ?>
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
<script>
$(document).ready(function() {
    if ($.fn.DataTable.isDataTable('#example3')) {
        $('#example3').DataTable().destroy();
    }
    $('#example3').DataTable({
        autoWidth: false,
        scrollX: true,
        pageLength: 25,
        language: {
            search: "Search Job Order Items:"
        },
        columns: [
            { width: '50px' },   // Sr.No.
            { width: '130px' },  // Job Order No
            { width: '110px' },  // Product Name
            { width: '160px' },  // Description
            { width: '55px' },   // QTY
            { width: '60px' },   // UNIT
            { width: '75px' },   // TAG NO.
            { width: '90px' },   // SCOPE
            { width: '110px' },  // STORES REMARK
            { width: '90px' },   // REMARK
            { width: '130px' },  // Company Name
            { width: '90px' },   // Date
            { width: '70px' },   // Status
            { width: '80px' },   // Cost Price
            { width: '80px' }    // Amount
        ],
        columnDefs: [
            { targets: [0], className: 'dt-center' },
            { targets: [4, 13, 14], className: 'dt-right' },
            { targets: [5, 6, 11, 12], className: 'dt-center' }
        ]
    });
});
</script>