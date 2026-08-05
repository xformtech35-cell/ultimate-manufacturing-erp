<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (isset($session_data_head1)) {
} else {
    header($this->config->item('header'));
}
//get user role 
$session_data_head = $this->session->userdata('session_data_head');
$user_role = $session_data_head['result']['role'];
defined('BASEPATH') or exit('No direct script access allowed');
require_once(APPPATH . '/third_party/amount_convert.php');
?>
<style>
    /* Disable responsive layout */
    @media (max-width: 992px) {
        .fixed-col {
            width: 25% !important;
            float: left !important;
        }
    }
    
    @media (max-width: 768px) {
        .fixed-col {
            width: 25% !important;
            float: left !important;
        }
    }
    
    @media (max-width: 480px) {
        .fixed-col {
            width: 25% !important;
            float: left !important;
        }
    }

    .fixed-col {
        width: 25%;
        float: left;
        padding: 10px;
        box-sizing: border-box;
    }

    .fixed-col .small-box {
        margin-bottom: 0;
    }

    .small-box .inner {
        min-height: 80px;
        padding: 10px;
    }

    .small-box .inner p {
        margin: 0;
        font-size: 13px;
        line-height: 1.4;
        word-wrap: break-word;
        overflow-wrap: break-word;
    }

    .small-box .inner h3 {
        font-size: 32px;
        font-weight: bold;
        margin: 5px 0;
    }

    .row {
        display: block !important;
        width: 100% !important;
    }

    .row::after {
        content: '';
        display: table;
        clear: both;
    }
</style>

