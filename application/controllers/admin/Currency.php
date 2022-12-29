<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Currency extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('admin/admin_model');
		$this->load->model('admin/currency_model');
	}

	public function index()
	{
       
	    if(!$this->session->has_userdata('is_admin_login'))
		{
			redirect('admin/auth/login');
		}else{
            redirect('admin/currency/default');
        }
	}

	public function default()
	{
	    
		if (isset($_POST) && !empty($_POST)) 
		{

	        //validate inputs
			$this->form_validation->set_rules('default_currency','Default Currency','trim|required');
            
			if ($this->form_validation->run() == FALSE) 
			{
				$data = array(
					'errors' => validation_errors(), 
				);
				$this->session->set_flashdata('validation_errors', $data['errors']);
				redirect(base_url('admin/currency/default'));
			}else{
                $data = array(
                    'default_currency' => $this->input->post('default_currency', true)
                );
                $data = $this->security->xss_clean($data);
                $result = $this->currency_model->update_default_currency($data);

				if ($result){
				    $this->session->set_flashdata('success', 'Updated Successfully');
				    redirect(base_url('admin/currency/default'));
				}else{
                    $this->session->set_flashdata('warning', 'Error when saving Data');
				    redirect(base_url('admin/currency/default'));
                }
			}
		}
        
		$data['currencies'] = $this->currency_model->get_currencies();

		$data['title'] = 'Currency Settings';
		$data['view'] = 'admin/currency/currency';
		$this->load->view('admin/layout', $data);
	}

	public function list()
	{
        
		$data['currencies'] = $this->currency_model->get_currencies();

		$data['title'] = 'Currency Settings';
		$data['view'] = 'admin/currency/currency';
		$this->load->view('admin/layout', $data);
	}

	public function add()
	{
	    
		if (isset($_POST) && !empty($_POST)) 
		{

	        //validate inputs
			$this->form_validation->set_rules('code','Currency Code','trim|required');
			$this->form_validation->set_rules('name','Currency Name','trim|required');
			$this->form_validation->set_rules('symbol','Currency Symbol','trim|required');
			$this->form_validation->set_rules('currency_format','Currency Format','trim|required');
			$this->form_validation->set_rules('symbol_direction','Currency Symbol Direction','trim|required');
			$this->form_validation->set_rules('space_money_symbol','Add Space Between Money and Symbol','trim|required');
			$this->form_validation->set_rules('status','Status','trim|required');
            
			if ($this->form_validation->run() == FALSE) 
			{
				$data = array(
					'errors' => validation_errors(), 
				);
				$this->session->set_flashdata('validation_errors', $data['errors']);
				redirect(base_url('admin/currency/add'));
			}else{
                $data = array(
                    'code' => $this->input->post('code', true),
                    'name' => $this->input->post('name', true),
                    'symbol' => $this->input->post('symbol', true),
                    'currency_format' => $this->input->post('currency_format', true),
                    'symbol_direction' => $this->input->post('symbol_direction', true),
                    'space_money_symbol' => $this->input->post('space_money_symbol', true),
                    'status' => $this->input->post('status', true)
                );
                $data = $this->security->xss_clean($data);
                $result = $this->currency_model->add_currency($data);

				if ($result){
				    $this->session->set_flashdata('success', 'Added Successfully');
				    redirect(base_url('admin/currency/add'));
				}else{
                    $this->session->set_flashdata('warning', 'Error when saving Data');
				    redirect(base_url('admin/currency/add'));
                }
			}
		}
        
		$data['title'] = 'Currency Settings';
		$data['view'] = 'admin/currency/currency';
		$this->load->view('admin/layout', $data);
	}

	public function edit($id)
	{
        $data['currency'] = $this->currency_model->get_currency($id);
	    
		if (isset($_POST) && !empty($_POST)) 
		{
	    
            $id = $this->input->post('id', true);
	        //validate inputs
			$this->form_validation->set_rules('code','Currency Code','trim|required');
			$this->form_validation->set_rules('name','Currency Name','trim|required');
			$this->form_validation->set_rules('symbol','Currency Symbol','trim|required');
			$this->form_validation->set_rules('currency_format','Currency Format','trim|required');
			$this->form_validation->set_rules('symbol_direction','Currency Symbol Direction','trim|required');
			$this->form_validation->set_rules('space_money_symbol','Add Space Between Money and Symbol','trim|required');
			$this->form_validation->set_rules('status','Status','trim|required');
            
			if ($this->form_validation->run() == FALSE) 
			{
				$data = array(
					'errors' => validation_errors(), 
				);
				$this->session->set_flashdata('validation_errors', $data['errors']);
				redirect(base_url('admin/currency/edit/'. $id));
			}else{
                $data = array(
                    'code' => $this->input->post('code', true),
                    'name' => $this->input->post('name', true),
                    'symbol' => $this->input->post('symbol', true),
                    'currency_format' => $this->input->post('currency_format', true),
                    'symbol_direction' => $this->input->post('symbol_direction', true),
                    'space_money_symbol' => $this->input->post('space_money_symbol', true),
                    'status' => $this->input->post('status', true)
                );
                $data = $this->security->xss_clean($data);
                $result = $this->currency_model->update_currency($id, $data);

				if ($result){
				    $this->session->set_flashdata('success', 'Updated Successfully');
				    redirect(base_url('admin/currency/edit/'. $id));
				}else{
                    $this->session->set_flashdata('warning', 'Error when saving Data');
				    redirect(base_url('admin/currency/edit/'. $id));
                }
			}
		}
        
		$data['title'] = 'Currency Settings';
		$data['view'] = 'admin/currency/currency';
		$this->load->view('admin/layout', $data);
	}
    
	public function delete()
	{
        $id = $this->input->post('id', true);
        if ($this->currency_model->delete_currency($id)) {
            $this->session->set_flashdata('success', get_phrase("deleted_successfully"));
        } else {
            $this->session->set_flashdata('error', get_phrase("error_on_deleting"));
        }
	}

}

?>	