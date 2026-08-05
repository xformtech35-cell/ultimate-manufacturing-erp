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
                    Profit Loss Report 
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/'?>"><i class="fa fa-dashboard"></i>Home</a></li>
                    <li><a href="#">Report</a></li>
                    <li class="active">Report</li>
                </ol>
            </section>
           
            <!-- Main content -->
            <section class="content">
               

                                <?php if ($this->session->flashdata('INFOMSG')) { ?>
                                    <div role="alert" class="alert alert-info">
                                        <button data-dismiss="alert" class="close" type="button"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                                        <strong>Oops!!</strong> <?= $this->session->flashdata('INFOMSG') ?>
                                    </div>
                                <?php } ?>
                <div class="row">
                    <!-- left column -->
                    <div class="col-md-6">
                        <!-- Horizontal Form -->
                        <div class="box box-info">
                            <div class="box-header with-border">
                                <h3 class="box-title">Sell</h3>
                            </div>
                            <!-- /.box-header -->
                            
                            <!-- form start -->
                            <form class="form-horizontal form_overlay" method="post" action="<?php echo base_url(); ?>InventoryController/profit_loss_report">
                                <div class="box-body">
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-3 control-label">From Date</label>

                                        <div class="col-sm-9">
                                            <input type="text" id="from_date" class="form-control backdate created-date" name="from_date" required="" onkeydown="return false;" autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-3 control-label">To Date</label>

                                        <div class="col-sm-9">
                                            <input type="text" id="to_date" class="form-control payment-due-date-check" name="to_date" required="" onkeydown="return false;" autocomplete="off">
                                        </div>
                                    </div>
                                    
                                </div>
                                <!-- /.box-body -->
                                <div class="box-footer">
                                    <button type="submit" class="btn btn-default">Cancel</button>
                                    <button type="submit" class="btn btn-success pull-right">Submit</button>
                                </div>
                                <!-- /.box-footer -->
                            </form>
                        </div>
                        <!-- /.box -->

                    </div>
                    <!--/.col (left ) -->

                    <!-- left column -->
                    <div class="col-md-6">
                        <!-- Horizontal Form -->
                        <div class="box box-info">
                            <div class="box-header with-border">
                                <h3 class="box-title">Sell</h3>
                            </div>
                            <!-- /.box-header -->
                            <!-- form start -->
                            <div class="box-body">
                                <div class="col-md-9 col-sm-8">
                                    <div class="pad">

                                        <div id="panel-invoice-overview" class="panel panel-default overview">

                                            <table class="table table-bordered table-condensed no-margin">
                                                <tbody>
                                                    <tr class="hide">
                                                        <td>
                                                            Cost Price:
                                                        </td>
                                                        <td class="amount">
                                                            <span class="draft">₹<?php echo number_format($cost_price[0]->cost_price, 2); ?></span>

                                                        </td>
                                                    </tr>

                                                    <tr>
                                                        <td>
                                                            Sell Price:                             
                                                        </td>
                                                        <td class="amount">
                                                            <span class="draft">₹<?php echo number_format($sell_price[0]->sell_price, 2); ?></span>
                                                        </td>
                                                    </tr>
                                                    
                                                    <tr class="hide">
                                                        <td>
                                                            Expense Amount:                               
                                                        </td>
                                                        <td class="amount">
                                                            <span class="draft">₹<?php echo number_format($expense_amount[0]->expense_amount, 2); ?></span>
                                                        </td>
                                                    </tr>
                                                    
                                                   <?php if($expense_amount[0]->expense_amount + $cost_price[0]->cost_price  > $sell_price[0]->sell_price) { ?>
                                                    <tr class="hide">
                                                        <td>
                                                            <b class="hide"> Loss: </b>
                                                            <b> Total Sell: </b>
                                                        </td>
                                                        <td class="amount">
                                                            <b><span class="draft">₹<?php echo number_format(($cost_price[0]->cost_price + $expense_amount[0]->expense_amount - $sell_price[0]->sell_price),2); ?></span></b>
                                                        </td>
                                                    </tr>
                                                   <?php  } else { ?>
                                                  <tr class="hide">
                                                        <td>
                                                            <b class="hide"> Profit: </b>
                                                           <b> Total Sell: </b>
                                                        </td>
                                                        <td class="amount">
                                                            <b><span class="draft">₹<?php echo number_format($sell_price[0]->sell_price - ($cost_price[0]->cost_price + $expense_amount[0]->expense_amount),2); ?></span></b>
                                                        </td>
                                                    </tr>
                                                 <?php  } ?>
                                                    
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <!-- /.box -->

                    </div>
                    <!--/.col (left ) -->

                </div>
                <!-- /.row -->
                
                
                
                
                <div class="row">
                    <!-- left column -->
                    <div class="col-md-6">
                        <!-- Horizontal Form -->
                        <div class="box box-info">
                            <div class="box-header with-border">
                                <h3 class="box-title">Expense</h3>
                            </div>
                            <!-- /.box-header -->
                            
                            <!-- form start -->
                            <form class="form-horizontal form_overlay" method="post" action="<?php echo base_url(); ?>InventoryController/profit_loss_report">
                                <div class="box-body">
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-3 control-label">From Date</label>

                                        <div class="col-sm-9">
                                            <input type="text" id="from_date1" class="form-control backdate created-date" name="from_date1" required="" onkeydown="return false;">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-3 control-label">To Date</label>

                                        <div class="col-sm-9">
                                            <input type="text" id="to_date1" class="form-control payment-due-date-check" name="to_date1" required="" onkeydown="return false;">
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                                <label for="inputEmail3" class="col-sm-3 control-label">Expense Category</label>
                                                <div class="col-sm-9">
                                                    <select class="form-control input-sm"  name="expense_category" id="expense_category">
                                                        <option value="">Select Expense Category</option>
                                                        <?php foreach ($categories as $key) { ?>
                                                            <option value="<?php echo $key->exp_cat; ?>"><?php echo $key->exp_cat; ?></option> 
                                                        <?php } ?>  
                                                    </select>
                                                </div>
                                           
                                    </div>
                                    
                                    
                                </div>
                                <!-- /.box-body -->
                                <div class="box-footer">
                                    <button type="submit" class="btn btn-default">Cancel</button>
                                    <button type="submit" class="btn btn-success pull-right">Submit</button>
                                </div>
                                <!-- /.box-footer -->
                            </form>
                        </div>
                        <!-- /.box -->

                    </div>
                    <!--/.col (left ) -->

                    <!-- left column -->
                    <div class="col-md-6">
                        <!-- Horizontal Form -->
                        <div class="box box-info">
                            <div class="box-header with-border">
                                <h3 class="box-title">Expense</h3>
                            </div>
                            <!-- /.box-header -->
                            <!-- form start -->
                            <div class="box-body">
                                <div class="col-md-9 col-sm-8">
                                    <div class="pad">

                                        <div id="panel-invoice-overview" class="panel panel-default overview">

                                            <table class="table table-bordered table-condensed no-margin">
                                                <tbody>
                                                    <tr class="hide">
                                                        <td>
                                                            Cost Price:
                                                        </td>
                                                        <td class="amount">
                                                            <span class="draft">₹<?php echo number_format($cost_price[0]->cost_price, 2); ?></span>

                                                        </td>
                                                    </tr>

                                                    <tr class="hide">
                                                        <td>
                                                            Sell Price:                             
                                                        </td>
                                                        <td class="amount">
                                                            <span class="draft">₹<?php echo number_format($sell_price[0]->sell_price, 2); ?></span>
                                                        </td>
                                                    </tr>
                                                    
                                                    <tr>
                                                        <td>
                                                            Expense Amount:                               
                                                        </td>
                                                        <td class="amount">
                                                            <span class="draft">₹<?php echo number_format($expense_amount[0]->expense_amount, 2); ?></span>
                                                        </td>
                                                    </tr>
                                                    
                                                   <?php if($expense_amount[0]->expense_amount + $cost_price[0]->cost_price  > $sell_price[0]->sell_price) { ?>
                                                    <tr class="hide">
                                                        <td>
                                                           <b> Loss: </b>
                                                           
                                                        </td>
                                                        <td class="amount">
                                                            <b><span class="draft">₹<?php echo number_format(($cost_price[0]->cost_price + $expense_amount[0]->expense_amount - $sell_price[0]->sell_price),2); ?></span></b>
                                                        </td>
                                                    </tr>
                                                   <?php  } else { ?>
                                                   <tr class="hide">
                                                        <td>
                                                           <b> Profit: </b>
                                                        </td>
                                                        <td class="amount">
                                                            <b><span class="draft">₹<?php echo number_format($sell_price[0]->sell_price - ($cost_price[0]->cost_price + $expense_amount[0]->expense_amount),2); ?></span></b>
                                                        </td>
                                                    </tr>
                                                 <?php  } ?>
                                                    
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <!-- /.box -->

                    </div>
                    <!--/.col (left ) -->

                </div>
                
                
                
            </section>
            <!-- /.content -->

        </div>

        <?php $this->load->view('admin/footer'); ?>
        <div class="control-sidebar-bg"></div>
    </div>
    <!-- ./wrapper -->
