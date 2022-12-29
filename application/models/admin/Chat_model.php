<?php
	class Chat_model extends CI_Model{


        //get
		public function get_users(){
            $this->db->order_by('created_at ASC');
            return $this->db->get('users')->result();
		}
		public function get_messages(){
            $this->db->order_by('created DESC');
            return $this->db->get('messages')->result();
		}
		public function get_new_messages(){
            $this->db->where(array('to_id' => $this->admin['id']));
            $this->db->limit(3);
            $this->db->order_by('created DESC');
            return $this->db->get('messages')->result();
		}
        /*
        public function get_users()
        {
            
            $this->db->select('*, COUNT(messages.from_id) as messages_count');
            $this->db->from('users');
            $this->db->join('messages', 'users.userid = messages.from_id', 'right');
            //$this->db->where(array('services.category_id !=' => null, 'categories.id != ' => NULL));
            //$this->db->order_by('messages.created DESC');
            $this->db->group_by('users.id');
            $query = $this->db->get();
            return $query->result();   
        }
        /*
        public function get_users()
        {
            $this->db->select('city as name, COUNT(city) as total_jobs');
            $this->db->from('xx_job_post');
            $this->db->group_by('city');
            $query = $this->db->get();
            return $query->result_array();
        }
        */

        //get
        public function get_user($id)
        {
           $this->db->where('id', clean_number($id));
           return $this->db->get('users')->row();
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
            $this->db->where(array('to_id' => $this->admin['id'], 'read_status' => 0));
            return $this->db->count_all_results('messages');
        }

    }

?>