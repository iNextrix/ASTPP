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
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );

/**
 * Dynamically build forms for display
 */
class common {
	protected $CI; // codeigniter
	function __construct($library_name = '') {
		$this->CI = & get_instance ();
		$this->CI->load->library ( "timezone" );
		$this->CI->load->model ( 'db_model' );
		$this->CI->load->library ( 'email' );
		$this->CI->load->library ( 'session' );
	}

	// __construct
	/**
	 * adds raw html to the field array
	 */
	function generate_password() {
		$length = common_model::$global_config ['system_config'] ['pinlength'];
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$^&{}~,.*()_-=+<>[]|?";
		$pass = str_shuffle ( $chars );
		$pass = substr ( base64_encode ( sha1 ( $pass . $chars, true ) ), 0, $length );
		for($i = 0; $i < $length; $i ++) {
			$pass .= $chars {mt_rand ( 0, strlen ( $chars ) - 1 )};
		}
		return $pass;
	}
	function find_uniq_rendno($size = '', $field = '', $tablename = '') {
		if ($tablename != '') {
			$accounttype_array = array ();
			$uname = rand ( pow ( 10, $size - 1 ), pow ( 10, $size ) - 1 );
			$where = array (
					$field => $uname
			);
			$acc_result = $this->CI->db_model->getSelect ( 'Count(*) as count', $tablename, $where );
			$acc_result = $acc_result->result ();
			while ( $acc_result [0]->count != 0 ) {
				$uname = rand ( pow ( 10, $size - 1 ), pow ( 10, $size ) - 1 );
				$acc_result = $this->CI->db_model->getSelect ( 'Count(*) as count', $tablename, $where );
			}
		} else {
			$uname = rand ( pow ( 10, $size - 1 ), pow ( 10, $size ) - 1 );
		}
		return $uname;
	}
	function find_uniq_rendno_customer($size = '', $field = '', $tablename = '') {
		if ($tablename != '') {
			$accounttype_array = array ();
			$uname = rand ( pow ( 10, $size - 1 ), pow ( 10, $size ) - 1 );
			$where = array (
					$field => $uname
			);
			$acc_result = $this->CI->db_model->getSelect ( 'Count(*) as count', $tablename, $where );
			$acc_result = $acc_result->result ();
			while ( $acc_result [0]->count != 0 ) {
				$uname = rand ( pow ( 10, $size - 1 ), pow ( 10, $size ) - 1 );
				$acc_result = $this->CI->db_model->getSelect ( 'Count(*) as count', $tablename, $where );
			}
		} else {
			$uname = rand ( pow ( 10, $size - 1 ), pow ( 10, $size ) - 1 );
		}
		$start_prifix_value = common_model::$global_config ['system_config'] ['startingdigit'];
		if ($tablename == 'accounts' && $start_prifix_value != 0) {
			$length = strlen ( $start_prifix_value );
			$uname = substr ( $uname, $length );
			$uname = $start_prifix_value . $uname;
		}
		return $uname;
	}

	/**
	 *
	 * @param string $length
	 */
	function random_string($length) {
		$chars = "1234567890"; // length:36
		$final_rand = '';
		for($i = 0; $i < $length; $i ++) {
			$final_rand .= $chars [rand ( 0, strlen ( $chars ) - 1 )];
		}
		return $final_rand;
	}
	function find_uniq_rendno_accno($length = '', $field = '', $tablename = '', $default, $creation_count) {
		$number = array ();
		$j = 0;

		$total_count = pow ( 10, $length );
		for($i = 1; $i <= $total_count; $i ++) {

			$flag = false;
			$uname = $this->random_string ( $length );
			$uname = strtolower ( $uname );
			if (isset ( $default ))
				$uname = $default . $uname;
			if (! in_array ( $uname, $number )) {
				$where = array (
						$field => $uname
				);
				$acc_result = $this->CI->db_model->getSelect ( 'Count(id) as count', $tablename, $where );
				$acc_result = $acc_result->result_array ();
				if ($acc_result [0] ['count'] == 0 && ! in_array ( $uname, $number )) {
					$number [] = $uname;
					$j ++;
				}
				if ($j == $creation_count) {
					break;
				}
			} else {
				$total_count ++;
			}
		}
		return $number;
	}

	/**
	 *
	 * @param string $select
	 * @param string $table
	 */
	function get_field_count($select, $table, $where) {
		// echo $select."=====".$table."===".$where;
		if (is_array ( $where )) {
			$where = $where;
		} else {
			$where = array (
					$select => $where
			);
		}
		$field_name = $this->CI->db_model->countQuery ( $select, $table, $where );
		if (isset ( $field_name ) && ! empty ( $field_name )) {
			return $field_name;
		} else {
			return "0";
		}
	}

	/**
	 *
	 * @param string $select
	 * @param string $table
	 */
	function get_field_name($select, $table, $where) {
		if (is_array ( $where )) {
			$where = $where;
		} else {
			$where = array (
					"id" => $where
			);
		}
		$field_name = $this->CI->db_model->getSelect ( $select, $table, $where );
		$field_name = $field_name->result ();
		if (isset ( $field_name ) && ! empty ( $field_name )) {
			return $field_name [0]->{$select};
		} else {
			return "";
		}
	}

	/**
	 *
	 * @param string $select
	 * @param string $table
	 */
	function get_field_name_coma_new($select, $table, $where) {
		$value = '';
		if (is_array ( $where )) {
			$where = $where;
		} else {
			$where = explode ( ',', $where );
		}
		$select1 = explode ( ',', $select );
		for($i = 0; $i < count ( $where ); $i ++) {
			$where_in = array (
					"id" => $where [$i]
			);

			$field_name = $this->CI->db_model->getSelect ( $select, $table, $where_in );
			$field_name = $field_name->result ();
			if (isset ( $field_name ) && ! empty ( $field_name )) {
				foreach ( $select1 as $sel ) {
					if ($sel == 'number') {
						$value .= "(" . $field_name [0]->{$sel} . ")";
					} else {
						$value .= $field_name [0]->{$sel} . " ";
					}
				}
			} else {
				$value = "";
			}
		}
		return rtrim ( $value, ',' );
	}
	function check_did_avl($select, $table, $where) {
		$accountinfo = $this->CI->session->userdata ( 'accountinfo' );
		$flag_status = "";
		$where = array (
				"number" => $where
		);
		$field_name = $this->CI->db_model->getSelect ( "id,accountid,parent_id", 'dids', $where );
		$field_name = $field_name->result ();
		if (isset ( $field_name ) && ! empty ( $field_name )) {
			if (isset ( $field_name [0] ) && $accountinfo ['type'] != 1) {
				if ($field_name [0]->accountid != 0 && $field_name [0]->parent_id == 0) {
					$flag_status = "<a href='../did_list_release/" . $field_name [0]->id . "' title='Release' onClick='return get_reliase_msg();'><span class=' label label-sm label-inverse_blue arrowed_blue-in' title='release'>Release(C)<span></a>";
				} else if ($field_name [0]->parent_id != 0) {
					$flag_status = "<a href='../did_list_release/" . $field_name [0]->id . "' title='Release' onClick='return get_reliase_msg();'><span class=' label label-sm label-inverse_blue arrowed_blue-in' title='release'>Release(R)</span></a>";
				} else {
					$flag_status = "<span class=' label label-sm label-inverse arrowed-in' title='Not in use'>Not in use</span>";
				}
			} else {
				$reseller_id = $accountinfo ['type'] != 1 ? 0 : $accountinfo ['id'];
				$where = array (
						"note" => $field_name [0]->number,
						'parent_id' => $reseller_id
				);
				$field_name_re = $this->CI->db_model->getSelect ( "reseller_id", 'reseller_pricing', $where );
				$field_name_re = $field_name_re->result ();

				if (isset ( $field_name_re ) && ! empty ( $field_name_re )) {
					$flag_status = "<a href='../did_list_release/" . $field_name [0]->id . "' title='Release' onClick='return get_reliase_msg();'><span class=' label label-sm label-inverse_blue arrowed_blue-in' title='release'>Release(R)</span></a>";
				} else {
					$flag_status = "<span class=' label label-sm label-inverse arrowed-in' title='Not in use'>Not in use</span>";
				}
			}
		} else {
			$flag_status = "<span class=' label label-sm label-inverse arrowed-in' title='Not in use'>Not in use</span>";
		}
		return $flag_status;
	}
	function check_did_avl_export($number) {
		$this->CI->db->where ( 'number', $number );
		$this->CI->db->select ( 'id,accountid,parent_id' );
		$status = null;
		$did_info = ( array ) $this->CI->db->get ( 'dids' )->first_row ();
		$accountinfo = $this->CI->session->userdata ( 'accountinfo' );
		if ($did_info ['accountid'] == 0 && $did_info ['parent_id'] == 0) {
			$status = 'Not in use';
		} elseif ($accountinfo ['type'] != 1) {
			if ($did_info ['accountid'] == 0 && $did_info ['parent_id'] > 0) {
				$status = 'Purchase by Reseller';
			}
			if ($did_info ['accountid'] > 0 && $did_info ['parent_id'] == 0) {
				$status = 'Purchase by Customer';
			}
		} else {
			$where_arr = array (
					'note' => $did_info ['number'],
					"parent_id" => $accountinfo ['id']
			);
			$this->db->where ( $where );
			$this->CI->db->select ( 'reseller_id,parent_id' );
			$reseller_pricing = ( array ) $this->db->get ( 'reseller_pricing' )->first_row ();
			if ($reseller_pricing ['reseller_id'] == 0 && $did_info ['accountid'] == 0 && $did_info ['parent_id'] == $accountinfo ['id']) {
				$status = 'Not in use';
			}
			if ($reseller_pricing ['reseller_id'] == 0 && $did_info ['accountid'] == 0) {
				$status = 'Not in use';
			}
		}
		return $status;
	}
	function check_did_avl_reseller($select, $table, $where) {
		$accountinfo = $this->CI->session->userdata ( 'accountinfo' );
		$flag_status = "<span class=' label label-sm label-inverse arrowed-in' title='Not in use'>Not in use</span>";
		$this->CI->db->where ( 'number', $where );
		$this->CI->db->select ( 'id,accountid,parent_id,number' );
		$did_info = ( array ) $this->CI->db->get ( 'dids' )->first_row ();
		if ($did_info ['accountid'] > 0 && $did_info ['parent_id'] == $accountinfo ['id']) {
			$flag_status = "<a href='../did_list_release/" . $did_info ['id'] . "' title='Release' onClick='return get_reliase_msg();'><span class=' label label-sm label-inverse_blue arrowed_blue-in' title='release'>Release(C)<span></a>";
		} else if ($accountinfo ['type'] != 1 && $did_info ['parent_id'] != $accountinfo ['id']) {
			$flag_status = "<a href='../did_list_release/" . $did_info ['id'] . "' title='Release' onClick='return get_reliase_msg();'><span class=' label label-sm label-inverse_blue arrowed_blue-in' title='release'>Release(R)</span></a>";
		} else {
			$reseller_id = $accountinfo ['type'] != 1 ? 0 : $accountinfo ['id'];
			$where = array (
					"note" => $did_info ['number'],
					'parent_id' => $reseller_id
			);
			$this->CI->db->where ( $where );
			$this->CI->db->select ( 'reseller_id,id' );
			$reseller_pricing_info = ( array ) $this->CI->db->get ( 'reseller_pricing' )->first_row ();
			if (isset ( $reseller_pricing_info ) && ! empty ( $reseller_pricing_info )) {
				$flag_status = "<a href='../did/did_reseller_edit/delete/" . $reseller_pricing_info ['id'] . "' title='Reliase' onClick='return get_reliase_msg();'><span class=' label label-sm label-inverse_blue arrowed_blue-in' title='release'>Release(R)</span></a>";
			} else {
				$flag_status = "<span class=' label label-sm label-inverse arrowed-in' title='Not in use'>Not in use</span>";
			}
		}
		return $flag_status;
	}

