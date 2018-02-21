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

class Accounts extends MX_Controller {

    function Accounts() {
        parent::__construct();
        $this->load->helper('template_inheritance');
        
        $this->load->library('accounts_form');
        $this->load->library('astpp/form');

        $this->load->model('common_model');
        $this->load->library('session');
       // echo 'saddsa1';
        $this->load->helper('form');
        $this->load->model('accounts_model');
      //  echo 'saddsa';exit;
        $this->load->model('Astpp_common');
        $this->load->config('accounts_config');
        $this->protected_pages = array('account_list');
        if ($this->session->userdata('user_login') == FALSE)
            redirect(base_url() . '/login/login');
    }

function customer_export_cdr_xls() {
        $query = $this->accounts_model->get_customer_Account_list(true, '', '', true);
        $customer_array[] = array("Account", "First Name", "Last Name","Company",  "Entity Type", "Rate Group ","Account type",  "Balance", "Credit Limit","Creation", "First Used" ,"Expiry" , "Status");
        if ($query->num_rows() > 0) {

            foreach ($query->result_array() as $row) {
                        $customer_array[] = array(
			$row['number'],
			$row['first_name'],
			$row['last_name'],
			$row['company_name'],
			$this->common->get_entity_type('','',$row['type']),
			$this->common->get_field_name('name','pricelists',$row['pricelist_id']),
			$this->common->get_account_type('','',$row['posttoexternal']),
			$this->common_model->calculate_currency($row['balance']),
                        $this->common_model->calculate_currency($row['credit_limit']),
			$row['creation'],
			$row['first_used'],
			$row['expiry'],
			$this->common->get_status('','',$row['status']),			
                    );
                
            }
        }
//echo "<pre>"; print_r($customer_array); exit;
        $this->load->helper('csv');
        array_to_csv($customer_array, 'Customer_' . date("Y-m-d") . '.csv');
    }

function reseller_export_cdr_xls() {
        $query = $this->accounts_model->get_reseller_Account_list(true, '', '', true);
        $customer_array[] = array("Account", "First Name", "Last Name","Company","Rate Group ","Account type",  "Balance", "Credit Limit", "Status");
        if ($query->num_rows() > 0) {

            foreach ($query->result_array() as $row) {
                        $customer_array[] = array(
			$row['number'],
			$row['first_name'],
			$row['last_name'],
			$row['company_name'],
			//$this->common->get_entity_type('','',$row['type']),
			$this->common->get_field_name('name','pricelists',$row['pricelist_id']),
			$this->common->get_account_type('','',$row['posttoexternal']),
			$this->common_model->calculate_currency($row['balance']),
                        $this->common_model->calculate_currency($row['credit_limit']),
			$this->common->get_status('','',$row['status']),			
                    );
                
            }
        }
        $this->load->helper('csv');
        array_to_csv($customer_array, 'Reseller_' . date("Y-m-d") . '.csv');
    }


    function customer_add($type = 0) {
	$entity_type =strtolower($this->common->get_entity_type('','',$type));
        $data['username'] = $this->session->userdata('user_name');
        $data['flag'] = 'create';
        $data['page_title'] = 'Create '.$entity_type;


	$data['country_id']=Common_model::$global_config['system_config']['country'];
	//$data['currency_id']=Common_model::$global_config['system_config']['base_currency'];
	$data['currency_id']=$this->common->get_field_name('id', 'currency', array('currency'=>Common_model::$global_config['system_config']['base_currency']));
	$data['timezone_id']=Common_model::$global_config['system_config']['default_timezone'];

	        $data['form'] = $this->form->build_form($this->accounts_form->get_customer_form_fields($entity_type), '');
        if(!$data['timezone_id'])
        {
                $data['timezone_id']=1;
        }
        if( !$data['currency_id'] )
        {
                $data['currency_id']=1;
        }
        if(!$data['country_id'])
        {
                $data['country_id']=1;
        }
        $data['entity_name']=$entity_type;
        $this->load->view('view_accounts_create', $data);
    }
        function customer_bulk_creation() {
	$data['country_id']=Common_model::$global_config['system_config']['country'];
	//$data['currency_id']=Common_model::$global_config['system_config']['base_currency'];
	//$data['country_id']=$this->common->get_field_name('id', 'countrycode', array('country'=>ucfirst(Common_model::$global_config['system_config']['country'])));
	$data['currency_id']=$this->common->get_field_name('id', 'currency', array('currency'=>Common_model::$global_config['system_config']['base_currency']));
	$data['timezone_id']=Common_model::$global_config['system_config']['default_timezone'];
        if(!$data['timezone_id'])
        {
                $data['timezone_id']=1;
        }
        if( !$data['currency_id'] )
        {
                $data['currency_id']=1;
        }
        if(!$data['country_id'])
        {
                $data['country_id']=1;
        }
            $data['page_title'] = 'Create Bulk Customer';
            $data['username'] = $this->session->userdata('user_name');
            $data['page_title'] = 'Mass Customer';
            $data['form'] = $this->form->build_form($this->accounts_form->customer_bulk_generate_form(), '');
            $this->load->view('view_bulk_account_creation', $data);
    }

      function customer_bulk_save() {
        $error_flag=false;
        $add_array = $this->input->post();
        $logintype = $this->session->userdata('logintype');
        if (!empty($add_array) && isset($add_array)) {
            $currentlength=$this->accounts_model->get_max_limit($add_array);
    	    if ($logintype == 1 || $logintype == 5) {
                $account_data = $this->session->userdata("accountinfo");
                $add_array['reseller_id'] = $account_data['id'];
            } else {
                $add_array['reseller_id'] = "0";
            }
            $data['page_title'] = 'Create Bulk Customer';
            $data['form'] = $this->form->build_form($this->accounts_form->customer_bulk_generate_form(), $add_array);
            if ($this->form_validation->run() == FALSE) {
                $data['validation_errors'] = validation_errors();
                echo $data['validation_errors'];
                exit;
            }
            if($add_array['account_length']<=strlen($add_array['prefix'])){
                 echo json_encode(array("account_length_error"=>"Please Enter Proper Account Length."));
                 exit;      
            }
             if($currentlength > 0 && $add_array['count'] > $currentlength){
                    echo json_encode(array("count_error"=>"You Can Create Maximum ".$currentlength." accounts with ".$add_array['prefix']." prefix"));
                    exit;      
           }else{
                $this->accounts_model->bulk_insert_accounts($add_array);
                echo json_encode(array("SUCCESS" => "Bulk customer generate successfully!"));
                exit;
            }
        } else {
            redirect(base_url()."accounts/customer_list/");
        }
    }
    function customer_invoice_option($value =false){
        $sweepid = $this->input->post("sweepid",true);
        $invoice_dropdown = $this->common->set_invoice_option($sweepid,"","",$value);
        echo $invoice_dropdown;
    }
    function validate_customer_data($data){
        $id = "";
        if(isset($data["id"]) && $data["id"] != ""){
            $id = $data["id"];
        }
	$where = array("email"=>$data["email"]);
	$email_flag = $this->accounts_model->account_authentication($where,$id);
	if($email_flag == 0){
	    return "1";
	}else{
            return "Duplicate Email Address Found Email Must Be Unique.";
        }
        return "0";
    }
    function customer_edit($edit_id = '') {
        if ($this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 5) {
            $account_data = $this->session->userdata("accountinfo");
            $reseller = $account_data['id'];
        } else {
            $reseller = "0";
        }
        

        $where = array('id' => $edit_id, "reseller_id" => $reseller);
        $account = $this->db_model->getSelect("*", "accounts", $where);

        if ($account->num_rows > 0) {
	    $account_data =$account->result_array();
	    $entity_type =strtolower($this->common->get_entity_type('','',$account_data[0]['type']));
	    $data['invoice_date']=$account_data[0]['invoice_day'];
            $data["account_data"] = $account_data;
            $data["ipmap_grid_field"] = json_decode($this->accounts_form->build_ip_list_for_customer($edit_id,$entity_type));
            $data["animap_grid_field"] = json_decode($this->accounts_form->build_animap_list_for_customer($edit_id));
            $data['chargelist'] = form_dropdown('applayable_charge', $this->Astpp_common->list_applyable_charges($edit_id), '');

            $this->load->module('charges/charges');
            $data['charges_grid_field'] = $this->charges->charges_form->build_charges_list_for_customer($edit_id,$entity_type);

            $this->load->module('rates/rates');
            $data['pattern_grid_fields'] = $this->rates->rates_form->build_pattern_list_for_customer($edit_id);
            $data['pattern_grid_buttons'] = $this->rates->rates_form->set_pattern_grid_buttons($edit_id);

            $this->load->module('freeswitch/freeswitch');
            $data["fs_grid_buttons"] = $this->freeswitch->freeswitch_form->fsdevices_build_grid_buttons($edit_id);
            $data['sipiax_grid_field'] = $this->freeswitch->freeswitch_form->build_devices_list_for_customer();

            $this->load->module('opensips/opensips');
            $data["opensips_grid_buttons"] = $this->opensips->opensips_form->opensips_customer_build_grid_buttons($edit_id);
            $data['opensips_grid_field'] = $this->opensips->opensips_form->opensips_customer_build_opensips_list($edit_id);

            $data['ip_pricelist'] = form_dropdown('pricelist_id', $this->db_model->build_dropdown("id,name", "pricelists", "where_arr",array("reseller_id"=>"0","status"=>'1')), '');

            $this->load->module('did/did');
            $data['did_grid_fields'] = $this->did->did_form->build_did_list_for_customer($edit_id,$entity_type);
          
	    $result_did_final=array();
	    $reseller_data =array();
	    
	    if ($this->session->userdata('logintype') == 1) {
                $acc_data = $this->session->userdata("accountinfo");
                
                  $result_did = $this->db->query("SELECT id, number FROM dids WHERE accountid = '0' and parent_id='".$acc_data['id']."'");
                  foreach ($result_did->result_array() as $drp_value) {
                    $result_did_final[$drp_value["id"]] = $drp_value["number"];
                  }
            } else {
		
		$result_did = $this->db->query("SELECT id, number FROM dids WHERE accountid = '0' and parent_id='0'");

		foreach($result_did->result_array()  as $key => $value_did)
		{
		  $result_did_final[$value_did['id']]=$value_did['number'];
		}
            }
            $data['didlist'] = form_dropdown_all('free_did_list', $result_did_final, '');
            $this->load->module('invoices/invoices');
            $data['invoice_grid_fields'] = $this->invoices->invoices_form->build_invoices_list_for_admin();

            $this->load->module('reports/reports');
            $data['cdrs_grid_fields'] = $this->reports->reports_form->build_report_list_for_user();

            foreach ($account->result_array() as $key => $value) {
                $edit_data = $value;
            }
            //Purpose : Get assigned taxes value for customer
            $taxes_data  = $this->db_model->getSelect("group_concat(taxes_id) as taxes_id","taxes_to_accounts", array("accountid" => $edit_id));
            if(isset($taxes_data) && $taxes_data->num_rows() > 0)
            {
		 $taxes_data=$taxes_data->result_array();
		 $edit_data["tax_id"] =  explode(",",$taxes_data[0]['taxes_id']);
            }
            //Completed
            $entity_name = strtolower($this->common-> get_entity_type('','',$edit_data['type']));
            
            $data['page_title'] = 'Edit '.ucfirst($entity_name);
            $data['form'] = $this->form->build_form($this->accounts_form->get_customer_form_fields($entity_name,$edit_id), $edit_data);
            $data['entity_name']=$entity_name;
            
            
            
            $this->load->view('view_customer_details', $data);
        } else {
            redirect(base_url() . 'accounts/customer_list/');
        }
    }

