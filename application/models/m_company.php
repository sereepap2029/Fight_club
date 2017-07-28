<?php
class M_company extends CI_Model
{
    
    public function __construct() {
        parent::__construct();
        $this->load->model("m_stringlib");
    }
    
    function generate_id() {
        $isuniq = FALSE;
        $clam_id = '';
        do {
            $temp_id = $this->m_stringlib->uniqueAlphaNum10();
            $query = $this->db->get_where('company', array('id' => $temp_id));
            if ($query->num_rows() == 0) {
                $clam_id = $temp_id;
                $isuniq = TRUE;
            }
        } while (!$isuniq);
        
        return $clam_id;
    }
    function delete_company($id) {
        $this->delete_bu_by_company_id($id);
        $this->db->where('id', $id);
        $this->db->delete('company');
    }
    function delete_bu($id) {
        $this->db->where('id', $id);
        $this->db->delete('company_has_bu');
    }
    function delete_bu_by_company_id($id) {
        $this->db->where('company_id', $id);
        $this->db->delete('company_has_bu');
    }

    function add_company($data) {
        $this->db->insert('company', $data);
    }
    function add_bu($data) {
        $this->db->insert('company_has_bu', $data);
    }
    function update_bu($data, $id) {
        $this->db->where('id', $id);
        $this->db->update('company_has_bu', $data);
    }
    function update_company($data, $id) {
        $this->db->where('id', $id);
        $this->db->update('company', $data);
    }
    function get_all_company() {
        $g_list = array();
        $this->db->order_by("name", "asc");
        $query = $this->db->get('company');
        
        if ($query->num_rows() > 0) {
            $g_list = $query->result();
        }
        return $g_list;
    }
    function get_bu_by_company_id($id) {
        $g_list = array();
        $this->db->where('company_id', $id);
        $this->db->order_by("bu_name", "asc");
        $query = $this->db->get('company_has_bu');
        
        if ($query->num_rows() > 0) {
            $g_list = $query->result();
        }
        return $g_list;
    }
    function get_all_bu() {
        $g_list = array();
        $this->db->order_by("bu_name", "asc");
        $query = $this->db->get('company_has_bu');
        
        if ($query->num_rows() > 0) {
            $g_list = $query->result();
        }
        return $g_list;
    }

    function get_bu_by_id($id) {
        $g_list = new stdClass();
        $this->db->where('id', $id);
        $query = $this->db->get('company_has_bu');
        
        if ($query->num_rows() > 0) {
            $g_list = $query->result();
            $g_list = $g_list[0];
            //$g_list->bu=$this->get_bu_by_company_id($id);
        }
        return $g_list;
    }

    function get_company_by_id($id) {
        $business = new stdClass();
        $query = $this->db->get_where('company', array('id' => $id));
        
        if ($query->num_rows() > 0) {
            $business = $query->result();
            $business = $business[0];
            $business->bu=$this->get_bu_by_company_id($id);
        }
        return $business;
    }
}