	// get data for Comma seprated
	function get_field_name_coma($select, $table, $where) {
		$value = '';
		if (is_array ( $where )) {
			$where = $where;
		} else {
			$where = explode ( ',', $where );
		}
		for($i = 0; $i < count ( $where ); $i ++) {
			$where_in = array (
					"id" => $where [$i]
			);

			$field_name = $this->CI->db_model->getSelect ( $select, $table, $where_in );
			$field_name = $field_name->result ();
			if (isset ( $field_name ) && ! empty ( $field_name )) {
				$value .= $field_name [0]->{$select} . ",";
			} else {
				$value = "";
			}
		}
		return rtrim ( $value, ',' );
	}
	function set_invoice_option($select = "", $table = "", $call_type = "", $edit_value = '') {
		$invoice_date = false;
		$uri_segment = $this->CI->uri->segments;
		if (isset ( $uri_segment [3] ) && $uri_segment [3] > 0 && empty ( $edit_value )) {
			$field_name = $this->CI->db_model->getSelect ( "sweep_id,invoice_day", "accounts", array (
					"id" => $uri_segment [3]
			) );
			$field_name = $field_name->result_array ();
			$select = $field_name [0] ["sweep_id"];
			$invoice_date = $field_name [0] ["invoice_day"];
		} else {
			$invoice_date = $edit_value;
		}
		if ($select == "" || $select == "0") {
			$daily_arr = array (
					"0" => "0"
			);
			return $daily_arr;
		}
		if ($select == 1) {
			$week_arr = array (
					"1" => "Monday",
					"2" => "Tuesday",
					"3" => "Wednesday",
					"4" => "Thursday",
					"5" => "Friday",
					"6" => "Saturday",
					"7" => "Sunday"
			);
			$rawDate = date ( "Y-m-d" );
			$day = date ( 'N', strtotime ( $rawDate ) );
			if (isset ( $uri_segment [3] )) {
				return $week_arr;
			} else {
				$week_drp = form_dropdown ( array (
						"name" => 'invoice_day',
						'style' => "width: 100% !important;",
						"class" => "invoice_day"
				), $week_arr, $day );
				return $week_drp;
			}
		}
		if ($select != 0 && $select != 1) {
			for($i = 1; $i < 29; $i ++) {
				$mon_arr [$i] = $i;
			}
			if (isset ( $uri_segment [3] ) && $uri_segment [3] > 0 && empty ( $edit_value )) {
				return $mon_arr;
			} else {
				$day = $invoice_date > 0 ? $invoice_date : date ( 'd' );
				$month_drp = form_dropdown ( array (
						"name" => 'invoice_day',
						"class" => "width_dropdown invoice_day"
				), $mon_arr, $day );
				return $month_drp;
			}
		}
	}
	function set_status($status = '') {
		$status_array = array (
				'0' => gettext ( 'Active' ),
				'1' => gettext ( 'Inactive' )
		);
		return $status_array;
	}
	function set_routetype($status = '') {
		$status_array = array (
				'0' => gettext ( 'LCR' ),
				'1' => gettext ( 'COST' )
		);
		return $status_array;
	}
	function set_prorate($status = '') {
		$status_array = array (
				'0' => gettext ( 'Yes' ),
				'1' => gettext ( 'No' )
		);
		return $status_array;
	}
	/*
	 * Add For Package Inbound or Outbound or both?
	 */
	function set_package_type($applicable_for = "") {
		$package_applicable = array (
				'0' => gettext ( 'Outbound' ),
				'1' => gettext ( 'Inbound' ),
				'2' => gettext ( 'Both' )
		);
		return $package_applicable;
	}
	function get_package_type($status = '', $table = "", $applicable_for) {
		$package_applicable = array (
				'0' => gettext ( 'Outbound' ),
				'1' => gettext ( 'Inbound' ),
				'2' => gettext ( 'Both' )
		);
		return $package_applicable [$applicable_for];
	}
	/**
	 * ******************************************
	 */
	function set_allow($status = '') {
		$status_array = array (
				'1' => gettext ( 'Yes' ),
				'0' => gettext ( 'No' )
		);
		return $status_array;
	}
	function set_allow_invoice($status = '') {
		$status_array = array (
				'1' => gettext ( 'Yes' ),
				'0' => gettext ( 'No' )
		);
		return $status_array;
	}
	function set_pin_allow($status = '') {
		$status_array = array (
				'0' => gettext ( 'Disable' ),
				'1' => gettext ( 'Enable' )
		);
		return $status_array;
	}
	function set_pin_allow_customer($status = '') {
		$status_array = array (
				'0' => gettext ( 'No' ),
				'1' => gettext ( 'Yes' )
		);
		return $status_array;
	}
	function get_allow($select = "", $table = "", $status) {
		return ($status == 1) ? "Yes" : "No";
	}
	function set_call_type($call_type = "") {
		$call_type_array = array (
				"-1" => gettext ( "--Select--" ),
				'1' => gettext ( 'DID-Local' ),
				'5' => gettext ( 'DID@IP/URL' ),
				'4' => gettext ( 'Direct-IP' ),
				'2' => gettext ( 'Other' ),
				'0' => gettext ( 'PSTN' ),
				'3' => gettext ( 'SIP-DID' )
		);
		return $call_type_array;
	}
	function set_call_type_search() {
		$call_type_array = array (
				"" => gettext ( "--Select--" ),
				'1' => gettext ( 'DID-Local' ),
				'5' => gettext ( 'DID@IP/URL' ),
				'4' => gettext ( 'Direct-IP' ),
				'2' => gettext ( 'Other' ),
				'0' => gettext ( 'PSTN' ),
				'3' => gettext ( 'SIP-DID' )
		);
		return $call_type_array;
	}
	function get_call_type($select = "", $table = "", $call_type) {
		$call_type_array = array (
				'1' => gettext ( 'DID-Local' ),
				'5' => gettext ( 'DID@IP/URL' ),
				'4' => gettext ( 'Direct-IP' ),
				'2' => gettext ( 'Other' ),
				'0' => gettext ( 'PSTN' ),
				'3' => gettext ( 'SIP-DID' ),
				'-1' => ""
		);
		return $call_type_array [$call_type];
	}
	function get_custom_call_type($call_type) {
		$call_type_array = array (
				'DID-Local' => '1',
				'DID@IP/URL' => '5',
				'Direct-IP' => '4',
				'Other' => '2',
				'PSTN' => '0',
				'SIP-DID' => '3',
				"" => "-1"
		);
		return $call_type_array [$call_type];
	}
	function set_sip_config_option($option = "") {
		$config_option = array (
				"true" => "True",
				"false" => "False"
		);
		return $config_option;
	}
	function set_option_default_false($option = "") {
                $config_option = array (
                                "false" => "False",
                                "true" => "True"
                );
                return $config_option;
        }
	function get_entity_type($select = "", $table = "", $entity_type) {
		$entity_array = array (
				'-1' => "Administrator",
				'0' => 'Customer',
				'1' => 'Reseller',
				'2' => 'Admin',
				'3' => "Provider",
				"4" => "Subadmin",
				"5" => "Callshop"
		);
		return ($entity_array [$entity_type]);
	}
	function set_entity_type_customer($entity_type = "") {
		$entity_array = array (
				'' => gettext ( "--Select--" ),
				'0' => gettext ( 'Customer' ),
				'3' => gettext ( "Provider" )
		);
		return $entity_array;
	}
	function set_entity_type_admin($entity_type = "") {
		$entity_array = array (
				'' => gettext ( "--Select--" ),
				'2' => gettext ( 'Admin' ),
				"4" => gettext ( "Sub Admin" )
		);
		return $entity_array;
	}
	function set_entity_type_email_mass($entity_type = "") {
		$entity_array = array (
				'' => gettext ( "--Select--" ),
				'0' => gettext ( 'Customer' ),
				'1' => gettext ( 'Reseller' ),
				'3' => gettext ( "Provider" )
		);
		return $entity_array;
	}
	function set_sip_config_options($option = "") {
		$config_option = array (
				"false" => gettext ( "False" ),
				"true" => gettext ( "True" )
		);
		return $config_option;
	}
	function set_sip_config_default($option = "") {
		$config_option = array (
				"" => gettext ( "--SELECT--" ),
				"false" => gettext ( "False" ),
				"true" => gettext ( "True" )
		);
		return $config_option;
	}
	function set_sip_bind_params($option = "") {
		$config_option = array (
				"" => gettext ( "--SELECT--" ),
				"udp" => gettext ( "UDP" ),
				"tcp" => gettext ( "TCP" )
		);
		return $config_option;
	}
	function set_sip_vad_option() {
		$config_option = array (
				"in" => gettext ( "In" ),
				"out" => gettext ( "Out" ),
				"both" => gettext ( "Both" )
		);
		return $config_option;
	}
	function set_sip_drp_option($option = "") {
		$status_array = array (
				'no' => gettext ( 'No' ),
				'yes' => gettext ( 'Yes' )
		);
		return $status_array;
	}
	function set_status_callingcard($status = '') {
		$status_array = array (
				'1' => gettext ( 'Active' ),
				'0' => gettext ( 'Inactive' ),
				'2' => gettext ( 'Deleted' )
		);
		return $status_array;
	}
	/*
	 * show status on all grid
	 */
	function get_status($select = "", $table = "", $status) {
		if ($select != 'export') {
			$status_tab = $this->encode ( $table );
			$status ['table'] = "'" . $status_tab . "'";
			if ($status ['status'] == 0) {
				$status_array = '<div style="width: 100%; text-align: -moz-center; padding: 0;"><input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id=switch' . $status ['id'] . ' value=' . $status ['status'] . ' onclick="javascript:processForm(' . $status ['id'] . ',' . $status ['table'] . ')" checked>
	<label class="onoffswitch-label" for=switch' . $status ["id"] . '>
     	<span class="onoffswitch-inner"></span>
	</label></div>';
			} else {
				$status_array = '<div style="width: 100%; text-align: -moz-center; padding: 0;"><input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id=switch' . $status ['id'] . ' value=' . $status ['status'] . ' onclick="javascript:processForm(' . $status ['id'] . ',' . $status ['table'] . ')">
	<label class="onoffswitch-label" for=switch' . $status ["id"] . '>
     	<span class="onoffswitch-inner"></span>
	</label></div>';
			}
		} else {
			return ($status == 0) ? "Active" : "Inactive";
		}
		return $status_array;
	}
	function get_routetype($select = "", $table = "", $status) {
		return ($status == 0) ? "LCR" : "COST";
	}
	function get_prorate($select = "", $table = "", $status) {
		return ($status == 0) ? "Yes" : "No";
	}
	function get_import_status($status) {
		return strtolower ( trim ( $status ) ) == 'active' ? 0 : 1;
	}
	function get_did_status($select, $table, $status) {
		return ($status ['status'] == 0) ? "<span class='label label-sm label-inverse arrowed-in' title='release'>Active<span>" : "<span class='label label-sm' title='release'>Inactive<span>";
	}

	/**
	 *
	 * @param string $select
	 */
	function get_invoice_date($select, $accountid = 0, $reseller_id, $order_by = 'id') {

		$where = array (
				"reseller_id" => $reseller_id
		);
		if ($accountid > 0) {
			$where ['accountid'] = $accountid;

			// Patch to fetch correct invoice date for postpaid customer
			$posttoexternal = $this->get_field_name ( "posttoexternal", "accounts", array (
					"id" => $accountid
			) );
			if ($posttoexternal == 1)
				$where ['type'] = "I";			
		}
		
		//Fetch date for only auto generated invoice date
		$where ['generate_type'] = "0";		
		
		$invoice_res = $this->CI->db_model->select ( $select, "invoices", $where, $order_by, "DESC", "1", "0" );

		if ($invoice_res->num_rows () > 0) {
			$invoice_info = ( array ) $invoice_res->first_row ();
			return $invoice_info [$select];
		}

		return false;
	}
	function convert_to_date($select = '', $table = '', $from_date) {
		$from_date = date ( 'Y-m-d', strtotime ( $from_date ) );
		return $from_date;
	}
	function get_account_balance($select = "", $table = "", $amount) {
		$this->CI->load->model ( 'common_model' );
		if ($amount == 0) {
			return $amount;
		} else {
			$balance = $this->CI->common_model->add_calculate_currency ( ($amount), "", '', true, true );

			return $balance;
		}
	}
	function convert_to_currency($select = "", $table = "", $amount) {
		$this->CI->load->model ( 'common_model' );
		return $this->CI->common_model->calculate_currency ( $amount, '', '', true, false );
	}
	function account_number_icon($select = "", $table = "", $number) {
		$return_value = '';
		$where = array (
				'number' => $number
		);
		$account_res = ( array ) $this->CI->db->get_where ( "accounts", $where )->first_row ();
		if ($account_res ['type'] == 0) {
			$return_value = '<div class="flx_font flx_magenta" title="Customer">C</div>' . " <span title='Edit'>" . $account_res ['number'] . " </span>";
		}
		if ($account_res ['type'] == 3) {
			$return_value = '<div class="flx_font flx_blue" title="Provider">P</div>' . " <span title='Edit'>" . $account_res ['number'] . " </span>";
		}
		if ($account_res ['type'] == - 1 || $account_res ['type'] == 2) {
			$return_value = '<div class="flx_font flx_pink" title="Admin">A</div>' . " <span title='Edit'>" . $account_res ['number'] . " </span>";
		}
		if ($account_res ['type'] == 4) {
			$return_value = '<div class="flx_font flx_purple" title="Subadmin">S</div>' . " <span title='Edit'>" . $account_res ['number'] . " </span>";
		}
		return $return_value;
	}
	function convert_to_currency_account($select = "", $table = "", $amount) {
		$this->CI->load->model ( 'common_model' );
		return $this->CI->common_model->calculate_currency_customer ( $amount );
	}
	function get_paid_status($select = "", $table = "", $status) {
		return ($status == 1) ? "Paid" : "Unpaid";
	}
	function set_account_type($status = '') {
		$status_array = array (
				'0' => gettext ( 'Prepaid' ),
				'1' => gettext ( 'Postpaid' )
		);
		return $status_array;
	}
	function set_account_type_search($status = '') {
		$status_array = array (
				'' => gettext ( "--Select--" ),
				'0' => gettext ( 'Prepaid' ),
				'1' => gettext ( 'Postpaid' )
		);
		return $status_array;
	}
	function get_account_type($select = "", $table = "", $PTE) {
		return ($PTE == 1) ? "Postpaid" : "Prepaid";
	}

	/**
	 * ****
	 * Payment to refill
	 * ****
	 */
	function get_refill_by($select = "", $table = "", $type) {
		if ($type == '-1') {
			$type = "Admin";
		} else {
			$type = $this->get_field_name("number", "accounts", array("id" => $type));
			//$type = $this->build_concat_string ( $select, $table, $type );
		}
		return $type;
	}

	/**
	 * *****************
	 */
	function get_payment_by($select = "", $table = "", $type) {
		if ($type == '-1') {
			$type = "Admin";
		} else {
			$type = $this->get_field_name ( "number", "accounts", array (
					"id" => $type
			) );
		}
		return $type;
	}
	function set_payment_type($payment_type = '') {
		/*
		 * Recharge to Refill
		 */
		$status_array = array (
				'0' => gettext ( 'Refill' ),
				'1' => gettext ( 'Postcharge' )
		);
		return $status_array;
	}
	function search_int_type($status = '') {
		$status_array = array (
				'1' => gettext ( 'Is Equal To' ),
				'2' => gettext ( 'Is Not Equal To' ),
				'3' => gettext ( 'Greater Than' ),
				'4' => gettext ( 'Less Than' ),
				'5' => gettext ( 'Greater Or Equal Than' ),
				'6' => gettext ( 'Less Or Equal Than' )
		);
		return $status_array;
	}
	function update_int_type($status = '') {
		$status_array = array (
				'1' => gettext ( 'Preserve' ),
				'2' => gettext ( 'Set To' ),
				'3' => gettext ( 'Increase By' ),
				'4' => gettext ( 'Decrease By' )
		);
		return $status_array;
	}
	function update_drp_type($status = '') {
		$status_array = array (
				'1' => gettext ( 'Preserve' ),
				'2' => gettext ( 'Set To' )
		);
		return $status_array;
	}
	function search_string_type($status = '') {
		$status_array = array (
				'5' => gettext ( "Begins With" ),
				'1' => gettext ( 'Contains' ),
				'2' => gettext ( 'Doesnt Contain' ),
				'3' => gettext ( 'Is Equal To' ),
				'4' => gettext ( 'Is Not Equal To' ),
				"6" => gettext ( "Ends With" )
		);
		return $status_array;
	}
	function set_protocal($protpcal = '') {
		$status_array = array (
				'SIP' => gettext ( 'SIP' ),
				'IAX2' => gettext ( 'IAX2' ),
				'Zap' => gettext ( 'Zap' ),
				'Local' => gettext ( 'Local' ),
				'OH323' => gettext ( 'OH323' ),
				'OOH323C' => gettext ( 'OOH323C' )
		);
		return $status_array;
	}

