<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Summernote extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
        $path="public/uploads/summernote/";

        if(!empty($_FILES['image']['name']))
        {
             $result = $this->functions->file_insert($path, 'image', 'image', '90097152');
             echo $result['msg'];  
        }
	}
    
	public function delete()
	{
        $src = $this->input->post('src');
        $path="public/uploads/summernote/";
        $new_str = preg_replace("/\s+/", "", $src);
        $this->functions->only_delete_file($path, $new_str);
        echo $new_str;
	}

}

?>	