<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (!isset($session_data_head1)) {
    header($this->config->item('header'));
    exit;
}
defined('BASEPATH') or exit('No direct script access allowed');

// Calculate default financial year dates
$fy_year = $this->session->userdata('fy_year');
if (!empty($fy_year) && $fy_year !== 'all') {
    $default_from = '01-04-' . $fy_year;
    $default_to   = '31-03-' . ($fy_year + 1);
} else {
    $cur_month  = (int)date('m');
    $cur_year   = (int)date('Y');
    $start_year = ($cur_month >= 4) ? $cur_year : ($cur_year - 1);
    $default_from = '01-04-' . $start_year;
    $default_to   = date('d-m-Y');
}
?>

<style>
    .ledger-card {
        border-radius: 8px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        border: 1px solid #e1e8ed;
        margin-bottom: 25px;
        transition: transform 0.2s, box-shadow 0.2s;
        background: #fff;
    }
    .ledger-card:hover {
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.12);
    }
    .ledger-card .box-header {
        padding: 16px 20px;
        border-bottom: 1px solid #f0f3f6;
        border-top-left-radius: 8px;
        border-top-right-radius: 8px;
    }
    .ledger-card .box-header .box-title {
        font-size: 17px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .ledger-card .box-body {
        padding: 22px 24px;
    }
    .ledger-card .box-footer {
        padding: 15px 24px;
        background: #fcfdfe;
        border-top: 1px solid #f0f3f6;
        border-bottom-left-radius: 8px;
        border-bottom-right-radius: 8px;
    }
    .preset-btn-group {
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
        margin-bottom: 15px;
    }
    .preset-btn {
        font-size: 11px;
        padding: 3px 9px;
        border-radius: 12px;
        border: 1px solid #d2d6de;
        background: #fff;
        color: #555;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .preset-btn:hover {
        background: #3c8dbc;
        border-color: #3c8dbc;
        color: #fff;
    }
    .preset-btn-success:hover {
        background: #00a65a;
        border-color: #00a65a;
        color: #fff;
    }
    .select2-container .select2-selection--single {
        height: 38px !important;
        border: 1px solid #d2d6de !important;
        border-radius: 4px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 36px !important;
        padding-left: 12px !important;
        color: #333 !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px !important;
    }
    .form-group label {
        font-weight: 600;
        color: #374151;
        margin-bottom: 6px;
    }
    .required-star {
        color: #e53e3e;
        font-weight: bold;
    }
    .helper-callout {
        background: linear-gradient(135deg, #eef6ff 0%, #f0fdf4 100%);
        border-left: 4px solid #3b82f6;
        border-radius: 6px;
        padding: 14px 18px;
        margin-bottom: 22px;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.04);
    }
</style>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <i class="fa fa-book text-primary"></i> Ledger Report
            <small>Customer (Sales) & Vendor (Purchase) Account Statements</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo base_url('Home/index/'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Report</a></li>
            <li class="active">Ledger Report</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">

        <!-- Top Info Banner -->
        <div class="helper-callout">
            <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <span style="font-size: 24px; color: #2563eb;"><i class="fa fa-info-circle"></i></span>
                    <div>
                        <strong style="color: #1e3a8a; font-size: 14px;">Generate Financial Ledger Statements</strong>
                        <div style="color: #4b5563; font-size: 12.5px; margin-top: 2px;">
                            Filter by date range and select a company to view comprehensive transaction history (Invoices, Receipts, Purchase Bills, Payments & Running Balance).
                        </div>
                    </div>
                </div>
                <div>
                    <span class="badge" style="background-color: #2563eb; padding: 6px 12px; font-size: 12px;">
                        <i class="fa fa-calendar-check-o"></i> Active FY: <?php echo (!empty($fy_year) && $fy_year !== 'all') ? ($fy_year . '-' . ($fy_year + 1)) : 'All Data'; ?>
                    </span>
                </div>
            </div>
        </div>

        <div class="row">

            <!-- ══════════════════════════════════════════════
                 LEFT: Sales Ledger (Customer Statement)
            ═══════════════════════════════════════════════ -->
            <div class="col-md-6 col-sm-12">
                <div class="box box-primary ledger-card">
                    <div class="box-header with-border">
                        <h3 class="box-title text-primary">
                            <i class="fa fa-user-circle"></i> Sales Ledger (Customer)
                        </h3>
                        <span class="label label-primary pull-right" style="font-size: 11px; padding: 4px 8px; border-radius: 4px;">
                            <i class="fa fa-arrow-down"></i> Receivables
                        </span>
                    </div>

                    <form method="post" action="<?php echo base_url(); ?>PaymentController/get_gst_ledger" id="salesLedgerForm" class="form_overlay">
                        <div class="box-body">

                            <!-- Quick Date Presets -->
                            <div class="form-group" style="margin-bottom: 12px;">
                                <label style="font-size: 12px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px;">Quick Date Range:</label>
                                <div class="preset-btn-group">
                                    <button type="button" class="preset-btn" onclick="setDates('sales', 'fy')"><i class="fa fa-calendar"></i> Current FY</button>
                                    <button type="button" class="preset-btn" onclick="setDates('sales', 'month')"><i class="fa fa-clock-o"></i> This Month</button>
                                    <button type="button" class="preset-btn" onclick="setDates('sales', 'last30')"><i class="fa fa-history"></i> Last 30 Days</button>
                                    <button type="button" class="preset-btn" onclick="setDates('sales', 'clear')"><i class="fa fa-times"></i> Reset</button>
                                </div>
                            </div>

                            <!-- Date Range (Row) -->
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="from_date">From Date <span class="required-star">*</span></label>
                                        <div class="input-group">
                                            <input type="text" id="from_date" name="from_date" class="form-control backdate" value="<?php echo htmlspecialchars($default_from); ?>" required autocomplete="off" placeholder="DD-MM-YYYY">
                                            <span class="input-group-addon" style="cursor: pointer;" onclick="$('#from_date').focus();"><i class="fa fa-calendar text-primary"></i></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="to_date">To Date <span class="required-star">*</span></label>
                                        <div class="input-group">
                                            <input type="text" id="to_date" name="to_date" class="form-control backdate" value="<?php echo htmlspecialchars($default_to); ?>" required autocomplete="off" placeholder="DD-MM-YYYY">
                                            <span class="input-group-addon" style="cursor: pointer;" onclick="$('#to_date').focus();"><i class="fa fa-calendar text-primary"></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Customer / Company Dropdown -->
                            <div class="form-group" style="margin-top: 5px;">
                                <label for="company_name">Customer / Company Name <span class="required-star">*</span></label>
                                <select class="form-control select2" name="company_name" id="company_name" required style="width: 100%;">
                                    <option value="">-- Search & Select Customer --</option>
                                    <?php if (!empty($company_name) && is_array($company_name)): ?>
                                        <?php foreach ($company_name as $key): ?>
                                            <option value="<?php echo $key->customer_id; ?>">
                                                <?php echo htmlspecialchars($key->company_name . (!empty($key->c_code) ? " (" . $key->c_code . ")" : "")); ?>
                                            </option> 
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <!-- Target Option -->
                            <div class="checkbox" style="margin-top: 15px; margin-bottom: 0;">
                                <label style="font-size: 13px; color: #4b5563;">
                                    <input type="checkbox" id="sales_new_tab" checked onchange="toggleFormTarget('salesLedgerForm', this.checked)">
                                    Open ledger in new tab / printable window
                                </label>
                            </div>

                        </div>

                        <div class="box-footer clearfix">
                            <button type="reset" class="btn btn-default pull-left" style="font-weight: 600;">
                                <i class="fa fa-undo"></i> Reset
                            </button>
                            <button type="submit" class="btn btn-primary pull-right" style="font-weight: 600; padding: 6px 20px;">
                                <i class="fa fa-file-text-o"></i> View Sales Ledger
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- ══════════════════════════════════════════════
                 RIGHT: Purchase Ledger (Vendor Statement)
            ═══════════════════════════════════════════════ -->
            <div class="col-md-6 col-sm-12">
                <div class="box box-success ledger-card">
                    <div class="box-header with-border">
                        <h3 class="box-title text-success">
                            <i class="fa fa-truck"></i> Purchase Ledger (Vendor / Supplier)
                        </h3>
                        <span class="label label-success pull-right" style="font-size: 11px; padding: 4px 8px; border-radius: 4px;">
                            <i class="fa fa-arrow-up"></i> Payables
                        </span>
                    </div>

                    <form method="post" action="<?php echo base_url(); ?>PaymentController/get_purchse_ledger" id="purchaseLedgerForm" class="form_overlay">
                        <div class="box-body">

                            <!-- Quick Date Presets -->
                            <div class="form-group" style="margin-bottom: 12px;">
                                <label style="font-size: 12px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px;">Quick Date Range:</label>
                                <div class="preset-btn-group">
                                    <button type="button" class="preset-btn preset-btn-success" onclick="setDates('purchase', 'fy')"><i class="fa fa-calendar"></i> Current FY</button>
                                    <button type="button" class="preset-btn preset-btn-success" onclick="setDates('purchase', 'month')"><i class="fa fa-clock-o"></i> This Month</button>
                                    <button type="button" class="preset-btn preset-btn-success" onclick="setDates('purchase', 'last30')"><i class="fa fa-history"></i> Last 30 Days</button>
                                    <button type="button" class="preset-btn preset-btn-success" onclick="setDates('purchase', 'clear')"><i class="fa fa-times"></i> Reset</button>
                                </div>
                            </div>

                            <!-- Date Range (Row) -->
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="from_date2">From Date <span class="required-star">*</span></label>
                                        <div class="input-group">
                                            <input type="text" id="from_date2" name="from_date" class="form-control backdate" value="<?php echo htmlspecialchars($default_from); ?>" required autocomplete="off" placeholder="DD-MM-YYYY">
                                            <span class="input-group-addon" style="cursor: pointer;" onclick="$('#from_date2').focus();"><i class="fa fa-calendar text-success"></i></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="to_date2">To Date <span class="required-star">*</span></label>
                                        <div class="input-group">
                                            <input type="text" id="to_date2" name="to_date" class="form-control backdate" value="<?php echo htmlspecialchars($default_to); ?>" required autocomplete="off" placeholder="DD-MM-YYYY">
                                            <span class="input-group-addon" style="cursor: pointer;" onclick="$('#to_date2').focus();"><i class="fa fa-calendar text-success"></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Supplier / Company Dropdown -->
                            <div class="form-group" style="margin-top: 5px;">
                                <label for="supplier_name">Supplier / Vendor Name <span class="required-star">*</span></label>
                                <select class="form-control select2" name="supplier_name" id="supplier_name" required style="width: 100%;">
                                    <option value="">-- Search & Select Vendor / Supplier --</option>
                                    <?php if (!empty($result) && is_array($result)): ?>
                                        <?php foreach ($result as $key): ?>
                                            <option value="<?php echo $key->supplier_id; ?>">
                                                <?php echo htmlspecialchars($key->company_name . (!empty($key->s_code) ? " (" . $key->s_code . ")" : "")); ?>
                                            </option> 
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <!-- Target Option -->
                            <div class="checkbox" style="margin-top: 15px; margin-bottom: 0;">
                                <label style="font-size: 13px; color: #4b5563;">
                                    <input type="checkbox" id="purchase_new_tab" checked onchange="toggleFormTarget('purchaseLedgerForm', this.checked)">
                                    Open ledger in new tab / printable window
                                </label>
                            </div>

                        </div>

                        <div class="box-footer clearfix">
                            <button type="reset" class="btn btn-default pull-left" style="font-weight: 600;">
                                <i class="fa fa-undo"></i> Reset
                            </button>
                            <button type="submit" class="btn btn-success pull-right" style="font-weight: 600; padding: 6px 20px;">
                                <i class="fa fa-file-text-o"></i> View Purchase Ledger
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
        <!-- /.row -->

    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<script>
$(document).ready(function() {
    // Initialize Select2 searchable dropdowns
    if ($.fn.select2) {
        $('#company_name').select2({
            placeholder: "-- Search & Select Customer --",
            allowClear: true,
            width: '100%'
        });
        $('#supplier_name').select2({
            placeholder: "-- Search & Select Vendor / Supplier --",
            allowClear: true,
            width: '100%'
        });
    }

    // Initialize datepickers
    if ($.fn.datepicker) {
        $('.backdate').datepicker({
            dateFormat: 'dd-mm-yy',
            changeMonth: true,
            changeYear: true,
            autoclose: true,
            todayHighlight: true
        });
    }

    // Set initial form targets to new tab
    toggleFormTarget('salesLedgerForm', true);
    toggleFormTarget('purchaseLedgerForm', true);

    // Form validations
    $('#salesLedgerForm').on('submit', function(e) {
        var comp = $('#company_name').val();
        if (!comp) {
            e.preventDefault();
            alert('Please select a Customer / Company first.');
            if ($.fn.select2) {
                $('#company_name').select2('open');
            } else {
                $('#company_name').focus();
            }
            return false;
        }
    });

    $('#purchaseLedgerForm').on('submit', function(e) {
        var supp = $('#supplier_name').val();
        if (!supp) {
            e.preventDefault();
            alert('Please select a Supplier / Vendor first.');
            if ($.fn.select2) {
                $('#supplier_name').select2('open');
            } else {
                $('#supplier_name').focus();
            }
            return false;
        }
    });
});

function toggleFormTarget(formId, openNewTab) {
    var form = document.getElementById(formId);
    if (form) {
        if (openNewTab) {
            form.setAttribute('target', '_blank');
        } else {
            form.removeAttribute('target');
        }
    }
}

function setDates(type, range) {
    var fromId = (type === 'sales') ? '#from_date' : '#from_date2';
    var toId   = (type === 'sales') ? '#to_date'   : '#to_date2';
    var now = new Date();

    function formatDate(d) {
        var day = String(d.getDate()).padStart(2, '0');
        var mon = String(d.getMonth() + 1).padStart(2, '0');
        var yr  = d.getFullYear();
        return day + '-' + mon + '-' + yr;
    }

    if (range === 'fy') {
        var curM = now.getMonth() + 1;
        var startY = (curM >= 4) ? now.getFullYear() : (now.getFullYear() - 1);
        $(fromId).val('01-04-' + startY);
        $(toId).val(formatDate(now));
    } else if (range === 'month') {
        var firstDay = new Date(now.getFullYear(), now.getMonth(), 1);
        $(fromId).val(formatDate(firstDay));
        $(toId).val(formatDate(now));
    } else if (range === 'last30') {
        var prior30 = new Date();
        prior30.setDate(prior30.getDate() - 30);
        $(fromId).val(formatDate(prior30));
        $(toId).val(formatDate(now));
    } else if (range === 'clear') {
        $(fromId).val('');
        $(toId).val('');
    }
}
</script>

<?php $this->load->view('admin/footer'); ?>
<div class="control-sidebar-bg"></div>
</div>
<!-- ./wrapper -->
