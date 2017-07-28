<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Main extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('m_user');
		$this->load->model('m_time');
		$this->load->model('m_group');
		$this->load->model('m_position');
	}

	public function index()
	{
		$this->load->view('v_login');
	}

	public function login()
	{	
	$data['error_msg2']='Please login with your username and password';

	if(isset($_POST['login']) && $_POST['login'] == 'yes' )
		{
			$user_data=$this->m_user->get_user($_POST['username'],$_POST['password']);
			//echo $_POST['login_name']." asdasd ".$_POST['password'];
			if (isset($user_data->username)) {
				$this->session->set_userdata('username', $user_data->username);
				$data2 = array(
	               'last_access' => time()
	            );

				$this->db->where('username', $user_data->username);
				$this->db->update('user', $data2); 
				redirect('gate');

			}else{			
				$this->load->view('v_login',$data);
				$this->session->sess_destroy();
			}			
		}else{
			$this->load->view('v_login',$data);
			$this->session->sess_destroy();
		}	
		
	}
	public function logout()
	{		
		$this->session->set_userdata('username', '');
		$this->session->sess_destroy();
		redirect('main');
	}
	public function Register()
	{
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
                
                $this->load->view('v_register', $data);
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
                    'g_prem_id' => "90ce2f6d18", 
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
                ?>
                <script type="text/javascript">
                	alert("เรียบร้อยแล้ว")
                </script>
                <?
                
                redirect('');
            }
        } 
        else {
            $data_head['head_name'] = "Admin Panel";
            $data_head['link_head'] = site_url('admin');
            $this->load->view('v_register', $data);
            $this->load->view('v_footer');
        }
	}

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */