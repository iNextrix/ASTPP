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
class Reports extends MX_Controller {
	function Reports() {
		parent::__construct ();
		
		$this->load->helper ( 'template_inheritance' );
		
		$this->load->library ( 'session' );
		$this->load->library ( "reports_form" );
		$this->load->library ( 'astpp/form' );
		$this->load->model ( 'reports_model' );
		$this->load->library ( 'fpdf' );
		$this->load->library ( 'pdf' );
		$this->fpdf = new PDF ( 'P', 'pt' );
		$this->fpdf->initialize ( 'P', 'mm', 'A4' );
		
		if ($this->session->userdata ( 'user_login' ) == FALSE)
			redirect ( base_url () . '/astpp/login' );
	}
	function customerReport() {
		$data ['page_title'] = gettext ( 'Customer CDRs Report' );
		$data ['search_flag'] = true;
		$this->session->set_userdata ( 'advance_search', 0 );
		$data ['grid_fields'] = $this->reports_form->build_report_list_for_customer ();
		$data ["grid_buttons"] = $this->reports_form->build_grid_customer ();
		$data ['form_search'] = $this->form->build_serach_form ( $this->reports_form->get_customer_cdr_form () );
		$this->load->view ( 'view_cdr_customer_list', $data );
	}
	
	/*
	 * ****
	 * ASTPP 3.0
	 * Add recording file value in json
	 * ****
	 */
	function customerReport_json() {
		$count_res = $this->reports_model->getcustomer_cdrs ( false, "", "" );
		$accountinfo = $this->session->userdata ( 'accountinfo' );
		$count_all = ( array ) $count_res->first_row ();
		$paging_data = $this->form->load_grid_config ( $count_all ['count'], $_GET ['rp'], $_GET ['page'] );
		$json_data = $paging_data ["json_paging"];
		$query = $this->reports_model->getcustomer_cdrs ( true, $paging_data ["paging"] ["start"], $paging_data ["paging"] ["page_no"] );
		if ($query->num_rows () > 0) {
			// Initialization of Rategroup and Trunk Array
			$pricelist_arr = array ();
			$trunk_arr = array ();
			// Get search information from session.
			$search_arr = $this->session->userdata ( 'customer_cdr_list_search' );
			$show_seconds = (! empty ( $search_arr ['search_in'] )) ? $search_arr ['search_in'] : 'minutes';
			$query = $query->result_array ();
			
			$where = "id IN (" . $count_all ['pricelist_ids'] . ")";
			$this->db->where ( $where );
			$this->db->select ( 'id,name' );
			$pricelist_res = $this->db->get ( 'pricelists' );
			$pricelist_res = $pricelist_res->result_array ();
			foreach ( $pricelist_res as $value ) {
				$pricelist_arr [$value ['id']] = $value ['name'];
			}
			$where = "id IN (" . $count_all ['trunk_ids'] . ")";
			$this->db->where ( $where );
			$this->db->select ( 'id,name' );
			$trunk_res = $this->db->get ( 'trunks' );
			$trunk_res = $trunk_res->result_array ();
			foreach ( $trunk_res as $value ) {
				$trunk_arr [$value ['id']] = $value ['name'];
			}
			$where = "id IN (" . $count_all ['accounts_ids'] . ")";
			$this->db->where ( $where );
			$this->db->select ( 'id,number,first_name,last_name' );
			$account_res = $this->db->get ( 'accounts' );
			foreach ( $account_res->result_array () as $value ) {
				$account_arr [$value ['id']] = $value ['first_name'] . " " . $value ['last_name'] . ' (' . $value ['number'] . ')';
			}
			
			// Get Decimal points,system currency and user currency.
			$currency_info = $this->common->get_currency_info ();
			foreach ( $query as $value ) {
				$duration = ($show_seconds == 'minutes') ? ($value ['billseconds'] > 0) ? sprintf ( '%02d', $value ['billseconds'] / 60 ) . ":" . sprintf ( '%02d', $value ['billseconds'] % 60 ) : "00:00" : $value ['billseconds'];
				$account = isset ( $account_arr [$value ['accountid']] ) ? $account_arr [$value ['accountid']] : 'Anonymous';
				$uid = $value ['uniqueid'];
				if ($accountinfo ['type'] == 1) {
					$json_data ['rows'] [] = array (
							'cell' => array (
									$this->common->convert_GMT_to ( '', '', $value ['callstart'] ),
									$value ['callerid'],
									$value ['callednum'],
									filter_var ( $value ['pattern'], FILTER_SANITIZE_NUMBER_INT ),
									$value ['notes'],
									$duration,
									$this->common->calculate_currency_manually ( $currency_info, $value ['debit'], false ),
									$this->common->calculate_currency_manually ( $currency_info, $value ['cost'], false ),
									$value ['disposition'],
									$account,
									// isset($trunk_arr[$value['trunk_id']]) ? $trunk_arr[$value['trunk_id']] : '',
									isset ( $pricelist_arr [$value ['pricelist_id']] ) ? $pricelist_arr [$value ['pricelist_id']] : '',
									$value ['calltype'] 
							)
							// $recording,
							 
					);
				} else {
					$json_data ['rows'] [] = array (
							'cell' => array (
									$this->common->convert_GMT_to ( '', '', $value ['callstart'] ),
									$value ['callerid'],
									$value ['callednum'],
									filter_var ( $value ['pattern'], FILTER_SANITIZE_NUMBER_INT ),
									$value ['notes'],
									$duration,
									$this->common->calculate_currency_manually ( $currency_info, $value ['debit'], false ),
									$this->common->calculate_currency_manually ( $currency_info, $value ['cost'], false ),
									$value ['disposition'],
									$account,
									isset ( $trunk_arr [$value ['trunk_id']] ) ? $trunk_arr [$value ['trunk_id']] : '',
									isset ( $pricelist_arr [$value ['pricelist_id']] ) ? $pricelist_arr [$value ['pricelist_id']] : '',
									$value ['calltype'] 
							)
							// $recording,
							 
					);
				}
			}
			$duration = ($show_seconds == 'minutes') ? ($count_all ['billseconds'] > 0) ? floor ( $count_all ['billseconds'] / 60 ) . ":" . sprintf ( '%02d', $count_all ['billseconds'] % 60 ) : "00:00" : $count_all ['billseconds'];
			$json_data ['rows'] [] = array (
					"cell" => array (
							"<b>Grand Total</b>",
							"",
							"",
							"",
							"",
							"<b>$duration</b>",
							// $count_all['total_debit'],
							"<b>" . $this->common->calculate_currency_manually ( $currency_info, $count_all ['total_debit'], false ) . "</b>",
							"<b>" . $this->common->calculate_currency_manually ( $currency_info, $count_all ['total_cost'], false ) . "</b>",
							"",
							"",
							"",
							"",
							"",
							"",
							"",
							"" 
					) 
			);
		}
		echo json_encode ( $json_data );
	}
	
