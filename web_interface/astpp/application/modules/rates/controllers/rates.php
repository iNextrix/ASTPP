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
class Rates extends MX_Controller {

    function Rates() {
        parent::__construct();

        $this->load->helper('template_inheritance');

        $this->load->library('session');
        $this->load->library('rates_form');
        $this->load->library('astpp/form');
        $this->load->model('rates_model');
        $this->load->library('csvreader');
        if ($this->session->userdata('user_login') == FALSE)
            redirect(base_url() . '/astpp/login');
    }

    function terminationrates_list() {
        $data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = 'Termination Rates';
	    $data['search_flag'] = true;
	    $data['batch_update_flag'] = true;
        $this->session->set_userdata('advance_search', 0);
        $data['grid_fields'] = $this->rates_form->build_terminationrates_for_admin();
        $data["grid_buttons"] = $this->rates_form->build_grid_buttons();
        $data['form_search'] = $this->form->build_serach_form($this->rates_form->get_termination_search_form());
        $data['form_batch_update'] = $this->form->build_batchupdate_form($this->rates_form->termination_batch_update_form());
        $this->load->view('view_terminationrates_list', $data);
    }
    /**
     * -------Here we write code for controller accounts functions account_list------
     * Listing of Accounts table data through php function json_encode
     */
    function terminationrates_list_json() {
        $json_data = array();
        $count_all = $this->rates_model->getoutbound_rates_list(false);
        $paging_data = $this->form->load_grid_config($count_all, $_GET['rp'], $_GET['page']);
        $json_data = $paging_data["json_paging"];

        $query = $this->rates_model->getoutbound_rates_list(true, $paging_data["paging"]["start"], $paging_data["paging"]["page_no"]);
        $grid_fields = json_decode($this->rates_form->build_terminationrates_for_admin());
        $json_data['rows'] = $this->form->build_grid($query, $grid_fields);

        echo json_encode($json_data);
    }

