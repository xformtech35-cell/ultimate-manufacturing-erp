<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (isset($session_data_head1)) {
} else {
    header($this->config->item('header'));
}
defined('BASEPATH') or exit('No direct script access allowed');
?>
<style>
    .required label {
        font-weight: bold;
    }

    .required label:after {
        color: #e32;
        content: '*';
        display: inline;
    }
</style>

<body class="hold-transition skin-blue sidebar-mini">
    <div id="loader" class="center"></div>
    <div class="wrapper">
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    Vendor
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="#">Vendor</a></li>
                    <li class="active">Vendor Details</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-info">
                            <div class="box-header">
                                <h3 class="box-title">Vendor Details</h3>
                                <div class="pull-right">
                                    <button class="btn btn-primary btn-sm" onclick="window.location.href='<?php echo base_url() . 'SupplierController/import_vendors_view' ?>'">
                                        <i class="fa fa-upload"></i> Import
                                    </button>
                                    <button class="btn btn-warning btn-sm" onclick="window.location.href='<?php echo base_url() . 'SupplierController/export_vendors' ?>'">
                                        <i class="fa fa-download"></i> Export Excel
                                    </button>
                                    <button class="btn btn-danger btn-sm" onclick="window.location.href='<?php echo base_url() . 'SupplierController/export_vendors_pdf' ?>'">
                                        <i class="fa fa-file-pdf-o"></i> Export PDF
                                    </button>
                                    <button class="btn btn-success btn-sm add-vendor-btn" data-toggle="modal" data-target="#addVendorModal">
                                        <i class="glyphicon glyphicon-plus"></i> Add Vendor
                                    </button>
                                </div>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">

                               

                              

                                <table id="example7" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Sr.No.</th>
                                            <th>Code</th>
                                            <th>Company Name</th>
                                            <th>Name</th>
                                            <th>PAN No</th>
                                            <th>TAX No</th>
                                            <th>Email</th>
                                            <th>Mobile</th>
                                            <th>State Code</th>
                                            <th>Address</th>
                                            <th>Edit</th>
                                            <th>Delete</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $i = 1;
                                        foreach ($result as $key) { ?>
                                            <tr>
                                                <td>
                                                    <?php echo $i; ?>
                                                </td>
                                                <td> <?php echo $key->s_code; ?> </td>
                                                <td> <?php echo $key->company_name; ?> </td>
                                                <td> <?php echo $key->fullname; ?> </td>
                                                <td> <?php echo $key->pancard; ?> </td>
                                                <td> <?php echo $key->gst; ?> </td>
                                                <td> <?php echo $key->email; ?> </td>
                                                <td> <?php echo $key->mobile; ?> </td>
                                                <td> <?php echo $key->state_code; ?> </td>
                                                <td> <?php echo $key->address; ?> </td>
                                                <td> <a href="<?php echo base_url() . 'SupplierController/get_supplier_by_id/' . $key->supplier_id; ?> " class="btn btn-primary" role="button"><i class="fa fa-pencil-square" aria-hidden="true"></i>
                                                    </a> </td>
                                                <td> <a href="<?php echo base_url() . 'SupplierController/delete_supplier_by_id/' . $key->supplier_id; ?>" class="btn btn-danger" role="button" onClick="return confirm('Are you sure you want to delete?')"><i class="fa fa-trash" aria-hidden="true"></i></a> </td>
                                            </tr>
                                        <?php $i++;
                                        } ?>
                                    </tbody>
                                </table>
                            </div>
                            <!-- /.box-body -->
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
        <!-- Add the sidebar's background. This div must be placed
             immediately after the control sidebar -->
        <div class="control-sidebar-bg"></div>
    </div>
    <!-- ./wrapper -->

    <!-- ./Vendor modal -->

    <div id="myModal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header btn-danger">
                    <center>
                        <h4 class="modal-title">Add Vendor<button type="button" class="close" data-dismiss="modal">&times;</button></h4>
                    </center>
                </div>
                <form class="form-horizontal form_overlay" method="post" action="<?php echo base_url(); ?>SupplierController/add_supplier" enctype="multipart/form-data">

                    <div class="modal-body">

                        <div class="card-body ">
                            <!-- form start -->



                            <div class="form-group row required">
                                <label for="inputEmail3" class="col-sm-4 control-label">Vendor Code</label>
                                <div class="col-sm-7">
                                    <input type="text" class="form-control input-sm" name="s_code" id="s_code" placeholder="Leave blank to auto-generate">
                                </div>
                            </div>

                            <div class="form-group row required">
                                <label for="inputEmail3" class="col-sm-4 control-label">Company Name</label>
                                <div class="col-sm-7">
                                    <input type="text" class="form-control input-sm" name="company_name" id="company_name" required="">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label">Customer Name</label>
                                <div class="col-sm-7">
                                    <input type="text" class="form-control input-sm" name="fullname" id="fullname">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label">PAN No</label>
                                <div class="col-sm-7">
                                    <input type="text" maxlength="10" class="form-control input-sm" style="text-transform: uppercase;" name="pancard" id="pancard">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label">GST No</label>
                                <div class="col-sm-7">
                                    <input type="text" maxlength="15" class="form-control input-sm" style="text-transform: uppercase;" name="gst" id="gst">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label">Email(s)</label>
                                <div class="col-sm-7">
                                    <div id="emailsContainer">
                                        <div class="input-group input-group-sm" style="margin-bottom: 5px; display: flex; width: 100%;">
                                            <input type="email" class="form-control supplier-email-input" name="emails[]" placeholder="email@example.com" />
                                            <span class="input-group-btn">
                                                <button type="button" class="btn btn-default" onclick="addEmailField()"><i class="fa fa-plus"></i></button>
                                            </span>
                                        </div>
                                    </div>
                                    <small class="text-muted">Click + to add multiple email addresses.</small>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label">Mobile</label>
                                <div class="col-sm-7">
                                    <input type="tel" class="form-control input-sm" name="mobile" id="mobile" maxlength="10" onkeyup="if (/\D/g.test(this.value))
                                    this.value = this.value.replace(/\D/g, '')" />
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label"> State Code</label>
                                <div class="col-sm-7">
                                    <input type="text" class="form-control input-sm" name="state_code" id="state_code">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label">Address</label>
                                <div class="col-sm-7">

                                    <textarea type="text" class="form-control input-sm" name="address" id="address"></textarea>
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
        function addEmailField() {
            var container = document.getElementById('emailsContainer');
            var div = document.createElement('div');
            div.className = 'input-group input-group-sm';
            div.style.marginBottom = '5px';
            div.style.display = 'flex';
            div.style.width = '100%';
            div.innerHTML = '<input type="email" class="form-control supplier-email-input" name="emails[]" placeholder="email@example.com" />' +
                            '<span class="input-group-btn">' +
                            '<button type="button" class="btn btn-danger" onclick="removeEmailField(this)"><i class="fa fa-minus"></i></button>' +
                            '</span>';
            container.appendChild(div);
        }

        function removeEmailField(btn) {
            var group = btn.closest('.input-group');
            if (group) {
                group.remove();
            }
        }
    </script>