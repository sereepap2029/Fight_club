<?php
class M_account extends CI_Model
{
    
    public function __construct() {
        parent::__construct();
        $this->load->model("m_stringlib");
        $this->load->model("m_project");
        $this->load->model("m_oc");
        $this->load->model("m_pce");
        $this->load->model("m_outsource");
        $this->load->model("m_forcast");
        $this->load->model("m_time");
        $this->load->model("m_company");
    }
    function check_stat_bill($project_id){
        //$oc_list=$this->m_oc->get_all_oc_by_project_id($project_id);
        $pce_list=$this->m_pce->get_all_pce_by_project_id($project_id,false,false,true);
        $re_string = "";
        $fully=true;
        $have_colloct=false;
        $all_have_po=true;
        foreach ($pce_list as $key => $value) {
            $oc_list=$this->m_oc->get_all_oc_by_pce_id($value->id,false);
            foreach ($oc_list as $okey => $ovalue) {
                foreach ($ovalue->oc_bill as $bkey => $bvalue) {
                    if ($bvalue->collected=="y") {
                       $have_colloct=true;
                    }else{
                        $fully=false;
                    }
                    if (trim($bvalue->po)=="") {
                        $all_have_po=false;
                    }
                }
                if (count($ovalue->oc_bill)<=0) {
                    $fully=false;
                }
            }
            if (count($oc_list)<=0) {
                $fully=false;
            }
        }
        
        if (!$all_have_po) {
            $re_string = "Awaiting PO";
        }else if($all_have_po&&!$have_colloct&&!$fully){
            $re_string = "With PO";
        }else if($all_have_po&&!$fully&&$have_colloct){
            $re_string = "Partially Billed";
        }else if($all_have_po&&$fully&&$have_colloct){
            $re_string = "Fully Billed";
        }else{
            $re_string = "Awaiting PO";
        }
        return $re_string;

    }
    function ger_report_bill_for_print($start,$end,$all_status="y",$account_unit_id="all",$business_unit_id="all") {
        $g_list = array();
        $oc_bill = array();
        $business_unit_list=$this->get_all_business();
        if ($all_status=="y") {
            $oc_bill=$this->m_oc->get_oc_bill_by_time($start,$end);
        }else if($all_status=="c"){
            $oc_bill=$this->m_oc->get_oc_bill_by_time($start,$end,"n");
        }else{
            $oc_bill=$this->m_oc->get_oc_bill_by_time($start,$end,"y",true,"paid_date");
        }        
        foreach ($oc_bill as $key => $value) {              
                $oc_doc=$this->m_oc->get_oc_by_id($value->oc_id,true);
                $project=$this->m_project->get_project_by_id($oc_doc->project_id);
                if (($account_unit_id=="all"||$project->account_unit_id==$account_unit_id)&&($business_unit_id=="all"||$project->business_unit_id==$business_unit_id)) {                   
                    $client=$this->m_company->get_company_by_id($project->project_client);
                    $client_bu=$this->m_company->get_bu_by_id($project->project_bu);
                    $g_list[$key]= new stdClass();
                    $g_list[$key]->time=$value->time;
                    $g_list[$key]->time_bill=$this->m_time->unix_to_datepicker_reverse($value->paid_date);
                    $g_list[$key]->time_check=$this->m_time->unix_to_datepicker_reverse($value->receive_check_date);
                    $g_list[$key]->oc_no=$oc_doc->oc_no;
                    $g_list[$key]->so=$value->so;
                    if (isset($client->name)&&isset($client_bu->bu_name)) {
                        $g_list[$key]->client=$client->name." ".$client_bu->bu_name;
                    }else{
                        $g_list[$key]->client="Client not found";
                    }
                    if (isset($business_unit_list[$project->business_unit_id]->name)){
                        $g_list[$key]->business=$business_unit_list[$project->business_unit_id]->name;
                    }else{
                        $g_list[$key]->business="business unit not found";
                    }
                    if (isset($business_unit_list[$project->account_unit_id]->name)){
                        $g_list[$key]->account_unit=$business_unit_list[$project->account_unit_id]->name;
                    }else{
                        $g_list[$key]->account_unit="Account unit not found";
                    }            
                    $g_list[$key]->project_name=$project->project_name;                
                    $g_list[$key]->amount=$value->amount;
                    $g_list[$key]->receive_amount=$value->paid_amount;
                }
            
        }
        return $g_list;
    }
    function ger_report_payment_for_print($start,$end,$all_status="y",$account_unit_id="all",$business_unit_id="all") {
        $g_list = array();
        $payment_bill = array();
        $business_unit_list=$this->get_all_business();
        if ($all_status=="y") {
            $payment_bill=$this->m_outsource->get_outsource_bill_by_time($start,$end);
        }else if ($all_status=="c") {
            $payment_bill=$this->m_outsource->get_outsource_bill_by_time($start,$end,"n");
        }else{
            $payment_bill_paid=$this->m_outsource->get_outsource_bill_paid_by_time($start,$end,"y");
            foreach ($payment_bill_paid as $key => $value) {
                if (!isset($payment_bill[$value->bill_id])) {
                    $payment_bill[$value->bill_id]=$this->m_outsource->get_outsource_bill_by_id($value->bill_id);
                }
            }
        }
        
        //print_r($payment_bill);
        foreach ($payment_bill as $key => $value) {
            $bill_paid=$this->m_outsource->get_outsource_bill_paid_by_outsource_bill_id($value->id);
            $outsource=$this->m_outsource->get_outsource_by_id($value->outsource_id);
            $pce=$this->m_pce->get_pce_by_id($outsource->pce_id,false);
            $oc=$this->m_oc->get_all_oc_by_pce_id($outsource->pce_id,false,false);
            $project=$this->m_project->get_project_by_id($pce->project_id);
            if (($account_unit_id=="all"||$project->account_unit_id==$account_unit_id)&&($business_unit_id=="all"||$project->business_unit_id==$business_unit_id)) {                   
                $client=$this->m_company->get_company_by_id($project->project_client);
                $client_bu=$this->m_company->get_bu_by_id($project->project_bu);
                foreach ($bill_paid as $key2 => $value2) {
                        $tmp_val= new stdClass();
                        $tmp_val->time=$value->time;
                        $tmp_val->time_paid=$this->m_time->unix_to_datepicker_reverse($value2->date);
                        $tmp_val->save_date=$this->m_time->unix_to_datepicker_reverse($value2->save_date);
                        $tmp_val->pv=$value2->pv;
                        if (isset($oc[0]->oc_no)) {
                            $tmp_val->oc_no=$oc[0]->oc_no;
                        }else{
                            $tmp_val->oc_no="ยังไม่มี OC";
                        }
                        $tmp_val->project_name=$project->project_name;
                        if (isset($client->name)&&isset($client_bu->bu_name)) {
                            $tmp_val->client=$client->name." ".$client_bu->bu_name;
                        }else{
                            $tmp_val->client="Client not found";
                        }          
                        if (isset($business_unit_list[$project->business_unit_id]->name)){
                            $tmp_val->business=$business_unit_list[$project->business_unit_id]->name;
                        }else{
                            $tmp_val->business="business unit not found";
                        }      
                        if (isset($business_unit_list[$project->account_unit_id]->name)){
                            $tmp_val->account_unit=$business_unit_list[$project->account_unit_id]->name;
                        }else{
                            $tmp_val->account_unit="Account unit not found";
                        }
                        $tmp_val->description=$outsource->qt_des;
                        $tmp_val->amount=$value2->amount;
                        $tmp_val->paid_type=$value2->paid_type;
                        $tmp_val->max_amount=$value->amount;
                        $g_list[]=$tmp_val;
                    
                }
                if (count($bill_paid)==0&&($all_status=="y"||$all_status=="c")) {
                    $tmp_val= new stdClass();
                    $tmp_val->time=$value->time;
                    $tmp_val->time_paid=$this->m_time->unix_to_datepicker_reverse($value->time+(60*60*24*30));//credit_term
                    $tmp_val->save_date="ยังไม่บันทึก";
                    $tmp_val->pv="ยังไม่มี PV";
                    if (isset($oc[0]->oc_no)) {
                        $tmp_val->oc_no=$oc[0]->oc_no;
                    }else{
                        $tmp_val->oc_no="ยังไม่มี OC";
                    }
                    $tmp_val->project_name=$project->project_name;
                    if (isset($client->name)&&isset($client_bu->bu_name)) {
                        $tmp_val->client=$client->name." ".$client_bu->bu_name;
                    }else{
                        $tmp_val->client="Client not found";
                    }            
                    if (isset($business_unit_list[$project->business_unit_id]->name)){
                        $tmp_val->business=$business_unit_list[$project->business_unit_id]->name;
                    }else{
                        $tmp_val->business="business unit not found";
                    }  
                    if (isset($business_unit_list[$project->account_unit_id]->name)){
                        $tmp_val->account_unit=$business_unit_list[$project->account_unit_id]->name;
                    }else{
                        $tmp_val->account_unit="Account unit not found";
                    }
                    $tmp_val->description=$outsource->qt_des;
                    $tmp_val->amount=0;
                    $tmp_val->paid_type="ไม่ระบุ";
                    $tmp_val->max_amount=$value->amount;
                    $g_list[]=$tmp_val;
                }
            }
            
        }
        return $g_list;
    }

