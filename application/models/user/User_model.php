<?php
	class User_model extends CI_Model{


		public function get_user(){
			$id = $this->session->userdata('userid');
			//$query = $this->db->get_where('users', array('userid' => $id));
			//return $result = $query->row();
            $this->db->where('userid', clean_number($id));
            return $this->db->get('users')->row();
		}
		//--------------------------------------------------------------------
		public function update_user($data){
			$id = $this->session->userdata('userid');
			$this->db->where('userid', $id);
			$this->db->update('users', $data);
			return true;
		}
		//--------------------------------------------------------------------
		public function update_image($data){
			$id = $this->session->userdata('userid');
			$this->db->where('userid', $id);
			$this->db->update('users', $data);
			return true;
		}
		//--------------------------------------------------------------------
		public function change_password($data){
			$id = $this->session->userdata('userid');
			//$this->db->where('userid', $id);
			//$this->db->update('users', $data);
            $this->db->where('userid', $id);
            $this->db->update(users, array('password' => $data));
            //$this->db->query('update users SET password=$password where userid=$id');
			return true;
		}

    }

?>