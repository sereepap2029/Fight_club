<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Project extends CI_Controller {

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
        $this->load->model('m_account');
		if ($this->session->userdata('username')) {
			$user_data=$this->m_user->get_user_by_login_name($this->session->userdata('username'));
			$prem_flag=(isset($user_data->prem['cs'])
				||isset($user_data->prem['csd'])
				||isset($user_data->prem['hod'])
				||isset($user_data->prem['fc'])
				||isset($user_data->prem['hr'])||isset($user_data->prem['account']));
			if (isset($user_data->username)&&$prem_flag) {
				$this->user_data=$user_data;
			}else{
				redirect('main/logout');
			}
		}else{
			redirect('main/logout');
		}
	}

	public function project_note()
	{
		$id=$this->uri->segment(3,'');
		if (isset($_POST['dat'])) {
			header('Content-Type: application/json');
			$json = array();
			$json['flag']="OK";
			$data = array('project_note' => $_POST['dat'], );
			$this->m_project->update_project($data,$id);
			echo json_encode($json);
		}else{			
			$data['project_id']=$id;
			$data['project']=$this->m_project->get_project_by_id($id);
			$data['note_name']=" Project Note : ".$data['project']->project_name;
			$data['func_note']="project_note";
			$data['a_type']="note";
			$data['project_attachment']=$this->m_project->get_all_project_attachment($id,"note");
			$this->load->view('v_header_popup');
			$this->load->view('v_project_note',$data);
			$this->load->view('v_footer');
		}
		
	}
	public function finan_note()
	{
		$id=$this->uri->segment(3,'');
		if (isset($_POST['dat'])) {
			header('Content-Type: application/json');
			$json = array();
			$json['flag']="OK";
			$data = array('finan_note' => $_POST['dat'], );
			$this->m_project->update_project($data,$id);
			echo json_encode($json);
		}else{			
			$data['project_id']=$id;
			$data['project']=$this->m_project->get_project_by_id($id);
			$data['note_name']=" Financial Note : ".$data['project']->project_name;
			$data['func_note']="finan_note";
			$data['a_type']="financial";
			$data['project_attachment']=$this->m_project->get_all_project_attachment($id,"financial");
			$this->load->view('v_header_popup');
			$this->load->view('v_project_note',$data);
			$this->load->view('v_footer');
		}
	}












	///////////////////////////////////  project_attachment AJAX region ///////////////////////////////////////
	public function add_project_attachment_file()
	{
		header('Content-Type: application/json');
        $json = array();
        $json['flag']="OK";
        if ($_POST['file_path'] != ""&&$_POST['project_id'] != "") {
                    $attachment_id=$this->m_project->generate_project_attachment_id();
                    $filename = $_POST['file_path'];
                    $ext = explode(".", $filename);
                    $new_ext = $ext[count($ext) - 1];
                    $new_filename = $_POST['project_id'] ."_".$attachment_id. "." . $new_ext;
                    $file = './media/temp/' . $filename;
                    $newfile = './media/project_attachment/' . $new_filename;
                    
                    if (!copy($file, $newfile)) {
                        echo "failed to copy $file...\n" . $file . " to " . $newfile . "  and  ";
                        
                        @unlink("./media/temp/" . $filename);
                    } 
                    else {
                        $data = array(
                            'id' => $attachment_id,
                            'project_id' => $_POST["project_id"],
                            'filename' => $new_filename,
                            'sort_order' => 999,
                            'type' => $_POST["type"],
                            'origin_filename' => $filename,
                            );
                        $this->m_project->add_project_attachment($data,$_POST["project_id"]);
                        @unlink("./media/temp/" . $filename);
                        $json['html']='<div class="img-region" id="'.$attachment_id.'">'.
                                        '<a href="'.site_url("media/project_attachment/".$new_filename).'" title="" target="_blank">'.
                                           $filename.
                                        '</a>'.
                                        '<input type="hidden" name="photo_id_list[]" value="'.$attachment_id.'">'.
                                        '<a href="javascript:;" id-dat="'.$attachment_id.'" class="btn btn-danger del_img_but"><i class="icon-remove icon-white"></i></a>'.
                                    '</div>';
                    }
                }else{
                    $json['flag']="File or work ID not receive  ";
                }
        
        echo json_encode($json);
		
	}
	public function del_attachment_file(){
        header('Content-Type: application/json');
        $json = array();
        $json['flag']="OK";
        $this->m_project->delete_project_attachment($_POST["file_id"]);
        echo json_encode($json);
    }
    public function sort_attachment_file(){
        header('Content-Type: application/json');
        $json = array();
        $json['flag']="OK";
        $count=1;
        if (isset($_POST['photo_id_list'])) {
            foreach ($_POST['photo_id_list'] as $key => $value) {
                $data = array(
                    'sort_order' =>  $count,
                    );
                $this->m_project->update_project_attachment($data,$value);
                $count+=1;
            }
        }
        echo json_encode($json);
    }
    /////////////////////////////////// END  project_attachment AJAX region ///////////////////////////////////////












	public function index()
	{
		$data_foot['table']="yes";
		$data_head['user_data']=$this->user_data;
		$data_view['project_list']=$this->m_project->get_all_project();
		$this->load->view('v_header',$data_head);
		$this->load->view('v_project_list',$data_view);
		$this->load->view('v_footer',$data_foot);
	}
	public function add()
	{

		$data_foot['table']="yes";
		$data_head['user_data']=$this->user_data;
		$data['a']="0";
		$data['company']=$this->m_company->get_all_company();
		$data['business_list'] = $this->m_business->get_all_business();
			$this->load->view('v_header',$data_head);
			$this->load->view('v_project_add',$data);
			$this->load->view('v_footer',$data_foot);
		
	}
	public function edit()
	{
		$id=$this->uri->segment(3,'');
		$data_foot['table']="yes";
		$data_head['user_data']=$this->user_data;
		$data['user_data']=$this->user_data;
		$data['a']="0";
		$data['company']=$this->m_company->get_all_company();
		$data['project']=$this->m_project->get_project_by_id($id);
		$data['r_sheet']=$this->m_Rsheet->get_all_r_sheet_by_project_id($id);
		$data['pce_doc']=$this->m_pce->get_all_pce_by_project_id($id,false,true);
		$data['business_list'] = $this->m_business->get_all_business();
		//print_r($data['pce_doc']);
			$this->load->view('v_header',$data_head);
			$this->load->view('v_project_edit',$data);
			$this->load->view('v_footer',$data_foot);
		
	}
	public function edit_oc()
	{
		$id=$this->uri->segment(3,'');
		$data_foot['table']="yes";
		$data_head['user_data']=$this->user_data;
		$data['user_data']=$this->user_data;
		$data['a']="0";
		$data['company']=$this->m_company->get_all_company();
		$data['project']=$this->m_project->get_project_by_id($id);
		$data['r_sheet']=$this->m_Rsheet->get_all_r_sheet_by_project_id($id);
		$data['pce_doc']=$this->m_pce->get_all_pce_by_project_id($id,false,true);
		$data['business_list'] = $this->m_business->get_all_business();
		//print_r($data['pce_doc']);
			$this->load->view('v_header',$data_head);
			$this->load->view('v_project_edit_oc',$data);
			$this->load->view('v_footer',$data_foot);
		
	}
	public function delete_project(){
		$id=$this->uri->segment(3,'');
		$this->m_project->delete_project($id);
		redirect('project');
	}
	public function view_sign_pce(){
		require_once('./assets/fpdf17/fpdf.php');
		require_once('./assets/fpdf17/fpdi.php');
		$pce_id=$this->uri->segment(3,'');
		
		// init pce data                       
		$pce_dat=$this->m_pce->get_pce_by_id($pce_id);
		$project=$this->m_project->get_project_by_id($pce_dat->project_id);
        //init sign pic
        $ae_sign_user_dat=$this->m_user->get_user_by_login_name($project->project_cs);
        $ae_sign_filename="no";
        if (isset($ae_sign_user_dat->sign_filename)) {
          $ae_sign_filename=$ae_sign_user_dat->sign_filename;
        }
        $client_service_sign_dat=$this->m_user->get_user_by_login_name($pce_dat->csd_sign);
        $client_service_sign_filename="no";
        if (isset($client_service_sign_dat->sign_filename)) {
          $client_service_sign_filename=$client_service_sign_dat->sign_filename;
        }
        $finance_sign_dat=$this->m_user->get_user_by_login_name($pce_dat->fc_sign);
        $finance_sign_filename="no";
        if (isset($finance_sign_dat->sign_filename)) {
          $finance_sign_filename=$finance_sign_dat->sign_filename;
        }
        // initiate FPDI
        $pdf = new FPDI();

        // เพิ่มฟอนต์ภาษาไทยเข้ามา ตัวธรรมดา กำหนด ชื่อ เป็น angsana
            $pdf->AddFont('angsana','','angsa.php');
             
            // เพิ่มฟอนต์ภาษาไทยเข้ามา ตัวหนา  กำหนด ชื่อ เป็น angsana
            $pdf->AddFont('angsana','B','angsab.php');
             
            // เพิ่มฟอนต์ภาษาไทยเข้ามา ตัวหนา  กำหนด ชื่อ เป็น angsana
            $pdf->AddFont('angsana','I','angsai.php');
             
            // เพิ่มฟอนต์ภาษาไทยเข้ามา ตัวหนา  กำหนด ชื่อ เป็น angsana
            $pdf->AddFont('angsana','BI','angsaz.php');


        // get the page count
        $pageCount = $pdf->setSourceFile("./media/real_pdf/".$pce_dat->filename);
        // iterate through all pages
        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            // import a page
            $templateId = $pdf->importPage($pageNo);
            // get the size of the imported page
            $size = $pdf->getTemplateSize($templateId);

            // create a page (landscape or portrait depending on the imported page size)
            if ($size['w'] > $size['h']) {
                $pdf->AddPage('L', array($size['w'], $size['h']));
            } else {
                $pdf->AddPage('P', array($size['w'], $size['h']));
            }

            // use the imported page
            $pdf->useTemplate($templateId);

            $pdf->SetTextColor(0,0,0);
            $pdf->SetFont('angsana','B',14);
            $pdf->SetXY(5, 5);
            //$pdf->Write(8, 'A complete document imported with FPDI');
            $all_three_sign=0;
            if ($finance_sign_filename!="no"&&$finance_sign_filename!=""&&$finance_sign_filename!=null) {
              $pdf->Image("./media/sign_photo/".$finance_sign_filename,152,242,40,0);
              $c_time=$pce_dat->fc_sign_time;
              $pdf->SetXY(162,269);
              $pdf->Cell(30,5,iconv( 'UTF-8','cp874' , date("d",$c_time)."     ".date("m",$c_time)."     ".date("Y",$c_time)),0,0);
              $all_three_sign+=1;
            }
            if ($client_service_sign_filename!="no"&&$client_service_sign_filename!=""&&$client_service_sign_filename!=null) {
              $pdf->Image("./media/sign_photo/".$client_service_sign_filename,86,242,40,0);
              $c_time=$pce_dat->csd_sign_time;
              $pdf->SetXY(96,269.5);
              $pdf->Cell(30,5,iconv( 'UTF-8','cp874' , date("d",$c_time)."     ".date("m",$c_time)."     ".date("Y",$c_time)),0,0);
              $all_three_sign+=1;
            }
            if ($ae_sign_filename!="no"&&$ae_sign_filename!=""&&$ae_sign_filename!=null) {
              $pdf->Image("./media/sign_photo/".$ae_sign_filename,20,242,40,0);
              $c_time=$pce_dat->cs_sign_time;
              $pdf->SetXY(30,270);
              $pdf->Cell(30,5,iconv( 'UTF-8','cp874' , date("d",$c_time)."     ".date("m",$c_time)."     ".date("Y",$c_time)),0,0);
              $all_three_sign+=1;
            }
            //if ($all_three_sign==3) {
            	$pdf->Image("./img/Client_Approval_Tag.png",110,204.2,35,0);
            //}
            //$pdf->Image("images/play.png",100,100,100,0);
        }
        $new_filename=$pce_dat->pce_no."_A.pdf";

        // Output the new PDF
        //@unlink("./media/real_pdf/".$new_filename);
        $pdf->Output($new_filename,"I");
        //redirect("media/real_pdf/".$new_filename);
	}

	public function view_sign_oc(){
		require_once('./assets/fpdf17/fpdf.php');
		require_once('./assets/fpdf17/fpdi.php');
		$oc_id=$this->uri->segment(3,'');
		
		// init pce data                       
		$oc_dat=$this->m_oc->get_oc_by_id($oc_id);
		$project=$this->m_project->get_project_by_id($oc_dat->project_id);
        //init sign pic
        $ae_sign_user_dat=$this->m_user->get_user_by_login_name($project->project_cs);
        $ae_sign_filename="no";
        if (isset($ae_sign_user_dat->sign_filename)) {
          $ae_sign_filename=$ae_sign_user_dat->sign_filename;
        }
        $finance_sign_dat=$this->m_user->get_user_by_login_name($oc_dat->fc_sign);
        $finance_sign_filename="no";
        if (isset($finance_sign_dat->sign_filename)) {
          $finance_sign_filename=$finance_sign_dat->sign_filename;
        }
        // initiate FPDI
        $pdf = new FPDI();

        // เพิ่มฟอนต์ภาษาไทยเข้ามา ตัวธรรมดา กำหนด ชื่อ เป็น angsana
            $pdf->AddFont('angsana','','angsa.php');
             
            // เพิ่มฟอนต์ภาษาไทยเข้ามา ตัวหนา  กำหนด ชื่อ เป็น angsana
            $pdf->AddFont('angsana','B','angsab.php');
             
            // เพิ่มฟอนต์ภาษาไทยเข้ามา ตัวหนา  กำหนด ชื่อ เป็น angsana
            $pdf->AddFont('angsana','I','angsai.php');
             
            // เพิ่มฟอนต์ภาษาไทยเข้ามา ตัวหนา  กำหนด ชื่อ เป็น angsana
            $pdf->AddFont('angsana','BI','angsaz.php');


        // get the page count
        $pageCount = $pdf->setSourceFile("./media/real_pdf/".$oc_dat->filename);
        // iterate through all pages
        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            // import a page
            $templateId = $pdf->importPage($pageNo);
            // get the size of the imported page
            $size = $pdf->getTemplateSize($templateId);

            // create a page (landscape or portrait depending on the imported page size)
            if ($size['w'] > $size['h']) {
                $pdf->AddPage('L', array($size['w'], $size['h']));
            } else {
                $pdf->AddPage('P', array($size['w'], $size['h']));
            }

            // use the imported page
            $pdf->useTemplate($templateId);

            $pdf->SetTextColor(0,0,0);
            $pdf->SetFont('angsana','B',14);
            $pdf->SetXY(5, 5);
            //$pdf->Write(8, 'A complete document imported with FPDI');

            //// temporaly disable 
            if ($finance_sign_filename!="no"&&$finance_sign_filename!=""&&$finance_sign_filename!=null) {
              //$pdf->Image("./media/sign_photo/".$finance_sign_filename,100,10,40,0);
            }
            if ($ae_sign_filename!="no"&&$ae_sign_filename!=""&&$ae_sign_filename!=null) {
              //$pdf->Image("./media/sign_photo/".$ae_sign_filename,100,110,40,0);
            }
            //$pdf->Image("images/play.png",100,100,100,0);
        }
        $new_filename=$oc_dat->oc_no."_A.pdf";

        // Output the new PDF
        //@unlink("./media/real_pdf/".$new_filename);
        $pdf->Output($new_filename,"I");
        //redirect("media/real_pdf/".$new_filename);
	}


	public function add_project_action(){
		$project_id=$this->m_project->generate_id();
		$project_dat = array(
			'project_id' => $project_id,
			'project_client' => $_POST['project_client'],
			'project_bu' => $_POST['project_bu'],
			'project_start' => $this->m_time->datepicker_to_unix($_POST['project_start']),
			'project_end' => $this->m_time->datepicker_to_unix($_POST['project_end']),
			'project_name' => $_POST['project_name'],
			'business_unit_id' => $_POST['business_unit_id'],
            'account_unit_id' => $_POST['account_unit_id'],
			'project_cs' => $this->user_data->username,
			);
		if (isset($_POST['submit_job'])) {
			$project_dat['status']='Proposing';
		}
		$this->m_project->add_project($project_dat);
		$this->m_company->update_company(array('is_use' => "y", ),$_POST['project_client']);
		$this->m_company->update_bu(array('is_use' => "y", ),$_POST['project_bu']);

		$r_sheet = array();
		$hod_array=array();
		if (!isset($_POST['task'])) {
				$_POST['task']=array();
			}
		foreach ($_POST['task'] as $key => $value) {
			$r_id=$this->m_Rsheet->generate_id();
			$r_sheet[$key] = array(
				'r_id' => $r_id,
				'task' => $_POST['task'][$key],
				'type' => $_POST['type'][$key],
				'approve_budget' => (int)$_POST['approve_budget'][$key],
				'project_id' => $project_id,
				//'sort_order' => $this->m_Rsheet->get_sort_order($project_id),
				'sort_order' => (float)$_POST['sort_order'][$key],
				);
			$this->m_Rsheet->add_r_sheet ($r_sheet[$key]);
			$h_r_dat=$this->m_hour_rate->get_hour_rate_by_id($_POST['type'][$key]);
			if ($h_r_dat->is_special=="n") {
				$hod_array[$key]=$this->m_Rsheet->find_hod_by_t_type($_POST['type'][$key]);
                echo "inloop hod<br>";
                print_r($hod_array[$key]);
			}			
		}
		echo "<br><br><br>";
		echo "hod_array<br>";
		print_r($hod_array);
		echo "<br><br><br>";
		$pce = array();
		$qt = array();
		if (!isset($_POST['pce_filename'])) {
			$_POST['pce_filename']= array();
		}
		foreach ($_POST['pce_filename'] as $key => $value) {
			$pce_id=$this->m_pce->generate_id();
			$pce[$key] = array(
				'id' => $pce_id, 
				'project_id' => $project_id, 
				'filename' => $this->m_pce->handle_file_pce($value,$pce_id), 
				'pce_no' => $_POST['pce_no'][$key], 
				'pce_des' => $_POST['pce_des'][$key], 
				'pce_amount' => (int)$_POST['pce_amount'][$key], 
				'time_create' => time(),
				'cs_sign_time' => time(), 
				);
			$this->m_pce->add_pce($pce[$key]);

			foreach ($hod_array as $h_key => $h_value) {
				foreach ($h_value as $h_key2 => $h_value2) {
					$ap_dat = array(
						'hod_usn' => $h_value2, 
						'pce_id' => $pce_id, 
					);
					$hod_tmp=$this->m_pce->get_hod_approve_pce($pce_id,$h_value2);
					if (!isset($hod_tmp->hod_usn)) {
						$this->m_pce->add_hod_approve_pce($ap_dat);
					}
					
				}				
			}


			if (isset($_POST['qt_no'][$key])) {
			
				foreach ($_POST['qt_no'][$key] as $key2 => $value2) {
					$qt[$key][$key2] = array(
						'qt_no' => $_POST['qt_no'][$key][$key2], 
						'pce_id' => $pce_id, 
						'qt_des' => (int)$_POST['qt_des'][$key][$key2], 
						'qt_cost' => (int)$_POST['qt_cost'][$key][$key2], 
						'qt_charge' => $_POST['qt_charge'][$key][$key2],
						'filename' => $this->m_outsource->handle_file($_POST['qt_filename'][$key][$key2]), 
						);
					$this->m_outsource->add_outsource($qt[$key][$key2]);
				}
			}
		}
		print_r($project_dat);
		echo "<br>";
		print_r($r_sheet);
		echo "<br>";
		print_r($pce);
		echo "<br>";
		print_r($qt);
		echo "<br>";
		redirect("cs");
	}
    public function dup_project_action(){
        $project_id=$this->m_project->generate_id();
        $project_dat = array(
            'project_id' => $project_id,
            'project_client' => $_POST['project_client'],
            'project_bu' => $_POST['project_bu'],
            'project_start' => $this->m_time->datepicker_to_unix($_POST['project_start']),
            'project_end' => $this->m_time->datepicker_to_unix($_POST['project_end']),
            'project_name' => $_POST['project_name'],
            'business_unit_id' => $_POST['business_unit_id'],
            'project_cs' => $this->user_data->username,
            );
        if (isset($_POST['submit_job'])) {
            $project_dat['status']='Proposing';
        }
        $this->m_project->add_project($project_dat);
        $this->m_company->update_company(array('is_use' => "y", ),$_POST['project_client']);
        $this->m_company->update_bu(array('is_use' => "y", ),$_POST['project_bu']);

        $r_sheet = array();
        $hod_array=array();
        if (!isset($_POST['task'])) {
                $_POST['task']=array();
            }
        foreach ($_POST['task'] as $key => $value) {
            $r_id=$this->m_Rsheet->generate_id();
            $r_sheet[$key] = array(
                'r_id' => $r_id,
                'task' => $_POST['task'][$key],
                'type' => $_POST['type'][$key],
                'approve_budget' => (int)$_POST['approve_budget'][$key],
                'project_id' => $project_id,
                'sort_order' => (float)$_POST['sort_order'][$key],
                );
            $this->m_Rsheet->add_r_sheet ($r_sheet[$key]);    
        }
        if (isset($_POST['task_old'])) {
                foreach ($_POST['task_old'] as $key => $value) {
                    $r_id=$this->m_Rsheet->generate_id();
                    $r_sheet_cur=$this->m_Rsheet->get_r_sheet_by_id($_POST['r_id_old'][$key]);    
                        $r_sheet[$key] = array(
                            'r_id' => $r_id,
                            'task' => $r_sheet_cur->task,
                            'type' => $r_sheet_cur->type,
                            'approve_budget' => $r_sheet_cur->approve_budget,
                            'project_id' => $project_id,
                            'sort_order' => $r_sheet_cur->sort_order,
                            );
                        $this->m_Rsheet->add_r_sheet ($r_sheet[$key]);
                }
        }
            
        redirect("project/edit/".$project_id);
    }

	public function check_if_base_change($post,$project_id){
		$result=false;
		$reason="";
		$project=$this->m_project->get_project_by_id($project_id);
		$r_sheet=$this->m_Rsheet->get_all_r_sheet_by_project_id($project_id);
		$pce_doc=$this->m_pce->get_all_pce_by_project_id($project_id);
		// base project check
		if ($post['project_client']!="".$project->project_client) {
			$result=true;
			$reason.="project_client <br>";
		}
		if ($post['project_bu']!="".$project->project_bu) {
			$result=true;
			$reason.="project_bu <br>";
		}
		if ($this->m_time->datepicker_to_unix($post['project_start'])!=$project->project_start) {
			$result=true;
			$reason.="project_start <br>";
		}
		if ($this->m_time->datepicker_to_unix($post['project_end'])!=$project->project_end) {
			$result=true;
			$reason.="project_end <br>";
		}
		if ($post['project_name']!="".$project->project_name) {
			$result=true;
			$reason.="project_name <br>";
		}
		if ($post['business_unit_id']!="".$project->business_unit_id) {
			$result=true;
			$reason.="business_unit_id <br>";
		}
        if ($post['account_unit_id']!="".$project->account_unit_id) {
            $result=true;
            $reason.="account_unit_id <br>";
        }
		// end base project check
		//start check r_sheet
		if (!isset($post['task_old'])) {
			$post['task_old']=array();
		}
		if (!isset($post['r_id_old'])) {
			$post['r_id_old']=array();
		}
		//echo "<br>rsheet count :".count($r_sheet)." task_old :".count($post['task_old']);
		if (count($r_sheet)!=count($post['task_old'])) {
			$result=true;
			$reason.="r_sheet count <br>";
		}
		if (isset($post['task'])) {
			$result=true;
			$reason.="new task<br>";
		}
			foreach ($post['r_id_old'] as $key => $value) {
				if ("".$r_sheet[$value]->sort_order!=$post['sort_order'][$key]) {
					$result=true;
					$reason.="sort_order change <br>";
				}
				if ("".$r_sheet[$value]->task!=$post['task_old'][$key]) {
					$result=true;
					$reason.="r_sheet task <br>";
				}
				if ("".$r_sheet[$value]->type!=$post['type_old'][$key]) {
					$result=true;
					$reason.="r_sheet type <br>";
				}
				if ("".$r_sheet[$value]->approve_budget!=$post['approve_budget_old'][$key]) {
					$result=true;
					$reason.="r_sheet approve_budget <br>";
				}
			}
		//end check r_sheet

		if (!isset($post['pce_no'])) {
			$post['pce_no']=array();
		}
		//check outsource and PCE#		
		if (count($pce_doc)!=count($post['pce_no'])) {
			$result=true;
			$reason.="pce_doc count <br>";
		}
		foreach ($pce_doc as $pkey => $pvalue) {
			if (isset($post['pce_no'][$pvalue->id])) {

				if ($post['pce_filename'][$pvalue->id]!="old__".$pvalue->filename) {
					$result=true;
					$reason.="pce_doc pce_filename <br>";
				}
				if ($post['pce_no'][$pvalue->id]!="".$pvalue->pce_no) {
					$result=true;
					$reason.="pce_doc pce_no <br>";
				}
				if ($post['pce_des'][$pvalue->id]!="".$pvalue->pce_des) {
					$result=true;
					$reason.="pce_doc pce_des <br>";
				}
				if ($post['pce_amount'][$pvalue->id]!="".$pvalue->pce_amount) {
					$result=true;
					$reason.="pce_doc pce_amount <br>";
				}

				if (isset($post['qt_no'][$pvalue->id])) {
					if (count($post['qt_no'][$pvalue->id])!=count($pvalue->outsource)) {
						$result=true;
						$reason.="outsource count <br>";
					}
					foreach ($post['qt_no'][$pvalue->id] as $qkey => $qvalue) {
						if (isset($pvalue->outsource[$qkey])) {
							if ($qvalue!="".$pvalue->outsource[$qkey]->qt_no) {
								$result=true;
								$reason.="outsource qt_no <br>";
							}
							if ($post['qt_filename'][$pvalue->id][$qkey]!="old__".$pvalue->outsource[$qkey]->filename) {
								$result=true;
								$reason.="outsource filename <br>";
							}
							if ($post['qt_des'][$pvalue->id][$qkey]!="".$pvalue->outsource[$qkey]->qt_des) {
								$result=true;
								$reason.="outsource qt_des <br>";
							}
							if ($post['qt_cost'][$pvalue->id][$qkey]!="".$pvalue->outsource[$qkey]->qt_cost) {
								$result=true;
								$reason.="outsource qt_cost <br>";
							}
							if ($post['qt_charge'][$pvalue->id][$qkey]!="".$pvalue->outsource[$qkey]->qt_charge) {
								$result=true;
								$reason.="outsource qt_charge <br>";
							}
						}else{
							$result=true;
							$reason.="outsource new <br>";
						}
					}
				}// end qt list
				if(!isset($post['qt_no'][$pvalue->id])&&count($pvalue->outsource)>0){
					$result=true;
					$reason.="outsource DELETEed <br>";
				}
			}else{
				$result=true;
				$reason.="PCE DELETEed <br>";
			}
		}
		//end check outsource
		echo $reason;
		return $result;

	}



	public function edit_project_action(){
		//print_r( $_POST );
		$task_r_sheet_del=true;
		$project_id=$_POST['project_id'];
		$is_change=$this->check_if_base_change($_POST,$project_id);
		if (isset($_POST['submit_job'])) {
			$tmp_project=$this->m_project->get_project_by_id($project_id);
			$project_dat['status']='Proposing';
			if ($tmp_project->status=="WIP"||$tmp_project->status=="Revise") {
				$project_dat['status']='Revise';
			}
			$this->m_project->update_project($project_dat,$project_id);
		}else{
			$tmp_project=$this->m_project->get_project_by_id($project_id);
			$project_dat['status']='Drafing';
			if ($tmp_project->status=="WIP"||$tmp_project->status=="Revise") {
				$project_dat['status']='Revise';
			}
			$this->m_project->update_project($project_dat,$project_id);
		}
		if ($is_change) {
			
			$project_dat = array(
				'project_client' => $_POST['project_client'],
				'project_bu' => $_POST['project_bu'],
				'project_start' => $this->m_time->datepicker_to_unix($_POST['project_start']),
				'project_end' => $this->m_time->datepicker_to_unix($_POST['project_end']),
				'project_name' => $_POST['project_name'],
				'business_unit_id' => $_POST['business_unit_id'],
                'account_unit_id' => $_POST['account_unit_id'],
				);
			if (isset($_POST['submit_job'])) {
				$project_dat['status']='Proposing';
			}else{
				$project_dat['status']='Drafing';
			}
			$this->m_project->update_project($project_dat,$project_id);
			$this->m_company->update_company(array('is_use' => "y", ),$_POST['project_client']);
			$this->m_company->update_bu(array('is_use' => "y", ),$_POST['project_bu']);
            
			$r_sheet = array();
			$hod_array=array();
			//$this->m_Rsheet->delete_r_sheet_by_project_id($project_id);
			if (!isset($_POST['task'])) {
				$_POST['task']=array();
			}
			$num_sort=0;
			foreach ($_POST['task'] as $key => $value) {

				$r_id=$this->m_Rsheet->generate_id();
				$r_sheet[$key] = array(
					'r_id' => $r_id,
					'task' => $_POST['task'][$key],
					'type' => $_POST['type'][$key],
					'approve_budget' => (int)$_POST['approve_budget'][$key],
					'project_id' => $project_id,
					'sort_order' => (float)$_POST['sort_order'][$key],
					//'sort_order' => $this->m_Rsheet->get_sort_order($project_id),
					);
				$this->m_Rsheet->add_r_sheet ($r_sheet[$key]);
				$h_r_dat=$this->m_hour_rate->get_hour_rate_by_id($_POST['type'][$key]);
				if ($h_r_dat->is_special=="n") {
					$hod_array[$key]=$this->m_Rsheet->find_hod_by_t_type($_POST['type'][$key]);
                    echo "inloop hod task new<br><br>";
                    print_r($hod_array[$key]);
				}
			}
			if (isset($_POST['task_old'])) {
				foreach ($_POST['task_old'] as $key => $value) {
					$num_sort+=1;
					//$this->m_Rsheet->delete_r_sheet_allow_hour_by_r_id($_POST['r_id_old'][$key]);
					$sum_hour=$this->m_work_sheet->get_sum_assign_hour_by_t_type_and_project_id($_POST['type_old'][$key],$project_id);
					//if ($sum_hour<=$_POST['approve_budget_old'][$key]) {// old condition : new approve budget must more than assign budget
					if ($_POST['approve_budget_old'][$key]>0) {	
						$r_sheet[$key] = array(
							'r_id' => $_POST['r_id_old'][$key],
							'task' => $_POST['task_old'][$key],
							//'type' => $_POST['type_old'][$key],
							'approve_budget' => (int)$_POST['approve_budget_old'][$key],
							//'sort_order' => $num_sort,
							'sort_order' => (float)$_POST['sort_order'][$key],
							);
						if (isset($_POST['type_old'][$key])) {
							$r_sheet[$key]['type']=$_POST['type_old'][$key];
						}
						$this->m_Rsheet->update_r_sheet($r_sheet[$key],$_POST['r_id_old'][$key]);
						$h_r_dat=$this->m_hour_rate->get_hour_rate_by_id($_POST['type_old'][$key]);
						if ($h_r_dat->is_special=="n") {
							$hod_array[$key]=$this->m_Rsheet->find_hod_by_t_type($_POST['type_old'][$key]);
                            echo "inloop hod<br>";
                            print_r($hod_array[$key]);
						}
					}
				}
			}
			if (isset($_POST['task_del'])) {
				foreach ($_POST['task_del'] as $key => $value) {
					$r_sheet_1=$this->m_Rsheet->get_r_sheet_by_id($value);
					$sum_hour=$this->m_work_sheet->get_sum_assign_hour_by_t_type_and_project_id($r_sheet_1->type,$project_id);
					if ($sum_hour>0) {
						$task_r_sheet_del=false;
					}else{
						$this->m_Rsheet->delete_r_sheet($value);
					}
					
				}
			}
			echo "hod_array<br>";
			print_r($hod_array);
			echo "<br><br><br>";
			$pce = array();
			$qt = array();
			if (!isset($_POST['pce_filename'])) {
				$_POST['pce_filename']= array();
			}
			if (isset($_POST['pce_del_list'])) {
				foreach ($_POST['pce_del_list'] as $key => $value) {
					$this->m_pce->delete_pce($value);
				}
			}
			if (isset($_POST['outsource_del_list'])) {
				foreach ($_POST['outsource_del_list'] as $key => $value) {
					$this->m_outsource->delete_outsource($value);
				}
			}
			foreach ($_POST['pce_filename'] as $key => $value) {
                $key.="";
				$pos = strpos($value, "old__");
				if ($pos === false) {
					
					$pce_id=$this->m_pce->generate_id();
					$pce[$key] = array(
						'id' => $pce_id, 
						'project_id' => $project_id, 
						'filename' => $this->m_pce->handle_file_pce($value,$pce_id), 
						'pce_no' => $_POST['pce_no'][$key], 
						'pce_des' => $_POST['pce_des'][$key], 
						'pce_amount' => (int)$_POST['pce_amount'][$key], 
						'time_create' => time(),
						'cs_sign_time' => time(),
						);
					$this->m_pce->add_pce($pce[$key]);

					$this->m_pce->delete_hod_approve_by_pce_id($pce_id);
					foreach ($hod_array as $h_key => $h_value) {
						foreach ($h_value as $h_key2 => $h_value2) {
							$ap_dat = array(
								'hod_usn' => $h_value2, 
								'pce_id' => $pce_id, 
							);
							$hod_tmp=$this->m_pce->get_hod_approve_pce($pce_id,$h_value2);
							if (!isset($hod_tmp->hod_usn)) {
								$this->m_pce->add_hod_approve_pce($ap_dat);
							}
							
						}				
					}


					if (isset($_POST['qt_no'][$key])) {
					
						foreach ($_POST['qt_no'][$key] as $key2 => $value2) {
							$qt[$key][$key2] = array(
								'qt_no' => $_POST['qt_no'][$key][$key2], 
								'pce_id' => $pce_id, 
								'qt_des' => $_POST['qt_des'][$key][$key2], 
								'qt_cost' => (int)$_POST['qt_cost'][$key][$key2], 
								'qt_charge' => (int)$_POST['qt_charge'][$key][$key2],
								'filename' => $this->m_outsource->handle_file($_POST['qt_filename'][$key][$key2]), 
								);
							$this->m_outsource->add_outsource($qt[$key][$key2]);
						}
					}
				}else{
					echo "in else PCEEE##########  ".$key;
					$pce_id=$key."";
					$pce[$key] = array(
						'fc_sign' => "not sign", 
						'csd_sign' => "not sign", 
						'fc_sign_status' => "ns", 
						'csd_sign_status' => "ns", 
						'csd_sign_time' => 0, 
						'fc_sign_time' => 0, 
                        'csd_comment' => "", 
                        'fc_comment' => "", 
						);
					$this->m_pce->update_pce($pce[$key],$pce_id);

					$this->m_pce->delete_hod_approve_by_pce_id($pce_id);
					foreach ($hod_array as $h_key => $h_value) {
						foreach ($h_value as $h_key2 => $h_value2) {
							$ap_dat = array(
								'hod_usn' => $h_value2, 
								'pce_id' => $pce_id, 
							);
							$hod_tmp=$this->m_pce->get_hod_approve_pce($pce_id,$h_value2);
							if (!isset($hod_tmp->hod_usn)) {
								$this->m_pce->add_hod_approve_pce($ap_dat);
							}
							
						}				
					}


					if (isset($_POST['qt_no'][$key])) {
						
						foreach ($_POST['qt_no'][$key] as $key2 => $value2) {
							$pos_qt = strpos($_POST['qt_filename'][$key][$key2], "old__");
							$qt[$key][$key2] = array(
									'qt_no' => $_POST['qt_no'][$key][$key2], 
									'pce_id' => $pce_id, 
									'qt_des' => $_POST['qt_des'][$key][$key2], 
									'qt_cost' => (int)$_POST['qt_cost'][$key][$key2], 
									'qt_charge' => (int)$_POST['qt_charge'][$key][$key2],
									);
							if ($pos_qt === false) {
								$qt[$key][$key2]['filename']=$this->m_outsource->handle_file($_POST['qt_filename'][$key][$key2]);
								
							}
							//echo $qt[$key][$key2]['filename'];
							if (isset($_POST['qt_id'][$key][$key2])) {
								$this->m_outsource->update_outsource($qt[$key][$key2],$_POST['qt_id'][$key][$key2]);
							}else{
								$this->m_outsource->add_outsource($qt[$key][$key2]);
							}
						}
					}


				}// end if old__
			}
			$all_oc=$this->m_oc->get_all_oc_by_project_id($project_id);
			foreach ($all_oc as $key => $value) {
				$oc_dat=array(
					"fc_sign" => "not sign",
					"status" => "ns",
                    "comment" => "",
					"fc_sign_time" => 0,
					);
				$this->m_oc->update_oc($oc_dat,$value->id);
			}
			if (isset($_POST['oc_del_list'])) {
				foreach ($_POST['oc_del_list'] as $key => $value) {
					$this->m_oc->delete_oc($value);
				}
			}
			print_r($project_dat);
			echo "<br>";
			//print_r($r_sheet);
			echo "<br>";
			print_r($pce);
			echo "<br>";
			print_r($qt);
			echo "<br>";
		}// end is change

		if (isset($_POST['is_oc_change'])) {			
			if ($_POST['is_oc_change']=="y"&&!$is_change) {
				$all_oc=$this->m_oc->get_all_oc_by_project_id($project_id);
				foreach ($all_oc as $key => $value) {
					$oc_dat=array(
						"fc_sign" => "not sign",
						"status" => "ns",
                        "comment" => "",
						"fc_sign_time" => 0,
						);
					$this->m_oc->update_oc($oc_dat,$value->id);
				}
				if (isset($_POST['oc_del_list'])) {
					foreach ($_POST['oc_del_list'] as $key => $value) {
						$this->m_oc->delete_oc($value);
					}
				}
			}
		}
        $pro_dat = array('status_bill' => $this->m_account->check_stat_bill($project_id), );
        $this->m_project->update_project($pro_dat,$project_id);
		if ($task_r_sheet_del) {
			//redirect("cs");
            ?>
            <script type="text/javascript">
            window.open("<?=site_url('cs')?>","_self");
            </script>
            <?
		}else{
			//redirect("project/edit/".$project_id);
            ?>
            <script type="text/javascript">
            window.open("<?=site_url('project/edit/'.$project_id)?>","_self");
            </script>
            <?
		}
		
		
	}

	public function outsource_payment(){
		$out_id=$this->uri->segment(3,'');
		if (isset($_POST['add_bill'])) {
			?>
			<tr>
                <td>
                    <input class="datepicker" type="text" name="time[]" value="<?=$this->m_time->unix_to_datepicker(time())?>">
                </td>
                <td>
                    <input class="" type="text" name="amount[]" >
                </td>
                <td>
                  <a href="javascript:;" class="btn btn-danger del_bill"><i class="icon-remove icon-white"></i></a>
                </td>
            </tr>
			<?
		}else if(isset($_POST['save'])){
			header('Content-Type: application/json');
			$json = array();
			$json['flag']="OK";
			//$this->m_outsource->delete_outsource_bill_by_out_id($_POST['save']);
			if (isset($_POST['time'])) {
			
				foreach ($_POST['time'] as $key => $value) {
					$data = array(
						'outsource_id' => $_POST['save'], 
						'amount' => (int)$_POST['amount'][$key], 
						'time' => $this->m_time->datepicker_to_unix($value), 
						);
					$this->m_outsource->add_outsource_bill($data);
				}
			}
			if (isset($_POST['id'])) {				
				foreach ($_POST['id'] as $key => $value) {
					$data = array(
						'amount' => (int)$_POST['amount_old'][$key], 
						'time' => $this->m_time->datepicker_to_unix($_POST['time_old'][$key]), 
						);
					$this->m_outsource->update_outsource_bill($data,$value);
				}
			}
			echo json_encode($json);
		}else if(isset($_POST['del'])){
			$out=$this->m_outsource->get_outsource_bill_by_id($_POST['del']);
			header('Content-Type: application/json');
			$json = array();
			$json['flag']="OK";
			if ($out->paid=="y") {
				$json['flag']="Cannot delete because already Paid Money";
			}else{
				$this->m_outsource->delete_outsource_bill_by_id($_POST['del']);
			}
			echo json_encode($json);
		}else{
			$data['out_id']=$out_id;
			$data['out_bill']=$this->m_outsource->get_outsource_bill_by_out_id($out_id);
			$data['out']=$this->m_outsource->get_outsource_by_id($out_id);
			$this->load->view('v_header_popup');
			$this->load->view('v_outsource_payment',$data);
			$this->load->view('v_footer');
		}
		
	}
	public function outsource_payment_view(){
			$out_id=$this->uri->segment(3,'');
			$data['out_id']=$out_id;
			$data['out_bill']=$this->m_outsource->get_outsource_bill_by_out_id($out_id);
			$data['out']=$this->m_outsource->get_outsource_by_id($out_id);
			$this->load->view('v_header_popup');
			$this->load->view('v_outsource_payment_view',$data);
			$this->load->view('v_footer');		
	}

	public function oc_payment(){
		$oc_id=$this->uri->segment(3,'');
		if (isset($_POST['add_bill'])) {
			$oc=$this->m_oc->get_oc_by_id($oc_id);
			$project=$this->m_project->get_project_by_id($oc->project_id);
			?>
			<tr>
                <td>
                    <input class="datepicker" type="text" name="time[]" value="<?=$this->m_time->unix_to_datepicker($project->project_end)?>">
                </td>
                <td>
                    <input class="" type="text" name="amount[]" >
                </td>
                <td>
                    <input class="" type="text" name="comment[]" >
                </td>
                <td>
                  <a href="javascript:;" class="btn btn-danger del_bill"><i class="icon-remove icon-white"></i></a>
                </td>
            </tr>
			<?
		}else if(isset($_POST['save'])){
			header('Content-Type: application/json');
			$json = array();
			$json['flag']="OK";
			//$this->m_oc->delete_oc_bill_by_oc_id($_POST['save']);
			if (isset($_POST['time'])) {				
				foreach ($_POST['time'] as $key => $value) {
					$data = array(
						'oc_id' => $_POST['save'], 
						'amount' => (int)$_POST['amount'][$key], 
						'comment' => $_POST['comment'][$key], 
						'time' => $this->m_time->datepicker_to_unix($value), 
						);
					$this->m_oc->add_oc_bill($data);
				}
			}
			if (isset($_POST['bil_id'])) {				
				foreach ($_POST['bil_id'] as $key => $value) {
					$data = array(
						'amount' => (int)$_POST['amount_old'][$key], 
						'comment' => $_POST['comment_old'][$key], 
						'time' => $this->m_time->datepicker_to_unix($_POST['time_old'][$key]), 
						);
					$this->m_oc->update_oc_bill($data,$value);
				}
			}
			echo json_encode($json);
		}else if(isset($_POST['del'])){
			$oc_bil=$this->m_oc->get_oc_bill_by_id($_POST['del']);
			header('Content-Type: application/json');
			$json = array();
			$json['flag']="OK";
			if ($oc_bil->collected=="y") {
				$json['flag']="Cannot delete because already receive Money";
			}else{
				$this->m_oc->delete_oc_bill_by_id($_POST['del']);
			}
			echo json_encode($json);
		}else{
			$data['oc_id']=$oc_id;
			
			$data['oc']=$this->m_oc->get_oc_by_id($oc_id);
			$data['oc_bill']=$data['oc']->oc_bill;
			$this->load->view('v_header_popup');
			$this->load->view('v_oc_payment',$data);
			$this->load->view('v_footer');
		}
		
	}
	public function oc_payment_view(){
			$oc_id=$this->uri->segment(3,'');
			$data['oc_id']=$oc_id;
			$data['oc']=$this->m_oc->get_oc_by_id($oc_id);
			$data['oc_bill']=$data['oc']->oc_bill;
			$this->load->view('v_header_popup');
			$this->load->view('v_oc_payment_view',$data);
			$this->load->view('v_footer');
	}
	public function approve_pce_view(){
			$pce_id=$this->uri->segment(3,'');
			$data['type']=$this->uri->segment(4,'');
			$data['pce_id']=$pce_id;
			$data['pce_doc']=$this->m_pce->get_pce_by_id($pce_id);
			$data['pce_doc']->hod_list=$this->m_pce->get_hod_approve_by_pce_id($pce_id);
			$csd_sign=$this->m_user->get_user_by_login_name($data['pce_doc']->csd_sign);
				$fc_sign=$this->m_user->get_user_by_login_name($data['pce_doc']->fc_sign);
				if (isset($csd_sign->firstname)) {
					$data['pce_doc']->csd_sign_name=$csd_sign->firstname." ".$csd_sign->lastname;
				}else{
					$data['pce_doc']->csd_sign_name="not sign";
				}
				if (isset($fc_sign->firstname)) {
					$data['pce_doc']->fc_sign_name=$fc_sign->firstname." ".$fc_sign->lastname;
				}else{
					$data['pce_doc']->fc_sign_name="not sign";
				}
			$data['user_data']=$this->user_data;
			$data['prem_usn_list']=$this->m_user->get_all_user_by_prem($data['type']);
			$this->load->view('v_header_popup');
			$this->load->view('v_approve_pce_view',$data);
			$this->load->view('v_footer');
	}
	public function approve_oc_view(){
			$oc_id=$this->uri->segment(3,'');
			$data['oc_id']=$oc_id;
			$data['oc']=$this->m_oc->get_oc_by_id($oc_id);
			$fc_sign=$this->m_user->get_user_by_login_name($data['oc']->fc_sign);
				if (isset($fc_sign->firstname)) {
					$data['oc']->fc_sign_name=$fc_sign->firstname." ".$fc_sign->lastname;
				}else{
					$data['oc']->fc_sign_name="not sign";
				}
			$data['user_data']=$this->user_data;
			$data['prem_usn_list']=$this->m_user->get_all_user_by_prem("fc");
			$this->load->view('v_header_popup');
			$this->load->view('v_approve_oc_view',$data);
			$this->load->view('v_footer');
	}
	public function rewrite_pce_view(){
			$pce_id=$this->uri->segment(3,'');
			$data['pce_id']=$pce_id;
			$data['pce_doc']=$this->m_pce->get_all_pce_rewrite_by_id($pce_id);
			$data['user_data']=$this->user_data;
			$this->load->view('v_header_popup');
			$this->load->view('v_rewrite_pce_view',$data);
			$this->load->view('v_footer');
	}
	public function cs_set_sign_time(){
		if(isset($_POST['save'])){
			header('Content-Type: application/json');
			$json = array();
			$json['flag']="OK";
			$time_unix=$this->m_time->datetimepicker_to_unix($_POST['time']);
			if ($time_unix<(time()-(60*60*24*300))) {
				$time_unix=time();
			}
				$data = array(
					'cs_sign_time' => $time_unix, 
					);
				$this->m_pce->update_pce($data,$_POST['save']);
			echo json_encode($json);
		}else{
			$pce_id=$this->uri->segment(3,'');
			$data['pce_id']=$pce_id;
			$data['pce_doc']=$this->m_pce->get_pce_by_id($pce_id);
			$data['user_data']=$this->user_data;
			$this->load->view('v_header_popup');
			$this->load->view('v_cs_sign_time',$data);
			$this->load->view('v_footer');
		}
	}


	public function pop_add_pce(){
			$project_id=$this->uri->segment(3,'');
			$data['project_id']=$project_id;
			$data['project']=$this->m_project->get_project_by_id($project_id);
			$data['user_data']=$this->user_data;
			$this->load->view('v_header_popup');
			$this->load->view('v_pop_add_pce',$data);
			$this->load->view('v_footer');
	}
	public function pop_add_oc(){
			$pce_id=$this->uri->segment(3,'');
			$data['pce_id']=$pce_id;
			$data['pce_doc']=$this->m_pce->get_pce_by_id($pce_id);
			$data['user_data']=$this->user_data;
			$this->load->view('v_header_popup');
			$this->load->view('v_pop_add_oc',$data);
			$this->load->view('v_footer');
	}

	/////////////////////////////////////// AJAX REGION///////////////////////////////////////
    public function check_pce_paid_billed(){
        $pce_id=$_POST['pce_id'];
        $flag=$this->m_pce->check_pce_paid_billed($pce_id);
        header('Content-Type: application/json');
        $json = array();
        $json['flag']="OK";
        if ($flag) {
            $json['flag']="ไม่สามารถลบ PCE ได้ เพราะวางบิล หรือ จ่าย supply แล้ว";
        }
        echo json_encode($json);
    }
	public function approve_pce(){
		header('Content-Type: application/json');
		$json = array();
		$json['flag']="OK";
		$type=$_POST['type'];
		$pce_id=$_POST['pce_id'];
        $project_id=$_POST['project_id'];
		$status=$_POST['status'];
		$comment=$_POST['comment'];
		$no_prem="Don't have premmision";
		$time_unix=$this->m_time->datetimepicker_to_unix($_POST['time']);
		if ($time_unix<(time()-(60*60*24*300))) {
			$time_unix=time();
		}
		if ($type=="csd") {
			if (isset($this->user_data->prem['csd']) ){
				$pce = array(
						'csd_sign' => $this->user_data->username, 
						'csd_sign_time' => $time_unix, 
						'csd_sign_status' => $status, 
						'csd_comment' => $comment, 
						);
					$this->m_pce->update_pce($pce,$pce_id);
                    $this->m_pce->sync_hod_in_pce($pce_id,$project_id);
			}else{
				$json['flag']=$no_prem;
			}
			
		}else if($type=="fc"){
			if (isset($this->user_data->prem['fc'])) {
				$pce = array(
						'fc_sign' => $this->user_data->username, 
						'fc_sign_time' => $time_unix, 
						'fc_sign_status' => $status, 
						'fc_comment' => $comment, 
						);
					$this->m_pce->update_pce($pce,$pce_id);
			}else{
				$json['flag']=$no_prem;
			}
		}else if($type=="hod"){
			if (isset($this->user_data->prem['hod'])) {
				$hod_list=$this->m_pce->get_hod_approve_by_pce_id($pce_id);
				if (isset($hod_list[$this->user_data->username])) {
					$h_ap = array(
						'approve' => $status,
						'approve_time' => $time_unix, 
						'comment' => $comment,
						);
					$this->m_pce->update_hod_approve($h_ap,$hod_list[$this->user_data->username]->id);
				}else{
					$json['flag']="you not in HOD list";
				}
			}else{
				$json['flag']=$no_prem;
			}
		}else{
				$json['flag']="invalid type";
		}
		echo json_encode($json);
	}
	public function approve_oc(){
		header('Content-Type: application/json');
		$json = array();
		$json['flag']="OK";
		$oc_id=$_POST['oc_id'];
		$status=$_POST['status'];
		$comment=$_POST['comment'];
		$time_unix=$this->m_time->datetimepicker_to_unix($_POST['time']);
		if ($time_unix<(time()-(60*60*24*300))) {
			$time_unix=time();
		}
		$no_prem="Don't have premmision";
			if (isset($this->user_data->prem['fc'])) {
				$oc_dat = array(
						'fc_sign' => $this->user_data->username, 
						'fc_sign_time' => $time_unix, 
						'status' => $status, 
						'comment' => $comment, 
						);
					$this->m_oc->update_oc($oc_dat,$oc_id);
			}else{
				$json['flag']=$no_prem;
			}
		echo json_encode($json);
	}
	public function ajax_bu_html(){
		$company_id=$_POST['company_id'];
		$bu=$this->m_company->get_bu_by_company_id($company_id);
		if (count($bu)<1) {
			?>
			<option value="no">please select company</option>
			<?
		}
		foreach ($bu as $key => $value) {

			?>
			<option value="<?=$value->id?>"><?=$value->bu_name?></option>
			<?
		}
	}
	public function ajax_add_res_html(){
		
		$hour_rate_list = $this->m_hour_rate->get_all_hour_rate();
		?>
		<tr>
            <td><input class="r-no-input" type="text" name="sort_order[]" value="1"></td>
            <td><input class="r-task-input" type="text" name="task[]"></td>
            <td>
            	<select class="type_change" name="type[]">
            	<?
            	foreach ($hour_rate_list as $key => $value) {
            		?>
            		<option id="<?=$value->id?>" data="<?=$value->hour_rate?>" value="<?=$value->id?>"><?=$value->name?></option>
            		<?
            	}
            	?>
            		
            	</select>
            </td>
            <td class="ap_budget"><input class="type_change" type="text" name="approve_budget[]"></td>
            <td></td>
            <td></td>
            <td></td>
            <td>
                <a href="javascript:;" class="btn btn-danger del_res"><i class="icon-remove icon-white"></i></a>
            </td>
        </tr>
		<?
	}
	public function ajax_add_pce_html(){ 
		$cur_time=time(); 
	?>
    <div id="pce_cur_<?echo $cur_time;?>" class="span12 no-margin-left">
        <table class="table table-noborder">
            <tr>
                <td> 
                </td>
                <td></td>
                <td style="text-align:right;"></td>
            </tr>
            <tr>
                <td class="first-ta">PCE#</td>
                <td colspan="2" style="text-align: left;">
                    <a href="<?echo site_url("media/temp/".$_POST['pce_file'])?>" target="_blank">
                        <?=$_POST['pce_no']?>&nbsp;&nbsp;<img src="<?echo site_url("img/pdf_img.png")?>"></a>
                    <input type="hidden" name="pce_filename[<?echo $cur_time;?>]" value="<?=$_POST['pce_file']?>">
                    <input type="hidden" name="pce_no[<?echo $cur_time;?>]" value="<?=$_POST['pce_no']?>">
                </td>
            </tr>
            <tr>
                <td class="first-ta">Description</td>
                <td colspan="2" style="text-align: left;">
                    <?=$_POST['pce_des']?>
                        <input type="hidden" name="pce_des[<?echo $cur_time;?>]" value="<?=$_POST['pce_des']?>">
                </td>
            </tr>
            <tr>
                <td class="first-ta">Amount</td>
                <td colspan="2" style="text-align: left;">
                    <?=number_format($_POST['pce_amount'], 2, '.', ',')?>
                        <input type="hidden" name="pce_amount[<?echo $cur_time;?>]" value="<?=$_POST['pce_amount']?>">
                </td>
            </tr>
            <tr>
                <td colspan="3" style="text-align: center;">                    
                </td>
            </tr>
        </table>
    </div>
    <div id="outsource-but_<?=$cur_time?>" class="span12 no-margin-left outsource-cls">
        <table class="table table-noborder">
            <tr>
                <td style="text-align:center">
                    <a id="out_but_<?=$cur_time?>" iden="<?=$cur_time?>" togStat="hide" href="javascript:;" class="btn btn-large btn-block toggle_outsource">Out source</a>
                </td>
            </tr>
        </table>
    </div>
    <div id="outsource_<?echo $cur_time;?>" class="span12 no-margin-left" style="display:none">
        <div class="span12 no-margin-left">
            <h5>Out Source</h5>
            <h5>PCE# <?=$_POST['pce_no']?></h5>
            <div>
                <?=$_POST['pce_des']?>
            </div>
        </div>
        <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered orange lowfont">
            <thead>
                <tr>
                    <th>QT#</th>
                    <th></th>
                    <th>description</th>
                    <th>Cost</th>
                    <th>Charge</th>
                    <th>Margin</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="table_out_<?=$cur_time?>">
                <tr id="be_out_<?=$cur_time?>">
                    <td></td>
                    <td></td>
                    <td>Grand Total</td>
                    <td class="total_cost"></td>
                    <td class="total_charge"></td>
                    <td class="total_margin"></td>
                    <td>
                        <a iden="<?=$cur_time?>" href="javascript:;" class="btn btn-success add_out_list"><i class="icon-plus icon-white"></i></a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div id="del-but_<?=$cur_time?>" class="span12 no-margin-left">
        <table class="table table-noborder">
            <tr>
                <td style="text-align:center">
                    <a id="" href="javascript:;" iden="<?=$cur_time?>" class="btn btn-danger pce_delete">DELETE PCE</a>
                </td>
            </tr>
        </table>
    </div>
    <hr id="hr_<?echo $cur_time;?>">
    <?

	}










	public function ajax_rewrite_pce_save(){
		$pce_old_id=$_POST['pce_id'];
		//set old to rewrite status
		$new_pce_id=$this->m_pce->generate_id();
		$dat_old = array(
			'rewrite_stat' => "y", 
			'rewrite_by' => $new_pce_id, 
			);
		$dat_hod_ap = array(
			'pce_id' => $new_pce_id, 
			'approve' => "ns",
			);
		$this->m_pce->update_pce($dat_old,$pce_old_id);
		$this->m_pce->update_hod_approve_by_pce_id($dat_hod_ap,$pce_old_id);
		$oc_list=$this->m_oc->get_all_oc_by_pce_id($pce_old_id);
		foreach ($oc_list as $key => $value) {
			$dat_oc_old = array('rewrite_stat' => "y", );
			$this->m_oc->update_oc($dat_oc_old,$value->id);
            $this->m_oc->delete_oc_bill_by_oc_id($value->id);
		}
		// insert new PCE#
		
		$pce_dat = array(
				'id' => $new_pce_id, 
				'project_id' => $_POST['project_id'], 
				'filename' => $this->m_pce->handle_file_pce($_POST['pce_file'],$new_pce_id), 
				'pce_no' => $_POST['pce_no'], 
				'pce_des' => $_POST['pce_des'], 
				'pce_amount' => (int)$_POST['pce_amount'], 
				'time_create' => time(),
				'cs_sign_time' => time(),
				);
			$this->m_pce->add_pce($pce_dat);
		if (isset($_POST['qt_no'][$pce_old_id])) {					
			foreach ($_POST['qt_no'][$pce_old_id] as $key2 => $value2) {
				$pos_qt = strpos($_POST['qt_filename'][$pce_old_id][$key2], "old__");
				$qt= array(
					'qt_no' => $_POST['qt_no'][$pce_old_id][$key2], 
					'pce_id' => $new_pce_id, 
					'qt_des' => $_POST['qt_des'][$pce_old_id][$key2], 
					'qt_cost' => (int)$_POST['qt_cost'][$pce_old_id][$key2], 
					'qt_charge' => (int)$_POST['qt_charge'][$pce_old_id][$key2],
					);
				if ($pos_qt === false) {
					$qt['filename']=$this->m_outsource->handle_file($_POST['qt_filename'][$pce_old_id][$key2]);					
				}else{
					//$qt['filename']="no_file";
				}
				$this->m_outsource->update_outsource($qt,$_POST['qt_id'][$pce_old_id][$key2]);
			}
		}	
		$pce=$this->m_pce->get_pce_by_id($new_pce_id);
		$cur_time=$pce->id;
		$user_data=$this->user_data;
		if (isset($_POST['from_oc'])) {
			$oc_1_list=$this->m_oc->get_all_oc_by_pce_id($pce->id);
			?>
										<div id="pce_cur_<?echo $cur_time;?>" class="span12 no-margin-left pce-hold">
                                            <div class="span6" id="pce_inner_<?echo $cur_time;?>">
                                            <table class="table table-noborder">
                                                <tr>
                                                    <td> <a class="btn btn-info fancybox" data-fancybox-type="iframe" href="<?echo site_url("project/cs_set_sign_time/".$cur_time);?>"  ><i class="icon-pencil icon-white"></i></a>
                                                    <?
                                                    $have_rewrite=$this->m_pce->get_pce_rewrite_child_by_id($pce->id);
                                                    if (isset($have_rewrite->id)) {
                                                        ?>
                                                         <a class="btn btn-warning fancybox" data-fancybox-type="iframe" href="<?echo site_url("project/rewrite_pce_view/".$cur_time);?>"  ><i class="icon-list icon-white"></i></a>
                                                        <?
                                                    }
                                                    ?> </td>
                                                    <td></td><td style="text-align:right;"><a id="" href="javascript:;" iden="<?=$cur_time?>" class="btn-atom btn-atom-warning pce_rewrite">Revise</a></td>
                                                </tr>
                                                <tr><td class="first-ta">PCE#</td><td colspan="2" style="text-align: left;">
                                                <a href="<?echo site_url("project/view_sign_pce/".$pce->id)?>" target="_blank"><?=$pce->pce_no?>&nbsp;&nbsp;<img src="<?echo site_url("img/pdf_img.png")?>"></a>
                                                <input type="hidden" name="pce_filename[<?echo $cur_time;?>]" value="old__<?=$pce->filename?>">
                                                <input type="hidden" name="pce_no[<?echo $cur_time;?>]" value="<?=$pce->pce_no?>">
                                                </td></tr>
                                                <tr><td class="first-ta">Description</td><td colspan="2" style="text-align: left;">
                                                     <?=$pce->pce_des?>
                                                    <input type="hidden" name="pce_des[<?echo $cur_time;?>]" value="<?=$pce->pce_des?>">
                                                </td></tr>
                                                <tr><td class="first-ta">Amount</td><td colspan="2" style="text-align: left;">
                                                    <?=number_format($pce->pce_amount, 2, '.', ',')?>
                                                <input type="hidden" name="pce_amount[<?echo $cur_time;?>]" value="<?=$pce->pce_amount?>">
                                                </td></tr>
                                                <td></td><td colspan="2" style="text-align: left;">
                                                    <?
                                                    $hod_all_approve=true;
                                                    $hod_reject_flag=false;
                                                    foreach ($pce->hod_list as $hlistkey => $hlistvalue) {
                                                        if($hlistvalue->approve=="ns"){
                                                            $hod_all_approve=false;
                                                        }
                                                        if($hlistvalue->approve=="n"){
                                                            $hod_reject_flag=true;
                                                        }
                                                    }
                                                   if ($pce->csd_sign_status=="y") {                                                    
                                                        ?>
                                                        <a id="csd_a_<?echo $cur_time;?>" class="btn btn-inverse fancybox" data-fancybox-type="iframe" href="<?echo site_url("project/approve_pce_view/".$cur_time."/csd");?>"  ><i class="icon-ok icon-white"></i>CSD </a>
                                                        <?
                                                    }else if($pce->csd_sign_status=="n"){
                                                        ?>
                                                        <a id="csd_a_<?echo $cur_time;?>" class="btn btn-danger fancybox" data-fancybox-type="iframe" href="<?echo site_url("project/approve_pce_view/".$cur_time."/csd");?>"  ><i class="icon-remove icon-white"></i>CSD </a>
                                                        <?
                                                    }else{
                                                        ?>
                                                        <a id="csd_a_<?echo $cur_time;?>" class="btn fancybox" data-fancybox-type="iframe" href="<?echo site_url("project/approve_pce_view/".$cur_time."/csd");?>" >CSD </a>
                                                        <?
                                                    }
                                                    if($hod_reject_flag){
                                                        ?>
                                                        <a id="hod_a_<?echo $cur_time;?>" class="btn btn-danger fancybox" data-fancybox-type="iframe" href="<?echo site_url("project/approve_pce_view/".$cur_time."/hod");?>" ><i class="icon-remove icon-white"></i>HOD </a>
                                                        <?
                                                    }else if (isset($pce->hod_list[$user_data->username])&&$pce->hod_list[$user_data->username]->approve=="y") {
                                                        ?>
                                                        <a id="hod_a_<?echo $cur_time;?>" class="btn btn-inverse fancybox" data-fancybox-type="iframe" href="<?echo site_url("project/approve_pce_view/".$cur_time."/hod");?>" ><i class="icon-ok icon-white"></i>HOD </a>
                                                        <?
                                                    }else if($hod_all_approve){
                                                        ?>
                                                        <a id="hod_a_<?echo $cur_time;?>" class="btn btn-inverse fancybox" data-fancybox-type="iframe" href="<?echo site_url("project/approve_pce_view/".$cur_time."/hod");?>" ><i class="icon-ok icon-white"></i>HOD </a>
                                                        <?
                                                    }else{
                                                        ?>
                                                        <a id="hod_a_<?echo $cur_time;?>" class="btn fancybox" data-fancybox-type="iframe" href="<?echo site_url("project/approve_pce_view/".$cur_time."/hod");?>">HOD </a>
                                                        <?
                                                    }
                                                    
                                                    if ($pce->fc_sign_status=="y") {                                                    
                                                        ?>
                                                        <a id="fc_a_<?echo $cur_time;?>" class="btn btn-inverse fancybox" data-fancybox-type="iframe" href="<?echo site_url("project/approve_pce_view/".$cur_time."/fc");?>"  ><i class="icon-ok icon-white"></i>FC </a>
                                                        <?
                                                    }else if($pce->fc_sign_status=="n"){
                                                        ?>
                                                        <a id="fc_a_<?echo $cur_time;?>" class="btn btn-danger fancybox" data-fancybox-type="iframe" href="<?echo site_url("project/approve_pce_view/".$cur_time."/fc");?>"  ><i class="icon-remove icon-white"></i>FC </a>
                                                        <?
                                                    }else{
                                                        ?>
                                                        <a id="fc_a_<?echo $cur_time;?>" class="btn fancybox" data-fancybox-type="iframe" href="<?echo site_url("project/approve_pce_view/".$cur_time."/fc");?>" >FC </a>
                                                        <?
                                                    }
                                                    ?>
                                                </td></tr>
                                            </table>
                                            </div>
                                            <div id="oc_region_<?=$cur_time?>" class="span6 oc_hold_special">
                                            <?
                                            foreach ($oc_1_list as $key => $oc) {
                                                $oc_id=$oc->id
                                                ?>

                                                    <div id="oc_cur_<?echo $oc_id;?>" class="span12 no-margin-left">
                                                    <table class="table table-noborder">
                                                        <tr>
                                                            <td colspan="2">
                                                                <a class="btn-atom btn-atom-success fancybox" data-fancybox-type="iframe" href="<?=site_url("project/oc_payment/".$oc_id)?>">Billing due <i class="icon-plus icon-white"></i></a>
                                                                <?
                                                                if ($oc->is_done=="n") {
                                                                    ?>
                                                                    <a id="" href="javascript:;" iden="<?=$oc_id?>" stat="Done" class="btn-atom btn-atom-info oc_done">Not Done</a>
                                                                    <?
                                                                }else{
                                                                    ?>
                                                                    <a id="" href="javascript:;" iden="<?=$oc_id?>" stat="Undone" class="btn-atom btn-atom-info oc_done">Done</a>
                                                                    <?
                                                                }
                                                                ?>
                                                            </td>
                                                            <td style="text-align:right;">
                                                                <a id="" href="javascript:;" iden="<?=$oc_id?>" class="btn-atom btn-atom-danger oc_delete">DELETE</a>
                                                            </td>
                                                            </tr>
                                                    </table>
                                                    <table class="table table-noborder">
                                                            <tr><td class="first-ta">OC#</td><td colspan="2" style="text-align: left;">
                                                            <a href="<?echo site_url("project/view_sign_oc/".$oc->id)?>" target="_blank"><?=$oc->oc_no?></a>&nbsp;|&nbsp;
                                                            <a href="<?echo site_url("media/real_pdf/".$oc->filename_pce)?>" target="_blank"><?=$oc->pce->pce_no?></a>   
                                                            </td></tr>
                                                            <tr><td class="first-ta">Description#</td><td colspan="2" style="text-align: left;">
                                                            <?=$oc->oc_des?>
                                                            </td></tr>
                                                            <tr><td class="first-ta">Amount#</td><td colspan="2" style="text-align: left;">
                                                            <?=number_format($oc->oc_amount, 2, '.', ',')?>
                                                            </td></tr>
                                                            <td></td><td colspan="2" style="text-align: left;">
                                                                <?
                                                                    if ($oc->status=="y") {
                                                                           
                                                                            ?>
                                                                            <a id="fc_oc_<?echo $oc_id;?>" href="<?echo site_url("project/approve_oc_view/".$oc_id);?>" class="btn btn-inverse fancybox" data-fancybox-type="iframe"><i class="icon-ok icon-white"></i>FC </a>
                                                                            <?
                                                                        }else if($oc->status=="n"){
                                                                            ?>
                                                                            <a id="fc_oc_<?echo $oc_id;?>" href="<?echo site_url("project/approve_oc_view/".$oc_id);?>" class="btn btn-danger fancybox" data-fancybox-type="iframe"><i class="icon-remove icon-white"></i>FC </a>
                                                                            <?
                                                                        }else{
                                                                            ?>
                                                                            <a id="fc_oc_<?echo $oc_id;?>" href="<?echo site_url("project/approve_oc_view/".$oc_id);?>" class="btn fancybox" data-fancybox-type="iframe">FC </a>                 
                                                                            <?
                                                                        }
                                                                    ?>
                                                            </td></tr>
                                                        <tr>
                                                            <td colspan="3"><hr id="hr_<?echo $oc_id;?>"></td>
                                                        </tr>
                                                    </table>    
                                                    </div>                                                    
                                                    
                                                <?
                                            }
                                            ?>
                                            <div class="span12 no-margin-left" id="before_add_oc_but_<?=$cur_time?>">
                                                <table class="table table-noborder">
                                                    <tr>
                                                        <td style="text-align:center">
                                                           <a class="btn btn-success fancybox" data-fancybox-type="iframe" href="<?echo site_url("project/pop_add_oc/".$pce->id);?>"  >Add OC/IOC</a>  
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                            </div>
                                        </div>
                                        <div id="outsource-but_<?=$cur_time?>" class="span12 no-margin-left outsource-cls">
                                            <table class="table table-noborder">
                                                <tr>
                                                    <td style="text-align:center">
                                                       <a id="out_but_<?=$cur_time?>" iden="<?=$cur_time?>" togStat="hide" href="javascript:;" class="btn btn-large btn-block toggle_outsource">Out source</a>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div id="outsource_<?echo $cur_time;?>" class="span12 no-margin-left" style="display:none">
                                            <div class="span12 no-margin-left">
                                                <h5>Out Source</h5>
                                                <h5>PCE# <?=$pce->pce_no?></h5>
                                                <div>
                                                    <?=$pce->pce_des?>
                                                </div>
                                            </div>
                                            <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered orange lowfont">
                                                <thead>
                                                    <tr>
                                                        <th>QT#</th>
                                                        <th></th>
                                                        <th>description</th>
                                                        <th>Cost</th>
                                                        <th>Charge</th>
                                                        <th>Margin</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="table_out_<?=$cur_time?>">
                                                <?
                                                foreach ($pce->outsource as $outsource_key => $outsource) {
                                                    $up_time=$outsource->id;
                                                    ?>

                                                    <tr class="out_<?=$cur_time?>" iden="<?=$cur_time?>">
                                                        <td><input class="" type="text" name="qt_no[<?=$cur_time?>][]" value="<?=$outsource->qt_no?>">
                                                        <input class="" type="hidden" name="qt_id[<?=$cur_time?>][]" value="<?=$outsource->id?>">
                                                        </td>
                                                        <td>
                                                            <div class="control-group">
                                                                <?
                                                                if ($outsource->filename!="no_file") {
                                                                    ?>
                                                                    <a id="qt_view_<?=$up_time?>" href="<?echo site_url("media/real_pdf/".$outsource->filename)?>" target="_blank">ดูไฟล์</a>
                                                                    <?
                                                                }else{
                                                                    ?>
                                                                    <a id="qt_view_<?=$up_time?>" href="" target="_blank"></a>
                                                                    <?
                                                                }
                                                                ?>
                                                                
                                                                <span class="btn btn-success fileinput-button">
                                                                    
                                                                    <i class="glyphicon glyphicon-plus"></i>
                                                                    <span id="but_upload_<?=$cur_time?>">เลือกไฟล์ PDF</span>
                                                                     <!-- The file input field used as target for the file upload widget -->
                                                                    <input id="qtupload_<?=$up_time?>" type="file">
                                                                    <input id="qt_file_<?=$up_time?>" type="hidden" name="qt_filename[<?=$cur_time?>][]" value="old__<?=$outsource->filename?>">
                                                                </span>
                                                                <script type="text/javascript">
                                                                    $('#qtupload_<?=$up_time?>').fileupload({
                                                                            previewThumbnail: false,
                                                                            url: '<?php echo site_url('upload_handler/pdf '); ?>',
                                                                            dataType: 'json',
                                                                            beforeSend: function() {
                                                                                $('#but_upload_<?=$up_time?>').html("Please wait");
                                                                                $('#qtupload_<?=$up_time?>').attr("disabled","");
                                                                            },
                                                                            done: function(e, data) {
                                                                                //console.log(data);

                                                                                $.each(data.result.files, function(index, file) {
                                                                                    //console.log(file);
                                                                                    if (file.error == "File is too big") {
                                                                                        alert("File is too big exceed 100 MB");
                                                                                        $("#qt_file_<?=$up_time?>").val("");
                                                                                    }else if (file.error == "Filetype not allowed") {
                                                                                        alert("Filetype not allowed");
                                                                                       $("#qt_file_<?=$up_time?>").val("");
                                                                                    } else {
                                                                                        alert("Upload Complete file " + file.name);
                                                                                        $("#qt_file_<?=$up_time?>").val(file.name);
                                                                                        $('#qt_view_<?=$up_time?>').attr("href",'<?echo site_url("media/temp/")?>/'+file.name);
                                                                                        $('#qt_view_<?=$up_time?>').html("ดูไฟล์");
                                                                                    }
                                                                                    $('#but_upload_<?=$up_time?>').html("เลือกไฟล์ PDF");

                                                                                    $('#qtupload_<?=$up_time?>').removeAttr("disabled");
                                                                                });

                                                                            },
                                                                            progressall: function(e, data) {
                                                                            }
                                                                        }).prop('disabled', !$.support.fileInput)
                                                                        .parent().addClass($.support.fileInput ? undefined : 'disabled');
                                                                </script>
                                                            </div>
                                                        </td>
                                                        <td><input class="" type="text" name="qt_des[<?=$cur_time?>][]" value="<?=$outsource->qt_des?>"></td>
                                                        <td><input class="out_change" type="text" name="qt_cost[<?=$cur_time?>][]" value="<?=$outsource->qt_cost?>"></td>
                                                        <td><input class="out_change" type="text" name="qt_charge[<?=$cur_time?>][]" value="<?=$outsource->qt_charge?>"></td>
                                                        <td class="out_margin"><?echo number_format("".($outsource->qt_charge-$outsource->qt_cost)/$outsource->qt_cost*100,2);?></td>
                                                        <td>
                                                            <a iden="<?=$up_time?>" href="javascript:;" class="btn btn-danger del_outlist"><i class="icon-remove icon-white"></i></a>                                                                 
                                                            <a class="btn btn-success fancybox" data-fancybox-type="iframe" href="<?=site_url("project/outsource_payment/".$up_time)?>"><i class="icon-plus icon-white"></i>Pay date</a>               
                                                        </td>
                                                    </tr>
                                                    <?
                                                }
                                                ?>
                                                    <tr id="be_out_<?=$cur_time?>">
                                                        <td></td>
                                                        <td></td>
                                                        <td>Grand Total</td>
                                                        <td class="total_cost"></td>
                                                        <td class="total_charge"></td>
                                                        <td class="total_margin"></td>
                                                        <td>
                                                            <a iden="<?=$cur_time?>" href="javascript:;" class="btn btn-success add_out_list"><i class="icon-plus icon-white"></i></a>                                                            
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div id="del-but_<?=$cur_time?>" class="span12 no-margin-left">
                                            <table class="table table-noborder">
                                                <tr>
                                                    <td style="text-align:center">
                                                       <a id="" href="javascript:;" iden="<?=$cur_time?>" class="btn btn-danger pce_delete">DELETE PCE</a>   
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                        <script type="text/javascript">
                                        cal_outSource('<?=$cur_time?>');
                                        </script>
                                        <hr id="hr_<?echo $cur_time;?>">
			<?
		}else{
                                    ?>

                                        <div id="pce_cur_<?echo $cur_time;?>" class="span12 no-margin-left pce-hold">
                                        <table class="table table-noborder">
                                                <tr>
                                                    <td> <a class="btn btn-info fancybox" data-fancybox-type="iframe" href="<?echo site_url("project/cs_set_sign_time/".$cur_time);?>"  ><i class="icon-pencil icon-white"></i></a>
                                                    <?
                                                    $have_rewrite=$this->m_pce->get_pce_rewrite_child_by_id($pce->id);
                                                    if (isset($have_rewrite->id)) {
                                                        ?>
                                                         <a class="btn btn-warning fancybox" data-fancybox-type="iframe" href="<?echo site_url("project/rewrite_pce_view/".$cur_time);?>"  ><i class="icon-list icon-white"></i></a>
                                                        <?
                                                    }
                                                    ?> </td>
                                                    <td></td><td style="text-align:right;"><a id="" href="javascript:;" iden="<?=$cur_time?>" class="btn-atom btn-atom-warning pce_rewrite">Revise</a></td>
                                                </tr>
                                                <tr><td class="first-ta">PCE#</td><td colspan="2" style="text-align: left;">
                                                <a href="<?echo site_url("project/view_sign_pce/".$pce->id)?>" target="_blank"><?=$pce->pce_no?>&nbsp;&nbsp;<img src="<?echo site_url("img/pdf_img.png")?>"></a>
                                                <input type="hidden" name="pce_filename[<?echo $cur_time;?>]" value="old__<?=$pce->filename?>">
                                                <input type="hidden" name="pce_no[<?echo $cur_time;?>]" value="<?=$pce->pce_no?>">
                                                </td></tr>
                                                <tr><td class="first-ta">Description</td><td colspan="2" style="text-align: left;">
                                                     <?=$pce->pce_des?>
                                                    <input type="hidden" name="pce_des[<?echo $cur_time;?>]" value="<?=$pce->pce_des?>">
                                                </td></tr>
                                                <tr><td class="first-ta">Amount</td><td colspan="2" style="text-align: left;">
                                                    <?=number_format($pce->pce_amount, 2, '.', ',')?>
                                                <input type="hidden" name="pce_amount[<?echo $cur_time;?>]" value="<?=$pce->pce_amount?>">
                                                </td></tr>
                                                <td></td><td colspan="2" style="text-align: left;">
                                                    <?
                                                    $hod_all_approve=true;
                                                    $hod_reject_flag=false;
                                                    foreach ($pce->hod_list as $hlistkey => $hlistvalue) {
                                                        if($hlistvalue->approve=="ns"){
                                                            $hod_all_approve=false;
                                                        }
                                                        if($hlistvalue->approve=="n"){
                                                            $hod_reject_flag=true;
                                                        }
                                                    }
                                                   if ($pce->csd_sign_status=="y") {                                                    
                                                        ?>
                                                        <a id="csd_a_<?echo $cur_time;?>" class="btn btn-inverse fancybox" data-fancybox-type="iframe" href="<?echo site_url("project/approve_pce_view/".$cur_time."/csd");?>"  ><i class="icon-ok icon-white"></i>CSD </a>
                                                        <?
                                                    }else if($pce->csd_sign_status=="n"){
                                                        ?>
                                                        <a id="csd_a_<?echo $cur_time;?>" class="btn btn-danger fancybox" data-fancybox-type="iframe" href="<?echo site_url("project/approve_pce_view/".$cur_time."/csd");?>"  ><i class="icon-remove icon-white"></i>CSD </a>
                                                        <?
                                                    }else{
                                                        ?>
                                                        <a id="csd_a_<?echo $cur_time;?>" class="btn fancybox" data-fancybox-type="iframe" href="<?echo site_url("project/approve_pce_view/".$cur_time."/csd");?>" >CSD </a>
                                                        <?
                                                    }
                                                    if($hod_reject_flag){
                                                        ?>
                                                        <a id="hod_a_<?echo $cur_time;?>" class="btn btn-danger fancybox" data-fancybox-type="iframe" href="<?echo site_url("project/approve_pce_view/".$cur_time."/hod");?>" ><i class="icon-remove icon-white"></i>HOD </a>
                                                        <?
                                                    }else if (isset($pce->hod_list[$user_data->username])&&$pce->hod_list[$user_data->username]->approve=="y") {
                                                        ?>
                                                        <a id="hod_a_<?echo $cur_time;?>" class="btn btn-inverse fancybox" data-fancybox-type="iframe" href="<?echo site_url("project/approve_pce_view/".$cur_time."/hod");?>" ><i class="icon-ok icon-white"></i>HOD </a>
                                                        <?
                                                    }else if($hod_all_approve){
                                                        ?>
                                                        <a id="hod_a_<?echo $cur_time;?>" class="btn btn-inverse fancybox" data-fancybox-type="iframe" href="<?echo site_url("project/approve_pce_view/".$cur_time."/hod");?>" ><i class="icon-ok icon-white"></i>HOD </a>
                                                        <?
                                                    }else{
                                                        ?>
                                                        <a id="hod_a_<?echo $cur_time;?>" class="btn fancybox" data-fancybox-type="iframe" href="<?echo site_url("project/approve_pce_view/".$cur_time."/hod");?>">HOD </a>
                                                        <?
                                                    }
                                                    
                                                    if ($pce->fc_sign_status=="y") {                                                    
                                                        ?>
                                                        <a id="fc_a_<?echo $cur_time;?>" class="btn btn-inverse fancybox" data-fancybox-type="iframe" href="<?echo site_url("project/approve_pce_view/".$cur_time."/fc");?>"  ><i class="icon-ok icon-white"></i>FC </a>
                                                        <?
                                                    }else if($pce->fc_sign_status=="n"){
                                                        ?>
                                                        <a id="fc_a_<?echo $cur_time;?>" class="btn btn-danger fancybox" data-fancybox-type="iframe" href="<?echo site_url("project/approve_pce_view/".$cur_time."/fc");?>"  ><i class="icon-remove icon-white"></i>FC </a>
                                                        <?
                                                    }else{
                                                        ?>
                                                        <a id="fc_a_<?echo $cur_time;?>" class="btn fancybox" data-fancybox-type="iframe" href="<?echo site_url("project/approve_pce_view/".$cur_time."/fc");?>" >FC </a>
                                                        <?
                                                    }
                                                    ?>
                                                </td></tr>
                                            </table>       
                                        </div>
                                        <div id="outsource-but_<?=$cur_time?>" class="span12 no-margin-left outsource-cls">
                                            <table class="table table-noborder">
                                                <tr>
                                                    <td style="text-align:center">
                                                       <a id="out_but_<?=$cur_time?>" iden="<?=$cur_time?>" togStat="hide" href="javascript:;" class="btn btn-large btn-block toggle_outsource">Out source</a>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div id="outsource_<?echo $cur_time;?>" class="span12 no-margin-left" style="display:none">
                                            <div class="span12 no-margin-left">
                                                <h5>Out Source</h5>
                                                <h5>PCE# <?=$pce->pce_no?></h5>
                                                <div>
                                                    <?=$pce->pce_des?>
                                                </div>
                                            </div>
                                            <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered orange lowfont">
                                                <thead>
                                                    <tr>
                                                        <th>QT#</th>
                                                        <th></th>
                                                        <th>description</th>
                                                        <th>Cost</th>
                                                        <th>Charge</th>
                                                        <th>Margin</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="table_out_<?=$cur_time?>">
                                                <?
                                                foreach ($pce->outsource as $outsource_key => $outsource) {
                                                    $up_time=$outsource->id;
                                                    ?>

                                                    <tr class="out_<?=$cur_time?>" iden="<?=$cur_time?>">
                                                        <td><input class="" type="text" name="qt_no[<?=$cur_time?>][]" value="<?=$outsource->qt_no?>">
                                                        <input class="" type="hidden" name="qt_id[<?=$cur_time?>][]" value="<?=$outsource->id?>">
                                                        </td>
                                                        <td>
                                                            <div class="control-group">
                                                            <?
                                                                if ($outsource->filename!="no_file") {
                                                                    ?>
                                                                    <a id="qt_view_<?=$up_time?>" href="<?echo site_url("media/real_pdf/".$outsource->filename)?>" target="_blank">ดูไฟล์</a>
                                                                    <?
                                                                }else{
                                                                    ?>
                                                                    <a id="qt_view_<?=$up_time?>" href="" target="_blank"></a>
                                                                    <?
                                                                }
                                                                ?>
                                                                <span class="btn btn-success fileinput-button">
                                                                    
                                                                    <i class="glyphicon glyphicon-plus"></i>
                                                                    <span id="but_upload_<?=$cur_time?>">เลือกไฟล์ PDF</span>
                                                                     <!-- The file input field used as target for the file upload widget -->
                                                                    <input id="qtupload_<?=$up_time?>" type="file">
                                                                    <input id="qt_file_<?=$up_time?>" type="hidden" name="qt_filename[<?=$cur_time?>][]" value="old__<?=$outsource->filename?>">
                                                                </span>
                                                                <script type="text/javascript">
                                                                    $('#qtupload_<?=$up_time?>').fileupload({
                                                                            previewThumbnail: false,
                                                                            url: '<?php echo site_url('upload_handler/pdf '); ?>',
                                                                            dataType: 'json',
                                                                            beforeSend: function() {
                                                                                $('#but_upload_<?=$up_time?>').html("Please wait");
                                                                                $('#qtupload_<?=$up_time?>').attr("disabled","");
                                                                            },
                                                                            done: function(e, data) {
                                                                                //console.log(data);

                                                                                $.each(data.result.files, function(index, file) {
                                                                                    //console.log(file);
                                                                                    if (file.error == "File is too big") {
                                                                                        alert("File is too big exceed 100 MB");
                                                                                        $("#qt_file_<?=$up_time?>").val("");
                                                                                    }else if (file.error == "Filetype not allowed") {
                                                                                        alert("Filetype not allowed");
                                                                                       $("#qt_file_<?=$up_time?>").val("");
                                                                                    } else {
                                                                                        alert("Upload Complete file " + file.name);
                                                                                        $("#qt_file_<?=$up_time?>").val(file.name);
                                                                                        $('#qt_view_<?=$up_time?>').attr("href",'<?echo site_url("media/temp/")?>/'+file.name);
                                                                                        $('#qt_view_<?=$up_time?>').html("ดูไฟล์");
                                                                                    }
                                                                                    $('#but_upload_<?=$up_time?>').html("เลือกไฟล์ PDF");

                                                                                    $('#qtupload_<?=$up_time?>').removeAttr("disabled");
                                                                                });

                                                                            },
                                                                            progressall: function(e, data) {
                                                                            }
                                                                        }).prop('disabled', !$.support.fileInput)
                                                                        .parent().addClass($.support.fileInput ? undefined : 'disabled');
                                                                </script>
                                                            </div>
                                                        </td>
                                                        <td><input class="" type="text" name="qt_des[<?=$cur_time?>][]" value="<?=$outsource->qt_des?>"></td>
                                                        <td><input class="out_change" type="text" name="qt_cost[<?=$cur_time?>][]" value="<?=$outsource->qt_cost?>"></td>
                                                        <td><input class="out_change" type="text" name="qt_charge[<?=$cur_time?>][]" value="<?=$outsource->qt_charge?>"></td>
                                                        <td class="out_margin"><?echo number_format("".($outsource->qt_charge-$outsource->qt_cost)/$outsource->qt_cost*100,2);?></td>
                                                        <td>
                                                            <a iden="<?=$up_time?>" href="javascript:;" class="btn btn-danger del_outlist"><i class="icon-remove icon-white"></i></a>                          
                                                        </td>
                                                    </tr>
                                                    <?
                                                }
                                                ?>
                                                    <tr id="be_out_<?=$cur_time?>">
                                                        <td></td>
                                                        <td></td>
                                                        <td>Grand Total</td>
                                                        <td class="total_cost"></td>
                                                        <td class="total_charge"></td>
                                                        <td class="total_margin"></td>
                                                        <td>
                                                            <a iden="<?=$cur_time?>" href="javascript:;" class="btn btn-success add_out_list"><i class="icon-plus icon-white"></i></a>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div id="del-but_<?=$cur_time?>" class="span12 no-margin-left">
                                            <table class="table table-noborder">
                                                <tr>
                                                    <td style="text-align:center">
                                                       <a id="" href="javascript:;" iden="<?=$cur_time?>" class="btn btn-danger pce_delete">DELETE PCE</a>   
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                        <script type="text/javascript">
                                        cal_outSource('<?=$cur_time?>');
                                        </script>
                                        <hr id="hr_<?echo $cur_time;?>">
                                    <?
                                }
	}













	public function ajax_rewrite_pce_html(){
		$pce_id=$_POST['pce_id'];
		$pce=$this->m_pce->get_pce_by_id($pce_id);
		$cur_time=$pce->id;
		if (isset($_POST['from_oc'])) {
                                    $oc_1_list=$this->m_oc->get_all_oc_by_pce_id($pce->id);
                                    ?>

                                        <div id="pce_cur_<?echo $cur_time;?>" class="span12 no-margin-left pce-hold">
                                        <div id="" class="span6">
                                            <table class="table table-noborder">
                                                <tr>
                                                    <td></td>
                                                    <td></td><td style="text-align:right;">
                                                    	<a id="" href="javascript:;" iden="<?=$cur_time?>" class="btn btn-danger pce_rewrite_cancel">Cancel</a>
                                                		<a id="" href="javascript:;" iden="<?=$cur_time?>" class="btn btn-info pce_rewrite_ok">OK</a>
                                                    </td>
                                                </tr>
                                                <tr><td class="first-ta">PCE#</td><td colspan="2" style="text-align: left;">
                                                <input class="form-control" type="text" id="pce_no_<?echo $cur_time;?>" value="<?=$pce->pce_no?>">
                                                </td></tr>
                                                <tr>
                                                	<td class="first-ta">
                                                		File
                                                	</td>
                                                	<td colspan="2">
                                                		<div class="control-group">
				                                            <span class="btn btn-success fileinput-button">
				                                                            <i class="glyphicon glyphicon-plus"></i>
				                                                            <span>เลือกไฟล์ PDF</span> 
				                                            <!-- The file input field used as target for the file upload widget -->
				                                            <input id="fileupload_rewrite_<?echo $cur_time;?>" type="file" name="files">
				                                            <input id="temp_f_name_rewrite_<?echo $cur_time;?>" type="hidden" value="old">
				                                            </span> <a href="<?echo site_url("project/view_sign_pce/".$pce->id)?>" target="_blank"><?=$pce->pce_no?></a>
				                                            <br>
				                                            <br>
				                                            <!-- The global progress bar -->
				                                            <div id="progress_rewrite_<?echo $cur_time;?>" class="progress">
				                                                <div class="progress-bar progress-bar-success"></div>
				                                            </div>
				                                        </div>
				                                        <script type="text/javascript">
                                                                    $('#fileupload_rewrite_<?echo $cur_time;?>').fileupload({
                                                                            previewThumbnail: false,
                                                                            url: '<?php echo site_url('upload_handler/pdf '); ?>',
                                                                            dataType: 'json',
                                                                            beforeSend: function() {
                                                                            	$('#progress_rewrite_<?echo $cur_time;?> .progress-bar').css(
																	                    'width',
																	                    '10%'
																	                );
                                                                            },
                                                                            done: function(e, data) {
                                                                                //console.log(data);

                                                                                $.each(data.result.files, function(index, file) {
                                                                                    //console.log(file);
                                                                                    if (file.error == "File is too big") {
                                                                                        alert("File is too big exceed 100 MB");
                                                                                        $("#temp_f_name_rewrite_<?echo $cur_time;?>").val("old");
                                                                                    }else if (file.error == "Filetype not allowed") {
                                                                                        alert("Filetype not allowed");
                                                                                       $("#temp_f_name_rewrite_<?echo $cur_time;?>").val("old");
                                                                                    } else {
                                                                                        alert("Upload Complete file " + file.name);
                                                                                        $("#temp_f_name_rewrite_<?echo $cur_time;?>").val(file.name);
                                                                                    }
                                                                                });

                                                                            },
                                                                            progressall: function(e, data) {
                                                                            	var progress = parseInt(data.loaded / data.total * 100, 10);
																                $('#progress_rewrite_<?echo $cur_time;?> .progress-bar').css(
																                    'width',
																                    progress + '%'
																                );
                                                                            }
                                                                        }).prop('disabled', !$.support.fileInput)
                                                                        .parent().addClass($.support.fileInput ? undefined : 'disabled');
                                                                </script>
                                                	</td>
                                                </tr>
                                                <tr><td class="first-ta">Description</td><td colspan="2" style="text-align: left;">
                                                     <input class="form-control" type="text" id="pce_des_<?echo $cur_time;?>" value="<?=$pce->pce_des?>">
                                                </td></tr>
                                                <tr><td class="first-ta">Amount</td><td colspan="2" style="text-align: left;">
                                                    <input class="form-control" type="text" id="pce_amount_<?echo $cur_time;?>" value="<?=$pce->pce_amount?>">
                                                </td></tr>                                                
                                            </table>  
                                            </div>

                                            <div id="oc_region_<?=$cur_time?>" class="span6 oc_hold_special">
                                            <?
                                            foreach ($oc_1_list as $key => $oc) {
                                                $oc_id=$oc->id
                                                ?>

                                                    <div id="oc_cur_<?echo $oc_id;?>" class="span12 no-margin-left">
                                                    <table class="table table-noborder">
                                                        <tr>
                                                            <td colspan="2">
                                                                <a class="btn-atom btn-atom-success fancybox" data-fancybox-type="iframe" href="<?=site_url("project/oc_payment/".$oc_id)?>">Billing due <i class="icon-plus icon-white"></i></a>
                                                                <?
                                                                if ($oc->is_done=="n") {
                                                                    ?>
                                                                    <a id="" href="javascript:;" iden="<?=$oc_id?>" stat="Done" class="btn-atom btn-atom-info oc_done">Not Done</a>
                                                                    <?
                                                                }else{
                                                                    ?>
                                                                    <a id="" href="javascript:;" iden="<?=$oc_id?>" stat="Undone" class="btn-atom btn-atom-info oc_done">Done</a>
                                                                    <?
                                                                }
                                                                ?>
                                                            </td>
                                                            <td style="text-align:right;">
                                                                <a id="" href="javascript:;" iden="<?=$oc_id?>" class="btn-atom btn-atom-danger oc_delete">DELETE</a>
                                                            </td>
                                                            </tr>
                                                    </table>
                                                    <table class="table table-noborder">
                                                            <tr><td class="first-ta">OC#</td><td colspan="2" style="text-align: left;">
                                                            <a href="<?echo site_url("project/view_sign_oc/".$oc->id)?>" target="_blank"><?=$oc->oc_no?></a>&nbsp;|&nbsp;
                                                            <a href="<?echo site_url("media/real_pdf/".$oc->filename_pce)?>" target="_blank"><?=$oc->pce->pce_no?></a>   
                                                            </td></tr>
                                                            <tr><td class="first-ta">Description#</td><td colspan="2" style="text-align: left;">
                                                            <?=$oc->oc_des?>
                                                            </td></tr>
                                                            <tr><td class="first-ta">Amount#</td><td colspan="2" style="text-align: left;">
                                                            <?=number_format($oc->oc_amount, 2, '.', ',')?>
                                                            </td></tr>
                                                            <td></td><td colspan="2" style="text-align: left;">
                                                                <?
                                                                    if ($oc->status=="y") {
                                                                           
                                                                            ?>
                                                                            <a id="fc_oc_<?echo $oc_id;?>" href="<?echo site_url("project/approve_oc_view/".$oc_id);?>" class="btn btn-inverse fancybox" data-fancybox-type="iframe"><i class="icon-ok icon-white"></i>FC </a>
                                                                            <?
                                                                        }else if($oc->status=="n"){
                                                                            ?>
                                                                            <a id="fc_oc_<?echo $oc_id;?>" href="<?echo site_url("project/approve_oc_view/".$oc_id);?>" class="btn btn-danger fancybox" data-fancybox-type="iframe"><i class="icon-remove icon-white"></i>FC </a>
                                                                            <?
                                                                        }else{
                                                                            ?>
                                                                            <a id="fc_oc_<?echo $oc_id;?>" href="<?echo site_url("project/approve_oc_view/".$oc_id);?>" class="btn fancybox" data-fancybox-type="iframe">FC </a>                 
                                                                            <?
                                                                        }
                                                                    ?>
                                                            </td></tr>
                                                        <tr>
                                                            <td colspan="3"><hr id="hr_<?echo $oc_id;?>"></td>
                                                        </tr>
                                                    </table>    
                                                    </div>   
                                                <?
                                            }
                                            ?>

                                            </div>

                                        </div>
                                        <div id="outsource-but_<?=$cur_time?>" class="span12 no-margin-left outsource-cls">
                                            <table class="table table-noborder">
                                                <tr>
                                                    <td style="text-align:center">
                                                       <a id="out_but_<?=$cur_time?>" iden="<?=$cur_time?>" togStat="hide" href="javascript:;" class="btn btn-large btn-block toggle_outsource">Out source</a>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div id="outsource_<?echo $cur_time;?>" class="span12 no-margin-left" style="display:none">
                                            <div class="span12 no-margin-left">
                                                <h5>Out Source</h5>
                                                <h5>PCE# <?=$pce->pce_no?></h5>
                                                <div>
                                                    <?=$pce->pce_des?>
                                                </div>
                                            </div>
                                            <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered orange lowfont">
                                                <thead>
                                                    <tr>
                                                        <th>QT#</th>
                                                        <th></th>
                                                        <th>description</th>
                                                        <th>Cost</th>
                                                        <th>Charge</th>
                                                        <th>Margin</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="table_out_<?=$cur_time?>">
                                                <?
                                                foreach ($pce->outsource as $outsource_key => $outsource) {
                                                    $up_time=$outsource->id;
                                                    ?>

                                                    <tr class="out_<?=$cur_time?>" iden="<?=$cur_time?>">
                                                        <td><input class="" type="text" name="qt_no[<?=$cur_time?>][]" value="<?=$outsource->qt_no?>">
                                                        <input class="" type="hidden" name="qt_id[<?=$cur_time?>][]" value="<?=$outsource->id?>">
                                                        </td>
                                                        <td>
                                                            <div class="control-group">
                                                            <?
                                                                if ($outsource->filename!="no_file") {
                                                                    ?>
                                                                    <a id="qt_view_<?=$up_time?>" href="<?echo site_url("media/real_pdf/".$outsource->filename)?>" target="_blank">ดูไฟล์</a>
                                                                    <?
                                                                }else{
                                                                    ?>
                                                                    <a id="qt_view_<?=$up_time?>" href="" target="_blank"></a>
                                                                    <?
                                                                }
                                                                ?>
                                                                <span class="btn btn-success fileinput-button">
                                                                    
                                                                    <i class="glyphicon glyphicon-plus"></i>
                                                                    <span id="but_upload_<?=$cur_time?>">เลือกไฟล์ PDF</span>
                                                                     <!-- The file input field used as target for the file upload widget -->
                                                                    <input id="qtupload_<?=$up_time?>" type="file">
                                                                    <input id="qt_file_<?=$up_time?>" type="hidden" name="qt_filename[<?=$cur_time?>][]" value="old__<?=$outsource->filename?>">
                                                                </span>
                                                                <script type="text/javascript">
                                                                    $('#qtupload_<?=$up_time?>').fileupload({
                                                                            previewThumbnail: false,
                                                                            url: '<?php echo site_url('upload_handler/pdf '); ?>',
                                                                            dataType: 'json',
                                                                            beforeSend: function() {
                                                                                $('#but_upload_<?=$up_time?>').html("Please wait");
                                                                                $('#qtupload_<?=$up_time?>').attr("disabled","");
                                                                            },
                                                                            done: function(e, data) {
                                                                                //console.log(data);

                                                                                $.each(data.result.files, function(index, file) {
                                                                                    //console.log(file);
                                                                                    if (file.error == "File is too big") {
                                                                                        alert("File is too big exceed 100 MB");
                                                                                        $("#qt_file_<?=$up_time?>").val("");
                                                                                    }else if (file.error == "Filetype not allowed") {
                                                                                        alert("Filetype not allowed");
                                                                                       $("#qt_file_<?=$up_time?>").val("");
                                                                                    } else {
                                                                                        alert("Upload Complete file " + file.name);
                                                                                        $("#qt_file_<?=$up_time?>").val(file.name);
                                                                                        $('#qt_view_<?=$up_time?>').attr("href",'<?echo site_url("media/temp/")?>/'+file.name);
                                                                                        $('#qt_view_<?=$up_time?>').html("ดูไฟล์");
                                                                                    }
                                                                                    $('#but_upload_<?=$up_time?>').html("เลือกไฟล์ PDF");

                                                                                    $('#qtupload_<?=$up_time?>').removeAttr("disabled");
                                                                                });

                                                                            },
                                                                            progressall: function(e, data) {
                                                                            }
                                                                        }).prop('disabled', !$.support.fileInput)
                                                                        .parent().addClass($.support.fileInput ? undefined : 'disabled');
                                                                </script>
                                                            </div>
                                                        </td>
                                                        <td><input class="" type="text" name="qt_des[<?=$cur_time?>][]" value="<?=$outsource->qt_des?>"></td>
                                                        <td><input class="out_change" type="text" name="qt_cost[<?=$cur_time?>][]" value="<?=$outsource->qt_cost?>"></td>
                                                        <td><input class="out_change" type="text" name="qt_charge[<?=$cur_time?>][]" value="<?=$outsource->qt_charge?>"></td>
                                                        <td class="out_margin"><?echo number_format("".($outsource->qt_charge-$outsource->qt_cost)/$outsource->qt_cost*100,2);?></td>
                                                        <td>
                                                            <a iden="<?=$up_time?>" href="javascript:;" class="btn btn-danger del_outlist"><i class="icon-remove icon-white"></i></a>                          
                                                        </td>
                                                    </tr>
                                                    <?
                                                }
                                                ?>
                                                    <tr id="be_out_<?=$cur_time?>">
                                                        <td></td>
                                                        <td></td>
                                                        <td>Grand Total</td>
                                                        <td class="total_cost"></td>
                                                        <td class="total_charge"></td>
                                                        <td class="total_margin"></td>
                                                        <td>
                                                            
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <script type="text/javascript">
                                        cal_outSource('<?=$cur_time?>');
                                        </script>
			<?
		}else{
                                    ?>

                                        <div id="pce_cur_<?echo $cur_time;?>" class="span12 no-margin-left">
                                        <table class="table table-noborder">
                                                <tr>
                                                    <td></td>
                                                    <td></td><td style="text-align:right;">
                                                    	<a id="" href="javascript:;" iden="<?=$cur_time?>" class="btn btn-danger pce_rewrite_cancel">Cancel</a>
                                                		<a id="" href="javascript:;" iden="<?=$cur_time?>" class="btn btn-info pce_rewrite_ok">OK</a>
                                                    </td>
                                                </tr>
                                                <tr><td class="first-ta">PCE#</td><td colspan="2" style="text-align: left;">
                                                <input class="form-control" type="text" id="pce_no_<?echo $cur_time;?>" value="<?=$pce->pce_no?>">
                                                </td></tr>
                                                <tr>
                                                	<td class="first-ta">
                                                		File
                                                	</td>
                                                	<td colspan="2">
                                                		<div class="control-group">
				                                            <span class="btn btn-success fileinput-button">
				                                                            <i class="glyphicon glyphicon-plus"></i>
				                                                            <span>เลือกไฟล์ PDF</span> 
				                                            <!-- The file input field used as target for the file upload widget -->
				                                            <input id="fileupload_rewrite_<?echo $cur_time;?>" type="file" name="files">
				                                            <input id="temp_f_name_rewrite_<?echo $cur_time;?>" type="hidden" value="old">
				                                            </span> <a href="<?echo site_url("project/view_sign_pce/".$pce->id)?>" target="_blank"><?=$pce->pce_no?></a>
				                                            <br>
				                                            <br>
				                                            <!-- The global progress bar -->
				                                            <div id="progress_rewrite_<?echo $cur_time;?>" class="progress">
				                                                <div class="progress-bar progress-bar-success"></div>
				                                            </div>
				                                        </div>
				                                        <script type="text/javascript">
                                                                    $('#fileupload_rewrite_<?echo $cur_time;?>').fileupload({
                                                                            previewThumbnail: false,
                                                                            url: '<?php echo site_url('upload_handler/pdf '); ?>',
                                                                            dataType: 'json',
                                                                            beforeSend: function() {
                                                                            	$('#progress_rewrite_<?echo $cur_time;?> .progress-bar').css(
																	                    'width',
																	                    '10%'
																	                );
                                                                            },
                                                                            done: function(e, data) {
                                                                                //console.log(data);

                                                                                $.each(data.result.files, function(index, file) {
                                                                                    //console.log(file);
                                                                                    if (file.error == "File is too big") {
                                                                                        alert("File is too big exceed 100 MB");
                                                                                        $("#temp_f_name_rewrite_<?echo $cur_time;?>").val("old");
                                                                                    }else if (file.error == "Filetype not allowed") {
                                                                                        alert("Filetype not allowed");
                                                                                       $("#temp_f_name_rewrite_<?echo $cur_time;?>").val("old");
                                                                                    } else {
                                                                                        alert("Upload Complete file " + file.name);
                                                                                        $("#temp_f_name_rewrite_<?echo $cur_time;?>").val(file.name);
                                                                                    }
                                                                                });

                                                                            },
                                                                            progressall: function(e, data) {
                                                                            	var progress = parseInt(data.loaded / data.total * 100, 10);
																                $('#progress_rewrite_<?echo $cur_time;?> .progress-bar').css(
																                    'width',
																                    progress + '%'
																                );
                                                                            }
                                                                        }).prop('disabled', !$.support.fileInput)
                                                                        .parent().addClass($.support.fileInput ? undefined : 'disabled');
                                                                </script>
                                                	</td>
                                                </tr>
                                                <tr><td class="first-ta">Description</td><td colspan="2" style="text-align: left;">
                                                     <input class="form-control" type="text" id="pce_des_<?echo $cur_time;?>" value="<?=$pce->pce_des?>">
                                                </td></tr>
                                                <tr><td class="first-ta">Amount</td><td colspan="2" style="text-align: left;">
                                                    <input class="form-control" type="text" id="pce_amount_<?echo $cur_time;?>" value="<?=$pce->pce_amount?>">
                                                </td></tr>                                                
                                            </table>  
                                        </div>
                                        <div id="outsource-but_<?=$cur_time?>" class="span12 no-margin-left outsource-cls">
                                            <table class="table table-noborder">
                                                <tr>
                                                    <td style="text-align:center">
                                                       <a id="out_but_<?=$cur_time?>" iden="<?=$cur_time?>" togStat="hide" href="javascript:;" class="btn btn-large btn-block toggle_outsource">Out source</a>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div id="outsource_<?echo $cur_time;?>" class="span12 no-margin-left" style="display:none">
                                            <div class="span12 no-margin-left">
                                                <h5>Out Source</h5>
                                                <h5>PCE# <?=$pce->pce_no?></h5>
                                                <div>
                                                    <?=$pce->pce_des?>
                                                </div>
                                            </div>
                                            <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered orange lowfont">
                                                <thead>
                                                    <tr>
                                                        <th>QT#</th>
                                                        <th></th>
                                                        <th>description</th>
                                                        <th>Cost</th>
                                                        <th>Charge</th>
                                                        <th>Margin</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="table_out_<?=$cur_time?>">
                                                <?
                                                foreach ($pce->outsource as $outsource_key => $outsource) {
                                                    $up_time=$outsource->id;
                                                    ?>

                                                    <tr class="out_<?=$cur_time?>" iden="<?=$cur_time?>">
                                                        <td><input class="" type="text" name="qt_no[<?=$cur_time?>][]" value="<?=$outsource->qt_no?>">
                                                        <input class="" type="hidden" name="qt_id[<?=$cur_time?>][]" value="<?=$outsource->id?>">
                                                        </td>
                                                        <td>
                                                            <div class="control-group">
                                                            <?
                                                                if ($outsource->filename!="no_file") {
                                                                    ?>
                                                                    <a id="qt_view_<?=$up_time?>" href="<?echo site_url("media/real_pdf/".$outsource->filename)?>" target="_blank">ดูไฟล์</a>
                                                                    <?
                                                                }else{
                                                                    ?>
                                                                    <a id="qt_view_<?=$up_time?>" href="" target="_blank"></a>
                                                                    <?
                                                                }
                                                                ?>
                                                                <span class="btn btn-success fileinput-button">
                                                                    
                                                                    <i class="glyphicon glyphicon-plus"></i>
                                                                    <span id="but_upload_<?=$cur_time?>">เลือกไฟล์ PDF</span>
                                                                     <!-- The file input field used as target for the file upload widget -->
                                                                    <input id="qtupload_<?=$up_time?>" type="file">
                                                                    <input id="qt_file_<?=$up_time?>" type="hidden" name="qt_filename[<?=$cur_time?>][]" value="old__<?=$outsource->filename?>">
                                                                </span>
                                                                <script type="text/javascript">
                                                                    $('#qtupload_<?=$up_time?>').fileupload({
                                                                            previewThumbnail: false,
                                                                            url: '<?php echo site_url('upload_handler/pdf '); ?>',
                                                                            dataType: 'json',
                                                                            beforeSend: function() {
                                                                                $('#but_upload_<?=$up_time?>').html("Please wait");
                                                                                $('#qtupload_<?=$up_time?>').attr("disabled","");
                                                                            },
                                                                            done: function(e, data) {
                                                                                //console.log(data);

                                                                                $.each(data.result.files, function(index, file) {
                                                                                    //console.log(file);
                                                                                    if (file.error == "File is too big") {
                                                                                        alert("File is too big exceed 100 MB");
                                                                                        $("#qt_file_<?=$up_time?>").val("");
                                                                                    }else if (file.error == "Filetype not allowed") {
                                                                                        alert("Filetype not allowed");
                                                                                       $("#qt_file_<?=$up_time?>").val("");
                                                                                    } else {
                                                                                        alert("Upload Complete file " + file.name);
                                                                                        $("#qt_file_<?=$up_time?>").val(file.name);
                                                                                        $('#qt_view_<?=$up_time?>').attr("href",'<?echo site_url("media/temp/")?>/'+file.name);
                                                                                        $('#qt_view_<?=$up_time?>').html("ดูไฟล์");
                                                                                    }
                                                                                    $('#but_upload_<?=$up_time?>').html("เลือกไฟล์ PDF");

                                                                                    $('#qtupload_<?=$up_time?>').removeAttr("disabled");
                                                                                });

                                                                            },
                                                                            progressall: function(e, data) {
                                                                            }
                                                                        }).prop('disabled', !$.support.fileInput)
                                                                        .parent().addClass($.support.fileInput ? undefined : 'disabled');
                                                                </script>
                                                            </div>
                                                        </td>
                                                        <td><input class="" type="text" name="qt_des[<?=$cur_time?>][]" value="<?=$outsource->qt_des?>"></td>
                                                        <td><input class="out_change" type="text" name="qt_cost[<?=$cur_time?>][]" value="<?=$outsource->qt_cost?>"></td>
                                                        <td><input class="out_change" type="text" name="qt_charge[<?=$cur_time?>][]" value="<?=$outsource->qt_charge?>"></td>
                                                        <td class="out_margin"><?echo number_format("".($outsource->qt_charge-$outsource->qt_cost)/$outsource->qt_cost*100,2);?></td>
                                                        <td>
                                                            
                                                        </td>
                                                    </tr>
                                                    <?
                                                }
                                                ?>
                                                    <tr id="be_out_<?=$cur_time?>">
                                                        <td></td>
                                                        <td></td>
                                                        <td>Grand Total</td>
                                                        <td class="total_cost"></td>
                                                        <td class="total_charge"></td>
                                                        <td class="total_margin"></td>
                                                        <td>
                                                            <a iden="<?=$cur_time?>" href="javascript:;" class="btn btn-success add_out_list"><i class="icon-plus icon-white"></i></a>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <script type="text/javascript">
                                        cal_outSource('<?=$cur_time?>');
                                        </script>
                                    <?
                                }
	}
	public function ajax_rewrite_pce_cancel_html(){
		$pce_id=$_POST['pce_id'];
		$pce=$this->m_pce->get_pce_by_id($pce_id);
		$cur_time=$pce->id;
		$user_data=$this->user_data;
		if (isset($_POST['from_oc'])) {
			$oc_1_list=$this->m_oc->get_all_oc_by_pce_id($pce->id);
			?>
										<div id="pce_cur_<?echo $cur_time;?>" class="span12 no-margin-left pce-hold">
                                            <div class="span6" id="pce_inner_<?echo $cur_time;?>">
                                            <table class="table table-noborder">
                                                <tr>
                                                    <td> <a class="btn btn-info fancybox" data-fancybox-type="iframe" href="<?echo site_url("project/cs_set_sign_time/".$cur_time);?>"  ><i class="icon-pencil icon-white"></i></a>
                                                    <?
                                                    $have_rewrite=$this->m_pce->get_pce_rewrite_child_by_id($pce->id);
                                                    if (isset($have_rewrite->id)) {
                                                        ?>
                                                         <a class="btn btn-warning fancybox" data-fancybox-type="iframe" href="<?echo site_url("project/rewrite_pce_view/".$cur_time);?>"  ><i class="icon-list icon-white"></i></a>
                                                        <?
                                                    }
                                                    ?> </td>
                                                    <td></td><td style="text-align:right;"><a id="" href="javascript:;" iden="<?=$cur_time?>" class="btn-atom btn-atom-warning pce_rewrite">Revise</a></td>
                                                </tr>
                                                <tr><td class="first-ta">PCE#</td><td colspan="2" style="text-align: left;">
                                                <a href="<?echo site_url("project/view_sign_pce/".$pce->id)?>" target="_blank"><?=$pce->pce_no?>&nbsp;&nbsp;<img src="<?echo site_url("img/pdf_img.png")?>"></a>
                                                <input type="hidden" name="pce_filename[<?echo $cur_time;?>]" value="old__<?=$pce->filename?>">
                                                <input type="hidden" name="pce_no[<?echo $cur_time;?>]" value="<?=$pce->pce_no?>">
                                                </td></tr>
                                                <tr><td class="first-ta">Description</td><td colspan="2" style="text-align: left;">
                                                     <?=$pce->pce_des?>
                                                    <input type="hidden" name="pce_des[<?echo $cur_time;?>]" value="<?=$pce->pce_des?>">
                                                </td></tr>
                                                <tr><td class="first-ta">Amount</td><td colspan="2" style="text-align: left;">
                                                    <?=number_format($pce->pce_amount, 2, '.', ',')?>
                                                <input type="hidden" name="pce_amount[<?echo $cur_time;?>]" value="<?=$pce->pce_amount?>">
                                                </td></tr>
                                                <td></td><td colspan="2" style="text-align: left;">
                                                    <?
                                                    $hod_all_approve=true;
                                                    $hod_reject_flag=false;
                                                    foreach ($pce->hod_list as $hlistkey => $hlistvalue) {
                                                        if($hlistvalue->approve=="ns"){
                                                            $hod_all_approve=false;
                                                        }
                                                        if($hlistvalue->approve=="n"){
                                                            $hod_reject_flag=true;
                                                        }
                                                    }
                                                   if ($pce->csd_sign_status=="y") {                                                    
                                                        ?>
                                                        <a id="csd_a_<?echo $cur_time;?>" class="btn btn-inverse fancybox" data-fancybox-type="iframe" href="<?echo site_url("project/approve_pce_view/".$cur_time."/csd");?>"  ><i class="icon-ok icon-white"></i>CSD </a>
                                                        <?
                                                    }else if($pce->csd_sign_status=="n"){
                                                        ?>
                                                        <a id="csd_a_<?echo $cur_time;?>" class="btn btn-danger fancybox" data-fancybox-type="iframe" href="<?echo site_url("project/approve_pce_view/".$cur_time."/csd");?>"  ><i class="icon-remove icon-white"></i>CSD </a>
                                                        <?
                                                    }else{
                                                        ?>
                                                        <a id="csd_a_<?echo $cur_time;?>" class="btn fancybox" data-fancybox-type="iframe" href="<?echo site_url("project/approve_pce_view/".$cur_time."/csd");?>" >CSD </a>
                                                        <?
                                                    }
                                                    if($hod_reject_flag){
                                                        ?>
                                                        <a id="hod_a_<?echo $cur_time;?>" class="btn btn-danger fancybox" data-fancybox-type="iframe" href="<?echo site_url("project/approve_pce_view/".$cur_time."/hod");?>" ><i class="icon-remove icon-white"></i>HOD </a>
                                                        <?
                                                    }else if (isset($pce->hod_list[$user_data->username])&&$pce->hod_list[$user_data->username]->approve=="y") {
                                                        ?>
                                                        <a id="hod_a_<?echo $cur_time;?>" class="btn btn-inverse fancybox" data-fancybox-type="iframe" href="<?echo site_url("project/approve_pce_view/".$cur_time."/hod");?>" ><i class="icon-ok icon-white"></i>HOD </a>
                                                        <?
                                                    }else if($hod_all_approve){
                                                        ?>
                                                        <a id="hod_a_<?echo $cur_time;?>" class="btn btn-inverse fancybox" data-fancybox-type="iframe" href="<?echo site_url("project/approve_pce_view/".$cur_time."/hod");?>" ><i class="icon-ok icon-white"></i>HOD </a>
                                                        <?
                                                    }else{
                                                        ?>
                                                        <a id="hod_a_<?echo $cur_time;?>" class="btn fancybox" data-fancybox-type="iframe" href="<?echo site_url("project/approve_pce_view/".$cur_time."/hod");?>">HOD </a>
                                                        <?
                                                    }
                                                    
                                                    if ($pce->fc_sign_status=="y") {                                                    
                                                        ?>
                                                        <a id="fc_a_<?echo $cur_time;?>" class="btn btn-inverse fancybox" data-fancybox-type="iframe" href="<?echo site_url("project/approve_pce_view/".$cur_time."/fc");?>"  ><i class="icon-ok icon-white"></i>FC </a>
                                                        <?
                                                    }else if($pce->fc_sign_status=="n"){
                                                        ?>
                                                        <a id="fc_a_<?echo $cur_time;?>" class="btn btn-danger fancybox" data-fancybox-type="iframe" href="<?echo site_url("project/approve_pce_view/".$cur_time."/fc");?>"  ><i class="icon-remove icon-white"></i>FC </a>
                                                        <?
                                                    }else{
                                                        ?>
                                                        <a id="fc_a_<?echo $cur_time;?>" class="btn fancybox" data-fancybox-type="iframe" href="<?echo site_url("project/approve_pce_view/".$cur_time."/fc");?>" >FC </a>
                                                        <?
                                                    }
                                                    ?>
                                                </td></tr>
                                            </table>
                                            </div>
                                            <div id="oc_region_<?=$cur_time?>" class="span6 oc_hold_special">
                                            <?
                                            foreach ($oc_1_list as $key => $oc) {
                                                $oc_id=$oc->id
                                                ?>

                                                    <div id="oc_cur_<?echo $oc_id;?>" class="span12 no-margin-left">
                                                    <table class="table table-noborder">
                                                        <tr>
                                                            <td colspan="2">
                                                                <a class="btn-atom btn-atom-success fancybox" data-fancybox-type="iframe" href="<?=site_url("project/oc_payment/".$oc_id)?>">Billing due <i class="icon-plus icon-white"></i></a>
                                                                <?
                                                                if ($oc->is_done=="n") {
                                                                    ?>
                                                                    <a id="" href="javascript:;" iden="<?=$oc_id?>" stat="Done" class="btn-atom btn-atom-info oc_done">Not Done</a>
                                                                    <?
                                                                }else{
                                                                    ?>
                                                                    <a id="" href="javascript:;" iden="<?=$oc_id?>" stat="Undone" class="btn-atom btn-atom-info oc_done">Done</a>
                                                                    <?
                                                                }
                                                                ?>
                                                            </td>
                                                            <td style="text-align:right;">
                                                                <a id="" href="javascript:;" iden="<?=$oc_id?>" class="btn-atom btn-atom-danger oc_delete">DELETE</a>
                                                            </td>
                                                            </tr>
                                                    </table>
                                                    <table class="table table-noborder">
                                                            <tr><td class="first-ta">OC#</td><td colspan="2" style="text-align: left;">
                                                            <a href="<?echo site_url("project/view_sign_oc/".$oc->id)?>" target="_blank"><?=$oc->oc_no?></a>&nbsp;|&nbsp;
                                                            <a href="<?echo site_url("media/real_pdf/".$oc->filename_pce)?>" target="_blank"><?=$oc->pce->pce_no?></a>   
                                                            </td></tr>
                                                            <tr><td class="first-ta">Description#</td><td colspan="2" style="text-align: left;">
                                                            <?=$oc->oc_des?>
                                                            </td></tr>
                                                            <tr><td class="first-ta">Amount#</td><td colspan="2" style="text-align: left;">
                                                            <?=number_format($oc->oc_amount, 2, '.', ',')?>
                                                            </td></tr>
                                                            <td></td><td colspan="2" style="text-align: left;">
                                                                <?
                                                                    if ($oc->status=="y") {
                                                                           
                                                                            ?>
                                                                            <a id="fc_oc_<?echo $oc_id;?>" href="<?echo site_url("project/approve_oc_view/".$oc_id);?>" class="btn btn-inverse fancybox" data-fancybox-type="iframe"><i class="icon-ok icon-white"></i>FC </a>
                                                                            <?
                                                                        }else if($oc->status=="n"){
                                                                            ?>
                                                                            <a id="fc_oc_<?echo $oc_id;?>" href="<?echo site_url("project/approve_oc_view/".$oc_id);?>" class="btn btn-danger fancybox" data-fancybox-type="iframe"><i class="icon-remove icon-white"></i>FC </a>
                                                                            <?
                                                                        }else{
                                                                            ?>
                                                                            <a id="fc_oc_<?echo $oc_id;?>" href="<?echo site_url("project/approve_oc_view/".$oc_id);?>" class="btn fancybox" data-fancybox-type="iframe">FC </a>                 
                                                                            <?
                                                                        }
                                                                    ?>
                                                            </td></tr>
                                                        <tr>
                                                            <td colspan="3"><hr id="hr_<?echo $oc_id;?>"></td>
                                                        </tr>
                                                    </table>    
                                                    </div>                                                    
                                                    
                                                <?
                                            }
                                            ?>
                                            <div class="span12 no-margin-left" id="before_add_oc_but_<?=$cur_time?>">
                                                <table class="table table-noborder">
                                                    <tr>
                                                        <td style="text-align:center">
                                                           <a class="btn btn-success fancybox" data-fancybox-type="iframe" href="<?echo site_url("project/pop_add_oc/".$pce->id);?>"  >Add OC/IOC</a>  
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                            </div>
                                        </div>
                                        <div id="outsource-but_<?=$cur_time?>" class="span12 no-margin-left outsource-cls">
                                            <table class="table table-noborder">
                                                <tr>
                                                    <td style="text-align:center">
                                                       <a id="out_but_<?=$cur_time?>" iden="<?=$cur_time?>" togStat="hide" href="javascript:;" class="btn btn-large btn-block toggle_outsource">Out source</a>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div id="outsource_<?echo $cur_time;?>" class="span12 no-margin-left" style="display:none">
                                            <div class="span12 no-margin-left">
                                                <h5>Out Source</h5>
                                                <h5>PCE# <?=$pce->pce_no?></h5>
                                                <div>
                                                    <?=$pce->pce_des?>
                                                </div>
                                            </div>
                                            <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered orange lowfont">
                                                <thead>
                                                    <tr>
                                                        <th>QT#</th>
                                                        <th></th>
                                                        <th>description</th>
                                                        <th>Cost</th>
                                                        <th>Charge</th>
                                                        <th>Margin</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="table_out_<?=$cur_time?>">
                                                <?
                                                foreach ($pce->outsource as $outsource_key => $outsource) {
                                                    $up_time=$outsource->id;
                                                    ?>

                                                    <tr class="out_<?=$cur_time?>" iden="<?=$cur_time?>">
                                                        <td><input class="" type="text" name="qt_no[<?=$cur_time?>][]" value="<?=$outsource->qt_no?>">
                                                        <input class="" type="hidden" name="qt_id[<?=$cur_time?>][]" value="<?=$outsource->id?>">
                                                        </td>
                                                        <td>
                                                            <div class="control-group">
                                                                <?
                                                                if ($outsource->filename!="no_file") {
                                                                    ?>
                                                                    <a id="qt_view_<?=$up_time?>" href="<?echo site_url("media/real_pdf/".$outsource->filename)?>" target="_blank">ดูไฟล์</a>
                                                                    <?
                                                                }else{
                                                                    ?>
                                                                    <a id="qt_view_<?=$up_time?>" href="" target="_blank"></a>
                                                                    <?
                                                                }
                                                                ?>
                                                                
                                                                <span class="btn btn-success fileinput-button">
                                                                    
                                                                    <i class="glyphicon glyphicon-plus"></i>
                                                                    <span id="but_upload_<?=$cur_time?>">เลือกไฟล์ PDF</span>
                                                                     <!-- The file input field used as target for the file upload widget -->
                                                                    <input id="qtupload_<?=$up_time?>" type="file">
                                                                    <input id="qt_file_<?=$up_time?>" type="hidden" name="qt_filename[<?=$cur_time?>][]" value="old__<?=$outsource->filename?>">
                                                                </span>
                                                                <script type="text/javascript">
                                                                    $('#qtupload_<?=$up_time?>').fileupload({
                                                                            previewThumbnail: false,
                                                                            url: '<?php echo site_url('upload_handler/pdf '); ?>',
                                                                            dataType: 'json',
                                                                            beforeSend: function() {
                                                                                $('#but_upload_<?=$up_time?>').html("Please wait");
                                                                                $('#qtupload_<?=$up_time?>').attr("disabled","");
                                                                            },
                                                                            done: function(e, data) {
                                                                                //console.log(data);

                                                                                $.each(data.result.files, function(index, file) {
                                                                                    //console.log(file);
                                                                                    if (file.error == "File is too big") {
                                                                                        alert("File is too big exceed 100 MB");
                                                                                        $("#qt_file_<?=$up_time?>").val("");
                                                                                    }else if (file.error == "Filetype not allowed") {
                                                                                        alert("Filetype not allowed");
                                                                                       $("#qt_file_<?=$up_time?>").val("");
                                                                                    } else {
                                                                                        alert("Upload Complete file " + file.name);
                                                                                        $("#qt_file_<?=$up_time?>").val(file.name);
                                                                                        $('#qt_view_<?=$up_time?>').attr("href",'<?echo site_url("media/temp/")?>/'+file.name);
                                                                                        $('#qt_view_<?=$up_time?>').html("ดูไฟล์");
                                                                                    }
                                                                                    $('#but_upload_<?=$up_time?>').html("เลือกไฟล์ PDF");

                                                                                    $('#qtupload_<?=$up_time?>').removeAttr("disabled");
                                                                                });

                                                                            },
                                                                            progressall: function(e, data) {
                                                                            }
                                                                        }).prop('disabled', !$.support.fileInput)
                                                                        .parent().addClass($.support.fileInput ? undefined : 'disabled');
                                                                </script>
                                                            </div>
                                                        </td>
                                                        <td><input class="" type="text" name="qt_des[<?=$cur_time?>][]" value="<?=$outsource->qt_des?>"></td>
                                                        <td><input class="out_change" type="text" name="qt_cost[<?=$cur_time?>][]" value="<?=$outsource->qt_cost?>"></td>
                                                        <td><input class="out_change" type="text" name="qt_charge[<?=$cur_time?>][]" value="<?=$outsource->qt_charge?>"></td>
                                                        <td class="out_margin"><?echo number_format("".($outsource->qt_charge-$outsource->qt_cost)/$outsource->qt_cost*100,2);?></td>
                                                        <td>
                                                            <a iden="<?=$up_time?>" href="javascript:;" class="btn btn-danger del_outlist"><i class="icon-remove icon-white"></i></a>                                                                 
                                                            <a class="btn btn-success fancybox" data-fancybox-type="iframe" href="<?=site_url("project/outsource_payment/".$up_time)?>"><i class="icon-plus icon-white"></i>Pay date</a>               
                                                        </td>
                                                    </tr>
                                                    <?
                                                }
                                                ?>
                                                    <tr id="be_out_<?=$cur_time?>">
                                                        <td></td>
                                                        <td></td>
                                                        <td>Grand Total</td>
                                                        <td class="total_cost"></td>
                                                        <td class="total_charge"></td>
                                                        <td class="total_margin"></td>
                                                        <td>
                                                            <a iden="<?=$cur_time?>" href="javascript:;" class="btn btn-success add_out_list"><i class="icon-plus icon-white"></i></a>                                                            
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div id="del-but_<?=$cur_time?>" class="span12 no-margin-left">
                                            <table class="table table-noborder">
                                                <tr>
                                                    <td style="text-align:center">
                                                       <a id="" href="javascript:;" iden="<?=$cur_time?>" class="btn btn-danger pce_delete">DELETE PCE</a>   
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                        <script type="text/javascript">
                                        cal_outSource('<?=$cur_time?>');
                                        </script>
			<?
		}else{
                                    ?>

                                        <div id="pce_cur_<?echo $cur_time;?>" class="span12 no-margin-left pce-hold">
                                        <table class="table table-noborder">
                                                <tr>
                                                    <td> <a class="btn btn-info fancybox" data-fancybox-type="iframe" href="<?echo site_url("project/cs_set_sign_time/".$cur_time);?>"  ><i class="icon-pencil icon-white"></i></a>
                                                    <?
                                                    $have_rewrite=$this->m_pce->get_pce_rewrite_child_by_id($pce->id);
                                                    if (isset($have_rewrite->id)) {
                                                        ?>
                                                         <a class="btn btn-warning fancybox" data-fancybox-type="iframe" href="<?echo site_url("project/rewrite_pce_view/".$cur_time);?>"  ><i class="icon-list icon-white"></i></a>
                                                        <?
                                                    }
                                                    ?> </td>
                                                    <td></td><td style="text-align:right;"><a id="" href="javascript:;" iden="<?=$cur_time?>" class="btn-atom btn-atom-warning pce_rewrite">Revise</a></td>
                                                </tr>
                                                <tr><td class="first-ta">PCE#</td><td colspan="2" style="text-align: left;">
                                                <a href="<?echo site_url("project/view_sign_pce/".$pce->id)?>" target="_blank"><?=$pce->pce_no?>&nbsp;&nbsp;<img src="<?echo site_url("img/pdf_img.png")?>"></a>
                                                <input type="hidden" name="pce_filename[<?echo $cur_time;?>]" value="old__<?=$pce->filename?>">
                                                <input type="hidden" name="pce_no[<?echo $cur_time;?>]" value="<?=$pce->pce_no?>">
                                                </td></tr>
                                                <tr><td class="first-ta">Description</td><td colspan="2" style="text-align: left;">
                                                     <?=$pce->pce_des?>
                                                    <input type="hidden" name="pce_des[<?echo $cur_time;?>]" value="<?=$pce->pce_des?>">
                                                </td></tr>
                                                <tr><td class="first-ta">Amount</td><td colspan="2" style="text-align: left;">
                                                    <?=number_format($pce->pce_amount, 2, '.', ',')?>
                                                <input type="hidden" name="pce_amount[<?echo $cur_time;?>]" value="<?=$pce->pce_amount?>">
                                                </td></tr>
                                                <td></td><td colspan="2" style="text-align: left;">
                                                    <?
                                                    $hod_all_approve=true;
                                                    $hod_reject_flag=false;
                                                    foreach ($pce->hod_list as $hlistkey => $hlistvalue) {
                                                        if($hlistvalue->approve=="ns"){
                                                            $hod_all_approve=false;
                                                        }
                                                        if($hlistvalue->approve=="n"){
                                                            $hod_reject_flag=true;
                                                        }
                                                    }
                                                   if ($pce->csd_sign_status=="y") {                                                    
                                                        ?>
                                                        <a id="csd_a_<?echo $cur_time;?>" class="btn btn-inverse fancybox" data-fancybox-type="iframe" href="<?echo site_url("project/approve_pce_view/".$cur_time."/csd");?>"  ><i class="icon-ok icon-white"></i>CSD </a>
                                                        <?
                                                    }else if($pce->csd_sign_status=="n"){
                                                        ?>
                                                        <a id="csd_a_<?echo $cur_time;?>" class="btn btn-danger fancybox" data-fancybox-type="iframe" href="<?echo site_url("project/approve_pce_view/".$cur_time."/csd");?>"  ><i class="icon-remove icon-white"></i>CSD </a>
                                                        <?
                                                    }else{
                                                        ?>
                                                        <a id="csd_a_<?echo $cur_time;?>" class="btn fancybox" data-fancybox-type="iframe" href="<?echo site_url("project/approve_pce_view/".$cur_time."/csd");?>" >CSD </a>
                                                        <?
                                                    }
                                                    if($hod_reject_flag){
                                                        ?>
                                                        <a id="hod_a_<?echo $cur_time;?>" class="btn btn-danger fancybox" data-fancybox-type="iframe" href="<?echo site_url("project/approve_pce_view/".$cur_time."/hod");?>" ><i class="icon-remove icon-white"></i>HOD </a>
                                                        <?
                                                    }else if (isset($pce->hod_list[$user_data->username])&&$pce->hod_list[$user_data->username]->approve=="y") {
                                                        ?>
                                                        <a id="hod_a_<?echo $cur_time;?>" class="btn btn-inverse fancybox" data-fancybox-type="iframe" href="<?echo site_url("project/approve_pce_view/".$cur_time."/hod");?>" ><i class="icon-ok icon-white"></i>HOD </a>
                                                        <?
                                                    }else if($hod_all_approve){
                                                        ?>
                                                        <a id="hod_a_<?echo $cur_time;?>" class="btn btn-inverse fancybox" data-fancybox-type="iframe" href="<?echo site_url("project/approve_pce_view/".$cur_time."/hod");?>" ><i class="icon-ok icon-white"></i>HOD </a>
                                                        <?
                                                    }else{
                                                        ?>
                                                        <a id="hod_a_<?echo $cur_time;?>" class="btn fancybox" data-fancybox-type="iframe" href="<?echo site_url("project/approve_pce_view/".$cur_time."/hod");?>">HOD </a>
                                                        <?
                                                    }
                                                    
                                                    if ($pce->fc_sign_status=="y") {                                                    
                                                        ?>
                                                        <a id="fc_a_<?echo $cur_time;?>" class="btn btn-inverse fancybox" data-fancybox-type="iframe" href="<?echo site_url("project/approve_pce_view/".$cur_time."/fc");?>"  ><i class="icon-ok icon-white"></i>FC </a>
                                                        <?
                                                    }else if($pce->fc_sign_status=="n"){
                                                        ?>
                                                        <a id="fc_a_<?echo $cur_time;?>" class="btn btn-danger fancybox" data-fancybox-type="iframe" href="<?echo site_url("project/approve_pce_view/".$cur_time."/fc");?>"  ><i class="icon-remove icon-white"></i>FC </a>
                                                        <?
                                                    }else{
                                                        ?>
                                                        <a id="fc_a_<?echo $cur_time;?>" class="btn fancybox" data-fancybox-type="iframe" href="<?echo site_url("project/approve_pce_view/".$cur_time."/fc");?>" >FC </a>
                                                        <?
                                                    }
                                                    ?>
                                                </td></tr>
                                            </table>       
                                        </div>
                                        <div id="outsource-but_<?=$cur_time?>" class="span12 no-margin-left outsource-cls">
                                            <table class="table table-noborder">
                                                <tr>
                                                    <td style="text-align:center">
                                                       <a id="out_but_<?=$cur_time?>" iden="<?=$cur_time?>" togStat="hide" href="javascript:;" class="btn btn-large btn-block toggle_outsource">Out source</a>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div id="outsource_<?echo $cur_time;?>" class="span12 no-margin-left" style="display:none">
                                            <div class="span12 no-margin-left">
                                                <h5>Out Source</h5>
                                                <h5>PCE# <?=$pce->pce_no?></h5>
                                                <div>
                                                    <?=$pce->pce_des?>
                                                </div>
                                            </div>
                                            <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered orange lowfont">
                                                <thead>
                                                    <tr>
                                                        <th>QT#</th>
                                                        <th></th>
                                                        <th>description</th>
                                                        <th>Cost</th>
                                                        <th>Charge</th>
                                                        <th>Margin</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="table_out_<?=$cur_time?>">
                                                <?
                                                foreach ($pce->outsource as $outsource_key => $outsource) {
                                                    $up_time=$outsource->id;
                                                    ?>

                                                    <tr class="out_<?=$cur_time?>" iden="<?=$cur_time?>">
                                                        <td><input class="" type="text" name="qt_no[<?=$cur_time?>][]" value="<?=$outsource->qt_no?>">
                                                        <input class="" type="hidden" name="qt_id[<?=$cur_time?>][]" value="<?=$outsource->id?>">
                                                        </td>
                                                        <td>
                                                            <div class="control-group">
                                                            <?
                                                                if ($outsource->filename!="no_file") {
                                                                    ?>
                                                                    <a id="qt_view_<?=$up_time?>" href="<?echo site_url("media/real_pdf/".$outsource->filename)?>" target="_blank">ดูไฟล์</a>
                                                                    <?
                                                                }else{
                                                                    ?>
                                                                    <a id="qt_view_<?=$up_time?>" href="" target="_blank"></a>
                                                                    <?
                                                                }
                                                                ?>
                                                                <span class="btn btn-success fileinput-button">
                                                                    
                                                                    <i class="glyphicon glyphicon-plus"></i>
                                                                    <span id="but_upload_<?=$cur_time?>">เลือกไฟล์ PDF</span>
                                                                     <!-- The file input field used as target for the file upload widget -->
                                                                    <input id="qtupload_<?=$up_time?>" type="file">
                                                                    <input id="qt_file_<?=$up_time?>" type="hidden" name="qt_filename[<?=$cur_time?>][]" value="old__<?=$outsource->filename?>">
                                                                </span>
                                                                <script type="text/javascript">
                                                                    $('#qtupload_<?=$up_time?>').fileupload({
                                                                            previewThumbnail: false,
                                                                            url: '<?php echo site_url('upload_handler/pdf '); ?>',
                                                                            dataType: 'json',
                                                                            beforeSend: function() {
                                                                                $('#but_upload_<?=$up_time?>').html("Please wait");
                                                                                $('#qtupload_<?=$up_time?>').attr("disabled","");
                                                                            },
                                                                            done: function(e, data) {
                                                                                //console.log(data);

                                                                                $.each(data.result.files, function(index, file) {
                                                                                    //console.log(file);
                                                                                    if (file.error == "File is too big") {
                                                                                        alert("File is too big exceed 100 MB");
                                                                                        $("#qt_file_<?=$up_time?>").val("");
                                                                                    }else if (file.error == "Filetype not allowed") {
                                                                                        alert("Filetype not allowed");
                                                                                       $("#qt_file_<?=$up_time?>").val("");
                                                                                    } else {
                                                                                        alert("Upload Complete file " + file.name);
                                                                                        $("#qt_file_<?=$up_time?>").val(file.name);
                                                                                        $('#qt_view_<?=$up_time?>').attr("href",'<?echo site_url("media/temp/")?>/'+file.name);
                                                                                        $('#qt_view_<?=$up_time?>').html("ดูไฟล์");
                                                                                    }
                                                                                    $('#but_upload_<?=$up_time?>').html("เลือกไฟล์ PDF");

                                                                                    $('#qtupload_<?=$up_time?>').removeAttr("disabled");
                                                                                });

                                                                            },
                                                                            progressall: function(e, data) {
                                                                            }
                                                                        }).prop('disabled', !$.support.fileInput)
                                                                        .parent().addClass($.support.fileInput ? undefined : 'disabled');
                                                                </script>
                                                            </div>
                                                        </td>
                                                        <td><input class="" type="text" name="qt_des[<?=$cur_time?>][]" value="<?=$outsource->qt_des?>"></td>
                                                        <td><input class="out_change" type="text" name="qt_cost[<?=$cur_time?>][]" value="<?=$outsource->qt_cost?>"></td>
                                                        <td><input class="out_change" type="text" name="qt_charge[<?=$cur_time?>][]" value="<?=$outsource->qt_charge?>"></td>
                                                        <td class="out_margin"><?echo number_format("".($outsource->qt_charge-$outsource->qt_cost)/$outsource->qt_cost*100,2);?></td>
                                                        <td>
                                                            <a iden="<?=$up_time?>" href="javascript:;" class="btn btn-danger del_outlist"><i class="icon-remove icon-white"></i></a>                          
                                                        </td>
                                                    </tr>
                                                    <?
                                                }
                                                ?>
                                                    <tr id="be_out_<?=$cur_time?>">
                                                        <td></td>
                                                        <td></td>
                                                        <td>Grand Total</td>
                                                        <td class="total_cost"></td>
                                                        <td class="total_charge"></td>
                                                        <td class="total_margin"></td>
                                                        <td>
                                                            <a iden="<?=$cur_time?>" href="javascript:;" class="btn btn-success add_out_list"><i class="icon-plus icon-white"></i></a>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div id="del-but_<?=$cur_time?>" class="span12 no-margin-left">
                                            <table class="table table-noborder">
                                                <tr>
                                                    <td style="text-align:center">
                                                       <a id="" href="javascript:;" iden="<?=$cur_time?>" class="btn btn-danger pce_delete">DELETE PCE</a>   
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                        <script type="text/javascript">
                                        cal_outSource('<?=$cur_time?>');
                                        </script>
                                    <?
                                }
	}

	public function ajax_add_oc_html(){
		$pce=$this->m_pce->get_pce_by_id($_POST['oc_pce']);
		$oc_id=$this->m_oc->generate_id();
		$cur_time=$oc_id;
		$filename=$this->m_oc->handle_file_oc($_POST['oc_file'],$oc_id);
		$filename_pce=$this->m_oc->handle_file_pce_final($_POST['oc_pce_file'],$oc_id);
		$data = array(
			'id' => $oc_id,
			'oc_no' => $_POST['oc_no'], 
			'filename' => $filename, 
			'filename_pce' => $filename_pce, 
			'pce_id' => $_POST['oc_pce'], 
			'project_id' => $pce->project_id, 
			'oc_des' => $_POST['oc_des'], 
			'oc_amount' => (int)$_POST['oc_amount'], 
			);
		$this->m_oc->add_oc($data);
		$oc=$this->m_oc->get_oc_by_id($oc_id);
		$oc->pce=$this->m_pce->get_pce_by_id($oc->pce_id);
		?>
		<div id="oc_cur_<?echo $oc_id;?>" class="span12 no-margin-left">
            <table class="table table-noborder">
                <tr>
                    <td>
                        <a class="btn-atom btn-atom-success fancybox" data-fancybox-type="iframe" href="<?=site_url("project/oc_payment/".$oc_id)?>">Billing due <i class="icon-plus icon-white"></i></a>
                        
                    </td>
                    <td>
                        <?
                    if ($oc->is_done=="n") {
                        ?>
                        <a id="" href="javascript:;" iden="<?=$oc_id?>" stat="Done" class="btn-atom btn-atom-info oc_done">Not Done</a>
                        <?
                    }else{
                        ?>
                        <a id="" href="javascript:;" iden="<?=$oc_id?>" stat="Undone" class="btn-atom btn-atom-info oc_done">Done</a>
                        <?
                    }
                    ?>
                    </td>
                    <td>
                        <a id="" href="javascript:;" iden="<?=$oc_id?>" class="btn-atom btn-atom-danger oc_delete">DELETE</a>
                    </td>
                    <tr><td class="first-ta">OC#</td><td colspan="2" style="text-align: left;">
                    <a href="<?echo site_url("project/view_sign_oc/".$oc->id)?>" target="_blank"><?=$oc->oc_no?></a>&nbsp;|&nbsp;
                    <a href="<?echo site_url("media/real_pdf/".$oc->filename_pce)?>" target="_blank"><?=$oc->pce->pce_no?></a>   
                    </td></tr>
                    <tr><td class="first-ta">Description#</td><td colspan="2" style="text-align: left;">
                    <?=$oc->oc_des?>
                    </td></tr>
                    <tr><td class="first-ta">Amount#</td><td colspan="2" style="text-align: left;">
                    <?=number_format($oc->oc_amount, 2, '.', ',')?>
                    </td></tr>
                    <td></td><td colspan="2" style="text-align: left;">
                        <?
                            if ($oc->status=="y") {
                                   
                                    ?>
                                    <a id="fc_oc_<?echo $oc_id;?>" href="<?echo site_url("project/approve_oc_view/".$oc_id);?>" class="btn btn-inverse fancybox" data-fancybox-type="iframe"><i class="icon-ok icon-white"></i>FC </a>
                                    <?
                                }else if($oc->status=="n"){
                                    ?>
                                    <a id="fc_oc_<?echo $oc_id;?>" href="<?echo site_url("project/approve_oc_view/".$oc_id);?>" class="btn btn-danger fancybox" data-fancybox-type="iframe"><i class="icon-remove icon-white"></i>FC </a>
                                    <?
                                }else{
                                    ?>
                                    <a id="fc_oc_<?echo $oc_id;?>" href="<?echo site_url("project/approve_oc_view/".$oc_id);?>" class="btn fancybox" data-fancybox-type="iframe">FC </a>                 
                                    <?
                                }
                            ?>
                    </td></tr>
                </tr>
                <tr>
                    <td colspan="3"><hr id="hr_<?echo $oc_id;?>"></td>
                </tr>
            </table>    
        </div>		
		<?

	}
	public function ajax_add_outlist_html(){
		$cur_time=$_POST['cur_time'];
		$up_time=$this->m_stringlib->uniqueAlphaNum10();
		?>
					<tr class="out_<?=$cur_time?>" iden="<?=$cur_time?>">
		                <td><input class="" type="text" name="qt_no[<?=$cur_time?>][]"></td>
		                <td>
		                	<div class="control-group">
		                		<a id="qt_view_<?=$up_time?>" href="" target="_blank"></a>
                                <span class="btn btn-success fileinput-button">
                                	
                                    <i class="glyphicon glyphicon-plus"></i>
                                    <span id="but_upload_<?=$cur_time?>">เลือกไฟล์ PDF</span>
                                     <!-- The file input field used as target for the file upload widget -->
                                    <input id="qtupload_<?=$up_time?>" type="file">
                                    <input id="qt_file_<?=$up_time?>" type="hidden" name="qt_filename[<?=$cur_time?>][]">
                                </span>
                                <script type="text/javascript">
								    $('#qtupload_<?=$up_time?>').fileupload({
								            previewThumbnail: false,
								            url: '<?php echo site_url('upload_handler/pdf '); ?>',
								            dataType: 'json',
								            beforeSend: function() {
								                $('#but_upload_<?=$up_time?>').html("Please wait");
								                $('#qtupload_<?=$up_time?>').attr("disabled","");
								            },
								            done: function(e, data) {
								                //console.log(data);

								                $.each(data.result.files, function(index, file) {
								                    //console.log(file);
								                    if (file.error == "File is too big") {
								                        alert("File is too big exceed 100 MB");
								                        $("#qt_file_<?=$up_time?>").val("");
								                    }else if (file.error == "Filetype not allowed") {
								                        alert("Filetype not allowed");
								                       $("#qt_file_<?=$up_time?>").val("");
								                    } else {
								                        alert("Upload Complete file " + file.name);
								                        $("#qt_file_<?=$up_time?>").val(file.name);
								                        $('#qt_view_<?=$up_time?>').attr("href",'<?echo site_url("media/temp/")?>/'+file.name);
								                        $('#qt_view_<?=$up_time?>').html("ดูไฟล์");
								                    }
								                    $('#but_upload_<?=$up_time?>').html("เลือกไฟล์ PDF");

								                	$('#qtupload_<?=$up_time?>').removeAttr("disabled");
								                });

								            },
								            progressall: function(e, data) {
								            }
								        }).prop('disabled', !$.support.fileInput)
								        .parent().addClass($.support.fileInput ? undefined : 'disabled');
                                </script>
                            </div>
                        </td>
		                <td><input class="" type="text" name="qt_des[<?=$cur_time?>][]"></td>
		                <td><input class="out_change" type="text" name="qt_cost[<?=$cur_time?>][]"></td>
		                <td><input class="out_change" type="text" name="qt_charge[<?=$cur_time?>][]"></td>
		                <td class="out_margin"></td>
		                <td>
		                    <a iden="<?=$cur_time?>" href="javascript:;" class="btn btn-danger del_outlist"><i class="icon-remove icon-white"></i></a>		                    
		                </td>
		            </tr>
		<?
	}
	public function check_get_complete_button(){
		$project=$this->m_project->get_project_by_id($_POST['id']);
		?>
		<font class="head-pull-right"><?=$project->status?> </font>
            <?
            if ($project->status!="Done"&&$project->status!="Cancel"&&$project->status!="Archive") {
                
                //$check_hod_assign=$this->m_project->check_hod_assign_resource($project->project_id);
                $check_all_oc_done=$this->m_oc->check_all_done_oc_by_project_id($project->project_id);
                if ($check_all_oc_done) {
                    ?>
                    <a href="javascript:done_job();" class="btn">Complete</a>
            <?
                }
            
                            ?>
            <a href="javascript:cancel_job();" class="btn">Cancel</a>
            <?
        	}else if($project->status=="Done"){
                ?>
                <a href="javascript:archive_job();" class="btn">Archive</a>
                <?
            }
                        
	}
	public function done_oc(){
		header('Content-Type: application/json');
		$json = array();
		$json['flag']="OK";
		$oc_id=$_POST['id'];
		$no_prem="Don't have premmision";
		//$check_paid=$this->m_project->check_biling_paid_by_oc($oc_id);
			if (isset($this->user_data->prem['cs'])) {
				//if ($check_paid) {
					$oc_obj=$this->m_oc->get_oc_by_id($oc_id);
                    $project=$this->m_project->get_project_by_id($oc_obj->project_id);
                    if (($project->status=="Done"||$project->status=="Archive")&&$oc_obj->is_done=="y") {
                        $json['flag']="Project มีสถานะเป็น Conplete หรือ Archive ไม่สามารถ Undone OC ได้";
                    }else{
    					$stat="y";
    					$json['stat']="Done";
    					$json['stat2']="Undone";
    					if ($oc_obj->is_done=="y") {
    						$stat="n";
    						$json['stat']="Not Done";
    						$json['stat2']="Done";
    					}
    					$oc_dat = array(
    							'is_done' => $stat,
    							);
    					$this->m_oc->update_oc($oc_dat,$oc_id);
                    }
				//}else{
					//$json['flag']="OC นี้ ยังรับเงินไม่ครบตามจำนวน";
				//}
				
			}else{
				$json['flag']=$no_prem;
			}
		echo json_encode($json);
	}
	public function check_oc_bill_date_ready(){
		header('Content-Type: application/json');
		$json = array();
		$json['flag']="pennding";
		$oc_id=$_POST['save'];
		$no_prem="Don't have premmision";
			if (isset($this->user_data->prem['cs'])) {
				$bill_ready=$this->m_project->check_biling_date_ready_by_oc($oc_id);
				if ($bill_ready) {
					$json['flag']="OK";
				}else{
					$json['flag']="Sum billing amount must not less than OC amount";
				}
			}else{
				$json['flag']=$no_prem;
			}
		echo json_encode($json);
	}
	public function check_pay_outsource_date_ready(){
		header('Content-Type: application/json');
		$json = array();
		$json['flag']="pennding";
		$out_id=$_POST['save'];
		$no_prem="Don't have premmision";
			if (isset($this->user_data->prem['cs'])) {
				$bill_ready=$this->m_project->check_pay_date_ready_by_out_id($out_id);
				if ($bill_ready) {
					$json['flag']="OK";
				}else{
					$json['flag']="Payment amount must equal to outsource cost";
				}
			}else{
				$json['flag']=$no_prem;
			}
		echo json_encode($json);
	}
	public function check_project_bill_date_ready(){
		header('Content-Type: application/json');
		$json = array();
		$json['flag']="pennding";
		$project_id=$_POST['save'];
		$no_prem="Don't have premmision";
			if (isset($this->user_data->prem['cs'])) {					
				if (isset($_POST['oc_del_list'])) {
					foreach ($_POST['oc_del_list'] as $key => $value) {
						$this->m_oc->delete_oc($value);
					}
				}
				$bill_ready1=$this->m_project->check_biling_date_ready_by_project($project_id);
				$bill_ready2=$this->m_project->check_pay_date_ready_by_project($project_id);
				if ($bill_ready1&&$bill_ready2) {
					$json['flag']="OK";
				}else{
					$json['flag']="";
					if (!$bill_ready1) {
						$json['flag'].="All OC billing amount must not less than OC amount \n";
					}
					if (!$bill_ready2) {
						$json['flag'].="All Payment amount must equal to outsource cost";
					}
					
				}
			}else{
				$json['flag']=$no_prem;
			}
		echo json_encode($json);
	}




    // manual function ///////////

	public function see_post(){
		print_r($_POST);
	}
    public function clear_account_unit(){
        $g_list = array();
        $query = $this->db->get('project');
        
        if ($query->num_rows() > 0) {
            $g_list = $query->result();
        }
        foreach ($g_list as $key => $value) {
            $data = array('account_unit_id' => $value->business_unit_id);
            $this->m_project->update_project($data,$value->project_id);
        }
        echo "complete";
    }
    public function delete_bill_under_rewrite_pce(){
        $oc_list=$this->m_oc->get_all_oc(true,true);
        foreach ($oc_list as $key => $value) {
            $this->m_oc->delete_oc_bill_by_oc_id($value->id);
        }
    }
    public function del_oc_by_id(){
        $id=$this->uri->segment(3,'');
        $this->m_oc->delete_oc($id);
    }
    public function delete_outsource_under_rewrite_pce(){
        $pce_list=$this->m_pce->get_all_pce("y");
        ?>
        <h1>deleted outsource</h1>
        <table>
        <tr>
            <td>
                Project Name
            </td>
            <td>
                PCE ID
            </td>
            <td>
                pce_no
            </td>
            <td>
                pce_amount
            </td>
            <td>
                rewrite_stat
            </td>
            <td>
                rewrite_by
            </td>
            <td>
                qt_no
            </td>
            <td>
                qt_cost
            </td>
            <td>
                qt_charge
            </td>
        </tr>
        <?
        foreach ($pce_list as $key => $value) {
            $project=$this->m_project->get_project_by_id($value->project_id);
            foreach ($value->outsource as $key2 => $value2) {
                ?>
                <tr>
                    <td><?=$project->project_name?></td>
                    <td><?=$value->id?></td>
                    <td><?=$value->pce_no?></td>
                    <td><?=$value->pce_amount?></td>
                    <td><?=$value->rewrite_stat?></td>
                    <td><?=$value->rewrite_by?></td>
                    <td><?=$value2->qt_no?></td>
                    <td><?=$value2->qt_cost?></td>
                    <td><?=$value2->qt_charge?></td>
                </tr>
                <?
                $this->m_outsource->delete_outsource($value2->id);
            }
        }
        ?>
        </table>
        <?
    }



}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */