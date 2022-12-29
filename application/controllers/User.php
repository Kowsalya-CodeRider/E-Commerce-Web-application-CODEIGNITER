<?php defined('BASEPATH') OR exit('No direct script access allowed');

class User extends Main_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('user/user_model');
		$this->load->model('user/payment_model');
		$this->load->model('user/orders_model');
		$this->load->model('user/admins_model');
		$this->load->model('user/chat_model');
		$this->load->model('user/message_model');
		$this->load->model('admin/services_model');
        

        if(!$this->session->has_userdata('is_user_login'))
        {
            redirect('auth/login');
        }
	}

	public function dashboard()
	{
        
		$data['title'] = 'Dashboard';
		$data['page_name'] = 'dashboard';
		$data['view'] = 'user/dashboard';
		$this->load->view('layout', $data);
	}

	public function chat()
	{
        
		$data['admins'] = $this->admins_model->get_admins();
		$data['title'] = 'Chat';
		$data['view'] = 'user/chat';
		$this->load->view('layout', $data);
	}
    
	public function ajax_get_user_by_id($id='')
	{	
		$id = !empty($id)?$id:$this->input->post('id');
		if ($this->session->has_userdata('is_user_login') && !empty($id) && is_numeric($id))
		{
            $admin = $this->admins_model->get($id);
			if(!empty($admin)){
				$tempRow['id'] = $admin->id; 
				$tempRow['name'] = $admin->name;
				$tempRow['email'] = $admin->email;
				$tempRow['imagelocation'] = $admin->imagelocation;
                
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
		if ($this->session->has_userdata('is_user_login'))
		{
			$this->form_validation->set_rules('opposite_user_id', 'Chat ID', 'trim|required|strip_tags|xss_clean|is_numeric');
			
			if($this->form_validation->run() == TRUE){

				$data = $this->chat_model->get_chat($this->session->userdata('userid'),$this->input->post('opposite_user_id'));

				foreach($data as $key => $task){
					$temp[$key] = $task;
					$temp[$key]['text'] = $task['message'];
					$temp[$key]['position'] = $this->session->userdata('userid') == $task['from_id']?'right':'left';
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
		if ($this->session->has_userdata('is_user_login'))
		{
			$this->form_validation->set_rules('message', 'Message', 'trim|required|strip_tags|xss_clean');
			$this->form_validation->set_rules('to_id', 'Admin', 'trim|required|strip_tags|xss_clean');

			if($this->form_validation->run() == TRUE){
				$data = array(
					'type' => 'chat',
					'from_id' => $this->input->post('userid'),
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

	public function settings()
	{
	    
		if ($this->input->post('details')) 
		{

	        //validate inputs
			$this->form_validation->set_rules('name','name','trim|required|min_length[3]');
			$this->form_validation->set_rules('email','email','trim|required|min_length[5]|valid_email');
            
			if ($this->form_validation->run() == FALSE) 
			{
				$data = array(
					'errors' => validation_errors(), 
				);
				$this->session->set_flashdata('validation_errors', $data['errors']);
				redirect(base_url('user/settings'));
			}else{
                
                $data = array(
                    'name' => $this->input->post('name'),
                    'email' => $this->input->post('email'),
                    'updated_at' => date('Y-m-d : h:m:s'),
                );
                $data = $this->security->xss_clean($data);
                $result = $this->user_model->update_user($data);

				if ($result){
				    $this->session->set_flashdata('success', get_phrase('updated_successfully'));
					redirect(base_url('user/settings'));
				}else{
                    $this->session->set_flashdata('warning', get_phrase('error_when_saving_data'));
					redirect(base_url('user/settings'));
                }
			}
		}
	    
		if ($this->input->post('image')) 
		{

            $old_image = $this->input->post('old_image');
            $path="public/uploads/users/";

            if(!empty($_FILES['image']['name']))
            {
                if($old_image != 'default.png'){
                   $this->functions->delete_file($path, $old_image);
                }

                $result = $this->functions->file_insert($path, 'image', 'image', '9097152');
                if($result['status'] == 1){
                    $data['imagelocation'] = $result['msg'];
                    $data = $this->security->xss_clean($data);
                    $result = $this->user_model->update_image($data);

                    if ($result){
                        $this->session->set_flashdata('success', get_phrase('updated_successfully'));
                        redirect(base_url('user/settings'));
                    }else{
                        $this->session->set_flashdata('warning', get_phrase('error_when_saving_data'));
                        redirect(base_url('user/settings'));
                    }
                }
                else{
                    $this->session->set_flashdata('error', $result['msg']);
                    redirect(base_url('user/settings'));
                }
            }else{
                
				$data = array(
					'errors' => get_phrase('image_not_selected'), 
				);
				$this->session->set_flashdata('validation_errors', $data['errors']);
				redirect(base_url('user/settings'));
            }
		}
        
		if($this->input->post('pass')){
            
			$this->form_validation->set_rules('password_current', 'Password Current', 'trim|required');
			$this->form_validation->set_rules('password', 'Password', 'trim|required');
			$this->form_validation->set_rules('confirm_password', 'Confirm Password', 'trim|required|matches[password]');
            
			if ($this->form_validation->run() == FALSE) {
				$data = array(
					'errors' => validation_errors(), 
				);
				$this->session->set_flashdata('validation_errors', $data['errors']);
				redirect(base_url('user/settings'));
			}
			else{

				if (password_verify($this->input->post('password_current'), $this->user->password)) {
                        $password = password_hash($this->input->post('password'), PASSWORD_DEFAULT);
                        $data = $this->security->xss_clean($password);
                        $result = $this->user_model->change_password($data);
                        if($result){
                            $this->session->set_flashdata('success', get_phrase('password_changed_successfully'));
                            redirect(base_url('user/settings'));
                        }else{
                            $this->session->set_flashdata('warning', get_phrase('error_when_saving_data'));
                            redirect(base_url('user/settings'));
                        }
					
				} else {
                            $this->session->set_flashdata('warning', get_phrase('current_password_does_not_match'));
                            redirect(base_url('user/settings'));
				 
				}
			}
		}
        
		$data['title'] = 'Settings';
		$data['page_name'] = 'settings';
		$data['view'] = 'user/settings';
		$this->load->view('layout', $data);
	}

	public function payments()
	{
        
		$data['payments'] = $this->payment_model->get_payments();
		$data['total_orders'] = $this->orders_model->total_orders();
		$data['services'] = $this->services_model->get_services();
		$data['title'] = 'Payments';
		$data['page_name'] = 'payments';
		$data['view'] = 'user/payments';
		$this->load->view('layout', $data);
	}

	public function orders()
	{
        
		$data['orders'] = $this->orders_model->get_orders();
		$data['total_orders'] = $this->orders_model->total_orders();
		$data['services'] = $this->services_model->get_services();
		$data['title'] = 'Orders';
		$data['page_name'] = 'orders';
		$data['view'] = 'user/orders';
		$this->load->view('layout', $data);
	}
    
    
    
    
	public function message(){
		
        $data['strTitle']='All Users';
        $data['strsubTitle']='Users';
        $data['chatTitle']='Select Admin with Chat';
        
		$data['admins'] = $this->admins_model->get_admins();
		$data['title'] = 'Chat';
		$data['view'] = 'user/message';
		$this->load->view('layout', $data);
        
 		//$this->parser->parse('chat/template',$data);
    }
	
	
	public function send_text_message(){
		$post = $this->input->post();
		$messageTxt='NULL';
		$attachment_name='';
		$file_ext='';
		$mime_type='';
		
		if(isset($post['type'])=='Attachment'){ 
		 	$AttachmentData = $this->ChatAttachmentUpload();
			//print_r($AttachmentData);
			$attachment_name = $AttachmentData['file_name'];
			$file_ext = $AttachmentData['file_ext'];
			$mime_type = $AttachmentData['file_type'];
			 
		}else{
			$messageTxt = reduce_multiples($post['messageTxt'],' ');
		}	
		 
				$data=[
 					'sender_id' => $this->session->userdata('id'),
					'receiver_id' => $post['receiver_id'],
					'message' =>   $messageTxt,
					'attachment_name' => $attachment_name,
					'file_ext' => $file_ext,
					'mime_type' => $mime_type,
					'message_date_time' => date('Y-m-d H:i:s'), //23 Jan 2:05 pm
					'ip_address' => $this->input->ip_address(),
				];
		  
                $data = $this->security->xss_clean($data);
 				$query = $this->message_model->SendTxtMessage($data); 
 				$response='';
				if($query == true){
					$response = ['status' => 1 ,'message' => '' ];
				}else{
					$response = ['status' => 0 ,'message' => 'sorry we re having some technical problems. please try again !' 						];
				}
             
 		   echo json_encode($response);
	}
	public function ChatAttachmentUpload(){
		 
		
		$file_data='';
		if(isset($_FILES['attachmentfile']['name']) && !empty($_FILES['attachmentfile']['name'])){	
				$config['upload_path']          = './public/uploads/attachments';
				$config['allowed_types']        = 'jpeg|jpg|png|txt|pdf|docx|xlsx|pptx|rtf|zip|rar|7zip|apk|aab|mp4|mp3|mkv|mov';
				//$config['max_size']             = 500;
				//$config['max_width']            = 1024;
				//$config['max_height']           = 768;
				$this->load->library('upload', $config);
				if ( ! $this->upload->do_upload('attachmentfile'))
				{
					echo json_encode(['status' => 0,
					'message' => '<span style="color:#900;">'.$this->upload->display_errors(). '<span>' ]); die;
				}
				else
				{
					$file_data = $this->upload->data();
					//$filePath = $file_data['file_name'];
					return $file_data;
				}
		    }
 		 
	}
	
	public function get_chat_history_by_vendor(){
		$receiver_id = $this->input->get('receiver_id');
		
		$Logged_sender_id = $this->session->userdata('id');
		$history = $this->message_model->GetReciverChatHistory($receiver_id);
		//print_r($history);
		foreach($history as $chat):
			
			$message_id = $chat['id'];
			$sender_id = $chat['sender_id'];
        
	        $user = $this->user_model->get_user();
	        $admin = $this->admin_model->admin_get($sender_id);
			
			$message = $chat['message'];
			$messagedatetime = date('d M H:i A',strtotime($chat['message_date_time']));
			
 		?>
        	<?php
				$messageBody='';
            	if($message=='NULL'){ //fetach media objects like images,pdf,documents etc
					$classBtn = 'right';
					if($Logged_sender_id==$sender_id){$classBtn = 'left';}
					
					$attachment_name = $chat['attachment_name'];
					$file_ext = $chat['file_ext'];
					$mime_type = explode('/',$chat['mime_type']);
					
					$document_url = base_url('public/uploads/attachments/'.$attachment_name);
					
				  if($mime_type[0]=='image'){
 					$messageBody.='<img src="'.$document_url.'" onClick="ViewAttachmentImage('."'".$document_url."'".','."'".$attachment_name."'".');" class="img-fluid">';	
				  }else{
					$messageBody='';
                           $messageBody.='<span>Attachments: ';
                            $messageBody.= $attachment_name;
                          $messageBody.='</span>';
                            $messageBody.='<a download href="'.$document_url.'"><button type="button" id="'.$message_id.'" class="btn btn-primary btn-sm btn-flat btnFileOpen">Open</button></a>';
					}
						
											
				}else{
					$messageBody = $message;
				}
			?>
            
            
        
             <?php if($Logged_sender_id != $sender_id){?>     

                     <div class="convo-box convo-left">
                      <div class="convo-area convo-left">
                       <div class="convo-message">
                        <p><?=$messageBody;?></p>
                       </div><!--/ convo-message-->
                       <span><?=$admin['name'];?> - <?=$messagedatetime;?></span>
                      </div><!--/ convo-area -->
                      <div class="convo-img">
                       <img src="<?= base_url(); ?>public/uploads/admin/<?=$admin['imagelocation'];?>" alt="<?=$admin['name'];?>" class="img-fluid rounded">
                      </div><!--/ convo-img -->
                     </div><!--/ convo-box -->

			<?php }else{?>


                     <div class="convo-box pull-right">
                      <div class="convo-area pull-right">
                       <div class="convo-message">
                        <p><?=$messageBody;?></p>
                       </div><!--/ convo-message-->
                       <span><?=$user->name;?> - <?=$messagedatetime;?></span>
                      </div><!--/ convo-area -->
                      <div class="convo-img">
                       <img src="<?= base_url(); ?>public/uploads/users/<?=$user->imagelocation;?>" alt="<?=$user->name;?>" class="img-fluid rounded">
                      </div><!--/ convo-img -->
                     </div><!--/ convo-box -->

             <?php }?>
        
        <?php
		endforeach;
 		
	}
	public function chat_clear_client_cs(){
		$receiver_id = $this->OuthModel->Encryptor('decrypt', $this->input->get('receiver_id') );
		
		$messagelist = $this->ChatModel->GetReciverMessageList($receiver_id);
		
		foreach($messagelist as $row){
			
			if($row['message']=='NULL'){
				$attachment_name = unlink('uploads/attachments/'.$row['attachment_name']);
			}
 		}
		
		$this->ChatModel->TrashById($receiver_id); 
 
 		
	}    

}

?>	