    function get_all_business() {
        $g_list = array();
        $g_list2 = array();
        $this->db->order_by("name", "asc");
        $query = $this->db->get('business');
        
        if ($query->num_rows() > 0) {
            $g_list = $query->result();
            foreach ($g_list as $key => $value) {
                $g_list2[$value->id]=$value;
            }
        }
        return $g_list2;
    }
    function get_all_project_by_unit($unit,$start="all",$end="all") {
        $g_list = array();
        $this->db->where("business_unit_id", $unit);
        if ($start!="all") {
            $this->db->where("project_start >=", $start);
        }
        if ($end!="all") {
            $this->db->where("project_start <=", $end);
        }
        $query = $this->db->get('project');
        
        if ($query->num_rows() > 0) {
            $g_list = $query->result();
        }
        return $g_list;
    }
    function get_all_billing_status() {
        $g_list = array();
        $g_list=$this->get_all_business();
        $all_bill_mtd=$this->get_all_bill_mtd();
        $all_bill_outstanding=$this->get_all_bill_outstanding();
        $all_bill_nextmonth=$this->get_all_bill_next_month();
        $all_overdue=$this->get_all_bill_overdue();
            foreach ($g_list as $key => $value) {
                if (isset($all_bill_mtd[$value->id])) {
                    $g_list[$key]->bill_mtd=$all_bill_mtd[$value->id];
                }else{
                    $g_list[$key]->bill_mtd=0;
                }
                if (isset($all_bill_outstanding[$value->id])) {
                    $g_list[$key]->outstanding=$all_bill_outstanding[$value->id];
                }else{
                    $g_list[$key]->outstanding=0;
                }
                if (isset($all_bill_nextmonth[$value->id])) {
                    $g_list[$key]->bill_nextmonth=$all_bill_nextmonth[$value->id];
                }else{
                    $g_list[$key]->bill_nextmonth=0;
                }
                if (isset($all_overdue[$value->id])) {
                    $g_list[$key]->overdue=$all_overdue[$value->id];
                }else{
                    $g_list[$key]->overdue=0;
                }
                
            }
        return $g_list;
    }
    

