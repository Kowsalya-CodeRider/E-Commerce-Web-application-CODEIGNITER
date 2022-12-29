<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Payment_model extends CI_Model{
        
    public function __construct()
    {
        parent::__construct();
        $this->cart_product_ids = array();
    }
        
    
    //add payment transaction
    public function add_payment_transaction($data_transaction)
    {
      
      $cart = $this->cart->contents();
	  foreach($cart as $item){
        $data = array(
            'payment_method' => $data_transaction["payment_method"],
            'payment_id' => $data_transaction["payment_id"],
            'userid' => $data_transaction["userid"],
            'service_id' => $item['id'],
            'currency' => $data_transaction["currency"],
            'payment_amount' => $item['discounted_total'],
            'payment_status' => $data_transaction["payment_status"],
			'discount' => $item['discount'],
			'original_amount' => $item['price'],
            'created_at' => date('Y-m-d H:i:s')
        );
        $this->db->insert('transactions', $data);
        $payment_id = $this->db->insert_id();
          
        $data_orders = array(
            'userid' => $data_transaction["userid"],
            'payment_id' => $payment_id,
            'service_id' => $item['id'],
            'progress_status' => 1,
			'user_message' => $item['user_message'],
			'discount_id'  => $this->session->userdata('discount_id'),
            'created_at' => date('Y-m-d H:i:s')
        );
        $this->db->insert('orders', $data_orders);
      }
        
      $this->cart->destroy();    
        
      return true;    
          
    }

    //get cart payment method option session
    public function get_sess_cart_payment_method()
    {
        if (!empty($this->session->userdata('mds_cart_payment_method'))) {
            return $this->session->userdata('mds_cart_payment_method');
        }
    }

    public function get_payments(){
        
		$this->db->where('userid', $this->user->userid);
        $this->db->order_by('created_at DESC');
        return $this->db->get('transactions')->result();
    }

}

?>