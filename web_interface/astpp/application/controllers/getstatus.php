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
	function reload_freeswitch($command, $server_host = "") {
                $response = '';
                $query = $this->db_model->getSelect ( "*", "freeswich_servers", "" );
                $fs_data = $query->result_array ();
                foreach ( $fs_data as $fs_key => $fs_value ) {
                        $fp = $this->freeswitch_lib->event_socket_create ( $fs_value ["freeswitch_host"], $fs_value ["freeswitch_port"], $fs_value ["freeswitch_password"] );
                        if ($fp) {
                                $response .= $this->freeswitch_lib->event_socket_request ( $fp, $command );
                                fclose ( $fp );
                        }
                }
                return $response;
        }
	function customer_list_status($id) {

		if ($this->session->userdata ( 'user_login' ) == TRUE) {
		$post_data = $this->input->post ();
			if(isset($post_data['table']) && $post_data['table']!=""){
			$post_data ['table'] = $this->common->decode ( $post_data ['table'] );
			
			$data ['status'] = $post_data ['status'] == 'true' ? 0 : 1;
			$status=$post_data ['status'] == 'true' ? 0 : 1;
			$last_modified_date= gmdate ( 'Y-m-d H:i:s' );
			if ($post_data ['table'] == 'accounts') {
				$where = array (
						'id' => $post_data ['id'] 
				);
				$account_data = ( array ) $this->db_model->getSelect ( "*", "accounts", $where )->first_row ();
			}
			if($post_data ['table'] == 'products'){
					$this->db->update ( 'dids', $data, array (
						"product_id" => $post_data ['id'] 
				) );
			}
			if($post_data ['table'] == 'dids'){
					$this->db->update ( 'dids', $data, array (
						"product_id" => $post_data ['id'] 
				) );
					$this->db->update ( 'products', $data, array (
						"id" => $post_data ['id'] 
				) );
			}
			if($post_data ['table'] == 'reseller_products'){
					$this->db->update ( 'reseller_products', $data, array (
						"product_id" => $post_data ['id'] 
				) );
					$this->db->update ( 'products', $data, array (
						"id" => $post_data ['id'] 
				) );
			}
			
			$result = $post_data ['table'] == 'accounts' && $post_data ['id'] == 1 ? null : $post_data ['table'] == 'dids' || $post_data ['table'] == 'accounts' || $post_data ['table'] == 'calltype'? $this->db->update ( $post_data ['table'], $data, array ("id" => $post_data ['id']) ):$post_data ['table'] == 'localization' || $post_data ['table'] == 'call_barring' ? $this->db->update ( $post_data ['table'],array('status'=>$status,'modified_date'=>$last_modified_date), array ("id" => $post_data ['id']) ):$this->db->update ( $post_data ['table'],array('status'=>$status,'last_modified_date'=>$last_modified_date), array ("id" => $post_data ['id']) );
			if ($post_data ['table'] == "ip_map") {
							$this->load->library ( 'freeswitch_lib' );
							$command = "api reloadacl";
							$response = $this->reload_freeswitch ( $command );
					}
				echo TRUE;
			}
			else{
				$this->session->set_flashdata ( 'astpp_notification','Permission Denied!');
				redirect ( base_url () . 'dashboard/' );
			}
		}else{
			redirect ( base_url () . 'dashboard/' );
		}
	}
	
	function get_email_status($id) {
		if ($this->session->userdata ( 'user_login' ) == TRUE) {
		$post_data = $this->input->post ();
		$post_data ['table'] = $this->common->decode ( $post_data ['table'] );
		$data ['is_email_enable'] = $post_data ['is_email_enable'] == 'true' ? 0 : 1;
		$this->db->update ( $post_data ['table'], $data, array (
				"id" => $post_data ['id'] 
		) );
		
		}else{
			redirect ( base_url () . 'dashboard/' );
		}
	}
	
	function get_sms_status($id) {
		if ($this->session->userdata ( 'user_login' ) == TRUE) {
		$post_data = $this->input->post ();
		$post_data ['table'] = $this->common->decode ( $post_data ['table'] );
		$data ['is_sms_enable'] = $post_data ['is_sms_enable'] == 'true' ? 0 : 1;
		$this->db->update ( $post_data ['table'], $data, array (
				"id" => $post_data ['id'] 
		) );
		
		}else{
			redirect ( base_url () . 'dashboard/' );
		}
	}
	
	function get_alert_status($id) {
		if ($this->session->userdata ( 'user_login' ) == TRUE) {
		$post_data = $this->input->post ();
		$post_data ['table'] = $this->common->decode ( $post_data ['table'] );
		$data ['is_alert_enable'] = $post_data ['is_alert_enable'] == 'true' ? 0 : 1;
		$this->db->update ( $post_data ['table'], $data, array (
				"id" => $post_data ['id'] 
		) );
		
		}else{
			redirect ( base_url () . 'dashboard/' );
		}
	}
	
	
}
?>
