<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (isset($session_data_head1)) {
    
} else {
    header($this->config->item('header'));
}
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<style>
    /* Styling variables */
    :root {
        --primary: #2563eb;
        --primary-hover: #1d4ed8;
        --success: #10b981;
        --success-hover: #059669;
        --info: #3b82f6;
        --warning: #f59e0b;
        --danger: #ef4444;
        --dark: #0f172a;
        --slate-50: #f8fafc;
        --slate-100: #f1f5f9;
        --slate-200: #e2e8f0;
        --slate-300: #cbd5e1;
        --slate-700: #334155;
        --border-radius: 6px;
        --transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        --shadow: 0 1px 3px rgba(0, 0, 0, 0.05), 0 1px 2px rgba(0, 0, 0, 0.03);
        --shadow-lg: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
    }

    /* Set font family globally, but color/size only within page content to protect sidebar */
    body {
        font-family: 'Inter', 'Source Sans Pro', sans-serif !important;
    }

    body {
        background-color: #f1f5f9 !important;
    }

    .content-wrapper, 
    .content-wrapper p, 
    .content-wrapper h1, 
    .content-wrapper h2, 
    .content-wrapper h3, 
    .content-wrapper h4, 
    .content-wrapper h5, 
    .content-wrapper h6, 
    .content-wrapper span, 
    .content-wrapper div, 
    .content-wrapper input, 
    .content-wrapper select, 
    .content-wrapper textarea, 
    .content-wrapper button, 
    .content-wrapper table, 
    .content-wrapper th, 
    .content-wrapper td, 
    .content-wrapper label, 
    .content-wrapper .control-label, 
    .content-wrapper .form-control, 
    .content-wrapper .btn, 
    .content-wrapper .breadcrumb, 
    .content-wrapper .box-title, 
    .content-wrapper .alert {
        font-family: 'Inter', 'Source Sans Pro', sans-serif !important;
        font-size: 12px !important;
        color: var(--slate-700);
    }

    .content-wrapper {
        background-color: #f1f5f9 !important;
        padding-bottom: 20px;
    }

    .content-header h1 {
        font-size: 18px !important;
        font-weight: 700 !important;
        color: var(--dark);
        margin: 0;
    }
    
    .breadcrumb {
        background: transparent !important;
        padding: 4px 0 !important;
        margin-bottom: 0 !important;
    }

    .box.box-info {
        border-top: none !important;
        border-radius: var(--border-radius) !important;
        box-shadow: var(--shadow) !important;
        background: #fff;
        margin-bottom: 20px;
        border: 1px solid var(--slate-200);
        overflow: hidden;
    }

    .box-body {
        padding: 16px !important;
    }

    /* Form Layout Grid */
    .form-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px 18px;
    }

    @media (max-width: 992px) {
        .form-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 768px) {
        .form-grid {
            grid-template-columns: 1fr;
            gap: 12px;
        }
    }

    /* Form group styling */
    .form-group-custom {
        display: flex;
        flex-direction: column;
        gap: 4px;
        margin-bottom: 0 !important;
    }

    .form-group-custom.span-2 {
        grid-column: span 2;
    }

    .form-group-custom.span-3 {
        grid-column: span 3;
    }

    @media (max-width: 992px) {
        .form-group-custom.span-3 {
            grid-column: span 2;
        }
    }

    @media (max-width: 768px) {
        .form-group-custom.span-2,
        .form-group-custom.span-3 {
            grid-column: span 1;
        }
    }

    .form-group-custom label {
        font-size: 12px !important;
        font-weight: 600 !important;
        color: var(--slate-700);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .required label:after {
        content: "*";
        color: var(--danger);
        font-weight: 700;
    }

    input, textarea {
        text-transform: none !important;
    }

    /* Input styling */
    .form-control-custom {
        width: 100%;
        height: 32px !important;
        padding: 6px 12px !important;
        font-size: 12px !important;
        font-weight: 400 !important;
        color: var(--dark) !important;
        background-color: #fff !important;
        border: 1px solid var(--slate-300) !important;
        border-radius: var(--border-radius) !important;
        box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.02) !important;
        transition: var(--transition) !important;
    }

    .form-control-custom:focus {
        border-color: var(--primary) !important;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15) !important;
        outline: none !important;
    }

    textarea.form-control-custom {
        height: auto !important;
        min-height: 50px;
        resize: vertical;
        padding: 8px 12px !important;
    }

    /* Select2 integration styling */
    .select2-container--default .select2-selection--single {
        border: 1px solid var(--slate-300) !important;
        border-radius: var(--border-radius) !important;
        height: 32px !important;
        box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.02) !important;
        transition: var(--transition) !important;
    }

    .select2-container--default .select2-selection--single:focus,
    .select2-container--default.select2-container--focus .select2-selection--single {
        border-color: var(--primary) !important;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15) !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 30px !important;
        padding-left: 12px !important;
        font-size: 12px !important;
        color: var(--dark) !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 30px !important;
        right: 8px !important;
    }

    /* File upload design */
    .file-upload-wrapper {
        border: 1px dashed var(--slate-300);
        border-radius: var(--border-radius);
        padding: 5px 12px;
        background-color: var(--slate-50);
        transition: var(--transition);
        display: flex;
        align-items: center;
        gap: 8px;
        position: relative;
        height: 32px;
        overflow: hidden;
    }

    .file-upload-wrapper:hover {
        border-color: var(--primary);
        background-color: #fff;
    }

    .file-upload-wrapper input[type="file"] {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        opacity: 0;
        cursor: pointer;
        z-index: 2;
    }

    .file-upload-icon {
        font-size: 16px;
        color: var(--primary);
    }

    .file-upload-info {
        display: flex;
        flex-direction: row;
        align-items: center;
        gap: 8px;
        width: 100%;
        justify-content: space-between;
    }

    .file-name-label {
        font-size: 12px !important;
        font-weight: 500;
        color: var(--slate-700);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 180px;
    }

    .file-upload-info small {
        font-size: 10px;
        color: #64748b;
        white-space: nowrap;
    }

    /* Buttons */
    .btn-submit-container {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 15px;
        border-top: 1px solid var(--slate-200);
        padding-top: 12px;
    }

    .btn-primary-custom {
        background-color: var(--primary) !important;
        border: 1px solid var(--primary) !important;
        color: white !important;
        font-weight: 600 !important;
        padding: 6px 18px !important;
        border-radius: var(--border-radius) !important;
        transition: var(--transition) !important;
        font-size: 12px !important;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .btn-primary-custom:hover {
        background-color: var(--primary-hover) !important;
        border-color: var(--primary-hover) !important;
    }

    .btn-default-custom {
        background-color: #fff !important;
        border: 1px solid var(--slate-300) !important;
        color: var(--slate-700) !important;
        font-weight: 600 !important;
        padding: 6px 18px !important;
        border-radius: var(--border-radius) !important;
        transition: var(--transition) !important;
        font-size: 12px !important;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .btn-default-custom:hover {
        background-color: var(--slate-50) !important;
        color: var(--dark) !important;
    }

    .projects-list-container {
        padding: 0 16px 16px 16px;
    }

    .projects-list-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
        margin-top: 15px;
        border-bottom: 2px solid var(--slate-200);
        padding-bottom: 8px;
    }

    .projects-list-header h3 {
        font-size: 14px !important;
        font-weight: 700 !important;
        color: var(--dark);
        margin: 0;
    }

    .btn-export-excel {
        background-color: var(--success) !important;
        border: 1px solid var(--success) !important;
        color: white !important;
        font-weight: 600 !important;
        padding: 5px 12px !important;
        border-radius: var(--border-radius) !important;
        transition: var(--transition) !important;
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 11px !important;
    }

    .btn-export-excel:hover {
        background-color: var(--success-hover) !important;
        border-color: var(--success-hover) !important;
    }

    .table-responsive {
        overflow: visible !important; /* Fixed: Allows dropdowns to go outside the table container */
        background: #fff;
        width: 100% !important;
    }

    .table-custom {
        margin-bottom: 0 !important;
        width: 100%;
        border-collapse: collapse;
    }

    .table-custom th {
        background-color: var(--slate-100) !important;
        color: var(--slate-700) !important;
        font-weight: 600 !important;
        text-transform: uppercase !important;
        font-size: 11px !important;
        letter-spacing: 0.5px;
        padding: 5px 12px !important;
        border-bottom: 2px solid var(--slate-200) !important;
        text-align: left;
        vertical-align: middle !important;
        white-space: nowrap !important;
    }

    .table-custom td {
        padding: 5px 12px !important;
        vertical-align: middle !important;
        border-bottom: 1px solid var(--slate-100) !important;
        font-size: 11px !important;
        color: var(--slate-700);
        white-space: nowrap !important;
    }

    .table-custom tbody tr:hover {
        background-color: var(--slate-50) !important;
    }

    .text-center {
        text-align: center !important;
    }

    /* Badges */
    .status-badge {
        padding: 3px 8px;
        border-radius: 9999px;
        font-size: 10px !important;
        font-weight: 600;
        display: inline-block;
        white-space: nowrap !important;
    }

    .status-planning {
        background-color: #f3e8ff;
        color: #6b21a8;
        border: 1px solid #e9d5ff;
    }

    .status-inprogress {
        background-color: #dcfce7;
        color: #15803d;
        border: 1px solid #bbf7d0;
    }

    .status-completed {
        background-color: #dbeafe;
        color: #1d4ed8;
        border: 1px solid #bfdbfe;
    }

    .status-onhold {
        background-color: #fef3c7;
        color: #b45309;
        border: 1px solid #fde68a;
    }

    .status-cancelled {
        background-color: #fee2e2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }

    /* Actions and document download */
    .btn-download {
        background-color: #eff6ff !important;
        border: 1px solid #bfdbfe !important;
        color: #1d4ed8 !important;
        padding: 3px 8px !important;
        border-radius: 4px !important;
        font-size: 10px !important;
        font-weight: 500 !important;
        transition: var(--transition);
        display: inline-block;
        text-decoration: none !important;
    }

    .btn-download:hover {
        background-color: #1d4ed8 !important;
        color: #fff !important;
    }

    .badge-no-doc {
        background-color: var(--slate-100);
        color: #64748b;
        border: 1px solid var(--slate-200);
        padding: 3px 6px;
        border-radius: 4px;
        font-size: 10px !important;
        display: inline-block;
    }

    .dropdown-toggle-custom {
        background-color: #fff !important;
        border: 1px solid var(--slate-300) !important;
        color: var(--slate-700) !important;
        padding: 3px 8px !important;
        border-radius: 4px !important;
        font-weight: 500 !important;
        font-size: 11px !important;
    }

    .dropdown-toggle-custom:hover {
        background-color: var(--slate-50) !important;
        border-color: var(--slate-400) !important;
    }

    .dropdown-menu-custom {
        border-radius: var(--border-radius) !important;
        box-shadow: var(--shadow-lg) !important;
        border: 1px solid var(--slate-200) !important;
        padding: 4px 0 !important;
        min-width: 100px !important;
        right: 0 !important; /* Fixed: Align dropdown popup rightwards to prevent cutting off */
        left: auto !important;
    }

    .dropdown-menu-custom > li > a {
        padding: 6px 12px !important;
        font-size: 11px !important;
        color: var(--slate-700) !important;
        transition: var(--transition);
    }

    .dropdown-menu-custom > li > a:hover {
        background-color: var(--slate-100) !important;
        color: var(--dark) !important;
    }

    /* Alerts */
    .alert-custom {
        border-radius: var(--border-radius) !important;
        border: none !important;
        box-shadow: var(--shadow) !important;
        padding: 10px 14px !important;
        font-size: 12px !important;
        margin-bottom: 15px !important;
    }

    /* DataTable customization */
    .dataTables_wrapper {
        padding: 12px 0 8px 0 !important;
        background: #fff;
    }
    .dataTables_wrapper .row {
        margin-left: 0 !important;
        margin-right: 0 !important;
    }
    .dataTables_wrapper [class*="col-"] {
        padding-left: 0 !important;
        padding-right: 0 !important;
    }
    .dataTables_wrapper .dataTables_length {
        padding-left: 12px !important;
        margin-bottom: 8px !important;
        font-size: 11px !important;
        color: var(--slate-600) !important;
    }
    .dataTables_wrapper .dataTables_filter {
        padding-right: 12px !important;
        margin-bottom: 8px !important;
        font-size: 11px !important;
        color: var(--slate-600) !important;
    }
    .dataTables_wrapper .dataTables_info {
        padding-left: 12px !important;
        margin-top: 8px !important;
        font-size: 11px !important;
        color: var(--slate-600) !important;
    }
    .dataTables_wrapper .dataTables_paginate {
        padding-right: 12px !important;
        margin-top: 8px !important;
        font-size: 11px !important;
        color: var(--slate-600) !important;
    }
    .dataTables_wrapper .dataTables_filter input {
        border: 1px solid var(--slate-300) !important;
        border-radius: var(--border-radius) !important;
        padding: 4px 8px !important;
        margin-left: 6px !important;
        outline: none !important;
        height: 26px;
    }
    .dataTables_wrapper .dataTables_filter input:focus {
        border-color: var(--primary) !important;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1) !important;
    }
    .dataTables_wrapper .dataTables_length select {
        border: 1px solid var(--slate-300) !important;
        border-radius: var(--border-radius) !important;
        padding: 2px 4px !important;
        height: 26px;
        outline: none !important;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        border-radius: var(--border-radius) !important;
        border: 1px solid transparent !important;
        transition: var(--transition) !important;
        padding: 2px 6px !important;
        margin: 0 2px !important;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current, 
    .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
        background: var(--primary) !important;
        color: white !important;
        border-color: var(--primary) !important;
    }
    .table-custom th.sorting, 
    .table-custom th.sorting_asc, 
    .table-custom th.sorting_desc {
        padding-right: 18px !important;
        cursor: pointer;
    }
</style>

<body class="hold-transition skin-blue sidebar-mini">
    <div id="loader" class="center"></div> 
    <div class="wrapper">
        <!-- Sidebar -->
        <?php $this->load->view('admin/header_side_bar'); ?>
        
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    Add Project
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li class="active">Add Project</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-info">
                            <!-- /.box-header - REMOVED heading per request -->
                            <div class="box-body">

                                <?php if ($this->session->flashdata('SUCCESSMSG')) { ?>
                                    <div role="alert" class="alert alert-success alert-custom">
                                        <button data-dismiss="alert" class="close" type="button"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                        <strong>Success!</strong> <?= $this->session->flashdata('SUCCESSMSG') ?>
                                    </div>
                                <?php } ?>

                                <?php if ($this->session->flashdata('INFOMSG')) { ?>
                                    <div role="alert" class="alert alert-info alert-custom">
                                        <button data-dismiss="alert" class="close" type="button"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                        <strong>Oops!!</strong> <?= $this->session->flashdata('INFOMSG') ?>
                                    </div>
                                <?php } ?>

                                <?php if (validation_errors()) { ?>
                                    <div role="alert" class="alert alert-danger alert-custom">
                                        <button data-dismiss="alert" class="close" type="button"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                        <strong>Validation Errors:</strong> <?= validation_errors() ?>
                                    </div>
                                <?php } ?>

                                <form class="form-horizontal form_overlay" method="post" action="<?php echo base_url(); ?>ProjectController/add_project" enctype="multipart/form-data">
                                    <div class="form-grid">
                                        
                                        <div class="form-group-custom required">
                                            <label for="project_code">Project Code</label>
                                            <input type="text" class="form-control-custom" name="project_code" id="project_code" placeholder="Enter project code" value="<?php echo set_value('project_code'); ?>" required="">
                                            <?php echo form_error('project_code', '<div class="text-danger" style="font-size:11px;">', '</div>'); ?>
                                        </div>
                                        
                                        <div class="form-group-custom required">
                                             <label for="project_name">Project Name</label>
                                             <input type="text" class="form-control-custom" name="project_name" id="project_name" placeholder="Enter project name" value="<?php echo set_value('project_name'); ?>" required="">
                                             <?php echo form_error('project_name', '<div class="text-danger" style="font-size:11px;">', '</div>'); ?>
                                         </div>
                                         
                                         <div class="form-group-custom required">
                                             <label for="system">System</label>
                                             <input type="text" class="form-control-custom" name="system" id="system" placeholder="Enter system name" value="<?php echo set_value('system'); ?>" required="">
                                             <?php echo form_error('system', '<div class="text-danger" style="font-size:11px;">', '</div>'); ?>
                                         </div>
                                        
                                        <div class="form-group-custom">
                                            <label for="opportunity_name">Opportunity Name</label>
                                            <input type="text" class="form-control-custom" name="opportunity_name" id="opportunity_name" placeholder="Enter opportunity name" value="<?php echo set_value('opportunity_name'); ?>">
                                        </div>
                                        
                                        <div class="form-group-custom required">
                                            <label for="organisation_name">Organization Name</label>
                                            <select name="organisation_name" id="organisation_name" class="form-control-custom select2" style="width:100%;" required="">
                                                <option value="">Select company</option>
                                                <?php if (!empty($customers)) {
                                                    foreach ($customers as $customer) {
                                                        $customer_name = isset($customer->company_name) ? $customer->company_name : (isset($customer['company_name']) ? $customer['company_name'] : '');
                                                        if ($customer_name == '') continue;
                                                        $selected = set_select('organisation_name', $customer_name);
                                                        echo "<option value='" . htmlspecialchars($customer_name, ENT_QUOTES) . "' $selected>" . htmlspecialchars($customer_name, ENT_QUOTES) . "</option>";
                                                    }
                                                } ?>
                                            </select>
                                            <?php echo form_error('organisation_name', '<div class="text-danger" style="font-size:11px;">', '</div>'); ?>
                                        </div>
                                        
                                        <div class="form-group-custom required">
                                            <label for="project_status">Project Status</label>
                                            <select name="project_status" class="form-control-custom" id="project_status" required="">
                                                <option value="">Select Status</option>
                                                <option value="Planning" <?php echo set_select('project_status', 'Planning'); ?>>Planning</option>
                                                <option value="In Progress" <?php echo set_select('project_status', 'In Progress'); ?>>In Progress</option>
                                                <option value="Completed" <?php echo set_select('project_status', 'Completed'); ?>>Completed</option>
                                                <option value="On Hold" <?php echo set_select('project_status', 'On Hold'); ?>>On Hold</option>
                                                <option value="Cancelled" <?php echo set_select('project_status', 'Cancelled'); ?>>Cancelled</option>
                                            </select>
                                            <?php echo form_error('project_status', '<div class="text-danger" style="font-size:11px;">', '</div>'); ?>
                                        </div>
                                        
                                        <div class="form-group-custom required">
                                            <label for="project_start_date">Start Date</label>
                                            <input type="text" class="form-control-custom alldate created-date" name="project_start_date" id="project_start_date" required="" onkeydown="return false;" placeholder="Click to select date" value="<?php echo set_value('project_start_date'); ?>">
                                            <?php echo form_error('project_start_date', '<div class="text-danger" style="font-size:11px;">', '</div>'); ?>
                                        </div>
                                        
                                        <div class="form-group-custom">
                                            <label for="project_completed_date">Completed Date</label>
                                            <input type="text" class="form-control-custom alldate" name="project_completed_date" id="project_completed_date" onkeydown="return false;" placeholder="Click to select date" value="<?php echo set_value('project_completed_date'); ?>">
                                        </div>
                                        
                                        <div class="form-group-custom">
                                            <label for="forecast_completed_date">Forecast Completed Date</label>
                                            <input type="text" class="form-control-custom alldate" name="forecast_completed_date" id="forecast_completed_date" onkeydown="return false;" placeholder="Click to select date" value="<?php echo set_value('forecast_completed_date'); ?>">
                                        </div>
                                        
                                        <div class="form-group-custom">
                                            <label for="upload_project_doc">Project Document</label>
                                            <div class="file-upload-wrapper">
                                                <i class="fa fa-cloud-upload file-upload-icon"></i>
                                                <div class="file-upload-info">
                                                    <span class="file-name-label">Choose file...</span>
                                                    <small>Max 2MB</small>
                                                </div>
                                                <input type="file" name="upload_project_doc" id="upload_project_doc">
                                            </div>
                                        </div>
                                        
                                        <div class="form-group-custom span-3">
                                            <label for="project_description">Project Description</label>
                                            <textarea class="form-control-custom" name="project_description" id="project_description" rows="2" placeholder="Enter project description"><?php echo set_value('project_description'); ?></textarea>
                                        </div>
                                        
                                    </div>
                                    
                                    <div class="btn-submit-container">
                                        <button type="submit" class="btn-primary-custom">Submit</button>
                                    </div>
                                </form>

                            </div>
                            
                            <div class="projects-list-container">
                                <div class="projects-list-header">
                                    <h3>Projects List</h3>
                                    <a href="<?php echo base_url(); ?>ProjectController/export_projects" class="btn-export-excel">
                                        <i class="fa fa-file-excel-o"></i> Export to Excel
                                    </a>
                                </div>
                                
                                <!-- /.box-body -->
                                <div class="table-responsive">
                                    <table id="example3" class="table-custom">
                                        <thead>
                                            <tr>
                                                <th class="text-center" style="width: 45px;">Sr.No.</th>
                                                <th class="text-center" style="width: 85px;">Project Code</th>
                                                 <th style="width: 18%;">Project Name</th>
                                                 <th style="width: 15%;">System</th>
                                                 <th style="width: 18%;">Opportunity Name</th>
                                                <th class="text-center" style="width: 90px;">Status</th>
                                                <th class="text-center" style="width: 85px;">Start Date</th>
                                                <th style="width: 20%;">Organization</th>
                                                <th class="text-center" style="width: 80px;">Document</th>
                                                <th class="text-center" style="width: 75px;">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $i = 1;
                                            if (!empty($projects)) {
                                                foreach ($projects as $project) {
                                                    $project_id = isset($project->project_id) ? $project->project_id : '';
                                                     $project_code = isset($project->project_code) ? $project->project_code : '';
                                                     $project_name = isset($project->project_name) ? $project->project_name : '';
                                                     $system = isset($project->system) ? $project->system : '';
                                                     $opportunity_name = isset($project->opportunity_name) ? $project->opportunity_name : '';
                                                    $project_status = isset($project->project_status) ? $project->project_status : '';
                                                    $project_start_date = isset($project->project_start_date) ? $project->project_start_date : '';
                                                    $organisation_name = isset($project->organisation_name) ? $project->organisation_name : '';
                                                    $upload_project_doc = isset($project->upload_project_doc) ? $project->upload_project_doc : '';
                                                    ?>
                                                    <tr>
                                                        <td class="text-center"><?php echo $i; ?></td>
                                                        <td class="text-center"><strong><?php echo $project_code; ?></strong></td>
                                                         <td style="max-width: 150px; overflow: hidden; text-overflow: ellipsis;" title="<?php echo htmlspecialchars($project_name, ENT_QUOTES); ?>"><?php echo $project_name; ?></td>
                                                         <td style="max-width: 150px; overflow: hidden; text-overflow: ellipsis;" title="<?php echo htmlspecialchars($system, ENT_QUOTES); ?>"><?php echo !empty($system) ? $system : 'N/A'; ?></td>
                                                         <td style="max-width: 150px; overflow: hidden; text-overflow: ellipsis;" title="<?php echo htmlspecialchars($opportunity_name, ENT_QUOTES); ?>"><?php echo $opportunity_name; ?></td>
                                                        <td class="text-center">
                                                            <?php 
                                                            $status_class = 'status-badge status-planning';
                                                            if (!empty($project_status)) {
                                                                switch($project_status) {
                                                                    case 'Planning': $status_class = 'status-badge status-planning'; break;
                                                                    case 'In Progress': $status_class = 'status-badge status-inprogress'; break;
                                                                    case 'Completed': $status_class = 'status-badge status-completed'; break;
                                                                    case 'On Hold': $status_class = 'status-badge status-onhold'; break;
                                                                    case 'Cancelled': $status_class = 'status-badge status-cancelled'; break;
                                                                }
                                                            }
                                                            ?>
                                                            <span class="<?php echo $status_class; ?>"><?php echo !empty($project_status) ? $project_status : 'N/A'; ?></span>
                                                        </td>
                                                        <td class="text-center">
                                                            <?php 
                                                            if (!empty($project_start_date) && $project_start_date != '0000-00-00' && $project_start_date != '1970-01-01') {
                                                                // Format date as dd-mm-yyyy
                                                                echo date('d-m-Y', strtotime($project_start_date));
                                                            } else {
                                                                echo 'N/A';
                                                            }
                                                            ?>
                                                        </td>
                                                        <td style="max-width: 160px; overflow: hidden; text-overflow: ellipsis;" title="<?php echo htmlspecialchars($organisation_name, ENT_QUOTES); ?>"><?php echo !empty($organisation_name) ? $organisation_name : 'N/A'; ?></td>
                                                        <td class="text-center">
                                                            <?php if (!empty($upload_project_doc) && $upload_project_doc != './uploads/' && $upload_project_doc != '0x2e2f75706c6f6164732f') { ?>
                                                                <a href="<?php echo base_url() . 'ProjectController/download_document/' . $project_id; ?>" class="btn-download">Download</a>
                                                            <?php } else { ?>
                                                                <span class="badge-no-doc">No Doc</span>
                                                            <?php } ?>
                                                        </td>
                                                        <td class="text-center">
                                                            <?php if (!empty($project_id)) { ?>
                                                            <div class="dropdown">
                                                                <button class="dropdown-toggle-custom dropdown-toggle" type="button" data-toggle="dropdown">Action
                                                                    <span class="caret"></span>
                                                                </button>
                                                                <ul class="dropdown-menu dropdown-menu-custom dropdown-menu-right">
                                                                    <li><a href="<?php echo base_url() . 'ProjectController/edit_project/' . $project_id; ?>"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</a></li>
                                                                    <li><a href="<?php echo base_url() . 'ProjectController/delete_project/' . $project_id; ?>" 
                                                                        role="button" onClick="return confirm('Are you sure you want to delete?')"><i class="fa fa-trash" aria-hidden="true"></i> Delete</a></li>
                                                                </ul>
                                                            </div>
                                                            <?php } else { ?>
                                                                <span class="label label-danger">Invalid</span>
                                                            <?php } ?>
                                                        </td>
                                                    </tr>
                                                    <?php
                                                    $i++;
                                                }
                                            } else {
                                                ?>
                                                <tr>
                                                    <td colspan="9" class="text-center">No projects found</td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        </div>
                        <!-- /.box -->
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->
            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->
        
        <script>
            $(document).ready(function() {
                // Initialize DataTable with 20 entries per page (destroying any previous instance from global scripts)
                if ($.fn.DataTable) {
                    $('#example3').DataTable({
                        "destroy": true,
                        "pageLength": 25,
                        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
                        "language": {
                            "search": "Search Projects:"
                        }
                    });
                }

                $('#upload_project_doc').on('change', function() {
                    var filename = $(this).val().split('\\').pop();
                    if (filename) {
                        $('.file-name-label').text(filename);
                    } else {
                        $('.file-name-label').text('Choose file...');
                    }
                });
            });
        </script>
        <?php $this->load->view('admin/footer'); ?>
        <div class="control-sidebar-bg"></div>
    </div>
    <!-- ./wrapper -->
</body>
</html>