<?php
class Email_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
	}

	/*--------------------------
	   Email Template Settings
	--------------------------*/

	function get_email_templates()
	{
		return $this->db->get('email_templates')->result_array();
	}

	function update_email_template($data,$id)
	{
		$this->db->where('id', $id);
		$this->db->update('email_templates', $data);
		return true;
	}

	function get_email_template_content_by_id($id)
	{
		return $this->db->get_where('email_templates',array('id' => $id))->row_array();
	}

	function get_email_template_variables_by_id($id)
	{
		return $this->db->get_where('email_template_variables',array('template_id' => $id))->result_array();
	}
	
}
?>