    function customer_save($add_array =false) {
        $add_array = $this->input->post();
        $entity_name = strtolower($this->common-> get_entity_type('','',$add_array['type']));
//         echo $entity_name;exit;
        $data['form'] = $this->form->build_form($this->accounts_form->get_customer_form_fields($entity_name,$add_array['id']), $add_array);
        if ($add_array['id'] != '') {
	    $data['page_title'] = 'Edit '.$this->common-> get_entity_type('','',$add_array['type']);
            if ($this->form_validation->run() == FALSE) {
                $data['validation_errors'] = validation_errors();
            } else {
		  $add_array['credit_limit'] = $this->common_model->add_calculate_currency($add_array['credit_limit'], '', '', false, false);
                 //Purpose : Get assigned taxes value for customer
		  
		  $query = $this->accounts_model->remove_all_account_tax($add_array['id']);
		  if(isset($add_array['tax_id']))
		  {
		      foreach($add_array['tax_id'] as $key=>$val)
		      {
			    $data1 = array(
				'accountid' => $add_array['id'],
				'taxes_id' => $val,
			    );
			    $this->accounts_model->add_account_tax($data1);
		      }
		      unset($add_array['tax_id']);
		  }
		  //Completed
		  unset($add_array['posttoexternal']);
		  $this->accounts_model->edit_account($add_array, $add_array['id']);
		  $this->session->set_flashdata('astpp_errormsg', ucfirst($entity_name).' updated successfully!');
		  redirect(base_url() . 'accounts/customer_list/');
		  exit;
            }
            $data["account_data"]["0"] = $add_array;
	    $edit_id = $add_array["id"];
            $data["ipmap_grid_field"] = json_decode($this->accounts_form->build_ip_list_for_customer($edit_id, "customer"));
            $data["animap_grid_field"] = json_decode($this->accounts_form->build_animap_list_for_customer($edit_id));
            $data['chargelist'] = form_dropdown('applayable_charge', $this->Astpp_common->list_applyable_charges($edit_id), '');

            $this->load->module('charges/charges');
            $data['charges_grid_field'] = $this->charges->charges_form->build_charges_list_for_customer($edit_id, "customer");

            $this->load->module('rates/rates');
            $data['pattern_grid_fields'] = $this->rates->rates_form->build_pattern_list_for_customer($edit_id);
            $data['pattern_grid_buttons'] = $this->rates->rates_form->set_pattern_grid_buttons($edit_id);

            $this->load->module('freeswitch/freeswitch');
            $data["fs_grid_buttons"] = $this->freeswitch->freeswitch_form->fsdevices_build_grid_buttons($edit_id);
            $data['sipiax_grid_field'] = $this->freeswitch->freeswitch_form->build_devices_list_for_customer();

            $this->load->module('opensips/opensips');
            $data["opensips_grid_buttons"] = $this->opensips->opensips_form->opensips_customer_build_grid_buttons($edit_id);
            $data['opensips_grid_field'] = $this->opensips->opensips_form->opensips_customer_build_opensips_list($edit_id);

            $data['ip_pricelist'] = form_dropdown('ip_pricelist', $this->db_model->build_dropdown("id,name", "pricelists", "reseller_id", "0"), '');

            $this->load->module('did/did');
            $data['did_grid_fields'] = $this->did->did_form->build_did_list_for_customer($edit_id, "customer");
            if ($this->session->userdata('logintype') == 1) {
                $acc_data = $this->session->userdata("accountinfo");
                $reseller_data =array();
                $table = "reseller_pricing";
                $field = "dids.id,reseller_pricing.note";
                $where = 'dids.accountid = "0" AND reseller_pricing.reseller_id ='.$acc_data['id'];
                $jionTable = "dids";
                $jionCondition = "reseller_pricing.note = dids.number";
                  $drp_data = $this->db_model->getJionQuery($table, $field, $where, $jionTable, $jionCondition,'',100,0);
// echo $this->db->last_query(); exit;
                  foreach ($drp_data->result_array() as $drp_value) {
                    $reseller_data[$drp_value["id"]] = $drp_value["note"];
                  }
//                   echo "<pre>";print_r($reseller_data);exit;
                  $data['didlist'] = form_dropdown('free_did_list',$reseller_data, '');
            } else {
                $data['didlist'] = form_dropdown('free_did_list', $this->db_model->build_dropdown("id,number", "dids", "accountid", "0"), '');
            }
            $this->load->module('invoices/invoices');
            $data['invoice_grid_fields'] = $this->invoices->invoices_form->build_invoices_list_for_admin();

            $this->load->module('reports/reports');
            $data['cdrs_grid_fields'] = $this->reports->reports_form->build_report_list_for_user();
            $data['entity_name']=$entity_name;
            $this->load->view('view_customer_details', $data);
        } else {
            $data['page_title'] = 'Create '.$entity_name;
            if ($this->form_validation->run() == FALSE) {
                $data['validation_errors'] = validation_errors();
            } else {
		  $add_array['credit_limit'] = $this->common_model->add_calculate_currency($add_array['credit_limit'], '', '', false, false);
		  $last_id=$this->accounts_model->add_account($add_array);
		  
		  if(isset($add_array['tax_id']))
		  {
		      foreach($add_array['tax_id'] as $key=>$val)
		      {
			    $data1 = array(
				'accountid' => $last_id,
				'taxes_id' => $val,
			    );
			    $this->accounts_model->add_account_tax($data1);    
		      }
		      unset($add_array['tax_id']);
		  }
		  
		  $this->session->set_flashdata('astpp_errormsg', ucfirst($entity_name).' added successfully!');
		  redirect(base_url() . 'accounts/customer_list/');
		  exit;
            }
	    $this->load->view('view_accounts_create', $data);
        }
    }

    function customer_list_search() {
        $ajax_search = $this->input->post('ajax_search', 0);

        if ($this->input->post('advance_search', TRUE) == 1) {
            $this->session->set_userdata('advance_search', $this->input->post('advance_search'));
            $action = $this->input->post();
            unset($action['action']);
            unset($action['advance_search']);
        if(isset($action['balance']['balance']) && $action['balance']['balance']!=''){
		     $action['balance']['balance']=$this->common_model->add_calculate_currency($action['balance']['balance'], "", '', false, false);
	    }
	    if(isset($action['credit_limit']['credit_limit']) && $action['credit_limit']['credit_limit']!=''){
	       	$action['credit_limit']['credit_limit']=$this->common_model->add_calculate_currency($action['credit_limit']['credit_limit'], "", '', false, false);
	    }
            $this->session->set_userdata('customer_list_search', $action);
        }
        if (@$ajax_search != 1) {
            redirect(base_url() . 'accounts/customer_list/');
        }
       //  echo '<pre>'; print_r($action); exit;
         
    }
    function customer_list_clearsearchfilter() {
        $this->session->set_userdata('advance_search', 0);
        $this->session->set_userdata('customer_list_search', "");
    }
    function customer_payment_process_add($id = '') {
        $account = $this->accounts_model->get_account_by_number($id);
        $currency = $this->accounts_model->get_currency_by_id($account['currency_id']);
        $data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = 'Process Payment';
        $data['form'] = $this->form->build_form($this->accounts_form->get_customer_payment_fields($currency['currency'], $account['number'], $currency['currency'], $id), '');
        $this->load->view('view_accounts_process_payment', $data);
    }

        function customer_payment_save($id = '') {
	$post_array = $this->input->post();
	$data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = 'Process Payment';
        $account = $this->accounts_model->get_account_by_number($post_array['id']);
        $currency = $this->accounts_model->get_currency_by_id($account['currency_id']);
        $data['form'] = $this->form->build_form($this->accounts_form->get_customer_payment_fields($currency['currency'], $account['number'], $currency['currency'], $id), '');
	if ($this->form_validation->run() == FALSE) {
		    $data['validation_errors'] = validation_errors();
		    echo $data['validation_errors'];
		    exit;
	} else {
            $post_array['credit'] = $this->common_model->add_calculate_currency($post_array['credit'], "", '', false, false);
            $logintype = $this->session->userdata('logintype');
            $username = $this->session->userdata('username');
	    $login_user_data = $this->session->userdata("accountinfo");
            $accountinfo = $this->accounts_model->get_account_by_number($post_array['id']);
            if ($logintype == 1 || $logintype == 5) {
                if ($accountinfo['reseller_id'] == $login_user_data["id"]) {
                     $response = $this->accounts_model->account_process_payment($post_array);
                     if($post_array['payment_type']== 1 && $account['posttoexternal']==0){
                      $this->load->module('invoices/invoices');
		      $this->invoices->invoices->generate_receipt($post_array['id'],$post_array['credit']);
                     }
                     echo json_encode(array("SUCCESS"=> "Account refilled successfully!"));
		     exit;
		  }
		 else{
		     echo json_encode(array("SUCCESS"=> "You are not allowed to add amount to this account."));
                     exit;
                }
            } else {
            	//echo '<pre>'; print_r($post_array); exit;
                $response = $this->accounts_model->account_process_payment($post_array);
                if($post_array['payment_type']== 1 && $account['posttoexternal']==0){
		    $this->load->module('invoices/invoices');
		    $invoice_id=$this->invoices->invoices->generate_receipt($post_array['id'],$post_array['credit']);
/*
*
* Purpose : Solve issue of showing number instead of id when post charge added and showing credit instead of debit
* Version 2.1
*
*/
		    $insert_arr = array("accountid" => $post_array['id'],
				    "description" => trim($post_array['notes']),
				    "debit" => $post_array['credit'],
				    "created_date" => gmdate("Y-m-d H:i:s"), 
				    "charge_type" => "post_charge",
				    "invoiceid"=>$invoice_id
				    );
		    $this->db->insert("invoice_item", $insert_arr);
                }else{
            		    $insert_arr = array("accountid" => $post_array['id'],
				    "description" => trim($post_array['notes']),
				    "debit" => $post_array['credit'],
				    "created_date" => gmdate("Y-m-d H:i:s"), 
				    "charge_type" => "post_charge",
				    "invoiceid"=>0
				    );
			    $this->db->insert("invoice_item", $insert_arr);  
		}
	        $message = $post_array['payment_type']== 0 ? "Recharge successfully!" : "Post charge applied successfully.";
/***********************************************************************************************/
                echo json_encode(array("SUCCESS"=> $message));
                exit;
            }
        }
        $this->load->view('view_accounts_process_payment', $data);
    }
    
    
    /** code for customer transfer */
	
	function customer_transfer() {
	$accou_infor = $this->session->userdata('accountinfo');
	$account = $this->accounts_model->get_account_by_number($accou_infor['id']);
        $currency = $this->accounts_model->get_currency_by_id($account['currency_id']);
        $data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = 'Fund Transfer';
        $data['form'] = $this->form->build_form($this->accounts_form->get_customer_transfer_fields($currency['currency'], $account['number'], $currency['currency'], $accou_infor['id']), '');
        $this->load->view('view_user_transfer', $data);
        
    }

/*customer transfer save*/
    function customer_transfer_save() {
        $post_array = $this->input->post();
    $accou_infor = $this->session->userdata('accountinfo');
    $account = $this->accounts_model->get_account_by_number($accou_infor['id']);
        $currency = $this->accounts_model->get_currency_by_id($account['currency_id']);
        $data['form'] = $this->form->build_form($this->accounts_form->get_customer_transfer_fields($currency['currency'], $account['number'], $currency['currency'], $accou_infor['id']), $post_array);
        $data['page_title'] = 'Fund Transfer';
            if ($this->form_validation->run() == FALSE) {
                $data['validation_errors'] = validation_errors();
            } else {
        if(trim($post_array['fromaccountid']) != trim($post_array['toaccountid'])){
            $account_info = $this->session->userdata('accountinfo');
            $balance=$this->common->get_field_name('balance', 'accounts', array('id'=>$account_info['id'],'status'=>0,'type'=>0,'deleted'=>0)); ;
            $toid=$this->common->get_field_name('id', 'accounts', array('number'=>$post_array['toaccountid'],'status'=>0,'type'=>0,'deleted'=>0));   
             $reseller_id=$this->common->get_field_name('reseller_id', 'accounts', array('number'=>$post_array['toaccountid'],'status'=>0,'type'=>0,'deleted'=>0));   
            $post_array['credit'] = $this->common_model->add_calculate_currency($post_array['credit'], '', '', false, false);
        if ($post_array['toccountid'] == $account_info['number']){
         $this->session->set_flashdata('astpp_notification', 'You can not transfer fund in same account.');
              redirect(base_url() . 'accounts/customer_transfer/'); 
        }
        if ($reseller_id != $account_info['reseller_id']){
         $this->session->set_flashdata('astpp_notification', 'You can only transfer fund in same level account.');
              redirect(base_url() . 'accounts/customer_transfer/'); 
        }
        if ($post_array['toaccountid'] == ''){
              $this->session->set_flashdata('astpp_notification', 'Please enter To account number.');
              redirect(base_url() . 'accounts/customer_transfer/');
            }
            if (empty($post_array['credit'])){
              $this->session->set_flashdata('astpp_notification', 'Please enter a amount.');
              redirect(base_url() . 'accounts/customer_transfer/'); 
            }
            if ($post_array['credit'] > $balance){
              $this->session->set_flashdata('astpp_notification', 'You have insufficient balance.');
              redirect(base_url() . 'accounts/customer_transfer/'); 
            }
            if ($toid <= 0 || !isset($post_array['toaccountid'])){
              $this->session->set_flashdata('astpp_notification', 'Please enter valid account number.');
              redirect(base_url() . 'accounts/customer_transfer/');
            }
            if($post_array['credit'] < 0)
            {
               $this->session->set_flashdata('astpp_notification', 'Please enter amount greater then 0.');
               redirect(base_url() . 'accounts/customer_transfer/');   
            } 
    $patterns = $this->db_model->getSelect("value", 'system', array('name' => 'minimum_fund_transfer'));
        $minimum_fund = $patterns->result_array();
    if($minimum_fund[0]['value'] >= $post_array['credit']){
               $this->session->set_flashdata('astpp_notification', 'You need to enter minimum amount of fund transfer '.$minimum_fund[0]['value'].' .');
                redirect(base_url() . 'accounts/customer_transfer/');   
    } 
    if (!isset($toid) || !isset($post_array['toaccountid'])){
      $this->session->set_flashdata('astpp_notification', 'Please enter valid account number!');
      redirect(base_url() . 'accounts/customer_transfer/');
    }
    if($post_array['credit'] < 0 || $post_array['credit'] > $account_info['balance'] )
    {
       $this->session->set_flashdata('astpp_notification', 'Incefficent amount !');
       redirect(base_url() . 'accounts/customer_transfer/');    
    }
            $from['id']=$post_array['id'];
            $from['account_currency']=$post_array['account_currency'];
            $from['accountid']=$post_array['fromaccountid'];
            if($account['posttoexternal'] ==1){
              $from['credit']= abs($post_array['credit']);
            }else{
              $from['credit']= -1 * abs($post_array['credit']);
            }
            $from['posttoexternal']= $account['posttoexternal'];
            $from['payment_type']='0';
            $from['notes']=$post_array['notes'];
            $from['action']='save';

            $to['id']=$toid;
            $to['account_currency']=$post_array['account_currency'];
            $to['accountid']=$post_array['toaccountid'];
            $to['credit']= $post_array['credit'];
            $to['payment_type']='0';
            $to['notes']=$post_array['notes'];
            $to['action']='save';

            $response = $this->accounts_model->account_process_payment($from);
            
            if ($response){
              $toresponse=$this->accounts_model->account_process_payment($to);
              $this->session->set_flashdata('astpp_errormsg', 'Transfer success!');       
            }
            else{
              $this->session->set_flashdata('astpp_notification', 'Sorry We are not able to process this request.');
            }

        }else{
            $this->session->set_flashdata('astpp_notification', 'You can not transfer fund in same account.');
            redirect(base_url() . 'accounts/customer_transfer/');   
        }
        redirect(base_url() . 'accounts/customer_transfer/');
    }
        $this->load->view('view_user_transfer', $data);
    }
    /**
     * -------Here we write code for controller accounts functions add_callerid------
     * Add caller ids against account no
     * @account_number: Account No
     */
    function customer_add_callerid($id = "") {
        $data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = 'Caller ID';
        $account_num = $this->accounts_model->get_account_number($id);
        $result = $this->accounts_model->get_callerid($id);
        if ($result->num_rows() > 0) {
            foreach ($result->result_array() as $values) {
                $data['accountid'] = $values['accountid'];
                $data['callerid_name'] = $values['callerid_name'];
                $data['callerid_number'] = $values['callerid_number'];
                $data['status'] = $values['status'];
                $data['flag'] = '1';
            }
        } else {
            $data['accountid'] = $id;
            $data['callerid_name'] = '';
            $data['callerid_number'] = '';
            $data['status'] = '0';
            $data['flag'] = '0';
        }
        $data['accountid'] = $account_num['number'];
        $data['form'] = $this->form->build_form($this->accounts_form->get_customer_callerid_fields(), $data);
        $post_array = $this->input->post();
        
        if (!empty($post_array)) {
            if ($this->form_validation->run() == FALSE) {
                $data['validation_errors'] = validation_errors();
                echo $data['validation_errors'];
                exit;
            } else {
                if ($post_array['flag'] == '1') {
                    $this->accounts_model->edit_callerid($post_array);
                     echo json_encode(array("SUCCESS"=> "Account callerID updated successfully!"));
                      exit;
                } else {
                    $this->accounts_model->add_callerid($post_array);
                    echo json_encode(array("SUCCESS"=> "Account callerID added successfully!"));
                    exit;
                }
            }
        }

        $this->load->view('view_accounts_add_callerid', $data);
    }

