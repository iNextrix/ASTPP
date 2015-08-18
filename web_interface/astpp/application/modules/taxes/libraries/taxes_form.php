<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Taxes_form {

    function get_taxes_form_fields() {
        $form['forms'] = array(base_url() . 'taxes/taxes_save/', array('id' => 'taxes_form', 'method' => 'POST', 'name' => 'taxes_form'));
        $form['Taxes Information'] = array(
            array('', 'HIDDEN', array('name' => 'id'), '', '', '', ''),
            array('Name', 'INPUT', array('name' => 'taxes_description', 'size' => '20', 'maxlength' => '255', 'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('Priority', 'INPUT', array('name' => 'taxes_priority', 'size' => '20', 'maxlength' => '5', 'class' => "text field medium"), 'trim|required|numeric', 'tOOL TIP', ''),
            array('Amount', 'INPUT', array('name' => 'taxes_amount', 'size' => '20', 'maxlength' => '10', 'class' => "text field medium"), 'trim|numeric|xss_clean', 'tOOL TIP', ''),
            array('Rate(%)', 'INPUT', array('name' => 'taxes_rate', 'size' => '20', 'maxlength' => '10', 'class' => "text field medium"), 'trim|numeric|xss_clean', 'tOOL TIP', ''),
            array('Status', 'status', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', '', '', '', 'set_status'),

        );
        $form['button_save'] = array('name' => 'action', 'content' => 'Save', 'value' => 'save', 'id' => 'submit', 'type' => 'button', 'class' => 'btn btn-line-parrot');
        $form['button_cancel'] = array('name' => 'action', 'content' => 'Cancel', 'value' => 'cancel', 'type' => 'button', 'class' => 'btn btn-line-sky margin-x-10', 'onclick' => 'return redirect_page(\'NULL\')');
        return $form;
    }

    function get_search_taxes_form() {
        $form['forms'] = array("", array('id' => "taxes_search"));
        $form['Search'] = array(
             array('Name', 'INPUT', array('name' => 'taxes_description[taxes_description]', '', 'size' => '20', 'class' => "text field "), '', 'tOOL TIP', '1', 'taxes_description[taxes_description-string]', '', '', '', 'search_string_type', ''),
            array('Amount', 'INPUT', array('name' => 'taxes_amount[taxes_amount]', 'value' => '', 'size' => '20', 'class' => "text field "), '', 'Tool tips info', '1', 'taxes_amount[taxes_amount-integer]', '', '', '', 'search_int_type', ''),
            array('Rate(%)', 'INPUT', array('name' => 'taxes_rate[taxes_rate]', 'value' => '', 'size' => '20', 'class' => "text field "), '', 'Tool tips info', '1', 'taxes_rate[taxes_rate-integer]', '', '', '', 'search_int_type', ''), array('Status', 'status', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', '', '', '', 'set_search_status', '', ''),   array('', 'HIDDEN', 'ajax_search', '1', '', '', ''),
            array('', 'HIDDEN', 'advance_search', '1', '', '', '')
        );
        $form['button_search'] = array('name' => 'action', 'id' => "taxes_search_btn", 'content' => 'Search', 'value' => 'save', 'type' => 'button', 'class' => 'btn btn-line-parrot pull-right');
        $form['button_reset'] = array('name' => 'action', 'id' => "id_reset", 'content' => 'Clear', 'value' => 'cancel', 'type' => 'reset', 'class' => 'btn btn-line-sky pull-right margin-x-10');
        return $form;
    }

    function build_charge_list_for_admin() {
        // array(display name, width, db_field_parent_table,feidname, db_field_child_table,function name);
        $grid_field_arr = json_encode(array(array("<input type='checkbox' name='chkAll' class='ace checkall'/><label class='lbl'></label>", "30", "", "", "", ""),
            array("Name", "270", "taxes_description", "", "", ""),
            array("Priority", "130", "taxes_priority", "", "", ""),
            array("Amount", "260", "taxes_amount", "", "", "convert_to_currency"),
            array("Rate(%)", "230", "taxes_rate", "", "", ""),
            array("Status", "160", "status", "status", "status", "get_status"),

           array("Action", "175", "", "", "", array("EDIT" => array("url" => "taxes/taxes_edit/", "mode" => "popup"),
                    "DELETE" => array("url" => "taxes/taxes_delete/", "mode" => "single")))
                ));
        return $grid_field_arr;
    }

    function build_grid_buttons() {
        $buttons_json = json_encode(array(array("Create","btn btn-line-warning btn", "fa fa-plus-circle fa-lg", "button_action", "/taxes/taxes_add/", "popup"),
            array("Delete","btn btn-line-danger", "fa fa-times-circle fa-lg", "button_action", "/taxes/taxes_delete_multiple/")));
        return $buttons_json;
    }

}

?>
