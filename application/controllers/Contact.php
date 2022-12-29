<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Contact extends Main_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('frontend/home_model');
	}

	//-----------------------------------------------------------------------------
	// Index funciton will call bydefault
	public function index()
	{	
		if (isset($_POST) && !empty($_POST))
		{
			$this->form_validation->set_rules('name','Name','trim|required|min_length[3]');
			$this->form_validation->set_rules('email','email','trim|required|min_length[3]|valid_email');
			$this->form_validation->set_rules('need','need','trim|required');
			$this->form_validation->set_rules('message','message','trim|required|min_length[3]');

			if ($this->form_validation->run() == FALSE) {
				$data = array(
					'errors' => validation_errors(), 
				);
				$this->session->set_flashdata('validation_errors', $data['errors']);
				redirect(base_url('contact'));
			}
			else
			{
				$data = array(
					'name' => $this->input->post('name'),
					'email' => $this->input->post('email'),
					'need' => $this->input->post('need'),
					'message' => $this->input->post('message'),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
				);

				$data = $this->security->xss_clean($data); // XSS Clean Data

				$result = $this->home_model->contact($data);

				if($result) 
				{
					// email code
					$this->load->helper('email_helper');

					$to = $this->settings['contact_email'];
					$subject = 'Contact Us | '.$this->settings['sitename'];
					$message =  '<p>Name: '.$data['name'].'</p> 
					<p>Email: '.$data['email'].'</p>
					<p>Message: '.$data['message'].'</p>' ;

					sendEmail($to, $subject, $message, $file = '' , $cc = '');
                    
				    $this->session->set_flashdata('success', get_phrase('email_sent_successfully'));
				    redirect(base_url('contact'));
                    
				}else{
					redirect(base_url('contact'));
				}
			}
		}
        
		$data['title'] = 'Contact';
		$data['view'] = 'contact';
		$this->load->view('layout', $data);
	}
    
}

?>