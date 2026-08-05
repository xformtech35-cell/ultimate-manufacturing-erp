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
        Stock
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo base_url() . 'Home/index/'?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">Stock</a></li>
        <li class="active">Stock Details</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          <div class="box box-info">
            <div class="box-header">
              <h3 class="box-title">Stock Details</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
             <table id="example2" class="table table-bordered table-striped">
                <thead>
                <tr>
                            <th>Sr.No</th>
                            <th>Raw Item</th>
                            <th>Available Stock</th>
                            <th>Action</th>
                </tr>
                </thead>
                <tbody>
                    <?php $i=1; foreach ($stock as $key) {  ?>
                            <tr>
                                <td>
                                <?php echo $i; ?>
                                </td>
                                <td> <?php echo $key->raw_item_master_name; ?> </td>
                                <td> <?php echo $key->raw_item_stock; ?> </td>
                                <td>
                                    <a href="<?php echo base_url() . 'SupplierController/delete_row_item_stock_by_id/' . $key->raw_item_stock_id; ?>" class="btn btn-danger" role="button" onClick="return confirm('Are you sure you want to delete?')"><i class="fa fa-trash" aria-hidden="true"></i></a> </td>
                            </tr>
                        <?php $i++; } ?>
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
<?php $this->load->view('admin/footer'); ?>
  <div class="control-sidebar-bg"></div>
</div>
