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
                Dispatch                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/'?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="#">Dispatch</a></li>
                    <li class="active">Dispatch Details</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-info">

                            <!-- /.box-header -->
                            <div class="box-body">

                                <form class="form-horizontal form_overlay" method="post" action="<?php echo base_url(); ?>EmailController/add_dispatch" enctype="multipart/form-data">
                                    <div class="card-body ">
                                        <div class="box-header">
                                            <h3 class="box-title">Dispatch</h3>
                                        </div>

                                        <div class="form-group row"> 
                                            <?php if ($this->session->flashdata('SUCCESSMSG')) { ?>
                                                <div role="alert" class="alert alert-success">
                                                    <button data-dismiss="alert" class="close" type="button"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                                                    <strong>Well done!!</strong> <?= $this->session->flashdata('SUCCESSMSG') ?>
                                                </div>
                                            <?php } ?>
                                        </div>

                                        

                                        <div class="form-group row ">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Dispatch Date<span style="color: red;">*</span></label>
                                                <div class="col-sm-7">
                                                    <input type="text" class="form-control alldate input-sm" name="DispatchDate" id="DispatchDate" required="" onkeydown="return false;">
                                                </div>
                                            </div>


                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-4 control-label">Device Type<span style="color: red">*</span></label>
                                            <div class="col-sm-7">
                                                <input type="text" value="" class="form-control input-sm" name="DeviceType" id="DeviceType" required="" />
                                            </div>
                                        </div>


                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-4 control-label">Keypad Type<span style="color: red">*</span></label>
                                            <div class="col-sm-7">
                                                <input type="text" value="" class="form-control input-sm" name="KeypadType" id="KeypadType" required="" />
                                            </div>
                                        </div>


                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-4 control-label">Client Name<span style="color: red">*</span></label>
                                            <div class="col-sm-7">
                                                <input type="text" value="" class="form-control input-sm" name="ClientName" id="ClientName" required="" />
                                            </div>
                                        </div>


                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-4 control-label">Location<span style="color: red">*</span></label>
                                            <div class="col-sm-7">
                                                <input type="text" value="" class="form-control input-sm" name="Location" id="Location" required="" />
                                            </div>
                                        </div>


                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-4 control-label">MAC Address<span style="color: red">*</span></label>
                                            <div class="col-sm-7">
                                                <input type="text" value="" class="form-control input-sm" name="MACAddress" id="MACAddress" required="" />
                                            </div>
                                        </div>


                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-4 control-label">Bluetooth Name<span style="color: red">*</span></label>
                                            <div class="col-sm-7">
                                                <input type="text" value="" class="form-control input-sm" name="BluetoothName" id="BluetoothName" required="" />
                                            </div>
                                        </div>


                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-4 control-label">QR Codes<span style="color: red">*</span></label>
                                            <div class="col-sm-7">
                                                <input type="text" value="" class="form-control input-sm" name="QRCodes" id="QRcodes" required="" />
                                            </div>
                                        </div>


                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-4 control-label">Status<span style="color: red">*</span></label>
                                            <div class="col-sm-7">
                                                <input type="text" value="" class="form-control input-sm" name="Status" id="Status" required="" />
                                            </div>
                                        </div>


                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-4 control-label">Remark<span style="color: red">*</span></label>
                                            <div class="col-sm-7">
                                                <input type="text" value="" class="form-control input-sm" name="Remark" id="Remark" required="" />
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-4 control-label">Reason Of Replacement<span style="color: red">*</span></label>
                                            <div class="col-sm-7">
                                                <input type="text" value="" class="form-control input-sm" name="ReasonOfReplacement" id="ReasonOfReplacement" required="" />
                                            </div>
                                        </div>


                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-4 control-label">BIN File <span style="color: red">*</span></label>
                                            <div class="col-sm-7">
                                                <input type="text" value="" class="form-control input-sm" name="BINFile" id="BINFile" required="" />
                                            </div>
                                        </div>


                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-4 control-label">HEX File <span style="color: red">*</span></label>
                                            <div class="col-sm-7">
                                                <input type="text" value="" class="form-control input-sm" name="HEXfile" id="HEXfile" required="" />
                                            </div>
                                        </div>
                                        
                                        
                                        <br>
                                        
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

        <div class="control-sidebar-bg"></div>
        <?php $this->load->view('admin/footer'); ?>
    </div>
    <!-- ./wrapper -->
    

