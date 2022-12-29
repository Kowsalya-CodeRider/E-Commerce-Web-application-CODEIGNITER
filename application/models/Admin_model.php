<?php if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Admin_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }

	// registraion
	public function register($data)
	{
		$this->db->insert('users',$data);
		return $this->db->insert_id();
	}
	function signin($email, $password)
	{
		$credential = array('email' => $email);
		$query = $this->db->get_where('users', $credential);
        if ($query->num_rows() > 0) {
            $row = $query->row();
		    $validPassword = password_verify($password, $row->password);
		    if($validPassword){
                $this->session->set_userdata('is_admin_login', true);
                $this->session->set_userdata('user_id', $row->id);
                $this->session->set_userdata('name', $row->name); // 1=admin, 0=customer
                return true;
		    }else{
                $this->session->set_flashdata('warning', 'Password Error');
                return false;
            }
        }
		else {
			$this->session->set_flashdata('signin_result', 'failed');
			return false;
		}
	}    
	// login function
	public function login($data)
	{
		$query = $this->db->get_where('xx_users', array('email' => $data['email']));
		if ($query->num_rows() == 0){
			return false;
		}
		else{
			//Compare the password attempt with the password we have stored.
			$result = $query->row_array();
		    $validPassword = password_verify($data['password'], $result['password']);
		    if($validPassword){
		        return $result = $query->row_array();
		    }
			
		}
	}
	/*
	* USER QUERIES
	*/
	function signup_user()
	{
		$data['email'] 		= $this->input->post('email');
		$data['password'] 	= sha1($this->input->post('password'));
		$data['type'] 		= 0; // user type = customer

		$this->db->where('email' , $data['email']);
		$this->db->from('user');
        $total_number_of_matching_user = $this->db->count_all_results();
		// validate if duplicate email exists
		$unverified_user = $this->db->get_where('user', array('email' => $data['email'], 'status' => 0));
        if ($total_number_of_matching_user == 0 || $unverified_user->num_rows() > 0) {
        	if(get_settings('email_verification') == 1){
        		$data['status'] 		= 0;
        		$data['verification_code'] 		= rand(100000, 999999);

        		if($unverified_user->num_rows() > 0){
        			$this->email_model->send_email_verification_mail($data['email'], $unverified_user->row('verification_code'));
        		}else{
        			$this->email_model->send_email_verification_mail($data['email'], $data['verification_code']);
        			$this->db->insert('user' , $data);
        		}
        		$this->session->set_userdata('register_email', $data['email']);
				redirect(base_url().'index.php?home/verification_code' , 'refresh');
        	}else{
        		$data['status'] 		= 1;
        	}


			$this->db->insert('user' , $data);
			$user_id	=	$this->db->insert_id();

			// create a free subscription for premium package for 30 days
			$trial_period	=	$this->get_settings('trial_period');
			if($trial_period == 'on') {
				$this->create_free_subscription($user_id);
			}

            $this->signin($this->input->post('email') , $this->input->post('password'));
			$this->session->set_flashdata('signup_result', 'success');

			if ($total_number_of_matching_user > 0){
        		$this->session->set_flashdata('signup_result', 'failed');
				return false;
        	}else{
				return true;
			}
        }
		else {
			$this->session->set_flashdata('signup_result', 'failed');
			return false;
		}

	}


}
