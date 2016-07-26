<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class trunk_form {

    function get_trunk_form_fields() {
        $form['forms'] = array(base_url() . 'trunk/trunk_save/', array('id' => 'trunks_form', 'method' => 'POST', 'name' => 'trunks_form'));
        $form['Trunk Information'] = array(
            array('', 'HIDDEN', array('name' => 'id'), '', '', '', ''),
            array('Trunk name', 'INPUT', array('name' => 'name', 'size' => '20', 'maxlength' => '30', 'class' => "text field medium"), 'trim|required|min_length[2]|max_length[25]|xss_clean', 'tOOL TIP', ''),
//             array('Protocal', 'tech', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', '', '', '', 'set_protocal'),
	    array('Provider', 'provider_id', 'SELECT', '', 'trim|required|xss_clean', 'tOOL TIP', 'Please Enter account number', 'id', 'first_name,last_name,number', 'accounts', 'build_concat_dropdown', 'where_arr',array('type'=>3,"deleted"=>"0","status"=>"0")),
            array('Gateway', 'gateway_id', 'SELECT', '', 'trim|required|xss_clean', 'tOOL TIP', 'Please select gateway first', 'id', 'name', 'gateways', 'build_dropdown','where_arr', array("status" => "0")),
	    array('Fail Over Gateway', 'failover_gateway_id', 'SELECT', '', '', 'tOOL TIP', 'Please select gateway first', 'id', 'name', 'gateways', 'build_dropdown', 'where_arr', array("status" => "0")),
        array('Fail Over Gateway', 'failover_gateway_id1', 'SELECT', '', '', 'tOOL TIP', 'Please select gateway first', 'id', 'name', 'gateways', 'build_dropdown', 'where_arr', array("status" => "0")),            
            array('Max Channels', 'INPUT', array('name' => 'maxchannels', 'value' => '0' , 'size' => '20', 'maxlength' => '4', 'class' => "text field medium"), '', 'tOOL TIP', ''));
            
            $form['Trunk Settings'] = array(
            
            array('Number Translation', 'INPUT', array('name' => 'dialed_modify', 'size' => '20', 'maxlength' => '200', 'class' => "text field medium"), '', 'tOOL TIP', ''),
	    array('Codecs', 'INPUT', array('name' => 'codec', 'size' => '20', 'maxlength' => '100', 'class' => "text field medium"), 'trim|xss_clean', 'tOOL TIP', ''),
            array('Precedence', 'INPUT', array('name' => 'precedence', 'size' => '20', 'maxlength' => '4', 'class' => "text field medium"), '', 'tOOL TIP', ''),
            
//             array('Reseller','reseller_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'number', 'accounts', 'build_dropdown', 'where_arr', array("deleted" => "0", 'type'=> '1','reseller_id'=>"0"), 'multi'),
            array('Status', 'status', 'SELECT', '', '', 'tOOL TIP', 'Please Select Status', '', '', '', 'set_status'),
        );
        $form['button_cancel'] = array('name' => 'action', 'content' => 'Cancel', 'value' => 'cancel', 'type' => 'button', 'class' => 'btn btn-line-sky margin-x-10', 'onclick' => 'return redirect_page(\'NULL\')');
        $form['button_save'] = array('name' => 'action', 'content' => 'Save', 'value' => 'save', 'id' => 'submit', 'type' => 'button', 'class' => 'btn btn-line-parrot');
        return $form;
    }

    function get_trunk_search_form() {
        $form['forms'] = array("", array('id' => "trunk_search"));
        $form['Search'] = array(
            array('Trunk Name', 'INPUT', array('name' => 'name[name]', '', 'size' => '20', 'class' => "text field"), '', 'tOOL TIP', '1', 'name[name-string]', '', '', '', 'search_string_type', ''),
            array('Provider', 'provider_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'first_name,last_name,number', 'accounts', 'build_concat_dropdown', 'where_arr',array('type'=>3)),
            array('Gateway', 'gateway_id', 'SELECT', '', '', 'tOOL TIP', 'Please select gateway first', 'id', 'name', 'gateways', 'build_dropdown','where_arr', array("status" => "0")),
	array('Status', 'status', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', '', '', '', 'set_search_status', '', ''),
	    array('', 'HIDDEN', 'ajax_search', '1', '', '', ''),
            array('', 'HIDDEN', 'advance_search', '1', '', '', ''),
        );

        $form['button_search'] = array('name' => 'action', 'id' => "trunk_search_btn", 'content' => 'Search', 'value' => 'save', 'type' => 'button', 'class' => 'btn btn-line-parrot pull-right');
        $form['button_reset'] = array('name' => 'action', 'id' => "id_reset", 'content' => 'Clear', 'value' => 'cancel', 'type' => 'reset', 'class' => 'btn btn-line-sky pull-right margin-x-10');
        return $form;
    }

    function build_trunk_list_for_admin() {
        // array(display name, width, db_field_parent_table,feidname, db_field_child_table,function name);
        $grid_field_arr = json_encode(array(array("<input type='checkbox' name='chkAll' class='ace checkall'/><label class='lbl'></label>", "30", "", "", "", ""),
            array("Trunk Name", "160", "name", "", "", ""),
//             array("Protocol", "180", "tech", "", "", ""),
            array("Provider", "165", "provider_id", "first_name,last_name,number", "accounts", "build_concat_string"),
            array("Gateway Name", "165", "gateway_id", "name", "gateways", "get_field_name"),
	    array("Failover GW name", "150", "failover_gateway_id","name", "gateways", "get_field_name"),
        array("Failover GW name", "150", "failover_gateway_id1","name", "gateways", "get_field_name"),
	    array("Max<br>channels", "70", "maxchannels", "", "", ""),
            array("Codecs", "110", "codec", "", "", ""),
            //array("Precedence", "100", "precedence", "", "", ""),
	    array("Rate<br>Count", "70", "id", "trunk_id", "outbound_routes", "get_field_count"),
            array("Status", "70", "status", "status", "status", "get_status"),
	  array("Action", "124", "", "", "", array("EDIT" => array("url" => "/trunk/trunk_edit/", "mode" => "popup"),
                    "DELETE" => array("url" => "/trunk/trunk_remove/", "mode" => "single")))
                ));
        return $grid_field_arr;
    }

    function build_grid_buttons() {
        $buttons_json = json_encode(array(array("Create","btn btn-line-warning btn", "fa fa-plus-circle fa-lg", "button_action", "/trunk/trunk_add/", "popup"),
            array("Delete", "btn btn-line-danger", "fa fa-times-circle fa-lg", "button_action", "/trunk/trunk_delete_multiple/")
            ));
        return $buttons_json;
    }

}

?>
