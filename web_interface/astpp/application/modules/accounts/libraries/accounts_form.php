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
class Accounts_form {
	function __construct($library_name = '') {
		$this->CI = & get_instance ();
	}
	function get_customer_form_fields($entity_type = false, $id = false) {
		$expiry_date = gmdate ( 'Y-m-d H:i:s', strtotime ( '+10 years' ) );
		$readable = FALSE;
		$type = $entity_type == 'customer' ? 0 : 3;
		$uname = $this->CI->common->find_uniq_rendno_customer ( common_model::$global_config ['system_config'] ['cardlength'], 'number', 'accounts' );
		$pin = Common_model::$global_config ['system_config'] ['pinlength'];
		$pin_number = $this->CI->common->find_uniq_rendno ( $pin, 'pin', 'accounts' );
		$uname_user = $this->CI->common->find_uniq_rendno ( '10', 'number', 'accounts' );
		$password = $this->CI->common->generate_password ();
		$logintype = $this->CI->session->userdata ( 'logintype' );
		if ($logintype == 1 || $logintype == 5) {
			$account_data = $this->CI->session->userdata ( "accountinfo" );
			$loginid = $account_data ['id'];
		} else {
			$loginid = "0";
		}
		$sip_device = null;
		$opensips_device = null;
		if (! $entity_type) {
			$entity_type = 'customer';
		}
		$params = array (
				'name' => 'number',
				'value' => $uname,
				'size' => '20',
				'class' => "text field medium",
				'id' => 'number',
				'readonly' => true 
		);
		
		if ($id > 0) {
			$readable = 'disabled';
			$val = 'accounts.email.' . $id;
			$account_val = 'accounts.number.' . $id;
			$password = array (
					'Password',
					'PASSWORD',
					array (
							'name' => 'password',
							'id' => 'password_show',
							'onmouseover' => 'seetext(password_show)',
							'onmouseout' => 'hidepassword(password_show)',
							'size' => '20',
							'class' => "text field medium" 
					),
					'required|',
					'tOOL TIP',
					'' 
			);
			$balance = array (
					'Balance',
					'INPUT',
					array (
							'name' => 'balance',
							'size' => '20',
							'readonly' => true,
							'class' => "text field medium" 
					),
					'',
					'tOOL TIP',
					'' 
			);
			$account = array (
					'Account',
					'INPUT',
					$params,
					'required|integer|greater_than[0]|is_unique[' . $account_val . ']',
					'tOOL TIP',
					'' 
			);
			/* * ******************************** */
		} else {
			$val = 'accounts.email';
			$account_val = 'accounts.number';
			if (common_model::$global_config ['system_config'] ['opensips'] == 0) {
				$sip_device = array (
						'Create Opensips Device',
						'opensips_device_flag',
						'SELECT',
						'',
						'',
						'tOOL TIP',
						'Please Enter account number',
						'',
						'',
						'',
						'set_prorate' 
				);
			} else {
				$sip_device = array (
						'Create SIP Device',
						'sip_device_flag',
						'SELECT',
						'',
						'',
						'tOOL TIP',
						'Please Enter account number',
						'',
						'',
						'',
						'set_prorate' 
				);
			}
			$account = array (
					'Account',
					'INPUT',
					$params,
					'required|integer|greater_than[0]|is_unique[' . $account_val . ']',
					'tOOL TIP',
					'',
					'<i style="cursor:pointer; font-size: 17px; padding-left:10px; padding-top:6px;color: #1bcb61;" title="Generate Account" class="change_number fa fa-refresh" ></i>' 
			);
			$password = array (
					'Password',
					'INPUT',
					array (
							'name' => 'password',
							'value' => $password,
							'size' => '20',
							'class' => "text field medium",
							'id' => 'password' 
					),
					'required|',
					'tOOL TIP',
					'',
					'<i style="cursor:pointer; font-size: 17px; padding-left:10px; padding-top:6px;color: #1bcb61;" title="Reset Password" class="change_pass fa fa-refresh" ></i>' 
			);
			$balance = array (
					'Balance',
					'INPUT',
					array (
							'name' => 'balance',
							'size' => '20',
							'class' => "text field medium" 
					),
					'',
					'tOOL TIP',
					'' 
			);
		}
		$pin = array (
				'Pin',
				'INPUT',
				array (
						'name' => 'pin',
						'value' => $pin_number,
						'size' => '20',
						'class' => "text field medium",
						'id' => 'change_pin' 
				),
				'is_numeric',
				'tOOL TIP',
				'',
				'<i style="cursor:pointer; font-size: 17px; padding-left:10px; padding-top:6px;color: #1bcb61;" title="Generate Pin" class="change_pin fa fa-refresh" ></i>' 
		);
		$form ['forms'] = array (
				base_url () . 'accounts/' . $entity_type . '_save/' . $id . "/",
				array (
						"id" => "customer_form",
						"name" => "customer_form" 
				) 
		);
		$form [gettext ( 'Account Profile' )] = array (
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
								'value' => $type 
						),
						'',
						'',
						'' 
				),
				$account,
				$password,
				$pin,
				array (
						gettext ( 'First Name' ),
						'INPUT',
						array (
								'name' => 'first_name',
								'id' => 'first_name',
								'size' => '15',
								'class' => "text field medium" 
						),
						'required',
						'tOOL TIP',
						'' 
				),
				array (
						gettext ( 'Last Name' ),
						'INPUT',
						array (
								'name' => 'last_name',
								'size' => '15',
								'class' => "text field medium" 
						),
						'trim|xss_clean',
						'tOOL TIP',
						'' 
				),
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
						gettext ( 'Phone' ),
						'INPUT',
						array (
								'name' => 'telephone_1',
								'size' => '15',
								'class' => "text field medium" 
						),
						'phn_number',
						'tOOL TIP',
						'' 
				),
				array (
						gettext ( 'Mobile' ),
						'INPUT',
						array (
								'name' => 'telephone_2',
								'size' => '15',
								'class' => "text field medium" 
						),
						'phn_number',
						'tOOL TIP',
						'' 
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
						'' 
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
						'' 
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
						'' 
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
						'' 
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
						'' 
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
						'' 
				),
				array (
						gettext ( 'Country' ),
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
				),
				array (
						gettext ( 'Timezone' ),
						array (
								'name' => 'timezone_id',
								'class' => 'timezone_id' 
						),
						'SELECT',
						'',
						array (
								"name" => "timezone_id",
								"rules" => "required" 
						),
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
						gettext ( 'Currency' ),
						array (
								'name' => 'currency_id',
								'class' => 'currency_id' 
						),
						'SELECT',
						'',
						array (
								"name" => "currency_id",
								"rules" => "required" 
						),
						'tOOL TIP',
						'Please Enter account number',
						'id',
						'currencyname,currency',
						'currency',
						'build_concat_dropdown',
						'',
						array () 
				) 
		);
		
		$form [gettext ( 'Account Settings' )] = array (
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
				),
				array (
						gettext ( 'Allow Recording' ),
						'is_recording',
						'SELECT',
						'',
						'',
						'tOOL TIP',
						'Please Enter account number',
						'',
						'',
						'',
						'custom_status_recording' 
				),
				array (
						gettext ( 'Allow IP Management' ),
						'allow_ip_management',
						'SELECT',
						'',
						'',
						'tOOL TIP',
						'Please Enter account number',
						'',
						'',
						'',
						'custom_status' 
				),
				$sip_device,
				array (
						gettext ( 'Number Translation' ),
						'INPUT',
						array (
								'name' => 'dialed_modify',
								'size' => '20',
								'class' => "text field medium" 
						),
						'',
						'tOOL TIP',
						'' 
				),
				
				// Added caller id translation code.
				array (
						gettext ( 'OUT Callerid Translation' ),
						'INPUT',
						array (
								'name' => 'std_cid_translation',
								'size' => '20',
								'class' => "text field medium" 
						),
						'',
						'tOOL TIP',
						'' 
				),
				array (
						gettext ( 'IN Callerid Translation' ),
						'INPUT',
						array (
								'name' => 'did_cid_translation',
								'size' => '20',
								'class' => "text field medium" 
						),
						'',
						'tOOL TIP',
						'' 
				),
				
				array (
						gettext ( 'Concurrent Calls' ),
						'INPUT',
						array (
								'name' => 'maxchannels',
								'size' => '20',
								'class' => "text field medium" 
						),
						'numeric',
						'tOOL TIP',
						'' 
				),
				array (
						gettext ( 'CPS' ),
						'INPUT',
						array (
								'name' => 'cps',
								'size' => '20',
								'class' => "text field medium" 
						),
						'numeric',
						'tOOL TIP',
						'' 
				),
				array (
						gettext ( 'First Used' ),
						'INPUT',
						array (
								'name' => 'first_used',
								'size' => '20',
								'readonly' => true,
								'class' => "text field medium",
								'value' => '0000-00-00 00:00:00' 
						),
						'',
						'tOOL TIP',
						'' 
				),
				array (
						gettext ( 'Account Valid Days' ),
						'INPUT',
						array (
								'name' => 'validfordays',
								'size' => '20',
								'class' => "text field medium" 
						),
						'trim|numeric|xss_clean',
						'tOOL TIP',
						'' 
				),
				array (
						gettext ( 'Expiry Date' ),
						'INPUT',
						array (
								'name' => 'expiry',
								'size' => '20',
								'class' => "text field medium",
								'value' => $expiry_date,
								'id' => 'expiry' 
						),
						'',
						'tOOL TIP',
						'' 
				) 
		);
		
		$form [gettext ( 'Billing Settings' )] = array (
				array (
						gettext ( 'Rate Group' ),
						'pricelist_id',
						'SELECT',
						'',
						array (
								"name" => "pricelist_id",
								"rules" => "dropdown" 
						),
						'tOOL TIP',
						'Please Enter account number',
						'id',
						'name',
						'pricelists',
						'build_dropdown',
						'where_arr',
						array (
								"status" => "0",
								"reseller_id" => $loginid 
						) 
				),
				array (
						gettext ( 'Account Type' ),
						array (
								'name' => 'posttoexternal',
								'disabled' => $readable,
								'class' => 'posttoexternal',
								'id' => 'posttoexternal' 
						),
						'SELECT',
						'',
						'',
						'tOOL TIP',
						'Please Enter account number',
						'',
						'',
						'',
						'set_account_type' 
				),
				array (
						gettext ( 'Billing Schedule' ),
						array (
								'name' => 'sweep_id',
								'class' => 'sweep_id',
								'id' => 'sweep_id' 
						),
						'SELECT',
						'',
						'',
						'tOOL TIP',
						'',
						'id',
						'sweep',
						'sweeplist',
						'build_dropdown',
						'',
						'' 
				),
				array (
						gettext ( 'Billing Day' ),
						array (
								"name" => 'invoice_day',
								"class" => "invoice_day" 
						),
						'SELECT',
						'',
						'',
						'tOOL TIP',
						'',
						'',
						'',
						'',
						'set_invoice_option' 
				),
				$balance,
				array (
						gettext ( 'Credit Limit' ),
						'INPUT',
						array (
								'name' => 'credit_limit',
								'size' => '20',
								'class' => "text field medium" 
						),
						'currency_decimal',
						'tOOL TIP',
						'' 
				),
				array (
						gettext ( 'Allow Local Calls' ),
						'local_call',
						'SELECT',
						'',
						'',
						'tOOL TIP',
						'Please Enter account number',
						'',
						'',
						'',
						'custom_status' 
				),
				array (
						gettext ( 'LC Charge / Min' ),
						'INPUT',
						array (
								'name' => 'charge_per_min',
								'size' => '20',
								'class' => "text field medium" 
						),
						'',
						'tOOL TIP',
						'' 
				),
				array (
						gettext ( 'Tax' ),
						'tax_id',
						'SELECT',
						'',
						'',
						'tOOL TIP',
						'Please Enter account number',
						'id',
						'taxes_description',
						'taxes',
						'build_dropdown',
						'where_arr',
						array (
								'status' => 0,
								"reseller_id" => $loginid 
						),
						'multi' 
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
		if ($id == 0) {
			$form [gettext ( 'Alert Threshold' )] = array (
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
							gettext ( 'Email Alerts ?' ),
							'notify_flag',
							'SELECT',
							'',
							'',
							'tOOL TIP',
							'',
							'',
							'',
							'',
							'custom_status_recording' 
					),
					array (
							gettext ( 'Balance Below' ),
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
							gettext ( 'Email' ),
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
		}
		$form ['button_save'] = array (
				'name' => 'action',
				'content' => gettext ( 'Save' ),
				'value' => 'save',
				'type' => 'submit',
				'class' => 'btn btn-line-parrot' 
		);
		$form ['button_cancel'] = array (
				'name' => 'action',
				'content' => gettext ( 'Cancel' ),
				'value' => 'cancel',
				'type' => 'button',
				'class' => 'btn btn-line-sky margin-x-10',
				'onclick' => 'return redirect_page(\'/accounts/customer_list/\')' 
		);
		if ($id > 0) {
			unset ( $form [gettext ( 'Account Settings' )] [3] );
		}
		return $form;
	}
	function customer_alert_threshold($entity_type) {
		$form ['forms'] = array (
				base_url () . 'accounts/' . $entity_type . '_alert_threshold_save/' . $entity_type . "/",
				array (
						"id" => "customer_alert_threshold",
						"name" => "customer_alert_threshold" 
				) 
		);
		$form [gettext ( 'Alert Threshold' )] = array (
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
						gettext ( 'Email Alerts ?' ),
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
						gettext ( 'Balance Below' ),
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
						gettext ( 'Email' ),
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
	function customer_bulk_generate_form() {
		$logintype = $this->CI->session->userdata ( 'logintype' );
		$sip_device = null;
		$opensips_device = null;
		if ($logintype == 5) {
			$account_data = $this->CI->session->userdata ( "accountinfo" );
			$loginid = $account_data ['id'];
		} else {
			$loginid = "0";
			if (common_model::$global_config ['system_config'] ['opensips'] == 0) {
				$opensips_device = array (
						gettext ( 'Create Opensips Device' ),
						'opensips_device_flag',
						'SELECT',
						'',
						'',
						'tOOL TIP',
						'Please Enter account number',
						'',
						'',
						'',
						'custom_status' 
				);
			} else {
				$sip_device = array (
						gettext ( 'Create SIP Device' ),
						'sip_device_flag',
						'SELECT',
						'',
						'',
						'tOOL TIP',
						'Please Enter account number',
						'',
						'',
						'',
						'custom_status' 
				);
			}
		}
		$form ['forms'] = array (
				base_url () . 'accounts/customer_bulk_save/',
				array (
						"id" => "customer_bulk_form",
						"name" => "customer_bulk_form" 
				) 
		);
		$form [gettext ( 'General Details' )] = array (
				array (
						gettext ( 'Account Count' ),
						'INPUT',
						array (
								'name' => 'count',
								'size' => '20',
								'class' => "text field medium" 
						),
						'trim|required|numeric|greater_than[0]|xss_clean',
						'tOOL TIP',
						'' 
				),
				array (
						gettext ( 'Start Prefix' ),
						'INPUT',
						array (
								'name' => 'prefix',
								'size' => '20',
								'class' => "text field medium" 
						),
						'trim|required|numeric|greater_than[0]|xss_clean',
						'tOOL TIP',
						'' 
				),
				array (
						gettext ( 'Acc. Number Length' ),
						'INPUT',
						array (
								'name' => 'account_length',
								'size' => '20',
								'class' => "text field medium" 
						),
						'trim|greater_than[0]|required|numeric|xss_clean',
						'tOOL TIP',
						'' 
				),
				array (
						gettext ( 'Company' ),
						'INPUT',
						array (
								'name' => 'company_name',
								'size' => '15',
								'class' => 'text field medium' 
						),
						'trim|required|alpha|xss_clean',
						'tOOL TIP',
						'' 
				),
				array (
						gettext ( 'Country' ),
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
				),
				array (
						gettext ( 'Timezone' ),
						array (
								'name' => 'timezone_id',
								'class' => 'timezone_id' 
						),
						'SELECT',
						'',
						array (
								"name" => "timezone_id",
								"rules" => "required" 
						),
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
						gettext ( 'Generate Pin' ),
						'pin',
						'SELECT',
						'',
						'',
						'tOOL TIP',
						'Please Enter account number',
						'',
						'',
						'',
						'set_pin_allow_customer' 
				),
				array (
						gettext ( 'Allow Recording' ),
						'is_recording',
						'SELECT',
						'',
						'',
						'tOOL TIP',
						'Please Enter account number',
						'',
						'',
						'',
						'custom_status_recording' 
				),
				array (
						gettext ( 'Allow IP Management' ),
						'allow_ip_management',
						'SELECT',
						'',
						'',
						'tOOL TIP',
						'Please Enter account number',
						'',
						'',
						'',
						'custom_status' 
				),
				$sip_device,
				$opensips_device 
		);
		$form [gettext ( 'Default Settings' )] = array (
				array (
						gettext ( 'Rate Group' ),
						array (
								'name' => 'pricelist_id',
								'class' => 'pricelist_id' 
						),
						'SELECT',
						'',
						"required",
						'tOOL TIP',
						'Please Enter account number',
						'id',
						'name',
						'pricelists',
						'build_dropdown',
						'where_arr',
						array (
								"status" => "0",
								"reseller_id" => $loginid 
						) 
				),
				array (
						gettext ( 'Account Type' ),
						'posttoexternal',
						'SELECT',
						'',
						'',
						'tOOL TIP',
						'Please Enter account number',
						'',
						'',
						'',
						'set_account_type' 
				),
				array (
						gettext ( 'Billing Schedule' ),
						array (
								'name' => 'sweep_id',
								'id' => 'sweep_id',
								'class' => 'sweep_id' 
						),
						'SELECT',
						'',
						'',
						'tOOL TIP',
						'',
						'id',
						'sweep',
						'sweeplist',
						'build_dropdown',
						'',
						'' 
				),
				array (
						gettext ( 'Billing Day' ),
						array (
								"name" => 'invoice_day',
								"class" => "invoice_day" 
						),
						'SELECT',
						'',
						'',
						'tOOL TIP',
						'',
						'',
						'',
						'',
						'set_invoice_option' 
				),
				array (
						gettext ( 'Currency' ),
						array (
								'name' => 'currency_id',
								'class' => 'currency_id' 
						),
						'SELECT',
						'',
						array (
								"name" => "currency_id",
								"rules" => "required" 
						),
						'tOOL TIP',
						'Please Enter account number',
						'id',
						'currencyname,currency',
						'currency',
						'build_concat_dropdown',
						'',
						array () 
				),
				array (
						gettext ( 'Balance' ),
						'INPUT',
						array (
								'name' => 'balance',
								'size' => '20',
								'class' => "text field medium" 
						),
						'trim|numeric|greater_than[0]|currency_decimal|xss_clean',
						'tOOL TIP',
						'' 
				),
				array (
						gettext ( 'Credit Limit' ),
						'INPUT',
						array (
								'name' => 'credit_limit',
								'size' => '20',
								'class' => "text field medium" 
						),
						'trim|currency_decimal|xss_clean',
						'tOOL TIP',
						'' 
				),
				array (
						gettext ( 'Valid Days' ),
						'INPUT',
						array (
								'name' => 'validfordays',
								'size' => '20',
								'class' => "text field medium" 
						),
						'trim|numeric|greater_than[0]|xss_clean',
						'tOOL TIP',
						'' 
				),
				array (
						gettext ( 'Allow Local Calls' ),
						'local_call',
						'SELECT',
						'',
						'',
						'tOOL TIP',
						'Please Enter account number',
						'',
						'',
						'',
						'custom_status' 
				),
				array (
						gettext ( 'LC Charge / Min' ),
						'INPUT',
						array (
								'name' => 'charge_per_min',
								'size' => '20',
								'class' => "text field medium" 
						),
						'numeric|greater_than[0]',
						'tOOL TIP',
						'' 
				) 
		);
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
	function get_customer_callerid_fields() {
		$form ['forms'] = array (
				base_url () . 'accounts/customer_add_callerid/',
				array (
						"id" => "callerid_form" 
				) 
		);
		$form [gettext ( 'Information' )] = array (
				array (
						'',
						'HIDDEN',
						array (
								'name' => 'flag' 
						),
						'',
						'',
						'',
						'' 
				),
				array (
						gettext ( 'Account' ),
						'INPUT',
						array (
								'name' => 'accountid',
								'size' => '20',
								'readonly' => true,
								'class' => "text field medium" 
						),
						'',
						'tOOL TIP',
						'Please Enter account number' 
				),
				array (
						gettext ( 'Enable' ),
						'status',
						'SELECT',
						'',
						'',
						'tOOL TIP',
						'Please Enter account number',
						'',
						'',
						'',
						'custom_status' 
				),
				array (
						gettext ( 'Caller Id Name' ),
						'INPUT',
						array (
								'name' => 'callerid_name',
								'size' => '20',
								'class' => "text field medium" 
						),
						'trim',
						'tOOL TIP',
						'' 
				),
				array (
						gettext ( 'Caller Id Number' ),
						'INPUT',
						array (
								'name' => 'callerid_number',
								'size' => '20',
								'class' => "text field medium" 
						),
						'trim',
						'tOOL TIP',
						'' 
				) 
		);
		$form ['button_save'] = array (
				'name' => 'action',
				'content' => gettext ( 'Save' ),
				'value' => 'save',
				"id" => "submit",
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
	function get_customer_payment_fields($currency, $number, $currency_id, $id) {
		$form ['forms'] = array (
				base_url () . 'accounts/customer_payment_save/',
				array (
						'id' => 'acccount_charges_form',
						'method' => 'POST',
						'name' => 'acccount_charges_form' 
				) 
		);
		$form ['​Refill information'] = array (
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
						gettext ( 'Account' ),
						'INPUT',
						array (
								'name' => 'accountid',
								'size' => '20',
								'value' => $number,
								'readonly' => true,
								'class' => "text field medium" 
						),
						'',
						'tOOL TIP',
						'Please Enter account number' 
				),
				array (
						gettext ( 'Amount' ) . "(" . $currency . ")",
						'INPUT',
						array (
								'name' => 'credit',
								'size' => '20',
								'class' => "text col-md-5 field medium" 
						),
						'trim|required',
						'tOOL TIP',
						'' 
				),
				array (
						gettext ( 'Type' ),
						'payment_type',
						'SELECT',
						'',
						'',
						'tOOL TIP',
						'Please Enter account number',
						'',
						'',
						'',
						'set_payment_type' 
				),
				array (
						gettext ( 'Note' ),
						'TEXTAREA',
						array (
								'name' => 'notes',
								'size' => '20',
								'cols' => '50',
								'rows' => '3',
								'class' => "text col-md-5 field medium",
								'style' => "width: 450px; height: 100px;" 
						),
						'',
						'tOOL TIP',
						'' 
				) 
		);
		/* *************************************** */
		$form ['button_save'] = array (
				'name' => 'action',
				'content' => gettext ( 'Process' ),
				'value' => 'save',
				'id' => "submit",
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
	function get_form_reseller_fields($id = false) {
		$readable = false;
		$invoice_config = null;
		$concurrent_calls = null;
		$logintype = $this->CI->session->userdata ( 'logintype' );
		$uname = $this->CI->common->find_uniq_rendno ( common_model::$global_config ['system_config'] ['cardlength'], 'number', 'accounts' );
		$password = $this->CI->common->generate_password ();
		$params = array (
				'name' => 'number',
				'value' => $uname,
				'size' => '20',
				'class' => "text field medium",
				'id' => 'number',
				'readonly' => true 
		);
		if ($logintype == 1 || $logintype == 5) {
			$account_data = $this->CI->session->userdata ( "accountinfo" );
			$loginid = $account_data ['id'];
		} else {
			$loginid = "0";
		}
		if ($id > 0) {
			$val = 'accounts.email.' . $id;
			$account_val = 'accounts.number.' . $id;
			$readable = 'disabled';
			$password = array (
					'Password',
					'PASSWORD',
					array (
							'name' => 'password',
							'id' => 'password_show',
							'onmouseover' => 'seetext(password_show)',
							'onmouseout' => 'hidepassword(password_show)',
							'size' => '20',
							'class' => "text field medium" 
					),
					'required|notMatch[number]|',
					'tOOL TIP',
					'' 
			);
			$concurrent_calls = array (
					'Concurrent Calls',
					'INPUT',
					array (
							'name' => 'maxchannels',
							'size' => '20',
							'class' => "text field medium" 
					),
					'numeric',
					'tOOL TIP',
					'' 
			);
			$account = array (
					'Account',
					'INPUT',
					$params,
					'required|integer|greater_than[0]|is_unique[' . $account_val . ']',
					'tOOL TIP',
					'' 
			);
		} else {
			$val = 'accounts.email';
			$account_val = 'accounts.number';
			$invoice_config = array (
					'Use same credential for Invoice Config',
					'invoice_config_flag',
					'SELECT',
					'',
					'',
					'tOOL TIP',
					'Please Enter account number',
					'',
					'',
					'',
					'set_prorate' 
			);
			$password = array (
					'Password',
					'INPUT',
					array (
							'name' => 'password',
							'value' => $password,
							'size' => '20',
							'class' => "text field medium",
							'id' => 'password' 
					),
					'required|',
					'tOOL TIP',
					'',
					'<i style="cursor:pointer; font-size: 17px; padding-left:10px; padding-top:6px;color: #1bcb61;" title="Reset Password" class="change_pass fa fa-refresh" ></i>' 
			);
			$account = array (
					'Account',
					'INPUT',
					$params,
					'required|integer|greater_than[0]|is_unique[' . $account_val . ']',
					'tOOL TIP',
					'',
					'<i style="cursor:pointer; font-size: 17px; padding-left:10px; padding-top:6px;color: #1bcb61;" title="Generate Account" class="change_number fa fa-refresh" ></i>' 
			);
		}
		if ($id == "") {
			$reg_url = array (
					'',
					'HIDDEN',
					array (
							'name' => 'id' 
					),
					'',
					'',
					'',
					'' 
			);
		} else {
			$reg_url = array (
					'Registration URL',
					'INPUT',
					array (
							'name' => 'registration_url',
							'size' => '20',
							'readonly' => true,
							'class' => "text field medium" 
					),
					'tOOL TIP',
					'' 
			);
		}
		/* * ****************************************************************** */
		$form ['forms'] = array (
				base_url () . 'accounts/reseller_save/',
				array (
						"id" => "reseller_form",
						"name" => "reseller_form" 
				) 
		);
		$form ['Client Panel Access'] = array (
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
								'value' => '1' 
						),
						'',
						'',
						'' 
				),
				$account,
				$password,
				$reg_url 
		);
		if ($id > 0) {
			
			$form [gettext ( 'Billing Information' )] = array (
					array (
							gettext ( 'Rate Group' ),
							'pricelist_id',
							'SELECT',
							'',
							array (
									"name" => "pricelist_id",
									'rules' => 'required' 
							),
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
					$concurrent_calls,
					array (
							gettext ( 'Billing Schedule' ),
							array (
									'name' => 'sweep_id',
									'class' => 'sweep_id' 
							),
							'SELECT',
							'',
							'',
							'tOOL TIP',
							'',
							'id',
							'sweep',
							'sweeplist',
							'build_dropdown',
							'',
							'' 
					),
					array (
							gettext ( 'Billing Day' ),
							array (
									"name" => 'invoice_day',
									"class" => "invoice_day" 
							),
							'SELECT',
							'',
							'',
							'tOOL TIP',
							'',
							'',
							'',
							'',
							'set_invoice_option' 
					),
					array (
							gettext ( 'Currency' ),
							array (
									'name' => 'currency_id',
									'class' => 'currency_id' 
							),
							'SELECT',
							'',
							array (
									"name" => "currency_id",
									"rules" => "required" 
							),
							'tOOL TIP',
							'Please Enter account number',
							'id',
							'currencyname,currency',
							'currency',
							'build_concat_dropdown',
							'',
							array () 
					),
					array (
							gettext ( 'Account Type' ),
							array (
									'name' => 'posttoexternal',
									'disabled' => $readable,
									'class' => 'posttoexternal',
									'id' => 'posttoexternal' 
							),
							'SELECT',
							'',
							'',
							'tOOL TIP',
							'Please Enter account number',
							'',
							'',
							'',
							'set_account_type' 
					),
					array (
							gettext ( 'Credit Limit' ),
							'INPUT',
							array (
									'name' => 'credit_limit',
									'size' => '20',
									'class' => "text field medium" 
							),
							'',
							'tOOL TIP',
							'' 
					),
					array (
							gettext ( 'Tax' ),
							'tax_id',
							'SELECT',
							'',
							'',
							'tOOL TIP',
							'Please Enter account number',
							'id',
							'taxes_description',
							'taxes',
							'build_dropdown',
							'where_arr',
							array (
									'status' => 0,
									'reseller_id' => $loginid 
							),
							'multi' 
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
		} else {
			$form [gettext ( 'Billing Information' )] = array (
					array (
							gettext ( 'Rate Group' ),
							'pricelist_id',
							'SELECT',
							'',
							array (
									"name" => "pricelist_id",
									'rules' => 'required' 
							),
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
							gettext ( 'Billing Schedule' ),
							array (
									'name' => 'sweep_id',
									'class' => 'sweep_id' 
							),
							'SELECT',
							'',
							'',
							'tOOL TIP',
							'',
							'id',
							'sweep',
							'sweeplist',
							'build_dropdown',
							'',
							'' 
					),
					array (
							gettext ( 'Billing Day' ),
							array (
									"name" => 'invoice_day',
									"class" => "invoice_day" 
							),
							'SELECT',
							'',
							'',
							'tOOL TIP',
							'',
							'',
							'',
							'',
							'set_invoice_option' 
					),
					array (
							gettext ( 'Currency' ),
							array (
									'name' => 'currency_id',
									'class' => 'currency_id' 
							),
							'SELECT',
							'',
							array (
									"name" => "currency_id",
									"rules" => "required" 
							),
							'tOOL TIP',
							'Please Enter account number',
							'id',
							'currencyname,currency',
							'currency',
							'build_concat_dropdown',
							'',
							array () 
					),
					array (
							gettext ( 'Account Type' ),
							array (
									'name' => 'posttoexternal',
									'disabled' => $readable,
									'class' => 'posttoexternal',
									'id' => 'posttoexternal' 
							),
							'SELECT',
							'',
							'',
							'tOOL TIP',
							'Please Enter account number',
							'',
							'',
							'',
							'set_account_type' 
					),
					array (
							gettext ( 'Credit Limit' ),
							'INPUT',
							array (
									'name' => 'credit_limit',
									'size' => '20',
									'class' => "text field medium" 
							),
							'',
							'tOOL TIP',
							'' 
					),
					array (
							gettext ( 'Tax' ),
							'tax_id',
							'SELECT',
							'',
							'',
							'tOOL TIP',
							'Please Enter account number',
							'id',
							'taxes_description',
							'taxes',
							'build_dropdown',
							'where_arr',
							array (
									'status' => 0,
									'reseller_id' => $loginid 
							),
							'multi' 
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
					),
					$invoice_config 
			);
		}
		$form [gettext ( 'Reseller Profile' )] = array (
				array (
						gettext ( 'First Name' ),
						'INPUT',
						array (
								'name' => 'first_name',
								'id' => 'first_name',
								'size' => '50',
								'class' => "text field medium" 
						),
						'trim|required|xss_clean',
						'tOOL TIP',
						'' 
				),
				array (
						gettext ( 'Last Name' ),
						'INPUT',
						array (
								'name' => 'last_name',
								'size' => '15',
								'class' => "text field medium" 
						),
						'trim|xss_clean',
						'tOOL TIP',
						'' 
				),
				array (
						gettext ( 'Company' ),
						'INPUT',
						array (
								'name' => 'company_name',
								'size' => '50',
								'class' => 'text field medium' 
						),
						'trim|xss_clean',
						'tOOL TIP',
						'' 
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
						'' 
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
						'' 
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
						'' 
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
						'' 
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
						'' 
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
						'' 
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
						'' 
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
						'' 
				),
				array (
						gettext ( 'Country' ),
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
				),
				array (
						gettext ( 'Timezone' ),
						array (
								'name' => 'timezone_id',
								'class' => 'timezone_id' 
						),
						'SELECT',
						'',
						array (
								"name" => "timezone_id",
								"rules" => "required" 
						),
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
						gettext ( 'Account Status' ),
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
		);
		if ($id == 0) {
			$form [gettext ( 'Alert Threshold' )] = array (
					array (
							'Email Alert?',
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
							gettext ( 'Balance Below' ),
							'INPUT',
							array (
									'name' => 'notify_credit_limit',
									'size' => '20',
									'class' => "text field medium" 
							),
							'',
							'tOOL TIP',
							'' 
					),
					array (
							gettext ( 'Email' ),
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
		}
		$form ['button_cancel'] = array (
				'name' => 'action',
				'content' => gettext ( 'Cancel' ),
				'value' => 'cancel',
				'type' => 'button',
				'class' => 'btn btn-line-sky margin-x-10',
				'onclick' => 'return redirect_page(\'/accounts/reseller_list/\')' 
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
	function get_form_admin_fields($entity_type = '', $id = false) {
		$uname = $this->CI->common->find_uniq_rendno ( common_model::$global_config ['system_config'] ['cardlength'], 'number', 'accounts' );
		$params = array (
				'name' => 'number',
				'value' => $uname,
				'size' => '20',
				'class' => "text field medium",
				'id' => 'number',
				'readonly' => true 
		);
		if ($id > 0) {
			$val = 'accounts.email.' . $id;
			$account_val = 'accounts.number.' . $id;
			$password = array (
					'Password',
					'PASSWORD',
					array (
							'name' => 'password',
							'id' => 'password_show',
							'onmouseover' => 'seetext(password_show)',
							'onmouseout' => 'hidepassword(password_show)',
							'size' => '20',
							'class' => "text field medium" 
					),
					'required|notMatch[number]|',
					'tOOL TIP',
					'' 
			);
			$account = array (
					'Account',
					'INPUT',
					$params,
					'required|is_unique[' . $account_val . ']',
					'tOOL TIP',
					'' 
			);
			if ($entity_type == 'subadmin') {
				$account_status = array (
						gettext ( 'Account Status' ),
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
				);
			} else {
				$account_status = null;
			}
		} else {
			$val = 'accounts.email';
			$account_val = 'accounts.number';
			$password = $this->CI->common->generate_password ();
			$password = array (
					'Password',
					'INPUT',
					array (
							'name' => 'password',
							'value' => $password,
							'size' => '20',
							'class' => "text field medium",
							'id' => 'password' 
					),
					'required|',
					'tOOL TIP',
					'',
					'<i style="cursor:pointer; font-size: 17px; padding-left:10px; padding-top:6px;color: #1bcb61;" title="Reset Password" class="change_pass fa fa-refresh" ></i>' 
			);
			$account = array (
					'Account',
					'INPUT',
					$params,
					'required|is_unique[' . $account_val . ']',
					'tOOL TIP',
					'',
					'<i style="cursor:pointer; font-size: 17px; padding-left:10px; padding-top:6px;color: #1bcb61;" title="Generate Account" class="change_number fa fa-refresh" ></i>' 
			);
			$account_status = array (
					gettext ( 'Account Status' ),
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
			);
			/* * ****************** */
		}
		
		$type = $entity_type == 'admin' ? 2 : 4;
		$form ['forms'] = array (
				base_url () . 'accounts/' . $entity_type . '_save/',
				array (
						"id" => "admin_form",
						"name" => "admin_form" 
				) 
		);
		$form ['Client Panel Access'] = array (
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
								'value' => $type 
						),
						'',
						'',
						'' 
				),
				$account,
				$password,
				/*                 * ********************* */
		);
		$form [gettext ( $entity_type . ' Profile' )] = array (
				array (
						gettext ( 'First Name' ),
						'INPUT',
						array (
								'name' => 'first_name',
								'id' => 'first_name',
								'size' => '15',
								'maxlength' => '40',
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
						'trim|xss_clean',
						'tOOL TIP',
						'Please Enter Password' 
				),
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
						'' 
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
				$account_status,
				array (
						gettext ( 'Currency' ),
						array (
								'name' => 'currency_id',
								'class' => 'currency_id' 
						),
						'SELECT',
						'',
						'',
						'tOOL TIP',
						'Please Enter account number',
						'id',
						'currencyname,currency',
						'currency',
						'build_concat_dropdown',
						'',
						array () 
				) 
		);
		
		$form ['button_cancel'] = array (
				'name' => 'action',
				'content' => gettext ( 'Cancel' ),
				'value' => 'cancel',
				'type' => 'button',
				'class' => 'btn btn-line-sky margin-x-10',
				'onclick' => 'return redirect_page(\'/accounts/admin_list/\')' 
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
	
	/**
	 * ASTPP 3.0
	 * Reseller Batch Update
	 */
	function reseller_batch_update_form() {
		$status = array (
				'Status',
				array (
						'name' => 'status[status]',
						'id' => 'status' 
				),
				'SELECT',
				'',
				'',
				'tOOL TIP',
				'Please Enter account number',
				'id',
				'name',
				'',
				'set_status',
				'',
				'',
				array (
						'name' => 'status[operator]',
						'class' => 'update_drp' 
				),
				'update_drp_type' 
		);
		$form ['forms'] = array (
				"accounts/reseller_batch_update/",
				array (
						'id' => "reseller_batch_update" 
				) 
		);
		$form [gettext ( 'Batch Update' )] = array (
				array (
						gettext ( 'Rate Group' ),
						array (
								'name' => 'pricelist_id[pricelist_id]',
								'id' => 'pricelist_id' 
						),
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
						),
						array (
								'name' => 'pricelist_id[operator]',
								'class' => 'update_drp' 
						),
						'update_drp_type' 
				),
				array (
						gettext ( 'Balance' ),
						'INPUT',
						array (
								'name' => 'balance[balance]',
								'id' => 'balance',
								'value' => '',
								'size' => '20',
								'class' => "text field " 
						),
						'',
						'Tool tips info',
						'1',
						array (
								'name' => 'balance[operator]',
								'class' => 'update_drp' 
						),
						'',
						'',
						'',
						'update_int_type',
						'' 
				),
				
				$status 
		);
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
	
	/* * ***************************************************************************************************************************** */
	
	/**
	 * ASTPP 3.0
	 * Customer Batch Update
	 */
	function customer_batch_update_form() {
		$status = array (
				'Status',
				array (
						'name' => 'status[status]',
						'id' => 'status' 
				),
				'SELECT',
				'',
				'',
				'tOOL TIP',
				'Please Enter account number',
				'id',
				'name',
				'',
				'set_status',
				'',
				'',
				array (
						'name' => 'status[operator]',
						'class' => 'update_drp' 
				),
				'update_drp_type' 
		);
		$form ['forms'] = array (
				"accounts/customer_batch_update/",
				array (
						'id' => "reseller_batch_update" 
				) 
		);
		$form [gettext ( 'Batch Update' )] = array (
				array (
						gettext ( 'Rate Group' ),
						array (
								'name' => 'pricelist_id[pricelist_id]',
								'id' => 'pricelist_id' 
						),
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
						),
						array (
								'name' => 'pricelist_id[operator]',
								'class' => 'update_drp' 
						),
						'update_drp_type' 
				),
				array (
						gettext ( 'Balance' ),
						'INPUT',
						array (
								'name' => 'balance[balance]',
								'id' => 'balance',
								'value' => '',
								'size' => '20',
								'class' => "text field " 
						),
						'',
						'Tool tips info',
						'1',
						array (
								'name' => 'balance[operator]',
								'class' => 'update_drp' 
						),
						'',
						'',
						'',
						'update_int_type',
						'' 
				),
				$status 
		);
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
	
	/* * ***************************************************************************************************************************** */
	function get_search_customer_form() {
		$logintype = $this->CI->session->userdata ( 'userlevel_logintype' );
		if ($logintype != 1) {
			$form ['forms'] = array (
					"",
					array (
							'id' => "account_search" 
					) 
			);
			$form [gettext ( 'Search' )] = array (
					array (
							gettext ( 'Account' ),
							'INPUT',
							array (
									'name' => 'number[number]',
									'',
									'size' => '20',
									'class' => "text field " 
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
							gettext ( 'First Name' ),
							'INPUT',
							array (
									'name' => 'first_name[first_name]',
									'',
									'id' => 'first_name',
									'size' => '15',
									'class' => "text field " 
							),
							'',
							'tOOL TIP',
							'1',
							'first_name[first_name-string]',
							'',
							'',
							'',
							'search_string_type',
							'' 
					),
					array (
							gettext ( 'Last Name' ),
							'INPUT',
							array (
									'name' => 'last_name[last_name]',
									'value' => '',
									'size' => '20',
									'class' => "text field " 
							),
							'',
							'Tool tips info',
							'1',
							'last_name[last_name-string]',
							'',
							'',
							'',
							'search_string_type',
							'' 
					),
					array (
							gettext ( 'Company' ),
							'INPUT',
							array (
									'name' => 'company_name[company_name]',
									'value' => '',
									'size' => '20',
									'class' => "text field " 
							),
							'',
							'Tool tips info',
							'1',
							'company_name[company_name-string]',
							'',
							'',
							'',
							'search_string_type',
							'' 
					),
					array (
							'CC',
							'INPUT',
							array (
									'name' => 'maxchannels[maxchannels]',
									'value' => '',
									'size' => '20',
									'class' => "text field " 
							),
							'',
							'Tool tips info',
							'1',
							'maxchannels[maxchannels-integer]',
							'',
							'',
							'',
							'search_int_type' 
					),
					
					array (
							gettext ( 'Balance' ),
							'INPUT',
							array (
									'name' => 'balance[balance]',
									'value' => '',
									'size' => '20',
									'class' => "text field " 
							),
							'',
							'Tool tips info',
							'1',
							'balance[balance-integer]',
							'',
							'',
							'',
							'search_int_type',
							'' 
					),
					array (
							gettext ( 'Credit Limit' ),
							'INPUT',
							array (
									'name' => 'credit_limit[credit_limit]',
									'value' => '',
									'size' => '20',
									'class' => "text field " 
							),
							'',
							'Tool tips info',
							'1',
							'credit_limit[credit_limit-integer]',
							'',
							'',
							'',
							'search_int_type',
							'' 
					),
					array (
							gettext ( 'Email' ),
							'INPUT',
							array (
									'name' => 'email[email]',
									'value' => '',
									'size' => '20',
									'class' => "text field " 
							),
							'',
							'Tool tips info',
							'1',
							'email[email-string]',
							'',
							'',
							'',
							'search_string_type',
							'' 
					),
					array (
							gettext ( 'First Used' ),
							'INPUT',
							array (
									'name' => 'first_used[0]',
									'',
									'size' => '20',
									'class' => "text field",
									'id' => 'first_used' 
							),
							'',
							'tOOL TIP',
							'',
							'first_used[first_used-date]' 
					),
					array (
							gettext ( 'Expiry Date' ),
							'INPUT',
							array (
									'name' => 'expiry[0]',
									'id' => 'expiry',
									'size' => '20',
									'class' => "text field " 
							),
							'',
							'tOOL TIP',
							'',
							'expiry[expiry-date]' 
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
							'set_search_status' 
					),
					array (
							gettext ( 'Created Date' ),
							'INPUT',
							array (
									'name' => 'creation[0]',
									'',
									'size' => '20',
									'class' => "text field",
									'id' => 'creation' 
							),
							'',
							'tOOL TIP',
							'',
							'creation[creation-date]' 
					),
					array (
							gettext ( 'Entity Type' ),
							'type',
							'SELECT',
							'',
							'',
							'tOOL TIP',
							'Please Enter account number',
							'',
							'',
							'',
							'set_entity_type_customer' 
					),
					array (
							gettext ( 'Account Type' ),
							'posttoexternal',
							'SELECT',
							'',
							'',
							'tOOL TIP',
							'Please Enter account number',
							'',
							'',
							'',
							'set_account_type_search' 
					),
					array (
							gettext ( 'Billing Cycle' ),
							'sweep_id',
							'SELECT',
							'',
							'',
							'tOOL TIP',
							'Please Enter account number',
							'',
							'',
							'',
							'set_Billing_Schedule_status' 
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
		} else {
			
			$form ['forms'] = array (
					"",
					array (
							'id' => "account_search" 
					) 
			);
			$form [gettext ( 'Search' )] = array (
					array (
							gettext ( 'Account' ),
							'INPUT',
							array (
									'name' => 'number[number]',
									'',
									'size' => '20',
									'class' => "text field " 
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
							gettext ( 'First Name' ),
							'INPUT',
							array (
									'name' => 'first_name[first_name]',
									'',
									'id' => 'first_name',
									'size' => '15',
									'class' => "text field " 
							),
							'',
							'tOOL TIP',
							'1',
							'first_name[first_name-string]',
							'',
							'',
							'',
							'search_string_type',
							'' 
					),
					array (
							gettext ( 'Last Name' ),
							'INPUT',
							array (
									'name' => 'last_name[last_name]',
									'value' => '',
									'size' => '20',
									'class' => "text field " 
							),
							'',
							'Tool tips info',
							'1',
							'last_name[last_name-string]',
							'',
							'',
							'',
							'search_string_type',
							'' 
					),
					array (
							gettext ( 'Company' ),
							'INPUT',
							array (
									'name' => 'company_name[company_name]',
									'value' => '',
									'size' => '20',
									'class' => "text field " 
							),
							'',
							'Tool tips info',
							'1',
							'company_name[company_name-string]',
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
									"status" => "0",
									"reseller_id" => "0" 
							) 
					),
					array (
							gettext ( 'Balance' ),
							'INPUT',
							array (
									'name' => 'balance[balance]',
									'value' => '',
									'size' => '20',
									'class' => "text field " 
							),
							'',
							'Tool tips info',
							'1',
							'balance[balance-integer]',
							'',
							'',
							'',
							'search_int_type',
							'' 
					),
					array (
							gettext ( 'Credit Limit' ),
							'INPUT',
							array (
									'name' => 'credit_limit[credit_limit]',
									'value' => '',
									'size' => '20',
									'class' => "text field " 
							),
							'',
							'Tool tips info',
							'1',
							'credit_limit[credit_limit-integer]',
							'',
							'',
							'',
							'search_int_type',
							'' 
					),
					array (
							gettext ( 'Email' ),
							'INPUT',
							array (
									'name' => 'email[email]',
									'value' => '',
									'size' => '20',
									'class' => "text field " 
							),
							'',
							'Tool tips info',
							'1',
							'email[email-string]',
							'',
							'',
							'',
							'search_string_type',
							'' 
					),
					array (
							gettext ( 'First Used' ),
							'INPUT',
							array (
									'name' => 'first_used[0]',
									'',
									'size' => '20',
									'class' => "text field",
									'id' => 'first_used' 
							),
							'',
							'tOOL TIP',
							'',
							'first_used[first_used-date]' 
					),
					array (
							gettext ( 'Expiry Date' ),
							'INPUT',
							array (
									'name' => 'expiry[0]',
									'id' => 'expiry',
									'size' => '20',
									'class' => "text field " 
							),
							'',
							'tOOL TIP',
							'',
							'expiry[expiry-date]' 
					),
					array (
							'CC',
							'INPUT',
							array (
									'name' => 'maxchannels[maxchannels]',
									'value' => '',
									'size' => '20',
									'class' => "text field " 
							),
							'',
							'Tool tips info',
							'1',
							'maxchannels[maxchannels-integer]',
							'',
							'',
							'',
							'search_int_type' 
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
							gettext ( 'Created Date' ),
							'INPUT',
							array (
									'name' => 'creation[0]',
									'',
									'size' => '20',
									'class' => "text field",
									'id' => 'creation' 
							),
							'',
							'tOOL TIP',
							'',
							'creation[creation-date]' 
					),
					array (
							gettext ( 'Account Type' ),
							'posttoexternal',
							'SELECT',
							'',
							'',
							'tOOL TIP',
							'Please Enter account number',
							'',
							'',
							'',
							'set_account_type_search' 
					),
					array (
							gettext ( 'Billing Cycle' ),
							'sweep_id',
							'SELECT',
							'',
							'',
							'tOOL TIP',
							'Please Enter account number',
							'',
							'',
							'',
							'set_Billing_Schedule_status' 
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
		}
		
		$form ['button_search'] = array (
				'name' => 'action',
				'id' => "account_search_btn",
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
	function get_reseller_search_form() {
		$form ['forms'] = array (
				"",
				array (
						'id' => "account_search" 
				) 
		);
		$form [gettext ( 'Search' )] = array (
				array (
						gettext ( 'Account' ),
						'INPUT',
						array (
								'name' => 'number[number]',
								'',
								'size' => '20',
								'class' => "text field " 
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
						gettext ( 'First Name' ),
						'INPUT',
						array (
								'name' => 'first_name[first_name]',
								'',
								'id' => 'first_name',
								'size' => '15',
								'class' => "text field " 
						),
						'',
						'tOOL TIP',
						'1',
						'first_name[first_name-string]',
						'',
						'',
						'',
						'search_string_type',
						'' 
				),
				array (
						gettext ( 'Last Name' ),
						'INPUT',
						array (
								'name' => 'last_name[last_name]',
								'value' => '',
								'size' => '20',
								'class' => "text field " 
						),
						'',
						'Tool tips info',
						'1',
						'last_name[last_name-string]',
						'',
						'',
						'',
						'search_string_type',
						'' 
				),
				array (
						gettext ( 'Email' ),
						'INPUT',
						array (
								'name' => 'email[email]',
								'value' => '',
								'size' => '20',
								'class' => "text field " 
						),
						'',
						'Tool tips info',
						'1',
						'email[email-string]',
						'',
						'',
						'',
						'search_string_type',
						'' 
				),
				array (
						gettext ( 'Company' ),
						'INPUT',
						array (
								'name' => 'company_name[company_name]',
								'value' => '',
								'size' => '20',
								'class' => "text field " 
						),
						'',
						'Tool tips info',
						'1',
						'company_name[company_name-string]',
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
								"status" => "0",
								"reseller_id" => "0" 
						) 
				),
				array (
						gettext ( 'Account Type' ),
						'posttoexternal',
						'SELECT',
						'',
						'',
						'tOOL TIP',
						'Please Enter account number',
						'',
						'',
						'',
						'set_account_type_search' 
				),
				array (
						gettext ( 'Balance' ),
						'INPUT',
						array (
								'name' => 'balance[balance]',
								'value' => '',
								'size' => '20',
								'class' => "text field " 
						),
						'',
						'Tool tips info',
						'1',
						'balance[balance-integer]',
						'',
						'',
						'',
						'search_int_type',
						'' 
				),
				
				array (
						gettext ( 'Credit Limit' ),
						'INPUT',
						array (
								'name' => 'credit_limit[credit_limit]',
								'value' => '',
								'size' => '20',
								'class' => "text field " 
						),
						'',
						'Tool tips info',
						'1',
						'credit_limit[credit_limit-integer]',
						'',
						'',
						'',
						'search_int_type',
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
						gettext ( 'Created Date' ),
						'INPUT',
						array (
								'name' => 'creation[0]',
								'',
								'size' => '20',
								'class' => "text field",
								'id' => 'creation' 
						),
						'',
						'tOOL TIP',
						'',
						'creation[creation-date]' 
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
				'id' => "account_search_btn",
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
	function get_admin_search_form() {
		$form ['forms'] = array (
				"",
				array (
						'id' => "account_search" 
				) 
		);
		$form ['Search'] = array (
				array (
						gettext ( 'Account' ),
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
						gettext ( 'First Name' ),
						'INPUT',
						array (
								'name' => 'first_name[first_name]',
								'',
								'id' => 'first_name',
								'size' => '15',
								'class' => "text field " 
						),
						'',
						'tOOL TIP',
						'1',
						'first_name[first_name-string]',
						'',
						'',
						'',
						'search_string_type',
						'' 
				),
				array (
						gettext ( 'Last Name' ),
						'INPUT',
						array (
								'name' => 'last_name[last_name]',
								'value' => '',
								'size' => '20',
								'class' => "text field " 
						),
						'',
						'Tool tips info',
						'1',
						'last_name[last_name-string]',
						'',
						'',
						'',
						'search_string_type',
						'' 
				),
				array (
						gettext ( 'Company' ),
						'INPUT',
						array (
								'name' => 'company_name[company_name]',
								'value' => '',
								'size' => '20',
								'class' => "text field " 
						),
						'',
						'Tool tips info',
						'1',
						'company_name[company_name-string]',
						'',
						'',
						'',
						'search_string_type',
						'' 
				),
				array (
						gettext ( 'Email' ),
						'INPUT',
						array (
								'name' => 'email[email]',
								'value' => '',
								'size' => '20',
								'class' => "text field " 
						),
						'',
						'Tool tips info',
						'1',
						'email[email-string]',
						'',
						'',
						'',
						'search_string_type',
						'' 
				),
				array (
						gettext ( 'Entity Type' ),
						'type',
						'SELECT',
						'',
						'',
						'tOOL TIP',
						'Please Enter account number',
						'',
						'',
						'',
						'set_entity_type_admin' 
				),
				array (
						gettext ( 'Phone' ),
						'INPUT',
						array (
								'name' => 'telephone_1[telephone_1]',
								'value' => '',
								'size' => '20',
								'class' => "text field " 
						),
						'',
						'Tool tips info',
						'1',
						'telephone_1[telephone_1-integer]',
						'',
						'',
						'',
						'search_int_type',
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
				'id' => "account_search_btn",
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
	function build_account_list_for_admin() {
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
				array (
						gettext ( "Account" ),
						"135",
						"number",
						"number",
						"accounts",
						"account_number_icon",
						"EDITABLE",
						"true",
						"left" 
				),
				array (
						gettext ( "First Name" ),
						"150",
						"first_name",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Last Name" ),
						"150",
						"last_name",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Company" ),
						"150",
						"company_name",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Email" ),
						"170",
						"email",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Phone" ),
						"150",
						"telephone_1",
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
						gettext ( "Status" ),
						"110",
						"status",
						"status",
						"accounts",
						"get_status",
						"",
						"true",
						"center" 
				),
				/**
				 * ****************************************************************
				 */
				array (
						gettext ( "Action" ),
						"100",
						"",
						"",
						"",
						array (
								"EDIT" => array (
										"url" => "accounts/admin_edit/",
										"mode" => "single" 
								),
								"DELETE" => array (
										"url" => "accounts/admin_delete/",
										"mode" => "single" 
								) 
						) 
				) 
		) );
		return $grid_field_arr;
	}
	function build_account_list_for_customer() {
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
				array (
						gettext ( "Account" ),
						"125",
						"number",
						"number",
						"accounts",
						"account_number_icon",
						"EDITABLE",
						"true",
						"left" 
				),
				array (
						gettext ( "First Name" ),
						"95",
						"first_name",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Last Name" ),
						"95",
						"last_name",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Company" ),
						"85",
						"company_name",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Rate Group" ),
						"85",
						"pricelist_id",
						"name",
						"pricelists",
						"get_field_name",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Balance" ) . " ($currency)",
						"100",
						"balance",
						"balance",
						"balance",
						"convert_to_currency_account",
						"",
						"true",
						"right" 
				),
				array (
						gettext ( "Credit Limit" ) . " ($currency)",
						"120",
						"credit_limit",
						"credit_limit",
						"credit_limit",
						"convert_to_currency_account",
						"",
						"true",
						"right" 
				),
				array (
						gettext ( "First Used" ),
						"80",
						"first_used",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Expiry Date" ),
						"80",
						"expiry",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				array (
						"CC",
						"45",
						"maxchannels",
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
						"accounts",
						"get_status",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Created Date" ),
						"90",
						"creation",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				/**
				 * *********************************************************************
				 */
				array (
						gettext ( "Action" ),
						"140",
						"",
						"",
						"",
						array (
								"PAYMENT" => array (
										"url" => "accounts/customer_payment_process_add/",
										"mode" => "single" 
								),
								"CALLERID" => array (
										"url" => "accounts/customer_add_callerid/",
										"mode" => "popup" 
								),
								"EDIT" => array (
										"url" => "accounts/customer_edit/",
										"mode" => "single" 
								),
								"DELETE" => array (
										"url" => "accounts/customer_delete/",
										"mode" => "single" 
								) 
						) 
				) 
		) );
		return $grid_field_arr;
	}
	function build_account_list_for_reseller() {
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
				array (
						gettext ( "Account" ),
						"105",
						"number",
						"",
						"",
						"",
						"EDITABLE",
						"true",
						"center" 
				),
				array (
						gettext ( "First Name" ),
						"120",
						"first_name",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Last Name" ),
						"115",
						"last_name",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Company" ),
						"130",
						"company_name",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Rate Group" ),
						"95",
						"pricelist_id",
						"name",
						"pricelists",
						"get_field_name",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Account Type" ),
						"107",
						"posttoexternal",
						"posttoexternal",
						"posttoexternal",
						"get_account_type",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Balance" ) . " ($currency)",
						"100",
						"balance",
						"balance",
						"balance",
						"convert_to_currency",
						"",
						"true",
						"right" 
				),
				array (
						gettext ( "Credit Limit" ) . " ($currency)",
						"120",
						"credit_limit",
						"credit_limit",
						"credit_limit",
						"convert_to_currency",
						"",
						"true",
						"right" 
				),
				array (
						gettext ( "Status" ),
						"110",
						"status",
						"status",
						"accounts",
						"get_status",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Created Date" ),
						"90",
						"creation",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				/**
				 * ***********************************************************
				 */
				array (
						gettext ( "Action" ),
						"139",
						"",
						"",
						"",
						array (
								"PAYMENT" => array (
										"url" => "accounts/customer_payment_process_add/",
										"mode" => "single" 
								),
								"CALLERID" => array (
										"url" => "accounts/customer_add_callerid/",
										"mode" => 'popup' 
								),
								"EDIT" => array (
										"url" => "accounts/reseller_edit/",
										"mode" => "single" 
								),
								"DELETE" => array (
										"url" => "accounts/reseller_delete/",
										"mode" => "single" 
								) 
						) 
				) 
		) );
		return $grid_field_arr;
	}
	function build_grid_buttons_customer() {
		$logintype = $this->CI->session->userdata ( 'userlevel_logintype' );
		$provider = null;
		$account_import= array();
		if ($logintype != 1){
			$account_import = array (
						gettext ( "Import customers" ),
						"btn btn-line-warning",
						"fa fa-upload fa-lg",
						"button_action",
						"/account_import/customer_import_mapper/",
						'single' 
			);
			$provider = array (
					gettext ( "Create Provider" ),
					"btn btn-line-blue btn",
					"fa fa-plus-circle fa-lg",
					"button_action",
					"/accounts/provider_add/" 
			);
		}	
			// array(display name, width, db_field_parent_table,feidname, db_field_child_table,function name);
		$buttons_json = json_encode ( array (
				array (
						gettext ( "Create Customer" ),
						"btn btn-line-warning btn",
						"fa fa-plus-circle fa-lg",
						"button_action",
						"/accounts/customer_add/" 
				),
				array (
						gettext ( "Mass Create" ),
						"btn btn-line-warning btn",
						"fa fa-plus-circle fa-lg",
						"button_action",
						"/accounts/customer_bulk_creation/",
						"popup",
						"medium" 
				),
				$account_import,
				$provider,
				array (
						gettext ( "Export" ),
						"btn btn-xing",
						" fa fa-download fa-lg",
						"button_action",
						"/accounts/customer_export_cdr_xls/",
						'single' 
				),
				array (
						gettext ( "Delete" ),
						"btn btn-line-danger",
						"fa fa-times-circle fa-lg",
						"button_action",
						"/accounts/customer_selected_delete/" 
				) 
		) );
		return $buttons_json;
	}
	function build_grid_buttons_admin() {
		$buttons_json = json_encode ( array (
				array (
						gettext ( "Create Admin" ),
						"btn btn-line-warning btn",
						"fa fa-plus-circle fa-lg",
						"button_action",
						"/accounts/admin_add/" 
				),
				array (
						gettext ( "Create Subadmin" ),
						"btn btn-line-warning btn",
						"fa fa-plus-circle fa-lg",
						"button_action",
						"/accounts/subadmin_add/4" 
				),
				array (
						gettext ( "Delete" ),
						"btn btn-line-danger",
						"fa fa-times-circle fa-lg",
						"button_action",
						"/accounts/admin_selected_delete/" 
				) 
		) );
		return $buttons_json;
	}
	function build_grid_buttons_reseller() {
		$buttons_json = json_encode ( array (
				array (
						gettext ( "Create" ),
						"btn btn-line-warning btn",
						"fa fa-plus-circle fa-lg",
						"button_action",
						"/accounts/reseller_add/" 
				),
				array (
						gettext ( "Export" ),
						"btn btn-xing",
						" fa fa-download fa-lg",
						"button_action",
						"/accounts/reseller_export_cdr_xls",
						'single' 
				),
				array (
						gettext ( "Delete" ),
						"btn btn-line-danger",
						"fa fa-times-circle fa-lg",
						"button_action",
						"/accounts/reseller_selected_delete/" 
				) 
		) );
		return $buttons_json;
	}
	function build_ip_list_for_customer($accountid, $accountype) {
		$grid_field_arr = json_encode ( array (
				array (
						gettext ( 'Name' ),
						"180",
						"name",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( 'IP' ),
						"180",
						"ip",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( 'Prefix' ),
						"180",
						"prefix",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( 'Created Date' ),
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
						gettext ( 'Modified Date' ),
						"160",
						"last_modified_date",
						"last_modified_date",
						"last_modified_date",
						"convert_GMT_to",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( 'Action' ),
						"150",
						"",
						"",
						"",
						array (
								"DELETE" => array (
										"url" => "accounts/" . $accountype . "_ipmap_action/delete/$accountid/$accountype/",
										"mode" => "single" 
								) 
						) 
				) 
		) );
		return $grid_field_arr;
	}
	function build_animap_list_for_customer($accountid, $accounttype) {
		$grid_field_arr = json_encode ( array (
				array (
						gettext ( "Caller ID" ),
						"200",
						"number",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Status" ),
						"180",
						"status",
						"status",
						"ani_map",
						"get_status",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Created Date" ),
						"200",
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
						"170",
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
						"200",
						"",
						"",
						"",
						array (
								"DELETE" => array (
										"url" => "accounts/" . $accounttype . "_animap_action/delete/$accountid/",
										"mode" => "single" 
								) 
						) 
				) 
		) );
		return $grid_field_arr;
	}
	function build_sipiax_list_for_customer() {
		$grid_field_arr = json_encode ( array (
				array (
						"Tech",
						"150",
						"tech",
						"",
						"",
						"" 
				),
				array (
						gettext ( "Type" ),
						"150",
						"type",
						"",
						"",
						"" 
				),
				array (
						gettext ( "User Name" ),
						"150",
						"username",
						"sweep",
						"sweeplist",
						"get_field_name" 
				),
				array (
						gettext ( "Password" ),
						"150",
						"secret",
						"",
						"",
						"" 
				),
				array (
						gettext ( "Context" ),
						"150",
						"context",
						"",
						"",
						"" 
				) 
		) );
		return $grid_field_arr;
	}
	function set_block_pattern_action_buttons($id) {
		$ret_url = '';
		$ret_url .= '<a href="/did/delete/' . $id . '/" class="icon delete_image" title="Delete" onClick="return get_alert_msg();">&nbsp;</a>';
		return $ret_url;
	}
	function build_animap_list() {
		$grid_field_arr = json_encode ( array (
				array (
						"Caller ID",
						"180",
						"number",
						"",
						"",
						"" 
				),
				array (
						gettext ( "status" ),
						"180",
						"status",
						"status",
						"animap",
						"get_status" 
				),
				array (
						gettext ( "Action" ),
						"130",
						"",
						"",
						"",
						array (
								"EDIT_ANIMAP" => array (
										"url" => "accounts/callingcards_animap_list_edit/",
										"mode" => "single" 
								),
								"DELETE_ANIMAP" => array (
										"url" => "accounts/callingcards_animap_list_remove/",
										"mode" => "single" 
								) 
						) 
				) 
		) );
		return $grid_field_arr;
	}
	function build_grid_buttons_destination() {
		$buttons_json = json_encode ( array () );
		return $buttons_json;
	}
}

?>
