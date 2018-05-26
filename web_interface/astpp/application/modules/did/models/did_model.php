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
class DID_model extends CI_Model {
	function DID_model() {
		parent::__construct ();
	}
	function add_did($add_array) {
		if ($add_array ['accountid'] > 0) {
			$add_array ['assign_date'] = gmdate ( 'Y-m-d H:i:s' );
		}
		unset ( $add_array ["action"] );
		$this->db->insert ( "dids", $add_array );
		//$last_id = $this->db->insert_id ();
		if ($add_array ['accountid'] > 0) {
			$this->did_lib->did_billing_process($this->session->userdata,$add_array ['accountid'],$add_array ['number'],true);
		}
		return true;
	}
	function insert_pricelist() {
		$insert_array = array (
				'name' => 'default',
				'markup' => '',
				'inc' => '' 
		);
		return $this->db->insert_id ();
	}
	function edit_did($data, $id, $number) {
		unset ( $data ["action"] );
		$this->db->where ( 'number', $number );
		$this->db->select ( 'parent_id,accountid' );
		$did_result = $this->db->get ( 'dids' );
		$did_result = ( array ) $did_result->first_row ();
		if ($did_result ['accountid'] > 0 || $did_result ['parent_id'] > 0) {
			$data ['accountid'] = $did_result ['accountid'];
			$data ['parent_id'] = $did_result ['parent_id'];
		}
		$data ['last_modified_date'] = gmdate ( "Y-m-d H:i:s" );
		if ($did_result ['accountid'] == 0 && $data ['accountid'] > 0) {
			$data ['assign_date'] = gmdate ( "Y-m-d H:i:s" );
		}
		$this->db->where ( "number", $number );
		$this->db->where ( "id", $id );
		$this->db->update ( "dids", $data );
		/**
		 * ***********************************
		 */
		if ($did_result ['parent_id'] > 0) {
			$update_array ['call_type'] = $data ['call_type'];
			$update_array ['extensions'] = $data ['extensions'];
			$update_array ['note'] = $data ['number'];
			$this->db->where ( "note", $number );
			$this->db->update ( "reseller_pricing", $update_array );
		}
		if ($did_result ['accountid'] == 0 && $data ['accountid'] > 0) {
			$this->did_lib->did_billing_process($this->session->userdata,$data ['accountid'],$number,true);			
		}
	}
	function getdid_list($flag, $start = 0, $limit = 0) {
		$accountinfo = $this->session->userdata ( 'accountinfo' );
		$parent_id = $accountinfo ['type'] == 1 ? $accountinfo ['reseller_id'] : 0;
		
		if ($accountinfo ['type'] == 1) {
			$where = array (
					'reseller_pricing.reseller_id' => $accountinfo ['id'],
					"reseller_pricing.parent_id" => $parent_id 
				);
			if ($flag) {
				if ($accountinfo ['reseller_id'] > 0) {
					$search_string = $this->db_model->build_search_string ( 'did_list_search' );
					$search_string = ! empty ( $search_string ) ? " AND " . $search_string : null;
					if (isset ( $_GET ['sortname'] ) && $_GET ['sortname'] != 'undefined') {
						$this->db->order_by ( $_GET ['sortname'], ($_GET ['sortorder'] == 'undefined') ? 'desc' : $_GET ['sortorder'] );
					}
					$this->db->limit ( $limit, $start );
					$query = $this->db->query ( "SELECT a.note AS number,a.*,IF((SELECT COUNT( id ) AS count FROM reseller_pricing AS b WHERE b.parent_id =" . $accountinfo ['id'] . " AND a.note = b.note ) >0,(SELECT reseller_id AS accountid FROM reseller_pricing AS c WHERE c.note = a.note AND c.parent_id =" . $accountinfo ['id'] . "), (SELECT accountid from dids as d where d.parent_id = " . $accountinfo ['id'] . " AND d.number=a.note)) AS accountid FROM reseller_pricing AS a where a.reseller_id=" . $accountinfo ['id'] . " AND a.parent_id =" . $accountinfo ['reseller_id'] . $search_string );
				} else {
					$this->db_model->build_search ( 'did_list_search' );
					$query = $this->db_model->getJionQuery("reseller_pricing", "*,note as number,IF((SELECT COUNT( id ) AS count FROM reseller_pricing AS b WHERE b.parent_id =" . $accountinfo ['id'] . " AND reseller_pricing.note = b.note ) >0,(SELECT reseller_id AS accountid FROM reseller_pricing AS c WHERE c.note = reseller_pricing.note AND c.parent_id =" . $accountinfo ['id'] . "), (SELECT accountid from dids as d where d.parent_id = " . $accountinfo ['id'] . " AND d.number=reseller_pricing.note)) AS accountid", $where, "dids", "reseller_pricing.did_id=dids.id", '',  $limit, $start,"reseller_pricing.id", '', '');
				}
			} else {
				$this->db_model->build_search ( 'did_list_search' );
				$query = $this->db_model->getJionQueryCount("reseller_pricing", "*", $where, "dids", "reseller_pricing.did_id=dids.id", '', '', '', '', '', '');
			}
		} else {
			$this->db_model->build_search ( 'did_list_search' );
			if ($flag) {
				/*
				 * ASTPP 3.0 last_modified_date,assign_date put in query.
				 */
				$this->db->select ( 'dids.id,dids.connectcost,dids.includedseconds,dids.last_modified_date,dids.assign_date,dids.number,dids.extensions,dids.call_type,dids.country_id,dids.init_inc,dids.inc,dids.cost,dids.setup,dids.monthlycost,dids.status,(CASE when parent_id > 0 THEN (SELECT reseller_id as accountid from reseller_pricing where dids.number=reseller_pricing.note AND reseller_pricing.parent_id=0) ELSE dids.accountid END ) as accountid' );
				if (isset ( $_GET ['sortname'] ) && $_GET ['sortname'] != 'undefined') {
					$this->db->order_by ( $_GET ['sortname'], ($_GET ['sortorder'] == 'undefined') ? 'desc' : $_GET ['sortorder'] );
				}
				/**
				 * *******************************************************88
				 */
				$this->db->limit ( $limit, $start );
				$query = $this->db->get ( 'dids' );
			} else {
				$query = $this->db_model->countQuery ( "*", "dids" );
			}
		}
		return $query;
	}
	function remove_did($id) {
		/**
		 * ASTPP 3.0
		 * For Email broadcast
		 * *
		 */
		$where = array (
				'id' => $id 
		);
		$this->db->where ( $where );
		$this->db->select ( 'accountid,number,parent_id' );
		$did_info = ( array ) $this->db->get ( 'dids' )->first_row ();
		$this->db->where ( "id", $id );
		$this->db->delete ( "dids" );
		$this->db->where ( "note", $did_info ['number'] );
		$this->db->delete ( "reseller_pricing" );
		if ($did_info ['accountid'] == 0 && $did_info ['parent_id'] > 0) {
			$accountinfo = $this->session->userdata ( 'accountinfo' );
			$did_info ['accountid'] = $accouninfo ['id'];
			$accountinfo = ( array ) $this->db->get_where ( 'accounts', array (
					"id" => $did_info ['parent_id'] 
			) )->first_row ();
			$accountinfo ['did_number'] = $accountinfo ['number'];
			$this->common->mail_to_users ( 'email_remove_did', $accountinfo );
		} elseif ($did_info ['accountid'] > 0) {
			$accountinfo = ( array ) $this->db->get_where ( 'accounts', array (
					"id" => $did_info ['accountid'] 
			) )->first_row ();
			$accountinfo ['did_number'] = $did_info ['number'];
			$this->common->mail_to_users ( 'email_remove_did', $accountinfo );
		}
		return true;
	}
	function get_coutry_id_by_name($field_value) {
		$this->db->where ( "country", ucfirst ( $field_value ) );
		$query = $this->db->get ( 'countrycode' );
		$data = $query->result ();
		if ($query->num_rows () > 0)
			return $data [0]->id;
		else
			return '';
	}
	function bulk_insert_dids($field_value) {
		$this->db->insert_batch ( 'dids', $field_value );
		$affected_row = $this->db->affected_rows ();
		return $affected_row;
	}
	function get_account($accountdata) {
		$q = "SELECT * FROM accounts WHERE number = '" . $this->db->escape_str ( $accountdata ) . "' AND status = 0";
		$query = $this->db->query ( $q );
		if ($query->num_rows () > 0) {
			$row = $query->row_array ();
			return $row;
		}
		
		$q = "SELECT * FROM accounts WHERE cc = '" . $this->db->escape_str ( $accountdata ) . "' AND status = 0";
		$query = $this->db->query ( $q );
		if ($query->num_rows () > 0) {
			$row = $query->row_array ();
			return $row;
		}
		
		$q = "SELECT * FROM accounts WHERE accountid = '" . $this->db->escape_str ( $accountdata ) . "' AND status = 0";
		$query = $this->db->query ( $q );
		if ($query->num_rows () > 0) {
			$row = $query->row_array ();
			return $row;
		}
		
		return NULL;
	}
	function get_did_reseller_new($did, $reseller_id = "") {
		$sql = "SELECT dids.number AS number, " . "reseller_pricing.monthlycost AS monthlycost, " . "reseller_pricing.prorate AS prorate, " . "reseller_pricing.setup AS setup, " . "reseller_pricing.cost AS cost, " . "reseller_pricing.connectcost AS connectcost, " . "reseller_pricing.includedseconds AS includedseconds, " . "reseller_pricing.inc AS inc, " . "reseller_pricing.disconnectionfee AS disconnectionfee, " . "dids.provider_id AS provider_id, " . "dids.country_id AS country_id, " . "dids.city AS city, " . "dids.province AS province, " . "dids.extensions AS extensions, " . "dids.accountid AS account, " . "dids.variables AS variables, " . "dids.options AS options, " . "dids.maxchannels AS maxchannels, " . "dids.chargeonallocation AS chargeonallocation, " . "dids.allocation_bill_status AS allocation_bill_status, " . "dids.limittime AS limittime, " . "dids.dial_as AS dial_as, " . "dids.status AS status " . "FROM dids, reseller_pricing " . "WHERE dids.id = " . $did . " AND reseller_pricing.type = '1' AND reseller_pricing.reseller_id = " . $reseller_id;
		$query = $this->db->query ( $sql );
		if ($query->num_rows () > 0) {
			return $query->row_array ();
		}
	}
	function get_did_by_number($number) {
		$this->db->where ( "id", $number );
		$this->db->or_where ( "number", $number );
		$query = $this->db->get ( "dids" );
		if ($query->num_rows () > 0)
			return $query->row_array ();
		else
			return false;
	}
	function edit_did_reseller($did_id, $post) {
		$accountinfo = $this->session->userdata ( 'accountinfo' );
		
		$where_array = array (
				'reseller_id' => $accountinfo ['id'],
				'note' => $post ['number'],
				'type' => '1' 
		);
		$this->db->where ( $where_array );
		$flag = '0';
		$query = $this->db->get ( 'reseller_pricing' );
		if ($query->num_rows () > 0) {
			$flag = '1';
		}
		
		$this->insert_reseller_pricing ( $accountinfo, $post );
		return $flag;
	}
	function delete_pricing_reseller($username, $number) {
		$where = array (
				'reseller_id' => $username,
				'note' => $number,
				'type' => '1' 
		);
		$this->db->where ( $where );
		$this->db->delete ( 'reseller_pricing' );
		return true;
	}
	function insert_reseller_pricing($accountinfo, $post) {
		$insert_array = array (
				'reseller_id' => $accountinfo ['id'],
				'type' => '1',
				'did_id' => $post ['id'],
				'note' => $post ['number'],
				'parent_id' => $accountinfo ['reseller_id'],
				'monthlycost' => $post ['monthlycost'],
				'prorate' => $post ['prorate'],
				'setup' => $post ['setup'],
				'cost' => $post ['cost'],
				'inc' => $post ['inc'],
				'extensions' => $post ['extensions'],
				'call_type' => $post ['call_type'],
				'disconnectionfee' => $post ['disconnectionfee'],
				'connectcost' => $post ['connectcost'],
				'includedseconds' => $post ['includedseconds'],
				'status' => '0',
				'assign_date' => gmdate ( 'Y-m-d H:i:s' ) 
		);
		
		$this->db->insert ( 'reseller_pricing', $insert_array );
		return true;
	}
	function update_dids_reseller($post) {
		$where = array (
				'id' => $post ['did_id'] 
		);
		$update_array = array (
				'dial_as' => $post ['dial_as'],
				'extensions' => $post ['extension'] 
		);
		$this->db->where ( $where );
		$this->db->update ( 'dids', $update_array );
	}
	function delete_routes($id, $number, $pricelist_id) {
		$number = "^" . $number . ".*";
		$where = array (
				'pricelist_id' => $pricelist_id,
				'pattern' => $number 
		);
		$this->db->where ( $where );
		$this->db->delete ( 'routes' );
	}
	function insert_routes($post, $pricelist_id) {
		$commment = "DID:" . $post ['country'] . "," . $post ['province'] . "," . $post ['city'];
		$insert_array = array (
				'pattern' => "^" . $post ['number'] . ".*",
				'comment' => $commment,
				'pricelist_id' => $pricelist_id,
				'connectcost' => $post ['connectcost'],
				'includedseconds' => $post ['included'],
				'cost' => $post ['cost'],
				'inc' => $post ['inc'] 
		);
		$this->db->insert ( 'routes', $insert_array );
		return true;
	}
	function add_invoice_data($accountid, $charge_type, $description, $credit) {
		$insert_array = array (
				'accountid' => $accountid,
				'charge_type' => $charge_type,
				'description' => $description,
				'credit' => $credit,
				'charge_id' => '0',
				'package_id' => '0' 
		);
		
		$this->db->insert ( 'invoice_item', $insert_array );
		return true;
	}
	function check_unique_did($number) {
		$where = array (
				'number' => $number 
		);
		$query = $this->db_model->countQuery ( "*", "dids", $where );
		return $query;
	}
}
