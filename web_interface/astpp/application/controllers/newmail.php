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
class Newmail extends MX_Controller {
	function __construct() {
		$this->load->library ( 'astpp/email_lib' );
		parent::__construct ();
	}
	function index() {
		$data ['account_info'] = $this->session->userdata ['accountinfo'];
		$data ['username'] = $this->session->userdata ( 'user_name' );
		$data ['page_title'] = "Test Mail";
		$this->load->view ( 'view_newmail', $data );
	}
	function customer_mail_result($flag = FALSE) {
		$account_info = $this->session->userdata ['accountinfo'];
		$post_array = $this->input->post ();
		$post_array ['accountid'] = $account_info ['id'];
		$post_array ['history_id'] = 0;
		$post_array ['email'] = $post_array ['to'];
		unset ( $post_array ['to'] );
		$this->email_lib->send_email ( $post_array, $post_array, '', '', 0, 0 );
		$this->thanks ();
	}
	function thanks() {
		$this->load->view ( 'view_mail_response' );
	}
}
?>
