<?php

class DID_model extends CI_Model {

    function DID_model() {
        parent::__construct();
    }

    function add_did($add_array) {
        unset($add_array["action"]);
        $this->db->insert("dids", $add_array);

        $outbound_insert = $this->array_outboundroutes($add_array, '0');
        $this->load->module('rates/rates');
        $this->rates->rates_model->add_inbound($outbound_insert);
        return true;
    }

    function array_outboundroutes($add_array, $multi = '') {
        $flag = 0;
        $query = $this->db_model->getSelect("*", "system", array('name' => 'default_brand'));
        if ($query->num_rows > 0) {
            $result = $query->result_array();
            $query_pricelist = $this->db_model->getSelect("*", "pricelists", array('name' => $result[0]['value']));
            if ($query_pricelist->num_rows > 0) {
                $result_pricelist = $query_pricelist->result_array();
                $pricelist_id = $result_pricelist[0]['id'];
                $flag = 1;
            } else {
                $pricelist_id = $this->insert_pricelist();
                $flag = 1;
            }
        } else {
            $pricelist_id = $this->insert_pricelist();
            $flag = 1;
        }
        if ($multi == '0') {
            $add_array['number'] = filter_var($add_array['number'], FILTER_SANITIZE_NUMBER_INT);
            $origination_rate_array = array('pattern' => $add_array['number'],
                'comment' => "DID:" . $this->common->get_field_name('country', 'countrycode', $add_array['country_id']) . "," . $add_array['city'] . "," . $add_array['province']
                , 'pricelist_id' => $pricelist_id,
                'inc' => $add_array['inc'],
                'includedseconds' => $add_array['includedseconds'],
                'cost' => $add_array['cost'],
                'connectcost' => $add_array['connectcost'],
                'action' => ''
            );
        }
        if ($multi == '1') {
            $origination_rate_array = array('pattern' => "^" . $add_array['number'] . ".*",
                'comment' => "DID:" . $add_array['country'] . "," . $add_array['city'] . "," . $add_array['province']
                , 'pricelist_id' => $pricelist_id, 'inc' => $add_array['inc'],
                'includedseconds' => $add_array['includedseconds'],
                'cost' => $add_array['cost'],
                'connectcost' => $add_array['connectcost'],
            );
        }
        return $origination_rate_array;
    }

    function insert_pricelist() {
        $insert_array = array('name' => 'default', 'markup' => '', 'inc' => '');
        return $this->db->insert_id();
    }

    function edit_did($data, $id) {
        unset($data["action"]);
        $this->db->where("id", $id);
        $this->db->update("dids", $data);
    }

    function getdid_list($flag, $start = 0, $limit = 0) {
        $this->db_model->build_search('did_list_search');
        if ($this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 5) {
            $where = array("accountid" => "0", "status" => "1");
        } else {
            $where = array("status" => "1");
        }
        if ($flag) {
            $query = $this->db_model->select("*", "dids", $where, "id", "ASC", $limit, $start);
        } else {
            $query = $this->db_model->countQuery("*", "dids", $where);
        }
        return $query;
    }

    function remove_did($id) {
        $this->db->where("id", $id);
        $this->db->delete("dids");
        return true;
    }

    function get_coutry_id_by_name($field_value) {
        $this->db->where("country", ucfirst($field_value));
        $query = $this->db->get('countrycode');
        $data = $query->result();
        if ($query->num_rows > 0)
            return $data[0]->id;
        else
            return '';
    }

    function bulk_insert_dids($field_value) {
        $this->db->insert_batch('dids', $field_value);
        $affected_row = $this->db->affected_rows();
        return $affected_row;
    }

    function get_account($accountdata) {
        $q = "SELECT * FROM accounts WHERE number = '" . $this->db->escape_str($accountdata) . "' AND status = 1";
        $query = $this->db->query($q);
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            return $row;
        }

