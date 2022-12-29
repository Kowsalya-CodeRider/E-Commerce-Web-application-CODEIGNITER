<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Categories extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('admin/categories_model');
	}

	public function index()
	{
       
	    if(!$this->session->has_userdata('is_admin_login'))
		{
			redirect('admin/auth/login');
		}else{
            redirect('admin/categories/list');
        }
	}

	public function list()
	{
        
		$data['categories'] = $this->categories_model->get_categories();
		$data['title'] = 'Categories';
		$data['view'] = 'admin/categories/categories';
		$this->load->view('admin/layout', $data);
	}

	public function add()
	{
	    
		if (isset($_POST) && !empty($_POST)) 
		{

	        //validate inputs
			$this->form_validation->set_rules('name','Name','trim|required');
            
			if ($this->form_validation->run() == FALSE) 
			{
				$data = array(
					'errors' => validation_errors(), 
				);
				$this->session->set_flashdata('validation_errors', $data['errors']);
				redirect(base_url('admin/categories/add'));
			}else{

                $slug = slugify($this->input->post('name'));
                $data = array(
                    'name' => ucfirst($this->input->post('name')),
                    'slug' => $slug
                );
                $data = $this->security->xss_clean($data);
                $result = $this->categories_model->add($data);

				if ($result){
				    $this->session->set_flashdata('success', 'Saved Successfully');
				    redirect(base_url('admin/categories/add'));
				}else{
                    $this->session->set_flashdata('warning', 'Error when saving Data');
				    redirect(base_url('admin/categories/add'));
                }
			}
		}
        
		$data['title'] = 'Categories';
		$data['view'] = 'admin/categories/categories';
		$this->load->view('admin/layout', $data);
	}

	public function edit($id)
	{
        $data['category'] = $this->categories_model->get($id);
	    
		if (isset($_POST) && !empty($_POST)) 
		{

	        //validate inputs
			$this->form_validation->set_rules('name','Name','trim|required');
            
			if ($this->form_validation->run() == FALSE) 
			{
				$data = array(
					'errors' => validation_errors(), 
				);
				$this->session->set_flashdata('validation_errors', $data['errors']);
				redirect(base_url('admin/categories/edit/'. $id));
			}else{

                $slug = slugify($this->input->post('name'));
                $data = array(
                    'name' => ucfirst($this->input->post('name')),
                    'slug' => $slug
                );
                $data = $this->security->xss_clean($data);
                $id = $this->input->post('id', true);
                $result = $this->categories_model->update($id, $data);

				if ($result){
				    $this->session->set_flashdata('success', 'Updated Successfully');
				    redirect(base_url('admin/categories/edit/'. $id));
				}else{
                    $this->session->set_flashdata('warning', 'Error when saving Data');
				    redirect(base_url('admin/categories/edit/'. $id));
                }
			}
		}
        
		$data['title'] = 'Categories';
		$data['view'] = 'admin/categories/categories';
		$this->load->view('admin/layout', $data);
	}
    
	public function delete()
	{
        $id = $this->input->post('id', true);
        if ($this->categories_model->delete($id)) {
            $this->session->set_flashdata('success', get_phrase("deleted_successfully"));
        } else {
            $this->session->set_flashdata('error', get_phrase("error_on_deleting"));
        }
	}

}

?>	