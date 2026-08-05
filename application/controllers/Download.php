<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class Download extends MY_Controller {

    private function getNumberAndUserIdFromUri($startSegment = 3) {
        $segments = $this->uri->segment_array();
        $parts = array();

        foreach ($segments as $segmentIndex => $segmentValue) {
            if ($segmentIndex >= $startSegment && $segmentValue !== NULL && $segmentValue !== '') {
                $parts[] = $segmentValue;
            }
        }

        $user_id_send = array_pop($parts);

        return array(
            'number' => implode('/', $parts),
            'user_id_send' => $user_id_send
        );
    }

    private function getGrnNumberFromUri($startSegment = 3, $endSegment = 6) {
        $segments = array();

        for ($segmentIndex = $startSegment; $segmentIndex <= $endSegment; $segmentIndex++) {
            $segmentValue = $this->uri->segment($segmentIndex);
            if ($segmentValue !== NULL && $segmentValue !== '') {
                $segments[] = $segmentValue;
            }
        }

        return implode('/', $segments);
    }
    
    function __construct() {
        parent::__construct();

        $this->load->library('session');

        $this->load->model('login', '', TRUE);
        $this->load->model('customer', '', TRUE);
        $this->load->model('estimate', '', TRUE);
        $this->load->model('inventory', '', TRUE);
        $this->load->model('invoice', '', TRUE);
        $this->load->model('proformainvoice', '', TRUE);
        $this->load->model('supplier', '', TRUE);
        $this->load->model('grn', '', TRUE);
        $this->load->model('bom', '', TRUE);
        $this->load->model('joborder', '', TRUE);
        $this->load->model('units', '', TRUE);
        $this->load->library('form_validation');
    }

    public function public_quote($number) {
        // Public PDF download - no user_id dependency
        $data['show_quotation'] = $this->estimate->get_estimates_data($number, 0); // 0 = public/no filter
        $data['settings'] = $this->login->get_settings(0); // Public settings or first available
        $data['estimates_data_group'] = $this->estimate->get_estimates_data_group_by($number, 0);
        $data['stamp'] = 'yes';
        
        $html = $this->load->view('admin/print_igst_quote', $data, true);
        $pdfFilePath = "Quotation-" . str_replace("/", "-", $number) . ".pdf";
        
        require_once APPPATH . '../vendor/autoload.php';
        $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8']);
        $mpdf->SetHTMLHeader('<div>' . date("d-M-Y") . " - " . $number . '</div>');
        $mpdf->SetHTMLFooter('<div style="background-color: #f0f0ff; text-align: right">' . strtoupper($data['settings']['company_name']) . '   {PAGENO} of {nb}</div>');
        $mpdf->SetWatermarkText($data['settings']['company_name']);
        $mpdf->showWatermarkText = true;
        $mpdf->watermarkTextAlpha = 0.1;
        $mpdf->watermark_font = 'DejaVuSansCondensed';
        $mpdf->WriteHTML($html);
        $mpdf->Output($pdfFilePath, "D");   
    }

    public function index()
    {
        $quote_request = $this->getNumberAndUserIdFromUri();
        $number = $quote_request['number'];
        $user_id_send = $quote_request['user_id_send'];

        $data['show_quotation'] = $this->estimate->get_estimates_data($number, $user_id_send);
        $data['settings'] = $this->login->get_settings($user_id_send);
        $data['estimates_data_group'] = $this->estimate->get_estimates_data_group_by($number, $user_id_send);
                $data['stamp'] = 'yes';
                $html = $this->load->view('admin/print_igst_quote', $data, true);
        
        $pdfFilePath = "Quotation-".$number.".pdf";
                $mpdf = new \Mpdf\Mpdf();
                $mpdf->SetHTMLHeader('<div>' . date("d-M-Y") . " - " . $number . '</div>');
                $mpdf->SetHTMLFooter('<div style="background-color: #f0f0ff; text-align: right">' . strtoupper($data['settings']['company_name']) . '   {PAGENO} of {nb}</div>');
                $mpdf->SetWatermarkText($data['settings']['company_name']);
                $mpdf->showWatermarkText = true;
                $mpdf->watermarkTextAlpha = 0.1;
                $mpdf->watermark_font = 'DejaVuSansCondensed';
                $mpdf->WriteHTML($html);
                $mpdf->Output($pdfFilePath, "D");   
      }
      
    // ... rest of existing methods unchanged
    public function download_invoice()
    {
        $id = $this->uri->segment(3);
        $id1 = $this->uri->segment(4);
        $id2 = $this->uri->segment(5);
        //$id3 = $this->uri->segment(6);
        $user_id_send = $this->uri->segment(6);
        $invoice_number = $id . "/" . $id1 . "/" . $id2;
       
        $data['show_invoice'] = $this->invoice->get_invoice_data($invoice_number, $user_id_send);
        $data['invoice_data_group'] = $this->invoice->get_invoice_data_group_by($invoice_number, $user_id_send);
        $data['settings'] = $this->login->get_settings($user_id_send);
        
//      $this->load->view('admin/print', $data);
        $html = $this->load->view('admin/invoice_print', $data, true);
        //$pdfFilePath = "invoice.pdf";
        $pdfFilePath = "Invoice-".$invoice_number.".pdf";
        //load mPDF library
        $this->load->library('M_pdf');
       //generate the PDF from the given html
        $this->m_pdf->pdf->WriteHTML($html);
        //download it.
        $this->m_pdf->pdf->Output($pdfFilePath, "D");   
    }

    public function download_proforma_invoice()
    {
        $invoice_request = $this->getNumberAndUserIdFromUri();
        $invoice_number = $invoice_request['number'];
        $user_id_send = $invoice_request['user_id_send'];

        $data['show_invoice'] = $this->proformainvoice->get_proforma_invoice_data($invoice_number, $user_id_send);
        $data['invoice_data_group'] = $this->proformainvoice->get_proforma_invoice_data_group_by($invoice_number, $user_id_send);
        $data['settings'] = $this->login->get_settings($user_id_send);
        $data['stamp'] = 'yes';

        $html = $this->load->view('admin/proforma_invoice_print', $data, true);
        $pdfFilePath = "Proforma-Invoice-" . $invoice_number . ".pdf";

        $this->load->library('M_pdf');
        $this->m_pdf->pdf->WriteHTML($html);
        $this->m_pdf->pdf->Output($pdfFilePath, "D");
    }
    public function download_po()
    {
        $po_request = $this->getNumberAndUserIdFromUri();
        $po_number = $po_request['number'];
        $user_id_send = $po_request['user_id_send'];

        $data['show_po'] = $this->supplier->get_po_data($po_number, $user_id_send);
        $data['po_data_group'] = $this->supplier->get_po_data_group_by($po_number, $user_id_send);
        $data['settings'] = $this->login->get_settings($user_id_send);

        $html = $this->load->view('admin/po_print', $data, true);

        // Load Composer Autoloader for mPDF
        require_once APPPATH . '../vendor/autoload.php';

        // Create mPDF instance
        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4'
        ]);

        $mpdf->WriteHTML($html);

        // Remove "/" from filename
        $fileName = "Purchase-order-" . str_replace("/", "-", $po_number) . ".pdf";

        // Download the file
        $mpdf->Output($fileName, 'D');
    }

    public function download_bom()
    {
        $id = $this->uri->segment(3);
        $user_id_send = $this->uri->segment(4);

        if (empty($id) || empty($user_id_send)) {
            show_error('Invalid BOM request', 400);
            return;
        }

        $bom_number_id = $this->bom->get_bom_number_from_bom_total($id, $user_id_send);
        if (empty($bom_number_id) || empty($bom_number_id['number_fk'])) {
            show_error('BOM not found', 404);
            return;
        }

        $number = $bom_number_id['number_fk'];
        $data['show_bom'] = $this->bom->get_bom_data($number, $user_id_send);
        $data['settings'] = $this->login->get_settings($user_id_send);
        $data['bom_data_group'] = $this->bom->get_bom_data_group_by($number, $user_id_send);
        $data['unit_result'] = $this->units->get_units($user_id_send);
        $data['stamp'] = 'yes';

        if (empty($data['show_bom']) || empty($data['bom_data_group'])) {
            show_error('BOM not found', 404);
            return;
        }

        $html = $this->load->view('admin/print_bom', $data, true);

        require_once APPPATH . '../vendor/autoload.php';

        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4'
        ]);

        $mpdf->SetHTMLHeader('<div>' . date('d-M-Y') . ' - ' . $number . '</div>');
        $mpdf->SetHTMLFooter('<div style="background-color: #f0f0ff; text-align: right">' . strtoupper($data['settings']['company_name']) . '   {PAGENO} of {nb}</div>');
        $mpdf->SetWatermarkText($data['settings']['company_name']);
        $mpdf->showWatermarkText = true;
        $mpdf->watermarkTextAlpha = 0.1;
        $mpdf->watermark_font = 'DejaVuSansCondensed';
        $mpdf->WriteHTML($html);

        $fileName = 'BOM-' . str_replace('/', '-', $number) . '.pdf';
        $mpdf->Output($fileName, 'D');
    }

    public function download_joborder()
    {
        $id = $this->uri->segment(3);
        $user_id_send = $this->uri->segment(4);

        if (empty($id) || empty($user_id_send)) {
            show_error('Invalid Job Order request', 400);
            return;
        }

        $joborder_number_id = $this->joborder->get_joborder_number_from_joborder_total($id, $user_id_send);
        if (empty($joborder_number_id) || empty($joborder_number_id['number_fk'])) {
            show_error('Job Order not found', 404);
            return;
        }

        $number = $joborder_number_id['number_fk'];
        $data['show_joborder'] = $this->joborder->get_joborder_data($number, $user_id_send);
        $data['settings'] = $this->login->get_settings($user_id_send);
        $data['joborder_data_group'] = $this->joborder->get_joborder_data_group_by($number, $user_id_send);
        $data['unit_result'] = $this->units->get_units($user_id_send);

        if (empty($data['show_joborder']) || empty($data['joborder_data_group'])) {
            show_error('Job Order not found', 404);
            return;
        }

        $html = $this->load->view('joborder/print_joborder', $data, true);

        require_once APPPATH . '../vendor/autoload.php';

        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4'
        ]);

        $mpdf->SetHTMLHeader('<div>' . date('d-M-Y') . ' - ' . $number . '</div>');
        $mpdf->SetHTMLFooter('<div style="background-color: #f0f0ff; text-align: right">' . strtoupper($data['settings']['company_name']) . '   {PAGENO} of {nb}</div>');
        $mpdf->SetWatermarkText($data['settings']['company_name']);
        $mpdf->showWatermarkText = true;
        $mpdf->watermarkTextAlpha = 0.1;
        $mpdf->watermark_font = 'DejaVuSansCondensed';
        $mpdf->WriteHTML($html);

        $fileName = 'Job-Order-' . str_replace('/', '-', $number) . '.pdf';
        $mpdf->Output($fileName, 'D');
    }

    public function grn_pdf()
    {
        $grn_request = $this->getNumberAndUserIdFromUri();
        $grn_number = $grn_request['number'];
        $user_id_send = $grn_request['user_id_send'];

        $data['show_grn'] = $this->grn->get_grn_data($grn_number, $user_id_send);
        $data['grn_data_group'] = $this->grn->get_grn_data_group_by($grn_number, $user_id_send);
        $data['settings'] = $this->login->get_settings($user_id_send);
//      $this->load->view('admin/print', $data);
        $html = $this->load->view('admin/grn_print', $data, true);
        //$pdfFilePath = "PO.pdf";
        $pdfFilePath = "GRN-".$grn_number.".pdf";
        //load mPDF library
        $this->load->library('M_pdf');
       //generate the PDF from the given html
        $this->m_pdf->pdf->WriteHTML($html);
        //download it.
        $this->m_pdf->pdf->Output($pdfFilePath, "D");   
    }
    
    public function download_non_gst_quote()
    {
        $quote_request = $this->getNumberAndUserIdFromUri();
        $number = $quote_request['number'];
        $user_id_send = $quote_request['user_id_send'];

        $data['show_quotation'] = $this->estimate->get_non_gst_estimates_data($number, $user_id_send);
        $data['settings'] = $this->login->get_settings($user_id_send);
        $data['estimates_data_group'] = $this->estimate->get_non_gst_estimates_data_group_by($number, $user_id_send);
//      $this->load->view('admin/print', $data);
        $html = $this->load->view('admin/print_non_gst_quote', $data, true);
        //$pdfFilePath = base_url().'uploads/'.$number. ".pdf";
        
        $pdfFilePath = "NGQuotation-".$number.".pdf";
        //load mPDF library
        $this->load->library('M_pdf');
       //generate the PDF from the given html
        $this->m_pdf->pdf->WriteHTML($html);
        //download it.
        $this->m_pdf->pdf->Output($pdfFilePath, "D");   
      }
    
      public function download_non_gst_invoice()
    {
        $id = $this->uri->segment(3);
        $id1 = $this->uri->segment(4);
        $id2 = $this->uri->segment(5);
        $id3 = $this->uri->segment(6);
        $user_id_send = $this->uri->segment(7);
        $invoice_number = $id . "/" . $id1 . "/" . $id2 . "/" . $id3;
        $data['show_ng_invoice'] = $this->invoice->get_ng_invoice_data($invoice_number, $user_id_send);
        $data['ng_invoice_data_group'] = $this->invoice->get_ng_invoice_data_group_by($invoice_number, $user_id_send);
        $data['settings'] = $this->login->get_settings($user_id_send);
//      $this->load->view('admin/print', $data);
        $html = $this->load->view('admin/non_gst_invoice_print', $data, true);
        //$pdfFilePath = "invoice.pdf";
        $pdfFilePath = "NGInvoice-".$invoice_number.".pdf";
        //load mPDF library
        $this->load->library('M_pdf');
       //generate the PDF from the given html
        $this->m_pdf->pdf->WriteHTML($html);
        //download it.
        $this->m_pdf->pdf->Output($pdfFilePath, "D");   
    }
    
    public function download_invoice_file()
    {
        $filename = urldecode($this->uri->segment(3));
        
        // Security check - ensure filename doesn't contain path traversal
        if (strpos($filename, '..') !== false || strpos($filename, '/') !== false || strpos($filename, '\\') !== false) {
            show_error('Invalid file name', 400);
            return;
        }
        
        $filepath = FCPATH . 'uploads/invoice/' . $filename;
        
        // Debug: Check the path
        // echo "Filepath: " . $filepath . "<br>";
        // echo "File exists: " . (file_exists($filepath) ? 'Yes' : 'No') . "<br>";
        // die();
        
        // Check if file exists
        if (!file_exists($filepath)) {
            show_error('File not found: ' . $filepath, 404);
            return;
        }
        
        // Load download helper
        $this->load->helper('download');
        
        // Add timestamp to filename for download
        $timestamp = date('Ymd_His');
        $file_info = pathinfo($filename);
        $download_filename = $file_info['filename'] . '_' . $timestamp . '.' . $file_info['extension'];
        
        // Force download with timestamped filename
        force_download($download_filename, file_get_contents($filepath));
    }
} 
?>

