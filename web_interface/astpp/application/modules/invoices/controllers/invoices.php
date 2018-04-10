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
class Invoices extends MX_Controller {
	function Invoices() {
		parent::__construct ();
		
		$this->load->helper ( 'template_inheritance' );
		
		$this->load->library ( 'session' );
		$this->load->library ( 'invoices_form' );
		$this->load->library ( 'astpp/form' );
		$this->load->library ( 'astpp/permission' );
		$this->load->model ( 'invoices_model' );
		$this->load->model ( 'Astpp_common' );
		$this->load->model ( 'common_model' );
		$this->load->library ( "astpp/email_lib" );
		$this->load->library ( 'fpdf' );
		$this->load->library ( 'pdf' );
		
		if ($this->session->userdata ( 'user_login' ) == FALSE)
			redirect ( base_url () . '/astpp/login' );
	}
	function invoice_list() {
		$data ['username'] = $this->session->userdata ( 'user_name' );
		$data ['page_title'] = gettext ( 'Invoices' );
		$data ['login_type'] = $this->session->userdata ['userlevel_logintype'];
		$data ['search_flag'] = true;
		$this->session->set_userdata ( 'advance_search', 0 );
		$data ['grid_fields'] = $this->invoices_form->build_invoices_list_for_admin ();
		$data ["grid_buttons"] = $this->invoices_form->build_grid_buttons ();
		$data ['form_search'] = $this->form->build_serach_form ( $this->invoices_form->get_invoice_search_form () );
		$account = $this->db_model->getSelect ( 'id,first_name,last_name,number', 'accounts', array (
				'status' => 0,
				'type' => '0,3',
				'deleted' => 0 
		) );
		$data ['account_value'] = $account->result_array ();
		$this->load->view ( 'view_invoices_list', $data );
	}
	// /*ASTPP_invoice_changes_05_05_start*/
	function invoice_list_json() {
		$login_info = $this->session->userdata ( 'accountinfo' );
		$logintype = $this->session->userdata ( 'logintype' );
		$json_data = array ();
		$count_all = $this->invoices_model->get_invoice_list ( false );
		$paging_data = $this->form->load_grid_config ( $count_all, $_GET ['rp'], $_GET ['page'] );
		$json_data = $paging_data ["json_paging"];
		$result_query = $this->invoices_model->get_invoice_list ( true, $paging_data ["paging"] ["start"], $paging_data ["paging"] ["page_no"] );
		if ($logintype == - 1) {
			$currency_id = Common_model::$global_config ['system_config'] ['base_currency'];
		} else {
			$accountdata ["currency_id"] = $this->common->get_field_name ( 'currency', 'currency', $login_info ["currency_id"] );
			$currency_id = $accountdata ["currency_id"];
		}
		$grid_fields = json_decode ( $this->invoices_form->build_invoices_list_for_admin () );
		$url = ($logintype == 0 || $logintype == 3) ? "/user/user_invoice_download/" : '/invoices/invoice_download/';
		if ($result_query->num_rows () > 0) {
			$query = $result_query->result_array ();
			$total_value = 0;
			$ountstanding_value = 0;
			foreach ( $query as $key => $value ) {
				$delete_button = '';
				$date = strtotime ( $value ['invoice_date'] );
				$invoice_date = date ( "Y-m-d", $date );
				$fromdate = strtotime ( $value ['from_date'] );
				$from_date = date ( "Y-m-d", $fromdate );
				$due_date = date("Y-m-d",strtotime ( $value ['due_date'] ));				
				$outstanding = $value ['amount'];
				$last_payment_date = '';
				$invoice_total_query = $this->db_model->select ( "sum(debit) as debit,sum(credit) as credit,created_date", "invoice_details", array (
						"invoiceid" => $value ['id'],
						"item_type" => "INVPAY" 
				), "created_date", "DESC", "1", "0" );
				// echo $this->db->last_query(); exit;
				if ($invoice_total_query->num_rows () > 0) {
					$invoice_total_query = $invoice_total_query->result_array ();
					// echo '<pre>'; print_r($invoice_total_query);
					if ($value ['type'] == 'I') {
						$outstanding -= $this->common->currency_decimal ( $invoice_total_query [0] ['credit'] );
					} else {
						$outstanding = '';
					}
					$last_payment_date = $invoice_total_query [0] ['created_date'];
					if ($value ['type'] == 'I') {
						if ($last_payment_date) {
							$payment_date = strtotime ( $last_payment_date );
							$payment_last = date ( "Y-m-d", $payment_date );
						} else {
						    $payment_last = ($value ['amount']>0)?'':$invoice_date;
						}
					} else {
						$payment_last = $invoice_date;
					}
				}
				
				$invoice_total = '';
				$accountinfo = $this->session->userdata ( 'accountinfo' );
				$id = $accountinfo ['id'];
				if ($accountinfo ['type'] == 1) {
					$query = "select sum(amount) as grand_total from invoices where reseller_id='$id'";
				} else {
					$query = "select sum(amount) as grand_total from invoices where reseller_id='0'";
				}
				$ext_query = $this->db->query ( $query );
				if ($ext_query->num_rows () > 0) {
					$result_total = $ext_query->result_array ();
					$grandtotal = $result_total [0] ['grand_total'];
					$grand_total = $this->common->currency_decimal ( $grandtotal ) . ' ' . $currency_id;
				}
				
				if ($accountinfo ['type'] == 1) {
					$invoice_query = "select sum(credit) as grand_credit from invoice_details where reseller_id='$id'";
				} else {
					$invoice_query = "select sum(credit) as grand_credit from invoice_details where reseller_id='0'";
				}
				$credit_query = $this->db->query ( $invoice_query );
				if ($credit_query->num_rows () > 0) {
					$credit_total = $credit_query->result_array ();
					$grand_credit_total = $credit_total [0] ['grand_credit'];
					$grandcredit = $grand_total - $grand_credit_total;
					$grand_credit = $this->common->currency_decimal ( $grandcredit ) . ' ' . $currency_id;
				}
				$download = "<a  href=" . $url . $value ['id'] . '/00_' . $value ['invoice_prefix'] . $value ['invoiceid'] . " class='btn btn-royelblue btn-sm'  title='Download Invoice' ><i class='fa fa-cloud-download fa-fw'></i></a>&nbsp";
				if ($value ['type'] == 'I') {
					if ($value ['confirm'] == 0) {
						if ($value ['generate_type'] == 1) {
							$payment = '<a href="' . base_url () . 'invoices/invoice_manually_edit/' . $value ['id'] . '" class="btn btn-royelblue btn-sm"  title="Edit"><i class="fa fa-pencil-square-o fa-fw"></i></a>';
						} else {
							$payment = '<a href="' . base_url () . 'invoices/invoice_automatically_edit/' . $value ['id'] . '" class="btn btn-royelblue btn-sm"  title="Edit"><i class="fa fa-pencil-square-o fa-fw"></i></a>';
						}
						$id = $value ['id'];
						$delete_button = "<a onclick='invoice_delete($id)' class='btn btn-royelblue btn-sm'  title='Delete' ><i class='fa fa-trash fa-fw'></i></a>&nbsp";
					} else {
						if ($outstanding > 0) {
							$payment = '<a style="padding: 0 8px;" href="' . base_url () . 'invoices/invoice_summary/' . $value ['id'] . '" class="btn btn-warning"  title="Payment">Unpaid</i></a>';
						} else {
							$payment = '<button style="padding: 0 17px;" type="button"  class="btn btn-success">Paid</button>';
						}
						$delete_button = "&nbsp";
					}
				} else {
					$payment = '';
				}
				$account_arr = $this->db_model->getSelect ( 'first_name,number,last_name', 'accounts', array (
						'id' => $value ['accountid'] 
				) );
				$account_array = $account_arr->result_array ();
				if ($value ['generate_type'] == 1) {
					$invoice_type = 'Manually';
				} else {
					$invoice_type = 'Automatically';
				}
				if ($value ['deleted'] == 1) {
					$download = '';
					$payment = '<button style="padding: 0 17px;" type="button"  class="btn btn-line-sky">Deleted</button>';
					$delete_button = '';
				}

				if ($value ['type'] == 'R') {
					$icon = '<div class="flx_font flx_magenta">R</div>';
				} else {
					$icon = '<div class="flx_font flx_drk_pink">I</div>';
				}
				
				$json_data ['rows'] [] = array (
						'cell' => array (
								
								$value ['invoice_prefix'] . $value ['invoiceid'] . $icon,
								// $value['invoice_prefix'].$value['invoiceid'].'('.$value['type'].')',
								$invoice_type,
								$account_array [0] ['first_name'] . ' ' . $account_array [0] ['last_name'] . '</br>' . $account_array [0] ['number'],
								$invoice_date,
								$from_date,
								$due_date,
								$payment_last,
								$this->common->currency_decimal ( $this->common_model->calculate_currency ( $value ['amount'] ) ),
								$this->common->currency_decimal ( $this->common_model->calculate_currency ( $outstanding ) ),
								$download . '' . $payment . ' ' . $delete_button 
						)
						 
				);
				$total_value = $total_value + $value ['amount'];
				$ountstanding_value = $ountstanding_value + $outstanding;
			}
		}
		echo json_encode ( $json_data );
	}
	// end
	function invoice_manually_edit($id) {
		$confirm = $this->common->get_field_name ( 'confirm', 'invoices', $id );
		$deleted = $this->common->get_field_name ( 'deleted', 'invoices', $id );
		if ($confirm == 1 || $deleted == 1) {
			redirect ( base_url () . 'invoices/invoice_list/' );
		}
		$data ['total_tax_dis'] = 0;
		$data ['total_credit_dis'] = 0;
		$query = "SELECT  * from invoice_details where generate_type=1 AND invoiceid='$id' ";
		$invoice_total_query = $this->db->query ( $query );
		$data ['count'] = 0;
		$data ['row_count'] = 5;
		if ($invoice_total_query->num_rows () > 0) {
			$count = $invoice_total_query->num_rows ();
			$data ['count'] = $count;
			$invoice_total_query = $invoice_total_query->result_array ();
			$i = 1;
			$taxi = 0;
			$get_data = array ();
			$data ['total_tax_dis'] = array ();
			foreach ( $invoice_total_query as $value ) {
				if ($value ['item_type'] == 'TAX') {
					$data ['total_tax_dis'] [$taxi] = $value ['debit'];
					$taxi ++;
				} else {
					if ($i >= 5) {
						$data ['row_count'] = $i + 1;
					}
					$get_data ['invoice_from_date_' . $i] = $value ['created_date'];
					$get_data ['invoice_description_' . $i] = $value ['description'];
					$get_data ['invoice_amount_' . $i] = $value ['debit'];
					$i ++;
					$data ['total_credit_dis'] += $value ['debit'];
				}
			}
			$data ['get_data'] = $get_data;
		}
		$account_data = $this->session->userdata ( "accountinfo" );
		$logintype = $this->session->userdata ( 'logintype' );
		$invoice_total = '';
		$query = "SELECT  * from invoice_details where item_type='INVPAY' AND invoiceid='$id' ORDER BY created_date ASC";
		$invoice = $this->db_model->getSelect ( "*", "invoices", array (
				"id" => $id 
		) );
		if ($invoice->num_rows () > 0) {
			$invoice = $invoice->result_array ();
			$result = $invoice [0];
			$data ['payment_due_date'] = $result ['due_date'];
		}
		
		$accountdata = $this->db_model->getSelect ( "*", "accounts", array (
				"id" => $result ['accountid'] 
		) );
		if ($accountdata->num_rows () > 0) {
			$accountdata = $accountdata->result_array ();
			$accountdata = $accountdata [0];
		}
		$data ['taxes_count'] = 0;
		$taxes = $this->db_model->getSelect ( "*", "taxes_to_accounts", array (
				"accountid" => $result ['accountid'] 
		) );
		$total_tax = 0;
		$data ['taxes_count'] = $taxes->num_rows ();
		if ($taxes->num_rows () > 0) {
			$taxes = $taxes->result_array ();
			foreach ( $taxes as $tax_value ) {
				$taxe_res = $this->db_model->getSelect ( "*", "taxes", array (
						"id" => $tax_value ['taxes_id'] 
				) );
				$taxe_res = $taxe_res->result_array ();
				foreach ( $taxe_res as $taxe_res_val ) {
					$data ['taxes_to_accounts'] [] = $taxe_res_val;
					$total_tax += $taxe_res_val ['taxes_rate'];
				}
			}
		}
		
		$system_config = common_model::$global_config ['system_config'];
		if ($system_config ["paypal_mode"] == 0) {
			$data ["paypal_url"] = $system_config ["paypal_url"];
			$data ["paypal_email_id"] = $system_config ["paypal_id"];
		} else {
			$data ["paypal_url"] = $system_config ["paypal_sandbox_url"];
			$data ["paypal_email_id"] = $system_config ["paypal_sandbox_id"];
		}
		$date = strtotime ( $result ['invoice_date'] );
		$data ['time'] = date ( "Y-m-d h:i:s ", $date );
		$data ["paypal_tax"] = $system_config ["paypal_tax"];
		$data ["from_currency"] = $this->common->get_field_name ( 'currency', 'currency', $account_data ["currency_id"] );
		$data ["to_currency"] = Common_model::$global_config ['system_config'] ['base_currency'];
		if ($account_data ['type'] == - 1) {
			$data ["to_currency"] = $data ["to_currency"];
		} elseif ($account_data ['type'] == 1) {
			$accountdata ["currency_id"] = $this->common->get_field_name ( 'currency', 'currency', $account_data ["currency_id"] );
			$data ["to_currency"] = $accountdata ["currency_id"];
		} else {
			$accountdata ["currency_id"] = $this->common->get_field_name ( 'currency', 'currency', $account_data ["currency_id"] );
			$data ["to_currency"] = $accountdata ["currency_id"];
		}
		$data ['total_tax'] = $total_tax;
		$data ['invoice_notes'] = $result ['notes'];
		$data ['from_date'] = $result ['from_date'];
		$data ['to_date'] = $result ['to_date'];
		$data ['invoice_date'] = $result ['invoice_date'];
		$data ['amount'] = $this->common_model->calculate_currency ( $result ['amount'], '', '', '', '' );
		$data ['invoice_prefix'] = $result ['invoice_prefix'];
		$data ['page_title'] = gettext ( 'Invoice Summary' );
		$data ['invoice_date'] = $result ['invoice_date'];
		$data ['return'] = base_url () . "invoices/invoice_list_modified";
		$data ['cancel_return'] = base_url () . "invoice/invoice_list_cancel";
		$data ['paypal_mode'] = 1;
		$data ['prefix_id'] = $result ['invoiceid'];
		$data ['logintype'] = $logintype;
		$data ['accountdata'] = $accountdata;
		$data ['id'] = $id;
		$data ['notify_url'] = base_url () . "invoices/invoice_list_get_data";
		if ($account_data ['type'] == '1') {
			$data ['response_url'] = base_url () . "invoices/invoice_list_responce/";
		} else {
			$data ['response_url'] = base_url () . "user/user_list_responce/";
		}
		$data ['sucess_url'] = base_url () . "invoices/invoice_list_sucess";
		$this->load->view ( 'view_invoice_edit_manually', $data );
	}
	function invoice_automatically_edit($id) {
		$confirm = $this->common->get_field_name ( 'confirm', 'invoices', $id );
		$deleted = $this->common->get_field_name ( 'deleted', 'invoices', $id );
		if ($confirm == 1 || $deleted == 1) {
			redirect ( base_url () . 'invoices/invoice_list/' );
		}
		$data ['total_tax_dis'] = 0;
		$data ['total_credit_dis'] = 0;
		$query = "SELECT  * from invoice_details where invoiceid='$id' ";
		$invoice_total_query = $this->db->query ( $query );
		$data ['count'] = 0;
		$data ['row_count'] = 5;
		if ($invoice_total_query->num_rows () > 0) {
			$count = $invoice_total_query->num_rows ();
			$data ['count'] = $count;
			$invoice_total_query = $invoice_total_query->result_array ();
			$i = 1;
			$taxi = 0;
			$get_data = array ();
			$data ['total_tax_dis'] = array ();
			$data ['total_credit_sum'] = 0;
			foreach ( $invoice_total_query as $value ) {
				if ($value ['item_type'] == 'TAX') {
					$data ['total_tax_dis'] [$taxi] = $value ['debit'];
					$taxi ++;
				} else {
					if ($value ['generate_type'] == 1) {
						if ($i >= 5) {
							$data ['row_count'] = $i + 1;
						}
						$get_data ['invoice_from_date_' . $i] = $value ['created_date'];
						$get_data ['invoice_description_' . $i] = $value ['description'];
						$get_data ['invoice_amount_' . $i] = $value ['debit'];
						$i ++;
						$data ['total_credit_dis'] += $value ['debit'];
					}
					$data ['total_credit_sum'] += $value ['debit'];
				}
			}
			$data ['get_data'] = $get_data;
		}
		$account_data = $this->session->userdata ( "accountinfo" );
		$logintype = $this->session->userdata ( 'logintype' );
		$invoice_total = '';
		$query = "SELECT  * from invoice_details where invoiceid='$id' and generate_type=0 and item_type != 'TAX'  ORDER BY id ASC";
		$invoice_total_query = $this->db->query ( $query );
		$data ['auto_count'] = 0;
		if ($invoice_total_query->num_rows () > 0) {
			$data ['auto_count'] = $invoice_total_query->num_rows ();
			$invoice_total_query = $invoice_total_query->result_array ();
			
			$data ['invoice_total_query'] = $invoice_total_query;
		}
		$invoice = $this->db_model->getSelect ( "*", "invoices", array (
				"id" => $id 
		) );
		if ($invoice->num_rows () > 0) {
			$invoice = $invoice->result_array ();
			$result = $invoice [0];
			$data ['payment_due_date'] = $result ['due_date'];
		}
		$invoice_auto_res = $this->db_model->getSelect ( "sum(debit) as debit", "invoice_details", array (
				"invoiceid" => $id,
				'generate_type' => 0 
		) );
		$data ['invoice_auto_res'] = 0;
		if ($invoice_auto_res->num_rows () > 0) {
			$invoice_auto_res = $invoice_auto_res->result_array ();
			$result_auto_res = $invoice_auto_res [0];
			$data ['invoice_auto_res'] = $result_auto_res ['debit'];
		}
		
		$accountdata = $this->db_model->getSelect ( "*", "accounts", array (
				"id" => $result ['accountid'] 
		) );
		if ($accountdata->num_rows () > 0) {
			$accountdata = $accountdata->result_array ();
			$accountdata = $accountdata [0];
		}
		$data ['taxes_count'] = 0;
		$taxes = $this->db_model->getSelect ( "*", "taxes_to_accounts", array (
				"accountid" => $result ['accountid'] 
		) );
		$data ['taxes_count'] = $taxes->num_rows ();
		$total_tax = 0;
		if ($taxes->num_rows () > 0) {
			$taxes = $taxes->result_array ();
			foreach ( $taxes as $tax_value ) {
				$taxe_res = $this->db_model->getSelect ( "*", "taxes", array (
						"id" => $tax_value ['taxes_id'] 
				) );
				$taxe_res = $taxe_res->result_array ();
				foreach ( $taxe_res as $taxe_res_val ) {
					$data ['taxes_to_accounts'] [] = $taxe_res_val;
					$total_tax += $taxe_res_val ['taxes_rate'];
				}
			}
		}
		
		$system_config = common_model::$global_config ['system_config'];
		if ($system_config ["paypal_mode"] == 0) {
			$data ["paypal_url"] = $system_config ["paypal_url"];
			$data ["paypal_email_id"] = $system_config ["paypal_id"];
		} else {
			$data ["paypal_url"] = $system_config ["paypal_sandbox_url"];
			$data ["paypal_email_id"] = $system_config ["paypal_sandbox_id"];
		}
		$date = strtotime ( $result ['invoice_date'] );
		$data ['total_tax'] = $total_tax;
		$data ['time'] = date ( "Y-m-d h:i:s ", $date );
		$data ["paypal_tax"] = $system_config ["paypal_tax"];
		$data ["from_currency"] = $this->common->get_field_name ( 'currency', 'currency', $account_data ["currency_id"] );
		$data ["to_currency"] = Common_model::$global_config ['system_config'] ['base_currency'];
		if ($account_data ['type'] == - 1) {
			$accountdata ["currency_id"] = $this->common->get_field_name ( 'currency', 'currency', $account_data ["currency_id"] );
			$data ["to_currency"] = $accountdata ["currency_id"];
		} elseif ($account_data ['type'] == 1) {
			$accountdata ["currency_id"] = $this->common->get_field_name ( 'currency', 'currency', $account_data ["currency_id"] );
			$data ["to_currency"] = $accountdata ["currency_id"];
		} else {
			$accountdata ["currency_id"] = $this->common->get_field_name ( 'currency', 'currency', $account_data ["currency_id"] );
			$data ["to_currency"] = $accountdata ["currency_id"];
		}
		$data ['from_date'] = $result ['from_date'];
		$data ['to_date'] = $result ['to_date'];
		$data ['invoice_notes'] = $result ['notes'];
		$data ['invoice_date'] = $result ['invoice_date'];
		$data ['amount'] = $this->common_model->calculate_currency ( $result ['amount'], '', '', '', '' );
		$data ['invoice_prefix'] = $result ['invoice_prefix'];
		$data ['page_title'] = gettext ( 'Invoice Summary' );
		$data ['invoice_date'] = $result ['invoice_date'];
		$data ['return'] = base_url () . "invoices/invoice_list_modified";
		$data ['cancel_return'] = base_url () . "invoice/invoice_list_cancel";
		$data ['paypal_mode'] = 1;
		$data ['prefix_id'] = $result ['invoiceid'];
		$data ['logintype'] = $logintype;
		$data ['accountdata'] = $accountdata;
		$data ['id'] = $id;
		$data ['notify_url'] = base_url () . "invoices/invoice_list_get_data";
		if ($account_data ['type'] == '1') {
			$data ['response_url'] = base_url () . "invoices/invoice_list_responce/";
		} else {
			$data ['response_url'] = base_url () . "user/user_list_responce/";
		}
		$data ['sucess_url'] = base_url () . "invoices/invoice_list_sucess";
		
		$this->load->view ( 'view_invoice_edit_automatically', $data );
	}
	function invoice_manually_payment_edit_save() {
		$response_arr = $_POST;
		if (isset ( $response_arr ['save'] )) {
			$confirm = 0;
		} else {
			$confirm = 1;
		}
		$where = array (
				'invoiceid' => $response_arr ['invoiceid'],
				'generate_type' => 1 
		);
		$this->db->where ( $where );
		$this->db->delete ( "invoice_details" );
		$final_bal = 0;
		$final_tax_bal = 0;
		$account_balance = $this->common->get_field_name ( 'balance', 'accounts', $response_arr ['accountid'] );
		if ($response_arr ['taxes_count'] > 0) {
			for($a = 0; $a < $response_arr ['taxes_count']; $a ++) {
				$add_arr = array (
						'accountid' => $response_arr ['accountid'],
						'reseller_id' => $response_arr ['reseller_id'],
						'invoiceid' => $response_arr ['invoiceid'],
						'item_id' => 0,
						'generate_type' => 1,
						'item_type' => 'TAX',
						'description' => $response_arr ['description_total_tax_input_' . $a],
						'debit' => $this->common_model->add_calculate_currency ( $response_arr ['abc_total_tax_input_' . $a], "", "", true, false ),
						'created_date' => gmdate ( "Y-m-d H:i:s" ) 
				);
				$final_tax_bal += $this->common_model->add_calculate_currency ( $response_arr ['abc_total_tax_input_' . $a], "", "", true, false );
				$this->db->insert ( "invoice_details", $add_arr );
			}
		}
		for($i = 1; $i <= $response_arr ['row_count']; $i ++) {
			if ($response_arr ['invoice_amount_' . $i] != '') {
				$add_arr = array (
						'accountid' => $response_arr ['accountid'],
						'reseller_id' => $response_arr ['reseller_id'],
						'invoiceid' => $response_arr ['invoiceid'],
						'item_id' => 0,
						'generate_type' => 1,
						'item_type' => 'manual_inv',
						'description' => $response_arr ['invoice_description_' . $i],
						'debit' => $this->common_model->add_calculate_currency ( $response_arr ['invoice_amount_' . $i], "", "", true, false ),
						'created_date' => $response_arr ['invoice_from_date_' . $i] 
				);
				$this->db->insert ( "invoice_details", $add_arr );
			}
			$final_bal += $this->common_model->add_calculate_currency ( $response_arr ['invoice_amount_' . $i], "", "", true, false );
		}
		$query = "select  sum(debit) as credit from invoice_details where invoiceid = " . $response_arr ['invoiceid'];
		$invoice_total_query = $this->db->query ( $query );
		$invoice_total_query = $invoice_total_query->result_array ();
		$data = array (
				'amount' => $this->common_model->add_calculate_currency ( $response_arr ['total_val_final'], "", "", true, false ),
				'confirm' => $confirm,
				'notes' => $response_arr ['invoice_notes'] 
		);
		$this->db->where ( "id", $response_arr ['invoiceid'] );
		$this->db->update ( "invoices", $data );
		if ($confirm == 1) {
			$invoice_details = $this->db_model->getSelect ( "*", "invoice_details", array (
					"invoiceid" => $response_arr ["invoiceid"] 
			) );
			if ($invoice_details->num_rows () > 0) {
				$invoice_details_res = $invoice_details->result_array ();
				$after_bal = 0;
				foreach ( $invoice_details_res as $details_key => $details_value ) {
					$before_balance_add = $account_balance - $after_bal;
					$after_balance_add = $before_balance_add - $details_value ['debit'];
					$balnace_update = array (
							'before_balance' => $before_balance_add,
							'after_balance' => $after_balance_add 
					);
					$after_bal += $details_value ['debit'];
					$this->db->where ( "id", $details_value ['id'] );
					$this->db->update ( "invoice_details", $balnace_update );
				}
			}
			
			$account_data = $this->db_model->getSelect ( "*", "accounts", array (
					"id" => $response_arr ["accountid"] 
			) );
			$account_data = $account_data->result_array ();
			if ($account_data [0] ['posttoexternal'] == 1) {
				$bal_data = $account_data [0] ['balance'] - $this->common_model->add_calculate_currency ( $response_arr ['total_val_final'], "", "", true, false );
			} else {
				$bal_data = $account_data [0] ['balance'] - $this->common_model->add_calculate_currency ( $response_arr ['total_val_final'], "", "", true, false );
				if ($response_arr ['reseller_id'] == 0) {
					$payment = 1;
				} else {
					$payment = $response_arr ['reseller_id'];
				}
				$invoice_prefix = $this->common->get_field_name ( 'invoice_prefix', 'invoices', $response_arr ['invoiceid'] );
				$invoice_prefix_id = $this->common->get_field_name ( 'invoiceid', 'invoices', $response_arr ['invoiceid'] );
				$payment_arr = array (
						"accountid" => $response_arr ["accountid"],
						"payment_mode" => "1",
						"credit" => $this->common_model->add_calculate_currency ( $response_arr ['total_val_final'], "", "", true, false ),
						"type" => "invoice",
						"payment_by" => $payment,
						"notes" => "Payment made by " . $account_data [0] ['first_name'] . " " . $account_data [0] ['last_name'] . ", invoices No: " . $invoice_prefix . "_" . $invoice_prefix_id . " ",
						"paypalid" => '',
						"txn_id" => '',
						'payment_date' => gmdate ( 'Y-m-d H:i:s' ) 
				);
				$this->db->insert ( 'payments', $payment_arr );
				$account_balance = $this->common->get_field_name ( 'balance', 'accounts', $response_arr ['accountid'] );
				$add_arr = array (
						'accountid' => $response_arr ['accountid'],
						'reseller_id' => $response_arr ['reseller_id'],
						'invoiceid' => $response_arr ['invoiceid'],
						'item_id' => 0,
						'generate_type' => 1,
						'item_type' => 'INVPAY',
						'description' => 'Prepaid invoice generate',
						'credit' => $this->common_model->add_calculate_currency ( $response_arr ['total_val_final'], "", "", true, false ),
						'created_date' => gmdate ( "Y-m-d H:i:s" ),
						'before_balance' => $account_balance,
						'after_balance' => $account_balance - $this->common_model->add_calculate_currency ( $response_arr ['total_val_final'], "", "", true, false ) 
				);
				$this->db->insert ( "invoice_details", $add_arr );
			}
			$this->db->where ( "id", $response_arr ['accountid'] );
			$act_status = 0;
			if ($bal_data < 0 && $account_data [0] ['posttoexternal'] == 0) {
				$act_status = 1;
			}
			$balance_data = array (
					'balance' => $bal_data
					//,'status' => $act_status 
			);
			$this->db->update ( "accounts", $balance_data );
			/**
			 * * invoice mail **
			 */
			$this->invoice_send_notification ( $response_arr ['invoiceid'], $account_data [0], 'manually' );
		/**
		 * *** **
		 */
		}
		$this->session->set_flashdata ( 'astpp_errormsg', 'Invoice updated successfully!' );
		redirect ( base_url () . 'invoices/invoice_list/' );
	}
	function invoice_automatically_payment_edit_save() {
		$response_arr = $_POST;
		if (isset ( $response_arr ['save'] )) {
			$confirm = 0;
		} else {
			$confirm = 1;
		}
		foreach ( $response_arr ['auto_invoice_date'] as $key => $val ) {
			$data = array (
					'debit' => $this->common_model->add_calculate_currency ( $response_arr ['auto_invoice_amount'] [$key], "", "", true, false ),
					'created_date' => $response_arr ['auto_invoice_date'] [$key],
					'description' => $response_arr ['auto_invoice_description'] [$key],
					'generate_type' => 0 
			);
			$this->db->where ( "id", $key );
			$this->db->update ( "invoice_details", $data );
		}
		$where = array (
				'invoiceid' => $response_arr ['invoiceid'],
				'generate_type' => 1 
		);
		$this->db->where ( $where );
		$this->db->delete ( "invoice_details" );
		$final_bal = 0;
		$final_tax_bal = 0;
		$account_balance = $this->common->get_field_name ( 'balance', 'accounts', $response_arr ['accountid'] );
		if ($response_arr ['taxes_count'] > 0) {
			for($a = 0; $a < $response_arr ['row_count']; $a ++) {
				
				$update_arr = array (
						'debit' => $this->common_model->add_calculate_currency ( $response_arr ['abc_total_tax_input_' . $a], "", "", true, false ) 
				);
				$final_tax_bal += $this->common_model->add_calculate_currency ( $response_arr ['abc_total_tax_input_' . $a], "", "", true, false );
				$arr_update = array (
				    'item_type' => 'TAX',
				    'id' => $response_arr ['description_total_tax_id_' . $a]
				);
				$this->db->where ( $arr_update );
				$this->db->update ( "invoice_details", $update_arr );
			}
		}
		for($i = 1; $i <= $response_arr ['row_count']; $i ++) {
			if ($response_arr ['invoice_amount_' . $i] != '') {
				$add_arr = array (
						'accountid' => $response_arr ['accountid'],
						'reseller_id' => $response_arr ['reseller_id'],
						'invoiceid' => $response_arr ['invoiceid'],
						'item_id' => 0,
						'generate_type' => 1,
						'item_type' => 'manual_inv',
						'description' => $response_arr ['invoice_description_' . $i],
						'debit' => $this->common_model->add_calculate_currency ( $response_arr ['invoice_amount_' . $i], "", "", true, false ),
						'created_date' => $response_arr ['invoice_from_date_' . $i] 
				);
				$this->db->insert ( "invoice_details", $add_arr );
			}
		}
		$query = "select  sum(debit) as credit from invoice_details where invoiceid = " . $response_arr ['invoiceid'];
		$invoice_total_query = $this->db->query ( $query );
		$invoice_total_query = $invoice_total_query->result_array ();
		$data = array (
				'amount' => $this->common_model->add_calculate_currency ( $response_arr ['total_val_final'], "", "", true, false ),
				'confirm' => $confirm,
				'notes' => $response_arr ['invoice_notes'] 
		);
		$this->db->where ( "id", $response_arr ['invoiceid'] );
		$this->db->update ( "invoices", $data );
		if ($confirm == 1) {
			$invoice_details = $this->db_model->getSelect ( "*", "invoice_details", array (
					"invoiceid" => $response_arr ["invoiceid"] 
			) );
			if ($invoice_details->num_rows () > 0) {
				$invoice_details_res = $invoice_details->result_array ();
				$after_bal = 0;
				foreach ( $invoice_details_res as $details_key => $details_value ) {
					if ($details_value ['item_type'] != 'STANDARD') {
						$before_balance_add = $account_balance - $after_bal;
						$after_balance_add = $before_balance_add - $details_value ['debit'];
						$balnace_update = array (
								'before_balance' => $before_balance_add,
								'after_balance' => $after_balance_add 
						);
						$after_bal += $details_value ['debit'];
						$this->db->where ( "id", $details_value ['id'] );
						$this->db->update ( "invoice_details", $balnace_update );
					}
				}
			}
			$account_data = $this->db_model->getSelect ( "*", "accounts", array (
					"id" => $response_arr ["accountid"] 
			) );
			$account_data = $account_data->result_array ();
			$invoice_not_deduct = $this->db_model->getSelect ( "*", "invoice_details", array (
					"invoiceid" => $response_arr ['invoiceid'] 
			) );
			$standard_call_balance = 0;
			$invoice_not_deduct = $invoice_not_deduct->result_array ();
			foreach ( $invoice_not_deduct as $key => $invoice_nodeduct_val ) {
				if ($invoice_nodeduct_val ['item_type'] == 'STANDARD') {
					$standard_call_balance = $invoice_nodeduct_val ['debit'];
				}
			}
			if ($account_data [0] ['posttoexternal'] == 1) {
				$finaldeduct_bal = $response_arr ['total_val_final'] - $standard_call_balance;
				$bal_data = $account_data [0] ['balance'] - $finaldeduct_bal;
			}
			$this->db->where ( "id", $response_arr ['accountid'] );
			$balance_data = array (
					'balance' => $bal_data 
			);
			$this->db->update ( "accounts", $balance_data );
			/**
			 * * invoice mail **
			 */
			$this->invoice_send_notification ( $response_arr ['invoiceid'], $account_data [0], 'auto' );
		/**
		 * *** **
		 */
		}
		$this->session->set_flashdata ( 'astpp_errormsg', 'Invoice updated successfully!' );
		redirect ( base_url () . 'invoices/invoice_list/' );
	}
	
