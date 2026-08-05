<!DOCTYPE html>
<html moznomarginboxes mozdisallowselectionprint>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title> Invoice </title>
        <meta name="description" content="Invoice print page">
        <meta name="viewport" content="width=device-width">
        <link href="<?php echo base_url(); ?>printme/bootstrap.min.css" rel="stylesheet" >
        <link href="<?php echo base_url(); ?>printme/main.css" rel="stylesheet">
        <script src="<?php echo base_url(); ?>printme/jquery.min.js"></script>
        <script src="<?php echo base_url(); ?>printme/jquery-printme.js"></script>

        <script type="text/javascript">

            $(document).ready(function () {
                $("#print_btn").click(function () {
                    var print_btn = document.getElementById("print_btn");
                    var download_btn = document.getElementById("download_btn");
                    var hide_url = document.getElementById("hide_url");
                    
                    print_btn.style.visibility = 'hidden';
                    download_btn.style.visibility = 'hidden';
                    hide_url.style.visibility = 'hidden';
                    
                    window.print();
                    location.reload();
                    print_btn.style.visibility = 'visible';
                    download_btn.style.visibility = 'download_btn';
                });
            });
        </script>

    </head>
    <body>
        <div class="container main print shadows1" id="print">

            <div class="row">

                <div class="row" id="dataexample3">
                    <div class="col-md-9">

                    </div>
                    <div class="col-md-1 hide">
                        <div class="panel-body">
                            <p>
                                <button id="print_btn" class="btn btn-primary btn-sm">Print</button>
                            </p>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="panel-body">
                            <p id="hide_url">
                                <?php $url = base_url() . 'Pdf/download_non_gst_invoice/' . $ng_invoice_data_group['invoice_number']; ?>
                                <a href="<?php echo $url; ?>"><button id="download_btn" class="btn btn-primary btn-sm">Export As PDF</button></a>
                            </p>
                        </div>
                    </div>

                </div>

                <div>
                    <div class="col-sm-8">
                        <img src="<?php echo base_url() . $settings['company_logo'] ?>" width="20%" height="20%">
                    </div>
                    <div class="col-sm-4">
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
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-8">

                    <div class="col-sm-8 row">
                        <span class="wv-text--strong"><b>BILL TO:</b></span>
                        <!--<label for="inputEmail3" id="subheading1"><b>BILL TO:</b></label>-->
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
                            <span class="wv-text--strong"><b>GST Number:</b><?php if ($ng_invoice_data_group['gst']) { ?>
                                                <?php echo $ng_invoice_data_group['gst']; ?>
                                            <?php } else { ?>
                                                <?php echo "None Provided";
                                            } ?>
                            </span>
                        </div>
                    </div>

                    <div class="form-group row ">
                        <div class="col-sm-8">
                            <span class="wv-text--strong"><b>PAN Number:</b><?php if ($ng_invoice_data_group['pancard']) { ?>
                                                <?php echo $ng_invoice_data_group['pancard']; ?>
                                            <?php } else { ?>
                                                <?php echo "None Provided";
                                            } ?>
                            </span>
                        </div>
                    </div>

                    <div class="form-group row ">
                        <div class="col-sm-8">
                            <b> Customer Name:  <?php echo $ng_invoice_data_group['fullname']; ?></b>
                        </div>
                    </div>
                    
                 
                </div>
                <div class="col-md-4">
                    <div class="form-group row ">
                        <label class="control-label"><b>Invoice Number:</b></label><?php echo $ng_invoice_data_group['invoice_number']; ?>
                    </div>

                    <div class="form-group row ">
                        <label class="control-label"><b>Invoice Date:</b></label><?php echo date('d-m-Y', strtotime($ng_invoice_data_group['invoice_date'])); ?>
                    </div>

                    <div class="form-group row ">
                        <label class="control-label"><b>Grand Total (INR): </b></label><?php echo number_format($ng_invoice_data_group['total'], 2); ?>
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
                                <?php echo $key->description; ?><input type="hidden" name="description[]" value="<?php echo $key->description; ?>" id="description<?php echo $i; ?>" class="form-control input-sm name_list quantity_auto" />
                            </td>
                            
                            <td><span id="" class=""></span>
                                <?php echo $key->quantity; ?><input type="hidden" name="quantity[]" value="<?php echo $key->quantity; ?>" id="quantity<?php echo $i; ?>" class="form-control input-sm name_list quantity_auto" value="1" />
                            </td>
                            <td><span id="" class=""></span>
                                <?php echo $key->hsn_code; ?><input type="hidden" name="hsn[]" value="<?php echo $key->hsn_code; ?>" id="hsn<?php echo $i; ?>" class="form-control input-sm name_list" /> 
                            </td>
                            <td class="hide"><span id="" class=""></span>
                                <?php echo $key->gst; ?><input type="hidden" name="gst_per[]" value="<?php echo $key->gst; ?>" id="gst_per<?php echo $i; ?>" class="form-control input-sm name_list" />
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
            </div>  

            <div class="row" id="dataexample3">
                <div class="col-md-10">

                </div>
                <div class="col-md-2">
                    <label class="control-label"><b>Grand Total (INR): </b></label><?php echo number_format($ng_invoice_data_group['total'], 2); ?>
                </div>
            </div>

            <hr>
            <div class="row" id="dataexample3">
                <div class="col-md-10">
                </div>
                <div class="col-md-2">
                    <?php
                    // include amount_convert
                    require( APPPATH . '/third_party/amount_convert.php');
                    ?>
                    <label class="control-label"><b>Grand Total in Words: <?php echo number_to_word($ng_invoice_data_group['total']); ?> Only</label>
                </div>
            </div>
            
            <hr>

            <div class="row ">
                <div class="col-md-10">

                </div>
                <div class="col-md-2">
                    <label class="control-label"><b>Payment mode: </b></label>
                        <?php if ($ng_invoice_data_group['payment_method'] == '1') { ?>
                        <?php echo "By Cash"; ?>
                    <?php } else if ($ng_invoice_data_group['payment_method'] == '2') { ?>
                        <?php
                        echo "By Cheque";
                    } else if ($ng_invoice_data_group['payment_method'] == '3') {
                        ?>
                        <?php
                        echo "By NetBanking";
                    }
                    else {?>
                         <?php
                        echo "None Provided";
                        }?>
                </div>
            </div>


            <div class="row" id="dataexample4">
                <div>
                    <label class="control-label"><b>Notes</b></label><br>
                </div>
                <div class="col-sm-12">
                    <textarea style="overflow: auto; border: none;" class="form-control" readonly="" name="notes" id="quotation_memo" rows="8"><?php echo $settings['notes']; ?></textarea>
                </div>
            </div>
            <table class="table table-striped">
            <tr>
                <td class="text-left">
                    <label for="inputEmail3" class="col-sm-3 control-label"><b>Receivers Sign :</b></label>
                </td>

                <td colspan="8"  class="text-right">
                    <label for="inputEmail3" class="col-sm-9 control-label"><b> Authorized Sign :</b></label>
                </td>
            </tr>
        </table> 

        <label class="control-label pull-left"><b>*(It is electronic generated invoice signatures may not appear).</b></label><br>

        </div>
    </body>
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
</html>