	/*
	 * ****
	 * ASTPP 3.0
	 * Recording file download
	 * ****
	 */
	function customerReport_recording_download($file_name) {
		$file_name = getcwd () . "/recording/" . $file_name;
		header ( 'Content-Description: File Transfer' );
		header ( 'Content-Type: application/octet-stream' );
		header ( 'Content-Disposition: attachment; filename=' . basename ( $file_name ) );
		header ( 'Content-Transfer-Encoding: binary' );
		header ( 'Expires: 0' );
		header ( 'Cache-Control: must-revalidate' );
		header ( 'Pragma: public' );
		ob_clean ();
		flush ();
		readfile ( $file_name );
		exit ();
	}
	function customerReport_search() {
		$ajax_search = $this->input->post ( 'ajax_search', 0 );
		if ($this->input->post ( 'advance_search', TRUE ) == 1) {
			$this->session->set_userdata ( 'advance_search', $this->input->post ( 'advance_search' ) );
			$action = $this->input->post ();
			unset ( $action ['action'] );
			unset ( $action ['advance_search'] );
			$this->session->set_userdata ( 'customer_cdr_list_search', $action );
		}
		if (@$ajax_search != 1) {
			redirect ( base_url () . 'reports/customerReport/' );
		}
	}
	function customerReport_clearsearchfilter() {
		$this->session->set_userdata ( 'advance_search', 0 );
		$this->session->set_userdata ( 'customer_cdr_list_search', "" );
	}
	
