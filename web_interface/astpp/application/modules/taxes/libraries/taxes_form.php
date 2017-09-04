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
class Taxes_form {
	function __construct() {
		$this->CI = & get_instance ();
	}
	function get_taxes_form_fields() {
		$form ['forms'] = array (
				base_url () . 'taxes/taxes_save/',
				array (
						'id' => 'taxes_form',
						'method' => 'POST',
						'name' => 'taxes_form' 
				) 
		);
		$form [gettext ( 'Tax Information' )] = array (
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
								'name' => 'taxes_description',
								'size' => '20',
								'class' => "text field medium" 
						),
						'trim|required',
						'tOOL TIP',
						'' 
				),
				array (
						gettext ( 'Priority' ),
						'INPUT',
						array (
								'name' => 'taxes_priority',
								'size' => '20',
								'maxlength' => '5',
								'class' => "text field medium" 
						),
						'trim|required|numeric',
						'tOOL TIP',
						'' 
				),
				array (
						gettext ( 'Amount' ),
						'INPUT',
						array (
								'name' => 'taxes_amount',
								'size' => '20',
								'maxlength' => '20',
								'class' => "text field medium" 
						),
						'trim|numeric|xss_clean',
						'tOOL TIP',
						'' 
				),
				array (
						gettext ( 'Rate(%)' ),
						'INPUT',
						array (
								'name' => 'taxes_rate',
								'size' => '20',
								'maxlength' => '20',
								'class' => "text field medium" 
						),
						'trim|numeric|xss_clean|less_than[100]|',
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
				'content' => gettext ( 'Close' ),
				'value' => 'cancel',
				'type' => 'button',
				'class' => 'btn btn-line-sky margin-x-10',
				'onclick' => 'return redirect_page(\'NULL\')' 
		);
		return $form;
	}
	function get_search_taxes_form() {
		$form ['forms'] = array (
				"",
				array (
						'id' => "taxes_search" 
				) 
		);
		$form [gettext ( 'Search' )] = array (
				array (
						gettext ( 'Name' ),
						'INPUT',
						array (
								'name' => 'taxes_description[taxes_description]',
								'',
								'size' => '20',
								'class' => "text field " 
						),
						'',
						'tOOL TIP',
						'1',
						'taxes_description[taxes_description-string]',
						'',
						'',
						'',
						'search_string_type',
						'' 
				),
				array (
						gettext ( 'Amount' ),
						'INPUT',
						array (
								'name' => 'taxes_amount[taxes_amount]',
								'value' => '',
								'size' => '20',
								'class' => "text field " 
						),
						'',
						'Tool tips info',
						'1',
						'taxes_amount[taxes_amount-integer]',
						'',
						'',
						'',
						'search_int_type',
						'' 
				),
				array (
						gettext ( 'Rate(%)' ),
						'INPUT',
						array (
								'name' => 'taxes_rate[taxes_rate]',
								'value' => '',
								'size' => '20',
								'class' => "text field " 
						),
						'',
						'Tool tips info',
						'1',
						'taxes_rate[taxes_rate-integer]',
						'',
						'',
						'',
						'search_int_type',
						'' 
				),
				array (
						'Status',
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
				'id' => "taxes_search_btn",
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
	function build_taxes_list_for_admin() {
		$account_info = $accountinfo = $this->CI->session->userdata ( 'accountinfo' );
		$currency_id = $account_info ['currency_id'];
		$currency = $this->CI->common->get_field_name ( 'currency', 'currency', $currency_id );
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
						gettext ( "Name" ),
						"270",
						"taxes_description",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Priority" ),
						"145",
						"taxes_priority",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Amount($currency)" ),
						"260",
						"taxes_amount",
						"taxes_amount",
						"taxes_amount",
						"convert_to_currency",
						"",
						"true",
						"right" 
				),
				array (
						gettext ( "Rate(%)" ),
						"230",
						"taxes_rate",
						"",
						"",
						"",
						"",
						"true",
						"right" 
				),
				array (
						gettext ( "Status" ),
						"160",
						"status",
						"status",
						"taxes",
						"get_status",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Action" ),
						"175",
						"",
						"",
						"",
						array (
								"EDIT" => array (
										"url" => "taxes/taxes_edit/",
										"mode" => "popup" 
								),
								"DELETE" => array (
										"url" => "taxes/taxes_delete/",
										"mode" => "single" 
								) 
						) 
				) 
		) );
		return $grid_field_arr;
	}
	function build_grid_buttons() {
		$buttons_json = json_encode ( array (
				array (
						gettext ( "Create" ),
						"btn btn-line-warning btn",
						"fa fa-plus-circle fa-lg",
						"button_action",
						"/taxes/taxes_add/",
						"popup" 
				),
				array (
						gettext ( "Delete" ),
						"btn btn-line-danger",
						"fa fa-times-circle fa-lg",
						"button_action",
						"/taxes/taxes_delete_multiple/" 
				) 
		) );
		return $buttons_json;
	}
}

?>
