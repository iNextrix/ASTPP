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
class Opensips_model extends CI_Model {

    function Opensips_model() {
        parent::__construct();
    }
    
    function getopensipsdevice_list($flag, $start = 0, $limit = 0) {
        $db_config = Common_model::$global_config['system_config'];
        $opensipdsn = "mysql://" . $db_config['opensips_dbuser'] . ":" . $db_config['opensips_dbpass'] . "@" . $db_config['opensips_dbhost'] . "/" . $db_config['opensips_dbname'] . "?char_set=utf8&dbcollat=utf8_general_ci&cache_on=true&cachedir=";
        $this->opensips_db = $this->load->database($opensipdsn, true);
        $this->build_search_opensips('opensipsdevice_list_search');
	if($this->session->userdata("logintype") == 1)
	{
                $accountinfo['reseller_id'] = $this->session->userdata["accountinfo"]['id'];
		$num = $this->db_model->getSelect("number", "accounts", array("reseller_id" => $accountinfo['reseller_id']));
	        $num_value = $num->result_array();
		foreach($num_value as $value){
			$value =$value;
		$this->opensips_db->or_where(array("accountcode"=>$value['number']));
		}
	}else{
                $accountinfo['reseller_id'] = 0;
        $num = $this->db_model->getSelect("number", "accounts", array("reseller_id" => $accountinfo['reseller_id']));
            $num_value = $num->result_array();
        foreach($num_value as $value){
            $value =$value;
        $this->opensips_db->or_where(array("accountcode"=>$value['number']));
        }
    }
        if ($flag) {
            $this->opensips_db->limit($limit,$start);
            $query = $this->opensips_db->get("subscriber");
        } else {
            $query = $this->opensips_db->get("subscriber");
            $query = $query->num_rows();
        }
//echo $this->opensips_db->last_query(); exit;
        return $query;
    }
    

 /* function getopensipsdevice_list($flag, $start = 0, $limit = 0) {
	//echo 'da'; exit;
	$where = array();
	$accountinfo = $this->session->userdata('accountinfo');
	$reseller_id=$accountinfo['type']== -1 ? 0 : $accountinfo['id'];
	$this->db->where('reseller_id',$reseller_id);
	$this->db->select('number');
	$result=$this->db->get('accounts');
	$this->build_search_opensips('opensipsdevice_list_search');

	if($this->session->userdata('advance_search')!= 1){
	  if($result->num_rows() >0){
	  $acc_arr=array();
	  $result=$result->result_array();
	    foreach($result as $data){
	      $acc_arr[]=$data['number'];
	    }
$db_config = Common_model::$global_config['system_config'];
        $opensipdsn = "mysql://" . $db_config['opensips_dbuser'] . ":" . $db_config['opensips_dbpass'] . "@" . $db_config['opensips_dbhost'] . "/" . $db_config['opensips_dbname'] . "?char_set=utf8&dbcollat=utf8_general_ci&cache_on=true&cachedir=";
        $this->opensips_db = $this->load->database($opensipdsn, true);
	    $this->opensips_db->get("subscriber");
	    $this->opensips_db->where_in('accountcode',$acc_arr);
	   // echo $flag; exit;
	    if($flag){
	      $this->opensips_db->select('*');
	      $this->opensips_db->limit($limit, $start);
	    }
	    else{
	      $this->opensips_db->select('count(id) as count');
	    }
/*	    if($flag){
	    	      
	    }
	$result = $this->opensips_db->get("subscriber");
	
	    if($result->num_rows() > 0){
	    
	  //  echo $this->opensips_db->last_query(); exit;	
	    if($flag){
	      //echo "Hello";
	      return $result;

	    }else{
	      $result=$result->result_array();
	      return $result[0]['count'];
	    }
	    
	    }
	   
	    else{
	    if($flag){
	      $query=(object)array('num_rows'=>0);
	    }
	    else{
	      $query=0;
	    }
	    return $query;
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
	      $acc_arr[]=$data['number'];
	    }
	    $this->opensips_db->where_in('accountcode',$acc_arr);
	    
	}
         
         if($flag){
	  $this->opensips_db->select('*');
         }
         else{
         
        
         // echo $this->opensips_db->last_query(); exit;
          $this->opensips_db->select('count(id) as count');
          
         }
         
         if($flag){
	  $this->db->limit($limit, $start);
         }
$result = $this->opensips_db->get("subscriber");

//echo "<pre>"; print_r($result); exit;
                 
         if($result->num_rows() > 0){
         
	      if($flag){
	        return $result;
	      }else{
	      
		$result=$result->result_array();
		return $result[0]['count'];
		// echo 'dada'; exit;
	      }
         }else{
	      if($flag){
	           $query=(object)array('num_rows'=>0);
	      }
	      else{
		  $query=0;
	      }
	      
	      $result = $this->opensips_db->get("subscriber");
	     // $this->opensipsdb->last_query(); exit;
	return $query;
	}
    }
 }   */
    

