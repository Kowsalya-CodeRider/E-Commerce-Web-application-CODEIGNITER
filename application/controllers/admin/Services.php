<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Services extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('admin/services_model');
		$this->load->model('admin/categories_model');
		$this->load->model('admin/currency_model');
	}

	public function index()
	{
       
	    if(!$this->session->has_userdata('is_admin_login'))
		{
			redirect('admin/auth/login');
		}else{
            redirect('admin/services/list');
        }
	}

	public function list()
	{
        
		$data['services'] = $this->services_model->get_services();
		$data['categories'] = $this->categories_model->get_categories();
		$data['title'] = 'Services';
		$data['view'] = 'admin/services/services';
		$this->load->view('admin/layout', $data);
	}

	public function add()
	{
	    
		if (isset($_POST) && !empty($_POST)) 
		{
            
            $path="public/uploads/services/";

            if(!empty($_FILES['image']['name']))
            {
                
                //validate inputs
                $this->form_validation->set_rules('title','Title','trim|required');
                $this->form_validation->set_rules('description','Description','trim|required');
                $this->form_validation->set_rules('keywords','Keywords','trim|required');
                $this->form_validation->set_rules('content','Content','trim|required');
                $this->form_validation->set_rules('price','Price','trim|required|numeric');
                $this->form_validation->set_rules('time_duration','Time to Complete','trim|required');
                $this->form_validation->set_rules('category_id','Category','trim|required');

                if ($this->form_validation->run() == FALSE) 
                {
                    $data = array(
                        'errors' => validation_errors(), 
                    );
                    $this->session->set_flashdata('validation_errors', $data['errors']);
                    redirect(base_url('admin/services/add'));
                }else{
                    
                    $upload = $this->functions->file_insert($path, 'image', 'image', '9097152');
                    if($upload['status'] == 1){
                        
                        $slug = slugify($this->input->post('title'));
                        $data = array(
                            'title' => ucfirst($this->input->post('title')),
                            'slug' => $slug,
                            'price' => $this->input->post('price'),
                            'time_duration' => $this->input->post('time_duration'),
                            'category_id' => $this->input->post('category_id'),
                            'description' => $this->input->post('description'),
                            'keywords' => $this->input->post('keywords'),
                            'content' => $this->input->post('content'),
                            'imagelocation' => $upload['msg'],
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s')
                        );
                        $data = $this->security->xss_clean($data);
                        $result = $this->services_model->add($data);

                        if ($result){
                            $this->session->set_flashdata('success', 'Saved Successfully');
                            redirect(base_url('admin/services/add'));
                        }else{
                            $this->session->set_flashdata('warning', 'Error when saving Data');
                            redirect(base_url('admin/services/add'));
                        }
                    }
                    else{
                        $this->session->set_flashdata('error', $upload['msg']);
                        redirect(base_url('admin/services/add'));
                    }
                }                

            }else{
                
				$data = array(
					'errors' => 'Image not selected', 
				);
				$this->session->set_flashdata('validation_errors', $data['errors']);
				redirect(base_url('admin/services/add'));
            }            
            
		}
        
        
		$data['categories'] = $this->categories_model->get_categories();
		$data['title'] = 'Services';
		$data['view'] = 'admin/services/services';
		$this->load->view('admin/layout', $data);
	}

	public function edit($id)
	{
        $data['service'] = $this->services_model->get($id);
	    
		if (isset($_POST) && !empty($_POST)) 
		{
            
            $path="public/uploads/services/";
            $old_image = $this->input->post('old_image');
            $id = $this->input->post('id', true);

            if(!empty($_FILES['image']['name']))
            {
                
                //validate inputs
                $this->form_validation->set_rules('title','Title','trim|required');
                $this->form_validation->set_rules('description','Description','trim|required');
                $this->form_validation->set_rules('keywords','Keywords','trim|required');
                $this->form_validation->set_rules('content','Content','trim|required');
                $this->form_validation->set_rules('price','Price','trim|required|numeric');
                $this->form_validation->set_rules('time_duration','Time to Complete','trim|required');
                $this->form_validation->set_rules('category_id','Category','trim|required');

                if ($this->form_validation->run() == FALSE) 
                {
                    $data = array(
                        'errors' => validation_errors(), 
                    );
                    $this->session->set_flashdata('validation_errors', $data['errors']);
				    redirect(base_url('admin/services/edit/'. $id));
                }else{
                    
                    $this->functions->delete_file($path, $old_image);
                    $upload = $this->functions->file_insert($path, 'image', 'image', '9097152');
                    if($upload['status'] == 1){
                        
                        $slug = slugify($this->input->post('title'));
                        $data = array(
                            'title' => ucfirst($this->input->post('title')),
                            'slug' => $slug,
                            'price' => $this->input->post('price'),
                            'time_duration' => $this->input->post('time_duration'),
                            'category_id' => $this->input->post('category_id'),
                            'description' => $this->input->post('description'),
                            'keywords' => $this->input->post('keywords'),
                            'content' => $this->input->post('content'),
                            'imagelocation' => $upload['msg'],
                            'updated_at' => date('Y-m-d H:i:s')
                        );
                        $data = $this->security->xss_clean($data);
                        $result = $this->services_model->update($id, $data);

                        if ($result){
                            $this->session->set_flashdata('success', 'Saved Successfully');
				            redirect(base_url('admin/services/edit/'. $id));
                        }else{
                            $this->session->set_flashdata('warning', 'Error when saving Data');
				            redirect(base_url('admin/services/edit/'. $id));
                        }
                    }
                    else{
                        $this->session->set_flashdata('error', $upload['msg']);
				        redirect(base_url('admin/services/edit/'. $id));
                    }
                }                

            }else{
                
                //validate inputs
                $this->form_validation->set_rules('title','Title','trim|required');
                $this->form_validation->set_rules('description','Description','trim|required');
                $this->form_validation->set_rules('keywords','Keywords','trim|required');
                $this->form_validation->set_rules('content','Content','trim|required');
                $this->form_validation->set_rules('price','Price','trim|required|numeric');
                $this->form_validation->set_rules('time_duration','Time to Complete','trim|required');
                $this->form_validation->set_rules('category_id','Category','trim|required');

                if ($this->form_validation->run() == FALSE) 
                {
                    $data = array(
                        'errors' => validation_errors(), 
                    );
                    $this->session->set_flashdata('validation_errors', $data['errors']);
				    redirect(base_url('admin/services/edit/'. $id));
                }else{
                    
                    $slug = make_slug($this->input->post('title'));
                    $data = array(
                        'title' => ucfirst($this->input->post('title')),
                        'slug' => $slug,
                        'price' => $this->input->post('price'),
                        'time_duration' => $this->input->post('time_duration'),
                        'category_id' => $this->input->post('category_id'),
                        'description' => $this->input->post('description'),
                        'keywords' => $this->input->post('keywords'),
                        'content' => $this->input->post('content'),
                        'updated_at' => date('Y-m-d H:i:s')
                    );
                    $data = $this->security->xss_clean($data);
                    $result = $this->services_model->update($id, $data);

                    if ($result){
                        $this->session->set_flashdata('success', 'Saved Successfully');
                        redirect(base_url('admin/services/edit/'. $id));
                    }else{
                        $this->session->set_flashdata('warning', 'Error when saving Data');
                        redirect(base_url('admin/services/edit/'. $id));
                    }
                        
                } 
            }            
            
		}
        
		$data['categories'] = $this->categories_model->get_categories();
		$data['title'] = 'Services';
		$data['view'] = 'admin/services/services';
		$this->load->view('admin/layout', $data);
	}
    
	public function delete()
	{
        $id = $this->input->post('id', true);
        $service = $this->services_model->get($id);
        $path="public/uploads/services/";
        
        $this->functions->delete_file($path, $service->imagelocation);
        
        if ($this->services_model->delete($id)) {
            $this->session->set_flashdata('success', get_phrase("deleted_successfully"));
        } else {
            $this->session->set_flashdata('error', get_phrase("error_on_deleting"));
        }
	}

}

?>	