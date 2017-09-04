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
class Opensips_form {
	function __construct() {
		$this->CI = & get_instance ();
	}
	function get_opensips_form_fields($id = false) {
		$accountinfo = $this->CI->session->userdata ( "accountinfo" );
		
		$uname_user = $this->CI->common->find_uniq_rendno ( '10', '', '' );
		$password = $this->CI->common->generate_password ();
		$val = $id > 0 ? 'subscriber.username.' . $id : 'subscriber.username';
		
		// echo '<pre>'; print_r($val); exit;
		$loginid = $this->CI->session->userdata ( 'logintype' ) == 2 ? 0 : $accountinfo ['id'];
		$form ['forms'] = array (
				base_url () . 'opensips/opensips_save/',
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
						gettext ( 'Username' ),
						'INPUT',
						array (
								'name' => 'username',
								'size' => '30',
								'value' => $uname_user,
								'id' => 'username',
								'class' => "text field medium" 
						),
						'trim|required|xss_clean',
						'tOOL TIP',
						'Please Enter account number',
						'<i style="cursor:pointer; font-size: 17px; padding-left:10px; padding-top:6px;" title="Reset Password" class="change_number fa fa-refresh"></i>' 
				),
				array (
						gettext ( 'Password' ),
						'INPUT',
						array (
								'name' => 'password',
								'size' => '30',
								'value' => $password,
								'id' => 'password',
								'class' => "text field medium" 
						),
						'trim|required|xss_clean',
						'tOOL TIP',
						'Please Enter Password',
						'<i style="cursor:pointer; font-size: 17px; padding-left:10px; padding-top:6px;" title="Reset Password" class="change_pass fa fa-refresh"></i>' 
				),
				array (
						gettext ( 'Account' ),
						'accountcode',
						'SELECT',
						'',
						'',
						'tOOL TIP',
						'Please Enter account number',
						'number',
						'number',
						'accounts',
						'build_dropdown',
						'where_arr',
						array (
								"reseller_id" => $loginid,
								"type" => "GLOBAL",
								"deleted" => "0" 
						) 
				),
				array (
						gettext ( 'Domain' ),
						'INPUT',
						array (
								'name' => 'domain',
								'size' => '20',
								'class' => "text field medium" 
						),
						'',
						'tOOL TIP',
						'' 
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
		$form ['button_save'] = array (
				'name' => 'action',
				'content' => gettext ( 'Save' ),
				'value' => 'save',
				'type' => 'button',
				'id' => 'submit',
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
		// echo '<pre>'; print_r($form); exit;
		return $form;
	}
	function get_dispatcher_form_fields() {
		$form ['forms'] = array (
				base_url () . 'opensips/dispatcher_save/',
				array (
						"id" => "opensips_dispatcher_form",
						"name" => "opensips_dispatcher_form" 
				) 
		);
		$form ['Dispatcher Information'] = array (
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
						gettext ( 'Setid' ),
						'INPUT',
						array (
								'name' => 'setid',
								'size' => '20',
								'class' => "text field medium" 
						),
						'',
						'tOOL TIP',
						'' 
				),
				array (
						gettext ( 'Destination' ),
						'INPUT',
						array (
								'name' => 'destination',
								'size' => '20',
								'class' => "text field medium" 
						),
						'trim|required',
						'tOOL TIP',
						'' 
				),
				array (
						gettext ( 'Weight' ),
						'INPUT',
						array (
								'name' => 'weight',
								'size' => '20',
								'class' => "text field medium" 
						),
						'',
						'tOOL TIP',
						'' 
				),
				array (
						gettext ( 'Attrs' ),
						'INPUT',
						array (
								'name' => 'attrs',
								'size' => '20',
								'class' => "text field medium" 
						),
						'',
						'tOOL TIP',
						'' 
				),
				array (
						gettext ( 'Description' ),
						'INPUT',
						array (
								'name' => 'description',
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
				'type' => 'button',
				'id' => 'submit',
				'class' => 'btn btn-line-parrot' 
		);
		$form ['button_cancel'] = array (
				'name' => 'action',
				'content' => gettext ( 'Close' ),
				'value' => 'cancel',
				'type' => 'button',
				'class' => 'btn btn-line-sky margin-x-10',
				'onclick' => 'return redirect_page(\'/opensips/dispatcher_list/\')' 
		);
		return $form;
	}
	function get_search_dispatcher_form() {
		$form ['forms'] = array (
				"",
				array (
						'id' => "dispatcher_search" 
				) 
		);
		$form ['Search'] = array (
				array (
						gettext ( 'Description' ),
						'INPUT',
						array (
								'name' => 'description[description]',
								'',
								'size' => '20',
								'class' => "text field " 
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
				'id' => "opensipsdispatcher_search_btn",
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
	function get_search_opensips_form() {
		$form ['forms'] = array (
				"",
				array (
						'id' => "opensips_list_search" 
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
	function build_opensips_list() {
		// array(display name, width, db_field_parent_table,feidname, db_field_child_table,function name);
		$grid_field_arr = json_encode ( array (
				array (
						gettext ( "Username" ),
						"150",
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
						"150",
						"password",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Account" ),
						"150",
						"accountcode",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Domain" ),
						"317",
						"domain",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Caller Name" ),
						"200",
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
						"200",
						"effective_caller_id_number",
						"",
						"",
						"",
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
										"url" => "/opensips/opensips_edit/",
										"mode" => "popup" 
								),
								"DELETE" => array (
										"url" => "/opensips/opensips_remove/",
										"mode" => "single" 
								) 
						) 
				) 
		) );
		return $grid_field_arr;
	}
	function build_opensipsdispatcher_list() {
		// array(display name, width, db_field_parent_table,feidname, db_field_child_table,function name);
		$grid_field_arr = json_encode ( array (
				array (
						gettext ( "Set Id" ),
						"160",
						"setid",
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
						"destination",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Weight" ),
						"190",
						"weight",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Attrs" ),
						"180",
						"attrs",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Description" ),
						"190",
						"description",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Action" ),
						"170",
						"",
						"",
						"",
						array (
								"EDIT" => array (
										"url" => "/opensips/dispatcher_edit/",
										"mode" => "popup" 
								),
								"DELETE" => array (
										"url" => "/opensips/dispatcher_remove/",
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
						"Create",
						"btn btn-line-warning btn",
						"fa fa-plus-circle fa-lg",
						"button_action",
						"/opensips/opensips_add/",
						'popup' 
				) 
		)
		// array("Refresh","reload","/accounts/clearsearchfilter/")
		 );
		return $buttons_json;
	}
	function build_grid_dispatcherbuttons() {
		$buttons_json = json_encode ( array (
				array (
						"Create",
						"btn btn-line-warning btn",
						"fa fa-plus-circle fa-lg",
						"button_action",
						"/opensips/dispatcher_add/",
						"popup" 
				) 
		)
		// array("Refresh","reload","/accounts/clearsearchfilter/")
		 );
		return $buttons_json;
	}
	function get_opensips_form_fields_for_customer($accountid, $id = false) {
		$val = $id > 0 ? 'subscriber.username.' . $id : 'subscriber.username';
		$uname_user = $this->CI->common->find_uniq_rendno ( '10', '', '' );
		$password = $this->CI->common->generate_password ();
		$accountinfo = $this->CI->session->userdata ( 'accountinfo' );
		$link = ($accountinfo ['type'] == 1 || $accountinfo ['type'] == 3) ? base_url () . 'opensips/user_opensips_save/true/' : base_url () . 'opensips/customer_opensips_save/true/';
		$form ['forms'] = array (
				$link,
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
								'value' => $this->CI->common->get_field_name ( 'number', 'accounts', array (
										'id' => $accountid 
								) ) 
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
						gettext ( 'Password' ),
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
						gettext ( 'Domain' ),
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
		$form ['button_save'] = array (
				'name' => 'action',
				'content' => gettext ( 'Save' ),
				'value' => 'save',
				'type' => 'button',
				'id' => 'submit',
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
	function user_opensips() {
		// array(display name, width, db_field_parent_table,feidname, db_field_child_table,function name);
		$grid_field_arr = json_encode ( array (
				array (
						gettext ( "Username" ),
						"130",
						"username",
						"",
						"",
						"" 
				),
				array (
						gettext ( "Password" ),
						"130",
						"password",
						"",
						"",
						"" 
				),
				array (
						gettext ( "Domain" ),
						"130",
						"domain",
						"",
						"",
						"" 
				),
				array (
						gettext ( "Action" ),
						"120",
						"",
						"",
						"",
						array (
								"EDIT" => array (
										"url" => 'user/user_opensips_action/edit/',
										"mode" => "popup" 
								),
								"DELETE" => array (
										"url" => 'user/user_opensips_action/delete/',
										"mode" => "popup" 
								) 
						) 
				) 
		) );
		return $grid_field_arr;
	}
	function opensips_customer_build_grid_buttons($accountid) {
		$buttons_json = json_encode ( array (
				array (
						"Add Devices",
						"btn btn-line-warning btn",
						"fa fa-plus-circle fa-lg",
						"button_action",
						"/opensips/customer_opensips_add/$accountid/",
						"popup" 
				) 
		)
		// array("Refresh", "reload", "/accounts/clearsearchfilter/")
		 );
		return $buttons_json;
	}
	function opensips_customer_build_opensips_list($accountid) {
		// echo $accountid;
		// array(display name, width, db_field_parent_table,feidname, db_field_child_table,function name);
		$grid_field_arr = json_encode ( array (
				array (
						gettext ( "Username" ),
						"200",
						"username",
						"",
						"",
						"" 
				),
				array (
						gettext ( "Password" ),
						"200",
						"password",
						"",
						"",
						"" 
				),
				array (
						gettext ( "Domain" ),
						"200",
						"domain",
						"",
						"",
						"" 
				),
				array (
						gettext ( "Caller Name" ),
						"150",
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
						"150",
						"effective_caller_id_number",
						"",
						"",
						"",
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
										"url" => 'accounts/customer_opensips_action/edit/' . $accountid . '/',
										"mode" => "popup" 
								),
								"DELETE" => array (
										"url" => 'accounts/customer_opensips_action/delete/' . $accountid . "/",
										"mode" => "popup" 
								) 
						) 
				) 
		) );
		
		return $grid_field_arr;
	}
}

?>
