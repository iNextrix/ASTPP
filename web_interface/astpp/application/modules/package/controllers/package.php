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
class Package extends MX_Controller {
	function Package() {
		parent::__construct ();
		
		$this->load->helper ( 'template_inheritance' );
		
		$this->load->library ( 'session' );
		$this->load->library ( 'package_form' );
		$this->load->library ( 'astpp/form' );
		$this->load->library ( 'astpp/permission');
		$this->load->model ( 'package_model' );
		$this->load->library ( 'csvreader' );
		
		if ($this->session->userdata ( 'user_login' ) == FALSE)
			redirect ( base_url () . '/astpp/login' );
	}
	function package_list() {
		$data ['username'] = $this->session->userdata ( 'user_name' );
		$data ['page_title'] = gettext ( gettext ( 'Packages' ) );
		$data ['search_flag'] = true;
		$this->session->set_userdata ( 'advance_search', 0 );
		$data ['grid_fields'] = $this->package_form->build_package_list_for_admin ();
		$data ["grid_buttons"] = $this->package_form->build_grid_buttons ();
		$data ['form_search'] = $this->form->build_serach_form ( $this->package_form->get_package_search_form () );
		$this->load->view ( 'view_package_list', $data );
	}
	
	/**
	 * -------Here we write code for controller accounts functions account_list------
	 * Listing of Accounts table data through php function json_encode
	 */
	function package_list_json() {
		$json_data = array ();
		$count_all = $this->package_model->getpackage_list ( false );
		$paging_data = $this->form->load_grid_config ( $count_all, $_GET ['rp'], $_GET ['page'] );
		$json_data = $paging_data ["json_paging"];
		
		$query = $this->package_model->getpackage_list ( true, $paging_data ["paging"] ["start"], $paging_data ["paging"] ["page_no"] );
		$grid_fields = json_decode ( $this->package_form->build_package_list_for_admin () );
		$json_data ['rows'] = $this->form->build_grid ( $query, $grid_fields );
		
		echo json_encode ( $json_data );
	}
	function package_list_reseller($accountid, $accounttype) {
		$json_data = array ();
		$count_all = $this->package_model->get_reseller_package_list ( false, $accountid, $accounttype );
		$paging_data = $this->form->load_grid_config ( $count_all, $_GET ['rp'], $_GET ['page'] );
		$json_data = $paging_data ["json_paging"];
		$query = $this->package_model->get_reseller_package_list ( true, $accountid, $accounttype, $paging_data ["paging"] ["start"], $paging_data ["paging"] ["page_no"] );
		$grid_fields = json_decode ( $this->package_form->build_package_list_for_reseller () );
		$json_data ['rows'] = $this->form->build_grid ( $query, $grid_fields );
		echo json_encode ( $json_data );
	}
	function package_list_search() {
		$ajax_search = $this->input->post ( 'ajax_search', 0 );
		
		if ($this->input->post ( 'advance_search', TRUE ) == 1) {
			$this->session->set_userdata ( 'advance_search', $this->input->post ( 'advance_search' ) );
			$action = $this->input->post ();
			unset ( $action ['action'] );
			unset ( $action ['advance_search'] );
			$this->session->set_userdata ( 'package_list_search', $action );
		}
		if (@$ajax_search != 1) {
			redirect ( base_url () . 'package/package_list/' );
		}
	}
	function package_list_clearsearchfilter() {
		$this->session->set_userdata ( 'advance_search', 0 );
		$this->session->set_userdata ( 'package_list_search', "" );
	}
	function package_add($type = "") {
		$data ['page_title'] = gettext ( 'Create Package' );
		$data ['form'] = $this->form->build_form ( $this->package_form->get_package_form_fields (), '' );
		$this->load->view ( 'view_package_add', $data );
	}
	function package_edit($edit_id = '') {
		$this->permission->check_web_record_permission($edit_id,'packages','package/package_list/');
		$data ['page_title'] = gettext ( 'Package Details' );
		$accountinfo = $this->session->userdata ( "accountinfo" );
		$reseller_id = $accountinfo ['type'] == 1 ? $accountinfo ['id'] : 0;
		$package_result = $this->db_model->getSelect ( "*", " packages", array (
				'id' => $edit_id,
				"reseller_id" => $reseller_id 
		) );
		if ($package_result->num_rows () > 0) {
			$package_info = ( array ) $package_result->first_row ();
			$data ['form'] = $this->form->build_form ( $this->package_form->get_package_form_fields ( $package_info ['id'] ), $package_info );
			$data ['edit_id'] = $package_info ['id'];
			$this->load->view ( 'view_packages_edit', $data );
		} else {
			redirect ( base_url () . 'package/package_list/' );
		}
	}
	function package_save($id = "") {
		$add_array = $this->input->post ();
		$data ['form'] = $this->form->build_form ( $this->package_form->get_package_form_fields ( $add_array ['id'] ), $add_array );
		if ($add_array ['id'] != '') {
			if ($this->form_validation->run () == FALSE) {
				$data ['edit_id'] = $add_array ['id'];
				$data ['validation_errors'] = validation_errors ();
				$this->load->view ( 'view_packages_edit', $data );
			} else {
				$this->package_model->edit_package ( $add_array, $add_array ['id'] );
				$this->session->set_flashdata ( 'astpp_errormsg', 'Package updated successfully!' );
				redirect ( base_url () . 'package/package_list/' );
				exit ();
			}
		} else {
			$data ['page_title'] = gettext ( 'Create Package' );
			if ($this->form_validation->run () == FALSE) {
				$data ['validation_errors'] = validation_errors ();
				$this->load->view ( 'view_package_add', $data );
			} else {
				
				$this->package_model->add_package ( $add_array );
				/**
				 * ASTPP 3.0
				 * For Email Broadcast when package is add
				 * *
				 */
				$accountinfo = $this->db_model->getSelect ( "*", "accounts", array (
						"pricelist_id" => $add_array ['pricelist_id'],
						"status" => 0,
						"deleted" => 0 
				) );
				$accountinfo = $accountinfo->result_array ();
				$this->session->set_flashdata ( 'astpp_errormsg', 'Package added successfully!' );
				foreach ( $accountinfo as $key => $value ) {
					$this->common->mail_to_users ( 'add_package', $value );
				}
				/**
				 * ***********************************
				 */
				redirect ( base_url () . 'package/package_list/' );
				exit ();
			}
		}
	}
	function package_delete($id) {
		$this->permission->check_web_record_permission($id,'packages','package/package_list/');
		/**
		 * ASTPP 3.0
		 * For Email Broadcast when package is add
		 * *
		 */
		$package_detail = $this->db_model->getSelect ( "*", "packages", array (
				"id" => $id 
		) );
		$package_detail = $package_detail->result_array ();
		$package_detail = $package_detail [0];
		$accountinfo = $this->db_model->getSelect ( "*", "accounts", array (
				"pricelist_id" => $package_detail ['pricelist_id'],
				"status" => 0,
				"deleted" => 0 
		) );
		$this->package_model->remove_package ( $id );
		$this->session->set_flashdata ( 'astpp_notification', 'Package removed successfully!' );
		foreach ( $accountinfo->result_array () as $key => $value ) {
			$this->common->mail_to_users ( 'remove_package', $value );
		}
		/**
		 * ***************************************
		 */
		redirect ( base_url () . 'package/package_list/' );
	}
	function package_delete_multiple() {
		$ids = $this->input->post ( "selected_ids", true );
		$where = "id IN ($ids)";
		$this->db->where ( $where );
		echo $this->db->delete ( "packages" );
	}
	function package_pattern_list($package_id) {
		$this->permission->check_web_record_permission($package_id,'packages',"package/package_list/");
		$data ['page_title'] = gettext ( 'Package Codes' );
		if (! empty ( $package_id )) {
			$data ['grid_fields'] = $this->package_form->build_pattern_list_for_customer ( $package_id );
			$data ['grid_buttons'] = $this->package_form->set_pattern_grid_buttons ( $package_id );
			$data ["edit_id"] = $package_id;
			$this->load->view ( "view_package_pattern_list", $data );
		} else {
			redirect ( base_url () . "package/package_list/" );
		}
	}
	function package_pattern_list_json($package_id) {
		$json_data = array ();
		$instant_search = $this->session->userdata ( 'left_panel_search_package_pattern' );
		$like_str = ! empty ( $instant_search ) ? "(patterns like '%$instant_search%'  OR destination like '%$instant_search%' )" : null;
		if (! empty ( $like_str ))
			$this->db->where ( $like_str );
		$where = array (
				'package_id' => $package_id 
		);
		$count_all = $this->db_model->countQuery ( "*", "package_patterns", $where );
		$paging_data = $this->form->load_grid_config ( $count_all, $_GET ['rp'], $_GET ['page'] );
		$json_data = $paging_data ["json_paging"];
		if (! empty ( $like_str ))
			$this->db->where ( $like_str );
		$pattern_data = $this->db_model->select ( "*", "package_patterns", $where, "id", "ASC", $paging_data ["paging"] ["page_no"], $paging_data ["paging"] ["start"] );
		
		$grid_fields = json_decode ( $this->package_form->build_pattern_list_for_customer ( $package_id ) );
		$json_data ['rows'] = $this->form->build_grid ( $pattern_data, $grid_fields );
		
		echo json_encode ( $json_data );
	}
	function package_counter() {
		$data ['username'] = $this->session->userdata ( 'user_name' );
		$data ['page_title'] = gettext ( 'Usage Report' );
		$data ['grid_fields'] = $this->package_form->build_package_counter_list_for_admin ();
		$data ["grid_buttons"] = $this->package_form->build_package_counter_report ();
		$this->load->view ( 'view_package_counter_report', $data );
	}
	
