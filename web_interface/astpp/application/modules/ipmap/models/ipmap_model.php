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

class IPMAP_model extends CI_Model {

    function IPMAP_model() {
        parent::__construct();
    }

    function ipmap_list($flag, $start = 0, $limit = 0) {
        $accountinfo = $this->session->userdata('accountinfo');
        if ($this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 5) {
            $qry = $this->db_model->getselect('id', 'accounts', array('reseller_id' => $accountinfo['id']));
            $result = $qry->result_array();
         	foreach ($result as $value1) {
                $value[] = $value1['id'];
		 	}
		 	if(!empty($value)){
		 		$this->db->where_in('accountid', $value);	
		 	}else{
		 		$this->db->where_in('accountid', '0');
		 	}
            
        } else {

            $qry = $this->db_model->getselect('id', 'accounts', array('reseller_id' => 0));
            $result = $qry->result_array();

            foreach ($result as $value1) {
                $value[] = $value1['id'];
            }
            //$this->db->where_in('accountid', $value);
			if(!empty($value)){
		 		$this->db->where_in('accountid', $value);	
		 	}else{
		 		$this->db->where_in('accountid', '0');
		 	}            
        }
         $this->db_model->build_search('ipmap_list_search');
        
        if ($flag) {
           $query = $this->db_model->select("*", "ip_map", "", "id", "ASC", $limit, $start);
           } else {
            $query = $this->db_model->countQuery("*", "ip_map", "");
        }
          return $query;
    }

    function add_ipmap($add_array) {
 $data = array('name' => $add_array['name'], 'ip' => $add_array['ip'], 'prefix' => $add_array['prefix'], 'accountid' => $add_array['accountid'], 'context' => 'default');
        $this->db->insert("ip_map", $data);
       return $this->db->insert_id();
    }

    function edit_ipmap($add_array, $id) {
     $data = array('name' => $add_array['name'], 'ip' => $add_array['ip'], 'prefix' => $add_array['prefix'], 'accountid' => $add_array['accountid'], 'context' => 'default');
        $this->db->where("id", $id);
        return $this->db->update("ip_map", $data);
       
    }

    function remove_ipmap($id) {
        $this->db->where("id", $id);
        $this->db->delete("ip_map");
       return true;
    }
    }
