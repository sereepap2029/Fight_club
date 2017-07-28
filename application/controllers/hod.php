<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');


class hod extends CI_Controller
{
public function __construct() {
        parent::__construct();
        $this->load->model('m_user');
        $this->load->model('m_project');
        $this->load->model('m_time');
        $this->load->model('m_group');
        $this->load->model('m_hour_rate');
        $this->load->model('m_resource');
        $this->load->model('m_company');
        $this->load->model('m_Rsheet');
        $this->load->model('m_business');
        $this->load->model('m_work_sheet');
        $this->load->model('m_position');
        $this->load->model('m_hr');
        $this->load->model('m_utilization_report');
        if ($this->session->userdata('username')) {
            $user_data = $this->m_user->get_user_by_login_name($this->session->userdata('username'));
            if (isset($user_data->username) && isset($user_data->prem['hod'])) {
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
        $data_foot['table'] = "yes";
        $data_head['user_data'] = $this->user_data;
        $data_view['filter'] = array();
        $data_view['filter']['start_date']=(time()-(60*60*24*60));
        if (isset($_POST['start_time'])) {
            $data_view['filter']['start_date']=$this->m_time->datepicker_to_unix($_POST['start_time']);
        }
        $data_view['filter']['end_date']=(time()+(60*60*24*60));
        if (isset($_POST['end_time'])) {
            $data_view['filter']['end_date']=$this->m_time->datepicker_to_unix($_POST['end_time']);
        }
        $data_view['filter']['project_cs']="all";
        if (isset($_POST['project_cs'])) {
            $data_view['filter']['project_cs']=$_POST['project_cs'];
        }
        $data_view['cs']=$this->m_user->get_all_user_by_prem('cs');
        $data_view['project_list']=$this->m_project->get_project_by_hod_not_sign($this->user_data->username,$data_view['filter']);        
        $this->load->view('v_header', $data_head);
        $this->load->view('hod/v_hod_list',$data_view);
        $this->load->view('v_footer', $data_foot);
    }

    public function edit_hod()
    {
        $id=$this->uri->segment(3,'');
        $data_foot['table']="yes";
        $data_head['user_data']=$this->user_data;
        $data['a']="0";
        $data['project']=$this->m_project->get_project_by_id($id);
        $data['r_sheet']=$this->m_Rsheet->get_all_r_sheet_by_project_id($id);
        $data['pce_doc']=$this->m_pce->get_all_pce_by_project_id($id);
        $data['bu']=$this->m_company->get_bu_by_id($data['project']->project_bu);
        $data['company']=$this->m_company->get_company_by_id($data['project']->project_client);
        $data['business_list'] = $this->m_business->get_all_business();
        foreach ($data['r_sheet'] as $key => $value) {
            $data['r_sheet'][$key]->type_obj=$this->m_hour_rate->get_hour_rate_by_id($value->type);
        }
        //print_r($data['pce_doc']);
            $this->load->view('v_header',$data_head);
            $this->load->view('hod/v_hod_edit',$data);
            $this->load->view('v_footer',$data_foot);
        
    }

    public function assign_resource()
    {
        $id=$this->uri->segment(3,'');
        $data_foot['table']="yes";
        $data_head['user_data']=$this->user_data;
        $data['a']="0";
        //$this->m_Rsheet->combine_same_type($id);
        $data['project']=$this->m_project->get_project_by_id($id);
        $data['r_sheet']=$this->m_Rsheet->get_all_r_sheet_by_project_id($id);
        $data['pce_doc']=$this->m_pce->get_all_pce_by_project_id($id);
        $data['bu']=$this->m_company->get_bu_by_id($data['project']->project_bu);
        $data['company']=$this->m_company->get_company_by_id($data['project']->project_client);
        $data['business_list'] = $this->m_business->get_all_business();
        foreach ($data['r_sheet'] as $key => $value) {
            $data['r_sheet'][$key]->type_obj=$this->m_hour_rate->get_hour_rate_by_id($value->type);
        }
        $data['delegate_r']=$this->m_Rsheet->get_delegate_list_by_project_id($id,"no","r_id");
        //print_r($data['pce_doc']);
            $this->load->view('v_header',$data_head);
            $this->load->view('hod/v_assign',$data);
            $this->load->view('v_footer',$data_foot);
        
    }
    public function complete_assign()
    {
        $id=$this->uri->segment(3,'');
        $pce_list=$this->m_pce->get_all_pce_by_project_id($id);
        foreach ($pce_list as $key => $value) {
                $data = array(
                        'complete_assign' => "y", 
                        );
                $this->db->where('hod_usn', $this->user_data->username);
                $this->db->where('pce_id', $value->id);
                $this->db->update('hod_approve_pce', $data);
        }
        $check_hod_assign=$this->m_project->check_hod_assign_resource($id);
        if ($check_hod_assign) {
            $work_sheet=$this->m_work_sheet->get_work_sheet_by_project_id($id);  
            if (count($work_sheet)<=0) {
                $r_sheet=$this->m_Rsheet->get_all_r_sheet_by_project_id($id);
                $sort_order=1;
                foreach ($r_sheet as $key => $value) {
                    $work_sheet_id=$this->m_work_sheet->generate_id();
                    $dat_ins = array(
                        'id' =>  $work_sheet_id,
                        'work_name' =>  $value->task,
                        'project_id' =>  $value->project_id,
                        'task_type' =>  $value->type,
                        'start' =>  0,
                        'end' =>  0,
                        'sort_order' =>  $sort_order,
                        );
                    $this->m_work_sheet->add_work_sheet($dat_ins);
                    $allow_list=$this->m_Rsheet->get_allow_list_by_r_id($value->r_id);
                    foreach ($allow_list as $key2 => $value2) {
                        $data_res_ins = array(
                            'usn' => $value2->resource_usn, 
                            'work_sheet_id' => $work_sheet_id,
                        );
                        $this->m_work_sheet->add_work_sheet_has_res($data_res_ins);
                    }
                    $sort_order+=1;
                }
            }
            $project=$this->m_project->get_project_by_id($id);
            /*if ($project->status=="Proposing"||$project->status=="Revise") {
                $dat_up = array('status' => "WIP");
                $this->m_project->update_project($dat_up,$id);
            }*/
        }

        redirect('hod');
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
        $data_view['holiday_obj']=$this->m_hr->get_all_holiday($start_car_time,$end_car_time);
        $data_view['user_leave_obj']=$this->m_hr->get_all_user_leave($start_car_time,$end_car_time);
        $position=$this->m_position->get_all_position();
        foreach ($all_child as $key => $value) {
            if (isset($position[$value->position])&&$position[$value->position]->non_productive=="n") {
                $data_view['all_child'][$key]=$value;
                $data_view['all_child'][$key]->project_wip=$this->ger_detail_resource_for_res_manager($value->username);
                $data_view['all_child'][$key]->daily_dat=$this->get_detail_res_for_overall_view($value->username);
            }
            
        }
        //print_r($data_view['all_child']);
        $this->load->view('v_header', $data_head);
        $this->load->view('hod/res_manager',$data_view);
        $this->load->view('v_footer', $data_foot);
    }
    public function utilization_report() {
        $data_view['filter'] = array();
        $data_view['filter']['start_date']=(time()-(60*60*24*30));
        if (isset($_POST['start_time'])) {
            $data_view['filter']['start_date']=$this->m_time->datepicker_to_unix($_POST['start_time']);
        }
        $data_view['filter']['end_date']=(time()+(60*60*24*30));
        if (isset($_POST['end_time'])) {
            $data_view['filter']['end_date']=$this->m_time->datepicker_to_unix($_POST['end_time'])+(60*60*24)-60;
        }

        $data_foot['table'] = "yes";
        $data_head['user_data'] = $this->user_data;
        $all_child=$this->get_all_user_child_node();
        $all_child[$this->user_data->username]=$this->user_data;
        
        $data_head['user_data'] = $this->user_data;
        $data_view['all_child'] = array();
        $position=$this->m_position->get_all_position();
        $lenght_time=(int)(($data_view['filter']['end_date']-$data_view['filter']['start_date'])/(60*60*24));
        $mod_time=(int)(($data_view['filter']['end_date']-$data_view['filter']['start_date'])%(60*60*24));
        if ($mod_time>0) {
          $lenght_time+=1;
        }
        $data_view['lenght_time']=$lenght_time;
        foreach ($all_child as $key => $value) {
            if (isset($position[$value->position])&&$position[$value->position]->non_productive=="n") {
                $data_view['all_child'][$key]=$value;
                $data_view['all_child'][$key]->report=$this->m_utilization_report->get_work_sheet_by_usn($value->username,$data_view['filter']);
                $data_view['all_child'][$key]->projects=$this->m_utilization_report->group_assign_to_project($data_view['all_child'][$key]->report);
                $data_view['all_child'][$key]->available_Hours=$this->m_utilization_report->cal_Available_Hours($data_view['filter']['start_date'],$data_view['filter']['end_date'],$value->username);
            }
            
        }
        //print_r($data_view['all_child']);
        $this->load->view('v_header', $data_head);
        $this->load->view('hod/utilization_report',$data_view);
        $this->load->view('v_footer', $data_foot);
    }
    public function utilization_reportv2() {
        $data_view['filter'] = array();
        $data_view['filter']['start_date']=(time()-(60*60*24*30));
        if (isset($_POST['start_time'])) {
            $data_view['filter']['start_date']=$this->m_time->datepicker_to_unix($_POST['start_time']);
        }
        $data_view['filter']['end_date']=(time()+(60*60*24*30));
        if (isset($_POST['end_time'])) {
            $data_view['filter']['end_date']=$this->m_time->datepicker_to_unix($_POST['end_time'])+(60*60*24)-60;
        }

        $data_foot['table'] = "yes";
        $data_head['user_data'] = $this->user_data;
        $all_child=$this->get_all_user_child_node();
        $all_child[$this->user_data->username]=$this->user_data;
        
        $data_head['user_data'] = $this->user_data;
        $data_view['all_child'] = array();
        $position=$this->m_position->get_all_position();
        $lenght_time=(int)(($data_view['filter']['end_date']-$data_view['filter']['start_date'])/(60*60*24));
        $mod_time=(int)(($data_view['filter']['end_date']-$data_view['filter']['start_date'])%(60*60*24));
        if ($mod_time>0) {
          $lenght_time+=1;
        }
        $data_view['lenght_time']=$lenght_time;
        foreach ($all_child as $key => $value) {
            if (isset($position[$value->position])&&$position[$value->position]->non_productive=="n") {
                $data_view['all_child'][$key]=$value;
                $data_view['all_child'][$key]->report=$this->m_utilization_report->get_work_sheet_by_usn($value->username,$data_view['filter']);
                $data_view['all_child'][$key]->projects=$this->m_utilization_report->group_assign_to_project($data_view['all_child'][$key]->report);
                $data_view['all_child'][$key]->available_Hours=$this->m_utilization_report->cal_Available_Hours($data_view['filter']['start_date'],$data_view['filter']['end_date'],$value->username);
            }
            
        }
        //print_r($data_view['all_child']);
        $this->load->view('v_header', $data_head);
        $this->load->view('hod/utilization_reportv2',$data_view);
        $this->load->view('v_footer', $data_foot);
    }


    //////////////////////////////////////////////////// AJAX Refion //////////////////////////////////


    public function assign_action(){
        $r_id=$this->uri->segment(3,'');
        if (isset($_POST['add_bill'])) {
            $user=$this->m_Rsheet->find_resource_by_t_type_and_hod_usn($_POST['t_type'],$this->user_data->username);
            ?>
            <tr>
                <td>
                    <div class="control-group">
                        <select id="resource_usn" class="chzn-select" name="resource_usn[]">
                            <option value="no">---please select ------</option>
                            <?
                            foreach ($user as $key => $value) {
                                                    ?>
                                <option value="<?=$value->username?>"><?=$value->nickname?></option>
                                <?
                            }
                            ?>
                        </select>
                    </div>
                </td>
                <td>
                  <a href="javascript:;" iden="no" class="btn btn-danger del_allow"><i class="icon-remove icon-white"></i></a>
                </td>
            </tr>
            <?
        }else if(isset($_POST['save'])){
            header('Content-Type: application/json');
            $json = array();
            $json['flag']="OK";      
                if (isset($_POST['resource_usn'])) {
                    foreach ($_POST['resource_usn'] as $key => $value) {
                        $data = array(
                            'r_id' => $_POST['save'], 
                            'resource_usn' => $value, 
                        );
                        if ($this->m_user->check_have_user($value)) {
                            $this->m_Rsheet->add_r_sheet_allow_hour($data);
                        }
                    }
                }
                if (isset($_POST['resource_usn_old'])) {
                    foreach ($_POST['resource_usn_old'] as $key => $value) {
                        $data = array(
                            'r_id' => $_POST['save'], 
                            'resource_usn' => $value, 
                        );
                        if ($this->m_user->check_have_user($value)) {
                            $this->m_Rsheet->update_r_sheet_allow_hour($data,$_POST['id_old'][$key]);
                        }
                    }
                }
                if (isset($_POST['del_list'])) {
                    foreach ($_POST['del_list'] as $key => $value) {
                        $this->m_Rsheet->delete_r_sheet_allow_hour($value);
                    }
                }
            echo json_encode($json);
        }else{
            $t_type=$this->uri->segment(4,'');
            $data['r_id']=$r_id;
            $data['t_type']=$t_type;
            $data['user']=$this->m_Rsheet->find_resource_by_t_type_and_hod_usn($t_type,$this->user_data->username);
            $data['task']=$this->m_Rsheet->get_r_sheet_by_id($r_id);
            $data['allow_list']=$this->m_Rsheet->get_allow_list_by_r_id($r_id);
            //print_r($data['task']);
            $this->load->view('v_header_popup');
            $this->load->view('hod/v_assign_pop',$data);
            $this->load->view('v_footer');
        }
        
    }

    public function delegate_action(){
        $r_id=$this->uri->segment(3,'');
        if (isset($_POST['add_bill'])) {
            //$user=$this->m_Rsheet->find_resource_by_t_type_and_hod_usn($_POST['t_type'],$this->user_data->username);
            $user=$this->m_user->get_all_user_by_super_usn_1_lv($this->user_data->username);
            ?>
            <tr>
                <td>
                    <div class="control-group">
                        <select id="resource_usn" class="chzn-select" name="resource_usn[]">
                            <option value="no">---please select ------</option>
                            <?
                            foreach ($user as $key => $value) {
                                                    ?>
                                <option value="<?=$value->username?>"><?=$value->nickname?></option>
                                <?
                            }
                            ?>
                        </select>
                    </div>
                </td>
                <td>
                  <a href="javascript:;" iden="no" class="btn btn-danger del_allow"><i class="icon-remove icon-white"></i></a>
                </td>
            </tr>
            <?
        }else if(isset($_POST['save'])){
            header('Content-Type: application/json');
            $json = array();
            $json['flag']="OK";      
                if (isset($_POST['resource_usn'])) {
                    foreach ($_POST['resource_usn'] as $key => $value) {
                        $data = array(
                            'r_id' => $_POST['save'],
                            'project_id' => $_POST['project_id'], 
                            'resource_usn' => $value, 
                        );
                        if ($this->m_user->check_have_user($value)) {
                            $this->m_Rsheet->add_r_sheet_delegate($data);
                        }
                    }
                }
                if (isset($_POST['resource_usn_old'])) {
                    foreach ($_POST['resource_usn_old'] as $key => $value) {
                        $data = array(
                            'r_id' => $_POST['save'], 
                            'project_id' => $_POST['project_id'], 
                            'resource_usn' => $value, 
                        );
                        if ($this->m_user->check_have_user($value)) {
                            $this->m_Rsheet->update_r_sheet_delegate($data,$_POST['id_old'][$key]);
                        }
                    }
                }
                if (isset($_POST['del_list'])) {
                    foreach ($_POST['del_list'] as $key => $value) {
                        $this->m_Rsheet->delete_r_sheet_delegate($value);
                    }
                }
            echo json_encode($json);
        }else{
            $t_type=$this->uri->segment(4,'');
            $project_id=$this->uri->segment(5,'');
            $data['r_id']=$r_id;
            $data['t_type']=$t_type;
            $data['project_id']=$project_id;
            //$data['user']=$this->m_Rsheet->find_resource_by_t_type_and_hod_usn($t_type,$this->user_data->username);
            $data['user']=$this->m_user->get_all_user_by_super_usn_1_lv($this->user_data->username);
            $data['task']=$this->m_Rsheet->get_r_sheet_by_id($r_id);
            $data['allow_list']=$this->m_Rsheet->get_delegate_list_by_r_id($r_id);
            //print_r($data['task']);
            $this->load->view('v_header_popup');
            $this->load->view('hod/v_delegate_pop',$data);
            $this->load->view('v_footer');
        }
        
    }

    public function approve_hod()
    {
        $id=$this->uri->segment(3,'');
        $data_foot['table']="yes";
        $data['a']="0";
        $data_head['user_data']=$this->user_data;
        $data['pce_doc']=$this->m_pce->get_all_pce_by_project_id($id);
        
        if (isset($this->user_data->prem['hod']) ){
            foreach ($data['pce_doc'] as $key => $value) {
                    $pce = array(
                        'approve' => 'y', 
                        'approve_time' => time(), 
                        );
                    $hod_list=$this->m_pce->get_hod_approve_by_pce_id($data['pce_doc'][$key]->id);
                    if(sizeof($hod_list) == 0)
                    {
                        break;
                    }
                        else
                        {
                            $this->m_pce->update_hod_approve($pce,$hod_list[$this->user_data->username]->id);
                        }
                    
                    
            }
               echo "<script type='text/javascript'>alert('success');</script>";                  
            }else{
               echo "<script type='text/javascript'>alert('error');</script>";
               
            }
        
        redirect('hod', 'refresh');
        
    }
    public function app_bat()
    {
        header('Content-Type: application/json');
            $json = array();
            $json['flag']="OK";      
        if (isset($_POST['app_bat'])) {
                foreach ($_POST['app_bat'] as $key => $value) {
                    $data['pce_doc']=$this->m_pce->get_all_pce_by_project_id($value);
                    if (isset($this->user_data->prem['hod']) ){
                        foreach ($data['pce_doc'] as $bkey => $bvalue) {
                                $pce = array(
                                    'approve' => 'y', 
                                    'approve_time' => time(), 
                                    );
                                $hod_list=$this->m_pce->get_hod_approve_by_pce_id($data['pce_doc'][$bkey]->id);
                                if(sizeof($hod_list) == 0)
                                {
                                    break;
                                }
                                    else
                                    {
                                        $this->m_pce->update_hod_approve($pce,$hod_list[$this->user_data->username]->id);
                                    }
                                
                                
                        }                
                    }else{
                       $json['flag']="no prem";
                       
                    }
                }
        }else{
            $json['flag']="Wrong POST";
        }    
        echo json_encode($json);
        
    }
    public function show_allow_resource()
    {
        ?>
            <tr class="resource_show">
                <td colspan="1">
                    <?
                $r_id=$_POST['r_id'];
                $allow_list=$this->m_Rsheet->get_allow_list_by_r_id($r_id);
                $delegate_list=$this->m_Rsheet->get_delegate_list_by_r_id($r_id);
                $al_num=count($allow_list);
                foreach ($allow_list as $key => $value) {
                    $user=$this->m_user->get_user_by_login_name($value->resource_usn);
                     if ($al_num==$key+1) {
                         echo $user->nickname;
                     }else{
                          echo $user->nickname.",";
                      }            
                            
                }
                ?>
                </td>
                <td colspan="1" style="color:red">
                    <?
                    $al_num=count($delegate_list);
                foreach ($delegate_list as $key => $value) {
                    $user=$this->m_user->get_user_by_login_name($value->resource_usn);
                     if ($al_num==$key+1) {
                         echo $user->nickname;
                     }else{
                          echo $user->nickname.",";
                      }            
                            
                }
                ?>
                </td>
            </tr>
            <?
    }
    public function tap_three(){
        $filter = array();
        $filter['start_date']=0;
        if (isset($_POST['start_time'])) {
            $filter['start_date']=$this->m_time->datepicker_to_unix($_POST['start_time']);
        }
        $filter['end_date']=(time()+(60*60*24*300));
        if (isset($_POST['end_time'])) {
            $filter['end_date']=$this->m_time->datepicker_to_unix($_POST['end_time']);
        }
        $filter['project_cs']="all";
        if (isset($_POST['project_cs'])) {
            $filter['project_cs']=$_POST['project_cs'];
        }
        $project_list_ready_assign=$this->m_project->get_project_by_ready_assign_res(true,true,$this->user_data->username,"all",$filter);
         $countnum=0;foreach ($project_list_ready_assign as $key=> $value) { $countnum+=1;
                                                          $company=$this->m_company->get_company_by_id($value->project_client);
                                                          $company_bu=$this->m_company->get_bu_by_id($value->project_bu);
                                                          $oc_list=$this->m_oc->get_all_oc_by_project_id($value->project_id);
                                                          $pce_list=$this->m_pce->get_all_pce_by_project_id($value->project_id);
                                                          $oc_sum_value=0;
                                                          $oc_sum_billed=0;
                                                          $outsource_bil=$this->m_project->get_sum_outsource_bill_by_project_id($value->project_id);
                                                          $sum_allocate=$this->m_project->get_sum_allocate_budget_by_project_id($value->project_id);
                                                          $is_delegate=$this->m_Rsheet->check_delegate_project_by_project_id($value->project_id);
                                                         ?>
                                                         <tr>
                                                              <td><? echo $countnum; ?></td>
                                                              <td style="word-wrap: normal;min-width:400px;">
                                                                <a href="<? echo site_url('hod/assign_resource/'.$value->project_id)?>" class=""><?=$value->project_name?><i class=" icon-pencil"></i></a>
                                                                <br>
                                                              <? echo $company->name." ".$company_bu->bu_name;
                                                              ?><br>
                                                              <div id="Modal_<?=$value->project_id?>" class="modal hide in" aria-hidden="false">
                                                                <div class="modal-header">
                                                                  <button data-dismiss="modal" class="close" type="button">×</button>
                                                                  <h3><? echo $value->project_name; ?> OC List </h3>
                                                                </div>
                                                                <div class="modal-body">
                                                                  <table class="just-table">
                                                                    <?
                                                                      $pce_check_list = array();
                                                                      foreach ($oc_list as $oc_key => $oc_value) {
                                                                        $pce_check_list[$oc_value->pce_id]="yes";
                                                                        $oc_bil=$this->m_oc->get_oc_bill_by_oc_id($oc_value->id);
                                                                        ?>
                                                                        <tr>
                                                                          <td><?echo $oc_value->oc_no;?></td>
                                                                          <td>
                                                                          <?
                                                                          $so_arr = array();
                                                                          $oc_sum_value+=(int)$oc_value->oc_amount;
                                                                          foreach ($oc_bil as $oc_bil_k => $oc_bil_v) {
                                                                            if ($oc_bil_v->collected=="y") {
                                                                              if (!isset($so_arr[$oc_bil_v->so])) {
                                                                                $so_arr[$oc_bil_v->so]=$oc_bil_v->so;
                                                                                echo $oc_bil_v->so."<br>";
                                                                              }
                                                                              $oc_sum_billed+=(int)$oc_bil_v->paid_amount;
                                                                            }                                                      
                                                                          }
                                                                          ?>
                                                                          </td>
                                                                        </tr>
                                                                        <?
                                                                      }
                                                                        foreach ($pce_list as $pce_key => $pce_value) {
                                                                          if (!isset($pce_check_list[$pce_value->id])) {
                                                                            $oc_sum_value+=(int)$pce_value->pce_amount;
                                                                          }                                                            
                                                                        }
                                                                      ?>
                                                                  </table>
                                                                </div>
                                                              </div>
                                                              <table class="just-table">
                                                                <tr>
                                                                  <td><?
                                                                  echo number_format($oc_sum_value, 0, '.', ',')."(<font class='green-f'>".number_format($oc_sum_billed, 0, '.', ',')."</font>)";
                                                                  ?></td>
                                                                  <td style="text-align:right">
                                                                    <a href="#Modal_<?=$value->project_id?>" data-toggle="modal">View OC</a>
                                                                  </td>
                                                                </tr>
                                                              </table>
                                                              
                                                              </td>
                                                              <td><? echo $this->m_time->unix_to_datepicker($value->project_start)." - ".$this->m_time->unix_to_datepicker($value->project_end); ?>
                                                              <br>
                                                              <?
                                                                $lenght_time=(int)(($value->project_end-$value->project_start)/(60*60*24));
                                                                $until_now_time=(int)((time()-$value->project_start)/(60*60*24));
                                                                $cur_lenght=0;
                                                                if ($until_now_time>=$lenght_time) {
                                                                  $cur_lenght=100;
                                                                }else{
                                                                  $cur_lenght=(int)(($until_now_time/$lenght_time)*100);
                                                                }                                                    
                                                                echo $lenght_time." Days ";
                                                                ?>
                                                                <div class="progress">
                                                                  <div style="width: <?=$cur_lenght?>%;" class="bar"><?=$cur_lenght?>%</div>
                                                                </div>
                                                              </td>
                                                                                                               
                                                              <td><? echo $value->status; ?></td>
                                                              <td><? echo $value->status_bill; ?></td>
                                                              <td >
                                                                <div class="progress ">
                                                                  <div style="width: 100%;" class="bar">
                                                                  <table class="just-table">
                                                                    <tr>
                                                                      <td style="width:120px">BUDGET</td>
                                                                      <td style="width:60px"><?$sum_budget=$this->m_project->get_sum_budget_by_project_id($value->project_id);echo $sum_budget;?></td>
                                                                    </tr>
                                                                  </table>                                                      
                                                                  </div>
                                                                </div>
                                                                <?
                                                                if ($sum_budget<=0) {
                                                                  $sum_budget=1;
                                                                }
                                                                $p_allocate=(int)(($sum_allocate['sum_all']/$sum_budget)*100);
                                                                $p_spend=(int)(($sum_allocate['sum_spend']/$sum_budget)*100);
                                                                if ($p_spend>100) {
                                                                  $p_spend=100;
                                                                }
                                                                if ($p_allocate>100) {
                                                                  $p_allocate=100;
                                                                }
                                                                ?>
                                                                <div class="progress progress-warning">
                                                                  <div style="width: <?=$p_allocate?>%;" class="bar">
                                                                  <table class="just-table">
                                                                    <tr>
                                                                      <td style="width:120px">ALLOCATE</td>
                                                                      <td style="width:60px"><?echo $sum_allocate['sum_all'];?></td>
                                                                    </tr>
                                                                  </table>
                                                                  </div>
                                                                </div>
                                                                <div class="progress progress-success">
                                                                  <div style="width: <?=$p_spend?>%;" class="bar">
                                                                  <table class="just-table">
                                                                    <tr>
                                                                      <td style="width:120px">SPENT</td>
                                                                      <td style="width:60px"><?echo $sum_allocate['sum_spend'];?></td>
                                                                    </tr>
                                                                  </table>
                                                                  
                                                                  </div>
                                                                </div>
                                                              </td>
                                                              <td><?
                                                              echo number_format($outsource_bil['sum_all'], 0, '.', ',')."<br>(<font class='green-f'>".number_format($outsource_bil['sum_paid'], 0, '.', ',')."</font>)";
                                                              ?></td>
                                                              <td>
                                                                <?
                                                               if ($value->base_approve_stat['csd']===true) {                                                    
                                                                    ?>
                                                                    <p class="project-status-ok"><i class="icon-ok icon-white"></i>CSD</p>
                                                                    <?
                                                                }else if($value->base_approve_stat['csd']=="reject"){
                                                                    ?>
                                                                    <p class="project-status-reject"><i class="icon-remove icon-white"></i>CSD </p>
                                                                    <?
                                                                }else{
                                                                    ?>
                                                                    <p class="project-status">CSD </p>
                                                                    <?
                                                                }
                                                                if($value->base_approve_stat['hod']===true){
                                                                    ?>
                                                                    <p class="project-status-ok"><i class="icon-ok icon-white"></i>HOD </p>
                                                                    <?
                                                                }else if($value->base_approve_stat['hod']=="reject"){
                                                                    ?>
                                                                    <p class="project-status-reject"><i class="icon-remove icon-white"></i>HOD </p>
                                                                    <?
                                                                }else{
                                                                    ?>
                                                                    <p class="project-status">HOD </p>
                                                                    <?
                                                                }
                                                                
                                                                if ($value->base_approve_stat['fc']===true) {                                                    
                                                                    ?>
                                                                    <p class="project-status-ok"><i class="icon-ok icon-white"></i>FC </p>
                                                                    <?
                                                                }else if($value->base_approve_stat['fc']=="reject"){
                                                                    ?>
                                                                    <p class="project-status-reject"><i class="icon-remove icon-white"></i>FC </p>
                                                                    <?
                                                                }else{
                                                                    ?>
                                                                    <p class="project-status">FC </p>
                                                                    <?
                                                                }
                                                                if ($value->base_approve_stat['fc_oc']===true) {                                                    
                                                                    ?>
                                                                    <p class="project-status-ok"><i class="icon-ok icon-white"></i>OC </p>
                                                                    <?
                                                                }else if($value->base_approve_stat['fc_oc']=="reject"){
                                                                    ?>
                                                                    <p class="project-status-reject"><i class="icon-remove icon-white"></i>OC </p>
                                                                    <?
                                                                }else{
                                                                    ?>
                                                                    <p class="project-status">OC </p>
                                                                    <?
                                                                }
                                                                ?>
                                                              </td>
                                                              <td>                                                            
                                                              <?
                                                              $manager=$this->m_user->get_user_by_login_name($value->project_cs);
                                                                ?>
                                                                <a href="<? echo site_url('hod/assign_resource/'.$value->project_id)?>" class=""><?=$manager->nickname?><i class=" icon-pencil"></i></a><br>                                                                
                                                                <a href="<? echo site_url('hod/assign_resource/'.$value->project_id)?>" class="btn btn-info btn-xs">View/Assign</a>
                                                              <br>                
                                                                <a class="fancybox" data-fancybox-type="iframe" href="<?=site_url("project/project_note/".$value->project_id)?>">
                                                                  <img style="width:30px" class="no-margin-left" src="<?echo site_url('img/project_note.png')?>">
                                                                </a>
                                                                <a class="fancybox" data-fancybox-type="iframe" href="<?=site_url("project/finan_note/".$value->project_id)?>">
                                                                  <img style="width:30px" class="no-margin-left" src="<?echo site_url('img/finance_note.png')?>">
                                                                </a>
                                                                <br>
                                                                <?
                                                                if ($is_delegate) {
                                                                    ?><p class="project-status-ok"><i class="icon-ok icon-white"></i>Delegate </p><?
                                                                }
                                                                ?>
                                                              </td>
                                                        </tr>
                                                        <? } 
    }
    public function tap_four(){
        $filter = array();
        $filter['start_date']=0;
        if (isset($_POST['start_time'])) {
            $filter['start_date']=$this->m_time->datepicker_to_unix($_POST['start_time']);
        }
        $filter['end_date']=(time()+(60*60*24*300));
        if (isset($_POST['end_time'])) {
            $filter['end_date']=$this->m_time->datepicker_to_unix($_POST['end_time']);
        }
        $filter['project_cs']="all";
        if (isset($_POST['project_cs'])) {
            $filter['project_cs']=$_POST['project_cs'];
        }
        $project_list_ready_assign=$this->m_project->get_pre_oc_assign($this->user_data->username,"all",$filter);
         $countnum=0;foreach ($project_list_ready_assign as $key=> $value) { $countnum+=1;
                                                          $company=$this->m_company->get_company_by_id($value->project_client);
                                                          $company_bu=$this->m_company->get_bu_by_id($value->project_bu);
                                                          $oc_list=$this->m_oc->get_all_oc_by_project_id($value->project_id);
                                                          $pce_list=$this->m_pce->get_all_pce_by_project_id($value->project_id);
                                                          $oc_sum_value=0;
                                                          $oc_sum_billed=0;
                                                          $outsource_bil=$this->m_project->get_sum_outsource_bill_by_project_id($value->project_id);
                                                          $sum_allocate=$this->m_project->get_sum_allocate_budget_by_project_id($value->project_id);
                                                          $is_delegate=$this->m_Rsheet->check_delegate_project_by_project_id($value->project_id);
                                                         ?>
                                                         <tr>
                                                              <td><? echo $countnum; ?></td>
                                                              <td style="word-wrap: normal;min-width:400px;">
                                                                <a href="<? echo site_url('hod/assign_resource/'.$value->project_id)?>" class=""><?=$value->project_name?><i class=" icon-pencil"></i></a>
                                                                <br>
                                                              <? echo $company->name." ".$company_bu->bu_name;
                                                              ?><br>
                                                              <div id="Modal_<?=$value->project_id?>" class="modal hide in" aria-hidden="false">
                                                                <div class="modal-header">
                                                                  <button data-dismiss="modal" class="close" type="button">×</button>
                                                                  <h3><? echo $value->project_name; ?> OC List </h3>
                                                                </div>
                                                                <div class="modal-body">
                                                                  <table class="just-table">
                                                                    <?
                                                                      $pce_check_list = array();
                                                                      foreach ($oc_list as $oc_key => $oc_value) {
                                                                        $pce_check_list[$oc_value->pce_id]="yes";
                                                                        $oc_bil=$this->m_oc->get_oc_bill_by_oc_id($oc_value->id);
                                                                        ?>
                                                                        <tr>
                                                                          <td><?echo $oc_value->oc_no;?></td>
                                                                          <td>
                                                                          <?
                                                                          $so_arr = array();
                                                                          $oc_sum_value+=(int)$oc_value->oc_amount;
                                                                          foreach ($oc_bil as $oc_bil_k => $oc_bil_v) {
                                                                            if ($oc_bil_v->collected=="y") {
                                                                              if (!isset($so_arr[$oc_bil_v->so])) {
                                                                                $so_arr[$oc_bil_v->so]=$oc_bil_v->so;
                                                                                echo $oc_bil_v->so."<br>";
                                                                              }
                                                                              $oc_sum_billed+=(int)$oc_bil_v->paid_amount;
                                                                            }                                                      
                                                                          }
                                                                          ?>
                                                                          </td>
                                                                        </tr>
                                                                        <?
                                                                      }
                                                                        foreach ($pce_list as $pce_key => $pce_value) {
                                                                          if (!isset($pce_check_list[$pce_value->id])) {
                                                                            $oc_sum_value+=(int)$pce_value->pce_amount;
                                                                          }                                                            
                                                                        }
                                                                      ?>
                                                                  </table>
                                                                </div>
                                                              </div>
                                                              <table class="just-table">
                                                                <tr>
                                                                  <td><?
                                                                  echo number_format($oc_sum_value, 0, '.', ',')."(<font class='green-f'>".number_format($oc_sum_billed, 0, '.', ',')."</font>)";
                                                                  ?></td>
                                                                  <td style="text-align:right">
                                                                    <a href="#Modal_<?=$value->project_id?>" data-toggle="modal">View OC</a>
                                                                  </td>
                                                                </tr>
                                                              </table>
                                                              
                                                              </td>
                                                              <td><? echo $this->m_time->unix_to_datepicker($value->project_start)." - ".$this->m_time->unix_to_datepicker($value->project_end); ?>
                                                              <br>
                                                              <?
                                                                $lenght_time=(int)(($value->project_end-$value->project_start)/(60*60*24));
                                                                $until_now_time=(int)((time()-$value->project_start)/(60*60*24));
                                                                $cur_lenght=0;
                                                                if ($until_now_time>=$lenght_time) {
                                                                  $cur_lenght=100;
                                                                }else{
                                                                  $cur_lenght=(int)(($until_now_time/$lenght_time)*100);
                                                                }                                                    
                                                                echo $lenght_time." Days ";
                                                                ?>
                                                                <div class="progress">
                                                                  <div style="width: <?=$cur_lenght?>%;" class="bar"><?=$cur_lenght?>%</div>
                                                                </div>
                                                              </td>
                                                                                                               
                                                              <td><? echo $value->status; ?></td>
                                                              <td><? echo $value->status_bill; ?></td>
                                                              <td >
                                                                <div class="progress ">
                                                                  <div style="width: 100%;" class="bar">
                                                                  <table class="just-table">
                                                                    <tr>
                                                                      <td style="width:120px">BUDGET</td>
                                                                      <td style="width:60px"><?$sum_budget=$this->m_project->get_sum_budget_by_project_id($value->project_id);echo $sum_budget;?></td>
                                                                    </tr>
                                                                  </table>                                                      
                                                                  </div>
                                                                </div>
                                                                <?
                                                                if ($sum_budget<=0) {
                                                                  $sum_budget=1;
                                                                }
                                                                $p_allocate=(int)(($sum_allocate['sum_all']/$sum_budget)*100);
                                                                $p_spend=(int)(($sum_allocate['sum_spend']/$sum_budget)*100);
                                                                if ($p_spend>100) {
                                                                  $p_spend=100;
                                                                }
                                                                if ($p_allocate>100) {
                                                                  $p_allocate=100;
                                                                }
                                                                ?>
                                                                <div class="progress progress-warning">
                                                                  <div style="width: <?=$p_allocate?>%;" class="bar">
                                                                  <table class="just-table">
                                                                    <tr>
                                                                      <td style="width:120px">ALLOCATE</td>
                                                                      <td style="width:60px"><?echo $sum_allocate['sum_all'];?></td>
                                                                    </tr>
                                                                  </table>
                                                                  </div>
                                                                </div>
                                                                <div class="progress progress-success">
                                                                  <div style="width: <?=$p_spend?>%;" class="bar">
                                                                  <table class="just-table">
                                                                    <tr>
                                                                      <td style="width:120px">SPENT</td>
                                                                      <td style="width:60px"><?echo $sum_allocate['sum_spend'];?></td>
                                                                    </tr>
                                                                  </table>
                                                                  
                                                                  </div>
                                                                </div>
                                                              </td>
                                                              <td><?
                                                              echo number_format($outsource_bil['sum_all'], 0, '.', ',')."<br>(<font class='green-f'>".number_format($outsource_bil['sum_paid'], 0, '.', ',')."</font>)";
                                                              ?></td>
                                                              <td>
                                                                <?
                                                               if ($value->base_approve_stat['csd']===true) {                                                    
                                                                    ?>
                                                                    <p class="project-status-ok"><i class="icon-ok icon-white"></i>CSD</p>
                                                                    <?
                                                                }else if($value->base_approve_stat['csd']=="reject"){
                                                                    ?>
                                                                    <p class="project-status-reject"><i class="icon-remove icon-white"></i>CSD </p>
                                                                    <?
                                                                }else{
                                                                    ?>
                                                                    <p class="project-status">CSD </p>
                                                                    <?
                                                                }
                                                                if($value->base_approve_stat['hod']===true){
                                                                    ?>
                                                                    <p class="project-status-ok"><i class="icon-ok icon-white"></i>HOD </p>
                                                                    <?
                                                                }else if($value->base_approve_stat['hod']=="reject"){
                                                                    ?>
                                                                    <p class="project-status-reject"><i class="icon-remove icon-white"></i>HOD </p>
                                                                    <?
                                                                }else{
                                                                    ?>
                                                                    <p class="project-status">HOD </p>
                                                                    <?
                                                                }
                                                                
                                                                if ($value->base_approve_stat['fc']===true) {                                                    
                                                                    ?>
                                                                    <p class="project-status-ok"><i class="icon-ok icon-white"></i>FC </p>
                                                                    <?
                                                                }else if($value->base_approve_stat['fc']=="reject"){
                                                                    ?>
                                                                    <p class="project-status-reject"><i class="icon-remove icon-white"></i>FC </p>
                                                                    <?
                                                                }else{
                                                                    ?>
                                                                    <p class="project-status">FC </p>
                                                                    <?
                                                                }
                                                                if ($value->base_approve_stat['fc_oc']===true) {                                                    
                                                                    ?>
                                                                    <p class="project-status-ok"><i class="icon-ok icon-white"></i>OC </p>
                                                                    <?
                                                                }else if($value->base_approve_stat['fc_oc']=="reject"){
                                                                    ?>
                                                                    <p class="project-status-reject"><i class="icon-remove icon-white"></i>OC </p>
                                                                    <?
                                                                }else{
                                                                    ?>
                                                                    <p class="project-status">OC </p>
                                                                    <?
                                                                }
                                                                ?>
                                                              </td>
                                                              <td>                                                            
                                                              <?
                                                              $manager=$this->m_user->get_user_by_login_name($value->project_cs);
                                                                ?>
                                                                <a href="<? echo site_url('hod/assign_resource/'.$value->project_id)?>" class=""><?=$manager->nickname?><i class=" icon-pencil"></i></a><br>                                                                
                                                                <a href="<? echo site_url('hod/assign_resource/'.$value->project_id)?>" class="btn btn-info btn-xs">View/Assign</a>
                                                              <br>                
                                                                <a class="fancybox" data-fancybox-type="iframe" href="<?=site_url("project/project_note/".$value->project_id)?>">
                                                                  <img style="width:30px" class="no-margin-left" src="<?echo site_url('img/project_note.png')?>">
                                                                </a>
                                                                <a class="fancybox" data-fancybox-type="iframe" href="<?=site_url("project/finan_note/".$value->project_id)?>">
                                                                  <img style="width:30px" class="no-margin-left" src="<?echo site_url('img/finance_note.png')?>">
                                                                </a>
                                                                <br>
                                                                <?
                                                                if ($is_delegate) {
                                                                    ?><p class="project-status-ok"><i class="icon-ok icon-white"></i>Delegate </p><?
                                                                }
                                                                ?>
                                                              </td>
                                                        </tr>
                                                        <? } 
    }

    public function tap_two(){
        $filter = array();
        $filter['start_date']=0;
        if (isset($_POST['start_time'])) {
            $filter['start_date']=$this->m_time->datepicker_to_unix($_POST['start_time']);
        }
        $filter['end_date']=(time()+(60*60*24*300));
        if (isset($_POST['end_time'])) {
            $filter['end_date']=$this->m_time->datepicker_to_unix($_POST['end_time']);
        }
        $filter['project_cs']="all";
        if (isset($_POST['project_cs'])) {
            $filter['project_cs']=$_POST['project_cs'];
        }
        $project_list_ready_assign_nowip=$this->m_project->get_project_by_ready_assign_res(false,false,$this->user_data->username,"n",$filter);        
         $countnum=0;foreach ($project_list_ready_assign_nowip as $key=> $value) { $countnum+=1;
                                                          $company=$this->m_company->get_company_by_id($value->project_client);
                                                          $company_bu=$this->m_company->get_bu_by_id($value->project_bu);
                                                          $oc_list=$this->m_oc->get_all_oc_by_project_id($value->project_id);
                                                          $pce_list=$this->m_pce->get_all_pce_by_project_id($value->project_id);
                                                          $oc_sum_value=0;
                                                          $oc_sum_billed=0;
                                                          $outsource_bil=$this->m_project->get_sum_outsource_bill_by_project_id($value->project_id);
                                                          $sum_allocate=$this->m_project->get_sum_allocate_budget_by_project_id($value->project_id);
                                                          $is_delegate=$this->m_Rsheet->check_delegate_project_by_project_id($value->project_id);
                                                         ?>
                                                         <tr>
                                                              <td><? echo $countnum; ?></td>
                                                              <td style="word-wrap: normal;min-width:400px;">
                                                                <a href="<? echo site_url('hod/assign_resource/'.$value->project_id)?>" class=""><?=$value->project_name?><i class=" icon-pencil"></i></a>
                                                                <br>
                                                              <? echo $company->name." ".$company_bu->bu_name;
                                                              ?><br>
                                                              <div id="Modal_<?=$value->project_id?>" class="modal hide in" aria-hidden="false">
                                                                <div class="modal-header">
                                                                  <button data-dismiss="modal" class="close" type="button">×</button>
                                                                  <h3><? echo $value->project_name; ?> OC List </h3>
                                                                </div>
                                                                <div class="modal-body">
                                                                  <table class="just-table">
                                                                    <?
                                                                      $pce_check_list = array();
                                                                      foreach ($oc_list as $oc_key => $oc_value) {
                                                                        $pce_check_list[$oc_value->pce_id]="yes";
                                                                        $oc_bil=$this->m_oc->get_oc_bill_by_oc_id($oc_value->id);
                                                                        ?>
                                                                        <tr>
                                                                          <td><?echo $oc_value->oc_no;?></td>
                                                                          <td>
                                                                          <?
                                                                          $so_arr = array();
                                                                          $oc_sum_value+=(int)$oc_value->oc_amount;
                                                                          foreach ($oc_bil as $oc_bil_k => $oc_bil_v) {
                                                                            if ($oc_bil_v->collected=="y") {
                                                                              if (!isset($so_arr[$oc_bil_v->so])) {
                                                                                $so_arr[$oc_bil_v->so]=$oc_bil_v->so;
                                                                                echo $oc_bil_v->so."<br>";
                                                                              }
                                                                              $oc_sum_billed+=(int)$oc_bil_v->paid_amount;
                                                                            }                                                      
                                                                          }
                                                                          ?>
                                                                          </td>
                                                                        </tr>
                                                                        <?
                                                                      }
                                                                        foreach ($pce_list as $pce_key => $pce_value) {
                                                                          if (!isset($pce_check_list[$pce_value->id])) {
                                                                            $oc_sum_value+=(int)$pce_value->pce_amount;
                                                                          }                                                            
                                                                        }
                                                                      ?>
                                                                  </table>
                                                                </div>
                                                              </div>
                                                              <table class="just-table">
                                                                <tr>
                                                                  <td><?
                                                                  echo number_format($oc_sum_value, 0, '.', ',')."(<font class='green-f'>".number_format($oc_sum_billed, 0, '.', ',')."</font>)";
                                                                  ?></td>
                                                                  <td style="text-align:right">
                                                                    <a href="#Modal_<?=$value->project_id?>" data-toggle="modal">View OC</a>
                                                                  </td>
                                                                </tr>
                                                              </table>
                                                              
                                                              </td>
                                                              <td><? echo $this->m_time->unix_to_datepicker($value->project_start)." - ".$this->m_time->unix_to_datepicker($value->project_end); ?>
                                                              <br>
                                                              <?
                                                                $lenght_time=(int)(($value->project_end-$value->project_start)/(60*60*24));
                                                                $until_now_time=(int)((time()-$value->project_start)/(60*60*24));
                                                                $cur_lenght=0;
                                                                if ($lenght_time==0) {
                                                      $lenght_time=1;
                                                    }
                                                                if ($until_now_time>=$lenght_time) {
                                                                  $cur_lenght=100;
                                                                }else{
                                                                  $cur_lenght=(int)(($until_now_time/$lenght_time)*100);
                                                                }                                                    
                                                                echo $lenght_time." Days ";
                                                                ?>
                                                                <div class="progress">
                                                                  <div style="width: <?=$cur_lenght?>%;" class="bar"><?=$cur_lenght?>%</div>
                                                                </div>
                                                              </td>
                                                                                                               
                                                              <td><? echo $value->status; ?></td>
                                                              <td><? echo $value->status_bill; ?></td>
                                                              <td >
                                                                <div class="progress ">
                                                                  <div style="width: 100%;" class="bar">
                                                                  <table class="just-table">
                                                                    <tr>
                                                                      <td style="width:120px">BUDGET</td>
                                                                      <td style="width:60px"><?$sum_budget=$this->m_project->get_sum_budget_by_project_id($value->project_id);echo $sum_budget;?></td>
                                                                    </tr>
                                                                  </table>                                                      
                                                                  </div>
                                                                </div>
                                                                <?
                                                                if ($sum_budget<=0) {
                                                                  $sum_budget=1;
                                                                }
                                                                $p_allocate=(int)(($sum_allocate['sum_all']/$sum_budget)*100);
                                                                $p_spend=(int)(($sum_allocate['sum_spend']/$sum_budget)*100);
                                                                if ($p_spend>100) {
                                                                  $p_spend=100;
                                                                }
                                                                if ($p_allocate>100) {
                                                                  $p_allocate=100;
                                                                }
                                                                ?>
                                                                <div class="progress progress-warning">
                                                                  <div style="width: <?=$p_allocate?>%;" class="bar">
                                                                  <table class="just-table">
                                                                    <tr>
                                                                      <td style="width:120px">ALLOCATE</td>
                                                                      <td style="width:60px"><?echo $sum_allocate['sum_all'];?></td>
                                                                    </tr>
                                                                  </table>
                                                                  </div>
                                                                </div>
                                                                <div class="progress progress-success">
                                                                  <div style="width: <?=$p_spend?>%;" class="bar">
                                                                  <table class="just-table">
                                                                    <tr>
                                                                      <td style="width:120px">SPENT</td>
                                                                      <td style="width:60px"><?echo $sum_allocate['sum_spend'];?></td>
                                                                    </tr>
                                                                  </table>
                                                                  
                                                                  </div>
                                                                </div>
                                                              </td>
                                                              <td><?
                                                              echo number_format($outsource_bil['sum_all'], 0, '.', ',')."<br>(<font class='green-f'>".number_format($outsource_bil['sum_paid'], 0, '.', ',')."</font>)";
                                                              ?></td>
                                                              <td>
                                                                <?
                                                               if ($value->base_approve_stat['csd']===true) {                                                    
                                                                    ?>
                                                                    <p class="project-status-ok"><i class="icon-ok icon-white"></i>CSD</p>
                                                                    <?
                                                                }else if($value->base_approve_stat['csd']=="reject"){
                                                                    ?>
                                                                    <p class="project-status-reject"><i class="icon-remove icon-white"></i>CSD </p>
                                                                    <?
                                                                }else{
                                                                    ?>
                                                                    <p class="project-status">CSD </p>
                                                                    <?
                                                                }
                                                                if($value->base_approve_stat['hod']===true){
                                                                    ?>
                                                                    <p class="project-status-ok"><i class="icon-ok icon-white"></i>HOD </p>
                                                                    <?
                                                                }else if($value->base_approve_stat['hod']=="reject"){
                                                                    ?>
                                                                    <p class="project-status-reject"><i class="icon-remove icon-white"></i>HOD </p>
                                                                    <?
                                                                }else{
                                                                    ?>
                                                                    <p class="project-status">HOD </p>
                                                                    <?
                                                                }
                                                                
                                                                if ($value->base_approve_stat['fc']===true) {                                                    
                                                                    ?>
                                                                    <p class="project-status-ok"><i class="icon-ok icon-white"></i>FC </p>
                                                                    <?
                                                                }else if($value->base_approve_stat['fc']=="reject"){
                                                                    ?>
                                                                    <p class="project-status-reject"><i class="icon-remove icon-white"></i>FC </p>
                                                                    <?
                                                                }else{
                                                                    ?>
                                                                    <p class="project-status">FC </p>
                                                                    <?
                                                                }
                                                                if ($value->base_approve_stat['fc_oc']===true) {                                                    
                                                                    ?>
                                                                    <p class="project-status-ok"><i class="icon-ok icon-white"></i>OC </p>
                                                                    <?
                                                                }else if($value->base_approve_stat['fc_oc']=="reject"){
                                                                    ?>
                                                                    <p class="project-status-reject"><i class="icon-remove icon-white"></i>OC </p>
                                                                    <?
                                                                }else{
                                                                    ?>
                                                                    <p class="project-status">OC </p>
                                                                    <?
                                                                }
                                                                ?>
                                                              </td>
                                                              <td>                                                            
                                                              <?
                                                              $manager=$this->m_user->get_user_by_login_name($value->project_cs);
                                                                ?>
                                                                <a href="<? echo site_url('hod/assign_resource/'.$value->project_id)?>" class=""><?=$manager->nickname?><i class=" icon-pencil"></i></a><br>                                                                
                                                                <a href="<? echo site_url('hod/assign_resource/'.$value->project_id)?>" class="btn btn-info btn-xs">View/Assign</a>
                                                              <br>                
                                                                <a class="fancybox" data-fancybox-type="iframe" href="<?=site_url("project/project_note/".$value->project_id)?>">
                                                                  <img style="width:30px" class="no-margin-left" src="<?echo site_url('img/project_note.png')?>">
                                                                </a>
                                                                <a class="fancybox" data-fancybox-type="iframe" href="<?=site_url("project/finan_note/".$value->project_id)?>">
                                                                  <img style="width:30px" class="no-margin-left" src="<?echo site_url('img/finance_note.png')?>">
                                                                </a>
                                                                <br>
                                                                <?
                                                                if ($is_delegate) {
                                                                    ?><p class="project-status-ok"><i class="icon-ok icon-white"></i>Delegate </p><?
                                                                }
                                                                ?>
                                                              </td>
                                                        </tr>
                                                        <? } 
    }
    ////////////////////////////////// etc function ////////////////////
    function get_all_user_child_node(){
        $usn_under_super=$this->m_user->get_all_user_by_super_usn_all_lv($this->user_data->username,$this->user_data->username);
        $child_ready=$this->m_Rsheet->change_node_user_to_array($usn_under_super);
        return $child_ready;
    }
    function ger_detail_resource_for_res_manager($username){
        $current_day_time=$this->m_time->datepicker_to_unix(date("d/m/Y"));
        $start_car_time=$current_day_time-(60*60*24*10);
        $end_car_time=$current_day_time+(60*60*24*60);
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
    function get_detail_res_for_overall_view($username){
        $current_day_time=$this->m_time->datepicker_to_unix(date("d/m/Y"));
        $start_car_time=$current_day_time-(60*60*24*10);
        $end_car_time=$current_day_time+(60*60*24*60);
        $assign_obj=$this->m_work_sheet->get_res_assign_by_usn_with_start_end($username,$start_car_time,$end_car_time);
        return $assign_obj;
    }

























    public function utilization_report_excel()
    {
        $filter = array();
        $filter['start_date']=(time()-(60*60*24*30));
        if (isset($_POST['start_time'])) {
            $filter['start_date']=$this->m_time->datepicker_to_unix($_POST['start_time']);
        }
        $filter['end_date']=(time()+(60*60*24*30));
        if (isset($_POST['end_time'])) {
            $filter['end_date']=$this->m_time->datepicker_to_unix($_POST['end_time'])+(60*60*24)-60;
        }
        $all_child1=$this->get_all_user_child_node();
        $all_child1[$this->user_data->username]=$this->user_data;
        
        $data_head['user_data'] = $this->user_data;
        $all_child = array();
        $position=$this->m_position->get_all_position();
        $lenght_time=(int)(($filter['end_date']-$filter['start_date'])/(60*60*24));
        $mod_time=(int)(($filter['end_date']-$filter['start_date'])%(60*60*24));
        if ($mod_time>0) {
          $lenght_time+=1;
        }
        $lenght_time=$lenght_time;
        foreach ($all_child1 as $key => $value) {
            if (isset($position[$value->position])&&$position[$value->position]->non_productive=="n") {
                $all_child[$key]=$value;
                $all_child[$key]->report=$this->m_utilization_report->get_work_sheet_by_usn($value->username,$filter);
                $all_child[$key]->projects=$this->m_utilization_report->group_assign_to_project($all_child[$key]->report);
                $all_child[$key]->available_Hours=$this->m_utilization_report->cal_Available_Hours($filter['start_date'],$filter['end_date'],$value->username);
            }
            
        }
        /** Error reporting */
        error_reporting(E_ALL);
        ini_set('display_errors', TRUE);
        ini_set('display_startup_errors', TRUE);

        if (PHP_SAPI == 'cli')
            die('This example should only be run from a Web Browser');

        /** Include PHPExcel */
        require_once './PHPExcel/Classes/PHPExcel.php';
        require_once './PHPExcel/Classes/PHPExcel/Cell/AdvancedValueBinder.php';
        PHPExcel_Cell::setValueBinder( new PHPExcel_Cell_AdvancedValueBinder() );


        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();
        $styleblack = array(
        'font'  => array(
            'bold'  => false,
            'color' => array('rgb' => 'FFFFFF'),
            'size'  => 11,
            'name'  => 'Calibri'
        ),
        'fill' => array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array('rgb' => '000000')
        )
        );
        $style_center = array(
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        )
        );
        $stylegray = array(
        'font'  => array(
            'bold'  => false,
            'color' => array('rgb' => '000000'),
            'size'  => 11,
            'name'  => 'Calibri'
        ),
        'fill' => array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array('rgb' => 'E2E2E2')
        )
        );
        // Set document properties
        $objPHPExcel->getProperties()->setCreator("Rcal Neumerlin Group")
                                     ->setLastModifiedBy("DekGym3Atom")
                                     ->setTitle("Office 2007 XLSX User report")
                                     ->setSubject("Office 2007 XLSX User report")
                                     ->setDescription("User report document for Office 2007 XLSX, generated using PHP classes.")
                                     ->setKeywords("office 2007 openxml php")
                                     ->setCategory("User report");


        // Add some data     
        $cur_col=0;
        $cur_row=1;   
        $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(0, $cur_row)->setValueExplicit('Date '.date("d F Y"), PHPExcel_Cell_DataType::TYPE_STRING);
        $cur_row+=1;   
        $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(0, $cur_row)->setValueExplicit(date("d F Y",$filter['start_date']).' - '.date("d F Y",$filter['end_date']), PHPExcel_Cell_DataType::TYPE_STRING);
        $cur_row+=1;   
        $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(0, $cur_row)->setValueExplicit('Resources / Project', PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex(0))->setAutoSize(true);
        $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(1, $cur_row)->setValueExplicit('Available Hours', PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex(1))->setAutoSize(true);
        $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(2, $cur_row)->setValueExplicit('Allocation Hour', PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex(2))->setAutoSize(true);
        $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(3, $cur_row)->setValueExplicit('Spent Hour', PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex(3))->setAutoSize(true);
        $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(4, $cur_row)->setValueExplicit('Spent Rate (%)', PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex(4))->setAutoSize(true);
        $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(5, $cur_row)->setValueExplicit('Utilization (%)', PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex(5))->setAutoSize(true);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex(0).($cur_row).":".PHPExcel_Cell::stringFromColumnIndex(5).$cur_row)->applyFromArray($stylegray);
        foreach ($all_child as $key => $value) {
            $cur_row+=1;
            $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(0, $cur_row)->setValueExplicit($value->nickname, PHPExcel_Cell_DataType::TYPE_STRING);
            $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(1, $cur_row)->setValueExplicit('Available Hours', PHPExcel_Cell_DataType::TYPE_STRING);
            $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(2, $cur_row)->setValueExplicit('Allocation Hour', PHPExcel_Cell_DataType::TYPE_STRING);
            $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(3, $cur_row)->setValueExplicit('Spent Hour', PHPExcel_Cell_DataType::TYPE_STRING);
            $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(4, $cur_row)->setValueExplicit('Spent Rate (%)', PHPExcel_Cell_DataType::TYPE_STRING);
            $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(5, $cur_row)->setValueExplicit('Utilization (%)', PHPExcel_Cell_DataType::TYPE_STRING);
            $objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex(0).($cur_row).":".PHPExcel_Cell::stringFromColumnIndex(5).$cur_row)->applyFromArray($stylegray);
            $flag_first=true;
            foreach ($value->projects as $key2 => $value2) {
                $cur_row+=1;
                $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(0, $cur_row)->setValueExplicit($value2->project_name, PHPExcel_Cell_DataType::TYPE_STRING);
                if ($flag_first) {
                    $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(1, $cur_row)->setValueExplicit($value->available_Hours, PHPExcel_Cell_DataType::TYPE_STRING);
                    $objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex(1).$cur_row)->applyFromArray($style_center);
                    $objPHPExcel->setActiveSheetIndex(0)->mergeCells(PHPExcel_Cell::stringFromColumnIndex(1).($cur_row).":".PHPExcel_Cell::stringFromColumnIndex(1).($cur_row+count($value->projects)-1));
                }
                $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(2, $cur_row)->setValueExplicit($value2->hour_amount, PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(3, $cur_row)->setValueExplicit($value2->spend_amount, PHPExcel_Cell_DataType::TYPE_STRING);
                $spend_rate="0%";
                if ($value2->hour_amount!=0) {
                  $spend_rate=number_format(($value2->spend_amount/$value2->hour_amount)*100)."%";
                }
                $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(4, $cur_row)->setValueExplicit($spend_rate, PHPExcel_Cell_DataType::TYPE_STRING);
                if ($flag_first) {
                    $utilization="0%";
                    if ($value2->hour_amount!=0) {
                      $utilization=number_format(($value->report->hour_amount/$value->available_Hours)*100)."%";
                    }
                    $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(5, $cur_row)->setValueExplicit($utilization, PHPExcel_Cell_DataType::TYPE_STRING);
                    $objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex(5).$cur_row)->applyFromArray($style_center);
                    $objPHPExcel->setActiveSheetIndex(0)->mergeCells(PHPExcel_Cell::stringFromColumnIndex(5).($cur_row).":".PHPExcel_Cell::stringFromColumnIndex(5).($cur_row+count($value->projects)-1));
                }

