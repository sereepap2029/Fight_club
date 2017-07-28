<?php
class M_pce extends CI_Model {
 
  public function __construct(){
    parent::__construct();
  		$this->load->model("m_stringlib"); 
  		$this->load->model("m_user");  	
  		$this->load->model("m_outsource");  		
  		$this->load->model("m_oc");  	
  		$this->load->model("m_Rsheet");  	
  		$this->load->model("m_project");  	
  		$this->load->model("m_hour_rate"); 
	}	
	function generate_id()
	{
		$isuniq    = FALSE;
		$clam_id = '';
		do
		{
			$temp_id = $this->m_stringlib->uniqueAlphaNum10();
			$query = $this->db->get_where('pce_doc', array('id' => $temp_id));
			if ($query->num_rows() == 0)
			{
				$clam_id = $temp_id;
				$isuniq    = TRUE;
			}
		}
		while(!$isuniq);
	
		return $clam_id;
	}
	function delete_pce ($id) {
		$pce=$this->get_pce_by_id($id);
		if (isset($pce->id)) {
			$rewrite_pce=$this->get_pce_rewrite_child_by_id($id);
			if (isset($rewrite_pce->id)) {
				$this->delete_pce($rewrite_pce->id);
			}
			@unlink("./media/real_pdf/" . $pce->filename);
			foreach ($pce->outsource as $key => $value) {
				$this->m_outsource->delete_outsource($value->id);
			}
			foreach ($pce->oc_list as $key => $value) {
				$this->m_oc->delete_oc($value->id);
			}
			$this->delete_hod_approve_by_pce_id($id);
			$this->db->where('id', $id);
			$this->db->delete('pce_doc');
		}
		
	}
	function delete_hod_approve_by_pce_id($pce_id) {
		$this->db->where('pce_id', $pce_id);
		$this->db->delete('hod_approve_pce');
	}
	function delete_hod_approve_by_id($id) {
		$this->db->where('id', $id);
		$this->db->delete('hod_approve_pce');
	}
	function add_pce ($data) {
		$this->db->insert('pce_doc', $data);
	}
	function add_hod_approve_pce ($data) {
		$this->db->insert('hod_approve_pce', $data);
	}

	
	function get_hod_approve_pce ($pce_id,$usn) {
		$business = new stdClass();
		$query = $this->db->get_where('hod_approve_pce', array('pce_id' => $pce_id,'hod_usn' => $usn));
		
		if ($query->num_rows() > 0) {
			$business = $query->result();
			$business = $business[0];
		}
		return $business;
	}
	function get_hod_approve_by_pce_id ($pce_id) {
		$g_list = array();
		$g_list2 = array();
		$query = $this->db->get_where('hod_approve_pce', array('pce_id' => $pce_id));
		
		if ($query->num_rows() > 0) {
			$g_list = $query->result();
			foreach ($g_list as $key => $value) {
				$g_list2[$value->hod_usn]=$value;
			}
		}
		return $g_list2;
	}
	function get_all_hod_approve_pce(){
		$g_list = array();
		$g_list2 = array();
		$query = $this->db->get('hod_approve_pce');
		
		if ($query->num_rows() > 0) {
			$g_list = $query->result();
			foreach ($g_list as $key => $value) {
				$g_list2[$value->hod_usn]=$value;
			}
		}
		return $g_list2;
	}
	function update_hod_approve($data, $id) {
		$this->db->where('id', $id);
		$this->db->update('hod_approve_pce', $data);
	}
	function update_hod_approve_by_pce_id($data, $id) {
		$this->db->where('pce_id', $id);
		$this->db->update('hod_approve_pce', $data);
	}
	function update_pce($data, $id) {
		$this->db->where('id', $id);
		$this->db->update('pce_doc', $data);
	}
	function get_all_pce_by_project_id($project_id,$get_rewrite=false,$get_bill_paid=false,$only_pce=false){
		$g_list = array();
		$this->db->where('project_id', $project_id);
		$this->db->order_by("pce_no", "asc");
		if (!$get_rewrite) {
			$this->db->where('rewrite_stat', "n");
		}
		
		$query = $this->db->get('pce_doc');
		
		if ($query->num_rows() > 0) {
			$g_list = $query->result();
			foreach ($g_list as $key => $value) {
				$csd_sign=$this->m_user->get_user_by_login_name($g_list[$key]->csd_sign);
				$fc_sign=$this->m_user->get_user_by_login_name($g_list[$key]->fc_sign);
				if (isset($csd_sign->firstname)) {
					$g_list[$key]->csd_sign_name=$csd_sign->firstname." ".$csd_sign->lastname;
				}else{
					$g_list[$key]->csd_sign_name="not sign";
				}
				if (isset($fc_sign->firstname)) {
					$g_list[$key]->fc_sign_name=$fc_sign->firstname." ".$fc_sign->lastname;
				}else{
					$g_list[$key]->fc_sign_name="not sign";
				}
				if (!$only_pce) {
					$g_list[$key]->outsource=$this->m_outsource->get_all_outsource_by_pce_id($value->id,$get_bill_paid);
					$g_list[$key]->hod_list=$this->get_hod_approve_by_pce_id($value->id);
				}
				
			}
		}
		return $g_list;

	}

	function get_all_pce_rewrite_by_id($pce_id){
		$g_list = array();		
		$pce_dat=$this->get_pce_by_id($pce_id);
		if (isset($pce_dat->id)) {
			$rewrite=$this->get_pce_rewrite_child_by_id($pce_id);
			if (isset($rewrite->id)) {
				$g_list=$this->get_all_pce_rewrite_by_id($rewrite->id);
				
			}
		}
		$g_list[$pce_id]=$pce_dat;
		return $g_list;

	}

