<?php
class Menu_model extends CI_Model{


    public function get_menu(){
        $this->db->order_by('id DESC');
        return $this->db->get('menu')->result();
    }

    public function get_menu_home(){

        $this->db->order_by('id ASC');
        return $this->db->get('menu')->result();
        /*$sql = "SELECT menu.id as menu_id, menu.menu,GROUP_CONCAT(submenu.id) as submenu_id,
                    GROUP_CONCAT(submenu.submenu) as submenu,
                    GROUP_CONCAT(dropdown_menu.id) as dropdown_menu_id,
                    GROUP_CONCAT(dropdown_menu.dropdown_menu) as dropdown_menu
                    FROM `menu`
                    LEFT JOIN submenu ON submenu.menu_id = menu.id
                    LEFT JOIN dropdown_menu ON dropdown_menu.submenu_id = submenu.id
                    GROUP BY menu.id";
        $query = $this->db->query($sql);
        return $query->result();*/

    }

    //add
    public function add($data)
    {
        return $this->db->insert('menu', $data);
    }

    //get
    public function get($id)
    {
        $this->db->where('id', clean_number($id));
        return $this->db->get('menu')->row();
    }

    //update pages
    public function update($id, $data)
    {
        $id = clean_number($id);

        $this->db->where('id', $id);
        return $this->db->update('menu', $data);
    }

    //delete
    public function delete($id)
    {
        $id = clean_number($id);
        $page = $this->get($id);
        if (!empty($page)) {
            $this->db->where('id', $id);
            return $this->db->delete('menu');
        }
        return false;
    }

}

?>