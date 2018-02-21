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
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );

/**
 * Dynamically build forms for display
 */
class email_lib {
	protected $CI; // codeigniter
	public $email = '';
	public $smtp = '';
	public $smtp_host = '';
	public $smtp_user = '';
	public $smtp_pass = '';
	public $smtp_port = '';
	public $message = '';
	public $from = '';
	public $to = '';
	public $subject = '';
	public $company_name = '';
	public $company_website = '';
	public $account_id = '';
	function __construct($library_name = '') {
		$this->CI = & get_instance ();
		$this->CI->load->model ( 'db_model' );
		$this->CI->load->library ( 'email' );
		$this->CI->load->library ( 'session' );
	}
	function get_email_settings() {
		$where = array (
				'group_title' => 'email'
		);
		$query = $this->CI->db_model->getSelect ( "*", "system", $where );
		$query = $query->result_array ();
		foreach ( $query as $key => $val ) {
			$tempvar = strtolower ( $val ['name'] );
			$this->$tempvar = $val ['value'];
		}
	}
	function get_template($type) {
		$where = array (
				'name' => $type
		);
		$query = $this->CI->db_model->getSelect ( "*", "default_templates", $where );
		$query = $query->result ();
		$this->message = $query [0]->template;
		$this->subject = $query [0]->subject;
	}
	function get_account_info($accountid) {
		$where = array (
				'id' => $accountid
		);
		$query = $this->CI->db_model->getSelect ( "*", "accounts", $where );
		$query = $query->result_array ();
		if (isset ( $query [0] ['email'] ) && $query [0] ['email'] != '') {
			$query [0] ['currency_name'] = $this->CI->common->get_field_name ( 'currency', 'currency', $query [0] ['currency_id'] );
			$query [0] ['timezone_name'] = $this->CI->common->get_field_name ( 'gmtzone', 'timezone', $query [0] ['timezone_id'] );
			$query [0] ['country_name'] = $this->CI->common->get_field_name ( 'country', 'countrycode', $query [0] ['country_id'] );
			$this->to = $query [0] ['email'];
			$this->account_id = $query [0] ['id'];
			unset ( $query [0] ['id'] );
			$query [0] ['username'] = $query [0] ['number'];
			unset ( $query [0] ['number'] );
			return $query [0];
		}
		return false;
	}

