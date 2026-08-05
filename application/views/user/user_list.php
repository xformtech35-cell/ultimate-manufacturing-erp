<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (isset($session_data_head1)) {
} else {
    header($this->config->item('header'));
}
defined('BASEPATH') or exit('No direct script access allowed');
?>

<body class="hold-transition skin-blue sidebar-mini">
    <div id="loader" class="center"></div>

    <div class="wrapper">
        <div class="content-wrapper">
            <section class="content-header">
                <div class="row">
                    <div class="col-md-6">
                        <h1>
                            <i class="fa fa-users"></i> User Management
                        </h1>
                    </div>
                    <div class="col-md-6">
                        <ol class="breadcrumb pull-right">
                            <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                            <li class="active">Users</li>
                        </ol>
                    </div>
                </div>
            </section>

            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h3 class="box-title"><i class="fa fa-list"></i> User List</h3>
                                    </div>
                                    <div class="col-md-6 text-right">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-success btn-sm dropdown-toggle" data-toggle="dropdown">
                                                <i class="fa fa-download"></i> Export <span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu" role="menu">
                                                <li><a href="<?php echo base_url(); ?>UserController/export_excel"><i class="fa fa-file-excel-o"></i> Excel</a></li>
                                                <li><a href="<?php echo base_url(); ?>UserController/export_csv"><i class="fa fa-file-text-o"></i> CSV</a></li>
                                            </ul>
                                        </div>

                                        <a href="<?php echo base_url(); ?>UserController/import_form" class="btn btn-primary btn-sm">
                                            <i class="fa fa-upload"></i> Import
                                        </a>

                                        <a href="<?php echo base_url(); ?>UserController/add_user_form" class="btn btn-success btn-sm">
                                            <i class="fa fa-plus"></i> Add User
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="box-body">
                              

                                <!-- User Stats -->
                                <div class="row">
                                    <div class="col-md-3 col-sm-6 col-xs-12">
                                        <div class="info-box bg-green">
                                            <span class="info-box-icon"><i class="fa fa-users"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Total Users</span>
                                                <span class="info-box-number"><?php echo count($users); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-6 col-xs-12">
                                        <div class="info-box bg-blue">
                                            <span class="info-box-icon"><i class="fa fa-sitemap"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Departments</span>
                                                <span class="info-box-number"><?php echo count($department_result); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- User Table -->
                                <div class="table-responsive">
                                    <table id="userTable" class="table table-hover table-striped table-bordered">
                                        <thead>
                                            <tr class="bg-primary">
                                                <th width="5%">#</th>
                                                <th width="20%">Username</th>
                                                <th width="20%">Email</th>
                                                <th width="15%">Role</th>
                                                <th width="15%">Department</th>
                                                <th width="15%">Location</th>
                                                <th width="10%">Created Date</th>
                                                <th width="15%" class="text-center">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($users)): ?>
                                                <?php $counter = 1; ?>
                                                <?php foreach ($users as $user): ?>
                                                    <tr>
                                                        <td><?php echo $counter++; ?></td>
                                                        <td>
                                                            <strong><?php echo htmlspecialchars($user->username); ?></strong>
                                                        </td>
                                                        <td>
                                                            <i class="fa fa-envelope text-muted"></i>
                                                            <?php echo htmlspecialchars($user->user_email); ?>
                                                        </td>
                                                        <td>
                                                            <span class="label label-info">
                                                                <?php echo htmlspecialchars($user->role_name); ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <?php if (!empty($user->department_name)): ?>
                                                                <span class="label label-success">
                                                                    <?php echo htmlspecialchars($user->department_name); ?>
                                                                </span>
                                                            <?php else: ?>
                                                                <span class="label label-default">Not Assigned</span>
                                                            <?php endif; ?>
                                                        </td>

                                                        <td>
                                                            <?php if (!empty($user->location_name)): ?>
                                                                <span class="label label-warning">
                                                                    <i class="fa fa-map-marker"></i> <?php echo htmlspecialchars($user->location_name); ?>
                                                                </span>
                                                            <?php else: ?>
                                                                <span class="label label-default">Not Assigned</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <i class="fa fa-calendar text-muted"></i>
                                                            <?php echo date('d-m-Y', strtotime($user->created_date)); ?>
                                                        </td>
                                                        <td class="text-center">
                                                            <div class="btn-group">
                                                                <a href="<?php echo base_url('UserController/get_user_by_id/' . $user->user_id); ?>"
                                                                    class="btn btn-primary btn-xs"
                                                                    title="Edit User"
                                                                    data-toggle="tooltip">
                                                                    <i class="fa fa-edit"></i>
                                                                </a>
                                                                <a href="<?php echo base_url('UserController/delete_user_by_id/' . $user->user_id); ?>"
                                                                    class="btn btn-danger btn-xs"
                                                                    title="Delete User"
                                                                    data-toggle="tooltip"
                                                                    onclick="return confirmDelete('<?php echo htmlspecialchars($user->username); ?>')">
                                                                    <i class="fa fa-trash"></i>
                                                                </a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="7" class="text-center">
                                                        <div class="alert alert-info">
                                                            <i class="fa fa-info-circle"></i> No users found.
                                                            <a href="<?php echo base_url(); ?>UserController/add_user_form" class="alert-link">Add a new user</a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="box-footer">
                                <div class="row">
                                    <div class="col-md-6">
                                        <a href="<?php echo base_url(); ?>UserController/download_template" class="btn btn-default">
                                            <i class="fa fa-download"></i> Download Import Template
                                        </a>
                                    </div>
                                    <div class="col-md-6 text-right">
                                        <small class="text-muted">
                                            Showing <?php echo count($users); ?> user(s)
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <?php $this->load->view('admin/footer'); ?>
    </div>

    <style>
        .info-box {
            min-height: 60px;
            margin-bottom: 15px;
        }

        .info-box-icon {
            height: 60px;
            line-height: 60px;
            width: 60px;
        }

        .info-box-content {
            padding: 10px;
        }

        .table-hover tbody tr:hover {
            background-color: #f5f5f5;
        }

        .label {
            padding: 5px 10px;
            font-size: 12px;
        }

        .btn-group .btn-xs {
            padding: 3px 8px;
            margin: 0 2px;
        }

        .alert {
            margin-bottom: 15px;
        }
    </style>

    <script>
        $(document).ready(function() {
            // Destroy existing DataTable if it exists
            if ($.fn.DataTable.isDataTable('#userTable')) {
                $('#userTable').DataTable().destroy();
            }

            // Initialize DataTable
            $('#userTable').DataTable({
                "pageLength": 25,
                "lengthMenu": [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "All"]
                ],
                "order": [
                    [0, 'asc']
                ],
                "responsive": true,
                "language": {
                    "search": "Search Users:",
                    "lengthMenu": "Show _MENU_ users per page",
                    "zeroRecords": "No users found",
                    "info": "Showing _START_ to _END_ of _TOTAL_ users",
                    "infoEmpty": "No users available",
                    "infoFiltered": "(filtered from _MAX_ total users)"
                },
                "columnDefs": [{
                    "orderable": false,
                    "targets": [6]
                }]
            });

            // Initialize tooltips
            $('[data-toggle="tooltip"]').tooltip();
        });

        function confirmDelete(username) {
            return confirm('Are you sure you want to delete user: ' + username + '?\nThis action cannot be undone.');
        }
    </script>
</body>