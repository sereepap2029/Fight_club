<?php
class M_oc extends CI_Model {
 
  public function __construct(){
    parent::__construct();
  		$this->load->model("m_stringlib"); 
  		$this->load->model("m_user");  			
  		$this->load->model("m_pce");  			
	}	
	function generate_id()
	{
		$isuniq    = FALSE;
		$clam_id = '';
		do
		{
			$temp_id = $this->m_stringlib->uniqueAlphaNum10();
			$query = $this->db->get_where('oc_doc', array('id' => $temp_id));
			if ($query->num_rows() == 0)
			{
				$clam_id = $temp_id;
				$isuniq    = TRUE;
			}
		}
		while(!$isuniq);
	
		return $clam_id;
	}
	function delete_oc ($id) {
		$oc=$this->get_oc_by_id($id);
		@unlink("./media/real_pdf/" . $oc->filename);
		@unlink("./media/real_pdf/" . $oc->filename_pce);
		$this->delete_oc_bill_by_oc_id($id);
		$this->db->where('id', $id);
		$this->db->delete('oc_doc');
	}
	function delete_oc_bill_by_oc_id($oc_id) {
		$this->db->where('oc_id', $oc_id);
		$this->db->delete('oc_bill');
	}
	function delete_oc_bill_by_id($id) {
		$this->db->where('id', $id);
		$this->db->delete('oc_bill');
	}
	function add_oc ($data) {
		$this->db->insert('oc_doc', $data);
	}
	function add_oc_bill($data) {
		$this->db->insert('oc_bill', $data);
	}
	function get_oc_bill_by_id ($id) {
		$business = new stdClass();
		$query = $this->db->get_where('oc_bill', array('id' => $id));
		
		if ($query->num_rows() > 0) {
			$business = $query->result();
			$business = $business[0];
		}
		return $business;
	}
	function get_oc_bill_by_oc_id ($oc_id) {
		$g_list = array();
		$g_list2 = array();
		$this->db->order_by("time", "asc");
		$query = $this->db->get_where('oc_bill', array('oc_id' => $oc_id));
		
		if ($query->num_rows() > 0) {
			$g_list = $query->result();
			foreach ($g_list as $key => $value) {
				$g_list2[$value->id]=$value;
			}
		}
		return $g_list2;
	}
	function get_oc_bill_by_time ($start,$end,$colloect="all",$by_receive=false,$order_col="time",$collect_by="bill") {
		$g_list = array();
		$g_list2 = array();
		$this->db->order_by($order_col, "asc");
		if ($by_receive) {
			$this->db->where('paid_date <=', $end);
			$this->db->where('paid_date >=', $start);
		}else{
			$this->db->where('time <=', $end);
			$this->db->where('time >=', $start);
		}		
		if ($colloect!="all"&&$collect_by=="bill") {
			$this->db->where('collected', $colloect);
		}else if($colloect!="all"&&$collect_by=="check"){
			$this->db->where('receive_check_colllect', $colloect);
		}		
		$query = $this->db->get('oc_bill');
		
		if ($query->num_rows() > 0) {
			$g_list = $query->result();
		}
		return $g_list;
	}
	function update_oc_bill($data, $id) {
		$this->db->where('id', $id);
		$this->db->update('oc_bill', $data);
	}
	function update_oc($data, $id) {
		$this->db->where('id', $id);
		$this->db->update('oc_doc', $data);
	}

	function approve_all_oc_by_project_id($usn, $project_id) {
		$this->db->where('project_id', $project_id);
		$data = array(
			'fc_sign' => $usn, 
			'fc_sign_time' => time(),
			'status' => "y", 
			);
		$this->db->update('oc_doc', $data);
	}

