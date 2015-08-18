<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class System_form {

    function get_template_form_fields() {

        $form['forms'] = array(base_url() . 'systems/template_save/', array("template_form", "name" => "template_form"));
        $form['Email Template'] = array(
            array('', 'HIDDEN', array('name' => 'id'), '', '', '', ''),
            array(' Name', 'INPUT', array('name' => 'name', 'size' => '20', 'maxlength' => '45', 'readonly' => true, 'class' => "text field medium"), 'trim|required|min_length[2]|max_length[80]|xss_clean', 'tOOL TIP', ''),
            array('Subject', 'INPUT', array('name' => 'subject', 'size' => '20', 'maxlength' => '10000', 'class' => "text field medium"), 'trim|required', 'tOOL TIP', ''),
            array('Body', 'TEXTAREA', array('name' => 'template', 'id' => 'template', 'size' => '20', 'maxlength' => '1000', 'class' => "textarea medium"), 'trim|required', 'tOOL TIP', ''),
        );
        $form['button_cancel'] = array('name' => 'action', 'content' => 'Cancel', 'value' => 'cancel', 'type' => 'button', 'class' => 'btn btn-line-sky margin-x-10', 'onclick' => 'return redirect_page(\'/systems/template/\')');
        $form['button_save'] = array('name' => 'action', 'content' => 'Save', 'value' => 'save', 'type' => 'submit', 'class' => 'btn btn-line-parrot');

        return $form;
    }

    function get_template_search_form() {
        $form['forms'] = array("", array('id' => "template_search"));
        $form['Search'] = array(
            
            array(' Name', 'INPUT', array('name' => 'name[name]', '', 'size' => '20', 'class' => "text field "), '', 'tOOL TIP', '1', 'name[name-string]', '', '', '', 'search_string_type', ''),
            array('Subject', 'INPUT', array('name' => 'subject[subject]', '', 'size' => '20', 'class' => "text field "), '', 'tOOL TIP', '1', 'subject[subject-string]', '', '', '', 'search_string_type', ''),array('', 'HIDDEN', 'ajax_search', '1', '', '', ''),
            array('', 'HIDDEN', 'advance_search', '1', '', '', '')
//             array('Body', 'INPUT', array('name' => 'template[template]', '', 'size' => '20', 'class' => "text field "), '', 'tOOL TIP', '1', 'template[template-string]', '', '', '', 'search_string_type', ''),
        );
        $form['button_search'] = array('name' => 'action', 'id' => "template_search_btn", 'content' => 'Search', 'value' => 'save', 'type' => 'button', 'class' => 'btn btn-line-parrot pull-right');
        $form['button_reset'] = array('name' => 'action', 'id' => "id_reset", 'content' => 'Clear', 'value' => 'cancel', 'type' => 'reset', 'class' => 'btn btn-line-sky pull-right margin-x-10');
        return $form;
    }

    function get_configuration_form_fields() {

        $form['forms'] = array(base_url() . 'systems/configuration_save/', array("id" => "config_form", "name" => "config_form"));
        $form['Edit Settings '] = array(
            array('', 'HIDDEN', array('name' => 'id'), '', '', '', ''),
            array('Name', 'INPUT', array('name' => 'name', 'size' => '20', 'maxlength' => '15', 'readonly' => true, 'class' => "text field medium"), 'trim|required|min_length[2]|max_length[80]|xss_clean', 'tOOL TIP', ''),
            array('Value', 'INPUT', array('name' => 'value', 'size' => '20', 'maxlength' => '200', 'class' => "text field medium"), 'trim|required', 'tOOL TIP', ''),
            array('Comment', 'INPUT', array('name' => 'comment', 'size' => '20', 'maxlength' => '15', 'class' => "text field medium"), '', 'tOOL TIP', ''),
        );

        $form['button_cancel'] = array('name' => 'action', 'content' => 'Cancel', 'value' => 'cancel', 'type' => 'button', 'class' => 'btn btn-line-sky margin-x-10', 'onclick' => 'return redirect_page(\'NULL\')');
        $form['button_save'] = array('name' => 'action', 'content' => 'Save', 'value' => 'save', 'id' => 'submit', 'type' => 'button', 'class' => 'btn btn-line-parrot');

        return $form;
    }

    function get_configuration_search_form() {
        $form['forms'] = array("", array('id' => "configuration_search"));
        $form['Search'] = array(
           
            array('Name', 'INPUT', array('name' => 'name[name]', '', 'size' => '20', 'class' => "text field "), '', 'tOOL TIP', '1', 'name[name-string]', '', '', '', 'search_string_type', ''),
            array('Value', 'INPUT', array('name' => 'value[value]', '', 'size' => '20', 'class' => "text field "), '', 'tOOL TIP', '1', 'value[value-string]', '', '', '', 'search_string_type', ''),
            array('Description', 'INPUT', array('name' => 'comment[comment]', '', 'size' => '20', 'class' => "text field "), '', 'tOOL TIP', '1', 'comment[comment-string]', '', '', '', 'search_string_type', ''),
            array('Group', 'group_title', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'group_title', 'group_title', 'system', 'build_dropdown','where_arr',"group_title NOT IN ('asterisk','osc','freepbx')", 'group_by', 'group_title'),
 array('', 'HIDDEN', 'ajax_search', '1', '', '', ''),
            array('', 'HIDDEN', 'advance_search', '1', '', '', '')
        );
        $form['button_search'] = array('name' => 'action', 'id' => "configuration_search_btn", 'content' => 'Search', 'value' => 'save', 'type' => 'button', 'class' => 'btn btn-line-parrot pull-right');
        $form['button_reset'] = array('name' => 'action', 'id' => "id_reset", 'content' => 'Clear', 'value' => 'cancel', 'type' => 'reset', 'class' => 'btn btn-line-sky pull-right margin-x-10');
        return $form;
    }

    function build_system_list_for_admin() {
        // array(display name, width, db_field_parent_table,feidname, db_field_child_table,function name);
        $grid_field_arr = json_encode(array(
	    //array("ID", "80", "id", "", "", ""),
            array("Name", "190", "name", "", "", ""),
            array("Value", "190", "value", "", "", ""),
            array("Description", "320", "comment", "", "", ""),
            array("Group", "120", "group_title", "", "", ""),
            array("Action", "442", "", "", "",
                array("EDIT" => array("url" => "/systems/configuration_edit/", "mode" => "popup"),
            ))
                ));
        return $grid_field_arr;
    }

    function build_grid_buttons() {
        $buttons_json = json_encode(array(
            ));
        return $buttons_json;
    }

    function build_template_list_for_admin() {
        // array(display name, width, db_field_parent_table,feidname, db_field_child_table,function name);
        $grid_field_arr = json_encode(array(array("Name", "425", "name", "", "", ""),
            array("Subject", "650", "subject", "", "", ""),
//             array("Body", "925", "template", "", "", ""),
            array("Action", "180", "", "", "",
                array("EDIT" => array("url" => "/systems/template_edit/", "mode" => "single"),
            ))
                ));
        return $grid_field_arr;
    }

    function build_country_list_for_admin() {
      // array(display name, width, db_field_parent_table,feidname, db_field_child_table,function name);
        
            $action = 'systems/country_list_edit/';
            $action_remove = 'systems/country_remove/';
            $mode="popup";
        
        $grid_field_arr = json_encode(array(
	    array("<input type='checkbox' name='chkAll' class='ace checkall'/><label class='lbl'></label>", "50", "", "", "", ""),
            array("Name", "705", "country", "", "", ""),
               array("Action", "500", "", "", "", array("EDIT" => array("url" => "$action", "mode" => "$mode"),
                    "DELETE" => array("url" => "$action_remove", "mode" => "single")
                ))
                ));
        return $grid_field_arr;
     }
    
     function build_admin_grid_buttons() {
        $buttons_json = json_encode(array(array("Create","btn btn-line-warning btn","fa fa-plus-circle fa-lg", "button_action", "/systems/country_add/", "popup"),
            array("Delete",  "btn btn-line-danger","fa fa-times-circle fa-lg", "button_action", "/systems/country_delete_multiple"),
            //array("Export","btn btn-xing" ,"fa fa-file-excel-o fa-lg", "button_action", "/systems/country_export_xls/", 'single')
           ));
        return $buttons_json;
    }

     function get_search_country_form() {

        $form['forms'] = array("", array('id' => "country_search"));
        $form['Search'] = array(
            array('Name', 'id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'country', 'countrycode', 'build_dropdown', '', ''),
            array('', 'HIDDEN', 'ajax_search', '1', '', '', ''),
            array('', 'HIDDEN', 'advance_search', '1', '', '', '')
            
        );

        $form['button_search'] = array('name' => 'action', 'id' => "country_search_btn", 'content' => 'Search', 'value' => 'save', 'type' => 'button', 'class' => 'btn btn-line-parrot pull-right');
        $form['button_reset'] = array('name' => 'action', 'id' => "id_reset", 'content' => 'Clear', 'value' => 'cancel', 'type' => 'reset', 'class' => 'btn btn-line-sky pull-right margin-x-10');

        return $form;
    }

     function get_country_form_fields() {
     
        
        $form['forms'] = array(base_url() . '/systems/country_save/', array('id' => 'system_form', 'method' => 'POST', 'name' => 'system_form'));
        $form['Country List'] = array(
            array('', 'HIDDEN', array('name' => 'id'), '', '', '', ''),
            array('Name', 'INPUT', array('name' => 'country', 'size' => '20', 'maxlength' => '150', 'class' => "text field medium"), 'trim|required|char|min_length[2]|max_length[20]|xss_clean', 'tOOL TIP', 'Please Enter country'),

        );
        $form['button_save'] = array('name' => 'action', 'content' => 'Save', 'value' => 'save', 'id' => 'submit', 'type' => 'button', 'class' => 'btn btn-line-parrot');
        $form['button_cancel'] = array('name' => 'action', 'content' => 'Cancel', 'value' => 'cancel', 'type' => 'button', 'class' => 'btn btn-line-sky margin-x-10', 'onclick' => 'return redirect_page(\'NULL\')');
        return $form;
    }

      function build_currency_list_for_admin() {
      // array(display name, width, db_field_parent_table,feidname, db_field_child_table,function name);
        
            $action = 'systems/currency_list_edit/';
            $action_remove = 'systems/currency_remove/';
            $mode="popup";
        
        $grid_field_arr = json_encode(array(
	    array("<input type='checkbox' name='chkAll' class='ace checkall'/><label class='lbl'></label>", "70", "", "", "", ""),
            array("Name", "320", "currencyname", "", "", ""),
	    array("Code", "270", "currency", "", "", ""),
	    array(" Rate", "330", "currencyrate", "", "", ""),
               array("Action", "265", "", "", "", array("EDIT" => array("url" => "$action", "mode" => "$mode"),
                    "DELETE" => array("url" => "$action_remove", "mode" => "single")
                ))
                ));
        return $grid_field_arr;
     }

    function get_search_currency_form() {

        $form['forms'] = array("", array('id' => "currency_search"));
        $form['Search'] = array(
            
            array('Name', 'INPUT', array('name' => 'currencyname[currencyname]', '', 'size' => '20', 'class' => "text field "), '', 'tOOL TIP', '1', 'currencyname[currencyname-string]', '', '', '', 'search_int_type', ''),
            array('Code', 'INPUT', array('name' => 'currency[currency]', '', 'size' => '20', 'class' => "text field "), '', 'tOOL TIP', '1', 'currency[currency-string]', '', '', '', 'search_string_type', ''),
            array('Rate', 'INPUT', array('name' => 'currencyrate[currencyrate]', '', 'size' => '20', 'class' => "text field "), '', 'tOOL TIP', '1', 'currencyrate[currencyrate-integer]', '', '', '', 'search_int_type', ''), 
            array('', 'HIDDEN', 'ajax_search', '1', '', '', ''),
            array('', 'HIDDEN', 'advance_search', '1', '', '', ''));

        $form['button_search'] = array('name' => 'action', 'id' => "currency_search_btn", 'content' => 'Search', 'value' => 'save', 'type' => 'button', 'class' => 'btn btn-line-parrot pull-right');
        $form['button_reset'] = array('name' => 'action', 'id' => "id_reset", 'content' => 'Clear', 'value' => 'cancel', 'type' => 'reset', 'class' => 'btn btn-line-sky pull-right margin-x-10');

        return $form;
    }

    function get_currency_form_fields() {
     
        
        $form['forms'] = array(base_url() . '/systems/currency_save/', array('id' => 'system_form', 'method' => 'POST', 'name' => 'system_form'));
        $form['Currency List'] = array(
            array('', 'HIDDEN', array('name' => 'id'), '', '', '', ''),
            array('Name', 'INPUT', array('name' => 'currencyname', 'size' => '20', 'maxlength' => '40', 'class' => "text field medium"), 'trim|required|char|min_length[2]|max_length[20]|xss_clean', 'tOOL TIP', 'Please Enter country'),
	    array('Code', 'INPUT', array('name' => 'currency', 'size' => '20', 'maxlength' => '3', 'class' => "text field medium"), 'trim|required|char|min_length[3]|max_length[3]|xss_clean', 'tOOL TIP', 'Please Enter country'),
            array('Rate', 'INPUT', array('name' => 'currencyrate', 'size' => '20', 'maxlength' => '20', 'class' => "text field medium"), 'trim|required|min_length[1]|max_length[20]|xss_clean', 'tOOL TIP', 'Please Enter country'),

        );
        $form['button_save'] = array('name' => 'action', 'content' => 'Save', 'value' => 'save', 'id' => 'submit', 'type' => 'button', 'class' => 'btn btn-line-parrot');
        $form['button_cancel'] = array('name' => 'action', 'content' => 'Cancel', 'value' => 'cancel', 'type' => 'button', 'class' => 'btn btn-line-sky margin-x-10', 'onclick' => 'return redirect_page(\'NULL\')');
        return $form;
    }

     function build_admin_currency_grid_buttons() {
        $buttons_json = json_encode(array(array("Create","btn btn-line-warning btn","fa fa-plus-circle fa-lg", "button_action", "/systems/currency_add/", "popup"),
            array("Update Currencies","btn btn-line-blue" ,"fa fa-upload fa-lg", "button_action", "/currencyupdate/update_currency/", 'single'),
            array("Delete","btn btn-line-danger","fa fa-times-circle fa-lg", "button_action", "/systems/currency_delete_multiple"),
            //array("Export","btn btn-xing" ,"fa fa-file-excel-o fa-lg", "button_action", "/systems/currency_export_xls/", 'single'),
           ));
        return $buttons_json;
    }
 
  
    function get_backup_database_form_fields() {
	$tmp_value="/tmp/db_astpp-".date("YmdHi").".sql.gz";
	$form['forms'] = array(base_url() . 'systems/database_backup_save/', array("backup_form", "name" => "backup_form"));
        $form['Backup Database'] = array(
            array('Name', 'INPUT', array('name' => 'backup_name', 'size' => '20', 'maxlength' => '100','class' => "text field medium"), '', 'tOOL TIP', ''),
            array('Path', 'INPUT', array('name' => 'path', 'size' => '20', 'maxlength' => '100', 'value'=>$tmp_value,'class' => "text field medium"), 'trim|required', 'tOOL TIP', ''),
        );
        $form['button_cancel'] = array('name' => 'action', 'content' => 'Cancel', 'value' => 'cancel', 'type' => 'button', 'class' => 'btn btn-line-sky  margin-x-10', 'onclick' => 'return redirect_page(\'NULL\')');
        $form['button_save'] = array('name' => 'action', 'content' => 'Save', 'value' => 'save', 'type' => 'submit', 'class' => 'btn btn-line-parrot');

        return $form;
    }
  function build_backupdastabase_list() {
        $grid_field_arr = json_encode(array(
		//array("ID", "80", "id", "", "", ""),
	 array("<input type='checkbox' name='chkAll' class='ace checkall'/><label class='lbl'></label>", "50", "", "", "", ""),
            array("Date", "260", "date", "date", "date", "convert_GMT_to"),
	    array("Name", "280", "backup_name", "", "", ""),
            array("File Name", "480", "path", "", "", ""),
            array("Action", "185", "", "", "",
                array("EDIT_RESTORE" => array("url" => "/systems/database_restore_one/", "mode" => ""),
                "DOWNLOAD_DATABASE" => array("url" => "/systems/database_download/", "mode" => ""),
                "Delete" => array("url" => "/systems/database_delete/", "mode" => ""),
            ))));
        return $grid_field_arr;
    }
    function build_backupdastabase_buttons() {
        $buttons_json = json_encode(array(array("Create","btn btn-line-warning btn","fa fa-plus-circle fa-lg", "button_action", "/systems/database_backup/", "popup"),
		  array("import","btn btn-line-blue" ,"fa fa-upload fa-lg", "button_action", "/systems/database_import/", "popup"),	
		array("Delete","btn btn-line-danger","fa fa-times-circle fa-lg", "button_action", "/systems/database_backup_delete_multiple")
						));
	      return $buttons_json;
    }
}

?>
