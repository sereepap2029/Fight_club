<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');


class Res extends CI_Controller
{
public function __construct() {
        parent::__construct();
        $this->load->model('m_user');
        $this->load->model('m_project');
        $this->load->model('m_time');
        $this->load->model('m_work_sheet');
        $this->load->model('m_resource');
        $this->load->model('m_company');
        $this->load->model('m_business');
        $this->load->model('m_hour_rate');
        $this->load->model('m_hr');
        if ($this->session->userdata('username')) {
            $user_data = $this->m_user->get_user_by_login_name($this->session->userdata('username'));
            if (isset($user_data->username) && isset($user_data->prem['resource'])) {
                $this->user_data = $user_data;
            }
            else {
                redirect('main/logout');
            }
        }
        else {
            redirect('main/logout');
        }
    }
    
    public function index() {
        $current_day_time=$this->m_time->datepicker_to_unix(date("d/m/Y"));
        $start_car_time=$current_day_time-(60*60*24*7);
        $end_car_time=$current_day_time+(60*60*24*60);
        $data_foot['table'] = "yes";
        $data_head['user_data'] = $this->user_data;
        //$data_view['hour_list']=$this->m_resource->get_res_assign_by_and_usn_and_time($this->user_data->username,$start_car_time,$end_car_time);
        $data_view['project_wip']=$this->m_resource->get_project_WIP_revise($this->user_data->username,false);
        $data_view['holiday_obj']=$this->m_hr->get_all_holiday($start_car_time,$end_car_time);
        $data_view['user_leave_obj']=$this->m_hr->get_all_user_leave($start_car_time,$end_car_time);
        //print_r($data_view['holiday']);
        foreach ($data_view['project_wip'] as $key => $value) {
            $data_view['project_wip'][$key]->work_sheet=$this->m_work_sheet->get_work_sheet_by_project_id($key);
            foreach ($data_view['project_wip'][$key]->work_sheet as $key2 => $value2) {
                $data_view['project_wip'][$key]->work_sheet[$key2]->assign_obj=$this->m_work_sheet->get_res_assign_by_work_id_and_usn($value2->id,$this->user_data->username,$start_car_time,"all",true);
                $resource_in_work=$this->m_work_sheet->get_work_sheet_has_res_by_work_id($value2->id);
                $have_this_usn=false;
                foreach ($resource_in_work as $res_in_key => $res_in_value) {
                    if ($res_in_value->usn==$this->user_data->username) {
                        $have_this_usn=true;
                        break;
                    }
                }
                if (!$have_this_usn) {
                    unset($data_view['project_wip'][$key]->work_sheet[$key2]);
                }else if($value2->end<$start_car_time){
                    unset($data_view['project_wip'][$key]->work_sheet[$key2]); //comment for show all current WIP project to res
                }
            }
            if (count($data_view['project_wip'][$key]->work_sheet)<=0) {
                //unset($data_view['project_wip'][$key]); comment for show all project in WIP section
            }
        }
        //var_dump($data_view['project_wip']);
        $this->load->view('v_header', $data_head);
        $this->load->view('res/v_res_dash',$data_view);
        $this->load->view('v_footer', $data_foot);

    }
    public function view_task_note(){
        $work_id=$this->uri->segment(3,'');
            $data['work_id']=$work_id;
            $data['work']=$this->m_work_sheet->get_work_sheet_by_id($work_id);
            $data['work_photo']=$this->m_work_sheet->get_work_sheet_photo_by_work_id($work_id);
            $data['user_data']=$this->user_data;
            $this->load->view('v_header_popup');
            $this->load->view('res/v_work_sheet_note',$data);
            $this->load->view('v_footer');
    }
    public function project_task()
    {   
        $id=$this->uri->segment(3,'');
                                    
            
            $data_foot['table']="yes";
            $data_head['user_data']=$this->user_data;
            $data['user_data']=$this->user_data;
            $data['a']="0";
            $data['task_type_list']=$this->m_work_sheet->get_advalid_task_type_by_project_id($id);
            $data['project']=$this->m_project->get_project_by_id($id);
            $data['bu']=$this->m_company->get_bu_by_id($data['project']->project_bu);
            $data['company']=$this->m_company->get_company_by_id($data['project']->project_client);
            $data['business_list'] = $this->m_business->get_all_business();
            //print_r($data['pce_doc']);
            $this->load->view('v_header_popup');
            $this->load->view('res/v_project_task',$data);
            $this->load->view('v_footer',$data_foot);
        
    }

