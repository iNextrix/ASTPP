<?php
###########################################################################
# ASTPP - Open Source Voip Billing
# Copyright (C) 2004, Aleph Communications
#
# Contributor(s)
# "iNextrix Technologies Pvt. Ltd - <astpp@inextrix.com>"
#
# This program is free software; you can redistribute it and/or
# modify it under the terms of the GNU General Public License
# as published by the Free Software Foundation; either version 2
# of the License, or (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details..
#
# You should have received a copy of the GNU General Public License
# along with this program.  If not, see <http://www.gnu.org/licenses/>
############################################################################
class User extends MX_Controller {

    function User() {
        parent::__construct();
        $this->load->helper('template_inheritance');
        $this->load->helper('form');
        $this->load->library("astpp/form");
        $this->load->library("user_form");
        $this->load->model('Auth_model');
        $this->load->model('Astpp_common');
    }

    function index() {
        if ($this->session->userdata('user_login') == FALSE)
            redirect(base_url() . 'login/login');
            $data['page_title']='Dashboard';
	    $this->load->view('view_user_dashboard', $data);
    }
  function user_recent_payments(){
        $this->load->module('dashboard/dashboard');
        $this->dashboard->user_recent_payments();
  }
  function user_package_data(){
    $accountinfo=$this->session->userdata('accountinfo');
    $json_data=array();
    $this->db->where('pricelist_id',$accountinfo['pricelist_id']);
    $this->db->select('*');
    $result=$this->db->get('packages',10);
    $i=1;
    if($result->num_rows() > 0){
	$json_data[0]['package_name']='Package Name';
	$json_data[0]['includedseconds']='Included Seconds';
	$json_data[0]['status']='Status';
     $result=$result->result_array();
     foreach($result as $data){
	    $json_data[$i]['package_name']=$data['package_name'];
	    $json_data[$i]['includedseconds']=$data['includedseconds'];
	     $json_data[$i]['status']=$this->common->get_status('','',$data['status']);
	    $i++;
     }
    }
    echo json_encode($json_data);
  }
    function user_invoices_data(){
        $accountinfo=$this->session->userdata('accountinfo');
//       echo "<pre>";
//       print_r($accountinfo);
       $this->db->where('accountid',$accountinfo['id']);
       $this->db->select('*');
       $this->db->order_by('invoice_date','desc');
      $result=$this->db->get('invoices',10);
      $json_data=array();
      $gmtoffset=$this->common->get_timezone_offset();
      if($result->num_rows()> 0 ){
	$result=$result->result_array();
	$json_data[0]['type']='Type';
	$json_data[0]['id']='Number';
	$json_data[0]['from_date']='From Date';
	$json_data[0]['invoice_date']='Generated Date';
	$json_data[0]['amount']='Amount';
	$i=1;
	foreach($result as $key=>$data){
	    $json_data[$i]['type']=$data['type'];
	    $json_data[$i]['id']=$data['id'];
	    $json_data[$i]['from_date']=date('Y-m-d H:i:s',strtotime($data['from_date'])+$gmtoffset);
	    $json_data[$i]['invoice_date']=date('Y-m-d H:i:s',strtotime($data['invoice_date'])+$gmtoffset);
	    $json_data[$i]['amount']=$this->common->get_invoice_total('','',$data['id']);
	    $i++;
	}
//  	echo "<pre>";
//  	print_r($json_data);exit;
      }
      echo json_encode($json_data); 
  
  }
  function user_subscription_data(){
      $accountinfo=$this->session->userdata('accountinfo');
/*       echo "<pre>";
       print_r($accountinfo);exit;*/
       $this->db->where('accountid',$accountinfo['id']);
       $this->db->select('*');
       $this->db->order_by('assign_date','desc');
      $result=$this->db->get('charge_to_account',10);
      $json_data=array();
      $gmtoffset=$this->common->get_timezone_offset();
      if($result->num_rows()> 0 ){
       $result=$result->result_array();
       $charge_str=null;
       $charges_arr=array();
       foreach($result as $charges_data){
	$charge_str.=$charges_data['charge_id'].",";
       }
       $charge_str=rtrim($charge_str,",");
       $where = "id IN ($charge_str)";
        $this->db->where($where);
       $this->db->select('id,description');
       
       $charge_result=$this->db->get('charges');
       foreach($charge_result->result_array() as $data){
	$charges_arr[$data['id']]=$data['description'];
       }
	$json_data[0]['charge_id']='Charge Name';
	$json_data[0]['assign_date']='Assign Date';
//	$json_data[0]['payment_date']='Assign Date';
	$i=1;
	foreach($result as $key=>$data){
	    $data['charge_id'] =isset($charges_arr[$data['charge_id']]) ? $charges_arr[$data['charge_id']] :"Anonymous";
	    $json_data[$i]['charge_id']=$data['charge_id'];
	    if($data['assign_date'] != '0000-00-00 00:00:00'){
	        $json_data[$i]['assign_date']=date('Y-m-d H:i:s',strtotime($data['assign_date'])+$gmtoffset);
	    }else{
                $json_data[$i]['assign_date']=$data['assign_date'];
	    }
	    
	    $i++;
	}
// 	echo "<pre>";
// 	print_r($json_data);exit;
      }
      echo json_encode($json_data); 
  }
   function user_edit_account() {
        $this->load->module('accounts/accounts');
        $account_data = $this->session->userdata("accountinfo");
	$entity_name = strtolower($this->common-> get_entity_type('','',$account_data['type']));
        $add_array = $this->input->post();
        
        if ($add_array['id'] != '') {
	    $data['form'] = $this->form->build_form($this->accounts->accounts_form->get_user_form_fields($add_array['id']), $add_array);
            $data['page_title'] = 'Edit '.$entity_name;
            if ($this->form_validation->run() == FALSE) {
                $data['validation_errors'] = validation_errors();
            } else {
                 $this->accounts->accounts_model->edit_account($add_array, $add_array['id']);
                 $accountinfo=$this->session->userdata('accountinfo');
		 if($add_array['id']==$accountinfo['id'] ){
		  $result=$this->db->get_where('accounts',array('id'=>$add_array['id']));
		  $result=$result->result_array();
		  $this->session->set_userdata('accountinfo',$result[0]);
		}
                 $this->session->set_flashdata('astpp_errormsg', ucfirst($entity_name) .' updated successfully!');
		 redirect(base_url() . 'user/user/');
            }
            $this->load->view('view_user_details', $data);
        } else {

            $data['page_title'] = 'Edit '.$entity_name;
            $where = array('id' => $account_data["id"]);
            $account = $this->db_model->getSelect("*", "accounts", $where);
            $data["account_data"] = $account->result_array();

            foreach ($account->result_array() as $key => $value) {
                $editable_data = $value;
            }
            $data['form'] = $this->form->build_form($this->accounts->accounts_form->get_user_form_fields($editable_data['id']), $editable_data);
            $this->load->view('view_user_details', $data);
        }
    }

