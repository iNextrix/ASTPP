<?php

// ##############################################################################
// ASTPP - Open Source VoIP Billing Solution
//
// Copyright (C) 2016 iNextrix Technologies Pvt. Ltd.
// Samir Doshi <samir.doshi@inextrix.com>
// ASTPP Version 3.0 and above
// License https://www.gnu.org/licenses/agpl-3.0.html
//
// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU Affero General Public License as
// published by the Free Software Foundation, either version 3 of the
// License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU Affero General Public License for more details.
//
// You should have received a copy of the GNU Affero General Public License
// along with this program. If not, see <http://www.gnu.org/licenses/>.
// ##############################################################################
class DID_purchase_model extends CI_Model {
	function __construct() {
		parent::__construct ();
	}
	function getavailable_did_list($flag, $start = 0, $limit = 0){ 
		$accountinfo = $this->session->userdata ( 'accountinfo' );
		$this->db_model->build_search ( 'did_purchase_list_search' );
		$session_data['reseller_id']=$this->session->userdata ( 'did_reseller_id' );
		if(!empty($this->session->userdata ( 'did_purchase_list_search')) && $this->session->userdata ( 'did_purchase_list_search' )!= '' )
		{
			$where ['accountid'] = 0;
			$where ['status'] = 0;
			if( $session_data['reseller_id'] > 0 ){
				if ($flag) {
					
					$query = $this->db_model->getJionQuery('dids','dids.id,dids.product_id as productid,dids.province,dids.city,dids.number,dids.accountid,dids.country_id,dids.cost,dids.call_type,dids.leg_timeout,dids.maxchannels,dids.extensions,reseller_products.buy_cost,reseller_products.setup_fee,
reseller_products.price,reseller_products.billing_type,reseller_products.billing_days,
,reseller_products.product_id',array('reseller_products.account_id'=>$session_data['reseller_id'],'dids.status'=>0,'dids.parent_id'=>$session_data['reseller_id'],'dids.accountid'=>0), 'reseller_products','dids.product_id=reseller_products.product_id', 'inner', $limit , $start,'DESC','dids.id');
				}else{
					$query = $this->db_model->getJionQueryCount('dids', 'dids.id,dids.product_id as productid,dids.province,dids.city,dids.number,dids.accountid,dids.country_id,dids.cost,dids.call_type,dids.leg_timeout,dids.maxchannels,dids.extensions,reseller_products.buy_cost,reseller_products.setup_fee,
reseller_products.price,reseller_products.billing_type,reseller_products.billing_days,
,reseller_products.product_id',array('reseller_products.account_id'=>$session_data['reseller_id'],'dids.status'=>0,'dids.parent_id'=>$session_data['reseller_id'],'dids.accountid'=>0), 'reseller_products','dids.product_id=reseller_products.product_id', 'inner', $limit , $start,'DESC','dids.id');
	
				}
			}else{
				if ($flag) {
					$query = $this->db_model->select ( "*,product_id as productid", "dids", $where, "id", "desc", $limit, $start );
				} else {
					$query = $this->db_model->countQuery ( "*", "dids", $where );
				}
			}
			return $query;
		}else{
			$where ['id'] = 0;
			if ($flag) {
				$query = $this->db_model->select ( "*,product_id as productid", "dids", $where, "id", "desc", $limit, $start );
			} else {
				$query = $this->db_model->countQuery ( "*", "dids", $where );
			}
			return $query;
		}
	}
	function did_forward()
	{
		//$where=array('id'=>$id);
		$query = $this->db_model->getSelect("*", "dids");
		$result =$query->result_array();
		return $result;
	}
	function update_did_purchase($data) {
		// print_r($data); die;
		if(isset($data['always_vm_flag'])){
			$data['always_vm_flag']=0;
		}else{
			$data['always_vm_flag']=1;
		}
		if(isset($data['user_busy_vm_flag'])){
			$data['user_busy_vm_flag']=0;
		}else{
			$data['user_busy_vm_flag']=1;
		}
		if(isset($data['user_not_registered_vm_flag'])){
			$data['user_not_registered_vm_flag']=0;
		}else{
			$data['user_not_registered_vm_flag']=1;
		}
		if(isset($data['no_answer_vm_flag'])){
			$data['no_answer_vm_flag']=0;
		}else{
			$data['no_answer_vm_flag']=1;
		}
		unset($data["action"]);
		unset($data["id"]);

		$whr = "id IN ($data[ids])";
		$this->db->where ($whr);
		unset($data["ids"]);
		 return $this->db->update("dids", $data);
		//  print_r($this->db->last_query()); die;
   }
}
