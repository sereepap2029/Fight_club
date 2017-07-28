<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class All extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('m_user');
		$this->load->model('m_project');
		$this->load->model('m_time');
		$this->load->model('m_pce');
		$this->load->model('m_oc');
		$this->load->model('m_company');
		$this->load->model('m_outsource');
		$this->load->model('m_hour_rate');
		$this->load->model('m_Rsheet');
		$this->load->model('m_business');
		$this->load->model('m_work_sheet');
		if ($this->session->userdata('username')) {
			$user_data=$this->m_user->get_user_by_login_name($this->session->userdata('username'));
			$prem_flag=(isset($user_data->prem['cs'])
				||isset($user_data->prem['csd'])
				||isset($user_data->prem['hod'])
				||isset($user_data->prem['fc'])
				||isset($user_data->prem['admin']));
			if (isset($user_data->username)&&$prem_flag) {
				$this->user_data=$user_data;
			}else{
				redirect('main/logout');
			}
		}else{
			redirect('main/logout');
		}
	}

	public function all_project()
	{
		$data_foot['table'] = "yes";
        $data_head['user_data'] = $this->user_data;
        $all_user_under=$this->m_user->get_all_user_by_super_usn_all_lv($this->user_data->username,$this->user_data->username);
        $array_user=$this->m_user->change_node_user_to_array($all_user_under);
        //print_r($array_user);
        $data_view['cs']=$this->m_user->get_all_user_by_prem('cs',true);
        $data_view['filter'] = array();
        $data_view['filter']['start_date']=(time()-(60*60*24*30));;
        if (isset($_POST['start_time'])) {
            $data_view['filter']['start_date']=$this->m_time->datepicker_to_unix($_POST['start_time']);
        }
        $data_view['filter']['end_date']=(time()+(60*60*24*30));
        if (isset($_POST['end_time'])) {
            $data_view['filter']['end_date']=$this->m_time->datepicker_to_unix($_POST['end_time']);
        }
        $data_view['filter']['project_cs']="all";
        if (isset($_POST['project_cs'])) {
            $data_view['filter']['project_cs']=$_POST['project_cs'];
        }

        if (isset($this->user_data->prem['admin'])) {
        	$data_view['project_list'] = $this->m_project->get_all_project(true,false,$data_view['filter']);
        }else{
        	$where = "(status='Proposing' OR status='WIP' OR status='Delay') AND (";
        	$count=1;
        	foreach ($array_user as $key => $value) {
        		if ($count==1) {
        			$where.="project_cs='".$value->username."'";
        		}else{
        			$where.=" OR project_cs='".$value->username."'";
        		}        		
        		$count+=1;
        	}
            if (count($array_user)>0) {
                $where.=" OR ";
            }
            $where.=" project_cs='".$this->user_data->username."') AND status !='Archive'";
        	$data_view['project_list'] = $this->m_project->get_project_by_cs($this->user_data->username,"all",$where,true,$data_view['filter']);
        }
        //echo "<br>".$where;
        $this->load->view('v_header', $data_head);
        $this->load->view('all/v_all_project_list',$data_view);
        $this->load->view('v_footer', $data_foot);
		
	}
    public function archive_project()
    {
        $data_foot['table'] = "yes";
        $data_head['user_data'] = $this->user_data;
        $all_user_under=$this->m_user->get_all_user_by_super_usn_all_lv($this->user_data->username,$this->user_data->username);
        $array_user=$this->m_user->change_node_user_to_array($all_user_under);
        //print_r($array_user);
        $data_view['cs']=$this->m_user->get_all_user_by_prem('cs',true);
        $data_view['filter'] = array();
        $data_view['filter']['start_date']=(time()-(60*60*24*30));;
        if (isset($_POST['start_time'])) {
            $data_view['filter']['start_date']=$this->m_time->datepicker_to_unix($_POST['start_time']);
        }
        $data_view['filter']['end_date']=(time()+(60*60*24*30));
        if (isset($_POST['end_time'])) {
            $data_view['filter']['end_date']=$this->m_time->datepicker_to_unix($_POST['end_time']);
        }
        $data_view['filter']['project_cs']="all";
        if (isset($_POST['project_cs'])) {
            $data_view['filter']['project_cs']=$_POST['project_cs'];
        }

        if (isset($this->user_data->prem['admin'])) {
            $data_view['project_list'] = $this->m_project->get_all_project(false,true,$data_view['filter']);
        }else{
            $where = "(";
            $count=1;
            foreach ($array_user as $key => $value) {
                if ($count==1) {
                    $where.="project_cs='".$value->username."'";
                }else{
                    $where.=" OR project_cs='".$value->username."'";
                }               
                $count+=1;
            }
            if (count($array_user)>0) {
                $where.=" OR ";
            }
            $where.=" project_cs='".$this->user_data->username."')";
            $data_view['project_list'] = $this->m_project->get_project_by_cs($this->user_data->username,"Archive",$where,false,$data_view['filter']);
        }
        //echo "<br>".$where;
        $this->load->view('v_header', $data_head);
        $this->load->view('all/v_all_project_list_archive',$data_view);
        $this->load->view('v_footer', $data_foot);
        
    }
	public function project_detail()
    {
        $id=$this->uri->segment(3,'');
        $data_foot['table']="yes";
        $data_head['user_data']=$this->user_data;
        $data['a']="0";
        $data['project']=$this->m_project->get_project_by_id($id);
        $data['r_sheet']=$this->m_Rsheet->get_all_r_sheet_by_project_id($id);
        $data['pce_doc']=$this->m_pce->get_all_pce_by_project_id($id,false,true);
        $data['bu']=$this->m_company->get_bu_by_id($data['project']->project_bu);
        //$data['company']=$this->m_company->get_company_by_id($data['project']->project_client);
        $data['company']=$this->m_company->get_all_company();
        $data['business_list'] = $this->m_business->get_all_business();
        foreach ($data['r_sheet'] as $key => $value) {
            $data['r_sheet'][$key]->type_obj=$this->m_hour_rate->get_hour_rate_by_id($value->type);
        }
        $data['cs']=$this->m_user->get_all_user_by_prem('cs',true);
        $data['user_data']=$this->user_data;
        //print_r($data['pce_doc']);
            $this->load->view('v_header',$data_head);
            $this->load->view('all/v_project_detail',$data);
            $this->load->view('v_footer',$data_foot);
        
    }
    function edit_project(){
        $project_id=$_POST['project_id'];
        $project_dat = array(
                'project_cs' => $_POST['project_cs'],
                'project_client' => $_POST['project_client'],
                'project_bu' => $_POST['project_bu'],
                'project_start' => $this->m_time->datepicker_to_unix($_POST['project_start']),
                'project_end' => $this->m_time->datepicker_to_unix($_POST['project_end']),
                'project_name' => $_POST['project_name'],
                'business_unit_id' => $_POST['business_unit_id'],
                'account_unit_id' => $_POST['account_unit_id'],
                );
        if (isset($this->user_data->prem['admin'])||isset($this->user_data->prem['csd'])) {
            $this->m_project->update_project($project_dat,$project_id);
        }
        ?>
            <script type="text/javascript">
            window.open("<?=site_url('all/project_detail/'.$project_id)?>","_self");
            </script>
            <?
    }
    public function project_unarchive() {
        $id=$this->uri->segment(3,'');
            $project_dat = array(
                'status' => "Done",
                );
            $this->m_project->update_project($project_dat,$id);
            redirect("all/project_detail/".$id);        
    }
}