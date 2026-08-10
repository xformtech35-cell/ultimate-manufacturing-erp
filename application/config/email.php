<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
| -------------------------------------------------------------------
| EMAIL CONFIGURATION
| -------------------------------------------------------------------
| Dynamic configuration for live server vs localhost
*/

$CI = &get_instance();
$CI->load->library('session');
$session_data_head2 = $CI->session->userdata('session_data_head2');

$mail_id = 'infp.pmms@gmail.com';
$pass = 'tsyq both zauh unzf';

$is_live = isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], 'uwsenvirotech.com') !== false;

if ($is_live) {
    // Live Server (Godaddy Hosting) - Use native mail protocol to bypass port 465 socket block
    $config['protocol']  = 'mail';
    $config['mailpath']  = '/usr/sbin/sendmail';
    $config['charset']   = 'utf-8';
    $config['newline']   = "\r\n";
    $config['crlf']      = "\r\n";
    $config['mailtype']  = "html";
} else {
    // Localhost Environment - Use SMTP
    $config['protocol']     = 'smtp';
    $config['smtp_host']    = 'ssl://smtp.googlemail.com';
    $config['smtp_port']    = 465;
    $config['smtp_timeout'] = 5;
    $config['smtp_user']    = $mail_id;
    $config['smtp_pass']    = $pass;
    $config['charset']     = 'utf-8';
    $config['newline']     = "\r\n";
    $config['crlf']        = "\r\n";
    $config['mailtype']    = "html";
}
