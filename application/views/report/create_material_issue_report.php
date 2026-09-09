<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (isset($session_data_head1)) {
} else {
    header($this->config->item('header'));
}
defined('BASEPATH') OR exit('No direct script access allowed');

$from_date  = isset($from_date)  ? $from_date  : '';
$to_date    = isset($to_date)    ? $to_date    : '';
$result     = isset($result)     ? $result     : array();
$is_filtered = isset($is_filtered) ? (bool) $is_filtered : false;

$show_project_cols = false;
?>

<style>
    .table-responsive {
        width: 100% !important;
        overflow-x: auto !important;
        -webkit-overflow-scrolling: touch;
    }
    #example3 th, #example3 td {
        vertical-align: middle !important;
        font-size: 12px;
        white-space: nowrap !important;
    }
    #example3 th {
        background-color: #3c8dbc;
        color: #ffffff;
        font-weight: 600;
    }
</style>

<div class="content-wrapper">
    <section class="content-header">
        <h1>
            <i class="fa fa-list-alt"></i> Material Issue Report
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Report</a></li>
            <li class="active">Material Issue Report</li>
        </ol>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-filter"></i> Filter Material Issue Report</h3>
                    </div>
                    <form class="form-horizontal form_overlay" method="post" action="<?php echo base_url(); ?>ReportController/create_material_issue_report">
                        <div class="box-body">
                            <div class="form-group">
                                <label for="from_date" class="col-sm-3 control-label">From Date<span style="color: red;">*</span></label>
                                <div class="col-sm-4">
                                    <input type="text" id="from_date" autocomplete="off" class="form-control backdate created-date" value="<?php echo $from_date; ?>" name="from_date" required="" onkeydown="return false;">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="to_date" class="col-sm-3 control-label">To Date<span style="color: red;">*</span></label>
                                <div class="col-sm-4">
                                    <input type="text" id="to_date" autocomplete="off" class="form-control payment-due-date-check" value="<?php echo $to_date; ?>" name="to_date" required="" onkeydown="return false;">
                                </div>
                            </div>
                        </div>
                        <div class="box-footer">
                            <center>
                                <button type="button" class="btn btn-default" onclick="history.back()"><i class="fa fa-times"></i> Cancel</button>
                                <button type="submit" class="btn btn-success"><i class="fa fa-search"></i> Submit</button>
                            </center>
                        </div>
                    </form>
                </div>

                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-table"></i> Material Issue Details</h3>
                        <?php if ($is_filtered) { ?>
                            <div class="box-tools pull-right">
                                <a href="<?php echo base_url(); ?>ReportController/get_material_issue_report_by_date_xlsx" class="btn btn-sm btn-success">
                                    <i class="fa fa-file-excel-o"></i> Export to Excel
                                </a>
                                <a href="<?php echo base_url(); ?>ReportController/get_material_issue_report_by_date_pdf" class="btn btn-sm btn-danger">
                                    <i class="fa fa-file-pdf-o"></i> Export to PDF
                                </a>
                            </div>
                        <?php } ?>
                    </div>

                    <div class="box-body">
                        <div class="table-responsive">
                            <table id="example3" class="table table-bordered table-striped table-hover" style="width:100%;">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="width: 4%;">#</th>
                                        <th class="text-center">Issue Slip No.</th>
                                        <th class="text-center">Issue Date</th>
                                        <?php if ($show_project_cols): ?>
                                        <th class="text-center">Project Code</th>
                                        <th>Project Name</th>
                                        <?php endif; ?>
                                        <th>SO Reference</th>
                                        <th>BOM Number(s)</th>
                                        <th>Job Order No.</th>
                                        <th>Item Code</th>
                                        <th>Item Name</th>
                                        <th class="text-right">Quantity</th>
                                        <th class="text-right">Issued Qty</th>
                                        <th class="text-right">Cost Price</th>
                                        <th class="text-right">Total Cost</th>
                                        <th class="text-center">Status</th>
                                        <th>User Name</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = 1;
                                    $total_issued_qty = 0;
                                    $total_cost = 0;
                                    foreach ($result as $row) {
                                        $qty = isset($row->joborder_qty) ? (float) $row->joborder_qty : 0;
                                        $issued_qty = isset($row->issued_qty) ? (float) $row->issued_qty : 0;
                                        $cost_price = isset($row->cost_price) ? (float) $row->cost_price : 0;
                                        $line_total = isset($row->total_cost) ? (float) $row->total_cost : ($issued_qty * $cost_price);
                                        $total_issued_qty += $issued_qty;
                                        $total_cost += $line_total;

                                        $st = strtolower(trim(isset($row->status) ? $row->status : ''));
                                        $status_badge = 'label-default';
                                        if (in_array($st, ['issued', 'approved', 'completed'])) {
                                            $status_badge = 'label-success';
                                        } else if (in_array($st, ['draft', 'pending'])) {
                                            $status_badge = 'label-warning';
                                        } else if (in_array($st, ['cancelled', 'rejected'])) {
                                            $status_badge = 'label-danger';
                                        }
                                    ?>
                                        <tr>
                                            <td class="text-center"><?php echo $i; ?></td>
                                            <td class="text-center"><code><?php echo htmlspecialchars(isset($row->issue_no) ? $row->issue_no : ''); ?></code></td>
                                            <td class="text-center"><?php echo !empty($row->issue_date) ? date('d-m-Y', strtotime($row->issue_date)) : ''; ?></td>
                                            <?php if ($show_project_cols): ?>
                                            <td class="text-center">
                                                <?php if (!empty($row->project_code)): ?>
                                                    <span class="label label-info"><?php echo htmlspecialchars($row->project_code); ?></span>
                                                <?php else: ?>
                                                    -
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo htmlspecialchars(isset($row->project_name) ? $row->project_name : '-'); ?></td>
                                            <?php endif; ?>
                                            <td><?php echo htmlspecialchars(isset($row->salesorder_number) ? $row->salesorder_number : '-'); ?></td>
                                             <td>
                                                 <?php 
                                                 $boms = array_filter(array_map('trim', explode(',', isset($row->bom_numbers) ? $row->bom_numbers : '')));
                                                 if (!empty($boms)) {
                                                     $boms = array_values(array_unique($boms));
                                                     if (count($boms) == 1) {
                                                         echo '<span class="label label-default" style="font-size:10px; display:inline-block;"><i class="fa fa-file-text-o"></i> ' . htmlspecialchars($boms[0]) . '</span>';
                                                     } else {
                                                         $json_boms = htmlspecialchars(json_encode($boms), ENT_QUOTES, 'UTF-8');
                                                         $issue_ref = htmlspecialchars(isset($row->issue_no) ? $row->issue_no : 'MIS');
                                                         echo '<span class="label label-default" style="font-size:10px; display:inline-block; margin-right:3px;"><i class="fa fa-file-text-o"></i> ' . htmlspecialchars($boms[0]) . '</span>';
                                                         echo '<button type="button" class="btn btn-xs btn-info view-boms-modal-btn" style="font-size:10px; padding:1px 6px; border-radius:10px;" data-boms=\'' . $json_boms . '\' data-issue="' . $issue_ref . '"><i class="fa fa-eye"></i> View (' . count($boms) . ')</button>';
                                                     }
                                                 } else {
                                                     echo '-';
                                                 }
                                                 ?>
                                             </td>
                                            <td><?php echo htmlspecialchars(isset($row->joborder_number) ? $row->joborder_number : '-'); ?></td>
                                            <td><code><?php echo htmlspecialchars(isset($row->item_code) ? $row->item_code : ''); ?></code></td>
                                            <td><strong><?php echo htmlspecialchars(isset($row->item_name) ? $row->item_name : ''); ?></strong></td>
                                            <td class="text-right"><?php echo number_format($qty, 2); ?></td>
                                            <td class="text-right font-weight-bold"><?php echo number_format($issued_qty, 2); ?></td>
                                            <td class="text-right"><?php echo number_format($cost_price, 2); ?></td>
                                            <td class="text-right font-weight-bold"><?php echo number_format($line_total, 2); ?></td>
                                            <td class="text-center">
                                                <span class="label <?php echo $status_badge; ?>"><?php echo htmlspecialchars(ucfirst($st ?: 'N/A')); ?></span>
                                            </td>
                                            <td><?php echo htmlspecialchars(isset($row->username) ? $row->username : ''); ?></td>
                                        </tr>
                                    <?php
                                        $i++;
                                    }
                                    ?>
                                </tbody>
                                <?php if (!empty($result)) { ?>
                                    <tfoot>
                                        <tr style="font-weight:bold;background:#e8f442;">
                                            <td colspan="<?php echo $show_project_cols ? '11' : '9'; ?>" class="text-right">Total:</td>
                                            <td class="text-right"><?php echo number_format($total_issued_qty, 2); ?></td>
                                            <td></td>
                                            <td class="text-right"><?php echo number_format($total_cost, 2); ?></td>
                                            <td colspan="2"></td>
                                        </tr>
                                    </tfoot>
                                <?php } ?>
                            </table>
                        </div>

                        <?php if ($is_filtered && empty($result)) { ?>
                            <div class="alert alert-info text-center" style="margin-top:15px;">
                                <strong>No material issue records found</strong> for the selected date range.
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Modal for Viewing Multiple BOMs -->
<div class="modal fade" id="bomsModal" tabindex="-1" role="dialog" aria-labelledby="bomsModalLabel">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content" style="border-radius: 6px; box-shadow: 0 5px 15px rgba(0,0,0,0.3);">
            <div class="modal-header" style="background-color: #3c8dbc; color: #fff; border-top-left-radius: 5px; border-top-right-radius: 5px; padding: 12px 15px;">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: #fff; opacity: 0.9;"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="bomsModalLabel" style="font-weight: bold; font-size: 15px;"><i class="fa fa-file-text-o"></i> Associated BOM Numbers</h4>
            </div>
            <div class="modal-body" style="background-color: #f9f9f9; padding: 20px;">
                <p id="bomsModalSubheading" style="color: #444; font-size: 13px; margin-bottom: 15px; border-bottom: 1px solid #eee; padding-bottom: 8px;"></p>
                <div id="bomsModalList" style="display: flex; flex-wrap: wrap; gap: 10px;">
                    <!-- Populated dynamically -->
                </div>
            </div>
            <div class="modal-footer" style="background-color: #f5f5f5; padding: 10px 15px;">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('admin/footer'); ?>

<script>
$(document).ready(function() {
    if ($.fn.DataTable.isDataTable('#example3')) {
        $('#example3').DataTable().destroy();
    }
    $('#example3').DataTable({
        "paging": true,
        "lengthChange": true,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "scrollX": true,
        "pageLength": 25,
        "language": {
            "search": "Search Material Issues:"
        }
    });

    $(document).on('click', '.view-boms-modal-btn', function() {
        var issueNo = $(this).data('issue');
        var bomsData = $(this).data('boms');
        var listContainer = $('#bomsModalList');
        
        $('#bomsModalSubheading').html('<strong>Issue Slip:</strong> <span class="text-navy" style="font-weight:bold;">' + issueNo + '</span> &nbsp;|&nbsp; <strong>Total BOMs:</strong> <span class="badge bg-blue">' + bomsData.length + '</span>');
        
        var html = '';
        $.each(bomsData, function(idx, bomNo) {
            html += '<div style="background:#fff; border:1px solid #d2d6de; padding:8px 12px; border-radius:6px; font-size:12px; display:inline-flex; align-items:center; gap:10px; box-shadow:0 1px 3px rgba(0,0,0,0.06); margin-right:8px; margin-bottom:8px;">';
            html += '<span><i class="fa fa-file-text-o text-blue"></i> <strong>' + bomNo + '</strong></span>';
            html += '<a href="<?php echo base_url('BomController/show_bom/'); ?>' + encodeURIComponent(bomNo) + '" target="_blank" class="btn btn-xs btn-primary" style="padding:2px 8px; font-size:10px; border-radius:4px;"><i class="fa fa-external-link"></i> View</a>';
            html += '</div>';
        });
        
        listContainer.html(html);
        $('#bomsModal').modal('show');
    });
});
</script>
