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
class Opensips_model extends CI_Model {
	function Opensips_model() {
		parent::__construct ();
		$db_config = Common_model::$global_config ['system_config'];
		$opensipdsn = "mysqli://" . $db_config ['opensips_dbuser'] . ":" . $db_config ['opensips_dbpass'] . "@" . $db_config ['opensips_dbhost'] . "/" . $db_config ['opensips_dbname'] . "?char_set=utf8&dbcollat=utf8_general_ci&cache_on=true&cachedir=";
		$this->opensips_db = $this->load->database ( $opensipdsn, true );
	}
	function getopensipsdevice_list($flag, $start = 0, $limit = 0) {
		$this->db_model->build_search_opensips ( $this->opensips_db, 'opensipsdevice_list_search' );
		$accountinfo = $this->session->userdata ( 'accountinfo' );
		$reseller_id = $accountinfo ['type'] == 1 ? $accountinfo ['id'] : 0;
		$this->opensips_db->where ( 'reseller_id', $reseller_id );
		if ($flag) {
			$this->opensips_db->limit ( $limit, $start );
			if (isset ( $_GET ['sortname'] ) && $_GET ['sortname'] != 'undefined') {
				$this->opensips_db->order_by ( $_GET ['sortname'], ($_GET ['sortorder'] == 'undefined') ? 'desc' : $_GET ['sortorder'] );
			} else {
				$this->opensips_db->order_by ( 'username', 'asc' );
			}
			$query = $this->opensips_db->get ( "subscriber" );
		} else {
			$query = $this->opensips_db->get ( "subscriber" );
			$query = $query->num_rows ();
		}
		return $query;
	}
	function getopensipsdevice_customer_list($flag, $accountid = "", $accounttype, $start = "0", $limit = "0") {
		if ($accountid != "") {
			$where = array (
					"accountcode" => $this->common->get_field_name ( 'number', 'accounts', array (
							'id' => $accountid 
					) ) 
			);
		}
		$instant_search = $this->session->userdata ( 'left_panel_search_' . $accounttype . '_opensips' );
		$like_str = ! empty ( $instant_search ) ? "(username like '%$instant_search%'
                                            OR  password like '%$instant_search%'
                                            OR  domain like '%$instant_search%'
                                            OR  effective_caller_id_name like '%$instant_search%'
                                            OR  effective_caller_id_number like '%$instant_search%'
                                                )" : null;
		if (! empty ( $like_str ))
			$this->opensips_db->where ( $like_str );
		$this->opensips_db->where ( $where );
		if ($flag) {
			$this->opensips_db->limit ( $limit, $start );
		}
		$result = $this->opensips_db->get ( "subscriber" );
		if ($result->num_rows () > 0) {
			if ($flag) {
				return $result;
			} else {
				return $result->num_rows ();
			}
		} else {
			if ($flag) {
				$result = ( object ) array (
						'num_rows' => 0 
				);
			} else {
				$result = 0;
			}
			return $result;
		}
	}
	function getopensipsdispatcher_list($flag, $start = '', $limit = '') {
		$this->db_model->build_search_opensips ( $this->opensips_db, 'opensipsdispatcher_list_search' );
		if ($flag) {
			$this->opensips_db->limit ( $limit, $start );
			if (isset ( $_GET ['sortname'] ) && $_GET ['sortname'] != 'undefined') {
				$this->opensips_db->order_by ( $_GET ['sortname'], ($_GET ['sortorder'] == 'undefined') ? 'desc' : $_GET ['sortorder'] );
			} else {
				$this->opensips_db->order_by ( 'setid', 'asc' );
			}
			$query = $this->opensips_db->get ( "dispatcher" );
		} else {
			$query = $this->opensips_db->get ( "dispatcher" );
			$query = $query->num_rows ();
		}
		return $query;
	}
	function add_opensipsdevices($data) {
		unset ( $data ["action"] );
		$data ['creation_date'] = gmdate ( "Y-m-d H:i:s" );
		$accountinfo = $this->session->userdata ( 'accountinfo' );
		$data ['reseller_id'] = $accountinfo ['type'] == 1 ? $accountinfo ['id'] : 0;
		unset ( $data ["id"] );
		$this->opensips_db->insert ( "subscriber", $data );
	}
	function edit_opensipsdevices($data, $id) {
		unset ( $data ["action"] );
		$data = array (
				"username" => $data ['username'],
				"password" => $data ['password'],
				"accountcode" => $data ['accountcode'],
				"domain" => $data ['domain'],
				"effective_caller_id_name" => $data ['effective_caller_id_name'],
				"effective_caller_id_number" => $data ['effective_caller_id_number'],
				"status" => $data ['status'] 
		);
		$this->opensips_db->where ( "id", $id );
		$data ['last_modified_date'] = gmdate ( "Y-m-d H:i:s" );
		$this->opensips_db->update ( "subscriber", $data );
	}
	function delete_opensips_devices($id) {
		$this->opensips_db->where ( "id", $id );
		$this->opensips_db->delete ( "subscriber" );
		return true;
	}
	function remove_opensips($id) {
		$this->opensips_db->where ( "id", $id );
		$this->opensips_db->delete ( "subscriber" );
		return true;
	}
	function add_opensipsdispatcher($data) {
		unset ( $data ["action"] );
		$this->opensips_db->insert ( "dispatcher", $data );
	}
	function edit_opensipsdispatcher($data, $id) {
		unset ( $data ["action"] );
		$this->opensips_db->where ( "id", $id );
		$this->opensips_db->update ( "dispatcher", $data );
	}
	function remove_dispatcher($id) {
		$this->opensips_db->where ( "id", $id );
		$this->opensips_db->delete ( "dispatcher" );
		return true;
	}
	function build_search_opensips($accounts_list_search) {
		if ($this->session->userdata ( 'advance_search' ) == 1) {
			$account_search = $this->session->userdata ( $accounts_list_search );
			unset ( $account_search ["ajax_search"] );
			unset ( $account_search ["advance_search"] );
			foreach ( $account_search as $key => $value ) {
				if ($value != "") {
					if (is_array ( $value )) {
						if (array_key_exists ( $key . "-integer", $value )) {
							$this->get_interger_array ( $key, $value [$key . "-integer"], $value [$key] );
						}
						if (array_key_exists ( $key . "-string", $value )) {
							$this->get_string_array ( $key, $value [$key . "-string"], $value [$key] );
						}
					} else {
						$this->opensips_db->where ( $key, $value );
					}
				}
			}
		}
	}
	function get_interger_array($field, $value, $search_array) {
		if ($search_array != '') {
			switch ($value) {
				case "1" :
					$this->opensips_db->where ( $field, $search_array );
					break;
				case "2" :
					$this->opensips_db->where ( $field . ' <>', $search_array );
					break;
				case "3" :
					$this->opensips_db->where ( $field . ' > ', $search_array );
					break;
				case "4" :
					$this->opensips_db->where ( $field . ' < ', $search_array );
					break;
				case "5" :
					$this->opensips_db->where ( $field . ' >= ', $search_array );
					break;
				case "6" :
					$this->opensips_db->where ( $field . ' <= ', $search_array );
					break;
			}
		}
	}
	function get_string_array($field, $value, $search_array) {
		if ($search_array != '') {
			switch ($value) {
				case "1" :
					$this->opensips_db->like ( $field, $search_array );
					break;
				case "2" :
					$this->opensips_db->not_like ( $field, $search_array );
					break;
				case "3" :
					$this->opensips_db->where ( $field, $search_array );
					break;
				case "4" :
					$this->opensips_db->where ( $field . ' <>', $search_array );
					break;
			}
		}
	}
}
