<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		//$this->load->library('mailer');
		$this->load->model('admin/auth_model');
		$this->load->model('admin/settings_model');

        //general settings
        $global_data['settings'] = $this->settings_model->get_settings();
        $this->settings = $global_data['settings'];
	}
    
	//--------------------------------------------------------------
	public function login()
	{
	    if($this->session->has_userdata('is_admin_login'))
		{
			redirect('admin/dashboard');
		}
		
		if(isset($_POST) && !empty($_POST)){
			$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
			$this->form_validation->set_rules('password', 'Password', 'trim|required');

			if ($this->form_validation->run() == FALSE) {
				$data = array(
					'errors' => validation_errors(), 
				);
				$this->session->set_flashdata('validation_errors', $data['errors']);
				$this->load->view('admin/auth/login');
			}
			else {
				$data = array(
					'email' => $this->input->post('email'),
					'password' => $this->input->post('password')
				);
				$result = $this->auth_model->login($data);
				if($result){
		    
                        // --- sending welcome email
                        $mail_data = array(
                            'fullname' => $result['name'],
                        );
			            //$this->mailer->mail_template($result['email'],'welcome',$mail_data);
                    
						$admin_data = array(
							'admin_id' => $result['id'],
							'name' => $result['name'],
							'is_admin_login' => TRUE
						);
						$this->session->set_userdata($admin_data);
						redirect(base_url('admin/dashboard'));
                    
				}
				else{
                    $this->session->set_flashdata('warning', 'Invalid Username or Password!');
					redirect(base_url('admin/auth/login'));
				}
			}
		}
        
            $data['view'] = 'admin/auth/login';
            $this->load->view('admin/layout', $data);
	}

	//-------------------------------------------------------
	public function logout()
	{
		$this->session->sess_destroy();
		redirect(base_url('admin/auth/login'));
	}

}  // end class

?>