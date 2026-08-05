<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (isset($session_data_head1)) {
    
} else {
    header($this->config->item('header'));
}
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<style>

    .required label {
        font-weight: bold;
    }
    .required label:after {
        color: #e32;
        content: '*';
        display:inline;
    }


</style>
<body class="hold-transition skin-blue sidebar-mini">
    <div id="loader" class="center"></div> 
    <div class="wrapper">

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    Customer
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="<?php echo base_url() . 'CustomerController/index/' ?>">Edit Customer</a></li>
                    <li class="active">Edit Customer Details</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-info">
                            <div class="box-header">
                                <h3 class="box-title">Edit Company Details</h3>
                                <a href="javascript:void(0);" onclick="window.history.back()" class="btn btn-primary pull-right"><i class="fa fa-close"></i> Close</a>

                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">

                                <!-- Start Flash Message -->
                               



                                <?php if ($this->session->flashdata('INFOMSG')) { ?>
                                    <div role="alert" class="alert alert-info">
                                        <button data-dismiss="alert" class="close" type="button"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                                        <strong>Oops!!</strong> <?= $this->session->flashdata('INFOMSG') ?>
                                    </div>
                                <?php } ?>
                                <!-- End Flash Message -->

                                <form class="form-horizontal form_overlay" method="post" action="<?php echo base_url(); ?>CustomerController/edit_customer">
                                    <div class="card-body ">
                                        <!-- form start -->
                                        <div class="form-group row required">
                                            <label for="inputEmail3" class="col-sm-3 control-label">Company Name</label>
                                            <div class="col-sm-9">
                                                <input type="hidden" class="form-control" required=""   name="customer_id" id="customer_id" value="<?php
                                                if (isset($customer) && !empty($customer)) {
                                                    echo $customer['customer_id'];
                                                }
                                                ?>" >

                                                <input type="text" class="form-control input-sm"   name="company_name" id="company_name" value="<?php
                                                if (isset($customer) && !empty($customer)) {
                                                    echo $customer['company_name'];
                                                }
                                                ?>" required="">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-3 control-label">Customer Name</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control input-sm"  name="fullname" id="fullname" value="<?php
                                                if (isset($customer) && !empty($customer)) {
                                                    echo $customer['fullname'];
                                                }
                                                ?>">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-3 control-label"> PAN</label>
                                            <div class="col-sm-9">
                                                <input type="text" maxlength="10" class="form-control input-sm" style="text-transform: uppercase;" name="pancard" id="pancard"  value="<?php
                                                if (isset($customer) && !empty($customer)) {
                                                    echo $customer['pancard'];
                                                }
                                                ?>">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-3 control-label"> GST</label>
                                            <div class="col-sm-9">
                                                <input type="text" maxlength="15" class="form-control input-sm" style="text-transform: uppercase;" name="gst" id="gst" value="<?php
                                                if (isset($customer) && !empty($customer)) {
                                                    echo $customer['gst'];
                                                }
                                                ?>">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-3 control-label"> Email</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control input-sm"  name="email" id="email" pattern="^([0-9a-zA-Z]([-.\w]*[0-9a-zA-Z])*@([0-9a-zA-Z][-\w]*[0-9a-zA-Z]\.)+[a-zA-Z]{2,9})$" value="<?php
                                                if (isset($customer) && !empty($customer)) {
                                                    echo $customer['email'];
                                                }
                                                ?>"  />
                                            </div>
                                        </div>

                                        <div class="form-group row ">
                                            <label for="inputEmail3" class="col-sm-3 control-label"> Mobile</label>
                                            <div class="col-sm-9">                                               
                                                <input type="text" class="form-control input-sm" name="mobile" id="mobile" maxlength="10"  maxlength="10" onkeyup="if (/\D/g.test(this.value))
                                                            this.value = this.value.replace(/\D/g, '')" value="<?php
                                                       if (isset($customer) && !empty($customer)) {
                                                           echo $customer['mobile'];
                                                       }
                                                       ?>"/>                                             
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-3 control-label"> State Code</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control input-sm"  name="state_code" id="state_code" value="<?php
                                                if (isset($customer) && !empty($customer)) {
                                                    echo $customer['state_code'];
                                                }
                                                ?>">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-3 control-label"> Address</label>          
                                             <div class="col-sm-9">
                                                 <textarea class="form-control input-sm"  name="address" id="address"><?php
                                                if (isset($customer) && !empty($customer)) {
                                                    echo $customer['address'];
                                                }
                                                ?></textarea>
                                            </div>
                                        </div>



                                    </div>
                                    <div class="card-footer small text-muted">
                                        <button type="button" id="back" class="btn btn-default">Back</button>
                                        <button type="submit" class="btn btn-success pull-right">Submit</button>
                                    </div>
                                </form>

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
        <!-- /.content-wrapper -->
        <?php $this->load->view('admin/footer'); ?>

        <!-- Add the sidebar's background. This div must be placed
             immediately after the control sidebar -->
        <div class="control-sidebar-bg"></div>
    </div>
    <!-- ./wrapper -->


