<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');


class cs extends CI_Controller
{
public function __construct() {
        parent::__construct();
        $this->load->model('m_user');
        $this->load->model('m_time');
        $this->load->model('m_group');
        $this->load->model('m_hour_rate');
        $this->load->model('m_company');
        $this->load->model('m_project');
        $this->load->model('m_work_sheet');
        $this->load->model('m_Rsheet');
        $this->load->model('m_business');
        $this->load->model('m_hr');
        if ($this->session->userdata('username')) {
            $user_data = $this->m_user->get_user_by_login_name($this->session->userdata('username'));
            if (isset($user_data->username) && isset($user_data->prem['cs'])) {
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
        $data_view['project_list'] = $this->m_project->get_project_by_cs($this->user_data->username);
        $this->load->view('v_header', $data_head);
        $this->load->view('cs/v_cs_project_list',$data_view);
        $this->load->view('v_footer', $data_foot);
    }
    public function project_done() {
        $id=$this->uri->segment(3,'');
       $check_hod_assign=$this->m_project->check_hod_assign_resource($id);
        //$check_payment=$this->m_project->check_all_pay_date_paid_by_project($id);
        //$check_billing=$this->m_project->check_biling_date_ready_by_project($id);
        $check_all_oc_done=$this->m_oc->check_all_done_oc_by_project_id($id);
        if ($check_hod_assign&&$check_all_oc_done) {
            $project_dat = array(
                'status' => "Done",
                );
            $this->m_project->update_project($project_dat,$id);
            redirect("cs");
        }else if(!$check_hod_assign){
            ?>
            <script type="text/javascript">
            alert("HOD must complete assign");
            window.open("<?=site_url("project/edit_oc/".$id)?>","_self");
            </script>
            <?
        }else{
            ?>
            <script type="text/javascript">
            alert("All OC must be done");
            window.open("<?=site_url("project/edit_oc/".$id)?>","_self");
            </script>
            <?
        }
        
    }
    public function project_archive() {
        $id=$this->uri->segment(3,'');
        $check_hod_assign=$this->m_project->check_hod_assign_resource($id);
        $check_payment=$this->m_project->check_all_pay_date_paid_by_project($id);
        $check_billing=$this->m_project->check_biling_date_ready_by_project($id);
        if ($check_hod_assign&&$check_payment&&$check_billing) {
            $project_dat = array(
                'status' => "Archive",
                );
            $this->m_project->update_project($project_dat,$id);
            redirect("cs");
        }else if(!$check_hod_assign){
            ?>
            <script type="text/javascript">
            alert("HOD must complete assign");
            window.open("<?=site_url("project/edit_oc/".$id)?>","_self");
            </script>
            <?
        }else{
			if($check_payment){
				echo "pay<br>";
			}
			if($check_billing){
				echo "bill<br>";
			}
            ?>
            <script type="text/javascript">
            alert("Cannot Archive this project please check payment and billing");
            window.open("<?=site_url("cs")?>","_self");
            </script>
            <?
        }
		
        
    }
    public function project_cancel() {
        $id=$this->uri->segment(3,'');
            $project_dat = array(
                'status' => "Cancel",
                );
            $this->m_project->update_project($project_dat,$id);
            redirect("cs");
    }
    public function work_sheet()
    {   
        $id=$this->uri->segment(3,'');
        $check_max_allocate=$this->m_project->check_hod_assign_resource($id);
        if ($check_max_allocate) {
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
            $this->m_Rsheet->combine_same_type($id);
            $data_foot['table']="yes";
            $data_head['user_data']=$this->user_data;
            $data['user_data']=$this->user_data;
            $data['a']="0";
            $data['task_type_list']=$this->m_work_sheet->get_advalid_task_type_by_project_id($id);
            $data['project']=$this->m_project->get_project_by_id($id);
            if ($data['project']->status=="Proposing"||$data['project']->status=="Revise") {
                $dat_up = array('status' => "WIP");
                $this->m_project->update_project($dat_up,$id);
                $data['project']=$this->m_project->get_project_by_id($id);
            }
            $data['bu']=$this->m_company->get_bu_by_id($data['project']->project_bu);            
            $data['company']=$this->m_company->get_company_by_id($data['project']->project_client);
            $data['business_list'] = $this->m_business->get_all_business();
            //print_r($data['pce_doc']);
            $this->load->view('v_header',$data_head);
            $this->load->view('cs/v_work_sheet',$data);
            $this->load->view('v_footer',$data_foot);
        }else{
            redirect("cs");
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
                if (!isset($cur_assign->id)) {
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
                }
                
            
        }else{
            $json['flag']="false code:".$project_id.",".$work_id.",".$time.",".$_POST['save'].","; 
        }
        echo json_encode($json);
        
    }



    public function work_add_resource(){
        $work_id=$this->uri->segment(3,'');
        if (isset($_POST['add_bill'])) {
            $work=$this->m_work_sheet->get_work_sheet_by_id($work_id);
            $user=$this->m_work_sheet->get_allow_list_by_t_type_and_project_id($work->task_type,$work->project_id);
            ?>
            <tr>
                <td>
                    <div class="control-group">
                        <select class="chzn-select change_usn" name="resource_usn[]">
                            <option value="no">---please select ------</option>
                            <?
                            foreach ($user as $key2 => $value2) {
                                                    ?>
                                <option value="<?=$value2->user_dat->username?>" ><?=$value2->user_dat->nickname?></option>
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
            $json['msg']="NO" ;
                if (isset($_POST['del_list'])) {
                    foreach ($_POST['del_list'] as $key => $value) {
                        $fag=$this->m_work_sheet->delete_work_sheet_has_res($value);
                        if (!$fag) {
                            $json['msg']="Resoure บางคนไม่สามารถลบได้ เพราะ Resoure ได้ ใช้ ชั่วโมงการทำงานไปแล้ว";
                        }
                    }
                }
                if (isset($_POST['resource_usn'])) {
                    foreach ($_POST['resource_usn'] as $key => $value) {
                        $curent_res=$this->m_work_sheet->get_work_sheet_has_res_by_work_id_and_usn($_POST['save'],$value);
                        if (!isset($curent_res->id)) {
                            $data = array(
                                'work_sheet_id' => $_POST['save'], 
                                'usn' => $value, 
                            );
                            if ($this->m_user->check_have_user($value)) {
                               $this->m_work_sheet->add_work_sheet_has_res($data);
                            }
                        }       
                    }
                }
                if (isset($_POST['resource_usn_old'])) {
                    foreach ($_POST['resource_usn_old'] as $key => $value) {
                        $data = array(
                            'usn' => $value
                        );
                        if ($this->m_user->check_have_user($value)) {
                            $this->m_work_sheet->update_work_sheet_has_res($data,$_POST['id_old'][$key]);
                        }
                    }
                }
                
            echo json_encode($json);
        }else{
            $data['work_id']=$work_id;

            $data['work']=$this->m_work_sheet->get_work_sheet_by_id($data['work_id']);
            $data['user']=$this->m_work_sheet->get_allow_list_by_t_type_and_project_id($data['work']->task_type,$data['work']->project_id);
            $data['res_list']=$this->m_work_sheet->get_res_assign_detail_by_work_id($data['work']->id);
            $data['project_id']=$data['work']->project_id;
            $data['hour_rate']=$this->m_hour_rate->get_hour_rate_by_id($data['work']->task_type);
            $this->load->view('v_header_popup');
            $this->load->view('cs/v_work_add_resource_pop',$data);
            $this->load->view('v_footer');
        }
        
    }
    public function get_advailable_hour_in($t_type,$project_id,$end_time){
        $hour=0;
        $sum_approve_budget=$this->m_Rsheet->get_sum_approve_budget_by_type_and_project_id($t_type,$project_id);
        $sum_assign=$this->m_work_sheet->get_sum_assign_hour_by_t_type_and_project_id($t_type,$project_id,$end_time);
        $hour=$sum_approve_budget-$sum_assign;
        return $hour;
    }
    public function edit_task_peroid(){
        $work_id=$this->uri->segment(3,'');
            $data['work_id']=$work_id;
            $data['work']=$this->m_work_sheet->get_work_sheet_by_id($work_id);
            $data['user_data']=$this->user_data;
            $this->load->view('v_header_popup');
            $this->load->view('cs/v_edit_work_sheet_peroid',$data);
            $this->load->view('v_footer');
    }
    public function edit_task_note(){
        $work_id=$this->uri->segment(3,'');
            $data['work_id']=$work_id;
            $data['work']=$this->m_work_sheet->get_work_sheet_by_id($work_id);
            $data['work_photo']=$this->m_work_sheet->get_work_sheet_photo_by_work_id($work_id);
            $data['user_data']=$this->user_data;
            $this->load->view('v_header_popup');
            $this->load->view('cs/v_edit_work_sheet_note',$data);
            $this->load->view('v_footer');
    }
    ///////////////////////////////// AJAX Region ///////////////////////////////////////////////
    public function update_task_note(){
        header('Content-Type: application/json');
        $json = array();
        $json['flag']="OK";
        $data = array('comment' => trim($_POST["dat"]));
        $this->m_work_sheet->update_work_sheet($data,$_POST["work_id"]);
        echo json_encode($json);
    }
    public function del_task_photo(){
        header('Content-Type: application/json');
        $json = array();
        $json['flag']="OK";
        $this->m_work_sheet->delete_work_sheet_photo($_POST["photo_id"]);
        echo json_encode($json);
    }
    public function sort_task_photo(){
        header('Content-Type: application/json');
        $json = array();
        $json['flag']="OK";
        $count=1;
        if (isset($_POST['photo_id_list'])) {
            foreach ($_POST['photo_id_list'] as $key => $value) {
                $data = array(
                    'sort_order' =>  $count,
                    );
                $this->m_work_sheet->update_work_sheet_photo($data,$value);
                $count+=1;
            }
        }
        echo json_encode($json);
    }
    public function insert_task_Photo(){
        header('Content-Type: application/json');
        $json = array();
        $json['flag']="OK";
        if ($_POST['file_path'] != ""&&$_POST['work_id'] != "") {
                    $photo_id=$this->m_work_sheet->generate_id_photo();
                    $filename = $_POST['file_path'];
                    $ext = explode(".", $filename);
                    $new_ext = $ext[count($ext) - 1];
                    $new_filename = $_POST['work_id'] ."_".$photo_id. "." . $new_ext;
                    $file = './media/temp/' . $filename;
                    $newfile = './media/work_sheet_comment_photo/' . $new_filename;
                    
                    if (!copy($file, $newfile)) {
                        echo "failed to copy $file...\n" . $file . " to " . $newfile . "  and  ";
                        
                        @unlink("./media/temp/" . $filename);
                    } 
                    else {
                        $data = array(
                            'id' => $photo_id,
                            'work_sheet_id' => $_POST["work_id"],
                            'filename' => $new_filename,
                            'sort_order' => 999,
                            );
                        $this->m_work_sheet->add_work_sheet_photo($data,$_POST["work_id"]);
                        @unlink("./media/temp/" . $filename);
                        $json['html']='<div id="'.$photo_id.'" class="img-region"><a class="fancybox " rel="gallery1" href="'.site_url('media/work_sheet_comment_photo/'.$new_filename).'" >'.
                                            '<img src="'.site_url('media/work_sheet_comment_photo/'.$new_filename).'" alt="" />'.
                                        '</a><a href="javascript:;" id-dat="'.$photo_id.'" class="btn btn-danger del_img_but">'.
                                        '<input type="hidden" name="photo_id_list[]" value="'.$photo_id.'">'.
                                        '<i class="icon-remove icon-white"></i></a></div>';
                    }
                }else{
                    $json['flag']="File or work ID not receive  ";
                }
        
        echo json_encode($json);
    }
    public function get_advailable_hour(){
        header('Content-Type: application/json');
        $json = array();
        $json['hour']=$this->get_advailable_hour_in($_POST['t_type'],$_POST['project_id']);
        echo json_encode($json);
    }
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
    
    public function force_sort_work_sheet(){
        header('Content-Type: application/json');
        $json = array();
        $json['flag']="OK";
        $count=1;
        if (isset($_POST['work_id_list'])) {
            foreach ($_POST['work_id_list'] as $key => $value) {
                $data = array(
                    'sort_order' =>  $count,
                    );
                $this->m_work_sheet->update_work_sheet($data,$value);
                $count+=1;
            }
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
                );
            $this->m_work_sheet->update_work_sheet($data,$_POST['id']);
            $this->m_work_sheet->sort_work_sheet($_POST['project_id']);
            $this->m_work_sheet->valid_delay_project($_POST['project_id']);
        }else{
            $json['flag']="ไม่สมารถเปลี่ยน peroid งานได้ เพราะมีชั่วโมงอยู่นอก peroid งาน";
        }
        
        echo json_encode($json);
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
    public function print_work_task_left(){
        $work=$this->m_work_sheet->get_work_sheet_by_project_id($_POST['project_id']);
        foreach ($work as $key => $value) {            
            $res_list=$this->m_work_sheet->get_res_assign_detail_by_work_id($value->id);
            ?>
            <tr id="<?=$value->id?>" >
                <td class="hiligh-border-bot"><?=$value->sort_order?><br>
                <?
                $flag_spend=$this->m_work_sheet->check_if_work_task_has_spend($value->id);
                if (!$flag_spend) {
                    ?>
                    <a iden="<?=$value->id?>" href="javascript:;" class="btn btn-danger del_work"><i class="icon-remove icon-white"></i></a>
                    <?
                }
                ?>
                <input type="hidden" name="work_id_list[]" value="<?=$value->id?>">
                </td>
                <td class="w-wrap hiligh-border-bot">
                       <?=$value->work_name?>
                 
                </td>
                <td class="hiligh-border-bot">
                       <a class="btn btn-inverse fancybox" data-fancybox-type="iframe" href="<?echo site_url("cs/edit_task_note/".$value->id);?>"><i class="icon-file icon-white"></i></a>
                 
                </td>
                <td class="hiligh-border-bot">
                    <a class="btn btn-inverse fancybox" data-fancybox-type="iframe" href="<?echo site_url("cs/edit_task_peroid/".$value->id);?>"><i class="icon-calendar icon-white"></i></a>
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
                        if (count($res_list)==0) {
                            ?>
                            <tr id="">
                                <td><a href="javascript:;" class="btn btn-success">Add Resource</a></td>
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
        $first_work=$this->m_work_sheet->get_first_start_time_work_sheet_by_project_id($_POST['project_id']);
        $start_carlendar_unix=0;
        if (isset($first_work->start)&&$first_work->start!=0) {
            $start_carlendar_unix=$first_work->start;
        }else{
            $start_carlendar_unix=$project->project_start;
        }
        $end_carlendar_unix=0;
        if (isset($last_work->end)&&isset($first_work->end)&&$first_work->end!=0) {
            $end_carlendar_unix=$last_work->end;
        }else{
            $end_carlendar_unix=$project->project_end;
        }
        $holiday_obj=$this->m_hr->get_all_holiday($start_carlendar_unix,$end_carlendar_unix);
        $user_leave_obj=$this->m_hr->get_all_user_leave($start_carlendar_unix,$end_carlendar_unix);
            ?>
            <table cellpadding="0" cellspacing="0" border="0" class="table table-nopadding table-bordered">
                                            <thead>
                                                <tr>
                                                <?
                                                $current_time=$start_carlendar_unix;
                                                $exceed_maximun_day=false;
                                                    $cur_month=1000;
                                                    while ($current_time<=$end_carlendar_unix||!$exceed_maximun_day) {
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
                                                            <th colspan="<?=$colspan1?>" style="text-align: center;"><?=date("F",$current_time)?></th>
                                                            <?
                                                        }                                                        
                                                       $current_time+=(60*60*24);
                                                       $day_pass=($current_time-$project->project_start)/(60*60*24);
                                                           if ($day_pass>30) {
                                                               $exceed_maximun_day=true;
                                                           }
                                                    }
                                                ?>
                                                </tr>
                                                <tr>
                                                <?
                                                $current_time=$start_carlendar_unix;
                                                $exceed_maximun_day=false;
                                                    while ($current_time<=$end_carlendar_unix||!$exceed_maximun_day) {
                                                        $holiday="";
                                                        $curday_td="";
                                                        if (date("N",$current_time)==6||date("N",$current_time)==7) {
                                                            $holiday='holiday_td';
                                                        }
                                                        if ($current_time==$current_day) {
                                                                $curday_td='curday_td';
                                                            }
                                                        if (isset($holiday_obj[$current_time])&&$holiday_obj[$current_time]->is_holiday=="y") {
                                                                $holiday='s_holiday_td';
                                                            }
                                                       ?>
                                                       <th class="<?=$holiday?> <?=$curday_td?>"><?=date("d",$current_time)?></th>
                                                       <?
                                                       $current_time+=(60*60*24);
                                                       $day_pass=($current_time-$project->project_start)/(60*60*24);
                                                           if ($day_pass>30) {
                                                               $exceed_maximun_day=true;
                                                           }
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
                                                    $current_time=$start_carlendar_unix;
                                                    $exceed_maximun_day=false;
                                                        while ($current_time<=$end_carlendar_unix||!$exceed_maximun_day) {
                                                            $holiday="";
                                                            $in_range="";
                                                            $curday_td="";
                                                            if (date("N",$current_time)==6||date("N",$current_time)==7) {
                                                                $holiday='holiday_td';
                                                            }
                                                            if (isset($holiday_obj[$current_time])&&$holiday_obj[$current_time]->is_holiday=="y") {
                                                                $holiday='s_holiday_td';
                                                            }                                                            
                                                            if ($value->start<=$current_time&&$value->end>=$current_time&&$holiday=="") {
                                                                $in_range='range_task';
                                                            }
                                                            if ($current_time==$current_day) {
                                                                $curday_td='curday_td';
                                                            }
                                                           ?>
                                                           <td class="<?=$holiday?> <?=$curday_td?> <?=$in_range?> hiligh-border-bot" iden="<?=$value->id?>" time="<?=$current_time?>">
                                                               <table class="no-border">
                                                               <?
                                                                foreach ($res_list as $res_key => $res_value) {
                                                                    $range_task_over="";
                                                                    if (isset($res_value->assign_list->list[$current_time])) {
                                                                        if ($res_value->assign_list->list[$current_time]->status=="over"&&$res_value->assign_list->list[$current_time]->hour!=0) {
                                                                            $range_task_over="range_task_over";
                                                                        }
                                                                        if ($res_value->assign_list->list[$current_time]->status=="over"&&$res_value->assign_list->list[$current_time]->hour!=0) {
                                                                            $range_task_over.=" range_task_interfere";
                                                                        }
                                                                    }
                                                                    ?>
                                                                    <tr class="allow_tr_con <?=$range_task_over?>" iden="<?=$value->id?>__<?=$res_value->username?>" time="<?=$current_time?>">
                                                                        <td>
                                                                            <?
                                                                            //print_r($res_value->assign_list->list);
                                                                            $disable_input="";
                                                                            if ($current_day-(60*60*24*4)>$current_time) {
                                                                                $disable_input="disabled";
                                                                            }
                                                                            if (!$this->m_Rsheet->check_res_with_type_in_project($res_value->username,$value->task_type,$_POST['project_id'])) {
                                                                                $disable_input="disabled";
                                                                            }
                                                                            $sum_hour_assign=$this->m_work_sheet->get_res_assign_by_usn_and_time($res_value->username,$current_time)->hour_amount;
                                                                            if (isset($res_value->assign_list->list[$current_time])) {
                                                                                $use_val=0;
                                                                                if ($res_value->assign_list->list[$current_time]->hour!=0.5) {
                                                                                    $use_val=number_format($res_value->assign_list->list[$current_time]->hour,0);
                                                                                }else{
                                                                                    $use_val=$res_value->assign_list->list[$current_time]->hour;
                                                                                }
                                                                                if ($in_range=="range_task") {
                                                                                ?>
                                                                                <input <?=$disable_input?> title="<?=$sum_hour_assign?>/8 <?=date("- d F Y",$current_time)?>" class="input-assign" workid="<?=$value->id?>" time="<?=$current_time?>" usn="<?=$res_value->username?>" value="<?=$use_val?>">
                                                                                <?      
                                                                                }else{
                                                                                ?>
                                                                                <input disabled title="<?=$sum_hour_assign?>/8 <?=date("- d F Y",$current_time)?>" class="input-assign" workid="<?=$value->id?>" time="<?=$current_time?>" usn="<?=$res_value->username?>" value="<?=$use_val?>">
                                                                                <?      
                                                                                }                                                                          
                                                                            }else{
                                                                                if(isset($user_leave_obj[$current_time][$res_value->username])){
                                                                                    ?>
                                                                                    <div class="leave_td" style="margin:0px auto;width:30px;">L</div>
                                                                                    <?
                                                                                }else if ($in_range=="range_task") {                                                                                    
                                                                                    ?>
                                                                                    <input <?=$disable_input?> title="<?=$sum_hour_assign?>/8 <?=date("- d F Y",$current_time)?>" class="input-assign" value="0" workid="<?=$value->id?>" time="<?=$current_time?>" usn="<?=$res_value->username?>">
                                                                                    <?
                                                                                }else if($holiday=="s_holiday_td"){
                                                                                    ?>
                                                                                    <div style="margin:0px auto;width:30px;">H</div>
                                                                                    <?
                                                                                }else{
                                                                                    ?>
                                                                                    <div title="<?echo date("- d F Y",$current_time);?>" class="div-info" style="width:30px;height:30px"></div>
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
                                                           $day_pass=($current_time-$project->project_start)/(60*60*24);
                                                           if ($day_pass>30) {
                                                               $exceed_maximun_day=true;
                                                           }
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
    public function reverse_allocate(){
        $end=mktime(0,0,1,date("n"),date("j"),date("Y"))-(60*60*24*5);
        $this->m_work_sheet->delete_work_sheet_assign_no_spend(0,$end,$_POST['project_id']);
    }

}