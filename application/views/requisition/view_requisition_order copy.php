<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (isset($session_data_head1)) {
} else {
    header($this->config->item('header'));
}

$session_data_head2 = $this->session->userdata('session_data_head2');
$set_cc_email = $session_data_head2['cc_email'];
defined('BASEPATH') or exit('No direct script access allowed');
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
                    <li class="active">Purchase Requisition details</li>
                </ol>
            </section>
            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-info">
                            <div class="box-header">

                                <div class="row">
                                    <div class="col-md-4">
                                        <h3 class="box-title">Purchase Requisition details</h3>
                                    </div>
                                    <div class="col-md-4">
                                        <form action="<?php echo base_url(); ?>SupplierController/view_purchase_order" method="post">
                                            <div class="form-group row">
                                                <div class="col-sm-9">
                                                    <input type="text" class="form-control onlymonth input-sm" name="month_year" id="month_year" onkeydown="return false;" autocomplete="off" required="">
                                                </div>
                                                <button class="btn btn-primary" name="submit" value="">Submit</button>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="col-md-4">
                                        <a href="<?php echo base_url(); ?>RequisitionController/view_requisition_order?str=All"><button class="btn btn-success btn-sm"> Show All Purchase Requisition</button></a>
                                        <a href="<?php echo base_url(); ?>RequisitionController/create_purchase_requisition"><button class="btn btn-success btn-sm"><i class="glyphicon glyphicon-plus"></i>Create Requisition</button></a>
                                    </div>
                                </div>
                            </div>
                            <!-- /.box-header -->

                            <div class="box-body">
                                <!-- Flash Messages here -->
                                <!-- Start of Form for Convert to RFQ -->
                                <form action="<?php echo base_url(); ?>RFQController/convert_to_rfq" method="post" id="convertToRfqForm">
                                    <!-- Convert to RFQ Button at the top - Prominent -->
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <div class="alert alert-info" style="padding: 10px 15px;">
                                                <div class="row">
                                                    <div class="col-md-8">
                                                        <h4 style="margin: 0; font-size: 16px;">
                                                            <i class="fa fa-info-circle"></i> Convert Purchase Requisition to RFQ
                                                        </h4>
                                                        <p style="margin: 5px 0 0 0; font-size: 12px;">Select items from the table below and click "Convert to RFQ"</p>
                                                    </div>
                                                    <div class="col-md-4 text-right">
                                                        <button type="submit" class="btn btn-primary btn-lg" id="convertBtn">
                                                            <i class="fa fa-exchange"></i> Convert to RFQ
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <table id="example3" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>
                                                    <input type="checkbox" id="selectAll" title="Select All">
                                                </th>
                                                <th>PR Number</th>
                                                <th>Status</th>
                                                <th>Item</th>
                                                <th>Unit</th>
                                                <th>Quantity</th>
                                                <th>Date</th>
                                                <th>Required Date</th>
                                                <th>Department</th>
                                                <th>Requested By</th>
                                                <th>Urgency Level</th>
                                                <th>Remarks</th>
                                            </tr>
                                            <!-- SEARCH INPUTS ROW -->
                                            <tr>
                                                <th></th>
                                                <th><input type="text" class="column_search form-control" placeholder="Search PR No" style="font-size: 9px; height: 25px; padding: 2px 5px;"></th>
                                                <th><input type="text" class="column_search form-control" placeholder="Search Status" style="font-size: 9px; height: 25px; padding: 2px 5px;"></th>
                                                <th><input type="text" class="column_search form-control" placeholder="Search Item" style="font-size: 9px; height: 25px; padding: 2px 5px;"></th>
                                                <th><input type="text" class="column_search form-control" placeholder="Search Unit" style="font-size: 9px; height: 25px; padding: 2px 5px;"></th>
                                                <th><input type="text" class="column_search form-control" placeholder="Search Qty" style="font-size: 9px; height: 25px; padding: 2px 5px;"></th>
                                                <th><input type="text" class="column_search form-control" placeholder="Search Date" style="font-size: 9px; height: 25px; padding: 2px 5px;"></th>
                                                <th><input type="text" class="column_search form-control" placeholder="Search Req Date" style="font-size: 9px; height: 25px; padding: 2px 5px;"></th>
                                                <th><input type="text" class="column_search form-control" placeholder="Search Dept" style="font-size: 9px; height: 25px; padding: 2px 5px;"></th>
                                                <th><input type="text" class="column_search form-control" placeholder="Search Requested By" style="font-size: 9px; height: 25px; padding: 2px 5px;"></th>
                                                <th><input type="text" class="column_search form-control" placeholder="Search Urgency" style="font-size: 9px; height: 25px; padding: 2px 5px;"></th>
                                                <th><input type="text" class="column_search form-control" placeholder="Search Remarks" style="font-size: 9px; height: 25px; padding: 2px 5px;"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($purchase_requisition as $req): ?>
                                                <tr>
                                                    <td>
                                                        <input type="checkbox" name="item_id[]" value="<?php echo $req->item_id; ?>" class="item-checkbox" />
                                                    </td>
                                                    <td><?php echo $req->pr_no; ?></td>
                                                    <td>
                                                        <?php
                                                        switch ($req->approval_status) {
                                                            case 'Pending':
                                                                echo "Pending";
                                                                break;
                                                            case 'Approved':
                                                                echo "Approved";
                                                                break;
                                                            case 'Rejected':
                                                                echo "Rejected";
                                                                break;
                                                        }
                                                        ?>
                                                    </td>
                                                    <td><?php echo $req->item_code; ?></td>
                                                    <td><?php echo $req->unit; ?></td>
                                                    <td><?php echo $req->quantity; ?></td>
                                                    <td><?php echo date('d-m-Y', strtotime($req->pr_date)); ?></td>
                                                    <td><?php echo date('d-m-Y', strtotime($req->required_date)); ?></td>
                                                    <td><?php echo $req->department_name; ?></td>
                                                    <td><?php echo $req->requested_by_name; ?></td>
                                                    <td><?php echo $req->urgency_level; ?></td>
                                                    <td><?php echo $req->remarks; ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </form>
                                <!-- End of Form -->

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

        <!-- Control Sidebar -->

        <?php $this->load->view('admin/footer'); ?>
        <div class="control-sidebar-bg"></div>
    </div>

    <!-- ./Email modal -->
    <div id="modal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header btn-danger">
                    <center>
                        <h4 class="modal-title">Send Purchase Order<button type="button" class="close" data-dismiss="modal">&times;</button></h4>
                    </center>
                </div>
                <form class="form-horizontal form_overlay" method="post" action="<?php echo base_url(); ?>SupplierController/send_po_email" enctype="multipart/form-data">
                    <div class="modal-body">

                        <div class="card-body ">

                            <!-- form start -->
                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label">To<span style="color: red;">*</span></label>
                                <div class="col-sm-7">
                                    <input type="hidden" class="form-control" name="supplier_id" id="supplier_id" value="" required="">
                                    <input type="hidden" class="form-control" name="number" id="number" value="" required="">
                                    <input type="email" class="form-control input-sm" name="to_email" id="to_email" required="">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label">Subject </label>
                                <div class="col-sm-7">
                                    <textarea class="form-control input-sm" name="subject" id="subject" rows="2"></textarea>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label">Message </label>
                                <div class="col-sm-7">
                                    <textarea class="form-control input-sm" name="message" id="message" rows="3"></textarea>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label">Send a copy to</label>
                                <div class="col-sm-7">
                                    <input type="checkbox" name="copy_email" id="copy_email"> <?php echo $set_cc_email; ?>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" id="btnSave" class="btn btn-success"><i class="fa fa-paper-plane" aria-hidden="true"></i>
                            Send</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="glyphicon glyphicon-remove"></i> Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="modal1" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header btn-danger">
                    <center>
                        <h4 class="modal-title">Enter Payment<button type="button" class="close" data-dismiss="modal">&times;</button></h4>
                    </center>
                </div>
                <form class="form-horizontal balance-check form_overlay" method="post" action="<?php echo base_url(); ?>SupplierController/edit_purchase_payment">
                    <div class="modal-body">

                        <div class="card-body ">

                            <!-- form start -->
                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label">Balance<span style="color: red;">*</span></label>
                                <div class="col-sm-7">
                                    <input type="hidden" class="form-control" name="id" id="id" value="">
                                    <input type="hidden" class="form-control" name="total" id="total" value="">
                                    <input type="hidden" class="form-control" name="number" id="number_fk" value="">
                                    <input type="hidden" class="form-control" name="supplier_id_fk" id="supplier_id_fk">
                                    <input type="text" readonly="" class="form-control input-sm" name="balance" id="balance" required="">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label">Payment Type<span style="color: red;">*</span></label>
                                <div class="col-sm-7">
                                    <select class="form-control input-sm " name="payment_type" id="payment_type" required="">
                                        <option value="">Select Payment Type</option>
                                        <option value="Advance">Advance</option>
                                        <option value="Partial">Partial</option>
                                        <option value="Final">Final</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label">Paying Amount<span style="color: red;">*</span></label>
                                <div class="col-sm-7">
                                    <input type="text" class="form-control allownumericwithdecimal" name="paid" id="paid" value="" required="">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label">Payment Method<span style="color: red;">*</span></label>
                                <div class="col-sm-7">
                                    <select class="form-control input-sm " name="payment_method" id="payment_method" required="">
                                        <option value="">Select Payment Method</option>
                                        <option value="1">Cash</option>
                                        <option value="2">Cheque</option>
                                        <option value="3">NetBanking</option>
                                        <option value="4">Credit Card</option>
                                        <option value="5">Other</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label">Note<span style="color: red;">*</span></label>
                                <div class="col-sm-7">
                                    <textarea class="form-control input-sm" required="" name="note" id="note" rows="3"></textarea>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label">Payment Date<span style="color: red;">*</span></label>
                                <div class="col-sm-7">
                                    <input type="text" class="form-control backdate" name="date" id="date" value="" required="" onkeydown="return false;" autocomplete="off">
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