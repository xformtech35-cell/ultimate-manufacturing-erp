<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (isset($session_data_head1)) {
    
} else {
    header($this->config->item('header'));
}
$_has_project_master = isset($session_data_head1['permission']) && in_array('Projects', $session_data_head1['permission']);
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<style>
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
    .required label {
        font-weight: bold;
    }
    .required label:after {
        color: #e32;
        content: '*';
        display: inline;
    }
    .table > thead > tr > th {
        font-size: 11px !important;
        padding: 6px !important;
    }
    .table > tbody > tr > td {
        font-size: 11px !important;
        padding: 6px !important;
        vertical-align: middle;
    }
    .status-active {
        color: #00a65a;
        font-weight: bold;
    }
    .status-obsolete {
        color: #dd4b39;
        font-weight: bold;
    }
    
    /* Clickable row style */
    .clickable-row {
        cursor: pointer;
        transition: background-color 0.2s;
    }
    .clickable-row:hover {
        background-color: #f5f5f5 !important;
    }
    
    /* Button center alignment */
    .button-group {
        text-align: center;
        margin: 20px 0;
    }
    .button-group .btn {
        margin: 0 10px;
        min-width: 100px;
    }
    
    /* Drawing info card */
    .drawing-info {
        background: #f9f9f9;
        padding: 10px;
        margin-bottom: 15px;
        border-left: 3px solid #3c8dbc;
    }
    .drawing-info small {
        display: inline-block;
        margin-right: 20px;
    }
    
    /* Responsive table */
    @media (max-width: 768px) {
        .table-responsive {
            overflow-x: auto;
        }
    }
    
    /* Dropdown action button styles */
    .action-dropdown {
        position: relative;
        display: inline-block;
    }
    
    .action-btn {
        background: #3c8dbc;
        border: none;
        color: white;
        padding: 10px 10px;
        border-radius: 3px;
        cursor: pointer;
        font-size: 11px;
        transition: background 0.2s;
    }
    
    .action-btn:hover {
        background: #2c6b8f;
    }
    
    .action-btn i {
        margin-right: 4px;
    }
    
    .dropdown-menu-custom {
        position: absolute;
        top: 100%;
        right: 0;
        left: auto;
        background: white;
        border: 1px solid #ddd;
        border-radius: 4px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        min-width: 130px;
        z-index: 1000;
        display: none;
        margin-top: 2px;
        padding: 5px 0;
    }
    
    .dropdown-menu-custom.show {
        display: block;
    }
    
    .dropdown-menu-custom a {
        display: block;
        padding: 6px 12px;
        text-decoration: none;
        color: #333;
        font-size: 11px;
        transition: background 0.2s;
    }
    
    .dropdown-menu-custom a:hover {
        background: #f5f5f5;
    }
    
    .dropdown-menu-custom a i {
        margin-right: 8px;
        width: 14px;
        color: #666;
    }
    
    .dropdown-menu-custom a.text-danger i {
        color: #dd4b39;
    }
    
    .dropdown-menu-custom a.text-danger:hover {
        background: #f9e5e5;
    }
    
    /* Action cell styling */
    .action-cell {
        text-align: center;
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
        font-size: 16px;
        font-weight: bold;
        cursor: pointer;
        transition: color 0.15s;
    }
    .remove-file:hover {
        color: #c9302c;
    }
</style>