  function user_didlist() {
        $data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = 'DIDs List';
        $this->load->module('did/did');
        $data['grid_fields'] = $this->did->did_form->build_did_list_for_user();
        $data["grid_buttons"] = array();
	$acc_data = $this->session->userdata("accountinfo");
        $reseller_id=$acc_data['reseller_id'];
        $drp_data = $this->db->query("SELECT id, number FROM dids WHERE accountid = '0' and parent_id='".$reseller_id."'");
        $reseller_data=array();
        foreach ($drp_data->result_array() as $drp_value) {
          $reseller_data[$drp_value["id"]] = $drp_value["number"];
        }
	$data['didlist'] = form_dropdown_all('free_did_list',$reseller_data, '');
        $this->load->view('view_did_list', $data);
    }

    /**
     * -------Here we write code for controller accounts functions account_list------
     * Listing of Accounts table data through php function json_encode
     */
    function user_didlist_json() {
        $account_data = $this->session->userdata("accountinfo");
        $this->load->module('did/did');
        $this->did->user_did($account_data["id"]);
    }

    function user_invoice_list() {
        $data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = 'Invoices List';
        $data['search_flag'] = true;
        $this->session->set_userdata('advance_search', 0);

        $this->load->module('invoices/invoices');
        $data['form_search'] =$this->form->build_serach_form($this->invoices->invoices_form->get_invoice_search_form());
        
        $data['grid_fields'] = $this->invoices->invoices_form->build_invoices_list_for_customer();
        $this->load->view('view_invoices_list', $data);
    }

