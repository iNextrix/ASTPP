<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Package_form {

    function get_package_form_fields() {
        $form['forms'] = array(base_url() . 'package/package_save/',array('id'=>'packeage_form','method'=>'POST','name'=>'packeage_form'));
        $form['Package Add/Edit'] = array(
            array('', 'HIDDEN', array('name' => 'id'), '', '', '', ''),
            array('', 'HIDDEN', array('name' => 'status', 'value' => '1'), '', '', ''),
            array('Package name', 'INPUT', array('name' => 'package_name', 'size' => '20', 'maxlength' => '35', 'class' => "text field medium"), 'trim|required|min_length[2]|max_length[45]|xss_clean', 'tOOL TIP', ''),
            array('Rate Group', 'pricelist_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'name', 'pricelists', 'build_dropdown', 'where_arr', array("status" => "1","reseller_id" => "0")),
            array('Included Seconds', 'INPUT', array('name' => 'includedseconds', 'size' => '20', 'maxlength' => '15', 'class' => "text field medium"), 'trim|required|xss_clean', 'tOOL TIP', ''),
        );
        $form['button_cancel'] = array('name' => 'action', 'content' => 'Cancel', 'value' => 'cancel', 'type' => 'button', 'class' => 'ui-state-default float-right ui-corner-all ui-button', 'onclick' => 'return redirect_page(\'/package/package_list/\')');
        $form['button_save'] = array('name' => 'action', 'content' => 'Save', 'value' => 'save', 'type' => 'submit', 'class' => 'ui-state-default float-right ui-corner-all ui-button');

        return $form;
    }

    function get_package_search_form() {
        $form['forms'] = array("", array('id' => "package_search"));
        $form['Search package'] = array(
            array('', 'HIDDEN', 'ajax_search', '1', '', '', ''),
            array('', 'HIDDEN', 'advance_search', '1', '', '', ''),
            array('Package name', 'INPUT', array('name' => 'package_name[package_name]', '', 'size' => '20', 'maxlength' => '30', 'class' => "text field"), '', 'tOOL TIP', '1', 'package_name[package_name-string]', '', '', '', 'search_string_type', ''),
            array('Rate Group', 'pricelist_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'name', 'pricelists', 'build_dropdown', 'where_arr', array("status" => "1","reseller_id" => "0")),
            array('Included Seconds', 'INPUT', array('name' => 'includedseconds[includedseconds]', 'value' => '', 'size' => '20', 'maxlength' => '15', 'class' => "text field"), '', 'Tool tips info', '1', 'includedseconds[includedseconds-integer]', '', '', '', 'search_int_type', ''),
        );
        $form['button_search'] = array('name' => 'action', 'id' => "package_search_btn", 'content' => 'Search', 'value' => 'save', 'type' => 'button', 'class' => 'ui-state-default float-right ui-corner-all ui-button');
        $form['button_reset'] = array('name' => 'action', 'id' => "id_reset", 'content' => 'Clear Search Filter', 'value' => 'cancel', 'type' => 'reset', 'class' => 'ui-state-default float-right ui-corner-all ui-button');

        return $form;
    }

    function build_package_list_for_admin() {
        // array(display name, width, db_field_parent_table,feidname, db_field_child_table,function name);
        $grid_field_arr = json_encode(array(array("<input type='checkbox' name='chkAll' class='checkall'/>", "30", "", "", "", ""),
            array("Package Name", "250", "package_name", "", "", ""),
            array("Rate Group", "250", "pricelist_id", "name", "pricelists", "get_field_name"),
            array("Included Seconds", "250", "includedseconds", "", "", ""),
            array("Action", "60", "", "", "", array("EDIT" => array("url" => "/package/package_edit/", "mode" => "single"),
                    "DELETE" => array("url" => "/package/package_delete/", "mode" => "single")))
                ));
        return $grid_field_arr;
    }

    function build_grid_buttons() {
        $buttons_json = json_encode(array(array("Create Package", "add", "button_action", "/package/package_add/"),
            array("DELETE", "delete", "button_action", "/package/package_delete_multiple/"),
            array("Refresh", "reload", "/accounts/clearsearchfilter/")));
        return $buttons_json;
    }

    function build_charges_list_for_customer($accountid, $accounttype) {
        $grid_field_arr = json_encode(array(array("Description", "180", "description", "", "", ""),
            array("Charge", "160", "charge", "", "", ""),
            array("Cycle", "160", "sweep_id", "sweep", "sweeplist", "get_field_name"),
            array("Action", "30", "", "", "", array("DELETE" => array("url" => "/accounts/customer_charges_action/delete/$accountid/$accounttype/", "mode" => "single")))
                ));
        return $grid_field_arr;
    }

    function build_package_counter_list_for_admin() {
        // array(display name, width, db_field_parent_table,feidname, db_field_child_table,function name);
        $grid_field_arr = json_encode(array(array("Package Name", "200", "package_id", "package_name", "packages", "get_field_name"),
            array("Account Number", "200", "accountid", "number", "accounts", "get_field_name"),
            array("Used Seconds", "200", "seconds", "", "", ""),
                ));
        return $grid_field_arr;
    }
    function build_pattern_list_for_customer($packageid) {
        $grid_field_arr = json_encode(array(array("Patterns", "100", "patterns", "patterns", "", "get_only_numeric_val"),
            array("Action", "30", "", "", "", array("DELETE" => array("url" => "/package/customer_delete_package_pattern/$packageid/", "mode" => "single")))
                ));
        return $grid_field_arr;
    }

    function set_pattern_grid_buttons($packageid) {
        $buttons_json = json_encode(array(array("ADD Patterns", "add", "button_action", "/package/customer_add_patterns/$packageid", "popup")));
        return $buttons_json;
    }

}

?>
