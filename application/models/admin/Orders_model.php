<?php
	class Orders_model extends CI_Model{


		public function get_orders(){
            $this->db->order_by('created_at DESC');
            return $this->db->get('orders')->result();
		}

        //completed 
        public function completed($id)
        {
            $id = clean_number($id);
            $data = array(
                'progress_status' => 2,
            );
            $this->db->where('id', $id);
            return $this->db->update('orders', $data);
        }

        //not_completed 
        public function not_completed($id)
        {
            $id = clean_number($id);
            $data = array(
                'progress_status' => 1,
            );
            $this->db->where('id', $id);
            return $this->db->update('orders', $data);
        }

    }

?>