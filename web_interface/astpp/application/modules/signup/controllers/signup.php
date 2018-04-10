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
class Signup extends MX_Controller {
	function signup() {
		parent::__construct ();
		$this->load->model ( 'signup_model' );
		$this->load->helper ( 'captcha' );
		$this->load->helper ( 'template_inheritance' );
		// $this->load->library('form_validation');
		$this->load->library ( 'astpp/common' );
		$this->load->library ( 'astpp/email_lib' );
		$this->load->model ( 'db_model' );
		$this->load->model ( 'common_model' );
		$this->load->library ( 'session' );
		$this->load->library ( 'encrypt' );
		$this->load->model ( 'Astpp_common' );				
		$data ['row'] = $this->signup_model->get_rate ();
	}
	function index($key = "") {
		if (Common_model::$global_config ['system_config'] ['enable_signup'] == 1) {
			redirect ( base_url () );
		}
		
		$userCaptcha = $this->input->post ( 'userCaptcha' );
		$random_number = substr ( number_format ( time () * rand (), 0, '', '' ), 0, 6 );
		$accountinfo = ( array ) $this->db->get_where ( 'accounts', array (
				'type' => - 1 
		) )->first_row ();
		$data ['timezone_id'] = (! $accountinfo ['timezone_id']) ? 1 : $accountinfo ['timezone_id'];
		$data ['currency_id'] = (! $accountinfo ['currency_id']) ? 1 : $accountinfo ['currency_id'];
		$data ['country_id'] = (! $accountinfo ['country_id']) ? 1 : $accountinfo ['country_id'];
		
		$vals = array (
				'word' => $random_number,
				'img_path' => getcwd () . '/assets/captcha/',
				'img_url' => base_url () . 'assets/captcha/',
				// 'font_path' => './fonts/impact.ttf',
				'img_width' => '243',
				'img_height' => '50',
				'expiration' => '3600' 
		);
		
		if (isset ( $key ) && $key != '') {
			$data ['key_unique'] = $key;
		} else {
			$data ['key_unique'] = "admin";
		}
		$unique = $data ['key_unique'];
		
		if ($unique != "admin") {
			$unique = $this->common->decode_params ( trim ( $unique ) );
			$decoded_str = $this->common->decode ( $unique );
			$unique = $decoded_str;
			$query = $this->db_model->getSelect ( "*", 'accounts', array (
					'id' => $unique,
					"deleted" => "0" 
			) );
			if ($query->num_rows () == 0) {
				redirect ( base_url () . "signup/signup_inactive" );
			}
			if ($query->num_rows () > 0) {
				$query = $query->result_array ();
				$query = $query [0];
				
				if ($query ['status'] != 0) {
					redirect ( base_url () . "signup/signup_inactive" );
				}
			}
		}
		$data ['captcha'] = create_captcha ( $vals );		
		$this->session->set_userdata ( 'captchaWord', $data ['captcha'] ['word'] );
		$this->db->select ( "*" );
		$this->db->where ( array (
				"domain" => $_SERVER ["HTTP_HOST"] 
		) );
		$res = $this->db->get ( "invoice_conf" );
		$logo_arr = $res->result ();
		// ~ echo "<pre>"; print_r($_SERVER); exit;
		$data ['user_logo'] = (isset ( $logo_arr [0]->logo ) && $logo_arr [0]->logo != "") ? $logo_arr [0]->accountid . "_" . $logo_arr [0]->logo : "logo.png";
		$data ['website_header'] = (isset ( $logo_arr [0]->website_title ) && $logo_arr [0]->website_title != "") ? $logo_arr [0]->website_title : "ASTPP - Open Source Voip Billing Solution";
		$data ['website_footer'] = (isset ( $logo_arr [0]->website_footer ) && $logo_arr [0]->website_footer != "") ? $logo_arr [0]->website_footer : "Inextrix Technologies Pvt. Ltd All Rights Reserved.";
		$this->session->set_userdata ( 'user_logo', $data ['user_logo'] );
		$this->session->set_userdata ( 'user_header', $data ['website_header'] );
		$this->session->set_userdata ( 'user_footer', $data ['website_footer'] );
		$this->load->view ( 'view_signup', $data );
	}
	public function check_captcha($str) {
		$word = $this->session->userdata ( 'captchaWord' );
		if (strcmp ( strtoupper ( $str ), strtoupper ( $word ) ) == 0) {
			return true;
		} else {
			$this->form_validation->set_message ( 'check_captcha', 'Please enter correct words!' );
			return false;
		}
	}
	function terms_check() {
		if (isset ( $_POST ['agreeCheck'] )) {
			return true;
		}
		$this->form_validation->set_message ( 'terms_check', 'THIS IS SOOOOO REQUIRED, DUDE!' );
		return false;
	}
	function signup_save($id = "") {
		if (empty ( $_POST )) {
			redirect ( base_url () . "signup/" );
		} else {
			$post_values = $this->input->post ();
			$userCaptcha = $this->input->post ( 'userCaptcha' );
			$cnt_result = $this->db_model->countQuery ( "*", 'accounts', array (
					'email' => $post_values ['email'],
					'deleted' => 0 
			) );
			
			if ($userCaptcha != $this->session->userdata ( 'captchaWord' ) || ! filter_var ( $this->input->post ( 'email' ), FILTER_VALIDATE_EMAIL ) || $cnt_result > 0) {
				if (! filter_var ( $this->input->post ( 'email' ), FILTER_VALIDATE_EMAIL )) {
					$data ['error'] ['email'] = "<div style='color: red;'> Please enter proper email </div>";
				}
				if ($userCaptcha != $this->session->userdata ( 'captchaWord' )) {
					$data ['error'] ['userCaptcha'] = "<div id='capcha_error' style='color: red;'>Please enter valid Captcha code</div>";
				}
				if ($cnt_result > 0) {
					$data ['error'] ['email'] = "<div id= 'email_error' style='color: red;'>Email Address already exists</div>";
				}
				$random_number = substr ( number_format ( time () * rand (), 0, '', '' ), 0, 6 );
				$vals = array (
						'word' => $random_number,
						'img_path' => getcwd () . '/assets/captcha/',
						'img_url' => base_url () . 'assets/captcha/',
						// 'font_path' => './fonts/impact.ttf',
						'img_width' => '243',
						'img_height' => '50',
						'expiration' => '3600' 
				);
				// echo "<pre>"; print_r($_POST); exit;
				if (isset ( $_POST ['key_unique'] ) && $_POST ['key_unique'] == "admin") {
					$data ['key_unique'] = $_POST ['key_unique'];
				}
				
				$accountinfo = ( array ) $this->db->get_where ( 'accounts', array (
						'type' => - 1 
				) )->first_row ();
				$data ['timezone_id'] = (! $accountinfo ['timezone_id']) ? 1 : $accountinfo ['timezone_id'];
				$data ['currency_id'] = (! $accountinfo ['currency_id']) ? 1 : $accountinfo ['currency_id'];
				$data ['country_id'] = (! $accountinfo ['country_id']) ? 1 : $accountinfo ['country_id'];
				
				$data ['timezone_id'] = (! $data ['timezone_id']) ? 1 : $data ['timezone_id'];
				$data ['currency_id'] = (! $data ['currency_id']) ? 1 : $data ['currency_id'];
				$data ['country_id'] = (! $data ['country_id']) ? 1 : $data ['country_id'];
				
				$data ['value'] = $post_values;
				$data ['captcha'] = create_captcha ( $vals );
				
				$this->session->set_userdata ( 'captchaWord', $data ['captcha'] ['word'] );
				$data ['key_unique'] = $_POST ['key_unique'];
				$this->load->view ( 'view_signup', $data );
			} else {
				// AVTLATP
				$user_data = $this->input->post ();
				
				if (! isset ( $_POST ['key_unique'] ) || ! isset ( $_POST ['email'] )) {
					redirect ( base_url () . "signup/" );
				}
				$reseller_id = 0;
				if (isset ( $_POST ['key_unique'] ) && $_POST ['key_unique'] != "admin") {
					$_POST ['key_unique'] = $this->common->decode_params ( trim ( $_POST ['key_unique'] ) );
					$decoded_str = $this->common->decode ( $_POST ['key_unique'] );
					$_POST ['key_unique'] = $decoded_str;
					$user_data ['key_unique'] = $_POST ['key_unique'];
					$reseller_id = $user_data ['key_unique'];
				}
				// echo "<pre>"; print_r($_POST); exit;
				// AVTLATP
				// Data want to insert or update
				$user_data ['status'] = "1";
				$user_data ['number'] = $this->common->find_uniq_rendno_customer ( common_model::$global_config ['system_config'] ['cardlength'], 'number', 'accounts' );
				
				$user_data ['password'] = $this->common->encode ( $this->common->generate_password () );
				$user_data ['pin'] = $this->common->generate_password ();
				$user_data ['reseller_id'] = $reseller_id;
				$user_data ['posttoexternal'] = "0";
				
				unset ( $user_data ['userCaptcha'] );
				unset ( $user_data ['action'] );
				
				$system_config = common_model::$global_config ['system_config'];
				$balance = $system_config ["balance"];
				
				/*
				 * $query = $this->db_model->getSelect("*", 'invoice_conf ', array('id' => $unique ,"deleted" => "0"));
				 * $query = $query->result_array();
				 */
				
				$company_website = $system_config ["company_website"];
				$company_name = $system_config ["company_name"];
				
				// echo $company_name; exit;
				$selection_rategroup_signup = $system_config ["default_signup_rategroup"];
				
				if ($reseller_id != 0) {
					$result = $this->db_model->getSelect ( "*", "pricelists", array (
							"reseller_id" => $reseller_id 
					), "ASC" );
					$result_arr = $result->result_array ();
					$selection_rategroup_signup = $result_arr [0] ['id'];
					$user_data ['pricelist_id'] = (isset ( $selection_rategroup_signup ) && $selection_rategroup_signup > 0) ? $selection_rategroup_signup : 0;
				} else {
					$pricelist_id = $this->common->get_field_name ( 'id', 'pricelists', array (
							'name' => $selection_rategroup_signup 
					) );
					/*
					 * if($pricelis_id != "")
					 * $user_data['pricelist_id'] = $pricelis_id;
					 * else
					 * $user_data['pricelist_id'] = 0;
					 */
					$user_data ['pricelist_id'] = ($pricelist_id != "") ? $pricelist_id : 0;
				}
				$last_id = '0';
				// Insert or Update record
				$signup_sipdevice_flag = $system_config ['create_sipdevice'];
				$user_data ['is_recording'] = 1;
				$last_id = $this->signup_model->add_user ( $user_data );
				if ($last_id == "") {
					redirect ( base_url () . "signup/signup_inactive" );
				}
				if ($signup_sipdevice_flag == '0') {
					$query = $this->db_model->select ( "*", "sip_profiles", array (
							'name' => "default" 
					), "id", "ASC", '1', '0' );
					$sip_id = $query->result_array ();
					if ($reseller_id > 0) {
						$reseller_id = $reseller_id;
					} else {
						$reseller_id = '0';
					}
					$free_switch_array = array (
							'fs_username' => $user_data ['number'],
							'fs_password' => $user_data ['password'],
							'context' => 'default',
							'effective_caller_id_name' => $user_data ['number'],
							'effective_caller_id_number' => $user_data ['number'],
							'sip_profile_id' => $sip_id [0] ['id'],
							'reseller_id' => $reseller_id,
							'pricelist_id' => $user_data ['pricelist_id'],
							'accountcode' => $last_id,
							'status' => $user_data ['status'],
							'voicemail_enabled' => true,
							'voicemail_password' => '',
							'voicemail_mail_to' => '',
							'voicemail_attach_file' => true,
							'vm_keep_local_after_email' => true,
							'vm_send_all_message' => true 
					);
					$user_custom_array = array_merge ( $user_data, $free_switch_array );
					$user_custom_array ['id'] = $last_id;
					$user_custom_array ['email'] = $user_data ['email'];
					
					$this->load->model ( 'freeswitch/freeswitch_model' );
					$this->freeswitch_model->add_freeswith ( $user_custom_array );
				}
				// echo "<pre>"; print_r ($user_data); exit;
				$activation = $this->encrypt->encode ( $user_data ['number'] );
				$message = base_url () . 'signup/signup_confirm?email=' . urlencode ( $user_data ['email'] ) . "&key=" . urlencode ( $activation );
				$user_data ['confirm'] = $message;
				
				$this->send_mail ( $last_id, 'email_signup_confirmation', $user_data );
				redirect ( base_url () . "signup/signup_success" );
			}
		}
	}
	function signup_confirm() {
		if (! empty ( $_GET )) {
			
			$system_config = common_model::$global_config ['system_config'];
			$balance = $system_config ["balance"];
			$accno = $this->encrypt->decode ( $_GET ['key'] );
			$email = $_GET ['email'];
			$success = $this->signup_model->check_user ( $accno, $email, $balance );
			$query = $this->db_model->getSelect ( "*", "accounts", array (
					'number' => $accno 
			) );
			$data = $query->result_array ();
			$user_data = $data [0];
			$user_data ['accountid'] = $user_data ['id'];
			$user_data ['success'] = $success;
			$user_data ['balance'] = $balance;
			$user_data ['confirm'] = base_url ();
			$this->active ( $user_data, $success );
		} else {
			redirect ( base_url () );
		}
	}
	function signup_success() {
		$this->load->view ( 'view_signup_success' );
	}
	function signup_inactive() {
		$this->load->view ( 'view_signup_inactive' );
	}
	function active($user_data, $success) {
		$data ['user_data'] = $user_data;
		$data ['user_data'] ['success'] = $success;
		
		if ($user_data ['success']) {
			$user_data ['password'] = $this->common->decode ( $user_data ['password'] );
			$this->send_mail ( $user_data ['id'], 'email_add_user', $user_data );
		}
		$this->load->view ( 'view_signup_active', $data );
	}
	function forgotpassword() {
		$this->load->view ( 'view_forgotpassword' );
	}
	function confirmpassword() {
		$email = $_POST ['email'];
		unset ( $_POST ['action'] );
		$where = array (
				'email' => $email 
		);
		$this->db->where ( $where );
		$this->db->or_where ( 'number', $email );
		$cnt_result = $this->db_model->countQuery ( "*", 'accounts', "" );
		if (! empty ( $email )) {
			$names = array (
					'0',
					'1',
					'3' 
			);
			$this->db->where_in ( 'type', $names );
			$where_arr = array (
					"email" => $email 
			);
			$this->db->where ( $where_arr );
			$this->db->or_where ( 'number', $email );
			$this->db->order_by('id','DESC');
			$acountdata = $this->db_model->getSelect ( "*", "accounts", "" );
			if ($acountdata->num_rows () > 0) {
				$user_data = $acountdata->result_array ();
				$user_data = $user_data [0];
				if ($user_data ['deleted'] == 1) {
					$data ['error'] ['email'] = "<div id='error_mail' style='color:red; margin: 1% 22%; float: left;'>Your account has been deleted. Please contact administrator for more information</div>";
					$this->load->view ( 'view_forgotpassword', $data );
					exit ();
				}
				if ($user_data ['status'] > 0) {
					$data ['error'] ['email'] = "<div id='error_mail' style='color:red; margin: 1% 22%; float: left;'>Your account is inactive. Please contact administrator for more information</div>";
					$this->load->view ( 'view_forgotpassword', $data );
					exit ();
				}
			}
			if ($acountdata->num_rows () == 0 && ! filter_var ( $email, FILTER_VALIDATE_EMAIL )) {
				if (! filter_var ( $email, FILTER_VALIDATE_EMAIL )) {
					$data ['error'] ['email'] = "<div id='error_mail' style='color: red; margin: 2% 22%; float: left; width:100%;'>Please enter proper Username or Email.</div>";
					
					$this->load->view ( 'view_forgotpassword', $data );
				} else {
					$data ['error'] ['email'] = "<div id='error_mail' style='color: red; margin: 2% 22%; float: left;width:100%;'>This Username or Email is not valid</div>";
					
					$this->load->view ( 'view_forgotpassword', $data );
				}
			} else if ($acountdata->num_rows () == 0) {
				$data ['error'] ['email'] = "<div id='error_mail' style='color: red; margin: 2% 22%; float: left; width:100%;'>Please enter proper Username or Email.</div>";
				$this->load->view ( 'view_forgotpassword', $data );
			} else {
				$acountdata = $acountdata->result_array ();
				$user_data = $acountdata [0];
				
				$email = $this->encrypt->encode ( $user_data ['email'] );
				$activation = $this->encrypt->encode ( $user_data ['number'] );
				$message = base_url () . 'confirm_pass?email=' . urlencode ( $email ) . "&key=" . urlencode ( $activation );
				$user_data ['confirm'] = $message;
				$where = array (
						"email" => $user_data ['email'] 
				);
				$data = array (
						"pass_link_status" => 1 
				);
				$this->db->where ( $where );
				$this->db->update ( 'accounts', $data );
				$system_config = common_model::$global_config ['system_config'];
				$balance = $system_config ["balance"];
				$this->send_mail ( $user_data ['id'], 'email_forgot_confirmation', $user_data );
				$this->load->view ( 'view_forgot_success' );
			}
		} else {
			redirect ( base_url () );
		}
	}
	function confirm_pass() {
		$confirm_pass = $_GET;
		$accno = '';
		$balance = '';
		$email1 = $this->encrypt->decode ( $confirm_pass ['email'] );
		$success = $this->signup_model->check_user ( $accno, $email1, $balance );
		if (! empty ( $confirm_pass )) {
			$where_arr = array (
					"email" => $email1,
					"status" => 0 
			);
			$acountdata = $this->db_model->getSelect ( "*", "accounts", $where_arr );
			if ($acountdata->num_rows () > 0) {
				$acountdata = $acountdata->result_array ();
				$user_data = $acountdata [0];
				$updateArr = array (
						"pass_link_status" => 0 
				);
				$this->db->where ( array (
						"email" => $email1 
				) );
				$this->db->update ( "accounts", $updateArr );
				if ($user_data ['pass_link_status'] == '0') {
					$user_data ['success'] = $success;
					$data ['user_data'] = $user_data;
					$this->active ( $user_data, $success );
				} else {
					$data ['email'] = $_GET ['email'];
					$this->load->view ( 'view_confirmpassword', $data );
				}
			}
		}
	}
	function confirmpass() {
		$passwordconf = $_POST;
		$email1 = $this->encrypt->decode ( $passwordconf ['email'] );
		if (! empty ( $passwordconf )) {
			$acountdata = $this->db_model->getSelect ( "*", "accounts", array (
					"email" => $email1 
			) );
			// echo $this->db->last_query();exit;
			if ($acountdata->num_rows () > 0) {
				$acountdata = $acountdata->result_array ();
				$user_data = $acountdata [0];
			}
			$user_data ['password'] = $this->common->encode ( $passwordconf ['password'] );
			$updateArr = array (
					"password" => $user_data ['password'] 
			);
			$where_arr = array (
					"email" => $email1,
					"status" => 0 
			);
			$this->db->where ( $where_arr );
			$this->db->update ( "accounts", $updateArr );
			// $activation = $this->encrypt->encode($user_data['number']);
			$message = base_url ();
			$user_data ['confirm'] = $message;
			$user_data ['password'] = $passwordconf ['password'];
			
			$system_config = common_model::$global_config ['system_config'];
			$balance = $system_config ["balance"];
			
			$this->send_mail ( $user_data ['id'], 'email_forgot_user', $user_data );
			
			$this->successpassword ();
		}
	}
	
