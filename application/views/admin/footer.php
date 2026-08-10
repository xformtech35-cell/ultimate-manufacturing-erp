<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Get CI instance
$CI =& get_instance();

// Get session data for footer
$session_data_head2 = $CI->session->userdata('session_data_head2');
$company_name = isset($session_data_head2['company_name']) ? $session_data_head2['company_name'] : 'Xform Technologies Pvt Ltd.';
$company_website = isset($session_data_head2['company_website']) ? $session_data_head2['company_website'] : 'https://www.xform.in/';

// Get user_id from session
$session_data_head = $CI->session->userdata('session_data_head');
$user_id = isset($session_data_head['result']['user_id']) ? $session_data_head['result']['user_id'] : null;

// Initialize variables
$gst_class = array();
$unit_result = array();
$category_result = array();
$group_result = array();

// Direct model calls from footer
if ($user_id) {
    // Load models if not already loaded
    $CI->load->model('inventory');
    $CI->load->model('units');
    $CI->load->model('ItemCategory');
    $CI->load->model('ItemGroup');
    
    // Get data directly from models
    $gst_class = $CI->inventory->get_gst_class($user_id);
    $unit_result = $CI->units->get_unit_name($user_id);
    $category_result = $CI->ItemCategory->get_categories($user_id);
    $group_result = $CI->ItemGroup->get_groups($user_id);
}

// Default GST values if no data found
if (empty($gst_class)) {
    $gst_class = array(
        array('gst_class' => '0'),
        array('gst_class' => '5'),
        array('gst_class' => '12'),
        array('gst_class' => '18'),
        array('gst_class' => '28')
    );
}

// Default unit values if no data found
if (empty($unit_result)) {
    $default_units = array('PCS', 'KG', 'MTR', 'BOX', 'LTR', 'SQFT', 'NOS');
    $unit_result = array();
    foreach ($default_units as $unit) {
        $obj = new stdClass();
        $obj->unit = $unit;
        $unit_result[] = $obj;
    }
}
?>

<!-- jQuery UI -->
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<!-- Bootstrap JS -->
<!-- <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script> -->

<!-- CKEditor -->
<script src="https://cdn.ckeditor.com/4.16.2/standard/ckeditor.js"></script>

<footer class="main-footer">
    <div class="pull-right hidden-xs"></div>
    <center>
        <strong>Copyright &copy; 2017-<?php echo date("Y"); ?> 
            <a href="<?php echo $company_website; ?>" target="_blank"><?php echo $company_name; ?></a>
        </strong> 
        Designed & Developed by 
        <strong><a href="http://xform.in/" target="_blank">Xform Technologies Pvt Ltd.</a></strong>
    </center>
</footer>