    function get_all_bill_overdue(){
        $end_month=$this->m_time->get_end_month(time());
        $start_month=$this->m_time->get_start_month(time());
        $oc_bill=$this->m_oc->get_oc_bill_by_time($start_month,time(),"n");
        $list_result=$this->group_bill_to_unit($oc_bill);
        $g_list = array();
        foreach ($list_result as $key => $value) {
            $g_list[$key]=0;
            foreach ($value as $key2 => $value2) {
                $g_list[$key]+=$value2->amount;
            }
            
            
        }
        return $g_list;
    }

    function get_all_bill_next_month(){
        $end_month=$this->m_time->get_end_month(time());
        $end_next_month=$this->m_time->get_end_month($end_month+2);
        $oc_bill=$this->m_oc->get_oc_bill_by_time($end_month+2,$end_next_month,"y",true);   
        $oc_bill2=$this->m_oc->get_oc_bill_by_time($end_month+2,$end_next_month,"n");
        $oc_bill3 = array_merge($oc_bill, $oc_bill2);
        $list_result=$this->group_bill_to_unit($oc_bill3);
        $g_list = array();
        foreach ($list_result as $key => $value) {
            $g_list[$key]=0;
            foreach ($value as $key2 => $value2) {
                $g_list[$key]+=$value2->amount;
            }
            
            
        }
        return $g_list;
    }

    function get_all_bill_outstanding(){
        $end_month=$this->m_time->get_end_month(time());
        $start_month=$this->m_time->get_start_month(time());
        $oc_bill=$this->m_oc->get_oc_bill_by_time($start_month,$end_month,"y",true);   
        $oc_bill2=$this->m_oc->get_oc_bill_by_time($start_month,$end_month,"n");
        $oc_bill3 = array_merge($oc_bill, $oc_bill2);
        $list_result=$this->group_bill_to_unit($oc_bill3);
        $g_list = array();
        foreach ($list_result as $key => $value) {
            $g_list[$key]=0;
            foreach ($value as $key2 => $value2) {
                $g_list[$key]+=$value2->amount;
            }
            
            
        }
        return $g_list;
    }

    function get_all_bill_mtd(){
        $end_month=$this->m_time->get_end_month(time());
        $start_month=$this->m_time->get_start_month(time());
        //echo date("d/m/Y H:i:s",$start_month);
        $oc_bill=$this->m_oc->get_oc_bill_by_time($start_month,time(),"y",true);        
        $list_result=$this->group_bill_to_unit($oc_bill);
        $g_list = array();
        foreach ($list_result as $key => $value) {
            $g_list[$key]=0;
            foreach ($value as $key2 => $value2) {
                $g_list[$key]+=$value2->amount;
            }
            
            
        }
        return $g_list;
    }
    function group_bill_to_unit($bill_list){        
        $g_list = array();
        $oc_b_list = array();
        foreach ($bill_list as $key => $value) {
            $oc_b_list[$value->oc_id][]=$value;
        }
        foreach ($oc_b_list as $key => $value) {
            $oc_obj=$this->m_oc->get_oc_by_id($key);
            $project=$this->m_project->get_project_by_id($oc_obj->project_id);
            foreach ($value as $key2 => $value2) {
                    $g_list[$project->business_unit_id][]=$value2;
            }
            
        }
        return $g_list;
    }










