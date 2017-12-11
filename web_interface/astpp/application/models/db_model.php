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
class Db_model extends CI_Model {
	function Db_model() {
		// parent::Model();
		parent::__construct ();
		$this->db->query ( "SET time_zone='+0:00'" );
	}

	/*
	 * ********************************************************
	 * Function getCriteria(Where=Condition in Array Format)
	 * ********************************************************
	 */
	function getCriteria($condition = "", $tableName) {
		// print_r($condition);
		if ($condition != "") {
			$this->db->where ( $condition );
		}
		return $this->db->get ( $tableName );
	}

	/*
	 * ********************************************************
	 * Function save() for addingthe record
	 * ********************************************************
	 */
	function save($tableName, $arr, $val = 'false') {
		$str = $this->db->insert_string ( $tableName, $arr );
		$rs = $this->db->query ( $str );
		if ($val == true)
			return $this->db->insert_id ();
		else
			return $rs;
	}

	/*
	 * ********************************************************
	 * Function update() for editing the record
	 * ********************************************************
	 */
	function update($tableName, $arr, $where) {
		$str = $this->db->update_string ( $tableName, $arr, $where );
		$rs = $this->db->query ( $str );
		return $rs;
	}

	/*
	 * ********************************************************
	 * Function getSelect()n for displaying record
	 * ********************************************************
	 */
	function getSelect($select, $tableName, $where) {
		$this->db->select ( $select, false );
		$this->db->from ( $tableName );
		if ($where != '') {
			$this->db->where ( $where );
		}
		$query = $this->db->get ();
		return $query;
	}

	/*
	 * ********************************************************
	 * Function getSelectWithOrder()n for displaying record
	 * ********************************************************
	 */
	function getSelectWithOrder($select, $tableName, $where, $order_type, $order_by) {
		$this->db->select ( $select );
		$this->db->from ( $tableName );
		$this->db->where ( $where );
		$this->db->order_by ( $order_by, $order_type );
		$query = $this->db->get ();
		return $query;
	}

	/*
	 * ********************************************************
	 * Function getSelectWithOrderAndLimit()n for displaying record
	 * ********************************************************
	 */
	function getSelectWithOrderAndLimit($select, $tableName, $where, $order_type, $order_by, $paging_limit) {
		$this->db->select ( $select );
		$this->db->from ( $tableName );
		$this->db->where ( $where );
		$this->db->order_by ( $order_by, $order_type );
		$this->db->limit ( $paging_limit );
		$query = $this->db->get ();
		return $query;
	}

	/*
	 * ********************************************************
	 * Function delete() for deletingthe record
	 * ********************************************************
	 */
	function delete($tableName, $where) {
		$this->db->where ( $where );
		$this->db->delete ( $tableName );
	}

	/*
	 * ********************************************************
	 * Function excecute() take compelet query
	 * ********************************************************
	 */
	function excecute($query) {
		$rs = $this->db->query ( $query );
		return $rs;
	}

	/*
	 * ********************************************************
	 * Function select() take full complete perms
	 * ********************************************************
	 */
	function select($select, $tableName, $where, $order_by, $order_type, $paging_limit = '', $start_limit = '', $groupby = '') {
		$this->db->select ( $select );
		$this->db->from ( $tableName );
		if ($where != "") {
			$this->db->where ( $where );
		}

		if ($paging_limit)
			$this->db->limit ( $paging_limit, $start_limit );
		if (! empty ( $groupby ))
			$this->db->group_by ( $groupby );
		if (isset ( $_GET ['sortname'] ) && $_GET ['sortname'] != 'undefined') {
			$this->db->order_by ( $_GET ['sortname'], ($_GET ['sortorder'] == 'undefined') ? 'desc' : $_GET ['sortorder'] );
		} else {
			if ($order_by)
				$this->db->order_by ( $order_by, $order_type );
		}
		$query = $this->db->get ();
		return $query;
	}

