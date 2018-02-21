<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Charges_form {

    function get_charegs_form_fields() {
        $form['forms'] = array(base_url() . 'charges/periodiccharges_save/', array('id' => 'chrges_form', 'method' => 'POST', 'name' => 'chrges_form'));
        $form['Card Information'] = array(
            array('', 'HIDDEN', array('name' => 'id'), '', '', '', ''),
            array('Description', 'INPUT', array('name' => 'description', 'size' => '20', 'maxlength' => '15', 'class' => "text field medium"), 'trim|required|min_length[2]|max_length[25]|xss_clean', 'tOOL TIP', 'Please Enter account number'),
            array('Rate Group', 'pricelist_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'name', 'pricelists', 'build_dropdown', 'where_arr', array("reseller_id" => "0", "status <>" => "2")),
            array('Rate', 'INPUT', array('name' => 'charge', 'size' => '20', 'maxlength' => '15', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter account number'),
            array('Cycle', 'sweep_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'sweep', 'sweeplist', 'build_dropdown', '', '')
        );
        $form['button_cancel'] = array('name' => 'action', 'content' => 'Cancel', 'value' => 'cancel', 'type' => 'button', 'class' => 'ui-state-default float-right ui-corner-all ui-button', 'onclick' => 'return redirect_page(\'NULL\')');
        $form['button_save'] = array('name' => 'action', 'content' => 'Save', 'value' => 'save', 'id' => 'submit', 'type' => 'button', 'class' => 'ui-state-default float-right ui-corner-all ui-button');
        return $form;
    }

    function get_charges_search_form() {
        $form['forms'] = array("", array('id' => "charges_search"));
        $form['Search Periodic Charges'] = array(
            array('', 'HIDDEN', 'ajax_search', '1', '', '', ''),
            array('', 'HIDDEN', 'advance_search', '1', '', '', ''),
            array('description', 'INPUT', array('name' => 'description[description]', '', 'size' => '20', 'maxlength' => '15', 'class' => "text field"), '', 'tOOL TIP', '1', 'description[description-string]', '', '', '', 'search_string_type', ''),
            array('charge', 'INPUT', array('name' => 'charge[charge]', 'value' => '', 'size' => '20', 'maxlength' => '15', 'class' => "text field"), '', 'Tool tips info', '1', 'charge[charge-integer]', '', '', '', 'search_int_type', ''),
            array('Account Status', 'status', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', '', '', '', 'set_status'),
            array('Rate Group', 'pricelist_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'name', 'pricelists', 'build_dropdown', '', ''),
            array('Billing Schedule', 'sweep_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'sweep', 'sweeplist', 'build_dropdown', '', '')
        );
        $form['button_search'] = array('name' => 'action', 'id' => "charges_search_btn", 'content' => 'Search', 'value' => 'save', 'type' => 'button', 'class' => 'ui-state-default float-right ui-corner-all ui-button');
        $form['button_reset'] = array('name' => 'action', 'id' => "id_reset", 'content' => 'Clear Search Filter', 'value' => 'cancel', 'type' => 'reset', 'class' => 'ui-state-default float-right ui-corner-all ui-button');

        return $form;
    }

    function build_charge_list_for_admin() {
        // array(display name, width, db_field_parent_table,feidname, db_field_child_table,function name);
        $grid_field_arr = json_encode(array(array("<input type='checkbox' name='chkAll' class='checkall'/>", "30", "", "", "", ""),
            array("Rate Group", "215", "pricelist_id", "name", "pricelists", "get_field_name"),
            array("Description", "245", "description", "", "", ""),
            array("Cost", "225", "charge", "charge", "charge", "convert_to_currency"),
            array("Cycle", "190", "sweep_id", "sweep", "sweeplist", "get_field_name"),
            array("Status", "190", "status", "status", "status", "get_status"),
            array("Action", "60", "", "", "", array("EDIT" => array("url" => "/charges/periodiccharges_edit/", "mode" => "popup", 'popup'),
                    "DELETE" => array("url" => "/charges/periodiccharges_delete/", "mode" => "single")))
                ));
        return $grid_field_arr;
    }

    function build_grid_buttons() {
        $buttons_json = json_encode(array(array("Create Charges", "add", "button_action", "/charges/periodiccharges_add/", "popup"),
            array("Delete", "delete", "button_action", "/charges/periodiccharges_delete_multiple/"),
            array("Refresh", "reload", "/accounts/clearsearchfilter/")));
        return $buttons_json;
    }

}

?>
