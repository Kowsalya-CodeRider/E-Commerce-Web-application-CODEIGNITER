<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Orders extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('admin/orders_model');
		$this->load->model('admin/users_model');
		$this->load->model('admin/services_model');
	}

	public function index()
	{
		$data['orders'] = $this->orders_model->get_orders();
		$data['users'] = $this->users_model->get_users();
		$data['services'] = $this->services_model->get_services();
		$data['title'] = 'Orders';
		$data['view'] = 'admin/orders/orders';
		$this->load->view('admin/layout', $data);
	}
    
	public function completed()
	{
        $id = $this->input->post('id', true);
        if ($this->orders_model->completed($id)) {
            $this->session->set_flashdata('success', get_phrase("completed_successfully"));
        } else {
            $this->session->set_flashdata('error', get_phrase("error_on_data_saving"));
        }
	}
    
	public function not_completed()
	{
        $id = $this->input->post('id', true);
        if ($this->orders_model->not_completed($id)) {
            $this->session->set_flashdata('success', get_phrase("successfully_updated"));
        } else {
            $this->session->set_flashdata('error', get_phrase("error_on_data_saving"));
        }
	}
    
}