	/*
	 *
	 * Purpose : Add Profit Margin report
	 * Version 2.1
	 */
	function set_notify_by($status = '') {
		$status_array = array (
				'' => gettext ( 'Select Notify By' ),
				'0' => gettext ( 'CSV' ),
				'1' => gettext ( 'Email' )
		);
		return $status_array;
	}
	function convert_to_percentage($select = "", $table = "", $amount) {
		return round ( $amount, 2 ) . " %";
	}
	function convert_to_minutes($select = "", $table = "", $amount) {
		return str_replace ( '.', ':', round ( $amount / 60, 2 ) );
	}
	function set_filter_type_search($status = '') {
		$status_array = array (
				'pricelist_id' => gettext ( 'Rate Group' ),
				'accountid' => gettext ( 'Customer' ),
				'reseller_id' => gettext ( 'Reseller' )
		);
		return $status_array;
	}
	function set_routetype_status($select = '') {
		$status_array = array (
				"" => gettext ( "--Select--" ),
				"0" => gettext ( "LCR" ),
				"1" => gettext ( "COST" )
		);
		return $status_array;
	}

	// attachment download in email module...
	function attachment_icons($select = "", $table = "", $attachement = "") {
		if ($attachement != "") {
			$array = explode ( ",", $attachement );
			$str = '';
			foreach ( $array as $key => $val ) {
				$link = base_url () . "email/email_history_list_attachment/" . $val;
				$str .= "<a href='" . $link . "' title='" . $val . "' class='btn btn-royelblue btn-sm'><i class='fa fa-paperclip fa-fw'></i></a>&nbsp;&nbsp;";
			}
			return $str;
		} else {
			return "";
		}
	}

	/* * ************************************************************* */
	function set_despostion($dis = '') {
		$status_array = array (
				"" => "--Select Disposition--",
				"ACCOUNT_INACTIVE_DELETED" => "ACCOUNT_INACTIVE_DELETED",
				"ACCOUNT_EXPIRE" => "ACCOUNT_EXPIRE",
				"ALLOTTED_TIMEOUT" => "ALLOTTED_TIMEOUT",
				"AUTHENTICATION_FAIL" => "AUTHENTICATION_FAIL",
				"BEARERCAPABILITY_NOTAUTH" => "BEARERCAPABILITY_NOTAUTH",
				"BEARERCAPABILITY_NOTAVAIL" => "BEARERCAPABILITY_NOTAVAIL",
				"BEARERCAPABILITY_NOTIMPL" => "BEARERCAPABILITY_NOTIMPL",
				"CALL_REJECTED" => "CALL_REJECTED",
				"CHAN_NOT_IMPLEMENTED" => "CHAN_NOT_IMPLEMENTED",
				"CHANNEL_UNACCEPTABLE" => "CHANNEL_UNACCEPTABLE",
				"DESTINATION_OUT_OF_ORDER" => "DESTINATION_OUT_OF_ORDER",
				"DESTINATION_BLOCKED" => "DESTINATION_BLOCKED",
				"DID_DESTINATION_NOT_FOUND" => "DID_DESTINATION_NOT_FOUND",
				"FACILITY_REJECTED" => "FACILITY_REJECTED",
				"FACILITY_NOT_SUBSCRIBED" => "FACILITY_NOT_SUBSCRIBED",
				"FACILITY_NOT_IMPLEMENTED" => "FACILITY_NOT_IMPLEMENTED",
				"INVALID_NUMBER_FORMAT" => "INVALID_NUMBER_FORMAT",
				"INCOMPATIBLE_DESTINATION" => "INCOMPATIBLE_DESTINATION",
				"MEDIA_TIMEOUT" => "MEDIA_TIMEOUT",
				"NO_ROUTE_DESTINATION" => "NO_ROUTE_DESTINATION",
				"NORMAL_CLEARING" => "NORMAL_CLEARING",
				"NETWORK_OUT_OF_ORDER" => "NETWORK_OUT_OF_ORDER",
				"NORMAL_UNSPECIFIED" => "NORMAL_UNSPECIFIED",
				"NORMAL_CIRCUIT_CONGESTION" => "NORMAL_CIRCUIT_CONGESTION",
				"NORMAL_TEMPORARY_FAILURE" => "NORMAL_TEMPORARY_FAILURE",
				"NO_SUFFICIENT_FUND" => "NO_SUFFICIENT_FUND",
				"NO_USER_RESPONSE" => "NO_USER_RESPONSE",
				"NO_ANSWER" => "NO_ANSWER",
				"NUMBER_CHANGED" => "NUMBER_CHANGED",
				"ORIGINATOR_CANCEL" => "ORIGINATOR_CANCEL",
				"ORIGNATION_RATE_NOT_FOUND" => "ORIGNATION_RATE_NOT_FOUND",
				"OUTGOING_CALL_BARRED" => "OUTGOING_CALL_BARRED",
				"PROGRESS_TIMEOUT" => "PROGRESS_TIMEOUT",
				"RECOVERY_ON_TIMER_EXPIRE" => "RECOVERY_ON_TIMER_EXPIRE",
				"RESELLER_COST_CHEAP" => "RESELLER_COST_CHEAP",
				"SERVICE_NOT_IMPLEMENTED" => "SERVICE_NOT_IMPLEMENTED",
				"SERVICE_UNAVAILABLE" => "SERVICE_UNAVAILABLE",
				"SUCCESS" => "SUCCESS",
				"SWITCH_CONGESTION" => "SWITCH_CONGESTION",
				"TERMINATION_RATE_NOT_FOUND" => "TERMINATION_RATE_NOT_FOUND",
				"UNSPECIFIED" => "UNSPECIFIED",
				"UNALLOCATED_NUMBER" => "UNALLOCATED_NUMBER",
				"USER_BUSY" => "USER_BUSY",
				"USER_NOT_REGISTERED" => "USER_NOT_REGISTERED"
		);
		return $status_array;
	}
	function set_calltype($type = '') {
		$status_array = array (
				"" => gettext ( "--Select Type--" ),
				"STANDARD" => gettext ( "STANDARD" ),
				"DID" => gettext ( "DID" ),
				"CALLINGCARD" => gettext ( "CALLINGCARD" ),
				"FREE" => gettext ( "FREE" )
		);
		return $status_array;
	}
	function set_search_status($select = '') {
		$status_array = array (
				"" => gettext ( "--Select--" ),
				"0" => gettext ( "Active" ),
				"1" => gettext ( "Inactive" )
		);
		return $status_array;
	}
	function set_Billing_Schedule_status($select = '') {
		$status_array = array (
				"" => gettext ( "--Select--" ),
				"0" => gettext ( "Daily" ),
				"2" => gettext ( "Monthly" )
		);
		return $status_array;
	}
	function get_action_buttons($buttons_arr, $linkid) {
		$ret_url = '';
		if (! empty ( $buttons_arr ) && $buttons_arr != '') {
			foreach ( $buttons_arr as $button_key => $buttons_params ) {
				if (strtoupper ( $button_key ) == "EDIT") {
					$ret_url .= $this->build_edit_button ( $buttons_params, $linkid );
				}
				/*
				 *
				 * Purpose : Add resend link
				 * Version 2.1
				 */
				if (strtoupper ( $button_key ) == "RESEND") {
					$ret_url .= $this->build_edit_button_resend ( $buttons_params, $linkid );
				}
				/* * ************************************* */
				if (strtoupper ( $button_key ) == "EDIT_RESTORE") {
					$ret_url .= $this->build_edit_button_restore ( $buttons_params, $linkid );
				}
				if (strtoupper ( $button_key ) == "DELETE") {
					$ret_url .= $this->build_delete_button ( $buttons_params->url, $linkid );
				}
				if (strtoupper ( $button_key ) == "VIEW") {
					$ret_url .= $this->build_view_button ( $buttons_params, $linkid );
				}
				if (strtoupper ( $button_key ) == "TAXES") {
					$ret_url .= $this->build_add_taxes_button ( $buttons_params, $linkid );
				}
				if (strtoupper ( $button_key ) == "BLUEBOX_LOGIN") {
					$ret_url .= $this->build_bluebox_login ( $buttons_params->url, $linkid );
				}
				if (strtoupper ( $button_key ) == "CALLERID") {
					$ret_url .= $this->build_add_callerid_button ( $buttons_params, $linkid );
				}
				if (strtoupper ( $button_key ) == "PAYMENT") {
					$ret_url .= $this->build_add_payment_button ( $buttons_params->url, $linkid );
				}
				if (strtoupper ( $button_key ) == "DOWNLOAD") {
					$ret_url .= $this->build_add_download_button ( $buttons_params->url, $linkid );
				}
				if (strtoupper ( $button_key ) == "START") {
					$ret_url .= $this->build_start_button ( $buttons_params->url, $linkid );
				}
				if (strtoupper ( $button_key ) == "STOP") {
					$ret_url .= $this->build_stop_button ( $buttons_params->url, $linkid );
				}
				if (strtoupper ( $button_key ) == "RELOAD") {
					$ret_url .= $this->build_reload_button ( $buttons_params->url, $linkid );
				}
				if (strtoupper ( $button_key ) == "RESCAN") {
					$ret_url .= $this->build_rescan_button ( $buttons_params->url, $linkid );
				}

				if (strtoupper ( $button_key ) == "DOWNLOAD_DATABASE") {
					$ret_url .= $this->build_add_download_database_button ( $buttons_params->url, $linkid );
				}
				if (strtoupper ( $button_key ) == "DELETE_ANIMAP") {
					$ret_url .= $this->build_delete_button_animap ( $buttons_params->url, $linkid );
				}
				if (strtoupper ( $button_key ) == "EDIT_ANIMAP") {
					$ret_url .= $this->build_edit_button_animap ( $buttons_params, $linkid );
				}
				if (strtoupper ( $button_key ) == "ANIMAP") {
					$ret_url .= $this->build_animap_button ( $buttons_params, $linkid );
				}
			}
		}
		return $ret_url;
	}
	function build_delete_button_animap($url, $linkid) {
		$link = base_url () . $url . "" . $linkid;
		return '<a href="javascript:void(0)" class="btn btn-royelblue btn-sm" title="Delete" onClick="return get_alert_msg_destination(' . $linkid . ');"><i class="fa fa-trash fa-fw"></i></a>';
	}
	function build_edit_button_animap($button_params, $linkid) {
		$link = base_url () . $button_params->url . "" . $linkid;
		return '<a href="javascript:void(0);" id="destination_new" class="btn btn-royelblue btn-sm" onclick="return get_destination(' . $linkid . ');" title="Update"><i class="fa fa-pencil-square-o fa-fw"></i></a>&nbsp;';
	}
	function build_animap_button($button_params, $linkid) {
		$link = base_url () . $button_params->url . "" . $linkid;
		return '<a href="' . $link . '" class="btn btn-royelblue btn-sm animap_image" rel="facebox" title="ANI Map"><i class="fa fa-reorder fa-fw"></i></a>&nbsp;';
	}
	function build_edit_button($button_params, $linkid) {
		$link = base_url () . $button_params->url . "" . $linkid;

		if ($button_params->mode == 'popup') {
			$rel = (isset ( $button_params->layout ) && $button_params->layout != '') ? "facebox_" . $button_params->layout : "facebox";
			return '<a href="' . $link . '" class="btn btn-royelblue btn-sm" rel="' . $rel . '" title="Edit" ="small"><i class="fa fa-pencil-square-o fa-fw"></i></a>&nbsp;';
		} else if (strpos ( $link, 'customer_edit' ) !== false) {
			return '<a href="' . $link . '" class="btn btn-royelblue btn-sm" title="Edit"><i class="fa fa-pencil-square-o fa-fw"></i></a>&nbsp;';
		} else {
			return '<a href="' . $link . '" class="btn btn-royelblue btn-sm" title="Edit"><i class="fa fa-pencil-square-o fa-fw"></i></a>&nbsp;';
		}
	}

	/**
	 * For Edit on Account number or name
	 */
	function build_custome_edit_button($button_params, $field, $linkid) {
		$link = base_url () . $button_params->url . "" . $linkid;
		if (isset ( $button_params->layout )) {
			if ($button_params->mode == 'popup') {
				return '<a href="' . $link . '" style="cursor:pointer;color:#005298;" rel="facebox_medium" title="Update">' . $field . '</a>&nbsp;';
			} else {
				return '<a href="' . $link . '" style="cursor:pointer;color:#005298;" title="Edit">' . $field . '</a>&nbsp;';
			}
		} else {
			if ($button_params->mode == 'popup') {
				return '<a href="' . $link . '" style="cursor:pointer;color:#005298;" rel="facebox" title="Update">' . $field . '</a>&nbsp;';
			} else {
				return '<a href="' . $link . '" style="cursor:pointer;color:#005298;" title="Edit">' . $field . '</a>&nbsp;';
			}
		}
	}

