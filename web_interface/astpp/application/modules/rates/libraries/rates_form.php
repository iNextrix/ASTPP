<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class rates_form {

    function get_termination_form_fields() {
        $form['forms'] = array(base_url() . 'rates/terminationrates_save/', array('id' => 'termination_form', 'method' => 'POST', 'name' => 'termination_form'));
        $form['Termination Rates Add/Edit'] = array(
            array('', 'HIDDEN', array('name' => 'id'), '', '', '', ''),
            array('Code', 'INPUT', array('name' => 'pattern', 'size' => '20', 'maxlength' => '15', 'class' => "text field medium"), 'trim|required|min_length[1]|numeric|max_length[25]|xss_clean', 'tOOL TIP', ''),
            array('Prepend', 'INPUT', array('name' => 'prepend', 'size' => '20', 'maxlength' => '15', 'class' => "text field medium"), 'trim|xss_clean', 'tOOL TIP', ''),
            array('Destination', 'INPUT', array('name' => 'comment', 'size' => '20', 'maxlength' => '30', 'class' => "text field medium"), 'tOOL TIP', ''),
             array('Increment', 'INPUT', array('name' => 'inc', 'size' => '20', 'maxlength' => '15', 'class' => "text field medium"), '', 'tOOL TIP', ''),
            
             array('Cost per Additional Minute', 'INPUT', array('name' => 'cost', 'size' => '20', 'maxlength' => '15', 'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('Connect Charge', 'INPUT', array('name' => 'connectcost', 'size' => '20', 'maxlength' => '15', 'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('Included Seconds', 'INPUT', array('name' => 'includedseconds', 'size' => '20', 'maxlength' => '15', 'class' => "text field medium"), '', 'tOOL TIP', ''),
          
            array('Precedence', 'INPUT', array('name' => 'precedence', 'size' => '20', 'maxlength' => '15', 'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('Trunk', 'trunk_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'name', 'trunks', 'build_dropdown', 'where_arr', array("status" => "1")),
        );

        $form['button_cancel'] = array('name' => 'action', 'content' => 'Cancel', 'value' => 'cancel', 'type' => 'button', 'class' => 'ui-state-default float-right ui-corner-all ui-button', 'onclick' => 'return redirect_page(\'NULL\')');
        $form['button_save'] = array('name' => 'action', 'content' => 'Save', 'value' => 'save', 'id' => 'submit', 'type' => 'button', 'class' => 'ui-state-default float-right ui-corner-all ui-button');

        return $form;
    }

    function get_inbound_form_fields() {
        $form['forms'] = array(base_url() . 'rates/origination_save/', array('id' => 'origination_form', 'method' => 'POST', 'name' => 'origination_form'));
        $form['Origination Rate Add/Edit'] = array(
            array('', 'HIDDEN', array('name' => 'id'), '', '', '', ''),
            array('', 'HIDDEN', array('name' => 'status', 'value' => '1'), '', '', ''),
            array('Rate Group', 'pricelist_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'name', 'pricelists', 'build_dropdown', 'where_arr', array("status" => "1","reseller_id" => "0")),
            array('Code', 'INPUT', array('name' => 'pattern', 'size' => '20', 'maxlength' => '15', 'class' => "text field medium"), 'trim|required|min_length[1]|numeric|max_length[25]|xss_clean', 'tOOL TIP', ''),
            array('Destination', 'INPUT', array('name' => 'comment', 'size' => '20', 'maxlength' => '30', 'class' => "text field medium"), 'tOOL TIP', ''),
            
            
            array('Increment', 'INPUT', array('name' => 'inc', 'size' => '20', 'maxlength' => '15', 'class' => "text field medium"), '', 'tOOL TIP', ''), 
            array('Cost per Additional Minute', 'INPUT', array('name' => 'cost', 'size' => '20', 'maxlength' => '15', 'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('Connect Charge', 'INPUT', array('name' => 'connectcost', 'size' => '20', 'maxlength' => '15', 'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('Included Seconds', 'INPUT', array('name' => 'includedseconds', 'size' => '20', 'maxlength' => '15', 'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('Precedence', 'INPUT', array('name' => 'precedence', 'size' => '20', 'maxlength' => '15', 'class' => "text field medium"), '', 'tOOL TIP', ''),
        );

        $form['button_cancel'] = array('name' => 'action', 'content' => 'Cancel', 'value' => 'cancel', 'type' => 'button', 'class' => 'ui-state-default float-right ui-corner-all ui-button', 'onclick' => 'return redirect_page(\'NULL\')');
        $form['button_save'] = array('name' => 'action', 'content' => 'Save', 'value' => 'save', 'id' => 'submit', 'type' => 'button', 'class' => 'ui-state-default float-right ui-corner-all ui-button');

        return $form;
    }

    function get_termination_search_form() {
        $form['forms'] = array("", array('id' => "termination_search"));
        $form['Search Termination Rates'] = array(
            array('', 'HIDDEN', 'ajax_search', '1', '', '', ''),
            array('', 'HIDDEN', 'advance_search', '1', '', '', ''),
            array('Code', 'INPUT', array('name' => 'pattern[pattern]', '', 'size' => '20', 'maxlength' => '15', 'class' => "text field"), '', 'tOOL TIP', '1', 'pattern[pattern-string]', '', '', '', 'search_string_type', ''),
            array('Prepend', 'INPUT', array('name' => 'prepend[prepend]', '', 'size' => '20', 'maxlength' => '15', 'class' => "text field "), '', 'tOOL TIP', '1', 'prepend[prepend-string]', '', '', '', 'search_string_type', ''),
            array('Destination', 'INPUT', array('name' => 'comment[comment]', '', 'size' => '20', 'maxlength' => '50', 'class' => "text field "), '', 'tOOL TIP', '1', 'comment[comment-string]', '', '', '', 'search_string_type', ''),
	    array('Increment', 'INPUT', array('name' => 'inc[inc]', '', 'size' => '20', 'maxlength' => '15', 'class' => "text field "), '', 'tOOL TIP', '1', 'inc[inc-integer]', '', '', '', 'search_int_type', ''),
                
	    array('Cost per Minutes', 'INPUT', array('name' => 'cost[cost]', 'value' => '', 'size' => '20', 'maxlength' => '15', 'class' => "text field "), '', 'Tool tips info', '1', 'cost[cost-integer]', '', '', '', 'search_int_type', ''),
            array('Connect Charge', 'INPUT', array('name' => 'connectcost[connectcost]', 'value' => '', 'size' => '20', 'maxlength' => '15', 'class' => "text field "), '', 'Tool tips info', '1', 'connectcost[connectcost-integer]', '', '', '', 'search_int_type', ''),
            array('Included Seconds', 'INPUT', array('name' => 'includedseconds[includedseconds]', 'value' => '', 'size' => '20', 'maxlength' => '15', 'class' => "text field "), '', 'Tool tips info', '1', 'includedseconds[includedseconds-integer]', '', '', '', 'search_int_type', ''),
            array('Trunk', 'trunk_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'name', 'trunks', 'build_dropdown', 'where_arr', array("status" => "1")),
            
            
        );

        $form['button_search'] = array('name' => 'action', 'id' => "termination_search_btn", 'content' => 'Search', 'value' => 'save', 'type' => 'button', 'class' => 'ui-state-default float-right ui-corner-all ui-button');
        $form['button_reset'] = array('name' => 'action', 'id' => "id_reset", 'content' => 'Clear Search Filter', 'value' => 'cancel', 'type' => 'reset', 'class' => 'ui-state-default float-right ui-corner-all ui-button');

        return $form;
    }

    
    function termination_batch_update_form() {
        $form['forms'] = array("rates/terminationrates_batch_update/", array('id' => "termination_batch_update"));
        $form['Batch Update Termination Rates'] = array(
            array('Prepend', 'INPUT', array('name' => 'prepend[prepend]','id'=>'prepend', 'size' => '20', 'maxlength' => '15', 'class' => "text field "), '', 'tOOL TIP', '1', array('name'=>'prepend[operator]','class'=>'update_drp'), '', '', '', 'update_drp_type', ''),
	    array('Increment', 'INPUT', array('name' => 'inc[inc]','id'=>'inc', 'size' => '20', 'maxlength' => '15', 'class' => "text field "), '', 'tOOL TIP', '1', array('name'=>'inc[operator]','class'=>'update_drp'), '', '', '', 'update_int_type', ''),
	    array('Cost per Minutes', 'INPUT', array('name' => 'cost[cost]','id'=>'cost', 'value' => '', 'size' => '20', 'maxlength' => '15', 'class' => "text field "), '', 'Tool tips info', '1', array('name'=>'cost[operator]','class'=>'update_drp'), '', '', '', 'update_int_type', ''),
            array('Connect Charge', 'INPUT', array('name' => 'connectcost[connectcost]','id'=>'connectcost', 'value' => '', 'size' => '20', 'maxlength' => '15', 'class' => "text field "), '', 'Tool tips info', '1', array('name'=>'connectcost[operator]','class'=>'update_drp'), '', '', '', 'update_int_type', ''),
            array('Included Seconds', 'INPUT', array('name' => 'includedseconds[includedseconds]','id'=>'includedseconds', 'value' => '', 'size' => '20', 'maxlength' => '15', 'class' => "text field "), '', 'Tool tips info', '1', array('name'=>'includedseconds[operator]','class'=>'update_drp'), '', '', '', 'update_int_type', ''),
            array('Precedence', 'INPUT', array('name' => 'precedence[precedence]','id'=>'precedence', 'size' => '20', 'maxlength' => '15', 'class' => "text field "), '', 'tOOL TIP', '1', array('name'=>'precedence[operator]','class'=>'update_drp'), '', '', '', 'update_drp_type', ''),
            array('Trunk', array('name'=> 'trunk_id[trunk_id]','id'=>'trunk_id'), 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'name', 'trunks', 'build_dropdown', 'where_arr', array("status" => "1"),array('name'=>'trunk_id[operator]','class'=>'update_drp'), 'update_drp_type'),
        );

        $form['button_search'] = array('name' => 'action', 'id' => "batch_update_btn", 'content' => 'Update', 'value' => 'save', 'type' => 'button', 'class' => 'ui-state-default float-right ui-corner-all ui-button');
        $form['button_reset'] = array('name' => 'action', 'id' => "id_batch_reset", 'content' => 'Clear Value', 'value' => 'cancel', 'type' => 'reset', 'class' => 'ui-state-default float-right ui-corner-all ui-button');

        return $form;
    }
    function inbound_batch_update_form() {
        $form['forms'] = array("rates/origination_batch_update/",array('id' => "inbound_batch_update"));        
        $form['Batch Update Origination Rates'] = array(
            array('Rate Group', array('name'=> 'pricelist_id[pricelist_id]','id'=>'pricelist_id'), 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'name', 'pricelists', 'build_dropdown', 'where_arr', array("status" => "1","reseller_id" => "0"),array('name'=>'pricelist_id[operator]','class'=>'update_drp'), 'update_drp_type'),
            array('Increment', 'INPUT', array('name' => 'inc[inc]', 'id'=>'inc', 'size' => '20', 'maxlength' => '15', 'class' => "text field "), '', 'tOOL TIP', '1', array('name'=>'inc[operator]','class'=>'update_drp'), '', '', '', 'update_int_type', ''),
            array('Cost per Minutes', 'INPUT', array('name' => 'cost[cost]', 'id'=>'cost', 'value' => '', 'size' => '20', 'maxlength' => '15', 'class' => "text field "), '', 'Tool tips info', '1',array('name'=>'cost[operator]','class'=>'update_drp'), '', '', '', 'update_int_type', ''),
            array('Connect Charge', 'INPUT', array('name' => 'connectcost[connectcost]','id'=>'connectcost', 'value' => '', 'size' => '20', 'maxlength' => '15', 'class' => "text field "), '', 'Tool tips info', '1', array('name'=>'connectcost[operator]','class'=>'update_drp'), '', '', '', 'update_int_type', ''),
            array('Included Seconds', 'INPUT', array('name' => 'includedseconds[includedseconds]','id'=>'includedseconds', 'value' => '', 'size' => '20', 'maxlength' => '15', 'class' => "text field "), '', 'Tool tips info', '1',array('name'=>'includedseconds[operator]','class'=>'update_drp'), '', '', '', 'update_int_type', ''),
            array('Precedence', 'INPUT', array('name' => 'precedence[precedence]','id'=>'precedence', 'size' => '20', 'maxlength' => '15', 'class' => "text field "), '', 'tOOL TIP', '1', array('name'=>'precedence[operator]','class'=>'update_drp'), '', '', '', 'update_drp_type', ''),
        );

        $form['button_search'] = array('name' => 'action', 'id' => "batch_update", 'content' => 'Update', 'value' => 'save', 'type' => 'button', 'class' => 'ui-state-default float-right ui-corner-all ui-button');
        $form['button_reset'] = array('name' => 'action', 'id' => "id_batch_reset", 'content' => 'Clear Value', 'value' => 'cancel', 'type' => 'reset', 'class' => 'ui-state-default float-right ui-corner-all ui-button');

        return $form;
    }
    
    
    function get_inbound_search_form() {
        $form['forms'] = array("", array('id' => "inbound_search"));
        $form['Search Origination Rates'] = array(
            array('', 'HIDDEN', 'ajax_search', '1', '', '', ''),
            array('', 'HIDDEN', 'advance_search', '1', '', '', ''),
            array('Code', 'INPUT', array('name' => 'pattern[pattern]', '', 'size' => '20', 'maxlength' => '15', 'class' => "text field "), '', 'tOOL TIP', '1', 'pattern[pattern-string]', '', '', '', 'search_string_type', ''),
//            array('Prepend', 'INPUT', array('name' => 'prepend[prepend]', '', 'size' => '20', 'maxlength' => '15', 'class' => "text field medium"), '', 'tOOL TIP', '1', 'prepend[prepend-string]', '', '', '', 'search_string_type', ''),
         
            array('Increment', 'INPUT', array('name' => 'inc[inc]', '', 'size' => '20', 'maxlength' => '15', 'class' => "text field "), '', 'tOOL TIP', '1', 'inc[inc-integer]', '', '', '', 'search_int_type', ''),
            array('Cost per Minutes', 'INPUT', array('name' => 'cost[cost]', 'value' => '', 'size' => '20', 'maxlength' => '15', 'class' => "text field "), '', 'Tool tips info', '1', 'cost[cost-integer]', '', '', '', 'search_int_type', ''),
            
            array('Included Seconds', 'INPUT', array('name' => 'includedseconds[includedseconds]', 'value' => '', 'size' => '20', 'maxlength' => '15', 'class' => "text field "), '', 'Tool tips info', '1', 'includedseconds[includedseconds-integer]', '', '', '', 'search_int_type', ''),
            array('Rate Group', 'pricelist_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'name', 'pricelists', 'build_dropdown', 'where_arr', array("status" => "1","reseller_id" => "0")),
        );

        $form['button_search'] = array('name' => 'action', 'id' => "inbound_search_btn", 'content' => 'Search', 'value' => 'save', 'type' => 'button', 'class' => 'ui-state-default float-right ui-corner-all ui-button');
        $form['button_reset'] = array('name' => 'action', 'id' => "id_reset", 'content' => 'Clear Search Filter', 'value' => 'cancel', 'type' => 'reset', 'class' => 'ui-state-default float-right ui-corner-all ui-button');

        return $form;
    }
    
    
      function get_user_rates_search_form() {
        $form['forms'] = array("", array('id' => "inbound_search"));
        $form['Search Origination Rates'] = array(
            array('', 'HIDDEN', 'ajax_search', '1', '', '', ''),
            array('', 'HIDDEN', 'advance_search', '1', '', '', ''),
            array('Code', 'INPUT', array('name' => 'pattern[pattern]', '', 'size' => '20', 'maxlength' => '15', 'class' => "text field "), '', 'tOOL TIP', '1', 'pattern[pattern-string]', '', '', '', 'search_string_type', ''),
//            array('Prepend', 'INPUT', array('name' => 'prepend[prepend]', '', 'size' => '20', 'maxlength' => '15', 'class' => "text field medium"), '', 'tOOL TIP', '1', 'prepend[prepend-string]', '', '', '', 'search_string_type', ''),
              array('Destination', 'INPUT', array('name' => 'comment[comment]', '', 'size' => '20', 'maxlength' => '15', 'class' => "text field "), '', 'tOOL TIP', '1', 'comment[comment-string]', '', '', '', 'search_string_type', ''),
            array('Increment', 'INPUT', array('name' => 'inc[inc]', '', 'size' => '20', 'maxlength' => '15', 'class' => "text field "), '', 'tOOL TIP', '1', 'inc[inc-integer]', '', '', '', 'search_int_type', ''),
            array('Cost per Minutes', 'INPUT', array('name' => 'cost[cost]', 'value' => '', 'size' => '20', 'maxlength' => '15', 'class' => "text field "), '', 'Tool tips info', '1', 'cost[cost-integer]', '', '', '', 'search_int_type', ''),
            array('Connect Charge', 'INPUT', array('name' => 'connectcost[connectcost]', 'value' => '', 'size' => '20', 'maxlength' => '15', 'class' => "text field "), '', 'Tool tips info', '1', 'connectcost[connectcost-integer]', '', '', '', 'search_int_type', ''),
            array('Included Seconds', 'INPUT', array('name' => 'includedseconds[includedseconds]', 'value' => '', 'size' => '20', 'maxlength' => '15', 'class' => "text field "), '', 'Tool tips info', '1', 'includedseconds[includedseconds-integer]', '', '', '', 'search_int_type', ''),
        );

        $form['button_search'] = array('name' => 'action', 'id' => "inbound_search_btn", 'content' => 'Search', 'value' => 'save', 'type' => 'button', 'class' => 'ui-state-default float-right ui-corner-all ui-button');
        $form['button_reset'] = array('name' => 'action', 'id' => "id_reset", 'content' => 'Clear Search Filter', 'value' => 'cancel', 'type' => 'reset', 'class' => 'ui-state-default float-right ui-corner-all ui-button');

        return $form;
    }

    function build_outbound_list_for_admin() {
        // array(display name, width, db_field_parent_table,feidname, db_field_child_table,function name);
        $grid_field_arr = json_encode(array(array("<input type='checkbox' name='chkAll' class='checkall'/>", "30", "", "", "", ""),
            array("Code", "80", "pattern", "pattern", "", "get_only_numeric_val"),
            array("Prepend", "80", "prepend", "", "", ""),
            array("Destination", "140", "comment", "", "", ""),
            array("Increment", "85", "inc", "", "", ""),
            array("Cost per Minutes", "145", "cost", "cost", "cost", "convert_to_currency"),

            array("Connect Charge", "115", "connectcost", "connectcost", "connectcost", "convert_to_currency"),
            array("Included Seconds", "110", "includedseconds", "", "", ""),

            array("Precedence", "110", "precedence", "", "", ""),
            array("Trunk", "148", "trunk_id", "name", "trunks", "get_field_name"),
//             array("Reseller", "103", "reseller_id", "number", "accounts", "get_field_name"),
            array("Action", "60", "", "", "", array("EDIT" => array("url" => "rates/terminationrates_edit/", "mode" => "popup"),
                    "DELETE" => array("url" => "rates/terminationrates_delete/", "mode" => "single")))
                ));
        return $grid_field_arr;
    }

    function build_inbound_list_for_admin() {
        // array(display name, width, db_field_parent_table,feidname, db_field_child_table,function name);
        $grid_field_arr = json_encode(array(array("<input type='checkbox' name='chkAll' class='checkall'/>", "30", "", "", "", ""),
            array("Code", "110", "pattern", "pattern", "", "get_only_numeric_val"),
            array("Destination", "170", "comment", "", "", ""),
            array("Rate Group", "160", "pricelist_id", "name", "pricelists", "get_field_name"),
            array("Increment", "130", "inc", "", "", ""),
           array("Cost per <br> Minutes", "118", "cost", "cost", "cost", "convert_to_currency"),
            array("Connect <br> Charge", "144", "connectcost", "connectcost", "connectcost", "convert_to_currency"),
            array("Included <br> Seconds", "112", "includedseconds", "", "", ""),
            array("Precedence", "82", "precedence", "", "", ""),
           
            array("Action", "60", "", "", "", array("EDIT" => array("url" => "rates/origination_edit/", "mode" => "popup"),
                    "DELETE" => array("url" => "/rates/origination_delete/", "mode" => "single")))
                ));
        return $grid_field_arr;
    }

    function build_grid_buttons() {
        $buttons_json = json_encode(array(array("Add", "add", "button_action", "/rates/terminationrates_add/", "popup"),
            array("import", "import", "button_action", "/rates/terminationrates_import/", 'popup'),
            array("Delete", "delete", "button_action", "/rates/terminationrates_delete_multiple/"),
            array("Refresh", "reload", "/accounts/clearsearchfilter/")));
        return $buttons_json;
    }

    function build_grid_buttons_inbound() {
        $buttons_json = json_encode(array(array("Add", "add", "button_action", "/rates/origination_add/", "popup"),
            array("import", "import", "button_action", "/rates/origination_import/", 'popup'),
            array("Delete", "delete", "button_action", "/rates/origination_delete_multiple/"),
            array("Refresh", "reload", "/accounts/clearsearchfilter/")));
        return $buttons_json;
    }

    function build_outbound_list_for_customer() {
        // array(display name, width, db_field_parent_table,feidname, db_field_child_table,function name);
        $grid_field_arr = json_encode(array(array("<input type='checkbox' name='chkAll' class='checkall'/>", "30", "", "", "", ""),
            array("Code", "150", "pattern", "pattern", "", "get_only_numeric_val"),
            array("Increment", "80", "inc", "", "", ""),
            array("Connect <br> Charge", "80", "connectcost", "connectcost", "connectcost", "convert_to_currency"),
            array("Included <br> Seconds", "80", "includedseconds", "", "", ""),
            array("Destination", "150", "comment", "", "", ""),
            array("Cost per <br> Minutes", "80", "cost", "cost", "cost", "convert_to_currency"),
            array("Precedence", "90", "precedence", "", "", ""),
            array("Reseller", "110", "reseller_id", "number", "accounts", "get_field_name")
                ));
        return $grid_field_arr;
    }

    function build_pattern_list_for_customer($accountid) {
        $grid_field_arr = json_encode(array(array("Patterns", "100", "blocked_patterns", "blocked_patterns", "", "get_only_numeric_val"),
            array("Action", "30", "", "", "", array("DELETE" => array("url" => "/accounts/customer_delete_block_pattern/$accountid/", "mode" => "single")))
                ));
        return $grid_field_arr;
    }

    function set_pattern_grid_buttons($accountid) {
        $buttons_json = json_encode(array(array("ADD Patterns", "add", "button_action", "/accounts/customer_add_blockpatterns/$accountid", "popup")));
        return $buttons_json;
    }

    function build_inbound_list_for_user() {
        // array(display name, width, db_field_parent_table,feidname, db_field_child_table,function name);
        $grid_field_arr = json_encode(array(array("Code", "100", "pattern", "pattern", "", "get_only_numeric_val"),
	      array("Destination", "150", "comment", "", "", ""),
	      array("Increment", "100", "inc", "", "", ""),
	      array("Cost per Minutes", "130", "cost", "cost", "cost", "convert_to_currency"),
	      array("Connect Charge", "130", "connectcost", "connectcost", "connectcost", "convert_to_currency"),
	      array("Included Seconds", "130", "includedseconds", "", "", ""),
	));
        return $grid_field_arr;
    }

}

?>
