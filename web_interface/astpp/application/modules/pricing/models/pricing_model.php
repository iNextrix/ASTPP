<?php

class pricing_model extends CI_Model {

    function pricing_model() {
        parent::__construct();
    }

    function getpricing_list($flag, $start = 0, $limit = 0) {
        $this->db_model->build_search('price_list_search');
        if ($this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 5) {
            $account_data = $this->session->userdata("accountinfo");
            $reseller = $account_data['id'];
            $where = array("reseller_id" => $reseller, "status <>" => "2");
        } else {
            $where = array("reseller_id" => "0", "status != " => "2");
        }
        if ($flag) {
            $query = $this->db_model->Select("*", "pricelists", $where, "id", "ASC", $limit, $start);
        } else {
            $query = $this->db_model->countQuery("*", "pricelists", $where);
        }
        return $query;
    }

    function add_price($add_array) {
        unset($add_array["action"]);
        if ($this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 5) {
            $account_data = $this->session->userdata("accountinfo");
            $add_array["reseller_id"] = $account_data['id'];
        } else {
            $add_array["reseller_id"] = "0";
        }
        $this->db->insert("pricelists", $add_array);
        return true;
    }

    function edit_price($data, $id) {
        unset($data["action"]);
        $this->db->where("id", $id);
        $this->db->update("pricelists", $data);
        return true;
    }

    function get_price_list_for_cdrs() {
        if ($this->session->userdata('username') != "" && $this->session->userdata('logintype') != 2) {
            $this->db->where('reseller', $this->session->userdata('username'));
        } else {
            $this->db->where(array('reseller' => "0"));
        }
        $this->db->where('status <', 2);
        $this->db->order_by('name', 'desc');
        $query = $this->db->get("pricelists");
        $price_list = array();
        $result = $query->result_array();
        foreach ($result as $row) {
            $price_list[$row['name']] = $row['name'];
        }
        return $price_list;
    }

}
