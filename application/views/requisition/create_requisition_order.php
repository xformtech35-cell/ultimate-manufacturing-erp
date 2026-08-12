<?php
$session_data_head1 = $this->session->userdata('session_data_head');

// var_dump($session_data_head1);

// die();

if (isset($session_data_head1)) {
} else {
    header($this->config->item('header'));
}
defined('BASEPATH') or exit('No direct script access allowed');
require_once(APPPATH . "views/admin/modal.php");
$_has_project_master = isset($session_data_head1['permission']) && in_array('Projects', $session_data_head1['permission']);

?>

<body class="hold-transition skin-blue sidebar-mini">
    <div id="loader" class="center"></div>
    <div class="wrapper">

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    Purchase Requisition
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="#">Purchase Requisition</a></li>
                    <li class="active">Purchase Requisition Details</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">




                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-info">
                            <div class="box-header">
                                <h3 class="box-title">Create Purchase Requisition</h3>

                                <a href="javascript:void(0);" onclick="window.history.back()" class="btn btn-primary pull-right" style="margin-left:10px;"><i class="fa fa-close"></i> Close</a>

                                <a href="<?php echo base_url(); ?>RequisitionController/view_requisition_order?str=All"
                                    class="btn btn-success pull-right"
                                    style="margin-left:10px;">
                                    Show All Purchase Requisition
                                </a>



                                <!--<button class="btn btn-success btn-sm pull-right"  data-toggle="modal" data-target="#myModal"><i class="glyphicon glyphicon-plus"></i>Add Customer</button>-->
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">

                                <!-- -->

                                <!-- <?php if ($this->session->flashdata('INFOMSG')) { ?>
                                    <div role="alert" class="alert alert-info">
                                        <button data-dismiss="alert" class="close" type="button"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                                        <strong>Oops!!</strong> <?= $this->session->flashdata('INFOMSG') ?>
                                    </div>
                                <?php } ?> -->

                                 <form class="form-horizontal form_overlay" name="add_name" id="add_name" method="post" action="<?php echo base_url(); ?>RequisitionController/add_requisition" enctype="multipart/form-data">
                                    <div class="row">

                                        <div class="col-md-12">
                                            <div class="form-group row ">
                                                <?php
                                                if (date('m') <= 3) {
                                                    $financial_year = (date('y') - 1) . '-' . date('y');
                                                } else {
                                                    $financial_year = date('y') . '-' . (date('y') + 1);
                                                }
                                                ?>


                                                <input type="hidden" class="form-control input-sm" name="pr_no" id="pr_no" required="" value="PR/<?php echo $financial_year; ?>/<?php printf("%04d", $pr_id + 1); ?>">
                                                <label for="inputEmail3" name="number" id="number" class="col-sm-12 control-label">
                                                    <h4> Purchase Requisition:<b> PR/<?php echo $financial_year; ?>/<?php printf("%04d", $pr_id + 1); ?> </b></h4>
                                                </label>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Department<span style="color: red;">*</span></label>
                                                <div class="col-sm-8">
                                                    <?php
                                                    // Get current user's department ID from session
                                                    $current_department_id = isset($session_data_head1['result']['department_id_fk']) ? $session_data_head1['result']['department_id_fk'] : '';
                                                    $current_department_name = '';

                                                    // Find the department name for current user's department
                                                    foreach ($department_result as $key) {
                                                        if ($key->department_id == $current_department_id) {
                                                            $current_department_name = $key->department_name;
                                                            break;
                                                        }
                                                    }
                                                    ?>

                                                    <!-- Display as readonly field or disabled select -->
                                                    <select class="form-control input-sm company_search_name" name="department_id_fk" id="department_id_fk" required="" disabled>
                                                        <option value="<?php echo $current_department_id; ?>" selected>
                                                            <?php echo $current_department_name; ?>
                                                        </option>
                                                    </select>
                                                    <!-- Hidden field to submit the value -->
                                                    <input type="hidden" name="department_id_fk" value="<?php echo $current_department_id; ?>">
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="requested_by" class="col-sm-4 control-label">
                                                    Requested By<span style="color: red;">*</span>
                                                </label>
                                                <div class="col-sm-8">

                                                    <select class="form-control input-sm" name="requested_by" id="requested_by" required>
                                                        <option value="">Select Requested By</option>

                                                        <?php
                                                        $logged_id = $session_data_head1['result']['user_id'];
                                                        $logged_name = $session_data_head1['result']['username'];
                                                        $logged_role = $session_data_head1['result']['role_name'];

                                                        // If Admin → show all users
                                                        if ($logged_role === 'Admin') {
                                                            foreach ($users as $user) { ?>
                                                                <option value="<?= $user->user_id; ?>">
                                                                    <?= htmlspecialchars($user->username); ?>
                                                                </option>
                                                            <?php }
                                                        } else { // All other roles → only logged in user 
                                                            ?>
                                                            <option value="<?= $logged_id; ?>" selected>
                                                                <?= htmlspecialchars($logged_name); ?>
                                                            </option>
                                                        <?php } ?>
                                                    </select>

                                                </div>
                                            </div>



                                            <div class="form-group row">
                                                <label for="urgency_level" class="col-sm-4 control-label">Urgency Level</label>
                                                <div class="col-sm-8">
                                                    <select class="form-control input-sm" name="urgency_level" id="urgency_level" required>
                                                        <option value="">Select Urgency Level</option>
                                                        <option value="Normal">Normal</option>
                                                        <option value="Urgent">Urgent</option>
                                                        <option value="Critical">Critical</option>
                                                    </select>
                                                </div>
                                            </div>



                                        </div>
                                        <div class="col-md-4">

                                            <div class="form-group row ">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Requisition Date<span style="color: red;">*</span></label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control alldate input-sm created-date" name="pr_date" id="pr_date" required="" onkeydown="return false;">
                                                </div>

                                            </div>



                                            <div class="form-group row ">
                                                <label for="inputEmail3" class="col-sm-4 control-label">Required Date<span style="color: red;">*</span></label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control date input-sm payment-due-date-check currentDateWithSevendays" name="required_date" id="required_date" required="" onkeydown="return false;" autocomplete="off">
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="approval_status" class="col-sm-4 control-label">Approval Status</label>
                                                <div class="col-sm-8">
                                                    <select class="form-control input-sm" name="approval_status" id="approval_status" required>
                                                        <option value="">Select Approval Status</option>

                                                        <?php if (isset($session_data_head1['result']['role_name']) && $session_data_head1['result']['role_name'] == 'Site Incharge') { ?>
                                                            <option value="Pending">Pending</option>

                                                        <?php } else { ?>
                                                            <option value="Pending">Pending</option>
                                                            <option value="Approved">Approved</option>
                                                            <option value="Rejected">Rejected</option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="col-md-4">


                                            <div class="form-group row">
                                                <label class="col-sm-4 control-label">Location<span style="color: red;">*</span></label>
                                                <div class="col-sm-8">
                                                    <?php
                                                    $loc_id = $session_data_head1['result']['location_id'] ?? '';

                                                    // Check if user's location exists in available locations
                                                    $user_location_exists = false;
                                                    if ($loc_id && !empty($location_result)) {
                                                        foreach ($location_result as $loc) {
                                                            if ($loc->location_id == $loc_id) {
                                                                $user_location_exists = true;
                                                                $loc_name = $loc->location_name;
                                                                break;
                                                            }
                                                        }
                                                    }
                                                    ?>

                                                    <?php if ($user_location_exists): ?>
                                                        <!-- Show user's location -->
                                                        <input type="text" class="form-control" value="<?php echo $loc_name; ?>" readonly>
                                                        <input type="hidden" name="location_id_fk" value="<?php echo $loc_id; ?>">
                                                    <?php else: ?>
                                                        <!-- Show all locations -->
                                                        <select class="form-control" name="location_id_fk">
                                                            <option value="">Select Location</option>
                                                            <?php if (!empty($location_result)): ?>
                                                                <?php foreach ($location_result as $loc): ?>
                                                                    <option value="<?php echo $loc->location_id; ?>">
                                                                        <?php echo $loc->location_name; ?>
                                                                    </option>
                                                                <?php endforeach; ?>
                                                            <?php endif; ?>
                                                        </select>
                                                    <?php endif; ?>
                                                </div>
                                            </div>


                                            <div class="form-group row">
                                                <label for="remarks" class="col-sm-4 control-label">Remarks</label>
                                                <div class="col-sm-8">
                                                    <textarea class="form-control" name="remarks" id="remarks" rows="3"></textarea>
                                                </div>
                                            </div>

                                        </div>

                                    </div>
                                    <div class="row" style="margin-top: 15px; border-top: 1px solid #eee; padding-top: 15px;">
                                         <?php if ($_has_project_master): ?>
                                         <div class="col-md-4">
                                             <div class="form-group row">
                                                 <label class="col-sm-4 control-label">Project Code</label>
                                                 <div class="col-sm-8">
                                                     <select class="form-control input-sm select2" name="project_code" id="project_code">
                                                         <option value="">Select Project Code</option>
                                                         <?php foreach ($projects as $proj) { ?>
                                                             <option value="<?php echo htmlspecialchars($proj['project_code']); ?>"><?php echo htmlspecialchars($proj['project_code']); ?></option>
                                                         <?php } ?>
                                                     </select>
                                                 </div>
                                             </div>
                                         </div>
                                         <?php endif; ?>
                                        <div class="col-md-4">
                                            <div class="form-group row">
                                                <label class="col-sm-4 control-label">Sales Order No</label>
                                                <div class="col-sm-8">
                                                    <select class="form-control input-sm select2" name="so_no" id="so_no">
    <option value="">Select Sales Order</option>

    <?php foreach ($sales_orders as $so) { ?>
        <option
            value="<?= htmlspecialchars($so['number_fk'] ?? '') ?>"
            data-oc="<?= htmlspecialchars($so['oc_number'] ?? '') ?>"
            data-project="<?= htmlspecialchars($so['project_code'] ?? '') ?>">
            <?= htmlspecialchars($so['number_fk'] ?? '') ?>
        </option>
    <?php } ?>

</select>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- <div class="col-md-4">
                                            <div class="form-group row">
                                                <label class="col-sm-4 control-label">OC Number</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control input-sm" name="oc_no" id="oc_no" readonly placeholder="Auto-populated">
                                                </div>
                                            </div>
                                        </div> -->
                                    </div>
                                    <div class="table-responsive">

                                        <table class="table table-bordered" id="dynamic_field">
                                            <th>Item</th>
                                            <th>Description</th>
                                            <th>HSN Code</th>
                                            <th>QTY</th>
                                            <th>UNIT</th>
                                            <th>Estimated Cost </th>
                                            <th>Specification</th>

                                            <th>Action
                                            </th>
                                            <tr>
                                                <td>
                                                    <select style="width: 150px" class="form-control input-sm product_name_auto item_search_name name_list" name="item_code[]" id="item_name1" onchange="myFunction1(this.id)" required="" data-live-search="true">
                                                        <option></option>
   <option value="NEW">+ Add New Product</option>

                                                        <?php foreach ($item_name as $key) { 
                                                            $sel = (isset($selected_item_code) && $selected_item_code === $key->code) ? 'selected' : '';
                                                        ?>
                                                            <option value="<?php echo $key->code; ?>" <?php echo $sel; ?>><?php echo $key->code . " - " . $key->item_name; ?></option>
                                                        <?php } ?>
                                                    </select>

                                                </td>
                                                <td>

                                                    <button type="button" class="btn btn-info" onClick="descButton(this.id)" id="btnDescriptionId1">description</button>

                                                    <textarea style="width: 150px; " class="form-control input-sm name_list description_auto hide" name="description[]" id="description1" rows="4"></textarea>

                                                </td>
                                                <td><input type="text" name="hsn[]" id="hsn1" required="" class="form-control input-sm required_list name_list" /></td>

                                                <td><input type="text" name="quantity[]" id="quantity1" required="" class="form-control input-sm required_list name_list quantity_auto number-only-validation" value="1" autocomplete="off" /></td>
                                                <td>
                                                    <select style="width: 100px" class="form-control input-sm  item_search_unit" name="unit[]" id="unit1" required="" data-live-search="true">
                                                        <option></option>
                                                    </select>
                                                </td>
                                                <td><input type="text" name="estimated_cost[]" id="estimated_cost1" class="form-control input-sm" value="" autocomplete="off" /></td>
                                                <td><input type="text" name="specification[]" id="specification1" class="form-control input-sm" value="" autocomplete="off" /></td>

                                                <td><button type="button" name="add_requisition" id="add_requisition" class="btn btn-success  action-header-btn" title="Add New Row"><i class="fa fa-plus-circle" aria-hidden="true"></i></button>
                                                    <button type="button" name="remove" id="remove4" class="btn btn-danger btn_remove">X</button>
                                                </td>
                                            </tr>
                                        </table>



                                        <div align="center">
                                            <button type="submit" name="submit" id="submit" class="btn btn-success">Save</button>
                                        </div>
                                </form>

                            </div>
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
    
    <script>
    $(document).ready(function() {
        $('#so_no').change(function() {
            var selected = $(this).find('option:selected');
            var oc = selected.data('oc');
            var project = selected.data('project');
            
            $('#oc_no').val(oc ? oc : '');
            if (project) {
                $('#project_code').val(project).trigger('change');
            }
        });

        <?php if (!empty($selected_item_code)) { ?>
            // Automatically initialize the first row details for pre-selected item
            setTimeout(function() {
                myFunction1('item_name1');
            }, 300);
        <?php } ?>
    });
    </script>