    function getopensipsdevice_customer_list($flag, $accountid = "", $start = "0", $limit = "0") {
	
        $db_config = Common_model::$global_config['system_config'];
        $opensipdsn = "mysql://" . $db_config['opensips_dbuser'] . ":" . $db_config['opensips_dbpass'] . "@" . $db_config['opensips_dbhost'] . "/" . $db_config['opensips_dbname'] . "?char_set=utf8&dbcollat=utf8_general_ci&cache_on=true&cachedir=";
        $this->opensips_db = $this->load->database($opensipdsn, true);
$this->build_search_opensips('opensipsdevice_list_search');

        if ($accountid != "") {
            $where = array("accountcode" => $this->common->get_field_name('number', 'accounts', array('id' => $accountid)));
        }
        $this->opensips_db->where($where);
        if ($flag) {
	      $this->opensips_db->limit($limit,$start);            
        }
        $result = $this->opensips_db->get("subscriber");
        if($result->num_rows() > 0){
	  if($flag){
	    return $result;
	  }
	  else{
	    return $result->num_rows();
	  }
        }else{
        
         if($flag){
	      $result=(object)array('num_rows'=>0);
	  }
	  else{
	      $result=0;
	  }
	  return $result;
        }
    }

    function getopensipsdispatcher_list($flag, $start = '', $limit = '') {
        $db_config = Common_model::$global_config['system_config'];
        $opensipdsn = "mysql://" . $db_config['opensips_dbuser'] . ":" . $db_config['opensips_dbpass'] . "@" . $db_config['opensips_dbhost'] . "/" . $db_config['opensips_dbname'] . "?char_set=utf8&dbcollat=utf8_general_ci&cache_on=true&cachedir=";
       // echo  $opensipdsn; exit;
        $this->opensips_db = $this->load->database($opensipdsn, true);
        $this->build_search_opensips('opensipsdispatcher_list_search');	      
	  if ($flag) {
            $this->opensips_db->limit( $limit,$start);
            $query = $this->opensips_db->get("dispatcher");
        } else {
            $query = $this->opensips_db->get("dispatcher");
            $query = $query->num_rows();

        }
        //echo $query; exit;
//
        return $query;
    }



 function add_opensipsdevices($data) {
        $db_config = Common_model::$global_config['system_config'];
        $opensipdsn = "mysql://" . $db_config['opensips_dbuser'] . ":" . $db_config['opensips_dbpass'] . "@" . $db_config['opensips_dbhost'] . "/" . $db_config['opensips_dbname'] . "?char_set=utf8&dbcollat=utf8_general_ci&cache_on=true&cachedir=";
        $this->opensips_db = $this->load->database($opensipdsn, true);
       // $this->opensips_db = $this->load->database($opensipdsn, true);
     //  echo '<pre>'; print_r($opensipdsn); exit;
        unset($data["action"]);
        $this->opensips_db->insert("subscriber", $data);
       // echo $this->opensips_db->last_query(); exit;
    }

   /* function add_opensipsdevices($data) {
   //echo 'da'; exit;
        $db_config = Common_model::$global_config['system_config'];
        $opensipdsn = "mysql://" . $db_config['opensips_dbuser'] . ":" . $db_config['opensips_dbpass'] . "@" . $db_config['opensips_dbhost'] . "/" . $db_config['opensips_dbname'] . "?char_set=utf8&dbcollat=utf8_general_ci&cache_on=true&cachedir=";
       // echo '<pre>'; print_r( $opensipdsn); exit;
       // $this->opensips_db = $this->load->database($opensipdsn, true);
        $this->opensips_db = $this->load->database($opensipdsn, true);
        unset($data["action"]);
      echo  $this->opensips_db->insert("subscriber", $data);
       //echo $this->opensips_db->last_query(); exit;
    }*/

    function edit_opensipsdevices($data, $id) {
   // echo 'da';.
   //echo '<pre>'; print_r($data); exit;
       unset($data["action"]);

        $db_config = Common_model::$global_config['system_config'];
        $opensipdsn = "mysql://" . $db_config['opensips_dbuser'] . ":" . $db_config['opensips_dbpass'] . "@" . $db_config['opensips_dbhost'] . "/" . $db_config['opensips_dbname'] . "?char_set=utf8&dbcollat=utf8_general_ci&cache_on=true&cachedir=";
      //  echo '<pre>'; print_r(  $opensipdsn); exit;
        $this->opensips_db = $this->load->database($opensipdsn, true);
      $data['accountcode']=$data['id'];
    
      $data=array("username"=>$data['username'],"password"=>$data['password'],"accountcode"=>$data['accountcode'],"pricelist_id"=>$data['pricelist_id'],"domain"=>$data['domain']);
      //  print_r( $data);exit;
        $this->opensips_db->where("id", $id);
      
        $this->opensips_db->update("subscriber", $data);
      //echo   $this->opensips_db->last_query(); exit;
    }