    function reseller_add($type = "") {
        $data['username'] = $this->session->userdata('user_name');
        $data['flag'] = 'create';
        $data['page_title'] = 'Create Reseller';
        $data['form'] = $this->form->build_form($this->accounts_form->get_form_reseller_fields(), '');
	    $data['country_id']=Common_model::$global_config['system_config']['country'];
	//$data['currency_id']=Common_model::$global_config['system_config']['base_currency'];
	   $data['currency_id']=$this->common->get_field_name('id', 'currency', array('currency'=>Common_model::$global_config['system_config']['base_currency']));
	   $data['timezone_id']=Common_model::$global_config['system_config']['default_timezone'];
	if(!$data['timezone_id'])
        {
                $data['timezone_id']=1;
        }
        if( !$data['currency_id'] )
        {
                $data['currency_id']=1;
        }
        if(!$data['country_id'])
        {
                $data['country_id']=1;
        }

        $this->load->view('view_accounts_create', $data);
    }

    function reseller_edit($edit_id = '') {
        $data['page_title'] = 'Edit Reseller';
        $where = array('id' => $edit_id);
        $account = $this->db_model->getSelect("*", "accounts", $where);
         
        $data["account_data"] = $account->result_array();
        $data['chargelist'] = form_dropdown('applayable_charge', $this->Astpp_common->list_applyable_charges($edit_id), '');
        $data['invoice_date']=$data['account_data'][0]['invoice_day'];
        $this->load->module('charges/charges');
        $data['charges_grid_field'] = $this->charges->charges_form->build_charges_list_for_customer($edit_id, "reseller");

        $data["sipiax_grid_field"] = json_decode($this->accounts_form->build_sipiax_list_for_customer());

        $this->load->module('did/did');
        $data['did_grid_fields'] = $this->did->did_form->build_did_list_for_reseller($edit_id, "reseller");
//         $data['didlist'] = form_dropdown('free_did_list', $this->db_model->build_dropdown("id,number", "dids", "accountid", "0"), '');

	/****************************FOR DID list dropdown**************/
        $acc_data = $this->session->userdata("accountinfo");
//  	$dids = $this->db_model->getSelect("id,number", "dids",array('accountid'=>0));
//  	$dids_array=$dids->result_array();
//  	$reseller_did = $this->db_model->getSelect("note", "reseller_pricing",array("reseller_id"=>1));
//  	$reseller_did_value = $reseller_did->result_array();
//  	
 	$resellerdids=array();
 	$reseller_did=array();
 	$drp_list=array();
//  	foreach($reseller_did_value  as $reseller_did_value_for)
//  	{
// 	  $resellerdids[]=$reseller_did_value_for['note'];
//  	}
//  	foreach($dids_array  as $key => $did_value)
//  	{
// 	    if(in_array($did_value['number'],$resellerdids))
//  	    {
// 	      unset($dids_array[$key]);
//  	    }
//  	}
 	if($this->session->userdata['userlevel_logintype'] == '-1')
 	{
// 	    $reseller_did = $this->db_model->getSelect("group_concat(concat('''',note,'''')) as note", "reseller_pricing",'');
// //  	echo "<pre>";print_r($reseller_did->result_array());
// 	      $reseller_did = $reseller_did->result_array();
// 	      if($reseller_did['0']['note'] != '' ){
// // 		$where_str = "number NOT IN (".$reseller_did['0']['note'].")";
// 		$where_str = "number NOT IN (SELECT note FROM (`reseller_pricing`))";
// 		$this->db->where($where_str);
// 	      }
 	
	    $where = array('dids.accountid'=>'0','dids.parent_id'=>'0');
// 	    $jionCondition = 'dids.number != reseller_pricing.note';
	    $reseller_did = $this->db_model->getSelect( '*' ,'dids',$where);
// 	   $reseller_did=$this->db_model->getJionQuery("dids",'dids.*',$where , "reseller_pricing", $jionCondition, 'INNER','500', '0',"reseller_pricing.id", "ASC",  '','');
	    $dids_array = $reseller_did->result_array();
	    foreach ($dids_array as $drp_value) {
	      $drp_list[$drp_value['id']] = $drp_value['number'];
	    }
 	}else{
	  
// 	  $where = array('reseller_pricing.reseller_id' => $acc_data['id'],'dids.accountid'=>'0');
// 	  $jionCondition = 'dids.number = reseller_pricing.note';
	  //	    $reseller_did = $this->db_model->getSelect("group_concat(concat('''',note,'''')) as note", "reseller_pricing",array("reseller_id <> " =>$this->session->userdata['accountinfo']['id']));
	 
// 	 $did_count=$this->db->query("select count(reseller_pricing.note) as count,note from reseller_pricing group by note");
// 	 echo "<pre>";
	 
// 	 $didcount_ar=$did_count->result_array();
	 
// 	 print_r($did_count->result_array());
// 	 $select="reseller_pricing.setup,reseller_pricing.cost,reseller_pricing.connectcost,dids.inc,reseller_pricing.includedseconds,reseller_pricing.monthlycost,dids.number,dids.id,dids.accountid,dids.extensions,dids.status,dids.provider_id,dids.allocation_bill_status,reseller_pricing.disconnectionfee,dids.dial_as,dids.call_type,dids.country_id";
// 	  $reseller_did=$this->db_model->getJionQuery("dids", $select,$where , "reseller_pricing", $jionCondition, 'inner','500', '0',"reseller_pricing.id", "ASC",  '','');

// 	  echo $this->db->last_query();
// 	$dids_array = $reseller_did->result_array();  
// 	print_r($dids_array);
// 	exit;
// 	  foreach ($dids_array as $drp_value) {
// 	      foreach ($didcount_ar as $ddicount_value) {
// 		  if($ddicount_value['count'] == 1)
// 		  {
// 			$drp_list1[] = $ddicount_value['note'];
// 		  }
// 		}
// 	    }
// 	    print_r($drp_list1);
	    
	    $where = array('dids.accountid'=>'0','dids.parent_id'=>$acc_data['id']);
	    $reseller_did = $this->db_model->getSelect( '*' ,'dids',$where);
	    $dids_array = $reseller_did->result_array();
	    foreach ($dids_array as $drp_value) {
	      
		  $drp_list[$drp_value['id']] = $drp_value['number'];
	      
	    }
	}



 	
//         echo "<pre>";print_r($drp_list);
        $data['didlist'] = form_dropdown_all('free_did_list', $drp_list, '');
        /****************************FOR DID list dropdown**************/
        
        $this->load->module('invoices/invoices');
        $data['invoice_grid_fields'] = $this->invoices->invoices_form->build_invoices_list_for_admin();

        $this->load->module('reports/reports');
        $data['cdrs_grid_fields'] = $this->reports->reports_form->build_report_list_for_user();

        foreach ($account->result_array() as $key => $value) {
            $edit_data = $value;
        }
	$taxes_data  = $this->db_model->getSelect("group_concat(taxes_id) as taxes_id","taxes_to_accounts", array("accountid" => $edit_id));
            if(isset($taxes_data) && $taxes_data->num_rows() > 0)
            {
		 $taxes_data=$taxes_data->result_array();
		 $edit_data["tax_id"] =  explode(",",$taxes_data[0]['taxes_id']);
            }
         
        $data['form'] = $this->form->build_form($this->accounts_form->get_form_reseller_fields($edit_id), $edit_data);
        $this->load->view('view_reseller_details', $data);
    }

    function reseller_save() {
        $add_array = $this->input->post();
        $data['form'] = $this->form->build_form($this->accounts_form->get_form_reseller_fields($add_array['id']), $add_array);
        if ($add_array['id'] != '') {
            $data['page_title'] = 'Edit Reseller';
            if ($this->form_validation->run() == FALSE) {
                $data['validation_errors'] = validation_errors();
            } else {
		  $add_array['credit_limit'] = $this->common_model->add_calculate_currency($add_array['credit_limit'], '', '', false, false);
		  $query = $this->accounts_model->remove_all_account_tax($add_array['id']);
		  if(isset($add_array['tax_id']))
		  {
		      foreach($add_array['tax_id'] as $key=>$val)
		      {
			    $data1 = array(
				'accountid' => $add_array['id'],
				'taxes_id' => $val,
			    );
			    $this->accounts_model->add_account_tax($data1);
		      }
		      unset($add_array['tax_id']);
		  }
		  $this->accounts_model->edit_account($add_array, $add_array['id']);
		  $this->session->set_flashdata('astpp_errormsg', 'Reseller updated successfully!');
		  
		  redirect(base_url().'accounts/reseller_list/');
		  exit;
            }
	    $data["account_data"]["0"] = $add_array;
	    $edit_id = $add_array["id"];
	    $data['chargelist'] = form_dropdown('applayable_charge', $this->Astpp_common->list_applyable_charges($edit_id), '');

	    $this->load->module('charges/charges');
	    $data['charges_grid_field'] = $this->charges->charges_form->build_charges_list_for_customer($edit_id, "reseller");

	    $data["sipiax_grid_field"] = json_decode($this->accounts_form->build_sipiax_list_for_customer());

	    $this->load->module('did/did');
	    $data['did_grid_fields'] = $this->did->did_form->build_did_list_for_customer($edit_id, "reseller");
	    $data['didlist'] = form_dropdown('free_did_list', $this->db_model->build_dropdown("id,number", "dids", "accountid", "0"), '');


	    $this->load->module('invoices/invoices');
	    $data['invoice_grid_fields'] = $this->invoices->invoices_form->build_invoices_list_for_admin();

	    $this->load->module('reports/reports');
	    $data['cdrs_grid_fields'] = $this->reports->reports_form->build_report_list_for_user();

            $this->load->view('view_reseller_details', $data);
        } else {
        
            $data['page_title'] = 'Create Reseller';
            if ($this->form_validation->run() == FALSE) {
                $data['validation_errors'] = validation_errors();
            } else {          
		  $add_array['credit_limit'] = $this->common_model->add_calculate_currency($add_array['credit_limit'], '', '', false, false);
/*		  $add_array['account_by_reseller'] = 'reseller account';
		  $this->accounts_model->add_account($add_array);*/
		  $last_id=$this->accounts_model->add_account($add_array);
		  
		  if(isset($add_array['tax_id']))
		  {
		      foreach($add_array['tax_id'] as $key=>$val)
		      {
			    $data1 = array(
				'accountid' => $last_id,
				'taxes_id' => $val,
			    );
			    $this->accounts_model->add_account_tax($data1);    
		      }
		      unset($add_array['tax_id']);
		  }

		  $this->session->set_flashdata('astpp_errormsg', 'Reseller added successfully!');
		  redirect(base_url() . 'accounts/reseller_list/');
		  exit;
            }
	    $this->load->view('view_accounts_create', $data);
        }
    }
    
