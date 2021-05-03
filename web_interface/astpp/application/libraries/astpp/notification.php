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

class notification {
	protected $CI; 
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
	


	function send_notification($notification_template,$accountid,$email_params,$attachment = "",$extra = ""){  
		$error_msg = '';
		$accountinfo = array();
		$invoiceconf = array();
		$notification_template_data = array();
		
		$query = $this->CI->db_model->getSelect ( "*", "accounts", array("id"=>$accountid) );
		$accountinfo = $query->result();
		if(isset($accountinfo) && !empty($accountinfo)){
			$accountinfo = (array)$accountinfo[0];
			$reseller_id = $accountinfo['reseller_id'] > 0 ? $accountinfo['reseller_id'] : 1;
			$where = "accountid IN ('" . $reseller_id . "')";
			$this->CI->db->where ( $where );
			$this->CI->db->select ( '*' );
			$this->CI->db->order_by ( 'accountid', 'desc' );
			$this->CI->db->limit ( 1 );
			$invoiceconf = $this->CI->db->get ( 'invoice_conf' );

			if($invoiceconf->num_rows > 0){ 
				$invoiceconf = ( array ) $invoiceconf->first_row ();
				$query = $this->CI->db_model->getSelect ( "*", "default_templates", array ('name' => $notification_template) );
				$notification_template_data = $query->result_array()[0];
				$notification_template_data['attachment'] = $attachment;
				$this->$notification_template($notification_template_data,$accountinfo,$email_params,$invoiceconf);

				if(Common_model::$global_config ['system_config']['email']==0){
					$this->send_email_notification($notification_template_data,$accountinfo,$invoiceconf);
				}

				if(Common_model::$global_config ['system_config']['account_generate']==0){
					$this->send_sms_notification($notification_template_data,$accountinfo,$invoiceconf);
				}

			}else{
				$error_msg = gettext('counld not find invoice configuration.');
			}	
		}else{
			$error_msg = gettext('unable to find account details');
		}

	}
	
	function send_email_notification($notification_template_data,$accountinfo,$invoiceconf){
		 $send_mail_details = array (
				'from' => $invoiceconf['emailaddress'],
				'to' => $accountinfo['email'],
				'subject' => $notification_template_data['subject'],
				'body' => $notification_template_data['template'],
				'accountid' => $accountinfo['id'],
				'status' => '1',
				'attachment' => $notification_template_data['attachment'],
				'reseller_id' => $accountinfo['reseller_id']
		);
		$this->CI->db->insert ( 'mail_details', $send_mail_details );
		return $this->CI->db->insert_id ();
		
	}
	function send_sms_notification($notification_template_data,$accountinfo,$invoiceconf){
		$numberlength=6;
		$sms_details=$this->get_sms_settings_details();
		$uniq_rendno = rand(pow(10, $numberlength - 1), pow(10, $numberlength) - 1);
		$message = str_replace ( "<p>", "", $notification_template_data['template'] );
		$message = str_replace ( "</p>", "", $notification_template_data['template'] );

		$url="https://rest.nexmo.com/sms/json?api_key='be33a9f1'&api_secret='LPn30y0lCuBHjKk2'&from=NEXMO&to=".$accountinfo['number']."&text=".$message;
            $ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url); 
			curl_setopt($ch, CURLOPT_VERBOSE, 1); 
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); 
			curl_setopt($ch, CURLOPT_POST, 1); 
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
           		$result = curl_exec($ch);
		
			return $uniq_rendno;
	}
	function get_sms_settings_details(){
		$where = array (
				'group_title' => 'notifications'
		);
		$query = $this->db_model->getSelect ( "*", "system", $where );
		$query = $query->result_array ();
		foreach ( $query as $key => $val ) {
			$tempvar = strtolower ( $val ['name'] );
			$this->$tempvar = $val ['value'];
		}
		
	}
	function otp_verification(&$notification_template_data,$accountinfo,$email_params,$invoiceconf){ 

		$email_template = $notification_template_data['template'];
		$subject = $notification_template_data['subject'];
		$sms_template = $notification_template_data['template'];
		$push_alert_template = $notification_template_data['template'];
			$email_template = str_replace ( '#NAME#', $accountinfo['first_name'] . " " . $accountinfo['last_name'], $email_template);
			$email_template = str_replace ( '#otp#',1234,$email_template );
			$email_template = str_replace ( '#COMPANY_NAME#',$invoiceconf['company_name'],$email_template);
			$notification_template_data['template'] = $email_template;

	}

	function email_add_user(&$notification_template_data,$accountinfo,$email_params,$invoiceconf){ 
		$subject = $notification_template_data['subject'];
		$email_template = $notification_template_data['template'];
		$sms_template = $notification_template_data['template'];
		$push_alert_template = $notification_template_data['template'];
			$email_template = str_replace ( '#NAME#', $accountinfo['first_name'] . " " . $accountinfo['last_name'], $email_template);
			$email_template = str_replace ( '#NUMBER#', $accountinfo['number'],$email_template);
			$email_template = str_replace ( '#PASSWORD#', $accountinfo['password'],$email_template);
			$email_template = str_replace ( '#COMPANY_WEBSITE#',$invoiceconf['website'],$email_template);
			$email_template = str_replace ( '#COMPANY_EMAIL#',$invoiceconf['emailaddress'],$email_template);
			$email_template = str_replace ( '#COMPANY_NAME#',$invoiceconf['company_name'],$email_template);
			$notification_template_data['template'] = $email_template;

	}
	
}

