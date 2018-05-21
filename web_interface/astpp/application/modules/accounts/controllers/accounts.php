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
class Accounts extends MX_Controller {
	function Accounts() {
		parent::__construct ();
		$this->load->helper ( 'template_inheritance' );

		$this->load->library ( 'accounts_form' );
		$this->load->library ( 'astpp/form' );
		$this->load->library ( 'astpp/permission' );
		$this->load->model ( 'common_model' );
		$this->load->library ( 'session' );
		$this->load->helper ( 'form' );
		$this->load->model ( 'accounts_model' );
		$this->load->model ( 'Astpp_common' );
		$this->protected_pages = array (
				'account_list'
		);
		if ($this->session->userdata ( 'user_login' ) == FALSE)
			redirect ( base_url () . '/login/login' );
	}
	function customer_list() {
		$data ['username'] = $this->session->userdata ( 'user_name' );
		$data ['page_title'] = gettext ( 'Customers' );
		$data ['search_flag'] = true;
		/**
		 * ASTPP 3.0
		 * Customer Batch Update.
		 */
		$data ['batch_update_flag'] = true;
		/* * *********************************** */
		$this->session->set_userdata ( 'advance_search', 0 );
		$data ['grid_fields'] = $this->accounts_form->build_account_list_for_customer ();
		$data ["grid_buttons"] = $this->accounts_form->build_grid_buttons_customer ();
		$data ['form_search'] = $this->form->build_serach_form ( $this->accounts_form->get_search_customer_form () );
		/**
		 * ASTPP 3.0
		 * Customer Batch Update.
		 */

		$data ['form_batch_update'] = $this->form->build_batchupdate_form ( $this->accounts_form->customer_batch_update_form () );
		/* * *****************************************************************************************8 */
		$this->load->view ( 'view_accounts_list', $data );
	}

	/**
	 * -------Here we write code for controller accounts functions account_list------
	 * Listing of Accounts table data through php function json_encode
	 */
	function customer_list_json() {
		$json_data = array ();
		$count_all = $this->accounts_model->get_customer_Account_list ( false );
		$paging_data = $this->form->load_grid_config ( $count_all, $_GET ['rp'], $_GET ['page'] );
		$json_data = $paging_data ["json_paging"];
		$query = $this->accounts_model->get_customer_Account_list ( true, $paging_data ["paging"] ["start"], $paging_data ["paging"] ["page_no"] );
		$grid_fields = json_decode ( $this->accounts_form->build_account_list_for_customer () );
		$json_data ['rows'] = $this->form->build_grid ( $query, $grid_fields );
		echo json_encode ( $json_data );
	}
	function customer_list_search() {
		$ajax_search = $this->input->post ( 'ajax_search', 0 );

		if ($this->input->post ( 'advance_search', TRUE ) == 1) {
			$this->session->set_userdata ( 'advance_search', $this->input->post ( 'advance_search' ) );
			$action = $this->input->post ();
			unset ( $action ['action'] );
			unset ( $action ['advance_search'] );
			if (isset ( $action ['balance'] ['balance'] ) && $action ['balance'] ['balance'] != '') {
				$action ['balance'] ['balance'] = $this->common_model->add_calculate_currency ( $action ['balance'] ['balance'], "", '', false, false );
			}
			if (isset ( $action ['credit_limit'] ['credit_limit'] ) && $action ['credit_limit'] ['credit_limit'] != '') {
				$action ['credit_limit'] ['credit_limit'] = $this->common_model->add_calculate_currency ( $action ['credit_limit'] ['credit_limit'], "", '', false, false );
			}
			$this->session->set_userdata ( 'customer_list_search', $action );
		}
		if (@$ajax_search != 1) {
			redirect ( base_url () . 'accounts/customer_list/' );
		}
	}
	function customer_list_clearsearchfilter() {
		$this->session->set_userdata ( 'advance_search', 0 );
		$this->session->set_userdata ( 'customer_list_search', "" );
	}
	function customer_export_cdr_xls() {
		$account_info = $accountinfo = $this->session->userdata ( 'accountinfo' );
		$currency_id = $account_info ['currency_id'];
		$currency = $this->common->get_field_name ( 'currency', 'currency', $currency_id );
		$query = $this->accounts_model->get_customer_Account_list ( true, '', '', true );
		ob_clean ();
		$customer_array [] = array (
				gettext ( "Account" ),
				gettext ( "First Name" ),
				gettext ( "Last Name" ),
				gettext ( "Company" ),
				gettext ( "Rate Group" ),
				gettext ( "Balance" ) . "(" . $currency . ")",
				gettext ( "Credit Limit" ) . "(" . $currency . ")",
				gettext ( "First Used" ),
				gettext ( "Expiry Date" ),
				gettext ( "CC" ),
				gettext ( "Status" ),
				gettext ( "Created Date" )
		);
		if ($query->num_rows () > 0) {

			foreach ( $query->result_array () as $row ) {
				$customer_array [] = array (
						$row ['number'],
						$row ['first_name'],
						$row ['last_name'],
						$row ['company_name'],
						$this->common->get_field_name ( 'name', 'pricelists', $row ['pricelist_id'] ),
						$this->common_model->calculate_currency_customer ( $row ['balance'] ),
						$this->common_model->calculate_currency_customer ( $row ['credit_limit'] ),
						$row ['first_used'],
						$row ['expiry'],
						$row ['maxchannels'],
						$this->common->get_status ( 'export', '', $row ['status'] ),
						$row ['creation']
				);
			}
		}
		$this->load->helper ( 'csv' );
		array_to_csv ( $customer_array, 'Customers_' . date ( "Y-m-d" ) . '.csv' );
	}
	function provider_add() {
		$this->customer_add ( 3 );
	}
	function provider_edit($edit_id = '') {
		$this->customer_edit ( $edit_id );
	}
	function provider_save() {
		$add_array = $this->input->post ();
		$this->customer_save ( $add_array );
	}
	function customer_add($type = 0) {		
		$accountinfo = $this->session->userdata ( 'accountinfo' );
		$entity_type = strtolower ( $this->common->get_entity_type ( '', '', $type ) );
		$data ['username'] = $this->session->userdata ( 'user_name' );
		$data ['flag'] = 'create';
		$data ['page_title'] = ($type == 3)?gettext ( 'Create Provider' ):gettext ( 'Create Customer' );
		$data ['back_flag'] = true;
		$data ['country_id'] = $accountinfo ['country_id'];
		$data ['callingcard'] = Common_model::$global_config ['system_config'] ['pinlength'];
		$data ['currency_id'] = $accountinfo ['currency_id'];
		$data ['timezone_id'] = $accountinfo ['timezone_id'];
		$data ['form'] = $this->form->build_form ( $this->accounts_form->get_customer_form_fields ( $entity_type ), '' );
		if (! $data ['timezone_id']) {
			$data ['timezone_id'] = 1;
		}
		if (! $data ['currency_id']) {
			$data ['currency_id'] = 1;
		}
		if (! $data ['country_id']) {
			$data ['country_id'] = 1;
		}
		$data ['entity_name'] = $entity_type;
		$this->load->view ( 'view_accounts_create', $data );
	}
	function customer_edit($edit_id = '') {
		// Get Account information from session.
		$accountinfo = $this->session->userdata ( 'accountinfo' );
		// Get Parent informartion
		$reseller_id = ($accountinfo ['type'] == 1 || $accountinfo ['type'] == 5) ? $accountinfo ['id'] : 0;
		$where = array (
				'id' => $edit_id,
				"reseller_id" => $reseller_id
		);
		$account = $this->db_model->getSelect ( "*", "accounts", $where );
		if ($account->num_rows () > 0) {
			$account_data = ( array ) $account->first_row ();
			$entity_name = strtolower ( $this->common->get_entity_type ( '', '', $account_data ['type'] ) );
			$data ['page_title'] = gettext ( ucfirst ( $entity_name ) . " Profile" );
			$data ['invoice_date'] = $account_data ['invoice_day'];
			$data ["account_data"] = $account_data;
			$data ['callingcard'] = Common_model::$global_config ['system_config'] ['pinlength'];
			$data ['entity_name'] = $entity_name;
			// Purpose : Get assigned taxes value for customer
			$taxes_data = $this->db_model->getSelect ( "group_concat(taxes_id) as taxes_id", "taxes_to_accounts", array (
					"accountid" => $edit_id
			) );
			if (isset ( $taxes_data ) && $taxes_data->num_rows () > 0) {
				$taxes_data = $taxes_data->result_array ();
				$account_data ["tax_id"] = explode ( ",", $taxes_data [0] ['taxes_id'] );
			}
			/*
			 * ***
			 * ASTPP 3.0
			 * Password decode
			 * ****
			 */
			$account_data ['password'] = $this->common->decode ( $account_data ['password'] );
			/* * ********************** */
			$data ['form'] = $this->form->build_form ( $this->accounts_form->get_customer_form_fields ( $entity_name, $edit_id ), $account_data );
			$data ['edit_id'] = $edit_id;
			$this->load->view ( 'view_customer_details', $data );
		} else {
			$this->session->set_flashdata ( 'astpp_notification','Permission Denied!');
			redirect ( base_url () . 'accounts/customer_list/' );
		}
	}
	function customer_save($add_array = false) {
		$add_array = $this->input->post ();
		$entity_name = strtolower ( $this->common->get_entity_type ( '', '', $add_array ['type'] ) );
		$data ['country_id'] = $add_array ['country_id'];
		$data ['timezone_id'] = $add_array ['timezone_id'];
		$data ['currency_id'] = $add_array ['currency_id'];
		$data ['entity_name'] = $entity_name;
		$data ['callingcard'] = Common_model::$global_config ['system_config'] ['pinlength'];
		$data ['edit_id'] = $add_array ['id'];
		$data ['form'] = $this->form->build_form ( $this->accounts_form->get_customer_form_fields ( $entity_name, $add_array ['id'] ), $add_array );
		if ($add_array ['id'] != '') {
			$data ['page_title'] = gettext ( 'Edit ' . $this->common->get_entity_type ( '', '', $add_array ['type'] ) );
			if ($this->form_validation->run () == FALSE) {
				$data ['validation_errors'] = validation_errors ();
			} else {
				/*
				 * ****
				 * ASTPP 3.0
				 * Password encode
				 * ****
				 */
				$add_array ['password'] = $this->common->encode ( $add_array ['password'] );
				/* * ****************** */
				$add_array ['credit_limit'] = $this->common_model->add_calculate_currency ( $add_array ['credit_limit'], '', '', false, false );
				// Purpose : Get assigned taxes value for customer

				$query = $this->accounts_model->remove_all_account_tax ( $add_array ['id'] );
				if (isset ( $add_array ['tax_id'] )) {
					foreach ( $add_array ['tax_id'] as $key => $val ) {
						$data1 = array (
								'accountid' => $add_array ['id'],
								'taxes_id' => $val
						);
						$this->accounts_model->add_account_tax ( $data1 );
					}
					unset ( $add_array ['tax_id'] );
				}
				// Completed
				unset ( $add_array ['posttoexternal'], $add_array ['number'], $add_array ['balance'] );
				$this->accounts_model->edit_account ( $add_array, $add_array ['id'] );
				$this->session->set_flashdata ( 'astpp_errormsg', ucfirst ( $entity_name ) . ' updated successfully!' );
				redirect ( base_url () . 'accounts/customer_list/' );
				exit ();
			}
			$data ["account_data"] ["0"] = $add_array;
			$this->load->view ( 'view_customer_details', $data );
		} else {
			$data ['page_title'] = gettext ( 'Create ' . $entity_name );
			if ($this->form_validation->run () == FALSE) {
				$data ['validation_errors'] = validation_errors ();
			} else {
				/*
				 * ****
				 * ASTPP 3.0
				 * Password encode
				 * ****
				 */
				$add_array ['password'] = $this->common->encode ( $add_array ['password'] );
				/* * ****************** */
				$add_array ['credit_limit'] = $this->common_model->add_calculate_currency ( $add_array ['credit_limit'], '', '', false, false );
				$add_array ['balance'] = $this->common_model->add_calculate_currency ( $add_array ['balance'], '', '', false, false );
				if ($add_array ['posttoexternal'] == 1) {
					$add_array ['balance'] = 0;
				}
				$last_id = $this->accounts_model->add_account ( $add_array );

				// Enter detail into invoices , invoices_details , payments
				if ($add_array ['balance'] > 0 && $add_array ['posttoexternal'] == 0) {
					$post_array ["account_currency"] = $this->common->get_field_name ( 'currency', 'currency', $add_array ["currency_id"] );
					$post_array ['id'] = $last_id;
					$post_array ['accountid'] = $add_array ['number'];
					$post_array ['credit'] = $add_array ['balance'];
					$post_array ['payment_type'] = 0;
					$post_array ['notes'] = 'Initial Credit';
					$response = $this->accounts_model->account_process_payment ( $post_array, 'false' );
					$accountinfo = $this->accounts_model->get_account_by_number ( $post_array ['id'] );
					$reseller_id = $accountinfo ['type'] == 1 ? $accountinfo ['id'] : 0;
					$where = "accountid IN ('" . $reseller_id . "','1')";
					$this->db->where ( $where );
					$this->db->select ( '*' );
					$this->db->order_by ( 'accountid', 'desc' );
					$this->db->limit ( 1 );
					$invoiceconf = $this->db->get ( 'invoice_conf' );
					$invoice_conf = ( array ) $invoiceconf->first_row ();
					$last_invoice_ID = $this->get_invoice_date ( "invoiceid", $accountinfo ["id"] );
					$invoice_prefix = $invoice_conf ['invoice_prefix'];
					$due_date = gmdate ( "Y-m-d H:i:s", strtotime ( gmdate ( "Y-m-d H:i:s" ) . " +" . $invoice_conf ['interval'] . " days" ) );

					$this->load->module ( 'invoices/invoices' );
					$invoice_id = $this->invoices->invoices->generate_receipt ( $post_array ['id'], $post_array ['credit'], $accountinfo, $last_invoice_ID + 1, $invoice_prefix, $due_date );

					$account_balance = $this->common->get_field_name ( 'balance', 'accounts', $post_array ['id'] );
					$insert_arr = array (
							"accountid" => $post_array ['id'],
							"description" => trim ( $post_array ['notes'] ),
							"debit" => '0',
							"credit" => $post_array ['credit'],
							"created_date" => gmdate ( "Y-m-d H:i:s" ),
							"invoiceid" => $invoice_id,
							"reseller_id" => '0',
							"item_type" => 'Refill',
							"item_id" => '0',
							'before_balance' => 0,
							'after_balance' => $account_balance
					);
					$this->db->insert ( "invoice_details", $insert_arr );
				}
				// end
				if (isset ( $add_array ['tax_id'] )) {
					foreach ( $add_array ['tax_id'] as $key => $val ) {
						$data1 = array (
								'accountid' => $last_id,
								'taxes_id' => $val
						);
						$this->accounts_model->add_account_tax ( $data1 );
					}
					unset ( $add_array ['tax_id'] );
				}
				$this->session->set_flashdata ( 'astpp_errormsg', ucfirst ( $entity_name ) . ' added successfully!' );
				redirect ( base_url () . 'accounts/customer_list/' );
				exit ();
			}
			$this->load->view ( 'view_accounts_create', $data );
		}
	}
	function provider_speeddial($edit_id) {
		$this->customer_speeddial ( $edit_id );
	}
	function customer_speeddial($edit_id) {
		// Get Account information from session.
		$data ['page_title'] = gettext ( "Speed Dial" );
		$accountinfo = $this->session->userdata ( 'accountinfo' );
		// Get Parent informartion
		$reseller_id = ($accountinfo ['type'] == 1 || $accountinfo ['type'] == 5) ? $accountinfo ['id'] : 0;
		$where = array (
				'id' => $edit_id,
				"reseller_id" => $reseller_id
		);
		$account = $this->db_model->getSelect ( "*", "accounts", $where );

		if ($account->num_rows () > 0) {
			$account_data = ( array ) $account->first_row ();
			$accounttype = strtolower ( $this->common->get_entity_type ( '', '', $account_data ['type'] ) );
			$data ['page_title'] = gettext ( 'Speed Dial' );
			$data ['accounttype'] = $accounttype;
			$data ['invoice_date'] = $account_data ['invoice_day'];
			$data ["account_data"] = $account_data;
			$data ['edit_id'] = $edit_id;
			$speeddial_res = $this->db->get_where ( "speed_dial", array (
					"accountid" => $edit_id
			) );
			$speeddial_info = array ();
			if ($speeddial_res->num_rows () > 0) {
				$speeddial_res = $speeddial_res->result_array ();
				foreach ( $speeddial_res as $key => $value ) {
					$speeddial_info [$value ['speed_num']] = $value ['number'];
				}
			}
			$data ['speeddial'] = $speeddial_info;
			$data ['form'] = $this->form->build_form ( $this->accounts_form->get_customer_form_fields ( $accounttype, $edit_id ), $account_data );
			$this->load->view ( 'view_customer_speed_dial', $data );
		} else {
			$this->session->set_flashdata ( 'astpp_notification','Permission Denied!');
			redirect ( base_url () . 'accounts/customer_list/' );
		}
	}
	function customer_speeddial_save($number, $accountid, $speed_num) {
		$where = array (
				"accountid" => $accountid
		);
		$account = $this->db_model->getSelect ( "*", "speed_dial", $where );
		if ($account->num_rows () == 0) {
			for($i = 0; $i <= 9; $i ++) {
				$dest_number = $speed_num == $i ? $number : '';
				$data [$i] = array (
						"number" => $dest_number,
						"speed_num" => $i,
						'accountid' => $accountid
				);
			}
			$this->db->insert_batch ( 'speed_dial', $data );
		} else {
			$updateinfo = array (
					'number' => $number
			);
			$this->db->where ( 'speed_num', $speed_num );
			$this->db->where ( 'accountid', $accountid );
			$result = $this->db->update ( 'speed_dial', $updateinfo );
		}
	}
	function customer_speeddial_remove($accountid, $speed_num) {
		$updateinfo = array (
				'number' => ''
		);
		$this->db->where ( 'speed_num', $speed_num );
		$this->db->where ( 'accountid', $accountid );
		$result = $this->db->update ( 'speed_dial', $updateinfo );
	}
	function provider_ipmap($edit_id) {
		$this->customer_ipmap ( $edit_id );
	}
	function customer_ipmap($edit_id) {
		$data ['page_title'] = gettext ( "IP Settings" );
		$data ['add_form'] = true;
		// Get Account information from session.
		$accountinfo = $this->session->userdata ( 'accountinfo' );
		// Get Parent informartion
		$reseller_id = ($accountinfo ['type'] == 1 || $accountinfo ['type'] == 5) ? $accountinfo ['id'] : 0;
		$where = array (
				'id' => $edit_id,
				"reseller_id" => $reseller_id
		);
		$account_res = $this->db_model->getSelect ( "type", "accounts", $where );
		if ($account_res->num_rows () > 0) {
			$account_data = ( array ) $account_res->first_row ();
			$accounttype = strtolower ( $this->common->get_entity_type ( '', '', $account_data ['type'] ) );
			$data ["grid_fields"] = $this->accounts_form->build_ip_list_for_customer ( $edit_id, $accounttype );
			$data ['edit_id'] = $edit_id;
			$data ['accounttype'] = $accounttype;
			$this->load->view ( 'view_customer_ipmap', $data );
		} else {
			$this->session->set_flashdata ( 'astpp_notification','Permission Denied!');
			redirect ( base_url () . 'accounts/customer_list/' );
			exit ();
		}
	}
	function customer_ipmap_json($accountid, $accounttype) {
		$json_data = array ();
		$where = array (
				"accountid" => $accountid
		);
		$instant_search = $this->session->userdata ( 'left_panel_search_' . $accounttype . '_ipmap' );
		$like_str = ! empty ( $instant_search ) ? "(name like '%$instant_search%'  OR ip like '%$instant_search%' OR prefix like '%$instant_search%' OR created_date like '%$instant_search%' )" : null;
		if (! empty ( $like_str ))
			$this->db->where ( $like_str );
		$count_all = $this->db_model->countQuery ( "*", "ip_map", $where );
		$paging_data = $this->form->load_grid_config ( $count_all, $_GET ['rp'], $_GET ['page'] );
		$json_data = $paging_data ["json_paging"];
		if (! empty ( $like_str ))
			$this->db->where ( $like_str );
		$query = $this->db_model->select ( "*", "ip_map", $where, "id", "ASC", $paging_data ["paging"] ["page_no"], $paging_data ["paging"] ["start"] );
		$grid_fields = json_decode ( $this->accounts_form->build_ip_list_for_customer ( $accountid, $accounttype ) );
		$json_data ['rows'] = $this->form->build_grid ( $query, $grid_fields );

		echo json_encode ( $json_data );
	}
	function provider_ipmap_action($action, $accountid, $accounttype, $ipmapid = "") {
		$this->customer_ipmap_action ( $action, $accountid, $accounttype, $ipmapid );
	}
	function customer_ipmap_action($action, $accountid, $accounttype, $ipmapid = "") {
		$add_array = $this->input->post ();
		if ($action == "add" && !empty($add_array)) {
			if ($add_array ['ip'] != "") {
				$ip = $add_array ['ip'];
				if (strpos ( $ip, '/' ) !== false) {
					$add_array ['ip'] = $add_array ['ip'];
				} else {
					$add_array ['ip'] = $add_array ['ip'] . '/32';
				}
				$where = array (
						"ip" => trim ( $add_array ['ip'] ),
						"prefix" => trim ( $add_array ['prefix'] )
				);
				$getdata = $this->db_model->countQuery ( "*", "ip_map", $where );
				if ($getdata > 0) {
					$this->session->set_flashdata ( 'astpp_notification', 'IP already exist in system.' );
				} else {
					if ($accounttype == "provider") {
						$add_array ['pricelist_id'] = 0;
					}
					unset ( $add_array ['action'] );
					$add_array ['context'] = 'default';
					$add_array ['accountid'] = $accountid;
					$ip_flag = $this->db->insert ( "ip_map", $add_array );
					if ($ip_flag) {
						$this->load->library ( 'freeswitch_lib' );
						$this->load->module ( 'freeswitch/freeswitch' );
						$command = "api reloadacl";
						$response = $this->freeswitch_model->reload_freeswitch ( $command );
						$this->session->set_userdata ( 'astpp_notification', $response );
					}
					$this->session->set_flashdata ( 'astpp_errormsg', 'IP added sucessfully.' );
				}
			}
		}
		if ($action == "delete") {
			$ip_flag = $this->db_model->delete ( "ip_map", array (
					"id" => $ipmapid
			) );
			//if ($ip_flag) {
				$this->load->library ( 'freeswitch_lib' );
				$this->load->module ( 'freeswitch/freeswitch' );
				$this->load->model ( "freeswitch_model" );
				$command = "api reloadacl";
				$this->freeswitch_model->reload_freeswitch ( $command );
			//}
			$this->session->set_flashdata ( 'astpp_notification', 'IP removed sucessfully.' );
		}
		redirect ( base_url () . "accounts/" . $accounttype . "_ipmap/" . $accountid . "/" );
	}

