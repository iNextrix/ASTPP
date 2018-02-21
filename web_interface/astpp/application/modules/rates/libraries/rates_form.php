<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class rates_form {
    function __construct($library_name = '') {
        $this->CI = & get_instance();
    }
    function get_termination_form_fields() {
        $form['forms'] = array(base_url() . 'rates/terminationrates_save/', array('id' => 'termination_form', 'method' => 'POST', 'name' => 'termination_form'));
        $form['Rate Information'] = array(
        array('', 'HIDDEN', array('name' => 'id'), '', '', '', ''),
            array('Trunk', 'trunk_id', 'SELECT', '', 'dropdown', 'tOOL TIP', 'Please Enter account number', 'id', 'name', 'trunks', 'build_dropdown', 'where_arr', array("status" => "0")),     
            array('Code', 'INPUT', array('name' => 'pattern', 'size' => '20', 'maxlength' => '15', 'class' => "text field medium"), 'trim|required|min_length[1]|numeric|max_length[25]|xss_clean', 'tOOL TIP', ''),
            array('Destination', 'INPUT', array('name' => 'comment', 'size' => '20', 'maxlength' => '80', 'class' => "text field medium"), 'tOOL TIP', ''),            
            array('Strip', 'INPUT', array('name' => 'strip', 'size' => '20', 'maxlength' => '40', 'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('Prepend', 'INPUT', array('name' => 'prepend', 'size' => '20', 'maxlength' => '40', 'class' => "text field medium"), 'trim|xss_clean', 'tOOL TIP', ''),
            array('Status', 'status', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', '', '', '', 'set_status'),
            
        
);
        $form['Billing Information'] = array(
            array('Connect Cost', 'INPUT', array('name' => 'connectcost', 'size' => '20', 'maxlength' => '10', 'class' => "text field medium"), 'trim|numeric|currency_decimal|xss_clean', 'tOOL TIP', ''),
            array('Included Seconds', 'INPUT', array('name' => 'includedseconds', 'size' => '20', 'maxlength' => '4', 'class' => "text field medium"), 'trim|numeric|xss_clean', 'tOOL TIP', ''),
             array('Per Minute Cost', 'INPUT', array('name' => 'cost', 'size' => '20', 'maxlength' => '10', 'class' => "text field medium"), 'trim|numeric|currency_decimal|xss_clean', 'tOOL TIP', ''),
            array('Increment', 'INPUT', array('name' => 'inc', 'size' => '20', 'maxlength' => '4', 'class' => "text field medium"), 'trim|numeric|xss_clean', 'tOOL TIP', ''),
            array('Precedence', 'INPUT', array('name' => 'precedence', 'size' => '20', 'maxlength' => '4', 'class' => "text field medium"), 'trim|numeric|xss_clean', 'tOOL TIP', ''));
	
        $form['button_cancel'] = array('name' => 'action', 'content' => 'Cancel', 'value' => 'cancel', 'type' => 'button', 'class' => 'btn btn-line-sky margin-x-10', 'onclick' => 'return redirect_page(\'NULL\')');
        $form['button_save'] = array('name' => 'action', 'content' => 'Save', 'value' => 'save', 'id' => 'submit', 'type' => 'button', 'class' => 'btn btn-line-parrot');

        return $form;
    }

    function get_inbound_form_fields() {
	 $logintype=$this->CI->session->userdata('userlevel_logintype');
        $trunk=null;
        if($logintype !=1)
	  $trunk = array('Force Trunk', 'trunk_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'name', 'trunks', 'build_dropdown', 'where_arr', array("status" => "0"));
        $form['forms'] = array(base_url() . 'rates/origination_save/', array('id' => 'origination_form', 'method' => 'POST', 'name' => 'origination_form'));
        $form['Rate Information'] = array(
            array('', 'HIDDEN', array('name' => 'id'), '', '', '', ''),
            array('Rate Group', 'pricelist_id', 'SELECT', '','', 'tOOL TIP', 'Please Enter account number', 'id', 'name', 'pricelists', 'build_dropdown', 'where_arr', array("status" => "0",'reseller_id'=>0)),        
            array('Code', 'INPUT', array('name' => 'pattern', 'size' => '20', 'maxlength' => '15', 'class' => "text field medium"), 'trim|required|min_length[1]|numeric|max_length[25]|xss_clean', 'tOOL TIP', ''),
            array('Destination', 'INPUT', array('name' => 'comment', 'size' => '20', 'maxlength' => '80', 'class' => "text field medium"), 'tOOL TIP', ''),
            array('Precedence', 'INPUT', array('name' => 'precedence', 'size' => '20', 'maxlength' => '4', 'class' => "text field medium"), 'trim|numeric|xss_clean', 'tOOL TIP', ''),
            array('Status', 'status', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', '', '', '', 'set_status'),
        
);
        $form['Billing Information'] = array(
            array('Connect Cost', 'INPUT', array('name' => 'connectcost', 'size' => '20', 'maxlength' => '10', 'class' => "text field medium"), 'trim|numeric|currency_decimal|xss_clean', 'tOOL TIP', ''),
            array('Included Seconds', 'INPUT', array('name' => 'includedseconds', 'size' => '20', 'maxlength' => '4', 'class' => "text field medium"), 'trim|numeric|xss_clean', 'tOOL TIP', ''),
             array('Per Minute Cost', 'INPUT', array('name' => 'cost', 'size' => '20', 'maxlength' => '10', 'class' => "text field medium"), 'trim|numeric|currency_decimal|xss_clean', 'tOOL TIP', ''),
            array('Increment', 'INPUT', array('name' => 'inc', 'size' => '20', 'maxlength' => '4', 'class' => "text field medium"), 'trim|numeric|xss_clean', 'tOOL TIP', ''),
            $trunk,
            
        
);

        $form['button_cancel'] = array('name' => 'action', 'content' => 'Cancel', 'value' => 'cancel', 'type' => 'button', 'class' => 'btn btn-line-sky margin-x-10', 'onclick' => 'return redirect_page(\'NULL\')');
        $form['button_save'] = array('name' => 'action', 'content' => 'Save', 'value' => 'save', 'id' => 'submit', 'type' => 'button', 'class' => 'btn btn-line-parrot');

        return $form;
    }
	
    function get_termination_search_form() {
        $form['forms'] = array("", array('id' => "termination_search"));
        $form['Search'] = array(
            
            array('Code', 'INPUT', array('name' => 'pattern[pattern]', '', 'size' => '20', 'class' => "text field"), '', 'tOOL TIP', '1', 'pattern[pattern-string]', '', '', '', 'search_string_type', ''),
            array('Destination', 'INPUT', array('name' => 'comment[comment]', '', 'size' => '20', 'class' => "text field "), '', 'tOOL TIP', '1', 'comment[comment-string]', '', '', '', 'search_string_type', ''),
            array('Connect Cost', 'INPUT', array('name' => 'connectcost[connectcost]', 'value' => '', 'size' => '20', 'class' => "text field "), '', 'Tool tips info', '1', 'connectcost[connectcost-integer]', '', '', '', 'search_int_type', ''),
            array('Included Seconds', 'INPUT', array('name' => 'includedseconds[includedseconds]', 'value' => '', 'size' => '20', 'class' => "text field "), '', 'Tool tips info', '1', 'includedseconds[includedseconds-integer]', '', '', '', 'search_int_type', ''),
            array('Per Minute Cost', 'INPUT', array('name' => 'cost[cost]', 'value' => '', 'size' => '20', 'class' => "text field "), '', 'Tool tips info', '1', 'cost[cost-integer]', '', '', '', 'search_int_type', ''),
	    array('Increment', 'INPUT', array('name' => 'inc[inc]', '', 'size' => '20', 'class' => "text field "), '', 'tOOL TIP', '1', 'inc[inc-integer]', '', '', '', 'search_int_type', ''),
                array('Prepend', 'INPUT', array('name' => 'prepend[prepend]', '', 'size' => '20', 'class' => "text field "), '', 'tOOL TIP', '1', 'prepend[prepend-string]', '', '', '', 'search_string_type', ''),
            array('Trunk', 'trunk_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'name', 'trunks', 'build_dropdown', 'where_arr', array("status" => "0")),array('', 'HIDDEN', 'ajax_search', '1', '', '', ''),
           array('Status', 'status', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', '', '', '', 'set_search_status', '', ''),
            
		 array('', 'HIDDEN', 'advance_search', '1', '', '', '')
            
            
        );

        $form['button_search'] = array('name' => 'action', 'id' => "termination_search_btn", 'content' => 'Search', 'value' => 'save', 'type' => 'button', 'class' => 'btn btn-line-parrot pull-right');
        $form['button_reset'] = array('name' => 'action', 'id' => "id_reset", 'content' => 'Clear', 'value' => 'cancel', 'type' => 'reset', 'class' => 'btn btn-line-sky pull-right margin-x-10');

        return $form;
    }

    
    function termination_batch_update_form() {
        $form['forms'] = array("rates/terminationrates_batch_update/", array('id' => "termination_batch_update"));
        $form['Batch Update'] = array(
	    array('Connect Cost', 'INPUT', array('name' => 'connectcost[connectcost]','id'=>'connectcost', 'value' => '', 'size' => '20', 'maxlength' => '15', 'class' => "text field "), '', 'Tool tips info', '1', array('name'=>'connectcost[operator]','class'=>'update_drp'), '', '', '', 'update_int_type', ''),
	    array('Included Seconds', 'INPUT', array('name' => 'includedseconds[includedseconds]','id'=>'includedseconds', 'value' => '', 'size' => '20', 'maxlength' => '15', 'class' => "text field "), '', 'Tool tips info', '1', array('name'=>'includedseconds[operator]','class'=>'update_drp'), '', '', '', 'update_int_type', ''),
	    array('Per Minute Cost', 'INPUT', array('name' => 'cost[cost]','id'=>'cost', 'value' => '', 'size' => '20', 'maxlength' => '15', 'class' => "text field "), '', 'Tool tips info', '1', array('name'=>'cost[operator]','class'=>'update_drp'), '', '', '', 'update_int_type', ''),
            
	    array('Increment', 'INPUT', array('name' => 'inc[inc]','id'=>'inc', 'size' => '20', 'maxlength' => '15', 'class' => "text field "), '', 'tOOL TIP', '1', array('name'=>'inc[operator]','class'=>'update_drp'), '', '', '', 'update_int_type', ''),
	    array('Precedence', 'INPUT', array('name' => 'precedence[precedence]','id'=>'precedence', 'size' => '20', 'maxlength' => '15', 'class' => "text field "), '', 'tOOL TIP', '1', array('name'=>'precedence[operator]','class'=>'update_drp'), '', '', '', 'update_drp_type', ''),
	    array('Prepand', 'INPUT', array('name' => 'prepend[prepend]','id'=>'prepend', 'size' => '20', 'maxlength' => '15', 'class' => "text field "), '', 'tOOL TIP', '1', array('name'=>'prepend[operator]','class'=>'update_drp'), '', '', '', 'update_drp_type', ''),
            array('Trunk', array('name'=> 'trunk_id[trunk_id]','id'=>'trunk_id'), 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'name', 'trunks', 'build_dropdown', 'where_arr', array("status" => "0"),array('name'=>'trunk_id[operator]','class'=>'update_drp'), 'update_drp_type'),
        );

        $form['button_search'] = array('name' => 'action', 'id' => "batch_update_btn", 'content' => 'Update', 'value' => 'save', 'type' => 'button', 'class' =>'btn btn-line-parrot pull-right');
        $form['button_reset'] = array('name' => 'action', 'id' => "id_batch_reset", 'content' => 'Clear', 'value' => 'cancel', 'type' => 'reset', 'class' => 'btn btn-line-sky pull-right margin-x-10');

        return $form;
    }
    function inbound_batch_update_form() {
    $logintype=$this->CI->session->userdata('userlevel_logintype');
            $trunk=null;
        if($logintype !=1)
	  $trunk = array('Force Trunk', array('name'=> 'trunk_id[trunk_id]','id'=>'trunk_id'), 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'name', 'trunks', 'build_dropdown', 'where_arr', array("status" => "0"),array('name'=>'trunk_id[operator]','class'=>'update_drp'), 'update_drp_type');
        $form['forms'] = array("rates/origination_batch_update/",array('id' => "inbound_batch_update"));        
        $form['Batch Update'] = array(
            array('Connect Cost', 'INPUT', array('name' => 'connectcost[connectcost]','id'=>'connectcost', 'value' => '', 'size' => '20', 'maxlength' => '15', 'class' => "text field "), '', 'Tool tips info', '1', array('name'=>'connectcost[operator]','class'=>'update_drp'), '', '', '', 'update_int_type', ''),
            array('Included Seconds', 'INPUT', array('name' => 'includedseconds[includedseconds]','id'=>'includedseconds', 'value' => '', 'size' => '20', 'maxlength' => '15', 'class' => "text field "), '', 'Tool tips info', '1',array('name'=>'includedseconds[operator]','class'=>'update_drp'), '', '', '', 'update_int_type', ''),
            array('Per Minute Cost', 'INPUT', array('name' => 'cost[cost]', 'id'=>'cost', 'value' => '', 'size' => '20', 'maxlength' => '15', 'class' => "text field "), '', 'Tool tips info', '1',array('name'=>'cost[operator]','class'=>'update_drp'), '', '', '', 'update_int_type', ''),
            array('Increment', 'INPUT', array('name' => 'inc[inc]', 'id'=>'inc', 'size' => '20', 'maxlength' => '15', 'class' => "text field "), '', 'tOOL TIP', '1', array('name'=>'inc[operator]','class'=>'update_drp'), '', '', '', 'update_int_type', ''),
            array('Precedence', 'INPUT', array('name' => 'precedence[precedence]','id'=>'precedence', 'size' => '20', 'maxlength' => '15', 'class' => "text field "), '', 'tOOL TIP', '1', array('name'=>'precedence[operator]','class'=>'update_drp'), '', '', '', 'update_drp_type', ''),
            array('Rate Group', array('name'=> 'pricelist_id[pricelist_id]','id'=>'pricelist_id'), 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'name', 'pricelists', 'build_dropdown', 'where_arr', array("status" => "0","reseller_id" => "0"),array('name'=>'pricelist_id[operator]','class'=>'update_drp'), 'update_drp_type'),
           $trunk,
        );

        $form['button_search'] = array('name' => 'action', 'id' => "batch_update", 'content' => 'Update', 'value' => 'save', 'type' => 'button', 'class' => 'btn btn-line-parrot pull-right');
        $form['button_reset'] = array('name' => 'action', 'id' => "id_batch_reset", 'content' => 'Clear', 'value' => 'cancel', 'type' => 'reset', 'class' =>'btn btn-line-sky pull-right margin-x-10');

        return $form;
    }
    
         function build_rates_list_for_reseller() {
        $grid_field_arr = json_encode(array(array('Code', "140", "pattern", "pattern", "", "get_only_numeric_val"),
		    array('Destination', "240", "comment", "", "", ""),
		    array('Connection Cost', "210", "connectcost", "connectcost", "connectcost", "convert_to_currency"),
		    array('Included Seconds', "180", "includedseconds", "", "", ""),
		    array('Per Minute Cost', "180", "cost", "cost", "cost", "convert_to_currency"),
		    array('Increment', "140", "inc", "", "", ""),
		    array('Precedence', "155", "precedence", "", "", ""),
                ));
        return $grid_field_arr;
    }
    function get_reseller_inbound_search_form() {
	$accountinfo=$this->CI->session->userdata('accountinfo');
	
        $form['forms'] = array("", array('id' => "resellerrates_list_search"));
        $form['Search My Rates'] = array(
           
            array('Code', 'INPUT', array('name' => 'pattern[pattern]', '', 'size' => '20', 'maxlength' => '15', 'class' => "text field "), '', 'tOOL TIP', '1', 'pattern[pattern-string]', '', '', '', 'search_int_type', ''),
//            array('Prepend', 'INPUT', array('name' => 'prepend[prepend]', '', 'size' => '20', 'maxlength' => '15', 'class' => "text field medium"), '', 'tOOL TIP', '1', 'prepend[prepend-string]', '', '', '', 'search_string_type', ''),
         
            array('Increment', 'INPUT', array('name' => 'inc[inc]', '', 'size' => '20', 'maxlength' => '15', 'class' => "text field "), '', 'tOOL TIP', '1', 'inc[inc-integer]', '', '', '', 'search_int_type', ''),
            array('Cost per Minutes', 'INPUT', array('name' => 'cost[cost]', 'value' => '', 'size' => '20', 'maxlength' => '15', 'class' => "text field "), '', 'Tool tips info', '1', 'cost[cost-integer]', '', '', '', 'search_int_type', ''),
            
            array('Included Seconds', 'INPUT', array('name' => 'includedseconds[includedseconds]', 'value' => '', 'size' => '20', 'maxlength' => '15', 'class' => "text field "), '', 'Tool tips info', '1', 'includedseconds[includedseconds-integer]', '', '', '', 'search_int_type', ''),
             array('', 'HIDDEN', 'ajax_search', '1', '', '', ''),
            array('', 'HIDDEN', 'advance_search', '1', '', '', ''),
        );

        $form['button_search'] = array('name' => 'action', 'id' => "resellerrates_list_search_btn", 'content' => 'Search', 'value' => 'save', 'type' => 'button', 'class' => 'btn btn-line-parrot pull-right');
        $form['button_reset'] = array('name' => 'action', 'id' => "id_reset", 'content' => 'Clear', 'value' => 'cancel', 'type' => 'reset', 'class' => 'btn btn-line-sky pull-right margin-x-10');

        return $form;
    }
    function get_inbound_search_form() {
        $form['forms'] = array("", array('id' => "inbound_search"));
        $form['Search'] = array(
           
            array('Code', 'INPUT', array('name' => 'pattern[pattern]', '', 'size' => '20', 'class' => "text field "), '', 'tOOL TIP', '1', 'pattern[pattern-string]', '', '', '', 'search_string_type', ''),
           array('Destination', 'INPUT', array('name' => 'comment[comment]', '', 'size' => '20', 'class' => "text field "), '', 'tOOL TIP', '1', 'comment[comment-string]', '', '', '', 'search_string_type', ''),
              array('Connect Cost', 'INPUT', array('name' => 'connectcost[connectcost]', 'value' => '', 'size' => '20', 'class' => "text field "), '', 'Tool tips info', '1', 'connectcost[connectcost-integer]', '', '', '', 'search_int_type', ''),
              array('Included Seconds', 'INPUT', array('name' => 'includedseconds[includedseconds]', 'value' => '', 'size' => '20', 'class' => "text field "), '', 'Tool tips info', '1', 'includedseconds[includedseconds-integer]', '', '', '', 'search_int_type', ''),
              array('Per Minute Cost', 'INPUT', array('name' => 'cost[cost]', 'value' => '', 'size' => '20', 'class' => "text field "), '', 'Tool tips info', '1', 'cost[cost-integer]', '', '', '', 'search_int_type', ''),
            array('Increment', 'INPUT', array('name' => 'inc[inc]', '', 'size' => '20', 'class' => "text field "), '', 'tOOL TIP', '1', 'inc[inc-integer]', '', '', '', 'search_int_type', ''),
            array('Rate Group', 'pricelist_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'name', 'pricelists', 'build_dropdown', 'where_arr', array("status" => "0","reseller_id"=>"0")),
		array('Status', 'status', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', '', '', '', 'set_search_status', '', ''),
             array('', 'HIDDEN', 'ajax_search', '1', '', '', ''),
            array('', 'HIDDEN', 'advance_search', '1', '', '', ''),
        );

        $form['button_search'] = array('name' => 'action', 'id' => "inbound_search_btn", 'content' => 'Search', 'value' => 'save', 'type' => 'button', 'class' => 'btn btn-line-parrot pull-right');
        $form['button_reset'] = array('name' => 'action', 'id' => "id_reset", 'content' => 'Clear', 'value' => 'cancel', 'type' => 'reset', 'class' => 'btn btn-line-sky pull-right margin-x-10');

        return $form;
    }
    
    
      function get_user_rates_search_form() {
        $form['forms'] = array("", array('id' => "inbound_search"));
        $form['Search'] = array(
            array('Code', 'INPUT', array('name' => 'pattern[pattern]', '', 'size' => '20', 'class' => "text field "), '', 'tOOL TIP', '1', 'pattern[pattern-string]', '', '', '', 'search_int_type', ''),
//            array('Prepend', 'INPUT', array('name' => 'prepend[prepend]', '', 'size' => '20', 'class' => "text field medium"), '', 'tOOL TIP', '1', 'prepend[prepend-string]', '', '', '', 'search_string_type', ''),
              array('Destination', 'INPUT', array('name' => 'comment[comment]', '', 'size' => '20', 'class' => "text field "), '', 'tOOL TIP', '1', 'comment[comment-string]', '', '', '', 'search_string_type', ''),
              array('Connect Cost', 'INPUT', array('name' => 'connectcost[connectcost]', 'value' => '', 'size' => '20', 'class' => "text field "), '', 'Tool tips info', '1', 'connectcost[connectcost-integer]', '', '', '', 'search_int_type', ''),
              array('Included Seconds', 'INPUT', array('name' => 'includedseconds[includedseconds]', 'value' => '', 'size' => '20', 'class' => "text field "), '', 'Tool tips info', '1', 'includedseconds[includedseconds-integer]', '', '', '', 'search_int_type', ''),
              array('Per Minute Cost', 'INPUT', array('name' => 'cost[cost]', 'value' => '', 'size' => '20', 'class' => "text field "), '', 'Tool tips info', '1', 'cost[cost-integer]', '', '', '', 'search_int_type', ''),
            array('Increment', 'INPUT', array('name' => 'inc[inc]', '', 'size' => '20', 'class' => "text field "), '', 'tOOL TIP', '1', 'inc[inc-integer]', '', '', '', 'search_int_type', ''),
            
            array('', 'HIDDEN', 'ajax_search', '1', '', '', ''),
            array('', 'HIDDEN', 'advance_search', '1', '', '', '') 
        );

        $form['button_search'] = array('name' => 'action', 'id' => "inbound_search_btn", 'content' => 'Search', 'value' => 'save', 'type' => 'button', 'class' => 'btn btn-line-parrot pull-right');
        $form['button_reset'] = array('name' => 'action', 'id' => "id_reset", 'content' => 'Clear', 'value' => 'cancel', 'type' => 'reset', 'class' => 'btn btn-line-sky pull-right margin-x-10');

        return $form;
    }

    function build_terminationrates_for_admin() {
        // array(display name, width, db_field_parent_table,feidname, db_field_child_table,function name);
        $grid_field_arr = json_encode(array(array("<input type='checkbox' name='chkAll' class='ace checkall'/><label class='lbl'></label>", "30", "", "", "", ""),
            array("Code", "110", "pattern", "pattern", "", "get_only_numeric_val"),
            array("Destination", "120", "comment", "", "", ""),
            array("Connect Cost", "100", "connectcost", "connectcost", "connectcost", "convert_to_currency"),
            array("Included Seconds", "120", "includedseconds", "", "", ""),
            array("Per Minute Cost", "110", "cost", "cost", "cost", "convert_to_currency"),
            array("Increment", "84", "inc", "", "", ""),
            array("Precedence", "90", "precedence", "", "", ""),
            array("Strip","110", "strip", "", "", ""),
            array("Prepend", "80", "prepend", "pattern", "", "get_only_numeric_val"),
            array("Trunk", "100", "trunk_id", "name", "trunks", "get_field_name"),
            array("Status", "100", "status", "status", "status", "get_status"),
//             array("Reseller", "103", "reseller_id", "number", "accounts", "get_field_name"),
            array("Action", "94", "", "", "", array("EDIT" => array("url" => "rates/terminationrates_edit/", "mode" => "popup"),
                    "DELETE" => array("url" => "rates/terminationrates_delete/", "mode" => "single")))
                ));
        return $grid_field_arr;
    }

    function build_inbound_list_for_admin() {
        // array(display name, width, db_field_parent_table,feidname, db_field_child_table,function name);
        $grid_field_arr = json_encode(array(array("<input type='checkbox' name='chkAll' class='ace checkall'/><label class='lbl'></label>", "30", "", "", "", ""),
            array("Code", "130", "pattern", "pattern", "", "get_only_numeric_val"),
            array("Destination", "170", "comment", "", "", ""),
            array("Connect Cost", "130", "connectcost", "connectcost", "connectcost", "convert_to_currency"),
            array("Included Seconds", "155", "includedseconds", "", "", ""),
            array("Per Minute Cost", "120", "cost", "cost", "cost", "convert_to_currency"),
            array("Increment", "110", "inc", "", "", ""),
            array("Precedence", "92", "precedence", "", "", ""),
            array("Rate Group", "118", "pricelist_id", "name", "pricelists", "get_field_name"),
            array("Status", "100", "status", "status", "status", "get_status"),
            array("Action", "95", "", "", "", array("EDIT" => array("url" => "rates/origination_edit/", "mode" => "popup"),
                    "DELETE" => array("url" => "/rates/origination_delete/", "mode" => "single")))
                ));
        return $grid_field_arr;
    }

    function build_grid_buttons() {
        $buttons_json = json_encode(array(array("Create","btn btn-line-warning btn","fa fa-plus-circle fa-lg", "button_action", "/rates/terminationrates_add/", "popup"),
            array("Delete", "btn btn-line-danger","fa fa-times-circle fa-lg", "button_action", "/rates/terminationrates_delete_multiple/"),
            array("import","btn btn-line-blue" ,"fa fa-upload fa-lg", "button_action", "/rates/terminationrates_import/", 'single'),
            array("Export","btn btn-xing" ," fa fa-download fa-lg", "button_action", "/rates/terminationrates_export_cdr_xls/", 'single')
            ));
        return $buttons_json;
    }

    function build_grid_buttons_inbound() {
        $buttons_json = json_encode(array(array("Create","btn btn-line-warning btn","fa fa-plus-circle fa-lg", "button_action", "/rates/origination_add/", "popup"),
            array("Delete", "btn btn-line-danger","fa fa-times-circle fa-lg", "button_action", "/rates/origination_delete_multiple/"),
            array("import", "btn btn-line-blue","fa fa-upload fa-lg", "button_action", "/rates/origination_import/", 'single'),
            array("Export","btn btn-xing" ," fa fa-download fa-lg", "button_action", "/rates/origination_export_cdr_xls/", 'single')
            
            ));
        return $buttons_json;
    }

    function build_outbound_list_for_customer() {
        // array(display name, width, db_field_parent_table,feidname, db_field_child_table,function name);
        $grid_field_arr = json_encode(array(array("<input type='checkbox' name='chkAll' class='ace checkall'/><label class='lbl'></label>", "30", "", "", "", ""),
            array("Code", "70", "pattern", "pattern", "", "get_only_numeric_val"),
            array("Increment", "75", "inc", "", "", ""),
            array("Connect <br> Charge", "100", "connectcost", "connectcost", "connectcost", "convert_to_currency"),
            array("Included <br> Seconds", "100", "includedseconds", "", "", ""),
            array("Destination", "100", "comment", "", "", ""),
            array("Cost per <br> Minutes", "100", "cost", "cost", "cost", "convert_to_currency"),
            array("Precedence", "80", "precedence", "", "", ""),
            array("Reseller", "80", "reseller_id", "number", "accounts", "get_field_name")
                ));
        return $grid_field_arr;
    }
    function build_pattern_list_for_customer($accountid) {
        $grid_field_arr = json_encode(array(array("Patterns", "200", "blocked_patterns", "blocked_patterns", "", "get_only_numeric_val"),
			  array("Destination", "200", "destination", "", "", ""),
            array("Action", "500", "", "", "", array("DELETE" => array("url" => "/accounts/customer_delete_block_pattern/$accountid/", "mode" => "single")))
                ));
        return $grid_field_arr;
    }

    function set_pattern_grid_buttons($accountid) {
        $buttons_json = json_encode(array(array("Add Prefixes","btn btn-line-warning btn","fa fa-plus-circle fa-lg", "button_action", "/accounts/customer_add_blockpatterns/$accountid", "popup")));
        return $buttons_json;
    }

    function build_inbound_list_for_user() {
              // array(display name, width, db_field_parent_table,feidname, db_field_child_table,function name);
        $grid_field_arr = json_encode(array(array("Code", "155", "pattern", "pattern", "", "get_only_numeric_val"),
	      array("Destination", "225", "comment", "", "", ""),
	      array("Increment", "235", "inc", "", "", ""),
	      array("Cost per Minutes", "240", "cost", "cost", "cost", "convert_to_currency"),
	      array("Connect Charge", "200", "connectcost", "connectcost", "connectcost", "convert_to_currency"),
	      array("Included Seconds", "200", "includedseconds", "", "", "")
	));
        return $grid_field_arr;
    }


function build_grid_buttons_rates() {
        $buttons_json = json_encode(array(array("Export","btn btn-xing" ," fa fa-download fa-lg", "button_action", "/rates/resellersrates_xls/", 'single')));
        return $buttons_json;
    }

function build_grid_buttons_for_user() {
//echo "gfghf"; exit;
        $buttons_json = json_encode(array(array("Export CSV","btn btn-xing" ,"fa fa-download fa-lg", "button_action", "/user/user_rates_export_xls", 'single')
                       ));
        return $buttons_json;
    }
}

?>
