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
if (! defined ( 'BASEPATH' )) {
	exit ( 'No direct script access allowed' );
}
class User_form {
	function __construct($library_name = '') {
		$this->CI = & get_instance ();
	}
	function build_packages_list_for_user() {
		$grid_field_arr = json_encode ( array (
				array (
						gettext ( "Name" ),
						"310",
						"package_name",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Rate Group" ),
						"250",
						"pricelist_id",
						"name",
						"pricelists",
						"get_field_name",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Included Seconds" ),
						"260",
						"includedseconds",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Status" ),
						"160",
						"status",
						"status",
						"status",
						"get_status",
						"",
						"true",
						"center" 
				) 
		) );
		return $grid_field_arr;
	}
	function build_refill_list_for_user() {
		$grid_field_arr = json_encode ( array (
				
				array (
						gettext ( "Date" ),
						"225",
						"payment_date",
						"",
						"",
						"" 
				),
				array (
						gettext ( "Amount" ),
						"250",
						"credit",
						"credit",
						"credit",
						"convert_to_currency" 
				),
				array (
                                                gettext ( "Refill By" ),
                                                "230",
                                                "payment_by",
                                                "first_name,last_name,number",
                                                "accounts",
                                                "get_refill_by"
                                ),
				array (
						gettext ( "Note" ),
						"290",
						"notes",
						"",
						"",
						"" 
				) 
		) );
		return $grid_field_arr;
	}
	function build_emails_list_for_user() {
		$grid_field_arr = json_encode ( array (
				array (
						gettext ( "Date" ),
						"110",
						"date",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "From" ),
						"170",
						"from",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Body" ),
						"550",
						"body",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Attachement" ),
						"100",
						"attachment",
						"attachment",
						"attachment",
						"attachment_icons",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Status" ),
						"100",
						"status",
						"status",
						"status",
						"email_status",
						"",
						"true",
						"center" 
				) 
		) );
		return $grid_field_arr;
	}
	function build_user_emails_search() {
		$form ['forms'] = array (
				"",
				array (
						'id' => "user_emails_search" 
				) 
		);
		$form ['Search'] = array (
				array (
						gettext ( 'From Date' ),
						'INPUT',
						array (
								'name' => 'date[]',
								'id' => 'customer_cdr_from_date',
								'size' => '20',
								'class' => "text field " 
						),
						'',
						'tOOL TIP',
						'',
						'date[date-date]' 
				),
				array (
						gettext ( 'To Date' ),
						'INPUT',
						array (
								'name' => 'date[]',
								'id' => 'customer_cdr_to_date',
								'size' => '20',
								'class' => "text field " 
						),
						'',
						'tOOL TIP',
						'',
						'date[date-date]' 
				),
				array (
						gettext ( 'From' ),
						'INPUT',
						array (
								'name' => 'from[from]',
								'',
								'id' => 'from',
								'size' => '20',
								'class' => "text field " 
						),
						'',
						'tOOL TIP',
						'1',
						'from[from-string]',
						'',
						'',
						'',
						'search_string_type',
						'' 
				),
				array (
						gettext ( 'Body' ),
						'INPUT',
						array (
								'name' => 'body[body]',
								'',
								'id' => 'body',
								'size' => '20',
								'class' => "text field " 
						),
						'',
						'tOOL TIP',
						'1',
						'body[body-string]',
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
				'id' => "user_email_search_btn",
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
	function get_userprofile_form_fields($dataArr = false) {
		if ($dataArr ['id'] > 0)
			$val = 'accounts.email.' . $dataArr ['id'];
		else
			$val = 'accounts.email';
		$uname = $this->CI->common->find_uniq_rendno ( common_model::$global_config ['system_config'] ['cardlength'], 'number', 'accounts' );
		$password = $this->CI->common->generate_password ();
		$logintype = $this->CI->session->userdata ( 'logintype' );
		$pin = ($logintype == '0') ? array (
				gettext ( 'Pin' ),
				'INPUT',
				array (
						'name' => 'pin',
						'size' => '20',
						'class' => "text field medium" 
				),
				'tOOL TIP',
				'' 
		) : array (
				'',
				'HIDDEN',
				array (
						'name' => 'Pin' 
				),
				'',
				'',
				'',
				'' 
		);
		$form ['forms'] = array (
				base_url () . 'user/user_myprofile/',
				array (
						"id" => "user_form",
						"name" => "user_form" 
				) 
		);
		
		$form ['User Profile'] = array (
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
						'',
						'HIDDEN',
						array (
								'name' => 'type',
								'value' => '0' 
						),
						'',
						'',
						'' 
				),
				array (
						gettext ( 'Account Number' ),
						'INPUT',
						array (
								'name' => 'number',
								'value' => $uname,
								'size' => '20',
								'readonly' => true,
								'class' => "text field medium" 
						),
						'',
						'tOOL TIP',
						'Please Enter account number' 
				),
				$pin,
				array (
						gettext ( 'Company' ),
						'INPUT',
						array (
								'name' => 'company_name',
								'size' => '15',
								'class' => 'text field medium' 
						),
						'trim|xss_clean',
						'tOOL TIP',
						'' 
				),
				array (
						gettext ( 'First Name' ),
						'INPUT',
						array (
								'name' => 'first_name',
								'id' => 'first_name',
								'size' => '15',
								'class' => "text field medium" 
						),
						'trim|required|xss_clean',
						'tOOL TIP',
						'Please Enter account number' 
				),
				array (
						gettext ( 'Last Name' ),
						'INPUT',
						array (
								'name' => 'last_name',
								'size' => '15',
								'class' => "text field medium" 
						),
						'trim|alpha_numeric_space|xss_clean',
						'tOOL TIP',
						'Please Enter Password' 
				),
				array (
						gettext ( 'Telephone 1' ),
						'INPUT',
						array (
								'name' => 'telephone_1',
								'size' => '15',
								'class' => "text field medium" 
						),
						'phn_number',
						'tOOL TIP',
						'Please Enter Password' 
				),
				array (
						gettext ( 'Telephone 2' ),
						'INPUT',
						array (
								'name' => 'telephone_2',
								'size' => '15',
								'class' => "text field medium" 
						),
						'phn_number',
						'tOOL TIP',
						'Please Enter Password' 
				),
				array (
						gettext ( 'Email' ),
						'INPUT',
						array (
								'name' => 'email',
								'size' => '50',
								'class' => "text field medium" 
						),
						'required|valid_email|is_unique[' . $val . ']',
						'tOOL TIP',
						'Please Enter Password' 
				),
				array (
						gettext ( 'Address 1' ),
						'INPUT',
						array (
								'name' => 'address_1',
								'size' => '15',
								'class' => "text field medium" 
						),
						'',
						'tOOL TIP',
						'Please Enter Password' 
				),
				array (
						gettext ( 'Address 2' ),
						'INPUT',
						array (
								'name' => 'address_2',
								'size' => '15',
								'class' => "text field medium" 
						),
						'',
						'tOOL TIP',
						'Please Enter Password' 
				),
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
						gettext ( 'Province/State' ),
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
						gettext ( 'Zip/Postal Code' ),
						'INPUT',
						array (
								'name' => 'postal_code',
								'size' => '15',
								'class' => "text field medium" 
						),
						'trim|xss_clean',
						'tOOL TIP',
						'Please Enter Password' 
				),
				array (
						gettext ( 'Country' ),
						array (
								'name' => 'country_id',
								'class' => 'country_id' 
						),
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
						gettext ( 'Timezone' ),
						array (
								'name' => 'timezone_id',
								'class' => 'timezone_id' 
						),
						'SELECT',
						'',
						'',
						'tOOL TIP',
						'Please Enter account number',
						'id',
						'gmtzone',
						'timezone',
						'build_dropdown',
						'',
						'' 
				),
				array (
						gettext ( 'Tax Number' ),
						'INPUT',
						array (
								'name' => 'tax_number',
								'size' => '100',
								'class' => "text field medium" 
						),
						'',
						'tOOL TIP',
						'' 
				)
		);
		$form ['button_save'] = array (
				'name' => 'action',
				'content' => gettext ( 'Save' ),
				'value' => 'save',
				'type' => 'submit',
				'class' => 'btn btn-line-parrot' 
		);
		
		return $form;
	}
	function get_userprofile_change_password() {
		$form ['forms'] = array (
				base_url () . 'user/user_change_password/',
				array (
						"id" => "customer_alert_threshold",
						"name" => "user_change_password" 
				) 
		);
		$form [gettext ( 'Change Password' )] = array (
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
						gettext ( 'Old Password' ),
						'PASSWORD',
						array (
								'name' => 'password',
								'size' => '20',
								'class' => "text field medium",
								'id' => 'old_password_show',
								'onmouseover' => 'seetext(old_password_show)',
								'onmouseout' => 'hidepassword(old_password_show)' 
						),
						'required|password_check[accounts]',
						'tOOL TIP',
						'',
						'' 
				),
				array (
						gettext ( 'New Password' ),
						'PASSWORD',
						array (
								'name' => 'new_password',
								'size' => '20',
								'class' => "text field medium",
								'id' => 'new_password_show',
								'onmouseover' => 'seetext(new_password_show)',
								'onmouseout' => 'hidepassword(new_password_show)' 
						),
						'required|',
						'tOOL TIP',
						'',
						'' 
				),
				array (
						gettext ( 'Confirm Password' ),
						'PASSWORD',
						array (
								'name' => 'new_confirm_password',
								'size' => '20',
								'class' => "text field medium",
								'id' => 'password_show',
								'onmouseover' => 'seetext(password_show)',
								'onmouseout' => 'hidepassword(password_show)' 
						),
						"required|matches[new_password]",
						'tOOL TIP',
						'',
						'' 
				) 
		);
		$form ['button_save'] = array (
				'name' => 'action',
				'content' => gettext ( 'Save' ),
				'value' => 'save',
				'type' => 'submit',
				'class' => 'btn btn-line-parrot' 
		);
		return $form;
	}
	function build_user_invoices() {
		$account_info = $accountinfo = $this->CI->session->userdata ( 'accountinfo' );
		$currency_id = $account_info ['currency_id'];
		$currency = $this->CI->common->get_field_name ( 'currency', 'currency', $currency_id );
		$url = ($this->CI->session->userdata ( 'logintype' ) == 0) ? "/user/user_invoice_download/" : '/invoices/invoice_main_download/';
		$grid_field_arr = json_encode ( array (
				array (
						gettext ( "Number" ),
						"110",
						"id",
						"id,'',type",
						"invoices",
						"build_concat_string",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Type" ),
						"100",
						"id",
						"id,'',type",
						"invoices",
						"build_concat_string",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Generated Date" ),
						"110",
						"invoice_date",
						"invoice_date",
						"",
						"get_invoice_date",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "From Date" ),
						"100",
						"from_date",
						"from_date",
						"",
						"get_from_date",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Due Date" ),
						"100",
						"",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Last Pay Date" ),
						"100",
						"",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Amount" ) . "($currency)",
						"100",
						"id",
						"id",
						"id",
						"get_invoice_total",
						"",
						"true",
						"right" 
				),
				array (
						gettext ( "Outstanding Amount" ) . "<br>($currency)",
						"150",
						"",
						"",
						"",
						"",
						"",
						"true",
						"right" 
				),
				array (
						gettext ( "Action" ),
						"140",
						"",
						"",
						"",
						array (
								"DOWNLOAD" => array (
										"url" => $url,
										"mode" => "single" 
								) 
						) 
				) 
		) );
		return $grid_field_arr;
	}
	function build_user_invoices_search() {
		$form ['forms'] = array (
				"",
				array (
						'id' => "user_invoice_search" 
				) 
		);
		$form ['Search'] = array (
				array (
						gettext ( 'From Date' ),
						'INPUT',
						array (
								'name' => 'from_date[0]',
								'id' => 'invoice_from_date',
								'size' => '20',
								'class' => "text field" 
						),
						'',
						'tOOL TIP',
						'',
						'from_date[from_date-date]' 
				),
				array (
						gettext ( 'To Date' ),
						'INPUT',
						array (
								'name' => 'to_date[1]',
								'id' => 'invoice_to_date',
								'size' => '20',
								'class' => "text field" 
						),
						'',
						'tOOL TIP',
						'',
						'to_date[to_date-date]' 
				),
				array (
						gettext ( 'Amount' ),
						'INPUT',
						array (
								'name' => 'amount[amount]',
								'value' => '',
								'size' => '20',
								'class' => "text field " 
						),
						'',
						'Tool tips info',
						'1',
						'amount[amount-string]',
						'',
						'',
						'',
						'search_string_type',
						'' 
				),
				array (
						gettext ( 'Generated Date' ),
						'INPUT',
						array (
								'name' => 'invoice_date[0]',
								'',
								'size' => '20',
								'class' => "text field",
								'id' => 'invoice_date' 
						),
						'',
						'tOOL TIP',
						'',
						'invoice_date[invoice_date-date]' 
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
				'id' => "user_invoice_search_btn",
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
	function build_user_charge_history() {
		$account_info = $accountinfo = $this->CI->session->userdata ( 'accountinfo' );
		$currency_id = $account_info ['currency_id'];
		$currency = $this->CI->common->get_field_name ( 'currency', 'currency', $currency_id );
		$grid_field_arr = json_encode ( array (
				array (
						gettext ( "Created Date" ),
						"140",
						"created_date",
						"",
						"",
						"" 
				),
				array (
						gettext ( "Invoice Number" ),
						"120",
						"created_date",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Charge Type" ),
						"100",
						"item_type",
						"",
						"",
						"" 
				),
				array (
						gettext ( "Before Balance" ) . "<br/>($currency)",
						"120",
						"before_balance",
						"before_balance",
						"before_balance",
						"convert_to_currency",
						"",
						"true",
						"right" 
				),
				array (
						gettext ( "Debit" ) . "<br/>($currency)",
						"120",
						"debit",
						"debit",
						"debit",
						"convert_to_currency",
						"",
						"true",
						"right" 
				),
				array (
						gettext ( "Credit" ) . "<br/>($currency)",
						"120",
						"credit",
						"credit",
						"credit",
						"convert_to_currency",
						"",
						"true",
						"right" 
				),
				array (
						gettext ( "After Balance" ) . "<br/>($currency)",
						"120",
						"after_balance",
						"after_balance",
						"after_balance",
						"convert_to_currency",
						"",
						"true",
						"right" 
				),
				array (
						gettext ( "Description" ),
						"180",
						"description",
						"",
						"",
						"" 
				) 
		) );
		return $grid_field_arr;
	}
	function build_user_charge_history_search() {
		$form ['forms'] = array (
				"",
				array (
						'id' => "user_charge_history_search" 
				) 
		);
		$form ['Search'] = array (
				array (
						gettext ( 'From Date' ),
						'INPUT',
						array (
								'name' => 'created_date[]',
								'id' => 'charge_from_date',
								'size' => '20',
								'class' => "text field " 
						),
						'',
						'tOOL TIP',
						'',
						'start_date[start_date-date]' 
				),
				array (
						gettext ( 'To Date' ),
						'INPUT',
						array (
								'name' => 'created_date[]',
								'id' => 'charge_to_date',
								'size' => '20',
								'class' => "text field " 
						),
						'',
						'tOOL TIP',
						'',
						'end_date[end_date-date]' 
				),
				array (
						gettext ( 'Debit ' ),
						'INPUT',
						array (
								'name' => 'debit[debit]',
								'value' => '',
								'size' => '20',
								'class' => "text field " 
						),
						'',
						'Tool tips info',
						'1',
						'debit[debit-integer]',
						'',
						'',
						'',
						'search_int_type',
						'' 
				),
				array (
						gettext ( 'Credit ' ),
						'INPUT',
						array (
								'name' => 'credit[credit]',
								'value' => '',
								'size' => '20',
								'class' => "text field " 
						),
						'',
						'Tool tips info',
						'1',
						'credit[credit-integer]',
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
	function build_user_subscription() {
		$account_info = $accountinfo = $this->CI->session->userdata ( 'accountinfo' );
		$currency_id = $account_info ['currency_id'];
		$currency = $this->CI->common->get_field_name ( 'currency', 'currency', $currency_id );
		$grid_field_arr = json_encode ( array (
				array (
						gettext ( "Name" ),
						"335",
						"description",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Amount" ) . "($currency)",
						"335",
						"charge",
						"charge",
						"charge",
						"convert_to_currency",
						"",
						"true",
						"right" 
				),
				array (
						gettext ( "Billing Cycle" ),
						"335",
						"sweep_id",
						"sweep",
						"sweeplist",
						"get_field_name",
						"",
						"true",
						"center" 
				) 
		) );
		return $grid_field_arr;
	}
	function build_user_subscription_search() {
		$accountinfo = $this->CI->session->userdata ( "accountinfo" );
		$form ['forms'] = array (
				"",
				array (
						'id' => "user_subscription_search" 
				) 
		);
		$form ['Search'] = array (
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
						gettext ( 'Bill Cycle' ),
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
				'id' => "user_subscriptions_button",
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
	function build_user_didlist() {
		$account_info = $accountinfo = $this->CI->session->userdata ( 'accountinfo' );
		$currency_id = $account_info ['currency_id'];
		$currency = $this->CI->common->get_field_name ( 'currency', 'currency', $currency_id );
		$grid_field_arr = json_encode ( array (
				array (
						gettext ( "DID" ),
						"105",
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
						"90",
						"country_id",
						"country",
						"countrycode",
						"get_field_name",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Per Minute<br/>Cost" ) . "($currency)",
						"90",
						"cost",
						"cost",
						"cost",
						"convert_to_currency",
						"",
						"true",
						"right" 
				),
				array (
						gettext ( "Initial<br/>Increment" ),
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
						"100",
						"inc",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Setup<br/>Fee" ) . "($currency)",
						"100",
						"setup",
						"setup",
						"setup",
						"convert_to_currency",
						"",
						"true",
						"right" 
				),
				array (
						gettext ( "Monthly<br/>Fee" ) . "($currency)",
						"100",
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
						"105",
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
						"153",
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
						"100",
						"last_modified_date",
						"last_modified_date",
						"last_modified_date",
						"convert_GMT_to",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Action" ),
						"80",
						"",
						"",
						"",
						array (
								"EDIT" => array (
										"url" => "/user/user_did_edit/",
										"mode" => "popup" 
								),
								"DELETE" => array (
										"url" => "/user/user_dids_action/delete/",
										"mode" => "single" 
								) 
						) 
				) 
		) );
		return $grid_field_arr;
	}
	function build_user_didlist_search() {
		$form ['forms'] = array (
				"",
				array (
						'id' => "user_did_search" 
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
				'id' => "user_did_search_btn",
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
	function build_user_ipmap() {
		$grid_field_arr = json_encode ( array (
				array (
						gettext ( "Name" ),
						"240",
						"name",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "IP" ),
						"240",
						"ip",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Prefix" ),
						"220",
						"prefix",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Created Date" ),
						"174",
						"created_date",
						"created_date",
						"created_date",
						"convert_GMT_to",
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
										"url" => "user/user_ipmap_action/delete/",
										"mode" => "single" 
								) 
						) 
				) 
		) );
		return $grid_field_arr;
	}
	function build_user_ipmap_search() {
		$form ['forms'] = array (
				"",
				array (
						'id' => "user_ipmap_search" 
				) 
		);
		$form ['Search'] = array (
				array (
						gettext ( 'Name' ),
						'INPUT',
						array (
								'name' => 'name[name]',
								'',
								'size' => '20',
								'class' => "text field" 
						),
						'',
						'tOOL TIP',
						'1',
						'name[name-string]',
						'',
						'',
						'',
						'search_string_type',
						'' 
				),
				array (
						gettext ( 'IP' ),
						'INPUT',
						array (
								'name' => 'ip[ip]',
								'',
								'size' => '20',
								'class' => "text field" 
						),
						'',
						'tOOL TIP',
						'1',
						'ip[ip-string]',
						'',
						'',
						'',
						'search_string_type',
						'' 
				),
				array (
						gettext ( 'Prefix' ),
						'INPUT',
						array (
								'name' => 'prefix[prefix]',
								'',
								'size' => '20',
								'class' => "text field" 
						),
						'',
						'tOOL TIP',
						'1',
						'prefix[prefix-string]',
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
				'id' => "user_ipmap_search_btn",
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
				'class' => "btn btn-line-sky pull-right margin-x-10" 
		);
		return $form;
	}
	function build_user_sipdevices() {
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
						gettext ( "User Name" ),
						"105",
						"username",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Password" ),
						"105",
						"password",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Caller Name" ),
						"110",
						"effective_caller_id_name",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Caller Number" ),
						"110",
						"effective_caller_id_number",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Status" ),
						"125",
						"status",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Created Date" ),
						"110",
						"creation_date",
						"creation_date",
						"creation_date",
						"convert_GMT_to",
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
						gettext ( "Voicemail" ),
						"90",
						"voicemail_enabled",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Action" ),
						"110",
						"",
						"",
						"",
						array (
								"EDIT" => array (
										"url" => "/accounts/fssipdevices_action/edit/",
										"mode" => "single",
										"layout" => "medium" 
								),
								"DELETE" => array (
										"url" => "/accounts/fssipdevices_action/delete/",
										"mode" => "single" 
								) 
						) 
				) 
		) );
		return $grid_field_arr;
	}
	function build_user_sipdevices_search() {
		$form ['forms'] = array (
				"",
				array (
						'id' => "user_sipdevices_search" 
				) 
		);
		$form ['Search'] = array (
				array (
						gettext ( 'Username' ),
						'INPUT',
						array (
								'name' => 'username[username]',
								'',
								'size' => '20',
								'class' => "text field" 
						),
						'',
						'tOOL TIP',
						'1',
						'username[username-string]',
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
				'id' => "user_sipdevices_search_btn",
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
	function build_user_sipdevices_form($id = '') {
		$val = $id > 0 ? 'sip_devices.username.' . $id : 'sip_devices.username';
		$uname_user = $this->CI->common->find_uniq_rendno ( '10', '', '' );
		$password = $this->CI->common->generate_password ();
		$form ['forms'] = array (
				base_url () . 'user/user_sipdevices_save/',
				array (
						"id" => "user_sipdevices_form",
						"name" => "user_sipdevices_form" 
				) 
		);
		$form [gettext ( 'Device Information' )] = array (
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
						gettext ( 'Username' ),
						'INPUT',
						array (
								'name' => 'fs_username',
								'size' => '20',
								'value' => $uname_user,
								'id' => 'username',
								'class' => "text field medium" 
						),
						'trim|required|is_unique[' . $val . ']|xss_clean',
						'tOOL TIP',
						'Please Enter account number',
						'<i style="color: #1BCB61;font-size: 14px;padding-left: 5px;padding-top: 8px;float: left;" title="Reset Password" class="change_number  fa fa-refresh"></i>' 
				),
				array (
						gettext ( 'Password' ),
						'INPUT',
						array (
								'name' => 'fs_password',
								'size' => '20',
								'value' => $password,
								'id' => 'password',
								'class' => "text field medium" 
						),
						'trim|required|xss_clean',
						'tOOL TIP',
						'Please Enter Password',
						'<i style="color: #1BCB61;font-size: 14px;padding-left: 5px;padding-top: 8px;float: left;" title="Reset Password" class="change_pass fa fa-refresh"></i>' 
				),
				array (
						gettext ( 'Caller Name' ),
						'INPUT',
						array (
								'name' => 'effective_caller_id_name',
								'size' => '20',
								'class' => "text field medium" 
						),
						'',
						'tOOL TIP',
						'Please Enter account number' 
				),
				array (
						gettext ( 'Caller Number' ),
						'INPUT',
						array (
								'name' => 'effective_caller_id_number',
								'size' => '20',
								'class' => "text field medium" 
						),
						'',
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
		);
		
		$form [gettext ( 'Voicemail Options' )] = array (
				array (
						gettext ( 'Enable' ),
						'voicemail_enabled',
						'SELECT',
						'',
						'',
						'tOOL TIP',
						'Please Select Status',
						'',
						'',
						'',
						'set_sip_config_option' 
				),
				array (
						gettext ( 'Password' ),
						'INPUT',
						array (
								'name' => 'voicemail_password',
								'size' => '20',
								'class' => "text field medium" 
						),
						'',
						'tOOL TIP',
						'Please Enter account number' 
				),
				array (
						gettext ( 'Mail To' ),
						'INPUT',
						array (
								'name' => 'voicemail_mail_to',
								'size' => '20',
								'class' => "text field medium" 
						),
						'',
						'tOOL TIP',
						'Please Enter account number' 
				),
				array (
						gettext ( 'Attach File' ),
						'voicemail_attach_file',
						'SELECT',
						'',
						'',
						'tOOL TIP',
						'Please Select Status',
						'',
						'',
						'',
						'set_sip_config_option' 
				),
				array (
						gettext ( 'Local After Email' ),
						'vm_keep_local_after_email',
						'SELECT',
						'',
						'',
						'tOOL TIP',
						'Please Select Status',
						'',
						'',
						'',
						'set_sip_config_option' 
				),
				array (
						gettext ( 'Send all Message' ),
						'vm_send_all_message',
						'SELECT',
						'',
						'',
						'tOOL TIP',
						'Please Select Status',
						'',
						'',
						'',
						'set_sip_config_option' 
				) 
		)
		;
		$form ['button_cancel'] = array (
				'name' => 'action',
				'content' => 'Close',
				'value' => 'cancel',
				'type' => 'button',
				'class' => 'btn btn-line-sky margin-x-10',
				'onclick' => 'return redirect_page(\'NULL\')' 
		);
		$form ['button_save'] = array (
				'name' => 'action',
				'content' => 'Save',
				'value' => 'save',
				'id' => 'submit',
				'type' => 'button',
				'class' => 'btn btn-line-parrot' 
		);
		return $form;
	}
	function build_user_animap() {
		$grid_field_arr = json_encode ( array (
				array (
						gettext ( "Caller ID" ),
						"735",
						"number",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Action" ),
						"275",
						"",
						"",
						"",
						array (
								"DELETE" => array (
										"url" => "user/user_animap_action/delete/",
										"mode" => "single" 
								) 
						) 
				) 
		) );
		return $grid_field_arr;
	}
	function user_rates_list_buttons() {
		$buttons_json = json_encode ( array (
				array (
						gettext ( "Export CSV" ),
						"btn btn-xing",
						"fa fa-download fa-lg",
						"button_action",
						"/user/user_rates_list_export/",
						'single' 
				) 
		) );
		return $buttons_json;
	}
	function user_rates_list() {
		$account_info = $accountinfo = $this->CI->session->userdata ( 'accountinfo' );
		$currency_id = $account_info ['currency_id'];
		$currency = $this->CI->common->get_field_name ( 'currency', 'currency', $currency_id );
		$grid_field_arr = json_encode ( array (
				array (
						gettext ( "Code" ),
						"155",
						"pattern",
						"pattern",
						"",
						"get_only_numeric_val",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Destination" ),
						"200",
						"comment",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Connect Cost" ) . "($currency)",
						"200",
						"connectcost",
						"",
						"",
						"",
						"",
						"true",
						"right" 
				),
				array (
						gettext ( "Included Seconds" ),
						"200",
						"includedseconds",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Per Minute Cost" ) . "($currency)",
						"200",
						"cost",
						"",
						"",
						"",
						"",
						"true",
						"right" 
				),
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
				array (
						gettext ( "Increment" ),
						"180",
						"inc",
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
	function user_rates_list_search() {
		$form ['forms'] = array (
				"",
				array (
						'id' => "user_rates_list_search" 
				) 
		);
		$form ['Search'] = array (
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
						'search_int_type',
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
				'id' => "user_rates_list_search_btn",
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
	function user_alert_threshold() {
		$form ['forms'] = array (
				base_url () . 'user/user_alert_threshold/',
				array (
						"id" => "customer_alert_threshold",
						"name" => "customer_alert_threshold" 
				) 
		);
		$form [gettext ( 'Low Balance Alert Email' )] = array (
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
						gettext ( 'Enable Email Alerts ?' ),
						'notify_flag',
						'SELECT',
						'',
						'',
						'tOOL TIP',
						'',
						'',
						'',
						'',
						'custom_status' 
				),
				array (
						gettext ( 'Low Balance Alert Level' ),
						'INPUT',
						array (
								'name' => 'notify_credit_limit',
								'size' => '20',
								'class' => "text field medium" 
						),
						'currency_decimal',
						'tOOL TIP',
						'' 
				),
				array (
						gettext ( 'Email Address' ),
						'INPUT',
						array (
								'name' => 'notify_email',
								'size' => '50',
								'class' => "text field medium" 
						),
						'valid_email',
						'tOOL TIP',
						'' 
				) 
		);
		$form ['button_save'] = array (
				'name' => 'action',
				'content' => gettext ( 'Save' ),
				'value' => 'save',
				'type' => 'submit',
				'class' => 'btn btn-line-parrot' 
		);
		return $form;
	}
	function build_cdrs_report($type) {
		$account_info = $accountinfo = $this->CI->session->userdata ( 'accountinfo' );
		$currency_id = $account_info ['currency_id'];
		$currency = $this->CI->common->get_field_name ( 'currency', 'currency', $currency_id );
		
		if ($type == '0' || $type == '1') {
			$cost_array = array (
					gettext ( "Debit" ) . "($currency)",
					"140",
					"debit",
					"debit",
					"debit",
					"convert_to_currency",
					"",
					"true",
					"right" 
			);
		}
		if ($type == '3') {
			$cost_array = array (
					gettext ( "Debit" ) . "($currency)",
					"140",
					"cost",
					"cost",
					"cost",
					"convert_to_currency",
					"",
					"true",
					"right" 
			);
		}
		$grid_field_arr = json_encode ( array (
				array (
						gettext ( "Date" ),
						"170",
						"callstart",
						"callstart",
						"callstart",
						"convert_GMT_to",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Caller ID" ),
						"110",
						"callerid",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Called Number" ),
						"160",
						"callednum",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Destination" ),
						"160",
						"notes",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Duration" ),
						"140",
						"billseconds",
						"user_cdrs_report_search",
						"billseconds",
						"convert_to_show_in",
						"",
						"true",
						"center" 
				),
				$cost_array,
				array (
						gettext ( "Disposition" ),
						"160",
						"disposition",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Call Type" ),
						"233",
						"calltype",
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
	function build_cdrs_report_search($type) {
		if ($type == '0' || $type == '1') {
			$cost_array = array (
					gettext ( 'Debit' ),
					'INPUT',
					array (
							'name' => 'debit[debit]',
							'value' => '',
							'size' => '20',
							'class' => "text field " 
					),
					'',
					'Tool tips info',
					'1',
					'debit[debit-integer]',
					'',
					'',
					'',
					'search_int_type',
					'' 
			);
		}
		if ($type == '3') {
			$cost_array = array (
					gettext ( 'Debit' ),
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
			);
		}
		$form ['forms'] = array (
				"",
				array (
						'id' => "user_cdrs_report_search" 
				) 
		);
		$form ['Search'] = array (
				array (
						gettext ( 'From Date' ),
						'INPUT',
						array (
								'name' => 'callstart[]',
								'id' => 'customer_cdr_from_date',
								'size' => '20',
								'class' => "text field " 
						),
						'',
						'tOOL TIP',
						'',
						'start_date[start_date-date]' 
				),
				array (
						gettext ( 'To Date' ),
						'INPUT',
						array (
								'name' => 'callstart[]',
								'id' => 'customer_cdr_to_date',
								'size' => '20',
								'class' => "text field " 
						),
						'',
						'tOOL TIP',
						'',
						'end_date[end_date-date]' 
				),
				array (
						gettext ( 'Caller ID' ),
						'INPUT',
						array (
								'name' => 'callerid[callerid]',
								'',
								'id' => 'first_name',
								'size' => '15',
								'class' => "text field " 
						),
						'',
						'tOOL TIP',
						'1',
						'callerid[callerid-string]',
						'',
						'',
						'',
						'search_string_type',
						'' 
				),
				array (
						gettext ( 'Called Number' ),
						'INPUT',
						array (
								'name' => 'callednum[callednum]',
								'value' => '',
								'size' => '20',
								'class' => "text field " 
						),
						'',
						'Tool tips info',
						'1',
						'callednum[callednum-string]',
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
								'name' => 'notes[notes]',
								'value' => '',
								'size' => '20',
								'class' => "text field " 
						),
						'',
						'Tool tips info',
						'1',
						'notes[notes-integer]',
						'',
						'',
						'',
						'search_int_type',
						'' 
				),
				array (
						gettext ( 'Duration' ),
						'INPUT',
						array (
								'name' => 'billseconds[billseconds]',
								'value' => '',
								'size' => '20',
								'class' => "text field " 
						),
						'',
						'Tool tips info',
						'1',
						'billseconds[billseconds-integer]',
						'',
						'',
						'',
						'search_int_type',
						'' 
				),
				$cost_array,
				array (
						gettext ( 'Disposition' ),
						'disposition',
						'SELECT',
						'',
						'',
						'tOOL TIP',
						'Please Enter account number',
						'',
						'',
						'',
						'set_despostion' 
				),
				array (
						gettext ( 'Call Type' ),
						'calltype',
						'SELECT',
						'',
						'',
						'tOOL TIP',
						'Please Enter account number',
						'',
						'',
						'',
						'set_calltype' 
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
		$form ['display_in'] = array (
				'name' => 'search_in',
				"id" => "search_in",
				"function" => "search_report_in",
				"content" => gettext ( "Display records in" ),
				'label_class' => "search_label col-md-3 no-padding",
				"dropdown_class" => "form-control",
				"label_style" => "font-size:13px;",
				"dropdown_style" => "background: #ddd; width: 21% !important;" 
		);
		$form ['button_search'] = array (
				'name' => 'action',
				'id' => "user_cdr_search_btn",
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
	function build_cdrs_report_buttons() {
		$buttons_json = json_encode ( array (
				array (
						gettext ( "Export" ),
						"btn btn-xing",
						" fa fa-download fa-lg",
						"button_action",
						"/user/user_report_export/",
						'single' 
				) 
		) );
		return $buttons_json;
	}
	function build_user_refill_report() {
		$account_info = $accountinfo = $this->CI->session->userdata ( 'accountinfo' );
		$currency_id = $account_info ['currency_id'];
		$currency = $this->CI->common->get_field_name ( 'currency', 'currency', $currency_id );
		$grid_field_arr = json_encode ( array (
				array (
						gettext ( "Date" ),
						"220",
						"payment_date",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Amount" ) . "($currency)",
						"220",
						"credit",
						"credit",
						"credit",
						"convert_to_currency",
						"",
						"true",
						"right" 
				),
				array (
						gettext ( "Refill By" ),
						"270",
						"payment_by",
						"payment_by",
						"payment_by",
						"get_refill_by",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Note" ),
						"300",
						"notes",
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
	function build_user_refill_report_search() {
		$form ['forms'] = array (
				"",
				array (
						'id' => "user_refill_report_search" 
				) 
		);
		$form ['Search'] = array (
				array (
						gettext ( 'From Date' ),
						'INPUT',
						array (
								'name' => 'payment_date[]',
								'id' => 'customer_cdr_from_date',
								'size' => '20',
								'class' => "text field " 
						),
						'',
						'tOOL TIP',
						'',
						'payment_date[payment_date-date]' 
				),
				array (
						gettext ( 'To Date' ),
						'INPUT',
						array (
								'name' => 'payment_date[]',
								'id' => 'customer_cdr_to_date',
								'size' => '20',
								'class' => "text field " 
						),
						'',
						'tOOL TIP',
						'',
						'payment_date[payment_date-date]' 
				),
				array (
						gettext ( 'Amount' ),
						'INPUT',
						array (
								'name' => 'credit[credit]',
								'value' => '',
								'size' => '20',
								'class' => "text field" 
						),
						'',
						'Tool tips info',
						'1',
						'credit[credit-integer]',
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
				'id' => "user_refill_report_search_btn",
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
	function build_user_fund_transfer_form($number, $currency_id, $id) {
		$form ['forms'] = array (
				base_url () . 'user/user_fund_transfer_save/',
				array (
						'id' => 'user_fund_transfer_form',
						'method' => 'POST',
						'class' => 'build_user_fund_transfer_frm',
						'name' => 'user_fund_transfer_form' 
				) 
		);
		$form [gettext ( 'Fund Transfer' )] = array (
				array (
						'',
						'HIDDEN',
						array (
								'name' => 'id',
								'value' => $id 
						),
						'',
						'',
						'',
						'' 
				),
				array (
						'',
						'HIDDEN',
						array (
								'name' => 'account_currency',
								'value' => $currency_id 
						),
						'',
						'',
						'' 
				),
				array (
						gettext ( 'From Account' ),
						'INPUT',
						array (
								'name' => 'fromaccountid',
								'size' => '20',
								'value' => $number,
								'readonly' => true,
								'class' => "text field medium" 
						),
						'required',
						'tOOL TIP',
						'Please Enter account number' 
				),
				array (
						gettext ( 'To Account' ),
						'INPUT',
						array (
								'name' => 'toaccountid',
								'size' => '20',
								'class' => "text field medium" 
						),
						'trim|required|numeric',
						'tOOL TIP',
						'Please Enter to account number' 
				),
				array (
						gettext ( 'Amount' ),
						'INPUT',
						array (
								'name' => 'credit',
								'size' => '20',
								'class' => "text field medium" 
						),
						'trim|required|numeric',
						'tOOL TIP',
						'' 
				),
				array (
						gettext ( 'Note' ),
						'TEXTAREA',
						array (
								'name' => 'notes',
								'size' => '20',
								'cols' => '63',
								'rows' => '5',
								'class' => "form-control col-md-5  text field medium",
								'style' => 'height: 80px;' 
						),
						'',
						'tOOL TIP',
						'' 
				) 
		);
		$form ['button_save'] = array (
				'name' => 'action',
				'content' => 'Transfer',
				'value' => gettext ( 'save' ),
				'id' => "submit",
				'type' => 'submit',
				'class' => 'btn btn-line-parrot' 
		);
		return $form;
	}
	function build_user_opensips_buttons() {
		$buttons_json = json_encode ( array (
				array (
						"Create",
						"btn btn-line-warning btn",
						"fa fa-plus-circle fa-lg",
						"button_action",
						"/user/user_opensips_add/",
						"popup" 
				),
				array (
						"Delete",
						"btn btn-line-danger",
						"fa fa-times-circle fa-lg",
						"button_action",
						"/user/user_opensips_delete_multiple/" 
				) 
		) );
		return $buttons_json;
	}
	function build_user_opensips() {
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
						"Username",
						"240",
						"username",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				array (
						"Password",
						"240",
						"password",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				array (
						"Domain",
						"240",
						"domain",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				array (
						"Action",
						"200",
						"",
						"",
						"",
						array (
								"EDIT" => array (
										"url" => 'user/user_opensips_edit/',
										"mode" => "popup" 
								),
								"DELETE" => array (
										"url" => 'user/user_opensips_delete/',
										"mode" => "popup" 
								) 
						) 
				) 
		) );
		return $grid_field_arr;
	}
	function build_user_opensips_form($id = false) {
		$val = $id > 0 ? 'subscriber.username.' . $id : 'subscriber.username';
		$uname_user = $this->CI->common->find_uniq_rendno ( '10', '', '' );
		$password = $this->CI->common->generate_password ();
		$accountinfo = $this->CI->session->userdata ( 'accountinfo' );
		$form ['forms'] = array (
				base_url () . 'user/user_opensips_save/',
				array (
						"id" => "opensips_form",
						"name" => "opensips_form" 
				) 
		);
		$form ['Opensips Device'] = array (
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
						'',
						'HIDDEN',
						array (
								'name' => 'accountcode',
								'value' => $accountinfo ['number'] 
						),
						'',
						'',
						'',
						'' 
				),
				array (
						'Username',
						'INPUT',
						array (
								'name' => 'username',
								'size' => '20',
								'id' => 'username',
								'value' => $uname_user,
								'class' => "text field medium" 
						),
						'trim|required|xss_clean',
						'tOOL TIP',
						'Please Enter account number',
						'<i style="cursor:pointer; font-size: 17px; padding-left:10px; padding-top:6px;" title="Reset Password" class="change_number fa fa-refresh"></i>' 
				),
				array (
						'Password',
						'PASSWORD',
						array (
								'name' => 'password',
								'size' => '20',
								'id' => 'password1',
								'value' => $password,
								'class' => "text field medium" 
						),
						'trim|required|xss_clean',
						'tOOL TIP',
						'Please Enter Password',
						'<i style="cursor:pointer; font-size: 17px; padding-left:10px; padding-top:6px;" title="Reset Password" class="change_pass fa fa-refresh"></i>' 
				),
				array (
						'Domain',
						'INPUT',
						array (
								'name' => 'domain',
								'size' => '20',
								'class' => "text field medium" 
						),
						'',
						'tOOL TIP',
						'Please Enter account number' 
				),
				array (
						'Status',
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
		$form ['button_save'] = array (
				'name' => 'action',
				'content' => 'Save',
				'value' => 'save',
				'type' => 'button',
				'id' => 'submit',
				'class' => 'btn btn-line-parrot' 
		);
		$form ['button_cancel'] = array (
				'name' => 'action',
				'content' => 'Close',
				'value' => 'cancel',
				'type' => 'button',
				'class' => 'btn btn-line-sky margin-x-10',
				'onclick' => 'return redirect_page(\'NULL\')' 
		);
		return $form;
	}
	function build_user_opensips_search() {
		$form ['forms'] = array (
				"",
				array (
						'id' => "opensips_list_search" 
				) 
		);
		$form ['Search'] = array (
				array (
						'Username',
						'INPUT',
						array (
								'name' => 'username[username]',
								'',
								'size' => '20',
								'class' => "text field " 
						),
						'',
						'tOOL TIP',
						'1',
						'username[username-string]',
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
				'id' => "opensipsdevice_search_btn",
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
	function build_user_did_form() {
		$form ['forms'] = array (
				base_url () . 'user/user_dids_action/edit/',
				array (
						"id" => "user_did_form",
						"name" => "user_did_form" 
				) 
		);
		$form ['Edit'] = array (
				array (
						'',
						'HIDDEN',
						array (
								'name' => 'free_didlist' 
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
								'class' => "text field medium",
								"readonly" => "true" 
						),
						'trim|required|is_numeric|xss_clean|integer',
						'tOOL TIP',
						'Please Enter account number' 
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
						'trim|required|xss_clean',
						'tOOL TIP',
						'Please Enter Password' 
				) 
		);
		$form ['button_save'] = array (
				'name' => 'action',
				'content' => 'Save',
				'value' => 'save',
				'type' => 'button',
				'id' => 'submit',
				'class' => 'btn btn-line-parrot' 
		);
		return $form;
	}
	function build_provider_report_buttons() {
		$buttons_json = json_encode ( array (
				array (
						"Export",
						"btn btn-xing",
						" fa fa-download fa-lg",
						"button_action",
						"/user/user_provider_cdrreport_export/",
						'single' 
				) 
		) );
		return $buttons_json;
	}
	function build_provider_report($type) {
		$account_info = $accountinfo = $this->CI->session->userdata ( 'accountinfo' );
		$currency_id = $account_info ['currency_id'];
		$currency = $this->CI->common->get_field_name ( 'currency', 'currency', $currency_id );
		
		if ($type == '0' || $type == '1') {
			$cost_array = array (
					"Debit($currency)",
					"140",
					"debit",
					"debit",
					"debit",
					"convert_to_currency",
					"",
					"true",
					"right" 
			);
		}
		if ($type == '3') {
			$cost_array = array (
					"Cost($currency)",
					"140",
					"cost",
					"cost",
					"cost",
					"convert_to_currency",
					"",
					"true",
					"right" 
			);
		}
		$grid_field_arr = json_encode ( array (
				array (
						"Date",
						"170",
						"callstart",
						"callstart",
						"callstart",
						"convert_GMT_to",
						"",
						"true",
						"center" 
				),
				array (
						"Caller ID",
						"110",
						"callerid",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				array (
						"Called Number",
						"160",
						"callednum",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				array (
						"Destination",
						"160",
						"notes",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				array (
						"Duration",
						"140",
						"billseconds",
						"user_provider_cdrs_report_search",
						"billseconds",
						"convert_to_show_in",
						"",
						"true",
						"center" 
				),
				$cost_array,
				array (
						"Disposition",
						"160",
						"disposition",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				array (
						"Call Type",
						"233",
						"calltype",
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
	function build_provider_report_search($type) {
		$cost_array = array (
				'Cost ',
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
		);
		$form ['forms'] = array (
				"",
				array (
						'id' => "user_provider_cdrs_report_search" 
				) 
		);
		$form ['Search'] = array (
				array (
						'From Date',
						'INPUT',
						array (
								'name' => 'callstart[]',
								'id' => 'customer_cdr_from_date',
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
								'id' => 'customer_cdr_to_date',
								'size' => '20',
								'class' => "text field " 
						),
						'',
						'tOOL TIP',
						'',
						'end_date[end_date-date]' 
				),
				array (
						'Caller ID',
						'INPUT',
						array (
								'name' => 'callerid[callerid]',
								'',
								'id' => 'first_name',
								'size' => '15',
								'class' => "text field " 
						),
						'',
						'tOOL TIP',
						'1',
						'callerid[callerid-string]',
						'',
						'',
						'',
						'search_string_type',
						'' 
				),
				array (
						'Called Number',
						'INPUT',
						array (
								'name' => 'callednum[callednum]',
								'value' => '',
								'size' => '20',
								'class' => "text field " 
						),
						'',
						'Tool tips info',
						'1',
						'callednum[callednum-string]',
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
						'notes[notes-integer]',
						'',
						'',
						'',
						'search_int_type',
						'' 
				),
				array (
						'Duration',
						'INPUT',
						array (
								'name' => 'billseconds[billseconds]',
								'value' => '',
								'size' => '20',
								'class' => "text field " 
						),
						'',
						'Tool tips info',
						'1',
						'billseconds[billseconds-integer]',
						'',
						'',
						'',
						'search_int_type',
						'' 
				),
				$cost_array,
				array (
						'Disposition',
						'disposition',
						'SELECT',
						'',
						'',
						'tOOL TIP',
						'Please Enter account number',
						'',
						'',
						'',
						'set_despostion' 
				),
				array (
						'Call Type',
						'calltype',
						'SELECT',
						'',
						'',
						'tOOL TIP',
						'Please Enter account number',
						'',
						'',
						'',
						'set_calltype' 
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
		$form ['display_in'] = array (
				'name' => 'search_in',
				"id" => "search_in",
				"function" => "search_report_in",
				"content" => "Display records in",
				'label_class' => "search_label col-md-3 no-padding",
				"dropdown_class" => "form-control",
				"label_style" => "font-size:13px;",
				"dropdown_style" => "background: #ddd; width: 21% !important;" 
		);
		$form ['button_search'] = array (
				'name' => 'action',
				'id' => "user_provider_cdr_search_btn",
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
}
?>
