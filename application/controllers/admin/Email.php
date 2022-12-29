<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Email extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('admin/email_model');
	}

	public function index()
	{
		if($this->input->post()){
			$this->form_validation->set_rules('subject', 'Email Subject', 'trim|required');
			$this->form_validation->set_rules('content', 'Email Body', 'trim|required');
			if ($this->form_validation->run() == FALSE) {
				echo validation_errors();
			}
			else{

				$id = $this->input->post('id');
				
				$data = array(
					'subject' => $this->input->post('subject'),
					'body' => $this->input->post('content'),
					'last_update' => date('Y-m-d H:i:s'),
				);
				$data = $this->security->xss_clean($data);
				$result = $this->email_model->update_email_template($data, $id);
				if($result){
					echo "true";
				}
			}
		}
        
			$data['title'] = 'Email Templates';
			$data['templates'] = $this->email_model->get_email_templates();
			$data['view'] = 'admin/email_templates/list';
			$this->load->view('admin/layout',$data);
	}

	// ------------------------------------------------------------
	// Get Email Template & Related variables via Ajax by ID
	public function get_email_template_content_by_id()
	{
		$id = $this->input->post('template_id');

		$data['template'] = $this->email_model->get_email_template_content_by_id($id);
		
		$variables = $this->email_model->get_email_template_variables_by_id($id);

		$data['variables'] = implode(',',array_column($variables, 'variable_name'));

		echo json_encode($data);
	}

	//---------------------------------------------------------------
    //
    public function email_preview()
    {
        if($this->input->post('content'))
        {
            $data['content'] = $this->input->post('content');
            $data['head'] = $this->input->post('head');
            $data['title'] = 'Send Email to Subscribers';
            echo $this->load->view('admin/email_templates/preview', $data,true);
        }
    }

}

?>	