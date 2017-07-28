<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Main extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('m_user');
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

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */