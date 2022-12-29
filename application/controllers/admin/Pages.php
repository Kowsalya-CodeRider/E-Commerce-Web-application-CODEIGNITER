<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Pages extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('admin/pages_model');
	}

	public function index()
	{
       
	    if(!$this->session->has_userdata('is_admin_login'))
		{
			redirect('admin/auth/login');
		}else{
            redirect('admin/pages/list');
        }
	}

	public function list()
	{
        
		$data['pages'] = $this->pages_model->get_pages();
		$data['title'] = 'Pages';
		$data['view'] = 'admin/pages/pages';
		$this->load->view('admin/layout', $data);
	}

	public function add()
	{
	    
		if (isset($_POST) && !empty($_POST)) 
		{

	        //validate inputs
			$this->form_validation->set_rules('title','Title','trim|required');
			$this->form_validation->set_rules('description','Description','trim|required');
			$this->form_validation->set_rules('keywords','Keywords','trim|required');
			$this->form_validation->set_rules('content','Content','trim|required');
            
			if ($this->form_validation->run() == FALSE) 
			{
				$data = array(
					'errors' => validation_errors(), 
				);
				$this->session->set_flashdata('validation_errors', $data['errors']);
				redirect(base_url('admin/pages/add'));
			}else{

                $slug = slugify($this->input->post('title'));
                $data = array(
                    'title' => ucfirst($this->input->post('title')),
                    'slug' => $slug,
                    'description' => $this->input->post('description'),
                    'keywords' => $this->input->post('keywords'),
                    'content' => $this->input->post('content'),
					'created_at' => date('Y-m-d H:i:s'),
					'updated_at' => date('Y-m-d H:i:s')
                );
                $data = $this->security->xss_clean($data);
                $result = $this->pages_model->add($data);

				if ($result){
				    $this->session->set_flashdata('success', 'Saved Successfully');
				    redirect(base_url('admin/pages/add'));
				}else{
                    $this->session->set_flashdata('warning', 'Error when saving Data');
				    redirect(base_url('admin/pages/add'));
                }
			}
		}
        
		$data['title'] = 'Pages';
		$data['view'] = 'admin/pages/pages';
		$this->load->view('admin/layout', $data);
	}

	public function edit($id)
	{
        $data['page'] = $this->pages_model->get($id);
	    
		if (isset($_POST) && !empty($_POST)) 
		{

	        //validate inputs
			$this->form_validation->set_rules('title','Title','trim|required');
			$this->form_validation->set_rules('description','Description','trim|required');
			$this->form_validation->set_rules('keywords','Keywords','trim|required');
			$this->form_validation->set_rules('content','Content','trim|required');
            
			if ($this->form_validation->run() == FALSE) 
			{
				$data = array(
					'errors' => validation_errors(), 
				);
				$this->session->set_flashdata('validation_errors', $data['errors']);
				redirect(base_url('admin/pages/edit/'. $id));
			}else{

                $slug = slugify($this->input->post('title'));
                $data = array(
                    'title' => ucfirst($this->input->post('title')),
                    'slug' => $slug,
                    'description' => $this->input->post('description'),
                    'keywords' => $this->input->post('keywords'),
                    'content' => $this->input->post('content'),
					'updated_at' => date('Y-m-d H:i:s')
                );
                $data = $this->security->xss_clean($data);
                $id = $this->input->post('id', true);
                $result = $this->pages_model->update($id, $data);

				if ($result){
				    $this->session->set_flashdata('success', 'Updated Successfully');
				    redirect(base_url('admin/pages/edit/'. $id));
				}else{
                    $this->session->set_flashdata('warning', 'Error when saving Data');
				    redirect(base_url('admin/pages/edit/'. $id));
                }
			}
		}
        
		$data['title'] = 'Pages';
		$data['view'] = 'admin/pages/pages';
		$this->load->view('admin/layout', $data);
	}
    
	public function delete()
	{
        $id = $this->input->post('id', true);
        if ($this->pages_model->delete($id)) {
            $this->session->set_flashdata('success', get_phrase("deleted_successfully"));
        } else {
            $this->session->set_flashdata('error', get_phrase("error_on_deleting"));
        }
	}

}

?>	