	// This function using for provider t
	function provider_animap($edit_id) {
		$this->customer_animap ( $edit_id, 'provider' );
	}

	// This function using for showing data of customer/provider CallerID Tab
	function customer_animap($edit_id, $entity_type = 'customer') {
		// Get Account information from session.
		$accountinfo = $this->session->userdata ( 'accountinfo' );
		// Get Parent informartion
		$reseller_id = ($accountinfo ['type'] == 1 || $accountinfo ['type'] == 5) ? $accountinfo ['id'] : 0;
		$where = array (
				'id' => $edit_id,
				"reseller_id" => $reseller_id
		);
		$account_res = $this->db_model->getSelect ( "type", "accounts", $where );
		if ($account_res->num_rows () > 0) {
			$data ['page_title'] = gettext ( "Caller ID" );
			$account_data = ( array ) $account_res->first_row ();
			$accounttype = strtolower ( $this->common->get_entity_type ( '', '', $account_data ['type'] ) );
			$data ["grid_fields"] = $this->accounts_form->build_animap_list_for_customer ( $edit_id, $accounttype );
			$data ['edit_id'] = $edit_id;
			$data ['accounttype'] = $accounttype;
			$this->load->view ( 'view_customer_animap', $data );
		} else {
			$this->session->set_flashdata ( 'astpp_notification','Permission Denied!');
			redirect ( base_url () . 'accounts/customer_list/' );
			exit ();
		}
	}
	function customer_animap_json($accountid, $accounttype) {
		$json_data = array ();
		$instant_search = $this->session->userdata ( 'left_panel_search_' . $accounttype . '_animap' );
		$like_str = ! empty ( $instant_search ) ? "(number like '%$instant_search%'  OR  creation_date like '%$instant_search%' )" : null;
		if (! empty ( $like_str ))
			$this->db->where ( $like_str );
		$where = array (
				"accountid" => $accountid
		);
		$count_all = $this->db_model->countQuery ( "*", "ani_map", $where );

		$paging_data = $this->form->load_grid_config ( $count_all, $_GET ['rp'], $_GET ['page'] );
		$json_data = $paging_data ["json_paging"];
		if (! empty ( $like_str ))
			$this->db->where ( $like_str );
		$query = $this->db_model->select ( "*", "ani_map", $where, "id", "ASC", $paging_data ["paging"] ["page_no"], $paging_data ["paging"] ["start"] );

		$grid_fields = json_decode ( $this->accounts_form->build_animap_list_for_customer ( $accountid, $accounttype ) );
		$json_data ['rows'] = $this->form->build_grid ( $query, $grid_fields );

		echo json_encode ( $json_data );
	}
	function provider_animap_action($action, $accountid, $aniid = "") {
		$this->customer_animap_action ( $action, $accountid, $aniid );
	}

	// This function using for customer/provider callerID add/delete
	function customer_animap_action($action, $accountid, $aniid = "") {
		$entity_type = $this->common->get_field_name ( 'type', 'accounts', array (
				'id' => $accountid
		) );
		$entity_type = strtolower ( $this->common->get_entity_type ( '', '', $entity_type ) );
		$url = "accounts/" . $entity_type . "_animap/$accountid/";
		if ($action == "add") {
			$ani = $this->input->post ();
			$this->db->where ( 'number', $ani ['number'] );
			$this->db->select ( 'count(id) as count' );
			$cnt_result = $this->db->get ( 'ani_map' );
			$cnt_result = $cnt_result->result_array ();
			$count = $cnt_result [0] ['count'];
			if ($count == 0) {
				if ($ani ['number'] != "") {
					/*
					 * ASTPP 3.0
					 * Add creation field
					 */
					$insert_arr = array (
							'creation_date' => gmdate ( 'Y-m-d H:i:s' ),
							"number" => $this->input->post ( 'number' ),
							"accountid" => $accountid,
							"context" => "default"
					);
					/* * *********************************** */
					$this->db->insert ( "ani_map", $insert_arr );
					$this->session->set_flashdata ( 'astpp_errormsg', 'Caller ID added successfully!' );
				} else {
					$this->session->set_flashdata ( 'astpp_notification', 'Please Enter Caller ID value.' );
				}
			} else {
				$this->session->set_flashdata ( 'astpp_notification', ' Caller ID already Exists.' );
			}
		}
		if ($action == "delete") {
			$this->session->set_flashdata ( 'astpp_notification', 'Caller ID removed sucessfully!' );
			$this->db_model->delete ( "ani_map", array (
					"id" => $aniid
			) );
		}
		redirect ( base_url () . $url );
	}

