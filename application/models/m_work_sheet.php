<?php
class M_work_sheet extends CI_Model {
 
  public function __construct(){
    parent::__construct();
  		$this->load->model("m_stringlib");  		
  		$this->load->model("m_hour_rate");  
  		$this->load->model("m_project");  
  		$this->load->model("m_Rsheet");  
  		$this->load->model("m_user");  
  		$this->load->model("m_position");  
	}	
	function generate_id()
	{
		$isuniq    = FALSE;
		$clam_id = '';
		do
		{
			$temp_id = $this->m_stringlib->uniqueAlphaNum10();
			$query = $this->db->get_where('work_sheet', array('id' => $temp_id));
			if ($query->num_rows() == 0)
			{
				$clam_id = $temp_id;
				$isuniq    = TRUE;
			}
		}
		while(!$isuniq);
	
		return $clam_id;
	}
	function generate_id_photo()
	{
		$isuniq    = FALSE;
		$clam_id = '';
		do
		{
			$temp_id = $this->m_stringlib->uniqueAlphaNum10();
			$query = $this->db->get_where('work_sheet_photo', array('id' => $temp_id));
			if ($query->num_rows() == 0)
			{
				$clam_id = $temp_id;
				$isuniq    = TRUE;
			}
		}
		while(!$isuniq);
	
		return $clam_id;
	}
	function get_advalid_task_type_by_project_id($project_id){
		$task_list=$this->m_Rsheet->get_all_r_sheet_by_project_id($project_id);
		$task_type_list = array();
		foreach ($task_list as $key => $value) {
			if (!isset($task_type_list[$value->type])) {
				$task_type_list[$value->type]=$this->m_hour_rate->get_hour_rate_by_id($value->type);
			}
		}
		return $task_type_list;
	}
	function add_work_sheet_assign ($data) {
		$this->db->insert('work_sheet_assign', $data);
	}
	function add_work_sheet_has_res ($data) {
		$this->db->insert('work_sheet_has_res', $data);
	}
	function add_work_sheet ($data) {
		$this->db->insert('work_sheet', $data);
	}
	function add_work_sheet_photo ($data) {
		$this->db->insert('work_sheet_photo', $data);
	}
	function get_work_sheet_by_id ($id) {
		$business = new stdClass();
		$query = $this->db->get_where('work_sheet', array('id' => $id));
		
		if ($query->num_rows() > 0) {
			$business = $query->result();
			$business = $business[0];
		}
		return $business;
	}
	function get_work_sheet_photo_by_id ($id) {
		$business = new stdClass();
		$query = $this->db->get_where('work_sheet_photo', array('id' => $id));
		
		if ($query->num_rows() > 0) {
			$business = $query->result();
			$business = $business[0];
		}
		return $business;
	}
	function get_work_sheet_assign_id ($id) {
		$business = new stdClass();
		$query = $this->db->get_where('work_sheet_assign', array('id' => $id));
		
		if ($query->num_rows() > 0) {
			$business = $query->result();
			$business = $business[0];
		}
		return $business;
	}
	function update_work_sheet($data, $id) {
		$this->db->where('id', $id);
		$this->db->update('work_sheet', $data);
	}
	function update_work_sheet_photo($data, $id) {
		$this->db->where('id', $id);
		$this->db->update('work_sheet_photo', $data);
	}
	function update_work_sheet_assign($data, $id) {
		$this->db->where('id', $id);
		$this->db->update('work_sheet_assign', $data);
	}
	function update_work_sheet_has_res($data, $id) {
		$this->db->where('id', $id);
		$this->db->update('work_sheet_has_res', $data);
	}
	function sort_work_sheet($project_id){
		$g_list = array();
		$this->db->where('project_id', $project_id);
		$this->db->order_by("sort_order", "asc");
		$query = $this->db->get('work_sheet');
		
		if ($query->num_rows() > 0) {
			$g_list = $query->result();
			$count=1;
			foreach ($g_list as $key => $value) {
				$dat = array('sort_order' => $count );
				$this->update_work_sheet($dat,$value->id);
				$count+=1;
			}
		}
	}
	function get_work_sheet_by_project_id($project_id){
		$g_list = array();
		$this->db->where('project_id', $project_id);
		$this->db->order_by("sort_order", "asc");
		$query = $this->db->get('work_sheet');
		
		if ($query->num_rows() > 0) {
			$g_list = $query->result();
		}
		return $g_list;
	}
	function get_work_sheet_photo_by_work_id($work_id){
		$g_list = array();
		$this->db->where('work_sheet_id', $work_id);
		$this->db->order_by("sort_order", "asc");
		$query = $this->db->get('work_sheet_photo');
		
		if ($query->num_rows() > 0) {
			$g_list = $query->result();
		}
		return $g_list;
	}
	function valid_delay_project($project_id){
		$project=$this->m_project->get_project_by_id($project_id);
		$g_list = array();
		$this->db->where('project_id', $project_id);
		$this->db->order_by("end", "desc");
		$query = $this->db->get('work_sheet');
		$work_sheet = new stdClass();
		if ($query->num_rows() > 0) {
			$work_sheet = $query->result();
			$work_sheet = $work_sheet[0];
			if ($work_sheet->end>$project->project_end) {
				$dat = array('status' => "Delay" );
				$this->m_project->update_project($dat,$project_id);
			}
		}

	}
	function get_last_end_time_work_sheet_by_project_id($project_id){
		$business = new stdClass();
		$this->db->where('project_id', $project_id);
		$this->db->order_by("end", "desc");
		$query = $this->db->get('work_sheet');
		
		if ($query->num_rows() > 0) {
			$business = $query->result();
			$business = $business[0];
		}
		return $business;
	}
	function get_first_start_time_work_sheet_by_project_id($project_id){
		$business = new stdClass();
		$this->db->where('project_id', $project_id);
		$this->db->where('start !=', 0);
		$this->db->order_by("start", "asc");
		$query = $this->db->get('work_sheet');
		
		if ($query->num_rows() > 0) {
			$business = $query->result();
			$business = $business[0];
		}
		return $business;
	}
	function get_work_sheet_has_res($id){
		$business = new stdClass();
		$this->db->where('id', $id);
		$query = $this->db->get('work_sheet_has_res');
		
		if ($query->num_rows() > 0) {
			$business = $query->result();
			$business = $business[0];
		}
		return $business;
	}
	function get_work_sheet_has_res_by_work_id($work_sheet_id){
		$g_list = array();
		$this->db->where('work_sheet_id', $work_sheet_id);
		$query = $this->db->get('work_sheet_has_res');
		
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $key => $value) {
				$g_list[$value->usn]=$value;
			}
		}
		return $g_list;
	}
	function get_work_sheet_has_res_by_project_id_and_usn($project_id,$usn){
		$g_list = array();
		$work_list=$this->get_work_sheet_by_project_id($project_id);
		foreach ($work_list as $key => $value) {
			$w_r=$this->get_work_sheet_has_res_by_work_id($value->id);
			foreach ($w_r as $wkey => $wvalue) {
				if ($wkey==$usn) {
					$g_list[$wvalue->work_sheet_id]=$wvalue;
				}
			}
		}
		return $g_list;
	}
	function get_work_sheet_has_res_by_work_id_and_usn($work_sheet_id,$usn){
		$business = new stdClass();
		$this->db->where('usn', $usn);
		$this->db->where('work_sheet_id', $work_sheet_id);
		$query = $this->db->get('work_sheet_has_res');
		
		if ($query->num_rows() > 0) {
			$business = $query->result();
			$business = $business[0];
		}
		return $business;
	}
	function get_work_sheet_assign_by_work_id($work_sheet_id,$time="no"){
		$g_list = array();
		$this->db->where('work_sheet_id', $work_sheet_id);
		if ($time!="no") {
			$this->db->where('time <=', $time);
		}
		$query = $this->db->get('work_sheet_assign');
		
		if ($query->num_rows() > 0) {
			$g_list = $query->result();
		}
		return $g_list;
	}
	function delete_work_sheet ($id) {
		$res_list=$this->get_work_sheet_assign_by_work_id($id);
		$photo_list=$this->get_work_sheet_photo_by_work_id($id);
		foreach ($res_list as $key => $value) {
			$this->delete_work_sheet_assign($value->id);
		}
		foreach ($photo_list as $key => $value) {
			$this->delete_work_sheet_photo($value->id);
		}
		$this->db->where('id', $id);
		$this->db->delete('work_sheet');
	}
	function delete_work_sheet_photo ($id) {
		$photo=$this->get_work_sheet_photo_by_id($id);
		@unlink("./media/work_sheet_comment_photo/" . $photo->filename);
		$this->db->where('id', $id);
		$this->db->delete('work_sheet_photo');
	}
	function delete_work_sheet_has_res ($id) {
		$heet_has_res=$this->get_work_sheet_has_res($id);
		$res_list=$this->get_res_assign_by_work_id_and_usn($heet_has_res->work_sheet_id,$heet_has_res->usn);
		$candel=true;
		foreach ($res_list->list as $akey => $avalue) {
			$assign_obj=$this->get_work_sheet_assign_id($avalue->id);
			if ($assign_obj->spend<=0) {
				$this->delete_work_sheet_assign($avalue->id);
			}else{
				$candel=false;
			}							
		}
		if ($candel) {
			$this->db->where('id', $id);
			$this->db->delete('work_sheet_has_res');
		}
		return $candel;
		
	}
	function delete_work_sheet_assign ($id) {
		$this->db->where('id', $id);
		$this->db->delete('work_sheet_assign');
	}
	function delete_work_sheet_assign_no_spend ($start,$end,$project_id) {
		$work=$this->get_work_sheet_by_project_id($project_id);
		foreach ($work as $key => $value) {
			$this->db->where('work_sheet_id', $value->id);
			//$this->db->where('time>=', $start);
			//$this->db->where('time<=', $end);
			$this->db->where('spend', 0);
			$this->db->delete('work_sheet_assign');
		}
		
	}
	function get_allow_list_by_t_type_and_project_id($type,$project_id,$usn="no"){
		$g_list = array();
		$g_list2 = array();
		$this->db->where('project_id', $project_id);
		$this->db->where('type', $type);
		$query = $this->db->get('r_sheet');
		if ($query->num_rows() > 0) {
			$g_list = $query->result();
			foreach ($g_list as $key => $value) {
				$allow_list=$this->m_Rsheet->get_allow_list_by_r_id($value->r_id,$usn);
				foreach ($allow_list as $key2 => $value2) {
					if (!isset($g_list2[$value2->resource_usn])) {
						$g_list2[$value2->resource_usn]= new stdClass();
						$g_list2[$value2->resource_usn]->approve_budget=$value->approve_budget;
						$g_list2[$value2->resource_usn]->user_dat=$this->m_user->get_user_by_login_name($value2->resource_usn);
					}
				}
			}
		}
		return $g_list2;
	}
	function get_res_assign_detail_by_work_id($work_sheet_id){
		$g_list = array();
		$g_list2 = array();
		$this->db->where('work_sheet_id', $work_sheet_id);
		$this->db->order_by("id", "asc");
		$query = $this->db->get('work_sheet_has_res');
		
		if ($query->num_rows() > 0) {
			$g_list = $query->result();
			foreach ($g_list as $key => $value) {
				if (!isset($g_list2[$value->usn])) {
					$g_list2[$value->usn]=$this->m_user->get_user_by_login_name($value->usn);
					$g_list2[$value->usn]->id=$value->id;
					$g_list2[$value->usn]->position=$this->m_position->get_position_by_id($g_list2[$value->usn]->position);
					$g_list2[$value->usn]->assign_list=$this->get_res_assign_by_work_id_and_usn($work_sheet_id,$value->usn);
				}
			}
		}
		return $g_list2;
	}
	function get_res_assign_detail_by_work_id_and_time($work_sheet_id,$time){
		$g_list = array();
		$g_list2 = array();
		$this->db->where('work_sheet_id', $work_sheet_id);
		$this->db->where('time', $time);
		$this->db->order_by("id", "asc");
		$query = $this->db->get('work_sheet_assign');
		
		if ($query->num_rows() > 0) {
			$g_list = $query->result();
			foreach ($g_list as $key => $value) {
				if (!isset($g_list2[$value->usn])) {
					$g_list2[$value->usn]=$value;
					$g_list2[$value->usn]->user_dat=$this->m_user->get_user_by_login_name($value->usn);
				}
			}
		}
		return $g_list2;
	}
	function get_res_assign_by_work_id_and_usn($work_sheet_id,$usn,$start_time="all",$end_time="all",$no_zero_assign=false){
		$g_list = array();
		$g_list2 = array();
		$return_obj = new stdClass();
		$this->db->where('work_sheet_id', $work_sheet_id);
		$this->db->where('usn', $usn);
		if ($no_zero_assign) {
			$this->db->where('hour !=', "0");
		}
		if ($start_time!="all") {
			$this->db->where('time >=', $start_time);
		}
		if ($end_time!="all") {
			$this->db->where('time <=', $end_time);
		}
		$this->db->order_by("time", "asc");
		$query = $this->db->get('work_sheet_assign');
		$return_obj->hour_amount=0;
		$return_obj->spend_amount=0;
		$return_obj->over_amount=0;
		if ($query->num_rows() > 0) {
			$g_list = $query->result();			
			foreach ($g_list as $key => $value) {
				if (!isset($g_list2[$value->time])) {
					$g_list2[$value->time]=$value;
					$return_obj->hour_amount+=$value->hour;
					$return_obj->spend_amount+=$value->spend;
					if ($value->status=="over") {
						$return_obj->over_amount+=1;
					}
				}
			}
		}
		$return_obj->list=$g_list2;
		return $return_obj;
	}
	function get_res_assign_by_usn_and_time($usn,$time){
		$g_list = array();
		$g_list2 = array();
		$return_obj = new stdClass();
		$this->db->where('usn', $usn);
		$this->db->where('time', $time);
		$query = $this->db->get('work_sheet_assign');
		$return_obj->hour_amount=0;
		if ($query->num_rows() > 0) {
			$g_list = $query->result();			
			foreach ($g_list as $key => $value) {
				$return_obj->hour_amount+=$value->hour;
			}
		}
		$return_obj->list=$g_list;
		return $return_obj;
	}
	function get_res_assign_by_usn_with_start_end($usn,$stime="all",$etime="all"){
		$g_list = array();
		$g_list2 = array();
		$return_obj = new stdClass();
		$this->db->where('usn', $usn);
		if ($stime!="all") {
			$this->db->where('time >=', $stime);
		}
		if ($etime!="all") {
			$this->db->where('time <=', $etime);
		}		
		$query = $this->db->get('work_sheet_assign');
		$return_obj->hour_amount=0;
		$return_obj->spend_amount=0;
		if ($query->num_rows() > 0) {
			$g_list = $query->result();			
			foreach ($g_list as $key => $value) {
				$return_obj->hour_amount+=$value->hour;
				$return_obj->spend_amount+=$value->spend;
					$g_list2[$value->time][]=$value;

			}
		}
		$return_obj->list=$g_list2;
		return $return_obj;
	}
	function get_sum_assign_hour_by_t_type_and_project_id($type,$project_id,$time="no"){
		$g_list = array();
		$sum = 0;
		$this->db->where('project_id', $project_id);
		$this->db->where('task_type', $type);
		$query = $this->db->get('work_sheet');
		if ($query->num_rows() > 0) {
			$g_list = $query->result();			
			foreach ($g_list as $key => $value) {
				$work_assign=$this->get_work_sheet_assign_by_work_id($value->id,$time);
				foreach ($work_assign as $key2 => $value2) {
					$sum+=$value2->hour;
				}
			}
		}
		return $sum;
	}
	function get_sum_assign_hour_by_work_id($work_id,$time="no"){
		$g_list = array();
		$sum = 0;
		$this->db->where('id', $work_id);
		$query = $this->db->get('work_sheet');
		if ($query->num_rows() > 0) {
			$g_list = $query->result();			
			foreach ($g_list as $key => $value) {
				$work_assign=$this->get_work_sheet_assign_by_work_id($value->id,$time);
				foreach ($work_assign as $key2 => $value2) {
					$sum+=$value2->hour;
				}
			}
		}
		return $sum;
	}
	function del_assign_usn_dup($work_sheet_id,$usn,$time){
		$this->db->where('work_sheet_id', $work_sheet_id);
		$this->db->where('usn', $usn);
		$this->db->where('time', $time);
		$query = $this->db->get('work_sheet_assign');
		if ($query->num_rows() > 0) {
			$g_list = $query->result();			
			foreach ($g_list as $key => $value) {
				$this->delete_work_sheet_assign($value->id);
			}
		}
	}
	function check_if_work_task_has_spend($work_sheet_id){
		$spend=false;
		$assign_list=$this->get_work_sheet_assign_by_work_id($work_sheet_id);
		foreach ($assign_list as $key => $value) {
			if ($value->spend!=0) {
				$spend=true;
				break;
			}
		}
		return $spend;
	}
	function check_if_assign_hour_out_peroid($work_sheet_id,$start,$end){
		$flag=false;
		$this->db->where('work_sheet_id', $work_sheet_id);
		$where = "(time>".$end." OR time<".$start.")";
        $this->db->where($where);
        $this->db->where('hour !=',0);
		$query = $this->db->get('work_sheet_assign');
		
		if ($query->num_rows() > 0) {
			//$g_list = $query->result();	
			//print_r($g_list);
			$flag=true;
		}
		return $flag;
	}

	

	//////////////////////////////// special case ////////////////////////////
	function check_res_assign_code_1001 ($time,$usn,$work_sheet_id) { // find any resource at that task and that time already assign if not in span then return null
		$business = new stdClass();
		$dat_return=new stdClass();
		$this->db->where('time', $time);
		$this->db->where('usn', $usn);
		$this->db->where('work_sheet_id', $work_sheet_id);
		$this->db->where('hour !=', "0");
		$query = $this->db->get('work_sheet_assign');
		$work_sheet=$this->get_work_sheet_by_id($work_sheet_id);
		if ($query->num_rows() > 0) {
			$business = $query->result();
			$dat_return = $business[0];
			
		}
		if ((int)$time<$work_sheet->start||(int)$time>$work_sheet->end) {
				//$dat_return->id="have"; comment out for make interfere can update all time
			}
		return $dat_return;
	}
	function get_advailable_hour_in($t_type,$project_id,$end_time){
        $hour=0;
        $sum_approve_budget=$this->m_Rsheet->get_sum_approve_budget_by_type_and_project_id($t_type,$project_id);
        $sum_assign=$this->m_work_sheet->get_sum_assign_hour_by_t_type_and_project_id($t_type,$project_id,$end_time);
        $hour=$sum_approve_budget-$sum_assign;
        return $hour;
    }
}