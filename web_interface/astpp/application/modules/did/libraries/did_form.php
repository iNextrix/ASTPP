<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class did_form {

    function __construct() {
        $this->CI = & get_instance();
    }

    function get_dids_form_fields() {
        $form['forms'] = array(base_url() . '/did/did_save/', array('id' => 'did_form', 'method' => 'POST', 'name' => 'did_form'));
        $form['DID Add/Edit'] = array(
            array('', 'HIDDEN', array('name' => 'id'), '', '', '', ''),
            array('Number', 'INPUT', array('name' => 'number', 'size' => '20', 'maxlength' => '20', 'class' => "text field medium"), 'trim|required|min_length[5]|max_length[20]|xss_clean', 'tOOL TIP', 'Please Enter account number'),
            array('Country', 'country_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'country', 'countrycode', 'build_dropdown', '', ''),
            array('City', 'INPUT', array('name' => 'city', 'size' => '20', 'maxlength' => '15', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter Password'),
            array('Province/State', 'INPUT', array('name' => 'province', 'size' => '15', 'maxlength' => '15', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter Password'),
            array('Provider', 'provider_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'number', 'accounts', 'build_dropdown', 'type', '3'),
        );

        $form['DID Billing'] = array(
            array('Account', 'accountid', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'first_name,last_name,number', 'accounts', 'build_concat_dropdown', 'where_arr', array("reseller_id" => "0", "type" => "0", "deleted" => "0")),
            array('Increments', 'INPUT', array('name' => 'inc', 'size' => '20', 'maxlength' => '15', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter Password'),
            array('Cost', 'INPUT', array('name' => 'cost', 'size' => '15', 'maxlength' => '100', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter Password'),
            array('Included Seconds', 'INPUT', array('name' => 'includedseconds', 'size' => '50', 'maxlength' => '50', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter Password'),
            array('Setup Fee', 'INPUT', array('name' => 'setup', 'maxlength' => '15', 'size' => '15', 'class' => 'text field medium'), '', 'tOOL TIP', ''),
            array('Monthly Fee', 'INPUT', array('name' => 'monthlycost', 'size' => '15', 'maxlength' => '20', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter Password'),
            array('Connection Fee', 'INPUT', array('name' => 'connectcost', 'size' => '15', 'maxlength' => '20', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter Password'),
            array('Disconnection Fee', 'INPUT', array('name' => 'disconnectionfee', 'id' => 'first_name', 'size' => '15', 'maxlength' => '15', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter account number'),
            array('Limit Length', 'limittime', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', '', '', '', 'set_allow', ''),
            array('Bill on Allocation', 'allocation_bill_status', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', '', '', '', 'set_allow', ''),
        );

        $form['DID Setting'] = array(
            array('Call Type', 'call_type', 'SELECT', '', '', 'tOOL TIP', '', '', '', '', 'set_call_type', ''),
            array('Destination', 'INPUT', array('name' => 'extensions', 'size' => '20', 'maxlength' => '100', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter Password'),
            array('Max Channels', 'INPUT', array('name' => 'maxchannels', 'size' => '20', 'maxlength' => '15', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter account number')
        );

        $form['button_save'] = array('name' => 'action', 'content' => 'Save', 'value' => 'save', 'id' => 'submit', 'type' => 'button', 'class' => 'ui-state-default float-right ui-corner-all ui-button');
        $form['button_cancel'] = array('name' => 'action', 'content' => 'Cancel', 'value' => 'cancel', 'type' => 'button', 'class' => 'ui-state-default float-right ui-corner-all ui-button', 'onclick' => 'return redirect_page(\'NULL\')');
        return $form;
    }

    function get_search_did_form() {

        $form['forms'] = array("", array('id' => "did_search"));
        $form['Search DID'] = array(
            array('', 'HIDDEN', 'ajax_search', '1', '', '', ''),
            array('', 'HIDDEN', 'advance_search', '1', '', '', ''),
            array('Did Number', 'INPUT', array('name' => 'number[number]', '', 'size' => '20', 'maxlength' => '20', 'class' => "text field"), '', 'tOOL TIP', '1', 'number[number-string]', '', '', '', 'search_string_type', ''),
            array('Country', 'country_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'country', 'countrycode', 'build_dropdown', '', ''),
            array('Provider', 'provider_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'number', 'accounts', 'build_dropdown', 'type', '3')
        );

        $form['button_search'] = array('name' => 'action', 'id' => "did_search_btn", 'content' => 'Search', 'value' => 'save', 'type' => 'button', 'class' => 'ui-state-default float-right ui-corner-all ui-button');
        $form['button_reset'] = array('name' => 'action', 'id' => "id_reset", 'content' => 'Clear Search Filter', 'value' => 'cancel', 'type' => 'reset', 'class' => 'ui-state-default float-right ui-corner-all ui-button');

        return $form;
    }

    function build_did_list_for_admin() {
        // array(display name, width, db_field_parent_table,feidname, db_field_child_table,function name);
        if ($this->CI->session->userdata('logintype') == '1') {
            $action = 'did/did_reseller_edit/edit/';
            $action_remove = 'did/did_reseller_edit/delete/';
            $mode="single";
        } else {
            $action = 'did/did_edit/';
            $action_remove = 'did/did_remove/';
            $mode="popup";
        }
        $grid_field_arr = json_encode(array(array("<input type='checkbox' name='chkAll' class='checkall'/>", "30", "", "", "", ""),
            array("DID Number", "110", "number", "", "", ""),
            array("Account Number", "110", "accountid", "number", "accounts", "get_field_name"),
            array("Call Type", "60", "call_type", "call_type", "call_type", "get_call_type"),
            array("Destination", "230", "extensions", "", "", ""),
            array("Country", "85", "country_id", "country", "countrycode", "get_field_name"),
            array("Increments", "70", "inc", "", "", ""),
            array("Cost", "85", "cost", "cost", "cost", "convert_to_currency"),
            array("Included <br>Seconds", "75", "includedseconds", "", "", ""),
            array("Setup <br> Fee", "88", "setup", "setup", "setup", "convert_to_currency"),
            array("Monthly<br> fee", "87", "monthlycost", "monthlycost", "monthlycost", "convert_to_currency"),
            array("Action", "60", "", "", "", array("EDIT" => array("url" => "$action", "mode" => "$mode"),
                    "DELETE" => array("url" => "$action_remove", "mode" => "single")))
                ));
        return $grid_field_arr;
    }

    function build_grid_buttons() {
        $buttons_json = json_encode(array(array("Add", "add", "button_action", "/did/did_add/", "popup"),
            array("Import", "import", "button_action", "/did/did_import/", 'popup'),
            array("Delete", "delete", "button_action", "/did/did_delete_multiple/"),
            array("Refresh", "reload", "/accounts/clearsearchfilter/")));
        return $buttons_json;
    }

    function build_did_list_for_customer($accountid, $accounttype) {
        // array(display name, width, db_field_parent_table,feidname, db_field_child_table,function name);
        $grid_field_arr = json_encode(array(array("DID Number", "140", "number", "", "", ""),
            array("Country", "80", "country_id", "country", "countrycode", "get_field_name"),
            array("Increments", "70", "inc", "", "", ""),
            array("Cost", "100", "cost", "cost", "cost", "convert_to_currency"),
            array("Included<br> Seconds", "100", "includedseconds", "", "", ""),
            array("Setup <br> Fee", "120", "setup", "setup", "setup", "convert_to_currency"),
            array("Monthly<br> Fee", "140", "monthlycost", "monthlycost", "monthlycost", "convert_to_currency"),
            array("Connection Fee", "139", "connectcost", "connectcost", "connectcost", "convert_to_currency"),
            array("Disconnection <br> Fee", "140", "disconnectionfee", "disconnectionfee", "disconnectionfee", "convert_to_currency"),
            array("Action", "30", "", "", "", array("DELETE" => array("url" => "/accounts/customer_dids_action/delete/$accountid/$accounttype/", "mode" => "single")))
                ));
        return $grid_field_arr;
    }

    function build_did_list_for_user() {
        // array(display name, width, db_field_parent_table,feidname, db_field_child_table,function name);
        $grid_field_arr = json_encode(array(array("DID Number", "130", "number", "", "", ""),
            array("Call Type", "100", "call_type", "call_type", "call_type", "get_call_type"),
            array("Destination", "230", "extensions", "", "", ""),
            array("Country", "88", "country_id", "country", "countrycode", "get_field_name"),
            array("Increments", "105", "inc", "", "", ""),
            array("Cost", "100", "cost", "cost", "cost", "convert_to_currency"),
            array("Included<br> Seconds", "70", "includedseconds", "", "", ""),
            array("Setup <br> Fee", "115", "setup", "setup", "setup", "convert_to_currency"),
            array("Monthly<br> Cost", "115", "monthlycost", "monthlycost", "monthlycost", "convert_to_currency"),
            array("Action", "60", "", "", "", array("EDIT" => array("url" => "/user/user_did_edit/", "mode" => "popup"),
                    "DELETE" => array("url" => "/user/user_dids_action/delete/", "mode" => "single")))
                ));
        return $grid_field_arr;
    }
}

?>
