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
class DID extends MX_Controller {

    function DID() {
        parent::__construct();

        $this->load->helper('template_inheritance');
        $this->load->library('session');
        $this->load->library('did_form');
        $this->load->library('astpp/form');
        $this->load->model('did_model');
        $this->load->library('csvreader');

        if ($this->session->userdata('user_login') == FALSE)
            redirect(base_url() . '/astpp/login');
    }

    function did_add($type = "") {
        $data['username'] = $this->session->userdata('user_name');
        $data['flag'] = 'create';
        $data['page_title'] = 'Add Did';
        $data['form'] = $this->form->build_form($this->did_form->get_dids_form_fields(), '');
	$data['country_id']=$this->common->get_field_name('id', 'countrycode', array('country'=>Common_model::$global_config['system_config']['country']));
         if(!$data['country_id'])
        {
                $data['country_id']=1;
        }
        $this->load->view('view_did_add_edit', $data);
    }

    function did_edit($edit_id = '') {
    
        $data['page_title'] = 'Edit Did';
        $where = array('id' => $edit_id);
        $account = $this->db_model->getSelect("*", "dids", $where);
        foreach ($account->result_array() as $key => $value) {
            $edit_data = $value;
        }
         $data['country_id']=Common_model::$global_config['system_config']['country'];
      
         if(!$data['country_id'])
        {
                $data['country_id']=1;
        }
        $edit_data['setup'] = $this->common_model->to_calculate_currency($edit_data['setup'], '', '', false, false);
//         $edit_data['disconnectionfee'] = $this->common_model->to_calculate_currency($edit_data['disconnectionfee'], '', '', false, false);
        $edit_data['monthlycost'] = $this->common_model->to_calculate_currency($edit_data['monthlycost'], '', '', false, false);
        $edit_data['connectcost'] = $this->common_model->to_calculate_currency($edit_data['connectcost'], '', '', false, false);
        $edit_data['cost'] = $this->common_model->to_calculate_currency($edit_data['cost'], '', '', false, false);
        $parent_id=$edit_data['parent_id'];
	$account_id=$edit_data['accountid'];
        if($parent_id > 0){
        $data['form'] = $this->form->build_form($this->did_form->get_dids_form_fields($edit_id,$parent_id,$account_id), $edit_data);
        }else{
        $data['form'] = $this->form->build_form($this->did_form->get_dids_form_fields($edit_id,'',$account_id), $edit_data);
        }
        $this->load->view('view_did_add_edit', $data);
    }
    function did_save() {
        $add_array = $this->input->post();
        $data['form'] = $this->form->build_form($this->did_form->get_dids_form_fields($add_array['id']), $add_array);
        if ($add_array['id'] != '') {
            $data['page_title'] = 'Edit Account Details';
            if ($this->form_validation->run() == FALSE) {
                $data['validation_errors'] = validation_errors();
                echo $data['validation_errors'];
                exit;
            } else {
				$number = $add_array['number'];
                unset($add_array['number']);

                $add_array['setup'] = $this->common_model->add_calculate_currency($add_array['setup'], '', '', false, false);
//                 $add_array['disconnectionfee'] = $this->common_model->add_calculate_currency($add_array['disconnectionfee'], '', '', false, false);
                $add_array['monthlycost'] = $this->common_model->add_calculate_currency($add_array['monthlycost'], '', '', false, false);
                $add_array['connectcost'] = $this->common_model->add_calculate_currency($add_array['connectcost'], '', '', false, false);
                $add_array['cost'] = $this->common_model->add_calculate_currency($add_array['cost'], '', '', false, false);
                $this->did_model->edit_did($add_array, $add_array['id'],$number);
                echo json_encode(array("SUCCESS"=> $number." DID Updated Successfully!"));
                exit;
            }
        } else {
            $data['page_title'] = 'Create Account Details';
            if ($this->form_validation->run() == FALSE) {
                $data['validation_errors'] = validation_errors();
                echo $data['validation_errors'];
                exit;
            } else {
		$check_did_number = $this->did_model->check_unique_did($add_array['number']);
                if($check_did_number > 0)
                {
                    echo json_encode(array("number_error"=> "Number already exist in system."));
                    exit;
                }

                $add_array['setup'] = $this->common_model->add_calculate_currency($add_array['setup'], '', '', false, false);
//                $add_array['disconnectionfee'] = $this->common_model->add_calculate_currency($add_array['disconnectionfee'], '', '', false, false);
                $add_array['monthlycost'] = $this->common_model->add_calculate_currency($add_array['monthlycost'], '', '', false, false);
                $add_array['connectcost'] = $this->common_model->add_calculate_currency($add_array['connectcost'], '', '', false, false);
                $add_array['cost'] = $this->common_model->add_calculate_currency($add_array['cost'], '', '', false, false);
                $response = $this->did_model->add_did($add_array);
                echo json_encode(array("SUCCESS"=> $add_array["number"]." DID Added Successfully!"));
                exit;
                exit;
            }
        }
    }

