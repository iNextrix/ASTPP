<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Charges_form {
      	function __construct($library_name = '') {
        $this->CI = & get_instance();
    }
   
    function get_charegs_form_fields() {
        $form['forms'] = array(base_url() . 'charges/periodiccharges_save/', array('id' => 'chrges_form', 'method' => 'POST', 'name' => 'chrges_form'));
        $form['Subscription Information'] = array(
            array('', 'HIDDEN', array('name' => 'id'), '', '', '', ''),
            array('Name', 'INPUT', array('name' => 'description', 'size' => '20', 'maxlength' => '80', 'class' => "text field medium"), 'trim|required|min_length[2]|max_length[25]|xss_clean', 'tOOL TIP', 'Please Enter account number'),
            array('Rate Group', 'pricelist_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'name', 'pricelists', 'build_dropdown', 'where_arr', array("reseller_id" => "0", "status <>" => "2")),
            array('Amount', 'INPUT', array('name' => 'charge', 'size' => '20', 'maxlength' => '10', 'class' => "text field medium"), 'trim|numeric|xss_clean', 'tOOL TIP', 'Please Enter account number'),
	    array('Prorate', 'pro_rate', 'SELECT', '', '', 'tOOL TIP', 'Please Select Pro rate', '', '', '', 'set_prorate'),      
	    array('Bill cycle', 'sweep_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'sweep', 'sweeplist', 'build_dropdown', '', ''),
		array('Status', 'status', 'SELECT', '', '', 'tOOL TIP', 'Please Select Status', '', '', '', 'set_status'),
        );
        $form['button_cancel'] = array('name' => 'action', 'content' => 'Cancel', 'value' => 'cancel', 'type' => 'button', 'class' => 'btn btn-line-sky margin-x-10', 'onclick' => 'return redirect_page(\'NULL\')');
        $form['button_save'] = array('name' => 'action', 'content' => 'Save', 'value' => 'save', 'id' => 'submit', 'type' => 'button', 'class' => 'btn btn-line-parrot');
        return $form;
    }

    function get_charges_search_form() {
    $logintype = $this->CI->session->userdata('logintype');
        if ($logintype == 1 || $logintype == 5) {
            $account_data = $this->CI->session->userdata("accountinfo");
            $loginid = $account_data['id'];

        }else{
            $loginid = "0";
        }
        $form['forms'] = array("", array('id' => "charges_search"));
        $form['Search'] = array(
            array('Name', 'INPUT', array('name' => 'description[description]', '', 'size' => '20', 'class' => "text field"), '', 'tOOL TIP', '1', 'description[description-string]', '', '', '', 'search_string_type', ''),
            array('Rate Group', 'pricelist_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'name', 'pricelists', 'build_dropdown', 'where_arr', array('status'=>0,'reseller_id'=>$loginid)),
           array('Amount', 'INPUT', array('name' => 'charge[charge]', 'value' => '', 'size' => '20', 'class' => "text field"), '', 'Tool tips info', '1', 'charge[charge-integer]', '', '', '', 'search_int_type', ''),array('Bill Cycle', 'sweep_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'sweep', 'sweeplist', 'build_dropdown', '', ''), 
	 array('Status', 'status', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', '', '', '', 'set_search_status'),
	array('', 'HIDDEN', 'ajax_search', '1', '', '', ''),
       
            array('', 'HIDDEN', 'advance_search', '1', '', '', '')
        );
        $form['button_search'] = array('name' => 'action', 'id' => "charges_search_btn", 'content' => 'Search', 'value' => 'save', 'type' => 'button', 'class' => 'btn btn-line-parrot pull-right');
        $form['button_reset'] = array('name' => 'action', 'id' => "id_reset", 'content' => 'Clear', 'value' => 'cancel', 'type' => 'reset', 'class' => 'btn btn-line-sky pull-right margin-x-10');

        return $form;
    }

    function build_charge_list_for_admin() {
        // array(display name, width, db_field_parent_table,feidname, db_field_child_table,function name);
        $grid_field_arr = json_encode(array(array("<input type='checkbox' name='chkAll' class='ace checkall'/><label class='lbl'></label>", "30", "", "", "", ""),
	    array("Name", "205", "description", "", "", ""),
            array("Rate Group", "230", "pricelist_id", "name", "pricelists", "get_field_name"),
            array("Amount", "255", "charge", "charge", "charge", "convert_to_currency"),
            array("Billing Cycle", "190", "sweep_id", "sweep", "sweeplist", "get_field_name"),
            array("Status", "220", "status", "status", "status", "get_status"),
            array("Action", "124", "", "", "", array("EDIT" => array("url" => "/charges/periodiccharges_edit/", "mode" => "popup", 'popup'),
                    "DELETE" => array("url" => "/charges/periodiccharges_delete/", "mode" => "single")))
                ));
        return $grid_field_arr;
    }

    function build_grid_buttons() {
        $buttons_json = json_encode(array(array("Create" , "btn btn-line-warning btn" , "fa fa-plus-circle fa-lg", "button_action", "/charges/periodiccharges_add/", "popup"),
            array("Delete", "btn btn-line-danger","fa fa-times-circle fa-lg",  "button_action", "/charges/periodiccharges_delete_multiple/")
            ));
        return $buttons_json;
    }
   
    function build_charges_list_for_customer($accountid, $accounttype) {
        $grid_field_arr = json_encode(array(array("Description", "244", "description", "", "", ""),
            array("Amount", "160", "charge", "", "", ""),
            array("Billing Cycle", "160", "sweep_id", "sweep", "sweeplist", "get_field_name"),
            array("Action", "500", "", "", "", array("DELETE" => array("url" => "/accounts/customer_charges_action/delete/$accountid/$accounttype/", "mode" => "single")))
                ));
        return $grid_field_arr;
    }   

}

?>
