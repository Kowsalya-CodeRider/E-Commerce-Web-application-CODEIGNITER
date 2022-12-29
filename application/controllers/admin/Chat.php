<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Chat extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('admin/chat_model');
	}

	public function index()
	{
        
		$users = $this->chat_model->get_users();
        foreach ($users as $user) {
                $tempRow['id'] = $user->id;
                $tempRow['userid'] = $user->userid;
                $tempRow['email'] = $user->email;
                $tempRow['name'] = $user->name;
                $tempRow['imagelocation'] = $user->imagelocation;
               if(has_message('messages','from_id='.$user->userid)){
                $tempRow['unread'] = get_count('id','messages','from_id='.$user->userid);
                }else{
                 $tempRow['unread'] = 0;  
               }   
                $rows[] = $tempRow;
        }
        $data['users'] = $rows;
		$data['messages'] = $this->chat_model->get_messages();
		$data['title'] = 'Chat';
		$data['view'] = 'admin/chat/chat';
		$this->load->view('admin/layout', $data);
	}
    
	public function ajax_get_user_by_id($id='')
	{	
		$id = !empty($id)?$id:$this->input->post('id');
		if ($this->session->has_userdata('is_admin_login') && !empty($id) && is_numeric($id))
		{
            $user = $this->chat_model->get_user($id);
			if(!empty($user)){
				$tempRow['id'] = $user->id; 
				$tempRow['userid'] = $user->userid; 
				$tempRow['name'] = $user->name;
				$tempRow['email'] = $user->email;
				$tempRow['imagelocation'] = $user->imagelocation;
                
				$this->data['error'] = false;
				$this->data['data'] = $tempRow;
				$this->data['message'] = 'Successful';
				echo json_encode($this->data);
			}else{
				$this->data['error'] = true;
				$this->data['message'] = 'No user found.';
				echo json_encode($this->data);
			}
		}else{
			$this->data['error'] = true;
			$this->data['message'] = 'Access Denied';
			echo json_encode($this->data);
		}
	} 
	public function get_chat()
	{
		if ($this->session->has_userdata('is_admin_login'))
		{
			$this->form_validation->set_rules('opposite_user_id', 'Chat ID', 'trim|required|strip_tags|xss_clean|is_numeric');
			
			if($this->form_validation->run() == TRUE){

				$data = $this->chat_model->get_chat($this->session->userdata('admin_id'),$this->input->post('opposite_user_id'));

				foreach($data as $key => $task){
					$temp[$key] = $task;
					$temp[$key]['text'] = $task['message'];
					$temp[$key]['position'] = $this->session->userdata('admin_id') == $task['from_id']?'right':'left';
				}
				$Chat = $temp;

				$this->data['error'] = false;
				$this->data['data'] = $Chat;
				$this->data['message'] = 'Successful';
				echo json_encode($this->data);
			}else{
				$this->data['error'] = true;
				$this->data['message'] = validation_errors();
				echo json_encode($this->data);
			}
		}else{
			$this->data['error'] = true;
			$this->data['message'] = 'Access Denied';
			echo json_encode($this->data);
		}
	}    

	public function create()
	{
		if ($this->session->has_userdata('is_admin_login'))
		{
			$this->form_validation->set_rules('message', 'Message', 'trim|required|strip_tags|xss_clean');
			$this->form_validation->set_rules('to_id', 'Admin', 'trim|required|strip_tags|xss_clean');

			if($this->form_validation->run() == TRUE){
				$data = array(
					'type' => 'chat',
					'from_id' => $this->input->post('from_id'),
					'to_id' => $this->input->post('to_id'),	
					'message' => $this->input->post('message'),	
				);

				$Chat = $this->chat_model->create($data);

				$this->data['error'] = false;
				$this->data['data'] = $Chat;
				$this->data['message'] = 'Successful';
				echo json_encode($this->data);
			}else{
				$this->data['error'] = true;
				$this->data['message'] = validation_errors();
				echo json_encode($this->data); 
			}

		}else{
			
			$this->data['error'] = true;
			$this->data['message'] = 'Access Denied';
			echo json_encode($this->data); 
		}
		
	}    

}

?>	