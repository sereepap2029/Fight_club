<?php
class M_position extends CI_Model
{
    
    public function __construct() {
        parent::__construct();
        $this->load->model("m_stringlib");
        $this->load->model("m_department");
        $this->load->model("m_hour_rate");
    }
    
    function generate_id() {
        $isuniq = FALSE;
        $clam_id = '';
        do {
            $temp_id = $this->m_stringlib->uniqueAlphaNum10();
            $query = $this->db->get_where('position', array('id' => $temp_id));
            if ($query->num_rows() == 0) {
                $clam_id = $temp_id;
                $isuniq = TRUE;
            }
        } while (!$isuniq);
        
        return $clam_id;
    }
    function delete_position($id) {
        $this->m_hour_rate->delete_hour_rate_has_position($id);
        $this->db->where('id', $id);
        $this->db->delete('position');
    }
    function add_position($data) {
        $this->db->insert('position', $data);
    }
    function update_position($data, $id) {
        $this->db->where('id', $id);
        $this->db->update('position', $data);
    }
    function get_all_position($depart_id="no") {
        $g_list = array();
        $g_list2 = array();
        $this->db->order_by("name", "asc");
        if ($depart_id!="no") {
            $this->db->where('department_id', $depart_id);
        }
        $query = $this->db->get('position');
        
        if ($query->num_rows() > 0) {
            $g_list = $query->result();
            if ($depart_id=="no") {
                foreach ($g_list as $key => $value) {
                    $g_list2[$value->id]=$value;
                    $g_list2[$value->id]->department=$this->m_department->get_department_by_id($value->department_id);
                    if (!isset($g_list2[$value->id]->department->name)) {
                        $g_list2[$value->id]->department->name="not selected";
                    }
                }
            }else{
                foreach ($g_list as $key => $value) {
                    $g_list2[$value->id]=$value;
                }
            }
        }
        return $g_list2;
    }

    function get_position_by_id($id) {
        $position = new stdClass();
        $query = $this->db->get_where('position', array('id' => $id));
        
        if ($query->num_rows() > 0) {
            $position = $query->result();
            $position = $position[0];
        }
        return $position;
    }
}
