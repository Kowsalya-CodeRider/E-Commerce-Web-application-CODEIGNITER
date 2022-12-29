<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Contact extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('admin/contact_model');
	}

	public function index()
	{
        
		$data['contacts'] = $this->contact_model->get_contacts();
		$data['title'] = 'Contact';
		$data['view'] = 'admin/contact/contact';
		$this->load->view('admin/layout', $data);
	}
    
}

?>