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
class User extends MX_Controller {
	function User() {
		parent::__construct ();
		$this->load->helper ( 'template_inheritance' );
		$this->load->helper ( 'form' );
		$this->load->library ( "astpp/form" );
		$this->load->library ( "user_form" );
		$this->load->library ( "astpp/permission" );
		$this->load->model ( 'Auth_model' );
		$this->load->model ( 'Astpp_common' );
		$this->load->model ( 'user_model' );
		$this->load->library ( 'did_lib' );
	}
	function index() {
		if ($this->session->userdata ( 'user_login' ) == FALSE)
			redirect ( base_url () . 'login/login' );
		$data ['page_title'] = gettext ( 'Dashboard' );
		$this->load->view ( 'view_user_dashboard', $data );
	}
	function user_dashboard_recent_payments() {
		$result = $this->user_model->user_dashboard_recent_recharge_info ();
		$gmtoffset = $this->common->get_timezone_offset ();
		$i = 0;
		$json_data = array ();
		if ($result->num_rows () > 0) {
			$account_arr = $this->common->get_array ( 'id,number,first_name,last_name', 'accounts', '' );
			$json_data [0] ['accountid'] = 'Accounts';
			$json_data [0] ['credit'] = 'Amount';
			$json_data [0] ['payment_date'] = 'Date';
			$json_data [0] ['notes'] = 'Notes';
			foreach ( $result->result_array () as $key => $data ) {
				$current_timestamp = strtotime ( $data ['payment_date'] );
				$modified_date = $current_timestamp + $gmtoffset;
				$data ['accountid'] = ($data ['accountid'] != '' && isset ( $account_arr [$data ['accountid']] )) ? $account_arr [$data ['accountid']] : "Anonymous";
				$json_data [$i] ['accountid'] = $data ['accountid'];
				$json_data [$i] ['credit'] = $this->common_model->calculate_currency ( $data ['credit'], '', '', true, false );
				$json_data [$i] ['payment_date'] = date ( 'Y-m-d H:i:s', strtotime ( $data ['payment_date'] ) + $gmtoffset );
				$json_data [$i] ['notes'] = $data ['notes'];
				$i ++;
			}
		}
		echo json_encode ( $json_data );
	}
	function user_dashboard_package_data() {
		$accountinfo = $this->session->userdata ( 'accountinfo' );
		$json_data = array ();
		$this->db->where ( 'pricelist_id', $accountinfo ['pricelist_id'] );
		$this->db->select ( '*' );
		$result = $this->db->get ( 'packages', 10 );
		$i = 1;
		if ($result->num_rows () > 0) {
			$json_data [0] ['package_name'] = 'Package Name';
			$json_data [0] ['includedseconds'] = 'Included Seconds';
			$json_data [0] ['status'] = 'Status';
			$result = $result->result_array ();
			foreach ( $result as $data ) {
				$json_data [$i] ['package_name'] = $data ['package_name'];
				$json_data [$i] ['includedseconds'] = $data ['includedseconds'];
				$json_data [$i] ['status'] = $this->common->get_status ( 'export', '', $data ['status'] );
				$i ++;
			}
		}
		echo json_encode ( $json_data );
	}
	function user_dashboard_invoices_data() {
		$accountinfo = $this->session->userdata ( 'accountinfo' );
		$currency = $this->common->get_field_name ( 'currency', 'currency', array (
				"id" => $accountinfo ['currency_id'] 
		) );
		$this->db->where ( 'accountid', $accountinfo ['id'] );
		$this->db->where ( 'confirm', 1 );
		$this->db->select ( '*' );
		$this->db->order_by ( 'invoice_date', 'desc' );
		$result = $this->db->get ( 'invoices', 10 );
		$json_data = array ();
		$gmtoffset = $this->common->get_timezone_offset ();
		if ($result->num_rows () > 0) {
			$result = $result->result_array ();
			$json_data [0] ['type'] = 'Invoice Type';
			$json_data [0] ['id'] = 'Number';
			$json_data [0] ['from_date'] = 'From Date';
			$json_data [0] ['invoice_date'] = 'Generated Date';
			$json_data [0] ['amount'] = 'Amount (' . $currency . ')';
			$i = 1;
			foreach ( $result as $key => $data ) {
				$invoice_prefix = $entity_type = $this->common->get_field_name ( 'invoice_prefix', 'invoices', array (
						'id' => $data ['id'] 
				) );
				
				$invoiceid = $entity_type = $this->common->get_field_name ( 'invoiceid', 'invoices', array (
						'id' => $data ['id'] 
				) );
				$invoice_num = $invoice_prefix . $invoiceid;
				$inv_type = $this->common->get_invoice_total ( 'item_type', '', $data ['id'] );
				if ($inv_type == '') {
					$inv_type = 'Automatically';
				}
				if ($inv_type == 'manual_inv') {
					$inv_type = 'Manually';
				}
				$inv_debit = $this->common->convert_to_currency ( '', '', $data ['amount'] );
				if ($inv_debit == '') {
					$inv_debit = $this->common->convert_to_currency ( '', '', 0 );
				}
				$json_data [$i] ['type'] = $data ['type'];
				$json_data [$i] ['id'] = $invoice_num;
				$json_data [$i] ['from_date'] = date ( 'Y-m-d H:i:s', strtotime ( $data ['from_date'] ) + $gmtoffset );
				$json_data [$i] ['invoice_date'] = date ( 'Y-m-d H:i:s', strtotime ( $data ['invoice_date'] ) + $gmtoffset );
				$json_data [$i] ['amount'] = $inv_debit;
				$i ++;
			}
		}
		echo json_encode ( $json_data );
	}
	function user_dashboard_subscription_data() {
		$accountinfo = $this->session->userdata ( 'accountinfo' );
		$this->db->where ( 'accountid', $accountinfo ['id'] );
		$this->db->select ( '*' );
		$this->db->order_by ( 'assign_date', 'desc' );
		$result = $this->db->get ( 'charge_to_account', 10 );
		$json_data = array ();
		
		$gmtoffset = $this->common->get_timezone_offset ();
		if ($result->num_rows () > 0) {
			$result = $result->result_array ();
			$charge_str = null;
			$charges_arr = array ();
			foreach ( $result as $charges_data ) {
				$charge_str .= $charges_data ['charge_id'] . ",";
			}
			$charge_str = rtrim ( $charge_str, "," );
			$where = "id IN ($charge_str)";
			$this->db->where ( $where );
			$this->db->select ( 'id,description,sweep_id' );
			$charge_result = $this->db->get ( 'charges' );
			foreach ( $charge_result->result_array () as $data ) {
				$charges_arr [$data ['id']] ['description'] = $data ['description'];
				$charges_arr [$data ['id']] ['sweep_id'] = $data ['sweep_id'];
			}
			$json_data [0] ['charge_id'] = 'Charge Name';
			$json_data [0] ['assign_date'] = 'Assign Date';
			$json_data [0] ['sweep_id'] = 'Billing Cycle';
			$i = 1;
			foreach ( $result as $key => $data ) {
				if (isset ( $charges_arr [$data ['charge_id']] ['sweep_id'] )) {
					$sweep_id = $charges_arr [$data ['charge_id']] ['sweep_id'];
				}
				$data ['charge_id'] = isset ( $charges_arr [$data ['charge_id']] ['description'] ) ? $charges_arr [$data ['charge_id']] ['description'] : "Anonymous";
				$json_data [$i] ['charge_id'] = $data ['charge_id'];
				if ($data ['assign_date'] != '0000-00-00 00:00:00') {
					$json_data [$i] ['assign_date'] = date ( 'Y-m-d H:i:s', strtotime ( $data ['assign_date'] ) + $gmtoffset );
				} else {
					$json_data [$i] ['assign_date'] = $data ['assign_date'];
				}
				if (isset ( $sweep_id )) {
					if ($sweep_id == 0) {
						$json_data [$i] ['sweep_id'] = 'Daily';
					} else {
						$json_data [$i] ['sweep_id'] = 'Monthly';
					}
				} else {
					$json_data [$i] ['sweep_id'] = 'Anonymous';
				}
				$i ++;
			}
		}
		echo json_encode ( $json_data );
	}
	function user_edit_account() {
		if ($add_array ['id'] != '') {
			$data ['form'] = $this->form->build_form ( $this->accounts->accounts_form->get_user_form_fields ( $add_array ['id'] ), $add_array );
			$data ['page_title'] = gettext ( 'Edit ' . $entity_name );
			if ($this->form_validation->run () == FALSE) {
				$data ['validation_errors'] = validation_errors ();
			} else {
				$add_array ['password'] = $this->common->encode ( $add_array ['password'] );
				unset ( $add_array ['number'] );
				$this->accounts->accounts_model->edit_account ( $add_array, $add_array ['id'] );
				$accountinfo = $this->session->userdata ( 'accountinfo' );
				if ($add_array ['id'] == $accountinfo ['id']) {
					$this->session->set_userdata ( 'accountinfo', ( array ) $this->db->get_where ( 'accounts', array (
							'id' => $add_array ['id'] 
					) )->first_row () );
				}
				$this->session->set_flashdata ( 'astpp_errormsg', ucfirst ( $entity_name ) . ' updated successfully!' );
				redirect ( base_url () . 'user/user/' );
			}
			$this->load->view ( 'view_user_details', $data );
		} else {
			$data ['page_title'] = gettext ( 'Edit ' . $entity_name );
			$where = array (
					'id' => $account_data ["id"] 
			);
			$account = $this->db_model->getSelect ( "*", "accounts", $where );
			$data ["account_data"] = $account->result_array ();
			
			foreach ( $account->result_array () as $key => $value ) {
				$editable_data = $value;
			}
			$editable_data ['password'] = $this->common->decode ( $editable_data ['password'] );
			$data ['form'] = $this->form->build_form ( $this->accounts->accounts_form->get_user_form_fields ( $editable_data ['id'] ), $editable_data );
			$this->load->view ( 'view_user_details', $data );
		}
	}
	function user_did_edit($edit_id = '') {
		$this->permission->customer_web_record_permission($edit_id,'dids','user/user_didlist/');
		$data ['page_title'] = gettext ( 'Edit DIDs' );
		$account_data = $this->session->userdata ( "accountinfo" );
		$this->db->where ( 'id', $edit_id );
		$this->db->select ( 'id,call_type,extensions,number' );
		$did_info = ( array ) $this->db->get ( 'dids' )->first_row ();
		$did_info ['free_didlist'] = $did_info ['id'];
		$data ['form'] = $this->form->build_form ( $this->user_form->build_user_did_form (), $did_info );
		$this->load->view ( 'view_user_did_edit', $data );
	}
	function user_dids_action($action, $did_id = "") {
		$accountinfo = $this->session->userdata ( 'accountinfo' );
		$reseller_id = $accountinfo ['reseller_id'];
		$did_id = empty ( $did_id ) ? $this->input->post ( "free_didlist", true ) : $did_id;
		
		if ($did_id != '') {
			$account_query = $this->db_model->getSelect ( "*", "accounts", array (
					'id' => $accountinfo ['id'] 
			) );
			$account_arr = ( array ) $account_query->first_row ();
			$did_query = $this->db_model->getSelect ( "*", "dids", array (
					"id" => $did_id 
			) );
			$did_arr = ( array ) $did_query->first_row ();
			if ($action == "add") {
				$this->load->library ( 'did_lib' );
				
				$did_result = $this->did_lib->did_billing_process($this->session->userdata,$accountinfo ['id'],$did_id);
				$astpp_flash_message_type = ($did_result[0] == "SUCCESS")?"astpp_errormsg":"astpp_notification";
				$this->session->set_flashdata ( $astpp_flash_message_type, $did_result[1] );	

				redirect ( base_url () . "user/user_didlist/" );	
			}
			if ($action == "edit") {
				$this->permission->customer_web_record_permission($did_id,'dids','user/user_didlist/');
				$add_array = $this->input->post ();
				$data ['form'] = $this->form->build_form ( $this->user_form->build_user_did_form ( $add_array ['free_didlist'] ), $add_array );
				if ($this->form_validation->run () == FALSE) {
					$data ['validation_errors'] = validation_errors ();
					echo $data ['validation_errors'];
					exit ();
				} else {
					$update_arr = array (
							"call_type" => $add_array ['call_type'],
							"extensions" => $add_array ['extensions'],
							"last_modified_date" => gmdate ( "Y-m-d H:i:s" ) 
					);
					$this->db->update ( "dids", $update_arr, array (
							"id" => $did_id 
					) );
					if ($accountinfo ['reseller_id'] > 0) {
						
						$this->db->update ( 'reseller_pricing', $update_arr, array (
								'note' => $did_arr ['number'] 
						) );
					}
					echo json_encode ( array (
							"SUCCESS" => $did_arr ['number'] . " DID Updated Successfully!" 
					) );
					exit ();
				}
				$this->load->view ( 'view_user_did_edit', $data );
			}
			if ($action == "delete") {
				$this->permission->customer_web_record_permission($did_id,'dids','user/user_didlist/');
				$this->db->update ( "dids", array (
						"accountid" => 0,
						"assign_date" => "0000-00-00 00:00:00",
						'charge_upto' => "0000-00-00 00:00:00" 
				), array (
						"id" => $did_id 
				) );
				$this->common->mail_to_users ( 'email_remove_did', $account_arr, "", $did_arr ['number'] );
				$this->session->set_flashdata ( 'astpp_notification', 'DID Removed Successfully.' );
				redirect ( base_url () . "user/user_didlist/" );
			}
		} else {
			$this->session->set_flashdata ( 'astpp_notification', 'DID not found.' );
			redirect ( base_url () . "user/user_didlist/" );
		}
	}
	function user_rates_list() {
		$data ['username'] = $this->session->userdata ( 'user_name' );
		$data ['page_title'] = gettext ( 'My Rates' );
		$data ['search_flag'] = true;
		$this->session->set_userdata ( 'advance_search', 0 );
		$this->load->module ( 'rates/rates' );
		$data ["grid_buttons"] = $this->user_form->user_rates_list_buttons ();
		$data ['grid_fields'] = $this->user_form->user_rates_list ();
		$data ['form_search'] = $this->form->build_serach_form ( $this->user_form->user_rates_list_search () );
		$this->load->view ( 'view_user_rates_list', $data );
	}
	function user_rates_list_json() {
		$account_data = $this->session->userdata ( "accountinfo" );
		$markup = $this->common->get_field_name ( 'markup', 'pricelists', array (
				'id' => $account_data ["pricelist_id"] 
		) );
		$count_all = $this->user_model->get_user_rates_list ( false );
		$paging_data = $this->form->load_grid_config ( $count_all, $_GET ['rp'], $_GET ['page'] );
		$json_data = $paging_data ["json_paging"];
		
		$query = $this->user_model->get_user_rates_list ( true, $paging_data ["paging"] ["start"], $paging_data ["paging"] ["page_no"] );
		$grid_fields = json_decode ( $this->user_form->user_rates_list () );
		foreach ( $query->result_array () as $key => $value ) {
			$cost = $account_data ['type'] != 3 ? ($value ['cost'] + (($value ['cost'] * $markup) / 100)) : $value ['cost'];
			$json_data ['rows'] [] = array (
					'cell' => array (
							$this->common->get_only_numeric_val ( "", "", $value ["pattern"] ),
							$value ['comment'],
							$this->common_model->calculate_currency ( $value ['connectcost'], '', '', true, false ),
							$value ['includedseconds'],
							$this->common_model->calculate_currency ( ($cost), '', '', true, false ),
							$value ['init_inc'],
							$value ['inc'] 
					) 
			);
		}
		echo json_encode ( $json_data );
	}
	function user_rates_list_search() {
		$ajax_search = $this->input->post ( 'ajax_search', 0 );
		
		if ($this->input->post ( 'advance_search', TRUE ) == 1) {
			$this->session->set_userdata ( 'advance_search', $this->input->post ( 'advance_search' ) );
			$action = $this->input->post ();
			unset ( $action ['action'], $action ['advance_search'] );
			if (isset ( $action ['connectcost'] ['connectcost'] ) && $action ['connectcost'] ['connectcost'] != '') {
				$action ['connectcost'] ['connectcost'] = $this->common_model->add_calculate_currency ( $action ['connectcost'] ['connectcost'], "", '', true, false );
			}
			if (isset ( $action ['cost'] ['cost'] ) && $action ['cost'] ['cost'] != '') {
				$account_data = $this->session->userdata ( "accountinfo" );
				$markup = $this->common->get_field_name ( 'markup', 'pricelists', array (
						'id' => $account_data ["pricelist_id"] 
				) );
				$markup = ($markup > 0) ? $markup : 1;
				$action ['cost'] ['cost'] = $this->common_model->add_calculate_currency ( $action ['cost'] ['cost'], "", '', true, false );
				if ($account_data ['type'] != 3)
					$action ['cost'] ['cost'] = ($action ['cost'] ['cost'] - ($action ['cost'] ['cost'] * $markup) / 100);
			}
			$this->session->set_userdata ( 'user_rates_list_search', $action );
		}
		if ($ajax_search != 1) {
			redirect ( base_url () . 'user/user_rates_list/' );
		}
	}
	function user_rates_list_clearsearchfilter() {
		$this->session->set_userdata ( 'advance_search', 0 );
		$this->session->set_userdata ( 'user_rates_list_search', "" );
	}
	function user_rates_list_export() {
		$account_data = $this->session->userdata ( 'accountinfo' );
		$currency_id = $account_data ['currency_id'];
		$currency = $this->common->get_field_name ( 'currency', 'currency', $currency_id );
		$query = $this->user_model->get_user_rates_list ( true, '', '', false );
		$markup = $this->common->get_field_name ( 'markup', 'pricelists', array (
				'id' => $account_data ["pricelist_id"] 
		) );
		ob_clean ();
		$inbound_array [] = array (
				gettext ( "Code" ),
				gettext ( "Destination" ),
				gettext ( "Connect Cost" ) . "(" . $currency . ")",
				gettext ( "Included Seconds" ),
				gettext ( "Per Minute Cost" ) . "(" . $currency . ")",
				gettext ( "Initital Increment" ),
				gettext ( "Increment" ) 
		);
		if ($query->num_rows () > 0) {
			foreach ( $query->result_array () as $row ) {
				$cost = $account_data ['type'] != 3 ? ($row ['cost'] + ($row ['cost'] * $markup) / 100) : $row ['cost'];
				$inbound_array [] = array (
						$row ['pattern'] = $this->common->get_only_numeric_val ( "", "", $row ["pattern"] ),
						$row ['comment'],
						$this->common_model->calculate_currency ( $row ['connectcost'], '', '', true, false ),
						$row ['includedseconds'],
						$this->common_model->calculate_currency ( $cost, '', '', true, false ),
						$row ['init_inc'],
						$row ['inc'] 
				);
			}
		}
		$this->load->helper ( 'csv' );
		array_to_csv ( $inbound_array, 'Rates_' . date ( "Y-m-d" ) . '.csv' );
	}
	function user_refill($action = "") {
		if (common_model::$global_config ['system_config'] ['paypal_status'] == 1) {
			redirect ( base_url () . 'user/user/' );
		}
		$this->load->module ( "user/refill" );
		if ($action == "GET_AMT") {
			$amount = $this->input->post ( "value", true );
			$this->refill->convert_amount ( $amount );
		} else {
			$this->refill->index ();
		}
	}
	function user_convert_amount($amount) {
		$amount = $this->common_model->add_calculate_currency ( $amount, "", "", false, false );
		echo number_format ( $amount, 5 );
	}
	function user_report_export() {
		$this->load->module ( 'reports/reports' );
		$this->user_cdrreport_export ();
	}
	function change_password() {
		$accountinfo = $this->session->userdata ( 'accountinfo' );
		$id = $accountinfo ['id'];
		$this->load->model ( 'user_model' );
		
		$query = $this->user_model->change_password ( $id );
		foreach ( $query as $row ) {
			$data ['password'] = $row->password;
		}
		$databasepassword = $data ['password'];
		$password = $_POST ['oldpassword'];
		$newpassword = $_POST ['newpassword'];
		$conformpassword = $_POST ['conformpassword'];
		if ($databasepassword == $password) {
			
			if ($conformpassword == $newpassword) {
				$update = $newpassword;
				$this->load->model ( 'user_model' );
				$this->user_model->change_db_password ( $update, $id );
				$this->session->set_flashdata ( 'astpp_errormsg', "Password changed Sucessfully....!!!" );
				redirect ( base_url () . 'user/user/changepassword/' );
			} else {
				$this->session->set_flashdata ( 'astpp_notification', "New Password & Conformpassword not match." );
				redirect ( base_url () . 'user/user/changepassword/' );
			}
		} else {
			$this->session->set_flashdata ( 'astpp_notification', "Invalid old passwword." );
			redirect ( base_url () . 'user/user/changepassword/' );
		}
	}
	function changepassword() {
		$data ['username'] = $this->session->userdata ( 'user_name' );
		$data ['page_title'] = gettext ( 'Change Password' );
		$this->load->view ( 'view_changepassword', $data );
	}
	function user_generate_password() {
		echo $this->common->generate_password ();
	}
	function user_generate_number($digit) {
		echo $this->common->find_uniq_rendno ( $digit, 'number', 'accounts' );
	}
	function user_refill_coupon_list() {
		$data ['username'] = $this->session->userdata ( 'user_name' );
		$data ['page_title'] = gettext ( 'Refill Coupon List' );
		$this->load->module ( 'refill_coupon/refill_coupon' );
		$data ['grid_fields'] = $this->refill_coupon->refill_coupon_form->build_user_refill_coupon_grid ();
		$acc_data = $this->session->userdata ( "accountinfo" );
		$reseller_id = $acc_data ['reseller_id'];
		
		$drp_data = $this->db->query ( "SELECT id,CONCAT(number,'(',amount,')') as details,number FROM refill_coupon WHERE status = '0' and reseller_id='" . $reseller_id . "'" );
		$reseller_data = array ();
		$data ['refill_coupon_list'] = form_dropdown_all ( 'refill_coupon_list', $reseller_data, '' );
		$this->load->view ( 'view_refill_coupon_list', $data );
	}
	function user_refill_coupon_list_json() {
		$account_data = $this->session->userdata ( "accountinfo" );
		
		$this->load->module ( 'refill_coupon/refill_coupon' );
		$this->refill_coupon->refill_coupon_customer_json ( $account_data ["id"] );
	}
	function user_refill_coupon_number($refill_coupon_no) {
		$accountinfo = $this->session->userdata ( 'accountinfo' );
		$reseller_id = $accountinfo ['reseller_id'];
		$customer_id = $accountinfo ['id'];
		$this->db->where ( 'reseller_id', $reseller_id );
		$this->db->where ( 'number', $refill_coupon_no );
		$this->db->select ( '*' );
		$refill_coupon_result = $this->db->get ( 'refill_coupon' );
		if ($refill_coupon_result->num_rows () > 0) {
			$refill_coupon_result = $refill_coupon_result->result_array ();
			$refill_coupon_result = $refill_coupon_result [0];
			if ($refill_coupon_result ['status'] == 1) {
				echo json_encode ( 1 );
			} elseif ($refill_coupon_result ['status'] == 2) {
				echo json_encode ( 2 );
			} else {
				$balance = $this->common->get_field_name ( 'balance', 'accounts', array (
						'id' => $accountinfo ['id'],
						'status' => 0,
						'type' => 0,
						'deleted' => 0 
				) );
				$user_balance = ($accountinfo ['posttoexternal'] == 1) ? $accountinfo ['credit_limit'] - $balance : $balance;
				
				$original_balance = $refill_coupon_result ['amount'];
				$refill_coupon_result ['amount'] = $this->common_model->to_calculate_currency ( $original_balance, '', '', TRUE, TRUE );
				$refill_coupon_result ['new_balance'] = $this->common_model->to_calculate_currency ( $user_balance + $original_balance, '', '', TRUE, TRUE );
				echo json_encode ( $refill_coupon_result );
			}
		} else {
			echo json_encode ( 3 );
		}
	}
	function user_refill_coupon_action($refill_coupon_no) {
		$accountinfo = $this->session->userdata ( 'accountinfo' );
		$reseller_id = $accountinfo ['reseller_id'];
		if ($reseller_id == 0) {
			$reseller_id = '-1';
		}
		$date = gmdate ( 'Y-m-d H:i:s' );
		$customer_id = $accountinfo ['id'];
		$this->db->where ( 'number', $refill_coupon_no );
		$this->db->select ( 'amount' );
		$result = $this->db->get ( 'refill_coupon' );
		if ($result->num_rows () > 0) {
			$result = $result->result_array ();
			$amount = $result [0] ['amount'];
			$this->db->where ( 'id', $customer_id );
			$this->db->select ( 'balance' );
			$result = $this->db->get ( 'accounts' );
			$result = $result->result_array ();
			$current_balance = $result [0] ['balance'];
			$new_balance = ($accountinfo ["posttoexternal"] == 1) ? ($current_balance - $amount) : ($current_balance + $amount);
			
			$data = array (
					'balance' => $new_balance 
			);
			$this->db->where ( 'id', $customer_id );
			$this->db->update ( 'accounts', $data );
			$this->db->where ( 'number', $refill_coupon_no );
			$refill_coupon_data = array (
					'status' => 2,
					"account_id" => $customer_id,
					'firstused' => $date 
			);
			$this->db->update ( 'refill_coupon', $refill_coupon_data );
			$payment_arr = array (
					"accountid" => $customer_id,
					'type' => 'refill_coupon',
					'credit' => $amount,
					'payment_by' => $reseller_id,
					'payment_date' => $date,
					'refill_coupon_number' => $refill_coupon_no,
					'notes' => 'Recharge using Refill coupon,Refill coupon No. ' . $refill_coupon_no . '' 
			);
			$this->db->insert ( 'payments', $payment_arr );
			
			if ($accountinfo ['reseller_id'] == 0) {
				$where = array (
						"accountid" => 1 
				);
			} else {
				$where = array (
						"accountid" => $accountinfo ['id'] 
				);
			}
			$query = $this->db_model->getSelect ( "*", "invoice_conf", $where );
			if ($query->num_rows () > 0) {
				$invoice_conf = $query->result_array ();
				$invoice_conf = $invoice_conf [0];
			} else {
				$query = $this->db_model->getSelect ( "*", "invoice_conf", array (
						"accountid" => 1 
				) );
				$invoice_conf = $query->result_array ();
				$invoice_conf = $invoice_conf [0];
			}
			
			$last_invoice_ID = $this->user_invoice_date ( "invoiceid", $accountinfo ["id"] );
			$invoice_prefix = $invoice_conf ['invoice_prefix'];
			$due_date = gmdate ( "Y-m-d H:i:s", strtotime ( gmdate ( "Y-m-d H:i:s" ) . " +" . $invoice_conf ['interval'] . " days" ) );
			$this->load->module ( 'invoices/invoices' );
			$invoice_id = $this->invoices->invoices->generate_receipt ( $accountinfo ['id'], $amount, $accountinfo, $last_invoice_ID + 1, $invoice_prefix, $due_date );
			$account_balance = $this->common->get_field_name ( 'balance', 'accounts', $accountinfo ['id'] );

			if ($accountinfo['posttoexternal'] == 1) {
				$account_balance = ($accountinfo['creditlimit']*$accountinfo['posttoexternal']) - $account_balance;
			} else {
				$account_balance = $account_balance;
			}						
			$before_balance = $account_balance - $amount ;
			$after_balance = $account_balance;

			$insert_arr = array (
					"accountid" => $accountinfo ['id'],
					"description" => 'Recharge using Refill coupon,Refill coupon No. ' . $refill_coupon_no . '',
					"debit" => 0,
					"credit" => $amount,
					"created_date" => gmdate ( "Y-m-d H:i:s" ),
					"invoiceid" => $invoice_id,
					"reseller_id" => $accountinfo ['reseller_id'],
					"item_type" => 'Refill',
					"item_id" => '0',
					'before_balance' => $before_balance,
					'after_balance' => $after_balance 
			);
			$this->db->insert ( "invoice_details", $insert_arr );
		}
		redirect ( base_url () . "user/user_refill_coupon_list/" );
	}
	function user_invoice_date($select, $accountid) {
		$query = $this->db_model->select ( $select, "invoices", '', "id", "DESC", "1", "0" );
		if ($query->num_rows () > 0) {
			$invoiceid = $query->result_array ();
			$invoice_date = $invoiceid [0] [$select];
			return $invoice_date;
		}
		return false;
	}
	function user_packages() {
		$data ['page_title'] = gettext ( 'Packages' );
		$data ['grid_fields'] = $this->user_form->build_packages_list_for_user ();
		$this->load->view ( 'view_user_packages_list', $data );
	}
	function user_packages_json() {
		$json_data = array ();
		$count_all = $this->user_model->get_user_packages_list ( false, '', '' );
		$paging_data = $this->form->load_grid_config ( $count_all, $_GET ['rp'], $_GET ['page'] );
		$json_data = $paging_data ["json_paging"];
		$query = $this->user_model->get_user_packages_list ( true, $paging_data ["paging"] ["start"], $paging_data ["paging"] ["page_no"] );
		$grid_fields = json_decode ( $this->user_form->build_packages_list_for_user () );
		$json_data ['rows'] = $this->form->build_grid ( $query, $grid_fields );
		echo json_encode ( $json_data );
	}
	function user_invoices() {
		$data ['page_title'] = gettext ( 'Invoices' );
		$this->load->view ( 'view_user_invoices_list', $data );
	}
	function user_invoices_json() {
		$json_data = array ();
		$count_all = $this->user_model->get_user_invoices_list ( false );
		$paging_data = $this->form->load_grid_config ( $count_all, 10, 1 );
		$json_data = $paging_data ["json_paging"];
		$query = $this->user_model->get_user_invoices_list ( true, $paging_data ["paging"] ["start"], $paging_data ["paging"] ["page_no"] );
		$this->load->module ( 'invoices/invoices' );
		$grid_fields = json_decode ( $this->user_form->build_invoices_list_for_user () );
		$json_data ['rows'] = $this->form->build_grid ( $query, $grid_fields );
		echo json_encode ( $json_data );
	}
	function user_emails() {
		$data ['page_title'] = gettext ( 'EMails' );
		$data ['grid_fields'] = $this->user_form->build_emails_list_for_user ();
		$data ['form_search'] = $this->form->build_serach_form ( $this->user_form->build_user_emails_search () );
		$this->load->view ( 'view_user_emails_list', $data );
	}
	function user_emails_json() {
		$json_data = array ();
		$count_all = $this->user_model->get_user_emails_list ( false );
		$paging_data = $this->form->load_grid_config ( $count_all, $_GET ['rp'], $_GET ['page'] );
		$json_data = $paging_data ["json_paging"];
		$query = $this->user_model->get_user_emails_list ( true, $paging_data ["paging"] ["start"], $paging_data ["paging"] ["page_no"] );
		$grid_fields = json_decode ( $this->user_form->build_emails_list_for_user () );
		$json_data ['rows'] = $this->form->build_grid ( $query, $grid_fields );
		echo json_encode ( $json_data );
	}
	function user_emails_search() {
		$ajax_search = $this->input->post ( 'ajax_search', 0 );
		if ($this->input->post ( 'advance_search', TRUE ) == 1) {
			$this->session->set_userdata ( 'advance_search', $this->input->post ( 'advance_search' ) );
			$action = $this->input->post ();
			unset ( $action ['action'], $action ['advance_search'] );
			$this->session->set_userdata ( 'user_emails_search', $action );
		}
		if (@$ajax_search != 1) {
			redirect ( base_url () . 'user/user_emails/' );
		}
	}
	function user_emails_clearsearchfilter() {
		$this->session->set_userdata ( 'advance_search', 0 );
		$this->session->set_userdata ( 'user_emails_search', "" );
	}
	function user_invoice_config() {
		$data ['page_title'] = gettext ( 'Company Profile' );
		$accountinfo = $this->session->userdata ( "accountinfo" );
		$add_array = $this->input->post ();
		$data ["account_data"] = $add_array;
		if (isset ( $add_array ['submit'] )) {
			if ($_FILES ['file'] ['name'] == '') {
				$invoiceconf = $this->user_model->get_invoiceconf ();
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
						redirect ( base_url () . 'user/user_invoice_config/' );
					}
				} else {
					$this->session->set_flashdata ( 'astpp_notification', 'Please upload only image!' );
					redirect ( base_url () . 'user/user_invoice_config/' );
				}
			}
			$add_array ['logo'] = $file_name;
			unset ( $add_array ['submit'] );
			if ($add_array ['id'] == '') {
				$add_array ['accountid'] = $accountinfo ['id'];
				$this->user_model->add_invoice_config ( $add_array );
			} else {
				$this->user_model->edit_invoice_config ( $add_array, $add_array ['id'] );
			}
			$this->session->set_flashdata ( 'astpp_errormsg', 'Invoice config updated successfully!' );
			redirect ( base_url () . 'user/user_invoice_config/' );
		} else {
			$data ["account_data"] = ( array ) $this->db->get_where ( 'invoice_conf', array (
					"accountid" => $accountinfo ['id'] 
			) )->first_row ();
			if (isset ( $data ["account_data"] ['logo'] )) {
				$data ["account_data"] ['file'] = $accountinfo ['id'] . "_" . $data ["account_data"] ['logo'];
			}
			$this->load->view ( 'view_user_invoices_config', $data );
		}
	}
	function user_invoice_logo_delete($accountid) {
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
	function user_myprofile() {
		$accountinfo = $this->session->userdata ( "accountinfo" );
		$entity_name = strtolower ( $this->common->get_entity_type ( '', '', $accountinfo ['type'] ) );
		$data ['page_title'] = gettext ( 'My Profile' );
		$add_array = $this->input->post ();
		if ($add_array ['id'] != '') {
			$add_array ['type'] = $accountinfo ['type'];
			$data ['form'] = $this->form->build_form ( $this->user_form->get_userprofile_form_fields ( $add_array ), $add_array );
			if ($this->form_validation->run () == FALSE) {
				$data ['validation_errors'] = validation_errors ();
			} else {
				if ($add_array ['id'] == $accountinfo ['id']) {
					$this->user_model->edit_account ( $add_array, $add_array ['id'] );
					$result = $this->db->get_where ( 'accounts', array (
							'id' => $add_array ['id'] 
					) );
					$result = $result->result_array ();
					$this->session->set_userdata ( 'accountinfo', $result [0] );
					$this->session->set_flashdata ( 'astpp_errormsg', ' Your profile updated successfully!' );
					redirect ( base_url () . 'user/user_myprofile/' );
				} else {
					$this->session->set_flashdata ( 'astpp_notification', 'Something wrong.Please contact to administrator.' );
				}
			}
		} else {
			$where = array (
					'id' => $accountinfo ["id"] 
			);
			$account = $this->db_model->getSelect ( "*", "accounts", $where );
			$data ["account_data"] = $account->result_array ();
			
			foreach ( $account->result_array () as $key => $value ) {
				$editable_data = $value;
			}
			$editable_data ['password'] = $this->common->decode ( $editable_data ['password'] );
			$data ['form'] = $this->form->build_form ( $this->user_form->get_userprofile_form_fields ( $editable_data ), $editable_data );
		}
		$this->load->view ( 'view_user_details', $data );
	}
	function user_change_password() {
		$accountinfo = $this->session->userdata ( "accountinfo" );
		$data ['page_title'] = gettext ( "Change Password" );
		$add_array = $this->input->post ();
		if (! empty ( $add_array )) {
			$data ['form'] = $this->form->build_form ( $this->user_form->get_userprofile_change_password (), $add_array );
			if ($this->form_validation->run () == FALSE) {
				$data ['validation_errors'] = validation_errors ();
			} else {
				$password_encode = $this->common->encode ( $add_array ['new_password'] );
				$data = array (
						'password' => $password_encode 
				);
				$this->db->where ( 'id', $add_array ['id'] );
				$this->db->update ( 'accounts', $data );
				$this->session->set_flashdata ( 'astpp_errormsg', 'Password updated successfully!' );
				redirect ( base_url () . 'user/user_change_password/' );
			}
		} else {
			$data_array ['id'] = $accountinfo ['id'];
			$data ['form'] = $this->form->build_form ( $this->user_form->get_userprofile_change_password (), $data_array );
		}
		$this->load->view ( 'view_user_change_password', $data );
	}
	function user_refill_report() {
		$accountinfo = $this->session->userdata ( 'accountinfo' );
		$data ['page_title'] = gettext ( 'Refill Report' );
		$data ['search_flag'] = true;
		$this->session->set_userdata ( 'advance_search', 0 );
		$data ['grid_fields'] = $this->user_form->build_user_refill_report ();
		$data ['form_search'] = $this->form->build_serach_form ( $this->user_form->build_user_refill_report_search () );
		if ($accountinfo ['type'] == 1) {
			$this->load->view ( 'view_reseller_refill_report', $data );
		} else {
			$this->load->view ( 'view_user_refill_report', $data );
		}
	}
	function user_refill_report_json() {
		$json_data = array ();
		$count_all = $this->user_model->get_user_refill_list ( false );
		$paging_data = $this->form->load_grid_config ( $count_all, $_GET ['rp'], $_GET ['page'] );
		$json_data = $paging_data ["json_paging"];
		$query = $this->user_model->get_user_refill_list ( true, $paging_data ["paging"] ["start"], $paging_data ["paging"] ["page_no"] );
		$grid_fields = json_decode ( $this->user_form->build_user_refill_report () );
		$json_data ['rows'] = $this->form->build_grid ( $query, $grid_fields );
		echo json_encode ( $json_data );
	}
	function user_refill_report_search() {
		$ajax_search = $this->input->post ( 'ajax_search', 0 );
		if ($this->input->post ( 'advance_search', TRUE ) == 1) {
			$this->session->set_userdata ( 'advance_search', $this->input->post ( 'advance_search' ) );
			$action = $this->input->post ();
			unset ( $action ['action'], $action ['advance_search'] );
			if (isset ( $action ['credit'] ['credit'] ) && $action ['credit'] ['credit'] != '') {
				$action ['credit'] ['credit'] = $this->common_model->add_calculate_currency ( $action ['credit'] ['credit'], "", '', true, false );
			}
			$this->session->set_userdata ( 'user_refill_report_search', $action );
		}
		if (@$ajax_search != 1) {
			redirect ( base_url () . 'user/user_refill_report/' );
		}
	}
	function user_refill_report_clearsearchfilter() {
		$this->session->set_userdata ( 'advance_search', 0 );
		$this->session->set_userdata ( 'user_refill_report_search', "" );
	}
	function user_invoices_list() {
		$data ['page_title'] = gettext ( 'Invoices' );
		$data ['search_flag'] = true;
		$this->session->set_userdata ( 'advance_search', 0 );
		$data ['grid_fields'] = $this->user_form->build_user_invoices ();
		$data ['form_search'] = $this->form->build_serach_form ( $this->user_form->build_user_invoices_search () );
		$this->load->view ( 'view_user_invoices_list', $data );
	}
	function user_invoices_list_json() {
		$accountinfo = $this->session->userdata ( 'accountinfo' );
		$where = array (
				"accountid" => $accountinfo ['id'] 
		);
		$count_all = $this->user_model->get_user_invoice_list ( false, '', '', $where );
		$paging_data = $this->form->load_grid_config ( $count_all, $_GET ['rp'], $_GET ['page'] );
		$json_data = $paging_data ["json_paging"];
		$user_currency = $this->common->get_field_name ( 'currency', 'currency', $accountinfo ['currency_id'] );
		$invoices_query = $this->user_model->get_user_invoice_list ( true, $paging_data ["paging"] ["start"], $paging_data ["paging"] ["page_no"], $where );
		$invoices_result = $invoices_query->result_array ();
		$ountstanding_value = 0;
		$total_amount = 0;
		$this->db->where ( 'accountid', $accountinfo ['id'] );
		$this->db->select ( 'sum(credit) as total_credit' );
		$invoice_details_result = $this->db->get ( 'invoice_details' );
		$total_credit = ( array ) $invoice_details_result->first_row ();
		$total_credit = $total_credit ['total_credit'];
		foreach ( $invoices_result as $key => $value ) {
			$total_amount += $value ['amount'];
			$invoice_date = date ( "Y-m-d", strtotime ( $value ['invoice_date'] ) );
			$from_date = date ( "Y-m-d", strtotime ( $value ['from_date'] ) );
			$due_date = date ( "Y-m-d", strtotime ( $value ['due_date'] ) );
			$outstanding = $value ['amount'];
			$invoice_total_query = $this->db_model->select ( "sum(debit) as debit,sum(credit) as credit,created_date", "invoice_details", array (
					"invoiceid" => $value ['id'],
					"item_type" => "INVPAY" 
			), "created_date", "DESC", "1", "0" );
			if ($invoice_total_query->num_rows () > 0) {
				$invoice_total_query = $invoice_total_query->result_array ();
				$outstanding -= $invoice_total_query [0] ['credit'];
				$payment_last = ($invoice_total_query [0] ['created_date']) ? date ( "Y-m-d", strtotime ( $invoice_total_query [0] ['created_date'] ) ) : '';				
			}

			//If that's receipt then forcefully override outstanding amount as user has already paid for the service.
			if ($value ['type'] == 'R') {
				$outstanding = '';
				$payment_last = $invoice_date;
			}

			$invoice_total_query = $this->db_model->select ( "debit,created_date", "invoice_details", array (
					"invoiceid" => $value ['id'],
					"item_type" => "INVPAY" 
			), "created_date", "DESC", "1", "0" );
			if ($invoice_total_query->num_rows () > 0) {
				$invoice_total_result = $invoice_total_query->result_array ();
			}
			$download = '<a href="' . base_url () . '/user/user_invoice_download/' . $value ['id'] . '/00' . $value ['invoice_prefix'] . $value ['invoiceid'] . '" class="btn btn-royelblue btn-sm"  title="Download Invoice" ><i class="fa fa-cloud-download fa-fw"></i></a>&nbsp';
			if ($value ['type'] == 'I') {
				if ($outstanding > 0) {
					$payment = ' <a style="padding: 0 8px;" href="' . base_url () . 'user/user_invoice_payment/' . $value ['id'] . '" class="btn btn-warning"  title="Payment">Unpaid</a>';
				} else {
					$payment = ' <button style="padding: 0 8px;" class="btn btn-success" type="button">Paid</button>';
				}
			} else {
				$payment = '';
			}
			if ($value ['generate_type'] == 1) {
				$invoice_type = 'Manually';
			} else {
				$invoice_type = 'Automatically';
			}
			
			if ($value ['type'] == 'R') {
				$icon = '<div class="flx_font flx_magenta">R</div>';
			} else {
				$icon = '<div class="flx_font flx_drk_pink">I</div>';
			}
			
			$json_data ['rows'] [] = array (
					'cell' => array (
							$value ['invoice_prefix'] . $value ['invoiceid'] . $icon,
							$invoice_type,
							$invoice_date,
							$from_date,
							$due_date,
							$payment_last,
							$this->common->currency_decimal ( $this->common_model->calculate_currency ( $value ['amount'] ) ),
							$this->common->currency_decimal ( $this->common_model->calculate_currency ( $outstanding ) ),
							$download . $payment 
					) 
			);
			$ountstanding_value = $ountstanding_value + $outstanding;
		}
		echo json_encode ( $json_data );
	}
	function user_invoices_list_search() {
		$ajax_search = $this->input->post ( 'ajax_search', 0 );
		if ($this->input->post ( 'advance_search', TRUE ) == 1) {
			$this->session->set_userdata ( 'advance_search', $this->input->post ( 'advance_search' ) );
			$action = $this->input->post ();
			print_r ( $action );
			unset ( $action ['action'] );
			unset ( $action ['advance_search'] );
			$action ['from_date'] [0] = $action ['from_date'] [0] ? $action ['from_date'] [0] . " 00:00:00" : '';
			$action ['to_date'] [1] = $action ['to_date'] [1] ? $action ['to_date'] [1] . " 23:59:59" : '';
			$action ['invoice_date'] [0] = $action ['invoice_date'] [0] ? $action ['invoice_date'] [0] . " 00:00:00" : '';
			$this->session->set_userdata ( 'user_invoice_list_search', $action );
		}
		if (@$ajax_search != 1) {
			redirect ( base_url () . 'user/user_invoice_list/' );
		}
	}
	function user_invoices_list_clearsearchfilter() {
		$this->session->set_userdata ( 'advance_search', 0 );
		$this->session->set_userdata ( 'user_invoice_list_search', "" );
	}
	function user_invoices_download($invoiceid) {
		$this->load->module ( 'invoices/invoices' );
		$this->invoices->invoice_main_download ( $invoiceid );
	}
	function user_list_responce() {
		$this->load->module ( 'invoices/invoices' );
		$this->invoices->invoice_list_responce ();
	}
	function user_invoice_payment($invoiceid) {
		$this->load->module ( 'invoices/invoices' );
		$this->invoices->invoice_summary ( $invoiceid );
	}
	function user_invoice_payment_pay($action = "") {
		$this->load->module ( "user/payment" );
		if ($action == "GET_AMT") {
			$amount = $this->input->post ( "value", true );
			$amount = $this->common_model->add_calculate_currency ( $amount, "", "", true, false );
			echo number_format ( $amount, 2 );
		} else {
			$this->payment->index ();
		}
	}
	function user_invoice_download($invoiceid) {
		$this->permission->customer_web_record_permission($invoiceid,'invoices','user/user_invoices_list/');
		$this->load->module ( 'invoices/invoices' );
		$this->invoices->invoice_download ( $invoiceid );
	}
	function user_charges_history() {
		$data ['page_title'] = gettext ( 'Charges History' );
		$this->session->set_userdata ( 'advance_search', 0 );
		$data ['grid_fields'] = $this->user_form->build_user_charge_history ();
		$data ['form_search'] = $this->form->build_serach_form ( $this->user_form->build_user_charge_history_search () );
		$this->load->view ( 'view_user_charges_list', $data );
	}
	function user_charges_history_json() {
		$json_data = array ();
		$count_all = $this->user_model->get_user_charge_history ( false );
		$paging_data = $this->form->load_grid_config ( $count_all, $_GET ['rp'], $_GET ['page'] );
		$json_data = $paging_data ["json_paging"];
		$query = $this->user_model->get_user_charge_history ( true, $paging_data ["paging"] ["start"], $paging_data ["paging"] ["page_no"] );
		
		$result = $query->result_array ();
		$query1 = $this->user_model->get_user_charge_history ( true, '', '' );
		$res = $query1->result_array ();
		$debit = 0;
		$credit = 0;
		$before_balance = 0;
		$after_balance = 0;
		$i = 0;
		foreach ( $result as $key => $value ) {
			$date = $this->common->convert_GMT_to ( '', '', $value ['created_date'] );
			$cust_type = $this->common->get_field_name ( 'posttoexternal', 'accounts', $value ['accountid'] );
			$invoice_prefix = $entity_type = $this->common->get_field_name ( 'invoice_prefix', 'invoices', array (
					'id' => $value ['invoiceid'] 
			) );
			$invoiceid = $entity_type = $this->common->get_field_name ( 'invoiceid', 'invoices', array (
					'id' => $value ['invoiceid'] 
			) );
			$invoice_num = $invoice_prefix . $invoiceid;
			$account = $this->common->get_field_name_coma_new ( 'first_name,last_name,number', 'accounts', $value ['accountid'] );
			$reseller = $this->common->reseller_select_value ( 'first_name,last_name,number', 'accounts', $value ['reseller_id'] );
			$item_type = $value ['item_type'];
			if ($value ['before_balance'] == '-') {
				$before_balance = '-';
			} else {
				$before_balance = $this->common->convert_to_currency ( '', '', $value ['before_balance'] );
			}
			if ($value ['debit'] == '-') {
				$debit = '-';
			} else {
				$debit = $this->common->convert_to_currency ( '', '', $value ['debit'] );
			}
			$credit = $this->common->convert_to_currency ( '', '', $value ['credit'] );
			if ($cust_type == 0 && $value ['item_type'] == 'INVPAY') {
				$credit = '(-) ' . $credit;
			}
			if ($value ['after_balance'] == '-') {
				$after_balance = '-';
			} else {
				$after_balance = $this->common->convert_to_currency ( '', '', $value ['after_balance'] );
			}
			$description = $value ['description'];
			$cust_type = $this->common->get_field_name ( 'posttoexternal', 'accounts', $value ['accountid'] );
			$json_data ['rows'] [] = array (
					'cell' => array (
							$date,
							$invoice_num,
							$item_type,
							$before_balance,
							$debit,
							$credit,
							$after_balance,
							$description 
					) 
			);
		}
		$debit_sum = 0;
		$credit_sum = 0;
		foreach ( $res as $value ) {
			$cust_type = $this->common->get_field_name ( 'posttoexternal', 'accounts', $value ['accountid'] );
			$cust_type = $this->common->get_field_name ( 'posttoexternal', 'accounts', $value ['accountid'] );
			$debit_sum += $value ['debit'];
			$credit_sum += $value ['credit'];
			$before_balance += $value ['before_balance'];
			$after_balance += $value ['after_balance'];
		}
		$json_data ['rows'] [$count_all] ['cell'] = array (
				'<b>Total</b>',
				'-',
				'-',
				'-',
				'<b>' . $this->common->convert_to_currency ( '', '', $debit_sum ) . '</b>',
				'<b>' . $this->common->convert_to_currency ( '', '', $credit_sum ) . '</b>',
				'-',
				'-' 
		);
		echo json_encode ( $json_data );
	}
	function user_charges_history_search() {
		$ajax_search = $this->input->post ( 'ajax_search', 0 );
		if ($this->input->post ( 'advance_search', TRUE ) == 1) {
			$this->session->set_userdata ( 'advance_search', $this->input->post ( 'advance_search' ) );
			$action = $this->input->post ();
			unset ( $action ['action'] );
			unset ( $action ['advance_search'] );
			$action ['created_date'] [0] = $action ['created_date'] [0] ? $action ['created_date'] [0] . " 00:00:00" : '';
			$action ['created_date'] [1] = $action ['created_date'] [1] ? $action ['created_date'] [1] . " 23:59:59" : '';
			if (isset ( $action ['debit'] ['debit'] ) && $action ['debit'] ['debit'] != '') {
				$action ['debit'] ['debit'] = $this->common_model->add_calculate_currency ( $action ['debit'] ['debit'], "", '', true, false );
			}
			if (isset ( $action ['credit'] ['credit'] ) && $action ['credit'] ['credit'] != '') {
				$action ['credit'] ['credit'] = $this->common_model->add_calculate_currency ( $action ['credit'] ['credit'], "", '', true, false );
			}
			$this->session->set_userdata ( 'user_charge_history_search', $action );
		}
		if (@$ajax_search != 1) {
			redirect ( base_url () . 'user/user_charges_history/' );
		}
	}
	function user_charges_history_clearsearchfilter() {
		$this->session->set_userdata ( 'advance_search', 0 );
		$this->session->set_userdata ( 'user_charge_history_search', "" );
	}
	function user_subscriptions() {
		$data ['page_title'] = gettext ( 'Subscriptions' );
		$this->session->set_userdata ( 'advance_search', 0 );
		$data ['grid_fields'] = $this->user_form->build_user_subscription ();
		$data ['form_search'] = $this->form->build_serach_form ( $this->user_form->build_user_subscription_search () );
		$this->load->view ( 'view_user_subscriptions_list', $data );
	}
	function user_subscriptions_json() {
		$accountinfo = $this->session->userdata ( 'accountinfo' );
		$json_data = array ();
		$select = "charge_to_account.id,charges.description,charges.charge,charges.sweep_id";
		$table = "charges";
		$jionTable = array (
				'charge_to_account',
				'accounts' 
		);
		$jionCondition = array (
				'charges.id = charge_to_account.charge_id',
				'accounts.id = charge_to_account.accountid' 
		);
		$type = array (
				'left',
				'inner' 
		);
		$where = array (
				'accounts.id' => $accountinfo ['id'] 
		);
		$order_type = 'charges.id';
		$order_by = "ASC";
		$this->db_model->build_search ( "user_subscription_search" );
		$count_all = $this->db_model->getCountWithJion ( $table, $select, $where, $jionTable, $jionCondition, $type );
		$paging_data = $this->form->load_grid_config ( $count_all, $_GET ['rp'], $_GET ['page'] );
		$json_data = $paging_data ["json_paging"];
		$this->db_model->build_search ( "user_subscription_search" );
		
		$account_charge_list = $this->db_model->getAllJionQuery ( $table, $select, $where, $jionTable, $jionCondition, $type, $paging_data ["paging"] ["page_no"], $paging_data ["paging"] ["start"], $order_by, $order_type, "" );
		$grid_fields = json_decode ( $this->user_form->build_user_subscription () );
		$json_data ['rows'] = $this->form->build_grid ( $account_charge_list, $grid_fields );
		echo json_encode ( $json_data );
	}
	function user_subscriptions_search() {
		$ajax_search = $this->input->post ( 'ajax_search', 0 );
		if ($this->input->post ( 'advance_search', TRUE ) == 1) {
			$this->session->set_userdata ( 'advance_search', $this->input->post ( 'advance_search' ) );
			$action = $this->input->post ();
			unset ( $action ['action'] );
			unset ( $action ['advance_search'] );
			if (isset ( $action ['charge'] ['charge'] ) && $action ['charge'] ['charge'] != '') {
				$action ['charge'] ['charge'] = $this->common_model->add_calculate_currency ( $action ['charge'] ['charge'], "", '', true, false );
			}
			if (isset ( $action ['sweep_id'] ) && $action ['sweep_id'] != '') {
				$action ['charges.sweep_id'] = $action ['sweep_id'];
				unset ( $action ['sweep_id'] );
			}
			$this->session->set_userdata ( 'user_subscription_search', $action );
		}
		if (@$ajax_search != 1) {
			redirect ( base_url () . 'user/user_subscriptions/' );
		}
	}
	function user_subscriptions_clearsearchfilter() {
		$this->session->set_userdata ( 'advance_search', 0 );
		$this->session->set_userdata ( 'user_subscription_search', "" );
	}
	function user_didlist() {
		$accountinfo = $this->session->userdata ( 'accountinfo' );
		$data ['page_title'] = gettext ( 'Purchase DIDs' );
		$data ['search_flag'] = true;
		$data ['grid_fields'] = $this->user_form->build_user_didlist ();
		$data ['form_search'] = $this->form->build_serach_form ( $this->user_form->build_user_didlist_search () );
		$data ["grid_buttons"] = array ();
		$acc_data = $this->session->userdata ( "accountinfo" );
		$data ['accountid'] = $acc_data ['id'];
		$data ['country_id'] = $acc_data ['country_id'];
		$result_did_final = array ();
		if ($accountinfo ['reseller_id'] > 0) {
			$this->db->select ( 'dids.id, dids.number, reseller_pricing.setup, reseller_pricing.monthlycost' );
			$this->db->where ( 'dids.accountid', 0 );
			$this->db->where ( 'reseller_pricing.note', 'dids.number', false );
			$this->db->where ( 'reseller_pricing.reseller_id', $accountinfo ['reseller_id'] );
			$this->db->from ( 'dids,reseller_pricing' );
		} else {
			$this->db->where ( 'parent_id', 0 );
			$this->db->where ( 'accountid', 0 );
			$this->db->select ( 'id,number,setup,monthlycost' );
			$this->db->from ( 'dids' );
		}
		$dids_array = ( array ) $this->db->get ()->result_array ();
		$drp_list = array ();
		if (! empty ( $dids_array )) {
			foreach ( $dids_array as $drp_value ) {
				if (! empty ( $drp_value ['monthlycost'] ) && $drp_value ['monthlycost'] != 0) {
					$did_cost = $this->common_model->to_calculate_currency ( $drp_value ['monthlycost'], '', '', true, true );
				} else {
					$did_cost = 0;
				}
				if (! empty ( $drp_value ['setup'] ) && $drp_value ['setup'] != 0) {
					$did_setup = $this->common_model->to_calculate_currency ( $drp_value ['setup'], '', '', true, true );
				} else {
					$did_setup = 0;
				}
				$drp_list [$drp_value ['id']] = $drp_value ['number'] . ' ( Setup : ' . $did_setup . ')' . '( Monthly : ' . $did_cost . ' )';
			}
		}
		$data ['didlist'] = form_dropdown_all ( array (
				"name" => "free_didlist",
				"id" => "free_didlist",
				"class" => "did_dropdown" 
		), $drp_list, '' );
		$this->load->view ( 'view_user_did_list', $data );
	}
	function user_didlist_json() {
		$account_data = $this->session->userdata ( "accountinfo" );
		if ($account_data ['reseller_id'] != 0) {
			$json_data = array ();
			$where = array (
					'dids.accountid' => $account_data ['id'] 
			);
			$jionCondition = 'dids.number = reseller_pricing.note AND dids.parent_id = reseller_pricing.reseller_id';
			$count_all = $this->db_model->getJionQueryCount ( "dids", '*', $where, "reseller_pricing", $jionCondition, 'inner' );
			$paging_data = $this->form->load_grid_config ( $count_all, $_GET ['rp'], $_GET ['page'] );
			$json_data = $paging_data ["json_paging"];
			$this->db_model->build_search ( 'user_did_search' );
			$query = $this->db_model->getJionQuery ( "dids", 'reseller_pricing.setup,reseller_pricing.cost,reseller_pricing.last_modified_date,reseller_pricing.connectcost,reseller_pricing.inc,reseller_pricing.init_inc,reseller_pricing.includedseconds,reseller_pricing.monthlycost,dids.number,dids.id,dids.accountid,dids.extensions,dids.status,dids.provider_id,dids.allocation_bill_status,reseller_pricing.disconnectionfee,dids.dial_as,dids.call_type,dids.country_id', $where, "reseller_pricing", $jionCondition, 'inner', $paging_data ["paging"] ["page_no"], $paging_data ["paging"] ["start"], "dids.id", "", '' );
		} else {
			$json_data = array ();
			$where = array (
					'accountid' => $account_data ['id'] 
			);
			$this->db_model->build_search ( 'user_did_search' );
			$count_all = $this->db_model->countQuery ( "*", "dids", $where );
			$paging_data = $this->form->load_grid_config ( $count_all, $_GET ['rp'], $_GET ['page'] );
			$json_data = $paging_data ["json_paging"];
			$this->db_model->build_search ( 'user_did_search' );
			$query = $this->db_model->getSelect ( "*", "dids", $where, "id", "ASC", $paging_data ["paging"] ["page_no"], $paging_data ["paging"] ["start"] );
		}
		$did_grid_fields = json_decode ( $this->user_form->build_user_didlist () );
		$json_data ['rows'] = $this->form->build_grid ( $query, $did_grid_fields );
		echo json_encode ( $json_data );
	}
	function user_did_country() {
		$this->load->module ( 'accounts/accounts' );
		$this->accounts->customer_did_country ();
	}
	function user_didlist_search() {
		$ajax_search = $this->input->post ( 'ajax_search', 0 );
		if ($this->input->post ( 'advance_search', TRUE ) == 1) {
			$this->session->set_userdata ( 'advance_search', $this->input->post ( 'advance_search' ) );
			$action = $this->input->post ();
			$accountinfo = $this->session->userdata ( 'accountinfo' );
			if ($accountinfo ['reseller_id'] > 0 && $action ['call_type'] > 0) {
				$action ['dids.call_type'] = $action ['call_type'];
				unset ( $action ['call_type'] );
			}
			unset ( $action ['action'] );
			unset ( $action ['advance_search'] );
			$this->session->set_userdata ( 'user_did_search', $action );
		}
		if (@$ajax_search != 1) {
			redirect ( base_url () . 'user/user_didlist/' );
		}
	}
	function user_didlist_clearsearchfilter() {
		$this->session->set_userdata ( 'advance_search', 0 );
		$this->session->set_userdata ( 'user_did_search', "" );
	}
	function user_ipmap() {
		$this->session->set_userdata ( 'advance_search', 0 );
		$data ['grid_fields'] = $this->user_form->build_user_ipmap ();
		$data ['form_search'] = $this->form->build_serach_form ( $this->user_form->build_user_ipmap_search () );
		$this->load->view ( 'view_user_ipmap_list', $data );
	}
	function user_ipmap_json() {
		$json_data = array ();
		$account_data = $this->session->userdata ( "accountinfo" );
		$count_all = $this->user_model->user_ipmap_list ( false );
		$paging_data = $this->form->load_grid_config ( $count_all, $_GET ['rp'], $_GET ['page'] );
		$json_data = $paging_data ["json_paging"];
		$query = $this->user_model->user_ipmap_list ( true, $paging_data ["paging"] ["start"], $paging_data ["paging"] ["page_no"] );
		$ipmap_grid_fields = json_decode ( $this->user_form->build_user_ipmap () );
		$json_data ['rows'] = $this->form->build_grid ( $query, $ipmap_grid_fields );
		echo json_encode ( $json_data );
	}
	function user_ipmap_search() {
		$ajax_search = $this->input->post ( 'ajax_search', 0 );
		if ($this->input->post ( 'advance_search', TRUE ) == 1) {
			$this->session->set_userdata ( 'advance_search', $this->input->post ( 'advance_search' ) );
			$action = $this->input->post ();
			unset ( $action ['action'] );
			unset ( $action ['advance_search'] );
			$this->session->set_userdata ( 'user_ipmap_search', $action );
		}
		if (@$ajax_search != 1) {
			redirect ( base_url () . 'user/user_ipmap/' );
		}
	}
	function user_ipmap_clearsearchfilter() {
		$this->session->set_userdata ( 'advance_search', 0 );
		$this->session->set_userdata ( 'user_ipmap_search', "" );
	}
	function user_ipmap_action($action = 'delete', $id = false) {
		$add_array = $this->input->post ();
		$accountinfo = $this->session->userdata ( 'accountinfo' );
		if ($action == 'add') {
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
				unset ( $add_array ['action'] );
				$add_array ['context'] = 'default';
				$add_array ['accountid'] = $accountinfo ['id'];
				$ip_flag = $this->db->insert ( "ip_map", $add_array );
				if ($ip_flag) {
					$this->load->library ( 'freeswitch_lib' );
					$this->load->module ( 'freeswitch/freeswitch' );
					$command = "api reloadacl";
					$response = $this->freeswitch_model->reload_freeswitch ( $command );
					$this->session->set_userdata ( 'astpp_notification', $response );
				}
				$this->session->set_flashdata ( 'astpp_errormsg', 'IP Added Sucessfully.' );
			}
		}
		if ($action == 'delete') {
			$this->permission->customer_web_record_permission($id,'ip_map','user/user_ipmap/');
			$this->db->delete ( 'ip_map', array (
					'id' => $id 
			) );
			$this->session->set_flashdata ( 'astpp_notification', 'IP Removed Sucessfully.' );
		}
		redirect ( base_url () . "user/user_ipmap/" );
	}
	function user_sipdevices() {
		if(common_model::$global_config['system_config']['opensips']== 0){
			$this->permission->permission_redirect_url("user/user/");
		}
		$data ['page_title'] = gettext ( 'SIP Devices' );
		$this->session->set_userdata ( 'advance_search', 0 );
		$data ['grid_fields'] = $this->user_form->build_user_sipdevices ();
		$data ['form_search'] = $this->form->build_serach_form ( $this->user_form->build_user_sipdevices_search () );
		$this->load->view ( 'view_user_sipdevices_list', $data );
	}
	function user_sipdevices_json() {
		$account_data = $this->session->userdata ( "accountinfo" );
		$json_data = array ();
		$count_all = $this->user_model->user_sipdevices_list ( false, $account_data ['id'] );
		$paging_data = $this->form->load_grid_config ( $count_all, $_GET ['rp'], $_GET ['page'] );
		$json_data = $paging_data ["json_paging"];
		$devices_result = array ();
		$query = $this->user_model->user_sipdevices_list ( true, $account_data ['id'], $paging_data ["paging"] ["start"], $paging_data ["paging"] ["page_no"] );
		foreach ( $query as $key => $value ) {
			$path_true = base_url () . '/assets/images/true.png';
			$path_false = base_url () . '/assets/images/false.png';
			$voicemail_enabled = $value ['voicemail_enabled'] == 'true' ? '<img src=' . $path_true . ' style="height:20px;width:20px;" title="Enable">' : '<img src=' . $path_false . ' style="height:20px;width:20px;" title="Disable">';
			$json_data ['rows'] [] = array (
					'cell' => array (
							'<input type="checkbox" name="chkAll" id="' . $value ['id'] . '" class="ace chkRefNos" onclick="clickchkbox(' . $value ['id'] . ')" value=' . $value ['id'] . '><lable class="lbl"></lable>',
							$value ['username'],
							$value ['password'],
							$value ['effective_caller_id_name'],
							$value ['effective_caller_id_number'],
							$this->common->get_status ( 'status', 'sip_devices', $value ),
							$this->common->convert_GMT_to ( '', '', $value ['creation_date'] ),
							$this->common->convert_GMT_to ( '', '', $value ['last_modified_date'] ),
							$voicemail_enabled,
							'<a href="' . base_url () . 'user/user_sipdevices_edit/' . $value ['id'] . '/" class="btn btn-royelblue btn-sm"  rel="facebox" title="Edit">&nbsp;<i class="fa fa-pencil-square-o fa-fw"></i></a>&nbsp;' . '<a href="' . base_url () . 'user/user_sipdevices_delete/' . $value ['id'] . '/" class="btn btn-royelblue btn-sm" title="Delete" onClick="return get_alert_msg();">&nbsp;<i class="fa fa-trash fa-fw"></i></a>' 
					) 
			);
		}
		echo json_encode ( $json_data );
	}
	function user_sipdevices_search() {
		$ajax_search = $this->input->post ( 'ajax_search', 0 );
		if ($this->input->post ( 'advance_search', TRUE ) == 1) {
			$this->session->set_userdata ( 'advance_search', $this->input->post ( 'advance_search' ) );
			$action = $this->input->post ();
			unset ( $action ['action'] );
			unset ( $action ['advance_search'] );
			$this->session->set_userdata ( 'user_sipdevices_search', $action );
		}
		if (@$ajax_search != 1) {
			redirect ( base_url () . 'user/user_sipdevices/' );
		}
	}
	function user_sipdevices_clearsearchfilter() {
		$this->session->set_userdata ( 'advance_search', 0 );
		$this->session->set_userdata ( 'user_sipdevices_search', "" );
	}
	function user_sipdevices_add() {
		$data ['page_title'] = gettext ( 'Create SIP Device' );
		$data ['form'] = $this->form->build_form ( $this->user_form->build_user_sipdevices_form (), "" );
		$this->load->view ( 'view_user_sipdevices_add_edit', $data );
	}
	function user_sipdevices_edit($edit_id = '') {
		$this->permission->customer_web_record_permission($edit_id,'sip_devices','user/user_sipdevices/');
		$account_data = $this->session->userdata ( "accountinfo" );
		$data ['page_title'] = gettext ( 'Edit SIP Device' );
		$where = array (
				'id' => $edit_id 
		);
		$sipdevice_info = $this->user_model->user_sipdevice_info ( $edit_id );
		$data ['form'] = $this->form->build_form ( $this->user_form->build_user_sipdevices_form ( $edit_id ), $sipdevice_info );
		$this->load->view ( 'view_user_sipdevices_add_edit', $data );
	}
	function user_sipdevices_save() {
		$add_array = $this->input->post ();
		$data ['form'] = $this->form->build_form ( $this->user_form->build_user_sipdevices_form ( $add_array ['id'] ), $add_array );
		if ($add_array ['id'] != '') {
			$data ['page_title'] = gettext ( 'Edit SIP Devices' );
			if ($this->form_validation->run () == FALSE) {
				$data ['validation_errors'] = validation_errors ();
				echo $data ['validation_errors'];
				exit ();
			} else {
				$this->user_model->user_sipdevice_edit ( $add_array, $add_array ['id'] );
				echo json_encode ( array (
						"SUCCESS" => "SIP Device Updated Successfully!" 
				) );
				exit ();
			}
		} else {
			$data ['page_title'] = gettext ( 'Create SIP Device' );
			if ($this->form_validation->run () == FALSE) {
				$data ['validation_errors'] = validation_errors ();
				echo $data ['validation_errors'];
				exit ();
			} else {
				$this->user_model->user_sipdevice_add ( $add_array );
				echo json_encode ( array (
						"SUCCESS" => "SIP Device Added Successfully!" 
				) );
				exit ();
			}
		}
	}
	function user_sipdevices_delete($id) {
		$this->permission->customer_web_record_permission($id,'sip_devices','user/user_sipdevices/');
		$this->db->delete ( 'sip_devices', array (
				'id' => $id 
		) );
		$this->session->set_flashdata ( 'astpp_notification', 'SIP Device Removed Sucessfully!' );
		redirect ( base_url () . "user/user_sipdevices/" );
	}
	function user_sipdevices_delete_multiple() {
		$ids = $this->input->post ( "selected_ids", true );
		$where = "id IN ($ids)";
		$this->db->delete ( "sip_devices", $where );
		echo TRUE;
	}
	function user_animap_list() {
		$data ['page_title'] = gettext ( 'Caller ID' );
		$this->session->set_userdata ( 'advance_search', 0 );
		$data ['grid_fields'] = $this->user_form->build_user_animap ();
		$this->load->view ( 'view_user_animap', $data );
	}
	function user_animap_list_json() {
		$account_data = $this->session->userdata ( "accountinfo" );
		$json_data = array ();
		$where = array (
				"accountid" => $account_data ['id'] 
		);
		$count_all = $this->db_model->countQuery ( "*", "ani_map", $where );
		$paging_data = $this->form->load_grid_config ( $count_all, $_GET ['rp'], $_GET ['page'] );
		$json_data = $paging_data ["json_paging"];
		$query = $this->db_model->select ( "*", "ani_map", $where, "id", "ASC", $paging_data ["paging"] ["page_no"], $paging_data ["paging"] ["start"] );
		$grid_fields = json_decode ( $this->user_form->build_user_animap () );
		$json_data ['rows'] = $this->form->build_grid ( $query, $grid_fields );
		echo json_encode ( $json_data );
	}
	function user_animap_action($action, $aniid = "") {
		$add_array = $this->input->post ();
		if ($action == "add" && $add_array ['number'] != '') {
			$this->db->where ( 'number', $add_array ['number'] );
			$this->db->select ( 'count(id) as count' );
			$cnt_result = $this->db->get ( 'ani_map' );
			$cnt_result = $cnt_result->result_array ();
			$count = $cnt_result [0] ['count'];
			if ($count == 0) {
				if ($add_array ['number'] != "") {
					$accountinfo = $this->session->userdata ( "accountinfo" );
					$insert_arr = array (
							"number" => $add_array ['number'],
							"accountid" => $accountinfo ['id'],
							"context" => "default" 
					);
					$this->db->insert ( "ani_map", $insert_arr );
					$this->session->set_flashdata ( 'astpp_errormsg', 'Add Caller ID Sucessfully!' );
				} else {
					$this->session->set_flashdata ( 'astpp_notification', 'Please Enter Caller ID value.' );
				}
			} else {
				$this->session->set_flashdata ( 'astpp_notification', ' Caller ID already Exists.' );
			}
		}
		if ($action == "delete") {
			$this->permission->customer_web_record_permission($aniid,'ani_map','user/user_animap_list/');
			$this->session->set_flashdata ( 'astpp_notification', 'Caller ID removed sucessfully!' );
			$this->db_model->delete ( "ani_map", array (
					"id" => $aniid 
			) );
		}
		redirect ( base_url () . "user/user_animap_list/" );
	}
	function user_alert_threshold() {
		$data ['page_title'] = gettext ( 'Alert Threshold' );
		$accountinfo = $this->session->userdata ( "accountinfo" );
		$add_array = $this->input->post ();
		if (! empty ( $add_array )) {
			unset ( $add_array ['action'], $add_array ['id'] );
			$this->user_model->edit_alert_threshold ( $add_array, $accountinfo ['id'] );
			$this->session->set_flashdata ( 'astpp_errormsg', 'Alert Threshold updated successfully!' );
			redirect ( base_url () . 'user/user_alert_threshold/' );
		} else {
			$where = array (
					'id' => $accountinfo ["id"] 
			);
			$account = $this->db_model->getSelect ( "notify_credit_limit,notify_flag,notify_email", "accounts", $where );
			$data ['form'] = $this->form->build_form ( $this->user_form->user_alert_threshold (), ( array ) $account->first_row () );
			$this->load->view ( 'view_user_alert_threshold', $data );
		}
	}
	function user_cdrs_report() {
		$accountinfo = $this->session->userdata ( 'accountinfo' );
		$data ['page_title'] = gettext ( 'CDRs' );
		$data ['search_flag'] = true;
		$data ["grid_buttons"] = $this->user_form->build_cdrs_report_buttons ();
		$data ['grid_fields'] = $this->user_form->build_cdrs_report ( $accountinfo ['type'] );
		$data ['form_search'] = $this->form->build_serach_form ( $this->user_form->build_cdrs_report_search ( $accountinfo ['type'] ) );
		$this->load->view ( 'view_user_cdrs_report', $data );
	}
	function user_cdrs_report_json() {
		$accountinfo = $this->session->userdata ( 'accountinfo' );
		$variable = $accountinfo ['type'] != 3 ? 'total_debit' : 'total_cost';
		$count_res = $this->user_model->getuser_cdrs_list ( false, "", "" );
		$count_all = ( array ) $count_res->first_row ();
		$paging_data = $this->form->load_grid_config ( $count_all ['count'], $_GET ['rp'], $_GET ['page'] );
		$json_data = $paging_data ["json_paging"];
		$query = $this->user_model->getuser_cdrs_list ( true, $paging_data ["paging"] ["start"], $paging_data ["paging"] ["page_no"], false );
		$grid_fields = json_decode ( $this->user_form->build_cdrs_report ( $accountinfo ['type'] ) );
		$json_data ['rows'] = $this->form->build_grid ( $query, $grid_fields );
		if ($count_all ['count'] > 0) {
			$search_arr = $this->session->userdata ( 'user_cdrs_report_search' );
			$show_seconds = (! empty ( $search_arr ['search_in'] )) ? $search_arr ['search_in'] : 'minutes';
			$duration = ($show_seconds == 'minutes') ? ($count_all ['billseconds'] > 0) ? floor ( $count_all ['billseconds'] / 60 ) . ":" . sprintf ( "%02d", $count_all ['billseconds'] % 60 ) : "00:00" : $count_all ['billseconds'];
			$json_data ['rows'] [] = array (
					"cell" => array (
							"<b>Grand Total</b>",
							"",
							"",
							"",
							$duration,
							"<b>" . $this->common_model->calculate_currency ( $count_all [$variable], "", "", true, false ) . "</b>",
							"",
							"" 
					) 
			);
		}
		echo json_encode ( $json_data );
	}
	function user_cdrs_report_search() {
		$ajax_search = $this->input->post ( 'ajax_search', 0 );
		if ($this->input->post ( 'advance_search', TRUE ) == 1) {
			$this->session->set_userdata ( 'advance_search', $this->input->post ( 'advance_search' ) );
			$action = $this->input->post ();
			unset ( $action ['action'] );
			unset ( $action ['advance_search'] );
			if (isset ( $action ['debit'] ['debit'] ) && $action ['debit'] ['debit'] != '') {
				$action ['debit'] ['debit'] = $this->common_model->add_calculate_currency ( $action ['debit'] ['debit'], "", '', true, false );
			}
			$this->session->set_userdata ( 'user_cdrs_report_search', $action );
		}
		if (@$ajax_search != 1) {
			redirect ( base_url () . 'user/user_cdrs_report/' );
		}
	}
	function user_cdrs_report_clearsearchfilter() {
		$this->session->set_userdata ( 'advance_search', 0 );
		$this->session->set_userdata ( 'user_cdrs_report_search', "" );
	}
	function user_cdrreport_export() {
		$account_info = $accountinfo = $this->session->userdata ( 'accountinfo' );
		$currency_id = $account_info ['currency_id'];
		$currency = $this->common->get_field_name ( 'currency', 'currency', $currency_id );
		$count_res = $this->user_model->getuser_cdrs_list ( false, "", "" );
		$count_all = ( array ) $count_res->first_row ();
		ob_clean ();
		$customer_array [] = array (
				gettext ( "Date" ),
				gettext ( "Caller ID" ),
				gettext ( "Called Number" ),
				gettext ( "Code" ),
				gettext ( "Destination" ),
				gettext ( "Duration" ),
				gettext ( "Debit" ) . "(" . $currency . ")",
				gettext ( "Disposition" ),
				gettext ( "Call Type" ) 
		);
		if ($count_all ['count'] > 0) {
			$query = $this->user_model->getuser_cdrs_list ( true, '', '', true );
			$currency_info = $this->common->get_currency_info ();
			$search_arr = $this->session->userdata ( 'user_cdrs_report_search' );
			$show_seconds = (! empty ( $search_arr ['search_in'] )) ? $search_arr ['search_in'] : 'minutes';
			foreach ( $query->result_array () as $value ) {
				$duration = ($show_seconds == 'minutes') ? ($value ['billseconds'] > 0) ? floor ( $value ['billseconds'] / 60 ) . ":" . sprintf ( "%02d", $value ['billseconds'] % 60 ) : "00:00" : $value ['billseconds'];
				$customer_array [] = array (
						$this->common->convert_GMT_to ( '', '', $value ['callstart'] ),
						$value ['callerid'],
						$value ['callednum'],
						filter_var ( $value ['pattern'], FILTER_SANITIZE_NUMBER_INT ),
						$value ['notes'],
						$duration,
						$this->common->calculate_currency_manually ( $currency_info, $value ['debit'], false ),
						$value ['disposition'],
						$value ['calltype'] 
				);
			}
			$duration = ($show_seconds == 'minutes') ? ($count_all ['billseconds'] > 0) ? floor ( $count_all ['billseconds'] / 60 ) . ":" . sprintf ( "%02d", $count_all ['billseconds'] % 60 ) : "00:00" : $count_all ['billseconds'];
			$customer_array [] = array (
					"Grand Total",
					"",
					"",
					"",
					"",
					$duration,
					$this->common->calculate_currency_manually ( $currency_info, $count_all ['total_debit'] ),
					"",
					"" 
			);
		}
		$this->load->helper ( 'csv' );
		array_to_csv ( $customer_array, 'Customer_CDR_' . date ( "Y-m-d" ) . '.csv' );
	}
	function user_payment($action = "") {
		if (common_model::$global_config ['system_config'] ['paypal_status'] == 1) {
			redirect ( base_url () . 'user/user/' );
		}
		$this->load->module ( "user/payment" );
		if ($action == "GET_AMT") {
			$amount = $this->input->post ( "value", true );
			$this->payment->convert_amount ( $amount );
		} else {
			$this->payment->index ();
		}
	}
	function user_fund_transfer(){
		$data['page_title'] = gettext('Fund Transfer');
		$accountinfo = $this->session->userdata('accountinfo');
		$account=(array)$this->db->get_where('accounts',array("id"=>$accountinfo['id']))->first_row();
		$currency = (array)$this->db->get_where('currency',array("id"=>$account['currency_id']))->first_row();
		$data['form'] = $this->form->build_form($this->user_form->build_user_fund_transfer_form($account['number'], $currency['currency'], $accountinfo['id']), '');
		$this->load->view('view_user_fund_transfer', $data);
	}
	function user_fund_transfer_save() {
		$data['page_title'] = gettext('Fund Transfer');
		$post_array = $this->input->post();
		$accountinfo = $this->session->userdata('accountinfo');
		$account=(array)$this->db->get_where('accounts',array("id"=>$accountinfo['id']))->first_row();
		$currency = (array)$this->db->get_where('currency',array("id"=>$account['currency_id']))->first_row();
		$data['form'] = $this->form->build_form($this->user_form->build_user_fund_transfer_form($account['number'], $currency['currency'], $accountinfo['id']), $post_array);
		if ($this->form_validation->run() == FALSE) {
			$data['validation_errors'] = validation_errors();
		} else {
			if (trim($post_array['fromaccountid']) != trim($post_array['toaccountid'])) {
				$account_info = $this->session->userdata('accountinfo');      
				$acc_balance = $this->common->get_field_name('balance', 'accounts', array('id' => $account_info['id'], 'status' => 0, 'type' => 0, 'deleted' => 0));

                                $balance = ($account_info["posttoexternal"] == 1) ? ($account_info["credit_limit"] - $acc_balance) :($acc_balance );       
               
				$toid = $this->common->get_field_name('id', 'accounts', array('number' => $post_array['toaccountid'], 'status' => 0, 'type' => 0, 'deleted' => 0));
				$toaccountinfo=(array)$this->db->get_where('accounts',array('number' => $post_array['toaccountid'], 'status' => 0, 'type' => 0, 'deleted' => 0),1)->first_row();
				if($toaccountinfo){
				$reseller_id = $toaccountinfo['reseller_id'];
				$post_array['credit'] = $this->common_model->add_calculate_currency($post_array['credit'], '', '', false, false);
				$minimum_fund=(array)$this->db->get_where('system',array("name"=>"minimum_fund_transfer"),1)->first_row();
				if ($post_array['toaccountid'] == $account_info['number']) {
					$this->session->set_flashdata('astpp_notification', 'You can not transfer fund in same account.');
				}
				elseif ($reseller_id != $account_info['reseller_id']) {
					$this->session->set_flashdata('astpp_notification', 'You can only transfer fund in same level account.');
				}
				elseif ($post_array['toaccountid'] == '') {
					$this->session->set_flashdata('astpp_notification', 'Please enter To account number.');
				}
				elseif (empty($post_array['credit'])) {
					$this->session->set_flashdata('astpp_notification', 'Please enter a amount.');
				}
				elseif ($post_array['credit'] > $balance) {
					$this->session->set_flashdata('astpp_notification', 'You have insufficient balance.');
				}
				elseif ($toid <= 0 || !isset($post_array['toaccountid'])) {
					$this->session->set_flashdata('astpp_notification', 'Please enter valid account number.');
				}
				elseif ($post_array['credit'] < 0) {
					$this->session->set_flashdata('astpp_notification', 'Please enter amount greater then 0.');
				}
				elseif ($minimum_fund['value'] >= $post_array['credit']) {
					 $this->session->set_flashdata('astpp_notification', 'You need to enter minimum '.$minimum_fund['value'].' for fund transfer.');
				}
				elseif (!isset($toid) || !isset($post_array['toaccountid'])) {
					$this->session->set_flashdata('astpp_notification', 'Please enter valid account number!');
				}
				elseif ($post_array['credit'] < 0 || $post_array['credit'] > $balance) {
					$this->session->set_flashdata('astpp_notification', 'Insuffiecient amount !');
				}else{
					$from['id'] = $post_array['id'];
					$from['account_currency'] = $post_array['account_currency'];
					$from['accountid'] = $post_array['fromaccountid'];
					if ($account['posttoexternal'] == 1) {
						$from['credit'] = abs($post_array['credit']);
						$from['payment_type'] = '0';
					} else {
						$from['credit'] = abs($post_array['credit']);
						$from['payment_type'] = 'debit';
					}
					$from['posttoexternal'] = $account['posttoexternal'];
                    
					$from['notes'] = $post_array['notes'];
					$from['action'] = 'save';
					$to['id'] = $toid;
					$to['account_currency'] = $post_array['account_currency'];
					$to['accountid'] = $post_array['toaccountid'];
					if ($toaccountinfo['posttoexternal'] == 0) {
						$to['credit'] = abs($post_array['credit']);
						$to['payment_type'] = '0';
					} else {
						$to['credit'] = abs($post_array['credit']);
						$to['payment_type'] = 'debit';
					}
					$to['notes'] = $post_array['notes'];
					$to['action'] = 'save';

					if($account['reseller_id'] == 0){
						$where = array("accountid"=> 1);
					}else{
						$where = array("accountid"=> $account['id']);    
					}
					$query = $this->db_model->getSelect("*", "invoice_conf", $where);
					if($query->num_rows () > 0){
						$invoice_conf = $query->result_array();
						$invoice_conf = $invoice_conf[0];
					}else{
						$query = $this->db_model->getSelect("*", "invoice_conf",array("accountid"=> 1));
						$invoice_conf = $query->result_array();
						$invoice_conf = $invoice_conf[0];            
					}

					$last_invoice_ID = $this->user_invoice_date("invoiceid",$account["id"]);
					$invoice_prefix=$invoice_conf['invoice_prefix'];
					$due_date = gmdate("Y-m-d H:i:s",strtotime(gmdate("Y-m-d H:i:s")." +".$invoice_conf['interval']." days"));
					$this->load->module('invoices/invoices');
					$invoice_id=$this->invoices->invoices->generate_receipt($account['id'],$from['credit'],$accountinfo,$last_invoice_ID+1,$invoice_prefix,$due_date);
					$account_balance = $this->common->get_field_name('balance', 'accounts', $from['id']);
					$before_balance = $account_balance;
					$after_balance = $account_balance  - $from['credit'] ;

					$response = $this->user_model->user_fund_transfer($from,$accountinfo);

					$query = "update accounts set balance =  IF(posttoexternal=1,balance+".$from['credit'] . ",balance-".$from['credit'].") where id ='".$from['id']."'";
					$this->db->query($query);

			                $from_arr = array("accountid" => $from['id'],
					          "description" => trim($post_array['notes']),
					          "debit" => $from['credit'],
					          "credit" =>'0',
					          "created_date" => gmdate("Y-m-d H:i:s"), 
					          "invoiceid"=>0,
					          "reseller_id"=>'0',
					          "item_type"=>'Refill',
					          "item_id"=>'0',
          					  'before_balance'=>$before_balance,
					          'after_balance'=>$after_balance,
					        );
			                $this->db->insert("invoice_details", $from_arr);
					if ($response) {
 						$accountinfo=(array)$this->db->get_where('accounts',array("id"=> $to['id']))->first_row();

						if($accountinfo['reseller_id'] == 0){
							$where = array("accountid"=> 1);
						}else{
							$where = array("accountid"=> $accountinfo['id']);    
						}
						$query = $this->db_model->getSelect("*", "invoice_conf", $where);
						if($query->num_rows () > 0){
							$invoice_conf = $query->result_array();
							$invoice_conf = $invoice_conf[0];
						}else{
							$query = $this->db_model->getSelect("*", "invoice_conf",array("accountid"=> 1));
							$invoice_conf = $query->result_array();
							$invoice_conf = $invoice_conf[0];            
						}

						$last_invoice_ID = $this->user_invoice_date("invoiceid",$accountinfo["id"]);
						$invoice_prefix=$invoice_conf['invoice_prefix'];
						$due_date = gmdate("Y-m-d H:i:s",strtotime(gmdate("Y-m-d H:i:s")." +".$invoice_conf['interval']." days"));
						$this->load->module('invoices/invoices');
						$invoice_id=$this->invoices->invoices->generate_receipt($accountinfo['id'],$to['credit'],$accountinfo,$last_invoice_ID+1,$invoice_prefix,$due_date);
						if ($account['posttoexternal'] == 1) {
							$account_balance = ($accountinfo['creditlimit']*$accountinfo['posttoexternal']) - $accountinfo['balance'];
						} else {
							$account_balance = $accountinfo['balance'];
						}						
						

						$before_balance = $account_balance ;
						$after_balance = $account_balance+$to['credit'] ;

						$toresponse = $this->user_model->user_fund_transfer($to,$accountinfo);

						$query = "update accounts set balance =  IF(posttoexternal=1,balance-".$to['credit'] . ",balance+".$to['credit'].") where id ='".$to['id']."'";
						$this->db->query($query);

			                	$to_arr = array("accountid" => $to['id'],
					          "description" => trim($post_array['notes']),
					          "debit" => '0',
					          "credit" =>$to['credit'],
					          "created_date" => gmdate("Y-m-d H:i:s"), 
					          "invoiceid"=>0,
					          "reseller_id"=>'0',
					          "item_type"=>'Refill',
					          "item_id"=>'0',
          					  'before_balance'=>$before_balance,
					          'after_balance'=>$after_balance,
					        );
				                $this->db->insert("invoice_details", $to_arr);

						$this->session->set_flashdata('astpp_errormsg', 'Transfer success!');
					} else {
						$this->session->set_flashdata('astpp_notification', 'Sorry We are not able to process this request.');
					}
				}
			}else{
		$this->session->set_flashdata('astpp_notification', 'Account number not found.');
			}
			} else {
				$this->session->set_flashdata('astpp_notification', 'You can not transfer fund in same account.');
			}
			redirect(base_url() . 'user/user_fund_transfer/');
		}
		$this->load->view('view_user_fund_transfer', $data);
	}
	function user_opensips() {
		if(common_model::$global_config['system_config']['opensips']== 1){
			$this->permission->permission_redirect_url("user/user/");
		}
		$data ['username'] = $this->session->userdata ( 'user_name' );
		$data ['page_title'] = gettext ( 'Opensips List' );
		$data ['search_flag'] = true;
		$data ["fs_grid_buttons"] = $this->user_form->build_user_opensips_buttons ();
		$data ['grid_fields'] = $this->user_form->build_user_opensips ();
		$data ['form_search'] = $this->form->build_serach_form ( $this->user_form->build_user_opensips_search () );
		$this->load->view ( 'view_opensips_list', $data );
	}
	function user_opensips_json() {
		$accountinfo = $this->session->userdata ( "accountinfo" );
		$json_data = array ();
		$count_all = $this->user_model->get_user_opensips ( false, $accountinfo ['number'] );
		$paging_data = $this->form->load_grid_config ( $count_all, $_GET ['rp'], $_GET ['page'] );
		$json_data = $paging_data ["json_paging"];
		$query = $this->user_model->get_user_opensips ( true, $accountinfo ['number'], $paging_data ["paging"] ["start"], $paging_data ["paging"] ["page_no"] );
		$grid_fields = json_decode ( $this->user_form->build_user_opensips () );
		$json_data ['rows'] = $this->form->build_grid ( $query, $grid_fields );
		echo json_encode ( $json_data );
	}
	function user_opensips_clearsearchfilter() {
		$this->session->set_userdata ( 'advance_search', 0 );
		$this->session->set_userdata ( 'user_opensips_search', "" );
	}
	function user_opensips_search() {
		$ajax_search = $this->input->post ( 'ajax_search', 0 );
		
		if ($this->input->post ( 'advance_search', TRUE ) == 1) {
			$this->session->set_userdata ( 'advance_search', $this->input->post ( 'advance_search' ) );
			$action = $this->input->post ();
			unset ( $action ['action'] );
			unset ( $action ['advance_search'] );
			$this->session->set_userdata ( 'user_opensips_search', $action );
		}
		if ($ajax_search != 1) {
			redirect ( base_url () . 'user/user_opensips/' );
		}
	}
	function user_opensips_add() {
		$accountinfo = $this->session->userdata ( "accountinfo" );
		$data ['username'] = $this->session->userdata ( 'user_name' );
		$data ['flag'] = 'create';
		$data ['page_title'] = gettext ( 'Create Opensips' );
		$data ['form'] = $this->form->build_form ( $this->user_form->build_user_opensips_form (), '' );
		$this->load->view ( 'view_opensips_add_edit', $data );
	}
	function user_opensips_edit($edit_id) {
		$data ['page_title'] = gettext ( 'Edit Opensips' );
		$db_config = Common_model::$global_config ['system_config'];
		$opensipdsn = "mysqli://" . $db_config ['opensips_dbuser'] . ":" . $db_config ['opensips_dbpass'] . "@" . $db_config ['opensips_dbhost'] . "/" . $db_config ['opensips_dbname'] . "?char_set=utf8&dbcollat=utf8_general_ci&cache_on=true&cachedir=";
		$this->opensips_db = $this->load->database ( $opensipdsn, true );
		$where = array (
				'id' => $edit_id 
		);
		$this->opensips_db->where ( $where );
		$account = $this->opensips_db->get ( "subscriber" );
		foreach ( $account->result_array () as $key => $value ) {
			$edit_data = $value;
		}
		$data ['form'] = $this->form->build_form ( $this->user_form->build_user_opensips_form ( $edit_id ), $edit_data );
		$this->load->view ( 'view_opensips_add_edit', $data );
	}
	function user_opensips_save() {
		$add_array = $this->input->post ();
		$data ['form'] = $this->form->build_form ( $this->user_form->build_user_opensips_form (), $add_array );
		if ($add_array ['id'] != '') {
			$data ['page_title'] = gettext ( 'Edit Opensips' );
			if ($this->form_validation->run () == FALSE) {
				$data ['validation_errors'] = validation_errors ();
				echo $data ['validation_errors'];
				exit ();
			} else {
				$auth_flag = $this->validate_device_data ( $add_array );
				if ($auth_flag == "TRUE") {
					$this->user_model->user_opensips_edit ( $add_array, $add_array ['id'] );
					echo json_encode ( array (
							"SUCCESS" => " OpenSips updated successfully!" 
					) );
					exit ();
				} else {
					echo json_encode ( $auth_flag );
					exit ();
				}
			}
		} else {
			$data ['page_title'] = gettext ( 'Add Opensips' );
			if ($this->form_validation->run () == FALSE) {
				$data ['validation_errors'] = validation_errors ();
				echo $data ['validation_errors'];
				exit ();
			} else {
				$auth_flag = $this->validate_device_data ( $add_array );
				if ($auth_flag == "TRUE") {
					$this->user_model->user_opensips_add ( $add_array );
					echo json_encode ( array (
							"SUCCESS" => "OpenSips added successfully!" 
					) );
					exit ();
				} else {
					echo json_encode ( $auth_flag );
					exit ();
				}
			}
		}
	}
	function validate_device_data($data) {
		if (isset ( $data ["username"] ) && $data ["username"] != "") {
			$db_config = Common_model::$global_config ['system_config'];
			$opensipdsn = "mysqli://" . $db_config ['opensips_dbuser'] . ":" . $db_config ['opensips_dbpass'] . "@" . $db_config ['opensips_dbhost'] . "/" . $db_config ['opensips_dbname'] . "?char_set=utf8&dbcollat=utf8_general_ci&cache_on=true&cachedir=";
			$this->opensips_db = $this->load->database ( $opensipdsn, true );
			$where = array (
					"username" => $data ["username"] 
			);
			if ($data ['id'] != "") {
				$this->opensips_db->where ( "id <>", $data ['id'] );
			}
			$this->opensips_db->where ( $where );
			$auth_flag = $this->opensips_db->get ( "subscriber" );
			$auth_flag = $auth_flag->num_rows ();
			if ($auth_flag == 0) {
				return "TRUE";
			} else {
				return array (
						"username_error" => "Duplicate Username Found.Username Must be Unique" 
				);
			}
		} else {
			return array (
					"username_error" => "User name is required field." 
			);
		}
		return "0";
	}
	function user_opensips_delete($id) {
		$this->user_model->user_opensips_delete ( $id );
		$this->session->set_flashdata ( 'astpp_errormsg', 'Opensips Device Removed Successfully!.' );
		redirect ( base_url () . "user/user_opensips/" );
	}
	function user_opensips_delete_multiple() {
		$db_config = Common_model::$global_config ['system_config'];
		$opensipdsn = "mysqli://" . $db_config ['opensips_dbuser'] . ":" . $db_config ['opensips_dbpass'] . "@" . $db_config ['opensips_dbhost'] . "/" . $db_config ['opensips_dbname'] . "?char_set=utf8&dbcollat=utf8_general_ci&cache_on=true&cachedir=";
		$this->opensips_db = $this->load->database ( $opensipdsn, true );
		$ids = $this->input->post ( "selected_ids", true );
		$where = "id IN ($ids)";
		$this->opensips_db->where ( $where );
		$this->opensips_db->delete ( "subscriber" );
		echo TRUE;
	}
	function user_cdrs() {
		$data ['username'] = $this->session->userdata ( 'user_name' );
		$accountinfo = $this->session->userdata ( 'accountinfo' );
		$data ['page_title'] = gettext ( 'CDRs' );
		$accounttype = strtolower ( $this->common->get_entity_type ( '', '', $accountinfo ['type'] ) );
		$this->load->module ( 'reports/reports' );
		$data ['grid_fields'] = $this->reports->reports_form->build_report_list_for_user ( $accounttype );
		$data ['form_search'] = $this->form->build_serach_form ( $this->user_form->build_user_opensips_search () );
		$data ['accounttype'] = $accounttype;
		$this->load->view ( 'view_user_cdrs', $data );
	}
	function user_cdrs_json() {
		$accountinfo = $this->session->userdata ( 'accountinfo' );
		$accounttype = strtolower ( $this->common->get_entity_type ( '', '', $accountinfo ['type'] ) );
		$this->load->module ( 'reports/reports' );
		$this->reports->customer_cdrreport ( $accountinfo ['id'], $accounttype );
	}
	function user_details_search($module_name) {
		$action = $this->input->post ();
		$this->session->set_userdata ( 'left_panel_search_' . $module_name, "" );
		if (! empty ( $action ['left_panel_search'] )) {
			$this->session->set_userdata ( 'left_panel_search_' . $module_name, $action ['left_panel_search'] );
		}
	}
	function user_provider_report_export() {
		$this->load->module ( 'reports/reports' );
		$this->provider_cdrreport_export ();
	}
	function user_provider_cdrs_report() {
		$accountinfo = $this->session->userdata ( 'accountinfo' );
		$data ['page_title'] = gettext ( 'Provider CDRs Report' );
		$data ['search_flag'] = true;
		$data ["grid_buttons"] = $this->user_form->build_provider_report_buttons ();
		$data ['grid_fields'] = $this->user_form->build_provider_report ( $accountinfo ['type'] );
		$data ['form_search'] = $this->form->build_serach_form ( $this->user_form->build_provider_report_search ( $accountinfo ['type'] ) );
		$this->load->view ( 'view_provider_cdrs_report', $data );
	}
	function user_provider_cdrs_report_json() {
		$accountinfo = $this->session->userdata ( 'accountinfo' );
		$variable = $accountinfo ['type'] != 3 ? 'total_debit' : 'total_cost';
		$count_res = $this->user_model->getprovider_cdrs_list ( false, "", "" );
		$count_all = ( array ) $count_res->first_row ();
		$paging_data = $this->form->load_grid_config ( $count_all ['count'], $_GET ['rp'], $_GET ['page'] );
		$json_data = $paging_data ["json_paging"];
		$query = $this->user_model->getprovider_cdrs_list ( true, $paging_data ["paging"] ["start"], $paging_data ["paging"] ["page_no"], false );
		$grid_fields = json_decode ( $this->user_form->build_provider_report ( $accountinfo ['type'] ) );
		$json_data ['rows'] = $this->form->build_grid ( $query, $grid_fields );
		if ($count_all ['count'] > 0) {
			$search_arr = $this->session->userdata ( 'user_provider_cdrs_report_search' );
			$show_seconds = (! empty ( $search_arr ['search_in'] )) ? $search_arr ['search_in'] : 'minutes';
			$duration = ($show_seconds == 'minutes') ? ($count_all ['billseconds'] > 0) ? floor ( $count_all ['billseconds'] / 60 ) . ":" . sprintf ( "%02d", $count_all ['billseconds'] % 60 ) : "00:00" : $count_all ['billseconds'];
			$json_data ['rows'] [] = array (
					"cell" => array (
							"<b>Grand Total</b>",
							"",
							"",
							"",
							$duration,
							"<b>" . $this->common_model->calculate_currency ( $count_all [$variable], "", "", true, false ) . "</b>",
							"",
							"" 
					) 
			);
		}
		echo json_encode ( $json_data );
	}
	function user_provider_cdrs_report_search() {
		$ajax_search = $this->input->post ( 'ajax_search', 0 );
		if ($this->input->post ( 'advance_search', TRUE ) == 1) {
			$this->session->set_userdata ( 'advance_search', $this->input->post ( 'advance_search' ) );
			$action = $this->input->post ();
			if (isset ( $action ['cost'] ['cost'] ) && $action ['cost'] ['cost'] != '') {
				$action ['cost'] ['cost'] = $this->common_model->add_calculate_currency ( $action ['cost'] ['cost'], "", '', true, false );
			}
			unset ( $action ['action'] );
			unset ( $action ['advance_search'] );
			$this->session->set_userdata ( 'user_provider_cdrs_report_search', $action );
		}
		if ($ajax_search != 1) {
			redirect ( base_url () . 'user/user_provider_cdrs_report/' );
		}
	}
	function user_provider_cdrs_report_clearsearchfilter() {
		$this->session->set_userdata ( 'advance_search', 0 );
		$this->session->set_userdata ( 'user_provider_cdrs_report_search', "" );
	}
	function user_provider_cdrreport_export() {
		$account_info = $accountinfo = $this->session->userdata ( 'accountinfo' );
		$currency_id = $account_info ['currency_id'];
		$currency = $this->common->get_field_name ( 'currency', 'currency', $currency_id );
		$count_res = $this->user_model->getprovider_cdrs_list ( false, "", "" );
		$count_all = ( array ) $count_res->first_row ();
		ob_clean ();
		$customer_array [] = array (
				gettext ( "Date" ),
				gettext ( "Caller ID" ),
				gettext ( "Called Number" ),
				gettext ( "Destination" ),
				gettext ( "Duration" ),
				gettext ( "Cost" ) . "(" . $currency . ")",
				gettext ( "Disposition" ),
				gettext ( "Call Type" ) 
		);
		if ($count_all ['count'] > 0) {
			$query = $this->user_model->getuser_cdrs_list ( true, '', '', true );
			$currency_info = $this->common->get_currency_info ();
			$search_arr = $this->session->userdata ( 'user_provider_cdrs_report_search' );
			$show_seconds = (! empty ( $search_arr ['search_in'] )) ? $search_arr ['search_in'] : 'minutes';
			foreach ( $query->result_array () as $value ) {
				$duration = ($show_seconds == 'minutes') ? ($value ['billseconds'] > 0) ? floor ( $value ['billseconds'] / 60 ) . ":" . sprintf ( "%02d", $value ['billseconds'] % 60 ) : "00:00" : $value ['billseconds'];
				$customer_array [] = array (
						$this->common->convert_GMT_to ( '', '', $value ['callstart'] ),
						$value ['callerid'],
						$value ['callednum'],
						filter_var ( $value ['pattern'], FILTER_SANITIZE_NUMBER_INT ),
						$duration,
						$this->common->calculate_currency_manually ( $currency_info, $value ['cost'], false ),
						$value ['disposition'],
						$value ['calltype'] 
				);
			}
			$duration = ($show_seconds == 'minutes') ? ($count_all ['billseconds'] > 0) ? floor ( $count_all ['billseconds'] / 60 ) . ":" . sprintf ( "%02d", $count_all ['billseconds'] % 60 ) : "00:00" : $count_all ['billseconds'];
			$customer_array [] = array (
					"Grand Total",
					"",
					"",
					"",
					$duration,
					$this->common->calculate_currency_manually ( $currency_info, $count_all ['total_cost'], false, true ),
					"",
					"" 
			);
		}
		$this->load->helper ( 'csv' );
		array_to_csv ( $customer_array, 'Provider_CDR_' . date ( "Y-m-d" ) . '.csv' );
	}
	function user_speeddial() {
		$data ['page_title'] = gettext ( "Speed Dial" );
		$accountinfo = $this->session->userdata ( 'accountinfo' );
		$speeddial_res = $this->db->get_where ( "speed_dial", array (
				"accountid" => $accountinfo ['id'] 
		) );
		$speeddial_info = array ();
		if ($speeddial_res->num_rows () > 0) {
			$speeddial_res = $speeddial_res->result_array ();
			foreach ( $speeddial_res as $key => $value ) {
				$speeddial_info [$value ['speed_num']] = $value ['number'];
			}
		}
		$data ['speeddial'] = $speeddial_info;
		$data ['account_data'] = $accountinfo;
		$this->load->view ( 'view_user_speeddial', $data );
	}
	function user_speeddial_save() {
		$add_array = $this->input->post ();
		$accountinfo = $this->session->userdata ( 'accountinfo' );
		$where = array (
				"accountid" => $accountinfo ['id'] 
		);
		$this->db->select ( 'count(id) as count' );
		$this->db->where ( $where );
		$speed_dial_result = ( array ) $this->db->get ( 'speed_dial' )->first_row ();
		if ($speed_dial_result ['count'] == 0) {
			for($i = 0; $i <= 9; $i ++) {
				$dest_number = $add_array ['number'] == $i ? $add_array ['destination'] : '';
				$data [$i] = array (
						"number" => $dest_number,
						"speed_num" => $i,
						'accountid' => $accountinfo ['id'] 
				);
			}
			$this->db->insert_batch ( 'speed_dial', $data );
		} else {
			$this->db->where ( 'speed_num', $add_array ['number'] );
			$this->db->where ( 'accountid', $accountinfo ['id'] );
			$result = $this->db->update ( 'speed_dial', array (
					'number' => $add_array ['destination'] 
			) );
		}
	}
	function user_speeddial_remove() {
		$accountinfo = $this->session->userdata ( 'accountinfo' );
		$add_array = $this->input->post ();
		$updateinfo = array (
				'number' => '' 
		);
		$this->db->where ( 'speed_num', $add_array ['number'] );
		$this->db->where ( 'accountid', $accountinfo ['id'] );
		$result = $this->db->update ( 'speed_dial', $updateinfo );
	}
}

?>

