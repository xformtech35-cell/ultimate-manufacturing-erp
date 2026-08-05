<?php
defined('BASEPATH') OR exit('No direct script access allowed');

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

// Show Project Code/Name columns only if 'Projects' module is enabled for user's role
if (!isset($show_project_cols)) {
    $CI =& get_instance();
    $sess = $CI->session->userdata('session_data_head');
    $user_role_id = $sess['result']['role'] ?? null;
    $role_name = strtolower($sess['result']['role_name'] ?? '');
    if ($role_name === 'admin' || $user_role_id == 1) {
        $show_project_cols = true;
    } else if ($user_role_id) {
        $perm_row = $CI->db->select('grp_perm')->from('permission')->where('role_id_fk', $user_role_id)->group_start()->where('grp_perm', 'Projects')->or_where('grp_perm', 'projects')->group_end()->get()->row_array();
        if ($perm_row) {
            $show_project_cols = true;
        } else {
            $count = $CI->db->where('role_id_fk', $user_role_id)->count_all_results('permission');
            $show_project_cols = ($count > 0) ? false : (in_array('Projects', $sess['permission'] ?? []) || in_array('projects', $sess['permission'] ?? []));
        }
    } else {
        $show_project_cols = in_array('Projects', $sess['permission'] ?? []) || in_array('projects', $sess['permission'] ?? []);
    }
}
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
            <i class="fa fa-list-alt"></i> Material Reversal Report
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Report</a></li>
            <li class="active">Material Reversal Report</li>
        </ol>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-filter"></i> Filter Material Reversal Report</h3>
                    </div>
                    <form class="form-horizontal form_overlay" method="post" action="<?php echo base_url(); ?>ReportController/material_reversal_report">
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
                        <h3 class="box-title"><i class="fa fa-table"></i> Material Reversal Details</h3>
                        <?php if ($is_filtered) { ?>
                            <div class="box-tools pull-right">
                                <a href="<?php echo base_url(); ?>ReportController/get_material_reversal_report_xlsx" class="btn btn-sm btn-success">
                                    <i class="fa fa-file-excel-o"></i> Export to Excel
                                </a>
                                <a href="<?php echo base_url(); ?>ReportController/get_material_reversal_report_pdf" class="btn btn-sm btn-danger">
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
                                        <th class="text-center">Slip / Reversal No.</th>
                                        <th class="text-center">Date</th>
                                        <?php if ($show_project_cols): ?>
                                        <th class="text-center">Project Code</th>
                                        <th>Project Name</th>
                                        <?php endif; ?>
                                        <th>SO Reference</th>
                                        <th>BOM Number(s)</th>
                                        <th>Job Order No.</th>
                                        <th>Item Code</th>
                                        <th>Item Name</th>
                                        <th class="text-right">Req Qty</th>
                                        <th class="text-right">Reversed Qty</th>
                                        <th class="text-right">Cost Price</th>
                                        <th class="text-right">Total Cost</th>
                                        <th class="text-center">Status</th>
                                        <th>User Name</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = 1;
                                    $total_reversed_qty = 0;
                                    $total_cost = 0;
                                    foreach ($result as $row) {
                                        $qty = isset($row->joborder_qty) ? (float) $row->joborder_qty : 0;
                                        $raw_issued_qty = isset($row->issued_qty) ? (float) $row->issued_qty : 0;
                                        $issued_qty = abs($raw_issued_qty);
                                        $cost_price = isset($row->cost_price) ? (float) $row->cost_price : 0;
                                        $raw_line_total = isset($row->total_cost) ? (float) $row->total_cost : ($raw_issued_qty * $cost_price);
                                        $line_total = abs($raw_line_total);

                                        $total_reversed_qty += $issued_qty;
                                        $total_cost += $line_total;

                                        $st = strtolower(trim(isset($row->status) ? $row->status : ''));
                                        $status_badge = 'label-default';
                                        if (in_array($st, ['reversed', 'issued', 'approved', 'completed'])) {
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
                                                    foreach ($boms as $b) {
                                                        echo '<span class="label label-default" style="font-size:10px; margin-right:2px; display:inline-block; margin-bottom:2px;"><i class="fa fa-file-text-o"></i> ' . htmlspecialchars($b) . '</span> ';
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
                                            <td class="text-right"><?php echo number_format($total_reversed_qty, 2); ?></td>
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
                                <strong>No material reversal records found</strong> for the selected date range.
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
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
            "search": "Search Material Reversal:"
        }
    });
});
</script>
