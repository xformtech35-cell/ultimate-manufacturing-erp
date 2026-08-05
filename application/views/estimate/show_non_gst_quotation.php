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
                <!--                <h1>
                                    Quotation
                                </h1>-->
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/'?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="#">Non GST Quotation</a></li>
                    <li class="active">Non GST Quotation Details</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <!--<div class="box">-->
                        <!--<div class="box-header">-->
                        <label for="inputEmail3" name="number" id="number" class="col-sm-12 control-label"><h2>Non GST Quotation:<?php echo $non_gst_estimates_data_group['number']; ?></h2></label>
                        <!--</div>-->
                        <!-- /.box-header -->
                        <!--<div class="box-body">-->

                        <div class="row" style="padding:2%">
                            <div class="pull-left">

                                <div class="col-md-2">
                                    <a href="<?php echo base_url(); ?>EstimateController/edit_non_gst_estimate_details/<?php echo $non_gst_estimates_data_group['number']; ?>" class="btn btn-primary" role="button">Edit</a>
                                </div>
                                <div class="col-md-2">
                                    <form method="POST" action="<?php echo base_url(); ?>EstimateController/convert_to_invoice_non_gst_data/<?php echo $non_gst_estimates_data_group['number']; ?>">
                                        <input type="hidden" class="form-control input-sm"   name="number" id="number" required="" value="<?php echo $non_gst_estimates_data_group['number']; ?>">  
                                        <input type="hidden" class="form-control input-sm"   name="invoice_number" id="invoice_number" required="" value="INV/<?php echo date("Y"); ?>/<?php echo strtoupper(date("M")); ?>/<?php printf("%04d", $invoice_id['COUNT(uid)'] + 1); ?>">
                                        <button id="convertToInvoice" class="btn btn-primary" role="button" type="submit">Convert to Invoice</button>
                                    </form>
                                </div>
                                <div class="pull-right">
                                    <div class="col-md-6">
                                        <form method="POST" action="<?php echo base_url(); ?>EstimateController/print_non_gst_quotation/<?php echo $non_gst_estimates_data_group['number']; ?>">
                                            <input type="hidden" class="form-control input-sm"   name="number" id="number" required="" value="<?php echo $non_gst_estimates_data_group['number']; ?>">  
                                            <input type="hidden" class="form-control input-sm"   name="invoice_number" id="invoice_number" required="" value="INV/<?php echo date("Y"); ?>/<?php echo strtoupper(date("M")); ?>/<?php printf("%04d", $invoice_id['COUNT(uid)'] + 1); ?>">
                                            <button id="convertToInvoice" class="btn btn-primary" role="button" type="submit">Customer View</button>
                                        </form>

                                    </div>
                                <div class="col-md-3 dropdown">
                                       
                                    <div class="col-md-2">

                                        <div class="dropdown">
                                            <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                More <b class="caret"></b>
                                            </button>
                                            <input type="hidden" class="form-control input-sm"   name="number" id="number" required="" value="<?php echo $non_gst_estimates_data_group['number']; ?>">  
                                            <ul class="dropdown-menu pull-right" aria-labelledby="dropdownMenuButton">
                                                <li class="hide"><a class="dropdown-item" href="<?php echo base_url(); ?>/LoginController/get_settings">Edit Business Information</a></li>
                                                <li><a class="dropdown-item" id="exportpdf" href="<?php echo base_url(); ?>Pdf/download_non_gst_quote/<?php echo $non_gst_estimates_data_group['number']; ?>">Export As PDF</a></li>
                                                <li><a class="dropdown-item" href="<?php echo base_url(); ?>EstimateController/print_non_gst_quotation/<?php echo $non_gst_estimates_data_group['number']; ?>">Print</a></li>
                                            </ul>

                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="shadows1" >
                            <div class="row">
                                <section class="contemporary-template__header">
                                    <div class="col-md-8">
                                        <div class="contemporary-template__header__logo">
                                            <img class="contemporary-template__business-logo" src="<?php echo base_url() . $settings['company_logo'] ?>" width="20%" height="20%">

                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="contemporary-template__header__info">
                                            <div class="wv-heading--title"><h1>QUOTATION</h1></div>
                                            <div class="wv-heading--subtitle"></div>
                                            <span class="wv-text--strong"><b><?php echo $settings['company_name']; ?></b></span><br>
                                            <span class="wv-text--strong"><b>GST Number:</b><?php echo $settings['company_gst']; ?></span><br>
                                            <span class="wv-text--strong"><b>PAN Number:</b><?php echo $settings['company_pan']; ?></span><br>
                                            <span class="wv-text--strong"><b>Mobile Number:</b><?php echo $settings['mobile']; ?></span><br>
                                            <span class="wv-text--strong"><b>Email ID:</b><?php echo $settings['email']; ?></span><br>
                                             <span class="wv-text--strong"><b>Address:</b></span>
                                            <div class="contemporary-template__header__info__address">
                                                <?php
                                                $pieces = explode(',', $settings['address']);
                                                foreach ($pieces as $part) {
                                                    echo $part . "<br>";
                                                }
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </section>

                            </div>
                            <hr>
                            <div class="row">

                                <div class="col-md-8">

                                    <div class="form-group row ">
                                        <label for="inputEmail3" id="subheading1" class="col-sm-4 control-label"><b>TO:</b></label>
                                    </div>

                                    <div class="form-group row ">
                                        <div class="col-sm-8">
                                            <?php echo $non_gst_estimates_data_group['company_name']; ?>
                                        </div>
                                    </div>

                                    <div class="form-group row ">
                                        <div class="col-sm-8">
                                            <?php echo $non_gst_estimates_data_group['address']; ?>
                                        </div>
                                    </div>

                                    <div class="form-group row hide">
                                        <div class="col-sm-8">
                                            <b>GST Number:</b><?php if ($non_gst_estimates_data_group['gst']) { ?>
                                                <?php echo $non_gst_estimates_data_group['gst']; ?>
                                            <?php } else { ?>
                                                <?php echo "None Provided";
                                            }
                                            ?>
                                        </div>
                                    </div>

                                    <div class="form-group row hide">
                                        <div class="col-sm-8">
                                            <b>PAN Number:</b><?php if ($non_gst_estimates_data_group['pancard']) { ?>
                                                <?php echo $non_gst_estimates_data_group['pancard']; ?>
                                            <?php } else { ?>
                                                <?php echo "None Provided";
                                            }
                                            ?>

                                        </div>
                                    </div>

                                    <div class="form-group row ">
                                        <div class="col-sm-8">
                                            <b>Customer Name: <?php echo $non_gst_estimates_data_group['fullname']; ?></b><br>

                                        </div>
                                    </div>



                                </div>

                                <div class="col-md-4">

                                    <div class="form-group row ">
                                        <div class="col-sm-12">
                                            <label class="control-label"><b>Quotation Number:</b></label><?php echo $non_gst_estimates_data_group['number']; ?>
                                        </div>
                                    </div>

                                    <div class="form-group row ">
                                        <div class="col-sm-12">
                                            <label class="control-label"><b>Quotation Date:</b></label><?php echo $non_gst_estimates_data_group['date']; ?>
                                        </div>
                                    </div>

                                    <div class="form-group row ">
                                        <div class="col-sm-12">
                                            <label class="control-label"><b>Expires on: </b></label><?php echo $non_gst_estimates_data_group['exp_date']; ?>
                                        </div>
                                    </div>

                                    <div class="form-group row ">
                                        <div class="col-sm-12">
                                            <span class="non_gst_total"><label class="control-label non_gst_total"><b>Grand Total (INR): </b></label><?php echo number_format($non_gst_estimates_data_group['basic_total'], 2); ?></span>
                                        </div>
                                    </div>

                                    <div class="form-group row ">
                                        <div class="col-sm-12">
                                            <label class="control-label"><b>Enquiry: </b></label><?php if ($non_gst_estimates_data_group['enquiry'] == '1') { ?>
                                                <?php echo "By Mail"; ?>
                                            <?php } else { ?>
                                                <?php
                                                echo "By Verbal";
                                            }
                                            ?>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="table-responsive">  

                                <table class="table table-bordered" id="dynamic_field">  
                                    <tr>
                                        <th>Sr.No.</th>
                                        <th>Item</th>
                                        <th>MOQ</th>
                                        <th>HSN Code</th>
                                        <th>Price/Unit</th>
                                        <th>Discount(%)</th>
                                        <th>Amount</th>
                                    </tr>
                                    <?php
                                    $i = 1;
                                    foreach ($show_non_gst_quotation as $key) {
                                        ?>
                                        <tr> 
                                            <td><span id="" class=""></span>
                                                <?php echo $i; ?>
                                            </td>
                                            <td><span id="" class=""></span>
                                                <?php echo $key->product_name; ?><input type="hidden" name="term[]" value="<?php echo $key->product_name; ?>" id="item_name<?php echo $i; ?>" class="form-control input-sm name_list product_name_auto" /><input type="hidden" class="form-control input-sm"   name="quotation_id[]" id="quotation_id<?php echo $i; ?>"  value="<?php echo $key->quotation_id; ?>">
                                            </td>
                                            <td><span id="" class=""></span>
                                                <?php echo $key->quantity; ?><input type="hidden" name="quantity[]" value="<?php echo $key->quantity; ?>" id="quantity<?php echo $i; ?>" class="form-control input-sm name_list quantity_auto" value="1" />
                                            </td>
                                            <td><span id="" class=""></span>
    <?php echo $key->hsn_code; ?><input type="hidden" name="hsn[]" value="<?php echo $key->hsn_code; ?>" id="hsn<?php echo $i; ?>" class="form-control input-sm name_list" /> 
                                            </td>

                                            <td><span id="" class=""></span>
    <?php echo number_format($key->price,2); ?><input type="hidden" name="price[]" value="<?php echo $key->price; ?>" id="price<?php echo $i; ?>" class="form-control input-sm name_list"/>
                                            </td>

                                            <td><span id="" class=""></span>
    <?php echo $key->discount; ?><input type="hidden" name="discount[]" value="<?php echo $key->discount; ?>" id="discount<?php echo $i; ?>" class="form-control input-sm name_list"/> %
                                            </td>

                                            <td><span id="" class=""></span>
    <?php echo number_format($key->amount, 2); ?><input type="hidden" name="amount[]" id="amount<?php echo $i; ?>" class="form-control input-sm name_list amount_auto" value="<?php echo $key->amount; ?>"/>

                                            </td>
                                        </tr>  
                                        <?php
                                        $i++;
                                    }
                                    ?>
                                </table>   

                                <div align="right" style="margin: 10px">

                                    <b> <span>Grand Total: <?php echo number_format($non_gst_estimates_data_group['basic_total'], 2); ?></span></b><br>
                                     <!--<span id="total_amount" name="total_amount">Total: ₹0.00</span><br>-->
                                    <span class="hide" id="sgst_amount" name="sgst_amount">SGST Amount: ₹0.00</span><br>
                                    <span class="hide" id="cgst_amount" name="cgst_amount">CGST Amount: ₹0.00</span><br>

