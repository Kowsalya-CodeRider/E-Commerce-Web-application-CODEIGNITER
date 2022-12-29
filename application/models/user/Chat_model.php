<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Chat_model extends CI_Model
{ 
    public function __construct()
	{
		parent::__construct();
    }
    
    function get_chat($from_id, $to_id){

        $this->db->where('from_id', $to_id);
        //$this->db->where(array('services.category_id !=' => null, 'categories.id != ' => NULL));
        $this->db->update('messages', array('read_status' => 1));
        
        $query = $this->db->query("SELECT * FROM messages WHERE type='chat' AND ((from_id=$from_id AND to_id=$to_id) OR (from_id=$to_id AND to_id=$from_id))");
        return $query->result_array();
    }

    function create($data){
        if($this->db->insert('messages', $data)){
            
            $id = $this->db->insert_id();
            //$q = $this->db->get_where('messages', array('id' => $id));
            //return $q->row();
            
           $query = $this->db->get_where('messages' , array('id' => $id));
           $result = $query->row_array();
            
            return $result['message'];
            
        }else{
            return false;
        }    
    }

	//----------------------------------------------------------------------
	// Unread
	public function unread()
	{
		$this->db->where(array('to_id' => $this->user->userid, 'read_status' => 0));
		return $this->db->count_all_results('messages');
	}

}