	/**
	 * -------Here we write code for controller accounts functions account_list------
	 * Listing of Accounts table data through php function json_encode
	 */
	function package_counter_json() {
		$json_data = array ();
		$count_all = $this->package_model->getpackage_counter_list ( false );
		$paging_data = $this->form->load_grid_config ( $count_all, $_GET ['rp'], $_GET ['page'] );
		$json_data = $paging_data ["json_paging"];
		
		$query = $this->package_model->getpackage_counter_list ( true, $paging_data ["paging"] ["start"], $paging_data ["paging"] ["page_no"] );
		$grid_fields = json_decode ( $this->package_form->build_package_counter_list_for_admin () );
		$json_data ['rows'] = $this->form->build_grid ( $query, $grid_fields );
		
		echo json_encode ( $json_data );
	}
	function package_counter_report_export() {
		$query = $this->db_model->getSelect ( "*", "counters", '' );
		$outbound_array = array ();
		ob_clean ();
		$outbound_array [] = array (
				gettext ( "Package Name" ),
				gettext ( "Account" ),
				gettext ( "Used Seconds" ) 
		);
		if ($query->num_rows () > 0) {
			
			foreach ( $query->result_array () as $row ) {
				$outbound_array [] = array (
						$this->common->get_field_name ( 'package_name', 'packages', $row ['package_id'] ),
						$this->common->get_field_name_coma_new ( 'first_name,last_name,number', 'accounts', $row ['accountid'] ),
						$row ['seconds'] 
				);
			}
		}
		$this->load->helper ( 'csv' );
		array_to_csv ( $outbound_array, 'Usage_Report_' . date ( "Y-m-d" ) . '.csv' );
	}
	function package_pattern_json($package_id) {
		$json_data = array ();
		$where = array (
				'package_id' => $package_id 
		);
		
		$count_all = $this->db_model->countQuery ( "*", "package_patterns", $where );
		$paging_data = $this->form->load_grid_config ( $count_all, $_GET ['rp'], $_GET ['page'] );
		$json_data = $paging_data ["json_paging"];
		
		$pattern_data = $this->db_model->select ( "*", "package_patterns", $where, "id", "ASC", $paging_data ["paging"] ["page_no"], $paging_data ["paging"] ["start"] );
		$grid_fields = json_decode ( $this->package_form->build_pattern_list_for_customer ( $package_id ) );
		$json_data ['rows'] = $this->form->build_grid ( $pattern_data, $grid_fields );
		
		echo json_encode ( $json_data );
	}
	function package_patterns_add($packageid) {
		$data ['username'] = $this->session->userdata ( 'user_name' );
		$data ['page_title'] = gettext ( 'Unblocked Prefixes' );
		$this->session->set_userdata ( 'advance_search', 0 );
		$this->load->module ( 'rates/rates' );
		$data ['patters_grid_fields'] = $this->rates->rates_form->build_block_pattern_list_for_customer ();
		$data ["packageid"] = $packageid;
		$this->load->view ( 'view_prefix_list', $data );
	}
	function package_patterns_add_json($accountid) {
		$this->load->module ( 'rates/rates' );
		$json_data = array ();
		$count_all = $this->rates_model->getunblocked_package_pattern ( $accountid, false );
		$paging_data = $this->form->load_grid_config ( $count_all, $_GET ['rp'], $_GET ['page'] );
		$json_data = $paging_data ["json_paging"];
		
		$query = $this->rates->rates_model->getunblocked_package_pattern ( $accountid, true, $paging_data ["paging"] ["start"], $paging_data ["paging"] ["page_no"] );
		$grid_fields = json_decode ( $this->rates->rates_form->build_block_pattern_list_for_customer () );
		$json_data ['rows'] = $this->rates->form->build_grid ( $query, $grid_fields );
		
		echo json_encode ( $json_data );
	}
	function package_patterns_add_info($packageid) {
		$result = $this->package_model->insert_package_pattern ( $this->input->post ( 'prefixies', true ), $packageid );
		unset ( $_POST );
		echo $result;
		exit ();
	}
	function package_patterns_delete($packageid, $patternid) {
		$this->permission->check_web_record_permission($patternid,'package_patterns','package/package_list/',false,array('field_name'=>"package_id","parent_table"=>"packages"));
		$this->db->delete ( "package_patterns", array (
				"id" => $patternid 
		) );
		redirect ( base_url () . "package/package_pattern_list/$packageid" );
	}
	function package_patterns_selected_delete() {
		$ids = $this->input->post ( "selected_ids", true );
		$where = "id IN ($ids)";
		unset ( $_POST );
		echo $this->db->delete ( "package_patterns", $where );
	}
	function package_quick_search($module_name) {
		$action = $this->input->post ();
		$this->session->set_userdata ( 'left_panel_search_package_pattern', "" );
		if (! empty ( $action ['left_panel_search'] )) {
			$this->session->set_userdata ( 'left_panel_search_package_pattern', $action ['left_panel_search'] );
		}
	}
	function package_patterns_import($edit_id) {
		// echo "nick";exit;
		$data ['page_title'] = gettext ( 'Import Package Patterns' );
		$this->session->set_userdata ( 'import_package_code_csv', "" );
		$error_data = $this->session->userdata ( 'import_package_code_csv_error' );
		$full_path = $this->config->item ( 'rates-file-path' );
		if (file_exists ( $full_path . $error_data ) && $error_data != "") {
			unlink ( $full_path . $error_data );
			$this->session->set_userdata ( 'import_package_code_csv_error', "" );
		}
		$data ['edit_id'] = $edit_id;
		$this->load->view ( 'view_import_package_code', $data );
	}
	function package_patterns_download_sample_file($file_name) {
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
	function package_patterns_preview_file($edit_id) {
		$invalid_flag = false;
		$data = array ();
		$data ['page_title'] = gettext ( 'Import Package Patterns' );
		$check_header = $this->input->post ( 'check_header', true );
		if (empty ( $_FILES ) || ! isset ( $_FILES )) {
			redirect ( base_url () . "package/package_pattern_list/" );
		}
		$get_extension = strpos ( $_FILES ['package_code_import'] ['name'], '.' );
		$new_final_arr_key = $this->config->item ( 'package-code-field' );
		if (! $get_extension) {
			$data ['error'] = "Please Upload File Atleast";
		}
		// echo "<pre>";print_r($_FILES);exit;
		if (isset ( $_FILES ['package_code_import'] ['name'] ) && $_FILES ['package_code_import'] ['name'] != "") {
			list ( $txt, $ext ) = explode ( ".", $_FILES ['package_code_import'] ['name'] );
			
			if ($ext == "csv" && $_FILES ['package_code_import'] ['size'] > 0) {
				$error = $_FILES ['package_code_import'] ['error'];
				if ($error == 0) {
					$uploadedFile = $_FILES ["package_code_import"] ["tmp_name"];
					$csv_data = $this->csvreader->parse_file ( $uploadedFile, $new_final_arr_key, $check_header );
					if (! empty ( $csv_data )) {
						$full_path = $this->config->item ( 'rates-file-path' );
						// echo "<pre>";print_r($full_path);exit;
						$actual_file_name = "ASTPP-ORIGIN-RATES-" . date ( "Y-m-d H:i:s" ) . "." . $ext;
						// echo "<pre>";print_r($actual_file_name);exit;
						if (move_uploaded_file ( $uploadedFile, $full_path . $actual_file_name )) {
							$flag = false;
							// $data['trunkid']=isset($_POST['trunk_id']) && $_POST['trunk_id'] > 0 ? $_POST['trunk_id'] : 0;
							$data ['csv_tmp_data'] = $csv_data;
							// $data['pricelistid'] = $_POST['pricelist_id'];
							$data ['page_title'] = gettext ( "Package Patterns Preview" );
							$data ['check_header'] = $check_header;
							$this->session->set_userdata ( 'import_package_code_csv', $actual_file_name );
						} else {
							$data ['error'] = "File Uploading Fail Please Try Again";
						}
					}
				} else {
					$data ['error'] == "File Uploading Fail Please Try Again";
				}
			} else {
				$data ['error'] = "Invalid file format : Only CSV file allows to import records(Can't import empty file)";
			}
		} else {
			$invalid_flag = true;
		}
		if ($invalid_flag) {
			$str = '';
			if (empty ( $_FILES ['package_code_import'] ['name'] )) {
				$str .= '<br/>Please Select File.';
			}
			$data ['error'] = $str;
		}
		$data ['edit_id'] = $edit_id;
		$this->load->view ( 'view_import_package_code', $data );
	}
	function package_patterns_import_file($edit_id, $check_header = false) {
		// echo $edit_id."===="; exit;
		$new_final_arr = array ();
		$invalid_array = array ();
		$new_final_arr_key = $this->config->item ( 'package-code-field' );
		$screen_path = $this->config->item ( 'screen_path' );
		$reseller_id = 0;
		if ($this->session->userdata ( 'logintype' ) == 1 || $this->session->userdata ( 'logintype' ) == 5) {
			$reseller_id = $this->session->userdata ["accountinfo"] ['id'];
		}
		
		$full_path = $this->config->item ( 'rates-file-path' );
		// echo "<pre>";print_r($full_path);exit;
		$originationrate_file_name = $this->session->userdata ( 'import_package_code_csv' );
		$csv_tmp_data = $this->csvreader->parse_file ( $full_path . $originationrate_file_name, $new_final_arr_key, $check_header );
		// echo "<pre>";print_r($csv_tmp_data);exit;
		$i = 0;
		$pattern_arr = array ();
		foreach ( $csv_tmp_data as $key => $csv_data ) {
			if (isset ( $csv_data ['patterns'] ) && $csv_data ['patterns'] != '' && $i != 0) {
				$str = null;
				$pattern = $csv_data ['patterns'];
				if (! in_array ( $csv_data ['patterns'], $pattern_arr )) {
					$this->db->select ( 'count(id) as count' );
					$this->db->where ( 'patterns', "^" . $csv_data ['patterns'] . ".*" );
					$this->db->where ( 'package_id', $edit_id );
					$pattern_res = ( array ) $this->db->get ( 'package_patterns' )->first_row ();
					if ($pattern_res ['count'] == 0) {
						$csv_data ['destination'] = isset ( $csv_data ['destination'] ) ? $csv_data ['destination'] : '';
						$str = $this->data_validate ( $csv_data );
						if ($str != "") {
							$invalid_array [$i] = $csv_data;
							$invalid_array [$i] ['error'] = $str;
						} else {
							$csv_data ['patterns'] = "^" . $csv_data ['patterns'] . ".*";
							$csv_data ['package_id'] = $edit_id;
							$new_final_arr [$i] = $csv_data;
							$pattern_arr [$csv_data ['patterns']] = $csv_data ['patterns'];
						}
					} else {
						$invalid_array [$i] = $csv_data;
						$invalid_array [$i] ['error'] = "Duplicate pattern found from  database.";
					}
				} else {
					$invalid_array [$i] = $csv_data;
					$invalid_array [$i] ['error'] = "Duplicate pattern found from import file";
				}
				$pattern_arr [$csv_data ['patterns']] = $pattern;
			}
			
			$i ++;
		}
		if (! empty ( $new_final_arr )) {
			$result = $this->package_model->bulk_insert_package_pattern ( $new_final_arr );
		}
		// unlink($full_path.$originationrate_file_name);
		$count = count ( $invalid_array );
		// echo "<pre>";print_r($count);exit;
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
			$this->session->set_userdata ( 'import_package_code_csv_error', $session_id . ".csv" );
			$data ["error"] = $invalid_array;
			$data ['packageid'] = $edit_id;
			$data ['impoted_count'] = count ( $new_final_arr );
			$data ['failure_count'] = count ( $invalid_array ) - 1;
			$data ['page_title'] = gettext ( 'Package Patterns Import Error' );
			// print_r($data) ;exit;
			$this->load->view ( 'view_import_error', $data );
		} else {
			$this->session->set_flashdata ( 'astpp_errormsg', 'Package patterns imported successfully!' );
			// echo base_url()."package/package_pattern_list/" . $edit_id . "/";exit;
			redirect ( base_url () . "package/package_pattern_list/" . $edit_id . "/" );
		}
	}
	function data_validate($csvdata) {
		$str = null;
		$alpha_regex = "/^[a-z ,.'-]+$/i";
		$alpha_numeric_regex = "/^[a-z0-9 ,.'-]+$/i";
		$email_regex = "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/";
		$str .= $csvdata ['patterns'] != '' ? null : 'Code,';
		$str = rtrim ( $str, ',' );
		if (! $str) {
			$str .= is_numeric ( $csvdata ['patterns'] ) ? null : 'Code,';
			
			$str .= preg_match ( $alpha_numeric_regex, $csvdata ['destination'] ) ? null : 'Destination,';
			
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
	function package_patterns_error_download() {
		$this->load->helper ( 'download' );
		$error_data = $this->session->userdata ( 'import_package_code_csv_error' );
		$full_path = $this->config->item ( 'rates-file-path' );
		$data = file_get_contents ( $full_path . $error_data );
		force_download ( "Package_Code_error.csv", $data );
	}
}

?>
 
