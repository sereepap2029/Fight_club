<?php
class M_forcast extends CI_Model
{
    
    public function __construct() {
        parent::__construct();
        $this->load->model("m_stringlib");
        $this->load->model("m_user");
        $this->load->model("m_project");
        $this->load->model("m_pce");
        $this->load->model("m_oc");
        $this->load->model("m_company");
    }
    
    function generate_id() {
        $isuniq = FALSE;
        $clam_id = '';
        do {
            $temp_id = $this->m_stringlib->uniqueAlphaNum10();
            $query = $this->db->get_where('forcast', array('project_id' => $temp_id));
            if ($query->num_rows() == 0) {
                $clam_id = $temp_id;
                $isuniq = TRUE;
            }
        } while (!$isuniq);
        
        return $clam_id;
    }
    function delete_forcast($id) {
        $this->db->where('project_id', $id);
        $this->db->delete('forcast');
    }
    function add_forcast($data) {
        $this->db->insert('forcast', $data);
    }
    function update_forcast($data, $id) {
        $this->db->where('project_id', $id);
        $this->db->update('forcast', $data);
    }
    function get_all_forcast($usn="all",$start="no",$end="no",$bu="all",$mulit_usn_where="no") {
        $g_list = array();
        if ($usn!="all") {
            if ($mulit_usn_where=="no") {
                $this->db->where('project_cs', $usn);
            }else{
                $this->db->where($mulit_usn_where);
            }
        }
        if ($start!="no"&&$end!="no") {
            $this->db->where('project_end >=', $start);
            $this->db->where('project_end <=', $end);
        }
        if ($bu!="all") {
            $this->db->where('business_unit_id', $bu);
        }
        $this->db->order_by("project_name", "asc");
        $query = $this->db->get('forcast');
        
        if ($query->num_rows() > 0) {
            $g_list = $query->result();
        }
        return $g_list;
    }

