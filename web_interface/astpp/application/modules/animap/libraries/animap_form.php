<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Animap_form {
      	function __construct($library_name = '') {
        $this->CI = & get_instance();
    }
   
    function get_animap_form_fields($edit_id) {
    
   // print_r($edit_id); exit;
   $id=$edit_id;
        $form['forms'] = array(base_url() . 'animap/animap_save/', array('id' => 'animap_form', 'method' => 'POST', 'name' => 'animap_form'));
  //$val ='ani_map.number';
  $val=$id > 0 ? 'ani_map.number.'.$id : 'ani_map.number';   
        $form['Caller Id'] = array(
            array('', 'HIDDEN', array('name' => 'id'), '', '', '', ''),
            array('Account', 'accountid', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'first_name,last_name,number', 'accounts', 'build_concat_dropdown', 'where_arr', array("reseller_id" => "0", "type" => "0", "deleted" => "0","status" => "0")),
            array('Caller Id', 'INPUT', array('name' => 'number', 'size' => '20', 'maxlength' => '20', 'class' => "text field medium"), 'trim|required|is_unique['.$val.']|numeric|xss_clean', 'tOOL TIP', 'Please Enter ANI number'),
           
            
        );
        $form['button_cancel'] = array('name' => 'action', 'content' => 'Cancel', 'value' => 'cancel', 'type' => 'button', 'class' => 'btn btn-line-sky margin-x-10', 'onclick' => 'return redirect_page(\'NULL\')');
        $form['button_save'] = array('name' => 'action', 'content' => 'Save', 'value' => 'save', 'id' => 'submit', 'type' => 'button', 'class' => 'btn btn-line-parrot');
        return $form;
    }

    function get_animap_search_form() {
    $logintype = $this->CI->session->userdata('logintype');
     
        $form['forms'] = array("", array('id' => "animap_search"));
        $form['Search'] = array(
             array('Account', 'accountid', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'first_name,last_name,number', 'accounts', 'build_concat_dropdown', 'where_arr', array("reseller_id" => "0","type"=>"0", "deleted" => "0")),
       array('Caller Id', 'INPUT', array('name' => 'number[number]', '', 'size' => '20', 'class' => "text field"), '', 'tOOL TIP', '1', 'number[number-string]', '', '', '', 'search_string_type', ''),
       
	array('', 'HIDDEN', 'ajax_search', '1', '', '', ''),
       
            array('', 'HIDDEN', 'advance_search', '1', '', '', '')
        );
        $form['button_search'] = array('name' => 'action', 'id' => "animap_search_btn", 'content' => 'Search', 'value' => 'save', 'type' => 'button', 'class' => 'btn btn-line-parrot pull-right');
        $form['button_reset'] = array('name' => 'action', 'id' => "id_reset", 'content' => 'Clear', 'value' => 'cancel', 'type' => 'reset', 'class' => 'btn btn-line-sky pull-right margin-x-10');

        return $form;
    }


    function build_animap_list_for_admin() {
        // array(display name, width, db_field_parent_table,feidname, db_field_child_table,function name);
        $grid_field_arr = json_encode(array(array("<input type='checkbox' name='chkAll' class='ace checkall'/><label class='lbl'></label>", "30", "", "", "", ""),
	   array("Account", "420", "accountid", "first_name,last_name,number", "accounts", "get_field_name_coma_new"),
            array("Caller Id", "430", "number", "", "", ""),
            //array("Context", "300", "context", "", "", ""),
            array("Action", "370", "", "", "", array("EDIT" => array("url" => "animap/animap_edit/", "mode" => "popup", 'popup'),
                    "DELETE" => array("url" => "/animap/animap_delete/", "mode" => "single")))
                ));
        return $grid_field_arr;
    }

    function build_grid_buttons() {
        $buttons_json = json_encode(array(array("Create" , "btn btn-line-warning btn" , "fa fa-plus-circle fa-lg", "button_action", "/animap/animap_add/", "popup"),
            array("Delete", "btn btn-line-danger","fa fa-times-circle fa-lg",  "button_action", "/animap/animap_delete_multiple/")
            ));
        return $buttons_json;
    }
   
   

}

?>
