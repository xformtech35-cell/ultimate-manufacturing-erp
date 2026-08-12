<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (!isset($session_data_head1)) {
    header($this->config->item('header'));
}
defined('BASEPATH') or exit('No direct script access allowed');
require_once(APPPATH . "views/admin/modal.php");
$_has_project_master = isset($session_data_head1['permission']) && in_array('Projects', $session_data_head1['permission']);
?>

<body class="hold-transition skin-blue sidebar-mini">
    <div id="loader" class="center"></div>
    <div class="wrapper">

        <div class="content-wrapper">
            <section class="content-header">
                <h1> Edit Purchase Requisition </h1>
                <ol class="breadcrumb">
                    <li><a href="<?= base_url('Home/index') ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="#">Purchase Requisition</a></li>
                    <li class="active">Edit Purchase Requisition</li>
                </ol>
            </section>

            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-info">
                            <div class="box-header">
                                <h3 class="box-title">Edit Purchase Requisition</h3>

                                <a href="javascript:void(0);" onclick="window.history.back()"
                                    class="btn btn-primary pull-right"
                                    style="margin-left:10px;">
                                    <i class="fa fa-close"></i> Close
                                </a>

                                <a href="<?php echo base_url(); ?>RequisitionController/view_requisition_order?str=All"
                                    class="btn btn-success pull-right"
                                    style="margin-left:10px;">
                                    Show All Purchase Requisition
                                </a>

                                <a href="<?php echo base_url(); ?>RequisitionController/create_purchase_requisition"
                                    class="btn btn-success pull-right"
                                    style="margin-left:10px;">
                                    <i class="glyphicon glyphicon-plus"></i> Create Requisition
                                </a>
                            </div>


                            <div class="box-body">
                                <form class="form-horizontal form_overlay" method="post" action="<?= base_url('RequisitionController/update_requisition') ?>" enctype="multipart/form-data">
                                    <input type="hidden" name="pr_id" value="<?= $requisition->pr_id ?>">

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group row">
                                                <label class="col-sm-2 control-label">PR Number</label>
                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control" name="pr_no" value="<?= $requisition->pr_no ?>" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <!-- Department -->
                                        <div class="col-md-4">
                                            <div class="form-group row">
                                                <label class="col-sm-4 control-label">Department<span style="color:red;">*</span></label>
                                                <div class="col-sm-8">
                                                    <select class="form-control input-sm" name="department_id_fk" required>
                                                        <option value="">Select Department</option>
                                                        <?php foreach ($department_result as $dep): ?>
                                                            <option value="<?= $dep->department_id ?>" <?= $requisition->department_id_fk == $dep->department_id ? 'selected' : '' ?>><?= $dep->department_name ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="requested_by" class="col-sm-4 control-label">
                                                    Requested By<span style="color:red;">*</span>
                                                </label>
                                                <div class="col-sm-8">
                                                    <select class="form-control input-sm" name="requested_by" id="requested_by" required>
                                                        <option value="">Select Requested By</option>
                                                        <?php foreach ($users as $user): ?>
                                                            <option value="<?php echo htmlspecialchars($user->user_id); ?>"
                                                                <?php echo ($requisition->requested_by == $user->user_id) ? 'selected' : ''; ?>>
                                                                <?php echo htmlspecialchars($user->username); ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label class="col-sm-4 control-label">Urgency Level</label>
                                                <div class="col-sm-8">
                                                    <select class="form-control input-sm" name="urgency_level" required>
                                                        <option value="">Select Urgency Level</option>
                                                        <option value="Normal" <?= $requisition->urgency_level == 'Normal' ? 'selected' : '' ?>>Normal</option>
                                                        <option value="Urgent" <?= $requisition->urgency_level == 'Urgent' ? 'selected' : '' ?>>Urgent</option>
                                                        <option value="Critical" <?= $requisition->urgency_level == 'Critical' ? 'selected' : '' ?>>Critical</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Dates and approval -->
                                        <div class="col-md-4">
                                            <div class="form-group row">
                                                <label class="col-sm-4 control-label">Requisition Date<span style="color:red;">*</span></label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control alldate input-sm" name="pr_date" value="<?= date('d-m-Y', strtotime($requisition->pr_date)) ?>" required readonly>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label class="col-sm-4 control-label">Required Date<span style="color:red;">*</span></label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control date input-sm" name="required_date" value="<?= date('d-m-Y', strtotime($requisition->required_date)) ?>" required>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label class="col-sm-4 control-label">Approval Status</label>
                                                <div class="col-sm-8">
                                                    <select class="form-control input-sm" name="approval_status">
                                                        <option value="">Select Status</option>
                                                        <option value="Pending" <?= $requisition->approval_status == 'Pending' ? 'selected' : '' ?>>Pending</option>
                                                        <option value="Approved" <?= $requisition->approval_status == 'Approved' ? 'selected' : '' ?>>Approved</option>
                                                        <option value="Rejected" <?= $requisition->approval_status == 'Rejected' ? 'selected' : '' ?>>Rejected</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Remarks -->
                                        <div class="col-md-4">

                                            <!-- ADD LOCATION FIELD HERE -->
                                            <div class="form-group row">
                                                <label class="col-sm-4 control-label">Location<span style="color:red;">*</span></label>
                                                <div class="col-sm-8">
                                                    <select class="form-control input-sm location_search_name" name="location_id_fk">
                                                        <option value="">Select Location</option>
                                                        <?php if (!empty($location_result)): ?>
                                                            <?php foreach ($location_result as $loc): ?>
                                                                <option value="<?= $loc->location_id ?>" <?= (isset($requisition->location_id_fk) && $requisition->location_id_fk == $loc->location_id) ? 'selected' : '' ?>>
                                                                    <?= htmlspecialchars($loc->location_name) ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        <?php else: ?>
                                                            <option value="">No locations available</option>
                                                        <?php endif; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-sm-4 control-label">Remarks</label>
                                                <div class="col-sm-8">
                                                    <textarea class="form-control" name="remarks" rows="3"><?= $requisition->remarks ?></textarea>
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
                                                             <option value="<?php echo htmlspecialchars($proj['project_code']); ?>" <?= (isset($requisition->project_code) && $requisition->project_code == $proj['project_code']) ? 'selected' : '' ?>><?php echo htmlspecialchars($proj['project_code']); ?></option>
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
                                                            <option value="<?php echo htmlspecialchars($so['number_fk']); ?>" data-oc="<?php echo htmlspecialchars($so['oc_number']); ?>" data-project="<?php echo htmlspecialchars($so['project_code']); ?>" <?= (isset($requisition->so_no) && $requisition->so_no == $so['number_fk']) ? 'selected' : '' ?>><?php echo htmlspecialchars($so['number_fk']); ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group row">
                                                <label class="col-sm-4 control-label">OC Number</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control input-sm" name="oc_no" id="oc_no" readonly placeholder="Auto-populated" value="<?php echo isset($requisition->oc_no) ? htmlspecialchars($requisition->oc_no) : ''; ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                            </div>

                            <!-- Items Table -->
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dynamic_field">
                                    <thead>
                                        <tr>
                                            <th>Item</th>
                                            <th>Description</th>
                                            <th>HSN Code</th>
                                            <th>QTY</th>
                                            <th>UNIT</th>
                                            <th>Estimated Cost</th>
                                            <th>Specification</th>
                                            <th>Action <button type="button" name="add_requisition" id="add_requisition" class="btn btn-success hide"><i class="fa fa-plus-circle"></i></button></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $i = 1;
                                        foreach ($requisition_items as $item): ?>
                                            <tr id="row<?= $i ?>">
                                                <td>
                                                    <select class="form-control input-sm product_name_auto" name="item_code[]" id="item_name<?= $i ?>" required onchange="myFunction1(this.id)">
                                                        <option value="">Select Item</option>
                                                        <?php foreach ($item_name as $itm): ?>
                                                            <option value="<?= $itm->code ?>" <?= $itm->code == $item->item_code ? 'selected' : '' ?>><?= $itm->code ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-info" onClick="descButton(this.id)" id="btnDescriptionId<?= $i ?>">description</button>
                                                    <textarea class="form-control description_auto hide" name="description[]" id="description<?= $i ?>" rows="4"><?= $item->description ?></textarea>
                                                </td>
                                                <td><input type="text" class="form-control" name="hsn[]" value="<?= $item->hsn ?>" required></td>
                                                <td><input type="text" class="form-control quantity_auto" name="quantity[]" value="<?= $item->quantity ?>" required></td>
                                                <td>



                                                    <select style="width: 100px" class="form-control input-sm  item_search_unit" name="unit[]" id="unit1" required="" data-live-search="true">
                                                        <option value="<?php echo $item->unit ?>"><?php echo $item->unit; ?></option>
                                                    </select>


                                                </td>
                                                <td><input type="text" class="form-control" name="estimated_cost[]" value="<?= $item->estimated_cost ?>"></td>
                                                <td><input type="text" class="form-control" name="specification[]" value="<?= $item->specification ?>"></td>
                                                <td><button type="button" name="remove" id="remove<?= $i ?>" class="btn btn-danger btn_remove">X</button></td>
                                            </tr>
                                        <?php $i++;
                                        endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <div align="center">
                                <button type="submit" class="btn btn-success">Update</button>
                            </div>
                            </form>

                        </div>
                    </div>
                </div>
        </div>
        </section>
    </div>

    <?php $this->load->view('admin/footer'); ?>
    <div class="control-sidebar-bg"></div>
    </div>

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
    });
    </script>