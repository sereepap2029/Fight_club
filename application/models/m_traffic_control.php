<?php
class M_traffic_control extends CI_Model
{
    
    public function __construct() {
        parent::__construct();
        $this->load->model("m_stringlib");
        $this->load->model("m_project");
        $this->load->model("m_oc");
        $this->load->model("m_pce");
        $this->load->model("m_outsource");
        $this->load->model("m_time");
    }
    function get_all_project_by_unit($unit,$start="all",$end="all") {
        $g_list = array();
        $this->db->where("business_unit_id", $unit);
        $where = "(status='WIP' OR status='Delay')";
        $this->db->where($where);
        if ($start!="all") {
            $this->db->where("project_start >=", $start);
        }
        if ($end!="all") {
            $this->db->where("project_start <=", $end);
        }
        $query = $this->db->get('project');
        
        if ($query->num_rows() > 0) {
            $g_list = $query->result();
        }
        return $g_list;
    }
}