<?php
	class Users_model extends CI_Model{


        //get
        public function get_users()
        {
            $this->db->order_by('created_at DESC');
            return $this->db->get('users')->result();
        }

        //get
        public function get($id)
        {
           $this->db->where('id', clean_number($id));
           return $this->db->get('users')->row();
        }

        //add
        public function add($data)
        {
            return $this->db->insert('users', $data);
        }

        //update
        public function update($id, $data)
        {
            $id = clean_number($id);

            $this->db->where('id', $id);
            return $this->db->update('users', $data);
        }

        //delete
        public function delete($id)
        {
            $id = clean_number($id);
            $user = $this->get($id);
            if (!empty($user)) {
                $this->db->where('id', $id);
                return $this->db->delete('users');
            }
            return false;
        }

    }

?>