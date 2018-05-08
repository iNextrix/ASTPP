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
class simulator_form {
	function get_simulator_form_fields() {
		$form ['forms'] = array (
				base_url () . 'simulator/simulator_save/',
				array (
						'id' => 'simulators_form',
						'method' => 'POST',
						'name' => 'simulators_form'
				) 
		);
		$form [gettext ( 'Information' )] = array (
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
						''
				),
				array (
						gettext ( 'Provider' ),
						'provider_id',
						'SELECT',
						'',
						'trim|required|xss_clean',
						'tOOL TIP',
						'Please Enter account number',
						'id',
						'first_name,last_name,number',
						'accounts',
						'build_concat_dropdown',
						'where_arr',
						array (
								'type' => 3,
								"deleted" => "0",
								"status" => "0" 
						) 
				),
				array (
						gettext ( 'Gateway Name' ),
						'gateway_id',
						'SELECT',
						'',
						'trim|required|xss_clean',
						'tOOL TIP',
						'Please select gateway first',
						'id',
						'name',
						'gateways',
						'build_dropdown',
						'where_arr',
						array (
								"status" => "0" 
						) 
				),
				array (
						gettext ( 'Failover GW Name #1' ),
						'failover_gateway_id',
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
						array (
								"status" => "0" 
						) 
				),
				array (
						gettext ( 'Failover GW Name #2' ),
						'failover_gateway_id1',
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
						array (
								"status" => "0" 
						) 
				),
				array (
						gettext ( 'Concurrent Calls' ),
						'INPUT',
						array (
								'name' => 'maxchannels',
								'value' => '0',
								'size' => '20',
								'class' => "text field medium" 
						),
						'',
						'tOOL TIP',
						'' 
				),
				array (
						gettext ( 'CPS' ),
						'INPUT',
						array (
								'name' => 'cps',
								'value' => '0',
								'size' => '20',
								'class' => "text field medium" 
						),
						'',
						'tOOL TIP',
						'' 
				) 
		);
		
		$form [gettext ( 'Settings' )] = array (
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
				// Added code for caller id translation
				array (
						gettext ( 'Callerid Translation' ),
						'INPUT',
						array (
								'name' => 'cid_translation',
								'size' => '20',
								'class' => "text field medium" 
						),
						'',
						'tOOL TIP',
						'' 
				),
				array (
						gettext ( 'Codecs' ),
						'INPUT',
						array (
								'name' => 'codec',
								'size' => '20',
								'class' => "text field medium" 
						),
						'trim|xss_clean',
						'tOOL TIP',
						'' 
				),
				// Added call leg_timeout parameter to timeout the calls.
				array (
						'Call Timeout (Sec.)',
						'INPUT',
						array (
								'name' => 'leg_timeout',
								'size' => '4',
								'class' => "text field medium" 
						),
						'trim|xss_clean',
						'tOOL TIP',
						'Please Enter Call Leg Timeout' 
				),
				array (
						gettext ( 'Priority' ),
						'INPUT',
						array (
								'name' => 'precedence',
								'size' => '20',
								'class' => "text field medium" 
						),
						'',
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
						'Please Select Status',
						'',
						'',
						'',
						'set_status' 
				) 
		);
		$form ['button_cancel'] = array (
				'name' => 'action',
				'content' => gettext ( 'Close2' ),
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
	function get_simulator_search_form() {
		$form ['forms'] = array (
				"",
				array (
						'id' => "simulator_search"
				) 
		);
		$form [gettext ( 'Search' )] = array (
			array (
        						gettext ( 'Provider' ),
        						'provider_id',
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
        								'type' => 3,
        								"status" => 0,
        								"deleted" => 0
        						)
        				),
        					array (
                        						gettext ( 'Rate Group' ),
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
                        						array (
                        								"status" => "0"
                        						)
                        				),
        				array (
                        						gettext ( 'Dialed Digits' ),
                        						'INPUT',
                        						array (
                        								'name' => 'from',
                        								'size' => '30',
                        								'class' => "text field medium"
                        						),
                        						'trim|required|xss_clean',
                        						'tOOL TIP',
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
				'id' => "simulator_search_btn",
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
	function build_simulator_list_for_admin() {
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
                						gettext ( 'Code' ),
                						"140",
                						"pattern",
                						"pattern",
                						"",
                						"get_only_numeric_val",
                						"",
                						"true",
                						"center"
                				),
				array (
						gettext ( "Provider" ),
						"110",
						"trunk_id",
						"name, codec",
						"trunks",
						"build_concat_string",
						"",
						"true",
						"center"
				),

		) );
		return $grid_field_arr;
	}
	function build_grid_buttons() {
		$buttons_json = json_encode ( array (
		

				array (
						("Create"),
						"btn btn-line-warning btn",
						"fa fa-plus-circle fa-lg",
						"button_action",
						"/simulator/simulator_add/",
						"popup",
						"medium"
				),
				array (
						gettext ( "Delete" ),
						"btn btn-line-danger",
						"fa fa-times-circle fa-lg",
						"button_action",
						"/simulator/simulator_delete_multiple/"
				)
		) );
		return $buttons_json;
	}
}

?>
