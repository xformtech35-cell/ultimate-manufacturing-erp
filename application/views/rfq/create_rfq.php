<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (!isset($session_data_head1)) {
    header($this->config->item('header'));
}
defined('BASEPATH') or exit('No direct script access allowed');
?>

<body class="hold-transition skin-blue sidebar-mini">
    <div id="loader" class="center"></div>

    <div class="content-wrapper">

        <section class="content-header">
            <h1>Create RFQ</h1>
        </section>

        <section class="content">

            <div class="box box-info">
                <div class="box-header">
                    <h3 class="box-title">RFQ for PR No: </h3>
                </div>

                <form method="POST" action="<?= base_url('RFQController/save_rfq'); ?>">

                    <input type="hidden" name="rfq_no" value="<?= $rfq_no ?>">

                    <div class="box-body">

                        <div class="row">
                            <div class="col-md-3">
                                <label>RFQ Number</label>
                                <input type="text" class="form-control" value="<?= $rfq_no ?>" readonly>
                            </div>

                            <div class="col-md-3">
                                <label>RFQ Date</label>
                                <input type="text" class="form-control" name="rfq_date" id="rfq_date" required>
                            </div>
                        </div>

                        <hr>

                        <h4>PR Items</h4>

                        <table class="table table-bordered table-condensed" style="font-size: 12px;">
                            <thead>
                                <tr>
                                    <th>PR NO</th>
                                    <th>Item</th>
                                    <th>Description</th>
                                    <th>Qty</th>
                                    <th>Unit</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pr_items as $item): ?>
                                    <tr>
                                        <td><?= $item->pr_no ?></td>
                                        <td><?= $item->item_code ?></td>
                                        <td><?= $item->description ?></td>
                                        <td><?= $item->quantity ?></td>
                                        <td><?= $item->unit ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <input type="hidden" name="pr_id" value="<?= $pr_items[0]->pr_id ?? '' ?>">
                        <?php foreach ($pr_items as $item): ?>
                            <input type="hidden" name="item_id[]" value="<?= $item->item_id ?>">
                        <?php endforeach; ?>

                        <hr>

                        <h4>Select Suppliers</h4>

                        <!-- Compact Search -->
                        <div class="row" style="margin-bottom: 10px;">
                            <div class="col-md-8">
                                <div class="input-group input-group-sm">
                                    <input type="text" id="supplierSearch" class="form-control" placeholder="Search suppliers..." onkeyup="filterSuppliers()">
                                    <span class="input-group-btn">
                                        <button type="button" class="btn btn-default" onclick="clearSearch()">
                                            <i class="fa fa-times"></i>
                                        </button>
                                        <button type="button" class="btn btn-primary" onclick="selectAllVisible()">
                                            <i class="fa fa-check"></i> All
                                        </button>
                                        <button type="button" class="btn btn-default" onclick="deselectAll()">
                                            <i class="fa fa-times"></i> None
                                        </button>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-4 text-right">
                                <small id="selectedCount" class="text-muted">Selected: 0</small>
                            </div>
                        </div>

                        <!-- Compact Suppliers List -->
                        <?php if (!empty($suppliers)): ?>
                            <div class="row" id="suppliersContainer" style="max-height: 320px; overflow-y: auto; border: 1px solid #ddd; padding: 5px;">
                                <?php foreach ($suppliers as $sup): ?>
                                    <div class="col-md-6 col-sm-6 supplier-item" style="margin-bottom: 5px;">
                                        <div class="checkbox" style="margin: 0; padding: 3px; border-bottom: 1px solid #f0f0f0;">
                                            <label style="margin: 0; font-size: 12px; width: 100%;">
                                                <input type="checkbox" name="suppliers[]" value="<?= $sup['supplier_id'] ?>"
                                                    class="supplier-checkbox" onchange="updateCount()">
                                                <strong style="font-size: 12px;"><?= htmlspecialchars($sup['company_name']) ?></strong>
                                                <?php if (!empty($sup['contact_person'])): ?>
                                                    <br><small class="text-muted"><?= htmlspecialchars($sup['contact_person']) ?></small>
                                                <?php endif; ?>
                                            </label>

                                            <!-- Email list for selection (Main / CC) -->
                                            <?php
                                            $emails_str = $sup['email'] ?? '';
                                            $emails_str = str_replace([';', ' ', '/'], ',', $emails_str);
                                            $emails_arr = array_filter(array_map('trim', explode(',', $emails_str)));
                                            ?>
                                            <div class="supplier-emails-box" id="emails_for_<?= $sup['supplier_id'] ?>" style="display: none; padding-left: 20px; margin-top: 5px; border-left: 2px solid #3c8dbc; margin-bottom: 5px;">
                                                <div id="emails_list_<?= $sup['supplier_id'] ?>">
                                                    <?php if (!empty($emails_arr)): ?>
                                                        <?php foreach ($emails_arr as $index => $email): ?>
                                                            <div style="margin-bottom: 4px; font-size: 11px; display: flex; align-items: center; flex-wrap: wrap; gap: 5px;">
                                                                <label style="font-weight: normal; margin-bottom: 0; display: inline-flex; align-items: center; gap: 4px; cursor: pointer;">
                                                                    <input type="checkbox" name="supplier_emails[<?= $sup['supplier_id'] ?>][]" value="<?= htmlspecialchars($email) ?>" checked>
                                                                    <?= htmlspecialchars($email) ?>
                                                                </label>
                                                                <select name="supplier_email_type[<?= $sup['supplier_id'] ?>][<?= htmlspecialchars($email) ?>]" style="font-size: 10px; padding: 1px 3px; height: 18px; line-height: 12px; border: 1px solid #ccc; border-radius: 3px; margin-left: auto;">
                                                                    <option value="main" <?= $index === 0 ? 'selected' : '' ?>>Main (To)</option>
                                                                    <option value="cc" <?= $index > 0 ? 'selected' : '' ?>>CC</option>
                                                                </select>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    <?php else: ?>
                                                        <small class="text-danger" id="no_email_msg_<?= $sup['supplier_id'] ?>"><i class="fa fa-exclamation-triangle"></i> No email registered</small>
                                                    <?php endif; ?>
                                                </div>
                                                <!-- Add new email inline form -->
                                                <div style="margin-top: 8px; display: flex; gap: 4px; align-items: center;">
                                                    <input type="email" id="new_email_input_<?= $sup['supplier_id'] ?>" placeholder="new.email@example.com" style="font-size: 10px; padding: 2px 4px; height: 20px; border: 1px solid #ccc; border-radius: 3px; width: 130px;" onkeydown="if(event.key==='Enter') { event.preventDefault(); addNewEmailRFQ(<?= $sup['supplier_id'] ?>); }">
                                                    <button type="button" class="btn btn-xs btn-primary" onclick="addNewEmailRFQ(<?= $sup['supplier_id'] ?>)" style="padding: 2px 6px; font-size: 9px; height: 20px; line-height: 12px;">
                                                        <i class="fa fa-plus"></i> Add
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <div id="noResults" class="alert alert-warning" style="display: none; padding: 5px; margin-top: 5px; font-size: 12px;">
                                No matching suppliers
                            </div>
                        <?php else: ?>
                            <div class="alert alert-danger" style="padding: 8px; font-size: 12px;">
                                No suppliers found
                            </div>
                        <?php endif; ?>

                    </div>

                    <div id="emailPreviewSummary" class="box box-solid box-default" style="margin-top: 15px; display: none;">
                        <div class="box-header with-border" style="background-color: #f7f7f7;">
                            <h4 class="box-title" style="font-size: 13px; font-weight: bold;"><i class="fa fa-envelope-o"></i> Email Distribution Preview</h4>
                        </div>
                        <div class="box-body" style="padding: 10px; font-size: 12px;">
                            <table class="table table-condensed table-bordered no-datatable" style="margin-bottom: 0;">
                                <thead>
                                    <tr style="background-color: #f9f9f9;">
                                        <th style="width: 40%;">Supplier / Vendor</th>
                                        <th>To (Main Recipient)</th>
                                        <th>CC (Carbon Copy)</th>
                                    </tr>
                                </thead>
                                <tbody id="previewSummaryBody">
                                </tbody>
                            </table>
                            
                            <div id="previewAdditionalCcContainer" style="margin-top: 8px; padding-top: 8px; border-top: 1px dashed #ddd; display: none;">
                                <strong>Additional CC:</strong> <span id="previewAdditionalCcList" class="text-primary"></span>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <h4>Email Options</h4>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="send_email" value="1" id="send_email_chk" checked onchange="toggleEmailOptions()">
                                    <strong>Send RFQ to selected vendors via email</strong>
                                    <small class="text-muted">(Will send PR items list to all selected vendors)</small>
                                </label>
                            </div>
                            
                            <div id="email_extra_options" style="margin-top:10px;padding:10px;border:1px solid #ddd;background:#fafafa;border-radius:4px;">
    <div class="form-group" style="margin-bottom:0;">
        <label for="additional_cc">
            Additional CC Email(s)
            <small style="color:#000;">(Select user email(s))</small>
        </label>

        <select
            name="additional_cc[]"
            id="additional_cc"
            class="form-control select2"
            multiple="multiple"
            data-placeholder="Select users to CC..."
            style="width:100%; color:#000;">
            <?php if (!empty($user_emails)): ?>
                <?php foreach ($user_emails as $u): ?>
                    <option value="<?= htmlspecialchars($u['user_email']) ?>" style="color:#000;">
                        <?= htmlspecialchars($u['username']) ?>
                        (<?= htmlspecialchars($u['user_email']) ?>)
                    </option>
                <?php endforeach; ?>
            <?php endif; ?>
        </select>
    </div>
