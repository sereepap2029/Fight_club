<?php
class M_department extends CI_Model
{
    
    public function __construct() {
        parent::__construct();
        $this->load->model("m_stringlib");
        $this->load->model("m_business");
    }
    
    function generate_id() {
        $isuniq = FALSE;
        $clam_id = '';
        do {
            $temp_id = $this->m_stringlib->uniqueAlphaNum10();
            $query = $this->db->get_where('department', array('id' => $temp_id));
            if ($query->num_rows() == 0) {
                $clam_id = $temp_id;
                $isuniq = TRUE;
            }
        } while (!$isuniq);
        
        return $clam_id;
    }
    function delete_department($id) {
        $this->db->where('id', $id);
        $this->db->delete('department');
    }
    function add_department($data) {
        $this->db->insert('department', $data);
    }
    function update_department($data, $id) {
        $this->db->where('id', $id);
        $this->db->update('department', $data);
    }
    function get_all_department($bu_id="no") {
        $g_list = array();
        $this->db->order_by("name", "asc");
        if ($bu_id!="no") {
            $this->db->where('business_id', $bu_id);
        }
        $query = $this->db->get('department');
        
        if ($query->num_rows() > 0) {
            $g_list = $query->result();
            if ($bu_id=="no") {
                foreach ($g_list as $key => $value) {
                    $g_list[$key]->bu=$this->m_business->get_business_by_id($value->business_id);
                    if (!isset($g_list[$key]->bu->name)) {
                        $g_list[$key]->bu->name="not selected";
                    }
                }
            }
            
        }
        return $g_list;
    }

    function get_department_by_id($id) {
        $department = new stdClass();
        $query = $this->db->get_where('department', array('id' => $id));
        
        if ($query->num_rows() > 0) {
            $department = $query->result();
            $department = $department[0];
            $department->bu=$this->m_business->get_business_by_id($department->business_id);
                if (!isset($department->bu->name)) {
                    $department->bu->name="not selected";
                }
        }
        return $department;
    }
}
