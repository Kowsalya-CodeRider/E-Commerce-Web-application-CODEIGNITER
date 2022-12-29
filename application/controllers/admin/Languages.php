<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Languages extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('admin/settings_model');
		$this->load->model('admin/admin_model');
	}

	public function index()
	{
       
	    if(!$this->session->has_userdata('is_admin_login'))
		{
			redirect('admin/auth/login');
		}else{
            redirect('admin/languages/list');
        }
	}

	// Language Functions
	public function list($param1 = '', $param2 = '', $param3 = ''){
        
        
		$data['languages'] = get_all_languages();
		$data['title'] = 'Languages';
		$data['view'] = 'admin/languages/languages';
		$this->load->view('admin/layout', $data);
	}

	// Language Functions
	public function edit_phrase($param1 = ''){
        
		$data['edit_profile'] = $param1;
		$data['title'] = 'Languages';
		$data['view'] = 'admin/languages/languages';
		$this->load->view('admin/layout', $data);
	}

	public function update_phrase() {
		$current_editing_language = sanitizer($this->input->post('currentEditingLanguage'));
		$updatedValue = sanitizer($this->input->post('updatedValue'));
		$key = sanitizer($this->input->post('key'));
		saveJSONFile($current_editing_language, $key, $updatedValue);
		echo $current_editing_language.' '.$key.' '.$updatedValue;
	}

	// Language Functions
	public function add_phrase(){
	    
		if (isset($_POST) && !empty($_POST)) 
		{
			$new_phrase = get_phrase(sanitizer($this->input->post('phrase')));
			$this->session->set_flashdata('success', $new_phrase.' has been added successfully.');
			redirect(base_url().'admin/languages/add_phrase');
        }
        
		$data['title'] = 'Languages';
		$data['view'] = 'admin/languages/languages';
		$this->load->view('admin/layout', $data);
	}

	// Language Functions
	public function add_language(){
	    
		if (isset($_POST) && !empty($_POST)) 
		{
			saveDefaultJSONFile(sanitizer($this->input->post('language')));
			$this->session->set_flashdata('success', 'Language Added Successfully');
			redirect(base_url().'admin/languages/add_language');
        }
        
		$data['title'] = 'Languages';
		$data['view'] = 'admin/languages/languages';
		$this->load->view('admin/layout', $data);
	}

	// Language Functions
	public function delete_language($param1 = ''){
        
        if (file_exists('application/language/'.$param1.'.json')) {
            unlink('application/language/'.$param1.'.json');
            $this->session->set_flashdata('success', get_phrase('language_deleted_successfully'));
            redirect(base_url().'admin/languages/list');
        }
	}
    
}

?>
