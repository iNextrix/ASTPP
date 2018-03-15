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
class did_form {
	function __construct() {
		$this->CI = & get_instance ();
	}
	function get_dids_form_fields($id = false, $parent_id = '0', $account_id = '0', $country_id = false) {
		if ($id != 0) {
			if ($parent_id > 0) {
				$account_dropdown = array (
						'Reseller',
						array (
								'name' => 'parent_id',
								'disabled' => 'disabled',
								'class' => 'accountid',
								'id' => 'accountid' 
						),
						'SELECT',
						'',
						'',
						'tOOL TIP',
						'Please Enter account number',
						'id',
						'first_name,last_name,number',
						'accounts',
						'build_concat_dropdown',
						'where_arr',
						array (
								"reseller_id" => "0",
								"type" => "1",
								"deleted" => "0",
								"status" => "0" 
						) 
				);
			} else {
				if ($account_id > 0) {
					$account_dropdown = array (
							'Account ',
							array (
									'name' => 'accountid',
									'disabled' => 'disabled',
									'class' => 'accountid',
									'id' => 'accountid' 
							),
							'SELECT',
							'',
							'',
							'tOOL TIP',
							'Please Enter account number',
							'id',
							'first_name,last_name,number',
							'accounts',
							'build_concat_dropdown',
							'where_arr',
							array (
									"reseller_id" => "0",
									"type" => "0,3",
									"deleted" => "0",
									"status" => "0" 
							) 
					);
				} else {
					$account_dropdown = array (
							'Account',
							'accountid',
							'SELECT',
							'',
							array (
									"name" => "accountid",
									"rules" => "did_account_checking" 
							),
							'tOOL TIP',
							'Please Enter account number',
							'id',
							'first_name,last_name,number',
							'accounts',
							'build_concat_dropdown',
							'where_arr',
							array (
									"reseller_id" => "0",
									"type" => "0,3",
									"deleted" => "0",
									"status" => "0" 
							) 
					);
				}
			}
		} else {
			$account_dropdown = array (
					'Account',
					'accountid',
					'SELECT',
					'',
					array (
							"name" => "accountid",
							"rules" => "did_account_checking" 
					),
					'tOOL TIP',
					'Please Enter account number',
					'id',
					'first_name,last_name,number',
					'accounts',
					'build_concat_dropdown',
					'where_arr',
					array (
							"reseller_id" => "0",
							"type" => "0,3",
							"deleted" => "0",
							"status" => "0" 
					) 
			);
		}
		if (! $country_id) {
			
			$country = array (
					'Country',
					array (
							'name' => 'country_id',
							'class' => 'country_id' 
					),
					'SELECT',
					'',
					array (
							"name" => "country_id",
							"rules" => "required" 
					),
					'tOOL TIP',
					'Please Enter account number',
					'id',
					'country',
					'countrycode',
					'build_dropdown',
					'',
					'' 
			);
		} else {
			$country = array (
					'Country',
					array (
							'name' => 'country_id',
							'class' => 'country_id',
							'vlaue' => $country_id 
					),
					'SELECT',
					'',
					array (
							"name" => "country_id",
							"rules" => "required",
							'selected' => 'selected' 
					),
					'tOOL TIP',
					'Please Enter account number',
					'id',
					'country',
					'countrycode',
					'build_dropdown',
					'',
					'' 
			);
		}
		
		$val = $id > 0 ? 'dids.number.' . $id : 'dids.number';
		$form ['forms'] = array (
				base_url () . '/did/did_save/',
				array (
						'id' => 'did_form',
						'method' => 'POST',
						'name' => 'did_form' 
				) 
		);
		$form ['DID Information'] = array (
				array (
						'',
						'HIDDEN',
						array (
								'name' => 'id' 
						),
						'',
						'',
						'',
						'' 
				),
				array (
						gettext ( 'DID' ),
						'INPUT',
						array (
								'name' => 'number',
								'size' => '20',
								'class' => "text field medium" 
						),
						'trim|required|is_numeric|xss_clean|integer|is_unique[' . $val . ']',
						'tOOL TIP',
						'Please Enter account number' 
				),
				$country,
				
				array (
						gettext ( 'City' ),
						'INPUT',
						array (
								'name' => 'city',
								'size' => '20',
								'class' => "text field medium" 
						),
						'',
						'tOOL TIP',
						'Please Enter Password' 
				),
				array (
						gettext ( 'Province' ),
						'INPUT',
						array (
								'name' => 'province',
								'size' => '15',
								'class' => "text field medium" 
						),
						'',
						'tOOL TIP',
						'Please Enter Password' 
				),
				array (
						gettext ( 'Provider' ),
						'provider_id',
						'SELECT',
						'',
						'',
						'tOOL TIP',
						'Please Enter account number',
						'id',
						'first_name,last_name,number',
						'accounts',
						'build_concat_dropdown',
						'where_arr',
						array (
								"type" => "3",
								"deleted" => "0",
								"status" => "0" 
						) 
				) 
		);
		
		$form ['Billing Information'] = array (
				$account_dropdown,
				array (
						gettext ( 'Connection Cost' ),
						'INPUT',
						array (
								'name' => 'connectcost',
								'size' => '15',
								'class' => "text field medium" 
						),
						'trim|is_numeric|xss_clean',
						'tOOL TIP',
						'Please Enter Password' 
				),
				array (
						gettext ( 'Included Seconds' ),
						'INPUT',
						array (
								'name' => 'includedseconds',
								'size' => '50',
								'class' => "text field medium" 
						),
						'trim|is_numeric|xss_clean',
						'tOOL TIP',
						'Please Enter Password' 
				),
				array (
						gettext ( 'Per Minute Cost' ),
						'INPUT',
						array (
								'name' => 'cost',
								'size' => '15',
								'class' => "text field medium" 
						),
						'trim|is_numeric|xss_clean',
						'tOOL TIP',
						'Please Enter Password' 
				),
				array (
						gettext ( 'Initial Increment' ),
						'INPUT',
						array (
								'name' => 'init_inc',
								'size' => '20',
								'class' => "text field medium" 
						),
						'trim|is_numeric|xss_clean',
						'tOOL TIP',
						'Please Enter Initial Increment' 
				),
				array (
						gettext ( 'Increment' ),
						'INPUT',
						array (
								'name' => 'inc',
								'size' => '20',
								'class' => "text field medium" 
						),
						'trim|is_numeric|xss_clean',
						'tOOL TIP',
						'Please Enter Increment' 
				),
				array (
						gettext ( 'Setup Fee' ),
						'INPUT',
						array (
								'name' => 'setup',
								'size' => '15',
								'class' => 'text field medium' 
						),
						'trim|is_numeric|xss_clean',
						'tOOL TIP',
						'' 
				),
				array (
						gettext ( 'Monthly<br>Fee' ),
						'INPUT',
						array (
								'name' => 'monthlycost',
								'size' => '15',
								'class' => "text field medium" 
						),
						'trim|is_numeric|xss_clean',
						'tOOL TIP',
						'Please Enter Password' 
				),
				// Added call leg_timeout parameter to timeout the calls.
				array (
						'Call Timeout (Sec.)',
						'INPUT',
						array (
								'name' => 'leg_timeout',
								'size' => '4',
								'class' => "text field medium" 
						),
						'trim|xss_clean',
						'tOOL TIP',
						'Please Enter Call Leg Timeout' 
				) 
		);
		
		$form ['DID Setting'] = array (
				array (
						gettext ( 'Call Type' ),
						'call_type',
						'SELECT',
						'',
						'',
						'tOOL TIP',
						'',
						'',
						'',
						'',
						'set_call_type',
						'' 
				),
				array (
						gettext ( 'Destination' ),
						'INPUT',
						array (
								'name' => 'extensions',
								'size' => '20',
								'class' => "text field medium" 
						),
						'',
						'tOOL TIP',
						'Please Enter Password' 
				),
				array (
						gettext ( 'Concurrent Calls' ),
						'INPUT',
						array (
								'name' => 'maxchannels',
								'size' => '20',
								'class' => "text field medium" 
						),
						'trim|is_numeric|xss_clean',
						'tOOL TIP',
						'Please Enter account number' 
				),
				array (
						gettext ( 'Status' ),
						'status',
						'SELECT',
						'',
						'',
						'tOOL TIP',
						'Please Select Status',
						'',
						'',
						'',
						'set_status' 
				) 
		)
		;
		
		$form ['button_save'] = array (
				'name' => 'action',
				'content' => gettext ( 'Save' ),
				'value' => 'save',
				'id' => 'submit',
				'type' => 'button',
				'class' => 'btn btn-line-parrot' 
		);
		$form ['button_cancel'] = array (
				'name' => 'action',
				'content' => 'Close',
				gettext ( 'value' ) => 'cancel',
				'type' => 'button',
				'class' => 'btn btn-line-sky margin-x-10',
				'onclick' => 'return redirect_page(\'NULL\')' 
		);
		return $form;
	}
	
