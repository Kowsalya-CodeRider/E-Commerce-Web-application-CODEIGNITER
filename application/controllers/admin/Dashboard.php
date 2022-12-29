<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('admin/admin_model');
	}

	public function index()
	{
        
		$data['admin'] = $this->admin_model->get_admin();
		$data['total_orders'] = $this->admin_model->total_orders();
		$data['total_users'] = $this->admin_model->total_users();
		$data['total_services'] = $this->admin_model->total_services();
		$data['total_revenue'] = $this->admin_model->total_revenue();
        
		$data['chart_data'] = $this->admin_model->chart();
		$data['user_data'] = $this->admin_model->user_chart();
        //var_dump($chart);
        
		$data['title'] = 'Dashboard';
		$data['page_name'] = 'dashboard';
		$data['view'] = 'admin/dashboard/dashboard';
		$this->load->view('admin/layout', $data);
	}

}

?>	