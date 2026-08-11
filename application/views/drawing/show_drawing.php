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
    .status-active {
        color: #00a65a;
        font-weight: bold;
    }
    .status-obsolete {
        color: #dd4b39;
        font-weight: bold;
    }
    .status-superseded {
        color: #f39c12;
        font-weight: bold;
    }
    
    /* Revision card styles */
    .revision-card {
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 4px;
        margin-bottom: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    }
    .revision-card:hover {
        box-shadow: 0 2px 5px rgba(0,0,0,0.15);
    }
    .revision-header {
        background: #f5f5f5;
        padding: 12px 15px;
        border-bottom: 1px solid #ddd;
        border-radius: 4px 4px 0 0;
        cursor: pointer;
    }
    .revision-header h4 {
        margin: 0;
        font-size: 14px;
        font-weight: bold;
    }
    .revision-header .revision-badge {
        font-size: 16px;
        font-weight: bold;
        color: #3c8dbc;
    }
    .revision-body {
        padding: 15px;
        display: none;
    }
    .revision-body.show {
        display: block;
    }
    .detail-row {
        padding: 8px 0;
        border-bottom: 1px solid #eee;
    }
    .detail-label {
        font-weight: bold;
        width: 180px;
        display: inline-block;
        color: #555;
    }
    .file-list {
        margin-top: 15px;
    }
    .file-item {
        background: #f9f9f9;
        border: 1px solid #e0e0e0;
        padding: 10px;
        margin-bottom: 10px;
        border-radius: 4px;
        transition: all 0.2s;
    }
    .file-item:hover {
        background: #f0f0f0;
    }
    .file-icon {
        font-size: 24px;
        color: #3c8dbc;
        margin-right: 10px;
    }
    .box-header {
        position: relative !important;
        padding: 12px 15px !important;
    }
    .box-header .box-title {
        font-size: 14px !important;
        font-weight: bold;
        display: inline-block;
    }
    .box-header .box-tools {
        position: absolute !important;
        right: 15px !important;
        top: 8px !important;
    }
    .box-header .box-tools .label {
        font-size: 11px !important;
        padding: 5px 10px !important;
        border-radius: 4px;
        display: inline-block;
    }
    .timeline {
        position: relative;
        padding: 10px 10px 10px 35px;
        margin: 0 0 0 15px;
    }
    .timeline:before {
        content: '';
        position: absolute;
        left: 15px;
        top: 10px;
        bottom: 10px;
        width: 2px;
        background: #cbd5e1;
    }
    .timeline-item {
        position: relative;
        margin-bottom: 25px;
    }
    .timeline-badge {
        position: absolute;
        left: -33px;
        top: 8px;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: #3c8dbc;
        color: white;
        text-align: center;
        line-height: 32px;
        z-index: 2;
        font-weight: bold;
        font-size: 11px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .timeline-badge.active {
        background: #00a65a;
    }
    .timeline-badge.superseded {
        background: #f39c12;
    }
    .timeline-content {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        padding: 0;
        margin-left: 0;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        overflow: hidden;
    }
    .btn-group-sm .btn {
        margin: 2px;
    }
    .drawing-info-bar {
        background: #f9f9f9;
        padding: 15px;
        border-left: 4px solid #3c8dbc;
        margin-bottom: 20px;
        border-radius: 4px;
    }
    .drawing-info-bar .info-item {
        display: inline-block;
        margin-right: 30px;
        margin-bottom: 8px;
    }
    .drawing-info-bar .info-label {
        font-weight: bold;
        color: #555;
    }
    .drawing-info-bar .info-value {
        color: #333;
        margin-left: 5px;
    }
    .action-buttons {
        margin-bottom: 20px;
    }
    .revision-number {
        font-size: 16px;
        font-weight: bold;
        margin-right: 10px;
    }
    .revision-date {
        color: #777;
        font-size: 11px;
    }
    .file-preview {
        max-width: 200px;
        max-height: 150px;
        margin-top: 5px;
        border: 1px solid #ddd;
        border-radius: 3px;
    }
    @media (max-width: 768px) {
        .drawing-info-bar .info-item {
            display: block;
            margin-bottom: 10px;
        }
        .timeline {
            padding-left: 20px;
        }
        .timeline-badge {
            width: 32px;
            height: 32px;
            line-height: 32px;
            font-size: 10px;
            left: -24px;
        }
        .detail-label {
            width: 100%;
            display: block;
            margin-bottom: 5px;
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
                    <i class="fa fa-pencil-square-o"></i> Drawing Details
                    <small>View all revisions</small>
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/'; ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="<?php echo base_url() . 'DrawingController/index/'; ?>"><i class="fa fa-list"></i> Drawings</a></li>
                    <li class="active">Drawing Details</li>
                </ol>
            </section>

            <section class="content">
                <div class="row">
                    <div class="col-md-12">
                        
                        <?php if ($this->session->flashdata('SUCCESSMSG')) { ?>
                            <div class="alert alert-success alert-dismissible sty">
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
                        
                        <!-- Drawing Information Bar -->
                        <div class="drawing-info-bar">
                            <div class="info-item">
                                <span class="info-label"><i class="fa fa-hashtag"></i> Drawing Number:</span>
                                <span class="info-value"><strong><?php echo isset($drawing) ? htmlspecialchars($drawing->drawing_no) : ''; ?></strong></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label"><i class="fa fa-file-text-o"></i> Drawing Name:</span>
                                <span class="info-value"><?php echo isset($drawing) ? htmlspecialchars($drawing->drawing_name) : ''; ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label"><i class="fa fa-building-o"></i> <?php echo $_has_project_master ? 'SO / Project:' : 'SO Number:'; ?></span>
                                <span class="info-value">
                                    <?php 
                                    if (isset($drawing)) {
                                        if ($_has_project_master) {
                                            $proj_display = $drawing->project_code . ' - ' . $drawing->project_name;
                                            if (!empty($drawing->so_numbers)) {
                                                $proj_display .= ' (SO: ' . $drawing->so_numbers . ')';
                                            }
                                        } else {
                                            $proj_display = !empty($drawing->so_numbers) ? $drawing->so_numbers : (!empty($drawing->project_code) ? $drawing->project_code : $drawing->project_name);
                                        }
                                        echo htmlspecialchars($proj_display);
                                    }
                                    ?>
                                </span>
                            </div>
                            <div class="info-item">
                                <span class="info-label"><i class="fa fa-tag"></i> Current Revision:</span>
                                <span class="label label-primary" style="font-size: 12px;">
                                    <?php echo isset($drawing) ? ($drawing->current_revision ?: '001') : '001'; ?>
                                </span>
                            </div>
                            <div class="info-item">
                                <span class="info-label"><i class="fa fa-calendar"></i> Created:</span>
                                <span class="info-value"><?php echo isset($drawing) ? date('d-m-Y', strtotime($drawing->created_at)) : ''; ?></span>
                            </div>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="action-buttons">
                            <div class="btn-group">
                                <a href="<?php echo base_url() . 'DrawingController/add_revision/' . (isset($drawing) ? $drawing->drawing_id : ''); ?>" 
                                   class="btn btn-success btn-sm" style="margin-left: 10px;">
                                    <i class="fa fa-plus"></i> Add New Revision
                                </a>
                                <a href="<?php echo base_url() . 'DrawingController/edit_drawing/' . (isset($drawing) ? $drawing->drawing_id : ''); ?>" 
                                   class="btn btn-primary btn-sm" style="margin-left: 10px;">
                                    <i class="fa fa-edit"></i> Edit Drawing
                                </a>
                                <a href="<?php echo base_url() . 'DrawingController/index'; ?>" 
                                   class="btn btn-default btn-sm" style="margin-left :10px;">
                                    <i class="fa fa-arrow-left"></i> Back to List
                                </a>
                            </div>
                        </div>
                        
                        <!-- Revisions Timeline -->
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <h3 class="box-title">
                                    <i class="fa fa-history"></i> Revision History
                                </h3>
                                <div class="box-tools pull-right">
                                    <span class="label label-info">
                                        <i class="fa fa-code-fork"></i> Total Revisions: <?php echo count($revisions); ?>
                                    </span>

                                </div>
                            </div>
                            <div class="box-body">
                                
                                <?php if (!empty($revisions)) { ?>
                                    
                                    <div class="timeline">
                                        <?php 
                                        $revision_count = count($revisions);
                                        $index = 0;
                                        foreach ($revisions as $rev) { 
                                            $index++;
                                            $is_current = ($rev->revision_no == $drawing->current_revision);
                                            $badge_class = ($rev->status == 'active') ? 'active' : 'superseded';
                                            $timeline_class = ($rev->status == 'active') ? 'active' : 'superseded';
                                        ?>
                                            <div class="timeline-item">
                                                <div class="timeline-badge <?php echo $timeline_class; ?>">
                                                    <?php echo $rev->revision_no; ?>
                                                </div>
                                                <div class="timeline-content">
                                                    <div class="revision-header" onclick="toggleRevision(<?php echo $rev->revision_id; ?>)">
                                                        <div class="row">
                                                            <div class="col-md-8">
                                                                <strong class="revision-number">
                                                                    <i class="fa fa-tag"></i> Revision <?php echo $rev->revision_no; ?>
                                                                </strong>

                                                                <?php if ($rev->status == 'active') { ?>
                                                                    <span class="label label-success">
                                                                        <i class="fa fa-check-circle"></i> ACTIVE
                                                                    </span>
                                                                <?php } else { ?>
                                                                    <span class="label label-warning">
                                                                        <i class="fa fa-arrow-up"></i> SUPERSEDED
                                                                    </span>
                                                                <?php } ?>
                                                                <span class="revision-date" style="margin: 0px 15px;">
                                                                    <i class="fa fa-calendar"></i> 
                                                                    <?php echo date('d-m-Y', strtotime($rev->revision_date)); ?>
                                                                </span>
                                                            </div>
                                                            <div class="col-md-4 text-right">
                                                                <small>
                                                                    <i class="fa fa-clock-o"></i> 
                                                                    <?php echo date('H:i:s', strtotime($rev->created_at)); ?>
                                                                </small>
                                                                <i class="fa fa-chevron-down pull-right" style="margin-left: 10px;"></i>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="revision-body" id="revision-<?php echo $rev->revision_id; ?>" style="display: none;">
                                                        
                                                        <div class="detail-row">
                                                            <span class="detail-label"><i class="fa fa-tag"></i> Revision Number:</span>
                                                            <span class="label label-primary"><?php echo $rev->revision_no; ?></span>
                                                        </div>
                                                        
                                                        <div class="detail-row">
                                                            <span class="detail-label"><i class="fa fa-calendar"></i> Revision Date:</span>
                                                            <?php echo date('d-m-Y', strtotime($rev->revision_date)); ?>
                                                        </div>
                                                        
                                                        <?php if (!empty($rev->change_description)) { ?>
                                                        <div class="detail-row">
                                                            <span class="detail-label"><i class="fa fa-pencil-square-o"></i> Change Description:</span>
                                                            <div style="display: inline-block; vertical-align: top;">
                                                                <?php echo nl2br(htmlspecialchars($rev->change_description)); ?>
                                                            </div>
                                                        </div>
                                                        <?php } ?>
                                                        
                                                        <?php if (!empty($rev->revision_note)) { ?>
                                                        <div class="detail-row">
                                                            <span class="detail-label"><i class="fa fa-sticky-note-o"></i> Revision Note:</span>
                                                            <div style="display: inline-block; vertical-align: top;">
                                                                <?php echo nl2br(htmlspecialchars($rev->revision_note)); ?>
                                                            </div>
                                                        </div>
                                                        <?php } ?>
                                                        

                                                        
                                                        <div class="detail-row">
                                                            <span class="detail-label"><i class="fa fa-clock-o"></i> Created At:</span>
                                                            <?php echo date('H:i:s', strtotime($rev->created_at)); ?>
                                                        </div>
                                                        
                                                        <!-- Files Section -->
                                                        <div class="detail-row">
                                                            <span class="detail-label"><i class="fa fa-paperclip"></i> Attached Files:</span>
                                                            <?php if (!empty($rev->files)) { ?>
                                                                <div class="file-list">
                                                                    <?php foreach ($rev->files as $file) { 
                                                                        $file_ext = strtolower($file->file_type);
                                                                        $is_image = in_array($file_ext, ['jpg', 'jpeg', 'png', 'gif']);
                                                                        $is_pdf = ($file_ext == 'pdf');
                                                                    ?>
                                                                        <div class="file-item">
                                                                            <div class="row">
                                                                                <div class="col-md-7">
                                                                                    <i class="fa fa-file-<?php 
                                                                                        if ($is_pdf) echo 'pdf';
                                                                                        elseif ($is_image) echo 'image';
                                                                                        elseif (in_array($file_ext, ['doc','docx'])) echo 'word';
                                                                                        elseif (in_array($file_ext, ['xls','xlsx'])) echo 'excel';
                                                                                        elseif (in_array($file_ext, ['dwg','dxf'])) echo 'o';
                                                                                        else echo 'o';
                                                                                    ?> fa-lg file-icon"></i>
                                                                                    <strong><?php echo htmlspecialchars($file->file_name); ?></strong>
                                                                                    <br>
                                                                                    <small class="text-muted">
                                                                                        <i class="fa fa-database"></i> Size: <?php echo round($file->file_size / 1024, 2); ?> KB
                                                                                        <i class="fa fa-file-o"></i> Type: <?php echo strtoupper($file->file_type); ?>
                                                                                        <?php if (!empty($file->description)) { ?>
                                                                                            <br><i class="fa fa-comment"></i> <?php echo htmlspecialchars($file->description); ?>
                                                                                        <?php } ?>
                                                                                    </small>
                                                                                    <?php if ($is_image) { ?>
                                                                                        <br>
                                                                                        <img src="<?php echo base_url() . $file->file_path; ?>" 
                                                                                             class="file-preview" 
                                                                                             alt="Preview"
                                                                                             onerror="this.style.display='none'">
                                                                                    <?php } ?>
                                                                                </div>
                                                                                <div class="col-md-5 text-right">
                                                                                    <div class="btn-group btn-group-xs">
                                                                                        <?php if ($is_image || $is_pdf) { ?>
                                                                                            <a href="javascript:void(0);" 
                                                                                               class="btn btn-info" 
                                                                                               onclick="previewMediaFile('<?php echo base_url() . $file->file_path; ?>', '<?php echo htmlspecialchars($file->file_name, ENT_QUOTES); ?>', '<?php echo $file_ext; ?>')" 
                                                                                               title="View File">
                                                                                                <i class="fa fa-eye"></i> View File
                                                                                            </a>
                                                                                        <?php } ?>
                                                                                        <a href="<?php echo base_url() . 'DrawingController/download_file/' . $file->file_id; ?>" 
                                                                                           class="btn btn-primary" title="Download File">
                                                                                            <i class="fa fa-download"></i> Download
                                                                                        </a>
                                                                                        <a href="<?php echo base_url() . 'DrawingController/delete_file/' . $file->file_id; ?>" 
                                                                                           class="btn btn-danger" 
                                                                                           onclick="return confirm('Are you sure you want to delete this file?')"
                                                                                           title="Delete File">
                                                                                            <i class="fa fa-trash"></i> Delete
                                                                                        </a>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    <?php } ?>
                                                                </div>
                                                            <?php } else { ?>
                                                                <span class="text-muted">
                                                                    <i class="fa fa-ban"></i> No files attached
                                                                </span>
                                                            <?php } ?>
                                                        </div>
                                                        
                                                        <div class="text-right" style="margin-top: 15px;">
                                                            <a href="<?php echo base_url() . 'DrawingController/view_revision/' . $rev->revision_id; ?>" 
                                                               class="btn btn-sm btn-info">
                                                                <i class="fa fa-eye"></i> View Full Details
                                                            </a>
                                                            <?php if ($rev->status == 'active') { ?>
                                                                <a href="<?php echo base_url() . 'DrawingController/delete_revision/' . $rev->revision_id; ?>" 
                                                                   class="btn btn-sm btn-danger" 
                                                                   onclick="return confirm('Are you sure you want to delete this revision? This action cannot be undone!')">
                                                                    <i class="fa fa-trash"></i> Delete Revision
                                                                </a>
                                                            <?php } ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                    
                                    <?php if ($revision_count > 1) { ?>
                                    <div class="text-center" style="margin-top: 20px;">
                                        <button type="button" class="btn btn-sm btn-default" onclick="expandAll()">
                                            <i class="fa fa-expand"></i> Expand All
                                        </button>
                                        <button type="button" class="btn btn-sm btn-default" onclick="collapseAll()">
                                            <i class="fa fa-compress"></i> Collapse All
                                        </button>
                                    </div>
                                    <?php } ?>
                                    
                                <?php } else { ?>
                                    <div class="alert alert-info text-center">
                                        <i class="fa fa-info-circle fa-2x"></i>
                                        <p style="margin-top: 10px;"><strong>No revisions found for this drawing.</strong></p>
                                        <p>Click the button below to add the first revision.</p>
                                        <br>
                                        <a href="<?php echo base_url() . 'DrawingController/add_revision/' . (isset($drawing) ? $drawing->drawing_id : ''); ?>" 
                                           class="btn btn-success">
                                            <i class="fa fa-plus"></i> Add First Revision (001)
                                        </a>
                                    </div>
                                <?php } ?>
                                
                            </div>
                            <div class="box-footer">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <small class="text-muted">
                                            <i class="fa fa-info-circle"></i> 
                                            <strong>Tip:</strong> Click on any revision header to expand/collapse details. 
                                            Current revision is highlighted in green.
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

        function toggleRevision(revisionId) {
            var element = document.getElementById('revision-' + revisionId);
            var chevron = event.currentTarget.querySelector('.fa-chevron-down, .fa-chevron-up');
            
            if (element.style.display === 'none' || element.style.display === '') {
                element.style.display = 'block';
                if (chevron) {
                    chevron.classList.remove('fa-chevron-down');
                    chevron.classList.add('fa-chevron-up');
                }
            } else {
                element.style.display = 'none';
                if (chevron) {
                    chevron.classList.remove('fa-chevron-up');
                    chevron.classList.add('fa-chevron-down');
                }
            }
        }
        
        function expandAll() {
            var elements = document.querySelectorAll('.revision-body');
            var chevrons = document.querySelectorAll('.fa-chevron-down, .fa-chevron-up');
            
            for (var i = 0; i < elements.length; i++) {
                elements[i].style.display = 'block';
            }
            for (var i = 0; i < chevrons.length; i++) {
                chevrons[i].classList.remove('fa-chevron-down');
                chevrons[i].classList.add('fa-chevron-up');
            }
        }
        
        function collapseAll() {
            var elements = document.querySelectorAll('.revision-body');
            var chevrons = document.querySelectorAll('.fa-chevron-down, .fa-chevron-up');
            
            for (var i = 0; i < elements.length; i++) {
                elements[i].style.display = 'none';
            }
            for (var i = 0; i < chevrons.length; i++) {
                chevrons[i].classList.remove('fa-chevron-up');
                chevrons[i].classList.add('fa-chevron-down');
            }
        }
        
        // Optional: Open the current revision by default
        $(document).ready(function() {
            <?php if (isset($drawing) && !empty($drawing->current_revision) && !empty($revisions)) { 
                foreach ($revisions as $rev) {
                    if ($rev->revision_no == $drawing->current_revision) { ?>
                        // Auto-expand current revision
                        var currentRevision = document.getElementById('revision-<?php echo $rev->revision_id; ?>');
                        if (currentRevision) {
                            currentRevision.style.display = 'block';
                            var chevron = document.querySelector('#revision-<?php echo $rev->revision_id; ?>').parentElement.querySelector('.fa-chevron-down');
                            if (chevron) {
                                chevron.classList.remove('fa-chevron-down');
                                chevron.classList.add('fa-chevron-up');
                            }
                        }
                    <?php }
                }
            } ?>
            
            // Initialize tooltips
            $('[title]').tooltip({ placement: 'top', container: 'body' });
        });
    </script>
</body>