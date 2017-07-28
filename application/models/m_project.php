<?php
class M_project extends CI_Model {
 
  public function __construct(){
    parent::__construct();
  		$this->load->model("m_stringlib");  		
  		$this->load->model("m_pce");  
  		$this->load->model("m_oc");  
  		$this->load->model("m_Rsheet");  
  		$this->load->model("m_outsource"); 
  		$this->load->model("m_work_sheet");  
	}	
	function generate_id()
	{
		$isuniq    = FALSE;
		$clam_id = '';
		do
		{
			$temp_id = $this->m_stringlib->uniqueAlphaNum10();
			$query = $this->db->get_where('project', array('project_id' => $temp_id));
			if ($query->num_rows() == 0)
			{
				$clam_id = $temp_id;
				$isuniq    = TRUE;
			}
		}
		while(!$isuniq);
	
		return $clam_id;
	}
	function generate_project_attachment_id()
	{
		$isuniq    = FALSE;
		$clam_id = '';
		do
		{
			$temp_id = $this->m_stringlib->uniqueAlphaNum10();
			$query = $this->db->get_where('project_attachment', array('id' => $temp_id));
			if ($query->num_rows() == 0)
			{
				$clam_id = $temp_id;
				$isuniq    = TRUE;
			}
		}
		while(!$isuniq);
	
		return $clam_id;
	}
	function delete_project ($project_id) {
		$this->db->where('project_id', $project_id);
		$this->db->delete('project');
	}
	function delete_project_attachment ($id) {
		$file=$this->get_project_attachment_by_id($id);
		@unlink("./media/project_attachment/" . $file->filename);
		$this->db->where('id', $id);
		$this->db->delete('project_attachment');
	}
	function add_project ($data) {
		$this->db->insert('project', $data);
	}
	function add_project_attachment ($data) {
		$this->db->insert('project_attachment', $data);
	}
	function update_project($data, $project_id) {
		$this->db->where('project_id', $project_id);
		$this->db->update('project', $data);
	}
	function update_project_attachment($data, $id) {
		$this->db->where('id', $id);
		$this->db->update('project_attachment', $data);
	}
	function get_all_project($no_archive=false,$only_archive=false,$filter = array()){
		$g_list = array();
		$this->db->order_by("project_start", "desc");
		if ($no_archive) {
			$this->db->where('status !=', "Archive");
		}
		if ($only_archive) {
			$this->db->where('status', "Archive");
		}
		if (isset($filter['start_date'])&&isset($filter['end_date'])) {
					$where = "((project_start>=".$filter['start_date']." AND project_start<=".$filter['end_date'].") OR (project_end>=".$filter['start_date']." AND project_end<=".$filter['end_date'].") OR (project_start<=".$filter['start_date']." AND project_end>=".$filter['end_date']."))";
					$this->db->where($where);
			}
		if (isset($filter['project_cs'])&&$filter['project_cs']!="all") {
			$this->db->where('project_cs', $filter['project_cs']);
		}
		$query = $this->db->get('project');
		
		if ($query->num_rows() > 0) {
			$g_list = $query->result();
			foreach ($g_list as $key => $value) {
				$g_list[$key]->pass=$this->check_all_base_approve($value->project_id);
				$g_list[$key]->base_approve_stat=$this->get_stat_base_approve($value->project_id);
				if ($g_list[$key]->status=="Drafing") {
					$g_list[$key]->pass=false;
				}
			}
		}
		return $g_list;

	}
	function get_all_project_attachment($project_id,$type){
		$g_list = array();
		$this->db->order_by("sort_order", "asc");
		$this->db->where('type', $type);
		$this->db->where('project_id', $project_id);
		$query = $this->db->get('project_attachment');
		
		if ($query->num_rows() > 0) {
			$g_list = $query->result();
		}
		return $g_list;

	}
	function get_project_attachment_by_id($id){
		$business = new stdClass();
		$this->db->where('id', $id);
		$query = $this->db->get('project_attachment');
		
		if ($query->num_rows() > 0) {
			$business = $query->result();
			$business = $business[0];
		}
		return $business;

	}

