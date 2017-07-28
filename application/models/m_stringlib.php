<?php
class M_stringlib extends CI_Model {
 
    public function __construct(){
	date_default_timezone_set('Asia/Bangkok');
        parent::__construct();
	}
	
	public function useMD5($str1, $str2) {
		return hash("MD5", $str1 . $str2, FALSE);
	}
	
	public function uniqueNum10 () {
		$random = substr(number_format(time() * rand(),0,'',''),0,10);
		return $random;
	}
	
	public function uniqueNum6 () {
		$random = substr(number_format(time() * rand(),0,'',''),0,6);
		return $random;
	}
	public function uniqueAlphaNum6 () {
		$random = substr(md5(time() * rand()),0,6);
		return $random;
	}
	
	public function uniqueAlphaNum10 () {
		$random = substr(md5(time() * rand()),0,10);
		return $random;
	}
	
	public function uniqueAlphaNum20 () {
		$random = substr(md5(time() * rand()),0,20);
		return $random;
	}
	
	public function isBlank ($str) {
		
	}
	
	
	
}
?>