    function user_invoice_list_json() {
        $account_data = $this->session->userdata("accountinfo");
        $this->load->module('invoices/invoices');
        $this->invoices->user_invoices($account_data["id"]);
    }
    function user_invoice_list_search(){
	$ajax_search = $this->input->post('ajax_search', 0);
        if ($this->input->post('advance_search', TRUE) == 1) {
            $this->session->set_userdata('advance_search', $this->input->post('advance_search'));
            $action = $this->input->post();
            unset($action['action']);
            unset($action['advance_search']);
            $action['from_date'][0]=$action['from_date'][0] ? $action['from_date'][0]." 00:00:00" :'';
 	    $action['invoice_date'][0]=$action['invoice_date'][0] ? $action['invoice_date'][0]." 00:00:00" : '';
            $this->session->set_userdata('invoice_list_search', $action);
        }
        if (@$ajax_search != 1) {
            redirect(base_url() . 'user/user_invoice_list/');
        }
    }
    function user_invoice_list_clearsearchfilter() {
        $this->session->set_userdata('advance_search', 0);
        $this->session->set_userdata('invoice_list_search', "");
    }
    function user_invoice_download($invoiceid){
	$this->load->module('invoices/invoices');
        $this->invoices->invoice_main_download($invoiceid);
    }
    function user_animap_list() {
        $data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = 'Caller Id List';
        $this->session->set_userdata('advance_search', 0);

        $this->load->module('accounts/accounts');
        $data['grid_fields'] = $this->accounts->accounts_form->build_animap_list_for_user();

        $this->load->view('view_animap_list', $data);
    }

    function user_animap_list_json() {
        $account_data = $this->session->userdata("accountinfo");
        $this->load->module('accounts/accounts');
        $this->accounts->user_animap_json($account_data["id"]);
    }
    function user_did_edit($edit_id = '') {
        $data['page_title'] = 'Edit Dids';
        
        $account_data = $this->session->userdata("accountinfo");
        
        $where = array('id' => $edit_id);
        $account = $this->db_model->getSelect("*", "dids", $where);
        foreach ($account->result_array() as $key => $value) {
            $edit_data = $value;
        }
        
        if ($account_data["reseller_id"] != 0){
	    $did_query = $this->db_model->getSelect("*", "reseller_pricing", array("note" => $edit_data['number']));
	    foreach ($did_query->result_array() as $key_reseller => $value_reseller) {
		$edit_data_reseller = $value;
	    }
	    $edit_data['setup'] = $this->common_model->to_calculate_currency($edit_data_reseller['setup'], '', '', false, false);
	    $edit_data['disconnectionfee'] = $this->common_model->to_calculate_currency($edit_data_reseller['disconnectionfee'], '', '', false, false);
	    $edit_data['monthlycost'] = $this->common_model->to_calculate_currency($edit_data_reseller['monthlycost'], '', '', false, false);
	    $edit_data['connectcost'] = $this->common_model->to_calculate_currency($edit_data_reseller['connectcost'], '', '', false, false);
	    $edit_data['cost'] = $this->common_model->to_calculate_currency($edit_data_reseller['cost'], '', '', false, false);
	}else{
	    $edit_data['setup'] = $this->common_model->to_calculate_currency($edit_data['setup'], '', '', false, false);
	    $edit_data['disconnectionfee'] = $this->common_model->to_calculate_currency($edit_data['disconnectionfee'], '', '', false, false);
	    $edit_data['monthlycost'] = $this->common_model->to_calculate_currency($edit_data['monthlycost'], '', '', false, false);
	    $edit_data['connectcost'] = $this->common_model->to_calculate_currency($edit_data['connectcost'], '', '', false, false);
	    $edit_data['cost'] = $this->common_model->to_calculate_currency($edit_data['cost'], '', '', false, false);
	}
        
        
        

        $data['call_type'] = form_dropdown('call_type', $this->common->set_call_type(), $edit_data["call_type"]);        
        $data["didinfo"] = $edit_data;
        $this->load->view('view_user_did_edit', $data);
    }
    