    public function res_manager_overall() {
        $data_foot['table'] = "yes";
        $data_head['user_data'] = $this->user_data;
        $all_child=$this->get_all_user_child_node();
        $all_child[$this->user_data->username]=$this->user_data;
        
        $data_head['user_data'] = $this->user_data;
        $data_view['all_child'] = array();
        $current_day_time=$this->m_time->datepicker_to_unix(date("d/m/Y"));
        $start_car_time=$current_day_time-(60*60*24*7);
        $end_car_time=$current_day_time+(60*60*24*60);
        $data_view['current_day_time']=$current_day_time;
        $data_view['start_car_time']=$start_car_time;
        $data_view['end_car_time']=$end_car_time;
        $data_view['holiday_obj']=$this->m_hr->get_all_holiday($start_car_time,$end_car_time);
        $data_view['user_leave_obj']=$this->m_hr->get_all_user_leave($start_car_time,$end_car_time);
        $position=$this->m_position->get_all_position();
        foreach ($all_child as $key => $value) {
            if (isset($position[$value->position])&&$position[$value->position]->non_productive=="n") {
                $data_view['all_child'][$key]=$value;
                $data_view['all_child'][$key]->project_wip=$this->ger_detail_resource_for_res_manager($value->username,$current_day_time,$start_car_time,$end_car_time);
                $data_view['all_child'][$key]->daily_dat=$this->get_detail_res_for_overall_view($value->username,$current_day_time,$start_car_time,$end_car_time);
            }
            
        }
        //print_r($data_view['all_child']);
        $this->load->view('v_header', $data_head);
        $this->load->view('res/res_manager',$data_view);
        $this->load->view('v_footer', $data_foot);
    }
    public function child_control() {
        $data_foot['table'] = "yes";
        $child_username=$this->uri->segment(3,'');
        $current_day_time=$this->m_time->datepicker_to_unix(date("d/m/Y"));
        $start_car_time=$current_day_time-(60*60*24*7);
        $end_car_time=$current_day_time+(60*60*24*30);
        $data_view['current_day_time']=$current_day_time;
        $data_view['start_car_time']=$start_car_time;
        $data_view['end_car_time']=$end_car_time;

        $data_view['child_user'] = $this->m_user->get_user_by_login_name($child_username);
        $data_view['project_wip']=$this->m_resource->get_project_WIP_revise($child_username,false);
        $data_view['holiday_obj']=$this->m_hr->get_all_holiday($start_car_time,$end_car_time);
        $data_view['user_leave_obj']=$this->m_hr->get_all_user_leave($start_car_time,$end_car_time);
        $hour_rate_tmp= $this->m_hour_rate->get_all_hour_rate();
        $data_view['hour_rate_list'] = array();
        foreach ($hour_rate_tmp as $key => $value) {
            $data_view['hour_rate_list'][$value->id]=$value;
        }
        //print_r($data_view['holiday']);
        foreach ($data_view['project_wip'] as $key => $value) {
            $data_view['project_wip'][$key]->work_sheet=$this->m_work_sheet->get_work_sheet_by_project_id($key);
            foreach ($data_view['project_wip'][$key]->work_sheet as $key2 => $value2) {                
                $resource_in_work=$this->m_work_sheet->get_work_sheet_has_res_by_work_id($value2->id);
                $have_this_usn=false;
                foreach ($resource_in_work as $res_in_key => $res_in_value) {
                    if ($res_in_value->usn==$child_username) {
                        $have_this_usn=true;
                        break;
                    }
                }
                if (!$have_this_usn) {
                    unset($data_view['project_wip'][$key]->work_sheet[$key2]);
                }else if($value2->end<$start_car_time){
                    //unset($data_view['project_wip'][$key]->work_sheet[$key2]); //comment for show all current WIP project to res
                }
            }
            if (count($data_view['project_wip'][$key]->work_sheet)<=0) {
                //unset($data_view['project_wip'][$key]); //comment for show all project in WIP section
            }
        }
        $data_head['user_data'] = $this->user_data;
        $this->load->view('v_header', $data_head);
        $this->load->view('res/v_child_work_control',$data_view);
        $this->load->view('v_footer', $data_foot);
    }
    public function edit_task_peroid(){
        $work_id=$this->uri->segment(3,'');
            $data['work_id']=$work_id;
            $data['work']=$this->m_work_sheet->get_work_sheet_by_id($work_id);
            $data['user_data']=$this->user_data;
            $hour_rate_tmp= $this->m_hour_rate->get_all_hour_rate();
            $data['hour_rate_list'] = array();
            foreach ($hour_rate_tmp as $key => $value) {
                $data['hour_rate_list'][$value->id]=$value;
            }
            $this->load->view('v_header_popup');
            $this->load->view('res/v_edit_work_sheet_peroid',$data);
            $this->load->view('v_footer');
    }
    public function add_task_work_sheet(){
        $project_id=$this->uri->segment(3,'');
        $data['child_username']=$this->uri->segment(4,'');
            $data['project_id']=$project_id;
            $data['project']=$this->m_project->get_project_by_id($project_id,false);
            $data['user_data']=$this->user_data;
            $data['task_type_list']=$this->m_work_sheet->get_advalid_task_type_by_project_id($project_id);
            $hour_rate_id_list=$this->m_user->get_all_task_type_by_user($data['child_username']);
            foreach ($data['task_type_list'] as $key => $value) {
                if (!isset($hour_rate_id_list[$value->id])) {
                    unset($data['task_type_list'][$key]);
                }
            }
            $this->load->view('v_header_popup');
            $this->load->view('res/v_add_task_work_sheet',$data);
            $this->load->view('v_footer');
    }
////////////////////////////////// etc function ////////////////////
    function get_all_user_child_node(){
        $usn_under_super=$this->m_user->get_all_user_by_super_usn_all_lv($this->user_data->username,$this->user_data->username);
        $child_ready=$this->m_Rsheet->change_node_user_to_array($usn_under_super);
        return $child_ready;
    }
    function ger_detail_resource_for_res_manager($username,$current_day_time,$start_car_time,$end_car_time){
        $data_view['project_wip']=$this->m_resource->get_project_WIP_revise($username);
        foreach ($data_view['project_wip'] as $key => $value) {
            $data_view['project_wip'][$key]->work_sheet=$this->m_work_sheet->get_work_sheet_by_project_id($key);
            foreach ($data_view['project_wip'][$key]->work_sheet as $key2 => $value2) {
                $data_view['project_wip'][$key]->work_sheet[$key2]->assign_obj=$this->m_work_sheet->get_res_assign_by_work_id_and_usn($value2->id,$username,$start_car_time,$end_car_time);
                if (count($data_view['project_wip'][$key]->work_sheet[$key2]->assign_obj->list)<=0) {
                    unset($data_view['project_wip'][$key]->work_sheet[$key2]);
                }
            }
        }
        return $data_view['project_wip'];
    }
    function get_detail_res_for_overall_view($username,$current_day_time,$start_car_time,$end_car_time){
        $assign_obj=$this->m_work_sheet->get_res_assign_by_usn_with_start_end($username,$start_car_time,$end_car_time);
        return $assign_obj;
    }
    public function get_advailable_hour_in($t_type,$project_id,$end_time){
        $hour=0;
        $sum_approve_budget=$this->m_Rsheet->get_sum_approve_budget_by_type_and_project_id($t_type,$project_id);
        $sum_assign=$this->m_work_sheet->get_sum_assign_hour_by_t_type_and_project_id($t_type,$project_id,$end_time);
        $hour=$sum_approve_budget-$sum_assign;
        return $hour;
    }
    ///////////////////////////////// AJAX Region ///////////////////////////////////////////////
    public function add_work_task(){
        header('Content-Type: application/json');
        $json = array();
        $json['flag']="OK";
        $json['t_id']=$this->m_work_sheet->generate_id();
        $data = array(
            'id' =>  $json['t_id'],
            'work_name' =>  $_POST['task_name'],
            'project_id' =>  $_POST['project_id'],
            'task_type' =>  $_POST['task_type'],
            'start' =>  0,
            'end' =>  0,
            'sort_order' =>  1000,
            );
        $this->m_work_sheet->add_work_sheet($data);
        $this->m_work_sheet->sort_work_sheet($_POST['project_id']);
        $this->m_work_sheet->valid_delay_project($_POST['project_id']);
        $data = array(
            'work_sheet_id' => $json['t_id'], 
            'usn' => $_POST['resource'], 
        );
        if ($this->m_user->check_have_user($_POST['resource'])) {
           $this->m_work_sheet->add_work_sheet_has_res($data);
        }
        echo json_encode($json);
    }
    public function ajax_edit_peroid_work_sheet(){
        header('Content-Type: application/json');
        $json = array();
        $json['flag']="OK";
        $start=$this->m_time->datepicker_to_unix($_POST['start_date']);
        $end=$this->m_time->datepicker_to_unix($_POST['end_date']);
        $is_have_out=$this->m_work_sheet->check_if_assign_hour_out_peroid($_POST['id'],$start,$end);
        if (!$is_have_out) {
            $data = array(
                'start' =>  $start,
                'end' =>  $end,
                'hour_limit' =>  (int)$_POST['hour'],
                );
            $this->m_work_sheet->update_work_sheet($data,$_POST['id']);
            $this->m_work_sheet->sort_work_sheet($_POST['project_id']);
            $this->m_work_sheet->valid_delay_project($_POST['project_id']);
        }else{
            $json['flag']="ไม่สมารถเปลี่ยน peroid งานได้ เพราะมีชั่วโมงอยู่นอก peroid งาน";
        }
        
        echo json_encode($json);
    }
    public function del_work(){
        header('Content-Type: application/json');
        $json = array();
        $json['flag']="OK";
        $this->m_work_sheet->delete_work_sheet($_POST['id']);
        $this->m_work_sheet->sort_work_sheet($_POST['project_id']);
        echo json_encode($json);
    }
    public function print_project_child_control(){
        $child_username=$_POST['child_username'];
        $project_id=$_POST['project_id'];
        $current_day_time=$this->m_time->datepicker_to_unix(date("d/m/Y"));
        $start_car_time=(int)$_POST['start'];
        $end_car_time=(int)$_POST['end'];
        $child_user = $this->m_user->get_user_by_login_name($child_username);
        $project=$this->m_project->get_project_by_id($project_id,false);
        $holiday_obj=$this->m_hr->get_all_holiday($start_car_time,$end_car_time);
        $user_leave_obj=$this->m_hr->get_all_user_leave($start_car_time,$end_car_time);
        $hour_rate_tmp= $this->m_hour_rate->get_all_hour_rate();
        $hour_rate_list = array();
        foreach ($hour_rate_tmp as $key => $value) {
            $hour_rate_list[$value->id]=$value;
        }
            $project->work_sheet=$this->m_work_sheet->get_work_sheet_by_project_id($project_id);
            foreach ($project->work_sheet as $key2 => $value2) {
                $project->work_sheet[$key2]->assign_obj=$this->m_work_sheet->get_res_assign_by_work_id_and_usn($value2->id,$child_username,$start_car_time,"all",true);
                $resource_in_work=$this->m_work_sheet->get_work_sheet_has_res_by_work_id($value2->id);
                $have_this_usn=false;
                foreach ($resource_in_work as $res_in_key => $res_in_value) {
                    if ($res_in_value->usn==$child_username) {
                        $have_this_usn=true;
                        break;
                    }
                }
                if (!$have_this_usn) {
                    unset($project->work_sheet[$key2]);
                }
            }
            foreach ($project->work_sheet as $wkey => $wvalue) {
                ?>
                <tr>
                    <td class="nowrap">
                    <?
                    $flag_spend=$this->m_work_sheet->check_if_work_task_has_spend($wvalue->id);
                    if (!$flag_spend&&($child_username!=$this->user_data->username||isset($this->user_data->prem['hod']))) {
                        ?>
                        <a iden="<?=$wvalue->id?>" project-id="<?=$project_id?>" href="javascript:;" class="btn btn-danger del_work"><i class="icon-remove icon-white"></i></a>
                        <?
                    }
                    ?>
                    <?=$hour_rate_list[$wvalue->task_type]->name?></td>
                    <td class="nowrap"><?=$wvalue->work_name?> (<?=(int)$wvalue->hour_limit?>)</td>
                    <td>
                        <a class="btn btn-inverse fancybox" data-fancybox-type="iframe" href="<?echo site_url("res/edit_task_peroid/".$wvalue->id);?>"><i class="icon-calendar icon-white"></i></a>
                    </td>
                    <?
                    $current_time=$start_car_time;
                        while ($current_time<=$end_car_time) {
                            $holiday="";
                            $in_range="";
                            $day_click="";
                            $over_cls="";
                            $current_cls="";
                            if (date("N",$current_time)==6||date("N",$current_time)==7) {
                                $holiday='holiday_td';
                            }
                            $sum_al_hour=0;            
                            $sum_spent_hour=0;
                            $sum_spent_hour_str="";
                            if (isset($wvalue->assign_obj->list[$current_time])) { 
                                    $sum_al_hour+=$wvalue->assign_obj->list[$current_time]->hour;
                                    $sum_spent_hour+=$wvalue->assign_obj->list[$current_time]->spend;
                            }
                            $sum_spent_hour_str=$sum_spent_hour."/";
                            $sum_hour_assign=$this->m_work_sheet->get_res_assign_by_usn_and_time($child_username,$current_time)->hour_amount;
                            if ($sum_al_hour>=8) {
                                $over_cls="over_allocate";
                            }else if($sum_al_hour==0){
                                $over_cls="";
                                $sum_al_hour="";
                                $sum_spent_hour_str="";
                            }else{
                                $over_cls="normal_allocate";
                            }
                            if ($current_time==$current_day_time) {
                                $current_cls="current_day";
                            }
                            if ($wvalue->start<=$current_time&&$wvalue->end>=$current_time&&$holiday=="") {
                                $in_range='range_task';
                            }
                            if (isset($holiday_obj[$current_time])&&$holiday_obj[$current_time]->is_holiday=="y") {
                                $holiday='s_holiday_td';
                                $sum_al_hour="H";
                            }
                            if (isset($user_leave_obj[$current_time][$user_data->username])) {
                                $holiday='leave_td';
                                $sum_al_hour="L";
                            }
                           ?>
                           <td class="<?=$current_cls?> <?=$holiday?> show_click <?=$over_cls?> <?=$in_range?>" time="<?=$current_time?>">
                           <?
                           if ($holiday=="s_holiday_td"||$holiday=="leave_td") {
                               echo $sum_spent_hour_str.$sum_al_hour;
                           }else if ($in_range=="range_task") {
                                if (isset($wvalue->assign_obj->list[$current_time])) {
                                    $use_val=0;
                                    if ($wvalue->assign_obj->list[$current_time]->hour!=0.5) {
                                        $use_val=number_format($wvalue->assign_obj->list[$current_time]->hour,0);
                                    }else{
                                        $use_val=$wvalue->assign_obj->list[$current_time]->hour;
                                    }
                                    ?>
                                    <input title="<?=$sum_hour_assign?>/8 <?=date("- d F Y",$current_time)?>" class="input-assign" workid="<?=$wvalue->id?>" time="<?=$current_time?>" usn="<?=$child_username?>" value="<?=$use_val?>">
                                    <?
                                }else{
                                   ?>
                                   <input title="<?=$sum_hour_assign?>/8 <?=date("- d F Y",$current_time)?>" class="input-assign" value="0" workid="<?=$wvalue->id?>" time="<?=$current_time?>" usn="<?=$child_username?>">
                                   <?
                               }
                           }else{
                            echo $sum_spent_hour_str.$sum_al_hour;
                           }
                           ?>
                           </td>
                           <?
                           $current_time+=(60*60*24);
                        }// end while
                    ?>
                </tr>
                <?
            }
                                                                    
    }
    public function print_table_sum_work(){
        $id=$_POST['project_id'];
        $r_sheet=$this->m_Rsheet->get_all_r_sheet_by_project_id($id,"approve_budget");
        $hour_rate_list = $this->m_hour_rate->get_all_hour_rate();
                                        $num=0;
                                        foreach ($r_sheet as $t_key => $task) {
                                            $num+=1;
                                            $sum_allow_hour=$this->m_work_sheet->get_sum_assign_hour_by_t_type_and_project_id($task->type,$id);
                                        ?>
                                        <tr>
                                            <td>
                                                <?
                                                $rate_selected=0;
                                                foreach ($hour_rate_list as $key => $value) {
                                                    if($value->id==$task->type){echo $value->name;$rate_selected=$value->hour_rate;}
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <?=$task->task?>
                                            </td>
                                            
                                            <td><?=$task->approve_budget?></td>
                                            <td><?=$sum_allow_hour?></td>
                                            <td><?=$task->approve_budget-$sum_allow_hour?></td>
                                            <td>
                                                <?
                                                $allow_list=$this->m_Rsheet->get_allow_list_by_r_id($task->r_id);
                                                $al_num=count($allow_list);
                                                foreach ($allow_list as $akey => $avalue) {
                                                   $auser=$this->m_user->get_user_by_login_name($avalue->resource_usn);
                                                   if ($al_num==$akey+1) {
                                                       echo $auser->nickname;
                                                   }else{
                                                        echo $auser->nickname.",";
                                                    }
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                        <?
                                        }
    }
    public function assign_action(){
         header('Content-Type: application/json');
            $current_time=$this->m_time->datepicker_to_unix(date("d/m/Y"));
            $json = array();
            $json['flag']="OK";
            $json['status']="normal";    
         if(isset($_POST['save'])){       
                $cur_assign=$this->m_work_sheet->check_res_assign_code_1001($_POST['time'],$_POST['usn'],$_POST['save']);
                $work_sheet=$this->m_work_sheet->get_work_sheet_by_id($_POST['save']);
                $ad_hour=$this->get_advailable_hour_in($work_sheet->task_type,$work_sheet->project_id,(int)$_POST['time']);
                $sum_hour_by_work_id=$this->m_work_sheet->get_sum_assign_hour_by_work_id($_POST['save']);
                if (!isset($cur_assign->id)) {
                    $check_limit=$sum_hour_by_work_id+(int)$_POST['hour'];
                    if ($check_limit<=$work_sheet->hour_limit) {
                        $this->m_work_sheet->del_assign_usn_dup($_POST['save'],$_POST['usn'],(int)$_POST['time']);                        
                        $status="normal";
                        $ad_hour-=(int)$_POST['hour'];
                        if ($ad_hour<0) {
                            $status="over";
                        }
                        if ($current_time==(int)$_POST['time']) {
                            $status2="interfere";
                        }
                        $json['status']=$status;
                        $data = array(
                            'work_sheet_id' => $_POST['save'], 
                            'usn' => $_POST['usn'], 
                            'hour' => $_POST['hour'], 
                            'time' => (int)$_POST['time'],
                            'time_assign' =>time(), 
                            'status' => $status,
                            'who_assign' => "cs",
                            'ad_hour' => $ad_hour,
                        );
                        if ($status2=="interfere") {
                            $data['interfere']="y";
                        }
                        if ($this->m_user->check_have_user($_POST['usn'])) {
                           $this->m_work_sheet->add_work_sheet_assign($data);
                        }
                    }else{
                        $json['flag']="This work exceed limit allocate"; 
                    }
                }else{
                    $check_limit=$sum_hour_by_work_id+(int)$_POST['hour']-$cur_assign->hour;
                    if ($check_limit<=$work_sheet->hour_limit) {
                        $status="normal";
                        $ad_hour+=$cur_assign->hour;
                        $ad_hour-=(int)$_POST['hour'];
                        if ($ad_hour<0) {
                            $status="over";
                        }
                        if ($current_time==(int)$_POST['time']) {
                            $status2="interfere";
                        }
                        $json['status']=$status;
                        $data = array(
                            'work_sheet_id' => $_POST['save'], 
                            'usn' => $_POST['usn'], 
                            'hour' => $_POST['hour'], 
                            'time' => (int)$_POST['time'],
                            'time_assign' =>time(), 
                            'status' => $status,
                            'who_assign' => "cs",
                            'ad_hour' => $ad_hour,
                        );
                        if ($status2=="interfere") {
                            $data['interfere']="y";
                        }else{
                            $data['interfere']="n";
                        }
                        if ($this->m_user->check_have_user($_POST['usn'])) {
                            if ((int)$_POST['hour']>=$cur_assign->spend) {
                                $this->m_work_sheet->update_work_sheet_assign($data,$cur_assign->id);
                            }else{
                                $json['flag']="Cannot change allocate hour below Resoure spend hour,"; 
                                $json['val']=$cur_assign->hour; 
                            }
                            
                        }
                    }else{
                        $json['flag']="This work exceed limit allocate"; 
                        $json['val']=$cur_assign->hour; 
                    }
                }
                
            
        }else{
            $json['flag']="false code:".$project_id.",".$work_id.",".$time.",".$_POST['save'].","; 
        }
        echo json_encode($json);
        
    }
    public function print_work_task_left(){
        $work=$this->m_work_sheet->get_work_sheet_by_project_id($_POST['project_id']);
        foreach ($work as $key => $value) {            
            $res_list=$this->m_work_sheet->get_res_assign_detail_by_work_id($value->id);
            ?>
            <tr id="<?=$value->id?>" >
                <td class="hiligh-border-bot"><?=$value->sort_order?><br>
                </td>
                <td class="w-wrap hiligh-border-bot">
                       <?=$value->work_name?>
                 
                </td>
                <td class="hiligh-border-bot">
                       <a class="btn btn-inverse fancybox" data-fancybox-type="iframe" href="<?echo site_url("res/view_task_note/".$value->id);?>"><i class="icon-file icon-white"></i></a>
                 
                </td>
                <td class="click_add_resource td-no-padding hiligh-border-bot" workid="<?=$value->id?>">
                    <table class="table">
                        <?
                        foreach ($res_list as $res_key => $res_value) {
                            ?>
                            <tr id="<?=$value->id?>__<?=$res_value->username?>">
                                <td><?=$res_value->nickname?></td>
                            </tr>
                            <?
                        }
                        ?>
                    </table>
                </td>
                <td class="td-no-padding hiligh-border-bot">
                    <table class="table">
                        <?
                        foreach ($res_list as $res_key => $res_value) {
                            ?>
                            <tr>
                                <td><?=$res_value->assign_list->hour_amount?></td>
                            </tr>
                            <?
                        }
                        ?>
                    </table>
                </td>
            </tr>
            <?
        }
    }
    public function print_work_task_right(){
        $current_day=$this->m_time->datepicker_to_unix(date("d/m/Y"));
        $work=$this->m_work_sheet->get_work_sheet_by_project_id($_POST['project_id']);
        $project=$this->m_project->get_project_by_id($_POST['project_id']); 
        $last_work=$this->m_work_sheet->get_last_end_time_work_sheet_by_project_id($_POST['project_id']);
        $end_carlendar_unix=0;
        if (isset($last_work->end)) {
            $end_carlendar_unix=$last_work->end;
        }else{
            $end_carlendar_unix=$project->project_end;
        }
            ?>
            <table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-nopadding">
                                            <thead>
                                                <tr>
                                                <?
                                                $current_time=$project->project_start;
                                                    $cur_month=1000;
                                                    while ($current_time<=$end_carlendar_unix) {
                                                        if ($cur_month!=date("n",$current_time)) {
                                                            $cur_month=date("n",$current_time);
                                                            $cur_day=date("j",$current_time);
                                                            $numday=date("t",$current_time);
                                                            $colspan1=0;
                                                            if ($cur_month==date("n",$end_carlendar_unix)) {
                                                                $numday=date("j",$end_carlendar_unix);
                                                                $colspan1=(int)$numday-(int)$cur_day+1;
                                                            }else{
                                                                $colspan1=(int)$numday-(int)$cur_day+1;
                                                            }
                                                            
                                                            ?>
                                                            <th colspan="<?=$colspan1?>" style="text-align: center;"><?=date("M",$current_time)?></th>
                                                            <?
                                                        }                                                        
                                                       $current_time+=(60*60*24);
                                                    }
                                                ?>
                                                </tr>
                                                <tr>
                                                <?
                                                $current_time=$project->project_start;
                                                    while ($current_time<=$end_carlendar_unix) {
                                                        $holiday="";
                                                        if (date("N",$current_time)==6||date("N",$current_time)==7) {
                                                            $holiday='class="holiday_td"';
                                                        }
                                                       ?>
                                                       <th <?=$holiday?>><?=date("d",$current_time)?></th>
                                                       <?
                                                       $current_time+=(60*60*24);
                                                    }
                                                ?>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            <?
                                            foreach ($work as $key => $value) {
                                             $res_list=$this->m_work_sheet->get_res_assign_detail_by_work_id($value->id);
                                            ?>
                                                <tr class="outer_tr" iden="<?=$value->id?>" >
                                                    <?
                                                    $current_time=$project->project_start;
                                                        while ($current_time<=$end_carlendar_unix) {
                                                            $holiday="";
                                                            $in_range="";
                                                            if (date("N",$current_time)==6||date("N",$current_time)==7) {
                                                                $holiday='holiday_td';
                                                            }
                                                            if ($value->start<=$current_time&&$value->end>=$current_time&&$holiday=="") {
                                                                $in_range='range_task';
                                                            }
                                                           ?>
                                                           <td class="<?=$holiday?> <?=$in_range?> hiligh-border-bot" iden="<?=$value->id?>" time="<?=$current_time?>">
                                                               <table class="no-border">
                                                               <?
                                                                foreach ($res_list as $res_key => $res_value) {
                                                                    $range_task_over="";
                                                                    if (isset($res_value->assign_list->list[$current_time])) {
                                                                        if ($res_value->assign_list->list[$current_time]->status=="over"&&$res_value->assign_list->list[$current_time]->hour!=0) {
                                                                            $range_task_over="range_task_over";
                                                                        }
                                                                    }
                                                                    ?>
                                                                    <tr class="allow_tr_con <?=$range_task_over?>" iden="<?=$value->id?>__<?=$res_value->username?>" time="<?=$current_time?>">
                                                                        <td>
                                                                            <?
                                                                            //print_r($res_value->assign_list->list);
                                                                            $disable_input="";
                                                                            if ($current_day>$current_time) {
                                                                                $disable_input="disabled";
                                                                            }
                                                                            $sum_hour_assign=$this->m_work_sheet->get_res_assign_by_usn_and_time($res_value->username,$current_time)->hour_amount;
                                                                            if (isset($res_value->assign_list->list[$current_time])) {
                                                                                ?>
                                                                                <input disabled <?=$disable_input?> title="<?=$sum_hour_assign?>/8" class="input-assign" workid="<?=$value->id?>" time="<?=$current_time?>" usn="<?=$res_value->username?>" value="<?=$res_value->assign_list->list[$current_time]->hour?>">
                                                                                <?                                                                                
                                                                            }else{
                                                                                if ($in_range!="") {                                                                                    
                                                                                    ?>
                                                                                    <input disabled <?=$disable_input?> title="<?=$sum_hour_assign?>/8" class="input-assign" value="0" workid="<?=$value->id?>" time="<?=$current_time?>" usn="<?=$res_value->username?>">
                                                                                    <?
                                                                                }
                                                                            }
                                                                            ?>
                                                                        </td>
                                                                    </tr>
                                                                    <?
                                                                }
                                                                ?>
                                                            </table>
                                                           </td>
                                                           <?
                                                           $current_time+=(60*60*24);
                                                        }
                                                    ?>
                                                </tr>
                                                <?
                                            }
                                                ?>

                                            </tbody>
                                    </table>
            <?
    }

    public function table_detail(){
        $hour_list=$this->m_resource->get_res_assign_by_and_usn_and_time($this->user_data->username,$_POST['time'],$_POST['time']);
        //print_r($hour_list);
        $count=1;
        if (isset($hour_list->list[(int)$_POST['time']])) {        
            
            foreach ($hour_list->list[(int)$_POST['time']] as $key => $value) {
                $work_sheet=$this->m_work_sheet->get_work_sheet_by_id($value->work_sheet_id);
                $project=$this->m_project->get_project_by_id($work_sheet->project_id);
               ?>
               <tr>
                    <td><?=$count?></td>
                   <td><?=$project->project_name?></td>
                   <td><?=$work_sheet->work_name?></td>
                   <td><?=$value->hour?></td>
                   <td><input class="spend-input" type="text" name="spend[<?=$value->id?>]" value="<?=$value->spend?>"></td>
                   <td><input class="comment-input" type="text" name="comment[<?=$value->id?>]" placeholder="Write your note here" value="<?=$value->comment?>"></td>
               </tr>
               <?
               $count+=1;
            }
        }
         $c_day=date("d/m/Y",(int)$_POST['time']);
         if ($c_day==date("d/m/Y")) {
                $c_day="TODAY";
         }
            if ((int)$_POST['time']<=time()) {
                //$c_day="TODAY";
                ?>
                <tr>
                <td></td>
                    <td>
                        <a class="btn btn-info" href="javascript:add_interfere();"><i class="icon-plus icon-white"></i></a>
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <?
            }
            ?>
            <input type="hidden" id="current_datepick" value="<?=$c_day?>">
            <?
         $hour_list=$this->m_resource->get_res_assign_by_and_usn_and_time($this->user_data->username,$_POST['time'],$_POST['time'],"interfere");
        //print_r($hour_list);
         $count=1;
        if (isset($hour_list->list[(int)$_POST['time']])) {        
            
            foreach ($hour_list->list[(int)$_POST['time']] as $key => $value) {
                $work_sheet=$this->m_work_sheet->get_work_sheet_by_id($value->work_sheet_id);
                $project=$this->m_project->get_project_by_id($work_sheet->project_id);
               ?>
               <tr>
                   <td><?=$count?></td>
                   <td><?=$project->project_name?></td>
                   <td><?=$work_sheet->work_name?></td>
                   <td><?=$value->hour?></td>
                   <td><input class="spend-input" type="text" name="spend[<?=$value->id?>]" value="<?=$value->spend?>"></td>
                   <td><input class="comment-input" type="text" name="comment[<?=$value->id?>]" placeholder="Write your note here" value="<?=$value->comment?>"></td>
               </tr>
               <?
               $count+=1;
            }
        }    
    }
    public function insert_hour(){
        $current_day_time=$this->m_time->datepicker_to_unix(date("d/m/Y"));
        if ($_POST['date']!="TODAY") {
            $current_day_time=$this->m_time->datepicker_to_unix($_POST['date']);
        }
        $start_car_time=$current_day_time-(60*60*24*4);
        $project_wip=$this->m_resource->get_project_WIP_revise($this->user_data->username,false);
        $hour_list=$this->m_resource->get_res_assign_by_and_usn_and_time($this->user_data->username,$current_day_time,$current_day_time,"all");
        foreach ($project_wip as $key => $value) {
            $project_wip[$key]->work_sheet=$this->m_work_sheet->get_work_sheet_by_project_id($key);
            foreach ($project_wip[$key]->work_sheet as $key2 => $value2) {                
                $resource_in_work=$this->m_work_sheet->get_work_sheet_has_res_by_work_id($value2->id);
                $have_this_usn=false;
                foreach ($resource_in_work as $res_in_key => $res_in_value) {
                    if ($res_in_value->usn==$this->user_data->username) {
                        $have_this_usn=true;
                    }
                }     
                if (!$have_this_usn) {
                    unset($project_wip[$key]->work_sheet[$key2]);
                }else if($value2->end<$current_day_time||$value2->start>$current_day_time){
                    //unset($project_wip[$key]->work_sheet[$key2]); comment for res can add all inter fere
                }
                if (isset($hour_list->list[$current_day_time])) {
                    foreach ($hour_list->list[$current_day_time] as $h_key => $h_value) {
                        if($value2->id==$h_value->work_sheet_id){
                            unset($project_wip[$key]->work_sheet[$key2]);
                            break;
                        }
                    }
                }
            }
            if (count($project_wip[$key]->work_sheet)<=0) {
                unset($project_wip[$key]);
            }
        }
        ?>
                <tr>       
                <td></td>        
                   <td>
                      <div class="control-group">
                         <select class="change_pro_id" name="project_id[]">
                             <option value="no">---please select ------</option>
                             <?
                             foreach ($project_wip as $key2 => $value2) {
                                                     ?>
                                 <option value="<?=$value2->project_id?>"><?=$value2->project_name?></option>
                                 <?
                             }
                             ?>
                         </select>
                     </div>
                    </td>
                    
                   <td><div class="control-group">
                         <select class="change_work_id" name="work_sheet_id[]">
                         <option value="no">---please select ------</option>
                         </select>
                     </div></td>
                     <td>0</td>
                   <td><input type="text" name="inter_hour[]"></td>
                   <td><input type="text" name="com_inter[]"></td>
               </tr>
        <?
    }
    public function get_interfere_work_id(){
        $current_day_time=$this->m_time->datepicker_to_unix(date("d/m/Y"));
        $worksheet=$this->m_work_sheet->get_work_sheet_by_project_id($_POST['project_id']);
        $res_list=$this->m_work_sheet->get_work_sheet_has_res_by_project_id_and_usn($_POST['project_id'],$this->user_data->username);
        $hour_list=$this->m_resource->get_res_assign_by_and_usn_and_time($this->user_data->username,$_POST['time'],$_POST['time'],"all");
        $check_work = array();
        if (isset($hour_list->list[(int)$_POST['time']])) {
            foreach ($hour_list->list[(int)$_POST['time']] as $key => $value) {
                $check_work[$value->work_sheet_id]=$value->work_sheet_id;
            }
        }
        
        ?>
        <?/*<!--<?print_r($hour_list);?> -->
        <!--<?print_r($check_work);?> -->
        <!--<?print_r($res_list);?> -->
        <!--<?print_r($worksheet);?> -->
        <!--<?print_r($current_day_time);?> -->*/?>
        <option value="no">---please select ------</option>
        <?
                             foreach ($worksheet as $key2 => $value2) {
                                //if($value2->end>=$current_day_time&&$value2->start<=$current_day_time){ comment this if for show all task fot interfere
                                    if (isset($res_list[$value2->id])&&!isset($check_work[$value2->id])) {
                                                         ?>
                                         <option value="<?=$value2->id?>"><?=$value2->work_name?></option>
                                         <?
                                    }
                                //}
                             }
    }
    public function update_spend(){
        header('Content-Type: application/json');
            $json = array();
            $json['flag']="OK";
        if (isset($_POST['spend'])) {
            
            if ((int)$_POST['cur_time']<=time()) {
               
                foreach ($_POST['spend'] as $key => $value) {
                    $data = array(
                                'spend' => (float)$value,
                                'comment' => $_POST['comment'][$key],
                            );
                    $this->m_work_sheet->update_work_sheet_assign($data,$key);
                }
            }else{
                $json['flag']="You cannot spend in this time";
            }
        }
        if (isset($_POST['work_sheet_id'])) {
            foreach ($_POST['work_sheet_id'] as $key => $value) {
                $work_sheet=$this->m_work_sheet->get_work_sheet_by_id($value);
                 $cur_assign=$this->m_work_sheet->check_res_assign_code_1001($_POST['cur_time'],$this->user_data->username,$value);              
                 //print_r($cur_assign);
                if (!isset($cur_assign->id)) {
                        $this->m_work_sheet->del_assign_usn_dup($value,$this->user_data->username,(int)$_POST['cur_time']); 
                        $ad_hour=$this->m_work_sheet->get_advailable_hour_in($work_sheet->task_type,$work_sheet->project_id,(int)$_POST['cur_time']);
                        $project_dat=$this->m_project->get_project_by_id($work_sheet->project_id);
                        $status="normal";
                        $ad_hour-=(float)$_POST['inter_hour'][$key];
                        if ($ad_hour<0) {
                            $status="over";
                        }
                        $data = array(
                            'work_sheet_id' => $value, 
                            'usn' => $this->user_data->username, 
                            'hour' => (float)$_POST['inter_hour'][$key], 
                            'time' => (int)$_POST['cur_time'],
                            'time_assign' =>time(), 
                            'status' => $status,
                            'interfere' => "y",
                            'who_assign' => "resource",
                            'ad_hour' => "0",
                            'spend' => (float)$_POST['inter_hour'][$key],
                            'comment' => $_POST['com_inter'][$key],
                        );
                        if (!$project_dat->pass) {
                            $data['pre_oc_allocate']="y";
                        }
                        if ($this->m_user->check_have_user($this->user_data->username)) {
                           $this->m_work_sheet->add_work_sheet_assign($data);
                           $data_update_work_sheet=array(
                            'start' =>  $work_sheet->start,
                            'end' =>  $work_sheet->end,
                            );
                           $update_flag=false;
                           if ($work_sheet->start==0) {
                               $data_update_work_sheet['start']=(int)$_POST['cur_time'];
                               $update_flag=true;
                           }else if ($work_sheet->start>(int)$_POST['cur_time']) {
                                $data_update_work_sheet['start']=(int)$_POST['cur_time'];
                                $update_flag=true;
                           }
                           if ($work_sheet->end<(int)$_POST['cur_time']) {
                               $data_update_work_sheet['end']=(int)$_POST['cur_time'];
                               $update_flag=true;
                           }
                           if ($update_flag) {
                               $this->m_work_sheet->update_work_sheet($data_update_work_sheet,$work_sheet->id);
                               $this->m_work_sheet->valid_delay_project($work_sheet->project_id);
                           }
                        }
                }else{
                    $json['flag']="Success but some interfare cannot update because Task not in time span";
                }
            }
            //echo "sd  ".(int)$_POST['cur_time'];
        }
        echo json_encode($json);
    }


}