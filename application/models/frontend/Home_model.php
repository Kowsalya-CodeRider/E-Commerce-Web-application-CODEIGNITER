<?php
	class Home_model extends CI_Model{


        public function contact($data)
        {
            $this->db->insert('contact_us',$data);
            return true;
        }

    }

?>