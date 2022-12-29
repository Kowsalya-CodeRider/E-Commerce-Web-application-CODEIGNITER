<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Transactions extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('admin/transactions_model');
		$this->load->model('admin/users_model');
	}

	public function index()
	{
		$data['transactions'] = $this->transactions_model->get_transactions();
		$data['users'] = $this->users_model->get_users();
		$data['title'] = 'Transactions';
		$data['view'] = 'admin/transactions/transactions';
		$this->load->view('admin/layout', $data);
	}
    
}