    function did_remove($id) {
        $this->did_model->remove_did($id);
        $this->session->set_flashdata('astpp_notification', 'DID Removed Successfully!');
        redirect(base_url() . 'did/did_list/');
    }
    function did_list_reliase($id) {
        $where =array('id'=>$id);
        $reseller_did = $this->db_model->getSelect( 'parent_id,accountid,number' ,'dids',$where);
        $reliase_did= $reseller_did->result_array();
        foreach($reliase_did as $key=>$value){
        $parent_id=$value['parent_id'];
        $account_id=$value['accountid'];
        $number=$value['number'];
       }
          if($parent_id > 0){
          $this->db->where("note", $number);
          $this->db->delete("reseller_pricing");
          //echo $this->last_query(); exit;
        }
        $where = array('id' => $id);
        $update_array = array('parent_id' => 0, 'accountid' => 0);
        $this->db->where($where);
        $this->db->update('dids', $update_array);
        $this->session->set_flashdata('astpp_errormsg', 'DID Released Successfully!');
        redirect(base_url() . 'did/did_list/');
    }

    function did_list() {
        $data['app_name'] = 'ASTPP - Open Source Billing Solution | Manage DIDs | DIDS';
        $data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = 'DIDs';
        $data['search_flag'] = true;
        $data['cur_menu_no'] = 4;
        $this->session->set_userdata('did_search', 0);
        

        if ($this->session->userdata('logintype') == 2) {
            $data["grid_buttons"] = $this->did_form->build_grid_buttons();
        } else {
            $data["grid_buttons"] = json_encode(array());
        }
        
        if($this->session->userdata['userlevel_logintype'] == '1')
 	{
	    $drp_list = array();
	    $accountinfo=$this->session->userdata('accountinfo');
	    $reseller_id=$accountinfo['type']!= 1 ? 0 : $accountinfo['reseller_id'];
	    $where =array('parent_id'=>$reseller_id,"accountid"=>"0");
	    $reseller_did = $this->db_model->getSelect( '*' ,'dids',$where);

// 	    echo $this->db->last_query();exit;
	    $dids_array = $reseller_did->result_array();
	    foreach ($dids_array as $drp_value) {
	      $drp_list[$drp_value['id']] = $drp_value['number'];
	    }
	    $data['didlist'] = form_dropdown_all('free_did_list', $drp_list, '');
        }

        
        if($this->session->userdata['userlevel_logintype'] == '1')
 	{
	  $data['grid_fields'] = $this->did_form->build_did_list_for_reseller_login();
	  $data['form_search'] = $this->form->build_serach_form($this->did_form->get_search_did_form_for_reseller());
	}else{
	  $data['grid_fields'] = $this->did_form->build_did_list_for_admin();
	  $data['form_search'] = $this->form->build_serach_form($this->did_form->get_search_did_form());
	}
        $this->load->view('view_did_list', $data);
    }