     function customer_generate_password(){
        echo $this->common->generate_password();
    }
    function customer_generate_number($digit){
        echo $this->common->find_uniq_rendno($digit, 'number', 'accounts');
    }

    function provider_add() {
    
           $this->customer_add(3);
//         $data['username'] = $this->session->userdata('user_name');
//         $data['flag'] = 'create';
//         $data['page_title'] = 'Create Provider';
//         $data['form'] = $this->form->build_form($this->accounts_form->get_form_provider_fields(), '');// 
//         $this->load->view('view_accounts_create', $data);
    }

    function provider_edit($edit_id = '') {
    
        $this->customer_edit($edit_id);
//         $data['page_title'] = 'Edit Provider';
//         $where = array('id' => $edit_id);
//         $account = $this->db_model->getSelect("*", "accounts", $where);
//         $data['account_data'] = $account->result_array();
// 
//         $data["ipmap_grid_field"] = $this->accounts_form->build_ip_list_for_customer($edit_id, "provider");
// 
//         $this->load->module('invoices/invoices');
//         $data['invoice_grid_fields'] = $this->invoices->invoices_form->build_invoices_list_for_admin();
// 
//         $this->load->module('reports/reports');
//         $data['cdrs_grid_fields'] = $this->reports->reports_form->build_report_list_for_user();
// 
//         foreach ($account->result_array() as $key => $value) {
//             $edit_data = $value;
//         }
//         $taxes_data  = $this->db_model->getSelect("group_concat(taxes_id) as taxes_id","taxes_to_accounts", array("accountid" => $edit_id));
//         if(isset($taxes_data) && $taxes_data->num_rows() > 0)
//             {
// 		 $taxes_data=$taxes_data->result_array();
// 		 $edit_data["tax_id"] =  explode(",",$taxes_data[0]['taxes_id']);
//             }  
//         $data['form'] = $this->form->build_form($this->accounts_form->get_form_provider_fields(), $edit_data);
//         $this->load->view('view_provider_details', $data);
    }

    function provider_save() {
         $add_array = $this->input->post();
	 $this->customer_save($add_array);
//         $data['form'] = $this->form->build_form($this->accounts_form->get_form_provider_fields(), $add_array);
//         if ($add_array['id'] != '') {
//             $data['page_title'] = 'Edit Provider';
//             if ($this->form_validation->run() == FALSE) {
//                 $data['validation_errors'] = validation_errors();
//             } else {
//                 $check_authentication = $this->validate_customer_data($add_array);
//                 if ($check_authentication == 1) {                
// 		  $add_array['credit_limit'] = $this->common_model->add_calculate_currency($add_array['credit_limit'], '', '', false, false);
// 		  $query = $this->accounts_model->remove_all_account_tax($add_array['id']);
// 		  if(isset($add_array['tax_id']))
// 		  {
// 		      foreach($add_array['tax_id'] as $key=>$val)
// 		      {
// 			    $data1 = array(
// 				'accountid' => $add_array['id'],
// 				'taxes_id' => $val,
// 			    );
// 			    $this->accounts_model->add_account_tax($data1);
// 		      }
// 		      unset($add_array['tax_id']);
// 		  }
// 		  $this->accounts_model->edit_account($add_array, $add_array['id']);
// 		  $this->session->set_flashdata('astpp_notification', 'Account Edit Completed!');
// 		  redirect(base_url() . 'accounts/customer_list/');
// 		  exit;
//                 }else {
//                     $data['validation_errors'] = $check_authentication;
//                 }
//             }
// 	    $data['account_data']["0"] = $add_array;
// 	    $edit_id = $add_array["id"];
// 	    $data["ipmap_grid_field"] = $this->accounts_form->build_ip_list_for_customer($edit_id, "provider");
// 
// 	    $this->load->module('invoices/invoices');
// 	    $data['invoice_grid_fields'] = $this->invoices->invoices_form->build_invoices_list_for_admin();
// 
// 	    $this->load->module('reports/reports');
// 	    $data['cdrs_grid_fields'] = $this->reports->reports_form->build_report_list_for_user();
// 
//             $this->load->view('view_provider_details', $data);
//         } else {
//             $data['page_title'] = 'Create Provider';
//             if ($this->form_validation->run() == FALSE) {
//                 $data['validation_errors'] = validation_errors();
//             } else {
//                 $check_authentication = $this->validate_customer_data($add_array);
//                 if ($check_authentication == 1) {                
// 		  $add_array['credit_limit'] = $this->common_model->add_calculate_currency($add_array['credit_limit'], '', '', false, false);
// 		  $last_id=$this->accounts_model->add_account($add_array);
//                   if(isset($add_array['tax_id']))
// 		  {
// 		      foreach($add_array['tax_id'] as $key=>$val)
// 		      {
// 			    $data1 = array(
// 				'accountid' => $last_id,
// 				'taxes_id' => $val,
// 			    );
// 			    $this->accounts_model->add_account_tax($data1);    
// 		      }
// 		      
// 		  }
// 		  $this->session->set_flashdata('astpp_notification', 'Account Setup Completed!');
// 		  redirect(base_url() . 'accounts/customer_list/');
// 		  exit;
//                 }else {
// 		    $data['validation_errors'] = json_encode(array("0"=>$check_authentication));
// //                     $data['validation_errors'] = $check_authentication;
//                 }
//             }
// 	    $this->load->view('view_accounts_create', $data);
//         }
    }

       function admin_add($type = 2) {
	$entity_type =strtolower($this->common->get_entity_type('','',$type));
	$entitytype = str_replace(' ', '', $entity_type);
        $data['username'] = $this->session->userdata('user_name');
        $data['flag'] = 'create';
        $data['page_title'] = 'Create '.$entity_type;
        $data['form'] = $this->form->build_form($this->accounts_form->get_form_admin_fields($entitytype), '');
	$data['country_id']=Common_model::$global_config['system_config']['country'];
	//$data['currency_id']=Common_model::$global_config['system_config']['base_currency'];
	$data['currency_id']=$this->common->get_field_name('id', 'currency', array('currency'=>Common_model::$global_config['system_config']['base_currency']));
	$data['timezone_id']=Common_model::$global_config['system_config']['default_timezone'];
        if(!$data['timezone_id'])
        {
                $data['timezone_id']=1;
        }
        if( !$data['currency_id'] )
        {
                $data['currency_id']=1;
        }
        if(!$data['country_id'])
        {
                $data['country_id']=1;
        }
        $data['entity_name']=$entity_type;
        $this->load->view('view_accounts_create', $data);
    }

 function admin_edit($edit_id = '') {
        $accountinfo=$this->session->userdata('accountinfo');
        $where = array('id' => $edit_id);
        $account = $this->db_model->getSelect("*", "accounts", $where);
        foreach ($account->result_array() as $key => $value) {
            $edit_data = $value;
        }
        $taxes_data  = $this->db_model->getSelect("group_concat(taxes_id) as taxes_id","taxes_to_accounts", array("accountid" => $edit_id));
        if(isset($taxes_data) && $taxes_data->num_rows() > 0)
        {
		 $taxes_data=$taxes_data->result_array();
		 $edit_data["tax_id"] =  explode(",",$taxes_data[0]['taxes_id']);
        }
        $type=$accountinfo['type']== -1 ? 2 : $accountinfo['type'];
        $entity_type =strtolower($this->common->get_entity_type('','',$type));
	$entitytype = str_replace(' ', '', $entity_type);
        $data['form'] = $this->form->build_form($this->accounts_form->get_form_admin_fields($entitytype,$edit_id), $edit_data);
        $data['page_title'] = 'Edit '.$entity_type;
        $this->load->view('view_admin_details', $data);
    }

     function admin_save($add_array=false) {
        $add_array = $this->input->post();
        $accountinfo=$this->session->userdata('accountinfo');
        $type=$accountinfo['type']== -1 ? 2 : $add_array['type'];
        $entity_type =strtolower($this->common->get_entity_type('','',$type));
	$entitytype = str_replace(' ', '', $entity_type);
        $data['username'] = $this->session->userdata('user_name');
        $data['flag'] = 'create';
        $data['page_title'] = 'Create '.$entity_type;
        $data['form'] = $this->form->build_form($this->accounts_form->get_form_admin_fields($entitytype,$add_array['id']), $add_array);
        if ($add_array['id'] != '') {
            $data['page_title'] = 'Edit '.$entity_type;
            if ($this->form_validation->run() == FALSE) {
                $data['validation_errors'] = validation_errors();
            } else {             
		  $add_array['credit_limit'] = $this->common_model->add_calculate_currency($add_array['credit_limit'], '', '', false, false);
		  $query = $this->accounts_model->remove_all_account_tax($add_array['id']);
		  if(isset($add_array['tax_id']))
		  {
		      foreach($add_array['tax_id'] as $key=>$val)
		      {
			    $data1 = array(
				'accountid' => $add_array['id'],
				'taxes_id' => $val,
			    );
			    $this->accounts_model->add_account_tax($data1);
		      }
		      unset($add_array['tax_id']);
		  }
		  $this->accounts_model->edit_account($add_array, $add_array['id']);
		  $accountinfo=$this->session->userdata('accountinfo');
		  if($add_array['id']==$accountinfo['id'] ){
		    $result=$this->db->get_where('accounts',array('id'=>$add_array['id']));
		    $result=$result->result_array();
		   $this->session->set_userdata('accountinfo',$result[0]);
		  }
		  $this->session->set_flashdata('astpp_errormsg',ucfirst($entity_type). ' updated successfully!');

		  redirect(base_url() . 'accounts/admin_list/');
		  exit;
            }
            $this->load->view('view_admin_details', $data);
        } else {
            $data['page_title'] = 'Create '.$entity_type;
            if ($this->form_validation->run() == FALSE) {
                $data['validation_errors'] = validation_errors();
            } else {
                $last_id=$this->accounts_model->add_account($add_array);
                if(isset($add_array['tax_id']))
		  {
		      foreach($add_array['tax_id'] as $key=>$val)
		      {
			    $data1 = array(
				'accountid' => $last_id,
				'taxes_id' => $val,
			    );
			    $this->accounts_model->add_account_tax($data1);    
		      }
		      unset($add_array['tax_id']);
		  }
		  $add_array['credit_limit'] = $this->common_model->add_calculate_currency($add_array['credit_limit'], '', '', false, false);
		  
		  $this->session->set_flashdata('astpp_errormsg', ucfirst($entity_type).' added successfully!');
		  redirect(base_url() . 'accounts/admin_list/');
		  exit;
            }$this->load->view('view_accounts_create', $data);
        }
    }

    function subadmin_add($type = "") {
        $this->admin_add(4);
    }

    function subadmin_edit($edit_id = '') {
         $this->admin_edit($edit_id);
//         $data['page_title'] = 'Edit Subadmin';
//         $where = array('id' => $edit_id);
//         $account = $this->db_model->getSelect("*", "accounts", $where);
//         foreach ($account->result_array() as $key => $value) {
//             $edit_data = $value;
//         }
//         $taxes_data  = $this->db_model->getSelect("group_concat(taxes_id) as taxes_id","taxes_to_accounts", array("accountid" => $edit_id));
//         if(isset($taxes_data) && $taxes_data->num_rows() > 0)
//         {
// 		 $taxes_data=$taxes_data->result_array();
// 		 $edit_data["tax_id"] =  explode(",",$taxes_data[0]['taxes_id']);
//         }
//         $data['form'] = $this->form->build_form($this->accounts_form->get_form_subadmin_fields(), $edit_data);
//         $this->load->view('view_subadmin_details', $data);
    }

    function subadmin_save() {
	    $add_array = $this->input->post();
	    $this->admin_save($add_array);
//         $add_array = $this->input->post();
//         $data['form'] = $this->form->build_form($this->accounts_form->get_form_subadmin_fields(), $add_array);
//         if ($add_array['id'] != '') {
//             $data['page_title'] = 'Edit Subadmin';
//             if ($this->form_validation->run() == FALSE) {
//                 $data['validation_errors'] = validation_errors();
//             } else {
//                 $check_authentication = $this->validate_customer_data($add_array);
//                 if ($check_authentication == 1) {                
// 		  $add_array['credit_limit'] = $this->common_model->add_calculate_currency($add_array['credit_limit'], '', '', false, false);
// 		  $query = $this->accounts_model->remove_all_account_tax($add_array['id']);
// 		  if(isset($add_array['tax_id']))
// 		  {
// 		      foreach($add_array['tax_id'] as $key=>$val)
// 		      {
// 			    $data1 = array(
// 				'accountid' => $add_array['id'],
// 				'taxes_id' => $val,
// 			    );
// 			    $this->accounts_model->add_account_tax($data1);
// 		      }
// 		      unset($add_array['tax_id']);
// 		  }
// 		  $this->accounts_model->edit_account($add_array, $add_array['id']);
// 		  $this->session->set_flashdata('astpp_errormsg', 'Account Edit Completed!');
// 		  redirect(base_url() . 'accounts/admin_list/');
// 		  exit;
//                 }else {
//                     $data['validation_errors'] = $check_authentication;
//                 }
//             }
//             $this->load->view('view_subadmin_details', $data);
//         } else {
//             $data['page_title'] = 'Create Subadmin';
//             if ($this->form_validation->run() == FALSE) {
//                 $data['validation_errors'] = validation_errors();
//             } else {
//                 $check_authentication = $this->validate_customer_data($add_array);
//                 if ($check_authentication == 1) {                
// //		  $add_array['credit_limit'] = $this->common_model->add_calculate_currency($add_array['credit_limit'], '', '', false, false);
//                     $last_id=$this->accounts_model->add_account($add_array);
// 		  if(isset($add_array['tax_id']))
// 		  {
// 		      foreach($add_array['tax_id'] as $key=>$val)
// 		      {
// 			    $data1 = array(
// 				'accountid' => $last_id,
// 				'taxes_id' => $val,
// 			    );
// 			    $this->accounts_model->add_account_tax($data1);    
// 		      }
// 		      unset($add_array['tax_id']);
// 		  }
// 		  
// 		  $this->session->set_flashdata('astpp_errormsg', 'Account Setup Completed!');
// 		  redirect(base_url() . 'accounts/admin_list/');
// 		  exit;
//                 }else {
// 		    $data['validation_errors'] = json_encode(array("0"=>$check_authentication));
// //                     $data['validation_errors'] = $check_authentication;
//                 }
//             }
// 	    $this->load->view('view_accounts_create', $data);
//         }
    }

