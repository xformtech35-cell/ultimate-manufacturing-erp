<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Details</title>
   
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

        .customer-details {
            background: #ffffff;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-top: 20px;
            margin-bottom: 20px; /* Added margin bottom for spacing */
        }

        .customer-details h1 {
            font-size: 24px;
            margin-bottom: 20px;
            border-bottom: 1px solid #e1e1e1;
            padding-bottom: 10px;
        }

        .customer-details p {
            font-size: 16px;
            margin-bottom: 10px;
        }

        .customer-details p strong {
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
                <h1>Customer Details</h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i> Home</a>
                    </li>
                    <li><a href="#">Customer</a></li>
                    <li class="active">Customer Details</li>
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

                            <div class="customer-details">
                                <div>
                                    <p><strong>Customer ID:</strong> <?php echo $customer['customer_id']; ?></p>
                                    <p><strong>Full Name:</strong> <?php echo $customer['fullname']; ?></p>
                                    <p><strong>Email:</strong> <?php echo $customer['email']; ?></p>
                                    <p><strong>Mobile:</strong> <?php echo $customer['mobile']; ?></p>
                                    <p><strong>Address:</strong> <?php echo $customer['address']; ?></p>
                                    <p><strong>State Code:</strong> <?php echo $customer['state_code']; ?></p>
                                    <p><strong>Company Name:</strong> <?php echo $customer['company_name']; ?></p>
                                    <p><strong>Pan Card:</strong> <?php echo $customer['pancard']; ?></p>
                                    <p><strong>GST:</strong> <?php echo $customer['gst']; ?></p>
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

</body>

</html>
