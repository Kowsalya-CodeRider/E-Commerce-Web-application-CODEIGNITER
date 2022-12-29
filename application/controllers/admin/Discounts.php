<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Discounts extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('admin/discounts_model');
	}

	public function index()
	{
       
	    if(!$this->session->has_userdata('is_admin_login'))
		{
			redirect('admin/auth/login');
		}else{
            redirect('admin/discounts/list');
        }
	}

	public function list()
	{
        
		$data['discounts'] = $this->discounts_model->get_discounts();

		$data['title'] = 'Discounts Settings';
		$data['view'] = 'admin/discounts/discounts';
		$this->load->view('admin/layout', $data);
	}
    
	public function add()
	{
	    
		if (isset($_POST) && !empty($_POST)) 
		{

	        //validate inputs
			$this->form_validation->set_rules('promocode','Promocode','trim|required|min_length[3]');
			$this->form_validation->set_rules('message','Message','trim|required|min_length[5]');
			$this->form_validation->set_rules('start_date','Start date','trim|required');
			$this->form_validation->set_rules('end_date','End date','trim|required');
			$this->form_validation->set_rules('no_users','No of users','trim|required');
			$this->form_validation->set_rules('min_order_amount','Minimum order amount','trim|required');
			$this->form_validation->set_rules('discount_type','Discount type','trim|required');
			$this->form_validation->set_rules('discount','Discount','trim|required');
			$this->form_validation->set_rules('max_discount_amount','Maximum discount amount','trim|required');
			$this->form_validation->set_rules('status','Status','trim|required');
			
			if ($this->form_validation->run() == FALSE) 
			{
				$data = array(
					'errors' => validation_errors(), 
				);
				$this->session->set_flashdata('validation_errors', $data['errors']);
				redirect(base_url('admin/discounts/add'));
			}else{
				$data = array(
					'promocode'   			=> $this->input->post('promocode'), 
					'message'     			=> $this->input->post('message'), 
					'start_date'  			=> $this->input->post('start_date'), 
					'end_date'    			=> $this->input->post('end_date'), 
					'no_users' 				=> $this->input->post('no_users'), 
					'min_order_amount'  	=> $this->input->post('min_order_amount'), 
					'discount' 				=> $this->input->post('discount'), 
					'discount_type' 		=> $this->input->post('discount_type'), 
					'max_discount_amount' 	=> $this->input->post('max_discount_amount'), 
					'status' 				=> $this->input->post('status'), 
					'created_at' 			=> date('Y-m-d H:i:s'),
					'updated_on' 			=> date('Y-m-d H:i:s')
				);

				$data = $this->security->xss_clean($data); // XSS Clean Data

				$result = $this->discounts_model->add($data);
				
				if ($result){
				    $this->session->set_flashdata('success', 'Added Successfully');
					redirect(base_url('admin/discounts/add'));
				}else{
                    $this->session->set_flashdata('warning', 'Error when saving Data');
					redirect(base_url('admin/discounts/add'));
                }
			}
        }
		$data['view'] = 'admin/discounts/discounts';
        $this->load->view('admin/layout', $data);
	}	

	public function edit($id)
	{
		if ($this->input->post('details')) 
		{
	    
            $id = $this->input->post('id', true);
	        //validate inputs
			$this->form_validation->set_rules('promocode','Promocode','trim|required|min_length[3]');
			$this->form_validation->set_rules('message','Message','trim|required|min_length[5]');
			$this->form_validation->set_rules('start_date','Start date','trim|required');
			$this->form_validation->set_rules('end_date','End date','trim|required');
			$this->form_validation->set_rules('no_users','No of users','trim|required');
			$this->form_validation->set_rules('min_order_amount','Minimum order amount','trim|required');
			$this->form_validation->set_rules('discount_type','Discount type','trim|required');
			$this->form_validation->set_rules('discount','Discount','trim|required');
			$this->form_validation->set_rules('max_discount_amount','Maximum discount amount','trim|required');
			$this->form_validation->set_rules('status','Status','trim|required');
			
			if ($this->form_validation->run() == FALSE) 
			{
				$data = array(
					'errors' => validation_errors(), 
				);
				$this->session->set_flashdata('validation_errors', $data['errors']);
				redirect(base_url('admin/discounts/edit/'. $id));
			}else{
                $data = array(
					'promocode'   			=> $this->input->post('promocode'), 
					'message'     			=> $this->input->post('message'), 
					'start_date'  			=> $this->input->post('start_date'), 
					'end_date'    			=> $this->input->post('end_date'), 
					'no_users' 				=> $this->input->post('no_users'), 
					'min_order_amount'  	=> $this->input->post('min_order_amount'), 
					'discount' 				=> $this->input->post('discount'), 
					'discount_type' 		=> $this->input->post('discount_type'), 
					'max_discount_amount' 	=> $this->input->post('max_discount_amount'), 
					'status' 				=> $this->input->post('status'), 
					'created_at' 			=> date('Y-m-d H:i:s'),
					'updated_on' 			=> date('Y-m-d H:i:s')
				);
                $data = $this->security->xss_clean($data);
                $result = $this->discounts_model->update($id, $data);

				if ($result){
				    $this->session->set_flashdata('success', 'Updated Successfully');
				    redirect(base_url('admin/discounts/edit/'. $id));
				}else{
                    $this->session->set_flashdata('warning', 'Error when saving Data');
				    redirect(base_url('admin/discounts/edit/'. $id));
                }
			}
		}
	   
        
        $data['discount'] = $this->discounts_model->get($id);
		$data['title'] = 'Discounts Settings';
		$data['view'] = 'admin/discounts/discounts';
        $this->load->view('admin/layout', $data);
	}
    
	public function delete()
	{
        $id = $this->input->post('id', true);       
        if ($this->discounts_model->delete($id)) {
            $this->session->set_flashdata('success', get_phrase("deleted_successfully"));
        } else {
            $this->session->set_flashdata('error', get_phrase("error_on_deleting"));
        }
	}

}  // end class

?>