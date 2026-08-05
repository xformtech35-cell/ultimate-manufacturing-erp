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
                    Expenditure Category Report 
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
                            <div class="box-header with-border">
                                <h3 class="box-title">Expenditure Category Report </h3>
                            </div>
                            <!-- /.box-header -->
                            <!-- form start -->
                            <form class="form-horizontal form_overlay" method="post" action="<?php echo base_url(); ?>ReportController/create_expenditure_category_report">
                                <div class="box-body">
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-3 control-label">From Date<span style="color: red;">*</span></label>

                                        <div class="col-sm-4">
                                            <input type="text" id="from_date" autocomplete="off" class="form-control backdate created-date" name="from_date" required="" value="<?php echo $from_date; ?>" onkeydown="return false;">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-3 control-label">To Date<span style="color: red;">*</span></label>

                                        <div class="col-sm-4">
                                            <input type="text" id="to_date" autocomplete="off" class="form-control  payment-due-date-check" name="to_date" required="" value="<?php echo $to_date;  ?>" onkeydown="return false;"> 
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="inputEmail3" class="col-sm-3 control-label">Expenditure Category<span style="color: red;">*</span></label>
                                        <div class="col-sm-4">
                                            <select type="text" class="form-control input-sm" name="expense_category" id="expense_category" required="">
                                                <option value="">-- Select Expenditure Category --</option>
                                                <option value="All">All Categories</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <center><button type="submit" class="btn btn-default">Cancel</button>
                                    <button type="submit" class="btn btn-success">Submit</button></center>
                                <!-- /.box-body -->

                                <!-- /.box-footer -->
                            </form>
                            <a href="<?php echo base_url();?>ReportController/get_expenditure_category_report"><button class="btn-sm btn btn-success pull-right">Export to Excel</button></a>
                            <table id="example3" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Sr.No.</th>
                                        <th>Expense Category</th>
                                        <th>Expense Amount</th>
                                       

                                    </tr>
                                </thead>

                                 <tbody>
                                    <?php
                                    $i = 1;
                                    foreach ($result as $key) {

                               
                                        ?>
                                        <tr>
                                            <td>
                                               <?php echo $i; ?>
                                            </td>
                               
                                    
                                            <td><?php echo $key->expense_category; ?> </td>
                                            <td><?php echo $key->expense_amount; ?> </td>
                                          
                             
                                        <?php
                                        $i++;
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <!-- /.box -->

                    </div>
                    <!--/.col (left ) -->

                    <!-- left column -->
                    <!--                    <div class="col-md-6">
                                             Horizontal Form 
                                            <div class="box box-info">
                                                <div class="box-header with-border">
                                                    <h3 class="box-title">GST Report</h3>
                                                </div>
                                                 /.box-header 
                                                 form start 
                                                <div class="box-body">
                                                    <div class="col-md-9 col-sm-8">
                                                        <div class="pad">
                    
                                                            <div id="panel-invoice-overview" class="panel panel-default overview">
                    
                                                                <table class="table table-bordered table-condensed no-margin">
                                                                    <tbody>
                                                                        <tr>
                                                                            <td>
                                                                                SGST 
                                                                            </td>
                                                                            <td class="amount">
                                                                                <span class="draft">₹<?php echo number_format($sgst[0]->sgst, 2); ?></span>
                                                                            </td>
                                                                        </tr>
                    
                                                                        <tr>
                                                                            <td>
                                                                                CGST                               
                                                                            </td>
                                                                            <td class="amount">
                                                                                <span class="draft">₹<?php echo number_format($cgst[0]->cgst, 2); ?></span>
                                                                            </td>
                                                                        </tr>
                    
                                                                        <tr>
                                                                            <td>
                                                                                IGST                               
                                                                            </td>
                                                                            <td class="amount">
                                                                                <span class="draft">₹<?php echo number_format($igst[0]->igst, 2); ?></span>
                                                                            </td>
                                                                        </tr>
                    
                                                                        <tr>
                                                                            <td>
                                                                                Total CGST & SGST :  
                                                                            </td>
                                                                            <td class="amount">
                                                                                <span class="draft">₹<?php echo number_format($sgst[0]->sgst + $cgst[0]->cgst, 2); ?></span>
                                                                            </td>
                                                                        </tr>
                    
                                                                        <tr>
                                                                            <td>
                                                                                Total IGST :  
                                                                            </td>
                                                                            <td class="amount">
                                                                                <span class="draft">₹<?php echo number_format($igst[0]->igst, 2); ?></span>
                                                                            </td>
                    
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                    
                                            </div>
                                             /.box 
                    
                                        </div>-->
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