                $flag_first=false;
            }
            $cur_row+=1;
            $objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex(0).($cur_row).":".PHPExcel_Cell::stringFromColumnIndex(5).$cur_row)->applyFromArray($stylegray);
            $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(0, $cur_row)->setValueExplicit($value->nickname." Total", PHPExcel_Cell_DataType::TYPE_STRING);
            $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(1, $cur_row)->setValueExplicit($lenght_time.' Days', PHPExcel_Cell_DataType::TYPE_STRING);
            $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(2, $cur_row)->setValueExplicit($value->report->hour_amount, PHPExcel_Cell_DataType::TYPE_STRING);
            $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(3, $cur_row)->setValueExplicit($value->report->spend_amount, PHPExcel_Cell_DataType::TYPE_STRING);
            $tspend_rate="0%";
            if ($value->report->hour_amount!=0) {
              $tspend_rate=number_format(($value->report->spend_amount/$value->report->hour_amount)*100)."%";
            }
            $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(4, $cur_row)->setValueExplicit($tspend_rate, PHPExcel_Cell_DataType::TYPE_STRING);
            $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(5, $cur_row)->setValueExplicit('', PHPExcel_Cell_DataType::TYPE_STRING);
        }
        
        
        // Rename worksheet
        $objPHPExcel->getActiveSheet()->setTitle('utilization_report');


        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);


        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="utilization_report.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;

    }

    public function utilization_report_excelv2()
    {
        $filter = array();
        $filter['start_date']=(time()-(60*60*24*30));
        if (isset($_POST['start_time'])) {
            $filter['start_date']=$this->m_time->datepicker_to_unix($_POST['start_time']);
        }
        $filter['end_date']=(time()+(60*60*24*30));
        if (isset($_POST['end_time'])) {
            $filter['end_date']=$this->m_time->datepicker_to_unix($_POST['end_time'])+(60*60*24)-60;
        }
        $all_child1=$this->get_all_user_child_node();
        $all_child1[$this->user_data->username]=$this->user_data;
        
        $data_head['user_data'] = $this->user_data;
        $all_child = array();
        $position=$this->m_position->get_all_position();
        $lenght_time=(int)(($filter['end_date']-$filter['start_date'])/(60*60*24));
        $mod_time=(int)(($filter['end_date']-$filter['start_date'])%(60*60*24));
        if ($mod_time>0) {
          $lenght_time+=1;
        }
        $lenght_time=$lenght_time;
        foreach ($all_child1 as $key => $value) {
            if (isset($position[$value->position])&&$position[$value->position]->non_productive=="n") {
                $all_child[$key]=$value;
                $all_child[$key]->report=$this->m_utilization_report->get_work_sheet_by_usn($value->username,$filter);
                $all_child[$key]->projects=$this->m_utilization_report->group_assign_to_project($all_child[$key]->report);
                $all_child[$key]->available_Hours=$this->m_utilization_report->cal_Available_Hours($filter['start_date'],$filter['end_date'],$value->username);
            }
            
        }
        /** Error reporting */
        error_reporting(E_ALL);
        ini_set('display_errors', TRUE);
        ini_set('display_startup_errors', TRUE);

        if (PHP_SAPI == 'cli')
            die('This example should only be run from a Web Browser');

        /** Include PHPExcel */
        require_once './PHPExcel/Classes/PHPExcel.php';
        require_once './PHPExcel/Classes/PHPExcel/Cell/AdvancedValueBinder.php';
        PHPExcel_Cell::setValueBinder( new PHPExcel_Cell_AdvancedValueBinder() );


        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();
        $styleblack = array(
        'font'  => array(
            'bold'  => false,
            'color' => array('rgb' => 'FFFFFF'),
            'size'  => 11,
            'name'  => 'Calibri'
        ),
        'fill' => array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array('rgb' => '000000')
        )
        );
        $style_center = array(
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        )
        );
        $stylegray = array(
        'font'  => array(
            'bold'  => false,
            'color' => array('rgb' => '000000'),
            'size'  => 11,
            'name'  => 'Calibri'
        ),
        'fill' => array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array('rgb' => 'E2E2E2')
        )
        );
        // Set document properties
        $objPHPExcel->getProperties()->setCreator("Rcal Neumerlin Group")
                                     ->setLastModifiedBy("DekGym3Atom")
                                     ->setTitle("Office 2007 XLSX User report")
                                     ->setSubject("Office 2007 XLSX User report")
                                     ->setDescription("User report document for Office 2007 XLSX, generated using PHP classes.")
                                     ->setKeywords("office 2007 openxml php")
                                     ->setCategory("User report");


        // Add some data     
        $cur_col=0;
        $cur_row=1;   
        $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(0, $cur_row)->setValueExplicit('Date '.date("d F Y"), PHPExcel_Cell_DataType::TYPE_STRING);
        $cur_row+=1;   
        $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(0, $cur_row)->setValueExplicit(date("d F Y",$filter['start_date']).' - '.date("d F Y",$filter['end_date']), PHPExcel_Cell_DataType::TYPE_STRING);
        $cur_row+=1;   
        $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(0, $cur_row)->setValueExplicit('Resources / Project', PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex(0))->setAutoSize(true);
        $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(1, $cur_row)->setValueExplicit('Available Hours', PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex(1))->setAutoSize(true);
        $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(2, $cur_row)->setValueExplicit('Allocation Hour', PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex(2))->setAutoSize(true);
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells(PHPExcel_Cell::stringFromColumnIndex(2).($cur_row).":".PHPExcel_Cell::stringFromColumnIndex(4).($cur_row));
        $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(5, $cur_row)->setValueExplicit('Spent', PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex(5))->setAutoSize(true);
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells(PHPExcel_Cell::stringFromColumnIndex(5).($cur_row).":".PHPExcel_Cell::stringFromColumnIndex(7).($cur_row));
        $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(8, $cur_row)->setValueExplicit('Utilization (%)', PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex(8))->setAutoSize(true);
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells(PHPExcel_Cell::stringFromColumnIndex(8).($cur_row).":".PHPExcel_Cell::stringFromColumnIndex(10).($cur_row));
        $objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex(0).($cur_row).":".PHPExcel_Cell::stringFromColumnIndex(10).$cur_row)->applyFromArray($stylegray);
        foreach ($all_child as $key => $value) {
            $cur_row+=1;
            $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(0, $cur_row)->setValueExplicit($value->nickname, PHPExcel_Cell_DataType::TYPE_STRING);
            $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(1, $cur_row)->setValueExplicit('Available Hours', PHPExcel_Cell_DataType::TYPE_STRING);
            $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(2, $cur_row)->setValueExplicit('TOTAL', PHPExcel_Cell_DataType::TYPE_STRING);
            $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(3, $cur_row)->setValueExplicit('BUDGET', PHPExcel_Cell_DataType::TYPE_STRING);
            $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(4, $cur_row)->setValueExplicit('OVER', PHPExcel_Cell_DataType::TYPE_STRING);
            $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(5, $cur_row)->setValueExplicit('TOTAL', PHPExcel_Cell_DataType::TYPE_STRING);
            $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(6, $cur_row)->setValueExplicit('on BUDGET', PHPExcel_Cell_DataType::TYPE_STRING);
            $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(7, $cur_row)->setValueExplicit('on TOTAL', PHPExcel_Cell_DataType::TYPE_STRING);
            $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(8, $cur_row)->setValueExplicit('by Budget', PHPExcel_Cell_DataType::TYPE_STRING);
            $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(9, $cur_row)->setValueExplicit('by Spent', PHPExcel_Cell_DataType::TYPE_STRING);
            $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(10, $cur_row)->setValueExplicit('GAP', PHPExcel_Cell_DataType::TYPE_STRING);
            $objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex(0).($cur_row).":".PHPExcel_Cell::stringFromColumnIndex(10).$cur_row)->applyFromArray($stylegray);
            $flag_first=true;
            foreach ($value->projects as $key2 => $value2) {
                $cur_row+=1;
                $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(0, $cur_row)->setValueExplicit($value2->project_name, PHPExcel_Cell_DataType::TYPE_STRING);
                if ($flag_first) {
                    $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(1, $cur_row)->setValueExplicit($value->available_Hours, PHPExcel_Cell_DataType::TYPE_STRING);
                    $objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex(1).$cur_row)->applyFromArray($style_center);
                    $objPHPExcel->setActiveSheetIndex(0)->mergeCells(PHPExcel_Cell::stringFromColumnIndex(1).($cur_row).":".PHPExcel_Cell::stringFromColumnIndex(1).($cur_row+count($value->projects)-1));
                }
                $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(2, $cur_row)->setValueExplicit($value2->hour_amount, PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(3, $cur_row)->setValueExplicit($value2->budget, PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(4, $cur_row)->setValueExplicit($value2->hour_over, PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(5, $cur_row)->setValueExplicit($value2->spend_amount, PHPExcel_Cell_DataType::TYPE_STRING);
                $spend_rate="0%";
                if ($value2->budget!=0) {
                  $spend_rate=number_format(($value2->spend_amount/$value2->budget)*100)."%";
                }
                $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(6, $cur_row)->setValueExplicit($spend_rate, PHPExcel_Cell_DataType::TYPE_STRING);
                $spend_rate="0%";
                if ($value2->hour_amount!=0) {
                  $spend_rate=number_format(($value2->spend_amount/$value2->hour_amount)*100)."%";
                }
                $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(7, $cur_row)->setValueExplicit($spend_rate, PHPExcel_Cell_DataType::TYPE_STRING);
                if ($flag_first) {
                    $gap1="0%";
                    if ($value2->hour_amount!=0) {
                      $gap1=number_format(($value->report->budget/$value->available_Hours)*100)."%";
                    }
                    $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(8, $cur_row)->setValueExplicit($gap1, PHPExcel_Cell_DataType::TYPE_STRING);
                    $objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex(8).$cur_row)->applyFromArray($style_center);
                    $objPHPExcel->setActiveSheetIndex(0)->mergeCells(PHPExcel_Cell::stringFromColumnIndex(8).($cur_row).":".PHPExcel_Cell::stringFromColumnIndex(8).($cur_row+count($value->projects)-1));
                    $gap2="0%";
                    if ($value2->hour_amount!=0) {
                      $gap2=number_format(($value->report->spend_amount/$value->available_Hours)*100)."%";
                    }
                    $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(9, $cur_row)->setValueExplicit($gap2, PHPExcel_Cell_DataType::TYPE_STRING);
                    $objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex(9).$cur_row)->applyFromArray($style_center);
                    $objPHPExcel->setActiveSheetIndex(0)->mergeCells(PHPExcel_Cell::stringFromColumnIndex(9).($cur_row).":".PHPExcel_Cell::stringFromColumnIndex(9).($cur_row+count($value->projects)-1));
                    
                    $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(10, $cur_row)->setValueExplicit(number_format($gap2-$gap1)."%", PHPExcel_Cell_DataType::TYPE_STRING);
                    $objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex(10).$cur_row)->applyFromArray($style_center);
                    $objPHPExcel->setActiveSheetIndex(0)->mergeCells(PHPExcel_Cell::stringFromColumnIndex(10).($cur_row).":".PHPExcel_Cell::stringFromColumnIndex(10).($cur_row+count($value->projects)-1));
                }

                $flag_first=false;
            }
            $cur_row+=1;
            $objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex(0).($cur_row).":".PHPExcel_Cell::stringFromColumnIndex(10).$cur_row)->applyFromArray($stylegray);
            $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(0, $cur_row)->setValueExplicit($value->nickname." Total", PHPExcel_Cell_DataType::TYPE_STRING);
            $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(1, $cur_row)->setValueExplicit($lenght_time.' Days', PHPExcel_Cell_DataType::TYPE_STRING);
            $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(2, $cur_row)->setValueExplicit($value->report->hour_amount, PHPExcel_Cell_DataType::TYPE_STRING);
            $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(3, $cur_row)->setValueExplicit($value->report->budget, PHPExcel_Cell_DataType::TYPE_STRING);
            $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(4, $cur_row)->setValueExplicit($value->report->hour_over, PHPExcel_Cell_DataType::TYPE_STRING);
            $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(5, $cur_row)->setValueExplicit($value->report->spend_amount, PHPExcel_Cell_DataType::TYPE_STRING);
            $tspend_rate="0%";
            if ($value->report->budget!=0) {
              $tspend_rate=number_format(($value->report->spend_amount/$value->report->budget)*100)."%";
            }
            $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(6, $cur_row)->setValueExplicit($tspend_rate, PHPExcel_Cell_DataType::TYPE_STRING);
            $tspend_rate="0%";
            if ($value->report->hour_amount!=0) {
              $tspend_rate=number_format(($value->report->spend_amount/$value->report->hour_amount)*100)."%";
            }
            $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(7, $cur_row)->setValueExplicit($tspend_rate, PHPExcel_Cell_DataType::TYPE_STRING);

            $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(8, $cur_row)->setValueExplicit('', PHPExcel_Cell_DataType::TYPE_STRING);
            $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(9, $cur_row)->setValueExplicit('', PHPExcel_Cell_DataType::TYPE_STRING);
            $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(10, $cur_row)->setValueExplicit('', PHPExcel_Cell_DataType::TYPE_STRING);
        }
        
        
        // Rename worksheet
        $objPHPExcel->getActiveSheet()->setTitle('utilization_report');


        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);


        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="utilization_report.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;

    }







}