    ///////////// Outsource region //////////////////////////////////////////////
    function get_all_outsource_status() {
        $g_list = array();
        $g_list=$this->get_all_business();
        $all_bill_mtd=$this->get_all_payment_mtd();
        $all_bill_outstanding=$this->get_all_payment_outstanding();
        $all_bill_nextmonth=$this->get_all_payment_next_month();
        $all_overdue=$this->get_all_payment_overdue();
            foreach ($g_list as $key => $value) {
                if (isset($all_bill_mtd[$value->id])) {
                    $g_list[$key]->bill_mtd=$all_bill_mtd[$value->id];
                }else{
                    $g_list[$key]->bill_mtd=0;
                }
                if (isset($all_bill_outstanding[$value->id])) {
                    $g_list[$key]->outstanding=$all_bill_outstanding[$value->id];
                }else{
                    $g_list[$key]->outstanding=0;
                }
                if (isset($all_bill_nextmonth[$value->id])) {
                    $g_list[$key]->bill_nextmonth=$all_bill_nextmonth[$value->id];
                }else{
                    $g_list[$key]->bill_nextmonth=0;
                }
                if (isset($all_overdue[$value->id])) {
                    $g_list[$key]->overdue=$all_overdue[$value->id];
                }else{
                    $g_list[$key]->overdue=0;
                }
                
            }
        return $g_list;
    }
    function get_all_payment_mtd(){
        $end_month=$this->m_time->get_end_month(time());
        $out_payment=$this->m_outsource->get_outsource_bill_by_time(0,$end_month,"y",true);
        $list_result=$this->group_payment_to_unit($out_payment);
        $g_list = array();
        foreach ($list_result as $key => $value) {
            $g_list[$key]=0;
            foreach ($value as $key2 => $value2) {
                $g_list[$key]+=$value2->amount;
            }
            
            
        }
        return $g_list;
    }
    function get_all_payment_outstanding(){
        $end_month=$this->m_time->get_end_month(time());
        $start_month=$this->m_time->get_start_month(time());
        $out_payment=$this->m_outsource->get_outsource_bill_by_time($start_month,$end_month,"y",true);   
        $out_payment2=$this->m_outsource->get_outsource_bill_by_time($start_month,$end_month,"n");
        $out_payment3 = array_merge($out_payment, $out_payment2);
        $list_result=$this->group_payment_to_unit($out_payment3);
        $g_list = array();
        foreach ($list_result as $key => $value) {
            $g_list[$key]=0;
            foreach ($value as $key2 => $value2) {
                $g_list[$key]+=$value2->amount;
            }
            
            
        }
        return $g_list;
    }

    function get_all_payment_next_month(){
        $end_month=$this->m_time->get_end_month(time());
        $end_next_month=$this->m_time->get_end_month($end_month+2);
        $out_payment=$this->m_outsource->get_outsource_bill_by_time($end_month+2,$end_next_month,"y",true);   
        $out_payment2=$this->m_outsource->get_outsource_bill_by_time($end_month+2,$end_next_month,"n");
        $out_payment3 = array_merge($out_payment, $out_payment2);
        $list_result=$this->group_payment_to_unit($out_payment3);
        $g_list = array();
        foreach ($list_result as $key => $value) {
            $g_list[$key]=0;
            foreach ($value as $key2 => $value2) {
                $g_list[$key]+=$value2->amount;
            }
            
            
        }
        return $g_list;
    }

    function get_all_payment_overdue(){
        $start_month=$this->m_time->get_start_month(time());
        $out_payment=$this->m_outsource->get_outsource_bill_by_time($start_month,time(),"n");
        $list_result=$this->group_payment_to_unit($out_payment);
        $g_list = array();
        foreach ($list_result as $key => $value) {
            $g_list[$key]=0;
            foreach ($value as $key2 => $value2) {
                $g_list[$key]+=$value2->amount;
            }
            
            
        }
        return $g_list;
    }
    function group_payment_to_unit($bill_list){
        //print_r($bill_list);
        $g_list = array();
        $out_p_list = array();
        $pce_list = array();
        foreach ($bill_list as $key => $value) {
            $out_p_list[$value->outsource_id][]=$value;
        }
        foreach ($out_p_list as $key => $value) {
            $out_obj=$this->m_outsource->get_outsource_by_id($key);
            $pce_list[$out_obj->pce_id][$key]=$value;
        }
        foreach ($pce_list as $key => $value) {
            $pce_obj=$this->m_pce->get_pce_by_id($key,false);
            $project=$this->m_project->get_project_by_id($pce_obj->project_id);
            foreach ($value as $key2 => $value2) {
                foreach ($value2 as $out_key => $out_value) {
                        $g_list[$project->business_unit_id][]=$out_value;
                }
                    
            }
            
        }
        //print_r($g_list);
        return $g_list;
    }
    ///////////// end out region ////////////////////////////////////////////////

