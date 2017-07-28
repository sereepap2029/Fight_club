<?php
class M_admin extends CI_Model {
 
  public function __construct(){
    parent::__construct();
  		$this->load->model("m_stringlib");  		
	}	

	function get_admin(){
		$g_list = array();
		$this->db->order_by("username", "desc"); 
		$query = $this->db->get('user');
		
		if ($query->num_rows() > 0) {
			$g_list = $query->result();
		}
		return $g_list;
	}

	function get_admin_by_username($username){
		$admin = new stdClass();
		$this->db->where('username', $username);
		$query = $this->db->get('user');
		
		if ($query->num_rows() > 0) {
			$admin = $query->result();
			$admin = $admin[0];
		}
		return $admin;
	}
	function delete_admin ($username) {
		$this->db->where('username', $username);
		$this->db->delete('user');
	}
	function add_admin ($data) {
		$this->db->insert('user', $data);
	}
	function update_admin($data, $username) {
		$this->db->where('username', $username);
		$this->db->update('user', $data);
	}
	function check_admin_username($username)
	{
		$isuniq    = FALSE;
			$query = $this->db->get_where('user', array('username' => $username));
			if ($query->num_rows() == 0)
			{
				$isuniq    = TRUE;
			}
	
		return $isuniq;
	}
}