    /**
     * -------Here we write code for controller accounts functions account_list------
     * Listing of Accounts table data through php function json_encode
     */
    function did_list_json() {
        $json_data = array();

        $count_all = $this->did_model->getdid_list(false);
        $paging_data = $this->form->load_grid_config($count_all, $_GET['rp'], $_GET['page']);
        $json_data = $paging_data["json_paging"];
	$list = $this->session->userdata['userlevel_logintype'] == 1 ? $this->did_form->build_did_list_for_reseller_login() :$this->did_form->build_did_list_for_admin();
        $query = $this->did_model->getdid_list(true, $paging_data["paging"]["start"], $paging_data["paging"]["page_no"]);
        $grid_fields = json_decode($list);
        $json_data['rows'] = $this->form->build_grid($query, $grid_fields);

        echo json_encode($json_data);
    }

    function did_list_search() {
        $ajax_search = $this->input->post('ajax_search', 0);
        if ($this->input->post('advance_search', TRUE) == 1) {
            $this->session->set_userdata('advance_search', $this->input->post('advance_search'));
            $action = $this->input->post();
            unset($action['action']);
            unset($action['advance_search']);
            $this->session->set_userdata('did_list_search', $action);
        }
        if (@$ajax_search != 1) {
            redirect(base_url() . 'did/did_list/');
        }
    }

    function did_list_clearsearchfilter() {
        $this->session->set_userdata('advance_search', 0);
        $this->session->set_userdata('did_search', "");
    }

    /* -------Here we write code for controller did functions did_import------
     * @Purpose this function check if account number exist or not then remove from database.
     * @params $account_number: Account Number
     * @return Return Appropreate message If Account Delete or not.
     */
    function reseller_did($accountid, $accounttype)
    {
	$json_data = array();
        $account_query = $this->db_model->getSelect("*", "accounts", array("id" => $accountid));
        $account_arr = $account_query->result_array();
        
	$this->db->where("reseller_id",$accountid);
	$this->db->select('id');
        $query = $this->db->get('accounts');
        $data = $query->result_array();
        
        $count_all = $this->db_model->countQuery("*", "reseller_pricing", array("reseller_id"=>$accountid));
        $paging_data = $this->form->load_grid_config($count_all, $_GET['rp'], $_GET['page']);
	$json_data = $paging_data["json_paging"];
	$this->db->select('*,note as number',false);
	
	$this->db->where("reseller_id",$accountid);

        if (@$flag) {
              $this->db->order_by('id','ASC');
              $this->db->limit($limit,$start);
        }
        
        $query = $this->db->get('reseller_pricing');
         //echo $this->db->last_query();exit;
        $did_grid_fields = json_decode($this->did_form->build_did_list_for_reseller($accountid, $accounttype));
	$json_data['rows'] = $this->form->build_grid($query, $did_grid_fields);
	
	
	
        echo json_encode($json_data);
        
    }