	function get_project_by_id ($project_id) {
		$business = new stdClass();
		$query = $this->db->get_where('project', array('project_id' => $project_id));
		
		if ($query->num_rows() > 0) {
			$business = $query->result();
			$business = $business[0];
			$business->pass=$this->check_all_base_approve($business->project_id);
			$business->base_approve_stat=$this->get_stat_base_approve($business->project_id);
		}
		return $business;
	}

	function get_project_by_id_status ($project_id,$status="proposing",$filter = array()) {
		$business = new stdClass();
		if ($status=="proposing") {
			$where = "(status='proposing' OR status='Revise' OR status='Delay')";
        	$this->db->where($where);
		}else{
			$this->db->where('status', $status);
		}
		if (isset($filter['start_date'])&&isset($filter['end_date'])) {
					$where = "((project_start>=".$filter['start_date']." AND project_start<=".$filter['end_date'].") OR (project_end>=".$filter['start_date']." AND project_end<=".$filter['end_date'].") OR (project_start<=".$filter['start_date']." AND project_end>=".$filter['end_date']."))";
					$this->db->where($where);
			}
		if (isset($filter['project_cs'])&&$filter['project_cs']!="all") {
			$this->db->where('project_cs', $filter['project_cs']);
		}			
        $this->db->where('project_id', $project_id);
		$query = $this->db->get('project');
		
		if ($query->num_rows() > 0) {
			$business = $query->result();
			$business = $business[0];
			$business->pass=$this->check_all_base_approve($business->project_id);
			$business->base_approve_stat=$this->get_stat_base_approve($business->project_id);
		}
		return $business;
	}
	function get_all_project_by_status($status="all",$bill_status="all",$start_time="no",$end_time="no",$bu="all",$sort_order="desc",$time_col="project_start",$account_unit_id="all"){
		$g_list = array();
		$g_list2 = array();
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

		if ($bill_status!="all") {
			if ($bill_status="no_fully") {
				$this->db->where('status_bill !=', "Fully Billed");
			}else{
				$this->db->where('status_bill', $bill_status);
			}
		}
		if ($bu!="all") {
			$this->db->where('business_unit_id', $bu);
		}
		if ($account_unit_id!="all") {
			$this->db->where('account_unit_id', $account_unit_id);
		}
		if ($start_time!="no") {
				$this->db->where($time_col.' >=', $start_time);
		}
		if ($end_time!="no") {
				$this->db->where($time_col.' <=', $end_time);
		}
		$this->db->order_by("project_start", $sort_order);
		$query = $this->db->get('project');
		
		if ($query->num_rows() > 0) {
			$g_list = $query->result();
			foreach ($g_list as $key => $value) {
				$g_list2[$value->project_id]=$value;
				$g_list2[$value->project_id]->pass=$this->check_all_base_approve($value->project_id);
				$g_list2[$value->project_id]->base_approve_stat=$this->get_stat_base_approve($value->project_id);
				if ($g_list2[$value->project_id]->status=="Drafing") {
					$g_list2[$value->project_id]->pass=false;
				}
			}
		}
		return $g_list2;
	}
	function get_project_by_cs($cs_usn,$status="all",$mulit_usn_where="no",$no_archive=true,$filter = array()){
		$g_list = array();
		if ($status!="all") {
			$this->db->where('status', $status);
		}
		if ($no_archive) {
			$this->db->where('status !=', "Archive");
		}
		// else{
		// 	$this->db->where('status !=', "Done");
		// }
		if ($mulit_usn_where=="no") {
			$this->db->where('project_cs', $cs_usn);
		}else{
        	$this->db->where($mulit_usn_where);
        	//echo $mulit_usn_where;
		}
		if (isset($filter['start_date'])&&isset($filter['end_date'])) {
					$where = "((project_start>=".$filter['start_date']." AND project_start<=".$filter['end_date'].") OR (project_end>=".$filter['start_date']." AND project_end<=".$filter['end_date'].") OR (project_start<=".$filter['start_date']." AND project_end>=".$filter['end_date']."))";
					$this->db->where($where);
			}
		if (isset($filter['project_cs'])&&$filter['project_cs']!="all") {
			$this->db->where('project_cs', $filter['project_cs']);
		}
		$this->db->order_by("project_start", "desc");
		$query = $this->db->get('project');
		
		if ($query->num_rows() > 0) {
			$g_list = $query->result();
			foreach ($g_list as $key => $value) {
				$g_list[$key]->pass=$this->check_all_base_approve($value->project_id);
				$g_list[$key]->base_approve_stat=$this->get_stat_base_approve($value->project_id);
				if ($g_list[$key]->status=="Drafing") {
					$g_list[$key]->pass=false;
				}
			}
		}
		return $g_list;
	}
	function get_stat_base_approve($project_id){
		$not_pass=false;
		$re_arr = array(
			'csd' => true, 
			'hod' => true, 
			'fc' => true, 
			'fc_oc' => true,
			);
		$pce=$this->m_pce->get_all_pce_by_project_id($project_id);
		$oc=$this->m_oc->get_all_oc_by_project_id($project_id,false,true);
		if (count($pce)==0) {
			$re_arr = array(
			'csd' => false, 
			'hod' => false, 
			'fc' => false, 
			'fc_oc' => false,
			);
		}
		if (count($oc)==0) {
			$re_arr['fc_oc']=false;
		}
		foreach ($oc as $key => $value) {
			if ($value->status=="ns") {
				$re_arr['fc_oc']=false;				
			}else if ($value->status=="n") {
				$re_arr['fc_oc']='reject';
				break;
			}
		}
		foreach ($pce as $key => $value) {
			if ($value->csd_sign_status=="ns") {
				$re_arr['csd']=false;
			}else if($value->csd_sign_status=="n"){
				$re_arr['csd']="reject";
			}
			if ($value->fc_sign_status=="ns") {
				$re_arr['fc']=false;
			}else if($value->fc_sign_status=="n"){
				$re_arr['fc']="reject";
			}
				foreach ($value->hod_list as $o_key => $o_value) {
					if ($o_value->approve=="ns") {
						$re_arr['hod']=false;
					}else if($o_value->approve=="n"){
						$not_pass=true;
						
					}
				}
			if ($not_pass) {
				$re_arr['hod']="reject";
			}	
		}
		return $re_arr;
	}
	function check_all_base_approve($project_id){
		$pass=true;
		$pce=$this->m_pce->get_all_pce_by_project_id($project_id);
		if (count($pce)==0) {
			$pass=false;
		}
		foreach ($pce as $key => $value) {
			if ($value->csd_sign_status=="ns"||$value->csd_sign_status=="n") {
				$pass=false;
			}
			if ($value->fc_sign_status=="ns"||$value->fc_sign_status=="n") {
				$pass=false;
			}
			if ($pass) {
				foreach ($value->hod_list as $o_key => $o_value) {
					if ($o_value->approve=="n"||$o_value->approve=="ns") {
						$pass=false;
					}
				}
			}
		}
		return $pass;
	}
	function check_csd_base_approve($project_id){
		$pass=true;
		$pce=$this->m_pce->get_all_pce_by_project_id($project_id);
		foreach ($pce as $key => $value) {
			if ($value->csd_sign_status=="ns"||$value->csd_sign_status=="n") {
				$pass=false;
			}
		}
		return $pass;
	}

