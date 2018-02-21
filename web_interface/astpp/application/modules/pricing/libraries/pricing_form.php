<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class pricing_form {

    function get_pricing_form_fields() {
        $form['forms'] = array(base_url() . 'pricing/price_save/', array('id' => 'pricing_form', 'method' => 'POST', 'name' => 'pricing_form'));
        $form['Rate Group Add/edit'] = array(
            array('', 'HIDDEN', array('name' => 'id'), '', '', '', ''),
            array('', 'HIDDEN', array('name' => 'status', 'value' => '1'), '', '', ''),
            array('Rate Group Name', 'INPUT', array('name' => 'name', 'size' => '20', 'maxlength' => '15', 'class' => "text field medium"), 'trim|required|min_length[2]|max_length[25]|xss_clean', 'tOOL TIP', 'Please Enter account number'),
            array('Increment', 'INPUT', array('name' => 'inc', 'size' => '20', 'maxlength' => '15', 'class' => "text field medium"), 'trim|required|xss_clean', 'tOOL TIP', 'Please Enter account number'),
            array('Markup(%)', 'INPUT', array('name' => 'markup', 'size' => '20', 'maxlength' => '15', 'class' => "text field medium"), 'trim|required|xss_clean', 'tOOL TIP', 'Please Enter account number'),
        );
        $form['button_cancel'] = array('name' => 'action', 'content' => 'Cancel', 'value' => 'cancel', 'type' => 'button', 'class' => 'ui-state-default float-right ui-corner-all ui-button', 'onclick' => 'return redirect_page(\'NULL\')');
        $form['button_save'] = array('name' => 'action', 'content' => 'Save', 'value' => 'save', 'id' => 'submit', 'type' => 'button', 'class' => 'ui-state-default float-right ui-corner-all ui-button');

        return $form;
    }

    function get_pricing_search_form() {
        $form['forms'] = array("", array('id' => "price_search"));
        $form['Search Rate Group'] = array(
            array('', 'HIDDEN', 'ajax_search', '1', '', '', ''),
            array('', 'HIDDEN', 'advance_search', '1', '', '', ''),
            array('Rate Group Name', 'INPUT', array('name' => 'name[name]', '', 'size' => '20', 'maxlength' => '15', 'class' => "text field"), '', 'tOOL TIP', '1', 'name[name-string]', '', '', '', 'search_string_type', ''),
            array('Increment ', 'INPUT', array('name' => 'inc[inc]', '', 'size' => '20', 'maxlength' => '15', 'class' => "text field"), '', 'tOOL TIP', '1', 'inc[inc-string]', '', '', '', 'search_string_type', ''),
        );
        $form['button_search'] = array('name' => 'action', 'id' => "price_search_btn", 'content' => 'Search', 'value' => 'save', 'type' => 'button', 'class' => 'ui-state-default float-right ui-corner-all ui-button');
        $form['button_reset'] = array('name' => 'action', 'id' => "id_reset", 'content' => 'Clear Search Filter', 'value' => 'cancel', 'type' => 'reset', 'class' => 'ui-state-default float-right ui-corner-all ui-button');

        return $form;
    }

    function build_pricing_list_for_admin() {
        // array(display name, width, db_field_parent_table,feidname, db_field_child_table,function name);
        $grid_field_arr = json_encode(array(array("<input type='checkbox' name='chkAll' class='checkall'/>", "30", "", "", "", ""),
            array("Rate Group Name", "350", "name", "", "", ""),
            array("Increment", "300", "inc", "", "", ""),
            array("Markup(%)", "300", "markup", "", "", ""),
  	    array("OT Count", "100", "id", "pricelist_id", "routes", "get_field_count"),
            array("Action", "85", "", "", "", array("EDIT" => array("url" => "/pricing/price_edit/", "mode" => "popup"),

                    "DELETE" => array("url" => "/pricing/price_delete/", "mode" => "single")))
                ));
        return $grid_field_arr;
    }

    function build_grid_buttons() {
        $buttons_json = json_encode(array(array("ADD Rate Group", "add", "button_action", "/pricing/price_add/", "popup"),
            array("Delete", "delete", "button_action", "/pricing/price_delete_multiple/"),
            array("Refresh", "reload", "/accounts/clearsearchfilter/")));
        return $buttons_json;
    }

}

?>