	/**
	 * ASTPP 3.0
	 * For Customer CDRs export
	 */
	function customerReport_export() {
		$account_info = $accountinfo = $this->session->userdata ( 'accountinfo' );
		$currency_id = $account_info ['currency_id'];
		$currency = $this->common->get_field_name ( 'currency', 'currency', $currency_id );
		$count_res = $this->reports_model->getcustomer_cdrs ( false, "", "" );
		$count_all = ( array ) $count_res->first_row ();
		ob_clean ();
		if ($count_all ['count'] > 0) {
			// Initialization of Rategroup and Trunk Array
			$pricelist_arr = array ();
			$trunk_arr = array ();
			$account_arr = array ();
			$query = $this->reports_model->getcustomer_cdrs ( true, '', '', true );
			// Get Decimal points,system currency and user currency.
			$currency_info = $this->common->get_currency_info ();
			// Get search information from session.
			$search_arr = $this->session->userdata ( 'customer_cdr_list_search' );
			$show_seconds = (! empty ( $search_arr ['search_in'] )) ? $search_arr ['search_in'] : 'minutes';
			$where = "id IN (" . $count_all ['pricelist_ids'] . ")";
			$this->db->where ( $where );
			$this->db->select ( 'id,name' );
			$pricelist_res = $this->db->get ( 'pricelists' );
			$pricelist_res = $pricelist_res->result_array ();
			foreach ( $pricelist_res as $value ) {
				$pricelist_arr [$value ['id']] = $value ['name'];
			}
			$where = "id IN (" . $count_all ['accounts_ids'] . ")";
			$this->db->where ( $where );
			$this->db->select ( 'id,number,first_name,last_name' );
			$account_res = $this->db->get ( 'accounts' );
			foreach ( $account_res->result_array () as $value ) {
				$account_arr [$value ['id']] = $value ['first_name'] . " " . $value ['last_name'] . ' (' . $value ['number'] . ')';
			}
			
			if ($accountinfo ['type'] != 1) {
				$customer_array [] = array (
						gettext ( "Date" ),
						gettext ( "Caller ID" ),
						gettext ( "Called Number" ),
						gettext ( "Code" ),
						gettext ( "Destination" ),
						gettext ( "Duration" ),
						gettext ( "Debit" ) . "(" . $currency . ")",
						gettext ( "Cost" ) . "(" . $currency . ")",
						gettext ( "Disposition" ),
						gettext ( "Account" ),
						gettext ( "Trunk" ),
						gettext ( "Rate Group" ),
						gettext ( "Call Type" ) 
				);
				$where = "id IN (" . $count_all ['trunk_ids'] . ")";
				$this->db->where ( $where );
				$this->db->select ( 'id,name' );
				$trunk_res = $this->db->get ( 'trunks' );
				$trunk_res = $trunk_res->result_array ();
				foreach ( $trunk_res as $value ) {
					$trunk_arr [$value ['id']] = $value ['name'];
				}
				foreach ( $query->result_array () as $value ) {
					$duration = ($show_seconds == 'minutes') ? ($value ['billseconds'] > 0) ? floor ( $value ['billseconds'] / 60 ) . ":" . sprintf ( '%02d', $value ['billseconds'] % 60 ) : "00:00" : $value ['billseconds'];
					$account = isset ( $account_arr [$value ['accountid']] ) ? $account_arr [$value ['accountid']] : 'Anonymous';
					$customer_array [] = array (
							$this->common->convert_GMT_to ( '', '', $value ['callstart'] ),
							$value ['callerid'],
							$value ['callednum'],
							filter_var ( $value ['pattern'], FILTER_SANITIZE_NUMBER_INT ),
							$value ['notes'],
							$duration,
							$this->common->calculate_currency_manually ( $currency_info, $value ['debit'], false, false ),
							$this->common->calculate_currency_manually ( $currency_info, $value ['cost'], false, false ),
							$value ['disposition'],
							$account,
							isset ( $trunk_arr [$value ['trunk_id']] ) ? $trunk_arr [$value ['trunk_id']] : '',
							isset ( $pricelist_arr [$value ['pricelist_id']] ) ? $pricelist_arr [$value ['pricelist_id']] : '',
							$value ['calltype'] 
					);
				}
				$duration = ($show_seconds == 'minutes') ? ($count_all ['billseconds'] > 0) ? floor ( $count_all ['billseconds'] / 60 ) . ":" . sprintf ( '%02d', $count_all ['billseconds'] % 60 ) : "00:00" : $count_all ['billseconds'];
				$customer_array [] = array (
						"Grand Total",
						"",
						"",
						"",
						"",
						$duration,
						$this->common->calculate_currency_manually ( $currency_info, $count_all ['total_debit'], false, false ),
						$this->common->calculate_currency_manually ( $currency_info, $count_all ['total_cost'], false, false ),
						"",
						"",
						"",
						"",
						"",
						"",
						"",
						"" 
				);
			} else {
				$customer_array [] = array (
						gettext ( "Date" ),
						gettext ( "CallerID" ),
						gettext ( "Called Number" ),
						gettext ( "Code" ),
						gettext ( "Destination" ),
						gettext ( "Duration" ),
						gettext ( "Debit" ) . "(" . $currency . ")",
						gettext ( "Cost" ) . "(" . $currency . ")",
						gettext ( "Disposition" ),
						gettext ( "Account" ),
						gettext ( "Rate Group" ),
						gettext ( "Call Type" ) 
				);
				foreach ( $query->result_array () as $value ) {
					$duration = ($show_seconds == 'minutes') ? ($value ['billseconds'] > 0) ? floor ( $value ['billseconds'] / 60 ) . ":" . sprintf ( '%02d', $value ['billseconds'] % 60 ) : "00:00" : $value ['billseconds'];
					$account = isset ( $account_arr [$value ['accountid']] ) ? $account_arr [$value ['accountid']] : 'Anonymous';
					$customer_array [] = array (
							$this->common->convert_GMT_to ( '', '', $value ['callstart'] ),
							$value ['callerid'],
							$value ['callednum'],
							filter_var ( $value ['pattern'], FILTER_SANITIZE_NUMBER_INT ),
							$value ['notes'],
							$duration,
							$this->common->calculate_currency_manually ( $currency_info, $value ['debit'], false, false ),
							$this->common->calculate_currency_manually ( $currency_info, $value ['cost'], false, false ),
							$value ['disposition'],
							$account,
							isset ( $pricelist_arr [$value ['pricelist_id']] ) ? $pricelist_arr [$value ['pricelist_id']] : '',
							$value ['calltype'] 
					);
				}
				$duration = ($show_seconds == 'minutes') ? ($count_all ['billseconds'] > 0) ? floor ( $count_all ['billseconds'] / 60 ) . ":" . sprintf ( '%02d', $count_all ['billseconds'] % 60 ) : "00:00" : $count_all ['billseconds'];
				$customer_array [] = array (
						"Grand Total",
						"",
						"",
						"",
						"",
						$duration,
						$this->common->calculate_currency_manually ( $currency_info, $count_all ['total_debit'], false, false ),
						$this->common->calculate_currency_manually ( $currency_info, $count_all ['total_cost'], false, false ),
						"",
						"",
						"",
						"",
						"",
						"",
						"" 
				);
			}
		}
		$this->load->helper ( 'csv' );
		if (isset ( $customer_array )) {
			array_to_csv ( $customer_array, 'Customer_CDR_' . date ( "Y-m-d" ) . '.csv' );
		} else {
			$customer_array [] = array (
					gettext ( "Date" ),
					gettext ( "Caller ID" ),
					gettext ( "Called Number" ),
					gettext ( "Code" ),
					gettext ( "Destination" ),
					gettext ( "Duration" ),
					gettext ( "Debit" ) . "(" . $currency . ")",
					gettext ( "Cost" ) . "(" . $currency . ")",
					gettext ( "Disposition" ),
					gettext ( "Account" ),
					gettext ( "Rate Group" ),
					gettext ( "Call Type" ) 
			);
			array_to_csv ( $customer_array, 'Customer_CDR_' . date ( "Y-m-d" ) . '.csv' );
		}
	}
	
