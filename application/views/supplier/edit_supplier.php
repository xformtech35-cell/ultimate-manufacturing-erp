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
                    <li><a href="<?php echo base_url() . 'Home/index/'?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="<?php echo base_url() . 'SupplierController/index/' ?>">Edit Vendor</a></li>
                    <li class="active">Edit Vendor Details</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-info">
                            <div class="box-header">
                                <h3 class="box-title">Edit Vendor Details</h3>
                                <a href="javascript:void(0);" onclick="window.history.back()" class="btn btn-primary pull-right"><i class="fa fa-close"></i> Close</a>

                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">

                               

                                <?php if ($this->session->flashdata('INFOMSG')) { ?>
                                    <div role="alert" class="alert alert-info">
                                        <button data-dismiss="alert" class="close" type="button"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                                        <strong>Oops!!</strong> <?= $this->session->flashdata('INFOMSG') ?>
                                    </div>
                                <?php } ?>

                                <form class="form-horizontal form_overlay" method="post" action="<?php echo base_url(); ?>SupplierController/edit_supplier" enctype="multipart/form-data">
                                    <div class="card-body ">
                                        <!-- form start -->
                                        <div class="form-group row required">
                                            <label for="inputEmail3" class="col-sm-3 control-label">Company Name</label>
                                            <div class="col-sm-9">
                                                <input type="hidden" class="form-control input-sm"  name="supplier_id" id="supplier_id"  value="<?php
                                                if (isset($supplier) && !empty($supplier)) {
                                                    echo $supplier['supplier_id'];
                                                }
                                                ?>">

                                                <input type="text" class="form-control input-sm" name="company_name" id="company_name" required="" value="<?php
                                                if (isset($supplier) && !empty($supplier)) {
                                                    echo $supplier['company_name'];
                                                }
                                                ?>">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-3 control-label">Vendor Code</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control input-sm" name="s_code" id="s_code" value="<?php echo isset($supplier['s_code']) ? $supplier['s_code'] : ''; ?>">
                                            </div>
                                        </div>
                                        
                                        
                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-3 control-label">Customer Name</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control input-sm" name="fullname" id="fullname" value="<?php
                                                if (isset($supplier) && !empty($supplier)) {
                                                    echo $supplier['fullname'];
                                                }
                                                ?>">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-3 control-label"> PAN No</label>
                                            <div class="col-sm-9">
                                                <input type="text" maxlength="10" class="form-control input-sm" style="text-transform: uppercase;" name="pancard" id="pancard" value="<?php
                                                if (isset($supplier) && !empty($supplier)) {
                                                    echo $supplier['pancard'];
                                                }
                                                ?>">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-3 control-label"> GST No</label>
                                            <div class="col-sm-9">
                                                <input type="text" maxlength="15" class="form-control input-sm" style="text-transform: uppercase;" name="gst" id="gst" value="<?php
                                                if (isset($supplier) && !empty($supplier)) {
                                                    echo $supplier['gst'];
                                                }
                                                ?>">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-3 control-label"> Email(s)</label>
                                            <div class="col-sm-9">
                                                <div id="emailsContainer">
                                                    <?php
                                                    $emails_str = '';
                                                    if (isset($supplier) && !empty($supplier)) {
                                                        $emails_str = $supplier['email'];
                                                    }
                                                    $emails_str = str_replace([';', ' ', '/'], ',', $emails_str);
                                                    $emails_arr = array_filter(array_map('trim', explode(',', $emails_str)));
                                                    if (empty($emails_arr)) {
                                                        $emails_arr = [''];
                                                    }
                                                    ?>
                                                    <?php foreach ($emails_arr as $index => $em): ?>
                                                        <div class="input-group input-group-sm" style="margin-bottom: 5px; display: flex; width: 100%;">
                                                            <input type="email" class="form-control supplier-email-input" name="emails[]" value="<?= htmlspecialchars($em) ?>" placeholder="email@example.com" />
                                                            <span class="input-group-btn">
                                                                <?php if ($index === 0): ?>
                                                                    <button type="button" class="btn btn-default" onclick="addEmailField()"><i class="fa fa-plus"></i></button>
                                                                <?php else: ?>
                                                                    <button type="button" class="btn btn-danger" onclick="removeEmailField(this)"><i class="fa fa-minus"></i></button>
                                                                <?php endif; ?>
                                                            </span>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                                <small class="text-muted">Click + to add multiple email addresses.</small>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-3 control-label"> Mobile</label>
                                            <div class="col-sm-9">                                               
                                                <input type="tel" class="form-control input-sm" name="mobile" id="mobile" minlength="10" max="10" maxlength="10" value="<?php
                                                if (isset($supplier) && !empty($supplier)) {
                                                    echo $supplier['mobile'];
                                                }
                                                ?>"  onkeyup="if (/\D/g.test(this.value))
                                        this.value = this.value.replace(/\D/g, '')"  onkeyup="if (/\D/g.test(this.value))
                                                               this.value = this.value.replace(/\D/g, '')"/>                                             
                                            </div>
                                        </div>
                                        
                                        
                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-3 control-label"> State Code</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control input-sm"  name="state_code" id="state_code" value="<?php
                                                if (isset($supplier) && !empty($supplier)) {
                                                    echo $supplier['state_code'];
                                                }
                                                ?>"/>
                                            </div>
                                        </div>
                                        
                                        

                                        <div class="form-group row ">
                                            <label for="inputEmail3" class="col-sm-3 control-label"> Address</label>
                                           
                                            
                                            <div class="col-sm-9">
                                                <textarea class="form-control input-sm"  name="address" id="address" value=""><?php
                                                if (isset($supplier) && !empty($supplier)) {
                                                    echo $supplier['address'];
                                                }
                                                ?></textarea>
                                            </div>
                                            
                                            
                                            
                                        </div>
                                        
                                    </div>
                                    <div class="card-footer small text-muted">
                                        <button type="button" id="back" class="btn btn-default">Back</button>
                                        <button type="submit" class="btn btn-success pull-right">Submit</button>
                                    </div>
                                </form>

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
        <?php $this->load->view('admin/footer'); ?>
        <div class="control-sidebar-bg"></div>
    </div>
    <!-- ./wrapper -->

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


