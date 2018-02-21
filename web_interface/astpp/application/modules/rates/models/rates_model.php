<?php

class rates_model extends CI_Model {

    function rates_model() {
        parent::__construct();
    }

    function getoutbound_rates_list($flag, $start = 0, $limit = 0) {
        $this->db_model->build_search('terminationrates_list_search');
        if ($flag) {
            $query = $this->db_model->select("*", "outbound_routes", "", "id", "ASC", $limit, $start);
        } else {
            $query = $this->db_model->countQuery("*", "outbound_routes", "");
        }
        return $query;
    }

    function getinbound_rates_list($flag, $start = 0, $limit = 0) {
        $this->db_model->build_search('inboundrates_list_search');
        if ($this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 5) {
            $account_data = $this->session->userdata("accountinfo");
            $reseller = $account_data['id'];
            $where = array("reseller_id" => $reseller, "status" => "1");
        } else {
            $where = array("status" => "1",'reseller_id'=>'0');
        }
        if ($flag) {
            $query = $this->db_model->select("*", "routes", $where, "id", "ASC", $limit, $start);
        } else {
            $query = $this->db_model->countQuery("*", "routes", $where);
        }
        return $query;
    }
    function getunblocked_pattern_list($accountid,$flag, $start = 0, $limit = 0) {
        $this->db_model->build_search('inboundrates_list_search');
        if ($this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 5) {
            $account_data = $this->session->userdata("accountinfo");
            $reseller = $account_data['id'];
            $where = array("reseller_id" => $reseller, "status" => "1");
        } else {
            $where = array("status" => "1",'reseller_id'=>'0');
        }
        $where1 = '(pattern NOT IN (select blocked_patterns from block_patterns where accountid = "'.$accountid.'"))';
        $this->db->where($where1);        
        if ($flag) {
            $query = $this->db_model->select("*", "routes", $where, "id", "ASC", $limit, $start);
//            echo "<pre>"; print_r($query); exit;
        } else {
            $query = $this->db_model->countQuery("*", "routes", $where);
        }
        
        return $query;
    }

    function getinbound_rates_list_for_user($flag, $start = 0, $limit = 0) {
        $this->db_model->build_search('inboundrates_list_search');

        $account_data = $this->session->userdata("accountinfo");
        $where = array("pricelist_id" => $account_data["pricelist_id"]);

        $this->db_model->build_search('inboundrates_list_search');
        if ($flag) {
            $query = $this->db_model->select("*", "routes", $where, "id", "ASC", $limit, $start);
        } else {
            $query = $this->db_model->countQuery("*", "routes", $where);
        }
        return $query;
    }

    function add_outbound($add_array) {
        unset($add_array["action"]);
        if ($this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 5) {
            $account_data = $this->session->userdata("accountinfo");
            $reseller = $account_data['id'];
            $add_array['reseller_id'] = $reseller;
        }
        $add_array['prepend'] = $add_array['prepend'] ;
        $add_array['pattern'] = "^" . $add_array['pattern'] . ".*";
        $this->db->insert("outbound_routes", $add_array);
        return true;
    }

    function edit_outbound($data, $id) {
        unset($data["action"]);
        $data['pattern'] = "^" . $data['pattern'] . ".*";
        $this->db->where("id", $id);
        $this->db->update("outbound_routes", $data);
    }

    function remove_outbound($id) {
        $this->db->where("id", $id);
        $this->db->delete("outbound_routes");
        return true;
    }

    function add_inbound($add_array) {
        unset($add_array["action"]);
        if ($this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 5) {
            $account_data = $this->session->userdata("accountinfo");
            $reseller = $account_data['id'];
            $add_array['reseller_id'] = $reseller;
        }
        $add_array['pattern'] = "^" . $add_array['pattern'] . ".*";
        $this->db->insert("routes", $add_array);
        return true;
    }

    function edit_inbound($data, $id) {
        unset($data["action"]);
        $data['pattern'] = "^" . $data['pattern'] . ".*";
        $this->db->where("id", $id);
        $this->db->update("routes", $data);
    }

    function remove_inbound($id) {
        $this->db->where("id", $id);
        $this->db->delete("routes");
        return true;
    }

    function get_trunk_name($field_value) {
        $this->db->where("name", $field_value);
        $query = $this->db->get('trunks');
        $data = $query->result();
        if ($query->num_rows > 0)
            return $data[0]->id;
        else
            return '';
    }

    function bulk_insert_terminationrates($field_value) {
        $this->db->insert_batch('outbound_routes', $field_value);
        $affected_row = $this->db->affected_rows();
        return $affected_row;
    }

    function bulk_insert_inboundrates($inserted_array) {
        $this->db->insert_batch('routes', $inserted_array);
        $affected_row = $this->db->affected_rows();
        return $affected_row;
    }
    function termination_rates_batch_update($update_array){
        $this->db_model->build_search('terminationrates_list_search');
        if($update_array["connectcost"]["operator"] != 1){
            $update_array["connectcost"]["connectcost"] = $this->common_model->add_calculate_currency($update_array["connectcost"]["connectcost"], '', '', false, false);
        }
        if($update_array["cost"]["operator"] != 1){
            $update_array["cost"]["cost"] = $this->common_model->add_calculate_currency($update_array["cost"]["cost"], '', '', false, false);
        }
        if ($this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 5) {
            $account_data = $this->session->userdata("accountinfo");
            $this->db->where("reseller_id",$account_data['id']);
        }
        
        $this->db_model->build_batch_update_array($update_array);
        
        return $this->db->update("outbound_routes");
    }
    function inboundrates_rates_batch_update($update_array){
        $this->db_model->build_search('inboundrates_list_search');
        if($update_array["cost"]["operator"] != 1){
            $update_array["cost"]["cost"] = $this->common_model->add_calculate_currency($update_array["cost"]["cost"], '', '', false, false);
        }
        if ($this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 5) {
            $account_data = $this->session->userdata("accountinfo");
            $this->db->where("reseller_id",$account_data['id']);
        }
        
        $this->db_model->build_batch_update_array($update_array);
        
        return $this->db->update("routes");
    }
    
}