	/**
	 * ************************************************************************
	 */
	function get_search_did_form() {
		$form ['forms'] = array (
				"",
				array (
						'id' => "did_search" 
				) 
		);
		$form ['Search'] = array (
				array (
						gettext ( 'DID' ),
						'INPUT',
						array (
								'name' => 'number[number]',
								'',
								'size' => '20',
								'class' => "text field" 
						),
						'',
						'tOOL TIP',
						'1',
						'number[number-string]',
						'',
						'',
						'',
						'search_string_type',
						'' 
				),
				array (
						gettext ( 'Country' ),
						'country_id',
						'SELECT',
						'',
						'',
						'tOOL TIP',
						'Please Enter account number',
						'id',
						'country',
						'countrycode',
						'build_dropdown',
						'',
						'' 
				),
				array (
						gettext ( 'Account' ),
						'accountid',
						'SELECT',
						'',
						'',
						'tOOL TIP',
						'Please Enter account number',
						'id',
						'first_name,last_name,number',
						'accounts',
						'build_concat_dropdown',
						'where_arr',
						array (
								"reseller_id" => "0",
								"type" => "0",
								"deleted" => "0" 
						) 
				),
				array (
						gettext ( 'Initial Increment' ),
						'INPUT',
						array (
								'name' => 'init_inc[init_inc]',
								'',
								'size' => '20',
								'class' => "text field " 
						),
						'',
						'tOOL TIP',
						'1',
						'init_inc[init_inc-integer]',
						'',
						'',
						'',
						'search_int_type',
						'' 
				),
				array (
						gettext ( 'Call Type' ),
						'call_type',
						'SELECT',
						'',
						'',
						'tOOL TIP',
						'',
						'',
						'',
						'',
						'set_call_type_search',
						'',
						'' 
				),
				array (
						gettext ( 'Destination' ),
						'INPUT',
						array (
								'name' => 'extensions[extensions]',
								'',
								'size' => '20',
								'class' => "text field" 
						),
						'',
						'tOOL TIP',
						'1',
						'extensions[extensions-string]',
						'',
						'',
						'',
						'search_string_type',
						'' 
				),
				array (
						gettext ( 'Status' ),
						'status',
						'SELECT',
						'',
						'',
						'tOOL TIP',
						'Please Enter account number',
						'',
						'',
						'',
						'set_search_status',
						'',
						'' 
				),
				
				array (
						'',
						'HIDDEN',
						'ajax_search',
						'1',
						'',
						'',
						'' 
				),
				array (
						'',
						'HIDDEN',
						'advance_search',
						'1',
						'',
						'',
						'' 
				) 
		);
		
		$form ['button_search'] = array (
				'name' => 'action',
				'id' => "did_search_btn",
				'content' => gettext ( 'Search' ),
				'value' => 'save',
				'type' => 'button',
				'class' => 'btn btn-line-parrot pull-right' 
		);
		$form ['button_reset'] = array (
				'name' => 'action',
				'id' => "id_reset",
				'content' => gettext ( 'Clear' ),
				'value' => 'cancel',
				'type' => 'reset',
				'class' => 'btn btn-line-sky pull-right margin-x-10' 
		);
		
		return $form;
	}
	function get_search_did_form_for_reseller() {
		$form ['forms'] = array (
				"",
				array (
						'id' => "did_search" 
				) 
		);
		$form ['Search'] = array (
				array (
						gettext ( 'DID' ),
						'INPUT',
						array (
								'name' => 'note[note]',
								'',
								'size' => '20',
								'class' => "text field" 
						),
						'',
						'tOOL TIP',
						'1',
						'note[note-string]',
						'',
						'',
						'',
						'search_string_type',
						'' 
				),
				array (
						gettext ( 'Account' ),
						'accountid',
						'SELECT',
						'',
						'',
						'tOOL TIP',
						'Please Enter account number',
						'id',
						'first_name,last_name,number',
						'accounts',
						'build_concat_dropdown',
						'where_arr',
						array (
								"reseller_id" => "0",
								"type" => "0",
								"deleted" => "0" 
						) 
				),
				array (
						gettext ( 'Call Type' ),
						'call_type',
						'SELECT',
						'',
						'',
						'tOOL TIP',
						'',
						'',
						'',
						'',
						'set_call_type_search',
						'',
						'' 
				),
				array (
						gettext ( 'Destination' ),
						'INPUT',
						array (
								'name' => 'extensions[extensions]',
								'',
								'size' => '20',
								'class' => "text field" 
						),
						'',
						'tOOL TIP',
						'1',
						'extensions[extensions-string]',
						'',
						'',
						'',
						'search_string_type',
						'' 
				),
				array (
						gettext ( 'Status' ),
						'status',
						'SELECT',
						'',
						'',
						'tOOL TIP',
						'Please Enter account number',
						'',
						'',
						'',
						'set_search_status',
						'',
						'' 
				),
				array (
						'',
						'HIDDEN',
						'ajax_search',
						'1',
						'',
						'',
						'' 
				),
				array (
						'',
						'HIDDEN',
						'advance_search',
						'1',
						'',
						'',
						'' 
				) 
		);
		
		$form ['button_search'] = array (
				'name' => 'action',
				'id' => "did_search_btn",
				'content' => gettext ( 'Search' ),
				'value' => 'save',
				'type' => 'button',
				'class' => 'btn btn-line-parrot pull-right' 
		);
		$form ['button_reset'] = array (
				'name' => 'action',
				'id' => "id_reset",
				'content' => gettext ( 'Clear' ),
				'value' => 'cancel',
				'type' => 'reset',
				'class' => 'btn btn-line-sky pull-right margin-x-10' 
		);
		
		return $form;
	}
	