     function terminationrates_import() {
        $data['page_title'] = 'Import Termination Rates';
        $this->session->set_userdata('import_terminationrates_csv',"");
        $this->session->set_userdata('import_terminationrates_csv_error',"");
        $this->load->view('view_import_terminationrates', $data);
    }
    function terminationrates_preview_file(){
    	$invalid_flag= false;
	$check_header=$this->input->post('check_header',true);
	$data['page_title'] = 'Import Termination Rates';
        $new_final_arr_key = $this->config->item('Termination-rates-field');
        if(empty($_FILES) || !isset($_FILES)){
	  redirect(base_url()."rates/terminationrates_list/");
	}
        if (isset($_FILES['terminationimport']['name']) && $_FILES['terminationimport']['name'] != "" &&  isset($_POST['trunk_id']) && $_POST['trunk_id'] != '') {
            list($txt, $ext) = explode(".", $_FILES['terminationimport']['name']);
            if($ext == "csv" && $_FILES['terminationimport']['size'] > 0){ 
                $error = $_FILES['terminationimport']['error'];
                if ($error == 0 ) {
                    $uploadedFile = $_FILES["terminationimport"]["tmp_name"];
                    $csv_data=$this->csvreader->parse_file($uploadedFile,$new_final_arr_key,$check_header);
		    if(!empty($csv_data)){
			$full_path = $this->config->item('rates-file-path');
			$actual_file_name = "ASTPP-TERMINATION-RATES-".date("Y-m-d H:i:s"). "." . $ext;
			if (move_uploaded_file($uploadedFile,$full_path.$actual_file_name)) {
			  $data['csv_tmp_data'] = $csv_data;
			  $data['trunkid'] = $_POST['trunk_id'];
			  $data['check_header']=$check_header;
			  $data['page_title'] = 'Termination Rates Preview';
			  $this->session->set_userdata('import_terminationrates_csv',$actual_file_name);
			}else{
			  $data['error'] = "File Uploading Fail Please Try Again";
			}
                    }
                }
                else{
                    $data['error']=="File Uploading Fail Please Try Again";
                }
            }else {
                $data['error'] = "Invalid file format : Only CSV file allows to import records(Can't import empty file)";
            }
        }else{
		$invalid_flag=true;
        }
        if ($invalid_flag) {
            $str = '';
            if (!isset($_POST['trunk_id']) || empty($_POST['trunk_id'])) {
                $str.= '<br/>Please Create Trunk.';
            }
            if (empty($_FILES['terminationimport']['name'])) {
                $str.= '<br/>Please Select  File.';
            }
            $data['error']=$str;
        }
        $this->load->view('view_import_terminationrates', $data);
    }
    function terminationrates_rates_import($trunkID,$check_header=false) {
        $new_final_arr = array();
        $invalid_array = array();
        $new_final_arr_key = $this->config->item('Termination-rates-field');
	$screen_path = $this->config->item('screen_path');
        if ($this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 5) {
            $account_data = $this->session->userdata("accountinfo");
        }
	$full_path = $this->config->item('rates-file-path');
        $terminationrate_file_name = $this->session->userdata('import_terminationrates_csv');	
        $csv_tmp_data = $this->csvreader->parse_file($full_path.$terminationrate_file_name,$new_final_arr_key,$check_header); 
	$i=0;
        foreach ($csv_tmp_data as $key => $csv_data) {
	 if(isset($csv_data['pattern']) && $csv_data['pattern']!= '' && $i != 0){
	    $str=null;
	    $csv_data['prepend']= isset($csv_data['prepend'])? $csv_data['prepend'] :'';
	    $csv_data['comment']= isset($csv_data['comment'])? $csv_data['comment'] :'';
	    $csv_data['connectcost']= isset($csv_data['connectcost']) ? $csv_data['connectcost'] :0;
	    $csv_data['includedseconds']= isset($csv_data['includedseconds']) ? $csv_data['includedseconds'] :0;
	    $csv_data['cost']= !empty($csv_data['cost']) && is_numeric( $csv_data['cost']) ? $csv_data['cost'] :0;
	    $csv_data['inc']= isset($csv_data['inc']) ? $csv_data['inc'] :0;
	    $csv_data['precedence']= isset($csv_data['precedence']) ? $csv_data['precedence'] :'';
	    $csv_data['strip']= isset($csv_data['strip']) ? $csv_data['strip'] :'';
	    $str=$this->data_validate($csv_data);
	    if($str != ""){
	      $invalid_array[$i]=$csv_data;
	      $invalid_array[$i]['error'] = $str;
	    }
	    else{
	      $csv_data['trunk_id']=$trunkID;
	      $csv_data['pattern'] = "^" . $csv_data['pattern'] . ".*";
	      $new_final_arr[$i]=$csv_data;
	    }
	  }
          $i++;
        }
//                          echo "<pre>";
//          echo "Valid Array";
//          print_r($new_final_arr);
//          echo "Invalid Array";
//          print_r($invalid_array);exit;
//          echo "</pre>";
         if(!empty($new_final_arr)){
 	    $result = $this->rates_model->bulk_insert_terminationrates($new_final_arr);
         }
          unlink($full_path.$terminationrate_file_name);
	  $count=count($invalid_array);
        if($count >0){
            $session_id = "-1";
            $fp = fopen($full_path.$session_id.'.csv', 'w');
            foreach($new_final_arr_key as $key=>$value){
	      $custom_array[0][$key]=ucfirst($key);
            }
            $custom_array[0]['error']= "Error";
            $invalid_array =array_merge($custom_array,$invalid_array);
            foreach($invalid_array as $err_data){
                    fputcsv($fp,$err_data);
            }
           fclose($fp);
           $this->session->set_userdata('import_terminationrates_csv_error', $session_id.".csv");
           $data["error"] = $invalid_array;
           $data['trunkid'] = $trunkID;
           $data['impoted_count'] = count($new_final_arr);
           $data['failure_count'] = count($invalid_array)-1;
           $data['page_title'] = 'Termination Rates Import Error';	
           $this->load->view('view_import_error',$data);
         } else{
	  $this->session->set_flashdata('astpp_errormsg', 'Total '.count($new_final_arr).' Termination rates imported successfully!');
           redirect(base_url()."rates/terminationrates_list/");
         }        
    }
    function terminationrates_error_download(){
        $this->load->helper('download');
        $error_data =  $this->session->userdata('import_terminationrates_csv_error');
        $full_path = $this->config->item('rates-file-path');
        $data = file_get_contents($full_path.$error_data);
        force_download("Terminationrate_error.csv", $data); 

    }  
    function origination_import() {
        $data['page_title'] = 'Import Origination Rates';
        $this->session->set_userdata('import_originationrate_csv',"");
        $error_data =  $this->session->userdata('import_originationrate_csv_error');
        $full_path = $this->config->item('rates-file-path');
        if(file_exists($full_path.$error_data) && $error_data != ""){
            unlink($full_path.$error_data);
            $this->session->set_userdata('import_originationrate_csv_error',"");
        }
        $this->load->view('view_import_originationrates', $data);
    }
    function origination_preview_file(){
	$invalid_flag= false;
	$data=array();
	$data['page_title'] = 'Import Origination Rates';
	$check_header=$this->input->post('check_header',true);
	if(empty($_FILES) || !isset($_FILES)){
	  redirect(base_url()."rates/origination_list/");
	}
	$get_extension=strpos($_FILES['originationimport']['name'],'.');
	$new_final_arr_key = $this->config->item('Origination-rates-field');
	if(!$get_extension){
		$data['error']= "Please Upload File Atleast";
        }
        if (isset($_FILES['originationimport']['name']) && $_FILES['originationimport']['name'] != "" && isset($_POST['pricelist_id']) && $_POST['pricelist_id'] != '') {
            list($txt,$ext) = explode(".", $_FILES['originationimport']['name']);
            
            if($ext == "csv" && $_FILES['originationimport']['size'] > 0){ 
                $error = $_FILES['originationimport']['error'];
                if ($error == 0) {
                    $uploadedFile = $_FILES["originationimport"]["tmp_name"];
                    $csv_data=$this->csvreader->parse_file($uploadedFile,$new_final_arr_key,$check_header);
                    if(!empty($csv_data)){
		    $full_path = $this->config->item('rates-file-path');
                    $actual_file_name = "ASTPP-ORIGIN-RATES-".date("Y-m-d H:i:s"). "." . $ext;
                    if (move_uploaded_file($uploadedFile,$full_path.$actual_file_name)) {
			$flag=false;
			$data['trunkid']=isset($_POST['trunk_id']) && $_POST['trunk_id'] > 0 ? $_POST['trunk_id'] : 0;
                        $data['csv_tmp_data'] = $csv_data;
                        $data['pricelistid'] = $_POST['pricelist_id'];
                        $data['page_title'] = "Origination Rates Preview";
                        $data['check_header']=$check_header;
                        $this->session->set_userdata('import_originationrate_csv',$actual_file_name);
                    }else{
                        $data['error'] = "File Uploading Fail Please Try Again";
                    }
                }
             }   
            else{
                    $data['error']=="File Uploading Fail Please Try Again";
                }
           }
           else {
                $data['error'] = "Invalid file format : Only CSV file allows to import records(Can't import empty file)";
            }
            }else{
		$invalid_flag=true;
            }
        if ($invalid_flag) {
            $str = '';
            if (!isset($_POST['pricelist_id']) || empty($_POST['pricelist_id'])) {
                $str.= '<br/>Please Create Rate Group.';
            }
            if (empty($_FILES['originationimport']['name'])) {
                $str.= '<br/>Please Select File.';
            }
            $data['error']=$str;
//             $this->session->set_userdata('error', $str);
// 
//             redirect(base_url() . "rates/rates_import/");
//             $data['error'] = "Please upload file first";
        }
        $this->load->view('view_import_originationrates', $data);
    }
    function origination_import_file($pricelistID,$trunkid,$check_header=false) {
       $new_final_arr = array();
        $invalid_array = array();
        $new_final_arr_key = $this->config->item('Origination-rates-field');
	$screen_path = $this->config->item('screen_path');
	$reseller_id=0;
        if ($this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 5) {
            $reseller_id = $this->session->userdata["accountinfo"]['id'];
        }
	$full_path = $this->config->item('rates-file-path');
        $originationrate_file_name = $this->session->userdata('import_originationrate_csv');	
        $csv_tmp_data = $this->csvreader->parse_file($full_path.$originationrate_file_name,$new_final_arr_key,$check_header); 
	$i=0;
        foreach ($csv_tmp_data as $key => $csv_data) {	
	  if(isset($csv_data['pattern']) && $csv_data['pattern']!= '' && $i != 0){
	    $str=null;
	    $csv_data['comment']= isset($csv_data['comment'])? $csv_data['comment'] :'';
	    $csv_data['connectcost']= isset($csv_data['connectcost']) ? $csv_data['connectcost'] :0;
	    $csv_data['includedseconds']= isset($csv_data['includedseconds']) ? $csv_data['includedseconds'] :0;
	    $csv_data['cost']= !empty($csv_data['cost']) && is_numeric( $csv_data['cost']) ? $csv_data['cost'] :0;
	    $csv_data['inc']= isset($csv_data['inc']) ? $csv_data['inc'] :0;
	    $csv_data['precedence']= isset($csv_data['precedence']) ? $csv_data['precedence'] :'';
	    $str=$this->data_validate($csv_data);
	    if($str != ""){
	      $invalid_array[$i]=$csv_data;
	      $invalid_array[$i]['error'] = $str;
	    }
	    else{
	      $csv_data['pricelist_id']=$pricelistID;
	      $csv_data['trunk_id']=$trunkid;
	      $csv_data['pattern'] = "^" . $csv_data['pattern'] . ".*";
	      $csv_data['reseller_id']= $reseller_id;
	      $new_final_arr[$i]=$csv_data;
	    }
	  }
          $i++;
        }
//         echo "<pre>";
//         print_r($invalid_array);exit;
//         
          if(!empty($new_final_arr)){
  	    $result = $this->rates_model->bulk_insert_originationrates($new_final_arr);
          }
          unlink($full_path.$originationrate_file_name);
	 $count=count($invalid_array);
        if($count >0){
            $session_id = "-1";
            $fp = fopen($full_path.$session_id.'.csv', 'w');
            foreach($new_final_arr_key as $key=>$value){
	      $custom_array[0][$key]=ucfirst($key);
            }
            $custom_array[0]['error']= "Error";
            $invalid_array =array_merge($custom_array,$invalid_array);
            foreach($invalid_array as $err_data){
                    fputcsv($fp,$err_data);
            }
            fclose($fp);
           $this->session->set_userdata('import_originationrate_csv_error', $session_id.".csv");
           $data["error"] = $invalid_array;
           $data['pricelistid'] = $pricelistID;
           $data['impoted_count'] = count($new_final_arr);
           $data['failure_count'] = count($invalid_array)-1;
           $data['page_title'] = 'Origination Rates Import Error';	
           $this->load->view('view_import_error',$data);
         } else{
	   $this->session->set_flashdata('astpp_errormsg', 'Total '.count($new_final_arr).' Origination rates imported successfully!');
           redirect(base_url()."rates/origination_list/");
           }
    }
     function data_validate($csvdata){
          $str=null;
	  $alpha_regex = "/^[a-z ,.'-]+$/i";
	  $alpha_numeric_regex = "/^[a-z0-9 ,.'-]+$/i";
	  $email_regex = "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/"; 
	  $str.= $csvdata['pattern']!= '' ? null : 'Code,';
	  $str=rtrim($str,',');
	  if(!$str){
	      $str.= is_numeric($csvdata['pattern']) ? null : 'Code,';
//	      $str.= (isset($csvdata['prepend']) && !empty($csvdata['prepend'])) ? (is_numeric($csvdata['prepend']) ? null :'Prepend,') : null;
// 	      $str.= preg_match( $alpha_numeric_regex, $csvdata['comment'] ) ? null :'Destination,';
	      $str.= !empty($csvdata['connectcost']) && is_numeric( $csvdata['connectcost']) ? null :( empty($csvdata['connectcost']) ? null : 'Connect Cost,');
	      $str.= !empty($csvdata['includedseconds']) && is_numeric( $csvdata['includedseconds']) ? null :( empty($csvdata['includedseconds']) ? null : 'Included Seconds,');

	      $str.= !empty($csvdata['inc']) && is_numeric( $csvdata['inc']) ? null :( empty($csvdata['inc']) ? null : 'Increment,');
	      $str.= !empty($csvdata['precedence']) && is_numeric( $csvdata['precedence']) ? null :( empty($csvdata['precedence']) ? null : 'Precedence,');
	      $str.= (isset($csvdata['strip']) && !empty($csvdata['strip'])) ? (is_numeric($csvdata['strip']) ? null :'Strip,') : null;
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
    function origination_error_download(){
        $this->load->helper('download');
        $error_data =  $this->session->userdata('import_originationrate_csv_error');
        $full_path = $this->config->item('rates-file-path');
        $data = file_get_contents($full_path.$error_data);
        force_download("Originationrate_error.csv", $data); 
    }
    function origination_add($type = "") {
        $data['username'] = $this->session->userdata('user_name');
        $data['flag'] = 'create';
        $data['page_title'] = 'Create Origination Rate';
        $data['form'] = $this->form->build_form($this->rates_form->get_inbound_form_fields(), '');

        $this->load->view('view_originationrates_add_edit', $data);
    }

    function origination_edit($edit_id = '') {
        $data['page_title'] = 'Edit Origination Rate';
        if ($this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 5) {
            $account_data = $this->session->userdata("accountinfo");
            $reseller = $account_data['id'];
            $where = array('id' => $edit_id, "reseller_id" => $reseller);
        } else {
            $where = array('id' => $edit_id);
        }
        $account = $this->db_model->getSelect("*", "routes", $where);
        if ($account->num_rows > 0) {
            foreach ($account->result_array() as $key => $value) {
                $edit_data = $value;
            }
            $edit_data['connectcost'] = $this->common_model->to_calculate_currency($edit_data['connectcost'], '', '', false, false);
            $edit_data['cost'] = $this->common_model->to_calculate_currency($edit_data['cost'], '', '', false, false);
            $edit_data['pattern'] = filter_var($edit_data['pattern'], FILTER_SANITIZE_NUMBER_INT);

            $data['form'] = $this->form->build_form($this->rates_form->get_inbound_form_fields(), $edit_data);
            $this->load->view('view_originationrates_add_edit', $data);
        } else {
            redirect(base_url() . 'rates/origination_list/');
        }
    }

    function origination_save() {
        $add_array = $this->input->post();
        $data['form'] = $this->form->build_form($this->rates_form->get_inbound_form_fields(), $add_array);
        if ($add_array['id'] != '') {
            $data['page_title'] = 'Edit Origination Rate';
            if ($this->form_validation->run() == FALSE) {
                $data['validation_errors'] = validation_errors();
                echo $data['validation_errors'];
                exit;
            } else {
                $add_array['connectcost'] = $this->common_model->add_calculate_currency($add_array['connectcost'], '', '', false, false);
                $add_array['cost'] = $this->common_model->add_calculate_currency($add_array['cost'], '', '', false, false);
                $this->rates_model->edit_inbound($add_array, $add_array['id']);
                echo json_encode(array("SUCCESS"=> "Origination updated successfully!"));
                exit;
            }
        } else {
            $data['page_title'] = 'Create Origination Rate';
            if ($this->form_validation->run() == FALSE) {
                $data['validation_errors'] = validation_errors();
                echo $data['validation_errors'];
                exit;
            } else {

                $add_array['connectcost'] = $this->common_model->add_calculate_currency($add_array['connectcost'], '', '', false, false);
                $add_array['cost'] = $this->common_model->add_calculate_currency($add_array['cost'], '', '', false, false);
                $this->rates_model->add_inbound($add_array);
                echo json_encode(array("SUCCESS"=> "Origination added successfully!"));
                exit;
            }
        }
    }

    function origination_list_search() {
        $ajax_search = $this->input->post('ajax_search', 0);

        if ($this->input->post('advance_search', TRUE) == 1) {
            $this->session->set_userdata('advance_search', $this->input->post('advance_search'));
            $action = $this->input->post();
            unset($action['action']);
            unset($action['advance_search']);
            $this->session->set_userdata('inboundrates_list_search', $action);
        }
        if (@$ajax_search != 1) {
            redirect(base_url() . 'rates/origination_list/');
        }
    }

    function origination_list_clearsearchfilter() {
        $this->session->set_userdata('advance_search', 0);
        $this->session->set_userdata('account_search', "");
    }

    function terminationrates_delete($id) {
        $this->rates_model->remove_outbound($id);
        $this->session->set_flashdata('astpp_notification', 'Termination removed successfully!');
        redirect(base_url() . '/rates/terminationrates_list/');
    }

    function origination_delete($id) {
        $this->rates_model->remove_inbound($id);
        $this->session->set_flashdata('astpp_notification', 'Origination removed successfully!');
        redirect(base_url() . '/rates/origination_list/');
    }

    function origination_list() {
        $data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = 'Origination Rates';
	$data['search_flag'] = true;
	$data['batch_update_flag'] = true;
        $this->session->set_userdata('advance_search', 0);
        $data['grid_fields'] = $this->rates_form->build_inbound_list_for_admin();
        $data["grid_buttons"] = $this->rates_form->build_grid_buttons_inbound();
        $data['form_search'] = $this->form->build_serach_form($this->rates_form->get_inbound_search_form());
        $data['form_batch_update'] = $this->form->build_batchupdate_form($this->rates_form->inbound_batch_update_form());
        $this->load->view('view_inbound_rates_list', $data);
    }

    /**
     * -------Here we write code for controller accounts functions inboundrates------
     */
    function origination_list_json() {
        $json_data = array();
        $count_all = $this->rates_model->getinbound_rates_list(false);
        $paging_data = $this->form->load_grid_config($count_all, $_GET['rp'], $_GET['page']);
        $json_data = $paging_data["json_paging"];

        $query = $this->rates_model->getinbound_rates_list(true, $paging_data["paging"]["start"], $paging_data["paging"]["page_no"]);
        $grid_fields = json_decode($this->rates_form->build_inbound_list_for_admin());
        $json_data['rows'] = $this->form->build_grid($query, $grid_fields);

        echo json_encode($json_data);
    }

    function terminationrates_add($type = "") {
        $data['username'] = $this->session->userdata('user_name');
        $data['flag'] = 'create';
        $data['page_title'] = 'Termination Rate';
        $data['form'] = $this->form->build_form($this->rates_form->get_termination_form_fields(), '');

        $this->load->view('view_outboundrates_add_edit', $data);
    }

    function terminationrates_edit($edit_id = '') {
    
        $data['page_title'] = 'Termination Rates';
        $where = array('id' => $edit_id);
        $account = $this->db_model->getSelect("*", "outbound_routes", $where);
        foreach ($account->result_array() as $key => $value) {
            $edit_data = $value;
        }
	$edit_data['connectcost'] = $this->common_model->to_calculate_currency($edit_data['connectcost'], '', '', false, false);
	$edit_data['cost'] = $this->common_model->to_calculate_currency($edit_data['cost'], '', '', false, false);

        $edit_data['pattern'] = filter_var($edit_data['pattern'], FILTER_SANITIZE_NUMBER_INT);
        $data['form'] = $this->form->build_form($this->rates_form->get_termination_form_fields(), $edit_data);
        $this->load->view('view_outboundrates_add_edit', $data);
    }

    function terminationrates_save() {
        $add_array = $this->input->post();
        $data['form'] = $this->form->build_form($this->rates_form->get_termination_form_fields(), $add_array);
        if ($add_array['id'] != '') {
            $data['page_title'] = 'Edit Termination Rates';
            if ($this->form_validation->run() == FALSE) {
                $data['validation_errors'] = validation_errors();
                echo $data['validation_errors'];
                exit;
            } else {
                $add_array['connectcost'] = $this->common_model->add_calculate_currency($add_array['connectcost'], '', '', false, false);
                $add_array['cost'] = $this->common_model->add_calculate_currency($add_array['cost'], '', '', false, false);
                $this->rates_model->edit_outbound($add_array, $add_array['id']);
                echo json_encode(array("SUCCESS"=> "Termination updated successfully!"));
                exit;
            }
        } else {
            $data['page_title'] = 'Termination Details';
            if ($this->form_validation->run() == FALSE) {
                $data['validation_errors'] = validation_errors();
                echo $data['validation_errors'];
                exit;
            } else {

                $add_array['connectcost'] = $this->common_model->add_calculate_currency($add_array['connectcost'], '', '', false, false);
                $add_array['cost'] = $this->common_model->add_calculate_currency($add_array['cost'], '', '', false, false);
                $this->rates_model->add_outbound($add_array);
                echo json_encode(array("SUCCESS"=> "Termination added successfully!"));
                exit;
            }
        }
        $this->load->view('view_outboundrates_add_edit', $data);
    }

    function terminationrates_list_search() {
        $ajax_search = $this->input->post('ajax_search', 0);

        if ($this->input->post('advance_search', TRUE) == 1) {
            $this->session->set_userdata('advance_search', $this->input->post('advance_search'));
            $action = $this->input->post();
            unset($action['action']);
            unset($action['advance_search']);
            $this->session->set_userdata('terminationrates_list_search', $action);
        }
        if (@$ajax_search != 1) {
            redirect(base_url() . 'rates/terminationrates_list/');
        }
    }

    function terminationrates_list_clearsearchfilter() {
        $this->session->set_userdata('advance_search', 0);
        $this->session->set_userdata('account_search', "");
    }

    function customer_block_pattern_list($accountid) {
        $json_data = array();
        $where = array('accountid' => $accountid);

        $count_all = $this->db_model->countQuery("*", "block_patterns", $where);
        $paging_data = $this->form->load_grid_config($count_all, $_GET['rp'], $_GET['page']);
        $json_data = $paging_data["json_paging"];

        $pattern_data = $this->db_model->getSelect("*", "block_patterns", $where, "id", "ASC", $paging_data["paging"]["page_no"], $paging_data["paging"]["start"]);
        $grid_fields = json_decode($this->rates_form->build_pattern_list_for_customer($accountid));
        $json_data['rows'] = $this->form->build_grid($pattern_data, $grid_fields);

        echo json_encode($json_data);
    }

    function terminationrates_delete_multiple() {
        $ids = $this->input->post("selected_ids", true);
        $where = "id IN ($ids)";
        $this->db->where($where);
        echo $this->db->delete("outbound_routes");
    }

    function origination_delete_multiple() {
        $ids = $this->input->post("selected_ids", true);
        $where = "id IN ($ids)";
        $this->db->where($where);
        echo $this->db->delete("routes");
    }

    function user_inboundrates_list_json() {
        $json_data = array();
        $account_data = $this->session->userdata("accountinfo");
        $markup = $this->common->get_field_name('markup', 'pricelists', array('id'=>$account_data["pricelist_id"]));
        $markup = ($markup > 0)?$markup:1;

        $count_all = $this->rates_model->getinbound_rates_list_for_user(false);
        $paging_data = $this->form->load_grid_config($count_all, $_GET['rp'], $_GET['page']);
        $json_data = $paging_data["json_paging"];

        $query = $this->rates_model->getinbound_rates_list_for_user(true, $paging_data["paging"]["start"], $paging_data["paging"]["page_no"]);
        $grid_fields = json_decode($this->rates_form->build_inbound_list_for_user());
        foreach ($query->result_array() as $key => $value) {
            $json_data['rows'][] = array('cell' => array(
                    $this->common->get_only_numeric_val("","",$value["pattern"]),
                    $value['comment'],
                    $value['inc'],
                    $this->common_model->calculate_currency(($value['cost'] + ($value['cost']*$markup)/100),'','','',true),
                    $this->common_model->calculate_currency($value['connectcost'],'','','',true),
                    $value['includedseconds']                    
            ));
        }
//        $json_data['rows'] = $this->form->build_grid($query, $grid_fields);
        echo json_encode($json_data);
    }

    function user_inboundrates_list_search() {
        $ajax_search = $this->input->post('ajax_search', 0);

        if ($this->input->post('advance_search', TRUE) == 1) {
            $this->session->set_userdata('advance_search', $this->input->post('advance_search'));
            $action = $this->input->post();
            unset($action['action']);
            unset($action['advance_search']);
            $this->session->set_userdata('inboundrates_list_search', $action);
        }
        if (@$ajax_search != 1) {
            redirect(base_url() . 'user/user_rates_list/');
        }
    }

    function user_inboundrates_list_clearsearchfilter() {
        $this->session->set_userdata('advance_search', 0);
        $this->session->set_userdata('account_search', "");
    }
    function customer_rates_download_sample_file($file_name){
        $this->load->helper('download');
	$full_path = base_url()."assets/Rates_File/".$file_name.".csv";
//         $full_path = "var/www/html/celero_new/assets/Rates_File/".$file_name.".csv";
        $file = file_get_contents($full_path);
        force_download("samplefile.csv", $file); 
    }
    function terminationrates_batch_update(){
        $batch_update_arr = $this->input->post();
	$batch_update_arr["cost"]["cost"] = isset($batch_update_arr["cost"]["cost"])?$this->common_model->add_calculate_currency($batch_update_arr["cost"]["cost"], '', '', true, false):"0.0000";
	$batch_update_arr["connectcost"]["connectcost"] = isset($batch_update_arr["connectcost"]["connectcost"])?$this->common_model->add_calculate_currency($batch_update_arr["connectcost"]["connectcost"], '', '', true, false):"0.0000";
//        $batch_update_arr = array("inc"=> array("inc"=>"1","operator"=>"3"),"cost"=> array("cost"=>"1","operator"=>"4"));
        $result = $this->rates_model->termination_rates_batch_update($batch_update_arr);
        echo json_encode(array("SUCCESS"=> "Termination rates batch updated successfully!"));
        exit;
    }
    
    function origination_batch_update(){
        $batch_update_arr = $this->input->post();
	     $batch_update_arr["cost"]["cost"] = isset($batch_update_arr["cost"]["cost"])?$this->common_model->add_calculate_currency($batch_update_arr["cost"]["cost"], '', '', true, false):"0.0000";
//        $batch_update_arr = array("inc"=> array("inc"=>"1","operator"=>"3"),"cost"=> array("cost"=>"1","operator"=>"4"));
        $result = $this->rates_model->inboundrates_rates_batch_update($batch_update_arr);
        echo json_encode(array("SUCCESS"=> "Origination rates batch updated successfully!"));
        exit;
    }

    function terminationrates_export_cdr_xls() {
        $query = $this->rates_model->getoutboundrates(true, '', '', false);
        $outbound_array = array();
        $outbound_array[] = array("Code", "Destination",  "Connect Cost", "Included Seconds","Per Minute Cost", "Increment","Precedence","Strip","Prepend");
        if ($query->num_rows() > 0) {

            foreach ($query->result_array() as $row) {
                    $outbound_array[] = array(
                        $row['pattern']=$this->common->get_only_numeric_val("","",$row["pattern"]),
                        $row['comment'],
                        $this->common_model->calculate_currency($row['connectcost'],'','','',false),
                        
                        $row['includedseconds'],
			$this->common_model->calculate_currency($row['cost'],'','','',false),
                        $row['inc'],
                        $row['precedence'],
                        $row['strip'],
                        $row['prepend']
                        );
                }
            }
        $this->load->helper('csv');
        array_to_csv($outbound_array, 'Termination_Rates_' . date("Y-m-d") . '.csv');
    }

    function terminationrates_export_cdr_pdf() {
        $query = $this->rates_model->getoutboundrates(true, '', '', false);
        $outbound_array = array();
        $this->load->library('fpdf');
        $this->load->library('pdf');
        $this->fpdf = new PDF('P', 'pt');
        $this->fpdf->initialize('P', 'mm', 'A4');
        $this->fpdf->tablewidths = array(20, 30, 20, 20, 20, 20, 20,20,20);
        $outbound_array[] = array("Code", "Destination",  "Connect Cost","Included Seconds","Per Minute Cost", "Increment","Precedence","Prepend","Strip");
        if ($query->num_rows() > 0) {

            foreach ($query->result_array() as $row) {
                    $outbound_array[] = array(
			$row['pattern']=$this->common->get_only_numeric_val("","",$row["pattern"]),
                        $row['comment'],
                        $row['connectcost'],
                        $row['includedseconds'],
			$this->common_model->calculate_currency($row['cost']),
                        $row['inc'],
                        $row['precedence'],
                        $row['prepend'],
                        $row['strip']
                    );
                }
        }
        $this->fpdf->AliasNbPages();
        $this->fpdf->AddPage();

        $this->fpdf->SetFont('Arial', '', 15);
        $this->fpdf->SetXY(60, 5);
        $this->fpdf->Cell(100, 10, "Outbound Rates Report " . date('Y-m-d'));

        $this->fpdf->SetY(20);
        $this->fpdf->SetFont('Arial', '', 7);
        $this->fpdf->SetFillColor(255, 255, 255);
        $this->fpdf->lMargin = 2;

        $dimensions = $this->fpdf->export_pdf($outbound_array, "7");
        $this->fpdf->Output('Termination_Rates_' . date("Y-m-d") . '.pdf', "D");
    }
    

    function origination_export_cdr_xls() {
        $query = $this->rates_model->getinboundrates(true, '', '', false);
	//echo "<pre>";print_r($query);exit;
        $inbound_array = array();
        $inbound_array[] = array("Code", "Destination","Connect Cost","Included Seconds","Per Minute Cost",  "Increment","Precedence");
        if ($query->num_rows() > 0) {

            foreach ($query->result_array() as $row) {
                    $inbound_array[] = array(
                        $row['pattern']=$this->common->get_only_numeric_val("","",$row["pattern"]),
                        $row['comment'],
                        $row['connectcost'],
                        $row['includedseconds'],
                        $this->common_model->calculate_currency($row['cost'],'','','',false),
                        $row['inc'],
                        $row['precedence']
                    );
                }
            }
        $this->load->helper('csv');
        array_to_csv($inbound_array, 'Origination_Rates_' . date("Y-m-d") . '.csv');
    }

    function origination_export_cdr_pdf() {
        $query = $this->rates_model->getinboundrates(true, '', '', false);
	
        $inbound_array = array();
        $this->load->library('fpdf');
        $this->load->library('pdf');
        $this->fpdf = new PDF('P', 'pt');
        $this->fpdf->initialize('P', 'mm', 'A4');
        $this->fpdf->tablewidths = array(20, 20, 20, 20, 20, 20);
	$inbound_array[] = array("Code", "Destination","Connect Cost","Included Seconds","Per Minute Cost","Increment");
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                    $inbound_array[] = array(
                       $row['pattern']=$this->common->get_only_numeric_val("","",$row["pattern"]),
                        $row['comment'],
                        $row['connectcost'],
                        $row['includedseconds'],
                        $this->common_model->calculate_currency($row['cost'],'','','',false),
                        $row['inc']
                    );
                }
        }
        $this->fpdf->AliasNbPages();
        $this->fpdf->AddPage();

        $this->fpdf->SetFont('Arial', '', 15);
        $this->fpdf->SetXY(60, 5);
        $this->fpdf->Cell(100, 10, "Inbound Rates Report " . date('Y-m-d'));

        $this->fpdf->SetY(20);
        $this->fpdf->SetFont('Arial', '', 7);
        $this->fpdf->SetFillColor(255, 255, 255);
        $this->fpdf->lMargin = 2;

        $dimensions = $this->fpdf->export_pdf($inbound_array, "5");
        $this->fpdf->Output('Origination_Rates_' . date("Y-m-d") . '.pdf', "D");
    }

