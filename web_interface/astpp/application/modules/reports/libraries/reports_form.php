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
        $form['Search Customer Report'] = array(
            array('', 'HIDDEN', 'ajax_search', '1', '', '', ''),
            array('', 'HIDDEN', 'advance_search', '1', '', '', ''),
            array('From Date', 'INPUT', array('name' => 'callstart[]', 'id' => 'customer_cdr_from_date', 'size' => '20', 'maxlength' => '15', 'class' => "text field "), '', 'tOOL TIP', '', 'start_date[start_date-date]'),
            array('TO Date', 'INPUT', array('name' => 'callstart[]', 'id' => 'customer_cdr_to_date', 'size' => '20', 'maxlength' => '15', 'class' => "text field "), '', 'tOOL TIP', '', 'end_date[end_date-date]'),
            array('Caller ID', 'INPUT', array('name' => 'callerid[callerid]', '', 'id' => 'first_name', 'size' => '15', 'maxlength' => '15', 'class' => "text field "), '', 'tOOL TIP', '1', 'callerid[callerid-string]', '', '', '', 'search_string_type', ''),
            array('Called Number', 'INPUT', array('name' => 'callednum[callednum]', 'value' => '', 'size' => '20', 'maxlength' => '15', 'class' => "text field "), '', 'Tool tips info', '1', 'callednum[callednum-string]', '', '', '', 'search_string_type', ''),
             array('Code ', 'INPUT', array('name' => 'pattern[pattern]', 'value' => '', 'size' => '20', 'maxlength' => '15', 'class' => "text field "), '', 'Tool tips info', '1', 'pattern[pattern-integer]', '', '', '', 'search_int_type', ''),
            array('Destination ', 'INPUT', array('name' => 'notes[notes]', 'value' => '', 'size' => '20', 'maxlength' => '15', 'class' => "text field "), '', 'Tool tips info', '1', 'notes[notes-integer]', '', '', '', 'search_int_type', ''),
            array('Bill Sec ', 'INPUT', array('name' => 'billseconds[billseconds]', 'value' => '', 'size' => '20', 'maxlength' => '15', 'class' => "text field "), '', 'Tool tips info', '1', 'billseconds[billseconds-integer]', '', '', '', 'search_int_type', ''),
	    array('Debit ', 'INPUT', array('name' => 'debit[debit]', 'value' => '', 'size' => '20', 'maxlength' => '15', 'class' => "text field "), '', 'Tool tips info', '1', 'debit[debit-integer]', '', '', '', 'search_int_type', ''),
            array('Cost ', 'INPUT', array('name' => 'cost[cost]', 'value' => '', 'size' => '20', 'maxlength' => '15', 'class' => "text field "), '', 'Tool tips info', '1', 'cost[cost-integer]', '', '', '', 'search_int_type', ''),
	    array('Disposition', 'disposition', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', '', '', '', 'set_despostion'),
            
            array('Account Number', 'accountid', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'first_name,last_name,number', 'accounts', 'build_concat_dropdown', 'where_arr', array("reseller_id" => "0","type"=>"0", "deleted" => "0")),
            array('Trunk', 'trunk_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'name', 'trunks', 'build_dropdown', 'where_arr', array("status" => "1")),
            array('Rate Group', 'pricelist_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'name', 'pricelists', 'build_dropdown', 'where_arr',array("reseller_id" => $reseller_id)),
            array('Call Type', 'calltype', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', '', '', '', 'set_calltype'),
           
        );

        $form['button_search'] = array('name' => 'action', 'id' => "cusotmer_cdr_search_btn", 'content' => 'Search', 'value' => 'save', 'type' => 'button', 'class' => 'ui-state-default float-right ui-corner-all ui-button');
        $form['button_reset'] = array('name' => 'action', 'id' => "id_reset", 'content' => 'Clear Search Filter', 'value' => 'cancel', 'type' => 'reset', 'class' => 'ui-state-default float-right ui-corner-all ui-button');

        return $form;
    }
    function get_user_cdr_form() {
        $form['forms'] = array("", array('id' => "cdr_customer_search"));
        $form['Search Customer Report'] = array(
            array('', 'HIDDEN', 'ajax_search', '1', '', '', ''),
            array('', 'HIDDEN', 'advance_search', '1', '', '', ''),
            array('From Date', 'INPUT', array('name' => 'callstart[]', 'id' => 'customer_cdr_from_date', 'size' => '20', 'maxlength' => '15', 'class' => "text field "), '', 'tOOL TIP', '', 'start_date[start_date-date]'),
            array('TO Date', 'INPUT', array('name' => 'callstart[]', 'id' => 'customer_cdr_to_date', 'size' => '20', 'maxlength' => '15', 'class' => "text field "), '', 'tOOL TIP', '', 'end_date[end_date-date]'),
            array('Caller ID', 'INPUT', array('name' => 'callerid[callerid]', '', 'id' => 'first_name', 'size' => '15', 'maxlength' => '15', 'class' => "text field "), '', 'tOOL TIP', '1', 'callerid[callerid-string]', '', '', '', 'search_string_type', ''),
            array('Called Number', 'INPUT', array('name' => 'callednum[callednum]', 'value' => '', 'size' => '20', 'maxlength' => '15', 'class' => "text field "), '', 'Tool tips info', '1', 'callednum[callednum-string]', '', '', '', 'search_string_type', ''),
//             array('Code ', 'INPUT', array('name' => 'pattern[pattern]', 'value' => '', 'size' => '20', 'maxlength' => '15', 'class' => "text field "), '', 'Tool tips info', '1', 'pattern[pattern-integer]', '', '', '', 'search_int_type', ''),
//            array('Destination ', 'INPUT', array('name' => 'notes[notes]', 'value' => '', 'size' => '20', 'maxlength' => '15', 'class' => "text field "), '', 'Tool tips info', '1', 'notes[notes-integer]', '', '', '', 'search_int_type', ''),
            array('Bill Sec ', 'INPUT', array('name' => 'billseconds[billseconds]', 'value' => '', 'size' => '20', 'maxlength' => '15', 'class' => "text field "), '', 'Tool tips info', '1', 'billseconds[billseconds-integer]', '', '', '', 'search_int_type', ''),
	    array('Disposition', 'disposition', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', '', '', '', 'set_despostion'),
            array('Debit ', 'INPUT', array('name' => 'debit[debit]', 'value' => '', 'size' => '20', 'maxlength' => '15', 'class' => "text field "), '', 'Tool tips info', '1', 'debit[debit-integer]', '', '', '', 'search_int_type', ''),
//            array('Cost ', 'INPUT', array('name' => 'cost[cost]', 'value' => '', 'size' => '20', 'maxlength' => '15', 'class' => "text field "), '', 'Tool tips info', '1', 'cost[cost-integer]', '', '', '', 'search_int_type', ''),
//            array('Account Number', 'number', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'first_name,last_name,number', 'accounts', 'build_concat_dropdown', 'where_arr', array("reseller_id" => "0","type"=>"0", "deleted" => "0")),
//            array('Trunk', 'trunk_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'name', 'trunks', 'build_dropdown', '', ''),
//            array('Rate Group', 'pricelist_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'name', 'pricelists', 'build_dropdown', '', ''),
            array('Call Type', 'calltype', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', '', '', '', 'set_calltype'),
           
        );

        $form['button_search'] = array('name' => 'action', 'id' => "cusotmer_cdr_search_btn", 'content' => 'Search', 'value' => 'save', 'type' => 'button', 'class' => 'ui-state-default float-right ui-corner-all ui-button');
        $form['button_reset'] = array('name' => 'action', 'id' => "id_reset", 'content' => 'Clear Search Filter', 'value' => 'cancel', 'type' => 'reset', 'class' => 'ui-state-default float-right ui-corner-all ui-button');

        return $form;
    }
    function get_reseller_cdr_form() {
        $form['forms'] = array("", array('id' => "cdr_reseller_search"));
        $form['Search Reseller Report'] = array(
            array('', 'HIDDEN', 'ajax_search', '1', '', '', ''),
            array('', 'HIDDEN', 'advance_search', '1', '', '', ''),
            array('From Date', 'INPUT', array('name' => 'callstart[]', 'id' => 'customer_cdr_from_date', 'size' => '20', 'maxlength' => '15', 'class' => "text field "), '', 'tOOL TIP', '', 'number[number-string]'),
            array('To Date', 'INPUT', array('name' => 'callstart[]', 'id' => 'customer_cdr_to_date', 'size' => '20', 'maxlength' => '15', 'class' => "text field "), '', 'tOOL TIP', '', 'number[number-string]'),
            array('Caller Id', 'INPUT', array('name' => 'callerid[callerid]', '', 'id' => 'first_name', 'size' => '15', 'maxlength' => '15', 'class' => "text field "), '', 'tOOL TIP', '1', 'callerid[callerid-string]', '', '', '', 'search_string_type', ''),
            array('Called Number', 'INPUT', array('name' => 'callednum[callednum]', 'value' => '', 'size' => '20', 'maxlength' => '15', 'class' => "text field "), '', 'Tool tips info', '1', 'callednum[callednum-string]', '', '', '', 'search_string_type', ''),
            
	    array('Code ', 'INPUT', array('name' => 'pattern[pattern]', 'value' => '', 'size' => '20', 'maxlength' => '15', 'class' => "text field "), '', 'Tool tips info', '1', 'pattern[pattern-integer]', '', '', '', 'search_int_type', ''),
            array('Destination ', 'INPUT', array('name' => 'notes[notes]', 'value' => '', 'size' => '20', 'maxlength' => '15', 'class' => "text field "), '', 'Tool tips info', '1', 'notes[notes-integer]', '', '', '', 'search_int_type', ''),
            
            
            array('Bill Sec ', 'INPUT', array('name' => 'billseconds[billseconds]', 'value' => '', 'size' => '20', 'maxlength' => '15', 'class' => "text field "), '', 'Tool tips info', '1', 'billseconds[billseconds-integer]', '', '', '', 'search_int_type', ''),
            array('Debit ', 'INPUT', array('name' => 'debit[debit]', 'value' => '', 'size' => '20', 'maxlength' => '15', 'class' => "text field "), '', 'Tool tips info', '1', 'debit[debit-integer]', '', '', '', 'search_int_type', ''),
            array('Cost ', 'INPUT', array('name' => 'cost[cost]', 'value' => '', 'size' => '20', 'maxlength' => '15', 'class' => "text field "), '', 'Tool tips info', '1', 'cost[cost-integer]', '', '', '', 'search_int_type', ''),
            array('Trunk', 'trunk_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'name', 'trunks', 'build_dropdown', 'where_arr', array("status" => "1")),
            array('Disposition', 'disposition', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', '', '', '', 'set_despostion'),
            array('Account', 'accountid', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'first_name,last_name,number', 'accounts', 'build_concat_dropdown', 'where_arr', array("reseller_id" => "0","type"=>"1", "deleted" => "0")),
            array('Rate Group', 'pricelist_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'name', 'pricelists', 'build_dropdown', '', ''),
            array('Call Type', 'calltype', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', '', '', '', 'set_calltype'),
          
        );

        $form['button_search'] = array('name' => 'action', 'id' => "reseller_cdr_search_btn", 'content' => 'Search', 'value' => 'save', 'type' => 'button', 'class' => 'ui-state-default float-right ui-corner-all ui-button');
        $form['button_reset'] = array('name' => 'action', 'id' => "id_reset", 'content' => 'Clear Search Filter', 'value' => 'cancel', 'type' => 'reset', 'class' => 'ui-state-default float-right ui-corner-all ui-button');

        return $form;
    }

    function get_provider_cdr_form() {
        $form['forms'] = array("", array('id' => "cdr_provider_search"));
        $form['Search Provider Report'] = array(
            array('', 'HIDDEN', 'ajax_search', '1', '', '', ''),
            array('', 'HIDDEN', 'advance_search', '1', '', '', ''),
            array('From Date', 'INPUT', array('name' => 'callstart[]', 'id' => 'customer_cdr_from_date', 'size' => '20', 'maxlength' => '15', 'class' => "text field "), '', 'tOOL TIP', '', 'number[number-string]'),
            array('To Date', 'INPUT', array('name' => 'callstart[]', 'id' => 'customer_cdr_to_date', 'size' => '20', 'maxlength' => '15', 'class' => "text field "), '', 'tOOL TIP', '', 'number[number-string]'),
            array('Caller Id', 'INPUT', array('name' => 'callerid[callerid]', '', 'id' => 'first_name', 'size' => '15', 'maxlength' => '15', 'class' => "text field "), '', 'tOOL TIP', '1', 'callerid[callerid-string]', '', '', '', 'search_string_type', ''),
            array('Called Number', 'INPUT', array('name' => 'callednum[callednum]', 'value' => '', 'size' => '20', 'maxlength' => '15', 'class' => "text field "), '', 'Tool tips info', '1', 'callednum[callednum-string]', '', '', '', 'search_string_type', ''),
            array('Code ', 'INPUT', array('name' => 'pattern[pattern]', 'value' => '', 'size' => '20', 'maxlength' => '15', 'class' => "text field "), '', 'Tool tips info', '1', 'pattern[pattern-integer]', '', '', '', 'search_int_type', ''),
            array('Destination ', 'INPUT', array('name' => 'notes[notes]', 'value' => '', 'size' => '20', 'maxlength' => '15', 'class' => "text field "), '', 'Tool tips info', '1', 'notes[notes-integer]', '', '', '', 'search_int_type', ''),
            array('Bill Sec ', 'INPUT', array('name' => 'billseconds[billseconds]', 'value' => '', 'size' => '20', 'maxlength' => '15', 'class' => "text field "), '', 'Tool tips info', '1', 'billseconds[billseconds-integer]', '', '', '', 'search_int_type', ''),
	    array('Debit ', 'INPUT', array('name' => 'debit[debit]', 'value' => '', 'size' => '20', 'maxlength' => '15', 'class' => "text field "), '', 'Tool tips info', '1', 'debit[debit-integer]', '', '', '', 'search_int_type', ''),
//	  array('Cost ', 'INPUT', array('name' => 'cost[cost]', 'value' => '', 'size' => '20', 'maxlength' => '15', 'class' => "text field "), '', 'Tool tips info', '1', 'cost[cost-integer]', '', '', '', 'search_int_type', ''),
          array('Disposition', 'disposition', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', '', '', '', 'set_despostion'),
	  array('Account', 'number', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'first_name,last_name,number', 'accounts', 'build_concat_dropdown', 'where_arr', array("reseller_id" => "0","type"=>"3", "deleted" => "0")),
//	  array('Trunk', 'trunk_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'name', 'trunks', 'build_dropdown', '', ''),
            
            
        );

        $form['button_search'] = array('name' => 'action', 'id' => "provider_cdr_search_btn", 'content' => 'Search', 'value' => 'save', 'type' => 'button', 'class' => 'ui-state-default float-right ui-corner-all ui-button');
        $form['button_reset'] = array('name' => 'action', 'id' => "id_reset", 'content' => 'Clear Search Filter', 'value' => 'cancel', 'type' => 'reset', 'class' => 'ui-state-default float-right ui-corner-all ui-button');

        return $form;
    }

    function build_report_list_for_admin() {
        // array(display name, width, db_field_parent_table,feidname, db_field_child_table,function name);
        $grid_field_arr = json_encode(array(
		array("Date", "120", "callstart", "callstart", "callstart", "convert_GMT_to"),
		array("CallerID", "100", "callerid", "", "", ""),
		array("Called Number", "95", "callednum", "", "", ""),
		array("Code", "60", "pattern", "pattern", "", "get_only_numeric_val"),
		array("Destination", "70", "notes", "", "", ""),
		array("Bill<br> Seconds", "50", "billseconds", "", "", ""),
		array("Debit", "70", "debit", "debit", "debit", "convert_to_currency"),
		array("Cost", "70", "cost", "cost", "cost", "convert_to_currency"),
		array("Disposition", "110", "disposition", "", "", ""),
		array("Account", "100", "accountid", "first_name,last_name,number", "accounts", "build_concat_string"),
		array("Trunk", "70", "trunk_id", "name", "trunks", "get_field_name"),
		array("Rate Group", "70", "pricelist_id", "name", "pricelists", "get_field_name"),
		array("Call Type", "80", "calltype", "", "", ""),
		
          
//             array("Provider", "80", "provider_id", "number", "accounts", "get_field_name"),
           
           
          
                ));
        return $grid_field_arr;
    }

    function build_report_list_for_reseller() {
        // array(display name, width, db_field_parent_table,feidname, db_field_child_table,function name);
        $grid_field_arr = json_encode(array(
		    array("Date", "120", "callstart", "callstart", "callstart", "convert_GMT_to"),
		    array("CallerID", "100", "callerid", "", "", ""),
		    array("Called Number", "100", "callednum", "", "", ""),
		    array("Code", "50", "pattern", "pattern", "", "get_only_numeric_val"),
		    array("Destination", "120", "notes", "", "", ""),
		    array("Bill Seconds", "100", "billseconds", "", "", ""),
		    array("Debit", "50", "debit", "debit", "debit", "convert_to_currency"),
		    array("Cost", "50", "cost", "cost", "cost", "convert_to_currency"),
		    array("Disposition", "100", "disposition", "", "", ""),
		    array("Account", "109", "accountid", "first_name,last_name,number", "accounts", "build_concat_string"),
		    array("Rate Group", "100", "pricelist_id", "name", "pricelists", "get_field_name"),
// 		    array("Call Type", "120", "calltype", "", "", ""), 
		    
           ));
        return $grid_field_arr;
    }

    function build_report_list_for_provider() {
        // array(display name, width, db_field_parent_table,feidname, db_field_child_table,function name);
        $grid_field_arr = json_encode(array(array("Date", "100", "callstart", "callstart", "callstart", "convert_GMT_to"),
		  array("CallerID", "120", "callerid", "", "", ""),
		  array("Called Number", "130", "callednum", "", "", ""),
		  array("Code", "90", "pattern", "pattern", "", "get_only_numeric_val"),
		  array("Destination", "130", "notes", "", "", ""),
		  array("Bill Seconds", "120", "billseconds", "", "", ""),
		  array("Debit", "90", "debit", "debit", "debit", "convert_to_currency"),
// 		  array("Cost", "50", "cost", "", "", ""),
		  array("Disposition", "200", "disposition", "", "", ""),
		  array("Account", "129", "accountid", "first_name,last_name,number", "accounts", "build_concat_string"),
// 		  array("Rate Group", "130", "pricelist_id", "name", "pricelists", "get_field_name"),
// 		  array("Call Type", "120", "calltype", "", "", ""),
		  
	    ));
        return $grid_field_arr;
    }

    function build_grid_buttons() {
        $buttons_json = json_encode(array(array("Refresh", "reload", "/accounts/clearsearchfilter/")));
        return $buttons_json;
    }

    function build_grid_buttons_reseller() {
        $buttons_json = json_encode(array(array("Refresh", "reload", "/accounts/clearsearchfilter/")));
        return $buttons_json;
    }

    function build_report_list_for_user() {
        // array(display name, width, db_field_parent_table,feidname, db_field_child_table,function name);
        $grid_field_arr = json_encode(array(array("Date", "170", "callstart", "callstart", "callstart", "convert_GMT_to"),
            array("CallerID", "120", "callerid", "", "", ""),
            array("Called Number", "140", "callednum", "", "", ""),
//            array("Account Number", "120", "accountid", "number", "accounts", "get_field_name"),
            array("Bill Seconds", "120", "billseconds", "", "", ""),
            array("Disposition", "140", "disposition", "", "", ""),
            array("Debit", "110", "debit", "debit", "debit", "convert_to_currency"),
            array("Destination", "125", "notes", "", "", ""),
            array("Call Type", "120", "calltype", "", "", ""),
                ));
        return $grid_field_arr;
    }

    function build_payment_report_for_user() {
        $grid_field_arr = json_encode(array(
            array("Account", "150", "accountid", "first_name,last_name,number", "accounts", "build_concat_string"),
            array("Amount", "150", "credit", "credit", "credit", "convert_to_currency"),
//             array("Payment Type", "150", "type", "", "", ""),
            array("Payment By", "150", "payment_by", "payment_by", "payment_by", "get_payment_by"),
            array("Note", "150", "notes", "", "", ""),
                ));
        return $grid_field_arr;
    }
    function get_user_cdr_payment_form() {
        $form['forms'] = array("", array('id' => "cdr_payment_search"));
        $form['User Payment Report'] = array(
            array('', 'HIDDEN', 'ajax_search', '1', '', '', ''),
            array('', 'HIDDEN', 'advance_search', '1', '', '', ''),
	    array('Account', 'accountid', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'first_name,last_name,number', 'accounts', 'build_concat_dropdown', 'where_arr', array("reseller_id" => "0","type"=>"0", "deleted" => "0")),
            array('Balance ', 'INPUT', array('name' => 'credit[credit]', 'value' => '', 'size' => '20', 'maxlength' => '15', 'class' => "text field"), '', 'Tool tips info', '1', 'credit[credit-integer]', '', '', '', 'search_int_type', ''),
        );

        $form['button_search'] = array('name' => 'action', 'id' => "cusotmer_cdr_payment_search_btn", 'content' => 'Search', 'value' => 'save', 'type' => 'button', 'class' => 'ui-state-default float-right ui-corner-all ui-button');
        $form['button_reset'] = array('name' => 'action', 'id' => "id_reset", 'content' => 'Clear Search Filter', 'value' => 'cancel', 'type' => 'reset', 'class' => 'ui-state-default float-right ui-corner-all ui-button');

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
            array('TO Date', 'INPUT', array('name' => 'date[]', 'id' => 'commission_to_date', 'size' => '20', 'maxlength' => '15', 'class' => "text field "), '', 'tOOL TIP', '', 'end_date[end_date-date]'),
	    array('Account', 'accountid', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'first_name,last_name,number', 'accounts', 'build_concat_dropdown', 'where_arr', array("reseller_id" => "0","type"=>"1", "deleted" => "0")),
            array('Amount', 'INPUT', array('name' => 'amount[amount]', 'value' => '', 'size' => '20', 'maxlength' => '15', 'class' => "text field"), '', 'Tool tips info', '1', 'amount[amount-integer]', '', '', '', 'search_int_type', ''),
        );

        $form['button_search'] = array('name' => 'action', 'id' => "commission_search_btn", 'content' => 'Search', 'value' => 'save', 'type' => 'button', 'class' => 'ui-state-default float-right ui-corner-all ui-button');
        $form['button_reset'] = array('name' => 'action', 'id' => "id_reset", 'content' => 'Clear Search Filter', 'value' => 'cancel', 'type' => 'reset', 'class' => 'ui-state-default float-right ui-corner-all ui-button');

        return $form;
    }
    function get_provider_summary_search_form() {
        $form['forms'] = array(base_url().'reports/provider_summery_Report', array('id' => "provider_search_summary"));
        $form['Search Provider Report'] = array(
            array('', 'HIDDEN', 'ajax_search', '1', '', '', ''),
            array('', 'HIDDEN', 'advance_search', '1', '', '', ''),
            array('From Date', 'INPUT', array('name' => 'start_date', 'id' => 'provider_from_date', 'size' => '20', 'maxlength' => '15', 'class' => "text field "), '', 'tOOL TIP', '', 'start_date[start_date-date]'),
            array('TO Date', 'INPUT', array('name' => 'end_date', 'id' => 'provider_to_date', 'size' => '20', 'maxlength' => '15', 'class' => "text field "), '', 'tOOL TIP', '', 'end_date[end_date-date]'),
            array('Account Number', 'number', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'first_name,last_name,number', 'accounts', 'build_concat_dropdown', 'where_arr', array("type"=>"3", "deleted" => "0")),
        );

        $form['button_search'] = array('name' => 'action', 'id' => "search_providerreport", 'content' => 'Search', 'value' => 'save', 'type' => 'submit', 'class' => 'ui-state-default float-right ui-corner-all ui-button');
        $form['button_reset'] = array('name' => 'action', 'id' => "id_reset", 'content' => 'Clear Search Filter', 'value' => 'cancel', 'type' => 'reset', 'class' => 'ui-state-default float-right ui-corner-all ui-button');

        return $form;
    }

}

?>
