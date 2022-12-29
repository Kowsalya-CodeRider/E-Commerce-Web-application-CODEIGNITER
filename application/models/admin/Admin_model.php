<?php
	class Admin_model extends CI_Model{


		public function get_admin(){
			$id = $this->session->userdata('admin_id');
			$query = $this->db->get_where('admin', array('id' => $id));
			return $result = $query->row_array();
		}

		public function admin_get($id){
			$query = $this->db->get_where('admin', array('id' => $id));
			return $result = $query->row_array();
		}
		//--------------------------------------------------------------------
		public function update_admin($data){
			$id = $this->session->userdata('admin_id');
			$this->db->where('id', $id);
			$this->db->update('admin', $data);
			return true;
		}
		//--------------------------------------------------------------------
		public function update_image($data){
			$id = $this->session->userdata('admin_id');
			$this->db->where('id', $id);
			$this->db->update('admin', $data);
			return true;
		}
		//--------------------------------------------------------------------
		public function change_password($data){
			$id = $this->session->userdata('admin_id');
			$this->db->where('id', $id);
			$this->db->update('admin', $data);
			return true;
		}

        public function total_orders()
        {
            return $this->db->count_all_results('orders');
        }

        public function total_users()
        {
            return $this->db->count_all_results('users');
        }

        public function total_services()
        {
            return $this->db->count_all_results('services');
        }

        public function total_revenue()
        {
              $query =  $this->db->query("SELECT SUM(`payment_amount`) as amount FROM transactions"); 
              return $query->result();
        }
        

     public function chart() {
   
      $query =  $this->db->query("SELECT SUM(`payment_amount`) as amount,MONTHNAME(created_at) as month_name FROM transactions WHERE YEAR(created_at) = '" . date('Y') . "'
      GROUP BY YEAR(created_at),MONTH(created_at)"); 
 
      $record = $query->result();
      $data = [];
 
      foreach($record as $row) {
            $data['label'][] = $row->month_name;
            $data['data'][] = $row->amount;
      }
       return json_encode($data);
      //$this->load->view('bar_chart',$data);
    }   
     public function user_chart() {
   
      $query =  $this->db->query("SELECT COUNT(id) as count,MONTHNAME(created_at) as month_name FROM users WHERE YEAR(created_at) = '" . date('Y') . "'
      GROUP BY YEAR(created_at),MONTH(created_at)"); 
 
      $record = $query->result();
      $data = [];
 
      foreach($record as $row) {
            $data['label'][] = $row->month_name;
            $data['data'][] = (int) $row->count;
      }
       return json_encode($data);
    }     

    }

?>