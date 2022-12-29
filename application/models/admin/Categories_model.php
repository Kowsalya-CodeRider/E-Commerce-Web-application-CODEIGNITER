<?php
	class Categories_model extends CI_Model{


		public function get_categories(){
            $this->db->order_by('id DESC');
            return $this->db->get('categories')->result();
		}

		public function get_categories_home(){


            $this->db->select('*, COUNT(services.category_id) as categories_count');
            $this->db->from('categories');
            $this->db->join('services', 'categories.id = services.category_id', 'right');
            //$this->db->where(array('services.category_id !=' => null, 'categories.id != ' => NULL));
            $this->db->group_by('categories.id');
            $query = $this->db->get();
            return $query->result();            
            
		}

        //add
        public function add($data)
        {
            return $this->db->insert('categories', $data);
        }

        //get
        public function get($id)
        {
            $this->db->where('id', clean_number($id));
            return $this->db->get('categories')->row();
        }

        //update pages
        public function update($id, $data)
        {
            $id = clean_number($id);

            $this->db->where('id', $id);
            return $this->db->update('categories', $data);
        }

        //delete 
        public function delete($id)
        {
            $id = clean_number($id);
            $page = $this->get($id);
            if (!empty($page)) {
                $this->db->where('id', $id);
                return $this->db->delete('categories');
            }
            return false;
        }
		
		public function get_subcategories_home_1($category_id){
            
            
            $this->db->select('*, COUNT(subcategories.id) as subcategories_count');
            $this->db->from('subcategories');
			$this->db->where('subcategories.category_id', $category_id);
            $this->db->group_by('subcategories.id');
            $query = $this->db->get();
            return $query->result();            
            
		}

    }

?>