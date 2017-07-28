<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class admin extends CI_Controller
{
    
    public function __construct() {
        parent::__construct();
        $this->load->model('m_user');
        $this->load->model('m_time');
        $this->load->model('m_group');
        $this->load->model('m_hour_rate');
        $this->load->model('m_company');
        $this->load->model('m_business');
        $this->load->model('m_department');
        $this->load->model('m_position');
        $this->load->model('m_pce');
        $this->load->model("m_traffic_control");
        $this->load->model("m_work_sheet");
        $this->load->model("m_Rsheet");
        $this->load->model("m_resource");
        if ($this->session->userdata('username')) {
            $user_data = $this->m_user->get_user_by_login_name($this->session->userdata('username'));
            if (isset($user_data->username) && (isset($user_data->prem['admin'])||isset($user_data->prem['hr'])||isset($user_data->prem['account']))) {
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
        $this->load->view('v_header_admin', $data_head);
        $this->load->view('v_footer', $data_foot);
    }
    public function prem_group_list() {
        $data_foot['table'] = "yes";
        $data_head['user_data'] = $this->user_data;
        $data_view['group_list'] = $this->m_group->get_all_group();
        $this->load->view('v_header_admin', $data_head);
        $this->load->view('admin/v_group_user_list', $data_view);
        $this->load->view('v_footer', $data_foot);
    }
    public function add_prem_group() {
        if (isset($_POST['g_name'])) {
            if ($_POST['g_name'] == "") {
                $data['err_msg'] = "กรุณากรอก ชื่อกลุ่ม";
                $data_head['user_data'] = $this->user_data;
                
                $this->load->view('v_header_admin', $data_head);
                $this->load->view('admin/v_group_user_add', $data);
                $this->load->view('v_footer');
            } 
            else {
                $g_id = $this->m_group->generate_id();
                $data = array('g_id' => $g_id, 'g_name' => $_POST['g_name'],);
                $this->m_group->add_group($data);
                foreach ($_POST['prem'] as $key => $value) {
                    $data2 = array('g_id' => $g_id, 'prem' => $value,);
                    $this->m_group->add_group_prem($data2);
                }
                foreach ($_POST['user'] as $key => $value) {
                    $data3 = array('g_prem_id' => $g_id,);
                    $this->m_user->update_user($data3, $value);
                }
                foreach ($_POST['init_user'] as $key => $value) {
                    $data3 = array('g_prem_id' => "no",);
                    $this->m_user->update_user($data3, $value);
                }
                
                redirect('admin/prem_group_list');
            }
        } 
        else {
            $data_foot['table'] = "yes";
            $data_head['user_data'] = $this->user_data;
            $this->load->view('v_header_admin', $data_head);
            $this->load->view('admin/v_group_user_add');
            $this->load->view('v_footer', $data_foot);
        }
    }
    public function edit_prem_group() {
        $g_id = $this->uri->segment(3, '');
        
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        if (isset($_POST['g_name'])) {
            if ($_POST['g_name'] == "") {
                $data['err_msg'] = "กรุณากรอก ชื่อกลุ่ม";
                $data_head['user_data'] = $this->user_data;
                
                $this->load->view('v_header_admin', $data_head);
                $this->load->view('admin/v_group_user_add', $data);
                $this->load->view('v_footer');
            } 
            else {
                $data = array('g_name' => $_POST['g_name'],);
                $this->m_group->update_group($data, $g_id);
                $this->m_group->delete_group_prem($g_id);
                foreach ($_POST['prem'] as $key => $value) {
                    $data2 = array('g_id' => $g_id, 'prem' => $value,);
                    $this->m_group->add_group_prem($data2);
                }
                foreach ($_POST['user'] as $key => $value) {
                    $data3 = array('g_prem_id' => $g_id,);
                    $this->m_user->update_user($data3, $value);
                }
                foreach ($_POST['init_user'] as $key => $value) {
                    $data3 = array('g_prem_id' => "no",);
                    $this->m_user->update_user($data3, $value);
                }
                
                redirect('admin/prem_group_list');
            }
        } 
        else if (isset($request->flag) && $request->flag == "get_init_selected_prem") {
            header('Content-Type: application/json');
            $json = array();
            $json['data'] = $this->m_group->get_prem_group($request->g_id);
            $json['flag'] = "OK";
            echo json_encode($json);
        } 
        else if (isset($request->flag) && $request->flag == "get_init_selected_user") {
            header('Content-Type: application/json');
            $json = array();
            $json['data'] = $this->m_user->get_all_user_by_group_id($request->g_id);
            $json['flag'] = "OK";
            echo json_encode($json);
        } 
        else if (isset($request->flag) && $request->flag == "get_init_user") {
            header('Content-Type: application/json');
            $json = array();
            $dat_tmp = $this->m_user->get_all_user("no");
            foreach ($dat_tmp as $key => $value) {
                $json['data'][$value->username] = $value;
            }
            $json['flag'] = "OK";
            echo json_encode($json);
        } 
        else {
            $data_foot['table'] = "yes";
            $data_head['user_data'] = $this->user_data;
            $data_view['edit'] = "yes";
            $data_view['group'] = $this->m_group->get_group_by_id($g_id);
            $this->load->view('v_header_admin', $data_head);
            $this->load->view('admin/v_group_user_add', $data_view);
            $this->load->view('v_footer', $data_foot);
        }
    }
    public function del_prem_group() {
        $id = $this->uri->segment(3, '');
        $this->m_group->delete_group($id);
        $this->m_group->delete_group_prem($id);
        redirect('admin/prem_group_list');
    }
    // ---------------------------user section ---------------------------------------
    
    public function user_list() {
        $data_foot['table'] = "yes";
        $data_head['user_data'] = $this->user_data;
        $data_view['user_list'] = $this->m_user->get_all_user("all",true);
        $this->load->view('v_header_admin', $data_head);
        $this->load->view('admin/v_user_list', $data_view);
        $this->load->view('v_footer', $data_foot);
    }
    public function user_structure() {
        $data_foot['table'] = "yes";
        $data_head['user_data'] = $this->user_data;
        $data_view['bu_struct'] = $this->m_user->contructBuTree();
        $this->load->view('v_header_admin', $data_head);
        $this->load->view('admin/v_user_structure', $data_view);
        $this->load->view('v_footer', $data_foot);
    }
    public function user_add() {
        
        $data_head['user_data'] = $this->user_data;
        $data['user_list'] = $this->m_user->get_all_user();
        $data['group_list'] = $this->m_group->get_all_group();
        $data['position_list'] = $this->m_position->get_all_position();
        $data['A'] = "0";
        //print_r($data);
        if (isset($_POST['username'])) {
            $isdup = $this->m_user->check_user_username($_POST['username']);
            if ($_POST['password'] != $_POST['confirm_password']) {
                
                $data['err_msg'] = "กรุณากรอกรหัสผ่านให้ตรงกัน";
                $data_head['head_name'] = "Admin Panel";
                $data_head['link_head'] = site_url('admin');
                
                $this->load->view('v_header_admin', $data_head);
                $this->load->view('admin/v_user_add', $data);
                $this->load->view('v_footer');
            } 
            else if ($_POST['username'] == "") {
                $data['err_msg'] = "กรุณากรอก username";
                $data_head['head_name'] = "Admin Panel";
                $data_head['link_head'] = site_url('admin');
                
                $this->load->view('v_header_admin', $data_head);
                $this->load->view('admin/v_user_add', $data);
                $this->load->view('v_footer');
            } 
            else if (!$isdup) {
                $data['err_msg'] = "username " . $_POST['username'] . " ถูกใช้ไปแล้ว";
                $data_head['head_name'] = "Admin Panel";
                $data_head['link_head'] = site_url('admin');
                
                $this->load->view('v_header_admin', $data_head);
                $this->load->view('admin/v_user_add', $data);
                $this->load->view('v_footer');
            } 
            else {
                $data = array(
                    'username' => $_POST['username'], 
                    'firstname' => $_POST['firstname'], 
                    'lastname' => $_POST['lastname'], 
                    'phone' => $_POST['phone'], 
                    'password' => $_POST['password'], 
                    'g_prem_id' => $_POST['g_prem_id'], 
                    'supervisor' => $_POST['supervisor'],
                    'nickname' => $_POST['nickname'],
                    'position' => $_POST['position'],
                    'weight' => $_POST['weight'],
                    'join_date' => $this->m_time->datepicker_to_unix($_POST['join_date']),
                    );
                if ($_POST['file_path'] != "") {
                    
                    //@unlink("./media/sign_photo/".$ch_user['sign_filename']);
                    $filename = $_POST['file_path'];
                    $ext = explode(".", $filename);
                    $new_ext = $ext[count($ext) - 1];
                    $new_filename = $_POST['username'] . "_sign." . $new_ext;
                    $file = './media/temp/' . $filename;
                    $newfile = './media/sign_photo/' . $new_filename;
                    
                    if (!copy($file, $newfile)) {
                        echo "failed to copy $file...\n" . $file . " to " . $newfile . "  and  ";
                        
                        @unlink("./media/temp/" . $filename);
                        $data['sign_filename'] = "no";
                    } 
                    else {
                        $data['sign_filename'] = $new_filename;
                        @unlink("./media/temp/" . $filename);
                    }
                }
                $this->m_user->add_user($data);
                foreach ($_POST['work'] as $key => $value) {
                    $hour_data = array('usn' => $_POST['username'], 'hour_rate_id' => $value,);
                    $this->m_hour_rate->add_hour_rate_has_usn($hour_data);
                }
                
                redirect('admin/user_list');
            }
        } 
        else {
            $data_head['head_name'] = "Admin Panel";
            $data_head['link_head'] = site_url('admin');
            $this->load->view('v_header_admin', $data_head);
            $this->load->view('admin/v_user_add', $data);
            $this->load->view('v_footer');
        }
    }
    public function user_edit() {
        $id = $this->uri->segment(3, '');
        $data_head['user_data'] = $this->user_data;
        
        $data['user'] = $this->m_user->get_user_by_login_name($id);
        $data['user_list'] = $this->m_user->get_all_user();
        $data['group_list'] = $this->m_group->get_all_group();
        $data['position_list'] = $this->m_position->get_all_position();
        $data['edit'] = "yes";
        if (isset($_POST['firstname'])) {
            if ($_POST['password'] != $_POST['confirm_password']) {
                
                $data['err_msg'] = "กรุณากรอกรหัสผ่านให้ตรงกัน";
                $data_head['head_name'] = "Admin Panel";
                $data_head['link_head'] = site_url('admin');
                
                $this->load->view('v_header_admin', $data_head);
                $this->load->view('admin/v_user_add', $data);
                $this->load->view('v_footer');
            } 
            else {
                $data_insert = array(
                    'firstname' => $_POST['firstname'], 
                    'lastname' => $_POST['lastname'], 
                    'phone' => $_POST['phone'], 
                    'password' => $_POST['password'], 
                    'g_prem_id' => $_POST['g_prem_id'], 
                    'supervisor' => $_POST['supervisor'],
                    'nickname' => $_POST['nickname'],
                    'position' => $_POST['position'],
                    'weight' => $_POST['weight'],
                    'join_date' => $this->m_time->datepicker_to_unix($_POST['join_date']),
                    );
                if ($_POST['file_path'] != "") {
                    echo "in file path    ";
                    unlink("./media/sign_photo/" . $data['user']->sign_filename);
                    $filename = $_POST['file_path'];
                    $ext = explode(".", $filename);
                    $new_ext = $ext[count($ext) - 1];
                    $new_filename = $data['user']->username . "_sign_".time()."." . $new_ext;
                    $file = './media/temp/' . $filename;
                    $newfile = './media/sign_photo/' . $new_filename;
                    
                    if (!copy($file, $newfile)) {
                        echo "failed to copy $file...\n" . $file . " to " . $newfile . "  and  ";
                        
                        unlink("./media/temp/" . $filename);
                        $data_insert['sign_filename'] = "no";
                    } 
                    else {
                        $data_insert['sign_filename'] = $new_filename;
                        unlink("./media/temp/" . $filename);
                    }
                }
                $this->m_user->update_user($data_insert, $id);                
                redirect('admin/user_list');
            }
        } 
        else {
            $data_head['head_name'] = "Admin Panel";
            $data_head['link_head'] = site_url('admin');
            $this->load->view('v_header_admin', $data_head);
            $this->load->view('admin/v_user_add', $data);
            $this->load->view('v_footer');
        }
    }
    public function delete_user() {
        $id = $this->uri->segment(3, '');
        $this->m_user->delete_user($id);
        $this->m_pce->sync_hod_all_pce();
        redirect('admin/user_list');
    }
    










    public function print_user_to_excel()
    {
        $user_list = $this->m_user->get_all_user();
        $position_list = $this->m_position->get_all_position();
        $position_use = array();
        foreach ($position_list as $key => $value) {
            $position_use[$value->id]=$value;
        }
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
                    ->setCellValue('A1', 'ชื่อ')
                    ->setCellValue('B1', 'ชื่อเล่น')
                    ->setCellValue('C1', 'Username')
                    ->setCellValue('D1', 'Password')
                    ->setCellValue('E1', 'เบอร์โทร')
                    ->setCellValue('F1', 'รหัสพนักงงาน')
                    ->setCellValue('G1', 'ตำแหน่ง');

        foreach ($user_list as $key => $value) {
            $pos_name="";
            if (isset($position_use[$value->position]->name)) {
                $pos_name=$position_use[$value->position]->name;
            }else{
                $pos_name="No Position";
            }
            $num=$key+2;
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValueExplicit('A'.$num, $value->firstname." ".$value->lastname,PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('B'.$num, $value->nickname,PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('C'.$num, $value->username,PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('D'.$num, $value->password,PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('E'.$num, $value->phone,PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('F'.$num, $value->user_no,PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('G'.$num, $pos_name,PHPExcel_Cell_DataType::TYPE_STRING);
        }
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);

        // Rename worksheet
        $objPHPExcel->getActiveSheet()->setTitle('Rcal_user_report');


        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);


        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Rcal_user_report.xlsx"');
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





    //------------------------------hour rate section ------------------------------------------
    
    public function hour_rate_add() {
        
        $data_head['user_data'] = $this->user_data;
        $data['A'] = "0";
        if (isset($_POST['name'])) {
            if ($_POST['name'] == "") {
                $data['err_msg'] = "กรุณากรอก name";
                $data_head['head_name'] = "Admin Panel";
                $data_head['link_head'] = site_url('admin');
                
                $this->load->view('v_header_admin', $data_head);
                $this->load->view('admin/v_hour_rate_add', $data);
                $this->load->view('v_footer');
            } 
            else if ($_POST['hour_rate'] == "") {
                $data['err_msg'] = "กรุณากรอก hour_rate";
                $data_head['head_name'] = "Admin Panel";
                $data_head['link_head'] = site_url('admin');
                
                $this->load->view('v_header_admin', $data_head);
                $this->load->view('admin/v_hour_rate_add', $data);
                $this->load->view('v_footer');
            } 
            else {
                $data = array(
                    'name' => $_POST['name'], 
                    'hour_rate' => $_POST['hour_rate'],
                    'is_special' => $_POST['is_special'],
                    'description' => $_POST['description'],
                    );
                $this->m_hour_rate->add_hour_rate($data);
                
                redirect('admin/hour_rate_list');
            }
        } 
        else {
            $data_head['head_name'] = "Admin Panel";
            $data_head['link_head'] = site_url('admin');
            $this->load->view('v_header_admin', $data_head);
            $this->load->view('admin/v_hour_rate_add', $data);
            $this->load->view('v_footer');
        }
    }
    
    public function hour_rate_edit() {
        $id = $this->uri->segment(3, '');
        $data_head['user_data'] = $this->user_data;
        $data['A'] = "0";
        $data['edit'] = "yes";
        $data['hour_rate'] = $this->m_hour_rate->get_hour_rate_by_id($id);
        if (isset($_POST['name'])) {
            if ($_POST['name'] == "") {
                $data['err_msg'] = "กรุณากรอก name";
                $data_head['head_name'] = "Admin Panel";
                $data_head['link_head'] = site_url('admin');
                
                $this->load->view('v_header_admin', $data_head);
                $this->load->view('admin/v_hour_rate_add', $data);
                $this->load->view('v_footer');
            } 
            else if ($_POST['hour_rate'] == "") {
                $data['err_msg'] = "กรุณากรอก hour_rate";
                $data_head['head_name'] = "Admin Panel";
                $data_head['link_head'] = site_url('admin');
                
                $this->load->view('v_header_admin', $data_head);
                $this->load->view('admin/v_hour_rate_add', $data);
                $this->load->view('v_footer');
            } 
            else {
                $data = array(
                    'name' => $_POST['name'], 
                    'hour_rate' => $_POST['hour_rate'],
                    'is_special' => $_POST['is_special'],
                    'description' => $_POST['description'],
                    );
                $this->m_hour_rate->update_hour_rate($data, $id);
                
                redirect('admin/hour_rate_list');
            }
        } 
        else {
            $data_head['head_name'] = "Admin Panel";
            $data_head['link_head'] = site_url('admin');
            $this->load->view('v_header_admin', $data_head);
            $this->load->view('admin/v_hour_rate_add', $data);
            $this->load->view('v_footer');
        }
    }
    
    public function hour_rate_list() {
        $data_foot['table'] = "yes";
        $data_head['user_data'] = $this->user_data;
        $data_view['hour_rate_list'] = $this->m_hour_rate->get_all_hour_rate();
        $this->load->view('v_header_admin', $data_head);
        $this->load->view('admin/v_hour_rate_list', $data_view);
        $this->load->view('v_footer', $data_foot);
    }
    public function pos_rate_chart() {
        $data_foot['table'] = "yes";
        $data_head['user_data'] = $this->user_data;
        $data_view['position_list'] = $this->m_position->get_all_position();
        $dat_tmp = $this->m_hour_rate->get_all_hour_rate();
        foreach ($dat_tmp as $key => $value) {
            $data_view['hour_rate_list'][$value->id]=$value;
        }
        foreach ($data_view['position_list'] as $key => $value) {
            $data_view['position_list'][$key]->service_list=$this->m_hour_rate->get_hour_rate_has_position($value->id);
        }
        $this->load->view('v_header_admin', $data_head);
        $this->load->view('admin/v_service_item_chart', $data_view);
        $this->load->view('v_footer', $data_foot);
    }
    public function rate_pos_chart() {
        $data_foot['table'] = "yes";
        $data_head['user_data'] = $this->user_data;
        $data_view['hour_rate_list'] = $this->m_hour_rate->get_all_hour_rate();
        $dat_tmp = $this->m_position->get_all_position();
        foreach ($dat_tmp as $key => $value) {
            $data_view['position_list'][$value->id]=$value;
        }
        foreach ($data_view['hour_rate_list'] as $key => $value) {
            $data_view['hour_rate_list'][$key]->pos=$this->m_hour_rate->get_hour_rate_has_position_by_hour($value->id);
        }
        $this->load->view('v_header_admin', $data_head);
        $this->load->view('admin/v_pos_service_chart', $data_view);
        $this->load->view('v_footer', $data_foot);
    }
    public function rate_pos_chart_table() {
        $data_foot['table'] = "yes";
        $data_head['user_data'] = $this->user_data;
        $data_view['position_list'] = $this->m_position->get_all_position();
        $dat_tmp = $this->m_hour_rate->get_all_hour_rate();
        foreach ($dat_tmp as $key => $value) {
            $data_view['hour_rate_list'][$value->id]=$value;
        }
        foreach ($data_view['position_list'] as $key => $value) {
            $data_view['position_list'][$key]->service_list=$this->m_hour_rate->get_hour_rate_has_position($value->id,true);
        }
        $this->load->view('v_header_admin', $data_head);
        $this->load->view('admin/v_service_position_table', $data_view);
        $this->load->view('v_footer', $data_foot);
    }
    public function correct_pos_and_service() {
        $list=$this->m_hour_rate->get_all_hour_rate_has_position();
        foreach ($list as $key => $value) {
            $pos=$this->m_position->get_position_by_id($value->position_id);
            if (!isset($pos->id)) {
                $this->m_hour_rate->delete_hour_rate_has_position($value->position_id);
            }
        }
    }
    public function delete_hour_rate() {
        $id = $this->uri->segment(3, '');
        $this->m_hour_rate->delete_hour_rate($id);
        redirect('admin/hour_rate_list');
    }
    public function hour_rate_ajax() {
        
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        if (isset($request->flag) && $request->flag == "get_init_hour_rate") {
            header('Content-Type: application/json');
            $json = array();
            $dat_tmp = $this->m_hour_rate->get_all_hour_rate();
            $json['data'] = new stdClass();
            foreach ($dat_tmp as $key => $value) {
                $json['data']->{"" + $value->id} = $value;
            }
            $json['flag'] = "OK";
            echo json_encode($json);
        }
        else if (isset($request->flag) && $request->flag == "get_init_selected_rate") {
            header('Content-Type: application/json');
            $json = array();
            $dat_tmp = $this->m_hour_rate->get_hour_rate_has_usn($request->username);
            $json['data'] = new stdClass();
            foreach ($dat_tmp as $key => $value) {
                $json['data']->{"" + $value->hour_rate_id} = $value;
            }
            $json['flag'] = "OK";
            echo json_encode($json);
        }else if (isset($request->flag) && $request->flag == "get_init_selected_rate_position") {
            header('Content-Type: application/json');
            $json = array();
            $dat_tmp = $this->m_hour_rate->get_hour_rate_has_position($request->position_id);
            $json['data'] = new stdClass();
            foreach ($dat_tmp as $key => $value) {
                $json['data']->{"" + $value->hour_rate_id} = $value;
            }
            $json['flag'] = "OK";
            echo json_encode($json);
        }  
        else {
            $json['flag'] = "invalid request";
        }
    }



///////////// company -- Client ////

 	public function company_add() {
        
        $co_id = $this->m_company->generate_id();
        $data_head['user_data'] = $this->user_data;
        $data['A'] = "0";
        if (isset($_POST['name'])) {
            if ($_POST['name'] == "") {
                $data['err_msg'] = "กรุณากรอก name";
                $data_head['head_name'] = "Admin Panel";
                $data_head['link_head'] = site_url('admin');
                
                $this->load->view('v_header_admin', $data_head);
                $this->load->view('admin/v_company_add', $data);
                $this->load->view('v_footer');
            } 
            else if ($_POST['bu'] == "") {
                $data['err_msg'] = "กรุณากรอก bu";
                $data_head['head_name'] = "Admin Panel";
                $data_head['link_head'] = site_url('admin');
                
                $this->load->view('v_header_admin', $data_head);
                $this->load->view('admin/v_company_add', $data);
                $this->load->view('v_footer');
            } 
            else if ($_POST['client_id'] == "") {
                $data['err_msg'] = "กรุณากรอก Client ID";
                $data_head['head_name'] = "Admin Panel";
                $data_head['link_head'] = site_url('admin');
                
                $this->load->view('v_header_admin', $data_head);
                $this->load->view('admin/v_company_add', $data);
                $this->load->view('v_footer');
            } 
            else {
                $data = array('id' => $co_id,'name' => $_POST['name'],'client_id' => $_POST['client_id'],);
                $this->m_company->add_company($data);
                foreach ($_POST['bu'] as $key => $value) {
                    $data = array(
                        'company_id' => $co_id,
                        'bu_name' => $value,
                        'credit_term' => $_POST['credit_term'][$key],
                        );
                    $this->m_company->add_bu($data);
                }
                
                redirect('admin/company_list');
            }
        } 
        else {
            $data_head['head_name'] = "Admin Panel";
            $data_head['link_head'] = site_url('admin');
            $data_foot['table'] = "yes";
            $this->load->view('v_header_admin', $data_head);
            $this->load->view('admin/v_company_add', $data);
            $this->load->view('v_footer',$data_foot);
        }
    }

	public function company_list() {
	        $data_foot['table'] = "yes";
	        $data_head['user_data'] = $this->user_data;
	        $data_view['company_list'] = $this->m_company->get_all_company();
	        $this->load->view('v_header_admin', $data_head);
	        $this->load->view('admin/v_company_list', $data_view);
	        $this->load->view('v_footer', $data_foot);
	    }

    public function company_edit() {
        $id = $this->uri->segment(3, '');
        $data_head['user_data'] = $this->user_data;
        $data['A'] = "0";
        $data['edit'] = "yes";
        $data['company'] = $this->m_company->get_company_by_id($id);
        if (isset($_POST['name'])) {
            if ($_POST['name'] == "") {
                $data['err_msg'] = "กรุณากรอก name";
                $data_head['head_name'] = "Admin Panel";
                $data_head['link_head'] = site_url('admin');
                
                $this->load->view('v_header_admin', $data_head);
                $this->load->view('admin/v_company_add', $data);
                $this->load->view('v_footer');
            } 
            else if ($_POST['client_id'] == "") {
                $data['err_msg'] = "กรุณากรอก Client ID";
                $data_head['head_name'] = "Admin Panel";
                $data_head['link_head'] = site_url('admin');
                
                $this->load->view('v_header_admin', $data_head);
                $this->load->view('admin/v_company_add', $data);
                $this->load->view('v_footer');
            } 
            else {
                $data = array('id' => $id,'name' => $_POST['name'],'client_id' => $_POST['client_id'],);
                $this->m_company->update_company($data, $id);
                //$this->m_company->delete_bu_by_company_id($id);
                if (isset($_POST['bu'])) {                   
                    foreach ($_POST['bu'] as $key => $value) {
                        $data = array(
                            'company_id' => $id,
                            'bu_name' => $value,
                            'credit_term' => $_POST['credit_term'][$key],
                            );
                        $this->m_company->add_bu($data);
                    }
                }
                if (isset($_POST['id_old'])) {                   
                    foreach ($_POST['id_old'] as $key => $value) {
                        $data = array(
                            'bu_name' => $_POST['bu_old'][$key],
                            'credit_term' => $_POST['credit_term_old'][$key],
                            );
                        $this->m_company->update_bu($data,$value);
                    }
                }
                if (isset($_POST['del_list'])) {                   
                    foreach ($_POST['del_list'] as $key => $value) {
                        $this->m_company->delete_bu($value);
                    }
                }
                
                
                redirect('admin/company_list');
            }
        } 
        else {
            $data_head['head_name'] = "Admin Panel";
            $data_head['link_head'] = site_url('admin');
            $this->load->view('v_header_admin', $data_head);
            $this->load->view('admin/v_company_add', $data);
            $this->load->view('v_footer');
        }
    }
    public function delete_company() {
        $id = $this->uri->segment(3, '');
        $this->m_company->delete_company($id);
        redirect('admin/company_list');
    }



    ///////////////////////////////////// business/////////////////////////////////////////////////////
    public function business() {
            $data_foot['table'] = "yes";
            $data_head['user_data'] = $this->user_data;
            $data_view['business_list'] = $this->m_business->get_all_business();
            $this->load->view('v_header_admin', $data_head);
            $this->load->view('admin/v_business_list', $data_view);
            $this->load->view('v_footer', $data_foot);
        }


    public function business_add() {
        
        $business_id = $this->m_business->generate_id();
        $data_head['user_data'] = $this->user_data;
        $data['A'] = "0";
        if (isset($_POST['name'])) {
            if ($_POST['name'] == "") {
                $data['err_msg'] = "กรุณากรอก name";
                $data_head['head_name'] = "Admin Panel";
                $data_head['link_head'] = site_url('admin');
                
                $this->load->view('v_header_admin', $data_head);
                $this->load->view('admin/v_business_add', $data);
                $this->load->view('v_footer');
            } 
            else {
                $data = array('id' => $business_id,'name' => $_POST['name'],);
                $this->m_business->add_business($data);                
                redirect('admin/business');
            }
        } 
        else {
            $data_head['head_name'] = "Admin Panel";
            $data_head['link_head'] = site_url('admin');
            $this->load->view('v_header_admin', $data_head);
            $this->load->view('admin/v_business_add', $data);
            $this->load->view('v_footer');
        }
    }

    public function business_edit() {
        $id = $this->uri->segment(3, '');
        $data_head['user_data'] = $this->user_data;
        $data['A'] = "0";
        $data['edit'] = "yes";
        $data['business'] = $this->m_business->get_business_by_id($id);
        if (isset($_POST['name'])) {
            if ($_POST['name'] == "") {
                $data['err_msg'] = "กรุณากรอก name";
                $data_head['head_name'] = "Admin Panel";
                $data_head['link_head'] = site_url('admin');
                
                $this->load->view('v_header_admin', $data_head);
                $this->load->view('admin/v_business_add', $data);
                $this->load->view('v_footer');
            } 
            else {
                $data = array('id' => $id,'name' => $_POST['name'],);
                $this->m_business->update_business($data, $id);
                
                redirect('admin/business');
            }
        } 
        else {
            $data_head['head_name'] = "Admin Panel";
            $data_head['link_head'] = site_url('admin');
            $this->load->view('v_header_admin', $data_head);
            $this->load->view('admin/v_business_add', $data);
            $this->load->view('v_footer');
        }
    }

     ///////////////////////////////////// department/////////////////////////////////////////////////////
    public function department() {
            $data_foot['table'] = "yes";
            $data_head['user_data'] = $this->user_data;
            $data_view['department_list'] = $this->m_department->get_all_department();
            $this->load->view('v_header_admin', $data_head);
            $this->load->view('admin/v_department_list', $data_view);
            $this->load->view('v_footer', $data_foot);
        }

    public function department_add() {
        
        $department_id = $this->m_department->generate_id();
        $data_head['user_data'] = $this->user_data;
        $data['A'] = "0";
        $data['business_list'] = $this->m_business->get_all_business();
        if (isset($_POST['name'])) {
            if ($_POST['name'] == "") {
                $data['err_msg'] = "กรุณากรอก name";
                $data_head['head_name'] = "Admin Panel";
                $data_head['link_head'] = site_url('admin');
                
                $this->load->view('v_header_admin', $data_head);
                $this->load->view('admin/v_department_add', $data);
                $this->load->view('v_footer');
            }else if ($_POST['business_id'] == "no") {
                $data['err_msg'] = "กรุณาเลือก Business";
                $data_head['head_name'] = "Admin Panel";
                $data_head['link_head'] = site_url('admin');
                
                $this->load->view('v_header_admin', $data_head);
                $this->load->view('admin/v_department_add', $data);
                $this->load->view('v_footer');
            }  
            else {
                $data = array('id' => $department_id,'name' => $_POST['name'],'business_id' => $_POST['business_id'],);
                $this->m_department->add_department($data);                
                redirect('admin/department');
            }
        } 
        else {
            $data_head['head_name'] = "Admin Panel";
            $data_head['link_head'] = site_url('admin');
            $this->load->view('v_header_admin', $data_head);
            $this->load->view('admin/v_department_add', $data);
            $this->load->view('v_footer');
        }
    }    

    public function department_edit() {
        $id = $this->uri->segment(3, '');
        $data_head['user_data'] = $this->user_data;
        $data['A'] = "0";
        $data['edit'] = "yes";
        $data['department'] = $this->m_department->get_department_by_id($id);
        $data['business_list'] = $this->m_business->get_all_business();
        if (isset($_POST['name'])) {
            if ($_POST['name'] == "") {
                $data['err_msg'] = "กรุณากรอก name";
                $data_head['head_name'] = "Admin Panel";
                $data_head['link_head'] = site_url('admin');
                
                $this->load->view('v_header_admin', $data_head);
                $this->load->view('admin/v_department_add', $data);
                $this->load->view('v_footer');
            }else if ($_POST['business_id'] == "no") {
                $data['err_msg'] = "กรุณาเลือก Business";
                $data_head['head_name'] = "Admin Panel";
                $data_head['link_head'] = site_url('admin');
                
                $this->load->view('v_header_admin', $data_head);
                $this->load->view('admin/v_department_add', $data);
                $this->load->view('v_footer');
            }   
            else {
                $data = array('id' => $id,'name' => $_POST['name'],'business_id' => $_POST['business_id'],);
                $this->m_department->update_department($data, $id);
                
                redirect('admin/department');
            }
        } 
        else {
            $data_head['head_name'] = "Admin Panel";
            $data_head['link_head'] = site_url('admin');
            $this->load->view('v_header_admin', $data_head);
            $this->load->view('admin/v_department_add', $data);
            $this->load->view('v_footer');
        }
    }
    public function delete_department() {
        $id = $this->uri->segment(3, '');
        $this->m_department->delete_department($id);
        redirect('admin/department');
    }


    ///////////////////////////////////// Position /////////////////////////////////////////////////////
    public function position() {
            $data_foot['table'] = "yes";
            $data_head['user_data'] = $this->user_data;
            $data_view['position_list'] = $this->m_position->get_all_position();
            $this->load->view('v_header_admin', $data_head);
            $this->load->view('admin/v_position_list', $data_view);
            $this->load->view('v_footer', $data_foot);
        }
    public function position_add() {
        
        $position_id = $this->m_position->generate_id();
        $data_head['user_data'] = $this->user_data;
        $data['A'] = "0";
        $data['department_list'] = $this->m_department->get_all_department();
        if (isset($_POST['name'])) {
            if ($_POST['name'] == "") {
                $data['err_msg'] = "กรุณากรอก name";
                $data_head['head_name'] = "Admin Panel";
                $data_head['link_head'] = site_url('admin');
                
                $this->load->view('v_header_admin', $data_head);
                $this->load->view('admin/v_position_add', $data);
                $this->load->view('v_footer');
            }else if ($_POST['department_id'] == "no") {
                $data['err_msg'] = "กรุณาเลือก Department";
                $data_head['head_name'] = "Admin Panel";
                $data_head['link_head'] = site_url('admin');
                
                $this->load->view('v_header_admin', $data_head);
                $this->load->view('admin/v_position_add', $data);
                $this->load->view('v_footer');
            }  
            else {
                $data = array('id' => $position_id,'name' => $_POST['name'],'department_id' => $_POST['department_id'],'description' => $_POST['description'],'non_productive' => $_POST['non_productive'],);
                $this->m_position->add_position($data);          
                $this->m_hour_rate->delete_hour_rate_has_position($position_id);
                foreach ($_POST['work'] as $key => $value) {
                    $hour_data = array('position_id' => $position_id, 'hour_rate_id' => $value,);
                    $this->m_hour_rate->add_hour_rate_has_position($hour_data);
                }      
                redirect('admin/position');
            }
        } 
        else {
            $data_head['head_name'] = "Admin Panel";
            $data_head['link_head'] = site_url('admin');
            $this->load->view('v_header_admin', $data_head);
            $this->load->view('admin/v_position_add', $data);
            $this->load->view('v_footer');
        }
    }   

    public function position_edit() {
        $id = $this->uri->segment(3, '');
        $data_head['user_data'] = $this->user_data;
        $data['A'] = "0";
        $data['edit'] = "yes";
        $data['position'] = $this->m_position->get_position_by_id($id);
        $data['department_list'] = $this->m_department->get_all_department();
        if (isset($_POST['name'])) {
            if ($_POST['name'] == "") {
                $data['err_msg'] = "กรุณากรอก name";
                $data_head['head_name'] = "Admin Panel";
                $data_head['link_head'] = site_url('admin');
                
                $this->load->view('v_header_admin', $data_head);
                $this->load->view('admin/v_position_add', $data);
                $this->load->view('v_footer');
            }else if ($_POST['department_id'] == "no") {
                $data['err_msg'] = "กรุณาเลือก Department";
                $data_head['head_name'] = "Admin Panel";
                $data_head['link_head'] = site_url('admin');
                
                $this->load->view('v_header_admin', $data_head);
                $this->load->view('admin/v_position_add', $data);
                $this->load->view('v_footer');
            }   
            else {
                $data = array('id' => $id,'name' => $_POST['name'],'department_id' => $_POST['department_id'],'description' => $_POST['description'],'non_productive' => $_POST['non_productive'],);
                $this->m_position->update_position($data, $id);
                $this->m_hour_rate->delete_hour_rate_has_position($id);
                foreach ($_POST['work'] as $key => $value) {
                    $hour_data = array('position_id' => $id, 'hour_rate_id' => $value,);
                    $this->m_hour_rate->add_hour_rate_has_position($hour_data);
                }
                
                redirect('admin/position');
            }
        } 
        else {
            $data_head['head_name'] = "Admin Panel";
            $data_head['link_head'] = site_url('admin');
            $this->load->view('v_header_admin', $data_head);
            $this->load->view('admin/v_position_add', $data);
            $this->load->view('v_footer');
        }
    }
    public function delete_position() {
        $id = $this->uri->segment(3, '');
        $this->m_position->delete_position($id);
        redirect('admin/position');
    }


    //////////////////////////////////////// traffic controll ///////////////////////////////////////////////
    public function traffic_control() {
        $data_foot['aa'] = "yes";
        $data_head['user_data'] = $this->user_data;
        $data_view['dat']=$this->m_business->get_all_business();
        foreach ($data_view['dat'] as $key => $value) {
            $data_view['dat'][$key]->project=$this->m_traffic_control->get_all_project_by_unit($value->id);
            foreach ($data_view['dat'][$key]->project as $key2 => $value2) {           
                $data_view['dat'][$key]->project[$key2]->budget=0;     
                $data_view['dat'][$key]->project[$key2]->budget_allocate=0;
                $data_view['dat'][$key]->project[$key2]->budget_spend=0;
                $data_view['dat'][$key]->project[$key2]->num_task=0;
                $data_view['dat'][$key]->project[$key2]->num_res=0;
                $last_work=$this->m_work_sheet->get_last_end_time_work_sheet_by_project_id($value2->project_id);
                if (isset($last_work->end)) {
                    $data_view['dat'][$key]->project[$key2]->project_end=$last_work->end;
                }                
                $data_view['dat'][$key]->project[$key2]->work_list=$this->m_work_sheet->get_work_sheet_by_project_id($value2->project_id);
                $data_view['dat'][$key]->project[$key2]->num_task=count($data_view['dat'][$key]->project[$key2]->work_list);

                foreach ($data_view['dat'][$key]->project[$key2]->work_list as $key3 => $value3) {
                    $data_view['dat'][$key]->project[$key2]->work_list[$key3]->assign_detail=$this->m_work_sheet->get_res_assign_detail_by_work_id($value3->id);
                    $data_view['dat'][$key]->project[$key2]->num_res+=count($value3->assign_detail);
                    $data_view['dat'][$key]->project[$key2]->work_list[$key3]->budget_allocate=0;
                    $data_view['dat'][$key]->project[$key2]->work_list[$key3]->budget_spend=0;
                    foreach ($data_view['dat'][$key]->project[$key2]->work_list[$key3]->assign_detail as $key4 => $value4) {
                        $data_view['dat'][$key]->project[$key2]->budget_allocate+=$value4->assign_list->hour_amount;
                        $data_view['dat'][$key]->project[$key2]->budget_spend+=$value4->assign_list->spend_amount;

                        $data_view['dat'][$key]->project[$key2]->work_list[$key3]->budget_allocate+=$value4->assign_list->hour_amount;;
                        $data_view['dat'][$key]->project[$key2]->work_list[$key3]->budget_spend+=$value4->assign_list->spend_amount;;
                    }
                }
                $data_view['dat'][$key]->project[$key2]->r_sheet=$this->m_Rsheet->get_all_r_sheet_by_project_id($value2->project_id);
                $data_view['dat'][$key]->project[$key2]->budget=0;
                foreach ($data_view['dat'][$key]->project[$key2]->r_sheet as $r_key => $r_value) {
                    $data_view['dat'][$key]->project[$key2]->budget+=$r_value->approve_budget;
                }

            }
            
        }
        //print_r($data_view['dat']);
        $this->load->view('v_header_admin', $data_head);
        $this->load->view('admin/v_traff_con',$data_view);
        $this->load->view('v_footer', $data_foot);
    }

    ////////////////////////////////////////////////////////////////////////////// resource Manager//////////////////////////////////////

    public function res_manager_overall() {
        $data_foot['table'] = "yes";
        $data_head['user_data'] = $this->user_data;
        $all_child=$this->m_user->get_all_user();
        
        $data_head['user_data'] = $this->user_data;
        $data_view['all_child'] = array();
        $position=$this->m_position->get_all_position();
        foreach ($all_child as $key => $value) {
            if (isset($position[$value->position])&&$position[$value->position]->non_productive=="n") {
                $data_view['all_child'][$key]=$value;
                $data_view['all_child'][$key]->daily_dat=$this->get_detail_res_for_overall_view($value->username);
            }
            
        }
        //print_r($data_view['all_child']);
        $this->load->view('v_header_admin', $data_head);
        $this->load->view('admin/v_res_manager_overall',$data_view);
        $this->load->view('v_footer', $data_foot);
    }
    public function res_manager_detail() {
        $data_foot['table'] = "yes";
        $data_head['user_data'] = $this->user_data;
        $all_child=$this->m_user->get_all_user();
        
        $data_head['user_data'] = $this->user_data;
        $data_view['all_child'] = array();
        $position=$this->m_position->get_all_position();
        foreach ($all_child as $key => $value) {
            if (isset($position[$value->position])&&$position[$value->position]->non_productive=="n") {
                $data_view['all_child'][$key]=$value;
                $data_view['all_child'][$key]->project_wip=$this->ger_detail_resource_for_res_manager($value->username);
            }
            
        }
        //print_r($data_view['all_child']);
        $this->load->view('v_header_admin', $data_head);
        $this->load->view('admin/v_res_manager_detail',$data_view);
        $this->load->view('v_footer', $data_foot);
    }
    function ger_detail_resource_for_res_manager($username){
        $current_day_time=$this->m_time->datepicker_to_unix(date("d/m/Y"));
        $start_car_time=$current_day_time-(60*60*24*10);
        $end_car_time=$current_day_time+(60*60*24*30);
        $data_view['project_wip']=$this->m_resource->get_project_WIP_revise($username);
        foreach ($data_view['project_wip'] as $key => $value) {
            $data_view['project_wip'][$key]->work_sheet=$this->m_work_sheet->get_work_sheet_by_project_id($key);
            foreach ($data_view['project_wip'][$key]->work_sheet as $key2 => $value2) {
                $data_view['project_wip'][$key]->work_sheet[$key2]->assign_obj=$this->m_work_sheet->get_res_assign_by_work_id_and_usn($value2->id,$username,$start_car_time);
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
        $assign_obj=$this->m_work_sheet->get_res_assign_by_usn_with_start_end($username,$start_car_time);
        return $assign_obj;
    }



    //////////////////////////////////////////////// DeLete project //////////////////////
    public function project_cancel_list() {
        $data_foot['table'] = "yes";
        $data_head['user_data'] = $this->user_data;
        $data_view['project_list'] = $this->m_project->get_all_project_by_status("Cancel");
        $this->load->view('v_header_admin', $data_head);
        $this->load->view('admin/v_project_cancel_list',$data_view);
        $this->load->view('v_footer', $data_foot);
    }
    public function delete_project() {
        $id = $this->uri->segment(3, '');
        $project=$this->m_project->get_project_by_id($id);
        $project_attachment_financial=$this->m_project->get_all_project_attachment($id,"financial");
        foreach ($project_attachment_financial as $key => $value) {
            $this->m_project->delete_project_attachment($value->id);
        }

        $project_attachment_note=$this->m_project->get_all_project_attachment($id,"note");
        foreach ($project_attachment_note as $key => $value) {
            $this->m_project->delete_project_attachment($value->id);
        }
        $pce_list=$this->m_pce->get_all_pce_by_project_id($id,true);
        foreach ($pce_list as $key => $value) {
            $this->m_pce->delete_pce($value->id);
        }
        $work_list=$this->m_work_sheet->get_work_sheet_by_project_id($id);
        foreach ($work_list as $key => $value) {
            $work_has_res=$this->m_work_sheet->get_work_sheet_has_res_by_work_id($value->id);
            foreach ($work_has_res as $key2 => $value2) {
                $this->m_work_sheet->delete_work_sheet_has_res($value2->id);
            }
            $this->m_work_sheet->delete_work_sheet($value->id);
        }
        $this->m_project->delete_project($id);
        redirect('admin/project_cancel_list');
    }

    //////////////////////////////////////////////////////////////////// correct HOD Approve PCe
    public function correct_pce_hod() {
        $ap_list=$this->m_pce->get_all_hod_approve_pce();
        $ch_arr = array();
        foreach ($ap_list as $key => $value) {
            if (!isset($ch_arr[$value->hod_usn][$value->pce_id])) {
                $ch_arr[$value->hod_usn][$value->pce_id]="have";
            }else{
                $this->m_pce->delete_hod_approve_by_id($value->id);
            }
        }

    }


}





/* End of file welcome.php */

/* Location: ./application/controllers/welcome.php */

