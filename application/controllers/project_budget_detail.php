<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Project_budget_detail extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('m_user');
		$this->load->model('m_project');
		$this->load->model('m_time');
		$this->load->model('m_company');
		$this->load->model('m_business');
        $this->load->model('m_forcast');
        $this->load->model('m_account');
        $this->load->model('m_work_sheet');
        $this->load->model('m_hour_rate');
		if ($this->session->userdata('username')) {
			$user_data=$this->m_user->get_user_by_login_name($this->session->userdata('username'));
			$prem_flag=(isset($user_data->prem['cs'])
				||isset($user_data->prem['csd'])
				||isset($user_data->prem['hod'])
				||isset($user_data->prem['fc'])
				||isset($user_data->prem['admin'])||isset($user_data->prem['account']));
			if (isset($user_data->username)&&$prem_flag) {
				$this->user_data=$user_data;
			}else{
				redirect('main/logout');
			}
		}else{
			redirect('main/logout');
		}
	}

	public function index()
	{
		
	}
    public function detail() {
        $project_id = $this->uri->segment(3, '');
        $data_foot['table']="yes";
        $data_head['user_data']=$this->user_data;
        $data['a']="0";
        $data['project']=$this->m_project->get_project_by_id($project_id);
        $data['r_sheet']=$this->m_Rsheet->get_all_r_sheet_by_project_id($project_id);
        $data['pce_doc']=$this->m_pce->get_all_pce_by_project_id($project_id,false,true);
        $data['bu']=$this->m_company->get_bu_by_id($data['project']->project_bu);
        $data['company']=$this->m_company->get_company_by_id($data['project']->project_client);
        $data['business_list'] = $this->m_business->get_all_business();
        $data['work_sheet'] = $this->m_work_sheet->get_work_sheet_by_project_id($project_id);
        foreach ($data['r_sheet'] as $key => $value) {
            $data['r_sheet'][$key]->type_obj=$this->m_hour_rate->get_hour_rate_by_id($value->type);
        }
        foreach ($data['work_sheet'] as $key => $value) {
            $data['work_sheet'][$key]->type_obj=$this->m_hour_rate->get_hour_rate_by_id($value->task_type);
            $data['work_sheet'][$key]->resource=$this->m_work_sheet->get_res_assign_detail_by_work_id($value->id);
        }
        $data['cs']=$this->m_user->get_all_user_by_prem('cs');
        $data['user_data']=$this->user_data;
        //print_r($data['pce_doc']);
            $this->load->view('v_header',$data_head);
            $this->load->view('project_budget_detail/detail',$data);
            $this->load->view('v_footer',$data_foot);
    }
    public function report()
    {
        $data_foot['table'] = "yes";
        $data_head['user_data'] = $this->user_data;
        $data_view['company'] = array();
        $company=$this->m_company->get_all_company();
        $bu=$this->m_company->get_all_bu();
        foreach ($company as $key => $value) {
            $data_view['company'][$value->id]=$value;
        }
        $data_view['bu'] = array();
        foreach ($bu as $key => $value) {
            $data_view['bu'][$value->id]=$value;
        }
        
        $data_view['business_list'] = $this->m_business->get_all_business();

        if (isset($_POST['start_time'])&&isset($_POST['end_time'])) {
            $data_view['start_time']=$this->m_time->datepicker_to_unix($_POST['start_time']);
            $data_view['end_carlendar_unix']=$this->m_time->datepicker_to_unix($_POST['end_time']);
        }else{
            $data_view['start_time']=mktime(0,0,1,1,1,date("Y"));
            $data_view['end_carlendar_unix']=mktime(0,0,1,12,31,date("Y")); 
        }
        if (isset($_POST['business_unit_id'])) {
            $data_view['bus_unit']=$_POST['business_unit_id'];
        }else{
            $data_view['bus_unit']="all";
        }
        if (isset($_POST['mode'])) {
            $data_view['mode']=(int)$_POST['mode'];
        }else{
            $data_view['mode']=1;
        }
        $multi_usn = array("all" => "all");
        if (isset($_POST['multi_usn'])) {
            
        }
        if (isset($this->user_data->prem['cs'])&&(!isset($this->user_data->prem['admin'])&&!isset($this->user_data->prem['account'])&&!isset($this->user_data->prem['csd']))) {
            $all_user_under=$this->m_user->get_all_user_by_super_usn_all_lv($this->user_data->username,$this->user_data->username);
            $array_user=$this->m_user->change_node_user_to_array($all_user_under);
            $array_user[$this->user_data->username]=$this->user_data->username;
            $data_view['forcast_report']=$this->m_forcast->get_forcast_report($data_view['start_time'],$data_view['end_carlendar_unix'],$data_view['bus_unit'],$array_user,$data_view['mode']);
            $data_view['pce_report']=$this->m_forcast->get_forcast_pce_report($data_view['start_time'],$data_view['end_carlendar_unix'],$data_view['bus_unit'],$array_user,$data_view['mode']);
            $data_view['target_bill_report']=$this->m_forcast->get_forcast_target_bill_report($data_view['start_time'],$data_view['end_carlendar_unix'],$data_view['bus_unit'],$array_user,$data_view['mode']);
            $data_view['actual_bill_report']=$this->m_forcast->get_forcast_actual_bill_report($data_view['start_time'],$data_view['end_carlendar_unix'],$data_view['bus_unit'],$array_user,$data_view['mode']);
        }else{
            $data_view['forcast_report']=$this->m_forcast->get_forcast_report($data_view['start_time'],$data_view['end_carlendar_unix'],$data_view['bus_unit'],$multi_usn,$data_view['mode']);
            $data_view['pce_report']=$this->m_forcast->get_forcast_pce_report($data_view['start_time'],$data_view['end_carlendar_unix'],$data_view['bus_unit'],$multi_usn,$data_view['mode']);
            $data_view['target_bill_report']=$this->m_forcast->get_forcast_target_bill_report($data_view['start_time'],$data_view['end_carlendar_unix'],$data_view['bus_unit'],$multi_usn,$data_view['mode']);
            $data_view['actual_bill_report']=$this->m_forcast->get_forcast_actual_bill_report($data_view['start_time'],$data_view['end_carlendar_unix'],$data_view['bus_unit'],$multi_usn,$data_view['mode']);
        }
        //print_r($data_view['target_bill_report']);
        $this->load->view('v_header', $data_head);
        if ($data_view['mode']==1) {
            $this->load->view('forcast/v_report',$data_view);
        }else if ($data_view['mode']==2||$data_view['mode']==3) {
            $this->load->view('forcast/v_report_m2',$data_view);
        }
        
        $this->load->view('v_footer', $data_foot);
        
    }

    public function operatingGPReport()
    {
        $data_foot['table'] = "yes";
        $data_head['user_data'] = $this->user_data;
        $data_view['company'] = array();
        $company=$this->m_company->get_all_company();
        $bu=$this->m_company->get_all_bu();
        foreach ($company as $key => $value) {
            $data_view['company'][$value->id]=$value;
        }
        $data_view['bu'] = array();
        foreach ($bu as $key => $value) {
            $data_view['bu'][$value->id]=$value;
        }
        
        $data_view['business_list'] = $this->m_business->get_all_business();

        if (isset($_POST['start_time'])&&isset($_POST['end_time'])) {
            $data_view['start_time']=$this->m_time->datepicker_to_unix($_POST['start_time']);
            $data_view['end_carlendar_unix']=$this->m_time->datepicker_to_unix($_POST['end_time']);
        }else{
            $data_view['start_time']=mktime(0,0,1,1,1,date("Y"));
            $data_view['end_carlendar_unix']=mktime(0,0,1,12,31,date("Y")); 
        }
        if (isset($_POST['business_unit_id'])) {
            $data_view['bus_unit']=$_POST['business_unit_id'];
        }else{
            $data_view['bus_unit']="all";
        }
        if (isset($_POST['mode'])) {
            $data_view['mode']=(int)$_POST['mode'];
        }else{
            $data_view['mode']=1;
        }
        $multi_usn = array("all" => "all");
        $data_view['multi_usn']="all";
        if (isset($_POST['multi_usn'])) {
            unset($multi_usn['all']);
            $multi_usn[$_POST['multi_usn']]=$_POST['multi_usn'];
            $data_view['multi_usn']=$_POST['multi_usn'];
        }        
        $data_view['cs']=$this->m_user->get_all_user_by_prem('cs');
        if (isset($this->user_data->prem['cs'])&&(!isset($this->user_data->prem['admin'])&&!isset($this->user_data->prem['account'])&&!isset($this->user_data->prem['csd']))) {
            $all_user_under=$this->m_user->get_all_user_by_super_usn_all_lv($this->user_data->username,$this->user_data->username);
            $array_user=$this->m_user->change_node_user_to_array($all_user_under);
            $array_user[$this->user_data->username]=$this->user_data->username;
            $data_view['forcast_report']=$this->m_forcast->get_forcast_report($data_view['start_time'],$data_view['end_carlendar_unix'],$data_view['bus_unit'],$array_user);
            $data_view['pce_report']=$this->m_forcast->get_forcast_pce_report($data_view['start_time'],$data_view['end_carlendar_unix'],$data_view['bus_unit'],$array_user);
            $data_view['target_bill_report']=$this->m_forcast->get_forcast_target_bill_report($data_view['start_time'],$data_view['end_carlendar_unix'],$data_view['bus_unit'],$array_user);
            $data_view['actual_bill_report']=$this->m_forcast->get_forcast_actual_bill_report($data_view['start_time'],$data_view['end_carlendar_unix'],$data_view['bus_unit'],$array_user);

            $data_view['forcast_out_report']=$this->m_account->get_forcast_outsource_report($data_view['start_time'],$data_view['end_carlendar_unix'],$data_view['bus_unit'],$array_user);
            $data_view['outsource_report']=$this->m_account->get_outsource_report($data_view['start_time'],$data_view['end_carlendar_unix'],$data_view['bus_unit'],$array_user);
            $data_view['target_out_report']=$this->m_account->get_outsource_target_bill_report($data_view['start_time'],$data_view['end_carlendar_unix'],$data_view['bus_unit'],$array_user);
            $data_view['actual_out_report']=$this->m_account->get_outsource_actual_bill_report($data_view['start_time'],$data_view['end_carlendar_unix'],$data_view['bus_unit'],$array_user);


            $data_view['forcast_report_cash']=$this->m_forcast->get_forcast_receive_report($data_view['start_time'],$data_view['end_carlendar_unix'],$data_view['bus_unit'],$array_user);
            $data_view['pce_report_cash']=$this->m_forcast->get_forcast_receive_pce_report($data_view['start_time'],$data_view['end_carlendar_unix'],$data_view['bus_unit'],$array_user);
            $data_view['target_bill_report_cash']=$this->m_forcast->get_forcast_receive_target_bill_report($data_view['start_time'],$data_view['end_carlendar_unix'],$data_view['bus_unit'],$array_user);
            $data_view['actual_bill_report_cash']=$this->m_forcast->get_forcast_receive_actual_bill_report($data_view['start_time'],$data_view['end_carlendar_unix'],$data_view['bus_unit'],$array_user);

            $data_view['forcast_out_report_cash']=$this->m_account->get_forcast_outsource_paid_report($data_view['start_time'],$data_view['end_carlendar_unix'],$data_view['bus_unit'],$array_user);
            $data_view['outsource_report_cash']=$this->m_account->get_outsource_paid_report($data_view['start_time'],$data_view['end_carlendar_unix'],$data_view['bus_unit'],$array_user);
            $data_view['target_out_report_cash']=$this->m_account->get_outsource_paid_target_bill_report($data_view['start_time'],$data_view['end_carlendar_unix'],$data_view['bus_unit'],$array_user);
            $data_view['actual_out_report_cash']=$this->m_account->get_outsource_paid_actual_bill_report($data_view['start_time'],$data_view['end_carlendar_unix'],$data_view['bus_unit'],$array_user);
        }else{
            $data_view['forcast_report']=$this->m_forcast->get_forcast_report($data_view['start_time'],$data_view['end_carlendar_unix'],$data_view['bus_unit'],$multi_usn,$data_view['mode']);
            $data_view['pce_report']=$this->m_forcast->get_forcast_pce_report($data_view['start_time'],$data_view['end_carlendar_unix'],$data_view['bus_unit'],$multi_usn,$data_view['mode']);
            $data_view['target_bill_report']=$this->m_forcast->get_forcast_target_bill_report($data_view['start_time'],$data_view['end_carlendar_unix'],$data_view['bus_unit'],$multi_usn,$data_view['mode']);
            $data_view['actual_bill_report']=$this->m_forcast->get_forcast_actual_bill_report($data_view['start_time'],$data_view['end_carlendar_unix'],$data_view['bus_unit'],$multi_usn,$data_view['mode']);

            $data_view['forcast_out_report']=$this->m_account->get_forcast_outsource_report($data_view['start_time'],$data_view['end_carlendar_unix'],$data_view['bus_unit'],$multi_usn,$data_view['mode']);
            $data_view['outsource_report']=$this->m_account->get_outsource_report($data_view['start_time'],$data_view['end_carlendar_unix'],$data_view['bus_unit'],$multi_usn,$data_view['mode']);
            $data_view['target_out_report']=$this->m_account->get_outsource_target_bill_report($data_view['start_time'],$data_view['end_carlendar_unix'],$data_view['bus_unit'],$multi_usn,$data_view['mode']);
            $data_view['actual_out_report']=$this->m_account->get_outsource_actual_bill_report($data_view['start_time'],$data_view['end_carlendar_unix'],$data_view['bus_unit'],$multi_usn,$data_view['mode']);


            $data_view['forcast_report_cash']=$this->m_forcast->get_forcast_receive_report($data_view['start_time'],$data_view['end_carlendar_unix'],$data_view['bus_unit'],$multi_usn,$data_view['mode']);
            $data_view['pce_report_cash']=$this->m_forcast->get_forcast_receive_pce_report($data_view['start_time'],$data_view['end_carlendar_unix'],$data_view['bus_unit'],$multi_usn,$data_view['mode']);
            $data_view['target_bill_report_cash']=$this->m_forcast->get_forcast_receive_target_bill_report($data_view['start_time'],$data_view['end_carlendar_unix'],$data_view['bus_unit'],$multi_usn,$data_view['mode']);
            $data_view['actual_bill_report_cash']=$this->m_forcast->get_forcast_receive_actual_bill_report($data_view['start_time'],$data_view['end_carlendar_unix'],$data_view['bus_unit'],$multi_usn,$data_view['mode']);

            $data_view['forcast_out_report_cash']=$this->m_account->get_forcast_outsource_paid_report($data_view['start_time'],$data_view['end_carlendar_unix'],$data_view['bus_unit'],$multi_usn,$data_view['mode']);
            $data_view['outsource_report_cash']=$this->m_account->get_outsource_paid_report($data_view['start_time'],$data_view['end_carlendar_unix'],$data_view['bus_unit'],$multi_usn,$data_view['mode']);
            $data_view['target_out_report_cash']=$this->m_account->get_outsource_paid_target_bill_report($data_view['start_time'],$data_view['end_carlendar_unix'],$data_view['bus_unit'],$multi_usn,$data_view['mode']);
            $data_view['actual_out_report_cash']=$this->m_account->get_outsource_paid_actual_bill_report($data_view['start_time'],$data_view['end_carlendar_unix'],$data_view['bus_unit'],$multi_usn,$data_view['mode']);
        }
        //print_r($data_view['target_bill_report']);

        $this->load->view('v_header', $data_head);
        if ($data_view['mode']==1) {
            $this->load->view('forcast/v_operatingGPReport',$data_view);
        }else if ($data_view['mode']==2||$data_view['mode']==3) {
            // equal array
            $data_view['pce_report']=$this->m_forcast->gd_equal_array($data_view['outsource_report'],$data_view['pce_report']);
            $data_view['target_bill_report']=$this->m_forcast->gd_equal_array($data_view['target_out_report'],$data_view['target_bill_report']);
            $data_view['actual_bill_report']=$this->m_forcast->gd_equal_array($data_view['actual_out_report'],$data_view['actual_bill_report']);

            $data_view['pce_report_cash']=$this->m_forcast->gd_equal_array($data_view['outsource_report_cash'],$data_view['pce_report_cash']);
            $data_view['target_bill_report_cash']=$this->m_forcast->gd_equal_array($data_view['target_out_report_cash'],$data_view['target_bill_report_cash']);
            $data_view['actual_bill_report_cash']=$this->m_forcast->gd_equal_array($data_view['actual_out_report_cash'],$data_view['actual_bill_report_cash']);
            // end equal array
            $this->load->view('forcast/v_operatingGPReport_m2_sum',$data_view);
        }
        $this->load->view('v_footer', $data_foot);
        
    }


























    public function forcest_excel()
    {
        $data_view['business_unit_id']="no";
        $start_time=0;
        $end_carlendar_unix=0;
        $bus_unit="";
        $company = array();
        $bu = array();
        $company_tmp=$this->m_company->get_all_company();
        $bu_tmp=$this->m_company->get_all_bu();
        foreach ($company_tmp as $key => $value) {
            $company[$value->id]=$value;
        }
        foreach ($bu_tmp as $key => $value) {
            $bu[$value->id]=$value;
        }
        
        $business_list = $this->m_business->get_all_business();
        if (isset($_POST['start_time'])&&isset($_POST['end_time'])) {
            $start_time=$this->m_time->datepicker_to_unix($_POST['start_time']);
            $end_carlendar_unix=$this->m_time->datepicker_to_unix($_POST['end_time']);
        }else{
            $start_time=mktime(0,0,1,1,1,date("Y"));
            $end_carlendar_unix=mktime(0,0,1,12,31,date("Y")); 
        }
        if (isset($_POST['business_unit_id'])) {
            $bus_unit=$_POST['business_unit_id'];
        }else{
            $bus_unit="all";
        }
        $forcast_report=$this->m_forcast->get_forcast_report($start_time,$end_carlendar_unix,$bus_unit);
        $pce_report=$this->m_forcast->get_forcast_pce_report($start_time,$end_carlendar_unix,$bus_unit);
        $target_bill_report=$this->m_forcast->get_forcast_target_bill_report($start_time,$end_carlendar_unix,$bus_unit);
        $actual_bill_report=$this->m_forcast->get_forcast_actual_bill_report($start_time,$end_carlendar_unix,$bus_unit);
        if (isset($this->user_data->prem['cs'])&&(!isset($this->user_data->prem['admin'])&&!isset($this->user_data->prem['account'])&&!isset($this->user_data->prem['csd']))) {
            $all_user_under=$this->m_user->get_all_user_by_super_usn_all_lv($this->user_data->username,$this->user_data->username);
            $array_user=$this->m_user->change_node_user_to_array($all_user_under);
            $array_user[$this->user_data->username]=$this->user_data->username;
            $forcast_report=$this->m_forcast->get_forcast_report($start_time,$end_carlendar_unix,$bus_unit,$array_user);
            $pce_report=$this->m_forcast->get_forcast_pce_report($start_time,$end_carlendar_unix,$bus_unit,$array_user);
            $target_bill_report=$this->m_forcast->get_forcast_target_bill_report($start_time,$end_carlendar_unix,$bus_unit,$array_user);
            $actual_bill_report=$this->m_forcast->get_forcast_actual_bill_report($start_time,$end_carlendar_unix,$bus_unit,$array_user);
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
        $num=0;
        $sumval=0;
        $sumvalr=0;
        $current_time=$start_time;
        $cur_month=1000;
        $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(0, $cur_row)->setValueExplicit('Month->', PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('A')->setAutoSize(true);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('B')->setAutoSize(true);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('C')->setAutoSize(true);
        $cur_col+=2;
        while ($current_time<=$end_carlendar_unix) {
            if ($cur_month!=date("n",$current_time)) {
                $cur_month=date("n",$current_time);
                $cur_day=date("j",$current_time);
                $numday=date("t",$current_time);
                $cur_col+=1;
                $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit(date("F",$current_time), PHPExcel_Cell_DataType::TYPE_STRING);
                
            }                                                        
           $current_time+=(60*60*24*$numday);
        }
                $cur_col+=1;
                $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit("Total", PHPExcel_Cell_DataType::TYPE_STRING);

        
        $cur_row+=1;
        $cur_col=0;
        $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(0, $cur_row)->setValueExplicit('Forcast', PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex(0).$cur_row)->applyFromArray($styleblack);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex(1).$cur_row)->applyFromArray($styleblack);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex(2).$cur_row)->applyFromArray($styleblack);
        $cur_col+=2;
        $current_time=$start_time;
        $cur_month=1000;
        $month_amount=0;
        $sum_total=0;
        while ($current_time<=$end_carlendar_unix) {
            $cur_day=date("j",$current_time);
            $numday=date("t",$current_time);
            foreach ($forcast_report as $key => $value) {          
              if (isset($value->forcast_list[$current_time])) {                                       
                foreach ($value->forcast_list[$current_time] as $key2 => $value2) {
                  $month_amount+=$value2->project_value;
                }
              }
            }
            if ($cur_month!=date("n",$current_time)&&$cur_day==$numday) {
                $cur_month=date("n",$current_time); 
                $cur_col+=1;
                $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit(number_format($month_amount), PHPExcel_Cell_DataType::TYPE_STRING);                                                                 
                $objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col).$cur_row)->applyFromArray($styleblack);
                $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($cur_col))->setAutoSize(true);
                $sum_total+=$month_amount;
                $month_amount=0;
            }                                                        
           $current_time+=(60*60*24);
        }
                $cur_col+=1;
                $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit(number_format($sum_total), PHPExcel_Cell_DataType::TYPE_STRING);                                                                 
                $objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col).$cur_row)->applyFromArray($styleblack);
                $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($cur_col))->setAutoSize(true);
        $cur_row+=1;

        foreach ($forcast_report as $key => $value) {
        $cur_col=0;    
        $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit("      ".$value->firstname." ".$value->lastname, PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col).$cur_row)->applyFromArray($stylegray);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col+1).$cur_row)->applyFromArray($stylegray);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col+2).$cur_row)->applyFromArray($stylegray);
        $cur_col+=2;
          $current_time=$start_time;
              $cur_month=1000;
              $month_amount=0;
              $sum_total=0;
              while ($current_time<=$end_carlendar_unix) {
                  $cur_day=date("j",$current_time);
                  $numday=date("t",$current_time);         
                    if (isset($value->forcast_list[$current_time])) {                                       
                      foreach ($value->forcast_list[$current_time] as $key2 => $value2) {
                        $month_amount+=$value2->project_value;
                      }
                  }
                  if ($cur_month!=date("n",$current_time)&&$cur_day==$numday) {
                      $cur_month=date("n",$current_time);                                                  
                      $cur_col+=1;
                      $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit(number_format($month_amount), PHPExcel_Cell_DataType::TYPE_STRING);
                      $objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col).$cur_row)->applyFromArray($stylegray);
                      $sum_total+=$month_amount;
                      $month_amount=0;
                  }                                                        
                 $current_time+=(60*60*24);
              }
                    $cur_col+=1;
                      $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit(number_format($sum_total), PHPExcel_Cell_DataType::TYPE_STRING);
                      $objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col).$cur_row)->applyFromArray($stylegray);
        $cur_row+=1;      
        foreach ($value->forcast_list as $key2 => $value2) {
          foreach ($value2 as $key3 => $value3) {
            $cur_col=0;
            $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit("            ".$value3->project_name, PHPExcel_Cell_DataType::TYPE_STRING);
            $cur_col+=1;
            $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit($company[$value3->project_client]->name." ".$bu[$value3->project_bu]->bu_name, PHPExcel_Cell_DataType::TYPE_STRING);
            $cur_col+=1;
            $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit('', PHPExcel_Cell_DataType::TYPE_STRING);
            
                $current_time=$start_time;
                    $cur_month=1000;
                    $month_amount=0;
                    $sum_total=0;
                    while ($current_time<=$end_carlendar_unix) {
                        $cur_day=date("j",$current_time);
                        $numday=date("t",$current_time);         
                          if ($current_time==$value3->project_end) {                                       
                              $month_amount+=$value3->project_value;
                          }
                        if ($cur_month!=date("n",$current_time)&&$cur_day==$numday) {
                            $cur_month=date("n",$current_time);                                                  
                            $cur_col+=1;
                            $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit(number_format($month_amount), PHPExcel_Cell_DataType::TYPE_STRING);
                            $sum_total+=$month_amount;
                            $month_amount=0;
                        }                                                        
                       $current_time+=(60*60*24);
                    }
                            $cur_col+=1;
                            $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit(number_format($sum_total), PHPExcel_Cell_DataType::TYPE_STRING);
                $cur_row+=1;    

              }
        }
        }



        /////////////////////////////////////////////// PCE //////////////////////////////////////////

        $cur_col=0;
        $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(0, $cur_row)->setValueExplicit('PCE', PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex(0).$cur_row)->applyFromArray($styleblack);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex(1).$cur_row)->applyFromArray($styleblack);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex(2).$cur_row)->applyFromArray($styleblack);
        $cur_col+=2;
        $current_time=$start_time;
        $cur_month=1000;
        $month_amount=0;
        $sum_total=0;
        while ($current_time<=$end_carlendar_unix) {
            $cur_day=date("j",$current_time);
            $numday=date("t",$current_time);
            foreach ($pce_report as $key => $value) {          
              if (isset($value->forcast_list[$current_time])) {                                       
                foreach ($value->forcast_list[$current_time] as $key2 => $value2) {
                  foreach ($value2->pce as $key3 => $value3) {
                    $month_amount+=$value3->pce_amount;
                  }
                  
                }
              }
            }
            if ($cur_month!=date("n",$current_time)&&$cur_day==$numday) {
                $cur_month=date("n",$current_time); 
                $cur_col+=1;
                $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit(number_format($month_amount), PHPExcel_Cell_DataType::TYPE_STRING);                                                                 
                $objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col).$cur_row)->applyFromArray($styleblack);
                $sum_total+=$month_amount;
                $month_amount=0;
            }                                                        
           $current_time+=(60*60*24);
        }
        $cur_col+=1;
                $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit(number_format($sum_total), PHPExcel_Cell_DataType::TYPE_STRING);                                                                 
                $objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col).$cur_row)->applyFromArray($styleblack);
        $cur_row+=1;

        foreach ($pce_report as $key => $value) {
        $cur_col=0;    
        $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit("      ".$value->firstname." ".$value->lastname, PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col).$cur_row)->applyFromArray($stylegray);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col+1).$cur_row)->applyFromArray($stylegray);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col+2).$cur_row)->applyFromArray($stylegray);
        $cur_col+=2;
          $current_time=$start_time;
              $cur_month=1000;
              $month_amount=0;
              $sum_total=0;
              while ($current_time<=$end_carlendar_unix) {
                  $cur_day=date("j",$current_time);
                  $numday=date("t",$current_time);         
                    if (isset($value->forcast_list[$current_time])) {                                       
                     foreach ($value->forcast_list[$current_time] as $key2 => $value2) {
                       foreach ($value2->pce as $key3 => $value3) {
                         $month_amount+=$value3->pce_amount;
                       }
                     }
                  }
                  if ($cur_month!=date("n",$current_time)&&$cur_day==$numday) {
                      $cur_month=date("n",$current_time);                                                  
                      $cur_col+=1;
                      $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit(number_format($month_amount), PHPExcel_Cell_DataType::TYPE_STRING);
                      $objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col).$cur_row)->applyFromArray($stylegray);
                      $sum_total+=$month_amount;
                      $month_amount=0;
                  }                                                        
                 $current_time+=(60*60*24);
              }
              $cur_col+=1;
                      $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit(number_format($sum_total), PHPExcel_Cell_DataType::TYPE_STRING);
                      $objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col).$cur_row)->applyFromArray($stylegray);
        $cur_row+=1;      
        foreach ($value->forcast_list as $key2 => $value2) {
          foreach ($value2 as $key3 => $value3) {
            $cur_col=0;
            $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit("            ".$value3->project_name, PHPExcel_Cell_DataType::TYPE_STRING);
            $cur_col+=1;
            $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit($company[$value3->project_client]->name." ".$bu[$value3->project_bu]->bu_name, PHPExcel_Cell_DataType::TYPE_STRING);
            $cur_col+=1;
            $pce_no_str="";
            foreach ($value3->pce as $key4 => $value4) {
                $pce_no_str.=$value4->pce_no.",";
            }
            $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit($pce_no_str, PHPExcel_Cell_DataType::TYPE_STRING);
            
                $current_time=$start_time;
                    $cur_month=1000;
                    $month_amount=0;
                    $sum_total=0;
                    while ($current_time<=$end_carlendar_unix) {
                        $cur_day=date("j",$current_time);
                        $numday=date("t",$current_time);         
                          if ($current_time==$value3->project_end) {   
                              foreach ($value3->pce as $key4 => $value4) {
                                $month_amount+=$value4->pce_amount;
                              }
                          }
                        if ($cur_month!=date("n",$current_time)&&$cur_day==$numday) {
                            $cur_month=date("n",$current_time);                                                  
                            $cur_col+=1;
                            $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit(number_format($month_amount), PHPExcel_Cell_DataType::TYPE_STRING);
                            $sum_total+=$month_amount;
                            $month_amount=0;
                        }                                                        
                       $current_time+=(60*60*24);
                    }
                    $cur_col+=1;
                            $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit(number_format($sum_total), PHPExcel_Cell_DataType::TYPE_STRING);
                $cur_row+=1;    

              }
        }
        }





        /////////////////////////////////////////////// Target Billing (OC) //////////////////////////////////////////

        $cur_col=0;
        $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(0, $cur_row)->setValueExplicit('Target Billing (OC)', PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex(0).$cur_row)->applyFromArray($styleblack);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex(1).$cur_row)->applyFromArray($styleblack);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex(2).$cur_row)->applyFromArray($styleblack);
        $cur_col+=2;
        $current_time=$start_time;
        $cur_month=1000;
        $month_amount=0;
        $sum_total=0;
        while ($current_time<=$end_carlendar_unix) {
            $cur_day=date("j",$current_time);
            $numday=date("t",$current_time);
            foreach ($target_bill_report as $key => $value) {       
              foreach ($value->forcast_list as $key2 => $value2) {
                foreach ($value2->oc as $key3 => $value3) {
                  if (isset($value3->oc_bill[$current_time])) {
                    foreach ($value3->oc_bill[$current_time] as $key4 => $value4) {
                      $month_amount+=$value4->amount;
                    }
                  }
                }
              }   
            }
            if ($cur_month!=date("n",$current_time)&&$cur_day==$numday) {
                $cur_month=date("n",$current_time); 
                $cur_col+=1;
                $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit(number_format($month_amount), PHPExcel_Cell_DataType::TYPE_STRING);                                                                 
                $objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col).$cur_row)->applyFromArray($styleblack);
                $sum_total+=$month_amount;
                $month_amount=0;
            }                                                        
           $current_time+=(60*60*24);
        }
        $cur_col+=1;
                $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit(number_format($sum_total), PHPExcel_Cell_DataType::TYPE_STRING);                                                                 
                $objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col).$cur_row)->applyFromArray($styleblack);
        $cur_row+=1;

        foreach ($target_bill_report as $key => $value) {
        $cur_col=0;    
        $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit("      ".$value->firstname." ".$value->lastname, PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col).$cur_row)->applyFromArray($stylegray);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col+1).$cur_row)->applyFromArray($stylegray);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col+2).$cur_row)->applyFromArray($stylegray);
        $cur_col+=2;
          $current_time=$start_time;
              $cur_month=1000;
              $month_amount=0;
              $sum_total=0;
              while ($current_time<=$end_carlendar_unix) {
                  $cur_day=date("j",$current_time);
                  $numday=date("t",$current_time);         
                    foreach ($value->forcast_list as $key2 => $value2) {
                        foreach ($value2->oc as $key3 => $value3) {
                          if (isset($value3->oc_bill[$current_time])) {
                            foreach ($value3->oc_bill[$current_time] as $key4 => $value4) {
                              $month_amount+=$value4->amount;
                            }
                          }
                        }
                      }
                  if ($cur_month!=date("n",$current_time)&&$cur_day==$numday) {
                      $cur_month=date("n",$current_time);                                                  
                      $cur_col+=1;
                      $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit(number_format($month_amount), PHPExcel_Cell_DataType::TYPE_STRING);
                      $objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col).$cur_row)->applyFromArray($stylegray);
                      $sum_total+=$month_amount;
                      $month_amount=0;
                  }                                                        
                 $current_time+=(60*60*24);
              }
              $cur_col+=1;
                      $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit(number_format($sum_total), PHPExcel_Cell_DataType::TYPE_STRING);
                      $objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col).$cur_row)->applyFromArray($stylegray);
        $cur_row+=1;      
        foreach ($value->forcast_list as $key2 => $value2) {
            $cur_col=0;
            $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit("            ".$value2->project_name, PHPExcel_Cell_DataType::TYPE_STRING);
            $cur_col+=1;
            $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit($company[$value2->project_client]->name." ".$bu[$value2->project_bu]->bu_name, PHPExcel_Cell_DataType::TYPE_STRING);
            $cur_col+=1;
            $oc_no_str="";
            foreach ($value2->oc as $key3 => $value3) {
                $oc_no_str.=$value3->oc_no.",";
            }
            $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit($oc_no_str, PHPExcel_Cell_DataType::TYPE_STRING);
            
                $current_time=$start_time;
                    $cur_month=1000;
                    $month_amount=0;
                    $sum_total=0;
                    while ($current_time<=$end_carlendar_unix) {
                        $cur_day=date("j",$current_time);
                        $numday=date("t",$current_time);         
                         foreach ($value2->oc as $key3 => $value3) {
                            if (isset($value3->oc_bill[$current_time])) {
                              foreach ($value3->oc_bill[$current_time] as $key4 => $value4) {
                                $month_amount+=$value4->amount;
                              }
                            }                                                              
                          }
                        if ($cur_month!=date("n",$current_time)&&$cur_day==$numday) {
                            $cur_month=date("n",$current_time);                                                  
                            $cur_col+=1;
                            $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit(number_format($month_amount), PHPExcel_Cell_DataType::TYPE_STRING);
                            $sum_total+=$month_amount;
                            $month_amount=0;
                        }                                                        
                       $current_time+=(60*60*24);
                    }
                    $cur_col+=1;
                            $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit(number_format($sum_total), PHPExcel_Cell_DataType::TYPE_STRING);
                $cur_row+=1;    

              }
        
        }



        /////////////////////////////////////////////// Actual Billing //////////////////////////////////////////

        $cur_col=0;
        $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(0, $cur_row)->setValueExplicit('Actual Billing', PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex(0).$cur_row)->applyFromArray($styleblack);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex(1).$cur_row)->applyFromArray($styleblack);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex(2).$cur_row)->applyFromArray($styleblack);
        $cur_col+=2;
        $current_time=$start_time;
        $cur_month=1000;
        $month_amount=0;
        $sum_total=0;
        while ($current_time<=$end_carlendar_unix) {
            $cur_day=date("j",$current_time);
            $numday=date("t",$current_time);
            foreach ($actual_bill_report as $key => $value) {       
              foreach ($value->forcast_list as $key2 => $value2) {
                foreach ($value2->oc as $key3 => $value3) {
                  if (isset($value3->oc_bill[$current_time])) {
                    foreach ($value3->oc_bill[$current_time] as $key4 => $value4) {
                      $month_amount+=$value4->paid_amount;
                    }
                  }
                }
              }   
            }
            if ($cur_month!=date("n",$current_time)&&$cur_day==$numday) {
                $cur_month=date("n",$current_time); 
                $cur_col+=1;
                $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit(number_format($month_amount), PHPExcel_Cell_DataType::TYPE_STRING);                                                                 
                $objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col).$cur_row)->applyFromArray($styleblack);
                $sum_total+=$month_amount;
                $month_amount=0;
            }                                                        
           $current_time+=(60*60*24);
        }
        $cur_col+=1;
                $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit(number_format($sum_total), PHPExcel_Cell_DataType::TYPE_STRING);                                                                 
                $objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col).$cur_row)->applyFromArray($styleblack);
        $cur_row+=1;

        foreach ($actual_bill_report as $key => $value) {
        $cur_col=0;    
        $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit("      ".$value->firstname." ".$value->lastname, PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col).$cur_row)->applyFromArray($stylegray);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col+1).$cur_row)->applyFromArray($stylegray);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col+2).$cur_row)->applyFromArray($stylegray);
        $cur_col+=2;
          $current_time=$start_time;
              $cur_month=1000;
              $month_amount=0;
              $sum_total=0;
              while ($current_time<=$end_carlendar_unix) {
                  $cur_day=date("j",$current_time);
                  $numday=date("t",$current_time);         
                    foreach ($value->forcast_list as $key2 => $value2) {
                        foreach ($value2->oc as $key3 => $value3) {
                          if (isset($value3->oc_bill[$current_time])) {
                            foreach ($value3->oc_bill[$current_time] as $key4 => $value4) {
                              $month_amount+=$value4->paid_amount;
                            }
                          }
                        }
                      }
                  if ($cur_month!=date("n",$current_time)&&$cur_day==$numday) {
                      $cur_month=date("n",$current_time);                                                  
                      $cur_col+=1;
                      $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit(number_format($month_amount), PHPExcel_Cell_DataType::TYPE_STRING);
                      $objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col).$cur_row)->applyFromArray($stylegray);
                      $sum_total+=$month_amount;
                      $month_amount=0;
                  }                                                        
                 $current_time+=(60*60*24);
              }
              $cur_col+=1;
                      $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit(number_format($sum_total), PHPExcel_Cell_DataType::TYPE_STRING);
                      $objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col).$cur_row)->applyFromArray($stylegray);
        $cur_row+=1;      
        foreach ($value->forcast_list as $key2 => $value2) {
            $cur_col=0;
            $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit("            ".$value2->project_name, PHPExcel_Cell_DataType::TYPE_STRING);
            $cur_col+=1;
            $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit($company[$value2->project_client]->name." ".$bu[$value2->project_bu]->bu_name, PHPExcel_Cell_DataType::TYPE_STRING);
            $cur_col+=1;
            $oc_no_str="";
            foreach ($value2->oc as $key3 => $value3) {
                $oc_no_str.=$value3->oc_no.",";
            }
            $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit($oc_no_str, PHPExcel_Cell_DataType::TYPE_STRING);
            
                $current_time=$start_time;
                    $cur_month=1000;
                    $month_amount=0;
                    $sum_total=0;
                    while ($current_time<=$end_carlendar_unix) {
                        $cur_day=date("j",$current_time);
                        $numday=date("t",$current_time);         
                         foreach ($value2->oc as $key3 => $value3) {
                            if (isset($value3->oc_bill[$current_time])) {
                              foreach ($value3->oc_bill[$current_time] as $key4 => $value4) {
                                $month_amount+=$value4->paid_amount;
                              }
                            }                                                              
                          }
                        if ($cur_month!=date("n",$current_time)&&$cur_day==$numday) {
                            $cur_month=date("n",$current_time);                                                  
                            $cur_col+=1;
                            $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit(number_format($month_amount), PHPExcel_Cell_DataType::TYPE_STRING);
                            $sum_total+=$month_amount;
                            $month_amount=0;
                        }                                                        
                       $current_time+=(60*60*24);
                    }
                    $cur_col+=1;
                            $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit(number_format($sum_total), PHPExcel_Cell_DataType::TYPE_STRING);
                $cur_row+=1;    

              }
        
        }
        
        // Rename worksheet
        $objPHPExcel->getActiveSheet()->setTitle('Forcast');


        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);


        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Forcast.xlsx"');
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




    public function forcest_excel_client_mode()
    {
        $data_view['business_unit_id']="no";
        $start_time=0;
        $end_carlendar_unix=0;
        $bus_unit="";
        $company = array();
        $bu = array();
        $company_tmp=$this->m_company->get_all_company();
        $bu_tmp=$this->m_company->get_all_bu();
        foreach ($company_tmp as $key => $value) {
            $company[$value->id]=$value;
        }
        foreach ($bu_tmp as $key => $value) {
            $bu[$value->id]=$value;
        }
        
        $business_list = $this->m_business->get_all_business();
        if (isset($_POST['start_time'])&&isset($_POST['end_time'])) {
            $start_time=$this->m_time->datepicker_to_unix($_POST['start_time']);
            $end_carlendar_unix=$this->m_time->datepicker_to_unix($_POST['end_time']);
        }else{
            $start_time=mktime(0,0,1,1,1,date("Y"));
            $end_carlendar_unix=mktime(0,0,1,12,31,date("Y")); 
        }
        if (isset($_POST['business_unit_id'])) {
            $bus_unit=$_POST['business_unit_id'];
        }else{
            $bus_unit="all";
        }
        if (isset($_POST['mode'])) {
            $mode=(int)$_POST['mode'];
        }else{
            $mode=1;
        }
        $multi_usn = array("all" => "all");
        if (isset($_POST['multi_usn'])) {
            
        }
        $forcast_report=$this->m_forcast->get_forcast_report($start_time,$end_carlendar_unix,$bus_unit,$multi_usn,$mode);
        $pce_report=$this->m_forcast->get_forcast_pce_report($start_time,$end_carlendar_unix,$bus_unit,$multi_usn,$mode);
        $target_bill_report=$this->m_forcast->get_forcast_target_bill_report($start_time,$end_carlendar_unix,$bus_unit,$multi_usn,$mode);
        $actual_bill_report=$this->m_forcast->get_forcast_actual_bill_report($start_time,$end_carlendar_unix,$bus_unit,$multi_usn,$mode);
        if (isset($this->user_data->prem['cs'])&&(!isset($this->user_data->prem['admin'])&&!isset($this->user_data->prem['account'])&&!isset($this->user_data->prem['csd']))) {
            $all_user_under=$this->m_user->get_all_user_by_super_usn_all_lv($this->user_data->username,$this->user_data->username);
            $array_user=$this->m_user->change_node_user_to_array($all_user_under);
            $array_user[$this->user_data->username]=$this->user_data->username;
            $forcast_report=$this->m_forcast->get_forcast_report($start_time,$end_carlendar_unix,$bus_unit,$array_user,$mode);
            $pce_report=$this->m_forcast->get_forcast_pce_report($start_time,$end_carlendar_unix,$bus_unit,$array_user,$mode);
            $target_bill_report=$this->m_forcast->get_forcast_target_bill_report($start_time,$end_carlendar_unix,$bus_unit,$array_user,$mode);
            $actual_bill_report=$this->m_forcast->get_forcast_actual_bill_report($start_time,$end_carlendar_unix,$bus_unit,$array_user,$mode);
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
        $num=0;
        $sumval=0;
        $sumvalr=0;
        $current_time=$start_time;
        $cur_month=1000;
        $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(0, $cur_row)->setValueExplicit('Month->', PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('A')->setAutoSize(true);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('B')->setAutoSize(true);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('C')->setAutoSize(true);
        $cur_col+=2;
        while ($current_time<=$end_carlendar_unix) {
            if ($cur_month!=date("n",$current_time)) {
                $cur_month=date("n",$current_time);
                $cur_day=date("j",$current_time);
                $numday=date("t",$current_time);
                $cur_col+=1;
                $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit(date("F",$current_time), PHPExcel_Cell_DataType::TYPE_STRING);
                
            }                                                        
           $current_time+=(60*60*24*$numday);
        }
                $cur_col+=1;
                $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit("Total", PHPExcel_Cell_DataType::TYPE_STRING);

        
        $cur_row+=1;
        $cur_col=0;
        $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(0, $cur_row)->setValueExplicit('Forcast', PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex(0).$cur_row)->applyFromArray($styleblack);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex(1).$cur_row)->applyFromArray($styleblack);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex(2).$cur_row)->applyFromArray($styleblack);
        $cur_col+=2;
        $current_time=$start_time;
        $cur_month=1000;
        $month_amount=0;
        $sum_total=0;
        while ($current_time<=$end_carlendar_unix) {
            $cur_day=date("j",$current_time);
            $numday=date("t",$current_time);
            foreach ($forcast_report as $key2 => $value2) {                                                                    
                if (isset($value2[$current_time])) {                                       
                  foreach ($value2[$current_time] as $key3 => $value3) {                                                                
                      $month_amount+=$value3->project_value;
                  }
                }                                                          
            }
            if ($cur_month!=date("n",$current_time)&&$cur_day==$numday) {
                $cur_month=date("n",$current_time); 
                $cur_col+=1;
                $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit(number_format($month_amount), PHPExcel_Cell_DataType::TYPE_STRING);                                                                 
                $objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col).$cur_row)->applyFromArray($styleblack);
                $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($cur_col))->setAutoSize(true);
                $sum_total+=$month_amount;
                $month_amount=0;
            }                                                        
           $current_time+=(60*60*24);
        }
                $cur_col+=1;
                $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit(number_format($sum_total), PHPExcel_Cell_DataType::TYPE_STRING);                                                                 
                $objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col).$cur_row)->applyFromArray($styleblack);
                $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($cur_col))->setAutoSize(true);
        $cur_row+=1;

        foreach ($forcast_report as $key2 => $value2) {
        $cur_col=0;    
        if ($mode==3) {
            $str1=explode("_", $key2);
            $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit("      ".$company[$str1[0]]->name." ".$bu[$str1[1]]->bu_name, PHPExcel_Cell_DataType::TYPE_STRING);
        }else{
            $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit("      ".$company[$key2]->name, PHPExcel_Cell_DataType::TYPE_STRING);
        }
        $objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col).$cur_row)->applyFromArray($stylegray);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col+1).$cur_row)->applyFromArray($stylegray);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col+2).$cur_row)->applyFromArray($stylegray);
        $cur_col+=2;
          $current_time=$start_time;
              $cur_month=1000;
              $month_amount=0;
              $sum_total=0;
              while ($current_time<=$end_carlendar_unix) {
                  $cur_day=date("j",$current_time);
                  $numday=date("t",$current_time);         
                    foreach ($value2 as $key3 => $value3) {                                                  
                      foreach ($value3 as $key4 => $value4) {    
                        if ($current_time==$value4->project_end) {                                       
                            $month_amount+=$value4->project_value;
                        }
                      }
                    }
                  if ($cur_month!=date("n",$current_time)&&$cur_day==$numday) {
                      $cur_month=date("n",$current_time);                                                  
                      $cur_col+=1;
                      $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit(number_format($month_amount), PHPExcel_Cell_DataType::TYPE_STRING);
                      $objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col).$cur_row)->applyFromArray($stylegray);
                      $sum_total+=$month_amount;
                      $month_amount=0;
                  }                                                        
                 $current_time+=(60*60*24);
              }
                    $cur_col+=1;
                      $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit(number_format($sum_total), PHPExcel_Cell_DataType::TYPE_STRING);
                      $objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col).$cur_row)->applyFromArray($stylegray);
        $cur_row+=1;   
        }



        /////////////////////////////////////////////// PCE //////////////////////////////////////////

        $cur_col=0;
        $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(0, $cur_row)->setValueExplicit('PCE', PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex(0).$cur_row)->applyFromArray($styleblack);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex(1).$cur_row)->applyFromArray($styleblack);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex(2).$cur_row)->applyFromArray($styleblack);
        $cur_col+=2;
        $current_time=$start_time;
        $cur_month=1000;
        $month_amount=0;
        $sum_total=0;
        while ($current_time<=$end_carlendar_unix) {
            $cur_day=date("j",$current_time);
            $numday=date("t",$current_time);
            foreach ($pce_report as $key2 => $value2) {                                                            
                                                            if (isset($value2[$current_time])) {                                       
                                                              foreach ($value2[$current_time] as $key3 => $value3) {
                                                                foreach ($value3->pce as $key4 => $value4) {
                                                                  $month_amount+=$value4->pce_amount;
                                                                }
                                                              }
                                                            }                                                          
                                                        }
            if ($cur_month!=date("n",$current_time)&&$cur_day==$numday) {
                $cur_month=date("n",$current_time); 
                $cur_col+=1;
                $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit(number_format($month_amount), PHPExcel_Cell_DataType::TYPE_STRING);                                                                 
                $objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col).$cur_row)->applyFromArray($styleblack);
                $sum_total+=$month_amount;
                $month_amount=0;
            }                                                        
           $current_time+=(60*60*24);
        }
        $cur_col+=1;
                $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit(number_format($sum_total), PHPExcel_Cell_DataType::TYPE_STRING);                                                                 
                $objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col).$cur_row)->applyFromArray($styleblack);
        $cur_row+=1;

        foreach ($pce_report as $key2 => $value2) {
        $cur_col=0;    
        if ($mode==3) {
            $str1=explode("_", $key2);
            $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit("      ".$company[$str1[0]]->name." ".$bu[$str1[1]]->bu_name, PHPExcel_Cell_DataType::TYPE_STRING);
        }else{
            $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit("      ".$company[$key2]->name, PHPExcel_Cell_DataType::TYPE_STRING);
        }
        $objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col).$cur_row)->applyFromArray($stylegray);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col+1).$cur_row)->applyFromArray($stylegray);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col+2).$cur_row)->applyFromArray($stylegray);
        $cur_col+=2;
          $current_time=$start_time;
              $cur_month=1000;
              $month_amount=0;
              $sum_total=0;
              while ($current_time<=$end_carlendar_unix) {
                  $cur_day=date("j",$current_time);
                  $numday=date("t",$current_time);         
                    foreach ($value2 as $key3 => $value3) {
                                                                foreach ($value3 as $key4 => $value4) {
                                                                  if ($current_time==$value4->project_end) {
                                                                    foreach ($value4->pce as $key5 => $value5) {
                                                                      $month_amount+=$value5->pce_amount;
                                                                    }
                                                                  }
                                                                }
                                                              }
                  if ($cur_month!=date("n",$current_time)&&$cur_day==$numday) {
                      $cur_month=date("n",$current_time);                                                  
                      $cur_col+=1;
                      $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit(number_format($month_amount), PHPExcel_Cell_DataType::TYPE_STRING);
                      $objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col).$cur_row)->applyFromArray($stylegray);
                      $sum_total+=$month_amount;
                      $month_amount=0;
                  }                                                        
                 $current_time+=(60*60*24);
              }
              $cur_col+=1;
                      $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit(number_format($sum_total), PHPExcel_Cell_DataType::TYPE_STRING);
                      $objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col).$cur_row)->applyFromArray($stylegray);
        $cur_row+=1;   
        }





        /////////////////////////////////////////////// Target Billing (OC) //////////////////////////////////////////

        $cur_col=0;
        $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(0, $cur_row)->setValueExplicit('Target Billing (OC)', PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex(0).$cur_row)->applyFromArray($styleblack);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex(1).$cur_row)->applyFromArray($styleblack);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex(2).$cur_row)->applyFromArray($styleblack);
        $cur_col+=2;
        $current_time=$start_time;
        $cur_month=1000;
        $month_amount=0;
        $sum_total=0;
        while ($current_time<=$end_carlendar_unix) {
            $cur_day=date("j",$current_time);
            $numday=date("t",$current_time);
            foreach ($target_bill_report as $key2 => $value2) {       
                                                            foreach ($value2 as $c_key => $c_value) {                                                           
                                                              foreach ($c_value->oc as $key3 => $value3) {
                                                                if (isset($value3->oc_bill[$current_time])) {
                                                                  foreach ($value3->oc_bill[$current_time] as $key4 => $value4) {
                                                                    $month_amount+=$value4->amount;
                                                                  }
                                                                }
                                                              }
                                                            }                                                             
                                                        }
            if ($cur_month!=date("n",$current_time)&&$cur_day==$numday) {
                $cur_month=date("n",$current_time); 
                $cur_col+=1;
                $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit(number_format($month_amount), PHPExcel_Cell_DataType::TYPE_STRING);                                                                 
                $objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col).$cur_row)->applyFromArray($styleblack);
                $sum_total+=$month_amount;
                $month_amount=0;
            }                                                        
           $current_time+=(60*60*24);
        }
        $cur_col+=1;
                $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit(number_format($sum_total), PHPExcel_Cell_DataType::TYPE_STRING);                                                                 
                $objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col).$cur_row)->applyFromArray($styleblack);
        $cur_row+=1;

        foreach ($target_bill_report as $key2 => $value2) {
        $cur_col=0;    
        if ($mode==3) {
            $str1=explode("_", $key2);
            $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit("      ".$company[$str1[0]]->name." ".$bu[$str1[1]]->bu_name, PHPExcel_Cell_DataType::TYPE_STRING);
        }else{
            $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit("      ".$company[$key2]->name, PHPExcel_Cell_DataType::TYPE_STRING);
        }
        $objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col).$cur_row)->applyFromArray($stylegray);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col+1).$cur_row)->applyFromArray($stylegray);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col+2).$cur_row)->applyFromArray($stylegray);
        $cur_col+=2;
          $current_time=$start_time;
              $cur_month=1000;
              $month_amount=0;
              $sum_total=0;
              while ($current_time<=$end_carlendar_unix) {
                  $cur_day=date("j",$current_time);
                  $numday=date("t",$current_time);         
                    foreach ($value2 as $c_key => $c_value) {            
                                                                    foreach ($c_value->oc as $key3 => $value3) {
                                                                      if (isset($value3->oc_bill[$current_time])) {
                                                                        foreach ($value3->oc_bill[$current_time] as $key4 => $value4) {
                                                                          $month_amount+=$value4->amount;
                                                                        }
                                                                      }                                                              
                                                                    }
                                                                  }
                  if ($cur_month!=date("n",$current_time)&&$cur_day==$numday) {
                      $cur_month=date("n",$current_time);                                                  
                      $cur_col+=1;
                      $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit(number_format($month_amount), PHPExcel_Cell_DataType::TYPE_STRING);
                      $objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col).$cur_row)->applyFromArray($stylegray);
                      $sum_total+=$month_amount;
                      $month_amount=0;
                  }                                                        
                 $current_time+=(60*60*24);
              }
              $cur_col+=1;
                      $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit(number_format($sum_total), PHPExcel_Cell_DataType::TYPE_STRING);
                      $objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col).$cur_row)->applyFromArray($stylegray);
        $cur_row+=1;   
        
        }



        /////////////////////////////////////////////// Actual Billing //////////////////////////////////////////

        $cur_col=0;
        $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(0, $cur_row)->setValueExplicit('Actual Billing', PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex(0).$cur_row)->applyFromArray($styleblack);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex(1).$cur_row)->applyFromArray($styleblack);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex(2).$cur_row)->applyFromArray($styleblack);
        $cur_col+=2;
        $current_time=$start_time;
        $cur_month=1000;
        $month_amount=0;
        $sum_total=0;
        while ($current_time<=$end_carlendar_unix) {
            $cur_day=date("j",$current_time);
            $numday=date("t",$current_time);
            foreach ($actual_bill_report as $key2 => $value2) {       
                                                            foreach ($value2 as $c_key => $c_value) {
                                                              foreach ($c_value->oc as $key3 => $value3) {
                                                                if (isset($value3->oc_bill[$current_time])) {
                                                                  foreach ($value3->oc_bill[$current_time] as $key4 => $value4) {
                                                                    $month_amount+=$value4->paid_amount;
                                                                  }
                                                                }
                                                              }
                                                            }                                                             
                                                        }
            if ($cur_month!=date("n",$current_time)&&$cur_day==$numday) {
                $cur_month=date("n",$current_time); 
                $cur_col+=1;
                $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit(number_format($month_amount), PHPExcel_Cell_DataType::TYPE_STRING);                                                                 
                $objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col).$cur_row)->applyFromArray($styleblack);
                $sum_total+=$month_amount;
                $month_amount=0;
            }                                                        
           $current_time+=(60*60*24);
        }
        $cur_col+=1;
                $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit(number_format($sum_total), PHPExcel_Cell_DataType::TYPE_STRING);                                                                 
                $objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col).$cur_row)->applyFromArray($styleblack);
        $cur_row+=1;

        foreach ($actual_bill_report as $key2 => $value2) {
        $cur_col=0;    
        if ($mode==3) {
            $str1=explode("_", $key2);
            $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit("      ".$company[$str1[0]]->name." ".$bu[$str1[1]]->bu_name, PHPExcel_Cell_DataType::TYPE_STRING);
        }else{
            $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit("      ".$company[$key2]->name, PHPExcel_Cell_DataType::TYPE_STRING);
        }
        $objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col).$cur_row)->applyFromArray($stylegray);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col+1).$cur_row)->applyFromArray($stylegray);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col+2).$cur_row)->applyFromArray($stylegray);
        $cur_col+=2;
          $current_time=$start_time;
              $cur_month=1000;
              $month_amount=0;
              $sum_total=0;
              while ($current_time<=$end_carlendar_unix) {
                  $cur_day=date("j",$current_time);
                  $numday=date("t",$current_time);         
                    foreach ($value2 as $c_key => $c_value) {            
                                                                    foreach ($c_value->oc as $key3 => $value3) {
                                                                      if (isset($value3->oc_bill[$current_time])) {
                                                                        foreach ($value3->oc_bill[$current_time] as $key4 => $value4) {
                                                                          $month_amount+=$value4->paid_amount;
                                                                        }
                                                                      }                                                              
                                                                    }
                                                                  }
                  if ($cur_month!=date("n",$current_time)&&$cur_day==$numday) {
                      $cur_month=date("n",$current_time);                                                  
                      $cur_col+=1;
                      $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit(number_format($month_amount), PHPExcel_Cell_DataType::TYPE_STRING);
                      $objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col).$cur_row)->applyFromArray($stylegray);
                      $sum_total+=$month_amount;
                      $month_amount=0;
                  }                                                        
                 $current_time+=(60*60*24);
              }
              $cur_col+=1;
                      $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit(number_format($sum_total), PHPExcel_Cell_DataType::TYPE_STRING);
                      $objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col).$cur_row)->applyFromArray($stylegray);
        $cur_row+=1;      
        
        }
        
        // Rename worksheet
        $objPHPExcel->getActiveSheet()->setTitle('Forcast');


        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);


        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Forcast.xlsx"');
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































    public function operatingGPReport_excel()
    {
        $data_view['business_unit_id']="no";
        $start_time=0;
        $end_carlendar_unix=0;
        $bus_unit="";
        $company = array();
        $bu = array();
        $company_tmp=$this->m_company->get_all_company();
        $bu_tmp=$this->m_company->get_all_bu();
        foreach ($company_tmp as $key => $value) {
            $company[$value->id]=$value;
        }
        foreach ($bu_tmp as $key => $value) {
            $bu[$value->id]=$value;
        }
        
        $business_list = $this->m_business->get_all_business();
        if (isset($_POST['start_time'])&&isset($_POST['end_time'])) {
            $start_time=$this->m_time->datepicker_to_unix($_POST['start_time']);
            $end_carlendar_unix=$this->m_time->datepicker_to_unix($_POST['end_time']);
        }else{
            $start_time=mktime(0,0,1,1,1,date("Y"));
            $end_carlendar_unix=mktime(0,0,1,12,31,date("Y")); 
        }
        if (isset($_POST['business_unit_id'])) {
            $bus_unit=$_POST['business_unit_id'];
        }else{
            $bus_unit="all";
        }
        $multi_usn = array("all" => "all");
        if (isset($_POST['multi_usn'])) {
            unset($multi_usn['all']);
            $multi_usn[$_POST['multi_usn']]=$_POST['multi_usn'];
        }
        $forcast_report=$this->m_forcast->get_forcast_report($start_time,$end_carlendar_unix,$bus_unit,$multi_usn);
        $pce_report=$this->m_forcast->get_forcast_pce_report($start_time,$end_carlendar_unix,$bus_unit,$multi_usn);
        $target_bill_report=$this->m_forcast->get_forcast_target_bill_report($start_time,$end_carlendar_unix,$bus_unit,$multi_usn);
        $actual_bill_report=$this->m_forcast->get_forcast_actual_bill_report($start_time,$end_carlendar_unix,$bus_unit,$multi_usn);

        $forcast_out_report=$this->m_account->get_forcast_outsource_report($start_time,$end_carlendar_unix,$bus_unit,$multi_usn);
        $outsource_report=$this->m_account->get_outsource_report($start_time,$end_carlendar_unix,$bus_unit,$multi_usn);
        $target_out_report=$this->m_account->get_outsource_target_bill_report($start_time,$end_carlendar_unix,$bus_unit,$multi_usn);
        $actual_out_report=$this->m_account->get_outsource_actual_bill_report($start_time,$end_carlendar_unix,$bus_unit,$multi_usn);


        $forcast_report_cash=$this->m_forcast->get_forcast_receive_report($start_time,$end_carlendar_unix,$bus_unit,$multi_usn);
        $pce_report_cash=$this->m_forcast->get_forcast_receive_pce_report($start_time,$end_carlendar_unix,$bus_unit,$multi_usn);
        $target_bill_report_cash=$this->m_forcast->get_forcast_receive_target_bill_report($start_time,$end_carlendar_unix,$bus_unit,$multi_usn);
        $actual_bill_report_cash=$this->m_forcast->get_forcast_receive_actual_bill_report($start_time,$end_carlendar_unix,$bus_unit,$multi_usn);

        $forcast_out_report_cash=$this->m_account->get_forcast_outsource_paid_report($start_time,$end_carlendar_unix,$bus_unit,$multi_usn);
        $outsource_report_cash=$this->m_account->get_outsource_paid_report($start_time,$end_carlendar_unix,$bus_unit,$multi_usn);
        $target_out_report_cash=$this->m_account->get_outsource_paid_target_bill_report($start_time,$end_carlendar_unix,$bus_unit,$multi_usn);
        $actual_out_report_cash=$this->m_account->get_outsource_paid_actual_bill_report($start_time,$end_carlendar_unix,$bus_unit,$multi_usn);


        if (isset($this->user_data->prem['cs'])&&(!isset($this->user_data->prem['admin'])&&!isset($this->user_data->prem['account'])&&!isset($this->user_data->prem['csd']))) {
            $all_user_under=$this->m_user->get_all_user_by_super_usn_all_lv($this->user_data->username,$this->user_data->username);
            $array_user=$this->m_user->change_node_user_to_array($all_user_under);
            $array_user[$this->user_data->username]=$this->user_data->username;
            $forcast_report=$this->m_forcast->get_forcast_report($start_time,$end_carlendar_unix,$bus_unit,$array_user);
            $pce_report=$this->m_forcast->get_forcast_pce_report($start_time,$end_carlendar_unix,$bus_unit,$array_user);
            $target_bill_report=$this->m_forcast->get_forcast_target_bill_report($start_time,$end_carlendar_unix,$bus_unit,$array_user);
            $actual_bill_report=$this->m_forcast->get_forcast_actual_bill_report($start_time,$end_carlendar_unix,$bus_unit,$array_user);

            $forcast_out_report=$this->m_account->get_forcast_outsource_report($start_time,$end_carlendar_unix,$bus_unit,$array_user);
            $outsource_report=$this->m_account->get_outsource_report($start_time,$end_carlendar_unix,$bus_unit,$array_user);
            $target_out_report=$this->m_account->get_outsource_target_bill_report($start_time,$end_carlendar_unix,$bus_unit,$array_user);
            $actual_out_report=$this->m_account->get_outsource_actual_bill_report($start_time,$end_carlendar_unix,$bus_unit,$array_user);


            $forcast_report_cash=$this->m_forcast->get_forcast_receive_report($start_time,$end_carlendar_unix,$bus_unit,$array_user);
            $pce_report_cash=$this->m_forcast->get_forcast_receive_pce_report($start_time,$end_carlendar_unix,$bus_unit,$array_user);
            $target_bill_report_cash=$this->m_forcast->get_forcast_receive_target_bill_report($start_time,$end_carlendar_unix,$bus_unit,$array_user);
            $actual_bill_report_cash=$this->m_forcast->get_forcast_receive_actual_bill_report($start_time,$end_carlendar_unix,$bus_unit,$array_user);

            $forcast_out_report_cash=$this->m_account->get_forcast_outsource_paid_report($start_time,$end_carlendar_unix,$bus_unit,$array_user);
            $outsource_report_cash=$this->m_account->get_outsource_paid_report($start_time,$end_carlendar_unix,$bus_unit,$array_user);
            $target_out_report_cash=$this->m_account->get_outsource_paid_target_bill_report($start_time,$end_carlendar_unix,$bus_unit,$array_user);
            $actual_out_report_cash=$this->m_account->get_outsource_paid_actual_bill_report($start_time,$end_carlendar_unix,$bus_unit,$array_user);
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
        $total_horizon_array = array();
        $cur_col=0;
        $cur_row=1;
        $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(0, $cur_row)->setValueExplicit('GROSS PROFIT FORECAST', PHPExcel_Cell_DataType::TYPE_STRING);   
        $cur_row=2;
        $num=0;
        $sumval=0;
        $sumvalr=0;
        $current_time=$start_time;
        $cur_month=1000;
        $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(0, $cur_row)->setValueExplicit('Month->', PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('A')->setAutoSize(true);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('B')->setAutoSize(true);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('C')->setAutoSize(true);
        $cur_col+=1;
        while ($current_time<=$end_carlendar_unix) {
            if ($cur_month!=date("n",$current_time)) {
                $cur_month=date("n",$current_time);
                $cur_day=date("j",$current_time);
                $numday=date("t",$current_time);
                $total_horizon_array[date("Yn",$current_time)]=0;
                $cur_col+=1;
                $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit(date("F",$current_time), PHPExcel_Cell_DataType::TYPE_STRING);
                
            }                                                        
           $current_time+=(60*60*24*$numday);
        }
                $cur_col+=1;
                $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit("Total", PHPExcel_Cell_DataType::TYPE_STRING);

        
        $cur_row+=1;
        $cur_col=0;
        $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(0, $cur_row)->setValueExplicit('Forcast', PHPExcel_Cell_DataType::TYPE_STRING);
        //$objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex(0).$cur_row)->applyFromArray($styleblack);
        $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(1, $cur_row)->setValueExplicit('Revenue', PHPExcel_Cell_DataType::TYPE_STRING);
        //$objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex(1).$cur_row)->applyFromArray($styleblack);
        
        $cur_col+=1;
        $current_time=$start_time;
        $cur_month=1000;
        $month_amount=0;
        $sum_total=0;
        while ($current_time<=$end_carlendar_unix) {
            $cur_day=date("j",$current_time);
            $numday=date("t",$current_time);
            foreach ($forcast_report as $key => $value) {          
              if (isset($value->forcast_list[$current_time])) {                                       
                foreach ($value->forcast_list[$current_time] as $key2 => $value2) {
                  $month_amount+=$value2->project_value;
                }
              }
            }
            if ($cur_month!=date("n",$current_time)&&$cur_day==$numday) {
                $cur_month=date("n",$current_time); 
                $cur_col+=1;
                $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit(number_format($month_amount), PHPExcel_Cell_DataType::TYPE_STRING);                                                                 
                //$objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col).$cur_row)->applyFromArray($styleblack);
                $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($cur_col))->setAutoSize(true);
                $sum_total+=$month_amount;
                $total_horizon_array[date("Yn",$current_time)]+=$month_amount;
                $month_amount=0;
            }                                                        
           $current_time+=(60*60*24);
        }
                $cur_col+=1;
                $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit(number_format($sum_total), PHPExcel_Cell_DataType::TYPE_STRING);                                                                 
                //$objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col).$cur_row)->applyFromArray($styleblack);
                $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($cur_col))->setAutoSize(true);
        $cur_row+=1;

        $cur_col=0;
        $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(0, $cur_row)->setValueExplicit('Forcast', PHPExcel_Cell_DataType::TYPE_STRING);
        //$objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex(0).$cur_row)->applyFromArray($styleblack);
        $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(1, $cur_row)->setValueExplicit('Outsource', PHPExcel_Cell_DataType::TYPE_STRING);
        //$objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex(1).$cur_row)->applyFromArray($styleblack);
        
        $cur_col+=1;
        $current_time=$start_time;
        $cur_month=1000;
        $month_amount=0;
        $sum_total=0;
        while ($current_time<=$end_carlendar_unix) {
            $cur_day=date("j",$current_time);
            $numday=date("t",$current_time);
            foreach ($forcast_out_report as $key => $value) {          
              if (isset($value->forcast_list[$current_time])) {                                       
                foreach ($value->forcast_list[$current_time] as $key2 => $value2) {
                  $month_amount+=$value2->outsource_value;
                }
              }
            }
            if ($cur_month!=date("n",$current_time)&&$cur_day==$numday) {
                $cur_month=date("n",$current_time);
                $month_amount*=-1; 
                $cur_col+=1;
                $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit(number_format($month_amount), PHPExcel_Cell_DataType::TYPE_STRING);                                                                 
                //$objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col).$cur_row)->applyFromArray($styleblack);
                $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($cur_col))->setAutoSize(true);
                $sum_total+=$month_amount;
                $total_horizon_array[date("Yn",$current_time)]+=$month_amount;
                $month_amount=0;
            }                                                        
           $current_time+=(60*60*24);
        }
                $cur_col+=1;
                $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit(number_format($sum_total), PHPExcel_Cell_DataType::TYPE_STRING);                                                                 
                //$objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col).$cur_row)->applyFromArray($styleblack);
                $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($cur_col))->setAutoSize(true);
        $cur_row+=1;




        /////////////////////////////////////////////// PCE //////////////////////////////////////////

        $cur_col=0;
        $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(0, $cur_row)->setValueExplicit('PCE', PHPExcel_Cell_DataType::TYPE_STRING);
        //$objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex(0).$cur_row)->applyFromArray($styleblack);
        $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(1, $cur_row)->setValueExplicit('Revenue', PHPExcel_Cell_DataType::TYPE_STRING);
        //$objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex(1).$cur_row)->applyFromArray($styleblack);
        $cur_col+=1;
        $current_time=$start_time;
        $cur_month=1000;
        $month_amount=0;
        $sum_total=0;
        while ($current_time<=$end_carlendar_unix) {
            $cur_day=date("j",$current_time);
            $numday=date("t",$current_time);
            foreach ($pce_report as $key => $value) {          
              if (isset($value->forcast_list[$current_time])) {                                       
                foreach ($value->forcast_list[$current_time] as $key2 => $value2) {
                  foreach ($value2->pce as $key3 => $value3) {
                    $month_amount+=$value3->pce_amount;
                  }
                  
                }
              }
            }
            if ($cur_month!=date("n",$current_time)&&$cur_day==$numday) {
                $cur_month=date("n",$current_time); 
                $cur_col+=1;
                $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit(number_format($month_amount), PHPExcel_Cell_DataType::TYPE_STRING);                                                                 
                //$objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col).$cur_row)->applyFromArray($styleblack);
                $sum_total+=$month_amount;
                $total_horizon_array[date("Yn",$current_time)]+=$month_amount;
                $month_amount=0;
            }                                                        
           $current_time+=(60*60*24);
        }
        $cur_col+=1;
                $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit(number_format($sum_total), PHPExcel_Cell_DataType::TYPE_STRING);                                                                 
                //$objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col).$cur_row)->applyFromArray($styleblack);
        $cur_row+=1;

        $cur_col=0;
        $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(0, $cur_row)->setValueExplicit('PCE', PHPExcel_Cell_DataType::TYPE_STRING);
        //$objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex(0).$cur_row)->applyFromArray($styleblack);
        $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(1, $cur_row)->setValueExplicit('Outsource', PHPExcel_Cell_DataType::TYPE_STRING);
        //$objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex(1).$cur_row)->applyFromArray($styleblack);
        
        $cur_col+=1;
        $current_time=$start_time;
        $cur_month=1000;
        $month_amount=0;
        $sum_total=0;
        while ($current_time<=$end_carlendar_unix) {
            $cur_day=date("j",$current_time);
            $numday=date("t",$current_time);
            foreach ($outsource_report as $key => $value) {          
              if (isset($value->forcast_list[$current_time])) {                                       
                  foreach ($value->forcast_list[$current_time] as $key2 => $value2) {
                    foreach ($value2->outsource as $key3 => $value3) {
                      $month_amount+=$value3->qt_cost;
                    }
                    
                  }
                }
            }
            if ($cur_month!=date("n",$current_time)&&$cur_day==$numday) {
                $cur_month=date("n",$current_time); 
                $month_amount*=-1;
                $cur_col+=1;
                $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit(number_format($month_amount), PHPExcel_Cell_DataType::TYPE_STRING);                                                                 
                //$objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col).$cur_row)->applyFromArray($styleblack);
                $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($cur_col))->setAutoSize(true);
                $sum_total+=$month_amount;
                $total_horizon_array[date("Yn",$current_time)]+=$month_amount;
                $month_amount=0;
            }                                                        
           $current_time+=(60*60*24);
        }
                $cur_col+=1;
                $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit(number_format($sum_total), PHPExcel_Cell_DataType::TYPE_STRING);                                                                 
                //$objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col).$cur_row)->applyFromArray($styleblack);
                $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($cur_col))->setAutoSize(true);
        $cur_row+=1;




        /////////////////////////////////////////////// Target Billing (OC) //////////////////////////////////////////

        $cur_col=0;
        $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(0, $cur_row)->setValueExplicit('Target Billing (OC)', PHPExcel_Cell_DataType::TYPE_STRING);
        //$objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex(0).$cur_row)->applyFromArray($styleblack);
        $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(1, $cur_row)->setValueExplicit('Revenue', PHPExcel_Cell_DataType::TYPE_STRING);
        //$objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex(1).$cur_row)->applyFromArray($styleblack);
        $cur_col+=1;
        $current_time=$start_time;
        $cur_month=1000;
        $month_amount=0;
        $sum_total=0;
        while ($current_time<=$end_carlendar_unix) {
            $cur_day=date("j",$current_time);
            $numday=date("t",$current_time);
            foreach ($target_bill_report as $key => $value) {       
              foreach ($value->forcast_list as $key2 => $value2) {
                foreach ($value2->oc as $key3 => $value3) {
                  if (isset($value3->oc_bill[$current_time])) {
                    foreach ($value3->oc_bill[$current_time] as $key4 => $value4) {
                      $month_amount+=$value4->amount;
                    }
                  }
                }
              }   
            }
            if ($cur_month!=date("n",$current_time)&&$cur_day==$numday) {
                $cur_month=date("n",$current_time); 
                $cur_col+=1;
                $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit(number_format($month_amount), PHPExcel_Cell_DataType::TYPE_STRING);                                                                 
                //$objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col).$cur_row)->applyFromArray($styleblack);
                $sum_total+=$month_amount;
                $total_horizon_array[date("Yn",$current_time)]+=$month_amount;
                $month_amount=0;
            }                                                        
           $current_time+=(60*60*24);
        }
        $cur_col+=1;
                $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit(number_format($sum_total), PHPExcel_Cell_DataType::TYPE_STRING);                                                                 
                //$objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col).$cur_row)->applyFromArray($styleblack);
        $cur_row+=1;

        $cur_col=0;
        $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(0, $cur_row)->setValueExplicit('Target Billing (OC)', PHPExcel_Cell_DataType::TYPE_STRING);
        //$objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex(0).$cur_row)->applyFromArray($styleblack);
        $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(1, $cur_row)->setValueExplicit('Outsource', PHPExcel_Cell_DataType::TYPE_STRING);
        //$objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex(1).$cur_row)->applyFromArray($styleblack);
        
        $cur_col+=1;
        $current_time=$start_time;
        $cur_month=1000;
        $month_amount=0;
        $sum_total=0;
        while ($current_time<=$end_carlendar_unix) {
            $cur_day=date("j",$current_time);
            $numday=date("t",$current_time);
            foreach ($target_out_report as $key => $value) {          
              foreach ($value->forcast_list as $key2 => $value2) {
                foreach ($value2->outsource as $key3 => $value3) {
                  if (isset($value3->bill[$current_time])) {
                    foreach ($value3->bill[$current_time] as $key4 => $value4) {
                      $month_amount+=($value4->amount-$value4->paid_amount);
                    }
                  }
                }
              }
            }
            if ($cur_month!=date("n",$current_time)&&$cur_day==$numday) {
                $cur_month=date("n",$current_time); 
                $month_amount*=-1;     
                $cur_col+=1;
                $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit(number_format($month_amount), PHPExcel_Cell_DataType::TYPE_STRING);                                                                 
                //$objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col).$cur_row)->applyFromArray($styleblack);
                $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($cur_col))->setAutoSize(true);
                $sum_total+=$month_amount;
                $total_horizon_array[date("Yn",$current_time)]+=$month_amount;
                $month_amount=0;
            }                                                        
           $current_time+=(60*60*24);
        }
                $cur_col+=1;
                $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit(number_format($sum_total), PHPExcel_Cell_DataType::TYPE_STRING);                                                                 
                //$objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col).$cur_row)->applyFromArray($styleblack);
                $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($cur_col))->setAutoSize(true);
        $cur_row+=1;



        /////////////////////////////////////////////// Actual Billing //////////////////////////////////////////

        $cur_col=0;
        $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(0, $cur_row)->setValueExplicit('Actual Billing', PHPExcel_Cell_DataType::TYPE_STRING);
        //$objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex(0).$cur_row)->applyFromArray($styleblack);
        $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(1, $cur_row)->setValueExplicit('Revenue', PHPExcel_Cell_DataType::TYPE_STRING);
        //$objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex(1).$cur_row)->applyFromArray($styleblack);
        $cur_col+=1;
        $current_time=$start_time;
        $cur_month=1000;
        $month_amount=0;
        $sum_total=0;
        while ($current_time<=$end_carlendar_unix) {
            $cur_day=date("j",$current_time);
            $numday=date("t",$current_time);
            foreach ($actual_bill_report as $key => $value) {       
              foreach ($value->forcast_list as $key2 => $value2) {
                foreach ($value2->oc as $key3 => $value3) {
                  if (isset($value3->oc_bill[$current_time])) {
                    foreach ($value3->oc_bill[$current_time] as $key4 => $value4) {
                      $month_amount+=$value4->paid_amount;
                    }
                  }
                }
              }   
            }
            if ($cur_month!=date("n",$current_time)&&$cur_day==$numday) {
                $cur_month=date("n",$current_time); 
                $cur_col+=1;
                $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit(number_format($month_amount), PHPExcel_Cell_DataType::TYPE_STRING);                                                                 
                //$objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col).$cur_row)->applyFromArray($styleblack);
                $sum_total+=$month_amount;
                $total_horizon_array[date("Yn",$current_time)]+=$month_amount;
                $month_amount=0;
            }                                                        
           $current_time+=(60*60*24);
        }
        $cur_col+=1;
                $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit(number_format($sum_total), PHPExcel_Cell_DataType::TYPE_STRING);                                                                 
                //$objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col).$cur_row)->applyFromArray($styleblack);
        $cur_row+=1;

        $cur_col=0;
        $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(0, $cur_row)->setValueExplicit('Actual Billing', PHPExcel_Cell_DataType::TYPE_STRING);
        //$objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex(0).$cur_row)->applyFromArray($styleblack);
        $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(1, $cur_row)->setValueExplicit('Outsource', PHPExcel_Cell_DataType::TYPE_STRING);
        //$objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex(1).$cur_row)->applyFromArray($styleblack);
        
        $cur_col+=1;
        $current_time=$start_time;
        $cur_month=1000;
        $month_amount=0;
        $sum_total=0;
        while ($current_time<=$end_carlendar_unix) {
            $cur_day=date("j",$current_time);
            $numday=date("t",$current_time);
            foreach ($actual_out_report as $key => $value) {          
              foreach ($value->forcast_list as $key2 => $value2) {
                foreach ($value2->outsource as $key3 => $value3) {
                  if (isset($value3->bill[$current_time])) {
                    foreach ($value3->bill[$current_time] as $key4 => $value4) {
                      $month_amount+=$value4->amount;
                    }
                  }
                }
              } 
            }
            if ($cur_month!=date("n",$current_time)&&$cur_day==$numday) {
                $cur_month=date("n",$current_time); 
                $month_amount*=-1;
                $cur_col+=1;
                $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit(number_format($month_amount), PHPExcel_Cell_DataType::TYPE_STRING);                                                                 
                //$objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col).$cur_row)->applyFromArray($styleblack);
                $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($cur_col))->setAutoSize(true);
                $sum_total+=$month_amount;
                $total_horizon_array[date("Yn",$current_time)]+=$month_amount;
                $month_amount=0;
            }                                                        
           $current_time+=(60*60*24);
        }
                $cur_col+=1;
                $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit(number_format($sum_total), PHPExcel_Cell_DataType::TYPE_STRING);                                                                 
                //$objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col).$cur_row)->applyFromArray($styleblack);
                $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($cur_col))->setAutoSize(true);
        $cur_row+=1;
        
        $cur_col=0;
        $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(0, $cur_row)->setValueExplicit('', PHPExcel_Cell_DataType::TYPE_STRING);
        //$objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex(0).$cur_row)->applyFromArray($styleblack);
        $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(1, $cur_row)->setValueExplicit('', PHPExcel_Cell_DataType::TYPE_STRING);
        //$objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex(1).$cur_row)->applyFromArray($styleblack);
        
        $cur_col+=1;
        $current_time=$start_time;
        $cur_month=1000;
        $sum_total=0;
        while ($current_time<=$end_carlendar_unix) {
            $cur_day=date("j",$current_time);
            $numday=date("t",$current_time);
            if ($cur_month!=date("n",$current_time)&&$cur_day==$numday) {
                $cur_month=date("n",$current_time); 
                $cur_col+=1;
                $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit(number_format($total_horizon_array[date("Yn",$current_time)]), PHPExcel_Cell_DataType::TYPE_STRING);                                                                 
                //$objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col).$cur_row)->applyFromArray($styleblack);
                $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($cur_col))->setAutoSize(true);
                $sum_total+=$total_horizon_array[date("Yn",$current_time)];
            }                                                        
           $current_time+=(60*60*24);
        }
                $cur_col+=1;
                $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit(number_format($sum_total), PHPExcel_Cell_DataType::TYPE_STRING);                                                                 
                //$objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col).$cur_row)->applyFromArray($styleblack);
                $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($cur_col))->setAutoSize(true);
        $cur_row+=1;















        /////////////////////////////////////////////////////////////////////// section 2 ///////////////////////////////////////////////////


        $cur_col=0;
        $cur_row+=1;   
        $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(0, $cur_row)->setValueExplicit('OPERATING CASH FLOW FORECAST', PHPExcel_Cell_DataType::TYPE_STRING);   
        $cur_row+=1;   
        $num=0;
        $sumval=0;
        $sumvalr=0;
        $current_time=$start_time;
        $cur_month=1000;
        $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(0, $cur_row)->setValueExplicit('Month->', PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('A')->setAutoSize(true);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('B')->setAutoSize(true);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('C')->setAutoSize(true);
        $cur_col+=1;
        while ($current_time<=$end_carlendar_unix) {
            if ($cur_month!=date("n",$current_time)) {
                $cur_month=date("n",$current_time);
                $cur_day=date("j",$current_time);
                $numday=date("t",$current_time);
                $cur_col+=1;
                $total_horizon_array[date("Yn",$current_time)]=0;
                $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit(date("F",$current_time), PHPExcel_Cell_DataType::TYPE_STRING);
                
            }                                                        
           $current_time+=(60*60*24*$numday);
        }
                $cur_col+=1;
                $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit("Total", PHPExcel_Cell_DataType::TYPE_STRING);

        
        $cur_row+=1;
        $cur_col=0;
        $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(0, $cur_row)->setValueExplicit('Forcast', PHPExcel_Cell_DataType::TYPE_STRING);
        //$objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex(0).$cur_row)->applyFromArray($styleblack);
        $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(1, $cur_row)->setValueExplicit('Revenue', PHPExcel_Cell_DataType::TYPE_STRING);
        //$objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex(1).$cur_row)->applyFromArray($styleblack);
        
        $cur_col+=1;
        $current_time=$start_time;
        $cur_month=1000;
        $month_amount=0;
        $sum_total=0;
        while ($current_time<=$end_carlendar_unix) {
            $cur_day=date("j",$current_time);
            $numday=date("t",$current_time);
            foreach ($forcast_report_cash as $key => $value) {          
              if (isset($value->forcast_list[$current_time])) {                                       
                foreach ($value->forcast_list[$current_time] as $key2 => $value2) {
                  $month_amount+=$value2->project_value;
                }
              }
            }
            if ($cur_month!=date("n",$current_time)&&$cur_day==$numday) {
                $cur_month=date("n",$current_time); 
                $cur_col+=1;
                $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit(number_format($month_amount), PHPExcel_Cell_DataType::TYPE_STRING);                                                                 
                //$objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col).$cur_row)->applyFromArray($styleblack);
                $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($cur_col))->setAutoSize(true);
                $sum_total+=$month_amount;
                $total_horizon_array[date("Yn",$current_time)]+=$month_amount;
                $month_amount=0;
            }                                                        
           $current_time+=(60*60*24);
        }
                $cur_col+=1;
                $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit(number_format($sum_total), PHPExcel_Cell_DataType::TYPE_STRING);                                                                 
                //$objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col).$cur_row)->applyFromArray($styleblack);
                $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($cur_col))->setAutoSize(true);
        $cur_row+=1;

        $cur_col=0;
        $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(0, $cur_row)->setValueExplicit('Forcast', PHPExcel_Cell_DataType::TYPE_STRING);
        //$objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex(0).$cur_row)->applyFromArray($styleblack);
        $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(1, $cur_row)->setValueExplicit('Outsource', PHPExcel_Cell_DataType::TYPE_STRING);
        //$objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex(1).$cur_row)->applyFromArray($styleblack);
        
        $cur_col+=1;
        $current_time=$start_time;
        $cur_month=1000;
        $month_amount=0;
        $sum_total=0;
        while ($current_time<=$end_carlendar_unix) {
            $cur_day=date("j",$current_time);
            $numday=date("t",$current_time);
            foreach ($forcast_out_report_cash as $key => $value) {          
              if (isset($value->forcast_list[$current_time])) {                                       
                foreach ($value->forcast_list[$current_time] as $key2 => $value2) {
                  $month_amount+=$value2->outsource_value;
                }
              }
            }
            if ($cur_month!=date("n",$current_time)&&$cur_day==$numday) {
                $cur_month=date("n",$current_time); 
                $month_amount*=-1;
                $cur_col+=1;
                $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit(number_format($month_amount), PHPExcel_Cell_DataType::TYPE_STRING);                                                                 
                //$objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col).$cur_row)->applyFromArray($styleblack);
                $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($cur_col))->setAutoSize(true);
                $sum_total+=$month_amount;
                $total_horizon_array[date("Yn",$current_time)]+=$month_amount;
                $month_amount=0;
            }                                                        
           $current_time+=(60*60*24);
        }
                $cur_col+=1;
                $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit(number_format($sum_total), PHPExcel_Cell_DataType::TYPE_STRING);                                                                 
                //$objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col).$cur_row)->applyFromArray($styleblack);
                $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($cur_col))->setAutoSize(true);
        $cur_row+=1;




        /////////////////////////////////////////////// PCE //////////////////////////////////////////

        $cur_col=0;
        $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(0, $cur_row)->setValueExplicit('PCE', PHPExcel_Cell_DataType::TYPE_STRING);
        //$objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex(0).$cur_row)->applyFromArray($styleblack);
        $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(1, $cur_row)->setValueExplicit('Revenue', PHPExcel_Cell_DataType::TYPE_STRING);
        //$objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex(1).$cur_row)->applyFromArray($styleblack);
        $cur_col+=1;
        $current_time=$start_time;
        $cur_month=1000;
        $month_amount=0;
        $sum_total=0;
        while ($current_time<=$end_carlendar_unix) {
            $cur_day=date("j",$current_time);
            $numday=date("t",$current_time);
            foreach ($pce_report_cash as $key => $value) {          
              if (isset($value->forcast_list[$current_time])) {                                       
                foreach ($value->forcast_list[$current_time] as $key2 => $value2) {
                  foreach ($value2->pce as $key3 => $value3) {
                    $month_amount+=$value3->pce_amount;
                  }
                  
                }
              }
            }
            if ($cur_month!=date("n",$current_time)&&$cur_day==$numday) {
                $cur_month=date("n",$current_time); 
                $cur_col+=1;
                $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit(number_format($month_amount), PHPExcel_Cell_DataType::TYPE_STRING);                                                                 
                //$objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col).$cur_row)->applyFromArray($styleblack);
                $sum_total+=$month_amount;
                $total_horizon_array[date("Yn",$current_time)]+=$month_amount;
                $month_amount=0;
            }                                                        
           $current_time+=(60*60*24);
        }
        $cur_col+=1;
                $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit(number_format($sum_total), PHPExcel_Cell_DataType::TYPE_STRING);                                                                 
                //$objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col).$cur_row)->applyFromArray($styleblack);
        $cur_row+=1;

        $cur_col=0;
        $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(0, $cur_row)->setValueExplicit('PCE', PHPExcel_Cell_DataType::TYPE_STRING);
        //$objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex(0).$cur_row)->applyFromArray($styleblack);
        $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(1, $cur_row)->setValueExplicit('Paid', PHPExcel_Cell_DataType::TYPE_STRING);
        //$objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex(1).$cur_row)->applyFromArray($styleblack);
        
        $cur_col+=1;
        $current_time=$start_time;
        $cur_month=1000;
        $month_amount=0;
        $sum_total=0;
        while ($current_time<=$end_carlendar_unix) {
            $cur_day=date("j",$current_time);
            $numday=date("t",$current_time);
            foreach ($outsource_report_cash as $key => $value) {          
              if (isset($value->forcast_list[$current_time])) {                                       
                  foreach ($value->forcast_list[$current_time] as $key2 => $value2) {
                    foreach ($value2->outsource as $key3 => $value3) {
                      $month_amount+=$value3->qt_cost;
                    }
                    
                  }
                }
            }
            if ($cur_month!=date("n",$current_time)&&$cur_day==$numday) {
                $cur_month=date("n",$current_time); 
                $month_amount*=-1;
                $cur_col+=1;
                $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit(number_format($month_amount), PHPExcel_Cell_DataType::TYPE_STRING);                                                                 
                //$objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col).$cur_row)->applyFromArray($styleblack);
                $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($cur_col))->setAutoSize(true);
                $sum_total+=$month_amount;
                $total_horizon_array[date("Yn",$current_time)]+=$month_amount;
                $month_amount=0;
            }                                                        
           $current_time+=(60*60*24);
        }
                $cur_col+=1;
                $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit(number_format($sum_total), PHPExcel_Cell_DataType::TYPE_STRING);                                                                 
                //$objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col).$cur_row)->applyFromArray($styleblack);
                $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($cur_col))->setAutoSize(true);
        $cur_row+=1;




        /////////////////////////////////////////////// Target Billing (OC) //////////////////////////////////////////

        $cur_col=0;
        $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(0, $cur_row)->setValueExplicit('Target Billing (OC)', PHPExcel_Cell_DataType::TYPE_STRING);
        //$objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex(0).$cur_row)->applyFromArray($styleblack);
        $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(1, $cur_row)->setValueExplicit('Revenue', PHPExcel_Cell_DataType::TYPE_STRING);
        //$objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex(1).$cur_row)->applyFromArray($styleblack);
        $cur_col+=1;
        $current_time=$start_time;
        $cur_month=1000;
        $month_amount=0;
        $sum_total=0;
        while ($current_time<=$end_carlendar_unix) {
            $cur_day=date("j",$current_time);
            $numday=date("t",$current_time);
            foreach ($target_bill_report_cash as $key => $value) {       
              foreach ($value->forcast_list as $key2 => $value2) {
                foreach ($value2->oc as $key3 => $value3) {
                  if (isset($value3->oc_bill[$current_time])) {
                    foreach ($value3->oc_bill[$current_time] as $key4 => $value4) {
                      $month_amount+=$value4->amount;
                    }
                  }
                }
              }   
            }
            if ($cur_month!=date("n",$current_time)&&$cur_day==$numday) {
                $cur_month=date("n",$current_time); 
                $cur_col+=1;
                $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit(number_format($month_amount), PHPExcel_Cell_DataType::TYPE_STRING);                                                                 
                //$objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col).$cur_row)->applyFromArray($styleblack);
                $sum_total+=$month_amount;
                $total_horizon_array[date("Yn",$current_time)]+=$month_amount;
                $month_amount=0;
            }                                                        
           $current_time+=(60*60*24);
        }
        $cur_col+=1;
                $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit(number_format($sum_total), PHPExcel_Cell_DataType::TYPE_STRING);                                                                 
                //$objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col).$cur_row)->applyFromArray($styleblack);
        $cur_row+=1;

        $cur_col=0;
        $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(0, $cur_row)->setValueExplicit('Target Billing (OC)', PHPExcel_Cell_DataType::TYPE_STRING);
        //$objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex(0).$cur_row)->applyFromArray($styleblack);
        $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(1, $cur_row)->setValueExplicit('Paid', PHPExcel_Cell_DataType::TYPE_STRING);
        //$objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex(1).$cur_row)->applyFromArray($styleblack);
        
        $cur_col+=1;
        $current_time=$start_time;
        $cur_month=1000;
        $month_amount=0;
        $sum_total=0;
        while ($current_time<=$end_carlendar_unix) {
            $cur_day=date("j",$current_time);
            $numday=date("t",$current_time);
            foreach ($target_out_report_cash as $key => $value) {          
              foreach ($value->forcast_list as $key2 => $value2) {
                foreach ($value2->outsource as $key3 => $value3) {
                  if (isset($value3->bill[$current_time])) {
                    foreach ($value3->bill[$current_time] as $key4 => $value4) {
                      $month_amount+=($value4->amount-$value4->paid_amount);
                    }
                  }
                }
              }
            }
            if ($cur_month!=date("n",$current_time)&&$cur_day==$numday) {
                $cur_month=date("n",$current_time); 
                $month_amount*=-1;     
                $cur_col+=1;
                $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit(number_format($month_amount), PHPExcel_Cell_DataType::TYPE_STRING);                                                                 
                //$objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col).$cur_row)->applyFromArray($styleblack);
                $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($cur_col))->setAutoSize(true);
                $sum_total+=$month_amount;
                $total_horizon_array[date("Yn",$current_time)]+=$month_amount;
                $month_amount=0;
            }                                                        
           $current_time+=(60*60*24);
        }
                $cur_col+=1;
                $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit(number_format($sum_total), PHPExcel_Cell_DataType::TYPE_STRING);                                                                 
                //$objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col).$cur_row)->applyFromArray($styleblack);
                $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($cur_col))->setAutoSize(true);
        $cur_row+=1;



        /////////////////////////////////////////////// Actual Billing //////////////////////////////////////////

        $cur_col=0;
        $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(0, $cur_row)->setValueExplicit('Actual Billing', PHPExcel_Cell_DataType::TYPE_STRING);
        //$objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex(0).$cur_row)->applyFromArray($styleblack);
        $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(1, $cur_row)->setValueExplicit('Revenue', PHPExcel_Cell_DataType::TYPE_STRING);
        //$objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex(1).$cur_row)->applyFromArray($styleblack);
        $cur_col+=1;
        $current_time=$start_time;
        $cur_month=1000;
        $month_amount=0;
        $sum_total=0;
        while ($current_time<=$end_carlendar_unix) {
            $cur_day=date("j",$current_time);
            $numday=date("t",$current_time);
            foreach ($actual_bill_report_cash as $key => $value) {       
              foreach ($value->forcast_list as $key2 => $value2) {
                foreach ($value2->oc as $key3 => $value3) {
                  if (isset($value3->oc_bill[$current_time])) {
                    foreach ($value3->oc_bill[$current_time] as $key4 => $value4) {
                      $month_amount+=$value4->paid_amount;
                    }
                  }
                }
              }   
            }
            if ($cur_month!=date("n",$current_time)&&$cur_day==$numday) {
                $cur_month=date("n",$current_time); 
                $cur_col+=1;
                $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit(number_format($month_amount), PHPExcel_Cell_DataType::TYPE_STRING);                                                                 
                //$objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col).$cur_row)->applyFromArray($styleblack);
                $sum_total+=$month_amount;
                $total_horizon_array[date("Yn",$current_time)]+=$month_amount;
                $month_amount=0;
            }                                                        
           $current_time+=(60*60*24);
        }
        $cur_col+=1;
                $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit(number_format($sum_total), PHPExcel_Cell_DataType::TYPE_STRING);                                                                 
                //$objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col).$cur_row)->applyFromArray($styleblack);
        $cur_row+=1;

        $cur_col=0;
        $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(0, $cur_row)->setValueExplicit('Actual Billing', PHPExcel_Cell_DataType::TYPE_STRING);
        //$objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex(0).$cur_row)->applyFromArray($styleblack);
        $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(1, $cur_row)->setValueExplicit('Paid', PHPExcel_Cell_DataType::TYPE_STRING);
        //$objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex(1).$cur_row)->applyFromArray($styleblack);
        
        $cur_col+=1;
        $current_time=$start_time;
        $cur_month=1000;
        $month_amount=0;
        $sum_total=0;
        while ($current_time<=$end_carlendar_unix) {
            $cur_day=date("j",$current_time);
            $numday=date("t",$current_time);
            foreach ($actual_out_report_cash as $key => $value) {          
              foreach ($value->forcast_list as $key2 => $value2) {
                foreach ($value2->outsource as $key3 => $value3) {
                  if (isset($value3->bill[$current_time])) {
                    foreach ($value3->bill[$current_time] as $key4 => $value4) {
                      $month_amount+=$value4->amount;
                    }
                  }
                }
              } 
            }
            if ($cur_month!=date("n",$current_time)&&$cur_day==$numday) {
                $cur_month=date("n",$current_time); 
                $month_amount*=-1;
                $cur_col+=1;
                $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit(number_format($month_amount), PHPExcel_Cell_DataType::TYPE_STRING);                                                                 
                //$objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col).$cur_row)->applyFromArray($styleblack);
                $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($cur_col))->setAutoSize(true);
                $sum_total+=$month_amount;
                $total_horizon_array[date("Yn",$current_time)]+=$month_amount;
                $month_amount=0;
            }                                                        
           $current_time+=(60*60*24);
        }
                $cur_col+=1;
                $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit(number_format($sum_total), PHPExcel_Cell_DataType::TYPE_STRING);                                                                 
                //$objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col).$cur_row)->applyFromArray($styleblack);
                $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($cur_col))->setAutoSize(true);
        $cur_row+=1;


        $cur_col=0;
        $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(0, $cur_row)->setValueExplicit('', PHPExcel_Cell_DataType::TYPE_STRING);
        //$objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex(0).$cur_row)->applyFromArray($styleblack);
        $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(1, $cur_row)->setValueExplicit('', PHPExcel_Cell_DataType::TYPE_STRING);
        //$objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex(1).$cur_row)->applyFromArray($styleblack);
        
        $cur_col+=1;
        $current_time=$start_time;
        $cur_month=1000;
        $sum_total=0;
        while ($current_time<=$end_carlendar_unix) {
            $cur_day=date("j",$current_time);
            $numday=date("t",$current_time);
            if ($cur_month!=date("n",$current_time)&&$cur_day==$numday) {
                $cur_month=date("n",$current_time); 
                $cur_col+=1;
                $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit(number_format($total_horizon_array[date("Yn",$current_time)]), PHPExcel_Cell_DataType::TYPE_STRING);                                                                 
                //$objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col).$cur_row)->applyFromArray($styleblack);
                $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($cur_col))->setAutoSize(true);
                $sum_total+=$total_horizon_array[date("Yn",$current_time)];
            }                                                        
           $current_time+=(60*60*24);
        }
                $cur_col+=1;
                $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit(number_format($sum_total), PHPExcel_Cell_DataType::TYPE_STRING);                                                                 
                //$objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col).$cur_row)->applyFromArray($styleblack);
                $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($cur_col))->setAutoSize(true);
        $cur_row+=1;








        
        // Rename worksheet
        $objPHPExcel->getActiveSheet()->setTitle('Operation GD report');


        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);


        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Operation_GD_report.xlsx"');
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










    public function operatingGPReport_excel_client_mode()
    {
        $data_view['business_unit_id']="no";
        $start_time=0;
        $end_carlendar_unix=0;
        $bus_unit="";
        $company = array();
        $bu = array();
        $company_tmp=$this->m_company->get_all_company();
        $bu_tmp=$this->m_company->get_all_bu();
        foreach ($company_tmp as $key => $value) {
            $company[$value->id]=$value;
        }
        foreach ($bu_tmp as $key => $value) {
            $bu[$value->id]=$value;
        }
        
        $business_list = $this->m_business->get_all_business();
        if (isset($_POST['start_time'])&&isset($_POST['end_time'])) {
            $start_time=$this->m_time->datepicker_to_unix($_POST['start_time']);
            $end_carlendar_unix=$this->m_time->datepicker_to_unix($_POST['end_time']);
        }else{
            $start_time=mktime(0,0,1,1,1,date("Y"));
            $end_carlendar_unix=mktime(0,0,1,12,31,date("Y")); 
        }
        if (isset($_POST['business_unit_id'])) {
            $bus_unit=$_POST['business_unit_id'];
        }else{
            $bus_unit="all";
        }
        if (isset($_POST['mode'])) {
            $mode=(int)$_POST['mode'];
        }else{
            $mode=1;
        }
        $multi_usn = array("all" => "all");
        if (isset($_POST['multi_usn'])) {
            unset($multi_usn['all']);
            $multi_usn[$_POST['multi_usn']]=$_POST['multi_usn'];
        }
        $forcast_report=$this->m_forcast->get_forcast_report($start_time,$end_carlendar_unix,$bus_unit,$multi_usn,$mode);
        $pce_report=$this->m_forcast->get_forcast_pce_report($start_time,$end_carlendar_unix,$bus_unit,$multi_usn,$mode);
        $target_bill_report=$this->m_forcast->get_forcast_target_bill_report($start_time,$end_carlendar_unix,$bus_unit,$multi_usn,$mode);
        $actual_bill_report=$this->m_forcast->get_forcast_actual_bill_report($start_time,$end_carlendar_unix,$bus_unit,$multi_usn,$mode);

        $forcast_out_report=$this->m_account->get_forcast_outsource_report($start_time,$end_carlendar_unix,$bus_unit,$multi_usn,$mode);
        $outsource_report=$this->m_account->get_outsource_report($start_time,$end_carlendar_unix,$bus_unit,$multi_usn,$mode);
        $target_out_report=$this->m_account->get_outsource_target_bill_report($start_time,$end_carlendar_unix,$bus_unit,$multi_usn,$mode);
        $actual_out_report=$this->m_account->get_outsource_actual_bill_report($start_time,$end_carlendar_unix,$bus_unit,$multi_usn,$mode);


        $forcast_report_cash=$this->m_forcast->get_forcast_receive_report($start_time,$end_carlendar_unix,$bus_unit,$multi_usn,$mode);
        $pce_report_cash=$this->m_forcast->get_forcast_receive_pce_report($start_time,$end_carlendar_unix,$bus_unit,$multi_usn,$mode);
        $target_bill_report_cash=$this->m_forcast->get_forcast_receive_target_bill_report($start_time,$end_carlendar_unix,$bus_unit,$multi_usn,$mode);
        $actual_bill_report_cash=$this->m_forcast->get_forcast_receive_actual_bill_report($start_time,$end_carlendar_unix,$bus_unit,$multi_usn,$mode);

        $forcast_out_report_cash=$this->m_account->get_forcast_outsource_paid_report($start_time,$end_carlendar_unix,$bus_unit,$multi_usn,$mode);
        $outsource_report_cash=$this->m_account->get_outsource_paid_report($start_time,$end_carlendar_unix,$bus_unit,$multi_usn,$mode);
        $target_out_report_cash=$this->m_account->get_outsource_paid_target_bill_report($start_time,$end_carlendar_unix,$bus_unit,$multi_usn,$mode);
        $actual_out_report_cash=$this->m_account->get_outsource_paid_actual_bill_report($start_time,$end_carlendar_unix,$bus_unit,$multi_usn,$mode);


        if (isset($this->user_data->prem['cs'])&&(!isset($this->user_data->prem['admin'])&&!isset($this->user_data->prem['account'])&&!isset($this->user_data->prem['csd']))) {
            $all_user_under=$this->m_user->get_all_user_by_super_usn_all_lv($this->user_data->username,$this->user_data->username);
            $array_user=$this->m_user->change_node_user_to_array($all_user_under);
            $array_user[$this->user_data->username]=$this->user_data->username;
            $forcast_report=$this->m_forcast->get_forcast_report($start_time,$end_carlendar_unix,$bus_unit,$array_user,$mode);
            $pce_report=$this->m_forcast->get_forcast_pce_report($start_time,$end_carlendar_unix,$bus_unit,$array_user,$mode);
            $target_bill_report=$this->m_forcast->get_forcast_target_bill_report($start_time,$end_carlendar_unix,$bus_unit,$array_user,$mode);
            $actual_bill_report=$this->m_forcast->get_forcast_actual_bill_report($start_time,$end_carlendar_unix,$bus_unit,$array_user,$mode);

            $forcast_out_report=$this->m_account->get_forcast_outsource_report($start_time,$end_carlendar_unix,$bus_unit,$array_user,$mode);
            $outsource_report=$this->m_account->get_outsource_report($start_time,$end_carlendar_unix,$bus_unit,$array_user,$mode);
            $target_out_report=$this->m_account->get_outsource_target_bill_report($start_time,$end_carlendar_unix,$bus_unit,$array_user,$mode);
            $actual_out_report=$this->m_account->get_outsource_actual_bill_report($start_time,$end_carlendar_unix,$bus_unit,$array_user,$mode);


            $forcast_report_cash=$this->m_forcast->get_forcast_receive_report($start_time,$end_carlendar_unix,$bus_unit,$array_user,$mode);
            $pce_report_cash=$this->m_forcast->get_forcast_receive_pce_report($start_time,$end_carlendar_unix,$bus_unit,$array_user,$mode);
            $target_bill_report_cash=$this->m_forcast->get_forcast_receive_target_bill_report($start_time,$end_carlendar_unix,$bus_unit,$array_user,$mode);
            $actual_bill_report_cash=$this->m_forcast->get_forcast_receive_actual_bill_report($start_time,$end_carlendar_unix,$bus_unit,$array_user,$mode);

            $forcast_out_report_cash=$this->m_account->get_forcast_outsource_paid_report($start_time,$end_carlendar_unix,$bus_unit,$array_user,$mode);
            $outsource_report_cash=$this->m_account->get_outsource_paid_report($start_time,$end_carlendar_unix,$bus_unit,$array_user,$mode);
            $target_out_report_cash=$this->m_account->get_outsource_paid_target_bill_report($start_time,$end_carlendar_unix,$bus_unit,$array_user,$mode);
            $actual_out_report_cash=$this->m_account->get_outsource_paid_actual_bill_report($start_time,$end_carlendar_unix,$bus_unit,$array_user,$mode);
        }
         $pce_report=$this->m_forcast->gd_equal_array($outsource_report,$pce_report);
            $target_bill_report=$this->m_forcast->gd_equal_array($target_out_report,$target_bill_report);
            $actual_bill_report=$this->m_forcast->gd_equal_array($actual_out_report,$actual_bill_report);

            $pce_report_cash=$this->m_forcast->gd_equal_array($outsource_report_cash,$pce_report_cash);
            $target_bill_report_cash=$this->m_forcast->gd_equal_array($target_out_report_cash,$target_bill_report_cash);
            $actual_bill_report_cash=$this->m_forcast->gd_equal_array($actual_out_report_cash,$actual_bill_report_cash);

            
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
        $total_horizon_array = array();
        $cur_col=0;
        $cur_row=1;
        $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(0, $cur_row)->setValueExplicit('GROSS PROFIT FORECAST', PHPExcel_Cell_DataType::TYPE_STRING);   
        $cur_row=2;
        $num=0;
        $sumval=0;
        $sumvalr=0;
        $current_time=$start_time;
        $cur_month=1000;
        $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(0, $cur_row)->setValueExplicit('Month->', PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(1, $cur_row)->setValueExplicit('Client', PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('A')->setAutoSize(true);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('B')->setAutoSize(true);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('C')->setAutoSize(true);
        $cur_col+=1;
        while ($current_time<=$end_carlendar_unix) {
            if ($cur_month!=date("n",$current_time)) {
                $cur_month=date("n",$current_time);
                $cur_day=date("j",$current_time);
                $numday=date("t",$current_time);
                $cur_col+=1;
                $total_horizon_array[date("Yn",$current_time)]=0;
                $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit(date("F",$current_time), PHPExcel_Cell_DataType::TYPE_STRING);
                
            }                                                        
           $current_time+=(60*60*24*$numday);
        }
                $cur_col+=1;
                $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit("Total", PHPExcel_Cell_DataType::TYPE_STRING);

        
        $cur_row+=1;
        $cur_col=0;
        $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(0, $cur_row)->setValueExplicit('Forcast', PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex(0).$cur_row)->applyFromArray($style_center);
        if (count($forcast_report)>1) {
            $objPHPExcel->setActiveSheetIndex(0)->mergeCells("A".($cur_row).":A".($cur_row+count($forcast_report)-1));
        }       
    foreach ($forcast_report as $key => $value) {
        $cur_col=1;   
        if ($mode==3) {
            $str1=explode("_", $key);
            $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit("      ".$company[$str1[0]]->name." ".$bu[$str1[1]]->bu_name, PHPExcel_Cell_DataType::TYPE_STRING);
        }else{
            $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit("      ".$company[$key]->name, PHPExcel_Cell_DataType::TYPE_STRING);
        }
        $current_time=$start_time;
        $cur_month=1000;
        $month_amount=0;
        $sum_total=0;
        while ($current_time<=$end_carlendar_unix) {
            $cur_day=date("j",$current_time);
            $numday=date("t",$current_time);
            if (isset($value[$current_time])) {                                       
                                                            foreach ($value[$current_time] as $key2 => $value2) {
                                                              $month_amount+=$value2->project_value;
                                                            }
                                                          }
                                                          if (isset($forcast_out_report[$key][$current_time])) {                                       
                                                            foreach ($forcast_out_report[$key][$current_time] as $key2 => $value2) {
                                                              $month_amount-=$value2->outsource_value;
                                                            }
                                                          }
            if ($cur_month!=date("n",$current_time)&&$cur_day==$numday) {
                $cur_month=date("n",$current_time); 
                $cur_col+=1;
                $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit(number_format($month_amount), PHPExcel_Cell_DataType::TYPE_STRING);                                                                 
                //$objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col).$cur_row)->applyFromArray($styleblack);
                $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($cur_col))->setAutoSize(true);
                $sum_total+=$month_amount;
                $total_horizon_array[date("Yn",$current_time)]+=$month_amount;
                $month_amount=0;
            }                                                        
           $current_time+=(60*60*24);
        }
                $cur_col+=1;
                $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit(number_format($sum_total), PHPExcel_Cell_DataType::TYPE_STRING);                                                                 
                //$objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col).$cur_row)->applyFromArray($styleblack);
                $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($cur_col))->setAutoSize(true);
        $cur_row+=1;
    }
        /////////////////////////////////////////////// PCE //////////////////////////////////////////

        $cur_col=0;
        $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(0, $cur_row)->setValueExplicit('PCE', PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex(0).$cur_row)->applyFromArray($style_center);
        if (count($pce_report)>1) {
           $objPHPExcel->setActiveSheetIndex(0)->mergeCells("A".($cur_row).":A".($cur_row+count($pce_report)-1));
       }
        foreach ($pce_report as $key => $value) { 
            $cur_col=1;   
            if ($mode==3) {
                $str1=explode("_", $key);
                $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit("      ".$company[$str1[0]]->name." ".$bu[$str1[1]]->bu_name, PHPExcel_Cell_DataType::TYPE_STRING);
            }else{
                $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit("      ".$company[$key]->name, PHPExcel_Cell_DataType::TYPE_STRING);
            }
            $current_time=$start_time;
            $cur_month=1000;
            $month_amount=0;
            $sum_total=0;
            while ($current_time<=$end_carlendar_unix) {
                $cur_day=date("j",$current_time);
                $numday=date("t",$current_time);
                if (isset($value[$current_time])) {                                       
                                                            foreach ($value[$current_time] as $key2 => $value2) {
                                                              foreach ($value2->pce as $key3 => $value3) {
                                                                $month_amount+=$value3->pce_amount;
                                                              }                                                              
                                                            }                                                          
                                                        }
                                                        if (isset($outsource_report[$key][$current_time])) {                                       
                                                            foreach ($outsource_report[$key][$current_time] as $key2 => $value2) {
                                                              foreach ($value2->outsource as $key3 => $value3) {
                                                                $month_amount-=$value3->qt_cost;
                                                              }      
                                                            }
                                                          }
                if ($cur_month!=date("n",$current_time)&&$cur_day==$numday) {
                    $cur_month=date("n",$current_time); 
                    $cur_col+=1;
                    $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit(number_format($month_amount), PHPExcel_Cell_DataType::TYPE_STRING);                                                                 
                    //$objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col).$cur_row)->applyFromArray($styleblack);
                    $sum_total+=$month_amount;
                    $total_horizon_array[date("Yn",$current_time)]+=$month_amount;
                    $month_amount=0;
                }                                                        
               $current_time+=(60*60*24);
            }
            $cur_col+=1;
                    $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit(number_format($sum_total), PHPExcel_Cell_DataType::TYPE_STRING);                                                                 
                    //$objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col).$cur_row)->applyFromArray($styleblack);
            $cur_row+=1;       
        }



        /////////////////////////////////////////////// Target Billing (OC) //////////////////////////////////////////

        $cur_col=0;
        $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(0, $cur_row)->setValueExplicit('Target Billing (OC)', PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex(0).$cur_row)->applyFromArray($style_center);
        if (count($target_bill_report)>1) {
           $objPHPExcel->setActiveSheetIndex(0)->mergeCells("A".($cur_row).":A".($cur_row+count($target_bill_report)-1));
       }
        foreach ($target_bill_report as $key => $value) {
            $cur_col=1;
            if ($mode==3) {
                $str1=explode("_", $key);
                $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit("      ".$company[$str1[0]]->name." ".$bu[$str1[1]]->bu_name, PHPExcel_Cell_DataType::TYPE_STRING);
            }else{
                $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit("      ".$company[$key]->name, PHPExcel_Cell_DataType::TYPE_STRING);
            }
            $current_time=$start_time;
            $cur_month=1000;
            $month_amount=0;
            $sum_total=0;
            while ($current_time<=$end_carlendar_unix) {
                $cur_day=date("j",$current_time);
                $numday=date("t",$current_time);
                foreach ($value as $key2 => $value2) {
                                                            foreach ($value2->oc as $key3 => $value3) {
                                                              if (isset($value3->oc_bill[$current_time])) {
                                                                foreach ($value3->oc_bill[$current_time] as $key4 => $value4) {
                                                                  $month_amount+=$value4->amount;
                                                                }
                                                              }
                                                            }
                                                          }
                                                          if (isset($target_out_report[$key])) {                                                               
                                                          foreach ($target_out_report[$key] as $key2 => $value2) {
                                                            foreach ($value2->outsource as $key3 => $value3) {
                                                              if (isset($value3->bill[$current_time])) {
                                                                foreach ($value3->bill[$current_time] as $key4 => $value4) {
                                                                  $month_amount-=($value4->amount-$value4->paid_amount);
                                                                }
                                                              }
                                                            }
                                                          }   
                                                        }   
                if ($cur_month!=date("n",$current_time)&&$cur_day==$numday) {
                    $cur_month=date("n",$current_time); 
                    $cur_col+=1;
                    $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit(number_format($month_amount), PHPExcel_Cell_DataType::TYPE_STRING);                                                                 
                    //$objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col).$cur_row)->applyFromArray($styleblack);
                    $sum_total+=$month_amount;
                    $total_horizon_array[date("Yn",$current_time)]+=$month_amount;
                    $month_amount=0;
                }                                                        
               $current_time+=(60*60*24);
            }
            $cur_col+=1;
                    $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit(number_format($sum_total), PHPExcel_Cell_DataType::TYPE_STRING);                                                                 
                    //$objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col).$cur_row)->applyFromArray($styleblack);
            $cur_row+=1;
        }
        
        /////////////////////////////////////////////// Actual Billing //////////////////////////////////////////

        $cur_col=0;
        $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(0, $cur_row)->setValueExplicit('Actual Billing', PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex(0).$cur_row)->applyFromArray($style_center);
        if (count($actual_bill_report)>1) {
           $objPHPExcel->setActiveSheetIndex(0)->mergeCells("A".($cur_row).":A".($cur_row+count($actual_bill_report)-1));
       }
        foreach ($actual_bill_report as $key => $value) {
            $cur_col=1;
            if ($mode==3) {
                $str1=explode("_", $key);
                $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit("      ".$company[$str1[0]]->name." ".$bu[$str1[1]]->bu_name, PHPExcel_Cell_DataType::TYPE_STRING);
            }else{
                $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit("      ".$company[$key]->name, PHPExcel_Cell_DataType::TYPE_STRING);
            }
            $current_time=$start_time;
            $cur_month=1000;
            $month_amount=0;
            $sum_total=0;
            while ($current_time<=$end_carlendar_unix) {
                $cur_day=date("j",$current_time);
                $numday=date("t",$current_time);
                foreach ($value as $key2 => $value2) {
                                                            foreach ($value2->oc as $key3 => $value3) {
                                                              if (isset($value3->oc_bill[$current_time])) {
                                                                foreach ($value3->oc_bill[$current_time] as $key4 => $value4) {
                                                                  $month_amount+=$value4->paid_amount;
                                                                }
                                                              }
                                                            }
                                                          }
                                                          if (isset($actual_out_report[$key])) {
                                                          foreach ($actual_out_report[$key] as $key2 => $value2) {
                                                            foreach ($value2->outsource as $key3 => $value3) {
                                                              if (isset($value3->bill[$current_time])) {
                                                                foreach ($value3->bill[$current_time] as $key4 => $value4) {
                                                                  $month_amount-=$value4->amount;
                                                                }
                                                              }
                                                            }
                                                          }   
                                                        }   
                if ($cur_month!=date("n",$current_time)&&$cur_day==$numday) {
                    $cur_month=date("n",$current_time); 
                    $cur_col+=1;
                    $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit(number_format($month_amount), PHPExcel_Cell_DataType::TYPE_STRING);                                                                 
                    //$objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col).$cur_row)->applyFromArray($styleblack);
                    $sum_total+=$month_amount;
                    $total_horizon_array[date("Yn",$current_time)]+=$month_amount;
                    $month_amount=0;
                }                                                        
               $current_time+=(60*60*24);
            }
            $cur_col+=1;
                    $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit(number_format($sum_total), PHPExcel_Cell_DataType::TYPE_STRING);                                                                 
                    //$objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col).$cur_row)->applyFromArray($styleblack);
            $cur_row+=1;
        }

               
        $cur_col=0;
        $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(0, $cur_row)->setValueExplicit('', PHPExcel_Cell_DataType::TYPE_STRING);
        //$objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex(0).$cur_row)->applyFromArray($styleblack);
        $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(1, $cur_row)->setValueExplicit('', PHPExcel_Cell_DataType::TYPE_STRING);
        //$objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex(1).$cur_row)->applyFromArray($styleblack);
        
        $cur_col+=1;
        $current_time=$start_time;
        $cur_month=1000;
        $sum_total=0;
        while ($current_time<=$end_carlendar_unix) {
            $cur_day=date("j",$current_time);
            $numday=date("t",$current_time);
            if ($cur_month!=date("n",$current_time)&&$cur_day==$numday) {
                $cur_month=date("n",$current_time); 
                $cur_col+=1;
                $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit(number_format($total_horizon_array[date("Yn",$current_time)]), PHPExcel_Cell_DataType::TYPE_STRING);                                                                 
                //$objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col).$cur_row)->applyFromArray($styleblack);
                $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($cur_col))->setAutoSize(true);
                $sum_total+=$total_horizon_array[date("Yn",$current_time)];
            }                                                        
           $current_time+=(60*60*24);
        }
                $cur_col+=1;
                $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit(number_format($sum_total), PHPExcel_Cell_DataType::TYPE_STRING);                                                                 
                //$objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col).$cur_row)->applyFromArray($styleblack);
                $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($cur_col))->setAutoSize(true);
        $cur_row+=1;















        /////////////////////////////////////////////////////////////////////// section 2 ///////////////////////////////////////////////////


        $cur_col=0;
        $cur_row+=1;   
        $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(0, $cur_row)->setValueExplicit('OPERATING CASH FLOW FORECAST', PHPExcel_Cell_DataType::TYPE_STRING);   
        $cur_row+=1;   
        $num=0;
        $sumval=0;
        $sumvalr=0;
        $current_time=$start_time;
        $cur_month=1000;
        $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(0, $cur_row)->setValueExplicit('Month->', PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('A')->setAutoSize(true);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('B')->setAutoSize(true);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('C')->setAutoSize(true);
        $cur_col+=1;
        while ($current_time<=$end_carlendar_unix) {
            if ($cur_month!=date("n",$current_time)) {
                $cur_month=date("n",$current_time);
                $cur_day=date("j",$current_time);
                $numday=date("t",$current_time);
                $cur_col+=1;
                $total_horizon_array[date("Yn",$current_time)]=0;
                $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit(date("F",$current_time), PHPExcel_Cell_DataType::TYPE_STRING);
                
            }                                                        
           $current_time+=(60*60*24*$numday);
        }
                $cur_col+=1;
                $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit("Total", PHPExcel_Cell_DataType::TYPE_STRING);

        
        $cur_row+=1;
        $cur_col=0;
        $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(0, $cur_row)->setValueExplicit('Forcast', PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex(0).$cur_row)->applyFromArray($style_center);
        if (count($forcast_report_cash)>1) {
           $objPHPExcel->setActiveSheetIndex(0)->mergeCells("A".($cur_row).":A".($cur_row+count($forcast_report_cash)-1));
       }
        foreach ($forcast_report_cash as $key => $value) {
            $cur_col=1;
            if ($mode==3) {
                $str1=explode("_", $key);
                $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit("      ".$company[$str1[0]]->name." ".$bu[$str1[1]]->bu_name, PHPExcel_Cell_DataType::TYPE_STRING);
            }else{
                $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit("      ".$company[$key]->name, PHPExcel_Cell_DataType::TYPE_STRING);
            }
            
            $current_time=$start_time;
            $cur_month=1000;
            $month_amount=0;
            $sum_total=0;
            while ($current_time<=$end_carlendar_unix) {
                $cur_day=date("j",$current_time);
                $numday=date("t",$current_time);
                if (isset($value[$current_time])) {                                       
                                                            foreach ($value[$current_time] as $key2 => $value2) {
                                                              $month_amount+=$value2->project_value;
                                                            }
                                                          }
                                                          if (isset($forcast_out_report_cash[$key][$current_time])) {                                       
                                                            foreach ($forcast_out_report_cash[$key][$current_time] as $key2 => $value2) {
                                                              $month_amount-=$value2->outsource_value;
                                                            }
                                                          }
                if ($cur_month!=date("n",$current_time)&&$cur_day==$numday) {
                    $cur_month=date("n",$current_time); 
                    $cur_col+=1;
                    $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit(number_format($month_amount), PHPExcel_Cell_DataType::TYPE_STRING);                                                                 
                    //$objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col).$cur_row)->applyFromArray($styleblack);
                    $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($cur_col))->setAutoSize(true);
                    $sum_total+=$month_amount;
                    $total_horizon_array[date("Yn",$current_time)]+=$month_amount;
                    $month_amount=0;
                }                                                        
               $current_time+=(60*60*24);
            }
                    $cur_col+=1;
                    $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit(number_format($sum_total), PHPExcel_Cell_DataType::TYPE_STRING);                                                                 
                    //$objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col).$cur_row)->applyFromArray($styleblack);
                    $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($cur_col))->setAutoSize(true);
            $cur_row+=1;
        }
       

        /////////////////////////////////////////////// PCE //////////////////////////////////////////

        $cur_col=0;
        $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(0, $cur_row)->setValueExplicit('PCE', PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex(0).$cur_row)->applyFromArray($style_center);
        if (count($pce_report_cash)>1) {
           $objPHPExcel->setActiveSheetIndex(0)->mergeCells("A".($cur_row).":A".($cur_row+count($pce_report_cash)-1));
       }
        foreach ($pce_report_cash as $key => $value) {
            $cur_col=1;
            if ($mode==3) {
                $str1=explode("_", $key);
                $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit("      ".$company[$str1[0]]->name." ".$bu[$str1[1]]->bu_name, PHPExcel_Cell_DataType::TYPE_STRING);
            }else{
                $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit("      ".$company[$key]->name, PHPExcel_Cell_DataType::TYPE_STRING);
            }
            $current_time=$start_time;
            $cur_month=1000;
            $month_amount=0;
            $sum_total=0;
            while ($current_time<=$end_carlendar_unix) {
                $cur_day=date("j",$current_time);
                $numday=date("t",$current_time);        
                  if (isset($value[$current_time])) {                                       
                                                            foreach ($value[$current_time] as $key2 => $value2) {
                                                              foreach ($value2->pce as $key3 => $value3) {
                                                                $month_amount+=$value3->pce_amount;
                                                              }                                                              
                                                            }                                                          
                                                          }
                                                          if (isset($outsource_report_cash[$key][$current_time])) {                                       
                                                            foreach ($outsource_report_cash[$key][$current_time] as $key2 => $value2) {
                                                              foreach ($value2->outsource as $key3 => $value3) {
                                                                $month_amount-=$value3->qt_cost;
                                                              }
                                                            }
                                                          }
                if ($cur_month!=date("n",$current_time)&&$cur_day==$numday) {
                    $cur_month=date("n",$current_time); 
                    $cur_col+=1;
                    $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit(number_format($month_amount), PHPExcel_Cell_DataType::TYPE_STRING);                                                                 
                    //$objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col).$cur_row)->applyFromArray($styleblack);
                    $sum_total+=$month_amount;
                    $total_horizon_array[date("Yn",$current_time)]+=$month_amount;
                    $month_amount=0;
                }                                                        
               $current_time+=(60*60*24);
            }
            $cur_col+=1;
                    $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit(number_format($sum_total), PHPExcel_Cell_DataType::TYPE_STRING);                                                                 
                    //$objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col).$cur_row)->applyFromArray($styleblack);
            $cur_row+=1;
        }

        
        /////////////////////////////////////////////// Target Billing (OC) //////////////////////////////////////////

        $cur_col=0;
        $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(0, $cur_row)->setValueExplicit('Target Billing (OC)', PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex(0).$cur_row)->applyFromArray($style_center);
        if (count($target_bill_report_cash)>1) {
           $objPHPExcel->setActiveSheetIndex(0)->mergeCells("A".($cur_row).":A".($cur_row+count($target_bill_report_cash)-1));
       }
        foreach ($target_bill_report_cash as $key => $value) {
            $cur_col=1;
            if ($mode==3) {
                $str1=explode("_", $key);
                $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit("      ".$company[$str1[0]]->name." ".$bu[$str1[1]]->bu_name, PHPExcel_Cell_DataType::TYPE_STRING);
            }else{
                $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit("      ".$company[$key]->name, PHPExcel_Cell_DataType::TYPE_STRING);
            }
            $current_time=$start_time;
            $cur_month=1000;
            $month_amount=0;
            $sum_total=0;
            while ($current_time<=$end_carlendar_unix) {
                $cur_day=date("j",$current_time);
                $numday=date("t",$current_time);
                foreach ($value as $key2 => $value2) {
                                                            foreach ($value2->oc as $key3 => $value3) {
                                                              if (isset($value3->oc_bill[$current_time])) {
                                                                foreach ($value3->oc_bill[$current_time] as $key4 => $value4) {
                                                                  $month_amount+=$value4->amount;
                                                                }
                                                              }
                                                            }
                                                          }   
                                                          if (isset($target_out_report_cash[$key])) {
                                                          foreach ($target_out_report_cash[$key] as $key2 => $value2) {
                                                            foreach ($value2->outsource as $key3 => $value3) {
                                                              if (isset($value3->bill[$current_time])) {
                                                                foreach ($value3->bill[$current_time] as $key4 => $value4) {
                                                                  $month_amount-=($value4->amount-$value4->paid_amount);
                                                                }
                                                              }
                                                            }
                                                          }   
                                                        }
                if ($cur_month!=date("n",$current_time)&&$cur_day==$numday) {
                    $cur_month=date("n",$current_time); 
                    $cur_col+=1;
                    $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit(number_format($month_amount), PHPExcel_Cell_DataType::TYPE_STRING);                                                                 
                    //$objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col).$cur_row)->applyFromArray($styleblack);
                    $sum_total+=$month_amount;
                    $total_horizon_array[date("Yn",$current_time)]+=$month_amount;
                    $month_amount=0;
                }                                                        
               $current_time+=(60*60*24);
            }
            $cur_col+=1;
                    $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit(number_format($sum_total), PHPExcel_Cell_DataType::TYPE_STRING);                                                                 
                    //$objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col).$cur_row)->applyFromArray($styleblack);
            $cur_row+=1;
        }

        
        /////////////////////////////////////////////// Actual Billing //////////////////////////////////////////

        $cur_col=0;
        $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(0, $cur_row)->setValueExplicit('Actual Billing', PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex(0).$cur_row)->applyFromArray($style_center);
        if (count($actual_bill_report_cash)>1) {
           $objPHPExcel->setActiveSheetIndex(0)->mergeCells("A".($cur_row).":A".($cur_row+count($actual_bill_report_cash)-1));
       }
        foreach ($actual_bill_report_cash as $key => $value) {
            $cur_col=1;
            if ($mode==3) {
                $str1=explode("_", $key);
                $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit("      ".$company[$str1[0]]->name." ".$bu[$str1[1]]->bu_name, PHPExcel_Cell_DataType::TYPE_STRING);
            }else{
                $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit("      ".$company[$key]->name, PHPExcel_Cell_DataType::TYPE_STRING);
            }
            $current_time=$start_time;
            $cur_month=1000;
            $month_amount=0;
            $sum_total=0;
            while ($current_time<=$end_carlendar_unix) {
                $cur_day=date("j",$current_time);
                $numday=date("t",$current_time);
                foreach ($value as $key2 => $value2) {
                                                            foreach ($value2->oc as $key3 => $value3) {
                                                              if (isset($value3->oc_bill[$current_time])) {
                                                                foreach ($value3->oc_bill[$current_time] as $key4 => $value4) {
                                                                  $month_amount+=$value4->paid_amount;
                                                                }
                                                              }
                                                            }
                                                          }  
                                                          if (isset($actual_out_report_cash[$key])) {
                                                          foreach ($actual_out_report_cash[$key] as $key2 => $value2) {
                                                            foreach ($value2->outsource as $key3 => $value3) {
                                                              if (isset($value3->bill[$current_time])) {
                                                                foreach ($value3->bill[$current_time] as $key4 => $value4) {
                                                                  $month_amount-=$value4->amount;
                                                                }
                                                              }
                                                            }
                                                          }   
                                                        } 
                if ($cur_month!=date("n",$current_time)&&$cur_day==$numday) {
                    $cur_month=date("n",$current_time); 
                    $cur_col+=1;
                    $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit(number_format($month_amount), PHPExcel_Cell_DataType::TYPE_STRING);                                                                 
                    //$objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col).$cur_row)->applyFromArray($styleblack);
                    $sum_total+=$month_amount;
                    $total_horizon_array[date("Yn",$current_time)]+=$month_amount;
                    $month_amount=0;
                }                                                        
               $current_time+=(60*60*24);
            }
            $cur_col+=1;
                    $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit(number_format($sum_total), PHPExcel_Cell_DataType::TYPE_STRING);                                                                 
                    //$objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col).$cur_row)->applyFromArray($styleblack);
            $cur_row+=1;       
        }


        $cur_col=0;
        $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(0, $cur_row)->setValueExplicit('', PHPExcel_Cell_DataType::TYPE_STRING);
        //$objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex(0).$cur_row)->applyFromArray($styleblack);
        $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(1, $cur_row)->setValueExplicit('', PHPExcel_Cell_DataType::TYPE_STRING);
        //$objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex(1).$cur_row)->applyFromArray($styleblack);
        
        $cur_col+=1;
        $current_time=$start_time;
        $cur_month=1000;
        $sum_total=0;
        while ($current_time<=$end_carlendar_unix) {
            $cur_day=date("j",$current_time);
            $numday=date("t",$current_time);
            if ($cur_month!=date("n",$current_time)&&$cur_day==$numday) {
                $cur_month=date("n",$current_time); 
                $cur_col+=1;
                $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit(number_format($total_horizon_array[date("Yn",$current_time)]), PHPExcel_Cell_DataType::TYPE_STRING);                                                                 
                //$objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col).$cur_row)->applyFromArray($styleblack);
                $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($cur_col))->setAutoSize(true);
                $sum_total+=$total_horizon_array[date("Yn",$current_time)];
            }                                                        
           $current_time+=(60*60*24);
        }
                $cur_col+=1;
                $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit(number_format($sum_total), PHPExcel_Cell_DataType::TYPE_STRING);                                                                 
                //$objPHPExcel->setActiveSheetIndex(0)->getStyle(PHPExcel_Cell::stringFromColumnIndex($cur_col).$cur_row)->applyFromArray($styleblack);
                $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($cur_col))->setAutoSize(true);
        $cur_row+=1;








        
        // Rename worksheet
        $objPHPExcel->getActiveSheet()->setTitle('Operation GD report');


        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);


        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Operation_GD_report.xlsx"');
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


















    public function add() {
        
        $data_head['user_data'] = $this->user_data;
        $data['company']=$this->m_company->get_all_company();
        $data['business_list'] = $this->m_business->get_all_business();
        $data['A'] = "0";
        //print_r($data);
        if (isset($_POST['project_name'])) {
            $project_id=$this->m_forcast->generate_id();
            $project_dat = array(
                'project_id' => $project_id,
                'project_client' => $_POST['project_client'],
                'project_bu' => $_POST['project_bu'],
                'project_start' => $this->m_time->datepicker_to_unix($_POST['project_start']),
                'project_end' => $this->m_time->datepicker_to_unix($_POST['project_end']),
                'project_name' => $_POST['project_name'],
                'business_unit_id' => $_POST['business_unit_id'],
                'project_value' => (int)$_POST['project_value'],
                'outsource_value' => (int)$_POST['outsource_value'],
                'project_cs' => $this->user_data->username,
                );
                $this->m_forcast->add_forcast($project_dat);

                redirect('forcast');
            
        } 
        else {
            $data_head['head_name'] = "Admin Panel";
            $data_head['link_head'] = site_url('admin');
            $this->load->view('v_header', $data_head);
            $this->load->view('forcast/v_add', $data);
            $this->load->view('v_footer');
        }
    }
    public function edit() {
        
        $data_head['user_data'] = $this->user_data;
        $data['company']=$this->m_company->get_all_company();
        $data['business_list'] = $this->m_business->get_all_business();
        $data['A'] = "0";
        //print_r($data);
        $id=$this->uri->segment(3,'');
        $data['dat']=$this->m_forcast->get_forcast_by_id($id);
        if (isset($_POST['project_id'])) {
            $project_id=$_POST['project_id'];
            $project_dat = array(
                'project_client' => $_POST['project_client'],
                'project_bu' => $_POST['project_bu'],
                'project_start' => $this->m_time->datepicker_to_unix($_POST['project_start']),
                'project_end' => $this->m_time->datepicker_to_unix($_POST['project_end']),
                'project_name' => $_POST['project_name'],
                'business_unit_id' => $_POST['business_unit_id'],
                'project_value' => (int)$_POST['project_value'],
                'outsource_value' => (int)$_POST['outsource_value'],
                );
            $this->m_forcast->update_forcast($project_dat,$project_id);

                redirect('forcast');
            
        } 
        else {
            $data_head['head_name'] = "Admin Panel";
            $data_head['link_head'] = site_url('admin');
            $this->load->view('v_header', $data_head);
            $this->load->view('forcast/v_edit', $data);
            $this->load->view('v_footer');
        }
    }
    public function forcast_to_project() {
        
        $data_head['user_data'] = $this->user_data;
        $data['company']=$this->m_company->get_all_company();
        $data['business_list'] = $this->m_business->get_all_business();
        $data['A'] = "0";
        //print_r($data);
        $id=$this->uri->segment(3,'');
        $forcast_dat=$this->m_forcast->get_forcast_by_id($id);
        $project_id=$this->m_project->generate_id();
        $project_dat = array(
            'project_id' => $project_id,
            'project_client' => $forcast_dat->project_client,
            'project_bu' => $forcast_dat->project_bu,
            'project_start' => $forcast_dat->project_start,
            'project_end' => $forcast_dat->project_end,
            'project_name' => $forcast_dat->project_name,
            'business_unit_id' => $forcast_dat->business_unit_id,
            'project_cs' => $forcast_dat->project_cs,
            );
        $this->m_project->add_project($project_dat);
        $this->m_company->update_company(array('is_use' => "y", ),$forcast_dat->project_client);
        $this->m_company->update_bu(array('is_use' => "y", ),$forcast_dat->project_bu);
        $this->m_forcast->delete_forcast($id);

        redirect('project/edit/'.$project_id);
            
        
    }


}