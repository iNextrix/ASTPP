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
class DID extends MX_Controller {
	function DID() {
		parent::__construct ();
		
		$this->load->helper ( 'template_inheritance' );
		$this->load->library ( 'session' );
		$this->load->library ( 'did_form' );
		$this->load->library ( 'astpp/form' );
		$this->load->library ( 'astpp/permission' );
		$this->load->model ( 'did_model' );
		$this->load->library ( 'csvreader' );
		$this->load->library ( 'did_lib' );
		
		if ($this->session->userdata ( 'user_login' ) == FALSE)
			redirect ( base_url () . '/astpp/login' );
	}
	function did_add() {
		$data ['country_id'] = Common_model::$global_config ['system_config'] ['country'];
		$data ['page_title'] = gettext ( 'Create' ) . ' DID';
		$data ['form'] = $this->form->build_form ( $this->did_form->get_dids_form_fields (), '' );
		$data ['country_id'] = Common_model::$global_config ['system_config'] ['country'];
		
		if (! $data ['country_id']) {
			$data ['country_id'] = 1;
		}
		$data ['form'] = $this->form->build_form ( $this->did_form->get_dids_form_fields ( '', '', '', $data ['country_id'] ), '' );
		
		$this->load->view ( 'view_did_add_edit', $data );
	}
	function did_edit($edit_id = '') {
		$this->permission->check_web_record_permission('','','did/did_list/',true);
		$data ['page_title'] = gettext ( 'Edit DID' );
		$where = array (
				'id' => $edit_id 
		);
		$account = $this->db_model->getSelect ( "*", "dids", $where );
		foreach ( $account->result_array () as $value ) {
			$edit_data = $value;
		}
		/**
		 * ASTPP 3.0
		 * In DID Edit Country Field not change
		 */
		$data ['country_id'] = $edit_data ['country_id'];
		if ($edit_data ['country_id'] == "") {
			$data ['country_id'] = Common_model::$global_config ['system_config'] ['country'];
		}
		/* * ************************************************* */
		if (! $data ['country_id']) {
			$data ['country_id'] = 1;
		}
		$edit_data ['setup'] = $this->common_model->to_calculate_currency ( $edit_data ['setup'], '', '', false, false );
		$edit_data ['monthlycost'] = $this->common_model->to_calculate_currency ( $edit_data ['monthlycost'], '', '', false, false );
		$edit_data ['connectcost'] = $this->common_model->to_calculate_currency ( $edit_data ['connectcost'], '', '', false, false );
		$edit_data ['cost'] = $this->common_model->to_calculate_currency ( $edit_data ['cost'], '', '', false, false );
		$parent_id = $edit_data ['parent_id'];
		$account_id = $edit_data ['accountid'];
		if ($parent_id > 0) {
			$data ['form'] = $this->form->build_form ( $this->did_form->get_dids_form_fields ( $edit_id, $parent_id, $account_id ), $edit_data );
		} else {
			$data ['form'] = $this->form->build_form ( $this->did_form->get_dids_form_fields ( $edit_id, '', $account_id ), $edit_data );
		}
		$this->load->view ( 'view_did_add_edit', $data );
	}
	function did_save() {
		$add_array = $this->input->post ();
		$parent_id = isset ( $add_array ['parent_id'] ) && $add_array ['parent_id'] > 0 ? $add_array ['parent_id'] : '';
		$accountid = isset ( $add_array ['accountid'] ) && $add_array ['accountid'] > 0 ? $add_array ['accountid'] : '';
		$data ['form'] = $this->form->build_form ( $this->did_form->get_dids_form_fields ( $add_array ['id'], $parent_id, $accountid ), $add_array );
		if ($add_array ['id'] != '') {
			$data ['page_title'] = gettext ( 'Edit DID' );
			if ($this->form_validation->run () == FALSE) {
				$data ['validation_errors'] = validation_errors ();
				echo $data ['validation_errors'];
				exit ();
			} else {
				$this->db->where ( 'id', $add_array ['id'] );
				$this->db->select ( 'number' );
				$did_info = ( array ) $this->db->get ( 'dids' )->first_row ();
				$number = $did_info ['number'];
				// unset($add_array['number']);
				$add_array ['accountid'] = isset ( $add_array ['accountid'] ) ? $add_array ['accountid'] : 0;
				$add_array ['setup'] = $this->common_model->add_calculate_currency ( $add_array ['setup'], '', '', false, false );
				$add_array ['monthlycost'] = $this->common_model->add_calculate_currency ( $add_array ['monthlycost'], '', '', false, false );
				$add_array ['connectcost'] = $this->common_model->add_calculate_currency ( $add_array ['connectcost'], '', '', false, false );
				$add_array ['cost'] = $this->common_model->add_calculate_currency ( $add_array ['cost'], '', '', false, false );
				$this->did_model->edit_did ( $add_array, $add_array ['id'], $number );
				echo json_encode ( array (
						"SUCCESS" => $number . " DID Updated Successfully!" 
				) );
				exit ();
			}
		} else {
			$data ['page_title'] = gettext ( 'Add DID' );
			if ($this->form_validation->run () == FALSE) {
				$data ['validation_errors'] = validation_errors ();
				echo $data ['validation_errors'];
				exit ();
			} else {
				$check_did_number = $this->did_model->check_unique_did ( $add_array ['number'] );
				if ($check_did_number > 0) {
					echo json_encode ( array (
							"number_error" => "Number already exist in system." 
					) );
					exit ();
				}
				$add_array ['setup'] = $this->common_model->add_calculate_currency ( $add_array ['setup'], '', '', false, false );
				$add_array ['monthlycost'] = $this->common_model->add_calculate_currency ( $add_array ['monthlycost'], '', '', false, false );
				$add_array ['connectcost'] = $this->common_model->add_calculate_currency ( $add_array ['connectcost'], '', '', false, false );
				$add_array ['cost'] = $this->common_model->add_calculate_currency ( $add_array ['cost'], '', '', false, false );
				$add_array ['accountid'] = isset ( $add_array ['accountid'] ) ? $add_array ['accountid'] : 0;
				$this->did_model->add_did ( $add_array );
				echo json_encode ( array (
						"SUCCESS" => $add_array ["number"] . " DID Added Successfully!" 
				) );
				exit ();				
			}
		}
	}
	// /*ASTPP_invoice_changes_05_05_start*/
	function did_remove($id) {
		$this->permission->check_web_record_permission('','','did/did_list/',true);
		require_once (APPPATH . 'controllers/ProcessCharges.php');
		$ProcessCharges = new ProcessCharges ();
		$Params = array (
				"DIDid" => $id 
		);
		$ProcessCharges->BillAccountCharges ( "DIDs", $Params );
		
		$this->did_model->remove_did ( $id );
		$this->session->set_flashdata ( 'astpp_notification', 'DID Removed Successfully!' );
		redirect ( base_url () . 'did/did_list/' );
	}
	// end
	
