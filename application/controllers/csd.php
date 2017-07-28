<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');


class csd extends CI_Controller
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
        $this->load->model('m_pce');
        $this->load->model('m_business');
        if ($this->session->userdata('username')) {
            $user_data = $this->m_user->get_user_by_login_name($this->session->userdata('username'));
            if (isset($user_data->username) && isset($user_data->prem['csd'])) {
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
        $data_view['project_list']=$this->m_project->get_project_by_csd_not_sign();
        //$data_view['project_list_all']=$this->m_project->get_all_project_by_status("no_draf");
        $this->load->view('v_header', $data_head);
        $this->load->view('csd/v_csd_list',$data_view);
        $this->load->view('v_footer', $data_foot);

    }

    public function edit_csd()
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
            $this->load->view('csd/v_csd_edit',$data);
            $this->load->view('v_footer',$data_foot);
        
    }

    public function approve_csd()
    {
        $id=$this->uri->segment(3,'');
        $data_foot['table']="yes";
        $data['a']="0";
        $data_head['user_data']=$this->user_data;
        $data_view['project_list']=$this->m_project->get_project_by_csd_not_sign();
        $data['pce_doc']=$this->m_pce->get_all_pce_by_project_id($id);
        if (isset($this->user_data->prem['csd']) ){
            foreach ($data['pce_doc'] as $key => $value) {
                    $pce = array(
                        'csd_sign' => $data_head['user_data']->username, 
                        'csd_sign_time' => time(), 
                        'csd_sign_status' => "y",
                        );

                    $this->m_pce->update_pce($pce,$data['pce_doc'][$key]->id);
                    
            }
                echo "<script type='text/javascript'>alert('success');</script>";
                
                
            }else{
               echo "<script type='text/javascript'>alert('error');</script>";
               
            }
        //print_r($data['pce_doc']);
        redirect('csd', 'refresh');
        
    }
    public function app_bat()
    {
        header('Content-Type: application/json');
            $json = array();
            $json['flag']="OK";      
            $data_head['user_data']=$this->user_data;
        if (isset($_POST['app_bat'])) {
                foreach ($_POST['app_bat'] as $key => $value) {
                    $data['pce_doc']=$this->m_pce->get_all_pce_by_project_id($value);
                    if (isset($this->user_data->prem['csd']) ){
                        foreach ($data['pce_doc'] as $bkey => $bvalue) {
                            $pce = array(
                            'csd_sign' => $data_head['user_data']->username, 
                            'csd_sign_time' => time(), 
                            'csd_sign_status' => "y",
                            );

                        $this->m_pce->update_pce($pce,$data['pce_doc'][$bkey]->id);
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
}