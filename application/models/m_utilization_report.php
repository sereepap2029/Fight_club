<?php
class M_utilization_report extends CI_Model {
 
  public function __construct(){
    parent::__construct();
  		$this->load->model("m_stringlib");  		
  		$this->load->model("m_work_sheet");  
  		$this->load->model("m_project");  
  		$this->load->model("m_Rsheet");  
  		$this->load->model("m_user"); 
  		$this->load->model("m_time");  
  		$this->load->model("m_hr");  
	}	
	function get_work_sheet_by_usn($usn,$filter){
		$g_list = array();
		$g_list2 = array();
		$return_obj = new stdClass();
		$this->db->where('usn', $usn);
		$this->db->select('work_sheet_assign.time, work_sheet_assign.hour, work_sheet_assign.usn,work_sheet_assign.status, work_sheet_assign.work_sheet_id, work_sheet_assign.spend, work_sheet.project_id');
		if (isset($filter['start_date'])&&$filter['start_date']!="all") {
			$this->db->where('time >=', $filter['start_date']);
		}
		if (isset($filter['end_date'])&&$filter['end_date']!="all") {
			$this->db->where('time <=', $filter['end_date']);
		}		
		$this->db->join('work_sheet', 'work_sheet.id = work_sheet_assign.work_sheet_id');
		$query = $this->db->get('work_sheet_assign');
		$return_obj->hour_amount=0;
		$return_obj->spend_amount=0;
		$return_obj->budget=0;
		$return_obj->hour_over=0;
		if ($query->num_rows() > 0) {
			$g_list = $query->result();			
			foreach ($g_list as $key => $value) {
				$return_obj->hour_amount+=$value->hour;
				$return_obj->spend_amount+=$value->spend;
				if ($value->status=="over") {
					$return_obj->hour_over+=$value->hour;
				}else{
					$return_obj->budget+=$value->hour;
				}
				$g_list2[$value->time][]=$value;

			}
		}
		$return_obj->list=$g_list2;
		return $return_obj;
	}
	function group_assign_to_project($assign_obj){
		$g_list = array();
		foreach ($assign_obj->list as $key1 => $value1) {
			foreach ($value1 as $key => $value) {			
				if (isset($g_list[$value->project_id])) {
					$g_list[$value->project_id]->hour_amount+=$value->hour;
					$g_list[$value->project_id]->spend_amount+=$value->spend;
					if ($value->status=="over") {
						$g_list[$value->project_id]->hour_over+=$value->hour;
					}else{
						$g_list[$value->project_id]->budget+=$value->hour;
					}
					$g_list[$value->project_id]->assign_list[$value->time][]=$value;
				}else{
					$project=$this->m_project->get_project_by_id($value->project_id);
					$g_list[$value->project_id]=$project;
					$g_list[$value->project_id]->hour_amount=$value->hour;
					$g_list[$value->project_id]->spend_amount=$value->spend;
					$g_list[$value->project_id]->hour_over=0;
					$g_list[$value->project_id]->budget=0;
					if ($value->status=="over") {
						$g_list[$value->project_id]->hour_over=$value->hour;
					}else{
						$g_list[$value->project_id]->budget=$value->hour;
					}
					$g_list[$value->project_id]->assign_list[$value->time][]=$value;
				}
			}
			
		}
		return $g_list;
	}
	function cal_Available_Hours($start,$end,$usn){
		$numdays=$this->m_time->get_amount_day($start,$end);
		$num_weekend=$this->m_time->get_amount_weekend_day($start,$end);
		$user_leave=$this->m_hr->get_all_user_leave($start,$end,$usn);
		$holiday=$this->m_hr->get_all_holiday($start,$end,$usn);
		return ($numdays-$num_weekend-count($user_leave)-count($holiday))*8;
		//return $numdays." ".$num_weekend." ".count($user_leave)." ".count($holiday);
	}

	
}