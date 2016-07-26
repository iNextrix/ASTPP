<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Ipmap_form {
      	function __construct($library_name = '') {
        $this->CI = & get_instance();
    }
   
    function get_ipmap_form_fields() {
        $form['forms'] = array(base_url() . 'ipmap/ipmap_save/', array('id' => 'ipmap_form', 'method' => 'POST', 'name' => 'ipmap_form'));
        $form['IP map'] = array(
            array('', 'HIDDEN', array('name' => 'id'), '', '', '', ''),
            array('Account', 'accountid', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'first_name,last_name,number', 'accounts', 'build_concat_dropdown', 'where_arr', array("reseller_id" => "0", "type" => "0", "deleted" => "0","status" => "0")),
            array('Name', 'INPUT', array('name' => 'name', 'size' => '20', 'maxlength' => '20', 'class' => "text field medium"), 'trim|required|min_length[2]|max_length[20]|xss_clean', 'tOOL TIP', 'Please Enter account number'),
            array('IP', 'INPUT', array('name' => 'ip', 'size' => '20', 'class' => "text field medium"), 'trim|required', 'tOOL TIP', 'Please Enter proper ip'),
            array('Prefix', 'INPUT', array('name' => 'prefix', 'size' => '20', 'maxlength' => '15', 'class' => "text field medium"), 'trim|max_length[15]|numeric|xss_clean', 'tOOL TIP', 'Please Enter prefix number'),
            
        );
        $form['button_cancel'] = array('name' => 'action', 'content' => 'Cancel', 'value' => 'cancel', 'type' => 'button', 'class' => 'btn btn-line-sky margin-x-10', 'onclick' => 'return redirect_page(\'NULL\')');
        $form['button_save'] = array('name' => 'action', 'content' => 'Save', 'value' => 'save', 'id' => 'submit', 'type' => 'button', 'class' => 'btn btn-line-parrot');
        return $form;
    }

    function get_ipmap_search_form() {
    $logintype = $this->CI->session->userdata('logintype');
     
        $form['forms'] = array("", array('id' => "ipmap_search"));
        $form['Search'] = array(
             array('Account', 'accountid', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'first_name,last_name,number', 'accounts', 'build_concat_dropdown', 'where_arr', array("reseller_id" => "0","type"=>"0", "deleted" => "0")),
             array('Name', 'INPUT', array('name' => 'name[name]', '', 'size' => '20', 'class' => "text field"), '', 'tOOL TIP', '1', 'name[name-string]', '', '', '', 'search_string_type', ''),
             array('IP', 'INPUT', array('name' => 'ip[ip]', '', 'size' => '20', 'class' => "text field"), '', 'tOOL TIP', '1', 'ip[ip-string]', '', '', '', 'search_string_type', ''),    
             array('Prefix', 'INPUT', array('name' => 'prefix[prefix]', '', 'size' => '20', 'class' => "text field"), '', 'tOOL TIP', '1', 'prefix[prefix-string]', '', '', '', 'search_string_type', ''),
	     array('', 'HIDDEN', 'ajax_search', '1', '', '', ''),
             array('', 'HIDDEN', 'advance_search', '1', '', '', '')
        );
        $form['button_search'] = array('name' => 'action', 'id' => "ipmap_search_btn", 'content' => 'Search', 'value' => 'save', 'type' => 'button', 'class' => 'btn btn-line-parrot pull-right');
       $form['button_reset'] = array('name' => 'action', 'id' => "id_reset", 'content' => 'Clear', 'value' => 'cancel', 'type' => 'reset', 'class' => "btn btn-line-sky pull-right margin-x-10");

        return $form;
    }

    function build_ipmap_list_for_admin() {
            $grid_field_arr = json_encode(array(array("<input type='checkbox' name='chkAll' class='ace checkall'/><label class='lbl'></label>", "30", "", "", "", ""),
	   array("Account", "270", "accountid", "first_name,last_name,number", "accounts", "get_field_name_coma_new"),
           array("Name", "270", "name", "", "", ""),
           array("IP", "240", "ip", "", "", ""),
           array("Prefix", "240", "prefix", "", "", ""),
        //   array("Context", "180", "context", "", "", ""),
           array("Action", "205", "", "", "", array("EDIT" => array("url" => "ipmap/ipmap_edit/", "mode" => "popup", 'popup'),
                    "DELETE" => array("url" => "ipmap/ipmap_delete/", "mode" => "single")))
                ));
        return $grid_field_arr;
    }

    function build_grid_buttons() {
        $buttons_json = json_encode(array(array("Create" , "btn btn-line-warning btn" , "fa fa-plus-circle fa-lg", "button_action", "/ipmap/ipmap_add/", "popup"),
            array("Delete", "btn btn-line-danger","fa fa-times-circle fa-lg",  "button_action", "/ipmap/ipmap_delete_multiple/")
            ));
        return $buttons_json;
    }
   
   

}

?>
