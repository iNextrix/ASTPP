<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Taxes_form {

    function get_taxes_form_fields() {
        $form['forms'] = array(base_url() . 'taxes/taxes_save/', array('id' => 'taxes_form', 'method' => 'POST', 'name' => 'taxes_form'));
        $form['Taxes Information'] = array(
            array('', 'HIDDEN', array('name' => 'id'), '', '', '', ''),
            array('Priority', 'INPUT', array('name' => 'taxes_priority', 'size' => '20', 'maxlength' => '15', 'class' => "text field medium"), 'trim|required|numeric', 'tOOL TIP', ''),
            array('Amount', 'INPUT', array('name' => 'taxes_amount', 'size' => '20', 'maxlength' => '15', 'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('Rate(%)', 'INPUT', array('name' => 'taxes_rate', 'size' => '20', 'maxlength' => '15', 'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('Description', 'INPUT', array('name' => 'taxes_description', 'size' => '20', 'maxlength' => '15', 'class' => "text field medium"), '', 'tOOL TIP', '')
        );
        $form['button_save'] = array('name' => 'action', 'content' => 'Save', 'value' => 'save', 'id' => 'submit', 'type' => 'button', 'class' => 'ui-state-default float-right ui-corner-all ui-button');
        $form['button_cancel'] = array('name' => 'action', 'content' => 'Cancel', 'value' => 'cancel', 'type' => 'button', 'class' => 'ui-state-default float-right ui-corner-all ui-button', 'onclick' => 'return redirect_page(\'NULL\')');
        return $form;
    }

    function get_search_taxes_form() {
        $form['forms'] = array("", array('id' => "taxes_search"));
        $form['Search Taxes'] = array(
            array('', 'HIDDEN', 'ajax_search', '1', '', '', ''),
            array('', 'HIDDEN', 'advance_search', '1', '', '', ''),
            array('Amount', 'INPUT', array('name' => 'taxes_amount[taxes_amount]', 'value' => '', 'size' => '20', 'maxlength' => '15', 'class' => "text field "), '', 'Tool tips info', '1', 'taxes_amount[taxes_amount-integer]', '', '', '', 'search_int_type', ''),
            array('Rate', 'INPUT', array('name' => 'taxes_rate[taxes_rate]', 'value' => '', 'size' => '20', 'maxlength' => '15', 'class' => "text field "), '', 'Tool tips info', '1', 'taxes_rate[taxes_rate-integer]', '', '', '', 'search_int_type', ''),
            array('Description', 'INPUT', array('name' => 'taxes_description[taxes_description]', '', 'size' => '20', 'maxlength' => '15', 'class' => "text field "), '', 'tOOL TIP', '1', 'taxes_description[taxes_description-string]', '', '', '', 'search_string_type', '')
        );
        $form['button_search'] = array('name' => 'action', 'id' => "taxes_search_btn", 'content' => 'Search', 'value' => 'save', 'type' => 'button', 'class' => 'ui-state-default float-right ui-corner-all ui-button');
        $form['button_reset'] = array('name' => 'action', 'id' => "id_reset", 'content' => 'Clear Search Filter', 'value' => 'cancel', 'type' => 'reset', 'class' => 'ui-state-default float-right ui-corner-all ui-button');
        return $form;
    }

    function build_charge_list_for_admin() {
        // array(display name, width, db_field_parent_table,feidname, db_field_child_table,function name);
        $grid_field_arr = json_encode(array(array("<input type='checkbox' name='chkAll' class='checkall'/>", "30", "", "", "", ""),
            array("Priority", "240", "taxes_priority", "", "", ""),
            array("Amount", "280", "taxes_amount", "", "", "convert_to_currency"),
            array("Taxe Rate", "238", "taxes_rate", "", "", ""),
            array("Description", "320", "taxes_description", "", "", ""),
            array("Action", "60", "", "", "", array("EDIT" => array("url" => "/taxes/taxes_edit/", "mode" => "popup"),
                    "DELETE" => array("url" => "/taxes/taxes_delete/", "mode" => "single")))
                ));
        return $grid_field_arr;
    }

    function build_grid_buttons() {
        $buttons_json = json_encode(array(array("Add Taxes", "add", "button_action", "/taxes/taxes_add/", "popup"),
            array("DELETE", "delete", "button_action", "/taxes/taxes_delete_multiple/"),
            array("Refresh", "reload", "/accounts/clearsearchfilter/")));
        return $buttons_json;
    }

}

?>