	function get_all_oc_by_project_id($project_id,$get_rewrite=false,$get_only_oc=false){
		$g_list = array();
		$this->db->where('project_id', $project_id);
		if (!$get_rewrite) {
			$this->db->where('rewrite_stat', "n");
		}
		$query = $this->db->get('oc_doc');
		
		if ($query->num_rows() > 0) {
			$g_list = $query->result();
			foreach ($g_list as $key => $value) {
				$fc_sign=$this->m_user->get_user_by_login_name($g_list[$key]->fc_sign);
				if (isset($fc_sign->firstname)) {
					$g_list[$key]->fc_sign_name=$fc_sign->firstname." ".$fc_sign->lastname;
				}else{
					$g_list[$key]->fc_sign_name="not sign";
				}
				if (!$get_only_oc) {
					$g_list[$key]->oc_bill=$this->get_oc_bill_by_oc_id($value->id);
					$g_list[$key]->pce=$this->m_pce->get_pce_by_id($value->pce_id);
				}
				
			}
		}
		return $g_list;

	}
	function get_all_oc($get_rewrite=false,$get_only_oc=false){
		$g_list = array();
		if ($get_rewrite) {
			$this->db->where('rewrite_stat', "y");
		}else{
			$this->db->where('rewrite_stat', "n");
		}
		$query = $this->db->get('oc_doc');
		
		if ($query->num_rows() > 0) {
			$g_list = $query->result();
			foreach ($g_list as $key => $value) {
				$fc_sign=$this->m_user->get_user_by_login_name($g_list[$key]->fc_sign);
				if (isset($fc_sign->firstname)) {
					$g_list[$key]->fc_sign_name=$fc_sign->firstname." ".$fc_sign->lastname;
				}else{
					$g_list[$key]->fc_sign_name="not sign";
				}
				if (!$get_only_oc) {
					$g_list[$key]->oc_bill=$this->get_oc_bill_by_oc_id($value->id);
					$g_list[$key]->pce=$this->m_pce->get_pce_by_id($value->pce_id);
				}
				
			}
		}
		return $g_list;

	}
	function check_all_done_oc_by_project_id($project_id){
		$g_list = array();
		$this->db->where('project_id', $project_id);
		$this->db->where('rewrite_stat', "n");
		$query = $this->db->get('oc_doc');
		$is_done=true;
		if ($query->num_rows() > 0) {
			$g_list = $query->result();
			foreach ($g_list as $key => $value) {
				if ($value->is_done=="n") {
					$is_done=false;
					break;
				}
			}
		}
		return $is_done;

	}
	function get_all_oc_by_pce_id($pce_id,$get_pce=true,$get_bill=true){
		$g_list = array();
		$this->db->where('pce_id', $pce_id);
		$query = $this->db->get('oc_doc');
		
		if ($query->num_rows() > 0) {
			$g_list = $query->result();
			foreach ($g_list as $key => $value) {
				$fc_sign=$this->m_user->get_user_by_login_name($g_list[$key]->fc_sign);
				if (isset($fc_sign->firstname)) {
					$g_list[$key]->fc_sign_name=$fc_sign->firstname." ".$fc_sign->lastname;
				}else{
					$g_list[$key]->fc_sign_name="not sign";
				}
				if ($get_bill) {
					$g_list[$key]->oc_bill=$this->get_oc_bill_by_oc_id($value->id);
				}				
				if ($get_pce) {
					$g_list[$key]->pce=$this->m_pce->get_pce_by_id($value->pce_id,false);
				}
				
			}
		}
		return $g_list;

	}
	function get_oc_by_id ($id,$nobill=false) {
		$business = new stdClass();
		$query = $this->db->get_where('oc_doc', array('id' => $id));
		
		if ($query->num_rows() > 0) {
			$business = $query->result();
			$business = $business[0];
			if (!$nobill) {
				$business->oc_bill=$this->get_oc_bill_by_oc_id($id);
			}
			
		}
		return $business;
	}
	function handle_file_oc($filename,$oc_id){
		$filename = $filename;
        $ext = explode(".", $filename);
        $new_ext = $ext[count($ext) - 1];
        $new_filename = $oc_id ."_".time()."." . $new_ext;
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

	function handle_file_pce_final($filename,$oc_id){
		$filename = $filename;
        $ext = explode(".", $filename);
        $new_ext = $ext[count($ext) - 1];
        $new_filename = $oc_id ."_pce_".time()."." . $new_ext;
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
}