    function get_forcast_by_id($id) {
        $business = new stdClass();
        $query = $this->db->get_where('forcast', array('project_id' => $id));
        
        if ($query->num_rows() > 0) {
            $business = $query->result();
            $business = $business[0];
        }
        return $business;
    }
    function get_forcast_report($start,$end,$bu,$multi_usn = array("all" => "all"),$mode=1,$ac_unit="all") {
        $forcast_ready = array();
        $forcast_list = $this->get_all_forcast('all',$start,$end,$bu);
        if ($mode==1) {
            foreach ($forcast_list as $key => $value) {
                if (isset($multi_usn['all'])) {
                    if (!isset($forcast_ready[$value->project_cs])) {
                        $forcast_ready[$value->project_cs]=$this->m_user->get_user_by_login_name($value->project_cs);
                        $forcast_ready[$value->project_cs]->forcast_list = array();
                        $forcast_ready[$value->project_cs]->forcast_sum_value=0;
                        $forcast_ready[$value->project_cs]->forcast_sum_value+=$value->project_value;
                        $forcast_ready[$value->project_cs]->forcast_list[$value->project_end][$value->project_id]=$value;
                    }else{
                        $forcast_ready[$value->project_cs]->forcast_sum_value+=$value->project_value;
                        $forcast_ready[$value->project_cs]->forcast_list[$value->project_end][$value->project_id]=$value;
                    }
                }else if (isset($multi_usn[$value->project_cs])) {
                    if (!isset($forcast_ready[$value->project_cs])) {
                        $forcast_ready[$value->project_cs]=$this->m_user->get_user_by_login_name($value->project_cs);
                        $forcast_ready[$value->project_cs]->forcast_list = array();
                        $forcast_ready[$value->project_cs]->forcast_sum_value=0;
                        $forcast_ready[$value->project_cs]->forcast_sum_value+=$value->project_value;
                        $forcast_ready[$value->project_cs]->forcast_list[$value->project_end][$value->project_id]=$value;
                    }else{
                        $forcast_ready[$value->project_cs]->forcast_sum_value+=$value->project_value;
                        $forcast_ready[$value->project_cs]->forcast_list[$value->project_end][$value->project_id]=$value;
                    }
                }
                
            }
        }else if ($mode==2) {
            foreach ($forcast_list as $key => $value) {
                if (isset($multi_usn['all'])) {
                    $forcast_ready[$value->project_client][$value->project_end][$value->project_id]=$value;
                }else if (isset($multi_usn[$value->project_cs])) {
                    $forcast_ready[$value->project_client][$value->project_end][$value->project_id]=$value;
                }
                                 
                
            }

        }else if ($mode==3) {
            foreach ($forcast_list as $key => $value) {            
                if (isset($multi_usn['all'])) {    
                    $forcast_ready[$value->project_client."_".$value->project_bu][$value->project_end][$value->project_id]=$value;
                }else if (isset($multi_usn[$value->project_cs])) {
                    $forcast_ready[$value->project_client."_".$value->project_bu][$value->project_end][$value->project_id]=$value;
                }
            
            }

        }
        
        
        
        return $forcast_ready;
    }
    function get_forcast_pce_report($start,$end,$bu,$multi_usn = array("all" => "all"),$mode=1,$ac_unit="all") {
        $project_ready_pce = array();
        $project_ready_format = array();
        $forcast_list = $this->m_project->get_all_project_by_status('no_draf','no_fully',$start,$end,$bu,"asc","project_end",$ac_unit);
        // select only project that have PCE but don't have OC bill
        foreach ($forcast_list as $key => $value) {
            $pce_list=$this->m_pce->get_all_pce_by_project_id($value->project_id);
            if (count($pce_list)>0) {
                foreach ($pce_list as $key2 => $value2) {
                    $oc=$this->m_oc->get_all_oc_by_pce_id($value2->id,false,false);
                    $have_bill_flag=false;
                    foreach ($oc as $key3 => $value3) {
                        $oc_bill=$this->m_oc->get_oc_bill_by_oc_id($value3->id);
                        if (count($oc_bill)>0) {
                            $have_bill_flag=true;
                            break;
                        }
                    }
                    if (!$have_bill_flag) {
                        if (!isset($project_ready_pce[$value->project_id])) {
                            $project_ready_pce[$value->project_id]=$value;
                            $project_ready_pce[$value->project_id]->pce = array();
                            $project_ready_pce[$value->project_id]->pce[]=$value2;
                        }else{
                            $project_ready_pce[$value->project_id]->pce[]=$value2;
                        }
                    }
                }
            }           
        }
        // make array for view format
        if ($mode==1) {
            foreach ($project_ready_pce as $key => $value) {
                if (isset($multi_usn['all'])) {
                    if (!isset($project_ready_format[$value->project_cs])) {
                        $project_ready_format[$value->project_cs]=$this->m_user->get_user_by_login_name($value->project_cs);
                        $project_ready_format[$value->project_cs]->forcast_list = array();
                        $project_ready_format[$value->project_cs]->forcast_sum_value=0;
                        $project_ready_format[$value->project_cs]->forcast_list[$value->project_end][$value->project_id]=$value;
                    }else{
                        $project_ready_format[$value->project_cs]->forcast_list[$value->project_end][$value->project_id]=$value;
                    }
                }else if (isset($multi_usn[$value->project_cs])) {
                    if (!isset($project_ready_format[$value->project_cs])) {
                        $project_ready_format[$value->project_cs]=$this->m_user->get_user_by_login_name($value->project_cs);
                        $project_ready_format[$value->project_cs]->forcast_list = array();
                        $project_ready_format[$value->project_cs]->forcast_sum_value=0;
                        $project_ready_format[$value->project_cs]->forcast_list[$value->project_end][$value->project_id]=$value;
                    }else{
                        $project_ready_format[$value->project_cs]->forcast_list[$value->project_end][$value->project_id]=$value;
                    }
                }
            }
        }else if($mode==2){
            foreach ($project_ready_pce as $key => $value) {
                if (isset($multi_usn['all'])) {
                        $project_ready_format[$value->project_client][$value->project_end][$value->project_id]=$value;
                }else if (isset($multi_usn[$value->project_cs])) {  
                    $project_ready_format[$value->project_client][$value->project_end][$value->project_id]=$value;
                }
            }
        }else if($mode==3) {
            foreach ($project_ready_pce as $key => $value) {
                if (isset($multi_usn['all'])) {
                        $project_ready_format[$value->project_client."_".$value->project_bu][$value->project_end][$value->project_id]=$value;
                }else if (isset($multi_usn[$value->project_cs])) {
                    $project_ready_format[$value->project_client."_".$value->project_bu][$value->project_end][$value->project_id]=$value;
                }                        
               
            }
        }
        
        return $project_ready_format;
    }
    function get_forcast_target_bill_report($start,$end,$bu,$multi_usn = array("all" => "all"),$mode=1,$ac_unit="all") { 
        $oc_bill=$this->m_oc->get_oc_bill_by_time($start,$end,"n");
        $oc_list = array();
        $project_list = array();
        $project_ready_format = array();
        foreach ($oc_bill as $key => $value) {
            if (!isset($oc_list[$value->oc_id])) {
                $oc=$this->m_oc->get_oc_by_id($value->oc_id,true);
                $oc->oc_bill = array();
                $oc->oc_bill[$value->time][]=$value;
                if ($oc->rewrite_stat=="n") {
                    $oc_list[$value->oc_id]=$oc;
                }                
            }else{
                $oc_list[$value->oc_id]->oc_bill[$value->time][]=$value;
            }
            
        }

        foreach ($oc_list as $key => $value) {
            if (!isset($project_list[$value->project_id])) {
                $project=$this->m_project->get_project_by_id($value->project_id);
                $project->oc = array();
                $project->oc[$key]=$value;
                $project_list[$value->project_id]=$project;
            }else{
                $project_list[$value->project_id]->oc[$key]=$value;
            }
        }
        /// filter BU
        if ($bu!="all") {
            foreach ($project_list as $key => $value) {
                if ($value->business_unit_id!=$bu) {
                    unset($project_list[$key]);
                }                
            }
        }
        /// filter AC unit
        if ($ac_unit!="all") {
            foreach ($project_list as $key => $value) {
                if ($value->account_unit_id!=$ac_unit) {
                    unset($project_list[$key]);
                }                
            }
        }

       // make array for view format
        if ($mode==1) {
            foreach ($project_list as $key => $value) {
                if (isset($multi_usn['all'])) {
                    if (!isset($project_ready_format[$value->project_cs])) {
                        $project_ready_format[$value->project_cs]=$this->m_user->get_user_by_login_name($value->project_cs);
                        $project_ready_format[$value->project_cs]->forcast_list = array();
                        $project_ready_format[$value->project_cs]->forcast_sum_value=0;
                        $project_ready_format[$value->project_cs]->forcast_list[$value->project_id]=$value;                
                    }else{
                        $project_ready_format[$value->project_cs]->forcast_list[$value->project_id]=$value;
                    }
                }else if (isset($multi_usn[$value->project_cs])) {
                    if (!isset($project_ready_format[$value->project_cs])) {
                        $project_ready_format[$value->project_cs]=$this->m_user->get_user_by_login_name($value->project_cs);
                        $project_ready_format[$value->project_cs]->forcast_list = array();
                        $project_ready_format[$value->project_cs]->forcast_sum_value=0;
                        $project_ready_format[$value->project_cs]->forcast_list[$value->project_id]=$value;                
                    }else{
                        $project_ready_format[$value->project_cs]->forcast_list[$value->project_id]=$value;
                    }
                }
            }
        }else if ($mode==2) {
            foreach ($project_list as $key => $value) {               
                if (isset($multi_usn['all'])) {
                    $project_ready_format[$value->project_client][$value->project_id]=$value;
                }else if (isset($multi_usn[$value->project_cs])) {
                    $project_ready_format[$value->project_client][$value->project_id]=$value;
                }


            }
        }else if ($mode==3) {
            foreach ($project_list as $key => $value) {               
                if (isset($multi_usn['all'])) { 
                    $project_ready_format[$value->project_client."_".$value->project_bu][$value->project_id]=$value;     
                }else if (isset($multi_usn[$value->project_cs])) {        
                    $project_ready_format[$value->project_client."_".$value->project_bu][$value->project_id]=$value;                           
                }
            }
        }
        return $project_ready_format;
    }

