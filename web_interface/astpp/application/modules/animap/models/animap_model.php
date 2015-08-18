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
class ANIMAP_model extends CI_Model {

    function ANIMAP_model() {
        parent::__construct();
    }

    function animap_list($flag, $start = 0, $limit = 0) {
   
 $accountinfo = $this->session->userdata('accountinfo');
     
        if ($this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 5) {
            $qry = $this->db_model->getselect('id', 'accounts', array('reseller_id' => $accountinfo['id']));
            $result = $qry->result_array();

            foreach ($result as $value1) {
                $value[] = $value1['id'];

            
            }
            $this->db->where_in('accountid', $value);
        } else {
              $qry = $this->db_model->getselect('id', 'accounts', array('reseller_id' => 0));
            $result = $qry->result_array();

            foreach ($result as $value1) {
                $value[] = $value1['id'];

              //s  $res = implode(',', $value);
            }
            $this->db->where_in('accountid', $value);
        }
           $this->db_model->build_search('animap_list_search');
        if ($flag) {
            $query = $this->db_model->select("*", "ani_map",  "", "id", "ASC", $limit, $start);
        } else {
            $query = $this->db_model->countQuery("*", "ani_map", "");
        }
      //  echo $this->db->last_query(); exit;
        return $query;
        
    }

    function add_animap($add_array) {
        
        $data=array('number'=>$add_array['number'],'accountid'=>$add_array['accountid'],'context'=>'default');
        $this->db->insert("ani_map", $data);
      
       // echo $this->db->last_query();exit;
          return $this->db->insert_id();
    }

    function edit_animap($add_array, $id) {
      
         $data=array('number'=>$add_array['number'],'accountid'=>$add_array['accountid'],'context'=>'default');
        $this->db->where("id", $id);
        return  $this->db->update("ani_map", $data);
       // echo $this->db->last_query();exit;
    }

    function remove_animap($id) {
        $this->db->where("id", $id);
        $this->db->delete("ani_map");
        
       
        return true;
    }
    
   

}
