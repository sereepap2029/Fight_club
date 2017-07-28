<?php
class M_outsource extends CI_Model {
 
  public function __construct(){
    parent::__construct();
  		$this->load->model("m_stringlib");  		
	}	
	function generate_id()
	{
		$isuniq    = FALSE;
		$clam_id = '';
		do
		{
			$temp_id = $this->m_stringlib->uniqueAlphaNum10();
			$query = $this->db->get_where('outsource', array('id' => $temp_id));
			if ($query->num_rows() == 0)
			{
				$clam_id = $temp_id;
				$isuniq    = TRUE;
			}
		}
		while(!$isuniq);
	
		return $clam_id;
	}
	function get_outsource_bill_by_out_id ($outsource_id) {
		$g_list = array();
		$g_list2 = array();
		$this->db->order_by("time", "asc");
		$query = $this->db->get_where('outsource_bill', array('outsource_id' => $outsource_id));
		
		if ($query->num_rows() > 0) {
			$g_list = $query->result();
			foreach ($g_list as $key => $value) {
				$g_list2[$value->id]=$value;
				$g_list2[$value->id]->paid_obj=$this->get_outsource_bill_paid_by_outsource_bill_id($value->id);
			}
		}
		return $g_list2;
	}
	function get_outsource_bill_paid_by_outsource_bill_id ($bill_id,$paid="all") {
		$g_list = array();
		$g_list2 = array();
		$this->db->order_by("date", "asc");
		$this->db->where('bill_id', $bill_id);
		if ($paid!="all") {
			$this->db->where('paid', $paid);
		}
		$query = $this->db->get('outsource_bill_paid');
		
		if ($query->num_rows() > 0) {
			$g_list = $query->result();
			foreach ($g_list as $key => $value) {
				$g_list2[$value->id]=$value;
			}
		}
		return $g_list2;
	}
	function get_outsource_bill_paid_by_outsource_id ($id,$paid="all") {
		$g_list = array();
		$g_list2 = array();
		$this->db->order_by("date", "asc");
		$this->db->where('outsource_id', $id);
		if ($paid!="all") {
			$this->db->where('paid', $paid);
		}
		$query = $this->db->get('outsource_bill_paid');
		
		if ($query->num_rows() > 0) {
			$g_list = $query->result();
			foreach ($g_list as $key => $value) {
				$g_list2[$value->id]=$value;
			}
		}
		return $g_list2;
	}
	function get_outsource_bill_paid_by_time ($start,$end,$paid="all",$by_date="date",$order_col="date") {
		$g_list = array();
		$g_list2 = array();
		$this->db->order_by($order_col, "asc");
		$this->db->where($by_date.' <=', $end);
		$this->db->where($by_date.' >=', $start);
		if ($paid!="all") {
			$this->db->where('paid', $paid);
		}
		$query = $this->db->get('outsource_bill_paid');
		
		if ($query->num_rows() > 0) {
			$g_list = $query->result();			
		}
		return $g_list;
	}
	function get_outsource_bill_by_time ($start,$end,$paid="all",$by_pay_time=false) {
		$g_list = array();
		$g_list2 = array();
		$this->db->order_by("time", "asc");
		if ($by_pay_time) {
			$this->db->where('paid_date <=', $end);
			$this->db->where('paid_date >=', $start);
		}else{
			$this->db->where('time <=', $end);
			$this->db->where('time >=', $start);
		}		
		if ($paid!="all") {
			$this->db->where('paid', $paid);
		}
		
		$query = $this->db->get('outsource_bill');
		
		if ($query->num_rows() > 0) {
			$g_list = $query->result();
		}
		return $g_list;
	}
	function delete_outsource ($id) {
		$outs=$this->get_outsource_by_id($id);
		@unlink("./media/real_pdf/" . $outs->filename);
		$this->delete_outsource_bill_by_out_id($id);
		$this->delete_outsource_bill_paid_by_outsource_id($id);
		$this->db->where('id', $id);
		$this->db->delete('outsource');
	}
	function delete_outsource_bill_by_out_id ($id) {
		$this->db->where('outsource_id', $id);
		$this->db->delete('outsource_bill');
	}
	function delete_outsource_bill_by_id ($id) {
		$this->delete_outsource_bill_paid_by_outsource_bill_id($id);
		$this->db->where('id', $id);
		$this->db->delete('outsource_bill');
	}
	function delete_outsource_bill_paid_by_outsource_bill_id ($id) {
		$this->db->where('bill_id', $id);
		$this->db->delete('outsource_bill_paid');
	}
	function delete_outsource_bill_paid_by_outsource_id ($id) {
		$this->db->where('outsource_id', $id);
		$this->db->delete('outsource_bill_paid');
	}
	function add_outsource ($data) {
		$this->db->insert('outsource', $data);
	}
	function add_outsource_bill ($data) {
		$this->db->insert('outsource_bill', $data);
	}
	function add_outsource_bill_paid ($data) {
		$this->db->insert('outsource_bill_paid', $data);
	}
	function update_outsource($data, $id) {
		$this->db->where('id', $id);
		$this->db->update('outsource', $data);
	}
	function update_outsource_bill($data, $id) {
		$this->db->where('id', $id);
		$this->db->update('outsource_bill', $data);
	}
	function update_outsource_bill_paid($data, $id) {
		$this->db->where('id', $id);
		$this->db->update('outsource_bill_paid', $data);
	}
	function get_all_outsource_by_pce_id($pce_id,$get_bill_paid=false){
		$g_list = array();
		$this->db->where('pce_id', $pce_id);
		$this->db->order_by("qt_no", "asc");
		$query = $this->db->get('outsource');
		
		if ($query->num_rows() > 0) {
			$g_list = $query->result();
			if ($get_bill_paid) {
				foreach ($g_list as $key => $value) {
					$g_list[$key]->bill_paid=$this->get_outsource_bill_paid_by_outsource_id($value->id,"y");
				}
			}
		}
		return $g_list;

	}
	function get_outsource_by_id ($id) {
		$business = new stdClass();
		$query = $this->db->get_where('outsource', array('id' => $id));
		
		if ($query->num_rows() > 0) {
			$business = $query->result();
			$business = $business[0];
		}
		return $business;
	}
	function get_outsource_bill_by_id ($id) {
		$business = new stdClass();
		$query = $this->db->get_where('outsource_bill', array('id' => $id));
		
		if ($query->num_rows() > 0) {
			$business = $query->result();
			$business = $business[0];
		}
		return $business;
	}
	function handle_file($filename){
		$new_filename="no_file";
		if ($filename!="") {
			
			$id=$this->m_stringlib->uniqueAlphaNum10();
			$filename = $filename;
	        $ext = explode(".", $filename);
	        $new_ext = $ext[count($ext) - 1];
	        $new_filename = $id ."_".time()."." . $new_ext;
	        $file = './media/temp/' . $filename;
	        $newfile = './media/real_pdf/' . $new_filename;
	                    
	        if (!copy($file, $newfile)) {
	            $new_filename="error";            
	            @unlink("./media/temp/" . $filename);
	        } 
	        else {
	            @unlink("./media/temp/" . $filename);
	        }
	    }
        return $new_filename;
	}

	function valid_bill_outsource($bill_id){
		//$bill=$this->get_outsource_bill_by_id($bill_id);
		$bill_paid=$this->get_outsource_bill_paid_by_outsource_bill_id($bill_id,$paid="y");
		$amount=0;
		$all_paid=true;
		foreach ($bill_paid as $key => $value) {
			$amount+=$value->amount;
			if($value->paid=="n"){
				$all_paid=false;
			}
		}
		if ($amount>=0&&$all_paid) {
			$data = array(
				'paid_amount' => $amount, 
				'paid' => "y", 
				);
			$this->update_outsource_bill($data,$bill_id);
		}else{
			$data = array(
				'paid_amount' => $amount, 
				'paid' => "n", 
				);
			$this->update_outsource_bill($data,$bill_id);
		}
	}
	
}