    function customer_did($accountid, $accounttype) {
        $json_data = array();
        
 	$account_query = $this->db_model->getSelect("*", "accounts", array("id" => $accountid));
        $account_arr = $account_query->result_array();
//         echo "<pre>";print_r($account_arr);
        if($account_arr[0]['reseller_id'] != 0)
        {
	      $where = array('dids.accountid' => $accountid);
	      $group_by='`dids`.`number`';
	      $jionCondition = 'dids.number = reseller_pricing.note';
	      $count_all = $this->db_model->getJionQueryCount("dids", '*', $where,"reseller_pricing",$jionCondition,'inner','','','','',$group_by);
	      $paging_data = $this->form->load_grid_config($count_all, $_GET['rp'], $_GET['page']);
	      $json_data = $paging_data["json_paging"];
	      
// 	      $query = $this->db_model->getSelect("*", "dids", $where, "id", "ASC", $paging_data["paging"]["page_no"], $paging_data["paging"]["start"]);//echo*/ 
	      
	      //"<pre>";print_r($query);
	      
	      $query=$this->db_model->getJionQuery("dids", 'reseller_pricing.setup,reseller_pricing.cost,reseller_pricing.connectcost,dids.inc,reseller_pricing.includedseconds,reseller_pricing.monthlycost,dids.number,dids.id,dids.accountid,dids.extensions,dids.status,dids.provider_id,dids.allocation_bill_status,dids.dial_as,reseller_pricing.disconnectionfee,dids.call_type,dids.country_id', $where, "reseller_pricing", $jionCondition, 'inner',$paging_data["paging"]["page_no"], $paging_data["paging"]["start"],"dids.id", "ASC",  $group_by);
	      
// 	      echo "<pre>";echo $this->db->last_query();print_r($query->result());exit;
        }else{
        
	    $where = array('accountid' => $accountid); 
	    $count_all = $this->db_model->countQuery("*", "dids", $where);
	    $paging_data = $this->form->load_grid_config($count_all, $_GET['rp'], $_GET['page']);
	    $json_data = $paging_data["json_paging"];
	    $query = $this->db_model->getSelect("*", "dids", $where, "id", "ASC", $paging_data["paging"]["page_no"], $paging_data["paging"]["start"]);
// 	    echo "sdddddd";echo "<pre>";echo $this->db->last_query();print_r($query->result());exit;
        }
	$did_grid_fields = json_decode($this->did_form->build_did_list_for_customer($accountid, $accounttype));
	$json_data['rows'] = $this->form->build_grid($query, $did_grid_fields);
        echo json_encode($json_data);
    }
    function user_did($accountid) {
      
// 	echo $accountid;
	$acc_data = $this->session->userdata("accountinfo");
// 	echo "<pre>";print_r($acc_data);exit;
	if($acc_data['reseller_id'] != 0)
	{
	      $json_data = array();
	      $where = array('dids.accountid' => $accountid);
	      $jionCondition = 'dids.number = reseller_pricing.note AND dids.parent_id = reseller_pricing.reseller_id';
	      $count_all = $this->db_model->getJionQueryCount("dids", '*', $where,"reseller_pricing",$jionCondition,'inner');
	      $paging_data = $this->form->load_grid_config($count_all, $_GET['rp'], $_GET['page']);
	      $json_data = $paging_data["json_paging"];
// 	      $query = $this->db_model->getSelect("*", "dids", $where, "id", "ASC", $paging_data["paging"]["page_no"], $paging_data["paging"]["start"]);//echo*/ 
	      
	      //"<pre>";print_r($query);
	      
	      $query=$this->db_model->getJionQuery("dids", 'reseller_pricing.setup,reseller_pricing.cost,reseller_pricing.connectcost,dids.inc,reseller_pricing.includedseconds,reseller_pricing.monthlycost,dids.number,dids.id,dids.accountid,dids.extensions,dids.status,dids.provider_id,dids.allocation_bill_status,reseller_pricing.disconnectionfee,dids.dial_as,dids.call_type,dids.country_id', $where, "reseller_pricing", $jionCondition, 'inner',$paging_data["paging"]["page_no"], $paging_data["paging"]["start"],"dids.id", "ASC",  '');
// 	      exit;
	}else{
	      $json_data = array();
	      $where = array('accountid' => $accountid);
	      $count_all = $this->db_model->countQuery("*", "dids", $where);
	      $paging_data = $this->form->load_grid_config($count_all, $_GET['rp'], $_GET['page']);
	      $json_data = $paging_data["json_paging"];
	      $query = $this->db_model->getSelect("*", "dids", $where, "id", "ASC", $paging_data["paging"]["page_no"], $paging_data["paging"]["start"]);
        }
        
        $did_grid_fields = json_decode($this->did_form->build_did_list_for_user());
        $json_data['rows'] = $this->form->build_grid($query, $did_grid_fields);
        echo json_encode($json_data);
    }

