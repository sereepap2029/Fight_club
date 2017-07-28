<?php
class M_business extends CI_Model
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
            $query = $this->db->get_where('business', array('id' => $temp_id));
            if ($query->num_rows() == 0) {
                $clam_id = $temp_id;
                $isuniq = TRUE;
            }
        } while (!$isuniq);
        
        return $clam_id;
    }
    function delete_business($id) {
        $this->db->where('id', $id);
        $this->db->delete('business');
    }
    function add_business($data) {
        $this->db->insert('business', $data);
    }
    function update_business($data, $id) {
        $this->db->where('id', $id);
        $this->db->update('business', $data);
    }
    function get_all_business() {
        $g_list = array();
        $this->db->order_by("name", "asc");
        $query = $this->db->get('business');
        
        if ($query->num_rows() > 0) {
            $g_list = $query->result();
        }
        return $g_list;
    }

    function get_business_by_id($id) {
        $business = new stdClass();
        $query = $this->db->get_where('business', array('id' => $id));
        
        if ($query->num_rows() > 0) {
            $business = $query->result();
            $business = $business[0];
        }
        return $business;
    }
}
