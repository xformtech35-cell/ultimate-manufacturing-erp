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
                  Balance Sheet Report
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i>Home</a></li>
                    <li><a href="#">Report</a></li>
                    <li class="active">Report</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <!-- left column -->
                    <div class="col-md-12">
                        <!-- Horizontal Form -->
                        <div class="box box-info">
                            <div class="box-header with-border">
                                <h3 class="box-title">Balance Sheet Report </h3>
                            </div>
                            <!-- /.box-header -->
                            <!-- form start -->
                            <form class="form-horizontal form_overlay" method="post" action="<?php echo base_url(); ?>ReportController/create_balance_sheet_report">
                                <div class="box-body">
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-3 control-label">From Date<span style="color: red;">*</span></label>
                                        <div class="col-sm-4">
                                            <input type="text" id="from_date" autocomplete="off" class="form-control backdate created-date" name="from_date" required="" onkeydown="return false;">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-3 control-label">To Date<span style="color: red;">*</span></label>
                                        <div class="col-sm-4">
                                            <input type="text" id="to_date" autocomplete="off" class="form-control  payment-due-date-check" name="to_date" required="" onkeydown="return false;"> 
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-3 control-label">Assets</label>
                                        <div class="col-sm-9">
                                            <div style="overflow-x:auto;">
                                                <table class="table-bordered" id="dynamic_field">
                                                    <tr>
                                                        <td style="width: 120px">
                                                            
                                                            
                                                            <select class="form-control input-sm asset" id="asset_id" onchange="getAssetId();"  name="asset[]" data-live-search="true" >
                                                        <option value=""></option>
                                                        <?php foreach ($asset_name as $key) { ?>
                                                            <option value="<?php echo $key->asset_id; ?>"><?php echo $key->asset; ?></option> 
                                                        <?php } ?>  
                                                    </select>
                                                        </td>
                                                        <td style="width: 120px">
                                                            <select class="form-control input-sm subasset"  name="asset_sub_category[]" id="asset_sub_category" data-live-search="true">
                                                        <option value=""></option>
                                                       
                                                    </select>
                                                        </td> 
                                                        <td style="width: 120px"><input type="text" name="price[]" id="price" class="form-control form-control-sm "/></td> 
                                                        <td><button type="button" accesskey="n" name="add_assets" id="add_assets"   class="btn btn-success"><i class="fa fa-plus-circle" aria-hidden="true"></i></button></td>  
                                                    </tr>
                                                    
                                                   
                                                </table>
                                                 
                                            </div>
                                        </div>
                                    </div>
                                    
                                    
                                     <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-3 control-label">Liabilities</label>
                                        <div class="col-sm-9">
                                            <div style="overflow-x:auto;">
                                                  <table class="table-bordered" id="dynamic_field_Liabilities">
                                               <tr>
                                                        <td style="width: 120px">
                                                            
                                                            
                                                            <select class="form-control input-sm Liabilities" id="Liabilities_id" onchange="getLiabilitiesId();"  name="Liabilities_id[]" data-live-search="true" >
                                                        <option value=""></option>
                                                      
                                                         
                                                        <?php foreach ($liabilities_name as $key) { ?>
                                                       
                                                            <option value="<?php echo $key->liabilities_id; ?>"><?php echo $key->liabilities; ?></option> 
                                                        <?php } ?>  
                                                    </select>
                                                        </td>
                                                        <td style="width: 120px">
                                                            <select class="form-control input-sm subLiabilities"  name="Liabilities_sub_category[]" id="Liabilities_sub_category" data-live-search="true">
                                                        <option value=""></option>
                                                       
                                                    </select>
                                                        </td> 
                                                        <td style="width: 120px"><input type="text" name="Liabilitiesprice[]" id="Liabilitiesprice" class="form-control form-control-sm "/></td> 
                                                        <td><button type="button" accesskey="n" name="add_Liabilities" id="add_Liabilities"   class="btn btn-success"><i class="fa fa-plus-circle" aria-hidden="true"></i></button></td>  
                                                    </tr>
                                                       
                                                 </table>
                                                 
                                            </div>
                                        </div>
                                    </div>
                                    
                                  
                                    <center><button type="submit" class="btn btn-default">Cancel</button>
                                        <button type="submit" class="btn btn-success">Submit</button></center>
                                </div>
                                <!-- /.box-body -->
                                <div class="box-footer">
                                    
                                </div>
                                <!-- /.box-footer -->
                            </form>
                            
                             <a href="<?php echo base_url(); ?>ReportController/get_balance_sheet_report"><button class="btn btn-success pull-right">Export to Excel</button></a>
                      
                            
                            
                        </div>
                        <!-- /.box -->
                    </div>
                 </div>
                <!-- /.row -->
            </section>
            <!-- /.content -->
        </div>

        <?php $this->load->view('admin/footer'); ?>
        <div class="control-sidebar-bg"></div>
    </div>
    <!-- ./wrapper -->

