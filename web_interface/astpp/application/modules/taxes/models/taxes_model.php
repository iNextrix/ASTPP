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
class Taxes_model extends CI_Model {

    function Taxes_model() {
        parent::__construct();
    }

    function gettax_list($flag, $start = 0, $limit = 0) {
        $this->db_model->build_search('taxes_list_search');
        if ($this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 5) {
            $where = array("reseller_id"=>$this->session->userdata["accountinfo"]['id']);
        }else{
	    $where =array("reseller_id"=>0);
        }
        if ($flag) {
            $query = $this->db_model->select("*", "taxes", $where, "id", "ASC", $limit, $start);
        } else {
            $query = $this->db_model->countQuery("*", "taxes", $where);
        }
        return $query;
    }

    function add_tax($data) {
        unset($data["action"]);
        $data["date_added"] = date("Y-m-d H:i:s");
         if ($this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 5) {
	  $data['reseller_id'] = $this->session->userdata["accountinfo"]['id'];
         }else{
	  $data['reseller_id'] =0;
         }
        $this->db->insert("taxes", $data);
    }

    function edit_tax($data, $id) {
        unset($data["action"]);
        $data["last_modified"] = date("Y-m-d H:i:s");
        $this->db->where("id", $id);
        $this->db->update("taxes", $data);
    }

    function remove_taxes($id) {
        $this->db->where("id", $id);
        $this->db->delete("taxes");
        return true;
    }

}