        $q = "SELECT * FROM accounts WHERE cc = '" . $this->db->escape_str($accountdata) . "' AND status = 1";
        $query = $this->db->query($q);
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            return $row;
        }

        $q = "SELECT * FROM accounts WHERE accountid = '" . $this->db->escape_str($accountdata) . "' AND status = 1";
        $query = $this->db->query($q);
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            return $row;
        }

        return NULL;
    }

    function get_did_reseller_new($did, $reseller_id = "") {
        $sql = "SELECT dids.number AS number, "
                . "reseller_pricing.monthlycost AS monthlycost, "
                . "reseller_pricing.prorate AS prorate, "
                . "reseller_pricing.setup AS setup, "
                . "reseller_pricing.cost AS cost, "
                . "reseller_pricing.connectcost AS connectcost, "
                . "reseller_pricing.includedseconds AS includedseconds, "
                . "reseller_pricing.inc AS inc, "
                . "reseller_pricing.disconnectionfee AS disconnectionfee, "
                . "dids.provider_id AS provider_id, "
                . "dids.country_id AS country_id, "
                . "dids.city AS city, "
                . "dids.province AS province, "
                . "dids.extensions AS extensions, "
                . "dids.accountid AS account, "
                . "dids.variables AS variables, "
                . "dids.options AS options, "
                . "dids.maxchannels AS maxchannels, "
                . "dids.chargeonallocation AS chargeonallocation, "
                . "dids.allocation_bill_status AS allocation_bill_status, "
                . "dids.limittime AS limittime, "
                . "dids.dial_as AS dial_as, "
                . "dids.status AS status "
                . "FROM dids, reseller_pricing "
                . "WHERE dids.id = " . $did
                . " AND reseller_pricing.type = '1' AND reseller_pricing.reseller_id = "
                . $reseller_id;
//                . " AND reseller_pricing.note = "
//                . $did

        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            return $query->row_array();
        }
        //return $this->db_get_arrays($sql);
    }

    function get_did_by_number($number) {
        $this->db->where("id", $number);
        $query = $this->db->get("dids");

        if ($query->num_rows() > 0)
            return $query->row_array();
        else
            return false;
    }

    function edit_did_reseller($post) {
        $accountinfo = $this->session->userdata('accountinfo');

        $this->delete_pricing_reseller($accountinfo['id'], $post['number']);
        $this->insert_reseller_pricing($accountinfo['id'], $post);
        $this->update_dids_reseller($post);


        $query_pricelist = $this->db_model->getSelect("*", "pricelists", array('name' => $accountinfo['number']));
        print_r($query_pricelist);
        if ($query_pricelist->num_rows > 0) {
            $result_pricelist = $query_pricelist->result_array();
            $pricelist_id = $result_pricelist[0]['id'];
        }


        $this->delete_routes($accountinfo['number'], $post['number'], $pricelist_id);
        $this->insert_routes($post, $pricelist_id);
        return true;
    }

    function delete_pricing_reseller($username, $number) {
        $where = array('reseller_id' => $username, 'note' => $number, 'type' => '1');
        $this->db->where($where);
        $this->db->delete('reseller_pricing');
        return true;
    }

    function insert_reseller_pricing($id, $post) {

        $insert_array = array('reseller_id' => $id, 'type' => '1', 'note' => $post['number'],
            'monthlycost' => $post['monthlycost'],
            'prorate' => $post['prorate'],
            'setup' => $post['setup'],
            'cost' => $post['cost'],
            'inc' => $post['inc'],
            'disconnectionfee' => $post['disconnectionfee'],
            'connectcost' => $post['connectcost'],
            'includedseconds' => $post['included'],
            'status' => '1');

        $this->db->insert('reseller_pricing', $insert_array);
        return true;
    }

    function update_dids_reseller($post) {
        $where = array('id' => $post['did_id']);
        $update_array = array('dial_as' => $post['dial_as'], 'extensions' => $post['extension']);
        $this->db->where($where);
        $this->db->update('dids', $update_array);
    }

    function delete_routes($id, $number, $pricelist_id) {
        $number = "^" . $number . ".*";
        $where = array('pricelist_id' => $pricelist_id, 'pattern' => $number);
        $this->db->where($where);
        $this->db->delete('routes');
    }

    function insert_routes($post, $pricelist_id) {
        $commment = "DID:" . $post['country'] . "," . $post['province'] . "," . $post['city'];
        $insert_array = array('pattern' => "^" . $post['number'] . ".*", 'comment' => $commment, 'pricelist_id' => $pricelist_id,
            'connectcost' => $post['connectcost'], 'includedseconds' => $post['included'], 'cost' => $post['cost'], 'inc' => $post['inc']);
        $this->db->insert('routes', $insert_array);
        return true;
    }

    function remove_did_pricing($array_did, $reseller_id) {
        $where = array('note' => $array_did['number'], 'type' => '1', 'reseller_id' => $reseller_id);
        $this->db->where($where);
        $this->db->delete('reseller_pricing');

        $query_pricelist = $this->db_model->getSelect("*", "pricelists", array('name' => $this->session->userdata['accountinfo']['number']));
        if ($query_pricelist->num_rows > 0) {
            $result_pricelist = $query_pricelist->result_array();
            $pricelist_id = $result_pricelist[0]['id'];
        }
        $this->delete_routes('', $array_did['number'], $pricelist_id);
        return true;
    }

}