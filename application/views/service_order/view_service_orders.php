<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (!isset($session_data_head1)) {
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
                    <?php echo $config['title']; ?>
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="#"><?php echo $config['title']; ?></a></li>
                    <li class="active"><?php echo $config['title']; ?> Details</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-info">

                            <div class="box-header">
                                <div class="row">
                                    <div class="col-md-4">
                                        <h3 class="box-title"><?php echo $config['title']; ?> List</h3> 
                                    </div>
                                    <div class="col-md-3">
                                        <!-- Optional date filtering -->
                                    </div>
                                    <div class="col-md-5">
                                        <div class="pull-right">
                                            <a href="<?php echo base_url(); ?>ServiceOrderController/<?php echo $config['url_prefix']; ?>?str=All" class="btn btn-success btn-sm"> Show All</a>
                                            <a href="<?php echo base_url(); ?>ServiceOrderController/create_service_order/<?php echo $config['type']; ?>" class="btn btn-primary btn-sm"><i class="glyphicon glyphicon-plus"></i> Create <?php echo $config['title']; ?></a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <ul class="nav nav-tabs">
                                <li class="nav-item">
                                    <a class="nav-link <?php echo ($this->uri->segment(2) == 'get_service_order_data_by_status' && $this->uri->segment(4) == '2') ? 'active' : ''; ?>" href="<?php echo base_url(); ?>ServiceOrderController/get_service_order_data_by_status/<?php echo $config['type']; ?>/2">Sent <span class="badge bg-blue"><?php echo $sent_count; ?></span></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link <?php echo ($this->uri->segment(2) == 'get_service_order_data_by_status' && $this->uri->segment(4) == '1') ? 'active' : ''; ?>" href="<?php echo base_url(); ?>ServiceOrderController/get_service_order_data_by_status/<?php echo $config['type']; ?>/1">Draft <span class="badge bg-gray"><?php echo $draft_count; ?></span></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link <?php echo ($this->uri->segment(2) == $config['url_prefix'] || $this->uri->segment(2) == '') ? 'active' : ''; ?>" href="<?php echo base_url(); ?>ServiceOrderController/<?php echo $config['url_prefix']; ?>?str=All">All <span class="badge bg-green"><?php echo $total_count; ?></span></a>
                                </li>
                            </ul>

                            <!-- /.box-header -->
                            <div class="box-body">
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
                                        <h4><i class="icon fa fa-info"></i> Info</h4>
                                        <?php echo $this->session->flashdata('INFOMSG'); ?>
                                    </div>
                                <?php } ?>

                                <table id="service_orders_table" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Sr.No.</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                            <th>Number</th>
                                            <th>Company Name</th>
                                            <th>Customer Name</th>
                                            <th>Total</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $i = 1;
                                        foreach ($service_orders as $key) {
                                            $status_text = 'Draft';
                                            $status_class = 'label-default';
                                            if ($key['status'] == 2) { $status_text = 'Sent'; $status_class = 'label-primary'; }
                                            elseif ($key['status'] == 3) { $status_text = 'Viewed'; $status_class = 'label-info'; }
                                            elseif ($key['status'] == 4) { $status_text = 'Approved'; $status_class = 'label-success'; }
                                            elseif ($key['status'] == 5) { $status_text = 'Rejected'; $status_class = 'label-danger'; }
                                            elseif ($key['status'] == 6) { $status_text = 'Canceled'; $status_class = 'label-warning'; }
                                            ?>
                                            <tr>
                                                <td><?php echo $i; ?></td>
                                                <td><span class="label <?php echo $status_class; ?>"><?php echo $status_text; ?></span></td>
                                                <td><?php echo date('d-m-Y', strtotime($key['date'])); ?></td>
                                                <td>
                                                    <a href="<?php echo base_url() . 'ServiceOrderController/show_service_order/' . $key['number_fk']; ?>"><?php echo $key['number_fk']; ?></a>
                                                </td>
                                                <td><?php echo $key['company_name']; ?></td>
                                                <td><?php echo $key['fullname']; ?></td>
                                                <td><?php echo number_format($key['total'], 2); ?></td>
                                                <td>
                                                    <div class="dropdown">
                                                        <button class="btn btn-primary btn-xs dropdown-toggle" type="button" data-toggle="dropdown">
                                                            Actions <span class="caret"></span>
                                                        </button>
                                                        <ul class="dropdown-menu pull-right">
                                                            <li><a href="<?php echo base_url() . 'ServiceOrderController/show_service_order/' . $key['number_fk']; ?>">View</a></li>
                                                            <li><a href="<?php echo base_url() . 'ServiceOrderController/edit_service_order_details/' . $key['number_fk']; ?>">Edit</a></li>
                                                            <li><a href="<?php echo base_url() . 'ServiceOrderController/print_service_order/' . $key['number_fk']; ?>" target="_blank">Print</a></li>
                                                            <li class="divider"></li>
                                                            <li><a href="<?php echo base_url() . 'ServiceOrderController/delete_service_order/' . $key['number_fk']; ?>" onClick="return confirm('Are you sure you want to delete?')">Delete</a></li>
                                                        </ul>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php
                                            $i++;
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                            <!-- /.box-body -->
                        </div>
                        <!-- /.box -->
                    </div>
                </div>
            </section>
        </div>
        <?php $this->load->view('admin/footer'); ?>
    </div>
</body>