    function did_delete_multiple() {
        $ids = $this->input->post("selected_ids", true);
        $where = "id IN ($ids)";
        $this->db->where($where);
        echo $this->db->delete("dids");
    }

    /**
     * -------Here we write code for controller did functions manage------
     * @action: Add, Edit, Delete, List DID
     * @id: DID number
     */
   function did_reseller_edit($action = false, $id = false) {
	$data['page_title'] = 'Edit Did ';
        if ($action == 'edit') {
	    
            if (($this->input->post())) {
                $post = $this->input->post();
//                 echo "<pre>";print_r($post);exit;
                unset($post['action']);
                $this->db->where(array('note' => $post['note'],"reseller_id"=>$this->session->userdata['accountinfo']['id']));
                $this->db->update("reseller_pricing",$post);
                
                $where_update_did = array('extensions'=>$post['extensions'],'call_type'=>$post['call_type']);
                $where= array('number'=>$post['note']);
                $this->db->where($where);
                $this->db->update("dids",$where_update_did);
                
		echo json_encode(array("SUCCESS"=> " DID Updated Successfully!!"));
                exit;
                
            } else {
		
                if ($this->session->userdata('logintype') == 1) {
// 		    echo "<pre>";print_r($this->session->userdata['accountinfo']);exit;
                    $accountinfo = $this->did_model->get_account($this->session->userdata['accountinfo']['number']);
// 		    $didinfo = $this->did_model->get_did_by_number($id);


		     $reseller_did = $this->db_model->getSelect("*", "reseller_pricing",array('id'=>$id));
		      $reseller_didinfo = $reseller_did->result_array();
		      $reseller_didinfo = $reseller_didinfo[0];

		      if(!empty($reseller_didinfo)){
		      $reseller_didinfo['setup'] = $this->common_model->to_calculate_currency($reseller_didinfo['setup'], '', '', true, false);
// 		      $reseller_didinfo['disconnectionfee'] = $this->common_model->to_calculate_currency($reseller_didinfo['disconnectionfee'], '', '', true, false);
		      $reseller_didinfo['monthlycost'] = $this->common_model->to_calculate_currency($reseller_didinfo['monthlycost'], '', '', true, false);
		      $reseller_didinfo['connectcost'] = $this->common_model->to_calculate_currency($reseller_didinfo['connectcost'], '', '', true, false);
		      $reseller_didinfo['cost'] = $this->common_model->to_calculate_currency($reseller_didinfo['cost'], '', '', true, false);
		       $data['did'] = $reseller_didinfo['note'];
		    }
                    $data['reseller_didinfo'] = $reseller_didinfo;


                    $data['accountinfo'] = $accountinfo;
//                     $data['didinfo'] = $didinfo;                    
//                     echo "<pre>";
//                     print_r($data);exit;
                    $this->load->view('view_did_manage_reseller_add', $data);
                }
            }
        }
        if ($action == 'delete') {
	    
	    
	    $reseller_did = $this->db_model->getSelect("*", "reseller_pricing",array('id'=>$id));
	    $reseller_did_data = $reseller_did->result_array();

	    $did_number['number'] =  $reseller_did_data[0]['note'];

            if ($did = $this->did_model->get_did_by_number($did_number['number'])) {
		
                 $response = $this->did_model->remove_did_pricing($did_number, $this->session->userdata['accountinfo']['id']);
                $this->session->set_flashdata('astpp_notification', 'DID Removed Successfully!');

                
                redirect(base_url() . 'did/did_list/');
            }else {
                $this->session->set_flashdata('astpp_notification', "Invalid DID Number...");
                redirect(base_url() . 'did/did_list/');
            }
        }
    }
    