	/*
	 * ASTPP 3.0 grid size is change.
	 */
	function build_did_list_for_admin() {
		$account_info = $accountinfo = $this->CI->session->userdata ( 'accountinfo' );
		$currency_id = $account_info ['currency_id'];
		$currency = $this->CI->common->get_field_name ( 'currency', 'currency', $currency_id );
		
		// array(display name, width, db_field_parent_table,feidname, db_field_child_table,function name);
		$grid_field_arr = json_encode ( array (
				array (
						"<input type='checkbox' name='chkAll' class='ace checkall'/><label class='lbl'></label>",
						"30",
						"",
						"",
						"",
						"",
						"",
						"false",
						"center" 
				),
				
				/**
				 * ASTPP 3.0
				 * For DID edit on DID number
				 * *
				 */
				array (
						gettext ( "DID" ),
						"80",
						"number",
						"",
						"",
						"",
						"EDITABLE",
						"true",
						"center" 
				),
				array (
						gettext ( "Country" ),
						"60",
						"country_id",
						"country",
						"countrycode",
						"get_field_name",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Account" ),
						"95",
						"accountid",
						"first_name,last_name,number",
						"accounts",
						"get_field_name_coma_new" 
				),
				array (
						gettext ( "Per Minute <br>Cost($currency)" ),
						"85",
						"cost",
						"cost",
						"cost",
						"convert_to_currency",
						"",
						"true",
						"right" 
				),
				array (
						gettext ( "Initial <br>Increment" ),
						"80",
						"init_inc",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Increment" ),
						"90",
						"inc",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Setup <br>Fee($currency)" ),
						"70",
						"setup",
						"setup",
						"setup",
						"convert_to_currency",
						"",
						"true",
						"right" 
				),
				array (
						gettext ( "Monthly<br>Fee($currency)" ),
						"90",
						"monthlycost",
						"monthlycost",
						"monthlycost",
						"convert_to_currency",
						"",
						"true",
						"right" 
				),
				array (
						gettext ( "Call Type" ),
						"90",
						"call_type",
						"call_type",
						"call_type",
						"get_call_type",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Destination" ),
						"80",
						"extensions",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Status" ),
						"90",
						"status",
						"status",
						"dids",
						"get_status",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Modified <br/>Date" ),
						"90",
						"last_modified_date",
						"last_modified_date",
						"last_modified_date",
						"convert_GMT_to",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Is Purchased?" ),
						"110",
						"number",
						"number",
						"number",
						"check_did_avl",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Action" ),
						"100",
						"",
						"",
						"",
						array (
								"EDIT" => array (
										"url" => "did/did_edit/",
										"mode" => "popup",
										"layout" => "medium" 
								),
								"DELETE" => array (
										"url" => "did/did_remove/",
										"mode" => "single" 
								) 
						) 
				) 
		) );
		return $grid_field_arr;
	}
	/**
	 * **************************************************************************************
	 */
	/*
	 * ASTPP 3.0
	 * change in grid size
	 */
	function build_did_list_for_reseller_login() {
		$account_info = $accountinfo = $this->CI->session->userdata ( 'accountinfo' );
		$currency_id = $account_info ['currency_id'];
		$currency = $this->CI->common->get_field_name ( 'currency', 'currency', $currency_id );
		// array(display name, width, db_field_parent_table,feidname, db_field_child_table,function name);
		$grid_field_arr = json_encode ( array (
				array (
						gettext ( "DID" ),
						"90",
						"number",
						"",
						"",
						"",
						"EDITABLE",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Account" ),
						"100",
						"accountid",
						"first_name,last_name,number",
						"accounts",
						"get_field_name_coma_new",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Per Minute<br>Cost($currency)" ),
						"80",
						"cost",
						"cost",
						"cost",
						"convert_to_currency",
						"",
						"true",
						"right" 
				),
				array (
						gettext ( "Initial <br>Increment" ),
						"100",
						"init_inc",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Increment" ),
						"95",
						"inc",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Setup <br> Fee($currency)" ),
						"90",
						"setup",
						"setup",
						"setup",
						"convert_to_currency",
						"",
						"true",
						"right" 
				),
				array (
						gettext ( "Monthly<br> fee($currency)" ),
						"90",
						"monthlycost",
						"monthlycost",
						"monthlycost",
						"convert_to_currency",
						"",
						"true",
						"right" 
				),
				array (
						gettext ( "Call Type" ),
						"80",
						"call_type",
						"call_type",
						"call_type",
						"get_call_type",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Destination" ),
						"95",
						"extensions",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Status" ),
						"90",
						"status",
						"status",
						"reseller_pricing",
						"get_status",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Modified Date" ),
						"130",
						"last_modified_date",
						"last_modified_date",
						"last_modified_date",
						"convert_GMT_to",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Is purchased?" ),
						"100",
						"number",
						"number",
						"number",
						"check_did_avl_reseller",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Action" ),
						"90",
						"",
						"",
						"",
						array (
								"EDIT" => array (
										"url" => "did/did_reseller_edit/edit/",
										"mode" => "popup" 
								),
								"DELETE" => array (
										"url" => "did/did_reseller_edit/delete/",
										"mode" => "single" 
								) 
						) 
				) 
		) );
		return $grid_field_arr;
	}
	/**
	 * *********************************************************************
	 */
	function build_grid_buttons() {
		$buttons_json = json_encode ( array (
				array (
						gettext ( "Create" ),
						"btn btn-line-warning btn",
						"fa fa-plus-circle fa-lg",
						"button_action",
						"/did/did_add/",
						"popup",
						"medium" 
				),
				array (
						gettext ( "Delete" ),
						"btn btn-line-danger",
						"fa fa-times-circle fa-lg",
						"button_action",
						"/did/did_delete_multiple/" 
				),
				array (
						gettext ( "Import" ),
						"btn btn-line-blue",
						"fa fa-upload fa-lg",
						"button_action",
						"/did/did_import/",
						'',
						"small" 
				),
				array (
						gettext ( "Export" ),
						"btn btn-xing",
						"fa fa-download fa-lg",
						"button_action",
						"/did/did_export_data_xls",
						'single' 
				) 
		) );
		return $buttons_json;
	}
	function build_did_list_for_customer($accountid, $accounttype) {
		$account_info = $accountinfo = $this->CI->session->userdata ( 'accountinfo' );
		$currency_id = $account_info ['currency_id'];
		$currency = $this->CI->common->get_field_name ( 'currency', 'currency', $currency_id );
		// array(display name, width, db_field_parent_table,feidname, db_field_child_table,function name);
		$grid_field_arr = json_encode ( array (
				array (
						gettext ( "DID" ),
						"110",
						"number",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Country" ),
						"110",
						"country_id",
						"country",
						"countrycode",
						"get_field_name",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Per Minute Cost($currency)" ),
						"150",
						"cost",
						"cost",
						"cost",
						"convert_to_currency",
						"",
						"true",
						"right" 
				),
				array (
						gettext ( "Initial Increment" ),
						"140",
						"init_inc",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Increment" ),
						"120",
						"inc",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Setup Fee($currency)" ),
						"140",
						"setup",
						"setup",
						"setup",
						"convert_to_currency",
						"",
						"true",
						"right" 
				),
				array (
						gettext ( "Monthly Fee($currency)" ),
						"140",
						"monthlycost",
						"monthlycost",
						"monthlycost",
						"convert_to_currency",
						"",
						"true",
						"right" 
				),
				array (
						gettext ( "Action" ),
						"110",
						"",
						"",
						"",
						array (
								"DELETE" => array (
										"url" => "accounts/" . $accounttype . "_dids_action/delete/$accountid/$accounttype/",
										"mode" => "single" 
								) 
						) 
				) 
		) );
		return $grid_field_arr;
	}
	function build_did_list_for_reseller($accountid, $accounttype) {
		// array(display name, width, db_field_parent_table,feidname, db_field_child_table,function name);
		$grid_field_arr = json_encode ( array (
				array (
						"DID Number",
						"120",
						"number",
						"",
						"",
						"" 
				),
				array (
						gettext ( "Increment" ),
						"120",
						"inc",
						"",
						"",
						"" 
				),
				array (
						gettext ( "Is purchased?" ),
						"120",
						"number",
						"number",
						"number",
						"check_did_avl_reseller" 
				),
				array (
						gettext ( "Per Minute Cost" ),
						"120",
						"cost",
						"cost",
						"cost",
						"convert_to_currency" 
				),
				array (
						gettext ( "Included<br> Seconds" ),
						"100",
						"includedseconds",
						"",
						"",
						"" 
				),
				array (
						gettext ( "Setup <br> Fee" ),
						"109",
						"setup",
						"setup",
						"setup",
						"convert_to_currency" 
				),
				array (
						gettext ( "Monthly<br> Fee" ),
						"140",
						"monthlycost",
						"monthlycost",
						"monthlycost",
						"convert_to_currency" 
				),
				array (
						gettext ( "Connection Cost" ),
						"149",
						"connectcost",
						"connectcost",
						"connectcost",
						"convert_to_currency" 
				),
				array (
						gettext ( "Disconnection <br> Fee" ),
						"140",
						"disconnectionfee",
						"disconnectionfee",
						"disconnectionfee",
						"convert_to_currency" 
				),
				array (
						gettext ( "Action" ),
						"100",
						"",
						"",
						"",
						array (
								"DELETE" => array (
										"url" => "/accounts/reseller_did_action/delete/$accountid/$accounttype/",
										"mode" => "single" 
								) 
						) 
				) 
		) );
		return $grid_field_arr;
	}
}

?>