	// This function using by Customer/Provider edit tab
	function customer_details_json($module, $accountid) {
		$entity_type = $this->common->get_field_name ( 'type', 'accounts', array (
				'id' => $accountid
		) );
		$entity_type = strtolower ( $this->common->get_entity_type ( '', '', $entity_type ) );
		if ($module == "pattern") {
			$this->load->module ( 'rates/rates' );
			$this->rates->customer_block_pattern_list ( $accountid, $entity_type );
		}
		if ($module == "freeswitch") {
			$this->load->module ( 'freeswitch/freeswitch' );
			$this->freeswitch->customer_fssipdevices_json ( $accountid, $entity_type );
		}
		if ($module == "did") {
			$this->load->module ( 'did/did' );
			$this->did->customer_did ( $accountid, $entity_type );
		}
		if ($module == "invoices") {
			$this->load->module ( 'invoices/invoices' );
			$this->invoices->customer_invoices ( $accountid, $entity_type, false );
		}
		if ($module == "subscription") {
			$this->load->module ( 'charges/charges' );
			$this->charges->customer_charge_list ( $accountid, $entity_type );
		}
		if ($module == "cdrs") {
			$this->load->module ( 'reports/reports' );
			$this->reports->customer_cdrreport ( $accountid, $entity_type );
		}
		if ($module == "charges") {
			$this->load->module ( 'reports/reports' );
			$this->reports->customer_charge_history ( $accountid, $entity_type );
		}
		if ($module == "refill") {
			$this->load->module ( 'reports/reports' );
			$this->reports->customer_refillreport ( $accountid, $entity_type );
		}
		if ($module == "emailhistory") {
			$this->load->module ( 'email/email' );
			$this->email->customer_mail_record ( $accountid, $entity_type );
		}
		if ($module == "opensips") {
			$this->load->module ( 'opensips/opensips' );
			$this->opensips->customer_opensips_json ( $accountid, $entity_type );
		}
	}
	function customer_details_search($module_name) {
		$action = $this->input->post ();
		$this->session->set_userdata ( 'left_panel_search_' . $module_name, "" );
		if (! empty ( $action ['left_panel_search'] )) {
			$this->session->set_userdata ( 'left_panel_search_' . $module_name, $action ['left_panel_search'] );
		}
	}
	function provider_sipdevices($edit_id) {
		$this->customer_sipdevices ( $edit_id );
	}
	function customer_sipdevices($edit_id) {
		// Get Account information from session.
		$accountinfo = $this->session->userdata ( 'accountinfo' );
		// Get Parent informartion
		$reseller_id = ($accountinfo ['type'] == 1 || $accountinfo ['type'] == 5) ? $accountinfo ['id'] : 0;
		$where = array (
				'id' => $edit_id,
				"reseller_id" => $reseller_id
		);
		$account_res = $this->db_model->getSelect ( "type", "accounts", $where );
		if ($account_res->num_rows () > 0) {
			$data ['page_title'] = gettext ( "SIP Devices" );
			$account_data = ( array ) $account_res->first_row ();
			$accounttype = strtolower ( $this->common->get_entity_type ( '', '', $account_data ['type'] ) );
			$this->load->module ( 'freeswitch/freeswitch' );
			$data ["grid_buttons"] = $this->freeswitch->freeswitch_form->fsdevices_build_grid_buttons ( $edit_id, $accounttype );
			$data ['grid_fields'] = $this->freeswitch->freeswitch_form->build_devices_list_for_customer ();
			$data ['edit_id'] = $edit_id;
			$data ['accounttype'] = $accounttype;
			$this->load->view ( 'view_customer_sipdevices', $data );
		} else {
			$this->session->set_flashdata ( 'astpp_notification','Permission Denied!');
			redirect ( base_url () . 'accounts/customer_list/' );
			exit ();
		}
	}
	function provider_fssipdevices_action($action, $id, $accountid) {
		$this->customer_fssipdevices_action ( $action, $id, $accountid );
	}
	function customer_fssipdevices_action($action, $id, $accountid) {
		$this->permission->check_web_record_permission($id,'sip_devices','accounts/customer_list/');
		$entity_type = $this->common->get_field_name ( 'type', 'accounts', array (
				'id' => $accountid
		) );
		$entity_type = strtolower ( $this->common->get_entity_type ( '', '', $entity_type ) );
		$this->load->module ( 'freeswitch/freeswitch' );
		if ($action == "delete") {
			$this->freeswitch->freeswitch_model->delete_freeswith_devices ( $id );
			$this->session->set_flashdata ( 'astpp_notification', 'Sip Device removed successfully!' );
			redirect ( base_url () . "accounts/" . $entity_type . "_sipdevices/$accountid/" );
		}
		if ($action == "edit") {
			$this->freeswitch->customer_fssipdevices_edit ( $id, $accountid );
			$this->session->set_flashdata ( 'astpp_errormsg', 'Sip updated successfully!' );
		}
	}
	function provider_opensips($edit_id) {
		$this->customer_opensips ( $edit_id );
	}
	function customer_opensips($edit_id) {
		$data ['page_title'] = gettext ( "Opensips Devices" );
		// Get Account information from session.
		$accountinfo = $this->session->userdata ( 'accountinfo' );
		// Get Parent informartion
		$reseller_id = ($accountinfo ['type'] == 1 || $accountinfo ['type'] == 5) ? $accountinfo ['id'] : 0;
		$where = array (
				'id' => $edit_id,
				"reseller_id" => $reseller_id
		);
		$account_res = $this->db_model->getSelect ( "type", "accounts", $where );
		if ($account_res->num_rows () > 0) {
			$account_data = ( array ) $account_res->first_row ();
			$accounttype = strtolower ( $this->common->get_entity_type ( '', '', $account_data ['type'] ) );
			$this->load->module ( 'freeswitch/freeswitch' );
			$this->load->module ( 'opensips/opensips' );
			$data ["grid_buttons"] = $this->opensips->opensips_form->opensips_customer_build_grid_buttons ( $edit_id );
			$data ['grid_fields'] = $this->opensips->opensips_form->opensips_customer_build_opensips_list ( $edit_id );
			$data ['edit_id'] = $edit_id;
			$data ['accounttype'] = $accounttype;
			$this->load->view ( 'view_customer_opensips_devices', $data );
		} else {
			$this->session->set_flashdata ( 'astpp_notification','Permission Denied!');
			redirect ( base_url () . 'accounts/customer_list/' );
			exit ();
		}
	}
	function customer_opensips_action($action, $accountid, $id) {
		$entity_type = $this->common->get_field_name ( 'type', 'accounts', array (
				'id' => $accountid
		) );
		$entity_type = strtolower ( $this->common->get_entity_type ( '', '', $entity_type ) );
		$url = "accounts/" . $entity_type . "_opensips/$accountid/";
		$this->load->module ( 'opensips/opensips' );
		if ($action == "delete") {
			$this->opensips->opensips_model->remove_opensips ( $id );
			$this->session->set_flashdata ( 'astpp_notification', 'Opensips removed successfully!' );
			redirect ( base_url () . $url );
		}
		if ($action == "edit") {
			$this->opensips->customer_opensips_edit ( $accountid, $id );
			$this->session->set_flashdata ( 'astpp_errormsg', 'Opensips updated successfully!' );
		}
	}
	function provider_charges($edit_id) {
		$this->customer_charges ( $edit_id );
	}
	function reseller_charges($edit_id) {
		$this->customer_charges ( $edit_id );
	}
	function customer_charges($edit_id) {
		
		// Get Account information from session.
		$accountinfo = $this->session->userdata ( 'accountinfo' );
		// Get Parent informartion
		$reseller_id = ($accountinfo ['type'] == 1 || $accountinfo ['type'] == 5) ? $accountinfo ['id'] : 0;
		$where = array (
				'id' => $edit_id,
				"reseller_id" => $reseller_id
		);
		$account_res = $this->db_model->getSelect ( "type", "accounts", $where );
		if ($account_res->num_rows () > 0) {
			$data ['page_title'] = gettext ( "Charges History" );
			$account_data = ( array ) $account_res->first_row ();
			$accounttype = strtolower ( $this->common->get_entity_type ( '', '', $account_data ['type'] ) );
			$data ['chargelist'] = form_dropdown ( 'applayable_charge', $this->Astpp_common->list_applyable_charges ( $edit_id ), '' );
			$this->load->module ( 'reports/reports' );
			$data ['grid_fields'] = $this->reports->reports_form->build_charge_list_for_customer ( $edit_id, $accounttype );
			$data ['edit_id'] = $edit_id;
			$data ['accounttype'] = $accounttype;
			$this->load->view ( 'view_customer_charges', $data );
		} else { 
			$this->session->set_flashdata ( 'astpp_notification','Permission Denied!');
			redirect ( base_url () . 'accounts/customer_list/' );
			exit ();
		}
	}
	function provider_subscription($edit_id) {
		$this->customer_subscription ( $edit_id );
	}
	function reseller_subscription($edit_id) {
		$this->customer_subscription ( $edit_id );
	}
	function customer_subscription($edit_id) {
		
		// Get Account information from session.
		$accountinfo = $this->session->userdata ( 'accountinfo' );
		// Get Parent informartion
		$reseller_id = ($accountinfo ['type'] == 1 || $accountinfo ['type'] == 5) ? $accountinfo ['id'] : 0;
		$where = array (
				'id' => $edit_id,
				"reseller_id" => $reseller_id
		);
		$account_res = $this->db_model->getSelect ( "type", "accounts", $where );
		if ($account_res->num_rows () > 0) {
			$account_data = ( array ) $account_res->first_row ();
			$accounttype = strtolower ( $this->common->get_entity_type ( '', '', $account_data ['type'] ) );
			$data ['page_title'] = gettext ( "Subscription" );
			$data ['chargelist'] = form_dropdown_all ( array (
					"name" => 'applayable_charge',
					'id' => 'applayable_charge',
					'class' => ""
			), $this->Astpp_common->list_applyable_charges ( $edit_id ), '' );
			$this->load->module ( 'charges/charges' );
			$data ['grid_fields'] = $this->charges->charges_form->build_charges_list_for_customer ( $edit_id, $accounttype );
			$data ['edit_id'] = $edit_id;
			$data ['accounttype'] = $accounttype;
			$this->load->view ( 'view_customer_subscriptions', $data );
		} else {
			$this->session->set_flashdata ( 'astpp_notification','Permission Denied!');
			redirect ( base_url () . 'accounts/customer_list/' );
			exit ();
		}
	}
	function reseller_subscription_action($action, $accountid, $accounttype, $chargeid = "") {
		$this->customer_subscription_action ( $action, $accountid, $accounttype, $chargeid );
	}
	function provider_subscription_action($action, $accountid, $accounttype, $chargeid = "") {
		$this->customer_subscription_action ( $action, $accountid, $accounttype, $chargeid );
	}

	// /*ASTPP_invoice_changes_05_05_start*/
		// /*ASTPP_invoice_changes_05_05_start*/
	function customer_subscription_action($action, $accountid, $accounttype, $chargeid = "") {
		// Get Account information from session.
		$accountinfo = $this->session->userdata ( 'accountinfo' );
		// Get Parent informartion
		$reseller_id = ($accountinfo ['type'] == 1 || $accountinfo ['type'] == 5) ? $accountinfo ['id'] : 0;
		/**
		 * ASTPP 3.0
		 * For Email Broadcast when Subscription Add - Delete
		 */
		$accountinfo = $this->db_model->getSelect ( "*", "accounts", array (
				"id" => $accountid,'reseller_id'=>$reseller_id
		) );
		$accountinfo = ( array ) $accountinfo->first_row ();
		if(!empty($accountinfo)){
		/* * ****************************** */
			if ($action == "add") {
				$charge_id = $this->input->post ( "applayable_charge", true );
				if ($charge_id != "") {
					$insert_arr = array (
							"charge_id" => $charge_id,
							"accountid" => $accountid,
							"assign_date" => gmdate ( "Y-m-d H:i:s" )
					);
					$this->db->insert ( "charge_to_account", $insert_arr );
					require_once (APPPATH . 'controllers/ProcessCharges.php');
					$ProcessCharges = new ProcessCharges ();
					$Params = array (
							"ChargeID" => $charge_id,
							"AccountInfo" => $accountinfo
					);
					$ProcessCharges->BillAccountCharges ( "ACCOUNTSUBSCRIPTION", $Params );

					$this->session->set_flashdata ( 'astpp_errormsg', 'Subscripton Added Sucessfully.' );
					$template_name = 'add_subscription';
				}
			}
			if ($action == "delete") {

				require_once (APPPATH . 'controllers/ProcessCharges.php');
				$ProcessCharges = new ProcessCharges ();

				$Params = array (
						"ChargeID" => $chargeid,
						"AccountInfo" => $accountinfo
				);
				$ProcessCharges->BillAccountCharges ( "ACCOUNTSUBSCRIPTION", $Params );

				$this->db_model->delete ( "charge_to_account", array (
						"id" => $chargeid
				) );
				$this->session->set_flashdata ( 'astpp_notification', 'Subscription Removed Sucessfully.' );
				$template_name = 'remove_subscription';
			}
			/**
			 * ASTPP 3.0
			 * For Email Broadcast when Subscription Delete
			 */
			$this->common->mail_to_users ( $template_name, $accountinfo );
			/* * ********************************* */
		}	
		redirect ( base_url () . "accounts/" . $accounttype . "_subscription/$accountid/" );
	}
	// END
	function provider_dids($edit_id) {
		$this->customer_dids ( $edit_id );
	}
	function customer_dids($edit_id) {
		$data ['page_title'] = gettext ( "DID" );
		// Get Account information from session.
		$accountinfo = $this->session->userdata ( 'accountinfo' );
		// Get Parent informartion
		$reseller_id = ($accountinfo ['type'] == 1 || $accountinfo ['type'] == 5) ? $accountinfo ['id'] : 0;
		$where = array (
				'id' => $edit_id,
				"reseller_id" => $reseller_id
		);
		$account_res = $this->db_model->getSelect ( "type,country_id", "accounts", $where );
		if ($account_res->num_rows () > 0) {
			$account_data = ( array ) $account_res->first_row ();
			$accounttype = strtolower ( $this->common->get_entity_type ( '', '', $account_data ['type'] ) );
			$this->load->module ( 'did/did' );
			$data ['grid_fields'] = $this->did->did_form->build_did_list_for_customer ( $edit_id, $accounttype );
			$data ['edit_id'] = $edit_id;
			$data ['accounttype'] = $accounttype;
			$result_did_final = array ();
			$did_info = array (
					"name" => "free_didlist",
					"id" => "free_didlist",
					"class" => "free_didlist"
			);
			$data ['didlist'] = form_dropdown_all ( $did_info, $result_did_final, '' );
			$data ['country_id'] = $account_data ['country_id'];
			$this->load->view ( 'view_customer_dids', $data );
		} else {
			redirect ( base_url () . 'accounts/customer_list/' );
			exit ();
		}
	}
	function provider_dids_action($action, $accountid, $accounttype, $did_id = "") {
		$this->customer_dids_action ( $action, $accountid, $accounttype, $did_id );
	}
	function reseller_dids_action($action, $accountid, $accounttype, $did_id = "") {
		$this->customer_dids_action ( $action, $accountid, $accounttype, $did_id );
	}

