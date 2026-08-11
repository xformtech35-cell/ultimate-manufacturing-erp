<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (isset($session_data_head1)) {
} else {
    header($this->config->item('header'));
}
defined('BASEPATH') or exit('No direct script access allowed');
$_has_project_master = isset($session_data_head1['permission']) && in_array('Projects', $session_data_head1['permission']);
?>

    <div class="content-wrapper">
        <section class="content-header">
            <h1><i class="fa fa-list-alt"></i> BOM Order MRP Planning</h1>
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url() . 'Home/index/'; ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                <li>Inventory</li>
                <li class="active">BOM Order MRP</li>
            </ol>
        </section>

        <section class="content">
            <!-- Info Box Cards -->
            <div class="row">
                <div class="col-md-4 col-sm-6 col-xs-12">
                    <div class="info-box">
                        <span class="info-box-icon bg-aqua"><i class="fa fa-shopping-cart"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Sales Orders</span>
                            <span class="info-box-number"><?php echo count($sales_orders); ?></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6 col-xs-12">
                    <div class="info-box">
                        <span class="info-box-icon bg-green"><i class="fa fa-check-circle"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Active Projects</span>
                            <span class="info-box-number">
                                <?php 
                                    $unique_projects = array();
                                    foreach($sales_orders as $so) {
                                        if(!empty($so->project_code)) $unique_projects[$so->project_code] = true;
                                    }
                                    echo count($unique_projects);
                                ?>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6 col-xs-12">
                    <div class="info-box">
                        <span class="info-box-icon bg-yellow"><i class="fa fa-cogs"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Ready for MRP Run</span>
                            <span class="info-box-number">All Orders</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sales Order List Table -->
            <div class="row">
                <div class="col-xs-12">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title"><i class="fa fa-table"></i> Select a Sales Order to Run MRP Run</h3>
                        </div>
                        <div class="box-body">
                            <?php if ($this->session->flashdata('SUCCESSMSG')): ?>
                                <div class="alert alert-success alert-dismissible fade in">
                                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                                    <?php echo $this->session->flashdata('SUCCESSMSG'); ?>
                                </div>
                            <?php endif; ?>
                            <?php if ($this->session->flashdata('ERRORMSG')): ?>
                                <div class="alert alert-danger alert-dismissible fade in">
                                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                                    <?php echo $this->session->flashdata('ERRORMSG'); ?>
                                </div>
                            <?php endif; ?>

                            <div class="table-responsive">
                                <table id="soMrpTable" class="table table-bordered table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th style="width: 5%;">#</th>
                                            <th>SO Number</th>
                                            <th>Date</th>
                                            <th>Customer Name</th>
                                            <?php if ($_has_project_master): ?>
                                            <th>Project Code</th>
                                            <?php endif; ?>
                                            <th>BOM Number(s)</th>
                                            <th class="text-right">Total Value</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-center" style="width: 15%;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($sales_orders)): ?>
                                            <?php $sr = 1; foreach ($sales_orders as $so): ?>
                                                <tr>
                                                    <td><?php echo $sr++; ?></td>
                                                    <td><code><?php echo htmlspecialchars($so->number); ?></code></td>
                                                    <td><?php echo date('d-m-Y', strtotime($so->date)); ?></td>
                                                    <td>
                                                        <strong><?php echo htmlspecialchars($so->customer_name ?: $so->fullname); ?></strong>
                                                    </td>
                                                    <?php if ($_has_project_master): ?>
                                                    <td>
                                                        <span class="label label-info"><?php echo htmlspecialchars($so->project_code ?: 'N/A'); ?></span>
                                                    </td>
                                                    <?php endif; ?>
                                                    <td>
                                                        <?php if (!empty($so->associated_boms)): ?>
                                                            <?php foreach ($so->associated_boms as $bom_no): ?>
                                                                <span class="label label-success" style="font-size: 11px; margin-right: 3px; display: inline-block; margin-bottom: 2px;">
                                                                    <i class="fa fa-file-text-o"></i> <?php echo htmlspecialchars($bom_no); ?>
                                                                </span>
                                                            <?php endforeach; ?>
                                                        <?php else: ?>
                                                            <span class="text-muted" style="font-size:11px;">No BOM Found</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="text-right font-weight-bold">
                                                        <?php echo number_format($so->total, 2); ?>
                                                    </td>
                                                    <td class="text-center">
                                                        <?php 
                                                        switch ((int)$so->status) {
                                                            case 1:
                                                                echo '<span class="label label-default">Draft</span>';
                                                                break;
                                                            case 2:
                                                                echo '<span class="label label-warning">Under Process</span>';
                                                                break;
                                                            case 3:
                                                                echo '<span class="label label-primary">Viewed</span>';
                                                                break;
                                                            case 4:
                                                            case 0:
                                                                echo '<span class="label label-success">Approved</span>';
                                                                break;
                                                            case 5:
                                                                echo '<span class="label label-danger">Hold</span>';
                                                                break;
                                                            case 6:
                                                                echo '<span class="label label-danger">Canceled</span>';
                                                                break;
                                                            default:
                                                                echo '<span class="label label-success">Approved</span>';
                                                                break;
                                                        }
                                                        ?>
                                                    </td>
                                                     <td class="text-center">
                                                         <?php 
                                                         $btn_class = 'btn-success';
                                                         $btn_title = 'Run MRP Run';
                                                         if (isset($so->mrp_status) && $so->mrp_status === 'already_run') {
                                                             $btn_class = 'btn-warning';
                                                             $btn_title = 'Run MRP Run (Already Run)';
                                                         }
                                                         ?>
                                                         <a href="<?php echo base_url(); ?>MaterialIssueController/run_sales_order_mrp/<?php echo $so->id; ?>" 
                                                            class="btn btn-xs <?php echo $btn_class; ?> btn-flat" 
                                                            title="<?php echo $btn_title; ?>">
                                                             <i class="fa fa-cogs"></i> Run MRP Run
                                                         </a>
                                                     </td>
                                                </tr>
                                            <?php endforeach; ?>
                                         <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<!-- Scripts -->

<script>
$(document).ready(function() {
    $('#soMrpTable').DataTable({
        "paging": true,
        "lengthChange": true,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "pageLength": 25,
        "language": {
            "search": "Search Sales Orders:"
        }
    });
});
</script>
</body>
</html>
