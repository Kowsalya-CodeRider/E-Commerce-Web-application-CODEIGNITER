<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Profile extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('admin/admin_model');
	}

	public function index()
	{
       
	    if(!$this->session->has_userdata('is_admin_login'))
		{
			redirect('admin/auth/login');
		}else{
            redirect('admin/profile/details');
        }
	}

	public function details()
	{
	    
		if (isset($_POST) && !empty($_POST)) 
		{

	        //validate inputs
			$this->form_validation->set_rules('name','name','trim|required|min_length[3]');
			$this->form_validation->set_rules('email','email','trim|required|min_length[5]|valid_email');
            
			if ($this->form_validation->run() == FALSE) 
			{
				$data = array(
					'errors' => validation_errors(), 
				);
				$this->session->set_flashdata('validation_errors', $data['errors']);
				redirect(base_url('admin/profile/details'));
			}else{
                
                $data = array(
                    'name' => $this->input->post('name'),
                    'email' => $this->input->post('email'),
                    'updated_at' => date('Y-m-d : h:m:s'),
                );
                $data = $this->security->xss_clean($data);
                $result = $this->admin_model->update_admin($data);

				if ($result){
				    $this->session->set_flashdata('success', 'Updated Successfully');
					redirect(base_url('admin/profile/details'));
				}else{
                    $this->session->set_flashdata('warning', 'Error when saving Data');
					redirect(base_url('admin/profile/details'));
                }
			}
		}
        
		$data['admin'] = $this->admin_model->get_admin();

		$data['title'] = 'Profile';
		$data['view'] = 'admin/profile/profile';
		$this->load->view('admin/layout', $data);
	}

	public function image()
	{
	    
		if (isset($_POST) && !empty($_POST)) 
		{

            $old_image = $this->input->post('old_image');
            $path="public/uploads/admin/";

            if(!empty($_FILES['image']['name']))
            {
                if($old_image != 'default.png'){
                   $this->functions->delete_file($path, $old_image);
                }

                $result = $this->functions->file_insert($path, 'image', 'image', '9097152');
                if($result['status'] == 1){
                    $data['imagelocation'] = $result['msg'];
                    $data = $this->security->xss_clean($data);
                    $result = $this->admin_model->update_image($data);

                    if ($result){
                        $this->session->set_flashdata('success', 'Updated Successfully');
                        redirect(base_url('admin/profile/image'));
                    }else{
                        $this->session->set_flashdata('warning', 'Error when saving Data');
                        redirect(base_url('admin/profile/image'));
                    }
                }
                else{
                    $this->session->set_flashdata('error', $result['msg']);
                    redirect(base_url('admin/profile/image'));
                }
            }else{
                
				$data = array(
					'errors' => 'Image not selected', 
				);
				$this->session->set_flashdata('validation_errors', $data['errors']);
				redirect(base_url('admin/profile/image'));
            }
		}
        
		$data['admin'] = $this->admin_model->get_admin();
		$data['title'] = 'Profile';
		$data['view'] = 'admin/profile/profile';
		$this->load->view('admin/layout', $data);
	}

	public function password()
	{
        
		$data['admin'] = $this->admin_model->get_admin();
        
		if(isset($_POST) && !empty($_POST)){
            
			$this->form_validation->set_rules('password_current', 'Password Current', 'trim|required');
			$this->form_validation->set_rules('password', 'Password', 'trim|required');
			$this->form_validation->set_rules('confirm_password', 'Confirm Password', 'trim|required|matches[password]');
            
			if ($this->form_validation->run() == FALSE) {
				$data = array(
					'errors' => validation_errors(), 
				);
				$this->session->set_flashdata('validation_errors', $data['errors']);
				redirect(base_url('admin/profile/password'));
			}
			else{

				if (password_verify($this->input->post('password_current'), $data['admin']['password'])) {
                        $data = array(
                            'password' => password_hash($this->input->post('password'), PASSWORD_DEFAULT)
                        );
                        $data = $this->security->xss_clean($data);
                        $result = $this->admin_model->change_password($data);
                        if($result){
                            $this->session->set_flashdata('success', get_phrase('password_changed_successfully'));
                            redirect(base_url('admin/profile/password'));
                        }else{
                            $this->session->set_flashdata('warning', get_phrase('error_when_saving_data'));
                            redirect(base_url('admin/profile/password'));
                        }
					
				} else {
                            $this->session->set_flashdata('warning', get_phrase('current_password_does_not_match'));
                            redirect(base_url('admin/profile/password'));
				 
				}
			}
		}
        
		$data['title'] = 'Profile';
		$data['view'] = 'admin/profile/profile';
		$this->load->view('admin/layout', $data);
	}

	function admin_check()
	{
		if(!$this->session->has_userdata('is_admin_login'))
			redirect('admin/auth/login');
	}

}

?>	