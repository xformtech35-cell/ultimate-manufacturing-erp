<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Session extends CI_Session {

    public function __construct(array $params = array()) {
        parent::__construct($params);
    }

    /**
     * Override flashdata to immediately unset the key after reading.
     * This prevents flashdata from sticking around on page refreshes
     * or getting revived by concurrent AJAX requests.
     */
    public function flashdata($key = NULL) {
        if ($key === NULL) {
            // Return all flashdata
            $old_keys = $this->get_flash_keys();
            $flashdata = array();
            foreach ($old_keys as $k) {
                $flashdata[$k] = $this->userdata($k);
                $this->unset_userdata($k);
            }
            return $flashdata;
        }

        $value = parent::flashdata($key);
        if ($value !== NULL) {
            $this->unset_userdata($key);
        }
        return $value;
    }
}