    function get_forcast_actual_bill_report($start,$end,$bu,$multi_usn = array("all" => "all"),$mode=1,$ac_unit="all") { 
        $oc_bill=$this->m_oc->get_oc_bill_by_time($start,$end,"y",true,"paid_date");
        $oc_list = array();
        $project_list = array();
        $project_ready_format = array();
        foreach ($oc_bill as $key => $value) {
            if (!isset($oc_list[$value->oc_id])) {
                $oc=$this->m_oc->get_oc_by_id($value->oc_id,true);
                $oc->oc_bill = array();
                $oc->oc_bill[$value->paid_date][]=$value;
                if ($oc->rewrite_stat=="n") {
                    $oc_list[$value->oc_id]=$oc;
                }
            }else{
                $oc_list[$value->oc_id]->oc_bill[$value->paid_date][]=$value;
            }
            
        }

        foreach ($oc_list as $key => $value) {
            if (!isset($project_list[$value->project_id])) {
                $project=$this->m_project->get_project_by_id($value->project_id);
                $project->oc = array();
                $project->oc[$key]=$value;
                $project_list[$value->project_id]=$project;
            }else{
                $project_list[$value->project_id]->oc[$key]=$value;
            }
        }
        /// filter BU
        if ($bu!="all") {
            foreach ($project_list as $key => $value) {
                if ($value->business_unit_id!=$bu) {
                    unset($project_list[$key]);
                }                
            }
        }
        /// filter AC unit
        if ($ac_unit!="all") {
            foreach ($project_list as $key => $value) {
                if ($value->account_unit_id!=$ac_unit) {
                    unset($project_list[$key]);
                }                
            }
        }
        // make array for view format
        if ($mode==1) {
            foreach ($project_list as $key => $value) {
                if (isset($multi_usn['all'])) {
                    if (!isset($project_ready_format[$value->project_cs])) {
                        $project_ready_format[$value->project_cs]=$this->m_user->get_user_by_login_name($value->project_cs);
                        $project_ready_format[$value->project_cs]->forcast_list = array();
                        $project_ready_format[$value->project_cs]->forcast_sum_value=0;
                        $project_ready_format[$value->project_cs]->forcast_list[$value->project_id]=$value;                
                    }else{
                        $project_ready_format[$value->project_cs]->forcast_list[$value->project_id]=$value;
                    }
                }else if (isset($multi_usn[$value->project_cs])) {
                    if (!isset($project_ready_format[$value->project_cs])) {
                        $project_ready_format[$value->project_cs]=$this->m_user->get_user_by_login_name($value->project_cs);
                        $project_ready_format[$value->project_cs]->forcast_list = array();
                        $project_ready_format[$value->project_cs]->forcast_sum_value=0;
                        $project_ready_format[$value->project_cs]->forcast_list[$value->project_id]=$value;                
                    }else{
                        $project_ready_format[$value->project_cs]->forcast_list[$value->project_id]=$value;
                    }
                }
            }
        }else if ($mode==2) {
            foreach ($project_list as $key => $value) {
                if (isset($multi_usn['all'])) {
                        $project_ready_format[$value->project_client][$value->project_id]=$value;
                }else if (isset($multi_usn[$value->project_cs])) {
                    $project_ready_format[$value->project_client][$value->project_id]=$value;
                }
              
            }
        }else if ($mode==3) {
            foreach ($project_list as $key => $value) {
                if (isset($multi_usn['all'])) {               
                        $project_ready_format[$value->project_client."_".$value->project_bu][$value->project_id]=$value;
                }else if (isset($multi_usn[$value->project_cs])) {
                    $project_ready_format[$value->project_client."_".$value->project_bu][$value->project_id]=$value;
                }
                    
                
            }
        }
        return $project_ready_format;
    }









