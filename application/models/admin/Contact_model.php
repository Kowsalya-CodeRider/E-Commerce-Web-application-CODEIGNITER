<?php
	class Contact_model extends CI_Model{


		public function get_contacts(){
            $this->db->order_by('created_at DESC');
            return $this->db->get('contact_us')->result();
		}

    }

?>