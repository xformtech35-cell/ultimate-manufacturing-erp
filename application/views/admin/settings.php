<?php
$session_data_head1 = $this->session->userdata('session_data_head');
if (isset($session_data_head1)) {
} else {
    header($this->config->item('header'));
}
defined('BASEPATH') or exit('No direct script access allowed');
?>
<script src="<?php echo base_url(); ?>assets/js/ckeditor/ckeditor.js"></script>

<body class="hold-transition skin-blue sidebar-mini">
    <div id="loader" class="center"></div>
    <div class="wrapper">

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    Company Settings
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="#">Setting</a></li>
                    <li class="active">Setting Details</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-info">

                            <!-- /.box-header -->
                            <div class="box-body">

                                <form class="form-horizontal form_overlay" method="post" action="<?php echo base_url(); ?>LoginController/add_settings" enctype="multipart/form-data">
                                    <div class="card-body ">
                                        <div class="box-header">
                                            <h3 class="box-title">General settings</h3>
                                        </div>

                                        <div class="form-group row">
                                         
                                        </div>

                                        <!-- form start -->
                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-4 control-label"> Logo <span style="color: red">*</span></label>
                                            <div class="col-sm-7">
                                                <input type="hidden" value="<?php echo $settings['setting_id'] ?? ''; ?>" class="form-control input-sm" name="setting_id" id="setting_id">
                                                <input type="file" class="form-control input-sm" name="company_logo" id="company_logo">
                                                <?php 
                                                $current_logo_url = !empty($settings['company_logo']) ? base_url() . ltrim(str_replace('\\', '/', $settings['company_logo']), './') : '';
                                                if (!empty($current_logo_url)) { ?>
                                                    <div style="margin-top: 8px;">
                                                        <img src="<?php echo $current_logo_url; ?>" alt="Company Logo" style="max-width: 220px; max-height: 80px; object-fit: contain; border: 1px solid #ddd; padding: 4px; border-radius: 4px; background: #fff;">
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-4 control-label">Company Name <span style="color: red">*</span></label>
                                            <div class="col-sm-7">
                                                <input type="text" value="<?php echo $settings['company_name'] ?? ''; ?>" class="form-control input-sm" name="company_name" id="company_name" required="">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-4 control-label">Address <span style="color: red">*</span></label>
                                            <div class="col-sm-7">
                                                <textarea class="form-control" name="address" id="address" required="" rows="3"><?php echo $settings['address'] ?? ''; ?></textarea>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-4 control-label">State Code <span style="color: red">*</span></label>
                                            <div class="col-sm-7">
                                                <input type="text" value="<?php echo $settings['state_code'] ?? ''; ?>" class="form-control input-sm" name="state_code" id="state_code" required="">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-4 control-label">CIN <span style="color: red">*</span></label>
                                            <div class="col-sm-7">
                                                <input type="text" value="<?php echo $settings['cin'] ?? ''; ?>" class="form-control input-sm" name="cin" id="cin" required="">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-4 control-label">GST <span style="color: red">*</span></label>
                                            <div class="col-sm-7">
                                                <input type="text" value="<?php echo $settings['company_gst'] ?? ''; ?>" maxlength="15" class="form-control input-sm gst-number-check" name="company_gst" id="company_gst" required="" style="text-transform: uppercase;">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-4 control-label">PAN <span style="color: red">*</span></label>
                                            <div class="col-sm-7">
                                                <input type="text" value="<?php echo $settings['company_pan'] ?? ''; ?>" maxlength="10" class="form-control input-sm pancard-valid" name="company_pan" id="company_pan" required="" style="text-transform: uppercase;">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-4 control-label"> Mobile <span style="color: red">*</span></label>
                                            <div class="col-sm-7">
                                                <input type="text" value="<?php echo $settings['mobile'] ?? ''; ?>" class="form-control input-sm" name="mobile" id="mobile" required="" />
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-4 control-label"> Email <span style="color: red">*</span></label>
                                            <div class="col-sm-7">
                                                <input type="email" value="<?php echo $settings['email'] ?? ''; ?>" class="form-control input-sm" name="email" id="email" required="" pattern="^([0-9a-zA-Z]([-.\w]*[0-9a-zA-Z])*@([0-9a-zA-Z][-\w]*[0-9a-zA-Z]\.)+[a-zA-Z]{2,9})$" />
                                            </div>
                                        </div>


                                        <div class="box-header">
                                            <h3 class="box-title">Invoice settings</h3>
                                        </div>



                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-4 control-label"> Authorised Signatory (Stamp) </label>
                                            <div class="col-sm-7" style="height:auto;">
                                                <input type="file" class="form-control input-sm" name="company_stamp" id="company_stamp">
                                                <?php 
                                                $current_stamp_url = !empty($settings['company_stamp']) ? base_url() . ltrim(str_replace('\\', '/', $settings['company_stamp']), './') : '';
                                                if (!empty($current_stamp_url)) { ?>
                                                    <div style="margin-top: 8px;">
                                                        <img src="<?php echo $current_stamp_url; ?>" alt="Authorised Stamp" style="max-width: 150px; max-height: 80px; object-fit: contain; border: 1px solid #ddd; padding: 4px; border-radius: 4px; background: #fff;">
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>


                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-4 control-label">Default payment terms</label>
                                            <div class="col-sm-7">
                                                <select id="id_payment_terms" value="<?php echo $settings['invoice_default_payment_term'] ?? ''; ?>" name="invoice_default_payment_term" id="invoice_default_payment_term" tabindex="9">
                                                    <option value="0" selected="selected">Immediate</option>
                                                    <option value="1" selected="">Due upon receipt</option>
                                                    <option value="15">Due within 15 days</option>
                                                    <option value="30">Due within 30 days</option>
                                                    <option value="45">Due within 45 days</option>
                                                    <option value="60">Due within 60 days</option>
                                                    <option value="90">Due within 90 days</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-4 control-label">Default title</label>
                                            <div class="col-sm-7">
                                                <input type="text" value="<?php echo $settings['invoice_title'] ?? ''; ?>" class="form-control input-sm" name="invoice_title" id="invoice_title">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-4 control-label">Default subheading</label>
                                            <div class="col-sm-7">
                                                <input type="text" value="<?php echo $settings['invoice_subheading'] ?? ''; ?>" class="form-control input-sm" name="invoice_subheading" id="invoice_subheading">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-4 control-label">Default footer</label>
                                            <div class="col-sm-7">
                                                <textarea class="form-control" name="invoice_footer" id="invoice_footer" rows="3"><?php echo $settings['invoice_footer'] ?? ''; ?> </textarea>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-4 control-label">Note</label>
                                            <div class="col-sm-7">
                                                <textarea class="form-control" name="invoice_memo" id="invoice_memo" rows="3"><?php echo $settings['invoice_memo'] ?? ''; ?></textarea>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-4 control-label">Bank Details</label>
                                            <div class="col-sm-7">
                                                <textarea class="form-control" name="invoice_notes" id="invoice_notes" rows="3"><?php echo $settings['invoice_notes'] ?? ''; ?></textarea>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-4 control-label">Terms &amp; Conditions</label>
                                            <div class="col-sm-7">
                                                <textarea class="form-control" name="invoice_terms_and_conditions" id="invoice_terms_and_conditions" rows="3"><?php echo $settings['invoice_terms_and_conditions'] ?? ''; ?></textarea>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-4 control-label">Payment Terms</label>
                                            <div class="col-sm-7">
                                                <textarea class="form-control" name="invoice_payment_terms" id="invoice_payment_terms" rows="3"><?php echo $settings['invoice_payment_terms'] ?? ''; ?></textarea>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-4 control-label">Process Schedule</label>
                                            <div class="col-sm-7">
                                                <textarea class="form-control" name="invoice_process_schedule" id="invoice_process_schedule" rows="3"><?php echo $settings['invoice_process_schedule'] ?? ''; ?></textarea>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-4 control-label">Taxes</label>
                                            <div class="col-sm-7">
                                                <textarea class="form-control" name="invoice_taxes" id="invoice_taxes" rows="3"><?php echo $settings['invoice_taxes'] ?? ''; ?></textarea>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-4 control-label">Exclusions</label>
                                            <div class="col-sm-7">
                                                <textarea class="form-control" name="invoice_exclusions" id="invoice_exclusions" rows="3"><?php echo $settings['invoice_exclusions'] ?? ''; ?></textarea>
                                            </div>
                                        </div>


                                        <div class="box-header">
                                            <h3 class="box-title">Quotation settings</h3>
                                        </div>

                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-4 control-label">Default title</label>
                                            <div class="col-sm-7">
                                                <input type="text" value="<?php echo $settings['quotation_title'] ?? ''; ?>" class="form-control input-sm" name="quotation_title" id="quotation_title">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-4 control-label">Default subheading</label>
                                            <div class="col-sm-7">
                                                <input type="text" value="<?php echo $settings['quotation_subheading'] ?? ''; ?>" class="form-control input-sm" name="quotation_subheading" id="quotation_subheading">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-4 control-label">Default footer</label>
                                            <div class="col-sm-7">
                                                <textarea class="form-control" name="quotation_footer" id="quotation_footer" rows="3"><?php echo $settings['quotation_footer'] ?? ''; ?></textarea>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-4 control-label">Standard memo for new quotation</label>
                                            <div class="col-sm-7">
                                                <textarea class="form-control" name="quotation_memo" id="quotation_memo" rows="3"><?php echo $settings['quotation_memo'] ?? ''; ?></textarea>
                                            </div>
                                        </div>


                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-4 control-label">Notes</label>
                                            <div class="col-sm-7">
                                                <textarea class="form-control" name="notes" id="quotation_memo" rows="3"><?php echo $settings['notes'] ?? ''; ?></textarea>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-4 control-label">Terms & Conditions</label>
                                            <div class="col-sm-7">
                                                <textarea class="form-control" name="terms_and_conditions" id="terms_and_conditions" rows="3"><?php echo $settings['terms_and_conditions'] ?? ''; ?></textarea>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-4 control-label">Payment Terms</label>
                                            <div class="col-sm-7">
                                                <textarea class="form-control" name="payment_terms" id="payment_terms" rows="3"><?php echo $settings['payment_terms'] ?? ''; ?></textarea>
                                            </div>
                                        </div>


                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-4 control-label">Process Schedule</label>
                                            <div class="col-sm-7">
                                                <textarea class="form-control" name="process_schedule" id="process_schedule" rows="3"><?php echo $settings['process_schedule'] ?? ''; ?></textarea>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-4 control-label">Taxes</label>
                                            <div class="col-sm-7">
                                                <textarea class="form-control" name="taxes" id="taxes" rows="3"><?php echo $settings['taxes'] ?? ''; ?></textarea>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-4 control-label">Exclusions</label>
                                            <div class="col-sm-7">
                                                <textarea class="form-control" name="exclusions" id="exclusions" rows="3"><?php echo $settings['exclusions'] ?? ''; ?></textarea>
                                            </div>
                                        </div>





                                        <div class="box-header">
                                            <h3 class="box-title">Purchase Order settings</h3>
                                        </div>

                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-4 control-label">Default title</label>
                                            <div class="col-sm-7">
                                                <input type="text" value="<?php echo $settings['po_title'] ?? ''; ?>" class="form-control input-sm" name="po_title" id="po_title">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-4 control-label">Default subheading</label>
                                            <div class="col-sm-7">
                                                <input type="text" value="<?php echo $settings['po_subheading'] ?? ''; ?>" class="form-control input-sm" name="po_subheading" id="po_subheading">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-4 control-label">Default footer</label>
                                            <div class="col-sm-7">
                                                <textarea class="form-control" name="po_footer" id="po_footer" rows="3"><?php echo $settings['po_footer'] ?? ''; ?> </textarea>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-4 control-label">Standard memo</label>
                                            <div class="col-sm-7">
                                                <textarea class="form-control" name="po_memo" id="po_memo" rows="3"><?php echo $settings['po_memo'] ?? ''; ?></textarea>

                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-4 control-label">Note</label>
                                            <div class="col-sm-7">
                                                <textarea class="form-control" name="po_note" id="po_note" rows="3"><?php echo $settings['po_note'] ?? ''; ?></textarea>

                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-4 control-label">Terms & Conditions</label>
                                            <div class="col-sm-7">
                                                <textarea class="form-control" name="po_terms_and_conditions" id="po_terms_and_conditions" rows="3"><?php echo $settings['po_terms_and_conditions'] ?? ''; ?></textarea>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-4 control-label">Payment Terms</label>
                                            <div class="col-sm-7">
                                                <textarea class="form-control" name="po_payment_terms" id="po_payment_terms" rows="3"><?php echo $settings['po_payment_terms'] ?? ''; ?></textarea>
                                            </div>
                                        </div>


                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-4 control-label">Process Schedule</label>
                                            <div class="col-sm-7">
                                                <textarea class="form-control" name="po_process_schedule" id="po_process_schedule" rows="3"><?php echo $settings['po_process_schedule'] ?? ''; ?></textarea>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-4 control-label">Taxes</label>
                                            <div class="col-sm-7">
                                                <textarea class="form-control" name="po_taxes" id="po_taxes" rows="3"><?php echo $settings['po_taxes'] ?? ''; ?></textarea>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="inputEmail3" class="col-sm-4 control-label">Exclusions</label>
                                            <div class="col-sm-7">
                                                <textarea class="form-control" name="po_exclusions" id="po_exclusions" rows="3"><?php echo $settings['po_exclusions'] ?? ''; ?></textarea>
                                            </div>
                                        </div>


                                        <div class="box-header">
                                            <h3 class="box-title">Proforma Invoice Settings</h3>
                                        </div>

                                        <div class="form-group row">
                                            <label for="proforma_title" class="col-sm-4 control-label">Default title</label>
                                            <div class="col-sm-7">
                                                <input type="text" value="<?php echo $settings['proforma_title'] ?? ''; ?>" class="form-control input-sm" name="proforma_title" id="proforma_title">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="proforma_subheading" class="col-sm-4 control-label">Default subheading</label>
                                            <div class="col-sm-7">
                                                <input type="text" value="<?php echo $settings['proforma_subheading'] ?? ''; ?>" class="form-control input-sm" name="proforma_subheading" id="proforma_subheading">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="proforma_footer" class="col-sm-4 control-label">Default footer</label>
                                            <div class="col-sm-7">
                                                <textarea class="form-control" name="proforma_footer" id="proforma_footer" rows="3"><?php echo $settings['proforma_footer'] ?? ''; ?></textarea>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="proforma_memo" class="col-sm-4 control-label">Standard memo</label>
                                            <div class="col-sm-7">
                                                <textarea class="form-control" name="proforma_memo" id="proforma_memo" rows="3"><?php echo $settings['proforma_memo'] ?? ''; ?></textarea>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="proforma_note" class="col-sm-4 control-label">Note</label>
                                            <div class="col-sm-7">
                                                <textarea class="form-control" name="proforma_note" id="proforma_note" rows="3"><?php echo $settings['proforma_note'] ?? ''; ?></textarea>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="proforma_terms_and_conditions" class="col-sm-4 control-label">Terms &amp; Conditions</label>
                                            <div class="col-sm-7">
                                                <textarea class="form-control" name="proforma_terms_and_conditions" id="proforma_terms_and_conditions" rows="3"><?php echo $settings['proforma_terms_and_conditions'] ?? ''; ?></textarea>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="proforma_payment_terms" class="col-sm-4 control-label">Payment Terms</label>
                                            <div class="col-sm-7">
                                                <textarea class="form-control" name="proforma_payment_terms" id="proforma_payment_terms" rows="3"><?php echo $settings['proforma_payment_terms'] ?? ''; ?></textarea>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="proforma_process_schedule" class="col-sm-4 control-label">Process Schedule</label>
                                            <div class="col-sm-7">
                                                <textarea class="form-control" name="proforma_process_schedule" id="proforma_process_schedule" rows="3"><?php echo $settings['proforma_process_schedule'] ?? ''; ?></textarea>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="proforma_taxes" class="col-sm-4 control-label">Taxes</label>
                                            <div class="col-sm-7">
                                                <textarea class="form-control" name="proforma_taxes" id="proforma_taxes" rows="3"><?php echo $settings['proforma_taxes'] ?? ''; ?></textarea>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="proforma_exclusions" class="col-sm-4 control-label">Exclusions</label>
                                            <div class="col-sm-7">
                                                <textarea class="form-control" name="proforma_exclusions" id="proforma_exclusions" rows="3"><?php echo $settings['proforma_exclusions'] ?? ''; ?></textarea>
                                            </div>
                                        </div>


                                        <div class="box-header">
                                            <h3 class="box-title">Sales Order Settings</h3>
                                        </div>

                                        <div class="form-group row">
                                            <label for="so_title" class="col-sm-4 control-label">Default title</label>
                                            <div class="col-sm-7">
                                                <input type="text" value="<?php echo $settings['so_title'] ?? ''; ?>" class="form-control input-sm" name="so_title" id="so_title">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="so_subheading" class="col-sm-4 control-label">Default subheading</label>
                                            <div class="col-sm-7">
                                                <input type="text" value="<?php echo $settings['so_subheading'] ?? ''; ?>" class="form-control input-sm" name="so_subheading" id="so_subheading">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="so_footer" class="col-sm-4 control-label">Default footer</label>
                                            <div class="col-sm-7">
                                                <textarea class="form-control" name="so_footer" id="so_footer" rows="3"><?php echo $settings['so_footer'] ?? ''; ?></textarea>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="so_memo" class="col-sm-4 control-label">Standard memo</label>
                                            <div class="col-sm-7">
                                                <textarea class="form-control" name="so_memo" id="so_memo" rows="3"><?php echo $settings['so_memo'] ?? ''; ?></textarea>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="so_note" class="col-sm-4 control-label">Note</label>
                                            <div class="col-sm-7">
                                                <textarea class="form-control" name="so_note" id="so_note" rows="3"><?php echo $settings['so_note'] ?? ''; ?></textarea>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="so_terms_and_conditions" class="col-sm-4 control-label">Terms &amp; Conditions</label>
                                            <div class="col-sm-7">
                                                <textarea class="form-control" name="so_terms_and_conditions" id="so_terms_and_conditions" rows="3"><?php echo $settings['so_terms_and_conditions'] ?? ''; ?></textarea>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="so_payment_terms" class="col-sm-4 control-label">Payment Terms</label>
                                            <div class="col-sm-7">
                                                <textarea class="form-control" name="so_payment_terms" id="so_payment_terms" rows="3"><?php echo $settings['so_payment_terms'] ?? ''; ?></textarea>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="so_process_schedule" class="col-sm-4 control-label">Process Schedule</label>
                                            <div class="col-sm-7">
                                                <textarea class="form-control" name="so_process_schedule" id="so_process_schedule" rows="3"><?php echo $settings['so_process_schedule'] ?? ''; ?></textarea>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="so_taxes" class="col-sm-4 control-label">Taxes</label>
                                            <div class="col-sm-7">
                                                <textarea class="form-control" name="so_taxes" id="so_taxes" rows="3"><?php echo $settings['so_taxes'] ?? ''; ?></textarea>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="so_exclusions" class="col-sm-4 control-label">Exclusions</label>
                                            <div class="col-sm-7">
                                                <textarea class="form-control" name="so_exclusions" id="so_exclusions" rows="3"><?php echo $settings['so_exclusions'] ?? ''; ?></textarea>
                                            </div>
                                        </div>

                                    </div>

                                    <div class="box-header">
                                        <h3 class="box-title">Delivery Challan</h3>
                                    </div>

                                    <div>
                                        <div class="form-group row">
                                            <label for="dc_title" class="col-sm-4 control-label">Title</label>
                                            <div class="col-sm-7">
                                                <input type="text" class="form-control" name="dc_title" id="dc_title" value="<?php echo $settings['dc_title'] ?? ''; ?>">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="dc_subheading" class="col-sm-4 control-label">Subheading</label>
                                            <div class="col-sm-7">
                                                <input type="text" class="form-control" name="dc_subheading" id="dc_subheading" value="<?php echo $settings['dc_subheading'] ?? ''; ?>">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="dc_footer" class="col-sm-4 control-label">Footer</label>
                                            <div class="col-sm-7">
                                                <textarea class="form-control" name="dc_footer" id="dc_footer" rows="3"><?php echo $settings['dc_footer'] ?? ''; ?></textarea>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="dc_memo" class="col-sm-4 control-label">Memo</label>
                                            <div class="col-sm-7">
                                                <textarea class="form-control" name="dc_memo" id="dc_memo" rows="3"><?php echo $settings['dc_memo'] ?? ''; ?></textarea>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="dc_note" class="col-sm-4 control-label">Note</label>
                                            <div class="col-sm-7">
                                                <textarea class="form-control" name="dc_note" id="dc_note" rows="3"><?php echo $settings['dc_note'] ?? ''; ?></textarea>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="dc_terms_and_conditions" class="col-sm-4 control-label">Terms &amp; Conditions</label>
                                            <div class="col-sm-7">
                                                <textarea class="form-control" name="dc_terms_and_conditions" id="dc_terms_and_conditions" rows="3"><?php echo $settings['dc_terms_and_conditions'] ?? ''; ?></textarea>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="dc_payment_terms" class="col-sm-4 control-label">Payment Terms</label>
                                            <div class="col-sm-7">
                                                <textarea class="form-control" name="dc_payment_terms" id="dc_payment_terms" rows="3"><?php echo $settings['dc_payment_terms'] ?? ''; ?></textarea>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="dc_process_schedule" class="col-sm-4 control-label">Process Schedule</label>
                                            <div class="col-sm-7">
                                                <textarea class="form-control" name="dc_process_schedule" id="dc_process_schedule" rows="3"><?php echo $settings['dc_process_schedule'] ?? ''; ?></textarea>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="dc_taxes" class="col-sm-4 control-label">Taxes</label>
                                            <div class="col-sm-7">
                                                <textarea class="form-control" name="dc_taxes" id="dc_taxes" rows="3"><?php echo $settings['dc_taxes'] ?? ''; ?></textarea>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="dc_exclusions" class="col-sm-4 control-label">Exclusions</label>
                                            <div class="col-sm-7">
                                                <textarea class="form-control" name="dc_exclusions" id="dc_exclusions" rows="3"><?php echo $settings['dc_exclusions'] ?? ''; ?></textarea>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="box-header">
                                        <h3 class="box-title">Purchase Voucher</h3>
                                    </div>

                                    <div>
                                        <div class="form-group row">
                                            <label for="pv_title" class="col-sm-4 control-label">Title</label>
                                            <div class="col-sm-7">
                                                <input type="text" class="form-control" name="pv_title" id="pv_title" value="<?php echo $settings['pv_title'] ?? ''; ?>">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="pv_subheading" class="col-sm-4 control-label">Subheading</label>
                                            <div class="col-sm-7">
                                                <input type="text" class="form-control" name="pv_subheading" id="pv_subheading" value="<?php echo $settings['pv_subheading'] ?? ''; ?>">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="pv_footer" class="col-sm-4 control-label">Footer</label>
                                            <div class="col-sm-7">
                                                <textarea class="form-control" name="pv_footer" id="pv_footer" rows="3"><?php echo $settings['pv_footer'] ?? ''; ?></textarea>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="pv_memo" class="col-sm-4 control-label">Memo</label>
                                            <div class="col-sm-7">
                                                <textarea class="form-control" name="pv_memo" id="pv_memo" rows="3"><?php echo $settings['pv_memo'] ?? ''; ?></textarea>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="pv_note" class="col-sm-4 control-label">Note</label>
                                            <div class="col-sm-7">
                                                <textarea class="form-control" name="pv_note" id="pv_note" rows="3"><?php echo $settings['pv_note'] ?? ''; ?></textarea>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="pv_terms_and_conditions" class="col-sm-4 control-label">Terms &amp; Conditions</label>
                                            <div class="col-sm-7">
                                                <textarea class="form-control" name="pv_terms_and_conditions" id="pv_terms_and_conditions" rows="3"><?php echo $settings['pv_terms_and_conditions'] ?? ''; ?></textarea>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="pv_payment_terms" class="col-sm-4 control-label">Payment Terms</label>
                                            <div class="col-sm-7">
                                                <textarea class="form-control" name="pv_payment_terms" id="pv_payment_terms" rows="3"><?php echo $settings['pv_payment_terms'] ?? ''; ?></textarea>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="pv_process_schedule" class="col-sm-4 control-label">Process Schedule</label>
                                            <div class="col-sm-7">
                                                <textarea class="form-control" name="pv_process_schedule" id="pv_process_schedule" rows="3"><?php echo $settings['pv_process_schedule'] ?? ''; ?></textarea>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="pv_taxes" class="col-sm-4 control-label">Taxes</label>
                                            <div class="col-sm-7">
                                                <textarea class="form-control" name="pv_taxes" id="pv_taxes" rows="3"><?php echo $settings['pv_taxes'] ?? ''; ?></textarea>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="pv_exclusions" class="col-sm-4 control-label">Exclusions</label>
                                            <div class="col-sm-7">
                                                <textarea class="form-control" name="pv_exclusions" id="pv_exclusions" rows="3"><?php echo $settings['pv_exclusions'] ?? ''; ?></textarea>
                                            </div>
                                        </div>
                                    </div>





                                    <div class="box-header hide">
                                        <h3 class="box-title">Purchase Requisition</h3>
                                    </div>

                                    <div class="form-group row  hide">
                                        <label for="inputEmail3" class="col-sm-4 control-label">Purchase Requisition Notess</label>
                                        <div class="col-sm-7">
                                            <textarea class="form-control" name="purchase_requisition_notes" id="purchase_requisition_notes" rows="3"><?php echo $settings['purchase_requisition_notes'] ?? ''; ?></textarea>
                                        </div>
                                    </div>

                                    <div class="card-footer small text-muted">
                                        <button type="button" id="back" class="btn btn-default">Back</button>
                                        <button type="submit" class="btn btn-success pull-right">Submit</button>
                                    </div>
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

        <div class="control-sidebar-bg"></div>
        <?php $this->load->view('admin/footer'); ?>
    </div>
    <!-- ./wrapper -->


    <script>
        CKEDITOR.replace('quotation_memo');
        CKEDITOR.replace('notes');
        CKEDITOR.replace('terms_and_conditions');
        CKEDITOR.replace('payment_terms');
        CKEDITOR.replace('process_schedule');
        CKEDITOR.replace('taxes');
        CKEDITOR.replace('exclusions');

        CKEDITOR.replace('invoice_terms_and_conditions');
        CKEDITOR.replace('invoice_payment_terms');
        CKEDITOR.replace('invoice_process_schedule');
        CKEDITOR.replace('invoice_taxes');
        CKEDITOR.replace('invoice_exclusions');

        CKEDITOR.replace('po_note');
        CKEDITOR.replace('po_terms_and_conditions');
        CKEDITOR.replace('po_payment_terms');
        CKEDITOR.replace('po_process_schedule');
        CKEDITOR.replace('po_taxes');
        CKEDITOR.replace('po_exclusions');

        CKEDITOR.replace('proforma_memo');
        CKEDITOR.replace('proforma_note');
        CKEDITOR.replace('proforma_terms_and_conditions');
        CKEDITOR.replace('proforma_payment_terms');
        CKEDITOR.replace('proforma_process_schedule');
        CKEDITOR.replace('proforma_taxes');
        CKEDITOR.replace('proforma_exclusions');

        CKEDITOR.replace('so_memo');
        CKEDITOR.replace('so_note');
        CKEDITOR.replace('so_terms_and_conditions');
        CKEDITOR.replace('so_payment_terms');
        CKEDITOR.replace('so_process_schedule');
        CKEDITOR.replace('so_taxes');
        CKEDITOR.replace('so_exclusions');
        CKEDITOR.replace('dc_footer');
        CKEDITOR.replace('dc_memo');
        CKEDITOR.replace('dc_note');
        CKEDITOR.replace('dc_terms_and_conditions');
        CKEDITOR.replace('dc_payment_terms');
        CKEDITOR.replace('dc_process_schedule');
        CKEDITOR.replace('dc_taxes');
        CKEDITOR.replace('dc_exclusions');

        CKEDITOR.replace('pv_footer');
        CKEDITOR.replace('pv_memo');
        CKEDITOR.replace('pv_note');
        CKEDITOR.replace('pv_terms_and_conditions');
        CKEDITOR.replace('pv_payment_terms');
        CKEDITOR.replace('pv_process_schedule');
        CKEDITOR.replace('pv_taxes');
        CKEDITOR.replace('pv_exclusions');

        // ========== COMPANY SETTINGS: PROPER GST-PAN-STATE CODE VALIDATION ==========
        $(document).on('blur', '#company_gst', function() {
            var gstNo = $(this).val().trim().toUpperCase();
            console.log('Company GST blur - Value:', gstNo);
            
            if (gstNo.length === 0) {
                $('#company_pan').val('');
                $('#state_code').val('');
                return;
            }
            
            if (gstNo.length !== 15) {
                alert('Invalid! GST No must be exactly 15 characters.\nExample: 27AAPFU0205R1Z0\nYou entered: ' + gstNo + ' (' + gstNo.length + ' chars)');
                $(this).focus();
                $(this).select();
                return;
            }
            
            var gstRegex = /^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[0-9]{1}[A-Z]{1}[0-9]{1}$/;
            if (!gstRegex.test(gstNo)) {
                alert('Invalid GST format!\nExpected: [2 digits][5 letters][4 digits][1 letter][1 digit][1 letter][1 digit]\nExample: 27AAPFU0205R1Z0\nYou entered: ' + gstNo);
                $(this).focus();
                $(this).select();
                return;
            }
            
            var stateCode = gstNo.substring(0, 2);
            var panNo = gstNo.substring(2, 12);
            
            $('#state_code').val(stateCode);
            $('#company_pan').val(panNo);
            
            console.log('Company GST extracted - State Code: ' + stateCode + ', PAN: ' + panNo);
        });
        
        $(document).on('blur', '#company_pan', function() {
            var panNo = $(this).val().trim().toUpperCase();
            console.log('Company PAN blur - Value:', panNo);
            
            if (panNo.length === 0) {
                return;
            }
            
            var panRegex = /^[A-Z]{5}[0-9]{4}[A-Z]{1}$/;
            if (!panRegex.test(panNo)) {
                alert('Invalid PAN format!\nExpected: [5 letters][4 digits][1 letter]\nExample: AAPFU0205R\nYou entered: ' + panNo);
                $(this).focus();
                $(this).select();
                return;
            }
            
            var gstNo = $('#company_gst').val().trim().toUpperCase();
            if (gstNo.length === 15) {
                var gstPan = gstNo.substring(2, 12);
                if (panNo !== gstPan) {
                    alert('Mismatch Alert!\nPAN in GST: ' + gstPan + '\nPAN you entered: ' + panNo + '\n\nThey do not match. Please correct.');
                    $(this).focus();
                    $(this).select();
                } else {
                    console.log('Company PAN matches GST - OK');
                }
            }
        });
        
        $(document).on('blur', '#state_code', function() {
            var stateCode = $(this).val().trim();
            console.log('State Code blur - Value:', stateCode);
            
            if (stateCode.length === 0) {
                return;
            }
            
            if (!/^[0-9]{2}$/.test(stateCode)) {
                alert('Invalid State Code!\nExpected: [2 digits] (01-37)\nExample: 27\nYou entered: ' + stateCode);
                $(this).focus();
                $(this).select();
                return;
            }
            
            var gstNo = $('#company_gst').val().trim().toUpperCase();
            if (gstNo.length === 15) {
                var gstStateCode = gstNo.substring(0, 2);
                if (stateCode !== gstStateCode) {
                    alert('Mismatch Alert!\nState Code in GST: ' + gstStateCode + '\nState Code you entered: ' + stateCode + '\n\nThey do not match. Please correct.');
                    $(this).focus();
                    $(this).select();
                } else {
                    console.log('State Code matches GST - OK');
                }
            }
        });
    </script>