	/*
	 * ASTPP 3.0
	 * function Spelling change.
	 */
	function did_list_release($id) {
		$accountinfo = $this->session->userdata ( 'accountinfo' );
		$this->db->where ( 'id', $id );
		$this->db->select ( 'parent_id,accountid,number' );
		$did_info = ( array ) $this->db->get ( 'dids' )->first_row ();
		if ($did_info ['parent_id'] > 0) {
			$str = $this->common->get_parent_info ( $did_info ['parent_id'], $accountinfo ['id'] );
			$str = rtrim ( $str, "," );
			$account_result = ( array ) $this->db->get_where ( 'accounts', "id IN (" . $str . ")" )->result_array ();
			foreach ( $account_result as $key => $acc_row ) {
				$acc_row ['did_number'] = $did_info ['number'];
				$this->common->mail_to_users ( 'email_remove_did', $acc_row );
			}
		}
		if ($accountinfo ['type'] == - 1) {
			// /*ASTPP_invoice_changes_05_05_start*/
			$update_array = array (
					'parent_id' => 0,
					'accountid' => 0,
					'assign_date' => '0000-00-00 00:00:00',
					"charge_upto" => "0000-00-00 00:00:00",
					"last_modified_date" => gmdate ( "Y-m-d H:i:s" ),
					'call_type' => '-1',
					'extensions' => '' 
			);
			// end
			$where = array (
					'id' => $id 
			);
			$this->db->where ( $where );
			$this->db->update ( 'dids', $update_array );
			if ($did_info ['parent_id'] > 0) {
				$this->db->where ( 'note', $did_info ['number'] );
				$this->db->delete ( "reseller_pricing" );
			}
		} else {
			$reseller_ids = $this->common->get_subreseller_info ( $accountinfo ['id'] );
			$reseller_ids = rtrim ( $reseller_ids, "," );
			$where = "parent_id IN ($reseller_ids)";
			$this->db->where ( 'note', $did_info ['number'] );
			$this->db->delete ( 'reseller_pricing', $where );
		}
		// /*ASTPP_invoice_changes_05_05_start*/
		if ($accountinfo ['type'] == 1) {
			$update_array = array (
					'parent_id' => $accountinfo ['id'],
					'accountid' => 0,
					'assign_date' => '0000-00-00 00:00:00',
					"charge_upto" => "0000-00-00 00:00:00",
					"last_modified_date" => gmdate ( "Y-m-d H:i:s" ),
					'call_type' => '-1',
					'extensions' => '' 
			);
		} else {
			$update_array = array (
					'parent_id' => 0,
					'accountid' => 0,
					'assign_date' => '0000-00-00 00:00:00',
					"charge_upto" => "0000-00-00 00:00:00",
					"last_modified_date" => gmdate ( "Y-m-d H:i:s" ),
					'call_type' => '-1',
					'extensions' => '' 
			);
		}
		// end
		$where = array (
				'id' => $id 
		);
		$this->db->where ( $where );
		$this->db->update ( 'dids', $update_array );
		$accountid = $did_info ['accountid'] > 0 ? $did_info ['accountid'] : 0;
		if ($did_info ['accountid'] > 0) {
			$email_user_id = $did_info ['accountid'];
		} elseif ($did_info ['parent_id'] > 0) {
			$email_user_id = $did_info ['parent_id'];
		}
		$accountinfo = ( array ) $this->db->get_where ( 'accounts', array (
				"id" => $email_user_id 
		) )->first_row ();
		$accountinfo ['did_number'] = $did_info ['number'];
		// /*ASTPP_invoice_changes_05_05_start*/
		require_once (APPPATH . 'controllers/ProcessCharges.php');
		$ProcessCharges = new ProcessCharges ();
		$Params = array (
				"DIDid" => $id 
		);
		$ProcessCharges->BillAccountCharges ( "DIDs", $Params );
		// end
		$this->common->mail_to_users ( 'email_remove_did', $accountinfo );
		$this->session->set_flashdata ( 'astpp_errormsg', 'DID Released Successfully!' );
		redirect ( base_url () . 'did/did_list/' );
	}
	function did_list() {
		$data ['app_name'] = 'ASTPP - Open Source Billing Solution | Manage DIDs | DIDS';
		$data ['username'] = $this->session->userdata ( 'user_name' );
		$data ['page_title'] = gettext ( 'DIDs' );
		$data ['search_flag'] = true;
		$this->session->set_userdata ( 'did_search', 0 );
		if ($this->session->userdata ( 'logintype' ) == 2) {
			$data ["grid_buttons"] = $this->did_form->build_grid_buttons ();
		} else {
			$data ["grid_buttons"] = json_encode ( array () );
		}
		
		if ($this->session->userdata ['userlevel_logintype'] == '1') {
			$drp_list = array ();
			$accountinfo = $this->session->userdata ( 'accountinfo' );
			if ($accountinfo ['reseller_id'] > 0) {
				$dids_array = $this->db->query ( "SELECT a.id AS id,a.number as number, b.monthlycost, b.setup FROM dids AS a, reseller_pricing AS b WHERE a.number = b.note AND b.reseller_id = " . $accountinfo ['reseller_id'] . " AND a.parent_id =" . $accountinfo ['reseller_id'] )->result_array ();
			} else {
				$this->db->select ( 'id,monthlycost,setup,number' );
				$this->db->where ( 'accountid', 0 );
				$this->db->where ( 'parent_id', 0 );
				$dids_array = $this->db->get ( 'dids' )->result_array ();
			}
			if (! empty ( $dids_array )) {
				foreach ( $dids_array as $drp_value ) {
					if (! empty ( $drp_value ['monthlycost'] ) && $drp_value ['monthlycost'] != 0) {
						$did_cost = $this->common_model->to_calculate_currency ( $drp_value ['monthlycost'], '', '', true, false );
					} else {
						$did_cost = 0;
					}
					if (! empty ( $drp_value ['setup'] ) && $drp_value ['setup'] != 0) {
						$did_setup = $this->common_model->to_calculate_currency ( $drp_value ['setup'], '', '', true, false );
					} else {
						$did_setup = 0;
					}
					$drp_list [$drp_value ['id']] = $drp_value ['number'] . ' ( Setup : ' . $did_setup . ')' . '( Monthly : ' . $did_cost . ' )';
					/* * ********************************************************************************************* */
				}
			}
			$data ['didlist'] = form_dropdown_all ( array (
					"name" => "free_did_list",
					"id" => "free_did_list",
					"class" => "did_dropdown" 
			), $drp_list, '' );
		}
		if ($this->session->userdata ['userlevel_logintype'] == '1') {
			$data ['grid_fields'] = $this->did_form->build_did_list_for_reseller_login ();
			$data ['form_search'] = $this->form->build_serach_form ( $this->did_form->get_search_did_form_for_reseller () );
		} else {
			$data ['grid_fields'] = $this->did_form->build_did_list_for_admin ();
			$data ['form_search'] = $this->form->build_serach_form ( $this->did_form->get_search_did_form () );
		}
		$this->load->view ( 'view_did_list', $data );
	}
	
