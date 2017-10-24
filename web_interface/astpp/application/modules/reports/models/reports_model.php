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
class Reports_model extends CI_Model {
	function Reports_model() {
		parent::__construct ();
	}
	/**
	 * ASTPP 3.0
	 * For Detail Customer Report List,Export
	 * *
	 */
	function getcustomer_cdrs($flag, $start, $limit, $export = false) {
		$this->db_model->build_search ( 'customer_cdr_list_search' );
		$account_data = $this->session->userdata ( "accountinfo" );
		$where ['reseller_id'] = $account_data ['type'] == 1 ? $account_data ['id'] : 0;
		// $where['type']=0;
		if ($this->session->userdata ( 'advance_search' ) != 1) {
			$where ['callstart >= '] = date ( "Y-m-d" ) . " 00:00:00";
			$where ['callstart <='] = date ( "Y-m-d" ) . " 23:59:59";
		}
		
		$types = array (
				'0',
				'3' 
		);
		// $this->db->or_where_in('type', $types);
		$this->db->where_in ( 'type', $types );
		
		$this->db->where ( $where );
		if (isset ( $_GET ['sortname'] ) && $_GET ['sortname'] != 'undefined') {
			$this->db->order_by ( $_GET ['sortname'], ($_GET ['sortorder'] == 'undefined') ? 'desc' : $_GET ['sortorder'] );
		} else {
			$this->db->order_by ( "callstart desc" );
		}
		if ($flag) {
			if (! $export)
				$this->db->limit ( $limit, $start );
				// Add is_recording for enabled recording.
			$this->db->select ( 'callstart,callerid,callednum,pattern,notes,billseconds,disposition,debit,cost,accountid,pricelist_id,calltype,trunk_id,uniqueid' );
		} else {
			$this->db->select ( 'count(*) as count,sum(billseconds) as billseconds,sum(debit) as total_debit,sum(cost) as total_cost,group_concat(distinct(pricelist_id)) as pricelist_ids,group_concat(distinct(trunk_id)) as trunk_ids,group_concat(distinct(accountid)) as accounts_ids' );
		}
		$result = $this->db->get ( 'cdrs' );
		return $result;
	}
	/*
	 * Below function using by Detail reseller report
	 */
	function getreseller_cdrs($flag, $start, $limit, $export = false) {
		$this->db_model->build_search ( 'reseller_cdr_list_search' );
		$account_data = $this->session->userdata ( "accountinfo" );
		$where ['reseller_id'] = $account_data ['type'] == 1 ? $account_data ['id'] : 0;
		$where ["accountid <>"] = $account_data ['type'] == 1 ? $account_data ['id'] : 0;
		if ($this->session->userdata ( 'advance_search' ) != 1) {
			$where ['callstart >= '] = date ( "Y-m-d" ) . " 00:00:00";
			$where ['callstart <='] = date ( "Y-m-d" ) . " 23:59:59";
		}
		$this->db->where ( $where );
		if (isset ( $_GET ['sortname'] ) && $_GET ['sortname'] != 'undefined') {
			$this->db->order_by ( $_GET ['sortname'], ($_GET ['sortorder'] == 'undefined') ? 'desc' : $_GET ['sortorder'] );
		} else {
			$this->db->order_by ( "callstart desc" );
		}
		if ($flag) {
			if (! $export)
				$this->db->limit ( $limit, $start );
			$this->db->select ( 'callstart,callerid,callednum,pattern,notes,billseconds,disposition,debit,cost,accountid,pricelist_id,calltype' );
		} else {
			$this->db->select ( 'count(*) as count,sum(billseconds) as billseconds,sum(debit) as total_debit,sum(cost) as total_cost,group_concat(distinct(pricelist_id)) as pricelist_ids' );
		}
		$result = $this->db->get ( 'reseller_cdrs' );
		return $result;
	}
	/*
	 * Below function using by Detail provider report
	 */
	function getprovider_cdrs($flag, $start, $limit, $export = false) {
		$this->db_model->build_search ( 'provider_cdr_list_search' );
		$account_data = $this->session->userdata ( "accountinfo" );
		$where = array ();
		if ($account_data ['type'] == 3) {
			$where ['provider_id'] = $account_data ['id'];
		}
		
		if ($this->session->userdata ( 'advance_search' ) != 1) {
			$where ['callstart >= '] = date ( "Y-m-d" ) . " 00:00:00";
			$where ['callstart <='] = date ( "Y-m-d" ) . " 23:59:59";
		}
		$this->db->where ( 'trunk_id !=', '' );
		$this->db->where ( $where );
		if (isset ( $_GET ['sortname'] ) && $_GET ['sortname'] != 'undefined') {
			$this->db->order_by ( $_GET ['sortname'], ($_GET ['sortorder'] == 'undefined') ? 'desc' : $_GET ['sortorder'] );
		} else {
			$this->db->order_by ( "callstart desc" );
		}
		if ($flag) {
			if (! $export)
				$this->db->limit ( $limit, $start );
			$this->db->select ( 'callstart,callerid,callednum,pattern,notes,billseconds,provider_call_cost,disposition,provider_id,cost' );
		} else {
			$this->db->select ( 'count(*) as count,sum(billseconds) as billseconds,sum(cost) as total_cost' );
		}
		$result = $this->db->get ( 'cdrs' );
		// echo $this->db->last_query();
		return $result;
	}
	function users_cdrs_list($flag, $accountid, $entity_type, $start, $limit) {
		$where = "callstart >= '" . date ( 'Y-m-d 00:00:00' ) . "' AND callstart <='" . date ( 'Y-m-d 23:59:59' ) . "' AND ";
		$account_type = $entity_type == 'provider' ? 'provider_id' : 'accountid';
		$where .= "accountid = '" . $accountid . "' ";
		// ~ if($entity_type == 'provider'){
		// ~ $where.="OR provider_id = '".$accountid."'";
		// ~ }
		$table = $entity_type == 'reseller' ? 'reseller_cdrs' : 'cdrs';
		if ($flag) {
			$query = $this->db_model->select ( "*", $table, $where, "callstart", "DESC", $limit, $start );
		} else {
			$query = $this->db_model->countQuery ( "*", $table, $where );
		}
		return $query;
	}
	
