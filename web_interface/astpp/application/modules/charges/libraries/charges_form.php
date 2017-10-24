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
class Charges_form {
	function __construct($library_name = '') {
		$this->CI = & get_instance ();
	}
	function get_charge_form_fields() {
		$form ['forms'] = array (
				base_url () . 'charges/periodiccharges_save/',
				array (
						'id' => 'charges_form',
						'method' => 'POST',
						'name' => 'charges_form' 
				) 
		);
		$form [gettext ( 'Information' )] = array (
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
						gettext ( 'Name' ),
						'INPUT',
						array (
								'name' => 'description',
								'size' => '20',
								'class' => "text field medium" 
						),
						'trim|required|xss_clean',
						'tOOL TIP',
						'Please Enter account number' 
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
								"reseller_id" => "0",
								"status <>" => "2" 
						) 
				),
				array (
						gettext ( 'Amount' ),
						'INPUT',
						array (
								'name' => 'charge',
								'size' => '20',
								'class' => "text field medium" 
						),
						'trim|required|numeric|greater_than[0]|currency_decimal|xss_clean',
						'tOOL TIP',
						'Please Enter account number' 
				),
				array (
						gettext ( 'Prorate' ),
						'pro_rate',
						'SELECT',
						'',
						'',
						'tOOL TIP',
						'Please Select Pro rate',
						'',
						'',
						'',
						'set_prorate' 
				),
				array (
						gettext ( 'Bill cycle' ),
						'sweep_id',
						'SELECT',
						'',
						'',
						'tOOL TIP',
						'Please Enter account number',
						'id',
						'sweep',
						'sweeplist',
						'build_dropdown',
						'',
						'' 
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
	function get_charges_search_form() {
		$logintype = $this->CI->session->userdata ( 'logintype' );
		if ($logintype == 1 || $logintype == 5) {
			$account_data = $this->CI->session->userdata ( "accountinfo" );
			$loginid = $account_data ['id'];
		} else {
			$loginid = "0";
		}
		$form ['forms'] = array (
				"",
				array (
						'id' => "charges_search" 
				) 
		);
		$form [gettext ( 'Search' )] = array (
				array (
						gettext ( 'Name' ),
						'INPUT',
						array (
								'name' => 'description[description]',
								'',
								'size' => '20',
								'class' => "text field" 
						),
						'',
						'tOOL TIP',
						'1',
						'description[description-string]',
						'',
						'',
						'',
						'search_string_type',
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
								'status' => 0,
								'reseller_id' => $loginid 
						) 
				),
				array (
						gettext ( 'Amount' ),
						'INPUT',
						array (
								'name' => 'charge[charge]',
								'value' => '',
								'size' => '20',
								'class' => "text field" 
						),
						'',
						'Tool tips info',
						'1',
						'charge[charge-integer]',
						'',
						'',
						'',
						'search_int_type',
						'' 
				),
				array (
						gettext ( 'Billing Cycle' ),
						'sweep_id',
						'SELECT',
						'',
						'',
						'tOOL TIP',
						'Please Enter account number',
						'id',
						'sweep',
						'sweeplist',
						'build_dropdown',
						'',
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
						'set_search_status' 
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
				'id' => "charges_search_btn",
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
	/**
	 * ASTPP 3.0
	 * Change size of grind
	 */
	function build_charge_list_for_admin() {
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
						gettext ( "Name" ),
						"140",
						"description",
						"",
						"",
						"",
						"EDITABLE",
						"true",
						"center" 
				),
				/**
				 * ***********************************************
				 */
				array (
						gettext ( "Rate Group" ),
						"140",
						"pricelist_id",
						"name",
						"pricelists",
						"get_field_name",
						"",
						"true",
						"center" 
				),
				array (
						"Amount($currency)",
						"125",
						"charge",
						"charge",
						"charge",
						"convert_to_currency",
						"",
						"true",
						"right" 
				),
				array (
						gettext ( "Prorate" ),
						"125",
						"pro_rate",
						"pro_rate",
						"charges",
						"get_prorate",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Billing Cycle" ),
						"130",
						"sweep_id",
						"sweep",
						"sweeplist",
						"get_field_name",
						"",
						"true",
						"center" 
				),
				/**
				 * ASTPP 3.0
				 * Show Creation date,last_modified date in reseller grid with admin login
				 */
				array (
						gettext ( "Status" ),
						"125",
						"status",
						"status",
						"charges",
						"get_status",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Created Date" ),
						"150",
						"creation_date",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Modified Date" ),
						"150",
						"last_modified_date",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				/**
				 * *********************************************
				 */
				/*
				 * ASTPP 3.0
				 * change status active or inactive.
				 */
				
				/**
				 * ********************************************
				 */
				array (
						gettext ( "Action" ),
						"150",
						"",
						"",
						"",
						array (
								"EDIT" => array (
										"url" => "charges/periodiccharges_edit/",
										"mode" => "popup",
										'popup' 
								),
								"DELETE" => array (
										"url" => "charges/periodiccharges_delete/",
										"mode" => "single" 
								) 
						) 
				) 
		) );
		return $grid_field_arr;
	}
	/**
	 * *****************************************
	 */
	function build_grid_buttons() {
		$buttons_json = json_encode ( array (
				array (
						"Create",
						"btn btn-line-warning btn",
						"fa fa-plus-circle fa-lg",
						"button_action",
						"/charges/periodiccharges_add/",
						"popup" 
				),
				array (
						gettext ( "Delete" ),
						"btn btn-line-danger",
						"fa fa-times-circle fa-lg",
						"button_action",
						"/charges/periodiccharges_delete_multiple/" 
				) 
		) );
		return $buttons_json;
	}
	function build_charges_list_for_customer($accountid, $accounttype) {
		$account_info = $accountinfo = $this->CI->session->userdata ( 'accountinfo' );
		$currency_id = $account_info ['currency_id'];
		$currency = $this->CI->common->get_field_name ( 'currency', 'currency', $currency_id );
		
		$grid_field_arr = json_encode ( array (
				array (
						"Name",
						"140",
						"description",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Amount($currency)" ),
						"140",
						"charge",
						"charge",
						"charge",
						"convert_to_currency",
						"",
						"true",
						"right" 
				),
				array (
						gettext ( "Prorate" ),
						"125",
						"pro_rate",
						"pro_rate",
						"charges",
						"get_prorate",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Billing Cycle" ),
						"170",
						"sweep_id",
						"sweep",
						"sweeplist",
						"get_field_name",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Created Date" ),
						"150",
						"creation_date",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Modified Date" ),
						"150",
						"last_modified_date",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Action" ),
						"150",
						"",
						"",
						"",
						array (
								"DELETE" => array (
										"url" => "accounts/" . $accounttype . "_subscription_action/delete/$accountid/$accounttype/",
										"mode" => "single" 
								) 
						) 
				) 
		) );
		return $grid_field_arr;
	}
}

?>
