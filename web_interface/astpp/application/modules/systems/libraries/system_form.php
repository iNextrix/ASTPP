<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class System_form {

    function get_template_form_fields() {

        $form['forms'] = array(base_url() . 'systems/template_save/', array("template_form", "name" => "template_form"));
        $form['Email Template'] = array(
            array('', 'HIDDEN', array('name' => 'id'), '', '', '', ''),
            array('Template Name', 'INPUT', array('name' => 'name', 'size' => '20', 'maxlength' => '15', 'readonly' => true, 'class' => "text field medium"), 'trim|required|min_length[2]|max_length[80]|xss_clean', 'tOOL TIP', ''),
            array('Subject', 'INPUT', array('name' => 'subject', 'size' => '20', 'maxlength' => '15', 'class' => "text field medium"), 'trim|required', 'tOOL TIP', ''),
            array('Body', 'TEXTAREA', array('name' => 'template', 'id' => 'template', 'size' => '20', 'maxlength' => '1000', 'class' => "textarea medium"), 'trim|required', 'tOOL TIP', ''),
        );
        $form['button_cancel'] = array('name' => 'action', 'content' => 'Cancel', 'value' => 'cancel', 'type' => 'button', 'class' => 'ui-state-default float-right ui-corner-all ui-button', 'onclick' => 'return redirect_page(\'/systems/template/\')');
        $form['button_save'] = array('name' => 'action', 'content' => 'Save', 'value' => 'save', 'type' => 'submit', 'class' => 'ui-state-default float-right ui-corner-all ui-button');

        return $form;
    }

    function get_template_search_form() {
        $form['forms'] = array("", array('id' => "template_search"));
        $form['Search Email template'] = array(
            array('', 'HIDDEN', 'ajax_search', '1', '', '', ''),
            array('', 'HIDDEN', 'advance_search', '1', '', '', ''),
            array('Template Name', 'INPUT', array('name' => 'name[name]', '', 'size' => '20', 'maxlength' => '15', 'class' => "text field "), '', 'tOOL TIP', '1', 'name[name-string]', '', '', '', 'search_string_type', ''),
            array('Subject', 'INPUT', array('name' => 'subject[subject]', '', 'size' => '20', 'maxlength' => '15', 'class' => "text field "), '', 'tOOL TIP', '1', 'subject[subject-string]', '', '', '', 'search_string_type', ''),
//             array('Body', 'INPUT', array('name' => 'template[template]', '', 'size' => '20', 'maxlength' => '15', 'class' => "text field "), '', 'tOOL TIP', '1', 'template[template-string]', '', '', '', 'search_string_type', ''),
        );
        $form['button_search'] = array('name' => 'action', 'id' => "template_search_btn", 'content' => 'Search', 'value' => 'save', 'type' => 'button', 'class' => 'ui-state-default float-right ui-corner-all ui-button');
        $form['button_reset'] = array('name' => 'action', 'id' => "id_reset", 'content' => 'Clear Search Filter', 'value' => 'cancel', 'type' => 'reset', 'class' => 'ui-state-default float-right ui-corner-all ui-button');
        return $form;
    }

    function get_configuration_form_fields() {

        $form['forms'] = array(base_url() . 'systems/configuration_save/', array("id" => "config_form", "name" => "config_form"));
        $form['Edit System Configuration '] = array(
            array('', 'HIDDEN', array('name' => 'id'), '', '', '', ''),
            array('Name', 'INPUT', array('name' => 'name', 'size' => '20', 'maxlength' => '15', 'readonly' => true, 'class' => "text field medium"), 'trim|required|min_length[2]|max_length[80]|xss_clean', 'tOOL TIP', ''),
            array('Value', 'INPUT', array('name' => 'value', 'size' => '20', 'maxlength' => '200', 'class' => "text field medium"), 'trim|required', 'tOOL TIP', ''),
            array('Comment', 'INPUT', array('name' => 'comment', 'size' => '20', 'maxlength' => '15', 'class' => "text field medium"), '', 'tOOL TIP', ''),
        );

        $form['button_cancel'] = array('name' => 'action', 'content' => 'Cancel', 'value' => 'cancel', 'type' => 'button', 'class' => 'ui-state-default float-right ui-corner-all ui-button', 'onclick' => 'return redirect_page(\'NULL\')');
        $form['button_save'] = array('name' => 'action', 'content' => 'Save', 'value' => 'save', 'id' => 'submit', 'type' => 'button', 'class' => 'ui-state-default float-right ui-corner-all ui-button');

        return $form;
    }

    function get_configuration_search_form() {
        $form['forms'] = array("", array('id' => "configuration_search"));
        $form['Search Configuration'] = array(
            array('', 'HIDDEN', 'ajax_search', '1', '', '', ''),
            array('', 'HIDDEN', 'advance_search', '1', '', '', ''),
            array('Name', 'INPUT', array('name' => 'name[name]', '', 'size' => '20', 'maxlength' => '200', 'class' => "text field "), '', 'tOOL TIP', '1', 'name[name-string]', '', '', '', 'search_string_type', ''),
            array('Value', 'INPUT', array('name' => 'value[value]', '', 'size' => '20', 'maxlength' => '200', 'class' => "text field "), '', 'tOOL TIP', '1', 'value[value-string]', '', '', '', 'search_string_type', ''),
            array('Comment', 'INPUT', array('name' => 'comment[comment]', '', 'size' => '20', 'maxlength' => '200', 'class' => "text field "), '', 'tOOL TIP', '1', 'comment[comment-string]', '', '', '', 'search_string_type', ''),
            array('Group', 'group_title', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'group_title', 'group_title', 'system', 'build_dropdown', 'group_by', 'group_title'),
        );
        $form['button_search'] = array('name' => 'action', 'id' => "configuration_search_btn", 'content' => 'Search', 'value' => 'save', 'type' => 'button', 'class' => 'ui-state-default float-right ui-corner-all ui-button');
        $form['button_reset'] = array('name' => 'action', 'id' => "id_reset", 'content' => 'Clear Search Filter', 'value' => 'cancel', 'type' => 'reset', 'class' => 'ui-state-default float-right ui-corner-all ui-button');
        return $form;
    }

    function build_system_list_for_admin() {
        // array(display name, width, db_field_parent_table,feidname, db_field_child_table,function name);
        $grid_field_arr = json_encode(array(array("ID", "80", "id", "", "", ""),
            array("Name", "170", "name", "", "", ""),
            array("Value", "170", "value", "", "", ""),
            array("Content", "300", "comment", "", "", ""),
            array("Group", "100", "group_title", "", "", ""),
            array("Action", "120", "", "", "",
                array("EDIT" => array("url" => "/systems/configuration_edit/", "mode" => "popup"),
            ))
                ));
        return $grid_field_arr;
    }

    function build_grid_buttons() {
        $buttons_json = json_encode(array(
            array("Refresh", "reload", "/accounts/clearsearchfilter/")));
        return $buttons_json;
    }

    function build_template_list_for_admin() {
        // array(display name, width, db_field_parent_table,feidname, db_field_child_table,function name);
        $grid_field_arr = json_encode(array(array("Name", "200", "name", "", "", ""),
            array("Subject", "250", "subject", "", "", ""),
//             array("Body", "925", "template", "", "", ""),
            array("Action", "100", "", "", "",
                array("EDIT" => array("url" => "/systems/template_edit/", "mode" => "single"),
            ))
                ));
        return $grid_field_arr;
    }

}

?>