	// /*ASTPP_invoice_changes_05_05_start*/
	function customer_dids_action($action, $accountid, $accounttype, $did_id = "") {
		// Get account information from session.
		$accountinfo = $this->session->userdata ( 'accountinfo' );
		// Get Parent informartion
		$reseller_id = $accountinfo ['type'] == 1 ? $accountinfo ['id'] : 0;
		$did_id = empty ( $did_id ) ? $this->input->post ( "free_didlist", true ) : $did_id;
		if ($did_id != "") {
			$account_arr = ( array ) $this->db->get_where ( "accounts", array (
					"id" => $accountid
			) )->first_row ();
			$did_arr = ( array ) $this->db->get_where ( "dids", array (
					"id" => $did_id
			) )->first_row ();
			$field_name = $account_arr ['type'] == 1 ? "parent_id" : 'accountid';
			if ($action == "add") {				
				$this->load->library ( 'did_lib' );				
				$did_result = $this->did_lib->did_billing_process($this->session->userdata,$account_arr['id'],$did_id);
				$astpp_flash_message_type = ($did_result[0] == "SUCCESS")?"astpp_errormsg":"astpp_notification";
				$this->session->set_flashdata ( $astpp_flash_message_type, $did_result[1] );	
			}
						if ($action == "delete") {
				$data = array (
						"accountid" => "0",
						"assign_date" => "0000-00-00 00:00:00",
						"charge_upto" => "0000-00-00 00:00:00"
				);
				if ($accounttype == 'reseller') {
					$data = array (
							"accountid" => "0",
							"parent_id" => $reseller_id,
							"assign_date" => "0000-00-00 00:00:00",
							"charge_upto" => "0000-00-00 00:00:00"
					);
				}
				if($reseller_id > 0 ){
					$subreseller_id =$this->common->get_subreseller_info($accountinfo['id']);
					$subreseller_id = rtrim($subreseller_id,",");
					$where = "parent_id IN ($subreseller_id)";
					$this->db->where('id',$did_id);
					$this->db->where($where);
					$this->db->select('id');
					$this->db->from('dids');
					$result = (array)$this->db->get()->first_row();
					if(empty($result)){
						$this->permission->permission_redirect_url('accounts/customer_list/');
					}
				}
				$this->db_model->update ( "dids", $data, array (
						"id" => $did_id
				) );
				if ($accounttype == 'reseller') {
					$this->db->where ( 'reseller_id', $account_arr ['id'] );
					$this->db->where ( 'note', $did_arr ['number'] );
					$this->db->delete ( 'reseller_pricing' );
				}
				require_once (APPPATH . 'controllers/ProcessCharges.php');
				$ProcessCharges = new ProcessCharges ();
				$Params = array (
						"DIDid" => $did_id
				);
				$ProcessCharges->BillAccountCharges ( "DIDs", $Params );
				$account_arr ['did_number'] = $did_arr ['number'];
				$this->common->mail_to_users ( 'email_remove_did', $account_arr );
				$this->session->set_flashdata ( 'astpp_notification', 'Did Removed Successfully.' );
			}
		}
		redirect ( base_url () . "accounts/" . $accounttype . "_dids/$accountid/" );
	}
	// END
	function provider_invoices($edit_id) {
		$this->customer_invoices ( $edit_id );
	}
	function reseller_invoices($edit_id) {
		$this->customer_invoices ( $edit_id );
	}
	function customer_invoices($edit_id) {
		// Get Account information from session.
		$accountinfo = $this->session->userdata ( 'accountinfo' );
		// Get Parent informartion
		$reseller_id = ($accountinfo ['type'] == 1 || $accountinfo ['type'] == 5) ? $accountinfo ['id'] : 0;
		$where = array (
				'id' => $edit_id,
				"reseller_id" => $reseller_id
		);
		$account_res = $this->db_model->getSelect ( "type", "accounts", $where );
		if ($account_res->num_rows () > 0) {
			$data ['page_title'] = gettext ( "Invoice" );
			$account_data = ( array ) $account_res->first_row ();
			$accounttype = strtolower ( $this->common->get_entity_type ( '', '', $account_data ['type'] ) );
			$this->load->module ( 'invoices/invoices' );
			$data ['grid_fields'] = $this->invoices->invoices_form->build_invoices_list_for_customer_admin ( false );
			$data ['edit_id'] = $edit_id;
			$data ['accounttype'] = $accounttype;
			$this->load->view ( 'view_customer_invoices', $data );
		} else {
			$this->session->set_flashdata ( 'astpp_notification','Permission Denied!');
			redirect ( base_url () . 'accounts/customer_list/' );
			exit ();
		}
	}
	function provider_blocked_prefixes($edit_id) {
		$this->customer_blocked_prefixes ( $edit_id );
	}
	function customer_blocked_prefixes($edit_id) {
		// Get Account information from session.
		$accountinfo = $this->session->userdata ( 'accountinfo' );
		// Get Parent informartion
		$reseller_id = ($accountinfo ['type'] == 1 || $accountinfo ['type'] == 5) ? $accountinfo ['id'] : 0;
		$where = array (
				'id' => $edit_id,
				"reseller_id" => $reseller_id
		);
		$account_res = $this->db_model->getSelect ( "type", "accounts", $where );
		if ($account_res->num_rows () > 0) {
			$data ['page_title'] = gettext ( "Blocked Codes" );
			$account_data = ( array ) $account_res->first_row ();
			$accounttype = strtolower ( $this->common->get_entity_type ( '', '', $account_data ['type'] ) );
			$this->load->module ( 'rates/rates' );
			$data ['grid_fields'] = $this->rates->rates_form->build_pattern_list_for_customer ( $edit_id, $accounttype );
			$data ['grid_buttons'] = $this->rates->rates_form->set_pattern_grid_buttons ( $edit_id );
			$data ['edit_id'] = $edit_id;
			$data ['accounttype'] = $accounttype;
			$this->load->view ( 'view_customer_blocked_prefixes', $data );
		} else {
			$this->session->set_flashdata ( 'astpp_notification','Permission Denied!');
			redirect ( base_url () . 'accounts/customer_list/' );
			exit ();
		}
	}
	function customer_add_blockpatterns($accountid) {
		$data ['username'] = $this->session->userdata ( 'user_name' );
		$data ['page_title'] = gettext ( 'Unblocked Codes' );
		$this->session->set_userdata ( 'advance_search', 0 );
		$this->load->module ( 'rates/rates' );
		$data ['patters_grid_fields'] = $this->rates->rates_form->build_block_pattern_list_for_customer ();
		$data ["accountid"] = $accountid;
		$this->load->view ( 'view_block_prefix_list', $data );
	}
	function customer_add_blockpatterns_json($accountid) {
		$this->load->module ( 'rates/rates' );
		$json_data = array ();
		$count_all = $this->rates_model->getunblocked_pattern_list ( $accountid, false );
		$paging_data = $this->form->load_grid_config ( $count_all, $_GET ['rp'], $_GET ['page'] );
		$json_data = $paging_data ["json_paging"];

		$query = $this->rates->rates_model->getunblocked_pattern_list ( $accountid, true, $paging_data ["paging"] ["start"], $paging_data ["paging"] ["page_no"] );
		$grid_fields = json_decode ( $this->rates->rates_form->build_block_pattern_list_for_customer () );
		$json_data ['rows'] = $this->rates->form->build_grid ( $query, $grid_fields );
		echo json_encode ( $json_data );
	}
	function customer_block_prefix($accountid) {
		$result = $this->accounts_model->insert_block ( $this->input->post ( 'prefixies', true ), $accountid );
		echo $result;
		exit ();
	}
	function provider_delete_block_pattern($accountid, $patternid) {
		$this->customer_delete_block_pattern ( $accountid, $patternid );
	}
	function customer_delete_block_pattern($accountid, $patternid) {
		$this->permission->check_web_record_permission($patternid,'block_patterns','accounts/customer_list/',false,array('field_name'=>"accountid","parent_table"=>"accounts"));
		// Get Account information from session.
		$accountinfo = $this->session->userdata ( 'accountinfo' );
		// Get Parent informartion
		$reseller_id = ($accountinfo ['type'] == 1 || $accountinfo ['type'] == 5) ? $accountinfo ['id'] : 0;
		$where = array (
				'id' => $edit_id,
				"reseller_id" => $reseller_id
		);
		$entity_type = $this->common->get_field_name ( 'type', 'accounts',$where);
		if(!empty($entity_type)){
			$entity_type = strtolower ( $this->common->get_entity_type ( '', '', $entity_type ) );
			$url = "accounts/" . $entity_type . "_blocked_prefixes/$accountid";
			$this->db_model->delete ( "block_patterns", array (
				"id" => $patternid
			) );
			$this->session->set_flashdata ( 'astpp_notification', 'Block Code Removed Sucessfully!' );
		}else{
			$this->session->set_flashdata ( 'astpp_notification','Permission Denied!');
		}
		redirect ( base_url () . $url );
	}
	function customer_blockedprefixes_delete_multiple() {
		$ids = $this->input->post ( "selected_ids", true );
		$where = "id IN ($ids)";
		$this->db->delete ( "block_patterns", $where );
		echo TRUE;
	}

	/*
	 * ASTPP 3.0
	 * Using function using for provider cdrs tab
	 */
	function provider_cdrs($edit_id) {
		$this->customer_cdrs ( $edit_id );
	}

	/* * ************************************************ */
	/*
	 * ASTPP 3.0
	 * Using function using for reseller cdrs tab
	 */
	function reseller_cdrs($edit_id) {
		$this->customer_cdrs ( $edit_id );
	}

	/* * ************************************************ */
	/*
	 * ASTPP 3.0
	 * Using function using for customer cdrs tab
	 */
	function customer_cdrs($edit_id) {
		$data ['page_title'] = gettext ( "CDRs" );
		// Get Account information from session.
		$accountinfo = $this->session->userdata ( 'accountinfo' );
		// Get Parent informartion
		$reseller_id = ($accountinfo ['type'] == 1 || $accountinfo ['type'] == 5) ? $accountinfo ['id'] : 0;
		$where = array (
				'id' => $edit_id,
				"reseller_id" => $reseller_id
		);
		$account_res = $this->db_model->getSelect ( "type", "accounts", $where );
		if ($account_res->num_rows () > 0) {
			$account_data = ( array ) $account_res->first_row ();
			$accounttype = strtolower ( $this->common->get_entity_type ( '', '', $account_data ['type'] ) );
			$this->load->module ( 'reports/reports' );
			$data ['grid_fields'] = $this->reports->reports_form->build_report_list_for_user ( $accounttype );
			$data ['edit_id'] = $edit_id;
			$data ['accounttype'] = $accounttype;
			$this->load->view ( 'view_customer_cdrs_list', $data );
		} else {
			$this->session->set_flashdata ( 'astpp_notification','Permission Denied!');
			redirect ( base_url () . 'accounts/customer_list/' );
			exit ();
		}
	}

	/* * ***************************************** */
	/*
	 * ASTPP 3.0
	 * Using for provider refillreporttab.
	 */
	function reseller_packages($edit_id) {
		$data ['page_title'] = gettext ( "Packages" );
		$accountinfo = $this->session->userdata ( 'accountinfo' );
		$reseller_id = ($accountinfo ['type'] == 1 || $accountinfo ['type'] == 5) ? $accountinfo ['id'] : 0;
		$where = array (
				'id' => $edit_id,
				"reseller_id" => $reseller_id
		);
		$account_res = $this->db_model->getSelect ( "type", "accounts", $where );
		if ($account_res->num_rows () > 0) {
			$account_data = ( array ) $account_res->first_row ();
			$accounttype = strtolower ( $this->common->get_entity_type ( '', '', $account_data ['type'] ) );
			$this->load->module ( 'package/package' );
			$data ['grid_fields'] = $this->package->package_form->build_package_list_for_reseller ();
			$data ['edit_id'] = $edit_id;
			$data ['accounttype'] = $accounttype;
			$this->load->view ( 'view_reseller_package_list', $data );
		} else {
			$this->session->set_flashdata ( 'astpp_notification','Permission Denied!');
			redirect ( base_url () . 'accounts/reseller_list/' );
			exit ();
		}
	}
	function reseller_refillreport($edit_id) {
		$this->customer_refillreport ( $edit_id );
	}
	function provider_refillreport($edit_id) {
		$this->customer_refillreport ( $edit_id );
	}

	/* * ***************************************** */
	/*
	 * ASTPP 3.0
	 * Using for customer refill report tab.
	 */
	function customer_refillreport($edit_id) {
		$data ['page_title'] = gettext ( "Refill Report" );
		// Get Account information from session.
		$accountinfo = $this->session->userdata ( 'accountinfo' );
		// Get Parent informartion
		$reseller_id = ($accountinfo ['type'] == 1 || $accountinfo ['type'] == 5) ? $accountinfo ['id'] : 0;
		$where = array (
				'id' => $edit_id,
				"reseller_id" => $reseller_id
		);
		$account_res = $this->db_model->getSelect ( "type", "accounts", $where );
		if ($account_res->num_rows () > 0) {
			$account_data = ( array ) $account_res->first_row ();
			$accounttype = strtolower ( $this->common->get_entity_type ( '', '', $account_data ['type'] ) );
			$this->load->module ( 'reports/reports' );
			$data ['grid_fields'] = $this->reports->reports_form->build_refillreport_for_customer ();
			$data ['edit_id'] = $edit_id;
			$data ['accounttype'] = $accounttype;
			$this->load->view ( 'view_customer_refill_report', $data );
		} else {
			$this->session->set_flashdata ( 'astpp_notification','Permission Denied!');
			redirect ( base_url () . 'accounts/customer_list/' );
			exit ();
		}
	}

	/* * ***************************************** */
	/*
	 * ASTPP 3.0
	 * Using for provider email history tab.
	 */
	function reseller_emailhistory($edit_id) {
		$this->customer_emailhistory ( $edit_id );
	}
	function provider_emailhistory($edit_id) {
		$this->customer_emailhistory ( $edit_id );
	}

	/* * ***************************************** */
	/*
	 * ASTPP 3.0
	 * Using for customer email history tab.
	 */
	function customer_emailhistory($edit_id) {
		
		// Get Account information from session.
		$accountinfo = $this->session->userdata ( 'accountinfo' );
		// Get Parent informartion
		$reseller_id = ($accountinfo ['type'] == 1 || $accountinfo ['type'] == 5) ? $accountinfo ['id'] : 0;
		$where = array (
				'id' => $edit_id,
				"reseller_id" => $reseller_id
		);
		$account_res = $this->db_model->getSelect ( "type", "accounts", $where );
		if ($account_res->num_rows () > 0) {
			$data ['page_title'] = gettext ( "Emails" );
			$account_data = ( array ) $account_res->first_row ();
			$accounttype = strtolower ( $this->common->get_entity_type ( '', '', $account_data ['type'] ) );
			$this->load->module ( 'email/email' );
			$data ['grid_fields'] = $this->email->email_form->build_list_for_email_customer ( $edit_id, $accounttype );
			$data ['edit_id'] = $edit_id;
			$data ['accounttype'] = $accounttype;
			$this->load->view ( 'view_customer_email_history', $data );
		} else {
			$this->session->set_flashdata ( 'astpp_notification','Permission Denied!');
			redirect ( base_url () . 'accounts/customer_list/' );
			exit ();
		}
	}

	/* * *********************************************** */
	/*
	 * ASTPP 3.0
	 * Using for provider Alert threshold history tab.
	 */
	function reseller_alert_threshold($edit_id) {
		$this->customer_alert_threshold ( $edit_id );
	}
	function provider_alert_threshold($edit_id) {
		$this->customer_alert_threshold ( $edit_id );
	}

