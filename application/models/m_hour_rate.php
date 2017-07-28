<?php
class M_hour_rate extends CI_Model {
 
  public function __construct(){
    parent::__construct();
  		$this->load->model("m_stringlib");  		
	}	
	function delete_hour_rate ($id) {
		$this->db->where('id', $id);
		$this->db->delete('hour_rate');
	}
	function add_hour_rate ($data) {
		$this->db->insert('hour_rate', $data);
	}
	function update_hour_rate($data, $id) {
		$this->db->where('id', $id);
		$this->db->update('hour_rate', $data);
	}
	function get_all_hour_rate(){
		$g_list = array();
		$this->db->order_by("name", "asc");
		$query = $this->db->get('hour_rate');
		
		if ($query->num_rows() > 0) {
			$g_list = $query->result();
		}
		return $g_list;

	}
	function get_hour_rate_by_id ($id) {
		$business = new stdClass();
		$query = $this->db->get_where('hour_rate', array('id' => $id));
		
		if ($query->num_rows() > 0) {
			$business = $query->result();
			$business = $business[0];
		}
		return $business;
	}
	function get_hour_rate_has_usn ($usn) {
		$g_list = array();
		$query = $this->db->get_where('user_has_hour_rate', array('usn' => $usn));
		
		if ($query->num_rows() > 0) {
			$g_list = $query->result();
		}
		return $g_list;
	}
	function get_hour_rate_has_position ($position_id,$make_hour_to_id=false) {
		$g_list = array();
		$g_list2 = array();
		$query = $this->db->get_where('position_has_hour_rate', array('position_id' => $position_id));
		
		if ($query->num_rows() > 0) {
			$g_list = $query->result();
			if ($make_hour_to_id) {
				
				foreach ($g_list as $key => $value) {
					$g_list2[$value->hour_rate_id]=$value;
				}
			}else{
				$g_list2=$g_list;
			}
		}
		return $g_list2;
	}
	function get_all_hour_rate_has_position(){
		$g_list = array();
		$query = $this->db->get('position_has_hour_rate');
		
		if ($query->num_rows() > 0) {
			$g_list = $query->result();
		}
		return $g_list;
	}
	function get_hour_rate_has_position_by_hour ($hour_rate_id) {
		$g_list = array();
		$query = $this->db->get_where('position_has_hour_rate', array('hour_rate_id' => $hour_rate_id));
		
		if ($query->num_rows() > 0) {
			$g_list = $query->result();
		}
		return $g_list;
	}
	function delete_hour_rate_has_usn ($usn) {
		$this->db->where('usn', $usn);
		$this->db->delete('user_has_hour_rate');
	}
	function delete_hour_rate_has_position ($position_id) {
		$this->db->where('position_id', $position_id);
		$this->db->delete('position_has_hour_rate');
	}
	function add_hour_rate_has_usn ($data) {
		$this->db->insert('user_has_hour_rate', $data);
	}
	function add_hour_rate_has_position ($data) {
		$this->db->insert('position_has_hour_rate', $data);
	}
	
}