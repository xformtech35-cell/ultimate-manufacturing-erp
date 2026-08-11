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
    
    /* Info card styling */
    .info-card {
        background: #fff;
        border: 1px solid #e5e5e5;
        border-radius: 6px;
        padding: 0;
        margin-bottom: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        transition: all 0.2s;
    }
    .info-card:hover {
        box-shadow: 0 2px 8px rgba(0,0,0,0.12);
    }
    .info-card-header {
        background: #f5f5f5;
        padding: 12px 15px;
        border-bottom: 1px solid #e5e5e5;
        border-radius: 6px 6px 0 0;
        font-weight: bold;
        color: #3c8dbc;
    }
    .info-card-header i {
        margin-right: 8px;
    }
    .info-card-body {
        padding: 15px;
    }
    .info-row {
        padding: 10px 0;
        border-bottom: 1px solid #f0f0f0;
        display: flex;
        flex-wrap: wrap;
    }
    .info-row:last-child {
        border-bottom: none;
    }
    .info-label-custom {
        font-weight: 600;
        width: 140px;
        color: #555;
        flex-shrink: 0;
    }
    .info-value {
        flex: 1;
        color: #333;
    }
    
    /* File list styling */
    .file-list {
        margin-top: 5px;
    }
    .file-item {
        background: #fafafa;
        border: 1px solid #e5e5e5;
        padding: 12px 15px;
        margin-bottom: 12px;
        border-radius: 6px;
        transition: all 0.2s;
    }
    .file-item:hover {
        background: #f5f5f5;
        border-left: 3px solid #3c8dbc;
    }
    .file-icon {
        font-size: 24px;
        color: #3c8dbc;
        margin-right: 10px;
    }
    .file-name {
        font-weight: 600;
        font-size: 13px;
    }
    .file-meta {
        font-size: 11px;
        color: #777;
        margin-top: 5px;
    }
    .btn-file-action {
        padding: 4px 10px;
        font-size: 11px;
    }
    
    /* Status badges */
    .badge-status {
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 500;
    }
    .badge-active {
        background: #00a65a;
        color: #fff;
    }
    .badge-obsolete {
        background: #dd4b39;
        color: #fff;
    }
    
    /* Drawing header card */
    .drawing-header-card {
        background: linear-gradient(135deg, #3c8dbc 0%, #2c6b8f 100%);
        color: #fff;
        border-radius: 6px;
        padding: 20px;
        margin-bottom: 25px;
    }
    .drawing-header-card h3 {
        margin: 0 0 8px 0;
        font-size: 18px;
        color: #fff;
    }
    .drawing-header-card .drawing-meta {
        opacity: 0.9;
        font-size: 12px;
    }
    
    /* Revision badge */
    .revision-badge-large {
        background: rgba(255,255,255,0.2);
        padding: 8px 16px;
        border-radius: 30px;
        font-size: 18px;
        font-weight: bold;
        display: inline-block;
    }
    
    /* Action buttons */
    .action-buttons {
        margin-top: 25px;
        padding-top: 20px;
        border-top: 1px solid #e5e5e5;
        text-align: center;
    }
    .action-buttons .btn {
        margin: 0 8px;
        min-width: 120px;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .info-row {
            flex-direction: column;
        }
        .info-label-custom {
            width: auto;
            margin-bottom: 5px;
        }
        .drawing-header-card {
            text-align: center;
        }
        .action-buttons .btn {
            margin: 5px;
            width: calc(100% - 20px);
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
                    <i class="fa fa-history"></i> Revision Details
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/'; ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="<?php echo base_url() . 'DrawingController/index/'; ?>"><i class="fa fa-list"></i> Drawings</a></li>
                    <li><a href="<?php echo base_url() . 'DrawingController/edit_drawing/' . (isset($drawing) ? $drawing->drawing_id : ''); ?>">
                        <?php echo isset($drawing) ? htmlspecialchars($drawing->drawing_no) : ''; ?>
                    </a></li>
                    <li class="active">Revision <?php echo isset($revision) ? $revision->revision_no : ''; ?></li>
                </ol>
            </section>

            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        
                        <!-- Drawing Header Card -->
                        <div class="drawing-header-card">
                            <div class="row">
                                <div class="col-md-8">
                                    <h3>
                                        <i class="fa fa-file-text-o"></i> 
                                        <?php echo isset($drawing) ? htmlspecialchars($drawing->drawing_no) : 'N/A'; ?>
                                    </h3>
                                    <div class="drawing-meta">
                                        <?php echo isset($drawing) ? htmlspecialchars($drawing->drawing_name) : ''; ?>
                                        <?php if (isset($drawing) && !empty($drawing->project_code)) { ?>
                                            <br><i class="fa fa-building-o"></i> <?php echo htmlspecialchars($drawing->project_code); ?>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="col-md-4 text-right">
                                    <div class="revision-badge-large">
                                        <i class="fa fa-tag"></i> Rev. <?php echo isset($revision) ? $revision->revision_no : ''; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Revision Details Card -->
                        <div class="info-card">
                            <div class="info-card-header">
                                <i class="fa fa-info-circle"></i> Revision Information
                            </div>
                            <div class="info-card-body">
                                <div class="info-row">
                                    <div class="info-label-custom"><i class="fa fa-hashtag"></i> Revision Number:</div>
                                    <div class="info-value">
                                        <span class="label label-primary" style="font-size: 13px; padding: 4px 12px;">
                                            <?php echo isset($revision) ? $revision->revision_no : ''; ?>
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="info-row">
                                    <div class="info-label-custom"><i class="fa fa-calendar"></i> Revision Date:</div>
                                    <div class="info-value">
                                        <?php echo isset($revision) && $revision->revision_date ? date('d-m-Y', strtotime($revision->revision_date)) : 'Not specified'; ?>
                                    </div>
                                </div>
                                
                                <div class="info-row">
                                    <div class="info-label-custom"><i class="fa fa-align-left"></i> Change Description:</div>
                                    <div class="info-value">
                                        <?php echo isset($revision) && $revision->change_description ? nl2br(htmlspecialchars($revision->change_description)) : '<span class="text-muted">No description provided</span>'; ?>
                                    </div>
                                </div>
                                
                                <?php if (isset($revision) && !empty($revision->revision_note)) { ?>
                                <div class="info-row">
                                    <div class="info-label-custom"><i class="fa fa-sticky-note-o"></i> Revision Note:</div>
                                    <div class="info-value">
                                        <?php echo nl2br(htmlspecialchars($revision->revision_note)); ?>
                                    </div>
                                </div>
                                <?php } ?>
                                
                                <div class="info-row">
                                    <div class="info-label-custom"><i class="fa fa-user"></i> Uploaded By:</div>
                                    <div class="info-value">
                                        <?php echo isset($revision) && $revision->uploaded_by ? htmlspecialchars($revision->uploaded_by) : '<span class="text-muted">Unknown</span>'; ?>
                                    </div>
                                </div>
                                
                                <div class="info-row">
                                    <div class="info-label-custom"><i class="fa fa-check-circle"></i> Approved By:</div>
                                    <div class="info-value">
                                        <?php echo isset($revision) && $revision->approved_by ? htmlspecialchars($revision->approved_by) : '<span class="text-muted">Not approved</span>'; ?>
                                    </div>
                                </div>
                                
                                <div class="info-row">
                                    <div class="info-label-custom"><i class="fa fa-flag"></i> Status:</div>
                                    <div class="info-value">
                                        <?php if (isset($revision) && $revision->status == 'active') { ?>
                                            <span class="badge-status badge-active">
                                                <i class="fa fa-check-circle"></i> Active
                                            </span>
                                        <?php } else { ?>
                                            <span class="badge-status badge-obsolete">
                                                <i class="fa fa-ban"></i> Obsolete
                                            </span>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Files Section -->
                        <?php if (isset($files) && !empty($files)) { ?>
                        <div class="info-card">
                            <div class="info-card-header">
                                <i class="fa fa-paperclip"></i> Attached Files 
                                <span class="label label-default" style="margin-left: 8px;"><?php echo count($files); ?> file(s)</span>
                            </div>
                            <div class="info-card-body">
                                <div class="file-list">
                                    <?php foreach ($files as $file) { 
                                        // Determine file icon based on extension
                                        $file_ext = pathinfo($file->file_name, PATHINFO_EXTENSION);
                                        $file_icon = 'fa-file-o';
                                        if (in_array($file_ext, ['pdf'])) $file_icon = 'fa-file-pdf-o';
                                        elseif (in_array($file_ext, ['jpg', 'jpeg', 'png', 'gif'])) $file_icon = 'fa-file-image-o';
                                        elseif (in_array($file_ext, ['dwg', 'dxf'])) $file_icon = 'fa-file-code-o';
                                        elseif (in_array($file_ext, ['doc', 'docx'])) $file_icon = 'fa-file-word-o';
                                        elseif (in_array($file_ext, ['xls', 'xlsx'])) $file_icon = 'fa-file-excel-o';
                                    ?>
                                        <div class="file-item">
                                            <div class="row">
                                                <div class="col-md-7">
                                                    <i class="fa <?php echo $file_icon; ?> file-icon"></i>
                                                    <span class="file-name"><?php echo htmlspecialchars($file->file_name); ?></span>
                                                    <?php if (!empty($file->description)) { ?>
                                                        <div class="file-meta">
                                                            <i class="fa fa-comment-o"></i> <?php echo htmlspecialchars($file->description); ?>
                                                        </div>
                                                    <?php } ?>
                                                    <div class="file-meta">
                                                        <i class="fa fa-database"></i> Size: <?php echo round($file->file_size / 1024, 2); ?> KB
                                                        <?php if (!empty($file->uploaded_date)) { ?>
                                                            &nbsp;|&nbsp; <i class="fa fa-clock-o"></i> Uploaded: <?php echo date('d-m-Y H:i', strtotime($file->uploaded_date)); ?>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                                <div class="col-md-5 text-right">
                                                    <div class="btn-group btn-group-sm">
                                                        <?php 
                                                        $is_media = in_array(strtolower($file_ext), ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp', 'pdf']);
                                                        if ($is_media) { 
                                                        ?>
                                                            <a href="javascript:void(0);" 
                                                               class="btn btn-info btn-file-action" 
                                                               onclick="previewMediaFile('<?php echo base_url() . $file->file_path; ?>', '<?php echo htmlspecialchars($file->file_name, ENT_QUOTES); ?>', '<?php echo $file_ext; ?>')" 
                                                               title="View File">
                                                                <i class="fa fa-eye"></i> View File
                                                            </a>
                                                        <?php } ?>
                                                        <a href="<?php echo base_url() . 'DrawingController/download_file/' . $file->file_id; ?>" 
                                                           class="btn btn-primary btn-file-action" title="Download">
                                                            <i class="fa fa-download"></i> Download
                                                        </a>
                                                        <a href="<?php echo base_url() . 'DrawingController/delete_file/' . $file->file_id; ?>" 
                                                           class="btn btn-danger btn-file-action" 
                                                           onclick="return confirm('Are you sure you want to delete this file? This action cannot be undone!')"
                                                           title="Delete File">
                                                            <i class="fa fa-trash"></i> Delete
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                        <?php } else { ?>
                        <div class="info-card">
                            <div class="info-card-header">
                                <i class="fa fa-paperclip"></i> Attached Files
                            </div>
                            <div class="info-card-body text-center">
                                <div class="alert alert-info" style="margin: 0;">
                                    <i class="fa fa-info-circle"></i> No files attached to this revision.
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                        
                        <!-- Action Buttons -->
                        <div class="action-buttons">
                            <a href="<?php echo base_url() . 'DrawingController/edit_drawing/' . (isset($drawing) ? $drawing->drawing_id : ''); ?>" 
                               class="btn btn-default">
                                <i class="fa fa-arrow-left"></i> Back to Drawing
                            </a>
                            <?php if (isset($revision) && $revision->status == 'active') { ?>
                            <a href="<?php echo base_url() . 'DrawingController/add_revision/' . (isset($drawing) ? $drawing->drawing_id : ''); ?>" 
                               class="btn btn-success">
                                <i class="fa fa-plus"></i> Add New Revision
                            </a>
                            <?php } ?>
                            <a href="<?php echo base_url() . 'DrawingController/index/'; ?>" 
                               class="btn btn-primary">
                                <i class="fa fa-list"></i> All Drawings
                            </a>
                        </div>
                        
                    </div>
                </div>
            </section>
        </div>
        
        <?php $this->load->view('admin/footer'); ?>
        <div class="control-sidebar-bg"></div>
    </div>
    
    <!-- File Preview Modal -->
    <div class="modal fade" id="filePreviewModal" tabindex="-1" role="dialog" aria-labelledby="filePreviewModalLabel" style="z-index: 999999;">
        <div class="modal-dialog modal-lg" role="document" style="width: 85%; max-width: 1000px;">
            <div class="modal-content" style="border-radius: 6px; overflow: hidden; box-shadow: 0 5px 25px rgba(0,0,0,0.3);">
                <div class="modal-header" style="background-color: #3c8dbc; color: white; padding: 12px 20px;">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white; opacity: 0.9; font-size: 24px;"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="filePreviewModalLabel" style="font-weight: 600; font-size: 15px;"><i class="fa fa-eye"></i> <span id="filePreviewTitle">File Preview</span></h4>
                </div>
                <div class="modal-body" style="padding: 0; background-color: #1e1e1e; text-align: center; min-height: 450px; display: flex; align-items: center; justify-content: center;">
                    <div id="filePreviewContainer" style="width: 100%; height: 100%; min-height: 450px;">
                    </div>
                </div>
                <div class="modal-footer" style="background-color: #f9f9f9; padding: 10px 20px;">
                    <a id="filePreviewDownloadBtn" href="#" class="btn btn-primary" download><i class="fa fa-download"></i> Download</a>
                    <button type="button" class="btn btn-default" data-dismiss="modal" style="font-weight: 600;">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function previewMediaFile(filePath, fileName, fileExt) {
            $('#filePreviewTitle').text(fileName);
            $('#filePreviewDownloadBtn').attr('href', filePath);
            var container = $('#filePreviewContainer');
            container.empty();

            var ext = fileExt.toLowerCase();
            if (['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp'].indexOf(ext) !== -1) {
                container.html('<img src="' + filePath + '" style="max-width: 100%; max-height: 70vh; object-fit: contain; margin: 20px auto; border-radius: 4px; box-shadow: 0 2px 10px rgba(0,0,0,0.5);" alt="' + fileName + '">');
            } else if (ext === 'pdf') {
                container.html('<iframe src="' + filePath + '" style="width: 100%; height: 75vh; border: none;"></iframe>');
            } else {
                container.html('<div style="padding: 50px; color: #fff;"><h4>No preview available for .' + ext + ' files</h4><p>Click Download below to open the file.</p></div>');
            }

            $('#filePreviewModal').modal('show');
        }

        $(document).ready(function() {
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
</body>