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
class pricing_form {
	function __construct($library_name = '') {
		$this->CI = & get_instance ();
	}
	function get_pricing_form_fields() {
		$form ['forms'] = array (
				base_url () . 'pricing/price_save/',
				array (
						'id' => 'pricing_form',
						'method' => 'POST',
						'name' => 'pricing_form' 
				) 
		);
		if ($this->CI->session->userdata ( 'logintype' ) == 1 || $this->CI->session->userdata ( 'logintype' ) == 5) {
			$form ['Rate Group Information'] = array (
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
									'name' => 'status',
									'value' => '1' 
							),
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
							'Please Enter account number' 
					),
					array (
							gettext ( 'Routing Type' ),
							'routing_type',
							'SELECT',
							'',
							'',
							'tOOL TIP',
							'Please Select Status',
							'',
							'',
							'',
							'set_routetype' 
					),
					array (
							gettext ( 'Initial Increment' ),
							'INPUT',
							array (
									'name' => 'initially_increment',
									'size' => '20',
									'class' => "text field medium" 
							),
							'trim|required|xss_clean',
							'tOOL TIP',
							'Please Enter account number' 
					),
					array (
							gettext ( 'Default Increment' ),
							'INPUT',
							array (
									'name' => 'inc',
									'size' => '20',
									'class' => "text field medium" 
							),
							'trim|required|xss_clean',
							'tOOL TIP',
							'Please Enter account number' 
					),
					array (
							gettext ( 'Markup(%)' ),
							'INPUT',
							array (
									'name' => 'markup',
									'value' => "0",
									'size' => '20',
									'class' => "text field medium" 
							),
							'trim|required|xss_clean',
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
		} else {
			$form ['Rate Group Information'] = array (
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
									'name' => 'status',
									'value' => '1' 
							),
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
									'maxlength' => '30',
									'class' => "text field medium" 
							),
							'trim|required|xss_clean',
							'tOOL TIP',
							'Please Enter account number' 
					),
					array (
							gettext ( 'Routing Type' ),
							'routing_type',
							'SELECT',
							'',
							'',
							'tOOL TIP',
							'Please Select Status',
							'',
							'',
							'',
							'set_routetype' 
					),
					array (
							gettext ( 'Initial Increment' ),
							'INPUT',
							array (
									'name' => 'initially_increment',
									'size' => '20',
									'class' => "text field medium" 
							),
							'trim|required|xss_clean',
							'tOOL TIP',
							'Please Enter account number' 
					),
					array (
							gettext ( 'Default Increment' ),
							'INPUT',
							array (
									'name' => 'inc',
									'size' => '20',
									'class' => "text field medium" 
							),
							'trim|required|xss_clean',
							'tOOL TIP',
							'Please Enter account number' 
					),
					array (
							gettext ( 'Markup(%)' ),
							'INPUT',
							array (
									'name' => 'markup',
									'value' => "0",
									'size' => '20',
									'class' => "text field medium" 
							),
							'trim|required|xss_clean',
							'tOOL TIP',
							'Please Enter account number' 
					),
					array (
							gettext ( 'Trunks' ),
							'trunk_id',
							'SELECT',
							'',
							'',
							'tOOL TIP',
							'Please Select Trunks',
							'id',
							'name',
							'trunks',
							'build_dropdown',
							'where_arr',
							array (
									"status <" => "2" 
							),
							'multi' 
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
	function get_pricing_search_form() {
		$form ['forms'] = array (
				"",
				array (
						'id' => "price_search" 
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
						gettext ( 'Routing Type' ),
						'routing_type',
						'SELECT',
						'',
						'',
						'tOOL TIP',
						'Please Enter account number',
						'',
						'',
						'',
						'set_routetype_status',
						'',
						'' 
				),
				array (
						gettext ( 'Initial Increment ' ),
						'INPUT',
						array (
								'name' => 'initially_increment[initially_increment]',
								'',
								'size' => '20',
								'class' => "text field" 
						),
						'',
						'tOOL TIP',
						'1',
						'initially_increment[initially_increment-string]',
						'',
						'',
						'',
						'search_string_type',
						'' 
				),
				array (
						gettext ( 'Default Increment ' ),
						'INPUT',
						array (
								'name' => 'inc[inc]',
								'',
								'size' => '20',
								'class' => "text field" 
						),
						'',
						'tOOL TIP',
						'1',
						'inc[inc-string]',
						'',
						'',
						'',
						'search_string_type',
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
		)
		;
		$form ['button_search'] = array (
				'name' => 'action',
				'id' => "price_search_btn",
				'content' => gettext ( 'Search' ),
				'value' => 'save',
				'type' => 'button',
				'class' => "btn btn-line-parrot pull-right" 
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
	
	/*
	 * ASTPP 3.0 Changes in grid size
	 */
	function build_pricing_list_for_admin() {
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
				 * For Rategroup edit on Name
				 * *
				 */
				array (
						gettext ( "Name" ),
						"110",
						"name",
						"",
						"",
						"",
						"EDITABLE",
						"true",
						"center" 
				),
				/**
				 * ************************************
				 */
				array (
						gettext ( "Routing Type" ),
						"120",
						"routing_type",
						"routing_type",
						"routing_type",
						"get_routetype" 
				),
				array (
						gettext ( "Initial Increment" ),
						"140",
						"initially_increment",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				array (
						"Default Increment",
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
						gettext ( "Markup(%)" ),
						"100",
						"markup",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Rate Count" ),
						"100",
						"id",
						"pricelist_id",
						"routes",
						"get_field_count",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Status" ),
						"110",
						"status",
						"id",
						"pricelists",
						"get_status",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Created Date" ),
						"120",
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
						"140",
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
						"150",
						"",
						"",
						"",
						array (
								"EDIT" => array (
										"url" => "pricing/price_edit/",
										"mode" => "popup" 
								),
								
								"DELETE" => array (
										"url" => "pricing/price_delete/",
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
						"/pricing/price_add/",
						"popup" 
				),
				array (
						gettext ( "Delete" ),
						"btn btn-line-danger",
						"fa fa-times-circle fa-lg",
						"button_action",
						"/pricing/price_delete_multiple/" 
				) 
		) );
		return $buttons_json;
	}
}

?>
