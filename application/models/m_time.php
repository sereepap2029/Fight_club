<?php
class M_time extends CI_Model {
 
  public function __construct(){
    parent::__construct();
  		$this->load->model("m_stringlib");  		
	}		
	function datepicker_to_unix ($data) {
		@$str=explode("/", $data);
		return @mktime(0,0,1,(int)$str[1],(int)$str[0],((int)$str[2]));
	}
	function unix_to_datepicker ($data) {
		$date_string=date("d/m/Y",$data);
		$date_array=explode("/", $date_string);
		$true_date=$date_array[0]."/".$date_array[1]."/".((int)$date_array[2]);
		if ($data<1000000) {
			$true_date="";
		}
		return $true_date;
	}
	function unix_to_datepicker_reverse ($data) {
		$date_string=date("d/m/Y",$data);
		$date_array=explode("/", $date_string);
		$true_date=((int)$date_array[2])."/".$date_array[1]."/".$date_array[0];
		if ($data<1000000) {
			$true_date="";
		}
		return $true_date;
	}
	function unix_to_datetimepicker ($data) {
		$date_string=date("Y/m/d H:i",$data);
		@$str1=explode(" ", $date_string);
		@$str2=explode("/", $str1[0]);
		@$str3=explode(":", $str1[1]);
		@$true_date=((int)$str2[0])."/".$str2[1]."/".$str2[2]." ".$str1[1];
		return $true_date;
	}
	function datetimepicker_to_unix ($data) {

		@$str1=explode(" ", $data);
		@$str2=explode("/", $str1[0]);
		@$str3=explode(":", $str1[1]);
		return @mktime((int)$str3[0],(int)$str3[1],1,(int)$str2[1],(int)$str2[2],((int)$str2[0]));
	}
	function get_amount_day($start,$end){
		$lenght_time=(int)(($end-$start)/(60*60*24));
		$mod_time=(int)(($end-$start)%(60*60*24));
		if ($mod_time>0) {
			$lenght_time+=1;
		}
		return $lenght_time;
	}
	function get_end_month($time){
		return @mktime(23,59,59,(int)date("n",$time),(int)date("t",$time),((int)date("Y",$time)));
	}
	function get_start_month($time){
		return @mktime(0,0,1,(int)date("n",$time),1,((int)date("Y",$time)));
	}
	function get_amount_weekend_day($start,$end){
		$start_day=(int)date("N",$start);
		$end_day=(int)date("N",$end);
		$amount_day=$this->get_amount_day($start,$end);
		$num_weekend=0;
		if ($start_day>1&&$start_day<6) {
			$amount_day-=(7-$start_day);
			$num_weekend+=2;
		}else if($start_day>=6){
			$amount_day-=(7-$start_day);
			$num_weekend+=(8-$start_day);
		}
		$num_weekend+=(int)((int)($amount_day/7)*2);
		if (($amount_day%7)==6) {
			$num_weekend+=1;
		}
		return $num_weekend;

	}
	
}