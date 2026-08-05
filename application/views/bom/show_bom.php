<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (isset($session_data_head1)) {
    
} else {
    header($this->config->item('header'));
}
defined('BASEPATH') OR exit('No direct script access allowed');
$_has_project_master = isset($session_data_head1['permission']) && in_array('Projects', $session_data_head1['permission']);
?>
<body class="hold-transition skin-blue sidebar-mini">
<style>
    /* Section Heading Row in View BOM */
    .bom-heading-row td {
        background: linear-gradient(135deg, #e8e0f0 0%, #d4c8e8 100%) !important;
        color: #5a3d8a !important;
        font-weight: 700 !important;
        font-size: 13px !important;
        text-transform: uppercase;
        letter-spacing: 1px;
        padding: 8px 12px !important;
        border: 1px solid #b8a8d4 !important;
        vertical-align: middle !important;
    }
    .bom-heading-row .heading-icon {
        margin-right: 8px;
        opacity: 0.7;
    }
</style>
     <div id="loader" class="center"></div> 
    <div class="wrapper">

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="<?php echo base_url() . 'BomController/index/' ?>">BOM</a></li>
                    <li class="active">BOM Details</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <label for="inputEmail3" name="number" id="number" class="col-sm-12 control-label"><h2>BOM: <?php echo isset($bom_data_group['number']) ? $bom_data_group['number'] : ''; ?></h2></label>

                        <div class="row" style="padding:2%; margin-left:0; margin-right:0;">
    <div class="col-xs-6" style="padding-left:0;">
        <a href="<?php echo base_url(); ?>BomController/edit_bom_details/<?php echo isset($bom_data_group['id']) ? $bom_data_group['id'] : ''; ?>" class="btn btn-primary" role="button">Edit</a>
    </div>
    
    <div class="col-xs-6 text-right" style="padding-right:0;">
        <div class="dropdown" style="display:inline-block; margin-right:5px;">
            <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                More <b class="caret"></b>
            </button>
            <input type="hidden" class="form-control input-sm" name="number" id="number" required="" value="<?php echo isset($bom_data_group['number']) ? $bom_data_group['number'] : ''; ?>">  
            <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                <li><a class="dropdown-item" id="exportpdf" href="<?php echo base_url(); ?>Pdf/print_bom/<?php echo isset($bom_data_group['id']) ? $bom_data_group['id'] : ''; ?>">Export As PDF</a></li>
<li><a class="dropdown-item" id="exportpdf" href="<?php echo base_url(); ?>BomController/export_bom_excel/<?php echo isset($bom_data_group['id']) ? $bom_data_group['id'] : ''; ?>">Export As Excel</a></li>            </ul>
        </div>
        <a href="javascript:void(0);" onclick="window.history.back()" class="btn btn-primary"><i class="fa fa-close"></i> Close</a>
    </div>
</div>
                        
                        <div class="shadows1">
                            <div class="row">
                                <section class="contemporary-template__header">
                                    <div class="col-md-12 text-center" style="margin-bottom: 20px;">
                                        <img class="contemporary-template__business-logo" src="<?php echo base_url() . (isset($settings['company_logo']) ? $settings['company_logo'] : ''); ?>" style="max-height: 80px; width: auto; display: block; margin: 0 auto;" alt="Logo">
                                    </div>
                                    <div class="col-md-6">
                                        <div class="wv-heading--title"><h1>BOM</h1></div>
                                        <span class="wv-text--strong"><b><?php echo isset($settings['company_name']) ? $settings['company_name'] : ''; ?></b></span><br>
                                        <span class="wv-text--strong"><b>GST No :</b> <?php echo isset($settings['company_gst']) ? $settings['company_gst'] : ''; ?></span><br>
                                        <span class="wv-text--strong"><b>PAN No :</b> <?php echo isset($settings['company_pan']) ? $settings['company_pan'] : ''; ?></span>
                                    </div>
                                    <div class="col-md-6 text-right">
                                        <div class="contemporary-template__header__info" style="text-align: right; margin-top: 15px;">
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
                                         <?php if ($_has_project_master): ?>
                                         <span class="wv-text--strong"><b>Project Code :</b> <?php echo isset($bom_data_group['project_code']) ? $bom_data_group['project_code'] : 'N/A'; ?></span><br>
                                         <?php endif; ?>
                                        <span class="wv-text--strong"><b>Customer Code :</b> <?php echo isset($bom_data_group['customer_code']) ? $bom_data_group['customer_code'] : 'N/A'; ?></span><br>      
                                        <span class="wv-text--strong"><b>System :</b> <?php echo isset($bom_data_group['system']) ? $bom_data_group['system'] : 'N/A'; ?></span><br>
                                        <span class="wv-text--strong"><b>Location :</b> <?php echo isset($bom_data_group['location']) ? $bom_data_group['location'] : 'N/A'; ?></span><br>
                                        <span class="wv-text--strong"><b>Capacity :</b> <?php echo isset($bom_data_group['capacity']) ? $bom_data_group['capacity'] : 'N/A'; ?></span><br>
                                        <span class="wv-text--strong"><b>Project Quantity :</b> <?php echo isset($bom_data_group['project_qty']) ? $bom_data_group['project_qty'] : 'N/A'; ?></span><br>
                                        <span class="wv-text--strong"><b>OC Number :</b> <?php echo isset($bom_data_group['oc_number']) ? $bom_data_group['oc_number'] : 'N/A'; ?></span><br>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="contemporary-template__header__info">
                                        <div class="wv-heading--subtitle"><b>BOM Details :</b></div>
                                        <br>
                                        <span class="wv-text--strong"><b>BOM Number :</b> <?php echo isset($bom_data_group['number']) ? $bom_data_group['number'] : ''; ?></span><br>
                                        <span class="wv-text--strong"><b>BOM Date :</b> <?php echo isset($bom_data_group['date']) && $bom_data_group['date'] != '0000-00-00' ? date('d-m-Y', strtotime($bom_data_group['date'])) : 'N/A'; ?></span><br>
                                        <span class="wv-text--strong"><b>BOM Status :</b> 
                                            <?php
                                            $statusArr = array(1 => 'Draft', 2 => 'Sent', 3 => 'Viewed', 4 => 'Approved', 5 => 'Rejected', 6 => 'Canceled');
                                            echo (isset($bom_data_group['status']) && isset($statusArr[$bom_data_group['status']])) ? $statusArr[$bom_data_group['status']] : 'Draft';
                                            ?>
                                        </span><br>
                                        <span class="wv-text--strong"><b>Company Name :</b> <?php echo isset($bom_data_group['company_name']) ? $bom_data_group['company_name'] : 'N/A'; ?></span><br>
                                        <span class="wv-text--strong"><b>Prepared By :</b> <?php echo isset($bom_data_group['prepare_by']) ? $bom_data_group['prepare_by'] : 'N/A'; ?></span><br>
                                        <span class="wv-text--strong"><b>Approved By :</b> <?php echo isset($bom_data_group['approved_by']) ? $bom_data_group['approved_by'] : 'N/A'; ?></span><br>
                                    </div>
                                </div>
                            </div>
                            <br>
                            
                            <div class="table-responsive">  
                                <table class="table table-bordered" id="dynamic_field">  
                                     <tr>
                                         <th>Sr.No.</th>
                                         <th>Item Code</th>
                                         <th>Product Name</th>
                                         <th>Description</th>
                                         <th>QTY</th>
                                         <th>Unit</th>
                                         <th>Tag No.</th>
                                         <th>Scope</th>
                                         <th>Stores Remark (Y/N)</th>
                                         <th>Remark</th>
                                         <th>Status</th>
                                     </tr>
                                     <?php
                                     if(isset($show_bom) && !empty($show_bom)):
                                         $i = 1;
                                         foreach ($show_bom as $key):
                                             // ---- Section Heading Row ----
                                             if (isset($key->product_name) && $key->product_name === '__HEADING__'):
                                                 $desc = trim($key->description ?? '');
                                                 $isMain = null;
                                                 if (isset($key->tag_no) && ($key->tag_no === 'MAIN' || $key->tag_no === 'SUB')) {
                                                     $isMain = ($key->tag_no === 'MAIN');
                                                 } else {
                                                     if (preg_match('/SYSTEM|SPARES|COMMISSIONING|TANK FOR|EQUIPMENTS/i', $desc)) {
                                                         $isMain = true;
                                                     } elseif (preg_match('/PIPING|FITTINGS|VALVES|FLANGE|ELBOW|TEE|PIPE|CPVC|UPVC/i', $desc)) {
                                                         $isMain = false;
                                                     }
                                                 }
                                                 
                                                 if ($isMain === true) {
                                                     $bg = '#e6e0ed';
                                                     $fg = '#ff0000';
                                                     $displayDesc = strtoupper($desc);
                                                 } elseif ($isMain === false) {
                                                     $bg = '#fdeada';
                                                     $fg = '#000000';
                                                     $displayDesc = $desc;
                                                 } else {
                                                     $bg = '#dbeff4';
                                                     $fg = '#000000';
                                                     $displayDesc = $desc;
                                                 }
                                     ?>
                                         <tr style="background-color: <?php echo $bg; ?> !important;">
                                             <td colspan="11" style="background-color: <?php echo $bg; ?> !important; color: <?php echo $fg; ?> !important; font-weight: bold; padding: 8px 12px; border: 1px solid #ddd; vertical-align: middle;">
                                                 <i class="fa fa-tag heading-icon" style="color: <?php echo $fg; ?>; margin-right: 8px; opacity: 0.7;"></i>
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
                                             <td><?php echo isset($key->product_name) ? htmlspecialchars($key->product_name) : ''; ?></td>
                                             <td><?php echo isset($key->item_name) ? htmlspecialchars($key->item_name) : ''; ?></td>
                                             <td><?php echo isset($key->description) ? nl2br($key->description) : ''; ?></td>
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
                                             <td><?php echo isset($key->scope) ? nl2br($key->scope) : ''; ?></td>
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
                                             <td><?php echo isset($key->remark) ? nl2br($key->remark) : ''; ?></td>
                                             <td>
                                                  <?php 
                                                  $statusArr = array(1 => 'Draft', 2 => 'Sent', 3 => 'Viewed', 4 => 'Approved', 5 => 'Rejected', 6 => 'Canceled');
                                                  echo (isset($key->status_i) && isset($statusArr[$key->status_i])) ? $statusArr[$key->status_i] : '';
                                                  ?>
                                             </td>
                                        </tr>  
                                        <?php
                                            $i++;
                                        endforeach;
                                    else:
                                        ?>
                                        <tr>
                                            <td colspan="9" class="text-center">No items found</td>
                                        </tr>
                                    <?php endif; ?>
                                </table>   

                                <label class="control-label pull-left"><b>Notes</b></label><br>
                                <div class="col-lg-12">
                                    <pre><?php echo isset($bom_data_group['note']) ? $bom_data_group['note'] : ''; ?></pre>
                                </div>

                                <center style="font-size: 12px"><?php echo isset($settings['bom_footer']) ? $settings['bom_footer'] : ''; ?></center>
                                <center style="font-size: 10px">This is Computer Generated BOM</center><br>
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