<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (isset($session_data_head1)) {
} else {
    header($this->config->item('header'));
}
defined('BASEPATH') OR exit('No direct script access allowed');

$from_date = !empty($from_date) ? $from_date : date('d-m-Y', strtotime('first day of this month'));
$to_date = !empty($to_date) ? $to_date : date('d-m-Y', strtotime('last day of this month'));
$result = isset($result) && is_array($result) ? $result : array();

$phpRecordCount = count($result);
$cumulativeTotal = 0;
foreach ($result as $row) {
    $cumulativeTotal += (float) ($row->total ?? 0);
}
?>

<style>
    #loader {
        position: fixed;
        left: 50%;
        top: 50%;
        z-index: 9999;
        width: 60px;
        height: 60px;
        margin: -30px 0 0 -30px;
        border: 8px solid #f3f3f3;
        border-top: 8px solid #3c8dbc;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        display: none;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .hidden {
        display: none !important;
    }

    .excel-export {
        margin-bottom: 12px;
        margin-top: 5px;
        display: inline-block;
        float: right;
    }

    .box-info {
        border-top-color: #00c0ef;
    }

    .summary-stats-custom {
        background: #f9f9f9;
        padding: 10px 15px;
        border-radius: 4px;
        margin: 10px 0 15px 0;
        border: 1px solid #e3e3e3;
    }

    table.dataTable thead th {
        background-color: #f4f4f4;
    }

    .dataTables_wrapper .dt-buttons {
        margin-bottom: 10px;
    }

    .form_overlay .form-group {
        margin-bottom: 18px;
    }
</style>