	function check_csd_and_hod_base_approve($project_id){
		$pass=true;
		$pce=$this->m_pce->get_all_pce_by_project_id($project_id);
		foreach ($pce as $key => $value) {
			if ($value->csd_sign_status=="ns"||$value->csd_sign_status=="n") {
				$pass=false;
			}
			if ($pass) {
				foreach ($value->hod_list as $o_key => $o_value) {
					if ($o_value->approve=="n"||$o_value->approve=="ns") {
						$pass=false;
					}
				}
			}
		}
		return $pass;
	}

	function check_base_fc_not_sign($project_id){
		$pass=false;
		$pce=$this->m_oc->get_all_oc_by_project_id($project_id);
		foreach ($pce as $key => $value) {
			
			if ($value->status=="ns"||$value->status=="n") {
				$pass=true;
			}	
		}
		if (count($pce)<=0) {
			$pass=true;
		}
		return $pass;
	}

	function get_project_by_ready_assign_res($get_wip=true,$get_delay=true,$hod_usn="no",$complete_assign="all",$filter = array()){
		$g_list = array();
		$check = array();
		$where = "(status='proposing' OR status='Revise'";
        
		if ($get_wip) {
			//$this->db->or_where('status', "WIP");
			$where.=" OR status='WIP'";
		}		
		if ($get_delay) {
			//$this->db->or_where('status', "delay");
			$where.=" OR status='delay'";
		}		
		$where.=")";
		$this->db->where($where);		
		if (isset($filter['start_date'])&&isset($filter['end_date'])) {
					$where = "((project_start>=".$filter['start_date']." AND project_start<=".$filter['end_date'].") OR (project_end>=".$filter['start_date']." AND project_end<=".$filter['end_date'].") OR (project_start<=".$filter['start_date']." AND project_end>=".$filter['end_date']."))";
					$this->db->where($where);
			}
		if (isset($filter['project_cs'])&&$filter['project_cs']!="all") {
			$this->db->where('project_cs', $filter['project_cs']);
		}	
		$query = $this->db->get('project');
		if ($query->num_rows() > 0) {
			$g_list = $query->result();
			foreach ($g_list as $key => $value) {
				if (!isset($check[$value->project_id])) {
					$check_base = $this->check_all_base_approve($value->project_id);
					if($check_base){
						$check_have_hod=true;
						$check_base_fc_sign = $this->check_base_fc_not_sign($value->project_id);
						if ($hod_usn!="no") {
							$check_have_hod=$this->check_project_have_hod($value->project_id,$hod_usn,$complete_assign);
						}
						if(!$check_base_fc_sign&&$check_have_hod){
							$check[$value->project_id]=$value;
							$check[$value->project_id]->pass=$check_base;
							$check[$value->project_id]->base_approve_stat=$this->get_stat_base_approve($value->project_id);
							if ($check[$value->project_id]->status=="Drafing") {
								$check[$value->project_id]->pass=false;
							}
						}
					}
				}
			}
		}
		return $check;
	}
	function get_pre_oc_assign($hod_usn="no",$complete_assign="all",$filter = array()){
		$g_list = array();
		$check = array();
		$where = "(status='proposing' OR status='Revise' OR status='WIP' OR status='delay')";
		$this->db->where($where);		
		if (isset($filter['start_date'])&&isset($filter['end_date'])) {
					$where = "((project_start>=".$filter['start_date']." AND project_start<=".$filter['end_date'].") OR (project_end>=".$filter['start_date']." AND project_end<=".$filter['end_date'].") OR (project_start<=".$filter['start_date']." AND project_end>=".$filter['end_date']."))";
					$this->db->where($where);
			}
		if (isset($filter['project_cs'])&&$filter['project_cs']!="all") {
			$this->db->where('project_cs', $filter['project_cs']);
		}	
		$query = $this->db->get('project');
		if ($query->num_rows() > 0) {
			$g_list = $query->result();
			foreach ($g_list as $key => $value) {
				if (!isset($check[$value->project_id])) {
					//$check_base = $this->check_all_base_approve($value->project_id);
					//if(!$check_base){
						$check_have_hod=true;
						//$check_base_fc_sign = $this->check_base_fc_not_sign($value->project_id);
						if ($hod_usn!="no") {
							$check_have_hod=$this->check_project_have_hod($value->project_id,$hod_usn,$complete_assign);
						}
						$check_base_fc_sign = $this->check_base_fc_not_sign($value->project_id);
						if($check_have_hod&&$check_base_fc_sign){//if($check_base_fc_sign&&$check_have_hod){
							$check[$value->project_id]=$value;
							$check[$value->project_id]->pass=$check_base;
							$check[$value->project_id]->base_approve_stat=$this->get_stat_base_approve($value->project_id);
							if ($check[$value->project_id]->status=="Drafing") {
								$check[$value->project_id]->pass=false;
							}
						}
					//}
				}
			}
		}
		return $check;
	}
	function check_project_have_hod($project_id,$usn,$complete_assign="all"){
		$have_hod=false;		
		$pce_list=$this->m_pce->get_all_pce_by_project_id($project_id);
		foreach ($pce_list as $key => $value) {
			foreach ($value->hod_list as $key2 => $value2) {
				if ($complete_assign!="all") {
					if ($value2->hod_usn==$usn&&$value2->complete_assign==$complete_assign) {
						$have_hod=true;
						break;
					}
				}else{
					if ($value2->hod_usn==$usn) {
						$have_hod=true;
						break;
					}
				}
				
			}
		}
		return $have_hod;
	}
	function get_project_by_all_base_sign(){
		$g_list = array();
		$check = array();
		$this->db->where('status !=', "y");
		$query = $this->db->get('oc_doc');

		if ($query->num_rows() > 0) {
			$g_list = $query->result();
			
			foreach ($g_list as $key => $value) {
				if (!isset($check[$value->project_id])) {
					$check_base = $this->check_all_base_approve($value->project_id);
					//print_r($value->project_id);
					if($check_base){
						$check_base_fc_sign = $this->check_base_fc_not_sign($value->project_id);
						if($check_base_fc_sign){
							$set_val=$this->get_project_by_id_status($value->project_id);
							$bill_ready1=$this->check_biling_date_ready_by_project($value->project_id);
							$bill_ready2=$this->check_pay_date_ready_by_project($value->project_id);
							if (isset($set_val->project_name)&&$bill_ready1&&$bill_ready2) {
									$check[$value->project_id]=$set_val;
									$check[$set_val->project_id]->pass=$this->check_all_base_approve($set_val->project_id);
									$check[$set_val->project_id]->base_approve_stat=$this->get_stat_base_approve($set_val->project_id);
									if ($check[$set_val->project_id]->status=="Drafing") {
										$check[$set_val->project_id]->pass=false;
									}
							}
						}
					}
				}
			}
		}
		return $check;

	}


