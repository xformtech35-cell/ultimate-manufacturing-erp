<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Material Issue Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h2 {
            margin: 0;
            font-size: 18px;
            color: #1c6c94;
        }
        .header p {
            margin: 5px 0 0 0;
            font-size: 11px;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th {
            background-color: #31a3dd;
            color: #ffffff;
            font-weight: bold;
            text-align: left;
            border: 1px solid #ddd;
            padding: 6px;
        }
        td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
            vertical-align: top;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .total-row {
            font-weight: bold;
            background-color: #f5f5f5;
        }
    </style>
</head>
<body>

<?php
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
    <div class="header">
        <h2>MATERIAL ISSUE REPORT</h2>
        <p>Period: <?php echo !empty($from_date) ? htmlspecialchars($from_date) : '-'; ?> to <?php echo !empty($to_date) ? htmlspecialchars($to_date) : '-'; ?></p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 3%;">Sr.</th>
                <th style="width: 8%;">Issue No.</th>
                <th style="width: 7%;">Date</th>
                <?php if ($show_project_cols): ?>
                <th style="width: 7%;">Project Code</th>
                <th style="width: 12%;">Project Name</th>
                <?php endif; ?>
                <th style="width: 8%;">SO Reference</th>
                <th style="width: 8%;">BOM Number(s)</th>
                <th style="width: 8%;">Job Order No.</th>
                <th style="width: 7%;">Item Code</th>
                <th style="width: 12%;">Item Name</th>
                <th style="width: 5%; text-align: right;">Req Qty</th>
                <th style="width: 5%; text-align: right;">Issued Qty</th>
                <th style="width: 5%; text-align: right;">Cost Price</th>
                <th style="width: 5%; text-align: right;">Total Cost</th>
                <th style="width: 5%;">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 1;
            $total_issued = 0;
            $total_cost = 0;
            foreach ($result as $row) {
                $qty = isset($row->joborder_qty) ? (float) $row->joborder_qty : 0;
                $issued_qty = isset($row->issued_qty) ? (float) $row->issued_qty : 0;
                $cost_price = isset($row->cost_price) ? (float) $row->cost_price : 0;
                $line_total = isset($row->total_cost) ? (float) $row->total_cost : ($issued_qty * $cost_price);

                $total_issued += $issued_qty;
                $total_cost += $line_total;
            ?>
                <tr>
                    <td class="text-center"><?php echo $i; ?></td>
                    <td><?php echo htmlspecialchars(isset($row->issue_no) ? $row->issue_no : ''); ?></td>
                    <td><?php echo !empty($row->issue_date) ? date('d-m-Y', strtotime($row->issue_date)) : ''; ?></td>
                    <?php if ($show_project_cols): ?>
                    <td><?php echo htmlspecialchars(isset($row->project_code) ? $row->project_code : ''); ?></td>
                    <td><?php echo htmlspecialchars(isset($row->project_name) ? $row->project_name : ''); ?></td>
                    <?php endif; ?>
                    <td><?php echo htmlspecialchars(isset($row->salesorder_number) ? $row->salesorder_number : ''); ?></td>
                    <td><?php echo htmlspecialchars(isset($row->bom_numbers) ? $row->bom_numbers : ''); ?></td>
                    <td><?php echo htmlspecialchars(isset($row->joborder_number) ? $row->joborder_number : ''); ?></td>
                    <td><?php echo htmlspecialchars(isset($row->item_code) ? $row->item_code : ''); ?></td>
                    <td><?php echo htmlspecialchars(isset($row->item_name) ? $row->item_name : ''); ?></td>
                    <td class="text-right"><?php echo number_format($qty, 2); ?></td>
                    <td class="text-right"><?php echo number_format($issued_qty, 2); ?></td>
                    <td class="text-right"><?php echo number_format($cost_price, 2); ?></td>
                    <td class="text-right"><?php echo number_format($line_total, 2); ?></td>
                    <td><?php echo htmlspecialchars(ucfirst(isset($row->status) ? $row->status : '')); ?></td>
                </tr>
            <?php
                $i++;
            }
            ?>
            <?php if (!empty($result)) { ?>
                <tr class="total-row">
                    <td colspan="<?php echo $show_project_cols ? '11' : '9'; ?>" class="text-right">Total:</td>
                    <td class="text-right"><?php echo number_format($total_issued, 2); ?></td>
                    <td></td>
                    <td class="text-right"><?php echo number_format($total_cost, 2); ?></td>
                    <td colspan="1"></td>
                </tr>
            <?php } else { ?>
                <tr>
                    <td colspan="<?php echo $show_project_cols ? '15' : '13'; ?>" class="text-center">No records found.</td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

</body>
</html>
