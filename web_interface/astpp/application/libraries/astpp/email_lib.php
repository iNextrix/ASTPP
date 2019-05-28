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

class email_lib {
	protected $CI; 
	public $email = '';
	public $smtp = '';
	public $smtp_host = '';
	public $smtp_user = '';
	public $smtp_pass = '';
	public $smtp_port = '';
	public $message = '';
	public $from = '';
	public $sms_body = '';
	public $to_number = '';
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
				'group_title' => 'notifications'
		);
		$query = $this->CI->db_model->getSelect ( "*", "system", $where );
		$query = $query->result_array ();
		foreach ( $query as $key => $val ) {
			$tempvar = strtolower ( $val ['name'] );
			$this->$tempvar = $val ['value'];
		}
	}
	function get_sms_settings(){

		$where = array (
				'group_title' => 'sms'
		);
		$query = $this->CI->db_model->getSelect ( "*", "system", $where );
		$query = $query->result_array ();
		foreach ( $query as $key => $val ) {
			$tempvar = strtolower ( $val ['name'] );
			$this->$tempvar = $val ['value'];
		}
	}
	function push_alert_settings(){
		$where = array (
				'group_title' => 'alert'
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
			$this->sms_body = $template_type ['sms_body'];
			$this->to_number = $detail ['number'];
			$template_type = '';
		}
		if (is_array ( $detail )) {
			$templateinfo = $detail;
			if (isset ( $detail ['email'] )) {
				$this->to = $detail ['email'];
			}
			if (isset ( $templateinfo ['number'] )) {
				$templateinfo ['username'] = $templateinfo ['number'];
			}
			$this->account_id = $templateinfo ['accountid'];
			unset ( $templateinfo ['number'] );
		} else if (! is_array ( $detail ) && $detail_type == '') {
			$templateinfo = $this->get_account_info ( $detail );
		} else {
			$templateinfo = $this->get_info ( $detail, $detail_type );
		}

		if ($this->get_admin_details () && is_array ( $templateinfo ) && isset ( $templateinfo ['first_name'] ) && $templateinfo ['first_name'] != '') {
			$this->message  = html_entity_decode ( $this->message );
			$this->message  = str_replace ( "#COMPANY_EMAIL#", $this->from, $this->message );
			$this->message  = str_replace ( "#COMPANY_NAME#", $this->company_name, $this->message );
			$this->message  = str_replace ( "#COMPANY_WEBSITE#", $this->company_website, $this->message );
			$this->message  = str_replace ( '#NAME#', $templateinfo ['first_name'] . " " . $templateinfo ['last_name'], $this->message );
			$this->sms_body = str_replace ( "#FIRST_NAME#", $templateinfo ['first_name'] , $this->sms_body );
			$this->sms_body = str_replace ( "#COMPANY_NAME#", $this->company_name, $this->sms_body );
			$this->sms_body = str_replace ( "#BALANCE#", $templateinfo ['balance'], $this->sms_body );
			$this->message  = str_replace ( '#USERNAME#', $templateinfo ['username'], $this->message );
			$this->message  = str_replace ( '#PIN#', $templateinfo ['pin'], $this->message );
			$this->message  = str_replace ( '#BALANCE#', $templateinfo ['balance'], $this->message );
			$this->message  = str_replace ( '#PASSWORD#', $templateinfo ['password'], $this->message );
			$this->subject  = str_replace ( "#NAME#", $templateinfo ['first_name'] . " " . $templateinfo ['last_name'], $this->subject );
			$this->subject  = str_replace ( "#COMPANY_NAME#", $this->company_name, $this->subject );
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

	function mail_history($attachment) {
		$send_mail_details = array (
				'from'      => $this->from,
				'to'        => $this->to,
				'subject'   => $this->subject,
				'body'      => $this->message,
				'sms_body'  => $this->sms_body,
				'to_number' => isset ( $this->to_number ) ? $this->to_number : '',
				'accountid' => $this->account_id,
				'status'    => '1',
				'attachment'=> $attachment
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
			$query   = $this->CI->db_model->getSelect ( "*", "mail_details", $where );
			$query   = $query->result_array ();
			$details = $query [0];
		}
		$this->message  = $details ['body'];
		$this->from     = $details ['from'];
		$this->sms_body = $details ['sms_body'];
		$this->to_number  = $details ['to_number'];
		$this->to         = $details ['to'];
		$this->subject    = $details ['subject'];
		$this->account_id = $details ['accountid'];
	}
	function get_smtp_details() {
		if ($this->smtp_port != '' || $this->smtp_host != '' || $this->smtp_user != '' || $this->smtp_pass != '') {
			$config ['protocol'] = "smtp";
			$config ['smtp_host'] = $this->smtp_host;
			$config ['smtp_port'] = $this->smtp_port;
			$config ['smtp_user'] = $this->smtp_user;
			$config ['smtp_pass'] = $this->smtp_pass;
			$config ['charset']   = "utf-8";
			$config ['mailtype']  = "html";
			$config ['newline']   = "\r\n";
			$this->CI->email->initialize ( $config );
		}
	}
	function send_notifications ($template_type, $details, $detail_type = '', $attachment = '', $resend = 0, $mass_mail = 0, $brodcast = 0) {
		$this->send_notifications_email($template_type,$details,$detail_type,$attachment,$resend,$mass_mail,$brodcast);
		$this->send_notifications_sms($template_type,$details,$detail_type,$attachment = '',$resend,$mass_mail,$brodcast,$history_id='');
		$this->push_alert_notifications($template_type,$details,$detail_type,$attachment = '',$resend,$mass_mail,$brodcast,$history_id='');
		$this->update_mail_history ( $details ['history_id'] );
	}

	function send_notifications_sms($template_type, $details, $detail_type = '', $attachment = '', $resend = 0, $mass_mail = 0, $brodcast = 0 , $history_id) {

		$this->get_sms_settings ();
		if (!$this->email) {
			
			if (! $resend) {
				$this->build_template ( $template_type, $details, $detail_type );
			} else {
				$this->set_email_paramenters ( $details );
			}

			if (!$this->sms_notications) {
				$url = 'https://rest.nexmo.com/sms/json?' . http_build_query([
   			    	'api_key'    => $this->sms_api_key,
   			    	'api_secret' => $this->sms_secret_key,
        			'to'         => $this->to_number,
       				'from'       => 'ABC',
   			    	'text'       => $this->sms_body
   				 ]);
				$ch = curl_init($url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				$response = curl_exec($ch);
			}
		}
	}

	function send_notifications_email($template_type, $details, $detail_type = '', $attachment = '', $resend = 0, $mass_mail = 0, $brodcast = 0) {

		$this->get_email_settings ();

		$history_id = "";
		if (array_key_exists("history_id",$details)) {
			$history_id = $details ['history_id'];
		}

		if (!$this->email) {
			
			if (! $resend) {
				$this->build_template ( $template_type, $details, $detail_type );
			} else {
				$this->set_email_paramenters ( $details );
			}
			
			if (! $brodcast) {
				$history_id = $this->mail_history ( $attachment );
			}
			else {
				$history_id = $details ['history_id'];
			}
			if (isset ( $this->from ) && $this->from != '' && isset ( $this->to ) && $this->to != '' && ! $mass_mail) {
				if (! $this->smtp) {
					$this->get_smtp_details ();
				}

				$this->CI->email->from ( $this->from, $this->company_name );
				$this->CI->email->to ( $this->to );
				$this->CI->email->subject ( $this->subject );
				$this->CI->email->subject ( $this->subject );
				$this->CI->email->set_mailtype ( "html" );
				$this->message = nl2br($this->message);
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

				$data = $this->CI->email->send ();

				$mail_data ['from']    = isset ( $this->from ) ? $this->from : '';
				$mail_data ['to']      = isset ( $this->to ) ? $this->to : '';
				$mail_data ['subject'] = isset ( $this->subject ) ? $this->subject : '';
				$this->CI->email->print_debugger_email ( $mail_data, common_model::$global_config ['system_config'] ['mail_log'] );
				$this->CI->email->clear ( true );
				
				return $history_id;
			}
		}
	}

	function send_mail($template_type, $details, $detail_type = '', $attachment = '', $resend = 0, $mass_mail = 0, $brodcast = 0) {
		$this->get_email_settings ();
		if (! $this->email) {
			if (! $resend) {
				$this->build_template ( $template_type, $details, $detail_type );
			} else {
				$this->set_email_paramenters ( $details );
			}
			if (! $brodcast) {
				$history_id = $this->mail_history ( $attachment );
			} else {
				$history_id = $details ['history_id'];
			}
			if (isset ( $this->from ) && $this->from != '' && isset ( $this->to ) && $this->to != '' && ! $mass_mail) {
				if (! $this->smtp) {
					$this->get_smtp_details ();
				}
				$this->CI->email->from ( $this->from, $this->company_name );
				$this->CI->email->to ( $this->to );
				$this->CI->email->subject ( $this->subject );
				$this->message = nl2br($this->message);
				$this->CI->email->message ( $this->message );
				$this->CI->email->sms_body ( $this->sms_body );
				$this->CI->email->to_number ( $this->to_number );
				$this->CI->email->send ();
				$this->CI->email->clear ( true );
				$this->update_mail_history ( $history_id );
			}
		}
	}
	function send_sms() {
		$this->get_sms_settings();
		if (!$this->sms_notications) {
			$accountinfo = $this->CI->session->userdata ( 'accountinfo' );
			$country_code = $this->CI->common->get_field_name('countrycode','countrycode',array ("id" => $accountinfo['country_id']));
			$contact_no=isset($accountinfo['telephone_1'])?$accountinfo['telephone_1']:$accountinfo['telephone_2'];
			$url="https://rest.nexmo.com/sms/json?api_key=".$this->sms_api_key."&api_secret=".$this->sms_secret_key."&from=NEXMO&to=".$country_code."".$contact_no."&text=".$this->sms_content."";
            $ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url); 
			curl_setopt($ch, CURLOPT_VERBOSE, 1); 
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); 
			curl_setopt($ch, CURLOPT_POST, 1); 
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
            $result = curl_exec($ch);
		}	
	}
	function push_alert_notifications($template_type, $details, $detail_type = '', $attachment = '', $resend = 0, $mass_mail = 0, $brodcast = 0 , $history_id) {
		$this->push_alert_settings ();
		if (!$this->alert_notications) {
			$sip_user_name = $details ['sip_user_name'];
			$callkit_token = $details ['callkit_token'];
			$where = array ('username' => $sip_user_name , 'callkit_token' => $callkit_token);
			$dialer_device_info = ( array ) $this->CI->db->get_where( "dialer_device_info", $where )->first_row();
			if (!$this->alert_notications) {
				$this->send_notification_mobile ($dialer_device_info,$details);
			}
		}
	}
	function send_notification_mobile($dialer_device_info='',$details='') {


	if (array_key_exists('mobile_type', $dialer_device_info)) {

		if ($dialer_device_info ['mobile_type'] != '') {

			if ($dialer_device_info ['mobile_type'] == 'Android') {

				$GOOGLE_API_KEY_ANDROID = 
				'AAAAW1cvspA:APA91bHunJKY2-4AqWmudm356ljx-AeICDREbZJkZ5wxuWYD1mSw2bvJ3DN5nDRbFI5iiF-BsCx8ZXK_K78wFJhEAdj6tgsc80E_QsZdvUhDwAO12oVGYgS6gOm5pv-pD0qelNnKztgb';

				$registration_id = $details ['callkit_token'];

				$message_body = array(
							"message" => $details['push_message_body'],
							"registration_ids" => array($registration_id),
							"title" => $details ['subject'],
							"status" => $details ['status_code']
						);

				$fields = array(
					'to'   => $registration_id,
					'data' => $message_body,
				);
				$url = 'https://fcm.googleapis.com/fcm/send';
				$headers = array(
					'Authorization: key=' . $GOOGLE_API_KEY_ANDROID,
					'Content-Type: application/json'
				);
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields ,JSON_FORCE_OBJECT));
				$result = curl_exec($ch);
				if($result === FALSE){
					die('Curl failed: ' . curl_error($ch));
				}
			 	curl_close($ch);
			}

			if ($dialer_device_info ['mobile_type'] == 'ios') {

				$details ['subject'] = str_replace('<p>', '', $details ['subject']);
				$details ['subject'] = str_replace('</p>', '', $details ['subject']);
				$details ['subject'] = str_replace('&nbsp;', '', $details ['subject']);


				$details['push_message_body'] = str_replace('<p>', '', $details['push_message_body']);
				$details['push_message_body'] = str_replace('</p>', '', $details['push_message_body']);
				$details['push_message_body'] = str_replace('&nbsp;', '', $details['push_message_body']);

					$file_path = getcwd()."/".APPPATH . 'controllers/api/certificate/';

				if (Common_model::$global_config ['system_config'] ['ios_push_notification_mode'] == 0) {
					$file_path .= 'live.pem';
					$link = "gateway.push.apple.com";
				}  
				else {
					$file_path .= 'sandbox.pem';
					$link = "gateway.sandbox.push.apple.com";
				}
				$body ['aps'] = array (
						"alert" => array (
								"status" => $details ['status_code'],
								"title"  => $details ['subject'],
								"body"   => trim($details['push_message_body']),
						),
						"badge" => 1 ,
						"sound"  => "default" 
				);
				$ctx = stream_context_create ();
				stream_context_set_option ( $ctx, 'ssl', 'local_cert', $file_path );
				stream_context_set_option ( $ctx, 'ssl', 'passphrase', Common_model::$global_config ['system_config'] ['ios_push_notification_passphrase'] );
				$fp = stream_socket_client ( 'ssl://' . $link . ':2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx );
					
				if ($fp) {
						$apple_expiry     = time () + (90 * 24 * 60 * 60);
						$apple_identifier = 1;
						$payload          = json_encode ( $body );
						$msg              = chr ( 0 ) . pack ( 'n', 32 ) . pack ( 'H*', $details ['callkit_token'] ) . pack ( 'n', strlen ( $payload ) ) . $payload;
						$result           = fwrite ( $fp, $msg, strlen ( $msg ) );
						fclose ( $fp );
					}
				}
			}
		}
	}
}
