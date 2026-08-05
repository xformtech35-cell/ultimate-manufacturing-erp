<div class="content-wrapper">
    <section class="content-header">
        <h1><?php echo $title; ?></h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo base_url('home'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="<?php echo base_url('bom'); ?>">BOM</a></li>
            <li class="active"><?php echo $title; ?></li>
        </ol>
    </section>

    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Upload Excel File</h3>
            </div>
            <?php if ($this->session->flashdata('error')) { ?>
                <div class="alert alert-danger" style="margin:10px;"><?php echo $this->session->flashdata('error'); ?></div>
            <?php } ?>
            
            <?php echo form_open_multipart('bom-import/process'); ?>
            <div class="box-body">
                <div class="form-group">
                    <label for="project_id">Select Project</label>
                    <select name="project_id" class="form-control" required>
                        <option value="">-- Select Project --</option>
                        <!-- Populate dynamically from database in a real scenario -->
                        <option value="1">Dummy Project A</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="bom_file">BOM Excel File (.xlsx, .xls)</label>
                    <input type="file" name="bom_file" class="form-control" accept=".xlsx, .xls, .csv" required>
                    <p class="help-block">Please ensure the first row contains headers: Item Code, Item Name, Quantity, UOM.</p>
                </div>
            </div>
            <div class="box-footer">
                <button type="submit" class="btn btn-primary"><i class="fa fa-upload"></i> Import Data</button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </section>
</div>