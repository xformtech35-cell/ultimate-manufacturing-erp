<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendor Details</title>
    <!-- Bootstrap CSS -->
    <style>
    .content-wrapper {
        background: #f9f9f9;
        padding: 20px;
    }
    .breadcrumb {
        background: #ffffff;
        border: 1px solid #e1e1e1;
        border-radius: 4px;
    }
    .vendor-details {
        background: #ffffff;
        border-radius: 4px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        padding: 20px;
        margin-top: 20px;
    }
    .vendor-details h1 {
        font-size: 24px;
        margin-bottom: 20px;
        border-bottom: 1px solid #e1e1e1;
        padding-bottom: 10px;
    }
    .vendor-details p {
        font-size: 16px;
        margin-bottom: 10px;
    }
    .vendor-details p strong {
        color: #333;
    }
</style>

</head>

<body class="hold-transition skin-blue sidebar-mini">
    <div id="loader" class="center"></div>
    <div class="wrapper">
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                Vendor Details
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="#">Vendor</a></li>
                    <li class="active">Vendor Details</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <?php if ($this->session->flashdata('SUCCESSMSG')) { ?>
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    <h4 class="alert-heading"><i class="icon fa fa-check"></i> Success!</h4>
                                    <?= $this->session->flashdata('SUCCESSMSG'); ?>
                                </div>
                            <?php } ?>

                            <?php if ($this->session->flashdata('INFOMSG')) { ?>
                                <div class="alert alert-info alert-dismissible fade show" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    <h4 class="alert-heading"><i class="icon fa fa-info"></i> Info!</h4>
                                    <?= $this->session->flashdata('INFOMSG'); ?>
                                </div>
                            <?php } ?>

                            <div class="vendor-details">
                             
                                <div>
                                    <p><strong>Company Name:</strong> <?php echo $supplier['company_name']; ?></p>
                                    <p><strong>Name:</strong> <?php echo $supplier['fullname']; ?></p>
                                    <p><strong>PAN No:</strong> <?php echo $supplier['pancard']; ?></p>
                                    <p><strong>GST No:</strong> <?php echo $supplier['gst']; ?></p>
                                    <p><strong>Email:</strong> <?php echo $supplier['email']; ?></p>
                                    <p><strong>Mobile:</strong> <?php echo $supplier['mobile']; ?></p>
                                    <p><strong>State Code:</strong> <?php echo $supplier['state_code']; ?></p>
                                    <p><strong>Address:</strong> <?php echo $supplier['address']; ?></p>
                                </div>
                            </div>
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->
                </div>
                <!-- /.container-fluid -->
            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->

        <?php $this->load->view('admin/footer'); ?>
        <!-- Add the sidebar's background. This div must be placed
             immediately after the control sidebar -->
        <div class="control-sidebar-bg"></div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>

</html>
