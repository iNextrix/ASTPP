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
class Systems extends CI_Controller {
	function Systems() {
		parent::__construct ();
		
		$this->load->helper ( 'template_inheritance' );
		$this->load->helper ( 'file' );
		$this->load->library ( 'session' );
		$this->load->library ( "system_form" );
		$this->load->library ( 'astpp/form' );
		$this->load->model ( 'system_model' );
		$this->load->dbutil ();
		
		if ($this->session->userdata ( 'user_login' ) == FALSE)
			redirect ( base_url () . '/astpp/login' );
	}
	function configuration_edit($edit_id = '') {
		$data ['page_title'] = gettext ( 'Edit Settings' );
		$where = array (
				'id' => $edit_id 
		);
		$account = $this->db_model->getSelect ( "*", "system", $where );
		foreach ( $account->result_array () as $key => $value ) {
			$edit_data = $value;
		}
		$data ['form'] = $this->form->build_form ( $this->system_form->get_configuration_form_fields (), $edit_data );
		$this->load->view ( 'view_configuration_add_edit', $data );
	}
	function configuration_search() {
		$ajax_search = $this->input->post ( 'ajax_search', 0 );
		if ($this->input->post ( 'advance_search', TRUE ) == 1) {
			$this->session->set_userdata ( 'advance_search', $this->input->post ( 'advance_search' ) );
			$action = $this->input->post ();
			unset ( $action ['action'] );
			unset ( $action ['advance_search'] );
			$this->session->set_userdata ( 'configuration_search', $action );
		}
		if (@$ajax_search != 1) {
			redirect ( base_url () . 'systems/configuration/' );
		}
	}
	function configuration_clearsearchfilter() {
		$this->session->set_userdata ( 'advance_search', 0 );
		$this->session->set_userdata ( 'did_search', "" );
	}
	function configuration_save() {
		$add_array = $this->input->post ();
		$data ['form'] = $this->form->build_form ( $this->system_form->get_configuration_form_fields (), $add_array );
		if ($add_array ['id'] != '') {
			$data ['page_title'] = gettext ( 'Edit Settings' );
			if ($this->form_validation->run () == FALSE) {
				$data ['validation_errors'] = validation_errors ();
				echo $data ['validation_errors'];
				exit ();
			} else {
				$this->system_model->edit_configuration ( $add_array, $add_array ['id'] );
				echo json_encode ( array (
						"SUCCESS" => "setting updated successfully!" 
				) );
				exit ();
			}
		}
	}
	function configuration($group_title = '') {
		if ($group_title == "") {
			redirect ( base_url () . '/dashboard' );
		}
		if ($group_title == "email") {
			$data ['test_email_flag'] = true;
		}
		$data ['username'] = $this->session->userdata ( 'user_name' );
		$data ['page_title'] = gettext ( ucfirst ( $group_title ) );
		$data ['group_title'] = $group_title;
		$where = array (
				"group_title" => $group_title 
		);
		$details = $this->db_model->getSelect ( "*", "system", $where );
		$data ['details'] = $details->result_array ();
		$add_array = $this->input->post ();
		if (! empty ( $add_array )) {
			if (isset ( $add_array ['version'] )) {
				unset ( $add_array ['version'] );
			}
			foreach ( $add_array as $key => $val ) {
				$update_array = array (
						'value' => $val 
				);
				$this->system_model->edit_configuration ( $update_array, $key );
			}
			$this->session->set_flashdata ( 'astpp_errormsg', ucfirst ( $group_title ) . ' Settings updated sucessfully!' );
			redirect ( base_url () . 'systems/configuration/' . $group_title );
		} else {
			$this->load->view ( 'view_systemconf', $data );
		}
	}
	function configuration_json() {
		$json_data = array ();
		$count_all = $this->system_model->getsystem_list ( false, "", "" );
		$paging_data = $this->form->load_grid_config ( $count_all, $_GET ['rp'], $_GET ['page'] );
		$json_data = $paging_data ["json_paging"];
		
		$query = $this->system_model->getsystem_list ( true, $paging_data ["paging"] ["start"], $paging_data ["paging"] ["page_no"] );
		$grid_fields = json_decode ( $this->system_form->build_system_list_for_admin () );
		$json_data ['rows'] = $this->form->build_grid ( $query, $grid_fields );
		
		echo json_encode ( $json_data );
	}
	function template() {
		$data ['username'] = $this->session->userdata ( 'user_name' );
		$data ['page_title'] = gettext ( 'Email Templates' );
		$data ['search_flag'] = true;
		$this->session->set_userdata ( 'advance_search', 0 );
		$data ['grid_fields'] = $this->system_form->build_template_list_for_admin ();
		$data ["grid_buttons"] = $this->system_form->build_grid_buttons ();
		$data ['form_search'] = $this->form->build_serach_form ( $this->system_form->get_template_search_form () );
		$this->load->view ( 'view_template_list', $data );
	}
	function template_json() {
		$json_data = array ();
		$count_all = $this->system_model->gettemplate_list ( false, "", "" );
		$paging_data = $this->form->load_grid_config ( $count_all, $_GET ['rp'], $_GET ['page'] );
		$json_data = $paging_data ["json_paging"];
		
		$query = $this->system_model->gettemplate_list ( true, $paging_data ["paging"] ["start"], $paging_data ["paging"] ["page_no"] );
		$grid_fields = json_decode ( $this->system_form->build_template_list_for_admin () );
		$json_data ['rows'] = $this->form->build_grid ( $query, $grid_fields );
		
		echo json_encode ( $json_data );
	}
	function template_edit($edit_id = '') {
		$data ['page_title'] = gettext ( 'Edit Email template' );
		$where = array (
				'id' => $edit_id 
		);
		$account = $this->db_model->getSelect ( "*", "default_templates", $where );
		foreach ( $account->result_array () as $key => $value ) {
			$edit_data = $value;
		}
		$data ['form'] = $this->form->build_form ( $this->system_form->get_template_form_fields (), $edit_data );
		$this->load->view ( 'view_template_add_edit', $data );
	}
	function template_save() {
		$add_array = $this->input->post ();
		// ITPLATP 22_05_2017
		$template = preg_replace ( '<!-- (.|\s)*? -->', '', $add_array ['template'] );
		$add_array ['template'] = str_replace ( "&lt;", "", str_replace ( "&gt;", "", $template ) );
		// end
		if ($this->session->userdata ( 'logintype' ) == 1 || $this->session->userdata ( 'logintype' ) == 5) {
			$account_data = $this->session->userdata ( "accountinfo" );
			$reseller = $account_data ['id'];
			$this->resellertemplate_save ( $add_array, $reseller );
		} else {
			$this->admintemplate_save ( $add_array );
		}
	}
	function resellertemplate_save($data, $resellerid) {
		$where = array (
				'name' => $data ['name'],
				'reseller_id' => $resellerid 
		);
		$count = $this->db_model->countQuery ( "*", "default_templates", $where );
		$data ['form'] = $this->form->build_form ( $this->system_form->get_template_form_fields (), $data );
		if ($count > 0) {
			$data ['page_title'] = gettext ( 'Edit Template' );
			if ($this->form_validation->run () == FALSE) {
				$data ['validation_errors'] = validation_errors ();
			} else {
				
				$this->system_model->edit_resellertemplate ( $data, $data ['id'] );
				$this->session->set_flashdata ( 'astpp_errormsg', 'Template updated successfully!' );
				redirect ( base_url () . 'systems/template/' );
				exit ();
			}
		} else {
			if ($this->form_validation->run () == FALSE) {
				$data ['validation_errors'] = validation_errors ();
			} else {
				unset ( $data ['form'] );
				$data ['reseller_id'] = $resellerid;
				$this->system_model->add_resellertemplate ( $data );
				$this->session->set_flashdata ( 'astpp_errormsg', 'Template added successfully!' );
				redirect ( base_url () . 'systems/template/' );
				exit ();
			}
		}
		$this->load->view ( 'view_template_add_edit', $data );
	}
	function admintemplate_save($data) {
		$data ['form'] = $this->form->build_form ( $this->system_form->get_template_form_fields (), $data );
		if ($data ['id'] != '') {
			$data ['page_title'] = gettext ( 'Edit Template' );
			if ($this->form_validation->run () == FALSE) {
				$data ['validation_errors'] = validation_errors ();
			} else {
				unset ( $data ['form'] );
				unset ( $data ['page_title'] );
				$this->system_model->edit_template ( $data, $data ['id'] );
				$this->session->set_flashdata ( 'astpp_errormsg', 'Template updated successfully!' );
				redirect ( base_url () . 'systems/template/' );
				exit ();
			}
		} else {
			$data ['page_title'] = gettext ( 'Termination Details' );
			if ($this->form_validation->run () == FALSE) {
				$data ['validation_errors'] = validation_errors ();
			} else {
				unset ( $data ['form'] );
				$this->system_model->add_template ( $data );
				$this->session->set_flashdata ( 'astpp_errormsg', 'Template added successfully!' );
				redirect ( base_url () . 'systems/template/' );
				exit ();
			}
		}
		$this->load->view ( 'view_template_add_edit', $data );
	}
	function template_search() {
		$ajax_search = $this->input->post ( 'ajax_search', 0 );
		
		if ($this->input->post ( 'advance_search', TRUE ) == 1) {
			$this->session->set_userdata ( 'advance_search', $this->input->post ( 'advance_search' ) );
			$action = $this->input->post ();
			unset ( $action ['action'] );
			unset ( $action ['advance_search'] );
			$this->session->set_userdata ( 'template_search', $action );
		}
		if (@$ajax_search != 1) {
			redirect ( base_url () . 'systems/template/' );
		}
	}
	function template_clearsearchfilter() {
		$this->session->set_userdata ( 'advance_search', 0 );
		$this->session->set_userdata ( 'did_search', "" );
	}
	function country_list() {
		$data ['username'] = $this->session->userdata ( 'user_name' );
		$data ['page_title'] = gettext ( 'Countries' );
		$data ['search_flag'] = true;
		$data ['cur_menu_no'] = 4;
		$this->session->set_userdata ( 'country_search', 0 );
		$data ['grid_fields'] = $this->system_form->build_country_list_for_admin ();
		if ($this->session->userdata ( 'logintype' ) == 2) {
			$data ["grid_buttons"] = $this->system_form->build_admin_grid_buttons ();
		} else {
			$data ["grid_buttons"] = json_encode ( array () );
		}
		$data ['form_search'] = $this->form->build_serach_form ( $this->system_form->get_search_country_form () );
		$this->load->view ( 'view_country_list', $data );
	}
	function country_list_json() {
		$json_data = array ();
		$count_all = $this->system_model->getcountry_list ( false );
		$paging_data = $this->form->load_grid_config ( $count_all, $_GET ['rp'], $_GET ['page'] );
		$json_data = $paging_data ["json_paging"];
		$query = $this->system_model->getcountry_list ( true, $paging_data ["paging"] ["start"], $paging_data ["paging"] ["page_no"] );
		$grid_fields = json_decode ( $this->system_form->build_country_list_for_admin () );
		$json_data ['rows'] = $this->form->build_grid ( $query, $grid_fields );
		
		echo json_encode ( $json_data );
	}
	function country_list_search() {
		$ajax_search = $this->input->post ( 'ajax_search', 0 );
		
		if ($this->input->post ( 'advance_search', TRUE ) == 1) {
			$this->session->set_userdata ( 'advance_search', $this->input->post ( 'advance_search' ) );
			$action = $this->input->post ();
			unset ( $action ['action'] );
			unset ( $action ['advance_search'] );
			$this->session->set_userdata ( 'country_list_search', $action );
		}
		if (@$ajax_search != 1) {
			redirect ( base_url () . 'systems/country_list/' );
		}
	}
	function country_list_clearsearchfilter() {
		$this->session->set_userdata ( 'advance_search', 0 );
		$this->session->set_userdata ( 'country_search', "" );
	}
	function country_add() {
		$data ['username'] = $this->session->userdata ( 'user_name' );
		$data ['flag'] = 'create';
		$data ['page_title'] = gettext ( 'Add Country' );
		$data ['form'] = $this->form->build_form ( $this->system_form->get_country_form_fields (), '' );
		$this->load->view ( 'view_country_add_edit', $data );
	}
	function country_list_edit($edit_id = '') {
		$data ['page_title'] = gettext ( 'Edit Country' );
		$where = array (
				'id' => $edit_id 
		);
		$account = $this->db_model->getSelect ( "*", "countrycode", $where );
		foreach ( $account->result_array () as $key => $value ) {
			$edit_data = $value;
		}
		$data ['form'] = $this->form->build_form ( $this->system_form->get_country_form_fields (), $edit_data );
		$this->load->view ( 'view_country_add_edit', $data );
	}
	function country_save() {
		$add_array = $this->input->post ();
		$data ['form'] = $this->form->build_form ( $this->system_form->get_country_form_fields (), $add_array );
		if ($add_array ['id'] != '') {
			$data ['page_title'] = gettext ( 'Edit Country' );
			if ($this->form_validation->run () == FALSE) {
				$data ['validation_errors'] = validation_errors ();
				echo $data ['validation_errors'];
				exit ();
			} else {
				$this->system_model->edit_country ( $add_array, $add_array ['id'] );
				echo json_encode ( array (
						"SUCCESS" => $add_array ["country"] . " country updated successfully!" 
				) );
				exit ();
			}
		} else {
			$data ['page_title'] = gettext ( 'Add Country' );
			if ($this->form_validation->run () == FALSE) {
				$data ['validation_errors'] = validation_errors ();
				echo $data ['validation_errors'];
				exit ();
			} else {
				$response = $this->system_model->add_country ( $add_array );
				echo json_encode ( array (
						"SUCCESS" => $add_array ["country"] . " country added successfully!" 
				) );
				exit ();
			}
		}
	}
	function country_remove($id) {
		$this->system_model->remove_country ( $id );
		$country = $this->common->get_field_name ( 'country', 'countrycode', $id );
		$this->session->set_flashdata ( 'astpp_notification', $country . 'Country removed successfully!' );
		redirect ( base_url () . 'systems/country_list/' );
	}
	function country_delete_multiple() {
		$ids = $this->input->post ( "selected_ids", true );
		$where = "id IN ($ids)";
		$this->db->where ( $where );
		echo $this->db->delete ( "countrycode" );
	}
	function currency_list() {
		$base_currency = Common_model::$global_config ['system_config'] ['base_currency'];
		$data ['username'] = $this->session->userdata ( 'user_name' );
		$data ['page_title'] = gettext ( 'Currencies' );
		$data ['search_flag'] = true;
		$data ['cur_menu_no'] = 4;
		$this->session->set_userdata ( 'currency_search', 0 );
		$data ['grid_fields'] = $this->system_form->build_currency_list_for_admin ();
		if ($this->session->userdata ( 'logintype' ) == 2) {
			$data ["grid_buttons"] = $this->system_form->build_admin_currency_grid_buttons ();
		} else {
			$data ["grid_buttons"] = json_encode ( array () );
		}
		$data ['form_search'] = $this->form->build_serach_form ( $this->system_form->get_search_currency_form () );
		$this->load->view ( 'view_currency_list', $data );
	}
	function currency_list_json() {
		$json_data = array ();
		
		$count_all = $this->system_model->getcurrency_list ( false );
		$paging_data = $this->form->load_grid_config ( $count_all, $_GET ['rp'], $_GET ['page'] );
		$json_data = $paging_data ["json_paging"];
		
		$query = $this->system_model->getcurrency_list ( true, $paging_data ["paging"] ["start"], $paging_data ["paging"] ["page_no"] );
		$grid_fields = json_decode ( $this->system_form->build_currency_list_for_admin () );
		$json_data ['rows'] = $this->form->build_grid ( $query, $grid_fields );
		
		echo json_encode ( $json_data );
	}
	function currency_list_search() {
		$ajax_search = $this->input->post ( 'ajax_search', 0 );
		
		if ($this->input->post ( 'advance_search', TRUE ) == 1) {
			$this->session->set_userdata ( 'advance_search', $this->input->post ( 'advance_search' ) );
			$action = $this->input->post ();
			unset ( $action ['action'] );
			unset ( $action ['advance_search'] );
			$this->session->set_userdata ( 'currency_list_search', $action );
		}
		if (@$ajax_search != 1) {
			redirect ( base_url () . 'systems/currency_list/' );
		}
	}
	function currency_list_clearsearchfilter() {
		$this->session->set_userdata ( 'advance_search', 0 );
		$this->session->set_userdata ( 'currency_search', "" );
	}
	function currency_add() {
		$data ['username'] = $this->session->userdata ( 'user_name' );
		$data ['flag'] = 'create';
		$data ['page_title'] = gettext ( 'Add Currency' );
		$data ['form'] = $this->form->build_form ( $this->system_form->get_currency_form_fields (), '' );
		$this->load->view ( 'view_currency_add_edit', $data );
	}
	function currency_list_edit($edit_id = '') {
		$data ['page_title'] = gettext ( 'Edit Currency' );
		$where = array (
				'id' => $edit_id 
		);
		$account = $this->db_model->getSelect ( "*", "currency", $where );
		foreach ( $account->result_array () as $key => $value ) {
			$edit_data = $value;
		}
		$data ['form'] = $this->form->build_form ( $this->system_form->get_currency_form_fields (), $edit_data );
		$this->load->view ( 'view_country_add_edit', $data );
	}
	function currency_save() {
		$add_array = $this->input->post ();
		$data ['form'] = $this->form->build_form ( $this->system_form->get_currency_form_fields (), $add_array );
		if ($add_array ['id'] != '') {
			$data ['page_title'] = gettext ( 'Edit Currency' );
			$data ['page_title'] = gettext ( 'Edit Currency' );
			if ($this->form_validation->run () == FALSE) {
				$data ['validation_errors'] = validation_errors ();
				echo $data ['validation_errors'];
				exit ();
			} else {
				$this->system_model->edit_currency ( $add_array, $add_array ['id'] );
				echo json_encode ( array (
						"SUCCESS" => $add_array ["currencyname"] . " currency updated successfully!" 
				) );
				exit ();
			}
		} else {
			$data ['page_title'] = gettext ( 'Create Currency' );
			if ($this->form_validation->run () == FALSE) {
				$data ['validation_errors'] = validation_errors ();
				echo $data ['validation_errors'];
				exit ();
			} else {
				$response = $this->system_model->add_currency ( $add_array );
				echo json_encode ( array (
						"SUCCESS" => $add_array ["currencyname"] . " currency added successfully!" 
				) );
				exit ();
			}
		}
	}
	function currency_remove($id) {
		$currencyname = $this->common->get_field_name ( 'currencyname', 'currency', $id );
		$this->system_model->remove_currency ( $id );
		$this->session->set_flashdata ( 'astpp_notification', $currencyname . ' Currency removed successfully!' );
		redirect ( base_url () . 'systems/currency_list/' );
	}
	function currency_delete_multiple() {
		$ids = $this->input->post ( "selected_ids", true );
		$where = "id IN ($ids)";
		$this->db->where ( $where );
		echo $this->db->delete ( "currency" );
	}
	function database_backup() {
		$data = array ();
		$data ['username'] = $this->session->userdata ( 'user_name' );
		$data ['page_title'] = gettext ( 'Database Backup' );
		$filename = $this->db->database . "_" . date ( "YmdHms" ) . ".sql.gz";
		$data ['form'] = $this->form->build_form ( $this->system_form->get_backup_database_form_fields ( $filename ), '' );
		$this->load->view ( 'view_database_backup', $data );
	}
	function database_backup_save() {
		$add_array = $this->input->post ();
		
		$data ['form'] = $this->form->build_form ( $this->system_form->get_backup_database_form_fields ( $add_array ['path'], $add_array ['id'] ), $add_array );
		$data ['page_title'] = gettext ( 'Database Backup' );
		if ($add_array ['id'] != '') {
		} else {
			if ($this->form_validation->run () == FALSE) {
				$data ['validation_errors'] = validation_errors ();
				echo $data ['validation_errors'];
				exit ();
			} else {
				$db_name = $this->db->database;
				$db_username = $this->db->username;
				$db_password = $this->db->password;
				$db_hostname = $this->db->hostname;
				$filename = $add_array ['path'];
				$backup_file = DATABASE_DIRECTORY . $filename;
				if (substr ( $backup_file, - 3 ) == '.gz') {
					$backup_file = substr ( $backup_file, 0, - 3 );
					$do_gzip = 1;
				}
				
				$run_backup = "/usr/bin/mysqldump -all --databases " . $db_name . " -u'" . $db_username . "' -p'" . $db_password . "' > '$backup_file'";
				$error_zip = 0;
				exec ( $run_backup, $output, $error );
				if ($do_gzip) {
					$gzip = $this->config->item ( 'gzip-path' );
					$run_gzip = $gzip . " " . $backup_file;
					exec ( $run_gzip, $output, $error_zip );
				}
				
				if ($error == 0 && $error_zip == 0) {
					$this->system_model->backup_insert ( $add_array );
					echo json_encode ( array (
							"SUCCESS" => $add_array ['backup_name'] . " backup exported successfully!" 
					) );
					exit ();
				} else {
					echo 'An error occur when the system tried to backup of the database. Please check yours system settings for the backup section';
					exit ();
				}
			}
		}
	}
	function database_restore() {
		$data ['page_title'] = gettext ( 'Database Restore' );
		$data ['grid_fields'] = $this->system_form->build_backupdastabase_list ();
		$data ["grid_buttons"] = $this->system_form->build_backupdastabase_buttons ();
		$this->load->view ( 'view_database_list', $data );
	}
	function database_restore_json() {
		$json_data = array ();
		$count_all = $this->system_model->getbackup_list ( false, "", "" );
		$paging_data = $this->form->load_grid_config ( $count_all, $_GET ['rp'], $_GET ['page'] );
		$json_data = $paging_data ["json_paging"];
		
		$query = $this->system_model->getbackup_list ( true, $paging_data ["paging"] ["start"], $paging_data ["paging"] ["page_no"] );
		$grid_fields = json_decode ( $this->system_form->build_backupdastabase_list () );
		$json_data ['rows'] = $this->form->build_grid ( $query, $grid_fields );
		
		echo json_encode ( $json_data );
		exit ();
	}
	function database_restore_one($id = '') {
		$result = $this->system_model->get_backup_data ( $id );
		$result_array = $result->result_array ();
		if ($result->num_rows () > 0) {
			$db_name = $this->db->database;
			$db_username = $this->db->username;
			$db_password = $this->db->password;
			$db_hostname = $this->db->hostname;
			$path = DATABASE_DIRECTORY . $result_array [0] ['path'];
			if (file_exists ( $path )) {
				if (substr ( $path, - 3 ) == '.gz') {
					$GUNZIP_EXE = $this->config->item ( 'gunzip-path' );
					$run_gzip = $GUNZIP_EXE . " < " . $path . " | ";
				}
				$MYSQL = "/usr/bin/mysql";
				$run_restore = $run_gzip . $MYSQL . " -h" . $db_hostname . " -u" . $db_username . " -p" . $db_password . " " . $db_name;
				exec ( $run_restore );
				$this->session->set_flashdata ( 'astpp_errormsg', 'Database Restore successfully.' );
				redirect ( base_url () . 'systems/database_restore/' );
			} else {
				$this->session->set_flashdata ( 'astpp_notification', 'File not exists!' );
				redirect ( base_url () . 'systems/database_restore/' );
			}
		}
		redirect ( base_url () . 'systems/database_restore/' );
	}
	function database_download($id = '') {
		$result = $this->system_model->get_backup_data ( $id );
		$result_array = $result->result_array ();
		if ($result->num_rows () > 0) {
			$path = DATABASE_DIRECTORY. $result_array [0] ['path'];
			$filename = basename ( $path );
			$len = filesize ( $path );
			
			header ( "Content-Encoding: binary" );
			header ( "Content-Type: application/octet-stream" );
			header ( "content-length: " . $len );
			header ( "content-disposition: attachment; filename=" . $filename );
			header ( "Expires: 0" );
			header ( "Cache-Control: must-revalidate, post-check=0, pre-check=0" );
			header ( "Cache-Control: private" );
			header ( "Pragma: public" );
			ob_clean ();
			$fp = fopen ( $path, "r" );
			fpassthru ( $fp );
			exit ();
		}
	}
	function database_import() {
		$data ['page_title'] = gettext ( 'Import Database' );
		$this->load->view ( 'view_import_database', $data );
	}
	function database_import_file() {
		$filename = $_POST ['fname'];
		$upload_greeting_file = strtolower ( $_FILES ['userfile'] ['name'] );
		$db_file = explode ( ".", $upload_greeting_file );
		$last_key = array_pop ( $db_file );
		if ($last_key == 'csv' || $last_key == 'tar' || $last_key == 'sql') {
			$target_path = basename ( $_FILES ['userfile'] ['name'] );
			move_uploaded_file ( $_FILES ["userfile"] ["tmp_name"], $target_path );
			
			$query = $this->system_model->import_database ( $filename, $_FILES ['userfile'] ['name'] );
			$this->session->set_flashdata ( 'astpp_errormsg', "The file " . basename ( $_FILES ['userfile'] ['name'] ) . " has been uploaded" );
			redirect ( base_url () . "systems/database_restore/" );
		} else {
			$this->session->set_flashdata ( 'astpp_notification', "There is a some issue or invalid file format." );
			redirect ( base_url () . "systems/database_restore/" );
		}
	}
	function database_delete($id) {
		$where = array (
				"id" => $id 
		);
		$this->db->where ( $where );
		$this->db->delete ( "backup_database" );
		$this->session->set_flashdata ( 'astpp_errormsg', 'Database backup deleted successfully.' );
		redirect ( base_url () . 'systems/database_restore/' );
		return true;
	}
	function database_backup_delete_multiple() {
		$ids = $this->input->post ( "selected_ids", true );
		$where = "id IN ($ids)";
		$this->db->where ( $where );
		echo $this->db->delete ( "backup_database" );
	}
}

?>
  
