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
class Feedback extends MX_Controller {
	function __construct() {
		parent::__construct ();
	}
	function index() {
		$data ['account_info'] = $this->session->userdata ['accountinfo'];
		$data ['username'] = $this->session->userdata ( 'user_name' );
		$data ['page_title'] = "Feedback";
		$this->load->view ( 'view_feedback', $data );
	}
	function customer_feedback_result($flag = FALSE) {
		if ($flag) {
			$account_info = array ();
			$this->db->where ( "accountid", "1" );
			$res = $this->db->get ( 'invoice_conf' );
			if ($res->num_rows () > 0) {
				$masterdata = $res->result_array ();
				$account_info = $masterdata ['0'];
				
				$company_name = $account_info ['company_name'];
				$address = $account_info ['address'];
				$city = $account_info ['city'];
				$province = $account_info ['province'];
				$country = $account_info ['country'];
				$zipcode = $account_info ['zipcode'];
				$telephone = $account_info ['telephone'];
				$fax = $account_info ['fax'];
				$emailaddress = $account_info ['emailaddress'];
				$website = $account_info ['website'];
				
				$data = array (
						"name" => "Admin",
						"company_name" => $company_name,
						"address" => $address,
						"city" => $city,
						"province" => $province,
						"country" => $country,
						"zipcode" => $zipcode,
						"telephone" => $telephone,
						"fax" => $fax,
						"emailaddress" => $emailaddress,
						"website" => $website,
						"serverip" => $_SERVER ['SERVER_NAME'],
						"FLAG" => "TRUE" 
				);
			}
		} else {
			$account_info = array ();
			$this->db->where ( "type", "-1" );
			$res = $this->db->get ( 'accounts' );
			if ($res->num_rows () > 0) {
				$masterdata = $res->result_array ();
				$account_info = $masterdata ['0'];
				
				$name = $_REQUEST ['name'] = "Admin";
				$email = $_REQUEST ['email'] = $account_info ['email'];
				$feedback = $_REQUEST ['feedback'];
				$first_name = $account_info ['first_name'];
				$last_name = $account_info ['last_name'];
				$city = $account_info ['city'];
				$telephone_1 = $account_info ['telephone_1'];
				$account_email = $account_info ['email'];
				$company_name = $account_info ['company_name'];
				$address_1 = $account_info ['address_1'];
				$address_2 = $account_info ['address_2'];
				$telephone_2 = $account_info ['telephone_2'];
				$province = $account_info ['province'];
				
				$data = array (
						"name" => $name,
						"email" => $email,
						"feedback" => $feedback,
						"first_name" => $first_name,
						"last_name" => $last_name,
						"city" => $city,
						"telephone_1" => $telephone_1,
						"account_email" => $account_email,
						"company_name" => $company_name,
						"address_1" => $address_1,
						"address_2" => $address_2,
						"telephone_2" => $telephone_2,
						"province" => $province,
						"serverip" => $_SERVER ['SERVER_ADDR'],
						"FLAG" => "FALSE" 
				);
				
				$data_new = json_encode ( $data );
			}
		}
		$ch = curl_init ();
		curl_setopt ( $ch, CURLOPT_URL, 'http://feedback.astppbilling.org/feedback.php' );
		curl_setopt ( $ch, CURLOPT_CUSTOMREQUEST, 'POST' );
		curl_setopt ( $ch, CURLOPT_HEADER, 1 );
		curl_setopt ( $ch, CURLOPT_POST, 1 );
		curl_setopt ( $ch, CURLOPT_VERBOSE, 1 );
		curl_setopt ( $ch, CURLOPT_FOLLOWLOCATION, 1 );
		curl_setopt ( $ch, CURLOPT_FRESH_CONNECT, 1 );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt ( $ch, CURLINFO_HEADER_OUT, 1 );
		curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data );
		
		$response = curl_exec ( $ch );
		if (! $flag)
			redirect ( base_url () . 'feedback/thanks' );
	}
	function thanks() {
		$this->load->view ( 'view_feedback_response' );
	}
}
?>
