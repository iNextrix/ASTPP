<?php

class Package_model extends CI_Model {

    function Package_model() {
        parent::__construct();
    }

    function getpackage_list($flag, $start = 0, $limit = 0) {
        $this->db_model->build_search('package_list_search');
        if ($this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 5) {
            $account_data = $this->session->userdata("accountinfo");
            $where = array("reseller_id" => $account_data['id'], "status" => "1");
        } else {
            $where = array("status" => "1","reseller_id"=>"0");
        }
        if ($flag) {
            $query = $this->db_model->getSelect("*", "packages", $where, "id", "ASC", $limit, $start);
        } else {
            $query = $this->db_model->countQuery("*", "packages", $where);
        }
        return $query;
    }

    function add_package($add_array) {
        if ($this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 5) {
            $account_data = $this->session->userdata("accountinfo");
            $add_array["reseller_id"] = $account_data['id'];
        }
        unset($add_array["action"]);
        $this->db->insert("packages", $add_array);
        return true;
    }

    function edit_package($data, $id) {
        unset($data["action"]);
        if ($this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 5) {
            $account_data = $this->session->userdata("accountinfo");
            $add_array["reseller_id"] = $account_data['id'];
        }
        $this->db->where("id", $id);
        $this->db->update("packages", $data);
        return true;
    }

    function remove_package($id) {
        $this->db->where("id", $id);
        $this->db->delete("packages");
        return true;
    }

    function getpackage_counter_list($flag, $start = 0, $limit = 0) {
        $this->db_model->build_search('package_list_search');
        if ($flag) {
            $query = $this->db_model->getSelect("*", "counters", "", "id", "desc", $limit, $start);
        } else {
            $query = $this->db_model->countQuery("*", "counters", "");
        }
        return $query;
    }
    function insert_package_pattern($data, $packageid) {
        $data = explode(",", $data);
        $tmp = array();
        foreach ($data as $key => $data_value) {
            $tmp[$key]["package_id"] = $packageid;
            $tmp[$key]["patterns"] = $this->get_pattern_by_id($data_value);
        }
        return $this->db->insert_batch("package_patterns", $tmp);
    }

    function get_pattern_by_id($pattern) {
        $patterns = $this->db_model->getSelect("pattern", "routes", array("id" => $pattern));
        $patterns = $patterns->result_array();
        return $patterns[0]['pattern'];
    }


}
