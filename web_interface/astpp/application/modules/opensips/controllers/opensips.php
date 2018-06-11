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
class Opensips extends MX_Controller {
	function Opensips() {
		parent::__construct ();
		
		$this->load->helper ( 'template_inheritance' );
		$this->load->library ( 'session' );
		$this->load->library ( "opensips_form" );
		$this->load->library ( 'astpp/form' );
		$this->load->model ( 'opensips_model' );
		$db_config = Common_model::$global_config ['system_config'];
		$opensipdsn = "mysqli://" . $db_config ['opensips_dbuser'] . ":" . $db_config ['opensips_dbpass'] . "@" . $db_config ['opensips_dbhost'] . "/" . $db_config ['opensips_dbname'] . "?char_set=utf8&dbcollat=utf8_general_ci&cache_on=true&cachedir=";
		$this->opensips_db = $this->load->database ( $opensipdsn, true );
		if ($this->session->userdata ( 'user_login' ) == FALSE)
			redirect ( base_url () . '/astpp/login' );
	}
	function opensips_add() {
		// echo 'dd';
		$data ['username'] = $this->session->userdata ( 'user_name' );
		$data ['flag'] = 'create';
		$data ['page_title'] = gettext ( 'Add  Opensips' );
		$data ['form'] = $this->form->build_form ( $this->opensips_form->get_opensips_form_fields (), '' );
		$this->load->view ( 'view_opensips_add_edit', $data );
	}
	function opensips_edit($edit_id = '') {
		$data ['page_title'] = gettext ( 'Edit Opensips' );
		$this->opensips_db->where ( 'id', $edit_id );
		$account = $this->opensips_db->get ( "subscriber" );
		foreach ( $account->result_array () as $key => $value ) {
			$edit_data = $value;
		}
		
		$data ['form'] = $this->form->build_form ( $this->opensips_form->get_opensips_form_fields ( $edit_id ), $edit_data );
		$this->load->view ( 'view_opensips_add_edit', $data );
	}
	function customer_opensips_edit($accountid, $edit_id) {
		$data ['page_title'] = gettext ( 'Edit Opensips' );
		$where = array (
				'id' => $edit_id 
		);
		$this->opensips_db->where ( $where );
		$account = $this->opensips_db->get ( "subscriber" );
		foreach ( $account->result_array () as $key => $value ) {
			$edit_data = $value;
		}
		$data ['form'] = $this->form->build_form ( $this->opensips_form->get_opensips_form_fields_for_customer ( $accountid ), $edit_data );
		$this->load->view ( 'view_opensips_add_edit', $data );
	}
	function customer_opensips_add($accountid = '') {
		$data ['username'] = $this->session->userdata ( 'user_name' );
		$data ['flag'] = 'create';
		$data ['page_title'] = gettext ( 'Create Opensips' );
		$data ['form'] = $this->form->build_form ( $this->opensips_form->get_opensips_form_fields_for_customer ( $accountid ), '' );
		
		$this->load->view ( 'view_opensips_add_edit', $data );
	}
	function opensips_save() {
		$add_array = $this->input->post ();
		$data ['form'] = $this->form->build_form ( $this->opensips_form->get_opensips_form_fields ( $add_array ['id'] ), $add_array );
		if ($add_array ['id'] != '') {
			$data ['page_title'] = gettext ( 'Edit Opensips' );
			if ($this->form_validation->run () == FALSE) {
				$data ['validation_errors'] = validation_errors ();
				echo $data ['validation_errors'];
				exit ();
			} else {
				$auth_flag = $this->validate_device_data ( $add_array );
				if ($auth_flag == "TRUE") {
					$this->opensips_model->edit_opensipsdevices ( $add_array, $add_array ['id'] );
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
					$this->opensips_model->add_opensipsdevices ( $add_array );
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
			$opensipdsn = "mysql://" . $db_config ['opensips_dbuser'] . ":" . $db_config ['opensips_dbpass'] . "@" . $db_config ['opensips_dbhost'] . "/" . $db_config ['opensips_dbname'] . "?char_set=utf8&dbcollat=utf8_general_ci&cache_on=true&cachedir=";
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
						"username_error" => "Duplicate Email Address Found Email Must Be Unique." 
				);
			}
		} else {
			return array (
					"username_error" => "User name is required field." 
			);
		}
		return "0";
	}
	function user_opensips_save($user_flg = false) {
		$array_add = $this->input->post ();
		//
		$data ['form'] = $this->form->build_form ( $this->opensips_form->get_opensips_form_fields_for_customer ( $array_add ["accountcode"] ), $array_add );
		if ($array_add ['id'] != '') {
			$data ['page_title'] = gettext ( 'Edit Opensips' );
			if ($this->form_validation->run () == FALSE) {
				$data ['validation_errors'] = validation_errors ();
				echo $data ['validation_errors'];
				exit ();
			} else {
				$this->opensips_model->edit_opensipsdevices ( $array_add, $array_add ['id'] );
				echo json_encode ( array (
						"SUCCESS" => "Opensips Updated Successfully!" 
				) );
				exit ();
			}
		} else {
			$data ['page_title'] = gettext ( 'Add Opensips' );
			if ($this->form_validation->run () == FALSE) {
				$data ['validation_errors'] = validation_errors ();
				echo $data ['validation_errors'];
				exit ();
			} else {
				$this->opensips_model->add_opensipsdevices ( $array_add );
				echo json_encode ( array (
						"SUCCESS" => "Opensips Added Successfully!" 
				) );
				exit ();
			}
		}
	}
	function customer_opensips_save($user_flg = false) {
		$add_array = $this->input->post ();
		// print_r($array_add);exit;
		$data ['form'] = $this->form->build_form ( $this->opensips_form->get_opensips_form_fields_for_customer ( $add_array ["accountcode"] ), $add_array );
		if ($add_array ['id'] != '') {
			$data ['page_title'] = gettext ( 'Edit Opensips' );
			if ($this->form_validation->run () == FALSE) {
				$data ['validation_errors'] = validation_errors ();
				echo $data ['validation_errors'];
				exit ();
			} else {
				$this->opensips_model->edit_opensipsdevices ( $add_array, $add_array ['id'] );
				echo json_encode ( array (
						"SUCCESS" => "OpenSips Updated Successfully!" 
				) );
				exit ();
			}
		} else {
			$data ['page_title'] = gettext ( 'Add Opensips' );
			if ($this->form_validation->run () == FALSE) {
				$data ['validation_errors'] = validation_errors ();
				echo $data ['validation_errors'];
				exit ();
			} else {
				$this->opensips_model->add_opensipsdevices ( $add_array );
				echo json_encode ( array (
						"SUCCESS" => "OpenSips Added Successfully!" 
				) );
				exit ();
			}
		}
	}
	function customer_opensips_json($accountid, $accounttype) {
		$json_data = array ();
		$count_all = $this->opensips_model->getopensipsdevice_customer_list ( false, $accountid, $accounttype );
		$paging_data = $this->form->load_grid_config ( $count_all, $_GET ['rp'], $_GET ['page'] );
		$json_data = $paging_data ["json_paging"];
		
		$query = $this->opensips_model->getopensipsdevice_customer_list ( true, $accountid, $accounttype, $paging_data ["paging"] ["start"], $paging_data ["paging"] ["page_no"] );
		$grid_fields = json_decode ( $this->opensips_form->opensips_customer_build_opensips_list ( $accountid ) );
		$json_data ['rows'] = $this->form->build_grid ( $query, $grid_fields );
		
		echo json_encode ( $json_data );
	}
	function opensips_add_customer($add_data) {
		$this->opensips_model->add_opensipsdevices ( $add_array );
	}
	function opensips_remove($id) {
		$this->opensips_model->remove_opensips ( $id );
		$this->session->set_flashdata ( 'astpp_notification', 'OpenSips Removed Successfully!' );
		redirect ( base_url () . 'opensips/opensips_list/' );
	}
	function opensips_list() {
		$data ['username'] = $this->session->userdata ( 'user_name' );
		$data ['page_title'] = gettext ( 'Opensips Devices List' );
		$data ['search_flag'] = true;
		$this->session->set_userdata ( 'advance_search', 0 );
		$data ['grid_fields'] = $this->opensips_form->build_opensips_list ();
		$data ["grid_buttons"] = $this->opensips_form->build_grid_buttons ();
		$data ['form_search'] = $this->form->build_serach_form ( $this->opensips_form->get_search_opensips_form () );
		$this->load->view ( 'view_opensips_list', $data );
	}
	
