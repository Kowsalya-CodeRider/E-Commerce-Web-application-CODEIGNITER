<?php
	class Transactions_model extends CI_Model{


		public function get_transactions(){
            $this->db->order_by('created_at DESC');
            return $this->db->get('transactions')->result();
		}

    }

?>