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
class Broadcastemail extends CI_Controller {
	function __construct() {
		parent::__construct ();
		$this->load->model ( "db_model" );
		$this->load->library ( "astpp/email_lib" );
	}
	function broadcast_email() {
		$where = array (
				"status" => "1" 
		);
		$query = $this->db_model->getSelect ( "*", "mail_details", $where );
		if ($query->num_rows () > 0) {
			$account_data = $query->result_array ();
			foreach ( $account_data as $data_key => $account_value ) {
				$account_value ['history_id'] = $account_value ['id'];
				unset ( $account_value ['id'] );
				$this->email_lib->send_notifications ( '', $account_value, '', $account_value ['attachment'], 1, 0, 1 );
			}
		}
	}
}
?>
