<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');
/*
  | ——————————————————————-
  | EMAIL CONFING
  | ——————————————————————-
  | Configuration of outgoing mail server.
  | */
//$config['protocol']='smtp';
//$config['smtp_host']='ssl://smtp.googlemail.com';
//$config['smtp_port']='465';
//$config['smtp_timeout']='5';
//$config['smtp_user']='mahesh@xform.in';
//$config['smtp_pass']='mahi@1234';
//$config['charset']='utf-8';
//$config['newline']="\r\n";
/* End of file email.php */
/* Location: ./system/application/config/email.php */

$CI = &get_instance();
$CI->load->library('session');
//$this->load->library('session');
$session_data_head2 = $CI->session->userdata('session_data_head2');

//$mail_id = $session_data_head2['from_email'];
//$pass = $session_data_head2['password_email'];
$mail_id = 'infp.pmms@gmail.com';
$pass = 'tsyq both zauh unzf';

//echo $mail_id;
//echo $pass; 
//die();
$config['protocol'] = 'smtp';
$config['smtp_host'] = 'ssl://smtp.googlemail.com';
$config['smtp_port'] = 465;
$config['smtp_timeout'] = 5;
$config['smtp_user'] = $mail_id;
$config['smtp_pass'] = $pass;
$config['charset'] = 'iso-8859-1';
$config['newline'] = "\r\n";
$config['mailtype'] = "html";