	/**
	 * -------Here we write code for controller accounts functions account_list------
	 * Listing of Accounts table data through php function json_encode
	 */
	function opensips_list_json() {
		$json_data = array ();
		$count_all = $this->opensips_model->getopensipsdevice_list ( false );
		$paging_data = $this->form->load_grid_config ( $count_all, $_GET ['rp'], $_GET ['page'] );
		$json_data = $paging_data ["json_paging"];
		$query = $this->opensips_model->getopensipsdevice_list ( true, $paging_data ["paging"] ["start"], $paging_data ["paging"] ["page_no"] );
		$grid_fields = json_decode ( $this->opensips_form->build_opensips_list () );
		$json_data ['rows'] = $this->form->build_grid ( $query, $grid_fields );
		$result = $this->opensips_db->get ( "subscriber" );
		if ($result->num_rows () <= 0) {
			$json_data ['page'] = 0;
			$json_data ['total'] = 0;
		}
		echo json_encode ( $json_data );
	}
	function opensips_list_search() {
		// alert('hgjgh');
		$ajax_search = $this->input->post ( 'ajax_search', 0 );
		// alert();
		if ($this->input->post ( 'advance_search', TRUE ) == 1) {
			$this->session->set_userdata ( 'advance_search', $this->input->post ( 'advance_search' ) );
			$action = $this->input->post ();
			unset ( $action ['action'] );
			unset ( $action ['advance_search'] );
			$this->session->set_userdata ( 'opensipsdevice_list_search', $action );
		}
		if (@$ajax_search != 1) {
			redirect ( base_url () . 'opensips/opensips_list/' );
		}
	}
	function opensips_list_clearsearchfilter() {
		$this->session->set_userdata ( 'advance_search', '' );
		$this->session->set_userdata ( 'opensipsdevice_list_search', '' );
	}
	