	/*
	 * ********************************************************
	 * Function select for In query () take full complete perms
	 * ********************************************************
	 */
	function select_by_in($select, $tableName, $where, $order_by, $order_type, $paging_limit, $start_limit, $groupby = '', $key, $where_in) {
		$this->db->select ( $select );
		$this->db->from ( $tableName );
		if ($where != "") {
			$this->db->where ( $where );
		}
		$this->db->where_in ( $key, $where_in );
		$this->db->order_by ( $order_by, $order_type );
		if ($paging_limit)
			$this->db->limit ( $paging_limit, $start_limit );
		if (! empty ( $groupby ))
			$this->db->groupby ( $groupby );
		$query = $this->db->get ();

		return $query;
	}

	/*
	 * ********************************************************
	 * Function countQuery() take table name and select field
	 * ********************************************************
	 */
	function countQuery($select, $table, $where = "") {
		$this->db->select ( $select );
		if ($where != "") {
			$this->db->where ( $where );
		}
		$this->db->from ( $table );
		$query = $this->db->get ();
		return $query->num_rows ();
	}

	/*
	 * ********************************************************
	 * Function countQuery for where in query() take table name and select field
	 * ********************************************************
	 */
	function countQuery_by_in($select, $table, $where = "", $key, $where_in) {
		$this->db->select ( $select );
		if ($where != "") {
			$this->db->where ( $where );
		}
		if (! empty ( $where_in )) {
			$this->db->where_in ( $key, $where_in );
		}
		$this->db->from ( $table );
		$query = $this->db->get ();
		return $query->num_rows ();
	}

	/*
	 * ********************************************************
	 * Function maxQuery() take table name and select field
	 * ********************************************************
	 */
	function maxQuery($table, $select, $where = "", $name) {
		$this->db->select ( $select );
		$this->db->from ( $table );
		if ($where != "") {
			$this->db->where ( $where );
		}
		$query = $this->db->get ();
		if ($query->num_rows () > 0) {
			$rowP = $query->row ();
			return $rowP->{$name};
		} else {
			return 0;
		}
	}

	/*
	 * ********************************************************
	 * Function getCurrent get current value of the field
	 * ********************************************************
	 */
	function getCurrent($table, $field, $where) {
		// echo "<pre>table====><br>".$table."field====><br>".$field."where====><br>".print_r($where);
		$this->db->select ( $field );
		$this->db->from ( $table );
		$this->db->where ( $where );
		$query = $this->db->get ();
		if ($query->num_rows () > 0) {

			$rowP = $query->row ();
			return $rowP->{$field};
		} else {
			return false;
		}
	}