	/**
	 * **********************************
	 */
	function build_edit_button_restore($button_params, $linkid) {
		$link = base_url () . $button_params->url . "" . $linkid;
		if ($button_params->mode == 'popup') {
			return '<a href="' . $link . '" class="btn btn-royelblue btn-sm" rel="facebox" title="Restore" onClick="return get_alert_msg();"><i class="fa fa-reorder fa-fw"></i></a>&nbsp;';
		} else {
			return '<a href="' . $link . '" class="btn btn-royelblue btn-sm" title="Restore" onClick="return get_alert_msg_restore();"><i class="fa fa-reorder fa-fw"></i></a>&nbsp;';
		}
	}
	function build_delete_button($url, $linkid) {
		$flag = '0';
		$data = explode ( "/", $url );
		$link = base_url () . $url . "" . $linkid;
		foreach ( $data as $key => $value ) {
			if ($value == 'price_delete')
				$flag = '1';
			if ($value == 'trunk_remove')
				$flag = '2';
			if ($value == 'customer_delete' || $value == 'provider_delete')
				$flag = '3';
			if ($value == 'reseller_delete')
				$flag = '4';
		}
		if ($flag == '1') {
			$where = array (
					'pricelist_id' => $linkid,
					'deleted !=' => '1'
			);
			$customer_cnt = $this->get_field_count ( 'id', 'accounts', $where );
			$where = array (
					'pricelist_id' => $linkid
			);
			$rategroup_cnt = $this->get_field_count ( 'id', 'routes', $where );
			if ($rategroup_cnt > 0 || $customer_cnt > 0) {

				return '<a href="' . $link . '" class="btn btn-royelblue btn-sm" title="Delete" onClick="return get_alert_message(' . $rategroup_cnt . ',' . $customer_cnt . ',' . $linkid . ',1);"><i class="fa fa-trash fa-fw"></i></a>';
			} else {
				return '<a href="' . $link . '" class="btn btn-royelblue btn-sm" title="Delete" onClick="return get_alert_msg();"><i class="fa fa-trash fa-fw"></i></a>';
			}
		}
		if ($flag == '2') {
			$where = array (
					'trunk_id' => $linkid
			);
			$trunk_cnt = $this->get_field_count ( 'id', 'outbound_routes', $where );
			if ($trunk_cnt > 0) {
				return '<a href="' . $link . '" class="btn btn-royelblue btn-sm" title="Delete" onClick="return get_alert_message(' . $trunk_cnt . ',null,' . $linkid . ',2);"><i class="fa fa-trash fa-fw"></i></a>';
			} else {
				return '<a href="' . $link . '" class="btn btn-royelblue btn-sm" title="Delete" onClick="return get_alert_msg();"><i class="fa fa-trash fa-fw"></i></a>';
			}
		}
		if ($flag == '3') {
			return '<a href="' . $link . '" class="btn btn-royelblue btn-sm" title="Delete" onClick="return get_alert_message(0,null,' . $linkid . ',3);">
		<i class="fa fa-trash fa-fw"></i></a>';
		}
		if ($flag == '4') {
			return '<a href="' . $link . '" class="btn btn-royelblue btn-sm" title="Delete" onClick="return get_alert_message(0,null,' . $linkid . ',4);">
		<i class="fa fa-trash fa-fw"></i></a>';
		}
		if ($flag == '0' && $url . $linkid != 'accounts/admin_delete/1') {
			return '<a href="' . $link . '" class="btn btn-royelblue btn-sm" title="Delete" onClick="return get_alert_msg();"><i class="fa fa-trash fa-fw"></i></a>';
		}
	}
	function build_view_button($button_params, $linkid) {
		$link = base_url () . $button_params->url . "" . $linkid;
		if ($button_params->mode == 'popup') {
			$rel = (isset ( $button_params->layout ) && $button_params->layout != '') ? "facebox_" . $button_params->layout : "facebox";
			return '<a href="' . $link . '" class="btn btn-royelblue btn-sm" rel="' . $rel . '" title="View Details"><i class="fa fa-reorder fa-fw"></i></a>&nbsp;';
		} else {
			return '<a href="' . $link . '" class="btn btn-royelblue btn-sm" title="View Details"><i class="fa fa-reorder fa-fw"></i></a>&nbsp;';
		}
	}
	function build_add_taxes_button($button_params, $linkid) {
		$link = base_url () . $button_params->url . "" . $linkid;
		if ($button_params->mode == 'popup') {
			return '<a href="' . $link . '" class="btn btn-royelblue btn-sm" rel="facebox" title="Add Account Taxes"><i class="fa fa-reorder fa-fw"></i></a>&nbsp;';
		} else {
			return '<a href="' . $link . '" class="btn btn-royelblue btn-sm" title="Add Account Taxes"><i class="fa fa-reorder fa-fw"></i></a>&nbsp;';
		}
	}
	function build_add_download_database_button($url, $linkid) {
		$link = base_url () . $url . "" . $linkid;
		return '<a href="' . $link . '" class="btn btn-royelblue btn-sm "  title="Download Database" ><i class="fa-fw fa fa-file-archive-o"></i></a>&nbsp;';
	}
	function build_add_callerid_button($button_params, $linkid) {
		$link = base_url () . $button_params->url . "" . $linkid;
		if ($button_params->mode == 'popup') {
			return '<a href="' . $link . '" class="btn btn-royelblue btn-sm" rel="facebox" title="Force Caller Id"><i class="fa fa-mobile-phone fa-fw"></i></a>&nbsp;';
		} else {
			return '<a href="' . $link . '" class="btn btn-royelblue btn-sm" title="CallerID"><i class="fa fa-mobile-phone fa-fw"></i></a>&nbsp;';
		}
	}
	function build_start_button($url, $linkid) {
		$link = base_url () . $url . "" . $linkid;

		return '<a href="' . $link . '" class=""  title="Start" style="text-decoration:none;color: #428BCA;"><b>Start |</b></a>&nbsp;';
	}
	function build_stop_button($url, $linkid) {
		$link = base_url () . $url . "" . $linkid;
		return '<a href="' . $link . '" class=""  title="Stop" style="text-decoration:none;color: #428BCA;" ><b>Stop |</b></a>&nbsp;';
	}
	function build_reload_button($url, $linkid) {
		$link = base_url () . $url . "" . $linkid;
		return '<a href="' . $link . '" class=""  title="reload" style="text-decoration:none;color: #428BCA;"><b>Reload |</b></a>&nbsp;';
	}
	function build_rescan_button($url, $linkid) {
		$link = base_url () . $url . "" . $linkid;
		return '<a href="' . $link . '" class=""  title="rescan" style="text-decoration:none;color: #428BCA;"><b>Rescan</b></a>&nbsp;';
	}
	function build_add_payment_button($url, $linkid) {
		$link = base_url () . $url . "" . $linkid;
		return '<a href="' . $link . '" class="btn btn-royelblue btn-sm" rel="facebox" title="Refill" ><i class="fa fa-usd fa-fw"></i></a>&nbsp;';
	}
	function build_add_download_button($url, $linkid) {
		$link = base_url () . $url . "" . $linkid;
		return '<a href="' . $link . '" class="btn btn-royelblue btn-sm"  title="Download Invoice" ><i class="fa fa-cloud-download fa-fw"></i></a>&nbsp;';
	}

	/*
	 * Purpose : Add following for resent icon
	 * Version 2.1
	 */
	function build_edit_button_resend($button_params, $linkid) {
		$link = base_url () . $button_params->url . "" . $linkid;
		if ($button_params->mode == 'popup') {
			return '<a href="' . $link . '" class="btn btn-royelblue btn-sm" rel="facebox" title="Resend Mail"><i class="fa fa-repeat"></i></a>&nbsp;';
		} else {
			return '<a href="' . $link . '" class="btn btn-royelblue btn-sm" title="Resend Mail"><i class="fa fa-repeat"></i></a>&nbsp;';
		}
	}

	/*
	 * ----------------------------------------------------------------------------
	 */
	function get_only_numeric_val($select = "", $table = "", $string) {
		return filter_var ( $string, FILTER_SANITIZE_NUMBER_INT );
	}
	function mail_to_users($type, $accountinfo, $attachment = "", $amount = "") {
		$subject = "";
		$reseller_id = $accountinfo ['reseller_id'] > 0 ? $accountinfo ['reseller_id'] : 0;
		$where = "accountid IN ('" . $reseller_id . "','1')";
		$this->CI->db->where ( $where );
		$this->CI->db->select ( 'emailaddress' );
		$this->CI->db->order_by ( 'accountid', 'desc' );
		$this->CI->db->limit ( 1 );
		$invoiceconf = $this->CI->db->get ( 'invoice_conf' );
		$invoiceconf = ( array ) $invoiceconf->first_row ();
		$settings_reply_email = $invoiceconf ['emailaddress'];
		$company_name = Common_model::$global_config ['system_config'] ['company_name'];
		$company_website = Common_model::$global_config ['system_config'] ['company_website'];
		$where = array (
				'name' => $type
		);
		$query = $this->CI->db_model->getSelect ( "*", "default_templates", $where );
		$query = $query->result ();
		$message = $query [0]->template;
		$useremail = $accountinfo ['email'];
		$message = html_entity_decode ( $message );
		$message = str_replace ( "#COMPANY_EMAIL#", $settings_reply_email, $message );
		$message = str_replace ( "#COMPANY_NAME#", $company_name, $message );
		$message = str_replace ( "#COMPANY_WEBSITE#", $company_website, $message );
		$message = str_replace ( "<p>", "", $message );
		$message = str_replace ( "</p>", "", $message );
		if (isset ( $accountinfo ['refill_amount'] ) && $accountinfo ['refill_amount'] != "") {
			$refillamount = $accountinfo ['refill_amount'];
		} else {
			$refillamount = "0";
		}

		$subject = $query [0]->subject;
		switch ($type) {
			case 'email_add_user' :
				$message = str_replace ( '#NAME#', $accountinfo ['first_name'] . " " . $accountinfo ['last_name'], $message );
				$message = str_replace ( '#NUMBER#', $accountinfo ['number'], $message );
				$message = str_replace ( '#PASSWORD#', $accountinfo ['password'], $message );
				$message = str_replace ( '#LINK#', $accountinfo ['confirm'], $message );
				break;
			case 'email_forgot_user' :
				$message = str_replace ( '#NAME#', $accountinfo ['first_name'] . " " . $accountinfo ['last_name'], $message );
				$message = str_replace ( '#NUMBER#', $accountinfo ['number'], $message );
				$message = str_replace ( '#PASSWORD#', $accountinfo ['password'], $message );
				$message = str_replace ( '#LINK#', $accountinfo ['link'], $message );
				break;

			case 'email_forgot_confirmation' :
				$message = str_replace ( '#NAME#', $accountinfo ['first_name'] . " " . $accountinfo ['last_name'], $message );
				$message = str_replace ( '#CONFIRM#', $accountinfo ['confirm'], $message );
				break;

			case 'email_signup_confirmation' :
				$message = str_replace ( '#NAME#', $accountinfo ['first_name'] . " " . $accountinfo ['last_name'], $message );
				$message = str_replace ( '#CONFIRM#', $accountinfo ['confirm'], $message );
				break;
			case 'add_sip_device' :
				$sip_profiles = ( array ) $this->CI->db->get ( 'sip_profiles' )->first_row ();
				$profile_data = json_decode ( $sip_profiles ['profile_data'], true );
				if (Common_model::$global_config ['system_config'] ['opensips'] == 0) {
					$domain = Common_model::$global_config ['system_config'] ['opensips_domain'];

					$port = $sip_profiles ['sip_port'];
				} else {
					$fs_server = ( array ) $this->CI->db->get ( 'freeswich_servers' )->first_row ();
					$domain = $fs_server ['freeswitch_host'];
					$port = $fs_server ['freeswitch_port'];
				}
				$codec = $profile_data ['outbound-codec-prefs'];
				$sip_info = "SIP Server : " . $domain . " SIP Port : " . $port . " Preferable codecs : " . $codec;
				$message = str_replace ( '#NAME#', $accountinfo ['first_name'] . " " . $accountinfo ['last_name'], $message );
				$message = str_replace ( '#USERNAME#', $accountinfo ['number'], $message );
				$message = str_replace ( '#SIPHOST#', $sip_info, $message );
				$message = str_replace ( '#PASSWORD#', $accountinfo ['password'], $message );
				break;

			case 'voip_account_refilled' :
				$currency_id = $accountinfo ['currency_id'];
				$currency = $this->CI->common->get_field_name ( 'currency', 'currency', $currency_id );
				$message = str_replace ( '#NAME#', $accountinfo ['first_name'] . " " . $accountinfo ['last_name'], $message );
				$message = str_replace ( '#REFILLBALANCE#', $this->convert_to_currency ( '', '', $accountinfo ['refill_amount'] ) . ' ' . $currency, $message );
				$message = str_replace ( '#BALANCE#', $this->convert_to_currency ( '', '', $accountinfo ['refill_amount_balance'] ) . ' ' . $currency, $message );
				break;
			case 'voip_child_account_refilled' :
				$reseller_number = $this->CI->common->get_field_name ( 'number', 'accounts', $accountinfo ['reseller_id'] );
				$message = str_replace ( '#NAME#', $accountinfo ['first_name'] . " " . $accountinfo ['last_name'], $message );
				$message = str_replace ( '#REFILLBALANCE#', $accountinfo ['refill_amount'], $message );
				$message = str_replace ( '#BALANCE#', $accountinfo ['balance'], $message );
				$message = str_replace ( '#ACCOUNTNUMBER#', $reseller_number, $message );
				break;
			case 'add_subscription' :
				$message = str_replace ( '#NAME#', $accountinfo ['first_name'] . " " . $accountinfo ['last_name'], $message );
				break;
			case 'remove_subscription' :
				$message = str_replace ( '#NAME#', $accountinfo ['first_name'] . " " . $accountinfo ['last_name'], $message );
				break;
			case 'add_package' :
				$message = str_replace ( '#NAME#', $accountinfo ['first_name'] . " " . $accountinfo ['last_name'], $message );
				break;
			case 'remove_package' :
				$message = str_replace ( '#NAME#', $accountinfo ['first_name'] . " " . $accountinfo ['last_name'], $message );
				break;
			case 'email_calling_card' :
				$message = str_replace ( '#NAME#', $accountinfo ['first_name'] . " " . $accountinfo ['last_name'], $message );
				$message = str_replace ( '#CARDNUMBER#', $accountinfo ['cardnumber'], $message );
				$message = str_replace ( '#PIN#', $accountinfo ['pin'], $message );
				$message = str_replace ( '#BALANCE#', $accountinfo ['balance'], $message );
				break;
			case 'email_low_balance' :
				$to_currency = $this->CI->common->get_field_name ( 'currency', 'currency', $accountinfo ['currency_id'] );
				$balance = ($accountinfo ['posttoexternal'] == 1) ? ($accountinfo ["credit_limit"] - $accountinfo ["balance"]) : ($accountinfo ["balance"]);
				$useremail = ! empty ( $accountinfo ['notify_email'] ) ? $accountinfo ['notify_email'] : $accountinfo ['email'];
				$message = str_replace ( '#NAME#', $accountinfo ['first_name'] . " " . $accountinfo ['last_name'], $message );
				$message = str_replace ( '#AMOUNT#', $balance, $message );				
				$subject = str_replace ( "#NUMBER#", $accountinfo ['number'], $subject );
				break;
			case 'email_new_invoice' :
				$message = str_replace ( '#NAME#', $accountinfo ['first_name'] . " " . $accountinfo ['last_name'], $message );
				$message = str_replace ( '#AMOUNT#', $amount, $message );
				$message = str_replace ( '#INVOICE_NUMBER#', $amount, $message );				
				$subject = str_replace ( "#INVOICE_NUMBER#", $amount, $subject );
				break;
			case 'email_add_did' :
				if (isset ( $accountinfo ['did_maxchannels'] ) && $accountinfo ['did_maxchannels'] == "") {
					$accountinfo ['did_maxchannels'] = "Unlimited";
				}
				$message = str_replace ( '#NAME#', $accountinfo ['first_name'] . " " . $accountinfo ['last_name'], $message );
				$message = str_replace ( '#DIDNUMBER#', $accountinfo ['did_number'], $message );
				$message = str_replace ( '#COUNTRYNAME#', $accountinfo ['did_country_id'], $message );
				$message = str_replace ( '#SETUPFEE#', $accountinfo ['did_setup'], $message );
				$message = str_replace ( '#MONTHLYFEE#', $accountinfo ['did_monthlycost'], $message );
				$message = str_replace ( '#MAXCHANNEL#', $accountinfo ['did_maxchannels'], $message );
				$message = str_replace ( '#NUMBER#', $accountinfo ['number'], $message );				
				$subject = str_replace ( "#NUMBER#", $accountinfo ['number'], $subject );
				$subject = str_replace ( "#DIDNUMBER#", $accountinfo ['did_number'], $subject );
				break;
			case 'email_remove_did' :
				$message = str_replace ( '#NAME#', $accountinfo ['first_name'] . " " . $accountinfo ['last_name'], $message );
				$message = str_replace ( '#DIDNUMBER#', $accountinfo ['did_number'], $message );
				$message = str_replace ( '#NUMBER#', $accountinfo ['number'], $message );				
				$subject = str_replace ( "#NUMBER#", $accountinfo ['number'], $subject );
				$subject = str_replace ( "#DIDNUMBER#", $accountinfo ['did_number'], $subject );
				break;
		}

		$subject = str_replace ( "#NAME#", $accountinfo ['first_name'] . " " . $accountinfo ['last_name'], $subject );
		$message = str_replace ( "#COMPANY#", $accountinfo ['company_name'], $message );
		$subject = str_replace ( "#COMPANY#", $accountinfo ['company_name'], $subject );

		$account_id = (isset ( $accountinfo ['last_id'] ) && $accountinfo ['last_id'] != "") ? $accountinfo ['last_id'] : $accountinfo ['id'];
		$reseller_id = $accountinfo ['reseller_id'];
		if ($reseller_id != "0") {
			$reseller_result = $this->CI->db_model->getSelect ( "email", "accounts", array (
					"id" => $reseller_id
			) );
			$reseller_info = ( array ) $reseller_result->first_row ();
			$settings_reply_email = $reseller_info ['email'];
		}
		$this->emailFunction ( $settings_reply_email, $useremail, $subject, $message, $company_name, $attachment, $account_id, $reseller_id );
	}

