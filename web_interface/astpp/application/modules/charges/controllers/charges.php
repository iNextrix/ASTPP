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
class Charges extends MX_Controller {
	function Charges() {
		parent::__construct ();
		
		$this->load->helper ( 'template_inheritance' );
		
		$this->load->library ( 'session' );
		$this->load->library ( "charges_form" );
		$this->load->library ( 'astpp/form' );
		$this->load->library ( 'astpp/permission' );
		$this->load->model ( 'charges_model' );
		
		if ($this->session->userdata ( 'user_login' ) == FALSE)
			redirect ( base_url () . '/astpp/login' );
	}
	function periodiccharges_add() {
		$data ['username'] = $this->session->userdata ( 'user_name' );
		$data ['flag'] = 'create';
		$data ['page_title'] = gettext ( 'Create Subscription' );
		$data ['form'] = $this->form->build_form ( $this->charges_form->get_charge_form_fields (), '' );
		$this->load->view ( 'view_periodiccharges_add_edit', $data );
	}
	function periodiccharges_edit($edit_id = '') {
		$this->permission->check_web_record_permission($edit_id,'charges',"charges/periodiccharges/");
		$data ['page_title'] = gettext ( 'Edit Subscription' );
		$where = array (
				'id' => $edit_id
		);
		$charges_info = $this->db_model->getSelect ( "*", "charges", $where );
		$edit_data =(array)$charges_info->first_row();
		$edit_data ['charge'] = $this->common_model->to_calculate_currency ( $edit_data ['charge'], '', '', true, false );
		$data ['form'] = $this->form->build_form ( $this->charges_form->get_charge_form_fields (), $edit_data );
		$this->load->view ( 'view_periodiccharges_add_edit', $data );
	}
	function periodiccharges_save() {
		$add_array = $this->input->post ();
		$data ['form'] = $this->form->build_form ( $this->charges_form->get_charge_form_fields (), $add_array );
		if ($add_array ['id'] != '') {
			$data ['page_title'] = gettext ( 'Edit Subscription' );
			if ($this->form_validation->run () == FALSE) {
				$data ['validation_errors'] = validation_errors ();
				echo $data ['validation_errors'];
				exit ();
			} else {
				if ($add_array ['pricelist_id'] == '') {
					$add_array ['pricelist_id'] = '0';
				}
				$add_array ['charge'] = $this->common_model->add_calculate_currency ( $add_array ['charge'], '', '', false, false );
				$this->charges_model->edit_charge ( $add_array, $add_array ['id'] );
				
				if ($add_array ['pricelist_id'] > 0) {
					$this->charges_model->add_account_charges ( $add_array ['pricelist_id'], $add_array ['id'], true );
				}
				echo json_encode ( array (
						"SUCCESS" => $add_array ["description"] . " subscription updated successfully!" 
				) );
				exit ();
			}
		} else {
			$data ['page_title'] = gettext ( 'Add Subscription' );
			if ($this->form_validation->run () == FALSE) {
				$data ['validation_errors'] = validation_errors ();
				echo $data ['validation_errors'];
				exit ();
			} else {
				$add_array ['charge'] = $this->common_model->add_calculate_currency ( $add_array ['charge'], '', '', false, false );
				$charge_id = $this->charges_model->add_charge ( $add_array );
				if ($add_array ['pricelist_id'] > 0 && $charge_id > 0) {
					$this->charges_model->add_account_charges ( $add_array ['pricelist_id'], $charge_id, false );
				}
				echo json_encode ( array (
						"SUCCESS" => $add_array ["description"] . " subscription added successfully!" 
				) );
				exit ();
			}
		}
	}
	
