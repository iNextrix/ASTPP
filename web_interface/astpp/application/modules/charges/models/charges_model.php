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
class Charges_model extends CI_Model {

    function Charges_model() {
        parent::__construct();
    }

    function getcharges_list($flag, $start = 0, $limit = 0) {
        $this->db_model->build_search('charges_list_search');
        if ($this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 5) {
            $account_data = $this->session->userdata("accountinfo");
            $reseller = $account_data['id'];
            $where = array("reseller_id" => $reseller);
        } else {
            $where = array("reseller_id" => "0");
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
        return $this->db->insert_id();
    }

    function edit_charge($data, $id) {
        unset($data['action']);
        if ($this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 5) {
            $account_data = $this->session->userdata("accountinfo");
            $data['reseller_id'] = $account_data['id'];
        } else {
            $data['reseller_id'] = "0";
        }
        $this->db->where("id", $id);
        return $this->db->update("charges", $data);
    }

    function remove_charge($id) {
        $this->db->where("id", $id);
        $this->db->delete("charges");
        
        $this->db->where("charge_id", $id);
        $this->db->delete("charge_to_account");
        return true;
    }
    
    function add_account_charges($pricelistid,$chargeid,$flag){
        if($flag){
           $this->db->where("charge_id", $chargeid);
           $this->db->delete("charge_to_account");
        }
        $account = $this->db_model->getSelect("*", "accounts", array("pricelist_id"=>$pricelistid));
        if($account->num_rows > 0){
           foreach ($account->result_array() as $key => $value){
                $charge_arr = array("charge_id"=>$chargeid,"accountid"=>$value['id'],"assign_date"=>gmdate("Y-m-d H:i:s"));
                $this->db->insert("charge_to_account", $charge_arr);
//echo $this->db->last_query(); exit;                
           }
        }
    }

}