	/**
	 *
	 * @param string $message
	 */
	function emailFunction($from, $to, $subject, $message, $company_name = "", $attachment = "", $account_id, $reseller_id) {
		$send_mail_details = array (
				'from' => $from,
				'to' => $to,
				'subject' => $subject,
				'body' => $message,
				'accountid' => $account_id,
				'status' => '1',
				'attachment' => $attachment,
				'reseller_id' => $reseller_id
		);

		$this->CI->db->insert ( 'mail_details', $send_mail_details );
		return $this->CI->db->insert_id ();
	}

	// Added new parameter timezone_id for API
	function convert_GMT_to($select = "", $table = "", $date, $timezone_id = '') {
		if ($date == '0000-00-00 00:00:00') {
			return $date;
		} else {
			return $this->CI->timezone->display_GMT ( $date, 1, $timezone_id );
		}
	}
	function convert_GMT($date) {
		return $this->CI->timezone->convert_to_GMT ($date );
	}

	// @todo : Not sure how above function working. Rather impacting on existing application, creating new function.
	function custom_convert_GMT($date, $timezone_id) {
		return $this->CI->timezone->convert_to_GMT ( $date, 1, $timezone_id );
	}
	function convert_to_ucfirst($select = "", $table = "", $str_value) {
		return ucfirst ( $str_value );
	}
	function set_charge_type($status = '') {
		$status_array = array (
				'1' => gettext ( 'Accounts' ),
				'2' => gettext ( 'Rate Group' )
		);
		return $status_array;
	}
	function build_concat_string($select, $table, $id_where = '') {
		$select_params = explode ( ',', $select );
		$where = array (
				"1"
		);
		if ($id_where != '') {
			$where = array (
					"id" => $id_where
			);
		}
		$select_params = explode ( ',', $select );
		if (isset ( $select_params [2] )) {
			$cnt_str = " $select_params[0],' ',$select_params[1],' ','(',$select_params[2],')' ";
		} else {
			$cnt_str = " $select_params[0],' (',$select_params[1],')' ";
		}
		$select = "concat($cnt_str) as $select_params[0] ";
		$drp_array = $this->CI->db_model->getSelect ( $select, $table, $where );
		$drp_array = $drp_array->result ();
		if (isset ( $drp_array [0] )) {
			return $drp_array [0]->{$select_params [0]};
		}
	}

	/*
	 * Change invoice_total to invoice details
	 */
	function get_invoice_total($select = '', $table = '', $id) {
		$where_arr = array (
				'invoiceid' => $id,
				'item_type <>' => "FREE"
		);
		$this->CI->db->where ( $where_arr );
		$this->CI->db->select ( '*' );
		$result = $this->CI->db->get ( 'invoice_details' );
		if ($result->num_rows () > 0) {
			$result = $result->result_array ();
			if ($select == 'debit') {
				if ($result [0] ['item_type'] == 'POSTCHARG') {
					return $this->convert_to_currency ( '', '', $result [0] ['debit'] );
				} else {
					return $this->convert_to_currency ( '', '', $result [0] ['credit'] );
				}
			} else {
				return $result [0] [$select];
			}
		} else {
			return null;
		}
	}
	function get_array($select, $table_name, $where = false) {
		$new_array = array ();
		$select_params = array ();
		$select_params = explode ( ",", $select );
		if (isset ( $select_params [3] )) {
			$cnt_str = " $select_params[1],'(',$select_params[2],' ',$select_params[3],')' ";
			$select = "concat($cnt_str) as $select_params[3] ";
			$field_name = $select_params [3];
		} elseif (isset ( $select_params [2] )) {
			$cnt_str = " $select_params[1],' ','(',$select_params[2],')' ";
			$select = "concat($cnt_str) as $select_params[2] ";
			$field_name = $select_params [2];
		} else {
			$select = $select_params [1];
			$field_name = $select_params [1];
		}
		if ($where) {
			$this->CI->db->where ( $where );
		}
		$this->CI->db->select ( "$select_params[0],$select", false );
		$result = $this->CI->db->get ( $table_name );
		foreach ( $result->result_array () as $key => $value ) {
			$new_array [$value [$select_params [0]]] = $value [$field_name];
		}
		ksort ( $new_array );
		return $new_array;
	}
	function get_timezone_offset() {
		$gmtoffset = 0;
		$accountinfo = $this->CI->session->userdata ( 'accountinfo' );
		$account_result = $this->CI->db->get_where ( 'accounts', array (
				'id' => $accountinfo ['id']
		) );
		$account_result = $account_result->result_array ();
		$accountinfo = $account_result [0];
		$timezone_id_arr = array (
				$accountinfo ['timezone_id']
		);
		$this->CI->db->where_in ( 'id', $timezone_id_arr );
		$this->CI->db->select ( 'gmtoffset' );
		$this->CI->db->from ( 'timezone' );
		$timezone_result = $this->CI->db->get ();
		if ($timezone_result->num_rows () > 0) {

			$timezone_result = $timezone_result->result_array ();
			foreach ( $timezone_result as $data ) {
				$gmtoffset += $data ['gmtoffset'];
			}
		}
		// echo $gmtoffset;exit;
		return $gmtoffset;
	}
	/**
	 * Version 2.1
	 * Purpose : Set default data for new created profile
	 */
	function sip_profile_date() {
		$defualt_profile_data = '{"rtp-ip":"$${local_ip_v4}","dialplan":"XML","user-agent-string":"ASTPP","debug":"0","sip-trace":"no","tls":"false","inbound-reg-force-matching-username":"true","disable-transcoding":"true","all-reg-options-ping":"false","unregister-on-options-fail":"true","log-auth-failures":"true","status":"0","inbound-bypass-media":"false","inbound-proxy-media":"false","disable-transfer":"true","enable-100rel":"false","rtp-timeout-sec":"60","dtmf-duration":"2000","manual-redirect":"false","aggressive-nat-detection":"false","enable-timer":"false","minimum-session-expires":"120","session-timeout-pt":"1800","auth-calls":"true","apply-inbound-acl":"default","inbound-codec-prefs":"PCMU,PCMA,G729","outbound-codec-prefs":"PCMU,PCMA,G729","inbound-late-negotiation":"false"}';
		return $defualt_profile_data;
	}

	/*
	 * **
	 * Refill coupon dropdown
	 * **
	 */
	function set_refill_coupon_status($select = '', $table = '', $status = '') {
		$refill_coupon_array = array (
				"" => gettext ( "--Select--" ),
				'2' => gettext ( 'Yes' ),
				'0' => gettext ( 'No' )
		);
		return $refill_coupon_array;
	}
	function get_refill_coupon_status($select = '', $table = '', $status) {
		$refill_coupon_array = array (
				'0' => gettext ( 'Inactive' ),
				'1' => gettext ( 'Active' ),
				'2' => gettext ( 'Inuse' ),
				"3" => gettext ( "Expired" )
		);
		return $refill_coupon_array [$status];
	}
	function firstused_check($select = '', $table = '', $status) {
		if ($status == '0000-00-00 00:00:00') {
			return '-';
		}
		return $status;
	}
	function get_refill_coupon_used($select = '', $table = '', $status) {
		return $status ['status'] == 2 ? '<img src= "' . base_url () . 'assets/images/true.png" style="height:20px;width:20px;" title="Yes">' : '<img src= "' . base_url () . '/assets/images/false.png" style="height:20px;width:20px;" title="No">';
	}

	/*
	 * *******
	 * Password encode decode
	 * *******
	 */

	/**
	 *
	 * @param string $string
	 */
	function encode_params($string) {
		$data = base64_encode ( $string );
		$data = str_replace ( array (
				'+',
				'/',
				'='
		), array (
				'-',
				'$',
				''
		), $data );
		return $data;
	}

	/**
	 *
	 * @param string $value
	 */
	function encode($value) {
		$text = $value;
		$iv_size = mcrypt_get_iv_size ( MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB );
		$iv = mcrypt_create_iv ( $iv_size, MCRYPT_RAND );
		$crypttext = mcrypt_encrypt ( MCRYPT_RIJNDAEL_256, $this->CI->config->item ( 'private_key' ), $text, MCRYPT_MODE_ECB, $iv );
		return trim ( $this->encode_params ( $crypttext ) );
	}
	function decode_params($string) {
		$data = str_replace ( array (
				'-',
				'$'
		), array (
				'+',
				'/'
		), $string );
		$mod4 = strlen ( $data ) % 4;
		if ($mod4) {
			$data .= substr ( '====', $mod4 );
		}
		return base64_decode ( $data );
	}
	function decode($value) {
		$crypttext = $this->decode_params ( $value );
		$iv_size = mcrypt_get_iv_size ( MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB );
		$iv = mcrypt_create_iv ( $iv_size, MCRYPT_RAND );
		$decrypttext = mcrypt_decrypt ( MCRYPT_RIJNDAEL_256, $this->CI->config->item ( 'private_key' ), $crypttext, MCRYPT_MODE_ECB, $iv );
		return trim ( $decrypttext );
	}

	/**
	 * ****
	 * Recording enable/disable dropdown
	 * **
	 */
	function set_recording($status = '') {
		$status_array = array (
				'0' => 'On',
				'1' => 'Off'
		);
		return $status_array;
	}

	/* * ************************** */
	function email_status($select = "", $table = "", $status) {
		$status = ($status ['status'] == 0) ? "Sent" : "Not Sent";
		return $status;
	}
	function email_search_status($select = '') {
		$status_array = array (
				"" => gettext ( "--Select--" ),
				"0" => gettext ( "Sent" ),
				"1" => gettext ( "Not Sent" )
		);
		return $status_array;
	}

	/* ===================================================================== */

