<?php
class Dropdownmenu_model extends CI_Model{


    public function get_dropdownmenu(){
        $this->db->select('submenu.submenu,dropdown_menu.id,dropdown_menu.dropdown_menu');
        $this->db->from('dropdown_menu');
        $this->db->join('submenu','submenu.id = dropdown_menu.submenu_id','left');
        $this->db->order_by('id DESC');
        return $this->db->get()->result();
    }

    //add
    public function add($data)
    {
        return $this->db->insert('dropdown_menu', $data);
    }

    //get
    public function get($id)
    {
        $this->db->where('id', clean_number($id));
        return $this->db->get('dropdown_menu')->row();
    }

    //update pages
    public function update($id, $data)
    {
        $id = clean_number($id);

        $this->db->where('id', $id);
        return $this->db->update('dropdown_menu', $data);
    }

    //delete
    public function delete($id)
    {
        $id = clean_number($id);
        $page = $this->get($id);
        if (!empty($page)) {
            $this->db->where('id', $id);
            return $this->db->delete('dropdown_menu');
        }
        return false;
    }

}

?>