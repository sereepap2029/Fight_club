<?php
class M_resource extends CI_Model {
 
  public function __construct(){
    parent::__construct();
  		$this->load->model("m_stringlib");  		
  		$this->load->model("m_hour_rate");  
  		$this->load->model("m_project");  
  		$this->load->model("m_Rsheet");  
  		$this->load->model("m_user");
  		$this->load->model("m_work_sheet");  
	}	

	function get_res_assign_by_and_usn_and_time($usn,$start,$end,$status="no_interfere",$no_zero_assign=true){
		$g_list = array();
		$g_list2 = array();
		$return_obj = new stdClass();
		$this->db->where('time >=', $start);
		$this->db->where('time <=', $end);
		$this->db->where('usn', $usn);
		if ($no_zero_assign) {
			$this->db->where('hour !=', "0");
		}
		if ($status=="no_interfere") {
			$this->db->where('status !=', "interfere");
			$this->db->where('interfere', "n");
		}else if($status=="all"){

		}else if($status=="interfere"){
			$this->db->where('interfere', "y");
		}else{
			$this->db->where('status', $status);
		}
		$this->db->order_by("time", "asc");
		$query = $this->db->get('work_sheet_assign');
		$return_obj->hour_amount=0;
		if ($query->num_rows() > 0) {
			$g_list = $query->result();			
			foreach ($g_list as $key => $value) {
					$g_list2[$value->time][]=$value;
					$return_obj->hour_amount+=$value->hour;
			}
		}
		$return_obj->list=$g_list2;
		return $return_obj;
	}
	function get_project_WIP($usn,$notSeeDoneOC=false){
		$project_use = array();
		$project_list=$this->m_project->get_all_project_by_status("WIP");
		foreach ($project_list as $key => $value) {
			$r_sheet_list=$this->m_Rsheet->get_all_r_sheet_by_project_id($value->project_id);
				foreach ($r_sheet_list as $r_key => $r_value) {
					$allow_res=$this->m_Rsheet->get_allow_list_by_r_id($r_value->r_id,$usn);
					foreach ($allow_res as $a_key => $a_value) {
						if (isset($a_value->resource_usn)) {
							if ($notSeeDoneOC) {
								$check_hod_assign=$this->m_project->check_hod_assign_resource($value->project_id);
				                $check_all_oc_done=$this->m_oc->check_all_done_oc_by_project_id($value->project_id);
				                if (!($check_hod_assign&&$check_all_oc_done)) {
									$project_use[$value->project_id]=$value;
								}
							}else{
								$project_use[$value->project_id]=$value;
							}
							
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
	function get_project_WIP_revise($usn,$notSeeDoneOC=false){
		$project_use = array();
		$project_list=$this->get_all_project_by_status("WIP_revise","project_name");
		foreach ($project_list as $key => $value) {
			$r_sheet_list=$this->get_rsheet_by_res_usn_project_id($usn,$value->project_id);
				foreach ($r_sheet_list as $a_key => $a_value) {
					if (isset($project_use[$value->project_id])) {
						break;
					}else{
						if (isset($a_value->resource_usn)) {
							if ($notSeeDoneOC) {
								$check_hod_assign=$this->m_project->check_hod_assign_resource($value->project_id);
				                $check_all_oc_done=$this->m_oc->check_all_done_oc_by_project_id($value->project_id);
				                if (!($check_hod_assign&&$check_all_oc_done)) {
									$project_use[$value->project_id]=$value;
								}
							}else{
								if ($value->status!="Done"&&$value->status!="Archive") {
									$project_use[$value->project_id]=$value;
								}
								
							}
							
							break;
						}
					}
					
				}
		}
		return $project_use;
	}
	function get_rsheet_by_res_usn_project_id($usn,$project_id){
		$g_list = array();
		$this->db->where('resource_usn', $usn);
		$this->db->where('project_id', $project_id);
		$this->db->join('r_sheet_has_allow_res', 'r_sheet_has_allow_res.r_id = r_sheet.r_id');
		$query = $this->db->get('r_sheet');
		
		if ($query->num_rows() > 0) {
			$g_list = $query->result();
		}
		return $g_list;

	}
	function get_all_project_by_status($status="all",$order_column="project_start"){
		$g_list = array();
		if ($status!="all") {
			if ($status=="WIP") {
				$where = "(status='WIP' OR status='Delay')";
        		$this->db->where($where);
				//$this->db->where('status', $status);
				//$this->db->or_where('status',"Delay"); 
			}else if($status=="no_draf"){
				$this->db->where('status !=', "Drafing");
				$this->db->where('status !=', "Cancel");
			}else if($status=="WIP_revise"){
				$where = "(status='WIP' OR status='Delay' OR status='Revise' OR status='Proposing')";
        		$this->db->where($where);
			}else{
				$this->db->where('status', $status);
			}
			
		}else{
			$this->db->where('status !=', "Done");
		}

		$this->db->order_by($order_column, "asc");
		$query = $this->db->get('project');
		
		if ($query->num_rows() > 0) {
			$g_list = $query->result();
		}
		return $g_list;
	}
}