    function user_dids_action($action, $did_id = "") {
        if ($action == "add") {
            $account_data = $this->session->userdata("accountinfo");
            $did_id = $this->input->post("free_did_list", true);
            
            $where_acc = array('id' => $this->session->userdata['accountinfo']['id']);
	    $account = $this->db_model->getSelect("*", "accounts", $where_acc );
	    $response_data = $account->result_array();
            if ($did_id != "") {

		
                if ($account_data["reseller_id"] != 0){
		    $rese_did=$this->db_model->getSelect("*", "dids", array("id" => $did_id));
		    $did_res = $rese_did->result_array();
		    $didid_new=$did_res[0]['number'];
		    
                    $did_query = $this->db_model->getSelect("*", "reseller_pricing", array("note" => $didid_new));
                    $did_arr = $did_query->result_array();
                }else{
                    $did_query = $this->db_model->getSelect("*", "dids", array("id" => $did_id));
                    $did_arr = $did_query->result_array();
		    
                }
                
		$available_bal = $this->db_model->get_available_bal($response_data[0]);

                if($available_bal >= $did_arr[0]["setup"]){
                        $available_bal = $this->db_model->update_balance($did_arr[0]["setup"],$account_data["id"],"debit");
                        $this->add_invoice_data_user($account_data["id"],"did_charge",'DID Purchase',$did_arr[0]["setup"]);
// ,"call_type"=>$this->input->post("call_type", true)
                    $this->db_model->update("dids", array("accountid" => $account_data["id"]), array("id" => $did_id));
                    $this->session->set_flashdata('astpp_errormsg', 'DID purchased Successfully !');
                    redirect(base_url() . "user/user_didlist/");
                }else{
                    $this->session->set_flashdata('astpp_notification', 'Insuffiecient fund to purchase this did');
                    redirect(base_url() . "user/user_didlist/");
                }
            } else {
		$this->session->set_flashdata('astpp_notification', 'Please select DID');
                redirect(base_url() . "user/user_didlist/");
            }
        }
        if($action == "edit"){
            $update_arr = array("call_type"=>$this->input->post("call_type", true),
                                "extensions"=>$this->input->post("extension", true)
                                );
            $this->db_model->update("dids",$update_arr, array("id" =>$this->input->post("didid", true)));
              $this->session->set_flashdata('astpp_errormsg', 'DID Edit successfully.');
            redirect(base_url() . "user/user_didlist/");
        }
        if ($action == "delete") {
	    $account_data = $this->session->userdata("accountinfo");
	        if($account_data["reseller_id"] > 0){	
// 			$accountid_did=$account_data["reseller_id"];
			$accountid_did=0;
		}else{	
			$accountid_did=0;
		}

            $this->db_model->update("dids", array("accountid" => $accountid_did), array("id" => $did_id));
             //$this->session->set_flashdata('astpp_notification', 'DID removed successfully !');
              $this->session->set_flashdata('astpp_notification', 'DID removed successfully.');
            redirect(base_url() . "user/user_didlist/");
        }
    }

