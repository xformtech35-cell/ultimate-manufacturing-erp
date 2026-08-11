<?php
//print_r($invoices);die();

$session_data_head1 = $this->session->userdata('session_data_head');
if (isset($session_data_head1)) {
    
} else {
    header($this->config->item('header'));
}
$session_data_head2 = $this->session->userdata('session_data_head2');
$set_cc_email = $session_data_head2['cc_email'];
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<body class="hold-transition skin-blue sidebar-mini">
    <div id="loader" class="center"></div>
    <div class="wrapper">  

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    Credit/Debit Notes
                </h1>
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url() . 'Home/index/' ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                    <!-- <li><a href="#">GST Invoice</a></li>
                    <li class="active">GST Invoice Details</li> -->
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-info">

                            <div class="box-header">
                                <div class="row">
                                    <div class="col-md-4">
                                        <h3 class="box-title">Credit/Debit Notes</h3>
                                    </div>
                                    <div class="col-md-5">
                                        <form action="<?php echo base_url(); ?>CreditnoteController/get_datewise_record" method="post" style="margin:0; display:flex; gap:6px; align-items:center;">
                                            <div class="input-group input-group-sm" style="width: 150px;">
                                                <span class="input-group-addon" style="height: 30px; padding: 5px 6px;"><i class="fa fa-calendar"></i> From</span>
                                                <input type="text" class="form-control backdate input-sm" name="from_date" id="from_date" value="<?php echo isset($from_date) ? htmlspecialchars($from_date) : ''; ?>" onkeydown="return false;" autocomplete="off" required placeholder="From Date" style="height: 30px;">
                                            </div>
                                            <div class="input-group input-group-sm" style="width: 150px;">
                                                <span class="input-group-addon" style="height: 30px; padding: 5px 6px;"><i class="fa fa-calendar"></i> To</span>
                                                <input type="text" class="form-control backdate input-sm" name="to_date" id="to_date" value="<?php echo isset($to_date) ? htmlspecialchars($to_date) : ''; ?>" onkeydown="return false;" autocomplete="off" required placeholder="To Date" style="height: 30px;">
                                            </div>
                                            <button class="btn btn-primary btn-sm" name="submit" value="" type="submit" style="height: 30px; padding: 5px 12px; font-weight: 600; border: none; border-radius: 4px;"><i class="fa fa-filter"></i> Filter</button>
                                        </form>
                                    </div>
                                    <div class="col-md-3">
                                        <!-- <a href="<?php echo base_url(); ?>CreditnoteController/index?str=All"><button
                                                class="btn btn-success btn-sm"> Show All Invoice</button></a> -->
                                        <a href="<?php echo base_url(); ?>CreditnoteController/create_note"><button
                                                class="btn btn-success btn-sm"><i
                                                    class="glyphicon glyphicon-plus"></i>Create Invoice</button></a>
                                    </div>
                                </div>
                            </div>
                            <ul class="nav nav-tabs">
                                <!-- <li class="nav-item">
                                    <a class="nav-link"
                                        href="<?php echo base_url(); ?>InvoiceController/get_invoice_data_by_status/2">Sent
                                        <span class="badge badge-light"> <?php echo $invoice_sent_count; ?></span></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link"
                                        href="<?php echo base_url(); ?>InvoiceController/get_invoice_data_by_status/1">Draft
                                        <span class="badge badge-light"> <?php echo $invoice_draft_count; ?></span></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link active"
                                        href="<?php echo base_url(); ?>InvoiceController/index?str=All">All Invoices
                                        <span class="badge badge-light"> <?php echo $invoice_count; ?></span></a>
                                </li> -->
                            </ul>
                            <!-- /.box-header -->
                            <div class="box-body">

                                <!-- Start Flash Message -->
                                <?php if ($this->session->flashdata('SUCCESSMSG')) { ?>
                                <div role="alert" class="alert alert-success">
                                    <button data-dismiss="alert" class="close" type="button"><span
                                            aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                                    <strong>Well done!!</strong> <?= $this->session->flashdata('SUCCESSMSG') ?>
                                </div>
                                <?php } ?>

                                <?php if ($this->session->flashdata('INFOMSG')) { ?>
                                <div role="alert" class="alert alert-info">
                                    <button data-dismiss="alert" class="close" type="button"><span
                                            aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                                    <strong>Oops!!</strong> <?= $this->session->flashdata('INFOMSG') ?>
                                </div>
                                <?php } ?>
                                <!-- End Flash Message -->

                                <table id="example3" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Sr.No.</th>
                                            <th>Note Date</th>
                                            <th>Doc. No.</th>
                                            <th>Doc. Date</th>
                                            <th>Type</th>
                                            <th>Voucher Type</th>
                                            <th>Account Name</th>
                                            <th>Credit</th>
                                            <th>Debit</th>
                                            <th>Note No.</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $unpaid = 0; ?>
                                        <?php
                                        $i = 1;
                                        
                                        foreach ($notes as $key) {
                                            ?>
                                        <tr>

                                            <td>
                                                <?php echo $i; ?>
                                            </td>
                                            <td>
                                                <?php echo $key->credit_date; ?>
                                            </td>
                                            <td>
                                            <?php echo $key->doc_no; ?> 
                                            </td>
                                            <td> 
                                            <?php echo $key->doc_date; ?>
                                            </td> 
                                            <td> 
                                            <?php echo $key->type; ?>
                                            </td> 
                                            <td> 
                                            <?php echo $key->acc_type; ?>
                                            </td> 
                                            <td> 
                                            <?php echo $key->company_name; ?>
                                            </td> 
                                            <td> 
                                            <?php echo $key->credit_amt; ?>
                                            </td> 
                                            <td> 
                                            <?php echo $key->debit_amt; ?>
                                            </td> 
                                            <td> 
                                            <?php echo $key->credit_no; ?>
                                            </td> 
                                            <td>
    <!-- Send Email Icon -->
    <a class="view-modal-invoice-email-send" data-toggle="modal" data-href="#" data-id="<?php echo $key->invoice_number; ?>" data-target="#emailModal" title="Send Email">
        <i class="fa fa-envelope" title="Send Email"></i>
    </a>

    <!-- Edit Icon -->
    <a href="<?php echo base_url() . 'CreditnoteController/edit_note_details?invoice_number=' . $key->invoice_number; ?>" title="Edit">
        <i class="fa fa-edit" title="Edit"></i>
    </a>

    <!-- Export As PDF Icon -->
    <a href="<?php echo base_url() . 'Pdf/download_note?invoice_number=' . $key->invoice_number ?>" name="btn_submit" id="download1" title="Export As PDF">
        <i class="fa fa-file-pdf-o" title="Export As PDF"></i>
    </a>

    <!-- Delete Icon -->
    <a href="<?php echo base_url() . 'InvoiceController/delete_invoice_by_invoice_number?invoice_number=' . $key->invoice_number; ?>" onClick="return confirm('Are you sure you want to delete?')" title="Delete">
        <i class="fa fa-trash" title="Delete"></i>
    </a>
</td>

                                        </tr>
                                        <?php
                                            $i++;
                                        }
                                        ?>
                                    </tbody>
                                </table>
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
        <?php $this->load->view('admin/footer'); ?>
        <div class="control-sidebar-bg"></div>
    </div>

    <!-- ./Customer modal -->
    <div id="modal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header btn-danger">
                    <center>
                        <h4 class="modal-title">Enter Payment<button type="button" class="close"
                                data-dismiss="modal">&times;</button></h4>
                    </center>
                </div>
                <form class="form-horizontal balance-check form_overlay" method="post"
                    action="<?php echo base_url(); ?>InvoiceController/edit_invoice_payment">
                    <div class="modal-body">

                        <div class="card-body ">

                            <!-- form start -->
                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label">Balance<span
                                        style="color: red;">*</span></label>
                                <div class="col-sm-7">
                                    <input type="hidden" class="form-control" name="id" id="id" value="">
                                    <input type="hidden" class="form-control" name="total" id="total" value="">
                                    <input type="hidden" class="form-control" name="invoice_number" id="invoice_number"
                                        value="">
                                    <input type="hidden" class="form-control" name="customer_id_fk" id="customer_id_fk">
                                    <input type="text" readonly="" class="form-control input-sm" name="balance"
                                        id="balance" required="">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label">Payment Type<span
                                        style="color: red;">*</span></label>
                                <div class="col-sm-7">
                                    <select class="form-control input-sm " name="payment_type" id="payment_type"
                                        required="">
                                        <option value="">Select Payment Type</option>
                                        <option value="Advance">Advance</option>
                                        <option value="Partial">Partial</option>
                                        <option value="Final">Final</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label">Paying Amount<span
                                        style="color: red;">*</span></label>
                                <div class="col-sm-7">
                                    <input type="number" class="form-control allownumericwithdecimal" name="paid"
                                        id="paid" value="" required="" min=0 oninput="validity.valid||(value='');">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label">Payment Method<span
                                        style="color: red;">*</span></label>
                                <div class="col-sm-7">
                                    <select class="form-control input-sm " name="payment_method" id="payment_method"
                                        required="">
                                        <option value="">Select Payment Method</option>
                                        <option value="1">Cash</option>
                                        <option value="2">Cheque</option>
                                        <option value="3">NetBanking</option>
                                        <option value="4">Credit Card</option>
                                        <option value="5">Other</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label">Note<span
                                        style="color: red;">*</span></label>
                                <div class="col-sm-7">
                                    <textarea class="form-control input-sm" required="" name="note" id="note"
                                        rows="3"></textarea>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label">Payment Date<span
                                        style="color: red;">*</span></label>
                                <div class="col-sm-7">
                                    <input type="text" class="form-control backdate" name="date" id="date" value=""
                                        required="" onkeydown="return false;" autocomplete="off">
                                </div>
                            </div>


                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label">Bank Name</label>
                                <div class="col-sm-7">
                                    <select class="form-control input-sm " name="bank_name" id="bank_name">
                                        <option value="">Select Payment Method</option>
                                        <option value="HDFC">HDFC Bank</option>
                                        <option value="SBI">SBI Bank</option>
                                        <option value="Saraswat">Saraswat Bank</option>
                                        <option value="IDBI">IDBI Bank</option>
                                        <option value="ICICI">ICICI Bank</option>
                                        <option value="AXIS">AXIS Bank</option>
                                        <option value="RBL">RBL Bank</option>
                                        <option value="INDUS">INDUS Bank</option>
                                        <option value="BOM">BOM Bank</option>

                                    </select>
                                </div>
                            </div>


                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="submit" id="btnSave" class="btn btn-success">Submit</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i
                                class="glyphicon glyphicon-remove"></i> Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Email modal -->
    <div id="emailModal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header btn-danger">
                    <center>
                        <h4 class="modal-title">Send Invoice<button type="button" class="close"
                                data-dismiss="modal">&times;</button></h4>
                    </center>
                </div>
                <form class="form-horizontal form_overlay" method="post"
                    action="<?php echo base_url(); ?>InvoiceController/send_invoice_email"
                    enctype="multipart/form-data">
                    <div class="modal-body">

                        <div class="card-body ">

                            <!-- form start -->
                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label">To<span
                                        style="color: red;">*</span></label>
                                <div class="col-sm-7">
                                    <input type="hidden" class="form-control" name="customer_id" id="customer_id"
                                        value="" required="">
                                    <input type="hidden" class="form-control" name="invoice_number" id="invoice_number"
                                        required="">
                                    <input type="email" class="form-control input-sm" name="to_email" id="to_email"
                                        required="">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label">Subject<span
                                        style="color: red;">*</span> </label>
                                <div class="col-sm-7">
                                    <textarea class="form-control input-sm" name="subject" id="subject" rows="2"
                                        required=""></textarea>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label">Message </label>
                                <div class="col-sm-7">
                                    <textarea class="form-control input-sm" name="message" id="message"
                                        rows="3"></textarea>
                                </div>
                            </div>


                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label">Send a copy to</label>
                                <div class="col-sm-7">
                                    <input type="checkbox" name="copy_email" id="copy_email">
                                    <?php echo $set_cc_email; ?>

                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 control-label">To<span
                                        style="color: red;">*</span></label>
                                <div class="col-sm-7">
                                    <input type="hidden" class="form-control" name="invoice_number" id="invoice_number"
                                        required="">
                                    <input type="text" class="form-control input-sm" name="mobile" id="mobile"
                                        required="">
                                    <!--
<textarea class="form-control input-sm" name="mobile" id="mobile" rows="1"> <?php echo $key->mobile; ?>  </textarea>-->-->
                                </div>
                            </div>



                        </div>
                    </div>
                    <div class="modal-footer">
                        <a href="" id="urlSet" title="Share on whatsapp">WhatsApp</a>
                        <button type="submit" id="btnSave" class="btn btn-success"><i class="fa fa-paper-plane"
                                aria-hidden="true"></i>
                            Send</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i
                                class="glyphicon glyphicon-remove"></i> Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- ./ End Email modal -->