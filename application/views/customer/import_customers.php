<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (isset($session_data_head1)) {
    
} else {
    header($this->config->item('header'));
}
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<body class="hold-transition skin-blue sidebar-mini">
    <div id="loader" class="center"></div> 
    <div class="wrapper">

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    Import Customers
                    <small>Import customers from Excel</small>
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url('Home/index'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="<?php echo base_url('CustomerController'); ?>">Customers</a></li>
                    <li class="active">Import Customers</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <!-- Flash Messages -->
                <div class="row">
                    <div class="col-md-12">
                        <?php if ($this->session->flashdata('SUCCESSMSG')) { ?>
                            <div class="alert alert-success alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                <h4><i class="icon fa fa-check"></i> Success!</h4>
                                <?php echo $this->session->flashdata('SUCCESSMSG'); ?>
                            </div>
                        <?php } ?>
                        
                        <?php if ($this->session->flashdata('INFOMSG')) { ?>
                            <div class="alert alert-info alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                <h4><i class="icon fa fa-info"></i> Info!</h4>
                                <?php echo $this->session->flashdata('INFOMSG'); ?>
                            </div>
                        <?php } ?>
                        
                        <?php if ($this->session->flashdata('IMPORT_ERRORS')) { 
                            $errors = $this->session->flashdata('IMPORT_ERRORS');
                            if(!empty($errors)) { ?>
                                <div class="alert alert-warning alert-dismissible">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                    <h4><i class="icon fa fa-warning"></i> Import Errors!</h4>
                                    <ul style="margin-left: 20px;">
                                        <?php foreach($errors as $error) { ?>
                                            <li><?php echo $error; ?></li>
                                        <?php } ?>
                                    </ul>
                                </div>
                            <?php } 
                        } ?>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <!-- Import Form Box -->
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <h3 class="box-title">Upload Customer Excel File</h3>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">
                                <form method="post" action="<?php echo base_url('CustomerController/process_customer_import'); ?>" enctype="multipart/form-data">
                                    <div class="form-group">
                                        <label>Download Template</label>
                                        <p>
                                            <a href="<?php echo base_url('CustomerController/download_customer_template'); ?>" class="btn btn-info btn-sm">
                                                <i class="fa fa-download"></i> Download Import Template
                                            </a>
                                        </p>
                                        <p class="help-block">Download the template to see the correct format for importing customers.</p>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="customer_file">Select Excel File <span class="text-danger">*</span></label>
                                        <input type="file" name="customer_file" id="customer_file" required accept=".xls,.xlsx,.csv" class="form-control">
                                        <p class="help-block">Allowed file types: .xls, .xlsx, .csv (Max 5MB)</p>
                                    </div>
                                    
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fa fa-upload"></i> Import Customers
                                        </button>
                                        <a href="<?php echo base_url('CustomerController'); ?>" class="btn btn-default">
                                            <i class="fa fa-arrow-left"></i> Back to Customers
                                        </a>
                                    </div>
                                </form>
                            </div>
                            <!-- /.box-body -->
                        </div>
                        <!-- /.box -->
                    </div>
                    <!-- /.col -->
                    
                    <div class="col-md-6">
                        <!-- Instructions Box -->
                        <div class="box box-info">
                            <div class="box-header with-border">
                                <h3 class="box-title">Import Instructions</h3>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">
                                <h4>File Format Requirements</h4>
                                <p>The Excel file must have the following columns in this exact order:</p>
                                
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Column</th>
                                            <th>Field Name</th>
                                            <th>Required</th>
                                            <th>Description</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>A</td>
                                            <td><strong>Company Name*</strong></td>
                                            <td><span class="label label-danger">Required</span></td>
                                            <td>Customer company name</td>
                                        </tr>
                                        <tr>
                                            <td>B</td>
                                            <td>Contact Person</td>
                                            <td><span class="label label-default">Optional</span></td>
                                            <td>Contact person name</td>
                                        </tr>
                                        <tr>
                                            <td>C</td>
                                            <td>PAN No</td>
                                            <td><span class="label label-default">Optional</span></td>
                                            <td>10 characters, will be auto-uppercased</td>
                                        </tr>
                                        <tr>
                                            <td>D</td>
                                            <td>GST No</td>
                                            <td><span class="label label-default">Optional</span></td>
                                            <td>15 characters, will be auto-uppercased</td>
                                        </tr>
                                        <tr>
                                            <td>E</td>
                                            <td>Email</td>
                                            <td><span class="label label-default">Optional</span></td>
                                            <td>Valid email format</td>
                                        </tr>
                                        <tr>
                                            <td>F</td>
                                            <td><strong>Mobile*</strong></td>
                                            <td><span class="label label-danger">Required</span></td>
                                            <td>10 digits mobile number</td>
                                        </tr>
                                        <tr>
                                            <td>G</td>
                                            <td>State Code</td>
                                            <td><span class="label label-default">Optional</span></td>
                                            <td>Numeric state code (e.g., 27 for Maharashtra)</td>
                                        </tr>
                                        <tr>
                                            <td>H</td>
                                            <td>Address</td>
                                            <td><span class="label label-default">Optional</span></td>
                                            <td>Full address</td>
                                        </tr>
                                    </tbody>
                                </table>
                                
                                <h4 style="margin-top: 20px;">File Structure</h4>
                                <ul>
                                    <li><strong>Row 1:</strong> Column headers (will be skipped during import)</li>
                                    <li><strong>Row 2:</strong> Instructions (will be skipped during import)</li>
                                    <li><strong>Row 3:</strong> Sample data (for reference only)</li>
                                    <li><strong>Row 4 onwards:</strong> Your actual customer data</li>
                                </ul>
                                
                                <h4>Important Notes</h4>
                                <div class="alert alert-warning">
                                    <ul style="margin-left: 20px;">
                                        <li>Customer Code will be auto-generated (starts from 3000)</li>
                                        <li>Duplicate company names will be skipped</li>
                                        <li>All imported customers will be associated with your account</li>
                                        <li>Review the import errors report after upload for any issues</li>
                                        <li>Maximum file size: 5MB</li>
                                    </ul>
                                </div>
                                
                                <h4>Sample Data Format</h4>
                                <pre style="background-color: #f5f5f5; padding: 10px;">
Company Name,Contact Person,PAN No,GST No,Email,Mobile,State Code,Address
ABC Corporation,John Doe,ABCDE1234F,27ABCDE1234F1Z5,customer@example.com,9876543210,27,123 Street, City, State</pre>
                            </div>
                            <!-- /.box-body -->
                            <div class="box-footer">
                                <a href="<?php echo base_url('CustomerController/download_customer_template'); ?>" class="btn btn-success btn-sm">
                                    <i class="fa fa-download"></i> Download Template
                                </a>
                                <a href="<?php echo base_url('CustomerController'); ?>" class="btn btn-default btn-sm pull-right">
                                    <i class="fa fa-arrow-left"></i> Back to Customer List
                                </a>
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
        
        <?php $this->load->view('admin/footer'); ?>
        <div class="control-sidebar-bg"></div>
    </div>
    <!-- ./wrapper -->
</body>