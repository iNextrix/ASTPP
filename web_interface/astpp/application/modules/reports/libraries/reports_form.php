<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Reports_form {
    function __construct() {
        $this->CI = & get_instance();
    }
    function get_customer_cdr_form() {
        if ($this->CI->session->userdata('logintype') == 1 || $this->CI->session->userdata('logintype') == 5) {
            $accountinfo = $this->CI->session->userdata['accountinfo'];
            $reseller_id = $accountinfo["id"];
        } else {
            $reseller_id = "0";
        }
        $form['forms'] = array("", array('id' => "cdr_customer_search"));
        $form['Search'] = array(
           array('From Date', 'INPUT', array('name' => 'callstart[]', 'id' => 'customer_cdr_from_date', 'size' => '20', 'class' => "text field "), '', 'tOOL TIP', '', 'start_date[start_date-date]'),
            array('To Date', 'INPUT', array('name' => 'callstart[]', 'id' => 'customer_cdr_to_date', 'size' => '20', 'class' => "text field "), '', 'tOOL TIP', '', 'end_date[end_date-date]'),
            array('CallerID', 'INPUT', array('name' => 'callerid[callerid]', '', 'id' => 'first_name', 'size' => '15', 'class' => "text field "), '', 'tOOL TIP', '1', 'callerid[callerid-string]', '', '', '', 'search_string_type', ''),
            array('Called Number', 'INPUT', array('name' => 'callednum[callednum]', 'value' => '', 'size' => '20', 'class' => "text field "), '', 'Tool tips info', '1', 'callednum[callednum-string]', '', '', '', 'search_string_type', ''),
             array('Code ', 'INPUT', array('name' => 'pattern[pattern]', 'value' => '', 'size' => '20', 'class' => "text field "), '', 'Tool tips info', '1', 'pattern[pattern-string]', '', '', '', 'search_string_type', ''),
            array('Destination ', 'INPUT', array('name' => 'notes[notes]', 'value' => '', 'size' => '20', 'class' => "text field "), '', 'Tool tips info', '1', 'notes[notes-string]', '', '', '', 'search_string_type', ''),
            array('Duration ', 'INPUT', array('name' => 'billseconds[billseconds]', 'value' => '', 'size' => '20', 'class' => "text field "), '', 'Tool tips info', '1', 'billseconds[billseconds-integer]', '', '', '', 'search_int_type', ''),
	    array('Debit ', 'INPUT', array('name' => 'debit[debit]', 'value' => '', 'size' => '20', 'class' => "text field "), '', 'Tool tips info', '1', 'debit[debit-integer]', '', '', '', 'search_int_type', ''),
            array('Cost ', 'INPUT', array('name' => 'cost[cost]', 'value' => '', 'size' => '20', 'class' => "text field "), '', 'Tool tips info', '1', 'cost[cost-integer]', '', '', '', 'search_int_type', ''),
	    array('Disposition', 'disposition', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', '', '', '', 'set_despostion'),
            array('Account', 'accountid', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'IF(`deleted`=1,concat( first_name, " ", last_name, " ", "(", number, ")^" ),concat( first_name, " ", last_name, " ", "(", number, ")" )) as number', 'accounts', 'build_dropdown_deleted', 'where_arr', array("reseller_id" => "0","type"=>"GLOBAL")),
 
	  array('Trunk', 'trunk_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'IF(`status`=2, concat(name,"","^"),name) as name', 'trunks', 'build_dropdown_deleted', '', array("status" => "1")),

           array('Rate Group', 'pricelist_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'IF(`status`=2, concat(name,"","^"),name) as name', 'pricelists', 'build_dropdown_deleted', 'where_arr', array("reseller_id" => "0")),

          // array('Trunk', 'trunk_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'name', 'trunks', 'build_dropdown', 'where_arr', array("status" => "1")),
        //   array('Rate Group', 'pricelist_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'name', 'pricelists', 'build_dropdown', 'where_arr',array("reseller_id" => $reseller_id)),
            array('Call Type', 'calltype', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', '', '', '', 'set_calltype'),
            array('', 'HIDDEN', 'ajax_search', '1', '', '', ''),
            array('', 'HIDDEN', 'advance_search', '1', '', '', ''));

        $form['button_search'] = array('name' => 'action', 'id' => "cusotmer_cdr_search_btn", 'content' => 'Search', 'value' => 'save', 'type' => 'button', 'class' => 'btn btn-line-parrot pull-right');
        $form['button_reset'] = array('name' => 'action', 'id' => "id_reset", 'content' => 'Clear', 'value' => 'cancel', 'type' => 'reset', 'class' => 'btn btn-line-sky pull-right margin-x-10');

        return $form;
    }
   function get_user_cdr_form() {
        $form['forms'] = array("", array('id' => "user_cdrs_report_search"));
        $form['Search'] = array(
            array('From Date', 'INPUT', array('name' => 'callstart[]', 'id' => 'customer_cdr_from_date', 'size' => '20', 'class' => "text field "), '', 'tOOL TIP', '', 'start_date[start_date-date]'),
            array('To Date', 'INPUT', array('name' => 'callstart[]', 'id' => 'customer_cdr_to_date', 'size' => '20', 'class' => "text field "), '', 'tOOL TIP', '', 'end_date[end_date-date]'),
            array('Caller ID', 'INPUT', array('name' => 'callerid[callerid]', '', 'id' => 'first_name', 'size' => '15', 'class' => "text field "), '', 'tOOL TIP', '1', 'callerid[callerid-string]', '', '', '', 'search_string_type', ''),
            array('Called Number', 'INPUT', array('name' => 'callednum[callednum]', 'value' => '', 'size' => '20', 'class' => "text field "), '', 'Tool tips info', '1', 'callednum[callednum-string]', '', '', '', 'search_string_type', ''),
          //   array('Code ', 'INPUT', array('name' => 'pattern[pattern]', 'value' => '', 'size' => '20', 'class' => "text field "), '', 'Tool tips info', '1', 'pattern[pattern-integer]', '', '', '', 'search_int_type', ''),
            array('Duration', 'INPUT', array('name' => 'billseconds[billseconds]', 'value' => '', 'size' => '20', 'class' => "text field "), '', 'Tool tips info', '1', 'billseconds[billseconds-integer]', '', '', '', 'search_int_type', ''),
	    array('Disposition', 'disposition', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', '', '', '', 'set_despostion'),
            array('Debit ', 'INPUT', array('name' => 'debit[debit]', 'value' => '', 'size' => '20', 'class' => "text field "), '', 'Tool tips info', '1', 'debit[debit-integer]', '', '', '', 'search_int_type', ''),
            array('Destination ', 'INPUT', array('name' => 'notes[notes]', 'value' => '', 'size' => '20', 'class' => "text field "), '', 'Tool tips info', '1', 'notes[notes-integer]', '', '', '', 'search_int_type', ''),
           
	    //array('Cost ', 'INPUT', array('name' => 'cost[cost]', 'value' => '', 'size' => '20', 'class' => "text field "), '', 'Tool tips info', '1', 'cost[cost-integer]', '', '', '', 'search_int_type', ''),
           //array('Account Number', 'number', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'first_name,last_name,number', 'accounts', 'build_concat_dropdown', 'where_arr', array("reseller_id" => "0","type"=>"0", "deleted" => "0")),
         //array('Trunk', 'trunk_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'name', 'trunks', 'build_dropdown', '', ''),
         // array('Rate Group', 'pricelist_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'name', 'pricelists', 'build_dropdown', '', ''),
            array('Call Type', 'calltype', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', '', '', '', 'set_calltype'),
    
            array('', 'HIDDEN', 'ajax_search', '1', '', '', ''),
            array('', 'HIDDEN', 'advance_search', '1', '', '', '')       
        );

        $form['button_search'] = array('name' => 'action', 'id' => "user_cdr_search_btn", 'content' => 'Search', 'value' => 'save', 'type' => 'button', 'class' => 'btn btn-line-parrot pull-right');
        $form['button_reset'] = array('name' => 'action', 'id' => "id_reset", 'content' => 'Clear', 'value' => 'cancel', 'type' => 'reset', 'class' => 'btn btn-line-sky pull-right margin-x-10');

        return $form;
    }
    function get_reseller_cdr_form() {
        $form['forms'] = array("", array('id' => "cdr_reseller_search"));
        $form['Search'] = array(
            array('From Date', 'INPUT', array('name' => 'callstart[]', 'id' => 'customer_cdr_from_date', 'size' => '20', 'class' => "text field "), '', 'tOOL TIP', '', 'number[number-string]'),
            array('To Date', 'INPUT', array('name' => 'callstart[]', 'id' => 'customer_cdr_to_date', 'size' => '20', 'class' => "text field "), '', 'tOOL TIP', '', 'number[number-string]'),
            array('Caller Id', 'INPUT', array('name' => 'callerid[callerid]', '', 'id' => 'first_name', 'size' => '15', 'class' => "text field "), '', 'tOOL TIP', '1', 'callerid[callerid-string]', '', '', '', 'search_string_type', ''),
            array('Called Number', 'INPUT', array('name' => 'callednum[callednum]', 'value' => '', 'size' => '20', 'class' => "text field "), '', 'Tool tips info', '1', 'callednum[callednum-string]', '', '', '', 'search_string_type', ''),
            
	    array('Code ', 'INPUT', array('name' => 'pattern[pattern]', 'value' => '', 'size' => '20', 'class' => "text field "), '', 'Tool tips info', '1', 'pattern[pattern-string]', '', '', '', 'search_string_type', ''),
            array('Destination ', 'INPUT', array('name' => 'notes[notes]', 'value' => '', 'size' => '20', 'class' => "text field "), '', 'Tool tips info', '1', 'notes[notes-string]', '', '', '', 'search_string_type', ''),
            
            
            array('Duration ', 'INPUT', array('name' => 'billseconds[billseconds]', 'value' => '', 'size' => '20', 'class' => "text field "), '', 'Tool tips info', '1', 'billseconds[billseconds-integer]', '', '', '', 'search_int_type', ''),
            array('Debit ', 'INPUT', array('name' => 'debit[debit]', 'value' => '', 'size' => '20', 'class' => "text field "), '', 'Tool tips info', '1', 'debit[debit-integer]', '', '', '', 'search_int_type', ''),
            array('Cost ', 'INPUT', array('name' => 'cost[cost]', 'value' => '', 'size' => '20', 'class' => "text field "), '', 'Tool tips info', '1', 'cost[cost-integer]', '', '', '', 'search_int_type', ''),
            
            array('Disposition', 'disposition', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', '', '', '', 'set_despostion'),
	    array('Account', 'accountid', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'IF(`deleted`=1,concat( first_name, " ", last_name, " ", "(", number, ")^" ),concat( first_name, " ", last_name, " ", "(", number, ")" )) as number', 'accounts', 'build_dropdown_deleted', 'where_arr', array("reseller_id" => "0","type"=>"1")),
	     array('Rate Group', 'pricelist_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'IF(`status`=2, concat(name,"","^"),name) as name', 'pricelists', 'build_dropdown_deleted', 'where_arr', array("reseller_id" => "0")),
            array('Call Type', 'calltype', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', '', '', '', 'set_calltype'),
        
            array('', 'HIDDEN', 'ajax_search', '1', '', '', ''),
            array('', 'HIDDEN', 'advance_search', '1', '', '', ''));
        if ($this->CI->session->userdata('logintype') != 1 && $this->CI->session->userdata('logintype') != 5) {
	  $new_Array=array('Trunk', 'trunk_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'name', 'trunks', 'build_dropdown', 'where_arr', array("status" => "1"));     
	}
	
        $form['button_search'] = array('name' => 'action', 'id' => "reseller_cdr_search_btn", 'content' => 'Search', 'value' => 'save', 'type' => 'button', 'class' => 'btn btn-line-parrot pull-right');
        $form['button_reset'] = array('name' => 'action', 'id' => "id_reset", 'content' => 'Clear', 'value' => 'cancel', 'type' => 'reset', 'class' => 'btn btn-line-sky pull-right margin-x-10');

        return $form;
    }

    function get_provider_cdr_form() {
        $form['forms'] = array("", array('id' => "cdr_provider_search"));
        $form['Search'] = array(
            array('From Date', 'INPUT', array('name' => 'callstart[]', 'id' => 'customer_cdr_from_date', 'size' => '20', 'class' => "text field "), '', 'tOOL TIP', '', 'number[number-string]'),
            array('To Date', 'INPUT', array('name' => 'callstart[]', 'id' => 'customer_cdr_to_date', 'size' => '20', 'class' => "text field "), '', 'tOOL TIP', '', 'number[number-string]'),
            array('CallerID', 'INPUT', array('name' => 'callerid[callerid]', '', 'id' => 'first_name', 'size' => '15', 'class' => "text field "), '', 'tOOL TIP', '1', 'callerid[callerid-string]', '', '', '', 'search_string_type', ''),
            array('Called Number', 'INPUT', array('name' => 'callednum[callednum]', 'value' => '', 'size' => '20', 'class' => "text field "), '', 'Tool tips info', '1', 'callednum[callednum-string]', '', '', '', 'search_string_type', ''),
            array('Code ', 'INPUT', array('name' => 'pattern[pattern]', 'value' => '', 'size' => '20', 'class' => "text field "), '', 'Tool tips info', '1', 'pattern[pattern-string]', '', '', '', 'search_string_type', ''),
            array('Destination ', 'INPUT', array('name' => 'notes[notes]', 'value' => '', 'size' => '20', 'class' => "text field "), '', 'Tool tips info', '1', 'notes[notes-string]', '', '', '', 'search_string_type', ''),
            array('Duration', 'INPUT', array('name' => 'billseconds[billseconds]', 'value' => '', 'size' => '20', 'class' => "text field "), '', 'Tool tips info', '1', 'billseconds[billseconds-integer]', '', '', '', 'search_int_type', ''),
	    array('Cost ', 'INPUT', array('name' => 'provider_call_cost[provider_call_cost]', 'value' => '', 'size' => '20', 'class' => "text field "), '', 'Tool tips info', '1', 'provider_call_cost[provider_call_cost-integer]', '', '', '', 'search_int_type', ''),
//	  array('Cost ', 'INPUT', array('name' => 'cost[cost]', 'value' => '', 'size' => '20', 'class' => "text field "), '', 'Tool tips info', '1', 'cost[cost-integer]', '', '', '', 'search_int_type', ''),
          array('Disposition', 'disposition', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', '', '', '', 'set_despostion'),
 array('Account', 'provider_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'IF(`deleted`=1,concat( first_name, " ", last_name, " ", "(", number, ")^" ),concat( first_name, " ", last_name, " ", "(", number, ")" )) as number', 'accounts', 'build_dropdown_deleted', 'where_arr', array("reseller_id" => "0","type"=>"3")),
            array('', 'HIDDEN', 'ajax_search', '1', '', '', ''),array('', 'HIDDEN', 'advance_search', '1', '', '', '')

//	  array('Trunk', 'trunk_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'name', 'trunks', 'build_dropdown', '', ''),
            
            
        );

        $form['button_search'] = array('name' => 'action', 'id' => "provider_cdr_search_btn", 'content' => 'Search', 'value' => 'save', 'type' => 'button', 'class' => 'btn btn-line-parrot pull-right');
        $form['button_reset'] = array('name' => 'action', 'id' => "id_reset", 'content' => 'Clear', 'value' => 'cancel', 'type' => 'reset', 'class' => 'btn btn-line-sky pull-right margin-x-10');

        return $form;
    }

    function build_report_list_for_admin() {
             $account_data = $this->CI->session->userdata("accountinfo");
	     if($account_data['type'] == 1){
		$trunk='';
	    }else{
		$trunk=array("Trunk", "80", "trunk_id", "name", "trunks", "get_field_name");
	    }
        $grid_field_arr = json_encode(array(
		array("Date", "100", "callstart","callstart", "callstart", "convert_GMT_to"),
		array("CallerID", "120", "callerid", "", "", ""),
		array("Called Number", "115", "callednum", "", "", ""),
		array("Code", "60", "pattern", "pattern", "", "get_only_numeric_val"),
		array("Destination", "90", "notes", "", "", ""),
		array("Duration", "100", "billseconds", "", "", ""),
		array("Debit", "70", "debit", "debit", "debit", "convert_to_currency"),
		array("Cost", "70", "cost", "cost", "cost", "convert_to_currency"),
		array("Disposition", "150", "disposition", "", "", ""),
		array("Account", "110", "accountid", "first_name,last_name,number", "accounts", "build_concat_string"),
		$trunk,
		array("Rate Group", "90", "pricelist_id", "name", "pricelists", "get_field_name"),
		array("Call Type", "112", "calltype", "", "", ""),
		
          
//             array("Provider", "80", "provider_id", "number", "accounts", "get_field_name"),
           
           
          
                ));
        return $grid_field_arr;
    }

    function build_report_list_for_reseller() {
        // array(display name, width, db_field_parent_table,feidname, db_field_child_table,function name);
        $grid_field_arr = json_encode(array(
		    array("Date", "100", "callstart", "callstart", "callstart", "convert_GMT_to"),
		    array("CallerID", "80", "callerid", "", "", ""),
		    array("Called Number", "120", "callednum", "", "", ""),
		    array("Code", "70", "pattern", "pattern", "", "get_only_numeric_val"),
		    array("Destination", "120", "notes", "", "", ""),
		    array("Duration", "140", "billseconds", "", "", ""),
		    array("Debit", "90", "debit", "debit", "debit", "convert_to_currency"),
		    array("Cost", "90", "cost", "cost", "cost", "convert_to_currency"),
		    array("Disposition", "100", "disposition", "", "", ""),
		    array("Account", "110", "accountid", "first_name,last_name,number", "accounts", "build_concat_string"),
		    array("Rate Group", "100", "pricelist_id", "name", "pricelists", "get_field_name"),
 		    array("Call Type", "148", "calltype", "", "", ""), 
		    
           ));
        return $grid_field_arr;
    }

    function build_report_list_for_provider() {
        // array(display name, width, db_field_parent_table,feidname, db_field_child_table,function name);
        $grid_field_arr = json_encode(array(array("Date", "100", "callstart", "callstart", "callstart", "convert_GMT_to"),
		  array("CallerID", "120", "callerid", "", "", ""),
		  array("Called Number", "170", "callednum", "", "", ""),
		  array("Code", "117", "pattern", "pattern", "", "get_only_numeric_val"),
		  array("Destination", "130", "notes", "", "", ""),
		  array("Duration", "134", "billseconds", "", "", ""),
		  array("Cost", "150", "provider_call_cost", "provider_cost", "provider_cost", "convert_to_currency"),
// 		  array("Cost", "50", "cost", "", "", ""),
		  array("Disposition", "200", "disposition", "", "", ""),
		  array("Account", "150", "provider_id", "first_name,last_name,number", "accounts", "build_concat_string"),
// 		  array("Rate Group", "130", "pricelist_id", "name", "pricelists", "get_field_name"),
// 		  array("Call Type", "120", "calltype", "", "", ""),
		  
	    ));
        return $grid_field_arr;
    }

    function build_grid_buttons() {
        $buttons_json = json_encode(array(array("Export","btn btn-xing" ," fa fa-download fa-lg", "button_action", "/reports/customerReport_export_cdr_xls", 'single')));
        return $buttons_json;
    }

    function build_grid_buttons_user() {
        $buttons_json = json_encode(array(array("Export","btn btn-xing" ," fa fa-download fa-lg", "button_action", "/user/user_report_export_cdr_xls", 'single')));
        return $buttons_json;
    }
    function build_grid_buttons_reseller() {
        $buttons_json = json_encode(array( array("Export","btn btn-xing" ," fa fa-download fa-lg", "button_action", "/reports/resellerReport_export_cdr_xls", 'single')));
        return $buttons_json;
    }
    function build_grid_buttons_provider() {
        $buttons_json = json_encode(array(array("Export","btn btn-xing" ," fa fa-download fa-lg", "button_action", "/reports/providerReport_export_cdr_xls/", 'single')));
        return $buttons_json;
    }
    
    function build_report_list_for_user() {
     // array(display name, width, db_field_parent_table,feidname, db_field_child_table,function name);
        $grid_field_arr = json_encode(array(
        array("Date", "170", "callstart", "callstart", "callstart", "convert_GMT_to"),
            array("CallerID", "110", "callerid", "", "", ""),
            array("Called Number", "160", "callednum", "", "", ""),
            array("Destination", "160", "notes", "", "", ""),
//            array("Account Number", "120", "accountid", "number", "accounts", "get_field_name"),
            array("Duration", "140", "billseconds", "", "", ""),
            array("Debit", "140", "debit", "debit", "debit", "convert_to_currency"),
            array("Disposition", "160", "disposition", "", "", ""),
            array("Call Type", "233", "calltype", "", "", ""),
                ));
        return $grid_field_arr;
    }

    function build_payment_report_for_user() {
      $grid_field_arr = json_encode(array(
	
            array("Date", "225", "payment_date", "", "", ""),
            array("Account", "260", "accountid", "first_name,last_name,number", "accounts", "build_concat_string"),
            array("Amount", "250", "credit", "credit", "credit", "convert_to_currency"),
//             array("Payment Type", "150", "type", "", "", ""),
            array("Payment By", "230", "payment_by", "payment_by", "payment_by", "get_payment_by"),
            array("Note", "290", "notes", "", "", "")
                ));
        return $grid_field_arr;
    }
    function get_user_cdr_payment_form() {
        
         $form['forms'] = array("", array('id' => "cdr_payment_search"));
             $account_data = $this->CI->session->userdata("accountinfo");
             $acc_arr= array('Account', 'accountid', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'IF(`deleted`=1,concat( first_name, " ", last_name, " ", "(", number, ")^" ),concat( first_name, " ", last_name, " ", "(", number, ")" )) as number', 'accounts', 'build_dropdown_deleted', 'where_arr', array("reseller_id" => "0","type"=>"GLOBAL"));
             $logintype = $this->CI->session->userdata('logintype');
        if ($logintype == 1 || $logintype == 5) {
            $account_data = $this->CI->session->userdata("accountinfo");
            $loginid = $account_data['id'];

        }else{
            $loginid = "0";
        }
        if($logintype==0 || $logintype==3){
	    $acc_arr=null;
        }
             //echo '<pre>'; print_r($account_data); exit;
        //
           
          //   $accounts = array('Account', 'accountid', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'first_name,last_name,number', 'accounts', 'build_concat_dropdown', 'where_arr', array( 'id' => $account_data['id'],'type'=>'0'));
          //}else{
           //  $accounts = array('Account', 'accountid', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'first_name,last_name,number', 'accounts', 'build_concat_dropdown', 'where_arr', array('reseller_id' => '0','type'=>'0'));
          //}
      //    echo '<pre>'; print_r( $accounts); EXIT;
        $form['Search'] = array(
       array('From Date', 'INPUT', array('name' => 'payment_date[]', 'id' => 'customer_cdr_from_date', 'size' => '20',
 'class' => "text field "), '', 'tOOL TIP', '', 'payment_date[payment_date-date]'),
            array('To Date', 'INPUT', array('name' => 'payment_date[]', 'id' => 'customer_cdr_to_date', 'size' => '20', 'class' => "text field "), '', 'tOOL TIP', '', 'payment_date[payment_date-date]'),
// 	     array('Account', 'accountid', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'IF(`deleted`=1,concat( first_name, " ", last_name, " ", "(", number, ")^" ),concat( first_name, " ", last_name, " ", "(", number, ")" )) as number', 'accounts', 'build_dropdown_deleted', 'where_arr', array("reseller_id" => "0","type"=>"0")),
 	     $acc_arr,
//	    array('Account', 'accountid', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'first_name,last_name,number', 'accounts', 'build_concat_dropdown', 'where_arr', array("reseller_id" => "0","type"=>"0")),
 
            array('Amount ', 'INPUT', array('name' => 'credit[credit]', 'value' => '', 'size' => '20', 'class' => "text field"), '', 'Tool tips info', '1', 'credit[credit-integer]', '', '', '', 'search_int_type', ''),
           // array('', 'HIDDEN', 'ajax_search', '1', '', '', ''),array('', 'HIDDEN', 'advance_search', '1', '', '', ''),
            array('', 'HIDDEN', 'ajax_search', '1', '', '', ''),array('', 'HIDDEN', 'advance_search', '1', '', '', ''));

        $form['button_search'] = array('name' => 'action', 'id' => "cusotmer_cdr_payment_search_btn", 'content' => 'Search', 'value' => 'save', 'type' => 'button', 'class' => 'btn btn-line-parrot pull-right');
        $form['button_reset'] = array('name' => 'action', 'id' => "id_reset", 'content' => 'Clear', 'value' => 'cancel', 'type' => 'reset', 'class' => 'btn btn-line-sky pull-right margin-x-10');
//echo '<pre>'; print_r($form); exit;
        return $form;
    }
    function build_commission_report_for_admin() {
        $grid_field_arr = json_encode(array(
            array("Account", "150", "accountid", "first_name,last_name,number", "accounts", "build_concat_string"),
            array("Amount", "150", "amount", "credit", "credit", "convert_to_currency"),
            array("Description", "150", "description", "", "", ""),
            array("Reseller", "150", "reseller_id", "first_name,last_name,number", "accounts", "build_concat_string"),
            array("Commission Rate(%)", "150", "commission_percent", "", "", ""),
            array("Date", "150", "date", "", "", "")
            ));
        return $grid_field_arr;
    }
    function reseller_commission_search_form() {
        $form['forms'] = array("", array('id' => "reseller_commission_search"));
        $form['User Payment Report'] = array(
            array('', 'HIDDEN', 'ajax_search', '1', '', '', ''),
            array('', 'HIDDEN', 'advance_search', '1', '', '', ''),
            array('From Date', 'INPUT', array('name' => 'date[]', 'id' => 'commission_from_date', 'size' => '20', 'maxlength' => '15', 'class' => "text field "), '', 'tOOL TIP', '', 'start_date[start_date-date]'),
            array('To Date', 'INPUT', array('name' => 'date[]', 'id' => 'commission_to_date', 'size' => '20', 'maxlength' => '15', 'class' => "text field "), '', 'tOOL TIP', '', 'end_date[end_date-date]'),
	    array('Account', 'accountid', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'first_name,last_name,number', 'accounts', 'build_concat_dropdown', 'where_arr', array("reseller_id" => "0","type"=>"1", "deleted" => "0")),
            array('Amount', 'INPUT', array('name' => 'amount[amount]', 'value' => '', 'size' => '20', 'maxlength' => '15', 'class' => "text field"), '', 'Tool tips info', '1', 'amount[amount-integer]', '', '', '', 'search_int_type', ''),
        );

        $form['button_search'] = array('name' => 'action', 'id' => "commission_search_btn", 'content' => 'Search', 'value' => 'save', 'type' => 'button', 'class' => 'ui-state-default float-right ui-corner-all ui-button');
        $form['button_reset'] = array('name' => 'action', 'id' => "id_reset", 'content' => 'Clear Search Filter', 'value' => 'cancel', 'type' => 'reset', 'class' => 'ui-state-default float-right ui-corner-all ui-button');

        return $form;
    }
    function get_providersummary_search_form() {
        $form['forms'] = array('', array('id' => "providersummary_search"));
        $form['Search'] = array(
	array('From Date', 'INPUT', array('name' => 'callstart[]', 'id' => 'customer_from_date', 'size' => '20', 'class' => "text field "), '', 'tOOL TIP', '', 'start_date[start_date-date]'),
            array('To Date', 'INPUT', array('name' => 'callstart[]', 'id' => 'customer_to_date', 'size' => '20', 'class' => "text field "), '', 'tOOL TIP', '', 'end_date[end_date-date]'),
	    
           // array('From Date', 'INPUT', array('name' => 'start_date', 'id' => 'provider_from_date', 'size' => '20', 'class' => "text field "), '', 'tOOL TIP', '', 'start_date[start_date-date]'),
           // array('To Date', 'INPUT', array('name' => 'end_date', 'id' => 'provider_to_date', 'size' => '20', 'class' => "text field "), '', 'tOOL TIP', '', 'end_date[end_date-date]'),
           array('Account', 'provider_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'IF(`deleted`=1,concat( first_name, " ", last_name, " ", "(", number, ")^" ),concat( first_name, " ", last_name, " ", "(", number, ")" )) as number', 'accounts', 'build_dropdown_deleted', 'where_arr', array("reseller_id" => "0","type"=>"3")),
           array('Code ', 'INPUT', array('name' => 'pattern[pattern]', 'value' => '', 'size' => '20', 'maxlength' => '15', 'class' => "text field "), '', 'Tool tips info', '1', 'pattern[pattern-string]', '', '', '', 'search_string_type', ''),
            
            array('Destination ', 'INPUT', array('name' => 'notes[notes]', 'value' => '', 'size' => '20', 'class' => "text field "), '', 'Tool tips info', '1', 'notes[notes-string]', '', '', '', 'search_string_type', ''), 
		// array('Account Number', 'number', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'first_name,last_name,number', 'accounts', 'build_concat_dropdown', 'where_arr', array("type"=>"3", "deleted" => "0")),
            array('', 'HIDDEN', 'ajax_search', '1', '', '', ''),
            array('', 'HIDDEN', 'advance_search', '1', '', '', ''));
        $form['button_search'] = array('name' => 'action', 'id' => "providersummary_search_btn", 'content' => 'Search', 'value' => 'save', 'type' => 'button', 'class' => 'btn btn-line-parrot pull-right');
        $form['button_reset'] = array('name' => 'action', 'id' => "id_reset", 'content' => 'Clear', 'value' => 'cancel', 'type' => 'reset', 'class' => 'btn btn-line-sky pull-right margin-x-10');

        return $form;
    }
    function build_providersummary(){
        $grid_field_arr = json_encode(array(
            array("Provider", "220", "provider_id", "first_name,last_name,number", "accounts", "build_concat_string"),
            array("Code", "120", "pattern", "pattern", "", "get_only_numeric_val"),
            array("Destination", "150", "notes", "", "", ""),
            array("Attempted Calls", "130", "attempted_calls", "", "", ""),
            array("Completed Calls", "150", "description", "", "", ""),
            array("ASR","95","asr",'','',''),
            array("ACD","95","acd  ",'','',''),
            array("MCD","95","mcd",'','',''),
            array("Bilable","100","billable",'','',''),
            array("Cost","115","cost",'','',''),
//            array("Profit", "95", "profit", "", "", ""),
            ));
        return $grid_field_arr;
    }
    function build_grid_buttons_providersummary() {
       $buttons_json = json_encode(array(array("Export","btn btn-xing" ," fa fa-download fa-lg", "button_action", "/reports/providersummary_export_cdr_xls", 'single')));
        return $buttons_json;
    }
    function get_resellersummary_search_form() {
        $form['forms'] = array("",array('id' => "resellersummary_search"));
        $form['Search'] = array(
            array('From Date', 'INPUT', array('name' => 'callstart[]', 'id' => 'customer_from_date', 'size' => '20', 'class' => "text field "), '', 'tOOL TIP', '', 'start_date[start_date-date]'),
            array('To Date', 'INPUT', array('name' => 'callstart[]', 'id' => 'customer_to_date', 'size' => '20', 'class' => "text field "), '', 'tOOL TIP', '', 'end_date[end_date-date]'),
	
           // array('From Date', 'INPUT', array('name' => 'start_date', 'id' => 'reseller_from_date', 'size' => '20', 'class' => "text field "), '', 'tOOL TIP', '', 'start_date[start_date-date]'),
           // array('To Date', 'INPUT', array('name' => 'end_date', 'id' => 'reseller_to_date', 'size' => '20', 'class' => "text field "), '', 'tOOL TIP', '', 'end_date[end_date-date]'),
	    array('Account', 'reseller_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'IF(`deleted`=1,concat( first_name, " ", last_name, " ", "(", number, ")^" ),concat( first_name, " ", last_name, " ", "(", number, ")" )) as number', 'accounts', 'build_dropdown_deleted', 'where_arr', array("reseller_id" => "0","type"=>"1")),
           array('Code ', 'INPUT', array('name' => 'pattern[pattern]', 'value' => '', 'size' => '20', 'maxlength' => '15', 'class' => "text field "), '', 'Tool tips info', '1', 'pattern[pattern-string]', '', '', '', 'search_string_type', ''),
            
            array('Destination ', 'INPUT', array('name' => 'notes[notes]', 'value' => '', 'size' => '20', 'class' => "text field "), '', 'Tool tips info', '1', 'notes[notes-string]', '', '', '', 'search_string_type', ''), 
           // array('Account Number', 'number', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'first_name,last_name,number', 'accounts', 'build_concat_dropdown', 'where_arr', array("type"=>"1", "deleted" => "0")),
array('', 'HIDDEN', 'ajax_search', '1', '', '', ''),array('', 'HIDDEN', 'advance_search', '1', '', '', ''));
        $form['button_search'] = array('name' => 'action', 'id' => "resellersummary_search_btn", 'content' => 'Search', 'value' => 'save', 'type' => 'button', 'class' => 'btn btn-line-parrot pull-right');
        $form['button_reset'] = array('name' => 'action', 'id' => "id_reset", 'content' => 'Clear', 'value' => 'cancel', 'type' => 'reset', 'class' => 'btn btn-line-sky pull-right margin-x-10');

        return $form;
    }
    function build_resellersummary(){
        $grid_field_arr = json_encode(array(
            array("Account", "148", "accountid", "first_name,last_name,number", "accounts", "build_concat_string"),
            array("Code", "120", "pattern", "pattern", "", "get_only_numeric_val"),
            array("Destination", "150", "notes", "", "", ""),
            array("Attempted Calls", "120", "attempted_calls", "", "", ""),
            array("Completed Calls", "120", "description", "", "", ""),
            array("ASR","80","asr",'','',''),
            array("ACD","80","acd  ",'','',''),
            array("MCD","80","mcd",'','',''),
            array("Bilable","90","billable",'','',''),
            array("Price","90","price",'','',''),
            array("Cost","90","cost",'','',''),
            array("Profit", "100", "profit", "", "", ""),
            ));
        return $grid_field_arr;
    }
    function build_grid_buttons_resellersummary() {
         $buttons_json = json_encode(array(array("Export","btn btn-xing" ," fa fa-download fa-lg", "button_action", "/reports/resellersummary_export_cdr_xls", 'single')));
        return $buttons_json;
    }
    function get_customersummary_search_form() {
        $form['forms'] = array("",array('id' => "customersummary_search"));
        $form['Search'] = array(
            array('From Date', 'INPUT', array('name' => 'callstart[]', 'id' => 'customer_from_date', 'size' => '20', 'class' => "text field "), '', 'tOOL TIP', '', 'start_date[start_date-date]'),
            array('To Date', 'INPUT', array('name' => 'callstart[]', 'id' => 'customer_to_date', 'size' => '20', 'class' => "text field "), '', 'tOOL TIP', '', 'end_date[end_date-date]'),
	    array('Account', 'accountid', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'IF(`deleted`=1,concat( first_name, " ", last_name, " ", "(", number, ")^" ),concat( first_name, " ", last_name, " ", "(", number, ")" )) as number', 'accounts', 'build_dropdown_deleted', 'where_arr', array("reseller_id" => "0","type"=>"GLOBAL")),
            array('Code ', 'INPUT', array('name' => 'pattern[pattern]', 'value' => '', 'size' => '20', 'maxlength' => '15', 'class' => "text field "), '', 'Tool tips info', '1', 'pattern[pattern-string]', '', '', '', 'search_string_type', ''),
            
            array('Destination ', 'INPUT', array('name' => 'notes[notes]', 'value' => '', 'size' => '20', 'class' => "text field "), '', 'Tool tips info', '1', 'notes[notes-string]', '', '', '', 'search_string_type', ''),
           // array('Account Number', 'number', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'first_name,last_name,number', 'accounts', 'build_concat_dropdown', 'where_arr', array("type"=>"0", "deleted" => "0")),
	array('', 'HIDDEN', 'ajax_search', '1', '', '', ''),array('', 'HIDDEN', 'advance_search', '1', '', '', '')
        );
        $form['button_search'] = array('name' => 'action', 'id' => "customersummary_search_btn", 'content' => 'Search', 'value' => 'save', 'type' => 'button', 'class' => 'btn btn-line-parrot pull-right');
        $form['button_reset'] = array('name' => 'action', 'id' => "id_reset", 'content' => 'Clear', 'value' => 'cancel', 'type' => 'reset', 'class' => 'btn btn-line-sky pull-right margin-x-10');

        return $form;
    }
    function build_customersummary(){
        $grid_field_arr = json_encode(array(
            array("Account", "190", "accountid", "first_name,last_name,number", "accounts", "build_concat_string"),
            array("Code", "80", "pattern", "pattern", "", "get_only_numeric_val"),
            array("Destination", "110", "notes", "", "", ""),
            array("Attempted Calls", "140", "attempted_calls", "", "", ""),
            array("Completed Calls", "130", "description", "", "", ""),
            array("ASR","70","asr",'','',''),
            array("ACD","70","acd  ",'','',''),
            array("MCD","80","mcd",'','',''),
            array("Bilable","80","billable",'','',''),
            array("Debit","85","cost",'','',''),
            array("Cost","110","price",'','',''),            
            array("Profit", "123", "profit", "", "", ""),
            ));
        return $grid_field_arr;
    }
    function build_grid_buttons_customersummary() {
        $buttons_json = json_encode(array(array("Export","btn btn-xing" ," fa fa-download fa-lg", "button_action", "/reports/customersummary_export_cdr_xls", 'single')));
        return $buttons_json;
    }

}

?>
