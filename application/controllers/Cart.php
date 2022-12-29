<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cart extends Main_Controller {

	public function __construct()
	{
		parent::__construct();
		//$this->load->model('home_model');
		$this->load->model('admin/categories_model');
		$this->load->model('admin/services_model');
		$this->load->model('admin/currency_model');
		$this->load->model('admin/settings_model');
		$this->load->model('user/cart_model');
		$this->load->model('user/payment_model');
        $this->load->helper('text');
	}

	//-----------------------------------------------------------------------------
	// Index funciton will call bydefault
	public function cart()
	{	
        
		$data['services'] = $this->services_model->get_services();
		$data['categories'] = $this->categories_model->get_categories_home();
        $data['cart_contents'] = $this->cart->contents();
		$data['title'] = 'Cart';
		$data['view'] = 'cart/cart';
		$this->load->view('layout', $data);
	}

	public function payment_method()
	{	
		if(!$this->session->has_userdata('is_user_login')){

            $return = array(
                'last_request_page' => 'payment-method'
            );
            $this->session->set_userdata($return);
			$this->session->set_flashdata('warning', get_phrase('login_to_checkout'));
			redirect('auth/login');
        }    
        
        $data['cart_contents'] = $this->cart->contents();
		$data['title'] = 'Choose Payment Method';
		$data['view'] = 'cart/payment_method';
		$this->load->view('layout', $data);
	}

    /**
     * Payment Method Post
     */
    public function payment_method_post()
    {
        $this->cart_model->set_sess_cart_payment_method();

        $mds_payment_type = $this->input->post('mds_payment_type', true);
        redirect('payment');
    }

    

    /**
     * Payment
     */
    public function payment()
    {

        //check is set cart payment method
        $data['cart_payment_method'] = $this->cart_model->get_sess_cart_payment_method();
        if (empty($data['cart_payment_method'])) {
            redirect('payment-method');
        }
        
        $data['mds_payment_type'] = "sale";
        $data['cart_contents'] = $this->cart->contents();
		$data['title'] = 'Payment';
		$data['view'] = 'cart/payment';
		$this->load->view('layout', $data);
    }

    /**
     * Payment with Stripe
     */
    public function stripe_payment_post()
    {
        
        $stripe = get_payment_gateway('stripe');
        if (empty($stripe)) {
            $this->session->set_flashdata('error', "Payment method not found!");
            echo json_encode([
                'result' => 0
            ]);
            exit();
        }
        $payment_session = $this->session->userdata('mds_payment_cart_data');
        if (empty($payment_session)) {
            $this->session->set_flashdata('error', get_phrase("invalid_attempt"));
            echo json_encode([
                'result' => 0
            ]);
            exit();
        }

        $paymentObject = $this->input->post('paymentObject', true);
        if (!empty($paymentObject)) {
            $paymentObject = json_decode($paymentObject);
        }
        $clientSecret = $this->session->userdata('mds_stripe_client_secret');

        if (!empty($paymentObject) && $paymentObject->client_secret == $clientSecret) {
            $data_transaction = array(
                'payment_method' => $stripe->name,
                'payment_id' => $paymentObject->id,
                'currency' => strtoupper($paymentObject->currency),
                'payment_amount' => get_price($paymentObject->amount, 'decimal'),
                'payment_status' => "Succeeded",
                'userid' => $this->user->userid
            );
            //add transaction
            $sale = $this->payment_model->add_payment_transaction($data_transaction);
				
            if ($sale){
                $this->session->set_flashdata('success', 'Paid Successfully');
                echo json_encode([
                    'result' => 1,
                    'redirect_url' => 'user/orders'
                ]);
            }else{
                $this->session->set_flashdata('warning', 'Error when saving Data');
                echo json_encode([
                    'result' => 0
                ]);
            }
            
        } else {
            $this->session->set_flashdata('error', trans("msg_error"));
            echo json_encode([
                'result' => 0
            ]);
        }
        @$this->session->unset_userdata('mds_stripe_client_secret');
    }
    
    /**
     * Payment with Paypal
     */
    public function paypal_payment_post()
    {
        $payment_id = $this->input->post('payment_id', true);
        $this->load->library('paypal');

        //validate the order
        if ($this->paypal->get_order($payment_id)) {
            $data_transaction = array(
                'payment_method' => "PayPal",
                'payment_id' => $payment_id,
                'currency' => $this->input->post('currency', true),
                'payment_amount' => $this->input->post('payment_amount', true),
                'payment_status' => $this->input->post('payment_status', true),
                'userid' => $this->user->userid
            );
            
            
            //add transaction
            $sale = $this->payment_model->add_payment_transaction($data_transaction);
				
            if ($sale){
                $this->session->set_flashdata('success', 'Paid Successfully');
                echo json_encode([
                    'result' => 1,
                    'redirect_url' => 'user/orders'
                ]);
            }else{
                $this->session->set_flashdata('warning', 'Error when saving Data');
                echo json_encode([
                    'result' => 0
                ]);
            }

        } else {
            $this->session->set_flashdata('error', trans("msg_error"));
            echo json_encode([
                'result' => 0
            ]);
        }
    }

    /**
     * Payment with PayStack
     */
    public function paystack_payment_post()
    {
        $this->load->library('paystack');

        $data_transaction = array(
            'payment_method' => "PayStack",
            'payment_id' => $this->input->post('payment_id', true),
            'currency' => $this->input->post('currency', true),
            'payment_amount' => get_price($this->input->post('payment_amount', true), 'decimal'),
            'payment_status' => $this->input->post('payment_status', true),
            'userid' => $this->user->userid
        );

        if (empty($this->paystack->verify_transaction($data_transaction['payment_id']))) {
            $this->session->set_flashdata('error', 'Invalid transaction code!');
            echo json_encode([
                'result' => 0
            ]);
        } else {
            
            //add transaction
            $sale = $this->payment_model->add_payment_transaction($data_transaction);
				
            if ($sale){
                $this->session->set_flashdata('success', 'Paid Successfully');
                echo json_encode([
                    'result' => 1,
                    'redirect_url' => 'user/orders'
                ]);
            }else{
                $this->session->set_flashdata('warning', 'Error when saving Data');
                echo json_encode([
                    'result' => 0
                ]);
            }
        }
    }

    /**
     * Payment with Razorpay
     */
    public function razorpay_payment_post()
    {
        $this->load->library('razorpay');

        $data_transaction = array(
            'payment_method' => "Razorpay",
            'payment_id' => $this->input->post('payment_id', true),
            'razorpay_order_id' => $this->input->post('razorpay_order_id', true),
            'razorpay_signature' => $this->input->post('razorpay_signature', true),
            'currency' => $this->input->post('currency', true),
            'payment_amount' => get_price($this->input->post('payment_amount', true), 'decimal'),
            'payment_status' => 'Succeeded',
        );

        if (empty($this->razorpay->verify_payment_signature($data_transaction))) {
            $this->session->set_flashdata('error', 'Invalid signature passed!');
            echo json_encode([
                'result' => 0
            ]);
        } else {

            $data = array(
                'payment_method' => "Razorpay",
                'payment_id' => $this->input->post('payment_id', true),
                'currency' => $this->input->post('currency', true),
                'payment_amount' => get_price($this->input->post('payment_amount', true), 'decimal'),
                'payment_status' => 'Succeeded',
                'userid' => $this->user->userid
            );
            
            //add transaction
            $sale = $this->payment_model->add_payment_transaction($data);
				
            if ($sale){
                $this->session->set_flashdata('success', 'Paid Successfully');
                echo json_encode([
                    'result' => 1,
                    'redirect_url' => 'user/orders'
                ]);
            }else{
                $this->session->set_flashdata('warning', 'Error when saving Data');
                echo json_encode([
                    'result' => 0
                ]);
            }
        }
    }


    /**
     * Execute Sale Payment
     */
    public function execute_payment($data_transaction, $payment_type, $base_url)
    {
        //response object
        $response = new stdClass();
        $response->result = 0;
        $response->message = "";
        $response->redirect_url = "";
        $data_transaction["payment_status"] = "payment_received";
        if ($payment_type == 'sale') {
            //add order
            $order_id = $this->payment_model->add_payment($data_transaction);
            $order = $this->order_model->get_order($order_id);
            if (!empty($order)) {
                //decrease product quantity after sale
                $this->order_model->decrease_product_stock_after_sale($order->id);
                //send email
                if ($this->general_settings->send_email_buyer_purchase == 1) {
                    $email_data = array(
                        'email_type' => 'new_order',
                        'order_id' => $order_id
                    );
                    $this->session->set_userdata('mds_send_email_data', json_encode($email_data));
                }
                //set response and redirect URLs
                $response->result = 1;
                $response->redirect_url = $base_url . get_route("order_details", true) . $order->order_number;
                if ($order->buyer_id == 0) {
                    $this->session->set_userdata('mds_show_order_completed_page', 1);
                    $response->redirect_url = $base_url . get_route("order_completed", true) . $order->order_number;
                } else {
                    $response->message = trans("msg_order_completed");
                }
            } else {
                //could not added to the database
                $response->message = trans("msg_payment_database_error");
                $response->result = 0;
                $response->redirect_url = $base_url . get_route("cart", true) . get_route("payment");
            }
        } 
        //reset session for the payment
        @$this->session->unset_userdata('mds_payment_cart_data');
        //return response
        return $response;
    }

	public function setpromocode(){
		$promocode = $this->input->post('promocode');
		$result = $this->cart->setpromocode($promocode);
		echo $result;die;
	}


}// endClass