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
class rates_form {
	function __construct($library_name = '') {
		$this->CI = & get_instance ();
	}
	function get_termination_rate_form_fields() {
		$form ['forms'] = array (
				base_url () . 'rates/termination_rate_save/',
				array (
						'id' => 'termination_rate_form',
						'method' => 'POST',
						'name' => 'termination_rate_form' 
				) 
		);
		$form [gettext ( 'Rate Information' )] = array (
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
						gettext ( 'Trunk' ),
						'trunk_id',
						'SELECT',
						'',
						'dropdown',
						'tOOL TIP',
						'Please Enter account number',
						'id',
						'name',
						'trunks',
						'build_dropdown',
						'where_arr',
						array (
								"status" => "0" 
						) 
				),
				array (
						gettext ( 'Code' ),
						'INPUT',
						array (
								'name' => 'pattern',
								'size' => '20',
								'class' => "text field medium" 
						),
						'trim|required|numeric|xss_clean',
						'tOOL TIP',
						'' 
				),
				array (
						gettext ( 'Destination' ),
						'INPUT',
						array (
								'name' => 'comment',
								'size' => '20',
								'class' => "text field medium" 
						),
						'tOOL TIP',
						'' 
				),
				array (
						gettext ( 'Strip' ),
						'INPUT',
						array (
								'name' => 'strip',
								'size' => '20',
								'class' => "text field medium" 
						),
						'',
						'tOOL TIP',
						'' 
				),
				array (
						gettext ( 'Prepend' ),
						'INPUT',
						array (
								'name' => 'prepend',
								'size' => '20',
								'class' => "text field medium" 
						),
						'trim|xss_clean',
						'tOOL TIP',
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
						'set_status' 
				) 
		)
		;
		$form [gettext ( 'Billing Information' )] = array (
				array (
						gettext ( 'Connect Cost' ),
						'INPUT',
						array (
								'name' => 'connectcost',
								'size' => '20',
								'class' => "text field medium" 
						),
						'trim|numeric|currency_decimal|xss_clean',
						'tOOL TIP',
						'' 
				),
				array (
						gettext ( 'Included Seconds' ),
						'INPUT',
						array (
								'name' => 'includedseconds',
								'size' => '20',
								'class' => "text field medium" 
						),
						'trim|numeric|xss_clean',
						'tOOL TIP',
						'' 
				),
				array (
						gettext ( 'Per Minute Cost' ),
						'INPUT',
						array (
								'name' => 'cost',
								'size' => '20',
								'class' => "text field medium" 
						),
						'trim|numeric|currency_decimal|xss_clean',
						'tOOL TIP',
						'' 
				),
				/**
				 * ASTPP 3.0
				 * For Add Initial Increment field
				 * *
				 */
				array (
						gettext ( 'Initial Increment' ),
						'INPUT',
						array (
								'name' => 'init_inc',
								'size' => '20',
								'class' => "text field medium" 
						),
						'trim|numeric|xss_clean',
						'tOOL TIP',
						'' 
				),
				/**
				 * ****************************************************
				 */
				array (
						gettext ( 'Increment' ),
						'INPUT',
						array (
								'name' => 'inc',
								'size' => '20',
								'class' => "text field medium" 
						),
						'trim|numeric|xss_clean',
						'tOOL TIP',
						'' 
				),
				array (
						gettext ( 'Priority' ),
						'INPUT',
						array (
								'name' => 'precedence',
								'size' => '20',
								'class' => "text field medium" 
						),
						'trim|numeric|xss_clean',
						'tOOL TIP',
						'' 
				) 
		);
		
		$form ['button_cancel'] = array (
				'name' => 'action',
				'content' => gettext ( 'Close' ),
				'value' => 'cancel',
				'type' => 'button',
				'class' => 'btn btn-line-sky margin-x-10',
				'onclick' => 'return redirect_page(\'NULL\')' 
		);
		$form ['button_save'] = array (
				'name' => 'action',
				'content' => gettext ( 'Save' ),
				'value' => 'save',
				'id' => 'submit',
				'type' => 'button',
				'class' => 'btn btn-line-parrot' 
		);
		
