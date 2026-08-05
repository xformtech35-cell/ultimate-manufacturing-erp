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
                   Bank Statement Report
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
                                <h3 class="box-title">Bank Statement Report </h3>
                            </div>
                            <!-- /.box-header -->
                            <!-- form start -->
                            <form class="form-horizontal form_overlay" method="post" action="<?php echo base_url(); ?>ReportController/create_bank_statement_report">
                                <div class="box-body">
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-3 control-label">From Date<span style="color: red;">*</span></label>

                                        <div class="col-sm-4">
                                            <input type="text" id="from_date" autocomplete="off" class="form-control backdate created-date" name="from_date" required="" value="<?php echo $from_date;  ?>" onkeydown="return false;">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-3 control-label">To Date<span style="color: red;">*</span></label>

                                        <div class="col-sm-4">
                                            <input type="text" id="to_date" autocomplete="off" class="form-control  payment-due-date-check" name="to_date" required="" value="<?php echo $to_date;  ?>" onkeydown="return false;"> 
                                        </div>
                                    </div>
                                </div>
                                <center><button type="submit" class="btn btn-default">Cancel</button>
                                    <button type="submit" class="btn btn-success">Submit</button></center>
                                <!-- /.box-body -->

                                <!-- /.box-footer -->
                            </form>
                            
                            <a href="<?php echo base_url(); ?>ReportController/get_bank_statement_report"><button class="btn-sm btn btn-success pull-right">Export to Excel</button></a>
                            <table id="example3" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Sr.No.</th>
                                        <th>Date</th>
                                        <th>Bank Name</th>
                                        <th>Transaction Details</th>
                                        <th>Withdrawal Amount</th>
                                        <th>Deposit Amount</th>
                                        <th>Balance Amount</th>
                                        <th>Description</th>
                                        

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
                                            
                                          
                                            <td><?php echo $key->transaction_date; ?></td>
                                            <td><?php echo $key->bank_transaction_name; ?></td>
                                            <td><?php echo $key->transaction_detail; ?></td>
                                            <td><?php echo $key->withdrawal_amount; ?></td>
                                            <td><?php echo $key->deposite_amount; ?></td>
                                            <td><?php echo $key->balance_amount; ?></td>
                                            <td><?php echo $key->description; ?></td>     
                                        </tr>
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

