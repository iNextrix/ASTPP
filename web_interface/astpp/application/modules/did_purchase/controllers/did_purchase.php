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
class DID_Purchase extends MX_Controller {
	function __construct() {
		parent::__construct ();
		
		//$this->load->helper ( 'template_inheritance' );
		$this->load->library ( 'session' );
		$this->load->library ( 'did_purchase_form' );
		$this->load->library ( 'astpp/form','did_purchase_form' );
		$this->load->library ( 'astpp/permission' );
		$this->load->model ( 'did_purchase_model' );
		//$this->load->library ( 'csvreader' );
		$this->load->library ( 'did_lib' );
		$this->load->library ( 'astpp/order' );
		$this->load->library ('ASTPP_Sms');
		
		if ($this->session->userdata ( 'user_login' ) == FALSE)
			redirect ( base_url () . '/astpp/login' );
	}

	function did_purchase_list() {
		$this->session->set_userdata ( 'advance_search', 0 );
		//$data ['app_name'] = 'ASTPP - Open Source Billing Solution | Manage DIDs | DIDS';
		$data ['username'] = $this->session->userdata ( 'user_name' );
		$this->session->set_userdata ( 'did_purchase_list_search', "" );
		$this->session->set_userdata ( 'did_reseller_id', '');
		$data ['page_title'] = gettext ( 'DIDs Bulk Assign' );
		$this->session->set_userdata ( 'did_search', 0 );
		$data ['search_flag'] = true;
		$data ['grid_fields'] = $this->did_purchase_form->build_did_purchase_list();
		$data ["grid_buttons"] = $this->did_purchase_form->build_grid_buttons ();
		
		$data ['form_search'] = $this->form->build_serach_form ( $this->did_purchase_form->get_search_for_did_purchase());
		$this->load->view ( 'view_did_purchase_list', $data );
	}
	function did_purchase_list_json() { 
		$json_data = array ();
		$count_all = $this->did_purchase_model->getavailable_did_list ( false );
//echo $this->db->last_query(); exit;
		$paging_data = $this->form->load_grid_config ( $count_all, $_GET ['rp'], $_GET ['page'] );
		$json_data = $paging_data ["json_paging"];
		$query = $this->did_purchase_model->getavailable_did_list ( true, $paging_data ["paging"] ["start"], $paging_data ["paging"] ["page_no"] );
// echo $this->db->last_query(); exit;
		$grid_fields = json_decode ($this->did_purchase_form->build_did_purchase_list ());
		$json_data ['rows'] = $this->form->build_grid ( $query, $grid_fields );
		echo json_encode ( $json_data );
	}
	function did_purchase_list_clearsearchfilter() {
		$this->session->set_userdata ( 'advance_search', 0 );
		$this->session->set_userdata ( 'did_purchase_list_search', "" );
		$this->session->set_userdata ( 'did_reseller_id', '');
	}
	function did_purchase_list_search(){
		$ajax_search = $this->input->post ( 'ajax_search', 0 );
		if ($this->input->post ( 'advance_search', TRUE ) == 1) {
			$this->session->set_userdata ( 'advance_search', $this->input->post ( 'advance_search' ) );
			$action = $this->input->post ();

			$this->session->set_userdata ( 'did_reseller_id', $action['reseller_id']);
			// print_r($action); die;
			if(isset($action['reseller_id'])){
				$action['dids.country_id']=$action['country_id'];
				unset ( $action ['country_id'] );
			}
			unset ( $action ['action'] );
			unset ( $action ['advance_search'] );
			unset ($action ['reseller_id']);
			$this->session->set_userdata ( 'did_purchase_list_search', $action );

			//unset ($action ['reseller_id']);

			
			
		}	
		if (@$ajax_search != 1) {
			redirect ( base_url () . 'did_Purchase/did_purchase_list/' );
		}
	}
	function did_purchase_country_change() {
		$account_data = $this->session->userdata("accountinfo");
		$drp_list     = array();
		$country_id   = $_POST['country_id'];
		
		if(isset($country_id) && $country_id!=""){
			$state_list=array();
			$state_list_array=array();
			$this->db->where('province NOT LIKE','');
			$state_list=$this->db_model->getSelect ( "distinct(province)", "dids", array ('country_id' =>$country_id,'parent_id'=>0));
				if($state_list->num_rows () > 0 ){
					$state_list_array = $state_list->result_array();
					foreach($state_list_array as $key=>$val){
						foreach($val as $key1=>$val1){
							$data="<option value=".$val1.">".$val1."</option>";
							
						}	
				}
			}
		}
		echo json_encode($data);
	}
	function did_purchase_state_change() {
		$account_data = $this->session->userdata("accountinfo");
		$drp_list     = array();
		$provience_id   = $_POST['provience_id'];
		
		if(isset($provience_id) && $provience_id!=""){
			$state_list=array();
			$state_list_array=array();
			$this->db->where('city NOT LIKE','');
			$state_list=$this->db_model->getSelect ( "distinct(city)", "dids", array ('province' =>$provience_id,'parent_id'=>0));
			if($state_list->num_rows () > 0 ){
				$state_list_array = $state_list->result_array();
				foreach($state_list_array as $key=>$val){
					foreach($val as $key1=>$val1){
						$data="<option value=".$val1.">".$val1."</option>";
					}	
				}
			}
		}
		echo json_encode($data);
	}
	function did_purchase_add_account() {
		$data ['page_title'] = gettext ( 'Assign Bulk DIDs' );
		$data['accountinfo'] = $this->session->userdata ( 'accountinfo' );
		$data['logtype']=$this->session->userdata('logintype');
		
		$this->load->view ( 'view_did_account_add', $data );
	}
	function did_purchase_account_save(){
		$ids=$this->session->userdata ( 'did_purchase_ids' );
		$data=$this->input->post();
		if($ids != ''){
			$data['ids']=$ids;
			$result = $this->did_purchase_model->update_did_purchase($data);
			if(isset($result)){
				$this->session->set_userdata ( 'did_purchase_ids','');
				$this->session->set_flashdata('astpp_errormsg', 'DIDs Assigned sucessfully!');
				
				redirect ( base_url () . 'did_Purchase/did_purchase_list/' );

			}
		}else{
			echo json_encode ( array (
				"did_errors" => "fail" 
			) );
			exit ();
		}

	}
	function did_purchase_ids(){
		$data=$this->input->post();
		$this->session->set_userdata ( 'did_purchase_ids', $data['ids']);
	}
	
}
?>
 