    /**
     * -------Here we write code for controller accounts functions account_detail------
     * Account detail info through account number with checking account no exit or not.
     */
//     function account_detail($accountid) { //build_account_info
//         $data['app_name'] = 'ASTPP - Open Source Billing Solution | Accounts | Create';
//         $data['username'] = $this->session->userdata('user_name');
//         $data['page_title'] = 'Account Details';
//         $where = array('accountid' => urldecode($accountid));
//         $account = $this->db_model->getSelect("*", "accounts", $where);
//         $data["account_data"] = $account[0];
//         $data['sweeplist'] = $this->common_model->get_sweep_list();
//         $data['currency_list'] = $this->common_model->get_currency_list();
//         $data['config'] = $this->common_model->get_system_config();
//         $data['country_list'] = $this->common_model->get_country_list();
//         $Timezone = $this->db_model->getSelect("id,gmtzone", "timezone", "");
//         $Timezone_list = array();
//         foreach ($Timezone as $timezone_value) {
//             $Timezone_list[$timezone_value->id] = $timezone_value->gmtzone;
//         }
//         $data["Timezone_list"] = $Timezone_list;
//         $pricelist = $this->db_model->getSelect("name", "pricelists", "");
//         $pricelist_list = array();
//         foreach ($pricelist as $pricelist_value) {
//             $pricelist_list[$pricelist_value->name] = $pricelist_value->name;
//         }
// 
//         $data["Price_list"] = $pricelist_list;
//         $data["language_list"] = Common_model::$global_config['language_list'];
// 
//         /* Charges data fetch display in drop down list */
//         $data['chargelist'] = $this->Astpp_common->list_applyable_charges();
// 
//         /* Charges Grid field array declaired here */
//         $data['charges_grid_fields'] = array("0" => array("0" => "Description", "1" => "400"),
//             "1" => array("0" => "Charges", "1" => "100"),
//             "2" => array("0" => "Cycle", "1" => "100")
//         );
// 
//         $this->load->view('view_accounts_details', $data);
//     }

    function chargelist_json($accountid) {
        $json_data = array();
        $sweeplist = $this->common_model->get_sweep_list();

        $select = "charges.description,charges.charge,charges.sweep";
        $table = "charges";
        $jionTable = array('charge_to_account', 'accounts');
        $jionCondition = array('charges.id = charge_to_account.charge_id', 'accounts.number = charge_to_account.cardnum');
        $type = array('left', 'inner');
        $where = array('accounts.accountid' => $accountid);
        $order_type = 'charges.id';
        $order_by = "ASC";

        $account_charge_count = $this->db_model->getCountWithJion($table, $select, $where, $jionTable, $jionCondition, $type);

        $count_all = $account_charge_count;
        $config['total_rows'] = $count_all;
        $config['per_page'] = $_GET['rp'];

        $page_no = $_GET['page'];
        $json_data['page'] = $page_no;

        $json_data['total'] = $config['total_rows'];
        $perpage = $config['per_page'];
        $start = ($page_no - 1) * $perpage;
        if ($start < 0)
            $start = 0;

        $account_charge_list = $this->db_model->getAllJionQuery($table, $select, $where, $jionTable, $jionCondition, $type, $perpage, $start, $order_by, $order_type, "");
        if ($account_charge_list->num_rows > 0) {
            foreach ($account_charge_list->result() as $key => $charges_value) {
                $json_data['rows'][] = array('cell' => array(
                        $charges_value->description,
                        $charges_value->charge,
                        $sweeplist[$charges_value->sweep]
                        ));
            }
        }
        echo json_encode($json_data);
    }

    function admin_list() {

        $data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = 'Admins';
	$data['search_flag'] = true;
        $data['cur_menu_no'] = 1;
        $this->session->set_userdata('advance_search', 0);
        $data['grid_fields'] = $this->accounts_form->build_account_list_for_admin();
        $data["grid_buttons"] = $this->accounts_form->build_grid_buttons_admin();

        $data['form_search'] = $this->form->build_serach_form($this->accounts_form->get_admin_search_form());
        $this->load->view('view_accounts_list', $data);
    }

    /**
     * -------Here we write code for controller accounts functions account_list------
     * Listing of Accounts table data through php function json_encode
     */
    function admin_list_json() {
        $json_data = array();
        
        if ($this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 5) {
            $account_data = $this->session->userdata("accountinfo");
            $reseller_id=$accountdata['id'];
        } else {
             $reseller_id=0;
        }
        $count_all = $this->accounts_model->get_admin_Account_list(false,'','',$reseller_id);
        $paging_data = $this->form->load_grid_config($count_all, $_GET['rp'], $_GET['page']);
        $json_data = $paging_data["json_paging"];

        $query = $this->accounts_model->get_admin_Account_list(true, $paging_data["paging"]["start"], $paging_data["paging"]["page_no"],$reseller_id);
        $grid_fields = json_decode($this->accounts_form->build_account_list_for_admin());
        $json_data['rows'] = $this->form->build_grid($query, $grid_fields);

        echo json_encode($json_data);
    }

    function subadmin_list() {
        $data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = 'Sub-Admins';
        $data['search_flag'] = true;
        $this->session->set_userdata('advance_search', 0);
        $data['grid_fields'] = $this->accounts_form->build_account_list_for_subadmin();
        $data["grid_buttons"] = $this->accounts_form->build_grid_buttons_subadmin();
        $data['form_search'] = $this->form->build_serach_form($this->accounts_form->get_subadmin_search_form());
        $this->load->view('view_accounts_list', $data);
    }

    /**
     * -------Here we write code for controller accounts functions account_list------
     * Listing of Accounts table data through php function json_encode
     */
    function subadmin_list_json() {
        $json_data = array();
        $count_all = $this->accounts_model->get_subadmin_Account_list(false);
        $paging_data = $this->form->load_grid_config($count_all, $_GET['rp'], $_GET['page']);
        $json_data = $paging_data["json_paging"];

        $query = $this->accounts_model->get_subadmin_Account_list(true, $paging_data["paging"]["start"], $paging_data["paging"]["page_no"]);
        $grid_fields = json_decode($this->accounts_form->build_account_list_for_subadmin());
        $json_data['rows'] = $this->form->build_grid($query, $grid_fields);

        echo json_encode($json_data);
    }

    function subadmin_list_search() {
        $ajax_search = $this->input->post('ajax_search', 0);
        if ($this->input->post('advance_search', TRUE) == 1) {
            $this->session->set_userdata('advance_search', $this->input->post('advance_search'));
            $action = $this->input->post();
            unset($action['action']);
            unset($action['advance_search']);
            $this->session->set_userdata('subadmin_list_search', $action);
        }
        if (@$ajax_search != 1) {
            redirect(base_url() . 'accounts/admin_list/');
        }
    }

    function subadmin_list_clearsearchfilter() {
        $this->session->set_userdata('advance_search', 0);
        $this->session->set_userdata('subadmin_list_search', "");
    }

    function customer_list() {
        $data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = 'Customers';
	$data['search_flag'] = true;
        $this->session->set_userdata('advance_search', 0);
        $data['grid_fields'] = $this->accounts_form->build_account_list_for_customer();
        $data["grid_buttons"] = $this->accounts_form->build_grid_buttons_customer();
        $data['form_search'] = $this->form->build_serach_form($this->accounts_form->get_search_customer_form());

        $this->load->view('view_accounts_list', $data);
    }

    /**
     * -------Here we write code for controller accounts functions account_list------
     * Listing of Accounts table data through php function json_encode
     */
    function customer_list_json() {
        $json_data = array();
        $count_all = $this->accounts_model->get_customer_Account_list(false);
        $paging_data = $this->form->load_grid_config($count_all, $_GET['rp'],$_GET['page']);
        $json_data = $paging_data["json_paging"];

        $query = $this->accounts_model->get_customer_Account_list(true, $paging_data["paging"]["start"], $paging_data["paging"]["page_no"]);
        $grid_fields = json_decode($this->accounts_form->build_account_list_for_customer());
        $json_data['rows'] = $this->form->build_grid($query, $grid_fields);
        echo json_encode($json_data);
    }

    function provider_list() {
        $data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = 'Providers';
	$data['search_flag'] = true;
        $this->session->set_userdata('advance_search', 0);
        $data['grid_fields'] = $this->accounts_form->build_account_list_for_provider();
        $data["grid_buttons"] = $this->accounts_form->build_grid_buttons_provider();

        $data['form_search'] = $this->form->build_serach_form($this->accounts_form->get_provider_search_form());
        $this->load->view('view_accounts_list', $data);
    }

    /**
     * -------Here we write code for controller accounts functions account_list------
     * Listing of Accounts table data through php function json_encode
     */
    function provider_list_json() {
        $json_data = array();
        $count_all = $this->accounts_model->get_provider_Account_list(false);
        $paging_data = $this->form->load_grid_config($count_all, $_GET['rp'], $_GET['page']);
        $json_data = $paging_data["json_paging"];

        $query = $this->accounts_model->get_provider_Account_list(true, $paging_data["paging"]["start"], $paging_data["paging"]["page_no"]);
        $grid_fields = json_decode($this->accounts_form->build_account_list_for_provider());
        $json_data['rows'] = $this->form->build_grid($query, $grid_fields);

        echo json_encode($json_data);
    }

    function provider_list_search() {
        $ajax_search = $this->input->post('ajax_search', 0);

        if ($this->input->post('advance_search', TRUE) == 1) {
            $this->session->set_userdata('advance_search', $this->input->post('advance_search'));
            $action = $this->input->post();
            unset($action['action']);
            unset($action['advance_search']);
        if(isset($action['balance']['balance']) && $action['balance']['balance']!=''){
		  $action['balance']['balance']=$this->common_model->add_calculate_currency($action['balance']['balance'], "", '', false, false);
	    }
	    if(isset($action['credit_limit']['credit_limit']) && $action['credit_limit']['credit_limit']!=''){
		  $action['credit_limit']['credit_limit']=$this->common_model->add_calculate_currency($action['credit_limit']['credit_limit'], "", '', false, false);
	    }
            $this->session->set_userdata('provider_list_search', $action);
        }
        if (@$ajax_search != 1) {
            redirect(base_url() . 'accounts/customer_list/');
        }
    }

    function provider_list_clearsearchfilter() {
        $this->session->set_userdata('advance_search', 0);
        $this->session->set_userdata('provider_list_search', "");
//        redirect(base_url() . 'accounts/customer_account_list/');
    }

    function reseller_list() {
        $data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = 'Resellers';
	$data['search_flag'] = true;
        $this->session->set_userdata('advance_search', 0);
        $data['grid_fields'] = $this->accounts_form->build_account_list_for_reseller();
        $data["grid_buttons"] = $this->accounts_form->build_grid_buttons_reseller();
        $data['form_search'] = $this->form->build_serach_form($this->accounts_form->get_reseller_search_form());

        $this->load->view('view_accounts_list', $data);
    }

    /**
     * -------Here we write code for controller accounts functions account_list------
     * Listing of Accounts table data through php function json_encode
     */
    function reseller_list_json() {
        $json_data = array();
        $count_all = $this->accounts_model->get_reseller_Account_list(false);
        $paging_data = $this->form->load_grid_config($count_all, $_GET['rp'], $_GET['page']);
        $json_data = $paging_data["json_paging"];

        $query = $this->accounts_model->get_reseller_Account_list(true, $paging_data["paging"]["start"], $paging_data["paging"]["page_no"]);
        $grid_fields = json_decode($this->accounts_form->build_account_list_for_reseller());
        $json_data['rows'] = $this->form->build_grid($query, $grid_fields);

        echo json_encode($json_data);
    }

    function reseller_list_search() {
        $ajax_search = $this->input->post('ajax_search', 0);
        if ($this->input->post('advance_search', TRUE) == 1) {
            $this->session->set_userdata('advance_search', $this->input->post('advance_search'));
            $action = $this->input->post();
            unset($action['action']);
            unset($action['advance_search']);
            if(isset($action['balance']['balance']) && $action['balance']['balance']!=''){
		$action['balance']['balance']=$this->common_model->add_calculate_currency($action['balance']['balance'], "", '', false, false);
	    }
	    if(isset($action['credit_limit']['credit_limit']) && $action['credit_limit']['credit_limit']!=''){
		$action['credit_limit']['credit_limit']=$this->common_model->add_calculate_currency($action['credit_limit']['credit_limit'], "", '', false, false);
	    }
            $this->session->set_userdata('reseller_list_search', $action);
        }
        if (@$ajax_search != 1) {
            redirect(base_url() . 'accounts/reseller_list/');
        }
    }

