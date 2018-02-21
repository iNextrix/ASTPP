<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class trunk_form {

    function get_trunk_form_fields() {
        $form['forms'] = array(base_url() . 'trunk/trunk_save/', array('id' => 'trunks_form', 'method' => 'POST', 'name' => 'trunks_form'));
        $form['Trunk Add/Edit'] = array(
            array('', 'HIDDEN', array('name' => 'id'), '', '', '', ''),
            array('Trunk name', 'INPUT', array('name' => 'name', 'size' => '20', 'maxlength' => '30', 'class' => "text field medium"), 'trim|required|min_length[2]|max_length[25]|xss_clean', 'tOOL TIP', ''),
            array('Protocal', 'tech', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', '', '', '', 'set_protocal'),
            array('Gateway', 'gateway_id', 'SELECT', '', '', 'tOOL TIP', 'Please select gateway first', 'id', 'name', 'gateways', 'build_dropdown', '', ''),
            array('Provider', 'provider_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'number', 'accounts', 'build_dropdown', 'type', '3'),
            array('Max Channels', 'INPUT', array('name' => 'maxchannels', 'size' => '20', 'maxlength' => '15', 'class' => "text field medium"), 'trim|required|numeric', 'tOOL TIP', ''),
            array('Number Translation', 'INPUT', array('name' => 'dialed_modify', 'size' => '20', 'maxlength' => '200', 'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('Precedence', 'INPUT', array('name' => 'precedence', 'size' => '20', 'maxlength' => '15', 'class' => "text field medium"), 'trim|required|numeric', 'tOOL TIP', ''),
            array('Reseller','reseller_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'number', 'accounts', 'build_dropdown', 'where_arr', array("deleted" => "0", 'type'=> '1','reseller_id'=>"0"), 'multi')
        );
        $form['button_cancel'] = array('name' => 'action', 'content' => 'Cancel', 'value' => 'cancel', 'type' => 'button', 'class' => 'ui-state-default float-right ui-corner-all ui-button', 'onclick' => 'return redirect_page(\'NULL\')');
        $form['button_save'] = array('name' => 'action', 'content' => 'Save', 'value' => 'save', 'id' => 'submit', 'type' => 'button', 'class' => 'ui-state-default float-right ui-corner-all ui-button');
        return $form;
    }

    function get_trunk_search_form() {
        $form['forms'] = array("", array('id' => "trunk_search"));
        $form['Search Trunk'] = array(
            array('', 'HIDDEN', 'ajax_search', '1', '', '', ''),
            array('', 'HIDDEN', 'advance_search', '1', '', '', ''),
            array('Trunk Name', 'INPUT', array('name' => 'name[name]', '', 'size' => '20', 'maxlength' => '15', 'class' => "text field"), '', 'tOOL TIP', '1', 'name[name-string]', '', '', '', 'search_string_type', ''),
            array('Provider', 'provider_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'number', 'accounts', 'build_dropdown', 'type', '3')
        );

        $form['button_search'] = array('name' => 'action', 'id' => "trunk_search_btn", 'content' => 'Search', 'value' => 'save', 'type' => 'button', 'class' => 'ui-state-default float-right ui-corner-all ui-button');
        $form['button_reset'] = array('name' => 'action', 'id' => "id_reset", 'content' => 'Clear Search Filter', 'value' => 'cancel', 'type' => 'reset', 'class' => 'ui-state-default float-right ui-corner-all ui-button');
        return $form;
    }

    function build_trunk_list_for_admin() {
        // array(display name, width, db_field_parent_table,feidname, db_field_child_table,function name);
        $grid_field_arr = json_encode(array(array("<input type='checkbox' name='chkAll' class='checkall'/>", "30", "", "", "", ""),
            array("Trunk Name", "195", "name", "", "", ""),
            array("Protocol", "180", "tech", "", "", ""),
            array("Gateway Name", "190", "gateway_id", "name", "gateways", "get_field_name"),
            array("Provider", "190", "provider_id", "number", "accounts", "get_field_name"),
            array("maxchannels", "100", "maxchannels", "", "", ""),
            array("Precedence", "100", "precedence", "", "", ""),
	    array("TR Count", "80", "id", "trunk_id", "outbound_routes", "get_field_count"),
            array("Action", "60", "", "", "", array("EDIT" => array("url" => "/trunk/trunk_edit/", "mode" => "popup"),
                    "DELETE" => array("url" => "/trunk/trunk_remove/", "mode" => "single")))
                ));
        return $grid_field_arr;
    }

    function build_grid_buttons() {
        $buttons_json = json_encode(array(array("Create trunk", "add", "button_action", "/trunk/trunk_add/", "popup"),
            array("Delete", "delete", "button_action", "/trunk/trunk_delete_multiple/"),
            array("Refresh", "reload", "/accounts/clearsearchfilter/")));
        return $buttons_json;
    }

}

?>
