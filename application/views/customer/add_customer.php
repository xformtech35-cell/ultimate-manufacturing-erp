<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (isset($session_data_head1)) {
    
} else {
    header($this->config->item('header'));
}
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<style>
    .required label {
        font-weight: bold;
    }
    .required label:after {
        color: #e32;
        content: '*';
        display:inline;
    }
    /* DataTables search bar styling */
    .dataTables_filter {
        float: right;
        margin-bottom: 10px;
    }
    .dataTables_filter input {
        border-radius: 4px;
        padding: 5px 10px;
        border: 1px solid #ccc;
        margin-left: 5px;
    }
</style>
<!-- DataTables CSS (Bootstrap 3) -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap.min.css">
<body class="hold-transition skin-blue sidebar-mini">
    <div id="loader" class="center"></div> 
    <div class="wrapper">

        <div class="content-wrapper">
            <section class="content-header">
                <h1>Customer</h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/'?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="#">Customer</a></li>
                    <li class="active">Customer Details</li>
                </ol>
            </section>

            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-info">
                            <div class="box-header">
                                <h3 class="box-title">Customer Details</h3>
                                <div class="pull-right">

                                    <a href="<?php echo base_url('CustomerController/export_customers'); ?>" class="btn btn-success btn-sm" style="margin-left: 5px;">
                                        <i class="fa fa-file-excel-o"></i> Export Excel
                                    </a>
                                    <a href="<?php echo base_url('CustomerController/export_customers_pdf'); ?>" class="btn btn-danger btn-sm" style="margin-left: 5px;">
                                        <i class="fa fa-file-pdf-o"></i> Export PDF
                                    </a>
                                    <a href="<?php echo base_url('CustomerController/import_customers_view'); ?>" class="btn btn-info btn-sm" style="margin-left: 5px;">
                                        <i class="fa fa-upload"></i> Import
                                    </a>
                                                                        <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#myModal">
                                        <i class="glyphicon glyphicon-plus"></i> Add Customer
                                    </button>
                                </div>
                            </div>
                            <div class="box-body">
                             
                                <?php if ($this->session->flashdata('IMPORT_ERRORS')) { 
                                    $errors = $this->session->flashdata('IMPORT_ERRORS');
                                    if(!empty($errors)) { ?>
                                        <div class="alert alert-warning alert-dismissible">
                                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                            <h4><i class="icon fa fa-warning"></i> Import Errors!</h4>
                                            <ul>
                                                <?php foreach($errors as $error) { ?>
                                                    <li><?php echo $error; ?></li>
                                                <?php } ?>
                                            </ul>
                                        </div>
                                    <?php } 
                                } ?>

                                <table id="example3" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Sr.No.</th>
                                            <th>Code</th>
                                            <th>Company Name</th>
                                            <th>Name</th>
                                            <th>GST No</th>
                                            <th>PAN No</th>
                                            <th>Email</th>
                                            <th>Mobile</th>
                                            <th>Address</th>
                                            <th>State Code</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                         $i = 1;
                                         foreach ($result as $key) { 
                                            // Format address for display
                                            $rawAddress = $key->address;
                                            $addressList = [];
                                            $fullAddressString = '';
                                            
                                            if (!empty($rawAddress)) {
                                                $decoded = json_decode($rawAddress, true);
                                                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                                    $addressList = $decoded;
                                                    $fullAddressString = implode(' | ', $decoded);
                                                } else {
                                                    $fullAddressString = $rawAddress;
                                                    $addressList = [$rawAddress];
                                                }
                                            }
                                            
                                            $shortAddress = (strlen($fullAddressString) > 50) ? substr($fullAddressString, 0, 47) . '...' : $fullAddressString;
                                            $addressJson = htmlspecialchars(json_encode($addressList), ENT_QUOTES);
                                        ?>
                                            <tr>
                                                <td><?php echo $i; ?></td>
                                                <td><?php echo $key->c_code; ?></td>
                                                <td><?php echo $key->company_name; ?></td>
                                                <td><?php echo $key->fullname; ?></td>
                                                <td><?php echo $key->gst; ?></td>
                                                <td><?php echo $key->pancard; ?></td>
                                                <td><?php echo $key->email; ?></td>
                                                <td><?php echo $key->mobile; ?></td>
                                                <td>
                                                    <a href="#" class="address-popup-link" data-toggle="modal" data-target="#addressModal" 
                                                       data-addresses='<?php echo $addressJson; ?>'>
                                                        <?php echo htmlspecialchars($shortAddress); ?>
                                                    </a>
                                                 </td>
                                                <td><?php echo $key->state_code; ?></td>
                                                <td>
                                                    <a href="<?php echo base_url() . 'CustomerController/get_customer_by_id/' . $key->customer_id; ?>" class="btn btn-primary"><i class="fa fa-pencil-square"></i></a>
                                                    <a href="<?php echo base_url() . 'CustomerController/delete_customer_by_id/' . $key->customer_id; ?>" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                                                </td>
                                            </tr>
                                        <?php $i++; } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <?php $this->load->view('admin/footer'); ?>
        <div class="control-sidebar-bg"></div>
    </div>

    <!-- Address Modal -->
    <div class="modal fade" id="addressModal" tabindex="-1" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-info">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Complete Address Details</h4>
                </div>
                <div class="modal-body">
                    <div id="modalAddressContent"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Customer Modal -->
    <div id="myModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header btn-danger">
                    <center><h4 class="modal-title">Add Company <button type="button" class="close" data-dismiss="modal">&times;</button></h4></center>
                </div>
                <form class="form-horizontal form_overlay" method="post" action="<?php echo base_url(); ?>CustomerController/add_customer" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="card-body">
                            <div class="form-group row required">
                                <label for="company_name" class="col-sm-4 control-label">Company Name</label>
                                <div class="col-sm-7">
                                    <input type="text" class="form-control" name="company_name" id="company_name" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="fullname" class="col-sm-4 control-label">Customer Name</label>
                                <div class="col-sm-7">
                                    <input type="text" class="form-control" name="fullname" id="fullname">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="gst" class="col-sm-4 control-label">GST No</label>
                                <div class="col-sm-7">
                                    <input type="text" maxlength="15" class="form-control" name="gst" id="gst" style="text-transform: uppercase;">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="pancard" class="col-sm-4 control-label">PAN No</label>
                                <div class="col-sm-7">
                                    <input type="text" class="form-control" name="pancard" id="pancard" style="text-transform: uppercase;">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="state_code" class="col-sm-4 control-label">State Code</label>
                                <div class="col-sm-7">
                                    <input type="number" class="form-control" name="state_code" id="state_code">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="email" class="col-sm-4 control-label">Email</label>
                                <div class="col-sm-7">
                                    <input type="email" class="form-control" name="email" id="email" pattern="^([0-9a-zA-Z]([-.\w]*[0-9a-zA-Z])*@([0-9a-zA-Z][-\w]*[0-9a-zA-Z]\.)+[a-zA-Z]{2,9})$">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="mobile" class="col-sm-4 control-label">Mobile</label>
                                <div class="col-sm-7">
                                    <input type="text" class="form-control" name="mobile" id="mobile" maxlength="10" onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g, '')">
                                </div>
                            </div>
                          
                            <!-- Multiple Addresses -->
                            <div class="form-group row">
                                <label class="col-sm-4 control-label">Address(es)</label>
                                <div class="col-sm-8">
                                    <div id="address_container">
                                        <div class="address_row row" style="margin-bottom:15px;" data-address-index="1">
                                            <div class="col-sm-1" style="padding-top:8px;">
                                                <strong>1.</strong>
                                            </div>
                                            <div class="col-sm-9">
                                                <textarea class="form-control input-sm" name="address[]" placeholder="Enter Address" rows="2"></textarea>
                                            </div>
                                            <div class="col-sm-2">
                                                <button type="button" class="btn btn-success btn-sm add_address" style="margin-bottom:5px;"><i class="fa fa-plus"></i></button>
                                                <button type="button" class="btn btn-danger btn-sm remove_address"><i class="fa fa-minus"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                    <small class="text-muted">You can add multiple addresses. Each will be stored as JSON.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" id="btnSave" class="btn btn-success">Submit</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="glyphicon glyphicon-remove"></i> Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <script>
    $(document).ready(function () {

        // Renumber address rows in Add Modal
        function renumberAddressRows() {
            $('#address_container .address_row').each(function(index) {
                var newNum = index + 1;
                $(this).attr('data-address-index', newNum);
                $(this).find('.col-sm-1 strong').text(newNum + '.');
            });
        }

        // Add new address row
        $(document).on('click', '.add_address', function () {
            var container = $('#address_container');
            var currentCount = container.children('.address_row').length;
            var newIndex = currentCount + 1;
            var html = `
                <div class="address_row row" style="margin-bottom:15px;" data-address-index="${newIndex}">
                    <div class="col-sm-1" style="padding-top:8px;">
                        <strong>${newIndex}.</strong>
                    </div>
                    <div class="col-sm-9">
                        <textarea class="form-control input-sm" name="address[]" placeholder="Enter Address" rows="2"></textarea>
                    </div>
                    <div class="col-sm-2">
                        <button type="button" class="btn btn-success btn-sm add_address" style="margin-bottom:5px;"><i class="fa fa-plus"></i></button>
                        <button type="button" class="btn btn-danger btn-sm remove_address"><i class="fa fa-minus"></i></button>
                    </div>
                </div>
            `;
            container.append(html);
            renumberAddressRows();
        });

        // Remove address row
        $(document).on('click', '.remove_address', function () {
            if ($('.address_row').length > 1) {
                $(this).closest('.address_row').remove();
                renumberAddressRows();
            } else {
                alert("At least one address is required.");
            }
        });

        // Populate address modal with numbered list
        $(document).on('click', '.address-popup-link', function () {
            var addresses = $(this).data('addresses');
            var content = '';
            if (addresses && addresses.length) {
                if (Array.isArray(addresses)) {
                    if (addresses.length === 1) {
                        content = '<p><strong>1.</strong> ' + addresses[0] + '</p>';
                    } else {
                        content = '<ol style="margin-bottom:0; padding-left:20px;">';
                        $.each(addresses, function(index, addr) {
                            content += '<li>' + addr + '</li>';
                        });
                        content += '</ol>';
                    }
                } else {
                    content = '<p>' + addresses + '</p>';
                }
            } else {
                content = '<p class="text-muted">No address available.</p>';
            }
            $('#modalAddressContent').html(content);
        });

        // ==================== NEW CODE START ====================
        // Auto-fetch PAN and State Code from GST Number
        $('#gst').on('blur', function() {
            var gstNo = $(this).val().trim().toUpperCase();
            
            if (gstNo.length === 0) {
                $('#pancard').val('');
                $('#state_code').val('');
                return;
            }
            
            if (gstNo.length !== 15) {
                alert('GST No must be 15 characters long. Example: 27AAPFU0205R1Z0');
                $(this).val('');
                $('#pancard').val('');
                $('#state_code').val('');
                $(this).focus();
                return;
            }
            //var gstRegex = /^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[0-9]{1}[A-Z]{1}[A-Z0-9]{1}$/;
            var gstRegex = /^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[0-9]{1}[A-Z]{1}[A-Z0-9]{1}$/;
            if (!gstRegex.test(gstNo)) {
                alert('Invalid GST format. Expected: 2 digits + PAN + 1 digit + 1 letter + 1 digit\nExample: 27AAPFU0205R1Z0');
                $(this).val('');
                $('#pancard').val('');
                $('#state_code').val('');
                $(this).focus();
                return;
            }
            
            // Extract PAN (characters 3 to 12) and auto-fill
            $('#pancard').val(gstNo.substring(2, 12));
            // Extract State Code (first two digits) and auto-fill
            $('#state_code').val(gstNo.substring(0, 2));
        });
        // ==================== NEW CODE END ====================
    });
    </script>
</body>
</html>