<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Social_Login extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('admin/settings_model');
	}

	public function index()
	{
       
	    if(!$this->session->has_userdata('is_admin_login'))
		{
			redirect('admin/auth/login');
		}else{
            redirect('admin/social_login/facebook');
        }
	}

	public function facebook()
	{
	    
		if (isset($_POST) && !empty($_POST)) 
		{

	        //validate inputs
			$this->form_validation->set_rules('facebook_app_id','Facebook App ID','trim|required');
			$this->form_validation->set_rules('facebook_app_secret','Facebook App Secret','trim|required');
            
			if ($this->form_validation->run() == FALSE) 
			{
				$data = array(
					'errors' => validation_errors(), 
				);
				$this->session->set_flashdata('validation_errors', $data['errors']);
				redirect(base_url('admin/social_login/facebook'));
			}else{
                
                $data = array(
                    'facebook_app_id' => $this->input->post('facebook_app_id'),
                    'facebook_app_secret' => $this->input->post('facebook_app_secret')
                );
                $data = $this->security->xss_clean($data);
                $result = $this->settings_model->update_settings($data);

				if ($result){
				    $this->session->set_flashdata('success', 'Updated Successfully');
				    redirect(base_url('admin/social_login/facebook'));
				}else{
                    $this->session->set_flashdata('warning', 'Error when saving Data');
				    redirect(base_url('admin/social_login/facebook'));
                }
			}
		}
		$data['settings'] = $this->settings_model->get_settings();
		$data['title'] = 'Settings';
		$data['view'] = 'admin/social/social';
		$this->load->view('admin/layout', $data);
	}

	public function google()
	{
	    
		if (isset($_POST) && !empty($_POST)) 
		{

	        //validate inputs
			$this->form_validation->set_rules('google_app_id','Google App ID','trim|required');
			$this->form_validation->set_rules('google_app_secret','Google App Secret','trim|required');
            
			if ($this->form_validation->run() == FALSE) 
			{
				$data = array(
					'errors' => validation_errors(), 
				);
				$this->session->set_flashdata('validation_errors', $data['errors']);
				redirect(base_url('admin/social_login/google'));
			}else{
                
                $data = array(
                    'google_app_id' => $this->input->post('google_app_id'),
                    'google_app_secret' => $this->input->post('google_app_secret')
                );
                $data = $this->security->xss_clean($data);
                $result = $this->settings_model->update_settings($data);

				if ($result){
				    $this->session->set_flashdata('success', 'Updated Successfully');
				    redirect(base_url('admin/social_login/google'));
				}else{
                    $this->session->set_flashdata('warning', 'Error when saving Data');
				    redirect(base_url('admin/social_login/google'));
                }
			}
		}
		$data['settings'] = $this->settings_model->get_settings();
		$data['title'] = 'Settings';
		$data['view'] = 'admin/social/social';
		$this->load->view('admin/layout', $data);
	}

	public function vk()
	{
	    
		if (isset($_POST) && !empty($_POST)) 
		{

	        //validate inputs
			$this->form_validation->set_rules('vk_app_id','VK App ID','trim|required');
			$this->form_validation->set_rules('vk_app_secret','VK App Secret','trim|required');
            
			if ($this->form_validation->run() == FALSE) 
			{
				$data = array(
					'errors' => validation_errors(), 
				);
				$this->session->set_flashdata('validation_errors', $data['errors']);
				redirect(base_url('admin/social_login/vk'));
			}else{
                
                $data = array(
                    'vk_app_id' => $this->input->post('vk_app_id'),
                    'vk_app_secret' => $this->input->post('vk_app_secret')
                );
                $data = $this->security->xss_clean($data);
                $result = $this->settings_model->update_settings($data);

				if ($result){
				    $this->session->set_flashdata('success', 'Updated Successfully');
				    redirect(base_url('admin/social_login/vk'));
				}else{
                    $this->session->set_flashdata('warning', 'Error when saving Data');
				    redirect(base_url('admin/social_login/vk'));
                }
			}
		}
		$data['settings'] = $this->settings_model->get_settings();
		$data['title'] = 'Settings';
		$data['view'] = 'admin/social/social';
		$this->load->view('admin/layout', $data);
	}

}

?>	