<body class="hold-transition skin-blue sidebar-mini">
    <div id="loader" class="center"></div>

    <div class="wrapper">
        <div class="content-wrapper">
            <section class="content-header">
                <h1>
                    GRN Report
                    <small>Goods Receipt Note Summary</small>
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/'; ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="#">Report</a></li>
                    <li class="active">GRN Report</li>
                </ol>
            </section>

            <section class="content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="box box-info">
                            <div class="box-header with-border">
                                <h3 class="box-title">Filter GRN Records</h3>
                            </div>

                            <form class="form-horizontal form_overlay" method="post" action="<?php echo base_url('ReportController/create_grn_report'); ?>" id="grnFilterForm">
                                <div class="box-body">
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">From Date <span style="color: red;">*</span></label>
                                        <div class="col-sm-4">
                                            <input type="text" id="from_date" autocomplete="off" class="form-control backdate" value="<?php echo htmlspecialchars($from_date); ?>" name="from_date" required onkeydown="return false;">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">To Date <span style="color: red;">*</span></label>
                                        <div class="col-sm-4">
                                            <input type="text" id="to_date" autocomplete="off" class="form-control" value="<?php echo htmlspecialchars($to_date); ?>" name="to_date" required onkeydown="return false;">
                                        </div>
                                    </div>
                                </div>
                                <div class="box-footer text-center">
                                    <button type="button" class="btn btn-default" id="cancelBtn">Reset</button>
                                    <button type="submit" class="btn btn-success" id="submitBtn">Submit</button>
                                </div>
                            </form>

                            <div class="box-body">
                                <div class="clearfix">
                                    <a href="<?php echo base_url('ReportController/get_grn_report_by_date_xlsx'); ?>" id="excelExportLink" class="excel-export <?php echo $phpRecordCount > 0 ? '' : 'hidden'; ?>">
                                        <button type="button" class="btn-sm btn btn-success pull-right">
                                            <i class="fa fa-file-excel-o"></i> Export to Excel (<span id="exportRecordCount"><?php echo $phpRecordCount; ?></span> records)
                                        </button>
                                    </a>
                                </div>

                                <div class="row summary-stats-custom" id="summary-stats">
                                    <div class="col-md-6">
                                        <strong><i class="fa fa-list-alt"></i> Total Records: <span id="recordCount"><?php echo $phpRecordCount; ?></span></strong>
                                    </div>
                                    <div class="col-md-6 text-right">
                                        <strong><i class="fa fa-inr"></i> Total Amount: ₹<span id="totalAmount"><?php echo number_format($cumulativeTotal, 2); ?></span></strong>
                                    </div>
                                </div>

                                <table id="grnReportTable" class="table table-bordered table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Sr.No.</th>
                                            <th>GRN Number</th>
                                            <th>Date</th>
                                            <th>PO Number</th>
                                            <th>Product Name</th>
                                            <th>Quantity</th>
                                            <th>Received Qty</th>
                                            <th>Pending Qty</th>
                                            <th>Price</th>
                                            <th>Amount</th>
                                            <th>HSN Code</th>
                                            <th>GST %</th>
                                            <th>Supplier Name</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tableBody">
                                        <?php if (!empty($result)): ?>
                                            <?php $serial = 1; ?>
                                            <?php foreach ($result as $row): ?>
                                                <?php $rowTotal = (float) ($row->total ?? 0); ?>
                                                <tr>
                                                    <td><?php echo $serial++; ?></td>
                                                    <td><?php echo htmlspecialchars($row->grn_number ?? ''); ?></td>
                                                    <td><?php echo !empty($row->date) ? htmlspecialchars($row->date) : ''; ?></td>
                                                    <td><?php echo htmlspecialchars($row->po_number_fk ?? ''); ?></td>
                                                    <td><?php echo htmlspecialchars($row->product_name ?? ''); ?></td>
                                                    <td><?php echo number_format((float) ($row->quantity ?? 0), 0, '.', ''); ?></td>
                                                    <td><?php echo number_format((float) ($row->received_quantity ?? 0), 0, '.', ''); ?></td>
                                                    <td><?php echo number_format((float) ($row->pending_quantity ?? 0), 0, '.', ''); ?></td>
                                                    <td><?php echo number_format((float) ($row->price ?? 0), 2); ?></td>
                                                    <td><?php echo number_format((float) ($row->amount ?? 0), 2); ?></td>
                                                    <td><?php echo htmlspecialchars($row->hsn_code ?? ''); ?></td>
                                                    <td><?php echo (float) ($row->gst ?? 0); ?>%</td>
                                                    <td><?php echo htmlspecialchars($row->company_name ?? ''); ?></td>
                                                    <td><?php echo number_format($rowTotal, 2); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
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
        $(function () {
            var ajaxUrl = '<?php echo base_url("ReportController/create_grn_report"); ?>';
            var dataTableInstance = null;

            function parseDateDMY(dateStr) {
                var parts = String(dateStr || '').split('-');
                if (parts.length !== 3) {
                    return null;
                }
                return new Date(parts[2], parts[1] - 1, parts[0]);
            }

            function getCurrentMonthRange() {
                var today = new Date();
                var firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
                var lastDay = new Date(today.getFullYear(), today.getMonth() + 1, 0);

                return {
                    from: $.datepicker.formatDate('dd-mm-yy', firstDay),
                    to: $.datepicker.formatDate('dd-mm-yy', lastDay)
                };
            }

            function escapeHtml(value) {
                return $('<div>').text(value == null ? '' : value).html();
            }

            function destroyDataTable() {
                if ($.fn.DataTable.isDataTable('#grnReportTable')) {
                    $('#grnReportTable').DataTable().destroy();
                }
            }

            function initDataTable() {
                destroyDataTable();
                dataTableInstance = $('#grnReportTable').DataTable({
                    paging: true,
                    lengthChange: true,
                    searching: true,
                    ordering: true,
                    info: true,
                    autoWidth: false,
                    pageLength: 25,
                    scrollX: true,
                    language: {
                        emptyTable: 'No GRN records found for selected date range',
                        search: 'Search GRN Report:'
                    }
                });
            }

            function updateSummary(records) {
                var totalAmount = 0;

                $.each(records, function (_, item) {
                    totalAmount += parseFloat(item.total || 0);
                });

                $('#recordCount').text(records.length);
                $('#exportRecordCount').text(records.length);
                $('#totalAmount').text(totalAmount.toLocaleString('en-IN', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }));

                if (records.length > 0) {
                    $('#excelExportLink').removeClass('hidden');
                } else {
                    $('#excelExportLink').addClass('hidden');
                }
            }

            function renderRows(records) {
                var rowsHtml = '';

                $.each(records, function (index, item) {
                    var total = parseFloat(item.total || 0).toFixed(2);
                    var amount = parseFloat(item.amount || 0).toFixed(2);
                    var price = parseFloat(item.price || 0).toFixed(2);
                    var quantity = parseFloat(item.quantity || 0);
                    var receivedQty = parseFloat(item.received_quantity || 0);
                    var pendingQty = parseFloat(item.pending_quantity || 0);

                    rowsHtml += '<tr>' +
                        '<td>' + (index + 1) + '</td>' +
                        '<td>' + escapeHtml(item.grn_number || '') + '</td>' +
                        '<td>' + escapeHtml(item.date || '') + '</td>' +
                        '<td>' + escapeHtml(item.po_number_fk || '') + '</td>' +
                        '<td>' + escapeHtml(item.product_name || '') + '</td>' +
                        '<td>' + quantity.toLocaleString('en-IN', { maximumFractionDigits: 0 }) + '</td>' +
                        '<td>' + receivedQty.toLocaleString('en-IN', { maximumFractionDigits: 0 }) + '</td>' +
                        '<td>' + pendingQty.toLocaleString('en-IN', { maximumFractionDigits: 0 }) + '</td>' +
                        '<td>' + Number(price).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + '</td>' +
                        '<td>' + Number(amount).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + '</td>' +
                        '<td>' + escapeHtml(item.hsn_code || '') + '</td>' +
                        '<td>' + escapeHtml(item.gst || 0) + '%</td>' +
                        '<td>' + escapeHtml(item.company_name || '') + '</td>' +
                        '<td>' + Number(total).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + '</td>' +
                        '</tr>';
                });

                $('#tableBody').html(rowsHtml);
                updateSummary(records);
                initDataTable();
            }

            function validateDateRange(fromDate, toDate) {
                if (!fromDate || !toDate) {
                    alert('Please select both From Date and To Date.');
                    return false;
                }

                var fromObj = parseDateDMY(fromDate);
                var toObj = parseDateDMY(toDate);

                if (!fromObj || !toObj || fromObj > toObj) {
                    alert('From Date cannot be greater than To Date.');
                    return false;
                }

                return true;
            }

            function refreshReport(fromDate, toDate) {
                if (!validateDateRange(fromDate, toDate)) {
                    return;
                }

                $('#loader').show();
                $('#submitBtn').prop('disabled', true);

                $.ajax({
                    url: ajaxUrl,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        from_date: fromDate,
                        to_date: toDate
                    }
                }).done(function (response) {
                    var records = response && $.isArray(response.result) ? response.result : [];
                    renderRows(records);
                }).fail(function () {
                    alert('Unable to fetch GRN report. Please try again.');
                }).always(function () {
                    $('#loader').hide();
                    $('#submitBtn').prop('disabled', false);
                });
            }

            $('#from_date').datepicker({
                maxDate: '0',
                dateFormat: 'dd-mm-yy',
                changeMonth: true,
                changeYear: true,
                yearRange: '2020:2035',
                onSelect: function (selectedDate) {
                    $('#to_date').datepicker('option', 'minDate', selectedDate);

                    if (parseDateDMY($('#to_date').val()) < parseDateDMY(selectedDate)) {
                        $('#to_date').val(selectedDate);
                    }

                    refreshReport($('#from_date').val(), $('#to_date').val());
                }
            });

            $('#to_date').datepicker({
                maxDate: '0',
                dateFormat: 'dd-mm-yy',
                changeMonth: true,
                changeYear: true,
                yearRange: '2020:2035',
                onSelect: function (selectedDate) {
                    $('#from_date').datepicker('option', 'maxDate', selectedDate);

                    if (parseDateDMY($('#from_date').val()) > parseDateDMY(selectedDate)) {
                        $('#from_date').val(selectedDate);
                    }

                    refreshReport($('#from_date').val(), $('#to_date').val());
                }
            });

            $('#grnFilterForm').on('submit', function (e) {
                e.preventDefault();
                refreshReport($('#from_date').val().trim(), $('#to_date').val().trim());
            });

            $('#cancelBtn').on('click', function () {
                var range = getCurrentMonthRange();
                $('#from_date').val(range.from);
                $('#to_date').val(range.to);
                $('#to_date').datepicker('option', 'minDate', range.from);
                $('#from_date').datepicker('option', 'maxDate', range.to);
                refreshReport(range.from, range.to);
            });

            $('#excelExportLink').on('click', function () {
                if ($('#recordCount').text() === '0') {
                    return false;
                }
            });

            $('#to_date').datepicker('option', 'minDate', $('#from_date').val());
            $('#from_date').datepicker('option', 'maxDate', $('#to_date').val());

            initDataTable();
            refreshReport($('#from_date').val(), $('#to_date').val());
        });
    </script>
</body>
</html>
