<?php
defined('BASEPATH') or exit('No direct script access allowed');

$hook['pre_controller'][] = array(
    'class'    => 'LoadRequisitionHook',
    'function' => 'pre_controller',
    'filename' => 'LoadRequisitionHook.php',
    'filepath' => 'hooks',
    'params'   => array()
);

// OR use post_controller_constructor for better timing
$hook['post_controller_constructor'][] = array(
    'class'    => 'LoadRequisitionHook',
    'function' => 'load_requisition_model',
    'filename' => 'LoadRequisitionHook.php',
    'filepath' => 'hooks',
    'params'   => array()
);