		return $form;
	}
	function get_origination_rate_form_fields() {
		$logintype = $this->CI->session->userdata ( 'userlevel_logintype' );
		$trunk = null;
		if ($logintype != 1)
			$trunk = array (
					'Force Trunk',
					'trunk_id',
					'SELECT',
					'',
					'',
					'tOOL TIP',
					'Please Enter account number',
					'id',
					'name',
					'trunks',
					'build_dropdown',
					'where_arr',
					array (
							"status" => "0" 
					) 
			);
		$form ['forms'] = array (
				base_url () . 'rates/origination_rate_save/',
				array (
						'id' => 'origination_rate_form',
						'method' => 'POST',
						'name' => 'origination_rate_form' 
				) 
		);
		$form [gettext ( 'Rate Information' )] = array (
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
						gettext ( 'Rate Group' ),
						'pricelist_id',
						'SELECT',
						'',
						'',
						'tOOL TIP',
						'Please Enter account number',
						'id',
						'name',
						'pricelists',
						'build_dropdown',
						'where_arr',
						array (
								"status" => "0",
								'reseller_id' => 0 
						) 
				),
				array (
						gettext ( 'Code' ),
						'INPUT',
						array (
								'name' => 'pattern',
								'size' => '20',
								'class' => "text field medium" 
						),
						'trim|required|numeric|xss_clean',
						'tOOL TIP',
						'' 
				),
				array (
						gettext ( 'Destination' ),
						'INPUT',
						array (
								'name' => 'comment',
								'size' => '20',
								'class' => "text field medium" 
						),
						'tOOL TIP',
						'' 
				),
				// array('Priority', 'INPUT', array('name' => 'precedence', 'size' => '20', 'class' => "text field medium"), 'trim|numeric|xss_clean', 'tOOL TIP', ''),
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
						'set_status' 
				) 
		)
		;
		$form [gettext ( 'Billing Information' )] = array (
				array (
						gettext ( 'Connect Cost' ),
						'INPUT',
						array (
								'name' => 'connectcost',
								'size' => '20',
								'class' => "text field medium" 
						),
						'trim|numeric|currency_decimal|xss_clean',
						'tOOL TIP',
						'' 
				),
				array (
						gettext ( 'Included Seconds' ),
						'INPUT',
						array (
								'name' => 'includedseconds',
								'size' => '20',
								'class' => "text field medium" 
						),
						'trim|numeric|xss_clean',
						'tOOL TIP',
						'' 
				),
				array (
						gettext ( 'Per Minute Cost' ),
						'INPUT',
						array (
								'name' => 'cost',
								'size' => '20',
								'class' => "text field medium" 
						),
						'trim|numeric|currency_decimal|xss_clean',
						'tOOL TIP',
						'' 
				),
				/**
				 * ASTPP 3.0
				 * For Add Initial Increment field
				 * *
				 */
				array (
						gettext ( 'Initial Increment' ),
						'INPUT',
						array (
								'name' => 'init_inc',
								'size' => '20',
								'class' => "text field medium" 
						),
						'trim|numeric|xss_clean',
						'tOOL TIP',
						'' 
				),
				/**
				 * *****************************************************************
				 */
				array (
						gettext ( 'Increment' ),
						'INPUT',
						array (
								'name' => 'inc',
								'size' => '20',
								'class' => "text field medium" 
						),
						'trim|numeric|xss_clean',
						'tOOL TIP',
						'' 
				),
				 $trunk,
		);
		
		$form ['button_cancel'] = array (
				'name' => 'action',
				'content' => gettext ( 'Close' ),
				'value' => 'cancel',
				'type' => 'button',
				'class' => 'btn btn-line-sky margin-x-10',
				'onclick' => 'return redirect_page(\'NULL\')' 
		);
		$form ['button_save'] = array (
				'name' => 'action',
				'content' => gettext ( 'Save' ),
				'value' => 'save',
				'id' => 'submit',
				'type' => 'button',
				'class' => 'btn btn-line-parrot' 
		);
		
		return $form;
	}
	function get_termination_rate_search_form() {
		$form ['forms'] = array (
				"",
				array (
						'id' => "termination_rate_search" 
				) 
		);
		$form [gettext ( 'Search' )] = array (
				
				array (
						gettext ( 'Code' ),
						'INPUT',
						array (
								'name' => 'pattern[pattern]',
								'',
								'size' => '20',
								'class' => "text field" 
						),
						'',
						'tOOL TIP',
						'1',
						'pattern[pattern-string]',
						'',
						'',
						'',
						'search_string_type',
						'' 
				),
				array (
						gettext ( 'Destination' ),
						'INPUT',
						array (
								'name' => 'comment[comment]',
								'',
								'size' => '20',
								'class' => "text field " 
						),
						'',
						'tOOL TIP',
						'1',
						'comment[comment-string]',
						'',
						'',
						'',
						'search_string_type',
						'' 
				),
				array (
						gettext ( 'Connect Cost' ),
						'INPUT',
						array (
								'name' => 'connectcost[connectcost]',
								'value' => '',
								'size' => '20',
								'class' => "text field " 
						),
						'',
						'Tool tips info',
						'1',
						'connectcost[connectcost-integer]',
						'',
						'',
						'',
						'search_int_type',
						'' 
				),
				array (
						gettext ( 'Included Seconds' ),
						'INPUT',
						array (
								'name' => 'includedseconds[includedseconds]',
								'value' => '',
								'size' => '20',
								'class' => "text field " 
						),
						'',
						'Tool tips info',
						'1',
						'includedseconds[includedseconds-integer]',
						'',
						'',
						'',
						'search_int_type',
						'' 
				),
				array (
						gettext ( 'Per Minute Cost' ),
						'INPUT',
						array (
								'name' => 'cost[cost]',
								'value' => '',
								'size' => '20',
								'class' => "text field " 
						),
						'',
						'Tool tips info',
						'1',
						'cost[cost-integer]',
						'',
						'',
						'',
						'search_int_type',
						'' 
				),
				/**
				 * ASTPP 3.0
				 * For Add Initial Increment field
				 * *
				 */
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
				/**
				 * ************************************************************
				 */
				array (
						gettext ( 'Increment' ),
						'INPUT',
						array (
								'name' => 'inc[inc]',
								'',
								'size' => '20',
								'class' => "text field " 
						),
						'',
						'tOOL TIP',
						'1',
						'inc[inc-integer]',
						'',
						'',
						'',
						'search_int_type',
						'' 
				),
				array (
						gettext ( 'Priority' ),
						'INPUT',
						array (
								'name' => 'prepend[prepend]',
								'',
								'size' => '20',
								'class' => "text field " 
						),
						'',
						'tOOL TIP',
						'1',
						'prepend[prepend-string]',
						'',
						'',
						'',
						'search_string_type',
						'' 
				),
				array (
						gettext ( 'Trunk' ),
						'trunk_id',
						'SELECT',
						'',
						'',
						'tOOL TIP',
						'Please Enter account number',
						'id',
						'name',
						'trunks',
						'build_dropdown',
						'where_arr',
						array (
								"status" => "0" 
						) 
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
				/**
				 * ****
				 * ASTPP 3.0
				 * Batch Delete
				 * *****
				 */
				array (
						'',
						'HIDDEN',
						'ajax_search',
						'1',
						'',
						'',
						'' 
				),
				/**
				 * *********************
				 */
				array (
						'',
						'HIDDEN',
						'advance_search',
						'1',
						'',
						'',
						'' 
				) 
		)
		;
		/**
		 * ****
		 * ASTPP 3.0
		 * Batch Delete
		 * *****
		 */
		$form ['button_search_delete'] = array (
				'name' => 'action',
				'id' => "termination_rate_batch_dlt",
				'onclick' => "check_btn();",
				'content' => gettext ( 'Delete Search Record' ),
				'style' => 'display:none;',
				'value' => 'submit',
				'type' => 'button',
				'class' => 'btn pull-right btn btn-line-danger ' 
		);
		
		/**
		 * ********************
		 */
		/**
		 * ****
		 * ASTPP 3.0
		 * Batch Delete
		 * *****
		 */
		$form ['button_search'] = array (
				'name' => 'action',
				'id' => "termination_rate_search_btn",
				'onclick' => 'search_btn();',
				'content' => gettext ( 'Search' ),
				'value' => 'save',
				'type' => 'button',
				'class' => 'btn btn-line-parrot pull-right margin-x-10' 
		);
		/**
		 * ********************
		 */
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
	function termination_rate_batch_update_form() {
		$form ['forms'] = array (
				"rates/termination_rate_batch_update/",
				array (
						'id' => "termination_rate_batch_update" 
				) 
		);
		$form ['Batch Update'] = array (
				array (
						gettext ( 'Connect Cost' ),
						'INPUT',
						array (
								'name' => 'connectcost[connectcost]',
								'id' => 'connectcost',
								'value' => '',
								'size' => '20',
								'class' => "text field " 
						),
						'',
						'Tool tips info',
						'1',
						array (
								'name' => 'connectcost[operator]',
								'class' => 'update_drp' 
						),
						'',
						'',
						'',
						'update_int_type',
						'' 
				),
				array (
						gettext ( 'Included Seconds' ),
						'INPUT',
						array (
								'name' => 'includedseconds[includedseconds]',
								'id' => 'includedseconds',
								'value' => '',
								'size' => '20',
								'class' => "text field " 
						),
						'',
						'Tool tips info',
						'1',
						array (
								'name' => 'includedseconds[operator]',
								'class' => 'update_drp' 
						),
						'',
						'',
						'',
						'update_int_type',
						'' 
				),
				array (
						gettext ( 'Per Minute Cost' ),
						'INPUT',
						array (
								'name' => 'cost[cost]',
								'id' => 'cost',
								'value' => '',
								'size' => '20',
								'class' => "text field " 
						),
						'',
						'Tool tips info',
						'1',
						array (
								'name' => 'cost[operator]',
								'class' => 'update_drp' 
						),
						'',
						'',
						'',
						'update_int_type',
						'' 
				),
				
				array (
						gettext ( 'Increment' ),
						'INPUT',
						array (
								'name' => 'inc[inc]',
								'id' => 'inc',
								'size' => '20',
								'class' => "text field " 
						),
						'',
						'tOOL TIP',
						'1',
						array (
								'name' => 'inc[operator]',
								'class' => 'update_drp' 
						),
						'',
						'',
						'',
						'update_int_type',
						'' 
				),
				array (
						gettext ( 'Priority' ),
						'INPUT',
						array (
								'name' => 'precedence[precedence]',
								'id' => 'precedence',
								'size' => '20',
								'class' => "text field " 
						),
						'',
						'tOOL TIP',
						'1',
						array (
								'name' => 'precedence[operator]',
								'class' => 'update_drp' 
						),
						'',
						'',
						'',
						'update_drp_type',
						'' 
				),
				array (
						gettext ( 'Prepand' ),
						'INPUT',
						array (
								'name' => 'prepend[prepend]',
								'id' => 'prepend',
								'size' => '20',
								'class' => "text field " 
						),
						'',
						'tOOL TIP',
						'1',
						array (
								'name' => 'prepend[operator]',
								'class' => 'update_drp' 
						),
						'',
						'',
						'',
						'update_drp_type',
						'' 
				) 
		)
		;
		
		$form ['button_search'] = array (
				'name' => 'action',
				'id' => "batch_update_btn",
				'content' => gettext ( 'Update' ),
				'value' => 'save',
				'type' => 'button',
				'class' => 'btn btn-line-parrot pull-right' 
		);
		$form ['button_reset'] = array (
				'name' => 'action',
				'id' => "id_batch_reset",
				'content' => gettext ( 'Clear' ),
				'value' => 'cancel',
				'type' => 'reset',
				'class' => 'btn btn-line-sky pull-right margin-x-10' 
		);
		
		return $form;
	}
	function origination_rate_batch_update_form() {
		$logintype = $this->CI->session->userdata ( 'userlevel_logintype' );
		$trunk = null;
		if ($logintype != 1)
			$trunk = array (
					'Force Trunk',
					array (
							'name' => 'trunk_id[trunk_id]',
							'id' => 'trunk_id' 
					),
					'SELECT',
					'',
					'',
					'tOOL TIP',
					'Please Enter account number',
					'id',
					'name',
					'trunks',
					'build_dropdown',
					'where_arr',
					array (
							"status" => "0" 
					),
					array (
							'name' => 'trunk_id[operator]',
							'class' => 'update_drp' 
					),
					'update_drp_type' 
			);
		$form ['forms'] = array (
				"rates/origination_rate_batch_update/",
				array (
						'id' => "origination_rate_batch_update" 
				) 
		);
		$form [gettext ( 'Batch Update' )] = array (
				array (
						gettext ( 'Connect Cost' ),
						'INPUT',
						array (
								'name' => 'connectcost[connectcost]',
								'id' => 'connectcost',
								'value' => '',
								'size' => '20',
								'class' => "text field " 
						),
						'',
						'Tool tips info',
						'1',
						array (
								'name' => 'connectcost[operator]',
								'class' => 'update_drp' 
						),
						'',
						'',
						'',
						'update_int_type',
						'' 
				),
				array (
						gettext ( 'Included Seconds' ),
						'INPUT',
						array (
								'name' => 'includedseconds[includedseconds]',
								'id' => 'includedseconds',
								'value' => '',
								'size' => '20',
								'class' => "text field " 
						),
						'',
						'Tool tips info',
						'1',
						array (
								'name' => 'includedseconds[operator]',
								'class' => 'update_drp' 
						),
						'',
						'',
						'',
						'update_int_type',
						'' 
				),
				array (
						gettext ( 'Per Minute Cost' ),
						'INPUT',
						array (
								'name' => 'cost[cost]',
								'id' => 'cost',
								'value' => '',
								'size' => '20',
								'class' => "text field " 
						),
						'',
						'Tool tips info',
						'1',
						array (
								'name' => 'cost[operator]',
								'class' => 'update_drp' 
						),
						'',
						'',
						'',
						'update_int_type',
						'' 
				),
				array (
						gettext ( 'Increment' ),
						'INPUT',
						array (
								'name' => 'inc[inc]',
								'id' => 'inc',
								'size' => '20',
								'class' => "text field " 
						),
						'',
						'tOOL TIP',
						'1',
						array (
								'name' => 'inc[operator]',
								'class' => 'update_drp' 
						),
						'',
						'',
						'',
						'update_int_type',
						'' 
				),
				
				$trunk 
		);
		
		$form ['button_search'] = array (
				'name' => 'action',
				'id' => "batch_update",
				'content' => gettext ( 'Update' ),
				'value' => 'save',
				'type' => 'button',
				'class' => 'btn btn-line-parrot pull-right' 
		);
		$form ['button_reset'] = array (
				'name' => 'action',
				'id' => "id_batch_reset",
				'content' => gettext ( 'Clear' ),
				'value' => 'cancel',
				'type' => 'reset',
				'class' => 'btn btn-line-sky pull-right margin-x-10' 
		);
		
		return $form;
	}
	function build_rates_list_for_reseller() {
		$account_info = $accountinfo = $this->CI->session->userdata ( 'accountinfo' );
		$currency_id = $account_info ['currency_id'];
		$currency = $this->CI->common->get_field_name ( 'currency', 'currency', $currency_id );
		
		$grid_field_arr = json_encode ( array (
				array (
						gettext ( 'Code' ),
						"140",
						"pattern",
						"pattern",
						"",
						"get_only_numeric_val",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( 'Destination' ),
						"240",
						"comment",
						"",
						"",
						"" 
				),
				array (
						gettext ( 'Connection Cost(' . $currency . ')' ),
						"210",
						"connectcost",
						"connectcost",
						"connectcost",
						"convert_to_currency",
						"",
						"true",
						"right" 
				),
				array (
						gettext ( 'Included Seconds' ),
						"180",
						"includedseconds",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( 'Per Minute Cost(' . $currency . ')' ),
						"180",
						"cost",
						"cost",
						"cost",
						"convert_to_currency",
						"",
						"true",
						"right" 
				),
				array (
						gettext ( 'Increment' ),
						"140",
						"inc",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( 'Priority' ),
						"155",
						"precedence",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				) 
		) );
		return $grid_field_arr;
	}
	function get_reseller_origination_rate_search_form() {
		$accountinfo = $this->CI->session->userdata ( 'accountinfo' );
		
		$form ['forms'] = array (
				"",
				array (
						'id' => "resellerrates_list_search" 
				) 
		);
		$form [gettext ( 'Search My Rates' )] = array (
				
				array (
						gettext ( 'Code' ),
						'INPUT',
						array (
								'name' => 'pattern[pattern]',
								'',
								'size' => '20',
								'class' => "text field " 
						),
						'',
						'tOOL TIP',
						'1',
						'pattern[pattern-string]',
						'',
						'',
						'',
						'search_string_type',
						'' 
				),
				array (
						gettext ( 'Destination' ),
						'INPUT',
						array (
								'name' => 'comment[comment]',
								'',
								'size' => '20',
								'class' => "text field " 
						),
						'',
						'tOOL TIP',
						'1',
						'comment[comment-string]',
						'',
						'',
						'',
						'search_string_type',
						'' 
				),
				array (
						gettext ( 'Connect Cost' ),
						'INPUT',
						array (
								'name' => 'connectcost[connectcost]',
								'value' => '',
								'size' => '20',
								'class' => "text field " 
						),
						'',
						'Tool tips info',
						'1',
						'connectcost[connectcost-integer]',
						'',
						'',
						'',
						'search_int_type',
						'' 
				),
				array (
						gettext ( 'Included Seconds' ),
						'INPUT',
						array (
								'name' => 'includedseconds[includedseconds]',
								'value' => '',
								'size' => '20',
								'class' => "text field " 
						),
						'',
						'Tool tips info',
						'1',
						'includedseconds[includedseconds-integer]',
						'',
						'',
						'',
						'search_int_type',
						'' 
				),
				array (
						gettext ( 'Per Minute Cost' ),
						'INPUT',
						array (
								'name' => 'cost[cost]',
								'value' => '',
								'size' => '20',
								'class' => "text field " 
						),
						'',
						'Tool tips info',
						'1',
						'cost[cost-integer]',
						'',
						'',
						'',
						'search_int_type',
						'' 
				),
				array (
						gettext ( 'Increment' ),
						'INPUT',
						array (
								'name' => 'inc[inc]',
								'',
								'size' => '20',
								'class' => "text field " 
						),
						'',
						'tOOL TIP',
						'1',
						'inc[inc-integer]',
						'',
						'',
						'',
						'search_int_type',
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
				'id' => "resellerrates_list_search_btn",
				'content' => gettext ( 'Search' ),
				'value' => 'save',
				'type' => 'button',
				'class' => 'btn btn-line-parrot pull-right' 
		);
		$form ['button_reset'] = array (
				'name' => 'action',
				'id' => "id_reset",
				'content' => 'Clear',
				'value' => gettext ( 'cancel' ),
				'type' => 'reset',
				'class' => 'btn btn-line-sky pull-right margin-x-10' 
		);
		
		return $form;
	}
	function get_origination_rate_search_form() {
		$form ['forms'] = array (
				"",
				array (
						'id' => "origination_rate_list_search" 
				) 
		);
		$form [gettext ( 'Search' )] = array (
				
				array (
						gettext ( 'Code' ),
						'INPUT',
						array (
								'name' => 'pattern[pattern]',
								'',
								'size' => '20',
								'class' => "text field " 
						),
						'',
						'tOOL TIP',
						'1',
						'pattern[pattern-string]',
						'',
						'',
						'',
						'search_string_type',
						'' 
				),
				array (
						gettext ( 'Destination' ),
						'INPUT',
						array (
								'name' => 'comment[comment]',
								'',
								'size' => '20',
								'class' => "text field " 
						),
						'',
						'tOOL TIP',
						'1',
						'comment[comment-string]',
						'',
						'',
						'',
						'search_string_type',
						'' 
				),
				array (
						gettext ( 'Connect Cost' ),
						'INPUT',
						array (
								'name' => 'connectcost[connectcost]',
								'value' => '',
								'size' => '20',
								'class' => "text field " 
						),
						'',
						'Tool tips info',
						'1',
						'connectcost[connectcost-integer]',
						'',
						'',
						'',
						'search_int_type',
						'' 
				),
				array (
						gettext ( 'Included Seconds' ),
						'INPUT',
						array (
								'name' => 'includedseconds[includedseconds]',
								'value' => '',
								'size' => '20',
								'class' => "text field " 
						),
						'',
						'Tool tips info',
						'1',
						'includedseconds[includedseconds-integer]',
						'',
						'',
						'',
						'search_int_type',
						'' 
				),
				array (
						gettext ( 'Per Minute Cost' ),
						'INPUT',
						array (
								'name' => 'cost[cost]',
								'value' => '',
								'size' => '20',
								'class' => "text field " 
						),
						'',
						'Tool tips info',
						'1',
						'cost[cost-integer]',
						'',
						'',
						'',
						'search_int_type',
						'' 
				),
				/**
				 * ASTPP 3.0
				 * For Add Initial Increment field
				 * *
				 */
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
				/**
				 * ***********************************************************
				 */
				array (
						gettext ( 'Increment' ),
						'INPUT',
						array (
								'name' => 'inc[inc]',
								'',
								'size' => '20',
								'class' => "text field " 
						),
						'',
						'tOOL TIP',
						'1',
						'inc[inc-integer]',
						'',
						'',
						'',
						'search_int_type',
						'' 
				),
				array (
						gettext ( 'Rate Group' ),
						'pricelist_id',
						'SELECT',
						'',
						'',
						'tOOL TIP',
						'Please Enter account number',
						'id',
						'name',
						'pricelists',
						'build_dropdown',
						'where_arr',
						array (
								"status" => "0",
								"reseller_id" => "0" 
						) 
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
		/**
		 * ****
		 * ASTPP 3.0
		 * Batch Delete
		 * *****
		 */
		$form ['button_search_delete'] = array (
				'name' => 'action',
				'id' => "origination_rate_batch_dlt",
				'onclick' => "check_btn();",
				'content' => gettext ( 'Delete Search Record' ),
				'style' => 'display:none;',
				'value' => 'save',
				'type' => 'button',
				'class' => 'btn pull-right btn btn-line-danger ' 
		);
		
		/**
		 * ********************
		 */
		/**
		 * ******
		 * ASTPP 3.0
		 * Batch delete
		 * ********
		 */
		$form ['button_search'] = array (
				'name' => 'action',
				'id' => "origination_rate_list_search_btn",
				'onclick' => 'search_btn();',
				'content' => gettext ( 'Search' ),
				'value' => 'save',
				'type' => 'button',
				'class' => 'btn btn-line-parrot pull-right  margin-x-10' 
		);
		/**
		 * ***********************
		 */
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
	 * ASTPP 3.0
	 * changes in grid size
	 */
	function build_termination_rate_for_admin() {
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
				 * For Termination Rate edit on Code
				 * *
				 */
				array (
						gettext ( "Code" ),
						"78",
						"pattern",
						"pattern",
						"",
						"get_only_numeric_val",
						"EDITABLE",
						"true",
						"center" 
				),
				/**
				 * ******************************
				 */
				array (
						gettext ( "Destination" ),
						"80",
						"comment",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Connect Cost($currency)" ),
						"125",
						"connectcost",
						"connectcost",
						"connectcost",
						"convert_to_currency",
						"",
						"true",
						"right" 
				),
				array (
						gettext ( "Included<br/> Seconds" ),
						"70",
						"includedseconds",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Per Minute <br/>Cost($currency)" ),
						"100",
						"cost",
						"cost",
						"cost",
						"convert_to_currency",
						"",
						"true",
						"right" 
				),
				array (
						gettext ( "Initial <br/> Increment" ),
						"95",
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
						"75",
						"inc",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Priority" ),
						"70",
						"precedence",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Strip" ),
						"60",
						"strip",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Prepend" ),
						"70",
						"prepend",
						"pattern",
						"",
						"get_only_numeric_val",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Trunk" ),
						"80",
						"trunk_id",
						"name",
						"trunks",
						"get_field_name",
						"",
						"true",
						"center" 
				),
		   /*
            ASTPP  3.0 
            creation field show in grid
            */
			array (
						gettext ( "Status" ),
						"100",
						"status",
						"status",
						"outbound_routes",
						"get_status",
						"",
						"true",
						"center" 
				),
				// array("Created<br/>Date", "80", "creation_date", "creation_date", "creation_date", "convert_GMT_to","","true","center"),
				array (
						gettext ( "Modified Date" ),
						"150",
						"last_modified_date",
						"last_modified_date",
						"last_modified_date",
						"convert_GMT_to",
						"",
						"true",
						"center" 
				),
				/**
				 * *****************************************************************
				 */
				/*
				 * ASTPP 3.0
				 * status show active or inactive
				 */
				
				/**
				 * ********************************************************************
				 */
				// array("Reseller", "103", "reseller_id", "number", "accounts", "get_field_name"),
				array (
						gettext ( "Action" ),
						"80",
						"",
						"",
						"",
						array (
								"EDIT" => array (
										"url" => "rates/termination_rate_edit/",
										"mode" => "popup",
										"layout" => "medium" 
								),
								"DELETE" => array (
										"url" => "rates/termination_rate_delete/",
										"mode" => "single" 
								) 
						) 
				) 
		) );
		return $grid_field_arr;
	}
	/**
	 * ***********************************************
	 */
	
	/*
	 * ASTPP 3.0
	 * changes in grid size
	 */
	function build_origination_rate_list_for_admin() {
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
				 * For Origination rate edit on code
				 * *
				 */
				array (
						gettext ( "Code" ),
						"75",
						"pattern",
						"pattern",
						"",
						"get_only_numeric_val",
						"EDITABLE",
						"true",
						"center" 
				),
				/**
				 * *********************************
				 */
				array (
						gettext ( "Destination" ),
						"100",
						"comment",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Connect Cost($currency)" ),
						"130",
						"connectcost",
						"connectcost",
						"connectcost",
						"convert_to_currency",
						"",
						"true",
						"right" 
				),
				array (
						gettext ( "Included Seconds" ),
						"120",
						"includedseconds",
						"",
						"",
						"",
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
				/**
				 * ASTPP 3.0
				 * For Add Initial Increment field
				 * *
				 */
				array (
						gettext ( "Initial Increment" ),
						"130",
						"init_inc",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				/**
				 * ****************************************************************
				 */
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
				// array("Priority", "72", "precedence", "", "", "","","true","center"),
				array (
						gettext ( "Rate Group" ),
						"90",
						"pricelist_id",
						"name",
						"pricelists",
						"get_field_name",
						"",
						"true",
						"center" 
				),
			/*
            ASTPP  3.0  creation field show in grid
            */
			array (
						gettext ( "Status" ),
						"100",
						"status",
						"status",
						"routes",
						"get_status",
						"",
						"true",
						"center" 
				),
				// array("Created<br/>Date", "80", "creation_date", "creation_date", "creation_date", "convert_GMT_to","","true","center"),
				array (
						gettext ( "Modified Date" ),
						"150",
						"last_modified_date",
						"last_modified_date",
						"last_modified_date",
						"convert_GMT_to",
						"",
						"true",
						"center" 
				),
				
				/**
				 * *****************************************************************
				 */
				/*
				 * ASTPP 3.0
				 * chnage in status active or inactive
				 */
				
				array (
						gettext ( "Action" ),
						"95",
						"",
						"",
						"",
						array (
								"EDIT" => array (
										"url" => "rates/origination_rate_edit/",
										"mode" => "popup",
										"layout" => "medium" 
								),
								"DELETE" => array (
										"url" => "/rates/origination_rate_delete/",
										"mode" => "single" 
								) 
						) 
				) 
		) );
		return $grid_field_arr;
	}
	/**
	 * *************************************************************************************
	 */
	function build_grid_buttons() {
		$buttons_json = json_encode ( array (
				array (
						gettext ( "Create" ),
						"btn btn-line-warning btn",
						"fa fa-plus-circle fa-lg",
						"button_action",
						"/rates/termination_rate_add/",
						"popup",
						"medium" 
				),
				array (
						gettext ( "Delete" ),
						"btn btn-line-danger",
						"fa fa-times-circle fa-lg",
						"button_action",
						"/rates/termination_rate_delete_multiple/" 
				),
				array (
						gettext ( "import" ),
						"btn btn-line-blue",
						"fa fa-upload fa-lg",
						"button_action",
						"/rates/termination_rate_import/",
						'single' 
				),
				array (
						gettext ( "Import with field map" ),
						"btn btn-line-blue",
						"fa fa-upload fa-lg",
						"button_action",
						"/rates/termination_rate_import_mapper/",
						'single' 
				),
				array (
						gettext ( "Export" ),
						"btn btn-xing",
						" fa fa-download fa-lg",
						"button_action",
						"/rates/termination_rate_export_cdr_xls/",
						'single' 
				) 
		) );
		return $buttons_json;
	}
	function build_grid_buttons_origination_rate() {
		$buttons_json = json_encode ( array (
				array (
						gettext ( "Create" ),
						"btn btn-line-warning btn",
						"fa fa-plus-circle fa-lg",
						"button_action",
						"/rates/origination_rate_add/",
						"popup",
						"medium" 
				),
				array (
						gettext ( "Delete" ),
						"btn btn-line-danger",
						"fa fa-times-circle fa-lg",
						"button_action",
						"/rates/origination_rate_delete_multiple/" 
				),
				array (
						gettext ( "import" ),
						"btn btn-line-blue",
						"fa fa-upload fa-lg",
						"button_action",
						"/rates/origination_rate_import/",
						'single' 
				),
				array (
						gettext ( "Export" ),
						"btn btn-xing",
						" fa fa-download fa-lg",
						"button_action",
						"/rates/origination_rate_export_cdr_xls/",
						'single' 
				) 
		)
		 );
		return $buttons_json;
	}
	function build_termination_rate_list_for_customer() {
		// array(display name, width, db_field_parent_table,feidname, db_field_child_table,function name);
		$grid_field_arr = json_encode ( array (
				array (
						"<input type='checkbox' name='chkAll' class='ace checking checkall'/><label class='lbl'></label>",
						"30",
						"",
						"",
						"",
						"",
						"",
						"false",
						"center" 
				),
				array (
						gettext ( "Code" ),
						"70",
						"pattern",
						"pattern",
						"",
						"get_only_numeric_val" 
				),
				array (
						gettext ( "Increment" ),
						"75",
						"inc",
						"",
						"",
						"" 
				),
				array (
						gettext ( "Connect <br> Charge", "100" ),
						"connectcost",
						"connectcost",
						"connectcost",
						"convert_to_currency" 
				),
				array (
						gettext ( "Included <br> Seconds" ),
						"100",
						"includedseconds",
						"",
						"",
						"" 
				),
				array (
						gettext ( "Destination" ),
						"100",
						"comment",
						"",
						"",
						"" 
				),
				array (
						gettext ( "Cost per <br> Minutes" ),
						"100",
						"cost",
						"cost",
						"cost",
						"convert_to_currency" 
				),
				array (
						gettext ( "Priority" ),
						"80",
						"precedence",
						"",
						"",
						"" 
				),
				array (
						gettext ( "Reseller" ),
						"80",
						"reseller_id",
						"number",
						"accounts",
						"get_field_name" 
				) 
		) );
		return $grid_field_arr;
	}
	function build_block_pattern_list_for_customer() {
		// array(display name, width, db_field_parent_table,feidname, db_field_child_table,function name);
		$grid_field_arr = json_encode ( array (
				array (
						"<input type='checkbox' name='chkAll1' class='ace checking'/><label class='lbl'></label>",
						"30",
						"",
						"",
						"",
						"",
						"",
						"false",
						"center",
						"PatternChkBox" 
				),
				array (
						gettext ( "Code" ),
						"350",
						"pattern",
						"pattern",
						"",
						"get_only_numeric_val" 
				),
				array (
						gettext ( "Destination" ),
						"350",
						"comment",
						"",
						"",
						"" 
				) 
		) );
		return $grid_field_arr;
	}
	function build_pattern_list_for_customer($accountid, $accounttype) {
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
				array (
						gettext ( "Code" ),
						"450",
						"blocked_patterns",
						"blocked_patterns",
						"",
						"get_only_numeric_val" 
				),
				array (
						gettext ( "Destination" ),
						"450",
						"destination",
						"",
						"",
						"" 
				),
				array (
						gettext ( "Action" ),
						"100",
						"",
						"",
						"",
						array (
								"DELETE" => array (
										"url" => "accounts/" . $accounttype . "_delete_block_pattern/$accountid/",
										"mode" => "single" 
								) 
						) 
				) 
		) );
		return $grid_field_arr;
	}
	function set_pattern_grid_buttons($accountid) {
		$buttons_json = json_encode ( array (
				array (
						gettext ( "Add Prefixes" ),
						"btn btn-line-warning btn",
						"fa fa-plus-circle fa-lg",
						"button_action",
						"/accounts/customer_add_blockpatterns/$accountid",
						"popup" 
				) 
		) );
		return $buttons_json;
	}
	function build_origination_rate_list_for_user() {
		// array(display name, width, db_field_parent_table,feidname, db_field_child_table,function name);
		$grid_field_arr = json_encode ( array (
				array (
						gettext ( "Code" ),
						"155",
						"pattern",
						"pattern",
						"",
						"get_only_numeric_val" 
				),
				array (
						gettext ( "Destination" ),
						"225",
						"comment",
						"",
						"",
						"" 
				),
				array (
						gettext ( "Increment" ),
						"235",
						"inc",
						"",
						"",
						"" 
				),
				array (
						gettext ( "Cost per Minutes" ),
						"240",
						"cost",
						"cost",
						"cost",
						"convert_to_currency" 
				),
				array (
						gettext ( "Connect Charge" ),
						"200",
						"connectcost",
						"connectcost",
						"connectcost",
						"convert_to_currency" 
				),
				array (
						gettext ( "Included Seconds" ),
						"200",
						"includedseconds",
						"",
						"",
						"" 
				) 
		) );
		return $grid_field_arr;
	}
	function build_grid_buttons_rates() {
		$buttons_json = json_encode ( array (
				array (
						gettext ( "Export" ),
						"btn btn-xing",
						" fa fa-download fa-lg",
						"button_action",
						"/rates/resellersrates_xls/",
						'single' 
				) 
		) );
		return $buttons_json;
	}
}

?>
