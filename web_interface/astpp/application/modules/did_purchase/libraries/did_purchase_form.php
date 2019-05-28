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
class did_purchase_form extends common {
	function __construct() {
		$this->CI = & get_instance ();
	}
	
	function get_search_for_did_purchase() {
		$account_info = $this->CI->session->userdata ( 'accountinfo' );
		$form ['forms'] = array (
			"",
			array (
					'id' => "did_purchase_search" 
			) 
		);
		if($account_info['type'] == -1){
			
			$form ['Search'] = array (
					
					array (
							gettext ( 'Country' ),
							array (
									'name' => 'country_id',
									'class' => 'country_id',
									'id' => 'country_id' 
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
						gettext ( 'Provience' ),
						array (
								'name' => 'province',
								'id' => 'provience_id_search_drp',
								'size' => '20',
								'class' => "text field" 
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
						'where_arr',
						array('id'=>'-1')
					),
					array (
						gettext ( 'City' ),
						array (
								'name' => 'city',
								'id' => 'city_id_search_drp',
								'size' => '20',
								'class' => "text field" 
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
						'where_arr',
						array('id'=>'-1')
					),
					array(
						gettext ( 'Reseller'),
							array ( 
								'name'=>'reseller_id',
								'class'=>'reseller_id_search_drp'
							),  
						'SELECT',
						'',
						'', 
						'tOOL TIP',
						'Please Enter account number', 
						'id', 
						'first_name,last_name,number',
						'accounts',
						'build_concat_dropdown_reseller',
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
		}else if($account_info['type'] == 1){
			$form ['Search'] = array (
					
				array (
						gettext ( 'Country' ),
						array (
								'name' => 'country_id',
								'class' => 'country_id',
								'id' => 'country_id' 
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
					gettext ( 'Provience' ),
					array (
							'name' => 'province',
							'id' => 'provience_id_search_drp',
							'size' => '20',
							'class' => "text field" 
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
					'where_arr',
					array('id'=>'-1') 
				),
				array (
					gettext ( 'City' ),
					array (
							'name' => 'city',
							'id' => 'city_id_search_drp',
							'size' => '20',
							'class' => "text field" 
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
					'where_arr',
					array('id'=>'-1')
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
				'id' => "did_purchase_search_btn",
				'content' => gettext ( 'Search' ),
				'value' => 'save',
				'type' => 'button',
				'class' => 'btn btn-success float-right' 
		);
		$form ['button_reset'] = array (
				'name' => 'action',
				'id' => "id_reset",
				'content' => gettext ( 'Clear' ),
				'value' => 'cancel',
				'type' => 'reset',
				'class' => 'btn btn-secondary float-right ml-2' 
		);
		
		return $form;
	}

	function build_did_purchase_list() {
		$account_info = $accountinfo = $this->CI->session->userdata ( 'accountinfo' );
		$currency_id = $account_info ['currency_id'];
		$currency = $this->CI->common->get_field_name ( 'currency', 'currency', $currency_id );
		$reseller=$this->CI->session->userdata ( 'did_reseller_id' );
		if(isset($reseller) && $reseller > 0){
		$setup_fee=array (
			gettext ( "Setup Fee ($currency)" ),
			"80",
			"setup_fee",
			"setup_fee",
			"setup_fee",
			"convert_to_currency_account",
			"",
			"true",
			"right" 
		);
		$price=array (
				gettext ( "Price ($currency)" ),
				"80",
				"price",
				"price",
				"price",
				"convert_to_currency_account",
				"",
				"true",
				"right" 
		);
	}else{
		$setup_fee=array (
			gettext ( "Setup Fee ($currency)" ),
			"80",
			"setup",
			"setup",
			"setup",
			"convert_to_currency_account",
			"",
			"true",
			"right" 
		);
		$price=array (
				gettext ( "Price ($currency)" ),
				"80",
				"monthlycost",
				"monthlycost",
				"monthlycost",
				"convert_to_currency_account",
				"",
				"true",
				"right" 
		);
	}
		// array(display name, width, db_field_parent_table,feidname, db_field_child_table,function name);
		$grid_field_arr = json_encode ( array (
				array (
						"<input type='checkbox' name='chkAll' class='ace checkall did_select'/><label class='lbl'></label>",
						"40",
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
						"70",
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
						"100",
						"country_id",
						"country",
						"countrycode",
						"get_field_name",
						"",
						"true",
						"center" 
				),
				// array (
				// 		gettext ( "Account" ),
				// 		"105",
				// 		"accountid",
				// 		"first_name,last_name,number",
				// 		"accounts",
				// 		"get_field_name_coma_new",
				// 		"",
				// 		"true",
				// 		"center" 
				// ),		
				// array (
				// 		gettext ( "Reseller" ),
				// 		"100",
				// 		"parent_id",
				// 		"first_name,last_name,number",
				// 		"accounts",
				// 		"build_concat_string",
				// 		"",
				// 		"true",
				// 		"center" 
				// ),
				
				array (
						gettext ( "Province" ),
						"60",
						"province",
						"",
						"",
						"",
						"",
						"true",
						"right" 
				),
				array (
					gettext ( "City" ),
					"60",
					"city",
					"",
					"",
					"",
					"",
					"true",
					"right" 
				),	
				array (
						gettext ( "Cost/Min ($currency)" ),
						"60",
						"cost",
						"cost",
						"cost",
						"convert_to_currency_account",
						"",
						"true",
						"right" 
				),
				$setup_fee,
				$price,
				array (
						gettext ( "Call Timeout" ),
						"60",
						"leg_timeout",
						"",
						"",
						"",
						"",
						"true",
						"right" 
				),
				array (
						gettext ( "CC" ),
						"60",
						"maxchannels",
						"",
						"",
						"",
						"",
						"true",
						"right" 
				),
				array (
						gettext ( "Billing Type" ),
						"80",
						"id",
						"id",
						"id",
						"get_did_billing_type",
						"",
						"true",
						"right" 
				),

				array (
						gettext ( "Billing Days" ),
						"80",
						"productid",
						"billing_days",
						"products",
						"get_field_name",
						"",
						"true",
						"right" 
				),
				
				// array (
				// 		gettext ( "Is Purchased?" ),
				// 		"110",
				// 		"number",
				// 		"number",
				// 		"number",
				// 		"check_did_avl",
				// 		"",
				// 		"true",
				// 		"center" 
				// ),
				// array (
				// 		gettext ( "Call Type" ),
				// 		"90",
				// 		"call_type",
				// 		"call_type",
				// 		"call_type",
				// 		"get_call_type",
				// 		"",
				// 		"true",
				// 		"center" 
				// ),
				//Hiral
				//HP: PBX_ADDON
				// array (
				// 		gettext ( "Destination" ),
				// 		"80",
				// 		"did_id_new",
				// 		"did_id_new",
				// 		"did_id_new",
				// 		"get_call_type_grid",
				// 		"",
				// 		"true",
				// 		"center" 
				// ),
				// //END
				// array (
				// 		gettext ( "Forwarding" ),
				// 		"80",
				// 		"did_id",
				// 		"did_id",
				// 		"did_id",
				// 		"build_did_forward",
				// 		"",
				// 		"true",
				// 		"center" 
				// ),
				// array (
				// 		gettext ( "Status" ),
				// 		"90",
				// 		"status",
				// 		"status",
				// 		"dids",
				// 		"get_status",
				// 		"",
				// 		"true",
				// 		"center" 
				// ),
				array (
						gettext ( "Action" ),
						"100",
						"",
						"",
						"",
						array (
								"EDIT" => array (
										"url" => "products/products_edit/",
										"mode" => "single",
										"layout" => "medium" 
								),
								"DELETE" => array (
										"url" => "did/did_remove/",
										"mode" => "single" 
								) 
						),
						"false" 
				) 
		) );
		return $grid_field_arr;
	}
	function build_grid_buttons() {
		$buttons_json = json_encode ( array (
				
				array (
						gettext ( "Assign" ),
						"btn btn-line-warning btn package",
						"fa fa-plus-circle fa-lg",
						"",
						"/did_purchase/did_purchase_add_account/",
						"popup",
						"",
						"create"
				),
		) );
		return $buttons_json;
	}
	function get_account_form_fields(){
		$did_reseller_id = $this->CI->session->userdata ( "did_reseller_id" );
		$did_reseller_id=isset($did_reseller_id) ? $did_reseller_id : 0;
		$form ['forms'] = array (
			base_url () . 'did_purchase/did_purchase_account_save/',
			array (
					"id" => "account_form",
					"name" => "account_form" 
			) 
	);
	$form [gettext ( 'Account Information' )] = array (
		array (
			gettext ( 'Account' ),
				array ( 
					'name'=>'accountid',
					'id'=>'accountid_search_drp',
					'class'=>'accountid_search_drp'
				),  
				'SELECT',
				'',
				'',
				'tOOL TIP',
				'Please Enter account number',
				'id',
				'number',
				'accounts',
				'build_dropdown',
				'where_arr',
				array (
						"reseller_id" => $did_reseller_id,
						"type" => "0",
						"deleted"=>"0",
						"status"=>0 
				) 

		)
	);
	
		$form ['button_cancel'] = array (
				'name' => 'action',
				'content' => gettext ( 'Close' ),
				'value' => 'cancel',
				'type' => 'button',
				'class' => 'btn btn-secondary ml-2',
				'onclick' => 'return redirect_page(\'NULL\')' 
		);
		$form ['button_save'] = array (
				'name' => 'action',
				'content' => gettext ( 'Save' ),
				'value' => 'save',
				'id' => 'submit',
				'type' => 'button',
				'class' => 'btn btn-success' 
		);
		
		return $form;
	}
}
?>
