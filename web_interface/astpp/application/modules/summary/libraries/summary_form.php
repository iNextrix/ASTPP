<?php
// ##########################################################################
// ASTPP - Open Source Voip Billing
// Copyright (C) 2004, Aleph Communications
//
// Contributor(s)
// "iNextrix Technologies Pvt. Ltd - <astpp@inextrix.com>"
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details..
//
// You should have received a copy of the GNU General Public License
// along with this program. If not, see <http://www.gnu.org/licenses/>
// ###########################################################################
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Summary_form {
	function __construct() {
		$this->CI = & get_instance ();
	}
	function get_providersummary_search_form() {
		$form ['forms'] = array (
				'',
				array (
						'id' => "providersummary_search" 
				) 
		);
		$form ['Search'] = array (
				array (
						'From Date',
						'INPUT',
						array (
								'name' => 'callstart[]',
								'id' => 'customer_from_date',
								'size' => '20',
								'class' => "text field " 
						),
						'',
						'tOOL TIP',
						'',
						'start_date[start_date-date]' 
				),
				array (
						'To Date',
						'INPUT',
						array (
								'name' => 'callstart[]',
								'id' => 'customer_to_date',
								'size' => '20',
								'class' => "text field " 
						),
						'',
						'tOOL TIP',
						'',
						'end_date[end_date-date]' 
				),
				array (
						'Account',
						'provider_id',
						'SELECT',
						'',
						'',
						'tOOL TIP',
						'Please Enter account number',
						'id',
						'IF(`deleted`=1,concat( first_name, " ", last_name, " ", "(", number, ")^" ),concat( first_name, " ", last_name, " ", "(", number, ")" )) as number',
						'accounts',
						'build_dropdown_deleted',
						'where_arr',
						array (
								"reseller_id" => "0",
								"type" => "3" 
						) 
				),
				array (
						'Trunk',
						'trunk_id',
						'SELECT',
						'',
						'',
						'tOOL TIP',
						'Please Enter account number',
						'id',
						'IF(`status`=2, concat(name,"","^"),name) as name',
						'trunks',
						'build_dropdown_deleted',
						'',
						array (
								"status" => "1" 
						) 
				),
				array (
						'Code ',
						'INPUT',
						array (
								'name' => 'pattern[pattern]',
								'value' => '',
								'size' => '20',
								'class' => "text field " 
						),
						'',
						'Tool tips info',
						'1',
						'pattern[pattern-string]',
						'',
						'',
						'',
						'search_string_type',
						'' 
				),
				array (
						' Code Destination ',
						'INPUT',
						array (
								'name' => 'notes[notes]',
								'value' => '',
								'size' => '20',
								'class' => "text field " 
						),
						'',
						'Tool tips info',
						'1',
						'notes[notes-string]',
						'',
						'',
						'',
						'search_string_type',
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
				'id' => "providersummary_search_btn",
				'content' => 'Search',
				'value' => 'save',
				'type' => 'button',
				'class' => 'btn btn-line-parrot pull-right' 
		);
		$form ['button_reset'] = array (
				'name' => 'action',
				'id' => "id_reset",
				'content' => 'Clear',
				'value' => 'cancel',
				'type' => 'reset',
				'class' => 'btn btn-line-sky pull-right margin-x-10' 
		);
		
		return $form;
	}
	function build_providersummary() {
		$account_info = $accountinfo = $this->CI->session->userdata ( 'accountinfo' );
		$currency_id = $account_info ['currency_id'];
		$currency = $this->CI->common->get_field_name ( 'currency', 'currency', $currency_id );
		
		$new_arr = array ();
		if ($this->CI->session->userdata ( 'advance_search' ) == '1') {
			$search_array = $this->CI->session->userdata ( 'providersummary_reports_search' );
			if (isset ( $search_array ['time'] ) && ! empty ( $search_array ['time'] )) {
				$new_arr [] = array (
						$search_array ['time'],
						"151",
						$search_array ['time'] . "(callstart)",
						"",
						"",
						"" 
				);
			}
			if (isset ( $search_array ['groupby_1'] ) && ! empty ( $search_array ['groupby_1'] )) {
				$first_column_groupby = $search_array ['groupby_1'];
				if ($first_column_groupby == 'provider_id') {
					$new_arr [] = array (
							"Account",
							"151",
							"provider_id",
							"first_name,last_name,number",
							"accounts",
							"build_concat_string" 
					);
				} elseif ($first_column_groupby == 'pattern') {
					$new_arr [] = array (
							"Code",
							"65",
							"pattern",
							"pattern",
							"",
							"get_only_numeric_val" 
					);
					$new_arr [] = array (
							"Destination",
							"85",
							"notes",
							"",
							"",
							"" 
					);
				} elseif ($first_column_groupby == 'trunk_id') {
					$new_arr [] = array (
							"Trunk",
							"151",
							"trunk_id",
							"name",
							"trunks",
							"get_field_name" 
					);
				} elseif ($first_column_groupby == 'package_id') {
					$new_arr [] = array (
							"Package",
							"151",
							"package_id",
							"first_name,last_name,number",
							"accounts",
							"build_concat_string" 
					);
				}
			}
			if (isset ( $search_array ['groupby_2'] ) && ! empty ( $search_array ['groupby_2'] )) {
				$third_column_groupby = $search_array ['groupby_2'];
				if ($third_column_groupby == 'provider_id') {
					$new_arr [] = array (
							"Account",
							"151",
							"provider_id",
							"first_name,last_name,number",
							"accounts",
							"build_concat_string" 
					);
				} elseif ($third_column_groupby == 'pattern') {
					$new_arr [] = array (
							"Code",
							"65",
							"pattern",
							"pattern",
							"",
							"get_only_numeric_val" 
					);
					$new_arr [] = array (
							"Destination",
							"85",
							"notes",
							"",
							"",
							"" 
					);
				} elseif ($third_column_groupby == 'trunk_id') {
					$new_arr [] = array (
							"Trunk",
							"151",
							"trunk_id",
							"name",
							"trunks",
							"get_field_name" 
					);
				} elseif ($third_column_groupby == 'package_id') {
					$new_arr [] = array (
							"Package",
							"151",
							"package_id",
							"first_name,last_name,number",
							"accounts",
							"build_concat_string" 
					);
				}
			}
			if (isset ( $search_array ['groupby_3'] ) && ! empty ( $search_array ['groupby_3'] )) {
				$fifth_column_groupby = $search_array ['groupby_3'];
				if ($fifth_column_groupby == 'provider_id') {
					$new_arr [] = array (
							"Account",
							"151",
							"provider_id",
							"first_name,last_name,number",
							"accounts",
							"build_concat_string" 
					);
				} elseif ($fifth_column_groupby == 'pattern') {
					$new_arr [] = array (
							"Code",
							"65",
							"pattern",
							"pattern",
							"",
							"get_only_numeric_val" 
					);
					$new_arr [] = array (
							"Destination",
							"85",
							"notes",
							"",
							"",
							"" 
					);
				} elseif ($fifth_column_groupby == 'trunk_id') {
					$new_arr [] = array (
							"Trunk",
							"151",
							"trunk_id",
							"name",
							"trunks",
							"get_field_name" 
					);
				} elseif ($fifth_column_groupby == 'package_id') {
					$new_arr [] = array (
							"Package",
							"151",
							"package_id",
							"first_name,last_name,number",
							"accounts",
							"build_concat_string" 
					);
				}
			}
		}
		if (empty ( $new_arr ))
			$new_arr [] = array (
					"Account",
					"453",
					"provider_id",
					"first_name,last_name,number",
					"accounts",
					"build_concat_string" 
			);
		$fixed_arr = array (
				array (
						"Attempted Calls",
						"130",
						"attempted_calls",
						"",
						"",
						"" 
				),
				array (
						"Completed Calls",
						"130",
						"description",
						"",
						"",
						"" 
				),
				array (
						"Duration",
						"85",
						"billable",
						'',
						'',
						'' 
				),
				array (
						"ASR",
						"83",
						"asr",
						'',
						'',
						'' 
				),
				array (
						"ACD",
						"83",
						"acd  ",
						'',
						'',
						'' 
				),
				array (
						"MCD",
						"83",
						"mcd",
						'',
						'',
						'' 
				),
				array (
						"Billable",
						"102",
						"billable",
						'',
						'',
						'' 
				),
				array (
						"Cost($currency)",
						"117",
						"cost",
						'',
						'',
						'' 
				) 
		);
		$grid_field_arr = json_encode ( array_merge ( $new_arr, $fixed_arr ) );
		return $grid_field_arr;
	}
	function build_grid_buttons_providersummary() {
		$buttons_json = json_encode ( array (
				array (
						"Export",
						"btn btn-xing",
						" fa fa-download fa-lg",
						"button_action",
						"/summary/provider_export_csv/",
						'single' 
				) 
		) );
		return $buttons_json;
	}
	function get_resellersummary_search_form() {
		$form ['forms'] = array (
				"",
				array (
						'id' => "resellersummary_search" 
				) 
		);
		$form ['Search'] = array (
				array (
						'From Date',
						'INPUT',
						array (
								'name' => 'callstart[]',
								'id' => 'customer_from_date',
								'size' => '20',
								'class' => "text field " 
						),
						'',
						'tOOL TIP',
						'',
						'start_date[start_date-date]' 
				),
				array (
						'To Date',
						'INPUT',
						array (
								'name' => 'callstart[]',
								'id' => 'customer_to_date',
								'size' => '20',
								'class' => "text field " 
						),
						'',
						'tOOL TIP',
						'',
						'end_date[end_date-date]' 
				),
				array (
						'Account',
						'reseller_id',
						'SELECT',
						'',
						'',
						'tOOL TIP',
						'Please Enter account number',
						'id',
						'IF(`deleted`=1,concat( first_name, " ", last_name, " ", "(", number, ")^" ),concat( first_name, " ", last_name, " ", "(", number, ")" )) as number',
						'accounts',
						'build_dropdown_deleted',
						'where_arr',
						array (
								"reseller_id" => "0",
								"type" => "1" 
						) 
				),
				array (
						'Code ',
						'INPUT',
						array (
								'name' => 'pattern[pattern]',
								'value' => '',
								'size' => '20',
								'class' => "text field " 
						),
						'',
						'Tool tips info',
						'1',
						'pattern[pattern-string]',
						'',
						'',
						'',
						'search_string_type',
						'' 
				),
				array (
						'Destination ',
						'INPUT',
						array (
								'name' => 'notes[notes]',
								'value' => '',
								'size' => '20',
								'class' => "text field " 
						),
						'',
						'Tool tips info',
						'1',
						'notes[notes-string]',
						'',
						'',
						'',
						'search_string_type',
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
				'id' => "resellersummary_search_btn",
				'content' => 'Search',
				'value' => 'save',
				'type' => 'button',
				'class' => 'btn btn-line-parrot pull-right' 
		);
		$form ['button_reset'] = array (
				'name' => 'action',
				'id' => "id_reset",
				'content' => 'Clear',
				'value' => 'cancel',
				'type' => 'reset',
				'class' => 'btn btn-line-sky pull-right margin-x-10' 
		);
		
		return $form;
	}
	function build_resellersummary($new_column_arr) {
		$account_info = $accountinfo = $this->CI->session->userdata ( 'accountinfo' );
		$currency_id = $account_info ['currency_id'];
		$currency = $this->CI->common->get_field_name ( 'currency', 'currency', $currency_id );
		
		$column_arr = array (
				array (
						"Attempted Calls",
						"120",
						"attempted_calls",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				array (
						"Completed Calls",
						"120",
						"description",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				array (
						"Duration",
						"91",
						"billable",
						'',
						'',
						'',
						"",
						"true",
						"center" 
				),
				array (
						"ASR",
						"78",
						"asr",
						'',
						'',
						'',
						"",
						"true",
						"center" 
				),
				array (
						"ACD",
						"78",
						"acd  ",
						'',
						'',
						'',
						"",
						"true",
						"center" 
				),
				array (
						"MCD",
						"78",
						"mcd",
						'',
						'',
						'',
						"",
						"true",
						"center" 
				),
				array (
						"Billable",
						"80",
						"billable",
						'',
						'',
						'',
						"",
						"true",
						"center" 
				),
				array (
						"Debit($currency)",
						"100",
						"cost",
						'',
						'',
						'',
						"",
						"true",
						"right" 
				),
				array (
						"Cost($currency)",
						"100",
						"price",
						'',
						'',
						'',
						"",
						"true",
						"right" 
				),
				array (
						"Profit($currency)",
						"100",
						"profit",
						"",
						"",
						"",
						"",
						"true",
						"right" 
				) 
		);
		$grid_field_arr = json_encode ( array_merge ( $new_column_arr, $column_arr ) );
		return $grid_field_arr;
	}
	function build_grid_buttons_resellersummary() {
		$buttons_json = json_encode ( array (
				array (
						"Export",
						"btn btn-xing",
						" fa fa-download fa-lg",
						"button_action",
						"/summary/reseller_export_csv/",
						'single' 
				) 
		) );
		return $buttons_json;
	}
	function get_customersummary_search_form() {
		$form ['forms'] = array (
				base_url () . 'summary/customer_search',
				array (
						'id' => "customersummary_search",
						"name" => "customersummary_search" 
				) 
		);
		$form ['Search'] = array (
				array (
						'From Date',
						'INPUT',
						array (
								'name' => 'callstart[]',
								'id' => 'customer_from_date',
								'size' => '20',
								'class' => "text field " 
						),
						'',
						'tOOL TIP',
						'',
						'start_date[start_date-date]' 
				),
				array (
						'To Date',
						'INPUT',
						array (
								'name' => 'callstart[]',
								'id' => 'customer_to_date',
								'size' => '20',
								'class' => "text field " 
						),
						'',
						'tOOL TIP',
						'',
						'end_date[end_date-date]' 
				),
				array (
						'Accounts',
						'accountid',
						'SELECT',
						'',
						'',
						'tOOL TIP',
						'Please Enter account number',
						'id',
						'IF(`deleted`=1,concat( first_name, " ", last_name, " ", "(", number, ")^" ),concat( first_name, " ", last_name, " ", "(", number, ")" )) as number',
						'accounts',
						'build_dropdown_deleted',
						'where_arr',
						array (
								"reseller_id" => "0",
								"type" => "GLOBAL" 
						) 
				),
				array (
						'Code ',
						'INPUT',
						array (
								'name' => 'pattern[pattern]',
								'value' => '',
								'size' => '20',
								'class' => "text field " 
						),
						'',
						'Tool tips info',
						'1',
						'pattern[pattern-string]',
						'',
						'',
						'',
						'search_string_type',
						'' 
				),
				array (
						'Code Destination ',
						'INPUT',
						array (
								'name' => 'notes[notes]',
								'value' => '',
								'size' => '20',
								'class' => "text field " 
						),
						'',
						'Tool tips info',
						'1',
						'notes[notes-string]',
						'',
						'',
						'',
						'search_string_type',
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
		$form ['Group'] = array (
				array (
						'Group By #1',
						'groupby_1',
						'SELECT',
						'',
						'',
						'tOOL TIP',
						'',
						'',
						'',
						'',
						'set_summarycustomer_groupby' 
				),
				array (
						'Group By #2',
						'groupby_2',
						'SELECT',
						'',
						'',
						'tOOL TIP',
						'',
						'',
						'',
						'',
						'set_summarycustomer_groupby' 
				),
				array (
						'Group By #3',
						'groupby_3',
						'SELECT',
						'',
						'',
						'tOOL TIP',
						'',
						'',
						'',
						'',
						'set_summarycustomer_groupby' 
				) 
		);
		$form ['button_search'] = array (
				'name' => 'action',
				'id' => "customersummary_search_btn",
				'content' => 'Search',
				'value' => 'save',
				'type' => 'button',
				'class' => 'btn btn-line-parrot pull-right' 
		);
		$form ['button_reset'] = array (
				'name' => 'action',
				'id' => "id_reset",
				'content' => 'Clear',
				'value' => 'cancel',
				'type' => 'reset',
				'class' => 'btn btn-line-sky pull-right margin-x-10' 
		);
		
		return $form;
	}
	function build_customersummary($new_column_arr) {
		$account_info = $accountinfo = $this->CI->session->userdata ( 'accountinfo' );
		$currency_id = $account_info ['currency_id'];
		$currency = $this->CI->common->get_field_name ( 'currency', 'currency', $currency_id );
		
		$column_arr = array (
				array (
						"Attempted Calls",
						"120",
						"attempted_calls",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				array (
						"Completed Calls",
						"120",
						"description",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				array (
						"Duration",
						"95",
						"billable",
						'',
						'',
						'',
						"",
						"true",
						"center" 
				),
				array (
						"ASR",
						"85",
						"asr",
						'',
						'',
						'',
						"",
						"true",
						"center" 
				),
				array (
						"ACD",
						"85",
						"acd  ",
						'',
						'',
						'',
						"",
						"true",
						"center" 
				),
				array (
						"MCD",
						"85",
						"mcd",
						'',
						'',
						'',
						"",
						"true",
						"center" 
				),
				array (
						"Billable",
						"90",
						"billable",
						'',
						'',
						'',
						"",
						"true",
						"right" 
				),
				array (
						"Debit($currency)",
						"87",
						"cost",
						'',
						'',
						'',
						"",
						"true",
						"right" 
				),
				array (
						"Cost($currency)",
						"85",
						"price",
						'',
						'',
						'',
						"",
						"true",
						"right" 
				),
				array (
						"Profit($currency)",
						"93",
						"profit",
						"",
						"",
						"",
						"",
						"true",
						"right" 
				) 
		);
		$grid_field_arr = json_encode ( array_merge ( $new_column_arr, $column_arr ) );
		return $grid_field_arr;
	}
	function build_grid_buttons_customersummary() {
		$buttons_json = json_encode ( array (
				array (
						"Export",
						"btn btn-xing",
						" fa fa-download fa-lg",
						"button_action",
						"/summary/customer_export_csv/",
						'single' 
				) 
		) );
		return $buttons_json;
	}
}

?>