    function admin_list_clearsearchfilter() {
        $this->session->set_userdata('advance_search', 0);
        $this->session->set_userdata('admin_list_search', "");
    }

    function admin_list_search() {
        $ajax_search = $this->input->post('ajax_search', 0);
        if ($this->input->post('advance_search', TRUE) == 1) {
            $this->session->set_userdata('advance_search', $this->input->post('advance_search'));
            $action = $this->input->post();
            unset($action['action']);
            unset($action['advance_search']);
            $this->session->set_userdata('admin_list_search', $action);
        }
        if (@$ajax_search != 1) {
            redirect(base_url() . 'accounts/admin_list/');
        }
    }

    function reseller_list_clearsearchfilter() {
        $this->session->set_userdata('advance_search', 0);
        $this->session->set_userdata('reseller_list_search', "");
    }

    function callshop_list() {
        $data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = 'Call Shops';
	$data['search_flag'] = true;
        $this->session->set_userdata('advance_search', 0);
        $data['grid_fields'] = $this->accounts_form->build_account_list_for_callshop();
        $data["grid_buttons"] = $this->accounts_form->build_grid_buttons_callshop();
        $data['form_search'] = $this->form->build_serach_form($this->accounts_form->get_callshop_search_form());
        $this->load->view('view_accounts_list', $data);
    }

    function callshop_list_json() {
        $json_data = array();
        $count_all = $this->accounts_model->get_callshop_Account_list(false);
        $paging_data = $this->form->load_grid_config($count_all, $_GET['rp'], $_GET['page']);
        $json_data = $paging_data["json_paging"];

        $query = $this->accounts_model->get_callshop_Account_list(true, $paging_data["paging"]["start"], $paging_data["paging"]["page_no"]);
        $grid_fields = json_decode($this->accounts_form->build_account_list_for_callshop());
        $json_data['rows'] = $this->form->build_grid($query, $grid_fields);

        echo json_encode($json_data);
    }

    function callshop_list_search() {
        $ajax_search = $this->input->post('ajax_search', 0);
        if ($this->input->post('advance_search', TRUE) == 1) {
            $this->session->set_userdata('advance_search', $this->input->post('advance_search'));
            $action = $this->input->post();
            unset($action['action']);
            unset($action['advance_search']);
            $this->session->set_userdata('callshop_list_search', $action);
        }
        if (@$ajax_search != 1) {
            redirect(base_url() . 'accounts/callshop_list/');
        }
    }

    function callshop_list_clearsearchfilter() {
        $this->session->set_userdata('advance_search', 0);
        $this->session->set_userdata('callshop_list_search', "");
    }

    function customer_delete($id) {
        $this->accounts_model->remove_customer($id);
        $this->free_customer_did($id);
        $this->free_ani_map($id);
        $this->session->set_flashdata('astpp_notification', 'Customer removed successfully!');
        redirect(base_url() . 'accounts/customer_list/');
    }

    function reseller_delete($id) {
        $reseller_ids=$this->common->subreseller_list($id);
        $where= "reseller_id IN ($reseller_ids) OR id = $id";
        $data=array('deleted'=>1);
        $this->db->where($where);
        $this->db->update('accounts',$data);
        $this->free_reseller_did($reseller_ids);
        $this->session->set_flashdata('astpp_notification', 'Reseller removed successfully!');
        redirect(base_url() . 'accounts/reseller_list/');
    }
    
    function free_customer_did($accountid)
    {
	  $this->db->where(array("accountid"=>$accountid));
	  $this->db->update("dids",array('accountid' => "0"));
	  return true;
    }
    function free_ani_map($accountid){
	  $this->db->where(array("accountid"=>$accountid));
	  $this->db->delete('ani_map');
	  return true;
    }
    function free_reseller_did($ids)
    {
     $accountinfo=$this->session->userdata('accountinfo');
     $reseller_id= $accountinfo['type'] != 1 ? 0 : $accountinfo['id'];
     $data=array('parent_id'=>$reseller_id,'accountid'=>0);
     $where="parent_id IN ($ids)";
     $this->db->where($where);
     $this->db->update('dids',$data);
     $where = "reseller_id IN ($ids)";
     $this->db->where($where);
     $this->db->delete('reseller_pricing');
     return true;
    }
    
    function provider_delete($id) {
        $this->accounts_model->remove_customer($id);
        
        $this->session->set_flashdata('astpp_notification', 'Provider removed successfully!');
        redirect(base_url() . 'accounts/customer_list/');
    }

    function admin_delete($id) {
        $this->accounts_model->remove_customer($id);
        $this->session->set_flashdata('astpp_notification', 'Admin removed successfully!');
        redirect(base_url() . 'accounts/admin_list/');
    }

    function subadmin_delete($id) {
   // print_r($id); exit;
        $this->accounts_model->remove_customer($id);
        $this->session->set_flashdata('astpp_notification', 'Sub admin removed successfully!');
        redirect(base_url() . 'accounts/admin_list/');
    }

    function callshop_delete($id) {
        $this->accounts_model->remove_customer($id);
        $this->session->set_flashdata('astpp_notification', 'Callshop removed successfully!');
        redirect(base_url() . 'accounts/callshop_list/');
    }

    function customer_ipmap_json($accountid, $accounttype) {
        $json_data = array();
        $where = array("accountid" => $accountid);
        $count_all = $this->db_model->countQuery("*", "ip_map", $where);

        $paging_data = $this->form->load_grid_config($count_all, $_GET['rp'], $_GET['page']);
        $json_data = $paging_data["json_paging"];
        $query = $this->db_model->select("*", "ip_map", $where, "id", "ASC", $paging_data["paging"]["page_no"], $paging_data["paging"]["start"]);

        $grid_fields = json_decode($this->accounts_form->build_ip_list_for_customer($accountid, $accounttype));
        $json_data['rows'] = $this->form->build_grid($query, $grid_fields);

        echo json_encode($json_data);
    }

    function customer_animap_json($accountid) {
        $json_data = array();
        $where = array("accountid" => $accountid);
        $count_all = $this->db_model->countQuery("*", "ani_map", $where);

        $paging_data = $this->form->load_grid_config($count_all, $_GET['rp'], $_GET['page']);
        $json_data = $paging_data["json_paging"];
        $query = $this->db_model->select("*", "ani_map", $where, "id", "ASC", $paging_data["paging"]["page_no"], $paging_data["paging"]["start"]);

        $grid_fields = json_decode($this->accounts_form->build_animap_list_for_customer($accountid));
        $json_data['rows'] = $this->form->build_grid($query, $grid_fields);

        echo json_encode($json_data);
    }

    function customer_iax_sip_json($account_number = NULL) {

        $account_device_list = array();
        $account = $account_number;
        if ($account) {
            $data['account'] = $account;
            $data['account_number'] = $account_number;
            $this->load->model('common_model');

            $rt_db = 0;
            if ($rt_db) {

                $sip_names = $this->common_model->list_sip_account_rt($account_number);

                $iax_names = $this->common_model->list_iax_account_rt($account_number);

                foreach ($sip_names as $key => $value) {
                    $deviceinfo = $this->common_model->get_sip_account_rt($value['name']);
                    $row = array();
                    $row['tech'] = "SIP";
                    $row['type'] = $deviceinfo['type'];
                    $row['username'] = $deviceinfo['username'];
                    $row['secret'] = $deviceinfo['secret'];
                    $row['context'] = $deviceinfo['context'];
                    array_push($account_device_list, $row);
                }

                foreach ($iax_names as $key => $value) {
                    $deviceinfo = $this->common_model->get_iax_account_rt($value['name']);
                    $row = array();
                    $row['tech'] = "IAX2";
                    $row['type'] = $deviceinfo['type'];
                    $row['username'] = $deviceinfo['username'];
                    $row['secret'] = $deviceinfo['secret'];
                    $row['context'] = $deviceinfo['context'];
                    array_push($account_device_list, $row);
                }
            }

            $fs_db = 1;
            if ($fs_db) {
                $sip_devices = $this->common_model->fs_list_sip_usernames($account_number);
                if (count($sip_devices) > 0) {
                    foreach ($sip_devices as $key => $record) {

                        $deviceinfo = $this->switch_config_model->fs_retrieve_sip_user($record['id']);
                        $row = array();
                        $row['tech'] = "SIP";
                        $row['type'] = "user@" . $record['domain'];
                        $row['username'] = $record['username'];
                        $row['secret'] = $deviceinfo['password'];
                        $row['context'] = $deviceinfo['context'];
                        array_push($account_device_list, $row);
                    }
                }
            }

            $freepbx_db = 0;
            if ($freepbx_db) {
                $sip_names_freepbx = $this->common_model->list_sip_account_freepbx($account_number);
                $iax_names_freepbx = $this->common_model->list_iax_account_freepbx($account_number);

                foreach ($sip_names_freepbx as $key => $value) {
                    $deviceinfo = $this->common_model->get_sip_account_freepbx($value['name']);
                    $row = array();
                    $row['tech'] = "SIP";
                    $row['type'] = $deviceinfo['type'];
                    $row['username'] = $deviceinfo['username'];
                    $row['secret'] = $deviceinfo['secret'];
                    $row['context'] = $deviceinfo['context'];
                    array_push($account_device_list, $row);
                }
                foreach ($iax_names_freepbx as $key => $value) {
                    $deviceinfo = $this->common_model->get_iax_account_freepbx($value['name']);
                    $row = array();
                    $row['tech'] = "IAX2";
                    $row['type'] = $deviceinfo['type'];
                    $row['username'] = $deviceinfo['username'];
                    $row['secret'] = $deviceinfo['secret'];
                    $row['context'] = $deviceinfo['context'];
                    array_push($account_device_list, $row);
                }
            }
        }

        $count_all = count($account_device_list);
        $config['total_rows'] = $count_all;
        $config['per_page'] = $_GET['rp'];
        $page_no = $_GET['page'];

        $json_data['page'] = $page_no;
        $json_data['total'] = $config['total_rows'];

        $perpage = $config['per_page'];
        $start = ($page_no - 1) * $perpage;
        if ($start < 0)
            $start = 0;

        for ($i = $start; $i <= ($config['per_page'] + $start - 1); $i++) {
            if (isset($account_device_list[$i]['tech']) && $account_device_list[$i]['tech'] != "") {
                $json_data['rows'][] = array('cell' => array(
                        $account_device_list[$i]['tech'],
                        $account_device_list[$i]['type'],
                        $account_device_list[$i]['username'],
                        $account_device_list[$i]['secret'],
                        $account_device_list[$i]['context'],
                        ));
            }
        }

        echo json_encode($json_data);
    }

    function customer_details_json($module, $accountid) {
        $entity_type =$this->common->get_field_name('type','accounts',array('id'=>$accountid));
	$entity_type =strtolower($this->common->get_entity_type('','',$entity_type));
        if ($module == "pattern") {
            $this->load->module('rates/rates');
            $this->rates->customer_block_pattern_list($accountid);
        }
        if ($module == "freeswitch") {
            $this->load->module('freeswitch/freeswitch');
            $this->freeswitch->customer_fssipdevices_json($accountid);
        }
        if ($module == "did") {
            $this->load->module('did/did');
            $this->did->customer_did($accountid,$entity_type);
        }
        if ($module == "invoices") {
            $this->load->module('invoices/invoices');
            $this->invoices->customer_invoices($accountid);
        }
        if ($module == "charges") {
            $this->load->module('charges/charges');
            $this->charges->customer_charge_list($accountid, $entity_type);
        }
        if ($module == "reports") {
            $this->load->module('reports/reports');
            $this->reports->customer_cdrreport($accountid,$entity_type);
        }
        if ($module == "opensips") {
            $this->load->module('opensips/opensips');
            $this->opensips->customer_opensips_json($accountid);
        }
    }

    function customer_add_blockpatterns($accountid) {
        $data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = 'Unblocked Prefixes';
        $this->session->set_userdata('advance_search', 0);
        $this->load->module('rates/rates');
        $data['patters_grid_fields'] = $this->rates->rates_form->build_outbound_list_for_customer();
        $data["accountid"] = $accountid;
        $this->load->view('view_block_prefix_list', $data);
    }

    function customer_add_blockpatterns_json($accountid) {
	$this->load->module('rates/rates');
        $json_data = array();
        $count_all = $this->rates_model->getunblocked_pattern_list($accountid,false);
        $paging_data = $this->form->load_grid_config($count_all, $_GET['rp'], $_GET['page']);
        $json_data = $paging_data["json_paging"];

        $query = $this->rates->rates_model->getunblocked_pattern_list($accountid,true, $paging_data["paging"]["start"], $paging_data["paging"]["page_no"]);
        $grid_fields = json_decode($this->rates->rates_form->build_outbound_list_for_customer());
        $json_data['rows'] = $this->rates->form->build_grid($query, $grid_fields);

        echo json_encode($json_data);
        
    }

