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
class Invoices_model extends CI_Model {

    function Invoices_model() {
        parent::__construct();
    }
    function get_invoice_list($flag, $start = 0, $limit = 0) {
	$where = array();
	
	$accountinfo = $this->session->userdata('accountinfo');
	$reseller_id=$accountinfo['type']== -1 ? 0 : $accountinfo['id'];
	$this->db->where('reseller_id',$reseller_id);
	$this->db->select('id');
	$result=$this->db->get('accounts');
	
	
	$this->db_model->build_search('invoice_list_search');	
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
	      $this->db->order_by('invoice_date','desc');
	      $this->db->limit($limit, $start);
	    }
	    $result=$this->db->get('invoices');
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
	  $this->db->order_by('invoice_date','desc');
	  $this->db->limit($limit, $start);
         }
         $result=$this->db->get('invoices');
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
    
    
        
         /**
     * -------Here we write code for model accounting functions get_invoiceconf------
     * this function get the invoice configration to generate pdf file.
     */
/*	function get_invoiceconf() {
    	
	$accountdata = $this->session->userdata('accountinfo');

            $accountid = $accountdata['id'];
        $return_array=array();

        $where=array('accountid'=> $accountid);
 
        $query = $this->db_model->getSelect("*","invoice_conf",$where);

        foreach($query->result_array() as $key => $value)
        {
            $return_array=$value;

        }
        
         return $return_array;
    } */

function save_invoiceconf($post_array){
        $accountdata = $this->session->userdata('accountinfo');
        $accountid = $accountdata['id'];
        
$where_arr=array('accountid'=>$accountid);
        unset($post_array['action']);
        $this->db->where($where_arr);
	$this->db->select('count(id) as count');
        $result=$this->db->get('invoice_conf');
        $result=$result->result_array();
        $count=$result[0]['count'];
        if($count > 0 ){
            $this->db->where($where_arr);
	    $this->db->update('invoice_conf',$post_array);
	}else{
            $post_array['accountid']=$accountid;
   	    $this->db->insert('invoice_conf',$post_array);
	}
        return true;
    }
    function get_invoiceconf() {
        $accountdata = $this->session->userdata('accountinfo');
        $accountid = $accountdata['id'];

        $return_array=array();
        $where=array('accountid'=> $accountid);
        
        $query = $this->db_model->getSelect("*","invoice_conf",$where);
        foreach($query->result_array() as $key => $value)
        {
            $return_array=$value;
        }
        
         return $return_array;
    } 
    function getCdrs_invoice($invoiceid)
	{
		$this->db->where('invoiceid', $invoiceid);
		$this->db->from('cdrs');
		$query = $this->db->get();
		return $query;
    }
    function get_account_including_closed($accountdata)
	{
		$q = "SELECT * FROM accounts WHERE number = '".$this->db->escape_str($accountdata)."'";
		$query = $this->db->query($q);		
		if($query->num_rows() > 0)
		{
			$row = $query->row_array();
			return $row;
		}
		$q = "SELECT * FROM accounts WHERE accountid = '".$this->db->escape_str($accountdata)."'";
		$query = $this->db->query($q);		
		if($query->num_rows() > 0)
		{
			$row = $query->row_array();
			return $row;			
		}

		return NULL;
    }
    function get_user_invoice_list($flag, $start = 0, $limit = 0){
	$this->db_model->build_search('invoice_list_search');
	$accountinfo=$this->session->userdata('accountinfo');
        $where = array("accountid" => $accountinfo['id']);
        if ($flag) {
            $query = $this->db_model->select("*", "invoices", $where, "invoice_date", "desc", $limit, $start);
        } else {
            $query = $this->db_model->countQuery("*", "invoices", $where);
        }
//         echo $this->db->last_query();exit;
        return $query;
    }

}
