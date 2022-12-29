<?php
class Submenu_model extends CI_Model{


    public function get_submenu(){
        $this->db->select('menu.menu,submenu.id,submenu.submenu');
        $this->db->from('submenu');
        $this->db->join('menu','menu.id = submenu.menu_id','left');
        $this->db->order_by('id DESC');
        return $this->db->get()->result();
    }

    //add
    public function add($data)
    {
        return $this->db->insert('submenu', $data);
    }

    //get
    public function get($id)
    {
        $this->db->where('id', clean_number($id));
        return $this->db->get('submenu')->row();
    }

    //update pages
    public function update($id, $data)
    {
        $id = clean_number($id);

        $this->db->where('id', $id);
        return $this->db->update('submenu', $data);
    }

    //delete
    public function delete($id)
    {
        $id = clean_number($id);
        $page = $this->get($id);
        if (!empty($page)) {
            $this->db->where('id', $id);
            return $this->db->delete('submenu');
        }
        return false;
    }

}

?>