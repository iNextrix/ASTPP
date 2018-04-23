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
class simulator_model extends CI_Model {
	function simulator_model() {
		parent::__construct ();
	}
	/* function number_loop(destination_number,code) {
            local number_len = string.len(destination_number)

        	if (code == nil) {
        		code = "pattern"
        	}
            number_loop_str = '(';
            while (number_len  > 0) {
                number_loop_str = number_loop_str.. code.." ='^"..string.sub(destination_number,0,number_len)..".*' OR "
                number_len = number_len-1
            }
            number_loop_str = number_loop_str..code.." ='--')"
        	return number_loop_str

        }
    */
	function getsimulator_list($flag, $start = 0, $limit = 0) {
		$this->db_model->build_search ( 'simulator_list_search' );
		$where = array (
				"status != " => "2" 
		);
		if ($flag) {
			$query = $this->db_model->select ( "*", "outbound_routes", $where, "id", "ASC", $limit, $start );
		} else {
			$query = $this->db_model->countQuery ( "*", "outbound_routes", $where );
		}
		return $query;
	}
	function add_simulator($add_array) {
		unset ( $add_array ["action"] );
		$add_array ['creation_date'] = gmdate ( 'Y-m-d H:i:s' );
		$this->db->insert ( "simulators", $add_array );
		return true;
	}
	function edit_simulator($data, $id) {
		unset ( $data ["action"] );
		$data ['last_modified_date'] = gmdate ( 'Y-m-d H:i:s' );
		$this->db->where ( "id", $id );
		$this->db->update ( "simulators", $data );
	}
	function remove_simulator($id) {
		$this->db->where ( "id", $id );
		$this->db->update ( "simulators", array (
				"status" => 2 
		) );
		$this->db->where ( 'simulator_id', $id );
		$this->db->delete ( 'outbound_routes' );
		return true;
	}
}
