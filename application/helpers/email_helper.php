<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function sendEmail($to = '', $subject  = '', $body = '', $attachment = '', $cc = '')
{
	$controller =& get_instance();

	$controller->load->helper('path');
    
    $settings = $controller->settings_model->get_settings();

	// Configure email library

	$config = array();
	$config['useragent'] = "-";
	$config['mailpath'] = "/usr/bin/sendmail"; // or "/usr/sbin/sendmail"
	$config['protocol'] = "mail";
	$config['smtp_host'] = $settings['smtp_host'];
	$config['smtp_port'] = $settings['smtp_port'];
	$config['smtp_timeout'] = '90';
	$config['smtp_user'] = $settings['smtp_username'];
	$config['smtp_pass'] = $settings['smtp_password'];
	$config['mailtype'] = 'html';
	$config['charset'] = 'utf-8';
	$config['newline'] = "\r\n";
	$config['wordwrap'] = TRUE;

	$controller->load->library('email');

	$controller->email->initialize($config);
    
	$controller->email->from($settings['contact_email'], $settings['sitename']);

	$controller->email->to($to);

	$controller->email->subject($subject);  

	$controller->email->message($body);

	if ($cc != '') {
		$controller->email->cc($cc);
	}

	if ($attachment != '') {
		$controller->email->attach(base_url() . "your_file_path" . $attachment);

	}

	if ($controller->email->send()) {
		return "success";
	} else {
		echo $controller->email->print_debugger();
		exit();
	}
}
	
?>