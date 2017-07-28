<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Profile extends CI_Controller
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
    
    
    // ---------------------------user section ---------------------------------------
    
    public function index() {
        $data_head['user_data'] = $this->user_data;
        
        $data['user'] = $this->m_user->get_user_by_login_name($this->user_data->username);
        $data['user_list'] = $this->m_user->get_all_user();
        $data['group_list'] = $this->m_group->get_all_group();
        $data['position_list'] = $this->m_position->get_all_position();
        $data['edit'] = "yes";
        if (isset($_POST['firstname'])) {
            if ($_POST['password'] != $_POST['confirm_password']) {
                
                $data['err_msg'] = "กรุณากรอกรหัสผ่านให้ตรงกัน";
                $data_head['head_name'] = "Admin Panel";
                $data_head['link_head'] = site_url('admin');
                
                $this->load->view('v_header', $data_head);
                $this->load->view('admin/v_user_add', $data);
                $this->load->view('v_footer');
            } 
            else {
                $data_insert = array(
                    'firstname' => $_POST['firstname'], 
                    'lastname' => $_POST['lastname'], 
                    'phone' => $_POST['phone'], 
                    'password' => $_POST['password'], 
                    'nickname' => $_POST['nickname'],
                    'weight' => $_POST['weight'],
                    'position' => $_POST['position'],
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
                $this->m_user->update_user($data_insert, $this->user_data->username);
                
                redirect('profile');
            }
        } 
        else {
            $data_head['head_name'] = "Profile";
            $data_head['link_head'] = site_url('main');
            $this->load->view('v_header', $data_head);
            $this->load->view('profile/v_user_add', $data);
            $this->load->view('v_footer');
        }
    }
   
}