<body class="hold-transition skin-blue sidebar-mini">
    <div id="loader" class="center"></div>
    <div class="wrapper" id="contents">
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    Dashboard
                    <small><?php echo $fy_label; ?></small>
                </h1>
                <ol class="breadcrumb">
                    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li class="active">Dashboard</li>
                    <li class="pull-right" style="list-style:none; margin-top:-2px;">
                        <form method="get" action="<?php echo base_url(); ?>Home/index" class="form-inline" style="margin:0; display:flex; align-items:center; gap:8px;">
                            <label style="font-weight:600; font-size:13px; color:#444; margin:0; white-space:nowrap;"><i class="fa fa-calendar" style="margin-right:5px; color:#1a6496;"></i>Financial Year</label>
                            <select name="fy" onchange="this.form.submit()" style="appearance:none; -webkit-appearance:none; background:#1a6496 url('data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'12\' height=\'8\'><path d=\'M0 0l6 8 6-8z\' fill=\'%23fff\'/></svg>') no-repeat right 10px center; background-size:10px; padding:6px 30px 6px 12px; border:none; border-radius:4px; color:#fff; font-size:13px; font-weight:700; cursor:pointer; box-shadow:0 2px 6px rgba(0,0,0,0.2);">
                                <?php
                                $current_month = (int)date('m');
                                $current_year  = (int)date('Y');
                                $max_fy = ($current_month >= 4) ? $current_year : $current_year - 1;
                                for ($y = $max_fy; $y >= 2020; $y--) {
                                    $lbl = 'FY ' . $y . '-' . substr($y + 1, -2);
                                    $sel = ($y == $fy_year) ? 'selected' : '';
                                    echo "<option value=\"$y\" $sel>$lbl</option>";
                                }
                                ?>
                            </select>
                        </form>
                    </li>
                </ol>
            </section>

            <!-- ── Financial Year Summary Strip ───────────────────────────── -->
            <?php if (in_array("Financial_Summary", $session_data_head['permission'])) { ?>
            <section class="content" style="padding-bottom:0;">
                <?php $fy_net = $total_invoice_amount - $total_po_amount - $fy_total_expense; ?>
                <!-- FY top header bar -->
                <div style="background:linear-gradient(135deg,#1a6496 0%,#286090 100%); border-radius:6px; padding:0; margin-top:30px; color:#fff; overflow:hidden;">

                    <!-- Title row -->
                    <div style="background:rgba(0,0,0,0.15); padding:8px 20px; font-size:13px; font-weight:700; letter-spacing:1px; text-transform:uppercase; display:flex; justify-content:space-between; align-items:center;">
                        <span><i class="fa fa-bar-chart" style="margin-right:6px;"></i><?php echo $fy_label; ?> &mdash; Financial Summary</span>
                    </div>

                    <!-- Figures row -->
                    <div style="display:flex; flex-wrap:wrap;">

                        <!-- Sales -->
                        <div style="flex:1; min-width:150px; text-align:center; border-right:1px solid rgba(255,255,255,0.2); padding:14px 10px;">
                            <div style="font-size:10px; text-transform:uppercase; letter-spacing:1px; opacity:.75; margin-bottom:4px;">
                                <i class="fa fa-arrow-up" style="color:#a8ffb0;"></i> Sales / Revenue
                            </div>
                            <div style="font-size:22px; font-weight:700;">&#8377;<?php echo indian_number_format($total_invoice_amount, 2); ?></div>
                            <div style="font-size:11px; opacity:.7; margin-top:2px;"><?php echo $invoice_count; ?> Invoices</div>
                        </div>

                        <!-- Purchase -->
                        <div style="flex:1; min-width:150px; text-align:center; border-right:1px solid rgba(255,255,255,0.2); padding:14px 10px;">
                            <div style="font-size:10px; text-transform:uppercase; letter-spacing:1px; opacity:.75; margin-bottom:4px;">
                                <i class="fa fa-arrow-down" style="color:#ffb3b3;"></i> Purchase
                            </div>
                            <div style="font-size:22px; font-weight:700;">&#8377;<?php echo indian_number_format($total_po_amount, 2); ?></div>
                            <div style="font-size:11px; opacity:.7; margin-top:2px;"><?php echo $po_count; ?> Vouchers</div>
                        </div>

                        <!-- Expense -->
                        <div style="flex:1; min-width:150px; text-align:center; border-right:1px solid rgba(255,255,255,0.2); padding:14px 10px;">
                            <div style="font-size:10px; text-transform:uppercase; letter-spacing:1px; opacity:.75; margin-bottom:4px;">
                                <i class="fa fa-arrow-down" style="color:#ffb3b3;"></i> Expense
                            </div>
                            <div style="font-size:22px; font-weight:700;">&#8377;<?php echo indian_number_format($fy_total_expense, 2); ?></div>
                            <div style="font-size:11px; opacity:.7; margin-top:2px;">
                                D: &#8377;<?php echo indian_number_format($fy_direct_expense ?? 0, 2); ?> + I: &#8377;<?php echo indian_number_format($fy_indirect_expense ?? 0, 2); ?>
                            </div>
                        </div>

                        <!-- Divider formula label -->

                        <!-- Profit / Loss -->
                        <div style="flex:1.2; min-width:180px; text-align:center; padding:14px 10px; background:rgba(0,0,0,0.12);">
                            <div style="font-size:10px; text-transform:uppercase; letter-spacing:1px; opacity:.75; margin-bottom:4px;">
                                <?php if ($fy_net >= 0): ?>
                                    <i class="fa fa-thumbs-up" style="color:#a8ffb0;"></i> Net Profit
                                <?php else: ?>
                                    <i class="fa fa-thumbs-down" style="color:#ffb3b3;"></i> Net Loss
                                <?php endif; ?>
                            </div>
                            <div style="font-size:26px; font-weight:700; color:<?php echo $fy_net >= 0 ? '#a8ffb0' : '#ffb3b3'; ?>;">
                                <?php echo $fy_net < 0 ? '&minus;' : ''; ?>&#8377;<?php echo indian_number_format(abs($fy_net), 2); ?>
                            </div>
                            <div style="font-size:11px; opacity:.7; margin-top:2px;">Sales &minus; Purchase &minus; Expense</div>
                        </div>

                    </div>
                </div>
            </section>
            <?php } // end Financial_Summary permission check ?>

            <!--check role-->
            <?php 
            $has_admin_dashboard = in_array("Financial_Summary", $session_data_head['permission']) || 
                                    in_array("Business_Overview", $session_data_head['permission']) ||
                                    in_array("Procurement_Dashboard", $session_data_head['permission']);

            if ($has_admin_dashboard) { 
            ?>
                <!-- Main content -->
                <section class="content">
                    <!-- Small boxes (Stat box) -->


                    <?php
                    // ... existing session and header code ...

                    // Add these variables to your controller
                    // Make sure to pass these from your controller:
                    // $pr_count, $rfq_count, $po_count, $pr_data, $rfq_data, $po_data, etc.
                    ?>

                    <!-- Add this section after your existing dashboard sections -->
                    <?php if (in_array("Procurement_Dashboard", $session_data_head['permission'])) { ?>
                    <div class="row">
                       
                    </div>
                    <?php } ?>

                    <!-- Add this JavaScript at the bottom of your file -->
                    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                    <script>
                        $(document).ready(function() {
                            // Procurement Chart
                            var ctx = document.getElementById('procurementChart').getContext('2d');
                            var procurementChart = new Chart(ctx, {
                                type: 'bar',
                                data: {
                                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                                    datasets: [{
                                            label: 'Purchase Requisitions',
                                            data: <?php echo json_encode($monthly_pr_data); ?>,
                                            backgroundColor: 'rgba(153, 102, 255, 0.8)',
                                            borderColor: 'rgba(153, 102, 255, 1)',
                                            borderWidth: 1
                                        },
                                        {
                                            label: 'RFQs',
                                            data: <?php echo json_encode($monthly_rfq_data); ?>,
                                            backgroundColor: 'rgba(0, 166, 90, 0.8)',
                                            borderColor: 'rgba(0, 166, 90, 1)',
                                            borderWidth: 1
                                        },
                                        {
                                            label: 'Purchase Orders',
                                            data: <?php echo json_encode($monthly_po_data); ?>,
                                            backgroundColor: 'rgba(255, 159, 64, 0.8)',
                                            borderColor: 'rgba(255, 159, 64, 1)',
                                            borderWidth: 1
                                        }
                                    ]
                                },
                                options: {
                                    responsive: true,
                                    scales: {
                                        y: {
                                            beginAtZero: true,
                                            title: {
                                                display: true,
                                                text: 'Count'
                                            }
                                        },
                                        x: {
                                            title: {
                                                display: true,
                                                text: 'Months'
                                            }
                                        }
                                    },
                                    plugins: {
                                        legend: {
                                            position: 'top',
                                        }
                                    }
                                }
                            });
                        });
                    </script>


                    <?php if (in_array("Business_Overview", $session_data_head['permission'])) { ?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="box box-success">
                                <div class="box-header with-border">
                                    <h3 class="box-title">Business Overview &mdash; <?php echo $fy_label; ?></h3>

                                    <div class="box-tools pull-right">
                                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="box-body no-padding">


                                    <div class="row">

                                        <div class="fixed-col">
                                            <!-- small box -->
                                            <div class="small-box bg-blue-gradient">
                                                <div class="inner">
                                                    <h3><?php echo $inventory_count; ?></h3>

                                                    <p>Total Inventory - <?php echo indian_number_format($total_inventory_amount, 2); ?></p>
                                                </div>
                                                <div class="icon">
                                                    <i class="ion ion-bag"></i>
                                                </div>
                                                <a href="<?php echo base_url(); ?>InventoryController/index" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                                            </div>
                                        </div>
                                        <div class="fixed-col">
                                            <!-- small box -->
                                            <div class="small-box bg-aqua">
                                                <div class="inner">
                                                    <h3><?php echo $invoice_count; ?></h3>

                                                    <p>Total Invoices - <?php echo indian_number_format($total_invoice_amount, 2); ?></p>
                                                </div>
                                                <div class="icon">
                                                    <i class="ion ion-document"></i>
                                                </div>
                                                <a href="<?php echo base_url(); ?>InvoiceController/index" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                                            </div>
                                        </div>

                                        <div class="fixed-col">
                                            <!-- small box -->
                                            <div class="small-box bg-purple">
                                                <div class="inner">
                                                    <h3><?php echo $purchase_order_count; ?></h3>

                                                    <p>Total Purchase Order - <?php echo indian_number_format($purchase_order_total_amount, 2); ?></p>
                                                </div>
                                                <div class="icon">
                                                    <i class="ion ion-android-apps"></i>
                                                </div>
                                                <a href="<?php echo base_url(); ?>SupplierController/view_purchase_order" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                                            </div>
                                        </div>




                                        <div class="fixed-col">
                                            <!-- small box -->
                                            <div class="small-box bg-teal">
                                                <div class="inner">
                                                    <h3><?php echo $po_count; ?></h3>

                                                    <p>Total Purchase Vouchers - <?php echo indian_number_format($total_po_amount, 2); ?></p>
                                                </div>
                                                <div class="icon">
                                                    <i class="ion ion-android-bulb"></i>
                                                </div>
                                                <a href="<?php echo base_url(); ?>SupplierController/view_purchase_bill" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                                            </div>
                                        </div>





                                        <!-- ./col -->


                                        <div class="fixed-col">
                                            <!-- small box -->
                                            <div class="small-box bg-green">
                                                <div class="inner">
                                                    <h3><?php echo $quotation_count; ?></h3>

                                                    <p>Total GST Quotations</p>
                                                </div>
                                                <div class="icon">
                                                    <i class="ion ion-clipboard"></i>
                                                </div>
                                                <a href="<?php echo base_url(); ?>EstimateController/index" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                                            </div>
                                        </div>
                                        <!-- ./col -->
                                        <div class="fixed-col">
                                            <!-- small box -->
                                            <div class="small-box bg-orange">
                                                <div class="inner">
                                                    <h3><?php echo $customer_count; ?></h3>
                                                    <p>Total Customer</p>
                                                </div>
                                                <div class="icon">
                                                    <i class="ion ion-person-add"></i>
                                                </div>
                                                <a href="<?php echo base_url(); ?>CustomerController/index" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                                            </div>
                                        </div>
                                        <!-- ./col -->
                                        <div class="fixed-col">
                                            <!-- small box -->
                                            <div class="small-box bg-red">
                                                <div class="inner">
                                                    <h3><?php echo $supplier_count; ?></h3>
                                                    <p>Total Vendor</p>
                                                </div>
                                                <div class="icon">
                                                    <i class="ion ion-person-stalker"></i>
                                                </div>
                                                <a href="<?php echo base_url(); ?>SupplierController/index" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                                            </div>
                                        </div>

                                        									<div class="fixed-col">
                                            <!-- small box -->
                                            <div class="small-box bg-aqua">
                                                <div class="inner">
                                                    <h3><?php echo $proforma_count; ?></h3>

                                                    <p>Total Proforma</p>
                                                </div>
                                                <div class="icon">
                                                    <i class="ion ion-document"></i>
                                                </div>
                                                <a href="<?php echo base_url(); ?>ProformaInvoiceController/index" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                                            </div>
                                        </div>

                                        <div class="fixed-col hide">
                                            <!-- small box -->
                                            <div class="small-box bg-success">
                                                <div class="inner">
                                                    <h3><?php echo $grn_count; ?></h3>
                                                    <p>Total GRN</p>
                                                </div>
                                                <div class="icon">
                                                    <i class="ion ion-star"></i>
                                                </div>
                                                <a href="<?php echo base_url(); ?>GrnController/grn_index" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                                            </div>
                                        </div>

                                        <div class="fixed-col hide">
                                            <!-- small box -->
                                            <div class="small-box bg-purple">
                                                <div class="inner">
                                                    <h3><?php echo $po_count; ?></h3>
                                                    <p>Total Purchase Order</p>
                                                </div>
                                                <div class="icon">
                                                    <i class="ion ion-android-apps"></i>
                                                </div>
                                                <a href="<?php echo base_url(); ?>SupplierController/view_purchase_order" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                                            </div>
                                        </div>

                                        <!-- ./col -->
                                    </div>
                                    <!-- /.row -->




                                </div>
                            </div>
                        </div>
                    </div>
                    <?php } ?>



                    <?php if (in_array("Monthly_Sales", $session_data_head['permission'])) { ?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="box box-success">
                                <div class="box-header with-border">
                                    <h3 class="box-title">Monthly Sales &mdash; <?php echo $fy_label; ?></h3>

                                    <div class="box-tools pull-right">
                                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                        </button>
                                    </div>
                                </div>
                                <!--                                //.box-header -->
                                <div class="box-body no-padding">
                                    <div class="row">

                                        <div class="col-md-11 col-sm-8">
                                            <div class="pad">

                                                <strong>
                                                    <div id="info3" class="text-uppercase"></div>
                                                </strong>


                                                <div id="chart5"></div>
                                            </div>
                                        </div>


                                    </div>
                                </div>


                            </div>
                        </div>
                    </div>.
                    <?php } ?>


                    <?php if (in_array("Purchase_And_Expenses", $session_data_head['permission'])) { ?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="box box-success">
                                <div class="box-header with-border">
                                    <h3 class="box-title">Purchase &amp; Expenses &mdash; <?php echo $fy_label; ?></h3>

                                    <div class="box-tools pull-right">
                                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                        </button>
                                    </div>
                                </div>
                                <!--                                //.box-header -->
                                <div class="box-body no-padding">
                                    <div class="row">

                                        <div class="col-md-11 col-sm-8">
                                            <div class="pad">

                                                <strong>
                                                    <div id="info2" class="text-uppercase"></div>
                                                </strong>


                                                <div id="chart6"></div>
                                            </div>
                                        </div>


                                    </div>
                                </div>


                            </div>
                        </div>
                    </div>
                    <?php } ?>





                    
                            <!-- MAP & BOX PANE -->
                            <!--                            <div class="box box-success">
                                <div class="box-header with-border">
                                    <h3 class="box-title"><i class="fa fa-bar-chart fa-margin"></i>Quotation &nbsp;&nbsp;</h3>

                                    <div class="box-tools pull-right">

                                            <button type="button"  class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
    </button>
                                    </div>
                                </div>
                                 /.box-header 
                                <div class="box-body no-padding">
                                    <div class="row">
                                        <div class="col-md-9 col-sm-8">
                                            <div class="pad">
                                                <div id="panel-invoice-overview" class="panel panel-default overview">

                                                    <table class="table table-bordered table-condensed no-margin">
                                                        <tbody><tr>
                                                                <td>
                                                                    <input name="draft" id="draft" class="form-control datepicker_filter" class="form-control" type="hidden">
                                                                    <a href="<?php echo base_url(); ?>EstimateController/get_quotation_data_by_status/1">
                                                                        Draft                                </a>
                                                                </td>
                                                                <td class="amount">
                                                                    <span class="draft">
                                                                        ₹<?php echo indian_number_format($draft, 2); ?>                       </span>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <a href="<?php echo base_url(); ?>EstimateController/get_quotation_data_by_status/2">
                                                                        Sent                                </a>
                                                                </td>
                                                                <td class="amount">
                                                                    <span class="sent">
                                                                        ₹<?php echo indian_number_format($sent, 2); ?>                      </span>
                                                                </td>
                                                            </tr>



                                                            <tr>
                                                                <td>
                                                                    <a href="<?php echo base_url(); ?>EstimateController/index">
                                                                        All                                </a>
                                                                </td>

                                                            </tr>

                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                          111  </div>-->
                        </div>
                        <div class="col-md-3">
                            <!-- MAP & BOX PANE -->
                            <!--                            <div class="box box-success">
                                <div class="box-header with-border">

                                    <h3 class="box-title"><i class="fa fa-bar-chart fa-margin"></i>Invoice</h3>

                                    <div class="box-tools pull-right">  
                                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                        </button>
                                    </div>
                                </div>
                                 /.box-header 
                                <div class="box-body no-padding">
                                    <div class="row">
                                        <div class="col-md-9 col-sm-8">
                                            <div class="pad">

                                                <div id="panel-invoice-overview" class="panel panel-default overview">

                                                    <table class="table table-bordered table-condensed no-margin">
                                                        <tbody><tr>
                                                                <td>
                                                                    <a href="<?php echo base_url(); ?>InvoiceController/get_invoice_data_by_status/1">
                                                                        Draft                                </a>
                                                                </td>
                                                                <td class="amount">
                                                                    <span class="draft">
                                                                        ₹<?php echo indian_number_format($draft1, 2); ?>                          </span>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <a href="<?php echo base_url(); ?>InvoiceController/get_invoice_data_by_status/2">
                                                                        Sent                                </a>
                                                                </td>
                                                                <td class="amount">
                                                                    <span class="sent">
                                                                        ₹<?php echo indian_number_format($sent1, 2); ?>                        </span>
                                                                </td>
                                                            </tr>

                                                            <tr>
                                                                <td>
                                                                    <a href="<?php echo base_url(); ?>InvoiceController/index">
                                                                        All                                </a>
                                                                </td>

                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                          11  </div>-->
                        </div>
                        <div class="col-md-3 hide">
                            <!-- MAP & BOX PANE -->
                            <div class="box box-success">
                                <div class="box-header with-border">
                                    <h3 class="box-title"><i class="fa fa-bar-chart fa-margin"></i> Non GST Quotation Overview&nbsp;&nbsp;&nbsp;</h3>
                                    <div class="box-tools pull-right">
                                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                        </button>
                                    </div>
                                </div>
                                <!-- /.box-header -->
                                <div class="box-body no-padding">
                                    <div class="row">
                                        <div class="col-md-9 col-sm-8">
                                            <div class="pad">
                                                <div id="panel-invoice-overview" class="panel panel-default overview">

                                                    <table class="table table-bordered table-condensed no-margin">
                                                        <tbody>
                                                            <tr>
                                                                <td>
                                                                    <input name="draft" id="draft" class="form-control datepicker_filter" class="form-control" type="hidden">
                                                                    <a href="<?php echo base_url(); ?>EstimateController/get_ng_quotation_data_by_status/1">
                                                                        Draft </a>
                                                                </td>
                                                                <td class="amount">
                                                                    <span class="draft">
                                                                        ₹<?php echo indian_number_format(isset($draft3) ? $draft3 : 0, 2); ?> </span>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <a href="<?php echo base_url(); ?>EstimateController/get_quotation_data_by_status/2">
                                                                        Sent </a>
                                                                </td>
                                                                <td class="amount">
                                                                    <span class="sent">
                                                                        ₹<?php echo indian_number_format(isset($sent3) ? $sent3 : 0, 2); ?> </span>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <a href="<?php echo base_url(); ?>EstimateController/get_ng_quotation_data_by_status/3">
                                                                        Viewed </a>
                                                                </td>
                                                                <td class="amount">
                                                                    <span class="viewed">
                                                                        ₹<?php echo indian_number_format(isset($viewed3) ? $viewed3 : 0, 2); ?> </span>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <a href="<?php echo base_url(); ?>EstimateController/get_ng_quotation_data_by_status/4">
                                                                        Approved </a>
                                                                </td>
                                                                <td class="amount">
                                                                    <span class="paid">
                                                                        ₹<?php echo indian_number_format(isset($approved3) ? $approved3 : 0, 2); ?> </span>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <a href="<?php echo base_url(); ?>EstimateController/get_ng_quotation_data_by_status/5">
                                                                        Rejected </a>
                                                                </td>
                                                                <td class="amount">
                                                                    <span class="paid">
                                                                        ₹<?php echo indian_number_format(isset($rejected3) ? $rejected3 : 0, 2); ?> </span>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <a href="<?php echo base_url(); ?>EstimateController/get_ng_quotation_data_by_status/6">
                                                                        Canceled </a>
                                                                </td>
                                                                <td class="amount">

                                                                    <span class="paid">
                                                                        ₹<?php echo indian_number_format(isset($canceled3) ? $canceled3 : 0, 2); ?> </span>
                                                                </td>
                                                            </tr>

                                                            <tr>
                                                                <td>
                                                                    <a href="<?php echo base_url(); ?>EstimateController/non_gst_index">
                                                                        All </a>
                                                                </td>

                                                            </tr>

                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 hide">
                            <!-- MAP & BOX PANE -->
                            <div class="box box-success">
                                <div class="box-header with-border">
                                    <h3 class="box-title"><i class="fa fa-bar-chart fa-margin"></i> Non GST Invoice Overview&nbsp;&nbsp;&nbsp;</h3>

                                    <div class="box-tools pull-right">
                                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                        </button>
                                    </div>
                                </div>
                                <!-- /.box-header -->
                                <div class="box-body no-padding">
                                    <div class="row">
                                        <div class="col-md-9 col-sm-8">
                                            <div class="pad">

                                                <div id="panel-invoice-overview" class="panel panel-default overview">

                                                    <table class="table table-bordered table-condensed no-margin">
                                                        <tbody>
                                                            <tr>
                                                                <td>
                                                                    <a href="<?php echo base_url(); ?>InvoiceController/get_non_gst_invoice_status_count/1">
                                                                        Draft </a>
                                                                </td>
                                                                <td class="amount">
                                                                    <span class="draft">
                                                                        ₹<?php echo indian_number_format(isset($draft4) ? $draft4 : 0, 2); ?> </span>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <a href="<?php echo base_url(); ?>InvoiceController/get_non_gst_invoice_status_count/2">
                                                                        Sent </a>
                                                                </td>
                                                                <td class="amount">
                                                                    <span class="sent">
                                                                        ₹<?php echo indian_number_format(isset($sent4) ? $sent4 : 0, 2); ?> </span>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <a href="<?php echo base_url(); ?>InvoiceController/get_non_gst_invoice_status_count/3">
                                                                        Viewed </a>
                                                                </td>
                                                                <td class="amount">
                                                                    <span class="viewed">
                                                                        ₹<?php echo indian_number_format(isset($viewed4) ? $viewed4 : 0, 2); ?> </span>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <a href="<?php echo base_url(); ?>InvoiceController/get_non_gst_invoice_status_count/4">
                                                                        Approved </a>
                                                                </td>
                                                                <td class="amount">
                                                                    <span class="paid">
                                                                        ₹<?php echo indian_number_format(isset($approved4) ? $approved4 : 0, 2); ?> </span>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <a href="<?php echo base_url(); ?>InvoiceController/get_non_gst_invoice_status_count/5">
                                                                        Rejected </a>
                                                                </td>
                                                                <td class="amount">
                                                                    <span class="paid">
                                                                        ₹<?php echo indian_number_format(isset($rejected4) ? $rejected4 : 0, 2); ?> </span>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <a href="<?php echo base_url(); ?>InvoiceController/get_non_gst_invoice_status_count/6">
                                                                        Canceled </a>
                                                                </td>
                                                                <td class="amount">
                                                                    <span class="paid">
                                                                        ₹<?php echo indian_number_format(isset($canceled4) ? $canceled4 : 0, 2); ?> </span>
                                                                </td>
                                                            </tr>

                                                            <tr>
                                                                <td>
                                                                    <a href="<?php echo base_url(); ?>InvoiceController/index_non_gst">
                                                                        All </a>
                                                                </td>

                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <!-- /.content -->
            <?php } else { 
                $role_name = $session_data_head['result']['role_name'] ?? '';
                $user_fullname = $session_data_head['result']['username'] ?? 'User';
            ?>
                <!-- Custom Dashboard for Departments / Roles without Admin Permissions -->
                <section class="content">
                    <div class="row" style="margin-top: 35px; clear: both;">
                        <div class="col-md-12">
                            <div style="background: linear-gradient(135deg, #1a6496 0%, #286090 100%); border-radius: 6px; padding: 15px 20px; margin-bottom: 20px; color: #fff; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
                                <h4 style="margin: 0; font-weight: 700;"><i class="fa fa-dashboard" style="margin-right: 10px;"></i>Welcome, <?php echo $user_fullname; ?>!</h4>
                                <p style="margin: 5px 0 0 0; opacity: .85; font-size: 13px;">Here is the summary dashboard for the <strong><?php echo $role_name; ?></strong> department.</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <?php if (strpos(strtolower($role_name), 'store') !== false) { ?>
                            <!-- Store & Inventory Dashboard -->
                            <div class="col-lg-3 col-xs-6">
                                <div class="small-box bg-aqua">
                                    <div class="inner">
                                        <h3><?php echo $inventory_count; ?></h3>
                                        <p>Total Stock Items</p>
                                    </div>
                                    <div class="icon"><i class="fa fa-cubes"></i></div>
                                    <a href="<?php echo base_url(); ?>InventoryController/index" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                                </div>
                            </div>
                            <div class="col-lg-3 col-xs-6">
                                <div class="small-box bg-green">
                                    <div class="inner">
                                        <h3>&#8377;<?php echo indian_number_format($total_inventory_amount, 2); ?></h3>
                                        <p>Inventory Value</p>
                                    </div>
                                    <div class="icon"><i class="fa fa-money"></i></div>
                                    <a href="<?php echo base_url(); ?>InventoryController/index" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                                </div>
                            </div>
                            <div class="col-lg-3 col-xs-6">
                                <div class="small-box bg-red">
                                    <div class="inner">
                                        <h3><?php echo $low_stock_count; ?></h3>
                                        <p>Low Stock Items</p>
                                    </div>
                                    <div class="icon"><i class="fa fa-exclamation-triangle"></i></div>
                                    <a href="<?php echo base_url(); ?>MaterialIssueController/low_stock" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                                </div>
                            </div>
                            <div class="col-lg-3 col-xs-6">
                                <div class="small-box bg-yellow">
                                    <div class="inner">
                                        <h3><?php echo $pending_grns; ?></h3>
                                        <p>Pending GRNs</p>
                                    </div>
                                    <div class="icon"><i class="fa fa-truck"></i></div>
                                    <a href="<?php echo base_url(); ?>GrnController/grn_approvals" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                                </div>
                            </div>

                        <?php } elseif (strpos(strtolower($role_name), 'design') !== false || strpos(strtolower($role_name), 'engineering') !== false) { ?>
                            <!-- Design & Engineering Dashboard -->
                            <div class="col-lg-4 col-xs-12">
                                <div class="small-box bg-aqua">
                                    <div class="inner">
                                        <h3><?php echo $total_boms_count; ?></h3>
                                        <p>Total BOMs</p>
                                    </div>
                                    <div class="icon"><i class="fa fa-cogs"></i></div>
                                    <a href="<?php echo base_url(); ?>BomController/index" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                                </div>
                            </div>
                            <div class="col-lg-4 col-xs-12">
                                <div class="small-box bg-yellow">
                                    <div class="inner">
                                        <h3><?php echo $pending_boms_count; ?></h3>
                                        <p>Pending BOM Approvals</p>
                                    </div>
                                    <div class="icon"><i class="fa fa-clock-o"></i></div>
                                    <a href="<?php echo base_url(); ?>BomController/bom_approval_dashboard" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                                </div>
                            </div>
                            <div class="col-lg-4 col-xs-12">
                                <div class="small-box bg-blue">
                                    <div class="inner">
                                        <h3><?php echo $total_drawings_count; ?></h3>
                                        <p>Total Drawings</p>
                                    </div>
                                    <div class="icon"><i class="fa fa-file-image-o"></i></div>
                                    <a href="<?php echo base_url(); ?>DrawingController/show_drawing" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                                </div>
                            </div>

                        <?php } elseif (strpos(strtolower($role_name), 'quality') !== false) { ?>
                            <!-- Quality Control Dashboard -->
                            <div class="col-lg-4 col-xs-12">
                                <div class="small-box bg-yellow">
                                    <div class="inner">
                                        <h3><?php echo $pending_grn_inspections; ?></h3>
                                        <p>Pending Inspections</p>
                                    </div>
                                    <div class="icon"><i class="fa fa-clock-o"></i></div>
                                    <a href="<?php echo base_url(); ?>GrnController/conduct_inspection" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                                </div>
                            </div>
                            <div class="col-lg-4 col-xs-12">
                                <div class="small-box bg-green">
                                    <div class="inner">
                                        <h3><?php echo $approved_grn_inspections; ?></h3>
                                        <p>Approved Inspections</p>
                                    </div>
                                    <div class="icon"><i class="fa fa-check-circle"></i></div>
                                    <a href="<?php echo base_url(); ?>GrnController/conduct_inspection" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                                </div>
                            </div>
                            <div class="col-lg-4 col-xs-12">
                                <div class="small-box bg-purple">
                                    <div class="inner">
                                        <h3><?php echo $purchase_return_count ?? 0; ?></h3>
                                        <p>Returnable Challans</p>
                                    </div>
                                    <div class="icon"><i class="fa fa-exchange"></i></div>
                                    <a href="<?php echo base_url(); ?>SupplierController/view_purchase_return" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                                </div>
                            </div>

                        <?php } elseif (strpos(strtolower($role_name), 'production') !== false) { ?>
                            <!-- Production Dashboard -->
                            <div class="col-lg-4 col-xs-12">
                                <div class="small-box bg-blue">
                                    <div class="inner">
                                        <h3><?php echo $total_job_orders; ?></h3>
                                        <p>Total Job Orders</p>
                                    </div>
                                    <div class="icon"><i class="fa fa-industry"></i></div>
                                    <a href="<?php echo base_url(); ?>JobOrderController/index" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                                </div>
                            </div>
                            <div class="col-lg-4 col-xs-12">
                                <div class="small-box bg-yellow">
                                    <div class="inner">
                                        <h3><?php echo $pending_job_orders; ?></h3>
                                        <p>Pending Job Orders</p>
                                    </div>
                                    <div class="icon"><i class="fa fa-hourglass-start"></i></div>
                                    <a href="<?php echo base_url(); ?>JobOrderController/index" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                                </div>
                            </div>
                            <div class="col-lg-4 col-xs-12">
                                <div class="small-box bg-green">
                                    <div class="inner">
                                        <h3><?php echo $completed_job_orders; ?></h3>
                                        <p>Completed Job Orders</p>
                                    </div>
                                    <div class="icon"><i class="fa fa-check-circle"></i></div>
                                    <a href="<?php echo base_url(); ?>JobOrderController/index" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                                </div>
                            </div>

                        <?php } elseif (strpos(strtolower($role_name), 'sales') !== false || strpos(strtolower($user_fullname ?? ''), 'karthik') !== false || strpos(strtolower($role_name), 'karthik') !== false) { ?>
                            <!-- Sales Dashboard -->
                            <div class="col-lg-4 col-xs-6">
                                <div class="small-box bg-yellow">
                                    <div class="inner">
                                        <h3><?php echo $customer_count; ?></h3>
                                        <p>Total Customers</p>
                                    </div>
                                    <div class="icon"><i class="ion ion-person-add"></i></div>
                                    <a href="<?php echo base_url(); ?>CustomerController/index" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                                </div>
                            </div>
                            <div class="col-lg-4 col-xs-6">
                                <div class="small-box bg-aqua">
                                    <div class="inner">
                                        <h3><?php echo $quotation_count; ?></h3>
                                        <p>Total Quotations</p>
                                    </div>
                                    <div class="icon"><i class="fa fa-file-text-o"></i></div>
                                    <a href="<?php echo base_url(); ?>EstimateController/index" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                                </div>
                            </div>
                            <div class="col-lg-4 col-xs-6">
                                <div class="small-box bg-green">
                                    <div class="inner">
                                        <h3><?php echo $salesorder_count ?? 0; ?></h3>
                                        <p>Sales Orders</p>
                                    </div>
                                    <div class="icon"><i class="fa fa-shopping-cart"></i></div>
                                    <a href="<?php echo base_url(); ?>SalesOrderController/index" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                                </div>
                            </div>

                        <?php } elseif (strpos(strtolower($role_name), 'purchase') !== false || strpos(strtolower($role_name), 'buyer') !== false || strpos(strtolower($role_name), 'procurement') !== false) { ?>
                            <!-- Purchase & Procurement Dashboard -->
                            
                            <!-- Row 1: Purchase Operations -->
                            <div class="col-xs-12" style="margin-bottom: 15px;">
                                <h4 style="font-weight: 600; color: #475569; margin: 0; display: flex; align-items: center; gap: 8px;">
                                    <i class="fa fa-shopping-cart" style="color: #64748b;"></i> Purchase Operations
                                </h4>
                                <hr style="margin: 8px 0 0 0; border-top: 1px solid #cbd5e1;">
                            </div>
                            
                            <div class="col-lg-3 col-xs-6">
                                <div class="small-box bg-purple">
                                    <div class="inner">
                                        <h3><?php echo $pr_count; ?></h3>
                                        <p>Purchase Requisitions</p>
                                    </div>
                                    <div class="icon"><i class="fa fa-file-text-o"></i></div>
                                    <a href="<?php echo base_url(); ?>RequisitionController/index" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                                </div>
                            </div>
                            <div class="col-lg-3 col-xs-6">
                                <div class="small-box bg-aqua">
                                    <div class="inner">
                                        <h3><?php echo $rfq_count; ?></h3>
                                        <p>RFQ Count</p>
                                    </div>
                                    <div class="icon"><i class="fa fa-comments-o"></i></div>
                                    <a href="<?php echo base_url(); ?>RFQController/index" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                                </div>
                            </div>
                            <div class="col-lg-3 col-xs-6">
                                <div class="small-box bg-green">
                                    <div class="inner">
                                        <h3><?php echo $po_count; ?></h3>
                                        <p>Purchase Orders</p>
                                    </div>
                                    <div class="icon"><i class="fa fa-file-powerpoint-o"></i></div>
                                    <a href="<?php echo base_url(); ?>SupplierController/view_purchase_order" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                                </div>
                            </div>
                            <div class="col-lg-3 col-xs-6">
                                <div class="small-box bg-yellow">
                                    <div class="inner">
                                        <h3><?php echo $supplier_count; ?></h3>
                                        <p>Total Suppliers</p>
                                    </div>
                                    <div class="icon"><i class="ion ion-person-add"></i></div>
                                    <a href="<?php echo base_url(); ?>SupplierController/index" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                                </div>
                            </div>

                            <div class="clearfix"></div>
                            
                            <!-- Row 2: Inventory & Delivery Visibility -->
                            <div class="col-xs-12" style="margin-top: 15px; margin-bottom: 15px;">
                                <h4 style="font-weight: 600; color: #475569; margin: 0; display: flex; align-items: center; gap: 8px;">
                                    <i class="fa fa-cubes" style="color: #64748b;"></i> Inventory &amp; Delivery Visibility
                                </h4>
                                <hr style="margin: 8px 0 0 0; border-top: 1px solid #cbd5e1;">
                            </div>
                            
                            <!-- Low Stock Card -->
                            <div class="col-lg-3 col-xs-6">
                                <div class="small-box" style="background: linear-gradient(135deg, #f97316, #ea580c) !important; color: #ffffff;">
                                    <div class="inner" style="color: #ffffff;">
                                        <h3 style="color: #ffffff;"><?php echo $low_stock_count; ?></h3>
                                        <p style="color: #ffffff; font-weight: 500;">Low Stock Items</p>
                                    </div>
                                    <div class="icon"><i class="fa fa-exclamation-triangle" style="color: rgba(255,255,255,0.15);"></i></div>
                                    <a href="<?php echo base_url(); ?>MaterialIssueController/low_stock" class="small-box-footer" style="color: rgba(255,255,255,0.8); background: rgba(0,0,0,0.1);">More info <i class="fa fa-arrow-circle-right"></i></a>
                                </div>
                            </div>
                            
                            <!-- Pending PO Deliveries Card -->
                            <div class="col-lg-3 col-xs-6">
                                <div class="small-box" style="background: linear-gradient(135deg, #3b82f6, #1d4ed8) !important; color: #ffffff;">
                                    <div class="inner" style="color: #ffffff;">
                                        <h3 style="color: #ffffff;"><?php echo $pending_po_deliveries_count; ?></h3>
                                        <p style="color: #ffffff; font-weight: 500;">Pending PO Deliveries</p>
                                    </div>
                                    <div class="icon"><i class="fa fa-truck" style="color: rgba(255,255,255,0.15);"></i></div>
                                    <a href="<?php echo base_url(); ?>SupplierController/view_purchase_order" class="small-box-footer" style="color: rgba(255,255,255,0.8); background: rgba(0,0,0,0.1);">More info <i class="fa fa-arrow-circle-right"></i></a>
                                </div>
                            </div>
                            
                            <!-- GRN Received Today Card -->
                            <div class="col-lg-3 col-xs-6">
                                <div class="small-box" style="background: linear-gradient(135deg, #0d9488, #0f766e) !important; color: #ffffff;">
                                    <div class="inner" style="color: #ffffff;">
                                        <h3 style="color: #ffffff;"><?php echo $grn_today_count; ?></h3>
                                        <p style="color: #ffffff; font-weight: 500;">GRNs Received Today</p>
                                    </div>
                                    <div class="icon"><i class="fa fa-clipboard" style="color: rgba(255,255,255,0.15);"></i></div>
                                    <a href="<?php echo base_url(); ?>GrnController/grn_index" class="small-box-footer" style="color: rgba(255,255,255,0.8); background: rgba(0,0,0,0.1);">More info <i class="fa fa-arrow-circle-right"></i></a>
                                </div>
                            </div>
                            
                            <!-- Inventory Value Card -->
                            <div class="col-lg-3 col-xs-6">
                                <div class="small-box" style="background: linear-gradient(135deg, #10b981, #047857) !important; color: #ffffff;">
                                    <div class="inner" style="color: #ffffff;">
                                        <h3 style="color: #ffffff;">
                                            <?php 
                                            if ($inventory_total_value >= 10000000) {
                                                echo '₹' . number_format($inventory_total_value / 10000000, 2) . ' Cr';
                                            } elseif ($inventory_total_value >= 100000) {
                                                echo '₹' . number_format($inventory_total_value / 100000, 2) . ' L';
                                            } else {
                                                echo '₹' . number_format($inventory_total_value, 2);
                                            }
                                            ?>
                                        </h3>
                                        <p style="color: #ffffff; font-weight: 500;">Inventory Value (Cost)</p>
                                    </div>
                                    <div class="icon"><i class="fa fa-inr" style="color: rgba(255,255,255,0.15);"></i></div>
                                    <a href="<?php echo base_url(); ?>InventoryController/index" class="small-box-footer" style="color: rgba(255,255,255,0.8); background: rgba(0,0,0,0.1);">More info <i class="fa fa-arrow-circle-right"></i></a>
                                </div>
                            </div>

                        <?php } elseif (strpos(strtolower($role_name), 'account') !== false) { ?>
                            <!-- Accounts Dashboard -->
                            <div class="col-lg-3 col-xs-6">
                                <div class="small-box bg-green">
                                    <div class="inner">
                                        <h3><?php echo $invoice_count; ?></h3>
                                        <p>Sales Invoices</p>
                                    </div>
                                    <div class="icon"><i class="fa fa-file-text-o"></i></div>
                                    <a href="<?php echo base_url(); ?>InvoiceController/index" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                                </div>
                            </div>
                            <div class="col-lg-3 col-xs-6">
                                <div class="small-box bg-purple">
                                    <div class="inner">
                                        <h3><?php echo $proforma_count; ?></h3>
                                        <p>Proforma Invoices</p>
                                    </div>
                                    <div class="icon"><i class="fa fa-file-pdf-o"></i></div>
                                    <a href="<?php echo base_url(); ?>ProformaInvoiceController/index" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                                </div>
                            </div>
                            <div class="col-lg-3 col-xs-6">
                                <div class="small-box bg-red">
                                    <div class="inner">
                                        <h3><?php echo $credit_note_count ?? 0; ?></h3>
                                        <p>Credit Notes</p>
                                    </div>
                                    <div class="icon"><i class="fa fa-minus-circle"></i></div>
                                    <a href="<?php echo base_url(); ?>CreditnoteController/index" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                                </div>
                            </div>
                            <div class="col-lg-3 col-xs-6">
                                <div class="small-box bg-blue">
                                    <div class="inner">
                                        <h3><?php echo $po_count ?? 0; ?></h3>
                                        <p>Purchase Vouchers</p>
                                    </div>
                                    <div class="icon"><i class="fa fa-book"></i></div>
                                    <a href="<?php echo base_url(); ?>SupplierController/view_purchase_bill" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                                </div>
                            </div>

                        <?php } else { ?>
                            <!-- Generic / Fallback Dashboard -->
                            <div class="col-lg-12">
                                <div class="alert alert-info" style="background: #1a6496 !important; border: none; border-radius: 6px;">
                                    <h4><i class="icon fa fa-info-circle"></i> Dashboard Access</h4>
                                    Please use the sidebar menu to navigate through your assigned department modules.
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </section>
            <?php } ?>
        </div>
        <!-- /.content-wrapper -->
        <div class="control-sidebar-bg"></div>
        <?php $this->load->view('admin/footer'); ?>
    </div>
    <!-- ./wrapper -->

    <script type="text/javascript" src="<?php echo base_url() ?>jqplotgraph/jquery.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url() ?>jqplotgraph/jquery.jqplot.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url() ?>jqplotgraph/plugins/jqplot.barRenderer.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url() ?>jqplotgraph/plugins/jqplot.meterGaugeRenderer.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url() ?>jqplotgraph/plugins/jqplot.pieRenderer.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url() ?>jqplotgraph/plugins/jqplot.donutRenderer.min.js"></script>
    <!--    <script type="text/javascript" src="<?php echo base_url() ?>jqplotgraph/plugins/jqplot.canvasTextRenderer.js"></script>
    <script type="text/javascript" src="<?php echo base_url() ?>jqplotgraph/plugins/jqplot.canvasAxisTickRenderer.js"></script>-->
    <script type="text/javascript" src="<?php echo base_url() ?>jqplotgraph/plugins/jqplot.categoryAxisRenderer.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url() ?>jqplotgraph/plugins/jqplot.pointLabels.min.js"></script>
    <link rel="stylesheet" type="text/css" hrf="<?php echo base_url() ?>jqplotgraph/jquery.jqplot.min.css" />












    <script>
        $(document).ready(function() {

            /* Expences saless and purchase */

            var s5 = [<?php echo $APR5; ?>, <?php echo $MAY5; ?>, <?php echo $JUN5; ?>,
                <?php echo $JUL5; ?>, <?php echo $AUG5; ?>, <?php echo $SEP5; ?>,
                <?php echo $OCT5; ?>, <?php echo $NOV5; ?>, <?php echo $DEC5; ?>, <?php echo $JAN5; ?>, <?php echo $FEB5; ?>, <?php echo $MAR5; ?>
            ];



            var s8 = [<?php echo $APR8; ?>, <?php echo $MAY8; ?>, <?php echo $JUN8; ?>,
                <?php echo $JUL8; ?>, <?php echo $AUG8; ?>, <?php echo $SEP8; ?>,
                <?php echo $OCT8; ?>, <?php echo $NOV8; ?>, <?php echo $DEC8; ?>, <?php echo $JAN8; ?>, <?php echo $FEB8; ?>, <?php echo $MAR8; ?>
            ];





            // var ticks = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            var ticks = ['Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec', 'Jan', 'Feb', 'Mar'];

            plot2 = $.jqplot('chart5', [s8, s5], {
                seriesColors: ['#6699ff', '#85802b'],

                seriesDefaults: {
                    renderer: $.jqplot.BarRenderer,
                    pointLabels: {
                        show: true
                    }

                },
                legend: {
                    show: true,
                    location: 'n',
                    placement: 'outsideGrid',
                    rendererOptions: {
                        numberRows: 1
                    },
                    marginTop: '8px',
                    xoffset: -10
                },
                series: [{
                        label: 'Quotations'
                    },
                    {
                        label: 'Invoice'
                    },



                ],
                axes: {
                    xaxis: {
                        renderer: $.jqplot.CategoryAxisRenderer,
                        ticks: ticks,
                        label: "Months",
                        tickRenderer: $.jqplot.CanvasAxisTickRenderer,

                    }


                }
            });

            $('#chart5').bind('jqplotDataHighlight',
                function(ev, seriesIndex, pointIndex, data) {
                    var sales_type = ['Quotation', 'Sales'];
                    $('#info3').html('Type: ' + sales_type[seriesIndex] + ', Month : ' + ticks[pointIndex] + ', Amount: ' + data[1]);
                    // $('#info3').html('series: ' + seriesIndex + ', point: ' + pointIndex + ', data: ' + data[1]);
                }
            );

            $('#chart5').bind('jqplotDataUnhighlight',
                function(ev) {
                    $('#info3').html('Nothing');
                }
            );


        });
    </script>




    <script>
        $(document).ready(function() {

            /* Expences saless and purchase */


            var s6 = [<?php echo $APR6; ?>, <?php echo $MAY6; ?>, <?php echo $JUN6; ?>,
                <?php echo $JUL6; ?>, <?php echo $AUG6; ?>, <?php echo $SEP6; ?>,
                <?php echo $OCT6; ?>, <?php echo $NOV6; ?>, <?php echo $DEC6; ?>, <?php echo $JAN6; ?>, <?php echo $FEB6; ?>, <?php echo $MAR6; ?>
            ];

            var s7 = [<?php echo $APR7; ?>, <?php echo $MAY7; ?>, <?php echo $JUN7; ?>,
                <?php echo $JUL7; ?>, <?php echo $AUG7; ?>, <?php echo $SEP7; ?>,
                <?php echo $OCT7; ?>, <?php echo $NOV7; ?>, <?php echo $DEC7; ?>, <?php echo $JAN7; ?>, <?php echo $FEB7; ?>, <?php echo $MAR7; ?>
            ];






            // var ticks = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            var ticks = ['Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec', 'Jan', 'Feb', 'Mar'];

            plot2 = $.jqplot('chart6', [s6, s7], {
                seriesColors: ['#00749F', '#73C774'],

                seriesDefaults: {
                    renderer: $.jqplot.BarRenderer,
                    pointLabels: {
                        show: true
                    }

                },
                legend: {
                    show: true,
                    location: 'n',
                    placement: 'outsideGrid',
                    rendererOptions: {
                        numberRows: 1
                    },
                    marginTop: '8px',
                    xoffset: -10
                },
                series: [

                    {
                        label: 'Purchase'
                    },
                    {
                        label: 'Expense'
                    }


                ],
                axes: {
                    xaxis: {
                        renderer: $.jqplot.CategoryAxisRenderer,
                        ticks: ticks,
                        label: "Months",
                        tickRenderer: $.jqplot.CanvasAxisTickRenderer,

                    }


                }
            });

            $('#chart6').bind('jqplotDataHighlight',
                function(ev, seriesIndex, pointIndex, data) {
                    var sales_type = ['Purchase', 'Expense'];
                    $('#info2').html('Type: ' + sales_type[seriesIndex] + ', Month : ' + ticks[pointIndex] + ', Amount: ' + data[1]);
                    // $('#info2').html('series: ' + seriesIndex + ', point: ' + pointIndex + ', data: ' + data[1]);
                }
            );

            $('#chart6').bind('jqplotDataUnhighlight',
                function(ev) {
                    $('#info2').html('Nothing');
                }
            );


        });
    </script>
