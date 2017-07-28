<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Gate extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('m_user');
		$this->load->model('m_time');
		$this->load->model('m_Rsheet');
		if ($this->session->userdata('username')) {
			$user_data=$this->m_user->get_user_by_login_name($this->session->userdata('username'));
			if (isset($user_data->username)) {
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
		$data_foot['table']="yes";
		$data_head['user_data']=$this->user_data;
		$data_view['user_data']=$this->user_data;
		$data_head['head_01']="yes";
		$this->load->view('v_header',$data_head);
		$this->load->view('v_gate',$data_view);
		$this->load->view('v_footer',$data_foot);
	}


}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */