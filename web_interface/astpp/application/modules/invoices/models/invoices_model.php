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
class Invoices_model extends CI_Model {
	function Invoices_model() {
		parent::__construct ();
	}
	function get_invoice_list($flag, $start = 100, $limit = 100) {
		$where = array ();
		
		$accountinfo = $this->session->userdata ( 'accountinfo' );		
		$reseller_id = ($accountinfo ['type'] == - 1 || $accountinfo ['type'] == 2 || $accountinfo ['type'] == 4 ) ? 0 : $accountinfo ['id'];
		$this->db->where ( 'reseller_id', $reseller_id );
		$this->db->select ( 'id' );
		$result = $this->db->get ( 'accounts' );
		
		$this->db_model->build_search ( 'invoice_list_search' );
		/**
		 * **
		 * Invoice manually
		 * *
		 */
		
		if ($this->session->userdata ( 'advance_search' ) != 1) {
			if ($result->num_rows () > 0) {
				$acc_arr = array ();
				$result = $result->result_array ();
				foreach ( $result as $data ) {
					$acc_arr [] = $data ['id'];
				}
				$this->db->where_in ( 'accountid', $acc_arr );
				$this->db->where ( 'deleted', 0 );
				if ($flag) {
					$this->db->select ( '*' );
				} else {
					$this->db->select ( 'count(id) as count' );
				}
				if ($flag) {					
					$this->db->order_by ( 'invoice_date', 'desc' );
					$this->db->limit ( $limit, $start );
				}
				$result = $this->db->get ( 'invoices' );
				// echo $this->db->last_query();exit;
				if ($flag) {
					return $result;
				} else {
					$result = $result->result_array ();
					return $result [0] ['count'];
				}
			} else {
				if ($flag) {
					$query = ( object ) array (
							'num_rows' => 0 
					);
				} else {
					$query = 0;
				}
				
				return $query;
			}
		} else {
			
			if ($result->num_rows () > 0) {
				/**
				 * **
				 * Invoice manually
				 * *
				 */
				$acc_arr = array ();
				$result = $result->result_array ();
				foreach ( $result as $data ) {
					$acc_arr [] = $data ['id'];
				}
				$this->db->where_in ( 'accountid', $acc_arr );
			}
			
			if ($flag) {
				$this->db->select ( '*' );
			} else {
				$this->db->select ( 'count(id) as count' );
			}
			if ($flag) {
				$this->db->order_by ( 'invoice_date', 'desc' );
				$this->db->limit ( $limit, $start );
			}
			$result = $this->db->get ( 'invoices' );
			// echo $this->db->last_query();exit;
			if ($result->num_rows () > 0) {
				if ($flag) {
					
					return $result;
				} else {
					$result = $result->result_array ();
					
					return $result [0] ['count'];
				}
			} else {
				if ($flag) {
					
					$query = ( object ) array (
							'num_rows' => 0 
					);
					// echo '<pre>'; print_r($query);
				} else {
					$query = 0;
				}
				return $query;
			}
		}
	}
	function getCdrs_invoice($invoiceid) {
		$this->db->where ( 'invoiceid', $invoiceid );
		$this->db->from ( 'cdrs' );
		$query = $this->db->get ();
		return $query;
	}
	function get_account_including_closed($accountdata) {
		$q = "SELECT * FROM accounts WHERE number = '" . $this->db->escape_str ( $accountdata ) . "'";
		$query = $this->db->query ( $q );
		if ($query->num_rows () > 0) {
			$row = $query->row_array ();
			return $row;
		}
		$q = "SELECT * FROM accounts WHERE accountid = '" . $this->db->escape_str ( $accountdata ) . "'";
		$query = $this->db->query ( $q );
		if ($query->num_rows () > 0) {
			$row = $query->row_array ();
			return $row;
		}
		
		return NULL;
	}
	function get_user_invoice_list($flag, $start = 0, $limit = 0) {
		$this->db_model->build_search ( 'invoice_list_search' );
		$accountinfo = $this->session->userdata ( 'accountinfo' );
		/**
		 * **
		 * Invoice manually
		 * *
		 */
		$where = array (
				"accountid" => $accountinfo ['id'],
				'confirm' => 1 
		);
		if ($flag) {
			$query = $this->db_model->select ( "*", "invoices", $where, "invoice_date", "desc", $limit, $start );
		} else {
			$query = $this->db_model->countQuery ( "*", "invoices", $where );
		}
		// echo $this->db->last_query();exit;
		return $query;
	}
	// 22_1
	function getinvoiceconf_list($flag, $start = 0, $limit = 0) {
		$where = array ();
		$logintype = $this->session->userdata ( 'logintype' );
		
		if ($logintype == 1 || $logintype == 5) {
			
			$where = array (
					"accountid" => $this->session->userdata ["accountinfo"] ['id'] 
			);
		}
		
		if ($flag) {
			$query = $this->db_model->select ( "*", "invoice_conf", $where, "id", "ASC", $limit, $start );
		} else {
			$query = $this->db_model->countQuery ( "*", "invoice_conf", $where );
		}
		// echo $this->db->last_query();
		return $query;
	}
	function get_invoiceconf($accountid) {
		$return_array = array ();
		$logintype = $this->session->userdata ( 'logintype' );
		if ($logintype == 1 || $logintype == 5) {
			
			$where = array (
					"accountid" => $this->session->userdata ["accountinfo"] ['id'] 
			);
		} else {
			if ($logintype == - 1 || $logintype == 2) {
				$accountid = '1';
			}
			$where = array (
					'id' => $accountid 
			);
		}
		$query = $this->db_model->getSelect ( "*", "invoice_conf", $where );
		foreach ( $query->result_array () as $key => $value ) {
			$return_array = $value;
		}
		return $return_array;
	}
	function save_invoiceconf($post_array) {
		$where_arr = array (
				'id' => $post_array ['id'] 
		);
		unset ( $post_array ['action'] );
		if ($post_array ['id'] != "") {
			$this->db->where ( $where_arr );
			unset ( $post_array ['accountid'] );
			unset ( $post_array ['logo_main'] );
			$this->db->update ( 'invoice_conf', $post_array );
		} else {
			unset ( $post_array ['logo_main'] );
			$logintype = $this->session->userdata ( 'logintype' );
			if ($logintype == 1 || $logintype == 5) {
				$accountdata = $this->session->userdata ( 'accountinfo' );
				$post_array ['accountid'] = $accountdata ['id'];
			}
			if ($post_array ['accountid'] == 0) {
				$post_array ['accountid'] = 1;
			}
			$this->db->insert ( 'invoice_conf', $post_array );
		}
		return true;
	}
}