<!-- Product Modal -->
<div id="productModal" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content enhanced-card">
            <div class="modal-header" style="padding: 20px 30px; border-bottom: 1px solid #e5e5e5;">
                <h4 class="modal-title" style="margin: 0;"><i class="fa fa-plus-circle"></i> Add New Inventory Item</h4>
                <button type="button" class="close" data-dismiss="modal" style="margin-top: -10px;">&times;</button>
            </div>

            <form class="form-horizontal" method="post" action="#" id="product_submit">
                <div class="modal-body" style="padding: 30px;">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group" style="margin-bottom: 20px;">
                                <label class="control-label" style="margin-bottom: 8px; font-weight: 600;">Item Code <span class="required-star">*</span></label>
                                <input type="text" class="form-control" name="code" id="code" required placeholder="Enter unique item code" style="padding: 8px 12px;">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group" style="margin-bottom: 20px;">
                                <label class="control-label" style="margin-bottom: 8px; font-weight: 600;">Item Name <span class="required-star">*</span></label>
                                <input type="text" class="form-control" name="item_name_display" id="item_name_display" required placeholder="Enter item name" style="padding: 8px 12px;">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group" style="margin-bottom: 20px;">
                                <label class="control-label" style="margin-bottom: 8px; font-weight: 600;">HSN/SAC Code <span class="required-star">*</span></label>
                                <input type="number" min="0" class="form-control" name="hsn" id="hsn" required placeholder="Enter HSN/SAC code" style="padding: 8px 12px;">
                            </div>
                        </div>
                    </div>
 
                    <!-- Description with CKEditor -->
                    <div class="form-group" style="margin-bottom: 25px;">
                        <label class="control-label" style="margin-bottom: 10px; font-weight: 600;">Description</label>
                        <textarea class="form-control" name="prod_description" id="prod_description" rows="4" placeholder="Enter item description" style="padding: 10px 12px; resize: vertical;"></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group" style="margin-bottom: 20px;">
                                <label class="control-label" style="margin-bottom: 8px; font-weight: 600;">Category </label>
                                <select class="form-control" name="category_id" id="category_id" style="padding: 8px 12px;">
                                    <option value="">Select Category</option>
                                    <?php if (!empty($category_result)): ?>
                                        <?php foreach ($category_result as $c): ?>
                                            <option value="<?= $c->category_id; ?>"><?= $c->category_name; ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group" style="margin-bottom: 20px;">
                                <label class="control-label" style="margin-bottom: 8px; font-weight: 600;">Group </label>
                                <select class="form-control" name="group_id" id="group_id" style="padding: 8px 12px;">
                                    <option value="">Select Group</option>
                                    <?php if (!empty($group_result)): ?>
                                        <?php foreach ($group_result as $g): ?>
                                            <option value="<?= $g->group_id; ?>"><?= $g->group_name; ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group" style="margin-bottom: 20px;">
                                <label class="control-label" style="margin-bottom: 8px; font-weight: 600;">Company Name </label>
                                <input type="text" class="form-control" name="company_name" id="modal_company_name" placeholder="Enter company name" style="padding: 8px 12px;">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group" style="margin-bottom: 20px;">
                                <label class="control-label" style="margin-bottom: 8px; font-weight: 600;">Unit <span class="required-star">*</span></label>
                                <select class="form-control" name="unit" id="unit" required style="padding: 8px 12px;">
                                    <option value="">Select Unit</option>
                                    <?php if (!empty($unit_result)): ?>
                                        <?php foreach ($unit_result as $key): ?>
                                            <option value="<?php echo $key->unit; ?>"><?php echo $key->unit; ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group" style="margin-bottom: 20px;">
                                <label class="control-label" style="margin-bottom: 8px; font-weight: 600;">GST(%) <span class="required-star">*</span></label>
                                <select class="form-control" name="gst_per" id="gst_per" required style="padding: 8px 12px;">
                                    <option value="">Select GST</option>
                                    <?php if (!empty($gst_class)): ?>
                                        <?php foreach ($gst_class as $key): ?>
                                            <?php
                                            $gst_value = is_array($key) ? $key['gst_class'] : $key;
                                            ?>
                                            <option value="<?php echo $gst_value; ?>"><?php echo $gst_value; ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group" style="margin-bottom: 20px;">
                                <label class="control-label" style="margin-bottom: 8px; font-weight: 600;">Item Type</label>
                                <select class="form-control" name="item_type" id="item_type" style="padding: 8px 12px;">
                                    <option value="">Select type</option>
                                    <option value="B">Boughtout</option>
                                    <option value="M">Manufacturing</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group" style="margin-bottom: 20px;">
                                <label class="control-label" style="margin-bottom: 8px; font-weight: 600;">Cost Price (₹) <span class="required-star">*</span></label>
                                <input type="number" min="0" step="0.01" class="form-control" name="cost_price" id="cost_price" required placeholder="0.00" style="padding: 8px 12px;">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group" style="margin-bottom: 20px;">
                                <label class="control-label" style="margin-bottom: 8px; font-weight: 600;">Sell Price (₹) </label>
                                <input type="number" min="0" step="0.01" class="form-control" name="sell_price" id="sell_price" placeholder="0.00" style="padding: 8px 12px;">
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="item_name" id="item_name" value="">
                </div>
                <div class="modal-footer" style="padding: 20px 30px; border-top: 1px solid #e5e5e5;">
                    <button type="button" class="btn btn-default" data-dismiss="modal" style="padding: 8px 20px; margin-right: 10px;"><i class="fa fa-times"></i> Cancel</button>
                    <button type="submit" class="btn btn-success" style="padding: 8px 20px;"><i class="fa fa-check"></i> Add Item</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- DataTables JS -->
