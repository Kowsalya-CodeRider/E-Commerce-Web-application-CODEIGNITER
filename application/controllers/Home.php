<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends Main_Controller {

	public function __construct()
	{
		parent::__construct();
		//$this->load->model('home_model');
		$this->load->model('admin/categories_model');
		$this->load->model('admin/menu_model');
		$this->load->model('admin/services_model');
		$this->load->model('admin/currency_model');
		$this->load->model('admin/settings_model');
		$this->load->model('admin/pages_model');
        $this->load->helper('text');
	}

	//-----------------------------------------------------------------------------
	// Index funciton will call bydefault
	public function index()
	{	
        
		$data['services'] = $this->services_model->get_services();
		$data['categories'] = $this->categories_model->get_categories_home();
		$data['menu'] = $this->menu_model->get_menu_home();
		$data['cart_contents'] = $this->cart->contents();
		$data['view'] = 'home';
		$this->load->view('index', $data);
	}

	public function search()
	{	
     $search=  $this->input->post('search');
		
		$output = array(
			'product_list'			=>	$this->services_model->search($search)
		);
		echo json_encode($output);
     //$query = $this->services_model->search($search);
     //echo json_encode($query);
	}

	function fetch_data()
	{
		//sleep(2);
		$category = $this->input->post('category');
		$this->load->library("pagination");
		$config = array();
		$config["base_url"] = "#";
		$config["total_rows"] = $this->services_model->count_all($category);
		$config["per_page"] = 4;
		$config["uri_segment"] = 3;
		$config["use_page_numbers"] = TRUE;
		$config["full_tag_open"] = '<ul class="pagination pagination-sm align-items-center">';
		$config["full_tag_close"] = '</ul>';
		$config["first_tag_open"] = '<li class="page-item ml-2">';
		$config["first_tag_close"] = '</li>';
		$config["last_tag_open"] = '<li class="page-item mr-2">';
		$config["last_tag_close"] = '</li>';
		$config['next_link'] = '<li class="page-item ml-2"><span class="page-link">Next</span></li>';
		$config["next_tag_open"] = '<li class="page-item">';
		$config["next_tag_close"] = '</li>';
		$config["prev_link"] = '<li class="page-item mr-2"><span class="page-link">Previous</span></li>';
		$config["prev_tag_open"] = "<li class='page-item'>";
		$config["prev_tag_close"] = "</li>";
		$config["cur_tag_open"] = "<li class='page-item active mr-2'><a class='page-link' href='#'>";
		$config["cur_tag_close"] = "</a></li>";
		$config["num_tag_open"] = "<li class='page-item ml-2'>";
		$config["num_tag_close"] = "</li>";
		$config["num_links"] = 3;
		$this->pagination->initialize($config);
		$page = $this->uri->segment('3');
		$start = ($page - 1) * $config["per_page"];
		
		$output = array(
			'pagination_link'		=>	$this->pagination->create_links(),
			'product_list'			=>	$this->services_model->fetch_data($config["per_page"], $start, $category)
		);
		echo json_encode($output);
	}

    public function save_cart()
    {
        $data       = array();
        $service_id = $this->input->post('service_id');
        $results    = $this->services_model->get($service_id);
		$qty = $this->input->post('qty');
		$qty = (is_null($qty) || empty($qty)) ? '1':$qty;
        // var_dump($results);
        // exit();
        

        $data['id']      = $results->id;
        $data['name']    = $results->title;
        $data['price']   = $results->price;
        $data['qty']     = $qty;
        $data['options'] = array('image' => $results->imagelocation, 'description' => $results->description);
		$data['user_message'] = $this->input->post('user_message');
		
		
        $this->cart->insert($data);
        redirect($_SERVER['HTTP_REFERER']);
    }

    public function remove_cart()
    {
        $data = $this->input->post('rowid');
        $this->cart->remove($data);
        redirect($_SERVER['HTTP_REFERER']);
    }

// 	public function removeCartItem($rowid) {   
//         $data = array(
//             'rowid'   => $rowid,
//             'qty'     => 0
//         );

//         $this->cart->update($data);
// }

	public function page($slug)
	{	
        //$slug = $this->uri->segment(2);
        //$slug = 'website-development-using-htmlcssphp';
		$data['page'] = $this->pages_model->get_by_slug($slug);
		$data['view'] = 'page';
		$this->load->view('layout', $data);
	}

	// Language
	public function lang($lang) {
        
		$language_data = array(
			'language' => $lang
		);
        $this->settings_model->update_settings($language_data);
        
		redirect($_SERVER['HTTP_REFERER']);

		exit;
	}
	
	public function newdesign()
	{	
        
		$data['services'] = $this->services_model->get_services();
		$data['categories'] = $this->categories_model->get_categories_home();
        $data['cart_contents'] = $this->cart->contents();
		$data['view'] = 'home';
		$this->load->view('new', $data);
	}

    public function getsubmenu($menuid){
        $this->db->select('submenu.id,submenu,menu_id,dropdown_menu.id as dropdownmenu_id');
        $this->db->from('submenu');
        $this->db->join('dropdown_menu','dropdown_menu.submenu_id = submenu.id','left');
        $this->db->where('menu_id',$menuid);
        $this->db->group_by('submenu.id');
        $result = $this->db->get()->result();
        echo json_encode($result);
    }

    public function getdropdownmenu($menuid){
        $this->db->where('submenu_id',$menuid);
        $result = $this->db->get('dropdown_menu')->result();
        echo json_encode($result);
    }

}// endClass
