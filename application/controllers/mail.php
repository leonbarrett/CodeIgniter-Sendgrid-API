<?php

class Pack extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		
	}
	
	 /**
	 * Send - sends an email via Sendgrid
	 *
	 * @param 
	 * @return void
	 * @author Leon Barrett
	 */
	function send(){
		
		$recipients = //Data from database;
				
		$users = array();
		$names = array();
		
		$pack_sent = date('Y-m-d H:i:s');
		
		foreach($recipients as $recipient){
			
			$users[] = $recipient->contact_email;
			$names[] = $recipient->contact_first_name;

		}
		
		$this->load->library('sendgrid_api');
		$this->config->load('sendgrid');
		
		$this->sendgrid_api->addTo($users);
		
		$this->sendgrid_api->addSubVal('-name-', $names);
		$this->sendgrid_api->setCategory('My Emailer');		
		$xsmtpapi = $this->sendgrid_api->as_string();
			
		$this->load->library('email');
		
		$config['protocol'] = 'smtp';
		$config['smtp_host'] = $this->config->item('sendgrid_smtp_host');
		$config['smtp_user'] = $this->config->item('sendgrid_smtp_user');
		$config['smtp_pass'] = $this->config->item('sendgrid_smtp_pass');
		$config['smtp_port'] = 25;

		$this->email->initialize($config);
			
		$this->email->from('hello@email.com');
		$this->email->to('hello@email.com'); 
		$this->email->add_custom_header('X-SMTPAPI',$xsmtpapi); 

		$this->email->subject('My Emailer');
		
		$data['recipient_name'] = '-name-';		

		$email_message = $this->load->view('emailer/email.php',$data,TRUE);

		$this->email->message($email_message);	

		$status = $this->email->send();
		
		if(!$status){
			$email_debug = $this->email->print_debugger();
			log_message('error', $email_debug);
		}
					
	}
	
}