	/* * *************************** */
	function resellerReport() {
		$data ['page_title'] = gettext ( 'Resellers CDRs Report' );
		$data ['search_flag'] = true;
		$this->session->set_userdata ( 'advance_search', 0 );
		$data ['grid_fields'] = $this->reports_form->build_report_list_for_reseller ();
		$data ["grid_buttons"] = $this->reports_form->build_grid_buttons_reseller ();
		$data ['form_search'] = $this->form->build_serach_form ( $this->reports_form->get_reseller_cdr_form () );
		$this->load->view ( 'view_cdr_reseller_list', $data );
	}
	
	/**
	 * -------Here we write code for controller accounts functions account_list------
	 * Listing of Accounts table data through php function json_encode
	 */
	function resellerReport_json() {
		
		// Get Total Count,Total debit,Total cost information
		$count_res = $this->reports_model->getreseller_cdrs ( false, "", "" );
		$count_all = ( array ) $count_res->first_row ();
		$paging_data = $this->form->load_grid_config ( $count_all ['count'], $_GET ['rp'], $_GET ['page'] );
		$json_data = $paging_data ["json_paging"];
		$query = $this->reports_model->getreseller_cdrs ( true, $paging_data ["paging"] ["start"], $paging_data ["paging"] ["page_no"] );
		$grid_fields = json_decode ( $this->reports_form->build_report_list_for_reseller () );
		$json_data ['rows'] = $this->form->build_grid ( $query, $grid_fields );
		if ($count_all ['count'] > 0) {
			$search_arr = $this->session->userdata ( 'reseller_cdr_list_search' );
			$show_seconds = (! empty ( $search_arr ['search_in'] )) ? $search_arr ['search_in'] : 'minutes';
			$duration = ($show_seconds == 'minutes') ? ($count_all ['billseconds'] > 0) ? sprintf ( '%02d', $count_all ['billseconds'] / 60 ) . ":" . sprintf ( '%02d', $count_all ['billseconds'] % 60 ) : "00:00" : sprintf ( '%02d', $count_all ['billseconds'] );
			$json_data ['rows'] [] = array (
					"cell" => array (
							"<b>Grand Total</b>",
							"",
							"",
							"",
							"",
							"<b>$duration</b>",
							"<b>" . $this->common_model->calculate_currency ( $count_all ['total_debit'], '', '', true, false ) . "</b>",
							"<b>" . $this->common_model->calculate_currency ( $count_all ['total_cost'], '', '', true, false ) . "</b>",
							"",
							"",
							"",
							"",
							"",
							"",
							"",
							"" 
					) 
			);
		}
		echo json_encode ( $json_data );
	}
	function resellerReport_search() {
		$ajax_search = $this->input->post ( 'ajax_search', 0 );
		if ($this->input->post ( 'advance_search', TRUE ) == 1) {
			$this->session->set_userdata ( 'advance_search', $this->input->post ( 'advance_search' ) );
			$action = $this->input->post ();
			unset ( $action ['action'] );
			unset ( $action ['advance_search'] );
			$this->session->set_userdata ( 'reseller_cdr_list_search', $action );
		}
		if (@$ajax_search != 1) {
			redirect ( base_url () . 'reports/resellerReport/' );
		}
	}
	function resellerReport_clearsearchfilter() {
		$this->session->set_userdata ( 'advance_search', 0 );
		$this->session->set_userdata ( 'account_search', "" );
	}
	function resellerReport_export() {
		// Get All count related information.
		$account_info = $accountinfo = $this->session->userdata ( 'accountinfo' );
		$currency_id = $account_info ['currency_id'];
		$currency = $this->common->get_field_name ( 'currency', 'currency', $currency_id );
		$count_res = $this->reports_model->getreseller_cdrs ( false, "", "" );
		$count_all = ( array ) $count_res->first_row ();
		ob_clean ();
		$reseller_array [] = array (
				gettext ( "Date" ),
				gettext ( "Caller ID" ),
				gettext ( "Called Number" ),
				gettext ( "Code" ),
				gettext ( "Destination" ),
				gettext ( "Duration" ),
				gettext ( "Debit" ) . "(" . $currency . ")",
				gettext ( "Cost" ) . "(" . $currency . ")",
				gettext ( "Disposition" ),
				gettext ( "Account" ),
				gettext ( "Rate Group" ),
				gettext ( "Call Type" ) 
		);
		if ($count_all ['count'] > 0) {
			// Initialization of Rategroup array
			$pricelist_arr = array ();
			$query = $this->reports_model->getreseller_cdrs ( true, '', '', true );
			// Get Decimal points,system currency and user currency.
			$currency_info = $this->common->get_currency_info ();
			// Get search information from session.
			$search_arr = $this->session->userdata ( 'reseller_cdr_list_search' );
			$show_seconds = (! empty ( $search_arr ['search_in'] )) ? $search_arr ['search_in'] : 'minutes';
			$where = "id IN (" . $count_all ['pricelist_ids'] . ")";
			$this->db->where ( $where );
			$this->db->select ( 'id,name' );
			$pricelist_res = $this->db->get ( 'pricelists' );
			$pricelist_res = $pricelist_res->result_array ();
			foreach ( $pricelist_res as $value ) {
				$pricelist_arr [$value ['id']] = $value ['name'];
			}
			foreach ( $query->result_array () as $value ) {
				$duration = ($show_seconds == 'minutes') ? ($value ['billseconds'] > 0) ? sprintf ( '%02d', $value ['billseconds'] / 60 ) . ":" . sprintf ( '%02d', $value ['billseconds'] % 60 ) : "00:00" : $value ['billseconds'];
				$reseller_array [] = array (
						$this->common->convert_GMT_to ( '', '', $value ['callstart'] ),
						$value ['callerid'],
						$value ['callednum'],
						filter_var ( $value ['pattern'], FILTER_SANITIZE_NUMBER_INT ),
						$value ['notes'],
						$duration,
						$this->common->calculate_currency_manually ( $currency_info, $value ['debit'], false, false ),
						$this->common->calculate_currency_manually ( $currency_info, $value ['cost'], false, false ),
						$value ['disposition'],
						$this->common->build_concat_string ( "first_name,last_name,number", "accounts", $value ['accountid'] ),
						isset ( $pricelist_arr [$value ['pricelist_id']] ) ? $pricelist_arr [$value ['pricelist_id']] : '',
						$value ['calltype'] 
				);
			}
			$duration = ($show_seconds == 'minutes') ? ($count_all ['billseconds'] > 0) ? floor ( $count_all ['billseconds'] / 60 ) . ":" . sprintf ( '%02d', $count_all ['billseconds'] % 60 ) : "00:00" : $count_all ['billseconds'];
			$reseller_array [] = array (
					"Grand Total",
					"",
					"",
					"",
					"",
					$duration,
					$this->common->calculate_currency_manually ( $currency_info, $count_all ['total_debit'], false, false ),
					$this->common->calculate_currency_manually ( $currency_info, $count_all ['total_cost'], false, false ),
					"",
					"",
					"",
					"",
					"",
					"",
					"",
					"" 
			);
		}
		$this->load->helper ( 'csv' );
		array_to_csv ( $reseller_array, 'Reseller_CDR_' . date ( "Y-m-d" ) . '.csv' );
	}
	function providerReport() {
		$data ['page_title'] = gettext ( 'Provider CDRs Report' );
		$data ['search_flag'] = true;
		$this->session->set_userdata ( 'advance_search', 0 );
		$data ['grid_fields'] = $this->reports_form->build_report_list_for_provider ();
		$data ["grid_buttons"] = $this->reports_form->build_grid_buttons_provider ();
		$data ['form_search'] = $this->form->build_serach_form ( $this->reports_form->get_provider_cdr_form () );
		$this->load->view ( 'view_cdr_provider_list', $data );
	}
	function providerReport_json() {
		// Get All count related information.
		$count_res = $this->reports_model->getprovider_cdrs ( false, "", "" );
		$count_all = ( array ) $count_res->first_row ();
		$paging_data = $this->form->load_grid_config ( $count_all ['count'], $_GET ['rp'], $_GET ['page'] );
		$json_data = $paging_data ["json_paging"];
		
		$query = $this->reports_model->getprovider_cdrs ( true, $paging_data ["paging"] ["start"], $paging_data ["paging"] ["page_no"] );
		$grid_fields = json_decode ( $this->reports_form->build_report_list_for_provider () );
		$json_data ['rows'] = $this->form->build_grid ( $query, $grid_fields );
		if ($count_all ['count'] > 0) {
			$search_arr = $this->session->userdata ( 'provider_cdr_list_search' );
			$show_seconds = (! empty ( $search_arr ['search_in'] )) ? $search_arr ['search_in'] : 'minutes';
			$duration = ($show_seconds == 'minutes') ? ($count_all ['billseconds'] > 0) ? floor ( $count_all ['billseconds'] / 60 ) . ":" . sprintf ( "%02d", $count_all ['billseconds'] % 60 ) : "00:00" : $count_all ['billseconds'];
			$json_data ['rows'] [] = array (
					"cell" => array (
							"<b>Grand Total</b>",
							"",
							"",
							"",
							"",
							"<b>$duration</b>",
							"<b>" . $this->common_model->calculate_currency ( $count_all ['total_cost'], '', '', true, false ) . "</b>",
							"",
							"" 
					) 
			);
		}
		echo json_encode ( $json_data );
	}
	function providerReport_search() {
		$ajax_search = $this->input->post ( 'ajax_search', 0 );
		if ($this->input->post ( 'advance_search', TRUE ) == 1) {
			$this->session->set_userdata ( 'advance_search', $this->input->post ( 'advance_search' ) );
			$action = $this->input->post ();
			unset ( $action ['action'] );
			unset ( $action ['advance_search'] );
			$this->session->set_userdata ( 'provider_cdr_list_search', $action );
		}
		if (@$ajax_search != 1) {
			redirect ( base_url () . 'reports/providerReport/' );
		}
	}
	function providerReport_clearsearchfilter() {
		$this->session->set_userdata ( 'advance_search', 0 );
		$this->session->set_userdata ( 'account_search', "" );
	}
	function providerReport_export() {
		/*
		 * ASTPP 3.0
		 * Get All count information as well All total information
		 */
		$account_info = $accountinfo = $this->session->userdata ( 'accountinfo' );
		$currency_id = $account_info ['currency_id'];
		$currency = $this->common->get_field_name ( 'currency', 'currency', $currency_id );
		$count_res = $this->reports_model->getprovider_cdrs ( false, "", "" );
		$count_all = ( array ) $count_res->first_row ();
		ob_clean ();
		$provider_array [] = array (
				gettext ( "Date" ),
				gettext ( "Caller ID" ),
				gettext ( "Called Number" ),
				gettext ( "Code" ),
				gettext ( "Destination" ),
				gettext ( "Duration" ),
				gettext ( "Cost" ) . "(" . $currency . ")",
				gettext ( "Disposition" ),
				gettext ( "Account" ) 
		);
		if ($count_all ['count'] > 0) {
			$query = $this->reports_model->getprovider_cdrs ( true, '', '', true );
			// Get Decimal points,system currency and user currency.
			$currency_info = $this->common->get_currency_info ();
			// Get search information from session.
			$search_arr = $this->session->userdata ( 'provider_cdr_list_search' );
			$show_seconds = (! empty ( $search_arr ['search_in'] )) ? $search_arr ['search_in'] : 'minutes';
			foreach ( $query->result_array () as $value ) {
				// echo"<pre>";print_r($value);exit;
				$duration = ($show_seconds == 'minutes') ? ($value ['billseconds'] > 0) ? floor ( $value ['billseconds'] / 60 ) . ":" . sprintf ( "%02d", $value ['billseconds'] % 60 ) : "00:00" : $value ['billseconds'];
				$account_arr = $this->db_model->getSelect ( '*', 'accounts', array (
						'id' => $value ['provider_id'] 
				) );
				if ($account_arr->num_rows () > 0) {
					$account_array = $account_arr->result_array ();
					$account = $account_array [0] ['first_name'] . " " . $account_array [0] ['last_name'] . ' (' . $account_array [0] ['number'] . ')';
				} else {
					$account = "Anonymous";
				}
				$provider_array [] = array (
						$this->common->convert_GMT_to ( '', '', $value ['callstart'] ),
						$value ['callerid'],
						$value ['callednum'],
						filter_var ( $value ['pattern'], FILTER_SANITIZE_NUMBER_INT ),
						$value ['notes'],
						$duration,
						$this->common->calculate_currency_manually ( $currency_info, $value ['cost'], false, false ),
						$value ['disposition'],
						$account 
				);
			}
			$duration = ($show_seconds == 'minutes') ? ($count_all ['billseconds'] > 0) ? floor ( $count_all ['billseconds'] / 60 ) . ":" . sprintf ( "%02d", $count_all ['billseconds'] % 60 ) : "00:00" : $count_all ['billseconds'];
			$provider_array [] = array (
					"Grand Total",
					"",
					"",
					"",
					"",
					$duration,
					// $this->common->calculate_currency_manually($currency_info, $count_all['total_debit']),
					$this->common->calculate_currency_manually ( $currency_info, $count_all ['total_cost'], false, false ),
					"",
					"",
					"",
					"",
					"",
					"",
					"",
					"" 
			);
		}
		// echo "<pre>";print_r($provider_array);exit;
		$this->load->helper ( 'csv' );
		array_to_csv ( $provider_array, 'Provider_CDR_' . date ( "Y-m-d" ) . '.csv' );
	}
	
