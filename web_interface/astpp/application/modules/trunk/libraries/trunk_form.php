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

class trunk_form {

    function get_trunk_form_fields() {
        $form['forms'] = array(base_url() . 'trunk/trunk_save/', array('id' => 'trunks_form', 'method' => 'POST', 'name' => 'trunks_form'));
        $form['Information'] = array(
            array('', 'HIDDEN', array('name' => 'id'), '', '', '', ''),
            array('Name', 'INPUT', array('name' => 'name', 'size' => '20', 'class' => "text field medium"), 'trim|required|xss_clean', 'tOOL TIP', ''),
			array('Provider', 'provider_id', 'SELECT', '', 'trim|required|xss_clean', 'tOOL TIP', 'Please Enter account number', 'id', 'first_name,last_name,number', 'accounts', 'build_concat_dropdown', 'where_arr',array('type'=>3,"deleted"=>"0","status"=>"0")),
            array('Gateway Name', 'gateway_id', 'SELECT', '', 'trim|required|xss_clean', 'tOOL TIP', 'Please select gateway first', 'id', 'name', 'gateways', 'build_dropdown','where_arr', array("status" => "0")),
			array('Failover GW Name #1', 'failover_gateway_id', 'SELECT', '', '', 'tOOL TIP', 'Please select gateway first', 'id', 'name', 'gateways', 'build_dropdown', 'where_arr', array("status" => "0")),
			array('Failover GW Name #2', 'failover_gateway_id1', 'SELECT', '', '', 'tOOL TIP', 'Please select gateway first', 'id', 'name', 'gateways', 'build_dropdown', 'where_arr', array("status" => "0")),            
            array('CC', 'INPUT', array('name' => 'maxchannels', 'value' => '0' , 'size' => '20',  'class' => "text field medium"), '', 'tOOL TIP', ''));
        $form['Settings'] = array(  
            array('Number Translation', 'INPUT', array('name' => 'dialed_modify', 'size' => '20',  'class' => "text field medium"), '', 'tOOL TIP', ''),
			array('Codecs', 'INPUT', array('name' => 'codec', 'size' => '20', 'class' => "text field medium"), 'trim|xss_clean', 'tOOL TIP', ''),
            array('Priority', 'INPUT', array('name' => 'precedence', 'size' => '20',  'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('Status', 'status', 'SELECT', '', '', 'tOOL TIP', 'Please Select Status', '', '', '', 'set_status'),
        );
        $form['button_cancel'] = array('name' => 'action', 'content' => 'Close', 'value' => 'cancel', 'type' => 'button', 'class' => 'btn btn-line-sky margin-x-10', 'onclick' => 'return redirect_page(\'NULL\')');
        $form['button_save'] = array('name' => 'action', 'content' => 'Save', 'value' => 'save', 'id' => 'submit', 'type' => 'button', 'class' => 'btn btn-line-parrot');
        return $form;
    }

    function get_trunk_search_form() {
        $form['forms'] = array("", array('id' => "trunk_search"));
        $form['Search'] = array(
            array('Name', 'INPUT', array('name' => 'name[name]', '', 'size' => '20', 'class' => "text field"), '', 'tOOL TIP', '1', 'name[name-string]', '', '', '', 'search_string_type', ''),
            array('Provider', 'provider_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'first_name,last_name,number', 'accounts', 'build_concat_dropdown', 'where_arr',array('type'=>3, "status"=>0, "deleted" => 0)),
            array('Gateway Name', 'gateway_id', 'SELECT', '', '', 'tOOL TIP', 'Please select gateway first', 'id', 'name', 'gateways', 'build_dropdown','where_arr', array("status" => "0")),
			array('Status', 'status', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', '', '', '', 'set_search_status', '', ''),
			array('', 'HIDDEN', 'ajax_search', '1', '', '', ''),
            array('', 'HIDDEN', 'advance_search', '1', '', '', ''),
        );
        $form['button_search'] = array('name' => 'action', 'id' => "trunk_search_btn", 'content' => 'Search', 'value' => 'save', 'type' => 'button', 'class' => 'btn btn-line-parrot pull-right');
        $form['button_reset'] = array('name' => 'action', 'id' => "id_reset", 'content' => 'Clear', 'value' => 'cancel', 'type' => 'reset', 'class' => 'btn btn-line-sky pull-right margin-x-10');
        return $form;
    }

    function build_trunk_list_for_admin() {
        
        $grid_field_arr = json_encode(array(array("<input type='checkbox' name='chkAll' class='ace checkall'/><label class='lbl'></label>", "30", "", "", "", "","","false","center"),
            array("Name", "100", "name", "", "", "","EDITABLE","true","center"),
            array("Provider", "110", "provider_id", "first_name,last_name,number", "accounts", "build_concat_string","","true","center"),
            array("Gateway Name", "100", "gateway_id", "name", "gateways", "get_field_name","","true","center"),
			array("Failover <br>GW Name #1", "130", "failover_gateway_id","name", "gateways", "get_field_name","","true","center"),
            array("Failover<br> GW Name #2", "130", "failover_gateway_id1","name", "gateways", "get_field_name","","true","center"),
			array("CC", "90", "maxchannels", "", "", "","","true","center"),
            array("Codecs", "90", "codec", "", "", "","","true","center"),
			array("Rate<br>Count", "70", "id", "trunk_id", "outbound_routes", "get_field_count","","true","center"),
            array("Status", "100", "status", "status", "trunks", "get_status","","true","center"),
            array("Created Date", "110", "creation_date", "creation_date", "creation_date", "convert_GMT_to"),
            array("Modified <br/>Date", "105", "last_modified_date", "last_modified_date", "last_modified_date", "convert_GMT_to","","true","center"),
			array("Action", "100", "", "", "", array("EDIT" => array("url" => "trunk/trunk_edit/", "mode" => "popup","layout"=>"medium"),
                    "DELETE" => array("url" => "trunk/trunk_remove/", "mode" => "single")))
                ));
			return $grid_field_arr;
    }

    function build_grid_buttons() {
        $buttons_json = json_encode(array(array("Create","btn btn-line-warning btn", "fa fa-plus-circle fa-lg", "button_action", "/trunk/trunk_add/", "popup","medium"),
            array("Delete", "btn btn-line-danger", "fa fa-times-circle fa-lg", "button_action", "/trunk/trunk_delete_multiple/")
            ));
        return $buttons_json;
    }

}

?>
