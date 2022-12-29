<?php
	class Currency_model extends CI_Model{


        //get currencies
        public function get_currencies()
        {
            $this->db->order_by('status DESC, id');
            return $this->db->get('currencies')->result();
        }

        //get currency
        public function get_currency($id)
        {
            $this->db->where('id', clean_number($id));
            return $this->db->get('currencies')->row();
        }

        //get currency
        public function get_symbol($code)
        {
            $this->db->where('code', $code);
            return $this->db->get('currencies')->row();
        }

        //add currency
        public function add_currency($data)
        {
            return $this->db->insert('currencies', $data);
        }

        //update currency
        public function update_default_currency($data)
        {

            $this->db->where('id', 1);
            return $this->db->update('payment_settings', $data);
        }

        //update currency
        public function update_currency($id, $data)
        {
            $id = clean_number($id);

            $this->db->where('id', $id);
            return $this->db->update('currencies', $data);
        }

        //delete currency
        public function delete_currency($id)
        {
            $id = clean_number($id);
            $currency = $this->get_currency($id);
            if (!empty($currency)) {
                $this->db->where('id', $id);
                return $this->db->delete('currencies');
            }
            return false;
        }

    }

?>