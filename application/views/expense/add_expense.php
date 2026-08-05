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
                    Add Expenditure
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="<?php echo base_url() . 'InventoryController/add_expense_data/' ?>">Add Expenditure</a></li>
                    <li class="active">Add Expenditure Details</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-info">
                            <div class="box-header">
                                <h3 class="box-title">Add Expenditure Details</h3>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">

                               

                                <?php if ($this->session->flashdata('INFOMSG')) { ?>
                                    <div role="alert" class="alert alert-info">
                                        <button data-dismiss="alert" class="close" type="button"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                                        <strong>Oops!!</strong> <?= $this->session->flashdata('INFOMSG') ?>
                                    </div>
                                <?php } ?>

                                <form class="form-horizontal form_overlay" method="post" action="<?php echo base_url(); ?>InventoryController/add_expense" enctype="multipart/form-data">
                                    <div class="modal-body">

                                        <div class="card-body ">
                                           
                                            
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Expenditure Category <span style="color: red;">*</span></label>
                                                <div class="col-sm-7">
                                                    <select type="text" class="form-control input-sm" name="expense_category" id="expense_category" required="">
                                                        <option value="">Select Expenditure Category</option>
                                                      <?php foreach ($expense_catgory as $key) { ?>
                                                            <option value="<?php echo $key->exp_cat;  ?>"><?php echo $key->exp_cat; ?></option> 
                                                        <?php } ?>  
                                                       
                                                    </select>
                                                </div>
                                            </div>
                                          
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Expenditure Amount<span style="color: red;">*</span></label>
                                                <div class="col-sm-7">
                                                    <input type="number" min="1" class="form-control input-sm" name="expense_amount" id="expense_amount" required="">
                                                </div>
                                            </div>
                                            
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">GST(%)</label>
                                                <div class="col-sm-7">
                                                    <select type="text" min="0" maxlength="3" class="form-control input-sm" name="gst_class" id="gst_class">
                                                        <option value="">Select GST</option>
                                                      <?php foreach ($gst_class_result as $key) { ?>
                                                            <option value="<?php echo $key->gst_class; ?>"><?php echo $key->gst_class; ?></option> 
                                                        <?php } ?>  
                                                       
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Expenditure Date<span style="color: red;">*</span></label>
                                                <div class="col-sm-7">
                                                    <input type="text" class="form-control input-sm alldate" name="date" id="date" required="" onkeydown="return false;">
                                                </div>
                                            </div>

                                            <div class="form-group row ">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Expenditure Doc</label>
                                                <div class="col-sm-7">
                                                    <input type="file" class="form-control input-sm" name="expense_upload" id="expense_upload">
                                                </div>
                                            </div>

                                            

                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Note<span style="color: red;">*</span></label>
                                                <div class="col-sm-7">
                                                    <textarea class="form-control" required="" name="expense_note" id="expense_note" rows="2"></textarea>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Employee / Vendor name<span style="color: red;">*</span></label>
                                                <div class="col-sm-7">
                                                    <input type="text" class="form-control input-sm " name="employee_name" id="employee_name" required="">
                                                </div>
                                            </div>
                                            
                                              <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Payment Status <span style="color: red;">*</span></label>
                                                <div class="col-sm-7">
                                                    <select type="text" class="form-control input-sm" name="status" id="status" required="">
                                                        <option value="">Select Payment Status</option>
                                                            <option value="1">Done</option> 
                                                            <option value="2">Pending on Date</option> 
                                                            <option value="3">Advance</option> 
                                                            <option value="4">Pending Amount</option> 
                                                    </select>
                                                </div>
                                            </div>
                                            
                                        </div>
                                    </div>
                                    <div class="card-footer small text-muted">
                                        
                                        <button type="submit" class="btn btn-success pull-right">Submit</button>
                                    </div>
                                </form>

                            </div>
                            
                            <div class="row">
                                    <div class="col-md-6">
                                        <h3 class="box-title"> Expenditure Details</h3> 
                                    </div>
                                    <div class="col-md-3">
                                        <form action="<?php echo base_url(); ?>InventoryController/get_monthyearwise_record" method="post">
                                               <div class="form-group row">
                                                <div class="col-sm-9">
                                                    <input type="text" class="form-control onlymonth input-sm pull-right" name="month_year" id="month_year" onkeydown="return false;" autocomplete="off">
                                                </div>
                                                <button class="btn btn-primary pull-right" name="submit" value="">Submit</button>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="col-md-3">
                                          <a href="<?php echo base_url(); ?>InventoryController/add_expense_data?str=All"><button class="btn btn-success btn-sm"> Show All Expenditure </button></a>
                                    </div>
                            </div>
                            
                            <!-- /.box-body -->
                            <table id="example3" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Sr.No.</th>
                                        <th>Expenditure Amount</th>
                                        <th>Expenditure Doc</th>
                                        <th>Expenditure Category</th>
                                        <th>GST</th>
                                        <th>Note</th>
                                        <th>Date</th>
                                        <th>Employee Name</th>
                                         <th>Payment Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = 1;
                                    foreach ($expense_result as $key) {
                                        ?>
                                        <tr>
                                            <td>
                                               <?php echo $i; ?>
                                            </td>
                                            <td> <?php echo $key->expense_amount; ?> </td>
                                            <td>
                                                <?php if ($key->expense_upload && $key->expense_upload != './uploads/') { ?>
                                                    <a href="<?php echo base_url() . $key->expense_upload ?>" download="Download">Download</a>
                                                <?php } ?>
                                            </td>
                                            <td> <?php echo $key->expense_category; ?> </td>
                                            <td><?php echo $key->gst_class; ?> </td>
                                            <td> <?php echo $key->expense_note; ?> </td>
                                            <td> <?php echo date('d-m-Y', strtotime($key->date)); ?> </td>
                                            <td> <?php echo $key->employee_name; ?> </td>
                                             <td> <?php 
                                                switch ($key->status) {
                                                  case "1":
                                                    echo "Done";
                                                    break;
                                                  case "2":
                                                    echo "Pending on Date";
                                                    break;
                                                  case "3":
                                                    echo "Advance";
                                                    break;
                                                  default:
                                                    echo "Pending Amount";
                                                }                                
                                             ?>
                                             </td>
                                                        <td>
                                                
                                                <div class="dropdown">
                                                    <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">Action
                                                            <span class="caret"></span></button>
                                                        <ul class="dropdown-menu">
                                                            <li><a href="<?php echo base_url() . 'InventoryController/get_expense_by_id/' . $key->expense_id; ?>"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</a></li>
                                                            <li><a href="<?php echo base_url() . 'InventoryController/delete_expense_by_id/' . $key->expense_id; ?>" 
                                                                   role="button" onClick="return confirm('Are you sure you want to delete?')"><i class="fa fa-trash" aria-hidden="true"></i> Delete</a></li>
                                                        </ul>
                                                </div>
                                            </td>
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

