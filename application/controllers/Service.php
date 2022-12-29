<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Service extends Main_Controller{

	public function __construct()
	{
		parent::__construct();
		//$this->load->model('home_model');
		$this->load->model('admin/categories_model');
		$this->load->model('admin/services_model');
		$this->load->model('admin/currency_model');
	}

	//-----------------------------------------------------------------------------
	// Index funciton will call bydefault
	public function detail($slug)
	{	
        //$slug = $this->uri->segment(2);
        //$slug = 'website-development-using-htmlcssphp';
		$data['service'] = $this->services_model->get_by_slug($slug);
		$data['categories'] = $this->categories_model->get_categories();
		$data['view'] = 'service';
		$this->load->view('layout', $data);
	}

}

?> 