	function get_project_by_csd_not_sign(){
		$g_list = array();
		$check = array();
		$query = $this->db->get_where('pce_doc', array('csd_sign_status !=' => 'y','rewrite_stat' => 'n'));
		
		if ($query->num_rows() > 0) {
			$g_list = $query->result();

			foreach ($g_list as $key => $value) {
				
				if (!isset($check[$value->project_id])) {
					$set_val=$this->get_project_by_id_status($value->project_id);
					if (isset($set_val->project_name)) {
						$check[$value->project_id]=$set_val;
						$check[$value->project_id]->pass=$this->check_all_base_approve($value->project_id);
						$check[$value->project_id]->base_approve_stat=$this->get_stat_base_approve($value->project_id);
						if ($check[$value->project_id]->status=="Drafing") {
							$check[$value->project_id]->pass=false;
						}
					}
				}

			}
		
		}
		
		return $check;
	}


	function get_project_by_fc_not_sign(){
		$g_list = array();
		$check = array();
		$query = $this->db->get_where('pce_doc', array('fc_sign_status !=' => 'y','rewrite_stat' => 'n'));
		
		if ($query->num_rows() > 0) {
			$g_list = $query->result();
			foreach ($g_list as $key => $value) {
				if (!isset($check[$value->project_id])) {
					if ($this->check_csd_and_hod_base_approve($value->project_id)) {	
						$set_val=$this->get_project_by_id_status($value->project_id);
						if (isset($set_val->project_name)) {
								$check[$value->project_id]=$set_val;
								$check[$set_val->project_id]->pass=$this->check_all_base_approve($set_val->project_id);
								$check[$set_val->project_id]->base_approve_stat=$this->get_stat_base_approve($set_val->project_id);
								if ($check[$set_val->project_id]->status=="Drafing") {
									$check[$set_val->project_id]->pass=false;
								}
						}
					}
				}
			}
		}
		return $check;

	}

