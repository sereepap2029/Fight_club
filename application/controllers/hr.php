<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');


class hr extends CI_Controller
{
public function __construct() {
        parent::__construct();
        $this->load->model('m_user');
        $this->load->model('m_time');
        $this->load->model('m_hr');
        if ($this->session->userdata('username')) {
            $user_data = $this->m_user->get_user_by_login_name($this->session->userdata('username'));
            if (isset($user_data->username) && isset($user_data->prem['hr'])) {
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
        $data_view['a']="a";
        $this->load->view('v_header', $data_head);
        $this->load->view('hr/dashboard',$data_view);
        $this->load->view('v_footer', $data_foot);
    }

    public function holiday_carlendar()
    {
        $data_foot['table']="yes";
        $data_head['user_data']=$this->user_data;
        $data['a']="0";
        if (!isset($_POST['year'])) {
            $_POST['year']=date("Y");
        }
        $start=mktime(0,0,1,1,1,(int)$_POST['year']);
        $end=mktime(0,0,1,12,31,(int)$_POST['year']);
        $data['holiday']=$this->m_hr->get_all_holiday($start,$end);
            $this->load->view('v_header',$data_head);
            $this->load->view('hr/holiday_carlrndar',$data);
            $this->load->view('v_footer',$data_foot);
        
    }

    public function pop_holiday(){
        $time=$this->uri->segment(3,0);
        if(isset($_POST['time'])){
            header('Content-Type: application/json');
            $json = array();
            $json['flag']="OK";
            $this->m_hr->delete_holiday((int)$_POST['time']);
                    $data = array(
                        'time' => (int)$_POST['time'], 
                        'comment' => $_POST['comment'], 
                        'is_holiday' => $_POST['is_holiday'], 
                        
                        );
                    $this->m_hr->add_holiday($data);
            
            echo json_encode($json);
        }else{
            $data['time']=$time;
            
            $data['holiday']=$this->m_hr->get_holiday_by_time((int)$time);
            if (!isset($data['holiday']->time)) {
                $data['holiday']->time=(int)$time;
                $data['holiday']->comment="no comment";
                $data['holiday']->is_holiday="n";
            }
            $this->load->view('v_header_popup');
            $this->load->view('hr/pop_holiday',$data);
            $this->load->view('v_footer');
        }
        
    }  

    public function user_leave_carlendar()
    {
        $data_foot['table']="yes";
        $data_head['user_data']=$this->user_data;
        $data['a']="0";
        if (!isset($_POST['year'])) {
            $_POST['year']=date("Y");
        }
        $start=mktime(0,0,1,1,1,(int)$_POST['year']);
        $end=mktime(0,0,1,12,31,(int)$_POST['year']);
        $data['user_leave']=$this->m_hr->get_all_user_leave($start,$end);
            $this->load->view('v_header',$data_head);
            $this->load->view('hr/user_leave_car',$data);
            $this->load->view('v_footer',$data_foot);
        
    }
    public function pop_user_leave(){
        $time=$this->uri->segment(3,'');
        if (isset($_POST['add'])) {
            $user_list=$this->m_user->get_all_user();
            ?>
            <tr>
              <td>
                <div class="control-group">
                 <select id="usn" class="chzn-select" name="usn[]">
                     <option value="no">---please select ------</option>
                     <?
                     foreach ($user_list as $key2 => $value2) {
                                             ?>
                         <option value="<?=$value2->username?>"><?=$value2->nickname?></option>
                         <?
                     }
                     ?>
                 </select>
             </div>
              </td>
              <td>
                <input type="text" class="datetimepicker" name="time_start[]" value="09:30">
              </td>
              <td>
                <input type="text" class="datetimepicker" name="time_end[]" valie="19:30">
              </td>
            </tr>
            <?
        }else if(isset($_POST['save'])){
            header('Content-Type: application/json');
            $json = array();
            $json['flag']="OK";
            $json['have_leave']="n";
            $this->m_hr->delete_leave_by_time((int)$_POST['save']);
            
                foreach ($_POST['usn'] as $key => $value) {
                    $data = array(
                        'usn' => $value, 
                        'time_day' => (int)$_POST['save'], 
                        'time_start' => $this->m_time->datetimepicker_to_unix(date("Y/m/d",(int)$_POST['save'])." ".$_POST['time_start'][$key]), 
                        'time_end' => $this->m_time->datetimepicker_to_unix(date("Y/m/d",(int)$_POST['save'])." ".$_POST['time_end'][$key]), 
                        );
                    if ($value!="no") {
                        $this->m_hr->add_leave($data);
                        $json['have_leave']="y";
                    }
                    
                }
            echo json_encode($json);
        }else{
            $data['time']=$time;
            $data['leave_list']=$this->m_hr->get_all_user_leave($time,$time);
            if (isset($data['leave_list'][(int)$time])) {
                $data['leave_list']=$data['leave_list'][(int)$time];
            }
            $data['user_list']=$this->m_user->get_all_user();
            $this->load->view('v_header_popup');
            $this->load->view('hr/v_pop_leave',$data);
            $this->load->view('v_footer');
        }
        
    }
}