	/* * *********************************************** */
	/*
	 * ASTPP 3.0
	 * Using for customer Alert threshold history tab.
	 */
	function customer_alert_threshold($edit_id) {
		$data ['page_title'] = gettext ( "Alert Threshold" );
		// Get Account information from session.
		$accountinfo = $this->session->userdata ( 'accountinfo' );
		// Get Parent informartion
		$reseller_id = ($accountinfo ['type'] == 1 || $accountinfo ['type'] == 5) ? $accountinfo ['id'] : 0;
		$where = array (
				'id' => $edit_id,
				"reseller_id" => $reseller_id
		);
		$account_res = $this->db_model->getSelect ( "notify_credit_limit,notify_flag,notify_email,type,id", "accounts", $where );
		if ($account_res->num_rows () > 0) {
			$account_data = ( array ) $account_res->first_row ();
			$accounttype = strtolower ( $this->common->get_entity_type ( '', '', $account_data ['type'] ) );
			unset ( $account_data ['type'] );
			$data ['form'] = $this->form->build_form ( $this->accounts_form->customer_alert_threshold ( $accounttype ), $account_data );
			$data ['edit_id'] = $edit_id;
			$data ['accounttype'] = $accounttype;
			$this->load->view ( 'view_customer_alert_threshold', $data );
		} else {
			$this->session->set_flashdata ( 'astpp_notification','Permission Denied!');
			redirect ( base_url () . 'accounts/customer_list/' );
			exit ();
		}
	}
	function reseller_alert_threshold_save($entity_type) {
		$this->customer_alert_threshold_save ( $entity_type );
	}
	function provider_alert_threshold_save($entity_type) {
		$this->customer_alert_threshold_save ( $entity_type );
	}
	function customer_alert_threshold_save($entity_type) {
		$add_array = $this->input->post ();
		if (isset ( $add_array ['id'] ) && ! empty ( $add_array ['id'] )) {
			$data ['page_title'] = gettext ( "Alert Threshold" );
			$data ['form'] = $this->form->build_form ( $this->accounts_form->customer_alert_threshold ( $entity_type ), $add_array );
			$data ['edit_id'] = $add_array ['id'];
			$data ['accounttype'] = $entity_type;
			// Get Account information from session.
			$accountinfo = $this->session->userdata ( 'accountinfo' );
			// Get Parent informartion
			$reseller_id = ($accountinfo ['type'] == 1 || $accountinfo ['type'] == 5) ? $accountinfo ['id'] : 0;
			$where = array (
					'id' => $add_array ['id'],
					"reseller_id" => $reseller_id
			);
			$account_res = $this->db_model->getSelect ( "type", "accounts", $where );
			if ($account_res->num_rows () > 0) {
				if ($this->form_validation->run () == FALSE) {
					$data ['validation_errors'] = validation_errors ();
				} else {
					$this->db->where ( 'id', $add_array ['id'] );
					$id = $add_array ['id'];
					unset ( $add_array ['id'], $add_array ['action'] );
					$this->db->update ( 'accounts', $add_array );
					$this->session->set_flashdata ( 'astpp_errormsg', 'Alert threshold updated successfully!' );
					redirect ( base_url () . 'accounts/' . $entity_type . '_alert_threshold/' . $id . "/" );
					exit ();
				}
			} else {
				redirect ( base_url () . 'accounts/' . $entity_type . '_list/' );
				exit ();
			}
			$this->load->view ( 'view_customer_alert_threshold', $data );
		} else {
			$this->session->set_flashdata ( 'astpp_notification','Permission Denied!');
			redirect ( base_url () . 'accounts/customer_list/' );
			exit ();
		}
	}
	function customer_bulk_creation() {
		$type = 0;
		$data ['entity_name'] = strtolower ( $this->common->get_entity_type ( '', '', $type ) );
		$data ['page_title'] = gettext ( 'Mass Customer' );
		$data ['country_id'] = Common_model::$global_config ['system_config'] ['country'];
		$data ['currency_id'] = $this->common->get_field_name ( 'id', 'currency', array (
				'currency' => Common_model::$global_config ['system_config'] ['base_currency']
		) );
		$data ['timezone_id'] = Common_model::$global_config ['system_config'] ['default_timezone'];
		if (! $data ['timezone_id']) {
			$data ['timezone_id'] = 1;
		}
		if (! $data ['currency_id']) {
			$data ['currency_id'] = 1;
		}
		if (! $data ['country_id']) {
			$data ['country_id'] = 1;
		}
		$data ['form'] = $this->form->build_form ( $this->accounts_form->customer_bulk_generate_form (), '' );
		$this->load->view ( 'view_bulk_account_creation', $data );
	}
	function customer_bulk_save() {
		$data ['page_title'] = gettext ( 'Create Bulk Customer' );
		$add_array = $this->input->post ();
		if (! empty ( $add_array ) && isset ( $add_array )) {
			$currentlength = $this->accounts_model->get_max_limit ( $add_array );
			$account_data = $this->session->userdata ( "accountinfo" );
			$add_array ['reseller_id'] = $account_data ['type'] == 1 ? $account_data ['id'] : 0;
			$data ['form'] = $this->form->build_form ( $this->accounts_form->customer_bulk_generate_form (), $add_array );
			if ($this->form_validation->run () == FALSE) {
				$data ['validation_errors'] = validation_errors ();
				echo $data ['validation_errors'];
				exit ();
			}
			if ($currentlength <= 0) {
				echo json_encode ( array (
						"prefix_error" => "Your Account Limit has been reached.Please Change Your Prefix."
				) );
				exit ();
			}
			if ($add_array ['account_length'] <= strlen ( $add_array ['prefix'] )) {
				echo json_encode ( array (
						"account_length_error" => "Please Enter Proper Account Length."
				) );
				exit ();
			}
			if ($currentlength > 0 && $add_array ['count'] > $currentlength) {
				echo json_encode ( array (
						"count_error" => "You Can Create Maximum " . $currentlength . " accounts with " . $add_array ['prefix'] . " prefix"
				) );
				exit ();
			} else {
				$this->accounts_model->bulk_insert_accounts ( $add_array );
				echo json_encode ( array (
						"SUCCESS" => "Bulk customer generate successfully!"
				) );
				exit ();
			}
		} else {
			$this->session->set_flashdata ( 'astpp_notification','Permission Denied!');
			redirect ( base_url () . "accounts/customer_list/" );
		}
	}
	function customer_invoice_option($value = false) {
		$sweepid = $this->input->post ( "sweepid", true );
		$invoice_dropdown = $this->common->set_invoice_option ( $sweepid, "", "", $value );
		echo $invoice_dropdown;
	}

	/*
	 * *****
	 * ASTPP 3.0
	 * in customer Edit put one dropdown with the name of country and change with Purchase dropdown.
	 * *****
	 */
	function customer_did_country() {
		$country_id = $_POST ['country_id'];
		$accountid = $this->input->post ( 'accountid', true );
		$accountinfo = $this->session->userdata ( "accountinfo" );
		$entity_info = ( array ) $this->db->get_where ( 'accounts', array (
				"id" => $accountid
		) )->first_row ();
		$reseller_id = $accountinfo ['type'] == 1 ? $accountinfo ['id'] : 0;
		if ($entity_info ['reseller_id'] > 0) {
			$parent_id = $accountinfo ['type'] == 1 ? $accountinfo ['id'] : $accountinfo ['reseller_id'];
			$query = 'select dids.id as id,dids.number as number,reseller_pricing.setup as setup, reseller_pricing.monthlycost as monthlycost from dids,reseller_pricing where dids.number= reseller_pricing.note and reseller_pricing.reseller_id =' . $parent_id . " and dids.accountid=0 and dids.country_id =" . $country_id;
			$did_list = $this->db->query ( $query );
		} else {
			$did_list = $this->db_model->getSelect ( "id,number,setup,monthlycost", "dids", array (
					'country_id' => $country_id,
					'accountid' => '0',
					'parent_id' => $reseller_id
			) );
		}
		$did_arr = array ();
		if ($did_list->num_rows () > 0) {
			$did_data = $did_list->result_array ();
			foreach ( $did_data as $key => $value ) {
				$did_arr [$value ['id']] = $value ['number'] . " (Setup Cost : " . $this->common_model->calculate_currency ( $value ['setup'], '', '', true ) . ") (Monthly cost : " . $this->common_model->calculate_currency ( $value ['monthlycost'], '', '', true ) . ")";
			}
		}
		$did_info = array (
				"name" => "free_didlist",
				"id" => "free_didlist",
				"class" => "free_didlist"
		);
		echo form_dropdown_all ( $did_info, $did_arr, '' );
		exit ();
	}

/* * ************************************************************** */
	function customer_payment_process_add($id = '') {
		$accountinfo = $this->session->userdata('accountinfo');
		$reseller_id = $accountinfo['type']== 1 ? $accountinfo['id'] : 0;
		$customer_info =$this->db->get_where('accounts',array('id'=>$id,'reseller_id'=>$reseller_id));
		if($customer_info->num_rows > 0){
			$customer_info = (array)$customer_info->first_row();
			$currency = $this->accounts_model->get_currency_by_id ( $customer_info ['currency_id'] );
			$data ['username'] = $this->session->userdata ( 'user_name' );
			$data ['page_title'] = gettext ( '​Refill Process' );
			$data ['form'] = $this->form->build_form ( $this->accounts_form->get_customer_payment_fields ( $currency ['currency'], $customer_info ['number'], $currency ['currency'], $id ), '' );
			$this->load->view ( 'view_accounts_process_payment', $data );
		}else{
			$this->session->set_flashdata ( 'astpp_notification', 'Permission Denied' );
			redirect ( base_url () . 'accounts/customer_list/' );
		}	
	}
	function customer_payment_save($id = '') {
		$post_array = $this->input->post ();
		$accountinfo = $this->session->userdata('accountinfo');
		$reseller_id = $accountinfo['type']== 1 ? $accountinfo['id'] : 0;
		$customer_info =$this->db->get_where('accounts',array('id'=>$post_array['id'],'reseller_id'=>$reseller_id));
		//echo "sdf";exit;
		if($customer_info->num_rows > 0){
			$data ['page_title'] = gettext ( 'Process Payment' );
			$customer_info = (array)$customer_info->first_row();
			$currency = $this->accounts_model->get_currency_by_id ( $customer_info ['currency_id'] );
			$data ['form'] = $this->form->build_form ( $this->accounts_form->get_customer_payment_fields ( $currency ['currency'], $customer_info ['number'], $currency ['currency'], $id ),$post_array);
			if ($this->form_validation->run () == FALSE) {
				$data ['validation_errors'] = validation_errors ();
				echo $data ['validation_errors'];
				exit ();
			} else {
				
				$post_array ['credit'] = $this->common_model->add_calculate_currency ( $post_array ['credit'], "", '', false, false );
				$parent_id = ($customer_info ['reseller_id'] == 0) ? 1 : $customer_info ['reseller_id'];
				$where = "accountid IN('".$parent_id."','1')";
				$this->db->select('*');
				$this->db->where($where);
				$this->db->order_by('accountid','desc');
				$this->db->limit(1);
				$invoice_conf = $this->db->get('invoice_conf');
				$invoice_conf = ( array ) $invoice_conf->first_row();
				$last_invoice_ID = $this->get_invoice_date ( "invoiceid", $customer_info ["id"] );
				$invoice_prefix = $invoice_conf ['invoice_prefix'];
				$due_date = $invoice_conf ['interval']  > 0 ? date ( "Y-m-d H:i:s", strtotime ( gmdate ( "Y-m-d H:i:s" ) . " +" . $invoice_conf ['interval'] . " days" ) ) : gmdate ( "Y-m-d H:i:s" );
				$this->load->module ( 'invoices/invoices' );
				$invoice_id = $this->invoices->invoices->generate_receipt ( $post_array ['id'], $post_array ['credit'], $customer_info, $last_invoice_ID + 1, $invoice_prefix, $due_date );
				$account_balance = $customer_info ['posttoexternal'] == 1 ? $customer_info ['credit_limit'] - ($customer_info ['balance']) : $customer_info ['balance'];
				//echo $account_balance;exit;
				// Post charge
				$insert_arr = array (
						"accountid" =>$customer_info['id'],
						"description" =>trim($post_array['notes']),
						"created_date" =>gmdate("Y-m-d H:i:s"),
						"invoiceid" => $invoice_id,
						"reseller_id" => $reseller_id,
						"item_id" => '0',
						'before_balance' =>$account_balance,
					);
				if($post_array ['payment_type'] == 1) {
					$insert_arr['item_type'] = "POSTCHARG";
					$insert_arr['after_balance'] = $account_balance-$post_array['credit'];
					$insert_arr['debit'] = $post_array['credit'];
					$insert_arr['credit'] = 0;
				}else {
					//Refill 
					$insert_arr['item_type'] = "Refill";
					$insert_arr['after_balance'] = $account_balance+$post_array['credit'];
					$insert_arr['credit'] = $post_array['credit'];
					$insert_arr['debit'] = 0;
				}
				$this->db->insert("invoice_details",$insert_arr);
				$response = $this->accounts_model->account_process_payment ( $post_array );
				$message = $post_array ['payment_type'] == 0 ? "Recharge successfully!" : "Post charge applied successfully.";
				echo json_encode ( array (
					"SUCCESS" => $message
				) );
				exit ();
			}
			$this->load->view ( 'view_accounts_process_payment', $data );
		}else{
			$this->session->set_flashdata ( 'astpp_notification', 'Permission Denied' );
			redirect ( base_url () . 'accounts/customer_list/' );
		}
	}

	/**
	 *
	 * @param string $select
	 */
	function get_invoice_date($select, $accountid) {
		$query = $this->db_model->select ( $select, "invoices", '', "id", "DESC", "1", "0" );
		if ($query->num_rows () > 0) {
			$invoiceid = $query->result_array ();
			$invoice_date = $invoiceid [0] [$select];
			return $invoice_date;
		}
		return false;
	}
	/**
	 * *************************************Completed*******************************************************
	 */

	/**
	 * -------Here we write code for controller accounts functions add_callerid------
	 * Add caller ids against account no
	 * @account_number: Account No
	 */
	function customer_add_callerid($id = "") {
		$data ['username'] = $this->session->userdata ( 'user_name' );
		$data ['page_title'] = gettext ( '​Force Caller Id' );
		$account_num = $this->accounts_model->get_account_number ( $id );
		$result = $this->accounts_model->get_callerid ( $id );
		if ($result->num_rows () > 0) {
			foreach ( $result->result_array () as $values ) {
				$data ['accountid'] = $values ['accountid'];
				$data ['callerid_name'] = $values ['callerid_name'];
				$data ['callerid_number'] = $values ['callerid_number'];
				$data ['status'] = $values ['status'];
				$data ['flag'] = '1';
			}
		} else {
			$data ['accountid'] = $id;
			$data ['callerid_name'] = '';
			$data ['callerid_number'] = '';
			$data ['status'] = '0';
			$data ['flag'] = '0';
		}
		$data ['accountid'] = $account_num ['number'];
		$data ['form'] = $this->form->build_form ( $this->accounts_form->get_customer_callerid_fields (), $data );
		$post_array = $this->input->post ();

		if (! empty ( $post_array )) {
			if ($this->form_validation->run () == FALSE) {
				$data ['validation_errors'] = validation_errors ();
				echo $data ['validation_errors'];
				exit ();
			} else {
				if ($post_array ['flag'] == '1') {
					$this->accounts_model->edit_callerid ( $post_array );
					echo json_encode ( array (
							"SUCCESS" => "Account callerID updated successfully!"
					) );
					exit ();
				} else {
					$this->accounts_model->add_callerid ( $post_array );
					echo json_encode ( array (
							"SUCCESS" => "Account callerID added successfully!"
					) );
					exit ();
				}
			}
		}
		$this->load->view ( 'view_accounts_add_callerid', $data );
	}
	function reseller_add($type = "") {
		$accountinfo = $this->session->userdata ( 'accountinfo' );
		$data ['username'] = $this->session->userdata ( 'user_name' );
		$data ['flag'] = 'create';
		$data ['page_title'] = gettext ( 'Create Reseller' );
		$data ['back_flag'] = true;
		$data ['form'] = $this->form->build_form ( $this->accounts_form->get_form_reseller_fields (), '' );
		$data ['country_id'] = $accountinfo ['country_id'];
		$data ['currency_id'] = $accountinfo ['currency_id'];
		$data ['timezone_id'] = $accountinfo ['timezone_id'];
		if (! $data ['timezone_id']) {
			$data ['timezone_id'] = 1;
		}
		if (! $data ['currency_id']) {
			$data ['currency_id'] = 1;
		}
		if (! $data ['country_id']) {
			$data ['country_id'] = 1;
		}

		$this->load->view ( 'view_accounts_create', $data );
	}
	function reseller_edit($edit_id = '') {
		$data ['page_title'] = gettext ( 'Edit Reseller' );

		$accountinfo = $this->session->userdata ( 'accountinfo' );
		$reseller_id = ($accountinfo ['type'] == 1 || $accountinfo ['type'] == 5) ? $accountinfo ['id'] : 0;
		$where = array (
				'id' => $edit_id,
				"reseller_id" => $reseller_id
		);
		$account = $this->db_model->getSelect ( "*", "accounts", $where );		
		$data ["account_data"] = $account->result_array ();
		foreach ( $account->result_array () as $key => $value ) {
			$edit_data = $value;
		}		

		//Record permission
		if (count($edit_data) == 0)
		{
			$this->session->set_flashdata ( 'astpp_notification','Permission Denied!');
			redirect ( base_url () . 'accounts/reseller_list/' );
			exit;
		}

		$taxes_data = $this->db_model->getSelect ( "group_concat(taxes_id) as taxes_id", "taxes_to_accounts", array (
				"accountid" => $edit_id
		) );
		if (isset ( $taxes_data ) && $taxes_data->num_rows () > 0) {
			$taxes_data = $taxes_data->result_array ();
			$edit_data ["tax_id"] = explode ( ",", $taxes_data [0] ['taxes_id'] );
		}

		/**
		 * ASTPP 3.0
		 * Add For Signup module
		 */
		$encrypted_string = $this->common->encode ( $edit_data ['id'] );
		$encrypt = $this->common->encode_params ( $encrypted_string );
		$edit_data ['registration_url'] = base_url () . "signup/" . $encrypt;
		/* * *********************************************************** */
		/*
		 * ***
		 * ASTPP 3.0
		 * Password decode
		 * ****
		 */
		$edit_data ['password'] = $this->common->decode ( $edit_data ['password'] );
		$edit_data ['credit_limit'] = $this->common_model->calculate_currency ( ($edit_data ['credit_limit']), '', '', true, false );
		$entity_name = strtolower ( $this->common->get_entity_type ( '', '', $edit_data ['type'] ) );
		$data ['edit_id'] = $edit_id;
		$data ['page_title'] = gettext ( ucfirst ( $entity_name ) . " Profile" );
		$data ['entity_name'] = $entity_name;
		/* * ********************** */
		$data ['form'] = $this->form->build_form ( $this->accounts_form->get_form_reseller_fields ( $edit_id ), $edit_data );
		$this->load->view ( 'view_reseller_details', $data );
	}