	/*
	 * ****
	 * ASTPP 3.0
	 * Payment to refill
	 * ****
	 */
	function user_refillreport() {
		$json_data = array ();
		$count_all = $this->reports_model->getuser_refill_list ( false, "", "" );
		$paging_data = $this->form->load_grid_config ( $count_all, $_GET ['rp'], $_GET ['page'] );
		$json_data = $paging_data ["json_paging"];
		
		$query = $this->reports_model->getuser_refill_list ( true, $paging_data ["paging"] ["start"], $paging_data ["paging"] ["page_no"] );
		$grid_fields = json_decode ( $this->reports_form->build_refill_report_for_user () );
		$json_data ['rows'] = $this->form->build_grid ( $query, $grid_fields );
		
		echo json_encode ( $json_data );
	}
	function user_refillreport_search() {
		$ajax_search = $this->input->post ( 'ajax_search', 0 );
		if ($this->input->post ( 'advance_search', TRUE ) == 1) {
			$this->session->set_userdata ( 'advance_search', $this->input->post ( 'advance_search' ) );
			$action = $this->input->post ();
			unset ( $action ['action'] );
			unset ( $action ['advance_search'] );
			$this->session->set_userdata ( 'cdr_refill_search', $action );
		}
		if (@$ajax_search != 1) {
			redirect ( base_url () . 'user/user_cdrs_report/' );
		}
	}
	function user_refillreport_clearsearchfilter() {
		$this->session->set_userdata ( 'advance_search', 0 );
		$this->session->set_userdata ( 'account_search', "" );
	}
	
