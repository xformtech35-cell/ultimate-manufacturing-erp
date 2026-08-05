<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title> Quotation </title>
        <meta name="description" content="Quotation print page">
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
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
        <div class="container main print shadows1" id="print" >

            <div class="row" class="shadows1">


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
                                
                                <?php $url = base_url() . 'Pdf/download_non_gst_quote/' . $non_gst_estimates_data_group['number']; ?>
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
                </div>
            </div>
            <hr>
            <div class="row">
                <div>
                    <div class="col-md-8">

                        <div>
                            <div class="col-sm-8">
                                <span class="wv-text--strong"><b>TO:</b></span>
                                <!--<label for="inputEmail3" id="subheading1"><b>BILL TO:</b></label>-->
                            </div>
                        </div>
                        <div>
                            <div class="col-sm-8">
                                <?php echo $non_gst_estimates_data_group['company_name']; ?>
                            </div>
                        </div>

                        <div>
                            <div class="col-sm-8">
                                <?php echo $non_gst_estimates_data_group['address']; ?>
                            </div>
                        </div>
                        <div>
                            <div class="col-sm-8">
                                <span class="wv-text--strong"><b>GST Number:</b><?php if ($non_gst_estimates_data_group['gst']) { ?>
                                                <?php echo $non_gst_estimates_data_group['gst']; ?>
                                            <?php } else { ?>
                                                <?php echo "None Provided";
                                            } ?>
                                </span>
                            </div>
                        </div>

                        <div>
                            <div class="col-sm-8">
                                <span class="wv-text--strong"><b>PAN Number:</b><?php if ($non_gst_estimates_data_group['pancard']) { ?>
                                                <?php echo $non_gst_estimates_data_group['pancard']; ?>
                                            <?php } else { ?>
                                                <?php echo "None Provided";
                                            } ?>
                                </span>
                            </div>
                        </div>

                        <div>
                            <div class="col-sm-8">
                                <b>Customer Name: <?php echo $non_gst_estimates_data_group['fullname']; ?></b><br>
                            </div>
                        </div>

 

                    </div>

                    <div class="col-md-4">
                        <div>
                            <label class="control-label"><b>Quotation Number:</b></label><?php echo $non_gst_estimates_data_group['number']; ?>
                        </div>

                        <div>
                            <label class="control-label"><b>Quotation Date:</b></label><?php echo $non_gst_estimates_data_group['date']; ?>
                        </div>

                        <div>
                            <label class="control-label"><b>Expires on: </b></label><?php echo $non_gst_estimates_data_group['exp_date']; ?>
                        </div>

                        <div>
                            <span class="non_gst_total"><label class="control-label non_gst_total"><b>Grand Total (INR): </b></label><?php echo number_format($non_gst_estimates_data_group['total'], 2); ?></span>
                        </div>

                        <div>
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

            <div class="row">
                <div class="col-sm-12">
                    <div class="panel panel-default">
                        
                        <div id="dataexample2">
                            <table  class="table wv-table__row"> 
                                <thead style="background-color: #444444; color: white">
                                    <tr>
                                        <th>Sr.No</th>
                                        <th>Item</th>
                                        <th>Description</th>
                                        <th>MOQ</th>
                                        <th>HSN Code</th>
                                        <th>Price/Unit</th>
                                        <th>Discount(%)</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>

                                <?php
                                $i = 1;
                                foreach ($show_non_gst_quotation as $key) {
                                    ?>
                                    <tr> 
                                         <td><span id="" class=""></span>
                                             <?php echo $i; ?>
                                             </td>
                                        <td><span id="" class=""></span>
                                            <?php echo $key->product_name; ?><input type="hidden" name="term[]" value="<?php echo $key->product_name; ?>" id="item_name<?php echo $i; ?>" class="form-control product_name_auto" /><input type="hidden" class="form-control input-sm"   name="quotation_id[]" id="quotation_id<?php echo $i; ?>"  value="<?php echo $key->quotation_id; ?>">
                                        </td>
                                        
                                        <td><span id="" class=""></span>
                                <?php echo $key->description; ?><input type="hidden" name="description[]" value="<?php echo $key->description; ?>" id="description<?php echo $i; ?>" class="form-control input-sm name_list quantity_auto" />
                            </td>
                                        
                                        <td><span id="" class=""></span>
                                            <?php echo $key->quantity; ?><input type="hidden" name="quantity[]" value="<?php echo $key->quantity; ?>" id="quantity<?php echo $i; ?>" class="form-control quantity_auto" value="1" />
                                        </td>
                                        <td><span id="" class=""></span>
                                            <?php echo $key->hsn_code; ?><input type="hidden" name="hsn[]" value="<?php echo $key->hsn_code; ?>" id="hsn<?php echo $i; ?>" class="form-control name_list" /> 
                                        </td>
                                    <td><span id="" class=""></span>
                                        <?php echo number_format($key->price,2); ?><input type="hidden" name="price[]" value="<?php echo $key->price; ?>" id="price<?php echo $i; ?>" class="form-control name_list"/>
                                    </td>
                                    <td><span id="" class=""></span>
                                        <?php echo $key->discount; ?><input type="hidden" name="discount[]" value="<?php echo $key->discount; ?>" id="discount<?php echo $i; ?>" class="form-control name_list"/> %
                                    </td>
                                    <td><span id="" class=""></span>
                                        <?php echo number_format($key->amount, 2); ?><input type="hidden" name="amount[]" id="amount<?php echo $i; ?>" class="form-control name_list amount_auto" value="<?php echo $key->amount; ?>"/>

                                    </td>
                                    </tr>  
                                    <?php
                                    $i++;
                                }
                                ?>
                            </table>   
                        </div>

                    </div>
                </div>
            </div>

            <div class="row" id="dataexample3">
                <div class="col-md-10">

                </div>
                <div class="col-md-2">
                    <label class="control-label"><b>Grand Total (INR): </b></label><?php echo number_format($non_gst_estimates_data_group['total'], 2); ?>
                </div>
            </div>

            <hr>

            <div class="row" id="dataexample4">
                <div>
                    <label class="control-label"><b>Notes</b></label><br>
                </div>
                <div class="col-sm-12">
                    <textarea style="overflow: auto; border: none;" class="form-control" readonly="" name="notes" id="quotation_memo" rows="8"><?php echo $settings['notes']; ?></textarea><br>
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

        <label class="control-label pull-left"><b>*(It is electronic generated quotation signatures may not appear).</b></label><br>
        </div>
    </body>

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

</html>
