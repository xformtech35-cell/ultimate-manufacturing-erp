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
                                    Invoice
                                </h1>-->
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/'?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li><a href="#">Non GST Invoice</a></li>
                    <li class="active">Non GST Invoice Details</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">

                <label for="inputEmail3" name="invoice_number" id="invoice_number" class="col-sm-12 control-label"><h2>Non GST Invoice:<?php echo $ng_invoice_data_group['invoice_number']; ?></h2></label>

                <div class="row" style="padding:2%">
                    <div class="pull-left">

                        
                        <div class="col-md-2">
                            <a href="<?php echo base_url(); ?>InvoiceController/edit_non_gst_invoice_details/<?php echo $ng_invoice_data_group['invoice_number']; ?>" class="btn btn-primary" role="button">Edit</a>
                        </div>
                         
                        <div class="pull-right">
                            <div class="col-md-6">
                                <form method="POST" action="<?php echo base_url(); ?>InvoiceController/print_non_gst_invoice/<?php echo $ng_invoice_data_group['invoice_number']; ?>">
                                    <input type="hidden" class="form-control input-sm"   name="invoice_number" id="invoice_number" required="" value="<?php echo $ng_invoice_data_group['invoice_number']; ?>">  
                                    <input type="hidden" class="form-control input-sm"   name="invoice_number" id="invoice_number" required="" value="INV/<?php echo date("Y"); ?>/<?php echo strtoupper(date("M")); ?>/<?php printf("%04d", $invoice_id['COUNT(uid)'] + 1); ?>">
                                    <button id="convertToInvoice" class="btn btn-primary" role="button" type="submit">Customer View</button>
                                </form>
                            </div>
                            <div class="col-md-3 dropdown">
                                <div class="dropdown">
                                    <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Send <b class="caret"></b>
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        <li><a class="dropdown-item" href="#">Send Invoice</a></li>
                                        <li class="divider"></li>
                                        <li>Send Using</li>
                                        <li><a class="dropdown-item" href="https://www.gmail.com"><img src="https://d3mui6avu5pugx.cloudfront.net/sitestatic/images/invoice-view/ico-gmail.png" class="send-icon" alt="Gmail">Gmail</a></li>
                                        <li><a class="dropdown-item" href="https://login.yahoo.com"><img src="https://d3mui6avu5pugx.cloudfront.net/sitestatic/images/invoice-view/ico-yahoomail.png" class="send-icon" alt="Yahoo Mail">Yahoo! Mail</a></li>
                                        <li><a class="dropdown-item" href="https://outlook.live.com/owa/?authRedirect=true"><img src="https://d3mui6avu5pugx.cloudfront.net/sitestatic/images/invoice-view/ico-outlook.png" class="send-icon" alt="Outlook">Outlook</a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-md-2">

                                <div class="dropdown">
                                    <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        More <b class="caret"></b>
                                    </button>
                                    <input type="hidden" class="form-control input-sm"  name="invoice_number" id="invoice_number" required="" value="<?php echo $ng_invoice_data_group['invoice_number']; ?>">  
                                    <ul class="dropdown-menu pull-right" aria-labelledby="dropdownMenuButton">
                                        <li class="hide"><a class="dropdown-item" href="<?php echo base_url(); ?>LoginController/get_settings/">Edit Business Information</a></li>
                                        <li><a class="dropdown-item" id="exportpdf" href="<?php echo base_url(); ?>Pdf/download_non_gst_invoice/<?php echo $ng_invoice_data_group['invoice_number']; ?>">Export As PDF</a></li>
                                        <li><a class="dropdown-item" href="<?php echo base_url(); ?>InvoiceController/print_non_gst_invoice/<?php echo $ng_invoice_data_group['invoice_number']; ?>">Print</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- /.box-header -->

                <div class="shadows1">
                    <div class="row">
                        <section>
                            <div class="col-md-8">
                                <div>
                                    <img src="<?php echo base_url() . $settings['company_logo'] ?>" width="20%" height="20%">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="contemporary-template__header__info">
                                    <div class="wv-heading--title"><h1>TAX INVOICE</h1></div>
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
                                <label for="inputEmail3" id="subheading1" class="col-sm-4 control-label"><b>BILL TO:</b></label>
                            </div>
                            <div class="form-group row ">
                                <div class="col-sm-8">
                                    <?php echo $ng_invoice_data_group['company_name']; ?>
                                </div>
                            </div>
                            <div class="form-group row ">
                                <div class="col-sm-8">
                                    <?php echo $ng_invoice_data_group['address']; ?>
                                </div>
                            </div>

                            
                            <div class="form-group row ">
                                <div class="col-sm-8">
                                    <b>PAN Number:</b><?php if ($ng_invoice_data_group['pancard']) { ?>
                                                <?php echo $ng_invoice_data_group['pancard']; ?>
                                            <?php } else { ?>
                                                <?php echo "None Provided";
                                            } ?>
                                </div>
                            </div>

                            <div class="form-group row ">
                                <div class="col-sm-8">
                                    <b> Customer Name: <?php echo $ng_invoice_data_group['fullname']; ?></b>
                                </div>
                            </div>
                           
                        </div>

                        <div class="col-md-4">
                            <div class="form-group row ">
                                <div class="col-sm-12">
                                    <label class="control-label"><b>Invoice Number:</b></label><?php echo $ng_invoice_data_group['invoice_number']; ?>
                                </div>
                            </div>

                            <div class="form-group row ">
                                <div class="col-sm-12">
                                    <label class="control-label"><b>Invoice Date:</b></label><?php echo date('d-m-Y', strtotime($ng_invoice_data_group['invoice_date'])); ?>
                                </div>
                            </div>

                            <div class="form-group row ">
                                <div class="col-sm-12">
                                    <label class="control-label"><b>Grand Total (INR): </b></label><?php echo number_format($ng_invoice_data_group['total'], 2); ?>
                                </div>
                            </div>

                            <div class="form-group row ">
                                <div class="col-sm-12">
                                    <label class="control-label"><b>Payment mode:  </b></label><?php if ($ng_invoice_data_group['payment_method'] == '1') { ?>
                                        <?php echo "By Cash"; ?>
                                    <?php } else if ($ng_invoice_data_group['payment_method'] == '2') { ?>
                                        <?php echo "By Cheque";
                                    } else if ($ng_invoice_data_group['payment_method'] == '3') {
                                        ?>
                                        <?php echo "By NetBanking";
                                    }else { ?>
                                                <?php echo "None Provided";
                                    } ?>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="table-responsive">  

                        <table class="table table-bordered" id="dynamic_field">  
                            <tr>
                                <th>Sr.No.</th>
                                <th>Item</th>
                                <th>Description</th>
                                <th>Qty(Nos/Kg)</th>
                                <th>HSN Code</th>
                                <th>Price</th>
                                <th>Amount</th>
                            </tr>
                            <?php
                            $i = 1;
                            foreach ($show_ng_invoice as $key) {
                                ?>
                                <tr> 
                                    <td><span id="" class=""></span>
                                        <?php echo $i; ?>
                                        </td>
                                    <td><span id="" class=""></span>
                                        <?php echo $key->product_name; ?><input type="hidden" name="term[]" value="<?php echo $key->product_name; ?>" id="item_name<?php echo $i; ?>" class="form-control input-sm name_list product_name_auto" /><input type="hidden" class="form-control input-sm"   name="quotation_id[]" id="quotation_id<?php echo $i; ?>"  value="<?php echo $key->invoice_id; ?>">
                                    </td>
                                    
                                    <td><span id="" class=""></span>
                                        <?php echo $key->description; ?><input type="hidden" name="description[]" value="<?php echo $key->description; ?>" id="description<?php echo $i; ?>" class="form-control input-sm name_list description_auto" />
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
                                    <?php echo number_format($key->amount, 2); ?><input type="hidden" name="amount[]" id="amount<?php echo $i; ?>" class="form-control input-sm name_list amount_auto" value="<?php echo $key->amount; ?>"/>

                                    </td>
                                </tr>  
                                <?php
                                $i++;
                            }
                            ?>
                        </table>   

                        <div align="right" style="margin: 10px">
                            <span id="total_amount" name="total_amount">Total: ₹0.00</span><br>
                           
                            <span><b>Grand Total:</b> ₹<?php echo number_format($ng_invoice_data_group['total'],2); ?></span><br>
                            
                             <?php
                            // include amount_convert
                            require( APPPATH . '/third_party/amount_convert.php');
                            ?>
                            
                                 <b> Grand Total in Words:<?php echo number_to_word($ng_invoice_data_group['total']); ?> Only.</b><br>
                            
                            <!--<b> Grand Total in Words:<span id="word2" name="word2"></span>Only.</b><br>-->
                                            
                            <!--<span id="total_gst_amount" name="total_gst_amount">Total GST Amount: ₹<?php echo $ng_invoice_data_group['total']; ?></span>-->

                        </div>
                        <label class="control-label pull-left"><b>Notes</b></label><br>
                        <div class="col-sm-12">
                            <textarea style="overflow: auto;
                                      border: none;" class="form-control" readonly="" name="notes" id="quotation_memo" rows="8"><?php echo $settings['notes']; ?></textarea>
                        </div>
                        <div class="form-group row ">
                            <label for="inputEmail3" class="col-sm-9 control-label"><b>Receivers Sign :</b></label>
                            <label for="inputEmail3" class="col-sm-3 control-label"><b> Authorized Sign :</b></label>
                        </div>

                        <label class="control-label pull-left"><b>*(It is electronic generated invoice signatures may not appear).</b></label><br>

                    </div>  
                </div>

            </section>
            <!-- /.content -->
        </div>
<?php $this->load->view('admin/footer'); ?>
    </div>
    <!-- ./wrapper -->
    <script>

        $(document).ready(function () {

            var igst = $("#igst").val();
            //alert(igst);
            if (igst == "igst") {
                $(".gst").hide();
                $(".igst").show();
            } else {
                $(".gst").show();
                $(".igst").hide();
            }

        });
    </script>