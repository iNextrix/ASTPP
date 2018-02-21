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

class UpdateBalance extends MX_Controller {
    public $account_arr = array();
    function __construct()
    {
        parent::__construct();
        if(!defined( 'CRON' ) )  
          exit();
        $this->load->model("db_model");
        $this->load->library("astpp/common");
    }
    function GetUpdateBalance(){
        $accounts_data = $this->get_table_data("*","accounts",array("status"=>"0","deleted"=>"0"));
        foreach($accounts_data as $account_val){
          $this->account_arr[$account_val['id']] = $account_val;
        }
        $this->process_periodic_charges();
        $this->process_DID_charges();
    }
    
    function process_periodic_charges(){
      $invoiceid = 0;
      $charge_data = $this->get_table_data("*","charges",array("status"=>"0"));
      if($charge_data){
         $currentdate = gmdate("Y-m-d H:i:s");
         foreach($charge_data as $charge_key =>$charge_val){
             $account_data = $this->get_table_data("*", "charge_to_account",array("charge_id"=>$charge_val["id"]));
 	     if($account_data){
	       foreach($account_data as $account_key => $account_value){
              $user_account = $this->account_arr[$account_value['accountid']];
/*              if($user_account["posttoexternal"] == 0){
                    $billing_cycle = "2";
              }else{
*/                    $billing_cycle = $charge_val["sweep_id"];
//         	    }                  
                  $charge_upto = ($account_value["charge_upto"] != "0000-00-00 00:00:00" && $account_value["charge_upto"] != "")?$account_value["charge_upto"]:$account_value["assign_date"];
                  if($charge_upto != "0000-00-00 00:00:00" && $charge_upto != "" && strtotime($charge_upto) < strtotime($currentdate) && $user_account["id"] > 0){
    	 	     $charges_amt = $this->calculate_charges($charge_val["charge"],$charge_upto,$currentdate,$billing_cycle,$charge_val["pro_rate"]); 
  		     if($charges_amt){
                          $fromdate = gmdate("Y-m-d",strtotime($charge_upto));
                          $todate = gmdate("Y-m-d",strtotime($charges_amt["upto_date"]));

  		        if($user_account["posttoexternal"] == 0){
                              $invoiceid = $this->common_model->generate_receipt($user_account["id"],$charges_amt["charges"]);
  				            $this->db->set('balance', 'balance-'.$charges_amt["charges"], FALSE);
  				            $this->db->where('id', $user_account["id"]);
  		          		 	$this->db->update("accounts"); 
  		          }                        
                $invoice_item_arr = array("accountid"=>$user_account["id"],
                                          "description"=>$charge_val['description']."-".$fromdate." to ".$todate,
                                          "charge_id"=>$charge_val['id'],"debit"=>$charges_amt["charges"],"invoiceid"=>$invoiceid,
                                          "created_date"=>$currentdate,"charge_type"=>"periodic_charge");

                $this->manage_invoice($invoice_item_arr);
                $this->db->update("charge_to_account",array("charge_upto"=>$charges_amt["upto_date"]), array("charge_id"=>$charge_val["id"],"accountid"=>$user_account["id"]));		     		        
  		     }
		  }
               }
             }         
         }
      }    
    }
    