    function get_position_by_id($id) {
        $position = new stdClass();
        $query = $this->db->get_where('position', array('id' => $id));
        
        if ($query->num_rows() > 0) {
            $position = $query->result();
            $position = $position[0];
        }
        return $position;
    }


    //////////////////////////////////// outsource report region /////////////////////////////////
    function get_forcast_outsource_report($start,$end,$bu,$multi_usn = array("all" => "all"),$mode=1) {
        $forcast_ready = array();
        $forcast_list = $this->m_forcast->get_all_forcast('all',$start,$end,$bu);
        $cradit_turm=(0);
        if ($mode==1) {
            foreach ($forcast_list as $key => $value) {            
                $value->project_end_credit_term=$value->project_end+$cradit_turm;
                if (isset($multi_usn['all'])) {
                    if (!isset($forcast_ready[$value->project_cs])) {
                        $forcast_ready[$value->project_cs]=$this->m_user->get_user_by_login_name($value->project_cs);
                        $forcast_ready[$value->project_cs]->forcast_list = array();
                        $forcast_ready[$value->project_cs]->forcast_sum_value=0;
                        $forcast_ready[$value->project_cs]->forcast_sum_value+=$value->outsource_value;
                        $forcast_ready[$value->project_cs]->forcast_list[$value->project_end+$cradit_turm][$value->project_id]=$value;
                    }else{
                        $forcast_ready[$value->project_cs]->forcast_sum_value+=$value->outsource_value;
                        $forcast_ready[$value->project_cs]->forcast_list[$value->project_end+$cradit_turm][$value->project_id]=$value;
                    }
                }else if (isset($multi_usn[$value->project_cs])) {
                    if (!isset($forcast_ready[$value->project_cs])) {
                        $forcast_ready[$value->project_cs]=$this->m_user->get_user_by_login_name($value->project_cs);
                        $forcast_ready[$value->project_cs]->forcast_list = array();
                        $forcast_ready[$value->project_cs]->forcast_sum_value=0;
                        $forcast_ready[$value->project_cs]->forcast_sum_value+=$value->outsource_value;
                        $forcast_ready[$value->project_cs]->forcast_list[$value->project_end+$cradit_turm][$value->project_id]=$value;
                    }else{
                        $forcast_ready[$value->project_cs]->forcast_sum_value+=$value->outsource_value;
                        $forcast_ready[$value->project_cs]->forcast_list[$value->project_end+$cradit_turm][$value->project_id]=$value;
                    }
                }                
            }
        }else if ($mode==2) {
            foreach ($forcast_list as $key => $value) {          
                if (isset($multi_usn['all'])||isset($multi_usn[$value->project_cs])) {  
                    $value->project_end_credit_term=$value->project_end+$cradit_turm;
                    $forcast_ready[$value->project_client][$value->project_end+$cradit_turm][$value->project_id]=$value;
                }
            }
        }else if ($mode==3) {
            foreach ($forcast_list as $key => $value) {            
                if (isset($multi_usn['all'])||isset($multi_usn[$value->project_cs])) {
                    $value->project_end_credit_term=$value->project_end+$cradit_turm;
                    $forcast_ready[$value->project_client."_".$value->project_bu][$value->project_end+$cradit_turm][$value->project_id]=$value;     
                }        
            }
        }
        
        
        return $forcast_ready;
    }