	/**
	 *
	 * @param string $detail_type
	 */
	function get_info($id, $detail_type) {
		$where = array (
				'id' => $id
		);
		$query = $this->CI->db_model->getSelect ( "*", $detail_type, $where );
		$query = $query->result_array ();
		if (isset ( $query [0] ['accountid'] )) {
			$query = $this->get_account_info ( $query [0] ['accountid'] );
			return $query [0];
		}
		return false;
	}
	function get_admin_details() {
		$where = array ();
		$query = $this->CI->db_model->getSelect ( "*", "invoice_conf", $where );
		$query = $query->result ();
		if (isset ( $query [0]->emailaddress ) && $query [0]->emailaddress != '') {
			$this->company_website = $query [0]->website;
			$this->from = $query [0]->emailaddress;
			$this->company_name = $query [0]->company_name;
			return true;
		}
		return false;
	}
	function build_template($template_type, $detail, $detail_type = '') {
		if (! is_array ( $template_type ))
			$this->get_template ( $template_type );
		else {
			$this->message = $template_type ['message'];
			$this->subject = $template_type ['subject'];
			$template_type = '';
		}
		if (is_array ( $detail )) {
			$templateinfo = $detail;
			if (isset ( $detail ['email'] )) {
				$this->to = $detail ['email'];
			}
			/**
			 * ***
			 * ASTPP 3.0
			 * Email test
			 * ***
			 */
			if (isset ( $templateinfo ['number'] )) {
				$templateinfo ['username'] = $templateinfo ['number'];
			}

			/**
			 * **********
			 */
			$this->account_id = $templateinfo ['accountid'];
			unset ( $templateinfo ['number'] );
		} else if (! is_array ( $detail ) && $detail_type == '') {
			$templateinfo = $this->get_account_info ( $detail );
		} else {
			$templateinfo = $this->get_info ( $detail, $detail_type );
		}

		if ($this->get_admin_details () && is_array ( $templateinfo ) && isset ( $templateinfo ['first_name'] ) && $templateinfo ['first_name'] != '') {
			$this->message = html_entity_decode ( $this->message );
			$this->message = str_replace ( "#COMPANY_EMAIL#", $this->from, $this->message );
			$this->message = str_replace ( "#COMPANY_NAME#", $this->company_name, $this->message );
			$this->message = str_replace ( "#COMPANY_WEBSITE#", $this->company_website, $this->message );
			$this->message = str_replace ( '#NAME#', $templateinfo ['first_name'] . " " . $templateinfo ['last_name'], $this->message );
			$this->message = str_replace ( '#USERNAME#', $templateinfo ['username'], $this->message );
			$this->message = str_replace ( '#PIN#', $templateinfo ['pin'], $this->message );
			$this->message = str_replace ( '#BALANCE#', $templateinfo ['balance'], $this->message );
			$this->message = str_replace ( '#PASSWORD#', $templateinfo ['password'], $this->message );
			$this->subject = str_replace ( "#NAME#", $templateinfo ['first_name'] . " " . $templateinfo ['last_name'], $this->subject );
			$this->subject = str_replace ( "#COMPANY_NAME#", $this->company_name, $this->subject );
			switch ($template_type) {
				case 'email_add_user' :
					$this->message = str_replace ( '#NUMBER#', $templateinfo ['username'], $this->message );
					break;
				case 'email_calling_card' :
					$this->message = str_replace ( '#CARDNUMBER#', $templateinfo ['cardnumber'], $this->message );
					break;
				case 'email_new_invoice' :
					$this->message = str_replace ( '#AMOUNT#', $templateinfo ['amount'], $this->message );
					$this->message = str_replace ( '#INVOICE_NUMBER#', $templateinfo ['id'], $this->message );
					$this->subject = str_replace ( "#INVOICE_NUMBER#", $templateinfo ['id'], $this->subject );
					break;
				case 'email_add_did' :
					$this->message = str_replace ( '#NUNBER#', $templateinfo ['number'], $this->message );
					$this->subject = str_replace ( "#NUNBER#", $templateinfo ['number'], $this->subject );
					break;
				case 'email_remove_did' :
					$this->message = str_replace ( '#NUNBER#', $templateinfo ['number'], $this->message );
					$this->subject = str_replace ( "#NUNBER#", $templateinfo ['number'], $this->subject );
					break;
			}
		}
	}