    function process_DID_charges(){
       $dids_data = $this->get_table_data("*", "dids",array("status"=> "0","accountid >"=>"0"));
       if($dids_data){
         foreach($dids_data as $did_value){
            if($did_value['parent_id'] > 0){
               $parent_id = $did_value['parent_id'];
               while($parent_id != 0){
                $reseller_dids = $this->get_table_data("*", "reseller_pricing",array("reseller_id"=> $parent_id,"note"=>$did_value['number']));
                $reseller_dids = $reseller_dids[0];
                if(($parent_id == $reseller_dids['reseller_id'] && $did_value['accountid'] > 0) || $reseller_dids['parent_id'] == 0){
                   //Apply charges to resellers customers.
                   $this->manage_customer_DID_charge($did_value);
                }else{
                  //Apply charges to Resellers.
                  $this->manage_reseller_DID_charge($reseller_dids);
                }
                $parent_id = $reseller_dids['parent_id'];
               }
            }else{
                $this->manage_customer_DID_charge($did_value);
            }
         }
       }
    }
    function manage_customer_DID_charge($did_data){
        $currentdate = gmdate("Y-m-d H:i:s");
        $invoiceid = 0;
        $did_data['accountid'] = ($did_data['accountid'] == 0 && $did_data['parent_id'] > 0)?$did_data['parent_id']:$did_data['accountid'];

        $user_account = $this->account_arr[$did_data['accountid']];
        if($user_account["posttoexternal"] == 0){
           $billing_cycle = "2";
        }else{
           $billing_cycle = $user_account["sweep_id"];
 	      }         
        $charge_upto = ($did_data["charge_upto"] != "0000-00-00 00:00:00" && $did_data["charge_upto"] != "")?$did_data["charge_upto"]:$did_data["assign_date"];
	  if($charge_upto != "0000-00-00 00:00:00" && $charge_upto != "" && strtotime($charge_upto) < strtotime($currentdate) && $user_account["id"] > 0){
	    $charges_amt = $this->calculate_charges($did_data["monthlycost"],$charge_upto,$currentdate,"2");
            if($charges_amt){
                $fromdate = gmdate("Y-m-d",strtotime($charge_upto));
                $todate = gmdate("Y-m-d",strtotime($charges_amt["upto_date"]));
	        if($user_account["posttoexternal"] == 0){
                    $invoiceid = $this->common_model->generate_receipt($user_account["id"],$charges_amt["charges"]);
                    $this->db->set('balance', 'balance-'.$charges_amt["charges"], FALSE);
                    $this->db->where('id', $user_account["id"]);
                    $this->db->update("accounts");                    
                    if($user_account["balance"] <= 0){
                        $this->db->update("dids",array("accountid"=> "0"), array("id"=>$did_data["id"]));
                    }
	        }    
	        $did_update_arr =  array("charge_upto"=>$charges_amt["upto_date"]);
                $this->db->update("dids",$did_update_arr, array("id"=>$did_data["id"]));
                $invoice_item_arr = array("accountid"=>$user_account["id"],
                                          "description"=>$did_data['number']."-".$fromdate." to ".$todate,
                                          "charge_id"=>$did_data['id'],"debit"=>$charges_amt["charges"],"invoiceid"=>$invoiceid,
                                          "created_date"=>$currentdate,"charge_type"=>"did_charge");

                $this->manage_invoice($invoice_item_arr);
	    }	    
	  }    
    }
    function manage_reseller_DID_charge($did_data){
        $invoiceid = 0;
        $currentdate = gmdate("Y-m-d H:i:s");
        $user_account = $this->account_arr[$did_data['reseller_id']];
        if($user_account["posttoexternal"] == 0){
           $billing_cycle = "2";
        }else{
           $billing_cycle = $charge_val["sweep_id"];
 	}         
        $charge_upto = ($did_data["charge_upto"] != "0000-00-00 00:00:00" && $did_data["charge_upto"] != "")?$did_data["charge_upto"]:$did_data["assign_date"];
	  if($charge_upto != "0000-00-00 00:00:00" && $charge_upto != "" && strtotime($charge_upto) < strtotime($currentdate) && $user_account["id"] > 0){
	    $charges_amt = $this->calculate_charges($did_data["monthlycost"],$charge_upto,$currentdate,"2");
            if($charges_amt){
                $fromdate = gmdate("Y-m-d",strtotime($charge_upto));
                $todate = gmdate("Y-m-d",strtotime($charges_amt["upto_date"]));

	        if($user_account["posttoexternal"] == 0){
                    $invoiceid = $this->common_model->generate_receipt($user_account["id"],$charges_amt["charges"]);
	            	$this->db->set('balance', 'balance-'.$charges_amt["charges"], FALSE);
                    $this->db->where('id', $user_account["id"]);
                    $this->db->update("accounts");                    
	        }                        
                $this->db->update("reseller_pricing",array("charge_upto"=>$charges_amt["upto_date"]), array("id"=>$did_data["id"]));
                $invoice_item_arr = array("accountid"=>$user_account["id"],
                                          "description"=>$did_data['number']."-".$fromdate." to ".$todate,
                                          "charge_id"=>$did_data['id'],"debit"=>$charges_amt["charges"],"invoiceid"=>$invoiceid,
                                          "created_date"=>$currentdate,"charge_type"=>"did_charge");

                $this->manage_invoice($invoice_item_arr);
	    }	    
	  }    
    }
    
    function get_table_data($select,$table,$where){
      $query = $this->db_model->getSelect($select, $table, $where);
      if($query->num_rows >0){
	$query_result = $query->result_array();
	return $query_result;
      }else{
	return false;
      }
    }    
    
    function Manage_Invoice($invoice_item_arr){
        $this->db->insert("invoice_item",$invoice_item_arr);
    }
    
    function calculate_charges($charge,$upto_date,$currentdate,$bill_cycle,$pro_rate="1"){
        $billing_cycle = ($bill_cycle == "0")?"1 day":"1 month";
	      $upto_time=strtotime($upto_date);
	      $daylen = 60*60*24;

       	$days_diff = floor((strtotime($currentdate)-strtotime($upto_date))/$daylen);
      	if($bill_cycle == "0" || $pro_rate == "1"){
          $num_of_prev_day = cal_days_in_month(CAL_GREGORIAN, date('m',$upto_time), date('Y',$upto_time));
          if($bill_cycle > 0){                    
            $temp_charge = $charge/$num_of_prev_day;
            $pre_post_charge = ($temp_charge*$days_diff);  
          }else{
            $pre_post_charge = "0.00";
          }
      	}else{
          $num_of_prev_day = cal_days_in_month(CAL_GREGORIAN, date('m',$upto_time), date('Y',$upto_time));
          $pre_post_charge = (($charge*$days_diff)/$num_of_prev_day);
      	}
      	$total_charge =  ($charge + $pre_post_charge);
	
        $cycle = strtotime("+".$billing_cycle);	
      	$updated_date = gmdate('Y-m-d H:i:s',$cycle);
       if($total_charge > 0){
      	  $charge_arr = array("upto_date"=>$updated_date,"charges"=>$total_charge);
    	    return $charge_arr;
    	 }else{
    	     return false;
    	 }
  }    
}
?> 
