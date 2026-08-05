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
                    Dispatch
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/'?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="#">Add Dispatch</a></li>
                    <li class="active">Add Dispatch Details</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-info">
                            <div class="box-header">
                                <h3 class="box-title">Dispatch Details</h3>           <a href="<?php echo base_url(); ?>EmailController/add"><button class="btn btn-success btn-sm pull-right" ><i class="glyphicon glyphicon-plus"></i>Add Dispatch</button></a>  
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">

                               

                                <?php if ($this->session->flashdata('INFOMSG')) { ?>
                                    <div role="alert" class="alert alert-info">
                                        <button data-dismiss="alert" class="close" type="button"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                                        <strong>Oops!!</strong> <?= $this->session->flashdata('INFOMSG') ?>
                                    </div>
                                <?php } ?>

                                
                            </div>
                            <!-- /.box-body -->
                            <table id="example7" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Sr.No.</th>
                                            <th>Dispatch Date</th>
                                            <th>Device Type</th>
                                            <th>Keypad Type</th>
                                            <th>Client Name</th>
                                            <th>Location</th>
                                            <th>MAC Address</th>
                                            <th>Bluetooth Name</th>
                                            <th>QR Codes</th>
                                            <th>Status</th>
                                            <th>Remark</th>
                                            <th>Reason Of Replacement</th>
                                            <th>BIN File</th>
                                            <th>HEX File</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php // var_dump($result);
                                         $i=1; foreach ($result as $key) { ?>
                                         <tr>
                                                <td>
                                                    <?php echo $i; ?>
                                                </td>
                                                <td> <?php echo $key->DispatchDate; ?> </td>
                                                <td> <?php echo $key->DeviceType; ?> </td>
                                                <td> <?php echo $key->KeypadType; ?> </td>
                                                <td> <?php echo $key->ClientName; ?> </td>
                                                <td> <?php echo $key->Location; ?> </td>
                                                <td> <?php echo $key->MACAddress; ?> </td>
                                                <td> <?php echo $key->BluetoothName; ?> </td>
                                                <td> <?php echo $key->QRCodes; ?> </td>
                                                <td> <?php echo $key->Status; ?> </td>
                                                <td> <?php echo $key->Remark; ?> </td>
                                                <td> <?php echo $key->ReasonOfReplacement; ?> </td>
                                                <td> <?php echo $key->BINFile; ?> </td>
                                                <td> <?php echo $key->HEXFile; ?> </td>

                                            </tr>
                                        <?php $i++; } ?>
                                    </tbody>
                                </table>

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
        <div class="control-sidebar-bg"></div>
    </div>
    <!-- ./wrapper -->

  