	/*
	 * ********************************************************
	 * Function getJionQuery get result set on criteria
	 * ********************************************************
	 */
	function getJionQuery($table, $field, $where = "", $jionTable, $jionCondition, $type = 'inner', $start = '', $end = '', $order_type = '', $order_by = '', $group_by = '') {
		$start = ( int ) $start;
		$end = ( int ) $end;
		$this->db->select ( $field );
		$this->db->from ( $table );
		$this->db->join ( $jionTable, $jionCondition, $type );
		if ($where != "") {
			$this->db->where ( $where );
		}
		if (isset ( $_GET ['sortname'] ) && $_GET ['sortname'] != 'undefined') {
			$this->db->order_by ( $_GET ['sortname'], ($_GET ['sortorder'] == 'undefined') ? 'desc' : $_GET ['sortorder'] );
		} else {
			if ($order_by)
				$this->db->order_by ( $order_by, $order_type );
		}

		if ($group_by != '') {
			$this->db->group_by ( $group_by );
		}

		$this->db->limit ( $start, $end );

		return $query = $this->db->get ();
	}
	function getJionQueryCount($table, $field, $where = "", $jionTable, $jionCondition, $type = 'inner', $start = '', $end = '', $order_type = '', $order_by = '', $group_by = '') {
		$start = ( int ) $start;
		$end = ( int ) $end;
		$this->db->select ( $field );
		$this->db->from ( $table );
		$this->db->join ( $jionTable, $jionCondition, $type );
		if ($where != "") {
			$this->db->where ( $where );
		}

		if ($order_type != '' && $order_by != '') {
			$this->db->orderby ( $order_type, $order_by );
		}

		if ($group_by != '') {
			$this->db->group_by ( $group_by );
		}

		$query = $this->db->get ();
		return $query->num_rows ();
	}
	function getAllJionQuery($table, $field, $where = "", $jionTable, $jionCondition, $type, $start = '', $end = '', $order_type = '', $order_by = '', $group_by = '') {
		$start = ( int ) $start;
		$end = ( int ) $end;
		$this->db->select ( $field );
		$this->db->from ( $table );
		$jion_table_count = count ( $jionTable );
		for($i = 0; $i < $jion_table_count; $i ++) {
			$this->db->join ( $jionTable [$i], $jionCondition [$i], $type [$i] );
		}

		if ($where != "") {
			$this->db->where ( $where );
		}
		if (isset ( $_GET ['sortname'] ) && $_GET ['sortname'] != 'undefined') {
			$this->db->order_by ( $_GET ['sortname'], ($_GET ['sortorder'] == 'undefined') ? 'desc' : $_GET ['sortorder'] );
		} else {
			if ($order_by)
				$this->db->order_by ( $order_by, $order_type );
		}

		if ($group_by != '') {
			$this->db->group_by ( $group_by );
		}

		if ($start != '' && $end != '') {
			$this->db->limit ( $start, $end );
		}

		if ($start != '' && $end == '') {
			$this->db->limit ( $start );
		}

		return $query = $this->db->get ();
	}
	function getCountWithJion($table, $field, $where = "", $jionTable, $jionCondition, $type, $group_by = '') {
		$this->db->select ( $field );
		$this->db->from ( $table );
		$jion_table_count = count ( $jionTable );
		for($i = 0; $i < $jion_table_count; $i ++) {
			$this->db->join ( $jionTable [$i], $jionCondition [$i], $type [$i] );
		}

		if ($where != "") {
			$this->db->where ( $where );
		}
		if ($group_by != '') {
			$this->db->group_by ( $group_by );
		}
		$query = $this->db->get ();
		if ($query->num_rows () > 0) {
			return $query->num_rows ();
		} else {
			return false;
		}
	}

	/*
	 * ********************************************************
	 * Function getCurrentWithOrder
	 * ********************************************************
	 */
	function getCurrentWithOrder($table, $field, $where, $order, $order_by, $limit, $option) {
		$this->db->select ( $field );
		$this->db->from ( $table );
		$this->db->where ( $where );
		$this->db->order_by ( $order, $order_by );
		if ($limit != 0) {
			$this->db->limit ( $limit );
		}
		$query = $this->db->get ();
		if ($query->num_rows () > 0) {
			$rowP = $query->row ();
			if ($option == 'yes') {
				return $rowP->{$field};
			} else {
				return $query;
			}
		} else {
			if ($option == 'no') {
				return $query;
			} else {
				return false;
			}
		}
	}

