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
class Summary_model extends CI_Model {
	function Summary_model() {
		parent::__construct ();
	}
	function get_resellersummary_report_list($flag, $start = 0, $limit = 0, $group_by, $select, $order, $export = false) {
		$this->db_model->build_search ( 'summary_reseller_search' );
		$accountinfo = $this->session->userdata ( 'accountinfo' );
		$reseller_id = $accountinfo ['type'] == 1 ? $accountinfo ['id'] : 0;
		$where ['reseller_id'] = $reseller_id;
		if ($this->session->userdata ( 'advance_search' ) != 1) {
			$where ['callstart >='] = date ( 'Y-m-d' ) . " 00:00:00";
			$where ['callstart <='] = date ( 'Y-m-d' ) . " 23:59:59";
		}
		$this->db->where ( $where );
		if (! empty ( $group_by )) {
			$this->db->_protect_identifiers = false;
			$this->db->group_by ( $group_by, false );
			$this->db->_protect_identifiers = true;
		}
		if ($flag) {
			$this->db->select ( $select . ",COUNT(*) AS attempts, AVG(billseconds) AS acd,MAX(billseconds) AS mcd,SUM(billseconds) AS duration,SUM(CASE WHEN calltype !='free' THEN billseconds ELSE 0 END) as billable,SUM(CASE WHEN billseconds > 0 THEN 1 ELSE 0 END) as completed,SUM(debit) AS debit,SUM(cost) AS cost", false );
			$this->db->order_by ( $order, "ASC" );
			if (! $export && $limit > 0) {
				$this->db->limit ( $limit, $start );
			}
			$this->db->from ( "reseller_cdrs" );
			$result = $this->db->get ();
		} else {
			$result = $this->db_model->getSelect ( "count(*) as total_count", "reseller_cdrs", '' );
			if ($result->num_rows () > 0) {
				return $result->num_rows ();
			} else {
				return 0;
			}
		}
		return $result;
	}
	function get_providersummary_report_list($flag, $start = 0, $limit = 0, $group_by, $select, $order, $export = false) {
		$this->db_model->build_search ( 'summary_provider_search' );
		$where ['provider_id >'] = 0;
		if ($this->session->userdata ( 'advance_search' ) != 1) {
			$where ['callstart >='] = date ( 'Y-m-d' ) . " 00:00:00";
			$where ['callstart <='] = date ( 'Y-m-d' ) . " 23:59:59";
		}
		$this->db->where ( $where );
		if (! empty ( $group_by )) {
			$this->db->_protect_identifiers = false;
			$this->db->group_by ( $group_by, false );
			$this->db->_protect_identifiers = true;
		}
		if ($flag) {
			$this->db->select ( $select . ",COUNT(*) AS attempts, AVG(billseconds) AS acd,MAX(billseconds) AS mcd,SUM(billseconds) AS duration,SUM(CASE WHEN calltype !='free' THEN billseconds ELSE 0 END) as billable,SUM(CASE WHEN billseconds > 0 THEN 1 ELSE 0 END) as completed,SUM(cost) AS cost", "cdrs", false );
			$this->db->order_by ( $order, "ASC" );
			if (! $export && $limit > 0) {
				$this->db->limit ( $limit, $start );
			}
			$this->db->from ( 'cdrs' );
			$result = $this->db->get ();
		} else {
			$result = $this->db_model->getSelect ( "count(*) as total_count", "cdrs", '' );
			if ($result->num_rows () > 0) {
				return $result->num_rows ();
			} else {
				return 0;
			}
		}
		return $result;
	}
	function get_customersummary_report_list($flag, $start = 0, $limit = 0, $group_by, $select, $order, $export) {
		$this->db_model->build_search ( 'summary_customer_search' );
		$accountinfo = $this->session->userdata ( 'accountinfo' );
		$reseller_id = $accountinfo ['type'] == 1 ? $accountinfo ['id'] : 0;
		$where ['reseller_id'] = $reseller_id;
		if ($this->session->userdata ( 'advance_search' ) != 1) {
			$where ['callstart >='] = date ( 'Y-m-d' ) . " 00:00:00";
			$where ['callstart <='] = date ( 'Y-m-d' ) . " 23:59:59";
		}
		$this->db->where ( $where );
		$types = array (
				'0',
				'3' 
		);
		$this->db->where_in ( 'type', $types );
		if (! empty ( $group_by )) {
			$this->db->_protect_identifiers = false;
			$this->db->group_by ( $group_by, false );
			$this->db->_protect_identifiers = true;
		}
		if ($flag) {
			$this->db->select ( $select . ",COUNT(*) AS attempts, AVG(billseconds) AS acd,MAX(billseconds) AS mcd,SUM(billseconds) AS duration,SUM(CASE WHEN calltype !='free' THEN billseconds ELSE 0 END) as billable,SUM(CASE WHEN billseconds > 0 THEN 1 ELSE 0 END) as completed,SUM(debit) AS debit,SUM(cost) AS cost", false );
			$this->db->order_by ( $order, "ASC" );
			if (! $export && $limit > 0) {
				$this->db->limit ( $limit, $start );
			}
			$this->db->from ( "cdrs" );
			$result = $this->db->get ();
		} else {
			$result = $this->db_model->getSelect ( "count(*) as total_count", "cdrs", '' );
			if ($result->num_rows () > 0) {
				return $result->num_rows ();
			} else {
				return 0;
			}
		}
		 //echo $this->db->last_query();
		return $result;
	}
}