</div>

<script>
$('#additional_cc').select2({
    width: '100%'
});

$(document).on('select2:open', function () {
    $('.select2-results__option').css('color', '#000');
});

$(document).on('select2:select select2:unselect', function () {
    $('.select2-selection__choice').css('color', '#000');
});
</script>

                            <div class="alert alert-info" style="padding: 8px; font-size: 12px; margin-top: 10px;">
                                <i class="fa fa-info-circle"></i> Emails will be sent to vendor's registered email addresses with RFQ details and submission link.
                            </div>
                        </div>
                    </div>

                    <div class="box-footer" style="padding: 10px;">
                        <button type="submit" class="btn btn-success btn-sm">
                            <i class="fa fa-save"></i> Save RFQ
                        </button>
                        <a href="javascript:history.back()" class="btn btn-default btn-sm">
                            <i class="fa fa-arrow-left"></i> Cancel
                        </a>
                    </div>

                </form>

            </div>

        </section>
    </div>

    <script>
        function filterSuppliers() {
            var search = document.getElementById('supplierSearch').value.toLowerCase();
            var items = document.querySelectorAll('.supplier-item');
            var found = false;

            items.forEach(function(item) {
                var text = item.textContent.toLowerCase();
                if (text.includes(search) || search === '') {
                    item.style.display = 'block';
                    found = true;
                } else {
                    item.style.display = 'none';
                }
            });

            document.getElementById('noResults').style.display = found ? 'none' : 'block';
            updateCount();
        }

        function clearSearch() {
            document.getElementById('supplierSearch').value = '';
            filterSuppliers();
        }

        function selectAllVisible() {
            var visible = document.querySelectorAll('.supplier-item[style="display: block"] .supplier-checkbox');
            visible.forEach(function(cb) {
                cb.checked = true;
            });
            updateCount();
        }

        function deselectAll() {
            var all = document.querySelectorAll('.supplier-checkbox');
            all.forEach(function(cb) {
                cb.checked = false;
            });
            updateCount();
        }

        function updateCount() {
            var checkboxes = document.querySelectorAll('.supplier-checkbox');
            var checkedCount = 0;
            checkboxes.forEach(function(cb) {
                var supplierId = cb.value;
                var emailBox = document.getElementById('emails_for_' + supplierId);
                if (cb.checked) {
                    checkedCount++;
                    if (emailBox) {
                        emailBox.style.display = 'block';
                    }
                } else {
                    if (emailBox) {
                        emailBox.style.display = 'none';
                    }
                }
            });
            var total = checkboxes.length;
            document.getElementById('selectedCount').textContent = 'Selected: ' + checkedCount + '/' + total;
            updateEmailPreviewSummary();
        }

        function addNewEmailRFQ(supplierId) {
            var input = document.getElementById('new_email_input_' + supplierId);
            var email = input.value.trim();
            if (!email) {
                alert('Please enter an email address.');
                return;
            }
            
            // Simple email validation regex
            var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                alert('Please enter a valid email address.');
                return;
            }
            
            // Check if this email is already listed under this supplier
            var existingCheckboxes = document.querySelectorAll('#emails_for_' + supplierId + ' input[type="checkbox"]');
            var exists = false;
            existingCheckboxes.forEach(function(cb) {
                if (cb.value.toLowerCase() === email.toLowerCase()) {
                    exists = true;
                }
            });
            
            if (exists) {
                alert('This email is already in the list.');
                return;
            }
            
            // Create new email item row
            var listContainer = document.getElementById('emails_list_' + supplierId);
            
            // Remove the empty message if it exists
            var noEmailMsg = document.getElementById('no_email_msg_' + supplierId);
            if (noEmailMsg) {
                noEmailMsg.remove();
            }

            var div = document.createElement('div');
            div.style.marginBottom = '4px';
            div.style.fontSize = '11px';
            div.style.display = 'flex';
            div.style.alignItems = 'center';
            div.style.flexWrap = 'wrap';
            div.style.gap = '5px';
            
            var escapedEmail = email.replace(/"/g, '&quot;');
            
            div.innerHTML = 
                '<label style="font-weight: normal; margin-bottom: 0; display: inline-flex; align-items: center; gap: 4px; cursor: pointer;">' +
                    '<input type="checkbox" name="supplier_emails[' + supplierId + '][]" value="' + escapedEmail + '" checked> ' +
                    escapedEmail +
                '</label>' +
                '<input type="hidden" name="new_supplier_emails[' + supplierId + '][]" value="' + escapedEmail + '">' +
                '<select name="supplier_email_type[' + supplierId + '][' + escapedEmail + ']" style="font-size: 10px; padding: 1px 3px; height: 18px; line-height: 12px; border: 1px solid #ccc; border-radius: 3px; margin-left: auto;">' +
                    '<option value="main">Main (To)</option>' +
                    '<option value="cc" selected>CC</option>' +
                '</select>';
                
            listContainer.appendChild(div);
            input.value = '';
            updateEmailPreviewSummary();
        }

        function toggleEmailOptions() {
            var chk = document.getElementById('send_email_chk');
            var opts = document.getElementById('email_extra_options');
            if (opts) {
                opts.style.display = chk.checked ? 'block' : 'none';
            }
        }

        function updateEmailPreviewSummary() {
            var body = document.getElementById('previewSummaryBody');
            var container = document.getElementById('emailPreviewSummary');
            if (!body || !container) return;

            var selectedSuppliers = document.querySelectorAll('.supplier-checkbox:checked');
            body.innerHTML = '';

            if (selectedSuppliers.length === 0) {
                container.style.display = 'none';
                return;
            }

            var hasVisibleRows = false;

            selectedSuppliers.forEach(function(cb) {
                var supplierId = cb.value;
                var label = cb.closest('label');
                var companyName = label.querySelector('strong').textContent.trim();

                // Get checked emails for this supplier
                var emailCbs = document.querySelectorAll('#emails_list_' + supplierId + ' input[type="checkbox"]:checked');
                var toEmails = [];
                var ccEmails = [];

                emailCbs.forEach(function(emailCb) {
                    var emailVal = emailCb.value;
                    var selectEl = document.querySelector('select[name="supplier_email_type[' + supplierId + '][' + emailVal + ']"]');
                    var emailType = selectEl ? selectEl.value : 'main';

                    if (emailType === 'cc') {
                        ccEmails.push(emailVal);
                    } else {
                        toEmails.push(emailVal);
                    }
                });

                var toHtml = toEmails.length > 0 ? toEmails.join(', ') : '<span class="text-danger"><i>No main email selected</i></span>';
                var ccHtml = ccEmails.length > 0 ? ccEmails.join(', ') : '<span class="text-muted">None</span>';

                var tr = document.createElement('tr');
                tr.innerHTML = '<td><strong>' + companyName + '</strong></td>' +
                               '<td>' + toHtml + '</td>' +
                               '<td>' + ccHtml + '</td>';
                body.appendChild(tr);
                hasVisibleRows = true;
            });

            // Update Additional CC Preview
            var addCcInput = document.getElementById('additional_cc');
            var addCcVal = '';
            if (addCcInput) {
                if (addCcInput.tagName.toLowerCase() === 'select') {
                    var selectedOpts = Array.from(addCcInput.selectedOptions).map(opt => opt.value);
                    addCcVal = selectedOpts.join(', ');
                } else {
                    addCcVal = addCcInput.value.trim();
                }
            }
            var addCcContainer = document.getElementById('previewAdditionalCcContainer');
            var addCcList = document.getElementById('previewAdditionalCcList');

            if (addCcVal) {
                if (addCcList) addCcList.textContent = addCcVal;
                if (addCcContainer) addCcContainer.style.display = 'block';
            } else {
                if (addCcContainer) addCcContainer.style.display = 'none';
            }

            container.style.display = hasVisibleRows ? 'block' : 'none';
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            updateCount();
            toggleEmailOptions();
            
            // Listen for changes to email checkboxes and select types dynamically
            var container = document.getElementById('suppliersContainer');
            if (container) {
                container.addEventListener('change', function(e) {
                    if (e.target.name && (e.target.name.indexOf('supplier_emails') > -1 || e.target.name.indexOf('supplier_email_type') > -1)) {
                        updateEmailPreviewSummary();
                    }
                });
            }

            // Initialize select2 and bind change event
            var $addCcInput = $('#additional_cc');
            if ($addCcInput.length > 0 && typeof $.fn.select2 !== 'undefined') {
                $addCcInput.select2({
                    placeholder: "Select users to CC...",
                    allowClear: true
                }).on('change', function() {
                    updateEmailPreviewSummary();
                });
            } else {
                var addCcInput = document.getElementById('additional_cc');
                if (addCcInput) {
                    addCcInput.addEventListener('change', updateEmailPreviewSummary);
                }
            }

            // Set today's date as default
            var today = new Date().toISOString().split('T')[0];
            document.getElementById('rfq_date').value = today;
        });
    </script>

    <style>
        .supplier-item {
            transition: all 0.3s;
        }

        #suppliersContainer::-webkit-scrollbar {
            width: 5px;
        }

        #suppliersContainer::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        #suppliersContainer::-webkit-scrollbar-thumb {
            background: #888;
        }
    </style>

    <?php $this->load->view('admin/footer'); ?>
</body>