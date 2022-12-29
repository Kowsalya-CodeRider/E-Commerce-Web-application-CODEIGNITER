<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Smtp extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('admin/settings_model');
	}

	public function index()
	{
	    
		if (isset($_POST) && !empty($_POST)) 
		{

	        //validate inputs
			$this->form_validation->set_rules('smtp_host','SMTP Host','trim|required');
			$this->form_validation->set_rules('smtp_username','SMTP Username','trim|required');
			$this->form_validation->set_rules('smtp_password','SMTP Password','trim|required');
			$this->form_validation->set_rules('smtp_encryption','SMTP Encryption','trim|required');
			$this->form_validation->set_rules('smtp_port','SMTP Port','trim|required');
            
			if ($this->form_validation->run() == FALSE) 
			{
				$data = array(
					'errors' => validation_errors(), 
				);
				$this->session->set_flashdata('validation_errors', $data['errors']);
				redirect(base_url('admin/smtp'));
			}else{
                
                $data = array(
                    'smtp_host' => $this->input->post('smtp_host'),
                    'smtp_username' => $this->input->post('smtp_username'),
                    'smtp_password' => $this->input->post('smtp_password'),
                    'smtp_encryption' => $this->input->post('smtp_encryption'),
                    'smtp_port' => $this->input->post('smtp_port')
                );
                $data = $this->security->xss_clean($data);
                $result = $this->settings_model->update_settings($data);

				if ($result){
				    $this->session->set_flashdata('success', 'Updated Successfully');
				    redirect(base_url('admin/smtp'));
				}else{
                    $this->session->set_flashdata('warning', 'Error when saving Data');
				    redirect(base_url('admin/smtp'));
                }
			}
		}
        
        
		$data['settings'] = $this->settings_model->get_settings();
		$data['title'] = 'Pages';
		$data['view'] = 'admin/smtp/settings';
		$this->load->view('admin/layout', $data);
	}

}

?>	