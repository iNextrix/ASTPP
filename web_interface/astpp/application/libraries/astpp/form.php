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

/**
 * Dynamically build forms for display
 */
class Form {
	protected $CI; // codeigniter
	protected $fields = array (); // array of fields
	protected $form_title = 'Form';
	protected $form_id = 'form';
	protected $form_action = '';
	protected $form_class = '';
	protected $hidden = array ();
	protected $multipart = FALSE; // default to standard form
	protected $submit_button = 'Submit';
	protected $after_button = '';
	protected $rules = array (); // storage for validation rules
	function __construct() {
		$this->CI = & get_instance ();
		$this->CI->load->library ( 'form_validation' );
		$this->CI->load->library ( 'astpp/common' );
		$this->CI->load->model ( 'db_model' );
		$this->check_permissions ();
	}
	
	// __construct
	/**
	 * adds raw html to the field array
	 */
	function check_permissions() {
		if ($this->CI->session->userdata ( 'user_login' ) == TRUE) {
			$module_info = unserialize ( $this->CI->session->userdata ( "permited_modules" ) );
			if ($this->CI->session->userdata ( 'userlevel_logintype' ) != 0 && $this->CI->session->userdata ( 'userlevel_logintype' ) != 3) {
				$module_info [] = 'dashboard';
			}
			$url = $this->CI->uri->uri_string;
			$file_name = explode ( "/", $url );
			if (isset ( $file_name ['1'] )) {
				$module = explode ( '_', $file_name ['1'] );
			} else {
				$module = $file_name;
			}
			if ($this->CI->session->userdata ( 'userlevel_logintype' ) != -1) {
				$module_info [] = 'user';
			}
			if (in_array ( $module [0], $module_info )) {
				if ($this->CI->session->userdata ( 'userlevel_logintype' ) == 0 && $module [0] == 'customer' && isset ( $file_name [1] ) && $file_name [1] != 'customer_transfer') {
					redirect ( base_url () . 'user/user/' );
				} else {
					return true;
				}
			} else {
				$this->CI->load->library('astpp/permission');
				$this->CI->session->set_userdata ( 'astpp_errormsg', 'You do not have permission to access this module..!' );
				$url= $this->CI->session->userdata ( 'userlevel_logintype' ) ==0 ? 'user/user/' : "dashboard/";
				$this->CI->permission->permission_redirect_url($url);
			}
		} else {
			redirect ( base_url () );
		}
	}
	function build_form($fields_array, $values) {
		$form_contents = '';
		$form_contents .= '<div class="pop_md col-md-12 margin-t-10 padding-x-8">';
		if (isset ( $fields_array ['breadcrumb'] )) {
			$form_contents .= form_breadcrumb ( $fields_array ['breadcrumb'] );
			unset ( $fields_array ['breadcrumb'] );
		}
		$form_contents .= form_open ( $fields_array ['forms'] [0], $fields_array ['forms'] [1] );
		unset ( $fields_array ['forms'] );
		$button_array = array ();
		if (isset ( $fields_array ['button_save'] ) || isset ( $fields_array ['button_cancel'] ) || isset ( $fields_array ['additional_button'] )) {
			$save = $fields_array ['button_save'];
			unset ( $fields_array ['button_save'] );
			if (isset ( $fields_array ['button_cancel'] )) {
				$cancel = $fields_array ['button_cancel'];
				unset ( $fields_array ['button_cancel'] );
			}
			if (isset ( $fields_array ['additional_button'] )) {
				$additiopnal_button = $fields_array ['additional_button'];
				unset ( $fields_array ['additional_button'] );
			}
		}
		if (isset ( $additiopnal_button )) {
			$form_contents .= form_button ( gettext ( $additiopnal_button ) );
		}
		$i = 0;
		foreach ( $fields_array as $fieldset_key => $form_fileds ) {
			if (count ( $fields_array ) > 1) {
				if ($i == 1 || $i == 3) {
					$form_contents .= '<div class="col-md-6 no-padding pull-right">';
					$form_contents .= '<div class="col-md-12 padding-x-4">';
				} else {
					$form_contents .= '<div class="col-md-6 no-padding">';
					$form_contents .= '<div class="col-md-12 padding-x-4">';
				}
			} else {
				$form_contents .= '<div class="col-md-12 no-padding">';
				$form_contents .= '<div class="col-md-12 no-padding">';
			}
			$form_contents .= '<ul class="no-padding">';
			$form_contents .= '<div class="col-md-12 no-padding">';
			if ($i == 1 || $i == 3) {
				$form_contents .= form_fieldset ( gettext ( $fieldset_key ) );
			} else {
				$form_contents .= form_fieldset ( gettext ( $fieldset_key ) );
			}
			foreach ( $form_fileds as $fieldkey => $fieldvalue ) {
				if (isset ( $fieldvalue ) && $fieldvalue != '') {
					$form_contents .= '<li class="col-md-12">';
					if ($fieldvalue [1] == 'HIDDEN') {
						if (isset ( $this->CI->input->post ))
							$fieldvalue [2] ['value'] = (! $this->CI->input->post ( $fieldvalue [2] ['name'] )) ? @$fieldvalue [2] ['value'] : $this->CI->input->post ( $fieldvalue [2] ['name'] );
						else
							$fieldvalue [2] ['value'] = ($values) ? (isset ( $values [$fieldvalue [2] ['name']] ) ? $values [$fieldvalue [2] ['name']] : '') : (isset ( $fieldvalue [2] ['value'] ) ? $fieldvalue [2] ['value'] : '');
						$form_contents .= form_hidden ( $fieldvalue [2] ['name'], $fieldvalue [2] ['value'] );
					} else {
						$validation_arr = array ();
						if ($fieldvalue [1] == 'INPUT') {
							if (! empty ( $fieldvalue [3] )) {
								$validation_arr = explode ( "|", $fieldvalue [3] );
							}
						} elseif ($fieldvalue [2] == 'SELECT') {
							
							if (is_array ( $fieldvalue [4] )) {
								$validation_arr = explode ( "|", $fieldvalue [4] ['rules'] );
							} else {
								$validation_arr = explode ( "|", $fieldvalue [4] );
							}
						}
						if (! empty ( $validation_arr )) {
							$fieldvalue [0] = in_array ( 'required', $validation_arr ) ? $fieldvalue [0] . "<span style='color:black;'> *</span>" : $fieldvalue [0];
							$fieldvalue [0] = in_array ( 'dropdown', $validation_arr ) ? $fieldvalue [0] . "<span style='color:black;'> *</span>" : $fieldvalue [0];
						}
						
						if (is_array ( $fieldvalue [1] ) || (is_array ( $fieldvalue [2] ) && isset ( $fieldvalue [2] ['hidden'] ))) {
							$form_contents .= form_label ( gettext ( $fieldvalue [0] ), $fieldvalue [0], array (
									'class' => 'col-md-3 no-padding add_settings' 
							) );
						} else {
							$form_contents .= form_label ( gettext ( $fieldvalue [0] ), "", array (
									"class" => "col-md-3 no-padding" 
							) );
						}
					}
					if ($fieldvalue [2] == 'SELECT' && ! isset ( $fieldvalue [13] )) {
						
						/*
						 * To make Drop down enabled disabled
						 */
						$extra = isset ( $fieldvalue [1] ['extra'] ) ? $fieldvalue [1] ['extra'] : '';
						/**
						 * ************************
						 */
						if ($fieldvalue [7] != '' && $fieldvalue [8] != '') {
							$str = $fieldvalue [7] . "," . $fieldvalue [8];
							
							if (isset ( $this->CI->input->post )) {
								$fieldvalue ['value'] = (! $this->CI->input->post ( $fieldvalue [1] )) ? @$fieldvalue [1] : $this->CI->input->post ( $fieldvalue [1] );
							} else {
								if (is_array ( $fieldvalue [1] )) {
									$fieldvalue ['value'] = ($values) ? @$values [$fieldvalue [1] ['name']] : @$fieldvalue [1];
								} else {
									$fieldvalue ['value'] = ($values) ? @$values [$fieldvalue [1]] : @$fieldvalue [1];
								}
							}
							$drp_array = call_user_func_array ( array (
									$this->CI->db_model,
									$fieldvalue [10] 
							), array (
									$str,
									$fieldvalue [9],
									$fieldvalue [11],
									$fieldvalue [12] 
							) );
							
							if ($fieldset_key == gettext ( 'System Configuration Information' ) || ($fieldset_key == 'Billing Information' && $fieldvalue [0] == 'Force Trunk') || ($fieldset_key == 'Card Information' && $fieldvalue [0] == 'Rate Group') || ($fieldset_key == 'Billing Information' && $fieldvalue [0] == 'Account') || $fieldset_key == 'Freeswitch Devices' && $fieldvalue [0] == 'Rate Group' || ($fieldset_key == 'Origination Rate Add/Edit' && $fieldvalue [0] == 'Trunks') || $fieldset_key == 'Billing Information' && $fieldvalue [0] == 'Rate Group' || ($fieldset_key == 'Information' && $fieldvalue [0] == 'Failover GW Name #1') || ($fieldset_key == 'Information' && $fieldvalue [0] == 'Failover GW Name #2') || ($fieldset_key == 'Information' && $fieldvalue [0] == 'Rate Group') || ($fieldset_key == 'Sip Devices' && $fieldvalue [0] == 'Sip Profile') || ($fieldset_key == 'Sip Devices' && $fieldvalue [0] == 'Account')) {
								$form_contents .= form_dropdown_all ( $fieldvalue [1], $drp_array, $fieldvalue ['value'], $extra );
							} else {
								$form_contents .= form_dropdown ( $fieldvalue [1], $drp_array, $fieldvalue ['value'], $extra );
							}
							if (isset ( $fieldvalue [4] ) && $fieldvalue [4] != '') {
								if (is_array ( $fieldvalue [4] )) {
									
									if (isset ( $fieldvalue [1] ['name'] )) {
										$fieldvalue_pass = $fieldvalue [1] ['name'];
									} else {
										$fieldvalue_pass = $fieldvalue [1];
									}
									
									$this->CI->form_validation->set_rules ( $fieldvalue_pass, $fieldvalue [0], $fieldvalue [4] ['rules'] );
								} else {
									
									if (isset ( $fieldvalue [1] ['name'] )) {
										$fieldvalue_pass = $fieldvalue [1] ['name'];
									} else {
										$fieldvalue_pass = $fieldvalue [1];
									}
									
									$this->CI->form_validation->set_rules ( $fieldvalue_pass, $fieldvalue [0], $fieldvalue [4] );
								}
							}
							$form_contents .= '<div class="tooltips error_div pull-left no-padding" id="' . (is_array ( $fieldvalue [1] ) ? $fieldvalue [1] ['name'] : $fieldvalue [1]) . '_error_div" ><i style="color:#D95C5C; padding-left: 3px; padding-top: 10px;" class="fa fa-exclamation-triangle"></i>';
							$form_contents .= '<span class="popup_error error  no-padding" id="' . (gettext ( is_array ( $fieldvalue [1] ) ? $fieldvalue [1] ['name'] : $fieldvalue [1] )) . '_error">
                        </span></div>';
						} else {
							if (isset ( $this->CI->input->post )) {
								$fieldvalue ['value'] = (! $this->CI->input->post ( $fieldvalue [1] )) ? @$fieldvalue [1] : $this->CI->input->post ( $fieldvalue [1] );
							} else {
								if (is_array ( $fieldvalue [1] )) {
									$fieldvalue ['value'] = ($values) ? @$values [$fieldvalue [1] ['name']] : @$fieldvalue [1];
								} else {
									$fieldvalue ['value'] = ($values) ? @$values [$fieldvalue [1]] : @$fieldvalue [1];
								}
							}
							
							$str = $fieldvalue [7] . "," . $fieldvalue [8];
							$drp_array = call_user_func_array ( array (
									$this->CI->common,
									$fieldvalue [10] 
							), array (
									$fieldvalue [9] 
							) );
							$form_contents .= form_dropdown ( $fieldvalue [1], $drp_array, $fieldvalue ['value'], $extra );
							if (isset ( $fieldvalue [4] ) && $fieldvalue [4] != '') {
								$this->CI->form_validation->set_rules ( $fieldvalue [1], $fieldvalue [0], $fieldvalue [4] );
							}
							$form_contents .= '<div class="tooltips error_div pull-left no-padding" id="' . (is_array ( $fieldvalue [1] ) ? $fieldvalue [1] ['name'] : $fieldvalue [1]) . '_error_div" ><i style="color:#D95C5C; padding-left: 3px; padding-top: 10px;" class="fa fa-exclamation-triangle"></i>';
							$form_contents .= '<span class="popup_error error  no-padding" id="' . (is_array ( $fieldvalue [1] ) ? $fieldvalue [1] ['name'] : $fieldvalue [1]) . '_error">
                        </span></div>';
						}
					} else if (isset ( $fieldvalue [13] ) && $fieldvalue [13] != '') {
						
						/* For multi select code */
						$str = $fieldvalue [7] . "," . $fieldvalue [8];
						
						if (isset ( $this->CI->input->post ))
							$fieldvalue ['value'] = (! $this->CI->input->post ( $fieldvalue [1] )) ? @$fieldvalue [1] : $this->CI->input->post ( $fieldvalue [1] );
						else
							$fieldvalue ['value'] = ($values) ? @$values [$fieldvalue [1]] : @$fieldvalue [1];
						
						$drp_array = call_user_func_array ( array (
								$this->CI->db_model,
								$fieldvalue [10] 
						), array (
								$str,
								$fieldvalue [9],
								$fieldvalue [11],
								$fieldvalue [12] 
						) );
						if ($fieldset_key === 'System Configuration Information') {
							$form_contents .= form_dropdown_multiselect ( $fieldvalue [1], $drp_array, '' );
						} else {
							$form_contents .= form_dropdown_multiselect ( $fieldvalue [1] . "[]", $drp_array, $fieldvalue ['value'] );
						}
						if (isset ( $fieldvalue [4] ) && $fieldvalue [4] != '') {
							$this->CI->form_validation->set_rules ( $fieldvalue [1], $fieldvalue [0], $fieldvalue [4] );
						}
						$form_contents .= '<div class="tooltips error_div pull-left no-padding" id="' . $fieldvalue [1] . '_error_div" ><i style="color:#D95C5C; padding-left: 3px; padding-top: 10px;" class="fa fa-exclamation-triangle"></i>';
						$form_contents .= '<span class="popup_error error  no-padding" id="' . $fieldvalue [1] . '_error"></span></div>';
						/* End--------------------- For multi select code */
					} else if ($fieldvalue [1] == 'INPUT') {
						
						if (isset ( $this->CI->input->post ))
							$fieldvalue [2] ['value'] = (! $this->CI->input->post ( $fieldvalue [2] ['name'] )) ? $fieldvalue [2] ['value'] : $this->CI->input->post ( $fieldvalue [2] ['name'] );
						else {
							$fieldvalue [2] ['value'] = ($values) ? (isset ( $values [$fieldvalue [2] ['name']] ) ? $values [$fieldvalue [2] ['name']] : '') : (isset ( $fieldvalue [2] ['value'] ) ? $fieldvalue [2] ['value'] : '');
						}
						$form_contents .= form_input ( $fieldvalue [2], 'readonly' );
						if (isset ( $fieldvalue [6] ) && ! empty ( $fieldvalue [6] )) {
							$form_contents .= $fieldvalue [6];
						}
						$this->CI->form_validation->set_rules ( $fieldvalue [2] ['name'], $fieldvalue [0], $fieldvalue [3] );
						$form_contents .= '<div class="tooltips error_div pull-left no-padding" id="' . $fieldvalue [2] ['name'] . '_error_div" ><i style="color:#D95C5C; padding-left: 3px; padding-top: 10px;" class="fa fa-exclamation-triangle"></i>';
						$form_contents .= '<span class="popup_error error  no-padding" id="' . $fieldvalue [2] ['name'] . '_error">
                    </span></div>';
					} 					/*
					 * Image upload from invoice configuration code.
					 */
					else if ($fieldvalue [1] == 'IMAGE') {
						if (isset ( $this->CI->input->post ))
							$fieldvalue [2] ['value'] = (! $this->CI->input->post ( $fieldvalue [2] ['name'] )) ? @$fieldvalue [2] ['value'] : $this->CI->input->post ( $fieldvalue [2] ['name'] );
						
						else
							$fieldvalue [2] ['value'] = ($values) ? @$values [$fieldvalue [2] ['name']] : @$fieldvalue [2] ['value'];
						$fieldvalue [2] ['style'] = isset ( $fieldvalue [2] ['style'] ) ? $fieldvalue [2] ['style'] : "";
						$form_contents .= form_image ( $fieldvalue [2], 'readonly', $fieldvalue [2] ['style'] );
						$form_contents .= @$fieldvalue [6];
						$this->CI->form_validation->set_rules ( $fieldvalue [2] ['name'], $fieldvalue [0], $fieldvalue [3] );
						$form_contents .= '<div class="tooltips error_div pull-left no-padding" id="' . $fieldvalue [2] ['name'] . '_error_div" ><i style="color:#D95C5C; padding-left: 3px; padding-top: 10px;" class="fa fa-exclamation-triangle"></i>';
						$form_contents .= '<span class="popup_error error  no-padding" id="' . $fieldvalue [2] ['name'] . '_error">
                    </span></div>';
					} else if ($fieldvalue [1] == 'DEL_BUTTON') {
						if (isset ( $this->CI->input->post ))
							$fieldvalue [2] ['value'] = (! $this->CI->input->post ( $fieldvalue [2] ['name'] )) ? @$fieldvalue [2] ['value'] : $this->CI->input->post ( $fieldvalue [2] ['name'] );
						
						else
							$fieldvalue [2] ['value'] = ($values) ? @$values [$fieldvalue [2] ['name']] : @$fieldvalue [2] ['value'];
						$fieldvalue [2] ['style'] = isset ( $fieldvalue [2] ['style'] ) ? $fieldvalue [2] ['style'] : "";
						$form_contents .= form_img_delete ( $fieldvalue [2], 'readonly', $fieldvalue [2] ['style'] );
						$form_contents .= @$fieldvalue [6];
						$this->CI->form_validation->set_rules ( $fieldvalue [2] ['name'], $fieldvalue [0], $fieldvalue [3] );
						$form_contents .= '<div class="tooltips error_div pull-left no-padding" id="' . $fieldvalue [2] ['name'] . '_error_div" ><i style="color:#D95C5C; padding-left: 3px; padding-top: 10px;" class="fa fa-exclamation-triangle"></i>';
						$form_contents .= '<span class="popup_error error  no-padding" id="' . $fieldvalue [2] ['name'] . '_error">
                    </span></div>';
					} /**
					 * *******************************************************************************
					 */
					else if ($fieldvalue [1] == 'PASSWORD') {
						if (isset ( $this->CI->input->post ))
							$fieldvalue [2] ['value'] = (! $this->CI->input->post ( $fieldvalue [2] ['name'] )) ? @$fieldvalue [2] ['value'] : $this->CI->input->post ( $fieldvalue [2] ['name'] );
						else
							$fieldvalue [2] ['value'] = ($values) ? @$values [$fieldvalue [2] ['name']] : @$fieldvalue [2] ['value'];
						$form_contents .= form_password ( $fieldvalue [2] );
						$this->CI->form_validation->set_rules ( $fieldvalue [2] ['name'], $fieldvalue [0], $fieldvalue [3] );
						$form_contents .= '<div class="tooltips error_div pull-left no-padding" id="' . $fieldvalue [2] ['name'] . '_error_div" ><i style="color:#D95C5C; padding-left: 3px; padding-top: 10px;" class="fa fa-exclamation-triangle"></i>';
						$form_contents .= '<span class="popup_error error  no-padding" id="' . $fieldvalue [2] ['name'] . '_error">
                    </span></div>';
					} else if ($fieldvalue [2] == 'CHECKBOX') {
						$OptionArray = array ();
						
						if (isset ( $fieldvalue [7] ) && $fieldvalue [7] != '')
							$OptionArray = call_user_func_array ( array (
									$this->CI->common,
									$fieldvalue [7] 
							), array (
									$fieldvalue [6] 
							) );
						if (isset ( $this->CI->input->post )) {
							$fieldvalue [3] ['value'] = (! $this->CI->input->post ( $fieldvalue [1] )) ? @$fieldvalue [3] ['value'] : $this->CI->input->post ( $fieldvalue [1] );
						} else {
							$fieldvalue [3] ['value'] = ($values) ? (isset ( $values [$fieldvalue [1]] ) && $values [$fieldvalue [1]] ? 1 : 0) : @$fieldvalue [3] ['value'];
						}
						if ($fieldvalue [3] ['value'] == "1") {
							$checked = true;
						} else {
							$checked = false;
						}
						;
						if (isset ( $fieldvalue [3] ['table_name'] ) && $fieldvalue [3] ['table_name'] != "") {
							
							$form_contents .= form_checkbox ( $fieldvalue [1], $fieldvalue [3] ['value'], $checked, $OptionArray );
						} else {
							$form_contents .= form_checkbox ( $fieldvalue [1], $fieldvalue [3] ['value'], $checked, $OptionArray );
						}
					} else if ($fieldvalue [1] == 'TEXTAREA') {
						
						if (isset ( $this->CI->input->post ))
							$fieldvalue [2] ['value'] = (! $this->CI->input->post ( $fieldvalue [2] ['name'] )) ? @$fieldvalue [2] ['value'] : $this->CI->input->post ( $fieldvalue [2] ['name'] );
						else
							$fieldvalue [2] ['value'] = ($values) ? $values [$fieldvalue [2] ['name']] : @$fieldvalue [2] ['value'];
						$form_contents .= form_textarea ( $fieldvalue [2] );
					} else if ($fieldvalue [2] == 'RADIO') {
						
						$form_contents .= form_radio ( $fieldvalue [1], $fieldvalue [3] ['value'], $fieldvalue [3] ['checked'] );
					}
					$form_contents .= '</li>';
				}
			}
			$form_contents .= '</ul>';
			$form_contents .= '</div>';
			$form_contents .= '</div>';
			$i ++;
		}
		
		$form_contents .= '<center><div class="col-md-12 margin-t-20 margin-b-20">';
		
		$form_contents .= form_button ( $save );
		
		if (isset ( $cancel )) {
			$form_contents .= form_button ( $cancel );
		}
		$form_contents .= '</center></div>';
		$form_contents .= form_fieldset_close ();
		$form_contents .= form_close ();
		$form_contents .= '</div>';
		
		return $form_contents;
	}
	function build_serach_form($fields_array) {
		$form_contents = '';
		$form_contents .= '<div>';
		$form_contents .= form_open ( $fields_array ['forms'] [0], $fields_array ['forms'] [1] );
		unset ( $fields_array ['forms'] );
		$button_array = array ();
		/**
		 * *****
		 * Batch Delete
		 * *****
		 */
		if (isset ( $fields_array ['button_search'] ) || isset ( $fields_array ['button_reset'] ) || isset ( $fields_array ['button_search_delete'] ) || isset ( $fields_array ['display_in'] )) {
			$save = $fields_array ['button_search'];
			unset ( $fields_array ['button_search'] );
			if ($fields_array ['button_reset']) {
				$cancel = $fields_array ['button_reset'];
				unset ( $fields_array ['button_reset'] );
			}
			$button_search_delete = '';
			if (isset ( $fields_array ['button_search_delete'] )) {
				$button_search_delete = $fields_array ['button_search_delete'];
				unset ( $fields_array ['button_search_delete'] );
			}
			if (isset ( $fields_array ['display_in'] )) {
				$display_in = $fields_array ['display_in'];
				unset ( $fields_array ['display_in'] );
			}
		}
		/**
		 * ***********************
		 */
		$i = 1;
		foreach ( $fields_array as $fieldset_key => $form_fileds ) {
			
			$form_contents .= '<ul class="padding-15">';
			$form_contents .= form_fieldset ( gettext ( $fieldset_key ), array (
					'style' => 'font-weight:bold;' 
			), "search" );
			
			foreach ( $form_fileds as $fieldkey => $fieldvalue ) {
				if ($i == 0) {
					$form_contents .= '<li class="col-md-12">';
				}
				$form_contents .= '<div class="col-md-3 no-padding">';
				if ($fieldvalue [1] == 'HIDDEN') {
					$form_contents .= form_hidden ( $fieldvalue [2], $fieldvalue [3] );
				} else {
					$form_contents .= form_label ( gettext ( $fieldvalue [0] ), "", array (
							"class" => "search_label col-md-12 no-padding" 
					) );
				}
				if ($fieldvalue [1] == 'INPUT') {
					$form_contents .= form_input ( $fieldvalue [2] );
				}
				
				if ($fieldvalue [2] == 'SELECT' || $fieldvalue [5] == '1') {
					
					if ($fieldvalue [7] != '' && $fieldvalue [8] != '') {
						$str = $fieldvalue [7] . "," . $fieldvalue [8];
						
						$drp_array = call_user_func_array ( array (
								$this->CI->db_model,
								$fieldvalue [10] 
						), array (
								$str,
								$fieldvalue [9],
								$fieldvalue [11],
								$fieldvalue [12] 
						) );
						$form_contents .= form_dropdown_all ( $fieldvalue [1], $drp_array, '' );
					} else {
						
						if ($fieldvalue [1] == 'INPUT') {
							$fieldvalue [1] = $fieldvalue [6];
						}
						
						$drp_array = call_user_func_array ( array (
								$this->CI->common,
								$fieldvalue [10] 
						), array (
								$fieldvalue [9] 
						) );
						$form_contents .= form_dropdown_all_search ( $fieldvalue [1], $drp_array, '' );
					}
				} else if ($fieldvalue [1] == 'PASSWORD') {
					$form_contents .= form_password ( $fieldvalue [2] );
				} else if ($fieldvalue [2] == 'CHECKBOX') {
					$form_contents .= form_checkbox ( $fieldvalue [1], $fieldvalue [3] ['value'], $fieldvalue [3] ['checked'] );
				}
				$form_contents .= '</div>';
				if ($i % 5 == 0) {
					$form_contents .= '</li>';
					$i = 0;
				}
				$i ++;
			}
		}
		$form_contents .= '<div class="col-md-12 margin-t-20 margin-b-20">';
		$form_contents .= form_button ( $cancel );
		$form_contents .= form_button ( $save );
		if (! empty ( $display_in )) {
			$form_contents .= "<div class='col-md-5 pull-right'>";
			$form_contents .= "<div class='col-md-3'></div>";
			$extra_parameters ['class'] = $display_in ['label_class'];
			$extra_parameters ['style'] = $display_in ['label_style'];
			$form_contents .= form_label ( $display_in ['content'], "", $extra_parameters );
			$drp_array = call_user_func_array ( array (
					$this->CI->common,
					$display_in ['function'] 
			), array () );
			$extra_parameters ['class'] = $display_in ['dropdown_class'];
			$extra_parameters ['style'] = $display_in ['dropdown_style'];
			$form_contents .= form_dropdown_all_search ( $display_in, $drp_array, '', $extra_parameters );
			$form_contents .= "</div>";
		}
		if (isset ( $button_search_delete ) && $button_search_delete != '') {
			$form_contents .= form_button ( $button_search_delete );
		}
		$form_contents .= '<div class="col-md-12 no-padding margin-t-15" style="">
		<div class="pull-right btn-close" id="global_clearsearch_filter">' . gettext ( 'Close' ) . '</div> 
	</div>';
		$form_contents .= '</ul>';
		$form_contents .= '</div>';
		$form_contents .= form_fieldset_close ();
		$form_contents .= form_close ();
		$form_contents .= '</div>';
		
		return $form_contents;
	}
	function build_batchupdate_form($fields_array) {
		$form_contents = '';
		$form_contents .= '<div >';
		$form_contents .= form_open ( $fields_array ['forms'] [0], $fields_array ['forms'] [1] );
		unset ( $fields_array ['forms'] );
		$button_array = array ();
		if (isset ( $fields_array ['button_search'] ) || isset ( $fields_array ['button_reset'] )) {
			$save = $fields_array ['button_search'];
			unset ( $fields_array ['button_search'] );
			if ($fields_array ['button_reset']) {
				$cancel = $fields_array ['button_reset'];
				unset ( $fields_array ['button_reset'] );
			}
		}
		$i = 1;
		foreach ( $fields_array as $fieldset_key => $form_fileds ) {
			$form_contents .= '<div class="col-md-12 no-padding margin-t-12" style="margin-bottom:10px; !important">
		<div class="pull-right close" id="global_clearbatchupdate_filter">×</div></div>';
			
			$form_contents .= '<ul>';
			$form_contents .= form_fieldset ( gettext ( $fieldset_key ), array (
					'style' => 'margin-left:-22px;font-weight:bold;' 
			) );
			foreach ( $form_fileds as $fieldkey => $fieldvalue ) {
				if ($i == 0) {
					$form_contents .= '<li>';
				}
				$form_contents .= '<div class="col-md-4 no-padding">';
				if ($fieldvalue [1] == 'HIDDEN') {
					$form_contents .= form_hidden ( $fieldvalue [2], $fieldvalue [3] );
				} else {
					$form_contents .= form_label ( gettext ( $fieldvalue [0] ), "", array (
							"class" => "search_label col-md-12 no-padding" 
					) );
				}
				if ($fieldvalue [2] == 'SELECT' || $fieldvalue [5] == '1') {
					if ($fieldvalue [7] != '' && $fieldvalue [8] != '') {
						$str = $fieldvalue [7] . "," . $fieldvalue [8];
						if (is_array ( $fieldvalue [13] )) {
							$drp_array = call_user_func_array ( array (
									$this->CI->common,
									$fieldvalue [14] 
							), array (
									$fieldvalue [13] 
							) );
							$form_contents .= form_dropdown ( $fieldvalue [13], $drp_array, '' );
						}
						/**
						 * ASTPP 3.0
						 * Reseller Batch Update
						 */
						if ($fieldvalue [10] == 'set_status') {
							$drp_array = array (
									'0' => 'Active',
									'1' => 'Inactive' 
							);
						} /**
						 * *********************************************************
						 */
						else {
							$drp_array = call_user_func_array ( array (
									$this->CI->db_model,
									$fieldvalue [10] 
							), array (
									$str,
									$fieldvalue [9],
									$fieldvalue [11],
									$fieldvalue [12] 
							) );
						}
						$form_contents .= form_dropdown_all ( $fieldvalue [1], $drp_array, '' );
					} else {
						if ($fieldvalue [1] == 'INPUT') {
							$drp_name = $fieldvalue [6];
						}
						$drp_array = call_user_func_array ( array (
								$this->CI->common,
								$fieldvalue [10] 
						), array (
								$fieldvalue [9] 
						) );
						$form_contents .= form_dropdown ( $drp_name, $drp_array, '' );
					}
				}
				if ($fieldvalue [1] == 'INPUT') {
					$form_contents .= form_input ( $fieldvalue [2] );
				} else if ($fieldvalue [2] == 'CHECKBOX') {
					$form_contents .= form_checkbox ( $fieldvalue [1], $fieldvalue [3] ['value'], $fieldvalue [3] ['checked'] );
				}
				$form_contents .= '</div>';
				if ($i % 5 == 0) {
					$form_contents .= '</li>';
					$i = 0;
				}
				$i ++;
			}
		}
		
		$form_contents .= '</ul>';
		$form_contents .= '<div class="col-md-12 margin-t-20 margin-b-20">';
		
		$form_contents .= form_button ( $cancel );
		$form_contents .= form_button ( $save );
		
		$form_contents .= form_fieldset_close ();
		$form_contents .= form_close ();
		$form_contents .= '</div>';
		$form_contents .= '</div>';
		
		return $form_contents;
	}
	function load_grid_config($count_all, $rp, $page) {
		$json_data = array ();
		$config ['total_rows'] = $count_all;
		$config ['per_page'] = $rp;
		
		$page_no = $page;
		$json_data ["json_paging"] ['page'] = $page_no;
		
		$json_data ["json_paging"] ['total'] = $config ['total_rows'];
		$perpage = $config ['per_page'];
		$start = ($page_no - 1) * $perpage;
		if ($start < 0)
			$start = 0;
		$json_data ["paging"] ['start'] = $start;
		$json_data ["paging"] ['page_no'] = $perpage;
		return $json_data;
	}
	function build_grid($query, $grid_fields) {
		$jsn_tmp = array ();
		$json_data = array ();
		if ($query->num_rows () > 0) {
			foreach ( $query->result_array () as $row ) {
				/*
				 * ASTPP 3.0
				 * For Edit on Account number or name
				 */
				$row_id = isset ( $row ['id'] ) ? $row ["id"] : '';
				/**
				 * **************************
				 */
				foreach ( $grid_fields as $field_key => $field_arr ) {
					/**
					 * ASTPP 3.0
					 * For Edit on Account number or name
					 * *
					 */
					$Actionkey = array_search ( 'Action', $this->CI->common->array_column ( $grid_fields, 0 ) );
					if ($Actionkey == '') {
						$Actionkey = array_search ( 'action', $this->CI->common->array_column ( $grid_fields, 0 ) );
					}
					if ($Actionkey == '') {
						$Actionkey = array_search ( 'Acción', $this->CI->common->array_column ( $grid_fields, 0 ) );
					}
					if ($Actionkey == '') {
						$Actionkey = array_search ( 'Ação', $this->CI->common->array_column ( $grid_fields, 0 ) );
					}
					/**
					 * ******************************
					 */
					if ($field_arr [2] != "") {
						if ($field_arr [3] != "") {
							if ($field_arr [2] == "status") {
								$row ['id'] = $row_id;
								$jsn_tmp [$field_key] = call_user_func_array ( array (
										$this->CI->common,
										$field_arr [5] 
								), array (
										$field_arr [3],
										$field_arr [4],
										$row 
								) );
							} else {
								$jsn_tmp [$field_key] = call_user_func_array ( array (
										$this->CI->common,
										$field_arr [5] 
								), array (
										$field_arr [3],
										$field_arr [4],
										$row [$field_arr [2]] 
								) );
							}
							
							/**
							 * ASTPP 3.0
							 * For Edit on Account number or name
							 * *
							 */
							$row [$field_arr [2]] = $jsn_tmp [$field_key];
						}
						if (array_search ( "EDITABLE", $field_arr )) {
							$ActionArr = $grid_fields [$Actionkey];
							if ($ActionArr [5]->EDIT->url == "accounts/customer_edit/" || $ActionArr [5]->EDIT->url == "accounts/provider_edit/") {
								$ActionArr [5]->EDIT->url = $row ['type'] == 0 ? "accounts/customer_edit/" : "accounts/provider_edit/";
							}
							if ($ActionArr [5]->EDIT->url == "accounts/admin_edit/" || $ActionArr [5]->EDIT->url == "accounts/subadmin_edit/") {
								$ActionArr [5]->EDIT->url = $row ['type'] == 4 ? "accounts/subadmin_edit/" : "accounts/admin_edit/";
							}
							$acctype = "";
							if (isset ( $row ["type"] ) && ($row ["type"] == '0' || $row ["type"] == '1' || $row ["type"] == '3')) {
								$acctype = (isset ( $row ["posttoexternal"] ) && $row ["posttoexternal"] != '') ? "<span class='label label-default pull-right'>" . $this->CI->common->get_account_type ( "", "", $row ["posttoexternal"] ) . "</span>" : "";
							}
							
							$fieldstr = $this->CI->common->build_custome_edit_button ( $ActionArr [5]->EDIT, $row [$field_arr [2]], $row ["id"] );
							if ($acctype != '') {
								$jsn_tmp [$field_key] = $fieldstr . "<br/>" . $acctype;
							} else {
								$jsn_tmp [$field_key] = $fieldstr;
							}
						
						/**
						 * ******************************
						 */
						} else {
							$jsn_tmp [$field_key] = $row [$field_arr [2]];
						}
					} else {
						if ($field_arr [0] == gettext ( "Action" )) {
							if (isset ( $field_arr [5] ) && isset ( $field_arr [5]->EDIT ) && isset ( $field_arr [5]->DELETE )) {
								
								if ($field_arr [5]->EDIT->url == 'accounts/customer_edit/' || $field_arr [5]->EDIT->url == 'accounts/provider_edit/' || $field_arr [5]->DELETE->url == 'accounts/provider_delete/' || $field_arr [5]->DELETE->url == 'accounts/customer_delete/') {
									if ($row ['type'] == '0' || strtolower ( $row ['type'] ) == 'customer') {
										$field_arr [5]->EDIT->url = 'accounts/customer_edit/';
										$field_arr [5]->DELETE->url = 'accounts/customer_delete/';
									}
									if ($row ['type'] == 3 || strtolower ( $row ['type'] ) == 'provider') {
										$field_arr [5]->EDIT->url = 'accounts/provider_edit/';
										$field_arr [5]->DELETE->url = 'accounts/provider_delete/';
									}
								}
								if ($field_arr [5]->EDIT->url == 'accounts/admin_edit/' || $field_arr [5]->EDIT->url == 'accounts/subadmin_edit/' || $field_arr [5]->DELETE->url == 'accounts/admin_delete/' || $field_arr [5]->DELETE->url == 'accounts/subadmin_delete/') {
									if ($row ['type'] == 2 || strtolower ( $row ['type'] ) == 'administrator') {
										$field_arr [5]->EDIT->url = 'accounts/admin_edit/';
										$field_arr [5]->DELETE->url = 'accounts/admin_delete/';
									}
									if ($row ['type'] == 4 || strtolower ( $row ['type'] ) == 'sub admin') {
										$field_arr [5]->EDIT->url = 'accounts/subadmin_edit/';
										$field_arr [5]->DELETE->url = 'accounts/subadmin_delete/';
									}
								}
							}
							/*
							 * ASTPP 3.0
							 * For edit on account number or name
							 */
							$jsn_tmp [$field_key] = $this->CI->common->get_action_buttons ( $field_arr [5], $row_id );
						/**
						 * *************************************************************************
						 */
						} elseif ($field_arr [0] == gettext ( "Profile Action" )) {
							if (isset ( $field_arr [5] ) && isset ( $field_arr [5]->START ) && isset ( $field_arr [5]->STOP ) && isset ( $field_arr [5]->RELOAD ) && isset ( $field_arr [5]->RESCAN )) {
							}
							$jsn_tmp [$field_key] = $this->CI->common->get_action_buttons ( $field_arr [5], $row ["id"] );
						} else {
							$className = (isset ( $field_arr ['9'] ) && $field_arr ['9'] != '') ? $field_arr ['9'] : "chkRefNos";
							
							$jsn_tmp [$field_key] = '<input type="checkbox" name="chkAll" id=' . $row ['id'] . ' class="ace ' . $className . '" onclick="clickchkbox(' . $row ['id'] . ')" value=' . $row ['id'] . '><lable class="lbl"></lable>';
						}
					}
				}
				$json_data [] = array (
						'cell' => $jsn_tmp 
				);
			}
		}
		return $json_data;
	}
	function build_json_grid($query, $grid_fields) {
		$jsn_tmp = array ();
		$json_data = array ();
		foreach ( $query as $row ) {
			foreach ( $grid_fields as $field_key => $field_arr ) {
				$row_id = isset ( $row ['id'] ) ? $row ["id"] : '';
				/**
				 * ASTPP 3.0
				 * For Edit on Account number or name
				 * *
				 */
				$Actionkey = array_search ( 'Action', $this->CI->common->array_column ( $grid_fields, 0 ) );
				/**
				 * ****************************
				 */
				
				if ($field_arr [2] != "") {
					if ($field_arr [3] != "") {
						if ($field_arr [2] == "status") {
							$row ['id'] = $row_id;
							$jsn_tmp [$field_key] = call_user_func_array ( array (
									$this->CI->common,
									$field_arr [5] 
							), array (
									$field_arr [3],
									$field_arr [4],
									$row 
							) );
						} else {
							$jsn_tmp [$field_key] = call_user_func_array ( array (
									$this->CI->common,
									$field_arr [5] 
							), array (
									$field_arr [3],
									$field_arr [4],
									$row [$field_arr [2]] 
							) );
						}
						/**
						 * ASTPP 3.0
						 * For Edit on Account number or name
						 * *
						 */
						$row [$field_arr [2]] = $jsn_tmp [$field_key];
					}
					if (array_search ( "EDITABLE", $field_arr )) {
						$ActionArr = $grid_fields [$Actionkey];
						if ($ActionArr [5]->EDIT->url == "accounts/customer_edit/" || $ActionArr [5]->EDIT->url == "accounts/provider_edit/") {
							$ActionArr [5]->EDIT->url = $row ['type'] == 0 ? "accounts/customer_edit/" : "accounts/provider_edit/";
						}
						if ($ActionArr [5]->EDIT->url == "accounts/admin_edit/" || $ActionArr [5]->EDIT->url == "accounts/subadmin_edit/") {
							$ActionArr [5]->EDIT->url = $row ['type'] == 4 ? "accounts/subadmin_edit/" : "accounts/admin_edit/";
						}
						$jsn_tmp [$field_key] = $this->CI->common->build_custome_edit_button ( $ActionArr [5]->EDIT, $row [$field_arr [2]], $row ["id"] );
					/**
					 * ****************************
					 */
					} else {
						$jsn_tmp [$field_key] = isset ( $row [$field_arr [2]] ) ? $row [$field_arr [2]] : "";
					}
				} else {
					if ($field_arr [0] == "Action") {
						$jsn_tmp [$field_key] = $this->CI->common->get_action_buttons ( $field_arr [5], $row ["id"] );
					} else {
						$jsn_tmp [$field_key] = '<input type="checkbox" name="chkAll" id=' . $row ['id'] . ' class="ace chkRefNos" onclick="clickchkbox(' . $row ['id'] . ')" value=' . $row ['id'] . '><lable class="lbl"></lable>';
					}
				}
			}
			$json_data [] = array (
					'cell' => $jsn_tmp 
			);
		}
		return $json_data;
	}
}
