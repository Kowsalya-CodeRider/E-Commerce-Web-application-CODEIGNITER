<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Menu extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('admin/menu_model');
    }

    public function index()
    {

        if(!$this->session->has_userdata('is_admin_login'))
        {
            redirect('admin/auth/login');
        }else{
            redirect('admin/menu/list');
        }
    }

    public function list()
    {

        $data['menu'] = $this->menu_model->get_menu();
        $data['title'] = 'Menu';
        $data['view'] = 'admin/menu/menu';
        $this->load->view('admin/layout', $data);
    }

    public function add()
    {

        if (isset($_POST) && !empty($_POST))
        {

            //validate inputs
            $this->form_validation->set_rules('name','Name','trim|required');

            if ($this->form_validation->run() == FALSE)
            {
                $data = array(
                    'errors' => validation_errors(),
                );
                $this->session->set_flashdata('validation_errors', $data['errors']);
                redirect(base_url('admin/menu/add'));
            }else{

                $data = array(
                    'menu' => ucfirst($this->input->post('name')),
                );
                $data = $this->security->xss_clean($data);
                $result = $this->menu_model->add($data);

                if ($result){
                    $this->session->set_flashdata('success', 'Saved Successfully');
                    redirect(base_url('admin/menu/add'));
                }else{
                    $this->session->set_flashdata('warning', 'Error when saving Data');
                    redirect(base_url('admin/menu/add'));
                }
            }
        }

        $data['title'] = 'Menu';
        $data['view'] = 'admin/menu/menu';
        $this->load->view('admin/layout', $data);
    }

    public function edit($id)
    {
        $data['menu'] = $this->menu_model->get($id);

        if (isset($_POST) && !empty($_POST))
        {

            //validate inputs
            $this->form_validation->set_rules('name','Name','trim|required');

            if ($this->form_validation->run() == FALSE)
            {
                $data = array(
                    'errors' => validation_errors(),
                );
                $this->session->set_flashdata('validation_errors', $data['errors']);
                redirect(base_url('admin/menu/edit/'. $id));
            }else{

                $data = array(
                    'menu' => ucfirst($this->input->post('name'))
                );
                $data = $this->security->xss_clean($data);
                $id = $this->input->post('id', true);
                $result = $this->menu_model->update($id, $data);

                if ($result){
                    $this->session->set_flashdata('success', 'Updated Successfully');
                    redirect(base_url('admin/menu/edit/'. $id));
                }else{
                    $this->session->set_flashdata('warning', 'Error when saving Data');
                    redirect(base_url('admin/menu/edit/'. $id));
                }
            }
        }

        $data['title'] = 'Menu';
        $data['view'] = 'admin/menu/menu';
        $this->load->view('admin/layout', $data);
    }

    public function delete()
    {
        $id = $this->input->post('id', true);
        if ($this->menu_model->delete($id)) {
            $this->session->set_flashdata('success', get_phrase("deleted_successfully"));
        } else {
            $this->session->set_flashdata('error', get_phrase("error_on_deleting"));
        }
    }

}

?>