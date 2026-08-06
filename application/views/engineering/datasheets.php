<?php
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
</style>

<body class="hold-transition skin-blue sidebar-mini">
    <div id="loader" class="center"></div> 
    <div class="wrapper">
        <?php $this->load->view('admin/header_side_bar'); ?>
        
        <div class="content-wrapper">
            <section class="content-header">
                <h1>
                    <i class="fa fa-file-text-o"></i> Datasheet Upload (Excel / PDF)
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/'; ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="#">Engineering</a></li>
                    <li class="active">Datasheet Upload</li>
                </ol>
            </section>

            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        
                        <!-- Upload Form Box -->
                        <div class="box box-info">
                            <div class="box-header with-border">
                                <h3 class="box-title">
                                    <i class="fa fa-upload"></i> Upload Equipment / Material Datasheet
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

                                <form class="form-horizontal" method="post" action="<?php echo base_url('EngineeringController/upload_datasheet'); ?>" enctype="multipart/form-data">
                                    <div class="row">
                                        <div class="col-md-12">
                                            
                                            <div class="form-group row required">
                                                <label for="salesorder_id_fk" class="col-sm-3 control-label">Sales Order Number</label>
                                                <div class="col-sm-6">
                                                    <select class="form-control input-sm select2" name="salesorder_id_fk" id="salesorder_id_fk" required onchange="loadBomItems(this.value)">
                                                        <option value="">-- Select Sales Order --</option>
                                                        <?php if (!empty($sales_orders)) { ?>
                                                            <?php foreach ($sales_orders as $so) { ?>
                                                                <option value="<?php echo $so->id; ?>" data-sonumber="<?php echo htmlspecialchars($so->so_number); ?>">
                                                                    <?php echo htmlspecialchars($so->so_number . (!empty($so->company_name) ? ' - ' . $so->company_name : '')); ?>
                                                                </option>
                                                            <?php } ?>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="equipment_name" class="col-sm-3 control-label">Equipment / Material Name</label>
                                                <div class="col-sm-6">
                                                    <input type="text" class="form-control input-sm" name="equipment_name" id="equipment_name" placeholder="Enter Equipment or Material name">
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="bom_item_id_fk" class="col-sm-3 control-label">Link to BOM Item (Optional)</label>
                                                <div class="col-sm-6">
                                                    <select class="form-control input-sm" name="bom_item_id_fk" id="bom_item_id_fk">
                                                        <option value="">-- Select Sales Order First --</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="form-group row required">
                                                <label for="datasheet_file" class="col-sm-3 control-label">Datasheet File (Excel / PDF)</label>
                                                <div class="col-sm-6">
                                                    <input type="file" class="form-control input-sm" name="datasheet_file" id="datasheet_file" accept=".pdf, .xls, .xlsx, application/pdf, application/vnd.ms-excel, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" required>
                                                    <small class="text-muted"><i class="fa fa-info-circle"></i> Allowed formats: <strong>Excel (.xls, .xlsx)</strong> or <strong>PDF (.pdf)</strong>. Max size: 20MB.</small>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="remarks" class="col-sm-3 control-label">Remarks</label>
                                                <div class="col-sm-6">
                                                    <textarea class="form-control input-sm" name="remarks" id="remarks" rows="2" placeholder="Enter optional notes/remarks"></textarea>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <div class="col-sm-offset-3 col-sm-6">
                                                    <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-upload"></i> Upload Datasheet</button>
                                                    <button type="reset" class="btn btn-default btn-sm"><i class="fa fa-refresh"></i> Reset</button>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Datasheet Table Box -->
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <h3 class="box-title">
                                    <i class="fa fa-list"></i> Uploaded Datasheets List
                                </h3>
                            </div>
                            <div class="box-body table-responsive">
                                <table id="datasheetsTable" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th style="width: 50px;">#</th>
                                            <th>SO Number</th>
                                            <th>Equipment / Material</th>
                                            <th>BOM Item</th>
                                            <th>File Name</th>
                                            <th style="width: 80px;">Type</th>
                                            <th>Uploaded By</th>
                                            <th>Uploaded At</th>
                                            <th>Remarks</th>
                                            <th style="width: 100px;" class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($datasheets)) { 
                                            $cnt = 1;
                                            foreach ($datasheets as $ds) { ?>
                                                <tr>
                                                    <td><?= $cnt++; ?></td>
                                                    <td><strong><?= htmlspecialchars($ds->so_number); ?></strong></td>
                                                    <td><?= htmlspecialchars($ds->equipment_name ?: '-'); ?></td>
                                                    <td><?= htmlspecialchars($ds->bom_code ?: '-'); ?></td>
                                                    <td>
                                                        <i class="fa <?= ($ds->file_type === 'pdf') ? 'fa-file-pdf-o text-danger' : 'fa-file-excel-o text-success'; ?>"></i>
                                                        <?= htmlspecialchars($ds->original_name); ?>
                                                    </td>
                                                    <td>
                                                        <span class="label <?= ($ds->file_type === 'pdf') ? 'label-danger' : 'label-success'; ?>">
                                                            <?= strtoupper($ds->file_type); ?>
                                                        </span>
                                                    </td>
                                                    <td><?= htmlspecialchars($ds->uploaded_by); ?></td>
                                                    <td><?= date('d-M-Y H:i', strtotime($ds->uploaded_at)); ?></td>
                                                    <td><?= htmlspecialchars($ds->remarks ?: '-'); ?></td>
                                                    <td class="text-center">
                                                        <a href="<?= base_url('EngineeringController/download_file/datasheet/' . $ds->id); ?>" class="btn btn-xs btn-info" title="Download File">
                                                            <i class="fa fa-download"></i>
                                                        </a>
                                                        <a href="<?= base_url('EngineeringController/delete_datasheet/' . $ds->id); ?>" class="btn btn-xs btn-danger" onclick="return confirm('Are you sure you want to delete this datasheet?');" title="Delete Datasheet">
                                                            <i class="fa fa-trash"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php } 
                                        } else { ?>
                                            <tr>
                                                <td colspan="10" class="text-center text-muted">No datasheets uploaded yet.</td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
            </section>
        </div>
        
        <?php $this->load->view('admin/footer'); ?>
    </div>

    <script>
        function loadBomItems(soId) {
            var selectedOpt = $('#salesorder_id_fk option:selected');
            var soNumber = selectedOpt.data('sonumber');
            var bomSelect = $('#bom_item_id_fk');
            
            bomSelect.html('<option value="">-- Loading BOM Items... --</option>');

            if (!soNumber) {
                bomSelect.html('<option value="">-- Select Sales Order First --</option>');
                return;
            }

            $.ajax({
                url: '<?= base_url("EngineeringController/get_bom_items"); ?>',
                type: 'POST',
                data: { so_number: soNumber },
                dataType: 'json',
                success: function(data) {
                    var options = '<option value="">-- Select BOM Item (Optional) --</option>';
                    if (data && data.length > 0) {
                        $.each(data, function(idx, item) {
                            options += '<option value="' + item.bom_item_id + '">' + item.bom_code + '</option>';
                        });
                    } else {
                        options = '<option value="">-- No BOM Items found for this SO --</option>';
                    }
                    bomSelect.html(options);
                },
                error: function() {
                    bomSelect.html('<option value="">-- Select BOM Item (Optional) --</option>');
                }
            });
        }

        $(document).ready(function() {
            if ($.fn.DataTable) {
                if ($.fn.DataTable.isDataTable('#datasheetsTable')) {
                    $('#datasheetsTable').DataTable().destroy();
                }
                $('#datasheetsTable').DataTable({
                    "ordering": true,
                    "responsive": true
                });
            }
        });
    </script>
</body>
</html>
