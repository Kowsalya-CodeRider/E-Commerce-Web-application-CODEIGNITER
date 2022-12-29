<?php
	class MY_Controller extends CI_Controller
	{
		function __construct()
		{
			parent::__construct();
            $this->load->library('session');

			if(!$this->session->has_userdata('is_admin_login'))
			{
				redirect('admin/auth/login');
			}
            $this->load->model('admin/chat_model');
            $this->load->model('admin/message_model');

			//general settings
            $global_data['settings'] = $this->settings_model->get_settings();
	        $this->settings = $global_data['settings'];
	        //$global_data['general_settings'] = $this->setting_model->get_general_settings();
	        //$this->general_settings = $global_data['general_settings'];

	        //set timezone
	        date_default_timezone_set($this->settings['timezone']);

	        //Get Admin
	        $this->admin = $this->admin_model->get_admin();
            //payment settings
            $this->payment_settings = $this->settings_model->get_payment_settings();
            $this->users = $this->users_model->get_users();
            $this->messages = $this->message_model->get_new_messages();
            $this->unread = $this->message_model->unread();
		}
	}


	class Main_Controller extends CI_Controller
	{
		function __construct()
		{
			parent::__construct();
            $this->load->library('session');
            $this->load->model('user/chat_model');
            $this->load->model('user/message_model');
            $this->load->model('user/orders_model');

			//general settings
            $global_data['settings'] = $this->settings_model->get_settings();
	        $this->settings = $global_data['settings'];

			//general settings
            $global_data['payment_settings'] = $this->settings_model->get_payment_settings();
	        $this->payment_settings = $global_data['payment_settings'];

	        //set timezone
	        date_default_timezone_set($this->settings['timezone']);

			if($this->session->has_userdata('is_user_login'))
			{
                //Get User
                $this->user = $this->user_model->get_user();
                $this->unread = $this->message_model->unread();
                $this->total_orders = $this->orders_model->total_orders();
                $this->orders_completed = $this->orders_model->orders_completed();
                $this->orders_in_progress = $this->orders_model->orders_in_progress();
			}
            
		    $this->pages = $this->pages_model->get_pages();

	        
		}

		//verify recaptcha
	    public function recaptcha_verify_request()
	    {
	        if (!$this->recaptcha_status) {
	            return true;
	        }

	        $this->load->library('recaptcha');
	        $recaptcha = $this->input->post('g-recaptcha-response');
	        if (!empty($recaptcha)) {
	            $response = $this->recaptcha->verifyResponse($recaptcha);
	            if (isset($response['success']) && $response['success'] === true) {
	                return true;
	            }
	        }
	        return false;
	    }

	}

?>