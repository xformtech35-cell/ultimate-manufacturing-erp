<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (isset($session_data_head1)) {
    
} else {
    header($this->config->item('header'));
}
defined('BASEPATH') OR exit('No direct script access allowed');
$_POST = array();
?>
<body class="hold-transition skin-blue sidebar-mini" onload="checkFirstVisit()">
    <div id="loader" class="center"></div> 
    <div class="wrapper">
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    Stock
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="#">Stock</a></li>
                    <li class="active">Stock Details</li>
                </ol>
            </section>
            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-info">
                            <div class="box-header">
                                <h3 class="box-title">Stock Details</h3>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">
                                <div class="row ">
                                    <form class="form-horizontal balance-check form_overlay" method="post" action="<?php echo base_url(); ?>SupplierController/view_purchase_stock">
                                        <div class="col-sm-2">
                                        </div>
                                        <div class="form-group col-sm-3">
                                            From Date<input type="text" class="form-control holedate input-sm" name="from_date" id="from_date" required="">
                                        </div>
                                        <div class="col-sm-1">

                                        </div>
                                        <div class="form-group col-sm-3">
                                            To Date<input type="text" class="form-control holedate input-sm" name="to_date"  id="to_date" required="">
                                        </div>
                                        <div class="form-group col-sm-1" style="margin-top:17px;">
                                            <button type="submit" id="filter" class="btn btn-success btn-sm pull-right">Submit</button>
                                        </div>

                                    </form>
                                    <form class="form-horizontal balance-check form_overlay" method="post" action="<?php echo base_url(); ?>SupplierController/view_purchase_stock">
                                        <div class="form-group col-sm-1" style="margin-top: 17px;">
                                            <input type="hidden" name="reload">
                                            <button type="submit" id="refresh" class="form-group btn btn-primary btn-sm pull-right"><i class="fa fa-recycle">Get All</i></button>
                                        </div>
                                    </form>

                                </div>

                                <table id="example2" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Sr.No</th>
                                            <th>Item</th>
                                            <th class="hide">Old Stock</th>
                                            <th>In Stock</th>
                                            <th>Unit</th>
                                            <th>Name</th>
                                            <th>Company Name</th>
                                            <th>Amount</th>
                                            <th>Rate On Item</th>
                                            <th>Date</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $i = 1;
                                        foreach ($stock as $key) {
                                            ?>
                                            <tr>
                                                <td>
                                                    <?php echo $i; ?>
                                                </td>
                                                <td> <?php echo $key->raw_item_master_name; ?> </td>
                                                <td  class="hide"> <?php echo $key->oldstock; ?> </td>
                                                <td> <?php echo $key->instock; ?> </td>
                                                <td> <?php echo $key->purchase_unit; ?> </td>
                                                <td> <?php echo $key->fullname; ?> </td>
                                                <td> <?php echo $key->company_name; ?> </td>
                                                <td> <?php echo (int) $key->paid_amount; ?> </td>
                                                <td> <?php echo $key->rate_on_item; ?> </td>
                                                <td> <?php echo date('d-m-Y', strtotime($key->purchase_date)); ?> </td>
                                                <td> <a href="<?php echo base_url() . 'SupplierController/delete_purchase_stock_by_id/' . $key->purchase_stock_id; ?>" class="btn btn-danger" role="button" onClick="return confirm('Are you sure you want to delete?')"><i class="fa fa-trash" aria-hidden="true"></i></a> </td>
                                            </tr>
                                            <?php
                                            $i++;
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                            <!-- /.box-body -->
                        </div>
                        <!-- /.box -->
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->
            </section>
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-info">
                            <div class="box-header">
                                <h3 class="box-title">Purchase Ledger Toady's Details</h3>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">
                                <table id="example3" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Sr.No</th>
                                            <th>Name</th>
                                            <th>Company Name</th>
                                            <th>Amount</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $i = 1;
                                        foreach ($purchase_ledger as $key) {
                                            ?>
                                            <tr>
                                                <?php if ($key->paid_amount) { ?>
                                                    <td>
                                                        <?php echo $i; ?>
                                                    </td>
                                                    <td> <?php echo $key->fullname; ?> </td>
                                                    <td> <?php echo $key->company_name; ?> </td>
                                                    <td> <?php echo $key->total_purchase_amount; ?> </td>
                                                    <td> <?php echo $key->purchase_date; ?> </td>
                                                <?php } ?>
                                            </tr>
                                            <?php
                                            $i++;
                                        }
                                        ?>
                                    </tbody>
                                </table>
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
    <script>
    function checkFirstVisit() {
        if(document.cookie.indexOf('mycookie')==-1) {
          // cookie doesn't exist, create it now
          document.cookie = 'mycookie=1';
        }
        else {
          // not first visit, so alert
          //alert('You refreshed!');
          
          //$('#refresh').click();
          //window.stop();

        }
      }
    </script>