	/**
	 *
	 * @param string $inv_flag        	
	 */
	function invoice_send_notification($invoice_id, $accountdata, $inv_flag) {
		$invoicedata = $this->db_model->getSelect ( "*", "invoices", array (
				"id" => $invoice_id 
		) );
		$invoicedata = $invoicedata->result_array ();
		$invoicedata = $invoicedata [0];
		// $res = $this->common->get_invoice_template($invoicedata,$accountdata,"False");
		$invoice_conf = array ();
		if ($accountdata ['reseller_id'] == 0) {
			$where = array (
					"accountid" => 1 
			);
		} else {
			$where = array (
					"accountid" => $accountdata ['reseller_id'] 
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
		$template_config = $this->config->item ( 'invoice_screen' );
		include ($template_config . 'generateInvoice.php');
		$generateInvoice = new generateInvoice ();
		$generateInvoice->download_invoice ( $invoicedata ['id'], $accountdata, $invoice_conf, $inv_flag );
		return true;
	}
	function currency_decimal($amount) {
		$decimal_amount = Common_model::$global_config ['system_config'] ['decimalpoints'];
		$number_convert = number_format ( ( float ) $amount, $decimal_amount, '.', '' );
		return $number_convert;
	}
	function invoice_summary($id) {
		$query = "SELECT  * from invoice_details where  invoiceid='$id' ";
		
		$invoice_total_query = $this->db->query ( $query );
		if ($invoice_total_query->num_rows () > 0) {
			$invoice_final_query = $invoice_total_query->result_array ();
			$data ['invoice_final_query'] = $invoice_final_query;
			
			$account_data = $this->session->userdata ( "accountinfo" );
			$logintype = $this->session->userdata ( 'logintype' );
			$invoice_total = '';
			$query = "SELECT  * from invoice_details where item_type='INVPAY' AND invoiceid='$id' ORDER BY created_date ASC";
			$invoice_total_query = $this->db->query ( $query );
			if ($invoice_total_query->num_rows () > 0) {
				$invoice_total_query = $invoice_total_query->result_array ();
				
				$data ['invoice_total_query'] = $invoice_total_query;
			}
			$invoice = $this->db_model->getSelect ( "*", "invoices", array (
					"id" => $id 
			) );
			if ($invoice->num_rows () > 0) {
				$invoice = $invoice->result_array ();
				$result = $invoice [0];
				$data ['payment_due_date'] = $result ['due_date'];
			}
			
			$accountdata = $this->db_model->getSelect ( "*", "accounts", array (
					"id" => $result ['accountid'] 
			) );
			if ($accountdata->num_rows () > 0) {
				$accountdata = $accountdata->result_array ();
				$accountdata = $accountdata [0];
			}
			
			$system_config = common_model::$global_config ['system_config'];
			if ($system_config ["paypal_mode"] == 0) {
				$data ["paypal_url"] = $system_config ["paypal_url"];
				$data ["paypal_email_id"] = $system_config ["paypal_id"];
			} else {
				$data ["paypal_url"] = $system_config ["paypal_sandbox_url"];
				$data ["paypal_email_id"] = $system_config ["paypal_sandbox_id"];
			}
			$date = strtotime ( $result ['invoice_date'] );
			$data ['time'] = date ( "Y-m-d h:i:s ", $date );
			$data ["paypal_tax"] = $system_config ["paypal_tax"];
			$data ["from_currency"] = $this->common->get_field_name ( 'currency', 'currency', $account_data ["currency_id"] );
			$data ["to_currency"] = Common_model::$global_config ['system_config'] ['base_currency'];
			if ($account_data ['type'] == - 1) {
				$accountdata ["currency_id"] = $this->common->get_field_name ( 'currency', 'currency', $account_data ["currency_id"] );
				$data ["to_currency"] = $accountdata ["currency_id"];
			} elseif ($account_data ['type'] == 1) {
				$accountdata ["currency_id"] = $this->common->get_field_name ( 'currency', 'currency', $account_data ["currency_id"] );
				$data ["to_currency"] = $accountdata ["currency_id"];
			} else {
				$accountdata ["currency_id"] = $this->common->get_field_name ( 'currency', 'currency', $account_data ["currency_id"] );
				$data ["to_currency"] = $accountdata ["currency_id"];
			}
			$data ["system_currency"] = Common_model::$global_config ['system_config'] ['base_currency'];
			foreach ( $invoice_total_query as $value ) {
				$data ['from_date'] = $result ['from_date'];
				$data ['to_date'] = $result ['to_date'];
				$data ['invoice_date'] = $result ['invoice_date'];
				$data ['amount'] = $this->common_model->calculate_currency ( $result ['amount'] );
				$data ['invoice_prefix'] = $result ['invoice_prefix'];
				$data ['page_title'] = gettext ( 'Invoice Summary' );
				$data ['invoice_date'] = $result ['invoice_date'];
				$data ['return'] = base_url () . "invoices/invoice_list_modified";
				$data ['cancel_return'] = base_url () . "invoice/invoice_list_cancel";
				$data ['paypal_mode'] = 1;
				$data ['prefix_id'] = $result ['invoiceid'];
				$data ['logintype'] = $logintype;
				$data ['accountdata'] = $accountdata;
				$data ['value'] = $value;
				$data ['id'] = $id;
				$data ['notify_url'] = base_url () . "invoices/invoice_list_get_data/";
				if ($account_data ['type'] == '1') {
					$data ['response_url'] = base_url () . "invoices/invoice_list_responce/";
				} else {
					$data ['response_url'] = base_url () . "user/user_list_responce/";
				}
				$data ['sucess_url'] = base_url () . "invoices/invoice_list_sucess/";
			}
		} else {
			
			redirect ( base_url () . 'dashboard/' );
		}
		$this->load->view ( 'view_invoice_payment', $data );
	}
	function invoice_list_get_data() {
		redirect ( base_url () . 'invoices/invoice_list/' );
	}
	function convert_amount($amount) {
		$amount = $this->common_model->add_calculate_currency ( $amount, "", "", true, false );
		echo number_format ( $amount, 2 );
	}
	function invoice_list_responce() {
		$response_arr = $_POST;

		/*$fp = fopen ( "/tmp/astpp_payment.log", "a+" );
		$date = date ( "Y-m-d H:i:s" );
		fwrite ( $fp, "====================" . $date . "===============================\n" );
		foreach ( $response_arr as $key => $value ) {
			fwrite ( $fp, $key . ":::>" . $value . "\n" );
		}*/

		$logintype = $this->session->userdata ( 'logintype' );
		if (($response_arr ["payment_status"] == "Pending" || $response_arr ["payment_status"] == "Complete" || $response_arr ["payment_status"] == "Completed")) {
			$invoice_id = $response_arr ['item_number'];
			$amount = $response_arr ['payment_gross'];
			$description = $response_arr ['item_name'];
			$debit = '';
			
			$paypal_tax = $this->db_model->getSelect ( "value", "system", array (
					"name" => "paypal_tax",
					"group_title" => "paypal" 
			) );
			$paypal_tax = $paypal_tax->result ();
			$paypal_tax = $paypal_tax [0]->value;
			$balance_amt = $actual_amount = $response_arr ["custom"];
			
			$paypal_fee = $this->db_model->getSelect ( "value", "system", array (
					"name" => "paypal_fee",
					"group_title" => "paypal" 
			) );
			$paypal_fee = $paypal_fee->result ();
			$paypal_fee = $paypal_fee [0]->value;
			
			$paypalfee = ($paypal_fee == 0) ? '0' : $response_arr ["mc_gross"];
			
			$account_data = $this->db_model->getSelect ( "*", "accounts", array (
					"id" => $response_arr ["custom"] 
			) );
			$account_data = $account_data->result_array ();
			$account_data = $account_data [0];
			$currency = $this->db_model->getSelect ( 'currency,currencyrate', 'currency', array (
					"id" => $account_data ["currency_id"] 
			) );
			$currency = $currency->result_array ();
			$currency = $currency [0];
			$date = date ( 'Y-m-d H:i:s' );
			$invoice_total_query = $this->db_model->getSelect ( "*", "invoices", array (
					"id" => $invoice_id 
			) );
			if ($invoice_total_query->num_rows () > 0) {
				$invoice_total_query = $invoice_total_query->result_array ();
				$debit = $invoice_total_query [0] ['amount'];
			}
			$query = "select  sum(credit) as credit from invoice_details where invoiceid = " . $invoice_id . " AND item_type !='PAYMENT' Group By invoiceid";
			// echo $query; exit;
			$invoice_total_query = $this->db->query ( $query );
			if ($invoice_total_query->num_rows () > 0) {
				$invoice_total_query = $invoice_total_query->result_array ();
				$total_debit = $invoice_total_query [0] ['credit'];
			}
			$credit_total = $total_debit + $amount;
			// echo $debit; exit;
			if ($debit >= $credit_total) {
				
				if ($debit == $credit_total) {
					
					$this->db->where ( "id", $invoice_id );
					$data = array (
							'status' => '0' 
					);
					$this->db->update ( "invoices", $data );
					// echo $this->db->last_query(); exit;
				} else {
					$this->db->where ( "id", $invoice_id );
					$data = array (
							'status' => '2' 
					);
					$this->db->update ( "invoices", $data );
					// echo $this->db->last_query(); exit;
				}
				
				if ($amount > $debit) {
					$this->session->set_flashdata ( 'astpp_notification', 'Invoice payment amount should be higher then the invoice amount.' );
					redirect ( base_url () . 'invoices/invoice_summary/' . $invoice_id );
				}
				$payment_trans_array = array (
						"accountid" => $response_arr ["custom"],
						"amount" => $response_arr ["payment_gross"],
						"tax" => "1",
						"payment_method" => "Paypal",
						"actual_amount" => $amount,
						"paypal_fee" => $paypalfee,
						"user_currency" => $currency ["currency"],
						"currency_rate" => $currency ["currencyrate"],
						"transaction_details" => json_encode ( $response_arr ),
						"date" => $date 
				);
				$this->db->insert ( 'payment_transaction', $payment_trans_array );
				$transaction_details_id = $this->db->insert_id ();
				
				if ($transaction_details_id == '') {
					$transaction_details_id == 0;
				}
				
				$accountdata = $this->db_model->getSelect ( "*", "payment_transaction", array () );
				if ($accountdata->num_rows () > 0) {
					foreach ( $accountdata->result_array () as $payment_value ) {
						$payment_transaction = $payment_value ['transaction_details'];
						$payment_result = json_decode ( $payment_transaction, true );
						$transaction_id = $payment_result ['txn_id'];
					}
				}
				$invoice_total_history = $this->db_model->getSelect ( "*", "invoice_details", array (
						"invoiceid" => $invoice_id,
						'item_type' => 'INVPAY' 
				) );
				$account_balance = $account_data ['balance'];
				if ($invoice_total_history->num_rows () > 0) {
					// echo 'if'; exit;
					$invoice_main = $invoice_total_history->result_array ();
					// echo '<pre>'; print_r($invoice_main); exit;
					$debit_amount = "0.00";
					foreach ( $invoice_main as $value ) {
						$debit_amount = $value ['debit'];
					}
					
					$actual_amount = $debit_amount - $amount;
					// echo $actual_amount; exit;
					$tax_array = array (
							"accountid" => $response_arr ["custom"],
							"invoiceid" => $invoice_id,
							"item_id" => $transaction_details_id,
							"description" => $description,
							"debit" => $this->common->currency_decimal ( 0 ),
							"credit" => $this->common->currency_decimal ( $amount ),
							"item_type" => "INVPAY",
							"created_date" => $date,
							"reseller_id" => $account_data ['reseller_id'],
							'before_balance' => $account_balance,
							'after_balance' => $account_balance + $amount 
					); // echo '<pre>'; print_r( $tax_array); exit;
					$this->db->insert ( "invoice_details", $tax_array );
				} else {
					
					// echo 'else'; exit;
					$actual_amount = $debit - $amount;
					$tax_array = array (
							"accountid" => $response_arr ["custom"],
							"invoiceid" => $invoice_id,
							"item_id" => $transaction_details_id,
							"description" => $description,
							"debit" => $this->common->currency_decimal ( 0 ),
							"credit" => $this->common->currency_decimal ( $amount ),
							"item_type" => "INVPAY",
							"created_date" => $date,
							"reseller_id" => $account_data ['reseller_id'],
							'before_balance' => $account_balance,
							'after_balance' => $account_balance + $amount 
					);
					
					$this->db->insert ( "invoice_details", $tax_array );
				}
			} else {
				if ($logintype = 0) {
					$this->session->set_flashdata ( 'astpp_notification', 'Invoice payment amount should be higher then the invoice amount.' );
					redirect ( base_url () . 'user/user_invoice_payment/' . $invoice_id );
				} else {
					$this->session->set_flashdata ( 'astpp_notification', 'Invoice payment amount should be higher then the invoice amount.' );
					redirect ( base_url () . 'invoices/invoice_summary/' . $invoice_id );
				}
			}
			
			$this->load->module ( 'accounts/accounts' );
			$reseller_debit = 0;
			$reseller_debit = $this->common->get_field_name ( 'reseller_id', 'accounts', array (
					'id' => $response_arr ["custom"],
					'status' => 0,
					'deleted' => 0 
			) );
			// echo '<pre>'; print_r($reseller_debit); exit;
			if ($reseller_debit > 0) {
				$this->accounts_model->update_balance ( $amount*-1, $response_arr ["custom"], "debit" );
				$this->accounts_model->update_balance ( $amount*-1, $reseller_debit, "debit" );
			} else {
				$this->accounts_model->update_balance ( $amount*-1, $response_arr ["custom"], "debit" );
			}
			$account_data ['accountid'] = $account_data ['id'];
			//$this->email_lib->send_email ( 'email_payment_notification', $account_data, '', '', 0, 0, 0 );
			$this->session->set_flashdata ( 'astpp_errormsg', 'Invoice payment done successfully!' );
			if ($logintype = '1') {				
				redirect ( base_url () . 'invoices/invoice_list/' );
			} else {				
				redirect ( base_url () . 'user/user_invoices_list/' );
			}
		}
	}
	function invoice_admin_payment() {
		$response_arr = $_POST;
		
		if (! empty ( $response_arr )) {
			
			$amount = $response_arr ['amount'];
			$description = $response_arr ['item_name'];
			$invoice_id = $response_arr ['item_number'];
			$date = date ( 'Y-m-d H:i:s' );
			
			$invoice_total_query = $this->db_model->getSelect ( "*", "invoices", array (
					"id" => $invoice_id 
			) );
			if ($invoice_total_query->num_rows () > 0) {
				$invoice_total_query = $invoice_total_query->result_array ();
				$debit = $invoice_total_query [0] ['amount'];
			}
			
			$query = "select  sum(credit) as credit from invoice_details where invoiceid = " . $invoice_id . "  AND item_type !='PAYMENT'  Group By invoiceid";
			// echo $query; exit;
			$invoice_total_query = $this->db->query ( $query );
			if ($invoice_total_query->num_rows () > 0) {
				$invoice_total_query = $invoice_total_query->result_array ();
				$total_debit = $invoice_total_query [0] ['credit'];
			}
			$credit_total = $total_debit + $amount;
			// echo $credit_total; exit;
			if ($debit >= $credit_total) {
				
				if ($debit == $credit_total) {
					
					$this->db->where ( "id", $invoice_id );
					$data = array (
							'status' => '0' 
					);
					$this->db->update ( "invoices", $data );
					// echo $this->db->last_query(); exit;
				} else {
					$this->db->where ( "id", $invoice_id );
					$data = array (
							'status' => '2' 
					);
					$this->db->update ( "invoices", $data );
					// echo $this->db->last_query(); exit;
				}
				
				if ($amount > $debit) {
					$this->session->set_flashdata ( 'astpp_notification', 'Invoice payment amount should be higher then the invoice amount.' );
					redirect ( base_url () . 'invoices/invoice_summary/' . $invoice_id );
				}
				
				$debit_amount = "0.00";
				$account_balance = $this->common->get_field_name ( 'balance', 'accounts', $response_arr ['custom'] );
				$tax_array = array (
						"accountid" => $response_arr ["custom"],
						"invoiceid" => $invoice_id,
						"item_id" => '',
						"description" => $description,
						"debit" => "0.00",
						"credit" => $amount,
						"item_type" => "INVPAY",
						"created_date" => $date,
						"reseller_id" => "0",
						'before_balance' => $account_balance,
						'after_balance' => $account_balance + $amount 
				);
				$this->db->insert ( "invoice_details", $tax_array );
			} else {
				
				$this->session->set_flashdata ( 'astpp_notification', 'Invoice payment amount should be higher then the invoice amount.' );
				redirect ( base_url () . 'invoices/invoice_summary/' . $invoice_id );
			}
			
			$this->load->module ( 'accounts/accounts' );
			$this->accounts_model->update_balance ( $amount, $response_arr ["custom"], "debit" );
			$this->session->set_flashdata ( 'astpp_errormsg', 'Invoice payment done successfully!' );
		}
		$account_data = $this->db_model->getSelect ( "*", "accounts", array (
				"id" => $response_arr ["custom"] 
		) );
		$account_data = $account_data->result_array ();
		$account_data = $account_data [0];
		$account_data ['accountid'] = $account_data ['id'];
		// $this->email_lib->send_email('email_payment_notification',$account_data ,'','',0,0,0);
		redirect ( base_url () . 'invoices/invoice_list/' );
	}
	function invoice_list_sucess() {
		echo 'sucess';
		exit ();
	}
	function invoice_list_modified() {
		echo 'sucess';
		exit ();
	}
	function invoice_delete() {
		$ids = $this->input->post ( "selected_ids", true );
		$where = "id IN ($ids)";
		$this->db->where ( $where );
		echo $this->db->delete ( "invoices" );
	}
	function invoice_conf() {
		$data ['page_title'] = gettext ( 'Company Profile' );
		$post_array = $this->input->post ();
		$accountinfo = $this->session->userdata ( 'accountinfo' );
		unset ( $post_array ['action'] );
		unset ( $post_array ['button'] );
		unset ( $post_array ['file'] );
		
		if (! empty ( $post_array )) {
			$invoice_prefix = trim ( $post_array ['invoice_prefix'] );
			if ($_FILES ['file'] ['name'] == '') {
				$invoiceconf = $this->invoices_model->get_invoiceconf ( $post_array ['accountid'] );
				$file_name = ($invoiceconf ['logo'] != '') ? $invoiceconf ['logo'] : '';
			}
			if ($_FILES ['file_fav'] ['name'] == '') {
				$invoiceconf = $this->invoices_model->get_invoiceconf ( $post_array ['accountid'] );
				$file_name_fav = ($invoiceconf ['favicon'] != '') ? $invoiceconf ['favicon'] : '';
			}
			if ($invoice_prefix == '') {
				$this->session->set_flashdata ( 'astpp_notification', 'Invoice Prefix is required.' );
				redirect ( base_url () . 'invoices/invoice_conf/' );
			}
			
			if (isset ( $_FILES ['file'] ['name'] ) && $_FILES ['file'] ['name'] != '') {
				$files = $_FILES ['file'];
				if ($files ['size'] < 0) {
					$this->session->set_flashdata ( 'astpp_notification', 'PLease upload maximum file' );
					redirect ( base_url () . 'invoices/invoice_conf/' );
				}
				$file = $_FILES ['file'];
				$uploadedFile = $file ["tmp_name"];
				$file_name = $file ['name'];
				$file_type = $file ['type'];
				if ($file_type == 'image/jpg' || $file_type == 'image/png' || $file_type == 'image/jpeg') {
					$dir_path = FCPATH . "upload/";
					$path = $dir_path . $accountinfo ['id'] . "_" . $file ['name'];
					if (move_uploaded_file ( $uploadedFile, $path )) {
						$this->session->set_flashdata ( 'astpp_errormsg', gettext ( 'files added successfully!' ) );
					} else {
						$this->session->set_flashdata ( 'astpp_notification', "File Uploading Fail Please Try Again" );
						redirect ( base_url () . 'invoices/invoice_conf/' );
					}
				} else {
					$this->session->set_flashdata ( 'astpp_notification', 'Please upload only image!' );
					redirect ( base_url () . 'invoices/invoice_conf/' );
				}
			}
			if (isset ( $_FILES ['file_fav'] ['name'] ) && $_FILES ['file_fav'] ['name'] != '') {
				$files = $_FILES ['file_fav'];
				if ($files ['size'] < 0) {
					$this->session->set_flashdata ( 'astpp_notification', 'PLease upload maximum file' );
					redirect ( base_url () . 'invoices/invoice_conf/' );
				}
				$imageInformation = getimagesize ( $_FILES ['file_fav'] ['tmp_name'] );
				
				$imageWidth = $imageInformation [0]; // Contains the Width of the Image
				
				$imageHeight = $imageInformation [1]; // Contains the Height of the Image
				
				if ($imageWidth > '16' && $imageHeight > '16') {
					$this->session->set_flashdata ( 'astpp_notification', 'Please upload 16 * 16 size file' );
					redirect ( base_url () . 'invoices/invoice_conf/' );
				}
				$file = $_FILES ['file_fav'];
				$uploadedFile = $file ["tmp_name"];
				$file_name_fav = $file ['name'];
				$file_type = $file ['type'];
				
				if ($file_type == 'image/jpg' || $file_type == 'image/png' || $file_type == 'image/jpeg' || $file_type == 'image/vnd.microsoft.icon') {
					$dir_path = FCPATH . "upload/";
					$path = $dir_path . $accountinfo ['id'] . "_" . $file ['name'];
					if (move_uploaded_file ( $uploadedFile, $path )) {
						$this->session->set_flashdata ( 'astpp_errormsg', gettext ( 'files added successfully!' ) );
					} else {
						$this->session->set_flashdata ( 'astpp_notification', "File Uploading Fail Please Try Again" );
						redirect ( base_url () . 'invoices/invoice_conf/' );
					}
				} else {
					$this->session->set_flashdata ( 'astpp_notification', 'Please upload only image!' );
					redirect ( base_url () . 'invoices/invoice_conf/' );
				}
			}
			$post_array ['logo'] = $file_name;
			$post_array ['favicon'] = $file_name_fav;
			$this->invoices_model->save_invoiceconf ( $post_array );
			$this->session->set_flashdata ( 'astpp_errormsg', 'Invoice configuration updated sucessfully!' );
			redirect ( base_url () . 'invoices/invoice_conf/' );
		} else {
			
			$invoiceconf = $this->invoices_model->get_invoiceconf ( $accountinfo ['id'] );
			
			if (! empty ( $invoiceconf )) {
				$data ['file_name'] = $accountinfo ['id'] . "_" . $invoiceconf ['logo'];
				$invoiceconf ['file'] = $accountinfo ['id'] . "_" . $invoiceconf ['logo'];
				$invoiceconf ['file_fav'] = $accountinfo ['id'] . "_" . $invoiceconf ['favicon'];
				$data ['file_name_fav'] = $accountinfo ['id'] . "_" . $invoiceconf ['favicon'];
			}
			
			$data ['form'] = $this->form->build_form ( $this->invoices_form->get_invoiceconf_form_fields ( $invoiceconf ), $invoiceconf );
			
			$this->load->view ( 'view_invoiceconf', $data );
		}
	}
	function incr($inteval) {
		$inteval ++; // $a is undefined
		return $inteval;
	}
	
	// /*ASTPP_invoice_changes_05_05_start*/
	function customer_invoices($accountid) {
		// echo '<pre>'; print_r($accountid); exit;
		$json_data = array ();
		/**
		 * **
		 * Invoice manually
		 * *
		 */
		$where = array (
				'accountid' => $accountid,
				'confirm' => 1 
		);
		$count_all = $this->db_model->countQuery ( "*", "invoices", $where );
		$currency_id = Common_model::$global_config ['system_config'] ['base_currency'];
		$paging_data = $this->form->load_grid_config ( $count_all, $_GET ['rp'], $_GET ['page'] );
		$json_data = $paging_data ["json_paging"];
		
		$Invoice_grid_data = $this->db_model->select ( "*", "invoices", $where, "invoice_date", "desc", $paging_data ["paging"] ["page_no"], $paging_data ["paging"] ["start"] );
		// echo $this->db->last_query(); exit;
		$grid_fields = json_decode ( $this->invoices_form->build_invoices_list_for_admin () );
		
		// $json_data['rows'] = $this->form->build_grid($Invoice_grid_data,$grid_fields);
		$logintype = $this->session->userdata ( 'logintype' );
		$url = ($logintype == 0 || $logintype == 3) ? "/user/user_invoice_download/" : '/invoices/invoice_download/';
		if ($Invoice_grid_data->num_rows () > 0) {
			$query = $Invoice_grid_data->result_array ();
			$total_value = 0;
			$ountstanding_value = 0;
			
			foreach ( $query as $key => $value ) {
				$date = strtotime ( $value ['invoice_date'] );
				$invoice_date = date ( "Y-m-d", $date );
				$fromdate = strtotime ( $value ['from_date'] );
				$from_date = date ( "Y-m-d", $fromdate );
				$duedate = strtotime ( $value ['due_date'] );
				$due_date = date ( "Y-m-d", $duedate );
				$outstanding = $value ['amount'];
				$last_payment_date = '';
				$invoice_total_query = $this->db_model->select ( "sum(debit) as debit,sum(credit) as credit,created_date", "invoice_details", array (
						"invoiceid" => $value ['id'],
						"item_type" => "INVPAY" 
				), "created_date", "DESC", "1", "0" );
				// echo $this->db->last_query(); exit;
				if ($invoice_total_query->num_rows () > 0) {
					$invoice_total_query = $invoice_total_query->result_array ();
					// echo '<pre>'; print_r($invoice_total_query);
					$outstanding -= $invoice_total_query [0] ['credit'];
					
					$last_payment_date = $invoice_total_query [0] ['created_date'];
					if ($last_payment_date) {
						$payment_date = strtotime ( $last_payment_date );
						$payment_last = date ( "d/m/Y", $payment_date );
					} else {
						$payment_last = '';
					}
				}
				$invoice_total = '';
				$accountinfo = $this->session->userdata ( 'accountinfo' );
				$id = $accountinfo ['id'];
				$query = "select sum(amount) as grand_total from invoices where confirm=1 and accountid=$accountid";
				
				$ext_query = $this->db->query ( $query );
				if ($ext_query->num_rows () > 0) {
					$result_total = $ext_query->result_array ();
					$grandtotal = $result_total [0] ['grand_total'];
					$grand_total = $this->common->currency_decimal ( $grandtotal ) . ' ' . $currency_id;
				}
				
				$invoice_query = "select sum(credit) as grand_credit from invoice_details where accountid=$accountid";
				$credit_query = $this->db->query ( $invoice_query );
				if ($credit_query->num_rows () > 0) {
					$credit_total = $credit_query->result_array ();
					$grand_credit_total = $credit_total [0] ['grand_credit'];
					$grandcredit = $grand_total - $grand_credit_total;
					$grand_credit = $this->common->currency_decimal ( $grandcredit ) . ' ' . $currency_id;
				}
				
				$download = "<a href=" . $url . $value ['id'] . " class='btn btn-royelblue btn-sm'  title='Download Invoice' ><i class='fa fa-cloud-download fa-fw'></i></a>&nbsp";
				if ($value ['type'] == 'R') {
					$payment = '';
					$payment_last = $invoice_date;
					$outstanding = 0;
				} else {
					if ($outstanding > 0) {
						$payment = '<a style="padding: 0 8px;" href="' . base_url () . 'invoices/invoice_summary/' . $value ['id'] . '" class="btn btn-warning"  title="Payment">Unpaid</i></a>';
					} else {
						$payment = ' <button style="padding: 0 8px;" type="button"  class="btn btn-success">Paid</button>';
					}
				}
				
				$account_arr = $this->db_model->getSelect ( 'first_name,number,last_name', 'accounts', array (
						'id' => $value ['accountid'] 
				) );
				$account_array = $account_arr->result_array ();
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
								// $value['invoice_prefix'].$value['invoiceid'].' ('.$value['type'].')',
								$invoice_type,
								// $account_array[0]['first_name'].' '.$account_array[0]['last_name'].'</br>'.$account_array[0]['number'],
								$invoice_date,
								$from_date,
								$due_date,
								$payment_last,
								$this->common_model->to_calculate_currency ( $value ['amount'], '', '', true, false ),
								$this->common_model->to_calculate_currency ( $outstanding, '', '', true, false ),
								$download . '' . $payment 
						) 
				);
				$total_value = $total_value + $value ['amount'];
				$ountstanding_value = $ountstanding_value + $outstanding;
			}
		}
		
		echo json_encode ( $json_data );
	}
	// end
	function user_invoices($accountid) {
		$json_data = array ();
		$count_all = $this->invoices_model->get_user_invoice_list ( false );
		$paging_data = $this->form->load_grid_config ( $count_all, $_GET ['rp'], $_GET ['page'] );
		$json_data = $paging_data ["json_paging"];
		$currency_id = Common_model::$global_config ['system_config'] ['base_currency'];
		$query = $this->invoices_model->get_user_invoice_list ( true, $paging_data ["paging"] ["start"], $paging_data ["paging"] ["page_no"] );
		$grid_fields = json_decode ( $this->invoices_form->build_invoices_list_for_customer () );
		$query = $query->result_array ();
		$account_arr = '';
		$created_date = '';
		foreach ( $query as $key => $value ) {
			$date = strtotime ( $value ['invoice_date'] );
			$invoice_date = date ( "d/m/Y", $date );
			$fromdate = strtotime ( $value ['from_date'] );
			$from_date = date ( "d/m/Y", $fromdate );
			$duedate = strtotime ( $value ['due_date'] );
			$due_date = date ( "d/m/Y", $duedate );
			$outstanding = $value ['amount'];
			$invoice_total_query = $this->db_model->select ( "sum(debit) as debit,sum(credit) as credit,created_date", "invoice_details", array (
					"invoiceid" => $value ['id'],
					"item_type" => "INVPAY" 
			), "created_date", "DESC", "1", "0" );
			// echo $this->db->last_query(); exit;
			if ($invoice_total_query->num_rows () > 0) {
				$invoice_total_query = $invoice_total_query->result_array ();
				// echo '<pre>'; print_r($invoice_total_query);
				$outstanding -= $invoice_total_query [0] ['credit'];
				
				$last_payment_date = $invoice_total_query [0] ['created_date'];
				if ($last_payment_date) {
					$payment_date = strtotime ( $last_payment_date );
					$payment_last = date ( "d/m/Y", $payment_date );
				} else {
					$payment_last = '';
				}
			}
			$invoice_total_query = $this->db_model->select ( "debit,created_date", "invoice_details", array (
					"invoiceid" => $value ['id'],
					"item_type" => "INVPAY" 
			), "created_date", "DESC", "1", "0" );
			if ($invoice_total_query->num_rows () > 0) {
				$invoice_total_query = $invoice_total_query->result_array ();
				// $outstanding = $invoice_total_query[0]['debit'];
				$created_date = $invoice_total_query [0] ['created_date'];
			}
			$accountinfo = $this->session->userdata ( 'accountinfo' );
			$query = "select sum(amount) as grand_total from invoices where  confirm=1 and accountid=$accountid";
			
			$ext_query = $this->db->query ( $query );
			if ($ext_query->num_rows () > 0) {
				$result_total = $ext_query->result_array ();
				$grandtotal = $result_total [0] ['grand_total'];
				$grand_total = $this->common->currency_decimal ( $grandtotal ) . ' ' . $currency_id;
			}
			
			$invoice_query = "select sum(credit) as grand_credit from invoice_details where accountid=$accountid";
			$credit_query = $this->db->query ( $invoice_query );
			if ($credit_query->num_rows () > 0) {
				$credit_total = $credit_query->result_array ();
				$grand_credit_total = $credit_total [0] ['grand_credit'];
				$grandcredit = $grand_total - $grand_credit_total;
				$grand_credit = $this->common->currency_decimal ( $grandcredit ) . ' ' . $currency_id;
			}
			$download = '<a href="' . base_url () . '/user/user_invoice_download/' . $value ['id'] . '/00' . $value ['invoice_prefix'] . $value ['invoiceid'] . '" class="btn btn-royelblue btn-sm"  title="Download Invoice" ><i class="fa fa-cloud-download fa-fw"></i></a>&nbsp';
			if ($outstanding > 0) {
				$payment = ' <a style="padding: 0 8px;" href="' . base_url () . 'user/user_invoice_payment/' . $value ['id'] . '" class="btn btn-warning"  title="Payment">Unpaid</a>';
			} else {
				
				$payment = ' <button style="padding: 0 8px;" class="btn btn-success" type="button">Paid</button>';
			}
			$account_arr = $this->db_model->getSelect ( 'first_name,number,last_name', 'accounts', array (
					'id' => $value ['accountid'] 
			) );
			$account_array = $account_arr->result_array ();
			$date = strtotime ( $value ['invoice_date'] );
			$date = strtotime ( "+7 day", $date );
			$time = date ( "Y-m-d h:i:s ", $date );
			$json_data ['rows'] [] = array (
					'cell' => array (
							$value ['invoice_prefix'] . $value ['invoiceid'] . ' (' . $value ['type'] . ')',
							$account_array [0] ['first_name'] . ' ' . $account_array [0] ['last_name'] . '</br>' . $account_array [0] ['number'],
							$invoice_date,
							$from_date,
							$due_date,
							$payment_last,
							$this->common->currency_decimal ( $value ['amount'] ) . ' ' . $currency_id,
							$this->common->currency_decimal ( $outstanding ) . ' ' . $currency_id,
							$download . $payment 
					)
					 
			);
		}
		
		$json_data ['rows'] [] = array (
				'cell' => array (
						// $date.'- 0'.$value['id'].'('.$value['type'].')',
						'<b>Grand Total</b>',
						'',
						'',
						'',
						'',
						'',
						"<b>" . $grand_total . "</b>",
						"<b>" . $grand_credit . "<b>",
						'' 
				)
				 
		);
		echo json_encode ( $json_data );
	}
	function invoice_logo_delete($accountid) {
		$invoiceconf = $this->db_model->getSelect ( "*", "invoice_conf", array (
				"id" => $accountid 
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
		// redirect(base_url() . 'invoices/invoice_conf/');
	}
	function invoice_list_view_invoice($invoiceid = false) {
		$data ['username'] = $this->session->userdata ( 'user_name' );
		$data ['page_title'] = gettext ( 'Invoice Detail' );
		
		$cdrs_query = $this->invoices_model->getCdrs_invoice ( $invoiceid );
		
		$invoice_cdr_list = array ();
		$cdr_list = array ();
		if ($cdrs_query->num_rows () > 0) {
			foreach ( $cdrs_query->result_array () as $cdr ) {
				$cdr ['charge'] = $this->common_model->calculate_currency ( $cdr ['debit'] - $cdr ['credit'] );
				array_push ( $cdr_list, $cdr );
			}
		}
		$data ['invoice_cdr_list'] = $cdr_list;
		
		$invoice_total_query = $this->Astpp_common->get_invoice_total ( $invoiceid );
		
		$total_list = array ();
		$invoice_total_list = array ();
		
		if ($invoice_total_query->num_rows () > 0) {
			foreach ( $invoice_total_query->result_array () as $total ) {
				array_push ( $total_list, $total );
			}
		}
		$data ['invoice_total_list'] = $total_list;
		$invoicedata = $this->Astpp_common->get_invoice ( $invoiceid );
		$data ['invoiceid'] = @$invoicedata [0] ['invoiceid'];
		$data ['invoicedate'] = @$invoicedata [0] ['date'];
		$data ['accountid'] = @$invoicedata [0] ['accountid'];
		if (! empty ( $invoicedata )) {
			$accountinfo = $this->invoices_model->get_account_including_closed ( @$invoicedata [0] ['accountid'] );
			$data ['accountinfo'] = $accountinfo;
		}
		$invoiceconf = $this->invoices_model->get_invoiceconf ( $accountinfo ['reseller'] );
		$data ['invoiceconf'] = $invoiceconf;
		$this->load->view ( 'view_account_invoice_detail', $data );
	}
	function invoice_download($invoiceid) {
		$this->permission->check_web_record_permission($invoiceid,'invoices',"invoices/invoice_list/");
		$this->db->where ( 'id', $invoiceid );
		$this->db->select ( 'type,accountid' );
		$this->db->from ( 'invoices' );
		$result = $this->db->get ();
		if ($result->num_rows () > 0) {
			$result = (array)$result->first_row ();
			if ($result['type'] == 'I') {
				$this->invoice_main_download ( $invoiceid );
			}
			if ($result['type'] == 'R') {
				$this->receipt_download ( $invoiceid );
			}
		} else {
			redirect ( base_url () . 'invoices/invoice_list/' );
		}
	}
	function invoice_screen() {
		$login_type = $this->session->userdata ['userlevel_logintype'];
		if ($login_type == - 1 || $login_type == 2 || $login_type == 1 || $login_type == 4) {
			if ($this->input->post ()) {
				$data = $this->input->post ();
				if ($data ['accountid'] == '' || $data ['accountid'] == '-Select-') {
					$this->session->set_flashdata ( 'astpp_notification', 'Please select accounts' );
					redirect ( base_url () . "invoices/invoice_list/" );
				}
				if (! empty ( $data )) {
					if (isset ( $data ['notes'] ) && $data ['notes'] != '') {
						$this->session->set_userdata ( 'invoice_note', $data ['notes'] );
					}
					$date = date ( 'Y-m-d' );
					$feture_date = date ( 'Y-m-d', strtotime ( $date ) );
					$from_date = $data ['fromdate'];
					$genrated_date = $data ['todate'];
					$to_date = date ( 'Y-m-d', strtotime ( $genrated_date ) );
					if ($to_date > $feture_date) {
						$this->session->set_flashdata ( 'astpp_notification', 'To date should not be greater than current date.' );
						redirect ( base_url () . "invoices/invoice_list/" );
					} else {
						$todate = $data ['todate'] . ' ' . '23:59:59';
						$from_date = $data ['fromdate'] . ' ' . '00:00:01';
						$accountid = $data ['accountid'];
						$acc_query = $this->db_model->getSelect ( "*", "accounts", array (
								"id" => $accountid 
						) );
						$accountdata = $acc_query->result_array ();
						$accountdata = $accountdata [0];
						$screen_path = getcwd () . "/cron";
						$screen_filename = "Email_Broadcast_" . strtotime ( 'now' );
						$command = "cd " . $screen_path . " && /usr/bin/screen -d -m -S  $screen_filename php cron.php BroadcastEmail";
						exec ( $command );
						$invoice_data_count = 0;
						$invoice_conf = array ();
						if ($accountdata ['reseller_id'] == 0) {
							$where = array (
									"accountid" => 1 
							);
						} else {
							$where = array (
									"accountid" => $accountdata ['reseller_id'] 
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
						$last_invoice_ID = $this->get_invoice_date ( "invoiceid" );
						if ($last_invoice_ID && $last_invoice_ID > 0) {
							$last_invoice_ID = ($last_invoice_ID + 1);
						} else {
							$last_invoice_ID = $invoice_conf ['invoice_start_from'];
						}
						$last_invoice_ID = str_pad ( $last_invoice_ID, (strlen ( $last_invoice_ID ) + 4), '0', STR_PAD_LEFT );
						if ($accountdata ['posttoexternal'] == 1) {
							$balance = ($accountdata ['credit_limit'] - $accountdata ['balance']);
						} else {
							$balance = $accountdata ['balance'];
						}
						if ($invoice_conf ['interval'] > 0) {
							$due_date = gmdate ( "Y-m-d H:i:s", strtotime ( gmdate ( "Y-m-d H:i:s" ) . " +" . $invoice_conf ['interval'] . " days" ) );
						} else {
							$due_date = gmdate ( "Y-m-d H:i:s", strtotime ( gmdate ( "Y-m-d H:i:s" ) . " +7 days" ) );
						}
						$invoice_data = array (
								"accountid" => $accountdata ['id'],
								"invoice_prefix" => $invoice_conf ['invoice_prefix'],
								"invoiceid" => $last_invoice_ID,
								"reseller_id" => $accountdata ['reseller_id'],
								"invoice_date" => gmdate ( "Y-m-d H:i:s" ),
								"from_date" => $from_date,
								"to_date" => $todate,
								"due_date" => $due_date,
								"status" => 1,
								"amount" => "0.00",
								"balance" => $balance,
								'generate_type' => 1,
								'confirm' => 0,
								'notes' => $data ['notes'] 
						);
						// echo "<pre>"; print_r($invoice_data); exit;
						$invoice_note = $this->session->userdata ( 'invoice_note' );
						$this->session->unset_userdata ( 'invoice_note' );
						$invoice_data ['invoice_note'] = $invoice_note;
						$this->db->insert ( "invoices", $invoice_data );
						$invoiceid = $this->db->insert_id ();
						// $generateInvoice->process_invoice($accountdata,$from_date,$todate);
						$this->session->set_flashdata ( 'astpp_errormsg', 'Invoice generation completed .' );
						redirect ( base_url () . "invoices/invoice_manually_edit/" . $invoiceid );
					}
				}
			} else {
				$this->session->set_flashdata ( 'astpp_errormsg', 'No data found.....' );
				redirect ( base_url () . "invoices/invoice_list/" );
			}
		} else {
			$this->session->set_flashdata ( 'astpp_notification', 'Permission Denied.' );
			redirect ( base_url () . "invoices/invoice_list/" );
		}
	}
	
	/**
	 *
	 * @param string $select        	
	 */
	function get_invoice_date($select, $accountid = false) {
		if ($accountid) {
			$where = array (
					'type' => "I",
					"accountid" => $accountid 
			);
			$query = $this->db_model->select ( $select, "invoices", $where, "to_date", "DESC", "1", "0" );
		} else {
			$where = array (
					'type' => "I" 
			);
			$query = $this->db_model->select ( $select, "invoices", $where, "id", "DESC", "1", "0" );
		}
		if ($query->num_rows () > 0) {
			$invoiceid = $query->result_array ();
			$invoice_date = $invoiceid [0] [$select];
			return $invoice_date;
		}
		return false;
	}
	function invoice_main_download($invoiceid) {
		$invoicedata = $this->db_model->getSelect ( "*", "invoices", array (
				"id" => $invoiceid 
		) );
		$invoicedata = $invoicedata->result_array ();
		$invoicedata = $invoicedata [0];
		$invoice_path = '';
		$accountid = $invoicedata ['accountid'];
		$acc_file = $invoice_path . $accountid . '/' . $invoiceid;
		$accountdata = $this->db_model->getSelect ( "*", "accounts", array (
				"id" => $accountid 
		) );
		$accountdata = $accountdata->result_array ();
		$accountdata = $accountdata [0];
		
		$login_type = $this->session->userdata ['userlevel_logintype'];
		$query = "select item_type,credit from invoice_details where invoiceid = " . $invoicedata ['id'] . " and item_type='INVPAY' Group By id order by item_type desc";
		// echo $query; exit;
		$invoice_total_query = $this->db->query ( $query );
		if ($invoice_total_query->num_rows () > 0) {
			$total = $invoice_total_query->result_array ();
			foreach ( $total as $key => $value ) {
				$debit = $value ['credit'];
			}
			if ($debit) {
				// echo 'debit'; exit;
				$invoice_path = $this->config->item ( 'invoices_path' );
				$download_path = $invoice_path . $accountdata ["id"] . '/' . $invoicedata ['invoice_prefix'] . $invoicedata ['invoiceid'] . ".pdf";
				unlink ( $download_path );
				$res = $this->common->get_invoice_template ( $invoicedata, $accountdata, "TRUE" );
			}
		}
		$invoice_path = $this->config->item ( 'invoices_path' );
		$download_path = $invoice_path . $accountdata ["id"] . '/' . $invoicedata ['invoice_prefix'] . $invoicedata ['invoiceid'] . ".pdf";
		$res = $this->common->get_invoice_template ( $invoicedata, $accountdata, "TRUE" );
	}
	function Sec2Minutes($seconds) {
		return sprintf ( "%02.2d:%02.2d", floor ( $seconds / 60 ), $seconds % 60 );
	}
	function receipt_download($invoiceid) {
		$login_info = $this->session->userdata ( 'accountinfo' );
		ob_start ();
		$this->load->library ( '/html2pdf/html2pdf' );
		$template_config = $this->config->item ( 'invoice_template' );
		include ($template_config . 'invoice_template_receipt.php');
		$content = ob_get_clean ();
		ob_clean ();
		$accountid = $this->common->get_field_name ( 'accountid', 'invoices', $invoiceid );
		$accountdata = $this->db_model->getSelect ( "*", "accounts", array (
				"id" => $accountid 
		) );
		$accountdata = $accountdata->result_array ();
		$accountdata = $accountdata [0];
		$accountdata ["country"] = $this->common->get_field_name ( 'country', 'countrycode', $accountdata ["country_id"] );
		
		if ($login_info ['type'] == - 1) {
			$data ["to_currency"] = Common_model::$global_config ['system_config'] ['base_currency'];
			$currency = $data ["to_currency"];
		} elseif ($login_info ['type'] == 1) {
			$accountdata ["currency_id"] = $this->common->get_field_name ( 'currency', 'currency', $login_info ["currency_id"] );
			$currency = $accountdata ["currency_id"];
		} else {
			$accountdata ["currency_id"] = $this->common->get_field_name ( 'currency', 'currency', $login_info ["currency_id"] );
			$currency = $accountdata ["currency_id"];
		}
		$invoice_cdr_list = array ();
		$invoicedata = $this->db_model->getSelect ( "*", "invoices", array (
				"id" => $invoiceid 
		) );
		$invoicedata = (array)$invoicedata->first_row();
		
		$data ['invoiceid'] = $invoicedata['id'];
		$data ['id'] = $invoicedata ['invoiceid'];
		$data ['invoice_date'] = $invoicedata['invoice_date'];
		$data ['accountid'] = $invoicedata['accountid'];
		$data ['from_date'] = $invoicedata['from_date'];
		$data ['to_date'] = $invoicedata['to_date'];
		$total_list = array ();
		$data ['description'] = '';
		$data ['item_type'] = '';
		$data ['debit'] = '';
		$invoice_total_list = array ();
		$query = "select item_type,description,created_date,invoiceid,debit,credit from invoice_details where invoiceid = " . $invoiceid . " And ( item_type='POSTCHARG' Or item_type='Refill') Group By item_type";
		$invoice_total_query = $this->db->query ( $query );
		if ($invoice_total_query->num_rows () > 0) {
			$invoice_total_query = $invoice_total_query->result_array ();
			foreach ( $invoice_total_query as $key => $value ) {
				$data ['item_type'] = $value ['item_type'];
				$data ['description'] = $value ['description'];
				if ($value ['item_type'] == 'Refill') {
					$data ['debit'] = $value ['credit'];
				} else {
					$data ['debit'] = $value ['debit'];
				}
				$created_date = $value ['created_date'];
				$invoicedata ['invoiceid'] = $value ['invoiceid'];
			}
		} else {
//ASTPP_receipt_description_not_display_issue
			$query = "select item_type,description,created_date,invoiceid,debit,credit from invoice_details where invoiceid = " . $invoiceid . " And ( item_type='SUBCHRG' OR  item_type='DIDCHRG') Group By item_type";
//END
			$invoice_total_query = $this->db->query ( $query );
			if ($invoice_total_query->num_rows () > 0) {
				$invoice_total_query = $invoice_total_query->result_array ();
				foreach ( $invoice_total_query as $key => $value ) {
					$data ['item_type'] = $value ['item_type'];
					$data ['description'] = $value ['description'];
					if ($value ['item_type'] == 'Refill') {
						$data ['debit'] = $value ['credit'];
					} else {
						$data ['debit'] = $value ['debit'];
					}
					$created_date = $value ['created_date'];
					$invoicedata ['invoiceid'] = $value ['invoiceid'];
				}
			}
		}
		$data ['accountinfo'] = $accountdata;
		// Get invoice header information
		if ($accountdata ['reseller_id'] == '0')
			$accountid = '1';
		else
			$accountid = $accountdata ['reseller_id'];
		$invoiceconf = $this->db_model->getSelect ( "*", "invoice_conf", array (
				"accountid" => $accountid 
		) );
		$invoiceconf = $invoiceconf->result_array ();
		if (! empty ( $invoiceconf )) {
			$data ['invoiceconf'] = $invoiceconf [0];
		} else {
			$invoiceconf = $this->db_model->getSelect ( "*", "invoice_conf", array (
					"accountid" => "1" 
			) );
			$invoiceconf = $invoiceconf->result_array ();
			$data ['invoiceconf'] = $invoiceconf [0];
		}
		
		$INVOICE_NUM = '';
		$ACCOUNTADD = '';
		$ACCOUNTADD_CUSTOMER = '';
		$INVOICE_DETAIL = '';
		$INVOICE_CHARGE = '';
		$total_sum = 0;
		$total_vat = 0;
		$fromdate = strtotime ( $invoicedata ['from_date'] );
		$from_date = date ( "Y-m-d", $fromdate );
		$duedate = strtotime ( $invoicedata ['due_date'] );
		$due_date = date ( "Y-m-d", $duedate );
		/**
		 * ****************************Invoivce number*************************************************************
		 */
		$INVOICE_NUM = '<td style="font-size: 10px;color:#000;font-family:arial; line-height: 22px;letter-spacing:02;float:right;"><b><h2>Receipt: ' . $invoicedata ["invoice_prefix"] . $data ['id'] . '</h2></b></td>';
		/**
		 * ************************* Company Address Code START **************************************************
		 */
		$ACCOUNTADD .= '<tr><td style="width:100%;font-size: 12px;color:#000;font-family:arial; line-height: 22px;">' . $data['invoiceconf']['company_name'] . '</td>';
		$ACCOUNTADD .= '</tr>';
		$ACCOUNTADD .= '<tr><td style="width:100%;font-size: 12px;color:#000;font-family:arial; line-height: 22px;">' . $data ['invoiceconf']['address'] . '</td></tr>';
		$ACCOUNTADD .= '<tr><td style="width:100%;font-size: 12px;color:#000;font-family:arial; line-height: 22px;">' . $data ['invoiceconf']['city']. ' - ' .$data ['invoiceconf']['zipcode']. '</td></tr>';
		$ACCOUNTADD .= '<tr><td style="width:100%;font-size: 12px;color:#000;font-family:arial; line-height: 22px;">' . $data ['invoiceconf']['province'] . ', ' .$data ['invoiceconf']['country'].'</td></tr>';
		$ACCOUNTADD .= '<tr><td style="width:100%;font-size: 12px;color:#000;font-family:arial; line-height: 22px;">' . $data ['invoiceconf']['invoice_taxes_number'] . '</td></tr>';
		/**
		 * ************************* Company Address Code END **************************************************
		 */
	
		/**
		 * ************************* Customer Address Code START **************************************************
		 */
		$ACCOUNTADD_CUSTOMER .= '<table align=right>';
		$ACCOUNTADD_CUSTOMER .= '<tr>';
		$ACCOUNTADD_CUSTOMER .= '<td align="right" style="width:100%;font-size: 12px;color:#000;font-family:arial; line-height: 22px;">' . $accountdata ['company_name'] . '</td></tr>';
		$ACCOUNTADD_CUSTOMER .= '<tr><td align="right" style="width:100%;font-size: 12px;color:#000;font-family:arial; line-height: 22px;">' . $accountdata ['address_1'] . '</td></tr>';
		$ACCOUNTADD_CUSTOMER .= '<tr><td align="right" style="width:100%;font-size: 12px;color:#000;font-family:arial; line-height: 22px;">' . $accountdata ['city'] . ' - ' .$accountdata ['postal_code'].'</td></tr>';
		$ACCOUNTADD_CUSTOMER .= '<tr><td align="right" style="width:100%;font-size: 12px;color:#000;font-family:arial; line-height: 22px;">' . $accountdata ['province'] . ', ' .$accountdata ['country'].'</td></tr>';
		$ACCOUNTADD_CUSTOMER .= '<tr><td style="width:100%;font-size: 12px;color:#000;font-family:arial; line-height: 22px;">' . $accountdata ['tax_number'] . '</td></tr>';
		$ACCOUNTADD_CUSTOMER .= "</table>";
		/**
		 * ************************* Customer Address Code END **************************************************
		 */
		/**
		 * *************************Invoice detail START *******************************************************
		 */
		
		$INVOICE_DETAIL .= '<tr>
                                <td style="width:100%;">
                                <table style="width:100%;" cellspacing="0">
                                <tbody><tr>
                               
                                <td style="width:25%;"><b>Receipt Date :</b></td>
                                <td style="width:25%;text-align:right;border-right:1px solid #000; padding-right: 3px;">' . date ( "Y-m-d", strtotime ( $invoicedata ['from_date'] ) ) . '</td>
                                <td style="width:25%;"><b>This Receipt Amount :</b> </td>
                                <td style="width:25%;text-align:right;">' . $this->currency_decimal ( $this->common_model->calculate_currency ( $invoicedata ['amount'] ) ) . '</td>
                                </tr>
                                <tr>
                               
                                <td style="width:25%;"></td>
                                <td style="width:25%;border-right:1px solid #000;"></td>
                                <td style="width:25%;"></td>
                                <td style="width:25%;"></td>
                                </tr>
                                  
                                <tr>
                                <td style="width:25%;border-bottom:1px solid #000;"><b>Account Number :</b></td>
                                <td style="width:25%;border-bottom:1px solid #000;text-align:right;border-right:1px solid #000;padding-right: 3px;">' . $accountdata ['number'] . '</td>
                                <td colspan="2" style="width:50%;padding-left: 3px;">
                                <table style="border:1px solid #000;width:100%;">
                                <tbody><tr>
                                <td style="width:50%;font-weight:bold;font-style:italic;">Total Amount :</td>
                                <td style="width:50%;text-align:right;font-style:italic;">' . $this->currency_decimal ( $this->common_model->calculate_currency ( $invoicedata ['amount'] ) ) . '</td>
                                </tr>
                                </tbody></table>
                                </td>
                                </tr>
                                </tbody></table>
                                </td>
                             </tr>';
		
		/**
		 * *************************Invoice detail END *******************************************************
		 */
		
		/**
		 * **************************charge History START *****************************************************
		 */
		
		$INVOICE_CHARGE .= '
                                        
                                        <tr><th style="width:20%;border-left:1px solid #000;border-top:1px solid #000;border-bottom:1px solid #000;background-color:#EBEEF2;padding:5px;">Date </th>
                                        <th style="width:40%;border-left:1px solid #000;border-top:1px solid #000;border-bottom:1px solid #000;background-color:#EBEEF2;padding:5px;">Description</th>
                                       
                                        <th style="width:20%;border-left:1px solid #000;border-top:1px solid #000;border-bottom:1px solid #000;background-color:#EBEEF2;padding:5px;" align="right">Unit Price (' . $currency . ')</th>
                                        <th style="width:20%;border-left:1px solid #000;border-right:1px solid #000;border-top:1px solid #000;border-bottom:1px solid #000;background-color:#EBEEF2;padding:5px;" align="right">Cost (' . $currency . ')</th>
                                        </tr>
                                        ';
		
		// echo "<pre>";print_r($charge_res['description']);exit;
		$INVOICE_CHARGE .= '<tr >
                                                        <td style="width:20%;line-height:15px;border-left:1px solid #000;border-bottom:1px solid #000;padding:5px;">' . date ( 'Y-m-d', strtotime ( $invoicedata ['invoice_date'] ) ) . '</td>
                                                        <td style="width:40%;line-height:15px;border-left:1px solid #000;border-bottom:1px solid #000;padding:5px;">' . $data ['description'] . '</td>
                                                        <td style="width:20%;line-height:15px;border-left:1px solid #000;border-bottom:1px solid #000;text-align:right;padding:5px;">' . $this->currency_decimal ( $this->common_model->calculate_currency ( $invoicedata ['amount'] ) ) . '</td>
                                                        <td style="width:20%;line-height:15px;border-right:1px solid #000;border-left:1px solid #000;border-bottom:1px solid #000;text-align:right;padding:5px;">' . $this->currency_decimal ( $this->common_model->calculate_currency ( $invoicedata ['amount'] ) ) . '</td>
                                                     </tr>';
		
		$INVOICE_CHARGE .= '<tr>
                                                <td></td><td></td>
                                                <td  style="border-left:1px solid #000;border-bottom:1px solid #000;width:20%;padding:5px;text-align:left;" align="right">Sub Total</td>
                                                <td style="border-left:1px solid #000;border-bottom:1px solid #000;border-right:1px solid #000;width:10%;text-align:right;padding-top:5px;padding-right:5px;">' . $this->currency_decimal ( $this->common_model->calculate_currency ( $invoicedata ['amount'] ) ) . '</td>
                                                </tr>';
		
		/*
		 * if ($invoice_tax->num_rows() > 0) {
		 *
		 * $total_vat += $invoice_tax['debit'];
		 * $total_vat = $this->currency_decimal($this->CI->common_model->calculate_currency($total_vat));
		 * $INVOICE_CHARGE .= '<tr>
		 * <td></td><td></td>
		 * <td colspan=2 style="border-left:1px solid #000;border-bottom:1px solid #000;width:20%;padding-left:5px;padding-top:5px;padding-bottom:5px;text-align:left;">Tax ('.$invoice_tax["description"]. '):</td>
		 * <td style="border-left:1px solid #000;border-bottom:1px solid #000;border-right:1px solid #000;width:10%;text-align:right;padding-top:5px;padding-right:5px;">'.$this->currency_decimal($total_vat).'</td>
		 * </tr>';
		 *
		 *
		 * }
		 * $sub_total = $total_sum + $total_vat;
		 */
		$INVOICE_CHARGE .= '<tr>
                                                <td></td><td></td>
                                                <td  style="border-left:1px solid #000;border-bottom:1px solid #000;width:20%;padding:5px;text-align:left;" align="right"><b>Total</b></td>
                                                <td style="border-left:1px solid #000;border-bottom:1px solid #000;border-right:1px solid #000;width:10%;text-align:right;padding-top:5px;padding-right:5px;">' . $this->currency_decimal ( $this->common_model->calculate_currency ( $invoicedata ['amount'] ) ) . '</td>
                                                </tr>
                                                
                                               ';
		
		/**
		 * ****************************charge History END ****************************************************
		 */
		
		/**
		 * ************************* Invoice Note Code END **************************************************
		 */
		
		$invoice_notes = $this->db_model->getSelect ( '*', 'invoices', array (
				'id' => $invoicedata ['id'] 
		) );
		$invoice_notes = $invoice_notes->result_array ();
		
		if (isset ( $invoice_notes [0] ['notes'] )) {
			$invoice_notes = $invoice_notes [0] ['notes'];
		} else {
			if ($invoice_notes [0] ['invoice_note'] == '0') {
				$invoice_notes = 'THIS IS A 30 DAY ACCOUNT, SO PLEASE MAKE PAYMENT WITHIN THESE TERMS';
			} else {
				$invoice_notes = $invoice_notes [0] ['invoice_note'];
			}
		}
		/**
		 * ************************* Invoice Note Code END **************************************************
		 */
//ASTPP_invoice_download_issue
		if (! empty ( $data ['invoiceconf'] ) && $data ['invoiceconf'] != '') {
			$logo = $data ['invoiceconf'] ['logo'];
			$dir_path = getcwd () . "/upload/";
			$path = $dir_path . $data ['invoiceconf'] ['accountid'] . "_" . $data ['invoiceconf'] ['logo'];

			if (file_exists ( $path )) {
				if ($logo != '') {
					$src = $path;
					$logo = "<img style='height:50px; width:180px; margin-left:70px;' alt='logo' src='" . $src . "'>";
				} else {
					$path = FCPATH . "/assets/images/logo.png";
					$src = $path;
					$logo = "<img style='height:50px; width:180px; margin-left:70px;' alt='logo' src='" . $src . "'>";
				}
			} else {
				$dir_path = FCPATH . "/upload/logo.png";
				$src = $dir_path;
				$logo = "<img style='height:50px; width:180px; margin-left:70px;' alt='logo' src='" . $src . "'>";
			}
		}
//END
		$content = str_replace ( "<INVOICE_NUM>", $INVOICE_NUM, $content );
		$content = str_replace ( "<ACCOUNTADD>", $ACCOUNTADD, $content );
		$content = str_replace ( "<ACCOUNTADD_CUSTOMER>", $ACCOUNTADD_CUSTOMER, $content );
		$content = str_replace ( "<INVOICE_DETAIL>", $INVOICE_DETAIL, $content );
		$content = str_replace ( "<INVOICE_CHARGE>", $INVOICE_CHARGE, $content );
		$content = str_replace ( "<NOTES>", $invoice_notes, $content );
		$content = str_replace ( "<LOGO>", $src, $content );
		// echo $content ; exit;
		
		$invoice_path = $this->config->item ( 'invoices_path' );
		$download_path = $invoicedata ['invoice_prefix'] . $data ['id'] . ".pdf";
		$this->html2pdf->pdf->SetDisplayMode ( 'fullpage' );
		$this->html2pdf->writeHTML ( $content );
		
		$this->html2pdf->Output ( $download_path, "D" );
	}
	function calculate_currency($amount, $accountdata) {
		$base_currency = Common_model::$global_config ['system_config'] ['base_currency'];
		$from_currency = Common_model::$global_config ['currency_list'] [$base_currency];
		$to_currency = $this->db_model->getSelect ( "currencyrate", "currency", array (
				"currency" => $accountdata ["currency_id"] 
		) );
		if ($to_currency->num_rows () > 0) {
			$to_currency_arr = $to_currency->result_array ();
			$to_currency = $to_currency_arr [0] ["currencyrate"];
		} else {
			$to_currency = $from_currency;
		}
		
		$cal_amount = ($amount * $to_currency) / $from_currency;
		return $cal_amount;
	}
	function format_currency($amount) {
		$dp = $this->db_model->getSelect ( "value", "system", array (
				"name" => "decimalpoints" 
		) );
		$dp = $dp->result_array ();
		$dp = $dp [0] ["value"];
		
		return money_format ( '%.' . $dp . 'n', $amount );
	}
	function date_diff_custom($end = '2020-06-09 10:30:00', $out_in_array = false) {
		$intervalo = date_diff ( date_create (), date_create ( $end ) );
		$out = $intervalo->format ( "Years:%Y,Months:%M,Days:%d,Hours:%H,Minutes:%i,Seconds:%s" );
		if (! $out_in_array)
			return $out;
		$a_out = array ();
		array_walk ( explode ( ',', $out ), function ($val, $key) use (&$a_out) {
			$v = explode ( ':', $val );
			$a_out [$v [0]] = $v [1];
		} );
		return $a_out;
	}
	function invoice_list_search() {
		$ajax_search = $this->input->post ( 'ajax_search', 0 );
		
		if ($this->input->post ( 'advance_search', TRUE ) == 1) {
			$this->session->set_userdata ( 'advance_search', $this->input->post ( 'advance_search' ) );
			$action = $this->input->post ();
			
			echo '<pre>';
			print_r ( $action );
			unset ( $action ['action'] );
			unset ( $action ['advance_search'] );
			$action ['from_date'] [0] = $action ['from_date'] [0] ? $action ['from_date'] [0] . " 00:00:00" : '';
			$action ['invoice_date'] [0] = $action ['invoice_date'] [0] ? $action ['invoice_date'] [0] . " 00:00:00" : '';
			$this->session->set_userdata ( 'invoice_list_search', $action );
		}
		if (@$ajax_search != 1) {
			redirect ( base_url () . 'invoices/invoice_list/' );
		}
	}
	function invoice_list_clearsearchfilter() {
		$this->session->set_userdata ( 'advance_search', 0 );
		$this->session->set_userdata ( 'invoice_list_search', "" );
	}
	/**
	 * ============ From below code developed for ASTPP version 2.0 ======================================*
	 */
	function generate_receipt($accountid, $amount, $accountinfo, $last_invoice_ID, $invoice_prefix, $due_date) {
		$invoice_data = array (
				"accountid" => $accountid,
				"invoice_prefix" => $invoice_prefix,
				"invoiceid" => '0000' . $last_invoice_ID,
				"reseller_id" => $accountinfo ['reseller_id'],
				"invoice_date" => gmdate ( "Y-m-d H:i:s" ),
				"from_date" => gmdate ( "Y-m-d H:i:s" ),
				"to_date" => gmdate ( "Y-m-d H:i:s" ),
				"due_date" => $due_date,
				"status" => 1,
				"balance" => $accountinfo ['balance'],
				"amount" => $amount,
				"type" => 'R',
				"confirm" => '1' 
		);
		$this->db->insert ( "invoices", $invoice_data );
		$invoiceid = $this->db->insert_id ();
		return $invoiceid;
	}
	function insert_invoice_total_data($invoiceid, $sub_total, $sort_order) {
		$invoice_total_arr = array (
				"invoiceid" => $invoiceid,
				"sort_order" => $sort_order,
				"value" => $sub_total,
				"title" => "Sub Total",
				"text" => "Sub Total",
				"class" => "1" 
		);
		$this->db->insert ( "invoices_total", $invoice_total_arr );
		return $sort_order ++;
	}
	function apply_invoice_taxes($invoiceid, $accountid, $sort_order) {
		$tax_priority = "";
		$where = array (
				"accountid" => $accountid 
		);
		$accounttax_query = $this->db_model->getSelectWithOrder ( "*", "taxes_to_accounts", $where, "ASC", "taxes_priority" );
		if ($accounttax_query->num_rows () > 0) {
			$accounttax_query = $accounttax_query->result_array ();
			foreach ( $accounttax_query as $tax_key => $tax_value ) {
				$taxes_info = $this->db->get_where ( 'taxes', array (
						'id' => $tax_value ['taxes_id'] 
				) );
				if ($taxes_info->num_rows () > 0) {
					$tax_value = $taxes_info->result_array ();
					$tax_value = $tax_value [0];
					if ($tax_value ["taxes_priority"] == "") {
						$tax_priority = $tax_value ["taxes_priority"];
					} else if ($tax_value ["taxes_priority"] > $tax_priority) {
						$query = $this->db_model->getSelect ( "SUM(value) as total", "invoices_total", array (
								"invoiceid" => $invoiceid 
						) );
						$query = $query->result_array ();
						$sub_total = $query ["0"] ["total"];
					}
					$tax_total = (($sub_total * ($tax_value ['taxes_rate'] / 100)) + $tax_value ['taxes_amount']);
					$tax_array = array (
							"invoiceid" => $invoiceid,
							"title" => "TAX",
							"text" => $tax_value ['taxes_description'],
							"value" => $tax_total,
							"class" => "2",
							"sort_order" => $sort_order 
					);
					$this->db->insert ( "invoices_total", $tax_array );
					$sort_order ++;
				}
			}
		}
		return $sort_order;
	}
	function set_invoice_total($invoiceid, $sort_order) {
		$query = $this->db_model->getSelect ( "SUM(value) as total", "invoices_total", array (
				"invoiceid" => $invoiceid 
		) );
		$query = $query->result_array ();
		$sub_total = $query ["0"] ["total"];
		
		$invoice_total_arr = array (
				"invoiceid" => $invoiceid,
				"sort_order" => $sort_order,
				"value" => $sub_total,
				"title" => "Total",
				"text" => "Total",
				"class" => "9" 
		);
		$this->db->insert ( "invoices_total", $invoice_total_arr );
		return true;
	}
	function invoice_delete_statically($inv_id) {
		$data = array (
				'deleted' => 1 
		);
		$this->db->where ( 'id', $inv_id );
		$this->db->update ( "invoices", $data );
		$this->session->set_flashdata ( 'astpp_notification', 'Invoices removed successfully' );
		redirect ( base_url () . 'invoices/invoice_list/' );
	}
	function invoice_delete_massege() {
		$this->session->set_flashdata ( 'astpp_notification', 'Invoices removed successfully' );
		redirect ( base_url () . 'invoices/invoice_list/' );
	}
}

?>
 