	/* * *********************************************************** */
	function reseller_save() {
		$add_array = $this->input->post ();
		$entity_name = strtolower ( $this->common->get_entity_type ( '', '', $add_array ['type'] ) );
		$data ['country_id'] = $add_array ['country_id'];
		$data ['timezone_id'] = $add_array ['timezone_id'];
		$data ['currency_id'] = $add_array ['currency_id'];
		$data ['entity_name'] = $entity_name;
		$data ['edit_id'] = $add_array ['id'];
		$data ['form'] = $this->form->build_form ( $this->accounts_form->get_form_reseller_fields ( $add_array ['id'] ), $add_array );
		if ($add_array ['id'] != '') {
			$data ['page_title'] = gettext ( 'Edit Reseller' );
			if ($this->form_validation->run () == FALSE) {
				$data ['validation_errors'] = validation_errors ();
			} else {
				/*
				 * ****
				 * ASTPP 3.0
				 * Password encode
				 * ****
				 */
				$add_array ['password'] = $this->common->encode ( $add_array ['password'] );
				/* * ****************** */
				$add_array ['credit_limit'] = $this->common_model->add_calculate_currency ( $add_array ['credit_limit'], '', '', false, false );
				$query = $this->accounts_model->remove_all_account_tax ( $add_array ['id'] );
				if (isset ( $add_array ['tax_id'] )) {
					foreach ( $add_array ['tax_id'] as $key => $val ) {
						$data1 = array (
								'accountid' => $add_array ['id'],
								'taxes_id' => $val
						);
						$this->accounts_model->add_account_tax ( $data1 );
					}
					unset ( $add_array ['tax_id'] );
				}
				unset ( $add_array ['number'], $add_array ['registration_url'] );
				$this->accounts_model->edit_account ( $add_array, $add_array ['id'] );
				$this->session->set_flashdata ( 'astpp_errormsg', 'Reseller updated successfully!' );

				redirect ( base_url () . 'accounts/reseller_list/' );
				exit ();
			}
			$data ["account_data"] ["0"] = $add_array;
			$edit_id = $add_array ["id"];
			$data ['chargelist'] = form_dropdown ( 'applayable_charge', $this->Astpp_common->list_applyable_charges ( $edit_id ), '' );

			$this->load->module ( 'charges/charges' );
			$data ['charges_grid_field'] = $this->charges->charges_form->build_charges_list_for_customer ( $edit_id, "reseller" );

			$data ["sipiax_grid_field"] = json_decode ( $this->accounts_form->build_sipiax_list_for_customer () );

			$this->load->module ( 'did/did' );
			$data ['did_grid_fields'] = $this->did->did_form->build_did_list_for_customer ( $edit_id, "reseller" );
			$data ['didlist'] = form_dropdown ( 'free_did_list', $this->db_model->build_dropdown ( "id,number", "dids", "accountid", "0" ), '' );

			$this->load->module ( 'invoices/invoices' );
			$data ['invoice_grid_fields'] = $this->invoices->invoices_form->build_invoices_list_for_admin ();

			$this->load->module ( 'reports/reports' );
			$data ['cdrs_grid_fields'] = $this->reports->reports_form->build_report_list_for_user ();
			$this->load->view ( 'view_reseller_details', $data );
		} else {

			$data ['page_title'] = gettext ( 'Create Reseller' );
			if ($this->form_validation->run () == FALSE) {
				$data ['validation_errors'] = validation_errors ();
			} else {
				/*
				 * ****
				 * ASTPP 3.0
				 * Password encode
				 * ****
				 */
				$add_array ['password'] = $this->common->encode ( $add_array ['password'] );
				/* * ****************** */
				$add_array ['credit_limit'] = $this->common_model->add_calculate_currency ( $add_array ['credit_limit'], '', '', false, false );
				$last_id = $this->accounts_model->add_account ( $add_array );
				if (isset ( $add_array ['tax_id'] )) {
					foreach ( $add_array ['tax_id'] as $key => $val ) {
						$data1 = array (
								'accountid' => $last_id,
								'taxes_id' => $val
						);
						$this->accounts_model->add_account_tax ( $data1 );
					}
					unset ( $add_array ['tax_id'] );
				}
				$this->session->set_flashdata ( 'astpp_errormsg', 'Reseller added successfully!' );
				redirect ( base_url () . 'accounts/reseller_list/' );
				exit ();
			}
			$this->load->view ( 'view_accounts_create', $data );
		}
	}
	function reseller_dids($edit_id) {
		$this->customer_dids ( $edit_id );
	}
	function customer_generate_password() {
		echo $this->common->generate_password ();
	}
	function customer_generate_number($digit = '') {
		echo $this->common->find_uniq_rendno_customer ( common_model::$global_config ['system_config'] ['cardlength'], 'number', 'accounts' );
		// end
		// echo $this->common->find_uniq_rendno($digit, 'number', 'accounts');
	}
	function admin_add($type = 2) {
		$accountinfo = $this->session->userdata ( 'accountinfo' );
		$entity_type = strtolower ( $this->common->get_entity_type ( '', '', $type ) );
		$entitytype = str_replace ( ' ', '', $entity_type );
		$data ['username'] = $this->session->userdata ( 'user_name' );
		$data ['flag'] = 'create';
		$data ['page_title'] = gettext ( 'Create ' . ucfirst ( $entity_type ) );
		$data ['back_flag'] = true;
		$data ['form'] = $this->form->build_form ( $this->accounts_form->get_form_admin_fields ( $entitytype ), '' );
		$data ['country_id'] = $accountinfo ['country_id'];
		$data ['currency_id'] = $accountinfo ['currency_id'];
		$data ['timezone_id'] = $accountinfo ['timezone_id'];
		if (! $data ['timezone_id']) {
			$data ['timezone_id'] = 1;
		}
		if (! $data ['currency_id']) {
			$data ['currency_id'] = 1;
		}
		if (! $data ['country_id']) {
			$data ['country_id'] = 1;
		}
		$data ['entity_name'] = $entity_type;
		$this->load->view ( 'view_accounts_create', $data );
	}
	function admin_edit($edit_id = '') {
		$data ['back_flag'] = true;
		$accountinfo = ( array ) $this->db->get_where ( 'accounts', array (
				"id" => $edit_id
		) )->first_row ();
		$type = $accountinfo ['type'] == - 1 ? 2 : $accountinfo ['type'];
		$entity_type = strtolower ( $this->common->get_entity_type ( '', '', $type ) );
		$entitytype = str_replace ( ' ', '', $entity_type );
		$accountinfo ['password'] = $this->common->decode ( $accountinfo ['password'] );
		$data ['form'] = $this->form->build_form ( $this->accounts_form->get_form_admin_fields ( $entitytype, $edit_id ), $accountinfo );
		$data ['page_title'] = gettext ( 'Edit ' . ucfirst ( $entity_type ) );
		$this->load->view ( 'view_admin_details', $data );
	}
	function admin_save($add_array = false) {
		$add_array = $this->input->post ();
		$accountinfo = $this->session->userdata ( 'accountinfo' );
		$type = $add_array ['type'] == - 1 ? 2 : $add_array ['type'];
		$entity_type = strtolower ( $this->common->get_entity_type ( '', '', $type ) );
		$entitytype = str_replace ( ' ', '', $entity_type );
		$data ['username'] = $this->session->userdata ( 'user_name' );
		$data ['flag'] = 'create';
		$data ['page_title'] = gettext ( 'Create ' . $entity_type );
		$data ['form'] = $this->form->build_form ( $this->accounts_form->get_form_admin_fields ( $entitytype, $add_array ['id'] ), $add_array );
		if ($add_array ['id'] != '') {
			$data ['page_title'] = gettext ( 'Edit ' . $entity_type );
			if ($this->form_validation->run () == FALSE) {
				$data ['validation_errors'] = validation_errors ();
			} else {
				/*
				 * ****
				 * ASTPP 3.0
				 * Password encode
				 * ****
				 */
				$add_array ['password'] = $this->common->encode ( $add_array ['password'] );
				/* * ****************** */
				$add_array ['credit_limit'] = $this->common_model->add_calculate_currency ( $add_array ['credit_limit'], '', '', false, false );
				$query = $this->accounts_model->remove_all_account_tax ( $add_array ['id'] );
				if (isset ( $add_array ['tax_id'] )) {
					foreach ( $add_array ['tax_id'] as $key => $val ) {
						$data1 = array (
								'accountid' => $add_array ['id'],
								'taxes_id' => $val
						);
						$this->accounts_model->add_account_tax ( $data1 );
					}
					unset ( $add_array ['tax_id'] );
				}
				unset ( $add_array ['number'] );
				$this->accounts_model->edit_account ( $add_array, $add_array ['id'] );
				if ($add_array ['id'] == $accountinfo ['id']) {
					$result = $this->db->get_where ( 'accounts', array (
							'id' => $add_array ['id']
					) );
					$result = $result->result_array ();
					$this->session->set_userdata ( 'accountinfo', $result [0] );
				}
				$this->session->set_flashdata ( 'astpp_errormsg', ucfirst ( $entity_type ) . ' updated successfully!' );
				redirect ( base_url () . 'accounts/admin_list/' );
				exit ();
			}
			$this->load->view ( 'view_admin_details', $data );
		} else {
			$data ['page_title'] = gettext ( 'Create ' . ucfirst ( $entity_type ) );
			if ($this->form_validation->run () == FALSE) {
				$data ['validation_errors'] = validation_errors ();
			} else {
				$add_array ['password'] = $this->common->encode ( $add_array ['password'] );
				$last_id = $this->accounts_model->add_account ( $add_array );
				if (isset ( $add_array ['tax_id'] )) {
					foreach ( $add_array ['tax_id'] as $key => $val ) {
						$data1 = array (
								'accountid' => $last_id,
								'taxes_id' => $val
						);
						$this->accounts_model->add_account_tax ( $data1 );
					}
					unset ( $add_array ['tax_id'] );
				}
				$add_array ['credit_limit'] = $this->common_model->add_calculate_currency ( $add_array ['credit_limit'], '', '', false, false );

				$this->session->set_flashdata ( 'astpp_errormsg', ucfirst ( $entity_type ) . ' added successfully!' );
				redirect ( base_url () . 'accounts/admin_list/' );
				exit ();
			}
			$this->load->view ( 'view_accounts_create', $data );
		}
	}
	function subadmin_add($type = "") {
		$this->admin_add ( 4 );
	}
	function subadmin_edit($edit_id = '') {
		$this->admin_edit ( $edit_id );
	}
	function subadmin_save() {
		$add_array = $this->input->post ();
		$this->admin_save ( $add_array );
	}
	function chargelist_json($accountid) {
		$json_data = array ();
		$sweeplist = $this->common_model->get_sweep_list ();

		$select = "charges.description,charges.charge,charges.sweep";
		$table = "charges";
		$jionTable = array (
				'charge_to_account',
				'accounts'
		);
		$jionCondition = array (
				'charges.id = charge_to_account.charge_id',
				'accounts.number = charge_to_account.cardnum'
		);
		$type = array (
				'left',
				'inner'
		);
		$where = array (
				'accounts.accountid' => $accountid
		);
		$order_type = 'charges.id';
		$order_by = "ASC";

		$account_charge_count = $this->db_model->getCountWithJion ( $table, $select, $where, $jionTable, $jionCondition, $type );

		$count_all = $account_charge_count;
		$config ['total_rows'] = $count_all;
		$config ['per_page'] = $_GET ['rp'];

		$page_no = $_GET ['page'];
		$json_data ['page'] = $page_no;

		$json_data ['total'] = $config ['total_rows'];
		$perpage = $config ['per_page'];
		$start = ($page_no - 1) * $perpage;
		if ($start < 0)
			$start = 0;

		$account_charge_list = $this->db_model->getAllJionQuery ( $table, $select, $where, $jionTable, $jionCondition, $type, $perpage, $start, $order_by, $order_type, "" );
		if ($account_charge_list->num_rows () > 0) {
			foreach ( $account_charge_list->result () as $key => $charges_value ) {
				$json_data ['rows'] [] = array (
						'cell' => array (
								$charges_value->description,
								$charges_value->charge,
								$sweeplist [$charges_value->sweep]
						)
				);
			}
		}
		echo json_encode ( $json_data );
	}
	function admin_list() {
		$data ['username'] = $this->session->userdata ( 'user_name' );
		$data ['page_title'] = gettext ( 'Admins' );
		$data ['search_flag'] = true;
		$data ['cur_menu_no'] = 1;
		$this->session->set_userdata ( 'advance_search', 0 );
		$data ['grid_fields'] = $this->accounts_form->build_account_list_for_admin ();
		$data ["grid_buttons"] = $this->accounts_form->build_grid_buttons_admin ();
		$data ['form_search'] = $this->form->build_serach_form ( $this->accounts_form->get_admin_search_form () );
		$this->load->view ( 'view_accounts_list', $data );
	}