    ////////////////////////////////////////////////////////////////////////////////////////// receive Report /////////////////////////
    function get_forcast_receive_report($start,$end,$bu,$multi_usn = array("all" => "all"),$mode=1) {
        $forcast_ready = array();
        $forcast_list = $this->get_all_forcast('all',$start,$end,$bu);
        if ($mode==1) {
            foreach ($forcast_list as $key => $value) {
                $com_bu=$this->m_company->get_bu_by_id($value->project_bu);
                $value->project_end_credit_term=$value->project_end+(60*60*24*(int)$com_bu->credit_term);
                if (isset($multi_usn['all'])) {
                    if (!isset($forcast_ready[$value->project_cs])) {
                        $forcast_ready[$value->project_cs]=$this->m_user->get_user_by_login_name($value->project_cs);
                        $forcast_ready[$value->project_cs]->forcast_list = array();
                        $forcast_ready[$value->project_cs]->forcast_sum_value=0;
                        $forcast_ready[$value->project_cs]->forcast_sum_value+=$value->project_value;
                        $forcast_ready[$value->project_cs]->forcast_list[$value->project_end+(60*60*24*(int)$com_bu->credit_term)][$value->project_id]=$value;
                    }else{
                        $forcast_ready[$value->project_cs]->forcast_sum_value+=$value->project_value;
                        $forcast_ready[$value->project_cs]->forcast_list[$value->project_end+(60*60*24*(int)$com_bu->credit_term)][$value->project_id]=$value;
                    }
                }else if (isset($multi_usn[$value->project_cs])) {
                    if (!isset($forcast_ready[$value->project_cs])) {
                        $forcast_ready[$value->project_cs]=$this->m_user->get_user_by_login_name($value->project_cs);
                        $forcast_ready[$value->project_cs]->forcast_list = array();
                        $forcast_ready[$value->project_cs]->forcast_sum_value=0;
                        $forcast_ready[$value->project_cs]->forcast_sum_value+=$value->project_value;
                        $forcast_ready[$value->project_cs]->forcast_list[$value->project_end+(60*60*24*(int)$com_bu->credit_term)][$value->project_id]=$value;
                    }else{
                        $forcast_ready[$value->project_cs]->forcast_sum_value+=$value->project_value;
                        $forcast_ready[$value->project_cs]->forcast_list[$value->project_end+(60*60*24*(int)$com_bu->credit_term)][$value->project_id]=$value;
                    }
                }
                
            }
        }else if ($mode==2) {
            foreach ($forcast_list as $key => $value) {
                if (isset($multi_usn['all'])||isset($multi_usn[$value->project_cs])) {
                    $com_bu=$this->m_company->get_bu_by_id($value->project_bu);
                    $value->project_end_credit_term=$value->project_end+(60*60*24*(int)$com_bu->credit_term);
                    $forcast_ready[$value->project_client][$value->project_end+(60*60*24*(int)$com_bu->credit_term)][$value->project_id]=$value;
                }
            }
        }else if ($mode==3) {
            foreach ($forcast_list as $key => $value) {
                if (isset($multi_usn['all'])||isset($multi_usn[$value->project_cs])) {
                    $com_bu=$this->m_company->get_bu_by_id($value->project_bu);
                    $value->project_end_credit_term=$value->project_end+(60*60*24*(int)$com_bu->credit_term);
                    $forcast_ready[$value->project_client."_".$value->project_bu][$value->project_end+(60*60*24*(int)$com_bu->credit_term)][$value->project_id]=$value;
                }
            }
        }
        
        
        return $forcast_ready;
    }
    function get_forcast_receive_pce_report($start,$end,$bu,$multi_usn = array("all" => "all"),$mode=1) {
        $project_ready_pce = array();
        $project_ready_format = array();
        $forcast_list = $this->m_project->get_all_project_by_status('no_draf','no_fully',$start,$end,$bu,"asc","project_end");
        // select only project that have PCE but don't have OC bill
        foreach ($forcast_list as $key => $value) {
            $pce_list=$this->m_pce->get_all_pce_by_project_id($value->project_id);
            if (count($pce_list)>0) {
                foreach ($pce_list as $key2 => $value2) {
                    $oc=$this->m_oc->get_all_oc_by_pce_id($value2->id,false,false);
                    $have_bill_flag=false;
                    foreach ($oc as $key3 => $value3) {
                        $oc_bill=$this->m_oc->get_oc_bill_by_oc_id($value3->id);
                        if (count($oc_bill)>0) {
                            $have_bill_flag=true;
                            break;
                        }
                    }
                    if (!$have_bill_flag) {
                        if (!isset($project_ready_pce[$value->project_id])) {
                            $project_ready_pce[$value->project_id]=$value;
                            $project_ready_pce[$value->project_id]->pce = array();
                            $project_ready_pce[$value->project_id]->pce[]=$value2;
                        }else{
                            $project_ready_pce[$value->project_id]->pce[]=$value2;
                        }
                    }
                }
            }           
        }
        // make array for view format
        if ($mode==1) {
            foreach ($project_ready_pce as $key => $value) {
                $com_bu=$this->m_company->get_bu_by_id($value->project_bu);
                $value->project_end_credit_term=$value->project_end+(60*60*24*(int)$com_bu->credit_term);
                if (isset($multi_usn['all'])) {
                    if (!isset($project_ready_format[$value->project_cs])) {
                        $project_ready_format[$value->project_cs]=$this->m_user->get_user_by_login_name($value->project_cs);
                        $project_ready_format[$value->project_cs]->forcast_list = array();
                        $project_ready_format[$value->project_cs]->forcast_sum_value=0;
                        $project_ready_format[$value->project_cs]->forcast_list[$value->project_end+(60*60*24*(int)$com_bu->credit_term)][$value->project_id]=$value;
                    }else{
                        $project_ready_format[$value->project_cs]->forcast_list[$value->project_end+(60*60*24*(int)$com_bu->credit_term)][$value->project_id]=$value;
                    }
                }else if (isset($multi_usn[$value->project_cs])) {
                    if (!isset($project_ready_format[$value->project_cs])) {
                        $project_ready_format[$value->project_cs]=$this->m_user->get_user_by_login_name($value->project_cs);
                        $project_ready_format[$value->project_cs]->forcast_list = array();
                        $project_ready_format[$value->project_cs]->forcast_sum_value=0;
                        $project_ready_format[$value->project_cs]->forcast_list[$value->project_end+(60*60*24*(int)$com_bu->credit_term)][$value->project_id]=$value;
                    }else{
                        $project_ready_format[$value->project_cs]->forcast_list[$value->project_end+(60*60*24*(int)$com_bu->credit_term)][$value->project_id]=$value;
                    }
                }
            }
        }else if ($mode==2) {
            foreach ($project_ready_pce as $key => $value) {
                if (isset($multi_usn['all'])||isset($multi_usn[$value->project_cs])) {
                    $com_bu=$this->m_company->get_bu_by_id($value->project_bu);
                    $value->project_end_credit_term=$value->project_end+(60*60*24*(int)$com_bu->credit_term);
                    $project_ready_format[$value->project_client][$value->project_end+(60*60*24*(int)$com_bu->credit_term)][$value->project_id]=$value;
                }
            }
        }else if ($mode==3) {
            foreach ($project_ready_pce as $key => $value) {
                if (isset($multi_usn['all'])||isset($multi_usn[$value->project_cs])) {
                    $com_bu=$this->m_company->get_bu_by_id($value->project_bu);
                    $value->project_end_credit_term=$value->project_end+(60*60*24*(int)$com_bu->credit_term);
                    $project_ready_format[$value->project_client."_".$value->project_bu][$value->project_end+(60*60*24*(int)$com_bu->credit_term)][$value->project_id]=$value;
                }
            }
        }
        
        return $project_ready_format;
    }
    function get_forcast_receive_target_bill_report($start,$end,$bu,$multi_usn = array("all" => "all"),$mode=1) { 
        $oc_bill=$this->m_oc->get_oc_bill_by_time($start,$end,"n");
        $oc_list = array();
        $project_list = array();
        $project_ready_format = array();
        foreach ($oc_bill as $key => $value) {
            if (!isset($oc_list[$value->oc_id])) {
                $oc=$this->m_oc->get_oc_by_id($value->oc_id,true);
                $project=$this->m_project->get_project_by_id($oc->project_id);
                $com_bu=$this->m_company->get_bu_by_id($project->project_bu);
                $oc->credit_term=(60*60*24*(int)$com_bu->credit_term);
                $oc->oc_bill = array();
                $oc->oc_bill[$value->time+$oc->credit_term][]=$value;
                if ($oc->rewrite_stat=="n") {
                    $oc_list[$value->oc_id]=$oc;
                }
            }else{
                $credit_term=$oc_list[$value->oc_id]->credit_term;
                $oc_list[$value->oc_id]->oc_bill[$value->time+$credit_term][]=$value;
            }
            
        }

        foreach ($oc_list as $key => $value) {
            if (!isset($project_list[$value->project_id])) {
                $project=$this->m_project->get_project_by_id($value->project_id);
                $project->oc = array();
                $project->oc[$key]=$value;
                $project_list[$value->project_id]=$project;
            }else{
                $project_list[$value->project_id]->oc[$key]=$value;
            }
        }
        /// filter BU
        if ($bu!="all") {
            foreach ($project_list as $key => $value) {
                if ($value->business_unit_id!=$bu) {
                    unset($project_list[$key]);
                }                
            }
        }

       // make array for view format
        if ($mode==1) {
            foreach ($project_list as $key => $value) {
                if (isset($multi_usn['all'])) {
                    if (!isset($project_ready_format[$value->project_cs])) {
                        $project_ready_format[$value->project_cs]=$this->m_user->get_user_by_login_name($value->project_cs);
                        $project_ready_format[$value->project_cs]->forcast_list = array();
                        $project_ready_format[$value->project_cs]->forcast_sum_value=0;
                        $project_ready_format[$value->project_cs]->forcast_list[$value->project_id]=$value;                
                    }else{
                        $project_ready_format[$value->project_cs]->forcast_list[$value->project_id]=$value;
                    }
                }else if (isset($multi_usn[$value->project_cs])) {
                    if (!isset($project_ready_format[$value->project_cs])) {
                        $project_ready_format[$value->project_cs]=$this->m_user->get_user_by_login_name($value->project_cs);
                        $project_ready_format[$value->project_cs]->forcast_list = array();
                        $project_ready_format[$value->project_cs]->forcast_sum_value=0;
                        $project_ready_format[$value->project_cs]->forcast_list[$value->project_id]=$value;                
                    }else{
                        $project_ready_format[$value->project_cs]->forcast_list[$value->project_id]=$value;
                    }
                }
            }
        }else if ($mode==2) {
            foreach ($project_list as $key => $value) {
                if (isset($multi_usn['all'])||isset($multi_usn[$value->project_cs])) {
                    $project_ready_format[$value->project_client][$value->project_id]=$value;
                }
            }
        }else if ($mode==3) {
            foreach ($project_list as $key => $value) {
                if (isset($multi_usn['all'])||isset($multi_usn[$value->project_cs])) {
                    $project_ready_format[$value->project_client."_".$value->project_bu][$value->project_id]=$value;
                }
            }
        }
        return $project_ready_format;
    }