    function add_invoice_data_user($accountid,$charge_type,$description,$credit)
    {
	$insert_array = array('accountid' => $accountid, 
			      'charge_type' => $charge_type, 
			      'description' => $description,
			      'credit' => $credit,
			      'charge_id' => '0',
			      'package_id' => '0'
			    );

        $this->db->insert('invoice_item', $insert_array);
         $this->load->module('invoices/invoices');
        $this->invoices->invoices->generate_receipt($accountid,$credit);
        
        return true;
    }
    function user_animap_action($action, $aniid = "") {
        $ani = $this->input->post();
        $new_ani= $ani['ANI'];
         if ($action == "add" && $ani['ANI'] != '') {
             $this->db->where('number',$ani['ANI']);
             $this->db->select('count(id) as count');
             $cnt_result=$this->db->get('ani_map');
             $cnt_result=$cnt_result->result_array();
             $count=$cnt_result[0]['count'];
             if($count == 0 && $ani['ANI'] > 0){
                $accountinfo = $this->session->userdata("accountinfo"); 
 		$insert_arr = array("number" => $new_ani,
				    "accountid" => $accountinfo['id'],
				    "context" => "default");
 		$this->db->insert("ani_map", $insert_arr);
 		$this->session->set_flashdata('astpp_errormsg', 'Add Caller Id Sucessfully!');
 		redirect(base_url() . "user/user_animap_list/");
 	    }
 	    else{
  		$this->session->set_flashdata('astpp_notification', ' Caller Id already Exists.');
 		redirect(base_url() . "user/user_animap_list/");
 	    }
       }
       if ($action == "delete") {
          $this->session->set_flashdata('astpp_notification', 'Caller Id removed sucessfully!');
          $this->db_model->delete("ani_map", array("id" => $aniid));
          redirect(base_url() . "user/user_animap_list/");
       }
    }

    function user_cdrs_report() {
        $data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = 'CDRs Report';
        $data['search_flag'] = true; 
        $this->load->module('reports/reports');
        $data['grid_fields'] = $this->reports->reports_form->build_report_list_for_user();
        $data['form_search'] = $this->form->build_serach_form($this->reports->reports_form->get_user_cdr_form());
        $data['grid_title'] = "CDRs Report";
        $this->load->view('view_report_list', $data);
    }

    function user_cdrs_report_json() {
        $this->load->module('reports/reports');
        $this->reports->user_cdrreport();
    }

    function user_cdrs_report_search() {
        $ajax_search = $this->input->post('ajax_search', 0);
        if ($this->input->post('advance_search', TRUE) == 1) {
            $this->session->set_userdata('advance_search', $this->input->post('advance_search'));
            $action = $this->input->post();
            unset($action['action']);
            unset($action['advance_search']);
            $this->session->set_userdata('user_cdrs_report_search', $action);
        }
        if (@$ajax_search != 1) {
            redirect(base_url() . 'user/user_cdrs_report/');
        }
    }

    function user_cdrs_report_clearsearchfilter() {
        $this->session->set_userdata('advance_search', 0);
        $this->session->set_userdata('account_search', "");
    }

    function user_payment_report() {
        $data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = 'Payment Report';
        $data['search_flag'] = true; 
        $this->load->module('reports/reports');
        $data['grid_fields'] = $this->reports->reports_form->build_payment_report_for_user();
        $data['grid_title'] = "Payment Report";

        $this->load->module('reports/reports');
        $data['form_search'] = $this->form->build_serach_form($this->reports->reports_form->get_user_cdr_payment_form());
	//echo '<pre>'; print_r( $data); exit;
        $this->load->view('view_report_list_payment', $data);
    }

    function user_payment_report_json() {
        $this->load->module('reports/reports');
        $this->reports->user_paymentreport();
    }

