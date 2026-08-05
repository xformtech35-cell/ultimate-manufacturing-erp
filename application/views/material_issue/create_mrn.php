<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (isset($session_data_head1)) {
} else {
    header($this->config->item('header'));
}
defined('BASEPATH') or exit('No direct script access allowed');
$_has_project_master = isset($session_data_head1['permission']) && in_array('Projects', $session_data_head1['permission']);
?>

<body class="hold-transition skin-blue sidebar-mini">
    <div id="loader" class="center"></div>
    <div class="wrapper">
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    Material Return Note (MRN)
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="<?php echo base_url('MaterialIssueController/index') ?>">Material Issue</a></li>
                    <li class="active">Create MRN</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-warning" style="border-top: 3px solid #f39c12;">
                            <div class="box-header">
                                <h3 class="box-title">Create Material Return Note (Production to Store)</h3>
                                <a href="<?php echo base_url('MaterialIssueController/index') ?>"
                                    class="btn btn-default pull-right">
                                    <i class="fa fa-arrow-left"></i> Back to List
                                </a>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">

                                <?php if ($this->session->flashdata('ERRORMSG')): ?>
                                <div role="alert" class="alert alert-danger">
                                    <button data-dismiss="alert" class="close" type="button"><span
                                            aria-hidden="true">&times;</span></button>
                                    <strong>Error!</strong> <?php echo $this->session->flashdata('ERRORMSG'); ?>
                                </div>
                                <?php endif; ?>

                                <form method="post" action="<?php echo base_url('MaterialIssueController/create_mrn') ?>"
                                    id="mrnForm">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Return Date <span class="text-danger">*</span></label>
                                                <input type="text" name="return_date" class="form-control datepicker"
                                                    value="<?= date('d-m-Y') ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Returned By (Production Supervisor/Person) <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" name="returned_by" class="form-control" required placeholder="Enter name of person returning material">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Department</label>
                                                <select name="department" class="form-control select2">
                                                    <option value="Production" selected>Production</option>
                                                    <option value="Construction">Construction</option>
                                                    <option value="Maintenance">Maintenance</option>
                                                    <option value="Store">Store</option>
                                                    <option value="Office">Office</option>
                                                </select>
                                            </div>
                                        </div>
                                         <?php if ($_has_project_master): ?>
                                         <div class="col-md-6">
                                             <div class="form-group">
                                                 <label>Project Code</label>
                                                 <select name="project_code" id="project_code" class="form-control select2">
                                                     <option value="">Select Project</option>
                                                     <?php foreach ($projects as $project): ?>
                                                     <option value="<?= $project->project_code ?>">
                                                         <?= $project->project_code ?> - <?= $project->project_name ?>
                                                     </option>
                                                     <?php endforeach; ?>
                                                 </select>
                                             </div>
                                         </div>
                                         <?php endif; ?>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-10">
                                            <div class="form-group">
                                                <label>Job Order <span class="text-danger">*</span></label>
                                                <select name="joborder_number" id="joborder_number"
                                                    class="form-control" style="width: 100%;" required
                                                    onchange="loadJobOrderItems()">
                                                    <option value="">-- Select Job Order --</option>
                                                    <?php foreach ($joborders as $jo): ?>
                                                        <option value="<?= htmlspecialchars($jo->number_fk) ?>">
                                                            <?= htmlspecialchars($jo->number_fk) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>&nbsp;</label>
                                                <button type="button" class="btn btn-info btn-block" onclick="loadJobOrderItems()">
                                                    <i class="fa fa-refresh"></i> Load Items
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label>Remarks / Reason for Return</label>
                                        <textarea name="remarks" class="form-control" rows="2" placeholder="e.g. Excess material returned from assembly line"></textarea>
                                    </div>

                                    <h4>Items Details</h4>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover" id="itemsTable">
                                            <thead>
                                                <tr style="background:#f39c12; color:#fff;">
                                                    <th width="30%">Item Code & Name</th>
                                                    <th class="text-right" width="10%">Required Qty</th>
                                                    <th class="text-right" width="12%">Gross Issued Qty</th>
                                                    <th class="text-right" width="12%">Already Returned Qty</th>
                                                    <th class="text-right" width="12%">Qty with Production</th>
                                                    <th class="text-right" width="12%">Qty to Return *</th>
                                                    <th width="12%">Remarks</th>
                                                </tr>
                                            </thead>
                                            <tbody id="itemsBody">
                                                <tr>
                                                    <td colspan="7" class="text-center text-muted" style="padding:20px;">
                                                        Please select a Job Order to fetch items.
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="form-group" style="margin-top: 20px;">
                                        <button type="submit" class="btn btn-warning" id="submitBtn" disabled>
                                            <i class="fa fa-save"></i> Save Material Return Note
                                        </button>
                                        <a href="<?php echo base_url('MaterialIssueController/index') ?>"
                                            class="btn btn-default">
                                            Cancel
                                        </a>
                                    </div>
                                </form>
                            </div>
                            <!-- /.box-body -->
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <!-- Scripts -->
    <script>
    var BASE_URL = '<?php echo base_url(); ?>';

    // loadJobOrderItems is declared globally so both event bindings can use it
    function loadJobOrderItems() {
        var jo_number = $('#joborder_number').val();
        if (!jo_number) {
            $('#itemsBody').html('<tr><td colspan="7" class="text-center text-muted" style="padding:20px;">Please select a Job Order to fetch items.</td></tr>');
            $('#submitBtn').prop('disabled', true);
            return;
        }

        $('#itemsBody').html('<tr><td colspan="7" class="text-center"><i class="fa fa-spinner fa-spin fa-2x"></i><br>Fetching items...</td></tr>');

        $.ajax({
            url: BASE_URL + 'MaterialIssueController/get_joborder_items',
            type: 'GET',
            data: { number: jo_number },
            dataType: 'json',
            success: function(res) {
                if (res.status === 'success' && res.data && res.data.length > 0) {
                    var html = '';
                    var hasIssuableItems = false;

                    $.each(res.data, function(index, item) {
                        if (item.gross_issued_qty > 0) {
                            hasIssuableItems = true;
                            html += '<tr>';
                            html += '<td><strong><code>' + item.item_code + '</code></strong> - ' + item.item_name;
                            html += '<input type="hidden" name="inventory_id[]" value="' + item.inventory_id + '"></td>';
                            html += '<td class="text-right">' + parseFloat(item.required_qty).toFixed(2) + ' ' + (item.unit || '') + '</td>';
                            html += '<td class="text-right text-info">' + parseFloat(item.gross_issued_qty).toFixed(2) + ' ' + (item.unit || '') + '</td>';
                            html += '<td class="text-right text-warning">' + parseFloat(item.returned_qty).toFixed(2) + ' ' + (item.unit || '') + '</td>';
                            html += '<td class="text-right text-success">' + parseFloat(item.net_issued_qty).toFixed(2) + ' ' + (item.unit || '') + '</td>';
                            html += '<td>';
                            html += '<div class="input-group">';
                            html += '<input type="number" name="quantity[]" class="form-control quantity-input" data-index="' + index + '" data-max="' + item.net_issued_qty + '" step="0.01" min="0" placeholder="0.00" style="text-align:right;">';
                            html += '<span class="input-group-addon">' + (item.unit || '') + '</span>';
                            html += '</div>';
                            html += '<span class="help-block text-red qty-err" id="err_' + index + '" style="display:none;font-size:11px;"></span>';
                            html += '</td>';
                            html += '<td><input type="text" name="item_remarks[]" class="form-control" placeholder="Remarks"></td>';
                            html += '</tr>';
                        }
                    });

                    if (hasIssuableItems) {
                        $('#itemsBody').html(html);
                        $('#submitBtn').prop('disabled', false);
                    } else {
                        $('#itemsBody').html('<tr><td colspan="7" class="text-center text-danger" style="padding:20px;"><i class="fa fa-info-circle"></i> No issued items found for this Job Order.</td></tr>');
                        $('#submitBtn').prop('disabled', true);
                    }
                } else {
                    $('#itemsBody').html('<tr><td colspan="7" class="text-center text-danger" style="padding:20px;"><i class="fa fa-exclamation-triangle"></i> ' + (res.message || 'No items found.') + '</td></tr>');
                    $('#submitBtn').prop('disabled', true);
                }
            },
            error: function(xhr, status, err) {
                $('#itemsBody').html('<tr><td colspan="7" class="text-center text-danger" style="padding:20px;"><i class="fa fa-exclamation-triangle"></i> Server error: ' + err + '</td></tr>');
                $('#submitBtn').prop('disabled', true);
            }
        });
    }

    $(document).ready(function() {

        // Datepicker
        $('.datepicker').datepicker({ format: 'dd-mm-yyyy', autoclose: true, todayHighlight: true });

        // Document-level delegation — fires regardless of select2 re-initialization
        $(document).on('change', '#joborder_number', function() {
            loadJobOrderItems();
        });

        // After page fully loads (including footer.php scripts), re-init select2 with search
        $(window).on('load', function() {
            setTimeout(function() {
                if ($.fn.select2) {
                    if ($('#joborder_number').hasClass('select2-hidden-accessible')) {
                        $('#joborder_number').select2('destroy');
                    }
                    $('#joborder_number').select2({
                        placeholder: 'Search or select a Job Order...',
                        allowClear: true,
                        minimumResultsForSearch: 0,
                        width: '100%'
                    });
                    // Re-bind after re-init
                    $('#joborder_number').on('select2:select select2:clear', function() {
                        loadJobOrderItems();
                    });
                }
            }, 200);
        });

        // Form validation on submit
        $('#mrnForm').on('submit', function(e) {
            var isValid = true;
            var anyValueEntered = false;

            $('.quantity-input').each(function() {
                var val = parseFloat($(this).val()) || 0;
                var max = parseFloat($(this).data('max')) || 0;
                var idx = $(this).data('index');
                var errSpan = $('#err_' + idx);
                errSpan.hide().text('');

                if (val > 0) {
                    anyValueEntered = true;
                    if (val > max) {
                        errSpan.text('Max returnable: ' + max.toFixed(2)).show();
                        isValid = false;
                    }
                }
            });

            if (!anyValueEntered) {
                alert('Please enter at least one quantity to return.');
                isValid = false;
            }
            if (!isValid) e.preventDefault();
        });
    });
    </script>
</body>
</html>