	/**
	 * -------Here we write code for controller accounts functions account_list------
	 * Listing of Accounts table data through php function json_encode
	 */
	function admin_list_json() {
		$json_data = array ();
		$account_data = $this->session->userdata ( "accountinfo" );
		$reseller_id = $account_data ['type'] == 1 ? $account_data ['id'] : 0;
		$count_all = $this->accounts_model->get_admin_Account_list ( false, '', '', $reseller_id );
		$paging_data = $this->form->load_grid_config ( $count_all, $_GET ['rp'], $_GET ['page'] );
		$json_data = $paging_data ["json_paging"];

		$query = $this->accounts_model->get_admin_Account_list ( true, $paging_data ["paging"] ["start"], $paging_data ["paging"] ["page_no"], $reseller_id );
		$grid_fields = json_decode ( $this->accounts_form->build_account_list_for_admin () );
		$json_data ['rows'] = $this->form->build_grid ( $query, $grid_fields );

		echo json_encode ( $json_data );
	}

	/**
	 * ASTPP 3.0
	 * Customer Batch Update.
	 */
	function customer_batch_update() {
		$batch_update_arr = $this->input->post ();
		$result = $this->accounts_model->customer_rates_batch_update ( $batch_update_arr );
		echo json_encode ( array (
				"SUCCESS" => "Customer batch updated successfully!"
		) );
		exit ();
	}

	/* * ********************************************************************* */
	function reseller_list() {
		$data ['username'] = $this->session->userdata ( 'user_name' );
		$data ['page_title'] = gettext ( 'Resellers' );
		$data ['search_flag'] = true;
		/**
		 * ASTPP 3.0
		 * Reseller Batch Update
		 */
		$data ['batch_update_flag'] = true;
		/* * *************************************** */
		$this->session->set_userdata ( 'advance_search', 0 );
		$data ['grid_fields'] = $this->accounts_form->build_account_list_for_reseller ();
		$data ["grid_buttons"] = $this->accounts_form->build_grid_buttons_reseller ();
		$data ['form_search'] = $this->form->build_serach_form ( $this->accounts_form->get_reseller_search_form () );
		$data ['form_batch_update'] = $this->form->build_batchupdate_form ( $this->accounts_form->reseller_batch_update_form () );
		/* * ************************************************************************************************************ */
		$this->load->view ( 'view_accounts_list', $data );
	}

	/**
	 * -------Here we write code for controller accounts functions account_list------
	 * Listing of Accounts table data through php function json_encode
	 */
	function reseller_list_json() {
		$json_data = array ();
		$count_all = $this->accounts_model->get_reseller_Account_list ( false );
		$paging_data = $this->form->load_grid_config ( $count_all, $_GET ['rp'], $_GET ['page'] );
		$json_data = $paging_data ["json_paging"];

		$query = $this->accounts_model->get_reseller_Account_list ( true, $paging_data ["paging"] ["start"], $paging_data ["paging"] ["page_no"] );
		$grid_fields = json_decode ( $this->accounts_form->build_account_list_for_reseller () );
		$json_data ['rows'] = $this->form->build_grid ( $query, $grid_fields );

		echo json_encode ( $json_data );
	}
	function reseller_list_search() {
		$ajax_search = $this->input->post ( 'ajax_search', 0 );
		if ($this->input->post ( 'advance_search', TRUE ) == 1) {
			$this->session->set_userdata ( 'advance_search', $this->input->post ( 'advance_search' ) );
			$action = $this->input->post ();
			unset ( $action ['action'] );
			unset ( $action ['advance_search'] );
			if (isset ( $action ['balance'] ['balance'] ) && $action ['balance'] ['balance'] != '') {
				$action ['balance'] ['balance'] = $this->common_model->add_calculate_currency ( $action ['balance'] ['balance'], "", '', true, false );
			}
			if (isset ( $action ['credit_limit'] ['credit_limit'] ) && $action ['credit_limit'] ['credit_limit'] != '') {
				$action ['credit_limit'] ['credit_limit'] = $this->common_model->add_calculate_currency ( $action ['credit_limit'] ['credit_limit'], "", '', true, false );
			}
			$this->session->set_userdata ( 'reseller_list_search', $action );
		}
		if (@$ajax_search != 1) {
			redirect ( base_url () . 'accounts/reseller_list/' );
		}
	}
	function reseller_batch_update() {
		$batch_update_arr = $this->input->post ();
		$result = $this->accounts_model->reseller_rates_batch_update ( $batch_update_arr );
		echo json_encode ( array (
				"SUCCESS" => "Reseller batch updated successfully!"
		) );
		exit ();
	}
	function admin_list_clearsearchfilter() {
		$this->session->set_userdata ( 'advance_search', 0 );
		$this->session->set_userdata ( 'admin_list_search', "" );
	}
	function admin_list_search() {
		$ajax_search = $this->input->post ( 'ajax_search', 0 );
		if ($this->input->post ( 'advance_search', TRUE ) == 1) {
			$this->session->set_userdata ( 'advance_search', $this->input->post ( 'advance_search' ) );
			$action = $this->input->post ();
			unset ( $action ['action'] );
			unset ( $action ['advance_search'] );
			$this->session->set_userdata ( 'admin_list_search', $action );
		}
		if (@$ajax_search != 1) {
			redirect ( base_url () . 'accounts/admin_list/' );
		}
	}
	function reseller_list_clearsearchfilter() {
		$this->session->set_userdata ( 'advance_search', 0 );
		$this->session->set_userdata ( 'reseller_list_search', "" );
	}
	function customer_delete($id) {
		$accountinfo = $this->session->userdata ( 'accountinfo' );
		$reseller_id = ($accountinfo ['type'] == 1 || $accountinfo ['type'] == 5) ? $accountinfo ['id'] : 0;
		$where = array (
				'id' => $id,
				"reseller_id" => $reseller_id
		);
		$account_res = $this->db_model->getSelect ( "type", "accounts", $where );
		if($account_res->num_rows > 0 ){
			$this->common->customer_delete_dependencies ( $id );
			$this->session->set_flashdata ( 'astpp_notification', 'Customer removed successfully!' );
		
		}else{
			$this->session->set_flashdata ( 'astpp_notification','Permission Denied!');
		}	
		redirect ( base_url () . 'accounts/customer_list/' );
	}
	function reseller_delete($id) {
		$accountinfo = $this->session->userdata ( 'accountinfo' );
		$reseller_id = ($accountinfo ['type'] == 1 || $accountinfo ['type'] == 5) ? $accountinfo ['id'] : 0;
		$where = array (
				'id' => $id,
				"reseller_id" => $reseller_id
		);
		$account_res = $this->db_model->getSelect ( "type", "accounts", $where );
		if($account_res->num_rows > 0 ){
			$this->common->subreseller_list ( $id );
			$this->session->set_flashdata ( 'astpp_notification', 'Reseller removed successfully!' );
		}else{
			$this->session->set_flashdata ( 'astpp_notification','Permission Denied!');
		}	
		redirect ( base_url () . 'accounts/reseller_list/' );
	}
	function free_customer_did($accountid) {
		$this->db->where ( array (
				"accountid" => $accountid
		) );
		$this->db->update ( "dids", array (
				'accountid' => "0"
		) );
		return true;
	}
	function free_ani_map($accountid) {
		$this->db->where ( array (
				"accountid" => $accountid
		) );
		$this->db->delete ( 'ani_map' );
		return true;
	}
	function free_reseller_did($ids) {
		$accountinfo = $this->session->userdata ( 'accountinfo' );
		$reseller_id = $accountinfo ['type'] != 1 ? 0 : $accountinfo ['id'];
		$data = array (
				'parent_id' => $reseller_id,
				'accountid' => 0
		);
		$where = "parent_id IN ($ids)";
		$this->db->where ( $where );
		$this->db->update ( 'dids', $data );
		$where = "reseller_id IN ($ids)";
		$this->db->where ( $where );
		$this->db->delete ( 'reseller_pricing' );
		return true;
	}
	function provider_delete($id) {
		$accountinfo = $this->session->userdata ( 'accountinfo' );
		$where = array (
				'id' => $id,
				"reseller_id" => 0
		);
		$account_res = $this->db_model->getSelect ( "type", "accounts", $where );
		if($account_res->num_rows > 0 ){
			$this->common->customer_delete_dependencies ( $id,3 );
			$this->session->set_flashdata ( 'astpp_notification', 'Provider removed successfully!' );

		}
		redirect ( base_url () . 'accounts/customer_list/' );
	}
	function admin_delete($id) {
		$this->accounts_model->remove_customer ( $id,2);
		$this->session->set_flashdata ( 'astpp_notification', 'Admin removed successfully!' );
		redirect ( base_url () . 'accounts/admin_list/' );
	}
	function subadmin_delete($id) {
		$this->accounts_model->remove_customer ( $id,4);
		$this->session->set_flashdata ( 'astpp_notification', 'Sub admin removed successfully!' );
		redirect ( base_url () . 'accounts/admin_list/' );
	}
	function reseller_details_json($module, $accountid) {
		if ($module == "did") {
			$this->load->module ( 'did/did' );
			$this->did->reseller_did ( $accountid, "reseller" );
		}
		if ($module == "invoices") {
			$this->load->module ( 'invoices/invoices' );
			$this->invoices->customer_invoices ( $accountid, "reseller" );
		}
		if ($module == "charges") {
			$this->load->module ( 'charges/charges' );
			$this->charges->customer_charge_list ( $accountid, "reseller" );
		}
		if ($module == 'packages') {
			$this->load->module ( 'package/package' );
			$this->package->package_list_reseller ( $accountid, "reseller" );
		}
	}
	function provider_details_json($module, $accountid) {
		if ($module == "invoices") {
			$this->load->module ( 'invoices/invoices' );
			$this->invoices->customer_invoices ( $accountid );
		}
	}
	function customer_add_postcharges($accounttype, $accountid) {
		$accountinfo = $this->session->userdata ( 'accountinfo' );
		$reseller_id = ($accountinfo ['type'] == 1 || $accountinfo ['type'] == 5) ? $accountinfo ['id'] : 0;
		$where = array (
				'id' => $edit_id,
				"reseller_id" => $reseller_id
		);
		$account_res = $this->db_model->getSelect ( "type", "accounts", $where );
		if ($account_res->num_rows () > 0) {
			$charge = $this->input->post ( "amount", true );
			if ($charge != "") {
				$charge = $this->common_model->add_calculate_currency ( $charge, "", '', false, false );
				$date = date ( 'Y-m-d H:i:s' );
				$insert_arr = array (
					"accountid" => $accountid,
					"description" => $this->input->post ( "desc", true ),
					"created_date" => $date,
					"debit" => $charge,
					"charge_type" => "post_charge"
				);
				$this->db->insert ( "invoice_item", $insert_arr );
				$this->accounts_model->update_balance ( $charge, $accountid, "debit" );
				redirect ( base_url () . "accounts/" . $accounttype . "_edit/$accountid#packages" );
			} else {
				redirect ( base_url () . "accounts/" . $accounttype . "_edit/$accountid#packages" );
			}
		}else{
			$this->session->set_flashdata ( 'astpp_notification','Permission Denied!');
			redirect ( base_url () . "accounts/" . $accounttype . "_list/" );
		}
	}
	function reseller_did_action($action, $accountid, $accounttype, $did_id = "") {
		$did_id = $this->input->post ( "free_did_list", true );
		$accountinfo = $this->session->userdata('accountinfo');
		if ($action == "add" && !empty($did_id)) {
				$account_query = $this->db_model->getSelect ( "*", "accounts", array (
						"id" => $accountid
				) );
				$account_arr = $account_query->result_array ();
				$idofaccount = $accountid;
				$this->db_model->update ( "dids", array (
						"parent_id" => $accountid,
						'assign_date' => gmdate ( 'Y-m-d H:i:s' )
				), array (
						"id" => $did_id
				) );
				$accountid = $idofaccount;
				$this->load->module ( 'did/did' );
				$this->did->did_model->add_reseller_pricing ( $accountid, $did_id );

				$this->session->set_flashdata ( 'astpp_errormsg', 'DID added successfully.' );
				redirect ( base_url () . "accounts/" . $accounttype . "_edit/$accountid#did" );
			} else {
				redirect ( base_url () . "accounts/" . $accounttype . "_edit/$accountid#did" );
			}
		if ($action == "delete") {
			$this->db->where ( 'id', $did_id );
			$this->db->select ( 'note' );
			$pricing_res = $this->db->get ( 'reseller_pricing' );
			if ($pricing_res->num_rows () > 0) {
				$pricing_res = $pricing_res->result_array ();
				$did_number = $pricing_res [0] ['note'];
				$accountinfo = $this->session->userdata ( 'accountinfo' );
				if ($this->session->userdata ['userlevel_logintype'] == - 1) {
					$parent_id = 0;
				} else {
					$parent_id = $accountinfo['reseller_id'] ;
				}

				$reseller_ids = $this->common->subreseller_list ( $accountinfo ['id'] );
				$pricing_where = "parent_id = $parent_id AND note = $did_number";
				$this->db->where ( $pricing_where );
				$this->db->delete ( 'reseller_pricing' );
				$dids_where = "parent_id IN ($reseller_ids) AND number = $did_number";
				$this->db->where ( $dids_where );
				$data = array (
						'accountid' => 0,
						'parent_id' => $accountinfo ['id']
				);
				$this->db->update ( 'dids', $data );
				$this->session->set_flashdata ( 'astpp_notification', 'DID removed successfully.' );
			} else {
				$this->session->set_flashdata ( 'astpp_notification', 'DID already removed before.' );
			}
			redirect ( base_url () . "accounts/" . $accounttype . "_edit/$accountid#did" );
		}
	}
	function customer_selected_delete() {
		$ids = $this->input->post ( "selected_ids", true );
		$customer_ids = explode ( ",", $ids );
		foreach ( $customer_ids as $customer_id ) {
			$customer_id = str_replace ( "'", "", $customer_id );
			$this->common->customer_delete_dependencies ( $customer_id );
		}
		echo TRUE;
	}
	function reseller_selected_delete() {
		$ids = $this->input->post ( "selected_ids", true );
		$id_arr = explode ( ',', $ids );
		foreach ( $id_arr as $data ) {
			$data = str_replace ( "'", "", $data );
			$this->common->subreseller_list ( $data );
		}
		echo TRUE;
	}
	function callshop_selected_delete() {
		echo $this->delete_multiple ();
	}
	function provider_selected_delete() {
		echo $this->delete_multiple ();
	}
	function subadmin_selected_delete() {
		echo $this->delete_multiple ();
	}
	function admin_selected_delete() {
		echo $this->delete_multiple ();
	}
	function delete_multiple() {
		$ids = $this->input->post ( "selected_ids", true );
		$where = "id IN ($ids)";
		$data = array (
				'deleted' => 1
		);
		$this->db->where ( $where );
		$this->db->where ( 'type <>', '-1' );
		$this->db->update ( "accounts", $data );
		echo TRUE;
	}
	function customer_account_taxes($action = false, $id = false) {
		$data ['username'] = $this->session->userdata ( 'user_name' );
		$data ['page_title'] = gettext ( 'Account Taxes' );

		if ($action == false)
			$action = "list";

		if ($action == 'list') {
			$this->load->view ( 'view_account_taxes_list', $data );
		} elseif ($action == 'add') {

			if (($this->input->post ())) {
				$post_array = $this->input->post ();
				$query = $this->accounts_model->remove_all_account_tax ( $post_array ['account_id'] );

				foreach ( $post_array as $key => $value ) {
					$id = explode ( "_", $key );
					if ($id [0] == 'tax') {
						$data = array (
								'accountid' => $post_array ['account_id'],
								'taxes_id' => $post_array [$key]
						);
						$this->accounts_model->add_account_tax ( $data );
					}
				}
				$this->session->set_flashdata ( 'astpp_errormsg', 'Account tax added successfully!' );
				redirect ( base_url () . 'accounts/customer_list/' );
			}
			$data ['id'] = array ();
			$data ['taxesList'] = $this->common_model->get_list_taxes ();
			$this->load->view ( 'view_accounting_taxes_add', $data );
		} elseif ($action == 'edit') {
			$taxes_id = $this->accounts_model->get_accounttax_by_id ( $id );
			$account_num = $this->accounts_model->get_account_number ( $id );
			$data ['accountnum'] = $account_num ['number'];
			$data ['account_id'] = $id;
			for($i = 0; $i < count ( $taxes_id ); $i ++) {
				$tax_ids [] = $taxes_id [$i] ['taxes_id'];
			}
			$data ['tax_ids'] = $tax_ids;

			$data ['tax_id'] = $taxes_id;

			if (($this->input->post ())) {
				$post_array = $this->input->post ();
				$accountinfo = $this->accounts_model->get_account_by_number ( $post_array ['account_id'] );
				$query = $this->accounts_model->remove_all_account_tax ( $post_array ['account_id'] );
				foreach ( $post_array as $key => $value ) {
					$id = explode ( "_", $key );
					if ($id [0] == 'tax') {
						$data = array (
								'accountid' => $post_array ['account_id'],
								'taxes_id' => $post_array [$key]
						);
						$this->accounts_model->add_account_tax ( $data );
					}
				}
				if ($accountinfo ['type'] == '0') {
					$link = base_url () . '/accounts/customer_list/';
				} else {
					$link = base_url () . '/accounts/reseller_list/';
				}
				$this->session->set_flashdata ( 'astpp_errormsg', 'Account tax added successfully!' );
				redirect ( $link );
			}
			$data ['taxesList'] = $this->common_model->get_list_taxes ();
			$this->load->view ( 'view_accounting_taxes_add', $data );
		} elseif ($action == 'delete') {
			$this->accounting_model->remove_account_tax ( $id );
			$this->session->set_flashdata ( 'astpp_notification', 'Account tax removed successfully!' );
			redirect ( base_url () . 'accounting/account_taxes/' );
		}
	}

