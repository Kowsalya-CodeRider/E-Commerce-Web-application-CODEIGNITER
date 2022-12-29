<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Cart_model extends CI_Model{
        
    public function __construct()
    {
        parent::__construct();
        $this->cart_product_ids = array();
    }

    //set cart payment method option session
    public function set_sess_cart_payment_method()
    {
        $std = new stdClass();
        $std->payment_option = $this->input->post('payment_option', true);
        $this->session->set_userdata('mds_cart_payment_method', $std);
    }

    //get cart payment method option session
    public function get_sess_cart_payment_method()
    {
        if (!empty($this->session->userdata('mds_cart_payment_method'))) {
            return $this->session->userdata('mds_cart_payment_method');
        }
    }

}

?>