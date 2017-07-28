<?php
class M_group extends CI_Model {
 
  public function __construct(){
    parent::__construct();
  		$this->load->model("m_stringlib");  		
	}	
	function generate_id()
	{
		$isuniq    = FALSE;
		$clam_id = '';
		do
		{
			$temp_id = $this->m_stringlib->uniqueAlphaNum10();
			$query = $this->db->get_where('group_prem', array('g_id' => $temp_id));
			if ($query->num_rows() == 0)
			{
				$clam_id = $temp_id;
				$isuniq    = TRUE;
			}
		}
		while(!$isuniq);
	
		return $clam_id;
	}
	function delete_group ($g_id) {
		$this->db->where('g_id', $g_id);
		$this->db->delete('group_prem');
	}
	function delete_group_prem ($g_id) {
		$this->db->where('g_id', $g_id);
		$this->db->delete('group_has_prem');
	}
	function add_group ($data) {
		$this->db->insert('group_prem', $data);
	}
	function add_group_prem ($data) {
		$this->db->insert('group_has_prem', $data);
	}
	function update_group($data, $g_id) {
		$this->db->where('g_id', $g_id);
		$this->db->update('group_prem', $data);
	}
	function get_all_group(){
		$g_list = array();
		$this->db->order_by("g_name", "asc");
		$query = $this->db->get('group_prem');
		
		if ($query->num_rows() > 0) {
			$g_list = $query->result();
		}
		return $g_list;

	}
	function get_group_by_id ($g_id) {
		$business = new stdClass();
		$query = $this->db->get_where('group_prem', array('g_id' => $g_id));
		
		if ($query->num_rows() > 0) {
			$business = $query->result();
			$business = $business[0];
			$business->prem=$this->get_prem_group($business->g_id);
		}
		return $business;
	}
	function get_prem_group($g_id){
		$g_list = array();
		$g_list2 = array();
		$this->db->where('g_id', $g_id);
		$query = $this->db->get('group_has_prem');
		
		if ($query->num_rows() > 0) {
			$g_list = $query->result();
			foreach ($g_list as $key => $value) {
				$g_list2[$value->prem]=$value;
			}
		}
		return $g_list2;
	}

	function get_prem_group_by_prem($prem){
		$g_list = array();
		$g_list2 = array();
		$this->db->where('prem', $prem);
		$query = $this->db->get('group_has_prem');
		
		if ($query->num_rows() > 0) {
			$g_list = $query->result();
			foreach ($g_list as $key => $value) {
				$g_list2[$value->g_id]=$value;
			}
		}
		return $g_list2;
	}
	
}