    function user_inboundrates_cdr_xls() {
        $query = $this->rates_model->getinbound_rates_for_user(true, '', '', false);
        $inbound_array = array();
        $inbound_array[] = array("Code", "Destination", "Increment","Cost Per Minutes",  "Connect Charge", "Included Seconds");
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                
                    $inbound_array[] = array(
                        $row['pattern']=$this->common->get_only_numeric_val("","",$row["pattern"]),
                        $row['comment'],
                        $row['inc'],
			$this->common_model->calculate_currency($row['cost'],'','','',false),
                        $row['connectcost'],
                        $row['includedseconds']
                    );
            }
        }
        $this->load->helper('csv');
        array_to_csv($inbound_array, 'Rates_' . date("Y-m-d") . '.csv');
    }  
  
    function user_inboundrates_cdr_pdf() {
        $query = $this->rates_model->getinbound_rates_for_user(true, '', '', false);
        $inbound_array = array();
        $this->load->library('fpdf');
        $this->load->library('pdf');
        $this->fpdf = new PDF('P', 'pt');
        $this->fpdf->initialize('P', 'mm', 'A4');
	$this->fpdf->tablewidths = array(20, 20, 20, 20, 20, 20);
        $inbound_array[] = array("Code", "Destination", "Increment","Cost Per Minutes",  "Connect Charge", "Included Seconds");
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                    $inbound_array[] = array(
                       $row['pattern']=$this->common->get_only_numeric_val("","",$row["pattern"]),
                        $row['comment'],
                        $row['inc'],
			$this->common_model->calculate_currency($row['cost'],'','','',false),
                        $row['connectcost'],
                        $row['includedseconds']
                    );
            }
        }

        $this->fpdf->AliasNbPages();
        $this->fpdf->AddPage();

        $this->fpdf->SetFont('Arial', '', 15);
        $this->fpdf->SetXY(60, 5);
        $this->fpdf->Cell(100, 10, "Rates Report " . date('Y-m-d'));

        $this->fpdf->SetY(20);
        $this->fpdf->SetFont('Arial', '', 7);
        $this->fpdf->SetFillColor(255, 255, 255);
        $this->fpdf->lMargin = 2;

        $dimensions = $this->fpdf->export_pdf($inbound_array, "5");
        $this->fpdf->Output('Rates_' . date("Y-m-d") . '.pdf', "D");
    }
    function resellersrates_list(){
	$accountinfo=$this->session->userdata('accountinfo');
	$data['username'] = $this->session->userdata('user_name');
        $data['page_title'] = 'My Rates' ;
        $data['search_flag'] = true;
        $this->session->set_userdata('advance_search', 0);
        $data['grid_fields'] = $this->rates_form->build_rates_list_for_reseller();
	$data["grid_buttons"] = $this->rates_form->build_grid_buttons_rates();
        $data['form_search'] = $this->form->build_serach_form($this->rates_form->get_reseller_inbound_search_form());
        $this->load->view('view_resellersrates_list', $data);
    }
    function resellersrates_list_json() {
        $json_data = array();
        $account_data = $this->session->userdata("accountinfo");
        $markup = $this->common->get_field_name('markup', 'pricelists', array('id'=>$account_data["pricelist_id"]));
        $markup = ($markup > 0)?$markup:1;
        $count_all = $this->rates_model->getreseller_rates_list(false);
        $paging_data = $this->form->load_grid_config($count_all, $_GET['rp'], $_GET['page']);
        $json_data = $paging_data["json_paging"];
        $query = $this->rates_model->getreseller_rates_list(true, $paging_data["paging"]["start"], $paging_data["paging"]["page_no"]);
        $grid_fields = json_decode($this->rates_form->build_rates_list_for_reseller());
        foreach ($query->result_array() as $key => $value) {
            $json_data['rows'][] = array('cell' => array(
                    $this->common->get_only_numeric_val("","",$value["pattern"]),
                    $value['comment'],
                    $this->common_model->calculate_currency($value['connectcost'],'','','',true),
                    $value['includedseconds'],
                    $this->common_model->calculate_currency(($value['cost'] + ($value['cost']*$markup)/100),'','','',true),
                    $value['inc'],
                    $value['precedence'],
            ));
        }
//        $json_data['rows'] = $this->form->build_grid($query, $grid_fields);
        echo json_encode($json_data);
    }
    function resellersrates_list_search() {
        $ajax_search = $this->input->post('ajax_search', 0);

        if ($this->input->post('advance_search', TRUE) == 1) {
            $this->session->set_userdata('advance_search', $this->input->post('advance_search'));
            $action = $this->input->post();
            
            unset($action['action']);
            unset($action['advance_search']);
            $this->session->set_userdata('resellerrates_list_search', $action);
        }
        if (@$ajax_search != 1) {
            redirect(base_url() . 'rates/resellersrates_list/');
        }
    }
    function resellersrates_list_clearsearchfilter() {
        $this->session->set_userdata('advance_search', 0);
        $this->session->set_userdata('resellerrates_list_search', "");
    }
    function resellersrates_xls()
    {
	$query = $this->rates_model->getreseller_rates_list(true,'0','0','1');
	$customer_array = array();

	$customer_array[] = array("Code", "Destination","Connect Cost","Included Seconds","Per Minute Cost","Increment","Precedence");


	if ($query->num_rows() > 0) {

            foreach ($query->result_array() as $row) {
                
                    $customer_array[] = array(
                        $row['pattern']=$this->common->get_only_numeric_val("","",$row["pattern"]),
                        $row['comment'],
                        $row['connectcost'],
			$row['includedseconds'],
			$this->common_model->calculate_currency($row['cost']),
                        $row['inc'],
                        $row['precedence']
                    );
                
            }
        }
        $this->load->helper('csv');
        array_to_csv($customer_array, 'My_Own_Rate_' . date("Y-m-d") . '.csv');
        exit;
    }
}
?>
 
