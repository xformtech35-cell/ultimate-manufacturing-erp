<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (isset($session_data_head1)) {
    
} else {
    header($this->config->item('header'));
}
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<style>
    /* Base font sizing - full page consistency */
    body, p, h1, h2, h3, h4, h5, h6, span, div, input, select, textarea, button, table, th, td, label, .control-label, .form-control, .btn, .breadcrumb, .box-title, .alert {
        font-size: 12px !important;
    }
    .form-control.input-sm {
        font-size: 11px !important;
        height: 28px;
    }
    .btn-xs {
        font-size: 10px !important;
    }
    .label {
        font-size: 10px !important;
    }
    .box-header .box-title {
        font-size: 14px !important;
        font-weight: bold;
    }
    .content-header h1 {
        font-size: 18px !important;
    }
    
    /* Required field styling */
    .required label {
        font-weight: bold;
    }
    .required label:after {
        color: #e32;
        content: '*';
        display: inline;
    }
    
    /* File row styling */
    .file-row {
        background: #f9f9f9;
        padding: 12px 15px;
        margin-bottom: 12px;
        border-left: 3px solid #3c8dbc;
        border-radius: 3px;
        position: relative;
        transition: all 0.2s;
    }
    .file-row:hover {
        background: #f5f5f5;
        border-left-color: #00a65a;
    }
    .remove-file {
        position: absolute;
        top: 10px;
        right: 15px;
        color: #dd4b39;
        cursor: pointer;
        font-size: 20px;
        font-weight: bold;
        width: 24px;
        height: 24px;
        text-align: center;
        line-height: 22px;
        border-radius: 50%;
        transition: all 0.2s;
        background: white;
    }
    .remove-file:hover {
        color: #fff;
        background: #dd4b39;
        transform: scale(1.1);
    }
    .add-more-files {
        margin: 15px 0 5px;
    }
    
    /* Revision preview card */
    .revision-preview {
        background: linear-gradient(135deg, #f0f7ff 0%, #e9f0f9 100%);
        padding: 15px 20px;
        border-radius: 6px;
        margin-bottom: 20px;
        border-left: 4px solid #3c8dbc;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    .next-revision-badge {
        font-size: 16px;
        font-weight: bold;
    }
    
    /* Form layout improvements */
    .form-group {
        margin-bottom: 20px;
    }
    .control-label {
        padding-top: 6px;
        font-weight: 600;
    }
    .help-block {
        font-size: 11px;
        margin-top: 5px;
        margin-bottom: 0;
        color: #737373;
    }
    
    /* Button styling */
    .btn-submit {
        min-width: 140px;
        padding: 6px 20px;
    }
    .btn-cancel {
        min-width: 100px;
    }
    .form-actions {
        margin-top: 25px;
        padding-top: 15px;
        border-top: 1px solid #e5e5e5;
    }
    
    /* Alert styling */
    .alert {
        border-radius: 3px;
        margin-bottom: 20px;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .col-sm-3, .col-sm-5, .col-sm-7 {
            margin-bottom: 8px;
        }
        .control-label {
            text-align: left !important;
            padding-bottom: 5px;
        }
        .file-row {
            padding-right: 35px;
        }
    }
</style>

<body class="hold-transition skin-blue sidebar-mini">
    <div id="loader" class="center"></div> 
    <div class="wrapper">
        <?php $this->load->view('admin/header_side_bar'); ?>
        
        <div class="content-wrapper">
            <section class="content-header">
                <h1>
                    <i class="fa fa-plus-circle"></i> Add Drawing Revision
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/'; ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="<?php echo base_url() . 'DrawingController/index/'; ?>"><i class="fa fa-list"></i> Drawings</a></li>
                    <li><a href="<?php echo base_url() . 'DrawingController/show_drawing/' . (isset($drawing) ? $drawing->drawing_id : ''); ?>">
                        <?php echo isset($drawing) ? htmlspecialchars($drawing->drawing_no) : ''; ?>
                    </a></li>
                    <li class="active">Add Revision</li>
                </ol>
            </section>

            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-info">
                            <div class="box-header with-border">
                                <h3 class="box-title">
                                    <i class="fa fa-pencil-square-o"></i> Add New Revision
                                </h3>
                                <div class="box-tools pull-right">
                                    <button type="button" class="btn btn-box-tool" data-widget="collapse">
                                        <i class="fa fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="box-body">
                                
                                <!-- Flash Messages -->
                                <?php if ($this->session->flashdata('SUCCESSMSG')) { ?>
                                    <div class="alert alert-success alert-dismissible">
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                        <i class="icon fa fa-check"></i> <?= $this->session->flashdata('SUCCESSMSG') ?>
                                    </div>
                                <?php } ?>
                                
                                <?php if ($this->session->flashdata('INFOMSG')) { ?>
                                    <div class="alert alert-info alert-dismissible">
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                        <i class="icon fa fa-info"></i> <?= $this->session->flashdata('INFOMSG') ?>
                                    </div>
                                <?php } ?>
                                
                                <?php if (validation_errors()) { ?>
                                    <div class="alert alert-danger alert-dismissible">
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                        <i class="icon fa fa-ban"></i> <?= validation_errors() ?>
                                    </div>
                                <?php } ?>
                                
                                <!-- Drawing Information Preview Card -->
                                <div class="revision-preview">
                                    <div class="row">
                                        <div class="col-md-4 col-sm-6">
                                            <strong><i class="fa fa-file-text-o"></i> Drawing:</strong><br>
                                            <span style="font-size: 13px;">
                                                <?php echo isset($drawing) ? '<strong>' . htmlspecialchars($drawing->drawing_no) . '</strong> - ' . htmlspecialchars($drawing->drawing_name) : 'N/A'; ?>
                                            </span>
                                        </div>


                                    </div>
                                </div>
                                
                                <!-- Revision Form -->
                                <form class="form-horizontal" method="post" action="<?php echo base_url(); ?>DrawingController/save_revision" enctype="multipart/form-data" id="revisionForm">
                                    <input type="hidden" name="drawing_id" value="<?php echo isset($drawing) ? $drawing->drawing_id : ''; ?>">
                                    
                                    <!-- Revision Number -->
                                    <div class="form-group row required">
                                        <label for="revision_no" class="col-sm-3 control-label">Revision Number</label>
                                        <div class="col-sm-5">
                                            <input type="text" class="form-control input-sm" name="revision_no" id="revision_no" 
                                                   placeholder="Enter revision number (e.g., 001, 002, 003)" required
                                                   value="<?php echo isset($next_revision) && $next_revision ? $next_revision : '001'; ?>">
                                            <p class="help-block">
                                                <i class="fa fa-info-circle"></i> 
                                                Next sequential revision should be: <strong><?php echo isset($next_revision) && $next_revision ? $next_revision : '001'; ?></strong>
                                            </p>
                                        </div>
                                    </div>
                                    
                                    <!-- Revision Date -->
                                    <div class="form-group row required">
                                        <label for="revision_date" class="col-sm-3 control-label">Revision Date</label>
                                        <div class="col-sm-5">
                                            <input type="text" class="form-control input-sm" name="revision_date" id="revision_date" 
                                                   placeholder="DD-MM-YYYY or any date format" required
                                                   value="<?php echo date('d-m-Y'); ?>">

                                        </div>
                                    </div>
                                    
                                    <!-- Change Description -->
                                    <div class="form-group row">
                                        <label for="change_description" class="col-sm-3 control-label">Change Description</label>
                                        <div class="col-sm-7">
                                            <textarea class="form-control input-sm" name="change_description" id="change_description" rows="3" 
                                                      placeholder="Describe the changes made in this revision (e.g., Updated dimensions, Added notes, Corrected errors)"></textarea>
                                            <p class="help-block">Provide a detailed description of what changed in this revision</p>
                                        </div>
                                    </div>
                                    
                                    <!-- Revision Note -->
                                    <div class="form-group row">
                                        <label for="revision_note" class="col-sm-3 control-label">Revision Note</label>
                                        <div class="col-sm-7">
                                            <textarea class="form-control input-sm" name="revision_note" id="revision_note" rows="2" 
                                                      placeholder="Any additional notes for this revision"></textarea>
                                            <p class="help-block">Additional information about this revision (optional)</p>
                                        </div>
                                    </div>
                                    
                                    <!-- Multiple Files Section -->
                                    <div class="form-group row">
                                        <label class="col-sm-3 control-label">Drawing Files</label>
                                        <div class="col-sm-7">
                                            <div id="files-container">
                                                <div class="file-row" id="file-row-1">
                                                    <span class="remove-file" onclick="removeFileRow(1)" style="display: none;">&times;</span>
                                                    <input type="file" class="form-control input-sm" name="drawing_files[]" style="margin-bottom: 8px;">
                                                    <input type="text" class="form-control input-sm" name="file_description[]" 
                                                           placeholder="File description (optional)" style="margin-top: 5px;">
                                                    <small class="text-muted">
                                                        <i class="fa fa-info-circle"></i> Allowed: PDF, JPG, PNG, DWG, DXF, DOC, XLS (Max 5MB per file)
                                                    </small>
                                                </div>
                                            </div>
                                            
                                            <div class="add-more-files">
                                                <button type="button" class="btn btn-sm btn-info" onclick="addFileRow()">
                                                    <i class="fa fa-plus"></i> Add Another File
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Approved By -->
                                    <div class="form-group row">
                                        <label for="approved_by" class="col-sm-3 control-label">Approved By</label>
                                        <div class="col-sm-5">
                                            <input type="text" class="form-control input-sm" name="approved_by" id="approved_by" 
                                                   placeholder="Name of approving authority">
                                            <p class="help-block">Person who approved this revision</p>
                                        </div>
                                    </div>
                                    
                                    <!-- Form Actions -->
                                    <div class="form-group row form-actions">
                                        <div class="col-sm-offset-3 col-sm-9">
                                            <button type="submit" class="btn btn-success btn-submit">
                                                <i class="fa fa-check"></i> Submit Revision
                                            </button>
                                            <a href="<?php echo base_url() . 'DrawingController/show_drawing/' . (isset($drawing) ? $drawing->drawing_id : ''); ?>" 
                                               class="btn btn-default btn-cancel">
                                                <i class="fa fa-arrow-left"></i> Cancel
                                            </a>
                                        </div>
                                    </div>
                                </form>
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
        let fileCounter = 1;
        
        // Add new file row
        function addFileRow() {
            fileCounter++;
            const newRow = `
                <div class="file-row" id="file-row-${fileCounter}">
                    <span class="remove-file" onclick="removeFileRow(${fileCounter})">&times;</span>
                    <input type="file" class="form-control input-sm" name="drawing_files[]" style="margin-bottom: 8px;">
                    <input type="text" class="form-control input-sm" name="file_description[]" 
                           placeholder="File description (optional)" style="margin-top: 5px;">
                    <small class="text-muted">
                        <i class="fa fa-info-circle"></i> Allowed: PDF, JPG, PNG, DWG, DXF, DOC, XLS (Max 5MB per file)
                    </small>
                </div>
            `;
            document.getElementById('files-container').insertAdjacentHTML('beforeend', newRow);
            
            if (fileCounter > 1) {
                const firstRow = document.getElementById('file-row-1');
                if (firstRow) {
                    const removeBtn = firstRow.querySelector('.remove-file');
                    if (removeBtn) removeBtn.style.display = 'inline-block';
                }
            }
            
            addFileValidation();
        }
        
        // Remove file row
        function removeFileRow(rowId) {
            const row = document.getElementById(`file-row-${rowId}`);
            if (row) {
                row.remove();
                fileCounter--;
            }
            
            if (fileCounter === 1) {
                const firstRow = document.getElementById('file-row-1');
                if (firstRow) {
                    const removeBtn = firstRow.querySelector('.remove-file');
                    if (removeBtn) removeBtn.style.display = 'none';
                }
            }
        }
        
        // Add file validation to all file inputs
        function addFileValidation() {
            document.querySelectorAll('input[type="file"]').forEach(input => {
                input.removeEventListener('change', validateFile);
                input.addEventListener('change', validateFile);
            });
        }
        
        // Validate file size and type
        function validateFile(e) {
            const file = e.target.files[0];
            if (file) {
                const fileSize = file.size / 1024 / 1024;
                const allowedTypes = ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'dwg', 'dxf', 'doc', 'docx', 'xls', 'xlsx'];
                const fileExt = file.name.split('.').pop().toLowerCase();
                
                if (fileSize > 5) {
                    alert('File size must be less than 5MB');
                    e.target.value = '';
                    return false;
                }
                
                if (allowedTypes.indexOf(fileExt) === -1) {
                    alert('Invalid file type. Allowed: ' + allowedTypes.join(', '));
                    e.target.value = '';
                    return false;
                }
            }
        }
        
        // Format revision number to 3 digits
        function formatRevisionNumber() {
            const revisionInput = document.getElementById('revision_no');
            let val = revisionInput.value;
            if (val && !isNaN(val) && val.trim() !== '') {
                val = parseInt(val);
                if (val >= 1 && val <= 999) {
                    revisionInput.value = val.toString().padStart(3, '0');
                }
            }
        }
        
        // Auto-format date on blur (no alerts, just convert silently)
        function formatDate() {
            const dateInput = document.getElementById('revision_date');
            let val = dateInput.value.trim();
            
            if (!val) {
                // If empty, set to today's date
                const today = new Date();
                const day = String(today.getDate()).padStart(2, '0');
                const month = String(today.getMonth() + 1).padStart(2, '0');
                const year = today.getFullYear();
                dateInput.value = `${day}-${month}-${year}`;
                return;
            }
            
            // Try to parse the date
            let dateObj;
            let parsed = false;
            
            // Try different separators
            let separators = ['-', '/', '.'];
            for (let sep of separators) {
                if (val.includes(sep)) {
                    let parts = val.split(sep);
                    if (parts.length === 3) {
                        // Try DD-MM-YYYY
                        if (parts[0].length <= 2 && parts[1].length <= 2 && parts[2].length === 4) {
                            dateObj = new Date(parts[2], parts[1] - 1, parts[0]);
                            if (!isNaN(dateObj.getTime())) {
                                parsed = true;
                                break;
                            }
                        }
                        // Try MM-DD-YYYY
                        if (parts[1].length <= 2 && parts[0].length <= 2 && parts[2].length === 4) {
                            dateObj = new Date(parts[2], parts[0] - 1, parts[1]);
                            if (!isNaN(dateObj.getTime())) {
                                parsed = true;
                                break;
                            }
                        }
                        // Try YYYY-MM-DD
                        if (parts[0].length === 4 && parts[1].length <= 2 && parts[2].length <= 2) {
                            dateObj = new Date(parts[0], parts[1] - 1, parts[2]);
                            if (!isNaN(dateObj.getTime())) {
                                parsed = true;
                                break;
                            }
                        }
                    }
                }
            }
            
            // If still not parsed, try Date object
            if (!parsed) {
                dateObj = new Date(val);
                if (!isNaN(dateObj.getTime())) {
                    parsed = true;
                }
            }
            
            if (parsed && !isNaN(dateObj.getTime())) {
                const day = String(dateObj.getDate()).padStart(2, '0');
                const month = String(dateObj.getMonth() + 1).padStart(2, '0');
                const year = dateObj.getFullYear();
                dateInput.value = `${day}-${month}-${year}`;
            } else {
                // If all parsing fails, set to today's date
                const today = new Date();
                const day = String(today.getDate()).padStart(2, '0');
                const month = String(today.getMonth() + 1).padStart(2, '0');
                const year = today.getFullYear();
                dateInput.value = `${day}-${month}-${year}`;
            }
        }
        
        // Form submission validation
        document.getElementById('revisionForm').addEventListener('submit', function(e) {
            // Format revision number
            formatRevisionNumber();
            
            // Format date (this will convert to DD-MM-YYYY format for display)
            formatDate();
            
            // Check if at least one file is uploaded
            let hasFile = false;
            const fileInputs = document.querySelectorAll('input[type="file"]');
            for (let i = 0; i < fileInputs.length; i++) {
                if (fileInputs[i].files.length > 0) {
                    hasFile = true;
                    break;
                }
            }
            
            if (!hasFile) {
                if (!confirm('No files selected. Do you want to continue without attaching any files?')) {
                    e.preventDefault();
                    return false;
                }
            }
        });
        
        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            addFileValidation();
            
            // Add event listeners for formatting
            const revisionInput = document.getElementById('revision_no');
            if (revisionInput) {
                revisionInput.addEventListener('blur', formatRevisionNumber);
            }
            
            const dateInput = document.getElementById('revision_date');
            if (dateInput) {
                dateInput.addEventListener('blur', formatDate);
            }
            
            // Initialize date picker with DD-MM-YYYY format
            if (typeof $ !== 'undefined' && $.fn.datepicker) {
                $('.alldate').datepicker({
                    format: 'dd-mm-yyyy',
                    autoclose: true,
                    todayHighlight: true,
                    todayBtn: 'linked'
                }).on('changeDate', function(e) {
                    // Format the date to DD-MM-YYYY
                    const date = e.date;
                    const day = String(date.getDate()).padStart(2, '0');
                    const month = String(date.getMonth() + 1).padStart(2, '0');
                    const year = date.getFullYear();
                    $(this).val(`${day}-${month}-${year}`);
                });
            }
        });
    </script>
</body>