	// dispather List add edit delete
	function dispatcher_add() {
		$data ['username'] = $this->session->userdata ( 'user_name' );
		$data ['flag'] = 'create';
		$data ['page_title'] = gettext ( 'Dispatcher' );
		$data ['form'] = $this->form->build_form ( $this->opensips_form->get_dispatcher_form_fields (), '' );
		
		$this->load->view ( 'view_dispatcher_add_edit', $data );
	}
	function dispatcher_edit($edit_id = '') {
		$data ['page_title'] = gettext ( 'Dispatcher' );
		$this->opensips_db->where ( 'id', $edit_id );
		$account = $this->opensips_db->get ( "dispatcher" );
		foreach ( $account->result_array () as $key => $value ) {
			$edit_data = $value;
		}
		
		$data ['form'] = $this->form->build_form ( $this->opensips_form->get_dispatcher_form_fields (), $edit_data );
		$this->load->view ( 'view_dispatcher_add_edit', $data );
	}
	function dispatcher_save() {
		$add_array = $this->input->post ();
		$data ['form'] = $this->form->build_form ( $this->opensips_form->get_dispatcher_form_fields (), $add_array );
		if ($add_array ['id'] != '') {
			$data ['page_title'] = gettext ( 'Edit Dispatcher' );
			if ($this->form_validation->run () == FALSE) {
				$data ['validation_errors'] = validation_errors ();
				echo $data ['validation_errors'];
				exit ();
			} else {
				$this->opensips_model->edit_opensipsdispatcher ( $add_array, $add_array ['id'] );
				echo json_encode ( array (
						"SUCCESS" => "Dispatcher Updated Successfully!" 
				) );
				exit ();
			}
		} else {
			$data ['page_title'] = gettext ( 'Add Dispatcher' );
			if ($this->form_validation->run () == FALSE) {
				$data ['validation_errors'] = validation_errors ();
				echo $data ['validation_errors'];
				exit ();
			} else {
				$this->opensips_model->add_opensipsdispatcher ( $add_array );
				echo json_encode ( array (
						"SUCCESS" => "Dispatcher Added Successfully!" 
				) );
				exit ();
			}
		}
	}
	function dispatcher_remove($id) {
		$this->opensips_model->remove_dispatcher ( $id );
		$this->session->set_flashdata ( 'astpp_notification', 'Dispatcher Removed Successfully!' );
		redirect ( base_url () . 'opensips/dispatcher_list/' );
	}
	function dispatcher_list() {
		$data ['username'] = $this->session->userdata ( 'user_name' );
		$data ['page_title'] = gettext ( 'Opensips Dispatcher List' );
		$data ['search_flag'] = true;
		$this->session->set_userdata ( 'advance_search', 0 );
		$data ['grid_fields'] = $this->opensips_form->build_opensipsdispatcher_list ();
		$data ["grid_buttons"] = $this->opensips_form->build_grid_dispatcherbuttons ();
		$data ['form_search'] = $this->form->build_serach_form ( $this->opensips_form->get_search_dispatcher_form () );
		$this->load->view ( 'view_dispatcher_list', $data );
	}
	
	/**
	 * -------Here we write code for controller accounts functions account_list------
	 * Listing of Accounts table data through php function json_encode
	 */
	function dispatcher_list_json() {
		$json_data = array ();
		$count_all = $this->opensips_model->getopensipsdispatcher_list ( false );
		$paging_data = $this->form->load_grid_config ( $count_all, $_GET ['rp'], $_GET ['page'] );
		$json_data = $paging_data ["json_paging"];
		
		$query = $this->opensips_model->getopensipsdispatcher_list ( true, $paging_data ["paging"] ["start"], $paging_data ["paging"] ["page_no"] );
		$grid_fields = json_decode ( $this->opensips_form->build_opensipsdispatcher_list () );
		$json_data ['rows'] = $this->form->build_grid ( $query, $grid_fields );
		/*
		 * $result = $this->opensips_db->get("subscriber");
		 * if($result->num_rows() <= 0){
		 * $json_data['page'] = 0;
		 * $json_data['total'] = 0;
		 * }
		 */
		echo json_encode ( $json_data );
	}
	function dispatcher_list_search() {
		$ajax_search = $this->input->post ( 'ajax_search', 0 );
		if ($this->input->post ( 'advance_search', TRUE ) == 1) {
			$this->session->set_userdata ( 'advance_search', $this->input->post ( 'advance_search' ) );
			unset ( $_POST ['action'] );
			unset ( $_POST ['advance_search'] );
			$this->session->set_userdata ( 'opensipsdispatcher_list_search', $this->input->post () );
		}
		if (@$ajax_search != 1) {
			redirect ( base_url () . 'opensips/dispatcher_list/' );
		}
	}
	function dispatcher_list_clearsearchfilter() {
		$this->session->set_userdata ( 'advance_search', 0 );
		$this->session->set_userdata ( 'opensipsdispatcher_list_search', "" );
	}
}

?>
 
