<?php
defined('BASEPATH') or exit('No direct script access allowed');

class LoadRequisitionHook
{
    /**
     * Load requisition model - called from post_controller_constructor
     * CI instance is available at this point
     */
    public function load_requisition_model()
    {
        // Get the CI instance
        $CI = &get_instance();

        // Debug: Check what's available
        // error_log('Hook called. CI available: ' . (isset($CI) ? 'Yes' : 'No'));

        // Basic safety check
        if (!isset($CI) || !is_object($CI)) {
            return;
        }

        // Load model without any conditions - always load it
        if (!isset($CI->requisition) || !is_object($CI->requisition)) {
            // Check if model exists
            if (file_exists(APPPATH . 'models/Requisition.php')) {
                $CI->load->model('Requisition', 'requisition');
                // error_log('Requisition model loaded successfully');
            } else {
                // error_log('Requisition_model.php not found at: ' . APPPATH . 'models/Requisition_model.php');
            }
        }

        // Optional: Set user_id if available
        if (isset($CI->session) && $CI->session->userdata('user_id')) {
            $CI->user_id = $CI->session->userdata('user_id');
        }
    }
}
