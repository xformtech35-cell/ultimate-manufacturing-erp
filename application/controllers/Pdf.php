<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Pdf extends MY_Controller
{

  protected $user_id;

  private function getGrnNumberFromUri($startSegment = 3, $endSegment = 6)
  {
    $segments = array();

    for ($segmentIndex = $startSegment; $segmentIndex <= $endSegment; $segmentIndex++) {
      $segmentValue = $this->uri->segment($segmentIndex);
      if ($segmentValue !== NULL && $segmentValue !== '') {
        $segments[] = $segmentValue;
      }
    }

    return implode('/', $segments);
  }

  private function getInvoiceNumberFromUri($startSegment = 3, $endSegment = 7)
  {
    $segments = array();

    for ($segmentIndex = $startSegment; $segmentIndex <= $endSegment; $segmentIndex++) {
      $segmentValue = $this->uri->segment($segmentIndex);
      if ($segmentValue !== NULL && $segmentValue !== '') {
        $segments[] = $segmentValue;
      }
    }

    $last_segment = end($segments);
    if ($last_segment !== false && in_array(strtolower($last_segment), array('yes', 'no'), true)) {
      array_pop($segments);
    }

    return implode('/', $segments);
  }

  function __construct()
  {
    parent::__construct();

    $this->load->library('session');

    $this->load->model('login', '', TRUE);
    $this->load->model('customer', '', TRUE);
    $this->load->model('salesreturn', '', TRUE);
    $this->load->model('estimate', '', TRUE);
    $this->load->model('inventory', '', TRUE);
    $this->load->model('invoice', '', TRUE);
    $this->load->model('supplier', '', TRUE);
    $this->load->model('salesorder', '', TRUE);
    $this->load->model('grn', '', TRUE);
    $this->load->model('bom', '', TRUE);
    $this->load->model('deliverychallan', '', TRUE);
    $this->load->library('form_validation');
    $this->load->model('proformainvoice', '', TRUE);
    $this->load->model('units', '', TRUE);
    $this->load->model('moc', '', TRUE);

    $this->load->model('requisition');
    $this->load->model('department');
    $this->load->model('user', '', TRUE);
    $this->load->model('joborder', '', TRUE);



    $session_data_head = $this->session->userdata('session_data_head');
    $this->user_id = $session_data_head['result']['user_id'];

    //        if($this->user_id === NULL) { 
    //            $this->session->sess_destroy();
    //            $this->session->set_flashdata('SUCCESSMSG', "You have been Logged Out !!");
    //            redirect('LoginController/logout');
    //        }
  }

  public function index()
  {
    $id = $this->uri->segment(3);
    $quote_number_id = $this->estimate->get_quotation_number_from_quotation_total($id, $this->user_id);
    $number = $quote_number_id['number_fk'];
    $data['show_salesorder'] = $this->estimate->get_estimates_data($number, $this->user_id);
    $data['settings'] = $this->login->get_settings($this->user_id);
    $data['salesorders_data_group'] = $this->estimate->get_estimates_data_group_by($number, $this->user_id);
    //print_r($data['salesorders_data_group']);sie();

    //$html = $this->load->view('admin/print', $data, true);
    $html = $this->load->view('admin/print_igst_salesorder', $data, true);
    // Create an instance of the class:
    $mpdf = new \Mpdf\Mpdf();

    $mpdf->SetHTMLHeader('<div>' . date("d-M-Y") . " - " . $number . '</div>');
    $mpdf->SetHTMLFooter('<div style="background-color: #f0f0ff; text-align: right">' . strtoupper($data['settings']['company_name']) . '   {PAGENO} of {nb}</div>');
    //call watermark content aand image
    $mpdf->SetWatermarkText($data['settings']['company_name']);
    $mpdf->showWatermarkText = true;
    $mpdf->watermarkTextAlpha = 0.1;
    $mpdf->watermark_font = 'DejaVuSansCondensed';
    $mpdf->WriteHTML($html);
    $pdfFilePath = "" . $data['estimates_data_group']['company_name'] . "-" . $number . ".pdf";
    //save the file put which location you need folder/filname
    $mpdf->Output($pdfFilePath, 'D');
    //out put in browser below output function
    //  $mpdf->Output();
  }    
public function print_igst_quote()
{
    $id = $this->input->get('quote_number_id');
    $sez = $this->input->get('sez');
    $stamp = $this->input->get('stamp'); // Get stamp parameter from GET

    $quote_number_id = $this->estimate->get_quotation_number_from_quotation_total($id, $this->user_id);
    $number = $quote_number_id['number_fk'];
    $data['show_quotation'] = $this->estimate->get_estimates_data($number, $this->user_id);
    $data['settings'] = $this->login->get_settings($this->user_id);
    $data['estimates_data_group'] = $this->estimate->get_estimates_data_group_by($number, $this->user_id);
    $data['stamp'] = isset($stamp) ? $stamp : 'yes'; // Default to 'yes' if not set

    // Create an instance of the class:
    $mpdf = new \Mpdf\Mpdf();
    $html = '';
    if ($sez == 'sez') {
        $html = $this->load->view('admin/print_igst_quote_sez', $data, true);
    } else {
        $html = $this->load->view('admin/print_igst_quote', $data, true);
        //call watermark content and image
        $mpdf->SetWatermarkText($data['settings']['company_name']);
    }

    $mpdf->SetHTMLHeader('<div>' . date("d-M-Y") . " - " . $number . '</div>');
    $mpdf->SetHTMLFooter('<div style="background-color: #f0f0ff; text-align: right">' . strtoupper($data['settings']['company_name']) . '   {PAGENO} of {nb}</div>');

    $mpdf->showWatermarkText = true;
    $mpdf->watermarkTextAlpha = 0.1;
    $mpdf->watermark_font = 'DejaVuSansCondensed';
    $mpdf->WriteHTML($html);
    $pdfFilePath = "" . $data['estimates_data_group']['company_name'] . "-" . $number . ".pdf";
    $mpdf->Output($pdfFilePath, 'D');
}
public function download_invoice()
{
  $segments = array();
  foreach ($this->uri->segment_array() as $segmentIndex => $segmentValue) {
    if ($segmentIndex >= 3 && $segmentValue !== NULL && $segmentValue !== '') {
      $segments[] = $segmentValue;
    }
  }

  $data['stamp'] = 'no';
  $last_segment = end($segments);
  if ($last_segment !== false && in_array(strtolower((string) $last_segment), array('yes', 'no'), true)) {
    $data['stamp'] = strtolower((string) array_pop($segments));
  }

  $user_id_send = $this->user_id;
  $invoice_number = implode('/', $segments);
  $invoice_data_group = $this->invoice->get_invoice_data_group_by($invoice_number, $user_id_send);

  if (empty($invoice_data_group) && count($segments) > 1) {
    $possible_user_id = end($segments);
    if (is_numeric($possible_user_id)) {
      array_pop($segments);
      $user_id_send = $possible_user_id;
      $invoice_number = implode('/', $segments);
      $invoice_data_group = $this->invoice->get_invoice_data_group_by($invoice_number, $user_id_send);
    }
  }

  $data['show_invoice'] = $this->invoice->get_invoice_data($invoice_number, $user_id_send);
  $data['invoice_data_group'] = $invoice_data_group;

  if (empty($data['invoice_data_group'])) {
    $this->session->set_flashdata('INFOMSG', 'Invoice not found: ' . htmlspecialchars($invoice_number));
    redirect('InvoiceController/index');
    return;
  }

  $data['settings'] = $this->login->get_settings($user_id_send);

  // Create an instance of the class:
  $mpdf = new \Mpdf\Mpdf();
  $html = $this->load->view('admin/invoice_print', $data, true);

  $invoice_header_date = 'N/A';
  if (!empty($data['invoice_data_group']['invoice_date'])) {
    $invoice_header_timestamp = strtotime($data['invoice_data_group']['invoice_date']);
    if ($invoice_header_timestamp !== false) {
      $invoice_header_date = date('d-M-Y', $invoice_header_timestamp);
    }
  }

  $mpdf->SetHTMLHeader('<div>' . $invoice_header_date . " - " . $invoice_number . '</div>');

  $mpdf->SetHTMLFooter('<div style="background-color: #f0f0ff; text-align: right">' . strtoupper($data['settings']['company_name']) . '   {PAGENO} of {nb}</div>');
  //call watermark content aand image
  $mpdf->SetWatermarkText($data['settings']['company_name']);

  $mpdf->showWatermarkText = true;
  $mpdf->watermarkTextAlpha = 0.1;
  $mpdf->watermark_font = 'DejaVuSansCondensed';
  $mpdf->WriteHTML($html);
  // $pdfFilePath = "" . $invoice_number . ".pdf";
  $pdfCompanyName = !empty($data['invoice_data_group']['company_name']) ? $data['invoice_data_group']['company_name'] : 'Invoice';
  $pdfFilePath = "" . $pdfCompanyName . "-" . $invoice_number . ".pdf";
  //save the file put which location you need folder/filname
  $mpdf->Output($pdfFilePath, 'D');
}
public function download_proforma_invoice()
{
    $invoice_number = $this->getInvoiceNumberFromUri();
    
    $data['show_invoice'] = $this->proformainvoice->get_proforma_invoice_data($invoice_number, $this->user_id);
    $data['invoice_data_group'] = $this->proformainvoice->get_proforma_invoice_data_group_by($invoice_number, $this->user_id);

    if (empty($data['invoice_data_group'])) {
      $this->session->set_flashdata('INFOMSG', 'Proforma invoice not found: ' . htmlspecialchars($invoice_number));
      redirect('ProformaInvoiceController/index');
      return;
    }

    $data['invoice_id'] = $this->proformainvoice->get_last_proforma_invoice_number($this->user_id);
    $data['settings'] = $this->login->get_settings($this->user_id);
    $data['stamp'] = "yes";  // Always set stamp to "yes"
    
    // Create an instance of the class:
    $mpdf = new \Mpdf\Mpdf();
    $html = $this->load->view('admin/proforma_invoice_print', $data, true);
    $mpdf->SetHTMLHeader('<div>' . date("d-M-Y") . " - " . $invoice_number . '</div>');
    $mpdf->SetHTMLFooter('<div style="background-color: #f0f0ff; text-align: right">' . strtoupper($data['settings']['company_name']) . '   {PAGENO} of {nb}</div>');
    //call watermark content aand image
    $mpdf->SetWatermarkText($data['settings']['company_name']);
    
    $mpdf->showWatermarkText = true;
    $mpdf->watermarkTextAlpha = 0.1;
    $mpdf->watermark_font = 'DejaVuSansCondensed';
    $mpdf->WriteHTML($html);
    $pdfCompanyName = !empty($data['invoice_data_group']['company_name']) ? $data['invoice_data_group']['company_name'] : 'Proforma-Invoice';
    $pdfFilePath = "" . $pdfCompanyName . "-" . $invoice_number . ".pdf";
    //save the file put which location you need folder/filname
    $mpdf->Output($pdfFilePath, 'D');
}
public function download_delivery_challan()
{
    $invoice_number = $this->input->get('invoice_number');
    $qty = $this->input->get('qty');
    $stamp = $this->input->get('stamp'); // Get stamp parameter from GET

    $data['show_invoice'] = $this->deliverychallan->get_delivery_challan_data($invoice_number, $this->user_id);
    $data['invoice_data_group'] = $this->deliverychallan->get_delivery_challan_data_group_by($invoice_number, $this->user_id);
    $data['invoice_id'] = $this->deliverychallan->get_last_delivery_challan_number($this->user_id);
    $data['settings'] = $this->login->get_settings($this->user_id);
    $data['stamp'] = isset($stamp) ? $stamp : 'yes'; // Default to 'yes' if not set

    // Create an instance of the class:
    $mpdf = new \Mpdf\Mpdf();

    $html = "";
    if ($qty == 'qty') {
        $html = $this->load->view('admin/delivery_challan_print_qty', $data, true);
    } else {
        $html = $this->load->view('admin/delivery_challan_print', $data, true);
    }

    $mpdf->SetHTMLHeader('<div>' . date("d-M-Y") . " - " . $invoice_number . '</div>');
    $mpdf->SetHTMLFooter('<div style="background-color: #f0f0ff; text-align: right">' . strtoupper($data['settings']['company_name']) . '   {PAGENO} of {nb}</div>');
    //call watermark content aand image
    $mpdf->SetWatermarkText($data['settings']['company_name']);

    $mpdf->showWatermarkText = true;
    $mpdf->watermarkTextAlpha = 0.1;
    $mpdf->watermark_font = 'DejaVuSansCondensed';
    $mpdf->WriteHTML($html);
    $pdfFilePath = "" . $data['invoice_data_group']['company_name'] . "-" . $invoice_number . ".pdf";
    //save the file put which location you need folder/filname
    $mpdf->Output($pdfFilePath, 'D');
}
public function download_po()
{
    $segments = array();
    foreach ($this->uri->segment_array() as $segmentIndex => $segmentValue) {
      if ($segmentIndex >= 3 && $segmentValue !== NULL && $segmentValue !== '') {
        $segments[] = $segmentValue;
      }
    }

    $data['stamp'] = 'yes';
    $last_segment = end($segments);
    if ($last_segment !== false && in_array(strtolower((string) $last_segment), array('yes', 'no'), true)) {
      $data['stamp'] = strtolower((string) array_pop($segments));
    }

    $user_id_send = $this->user_id;
    $po_number = implode('/', $segments);
    $po_data_group = $this->supplier->get_po_data_group_by($po_number, $user_id_send);

    if (empty($po_data_group) && count($segments) > 1) {
      $possible_user_id = end($segments);
      if (is_numeric($possible_user_id)) {
        array_pop($segments);
        $user_id_send = $possible_user_id;
        $po_number = implode('/', $segments);
        $po_data_group = $this->supplier->get_po_data_group_by($po_number, $user_id_send);
      }
    }

    $data['show_po'] = $this->supplier->get_po_data($po_number, $user_id_send);
    $data['po_data_group'] = $po_data_group;
    if (empty($data['po_data_group'])) {
      $this->session->set_flashdata('FAILMSG', 'Purchase order not found.');
      redirect('SupplierController/view_purchase_order');
      return;
    }

    $data['settings'] = $this->login->get_settings($user_id_send);
    
    // Create an instance of the class:
    $mpdf = new \Mpdf\Mpdf();
    $html = $this->load->view('admin/po_print', $data, true);
    $mpdf->SetHTMLHeader('<div>' . date("d-M-Y") . " - " . $po_number . '</div>');
    $mpdf->SetHTMLFooter('<div style="background-color: #f0f0ff; text-align: right">' . strtoupper($data['settings']['company_name']) . '   {PAGENO} of {nb}</div>');
    //call watermark content and image
    $mpdf->SetWatermarkText($data['settings']['company_name']);
    $mpdf->showWatermarkText = true;
    $mpdf->watermarkTextAlpha = 0.1;
    $mpdf->watermark_font = 'DejaVuSansCondensed';
    $mpdf->WriteHTML($html);
    $company_name = !empty($data['po_data_group']['company_name']) ? $data['po_data_group']['company_name'] : 'purchase-order';
    $pdfFilePath = "" . $company_name . "-" . $po_number . ".pdf";
    //save the file put which location you need folder/filename
    $mpdf->Output($pdfFilePath, 'D');
}
public function grn_pdf()
{
  $segments = array();
  foreach ($this->uri->segment_array() as $segmentIndex => $segmentValue) {
    if ($segmentIndex >= 3 && $segmentValue !== NULL && $segmentValue !== '') {
      $segments[] = $segmentValue;
    }
  }

  $user_id_send = $this->user_id;
  $grn_number = implode('/', $segments);
  $grn_data_group = $this->grn->get_grn_data_group_by($grn_number, $user_id_send);

  if (empty($grn_data_group) && count($segments) > 1) {
    $possible_user_id = end($segments);
    if (is_numeric($possible_user_id)) {
      array_pop($segments);
      $user_id_send = $possible_user_id;
      $grn_number = implode('/', $segments);
      $grn_data_group = $this->grn->get_grn_data_group_by($grn_number, $user_id_send);
    }
  }

  $data['show_grn']        = $this->grn->get_grn_data($grn_number, $user_id_send);
  $data['grn_data_group']  = $grn_data_group;
  $data['settings']        = $this->login->get_settings($user_id_send);



  // echo "sss";


  // die();

  // Render HTML content
  $html = $this->load->view('admin/grn_print', $data, true);

  // Load Composer Autoloader
  require_once APPPATH . '../vendor/autoload.php';

  // Create mPDF instance
  $mpdf = new \Mpdf\Mpdf([
    'mode' => 'utf-8',
    'format' => 'A4'
  ]);

  $mpdf->WriteHTML($html);

  // Fix filename to remove invalid slashes
  $fileName =  str_replace("/", "-", $grn_number) . ".pdf";

  // Output the file for download
  $mpdf->Output($fileName, 'D');
}
public function download_non_gst_quote()
{
    $id = $this->uri->segment(3);
    $id1 = $this->uri->segment(4);
    $id2 = $this->uri->segment(5);
    $id3 = $this->uri->segment(6);
    $id4 = $this->uri->segment(7); // Get stamp parameter from URI
    
    $number = $id . "/" . $id1 . "/" . $id2 . "/" . $id3;
    $data['show_quotation'] = $this->estimate->get_non_gst_estimates_data($number, $this->user_id);
    $data['settings'] = $this->login->get_settings($this->user_id);
    $data['estimates_data_group'] = $this->estimate->get_non_gst_estimates_data_group_by($number, $this->user_id);
    $data['stamp'] = isset($id4) ? $id4 : 'yes'; // Default to 'yes' if not set
    
    $html = $this->load->view('admin/print_non_gst_quote', $data, true);
    $pdfFilePath = "NGQuotation-" . $number . ".pdf";
    
    //load mPDF library
    $this->load->library('M_pdf');
    //generate the PDF from the given html
    $this->m_pdf->pdf->WriteHTML($html);
    //download it.
    $this->m_pdf->pdf->Output($pdfFilePath, "D");
}
public function print_igst_salesorder()
{
  $segments = array();
  foreach ($this->uri->segment_array() as $segmentIndex => $segmentValue) {
    if ($segmentIndex >= 3 && $segmentValue !== NULL && $segmentValue !== '') {
      $segments[] = $segmentValue;
    }
  }

  if (count($segments) > 1) {
    $user_id_send = array_pop($segments);
    $number = implode('/', $segments);
  } else {
    $id = $this->uri->segment(3);
    $user_id_send = $this->user_id;
    $salesorder_number_id = $this->salesorder->get_salesorder_number_from_salesorder_total($id, $user_id_send);
    if (empty($salesorder_number_id) || empty($salesorder_number_id['number_fk'])) {
      $this->session->set_flashdata('INFOMSG', 'Sales Order not found.');
      redirect('SalesOrderController/index');
      return;
    }

    $number = $salesorder_number_id['number_fk'];
  }

  $data['show_salesorder'] = $this->salesorder->get_salesorders_data($number, $user_id_send);
  $data['settings'] = $this->login->get_settings($user_id_send);
  $data['salesorders_data_group'] = $this->salesorder->get_salesorders_data_group_by($number, $user_id_send);
  if (empty($data['show_salesorder']) || empty($data['salesorders_data_group'])) {
    $this->session->set_flashdata('INFOMSG', 'Sales Order data not found.');
    redirect('SalesOrderController/index');
    return;
  }

  //print_r( $data['salesorders_data_group']);die();
  // Create an instance of the class:
  $data['stamp'] = "yes"; 
  $mpdf = new \Mpdf\Mpdf();
  $html = $this->load->view('admin/print_igst_salesorder', $data, true);


  $mpdf->SetHTMLHeader('<div>' . date("d-M-Y") . " - " . $number . '</div>');

  $mpdf->SetHTMLFooter('<div style="background-color: #f0f0ff; text-align: right">' . strtoupper($data['settings']['company_name']) . '   {PAGENO} of {nb}</div>');
  //call watermark content aand image
  $mpdf->SetWatermarkText($data['settings']['company_name']);

  $mpdf->showWatermarkText = true;
  $mpdf->watermarkTextAlpha = 0.1;
  $mpdf->watermark_font = 'DejaVuSansCondensed';
  $mpdf->WriteHTML($html);
  $pdfFilePath = 'SalesOrder_' . str_replace("/", "-", $number) . '_' . date('Ymd_His') . ".pdf";
  $outputMode = ($this->input->get('mode') === 'I') ? 'I' : 'D';
  //save the file put which location you need folder/filname
  $mpdf->Output($pdfFilePath, $outputMode);
}
public function print_bom()
{
    $id = $this->uri->segment(3);
    $id4 = $this->uri->segment(4); // Get stamp parameter from URI
    
    $bom_number_id = $this->bom->get_bom_number_from_bom_total($id, $this->user_id);
    $number = $bom_number_id['number_fk'];
    $data['show_bom'] = $this->bom->get_bom_data($number, $this->user_id);
    $data['settings'] = $this->login->get_settings($this->user_id);
    $data['bom_data_group'] = $this->bom->get_bom_data_group_by($number, $this->user_id);
    $data['unit_result'] = $this->units->get_units($this->user_id);
    $data['moc_result'] = $this->moc->get_moc($this->user_id);
    $data['stamp'] = isset($id4) ? $id4 : 'yes'; // Default to 'yes' if not set

    // Create an instance of the class:
    $mpdf = new \Mpdf\Mpdf();
    $html = $this->load->view('admin/print_bom', $data, true);

    $mpdf->SetHTMLHeader('<div>' . date("d-M-Y") . " - " . $number . '</div>');
    $mpdf->SetHTMLFooter('<div style="background-color: #f0f0ff; text-align: right">' . strtoupper($data['settings']['company_name']) . '   {PAGENO} of {nb}</div>');
    //call watermark content and image
    $mpdf->SetWatermarkText($data['settings']['company_name']);
    $mpdf->showWatermarkText = true;
    $mpdf->watermarkTextAlpha = 0.1;
    $mpdf->watermark_font = 'DejaVuSansCondensed';
    $mpdf->WriteHTML($html);
    $pdfFilePath = "" . $number . ".pdf";
    //save the file put which location you need folder/filename
    $mpdf->Output($pdfFilePath, 'D');
}
public function download_purchase_bill()
{
    $segments = array();
    foreach ($this->uri->segment_array() as $segmentIndex => $segmentValue) {
      if ($segmentIndex >= 3 && $segmentValue !== NULL && $segmentValue !== '') {
        $segments[] = $segmentValue;
      }
    }

    $data['stamp'] = 'yes';
    $last_segment = end($segments);
    if ($last_segment !== false && in_array(strtolower((string) $last_segment), array('yes', 'no'), true)) {
      $data['stamp'] = strtolower((string) array_pop($segments));
    }

    $user_id_send = $this->user_id;
    $po_number = implode('/', $segments);
    $purchase_bill_data_group = $this->supplier->get_purchase_bill_data_group_by($po_number, $user_id_send);

    if (empty($purchase_bill_data_group) && count($segments) > 1) {
      $possible_user_id = end($segments);
      if (is_numeric($possible_user_id)) {
        array_pop($segments);
        $user_id_send = $possible_user_id;
        $po_number = implode('/', $segments);
        $purchase_bill_data_group = $this->supplier->get_purchase_bill_data_group_by($po_number, $user_id_send);
      }
    }

    $data['show_purchase_bill'] = $this->supplier->get_purchase_bill_data($po_number, $user_id_send);
    $data['purchase_bill_data_group'] = $purchase_bill_data_group;

    if (empty($data['purchase_bill_data_group'])) {
      $this->session->set_flashdata('INFOMSG', 'Purchase voucher not found: ' . htmlspecialchars($po_number));
      redirect('SupplierController/view_purchase_bill');
      return;
    }

    $data['settings'] = $this->login->get_settings($user_id_send);
    
    // Create an instance of the class:
    $mpdf = new \Mpdf\Mpdf();
    $html = $this->load->view('admin/purchase_bill_print', $data, true);
    $mpdf->SetHTMLHeader('<div>' . date("d-M-Y") . " - " . $po_number . '</div>');
    $mpdf->SetHTMLFooter('<div style="background-color: #f0f0ff; text-align: right">' . strtoupper($data['settings']['company_name']) . '   {PAGENO} of {nb}</div>');
    //call watermark content and image
    $mpdf->SetWatermarkText($data['settings']['company_name']);
    $mpdf->showWatermarkText = true;
    $mpdf->watermarkTextAlpha = 0.1;
    $mpdf->watermark_font = 'DejaVuSansCondensed';
    $mpdf->WriteHTML($html);
    $company_name = !empty($data['purchase_bill_data_group']['company_name']) ? $data['purchase_bill_data_group']['company_name'] : 'Purchase-Voucher';
    $safe_file_name = preg_replace('/[^A-Za-z0-9._-]/', '_', $company_name . '-' . $po_number) . '.pdf';
    //save the file put which location you need folder/filename
    $mpdf->Output($safe_file_name, 'D');
}
public function download_purchase_return()
{
  $segments = array();
  foreach ($this->uri->segment_array() as $segmentIndex => $segmentValue) {
    if ($segmentIndex >= 3 && $segmentValue !== NULL && $segmentValue !== '') {
      $segments[] = $segmentValue;
    }
  }

  $user_id_send = $this->user_id;
  $po_number = implode('/', $segments);
  $purchase_return_data_group = $this->supplier->get_purchase_return_data_group_by($po_number, $user_id_send);

  if (empty($purchase_return_data_group) && count($segments) > 1) {
    $possible_user_id = end($segments);
    if (is_numeric($possible_user_id)) {
      array_pop($segments);
      $user_id_send = $possible_user_id;
      $po_number = implode('/', $segments);
      $purchase_return_data_group = $this->supplier->get_purchase_return_data_group_by($po_number, $user_id_send);
    }
  }

  $data['show_purchase_return'] = $this->supplier->get_purchase_return_data($po_number, $user_id_send);
  $data['purchase_return_data_group'] = $purchase_return_data_group;
  if (empty($data['purchase_return_data_group'])) {
    $this->session->set_flashdata('FAILMSG', 'Purchase return not found.');
    redirect('SupplierController/view_purchase_return');
    return;
  }

  $data['settings'] = $this->login->get_settings($user_id_send);
  // Create an instance of the class:
  $mpdf = new \Mpdf\Mpdf();
  $html = $this->load->view('admin/purchase_return_print', $data, true);
  $mpdf->SetHTMLHeader('<div>' . date("d-M-Y") . " - " . $po_number . '</div>');
  $mpdf->SetHTMLFooter('<div style="background-color: #f0f0ff; text-align: right">' . strtoupper($data['settings']['company_name']) . '   {PAGENO} of {nb}</div>');
  //call watermark content aand image
  $mpdf->SetWatermarkText($data['settings']['company_name']);
  $mpdf->showWatermarkText = true;
  $mpdf->watermarkTextAlpha = 0.1;
  $mpdf->watermark_font = 'DejaVuSansCondensed';
  $mpdf->WriteHTML($html);
  $company_name = !empty($data['purchase_return_data_group']['company_name']) ? $data['purchase_return_data_group']['company_name'] : 'Returnable-Challan';
  $safe_file_name = preg_replace('/[^A-Za-z0-9._-]/', '_', $company_name . '-' . $po_number) . '.pdf';
  //save the file put which location you need folder/filname
  $mpdf->Output($safe_file_name, 'D');
}
public function print_voucher()
{
  $invocie_pay_id = $this->input->get('invocie_pay_id');
  $flag = $this->input->get('flag');

  if ($flag == "in") {
    $data['show_voucher_data'] = $this->invoice->print_voucher_in($invocie_pay_id, $this->user_id);
  } else if ($flag == "out") {
    $data['show_voucher_data'] = $this->supplier->print_voucher_out($invocie_pay_id, $this->user_id);
  } else {
    $data['show_voucher_data'] = $this->invoice->print_voucher($invocie_pay_id, $this->user_id);
  }


  $data['settings'] = $this->login->get_settings($this->user_id);
  $mpdf = new \Mpdf\Mpdf();

  if ($flag == "") {
    $html = $this->load->view('admin/print_voucher', $data, true);
  } else {
    $data['flag'] =  $flag;
    $html = $this->load->view('admin/print_voucher_payemt_in', $data, true);
  }


  $mpdf->SetHTMLHeader('<div> Date ' . date("d-M-Y") . '</div>');
  $mpdf->showWatermarkText = false;
  $mpdf->watermarkTextAlpha = 0.1;
  $mpdf->watermark_font = 'DejaVuSansCondensed';
  $mpdf->WriteHTML($html);
  $pdfFilePath = "Voucher.pdf";
  //save the file put which location you need folder/filname
  $mpdf->Output($pdfFilePath, 'D');
}
public function print_proforma_voucher()
{
  $invocie_pay_id = $this->input->get('invocie_pay_id');


  $data['show_voucher_data'] = $this->proformainvoice->print_proforma_voucher($invocie_pay_id, $this->user_id);
  //  print_r($data['show_voucher_data']);die();
  //$data['purchase_return_data_group'] = $this->supplier->get_purchase_return_data_group_by($po_number, $this->user_id);
  $data['settings'] = $this->login->get_settings($this->user_id);
  // print_r($data['settings']);die();
  // Create an instance of the class:
  $mpdf = new \Mpdf\Mpdf();
  $html = $this->load->view('admin/print_proforma_voucher', $data, true);
  $mpdf->SetHTMLHeader('<div> Date ' . date("d-M-Y") . '</div>');
  //  $mpdf->SetHTMLFooter('<div style="background-color: #f0f0ff; text-align: right">' . strtoupper($data['settings']['company_name']) . '   {PAGENO} of {nb}</div>');
  //call watermark content aand image
  // $mpdf->SetWatermarkText($data['settings']['company_name']);
  $mpdf->showWatermarkText = false;
  $mpdf->watermarkTextAlpha = 0.1;
  $mpdf->watermark_font = 'DejaVuSansCondensed';
  $mpdf->WriteHTML($html);
  $pdfFilePath = "Voucher.pdf";
  //save the file put which location you need folder/filname
  $mpdf->Output($pdfFilePath, 'D');
}
public function download_sales_return()
{
  $segments = array();
  foreach ($this->uri->segment_array() as $segmentIndex => $segmentValue) {
    if ($segmentIndex >= 3 && $segmentValue !== NULL && $segmentValue !== '') {
      $segments[] = $segmentValue;
    }
  }

  $user_id_send = $this->user_id;
  $po_number = implode('/', $segments);
  $sales_return_data_group = $this->salesreturn->get_sales_return_data_group_by($po_number, $user_id_send);

  if (empty($sales_return_data_group) && count($segments) > 1) {
    $possible_user_id = end($segments);
    if (is_numeric($possible_user_id)) {
      array_pop($segments);
      $user_id_send = $possible_user_id;
      $po_number = implode('/', $segments);
      $sales_return_data_group = $this->salesreturn->get_sales_return_data_group_by($po_number, $user_id_send);
    }
  }

  $data['show_sales_return'] = $this->salesreturn->get_sales_return_data($po_number, $user_id_send);
  $data['sales_return_data_group'] = $sales_return_data_group;

  if (empty($data['sales_return_data_group'])) {
    $this->session->set_flashdata('INFOMSG', 'Sales return not found: ' . htmlspecialchars($po_number));
    redirect('SalesReturnController/view_sales_return');
    return;
  }

  $data['settings'] = $this->login->get_settings($user_id_send);

  // Create an instance of the class:
  $mpdf = new \Mpdf\Mpdf();
  $html = $this->load->view('admin/sales_return_print', $data, true);
  $mpdf->SetHTMLHeader('<div>' . date("d-M-Y") . " - " . $po_number . '</div>');
  $mpdf->SetHTMLFooter('<div style="background-color: #f0f0ff; text-align: right">' . strtoupper($data['settings']['company_name']) . '   {PAGENO} of {nb}</div>');
  //call watermark content aand image
  $mpdf->SetWatermarkText($data['settings']['company_name']);
  $mpdf->showWatermarkText = true;
  $mpdf->watermarkTextAlpha = 0.1;
  $mpdf->watermark_font = 'DejaVuSansCondensed';
  $mpdf->WriteHTML($html);
  
  $pdfCompanyName = !empty($data['sales_return_data_group']['company_name']) ? $data['sales_return_data_group']['company_name'] : 'Sales-Return';
  $pdfFilePath = "" . $pdfCompanyName . "-" . $po_number . ".pdf";

  //save the file put which location you need folder/filname
  $mpdf->Output($pdfFilePath, 'D');
}
public function print_joborder()
{
  $id = $this->uri->segment(3);
  $joborder_number_id = $this->joborder->get_joborder_number_from_joborder_total($id, $this->user_id);
  $number = $joborder_number_id['number_fk'];
  $data['show_joborder'] = $this->joborder->get_joborder_data($number, $this->user_id);
  $data['settings'] = $this->login->get_settings($this->user_id);
  $data['joborder_data_group'] = $this->joborder->get_joborder_data_group_by($number, $this->user_id);
  $data['unit_result'] = $this->units->get_units($this->user_id);

  // Create an instance of the class:
  $mpdf = new \Mpdf\Mpdf();
  $html = $this->load->view('joborder/print_joborder', $data, true);

  $mpdf->SetHTMLHeader('<div>' . date("d-M-Y") . " - " . $number . '</div>');

  $mpdf->SetHTMLFooter('<div style="background-color: #f0f0ff; text-align: right">' . strtoupper($data['settings']['company_name']) . '   {PAGENO} of {nb}</div>');
  //call watermark content aand image
  $mpdf->SetWatermarkText($data['settings']['company_name']);

  $mpdf->showWatermarkText = true;
  $mpdf->watermarkTextAlpha = 0.1;
  $mpdf->watermark_font = 'DejaVuSansCondensed';
  $mpdf->WriteHTML($html);
  $pdfFilePath = "" . $number . ".pdf";
  //save the file put which location you need folder/filname
  $mpdf->Output($pdfFilePath, 'I');
}
public function download_requisition($pr_id)
{
  // Fetch master requisition data
  $data['requisition'] = $this->requisition->get_requisition_by_id($pr_id);

  // Fetch related items
  $data['requisition_items'] = $this->requisition->get_requisition_items($pr_id);



  // var_dump($data['requisition_items']);

  // die();

  // Department & user info
  $data['department_result'] = $this->department->get_departments();
  $data['users'] = $this->user->get_user("user");

  // Settings
  $data['settings'] = $this->login->get_settings($this->user_id);

  // Load HTML view
  $html = $this->load->view('requisition/print_requisition', $data, true);

  // Initialize mPDF with professional margins
  $mpdf = new \Mpdf\Mpdf([
    'margin_top' => 45,
    'margin_bottom' => 22,
    'margin_left' => 12,
    'margin_right' => 12,
    'default_font' => 'dejavusans'
  ]);

  // STYLISH HEADER
  $mpdf->SetHTMLHeader('
      <div style="background:#003d6b; color:#fff; padding:10px; font-size:15px; font-weight:bold;">
          PURCHASE REQUISITION
          <span style="float:right; font-size:12px;">Date: ' . date("d-M-Y") . ' | PR NO: ' . $data["requisition"]->pr_no . '</span>
      </div>
  ');

  // STYLISH FOOTER
  $mpdf->SetHTMLFooter('
      <div style="background:#f0f4ff; padding:8px; font-size:10px; text-align:right; color:#333;">
          ' . strtoupper($data["settings"]["company_name"]) . ' | Page {PAGENO} of {nb}
      </div>
  ');

  // WATERMARK
  $mpdf->SetWatermarkText($data["settings"]["company_name"]);
  $mpdf->showWatermarkText = true;
  $mpdf->watermarkTextAlpha = 0.06;

  // MAIN HTML CONTENT
  $mpdf->WriteHTML($html);

  $fileName = $data["requisition"]->pr_no . " " . $pr_no . ".pdf";

  // DOWNLOAD PDF
  return $mpdf->Output($fileName, "D");
}
    private function prepare_slip_items($issue_slip)
    {
        $items = $issue_slip['items'] ?? [];
        $joborder_number = $issue_slip['joborder_number'] ?? null;

        if (!empty($joborder_number)) {
            // Fetch all items from the Job Order
            $joborder_items = $this->db
                ->select('j.*, MIN(i.inventory_id) as inventory_id, MIN(i.stock) as current_stock, MIN(i.unit) as unit, MIN(i.item_name) as item_name, MIN(i.code) as code')
                ->from('joborder j')
                ->join('inventory i', 'i.code = j.product_name OR i.item_name = j.product_name', 'left')
                ->where('j.number', $joborder_number)
                ->group_by('j.joborder_id')
                ->get()
                ->result_array();

            $issued_items_map = [];
            foreach ($items as $item) {
                $issued_items_map[$item['inventory_id_fk']] = $item;
            }

            $merged_items = [];
            foreach ($joborder_items as $jo_item) {
                $inv_id = $jo_item['inventory_id'] ?? null;
                if (!$inv_id) {
                    continue;
                }

                // Get total issued quantity for this item across all slips
                $issued_qty = floatval($this->material_issue_model->get_issued_quantity_for_inventory($inv_id, $joborder_number));
                $required_qty = floatval($jo_item['quantity']);
                $pending_qty = max(0, $required_qty - $issued_qty);

                if (isset($issued_items_map[$inv_id])) {
                    $item_data = $issued_items_map[$inv_id];
                } else {
                    $item_data = [
                        'issue_item_id' => 0,
                        'issue_id' => $issue_slip['issue_id'],
                        'inventory_id_fk' => $inv_id,
                        'quantity' => 0.00,
                        'unit_price' => 0.00,
                        'total_amount' => 0.00,
                        'remarks' => '',
                        'code' => $jo_item['code'] ?? $jo_item['product_name'],
                        'item_name' => $jo_item['item_name'] ?? $jo_item['product_name'],
                        'unit' => $jo_item['unit'] ?? 'QTY',
                        'current_stock' => $jo_item['current_stock'] ?? 0.00
                    ];
                }

                $item_data['required_qty'] = $required_qty;
                $item_data['fulfilled_qty'] = $issued_qty;
                $item_data['pending_qty'] = $pending_qty;
                $item_data['current_stock'] = $jo_item['current_stock'] ?? 0.00;

                $merged_items[] = $item_data;
            }

            return $merged_items;
        } else {
            // No job order, keep standard behavior and calculate stock/pending
            foreach ($items as $index => $item) {
                $current_stock = isset($item['current_stock']) ? floatval($item['current_stock']) : 0;
                $issued_qty = floatval($item['quantity']);
                $pending_qty = max(0, $current_stock - $issued_qty);

                $items[$index]['required_qty'] = 0;
                $items[$index]['fulfilled_qty'] = $issued_qty;
                $items[$index]['pending_qty'] = $pending_qty;
            }
            return $items;
        }
    }

public function download_material_issue($issue_id)
{
    $this->load->model('Material_issue_model', 'material_issue_model');
    $data['issue_slip'] = $this->material_issue_model->get_issue_slip($issue_id);
    if (!$data['issue_slip']) {
        show_404();
    }

    // Populate required, fulfilled and pending quantities for PDF export
    $data['issue_slip']['items'] = $this->prepare_slip_items($data['issue_slip']);
    $data['items'] = $data['issue_slip']['items'];
    $data['total_qty'] = $data['issue_slip']['total_qty'];
    $data['items_count'] = count($data['items']);

    $data['settings'] = $this->login->get_settings($this->user_id);
    $data['stamp'] = 'yes';
    $html = $this->load->view('material_issue/print', $data, true);
    
    // Create mPDF instance
    $mpdf = new \Mpdf\Mpdf();
    $mpdf->SetHTMLHeader('<div>' . date("d-M-Y") . " - " . $data['issue_slip']['issue_no'] . '</div>');
    $mpdf->SetHTMLFooter('<div style="background-color: #f0f0ff; text-align: right">' . strtoupper($data['settings']['company_name']) . '   {PAGENO} of {nb}</div>');
    $mpdf->SetWatermarkText($data['settings']['company_name']);
    $mpdf->showWatermarkText = true;
    $mpdf->watermarkTextAlpha = 0.1;
    $mpdf->watermark_font = 'DejaVuSansCondensed';
    $mpdf->WriteHTML($html);
    
    $filename = "Material-Issue-" . str_replace("/", "-", $data['issue_slip']['issue_no']) . ".pdf";
    $mpdf->Output($filename, 'D');
}
}