	/**
	 *
	 * @param string $attachment
	 */
	function mail_history($attachment) {
		$send_mail_details = array (
				'from' => $this->from,
				'to' => $this->to,
				'subject' => $this->subject,
				'body' => $this->message,
				'accountid' => $this->account_id,
				'status' => '1',
				'attachment' => $attachment
		);
		$this->CI->db->insert ( 'mail_details', $send_mail_details );
		return $this->CI->db->insert_id ();
	}
	function update_mail_history($id) {
		$this->CI->db->where ( array (
				'id' => $id
		) );
		$send_mail_details = array (
				'status' => '0'
		);
		$this->CI->db->update ( 'mail_details', $send_mail_details );
	}
	function set_email_paramenters($details) {
		if (! is_array ( $details )) {
			$this->get_admin_details ();
			$where = array (
					'id' => $details
			);
			$query = $this->CI->db_model->getSelect ( "*", "mail_details", $where );
			$query = $query->result_array ();
			$details = $query [0];
		}
		$this->message = $details ['body'];
		$this->from = $details ['from'];
		$this->to = $details ['to'];
		$this->subject = $details ['subject'];
		$this->account_id = $details ['accountid'];
	}
	function get_smtp_details() {
		if ($this->smtp_port == '' || $this->smtp_host == '' || $this->smtp_user == '' || $this->smtp_pass == '')
			exit ();
		$config ['protocol'] = "smtp";
		$config ['smtp_host'] = $this->smtp_host;
		$config ['smtp_port'] = $this->smtp_port;
		$config ['smtp_user'] = $this->smtp_user;
		$config ['smtp_pass'] = $this->smtp_pass;
		$config ['charset'] = "utf-8";
		$config ['mailtype'] = "html";
		$config ['newline'] = "\r\n";
		$this->CI->email->initialize ( $config );
	}
	function send_email($template_type, $details, $detail_type = '', $attachment = '', $resend = 0, $mass_mail = 0, $brodcast = 0) {
		$this->get_email_settings ();
		if (! $this->email) {
			if (! $resend) {
				$this->build_template ( $template_type, $details, $detail_type );
			} else {
				$this->set_email_paramenters ( $details );
			}

			if (! $brodcast)
				$history_id = $this->mail_history ( $attachment );
			else
				$history_id = $details ['history_id'];
			if (isset ( $this->from ) && $this->from != '' && isset ( $this->to ) && $this->to != '' && ! $mass_mail) {
				if (! $this->smtp) {
					$this->get_smtp_details ();
				}
				$this->CI->email->from ( $this->from, $this->company_name );
				$this->CI->email->to ( $this->to );
				$this->CI->email->subject ( $this->subject );
				$this->CI->email->set_mailtype ( "html" );
				eval ( "\$message = \"" . $this->message . "\";" );
				$this->CI->email->message ( $this->message );

				if ($attachment != "") {
					$attac_exp = explode ( ",", $attachment );
					foreach ( $attac_exp as $key => $value ) {
						if ($value != '') {
							$this->CI->email->attach ( getcwd () . "/attachments/" . $value );
						}
						$mail_data ['attachment'] [$key] = $value;
					}
				}
				$this->CI->email->send ();
				$mail_data ['from'] = isset ( $this->from ) ? $this->from : '';
				$mail_data ['to'] = isset ( $this->to ) ? $this->to : '';
				$mail_data ['subject'] = isset ( $this->subject ) ? $this->subject : '';
				if (common_model::$global_config ['system_config'] ['mail_debug'] == 0) {
					$this->CI->email->print_debugger_email ( $mail_data, common_model::$global_config ['system_config'] ['log_path'] );
				}
				$this->CI->email->clear ( true );
				$this->update_mail_history ( $history_id );
			}
		}
	}
	/**
	 * ASTPP 3.0
	 * Add For Signup Module
	 * *
	 */
	function send_mail($template_type, $details, $detail_type = '', $attachment = '', $resend = 0, $mass_mail = 0, $brodcast = 0) {
		$this->get_email_settings ();
		if (! $this->email) {

			if (! $resend) {
				$this->build_template ( $template_type, $details, $detail_type );
			} else {
				$this->set_email_paramenters ( $details );
			}

			if (! $brodcast)
				$history_id = $this->mail_history ( $attachment );
			else
				$history_id = $details ['history_id'];
			if (isset ( $this->from ) && $this->from != '' && isset ( $this->to ) && $this->to != '' && ! $mass_mail) {
				if (! $this->smtp) {
					$this->get_smtp_details ();
				}
				$this->CI->email->from ( $this->from, $this->company_name );
				$this->CI->email->to ( $this->to );
				$this->CI->email->subject ( $this->subject );
				eval ( "\$message = \"" . $this->message . "\";" );
				$this->CI->email->message ( $this->message );
				$this->CI->email->send ();
				$this->CI->email->clear ( true );
				$this->update_mail_history ( $history_id );
			}
		}
	}
}

