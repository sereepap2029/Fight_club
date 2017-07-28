<?php
class M_Rsheet extends CI_Model {
 
  public function __construct(){
    parent::__construct();
  		$this->load->model("m_stringlib");
  		$this->load->model("m_user");  	
  		$this->load->model("m_project");  		
	}	
	function generate_id()
	{
		$isuniq    = FALSE;
		$clam_id = '';
		do
		{
			$temp_id = $this->m_stringlib->uniqueAlphaNum10();
			$query = $this->db->get_where('r_sheet', array('r_id' => $temp_id));
			if ($query->num_rows() == 0)
			{
				$clam_id = $temp_id;
				$isuniq    = TRUE;
			}
		}
		while(!$isuniq);
	
		return $clam_id;
	}
	function get_sort_order($project_id){
		$this->db->where('project_id', $project_id);
		$query = $this->db->get('r_sheet');
		$redat=$query->num_rows()+1;
		return $redat;
	}
	function delete_r_sheet ($r_id) {
		$this->delete_r_sheet_allow_hour_by_r_id($r_id);
		$this->db->where('r_id', $r_id);
		$this->db->delete('r_sheet');
	}
	function delete_r_sheet_allow_hour ($id) {
		$this->db->where('id', $id);
		$this->db->delete('r_sheet_has_allow_res');
	}
	function delete_r_sheet_delegate ($id) {
		$this->db->where('id', $id);
		$this->db->delete('r_sheet_delegate');
	}
	function delete_r_sheet_allow_hour_by_r_id ($r_id) {
		$this->db->where('r_id', $r_id);
		$this->db->delete('r_sheet_has_allow_res');
	}
	function delete_r_sheet_by_project_id ($project_id) {
		$this->db->where('project_id', $project_id);
		$this->db->delete('r_sheet');
	}
	function add_r_sheet ($data) {
		$this->db->insert('r_sheet', $data);
	}
	function add_r_sheet_allow_hour ($data) {
		$this->db->insert('r_sheet_has_allow_res', $data);
	}
	function add_r_sheet_delegate ($data) {
		$this->db->insert('r_sheet_delegate', $data);
	}
	function update_r_sheet($data, $r_id) {
		$this->db->where('r_id', $r_id);
		$this->db->update('r_sheet', $data);
	}
	function update_r_sheet_allow_hour($data, $id) {
		$this->db->where('id', $id);
		$this->db->update('r_sheet_has_allow_res', $data);
	}
	function update_r_sheet_delegate($data, $id) {
		$this->db->where('id', $id);
		$this->db->update('r_sheet_delegate', $data);
	}
	function get_r_sheet_allow_hour_by_id ($id) {
		$business = new stdClass();
		$query = $this->db->get_where('r_sheet_has_allow_res', array('id' => $id));
		
		if ($query->num_rows() > 0) {
			$business = $query->result();
			$business = $business[0];
		}
		return $business;
	}
	function get_all_r_sheet_by_project_id($project_id,$order_by="sort_order",$order_type="asc"){
		$g_list = array();
		$g_list2 = array();
		$this->db->where('project_id', $project_id);
		$this->db->order_by($order_by, $order_type);
		$query = $this->db->get('r_sheet');
		
		if ($query->num_rows() > 0) {
			$g_list = $query->result();
			foreach ($g_list as $key => $value) {
				$g_list2[$value->r_id]=$value;
			}
		}
		return $g_list2;

	}
	function get_allow_list_by_r_id($r_id,$usn="no"){
		$g_list = array();
		$g_list2 = array();
		$this->db->where('r_id', $r_id);
		if ($usn!="no") {
			$this->db->where('resource_usn', $usn);
		}
		$query = $this->db->get('r_sheet_has_allow_res');
		
		if ($query->num_rows() > 0) {
			$g_list = $query->result();
		}
		return $g_list;

	}
	function get_delegate_list_by_r_id($r_id,$usn="no"){
		$g_list = array();
		$g_list2 = array();
		$this->db->where('r_id', $r_id);
		if ($usn!="no") {
			$this->db->where('resource_usn', $usn);
		}
		$query = $this->db->get('r_sheet_delegate');
		
		if ($query->num_rows() > 0) {
			$g_list = $query->result();
		}
		return $g_list;

	}
	function get_delegate_list_by_project_id($project_id,$usn="no",$mode="usn"){
		$g_list = array();
		$g_list2 = array();
		$this->db->where('project_id', $project_id);
		if ($usn!="no") {
			$this->db->where('resource_usn', $usn);
		}
		$query = $this->db->get('r_sheet_delegate');
		
		if ($query->num_rows() > 0) {
			$g_list = $query->result();
			foreach ($g_list as $key => $value) {
				if ($mode=="usn") {
					$g_list2[$value->resource_usn]=$value;
				}else{
					$g_list2[$value->r_id]=$value;
				}
				
			}
		}
		return $g_list2;

	}
	function check_hod_assign_res_by_r_id($r_id){
		$g_list = array();
		$g_list2 = array();
		$this->db->where('r_id', $r_id);
		$query = $this->db->get('r_sheet_has_allow_res');
		$assign=false;
		if ($query->num_rows() > 0) {
			$assign=true;
		}
		return $assign;

	}
	function get_r_sheet_by_id ($r_id) {
		$business = new stdClass();
		$query = $this->db->get_where('r_sheet', array('r_id' => $r_id));
		
		if ($query->num_rows() > 0) {
			$business = $query->result();
			$business = $business[0];
		}
		return $business;
	}
	function find_hod_by_t_type($id){
		$usn_list=$this->m_user->get_all_user_by_task_type($id);
		$return_val=null;
		$hod_list=array();
		$searched_list=array();
		foreach ($usn_list as $key => $value) {
			$super=$value->supervisor;
			if (!isset($searched_list[$super])) {
				$flag_findupper_hod=true;
				$usn_tmp=$this->m_user->get_user_by_login_name($super);
				while ($flag_findupper_hod) {					
					if (isset($usn_tmp->username)) {
						if (isset($usn_tmp->prem['hod'])) {
						 	$hod_list[$super]=$super;
						 	$flag_findupper_hod=false;
						 	//echo "<br>".$usn_tmp->username."<br>";
						 }else{
						 	$usn_tmp=$this->m_user->get_user_by_login_name($usn_tmp->supervisor);
						 	$super=$usn_tmp->username;
						 }
					}else{
						$flag_findupper_hod=false;
					}
					 
				}
				
				 if (isset($value->prem['hod'])) {
				 	$hod_list[$value->username]=$value->username;
				 }
				$searched_list[$super]=$super; 
					
			}
		}
		//echo "end get by t type user<br>";
		//print_r($usn_list);
		//echo "end get user<br>";
		$return_val=$hod_list;
		
		return $return_val;
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
	function find_resource_by_t_type_and_hod_usn($id,$super_usn){
		$usn_list=$this->m_user->get_all_user_by_task_type($id);
		$usn_under_super=$this->m_user->get_all_user_by_super_usn_all_lv($super_usn,$super_usn);
		$child_ready=$this->change_node_user_to_array($usn_under_super);
		//print_r($child_ready);
		$searched_list=array();
		foreach ($child_ready as $key => $value) {
			if (isset($usn_list[$value->username])) {
				$searched_list[$value->username]=$value;
			}
		}
		if (isset($usn_list[$super_usn])) {
			$searched_list[$super_usn]=$this->m_user->get_user_by_login_name($super_usn);
		}
		ksort($searched_list);
		return $searched_list;
	}
	function r_sheet_valid_hour($r_id){
		$allow_list=$this->get_allow_list_by_r_id($r_id);
		$r_sheet=$this->get_r_sheet_by_id($r_id);
		$sum_hour_allow=0;
		$flag=true;
		foreach ($allow_list as $key => $value) {
			$sum_hour_allow+=(int)$value->allow_hour;
		}
		if ($sum_hour_allow>$r_sheet->approve_budget) {
			$flag=false;
		}
		return $flag;
	}
	function get_sum_approve_budget_by_type_and_project_id($type,$project_id){
		$g_list = array();
		$sum=0;
		$this->db->where('type', $type);
		$this->db->where('project_id', $project_id);
		$query = $this->db->get('r_sheet');
		
		if ($query->num_rows() > 0) {
			$g_list = $query->result();
			foreach ($g_list as $key => $value) {
				$sum+=$value->approve_budget;
			}

		}
		return $sum;
	}
	function get_delegate_project_list($usn){
		$project_use = array();
		$project_list=$this->m_project->get_all_project_by_status("WIP");
		foreach ($project_list as $key => $value) {
			$r_sheet_list=$this->m_Rsheet->get_all_r_sheet_by_project_id($value->project_id);
				foreach ($r_sheet_list as $r_key => $r_value) {
					$allow_res=$this->m_Rsheet->get_delegate_list_by_r_id($r_value->r_id,$usn);
					foreach ($allow_res as $a_key => $a_value) {
						if (isset($a_value->resource_usn)) {
							$project_use[$value->project_id]=$value;
							break;
						}
					}
					if (isset($project_use[$value->project_id])) {
						break;
					}
				}
		}
		$project_list=$this->m_project->get_all_project_by_status("Proposing");
		foreach ($project_list as $key => $value) {
			$r_sheet_list=$this->m_Rsheet->get_all_r_sheet_by_project_id($value->project_id);
				foreach ($r_sheet_list as $r_key => $r_value) {
					$allow_res=$this->m_Rsheet->get_delegate_list_by_r_id($r_value->r_id,$usn);
					foreach ($allow_res as $a_key => $a_value) {
						if (isset($a_value->resource_usn)) {
							$project_use[$value->project_id]=$value;
							break;
						}
					}
					if (isset($project_use[$value->project_id])) {
						break;
					}
				}
		}
		return $project_use;
	}
	function check_delegate_project_by_project_id($project_id){
		$project_use = false;
			$r_sheet_list=$this->m_Rsheet->get_all_r_sheet_by_project_id($project_id);
				foreach ($r_sheet_list as $r_key => $r_value) {
					$allow_res=$this->m_Rsheet->get_delegate_list_by_r_id($r_value->r_id);
					foreach ($allow_res as $a_key => $a_value) {
						if (isset($a_value->resource_usn)) {
							$project_use = true;
							break;
						}
					}
					if ($project_use) {
						break;
					}
				}
		return $project_use;
	}
	function combine_same_type($project_id){
		$r_list=$this->get_all_r_sheet_by_project_id($project_id);
		$delegate_r=$this->get_delegate_list_by_project_id($project_id,"no","r_id");
		$ins_r_list = array();
		foreach ($r_list as $key => $value) {
			if (!isset($ins_r_list[$value->type])) {
				$ins_r_list[$value->type]['task']=$value->task;
				$ins_r_list[$value->type]['approve_budget']=$value->approve_budget;
				$ins_r_list[$value->type]['project_id']=$value->project_id;
				$allow_res=$this->get_allow_list_by_r_id($value->r_id);
				foreach ($allow_res as $akey => $avalue) {
					$ins_r_list[$value->type]['assign_list'][$avalue->resource_usn]=$avalue;
				}
				if (isset($delegate_r[$value->r_id])) {
					$ins_r_list[$value->type]['delegate_list'][$value->r_id]=$delegate_r[$value->r_id]->id;
				}
				$this->delete_r_sheet_allow_hour_by_r_id($value->r_id);

			}else{
				$ins_r_list[$value->type]['task'].=",".$value->task;
				$ins_r_list[$value->type]['approve_budget']+=$value->approve_budget;
				$allow_res=$this->get_allow_list_by_r_id($value->r_id);
				foreach ($allow_res as $akey => $avalue) {
					$ins_r_list[$value->type]['assign_list'][$avalue->resource_usn]=$avalue;
				}
				if (isset($delegate_r[$value->r_id])) {
					$ins_r_list[$value->type]['delegate_list'][$value->r_id]=$delegate_r[$value->r_id]->id;
				}
				$this->delete_r_sheet_allow_hour_by_r_id($value->r_id);
			}
		}
		$this->delete_r_sheet_by_project_id($project_id);
		foreach ($ins_r_list as $key => $value) {
			$r_id=$this->generate_id();
				$r_sheet_dat= array(
					'r_id' => $r_id,
					'task' => $value['task'],
					'type' => $key,
					'approve_budget' => $value['approve_budget'],
					'project_id' => $value['project_id'],
					'sort_order' => $this->m_Rsheet->get_sort_order($value['project_id']),
					);
				$this->add_r_sheet ($r_sheet_dat);
				
				if (isset($value['assign_list'])) {
					foreach ($value['assign_list'] as $bkey => $bvalue) {
						$ass_dat= array(
						'r_id' => $r_id,
						'allow_hour' => $bvalue->allow_hour,
						'resource_usn' => $bvalue->resource_usn,
						);
						$this->add_r_sheet_allow_hour($ass_dat);
					}
				}
				if (isset($value['delegate_list'])) {
					foreach ($value['delegate_list'] as $ckey => $cvalue) {
						$del_dat= array(
						'r_id' => $r_id,
						);
						$this->update_r_sheet_delegate($del_dat,$cvalue);
					}
				}
				
		}
	}
	function check_res_with_type_in_project($usn,$task_type,$project_id){
		$g_list = array();
		$fag=false;
		$this->db->where('project_id', $project_id);
		$this->db->where("type", $task_type);
		$query = $this->db->get('r_sheet');
		
		if ($query->num_rows() > 0) {
			$g_list = $query->result();
			foreach ($g_list as $key => $value) {
				$res=$this->get_allow_list_by_r_id($value->r_id,$usn);
				if (isset($res[0]->resource_usn)) {
					$fag=true;
				}
			}
		}
		return $fag;
	}
}