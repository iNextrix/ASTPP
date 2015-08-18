<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Package_form {

    function get_package_form_fields() {
        $form['forms'] = array(base_url() . 'package/package_save/',array('id'=>'packeage_form','method'=>'POST','name'=>'packeage_form'));
        $form['Package Information'] = array(
            array('', 'HIDDEN', array('name' => 'id'), '', '', '', ''),
            array('', 'HIDDEN', array('name' => 'status', 'value' => '1'), '', '', ''),
            array('Package name', 'INPUT', array('name' => 'package_name', 'size' => '20', 'maxlength' => '20', 'class' => "text field medium"), 'trim|required|min_length[2]|max_length[45]|xss_clean', 'tOOL TIP', ''),
            array('Rate Group', 'pricelist_id', 'SELECT', '', 'dropdown', 'tOOL TIP', 'Please Enter account number', 'id', 'name', 'pricelists', 'build_dropdown', 'where_arr', array("status" => "0","reseller_id" => "0")),
            array('Included Seconds', 'INPUT', array('name' => 'includedseconds', 'size' => '20', 'maxlength' => '11', 'class' => "text field medium"), 'trim|is_numeric|required|xss_clean', 'tOOL TIP', ''),
	            array('Status', 'status', 'SELECT', '', '', 'tOOL TIP', 'Please Select Status', '', '', '', 'set_status')
        );
        $form['button_cancel'] = array('name' => 'action', 'content' => 'Cancel', 'value' => 'cancel', 'type' => 'button', 'class' => 'btn btn-line-sky margin-x-10', 'onclick' => 'return redirect_page(\'/package/package_list/\')');
        $form['button_save'] = array('name' => 'action', 'content' => 'Save', 'value' => 'save', 'type' => 'submit', 'class' => 'btn btn-line-parrot');

        return $form;
    }

    function get_package_search_form() {
        $form['forms'] = array("", array('id' => "package_search"));
        $form['Search'] = array(
            array('Package name', 'INPUT', array('name' => 'package_name[package_name]', '', 'size' => '20', 'class' => "text field"), '', 'tOOL TIP', '1', 'package_name[package_name-string]', '', '', '', 'search_string_type', ''),
            array('Rate Group', 'pricelist_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'name', 'pricelists', 'build_dropdown', 'where_arr', array("status" => "0","reseller_id" => "0")),
            array('Included Seconds', 'INPUT', array('name' => 'includedseconds[includedseconds]', 'value' => '', 'size' => '20', 'class' => "text field"), '', 'Tool tips info', '1', 'includedseconds[includedseconds-integer]', '', '', '', 'search_int_type', ''),
		array('Status', 'status', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', '', '', '', 'set_search_status', '', ''),
            
            array('', 'HIDDEN', 'ajax_search', '1', '', '', ''),
            array('', 'HIDDEN', 'advance_search', '1', '', '', ''),

        );
        $form['button_search'] = array('name' => 'action', 'id' => "package_search_btn", 'content' => 'Search', 'value' => 'save', 'type' => 'button', 'class' => 'btn btn-line-parrot pull-right');
        $form['button_reset'] = array('name' => 'action', 'id' => "id_reset", 'content' => 'Clear', 'value' => 'cancel', 'type' => 'reset', 'class' => 'btn btn-line-sky pull-right  margin-x-10');

        return $form;
    }

    function build_package_list_for_admin() {
        // array(display name, width, db_field_parent_table,feidname, db_field_child_table,function name);
        $grid_field_arr = json_encode(array(array("<input type='checkbox' name='chkAll' class='ace checkall'/><label class='lbl'></label>", "30", "", "", "", ""),
            array("Name", "310", "package_name", "", "", ""),
            array("Rate Group", "250", "pricelist_id", "name", "pricelists", "get_field_name"),
            array("Included Seconds", "260", "includedseconds", "", "", ""),
            array("Status", "160", "status", "status", "status", "get_status"),
            array("Action", "245", "", "", "", array("EDIT" => array("url" => "/package/package_edit/", "mode" => "single"),
                    "DELETE" => array("url" => "/package/package_delete/", "mode" => "single")))
                ));
        return $grid_field_arr;
    }

    function build_grid_buttons() {
        $buttons_json = json_encode(array(array("Create","btn btn-line-warning btn","fa fa-plus-circle fa-lg", "button_action", "/package/package_add/"),
            array("Delete", "btn btn-line-danger", "fa fa-times-circle fa-lg", "button_action", "/package/package_delete_multiple/")
            ));
        return $buttons_json;
    }



    function build_package_counter_list_for_admin() {
     // array(display name, width, db_field_parent_table,feidname, db_field_child_table,function name);
        $grid_field_arr = json_encode(array(array("Package Name", "450", "package_id", "package_name", "packages", "get_field_name"),
            array("Account", "400", "accountid", "first_name,last_name,number", "accounts", "build_concat_string"),
            array("Used Seconds", "400", "seconds", "", "", ""),
                ));
        return $grid_field_arr;
    }
    function build_pattern_list_for_customer($packageid) {
        $grid_field_arr = json_encode(array(array("Patterns", "420", "patterns", "patterns", "", "get_only_numeric_val"),
		array("Destination", "570", "destination", "", "", ""),
            array("Action", "250", "", "", "", array("DELETE" => array("url" => "/package/customer_delete_package_pattern/$packageid/", "mode" => "single")))
                ));
        return $grid_field_arr;
    }
//    function build_pattern_list_for_customer($packageid) {
  //      $grid_field_arr = json_encode(array(array("Patterns", "100", "patterns", "patterns", "", "get_only_numeric_val"),
	//	array("Destination", "150", "destination", "", "", ""),
          //  array("Action", "30", "", "", "", array("DELETE" => array("url" => "/package/customer_delete_package_pattern/$packageid/", "mode" => "single")))
            //    ));
//        return $grid_field_arr;
  //  }

    function set_pattern_grid_buttons($packageid) {
        $buttons_json = json_encode(array(array("Create","btn btn-line-warning btn","fa fa-plus-circle fa-lg", "button_action", "/package/customer_add_patterns/$packageid", "popup")));
        return $buttons_json;
    }



function build_package_counter_report() {
        $buttons_json = json_encode(array(
            array("Export","btn btn-xing" ," fa fa-download fa-lg", "button_action", "/package/package_counter_report/", 'single')
            
            ));
        return $buttons_json;
    }



}

?>