	/*
	 * Purpose : Add following for setting page
	 * Version 2.1
	 */
	function paypal_status($status = '') {
		$status_array = array (
				'0' => gettext ( 'Enable' ),
				'1' => gettext ( 'Disable' )
		);
		return $status_array;
	}
	function playback_audio_notification($status = '') {
		$status_array = array (
				'0' => gettext ( 'Enable' ),
				'1' => gettext ( 'Disable' )
		);
		return $status_array;
	}
	function custom_status($status='') {
		$status_array = array (
				'0' => gettext ( 'Yes' ),
				'1' => gettext ( 'No' )
		);
		return $status_array;
	}
	function custom_status_recording($status='') {
		$status_array = array (
				'1' => gettext ( 'No' ),
				'0' => gettext ( 'Yes' )
		);
		return $status_array;
	}
	function custom_status_active($status) {
		$status_array = array (
				'0' => gettext ( 'Active' ),
				'1' => gettext ( 'InActive' )
		);
		return $status_array;
	}
	function custom_status_true($status) {
		$status_array = array (
				'0' => gettext ( 'TRUE' ),
				'1' => gettext ( 'FALSE' )
		);
		return $status_array;
	}
	function custom_status_voicemail($status) {
		$status_array = array (
				'true' => gettext ( 'True' ),
				'false' => gettext ( 'False' )
		);
		return $status_array;
	}

	/**
	 * ****
	 * For enable Signup module
	 */
	function create_sipdevice($status = '') {
		$status_array = array (
				'0' => gettext ( 'Enable' ),
				'1' => gettext ( 'Disable' )
		);
		return $status_array;
	}
	function enable_signup($status = '') {
		$status_array = array (
				'0' => gettext ( 'Enable' ),
				'1' => gettext ( 'Disable' )
		);
		return $status_array;
	}
	function balance_announce($status = '') {
		$status_array = array (
				'0' => gettext ( 'Enable' ),
				'1' => gettext ( 'Disable' )
		);
		return $status_array;
	}
	function minutes_announce($status = '') {
		$status_array = array (
				'0' => gettext ( 'Enable' ),
				'1' => gettext ( 'Disable' )
		);
		return $status_array;
	}
	function enable_disable_option() {
		$option_array = array (
				'0' => gettext ( 'Enable' ),
				'1' => gettext ( 'Disable' )
		);
		return $option_array;
	}
	function paypal_mode($status = '') {
		$status_array = array (
				'0' => gettext ( 'Live' ),
				'1' => gettext ( 'Sandbox' )
		);
		return $status_array;
	}
	function paypal_fee($status = '') {
		$status_array = array (
				'0' => gettext ( 'Paid By Admin' ),
				'1' => gettext ( 'Paid By Customer' )
		);
		return $status_array;
	}
	function email() {
		$status_array = array (
				'1' => gettext ( 'Enable' ),
				'0' => gettext ( 'Disable' )
		);
		return $status_array;
	}
	function smtp() {
		return $this->set_allow ();
	}
	function debug() {
		$status_array = array (
				'1' => gettext ( 'Enable' ),
				'0' => gettext ( 'Disable' )
		);
		return $status_array;
	}
	function default_signup_rategroup() {
		$this->CI->db->select ( "id,name" );
		$this->CI->db->where ( "status", 0 );
		$this->CI->db->where ( "reseller_id", 0 );
		$pricelist_result = $this->CI->db->get ( "pricelists" )->result_array ();
		$pricelist_arr = array ();
		foreach ( $pricelist_result as $result ) {
			$pricelist_arr [$result ['id']] = $result ['name'];
		}
		return $pricelist_arr;
	}
	/**
	 * ****
	 * Enable Fax feature
	 */
	function outbound_fax() {
		$status_array = array (
				'0' => gettext ( 'Enable' ),
				'1' => gettext ( 'Disable' )
		);
		return $status_array;
	}
	function inbound_fax() {
		$status_array = array (
				'0' => gettext ( 'Enable' ),
				'1' => gettext ( 'Disable' )
		);
		return $status_array;
	}
	function opensips() {
		$status_array = array (
				'1' => gettext ( 'Enable' ),
				'0' => gettext ( 'Disable' )
		);
		return $status_array;
	}
	function cc_ani_auth() {
		$status_array = array (
				'1' => gettext ( 'Enable' ),
				'0' => gettext ( 'Disable' )
		);
		return $status_array;
	}
	function calling_cards_balance_announce() {
		$status_array = array (
				'1' => gettext ( 'Enable' ),
				'0' => gettext ( 'Disable' )
		);
		return $status_array;
	}
	function calling_cards_timelimit_announce() {
		$status_array = array (
				'1' => gettext ( 'Enable' ),
				'0' => gettext ( 'Disable' )
		);
		return $status_array;
	}
	function calling_cards_rate_announce() {
		$status_array = array (
				'1' => gettext ( 'Enable' ),
				'0' => gettext ( 'Disable' )
		);
		return $status_array;
	}
	function startingdigit() {
		$status_array = array (
				'1' => gettext ( 'Enable' ),
				'0' => gettext ( 'Disable' )
		);
		return $status_array;
	}
	function SMPT() {
		$status_array = array (
				'1' => gettext ( 'Enable' ),
				'0' => gettext ( 'Disable' )
		);
		return $status_array;
	}
	function country() {
		return $this->CI->common_model->get_country_list ();
	}
	function default_timezone() {
		return $this->CI->db_model->build_dropdown ( 'id,gmtzone', 'timezone' );
	}
	function timezone() {
		return $this->CI->db_model->build_dropdown ( 'gmttime,gmttime', 'timezone' );
	}
	function base_currency() {
		return $this->CI->db_model->build_dropdown ( 'currency,currencyname', 'currency' );
	}
	function automatic_invoice() {
		$status_array = array (
				'0' => gettext ( 'Confirmed' ),
				'1' => gettext ( 'Draft' )
		);
		return $status_array;
	}
	/**
	 * ****
	 * Calculate Currency manually.
	 */
	function calculate_currency_manually($currency_info, $amount, $show_currency_flag = true, $number_format = true) {
		$decimal_points = $currency_info ['decimalpoints'];
		$system_currency_rate = $currency_info ['base_currency'] ['currencyrate'];
		$user_currency_rate = $currency_info ['user_currency'] ['currencyrate'];
		$calculated_amount = ( float ) (($amount * $currency_info ['user_currency'] ['currencyrate']) / $currency_info ['base_currency'] ['currencyrate']);
		if ($number_format) {
			$calculated_amount = number_format ( $calculated_amount, $currency_info ['decimalpoints'] );
		}
		if ($show_currency_flag) {
			return $calculated_amount . " " . $currency_info ['user_currency'] ['currency'];
		} else {
			return $calculated_amount;
		}
	}

	/*
	 * Using By Summary Report search
	 */
	function search_report_in($select = '') {
		$status_array = array (
				"minutes" => "Minutes",
				"seconds" => "Seconds"
		);
		return $status_array;
	}
	function set_summarycustomer_groupby($status = '') {
		$status_array = array (
				'' => gettext ( "--Select--" ),
				'accountid' => gettext ( 'Account' ),
				'pattern' => gettext ( 'Code' ),
				'package_id' => gettext ( "Package" )
		);
		return $status_array;
	}
	function set_summaryprovider_groupby($status = '') {
		$status_array = array (
				'' => gettext ( "--Select--" ),
				'provider_id' => gettext ( 'Account' ),
				'trunk_id' => gettext ( "Trunks" ),
				'pattern' => gettext ( 'Code' )
		);
		return $status_array;
	}
	function get_currency_info() {
		// System Currency info
		$base_currency = Common_model::$global_config ['system_config'] ['base_currency'];
		// Get Account Information from session to get currency_id
		$accountinfo = $this->CI->session->userdata ( 'accountinfo' );
		// Get User Currency id
		$user_currency_id = $accountinfo ['currency_id'] > 0 ? $accountinfo ['currency_id'] : $base_currency;
		$where = "currency = '" . $base_currency . "' OR id= " . $user_currency_id;
		$this->CI->db->where ( $where );
		$this->CI->db->select ( '*' );
		$currency_result = $this->CI->db->get ( 'currency' );

		if ($currency_result->num_rows () == 2) {
			$currency_result = $currency_result->result_array ();
			foreach ( $currency_result as $key => $records ) {
				// User Currency is currency details of logged in user.
				if ($records ['id'] == $user_currency_id) {
					$currency_info ['user_currency'] = $records;
				}
				// System Currency is currency details of system.
				if ($records ['currency'] == Common_model::$global_config ['system_config'] ['base_currency']) {
					$currency_info ['base_currency'] = $records;
				}
			}
		} else if ($currency_result->num_rows () == 1) {
			$currency_info ['user_currency'] = $currency_info ['base_currency'] = ( array ) $currency_result->first_row ();
		}
		// Get Decimal points as per defined from system.
		$currency_info ['decimalpoints'] = Common_model::$global_config ['system_config'] ['decimalpoints'];
		return $currency_info;
	}
	function convert_to_show_in($search_name = "", $table = "", $second) {
		$search_arr = $this->CI->session->userdata ( $search_name );
		$show_seconds = (! empty ( $search_arr ['search_in'] )) ? $search_arr ['search_in'] : 'minutes';
		return ($show_seconds === 'minutes') ? ($second > 0) ? sprintf ( '%02d', $second / 60 ) . ":" . sprintf ( '%02d', ($second % 60) ) : "00:00" : sprintf ( '%02d', $second );
	}
	function array_column($input, $columnKey, $indexKey = null) {
		$array = array ();
		foreach ( $input as $value ) {
			if (! isset ( $value [$columnKey] )) {
				trigger_error ( "Key \"$columnKey\" does not exist in array" );
				return false;
			}

			if (is_null ( $indexKey )) {
				$array [] = $value [$columnKey];
			} else {
				if (! isset ( $value [$indexKey] )) {
					trigger_error ( "Key \"$indexKey\" does not exist in array" );
					return false;
				}
				if (! is_scalar ( $value [$indexKey] )) {
					trigger_error ( "Key \"$indexKey\" does not contain scalar value" );
					return false;
				}
				$array [$value [$indexKey]] = $value [$columnKey];
			}
		}

		return $array;
	}
	function group_by_time() {
		$status_array = array (
				'' => gettext ( "--Select--" ),
				'HOUR' => gettext ( 'Hour' ),
				'DAY' => gettext ( "Day" ),
				'MONTH' => gettext ( 'Month' ),
				"YEAR" => gettext ( "Year" )
		);
		return $status_array;
	}
	function currency_decimal($amount) {
		$amount = str_replace ( ',', '', $amount );
		$decimal_amount = Common_model::$global_config ['system_config'] ['decimalpoints'];
		$number_convert = number_format ( ( float ) $amount, $decimal_amount, '.', '' );
		return $number_convert;
	}
	function add_invoice_details($account_arr, $charge_type, $amount, $description) {		
		$accountinfo = $this->CI->session->userdata ( 'accountinfo' );
		//$reseller_id = $accountinfo ['type'] == 1 ? $accountinfo ['id'] : 1;

		//Get company profile information from invoice_conf table
		$reseller_id = ($account_arr['reseller_id'] > 0)?$account_arr['reseller_id']:1;
		$where = "accountid IN ('" . $reseller_id . "')";
		$this->CI->db->where ( $where );
		$this->CI->db->select ( '*' );
		$this->CI->db->order_by ( 'accountid', 'desc' );
		$this->CI->db->limit ( 1 );
		$invoiceconf = $this->CI->db->get ( 'invoice_conf' );
		$invoice_conf = ( array ) $invoiceconf->first_row ();

		//Get last invoice id
		$last_invoiceid = $this->get_invoice_date ( 'invoiceid', '', $account_arr ['reseller_id'] );

		if ($last_invoiceid && $last_invoiceid > 0) {
			$last_invoiceid = ($last_invoiceid + 1);			
			if ($last_invoiceid < $invoice_conf ['invoice_start_from'])
					$last_invoiceid = $invoice_conf ['invoice_start_from'];	
		} else {
			$last_invoiceid = $invoice_conf ['invoice_start_from'];
		}
		$last_invoiceid = str_pad ( $last_invoiceid, 6 , '0', STR_PAD_LEFT );
		$invoice_prefix = $invoice_conf ['invoice_prefix'];
		
		//Calculate due date
		$due_date = $invoice_conf ['interval']  > 0 ? date ( "Y-m-d H:i:s", strtotime ( gmdate ( "Y-m-d H:i:s" ) . " +" . $invoice_conf ['interval'] . " days" ) ) : gmdate ( "Y-m-d H:i:s" );

		//Generate receipt
		$invoiceid = $account_arr ['posttoexternal'] == 0 ? $this->CI->common_model->generate_receipt ( $account_arr ['id'], $amount, $account_arr, $last_invoiceid, $invoice_prefix, $due_date ) : 0;

		//Get balance
		$balance = ($account_arr ['posttoexternal'] == 1) ? $account_arr ['credit_limit'] - $account_arr ['balance'] : $account_arr ['balance'];

		//Generate receipt detail by inserting information in invoice_details table
		$insert_arr = array (
				"accountid" => $account_arr ['id'],
				"description" => $description,
				"debit" => $amount,
				"credit" => '0',
				"created_date" => gmdate ( "Y-m-d H:i:s" ),
				"invoiceid" => $invoiceid,
				"reseller_id" => $account_arr ['reseller_id'],
				"item_type" => $charge_type,
				"item_id" => '0',
				"before_balance" => $balance,
				"after_balance" => $balance - $amount
		);
		$this->CI->db->insert ( "invoice_details", $insert_arr );
		return true;
	}
	/*
	 * ASTPP 3.0 Remove all information related to going to delete customer.
	 */
	function customer_delete_dependencies($id,$entity=0) {
		$this->delete_data ( 'ani_map', array (
				'accountid' => $id
		) );
		$this->delete_data ( 'block_patterns', array (
				'accountid' => $id
		) );
		$this->delete_data ( 'charge_to_account', array (
				'accountid' => $id
		) );
		$this->delete_data ( 'counters', array (
				'accountid' => $id
		) );
		$this->delete_data ( 'ip_map', array (
				'accountid' => $id
		) );
		$this->delete_data ( 'sip_devices', array (
				'accountid' => $id
		) );
		$this->delete_data ( 'speed_dial', array (
				'accountid' => $id
		) );
		$this->delete_data ( 'taxes_to_accounts', array (
				'accountid' => $id
		) );
		$this->delete_data ( 'mail_details', array (
				'accountid' => $id
		) );
		$this->update_data ( 'dids', array (
				"accountid" => $id
		), array (
				'accountid' => 0
		) );
		$this->update_data ( "accounts", array (
				"id" => $id
		), array (
				"deleted" => 1
		) );

		//If provider deleted then disable their trunks. 
		if ($entity == 3)
		{
			$this->update_data ( "trunks", array (
					"provider_id" => $id
			), array (
					"status" => 1
			) );
		}
	}
	/*
	 * ASTPP 3.0
	 * Remove all information related to going to delete reseller.
	 */
	function reseller_delete_dependencies($id) {
		$accountinfo = $this->CI->session->userdata ( 'accountinfo' );
		$child_arr = $this->select_data ( "accounts", array (
				"reseller_id" => $id,
				"type" => 0
		), 'id,reseller_id' );
		if ($child_arr) {
			foreach ( $child_arr as $value ) {
				$this->customer_delete_dependencies ( $value ['id'] );
			}
		}
		$package_arr = $this->select_data ( "packages", array (
				"reseller_id" => $id
		), 'id' );
		if ($package_arr) {
			foreach ( $package_arr as $value ) {
				$this->delete_data ( 'package_patterns', array (
						"id" => $value ['id']
				) );
			}
		}
		$acc_arr = $this->select_data ( 'accounts', array (
				"id" => $id
		), 'id,reseller_id' );
		$parent_id = 0;
		if ($acc_arr) {
			$parent_id = $acc_arr [0] ['reseller_id'];
		}
		$pricelist_arr = $this->select_data ( 'pricelists', array (
				'reseller_id' => $id
		), 'id' );
		if ($pricelist_arr) {
			foreach ( $pricelist_arr as $value ) {
				$this->delete_data ( "routing", array (
						"pricelist_id" => $value ['id']
				) );
				$this->delete_data ( "routes", array (
						"pricelist_id" => $value ['id']
				) );
				$this->update_data ( 'pricelists', array (
						'id' => $value ['id']
				), array (
						'status' => 2
				) );
			}
		}
		$charge_arr = $this->select_data ( 'charges', array (
				'reseller_id' => $id
		), 'id' );
		if ($charge_arr) {
			foreach ( $charge_arr as $value ) {
				$this->delete_data ( "charge_to_account", array (
						"charge_id" => $value ['id']
				) );
			}
		}
		$this->delete_data ( 'charges', array (
				'reseller_id' => $id
		) );
		$this->update_data ( 'dids', array (
				'parent_id' => $id
		), array (
				"parent_id" => $parent_id
		) );
		$this->delete_data ( 'reseller_pricing', array (
				"reseller_id" => $id
		) );
		$this->delete_data ( 'refill_coupon', array (
				"reseller_id" => $id
		) );
		$this->delete_data ( 'mail_details', array (
				'accountid' => $id
		) );
		$taxes_arr = $this->select_data ( 'taxes', array (
				"reseller_id" => $id
		), 'id' );
		if ($taxes_arr) {
			foreach ( $taxes_arr as $value ) {
				$this->delete_data ( "taxes_to_accounts", array (
						"taxes_id" => $value ['id']
				) );
			}
		}
		$this->delete_data ( 'taxes', array (
				"reseller_id" => $id
		) );
		$this->delete_data ( 'default_templates', array (
				"reseller_id" => $id
		) );
		$package_arr = $this->select_data ( 'packages', array (
				'reseller_id' => $id
		), 'id' );
		if ($package_arr) {
			$this->delete_data ( "counters", array (
					"package_id" => $value ['id']
			) );
			$this->delete_data ( "package_patterns", array (
					"package_id" => $value ['id']
			) );
		}
		$this->delete_data ( 'invoice_conf', array (
				'accountid' => $id
		) );
		$this->delete_data ( 'packages', array (
				"reseller_id" => $id
		) );
		$this->update_data ( 'accounts', array (
				"id" => $id
		), array (
				"deleted" => 1
		) );
	}
	function subreseller_list($parent_id = '') {
		$customer_id = $parent_id;
		$this->reseller_delete_dependencies ( $parent_id );
		$query = 'select id,type from accounts where reseller_id = ' . $parent_id . ' AND deleted =0';
		$result = $this->CI->db->query ( $query );
		if ($result->num_rows () > 0) {
			$result = $result->result_array ();
			foreach ( $result as $data ) {
				if ($data ['type'] == 1) {
					$this->reseller_delete_dependencies ( $data ['id'] );
					$this->subreseller_list ( $data ['id'] );
				} else {
					$this->customer_delete_dependencies ( $data ['id'] );
				}
			}
		}
	}

