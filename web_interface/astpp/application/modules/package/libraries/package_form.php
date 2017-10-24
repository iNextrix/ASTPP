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
class Package_form {
	function get_package_form_fields($id = '') {
		$form ['forms'] = array (
				base_url () . 'package/package_save/' . $id . "/",
				array (
						'id' => 'packeage_form',
						'method' => 'POST',
						'name' => 'packeage_form' 
				) 
		);
		$form [gettext ( 'Package Information' )] = array (
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
								'name' => 'package_name',
								'size' => '20',
								'class' => "text field medium" 
						),
						'trim|required|xss_clean',
						'tOOL TIP',
						'' 
				),
				array (
						gettext ( 'Rate Group' ),
						'pricelist_id',
						'SELECT',
						'',
						'dropdown',
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
						gettext ( 'Included Seconds' ),
						'INPUT',
						array (
								'name' => 'includedseconds',
								'size' => '20',
								'class' => "text field medium" 
						),
						'trim|is_numeric|required|xss_clean',
						'tOOL TIP',
						'' 
				),
				/**
				 * ASTPP 3.0
				 * Add For Package Inbound or Outbound or both?
				 * *
				 */
				array (
						'Applicable For?',
						'applicable_for',
						'SELECT',
						'',
						'',
						'tOOL TIP',
						'',
						'',
						'',
						'',
						'set_package_type',
						'' 
				),
				/**
				 * ******************************************
				 */
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
		$form ['button_cancel'] = array (
				'name' => 'action',
				'content' => gettext ( 'Cancel' ),
				'value' => 'cancel',
				'type' => 'button',
				'class' => 'btn btn-line-sky margin-x-10',
				'onclick' => 'return redirect_page(\'/package/package_list/\')' 
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
	function get_package_search_form() {
		$form ['forms'] = array (
				"",
				array (
						'id' => "package_search" 
				) 
		);
		$form [gettext ( 'Search' )] = array (
				array (
						gettext ( 'Name' ),
						'INPUT',
						array (
								'name' => 'package_name[package_name]',
								'',
								'size' => '20',
								'class' => "text field" 
						),
						'',
						'tOOL TIP',
						'1',
						'package_name[package_name-string]',
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
						gettext ( 'Included Seconds' ),
						'INPUT',
						array (
								'name' => 'includedseconds[includedseconds]',
								'value' => '',
								'size' => '20',
								'class' => "text field" 
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
				/**
				 * ASTPP 3.0
				 * Add For Package Inbound or Outbound or both?
				 * *
				 */
				array (
						gettext ( 'Applicable For?' ),
						'applicable_for',
						'SELECT',
						'',
						'',
						'tOOL TIP',
						'',
						'',
						'',
						'',
						'set_package_type',
						'',
						'' 
				),
				/**
				 * ******************************************
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
				'id' => "package_search_btn",
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
				'class' => 'btn btn-line-sky pull-right  margin-x-10' 
		);
		
		return $form;
	}
	function build_package_list_for_admin() {
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
				 * For Package edit on Name
				 * *
				 */
				array (
						gettext ( "Name" ),
						"170",
						"package_name",
						"",
						"",
						"",
						"EDITABLE",
						"true",
						"center" 
				),
				/**
				 * ********************************
				 */
				array (
						gettext ( "Rate Group" ),
						"150",
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
						"160",
						"includedseconds",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				/**
				 * ASTPP 3.0
				 * Add For Package Inbound or Outbound or both?
				 * *
				 */
				array (
						gettext ( "Applicable For?" ),
						"200",
						"applicable_for",
						"applicable_for",
						"applicable_for",
						"get_package_type",
						"",
						"true",
						"center" 
				),
				/**
				 * *************************************************
				 */
				/*
				 * ASTPP 3.0
				 * Creation date,last modified date show in grid
				 */
				array (
						gettext ( "Status" ),
						"140",
						"status",
						"status",
						"packages",
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
				/**
				 * ************************************************************
				 */
				/*
				 * ASTPP 3.0
				 * status show active or inactive
				 */
				
				/**
				 * *****************************************
				 */
				array (
						gettext ( "Action" ),
						"160",
						"",
						"",
						"",
						array (
								"EDIT" => array (
										"url" => "package/package_edit/",
										"mode" => "single" 
								),
								"DELETE" => array (
										"url" => "package/package_delete/",
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
						"/package/package_add/" 
				),
				array (
						gettext ( "Delete" ),
						"btn btn-line-danger",
						"fa fa-times-circle fa-lg",
						"button_action",
						"/package/package_delete_multiple/" 
				) 
		) );
		return $buttons_json;
	}
	function build_package_counter_list_for_admin() {
		// array(display name, width, db_field_parent_table,feidname, db_field_child_table,function name);
		$grid_field_arr = json_encode ( array (
				array (
						gettext ( "Package Name" ),
						"430",
						"package_id",
						"package_name",
						"packages",
						"get_field_name",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Account" ),
						"420",
						"accountid",
						"first_name,last_name,number",
						"accounts",
						"get_field_name_coma_new",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Used Seconds" ),
						"420",
						"seconds",
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
	function build_pattern_list_for_customer($packageid) {
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
						gettext ( "Code" ),
						"450",
						"patterns",
						"patterns",
						"",
						"get_only_numeric_val" 
				),
				array (
						gettext ( "Destination" ),
						"450",
						"destination",
						"",
						"",
						"" 
				),
				array (
						gettext ( "Action" ),
						"100",
						"",
						"",
						"",
						array (
								"DELETE" => array (
										"url" => "package/package_patterns_delete/$packageid/",
										"mode" => "single" 
								) 
						) 
				) 
		) );
		return $grid_field_arr;
	}
	function set_pattern_grid_buttons($packageid) {
		$buttons_json = json_encode ( array (
				array (
						gettext ( "Create" ),
						"btn btn-line-warning btn",
						"fa fa-plus-circle fa-lg",
						"button_action",
						"/package/customer_add_patterns/$packageid",
						"popup" 
				) 
		) );
		return $buttons_json;
	}
	function build_package_counter_report() {
		$buttons_json = json_encode ( array (
				array (
						gettext ( "Export" ),
						"btn btn-xing",
						" fa fa-download fa-lg",
						"button_action",
						"/package/package_counter_report_export/",
						'single' 
				) 
		)
		 );
		return $buttons_json;
	}
	function build_package_list_for_reseller() {
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
}

?>
