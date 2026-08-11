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
    .revision-table td {
        vertical-align: middle !important;
    }
    
    /* Button center styling */
    .button-group-center {
        text-align: center;
        margin: 20px 0 10px;
        padding-top: 15px;
        border-top: 1px solid #e5e5e5;
    }
    .button-group-center .btn {
        margin: 0 8px;
        min-width: 100px;
        padding: 6px 20px;
    }
    
    /* Form styling improvements */
    .form-section {
        background: #fafafa;
        padding: 15px;
        border-radius: 4px;
        margin-bottom: 20px;
    }
    .card-footer {
        background: transparent;
        border-top: none;
        padding: 0;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .button-group-center .btn {
            width: calc(50% - 20px);
            margin: 5px;
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
                    <i class="fa fa-edit"></i> Edit Drawing
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/'; ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="<?php echo base_url() . 'DrawingController/index/'; ?>"><i class="fa fa-list"></i> Drawings</a></li>
                    <li class="active">Edit Drawing</li>
                </ol>
            </section>

            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        
                        <!-- Edit Drawing Form -->
                        <div class="box box-info">
                            <div class="box-header with-border">
                                <h3 class="box-title">
                                    <i class="fa fa-pencil-square-o"></i> Edit Drawing Details
                                </h3>
                                <div class="box-tools pull-right">
                                    <button type="button" class="btn btn-box-tool" data-widget="collapse">
                                        <i class="fa fa-minus"></i>
                                    </button>
                                </div>
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
                                
                                <form class="form-horizontal" method="post" action="<?php echo base_url(); ?>DrawingController/update_drawing" id="editDrawingForm">
                                    <input type="hidden" name="drawing_id" value="<?php echo isset($drawing) ? $drawing->drawing_id : ''; ?>">
                                    
                                    <div class="form-section">
                                        <div class="form-group row required">
                                            <label for="project_id_fk" class="col-sm-3 control-label">
                                                <?php echo $_has_project_master ? 'SO / Project' : 'SO Number'; ?>
                                            </label>
                                            <div class="col-sm-6">
                                                <select class="form-control input-sm" name="project_id_fk" id="project_id_fk" required>
                                                    <option value=""><?php echo $_has_project_master ? '-- Select SO / Project --' : '-- Select SO Number --'; ?></option>
                                                    <?php if (!empty($projects)) { ?>
                                                        <?php foreach ($projects as $proj) { ?>
                                                            <option value="<?php echo $proj->project_id; ?>" 
                                                                <?php echo (isset($drawing) && $drawing->project_id_fk == $proj->project_id) ? 'selected' : ''; ?>>
                                                                <?php
                                                                $option_display = !empty($proj->so_numbers) ? $proj->so_numbers : (!empty($proj->project_code) ? $proj->project_code : $proj->project_name);
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
                                                    value="<?php echo isset($drawing) ? htmlspecialchars($drawing->drawing_no) : ''; ?>" 
                                                    placeholder="Enter drawing number" required>
                                                <?php echo form_error('drawing_no', '<div class="text-danger">', '</div>'); ?>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row required">
                                            <label for="drawing_name" class="col-sm-3 control-label">Drawing Name</label>
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control input-sm" name="drawing_name" id="drawing_name" 
                                                    value="<?php echo isset($drawing) ? htmlspecialchars($drawing->drawing_name) : ''; ?>" 
                                                    placeholder="Enter drawing name" required>
                                                <?php echo form_error('drawing_name', '<div class="text-danger">', '</div>'); ?>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Centered Buttons -->
                                    <div class="button-group-center">
                                        <button type="submit" class="btn btn-success">
                                            <i class="fa fa-check"></i> Update Drawing
                                        </button>
                                        <button type="reset" class="btn btn-warning" onclick="resetForm()">
                                            <i class="fa fa-undo"></i> Reset
                                        </button>
                                        <a href="<?php echo base_url(); ?>DrawingController/index" class="btn btn-default">
                                            <i class="fa fa-times"></i> Cancel
                                        </a>
                                    </div>
                                </form>
                            </div>
                        </div>
                        
                        <!-- Revisions List -->
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <h3 class="box-title">
                                    <i class="fa fa-history"></i> Drawing Revisions
                                </h3>
                                <div class="box-tools pull-right">
                                    <a href="<?php echo base_url() . 'DrawingController/add_revision/' . (isset($drawing) ? $drawing->drawing_id : ''); ?>" 
                                       class="btn btn-success btn-sm">
                                        <i class="fa fa-plus"></i> Add Revision
                                    </a>
                                    <button type="button" class="btn btn-box-tool" data-widget="collapse">
                                        <i class="fa fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="box-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped table-hover revision-table">
                                        <thead>
                                            <tr>
                                                <th width="8%">Revision</th>
                                                <th width="12%">Revision Date</th>
                                                <th width="30%">Change Description</th>
                                                <th width="10%">File</th>
                                                <th width="12%">Uploaded By</th>
                                                <th width="8%">Status</th>
                                                <th width="20%">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($revisions)) { ?>
                                                <?php foreach ($revisions as $rev) { ?>
                                                    <tr>
                                                        <td class="text-center">
                                                            <span class="label label-primary" style="font-size: 11px; padding: 4px 8px;">
                                                                <?php echo $rev->revision_no; ?>
                                                            </span>
                                                        </td>
                                                        <td><?php echo date('d-m-Y', strtotime($rev->revision_date)); ?></td>
                                                        <td><?php echo htmlspecialchars($rev->change_description); ?></td>
                                                        <td class="text-center">
                                                            <?php if (!empty($rev->file_path)) { ?>
                                                                <a href="<?php echo base_url() . 'DrawingController/download_revision/' . $rev->revision_id; ?>" 
                                                                   class="btn btn-xs btn-info" title="Download File">
                                                                    <i class="fa fa-download"></i> Download
                                                                </a>
                                                            <?php } else { ?>
                                                                <span class="label label-default">No File</span>
                                                            <?php } ?>
                                                        </td>
                                                        <td><?php echo htmlspecialchars($rev->uploaded_by); ?></td>
                                                        <td class="text-center">
                                                            <span class="label <?php echo ($rev->status == 'active') ? 'label-success' : 'label-warning'; ?>">
                                                                <?php echo ucfirst($rev->status); ?>
                                                            </span>
                                                        </td>
                                                        <td class="text-center">
                                                            <div class="btn-group btn-group-xs">
                                                                <a href="<?php echo base_url() . 'DrawingController/view_revision/' . $rev->revision_id; ?>" 
                                                                   class="btn btn-primary" title="View Revision Details">
                                                                    <i class="fa fa-eye"></i> View
                                                                </a>
                                                                <?php if ($rev->status == 'active') { ?>
                                                                    <a href="<?php echo base_url() . 'DrawingController/delete_revision/' . $rev->revision_id; ?>" 
                                                                       class="btn btn-danger" 
                                                                       onclick="return confirm('Are you sure you want to delete this revision? This action cannot be undone!')"
                                                                       title="Delete Revision">
                                                                        <i class="fa fa-trash"></i> Delete
                                                                    </a>
                                                                <?php } ?>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                            <?php } else { ?>
                                                <tr>
                                                    <td colspan="7" class="text-center">
                                                        <div class="alert alert-info" style="margin: 0;">
                                                            <i class="fa fa-info-circle"></i> No revisions found for this drawing. 
                                                            <a href="<?php echo base_url() . 'DrawingController/add_revision/' . (isset($drawing) ? $drawing->drawing_id : ''); ?>" 
                                                               class="alert-link">Click here to add the first revision</a>.
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="box-footer">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <small class="text-muted">
                                            <i class="fa fa-info-circle"></i> 
                                            <strong>Note:</strong> Only the latest active revision is considered current. Adding a new revision will automatically mark previous revisions as obsolete.
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
        // Reset form function
        function resetForm() {
            if (confirm('Are you sure you want to reset all changes? Any unsaved changes will be lost.')) {
                document.getElementById("editDrawingForm").reset();
                // Reset select dropdown to original value
                var originalProjectId = '<?php echo isset($drawing) ? $drawing->project_id_fk : ''; ?>';
                if (originalProjectId) {
                    document.getElementById('project_id_fk').value = originalProjectId;
                }
                // Optionally show a success message
                alert('Form has been reset to original values.');
            }
            return false;
        }
        
        // Add form validation before submit
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('editDrawingForm');
            if (form) {
                form.addEventListener('submit', function(e) {
                    const drawingNo = document.getElementById('drawing_no').value.trim();
                    const drawingName = document.getElementById('drawing_name').value.trim();
                    const projectId = document.getElementById('project_id_fk').value;
                    
                    if (!projectId) {
                        e.preventDefault();
                        alert('Please select a project.');
                        return false;
                    }
                    
                    if (!drawingNo) {
                        e.preventDefault();
                        alert('Please enter drawing number.');
                        return false;
                    }
                    
                    if (!drawingName) {
                        e.preventDefault();
                        alert('Please enter drawing name.');
                        return false;
                    }
                    
                    return true;
                });
            }
        });
        
        // Add tooltips
        $(document).ready(function() {
            $('[title]').tooltip({ placement: 'top', container: 'body' });
            
            // Optional: Add confirmation for reset button
            $('button[type="reset"]').on('click', function(e) {
                if (!confirm('Are you sure you want to reset all changes?')) {
                    e.preventDefault();
                }
            });
        });
    </script>
</body>