    function get_forcast_receive_actual_bill_report($start,$end,$bu,$multi_usn = array("all" => "all"),$mode=1) { 
        $oc_bill=$this->m_oc->get_oc_bill_by_time($start,$end,"y",true,"receive_check_date","check");
        $oc_list = array();
        $project_list = array();
        $project_ready_format = array();
        foreach ($oc_bill as $key => $value) {
            if (!isset($oc_list[$value->oc_id])) {
                $oc=$this->m_oc->get_oc_by_id($value->oc_id,true);
                $oc->oc_bill = array();
                $oc->oc_bill[$value->receive_check_date][]=$value;
                if ($oc->rewrite_stat=="n") {
                    $oc_list[$value->oc_id]=$oc;
                }
            }else{
                $oc_list[$value->oc_id]->oc_bill[$value->receive_check_date][]=$value;
            }
            
        }

        foreach ($oc_list as $key => $value) {
            if (!isset($project_list[$value->project_id])) {
                $project=$this->m_project->get_project_by_id($value->project_id);
                $project->oc = array();
                $project->oc[$key]=$value;
                $project_list[$value->project_id]=$project;
            }else{
                $project_list[$value->project_id]->oc[$key]=$value;
            }
        }
        /// filter BU
        if ($bu!="all") {
            foreach ($project_list as $key => $value) {
                if ($value->business_unit_id!=$bu) {
                    unset($project_list[$key]);
                }                
            }
        }
        // make array for view format
        if ($mode==1) {
            foreach ($project_list as $key => $value) {
                if (isset($multi_usn['all'])) {
                    if (!isset($project_ready_format[$value->project_cs])) {
                        $project_ready_format[$value->project_cs]=$this->m_user->get_user_by_login_name($value->project_cs);
                        $project_ready_format[$value->project_cs]->forcast_list = array();
                        $project_ready_format[$value->project_cs]->forcast_sum_value=0;
                        $project_ready_format[$value->project_cs]->forcast_list[$value->project_id]=$value;                
                    }else{
                        $project_ready_format[$value->project_cs]->forcast_list[$value->project_id]=$value;
                    }
                }else if (isset($multi_usn[$value->project_cs])) {
                    if (!isset($project_ready_format[$value->project_cs])) {
                        $project_ready_format[$value->project_cs]=$this->m_user->get_user_by_login_name($value->project_cs);
                        $project_ready_format[$value->project_cs]->forcast_list = array();
                        $project_ready_format[$value->project_cs]->forcast_sum_value=0;
                        $project_ready_format[$value->project_cs]->forcast_list[$value->project_id]=$value;                
                    }else{
                        $project_ready_format[$value->project_cs]->forcast_list[$value->project_id]=$value;
                    }
                }
            }
        }else if ($mode==2) {
            foreach ($project_list as $key => $value) {
                if (isset($multi_usn['all'])||isset($multi_usn[$value->project_cs])) {
                    $project_ready_format[$value->project_client][$value->project_id]=$value;
                }
            }
        }else if ($mode==3) {
            foreach ($project_list as $key => $value) {
                if (isset($multi_usn['all'])||isset($multi_usn[$value->project_cs])) {
                    $project_ready_format[$value->project_client."_".$value->project_bu][$value->project_id]=$value;
                }
            }
        }
        return $project_ready_format;
    }

    function gd_equal_array($arr1,$arr2){
        foreach ($arr1 as $key => $value) {
            if (!isset($arr2[$key])) {
                $arr2[$key] = array();
            }
        }
        return $arr2;
    }


}