	function get_project_by_hod_not_sign($usn,$filter){
		$g_list = array();
		$check_pce = array();
		$check = array();
		$query = $this->db->get_where('hod_approve_pce', array('approve !=' => 'y','hod_usn' => $usn));
		
		if ($query->num_rows() > 0) {
			$g_list = $query->result();
			foreach ($g_list as $key => $value) {
				if (!isset($check_pce[$value->pce_id])) {
					$check_pce[$value->pce_id]=$this->m_pce->get_pce_by_id($value->pce_id);
					$set = $check_pce[$value->pce_id]->project_id;
					//print_r($check_pce[$value->pce_id]->project_id);

					if (!isset($check[$set])) {
						if ($this->check_csd_base_approve($set)) {							
							$set_val=$this->get_project_by_id_status($set,"proposing",$filter);
							if (isset($set_val->project_name)) {
								$check[$set]=$set_val;
								$check[$set_val->project_id]->pass=$this->check_all_base_approve($set_val->project_id);
								$check[$set_val->project_id]->base_approve_stat=$this->get_stat_base_approve($set_val->project_id);
								if ($check[$set_val->project_id]->status=="Drafing") {
									$check[$set_val->project_id]->pass=false;
								}
							}
						}
						
					}
				}
			}
		}
		return $check;

	}
	function check_hod_assign_resource($project_id,$complete_assign=true){
		$return1=true;
		$return2=false;
		$project=$this->get_project_by_id($project_id);
		if ($project->status=="Drafing") {
			$return1=false;
		}
		//$check_base=$this->check_all_base_approve($project_id);
		//$check_base_fc_sign = $this->check_base_fc_not_sign($project_id);
		//$check_base_fc_sign=(!$check_base_fc_sign);
		//if ($check_base_fc_sign&&$check_base) {
			if ($complete_assign) {
				$flag_complete=true;
				$pce_list=$this->m_pce->get_all_pce_by_project_id($project_id);
				foreach ($pce_list as $key => $value) {
					foreach ($value->hod_list as $key2 => $value2) {
						if ($value2->complete_assign=="n") {
							$flag_complete=false;
							break;
						}						
					}
				}
				$return2=$flag_complete;
			}else{			
				$r_sheet_list=$this->m_Rsheet->get_all_r_sheet_by_project_id($project_id);
				foreach ($r_sheet_list as $key => $value) {
					$sum=$this->m_Rsheet->check_hod_assign_res_by_r_id($value->r_id);
					if ($sum) {
						$return2=true;
					}
				}
			}

		//}else{
		//	$return1=false;
		//}
		//$return3=$this->m_pce->check_all_pce_have_oc($project_id);

		return $return1&&$return2;//&&$return3;
	}
	function get_sum_budget_by_project_id($project_id){
		$sumbudget=0;
		$r_sheet=$this->m_Rsheet->get_all_r_sheet_by_project_id($project_id);
		foreach ($r_sheet as $key => $value) {
			$sumbudget+=(int)$value->approve_budget;
		}
		return $sumbudget;
	}
	function get_sum_allocate_budget_by_project_id($project_id){
		$re_dat['sum_spend']=0;
		$re_dat['sum_all']=0;
		$work_sheet=$this->m_work_sheet->get_work_sheet_by_project_id($project_id);
		foreach ($work_sheet as $key => $value) {
			$w_assign=$this->m_work_sheet->get_work_sheet_assign_by_work_id($value->id);
			foreach ($w_assign as $a_key => $a_value) {
				$re_dat['sum_all']+=$a_value->hour;
				$re_dat['sum_spend']+=$a_value->spend;
			}
		}
		return $re_dat;
	}
	function get_sum_outsource_bill_by_project_id($project_id){
		$re_dat['sum_paid']=0;
		$re_dat['sum_all']=0;
		$re_dat['sum_all_raw']=0;
		$re_dat['payment'] = array();
		$pce=$this->m_pce->get_all_pce_by_project_id($project_id);
		foreach ($pce as $key => $value) {
			foreach ($value->outsource as $o_key => $o_value) {
				$ins=true;
				$re_dat['payment'][$o_value->id]=null;
				$outsource_bill=$this->m_outsource->get_outsource_bill_by_out_id($o_value->id);
				$re_dat['sum_all_raw']+=$o_value->qt_cost;
				foreach ($outsource_bill as $b_key => $b_value) {
					if ($b_value->paid=="y") {
						$re_dat['sum_paid']+=(int)$b_value->paid_amount;
					}else if($b_value->paid=="n"&&$ins){
						$re_dat['payment'][$o_value->id]=$b_value;
                        $ins=false;
					}
					$re_dat['sum_all']+=(int)$b_value->amount;
				}
			}
		}
		if ($re_dat['sum_all']==0) {
			$re_dat['sum_all']=$re_dat['sum_all_raw'];
		}
		return $re_dat;
	}
	function check_biling_date_ready_by_project($project_id){
		$pass=true;
		$oc_list=$this->m_oc->get_all_oc_by_project_id($project_id);
		foreach ($oc_list as $key => $value) {
			$oc_bill=$this->m_oc->get_oc_bill_by_oc_id($value->id);
			$bill_amount=0;
			foreach ($oc_bill as $bkey => $bvalue) {
				$bill_amount+=$bvalue->amount;
			}
			if ($bill_amount<$value->oc_amount) {
				$pass=false;
			}
		}
		return $pass;
	}

