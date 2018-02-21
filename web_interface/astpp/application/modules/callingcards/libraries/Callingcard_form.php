<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Callingcard_form {

    function get_callingcard_form_fields() {
        $form['forms'] = array(base_url() . '/callingcards/callingcards_save/',array("id" => "callingcard_form", "name" => "callingcard_form"));
        $form['Card Information'] = array(
            array('', 'HIDDEN', array('name' => 'id'), '', '', '', ''),
            array('Account Number', 'INPUT', array('name' => 'account_id', 'size' => '20', 'maxlength' => '15', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter account number'),
            array('Brand', 'brand_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'name', 'callingcardbrands', 'build_dropdown', '', ''),
            array('Balance', 'INPUT', array('name' => 'value', 'size' => '20', 'maxlength' => '15', 'class' => "text field medium"), 'trim|required|numeric', 'tOOL TIP', 'Please Enter account number'),
            array('Quantity', 'INPUT', array('name' => 'count', 'size' => '20', 'maxlength' => '15', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter account number'),
            array('Status', 'status', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', '', '', '', 'set_status_callingcard')
        );
        $form['button_cancel'] = array('name' => 'action', 'content' => 'Cancel', 'value' => 'cancel', 'type' => 'button', 'class' => 'ui-state-default float-right ui-corner-all ui-button', 'onclick' => 'return redirect_page(\'NULL\')');
        $form['button_save'] = array('name' => 'action', 'content' => 'Save', 'value' => 'save', 'id' => 'submit', 'type' => 'button', 'class' => 'ui-state-default float-right ui-corner-all ui-button');

        return $form;
    }

    function get_callingcard_updatestatus_form_fields() {
        $form['forms'] = array(base_url() . '/callingcards/callingcards_update_status/',array("id" => "callingcard_update_form", "name" => "callingcard_update_form"));
        $form['Change Calling Card Status'] = array(
            array('', 'HIDDEN', array('name' => 'id'), '', '', '', ''),
            array('starting Sequence Number', 'INPUT', array('name' => 'start_no', 'size' => '20', 'maxlength' => '15', 'class' => "text field medium"), 'trim|required|numeric', 'tOOL TIP', 'Please Enter account number'),
            array('Ending Sequence Number', 'INPUT', array('name' => 'end_no', 'size' => '20', 'maxlength' => '15', 'class' => "text field medium"), 'trim|required|numeric', 'tOOL TIP', 'Please Enter account number'),
            array('Status', 'status', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', '', '', '', 'set_status_callingcard')
        );

        $form['button_cancel'] = array('name' => 'action', 'content' => 'Cancel', 'value' => 'cancel', 'type' => 'button', 'class' => 'ui-state-default float-right ui-corner-all ui-button', 'onclick' => 'return redirect_page(\'/callingcards/callingcards_list/\')');
        $form['button_save'] = array('name' => 'action', 'content' => 'Save', 'value' => 'save', 'type' => 'submit', 'class' => 'ui-state-default float-right ui-corner-all ui-button');

        return $form;
    }

    function get_callingcard_refill_form_fields($id, $card_number) {
        $form['forms'] = array(base_url() . '/callingcards/callingcards_refill/',array("id" => "callingcard_refill", "name" => "callingcard_refill"));
        $form['Refill Calling Card '] = array(
            array('', 'HIDDEN', array('name' => 'id', 'value' => $id), '', '', '', ''),
            array('Card Number', 'INPUT', array('name' => 'cardnumber', 'size' => '20', 'value' => $card_number, 'readonly' => true, 'maxlength' => '15', 'class' => "text field medium"), 'trim|required|numeric', 'tOOL TIP', 'Please Enter account number'),
            array('Amount', 'INPUT', array('name' => 'value', 'size' => '20', 'maxlength' => '15', 'value' => '0.00', 'class' => "text field medium"), 'trim|required|numeric', 'tOOL TIP', 'Please Enter account number'),
        );
        $form['button_cancel'] = array('name' => 'action', 'content' => 'Cancel', 'value' => 'cancel', 'type' => 'button', 'class' => 'ui-state-default float-right ui-corner-all ui-button', 'onclick' => 'return redirect_page(\'/callingcards/callingcards_list/\')');
        $form['button_save'] = array('name' => 'action', 'content' => 'Save', 'value' => 'save', 'type' => 'submit', 'class' => 'ui-state-default float-right ui-corner-all ui-button');

        return $form;
    }

    function get_ccbrands_form_fields() {
        $form['forms'] = array(base_url() . '/callingcards/brands_save/', array("id" => "cc_brand_form", "name" => "cc_brand_form"));
        $form['CC Brand Setting'] = array(
            array('', 'HIDDEN', array('name' => 'id'), '', '', '', ''),
            array('CC Brand Name', 'INPUT', array('name' => 'name', 'size' => '20', 'maxlength' => '15', 'class' => "text field medium"), 'trim|required', 'tOOL TIP', 'Please Enter account number'),
            array('Pin Required', 'pin', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', '', '', '', 'set_allow'),
            array('Rate Group', 'pricelist_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'name', 'pricelists', 'build_dropdown', '', ''),
            array('Language', 'language_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'languagename', 'language', 'build_dropdown', '', ''),
            array('Days Valid For', 'INPUT', array('name' => 'validfordays', 'size' => '20', 'maxlength' => '15', 'class' => "text field medium"), 'trim|required|numeric', 'tOOL TIP', 'Please Enter account number'),
            array('MaintenanceFee', 'INPUT', array('name' => 'maint_fee_pennies', 'size' => '20', 'maxlength' => '15', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter account number'),
            array('Days Between Maintain Fee', 'INPUT', array('name' => 'maint_fee_days', 'size' => '20', 'maxlength' => '15', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter account number'),
            array('Disconnect Fee', 'INPUT', array('name' => 'disconnect_fee_pennies', 'size' => '20', 'maxlength' => '15', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter account number'),
            array('Charge after X minutes', 'INPUT', array('name' => 'minute_fee_pennies', 'size' => '20', 'maxlength' => '15', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter account number'),
            array('Minutes used before charge', 'INPUT', array('name' => 'minute_fee_minutes', 'size' => '20', 'maxlength' => '15', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter account number'),
            array('Minimum length thats not charged extra (minutes)', 'INPUT', array('name' => 'min_length_minutes', 'size' => '20', 'maxlength' => '15', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter account number'),
            array('Extra charge for short calls', 'INPUT', array('name' => 'min_length_pennies', 'size' => '20', 'maxlength' => '15', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter account number')
        );

        $form['button_cancel'] = array('name' => 'action', 'content' => 'Cancel', 'value' => 'cancel', 'type' => 'button', 'class' => 'ui-state-default float-right ui-corner-all ui-button', 'onclick' => 'return redirect_page(\'NULL\')');
        $form['button_save'] = array('name' => 'action', 'content' => 'Save', 'value' => 'save', 'id' => 'submit', 'type' => 'button', 'class' => 'ui-state-default float-right ui-corner-all ui-button');

        return $form;
    }

    function get_ccbrands_search_form() {
        $form['forms'] = array("", array('id' => "ccbrand_search"));
        $form['Search Calling Card'] = array(
            array('', 'HIDDEN', 'ajax_search', '1', '', '', ''),
            array('', 'HIDDEN', 'advance_search', '1', '', '', ''),
            array('Cc Brand Name', 'INPUT', array('name' => 'name[name]', '', 'id' => 'cardnumber', 'size' => '15', 'maxlength' => '15', 'class' => "text field "), '', 'tOOL TIP', '1', 'name[name-string]', '', '', '', 'search_string_type', ''),
            array('Rate Group', 'pricelist_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'name', 'pricelists', 'build_dropdown', '', ''),
            array('Days valid for', 'INPUT', array('name' => 'validfordays[validfordays]', '', 'id' => 'cardnumber', 'size' => '15', 'maxlength' => '15', 'class' => "text field"), '', 'tOOL TIP', '1', 'validfordays[validfordays-string]', '', '', '', 'search_string_type', ''),
            array('Status', 'status', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', '', '', '', 'set_allow', '', ''),
        );

        $form['button_search'] = array('name' => 'action', 'id' => "ccbrands_search_btn", 'content' => 'Search', 'value' => 'save', 'type' => 'button', 'class' => 'ui-state-default float-right ui-corner-all ui-button');
        $form['button_reset'] = array('name' => 'action', 'id' => "id_reset", 'content' => 'Clear Search Filter', 'value' => 'cancel', 'type' => 'reset', 'class' => 'ui-state-default float-right ui-corner-all ui-button');

        return $form;
    }

    function get_callingcards_search_form() {

        $form['forms'] = array("", array('id' => "callingcard_search"));
        $form['Search Calling Card'] = array(
            array('', 'HIDDEN', 'ajax_search', '1', '', '', ''),
            array('', 'HIDDEN', 'advance_search', '1', '', '', ''),
            array('Card Number', 'INPUT', array('name' => 'cardnumber[cardnumber]', '', 'id' => 'cardnumber', 'size' => '15', 'maxlength' => '15', 'class' => "text field "), '', 'tOOL TIP', '1', 'cardnumber[cardnumber-string]', '', '', '', 'search_string_type', ''),
            array('Brand', 'brand_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'name', 'callingcardbrands', 'build_dropdown', '', ''),
            array('Balance', 'INPUT', array('name' => 'value[value]', 'value' => '', 'size' => '20', 'maxlength' => '15', 'class' => "text field "), '', 'Tool tips info', '1', 'value[value-integer]', '', '', '', 'search_int_type', ''),
            array('Balance Used', 'INPUT', array('name' => 'used[used]', 'value' => '', 'size' => '20', 'maxlength' => '15', 'class' => "text field "), '', 'Tool tips info', '1', 'used[used-integer]', '', '', '', 'search_int_type', ''),
            array('In Use', 'inuse', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', '', '', '', 'set_allow', '', ''),
        );
        $form['button_search'] = array('name' => 'action', 'id' => "callingcard_search_btn", 'content' => 'Search', 'value' => 'save', 'type' => 'button', 'class' => 'ui-state-default float-right ui-corner-all ui-button');
        $form['button_reset'] = array('name' => 'action', 'id' => "id_reset", 'content' => 'Clear Search Filter', 'value' => 'cancel', 'type' => 'reset', 'class' => 'ui-state-default float-right ui-corner-all ui-button');

        return $form;
    }

    function build_ccbrand_list_for_admin() {
        // array(display name, width, db_field_parent_table,feidname, db_field_child_table,function name);
        $grid_field_arr = json_encode(array(array("CC Brand Name", "90", "name", "", "", ""),
            array("PIN Required", "80", "pin","pin", "pin", "get_allow"),
            array("Rate Group", "100", "pricelist_id", "name", "pricelists", "get_field_name"),
            array("Day <br>Valid for", "50", "validfordays", "", "", ""),
            array("Maintenance<br>Fee", "95", "maint_fee_pennies", "", "", ""),
            array("Day Between<br>Maintenance Fee", "90", "maint_fee_days", "", "", ""),
            array("Disconnect<br>Fee", "90", "disconnect_fee_pennies", "", "", ""),
            array("Charge after<br>X Mins", "70", "minute_fee_pennies", "", "", ""),
            array("Mins used<br>before charge", "82", "minute_fee_minutes", "", "", ""),
            array("Min Length Not<br>charge for Extra Mins", "120", "min_length_minutes", "", "", ""),
            array("chrge for<br> Short Call", "90", "min_length_pennies", "", "", ""),
            array("Status", "60", "status", "status", "status", "get_status"),
            array("Action", "60", "", "", "", array(
                    "EDIT" => array("url" => "/callingcards/brands_edit/", "mode" => "popup"),
                    "DELETE" => array("url" => "callingcards/brands_delete/", "mode" => "single")
            ))
                ));
        return $grid_field_arr;
    }

    function build_cc_list_for_admin() {
        // array(display name, width, db_field_parent_table,feidname, db_field_child_table,function name);
        $grid_field_arr = json_encode(array(array("Account Number", "87", "account_id", "number", "accounts", "get_field_name"),
            array("Sequence", "50", "id", "", "", ""),
            array("Card Number", "94", "cardnumber", "", "", ""),
            array("Pin", "52", "pin", "", "", ""),
            array("Brand", "85", "brand_id", "name", "callingcardbrands", "get_field_name"),
            array("Balance*", "70", "value", "", "", ""),
            array("Used<br>Balance", "60", "used", "", "", ""),
            array("Days Valid For", "70", "validfordays", "", "", ""),
            array("Creation", "107", "created", "", "", ""),
            array("First Use", "107", "firstused", "", "", ""),
            array("Expiration", "107", "expiry", "", "", ""),
            array("In Use?", "50", "inuse", "status", "status", "get_status"),
            array("Status", "50", "status", "status", "status", "get_status"),
            array("Action", "75", "", "", "", array("PAYMENT" => array("url" => "callingcards/callingcards_refill/", "mode" => "single"),
                    "VIEW" => array("url" => "callingcards/callingcards_view/", "mode" => "popup"),
                    "CALLERID" => array("url" => "callingcards/callingcards_add_callerid/", "mode" => "popup"),
                    "DELETE" => array("url" => "callingcards/callingcards_delete/", "mode" => "single")
            ))
                ));
        return $grid_field_arr;
    }

    function build_grid_buttons() {
        $buttons_json = json_encode(array(array("Add Calling Card", "add", "button_action", "/callingcards/callingcards_add/", "popup"),
            array("Update Status", "add", "button_action", "/callingcards/callingcards_update_status/"),
            array("Refresh", "reload", "/accounts/clearsearchfilter/")));
        return $buttons_json;
    }

    function build_grid_buttons_ccbrand() {
        $buttons_json = json_encode(array(array("Add cc Brand", "add", "button_action", "/callingcards/brands_add/", "popup"),
            array("Refresh", "reload", "/accounts/clearsearchfilter/")));
        return $buttons_json;
    }

    function build_template_list_for_admin() {
        // array(display name, width, db_field_parent_table,feidname, db_field_child_table,function name);
        $grid_field_arr = json_encode(array(array("Name", "100", "name", "", "", ""),
            array("Subject", "120", "subject", "", "", ""),
            array("Template", "930", "template", "", "", ""),
            array("Action", "50", "", "", "", "set_grid_action_buttons")
                ));
        return $grid_field_arr;
    }

    function build_cdrreport_list_for_admin() {
        // array(display name, width, db_field_parent_table,feidname, db_field_child_table,function name);
        $grid_field_arr = json_encode(array(array("Date", "130", "callstart", "", "", ""),
            array("CallerID", "100", "clid", "", "", ""),
            array("Destination", "100", "destination", "", "", ""),
            array("Code", "100", "pattern", "pattern", "", "get_only_numeric_val"),
            array("Code Name", "150", "notes", "", "", ""),
            array("Bill Seconds", "100", "seconds", "", "", "get_field_name"),
            array("Debit", "85", "debit", "", "", ""),
            array("Disposition", "150", "disposition", "", "", ""),
            array("Card Number", "100", "callingcard_id", "cardnumber", "callingcards", "get_field_name"),
            array("Rate Group", "100", "pricelist_id", "name", "pricelists", "get_field_name"),
                ));
        return $grid_field_arr;
    }

    function build_grid_buttons_cdrs() {
        $buttons_json = json_encode(array(array("Refresh", "reload", "/accounts/clearsearchfilter/")));
        return $buttons_json;
    }

    function get_cc_cdr_form() {
        $form['forms'] = array("", array('id' => "cdr_cc_search"));
        $form['Search Calling Cards'] = array(
            array('', 'HIDDEN', 'ajax_search', '1', '', '', ''),
            array('', 'HIDDEN', 'advance_search', '1', '', '', ''),
            array('From date & Time', 'INPUT', array('name' => 'callstart[]', 'id' => 'customer_cdr_from_date', 'size' => '20', 'maxlength' => '25', 'class' => "text field "), '', 'tOOL TIP', '', 'start_date[]'),
            array('To date & Time', 'INPUT', array('name' => 'callstart[]', 'id' => 'customer_cdr_to_date', 'size' => '20', 'maxlength' => '25', 'class' => "text field "), '', 'tOOL TIP', '', 'end_date[]'),
            array('Caller id', 'INPUT', array('name' => 'clid[clid]', '', 'id' => 'first_name', 'size' => '15', 'maxlength' => '25', 'class' => "text field "), '', 'tOOL TIP', '1', 'clid[clid-string]', '', '', '', 'search_string_type', ''),
            array('Desc', 'INPUT', array('name' => 'destination[destination]', 'value' => '', 'size' => '20', 'maxlength' => '45', 'class' => "text field "), '', 'Tool tips info', '1', 'destination[destination-string]', '', '', '', 'search_string_type', ''),
            array('Bill Sec ', 'INPUT', array('name' => 'seconds[seconds]', 'value' => '', 'size' => '20', 'maxlength' => '15', 'class' => "text field "), '', 'Tool tips info', '1', 'seconds[seconds-integer]', '', '', '', 'search_int_type', ''),
            array('Despostion', 'disposition', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', '', '', '', 'set_despostion'),
            array('Debit ', 'INPUT', array('name' => 'debit[debit]', 'value' => '', 'size' => '20', 'maxlength' => '15', 'class' => "text field "), '', 'Tool tips info', '1', 'debit[debit-integer]', '', '', '', 'search_int_type', ''),
            array('Rate Group', 'pricelist_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'name', 'pricelists', 'build_dropdown', '', ''),
            array('Code ', 'INPUT', array('name' => 'pattern[pattern]', 'value' => '', 'size' => '20', 'maxlength' => '15', 'class' => "text field "), '', 'Tool tips info', '1', 'pattern[pattern-integer]', '', '', '', 'search_int_type', ''),
        );

        $form['button_search'] = array('name' => 'action', 'id' => "cc_cdr_search_btn", 'content' => 'Search', 'value' => 'save', 'type' => 'button', 'class' => 'ui-state-default float-right ui-corner-all ui-button');
        $form['button_reset'] = array('name' => 'action', 'id' => "id_reset", 'content' => 'Clear Search Filter', 'value' => 'cancel', 'type' => 'reset', 'class' => 'ui-state-default float-right ui-corner-all ui-button');

        return $form;
    }

    function get_cc_callerid_fields($mode) {
        $form['forms'] = array(base_url() . '/callingcards/callingcards_add_callerid/', array("id" => "callerid_form", "name" => "callerid_form"));
        $form['callerid'] = array(
            array('', 'HIDDEN', array('name' => 'callingcard_id'), '', '', '', ''),
            array('Callingcard Number', 'INPUT', array('name' => 'callingcard_number', 'size' => '20', 'readonly' => true, 'maxlength' => '15', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter account number'),
            array('Status', 'status', 'CHECKBOX', array('name' => 'status', 'value' => 'on', 'checked' => false), '', 'tOOL TIP', ''),
            array('Caller Id Name', 'INPUT', array('name' => 'callerid_name', 'size' => '20', 'maxlength' => '25', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter account number'),
            array('Caller Id Number', 'INPUT', array('name' => 'callerid_number', 'size' => '20', 'maxlength' => '25', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter Password'),
            array('', 'HIDDEN', array('name' => $mode), '', '', '')
        );
        $form['button_save'] = array('name' => 'action', 'content' => 'Save', 'value' => 'save', 'type' => 'submit', 'class' => 'ui-state-default float-right ui-corner-all ui-button');
        return $form;
    }

}

?>
