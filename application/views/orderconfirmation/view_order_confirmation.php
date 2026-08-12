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
                    Order Confirmation (OC)
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/'?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li class="active">Order Confirmation</li>
                </ol>
            </section>
            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <h3 class="box-title">All Order Confirmations</h3>
                                <div class="box-tools pull-right">
                                    <a href="<?php echo base_url(); ?>OrderConfirmationController/create_order_confirmation" class="btn btn-primary btn-sm">
                                        <i class="fa fa-plus"></i> Create New OC
                                    </a>
                                </div>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">
                                <!-- Flash Messages -->
                                <?php if ($this->session->flashdata('SUCCESSMSG')) { ?>
                                    <div role="alert" class="alert alert-success">
                                        <button data-dismiss="alert" class="close" type="button"><span aria-hidden="true">x</span></button>
                                        <strong>Well done!!</strong> <?= $this->session->flashdata('SUCCESSMSG') ?>
                                    </div>
                                <?php } ?>

                                <?php if ($this->session->flashdata('INFOMSG')) { ?>
                                    <div role="alert" class="alert alert-info">
                                        <button data-dismiss="alert" class="close" type="button"><span aria-hidden="true">x</span></button>
                                        <strong>Oops!!</strong> <?= $this->session->flashdata('INFOMSG') ?>
                                    </div>
                                <?php } ?>

                                <!-- Summary Cards -->
                                <div class="row" style="margin-bottom: 15px;">
                                    <div class="col-md-3">
                                        <div class="small-box bg-aqua">
                                            <div class="inner">
                                                <h3><?php echo $oc_count; ?></h3>
                                                <p>Total OC</p>
                                            </div>
                                            <div class="icon">
                                                <i class="fa fa-file-text"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="small-box bg-yellow">
                                            <div class="inner">
                                                <h3><?php echo $oc_draft_count; ?></h3>
                                                <p>Draft</p>
                                            </div>
                                            <div class="icon">
                                                <i class="fa fa-edit"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="small-box bg-blue">
                                            <div class="inner">
                                                <h3><?php echo $oc_sent_count; ?></h3>
                                                <p>Sent/Confirmed</p>
                                            </div>
                                            <div class="icon">
                                                <i class="fa fa-paper-plane"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="small-box bg-green">
                                            <div class="inner">
                                                <h3><?php echo $oc_accepted_count; ?></h3>
                                                <p>Accepted</p>
                                            </div>
                                            <div class="icon">
                                                <i class="fa fa-check"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Filter -->
                                <div class="row" style="margin-bottom: 15px;">
                                    <div class="col-md-12">
                                        <form method="get" action="<?php echo base_url(); ?>OrderConfirmationController/index">
                                            <div class="input-group">
                                                <select name="str" class="form-control">
                                                    <option value="">-- Select Period --</option>
                                                    <option value="All">All Time</option>
                                                    <option value="<?php echo date('M-y'); ?>">This Month</option>
                                                </select>
                                                <span class="input-group-btn">
                                                    <button type="submit" class="btn btn-primary">Filter</button>
                                                </span>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                <!-- Data Table -->
                                <div class="table-responsive">
                                    <table id="example1" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>OC Number</th>
                                                <th>Date</th>
                                                <th>Supplier</th>
                                                <th>PO Reference</th>
                                                <th>Delivery Date</th>
                                                <th>Total Amount</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if(isset($orderconfirmations) && !empty($orderconfirmations)) { 
                                                 foreach($orderconfirmations as $oc) {
                                                     // Get supplier name with setting fallback
                                                     $supplier_name = !empty($oc->supplier_company_name) ? $oc->supplier_company_name : '';
                                                     if (empty($supplier_name) && isset($supplier_result)) {
                                                         foreach($supplier_result as $supplier) {
                                                             if($supplier->supplier_id == $oc->supplier_id) {
                                                                 $supplier_name = $supplier->company_name;
                                                                 break;
                                                             }
                                                         }
                                                     }
                                                     if (empty($supplier_name)) {
                                                         $supplier_name = !empty($settings['company_name']) ? $settings['company_name'] : 'Xformtech';
                                                     }
                                                     
                                                     // Status badge
                                                     $status_badge = '';
                                                     $status_class = '';
                                                     switch($oc->status) {
                                                         case 1:
                                                             $status_badge = 'Draft';
                                                             $status_class = 'label label-warning';
                                                             break;
                                                         case 2:
                                                             $status_badge = 'Sent/Confirmed';
                                                             $status_class = 'label label-info';
                                                             break;
                                                         case 3:
                                                             $status_badge = 'Accepted';
                                                             $status_class = 'label label-success';
                                                             break;
                                                         case 4:
                                                             $status_badge = 'Rejected';
                                                             $status_class = 'label label-danger';
                                                             break;
                                                         case 5:
                                                             $status_badge = 'Cancelled';
                                                             $status_class = 'label label-default';
                                                             break;
                                                     }
                                             ?>
                                                 <tr>
                                                     <td><strong><?php echo $oc->number_fk; ?></strong></td>
                                                     <td><?php echo date('d-M-Y', strtotime($oc->date)); ?></td>
                                                     <td><?php echo $supplier_name; ?></td>
                                                     <td><?php echo $oc->po_reference ? $oc->po_reference : '-'; ?></td>
                                                     <td><?php echo $oc->delivery_date ? date('d-M-Y', strtotime($oc->delivery_date)) : '-'; ?></td>
                                                     <td><strong><?php echo number_format($oc->total, 2); ?></strong></td>
                                                     <td><span class="<?php echo $status_class; ?>"><?php echo $status_badge; ?></span></td>
                                                     <td style="white-space: nowrap;">
                                                         <a href="<?php echo base_url(); ?>OrderConfirmationController/show_order_confirmation/<?php echo $oc->number_fk; ?>" class="btn btn-xs btn-primary" title="View Order Acceptance">
                                                             <i class="fa fa-eye"></i> View
                                                         </a>
                                                         <a href="<?php echo base_url(); ?>OrderConfirmationController/print_oa_letter/<?php echo $oc->number_fk; ?>" class="btn btn-xs btn-success" title="Print Order Acceptance Letter" target="_blank">
                                                             <i class="fa fa-file-pdf-o"></i> Print OA
                                                         </a>
                                                         <a href="<?php echo base_url(); ?>OrderConfirmationController/edit_order_confirmation_details/<?php echo $oc->number_fk; ?>" class="btn btn-xs btn-warning" title="Edit">
                                                             <i class="fa fa-edit"></i>
                                                         </a>
                                                         <a href="<?php echo base_url(); ?>OrderConfirmationController/delete_order_confirmation/<?php echo $oc->number_fk; ?>" class="btn btn-xs btn-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this Order Confirmation?');">
                                                             <i class="fa fa-trash"></i>
                                                         </a>
                                                     </td>
                                                 </tr>
                                             <?php } } else { ?>
                                                <tr>
                                                    <td colspan="8" class="text-center">No Order Confirmations found.</td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <!-- /.box-body -->
                        </div>
                        <!-- /.box -->
                    </div>
                </div>
                <!-- /.row -->
            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->
        <footer class="main-footer">
            <div class="pull-right hidden-xs">
                <b>Version</b> 1.0.0
            </div>
            <strong>Copyright &copy; 2024</strong> All rights reserved.
        </footer>
    </div>
    <!-- ./wrapper -->
    
    <!-- jQuery -->
    <script src="<?php echo base_url(); ?>bower_components/jquery/dist/jquery.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="<?php echo base_url(); ?>bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
    
    <script>
        $(function () {
            $('#example1').DataTable({
                'paging'      : true,
                'lengthChange': true,
                'searching'   : true,
                'ordering'    : true,
                'info'        : true,
                'autoWidth'   : false,
                'pageLength'  : 25,
                'language'    : {
                    'search': 'Search Order Confirmations:'
                }
            });
        });
    </script>
</body>
</html>