	function check_biling_date_ready_by_oc($oc_id){
		$pass=true;
		$oc_dat=$this->m_oc->get_oc_by_id($oc_id);
			$bill_amount=0;
			foreach ($oc_dat->oc_bill as $bkey => $bvalue) {
				$bill_amount+=$bvalue->amount;
			}
			if ($bill_amount<$oc_dat->oc_amount) {
				$pass=false;
			}
		return $pass;
	}
	function check_biling_paid_by_oc($oc_id){
		$pass=true;
		$oc_dat=$this->m_oc->get_oc_by_id($oc_id);
			$bill_amount=0;
			foreach ($oc_dat->oc_bill as $bkey => $bvalue) {
				$bill_amount+=$bvalue->paid_amount;
				if ($bvalue->collected=="n") {
					$pass=false;
				}
			}
			if ($bill_amount<$oc_dat->oc_amount) {
				$pass=false;
			}
		return $pass;
	}
	function check_pay_date_ready_by_project($project_id){
		$pass=true;
		$pce_list=$this->m_pce->get_all_pce_by_project_id($project_id);
		foreach ($pce_list as $key => $value) {
			
			foreach ($value->outsource as $bkey => $bvalue) {
				$bill_amount=0;
				$bill_list=$this->m_outsource->get_outsource_bill_by_out_id($bvalue->id);
				foreach ($bill_list as $dkey => $dvalue) {
					$bill_amount+=$dvalue->amount;
				}
				if ($bill_amount!=$bvalue->qt_cost) {
					$pass=false;
				}
			}
			
		}
		return $pass;
	}
	function check_pay_date_ready_by_out_id($out_id){
		$pass=true;
		$outsource=$this->m_outsource->get_outsource_by_id($out_id);
				$bill_amount=0;
				$bill_list=$this->m_outsource->get_outsource_bill_by_out_id($out_id);
				foreach ($bill_list as $dkey => $dvalue) {
					$bill_amount+=$dvalue->amount;
				}
				if ($bill_amount!=$outsource->qt_cost) {
					$pass=false;
				}
		return $pass;
	}

	function check_all_pay_date_paid_by_project($project_id){
		$pass=true;
		$pce_list=$this->m_pce->get_all_pce_by_project_id($project_id);
		foreach ($pce_list as $key => $value) {
			
			foreach ($value->outsource as $bkey => $bvalue) {
				$bill_amount=0;
				$bill_list=$this->m_outsource->get_outsource_bill_by_out_id($bvalue->id);
				foreach ($bill_list as $dkey => $dvalue) {
					$bill_amount+=$dvalue->paid_amount;
					if ($dvalue->paid=="n") {
						$pass=false;
					}
				}
				if ($bill_amount>$bvalue->qt_cost) {
					$pass=false;
				}
			}
			
		}
		return $pass;
	}
}