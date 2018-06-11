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
class Freeswitch_form {
	function __construct() {
		$this->CI = & get_instance ();
	}
	function get_freeswith_form_fields($id = false) {
		$log_type = $this->CI->session->userdata ( "logintype" );
		if ($log_type == 0 || $log_type == 3 || $log_type == 1) {
			$sip_pro = null;
		} else {
			$sip_pro = array (
					'SIP Profile',
					'sip_profile_id',
					'SELECT',
					'',
					'trim|dropdown|xss_clean',
					'tOOL TIP',
					'Please Enter account number',
					'id',
					'name',
					'sip_profiles',
					'build_dropdown',
					'',
					'' 
			);
		}
		$val = $id > 0 ? 'sip_devices.username.' . $id : 'sip_devices.username';
		$uname_user = $this->CI->common->find_uniq_rendno ( '10', '', '' );
		$password = $this->CI->common->generate_password ();
		/* Edit functionality */
		$form ['forms'] = array (
				base_url () . 'freeswitch/fssipdevices_save/',
				array (
						"id" => "sipdevices_form",
						"name" => "sipdevices_form" 
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
								'id' => 'username1',
								'class' => "text field medium" 
						),
						'trim|required|is_unique[' . $val . ']|xss_clean',
						'tOOL TIP',
						'Please Enter account number',
						'<i style="cursor:pointer; color:#1BCB61 !important; font-size:14px;    padding-left:5px;    padding-top:8px;    float:left;" title="Reset Password" class="change_number  fa fa-refresh"></i>' 
				),
				array (
						gettext ( 'Password' ),
						'INPUT',
						array (
								'name' => 'fs_password',
								'size' => '20',
								'value' => $password,
								'id' => 'password1',
								'class' => "text field medium" 
						),
						'trim|required|xss_clean',
						'tOOL TIP',
						'Please Enter Password',
						'<i style="cursor:pointer; color:#1BCB61 !important; font-size:14px;    padding-left:5px;    padding-top:8px;    float:left;" title="Reset Password" class="change_pass fa fa-refresh"></i>' 
				),
				array (
						gettext ( 'Account' ),
						'accountcode',
						'SELECT',
						'',
						'trim|dropdown|xss_clean',
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
								"deleted" => "0" 
						) 
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
				),
				$sip_pro 
		);
		/**
		 * ****
		 * ASTPP 3.0
		 * Voicemail add edit
		 * *****
		 */
		
		$form [gettext ( 'Voicemail Options' )] = array (
				array (
						gettext ( 'Enable' ),
						'voicemail_enabled',
						'SELECT',
						'',
						'',
						'tOOL TIP',
						'Please Enter account number',
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
						'Please Enter account number',
						'',
						'',
						'',
						'custom_status_voicemail' 
				),
				
				array (
						gettext ( 'Local After Email' ),
						'vm_keep_local_after_email',
						'SELECT',
						'',
						'',
						'tOOL TIP',
						'Please Enter account number',
						'',
						'',
						'',
						'custom_status_voicemail' 
				),
				
				array (
						gettext ( 'Send all Message' ),
						'vm_send_all_message',
						'SELECT',
						'',
						'',
						'tOOL TIP',
						'Please Enter account number',
						'',
						'',
						'',
						'custom_status_voicemail' 
				) 
		)
		;
		/**
		 * ***********************
		 */
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
		// echo "<pre>";print_r($form);exit;
		return $form;
	}
	function get_freeswith_search_form() {
		$form ['forms'] = array (
				"",
				array (
						'id' => "freeswith_search" 
				) 
		);
		$form [gettext ( 'Search' )] = array (
				
				array (
						gettext ( 'SIP Profile' ),
						'sip_profile_id',
						'SELECT',
						'',
						'',
						'tOOL TIP',
						'Please Enter account number',
						'id',
						'name',
						'sip_profiles',
						'build_dropdown',
						'where_arr',
						'' 
				),
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
						gettext ( 'Gateway' ),
						'gateway_id',
						'SELECT',
						'',
						'',
						'tOOL TIP',
						'Please select gateway first',
						'id',
						'name',
						'gateways',
						'build_dropdown',
						'where_arr',
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
				'id' => "freeswith_search_btn",
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
	function get_sipdevices_search_form_user() {
		$form ['forms'] = array (
				"",
				array (
						'id' => "sipdevices_search" 
				) 
		);
		$form [gettext ( 'Search' )] = array (
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
						gettext ( 'SIP Profile' ),
						'sip_profile_id',
						'SELECT',
						'',
						'',
						'tOOL TIP',
						'Please Enter account number',
						'id',
						'name',
						'sip_profiles',
						'build_dropdown',
						'where_arr',
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
		)
		;
		$form ['button_search'] = array (
				'name' => 'action',
				'id' => "sipdevices_search_btn",
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
	function get_gateway_search_form() {
		$form ['forms'] = array (
				"",
				array (
						'id' => "freeswith_search" 
				) 
		);
		$form [gettext ( 'Search' )] = array (
				
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
				// array('Gateway Name', 'id', 'SELECT', '', '', 'tOOL TIP', 'Please select gateway first', 'id', 'name', 'gateways', 'build_dropdown','where_arr', ''),
				array (
						gettext ( 'SIP Profile' ),
						'sip_profile_id',
						'SELECT',
						'',
						'',
						'tOOL TIP',
						'Please Enter account number',
						'id',
						'name',
						'sip_profiles',
						'build_dropdown',
						'where_arr',
						'' 
				),
				// array('Username', 'INPUT', array('name' => 'username[username]', '', 'size' => '20', 'maxlength' => '15', 'class' => "text field"), '', 'tOOL TIP', '1', 'username[username-string]', '', '', '', 'search_string_type', ''),
				
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
				// array('Account', 'accountid', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'first_name,last_name,number', 'accounts', 'build_concat_dropdown', 'where_arr', array("reseller_id" => "0","type"=>"0", "deleted" => "0")),
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
				'id' => "fsgateway_search_btn",
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
	function get_sipprofile_search_form() {
		$form ['forms'] = array (
				"",
				array (
						'id' => "freeswitch_search" 
				) 
		);
		$form [gettext ( 'Search' )] = array (
				
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
						gettext ( 'SIP IP' ),
						'INPUT',
						array (
								'name' => 'sip_ip[sip_ip]',
								'value' => '',
								'size' => '20',
								'class' => "text field " 
						),
						'',
						'Tool tips info',
						'1',
						'sip_ip[sip_ip-string]',
						'',
						'',
						'',
						'search_string_type',
						'' 
				),
				array (
						gettext ( 'SIP Port' ),
						'INPUT',
						array (
								'name' => 'sip_port[sip_port]',
								'value' => '',
								'size' => '20',
								'class' => "text field " 
						),
						'',
						'Tool tips info',
						'1',
						'sip_port[sip_port-string]',
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
				'id' => "fssipprofile_search_btn",
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
	function get_sipdevice_search_form() {
		$form ['forms'] = array (
				"",
				array (
						'id' => "freeswith_search" 
				) 
		);
		$form [gettext ( 'Search' )] = array (
				
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
						gettext ( 'SIP Profile' ),
						'sip_profile_id',
						'SELECT',
						'',
						'',
						'tOOL TIP',
						'Please Enter account number',
						'id',
						'name',
						'sip_profiles',
						'build_dropdown',
						'where_arr',
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
								"type" => "GLOBAL",
								"deleted" => "0" 
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
				// array('Voicemail', 'vm-enabled', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', '', '', '', 'set_voicemail_status'),
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
				'id' => "fssipdevice_search_btn",
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
	 * ASTPP 3.0
	 * Changes in gried size
	 */
	function build_system_list_for_admin() {
		$grid_field_arr = json_encode ( array (
				array (
						"<input type='checkbox' name='chkAll' class='ace checkall'/><label class='lbl'></label>",
						"50",
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
						"100",
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
						"100",
						"password",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "SIP Profile" ),
						"100",
						"sip_profile_id",
						"name",
						"sip_profiles",
						"get_field_name",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Account" ),
						"150",
						"accountid",
						"first_name,last_name,number",
						"accounts",
						"build_concat_string",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Caller Name" ),
						"100",
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
						"100",
						"effective_caller_id_number",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Voicemail" ),
						"100",
						"voicemail_enabled",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Status" ),
						"100",
						"status",
						"status",
						"sip_devies",
						"get_status",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Created Date" ),
						"130",
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
						gettext ( "Action" ),
						"107",
						"",
						"",
						"",
						array (
								"EDIT" => array (
										"url" => "/freeswitch/fssipdevices_edit/",
										"mode" => "single",
										"layout" => "medium" 
								),
								"DELETE" => array (
										"url" => "/freeswitch/fssipdevices_delete/",
										"mode" => "single" 
								) 
						) 
				) 
		) );
		return $grid_field_arr;
	}
	/**
	 * ******************************
	 */
	function build_grid_buttons() {
		$buttons_json = json_encode ( array (
				array (
						gettext ( "Create" ),
						"btn btn-line-warning btn",
						"fa fa-plus-circle fa-lg",
						"button_action",
						"/freeswitch/fssipdevices_add/",
						"popup",
						"medium" 
				),
				array (
						gettext ( "Delete" ),
						"btn btn-line-danger",
						"fa fa-times-circle fa-lg",
						"button_action",
						"/freeswitch/fssipdevices_delete_multiple/" 
				) 
		) );
		return $buttons_json;
	}
	function fsdevices_build_grid_buttons($accountid) {
		$buttons_json = json_encode ( array (
				array (
						gettext ( "Create" ),
						"btn btn-line-warning btn",
						"fa fa-plus-circle fa-lg",
						"button_action",
						"/freeswitch/customer_fssipdevices_add/$accountid/",
						"popup",
						"medium" 
				),
				array (
						gettext ( "Delete" ),
						"btn btn-line-danger",
						"fa fa-times-circle fa-lg",
						"button_action",
						"/freeswitch/customer_fssipdevices_delete_multiple/" 
				) 
		) );
		return $buttons_json;
	}
	function get_gateway_form_fields() {
		$form ['forms'] = array (
				base_url () . 'freeswitch/fsgateway_save/',
				array (
						"id" => "gateway_form",
						"name" => "gateway_form" 
				) 
		);
		$form [gettext ( 'Basic Information' )] = array (
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
								'name' => 'name',
								'size' => '20',
								'class' => "text field medium" 
						),
						'trim|required|xss_clean',
						'tOOL TIP',
						'Please Enter Gateway Name' 
				),
				array (
						gettext ( 'SIP Profile' ),
						'sip_profile_id',
						'SELECT',
						'',
						'',
						'tOOL TIP',
						'',
						'id',
						'name',
						'sip_profiles',
						'build_dropdown',
						'',
						'' 
				),
				array (
						gettext ( 'Username' ),
						'INPUT',
						array (
								'name' => 'username',
								'size' => '20',
								'class' => "text field medium" 
						),
						'',
						'tOOL TIP',
						'Please Enter user name' 
				),
				array (
						gettext ( 'Password' ),
						'INPUT',
						array (
								'name' => 'password',
								'size' => '20',
								'class' => "text field medium" 
						),
						'',
						'tOOL TIP',
						'Please Enter Password' 
				),
				array (
						gettext ( 'Proxy' ),
						'INPUT',
						array (
								'name' => 'proxy',
								'size' => '20',
								'class' => "text field medium" 
						),
						'trim|required|xss_clean',
						'tOOL TIP',
						'' 
				),
				array (
						gettext ( 'Outbound-<br/>Proxy' ),
						'INPUT',
						array (
								'name' => 'outbound-proxy',
								'size' => '20',
								'class' => "text field medium" 
						),
						'trim|xss_clean',
						'tOOL TIP',
						'' 
				),
				array (
						gettext ( 'Register' ),
						array (
								'name' => 'register',
								'class' => 'add_settings' 
						),
						'SELECT',
						'',
						'',
						'tOOL TIP',
						'',
						'',
						'',
						'',
						'set_sip_config_option' 
				),
				array (
						gettext ( 'Caller-id-in-from' ),
						array (
								'name' => 'caller-id-in-from',
								'class' => 'add_settings' 
						),
						'SELECT',
						'',
						'',
						'tOOL TIP',
						'',
						'',
						'',
						'',
						'set_sip_config_option' 
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
		$form [gettext ( 'Optional Information' )] = array (
				array (
						gettext ( 'From- Domain' ),
						'INPUT',
						array (
								'name' => 'from-domain',
								'size' => '20',
								'class' => "text field medium" 
						),
						'',
						'tOOL TIP',
						'' 
				),
				array (
						gettext ( 'From User' ),
						'INPUT',
						array (
								'name' => 'from-user',
								'size' => '20',
								'class' => "text field medium" 
						),
						'',
						'tOOL TIP',
						'' 
				),
				array (
						gettext ( 'Realm' ),
						'INPUT',
						array (
								'name' => 'realm',
								'size' => '20',
								'class' => "text field medium" 
						),
						'',
						'tOOL TIP',
						'' 
				),
				array (
                                                gettext ( 'Extension-In-Contact' ),
                                                array (
                                                        'name' => 'extension-in-contact',
                                                        'class' => 'add_settings'
                                                ),
                                                'SELECT',
                                                '',
                                                '',
                                                'tOOL TIP',
                                                '',
                                                '',
                                                '',
                                                '',
                                                'set_option_default_false'
                                ),
				array (
						gettext ( 'Extension' ),
						'INPUT',
						array (
								'name' => 'extension',
								'size' => '20',
								'class' => "text field medium" 
						),
						'',
						'tOOL TIP',
						'' 
				),
				array (
						gettext ( 'Expire Seconds' ),
						'INPUT',
						array (
								'name' => 'expire-seconds',
								'size' => '20',
								'class' => "text field medium" 
						),
						'',
						'tOOL TIP',
						'' 
				),
				array (
						gettext ( 'Reg-<br/>Transport' ),
						'INPUT',
						array (
								'name' => 'register-transport',
								'size' => '20',
								'class' => "text field medium" 
						),
						'',
						'tOOL TIP',
						'' 
				),
				array (
						gettext ( 'Contact Params' ),
						'INPUT',
						array (
								'name' => 'contact-params',
								'size' => '20',
								'class' => "text field medium" 
						),
						'',
						'tOOL TIP',
						'' 
				),
				array (
						gettext ( 'Ping' ),
						'INPUT',
						array (
								'name' => 'ping',
								'size' => '20',
								'class' => "text field medium" 
						),
						'',
						'tOOL TIP',
						'' 
				),
				array (
						gettext ( 'Retry-<br/>Seconds' ),
						'INPUT',
						array (
								'name' => 'retry-seconds',
								'size' => '20',
								'class' => "text field medium" 
						),
						'',
						'tOOL TIP',
						'' 
				),
				array (
						gettext ( 'Register-<br/>Proxy' ),
						'INPUT',
						array (
								'name' => 'register-proxy',
								'size' => '20',
								'class' => "text field medium" 
						),
						'',
						'tOOL TIP',
						'' 
				),
				array (
						gettext ( 'Channel' ),
						'INPUT',
						array (
								'name' => 'channel',
								'size' => '20',
								'class' => "text field medium" 
						),
						'',
						'tOOL TIP',
						'' 
				),
				
				/**
				 * ASTPP 3.0
				 * put one variable with the name of dialplan variable
				 */
				// array('Dialplan Variable', 'TEXTAREA', array('name' => 'dialplan_variable', 'size' => '20','cols'=>'10','rows'=>'5', 'class' => "col_md-5 form-control", 'style'=>"width: 200px; height: 100px;font-family: Open sans,sans-serif !important;"), '', 'tOOL TIP', ''),
				array (
						'Dialplan Variable',
						'TEXTAREA',
						array (
								'name' => 'dialplan_variable',
								'size' => '0',
								'cols' => '0',
								'rows' => '5',
								'class' => "",
								'style' => "background: #fdfdfd;border-radius: 4px;padding: 4px 7px !important;border:1px solid #e4e4e4;color:#555;resize:both;overflow:hidden;width:100px !important; max-width:225px !important;height: 100px;font-family: Open sans,sans-serif !important; position: relative;" 
						),
						'',
						'tOOL TIP',
						'' 
				) 
		)/**
		 * ******************************************************************************************************
		 */
		;
		
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
	
	/*
	 * ASTPP 3.0
	 * changes in grid size
	 */
	function build_fsgateway_list_for_admin() {
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
				 * For Gateway Edit on Gateway name
				 * *
				 */
				array (
						gettext ( "Name" ),
						"140",
						"name",
						"",
						"",
						"",
						"EDITABLE",
						"true",
						"center" 
				),
				/**
				 * *******************************
				 */
				array (
						gettext ( "SIP Profile" ),
						"115",
						"sip_profile_id",
						"name",
						"sip_profiles",
						"get_field_name",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Username" ),
						"140",
						"username",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				// array("Password", "181", "password", "", "", ""),
				array (
						gettext ( "Proxy" ),
						"145",
						"proxy",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Register" ),
						"120",
						"register",
						"register",
						"register",
						"convert_to_ucfirst",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Caller-Id-In-Form" ),
						"130",
						"caller-id-in-from",
						"caller-id-in-from",
						"caller-id-in-from",
						"convert_to_ucfirst",
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
						"110",
						"status",
						"status",
						"gateways",
						"get_status",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Created Date" ),
						"100",
						"created_date",
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
				/**
				 * *****************************************************************
				 */
				/*
				 * ASTPP 3.0
				 *
				 * status show active or inactive
				 */
				
				/**
				 * ***************************************
				 */
				array (
						"Action",
						"106",
						"",
						"",
						"",
						array (
								"EDIT" => array (
										"url" => "/freeswitch/fsgateway_edit/",
										"mode" => "popup",
										"layout" => "medium" 
								),
								"DELETE" => array (
										"url" => "/freeswitch/fsgateway_delete/",
										"mode" => "single" 
								) 
						) 
				) 
		) );
		return $grid_field_arr;
	}
	/**
	 * ***************************************************
	 */
	function build_fdgateway_grid_buttons() {
		$buttons_json = json_encode ( array (
				array (
						gettext ( "Create" ),
						"btn btn-line-warning btn",
						"fa fa-plus-circle fa-lg",
						"button_action",
						"/freeswitch/fsgateway_add/",
						"popup",
						"medium" 
				),
				array (
						gettext ( "Delete" ),
						"btn btn-line-danger",
						"fa fa-times-circle fa-lg",
						"button_action",
						"/freeswitch/fsgateway_delete_multiple/" 
				) 
		) );
		return $buttons_json;
	}
	function get_sipprofile_form_fields() {
		$form ['forms'] = array (
				base_url () . 'freeswitch/fssipprofile_save/',
				array (
						"id" => "fssipprofile_form",
						"name" => "fssipprofile_form" 
				) 
		);
		$form [gettext ( 'Basic Information' )] = array (
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
						gettext ( 'Profile name' ),
						'INPUT',
						array (
								'name' => 'name',
								'size' => '20',
								'class' => "text field medium" 
						),
						'trim|required|xss_clean',
						'tOOL TIP',
						'Please Enter Name' 
				),
				array (
						gettext ( 'sip-ip' ),
						'INPUT',
						array (
								'name' => 'sip_ip',
								'size' => '20',
								'class' => "text field medium" 
						),
						'trim|required|xss_clean',
						'tOOL TIP',
						'Please Enter SIP IP Name' 
				),
				array (
						gettext ( 'sip-port' ),
						'INPUT',
						array (
								'name' => 'sip_port',
								'size' => '20',
								'value' => '5060',
								'class' => "text field medium" 
						),
						'trim|required|xss_clean',
						'tOOL TIP',
						'Please Enter SIP Port' 
				),
				array (
						gettext ( 'rtp-ip' ),
						'INPUT',
						array (
								'name' => 'rtp_ip',
								'size' => '20',
								'class' => "text field medium" 
						),
						'',
						'tOOL TIP',
						'' 
				),
				array (
						gettext ( 'Dial Plan' ),
						'INPUT',
						array (
								'name' => 'dialplan',
								'size' => '20',
								'value' => 'XML',
								'class' => "text field medium" 
						),
						'',
						'tOOL TIP',
						'' 
				),
				array (
						gettext ( 'user-agent-string' ),
						'INPUT',
						array (
								'name' => 'user-agent-string',
								'size' => '20',
								'value' => 'ASTPP',
								'class' => "text field medium" 
						),
						'',
						'tOOL TIP',
						'' 
				),
				array (
						gettext ( 'debug' ),
						'INPUT',
						array (
								'name' => 'debug',
								'size' => '20',
								'value' => '0',
								'class' => "text field medium" 
						),
						'',
						'tOOL TIP',
						'' 
				),
				array (
						gettext ( 'sip-trace' ),
						'sip-trace',
						'SELECT',
						'',
						'',
						'tOOL TIP',
						'',
						'',
						'',
						'',
						'set_sip_drp_option' 
				),
				array (
						gettext ( 'tls' ),
						'tls',
						'SELECT',
						'',
						'',
						'tOOL TIP',
						'',
						'',
						'',
						'',
						'set_sip_config_options' 
				),
				array (
						gettext ( 'inbound-reg-force-matching-username' ),
						'inbound-reg-force-matching-username',
						'SELECT',
						'',
						'',
						'tOOL TIP',
						'',
						'',
						'',
						'',
						'set_sip_config_option' 
				),
				array (
						gettext ( 'disable-transcoding' ),
						'disable-transcoding',
						'SELECT',
						'',
						'',
						'tOOL TIP',
						'',
						'',
						'',
						'',
						'set_sip_config_option' 
				),
				array (
						gettext ( 'all-reg-options-ping' ),
						'all-reg-options-ping',
						'SELECT',
						'',
						'',
						'tOOL TIP',
						'',
						'',
						'',
						'',
						'set_sip_config_option' 
				),
				array (
						gettext ( 'unregister-on-options-fail' ),
						'unregister-on-options-fail',
						'SELECT',
						'',
						'',
						'tOOL TIP',
						'',
						'',
						'',
						'',
						'set_sip_config_option' 
				),
				array (
						gettext ( 'log-auth-failures' ),
						'log-auth-failures',
						'SELECT',
						'',
						'',
						'tOOL TIP',
						'',
						'',
						'',
						'',
						'set_sip_config_option' 
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
		
		$form [gettext ( 'Others Information' )] = array (
				array (
						gettext ( 'inbound-bypass-media' ),
						'inbound-bypass-media',
						'SELECT',
						'',
						'',
						'tOOL TIP',
						'',
						'',
						'',
						'',
						'set_sip_config_options' 
				),
				array (
						gettext ( 'inbound-proxy-media' ),
						'inbound-proxy-media',
						'SELECT',
						'',
						'',
						'tOOL TIP',
						'',
						'',
						'',
						'',
						'set_sip_config_options' 
				),
				array (
						gettext ( 'disable-transfer' ),
						'disable-transfer',
						'SELECT',
						'',
						'',
						'tOOL TIP',
						'',
						'',
						'',
						'',
						'set_sip_config_option' 
				),
				array (
						gettext ( 'enable-100rel' ),
						'enable-100rel',
						'SELECT',
						'',
						'',
						'tOOL TIP',
						'',
						'',
						'',
						'',
						'set_sip_config_options' 
				),
				array (
						gettext ( 'rtp-timeout-sec' ),
						'INPUT',
						array (
								'name' => 'rtp-timeout-sec',
								'size' => '20',
								'value' => '60',
								'class' => "text field medium" 
						),
						'',
						'tOOL TIP',
						'' 
				),
				array (
						gettext ( 'dtmf-duration' ),
						'INPUT',
						array (
								'name' => 'dtmf-duration',
								'size' => '20',
								'value' => '2000',
								'class' => "text field medium" 
						),
						'',
						'tOOL TIP',
						'' 
				),
				array (
						gettext ( 'manual-redirect' ),
						'manual-redirect',
						'SELECT',
						'',
						'',
						'tOOL TIP',
						'',
						'',
						'',
						'',
						'set_sip_config_options' 
				),
				array (
						gettext ( 'aggressive-nat-detection' ),
						'aggressive-nat-detection',
						'SELECT',
						'',
						'',
						'tOOL TIP',
						'',
						'',
						'',
						'',
						'set_sip_config_option' 
				),
				array (
						gettext ( 'enable-Timer' ),
						'enable-timer',
						'SELECT',
						'',
						'',
						'tOOL TIP',
						'',
						'',
						'',
						'',
						'set_sip_config_options' 
				),
				array (
						gettext ( 'minimum-session-expires' ),
						'INPUT',
						array (
								'name' => 'minimum-session-expires',
								'value' => '120',
								'size' => '20',
								'class' => "text field medium" 
						),
						'',
						'tOOL TIP',
						'' 
				),
				array (
						gettext ( 'session-timeout' ),
						'INPUT',
						array (
								'name' => 'session-timeout-pt',
								'value' => '1800',
								'size' => '20',
								'class' => "text field medium" 
						),
						'',
						'tOOL TIP',
						'' 
				),
				array (
						gettext ( 'auth-calls' ),
						'auth-calls',
						'SELECT',
						'',
						'',
						'tOOL TIP',
						'',
						'',
						'',
						'',
						'set_sip_config_option' 
				),
				array (
						gettext ( 'apply-inbound-acl' ),
						'INPUT',
						array (
								'name' => 'apply-inbound-acl',
								'value' => 'default',
								'size' => '20',
								'class' => "text field medium" 
						),
						'',
						'tOOL TIP',
						'' 
				),
				array (
						gettext ( 'inbound-codec-prefs' ),
						'INPUT',
						array (
								'name' => 'inbound-codec-prefs',
								'size' => '25',
								'class' => "text field medium" 
						),
						'',
						'tOOL TIP',
						'' 
				),
				array (
						gettext ( 'outbound-codec-prefs' ),
						'INPUT',
						array (
								'name' => 'outbound-codec-prefs',
								'size' => '25',
								'class' => "text field medium" 
						),
						'',
						'tOOL TIP',
						'' 
				),
				array (
						gettext ( 'inbound-late-negotiation' ),
						'inbound-late-negotiation',
						'SELECT',
						'',
						'',
						'tOOL TIP',
						'',
						'',
						'',
						'',
						'set_sip_config_options' 
				),
				array (
						gettext ( 'inbound-codec-negotiation' ),
						'INPUT',
						array (
								'name' => 'inbound-codec-negotiation',
								'size' => '25',
								'class' => "text field medium" 
						),
						'',
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
				'content' => gettext ( 'Save' ),
				'value' => 'save',
				'type' => 'button',
				'id' => 'submit',
				'class' => 'btn btn-line-parrot' 
		);
		
		return $form;
	}
	function build_fssipprofile_list_for_admin() {
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
				 * For Sip Profile edit on Profile name
				 * *
				 */
				array (
						gettext ( "Name" ),
						"190",
						"name",
						"",
						"",
						"",
						"EDITABLE",
						"true",
						"center" 
				),
				/**
				 * *********************************
				 */
				array (
						gettext ( "SIP IP" ),
						"205",
						"sip_ip",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "SIP Port" ),
						"200",
						"sip_port",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
		   /*
            ASTPP  3.0 
            status show active or inactive
            */
			array (
						gettext ( "Status" ),
						"160",
						"status",
						"status",
						"sip_profiles",
						"get_status",
						"",
						"true",
						"center" 
				),
				/**
				 * ****************************************************
				 */
				array (
						gettext ( "Profile Action" ),
						"282",
						"",
						"",
						"",
						array (
								"START" => array (
										"url" => "/freeswitch/fssipprofile_action/start/",
										"mode" => "single" 
								),
								"STOP" => array (
										"url" => "/freeswitch/fssipprofile_action/stop/",
										"mode" => "single" 
								),
								"RELOAD" => array (
										"url" => "/freeswitch/fssipprofile_action/reload/",
										"mode" => "single" 
								),
								"RESCAN" => array (
										"url" => "/freeswitch/fssipprofile_action/rescan/",
										"mode" => "single" 
								) 
						) 
				),
				array (
						gettext ( "Action" ),
						"202",
						"",
						"",
						"",
						array (
								"EDIT" => array (
										"url" => "/freeswitch/fssipprofile_edit/",
										"mode" => "" 
								),
								"DELETE" => array (
										"url" => "/freeswitch/fssipprofile_delete/",
										"mode" => "single" 
								) 
						) 
				) 
		) );
		return $grid_field_arr;
	}
	function build_fssipprofile_grid_buttons() {
		$buttons_json = json_encode ( array (
				array (
						gettext ( "Create" ),
						"btn btn-line-warning btn",
						"fa fa-plus-circle fa-lg",
						"button_action",
						"/freeswitch/fssipprofile_add/",
						"" 
				),
				array (
						gettext ( "Delete" ),
						"btn btn-line-danger",
						"fa fa-times-circle fa-lg",
						"button_action",
						"/freeswitch/fssipprofile_delete_multiple/" 
				) 
		) );
		return $buttons_json;
	}
	function build_fssipprofile_params_list_for_admin() {
		$grid_field_arr = json_encode ( array (
				array (
						gettext ( "Name" ),
						"450",
						"name",
						"",
						"",
						"" 
				),
				array (
						gettext ( "Value" ),
						"414",
						"sip_ip",
						"",
						"",
						"" 
				),
				// array("SIP Port", "250", "sip_port", "", "", ""),
				array (
						gettext ( "Action" ),
						"400",
						"",
						"",
						"",
						array (
								"DELETE" => array (
										"url" => "/freeswitch/fssipprofile_delete/",
										"mode" => "single" 
								) 
						) 
				) 
		) );
		return $grid_field_arr;
	}
	/*
	 * ASTPP 3.0
	 * changes in gried size
	 */
	function build_fsserver_list() {
		// array(display name, width, db_field_parent_table,feidname, db_field_child_table,function name);
		$grid_field_arr = json_encode ( array (
				array (
						gettext ( "Host" ),
						"200",
						"freeswitch_host",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Password" ),
						"200",
						"freeswitch_password",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Port" ),
						"200",
						"freeswitch_port",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
			/*
            ASTPP  3.0 .3 creation field show in grid
            */
			array (
						gettext ( "Status" ),
						"145",
						"status",
						"status",
						"freeswich_servers",
						"get_status",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Created Date" ),
						"170",
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
				/**
				 * *****************************************************************
				 */
				
				array (
						gettext ( "Action" ),
						"182",
						"",
						"",
						"",
						array (
								"EDIT" => array (
										"url" => "/freeswitch/fsserver_edit/",
										"mode" => "popup" 
								),
								"DELETE" => array (
										"url" => "/freeswitch/fsserver_delete/",
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
	function build_fsserver_grid_buttons() {
		$buttons_json = json_encode ( array (
				array (
						gettext ( "Create" ),
						"btn btn-line-warning btn",
						"fa fa-plus-circle fa-lg",
						"button_action",
						"/freeswitch/fsserver_add/",
						"popup" 
				) 
		)
		// array("Delete", "btn btn-line-danger","fa fa-times-circle fa-lg", "button_action", "/freeswitch/fssipdevices_delete_multiple/")
		 );
		return $buttons_json;
	}
	function get_form_fsserver_fields() {
		$form ['forms'] = array (
				base_url () . '/freeswitch/fsserver_save/',
				array (
						"id" => "fsserver_form",
						"name" => "fsserver_form" 
				) 
		);
		$form [gettext ( 'Freeswitch Server Information' )] = array (
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
						gettext ( 'Host' ),
						'INPUT',
						array (
								'name' => 'freeswitch_host',
								'size' => '20',
								'class' => "text field medium" 
						),
						'trim|required|valid_ip',
						'tOOL TIP',
						'Please Enter account number' 
				),
				array (
						gettext ( 'Password' ),
						'INPUT',
						array (
								'name' => 'freeswitch_password',
								'size' => '20',
								'class' => "text field medium" 
						),
						'',
						'tOOL TIP',
						'Please Enter account number' 
				),
				array (
						gettext ( 'Port' ),
						'INPUT',
						array (
								'name' => 'freeswitch_port',
								'size' => '20',
								'class' => "text field medium" 
						),
						'trim|required|numeric',
						'tOOL TIP',
						'Please Enter account number' 
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
	function get_search_fsserver_form() {
		$form ['forms'] = array (
				"",
				array (
						'id' => "fsserver_search" 
				) 
		);
		$form [gettext ( 'Search' )] = array (
				array (
						gettext ( 'Host' ),
						'INPUT',
						array (
								'name' => 'freeswitch_host[freeswitch_host]',
								'',
								'id' => 'first_name',
								'size' => '15',
								'class' => "text field " 
						),
						'',
						'tOOL TIP',
						'1',
						'freeswitch_host[freeswitch_host-string]',
						'',
						'',
						'',
						'search_string_type',
						'' 
				),
				array (
						gettext ( 'Port' ),
						'INPUT',
						array (
								'name' => 'freeswitch_port[freeswitch_port]',
								'value' => '',
								'size' => '20',
								'class' => "text field " 
						),
						'',
						'Tool tips info',
						'1',
						'freeswitch_port[freeswitch_port-string]',
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
						'Please Select Status',
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
				'id' => "fsserver_search_btn",
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
	 * ASTPP 3.0
	 * CHanges in grid size
	 */
	function build_devices_list_for_customer() {
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
						"100",
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
						"100",
						"password",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "SIP Profile" ),
						"90",
						"sip_profile_id",
						"name",
						"sip_profiles",
						"get_field_name",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Caller Name" ),
						"100",
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
						"100",
						"effective_caller_id_number",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Voicemail" ),
						"100",
						"voicemail_enabled",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				// array("Call Waiting", "105", "call_waiting", "", "", ""),
				array (
						gettext ( "Status" ),
						"100",
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
						"100",
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
						"110",
						"",
						"",
						"",
						array (
								"EDIT" => array (
										"url" => "/accounts/fssipdevices_action/edit/",
										"mode" => "single" 
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
	
	/**
	 * ****************8
	 */
	function fsdevice_form_fields_for_customer($accountid, $id = false) {
		
		// $val = $id > 0 ? 'sip_devices.username.' . $id : 'sip_devices.username';
		$val = $id > 0 ? 'sip_devices.username.' . $id : 'sip_devices.username';
		$uname_user = $this->CI->common->find_uniq_rendno ( '10', '', '' );
		$password = $this->CI->common->generate_password ();
		if ($this->CI->session->userdata ( "logintype" ) == '0' || $this->CI->session->userdata ( "logintype" ) == '3') {
			$link = base_url () . 'freeswitch/user_fssipdevices_save/true';
			$form ['forms'] = array (
					$link,
					array (
							"id" => "sipdevices_form",
							"name" => "sipdevices_form" 
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
							'',
							'HIDDEN',
							array (
									'name' => 'accountcode',
									'value' => $accountid 
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
							'<i style="cursor:pointer;color:#1BCB61 !important;font-size:14px; padding-left:5px; padding-top:8px; float:left; " title="Reset Password" class="change_number fa fa-refresh"></i>' 
					),
					array (
							gettext ( 'Password' ),
							'INPUT',
							array (
									'name' => 'fs_password',
									'size' => '20',
									'id' => 'password1',
									'value' => $password,
									'class' => "text field medium" 
							),
							'trim|required|xss_clean',
							'tOOL TIP',
							'Please Enter Password',
							'<i style="cursor:pointer;color:#1BCB61 !important;  font-size:14px;    padding-left:5px;    padding-top:8px;    float:left;" title="Reset Password" class="change_pass fa fa-refresh"></i>' 
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
					// array('Call Waiting', 'call_waiting', 'SELECT', '', '', 'tOOL TIP', 'Please Select Status', '', '', '', 'set_call_waiting'),
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
			/**
			 * ****
			 * ASTPP 3.0
			 * Voicemail add edit
			 * *****
			 */
			$form [gettext ( 'Voicemail Options' )] = array (
					
					array (
							gettext ( 'Enable' ),
							'voicemail_enabled',
							'SELECT',
							'',
							'',
							'tOOL TIP',
							'Please Enter account number',
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
							'Please Enter account number',
							'',
							'',
							'',
							'custom_status_voicemail' 
					),
					array (
							gettext ( 'Local After Email' ),
							'vm_keep_local_after_email',
							'SELECT',
							'',
							'',
							'tOOL TIP',
							'Please Enter account number',
							'',
							'',
							'',
							'custom_status_voicemail' 
					),
					
					array (
							gettext ( 'Send all Message' ),
							'vm_send_all_message',
							'CHECKBOX',
							array (
									'name' => 'vm_send_all_message',
									'value' => 'on',
									'checked' => false 
							),
							'',
							'tOOL TIP',
							'Please Select Status',
							'custom_status_voicemail',
							'',
							'',
							'' 
					) 
			)
			;
		/**
		 * ****************
		 */
		} else {
			if ($accountid) {
				$account_Arr = null;
			} else {
				$account_Arr = array (
						'Account',
						'accountcode',
						'SELECT',
						'',
						'trim|dropdown|xss_clean',
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
				);
			}
			if ($this->CI->session->userdata ( "logintype" ) == '1') {
				$sip_pro = null;
				$link = base_url () . 'freeswitch/customer_fssipdevices_save/true';
			} else {
				$sip_pro = array (
						'SIP Profile',
						'sip_profile_id',
						'SELECT',
						'',
						'trim|dropdown|xss_clean',
						'tOOL TIP',
						'Please Enter account number',
						'id',
						'name',
						'sip_profiles',
						'build_dropdown',
						'',
						'' 
				);
				$link = base_url () . 'freeswitch/fssipdevices_save/true';
			}
			$form ['forms'] = array (
					$link,
					array (
							"id" => "sipdevices_form",
							"name" => "sipdevices_form" 
					) 
			);
			/* Add sipdevice */
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
							'',
							'HIDDEN',
							array (
									'name' => 'accountcode',
									'value' => $accountid 
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
									'id' => 'username1',
									'class' => "text field medium" 
							),
							'trim|required|is_unique[' . $val . ']|xss_clean',
							'tOOL TIP',
							'Please Enter account number',
							'<i style="cursor:pointer; color:#1BCB61; font-size:14px; padding-left:5px;  padding-top:8px; float:left;" title="Reset Password" class="change_number fa fa-refresh"></i>' 
					),
					array (
							gettext ( 'Password' ),
							'INPUT',
							array (
									'name' => 'fs_password',
									'size' => '20',
									'id' => 'password1',
									'value' => $password,
									'class' => "text field medium" 
							),
							'trim|required|xss_clean',
							'tOOL TIP',
							'Please Enter Password',
							'<i style="cursor:pointer; color:#1BCB61; font-size:14px; padding-left:5px;padding-top:8px; float:left;" title="Reset Password" class="change_pass fa fa-refresh"></i>' 
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
					$account_Arr,
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
					),
					$sip_pro 
			);
			/**
			 * ****
			 * ASTPP 3.0
			 * Voicemail add edit
			 * *****
			 */
			$form [gettext ( 'Voicemail Options' )] = array (
					array (
							gettext ( 'Enable' ),
							'voicemail_enabled',
							'SELECT',
							'',
							'',
							'tOOL TIP',
							'Please Enter account number',
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
							'PleaseEnter account number' 
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
							'Please Enter account number',
							'',
							'',
							'',
							'custom_status_voicemail' 
					),
					// array('Local After Email', 'vm_keep_local_after_email', 'CHECKBOX', array('name' => 'vm_keep_local_after_email', 'value' => 'on', 'checked' => false), '', 'tOOL TIP', 'Please Select Status', 'custom_status_true', '', '', ''),
					array (
							gettext ( 'Local After Email' ),
							'vm_keep_local_after_email',
							'SELECT',
							'',
							'',
							'tOOL TIP',
							'Please Enter account number',
							'',
							'',
							'',
							'custom_status_voicemail' 
					),
					// array('Send all Message', 'vm_send_all_message', 'CHECKBOX', array('name' => 'vm_send_all_message', 'value' => 'on', 'checked' => false), '', 'tOOL TIP', 'Please Select Status', 'custom_status_true', '', '', ''),
					
					array (
							gettext ( 'Send all Message' ),
							'vm_send_all_message',
							'SELECT',
							'',
							'',
							'tOOL TIP',
							'Please Enter account number',
							'',
							'',
							'',
							'custom_status_voicemail' 
					) 
			)
			;
		/**
		 * *****************
		 */
		}
		
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
}

?>