	function get_pce_by_id ($id,$get_out=true) {
		$business = new stdClass();
		$query = $this->db->get_where('pce_doc', array('id' => $id));
		
		if ($query->num_rows() > 0) {
			$business = $query->result();
			$business = $business[0];
			if ($get_out) {
				$business->outsource=$this->m_outsource->get_all_outsource_by_pce_id($business->id);
				$business->hod_list=$this->get_hod_approve_by_pce_id($business->id);
				$business->oc_list=$this->m_oc->get_all_oc_by_pce_id($business->id,false);
			}
			
		}
		return $business;
	}
	function get_pce_rewrite_child_by_id ($id,$get_out=true) {
		$business = new stdClass();
		$query = $this->db->get_where('pce_doc', array('rewrite_by' => $id));
		
		if ($query->num_rows() > 0) {
			$business = $query->result();
			$business = $business[0];
			if ($get_out) {
				$business->outsource=$this->m_outsource->get_all_outsource_by_pce_id($business->id);
				$business->hod_list=$this->get_hod_approve_by_pce_id($business->id);
			}
			
		}
		return $business;
	}
	function handle_file_pce($filename,$pce_id){
		$filename = $filename;
        $ext = explode(".", $filename);
        $new_ext = $ext[count($ext) - 1];
        $new_filename = $pce_id ."_".time()."." . $new_ext;
        $file = './media/temp/' . $filename;
        $newfile = './media/real_pdf/' . $new_filename;
                    
        if (!copy($file, $newfile)) {
        	//echo "failed to copy $file...\n" . $file . " to " . $newfile . "  and  ";
            $new_filename="error";            
            @unlink("./media/temp/" . $filename);
        } 
        else {
            @unlink("./media/temp/" . $filename);
        }
        return $new_filename;
	}

	function check_all_pce_have_oc($project_id){
		$pass=true;
		$pce=$this->get_all_pce_by_project_id($project_id);
		foreach ($pce as $key => $value) {
			$oc=$this->m_oc->get_all_oc_by_pce_id($value->id,false);
			if (count($oc)==0) {
				$pass=false;
			}
		}
		return $pass;

	}
	function sync_hod_in_pce($pce_id,$project_id){
		$r_list=$this->m_Rsheet->get_all_r_sheet_by_project_id($project_id);
		$not_inapp_list = array();
		$appove_list=$this->get_hod_approve_by_pce_id($pce_id);
		$hod_list = array();
		$hod_list2 = array();
		foreach ($r_list as $key => $value) {
			$hour_dat=$this->m_hour_rate->get_hour_rate_by_id($value->type);
			if (isset($hour_dat->is_special)&&$hour_dat->is_special=="n") {
				$hod_array[$key]=$this->m_Rsheet->find_hod_by_t_type($value->type);
			}			
		}
		foreach ($hod_array as $key => $value) {
			foreach ($value as $key2 => $value2) {
				$hod_list2[$value2]=$value2;
			}
		}
		foreach ($hod_list2 as $key => $value) {
			$ap_dat = array(
						'hod_usn' => $value, 
						'pce_id' => $pce_id, 
			);
			if (!isset($appove_list[$value]->hod_usn)) {
				$this->add_hod_approve_pce($ap_dat);
			}
		}
		foreach ($appove_list as $key => $value) {
			if (!isset($hod_list2[$value->hod_usn])) {
				$this->delete_hod_approve_by_id($value->id);
			}
		}
	}
	function sync_hod_all_pce(){
		$project_list=$this->m_project->get_all_project_by_status("all");
		foreach ($project_list as $key => $value) {
			$pce_list=$this->get_all_pce_by_project_id($value->project_id);
			foreach ($pce_list as $key2 => $value2) {
				$this->sync_hod_in_pce($value2->id,$value->project_id);
			}
		}
	}
	function sync_hod_by_project($project_id){
			$pce_list=$this->get_all_pce_by_project_id($project_id);
			foreach ($pce_list as $key2 => $value2) {
				$this->sync_hod_in_pce($value2->id,$project_id);
			}
		
	}
	function get_all_pce($rewrite_stat="n"){
		$g_list = array();
		$this->db->order_by("pce_no", "asc");
			$this->db->where('rewrite_stat', $rewrite_stat);
		
		$query = $this->db->get('pce_doc');
		
		if ($query->num_rows() > 0) {
			$g_list = $query->result();
			foreach ($g_list as $key => $value) {
				$g_list[$key]->outsource=$this->m_outsource->get_all_outsource_by_pce_id($value->id);
				$g_list[$key]->hod_list=$this->get_hod_approve_by_pce_id($value->id);
			}
		}
		return $g_list;

	}
	function check_pce_paid_billed($pce_id){
		$flag=false;
		$out=$this->m_outsource->get_all_outsource_by_pce_id($pce_id,true);
		foreach ($out as $key => $value) {
			if (count($value->bill_paid)>0) {
				$flag=true;
				break;
			}
		}
		$oc=$this->m_oc->get_all_oc_by_pce_id($pce_id,false,true);
		foreach ($oc as $key => $value) {
			foreach ($value->oc_bill as $key2 => $value2) {
				if ($value2->collected=="y") {
					$flag=true;
					break 2;
				}
			}
		}
		return $flag;
	}
}