    function reseller_details_json($module, $accountid) {

        if ($module == "did") {
            $this->load->module('did/did');
            $this->did->reseller_did($accountid, "reseller");
        }
        if ($module == "invoices") {
            $this->load->module('invoices/invoices');
            $this->invoices->customer_invoices($accountid);
        }
        if ($module == "charges") {
            $this->load->module('charges/charges');
            $this->charges->customer_charge_list($accountid, "reseller");
        }
    }

    function provider_details_json($module, $accountid) {
        if ($module == "invoices") {
            $this->load->module('invoices/invoices');
            $this->invoices->customer_invoices($accountid);
        }
    }

    function customer_block_prefix($accountid) {
        $result = $this->accounts_model->insert_block($this->input->post('prefixies', true), $accountid);
        echo $result;
        exit;
    }

    function customer_charges_action($action, $accountid, $accounttype, $chargeid = "") {
        if ($action == "add") {
            $charge_id = $this->input->post("applayable_charge", true);
            if ($charge_id != "") {
                $insert_arr = array("charge_id" => $charge_id,"accountid" => $accountid, "status" => "1","assign_date"=>gmdate("Y-m-d H:i:s"));
                $this->db->insert("charge_to_account", $insert_arr);
 	    	$this->session->set_flashdata('astpp_errormsg', 'Subscripton Added Sucessfully.');
                redirect(base_url() . "accounts/" . $accounttype . "_edit/$accountid#packages");
            } else {
                redirect(base_url() . "accounts/" . $accounttype . "_edit/$accountid#packages");
            }
        }
        if ($action == "delete") {
            $this->db_model->delete("charge_to_account", array("id" => $chargeid));
 	    $this->session->set_flashdata('astpp_notification', 'Subscription Removed Sucessfully.');
            redirect(base_url() . "accounts/" . $accounttype . "_edit/$accountid#packages");
        }
    }

    function customer_add_postcharges($accounttype, $accountid) {
        $charge = $this->input->post("amount", true);
        if ($charge != "") {
            $charge = $this->common_model->add_calculate_currency($charge, "", '', false, false);
            $date = date('Y-m-d H:i:s');
            $insert_arr = array("accountid" => $accountid, "description" => $this->input->post("desc", true),
                "created_date" => $date, "debit" => $charge,"charge_type" => "post_charge");
            $this->db->insert("invoice_item", $insert_arr);

            $this->accounts_model->update_balance($charge, $accountid, "debit");
            redirect(base_url() . "accounts/" . $accounttype . "_edit/$accountid#packages");
        } else {
            redirect(base_url() . "accounts/" . $accounttype . "_edit/$accountid#packages");
        }
    }
   function customer_dids_action($action, $accountid, $accounttype, $did_id = "") {
   
	
        if ($action == "add") {
            $did_id = $this->input->post("free_did_list", true);
//             echo    $did_id."-".$accountid."-".$this->session->userdata('logintype');
            
            if ($did_id != "") {
            
            
                $did_query = $this->db_model->getSelect("*", "dids", array("id" => $did_id));
//		 for getting reseller setup price if reseller customer purchase
		$did_arr = $did_query->result_array();
		
		
                $reseller_pricing_query = $this->db_model->getSelect("setup", "reseller_pricing", array("note" => $did_arr[0]['number']));
		
		$account_query = $this->db_model->getSelect("*", "accounts", array("id" => $accountid));
		$account_arr = $account_query->result_array();
                
                $available_bal = $this->db_model->get_available_bal($account_arr[0]);
                
		$uri=$this->uri->uri_string;$type_url=explode("/",$uri);
		
		if($reseller_pricing_query->num_rows() > 0)
                 {
		    $reseller_pricing_query= $reseller_pricing_query->result_array();
		    $setup_charge = $reseller_pricing_query[0]['setup'];
                 }else{
		    $setup_charge = $did_arr[0]["setup"];
                 }
		if($this->session->userdata('logintype') != -1) 
		{
		      if ($available_bal >= $setup_charge)
		      {
			$available_bal = $this->db_model->update_balance($setup_charge, $accountid, "debit");
			$this->load->module('did/did');
			
			$this->did->did_model->add_invoice_data($accountid,"did_charge",'DID Purchase',$setup_charge);
			
			$this->db_model->update("dids", array("accountid" => $accountid), array("id" => $did_id));
			
			if($this->session->userdata('logintype') == '1' && @$type_url[4] == 'reseller')
			{
			    $this->accounts_model->add_reseller_pricing($accountid,$did_id);
			    if($this->session->userdata('logintype') == '1' && @$type_url[4] == 'reseller')
				redirect(base_url() . "did/did_list/"  );
			    else  
				redirect(base_url() . "accounts/" . $accounttype . "_edit/$accountid#did");
			}
			else{
	         	    $this->session->set_flashdata('astpp_errormsg', 'Did added successfully.');
			    redirect(base_url() . "accounts/" . $accounttype . "_edit/$accountid#did");
			}
		    }
		    else 
		    {
			$this->session->set_flashdata('astpp_notification', 'Insuffiecient fund to purchase this DID');

			    
			    if($accounttype == 'customer' || $this->session->userdata('logintype') == 2){
				redirect(base_url() . "accounts/" . $accounttype . "_edit/$accountid#did");
			    }else{
				redirect(base_url() . "did/did_list/"  );
			    }
		    }
                }
                else
                {
		      $this->db_model->update("dids", array("accountid" => $accountid), array("id" => $did_id));
		      if($this->session->userdata('logintype') == '1' && @$type_url[4] == 'reseller')
		      {
			  $this->accounts_model->add_reseller_pricing($accountid,$did_id);
			  if($this->session->userdata('logintype') == '1' && @$type_url[4] == 'reseller')
			      redirect(base_url() . "did/did_list/"  );
			  else  
			      redirect(base_url() . "accounts/" . $accounttype . "_edit/$accountid#did");
		      }
		      else{
			  redirect(base_url() . "accounts/" . $accounttype . "_edit/$accountid#did");
		      }
                }
                
            }
            else 
            {
                if($this->session->userdata('logintype') == '1')
                        redirect(base_url() . "did/did_list/"  );
                else
 	    	  //$this->session->set_flashdata('astpp_errormsg', 'Please Purchase Atleast One Did.');
		  redirect(base_url() . "accounts/" . $accounttype . "_edit/$accountid#did");
            }
        }
        if ($action == "delete") {
            $reseller_session_id=$this->session->userdata['accountinfo']['id'];
            if($this->session->userdata('logintype') == '1')
                $this->db_model->update("dids", array("accountid" => "0"), array("id" => $did_id));
            else
                $this->db_model->update("dids", array("accountid" => "0"), array("id" => $did_id));
            if($accounttype == 'reseller'){
                $did_query = $this->db_model->getSelect("*", "dids", array("id" => $did_id));
                $did_arr = $did_query->result_array();
                
                $delete_array=array('reseller_id'=>$accountid,'note'=>$did_arr[0]['number']);
                
                $this->db->where($delete_array);
                $this->db->delete('reseller_pricing');
            }
 	    $this->session->set_flashdata('astpp_notification', 'Did Removed Successfully.');
            redirect(base_url() . "accounts/" . $accounttype . "_edit/$accountid#did");
        }
    }
    
    function reseller_did_action($action, $accountid, $accounttype, $did_id = "")
    {
//	
	  if ($action == "add") {
            
	      $did_id = $this->input->post("free_did_list", true);
	      if ($did_id != "") {
// 		  $did_query = $this->db_model->getSelect("*", "dids", array("id" => $did_id));
// 		  $did_arr = $did_query->result_array();
		  
		  $account_query = $this->db_model->getSelect("*", "accounts", array("id" => $accountid));
		  
		  $account_arr = $account_query->result_array();
		  $idofaccount = $accountid;
		  //echo $idofaccount;exit;
// 		  echo "<pre>";print_r($this->session->userdata);echo "-----------".$accountid;exit;
// 		  if($this->session->userdata['userlevel_logintype'] == -1)
// 		  {
// 		    $accountid=0;
// 		  }else{
// 		    $accountid = $idofaccount;
// 		  }
		  $this->db_model->update("dids", array("parent_id" => $accountid), array("id" => $did_id));
		  $accountid = $idofaccount;
		//   echo $this->db->last_query(); exit;
		  $this->accounts_model->add_reseller_pricing($accountid,$did_id);
		 
		  $this->session->set_flashdata('astpp_errormsg', 'DID added successfully.');
		  redirect(base_url() . "accounts/" . $accounttype . "_edit/$accountid#did");
                    
	    }
	    else{
		redirect(base_url() . "accounts/" . $accounttype . "_edit/$accountid#did");
	    }
        }
        if ($action == "delete") {
             $this->db->where('id',$did_id);
             $this->db->select('note');
             $pricing_res=$this->db->get('reseller_pricing');
             if($pricing_res->num_rows() > 0){
             $pricing_res=$pricing_res->result_array();
             $did_number=$pricing_res[0]['note'];
             $accountinfo=$this->session->userdata('accountinfo');
             	  if($this->session->userdata['userlevel_logintype'] == -1)
		  {
		    $parent_id=0;
		  }else{
		    $parent_id=$this->session->userdata['accountinfo']['id'];
		  }

             $reseller_ids=$this->common->subreseller_list($accountinfo['id']);
             $pricing_where= "parent_id = $parent_id AND note = $did_number";
             $this->db->where($pricing_where);
             $this->db->delete('reseller_pricing');
            // 
             $dids_where="parent_id IN ($reseller_ids) AND number = $did_number";
             $this->db->where($dids_where);
             $data= array('accountid'=>0,'parent_id'=>$accountinfo['id']);
             $this->db->update('dids',$data);
           //  
	     $this->session->set_flashdata('astpp_notification', 'DID removed successfully.');
	    }else{
	      $this->session->set_flashdata('astpp_notification', 'DID already removed before.');
	    }
            redirect(base_url() . "accounts/" . $accounttype . "_edit/$accountid#did");
        }
    }
    function customer_ipmap_action($action, $accountid, $accounttype, $ipmapid = "") {
	$add_array=$this->input->post();
	
        if ($action == "add") {
            if ($add_array['ip'] != "") {
                $ip = $add_array['ip'];
                if (strpos($ip,'/') !== false) {
                   $add_array['ip']=$add_array['ip'];
                }
                else{
                   $add_array['ip']=$add_array['ip'].'/32';
                }
            $where = array("ip" => trim($add_array['ip']), "prefix" => trim($add_array['prefix']));
            $getdata = $this->db_model->countQuery("*", "ip_map", $where);
            if ($getdata > 0) {
                $this->session->set_flashdata('astpp_notification', 'IP already exist in system.');
                redirect(base_url() . "accounts/" . $accounttype . "_edit/$accountid#accounts");
            }
            else{
                if ($accounttype == "provider") {
			$add_array['pricelist_id']=0;
                }
                unset($add_array['action']);
                $add_array['context']='default';
		$add_array['accountid']=$accountid;
                $ip_flag = $this->db->insert("ip_map", $add_array);
                if ($ip_flag) {
                    $this->load->library('freeswitch_lib');
                    $this->load->module('freeswitch/freeswitch');
                    $command = "api reloadacl";
                    $response = $this->freeswitch_model->reload_freeswitch($command);
                    $this->session->set_userdata('astpp_notification',$response);
                }
 	    	$this->session->set_flashdata('astpp_errormsg', 'IP added sucessfully.');
                redirect(base_url() . "accounts/" . $accounttype . "_edit/$accountid#accounts");
            }
            } else {
                redirect(base_url() . "accounts/" . $accounttype . "_edit/$accountid#accounts");
            }
            
        }
        if ($action == "delete") {
            $ip_flag = $this->db_model->delete("ip_map", array("id" => $ipmapid));
            if ($ip_flag) {
                $this->load->library('freeswitch_lib');
                $this->load->model("freeswitch_model");
                $command = "api reloadacl";
                $this->freeswitch_model->reload_freeswitch($command);
            }
 	    	$this->session->set_flashdata('astpp_notification', 'IP removed sucessfully.');
            redirect(base_url() . "accounts/" . $accounttype . "_edit/$accountid#accounts");
        }
    }

    function customer_animap_action($action, $accountid, $aniid = "") {
       $entity_type =$this->common->get_field_name('type','accounts',array('id'=>$accountid));
       $entity_type =strtolower($this->common->get_entity_type('','',$entity_type));
       $url ="accounts/". $entity_type."_edit/$accountid#accounts";
        if ($action == "add") {
            $ani = $this->input->post();
            $this->db->where('number',$ani['number']);
            $this->db->select('count(id) as count');
            $cnt_result=$this->db->get('ani_map');
           // echo $this->db->last_query(); exit;
            $cnt_result=$cnt_result->result_array();
           // 
            $count=$cnt_result[0]['count'];
          //  echo '<pre>'; print_r($accountid); exit;
         
            if($count == 0 ){
	      if ($ani['number'] != "") {
		  $insert_arr = array("number" => $this->input->post('number'), "accountid" => $accountid,
		      "context" => "default");
		  $this->db->insert("ani_map", $insert_arr);
		  $this->session->set_flashdata('astpp_errormsg', 'Add Caller Id Sucessfully!');
		  redirect(base_url() .$url);
		  
	      } else {
		  $this->session->set_flashdata('astpp_notification', 'Please Enter Caller Id Field.');
		  redirect(base_url() . $url);
	      }
	    } 
	    else{
 		$this->session->set_flashdata('astpp_notification', ' Caller Id already Exists.');
		redirect(base_url() . $url);
	    }
      }
      if ($action == "delete") {
          $this->session->set_flashdata('astpp_notification', 'Caller Id removed sucessfully!');
          $this->db_model->delete("ani_map", array("id" => $aniid));
          redirect(base_url() .$url);
      }
    }
    function customer_delete_block_pattern($accountid, $patternid) {
    $entity_type =$this->common->get_field_name('type','accounts',array('id'=>$accountid));
       $entity_type =strtolower($this->common->get_entity_type('','',$entity_type));
       $url ="accounts/". $entity_type."_edit/$accountid#block_prefixes";
	$this->session->set_flashdata('astpp_notification', 'Block Prefix Removed Sucessfully!');
        $this->db_model->delete("block_patterns", array("id" => $patternid));
        redirect(base_url() . $url);
    }
    function customer_selected_delete() {
	$ids = $this->input->post("selected_ids", true);
        $this->reseller_customer_multidelete($ids);
        $this->free_customer_multiple_dids($ids);
        $this->free_multiple_ani($ids);
        echo TRUE;
    }
    
