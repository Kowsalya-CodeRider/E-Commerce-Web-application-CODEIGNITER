<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Orders_model extends CI_Model{

    public function get_orders(){
        $this->db->where('userid', $this->user->userid);
        $this->db->order_by('created_at DESC');
        return $this->db->get('orders')->result();
    }

	//----------------------------------------------------------------------
	// Total Job Posted
	public function total_orders()
	{
		$this->db->where('userid', $this->user->userid);
		return $this->db->count_all_results('orders');
	}
	public function orders_completed()
	{
        
        $array = array('userid' => $this->user->userid, 'progress_status' => 2);
        $this->db->where($array);        
		return $this->db->count_all_results('orders');
	}
	public function orders_in_progress()
	{
        
        $array = array('userid' => $this->user->userid, 'progress_status' => 1);
        $this->db->where($array);        
		return $this->db->count_all_results('orders');
	}

}

?>