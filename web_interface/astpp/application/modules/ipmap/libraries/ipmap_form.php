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
class Ipmap_form {
	function __construct($library_name = '') {
		$this->CI = & get_instance ();
	}
	
	/**
	 * *******************
	 * ASTPP 3.0
	 * in customer login show ipmap (ACL) module put $account_type in function params
	 * ***************************************
	 */
	function get_ipmap_form_fields($account_type = '') {
		/**
		 * ************************************************
		 */
		/**
		 * *******************
		 * ASTPP 3.0
		 * in customer login show ipmap (ACL) module
		 * ***************************************
		 */
		$logintype = $this->CI->session->userdata ( 'logintype' );
		if ($account_type == '0' || $logintype == '0') {
			$account = null;
			$form ['forms'] = array (
					base_url () . 'user/user_ipmap_save/',
					array (
							'id' => 'ipmap_form',
							'method' => 'POST',
							'name' => 'ipmap_form' 
					) 
			);
		} else {
			$account = array (
					'Account',
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
							"type" => "0,3",
							"deleted" => "0",
							"status" => "0" 
					) 
			);
			$form ['forms'] = array (
					base_url () . 'ipmap/ipmap_save/',
					array (
							'id' => 'ipmap_form',
							'method' => 'POST',
							'name' => 'ipmap_form' 
					) 
			);
		}
		/**
		 * ********************************************************************************************
		 */
		$form ['IP map'] = array (
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
				/**
				 * *******************
				 * ASTPP 3.0
				 * in customer login show ipmap (ACL) module
				 * ***************************************
				 */
				$account,
				/**
				 * *******************************************************************************************
				 */
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
						'Please Enter account number' 
				),
		   /*ASTPP  3.0 
   	    in customer login show ipmap (ACL) module
   	    *****************************************/
			array (
						gettext ( 'IP' ),
						'INPUT',
						array (
								'name' => 'ip',
								'size' => '20',
								'class' => "text field medium" 
						),
						'trim|required|valid_ip',
						'tOOL TIP',
						'Please Enter proper ip' 
				),
				/**
				 * *************************************************************************************************
				 */
				array (
						gettext ( 'Prefix' ),
						'INPUT',
						array (
								'name' => 'prefix',
								'size' => '20',
								'class' => "text field medium" 
						),
						'trim|xss_clean',
						'tOOL TIP',
						'Please Enter prefix number' 
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
	function get_ipmap_search_form($account_data = '') {
		$accountinfo = $this->CI->session->userdata ( 'accountinfo' );
		$reseller_id = $accountinfo ['type'] == 1 ? $accountinfo ['id'] : 0;
		/**
		 * *******************
		 * ASTPP 3.0
		 * in customer login show ipmap (ACL) module
		 * ***************************************
		 */
		if ($account_data == '0') {
			$account = null;
		} else {
			$account = array (
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
							"reseller_id" => $reseller_id,
							"type" => "0",
							"deleted" => "0" 
					) 
			);
		}
		/**
		 * ************************************************************************************
		 */
		$form ['forms'] = array (
				"",
				array (
						'id' => "ipmap_search" 
				) 
		);
		$form ['Search'] = array (
				$account,
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
				/**
				 * *******************
				 * ASTPP 3.0
				 * in customer login show ipmap (ACL) module
				 * ***************************************
				 */
				
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
				/**
				 * ************************************************************************************
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
				'id' => "ipmap_search_btn",
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
	
	/**
	 * *****
	 * ASTPP 3.0
	 * in customer Add/Edit show Ipmap (ACL) module.
	 * *****
	 */
	function build_ipmap_list_for_admin($account_data = '') {
		if ($account_data == '0') {
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
							"100",
							"name",
							"",
							"",
							"" 
					),
					array (
							gettext ( "IP" ),
							"240",
							"ip",
							"",
							"",
							"" 
					),
					array (
							gettext ( "Prefix" ),
							"240",
							"prefix",
							"",
							"",
							"" 
					),
			 /*
            ASTPP  3.0  creation field show in grid
            */
			array (
							gettext ( "Status" ),
							"180",
							"status",
							"status",
							"ip_map",
							"get_status",
							"",
							"true",
							"center" 
					),
					array (
							gettext ( "Created Date" ),
							"150",
							"creation_date",
							"creation_date",
							"creation_date",
							"convert_GMT_to" 
					),
					array (
							gettext ( "Modified Date Date" ),
							"170",
							"last_modified_date",
							"last_modified_date",
							"last_modified_date",
							"convert_GMT_to" 
					),
					
					/**
					 * *****************************************************************
					 */
					array (
							"Action",
							"205",
							"",
							"",
							"",
							array (
									"EDIT" => array (
											"url" => "ipmap/ipmap_edit/",
											"mode" => "popup",
											'popup' 
									),
									"DELETE" => array (
											"url" => "ipmap/ipmap_delete/",
											"mode" => "single" 
									) 
							) 
					) 
			) );
		} else {
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
							"200",
							"accountid",
							"first_name,last_name,number",
							"accounts",
							"get_field_name_coma_new",
							"",
							"true",
							"center" 
					),
					array (
							gettext ( "Name" ),
							"190",
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
							"170",
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
							"100",
							"prefix",
							"",
							"",
							"",
							"",
							"true",
							"center" 
					),
			/*
            ASTPP  3.0  creation field show in grid
            */
			array (
							gettext ( "Status" ),
							"160",
							"status",
							"status",
							"ip_map",
							"get_status",
							"",
							"true",
							"center" 
					),
					array (
							gettext ( "Created Date" ),
							"150",
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
							"100",
							"",
							"",
							"",
							array (
									"EDIT" => array (
											"url" => "ipmap/ipmap_edit/",
											"mode" => "popup",
											'popup' 
									),
									"DELETE" => array (
											"url" => "ipmap/ipmap_delete/",
											"mode" => "single" 
									) 
							) 
					) 
			) );
		}
		return $grid_field_arr;
	}
	/**
	 * *******************************************************************************************************
	 */
	function build_grid_buttons() {
		$buttons_json = json_encode ( array (
				array (
						gettext ( "Add" ),
						"btn btn-line-warning btn",
						"fa fa-plus-circle fa-lg",
						"button_action",
						"/ipmap/ipmap_add/",
						"popup" 
				),
				array (
						gettext ( "Delete" ),
						"btn btn-line-danger",
						"fa fa-times-circle fa-lg",
						"button_action",
						"/ipmap/ipmap_delete_multiple/" 
				) 
		) );
		return $buttons_json;
	}
	
	/**
	 * *****
	 * ASTPP 3.0
	 * in customer Add/Edit show Ipmap (ACL) module.
	 * *****
	 */
	function build_grid_buttons_user() {
		$buttons_json = json_encode ( array (
				array (
						gettext ( "Create" ),
						"btn btn-line-warning btn",
						"fa fa-plus-circle fa-lg",
						"button_action",
						"/user/user_ipmap_add/",
						"popup" 
				),
				array (
						gettext ( "Delete" ),
						"btn btn-line-danger",
						"fa fa-times-circle fa-lg",
						"button_action",
						"/user/user_ipmap_delete_multiple/" 
				) 
		) );
		return $buttons_json;
	}

/**
 * ***********************************************************************************************************************
 */
}

?>