    function user_sipdevices() {
        $data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = 'SIP Devices List';
        $data['search_flag'] = true; 
        $this->load->module('freeswitch/freeswitch');
        $data["fs_grid_buttons"] = $this->freeswitch->freeswitch_form->build_grid_buttons_for_user();
        $data['grid_fields'] = $this->freeswitch->freeswitch_form->build_devices_list_for_customer();
        $data['form_search'] =$this->form->build_serach_form($this->freeswitch->freeswitch_form->get_sipdevices_search_form_user());
        $data['grid_title'] = "SIP Devices";
        $this->load->view('view_sipdevices_list', $data);
    }
    function user_sipdevices_json() {
        $account_data = $this->session->userdata("accountinfo");
        $this->load->module('freeswitch/freeswitch');
        $this->freeswitch->customer_fssipdevices_json($account_data["id"]);
    }
    function user_sipdevices_clearsearchfilter() {
        $this->session->set_userdata('advance_search', 0);
        $this->session->set_userdata('fssipdevices_list_search', "");
    }

    function user_sipdevices_search() {
        $ajax_search = $this->input->post('ajax_search', 0);
        if ($this->input->post('advance_search', TRUE) == 1) {
            $this->session->set_userdata('advance_search', $this->input->post('advance_search'));
            $action = $this->input->post();
            unset($action['action']);
            unset($action['advance_search']);
            $this->session->set_userdata('fssipdevices_list_search', $action);
        }
        if (@$ajax_search != 1) {
            redirect(base_url() . 'user/user_sipdevices/');
        }
    }
    function user_fssipdevices_action($action, $id="", $accountid="") {
        $this->load->module('freeswitch/freeswitch');
        if ($action == "delete") {
            $this->freeswitch->freeswitch_model->delete_freeswith_devices($id);
              $this->session->set_flashdata('astpp_notification', 'Sip Device Removed Sucessfully.');
            redirect(base_url() . "user/user_sipdevices/");
        }
        if ($action == "edit") {
            $this->freeswitch->customer_fssipdevices_edit($id, $accountid);
        }
        if ($action == "add") {
            $account_data = $this->session->userdata("accountinfo");
            $this->freeswitch->customer_fssipdevices_add($account_data["id"]);
        }
    }


 function user_opensips() {
        $data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = 'Opensips List';
        $data['search_flag'] = true; 
        $this->load->module('opensips/opensips');
        $data["fs_grid_buttons"] = $this->opensips->opensips_form->build_grid_buttons_for_user();
        $data['grid_fields'] = $this->opensips->opensips_form->user_opensips();
        $data['form_search'] =$this->form->build_serach_form($this->opensips->opensips_form->get_search_opensips_form());
        $data['grid_title'] = "Opensips";
//echo "<pre>"; print_r($data); exit;
        $this->load->view('view_opensips_list', $data);
    }
    function user_opensips_json() {
	$accountinfo = $this->session->userdata("accountinfo");
        $json_data = array();
        $this->load->module('opensips/opensips');
        $count_all = $this->opensips_model->getopensipsdevice_customer_list(false, $accountinfo['id']);
        $paging_data = $this->form->load_grid_config($count_all, $_GET['rp']=10, $_GET['page']=1);
        $json_data = $paging_data["json_paging"];

        $query = $this->opensips_model->getopensipsdevice_customer_list(true, $accountinfo['id'], $paging_data["paging"]["start"], $paging_data["paging"]["page_no"]);
        $grid_fields = json_decode($this->opensips->opensips_form->user_opensips());
        $json_data['rows'] = $this->form->build_grid($query, $grid_fields);

        echo json_encode($json_data);
    }
    function user_opensips_clearsearchfilter() {
        $this->session->set_userdata('advance_search', 0);
        $this->session->set_userdata('opensipsdevice_list_search', "");
    }

