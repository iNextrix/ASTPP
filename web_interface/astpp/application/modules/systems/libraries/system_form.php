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
class System_form {
	function get_template_form_fields() {
		$form ['forms'] = array (
				base_url () . 'systems/template_save/',
				array (
						"template_form",
						"name" => "template_form" 
				) 
		);
		$form ['Email Template'] = array (
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
						gettext ( ' Name' ),
						'INPUT',
						array (
								'name' => 'name',
								'size' => '20',
								'readonly' => true,
								'class' => "text field medium" 
						),
						'trim|required|xss_clean',
						'tOOL TIP',
						'' 
				),
				array (
						gettext ( 'Subject' ),
						'INPUT',
						array (
								'name' => 'subject',
								'size' => '20',
								'class' => "text field medium" 
						),
						'trim|required|xss_clean',
						'tOOL TIP',
						'' 
				),
				array (
						gettext ( 'Body' ),
						'TEXTAREA',
						array (
								'name' => 'template',
								'id' => 'template',
								'size' => '20',
								'class' => "textarea medium" 
						),
						'trim|required',
						'tOOL TIP',
						'' 
				) 
		);
		$form ['button_cancel'] = array (
				'name' => 'action',
				'content' => gettext ( 'Cancel' ),
				'value' => 'cancel',
				'type' => 'button',
				'class' => 'btn btn-line-sky margin-x-10',
				'onclick' => 'return redirect_page(\'systems/template/\')' 
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
	function get_template_search_form() {
		$form ['forms'] = array (
				"",
				array (
						'id' => "template_search" 
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
								'class' => "text field " 
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
						gettext ( 'Subject' ),
						'INPUT',
						array (
								'name' => 'subject[subject]',
								'',
								'size' => '20',
								'class' => "text field " 
						),
						'',
						'tOOL TIP',
						'1',
						'subject[subject-string]',
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
				'id' => "template_search_btn",
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
	function get_configuration_form_fields() {
		$form ['forms'] = array (
				base_url () . 'systems/configuration_save/',
				array (
						"id" => "config_form",
						"name" => "config_form" 
				) 
		);
		$form ['Edit Settings '] = array (
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
								'readonly' => true,
								'class' => "text field medium" 
						),
						'trim|required|xss_clean',
						'tOOL TIP',
						'' 
				),
				array (
						gettext ( 'Value' ),
						'INPUT',
						array (
								'name' => 'value',
								'size' => '20',
								'class' => "text field medium" 
						),
						'trim|required',
						'tOOL TIP',
						'' 
				),
				array (
						gettext ( 'Comment' ),
						'INPUT',
						array (
								'name' => 'comment',
								'size' => '20',
								'class' => "text field medium" 
						),
						'',
						'tOOL TIP',
						'' 
				) 
		);
		
		$form ['button_cancel'] = array (
				'name' => 'action',
				'content' => gettext ( 'Cancel' ),
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
	function get_configuration_search_form() {
		$form ['forms'] = array (
				"",
				array (
						'id' => "configuration_search" 
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
								'class' => "text field " 
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
						gettext ( 'Value' ),
						'INPUT',
						array (
								'name' => 'value[value]',
								'',
								'size' => '20',
								'class' => "text field " 
						),
						'',
						'tOOL TIP',
						'1',
						'value[value-string]',
						'',
						'',
						'',
						'search_string_type',
						'' 
				),
				array (
						gettext ( 'Description' ),
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
						gettext ( 'Group' ),
						'group_title',
						'SELECT',
						'',
						'',
						'tOOL TIP',
						'Please Enter account number',
						'group_title',
						'group_title',
						'system',
						'build_dropdown',
						'where_arr',
						"group_title NOT IN ('asterisk','osc','freepbx')",
						'group_by',
						'group_title' 
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
				'id' => "configuration_search_btn",
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
	function build_system_list_for_admin() {
		$grid_field_arr = json_encode ( array (
				array (
						gettext ( "Name" ),
						"190",
						"name",
						"",
						"",
						"" 
				),
				array (
						gettext ( "Value" ),
						"190",
						"value",
						"",
						"",
						"" 
				),
				array (
						gettext ( "Description" ),
						"320",
						"comment",
						"",
						"",
						"" 
				),
				array (
						gettext ( "Group" ),
						"120",
						"group_title",
						"",
						"",
						"" 
				),
				array (
						gettext ( "Action" ),
						"442",
						"",
						"",
						"",
						array (
								"EDIT" => array (
										"url" => "systems/configuration_edit/",
										"mode" => "popup" 
								) 
						) 
				) 
		) );
		return $grid_field_arr;
	}
	function build_grid_buttons() {
		$buttons_json = json_encode ( array () );
		return $buttons_json;
	}
	function build_template_list_for_admin() {
		$grid_field_arr = json_encode ( array (
				array (
						gettext ( "Name" ),
						"425",
						"name",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Subject" ),
						"650",
						"subject",
						"",
						"",
						"",
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
								"EDIT" => array (
										"url" => "systems/template_edit/",
										"mode" => "single" 
								) 
						) 
				) 
		) );
		return $grid_field_arr;
	}
	function build_country_list_for_admin() {
		$action = 'systems/country_list_edit/';
		$action_remove = 'systems/country_remove/';
		$mode = "popup";
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
						gettext ( "Name" ),
						"705",
						"country",
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
										"url" => "$action",
										"mode" => "$mode" 
								),
								"DELETE" => array (
										"url" => "$action_remove",
										"mode" => "single" 
								) 
						) 
				) 
		) );
		return $grid_field_arr;
	}
	function build_admin_grid_buttons() {
		$buttons_json = json_encode ( array (
				array (
						gettext ( "Create" ),
						"btn btn-line-warning btn",
						"fa fa-plus-circle fa-lg",
						"button_action",
						"systems/country_add/",
						"popup" 
				),
				array (
						gettext ( "Delete" ),
						"btn btn-line-danger",
						"fa fa-times-circle fa-lg",
						"button_action",
						"systems/country_delete_multiple" 
				) 
		) );
		return $buttons_json;
	}
	function get_search_country_form() {
		$form ['forms'] = array (
				"",
				array (
						'id' => "country_search" 
				) 
		);
		$form ['Search'] = array (
				array (
						gettext ( 'Name' ),
						'id',
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
				'id' => "country_search_btn",
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
	function get_country_form_fields() {
		$form ['forms'] = array (
				base_url () . 'systems/country_save/',
				array (
						'id' => 'system_form',
						'method' => 'POST',
						'name' => 'system_form' 
				) 
		);
		$form ['Country List'] = array (
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
								'name' => 'country',
								'size' => '20',
								'class' => "text field medium" 
						),
						'trim|required|char|min_length[2]|max_length[20]|xss_clean',
						'tOOL TIP',
						'Please Enter country' 
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
	function build_currency_list_for_admin() {
		$action = 'systems/currency_list_edit/';
		$action_remove = 'systems/currency_remove/';
		$mode = "popup";
		
		$grid_field_arr = json_encode ( array (
				array (
						"<input type='checkbox' name='chkAll' class='ace checkall'/><label class='lbl'></label>",
						"70",
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
						"320",
						"currencyname",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Code" ),
						"290",
						"currency",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Rate" ),
						"330",
						"currencyrate",
						"",
						"",
						"",
						"",
						"true",
						"right" 
				),
				array (
						gettext ( "Action" ),
						"265",
						"",
						"",
						"",
						array (
								"EDIT" => array (
										"url" => "$action",
										"mode" => "$mode" 
								),
								"DELETE" => array (
										"url" => "$action_remove",
										"mode" => "single" 
								) 
						) 
				) 
		) );
		return $grid_field_arr;
	}
	function get_search_currency_form() {
		$form ['forms'] = array (
				"",
				array (
						'id' => "currency_search" 
				) 
		);
		$form ['Search'] = array (
				
				array (
						gettext ( 'Name' ),
						'INPUT',
						array (
								'name' => 'currencyname[currencyname]',
								'',
								'size' => '20',
								'class' => "text field " 
						),
						'',
						'tOOL TIP',
						'1',
						'currencyname[currencyname-string]',
						'',
						'',
						'',
						'search_string_type',
						'' 
				),
				array (
						gettext ( 'Code' ),
						'INPUT',
						array (
								'name' => 'currency[currency]',
								'',
								'size' => '20',
								'class' => "text field " 
						),
						'',
						'tOOL TIP',
						'1',
						'currency[currency-string]',
						'',
						'',
						'',
						'search_string_type',
						'' 
				),
				array (
						gettext ( 'Rate' ),
						'INPUT',
						array (
								'name' => 'currencyrate[currencyrate]',
								'',
								'size' => '20',
								'class' => "text field " 
						),
						'',
						'tOOL TIP',
						'1',
						'currencyrate[currencyrate-integer]',
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
				'id' => "currency_search_btn",
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
	function get_currency_form_fields() {
		$form ['forms'] = array (
				base_url () . 'systems/currency_save/',
				array (
						'id' => 'system_form',
						'method' => 'POST',
						'name' => 'system_form' 
				) 
		);
		$form ['Currency List'] = array (
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
								'name' => 'currencyname',
								'size' => '20',
								'maxlength' => '40',
								'class' => "text field medium" 
						),
						'trim|required|char|xss_clean',
						'tOOL TIP',
						'Please Enter country' 
				),
				array (
						gettext ( 'Code' ),
						'INPUT',
						array (
								'name' => 'currency',
								'size' => '20',
								'maxlength' => '3',
								'class' => "text field medium" 
						),
						'trim|required|char|xss_clean',
						'tOOL TIP',
						'Please Enter country' 
				),
				array (
						gettext ( 'Rate' ),
						'INPUT',
						array (
								'name' => 'currencyrate',
								'size' => '20',
								'maxlength' => '7',
								'class' => "text field medium" 
						),
						'trim|required|xss_clean',
						'tOOL TIP',
						'Please Enter country' 
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
	function build_admin_currency_grid_buttons() {
		$buttons_json = json_encode ( array (
				array (
						gettext ( "Create" ),
						"btn btn-line-warning btn",
						"fa fa-plus-circle fa-lg",
						"button_action",
						"systems/currency_add/",
						"popup" 
				),
				array (
						gettext ( "Update Currencies" ),
						"btn btn-line-blue",
						"fa fa-upload fa-lg",
						"button_action",
						"currencyupdate/update_currency/",
						'single' 
				),
				array (
						gettext ( "Delete" ),
						"btn btn-line-danger",
						"fa fa-times-circle fa-lg",
						"button_action",
						"systems/currency_delete_multiple" 
				) 
		) );
		return $buttons_json;
	}
	function get_backup_database_form_fields($file_name, $id = '') {
		$val = $id > 0 ? "backup_database.path.$id" : 'backup_database.path';
		$form ['forms'] = array (
				base_url () . 'systems/database_backup_save/',
				array (
						'id' => 'backup_form',
						'method' => 'POST',
						'name' => 'backup_form' 
				) 
		);
		$form ['Database Information'] = array (
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
								'name' => 'backup_name',
								'size' => '20',
								'class' => "text field medium" 
						),
						'required',
						'tOOL TIP',
						'' 
				),
				array (
						gettext ( 'File Name' ),
						'INPUT',
						array (
								'name' => 'path',
								'size' => '20',
								'value' => $file_name,
								'class' => "text field medium" 
						),
						'trim|required|is_unique[' . $val . ']',
						'tOOL TIP',
						'' 
				) 
		);
		$form ['button_cancel'] = array (
				'name' => 'action',
				'content' => gettext ( 'Close' ),
				'value' => 'cancel',
				'type' => 'button',
				'class' => 'btn btn-line-sky  margin-x-10',
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
	function build_backupdastabase_list() {
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
						gettext ( "Date" ),
						"260",
						"date",
						"date",
						"date",
						"convert_GMT_to",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Name" ),
						"295",
						"backup_name",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "File Name" ),
						"480",
						"path",
						"",
						"",
						"",
						"",
						"true",
						"center" 
				),
				array (
						gettext ( "Action" ),
						"185",
						"",
						"",
						"",
						array (
								"EDIT_RESTORE" => array (
										"url" => "systems/database_restore_one/",
										"mode" => "" 
								),
								"DOWNLOAD_DATABASE" => array (
										"url" => "systems/database_download/",
										"mode" => "" 
								),
								"Delete" => array (
										"url" => "systems/database_delete/",
										"mode" => "" 
								) 
						) 
				) 
		) );
		return $grid_field_arr;
	}
	function build_backupdastabase_buttons() {
		$buttons_json = json_encode ( array (
				array (
						gettext ( "Create" ),
						"btn btn-line-warning btn",
						"fa fa-plus-circle fa-lg",
						"button_action",
						"systems/database_backup/",
						"popup" 
				),
				array (
						gettext ( "import" ),
						"btn btn-line-blue",
						"fa fa-upload fa-lg",
						"button_action",
						"systems/database_import/",
						"popup" 
				),
				array (
						gettext ( "Delete" ),
						"btn btn-line-danger",
						"fa fa-times-circle fa-lg",
						"button_action",
						"systems/database_backup_delete_multiple" 
				) 
		) );
		return $buttons_json;
	}
}

?>
