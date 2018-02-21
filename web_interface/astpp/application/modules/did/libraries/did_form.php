<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class did_form {

    function __construct() {
        $this->CI = & get_instance();
    }

    function get_dids_form_fields($id=false,$parent_id='0',$account_id='0') {
    if ($id != 0){

if($parent_id > 0){
               $account_dropdown =  array('Reseller',  array('name' => 'parent_id', 'disabled' => 'disabled','class' => 'accountid', 'id' => 'accountid'), 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'first_name,last_name,number', 'accounts', 'build_concat_dropdown', 'where_arr', array("reseller_id" => "0","type"=>"1", "deleted" => "0","status" => "0"));                
            }else{
		if($account_id > 0){
              $account_dropdown =  array('Account ',  array('name' => 'accountid', 'disabled' => 'disabled','class' => 'accountid', 'id' => 'accountid'), 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'first_name,last_name,number', 'accounts', 'build_concat_dropdown', 'where_arr', array("reseller_id" => "0","type"=>"0", "deleted" => "0","status" => "0"));
		}
		else{
		$account_dropdown = array('Account', 'accountid', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id',  'first_name,last_name,number', 'accounts', 'build_concat_dropdown', 'where_arr', array("reseller_id" => "0", "type" => "0", "deleted" => "0","status" => "0" ));
		}
            }

        } else{
	$account_dropdown = array('Account', 'accountid', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id',  'first_name,last_name,number', 'accounts', 'build_concat_dropdown', 'where_arr', array("reseller_id" => "0", "type" => "0", "deleted" => "0","status" => "0" ));
            }

	$val= $id > 0 ? 'dids.number.'.$id : 'dids.number';
        $form['forms'] = array(base_url() . '/did/did_save/', array('id' => 'did_form', 'method' => 'POST', 'name' => 'did_form'));
        $form['DID Information'] = array(
            array('', 'HIDDEN', array('name' => 'id'), '', '', '', ''),
            array('DID', 'INPUT', array('name' => 'number', 'size' => '20', 'maxlength' => '40', 'class' => "text field medium"), 'trim|required|is_numeric|xss_clean|integer', 'tOOL TIP', 'Please Enter account number'),
              array('Country',array('name'=>'country_id','class'=>'country_id'), 'SELECT', '',array("name"=>"country_id","rules"=>"required"), 'tOOL TIP', 'Please Enter account number', 'id', 'country', 'countrycode', 'build_dropdown', '', ''),
            array('City', 'INPUT', array('name' => 'city', 'size' => '20', 'maxlength' => '20', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter Password'),
            array('Province', 'INPUT', array('name' => 'province', 'size' => '15', 'maxlength' => '20', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter Password'),
//            array('Provider', 'provider_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'number', 'accounts', 'build_dropdown', 'type', '3'),
array('Provider', 'provider_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'first_name,last_name,number', 'accounts', 'build_concat_dropdown', 'where_arr', array("type" => "3", "deleted" => "0","status" => "0")),
        );

        $form['DID Billing'] = array(
	   $account_dropdown,           
           // array('Account', 'accountid', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'first_name,last_name,number', 'accounts', 'build_concat_dropdown', 'where_arr', array("reseller_id" => "0", "type" => "0", "deleted" => "0","status" => "0")),
            array('Increments', 'INPUT', array('name' => 'inc', 'size' => '20', 'maxlength' => '4', 'class' => "text field medium"), 'trim|is_numeric|xss_clean', 'tOOL TIP', 'Please Enter Password'),
            array('Cost', 'INPUT', array('name' => 'cost', 'size' => '15', 'maxlength' => '100', 'class' => "text field medium"), 'trim|is_numeric|xss_clean', 'tOOL TIP', 'Please Enter Password'),
            array('Included Seconds', 'INPUT', array('name' => 'includedseconds', 'size' => '50', 'maxlength' => '11', 'class' => "text field medium"), 'trim|is_numeric|xss_clean', 'tOOL TIP', 'Please Enter Password'),
            array('Setup Fee', 'INPUT', array('name' => 'setup', 'maxlength' => '15', 'size' => '15', 'class' => 'text field medium'), 'trim|is_numeric|xss_clean', 'tOOL TIP', ''),
            array('Monthly<br>Fee', 'INPUT', array('name' => 'monthlycost', 'size' => '15', 'maxlength' => '20', 'class' => "text field medium"), 'trim|is_numeric|xss_clean', 'tOOL TIP', 'Please Enter Password'),
            array('Connection Fee', 'INPUT', array('name' => 'connectcost', 'size' => '15', 'maxlength' => '20', 'class' => "text field medium"), 'trim|is_numeric|xss_clean', 'tOOL TIP', 'Please Enter Password'),
//             array('Disconnection Fee', 'INPUT', array('name' => 'disconnectionfee', 'id' => 'first_name', 'size' => '15', 'maxlength' => '15', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter account number'),
//             array('Limit Length', 'limittime', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', '', '', '', 'set_allow', ''),
//             array('Bill on Allocation', 'allocation_bill_status', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', '', '', '', 'set_allow', ''),
        );

        $form['DID Setting'] = array(
            array('Call Type', 'call_type', 'SELECT', '', '', 'tOOL TIP', '', '', '', '', 'set_call_type', ''),
            array('Destination', 'INPUT', array('name' => 'extensions', 'size' => '20', 'maxlength' => '180', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter Password'),
            array('Max Channels', 'INPUT', array('name' => 'maxchannels', 'size' => '20', 'maxlength' => '4', 'class' => "text field medium"), 'trim|is_numeric|xss_clean', 'tOOL TIP', 'Please Enter account number'),
            array('Status', 'status', 'SELECT', '', '', 'tOOL TIP', 'Please Select Status', '', '', '', 'set_status'),

        );

        $form['button_save'] = array('name' => 'action', 'content' => 'Save', 'value' => 'save', 'id' => 'submit', 'type' => 'button', 'class' => 'btn btn-line-parrot');
        $form['button_cancel'] = array('name' => 'action', 'content' => 'Cancel', 'value' => 'cancel', 'type' => 'button', 'class' => 'btn btn-line-sky margin-x-10', 'onclick' => 'return redirect_page(\'NULL\')');
        return $form;
    }

    function get_search_did_form() {

    
        $form['forms'] = array("", array('id' => "did_search"));
        $form['Search'] = array(
            array('DID', 'INPUT', array('name' => 'number[number]', '', 'size' => '20', 'class' => "text field"), '', 'tOOL TIP', '1', 'number[number-string]', '', '', '', 'search_string_type', ''),
         array('Account', 'accountid', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'first_name,last_name,number', 'accounts', 'build_concat_dropdown', 'where_arr', array("reseller_id" => "0","type"=>"0", "deleted" => "0")),
	   array('Call Type', 'call_type', 'SELECT', '', '', 'tOOL TIP', '', '', '', '', 'set_call_type', '',''),
              array('Destination', 'INPUT', array('name' => 'extensions[extensions]', '', 'size' => '20', 'class' => "text field"), '', 'tOOL TIP', '1', 'extensions[extensions-string]', '', '', '', 'search_string_type', ''),
	array('Country', 'country_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'country', 'countrycode', 'build_dropdown', '', ''),
          //  array('Provider', 'provider_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'number', 'accounts', 'build_dropdown', 'type', '3'),
            
		array('Status', 'status', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', '', '', '', 'set_search_status', '', ''),
            
		array('', 'HIDDEN', 'ajax_search', '1', '', '', ''),array('', 'HIDDEN', 'advance_search', '1', '', '', '')
          );

        $form['button_search'] = array('name' => 'action', 'id' => "did_search_btn", 'content' => 'Search', 'value' => 'save', 'type' => 'button', 'class' => 'btn btn-line-parrot pull-right');
        $form['button_reset'] = array('name' => 'action', 'id' => "id_reset", 'content' => 'Clear', 'value' => 'cancel', 'type' => 'reset', 'class' => 'btn btn-line-sky pull-right margin-x-10');

        return $form;
    }

        function get_search_did_form_for_reseller() {

    
        $form['forms'] = array("", array('id' => "did_search"));
        $form['Search'] = array(
            array('DID', 'INPUT', array('name' => 'note[note]', '', 'size' => '20', 'class' => "text field"), '', 'tOOL TIP', '1', 'note[note-string]', '', '', '', 'search_string_type', ''),
// 	  array('Account', 'accountid', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'first_name,last_name,number', 'accounts', 'build_concat_dropdown', 'where_arr', array("reseller_id" => "0","type"=>"0", "deleted" => "0")),
	   array('Call Type', 'call_type', 'SELECT', '', '', 'tOOL TIP', '', '', '', '', 'set_call_type', '',''),
	  array('Destination', 'INPUT', array('name' => 'extensions[extensions]', '', 'size' => '20', 'class' => "text field"), '', 'tOOL TIP', '1', 'extensions[extensions-string]', '', '', '', 'search_string_type', ''),
	  array('Status', 'status', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', '', '', '', 'set_search_status', '', ''),
	  array('', 'HIDDEN', 'ajax_search', '1', '', '', ''),array('', 'HIDDEN', 'advance_search', '1', '', '', '')
          );

        $form['button_search'] = array('name' => 'action', 'id' => "did_search_btn", 'content' => 'Search', 'value' => 'save', 'type' => 'button', 'class' => 'btn btn-line-parrot pull-right');
        $form['button_reset'] = array('name' => 'action', 'id' => "id_reset", 'content' => 'Clear', 'value' => 'cancel', 'type' => 'reset', 'class' => 'btn btn-line-sky pull-right margin-x-10');

        return $form;
    }


    function build_did_list_for_admin() {
       // array(display name, width, db_field_parent_table,feidname, db_field_child_table,function name);
        $grid_field_arr = json_encode(array(array("<input type='checkbox' name='chkAll' class='ace checkall'/><label class='lbl'></label>", "30", "", "", "", ""),
            array("DID", "100", "number", "", "", ""),
//             array("Account Number", "100", "accountid", "number", "accounts", "get_field_name"),
            array("Account", "95", "accountid", "first_name,last_name,number", "accounts", "get_field_name_coma_new"),
            
            array("Call Type", "70", "call_type", "call_type", "call_type", "get_call_type"),
            array("Destination", "115", "extensions", "", "", ""),
             array("Country", "95", "country_id", "country", "countrycode", "get_field_name"),
            array("Increments", "85", "inc", "", "", ""),
            array("Cost", "90", "cost", "cost", "cost", "convert_to_currency"),
       //     array("Included <br>Seconds", "65", "includedseconds", "", "", ""),
            array("Setup <br>Fee", "90", "setup", "setup", "setup", "convert_to_currency"),
            array("Monthly<br>Fee", "90", "monthlycost", "monthlycost", "monthlycost", "convert_to_currency"),
            array("Included<br/>Seconds", "92", "includedseconds", "", "", ""),
            array("Status", "70", "status", "status", "status", "get_status"),
            array("Is purchased?", "135", "number", "number", "number", "check_did_avl"),
	    array("Action", "90", "", "", "", array("EDIT" => array("url" => "did/did_edit/", "mode" => "popup"),
                    "DELETE" => array("url" => "did/did_remove/", "mode" => "single")))
                ));
        return $grid_field_arr;
    }
        function build_did_list_for_reseller_login() {
        // array(display name, width, db_field_parent_table,feidname, db_field_child_table,function name);
        $grid_field_arr = json_encode(array(
            array("DID", "130", "number", "", "", ""),
            array("Account", "130", "accountid", "first_name,last_name,number", "accounts", "get_field_name_coma_new"),
            array("Is purchased?", "120", "number", "number", "number", "check_did_avl_reseller"),
            array("Call Type", "110", "call_type", "call_type", "call_type", "get_call_type"),
            array("Destination", "115", "extensions", "", "", ""),
          //  array("Country", "105", "country_id", "country", "countrycode", "get_field_name"),
            array("Increments", "120", "inc", "", "", ""),
            array("Cost", "120", "cost", "cost", "cost", "convert_to_currency"),
       //     array("Included <br>Seconds", "65", "includedseconds", "", "", ""),
            array("Setup <br> Fee", "110", "setup", "setup", "setup", "convert_to_currency"),
            array("Monthly<br> fee", "110", "monthlycost", "monthlycost", "monthlycost", "convert_to_currency"),
            array("Status", "90", "status", "status", "status", "get_status"),
	    array("Action", "90", "", "", "", array("EDIT" => array("url" => "did/did_reseller_edit/edit/", "mode" => "popup"),
                    "DELETE" => array("url" => "did/did_reseller_edit/delete/", "mode" => "single")))
                ));
        return $grid_field_arr;
    }
    function build_grid_buttons() {
        $buttons_json = json_encode(array(
            array("Create","btn btn-line-warning btn","fa fa-plus-circle fa-lg", "button_action", "/did/did_add/", "popup"),
            array("Delete", "btn btn-line-danger","fa fa-times-circle fa-lg", "button_action", "/did/did_delete_multiple/"),
            array("Import", "btn btn-line-blue","fa fa-upload fa-lg", "button_action", "/did/did_import/", ''),            
	   array("Export","btn btn-xing" ,"fa fa-download fa-lg", "button_action", "/did/did_export_data_xls", 'single')
            ));
        return $buttons_json;
    }

    function build_did_list_for_customer($accountid, $accounttype) {
        // array(display name, width, db_field_parent_table,feidname, db_field_child_table,function name);
        $grid_field_arr = json_encode(array(
	    array("DID Number", "140", "number", "", "", ""),
            array("Country", "100", "country_id", "country", "countrycode", "get_field_name"),
            array("Increments", "100", "inc", "", "", ""),
            array("Cost", "100", "cost", "cost", "cost", "convert_to_currency"),
            array("Included<br> Seconds", "130", "includedseconds", "", "", ""),
            array("Setup <br> Fee", "120", "setup", "setup", "setup", "convert_to_currency"),
            array("Monthly<br> Fee", "140", "monthlycost", "monthlycost", "monthlycost", "convert_to_currency"),
            array("Connection Fee", "139", "connectcost", "connectcost", "connectcost", "convert_to_currency"),
//             array("Disconnection <br> Fee", "140", "disconnectionfee", "disconnectionfee", "disconnectionfee", "convert_to_currency"),
            array("Action", "200", "", "", "", array("DELETE" => array("url" => "/accounts/customer_dids_action/delete/$accountid/$accounttype/", "mode" => "single")))
                ));
        return $grid_field_arr;
    }

    function build_did_list_for_reseller($accountid, $accounttype) {
        // array(display name, width, db_field_parent_table,feidname, db_field_child_table,function name);
        $grid_field_arr = json_encode(array(array("DID Number", "120", "number", "", "", ""),
            array("Increments", "120", "inc", "", "", ""),
            array("Is purchased?", "120", "number", "number", "number", "check_did_avl_reseller"),
            array("Cost", "120", "cost", "cost", "cost", "convert_to_currency"),
            array("Included<br> Seconds", "100", "includedseconds", "", "", ""),
            array("Setup <br> Fee", "109", "setup", "setup", "setup", "convert_to_currency"),
            array("Monthly<br> Fee", "140", "monthlycost", "monthlycost", "monthlycost", "convert_to_currency"),
            array("Connection Fee", "149", "connectcost", "connectcost", "connectcost", "convert_to_currency"),
            array("Disconnection <br> Fee", "140", "disconnectionfee", "disconnectionfee", "disconnectionfee", "convert_to_currency"),
            array("Action", "100", "", "", "", array("DELETE" => array("url" => "/accounts/reseller_did_action/delete/$accountid/$accounttype/", "mode" => "single")))
                ));
        return $grid_field_arr;
    }
    
    function build_did_list_for_user() {
        // array(display name, width, db_field_parent_table,feidname, db_field_child_table,function name);
        $grid_field_arr = json_encode(array(
	    array("DID Number", "135", "number", "", "", ""),
            array("Call Type", "105", "call_type", "call_type", "call_type", "get_call_type"),
            array("Destination", "205", "extensions", "", "", ""),
            array("Country", "110", "country_id", "country", "countrycode", "get_field_name"),
            array("Increments", "110", "inc", "", "", ""),
            array("Cost", "100", "cost", "cost", "cost", "convert_to_currency"),
            array("Included<br> Seconds", "125", "includedseconds", "", "", ""),
            array("Setup <br> Fee", "130", "setup", "setup", "setup", "convert_to_currency"),
            array("Monthly<br> Fee", "130", "monthlycost", "monthlycost", "monthlycost", "convert_to_currency"),
            array("Action", "100", "", "", "", array("EDIT" => array("url" => "/user/user_did_edit/", "mode" => "popup"),
                    "DELETE" => array("url" => "/user/user_dids_action/delete/", "mode" => "single")))
                ));
        return $grid_field_arr;
    }
}

?>
