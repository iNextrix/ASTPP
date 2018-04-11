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
class Charges_model extends CI_Model {
	function Charges_model() {
		parent::__construct ();
	}
	function getcharges_list($flag, $start = 0, $limit = 0) {
		$this->db_model->build_search ( 'charges_list_search' );
		if ($this->session->userdata ( 'logintype' ) == 1 || $this->session->userdata ( 'logintype' ) == 5) {
			$account_data = $this->session->userdata ( "accountinfo" );
			$reseller = $account_data ['id'];
			$where = array (
					"reseller_id" => $reseller 
			);
		} else {
			$where = array (
					"reseller_id" => "0" 
			);
		}
		if ($flag) {
			$query = $this->db_model->select ( "*", "charges", $where, "id", "ASC", $limit, $start );
		} else {
			$query = $this->db_model->countQuery ( "*", "charges", $where );
		}
		return $query;
	}
	function add_charge($add_array) {
		/*
		 * ASTPP 3.0
		 * Charges Add time last creation date update
		 */
		$add_array ['creation_date'] = gmdate ( 'Y-m-d H:i:s' );
		/**
		 * *******************************************************************************************
		 */
		// Get Account Information
		$accountinfo = $this->session->userdata('accountinfo');
		$reseller_id = ($accountinfo['type'] == 1 || $accountinfo['type'] ==5) ? $accountinfo['id'] : 0 ;
		
		unset ( $add_array ['action'] );
		$this->db->insert ( "charges", $add_array );
		$insert_id = $this->db->insert_id ();
		$data = $this->db_model->getSelect ( "*", "accounts", array (
				"pricelist_id" => $add_array ['pricelist_id'],'reseller_id'=>$reseller_id,'deleted'=>0
		) );
		if ($data->num_rows () > 0) {
			foreach ( $data->result_array () as $key => $value ) {
				$this->common->mail_to_users ( 'add_subscription', $value );
			}
		}
		return $insert_id;
	}
	function edit_charge($data, $id) {
		unset ( $data ['action'] );
		/*
		 * ASTPP 3.0
		 * Charges edit time last modified date update
		 */
		$data ['last_modified_date'] = gmdate ( 'Y-m-d H:i:s' );
		/**
		 * *******************************************************************************************
		 */
		if ($this->session->userdata ( 'logintype' ) == 1 || $this->session->userdata ( 'logintype' ) == 5) {
			$account_data = $this->session->userdata ( "accountinfo" );
			$data ['reseller_id'] = $account_data ['id'];
		} else {
			$data ['reseller_id'] = "0";
		}
		$this->db->where ( "id", $id );
		$this->db->update ( "charges", $data );
		
		if ($data ['pricelist_id'] == 0) {
			$this->db->where ( "charge_id", $id );
			$this->db->delete ( "charge_to_account" );
		}
	}
	function remove_charge($id) {
		// Get Account Information
		$accountinfo = $this->session->userdata('accountinfo');
		$reseller_id = ($accountinfo['type'] == 1 || $accountinfo['type'] ==5) ? $accountinfo['id'] : 0 ;
		// Get information of charges before remove it to notify user
		$data = (array)$this->db->get_where('charges',array("id"=>$id))->first_row();
		$this->db->where ( "id", $id );
		$this->db->delete ( "charges" );
		$this->db->where ( "charge_id", $id );
		$this->db->delete ( "charge_to_account" );
		// Send notifications to users for removed subscription,If they are belongs to this rate group.
		if($data['pricelist_id'] > 0){
			$data = $this->db->get_where('accounts',array("pricelist_id"=>$data['pricelist_id'],'reseller_id'=>$reseller_id,'deleted'=>0));
			if ($data->num_rows () > 0) {
				foreach ( $data->result_array () as $key => $value ) {
					$this->common->mail_to_users ( 'remove_subscription', $value );
				}
			}
		}	
		return true;
	}
	function add_account_charges($pricelistid, $chargeid, $flag) {
		if ($flag) {
			$this->db->where ( "charge_id", $chargeid );
			$this->db->delete ( "charge_to_account" );
		}
		$account = $this->db_model->getSelect ( "*", "accounts", array (
				"pricelist_id" => $pricelistid 
		) );
		if ($account->num_rows () > 0) {
			foreach ( $account->result_array () as $key => $value ) {
				$charge_arr = array (
						"charge_id" => $chargeid,
						"accountid" => $value ['id'],
						"assign_date" => gmdate ( "Y-m-d H:i:s" ) 
				);
				$this->db->insert ( "charge_to_account", $charge_arr );
				require_once (APPPATH . 'controllers/ProcessCharges.php');
				$ProcessCharges = new ProcessCharges ();
				$Params = array (
						"ChargeID" => $chargeid,
						"AccountInfo" => $value 
				);
				$ProcessCharges->BillAccountCharges ( "ACCOUNTSUBSCRIPTION", $Params );
			}
		}
	}
}
