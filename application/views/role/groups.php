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
        <i class="fa fa-shield"></i> Groups &amp; Permissions
    </h1>

    <ol class="breadcrumb">
        <li>
            <a href="<?= base_url('Home/index'); ?>">
                <i class="fa fa-dashboard"></i> Home
            </a>
        </li>
        <li class="active">Groups</li>
    </ol>
</section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <h3 class="box-title"><i class="fa fa-list"></i> Group Permission Details</h3>
                            </div>
                            <div class="box-body" style="padding-bottom: 120px;">
                            

                                <form class="form-horizontal" id="" method="post" action="<?php echo base_url(); ?>RoleController/permission_save/">
                                    <div class="row">
                                        <div class="form-group required">
                                            <label class="col-sm-3 control-label" for="role"><i class="fa fa-users"></i> Group Name</label>
                                            <div class="col-sm-5">
                                                <select class="chosen-select form-control" name="role" data-placeholder="Choose a Member Group..." required="" id="groupname">
                                                    <option value="">Choose a Member Group...</option>
                                                    <?php if (is_array($role) || is_object($role)) {
                                                        foreach ($role as $key) { ?>
                                                            <option value="<?php echo $key->role_id; ?>"><?php echo $key->role_name; ?></option>
                                                    <?php }
                                                    } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <hr style="color: 1px solid blue;">
                                    <style>
                                        .table-permissions th,
.table-permissions td {
    vertical-align: middle !important;
}

.permission-container {
    display: flex;
    flex-wrap: wrap;
    gap: 9px 18px;
    align-items: center;
}

.permission-label {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    margin: 4px 0;
    font-weight: 500;
    cursor: pointer;
    white-space: nowrap;
}

.permission-label input[type="checkbox"] {
    width: 15px;
    height: 15px;
    margin: 0;
}

.table-permissions td {
    padding: 6px !important;
}
                                    </style>
                                    <div class="row" style="padding: 0 20px;">
                                        <table class="table table-bordered table-striped table-permissions no-datatable">
                                            <thead>
                                                <tr>
                                                    <th style="width: 200px;">Module Name</th>
                                                    <th style="width: 250px;">Main Group Permission</th>
                                                    <th>Sub Group Permission</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                // Helper function to recursively find all sub-menu items under a parent ID
                                                if (!function_exists('get_descendants_with_permissions')) {
                                                    function get_descendants_with_permissions($parent_id, $all_menus) {
                                                        $descendants = array();
                                                        foreach ($all_menus as $menu) {
                                                            if ($menu['parent_id'] == $parent_id) {
                                                                if (!empty($menu['permission'])) {
                                                                    $descendants[] = $menu;
                                                                }
                                                                $children = get_descendants_with_permissions($menu['id'], $all_menus);
                                                                $descendants = array_merge($descendants, $children);
                                                            }
                                                        }
                                                        return $descendants;
                                                    }
                                                }

                                                if (is_array($sidebar_menu) || is_object($sidebar_menu)) {
                                                    foreach ($sidebar_menu as $parent) {
                                                        // Render top level menus only
                                                        if (empty($parent['parent_id'])) {
                                                            ?>
                                                            <tr>
                                                                <td><?= $parent['title']; ?></td>
                                                                <td>
                                                                    <?php if (!empty($parent['permission'])): ?>
<label class="permission-label">
    <input
        type="checkbox"
        value="<?= $parent['permission']; ?>"
        id="<?= $parent['permission']; ?>"
        name="grp_perm[]"
        class="main-perm">

    <span><?= $parent['title']; ?></span>
</label>                                                                    <?php endif; ?>
                                                                </td>
                                                               <td>
<?php
$descendants = get_descendants_with_permissions($parent['id'], $sidebar_menu);
?>

<div class="permission-container">

    <?php foreach ($descendants as $desc) { ?>

        <label class="permission-label">
            <input
                type="checkbox"
                value="<?= $desc['permission']; ?>"
                id="<?= $desc['permission']; ?>"
                name="grp_perm[]"
                class="sub-perm">

            <span><?= $desc['title']; ?></span>
        </label>

    <?php } ?>

</div>
</td>
                                                            </tr>
                                                            <?php
                                                        }
                                                    }
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="permission col-md-offset-5 col-md-9" style="margin-top:20px;">
                                        <input class="btn btn-success" type="submit" value="Save">
                                        &nbsp; &nbsp; &nbsp;
                                        <input class="btn btn-danger" type="reset" value="Reset">
                                    </div><br><br>
                                    <div style="height: 100px;"></div>
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
        <!-- /.content-wrapper -->
        <script>
        window.addEventListener('load', function() {
            // Exclude no-datatable class from DataTable initialization
            if ($.fn.DataTable && $('.table-permissions:not(.no-datatable)').length > 0) {
                $('.table-permissions:not(.no-datatable)').DataTable({
                    "pageLength": 25,
                    "ordering": false,
                    "language": {
                        "search": "Search Permissions:"
                    }
                });
            }

            // AJAX fetch permissions on Group Name change
            $('#groupname').on('change', function() {
                var role_id = $(this).val();
                
                // Clear all checkboxes first
                $('input[name="grp_perm[]"]').prop('checked', false);
                
                if (!role_id) {
                    return;
                }
                
                $.ajax({
                    url: '<?php echo base_url(); ?>RoleController/get_groups_by_role_id_fk',
                    type: 'POST',
                    data: { role_id_fk: role_id },
                    dataType: 'json',
                    success: function(response) {
                        if (response && response.length > 0) {
                            $.each(response, function(index, item) {
                                // Check matching main and sub checkboxes
                                $('input[name="grp_perm[]"][value="' + item.grp_perm + '"]').prop('checked', true);
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error fetching permissions: ' + error);
                    }
                });
            });

            // If a group name is already selected (e.g. reload), trigger change event to load permissions
            if ($('#groupname').val()) {
                $('#groupname').trigger('change');
            }

            // Toggle all sub-permissions when the main permission checkbox changes
            $(document).on('change', '.main-perm', function() {
                var isChecked = $(this).prop('checked');
                $(this).closest('tr').find('.sub-perm').prop('checked', isChecked);
            });

            // Check parent main permission if any sub-permission is checked; uncheck if all are unchecked
            $(document).on('change', '.sub-perm', function() {
                var row = $(this).closest('tr');
                var checkedSubs = row.find('.sub-perm:checked').length;
                
                if (checkedSubs > 0) {
                    row.find('.main-perm').prop('checked', true);
                } else {
                    row.find('.main-perm').prop('checked', false);
                }
            });
        });
        </script>
        <?php $this->load->view('admin/footer'); ?>
    </div>