    function user_opensips_search() {
       $ajax_search = $this->input->post('ajax_search', 0);
 
        if ($this->input->post('advance_search', TRUE) == 1) {
            $this->session->set_userdata('advance_search', $this->input->post('advance_search'));
            $action = $this->input->post();

            unset($action['action']);
            unset($action['advance_search']);

            $this->session->set_userdata('opensipsdevice_list_search', $action);
        }
        if (@$ajax_search != 1) {
            redirect(base_url() . 'user/user_opensips/');
        }
    }
    function user_opensips_action($action, $id="") {
	$accountinfo = $this->session->userdata("accountinfo");
        $this->load->module('opensips/opensips');
        if ($action == "delete") {
            $this->opensips->opensips_model->delete_opensips_devices($id);
            redirect(base_url() . "user/user_opensips/");
        }
        if ($action == "edit") {
            $this->opensips->customer_opensips_edit($accountinfo['id'],$id);
        }
        if ($action == "add") {
            $this->opensips->customer_opensips_add($accountinfo["id"]);
        }
    }




    
    function user_rates_list() {
        $data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = 'My Rates';
        $data['search_flag'] = true;
        $this->session->set_userdata('advance_search', 0);
        $this->load->module('rates/rates');
        $data["fs_grid_buttons"] = $this->rates->rates_form->build_grid_buttons_for_user();
        $data['grid_fields'] = $this->rates->rates_form->build_inbound_list_for_user();
        $data['form_search'] = $this->form->build_serach_form($this->rates->rates_form->get_user_rates_search_form());
        $this->load->view('view_rates_list', $data);
    }

    function user_rates_list_json() {
        $account_data = $this->session->userdata("accountinfo");
        $this->load->module('rates/rates');
        $this->rates->user_inboundrates_list_json($account_data["id"]);
    }
    function user_payment($action=""){
      if(common_model::$global_config['system_config']['paypal_status'] == 1){
        redirect(base_url() . 'user/user/');  
      }
        $this->load->module("user/payment");
        if($action=="GET_AMT"){
            $amount = $this->input->post("value",true);
            $this->payment->convert_amount($amount);
        }else{
            $this->payment->index();
        }
    }
     function user_convert_amount($amount){
       $amount = $this->common_model->add_calculate_currency($amount,"","",false,false);
       echo number_format($amount,5);//hiten
    }
    function user_change_password()
    {
        $data['username'] = $this->session->userdata('user_name');	
        $data['page_title'] = "Change Password";
        if(isset($_POST) && !empty($_POST)){
            $accountinfo = $this->session->userdata['accountinfo'];
            $pass_count = $this->usermodel->validate_password($_POST["old_pass"],$accountinfo['accountid']);
            if($pass_count>0){
                $this->load->library('email');
                $pass_count = $this->usermodel->update_password($_POST["new_pass"],$accountinfo['accountid']);
                $this->redirect_notification("Your Account Password Updated Successfully!",'/user/change_password/');						
            }else{
                $this->redirect_notification("You have entered wrong password! ",'/user/change_password/');						
            }
        }
        $this->load->view('view_user_change_password',$data);	    
    }	
    function user_ipmap_list(){
        $data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = "IP List";
        $data["account_data"] = $this->session->userdata("accountinfo");      
        $this->load->module('accounts/accounts');
        $data['ipmap_grid_field'] = $this->accounts->accounts_form->build_ipmap_for_user();
        $this->load->view('view_ip_map',$data);
        
    }
    function user_ipmap_json(){
        
        $json_data = array();
        $this->load->module('accounts/accounts');
        $accountdata=$this->session->userdata['accountinfo'];
        $where = array("accountid" => $accountdata['id']);
        $count_all = $this->db_model->countQuery("*", "ip_map", $where);

        $paging_data = $this->form->load_grid_config($count_all, $_GET['rp'], $_GET['page']);
        $json_data = $paging_data["json_paging"];
        $query = $this->db_model->select("*", "ip_map", $where, "id", "ASC", $paging_data["paging"]["page_no"], $paging_data["paging"]["start"]);

        $grid_fields = json_decode($this->accounts_form->build_ipmap_for_user());
        $json_data['rows'] = $this->form->build_grid($query, $grid_fields);

        echo json_encode($json_data);                
    }
    function user_ipmap_action($action, $id=""){
        
        if($action == 'add'){
            $ip = $this->input->post("ip", true);
            if($ip!=""){
            $flag=0;
            $where = array("ip" => $this->input->post('ip'),"prefix" => $this->input->post('prefix'));
            $getdata= $this->db_model->countQuery("*", "ip_map", $where);
            if($getdata==0){
            $insert_arr=  array();
            $insert_arr = array(
                "name"=>$this->input->post("name"),
                "ip"=>$this->input->post("ip"),
                "accountid"=>$this->session->userdata['accountinfo']['id'],
                "pricelist_id"=>$this->session->userdata['accountinfo']['pricelist_id'],
                "prefix"=>$this->input->post('prefix'),
                "context"=>'default'
            );
            $this->db->insert("ip_map", $insert_arr);
            $this->session->set_flashdata('astpp_errormsg', 'IP Map Added Successfully!');
            redirect(base_url() . "user/user_ipmap_list/");
            exit;
            }
            if($getdata>=1){
//                 echo json_encode(array("SUCCESS"=>" Duplication Found!! IP Address and Prefix Combination are Not Unique"));
             $this->session->set_userdata('astpp_notification', 'Duplication Found!! IP Address and Prefix Combination are Not Unique');
             redirect(base_url() . "user/user_ipmap_list/");     
             exit;
            }
        }
        else {
                redirect(base_url() . "user/user_ipmap_list/");  
                exit;
        }
        }
        if ($action == "delete") {
            $this->db_model->delete("ip_map", array("id" => $id));
            $this->session->set_flashdata('astpp_notification', 'IP Map Removed Successfully!');
            redirect(base_url() . "user/user_ipmap_list/");
            exit;
        }
        }
            function user_charges_calc(){
      echo "Still in development";exit;
    }

      	
    function user_rates_export_xls() {
        $this->load->module('rates/rates');
        $this->rates->rates->user_inboundrates_cdr_xls();
    }
    function user_rates_export_pdf() {
        $this->load->module('rates/rates');
        $this->rates->rates->user_inboundrates_cdr_pdf();
    }