<!--<span class="total" id="total_gst_amount" name="total_gst_amount">Total GST Amount: ₹<?php echo $non_gst_estimates_data_group['total']; ?></span>-->

                                </div>

                                <label class="control-label pull-left"><b>Notes</b></label><br>

                                <div class="col-sm-12">
                                    <textarea style="overflow: auto;
                                              border: none;" class="form-control" readonly="" name="notes" id="quotation_memo" rows="8"><?php echo $settings['notes']; ?></textarea><br>
                                </div>

                                <div class="form-group row ">
                                    <label for="inputEmail3" class="col-sm-9 control-label"><b>Receivers Sign :</b></label>
                                    <label for="inputEmail3" class="col-sm-3 control-label"><b> Authorized Sign :</b></label>
                                </div>

                                <label class="control-label pull-left"><b>*(It is electronic generated quotation signatures may not appear).</b></label><br>

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
    <script>

        $(document).ready(function () {

            var igst = $("#igst").val();
            var gst = $("#gst").val();
            var non_gst = $("#non_gst").val();
            //alert(igst);
            if (igst == "igst") {
                $(".gst").hide();
                $(".igst").show();
                $(".gst_per").show();
                $(".total").show();

                $(".non_gst_total").hide();
                $(".gst_total").hide();
                $(".igst_total").show();

            }
            if (gst == "gst") {
                $(".gst").show();
                $(".igst").hide();
                $(".gst_per").show();
                $(".total").show();

                $(".non_gst_total").hide();
                $(".gst_total").show();
                $(".igst_total").hide();
            }
            if (non_gst == "non_gst") {
                $(".gst").hide();
                $(".igst").hide();
                $(".gst_per").hide();
                $(".total").hide();

                $(".non_gst_total").show();
                $(".gst_total").hide();
                $(".igst_total").hide();

            }

        });
    </script>

