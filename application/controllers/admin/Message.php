<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Message extends MY_Controller {
 	public function __construct()
        {
                parent::__construct();
				$this->load->model('admin/message_model');
				$this->load->model('admin/admin_model');
		        $this->load->model('admin/users_model');
				$this->load->helper('string');
        }
	public function index(){
		
        $data['strTitle']='All Users';
        $data['strsubTitle']='Users';
        $data['chatTitle']='Select Users with Chat';
        
		$users = $this->chat_model->get_users();
        foreach ($users as $user) {
                $tempRow['id'] = $user->id;
                $tempRow['userid'] = $user->userid;
                $tempRow['email'] = $user->email;
                $tempRow['name'] = $user->name;
                $tempRow['imagelocation'] = $user->imagelocation;
               if(has_message('chat','sender_id='.$user->id)){
                $tempRow['unread'] = get_count('id','chat','sender_id='.$user->id);
                }else{
                 $tempRow['unread'] = 0;  
               }   
                $rows[] = $tempRow;
        }
        $data['users'] = $rows;
		$data['title'] = 'Chat';
		$data['view'] = 'admin/chat/template';
		$this->load->view('admin/layout', $data);
        
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
 					'sender_id' => $this->session->userdata('admin_id'),
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
		
		$Logged_sender_id = $this->session->userdata('admin_id');
		$history = $this->message_model->GetReciverChatHistory($receiver_id);
		//print_r($history);
		foreach($history as $chat):
			
			$message_id = $chat['id'];
			$sender_id = $chat['sender_id'];
        
	        $user = $this->users_model->get($sender_id);
	        $admin = $this->admin_model->get_admin();
			
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
                       <span><?=$user->name;?> - <?=$messagedatetime;?></span>
                      </div><!--/ convo-area -->
                      <div class="convo-img">
                       <img src="<?= base_url(); ?>public/uploads/users/<?=$user->imagelocation;?>" alt="<?=$user->name;?>" class="img-fluid rounded">
                      </div><!--/ convo-img -->
                     </div><!--/ convo-box -->

			<?php }else{?>

                     <div class="convo-box pull-right">
                      <div class="convo-area pull-right">
                       <div class="convo-message">
                        <p><?=$messageBody;?></p>
                       </div><!--/ convo-message-->
                       <span><?=$admin['name'];?> - <?=$messagedatetime;?></span>
                      </div><!--/ convo-area -->
                      <div class="convo-img">
                       <img src="<?= base_url(); ?>public/uploads/admin/<?=$admin['imagelocation'];?>" alt="<?=$admin['name'];?>" class="img-fluid rounded">
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