	/**
	 *
	 * @param string $temp_name        	
	 */
	function send_mail($account_id, $temp_name, $user_data) {
		$system_config = common_model::$global_config ['system_config'];
		// $screen_path = getcwd()."/cron";
		// $screen_filename = "Email_Broadcast_".strtotime('now');
		// $command = "cd ".$screen_path." && /usr/bin/screen -d -m -S $screen_filename php cron.php BroadcastEmail";
		// exec($command);
		
		$where = array (
				'name' => $temp_name 
		);
		$EmailTemplate = $this->db_model->getSelect ( "*", "default_templates", $where );
		$reseller_id = ($user_data ['reseller_id'] > 0) ? $user_data ['reseller_id'] : 1;
		$where = "accountid IN ('" . $reseller_id . "','1')";
		$this->db->where ( $where );
		$this->db->select ( '*' );
		$this->db->order_by ( 'accountid', 'desc' );
		$this->db->limit ( 1 );
		$invoiceconf = $this->db->get ( 'invoice_conf' );
		$invoiceconf = ( array ) $invoiceconf->first_row ();
		$company_email = $invoiceconf ['emailaddress'];
		$company_website = $invoiceconf ["website"];
		$company_name = $invoiceconf ["company_name"];
		
		$TemplateData = array ();
		
		foreach ( $EmailTemplate->result_array () as $value ) {
			$TemplateData = $value;
			$TemplateData['template'] = str_replace ( "<p>", "", $TemplateData ['template'] );
			$TemplateData['template'] = str_replace ( "</p>", "", $TemplateData ['template'] );
			
			$TemplateData ['subject'] = str_replace ( '#NAME#', $user_data ['first_name'] . " " . $user_data ['last_name'], $TemplateData ['subject'] );
			$TemplateData ['template'] = str_replace ( '#NAME#', $user_data ['first_name'] . " " . $user_data ['last_name'], $TemplateData ['template'] );
			$TemplateData ['template'] = str_replace ( '#NUMBER#', $user_data ['number'], $TemplateData ['template'] );
			$TemplateData ['template'] = str_replace ( '#PASSWORD#', $user_data ['password'], $TemplateData ['template'] );
			$TemplateData ['template'] = str_replace ( '#COMPANY_WEBSITE#', $company_website, $TemplateData ['template'] );
			$TemplateData ['template'] = str_replace ( '#LINK#', $user_data ['confirm'], $TemplateData ['template'] );
			$TemplateData ['template'] = str_replace ( '#COMPANY_EMAIL#', $company_email, $TemplateData ['template'] );
			$TemplateData ['template'] = str_replace ( '#COMPANY_NAME#', $company_name, $TemplateData ['template'] );
		}
		$email_array = array (
				'accountid' => $account_id,
				'subject' => $TemplateData ['subject'],
				'body' => $TemplateData ['template'],
				'from' => $invoiceconf ['emailaddress'],
				'to' => $user_data ['email'],
				'status' => "1",
				// 'attachment'=> $Filenm,
				'template' => '' 
		);
		// echo "<pre>"; print_r($TemplateData); exit;
		$this->db->insert ( "mail_details", $email_array );
		return true;
	}
	function successpassword() {
		$this->load->view ( 'view_successpassword' );
	}
}
?>