	/**
	 *
	 * @param string $table_name
	 */
	function delete_data($table_name, $where_arr) {
		$this->CI->db->where ( $where_arr );
		if ($table_name == 'accounts') {
			$this->CI->db->where ( 'type <>', '-1' );
		}
		$this->CI->db->delete ( $table_name );
	}

	/**
	 *
	 * @param string $table_name
	 */
	function update_data($table_name, $where_arr, $update_arr) {
		$this->CI->db->where ( $where_arr );
		if ($table_name == 'accounts') {
			$this->CI->db->where ( 'type <>', '-1' );
		}
		$this->CI->db->update ( $table_name, $update_arr );
	}

	/**
	 *
	 * @param string $table_name
	 * @param string $select
	 */
	function select_data($table_name, $where_arr, $select) {
		$this->CI->db->where ( $where_arr );
		$this->CI->db->select ( $select );
		$result = $this->CI->db->get ( $table_name );
		if ($result->num_rows () > 0) {
			return $result->result_array ();
		} else {
			return false;
		}
	}
	function set_call_waiting($status = '') {
		$status_array = array (
				'0' => 'Enable',
				'1' => 'Disable'
		);
		return $status_array;
	}
	function get_call_waiting($select = "", $table = "", $status) {
		return ($status == 0) ? "Enable" : "Disable";
	}
	function set_invoice_details($select = '') {
		$status_array = array (
				"invoice_select" => gettext ( "--Select--" ),
				"invoice_inactive" => gettext ( "Deleted Invoices" ),
				"invoice_active" => gettext ( "All Invoices" )
		);
		return $status_array;
	}
	// /*ASTPP_invoice_changes_05_05_start*/
	function get_invoice_template($invoicedata, $accountdata, $flag) {
		// echo "<pre>";print_r($invoicedata);exit;
		$login_info = $this->CI->session->userdata ( 'accountinfo' );
		$invoice_conf_res = array ();
		$reseller_id = ($accountdata ['reseller_id'] == 0) ? 1 : $accountdata ['reseller_id'];
		$where = "accountid IN ('" . $reseller_id . "','1')";
		$this->CI->db->select ( '*' );
		$this->CI->db->where ( $where );
		$this->CI->db->order_by ( 'accountid', 'desc' );
		$this->CI->db->limit ( 1 );
		$invoice_conf = $this->CI->db->get ( 'invoice_conf' );
		$invoice_conf_res = ( array ) $invoice_conf->first_row ();

		$accountdata ["currency_id"] = $this->get_field_name ( 'currency', 'currency', $accountdata ["currency_id"] );
		$accountdata ["country"] = $this->get_field_name ( 'country', 'countrycode', $accountdata ["country_id"] );
		$data ["to_currency"] = Common_model::$global_config ['system_config'] ['base_currency'];
		if ($login_info ['type'] == - 1) {
			$currency = $data ["to_currency"];
		} elseif ($login_info ['type'] == 1) {
			$accountdata ["currency_id"] = $this->get_field_name ( 'currency', 'currency', $login_info ["currency_id"] );
			$currency = $accountdata ["currency_id"];
		} else {
			$currency = $accountdata ["currency_id"];
		}
		$decimal_amount = Common_model::$global_config ['system_config'] ['decimalpoints'];
		ob_start ();
		$this->CI->load->library ( '/html2pdf/html2pdf' );
		$this->CI->html2pdf = new HTML2PDF ( 'P', 'A4', 'en' );
		$this->CI->html2pdf->pdf->SetDisplayMode ( 'fullpage' );
		$template_config = $this->CI->config->item ( 'invoice_template' );
		include ($template_config . 'invoice_template.php');
		$content = ob_get_clean ();
		ob_clean ();
		$INVOICE_NUM = '';

		$ACCOUNTADD = '';
		$ACCOUNTADD_CUSTOMER = '';
		$INVOICE_DETAIL = '';
		$INVOICE_CHARGE = '';
		$total_sum = 0;
		$total_vat = 0;
		$fromdate = strtotime ( $invoicedata ['from_date'] );
		$from_date = date ( "Y-m-d", $fromdate );
		$duedate = strtotime ( $invoicedata ['due_date'] );
		$due_date = date ( "Y-m-d", $duedate );
		/**
		 * ****************************Invoivce number*************************************************************
		 */
		$INVOICE_NUM = '<td style="font-size: 10px;color:#000;font-family:arial; line-height: 22px;letter-spacing:02;float:right;"><b><h2>INVOICE : ' . $invoicedata ["invoice_prefix"] . $invoicedata ["invoiceid"] . '</h2></b></td>';
		/**
		 * ************************* Company Address Code START **************************************************
		 */
		$ACCOUNTADD .= '<tr><td style="width:100%;font-size: 12px;color:#000;font-family:arial; line-height: 22px;">' . $invoice_conf_res ['company_name'] . '</td>';
		$ACCOUNTADD .= '</tr>';
		$ACCOUNTADD .= '<tr><td style="width:100%;font-size: 12px;color:#000;font-family:arial; line-height: 22px;">' . $invoice_conf_res ['address'] . '</td></tr>';
		$ACCOUNTADD .= '<tr><td style="width:100%;font-size: 12px;color:#000;font-family:arial; line-height: 22px;">' . $invoice_conf_res ['city']. ' - ' .$invoice_conf_res ['zipcode']. '</td></tr>';
		$ACCOUNTADD .= '<tr><td style="width:100%;font-size: 12px;color:#000;font-family:arial; line-height: 22px;">' . $invoice_conf_res ['province'] . ', ' .$invoice_conf_res ['country'].'</td></tr>';
		$ACCOUNTADD .= '<tr><td style="width:100%;font-size: 12px;color:#000;font-family:arial; line-height: 22px;">' . $invoice_conf_res ['invoice_taxes_number'] . '</td></tr>';
		/**
		 * ************************* Company Address Code END **************************************************
		 */

		/**
		 * ************************* Customer Address Code START **************************************************
		 */
		$ACCOUNTADD_CUSTOMER .= '<table align=right>';
		$ACCOUNTADD_CUSTOMER .= '<tr>';
		$ACCOUNTADD_CUSTOMER .= '<td align="right" style="width:100%;font-size: 12px;color:#000;font-family:arial; line-height: 22px;">' . $accountdata ['company_name'] . '</td></tr>';
		$ACCOUNTADD_CUSTOMER .= '<tr><td align="right" style="width:100%;font-size: 12px;color:#000;font-family:arial; line-height: 22px;">' . $accountdata ['address_1'] . '</td></tr>';
		$ACCOUNTADD_CUSTOMER .= '<tr><td align="right" style="width:100%;font-size: 12px;color:#000;font-family:arial; line-height: 22px;">' . $accountdata ['city'] . ' - ' .$accountdata ['postal_code'].'</td></tr>';
		$ACCOUNTADD_CUSTOMER .= '<tr><td align="right" style="width:100%;font-size: 12px;color:#000;font-family:arial; line-height: 22px;">' . $accountdata ['province'] . ', ' .$accountdata ['country'].'</td></tr>';
		$ACCOUNTADD_CUSTOMER .= '<tr><td style="width:100%;font-size: 12px;color:#000;font-family:arial; line-height: 22px;">' . $accountdata ['tax_number'] . '</td></tr>';
		$ACCOUNTADD_CUSTOMER .= "</table>";
		/**
		 * ************************* Customer Address Code END **************************************************
		 */
		/**
		 * *************************Invoice detail START *******************************************************
		 */
		$recharge_amount = $invoice_details = $this->CI->db_model->getSelect ( 'sum(credit) as credit', 'invoice_details', array (
				"invoiceid" => $invoicedata ['id'],
				'item_type' => 'INVPAY'
		) )->result_array ();

		$recharge_amount = isset ( $recharge_amount [0] ['credit'] ) ? $recharge_amount [0] ['credit'] : '0';
		$total_amount = $invoicedata ['amount'] - $recharge_amount;
		$INVOICE_DETAIL .= '<tr>
                                <td style="width:100%;">
                                <table style="width:100%;" cellspacing="0">
                                <tbody><tr>

                                <td style="width:25%;"><b>Invoice Date :</b></td>
                                <td style="width:25%;text-align:right;border-right:1px solid #000; padding-right: 3px;">' . date ( "Y-m-d", strtotime ( $invoicedata ['invoice_date'] ) ) . '</td>
                                <td style="width:25%;padding-left: 5px;"><b>This Invoice Amount :</b> </td>
                                <td style="width:25%;text-align:right;">' . $this->currency_decimal ( $this->CI->common_model->calculate_currency ( $invoicedata ['amount'] ) ) . '</td>

                                </tr>
                                <tr> <td style="width:25%;"></td><td style="width:25%;border-right:1px solid #000;"></td></tr>
                                <tr>
                                 <td style="width:25%;"><b>Invoice Due Date :</b></td>
                                <td style="width:25%;text-align:right;border-right:1px solid #000;padding-right: 3px;">' . $due_date . '</td>
                                 <td style="width:25%;padding-left: 5px;"><b>Recharge Payments :</b> </td>
                                <td style="width:25%;text-align:right;">' . $this->currency_decimal ( $this->CI->common_model->calculate_currency ( $recharge_amount ) ) . '</td>

                                </tr>
                                  <tr> <td style="width:25%;"></td><td style="width:25%;border-right:1px solid #000;"></td></tr>
                                <tr>
                                <td style="width:25%;border-bottom:1px solid #000;"><b>Account Number :</b></td>
                                <td style="width:25%;border-bottom:1px solid #000;text-align:right;border-right:1px solid #000;padding-right: 3px;">' . $accountdata ['number'] . '</td>
                                <td colspan="2" style="width:50%;padding-left: 3px;">
                                <table style="border:1px solid #000;width:100%;">
                                <tbody><tr>
                                <td style="width:50%;font-weight:bold;font-style:italic;">Total Amount :</td>
                                <td style="width:50%;text-align:right;font-style:italic;">' . $this->currency_decimal ( $this->CI->common_model->calculate_currency ( $total_amount ) ) . '</td>
                                </tr>
                                </tbody></table>
                                </td>
                                </tr>
                                </tbody></table>
                                </td>
                             </tr>';

		/**
		 * *************************Invoice detail END *******************************************************
		 */

		/**
		 * **************************charge History START *****************************************************
		 */

		$INVOICE_CHARGE .= '

                                        <tr><th style="width:20%;border-left:1px solid #000;border-top:1px solid #000;border-bottom:1px solid #000;background-color:#EBEEF2;padding:5px;">Date </th>
                                        <th style="width:40%;border-left:1px solid #000;border-top:1px solid #000;border-bottom:1px solid #000;background-color:#EBEEF2;padding:5px;">Description</th>

                                        <th style="width:15%;border-left:1px solid #000;border-top:1px solid #000;border-bottom:1px solid #000;background-color:#EBEEF2;padding:5px;">Unit Price (' . $currency . ')</th>
                                         <th style="width:10%;border-left:1px solid #000;border-top:1px solid #000;border-bottom:1px solid #000;background-color:#EBEEF2;padding:5px;">Quantity</th>
                                        <th style="width:15%;border-left:1px solid #000;border-right:1px solid #000;border-top:1px solid #000;border-bottom:1px solid #000;background-color:#EBEEF2;padding:5px;">Cost (' . $currency . ')</th>
                                        </tr>
                                        ';
		$this->CI->db->where ( 'item_type <>', 'INVPAY' );
		$invoice_details = $this->CI->db_model->getSelect ( '*', 'invoice_details', array (
				"invoiceid" => $invoicedata ['id'],
				'item_type <>' => 'TAX'
		) );
		$invoice_details = $invoice_details->result_array ();

		$invoice_tax = $this->CI->db_model->getSelect ( '*', 'invoice_details', array (
				"invoiceid" => $invoicedata ['id'],
				'item_type ' => 'TAX'
		) );

		foreach ( $invoice_details as $charge_res ) {

			// echo "<pre>";print_r($charge_res['description']);exit;
			$INVOICE_CHARGE .= '<tr >
                                                        <td style="width:20%;line-height:15px;border-left:1px solid #000;border-bottom:1px solid #000;padding:5px;">' . date ( 'Y-m-d', strtotime ( $charge_res ['created_date'] ) ) . '</td>
                                                        <td style="width:40%;line-height:15px;border-left:1px solid #000;border-bottom:1px solid #000;padding:5px;">' . $charge_res ['description'] . '</td>
                                                        <td style="width:15%;line-height:15px;border-left:1px solid #000;border-bottom:1px solid #000;text-align:right;padding:5px;">' . $this->currency_decimal ( $this->CI->common_model->calculate_currency ( $charge_res ['debit'] ) ) . '</td>
                                                         <td style="width:10%;line-height:15px;border-left:1px solid #000;border-bottom:1px solid #000;text-align:right;padding:5px;">'.$charge_res ['quantity'].'</td>
                                                        <td style="width:15%;line-height:15px;border-right:1px solid #000;border-left:1px solid #000;border-bottom:1px solid #000;text-align:right;padding:5px;">' . $this->currency_decimal ( $this->CI->common_model->calculate_currency ( $charge_res ['debit'] ) ) . '</td>
                                                     </tr>';
			$total_sum += $charge_res ['debit'];
		}

		$INVOICE_CHARGE .= '<tr>
                                                <td></td><td></td>
                                                <td colspan=2 style="border-left:1px solid #000;border-bottom:1px solid #000;width:20%;padding-left:5px;padding-top:5px;padding-bottom:5px;text-align:right;padding-right:5px;"><b>Sub Total</b></td>
                                                <td style="border-left:1px solid #000;border-bottom:1px solid #000;border-right:1px solid #000;width:10%;text-align:right;padding-top:5px;padding-right:5px;">' . $this->currency_decimal ( $this->CI->common_model->calculate_currency ( $total_sum ) ) . '</td>
                                                </tr>';

		if ($invoice_tax->num_rows () > 0) {
			foreach ( $invoice_tax->result_array () as $invoice_tax ) {
				$total_vat += $invoice_tax ['debit'];
				$total_vat = $this->currency_decimal ( $this->CI->common_model->calculate_currency ( $total_vat ) );
				$INVOICE_CHARGE .= '<tr>
                                                        <td></td><td></td>
                                                        <td colspan=2 style="border-left:1px solid #000;border-bottom:1px solid #000;width:20%;padding-left:5px;padding-top:5px;padding-bottom:5px;text-align:right;padding-right:5px;"><b>Tax (' . $invoice_tax ["description"] . ')</b></td>
                                                        <td style="border-left:1px solid #000;border-bottom:1px solid #000;border-right:1px solid #000;width:10%;text-align:right;padding-top:5px;padding-right:5px;">' . $this->currency_decimal ( $total_vat ) . '</td>
                                                        </tr>';
			}
		}
		$sub_total = $total_sum + $total_vat;
		$INVOICE_CHARGE .= '<tr>
                                                <td></td><td></td>
                                                <td colspan=2 style="border-left:1px solid #000;border-bottom:1px solid #000;width:20%;padding-left:5px;padding-top:5px;padding-bottom:5px;text-align:right;padding-right:5px;"><b>Total</b> </td>
                                                <td style="border-left:1px solid #000;border-bottom:1px solid #000;border-right:1px solid #000;width:10%;text-align:right;padding-top:5px;padding-right:5px;">' . $this->currency_decimal ( $sub_total ) . '</td>
                                                </tr>

                                               ';

		/**
		 * ****************************charge History END ****************************************************
		 */

		/**
		 * ************************* Invoice Note Code END **************************************************
		 */
		$invoice_notes = $this->CI->db_model->getSelect ( '*', 'invoices', array (
				'id' => $invoicedata ['id']
		) );
		$invoice_notes = $invoice_notes->result_array ();
		if (isset ( $invoice_notes [0] ['notes'] )) {
			$invoice_notes = $invoice_notes [0] ['notes'];
		} else {
			if ($invoice_notes [0] ['invoice_note'] == '0') {
				$invoice_notes = 'THIS IS A 30 DAY ACCOUNT, SO PLEASE MAKE PAYMENT WITHIN THESE TERMS';
			} else {
				$invoice_notes = $invoice_notes [0] ['invoice_note'];
			}
		}
		/**
		 * ************************* Invoice Note Code END **************************************************
		 */
//ASTPP_invoice_download_issue
		if (file_exists ( getcwd () . "/upload/" . $invoice_conf_res['accountid'] . "_" . $invoice_conf_res ['logo'] )) {
			if ($invoice_conf_res['logo'] != '') {
				$content = str_replace ( "<LOGO>", FCPATH . "upload/" . $invoice_conf_res ['accountid'] . "_" . $invoice_conf_res['logo'], $content );
			} else {
				$content = str_replace ( "<LOGO>", FCPATH . '/assets/images/logo.png', $content );
			}
		} else {
			$content = str_replace ( "<LOGO>", FCPATH . '/assets/images/logo.png', $content );
		}
//END

		$content = str_replace ( "<INVOICE_NUM>", $INVOICE_NUM, $content );
		$content = str_replace ( "<ACCOUNTADD>", $ACCOUNTADD, $content );
		$content = str_replace ( "<ACCOUNTADD_CUSTOMER>", $ACCOUNTADD_CUSTOMER, $content );
		$content = str_replace ( "<INVOICE_DETAIL>", $INVOICE_DETAIL, $content );
		$content = str_replace ( "<INVOICE_CHARGE>", $INVOICE_CHARGE, $content );
		$content = str_replace ( "<NOTES>", $invoice_notes, $content );

		// echo $content; exit;

		$invoice_path = $this->CI->config->item ( 'invoices_path' );
		$download_path = $invoice_path . $accountdata ["id"] . '/' . $invoicedata ['invoice_prefix'] . $invoicedata ['invoiceid'] . ".pdf";
		// echo $download_path; exit;
		$this->CI->html2pdf->pdf->SetDisplayMode ( 'fullpage' );
		$this->CI->html2pdf->writeHTML ( $content );

		if ($flag == 'TRUE') {
			$download_path = $invoicedata ['invoice_prefix'] . $invoicedata ['invoiceid'] . ".pdf";

			$this->CI->html2pdf->Output ( $download_path, "D" );
		} else {
			$current_dir = getcwd () . "/invoices/";
			$dir_name = $accountdata ["id"];
			if (! is_dir ( $current_dir . $dir_name )) {
				mkdir ( $current_dir . $dir_name, 0777, true );
				chmod ( $current_dir . $dir_name, 0777 );
			}
			$invoice_path = $this->CI->config->item ( 'invoices_path' );
			$download_path = $invoice_path . $accountdata ["id"] . '/' . $invoicedata ['invoice_prefix'] . $invoicedata ['invoiceid'] . ".pdf";
			$this->CI->html2pdf->Output ( $download_path, "F" );
		}
	}
	// end
	function reseller_select_value($select, $table, $id_where = '') {
		$select_params = explode ( ',', $select );
		$where = array (
				"1"
		);
		if ($id_where != '') {
			$where = array (
					"id" => $id_where
			);
		}
		$select_params = explode ( ',', $select );
		$cnt_str = " $select_params[0],' ',$select_params[1],' ','(',$select_params[2],')' ";
		$select = "concat($cnt_str) as $select_params[2] ";
		$drp_array = $this->CI->db_model->getSelect ( $select, $table, $where );
		$drp_array = $drp_array->result ();
		if (isset ( $drp_array [0] ))
			return $drp_array [0]->{$select_params [2]};
		else
			return 'Admin';
	}
	function get_subreseller_info($parent_id) {
		if (! empty ( $parent_id )) {
			$str = $parent_id . ",";
		} else {
			$str = null;
		}
		$query = "select id from accounts where reseller_id = '$parent_id' AND deleted =0 AND type=1";
		$result = $this->CI->db->query ( $query );
		if ($result->num_rows () > 0) {
			$result = $result->result_array ();
			foreach ( $result as $data ) {
				if (! empty ( $data ['id'] )) {
					$str .= $this->get_subreseller_info ( $data ['id'] );
				}
			}
		}
		return $str;
	}
	function get_parent_info($child_id, $parent_id) {
		if (! empty ( $child_id )) {
			$str = $child_id . ",";
		} else {
			$str = null;
		}
		if ($child_id > 0) {
			$query = "select reseller_id from accounts where id = '$child_id'";
			$result = $this->CI->db->query ( $query );
			if ($result->num_rows () > 0) {
				$parent_info = ( array ) $result->first_row ();
				if ($parent_info ['reseller_id'] != $parent_id) {
					$str .= $this->get_parent_info ( $parent_info ['reseller_id'], $parent_id );
				}
			}
		}
		return $str;
	}
	function get_did_accountid($select, $table, $where) {
		$accountinfo = $this->CI->session->userdata ( 'accountinfo' );
		$this->CI->db->where ( 'note', $where );
		$this->CI->db->where ( 'parent_id', $accountinfo ['id'] );
		$this->CI->db->select ( 'reseller_id' );
		$reseller_pricing_result = $this->CI->db->get ( 'reseller_pricing' );
		$account_info = ( array ) $reseller_pricing_result->first_row ();
		$account_name = $this->get_field_name_coma_new ( 'first_name,last_name,number', 'accounts', $account_info ['reseller_id'] );
		return $account_name;
	}
	function get_status_new($select = "", $table = "", $status = "") {
		return ($status ['status'] == 0) ? "<span class='label label-sm label-inverse arrowed-in' title='release'>Active<span>" : "<span class='label label-sm' title='release'>Inactive<span>";
	}

	
	// ASTPP_invoice_changes_05_05_start
    //@TODO : Make it common to use for receipt and invoice generate. 
	function Get_Invoice_configuration($AccountData) {
	    	    
		$InvoiceConf = array ();
		
		//Get company profile information from invoice_conf table
		$reseller_id = ($AccountData ['reseller_id'] == 0) ? 1 : $AccountData ['reseller_id'];
		$where = "accountid IN ('" . $reseller_id . "','1')";
		$this->CI->db->select ( '*' );
		$this->CI->db->where ( $where );
		$this->CI->db->order_by ( 'accountid', 'desc' );
		$this->CI->db->limit ( 1 );
		$InvoiceConf = $this->CI->db->get ( 'invoice_conf' );
		$InvoiceConf = ( array ) $InvoiceConf->first_row ();
		
		
		//$InvoiceConf ['invoice_prefix'] = str_pad ( $InvoiceConf ['invoice_prefix'], (strlen ( $InvoiceConf ['invoice_prefix'] ) + 4), '0', STR_PAD_RIGHT );

		return $InvoiceConf;
	}
	// END
	// Account Import mapper functions
	function csv_to_array($filename = '', $delimiter = ',') {
		if (! file_exists ( $filename ) || ! is_readable ( $filename ))
			return FALSE;
		$header = NULL;
		$data = array ();
		if (($handle = fopen ( $filename, 'r' )) !== FALSE) {
			while ( ($row = fgetcsv ( $handle, 1000, $delimiter )) !== FALSE ) {
				
				if (! $header)
					$header = $row;
				else{
					if(!empty($row)){
						$data [] = array_combine ( $header, $row );
					}	
				}	
			}
			
			fclose ( $handle );
		}
		
		return $data;
	}
	function utf8_converter($array) {
		array_walk_recursive ( $array, function (&$item, $key) {
			if (! mb_detect_encoding ( $item, 'utf-8', true )) {
				$item = utf8_encode ( $item );
			}
		} );
		return $array;
	}
	
	function get_value_by_option($value,$default_value){
		$options = array("Yes"=>0,"No"=>1);
		return isset($options[$value]) ?  ($options[$value]) : $options[$default_value];
	}
	function get_values(){
		return array("Yes"=>0,"No"=>1);
	}
	function get_account_type_value($value){
		return $value =='Postpaid' ? '1' : '0';
	}
	function get_sipinfo_array(){
		return array("Random"=>"Random");
	}
	// END
}
