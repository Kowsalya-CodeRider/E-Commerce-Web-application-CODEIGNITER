<?php
	class Pages_model extends CI_Model{


		public function get_pages(){
            $this->db->order_by('created_at DESC');
            return $this->db->get('pages')->result();
		}

        //add
        public function add($data)
        {
            return $this->db->insert('pages', $data);
        }

        //get
        public function get($id)
        {
            $this->db->where('id', clean_number($id));
            return $this->db->get('pages')->row();
        }

        //get
        public function get_by_slug($slug)
        {
            $this->db->where('slug', $slug);
            return $this->db->get('pages')->row();
        }

        //update pages
        public function update($id, $data)
        {
            $id = clean_number($id);

            $this->db->where('id', $id);
            return $this->db->update('pages', $data);
        }

        //delete 
        public function delete($id)
        {
            $id = clean_number($id);
            $page = $this->get($id);
            if (!empty($page)) {
                $this->db->where('id', $id);
                return $this->db->delete('pages');
            }
            return false;
        }

    }

?>