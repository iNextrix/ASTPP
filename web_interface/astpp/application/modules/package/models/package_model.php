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
class Package_model extends CI_Model {

    function Package_model() {
        parent::__construct();
    }

    function getpackage_list($flag, $start = 0, $limit = 0) {
        $this->db_model->build_search('package_list_search');
        if ($this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 5) {
            $account_data = $this->session->userdata("accountinfo");
            $where = array("reseller_id" => $account_data['id']);
        } else {
            $where = array("reseller_id"=>"0");
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
$where = array();
	
	$accountinfo = $this->session->userdata('accountinfo');
	$reseller_id=$accountinfo['type']== -1 ? 0 : $accountinfo['id'];
	$this->db->where('reseller_id',$reseller_id);
	$this->db->select('id');
	$result=$this->db->get('accounts');
	
	
	$this->db_model->build_search('package_list_search');	
	if($this->session->userdata('advance_search')!= 1){
	
	  
	  if($result->num_rows() >0){
	  $acc_arr=array();
	  $result=$result->result_array();
	    foreach($result as $data){
	      $acc_arr[]=$data['id'];
	    }
	    $this->db->where_in('accountid',$acc_arr);
	    if($flag){
	      $this->db->select('*');
	    }
	    else{
	      $this->db->select('count(id) as count');
	    }
	    if($flag){
	      $this->db->order_by('seconds','desc');
	      $this->db->limit($limit, $start);
	    }
	    $result=$this->db->get('counters');
// 	    echo $this->db->last_query();exit;    
	    if($flag){
	      return $result;
	    }else{
	      $result=$result->result_array();
	      return $result[0]['count'];
	    }
	  }else{
          if($flag){
	      $query=(object)array('num_rows'=>0);
	  }
	  else{
	      $query=0;
	  }
 	  
	  return $query;
        }
    }else{
          
         if($result->num_rows() >0){
	    $acc_arr=array();
	    $result=$result->result_array();
	    foreach($result as $data){
	      $acc_arr[]=$data['id'];
	    }
	    $this->db->where_in('accountid',$acc_arr);
	}
         
         if($flag){
	  $this->db->select('*');
         }
         else{
          $this->db->select('count(id) as count');
         }
         if($flag){
	  $this->db->order_by('seconds','desc');
	  $this->db->limit($limit, $start);
         }
         $result=$this->db->get('counters');
//  	echo $this->db->last_query();exit;              
         if($result->num_rows() > 0){
	      if($flag){
	        
		return $result;
	      }else{
		$result=$result->result_array();
		
		return $result[0]['count'];
	      }
         }else{
	      if($flag){
	          
		  $query=(object)array('num_rows'=>0);
	      }
	      else{
		  $query=0;
	      }
// 	echo $this->db->last_query();exit;    
	return $query;
	}
    }
    }
    function insert_package_pattern($data, $packageid) {
        $data = explode(",", $data);
        $tmp = array();
        foreach ($data as $key => $data_value) {
            $tmp[$key]["package_id"] = $packageid;
                $result=$this->get_pattern_by_id($data_value);

            $tmp[$key]["patterns"] = $result[0]['pattern'];
                $tmp[$key]["destination"] = $result[0]['comment'];
        }
        return $this->db->insert_batch("package_patterns", $tmp);
    }

    function get_pattern_by_id($pattern) {
        $patterns = $this->db_model->getSelect("pattern,comment", "routes", array("id" => $pattern));
        $patterns = $patterns->result_array();
        return $patterns;
    }


}