	/**
	 * -------Here we write code for controller accounts functions account_list------
	 * Listing of Accounts table data through php function json_encode
	 */
	function did_list_json() {
		$json_data = array ();
		
		$count_all = $this->did_model->getdid_list ( false );
		$paging_data = $this->form->load_grid_config ( $count_all, $_GET ['rp'], $_GET ['page'] );
		$json_data = $paging_data ["json_paging"];
		$list = $this->session->userdata ['userlevel_logintype'] == 1 ? $this->did_form->build_did_list_for_reseller_login () : $this->did_form->build_did_list_for_admin ();
		$query = $this->did_model->getdid_list ( true, $paging_data ["paging"] ["start"], $paging_data ["paging"] ["page_no"] );
		$grid_fields = json_decode ( $list );
		$json_data ['rows'] = $this->form->build_grid ( $query, $grid_fields );
		
		echo json_encode ( $json_data );
	}
	function did_list_search() {
		$ajax_search = $this->input->post ( 'ajax_search', 0 );
		if ($this->input->post ( 'advance_search', TRUE ) == 1) {
			$this->session->set_userdata ( 'advance_search', $this->input->post ( 'advance_search' ) );
			$action = $this->input->post ();
			unset ( $action ['action'] );
			unset ( $action ['advance_search'] );
			$this->session->set_userdata ( 'did_list_search', $action );
		}
		if (@$ajax_search != 1) {
			redirect ( base_url () . 'did/did_list/' );
		}
	}
	function did_list_clearsearchfilter() {
		$this->session->set_userdata ( 'advance_search', 0 );
		$this->session->set_userdata ( 'did_search', "" );
	}
	function reseller_did($accountid, $accounttype) {
		$json_data = array ();
		$account_query = $this->db_model->getSelect ( "*", "accounts", array (
				"id" => $accountid 
		) );
		$account_arr = $account_query->result_array ();
		
		$this->db->where ( "reseller_id", $accountid );
		$this->db->select ( 'id' );
		$query = $this->db->get ( 'accounts' );
		$data = $query->result_array ();
		
		$count_all = $this->db_model->countQuery ( "*", "reseller_pricing", array (
				"reseller_id" => $accountid 
		) );
		$paging_data = $this->form->load_grid_config ( $count_all, $_GET ['rp'], $_GET ['page'] );
		$json_data = $paging_data ["json_paging"];
		$this->db->select ( '*,note as number', false );
		
		$this->db->where ( "reseller_id", $accountid );
		
		if (@$flag) {
			$this->db->order_by ( 'id', 'ASC' );
			$this->db->limit ( $limit, $start );
		}
		
		$query = $this->db->get ( 'reseller_pricing' );
		// echo $this->db->last_query();exit;
		$did_grid_fields = json_decode ( $this->did_form->build_did_list_for_reseller ( $accountid, $accounttype ) );
		$json_data ['rows'] = $this->form->build_grid ( $query, $did_grid_fields );
		
		echo json_encode ( $json_data );
	}
	/*
	 * ASTPP 3.0
	 * Left panel DID Quick search added
	 *
	 */
	function customer_did($accountid, $accounttype) {
		$json_data = array ();
		$instant_search = $this->session->userdata ( 'left_panel_search_' . $accounttype . '_did' );
		$account_arr = ( array ) $this->db->get_where ( 'accounts', array (
				"id" => $accountid 
		) )->first_row ();
		$field_name = $accounttype == "reseller" ? "parent_id" : 'accountid';
		$like_str = ! empty ( $instant_search ) ? "(a.note like '%$instant_search%'
					OR  a.init_inc like '%$instant_search%'
					OR  a.inc like '%$instant_search%'
					OR  a.cost like '%$instant_search%'
					OR  a.includedseconds like '%$instant_search%'
					OR  a.setup like '%$instant_search%'
					OR  a.monthlycost like '%$instant_search%'
					OR  a.connectcost like '%$instant_search%'
					    )" : null;
		if ($account_arr ['reseller_id'] != 0) {
			if (! empty ( $like_str ))
				$this->db->where ( $like_str );
			if ($accounttype == 'reseller') {
				$this->db->where ( 'a.note', 'b.number', false );
				$this->db->where ( 'a.reseller_id', $account_arr ['id'] );
				$this->db->where ( 'a.parent_id', $account_arr ['reseller_id'] );
				$this->db->select ( 'count(a.id) as count' );
				$count_result = ( array ) $this->db->get ( 'reseller_pricing as a,dids as b' )->first_row ();
				$paging_data = $this->form->load_grid_config ( $count_result ['count'], $_GET ['rp'], $_GET ['page'] );
				$json_data = $paging_data ["json_paging"];
				$this->db->where ( 'a.note', 'b.number', false );
				$this->db->where ( 'a.reseller_id', $account_arr ['id'] );
				$this->db->where ( 'a.parent_id', $account_arr ['reseller_id'] );
				$this->db->select ( 'a . * , b.id, a.reseller_id AS accountid,a.note as number,b.country_id as country_id' );
				$this->db->limit ( $paging_data ["paging"] ["page_no"], $paging_data ["paging"] ["start"] );
				$query = $this->db->get ( 'reseller_pricing as a,dids as b' );
			} else {
				$count_result = ( array ) $this->db->query ( 'select count(id) as count from dids where accountid=' . $accountid . " AND parent_id =" . $account_arr ['reseller_id'] )->first_row ();
				$paging_data = $this->form->load_grid_config ( $count_result ['count'], $_GET ['rp'], $_GET ['page'] );
				$json_data = $paging_data ["json_paging"];
				$query = $this->db->query ( "SELECT a . * ,a.note as number,b.country_id as country_id,b.id FROM reseller_pricing AS a, dids AS b WHERE b.accountid =" . $account_arr ['id'] . " AND a.note = b.number AND a.reseller_id =" . $account_arr ['reseller_id'] );
			}
		} else {
			$like_str = ! empty ( $instant_search ) ? "(dids.number like '%$instant_search%'
                                                    OR dids.inc like '%$instant_search%'
                                                    OR dids.cost like '%$instant_search%'
                                                    OR dids.includedseconds like '%$instant_search%'
                                                    OR dids.setup like '%$instant_search%'
                                                    OR dids.monthlycost like '%$instant_search%'
                                                    OR dids.connectcost like '%$instant_search%'
                                                        )" : null;
			if (! empty ( $like_str ))
				$this->db->where ( $like_str );
			$where = array (
					$field_name => $accountid 
			);
			$count_all = $this->db_model->countQuery ( "*", "dids", $where );
			$paging_data = $this->form->load_grid_config ( $count_all, $_GET ['rp'], $_GET ['page'] );
			$json_data = $paging_data ["json_paging"];
			if (! empty ( $like_str ))
				$this->db->where ( $like_str );
			$query = $this->db_model->select ( "*", "dids", $where, "id", "ASC", $paging_data ["paging"] ["page_no"], $paging_data ["paging"] ["start"] );
		}
		$did_grid_fields = json_decode ( $this->did_form->build_did_list_for_customer ( $accountid, $accounttype ) );
		$json_data ['rows'] = $this->form->build_grid ( $query, $did_grid_fields );
		echo json_encode ( $json_data );
	}
	function did_delete_multiple() {
		$ids = $this->input->post ( "selected_ids", true );
		$where = "id IN ($ids)";
		$this->db->where ( $where );
		$this->db->select ( "group_concat(concat('''',number,'''')) as number", false );
		$dids_result = ( array ) $this->db->get ( 'dids' )->first_row ();
		$notes_where = "note IN (" . $dids_result ['number'] . ")";
		$this->db->where ( $notes_where );
		$this->db->delete ( 'reseller_pricing' );
		// /*ASTPP_invoice_changes_05_05_start*/
		$this->db->where ( $where );
		$this->db->delete ( 'dids' );
		echo "DIDs";
		// end
	}
	
	/**
	 * -------Here we write code for controller did functions manage------
	 * @action: Add, Edit, Delete, List DID
	 * @id: DID number
	 */
	function did_reseller_edit($action = false, $id = false) {
		$data ['page_title'] = gettext ( 'Edit DID ' );
		$accountinfo = $this->session->userdata ( 'accountinfo' );
		if ($action == 'edit') {
			if (($this->input->post ())) {
				$post = $this->input->post ();
				/*
				 * ASTPP 3.0 last modified date update
				 */
				$post ['last_modified_date'] = gmdate ( 'Y-m-d H:i:s' );
				/* * ***************************************************** */
				unset ( $post ['action'] );
				$post ['setup'] = $this->common_model->add_calculate_currency ( $post ['setup'], '', '', false, false );
				$post ['monthlycost'] = $this->common_model->add_calculate_currency ( $post ['monthlycost'], '', '', false, false );
				$post ['connectcost'] = $this->common_model->add_calculate_currency ( $post ['connectcost'], '', '', false, false );
				$post ['cost'] = $this->common_model->add_calculate_currency ( $post ['cost'], '', '', false, false );
				$this->db->where ( array (
						'note' => $post ['note'],
						"reseller_id" => $accountinfo ['id'] 
				) );
				$this->db->update ( "reseller_pricing", $post );
				$where_update_did = array (
						'extensions' => $post ['extensions'],
						'call_type' => $post ['call_type'] 
				);
				$this->db->where ( array (
						'note' => $post ['note'] 
				) );
				$this->db->update ( "reseller_pricing", $where_update_did );
				$where = array (
						'number' => $post ['note'] 
				);
				$this->db->where ( $where );
				$this->db->update ( "dids", $where_update_did );
				echo json_encode ( array (
						"SUCCESS" => " DID Updated Successfully!!" 
				) );
				exit ();
			} else {
				if ($this->session->userdata ( 'logintype' ) == 1) {
					$accountinfo = $this->did_model->get_account ( $accountinfo ['number'] );
					$reseller_did = $this->db_model->getSelect ( "*", "reseller_pricing", array (
							'did_id' => $id 
					) );
					$reseller_didinfo = ( array ) $reseller_did->first_row ();
					if (! empty ( $reseller_didinfo )) {
						$reseller_didinfo ['setup'] = $this->common_model->to_calculate_currency ( $reseller_didinfo ['setup'], '', '', true, false );
						$reseller_didinfo ['monthlycost'] = $this->common_model->to_calculate_currency ( $reseller_didinfo ['monthlycost'], '', '', true, false );
						$reseller_didinfo ['connectcost'] = $this->common_model->to_calculate_currency ( $reseller_didinfo ['connectcost'], '', '', true, false );
						$reseller_didinfo ['cost'] = $this->common_model->to_calculate_currency ( $reseller_didinfo ['cost'], '', '', true, false );
						$data ['did'] = $reseller_didinfo ['note'];
					}
					$data ['reseller_didinfo'] = $reseller_didinfo;
					$data ['accountinfo'] = $accountinfo;
					$this->load->view ( 'view_did_manage_reseller_add', $data );
				}
			}
		}
		if ($action == 'delete') {
			$this->db->where ( 'id', $id );
			$this->db->select ( 'note' );
			$reseller_pricing = ( array ) $this->db->get ( 'reseller_pricing' )->first_row ();
			$did_number = $reseller_pricing ['note'];
			$did_info = ( array ) $this->db->get_where ( 'dids', array (
					'number' => $did_number 
			) )->first_row ();
			$query = "select count(id) as count from reseller_pricing where id >= (select id from reseller_pricing where note =$did_number AND parent_id =" . $accountinfo ['reseller_id'] . " AND reseller_id =" . $accountinfo ['id'] . " limit 1) AND note= $did_number order by id desc";
			$result = ( array ) $this->db->query ( $query )->first_row ();
			if ($result ['count'] > 0) {
				$str = $this->common->get_parent_info ( $did_info ['parent_id'], $accountinfo ['id'] );
				$str = rtrim ( $str, "," );
				$account_result = ( array ) $this->db->get_where ( 'accounts', "id IN (" . $str . ")" )->result_array ();
				foreach ( $account_result as $key => $acc_row ) {
					$acc_row ['did_number'] = $did_info ['number'];
					$this->common->mail_to_users ( 'email_remove_did', $acc_row );
				}
				$reseller_ids = $this->common->get_subreseller_info ( $accountinfo ['id'] );
				$reseller_ids = rtrim ( $reseller_ids, "," );
				$where = "parent_id IN ($reseller_ids)";
				$this->db->where ( 'note', $did_info ['number'] );
				$this->db->delete ( 'reseller_pricing', $where );
				$this->db->where ( 'reseller_id', $accountinfo ['id'] );
				$this->db->where ( 'note', $did_info ['number'] );
				$this->db->delete ( 'reseller_pricing' );
			}
			$this->db->where ( 'number', $did_number );
			$this->db->select ( 'accountid' );
			$did_array = ( array ) $this->db->get ( 'dids' )->first_row ();
			if ($did_array ['accountid'] > 0) {
				$customer_info = ( array ) $this->db->get_where ( 'accounts', array (
						'id' => $did_array ['accountid'] 
				) )->first_row ();
				$customer_info ['did_number'] = $did_number;
				$this->common->mail_to_users ( 'email_remove_did', $customer_info );
			}
			$did_array = array (
					"accountid" => 0,
					"parent_id" => $accountinfo ['reseller_id'],
					"assign_date" => "0000-00-00 00:00:00",
					"charge_upto" => "0000-00-00 00:00:00" 
			);
			$this->db->where ( 'number', $did_number );
			$this->db->update ( 'dids', $did_array );
			$this->session->set_flashdata ( 'astpp_notification', 'DID Removed Successfully!' );
			redirect ( base_url () . 'did/did_list/' );
		}
	}

	//Reseller DID purchase process function
	function did_reseller_purchase() {
		// Get account information from session.
		$accountinfo = $this->session->userdata ( 'accountinfo' );

		if (($this->input->post ())) {
			$post = $this->input->post ();
			$did_result = $this->did_lib->did_billing_process($this->session->userdata,$accountinfo['id'],$post ['free_did_list']);
			$astpp_flash_message_type = ($did_result[0] == "SUCCESS")?"astpp_errormsg":"astpp_notification";
			$this->session->set_flashdata ( $astpp_flash_message_type, $did_result[1] );						
		}else {
			$this->session->set_flashdata ( 'astpp_notification', 'Please Select DID.' );
		}	
		redirect ( base_url () . 'did/did_list/' );
		exit ();		
	}
	function add_invoice_data_user($accountid, $charge_type, $description, $credit) {
		$insert_array = array (
				'accountid' => $accountid,
				'charge_type' => $charge_type,
				'description' => $description,
				'credit' => $credit,
				'charge_id' => '0',
				'package_id' => '0' 
		);
		
		$this->db->insert ( 'invoice_item', $insert_array );
		$this->load->module ( 'invoices/invoices' );
		$this->invoices->invoices->generate_receipt ( $accountid, $credit );
		
		return true;
	}
	function did_download_sample_file($file_name) {
		$this->load->helper ( 'download' );
		$full_path = base_url () . "assets/Rates_File/" . $file_name . ".csv";
		$arrContextOptions = array (
				"ssl" => array (
						"verify_peer" => false,
						"verify_peer_name" => false 
				) 
		);
		$file = file_get_contents ( $full_path, false, stream_context_create ( $arrContextOptions ) );
		force_download ( "samplefile.csv", $file );
	}
	/*
	 * -------Here we write code for controller did functions did_import------
	 * @Purpose this function check if account number exist or not then remove from database.
	 * @params $account_number: Account Number
	 * @return Return Appropreate message If Account Delete or not.
	 */
	function did_import() {
		$data ['page_title'] = gettext ( 'Import DIDs' );
		$this->session->set_userdata ( 'import_did_rate_csv', "" );
		$error_data = $this->session->userdata ( 'import_did_csv_error' );
		$full_path = $this->config->item ( 'rates-file-path' );
		if (file_exists ( $full_path . $error_data ) && $error_data != "") {
			unlink ( $full_path . $error_data );
			$this->session->set_userdata ( 'import_did_csv_error', "" );
		}
		$accountinfo = $this->session->userdata ( 'accountinfo' );
		$this->db->where ( 'id', $accountinfo ['currency_id'] );
		$this->db->select ( 'currency' );
		$currency_info = ( array ) $this->db->get ( 'currency' )->first_row ();
		$data ['fields'] = "DID,Country,Account,Per Minute Cost(" . $currency_info ['currency'] . "),Initial Increment,Increment,Setup Fee(" . $currency_info ['currency'] . "),Monthly Fee(" . $currency_info ['currency'] . "),Call Type,Destination,Status";
		$this->load->view ( 'view_import_did', $data );
	}
	function did_preview_file() {
		$data ['page_title'] = gettext ( 'Import DIDs' );
		$config_did_array = $this->config->item ( 'DID-rates-field' );
		$accountinfo = $this->session->userdata ( 'accountinfo' );
		$this->db->where ( 'id', $accountinfo ['currency_id'] );
		$this->db->select ( 'currency' );
		$currency_info = ( array ) $this->db->get ( 'currency' )->first_row ();
		foreach ( $config_did_array as $key => $value ) {
			$key = str_replace ( 'CURRENCY', $currency_info ['currency'], $key );
			$did_fields_array [$key] = $value;
		}
		$check_header = $this->input->post ( 'check_header', true );
		$invalid_flag = false;
		if (isset ( $_FILES ['didimport'] ['name'] ) && $_FILES ['didimport'] ['name'] != "") {
			list ( $txt, $ext ) = explode ( ".", $_FILES ['didimport'] ['name'] );
			if ($ext == "csv" && $_FILES ["didimport"] ['size'] > 0) {
				$error = $_FILES ['didimport'] ['error'];
				if ($error == 0) {
					$uploadedFile = $_FILES ["didimport"] ["tmp_name"];
					$full_path = $this->config->item ( 'rates-file-path' );
					$actual_file_name = "ASTPP-DIDs-" . date ( "Y-m-d H:i:s" ) . "." . $ext;
					if (move_uploaded_file ( $uploadedFile, $full_path . $actual_file_name )) {
						$data ['page_title'] = gettext ( 'Import DIDs Preview' );
						$data ['csv_tmp_data'] = $this->csvreader->parse_file ( $full_path . $actual_file_name, $did_fields_array, $check_header );
						$data ['provider_id'] = $_POST ['provider_id'];
						$data ['check_header'] = $check_header;
						$this->session->set_userdata ( 'import_did_rate_csv', $actual_file_name );
					} else {
						$data ['error'] = "File Uploading Fail Please Try Again";
					}
				}
			} else {
				$data ['error'] = "Invalid file format : Only CSV file allows to import records(Can't import empty file)";
			}
		} else {
			$invalid_flag = true;
		}
		if ($invalid_flag) {
			$data ['fields'] = "DID,Country,Account,Per Minute Cost(" . $currency_info ['currency'] . "),Initial Increment,Increment,Setup Fee(" . $currency_info ['currency'] . "),Monthly Fee(" . $currency_info ['currency'] . "),Call Type,Destination,Status";
			$str = '';
			if (empty ( $_FILES ['didimport'] ['name'] )) {
				$str .= '<br/>Please Select  File.';
			}
			$data ['error'] = $str;
		}
		$this->load->view ( 'view_import_did', $data );
	}
	function did_import_file($provider_id, $check_header = false) {
		$new_final_arr = array ();
		$invalid_array = array ();
		$new_final_arr_key = $this->config->item ( 'DID-rates-field' );
		$accountinfo = $this->session->userdata ( 'accountinfo' );
		$reseller_id = $accountinfo ['type'] == 1 ? $accountinfo ['id'] : 0;
		
		$full_path = $this->config->item ( 'rates-file-path' );
		$did_file_name = $this->session->userdata ( 'import_did_rate_csv' );
		$csv_tmp_data = $this->csvreader->parse_file ( $full_path . $did_file_name, $new_final_arr_key, $check_header );
		$flag = false;
		$i = 0;
		$number_arr = array ();
		$reseller_array = array ();
		$final_reseller_array = array ();
		foreach ( $csv_tmp_data as $key => $csv_data ) {
			
			if (isset ( $csv_data ['number'] ) && $csv_data ['number'] != '' && $i != 0) {
				$str = null;
				if (isset ( $csv_data ['call_type'] )) {
					if (strtolower ( $csv_data ['call_type'] ) == 'sip-did') {
						$call_type = '3';
					} else if (strtolower ( $csv_data ['call_type'] ) == 'did-local') {
						$call_type = '1';
					} else if (strtolower ( $csv_data ['call_type'] ) == 'other') {
						$call_type = '2';
					} else {
						$call_type = '0';
					}
				} else {
					$call_type = '0';
				}
				$csv_data ['accountid'] = isset ( $csv_data ['accountid'] ) ? $csv_data ['accountid'] : 0;
				$csv_data ['country_id'] = isset ( $csv_data ['country_id'] ) ? $csv_data ['country_id'] : 0;
				// $csv_data['call_type'] = isset($csv_data['call_type']) && (strtolower($csv_data['call_type']) == 'local' || strtolower($csv_data['call_type']) == 'pstn' || strtolower($csv_data['call_type']) == 'other' ) ? $this->common->get_custom_call_type(strtoupper($csv_data['call_type'])) : 0;
				$csv_data ['call_type'] = $call_type;
				$csv_data ['extensions'] = isset ( $csv_data ['extensions'] ) ? $csv_data ['extensions'] : '';
				$csv_data ['includedseconds'] = isset ( $csv_data ['includedseconds'] ) ? $csv_data ['includedseconds'] : 0;
				$csv_data ['cost'] = ! empty ( $csv_data ['cost'] ) && is_numeric ( $csv_data ['cost'] ) && $csv_data ['cost'] ? $csv_data ['cost'] : 0;
				$csv_data ['setup'] = ! empty ( $csv_data ['setup'] ) && is_numeric ( $csv_data ['setup'] ) && $csv_data ['setup'] > 0 ? $csv_data ['setup'] : 0;
				$csv_data ['monthlycost'] = ! empty ( $csv_data ['monthlycost'] ) && is_numeric ( $csv_data ['monthlycost'] ) && $csv_data ['monthlycost'] > 0 ? $csv_data ['monthlycost'] : 0;
				$csv_data ['connectcost'] = ! empty ( $csv_data ['connectcost'] ) && is_numeric ( $csv_data ['connectcost'] ) && $csv_data ['connectcost'] > 0 ? $csv_data ['connectcost'] : 0;
				$csv_data ['inc'] = isset ( $csv_data ['inc'] ) ? $csv_data ['inc'] : 0;
				$str = $this->data_validate ( $csv_data );
				if ($str != "") {
					$invalid_array [$i] = $csv_data;
					$invalid_array [$i] ['error'] = $str;
				} else {
					if (! in_array ( $csv_data ['number'], $number_arr )) {
						$number_count = $this->db_model->countQuery ( 'id', 'dids', array (
								'number' => $csv_data ['number'] 
						) );
						if ($number_count > 0) {
							$invalid_array [$i] = $csv_data;
							$invalid_array [$i] ['error'] = 'Duplicate DID found from database';
						} else {
							if ($csv_data ['accountid'] > 0) {
								$this->db->where ( 'type IN(0,1,3)' );
								$this->db->where ( 'reseller_id', 0 );
								$this->db->where ( 'deleted', 0 );
								$this->db->where ( 'status', 0 );
								$account_info = ( array ) $this->db->get_where ( 'accounts', array (
										"number" => $csv_data ['accountid'] 
								) )->first_row ();
								if ($account_info) {
									$account_balance = $this->db_model->get_available_bal ( $account_info );
									$setup = $this->common_model->add_calculate_currency ( $csv_data ['setup'], '', '', false, false );
									if ($account_balance >= $setup) {
										$field_name = $account_info ['type'] == 1 ? 'parent_id' : 'accountid';
										$currency_name = $this->common->get_field_name ( 'currency', "currency", array (
												'id' => $account_info ['currency_id'] 
										) );
										$csv_data ['monthlycost'] = $this->common_model->add_calculate_currency ( $csv_data ['monthlycost'], '', '', false, false );
										$csv_data ['cost'] = $this->common_model->add_calculate_currency ( $csv_data ['cost'], '', '', false, false );
										$csv_data ['connectcost'] = $this->common_model->add_calculate_currency ( $csv_data ['connectcost'], '', '', false, false );
										$csv_data ['setup'] = $setup;
										$csv_data [$field_name] = $account_info ['id'];
										$csv_data ['status'] = $this->common->get_import_status ( $csv_data ['status'] );
										$available_bal = $this->db_model->update_balance ( $csv_data ["setup"], $account_info ['id'], "debit" );
										$account_info ['did_number'] = $csv_data ['number'];
										$account_info ['did_country_id'] = $csv_data ['country_id'];
										$account_info ['did_setup'] = $this->common_model->calculate_currency ( $csv_data ['setup'], '', $currency_name, true, true );
										$account_info ['did_monthlycost'] = $this->common_model->calculate_currency ( $csv_data ['monthlycost'], '', $currency_name, true, true );
										$account_info ['did_maxchannels'] = "0";
										$csv_data ['country_id'] = $this->common->get_field_name ( 'id', 'countrycode', array (
												"country" => $csv_data ['country_id'] 
										) );
										if ($account_info ['type'] == 1) {
											$reseller_array = $csv_data;
											$reseller_array ['note'] = $csv_data ['number'];
											$reseller_array ['reseller_id'] = $account_info ['id'];
											$reseller_array ['parent_id'] = $account_info ['reseller_id'];
											$reseller_array ['assign_date'] = gmdate ( "Y-m-d H:i:s" );
											unset ( $reseller_array ['number'], $csv_data ['accountid'], $reseller_array ['accountid'], $reseller_array ['country_id'], $reseller_array ['init_inc'] );
											$csv_data ['accountid'] = 0;
											$final_reseller_array [$i] = $reseller_array;
										} else {
											$csv_data ['parent_id'] = 0;
										}
										$csv_data ['assign_date'] = gmdate ( "Y-m-d H:i:s" );
										$new_final_arr [$i] = $csv_data;
										$this->common->mail_to_users ( 'email_add_did', $account_info );
									} else {
										$invalid_array [$i] = $csv_data;
										$invalid_array [$i] ['error'] = 'Account have not sufficient amount to purchase this DID.';
									}
								} else {
									$invalid_array [$i] = $csv_data;
									$invalid_array [$i] ['error'] = 'Account not found or assign to invalid account';
								}
							} else {
								$csv_data ['setup'] = $this->common_model->add_calculate_currency ( $csv_data ['setup'], '', '', false, false );
								$csv_data ['monthlycost'] = $this->common_model->add_calculate_currency ( $csv_data ['monthlycost'], '', '', false, false );
								$csv_data ['cost'] = $this->common_model->add_calculate_currency ( $csv_data ['cost'], '', '', false, false );
								$csv_data ['connectcost'] = $this->common_model->add_calculate_currency ( $csv_data ['connectcost'], '', '', false, false );
								$csv_data ['accountid'] = 0;
								$csv_data ['country_id'] = $this->common->get_field_name ( 'id', 'countrycode', array (
										"country" => $csv_data ['country_id'] 
								) );
								$new_final_arr [$i] = $csv_data;
							}
						}
					} else {
						$invalid_array [$i] = $csv_data;
						$invalid_array [$i] ['error'] = 'Duplicate DID found from import file.';
					}
				}
				$number_arr [] = $csv_data ['number'];
			}
			$i ++;
		}
		if (! empty ( $new_final_arr )) {
			$result = $this->did_model->bulk_insert_dids ( $new_final_arr );
		}
		if (! empty ( $final_reseller_array )) {
			$this->db->insert_batch ( 'reseller_pricing', $final_reseller_array );
		}
		unlink ( $full_path . $did_file_name );
		$count = count ( $invalid_array );
		if ($count > 0) {
			$session_id = "-1";
			$fp = fopen ( $full_path . $session_id . '.csv', 'w' );
			foreach ( $new_final_arr_key as $key => $value ) {
				$custom_array [0] [$key] = ucfirst ( $key );
			}
			$custom_array [0] ['error'] = "Error";
			$invalid_array = array_merge ( $custom_array, $invalid_array );
			foreach ( $invalid_array as $err_data ) {
				fputcsv ( $fp, $err_data );
			}
			fclose ( $fp );
			$this->session->set_userdata ( 'import_did_csv_error', $session_id . ".csv" );
			$data ["error"] = $invalid_array;
			$data ['provider_id'] = $provider_id;
			$data ['import_record_count'] = count ( $new_final_arr ) + count ( $reseller_array );
			$data ['failure_count'] = count ( $invalid_array ) - 1;
			$data ['page_title'] = gettext ( 'DID Import Error' );
			$this->load->view ( 'view_import_error', $data );
		} else {
			$this->session->set_flashdata ( 'astpp_errormsg', 'Total ' . count ( $new_final_arr ) . ' DIDs Imported Successfully!' );
			redirect ( base_url () . "did/did_list/" );
		}
	}
	function data_validate($csvdata) {
		$str = null;
		$alpha_regex = "/^[a-z ,.'-]+$/i";
		$alpha_numeric_regex = "/^[a-z0-9 ,.'-]+$/i";
		$email_regex = "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/";
		$str .= $csvdata ['number'] != '' ? null : 'Number,';
		$str = rtrim ( $str, ',' );
		if (! $str) {
			$str .= is_numeric ( $csvdata ['number'] ) ? null : 'Number,';
			$str .= ! empty ( $csvdata ['connectcost'] ) && is_numeric ( $csvdata ['connectcost'] ) ? null : (empty ( $csvdata ['connectcost'] ) ? null : 'Connect Cost,');
			$str .= ! empty ( $csvdata ['includedseconds'] ) && is_numeric ( $csvdata ['includedseconds'] ) ? null : (empty ( $csvdata ['includedseconds'] ) ? null : 'Included Seconds,');
			if ($str) {
				$str = rtrim ( $str, ',' );
				$error_field = explode ( ',', $str );
				$count = count ( $error_field );
				$str .= $count > 1 ? ' are not valid' : ' is not Valid';
				return $str;
			} else {
				return false;
			}
		} else {
			$str = rtrim ( $str, ',' );
			$error_field = explode ( ',', $str );
			$count = count ( $error_field );
			$str .= $count > 1 ? ' are required' : ' is Required';
			return $str;
		}
	}
	function did_error_download() {
		$this->load->helper ( 'download' );
		$error_data = $this->session->userdata ( 'import_did_csv_error' );
		$full_path = $this->config->item ( 'rates-file-path' );
		$data = file_get_contents ( $full_path . $error_data );
		force_download ( "error_did_rates.csv", $data );
	}
	function did_export_data_xls() {
		$account_info = $accountinfo = $this->session->userdata ( 'accountinfo' );
		$currency_id = $account_info ['currency_id'];
		$currency = $this->common->get_field_name ( 'currency', 'currency', $currency_id );
		$query = $this->did_model->getdid_list ( true, '0', '10000000' );
		ob_clean ();
		$outbound_array [] = array (
				gettext ( "DID" ),
				gettext ( "Country" ),
				gettext ( "Account" ),
				gettext ( "Per Minute Cost" ) . "(" . $currency . ")",
				gettext ( "Initial Increment" ),
				gettext ( "Increment" ),
				gettext ( "Setup Fee" ) . "(" . $currency . ")",
				gettext ( "Monthly Fee" ) . "(" . $currency . ")",
				gettext ( "Call Type" ),
				gettext ( "Destination" ),
				gettext ( "Status" ),
				gettext ( "Modified Date" ),
				gettext ( "Is Purchased?" ) 
		);
		if ($query->num_rows () > 0) {
			foreach ( $query->result_array () as $row ) {
				$outbound_array [] = array (
						$row ['number'],
						$this->common->get_field_name ( "country", "countrycode", $row ['country_id'] ),
						$this->common->get_field_name ( "number", "accounts", $row ['accountid'] ),
						$this->common_model->calculate_currency ( $row ['cost'], '', '', true, false ),
						$row ['init_inc'],
						$row ['inc'],
						$this->common_model->calculate_currency ( $row ['setup'], '', '', true, false ),
						$this->common_model->calculate_currency ( $row ['monthlycost'], '', '', true, false ),
						$this->common->get_call_type ( "", "", $row ['call_type'] ),
						$row ['extensions'],
						$this->common->get_status ( 'export', '', $row ['status'] ),
						$row ['last_modified_date'],
						$this->common->check_did_avl_export ( $row ['number'] ) 
				);
			}
		}
		$this->load->helper ( 'csv' );
		array_to_csv ( $outbound_array, 'DIDs_' . date ( "Y-m-d" ) . '.csv' );
	}
}
?>
 
