<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Settings extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('admin/settings_model');
		$this->load->model('admin/admin_model');
		$this->load->model('admin/currency_model');
	}

	public function index()
	{
       
	    if(!$this->session->has_userdata('is_admin_login'))
		{
			redirect('admin/auth/login');
		}else{
            redirect('admin/settings/site');
        }
	}

	function admin_check()
	{
		if(!$this->session->has_userdata('is_admin_login'))
			redirect('admin/auth/login');
	}

	public function site()
	{
	    
		if (isset($_POST) && !empty($_POST)) 
		{

	        //validate inputs
			$this->form_validation->set_rules('sitename','Site Name','trim|required');
			$this->form_validation->set_rules('title','Title','trim|required|min_length[5]');
			$this->form_validation->set_rules('description','Description','trim|required|min_length[5]');
			$this->form_validation->set_rules('keywords','Keywords','trim|required|min_length[5]');
			$this->form_validation->set_rules('language','Language','trim|required');
			$this->form_validation->set_rules('timezone','Timezone','trim|required');
            
			if ($this->form_validation->run() == FALSE) 
			{
				$data = array(
					'errors' => validation_errors(), 
				);
				$this->session->set_flashdata('validation_errors', $data['errors']);
				redirect(base_url('admin/settings/site'));
			}else{
                
                $data = array(
                    'sitename' => $this->input->post('sitename'),
                    'title' => $this->input->post('title'),
                    'description' => $this->input->post('description'),
                    'keywords' => $this->input->post('keywords'),
                    'language' => $this->input->post('language'),
                    'timezone' => $this->input->post('timezone')
                );
                $data = $this->security->xss_clean($data);
                $result = $this->settings_model->update_settings($data);

				if ($result){
				    $this->session->set_flashdata('success', 'Updated Successfully');
				    redirect(base_url('admin/settings/site'));
				}else{
                    $this->session->set_flashdata('warning', 'Error when saving Data');
				    redirect(base_url('admin/settings/site'));
                }
			}
		}
        
		$data['admin'] = $this->admin_model->get_admin();
        $data['dir'] = APPPATH.'/language';
		$data['languages'] = get_all_languages();
		$data['settings'] = $this->settings_model->get_settings();
		$data['title'] = 'Settings';
		$data['view'] = 'admin/settings/settings';
		$this->load->view('admin/layout', $data);
	}

	public function home()
	{
	    
		if (isset($_POST) && !empty($_POST)) 
		{
            
            $path="public/uploads/settings/";
            $old_image = $this->input->post('old_image');

            if(!empty($_FILES['image']['name']))
            {
                
                //validate inputs
                $this->form_validation->set_rules('home_title','Title','trim|required');
                $this->form_validation->set_rules('home_sub_title','Home Sub Title','trim|required');

                if ($this->form_validation->run() == FALSE) 
                {
                    $data = array(
                        'errors' => validation_errors(), 
                    );
                    $this->session->set_flashdata('validation_errors', $data['errors']);
				    redirect(base_url('admin/settings/home'));
                }else{
                    
                    $this->functions->delete_file($path, $old_image);
                    $upload = $this->functions->file_insert($path, 'image', 'image', '9097152');
                    if($upload['status'] == 1){
                        
                        $data = array(
                            'home_title' => ucfirst($this->input->post('home_title')),
                            'home_sub_title' => $this->input->post('home_sub_title'),
                            'home_image' => $upload['msg'],
                        );
                        $data = $this->security->xss_clean($data);
                        $result = $this->settings_model->update_settings($data);

                        if ($result){
                            $this->session->set_flashdata('success', 'Saved Successfully');
				            redirect(base_url('admin/settings/home'));
                        }else{
                            $this->session->set_flashdata('warning', 'Error when saving Data');
				            redirect(base_url('admin/settings/home'));
                        }
                    }
                    else{
                        $this->session->set_flashdata('error', $upload['msg']);
				        redirect(base_url('admin/settings/home'));
                    }
                }                

            }else{
                
                //validate inputs
                $this->form_validation->set_rules('home_title','Title','trim|required');
                $this->form_validation->set_rules('home_sub_title','Home Sub Title','trim|required');

                if ($this->form_validation->run() == FALSE) 
                {
                    $data = array(
                        'errors' => validation_errors(), 
                    );
                    $this->session->set_flashdata('validation_errors', $data['errors']);
				    redirect(base_url('admin/settings/home'));
                }else{
                    
                    $slug = make_slug($this->input->post('title'));
                    $data = array(
                        'home_title' => ucfirst($this->input->post('home_title')),
                        'home_sub_title' => $this->input->post('home_sub_title')
                    );
                    $data = $this->security->xss_clean($data);
                    $result = $this->settings_model->update_settings($data);

                    if ($result){
                        $this->session->set_flashdata('success', 'Saved Successfully');
				        redirect(base_url('admin/settings/home'));
                    }else{
                        $this->session->set_flashdata('warning', 'Error when saving Data');
				        redirect(base_url('admin/settings/home'));
                    }
                        
                } 
            }            
            
		}
        
		$data['settings'] = $this->settings_model->get_settings();
		$data['title'] = 'Settings';
		$data['view'] = 'admin/settings/settings';
		$this->load->view('admin/layout', $data);
	}

	public function logo()
	{
	    
		if (isset($_POST) && !empty($_POST)) 
		{

            $old_image = $this->input->post('old_image');
            $path="public/uploads/settings/";

            if(!empty($_FILES['image']['name']))
            {
                if($old_image != 'default.png'){
                   $this->functions->delete_file($path, $old_image);
                }

                $result = $this->functions->file_insert($path, 'image', 'image', '9097152');
                if($result['status'] == 1){
                    $data['logo'] = $result['msg'];
                    $data = $this->security->xss_clean($data);
                    $result = $this->settings_model->update_settings($data);

                    if ($result){
                        $this->session->set_flashdata('success', 'Updated Successfully');
                        redirect(base_url('admin/settings/logo'));
                    }else{
                        $this->session->set_flashdata('warning', 'Error when saving Data');
                        redirect(base_url('admin/settings/logo'));
                    }
                }
                else{
                    $this->session->set_flashdata('error', $result['msg']);
                    redirect(base_url('admin/settings/logo'));
                }
            }else{
                
				$data = array(
					'errors' => 'Image not selected', 
				);
				$this->session->set_flashdata('validation_errors', $data['errors']);
                redirect(base_url('admin/settings/logo'));
            }
		}
        
		$data['admin'] = $this->admin_model->get_admin();
		$data['settings'] = $this->settings_model->get_settings();
		$data['title'] = 'Logo Settings';
		$data['view'] = 'admin/settings/settings';
		$this->load->view('admin/layout', $data);
	}

	public function favicon()
	{
	    
		if (isset($_POST) && !empty($_POST)) 
		{

            $old_image = $this->input->post('old_image');
            $path="public/uploads/settings/";

            if(!empty($_FILES['image']['name']))
            {
                if($old_image != 'default.png'){
                   $this->functions->delete_file($path, $old_image);
                }

                $result = $this->functions->file_insert($path, 'image', 'image', '9097152');
                if($result['status'] == 1){
                    $data['favicon'] = $result['msg'];
                    $data = $this->security->xss_clean($data);
                    $result = $this->settings_model->update_settings($data);

                    if ($result){
                        $this->session->set_flashdata('success', 'Updated Successfully');
                        redirect(base_url('admin/settings/favicon'));
                    }else{
                        $this->session->set_flashdata('warning', 'Error when saving Data');
                        redirect(base_url('admin/settings/favicon'));
                    }
                }
                else{
                    $this->session->set_flashdata('error', $result['msg']);
                    redirect(base_url('admin/settings/favicon'));
                }
            }else{
                
				$data = array(
					'errors' => 'Image not selected', 
				);
				$this->session->set_flashdata('validation_errors', $data['errors']);
                redirect(base_url('admin/settings/favicon'));
            }
		}
        
		$data['admin'] = $this->admin_model->get_admin();
		$data['settings'] = $this->settings_model->get_settings();
		$data['title'] = 'Logo Settings';
		$data['view'] = 'admin/settings/settings';
		$this->load->view('admin/layout', $data);
	}

	public function bg_image()
	{
	    
		if (isset($_POST) && !empty($_POST)) 
		{

            $old_image = $this->input->post('old_image');
            $path="public/uploads/settings/";

            if(!empty($_FILES['image']['name']))
            {
                if($old_image != 'default_bg.svg'){
                   $this->functions->delete_file($path, $old_image);
                }

                $result = $this->functions->file_insert($path, 'image', 'image', '9097152');
                if($result['status'] == 1){
                    $data['home_bg_image'] = $result['msg'];
                    $data = $this->security->xss_clean($data);
                    $result = $this->settings_model->update_settings($data);

                    if ($result){
                        $this->session->set_flashdata('success', 'Updated Successfully');
                        redirect(base_url('admin/settings/bg_image'));
                    }else{
                        $this->session->set_flashdata('warning', 'Error when saving Data');
                        redirect(base_url('admin/settings/bg_image'));
                    }
                }
                else{
                    $this->session->set_flashdata('error', $result['msg']);
                    redirect(base_url('admin/settings/bg_image'));
                }
            }else{
                
				$data = array(
					'errors' => 'Image not selected', 
				);
				$this->session->set_flashdata('validation_errors', $data['errors']);
                redirect(base_url('admin/settings/bg_image'));
            }
		}
        
		$data['admin'] = $this->admin_model->get_admin();
		$data['settings'] = $this->settings_model->get_settings();
		$data['title'] = 'Logo Settings';
		$data['view'] = 'admin/settings/settings';
		$this->load->view('admin/layout', $data);
	}

	public function service_bg_image()
	{
	    
		if (isset($_POST) && !empty($_POST)) 
		{

            $old_image = $this->input->post('old_image');
            $path="public/uploads/settings/";

            if(!empty($_FILES['image']['name']))
            {
                if($old_image != 'default_bg.svg'){
                   $this->functions->delete_file($path, $old_image);
                }

                $result = $this->functions->file_insert($path, 'image', 'image', '9097152');
                if($result['status'] == 1){
                    $data['service_bg_image'] = $result['msg'];
                    $data = $this->security->xss_clean($data);
                    $result = $this->settings_model->update_settings($data);

                    if ($result){
                        $this->session->set_flashdata('success', 'Updated Successfully');
                        redirect(base_url('admin/settings/service_bg_image'));
                    }else{
                        $this->session->set_flashdata('warning', 'Error when saving Data');
                        redirect(base_url('admin/settings/service_bg_image'));
                    }
                }
                else{
                    $this->session->set_flashdata('error', $result['msg']);
                    redirect(base_url('admin/settings/service_bg_image'));
                }
            }else{
                
				$data = array(
					'errors' => 'Image not selected', 
				);
				$this->session->set_flashdata('validation_errors', $data['errors']);
                redirect(base_url('admin/settings/service_bg_image'));
            }
		}
        
		$data['admin'] = $this->admin_model->get_admin();
		$data['settings'] = $this->settings_model->get_settings();
		$data['title'] = 'Logo Settings';
		$data['view'] = 'admin/settings/settings';
		$this->load->view('admin/layout', $data);
	}

	public function analytics()
	{
	    
		if (isset($_POST) && !empty($_POST)) 
		{

	        //validate inputs
			$this->form_validation->set_rules('analytics','Google Analytics','trim|required');
            
			if ($this->form_validation->run() == FALSE) 
			{
				$data = array(
					'errors' => validation_errors(), 
				);
				$this->session->set_flashdata('validation_errors', $data['errors']);
				redirect(base_url('admin/settings/analytics'), 'refresh');
			}else{
                
                $data = array(
                    'analytics' => $this->input->post('analytics')
                );
                $data = $this->security->xss_clean($data);
                $result = $this->settings_model->update_settings($data);

				if ($result){
				    $this->session->set_flashdata('success', 'Updated Successfully');
				    redirect(base_url('admin/settings/analytics'), 'refresh');
				}else{
                    $this->session->set_flashdata('warning', 'Error when saving Data');
				    redirect(base_url('admin/settings/analytics'), 'refresh');
                }
			}
		}
        
		$data['admin'] = $this->admin_model->get_admin();
        
		$data['settings'] = $this->settings_model->get_settings();
		$data['title'] = 'Settings';
		$data['view'] = 'admin/settings/settings';
		$this->load->view('admin/layout', $data);
	}

	public function contact()
	{
	    
		if (isset($_POST) && !empty($_POST)) 
		{

	        //validate inputs
			$this->form_validation->set_rules('contact_email','Contact Name','trim|required|valid_email');
			$this->form_validation->set_rules('contact_phone','Contact Phone','trim|required');
			$this->form_validation->set_rules('contact_location','Contact Location','trim|required');
            
			if ($this->form_validation->run() == FALSE) 
			{
				$data = array(
					'errors' => validation_errors(), 
				);
				$this->session->set_flashdata('validation_errors', $data['errors']);
				redirect(base_url('admin/settings/contact'));
			}else{
                
                $data = array(
                    'contact_email' => $this->input->post('contact_email'),
                    'contact_phone' => $this->input->post('contact_phone'),
                    'contact_location' => $this->input->post('contact_location')
                );
                $data = $this->security->xss_clean($data);
                $result = $this->settings_model->update_settings($data);

				if ($result){
				    $this->session->set_flashdata('success', 'Updated Successfully');
				    redirect(base_url('admin/settings/contact'));
				}else{
                    $this->session->set_flashdata('warning', 'Error when saving Data');
				    redirect(base_url('admin/settings/contact'));
                }
			}
		}
        
		$data['admin'] = $this->admin_model->get_admin();
        
		$data['settings'] = $this->settings_model->get_settings();
		$data['title'] = 'Settings';
		$data['view'] = 'admin/settings/settings';
		$this->load->view('admin/layout', $data);
	}

	public function social()
	{
	    
		if (isset($_POST) && !empty($_POST)) 
		{
            
                $data = array(
                    'facebook' => $this->input->post('facebook'),
                    'instagram' => $this->input->post('instagram'),
                    'twitter' => $this->input->post('twitter'),
                    'pinterest' => $this->input->post('pinterest'),
                    'linkedin' => $this->input->post('linkedin'),
                    'vk' => $this->input->post('vk'),
                    'whatsapp' => $this->input->post('whatsapp'),
                    'telegram' => $this->input->post('telegram'),
                    'youtube' => $this->input->post('youtube'),
                );
                $data = $this->security->xss_clean($data);
                $result = $this->settings_model->update_settings($data);

				if ($result){
				    $this->session->set_flashdata('success', 'Updated Successfully');
				    redirect(base_url('admin/settings/social'));
				}else{
                    $this->session->set_flashdata('warning', 'Error when saving Data');
				    redirect(base_url('admin/settings/social'));
                }
		}
        
		$data['admin'] = $this->admin_model->get_admin();
        
		$data['settings'] = $this->settings_model->get_settings();
		$data['title'] = 'Settings';
		$data['view'] = 'admin/settings/settings';
		$this->load->view('admin/layout', $data);
	}

    /*
    *-------------------------------------------------------------------------------------------------
    * PAYMENT SETTINGS
    *-------------------------------------------------------------------------------------------------
    */

    /*
    * Payment Settings
    */
    public function payments()
    {
        /*check mercado pago
        if (empty($this->db->where('name_key', 'mercado_pago')->get('payment_gateways')->row())) {
            $sql="INSERT INTO `payment_gateways` (`name`, `name_key`, `public_key`, `secret_key`, `environment`, `locale`, `base_currency`, `status`, `logos`) VALUES('Mercado Pago', 'mercado_pago', '', '', 'production', '', 'BRL', 0, 'visa,mastercard,amex,discover,mercado_pago');";
            $this->db->query($sql);
        }*/
        
		$data['title'] = 'Settings';
		$data['settings'] = $this->settings_model->get_settings();
        $data['currencies'] = $this->currency_model->get_currencies();
		$data['view'] = 'admin/payments/settings';
		$this->load->view('admin/layout', $data);
    }

    /**
     * Payment Gateway Settings Post
     */
    public function payment_gateway_settings_post()
    {
        $name_key = $this->input->post('name_key');
        if ($this->settings_model->update_payment_gateway($name_key)) {
			$this->session->set_flashdata('success', get_phrase("updated_successfully"));
        } else {
            $this->session->set_flashdata('error', get_phrase("error_when_saving_data"));
        }
        //$this->session->set_flashdata("mes_" . $name_key, 1);
        redirect($_SERVER['HTTP_REFERER']);
    }


}

?>	