<!-- <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap.min.js"></script> -->

<!-- Select2 JS -->
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script> -->


<!-- Custom JS -->
<script>
// Make base_url available globally

$(document).ready(function() {

    // Dynamic sticky positioning for dropdown menus on tables & scroll containers
    $(document).on('show.bs.dropdown', function(e) {
        var $dropdown = $(e.target);
        var $toggle = $dropdown.find('[data-toggle="dropdown"]');
        var $menu = $dropdown.find('.dropdown-menu');

        if ($toggle.length && $menu.length) {
            function positionTableDropdown() {
                if (!$dropdown.hasClass('open') && !$menu.is(':visible')) return;

                var offset = $toggle.offset();
                if (!offset) return;

                var btnHeight = $toggle.outerHeight();
                var btnWidth = $toggle.outerWidth();
                var menuWidth = $menu.outerWidth();
                var menuHeight = $menu.outerHeight();
                var scrollTop = $(window).scrollTop();
                var scrollLeft = $(window).scrollLeft();

                // Close if button is scrolled out of viewport
                if (offset.top < scrollTop - 60 || offset.top > scrollTop + $(window).height() + 60) {
                    $dropdown.removeClass('open');
                    return;
                }

                var top = offset.top - scrollTop + btnHeight;
                var left = offset.left - scrollLeft;

                // Adjust for right aligned dropdowns
                if ($menu.hasClass('pull-right') || $menu.hasClass('dropdown-menu-right')) {
                    left = (offset.left - scrollLeft + btnWidth) - menuWidth;
                }

                // Flip above button if it overflows bottom of screen
                if (top + menuHeight > $(window).height() && (offset.top - scrollTop - menuHeight) > 0) {
                    top = offset.top - scrollTop - menuHeight;
                }

                $menu.css({
                    'position': 'fixed',
                    'top': top + 'px',
                    'left': left + 'px',
                    'bottom': 'auto',
                    'right': 'auto',
                    'z-index': 99999,
                    'margin': 0
                });
            }

            setTimeout(positionTableDropdown, 0);

            $(window).off('.dropdownStick').on('scroll.dropdownStick resize.dropdownStick', positionTableDropdown);
            $('.table-responsive, .table-responsive-container, .box-body, .dataTables_wrapper, .content-wrapper, body').off('.dropdownStick').on('scroll.dropdownStick', positionTableDropdown);
        }
    });

    $(document).on('hide.bs.dropdown', function(e) {
        $(window).off('.dropdownStick');
        $('.table-responsive, .table-responsive-container, .box-body, .dataTables_wrapper, .content-wrapper, body').off('.dropdownStick');
        $(e.target).find('.dropdown-menu').css({
            'position': '',
            'top': '',
            'left': '',
            'bottom': '',
            'right': '',
            'z-index': '',
            'margin': ''
        });
    });

    // Initialize CKEditor for description
    if (typeof CKEDITOR !== 'undefined') {
        CKEDITOR.replace('prod_description', {
            height: 75,
            toolbar: [
                { name: 'basicstyles', items: ['Bold', 'Italic', 'Underline', 'Strike'] },
                { name: 'paragraph', items: ['NumberedList', 'BulletedList', 'Blockquote'] },
                { name: 'links', items: ['Link', 'Unlink'] },
                { name: 'styles', items: ['Format', 'Font', 'FontSize'] },
                { name: 'colors', items: ['TextColor', 'BGColor'] },
                { name: 'tools', items: ['Maximize'] }
            ]
        });
    }
    
    // Initialize Select2 only on actual select controls, not on generated container spans
    $('select.select2').select2();
    
    // Initialize DataTables if table exists (skip already-initialized tables)
    if ($.fn.DataTable) {
        // Global DataTables Defaults for ALL tables in the application
        $.extend(true, $.fn.dataTable.defaults, {
            "dom": '<"top"<"pull-left"l><"pull-right"f>><"table-responsive-container"t><"bottom"<"pull-left"i><"pull-right"p>><"clear">',
            "autoWidth": false,
            "pageLength": 25
        });

        // Automatically assign sticky-action-col to Action/Actions columns across all tables on load & redraw
        function applyStickyActionColumns($table) {
            var $lastTh = $table.find('thead th:last-child');
            var headerText = $.trim($lastTh.text()).toLowerCase();
            if (headerText === 'action' || headerText === 'actions' || headerText === 'option' || headerText === 'options' || $lastTh.hasClass('sticky-action-col')) {
                $lastTh.addClass('sticky-action-col');
                $table.find('tbody tr').each(function() {
                    $(this).find('td:last-child').addClass('sticky-action-col');
                });
                $table.find('tfoot tr').each(function() {
                    $(this).find('th:last-child, td:last-child').addClass('sticky-action-col');
                });
            }
        }

        $('table.table').each(function() {
            applyStickyActionColumns($(this));
        });

        $(document).on('draw.dt', function(e, settings) {
            if (settings && settings.nTable) {
                applyStickyActionColumns($(settings.nTable));
            }
        });

        $('.table:not(#dynamic_field):not(#so_reference_items_table):not(#drawings_table):not(#pending_approvals_table)').each(function() {
            if ($(this).find('thead').length > 0 && !$(this).hasClass('no-datatable') && !$(this).hasClass('table-details') && !$.fn.DataTable.isDataTable(this)) {
                var searchLabel = "Search:";
                var $boxTitleEl = $(this).closest('.box').find('.box-title');
                var boxTitle = "";
                if ($boxTitleEl.length > 0) {
                    var $boxTitleClone = $boxTitleEl.clone();
                    $boxTitleClone.find('span, i, .label, .badge, button').remove();
                    boxTitle = $boxTitleClone.text().trim();
                }
                if (!boxTitle) {
                    var $headerEl = $('.content-header h1');
                    if ($headerEl.length > 0) {
                        var $headerClone = $headerEl.clone();
                        $headerClone.find('span, i, small, .label, .badge, button').remove();
                        boxTitle = $headerClone.text().trim();
                    }
                }
                if (boxTitle) {
                    // Clean up title (remove icons/extra whitespace)
                    var cleanTitle = boxTitle.replace(/[\r\n\t]+/g, ' ').replace(/\s+/g, ' ')
                        .replace(/^(Select a|List of|Manage|View|Add New)\s+/i, '')
                        .replace(/\s+(Details|List|Table|Master|Records|History|Registry|Planning)$/i, '');
                    if (cleanTitle) {
                        searchLabel = "Search " + cleanTitle + ":";
                    }
                }
                $(this).DataTable({
                    "language": {
                        "search": searchLabel
                    }
                });
            }
        });
    }
    
    // Auto-dismiss alerts
    setTimeout(function() {
        $('.alert').fadeOut(500, function() {
            $(this).remove();
        });
    }, 2000);
    
    // Manual alert close
    $('.alert').on('click', '.close', function() {
        $(this).closest('.alert').fadeOut(500);
    });
    
  

    // Reset modal form when hidden
    $('#productModal').on('hidden.bs.modal', function(){
        // Reset form
        $(this).find('form')[0].reset();
        
        // Reset CKEditor
        if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances.prod_description) {
            CKEDITOR.instances.prod_description.setData('');
        }
        
        // Remove modal classes from body (fix scrolling)
        $('body').removeClass('modal-open');
        $('body').css({
            'overflow': '',
            'padding-right': ''
        });
        
        // Remove modal backdrop
        $('.modal-backdrop').remove();
        
        // Clear the stored ID
        window.item_id_new = null;
    });

    // Prevent body scrolling when modal is open
    $('#productModal').on('show.bs.modal', function() {
        $('body').css({
            'overflow': 'hidden',
            'position': 'relative'
        });
    });
    
});
</script>

