<?php
###########################################################################
# ASTPP - Open Source Voip Billing
# Copyright (C) 2004, Aleph Communications
#
# Contributor(s)
# "iNextrix Technologies Pvt. Ltd - <astpp@inextrix.com>"
#
# This program is free software; you can redistribute it and/or
# modify it under the terms of the GNU General Public License
# as published by the Free Software Foundation; either version 2
# of the License, or (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details..
#
# You should have received a copy of the GNU General Public License
# along with this program.  If not, see <http://www.gnu.org/licenses/>
############################################################################
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
//	echo $this->db->last_query();
//exit;
        }
        return $query;
    }
//hiten
    function getoutboundrates($flag, $start = 0, $limit = 0, $export = true) {
        $this->db_model->build_search('terminationrates_list_search');
        $this->db->from('outbound_routes');
        if ($flag) {
            if ($export)
                $this->db->limit($limit, $start);
            $result = $this->db->get();
        }else {
            $result = $this->db->count_all_results();
        }
        return $result;
    }

    function getinboundrates($flag, $start = 0, $limit = 0, $export = true) {
        $this->db_model->build_search('inboundrates_list_search');
	if ($this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 5) {
            $account_data = $this->session->userdata("accountinfo");
            $reseller = $account_data['id'];
            $where = array("reseller_id" => $reseller);
        } else {
            $where = array('reseller_id'=>'0');
        }
       
	$this->db_model->build_search('inboundrates_list_search');
        if ($flag) {
            if ($export)
                $this->db->limit($limit, $start);
            $result = $this->db_model->select("*", "routes", $where, "id", "ASC", $limit, $start);
        }else {
            $result = $this->db_model->countQuery("*", "routes", $where);
        }
// 	echo "<pre>";print_r($result->result());exit;
        return $result;
    }

    function getinbound_rates_for_user($flag, $start = 0, $limit = 0,$export = true) {
        $this->db_model->build_search('inboundrates_list_search');

        $account_data = $this->session->userdata("accountinfo");

        $where = array("pricelist_id" => $account_data["pricelist_id"]);

        $this->db_model->build_search('inboundrates_list_search');
        if ($flag) {
            if ($export)
                $this->db->limit($limit, $start);
            $result = $this->db_model->select("*", "routes", $where, "id", "ASC", $limit, $start);
        }else {
            $result = $this->db_model->countQuery("*", "routes", $where);
        }
        return $result;
    }
// ==============================================
    function getinbound_rates_list($flag, $start = 0, $limit = 0) {
        $this->db_model->build_search('inboundrates_list_search');
        if ($this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 5) {
            $account_data = $this->session->userdata("accountinfo");
            $where = array("reseller_id" => $account_data['id']);
        } else {
            $where = array('reseller_id'=>'0');
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
            $where = array("reseller_id" => $reseller, "status" => "0");
        } else {
            $where = array("status" => "0",'reseller_id'=>'0');
        }
        $where1 = '(pattern NOT IN (select blocked_patterns from block_patterns where accountid = "'.$accountid.'"))';
        $this->db->where($where1);        
        if ($flag) {
            $query = $this->db_model->select("*", "routes", $where, "id", "ASC", $limit, $start);
//             echo "<pre>"; print_r($query); exit;
        } else {
            $query = $this->db_model->countQuery("*", "routes", $where);
        }
        return $query;
    }
    function getunblocked_package_pattern($accountid,$flag, $start = 0, $limit = 0) {
        $this->db_model->build_search('inboundrates_list_search');
        if ($this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 5) {
            $account_data = $this->session->userdata("accountinfo");
            $reseller = $account_data['id'];
            $where = array("reseller_id" => $reseller, "status" => "0");
        } else {
            $where = array("status" => "0",'reseller_id'=>'0');
        }
        $where1 = '(pattern NOT IN (select DISTINCT patterns from package_patterns where package_id = "'.$accountid.'"))';
  //          echo "<pre>"; print_r($where1); exit;
        $this->db->where($where1);       
	// echo "<pre>"; print_r($where1); exit;
        if ($flag) {
            $query = $this->db_model->select("*", "routes", $where, "id", "ASC", $limit, $start);
//             echo "<pre>"; print_r($query); exit;
        } else {
            $query = $this->db_model->countQuery("*", "routes", $where);
        }
        return $query;
    }

    function getinbound_rates_list_for_user($flag, $start = 0, $limit = 0) {
        $this->db_model->build_search('inboundrates_list_search');

        $account_data = $this->session->userdata("accountinfo");
        $where = array("pricelist_id" => $account_data["pricelist_id"],"status" => '0');

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
         $add_array['pattern'] = "^" . $add_array['pattern'] . ".*";
        $add_array['prepend'] = $add_array['prepend'];
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

    function bulk_insert_originationrates($inserted_array) {
        $this->db->insert_batch('routes', $inserted_array);
        $affected_row = $this->db->affected_rows();
        return $affected_row;
    }
    function termination_rates_batch_update($update_array){
        $this->db_model->build_search('terminationrates_list_search');
        /*if($update_array["connectcost"]["operator"] != 1){
            $update_array["connectcost"]["connectcost"] = $this->common_model->add_calculate_currency($update_array["connectcost"]["connectcost"], '', '', false, false);
        }
        if($update_array["cost"]["operator"] != 1){
            $update_array["cost"]["cost"] = $this->common_model->add_calculate_currency($update_array["cost"]["cost"], '', '', false, false);
        }*/
        if ($this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 5) {
            $account_data = $this->session->userdata("accountinfo");
            $this->db->where("reseller_id",$account_data['id']);
        }
        
        $this->db_model->build_batch_update_array($update_array);
        
        return $this->db->update("outbound_routes");
    }
    function inboundrates_rates_batch_update($update_array){
        $this->db_model->build_search('inboundrates_list_search');
        /*if($update_array["cost"]["operator"] != 1){
            $update_array["cost"]["cost"] = $this->common_model->add_calculate_currency($update_array["cost"]["cost"], '', '', false, false);
        }*/
        if ($this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 5) {
            $account_data = $this->session->userdata("accountinfo");
            $this->db->where("reseller_id",$account_data['id']);
        }
        
        $this->db_model->build_batch_update_array($update_array);
        
        return $this->db->update("routes");
    }
    function getreseller_rates_list($flag, $start = 0, $limit = 0,$export=false) {
        $this->db_model->build_search('resellerrates_list_search');
        $account_data = $this->session->userdata("accountinfo");
        $where = array("status"=>"0","pricelist_id" => $account_data["pricelist_id"]);
        if ($flag) {
            $query = $this->db_model->select("*", "routes", $where, "id", "ASC", $limit, $start);            
        } else {
            $query = $this->db_model->countQuery("*", "routes", $where);
        }
        return $query;
    }
    
}