	/**
	 * -------Here we write code for controller accounting functions vallid_account_tax------
	 * here this function called by ajax form and vallidate the account number
	 * @$_POST['username']: Account Number
	 */
	function valid_account_tax() {
		$tax_id = '';
		if (! empty ( $_POST ['username'] )) {
			$account_num = mysqli_real_escape_string ( get_instance()->db->conn_id, $_POST ['username'] );
			$row = $this->accounts_model->check_account_num ( $account_num );
			if (isset ( $row ['accountid'] ) && $row ['accountid'] != '') {
				$taxes_id = $this->accounts_model->get_accounttax_by_id ( $row ['accountid'] );
				if ($taxes_id) {
					foreach ( $taxes_id as $id ) {
						$tax_id .= $id ['taxes_id'] . ",";
					}

					$tax_id = rtrim ( $tax_id, "," );
					echo $row ['accountid'] . ',' . $tax_id;
				} else {
					echo $row ['accountid'];
				}
			}
		}
	}
	function reseller_edit_account() {
		$account_data = $this->session->userdata ( "accountinfo" );

		$add_array = $this->input->post ();
		$data ['form'] = $this->form->build_form ( $this->accounts_form->get_reseller_own_form_fields (), $add_array );
		if ($add_array ['id'] != '') {
			$data ['page_title'] = gettext ( 'Edit Reseller' );
			if ($this->form_validation->run () == FALSE) {
				$data ['validation_errors'] = validation_errors ();
			} else {
				$this->accounts_model->edit_account ( $add_array, $add_array ['id'] );
				$accountinfo = $this->session->userdata ( 'accountinfo' );
				if ($add_array ['id'] == $accountinfo ['id']) {
					$result = $this->db->get_where ( 'accounts', array (
							'id' => $add_array ['id']
					) );
					$result = $result->result_array ();
					$this->session->set_userdata ( 'accountinfo', $result [0] );
				}
				$this->session->set_flashdata ( 'astpp_errormsg', 'Reseller updated successfully!' );
				redirect ( base_url () . '/dashboard/' );
			}
			$this->load->view ( 'view_reseller_edit_details_own', $data );
		} else {
			$data ['page_title'] = gettext ( 'Edit Reseller' );
			$where = array (
					'id' => $account_data ["id"]
			);
			$account = $this->db_model->getSelect ( "*", "accounts", $where );
			$data ["account_data"] = $account->result_array ();
			foreach ( $account->result_array () as $key => $value ) {
				$editable_data = $value;
			}
			$data ['form'] = $this->form->build_form ( $this->accounts_form->get_reseller_own_form_fields (), $editable_data );
			$this->load->view ( 'view_reseller_edit_details_own', $data );
		}
	}
	/**
	 * -------Here we write code for controller accounts functions add_callerid------
	 * Add caller ids against account no
	 * @account_number: Account No
	 */
	function customer_animap_list($id = '') {
		$data ['animap_id'] = $id;
		$data ['username'] = $this->session->userdata ( 'user_name' );
		$data ['page_title'] = gettext ( "Caller Id List" );
		$this->session->set_userdata ( 'animap_search', 0 );
		$data ['grid_fields'] = $this->accounts_form->build_animap_list ();
		$data ["grid_buttons"] = $this->accounts_form->build_grid_buttons_destination ();
		$this->load->view ( 'view_ani_map', $data );
	}
	function customer_animap_list_json($id = '') {
		$json_data = array ();
		$count_all = $this->accounts_model->get_animap ( false, '', '', $id );
		$data ['callingcard_id'] = $id;
		$paging_data = $this->form->load_grid_config ( $count_all, $_GET ['rp'], $_GET ['page'] );
		$json_data = $paging_data ["json_paging"];
		$query = $this->accounts_model->get_animap ( true, $paging_data ["paging"] ["start"], $paging_data ["paging"] ["page_no"], $id );
		$grid_fields = json_decode ( $this->accounts_form->build_animap_list () );
		$json_data ['rows'] = $this->form->build_grid ( $query, $grid_fields );
		echo json_encode ( $json_data );
		exit ();
	}
	function customer_animap_list_action($id = '') {
		$add_array = $this->input->post ();
		$add_array ['id'] = trim ( $add_array ['id'] );
		$add_array ['number'] = trim ( $add_array ['number'] );
		if (isset ( $add_array ['id'] ) && $add_array ['id'] != '') {
			$add_array ['id'] = trim ( $add_array ['id'] );
			$id = $add_array ['id'];
		}
		$where = array (
				"number" => $add_array ['number']
		);
		$pro = $this->accounts_model->animap_authentication ( $where, $id );
		if ($pro > 0) {
			echo "2";
			exit ();
		}
		if (isset ( $add_array ['number'] ) && ! empty ( $add_array ['number'] )) {
			if (isset ( $add_array ['id'] ) && $add_array ['id'] != '') {
				unset ( $add_array ['animap_id'] );
				$response = $this->accounts_model->edit_animap ( $add_array, $add_array ['id'] );
				echo "1";
				exit ();
			} else {
				$add_array ['context'] = "default";
				unset ( $add_array ['animap_id'] );
				$add_array ['accountid'] = $id;
				$response = $this->accounts_model->add_animap ( $add_array );
				echo "0";
				exit ();
			}
		} else {
			echo "3";
			exit ();
		}
	}
	function customer_animap_list_remove($id) {
		$this->accounts_model->remove_ani_map ( $id );
		echo "1";
		exit ();
	}
	function customer_animap_list_edit($id) {
		$where = array (
				'id' => $id
		);
		$account = $this->db_model->getSelect ( "*", "ani_map", $where );
		foreach ( $account->result_array () as $key => $value ) {
			$edit_data = $value;
		}
		$value_edit = '';
		foreach ( $edit_data as $key => $value ) {
			$value_edit .= $value . ",";
		}
		echo rtrim ( $value_edit, ',' );
		exit ();
	}
	function provider_edit_account() {
		$this->customer_edit_account ();
	}
	function customer_show_password($id) {
		$account = $this->db_model->getSelect ( "password", "accounts", array (
				'id' => $id
		) );
		$account_data = $account->result_array ();
		$password = $this->common->decode ( $account_data [0] ['password'] );
		echo $password;
	}
	function reseller_export_cdr_xls() {
		$account_info = $accountinfo = $this->session->userdata ( 'accountinfo' );
		$currency_id = $account_info ['currency_id'];
		$currency = $this->common->get_field_name ( 'currency', 'currency', $currency_id );
		ob_clean ();
		$query = $this->accounts_model->get_reseller_Account_list ( true, '', '', true );
		$customer_array [] = array (
				gettext ( "Account" ),
				gettext ( "First Name" ),
				gettext ( "Last Name" ),
				gettext ( "Company" ),
				gettext ( "Rate Group" ),
				gettext ( "Account Type" ),
				gettext ( "Balance" ) . "(" . $currency . ")",
				gettext ( "Credit Limit" ) . "(" . $currency . ")",
				gettext ( "Status" ),
				gettext ( "Created Date" )
		);
		if ($query->num_rows () > 0) {

			foreach ( $query->result_array () as $row ) {
				$customer_array [] = array (
						$row ['number'],
						$row ['first_name'],
						$row ['last_name'],
						$row ['company_name'],
						$this->common->get_field_name ( 'name', 'pricelists', $row ['pricelist_id'] ),
						$this->common->get_account_type ( '', '', $row ['posttoexternal'] ),
						$this->common_model->calculate_currency ( $row ['balance'], false, false ),
						$this->common_model->calculate_currency ( $row ['credit_limit'], false, false ),
						$this->common->get_status ( 'export', '', $row ['status'] ),
						$row ['creation']
				);
			}
		}

		$this->load->helper ( 'csv' );
		array_to_csv ( $customer_array, 'Resellers_' . date ( "Y-m-d" ) . '.csv' );
	}
	function customer_validate_ip() {
		$add_array = $this->input->post ();
		if (! empty ( $add_array )) {
			$ip = $add_array ['ip'];
			if (strpos ( $ip, '/' ) !== false) {
				$add_array ['ip'] = $add_array ['ip'];
			} else {
				$add_array ['ip'] = $add_array ['ip'] . '/32';
			}
			$this->db->where ( 'ip', $add_array ['ip'] );
			$this->db->where ( 'prefix', $add_array ['prefix'] );
			$this->db->select ( 'count(ip) as count' );
			$ip_map_result = ( array ) $this->db->get ( 'ip_map' )->first_row ();
			if ($ip_map_result ['count'] > 0) {
				echo 'FALSE';
			} else {
				echo 'TRUE';
			}
		} else {
			echo 'FALSE';
		}
	}
	function reseller_invoice_config($id = '') {
		$data ['page_title'] = gettext ( 'Company Profile' );
		$accountinfo = $this->session->userdata ( "accountinfo" );
		$add_array = $this->input->post ();
		$data ["account_data"] = $add_array;
		if (isset ( $add_array ['submit'] )) {
			if ($_FILES ['file'] ['name'] == '') {
				$this->load->module ( 'invoices/invoices' );
				$this->load->model ( "invoices_model" );
				$invoiceconf = $this->invoices_model->get_invoiceconf ();
				$file_name = ($invoiceconf ['logo'] != '') ? $invoiceconf ['logo'] : '';
			}
			if (isset ( $_FILES ['file'] ['name'] ) && $_FILES ['file'] ['name'] != '') {
				$files = $_FILES ['file'];
				if ($files ['size'] < 0) {
					$this->session->set_flashdata ( 'astpp_notification', 'PLease upload maximum file' );
					redirect ( base_url () . "accounts/reseller_invoice_config/" . $add_array ['accountid'] . "/" );
				}
				$file = $_FILES ['file'];
				$uploadedFile = $file ["tmp_name"];
				$file_name = $file ['name'];
				$file_type = $file ['type'];
				if ($file_type == 'image/jpg' || $file_type == 'image/png' || $file_type == 'image/jpeg') {
					$dir_path = FCPATH . "upload/";
					$path = $dir_path . $add_array ['accountid'] . "_" . $file ['name'];
					if (move_uploaded_file ( $uploadedFile, $path )) {
						$this->session->set_flashdata ( 'astpp_errormsg', gettext ( 'files added successfully!' ) );
					} else {
						$this->session->set_flashdata ( 'astpp_notification', "File Uploading Fail Please Try Again" );
						redirect ( base_url () . "accounts/reseller_invoice_config/" . $add_array ['accountid'] . "/" );
					}
				} else {
					$this->session->set_flashdata ( 'astpp_notification', 'Please upload only image!' );
					redirect ( base_url () . "accounts/reseller_invoice_config/" . $add_array ['accountid'] . "/" );
				}
			}
			$add_array ['logo'] = $file_name;
			unset ( $add_array ['submit'] );
			if ($add_array ['id'] == '') {
				$this->accounts_model->add_invoice_config ( $add_array );
			} else {
				$this->accounts_model->edit_invoice_config ( $add_array, $add_array ['id'] );
			}
			$this->session->set_flashdata ( 'astpp_errormsg', 'Invoice config updated successfully!' );
			redirect ( base_url () . "accounts/reseller_invoice_config/" . $add_array ['accountid'] . "/" );
		} else {
			$data ["account_data"] = ( array ) $this->db->get_where ( 'invoice_conf', array (
					"accountid" => $id
			) )->first_row ();
			$data ["accounttype"] = 'Reseller';
			$data ["edit_id"] = $id;
			if (isset ( $data ["account_data"] ['logo'] )) {
				$data ["account_data"] ['file'] = $id . "_" . $data ["account_data"] ['logo'];
			}
			$this->load->view ( 'view_reseller_invoices_config', $data );
		}
	}
	function reseller_invoice_logo_delete($accountid) {
		$invoiceconf = $this->db_model->getSelect ( "*", "invoice_conf", array (
				"accountid" => $accountid
		) );
		$result = $invoiceconf->result_array ();
		$logo = $result [0] ['logo'];
		$post_arr = array (
				'logo' => ''
		);
		$where_arr = array (
				'logo' => $logo
		);
		$this->db->where ( $where_arr );
		$this->db->update ( 'invoice_conf', $post_arr );
	}
	function customer_global_grid_list() {
		echo gettext ( $_POST ['display'] );
	}
}
?>