	/* * ************************** */
	function customer_cdrreport($accountid, $accounttype) {
		$instant_search = $this->session->userdata ( 'left_panel_search_' . $accounttype . '_cdrs' );
		$instant_search_currency = $this->common_model->add_calculate_currency ( $instant_search, "", '', true, false );
		$like_str = ! empty ( $instant_search ) ? "(callstart like '%$instant_search%'  
                                            OR  callerid like '%$instant_search%'
                                            OR  callednum like '%$instant_search%' 
                                            OR  notes like '%$instant_search%'
                                            OR  disposition like '%$instant_search%' 
                                            OR  calltype like '%$instant_search%' 
                                            OR  debit like '%$instant_search_currency%' 
                                            OR  cost like '%$instant_search_currency%'  
                                                )" : null;
		
		$json_data = array ();
		if (! empty ( $like_str ))
			$this->db->where ( $like_str );
		$count_all = $this->reports_model->users_cdrs_list ( false, $accountid, $accounttype, "", "" );
		$paging_data = $this->form->load_grid_config ( $count_all, $_GET ['rp'], $_GET ['page'] );
		$json_data = $paging_data ["json_paging"];
		if (! empty ( $like_str ))
			$this->db->where ( $like_str );
		$query = $this->reports_model->users_cdrs_list ( true, $accountid, $accounttype, $paging_data ["paging"] ["start"], $paging_data ["paging"] ["page_no"] );
		$grid_fields = json_decode ( $this->reports_form->build_report_list_for_user ( $accounttype ) );
		$json_data ['rows'] = $this->form->build_grid ( $query, $grid_fields );
		echo json_encode ( $json_data );
	}
	
	/*
	 * ****
	 * ASTPP 3.0
	 * Payment to refill
	 * ****
	 */
	function refillreport() {
		$data ['page_title'] = gettext ( 'Refill Report' );
		$data ['search_flag'] = true;
		$this->session->set_userdata ( 'advance_search', 0 );
		$data ['grid_buttons'] = $this->reports_form->build_refillreport_buttons ();
		$data ['grid_fields'] = $this->reports_form->build_refill_report_for_admin ();
		$data ['form_search'] = $this->form->build_serach_form ( $this->reports_form->build_search_refill_report_for_admin () );
		$this->load->view ( 'view_refill_report', $data );
	}
	function refillreport_json() {
		$json_data = array ();
		$count_all = $this->reports_model->get_refill_list ( false, "", "" );
		$paging_data = $this->form->load_grid_config ( $count_all, $_GET ['rp'], $_GET ['page'] );
		$json_data = $paging_data ["json_paging"];
		$query = $this->reports_model->get_refill_list ( true, $paging_data ["paging"] ["start"], $paging_data ["paging"] ["page_no"] );
		$grid_fields = json_decode ( $this->reports_form->build_refill_report_for_admin () );
		$json_data ['rows'] = $this->form->build_grid ( $query, $grid_fields );
		echo json_encode ( $json_data );
	}
	function refillreport_export() {
		$account_info = $accountinfo = $this->session->userdata ( 'accountinfo' );
		$currency_id = $account_info ['currency_id'];
		$reseller_id = $accountinfo ['reseller_id'] > 0 ? $accountinfo ['reseller_id'] : 0;
		$account_arr = array ();
		$currency_info = $this->common->get_currency_info ();
		$currency = $this->common->get_field_name ( 'currency', 'currency', $currency_id );
		ob_clean ();
		$customer_array [] = array (
				gettext ( "Date" ),
				gettext ( "Account" ),
				gettext ( "Amount" ) . "(" . $currency . ")",
				gettext ( "Refill By" ),
				gettext ( "Note" ) 
		);
		$query = $this->reports_model->get_refill_list ( true, "", "", true );
		$this->db->select ( "concat(first_name,' ',last_name,' ','(',number,')') as first_name,id", false );
		$this->db->where ( 'reseller_id', $reseller_id );
		$this->db->where_not_in ( 'type', array (
				"-1,2,4" 
		) );
		$account_res = $this->db->get ( 'accounts' );
		if ($account_res->num_rows () > 0) {
			$account_res = $account_res->result_array ();
			foreach ( $account_res as $key => $value ) {
				$account_arr [$value ['id']] = $value ['first_name'];
			}
		}
		if ($query->num_rows () > 0) {
			foreach ( $query->result_array () as $row ) {
				$customer_array [] = array (
						$row ['payment_date'],
						isset ( $account_arr [$row ['accountid']] ) ? $account_arr [$row ['accountid']] : 'Anonymous',
						number_format ( ( float ) (($row ['credit'] * $currency_info ['user_currency'] ['currencyrate']) / $currency_info ['base_currency'] ['currencyrate']), $currency_info ['decimalpoints'], ".", "" ),
						$this->common->get_refill_by ( "", "", $row ['payment_by'] ),
						$row ['notes'] 
				);
			}
		}
		$this->load->helper ( 'csv' );
		array_to_csv ( $customer_array, 'Refill_Report_' . date ( "Y-m-d" ) . '.csv' );
	}
	function customer_refillreport_search() {
		$ajax_search = $this->input->post ( 'ajax_search', 0 );
		if ($this->input->post ( 'advance_search', TRUE ) == 1) {
			$this->session->set_userdata ( 'advance_search', $this->input->post ( 'advance_search' ) );
			$action = $this->input->post ();
			unset ( $action ['action'] );
			unset ( $action ['advance_search'] );
			$this->session->set_userdata ( 'cdr_refill_search', $action );
		}
		if (@$ajax_search != 1) {
			redirect ( base_url () . 'user/user_cdrs_report/' );
		}
	}
	function customer_refillreport_clearsearchfilter() {
		$this->session->set_userdata ( 'advance_search', 0 );
		$this->session->set_userdata ( 'account_search', "" );
	}
	
	/**
	 * ******
	 * ASTPP 3.0
	 * Charge History
	 * ******
	 */
	function charges_history() {
		$data ['page_title'] = gettext ( 'Charges History' );
		$data ['search_flag'] = true;
		$this->session->set_userdata ( 'advance_search', 0 );
		$data ['grid_fields'] = $this->reports_form->build_charge_list_for_admin ();
		$data ['form_search'] = $this->form->build_serach_form ( $this->reports_form->get_charges_search_form () );
		$this->load->view ( 'view_charges_list', $data );
	}
	function charges_history_json() {
		$json_data = array ();
		$count_all = $this->reports_model->getcharges_list ( false );
		$paging_data = $this->form->load_grid_config ( $count_all, $_GET ['rp'], $_GET ['page'] );
		$json_data = $paging_data ["json_paging"];
		$query = $this->reports_model->getcharges_list ( true, $paging_data ["paging"] ["start"], $paging_data ["paging"] ["page_no"] );
		$result = $query->result_array ();
		$query1 = $this->reports_model->getcharges_list ( true, '', '' );
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
			if ($this->session->userdata ( 'logintype' ) == 1) {
				$json_data ['rows'] [] = array (
						'cell' => array (
								$date,
								$invoice_num,
								$account,
								// $reseller,
								$item_type,
								$before_balance,
								$debit,
								$credit,
								$after_balance,
								$description 
						) 
				);
			} else {
				$json_data ['rows'] [] = array (
						'cell' => array (
								$date,
								$invoice_num,
								$account,
								$item_type,
								$before_balance,
								$debit,
								$credit,
								$after_balance,
								$description 
						) 
				);
			}
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
		if ($this->session->userdata ( 'logintype' ) == 1) {
			$json_data ['rows'] [$count_all] ['cell'] = array (
					'<b>Total</b>',
					'-',
					'-',
					'-',
					'-',
					'<b>' . $this->common->convert_to_currency ( '', '', $debit_sum ) . '</b>',
					'<b>' . $this->common->convert_to_currency ( '', '', $credit_sum ) . '</b>',
					'-',
					'-' 
			);
		} else {
			$json_data ['rows'] [$count_all] ['cell'] = array (
					'<b>Total</b>',
					'-',
					'-',
					'-',
					'-',
					'<b>' . $this->common->convert_to_currency ( '', '', $debit_sum ) . '</b>',
					'<b>' . $this->common->convert_to_currency ( '', '', $credit_sum ) . '</b>',
					'-',
					'-' 
			);
		}
		echo json_encode ( $json_data );
	}
	function charges_history_search() {
		$ajax_search = $this->input->post ( 'ajax_search', 0 );
		if ($this->input->post ( 'advance_search', TRUE ) == 1) {
			$this->session->set_userdata ( 'advance_search', $this->input->post ( 'advance_search' ) );
			$action = $this->input->post ();
			unset ( $action ['action'] );
			unset ( $action ['advance_search'] );
			$this->session->set_userdata ( 'charges_list_search', $action );
		}
		if (@$ajax_search != 1) {
			redirect ( base_url () . 'accounts/admin_list/' );
		}
	}
	function charges_history_clearsearchfilter() {
		$this->session->set_userdata ( 'advance_search', 0 );
		$this->session->set_userdata ( 'charges_list_search', "" );
	}
	/* * ******************************** */
	/*
	 * ASTPP 3.0
	 * This function using for customer edit
	 */
	function customer_charge_history($accountid, $accounttype) {
		$json_data = array ();
		$instant_search = $this->session->userdata ( 'left_panel_search_' . $accounttype . '_charges' );
		$like_str = ! empty ( $instant_search ) ? "(created_date like '%$instant_search%'  
                                            OR  item_type like '%$instant_search%'
                                            OR  'debit' like '%$instant_search%'
                                            OR 'credit' like '%$instant_search%'
                                            OR  'description' like '%$instant_search%')" : null;
		if (! empty ( $like_str ))
			$this->db->where ( $like_str );
		$count_all = $this->reports_model->get_customer_charge_list ( false, $accountid );
		
		$paging_data = $this->form->load_grid_config ( $count_all, $_GET ['rp'], $_GET ['page'] );
		$json_data = $paging_data ["json_paging"];
		if (! empty ( $like_str ))
			$this->db->where ( $like_str );
		$query = $this->reports_model->get_customer_charge_list ( true, $accountid, $paging_data ["paging"] ["start"], $paging_data ["paging"] ["page_no"] );
		$result = $query->result_array ();
		$query1 = $this->reports_model->get_customer_charge_list ( true, $accountid, '', '' );
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
			if ($this->session->userdata ( 'logintype' ) == 1) {
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
			} else {
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
		if ($this->session->userdata ( 'logintype' ) == 1) {
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
		} else {
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
		}
		echo json_encode ( $json_data );
	}
	function customer_refillreport($accountid, $accounttype) {
		$json_data = array ();
		$instant_search = $this->session->userdata ( 'left_panel_search_' . $accounttype . '_refill' );
		$like_str = ! empty ( $instant_search ) ? "(payment_date like '%$instant_search%'
                                            OR  credit like '%$instant_search%'
                                            OR  payment_by like '%$instant_search%'
                                            OR  notes like '%$instant_search%'
                                                )" : null;
		if (! empty ( $like_str ))
			$this->db->where ( $like_str );
		$count_all = $this->reports_model->get_customer_refillreport ( false, $accountid );
		$paging_data = $this->form->load_grid_config ( $count_all, $_GET ['rp'], $_GET ['page'] );
		$json_data = $paging_data ["json_paging"];
		if (! empty ( $like_str ))
			$this->db->where ( $like_str );
		$query = $this->reports_model->get_customer_refillreport ( true, $accountid, $paging_data ["paging"] ["start"], $paging_data ["paging"] ["page_no"] );
		$grid_fields = json_decode ( $this->reports_form->build_refillreport_for_customer () );
		$json_data ['rows'] = $this->form->build_grid ( $query, $grid_fields );
		echo json_encode ( $json_data );
	}
/**
 * ********************************************************
 */
}
?>
 
