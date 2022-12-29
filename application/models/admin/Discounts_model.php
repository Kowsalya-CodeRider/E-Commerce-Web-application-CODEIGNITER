<?php
	class Discounts_model extends CI_Model{


        //get
        public function get_discounts()
        {
            $this->db->order_by('created_at DESC');
            return $this->db->get('discounts')->result();
        }

        //get
        public function get($id)
        {
           $this->db->where('id', clean_number($id));
           return $this->db->get('discounts')->row();
        }

        //add
        public function add($data)
        {
            return $this->db->insert('discounts', $data);
        }

        //update
        public function update($id, $data)
        {
            $id = clean_number($id);

            $this->db->where('id', $id);
            return $this->db->update('discounts', $data);
        }

        //delete
        public function delete($id)
        {
            $id = clean_number($id);
            $user = $this->get($id);
            if (!empty($user)) {
                $this->db->where('id', $id);
                return $this->db->delete('discounts');
            }
            return false;
        }

    }

?>