	// /*ASTPP_invoice_changes_05_05_start*/
	function periodiccharges_delete($id) {
		$this->permission->check_web_record_permission($id,'charges',"charges/periodiccharges/");
		$this->charges_model->remove_charge ( $id );
		
		require_once (APPPATH . 'controllers/ProcessCharges.php');
		$ProcessCharges = new ProcessCharges ();
		
		$Params = array (
				"ChargeID" => $id 
		);
		$ProcessCharges->BillAccountCharges ( "SUBSCRIPTION", $Params );
		
		$this->session->set_flashdata ( 'astpp_notification', 'Subscription removed successfully!' );
		redirect ( base_url () . 'charges/periodiccharges/' );
	}
	// end
	function periodiccharges_search() {
		$ajax_search = $this->input->post ( 'ajax_search', 0 );
		if ($this->input->post ( 'advance_search', TRUE ) == 1) {
			$this->session->set_userdata ( 'advance_search', $this->input->post ( 'advance_search' ) );
			$action = $this->input->post ();
			unset ( $action ['action'] );
			unset ( $action ['advance_search'] );
			if (isset ( $action ['charge'] ['charge'] ) && $action ['charge'] ['charge'] != '') {
				$action ['charge'] ['charge'] = $this->common_model->add_calculate_currency ( $action ['charge'] ['charge'], "", '', true, false );
			}
			$this->session->set_userdata ( 'charges_list_search', $action );
		}
		if (@$ajax_search != 1) {
			redirect ( base_url () . 'charges/periodiccharges/' );
		}
	}
	function periodiccharges_clearsearchfilter() {
		$this->session->set_userdata ( 'advance_search', 0 );
		$this->session->set_userdata ( 'charges_list_search', "" );
	}
	function periodiccharges() {
		$data ['username'] = $this->session->userdata ( 'user_name' );
		$data ['page_title'] = gettext ( 'Subscriptions' );
		$data ['search_flag'] = true;
		$this->session->set_userdata ( 'advance_search', 0 );
		$data ['grid_fields'] = $this->charges_form->build_charge_list_for_admin ();
		$data ["grid_buttons"] = $this->charges_form->build_grid_buttons ();
		$data ['form_search'] = $this->form->build_serach_form ( $this->charges_form->get_charges_search_form () );
		
		$this->load->view ( 'view_charges_list', $data );
	}
	
	/**
	 * -------Here we write code for controller accounts functions account_list------
	 * Listing of Accounts table data through php function json_encode
	 */
	function periodiccharges_json() {
		$json_data = array ();
		$count_all = $this->charges_model->getcharges_list ( false );
		$paging_data = $this->form->load_grid_config ( $count_all, $_GET ['rp'], $_GET ['page'] );
		$json_data = $paging_data ["json_paging"];
		$query = $this->charges_model->getcharges_list ( true, $paging_data ["paging"] ["start"], $paging_data ["paging"] ["page_no"] );
		$grid_fields = json_decode ( $this->charges_form->build_charge_list_for_admin () );
		$json_data ['rows'] = $this->form->build_grid ( $query, $grid_fields );
		
		echo json_encode ( $json_data );
	}
	// /*ASTPP_invoice_changes_05_05_start*/
	function periodiccharges_delete_multiple() {
		$ids = $this->input->post ( "selected_ids", true );
		
		$charge_where = "charge_id IN(" . $ids . ")";
		$this->db->delete ( 'charge_to_account', $charge_where );
		
		$where = "id IN ($ids)";
		$this->db->delete ( "charges", $where );
		
		echo "SUBSCRIPTION";
	}
	// end
	function customer_charge_list($accountid, $accounttype) {
		$json_data = array ();
		
		$select = "charge_to_account.id,charges.description,charges.charge,charges.sweep_id,charges.pro_rate,charges.creation_date,charges.last_modified_date";
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
				'accounts.id' => $accountid 
		);
		$order_type = 'id';
		$order_by = "ASC";
		$instant_search = $this->session->userdata ( 'left_panel_search_' . $accounttype . '_subscription' );
		$like_str = ! empty ( $instant_search ) ? "(charges.description like '%$instant_search%'  
                                            OR  charges.charge like '%$instant_search%'
                                            OR  charges.charge like '%$instant_search%' 
                                                )" : null;
		if (! empty ( $like_str ))
			$this->db->where ( $like_str );
		$count_all = $this->db_model->getCountWithJion ( $table, $select, $where, $jionTable, $jionCondition, $type );
		$paging_data = $this->form->load_grid_config ( $count_all, $_GET ['rp'], $_GET ['page'] );
		$json_data = $paging_data ["json_paging"];
		if (! empty ( $like_str ))
			$this->db->where ( $like_str );
		$account_charge_list = $this->db_model->getAllJionQuery ( $table, $select, $where, $jionTable, $jionCondition, $type, $paging_data ["paging"] ["page_no"], $paging_data ["paging"] ["start"], $order_by, $order_type, "" );
		$grid_fields = json_decode ( $this->charges_form->build_charges_list_for_customer ( $accountid, $accounttype ) );
		$json_data ['rows'] = $this->form->build_grid ( $account_charge_list, $grid_fields );
		
		echo json_encode ( $json_data );
	}
}

?>
 
