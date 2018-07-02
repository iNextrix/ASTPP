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
if (! defined ( 'BASEPATH' )) {
	exit ( 'No direct script access allowed' );
}
class did_lib extends MX_Controller {

	/**
	* Function will do allocation of DID and billing of it. 
	*
	* $request_from = array (Logged into account info.)
	* $accountid = Int. (Account id)
	* $did = numeric value (DID number)
	* $skip_balance_check = Boolean (True Or False)
	**/
	function did_billing_process($request_from,$accountid,$did,$skip_balance_check=false) {

		//Load DID module
		$this->load->module ( 'did/did' );	

		//Get account information 		
		$account = $this->db_model->getSelect ( "*", "accounts", array ('id' => $accountid ));
		$accountinfo = ( array ) $account->first_row ();
		//print_r($accountinfo);exit;

		$currency_name = $this->common->get_field_name ( 'currency', "currency", array (
				'id' => $accountinfo ['currency_id'] 
		) );

		//Get DID information
		$didinfo = $this->did->did_model->get_did_by_number ( $did );
		if ($request_from['logintype'] == 1 && $request_from['accountinfo']['id'] == $accountinfo ['id']){
			if ($didinfo['parent_id'] > 0)
			{
				return array("NOT_AVAL_FOR_PURCHASE","This DID is already purchased by someone.");
			}
		}

		//Check if account have any parent reseller or not. 0 = Admin
		if ($accountinfo ['reseller_id'] > 0) {
			$reseller_pricing_query = $this->db_model->getSelect ( "call_type,setup,extensions,monthlycost,connectcost,includedseconds,cost,inc", "reseller_pricing", array (
					"note" => $didinfo ['number'],
					'reseller_id' => $accountinfo ['reseller_id'] 
			) );
			$reseller_pricing_result = ( array ) $reseller_pricing_query->first_row ();
			$didinfo ['call_type'] = $reseller_pricing_result ['call_type'];
			$didinfo ['extensions'] = $reseller_pricing_result ['extensions'];
			$didinfo ['setup'] = $reseller_pricing_result ['setup'];
			$didinfo ['monthlycost'] = $reseller_pricing_result ['monthlycost'];
			$didinfo ['connectcost'] = $reseller_pricing_result ['connectcost'];
			$didinfo ['includedseconds'] = $reseller_pricing_result ['includedseconds'];
			$didinfo ['cost'] = $reseller_pricing_result ['cost'];
			$didinfo ['inc'] = $reseller_pricing_result ['inc'];
		}

		//Get account balance
		$available_bal = $this->db_model->get_available_bal ( $accountinfo );

		//Assigning did information to account array
		$accountinfo ['did_number'] = $didinfo ['number'];
		$accountinfo ['did_country_id'] = $didinfo ['country_id'];
		$accountinfo ['did_setup'] = $this->common_model->calculate_currency ( $didinfo ['setup'], '', $currency_name, true, true );
		$accountinfo ['did_monthlycost'] = $this->common_model->calculate_currency ( $didinfo ['monthlycost'], '', $currency_name, true, true );
		$accountinfo ['did_maxchannels'] = $didinfo ['maxchannels'];

		//Check if account have enough balance to purchase the DID.
		if ($available_bal >= ($didinfo ["setup"]+$didinfo ["monthlycost"]) || $skip_balance_check==true) {
			$this->db_model->update_balance ( $didinfo ['setup']+$didinfo ["monthlycost"], $accountinfo ['id'], "debit" );

			/*if (request_from = admin)
			{
				If (allocating to customer)
					update account id
				If (allocating to reseller)
					update parent id and also create new did entry in reseller_pricing
			}

			if (request_from = reseller)
			{
				If (reseller purchasing)
					update parent id and create new did entry in reseller pricing
				if (allocating to customer)
					update account id
				if (allocating to reseller)
					create new did entry in reseller pricing						
			}

			if (request from = customer)
			{
				update account id
			}
			
			*/
			//Admin (2 = admin, 1 = reseller, 0 = customer, 3 = provider)
			if ($request_from['logintype'] == 2 || ($request_from['logintype'] == 1 && $request_from['accountinfo']['id']) == $accountinfo ['id']){

				$field_name = $accountinfo ['type'] == '1' ? "parent_id" : 'accountid';

				//Update DID account id information
				$this->db_model->update ( "dids", array (
					$field_name => ($accountinfo ['id'] > 0)?$accountinfo ['id']:'',					
					"assign_date" => gmdate ( "Y-m-d H:i:s" ) 
				), array (
					"id" => $didinfo ['id']
				) );

				//If customer under reseller then create did in reseller pricing table for reseller.			
				if ($field_name == 'parent_id'){
					$this->did->did_model->insert_reseller_pricing ( $accountinfo, $didinfo );			
				}			
			}elseif($request_from['logintype'] == 1){

				$field_name = $accountinfo ['type'] == 1 ? "parent_id" : 'accountid';
				if ($accountinfo ['type'] == 0)
				{
					//Update DID account id information
					$this->db_model->update ( "dids", array (
						"accountid" => $accountinfo ['id'],					
						"assign_date" => gmdate ( "Y-m-d H:i:s" ) 
					), array (
						"id" => $didinfo ['id']
					) );
				}

				if ($accountinfo ['type'] == 1)
				{
					$this->did->did_model->insert_reseller_pricing ( $accountinfo, $didinfo );
				}
			}elseif($request_from['logintype'] == 0 || $request_from['logintype'] == 4){
				//Update DID account id information
				$this->db_model->update ( "dids", array (
					"accountid" => $accountinfo ['id'],					
					"assign_date" => gmdate ( "Y-m-d H:i:s" ) 
				), array (
					"id" => $didinfo ['id']
				) );
			}
			
			//Create invoice/receipt for purchase
			$this->common->add_invoice_details ( $accountinfo, "DIDCHRG", $didinfo ['setup']+$didinfo ["monthlycost"], "DID : ".$didinfo ['number']." (Setup Fee :".$accountinfo ['did_setup'].", Monthly Fee : ".$accountinfo ['did_monthlycost'].")" );
			
			require_once (APPPATH . 'controllers/ProcessCharges.php');
			$ProcessCharges = new ProcessCharges ();
			$Params = array (
					"DIDid" => $didinfo ['id'] 
			);
			$ProcessCharges->BillAccountCharges ( "DIDs", $Params );
			
			//Send email to user
			$this->common->mail_to_users ( 'email_add_did', $accountinfo, "", $didinfo ['number'] );			
			return array("SUCCESS","DID Purchased Successfully.");
		}else{			
			return array("INSUFFIECIENT_BALANCE","Insuffiecient fund to purchase this DID.");
		}
	}

	/**
	* Function will do did release process
	**/
	function did_release() {
		//TODO
	}
}	
?>
