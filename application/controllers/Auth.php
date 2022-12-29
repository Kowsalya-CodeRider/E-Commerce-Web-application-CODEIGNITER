    <?php defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends Main_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('user/auth_model');

		$this->load->library('session');
	}

	//-------------------------------------------------------------------
	// login functionality
	public function login()
	{
	    if($this->session->userdata('is_user_login'))
	    redirect(base_url());
	    
		if (isset($_POST) && !empty($_POST)) 
		{
            
                //validate inputs
                $this->form_validation->set_rules('email','email','trim|required|min_length[3]|valid_email' );
                $this->form_validation->set_rules('password','password','trim|required|min_length[3]');

                if ($this->form_validation->run() == FALSE) {
                    $data = array(
                        'errors' => validation_errors(), 
                    );
                    $this->session->set_flashdata('validation_errors', $data['errors']);
                    redirect(base_url('auth/login'));
                }else{
                    $data = array(
                        'email' => $this->input->post('email'),
                        'password' => $this->input->post('password') 
                    );
                    
                    $data = $this->security->xss_clean($data); // XSS Clean

                    $result = $this->auth_model->login($data);

                    if ($result) {
                        
                        $login_data = array(
                            'id' => $result['id'],
                            'userid' => $result['userid'],
                            'email' => $result['email'], 
                            'name' => $result['name'],
                            'is_user_login' => TRUE
                        );

                        $this->session->set_userdata($login_data);

                        // redirected to last request page
                        if(!empty($this->session->userdata('last_request_page')))
                        {
                            $back_to = $this->session->userdata('last_request_page');
                            redirect($back_to);
                        }
                        else
                        {
                            redirect(base_url('user/dashboard'));
                        }
                    }
                    else
                    {
                        $this->session->set_flashdata('warning', get_phrase('invalid_username_or_password'));
                        redirect(base_url('auth/login'));
                    }
                }
		}
	
			$data['title'] = 'Login Page';
			$data['meta_description'] = 'your meta description here';
			$data['keywords'] = 'meta tags here';

			$data['view'] = 'auth/login';
			$this->load->view('layout', $data);
	}

	//-------------------------------------------------------------------------------
	// Registration Functionality
	public function register()
	{
	    if($this->session->userdata('is_user_login'))
	    redirect(base_url());
	    
		if (isset($_POST) && !empty($_POST)) 
		{
            /* 

			if ($this->recaptcha_status == true) {
	            if (!$this->recaptcha_verify_request()) {
	                $this->session->set_flashdata('form_data', $this->input->post());
	                $this->session->set_flashdata('validation_errors', 'reCaptcha Error');
	                redirect(base_url('auth/registration'));
	                exit();
	            }
	        }
            
            */

	        //validate inputs
			$this->form_validation->set_rules('name','Name','trim|required|min_length[3]');
			$this->form_validation->set_rules('email','Email','trim|required|min_length[5]|valid_email|is_unique[users.email]');
			$this->form_validation->set_rules('password','Password','trim|required|min_length[3]');
			$this->form_validation->set_rules('confirm_password','Confirm Password','trim|required|min_length[3]|matches[password]');

			if ($this->form_validation->run() == FALSE) 
			{
				$data = array(
					'errors' => validation_errors(), 
				);
				$this->session->set_flashdata('validation_errors', $data['errors']);
				redirect(base_url('auth/register'));
			}
			else
			{
				$data = array(
					'userid' => uniqueid(),
					'name' => $this->input->post('name'),
					'email' => $this->input->post('email'),
					'password' => password_hash($this->input->post('password'), PASSWORD_BCRYPT),
					'imagelocation' => 'default.png',
					'created_at' => date('Y-m-d : h:m:s'),
					'updated_at' => date('Y-m-d : h:m:s')
				);

				$data = $this->security->xss_clean($data); // XSS Clean Data

				$result = $this->auth_model->insert_into_users($data);
				
				if ($result) 
				{
					// Send Verification Email

					//$this->mailer->send_verification_email($result,'user');

				    $this->session->set_flashdata('success', 'Registered Successfully, Login.');
					redirect(base_url('auth/login'));
				}
			}
		}

        
			$data['title'] = 'Registration';
			$data['view'] = 'auth/register';
			$this->load->view('layout', $data);
	}
	//--------------------------------------------------		
	public function forgot_password()
	{
	    if($this->session->userdata('is_user_login'))
	    redirect(base_url());
	    
		if(isset($_POST) && !empty($_POST)){

			//validate inputs
			$this->form_validation->set_rules('email', 'Email', 'valid_email|trim|required');
			if ($this->form_validation->run() == FALSE) 
			{
				$data = array(
					'errors' => validation_errors(), 
				);
				$this->session->set_flashdata('validation_errors', $data['errors']);
				redirect(base_url('auth/forgot_password'));
			}
            
			$email = $this->input->post('email');

			$response = $this->auth_model->check_user_mail($email); // check if email exist

			if($response){
				$rand_no = rand(0,1000);
				$pwd_reset_code = md5($rand_no.$response['id']);
				$this->auth_model->update_reset_code($pwd_reset_code, $response['id']);

				// --- sending email
				$name = $response['name'];
				$email = $response['email'];
				$reset_link = base_url('auth/reset_password/'.$pwd_reset_code);
				$body = $this->mailer->pwd_reset_link($name,$reset_link,$this->settings['sitename']);

				$this->load->helper('email_helper');
				$to = $email;
				$subject = 'Reset your password';
				$message =  $body ;
				$email = sendEmail($to, $subject, $message, $file = '' , $cc = '');

				if($email){
					$this->session->set_flashdata('success', get_phrase('reset_email_sent_successfully'));
					redirect(base_url('auth/forgot_password'));
				}
				else{
					$this->session->set_flashdata('warning', get_phrase('email_message_not_sent'));
					redirect(base_url('auth/forgot_password'));
				}
			}
			else{
				$this->session->set_flashdata('warning', get_phrase('invalid_email'));
				redirect(base_url('auth/forgot_password'));
			}
		}
        
			$data['title'] = get_phrase('forgot_password');
			$data['meta_description'] = 'your meta description here';
			$data['keywords'] = 'meta tags here';
			$data['view'] = 'auth/forgot_password';
			$this->load->view('layout', $data);
            
	}

	//----------------------------------------------------------------		
	public function reset_password($id=0)
	{
	    if($this->session->userdata('is_user_login'))
	    redirect(base_url());
	    
		// check the activation code in database
		if(isset($_POST) && !empty($_POST)){
			
			$this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[3]');
			$this->form_validation->set_rules('confirm_password', 'Password Confirmation', 'trim|required|matches[password]');

			if ($this->form_validation->run() == FALSE) {
				$data = array(
					'errors' => validation_errors(), 
				);
				$this->session->set_flashdata('validation_errors', $data['errors']);
				redirect(base_url('auth/reset_password/'. $this->input->post('code')));
			}   
			else{
				$new_password = password_hash($this->input->post('password'), PASSWORD_BCRYPT);
				$this->auth_model->reset_password($this->input->post('code'), $new_password);
				$this->session->set_flashdata('success',get_phrase('changed_password_successfully'));
				redirect(base_url('auth/login'));
			}
		}
		
			$result = $this->auth_model->check_password_reset_code($id);
			if($result){
				$data['reset_code'] = $id;
				$data['title'] = get_phrase('reset_password');
				$data['view'] = 'auth/reset_password';
				$this->load->view('layout', $data);
			}
			else{
				$this->session->set_flashdata('error',get_phrase('passcode_invalid'));
				redirect(base_url('auth/forgot_password'));
			}
		
	}

	//----------------------------------------------------------	
	public function verify()
	{
        if($this->session->userdata('is_user_login'))
	    redirect(base_url());
	    
		$verification_id = $this->uri->segment(3);
		$result = $this->auth_model->email_verification($verification_id);
		if($result){
		    
		    // --- sending welcome email
		    $mail_data = array(
		        'fullname' => $result['firstname'].' '.$result['lastname'],
		    );
			$this->mailer->mail_template($result['email'],'welcome',$mail_data);
			
			$this->session->set_flashdata('success', trans('email_verified_msg'));
			redirect(base_url('auth/login'));
		}
		else{
			$this->session->set_flashdata('success', trans('url_invalid_msg'));
			redirect(base_url('auth/login'));
		}	
	}


	//----------------------------------------------------------------------------
	// Logout Function
	public function logout()
	{
        $this->session->unset_userdata('id');
        $this->session->unset_userdata('userid');
        $this->session->unset_userdata('email');
        $this->session->unset_userdata('name');
        $this->session->unset_userdata('is_user_login');

		// Destroy the session
		$this->session->sess_destroy();
        //$this->clear_cache();//clear the cache after logout //
		redirect(base_url('auth/login'));
	}

}// endClass