<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class pricing_form {
    function __construct($library_name = '') {
        $this->CI = & get_instance();
    }
    function get_pricing_form_fields() {
	$form['forms'] = array(base_url() . 'pricing/price_save/', array('id' => 'pricing_form', 'method' => 'POST', 'name' => 'pricing_form'));
        if ($this->CI->session->userdata('logintype') == 1 || $this->CI->session->userdata('logintype') == 5) {
	    $form['Rate Group Information'] = array(
		array('', 'HIDDEN', array('name' => 'id'), '', '', '', ''),
		array('', 'HIDDEN', array('name' => 'status', 'value' => '1'), '', '', ''),
		array('Name', 'INPUT', array('name' => 'name', 'size' => '20', 'maxlength' => '30', 'class' => "text field medium"), 'trim|required|min_length[2]|max_length[25]|xss_clean', 'tOOL TIP', 'Please Enter account number'),
		array('Default Increment', 'INPUT', array('name' => 'inc', 'size' => '20', 'maxlength' => '4', 'class' => "text field medium"), 'trim|required|xss_clean', 'tOOL TIP', 'Please Enter account number'),
		array('Markup(%)', 'INPUT', array('name' => 'markup', 'value' => "0" ,  'size' => '20', 'maxlength' => '3', 'class' => "text field medium"), 'trim|required|xss_clean', 'tOOL TIP', 'Please Enter account number'),
		array('Status', 'status', 'SELECT', '', '', 'tOOL TIP', 'Please Select Status', '', '', '', 'set_status'),

	    );
	}
	else{
	  $form['Rate Group Information'] = array(
		array('', 'HIDDEN', array('name' => 'id'), '', '', '', ''),
		array('', 'HIDDEN', array('name' => 'status', 'value' => '1'), '', '', ''),
		array('Name', 'INPUT', array('name' => 'name', 'size' => '20', 'maxlength' => '30', 'class' => "text field medium"), 'trim|required|min_length[2]|max_length[25]|xss_clean', 'tOOL TIP', 'Please Enter account number'),
		array('Default Increment', 'INPUT', array('name' => 'inc', 'size' => '20', 'maxlength' => '4', 'class' => "text field medium"), 'trim|required|xss_clean', 'tOOL TIP', 'Please Enter account number'),
		array('Markup(%)', 'INPUT', array('name' => 'markup', 'value' => "0" , 'size' => '20', 'maxlength' => '3', 'class' => "text field medium"), 'trim|required|xss_clean', 'tOOL TIP', 'Please Enter account number'),
		array('Trunks','trunk_id', 'SELECT', '','', 'tOOL TIP', 'Please Select Trunks', 'id', 'name', 'trunks', 'build_dropdown', 'where_arr', array("status <" => "2"), 'multi'),
		array('Status', 'status', 'SELECT', '', '', 'tOOL TIP', 'Please Select Status', '', '', '', 'set_status'),
		  );
	}
        

        $form['button_cancel'] = array('name' => 'action', 'content' => 'Cancel', 'value' => 'cancel', 'type' => 'button', 'class' => 'btn btn-line-sky margin-x-10', 'onclick' => 'return redirect_page(\'NULL\')');
        $form['button_save'] = array('name' => 'action', 'content' => 'Save', 'value' => 'save', 'id' => 'submit', 'type' => 'button', 'class' => 'btn btn-line-parrot');

        return $form;
    }

    function get_pricing_search_form() {
        $form['forms'] = array("", array('id' => "price_search"));
        $form['Search'] = array(
            array('Name', 'INPUT', array('name' => 'name[name]', '', 'size' => '20', 'class' => "text field"), '', 'tOOL TIP', '1', 'name[name-string]', '', '', '', 'search_string_type', ''),
            array('Default Increment ', 'INPUT', array('name' => 'inc[inc]', '', 'size' => '20', 'class' => "text field"), '', 'tOOL TIP', '1', 'inc[inc-string]', '', '', '', 'search_string_type', ''),
	array('Status', 'status', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', '', '', '', 'set_search_status', '', ''),
            array('', 'HIDDEN', 'ajax_search', '1', '', '', ''),
            array('', 'HIDDEN', 'advance_search', '1', '', '', ''),

        );
        $form['button_search'] = array('name' => 'action', 'id' => "price_search_btn", 'content' => 'Search', 'value' => 'save', 'type' => 'button', 'class' => "btn btn-line-parrot pull-right");
        $form['button_reset'] = array('name' => 'action', 'id' => "id_reset", 'content' => 'Clear', 'value' => 'cancel', 'type' => 'reset', 'class' => "btn btn-line-sky pull-right margin-x-10");

        return $form;
    }

    function build_pricing_list_for_admin() {
        // array(display name, width, db_field_parent_table,feidname, db_field_child_table,function name);
        $grid_field_arr = json_encode(array(array("<input type='checkbox' name='chkAll' class='ace checkall'/><label class='lbl'></label>", "30", "", "", "", ""),
            array("Name", "240", "name", "", "", ""),
            array("Default Increment", "220", "inc", "", "", ""),
            array("Markup(%)", "260", "markup", "", "", ""),
  	    array("Rate Count", "170", "id", "pricelist_id", "routes", "get_field_count"),
            array("Status", "160", "status", "status", "status", "get_status"),
            array("Action", "170", "", "", "", array("EDIT" => array("url" => "/pricing/price_edit/", "mode" => "popup"),

                    "DELETE" => array("url" => "/pricing/price_delete/", "mode" => "single")))
                ));
        return $grid_field_arr;
    }

    function build_grid_buttons() {
        $buttons_json = json_encode(array(array("Create", "btn btn-line-warning btn" ,"fa fa-plus-circle fa-lg", "button_action", "/pricing/price_add/", "popup"),
            array("Delete", "btn btn-line-danger","fa fa-times-circle fa-lg", "button_action", "/pricing/price_delete_multiple/")
            ));
        return $buttons_json;
    }

}

?>
