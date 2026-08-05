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
                    Import BOM
                    <small>Import Bill of Materials from Excel</small>
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url('Home/index'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="<?php echo base_url('BomController/index'); ?>">BOM</a></li>
                    <li class="active">Import BOM</li>
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
                        
                        <?php if ($import_errors = $this->session->flashdata('IMPORT_ERRORS')) { 
                            if(!empty($import_errors)) { ?>
                                <div class="alert alert-warning alert-dismissible">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                    <h4><i class="icon fa fa-warning"></i> Import Errors!</h4>
                                    <ul style="margin-left: 20px;">
                                        <?php foreach($import_errors as $error) { ?>
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
                                <h3 class="box-title">Upload BOM Excel File</h3>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">
                                <form method="post" action="<?php echo base_url('BomController/process_bom_import'); ?>" enctype="multipart/form-data">
                                    <div class="form-group">
                                        <label>Download Template</label>
                                        <p>
                                            <a href="<?php echo base_url('BomController/download_bom_template'); ?>" class="btn btn-info btn-sm">
                                                <i class="fa fa-download"></i> Download Import Template
                                            </a>
                                        </p>
                                        <p class="help-block">Download the template to see the correct format for importing BOMs.</p>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="bom_file">Select Excel File <span class="text-danger">*</span></label>
                                        <input type="file" name="bom_file" id="bom_file" required accept=".xls,.xlsx,.csv" class="form-control">
                                        <p class="help-block">Allowed file types: .xls, .xlsx, .csv (Max 5MB)</p>
                                    </div>
                                    
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fa fa-upload"></i> Import BOMs
                                        </button>
                                        <a href="<?php echo base_url('BomController/index'); ?>" class="btn btn-default">
                                            <i class="fa fa-arrow-left"></i> Back to BOM List
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
                                <h3 class="box-title">BOM Import Instructions</h3>
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
                                            <td><strong>BOM Number*</strong></td>
                                            <td><span class="label label-danger">Required</span></td>
                                            <td>Unique BOM identifier (e.g., BOM/0001/JUL/23-24)</td>
                                        </tr>
                                        <tr>
                                            <td>B</td>
                                            <td><strong>Date*</strong></td>
                                            <td><span class="label label-danger">Required</span></td>
                                            <td>Format: YYYY-MM-DD (e.g., 2023-07-06)</td>
                                        </tr>
                                        <tr>
                                            <td>C</td>
                                            <td><strong>Customer Name*</strong></td>
                                            <td><span class="label label-danger">Required</span></td>
                                            <td>Must exist in the system</td>
                                        </tr>
                                        <tr>
                                            <td>D</td>
                                            <td>Project Code</td>
                                            <td><span class="label label-default">Optional</span></td>
                                            <td>Project reference code</td>
                                        </tr>
                                        <tr>
                                            <td>E</td>
                                            <td>PO Number</td>
                                            <td><span class="label label-default">Optional</span></td>
                                            <td>Purchase Order number</td>
                                        </tr>
                                        <tr>
                                            <td>F</td>
                                            <td>Customer Code</td>
                                            <td><span class="label label-default">Optional</span></td>
                                            <td>Customer-specific code</td>
                                        </tr>
                                        <tr>
                                            <td>G</td>
                                            <td>Status</td>
                                            <td><span class="label label-default">Optional</span></td>
                                            <td>1=Draft, 2=Sent, 3=Viewed, 4=Approved, 5=Rejected, 6=Canceled</td>
                                        </tr>
                                        <tr>
                                            <td>H</td>
                                            <td>Enquiry</td>
                                            <td><span class="label label-default">Optional</span></td>
                                            <td>Enquiry reference</td>
                                        </tr>
                                        <tr>
                                            <td>I</td>
                                            <td><strong>Item No*</strong></td>
                                            <td><span class="label label-danger">Required</span></td>
                                            <td>Item number/sequence</td>
                                        </tr>
                                        <tr>
                                            <td>J</td>
                                            <td><strong>Item Name*</strong></td>
                                            <td><span class="label label-danger">Required</span></td>
                                            <td>Description of the item</td>
                                        </tr>
                                        <tr>
                                            <td>K</td>
                                            <td><strong>Drawing No*</strong></td>
                                            <td><span class="label label-danger">Required</span></td>
                                            <td>Drawing number reference</td>
                                        </tr>
                                        <tr>
                                            <td>L</td>
                                            <td><strong>Quantity*</strong></td>
                                            <td><span class="label label-danger">Required</span></td>
                                            <td>Numeric quantity value</td>
                                        </tr>
                                        <tr>
                                            <td>M</td>
                                            <td>Size</td>
                                            <td><span class="label label-default">Optional</span></td>
                                            <td>Item dimensions/size</td>
                                        </tr>
                                        <tr>
                                            <td>N</td>
                                            <td>Unit</td>
                                            <td><span class="label label-default">Optional</span></td>
                                            <td>Unit of measurement (e.g., PCS, NOS, KG)</td>
                                        </tr>
                                        <tr>
                                            <td>O</td>
                                            <td>MOC</td>
                                            <td><span class="label label-default">Optional</span></td>
                                            <td>Material of Construction</td>
                                        </tr>
                                        <tr>
                                            <td>P</td>
                                            <td>Remark</td>
                                            <td><span class="label label-default">Optional</span></td>
                                            <td>Additional notes</td>
                                        </tr>
                                    </tbody>
                                </table>
                                
                                <h4 style="margin-top: 20px;">File Structure</h4>
                                <ul>
                                    <li><strong>Row 1:</strong> Column headers (will be skipped during import)</li>
                                    <li><strong>Row 2:</strong> Instructions (will be skipped during import)</li>
                                    <li><strong>Row 3:</strong> Sample data (for reference only)</li>
                                    <li><strong>Row 4 onwards:</strong> Your actual BOM data</li>
                                </ul>
                                
                                <h4>Important Notes</h4>
                                <div class="alert alert-warning">
                                    <ul style="margin-left: 20px;">
                                        <li>Customer Name must already exist in the system - it will not be created automatically</li>
                                        <li>If a BOM Number already exists, new items will be added to that BOM</li>
                                        <li>All imported BOMs will be associated with your account</li>
                                        <li>Review the import errors report after upload for any issues</li>
                                        <li>Maximum file size: 5MB</li>
                                    </ul>
                                </div>
                                
                                <h4>Sample Data Format</h4>
                                <pre style="background-color: #f5f5f5; padding: 10px; font-size: 11px; overflow-x: auto;">
BOM Number,Date,Customer Name,Project Code,PO Number,Customer Code,Status,Enquiry,Item No,Item Name,Drawing No,Quantity,Size,Unit,MOC,Remark
BOM/0001/JUL/23-24,2023-07-06,ABC Company,PROJ-001,PO-12345,CUST-001,1,ENQ-001,1,Sample Item,DW-001,10,100x200,PCS,SS304,Sample remark
BOM/0001/JUL/23-24,2023-07-06,ABC Company,PROJ-001,PO-12345,CUST-001,1,ENQ-001,2,Another Item,DW-002,5,50x100,NOS,MS,
BOM/0002/JUL/23-24,2023-07-06,XYZ Corporation,PROJ-002,PO-67890,CUST-002,2,ENQ-002,1,Test Item,DW-003,20,200x300,PCS,AL,</pre>
                            </div>
                            <!-- /.box-body -->
                            <div class="box-footer">
                                <a href="<?php echo base_url('BomController/download_bom_template'); ?>" class="btn btn-success btn-sm">
                                    <i class="fa fa-download"></i> Download Template
                                </a>
                                <a href="<?php echo base_url('BomController/index'); ?>" class="btn btn-default btn-sm pull-right">
                                    <i class="fa fa-arrow-left"></i> Back to BOM List
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
    
    <script>
    $(document).ready(function() {
        // File input validation
        $('#bom_file').on('change', function() {
            var file = this.files[0];
            if(file) {
                var fileSize = file.size / 1024 / 1024; // in MB
                var fileExt = file.name.split('.').pop().toLowerCase();
                
                // Check file size (max 5MB)
                if(fileSize > 5) {
                    alert('File size exceeds 5MB. Please select a smaller file.');
                    $(this).val('');
                    return false;
                }
                
                // Check file extension
                if($.inArray(fileExt, ['xls', 'xlsx', 'csv']) == -1) {
                    alert('Invalid file type. Please select .xls, .xlsx, or .csv file.');
                    $(this).val('');
                    return false;
                }
            }
        });
    });
    </script>
</body>