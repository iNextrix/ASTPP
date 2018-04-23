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
class Simulator extends CI_Controller {
	function Simulator() {
		parent::__construct ();
		
		$this->load->helper ( 'template_inheritance' );
		
		$this->load->library ( 'session' );
		$this->load->library ( "simulator_form" );
		$this->load->library ( 'astpp/form' );
		$this->load->model ( 'simulator_model' );
		
		if ($this->session->userdata ( 'user_login' ) == FALSE)
			redirect ( base_url () . '/astpp/login' );
	}
	function simulator_list() {
		$data ['username'] = $this->session->userdata ( 'user_name' );
		$data ['page_title'] = gettext ( 'Call Simulator' );
		$data ['search_flag'] = false;
		$this->session->set_userdata ( 'advance_search', 0 );
		$data ['grid_fields'] = $this->simulator_form->build_simulator_list_for_admin ();
		$data ["grid_buttons"] = $this->simulator_form->build_grid_buttons ();
		$data ['form_search'] = $this->form->build_serach_form ( $this->simulator_form->get_simulator_search_form () );
		$this->load->view ( 'view_simulator_list', $data );
	}
	function simulator_list_json() {
		$json_data = array ();
		$count_all = $this->simulator_model->getsimulator_list ( false );
		$paging_data = $this->form->load_grid_config ( $count_all, $_GET ['rp'], $_GET ['page'] );
		$json_data = $paging_data ["json_paging"];
		$query = $this->simulator_model->getsimulator_list ( true, $paging_data ["paging"] ["start"], $paging_data ["paging"] ["page_no"] );
		$grid_fields = json_decode ( $this->simulator_form->build_simulator_list_for_admin () );
		$json_data ['rows'] = $this->form->build_grid ( $query, $grid_fields );
		echo json_encode ( $json_data );
	}
	function simulator_add($type = "") {
		$data ['username'] = $this->session->userdata ( 'user_name' );
		$data ['flag'] = 'create';
		$data ['page_title'] = gettext ( 'Create Simulator' );
		$data ['form'] = $this->form->build_form ( $this->simulator_form->get_simulator_form_fields (), '' );
		$this->load->view ( 'view_simulator_add_edit', $data );
	}
	function simulator_edit($edit_id = '') {
		$data ['page_title'] = gettext ( 'Edit Simulator' );
		$where = array (
				'id' => $edit_id 
		);
		$account = $this->db_model->getSelect ( "*", "simulators", $where );
		foreach ( $account->result_array () as $key => $value ) {
			$edit_data = $value;
		}
		$edit_data ["resellers_id"] = explode ( ",", $edit_data ["resellers_id"] );
		$data ['form'] = $this->form->build_form ( $this->simulator_form->get_simulator_form_fields (), $edit_data );
		$this->load->view ( 'view_simulator_add_edit', $data );
	}
	function simulator_save() {
		$add_array = $this->input->post ();
		$data ['form'] = $this->form->build_form ( $this->simulator_form->get_simulator_form_fields (), $add_array );
		if ($add_array ['id'] != '') {
			$data ['page_title'] = gettext ( 'Edit Simulator Rates' );
			if ($this->form_validation->run () == FALSE) {
				$data ['validation_errors'] = validation_errors ();
				echo $data ['validation_errors'];
				exit ();
			} else {
				$this->simulator_model->edit_simulator ( $add_array, $add_array ['id'] );
				echo json_encode ( array (
						"SUCCESS" => $add_array ["name"] . " Simulator updated successfully!"
				) );
				exit ();
			}
		} else {
			$data ['page_title'] = gettext ( 'Termination Details' );
			if ($this->form_validation->run () == FALSE) {
				$data ['validation_errors'] = validation_errors ();
				echo $data ['validation_errors'];
				exit ();
			} else {
				$this->simulator_model->add_simulator ( $add_array );
				echo json_encode ( array (
						"SUCCESS" => $add_array ["name"] . " Simulator added successfully!"
				) );
				exit ();
			}
		}
	}
	function simulator_list_search() {
		$ajax_search = $this->input->post ( 'ajax_search', 0 );
		
		if ($this->input->post ( 'advance_search', TRUE ) == 1) {
			$this->session->set_userdata ( 'advance_search', $this->input->post ( 'advance_search' ) );
			$action = $this->input->post ();
			unset ( $action ['action'] );
			unset ( $action ['advance_search'] );
			$this->session->set_userdata ( 'simulator_list_search', $action );
		}
		if (@$ajax_search != 1) {
			redirect ( base_url () . 'simulator/simulator_list/' );
		}
	}
	function simulator_list_clearsearchfilter() {
		$this->session->set_userdata ( 'advance_search', 0 );
		$this->session->set_userdata ( 'account_search', "" );
	}
	function simulator_remove($id) {
		$this->simulator_model->remove_simulator ( $id );
		$this->db->delete ( "routing", array (
				"simulator_id" => $id
		) );
		$this->session->set_flashdata ( 'astpp_notification', 'Simulator removed successfully!' );
		redirect ( base_url () . 'simulator/simulator_list/' );
	}
	function simulator_delete_multiple() {
		$add_array = $this->input->post ();
		$where = 'IN (' . $add_array ['selected_ids'] . ')';
		if (isset ( $add_array ['flag'] )) {
			$update_data = array (
					'status' => '2' 
			);
			$this->db->where ( 'simulator_id ' . $where );
			$this->db->delete ( 'outbound_routes' );
			$this->db->where ( 'id ' . $where );
			$this->db->update ( 'simulators', $update_data );
			echo TRUE;
		} else {
			$simulator_arr = array ();
			$this->db->select ( 'id,name' );
			$this->db->where ( 'id ' . $where );
			$simulator_res = $this->db->get ( 'simulators' );
			$simulator_res = $simulator_res->result_array ();
			foreach ( $simulator_res as $value ) {
				$simulator_arr [$value ['id']] ['name'] = $value ['name'];
			}
			$this->db->where ( 'simulator_id ' . $where );
			$this->db->select ( 'count(id) as cnt,simulator_id' );
			$this->db->group_by ( 'simulator_id' );
			$outbound_routes_res = $this->db->get ( 'outbound_routes' );
			if ($outbound_routes_res->num_rows () > 0) {
				$outbound_routes_res = $outbound_routes_res->result_array ();
				foreach ( $outbound_routes_res as $key => $value ) {
					$simulator_arr [$value ['simulator_id']] ['outbound_routes'] = $value ['cnt'];
				}
			}
			$str = null;
			foreach ( $simulator_arr as $key => $value ) {
				if (isset ( $value ['outbound_routes'] )) {
					$str .= $value ['name'] . "simulator using by " . $value ['outbound_routes'] . " termination rates \n";
				}
			}
			if (! empty ( $str )) {
				$data ['str'] = $str;
			}
			$data ['selected_ids'] = $add_array ['selected_ids'];
			echo json_encode ( $data );
		}
	}
}

?>
 
