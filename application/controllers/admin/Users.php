<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('admin/users_model');
	}

	public function index()
	{
       
	    if(!$this->session->has_userdata('is_admin_login'))
		{
			redirect('admin/auth/login');
		}else{
            redirect('admin/users/list');
        }
	}

	public function list()
	{
        
		$data['users'] = $this->users_model->get_users();

		$data['title'] = 'Users Settings';
		$data['view'] = 'admin/users/users';
		$this->load->view('admin/layout', $data);
	}
    
	public function add()
	{
	    
		if (isset($_POST) && !empty($_POST)) 
		{

	        //validate inputs
			$this->form_validation->set_rules('name','name','trim|required|min_length[3]');
			$this->form_validation->set_rules('email','email','trim|required|min_length[5]|valid_email|is_unique[users.email]');
			$this->form_validation->set_rules('password','password','trim|required|min_length[3]');
            
			if ($this->form_validation->run() == FALSE) 
			{
				$data = array(
					'errors' => validation_errors(), 
				);
				$this->session->set_flashdata('validation_errors', $data['errors']);
				redirect(base_url('admin/users/add'));
			}else{
				$data = array(
					'userid' => uniqueid(),
					'name' => $this->input->post('name'), 
					'email' => $this->input->post('email'),
					'password' => password_hash($this->input->post('password'), PASSWORD_DEFAULT),
					'imagelocation' => 'default.png',
					'created_at' => date('Y-m-d H:i:s'),
					'updated_at' => date('Y-m-d H:i:s')
				);

				$data = $this->security->xss_clean($data); // XSS Clean Data

				$result = $this->users_model->add($data);
				
				if ($result){
				    $this->session->set_flashdata('success', 'Added Successfully');
					redirect(base_url('admin/users/add'));
				}else{
                    $this->session->set_flashdata('warning', 'Error when saving Data');
					redirect(base_url('admin/users/add'));
                }
			}
        }
		$data['view'] = 'admin/users/users';
        $this->load->view('admin/layout', $data);
	}	

	public function edit($id)
	{
		if ($this->input->post('details')) 
		{
	    
            $id = $this->input->post('id', true);
	        //validate inputs
			$this->form_validation->set_rules('name','name','trim|required|min_length[3]');
			$this->form_validation->set_rules('email','email','trim|required|min_length[5]|valid_email');
            
			if ($this->form_validation->run() == FALSE) 
			{
				$data = array(
					'errors' => validation_errors(), 
				);
				$this->session->set_flashdata('validation_errors', $data['errors']);
				redirect(base_url('admin/users/edit/'. $id));
			}else{
                $data = array(
					'name' => $this->input->post('name'), 
					'email' => $this->input->post('email'),
                );
                $data = $this->security->xss_clean($data);
                $result = $this->users_model->update($id, $data);

				if ($result){
				    $this->session->set_flashdata('success', 'Updated Successfully');
				    redirect(base_url('admin/users/edit/'. $id));
				}else{
                    $this->session->set_flashdata('warning', 'Error when saving Data');
				    redirect(base_url('admin/users/edit/'. $id));
                }
			}
		}
	    
		if ($this->input->post('image')) 
		{
	    
            $id = $this->input->post('id', true);
            $old_image = $this->input->post('old_image');
            $path="public/uploads/users/";

            if(!empty($_FILES['image']['name']))
            {
                if($old_image != 'default.png'){
                   $this->functions->delete_file($path, $old_image);
                }

                $result = $this->functions->file_insert($path, 'image', 'image', '9097152');
                if($result['status'] == 1){
                    $data['imagelocation'] = $result['msg'];
                    $data = $this->security->xss_clean($data);
                    $result = $this->users_model->update($id, $data);

                    if ($result){
                        $this->session->set_flashdata('success', 'Updated Successfully');
				        redirect(base_url('admin/users/edit/'. $id));
                    }else{
                        $this->session->set_flashdata('warning', 'Error when saving Data');
				        redirect(base_url('admin/users/edit/'. $id));
                    }
                }
                else{
                    $this->session->set_flashdata('error', $result['msg']);
				    redirect(base_url('admin/users/edit/'. $id));
                }
            }else{
                
				$data = array(
					'errors' => 'Image not selected', 
				);
				$this->session->set_flashdata('validation_errors', $data['errors']);
				redirect(base_url('admin/users/edit/'. $id));
            }
		}
        
		if ($this->input->post('pass')) 
		{
	    
            $id = $this->input->post('id', true);
	        //validate inputs
			$this->form_validation->set_rules('password','Password','trim|required|min_length[3]');
            
			if ($this->form_validation->run() == FALSE) 
			{
				$data = array(
					'errors' => validation_errors(), 
				);
				$this->session->set_flashdata('validation_errors', $data['errors']);
				redirect(base_url('admin/users/edit/'. $id));
			}else{
                $data = array(
					'password' => password_hash($this->input->post('password'), PASSWORD_DEFAULT)
                );
                $data = $this->security->xss_clean($data);
                $result = $this->users_model->update($id, $data);

				if ($result){
				    $this->session->set_flashdata('success', 'Updated Successfully');
				    redirect(base_url('admin/users/edit/'. $id));
				}else{
                    $this->session->set_flashdata('warning', 'Error when saving Data');
				    redirect(base_url('admin/users/edit/'. $id));
                }
			}
		}
        
        $data['user'] = $this->users_model->get($id);
		$data['title'] = 'Users Settings';
		$data['view'] = 'admin/users/users';
        $this->load->view('admin/layout', $data);
	}
    
	public function delete()
	{
        $id = $this->input->post('id', true);
        $user = $this->users_model->get($id);
        $path="public/uploads/users/";
        
        if($user->imagelocation != 'default.png'){
           $this->functions->delete_file($path, $user->imagelocation);
        }        
        
        if ($this->users_model->delete($id)) {
            $this->session->set_flashdata('success', get_phrase("deleted_successfully"));
        } else {
            $this->session->set_flashdata('error', get_phrase("error_on_deleting"));
        }
	}

}  // end class

?>