<body class="hold-transition skin-blue sidebar-mini">
    <div id="loader" class="center"></div> 
    <div class="wrapper">
        <?php $this->load->view('admin/header_side_bar'); ?>
        
        <div class="content-wrapper">
            <section class="content-header">
                <h1>
                    <i class="fa fa-pencil-square-o"></i> Drawings Master
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/'; ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li class="active">Drawings</li>
                </ol>
            </section>

            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        
                        <!-- Add Drawing Form -->
                        <div class="box box-info">
                            <div class="box-header with-border">
                                <h3 class="box-title">
                                    <i class="fa fa-plus-circle"></i> Add New Drawing
                                </h3>

                            </div>
                            <div class="box-body">
                                
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
                                
                                <form class="form-horizontal" method="post" action="<?php echo base_url(); ?>DrawingController/add_drawing" enctype="multipart/form-data" id="drawingForm">
                                    <div class="row">
                                        <div class="col-md-12">
                                            
                                            <div class="form-group row required">
                                                <label for="project_id_fk" class="col-sm-3 control-label">
                                                    <?php echo $_has_project_master ? 'SO / Project' : 'SO Number'; ?>
                                                </label>
                                                <div class="col-sm-6">
                                                    <select class="form-control input-sm select2" name="project_id_fk" id="project_id_fk" required>
                                                        <option value=""><?php echo $_has_project_master ? '-- Select SO / Project --' : '-- Select SO Number --'; ?></option>
                                                        <?php if (!empty($projects)) { ?>
                                                            <?php foreach ($projects as $proj) { ?>
                                                                <option value="<?php echo $proj->project_id; ?>" <?php echo set_select('project_id_fk', $proj->project_id); ?>>
                                                                    <?php 
                                                                    if ($_has_project_master) {
                                                                        $option_display = $proj->project_code . ' - ' . $proj->project_name;
                                                                        if (!empty($proj->so_numbers)) {
                                                                            $option_display .= ' (SO: ' . $proj->so_numbers . ')';
                                                                        }
                                                                    } else {
                                                                        $option_display = !empty($proj->so_numbers) ? $proj->so_numbers : $proj->project_name;
                                                                    }
                                                                    echo htmlspecialchars($option_display);
                                                                    ?>
                                                                </option>
                                                            <?php } ?>
                                                        <?php } ?>
                                                    </select>
                                                    <?php echo form_error('project_id_fk', '<div class="text-danger">', '</div>'); ?>
                                                </div>
                                            </div>
                                            
                                            <div class="form-group row required">
                                                <label for="drawing_no" class="col-sm-3 control-label">Drawing Number</label>
                                                <div class="col-sm-6">
                                                    <input type="text" class="form-control input-sm" name="drawing_no" id="drawing_no" 
                                                           placeholder="Enter drawing number (e.g., DRG-001)" 
                                                           value="<?php echo set_value('drawing_no'); ?>" required>
                                                    <?php echo form_error('drawing_no', '<div class="text-danger">', '</div>'); ?>
                                                </div>
                                            </div>
                                            
                                            <div class="form-group row required">
                                                <label for="drawing_name" class="col-sm-3 control-label">Drawing Name</label>
                                                <div class="col-sm-6">
                                                    <input type="text" class="form-control input-sm" name="drawing_name" id="drawing_name" 
                                                           placeholder="Enter drawing name" 
                                                           value="<?php echo set_value('drawing_name'); ?>" required>
                                                    <?php echo form_error('drawing_name', '<div class="text-danger">', '</div>'); ?>
                                                </div>
                                            </div>
                                                                                        <!-- Multiple Files Section -->
                                             <div class="form-group row">
                                                 <label class="col-sm-3 control-label">Drawing Files (Revision A)</label>
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
                                             
                                         </div>
                                     </div>
                                    
                                    <div class="button-group">
                                        <button type="submit" class="btn btn-success">
                                            <i class="fa fa-check"></i> Submit
                                        </button>
                                        <button type="reset" class="btn btn-danger" onclick="resetForm()">
                                            <i class="fa fa-undo"></i> Reset
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        
                        <!-- Drawings List Table -->
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <h3 class="box-title">
                                    <i class="fa fa-list"></i> Drawings List
                                </h3>
                                <div class="box-tools pull-right">

                                    <button type="button" class="btn btn-box-tool" data-widget="refresh" onclick="refreshTable()">
                                        <i class="fa fa-refresh"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="box-body">
                                <div class="table-responsive">
                                    <table id="drawings_table" class="table table-bordered table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th width="5%">ID</th>
                                                <th width="10%">Drawing Date</th>
                                                <th width="18%">SO</th>
                                                <th width="15%">Drawing No</th>
                                                <th width="20%">Drawing Name</th>
                                                <th width="10%">Drawing Revision</th>
                                                <th width="10%">Status</th>
                                                <th width="15%">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($drawings)) { ?>
                                                <?php foreach ($drawings as $drawing) { ?>
                                                    <tr class="clickable-row" 
                                                        data-drawing-id="<?php echo $drawing->drawing_id; ?>" 
                                                        data-drawing-no="<?php echo htmlspecialchars($drawing->drawing_no); ?>" 
                                                        data-drawing-name="<?php echo htmlspecialchars($drawing->drawing_name); ?>">
                                                        <td><?php echo $drawing->drawing_id; ?></td>
                                                        <td><?php echo isset($drawing->created_at) && $drawing->created_at ? date('d-m-Y', strtotime($drawing->created_at)) : 'N/A'; ?></td>
                                                        <td>
                                                            <?php
                                                            $so_display = !empty($drawing->so_numbers) ? $drawing->so_numbers : (!empty($drawing->project_code) ? $drawing->project_code : (!empty($drawing->project_name) ? $drawing->project_name : 'N/A'));
                                                            echo htmlspecialchars($so_display);
                                                            ?>
                                                        </td>
                                                        <td><strong><?php echo htmlspecialchars($drawing->drawing_no); ?></strong></td>
                                                        <td><?php echo htmlspecialchars($drawing->drawing_name); ?></td>
                                                        <td>
                                                            <span class="label label-primary">
                                                                <i class="fa fa-tag"></i> <?php echo $drawing->current_revision ?: 'A'; ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <?php if ($drawing->status == 'active') { ?>
                                                                <span class="status-active">
                                                                    <i class="fa fa-check-circle"></i> Active
                                                                </span>
                                                            <?php } else { ?>
                                                                <span class="status-obsolete">
                                                                    <i class="fa fa-ban"></i> Obsolete
                                                                </span>
                                                            <?php } ?>
                                                        </td>
                                                        <td class="action-cell">
                                                            <!-- Single Action Button with Dropdown -->
                                                            <div class="action-dropdown">
                                                                <button class="action-btn" data-dropdown-id="dropdown-<?php echo $drawing->drawing_id; ?>">
                                                                    <span class="caret"></span>
                                                                </button>
                                                                <div id="dropdown-<?php echo $drawing->drawing_id; ?>" class="dropdown-menu-custom">
                                                                    <a href="<?php echo base_url() . 'DrawingController/show_drawing/' . $drawing->drawing_id; ?>">
                                                                        <i class="fa fa-eye"></i> View Revisions
                                                                    </a>
                                                                    <a href="<?php echo base_url() . 'DrawingController/edit_drawing/' . $drawing->drawing_id; ?>">
                                                                        <i class="fa fa-edit"></i> Edit Drawing
                                                                    </a>
                                                                    <a href="<?php echo base_url() . 'DrawingController/add_revision/' . $drawing->drawing_id; ?>">
                                                                        <i class="fa fa-plus"></i> Add Revision
                                                                    </a>
                                                                    <a href="<?php echo base_url() . 'DrawingController/delete_drawing/' . $drawing->drawing_id; ?>" 
                                                                       class="text-danger"
                                                                       onclick="return confirmDeleteDrawing(event, this.href)">
                                                                        <i class="fa fa-trash"></i> Delete Drawing
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                            <?php } ?>
                                            <!-- Empty state handled by DataTables emptyTable language option -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="box-footer">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <small class="text-muted">
                                            <i class="fa fa-info-circle"></i> 
                                            <strong>Tip:</strong> Click on any row to view all revisions for that drawing. Click "Actions" button to see available operations.
                                        </small>
                                    </div>
                                </div>
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
        $(document).ready(function() {
            // Initialize DataTable only if not already initialized
            if (!$.fn.DataTable.isDataTable('#drawings_table')) {
                var table = $('#drawings_table').DataTable({
                    "paging": true,
                    "lengthChange": true,
                    "searching": true,
                    "ordering": true,
                    "info": true,
                    "autoWidth": false,
                    "pageLength": 25,
                    "order": [[0, 'desc']],
                    "columnDefs": [
                        { "orderable": false, "searchable": false, "targets": 6 }
                    ],
                    "language": {
                        "emptyTable": "<div class='alert alert-info' style='margin:8px 0;'><i class='fa fa-info-circle'></i> No drawings found. Click \"Add New Drawing\" to create one.</div>",
                        "info": "Showing _START_ to _END_ of _TOTAL_ drawings",
                        "infoEmpty": "Showing 0 to 0 of 0 drawings",
                        "search": "Search Drawings:",
                        "lengthMenu": "Show _MENU_ drawings per page",
                        "zeroRecords": "No matching drawings found"
                    }
                });
            }
            
            // Make entire row clickable to show drawing details
            $('#drawings_table tbody').on('click', 'tr', function(e) {
                // Don't trigger if clicking on action button or dropdown items
                if ($(e.target).closest('.action-dropdown, .action-btn, .dropdown-menu-custom a, .dropdown-menu-custom').length) {
                    return;
                }
                
                var drawingId = $(this).data('drawing-id');
                if (drawingId) {
                    // Redirect to show drawing page
                    window.location.href = '<?php echo base_url(); ?>DrawingController/show_drawing/' + drawingId;
                }
            });
            
            // Add hover effect for clickable rows
            $('#drawings_table tbody tr').css('cursor', 'pointer');
            
            // Initialize select2 for better dropdown
            if (typeof $.fn.select2 !== 'undefined') {
                $('.select2').select2({
                    width: '100%',
                    placeholder: 'Select Project',
                    allowClear: true
                });
            }
            
            // File validation for dynamically added elements
            addFileValidation();
            
            // Dropdown toggle functionality
            $(document).on('click', '.action-btn', function(e) {
                e.stopPropagation();
                var dropdownId = $(this).data('dropdown-id');
                var $dropdown = $('#' + dropdownId);
                
                // Close all other dropdowns
                $('.dropdown-menu-custom').not($dropdown).removeClass('show');
                
                // Toggle current dropdown
                $dropdown.toggleClass('show');
            });
            
            // Close dropdown when clicking outside
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.action-dropdown').length) {
                    $('.dropdown-menu-custom').removeClass('show');
                }
            });
            
            // Prevent row click when clicking on dropdown items
            $(document).on('click', '.dropdown-menu-custom a', function(e) {
                e.stopPropagation();
                // Allow the link to work normally
            });
        });
        
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

        // Function to reset form
        function resetForm() {
            // Remove dynamically added file rows and reset container
            document.getElementById('files-container').innerHTML = `
                <div class="file-row" id="file-row-1">
                    <span class="remove-file" onclick="removeFileRow(1)" style="display: none;">&times;</span>
                    <input type="file" class="form-control input-sm" name="drawing_files[]" style="margin-bottom: 8px;">
                    <input type="text" class="form-control input-sm" name="file_description[]" 
                           placeholder="File description (optional)" style="margin-top: 5px;">
                    <small class="text-muted">
                        <i class="fa fa-info-circle"></i> Allowed: PDF, JPG, PNG, DWG, DXF, DOC, XLS (Max 5MB per file)
                    </small>
                </div>
            `;
            fileCounter = 1;
            
            document.getElementById("drawingForm").reset();
            if (typeof $.fn.select2 !== 'undefined') {
                $('#project_id_fk').val('').trigger('change');
            }
            
            addFileValidation();
        }
        
        // Function to refresh table
        function refreshTable() {
            location.reload();
        }
        
        // Confirm before delete with event prevention
        function confirmDeleteDrawing(event, url) {
            event.preventDefault();
            event.stopPropagation();
            if (confirm('Are you sure you want to delete this drawing and ALL its revisions? This action cannot be undone!')) {
                window.location.href = url;
            }
            return false;
        }
        
        // Add tooltips to action buttons
        $(document).ready(function() {
            $('[title]').tooltip({ placement: 'top', container: 'body' });
        });
    </script>
</body>