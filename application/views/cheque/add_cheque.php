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
                   Add Cheque Details
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/'?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="#">Add Cheque Details</a></li>
                    <li class="active">Add Cheque Details</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-info">
                            <div class="box-header">
                                <h3 class="box-title">Add Cheque Details</h3>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">

                               

                                <?php if ($this->session->flashdata('INFOMSG')) { ?>
                                    <div role="alert" class="alert alert-info">
                                        <button data-dismiss="alert" class="close" type="button"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                                        <strong>Oops!!</strong> <?= $this->session->flashdata('INFOMSG') ?>
                                    </div>
                                <?php } ?>

                                <form class="form-horizontal form_overlay"  method="post" action="<?php echo base_url(); ?>ChequeController/add_cheque_detail" enctype="multipart/form-data">
                                    <div class="modal-body">

                                        <div class="card-body ">
                                            
                                                   <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Cheque Number</label>
                                                <div class="col-sm-4">
                                                    <input type="text"  class="form-control input-sm  filterme" name="cheque_no" id="cheque_no" >
                                                </div>
                                            </div>
                                            
                                            
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Creation Date</label>
                                                <div class="col-sm-4">
                                                   <input type="text" class="form-control input-sm alldate" name="creation_date" id="creation_date" onkeydown="return false;">
                                                </div>
                                            </div>
                                            
                                            
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Bank Account Name</label>
                                                <div class="col-sm-4">
                                                    <input type="text"  class="form-control input-sm  filterme" name="bank_account_name" id="bank_account_name" >
                                                </div>
                                            </div>
                                            
                                            <div class="form-group row">
                                                 <label for="inputEmail3" class="col-sm-4 control-label">No. of Cheque</label>
                                                    <div class="col-sm-4">
                                                        <input type="text"  class="form-control input-sm  filterme" name="no_of_cheque" id="no_of_cheque" >
                                                    </div>
                                            </div>
                                            
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Status</label>
                                                <div class="col-sm-4">
<!--                                                    <input type="text"  class="form-control input-sm  filterme" name="status" id="status">-->
                                                    <select type="text" class="form-control input-sm" name="status" id="status">
                                                        <option value="">Select Status</option>
                                                            <option value="1">Validate</option> 
                                                            <option value="2">Not Validate</option> 
                                                            
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer small text-muted">
<!--                                        <button type="button" id="back" class="btn btn-default">Back</button>-->
                                        <button type="submit" class="btn btn-success pull-right downloadButton">Submit</button>
                                    </div>
                                </form>
                                
                            </div>
                            <!-- /.box-body -->
                            <table id="" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Sr.No.</th>
                                            <th>Cheque Number</th>
                                            <th>Creation Date</th>
                                            <th>Bank Account Name</th>
                                            <th>No. of Cheque</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $i=1; foreach ($chequedetail_result as $key) { ?>
                                            <tr>
                                                <td>
                                                <?php echo $i; ?>
                                                </td>
                                                <td><?php echo $key->cheque_no; ?> </td>
                                                <td> <?php echo $key->creation_date; ?> </td>
                                                <td> <?php echo $key->bank_account_name; ?> </td>
                                                <td> <?php echo $key->no_of_cheque; ?> </td>
                                                <td> <?php
                                                        switch ($key->status) {
                                                            case "1":
                                                                echo "Validate";
                                                                break;
                                                            case "2":
                                                                echo "Not Validate";
                                                                break;
                                                            default:
                                                                echo "";
                                                        }
                                                        ?>
                                                    </td>
                                               
                                                <td>

                                                    <div class="dropdown">
                                                            <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">Action
                                                                <span class="caret"></span></button>
                                                            <ul class="dropdown-menu">
                                                                <li><a href="<?php echo base_url() . 'ChequeController/get_cheque_detail_id/' . $key->cheque_id ; ?>"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</a></li>
                                                                <li><a href="<?php echo base_url() . 'ChequeController/delete_cheque_detail_by_id/' . $key->cheque_id ; ?>" 
                                                                       role="button" onClick="return confirm('Are you sure you want to delete?')"><i class="fa fa-trash" aria-hidden="true"></i> Delete</a></li>
                                                            </ul>
                                                        </div>
                                                    </td>
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

  