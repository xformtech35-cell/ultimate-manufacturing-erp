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
                   Purchase Report 
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
                                <h3 class="box-title">Purchase Report </h3>
                            </div>
                            <!-- /.box-header -->
                            <!-- form start -->
                            <form class="form-horizontal form_overlay" method="post" action="<?php echo base_url(); ?>ReportController/create_purchase_report">
                                <div class="box-body">
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-3 control-label">From Date<span style="color: red;">*</span></label>

                                        <div class="col-sm-4">
                                            <input type="text" id="from_date" autocomplete="off" class="form-control backdate created-date" value="<?php echo $from_date; ?>" name="from_date" required="" onkeydown="return false;">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-3 control-label">To Date<span style="color: red;">*</span></label>

                                        <div class="col-sm-4">
                                            <input type="text" id="to_date" autocomplete="off" class="form-control  payment-due-date-check" value="<?php echo $to_date; ?>" name="to_date" required="" onkeydown="return false;"> 
                                        </div>
                                    </div>
                                </div>
                                <!-- /.box-body -->
                                <div class="box-footer">
                                    <center> <button type="submit" class="btn btn-default">Cancel</button>
                                        <button type="submit" class="btn btn-success">Submit</button></center>
                                </div>
                                <!-- /.box-footer -->
                            </form>
                            <a href="<?php echo base_url(); ?>ReportController/get_purchase_report_by_date_xlsx"><button class="btn-sm btn btn-success pull-right">Export to Excel</button></a>
                            <table id="example3" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Sr.No.</th>
                                        <th>Purchase Number</th>
                                       
                                        <th>Date</th>
                                        <th>Delivery Date</th>
                                        <th>Total</th>
                                        <th>Company Name</th>
                                        <th>Vendor Name</th>
                                         <th>Supplier code</th>
                                        <th>Pancard</th>
                                        <th>Email</th>
                                        <th>Mobile</th>
                                        <th>Address</th>
                                        
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = 1;
                                    foreach ($result as $key) {
                                       // print_r($result);die();
                                        ?>
                                        <tr>
                                            <td>
                                               <?php echo $i; ?>
                                            </td>
                                            <td> <?php echo $key->number; ?> </td>
                                            
                                            
                                            <td> <?php echo date("d-m-Y",strtotime($key->date)); ?> </td>
                                            <td><?php echo $key->delivery_date; ?> </td>
                                            <td> <?php echo $key->total; ?> </td>
                                            <td><?php echo $key->supplier_name; ?></td>
                                            <td><?php echo $key->supplier_fullname; ?></td>
                                            <td> <?php echo $key->s_code; ?> </td>
                                            <td><?php echo $key->pancard; ?></td>
                                            <td><?php echo $key->email; ?></td>
                                            <td><?php echo $key->mobile; ?></td>
                                            <td><?php echo $key->address; ?></td>
                                                      
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

    <!-- Vendor Modal (copied from add_supplier.php) -->
    <style>
        .required label {
            font-weight: bold;
        }
        .required label:after {
            color: #e32;
            content: '*';
            display: inline;
        }
    </style>
    <div id="myModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header btn-danger">
                    <center>
                        <h4 class="modal-title">Add Vendor<button type="button" class="close" data-dismiss="modal">&times;</button></h4>
                    </center>
                </div>
                <form class="form-horizontal form_overlay" method="post" action="<?php echo base_url(); ?>SupplierController/add_supplier" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="card-body ">
                            <!-- form start -->
                            <div class="form-group row required">
                                <label for="inputEmail3" class="col-sm-4 control-label">Vendor Code</label>
                                <div class="col-sm-7">
                                    <input type="text" class="form-control input-sm" name="s_code" id="s_code" placeholder="Leave blank to auto-generate">
                                </div>
                            </div>
                            <div class="form-group row required">
                                <label for="inputEmail3" class="col-sm-4 control-label">Company Name</label>
                                <div class="col-sm-7">
                                    <input type="text" class="form-control input-sm" name="company_name" id="company_name" required="">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label">Customer Name</label>
                                <div class="col-sm-7">
                                    <input type="text" class="form-control input-sm" name="fullname" id="fullname">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label">PAN No</label>
                                <div class="col-sm-7">
                                    <input type="text" maxlength="10" class="form-control input-sm" style="text-transform: uppercase;" name="pancard" id="pancard">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label">GST No</label>
                                <div class="col-sm-7">
                                    <input type="text" maxlength="15" class="form-control input-sm" style="text-transform: uppercase;" name="gst" id="gst">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label">Email</label>
                                <div class="col-sm-7">
                                    <input type="text" class="form-control input-sm" name="email" id="email" />
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label">Mobile</label>
                                <div class="col-sm-7">
                                    <input type="tel" class="form-control input-sm" name="mobile" id="mobile" maxlength="10" onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g, '')" />
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label"> State Code</label>
                                <div class="col-sm-7">
                                    <input type="text" class="form-control input-sm" name="state_code" id="state_code">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label">Address</label>
                                <div class="col-sm-7">
                                    <textarea type="text" class="form-control input-sm" name="address" id="address"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" id="btnSave" class="btn btn-success">Submit</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="glyphicon glyphicon-remove"></i> Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- ./wrapper -->

