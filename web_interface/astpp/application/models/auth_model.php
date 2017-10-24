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
class Auth_model extends CI_Model {
	function Auth_model() {
		parent::__construct ();
	}
	/**
	 * -------Here we write code for model auth_model functions verify_login------
	 * Purpose: Validate Login Name and Password.
	 * 
	 * @param
	 *        	$username,$password.
	 * @return integer login user name and password is valid then return true else return false.
	 */
	function verify_login($username, $password) {
		$q = "SELECT COUNT(*) as cnt FROM accounts WHERE (number = '" . $this->db->escape_str ( $username ) . "'";
		$q .= " OR email = '" . $this->db->escape_str ( $username ) . "')";
		$q .= " AND password = '" . $this->db->escape_str ( $password ) . "'";
		$q .= " AND status = 0 AND type IN (1,2,3,4,5,0,-1) AND deleted = 0";
		$query = $this->db->query ( $q );
		if ($query->num_rows () > 0) {
			$row = $query->row ();
			if ($row->cnt > 0) {
				$this->session->set_userdata ( 'user_name', $username );
				return 1;
			} else {
				return 0;
			}
		}
		
		return 0;
	}
}

// end class
?>