<style>
.main-footer {
    background: #fff;
    padding: 15px;
    color: #444;
    border-top: 1px solid #d2d6de;
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    width: 100%;
    z-index: 1000;
    font-size: 13px;
}

body {
    margin-bottom: 80px;
    min-height: 100vh;
}

/* Fix modal scrolling - prevent background scroll */
body.modal-open {
    overflow: hidden !important;
    position: relative;
    width: 100%;
    height: 100%;
}

.modal {
    overflow-y: auto !important;
    -webkit-overflow-scrolling: touch;
}

.modal-open .modal {
    overflow-x: hidden;
    overflow-y: auto;
}

.modal-dialog {
    margin: 30px auto;
    max-height: calc(100vh - 60px);
}

.modal-content {
    max-height: calc(100vh - 60px);
    overflow-y: auto;
    box-shadow: 0 5px 15px rgba(0,0,0,0.5);
}

/* Sticky modal header and footer */
.modal-header {
    background-color: #f8f9fa;
    border-bottom: 2px solid #007bff;
    position: sticky;
    top: 0;
    z-index: 10;
    border-radius: 4px 4px 0 0;
}

.modal-footer {
    position: sticky;
    bottom: 0;
    background-color: #f8f9fa;
    border-top: 1px solid #dee2e6;
    z-index: 10;
}

