<?php
class M_user extends CI_Model {
 
  public function __construct(){
    parent::__construct();
  		$this->load->model("m_stringlib");
  		$this->load->model("m_group");  	
  		$this->load->model("m_hour_rate");  		

  		// for BU structure
  		$this->load->model("m_business");
  		$this->load->model("m_department");
  		$this->load->model("m_position");
	}	
	function get_user ($login_name,$password) {
		$business = new stdClass();
		$query = $this->db->get_where('user', array('username' => $login_name,'password' => $password));
		
		if ($query->num_rows() > 0) {
			$business = $query->result();
			$business = $business[0];
			$business->prem=$this->m_group->get_prem_group($business->g_prem_id);

		}
		return $business;
	}
	function get_user_by_login_name ($login_name) {
		$business = new stdClass();
		$query = $this->db->get_where('user', array('username' => $login_name));
		
		if ($query->num_rows() > 0) {
			$business = $query->result();
			$business = $business[0];
			$business->prem=$this->m_group->get_prem_group($business->g_prem_id);
		}
		return $business;
	}
	function check_have_user($login_name){
		$flag=false;
		$query = $this->db->get_where('user', array('username' => $login_name));
		
		if ($query->num_rows() > 0) {
			$flag=true;
		}
		return $flag;
	}

	function get_all_user($group="all"){
		$g_list = array();
		//$this->db->select('username, firstname, lastname');
		if ($group!="all") {
			$this->db->where('g_prem_id', $group);
		}
		$this->db->order_by("firstname", "asc");
		$query = $this->db->get('user');
		
		if ($query->num_rows() > 0) {
			$g_list = $query->result();
		}
		return $g_list;

	}
	function get_all_user_by_group_id($g_prem_id){
		$g_list = array();
		$g_list2 = array();
		$this->db->where('g_prem_id', $g_prem_id);
		$this->db->order_by("firstname", "asc");
		$query = $this->db->get('user');
		
		if ($query->num_rows() > 0) {
			$g_list = $query->result();
			foreach ($g_list as $key => $value) {
				$g_list2[$value->username]=$value;
			}
		}
		return $g_list2;

	}
	function get_all_user_by_prem($prem){
		$g_list = array();
		$g_list2 = array();
		$group_list=$this->m_group->get_prem_group_by_prem($prem);
		foreach ($group_list as $key => $value) {
			$g_list=$this->get_all_user_by_group_id($key);
			foreach ($g_list as $key2 => $value2) {
				$g_list2[$value2->username]=$value2;
			}
		}
		return $g_list2;
	}
	function get_all_user_by_position_id($position_id){
		$g_list = array();
		$g_list2 = array();
		$this->db->where('position', $position_id);
		$this->db->order_by("firstname", "asc");
		$query = $this->db->get('user');
		
		if ($query->num_rows() > 0) {
			$g_list = $query->result();
			foreach ($g_list as $key => $value) {
				$g_list2[$value->username]=$value;
				$g_list2[$value->username]->prem=$this->m_group->get_prem_group($value->g_prem_id);
			}
		}
		return $g_list2;

	}
	function get_all_user_by_task_type($id){
		$g_list = array();
		$g_list2 = array();
		$this->db->where('hour_rate_id', $id);
		$query = $this->db->get('position_has_hour_rate');
		
		if ($query->num_rows() > 0) {
			$g_list = $query->result();
			foreach ($g_list as $key => $value) {
				$g_list[$key]->user_dat=$this->get_all_user_by_position_id($g_list[$key]->position_id);
				foreach ($g_list[$key]->user_dat as $u_key => $u_value) {
					if (!isset($g_list2[$u_key])) {
						$g_list2[$u_key]=$u_value;
					}
				}
			}
		}
		// echo "<br><br><br>";
		// echo "get_all_user_by_task_type<br>";
		// print_r($g_list2);
		// echo "<br><br><br>";
		return $g_list2;

	}
	function get_all_task_type_by_user($username){
		$user=$this->get_user_by_login_name($username);
		$hour_id_list=$this->m_hour_rate->get_hour_rate_has_position($user->position,true);
		return $hour_id_list;

	}
	function delete_user ($username) {
		$user=$this->get_user_by_login_name($username);
		@unlink("./media/sign_photo/".$user->sign_filename);
		$this->m_hour_rate->delete_hour_rate_has_usn($username);
		$this->db->where('username', $username);
		$this->db->delete('user');
	}
	function add_user ($data) {
		$this->db->insert('user', $data);
	}
	function update_user($data, $username) {
		$this->db->where('username', $username);
		$this->db->update('user', $data);
	}
	function check_user_username($username)
	{
		$isuniq    = FALSE;
			$query = $this->db->get_where('user', array('username' => $username));
			if ($query->num_rows() == 0)
			{
				$isuniq    = TRUE;
			}
	
		return $isuniq;
	}
	function get_all_user_by_super_usn_1_lv($usn){
		$g_list = array();
		$g_list2 = array();
		$this->db->where('supervisor', $usn);
		$this->db->order_by("firstname", "asc");
		$query = $this->db->get('user');
		
		if ($query->num_rows() > 0) {
			$g_list = $query->result();
			foreach ($g_list as $key => $value) {
				$g_list2[$value->username]=$value;
			}
		}
		return $g_list2;

	}
	function get_all_user_by_super_usn_all_lv($usn,$parent_usn){
		$is_break=false;
		$user_list = $this->get_all_user_by_super_usn_1_lv($usn);
		foreach ($user_list as $key => $value) {
			if ($parent_usn==$value->username) {
				//$is_break=true;
				//unset($user_list[$key]);
			}else{
				// if ($is_break) {
				// 	break;
				// }else{
					$user_list[$key]->user_list=$this->get_all_user_by_super_usn_all_lv($value->username,$parent_usn);
				//}
				
			}
			
		}

		return $user_list;

	}
	function contructBuTree(){
		$business=$this->m_business->get_all_business();
		foreach ($business as $key => $value) {
			$business[$key]->depart=$this->m_department->get_all_department($value->id);
			foreach ($business[$key]->depart as $key2 => $value2) {
				$business[$key]->depart[$key2]->pos=$this->m_position->get_all_position($value2->id);
				foreach ($business[$key]->depart[$key2]->pos as $key3 => $value3) {
					$business[$key]->depart[$key2]->pos[$key3]->user=$this->get_all_user_by_position_id($value3->id);
				}
			}
		}
		return $business;
	}

	function change_node_user_to_array($user_list){
		$list=array();
		
		foreach ($user_list as $key => $value) {
			$list2=array();
			if (!isset($value->user_list)) {
				$list[$value->username]=$value;
			}else{
				$list2=$this->change_node_user_to_array($value->user_list);
				unset($value->user_list);
				$list[$value->username]=$value;
			}			
			foreach ($list2 as $key2 => $value2) {
				$list[$value2->username]=$value2;
			}
			
		}
		return $list;

	}
	
}