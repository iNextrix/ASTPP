<?php

class Charges_model extends CI_Model {

    function Charges_model() {
        parent::__construct();
    }

    function getcharges_list($flag, $start = 0, $limit = 0) {
        $this->db_model->build_search('charges_list_search');
        if ($this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 5) {
            $account_data = $this->session->userdata("accountinfo");
            $reseller = $account_data['id'];
            $where = array("reseller_id" => $reseller, "status" => "1");
        } else {
            $where = array("reseller_id" => "0", "status" => "1");
        }
        if ($flag) {
            $query = $this->db_model->select("*", "charges", $where, "id", "ASC", $limit, $start);
        } else {
            $query = $this->db_model->countQuery("*", "charges", $where);
        }
        return $query;
    }

    function add_charge($add_array) {
        if ($this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 5) {
            $account_data = $this->session->userdata("accountinfo");
            $add_array['reseller_id'] = $account_data['id'];
        } else {
            $add_array['reseller_id'] = "0";
        }

        unset($add_array['action']);
        $this->db->insert("charges", $add_array);
        return true;
    }

    function edit_charge($data, $id) {
        unset($data['action']);
        if ($this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 5) {
            $account_data = $this->session->userdata("accountinfo");
            $add_array['reseller_id'] = $account_data['id'];
        } else {
            $add_array['reseller_id'] = "0";
        }

        $this->db->where("id", $data["id"]);
        return $this->db->update("charges", $data);
    }

    function remove_charge($id) {
        $this->db->where("id", $id);
        $this->db->delete("charges");
        return true;
    }

}
