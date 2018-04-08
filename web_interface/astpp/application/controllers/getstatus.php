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
class Getstatus extends MX_Controller {
	function __construct() {
		parent::__construct ();
		$this->load->model ( "db_model" );
		$this->load->library ( "astpp/common" );
	}
	function customer_list_status($id) {
		$post_data = $this->input->post ();
		if(!empty($post_data) && isset($post_data['table'])){
			$post_data ['table'] = $this->common->decode ( $post_data ['table'] );
			$data ['status'] = $post_data ['status'] == 'true' ? 0 : 1;
			if ($post_data ['table'] == 'accounts') {
				$where = array (
						'id' => $post_data ['id'] 
				);
				$account_data = ( array ) $this->db_model->getSelect ( "*", "accounts", $where )->first_row ();
			}
			$result = $post_data ['table'] == 'accounts' && $post_data ['id'] == 1 ? null : $this->db->update ( $post_data ['table'], $data, array (
					"id" => $post_data ['id'] 
			) );
			echo TRUE;
		}else{
			redirect(base_url()."dashboard/");
		}	
	}
}
?>