    function user_report_export_cdr_xls() {
        $this->load->module('reports/reports');
        $this->reports->reports->userReport_export_cdr_xls();
    }
    function user_report_export_cdr_pdf() {
        $this->load->module('reports/reports');
        $this->reports->reports->userReport_export_cdr_pdf();
    }  
function change_password()
	{	
		$accountinfo =  $this->session->userdata('accountinfo');
		$id = $accountinfo['id'];
			$this->load->model('user_model');
			
		$query = $this->user_model->change_password($id);
			foreach($query as $row)
           		 {
              			 $data['password'] = $row->password;
			}
			$databasepassword = $data['password'];			
//			print_r($databasepassword);
//			print_r( $password);
//			exit;	
			$password = $_POST['oldpassword'];		
			$newpassword = $_POST['newpassword'];
			$conformpassword = $_POST['conformpassword'];
			if($databasepassword == $password)
			{
			
				if($conformpassword == $newpassword)
				{								
					$update = $newpassword;	
					$this->load->model('user_model');
					$this->user_model->change_db_password($update,$id);
					$this->session->set_flashdata('astpp_errormsg', "Password changed Sucessfully....!!!");
					redirect(base_url() . 'user/user/changepassword/');	
	
				}
				else
				{
					$this->session->set_flashdata('astpp_notification', "New Password & Conformpassword not match.");
					redirect(base_url() . 'user/user/changepassword/');	
	
							
				}
			}
			else
			{	
				$this->session->set_flashdata('astpp_notification', "Invalid old passwword.");
				redirect(base_url() . 'user/user/changepassword/');	

						
			
		}


	}
	function changepassword()
	{
	        $data['username'] = $this->session->userdata('user_name');	
	        $data['page_title'] = 'Change Password';
		$this->load->view('view_changepassword',$data);
	}
	
	
	
	 function user_generate_password(){
        echo $this->common->generate_password();
    }
    function user_generate_number($digit){
        echo $this->common->find_uniq_rendno($digit, 'number', 'accounts');
    }
	
	
}
?>
