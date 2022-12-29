<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Dropdownmenu extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('admin/dropdownmenu_model');
        $this->load->model('admin/submenu_model');
    }

    public function index()
    {

        if(!$this->session->has_userdata('is_admin_login'))
        {
            redirect('admin/auth/login');
        }else{
            redirect('admin/dropdownmenu/list');
        }
    }

    public function list()
    {

        $data['dropdownmenu'] = $this->dropdownmenu_model->get_dropdownmenu();
        $data['title'] = 'Dropdown Menu';
        $data['view'] = 'admin/dropdownmenu/dropdownmenu';
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
                redirect(base_url('admin/dropdownmenu/add'));
            }else{

                $data = array(
                    'dropdown_menu' => ucfirst($this->input->post('name')),
                    'submenu_id' => $this->input->post('submenu_id')
                );
                $data = $this->security->xss_clean($data);
                $result = $this->dropdownmenu_model->add($data);

                if ($result){
                    $this->session->set_flashdata('success', 'Saved Successfully');
                    redirect(base_url('admin/dropdownmenu/add'));
                }else{
                    $this->session->set_flashdata('warning', 'Error when saving Data');
                    redirect(base_url('admin/dropdownmenu/add'));
                }
            }
        }

        $data['title'] = 'Dropdown Menu';
        $data['view'] = 'admin/dropdownmenu/dropdownmenu';
        $data['submenu'] = $this->submenu_model->get_submenu();
        $this->load->view('admin/layout', $data);
    }

    public function edit($id)
    {
        $data['dropdownmenu'] = $this->dropdownmenu_model->get($id);

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
                redirect(base_url('admin/dropdownmenu/edit/'. $id));
            }else{

                $data = array(
                    'dropdown_menu' => ucfirst($this->input->post('name')),
                    'submenu_id' => $this->input->post('submenu_id')
                );
                $data = $this->security->xss_clean($data);
                $id = $this->input->post('id', true);
                $result = $this->dropdownmenu_model->update($id, $data);

                if ($result){
                    $this->session->set_flashdata('success', 'Updated Successfully');
                    redirect(base_url('admin/dropdownmenu/edit/'. $id));
                }else{
                    $this->session->set_flashdata('warning', 'Error when saving Data');
                    redirect(base_url('admin/dropdownmenu/edit/'. $id));
                }
            }
        }

        $data['title'] = 'Sub Menu';
        $data['view'] = 'admin/dropdownmenu/dropdownmenu';
        $data['submenu'] = $this->submenu_model->get_submenu();
        $this->load->view('admin/layout', $data);
    }

    public function delete()
    {
        $id = $this->input->post('id', true);
        if ($this->submenu_model->delete($id)) {
            $this->session->set_flashdata('success', get_phrase("deleted_successfully"));
        } else {
            $this->session->set_flashdata('error', get_phrase("error_on_deleting"));
        }
    }

}

?>