	/*
	 * ********************************************************
	 * Function getReferPatients
	 * ********************************************************
	 */
	function getAllWithOrder($table, $field, $where) {
		$this->db->select ( $field );
		$this->db->from ( $table );
		$this->db->where ( $where );
		$query = $this->db->get ();
		if ($query->num_rows () > 0) {
			$rowP = $query->row ();
			return $rowP->{$field};
		} else {
			return false;
		}
	}
	function getCommaSperated($table, $select, $where, $limit, $return_message = FALSE, $message = '') {
		if ($table != '') {
			$this->db->select ( $select );
			$this->db->from ( $table );
			$this->db->where ( $where );
			if ($limit != 0) {
				$this->db->limit ( $limit );
			}
			$query = $this->db->get ();
			$string = '';
			if ($query->num_rows () > 0) {
				foreach ( $query->result () as $rows ) {
					$string .= $rows->{$select} . ',';
				}

				return substr ( $string, '', - 1 );
			} else {
				if ($return_message == FALSE) {
					return '';
				} else {
					return $message;
				}
			}
		} else {
			return '';
		}
	}
	function build_concat_dropdown($select, $table, $id_where = '', $id_value = '') {
		$select_params = explode ( ',', $select );
		if (isset ( $select_params [3] )) {
			$cnt_str = " $select_params[1],' ',$select_params[2],' ','(',$select_params[3],')' ";
		} else {
			$cnt_str = " $select_params[1],' (',$select_params[2],')' ";
		}
		$select = $select_params [0] . ", concat($cnt_str) as $select_params[1] ";
		$logintype = $this->session->userdata ( 'logintype' );
		if (($logintype == 1 || $logintype == 5) && $id_where == 'where_arr') {
			$account_data = $this->session->userdata ( "accountinfo" );
			$id_value ['reseller_id'] = $account_data ['id'];
		}
		if (isset ( $id_value ['type'] ) && $id_value ['type'] == '0,3') {
			$twhere = "type IN (" . $id_value ["type"] . ")";
			$this->db->where ( $twhere );
			unset ( $id_value ['type'] );
		}
		$where = $id_value;
		$drp_array = $this->getSelect ( $select, $table, $where );
		$drp_array = $drp_array->result ();

		$drp_list = array ();
		foreach ( $drp_array as $drp_value ) {
			$drp_list [$drp_value->{$select_params [0]}] = $drp_value->{$select_params [1]};
		}
		return $drp_list;
	}
	/**
	 * ****
	 * ASTPP 3.0
	 * Recording enable/disable dropdown
	 * ***
	 */
	function build_concat_dropdown_refill_coupon($select, $table, $id_where = '', $id_value = '') {
		$select_params = explode ( ',', $select );
		$account_data = $this->session->userdata ( "accountinfo" );
		if (isset ( $select_params [3] )) {
			$cnt_str = " $select_params[1],' ',$select_params[2],' ','(',$select_params[3],')' ";
		} else {
			$cnt_str = " $select_params[1],' (',$select_params[2],')' ";
		}
		$select = $select_params [0] . ", concat($cnt_str) as $select_params[1] ";
		$logintype = $this->session->userdata ( 'logintype' );
		if ($account_data ['type'] == 1 && $id_where == 'where_arr') {
			$id_value ['reseller_id'] = $account_data ['id'];
		} else {
			$this->db->or_where ( 'type', 3 );
		}
		$where = $id_value;
		$this->db->where ( $where );
		$drp_array = $this->getSelect ( $select, $table, '' );
		$drp_array = $drp_array->result ();
		$drp_list = array ();
		// $drp_list[0] = 'Admin';
		foreach ( $drp_array as $drp_value ) {
			$drp_list [$drp_value->{$select_params [0]}] = $drp_value->{$select_params [1]};
		}
		return $drp_list;
	}
	/**
	 * ********************************
	 */
	function build_concat_select_dropdown($select, $table, $id_where = '', $id_value = '') {
		$select_params = explode ( ',', $select );
		if (isset ( $select_params [3] )) {
			$cnt_str = " $select_params[1],' ',$select_params[2],' ','(',$select_params[3],')' ";
		} else {
			$cnt_str = " $select_params[1],' (',$select_params[2],')' ";
		}
		$select = $select_params [0] . ", concat($cnt_str) as $select_params[1] ";
		$where = $id_value;
		$drp_array = $this->getSelect ( $select, $table, $id_value );
		$drp_array = $drp_array->result ();

		$drp_list = array ();
		$drp_list [0] = "--Select--";
		foreach ( $drp_array as $drp_value ) {
			$drp_list [$drp_value->{$select_params [0]}] = $drp_value->{$select_params [1]};
		}
		return $drp_list;
	}
	function build_dropdown($select, $table, $id_where = '', $id_value = '') {
		$select_params = explode ( ',', $select );
		$where = '';
		if (isset ( $id_value ["type"] ) && $id_value ["type"] == "GLOBAL") {
			$where = "type IN ('0','3')";
			$this->db->where ( $where );
			unset ( $id_value ["type"] );
		}
		if ($id_where != '' && $id_value != '') {
			if ($id_where == 'group_by') {
				$this->db->group_by ( $id_value );
			} else if ($id_where == "where_arr") {
				$logintype = $this->session->userdata ( 'logintype' );
				if (($logintype == 1 || $logintype == 5) && $id_where == 'where_arr' && $this->db->field_exists ( 'reseller_id', $table )) {
					$id_value ['reseller_id'] = $this->session->userdata ["accountinfo"] ['id'];
				}
				$where = $id_value;
			} else {
				$logintype = $this->session->userdata ( 'logintype' );
				if (($logintype == 1 || $logintype == 5) && $id_where == 'reseller_id') {
					$account_data = $this->session->userdata ( "accountinfo" );
					$id_value = $account_data ['id'];
				}
				$where = array (
						$id_where => $id_value
				);
			}
		}

		$drp_array = $this->getSelect ( $select, $table, $where );
		$drp_array = $drp_array->result ();

		$drp_list = array ();
		foreach ( $drp_array as $drp_value ) {
			$drp_list [$drp_value->{$select_params [0]}] = $drp_value->{$select_params [1]};
		}
		return $drp_list;
	}
	function build_dropdown_deleted($select, $table, $id_where = '', $id_value = '') {
		$select_params = explode ( ',', $select );
		if (isset ( $id_value ["type"] )) {
			$where = $id_value ["type"] == "GLOBAL" ? "type IN ('0','3')" : "type IN (" . $id_value ["type"] . ")";
			$this->db->where ( $where );
			unset ( $id_value ["type"] );
		}
		$where = '';
		if ($id_where != '' && $id_value != '') {
			if ($id_where == 'group_by') {
				$this->db->group_by ( $id_value );
			} else if ($id_where == "where_arr") {
				$logintype = $this->session->userdata ( 'logintype' );
				if (($logintype == 1 || $logintype == 5) && $id_where == 'where_arr') {
					$account_data = $this->session->userdata ( "accountinfo" );
					$id_value ['reseller_id'] = $account_data ['id'];
				}
				$where = $id_value;
			} else {
				$logintype = $this->session->userdata ( 'logintype' );
				if (($logintype == 1 || $logintype == 5) && $id_where == 'reseller_id') {
					$account_data = $this->session->userdata ( "accountinfo" );
					$id_value = $account_data ['id'];
				}
				$where = array (
						$id_where => $id_value
				);
			}
		}

		$drp_array = $this->getSelect ( $select, $table, $where );

		$drp_array = $drp_array->result ();

		$name = explode ( "as", $select );
		if (isset ( $name [3] )) {
			$name = trim ( $name [3] );
		} else {
			$name = trim ( $name [1] );
		}

		$drp_list = array ();
		$dele = array ();
		foreach ( $drp_array as $drp_value ) {
			$dele = explode ( "^", $drp_value->{$name} );
			if (isset ( $dele [1] )) {
				$drp_list ['Deleted'] [$drp_value->{$select_params [0]}] = str_replace ( "^", "", $drp_value->{$name} );
			} else {
				$drp_list ['Active'] [$drp_value->{$select_params [0]}] = $drp_value->{$name};
			}
		}
		ksort ( $drp_list );
		return $drp_list;
	}
	function build_search($accounts_list_search) {
		if ($this->session->userdata ( 'advance_search' ) == 1) {
			$account_search = $this->session->userdata ( $accounts_list_search );
			unset ( $account_search ["ajax_search"] );
			unset ( $account_search ["advance_search"] );
			/*
			 * ASTPP 3.0
			 * Display Records in
			 */
			unset ( $account_search ['search_in'], $account_search ['time'] );
			if (! empty ( $account_search )) {
				foreach ( $account_search as $key => $value ) {
					if ($value != "") {
						if (is_array ( $value )) {
							if (array_key_exists ( $key . "-integer", $value )) {
								$this->get_interger_array ( $key, $value [$key . "-integer"], $value [$key] );
							}
							if (array_key_exists ( $key . "-string", $value )) {
								$this->get_string_array ( $key, $value [$key . "-string"], $value [$key] );
							}

							/**
							 * ASTPP 3.0
							 * first used,creation,expiry search date picker
							 */
							if ($key == 'callstart' || $key == 'date' || $key == 'payment_date' || $key == 'first_used' || $key == 'creation' || $key == 'from_date' || $key == 'invoice_date' || $key == 'expiry' || $key == 'created_date' || $key == 'to_date') {
								/**
								 * ********************************************
								 */
								$this->get_date_array ( $key, $value );
							}
						} else {
							//Getting disposition with Q.850 code
							if($key == 'disposition'){
                                                                $str1 = $key . " LIKE '%$value%'";
                                                                $this->db->where ( $str1 );
                                                        }else{
								$this->db->where ( $key, $value );
							}
						}
					}
				}
				return true;
			}
		}
	}
	function get_date_array($field, $value) {
		if ($value != '') {
			if (! empty ( $value [0] )) {
				if ($field == 'invoice_date') {
					$this->db->where ( $field . ' >= ', gmdate ( "Y-m-d", strtotime ( $value ['0'] ) ) . " 00:00:01" );
					$this->db->where ( $field . ' <= ', gmdate ( "Y-m-d", strtotime ( $value ['0'] ) ) . " 23:59:59" );
					// ITPLATP 22_05_2017
				} else if ($field == 'to_date') {
					$this->db->where ( $field . ' <= ', gmdate ( "Y-m-d", strtotime ( $value ['0'] ) ) . " 23:59:59" );
					// end
				} else {
					$this->db->where ( $field . ' >= ', gmdate ( 'Y-m-d H:i:s', strtotime ( $value [0] ) ) );
				}
			}
			if (! empty ( $value [1] )) {
				$this->db->where ( $field . ' <= ', gmdate ( 'Y-m-d H:i:s', strtotime ( $value [1] ) ) );
			}
		}
	}
	function get_interger_array($field, $value, $search_array) {
		if ($search_array != '') {
			switch ($value) {
				case "1" :
					$this->db->where ( $field, $search_array );
					break;
				case "2" :
					$this->db->where ( $field . ' <>', $search_array );
					break;
				case "3" :
					$this->db->where ( $field . ' > ', $search_array );
					break;
				case "4" :
					$this->db->where ( $field . ' < ', $search_array );
					break;
				case "5" :
					$this->db->where ( $field . ' >= ', $search_array );
					break;
				case "6" :
					$this->db->where ( $field . ' <= ', $search_array );
					break;
			}
		}
	}
	function get_string_array($field, $value, $search_array) {
		if ($search_array != '') {
			switch ($value) {
				case "1" :
					$str1 = $field . " LIKE '%$search_array%'";
					$this->db->where ( $str1 );
					break;
				case "2" :
					$str1 = $field . " NOT LIKE '%$search_array%'";
					$this->db->where ( $str1 );
					break;
				case "3" :
					$this->db->where ( $field, $search_array );
					break;
				case "4" :
					$this->db->where ( $field . ' <>', $search_array );
					break;
				case "5" :
					if ($field == "pattern") {
						$str1 = $field . " LIKE '^" . $search_array . "%'";
						$this->db->where ( $str1 );
					} else {
						$str1 = $field . " LIKE '" . $search_array . "%'";
						$this->db->where ( $str1 );
					}

					break;
				case "6" :
					if ($field == "pattern") {
						$str1 = $field . " LIKE '%" . $search_array . ".*'";
						$this->db->where ( $str1 );
					} else {
						$str1 = $field . " LIKE '%" . $search_array . "'";
						$this->db->where ( $str1 );
					}

					break;
			}
		}
	}
	function build_search_string($accounts_list_search) {
		$where = null;
		$search = $this->session->userdata ( $accounts_list_search );
		if ($this->session->userdata ( 'advance_search' ) == 1) {
			$account_search = $this->session->userdata ( $accounts_list_search );
			unset ( $account_search ["ajax_search"] );
			unset ( $account_search ["advance_search"] );
			if (! empty ( $account_search )) {
				foreach ( $account_search as $key => $value ) {
					if ($value != "") {
						if (is_array ( $value )) {
							if (array_key_exists ( $key . "-integer", $value )) {
								$string = null;
								$string = $this->build_interger_where ( $key, $value [$key . "-integer"], $value [$key] );
								if ($string)
									$where .= "$string AND ";
							}
							if (array_key_exists ( $key . "-string", $value )) {
								$string = null;
								$string = $this->build_string_where ( $key, $value [$key . "-string"], $value [$key] );
								if ($string)
									$where .= "$string AND ";
							}
							if ($key == 'callstart' || $key == 'date' || $key == 'log_time') {
								$string = null;
								$string = $this->build_date_where ( $key, $value );
								if ($string)
									$where .= "$string AND ";
							}
						} else {
							$where .= "$key = '$value'AND ";
						}
					}
				}
			}
		}
		$where = rtrim ( $where, " AND " );
		return $where;
	}
	// This function using by reports module don't delete it
	function build_string_where($field, $value, $search_array) {
		$where = null;
		if ($search_array != '') {
			switch ($value) {
				case "1" :
					$where = "$field LIKE '%$search_array%'";
					break;
				case "2" :
					$where = "$field NOT LIKE '%$search_array%'";
					break;
				case "3" :
					$where = "$field = '$search_array'";
					break;
				case "4" :
					$where = "$field <> '$search_array'";
					break;
				case "5" :
					if ($field == "pattern") {
						$where = $field . " LIKE '^" . $search_array . "%'";
					} else {
						$where = $field . " LIKE '" . $search_array . "%'";
					}
					break;
				case "6" :
					if ($field == "pattern") {
						$str1 = $field . " LIKE '%" . $search_array . ".*'";
					} else {
						$str1 = $field . " LIKE '%" . $search_array . "'";
					}
					break;
			}
		}
		return $where;
	}
	function build_interger_where($field, $value, $search_array) {
		$where = null;
		if ($search_array != '') {
			if (is_numeric ( $search_array )) {
				switch ($value) {
					case "1" :
						$where = "$field = '$search_array'";
						break;
					case "2" :
						$where = "$field <> '$search_array'";
						break;
					case "3" :
						$where = "$field > '$search_array'";
						break;
					case "4" :
						$where = "$field < '$search_array'";
						break;
					case "5" :
						$where = "$field >= '$search_array'";
						break;
					case "6" :
						$where = "$field <= '$search_array'";
						break;
				}
			} else {
				$this->db->where ( "$field IS NULL" );
				$where = "$field IS NULL";
			}
		}
		return $where;
	}
	function build_date_where($field, $value) {
		$where = null;
		if ($value != '') {
			if (! empty ( $value [0] )) {
				$string = null;
				$string = "$field >= '$value[0]'";
				if ($string)
					$where .= $string . " AND ";
			}
			if (! empty ( $value [1] )) {
				$string = null;
				$string = "$field <= '$value[1]'";
				if ($string)
					$where .= $string . " AND ";
			}
		}
		if ($where) {
			$where = rtrim ( $where, " AND " );
		}
		return $where;
	}
	function get_available_bal($account_info) {
		$available_bal = 0;
		$available_bal = ($account_info ["posttoexternal"] == 1) ? ($account_info ["credit_limit"] - $account_info ["balance"]) : ($account_info ["balance"]);

		return $available_bal;
	}
	function update_balance($amount, $accountid, $payment_type) {
		if ($payment_type == "debit" || $payment_type == "0") {

			$query = "update accounts set balance =  IF(posttoexternal=1,balance+" . $amount . ",balance-" . $amount . ") where id ='" . $accountid . "'";

			return $this->db->query ( $query );
		} else {
			$query = "update accounts set balance =  IF(posttoexternal=1,balance-" . $amount . ",balance+" . $amount . ") where id ='" . $accountid . "'";

			return $this->db->query ( $query );
		}
	}
	function build_batch_update_array($update_array) {
		$updateflg = false;
		foreach ( $update_array as $key => $update_fields ) {
			if (is_array ( $update_fields )) {
				switch ($update_fields ["operator"]) {
					case "1" :
						// $this->db->where($field, $search_array);
						break;
					case "2" :
						if ($update_fields [$key] != '') {
							$updateflg = true;
							$this->db->set ( $key, $update_fields [$key] );
						}
						break;
					case "3" :
						$this->db->set ( $key, $key . "+" . $update_fields [$key], FALSE );
						$updateflg = true;
						break;
					case "4" :
						$this->db->set ( $key, $key . "-" . $update_fields [$key], FALSE );
						$updateflg = true;
						break;
				}
			} else {
				if ($update_fields != "") {
					$this->db->set ( $key, $update_fields );
					$updateflg = true;
				}
			}
		}
		return $updateflg;
	}
	function build_search_opensips($opensips_db_obj, $accounts_list_search) {
		if ($this->session->userdata ( 'advance_search' ) == 1) {
			$account_search = $this->session->userdata ( $accounts_list_search );
			unset ( $account_search ["ajax_search"] );
			unset ( $account_search ["advance_search"] );
			foreach ( $account_search as $key => $value ) {
				if ($value != "") {
					if (is_array ( $value )) {
						if (array_key_exists ( $key . "-integer", $value )) {
							$this->get_opensips_interger_array ( $opensips_db_obj, $key, $value [$key . "-integer"], $value [$key] );
						}
						if (array_key_exists ( $key . "-string", $value )) {
							$this->get_opensips_string_array ( $opensips_db_obj, $key, $value [$key . "-string"], $value [$key] );
						}
					} else {
						$opensips_db_obj->where ( $key, $value );
					}
				}
			}
		}
	}
	function get_opensips_interger_array($opensips_db_obj, $field, $value, $search_array) {
		if ($search_array != '') {
			switch ($value) {
				case "1" :
					$opensips_db_obj->where ( $field, $search_array );
					break;
				case "2" :
					$opensips_db_obj->where ( $field . ' <>', $search_array );
					break;
				case "3" :
					$opensips_db_obj->where ( $field . ' > ', $search_array );
					break;
				case "4" :
					$opensips_db_obj->where ( $field . ' < ', $search_array );
					break;
				case "5" :
					$opensips_db_obj->where ( $field . ' >= ', $search_array );
					break;
				case "6" :
					$opensips_db_obj->where ( $field . ' <= ', $search_array );
					break;
			}
		}
	}
	function get_opensips_string_array($opensips_db_obj, $field, $value, $search_array) {
		if ($search_array != '') {
			switch ($value) {
				case "1" :
					$opensips_db_obj->like ( $field, $search_array );
					break;
				case "2" :
					$opensips_db_obj->not_like ( $field, $search_array );
					break;
				case "3" :
					$opensips_db_obj->where ( $field, $search_array );
					break;
				case "4" :
					$opensips_db_obj->where ( $field . ' <>', $search_array );
					break;
				case "5" :
					$str1 = $field . " LIKE '" . $search_array . "%'";
					$opensips_db_obj->where ( $str1 );
					break;
				case "6" :
					$str1 = $field . " LIKE '%" . $search_array . "'";
					$opensips_db_obj->where ( $str1 );
					break;
			}
		}
	}
	/**
	 * ******invoice changes ********
	 */
	function build_dropdown_invoices($select, $table, $id_where = '', $id_value = '') {
		$select_params = explode ( ',', $select );
		$select_params = explode ( ',', $select );
		if (isset ( $select_params [3] )) {
			$cnt_str = " $select_params[1],' ',$select_params[2],' ','(',$select_params[3],')' ";
		} else {
			$cnt_str = " $select_params[1],' (',$select_params[2],')' ";
		}
		$select = $select_params [0] . ", concat($cnt_str) as $select_params[1] ," . $select_params [4];
		$logintype = $this->session->userdata ( 'logintype' );
		if (($logintype == 1 || $logintype == 5) && $id_where == 'where_arr') {
			$account_data = $this->session->userdata ( "accountinfo" );
			$id_value ['reseller_id'] = $account_data ['id'];
		}
		$where = $id_value;
		$drp_array = $this->getSelect ( $select, $table, $where );
		$drp_array = $drp_array->result ();
		$drp_list = array ();
		foreach ( $drp_array as $drp_value ) {
			if ($drp_value->type == 3) {
				$drp_list ['Provider'] [$drp_value->id] = $drp_value->first_name;
			} elseif ($drp_value->type == 1) {
				$drp_list ['Reseller'] [$drp_value->id] = $drp_value->first_name;
			} else {
				$drp_list ['Customer'] [$drp_value->id] = $drp_value->first_name;
			}
		}
		ksort ( $drp_list );
		return $drp_list;
	}
}

?>