    function did_reseller_purchase()
    {
	    if (($this->input->post())) {
                $post = $this->input->post();
//                  echo "<pre>";print_r($post);exit;	
	      if(isset($post['free_did_list']) && $post['free_did_list'] != '')
	      {
		// For deduction of admin price to reseller
                $didinfo = $this->did_model->get_did_by_number($post['free_did_list']);
                
                
                $where = array('id' => $this->session->userdata['accountinfo']['id']);
		$account = $this->db_model->getSelect("*", "accounts", $where);
                $response_data = $account->result_array();
                $available_bal = $this->db_model->get_available_bal($response_data[0]);
                         
	      if ($available_bal >= $didinfo["setup"]){ 
			      
			    $this->db_model->update_balance($didinfo["setup"],$response_data[0]["id"],"debit");
			    $this->add_invoice_data_user($response_data[0]["id"],"did_charge",'DID Purchase',$didinfo["setup"]);
			    $this->did_model->edit_did_reseller($didinfo['id'],$didinfo);
			    $this->db_model->update("dids", array("parent_id" => $response_data[0]["id"]), array("id" => $didinfo['id']));
			    $this->session->set_flashdata('astpp_errormsg', 'DID Purchased Successfully.');
			    redirect(base_url() . 'did/did_list/');
		    }else{
			$this->session->set_flashdata('astpp_notification', 'Insuffiecient fund to purchase this did');
			redirect(base_url() . 'did/did_list/');
			exit;
		    }
                }else{
			$this->session->set_flashdata('astpp_notification', 'Please Select DID.');
			redirect(base_url() . 'did/did_list/');
			exit;
                }
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
    
  function did_download_sample_file($file_name){
        $this->load->helper('download');
        $full_path = base_url()."assets/Rates_File/".$file_name.".csv";
        $file = file_get_contents($full_path);
        force_download("samplefile.csv", $file); 
    }
      function did_import() {
        $data['page_title'] = 'Import DIDs';
        $this->session->set_userdata('import_did_rate_csv',"");
        $error_data =  $this->session->userdata('import_did_csv_error');
        $full_path = $this->config->item('rates-file-path');
        if(file_exists($full_path.$error_data) && $error_data != ""){
            unlink($full_path.$error_data);
            $this->session->set_userdata('import_did_csv_error',"");
        }
        $this->load->view('view_import_did', $data);
    }
    function did_priview_file(){
        $data['page_title']='Import DIDs';
        $did_fields_array = $this->config->item('DID-rates-field');
        $check_header=$this->input->post('check_header',true);
        $invalid_flag=false;
        if (isset($_FILES['didimport']['name']) && $_FILES['didimport']['name'] != "") {
            list($txt, $ext) = explode(".", $_FILES['didimport']['name']);
            if($ext == "csv" && $_FILES["didimport"]['size'] > 0){ 
                $error = $_FILES['didimport']['error'];
                if ($error == 0 ) {
                    $uploadedFile = $_FILES["didimport"]["tmp_name"];
		    $full_path = $this->config->item('rates-file-path');
                    $actual_file_name = "ASTPP-DIDs-".date("Y-m-d H:i:s"). "." . $ext;
                    if (move_uploaded_file($uploadedFile,$full_path.$actual_file_name)) {
			$data['page_title']='Import DIDs Preview';
                        $data['csv_tmp_data'] = $this->csvreader->parse_file($full_path.$actual_file_name,$did_fields_array,$check_header);
                        $data['provider_id'] = $_POST['provider_id'];
                        $data['check_header']=$check_header;
                        $this->session->set_userdata('import_did_rate_csv',$actual_file_name);
                    }else{
                        $data['error'] = "File Uploading Fail Please Try Again";
                    }
                }
            }else {
                    $data['error'] = "Invalid file format : Only CSV file allows to import records(Can't import empty file)";
            }
        }else{
		$invalid_flag=true;
        }
        if ($invalid_flag) {
            $str = '';
            if (empty($_FILES['didimport']['name'])) {
                $str.= '<br/>Please Select  File.';
            }
            $data['error']=$str;
        }
        $this->load->view('view_import_did', $data);
    }
       function did_import_file($provider_id,$check_header=false) {
       $new_final_arr = array();
        $invalid_array = array();
        $new_final_arr_key = $this->config->item('DID-rates-field');
	$reseller_id=0;
        if ($this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 5) {
            $reseller_id = $this->session->userdata["accountinfo"]['id'];
        }
	$full_path = $this->config->item('rates-file-path');
        $did_file_name = $this->session->userdata('import_did_rate_csv');	
        $csv_tmp_data = $this->csvreader->parse_file($full_path.$did_file_name,$new_final_arr_key,$check_header); 
	$flag =false;
	$i=0;
	$number_arr=array();
        foreach ($csv_tmp_data as $key => $csv_data) {	
	  if(isset($csv_data['number']) && $csv_data['number']!= '' && $i != 0){
	    $str=null;
	    $csv_data['accountid']=isset($csv_data['accountid']) ? $csv_data['accountid'] :0;
	    $csv_data['call_type']=isset($csv_data['call_type']) && (strtolower($csv_data['call_type']) == 'local' ||strtolower($csv_data['call_type'])=='pstn' || strtolower($csv_data['call_type']) =='other' )? $this->common->get_custom_call_type(strtoupper($csv_data['call_type'])) :0;
	    $csv_data['extensions']=isset($csv_data['extensions']) ? $csv_data['extensions'] :'';
	    $csv_data['includedseconds']= isset($csv_data['includedseconds']) ? $csv_data['includedseconds'] :0;
	    $csv_data['cost']= !empty($csv_data['cost']) && is_numeric( $csv_data['cost']) ? $csv_data['cost'] :0;
	    $csv_data['monthlycost']= !empty($csv_data['monthlycost']) && is_numeric( $csv_data['monthlycost']) ? $csv_data['monthlycost'] :0;
	    $csv_data['inc']= isset($csv_data['inc']) ? $csv_data['inc'] :0;
//	    $csv_data['province']= isset($csv_data['province']) ? $csv_data['province'] :'';
	    $str=$this->data_validate($csv_data);
	    if($str != ""){
	      $invalid_array[$i]=$csv_data;
	      $invalid_array[$i]['error'] = $str;
	    }
	    else{
	     if(!in_array($csv_data['number'],$number_arr)){
	      $number_count=$this->db_model->countQuery('id','dids',array('number'=>$csv_data['number']));
	      if($number_count > 0){
		$invalid_array[$i]=$csv_data;
		$invalid_array[$i]['error'] ='Duplicate DID found from database';
	      }else{
		$csv_data['accountid']=$this->common->get_field_name('id', 'accounts',array('number'=>$csv_data['accountid']));
		$csv_data['country_id'] = $this->common->get_field_name('id', 'countrycode',array('country'=> ucfirst($csv_data['country_id'])));
		$csv_data['provider_id']=$provider_id;
//		$csv_data['province']=$csv_data['province']== '' ? 0 :$csv_data['province'];
		$new_final_arr[$i]=$csv_data;
	      }
	    }else{
		$invalid_array[$i]=$csv_data;
		$invalid_array[$i]['error'] = 'Duplicate DID found from import file';
	    }
	    }
	    $number_arr[]=$csv_data['number'];
	  }
          $i++;
        }
        //echo "<pre>";
        //print_R($invalid_array);exit;
	 if(!empty($new_final_arr)){
 	    $result = $this->did_model->bulk_insert_dids($new_final_arr);
         }

        
         //unlink($full_path.$did_file_name);
	 $count=count($invalid_array);
        if($count >0){
	    $session_id = "-1";
            $fp = fopen($full_path.$session_id.'.csv', 'w');
            foreach($new_final_arr_key as $key=>$value){
	      $custom_array[0][$key]=ucfirst($key);
            }
            $custom_array[0]['error']= "Error";
            $invalid_array =array_merge($custom_array,$invalid_array);
//             echo "<pre>";
//             print_r($invalid_array);
//             exit;
            foreach($invalid_array as $err_data){
                    fputcsv($fp,$err_data);
            }
            fclose($fp);
           $this->session->set_userdata('import_did_csv_error', $session_id.".csv");
           $data["error"] = $invalid_array;
           $data['provider_id'] = $provider_id;
           $data['impoted_count'] = count($new_final_arr);
           $data['failure_count'] = count($invalid_array)-1;
           $data['page_title'] = 'DID Import Error';	
           $this->load->view('view_import_error',$data);
         } else{
           $this->session->set_flashdata('astpp_errormsg', 'Total '.count($new_final_arr).' DIDs Imported Successfully!');
           redirect(base_url()."did/did_list/");
           }
    }
     function data_validate($csvdata){
          $str=null;
	  $alpha_regex = "/^[a-z ,.'-]+$/i";
	  $alpha_numeric_regex = "/^[a-z0-9 ,.'-]+$/i";
	  $email_regex = "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/"; 
	  $str.= $csvdata['number']!= '' ? null : 'Number,';
	  $str=rtrim($str,',');
	  if(!$str){
	      $str.= is_numeric($csvdata['number']) ? null : 'Number,';
// 	      $str.= preg_match( $alpha_numeric_regex, $csvdata['comment'] ) ? null :'Destination,';
	      $str.= !empty($csvdata['connectcost']) && is_numeric( $csvdata['connectcost']) ? null :( empty($csvdata['connectcost']) ? null : 'Connect Cost,');
	      $str.= !empty($csvdata['includedseconds']) && is_numeric( $csvdata['includedseconds']) ? null :( empty($csvdata['includedseconds']) ? null : 'Included Seconds,');
	      if($str){
		$str=rtrim($str,',');
		$error_field=explode(',',$str);
		$count = count($error_field);
		$str.= $count > 1 ? ' are not valid' : ' is not Valid';
		return $str;
	      }
	      else{
	      return false;
	      }
	  }
	  else{
	  $str=rtrim($str,',');
	    $error_field=explode(',',$str);
	    $count = count($error_field);
	    $str.= $count > 1 ? ' are required' : ' is Required';
      return $str;
    }
    }
    function did_error_download(){
        $this->load->helper('download');
        $error_data =  $this->session->userdata('import_did_csv_error');
        $full_path = $this->config->item('rates-file-path');
        $data = file_get_contents($full_path.$error_data);
        force_download("error_did_rates.csv", $data); 
    }
    function did_export_data_xls() {
    
        $query = $this->did_model->getdid_list(true, '0','10000000');
        $outbound_array = array();
        $outbound_array[] = array("DID", "Account",  "Calltype", "Destination","Country","Increments","Cost", "Setup fee","Monthly fee","Included seconds");
        if ($query->num_rows() > 0) {

            foreach ($query->result_array() as $row) {
                    $outbound_array[] = array(
                        $row['number'],
                        $this->common->get_field_name("number", "accounts", $row['accountid']),
                        $this->get_Calltype($row['call_type']),
                        $row['extensions'],
                        $this->common->get_field_name("country", "countrycode", $row['country_id']),
                        $row['inc'],
                        $row['cost'],
                        $this->common_model->calculate_currency($row['setup'],'','','',false),
			$this->common_model->calculate_currency($row['monthlycost'],'','','',false),
			$row['includedseconds']
		    );
                }
            }
        $this->load->helper('csv');
        array_to_csv($outbound_array, 'DIDs_' . date("Y-m-d") . '.csv');
    }
	function get_Calltype($type)
    {
	if($type == 0)
	{
	  return "PSTN";
	}elseif($type == 1)
	{
	  return "LOCAL";
	}else{
	  return "OTHER";
	}
    }
}

?>
 
