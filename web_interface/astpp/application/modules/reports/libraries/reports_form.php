<?php
###############################################################################
# ASTPP - Open Source VoIP Billing Solution
#
# Copyright (C) 2016 iNextrix Technologies Pvt. Ltd.
# Samir Doshi <samir.doshi@inextrix.com>
# ASTPP Version 3.0 and above
# License https://www.gnu.org/licenses/agpl-3.0.html
#
# This program is free software: you can redistribute it and/or modify
# it under the terms of the GNU Affero General Public License as
# published by the Free Software Foundation, either version 3 of the
# License, or (at your option) any later version.
# 
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU Affero General Public License for more details.
# 
# You should have received a copy of the GNU Affero General Public License
# along with this program.  If not, see <http://www.gnu.org/licenses/>.
###############################################################################
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Reports_form {
    function __construct() {
        $this->CI = & get_instance();
    }
    function get_customer_cdr_form() {
		$logintype = $this->CI->session->userdata('userlevel_logintype');
		 if($logintype != 1){
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
            array('Caller ID', 'INPUT', array('name' => 'callerid[callerid]', '', 'id' => 'first_name', 'size' => '15', 'class' => "text field "), '', 'tOOL TIP', '1', 'callerid[callerid-string]', '', '', '', 'search_string_type', ''),
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
            array('Call Type', 'calltype', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', '', '', '', 'set_calltype'),
            array('', 'HIDDEN', 'ajax_search', '1', '', '', ''),
            array('', 'HIDDEN', 'advance_search', '1', '', '', ''));
             }
		else
		{
		$form['forms'] = array("", array('id' => "cdr_customer_search"));
        $form['Search'] = array(
           array('From Date', 'INPUT', array('name' => 'callstart[]', 'id' => 'customer_cdr_from_date', 'size' => '20', 'class' => "text field "), '', 'tOOL TIP', '', 'start_date[start_date-date]'),
            array('To Date', 'INPUT', array('name' => 'callstart[]', 'id' => 'customer_cdr_to_date', 'size' => '20', 'class' => "text field "), '', 'tOOL TIP', '', 'end_date[end_date-date]'),
            array('Caller ID', 'INPUT', array('name' => 'callerid[callerid]', '', 'id' => 'first_name', 'size' => '15', 'class' => "text field "), '', 'tOOL TIP', '1', 'callerid[callerid-string]', '', '', '', 'search_string_type', ''),
            array('Called Number', 'INPUT', array('name' => 'callednum[callednum]', 'value' => '', 'size' => '20', 'class' => "text field "), '', 'Tool tips info', '1', 'callednum[callednum-string]', '', '', '', 'search_string_type', ''),
             array('Code ', 'INPUT', array('name' => 'pattern[pattern]', 'value' => '', 'size' => '20', 'class' => "text field "), '', 'Tool tips info', '1', 'pattern[pattern-string]', '', '', '', 'search_string_type', ''),
            array('Destination ', 'INPUT', array('name' => 'notes[notes]', 'value' => '', 'size' => '20', 'class' => "text field "), '', 'Tool tips info', '1', 'notes[notes-string]', '', '', '', 'search_string_type', ''),
            array('Duration ', 'INPUT', array('name' => 'billseconds[billseconds]', 'value' => '', 'size' => '20', 'class' => "text field "), '', 'Tool tips info', '1', 'billseconds[billseconds-integer]', '', '', '', 'search_int_type', ''),
	    array('Debit ', 'INPUT', array('name' => 'debit[debit]', 'value' => '', 'size' => '20', 'class' => "text field "), '', 'Tool tips info', '1', 'debit[debit-integer]', '', '', '', 'search_int_type', ''),
            array('Cost ', 'INPUT', array('name' => 'cost[cost]', 'value' => '', 'size' => '20', 'class' => "text field "), '', 'Tool tips info', '1', 'cost[cost-integer]', '', '', '', 'search_int_type', ''),
	    array('Disposition', 'disposition', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', '', '', '', 'set_despostion'),
            array('Account', 'accountid', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'IF(`deleted`=1,concat( first_name, " ", last_name, " ", "(", number, ")^" ),concat( first_name, " ", last_name, " ", "(", number, ")" )) as number', 'accounts', 'build_dropdown_deleted', 'where_arr', array("reseller_id" => "0","type"=>"GLOBAL")),
 
	  //array('Trunk', 'trunk_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'IF(`status`=2, concat(name,"","^"),name) as name', 'trunks', 'build_dropdown_deleted', '', array("status" => "1")),

           array('Rate Group', 'pricelist_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'IF(`status`=2, concat(name,"","^"),name) as name', 'pricelists', 'build_dropdown_deleted', 'where_arr', array("reseller_id" => "0")),
            array('Call Type', 'calltype', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', '', '', '', 'set_calltype'),
            array('', 'HIDDEN', 'ajax_search', '1', '', '', ''),
            array('', 'HIDDEN', 'advance_search', '1', '', '', ''));
		
	  }  

	
	$form['display_in']=array('name' => 'search_in',"id"=>"search_in","function"=>"search_report_in", "content"=>"Display records in",'label_class' => "search_label col-md-3 no-padding","dropdown_class"=>"form-control","label_style"=>"font-size:13px;","dropdown_style"=>"background: #ddd; width: 21% !important;");
	
	/****************************************/
        $form['button_search'] = array('name' => 'action', 'id' => "cusotmer_cdr_search_btn", 'content' => 'Search', 'value' => 'save', 'type' => 'button', 'class' => 'btn btn-line-parrot pull-right');
        $form['button_reset'] = array('name' => 'action', 'id' => "id_reset", 'content' => 'Clear', 'value' => 'cancel', 'type' => 'reset', 'class' => 'btn btn-line-sky pull-right margin-x-10');

        return $form;
    }
   
    function get_reseller_cdr_form() {
        $form['forms'] = array("", array('id' => "cdr_reseller_search"));
        $form['Search'] = array(
            array('From Date', 'INPUT', array('name' => 'callstart[]', 'id' => 'customer_cdr_from_date', 'size' => '20', 'class' => "text field "), '', 'tOOL TIP', '', 'number[number-string]'),
            array('To Date', 'INPUT', array('name' => 'callstart[]', 'id' => 'customer_cdr_to_date', 'size' => '20', 'class' => "text field "), '', 'tOOL TIP', '', 'number[number-string]'),
            array('Caller ID', 'INPUT', array('name' => 'callerid[callerid]', '', 'id' => 'first_name', 'size' => '15', 'class' => "text field "), '', 'tOOL TIP', '1', 'callerid[callerid-string]', '', '', '', 'search_string_type', ''),
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
	
	$form['display_in']=array('name' => 'search_in',"id"=>"search_in","function"=>"search_report_in", "content"=>"Display records in &nbsp;&nbsp;",'label_class' => "search_label col-md-3 no-padding","dropdown_class"=>"form-control","label_style"=>"font-size:13px;text-align:right;","dropdown_style"=>"background: #ddd; width: 23% !important;");
	
	/****************************************/
        $form['button_search'] = array('name' => 'action', 'id' => "reseller_cdr_search_btn", 'content' => 'Search', 'value' => 'save', 'type' => 'button', 'class' => 'btn btn-line-parrot pull-right');
        $form['button_reset'] = array('name' => 'action', 'id' => "id_reset", 'content' => 'Clear', 'value' => 'cancel', 'type' => 'reset', 'class' => 'btn btn-line-sky pull-right margin-x-10');

        return $form;
    }

    function get_provider_cdr_form() {
        $form['forms'] = array("", array('id' => "cdr_provider_search"));
        $form['Search'] = array(
            array('From Date', 'INPUT', array('name' => 'callstart[]', 'id' => 'customer_cdr_from_date', 'size' => '20', 'class' => "text field "), '', 'tOOL TIP', '', 'number[number-string]'),
            array('To Date', 'INPUT', array('name' => 'callstart[]', 'id' => 'customer_cdr_to_date', 'size' => '20', 'class' => "text field "), '', 'tOOL TIP', '', 'number[number-string]'),
            array('Caller ID', 'INPUT', array('name' => 'callerid[callerid]', '', 'id' => 'first_name', 'size' => '15', 'class' => "text field "), '', 'tOOL TIP', '1', 'callerid[callerid-string]', '', '', '', 'search_string_type', ''),
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
	$form['display_in']=array('name' => 'search_in',"id"=>"search_in","function"=>"search_report_in", "content"=>"Display records in",'label_class' => "search_label col-md-3 no-padding","dropdown_class"=>"form-control","label_style"=>"font-size:13px;","dropdown_style"=>"background: #ddd; width: 21% !important;");
	
	/****************************************/
        $form['button_search'] = array('name' => 'action', 'id' => "provider_cdr_search_btn", 'content' => 'Search', 'value' => 'save', 'type' => 'button', 'class' => 'btn btn-line-parrot pull-right');
        $form['button_reset'] = array('name' => 'action', 'id' => "id_reset", 'content' => 'Clear', 'value' => 'cancel', 'type' => 'reset', 'class' => 'btn btn-line-sky pull-right margin-x-10');

        return $form;
    }

/******
ASTPP  3.0 
Addrecording field in grid
******/
    function build_report_list_for_customer() {
		$logintype = $this->CI->session->userdata('userlevel_logintype');
		 if($logintype != 1){
		$account_info = $accountinfo = $this->CI->session->userdata('accountinfo');
		$currency_id=$account_info['currency_id'];
		$currency=$this->CI->common->get_field_name('currency', 'currency', $currency_id);
             
             $recording=array();
             $account_data = $this->CI->session->userdata("accountinfo");
	     if($account_data['type'] == 1){
                $recording=array("Recording", "127", "recording", "", "", ""); 
             }
        $grid_field_arr = json_encode(array(
		array(gettext("Date"), "100", "callstart","callstart", "callstart", "convert_GMT_to","","true","center"),
		array("Caller ID", "120", "callerid", "", "", "","","true","center"),
		array("Called Number", "103", "callednum", "", "", "","","true","center"),
		array("Code", "71", "pattern", "pattern", "", "get_only_numeric_val","","true","center"),
		array("Destination", "90", "notes", "", "", "","","true","center"),
		array("Duration", "80", "billseconds", "customer_cdr_list_search", "billseconds", "convert_to_show_in","","true","center"),
		array("Debit($currency)", "75", "debit", "debit", "debit", "convert_to_currency","","true","right"),
		array("Cost($currency)", "75", "cost", "cost", "cost", "convert_to_currency","","true","right"),
		array("Disposition", "150", "disposition", "", "", "","","true","center"),
		array("Account", "110", "accountid", "first_name,last_name,number", "accounts", "build_concat_string","","true","center"),
        array("Trunk", "90", "trunk_id", "name", "trunks", "get_field_name","","true","center"),
		array("Rate Group", "90", "pricelist_id", "name", "pricelists", "get_field_name","","true","center"),
		array("Call Type", "112", "calltype", "", "", ""),
                $recording,
                ));
                
                }
                else{
		$account_info = $accountinfo = $this->CI->session->userdata('accountinfo');
		$currency_id=$account_info['currency_id'];
		$currency=$this->CI->common->get_field_name('currency', 'currency', $currency_id);
		$recording=array("Recording", "127", "recording", "", "", "");			
		$grid_field_arr = json_encode(array(
		array("Date", "100", "callstart","callstart", "callstart", "convert_GMT_to","","true","center"),
		array("Caller ID", "100", "callerid", "", "", "","","true","center"),
		array("Called Number", "103", "callednum", "", "", "","","true","center"),
		array("Code", "71", "pattern", "pattern", "", "get_only_numeric_val","","true","center"),
		array("Destination", "90", "notes", "", "", "","","true","center"),
		array("Duration", "80", "billseconds", "customer_cdr_list_search", "billseconds", "convert_to_show_in","","true","center"),
		array("Debit($currency)", "75", "debit", "debit", "debit", "convert_to_currency","","true","right"),
		array("Cost($currency)", "75", "cost", "cost", "cost", "convert_to_currency","","true","right"),
		array("Disposition", "130", "disposition", "", "", "","","true","center"),
		array("Account", "110", "accountid", "first_name,last_name,number", "accounts", "build_concat_string","","true","center"),
        //array("Trunk", "90", "trunk_id", "name", "trunks", "get_field_name","","true","center"),
		array("Rate Group", "90", "pricelist_id", "name", "pricelists", "get_field_name","","true","center"),
		array("Call Type", "112", "calltype", "", "", ""),
                $recording,
                ));
                
         }
                
        return $grid_field_arr;
    }
/******************************/

    function build_report_list_for_reseller() {
		
		$account_info = $accountinfo = $this->CI->session->userdata('accountinfo');
$currency_id=$account_info['currency_id'];
$currency=$this->CI->common->get_field_name('currency', 'currency', $currency_id);
		
        // array(display name, width, db_field_parent_table,feidname, db_field_child_table,function name);
        $grid_field_arr = json_encode(array(
		    array("Date", "100", "callstart", "callstart", "callstart", "convert_GMT_to","","true","center"),
		    array("Caller ID", "100", "callerid", "", "", "","","true","center"),
		    array("Called Number", "120", "callednum", "", "", "","","true","center"),
		    array("Code", "80", "pattern", "pattern", "", "get_only_numeric_val","","true","center"),
		    array("Destination", "120", "notes", "", "", "","","true","center"),
		    array("Duration", "107", "billseconds", "reseller_cdr_list_search", "billseconds", "convert_to_show_in","","true","center"),
		    array("Debit($currency)", "105", "debit", "debit", "debit", "convert_to_currency","","true","right"),
		    array("Cost($currency)", "104", "cost", "cost", "cost", "convert_to_currency","","true","right"),
		    array("Disposition", "100", "disposition", "", "", "","","true","center"),
		    array("Account", "120", "accountid", "first_name,last_name,number", "accounts", "build_concat_string","","true","center"),
		    array("Rate Group", "90", "pricelist_id", "name", "pricelists", "get_field_name","","true","center"),
 		    array("Call Type", "120", "calltype", "", "", "","","true","center"), 
		    
           ));
        return $grid_field_arr;
    }

    function build_report_list_for_provider() {

$account_info = $accountinfo = $this->CI->session->userdata('accountinfo');
$currency_id=$account_info['currency_id'];
$currency=$this->CI->common->get_field_name('currency', 'currency', $currency_id);
		
        // array(display name, width, db_field_parent_table,feidname, db_field_child_table,function name);
        $grid_field_arr = json_encode(array(array("Date", "100", "callstart", "callstart", "callstart", "convert_GMT_to","","true","center"),
		  array("Caller ID", "120", "callerid", "", "", "","","true","center"),
		  array("Called Number", "160", "callednum", "", "", "","","true","center"),
		  array("Code", "117", "pattern", "pattern", "", "get_only_numeric_val","","true","center"),
		  array("Destination", "130", "notes", "", "", "","","true","center"),
		  array("Duration", "110", "billseconds", "provider_cdr_list_search", "billseconds", "convert_to_show_in","","true","center"),
		  array("Cost($currency)", "150", "provider_call_cost", "provider_cost", "provider_cost", "convert_to_currency","","true","right"),
		  array("Disposition", "200", "disposition", "", "", "","","true","center"),
		  array("Account", "181", "provider_id", "first_name,last_name,number", "accounts", "build_concat_string","","true","center"),
	    ));
        return $grid_field_arr;
    }

    function build_grid_customer() {
        $buttons_json = json_encode(array(array("Export","btn btn-xing" ," fa fa-download fa-lg", "button_action", "/reports/customerReport_export/", 'single')));
        return $buttons_json;
    }
/**
ASTPP  3.0 
For Customer CDRs export
**/

    function build_grid_buttons_user() {
        $buttons_json = json_encode(array(array("Export","btn btn-xing" ," fa fa-download fa-lg", "button_action", "/user/user_report_export/", 'single')));
        return $buttons_json;
    }
/*****************************************************************/
    function build_grid_buttons_reseller() {
        $buttons_json = json_encode(array( array("Export","btn btn-xing" ," fa fa-download fa-lg", "button_action", "/reports/resellerReport_export/", 'single')));
        return $buttons_json;
    }
    function build_grid_buttons_provider() {
        $buttons_json = json_encode(array(array("Export","btn btn-xing" ," fa fa-download fa-lg", "button_action", "/reports/providerReport_export/", 'single')));
        return $buttons_json;
    }
    
    function build_report_list_for_user($accounttype = 'customer') {
		
		$account_info = $accountinfo = $this->CI->session->userdata('accountinfo');
		$currency_id=$account_info['currency_id'];
		$currency=$this->CI->common->get_field_name('currency', 'currency', $currency_id);			
		
		
        if($accounttype == 'customer' || $accounttype =='reseller'){
            $cost_array=array("Debit($currency)", "100", "debit", "debit", "debit", "convert_to_currency","","true","right");
        }
        if(strtolower($accounttype)=='provider'){
            $cost_array=array("Debit($currency)", "140", "cost", "cost", "cost", "convert_to_currency");
        }
     // array(display name, width, db_field_parent_table,feidname, db_field_child_table,function name);
        $grid_field_arr = json_encode(array(
        array("Date", "130", "callstart", "callstart", "callstart", "convert_GMT_to","","true","center"),
            array("Caller ID", "100", "callerid", "", "", "","","true","center"),
            array("Called Number", "120", "callednum", "", "", "","","true","center"),
            array("Destination", "135", "notes", "", "", "","","true","center"),
//            array("Account Number", "120", "accountid", "number", "accounts", "get_field_name"),
            array("Duration", "120", "billseconds", "user_cdrs_report_search", "billseconds", "convert_to_show_in","","true","center"),
            $cost_array,
            array("Disposition", "160", "disposition", "", "", "","","true","center"),
            array("Call Type", "140", "calltype", "", "", "","","true","center"),
                ));
        return $grid_field_arr;
    }

/******
ASTPP  3.0 
Payment to refill
******/
    function build_refill_report_for_admin() {
		
		  $account_info = $accountinfo = $this->CI->session->userdata('accountinfo');
    $currency_id=$account_info['currency_id'];
     $currency=$this->CI->common->get_field_name('currency', 'currency', $currency_id);
      $grid_field_arr = json_encode(array(
            array(gettext("Date"), "225", "payment_date", "", "", "","","true","center"),
            array(gettext("Account"), "240", "accountid", "first_name,last_name,number", "accounts", "build_concat_string","","true","center"),
            array("Amount($currency)", "250", "credit", "credit", "credit", "convert_to_currency","","true","right"),
            array(gettext("Refill By"), "230", "payment_by", "payment_by", "payment_by", "get_refill_by","","true","center"),
            array(gettext("Note"), "327", "notes", "", "", "","","true","center")
                ));
        return $grid_field_arr;
    }
    function build_refillreport_buttons() {
        $buttons_json = json_encode(array(array("Export","btn btn-xing" ," fa fa-download fa-lg", "button_action", "/reports/refillreport_export/", 'single')));
        return $buttons_json;
    }
    function build_search_refill_report_for_admin() {
         $accountinfo=$this->CI->session->userdata('accountinfo');
         $reseller_id=$accountinfo['type']==1 ? $accountinfo['id'] : 0 ;
         $form['forms'] = array("", array('id' => "cdr_refill_search"));
             $account_data = $this->CI->session->userdata("accountinfo");
             $acc_arr= array('Account', 'accountid', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'IF(`deleted`=1,concat( first_name, " ", last_name, " ", "(", number, ")^" ),concat( first_name, " ", last_name, " ", "(", number, ")" )) as number', 'accounts', 'build_dropdown_deleted', 'where_arr', array("reseller_id" => $reseller_id,"type"=>"0,1,3"));
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
        $form['Search'] = array(
        array('From Date', 'INPUT', array('name' => 'payment_date[]', 'id' => 'refill_from_date', 'size' => '20',
 'class' => "text field "), '', 'tOOL TIP', '', 'payment_date[payment_date-date]'),
            array('To Date', 'INPUT', array('name' => 'payment_date[]', 'id' => 'refill_to_date', 'size' => '20', 'class' => "text field "), '', 'tOOL TIP', '', 'payment_date[payment_date-date]'),
            $acc_arr,
//	    array('Account', 'accountid', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'first_name,last_name,number', 'accounts', 'build_concat_dropdown', 'where_arr', array("reseller_id" => "0","type"=>"0")), 
            array('Amount ', 'INPUT', array('name' => 'credit[credit]', 'value' => '', 'size' => '20', 'class' => "text field"), '', 'Tool tips info', '1', 'credit[credit-integer]', '', '', '', 'search_int_type', ''),
           // array('', 'HIDDEN', 'ajax_search', '1', '', '', ''),array('', 'HIDDEN', 'advance_search', '1', '', '', ''),
            array('', 'HIDDEN', 'ajax_search', '1', '', '', ''),array('', 'HIDDEN', 'advance_search', '1', '', '', ''));

        $form['button_search'] = array('name' => 'action', 'id' => "cusotmer_cdr_refill_search_btn", 'content' => 'Search', 'value' => 'save', 'type' => 'button', 'class' => 'btn btn-line-parrot pull-right');
        $form['button_reset'] = array('name' => 'action', 'id' => "id_reset", 'content' => 'Clear', 'value' => 'cancel', 'type' => 'reset', 'class' => 'btn btn-line-sky pull-right margin-x-10');
//echo '<pre>'; print_r($form); exit;
        return $form;
    }
/***************************/
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
/******
ASTPP  3.0 
Payment to refill
******/
        $form['User Refill Report'] = array(
/*************************/
            array('', 'HIDDEN', 'ajax_search', '1', '', '', ''),
            array('', 'HIDDEN', 'advance_search', '1', '', '', ''),
            array('From Date', 'INPUT', array('name' => 'date[]', 'id' => 'commission_from_date', 'size' => '20', 'class' => "text field "), '', 'tOOL TIP', '', 'start_date[start_date-date]'),
            array('To Date', 'INPUT', array('name' => 'date[]', 'id' => 'commission_to_date', 'size' => '20',  'class' => "text field "), '', 'tOOL TIP', '', 'end_date[end_date-date]'),
	    array('Account', 'accountid', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'first_name,last_name,number', 'accounts', 'build_concat_dropdown', 'where_arr', array("reseller_id" => "0","type"=>"1", "deleted" => "0")),
            array('Amount', 'INPUT', array('name' => 'amount[amount]', 'value' => '', 'size' => '20',  'class' => "text field"), '', 'Tool tips info', '1', 'amount[amount-integer]', '', '', '', 'search_int_type', ''),
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
           array('Account', 'provider_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'IF(`deleted`=1,concat( first_name, " ", last_name, " ", "(", number, ")^" ),concat( first_name, " ", last_name, " ", "(", number, ")" )) as number', 'accounts', 'build_dropdown_deleted', 'where_arr', array("reseller_id" => "0","type"=>"3")),
           array('Code ', 'INPUT', array('name' => 'pattern[pattern]', 'value' => '', 'size' => '20',  'class' => "text field "), '', 'Tool tips info', '1', 'pattern[pattern-string]', '', '', '', 'search_string_type', ''),
            
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
	    array('Account', 'reseller_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'IF(`deleted`=1,concat( first_name, " ", last_name, " ", "(", number, ")^" ),concat( first_name, " ", last_name, " ", "(", number, ")" )) as number', 'accounts', 'build_dropdown_deleted', 'where_arr', array("reseller_id" => "0","type"=>"1")),
           array('Code ', 'INPUT', array('name' => 'pattern[pattern]', 'value' => '', 'size' => '20',  'class' => "text field "), '', 'Tool tips info', '1', 'pattern[pattern-string]', '', '', '', 'search_string_type', ''),
            
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
            array('Code ', 'INPUT', array('name' => 'pattern[pattern]', 'value' => '', 'size' => '20', 'class' => "text field "), '', 'Tool tips info', '1', 'pattern[pattern-string]', '', '', '', 'search_string_type', ''),
            
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
/*********
ASTPP  3.0 .1
Charges History
*********/
    function build_charge_list_for_admin() {
	$account_info = $accountinfo = $this->CI->session->userdata('accountinfo');
	$currency_id=$account_info['currency_id'];
	$currency=$this->CI->common->get_field_name('currency', 'currency', $currency_id);
     if ($this->CI->session->userdata("logintype") == '1') {
        $grid_field_arr = json_encode(array(
            array(gettext("Created Date"), "120", "created_date", "", "", "","","true","center"),
            array(gettext("Invoice Number"), "120", "created_date", "", "", "","","true","center"),
    	    array(gettext("Account"), "120", "accountid", "first_name,last_name,number", "accounts", "build_concat_string","","true","center"),
//            array("Reseller", "120", "reseller_id", "first_name,last_name,number", "accounts", "reseller_select_value"),
            array(gettext("Charge Type"), "120", "item_type", "", "", "","","true","center"),
            array("Before Balance<br/>($currency)", "120", "before_balance", "before_balance", "before_balance", "convert_to_currency","","true","right"),
            array("Debit (-)<br/>($currency)", "110", "debit", "debit", "debit", "convert_to_currency","","true","right"),
            array("Credit (+)<br/>($currency)", "110", "credit", "credit", "credit", "convert_to_currency","","true","right"),
	    array("After Balance<br/>($currency)", "120", "after_balance", "after_balance", "after_balance", "convert_to_currency","","true","right"),	
            array(gettext("Description"), "300", "description", "", "", "","","true","center"),
                ));
	}else{
        $grid_field_arr = json_encode(array(
            array("Created Date", "120", "created_date", "", "", "","","true","center"),
            array("Invoice Number", "120", "created_date", "", "", "","","true","center"),
   	    array("Account", "120", "accountid", "first_name,last_name,number", "accounts", "build_concat_string","","true","center"),
            array("Charge Type", "120", "item_type", "", "", "","","true","center"),
            array("Before Balance<br/>($currency)", "120", "before_balance", "before_balance", "before_balance", "convert_to_currency","","true","right"),
            array("Debit (-)<br/>($currency)", "110", "debit", "debit", "debit", "convert_to_currency","","true","right"),
            array("Credit (+)<br/>($currency)", "110", "credit", "credit", "credit", "convert_to_currency","","true","right"),
	    array("After Balance<br/>($currency)", "120", "after_balance", "after_balance", "after_balance", "convert_to_currency","","true","right"),	
            array("Description", "300", "description", "", "", "","","true","center"),
                ));

	}
        return $grid_field_arr;
    }
    function get_charges_search_form() {
        $accountinfo=$this->CI->session->userdata('accountinfo');
        $reseller_id=$accountinfo['type']==1 ? $accountinfo['id'] : 0;
        $form['forms'] = array("", array('id' => "charges_search"));
        $form['Search'] = array(
            array('From Date', 'INPUT', array('name' => 'created_date[]', 'id' => 'charge_from_date', 'size' => '20', 'class' => "text field "), '', 'tOOL TIP', '', 'start_date[start_date-date]'),
            array('To Date', 'INPUT', array('name' => 'created_date[]', 'id' => 'charge_to_date', 'size' => '20', 'class' => "text field "), '', 'tOOL TIP', '', 'end_date[end_date-date]'),
	    array('Account', 'accountid', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'IF(`deleted`=1,concat( first_name, " ", last_name, " ", "(", number, ")^" ),concat( first_name, " ", last_name, " ", "(", number, ")" )) as number', 'accounts', 'build_dropdown_deleted', 'where_arr', array("reseller_id" => $reseller_id,"type"=>"GLOBAL")),
	    array('Debit ', 'INPUT', array('name' => 'debit[debit]', 'value' => '', 'size' => '20', 'class' => "text field "), '', 'Tool tips info', '1', 'debit[debit-integer]', '', '', '', 'search_int_type', ''),
	    array('Credit ', 'INPUT', array('name' => 'credit[credit]', 'value' => '', 'size' => '20', 'class' => "text field "), '', 'Tool tips info', '1', 'credit[credit-integer]', '', '', '', 'search_int_type', ''),

  	    array('', 'HIDDEN', 'ajax_search', '1', '', '', ''),    
            array('', 'HIDDEN', 'advance_search', '1', '', '', '')
        );
        $form['button_search'] = array('name' => 'action', 'id' => "charges_search_btn", 'content' => 'Search', 'value' => 'save', 'type' => 'button', 'class' => 'btn btn-line-parrot pull-right');
        $form['button_reset'] = array('name' => 'action', 'id' => "id_reset", 'content' => 'Clear', 'value' => 'cancel', 'type' => 'reset', 'class' => 'btn btn-line-sky pull-right margin-x-10');

        return $form;
    }
    /*******************************/
    /*********
        ASTPP  3.0
        Charges History
    *********/
    function build_charge_list_for_customer(){
		$account_info = $accountinfo = $this->CI->session->userdata('accountinfo');
		$currency_id=$account_info['currency_id'];
		$currency=$this->CI->common->get_field_name('currency', 'currency', $currency_id);	
		
        $grid_field_arr = json_encode(array(
            array("Created Date", "100", "created_date", "", "", "","","true","center"),
            array("Invoice Number", "110", "created_date", "", "", "","","true","center"),
            array("Charge Type", "100", "item_type", "", "", "","","true","center"),
            array("Before Balance<br/>($currency)", "120", "before_balance", "before_balance", "before_balance", "convert_to_currency","","true","right"),
            array("Debit (-)<br/>($currency)", "110", "debit", "debit", "debit", "convert_to_currency","","true","right"),
            array("Credit (+)<br/>($currency)", "110", "credit", "credit", "credit", "convert_to_currency","","true","right"),
	    array("After Balance<br/>($currency)", "120", "after_balance", "after_balance", "after_balance", "convert_to_currency","","true","right"),
            array("Description", "270", "description", "", "", "","","true","center"),
                ));
        return $grid_field_arr;
    }
    /*********
        ASTPP  3.0
        Refill History
    *********/
    function build_refillreport_for_customer(){
		
		$account_info = $accountinfo = $this->CI->session->userdata('accountinfo');
		$currency_id=$account_info['currency_id'];
		$currency=$this->CI->common->get_field_name('currency', 'currency', $currency_id);		
		
         $grid_field_arr = json_encode(array(
            array("Date", "225", "payment_date", "", "", "","","true","center"),
            array("Amount($currency)", "250", "credit", "credit", "credit", "convert_to_currency","","true","right"),
            array("Refill By", "230", "payment_by", "payment_by", "payment_by", "get_refill_by","","true","center"),
            array("Note", "325", "notes", "", "", "","","true","center")
                ));
        return $grid_field_arr;
    }


}

?>
