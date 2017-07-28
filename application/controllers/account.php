<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');


class Account extends CI_Controller
{
    public function __construct() {
        parent::__construct();
        $this->load->model('m_user');
        $this->load->model('m_time');
        $this->load->model('m_company');
        $this->load->model('m_project');
        $this->load->model('m_pce');      
        $this->load->model('m_outsource');   
        $this->load->model('m_account');      
        $this->load->model('m_business');
        $this->load->model('m_forcast');
        if ($this->session->userdata('username')) {
            $user_data = $this->m_user->get_user_by_login_name($this->session->userdata('username'));
            if (isset($user_data->username) && isset($user_data->prem['account'])) {
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
        $start_time=time()-(60*60*24*60);
        if (isset($_POST['start_time'])) {
            $start_time=$this->m_time->datepicker_to_unix($_POST['start_time']);
        }
        $end_time=(time()+(60*60*24*60));
        if (isset($_POST['end_time'])) {
            $end_time=$this->m_time->datepicker_to_unix($_POST['end_time']);
        }
        $data_foot['table'] = "yes";
        $data_head['user_data'] = $this->user_data;
        $data_view['project_list'] = $this->m_project->get_all_project_by_status("no_draf","all",$start_time,$end_time);
        $data_view['business_list'] = $this->m_business->get_all_business();
        $this->load->view('v_header', $data_head);
        $this->load->view('account/v_list',$data_view);
        $this->load->view('v_footer', $data_foot);
    }
    public function billing_status() {
        $data_foot['table'] = "yes";
        $data_head['user_data'] = $this->user_data;
        $data_view['bill_stat'] = $this->m_account->get_all_billing_status();
        $this->load->view('v_header', $data_head);
        $this->load->view('account/v_bill_stat',$data_view);
        $this->load->view('v_footer', $data_foot);
    }
    public function bill_report()
    {
        $list = $this->m_account->ger_report_bill_for_print($this->m_time->datepicker_to_unix($_POST['start_time']),$this->m_time->datepicker_to_unix($_POST['end_time']),$_POST['only_p'],$_POST['account_unit_id'],$_POST['business_unit_id']);
        /** Error reporting */
        error_reporting(E_ALL);
        ini_set('display_errors', TRUE);
        ini_set('display_startup_errors', TRUE);

        if (PHP_SAPI == 'cli')
            die('This example should only be run from a Web Browser');

        /** Include PHPExcel */
        require_once './PHPExcel/Classes/PHPExcel.php';


        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();

        // Set document properties
        $objPHPExcel->getProperties()->setCreator("Rcal Neumerlin Group")
                                     ->setLastModifiedBy("DekGym3Atom")
                                     ->setTitle("Office 2007 XLSX User report")
                                     ->setSubject("Office 2007 XLSX User report")
                                     ->setDescription("User report document for Office 2007 XLSX, generated using PHP classes.")
                                     ->setKeywords("office 2007 openxml php")
                                     ->setCategory("User report");


        // Add some data
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'วันที่เอกสาร')
                    ->setCellValue('B1', 'วันที่วางบิล')
                    ->setCellValue('C1', 'วันที่รับเงิน')
                    ->setCellValue('D1', 'เลขที่ OC')
                    ->setCellValue('E1', 'เลขที่ BL')
                    ->setCellValue('F1', 'ชื่อลูกค้า')
                    ->setCellValue('G1', 'Business Unit')
                    ->setCellValue('H1', 'Account Unit')
                    ->setCellValue('I1', 'ชื่องาน')
                    ->setCellValue('J1', 'จำนวนเงิน')
                    ->setCellValue('K1', 'จำนวนเงินที่รับ');
        $num=0;
        $sumval=0;
        $sumvalr=0;
        $num=1;
        foreach ($list as $key => $value) {
            $num=$num+1;
            $sumval+=$value->amount;
            $sumvalr+=$value->receive_amount;
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValueExplicit('A'.$num, $this->m_time->unix_to_datepicker_reverse($value->time),PHPExcel_Cell_DataType::TYPE_STRING)                    
                    ->setCellValueExplicit('B'.$num, $value->time_bill,PHPExcel_Cell_DataType::TYPE_STRING)                    
                    ->setCellValueExplicit('C'.$num, $value->time_check,PHPExcel_Cell_DataType::TYPE_STRING)                    
                    ->setCellValueExplicit('D'.$num, $value->oc_no,PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('E'.$num, $value->so,PHPExcel_Cell_DataType::TYPE_STRING)                    
                    ->setCellValueExplicit('F'.$num, $value->client,PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('G'.$num, $value->business,PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('H'.$num, $value->account_unit,PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('I'.$num, $value->project_name,PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('J'.$num, $value->amount,PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('K'.$num, $value->receive_amount,PHPExcel_Cell_DataType::TYPE_STRING);

            //$objPHPExcel->setActiveSheetIndex(0)->getStyle('A'.$num)->getNumberFormat()->setFormatCode('dd-mm-yyyy');
            //$objPHPExcel->setActiveSheetIndex(0)->getStyle('B'.$num)->getNumberFormat()->setFormatCode('dd-mm-yyyy');
            //$objPHPExcel->setActiveSheetIndex(0)->getStyle('C'.$num)->getNumberFormat()->setFormatCode('dd-mm-yyyy');
        }
        $num+=1;
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValueExplicit('A'.$num, "",PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('B'.$num, "",PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('C'.$num, "",PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('D'.$num, "",PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('E'.$num, "",PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('F'.$num, "",PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('G'.$num, "",PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('H'.$num, "",PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('I'.$num, "รวมทั้งสิ้น",PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('J'.$num, $sumval,PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('K'.$num, $sumvalr,PHPExcel_Cell_DataType::TYPE_STRING);

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);

        // Rename worksheet
        $objPHPExcel->getActiveSheet()->setTitle('Rcal_Bill_report');


        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);


        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Rcal_Bill_report.xlsx"');
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
    public function test_print(){
        $list = $this->m_account->ger_report_payment_for_print(0,time()+(60*60*24*1000));
    }
    public function payment_report()
    {
        $list = $this->m_account->ger_report_payment_for_print($this->m_time->datepicker_to_unix($_POST['start_time']),$this->m_time->datepicker_to_unix($_POST['end_time']),$_POST['only_p'],$_POST['account_unit_id'],$_POST['business_unit_id']);
        /** Error reporting */
        error_reporting(E_ALL);
        ini_set('display_errors', TRUE);
        ini_set('display_startup_errors', TRUE);

        if (PHP_SAPI == 'cli')
            die('This example should only be run from a Web Browser');

        /** Include PHPExcel */
        require_once './PHPExcel/Classes/PHPExcel.php';


        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();

        // Set document properties
        $objPHPExcel->getProperties()->setCreator("Rcal Neumerlin Group")
                                     ->setLastModifiedBy("DekGym3Atom")
                                     ->setTitle("Office 2007 XLSX User report")
                                     ->setSubject("Office 2007 XLSX User report")
                                     ->setDescription("User report document for Office 2007 XLSX, generated using PHP classes.")
                                     ->setKeywords("office 2007 openxml php")
                                     ->setCategory("User report");


        // Add some data
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'วันที่ Payment')
                    ->setCellValue('B1', 'วันที่บันทึก')
                    ->setCellValue('C1', 'วันที่จ่าย')
                    ->setCellValue('D1', 'เลขที่ Payment')
                    ->setCellValue('E1', 'เลขที่ OC')
                    ->setCellValue('F1', 'ชื่อลูกค้า')
                    ->setCellValue('G1', 'Business Unit')
                    ->setCellValue('H1', 'Account Unit')
                    ->setCellValue('I1', 'ชื่องาน')
                    ->setCellValue('J1', 'รายการ')
                    ->setCellValue('K1', 'จำนวนเงินทั้งหมด')
                    ->setCellValue('L1', 'จำนวนเงินที่จ่าย')
                    ->setCellValue('M1', 'ประเภทการจ่าย');
        $num=0;
        $sumval=0;
        $sumval_max=0;
        $num=1;
        foreach ($list as $key => $value) {
            $num=$num+1;
            $sumval+=$value->amount;
            $sumval_max+=$value->max_amount;
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValueExplicit('A'.$num, $this->m_time->unix_to_datepicker_reverse($value->time),PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('B'.$num, $value->save_date,PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('C'.$num, $value->time_paid,PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('D'.$num, $value->pv,PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('E'.$num, $value->oc_no,PHPExcel_Cell_DataType::TYPE_STRING)                    
                    ->setCellValueExplicit('F'.$num, $value->client,PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('G'.$num, $value->business,PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('H'.$num, $value->account_unit,PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('I'.$num, $value->project_name,PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('J'.$num, $value->description,PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('K'.$num, $value->max_amount,PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('L'.$num, $value->amount,PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('M'.$num, $value->paid_type,PHPExcel_Cell_DataType::TYPE_STRING);
            //$objPHPExcel->setActiveSheetIndex(0)->getStyle('A'.$num)->getNumberFormat()->setFormatCode('dd-mm-yyyy');
            //$objPHPExcel->setActiveSheetIndex(0)->getStyle('B'.$num)->getNumberFormat()->setFormatCode('dd-mm-yyyy');
        }
        $num+=1;
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValueExplicit('A'.$num, "",PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('B'.$num, "",PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('C'.$num, "",PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('D'.$num, "",PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('E'.$num, "",PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('F'.$num, "",PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('G'.$num, "",PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('H'.$num, "",PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('I'.$num, "",PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('J'.$num, "รวมทั้งสิ้น",PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('K'.$num, $sumval_max,PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('L'.$num, $sumval,PHPExcel_Cell_DataType::TYPE_STRING);

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);

        // Rename worksheet
        $objPHPExcel->getActiveSheet()->setTitle('Rcal_Payment_report');


        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);


        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Rcal_Payment_report.xlsx"');
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
    public function outsource_status() {
        $data_foot['table'] = "yes";
        $data_head['user_data'] = $this->user_data;
        $data_view['bill_stat'] = $this->m_account->get_all_outsource_status();
        $this->load->view('v_header', $data_head);
        $this->load->view('account/v_outsource_stat',$data_view);
        $this->load->view('v_footer', $data_foot);
    }
    public function poso(){
            $project_id=$this->uri->segment(3,'');
        if(isset($_POST['bill_id'])){
            header('Content-Type: application/json');
            $json = array();
            $json['flag']="OK";
            foreach ($_POST['bill_id'] as $key => $value) {
                $bill_obj=$this->m_oc->get_oc_bill_by_id($value);
                $data = array(
                    'po' => $_POST['po'][$key], 
                    'so' => $_POST['so'][$key],
                    'paid_amount' => (int)$_POST['paid_amount'][$key], 
                    'collected' => $_POST['colllect'][$key], 
                    'paid_date' => $this->m_time->datepicker_to_unix($_POST['paid_date'][$key]), 
                    'receive_check_colllect' => $_POST['receive_check_colllect'][$key], 
                    'receive_check_date' => $this->m_time->datepicker_to_unix($_POST['receive_check_date'][$key]), 
                    );
                $is_correct_paid=$bill_obj->amount>(int)$_POST['paid_amount'][$key]&&$data['collected']=="y";
                $is_notpass_date=$this->m_time->datepicker_to_unix($_POST['paid_date'][$key])>time()&&$data['collected']=="y";
                $is_notpass_date=false;// do this for temporaly ignore correction date
                if ($is_notpass_date||$is_correct_paid) {
                    $json['flag']="";
                    $data['collected']="n";
                    if ($is_notpass_date) {
                        $json['flag'].="จะ Confirm ได้ก็ต่อเมื่อเวลาได้ผ่านวันที่วางบิลแล้วเท่านั้น,";
                    }
                    if ($is_correct_paid) {
                        $json['flag'].="จำนวนเงินที่รับ ต้องมากกว่าหรือเท่ากับที่ กำหนดมา";
                    }
                    
                }
                $this->m_oc->update_oc_bill($data,$value);
            }
            
            $pro_dat = array('status_bill' => $this->m_account->check_stat_bill($project_id), );
            $this->m_project->update_project($pro_dat,$project_id);
            echo json_encode($json);
        }else{
            $data['project_id']=$project_id;
            $project_dat=$this->m_project->get_project_by_id($project_id);
            $company_bu=$this->m_company->get_bu_by_id($project_dat->project_bu);
            $data['oc']=$this->m_oc->get_all_oc_by_project_id($project_id);
            $data['credit_term']=$company_bu->credit_term;
            $this->load->view('v_header_popup');
            $this->load->view('account/v_poso',$data);
            $this->load->view('v_footer');
        }
    }
    public function cal_date_credit_term(){
        $p_date=$this->m_time->datepicker_to_unix($_POST['paid_date']);
        $credit_term=(int)$_POST['credit_term'];
        header('Content-Type: application/json');
            $json = array();
        $json['dat']=$this->m_time->unix_to_datepicker($p_date+(60*60*24*$credit_term));    
        //$json['dat']=$this->m_time->unix_to_datepicker($p_date+(60*60*24*30));    
        echo json_encode($json);
    }
    public function poqp(){
        $project_id=$this->uri->segment(3,'');
        if(isset($_POST['add'])){
            $outsource_bill=$this->m_outsource->get_outsource_bill_by_id($_POST['add']);
            ?>
            <tr>
              <td><input style="width:100px" class="pv" type="text" name="pv[<?=$outsource_bill->id?>][]" value=""></td>
              <td><input style="width:100px" class="amount" type="text" name="amount[<?=$outsource_bill->id?>][]" value="0"></td>
              <td><input style="width:100px" class="datepicker save_date" type="text" name="save_date[<?=$outsource_bill->id?>][]" value="<?=$this->m_time->unix_to_datepicker(time())?>"></td>
             <td><input style="width:100px" class="datepicker date" type="text" name="date[<?=$outsource_bill->id?>][]" value="<?=$this->m_time->unix_to_datepicker(time()+(60*60*24*30))?>"></td>
              <td><select style="width:100px" class="paid" name="paid[<?=$outsource_bill->id?>][]">
                  <option value="n">no</option>
                  <option value="y">yes</option>
                </select></td>
                <td><select style="width:100px" class="paid_type" name="paid_type[<?=$outsource_bill->id?>][]">
                  <option value="ไม่ระบุ">ไม่ระบุ</option>
                  <option value="มัดจำ">มัดจำ</option>
                  <option value="ค่าใช้จ่ายอื่น">ค่าใช้จ่ายอื่น</option>
                </select></td>
            </tr>
            <?

        }else if(isset($_POST['bill_id'])){
            header('Content-Type: application/json');
            $json = array();
            $json['flag']="OK";
            foreach ($_POST['bill_id'] as $key => $value) {
                $data = array(
                    'inv' => $_POST['inv'][$key], 
                    );
                $this->m_outsource->update_outsource_bill($data,$value);
                if (isset($_POST['pv'][$value])) {
                    $outsource_bill=$this->m_outsource->get_outsource_bill_by_id($value);
                    $sum_paid=0;
                    foreach ($_POST['pv'][$value] as $key2 => $value2) {
                        $sum_paid+=(int)$_POST['amount'][$value][$key2];
                    }
                    $json['sum_paid']=$sum_paid;
                    $json['outsource_bill_amount']=$outsource_bill->amount;
                    if ($sum_paid<=$outsource_bill->amount) {
                        $this->m_outsource->delete_outsource_bill_paid_by_outsource_bill_id($value);
                        foreach ($_POST['pv'][$value] as $key2 => $value2) {
                            if ($value2!="") {
                                $data = array(
                                'outsource_id' => $outsource_bill->outsource_id, 
                                'bill_id' => $value, 
                                'pv' => $value2, 
                                'amount' => $_POST['amount'][$value][$key2], 
                                'date' => $this->m_time->datepicker_to_unix($_POST['date'][$value][$key2]), 
                                'save_date' => $this->m_time->datepicker_to_unix($_POST['save_date'][$value][$key2]),
                                'paid' => $_POST['paid'][$value][$key2], 
                                'paid_type' => $_POST['paid_type'][$value][$key2], 
                                );
                                $this->m_outsource->add_outsource_bill_paid($data);
                            }
                            
                        }
                    }else{
                        $json['flag']="ต้องจ่ายเงิน Outsource น้อยกว่าหรือเท่ากับที่บิลกำหนดใว้";
                    }
                }
                
                $this->m_outsource->valid_bill_outsource($value);
                
            }
            echo json_encode($json);
        }else{
            $data['project_id']=$project_id;
            $data['pce']=$this->m_pce->get_all_pce_by_project_id($project_id);
            foreach ($data['pce'] as $key => $value) {
                foreach ($data['pce'][$key]->outsource as $okey => $ovalue) {
                    $data['pce'][$key]->outsource[$okey]->bill=$this->m_outsource->get_outsource_bill_by_out_id($data['pce'][$key]->outsource[$okey]->id);
                }
            }
            $this->load->view('v_header_popup');
            $this->load->view('account/v_poqp',$data);
            $this->load->view('v_footer');
        }
    }


    public function report_outsource()
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
        if (isset($this->user_data->prem['cs'])&&(!isset($this->user_data->prem['admin'])&&!isset($this->user_data->prem['account'])&&!isset($this->user_data->prem['csd']))) {
            $all_user_under=$this->m_user->get_all_user_by_super_usn_all_lv($this->user_data->username,$this->user_data->username);
            $array_user=$this->m_user->change_node_user_to_array($all_user_under);
            $array_user[$this->user_data->username]=$this->user_data->username;
            
            $data_view['forcast_report']=$this->m_account->get_forcast_outsource_report($data_view['start_time'],$data_view['end_carlendar_unix'],$data_view['bus_unit'],$array_user);
            $data_view['outsource_report']=$this->m_account->get_outsource_report($data_view['start_time'],$data_view['end_carlendar_unix'],$data_view['bus_unit'],$array_user);
            $data_view['target_bill_report']=$this->m_account->get_outsource_target_bill_report($data_view['start_time'],$data_view['end_carlendar_unix'],$data_view['bus_unit'],$array_user);
            $data_view['actual_bill_report']=$this->m_account->get_outsource_actual_bill_report($data_view['start_time'],$data_view['end_carlendar_unix'],$data_view['bus_unit'],$array_user);
        }else{

            $data_view['forcast_report']=$this->m_account->get_forcast_outsource_report($data_view['start_time'],$data_view['end_carlendar_unix'],$data_view['bus_unit']);
            $data_view['outsource_report']=$this->m_account->get_outsource_report($data_view['start_time'],$data_view['end_carlendar_unix'],$data_view['bus_unit']);
            $data_view['target_bill_report']=$this->m_account->get_outsource_target_bill_report($data_view['start_time'],$data_view['end_carlendar_unix'],$data_view['bus_unit']);
            $data_view['actual_bill_report']=$this->m_account->get_outsource_actual_bill_report($data_view['start_time'],$data_view['end_carlendar_unix'],$data_view['bus_unit']);
        }
        //print_r($data_view['target_bill_report']);
        $this->load->view('v_header', $data_head);
        $this->load->view('account/v_report_outsource',$data_view);
        $this->load->view('v_footer', $data_foot);
        
    }
    public function report_outsource_paid()
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
        if (isset($this->user_data->prem['cs'])&&(!isset($this->user_data->prem['admin'])&&!isset($this->user_data->prem['account'])&&!isset($this->user_data->prem['csd']))) {
            $all_user_under=$this->m_user->get_all_user_by_super_usn_all_lv($this->user_data->username,$this->user_data->username);
            $array_user=$this->m_user->change_node_user_to_array($all_user_under);
            $array_user[$this->user_data->username]=$this->user_data->username;
            $data_view['forcast_report']=$this->m_account->get_forcast_outsource_paid_report($data_view['start_time'],$data_view['end_carlendar_unix'],$data_view['bus_unit'],$array_user);
            $data_view['outsource_report']=$this->m_account->get_outsource_paid_report($data_view['start_time'],$data_view['end_carlendar_unix'],$data_view['bus_unit'],$array_user);
            $data_view['target_bill_report']=$this->m_account->get_outsource_paid_target_bill_report($data_view['start_time'],$data_view['end_carlendar_unix'],$data_view['bus_unit'],$array_user);
            $data_view['actual_bill_report']=$this->m_account->get_outsource_paid_actual_bill_report($data_view['start_time'],$data_view['end_carlendar_unix'],$data_view['bus_unit'],$array_user);
        }else{
            $data_view['forcast_report']=$this->m_account->get_forcast_outsource_paid_report($data_view['start_time'],$data_view['end_carlendar_unix'],$data_view['bus_unit']);
            $data_view['outsource_report']=$this->m_account->get_outsource_paid_report($data_view['start_time'],$data_view['end_carlendar_unix'],$data_view['bus_unit']);
            $data_view['target_bill_report']=$this->m_account->get_outsource_paid_target_bill_report($data_view['start_time'],$data_view['end_carlendar_unix'],$data_view['bus_unit']);
            $data_view['actual_bill_report']=$this->m_account->get_outsource_paid_actual_bill_report($data_view['start_time'],$data_view['end_carlendar_unix'],$data_view['bus_unit']);
        }
        //print_r($data_view['target_bill_report']);
        $this->load->view('v_header', $data_head);
        $this->load->view('account/v_report_outsource_paid',$data_view);
        $this->load->view('v_footer', $data_foot);
        
    }


    public function report_outsource_excel()
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
        $forcast_report=$this->m_account->get_forcast_outsource_report($start_time,$end_carlendar_unix,$bus_unit);
        $outsource_report=$this->m_account->get_outsource_report($start_time,$end_carlendar_unix,$bus_unit);
        $target_bill_report=$this->m_account->get_outsource_target_bill_report($start_time,$end_carlendar_unix,$bus_unit);
        $actual_bill_report=$this->m_account->get_outsource_actual_bill_report($start_time,$end_carlendar_unix,$bus_unit);
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
                  $month_amount+=$value2->outsource_value;
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
                        $month_amount+=$value2->outsource_value;
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
                              $month_amount+=$value3->outsource_value;
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
        $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(0, $cur_row)->setValueExplicit('Outsource (PCE)', PHPExcel_Cell_DataType::TYPE_STRING);
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

        foreach ($outsource_report as $key => $value) {
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
                           foreach ($value2->outsource as $key3 => $value3) {
                             $month_amount+=$value3->qt_cost;
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
            $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit('', PHPExcel_Cell_DataType::TYPE_STRING);
            
                $current_time=$start_time;
                    $cur_month=1000;
                    $month_amount=0;
                    $sum_total=0;
                    while ($current_time<=$end_carlendar_unix) {
                        $cur_day=date("j",$current_time);
                        $numday=date("t",$current_time);         
                         if ($current_time==$value3->project_end) {   
                              foreach ($value3->outsource as $key4 => $value4) {
                                $month_amount+=$value4->qt_cost;
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


        /////////////////////////////////////////////// Outsource (OC) //////////////////////////////////////////

        $cur_col=0;
        $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(0, $cur_row)->setValueExplicit('Outsource (OC)', PHPExcel_Cell_DataType::TYPE_STRING);
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
                      foreach ($value2->outsource as $key3 => $value3) {
                        if (isset($value3->bill[$current_time])) {
                          foreach ($value3->bill[$current_time] as $key4 => $value4) {
                            $month_amount+=($value4->amount-$value4->paid_amount);
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
            foreach ($value2->outsource as $key3 => $value3) {
                $oc_no_str.=$value3->qt_no.",";
            }
            $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit($oc_no_str, PHPExcel_Cell_DataType::TYPE_STRING);
            
                $current_time=$start_time;
                    $cur_month=1000;
                    $month_amount=0;
                    $sum_total=0;
                    while ($current_time<=$end_carlendar_unix) {
                        $cur_day=date("j",$current_time);
                        $numday=date("t",$current_time);         
                         foreach ($value2->outsource as $key3 => $value3) {
                           if (isset($value3->bill[$current_time])) {
                             foreach ($value3->bill[$current_time] as $key4 => $value4) {
                               $month_amount+=($value4->amount-$value4->paid_amount);
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



        /////////////////////////////////////////////// Outsource (Paid)  //////////////////////////////////////////

        $cur_col=0;
        $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(0, $cur_row)->setValueExplicit('Outsource (Paid)', PHPExcel_Cell_DataType::TYPE_STRING);
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
                      foreach ($value2->outsource as $key3 => $value3) {
                        if (isset($value3->bill[$current_time])) {
                          foreach ($value3->bill[$current_time] as $key4 => $value4) {
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
            foreach ($value2->outsource as $key3 => $value3) {
                $oc_no_str.=$value3->qt_no.",";
            }
            $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit($oc_no_str, PHPExcel_Cell_DataType::TYPE_STRING);
            
                $current_time=$start_time;
                    $cur_month=1000;
                    $month_amount=0;
                    $sum_total=0;
                    while ($current_time<=$end_carlendar_unix) {
                        $cur_day=date("j",$current_time);
                        $numday=date("t",$current_time);         
                         foreach ($value2->outsource as $key3 => $value3) {
                           if (isset($value3->bill[$current_time])) {
                             foreach ($value3->bill[$current_time] as $key4 => $value4) {
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
        
        // Rename worksheet
        $objPHPExcel->getActiveSheet()->setTitle('Outsource');


        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);


        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Outsource_report.xlsx"');
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
















    public function report_outsource_paid_excel()
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
        
        $forcast_report=$this->m_account->get_forcast_outsource_paid_report($start_time,$end_carlendar_unix,$bus_unit);
        $outsource_report=$this->m_account->get_outsource_paid_report($start_time,$end_carlendar_unix,$bus_unit);
        $target_bill_report=$this->m_account->get_outsource_paid_target_bill_report($start_time,$end_carlendar_unix,$bus_unit);
        $actual_bill_report=$this->m_account->get_outsource_paid_actual_bill_report($start_time,$end_carlendar_unix,$bus_unit);
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
                  $month_amount+=$value2->outsource_value;
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
                        $month_amount+=$value2->outsource_value;
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
                              $month_amount+=$value3->outsource_value;
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
        $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(0, $cur_row)->setValueExplicit('Outsource (PCE)', PHPExcel_Cell_DataType::TYPE_STRING);
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

        foreach ($outsource_report as $key => $value) {
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
                           foreach ($value2->outsource as $key3 => $value3) {
                             $month_amount+=$value3->qt_cost;
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
            $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit('', PHPExcel_Cell_DataType::TYPE_STRING);
            
                $current_time=$start_time;
                    $cur_month=1000;
                    $month_amount=0;
                    $sum_total=0;
                    while ($current_time<=$end_carlendar_unix) {
                        $cur_day=date("j",$current_time);
                        $numday=date("t",$current_time);         
                         if ($current_time==$value3->project_end) {   
                              foreach ($value3->outsource as $key4 => $value4) {
                                $month_amount+=$value4->qt_cost;
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


        /////////////////////////////////////////////// Outsource (OC) //////////////////////////////////////////

        $cur_col=0;
        $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(0, $cur_row)->setValueExplicit('Outsource (OC)', PHPExcel_Cell_DataType::TYPE_STRING);
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
                      foreach ($value2->outsource as $key3 => $value3) {
                        if (isset($value3->bill[$current_time])) {
                          foreach ($value3->bill[$current_time] as $key4 => $value4) {
                            $month_amount+=($value4->amount-$value4->paid_amount);
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
            foreach ($value2->outsource as $key3 => $value3) {
                $oc_no_str.=$value3->qt_no.",";
            }
            $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit($oc_no_str, PHPExcel_Cell_DataType::TYPE_STRING);
            
                $current_time=$start_time;
                    $cur_month=1000;
                    $month_amount=0;
                    $sum_total=0;
                    while ($current_time<=$end_carlendar_unix) {
                        $cur_day=date("j",$current_time);
                        $numday=date("t",$current_time);         
                         foreach ($value2->outsource as $key3 => $value3) {
                           if (isset($value3->bill[$current_time])) {
                             foreach ($value3->bill[$current_time] as $key4 => $value4) {
                               $month_amount+=($value4->amount-$value4->paid_amount);
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



        /////////////////////////////////////////////// Outsource (Paid)  //////////////////////////////////////////

        $cur_col=0;
        $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow(0, $cur_row)->setValueExplicit('Outsource (Paid)', PHPExcel_Cell_DataType::TYPE_STRING);
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
                      foreach ($value2->outsource as $key3 => $value3) {
                        if (isset($value3->bill[$current_time])) {
                          foreach ($value3->bill[$current_time] as $key4 => $value4) {
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
            foreach ($value2->outsource as $key3 => $value3) {
                $oc_no_str.=$value3->qt_no.",";
            }
            $objPHPExcel->setActiveSheetIndex(0)->getCellByColumnAndRow($cur_col, $cur_row)->setValueExplicit($oc_no_str, PHPExcel_Cell_DataType::TYPE_STRING);
            
                $current_time=$start_time;
                    $cur_month=1000;
                    $month_amount=0;
                    $sum_total=0;
                    while ($current_time<=$end_carlendar_unix) {
                        $cur_day=date("j",$current_time);
                        $numday=date("t",$current_time);         
                         foreach ($value2->outsource as $key3 => $value3) {
                           if (isset($value3->bill[$current_time])) {
                             foreach ($value3->bill[$current_time] as $key4 => $value4) {
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
        
        // Rename worksheet
        $objPHPExcel->getActiveSheet()->setTitle('Outsource');


        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);


        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Outsource_report_paid.xlsx"');
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











    public function report_forcast_receive()
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
        if (isset($this->user_data->prem['cs'])&&(!isset($this->user_data->prem['admin'])&&!isset($this->user_data->prem['account'])&&!isset($this->user_data->prem['csd']))) {
            $all_user_under=$this->m_user->get_all_user_by_super_usn_all_lv($this->user_data->username,$this->user_data->username);
            $array_user=$this->m_user->change_node_user_to_array($all_user_under);
            $array_user[$this->user_data->username]=$this->user_data->username;
            $data_view['forcast_report']=$this->m_forcast->get_forcast_receive_report($data_view['start_time'],$data_view['end_carlendar_unix'],$data_view['bus_unit'],$array_user);
            $data_view['pce_report']=$this->m_forcast->get_forcast_receive_pce_report($data_view['start_time'],$data_view['end_carlendar_unix'],$data_view['bus_unit'],$array_user);
            $data_view['target_bill_report']=$this->m_forcast->get_forcast_receive_target_bill_report($data_view['start_time'],$data_view['end_carlendar_unix'],$data_view['bus_unit'],$array_user);
            $data_view['actual_bill_report']=$this->m_forcast->get_forcast_receive_actual_bill_report($data_view['start_time'],$data_view['end_carlendar_unix'],$data_view['bus_unit'],$array_user);
        }else{
            $data_view['forcast_report']=$this->m_forcast->get_forcast_receive_report($data_view['start_time'],$data_view['end_carlendar_unix'],$data_view['bus_unit']);
            $data_view['pce_report']=$this->m_forcast->get_forcast_receive_pce_report($data_view['start_time'],$data_view['end_carlendar_unix'],$data_view['bus_unit']);
            $data_view['target_bill_report']=$this->m_forcast->get_forcast_receive_target_bill_report($data_view['start_time'],$data_view['end_carlendar_unix'],$data_view['bus_unit']);
            $data_view['actual_bill_report']=$this->m_forcast->get_forcast_receive_actual_bill_report($data_view['start_time'],$data_view['end_carlendar_unix'],$data_view['bus_unit']);
        }
        //print_r($data_view['target_bill_report']);
        $this->load->view('v_header', $data_head);
        $this->load->view('account/v_forcast_receive',$data_view);
        $this->load->view('v_footer', $data_foot);
        
    }
    public function forcest_receive_excel()
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
        $forcast_report=$this->m_forcast->get_forcast_receive_report($start_time,$end_carlendar_unix,$bus_unit);
        $pce_report=$this->m_forcast->get_forcast_receive_pce_report($start_time,$end_carlendar_unix,$bus_unit);
        $target_bill_report=$this->m_forcast->get_forcast_receive_target_bill_report($start_time,$end_carlendar_unix,$bus_unit);
        $actual_bill_report=$this->m_forcast->get_forcast_receive_actual_bill_report($start_time,$end_carlendar_unix,$bus_unit);
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
        header('Content-Disposition: attachment;filename="Forcast_receive.xlsx"');
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