	/**
	 * ****
	 * ASTPP 3.0
	 * Payment to refill
	 * *****
	 */
	function getuser_refill_list($flag, $start, $limit) {
		$this->db_model->build_search ( 'cdr_refill_search' );
		$account_data = $this->session->userdata ( "accountinfo" );
		$this->db_model->build_search ( 'customer_cdr_list_search' );
		$where = array (
				"accountid" => $account_data ["id"] 
		);
		if ($flag) {
			$query = $this->db_model->select ( "*", "payments", $where, "payment_date", "DESC", $limit, $start );
		} else {
			$query = $this->db_model->countQuery ( "*", "payments", $where );
		}
		return $query;
	}
	function get_refill_list($flag, $start, $limit, $export = false) {
		$this->db_model->build_search ( 'cdr_refill_search' );
		$accountinfo = $this->session->userdata ( 'accountinfo' );
		$where ['payment_by'] = $accountinfo ['type'] == 1 ? $accountinfo ['id'] : - 1;
		if ($flag) {
			if ($export)
				$query = $this->db_model->select ( "*", "payments", $where, "payment_date", "DESC", "", "" );
			else
				$query = $this->db_model->select ( "*", "payments", $where, "payment_date", "DESC", $limit, $start );
		} else {
			$query = $this->db_model->countQuery ( "*", "payments", $where );
		}
		
		return $query;
	}
	/**
	 * ***************
	 */
	function getreseller_commission_list($flag, $start, $limit) {
		$this->db_model->build_search ( 'reseller_commission_search' );
		if ($this->session->userdata ( 'logintype' ) == 1 || $this->session->userdata ( 'logintype' ) == 5) {
			$accountinfo = $this->session->userdata ['accountinfo'];
			$reseller_id = $accountinfo ["id"];
		} else {
			$reseller_id = "0";
		}
		if ($flag) {
			$query = $this->db_model->select_by_in ( "*", "commission", "", "date", "DESC", $limit, $start, "", "reseller_id", $reseller_id );
		} else {
			$query = $this->db_model->countQuery_by_in ( "*", "commission", "", "reseller_id", $reseller_id );
		}
		
		return $query;
	}
	/**
	 * *********
	 * ASTPP 3.0
	 * Charge History
	 * **********
	 */
	function getcharges_list($flag, $start = 0, $limit = 0) {
		$accountinfo = $this->session->userdata ( 'accountinfo' );
		$reseller_id = $accountinfo ['id'];
		if ($accountinfo ['type'] == 1) {
			$where ['reseller_id'] = $reseller_id;
		} else {
			$where ['reseller_id'] = 0;
		}
		if ($this->session->userdata ( 'advance_search' ) != 1) {
			$where ['created_date >='] = gmdate ( "Y-m-01 00:00:01" );
			$where ['created_date <='] = gmdate ( "Y-m-d 23:59:59" );
		}
		$this->db_model->build_search ( 'charges_list_search' );
		$this->db->where ( $where );
		$whr = "( invoiceid =0 OR invoiceid in (  select id  from invoices where confirm = 1) )";
		$this->db->where ( $whr );
		$this->db->where ( 'item_type <>', 'STANDARD' );
		if ($flag) {
			$query = $this->db_model->select ( '*', "invoice_details", "", "id", "DESC", $limit, $start );
		} else {
			$query = $this->db_model->countQuery ( "*", "invoice_details", "" );
		}
		return $query;
	}
	
	/**
	 * *********************************
	 */
	/*
	 * ASTPP 3.0
	 * This function using for customer edit
	 */
	function get_customer_charge_list($flag, $accountid, $start = 0, $limit = 0) {
		$this->db_model->build_search ( 'charges_list_search' );
		$accountinfo = $this->session->userdata ( 'accountinfo' );
		$reseller_id = $accountinfo ['type'] == 1 ? $accountinfo ['id'] : 0;
		$where ['reseller_id'] = $reseller_id;
		$where ['accountid'] = $accountid;
		if ($this->session->userdata ( 'advance_search' ) != 1) {
			$where ['created_date >='] = gmdate ( "Y-m-1 00:00:00" );
			$where ['created_date <='] = gmdate ( "Y-m-d 23:59:59" );
		}
		if ($flag) {
			$query = $this->db_model->select ( "*", "invoice_details", $where, "id", "DESC", $limit, $start );
		} else {
			$query = $this->db_model->countQuery ( "*", "invoice_details", $where );
		}
		return $query;
	}
	function get_customer_refillreport($flag, $accountid, $start = 0, $limit = 0) {
		$where = array (
				"accountid" => $accountid 
		);
		if ($flag) {
			$query = $this->db_model->select ( "*", "payments", $where, "payment_date", "DESC", $limit, $start );
		} else {
			$query = $this->db_model->countQuery ( "*", "payments", $where );
		}
		
		return $query;
	}
/**
 * **********************************************************************
 */
}
