<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');


class Delegate extends CI_Controller
{
public function __construct() {
        parent::__construct();
        $this->load->model('m_user');
        $this->load->model('m_project');
        $this->load->model('m_time');
        $this->load->model('m_group');
        $this->load->model('m_hour_rate');
        $this->load->model('m_company');
        $this->load->model('m_Rsheet');
        if ($this->session->userdata('username')) {
            $user_data = $this->m_user->get_user_by_login_name($this->session->userdata('username'));
            if (isset($user_data->username)) {
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
        $data_view['project_list']=$this->m_Rsheet->get_delegate_project_list($this->user_data->username);
        //print_r($data_view['project_list']);
        $this->load->view('v_header', $data_head);
        $this->load->view('hod/v_delegate_list',$data_view);
        $this->load->view('v_footer', $data_foot);
    }

    public function assign_resource()
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
        $data['delegate_r']=$this->m_Rsheet->get_delegate_list_by_project_id($id,$this->user_data->username,"r_id");
            $this->load->view('v_header',$data_head);
            $this->load->view('res/v_assign',$data);
            $this->load->view('v_footer',$data_foot);
        
    }

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
    public function complete_assign()
    {
        $id=$this->uri->segment(3,'');
        $pce_list=$this->m_pce->get_all_pce_by_project_id($id);
        // find parent HOD
        $find_hod=false;
        $supervisor=$this->user_data;
        if (!isset($this->user_data->prem['hod'])) {        
            while ( !$find_hod) {
                $user_data = $this->m_user->get_user_by_login_name($supervisor->supervisor);
                $supervisor=$user_data;
                if (isset($supervisor->prem['hod'])) {                
                    $find_hod=true;
                }
            }
        }
        foreach ($pce_list as $key => $value) {
                $data = array(
                        'complete_assign' => "y", 
                        );
                $this->db->where('hod_usn', $supervisor->username);
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
        }
        //echo $supervisor->username;
        redirect('delegate');
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






}