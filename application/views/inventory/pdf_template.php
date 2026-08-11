<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10pt;
            line-height: 1.4;
        }

        .report-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .report-title {
            color: #3498db;
            font-size: 24px;
            margin-bottom: 5px;
        }

        .report-subtitle {
            color: #666;
            font-size: 12px;
            margin-bottom: 15px;
        }

        .summary-box {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .summary-item {
            display: inline-block;
            margin-right: 30px;
        }

        .summary-label {
            font-size: 11px;
            color: #666;
            display: block;
        }

        .summary-value {
            font-size: 16px;
            font-weight: bold;
            color: #333;
        }

        .filters-box {
            background: #e8f4fd;
            border: 1px solid #b8daff;
            border-radius: 5px;
            padding: 10px 15px;
            margin-bottom: 20px;
            font-size: 11px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 9pt;
        }

        th {
            background-color: #3498db;
            color: white;
            font-weight: bold;
            padding: 8px;
            text-align: left;
            border: 1px solid #2980b9;
        }

        td {
            padding: 6px 8px;
            border: 1px solid #ddd;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .low-stock {
            color: #e74c3c;
            font-weight: bold;
        }

        .price-cost {
            color: #e74c3c;
        }

        .price-sell {
            color: #27ae60;
        }

        .badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8pt;
            font-weight: bold;
        }

        .badge-boughtout {
            background-color: #e3f2fd;
            color: #1565c0;
        }

        .badge-manufacturing {
            background-color: #e8f5e9;
            color: #2e7d32;
        }

        .footer {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            font-size: 8pt;
            color: #666;
            text-align: center;
        }

        .page-break {
            page-break-after: always;
        }

        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="report-header">
        <div class="report-title">Inventory Report</div>
        <div class="report-subtitle">
            Generated on: <?php echo date('d-m-Y'); ?><br>
            Report ID: INV-<?php echo date('Ymd-His'); ?>
        </div>
    </div>

    <!-- Summary Section -->
    <div class="summary-box">
        <div class="summary-item">
            <span class="summary-label">Total Items</span>
            <span class="summary-value"><?php echo number_format($total_items); ?></span>
        </div>
        <div class="summary-item">
            <span class="summary-label">Total Stock Value</span>
            <span class="summary-value">₹<?php echo number_format($total_stock_value, 2); ?></span>
        </div>
        <div class="summary-item">
            <span class="summary-label">Low Stock Items</span>
            <span class="summary-value low-stock"><?php echo $low_stock_count; ?></span>
        </div>
        <div class="summary-item">
            <span class="summary-label">Report Pages</span>
            <span class="summary-value">1</span>
        </div>
    </div>

    <!-- Filters Section -->
    <?php if ($filters['search_item'] || $filters['item_type'] || $filters['stock_status']): ?>
        <div class="filters-box">
            <strong>Applied Filters:</strong>
            <?php
            $filter_texts = [];
            if ($filters['search_item']) $filter_texts[] = "Search: " . htmlspecialchars($filters['search_item']);
            if ($filters['item_type']) $filter_texts[] = "Type: " . ($filters['item_type'] == 'B' ? 'Boughtout' : 'Manufacturing');
            if ($filters['stock_status']) $filter_texts[] = "Stock: " . ($filters['stock_status'] == 'low' ? 'Low Stock' : 'In Stock');
            if ($filters['sort_by']) $filter_texts[] = "Sorted By: " . ucfirst(str_replace('_', ' ', $filters['sort_by']));

            echo implode(' | ', $filter_texts);
            ?>
        </div>
    <?php endif; ?>

    <!-- Inventory Table -->
    <table>
        <thead>
            <tr>
                <th width="5%">#</th>
                <th width="12%">Item Code</th>
                <th width="20%">Description</th>
                <th width="8%">HSN</th>
                <th width="6%">GST%</th>
                <th width="8%">Type</th>
                <th width="8%">Stock</th>
                <th width="8%">Unit</th>
                <th width="10%" class="text-right">Cost Price</th>
                <th width="10%" class="text-right">Sell Price</th>
                <th width="5%">Cat</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sr_no = 1;
            $total_cost = 0;
            $total_sell = 0;

            foreach ($result as $item):
                $type_class = $item->item_type == 'B' ? 'badge-boughtout' : 'badge-manufacturing';
                $type_text = $item->item_type == 'B' ? 'BOUGHT' : 'MANUF';
                $stock_class = $item->stock <= 5 ? 'low-stock' : '';

                $total_cost += $item->cost_price;
                $total_sell += $item->sell_price;
            ?>
                <tr>
                    <td class="text-center"><?php echo $sr_no; ?></td>
                    <td><strong><?php echo htmlspecialchars($item->code); ?></strong></td>
                    <td>
                        <?php
                        $description = strip_tags($item->prod_description);
                        echo strlen($description) > 50 ? substr($description, 0, 50) . '...' : $description;
                        ?>
                    </td>
                    <td class="text-center"><?php echo $item->hsn; ?></td>
                    <td class="text-center"><?php echo $item->gst_per; ?></td>
                    <td class="text-center">
                        <span class="badge <?php echo $type_class; ?>">
                            <?php echo $type_text; ?>
                        </span>
                    </td>
                    <td class="text-center <?php echo $stock_class; ?>">
                        <?php echo $item->stock; ?>
                    </td>
                    <td class="text-center"><?php echo $item->unit; ?></td>
                    <td class="text-right price-cost">
                        ₹<?php echo number_format($item->cost_price, 2); ?>
                    </td>
                    <td class="text-right price-sell">
                        ₹<?php echo number_format($item->sell_price, 2); ?>
                    </td>
                    <td class="text-center">
                        <?php
                        $category = isset($item->category_name) ? substr($item->category_name, 0, 3) : 'N/A';
                        echo strtoupper($category);
                        ?>
                    </td>
                </tr>
            <?php
                $sr_no++;
            endforeach;

            // Add totals row
            if (count($result) > 0):
            ?>
                <tr style="background-color: #f1f8ff; font-weight: bold;">
                    <td colspan="8" class="text-right"><strong>Totals (Average):</strong></td>
                    <td class="text-right price-cost">
                        ₹<?php echo number_format($total_cost / count($result), 2); ?>
                    </td>
                    <td class="text-right price-sell">
                        ₹<?php echo number_format($total_sell / count($result), 2); ?>
                    </td>
                    <td></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <?php if (count($result) > 50): ?>
        <div class="page-break"></div>
        <!-- You can add additional sections or continue table on new page -->
    <?php endif; ?>

    <!-- Footer Notes -->
    <div class="footer">
        <p>
            <strong>Report Summary:</strong><br>
            • This report contains <?php echo count($result); ?> inventory items<br>
            • <?php echo $low_stock_count; ?> items are below reorder level (≤5 units)<br>
            • Total inventory value: ₹<?php echo number_format($total_stock_value, 2); ?><br>
            • Report generated by: User ID <?php echo $user_id; ?>
        </p>
        <p style="font-size: 7pt; color: #999; margin-top: 10px;">
            This is a computer-generated report. No signature required.<br>
            Confidential - For internal use only
        </p>
    </div>
</body>

</html>