    function get_outsource_report($start,$end,$bu,$multi_usn = array("all" => "all"),$mode=1) {
        $project_ready_pce = array();
        $project_ready_format = array();
        $forcast_list = $this->m_project->get_all_project_by_status('no_draf','all',$start,$end,$bu,"asc","project_end");
        // select only project that have outsource but don't have bill
        foreach ($forcast_list as $key => $value) {
            $pce_list=$this->m_pce->get_all_pce_by_project_id($value->project_id);
            if (count($pce_list)>0) {
                foreach ($pce_list as $key2 => $value2) {
                    $outsource=$this->m_outsource->get_all_outsource_by_pce_id($value2->id);
                    if (count($outsource)>0) {
                        foreach ($outsource as $outkey => $outvalue) {
                            $out_bill=$this->m_outsource->get_outsource_bill_by_out_id($outvalue->id);
                            if (count($out_bill)<=0) {
                                if (!isset($project_ready_pce[$value->project_id])) {
                                    $project_ready_pce[$value->project_id]=$value;
                                    $project_ready_pce[$value->project_id]->outsource = array();
                                    $project_ready_pce[$value->project_id]->outsource[]=$outvalue;
                                }else{
                                    $project_ready_pce[$value->project_id]->outsource[]=$outvalue;
                                }
                            }                            
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
        }else if ($mode==2) {
            foreach ($project_ready_pce as $key => $value) {
                if (isset($multi_usn['all'])||isset($multi_usn[$value->project_cs])) {
                    $project_ready_format[$value->project_client][$value->project_end][$value->project_id]=$value;
                }
            }
        }else if ($mode==3) {
            foreach ($project_ready_pce as $key => $value) {
                if (isset($multi_usn['all'])||isset($multi_usn[$value->project_cs])) {
                    $project_ready_format[$value->project_client."_".$value->project_bu][$value->project_end][$value->project_id]=$value;
                }
            }
        }
        
        return $project_ready_format;
    }
    function get_outsource_target_bill_report($start,$end,$bu,$multi_usn = array("all" => "all"),$mode=1) { 
        $outsource_bill=$this->m_outsource->get_outsource_bill_by_time($start,$end);
        $outsource_list = array();
        $project_list = array();
        $project_ready_format = array();
        foreach ($outsource_bill as $key => $value) {
            if (!isset($outsource_list[$value->outsource_id])) {
                $outsource=$this->m_outsource->get_outsource_by_id($value->outsource_id);
                $outsource->bill = array();
                $outsource->bill[$value->time][]=$value;
                $outsource_list[$value->outsource_id]=$outsource;
            }else{
                $outsource_list[$value->outsource_id]->bill[$value->time][]=$value;
            }
            
        }

        foreach ($outsource_list as $key => $value) {
            $pce=$this->m_pce->get_pce_by_id($value->pce_id,false);
            if (!isset($project_list[$pce->project_id])) {
                $project=$this->m_project->get_project_by_id($pce->project_id);
                $project->outsource = array();
                $project->outsource[$key]=$value;
                $project_list[$pce->project_id]=$project;
            }else{
                $project_list[$pce->project_id]->outsource[$key]=$value;
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
                if ($value->status!="Archive") {                   
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
            }
        }else if ($mode==2) {
            foreach ($project_list as $key => $value) {
                if ($value->status!="Archive") {
                    if (isset($multi_usn['all'])||isset($multi_usn[$value->project_cs])) {
                        $project_ready_format[$value->project_client][$value->project_id]=$value;
                    }
                }
            }
        }else if ($mode==3) {
            foreach ($project_list as $key => $value) {
                if ($value->status!="Archive") {
                    if (isset($multi_usn['all'])||isset($multi_usn[$value->project_cs])) {
                        $project_ready_format[$value->project_client."_".$value->project_bu][$value->project_id]=$value;
                    }
                }
            }
        }
        return $project_ready_format;
    }

    function get_outsource_actual_bill_report($start,$end,$bu,$multi_usn = array("all" => "all"),$mode=1) { 
        $outsource_bill=$this->m_outsource->get_outsource_bill_paid_by_time($start,$end,"y","save_date","save_date");
        $outsource_list = array();
        $project_list = array();
        $project_ready_format = array();
        foreach ($outsource_bill as $key => $value) {
            if (!isset($outsource_list[$value->outsource_id])) {
                $outsource=$this->m_outsource->get_outsource_by_id($value->outsource_id);
                if (isset($outsource->pce_id)) {
                    $outsource->bill = array();
                    $outsource->bill[$value->save_date][]=$value;
                    $outsource_list[$value->outsource_id]=$outsource;
                }else{
                    $this->m_outsource->delete_outsource($value->outsource_id);
                }                
            }else{
                $outsource_list[$value->outsource_id]->bill[$value->save_date][]=$value;
            }
            
        }

        foreach ($outsource_list as $key => $value) {
            $pce=$this->m_pce->get_pce_by_id($value->pce_id,false);
            if (!isset($project_list[$pce->project_id])) {
                $project=$this->m_project->get_project_by_id($pce->project_id);
                $project->outsource = array();
                $project->outsource[$key]=$value;
                $project_list[$pce->project_id]=$project;
            }else{
                $project_list[$pce->project_id]->outsource[$key]=$value;
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









    //////////////////////////////////// outsource report Paid region /////////////////////////////////
    function get_forcast_outsource_paid_report($start,$end,$bu,$multi_usn = array("all" => "all"),$mode=1) {
        $forcast_ready = array();
        $forcast_list = $this->m_forcast->get_all_forcast('all',$start,$end,$bu);
        $cradit_turm=(60*60*24*30);
        if ($mode==1) {
            foreach ($forcast_list as $key => $value) {            
                $value->project_end_credit_term=$value->project_end+$cradit_turm;
                if (isset($multi_usn['all'])) {
                    if (!isset($forcast_ready[$value->project_cs])) {
                        $forcast_ready[$value->project_cs]=$this->m_user->get_user_by_login_name($value->project_cs);
                        $forcast_ready[$value->project_cs]->forcast_list = array();
                        $forcast_ready[$value->project_cs]->forcast_sum_value=0;
                        $forcast_ready[$value->project_cs]->forcast_sum_value+=$value->outsource_value;
                        $forcast_ready[$value->project_cs]->forcast_list[$value->project_end+$cradit_turm][$value->project_id]=$value;
                    }else{
                        $forcast_ready[$value->project_cs]->forcast_sum_value+=$value->outsource_value;
                        $forcast_ready[$value->project_cs]->forcast_list[$value->project_end+$cradit_turm][$value->project_id]=$value;
                    }
                }else if (isset($multi_usn[$value->project_cs])) {
                    if (!isset($forcast_ready[$value->project_cs])) {
                        $forcast_ready[$value->project_cs]=$this->m_user->get_user_by_login_name($value->project_cs);
                        $forcast_ready[$value->project_cs]->forcast_list = array();
                        $forcast_ready[$value->project_cs]->forcast_sum_value=0;
                        $forcast_ready[$value->project_cs]->forcast_sum_value+=$value->outsource_value;
                        $forcast_ready[$value->project_cs]->forcast_list[$value->project_end+$cradit_turm][$value->project_id]=$value;
                    }else{
                        $forcast_ready[$value->project_cs]->forcast_sum_value+=$value->outsource_value;
                        $forcast_ready[$value->project_cs]->forcast_list[$value->project_end+$cradit_turm][$value->project_id]=$value;
                    }
                }                
            }
        }else if ($mode==2) {
            foreach ($forcast_list as $key => $value) {            
                if (isset($multi_usn['all'])||isset($multi_usn[$value->project_cs])) {
                    $value->project_end_credit_term=$value->project_end+$cradit_turm;
                    $forcast_ready[$value->project_client][$value->project_end+$cradit_turm][$value->project_id]=$value;
                }
            }
        }else if ($mode==3) {
            foreach ($forcast_list as $key => $value) {            
                if (isset($multi_usn['all'])||isset($multi_usn[$value->project_cs])) {
                    $value->project_end_credit_term=$value->project_end+$cradit_turm;
                    $forcast_ready[$value->project_client."_".$value->project_bu][$value->project_end+$cradit_turm][$value->project_id]=$value;
                }
            }
        }
        
        
        return $forcast_ready;
    }

    function get_outsource_paid_report($start,$end,$bu,$multi_usn = array("all" => "all"),$mode=1) {
        $project_ready_pce = array();
        $project_ready_format = array();
        $forcast_list = $this->m_project->get_all_project_by_status('no_draf','all',$start,$end,$bu,"asc","project_end");
        // select only project that have outsource but don't have bill
        foreach ($forcast_list as $key => $value) {
            $pce_list=$this->m_pce->get_all_pce_by_project_id($value->project_id);
            if (count($pce_list)>0) {
                foreach ($pce_list as $key2 => $value2) {
                    $outsource=$this->m_outsource->get_all_outsource_by_pce_id($value2->id);
                    if (count($outsource)>0) {
                        foreach ($outsource as $outkey => $outvalue) {
                            $out_bill=$this->m_outsource->get_outsource_bill_by_out_id($outvalue->id);
                            if (count($out_bill)<=0) {
                                if (!isset($project_ready_pce[$value->project_id])) {
                                    $project_ready_pce[$value->project_id]=$value;
                                    $project_ready_pce[$value->project_id]->outsource = array();
                                    $project_ready_pce[$value->project_id]->outsource[]=$outvalue;
                                }else{
                                    $project_ready_pce[$value->project_id]->outsource[]=$outvalue;
                                }
                            }                            
                        }                        
                    }
                }
            }           
        }
        // make array for view format
        $cradit_turm=(60*60*24*30);
        if ($mode==1) {
            foreach ($project_ready_pce as $key => $value) {
                if (isset($multi_usn['all'])) {
                    if (!isset($project_ready_format[$value->project_cs])) {
                        $project_ready_format[$value->project_cs]=$this->m_user->get_user_by_login_name($value->project_cs);
                        $project_ready_format[$value->project_cs]->forcast_list = array();
                        $project_ready_format[$value->project_cs]->forcast_sum_value=0;
                        $project_ready_format[$value->project_cs]->forcast_list[$value->project_end+$cradit_turm][$value->project_id]=$value;
                    }else{
                        $project_ready_format[$value->project_cs]->forcast_list[$value->project_end+$cradit_turm][$value->project_id]=$value;
                    }
                }else if (isset($multi_usn[$value->project_cs])) {
                    if (!isset($project_ready_format[$value->project_cs])) {
                        $project_ready_format[$value->project_cs]=$this->m_user->get_user_by_login_name($value->project_cs);
                        $project_ready_format[$value->project_cs]->forcast_list = array();
                        $project_ready_format[$value->project_cs]->forcast_sum_value=0;
                        $project_ready_format[$value->project_cs]->forcast_list[$value->project_end+$cradit_turm][$value->project_id]=$value;
                    }else{
                        $project_ready_format[$value->project_cs]->forcast_list[$value->project_end+$cradit_turm][$value->project_id]=$value;
                    }
                }
            }
        }else if ($mode==2) {
            foreach ($project_ready_pce as $key => $value) {
                if (isset($multi_usn['all'])||isset($multi_usn[$value->project_cs])) {
                    $project_ready_format[$value->project_client][$value->project_end+$cradit_turm][$value->project_id]=$value;
                }
            }
        }else if ($mode==3) {
            foreach ($project_ready_pce as $key => $value) {
                if (isset($multi_usn['all'])||isset($multi_usn[$value->project_cs])) {
                    $project_ready_format[$value->project_client."_".$value->project_bu][$value->project_end+$cradit_turm][$value->project_id]=$value;
                }
            }
        }
        
        return $project_ready_format;
    }
    function get_outsource_paid_target_bill_report($start,$end,$bu,$multi_usn = array("all" => "all"),$mode=1) { 
        $cradit_turm=(60*60*24*30);
        $outsource_bill=$this->m_outsource->get_outsource_bill_by_time($start,$end);
        $outsource_list = array();
        $project_list = array();
        $project_ready_format = array();
        foreach ($outsource_bill as $key => $value) {
            if (!isset($outsource_list[$value->outsource_id])) {
                $outsource=$this->m_outsource->get_outsource_by_id($value->outsource_id);
                $outsource->bill = array();
                $outsource->bill[$value->time+$cradit_turm][]=$value;
                $outsource_list[$value->outsource_id]=$outsource;
            }else{
                $outsource_list[$value->outsource_id]->bill[$value->time+$cradit_turm][]=$value;
            }
            
        }

        foreach ($outsource_list as $key => $value) {
            $pce=$this->m_pce->get_pce_by_id($value->pce_id,false);
            if (!isset($project_list[$pce->project_id])) {
                $project=$this->m_project->get_project_by_id($pce->project_id);
                $project->outsource = array();
                $project->outsource[$key]=$value;
                $project_list[$pce->project_id]=$project;
            }else{
                $project_list[$pce->project_id]->outsource[$key]=$value;
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
                if ($value->status!="Archive") {
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
            }
        }else if ($mode==2) {
            foreach ($project_list as $key => $value) {
                if ($value->status!="Archive") {
                    if (isset($multi_usn['all'])||isset($multi_usn[$value->project_cs])) {
                        $project_ready_format[$value->project_client][$value->project_id]=$value;
                    }
                }
            }
        }else if ($mode==3) {
            foreach ($project_list as $key => $value) {
                if ($value->status!="Archive") {
                    if (isset($multi_usn['all'])||isset($multi_usn[$value->project_cs])) {
                        $project_ready_format[$value->project_client."_".$value->project_bu][$value->project_id]=$value;
                    }
                }
            }
        }
        return $project_ready_format;
    }

    function get_outsource_paid_actual_bill_report($start,$end,$bu,$multi_usn = array("all" => "all"),$mode=1) { 
        $outsource_bill=$this->m_outsource->get_outsource_bill_paid_by_time($start,$end,"y");
        $outsource_list = array();
        $project_list = array();
        $project_ready_format = array();
        foreach ($outsource_bill as $key => $value) {
            if (!isset($outsource_list[$value->outsource_id])) {
                $outsource=$this->m_outsource->get_outsource_by_id($value->outsource_id);
                if (isset($outsource->pce_id)) {
                    $outsource->bill = array();
                    $outsource->bill[$value->date][]=$value;
                    $outsource_list[$value->outsource_id]=$outsource;
                }else{
                    $this->m_outsource->delete_outsource($value->outsource_id);
                }                
            }else{
                $outsource_list[$value->outsource_id]->bill[$value->date][]=$value;
            }
            
        }

        foreach ($outsource_list as $key => $value) {
            $pce=$this->m_pce->get_pce_by_id($value->pce_id,false);
            if (!isset($project_list[$pce->project_id])) {
                $project=$this->m_project->get_project_by_id($pce->project_id);
                $project->outsource = array();
                $project->outsource[$key]=$value;
                $project_list[$pce->project_id]=$project;
            }else{
                $project_list[$pce->project_id]->outsource[$key]=$value;
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
}