.required-star {
    color: red;
    font-weight: bold;
}

.enhanced-card {
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.modal-title {
    color: #007bff;
    font-weight: 600;
}

/* CKEditor fixes */
.cke_chrome {
    width: 100% !important;
    border: 1px solid #d2d6de !important;
    border-radius: 4px !important;
}

.cke_inner {
    border-radius: 4px !important;
}

.cke_top {
    background: #f8f9fa !important;
    border-bottom: 1px solid #d2d6de !important;
    border-radius: 4px 4px 0 0 !important;
}

/* Responsive */
@media (max-width: 767px) {
    .main-footer {
        position: relative;
    }
    body {
        margin-bottom: 0;
    }
    .modal-dialog {
        margin: 10px;
        max-height: calc(100vh - 40px);
    }
    .modal-content {
        max-height: calc(100vh - 40px);
    }
}
</style>

<!-- Add Vendor Modal -->
<div id="addVendorModal" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header btn-primary">
                <center><h4 class="modal-title">Add Vendor <button type="button" class="close" data-dismiss="modal">&times;</button></h4></center>
            </div>
            <div class="modal-body">
                <div class="form-group row">
                    <label class="col-sm-4 control-label">Vendor Code</label>
                    <div class="col-sm-7">
                        <input type="text" class="form-control input-sm" id="av_s_code" name="av_s_code" placeholder="Auto-generated" readonly style="background:#f5f5f5;">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-4 control-label">Company Name <span style="color:red;">*</span></label>
                    <div class="col-sm-7">
                        <input type="text" class="form-control input-sm" id="av_company_name" name="av_company_name" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-4 control-label">Customer Name</label>
                    <div class="col-sm-7">
                        <input type="text" class="form-control input-sm" id="av_fullname" name="av_fullname">
                    </div>
                </div>

                 <div class="form-group row">
                    <label class="col-sm-4 control-label">GST No</label>
                    <div class="col-sm-7">
                        <input type="text" maxlength="15" class="form-control input-sm" id="av_gst" name="av_gst" style="text-transform:uppercase;">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-4 control-label">PAN No</label>
                    <div class="col-sm-7">
                        <input type="text" maxlength="10" class="form-control input-sm" id="av_pancard" name="av_pancard" style="text-transform:uppercase;">
                    </div>
                </div>
               

                 <div class="form-group row">
                    <label class="col-sm-4 control-label">State Code</label>
                    <div class="col-sm-7">
                        <input type="text" class="form-control input-sm" id="av_state_code" name="av_state_code">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-4 control-label">Email</label>
                    <div class="col-sm-7">
                        <input type="email" class="form-control input-sm" id="av_email" name="av_email">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-4 control-label">Mobile</label>
                    <div class="col-sm-7">
                        <input type="tel" class="form-control input-sm" id="av_mobile" name="av_mobile" maxlength="10"
                               onkeyup="if(/\D/g.test(this.value)) this.value=this.value.replace(/\D/g,'');">
                    </div>
                </div>
               
                <div class="form-group row">
                    <label class="col-sm-4 control-label">Address</label>
                    <div class="col-sm-7">
                        <textarea class="form-control input-sm" id="av_address" name="av_address" rows="3"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnSaveVendor" class="btn btn-success"><i class="fa fa-check"></i> Save Vendor</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="glyphicon glyphicon-remove"></i> Close</button>
            </div>
        </div>
    </div>
</div>

<script>
// Add Vendor Modal — load next s_code when modal opens
$('#addVendorModal').on('show.bs.modal', function () {
    // Try primary URL format first
    var urlFormats = [
        base_url + 'index.php/SupplierController/get_next_vendor_code',
        base_url + 'SupplierController/get_next_vendor_code'
    ];
    
    function tryNextUrl(index) {
        if (index >= urlFormats.length) {
            $('#av_s_code').val('ERROR - Unable to reach server');
            console.error('All URL formats failed for get_next_vendor_code');
            alert('Unable to generate vendor code. Please check your connection and try again.');
            return;
        }
        
        var url = urlFormats[index];
        console.log('Attempting URL:', url);
        
        $.ajax({
            type: 'GET',
            url: url,
            dataType: 'text',
            timeout: 5000,
            success: function (data) {
                var vendorCode = $.trim(data);
                console.log('Vendor code response:', vendorCode);
                
                if (vendorCode && !isNaN(vendorCode) && vendorCode !== 'ERROR') {
                    $('#av_s_code').val(vendorCode);
                    console.log('Vendor code set successfully:', vendorCode);
                } else {
                    console.warn('Invalid vendor code received:', vendorCode);
                    tryNextUrl(index + 1);
                }
            },
            error: function (xhr, status, error) {
                console.warn('Failed URL format ' + index + ':', url, status, error);
                console.log('Response text:', xhr.responseText);
                tryNextUrl(index + 1);
            }
        });
    }
    
    tryNextUrl(0);
});

// Reset modal fields on close
$('#addVendorModal').on('hidden.bs.modal', function () {
    $(this).find('#av_company_name, #av_fullname, #av_pancard, #av_gst, #av_email, #av_mobile, #av_state_code, #av_address').val('');
    $('#av_s_code').val('');
    $('body').removeClass('modal-open');
    $('body').css({'overflow': '', 'padding-right': ''});
    $('.modal-backdrop').remove();
});

$(document).on('click', '#btnSaveVendor', function () {
    var company_name = $.trim($('#av_company_name').val());
    if (!company_name) {
        alert('Please enter Company Name');
        return;
    }
    $.ajax({
        type: 'POST',
        url: base_url + 'SupplierController/add_vendor_ajax',
        data: {
            company_name : company_name,
            fullname     : $('#av_fullname').val(),
            pancard      : $('#av_pancard').val(),
            gst          : $('#av_gst').val(),
            email        : $('#av_email').val(),
            mobile       : $('#av_mobile').val(),
            state_code   : $('#av_state_code').val(),
            address      : $('#av_address').val()
        },
        cache: false,
        dataType: 'json',
        success: function (data) {
            console.log('Save vendor response:', data);
            
            // Check for success property first (new response format)
            if (data.success === true || data.save_vendor === true) {
                var message = data.message || 'Vendor added successfully!';
                alert(message);
                
                if ($('#supplier_id').length) {
                    var $supplierSelect = $('#supplier_id');
                    var selectedSupplierId = '';

                    // Refresh supplier_id dropdown
                    $supplierSelect.find('option').remove().end()
                        .append('<option value="">Select Company</option>');
                    
                    if (data.vendors && Array.isArray(data.vendors)) {
                        $.each(data.vendors, function (i, v) {
                            var opt = $('<option>').val(v.supplier_id).text(v.company_name + ' - ' + v.s_code);
                            $supplierSelect.append(opt);
                            if (v.company_name === company_name) {
                                selectedSupplierId = v.supplier_id;
                            }
                        });
                    }

                    if (selectedSupplierId) {
                        $supplierSelect.val(selectedSupplierId);
                    }

                    $supplierSelect.trigger('change');
                    if ($supplierSelect.hasClass('select2-hidden-accessible')) {
                        $supplierSelect.trigger('change.select2');
                    }

                    $('#addVendorModal').modal('hide');
                } else {
                    // Listing page — reload to show new vendor in table
                    $('#addVendorModal').modal('hide');
                    setTimeout(function() {
                        location.reload();
                    }, 500);
                }
            } else {
                // Handle failure response
                var message = data.message || 'Vendor already exists!';
                alert(message);
            }
        },
        error: function (xhr, status, error) {
            console.error('AJAX Error:', status, error);
            console.log('Response text:', xhr.responseText);
            console.log('Status code:', xhr.status);
            
            var errorMsg = 'An error occurred while saving the vendor.';
            
            if (xhr.status === 0) {
                errorMsg = 'Network error. Please check your connection.';
            } else if (xhr.status === 404) {
                errorMsg = 'Server endpoint not found (404). Please check the URL configuration.';
            } else if (xhr.status === 500) {
                errorMsg = 'Server error (500). Please contact your administrator.';
            } else if (status === 'parsererror') {
                errorMsg = 'Response parse error. Server may have returned invalid data.';
                console.log('Attempted to parse:', xhr.responseText);
            } else if (status === 'timeout') {
                errorMsg = 'Request timeout. Server took too long to respond.';
            }
            
            alert(errorMsg);
        }
    });
});

// Auto-fill PAN and State Code from GST Number in Add Vendor Modal
$(document).ready(function() {
    $(document).on('blur change', '#av_gst', function() {
        var gstNo = $(this).val().trim().toUpperCase();
        
        if (gstNo.length === 0) {
            $('#av_pancard').val('');
            $('#av_state_code').val('');
            return;
        }
        
        if (gstNo.length !== 15) {
            alert('GST No must be 15 characters long. Example: 27AAPFU0205R1Z0');
            $(this).val('');
            $('#av_pancard').val('');
            $('#av_state_code').val('');
            $(this).focus();
            return;
        }
        
        // Strict (official): last char must be digit
        // var gstRegex = /^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[0-9]{1}[A-Z]{1}[A-Z0-9]{1}$/;
        
        // Relaxed (allow letter at end) – uncomment if needed
        var gstRegex = /^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[0-9]{1}[A-Z]{1}[A-Z0-9]{1}$/;
        
        if (!gstRegex.test(gstNo)) {
            alert('Invalid GST format. Expected: 2 digits + PAN + 1 digit + 1 letter + 1 digit\nExample: 27AAPFU0205R1Z0');
            $(this).val('');
            $('#av_pancard').val('');
            $('#av_state_code').val('');
            $(this).focus();
            return;
        }
        
        var panNo = gstNo.substring(2, 12);
        $('#av_pancard').val(panNo);
        
        var stateCode = gstNo.substring(0, 2);
        $('#av_state_code').val(stateCode);
    });
});
</script>

</body>
</html>