    function free_customer_multiple_dids($ids){
	$where = "accountid IN ($ids)";
	$this->db->where($where);
	$this->db->update("dids",array('accountid' => "0"));
    }
    
    function free_multiple_ani($ids){
	$where = "accountid IN ($ids)";
	$this->db->where($where);
	$this->db->delete('ani_map');
    }
    
    function reseller_customer_multidelete($ids){
        $where = "id IN ($ids) OR reseller_id IN($ids)";
        $data=array('deleted'=>1);
        $this->db->where($where);
        $this->db->update("accounts",$data);
    }
    
    function reseller_selected_delete() {
        $ids = $this->input->post("selected_ids", true);
        $id_arr=explode(',',$ids);
        $reseller_ids=null;
        foreach($id_arr as $data){
         $reseller_ids.=$this->common->subreseller_list($data);
         $reseller_ids.=',';
        }
        $reseller_ids=rtrim($reseller_ids,',');
        $this->reseller_customer_multidelete($reseller_ids);
        $this->free_reseller_multiple_dids($reseller_ids);
        echo TRUE;
    }
    
    function free_reseller_multiple_dids($ids){

     $accountinfo=$this->session->userdata('accountinfo');
     $reseller_id= $accountinfo['type'] != 1 ? 0 : $accountinfo['id'];
     $data=array('parent_id'=>$reseller_id,'accountid'=>0);
     $where="parent_id IN ($ids)";
     $this->db->where($where);
     $this->db->update('dids',$data);
     $where = "reseller_id IN ($ids)";
     $this->db->where($where);
     $this->db->delete('reseller_pricing');
     return true;
    }
    function callshop_selected_delete() {
        echo $this->delete_multiple();
    }

    function provider_selected_delete() {
        echo $this->delete_multiple();
    }

    function subadmin_selected_delete() {
        echo $this->delete_multiple();
    }

    function admin_selected_delete() {
        echo $this->delete_multiple();
    }
    function delete_multiple() {
        $ids = $this->input->post("selected_ids", true);
        $where = "id IN ($ids)";
        $data=array('deleted'=>1);
        $this->db->where($where);
        $this->db->update("accounts",$data);
        echo TRUE;
    }
    function user_animap_json($accountid) {
        $json_data = array();
        $where = array("accountid" => $accountid);
        $count_all = $this->db_model->countQuery("*", "ani_map", $where);

        $paging_data = $this->form->load_grid_config($count_all, $_GET['rp'], $_GET['page']);
        $json_data = $paging_data["json_paging"];
        $query = $this->db_model->select("*", "ani_map", $where, "id", "ASC", $paging_data["paging"]["page_no"], $paging_data["paging"]["start"]);
        $grid_fields = json_decode($this->accounts_form->build_animap_list_for_user());
        $json_data['rows'] = $this->form->build_grid($query, $grid_fields);

        echo json_encode($json_data);
    }

    function customer_account_taxes($action = false, $id = false) {
        $data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = 'Account Taxes';

        if ($action == false)
            $action = "list";

        if ($action == 'list') {
            $this->load->view('view_account_taxes_list', $data);
        } elseif ($action == 'add') {

            if (($this->input->post())) {
                $post_array = $this->input->post();
                $query = $this->accounts_model->remove_all_account_tax($post_array['account_id']);

                foreach ($post_array as $key => $value) {
                    $id = explode("_", $key);
                    if ($id[0] == 'tax') {
                        $data = array(
                            'accountid' => $post_array['account_id'],
                            'taxes_id' => $post_array[$key],
                        );
                        $this->accounts_model->add_account_tax($data);
                    }
                }
                $this->session->set_flashdata('astpp_errormsg', 'Account tax added successfully!');
                redirect(base_url() . 'accounts/customer_list/');
            }
            $data['id'] = array();
            $data['taxesList'] = $this->common_model->get_list_taxes();
            $this->load->view('view_accounting_taxes_add', $data);
        } elseif ($action == 'edit') {
            $taxes_id = $this->accounts_model->get_accounttax_by_id($id);
            $account_num = $this->accounts_model->get_account_number($id);
            $data['accountnum'] = $account_num['number'];
            $data['account_id'] = $id;
            for ($i = 0; $i < count($taxes_id); $i++) {
                $tax_ids[] = $taxes_id[$i]['taxes_id'];
            }
            $data['tax_ids'] = $tax_ids;

            $data['tax_id'] = $taxes_id;

            if (($this->input->post())) {
                $post_array = $this->input->post();
                $accountinfo = $this->accounts_model->get_account_by_number($post_array['account_id']);
                $query = $this->accounts_model->remove_all_account_tax($post_array['account_id']);
                foreach ($post_array as $key => $value) {
                    $id = explode("_", $key);
                    if ($id[0] == 'tax') {
                        $data = array(
                            'accountid' => $post_array['account_id'],
                            'taxes_id' => $post_array[$key],
                        );
                        $this->accounts_model->add_account_tax($data);
                    }
                }
                if ($accountinfo['type'] == '0') {
                    $link = base_url() . '/accounts/customer_list/';
                } else {
                    $link = base_url() . '/accounts/reseller_list/';
                }
                $this->session->set_flashdata('astpp_errormsg', 'Account tax added successfully!');
                redirect($link);
            }
            $data['taxesList'] = $this->common_model->get_list_taxes();
            $this->load->view('view_accounting_taxes_add', $data);
        } elseif ($action == 'delete') {
            $this->accounting_model->remove_account_tax($id);
            $this->session->set_flashdata('astpp_notification', 'Account tax removed successfully!');
            redirect(base_url() . 'accounting/account_taxes/');
        }
    }

    /**
     * -------Here we write code for controller accounting functions vallid_account_tax------
     * here this function called by ajax form and vallidate the account number
     * @$_POST['username']: Account Number
     */
    function valid_account_tax() {
        $tax_id = '';
        if (!empty($_POST['username'])) {

            $account_num = mysql_real_escape_string($_POST['username']);
            $row = $this->accounts_model->check_account_num($account_num);
            if (isset($row['accountid']) && $row['accountid'] != '') {
                $taxes_id = $this->accounts_model->get_accounttax_by_id($row['accountid']);
                if ($taxes_id) {
                    foreach ($taxes_id as $id) {
                        $tax_id.=$id['taxes_id'] . ",";
                    }

                    $tax_id = rtrim($tax_id, ",");
                    echo $row['accountid'] . ',' . $tax_id;
                } else {
                    echo $row['accountid'];
                }
            }
        }
    }
    function customer_fssipdevices_action($action, $id, $accountid) {
       $entity_type =$this->common->get_field_name('type','accounts',array('id'=>$accountid));
       $entity_type =strtolower($this->common->get_entity_type('','',$entity_type));
       $url ="accounts/". $entity_type."_edit/$accountid#accounts";
        $this->load->module('freeswitch/freeswitch');
        if ($action == "delete") {
            $this->session->set_flashdata('astpp_notification', 'Sip Device removed successfully!');
            $this->freeswitch->freeswitch_model->delete_freeswith_devices($id);
            redirect(base_url() . $url);
        }
        if ($action == "edit") {
	    $this->session->set_flashdata('astpp_errormsg', 'Sip updated successfully!');	    
            $this->freeswitch->customer_fssipdevices_edit($id, $accountid);
        }
    }

    function customer_opensips_action($action,$accountid,$id) {
      $entity_type =$this->common->get_field_name('type','accounts',array('id'=>$accountid));
       $entity_type =strtolower($this->common->get_entity_type('','',$entity_type));
       $url ="accounts/". $entity_type."_edit/$accountid#accounts";
        $this->load->module('opensips/opensips');
        if ($action == "delete") {
            $this->opensips->opensips_model->remove_opensips($id);
            $this->session->set_flashdata('astpp_notification', 'Opensips removed successfully!');
            redirect(base_url() . $url);
        }
        if ($action == "edit") {
            $this->opensips->customer_opensips_edit($accountid,$id);
	    $this->session->set_flashdata('astpp_errormsg', 'Opensips updated successfully!');
        }
    }


    function reseller_edit_account() {
        $account_data = $this->session->userdata("accountinfo");

        $add_array = $this->input->post();
        $data['form'] = $this->form->build_form($this->accounts_form->get_reseller_own_form_fields(), $add_array);
        if ($add_array['id'] != '') {
            $data['page_title'] = 'Edit Reseller';
            if ($this->form_validation->run() == FALSE) {
                $data['validation_errors'] = validation_errors();
            } else {
                $this->accounts_model->edit_account($add_array, $add_array['id']);
		$accountinfo=$this->session->userdata('accountinfo');
		if($add_array['id']==$accountinfo['id'] ){
		  $result=$this->db->get_where('accounts',array('id'=>$add_array['id']));
		  $result=$result->result_array();
		  $this->session->set_userdata('accountinfo',$result[0]);
		}
                $this->session->set_flashdata('astpp_errormsg', 'Reseller updated successfully!');
                redirect(base_url() . '/dashboard/');
            }
            $this->load->view('view_reseller_edit_details_own', $data);
        } else {

            $data['page_title'] = 'Edit Reseller';
            $where = array('id' => $account_data["id"]);
            $account = $this->db_model->getSelect("*", "accounts", $where);
            $data["account_data"] = $account->result_array();

            foreach ($account->result_array() as $key => $value) {
                $editable_data = $value;
            }
            $data['form'] = $this->form->build_form($this->accounts_form->get_reseller_own_form_fields(), $editable_data);
            $this->load->view('view_reseller_edit_details_own', $data);
        }
    }

function customer_animap_list($id='') {
	$data['animap_id']=$id;
	$data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = "Caller Id List";
        $this->session->set_userdata('animap_search', 0);
        $data['grid_fields'] = $this->accounts_form->build_animap_list();
        $data["grid_buttons"] = $this->accounts_form->build_grid_buttons_destination();
        $this->load->view('view_ani_map',$data);
    }
    function customer_animap_list_json($id=''){
	$json_data = array();
        $count_all = $this->accounts_model->get_animap(false,'','',$id);
        $data['callingcard_id'] = $id;
        $paging_data = $this->form->load_grid_config($count_all, $_GET['rp'], $_GET['page']);        
        $json_data = $paging_data["json_paging"];
        $query = $this->accounts_model->get_animap(true, $paging_data["paging"]["start"], $paging_data["paging"]["page_no"],$id);
        $grid_fields = json_decode($this->accounts_form->build_animap_list());
        $json_data['rows'] = $this->form->build_grid($query, $grid_fields);
        echo json_encode($json_data);
        exit;
    }

    function customer_animap_list_action($id=''){ 

	  $add_array=$this->input->post();

          $add_array['id']=trim($add_array['id']);
          $add_array['number']=trim($add_array['number']);
          if(isset($add_array['id']) && $add_array['id'] != ''){
              $add_array['id']=trim($add_array['id']);
              $id=$add_array['id'];
          }
          $where=array("number"=>$add_array['number']);
          $pro =$this->accounts_model->animap_authentication($where,$id);
          if($pro > 0){
		echo "2";
		exit;
            }
          if(isset($add_array['number'])&& !empty($add_array['number'])){
	        if(isset($add_array['id']) && $add_array['id'] != ''){
                unset($add_array['animap_id']);
		    	$response = $this->accounts_model->edit_animap($add_array,$add_array['id']);
			echo "1";
			exit;
                }else{
		    $add_array['context']="default";
		    unset($add_array['animap_id']);
			$add_array['accountid']=$id;
                        $response = $this->accounts_model->add_animap($add_array);
                        echo "0";
                        exit;
                    }
             }
            else{
                echo "3";
                exit;
            }
    }
    function customer_animap_list_remove($id){
             $this->accounts_model->remove_ani_map($id);
	     echo "1";
	     exit;
    }
    function customer_animap_list_edit($id){
    $where = array('id' => $id);
        $account = $this->db_model->getSelect("*", "ani_map", $where);
        foreach ($account->result_array() as $key => $value) {
            $edit_data = $value;
        }
        $value_edit='';
        foreach($edit_data as $key => $value){
            $value_edit.=$value.",";
        }
        echo rtrim($value_edit,',');
        exit;
    }

   function provider_edit_account(){
      $this->customer_edit_account();
   }
}

?>
 
