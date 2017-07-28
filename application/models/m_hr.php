<?php
class M_hr extends CI_Model
{
    
    public function __construct() {
        parent::__construct();
        $this->load->model("m_stringlib");
    }
    
    function delete_holiday($time) {
        $this->db->where('time', $time);
        $this->db->delete('holiday');
    }
    function delete_leave($id) {
        $this->db->where('id', $id);
        $this->db->delete('user_leave');
    }
    function delete_leave_by_time($time_day) {
        $this->db->where('time_day', $time_day);
        $this->db->delete('user_leave');
    }
    function add_holiday($data) {
        $this->db->insert('holiday', $data);
    }
    function add_leave($data) {
        $this->db->insert('user_leave', $data);
    }
    function update_holiday($data, $time) {
        $this->db->where('time', $time);
        $this->db->update('holiday', $data);
    }
    function get_all_holiday($start,$end) {
        $g_list = array();
        $g_list2 = array();
        $this->db->order_by("time", "asc");
        $this->db->where('time >=', $start);
        $this->db->where('time <=', $end);
        $query = $this->db->get('holiday');
        
        if ($query->num_rows() > 0) {
            $g_list = $query->result();
            foreach ($g_list as $key => $value) {
                $g_list2[$value->time]=$value;
            }
        }
        return $g_list2;
    }
    function get_all_user_leave($start,$end,$usn="all") {
        $g_list = array();
        $g_list2 = array();
        $this->db->order_by("time_day", "asc");
        $this->db->where('time_day >=', $start);
        $this->db->where('time_day <=', $end);
        if ($usn!="all") {
            $this->db->where('usn', $usn);
        }
        $query = $this->db->get('user_leave');
        
        if ($query->num_rows() > 0) {
            $g_list = $query->result();
            foreach ($g_list as $key => $value) {
                $g_list2[$value->time_day][$value->usn]=$value;
            }
        }
        return $g_list2;
    }

    function get_holiday_by_time($time) {
        $business = new stdClass();
        $query = $this->db->get_where('holiday', array('time' => $time));
        
        if ($query->num_rows() > 0) {
            $business = $query->result();
            $business = $business[0];
        }
        return $business;
    }
}