	 function delete_opensips_devices($id) {
        $db_config = Common_model::$global_config['system_config'];
        $opensipdsn = "mysql://" . $db_config['opensips_dbuser'] . ":" . $db_config['opensips_dbpass'] . "@" . $db_config['opensips_dbhost'] . "/" . $db_config['opensips_dbname'] . "?char_set=utf8&dbcollat=utf8_general_ci&cache_on=true&cachedir=";
        $this->opensips_db = $this->load->database($opensipdsn, true);
        $this->opensips_db->where("id", $id);
        $this->opensips_db->delete("subscriber");
        return true;
    }

    function remove_opensips($id) {
        $db_config = Common_model::$global_config['system_config'];
        $opensipdsn = "mysql://" . $db_config['opensips_dbuser'] . ":" . $db_config['opensips_dbpass'] . "@" . $db_config['opensips_dbhost'] . "/" . $db_config['opensips_dbname'] . "?char_set=utf8&dbcollat=utf8_general_ci&cache_on=true&cachedir=";
        $this->opensips_db = $this->load->database($opensipdsn, true);
        $this->opensips_db->where("id", $id);
        $this->opensips_db->delete("subscriber");
        return true;
    }

    function add_opensipsdispatcher($data) {
        $db_config = Common_model::$global_config['system_config'];
        $opensipdsn = "mysql://" . $db_config['opensips_dbuser'] . ":" . $db_config['opensips_dbpass'] . "@" . $db_config['opensips_dbhost'] . "/" . $db_config['opensips_dbname'] . "?char_set=utf8&dbcollat=utf8_general_ci&cache_on=true&cachedir=";
        $this->opensips_db = $this->load->database($opensipdsn, true);
        unset($data["action"]);
        $this->opensips_db->insert("dispatcher", $data);
    }

    function edit_opensipsdispatcher($data, $id) {
        unset($data["action"]);

        $db_config = Common_model::$global_config['system_config'];
        $opensipdsn = "mysql://" . $db_config['opensips_dbuser'] . ":" . $db_config['opensips_dbpass'] . "@" . $db_config['opensips_dbhost'] . "/" . $db_config['opensips_dbname'] . "?char_set=utf8&dbcollat=utf8_general_ci&cache_on=true&cachedir=";
        $this->opensips_db = $this->load->database($opensipdsn, true);

        $this->opensips_db->where("id", $id);
        $this->opensips_db->update("dispatcher", $data);
    }

    function remove_dispatcher($id) {
        $db_config = Common_model::$global_config['system_config'];
        $opensipdsn = "mysql://" . $db_config['opensips_dbuser'] . ":" . $db_config['opensips_dbpass'] . "@" . $db_config['opensips_dbhost'] . "/" . $db_config['opensips_dbname'] . "?char_set=utf8&dbcollat=utf8_general_ci&cache_on=true&cachedir=";
        $this->opensips_db = $this->load->database($opensipdsn, true);

        $this->opensips_db->where("id", $id);
        $this->opensips_db->delete("dispatcher");
        return true;
    }

 function build_search_opensips($accounts_list_search) {
$db_config = Common_model::$global_config['system_config'];
        $opensipdsn = "mysql://" . $db_config['opensips_dbuser'] . ":" . $db_config['opensips_dbpass'] . "@" . $db_config['opensips_dbhost'] . "/" . $db_config['opensips_dbname'] . "?char_set=utf8&dbcollat=utf8_general_ci&cache_on=true&cachedir=";
        $this->opensips_db = $this->load->database($opensipdsn, true);

        if ($this->session->userdata('advance_search') == 1) {
            $account_search = $this->session->userdata($accounts_list_search);
            unset($account_search["ajax_search"]);
            unset($account_search["advance_search"]);
            foreach ($account_search as $key => $value) {
                if ($value != "") {
                    if (is_array($value)) {
                        if (array_key_exists($key . "-integer", $value)) {
                            $this->get_interger_array($key, $value[$key . "-integer"], $value[$key]);
                        }
                        if (array_key_exists($key . "-string", $value)) {
                            $this->get_string_array($key, $value[$key . "-string"], $value[$key]);

                        }
                    } else {
                        $this->opensips_db->where($key, $value);
                    }
                }
            }
        }
    }

    function get_interger_array($field, $value, $search_array) {
        if ($search_array != '') {
            switch ($value) {
                case "1":
                    $this->opensips_db->where($field, $search_array);
                    break;
                case "2":
                    $this->opensips_db->where($field . ' <>', $search_array);
                    break;
                case "3":
                    $this->opensips_db->where($field . ' > ', $search_array);
                    break;
                case "4":
                    $this->opensips_db->where($field . ' < ', $search_array);
                    break;
                case "5":
                    $this->opensips_db->where($field . ' >= ', $search_array);
                    break;
                case "6":
                    $this->opensips_db->where($field . ' <= ', $search_array);
                    break;
            }
        }
    }

    function get_string_array($field, $value, $search_array) {
        if ($search_array != '') {
            switch ($value) {
                case "1":
                    $this->opensips_db->like($field, $search_array);
                    break;
                case "2":
                    $this->opensips_db->not_like($field, $search_array);
                    break;
                case "3":
                    $this->opensips_db->where($field, $search_array);
                    break;
                case "4":
                    $this->opensips_db->where($field . ' <>', $search_array);
                    break;
            }
        }
    }

}
