<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (isset($session_data_head1)) {
    
} else {
    header($this->config->item('header'));
}
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<style>
    .joborder-logo-left {
        text-align: left;
    }

    .joborder-logo-left img {
        display: inline-block;
        margin-top: 60px;
    }

    .joborder-grid-table {
        border-collapse: collapse;
        border: 2px solid #2f2f2f;
        background-color: #fff;
    }

    .joborder-grid-table > tbody > tr > th,
    .joborder-grid-table > tbody > tr > td,
    .joborder-grid-table > thead > tr > th,
    .joborder-grid-table > thead > tr > td {
        border: 1.5px solid #2f2f2f !important;
        color: #111;
        vertical-align: middle;
    }

    .joborder-grid-table > tbody > tr > th,
    .joborder-grid-table > thead > tr > th {
        background-color: #f3f3f3;
    }
    
    /* Print button spacing */
    .btn-group-custom {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        align-items: center;
    }
</style>
<body class="hold-transition skin-blue sidebar-mini">
     <div id="loader" class="center"></div> 
    <div class="wrapper">

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="<?php echo base_url() . 'JobOrderController/index/' ?>">JOB ORDER</a></li>
                    <li class="active">Job Order Details</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <label for="inputEmail3" name="number" id="number" class="col-sm-12 control-label"><h2>Job Order: <?php echo isset($joborder_data_group['number']) ? $joborder_data_group['number'] : ''; ?></h2></label>

                        <div class="row" style="padding:2%; margin-left:0; margin-right:0;">
                            <div class="col-xs-6" style="padding-left:0;">
                                <div class="btn-group-custom" role="group">
                                    <!-- Edit Button -->
                                    <a href="<?php echo base_url(); ?>JobOrderController/edit_joborder_details/<?php echo isset($joborder_data_group['id']) ? $joborder_data_group['id'] : ''; ?>" class="btn btn-primary" role="button">Edit</a>
                                    
                                    <!-- Print Button (opens PDF in new tab) -->
                                    <a href="<?php echo base_url(); ?>Pdf/print_joborder/<?php echo isset($joborder_data_group['id']) ? $joborder_data_group['id'] : ''; ?>" target="_blank" class="btn btn-primary" role="button">Print</a>
                                    
                                    <!-- More Dropdown -->
                                    <div class="dropdown" style="display:inline-block;">
                                        <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            More <b class="caret"></b>
                                        </button>
                                        <input type="hidden" class="form-control input-sm" name="number" id="number" required="" value="<?php echo isset($joborder_data_group['number']) ? $joborder_data_group['number'] : ''; ?>">  
                                        <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                                            <li><a class="dropdown-item" id="exportpdf" href="<?php echo base_url(); ?>Pdf/print_joborder/<?php echo isset($joborder_data_group['id']) ? $joborder_data_group['id'] : ''; ?>">Export As PDF</a></li>
                                            <li><a class="dropdown-item" id="exportexcel" href="<?php echo base_url(); ?>JobOrderController/export_joborder_excel/<?php echo isset($joborder_data_group['id']) ? $joborder_data_group['id'] : ''; ?>">Export As Excel</a></li>            
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-xs-6 text-right" style="padding-right:0;">
                                <a href="javascript:void(0);" onclick="window.history.back()" class="btn btn-primary"><i class="fa fa-close"></i> Close</a>
                            </div>
                        </div>
                        
                        <div class="shadows1">
                            <div class="row">
                                <section class="contemporary-template__header">
                                    <div class="col-md-6">
                                        <div class="contemporary-template__header__logo joborder-logo-left">
                                            <img class="contemporary-template__business-logo" src="<?php echo base_url() . (isset($settings['company_logo']) ? $settings['company_logo'] : ''); ?>" width="70%" height="35%">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="contemporary-template__header__info">
                                            
                                            <div class="wv-heading--title"><h1>Job Order</h1></div>
                                            <div class="contemporary-template__header__logo">
                                        </div>
                                            <span class="wv-text--strong"><b><?php echo isset($settings['company_name']) ? $settings['company_name'] : ''; ?></b></span><br>
                                            <span class="wv-text--strong"><b>GST No :</b> <?php echo isset($settings['company_gst']) ? $settings['company_gst'] : ''; ?></span><br>
                                            <span class="wv-text--strong"><b>PAN No :</b> <?php echo isset($settings['company_pan']) ? $settings['company_pan'] : ''; ?></span><br>
                                            <span class="wv-text--strong"><b>Mobile Number :</b> <?php echo isset($settings['mobile']) ? $settings['mobile'] : ''; ?></span><br>
                                            <span class="wv-text--strong"><b>Email ID :</b> <?php echo isset($settings['email']) ? $settings['email'] : ''; ?></span><br>
                                            <span class="wv-text--strong"><b>Address :</b> <?php echo isset($settings['address']) ? $settings['address'] : ''; ?></span>
                                        </div>
                                    </div>
                                </section>
                            </div>
                            <hr>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="contemporary-template__header__info">
                                         <div class="wv-heading--subtitle"><b>Customer Details :</b></div><br>
                                        <span class="wv-text--strong"><b>Customer Code :</b> <?php echo isset($joborder_data_group['customer_code']) ? $joborder_data_group['customer_code'] : 'N/A'; ?></span><br>      
                                        
                                        <span class="wv-text--strong"><b>Project Quantity :</b> <?php echo isset($joborder_data_group['project_qty']) ? $joborder_data_group['project_qty'] : 'N/A'; ?></span><br>
                                        <span class="wv-text--strong"><b>SO Number :</b> <?php echo isset($joborder_data_group['oc_number']) ? $joborder_data_group['oc_number'] : 'N/A'; ?></span><br>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="contemporary-template__header__info">
                                        <div class="wv-heading--subtitle"><b>Job Order Details :</b></div>
                                        <br>
                                        <span class="wv-text--strong"><b>Job Order Number :</b> <?php echo isset($joborder_data_group['number']) ? $joborder_data_group['number'] : ''; ?></span><br>
                                        <span class="wv-text--strong"><b>Job Order Date :</b> <?php echo isset($joborder_data_group['date']) && $joborder_data_group['date'] != '0000-00-00' ? date('d-m-Y', strtotime($joborder_data_group['date'])) : 'N/A'; ?></span><br>
                                        <span class="wv-text--strong"><b>Job Order Status :</b> 
                                            <?php
                                            $statusArr = array(1 => 'Draft', 2 => 'Sent', 3 => 'Viewed', 4 => 'Approved', 5 => 'Rejected', 6 => 'Canceled');
                                            echo (isset($joborder_data_group['status']) && isset($statusArr[$joborder_data_group['status']])) ? $statusArr[$joborder_data_group['status']] : 'Draft';
                                            ?>
                                        </span><br>
                                        <span class="wv-text--strong"><b>Company Name :</b> <?php echo isset($joborder_data_group['company_name']) ? $joborder_data_group['company_name'] : 'N/A'; ?></span><br>
                                        <span class="wv-text--strong"><b>Prepared By :</b> <?php echo isset($joborder_data_group['prepare_by']) ? $joborder_data_group['prepare_by'] : 'N/A'; ?></span><br>
                                        <span class="wv-text--strong"><b>Approved By :</b> <?php echo isset($joborder_data_group['approved_by']) ? $joborder_data_group['approved_by'] : 'N/A'; ?></span><br>
                                    </div>
                                </div>
                            </div>
                            <?php if (!empty($so_reference_items) || !empty($drawings)): ?>
                                <!-- Linked Sales Order Items Reference Card -->
                                <div class="row" id="so_items_reference_container" style="margin-bottom: 20px;">
                                    <div class="col-xs-12">
                                        <div class="so-ref-card" style="background: rgba(23, 162, 184, 0.05); border: 1px solid rgba(23, 162, 184, 0.2); border-left: 5px solid #17a2b8; border-radius: 8px; padding: 15px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05); transition: all 0.3s ease;">
                                            <!-- SO Items header & list -->
                                            <?php if (!empty($so_reference_items)): ?>
                                                <div style="display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid rgba(23, 162, 184, 0.1); padding-bottom: 8px; margin-bottom: 12px;">
                                                    <h4 style="margin: 0; color: #17a2b8; font-size: 15px; font-weight: 600; display: flex; align-items: center; gap: 8px;">
                                                        <i class="fa fa-shopping-cart" style="font-size: 16px;"></i>
                                                        Linked Sales Order Items <span style="font-size: 11px; font-weight: normal; color: #6c757d; margin-left: 5px;">(Reference Finished Goods)</span>
                                                    </h4>
                                                    <span class="label label-info" id="so_ref_badge" style="border-radius: 12px; padding: 4px 8px; font-size: 11px;"><?php echo count($so_reference_items); ?> Items</span>
                                                </div>
                                                <div class="so-ref-body" style="display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 10px;" id="so_items_list">
                                                    <?php foreach ($so_reference_items as $item): ?>
                                                        <?php $unitStr = !empty($item->unit) ? ' ' . htmlspecialchars($item->unit) : ''; ?>
                                                        <div style="background: #fff; border: 1px solid #e9ecef; border-radius: 6px; padding: 6px 12px; display: inline-flex; align-items: center; gap: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.02); margin-right: 5px; margin-bottom: 5px;">
                                                            <span style="color: #495057; font-weight: 500;"><?php echo htmlspecialchars($item->item_name ?? $item->product_name ?? $item->product_code ?? ''); ?></span>
                                                            <span class="label label-default" style="background: #e9ecef; color: #495057; border-radius: 4px; padding: 2px 6px; font-size: 11px; font-weight: 600;">Qty: <?php echo htmlspecialchars($item->quantity ?? 0) . $unitStr; ?></span>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php endif; ?>

                                            <!-- Drawings header & list -->
                                            <?php if (!empty($drawings)): ?>
                                                <div style="display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid rgba(23, 162, 184, 0.1); padding-bottom: 8px; margin-bottom: 12px; margin-top: 15px;">
                                                    <h4 style="margin: 0; color: #17a2b8; font-size: 15px; font-weight: 600; display: flex; align-items: center; gap: 8px;">
                                                        <i class="fa fa-file-picture-o" style="font-size: 16px;"></i>
                                                        Linked Drawings <span style="font-size: 11px; font-weight: normal; color: #6c757d; margin-left: 5px;">(Latest Revisions)</span>
                                                    </h4>
                                                    <span class="label label-info" id="drawings_ref_badge" style="border-radius: 12px; padding: 4px 8px; font-size: 11px;"><?php echo count($drawings); ?> Drawings</span>
                                                </div>
                                                <div class="so-ref-body" style="display: flex; flex-wrap: wrap; gap: 10px;" id="drawings_ref_list">
                                                    <?php foreach ($drawings as $d): ?>
                                                        <?php $revStr = !empty($d['latest_revision']) ? ' Rev: ' . htmlspecialchars($d['latest_revision']) : ''; ?>
                                                        
                                                        <?php
                                                        $filesHtml = '';
                                                        if (!empty($d['files'])) {
                                                            $filesHtml .= '<div style="display: flex; align-items: center; gap: 4px; border-left: 1px solid #ddd; padding-left: 10px; margin-left: 5px;">';
                                                            foreach ($d['files'] as $f) {
                                                                $downloadUrl = base_url() . 'DrawingController/download_file/' . $f['file_id'];
                                                                $viewUrl = base_url() . 'DrawingController/view_file/' . $f['file_id'];
                                                                $fName = htmlspecialchars($f['file_name']);
                                                                
                                                                $filesHtml .= '<span style="font-size: 12px; color: #6c757d; max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="' . $fName . '">' . $fName . '</span> ';
                                                                $filesHtml .= '<a href="' . $viewUrl . '" class="btn btn-info btn-xs" target="_blank" style="padding: 1px 4px; font-size: 10px; margin-left: 4px;"><i class="fa fa-eye"></i> View</a>';
                                                                $filesHtml .= '<a href="' . $downloadUrl . '" class="btn btn-default btn-xs" target="_blank" style="padding: 1px 4px; font-size: 10px; margin-left: 2px;"><i class="fa fa-download"></i></a>';
                                                            }
                                                            $filesHtml .= '</div>';
                                                        }
                                                        ?>
                                                        <div style="background: #fff; border: 1px solid #e9ecef; border-radius: 6px; padding: 6px 12px; display: inline-flex; align-items: center; box-shadow: 0 2px 4px rgba(0,0,0,0.02); margin-right: 5px; margin-bottom: 5px;">
                                                            <span style="color: #495057; font-weight: 500;"><i class="fa fa-file-picture-o" style="color: #17a2b8; margin-right: 6px;"></i><?php echo htmlspecialchars($d['drawing_name'] ?? $d['drawing_no'] ?? ''); ?></span>
                                                            <span class="label label-default" style="background: #e9ecef; color: #495057; border-radius: 4px; padding: 2px 6px; font-size: 11px; font-weight: 600; margin-left: 8px;"><?php echo htmlspecialchars($d['drawing_no']); ?><?php echo $revStr; ?></span>
                                                            <?php echo $filesHtml; ?>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <div class="table-responsive">  
                                <table class="table table-bordered joborder-grid-table" id="dynamic_field">  
                                    <tr>
                                        <th>Sr.No.</th>
                                        <th>Product Name</th>
                                         <th>Description</th>
                                          
                                        <th>QTY</th>
                                       <th>Unit</th>
                                        <th>Tag No.</th>
                                        <th>Scope</th>
                                        <th>Stores Remark (Y/N)</th>
                                        <th>Price</th>
                                        <th>Remark</th>

                                    </tr>
                                    <?php
                                    if(isset($show_joborder) && !empty($show_joborder)):
                                        $i = 1;
                                        foreach ($show_joborder as $key):
                                            // ---- Render Section Heading Row ----
                                            if (isset($key->product_name) && $key->product_name === '__HEADING__'):
                                                $desc = trim($key->description ?? '');
                                                $isMain = true;
                                                if (isset($key->tag_no) && ($key->tag_no === 'MAIN' || $key->tag_no === 'SUB')) {
                                                    $isMain = ($key->tag_no === 'MAIN');
                                                }
                                                $bg = $isMain ? '#e6e0ed' : '#fdeada';
                                                $fg = $isMain ? '#5a3d8a' : '#000000';
                                                $displayDesc = $isMain ? strtoupper($desc) : $desc;
                                    ?>
                                        <tr style="background-color: <?php echo $bg; ?> !important;">
                                            <td colspan="10" style="background-color: <?php echo $bg; ?> !important; color: <?php echo $fg; ?> !important; font-weight: bold; padding: 8px 12px; border: 1.5px solid #2f2f2f !important; vertical-align: middle;">
                                                <i class="fa fa-tag" style="color: <?php echo $fg; ?>; margin-right: 8px; opacity: 0.7;"></i>
                                                <strong><?php echo htmlspecialchars($displayDesc); ?></strong>
                                            </td>
                                        </tr>
                                    <?php
                                                continue;
                                            endif;
                                            // ---- End Heading Row ----
                                        ?>
                                        <tr> 
                                            <td><?php echo $i; ?></td>
                                            <td><?php echo isset($key->product_name) ? $key->product_name . " - " . $key->item_name : ''; ?></td>
                                            <td><?php echo isset($key->description) ? $key->description : ''; ?></td>
                                            <td><?php echo isset($key->quantity) ? $key->quantity : ''; ?></td>
                                            <td>
                                                <?php 
                                                if(isset($key->unit) && isset($unit_result)):
                                                    foreach ($unit_result as $unit): 
                                                        if($unit->unit == $key->unit): 
                                                            echo $unit->unit; 
                                                        endif; 
                                                    endforeach; 
                                                endif;
                                                ?>  
                                            </td>
                                            <td><?php echo isset($key->tag_no) ? $key->tag_no : ''; ?></td>
                                            <td><?php echo isset($key->scope) ? $key->scope : ''; ?></td>
                                            <td>
                                                <?php 
                                                if(isset($key->stores_remark)):
                                                    if($key->stores_remark == 'Y'):
                                                        echo 'Yes';
                                                    elseif($key->stores_remark == 'N'):
                                                        echo 'No';
                                                    endif;
                                                endif;
                                                ?>
                                            </td>
                                            <td><?php echo isset($key->price) ? $key->price : ''; ?></td>
                                            <td><?php echo isset($key->remark) ? $key->remark : ''; ?></td>
                                        </tr>   
                                        <?php
                                            $i++;
                                        endforeach;
                                    else:
                                        ?>
                                        <tr>
                                            <td colspan="10" class="text-center">No items found</td>
                                        </tr>
                                    <?php endif; ?>
                                </table>   
                                
                                <br>
                                <label class="control-label pull-left"><b>Notes</b></label><br>
                                <div class="col-lg-12">
                                    <pre><?php echo isset($joborder_data_group['note']) ? $joborder_data_group['note'] : ''; ?></pre>
                                </div>

                                <center style="font-size: 12px"><?php echo isset($settings['joborder_footer']) ? $settings['joborder_footer'] : ''; ?></center>
                                <center style="font-size: 10px">This is Computer Generated Job Order</center><br>
                            </div>  
                        </div>
                    </div>
                </div>
                <!-- /.row -->
            </section>
            <!-- /.content -->
        </div>
        <?php $this->load->view('admin/footer'); ?>
    </div>
    <!-- ./wrapper -->
</body>
</html>