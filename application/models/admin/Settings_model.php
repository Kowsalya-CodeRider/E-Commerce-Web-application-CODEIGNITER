<?php
	class Settings_model extends CI_Model{


		public function get_settings(){
			$query = $this->db->get_where('settings', array('id' => 1));
			return $result = $query->row_array();
		}
		//--------------------------------------------------------------------
		public function update_settings($data){
			$id = 1;
			$this->db->where('id', $id);
			$this->db->update('settings', $data);
			return true;
		}

        //get payment settings
        public function get_payment_settings()
        {
            $this->db->where('id', 1);
            $query = $this->db->get('payment_settings');
            return $query->row();
        }
        
        


        //update payment gateway
        public function update_payment_gateway($name_key)
        {
            $gateway = $this->get_payment_gateway($name_key);
            if (!empty($gateway)) {
                $data = array(
                    'public_key' => trim($this->input->post('public_key', true)),
                    'secret_key' => trim($this->input->post('secret_key', true)),
                    'environment' => !empty($this->input->post('environment', true)) ? $this->input->post('environment', true) : 'production',
                    'locale' => !empty($this->input->post('locale', true)) ? $this->input->post('locale', true) : '',
                    'status' => !empty($this->input->post('status', true)) ? 1 : 0,
                    'base_currency' => trim($this->input->post('base_currency', true)),
                );
                
                return $this->db->where('name_key', clean_str($name_key))->update('payment_gateways', $data);
            }
            return false;
        }

        //get payment gateway
        public function get_payment_gateway($name_key)
        {
            return $this->db->where('name_key', clean_slug($name_key))->get('payment_gateways')->row();
        }

        //get active payment gateways
        public function get_active_payment_gateways()
        {
            return $this->db->where('status', 1)->get('payment_gateways')->result();
        }

        //update bank transfer settings
        public function update_bank_transfer_settings()
        {
            $data = array(
                'bank_transfer_enabled' => $this->input->post('bank_transfer_enabled', true),
                'bank_transfer_accounts' => $this->input->post('bank_transfer_accounts', false)
            );

            $this->db->where('id', 1);
            return $this->db->update('payment_settings', $data);
        }

        //update cash on delivery settings
        public function update_cash_on_delivery_settings()
        {
            $data = array(
                'cash_on_delivery_enabled' => $this->input->post('cash_on_delivery_enabled', true)
            );

            $this->db->where('id', 1);
            return $this->db->update('payment_settings', $data);
        }        

    }

?>