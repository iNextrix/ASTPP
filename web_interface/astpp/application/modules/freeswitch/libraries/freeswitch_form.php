<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Freeswitch_form {

    function __construct() {
        $this->CI = & get_instance();
    }

    function get_freeswith_form_fields($id=false) {
	$log_type = $this->CI->session->userdata("logintype");
	if($log_type == 0  || $log_type == 3 || $log_type == 1){
	      $sip_pro=null;
	}
	else{
	      $sip_pro=array('Sip Profile', 'sip_profile_id', 'SELECT', '', 'trim|dropdown|xss_clean', 'tOOL TIP', 'Please Enter account number', 'id', 'name', 'sip_profiles', 'build_dropdown', '', '');

	}
  //  $val=$id > 0 ? 'sip_devices.username.'.$id : 'sip_devices.username';  
	$val=$id > 0 ? 'sip_devices.username.'.$id : 'sip_devices.username';   
	  $uname_user = $this->CI->common->find_uniq_rendno('10', '', '');
        $password = $this->CI->common->generate_password();
        $form['forms'] = array(base_url() . 'freeswitch/fssipdevices_save/', array("id" => "sipdevices_form", "name" => "sipdevices_form"));
        $form['Sip Devices'] = array(
            array('', 'HIDDEN', array('name' => 'id'), '', '', '', ''),
             array('Username', 'INPUT', array('name' => 'fs_username', 'size' => '20', 'maxlength' => '15','value'=>$uname_user,'id'=>'username', 'class' => "text field medium"), 'trim|required|min_length[2]|max_length[25]|is_unique['.$val.']|xss_clean', 'tOOL TIP', 'Please Enter account number','<i style="cursor:pointer; font-size: 17px; padding-left:10px; padding-top:6px;" title="Reset Password" class="change_number  fa fa-refresh"></i>'),
            array('Password', 'INPUT', array('name' => 'fs_password', 'size' => '20', 'maxlength' => '15','value'=>$password ,'id'=>'password','class' => "text field medium"), 'trim|required|min_length[5]|max_length[30]|xss_clean', 'tOOL TIP', 'Please Enter Password','<i style="cursor:pointer; font-size: 17px; padding-left:10px; padding-top:6px;" title="Reset Password" class="change_pass fa fa-refresh"></i>'),
         //   
		 array('Account', 'accountcode', 'SELECT', '','trim|dropdown|xss_clean', 'tOOL TIP', 'Please Enter account number', 'id', 'first_name,last_name,number', 'accounts', 'build_concat_dropdown', 'where_arr', array("reseller_id" => "0","type"=>"0", "deleted" => "0")),
            array('Caller Name', 'INPUT', array('name' => 'effective_caller_id_name', 'size' => '20', 'maxlength' => '15', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter account number'),
            array('Caller Number', 'INPUT', array('name' => 'effective_caller_id_number', 'size' => '20', 'maxlength' => '15', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter account number'),
            array('Status', 'status', 'SELECT', '', '', 'tOOL TIP', 'Please Select Status', '', '', '', 'set_status'),
           // array('Sip Profile', 'sip_profile_id', 'SELECT', '', 'trim|dropdown|xss_clean', 'tOOL TIP', 'Please Enter account number', 'id', 'name', 'sip_profiles', 'build_dropdown', '', ''),
	   $sip_pro,
//             array('Rate Group', 'pricelist_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'name', 'pricelists', 'build_dropdown', 'where_arr', array("status" => "1","reseller_id" => "0")),
//             array('Context::', 'INPUT', array('name' => 'context', 'size' => '20', 'maxlength' => '15', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter account number'),
        );

        $form['button_cancel'] = array('name' => 'action', 'content' => 'Cancel', 'value' => 'cancel', 'type' => 'button', 'class' => 'btn btn-line-sky margin-x-10', 'onclick' => 'return redirect_page(\'NULL\')');
        $form['button_save'] = array('name' => 'action', 'content' => 'Save', 'value' => 'save', 'id' => 'submit', 'type' => 'button', 'class' => 'btn btn-line-parrot');
//         echo "<pre>";print_r($form);exit; 
        return $form;
    }

    function get_freeswith_search_form() {
        $form['forms'] = array("", array('id' => "freeswith_search"));
        $form['Search'] = array(
            
             array('Sip Profile', 'sip_profile_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'name', 'sip_profiles', 'build_dropdown', 'where_arr', ''),
            array('Username', 'INPUT', array('name' => 'username[username]', '', 'size' => '20', 'class' => "text field"), '', 'tOOL TIP', '1', 'username[username-string]', '', '', '', 'search_string_type', ''),
            array('Gateway', 'gateway_id', 'SELECT', '', '', 'tOOL TIP', 'Please select gateway first', 'id', 'name', 'gateways', 'build_dropdown','where_arr', ''), 
            array('Account', 'accountid', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'first_name,last_name,number', 'accounts', 'build_concat_dropdown', 'where_arr', array("reseller_id" => "0","type"=>"0", "deleted" => "0")),
	 array('Status', 'status', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', '', '', '', 'set_search_status', '', ''),
	array('', 'HIDDEN', 'ajax_search', '1', '', '', ''),array('', 'HIDDEN', 'advance_search', '1', '', '', ''));
        $form['button_search'] = array('name' => 'action', 'id' => "freeswith_search_btn", 'content' => 'Search', 'value' => 'save', 'type' => 'button', 'class' => 'btn btn-line-parrot pull-right');
        $form['button_reset'] = array('name' => 'action', 'id' => "id_reset", 'content' => 'Clear', 'value' => 'cancel', 'type' => 'reset', 'class' => 'btn btn-line-sky pull-right margin-x-10');
        return $form;
    }

      function get_sipdevices_search_form_user() {
        $form['forms'] = array("", array('id' => "sipdevices_search"));
        $form['Search'] = array(
            array('Username', 'INPUT', array('name' => 'username[username]', '', 'size' => '20', 'maxlength' => '15', 'class' => "text field"), '', 'tOOL TIP', '1', 'username[username-string]', '', '', '', 'search_string_type', ''),
            array('Sip Profile', 'sip_profile_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'name', 'sip_profiles', 'build_dropdown', 'where_arr', ''),
	    array('', 'HIDDEN', 'ajax_search', '1', '', '', ''),
            array('', 'HIDDEN', 'advance_search', '1', '', '', ''),
        
	
        );
        $form['button_search'] = array('name' => 'action', 'id' => "sipdevices_search_btn", 'content' => 'Search', 'value' => 'save', 'type' => 'button', 'class' => 'btn btn-line-parrot pull-right');
        $form['button_reset'] = array('name' => 'action', 'id' => "id_reset", 'content' => 'Clear', 'value' => 'cancel', 'type' => 'reset', 'class' => 'btn btn-line-sky pull-right margin-x-10');
        return $form;
    }
    
    function get_gateway_search_form() {
        $form['forms'] = array("", array('id' => "freeswith_search"));
        $form['Search'] = array(
            
            array('Gateway Name', 'INPUT', array('name' => 'name[name]', '', 'size' => '20', 'class' => "text field"), '', 'tOOL TIP', '1', 'name[name-string]', '', '', '', 'search_string_type', ''),
//             array('Gateway Name', 'id', 'SELECT', '', '', 'tOOL TIP', 'Please select gateway first', 'id', 'name', 'gateways', 'build_dropdown','where_arr', ''), 
            array('Sip Profile', 'sip_profile_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'name', 'sip_profiles', 'build_dropdown', 'where_arr', ''),
           // array('Username', 'INPUT', array('name' => 'username[username]', '', 'size' => '20', 'maxlength' => '15', 'class' => "text field"), '', 'tOOL TIP', '1', 'username[username-string]', '', '', '', 'search_string_type', ''),
            
            array('Status', 'status', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', '', '', '', 'set_search_status'),
           // array('Account', 'accountid', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'first_name,last_name,number', 'accounts', 'build_concat_dropdown', 'where_arr', array("reseller_id" => "0","type"=>"0", "deleted" => "0")),
            array('', 'HIDDEN', 'ajax_search', '1', '', '', ''),
            array('', 'HIDDEN', 'advance_search', '1', '', '', ''));
        $form['button_search'] = array('name' => 'action', 'id' => "fsgateway_search_btn", 'content' => 'Search', 'value' => 'save', 'type' => 'button', 'class' => 'btn btn-line-parrot pull-right');
        $form['button_reset'] = array('name' => 'action', 'id' => "id_reset", 'content' => 'Clear', 'value' => 'cancel', 'type' => 'reset', 'class' => 'btn btn-line-sky pull-right margin-x-10');
        return $form;
    }
    function get_sipprofile_search_form(){
    
       $form['forms'] = array("", array('id' => "freeswitch_search"));
        $form['Search'] = array(
            
             array('Profile Name', 'INPUT', array('name' => 'name[name]', '', 'size' => '20', 'class' => "text field"), '', 'tOOL TIP', '1', 'name[name-string]', '', '', '', 'search_string_type', ''),
            // array('Username', 'INPUT', array('name' => 'username[username]', '', 'size' => '20', 'class' => "text field"), '', 'tOOL TIP', '1', 'username[username-string]', '', '', '', 'search_string_type', ''),
//             array('Gateway', 'gateway_id', 'SELECT', '', '', 'tOOL TIP', 'Please select gateway first', 'id', 'name', 'gateways', 'build_dropdown','where_arr', ''), 
             array('SIP IP', 'INPUT', array('name' => 'sip_ip[sip_ip]', 'value' => '', 'size' => '20', 'class' => "text field "), '', 'Tool tips info', '1', 'sip_ip[sip_ip-string]', '', '', '', 'search_string_type', ''),
             array('SIP Port', 'INPUT', array('name' => 'sip_port[sip_port]', 'value' => '', 'size' => '20', 'class' => "text field "), '', 'Tool tips info', '1', 'sip_port[sip_port-string]', '', '', '', 'search_string_type', ''),
             
             array('Status', 'status', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', '', '', '', 'set_search_status'),  
           // array('Account', 'accountid', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'first_name,last_name,number', 'accounts', 'build_concat_dropdown', 'where_arr', array("reseller_id" => "0","type"=>"0", "deleted" => "0")),
            array('', 'HIDDEN', 'ajax_search', '1', '', '', ''),
            array('', 'HIDDEN', 'advance_search', '1', '', '', ''));
        $form['button_search'] = array('name' => 'action', 'id' => "fssipprofile_search_btn", 'content' => 'Search', 'value' => 'save', 'type' => 'button', 'class' => 'btn btn-line-parrot pull-right');
        $form['button_reset'] = array('name' => 'action', 'id' => "id_reset", 'content' => 'Clear', 'value' => 'cancel', 'type' => 'reset', 'class' => 'btn btn-line-sky pull-right margin-x-10');
        return $form;         
    
    }
    function get_sipdevice_search_form() {
        $form['forms'] = array("", array('id' => "freeswith_search"));
        $form['Search'] = array(
            
            array('Username', 'INPUT', array('name' => 'username[username]', '', 'size' => '20', 'class' => "text field"), '', 'tOOL TIP', '1', 'username[username-string]', '', '', '', 'search_string_type', ''),
             array('Sip Profile', 'sip_profile_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'name', 'sip_profiles', 'build_dropdown', 'where_arr', ''),
            
            
            array('Account', 'accountid', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'first_name,last_name,number', 'accounts', 'build_concat_dropdown', 'where_arr', array("reseller_id" => "0","type"=>"GLOBAL", "deleted" => "0")),
            array('Status', 'status', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', '', '', '', 'set_search_status'),  
            array('', 'HIDDEN', 'ajax_search', '1', '', '', ''),array('', 'HIDDEN', 'advance_search', '1', '', '', ''));
        $form['button_search'] = array('name' => 'action', 'id' => "fssipdevice_search_btn", 'content' => 'Search', 'value' => 'save', 'type' => 'button', 'class' => 'btn btn-line-parrot pull-right');
        $form['button_reset'] = array('name' => 'action', 'id' => "id_reset", 'content' => 'Clear', 'value' => 'cancel', 'type' => 'reset', 'class' => 'btn btn-line-sky pull-right margin-x-10');
        return $form;
    }
    function build_system_list_for_admin() {
        // array(display name, width, db_field_parent_table,feidname, db_field_child_table,function name);
        $grid_field_arr = json_encode(array(
	    array("<input type='checkbox' name='chkAll' class='ace checkall'/><label class='lbl'></label>", "50", "", "", "", ""),
            array("User Name", "170", "username", "", "", ""),
            array("Password", "175", "password", "", "", ""),
            
            array("SIP profile", "140", "sip_profile_id", "name", "sip_profiles", "get_field_name"),
            array("Account", "200", "accountid", "first_name,last_name,number", "accounts", "build_concat_string"),
            
            array("Caller Name", "160", "effective_caller_id_name", "", "", ""),
            array("Caller Number", "150", "effective_caller_id_number", "", "", ""),
            
	    array("Status", "100", "status", "status", "status", "get_status"),
            array("Action", "107", "", "", "", array("EDIT" => array("url" => "/freeswitch/fssipdevices_edit/", "mode" => "single"),
                    "DELETE" => array("url" => "/freeswitch/fssipdevices_delete/", "mode" => "single")))
                ));
//                 echo "<pre>";print_r($grid_field_arr);exit;
        return $grid_field_arr;
    }

    function build_grid_buttons() {
        $buttons_json = json_encode(array(array("Create","btn btn-line-warning btn","fa fa-plus-circle fa-lg", "button_action", "/freeswitch/fssipdevices_add/", "popup"),
			array("Delete",  "btn btn-line-danger","fa fa-times-circle fa-lg",  "button_action", "/freeswitch/fssipdevices_delete_multiple/")   
                        ));
        return $buttons_json;
    }
    function build_grid_buttons_for_user() {
        $buttons_json = json_encode(array(array("Create","btn btn-line-warning btn","fa fa-plus-circle fa-lg", "button_action", "/user/user_fssipdevices_action/add/", "popup"),
			array("Delete",  "btn btn-line-danger","fa fa-times-circle fa-lg", "button_action", "/freeswitch/user_fssipdevices_delete_multiple/")
                       ));
        return $buttons_json;
    }

    function fsdevices_build_grid_buttons($accountid) {
        $buttons_json = json_encode(array(array("Create","btn btn-line-warning btn","fa fa-plus-circle fa-lg", "button_action", "/freeswitch/customer_fssipdevices_add/$accountid/", "popup"),
	    array("Delete","btn btn-line-danger","fa fa-times-circle fa-lg", "button_action", "/freeswitch/user_fssipdevices_delete_multiple/")
            ));
        return $buttons_json;
    }

    function get_gateway_form_fields() {

        $form['forms'] = array(base_url() . 'freeswitch/fsgateway_save/', array("id" => "gateway_form", "name" => "gateway_form"));
        $form['Basic Information'] = array(
            array('', 'HIDDEN', array('name' => 'id'), '', '', '', ''),
            array('Gateway Name', 'INPUT', array('name' => 'name', 'size' => '20', 'class' => "text field medium"), 'trim|required|min_length[2]|max_length[25]|xss_clean', 'tOOL TIP', 'Please Enter Gateway Name'),
            array('SIP Profile', 'sip_profile_id', 'SELECT', '', '', 'tOOL TIP', '', 'id', 'name', 'sip_profiles', 'build_dropdown', '', ''),
            array('Username', 'INPUT', array('name' => 'username', 'size' => '20', 'maxlength' => '30', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter user name'),
           array('Password', 'INPUT', array('name' => 'password', 'size' => '20', 'maxlength' => '30', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter Password'),
            array('Proxy', 'INPUT', array('name' => 'proxy', 'size' => '20', 'maxlength' => '35', 'class' => "text field medium"), 'trim|required|xss_clean', 'tOOL TIP', ''),
            array('Outbound-<br/>Proxy', 'INPUT', array('name' => 'outbound-proxy', 'size' => '20', 'maxlength' => '35', 'class' => "text field medium"), 'trim|xss_clean', 'tOOL TIP', ''),
	    array('Register', array('name' => 'register', 'class' => 'add_settings'), 'SELECT', '', '', 'tOOL TIP', '', '', '', '', 'set_sip_config_option'),
            array('Caller-id-in-from', array('name' => 'caller-id-in-from', 'class' => 'add_settings'), 'SELECT', '', '', 'tOOL TIP', '', '', '', '', 'set_sip_config_option'),
                array('Status', 'status', 'SELECT', '', '', 'tOOL TIP', 'Please Select Status', '', '', '', 'set_status'),
            
        );
        $form['Optional Information'] = array(
            array('From-Domain', 'INPUT', array('name' => 'from_domail', 'size' => '20', 'maxlength' => '80', 'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('From User', 'INPUT', array('name' => 'from_user', 'size' => '20', 'maxlength' => '30', 'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('Realm', 'INPUT', array('name' => 'realm', 'size' => '20', 'maxlength' => '80', 'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('Extension', 'INPUT', array('name' => 'extension', 'size' => '20', 'maxlength' => '15', 'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('Expire Seconds', 'INPUT', array('name' => 'expire-seconds', 'size' => '20', 'maxlength' => '15', 'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('Reg-Transport', 'INPUT', array('name' => 'register-transport', 'size' => '20', 'maxlength' => '15', 'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('Contact Params', 'INPUT', array('name' => 'contact-params', 'size' => '20', 'maxlength' => '100', 'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('Ping', 'INPUT', array('name' => 'ping', 'size' => '20', 'maxlength' => '15', 'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('Retry-Seconds', 'INPUT', array('name' => 'retry-seconds', 'size' => '20', 'maxlength' => '15', 'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('Register-Proxy', 'INPUT', array('name' => 'register-proxy', 'size' => '20', 'maxlength' => '15', 'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('Channel', 'INPUT', array('name' => 'channel', 'size' => '20', 'maxlength' => '15', 'class' => "text field medium"), '', 'tOOL TIP', ''),
        );

        $form['button_cancel'] = array('name' => 'action', 'content' => 'Cancel', 'value' => 'cancel', 'type' => 'button', 'class' => 'btn btn-line-sky margin-x-10', 'onclick' => 'return redirect_page(\'NULL\')');
        $form['button_save'] = array('name' => 'action', 'content' => 'Save', 'value' => 'save', 'id' => 'submit', 'type' => 'button', 'class' => 'btn btn-line-parrot');

        return $form;
    }

    function build_fsgateway_list_for_admin() {
        // array(display name, width, db_field_parent_table,feidname, db_field_child_table,function name);
        $grid_field_arr = json_encode(array(
	    array("<input type='checkbox' name='chkAll' class='ace checkall'/><label class='lbl'></label>", "30", "", "", "", ""),
            array("Gateway Name", "220", "name", "", "", ""),
            array("SIP Profile", "170", "sip_profile_id", "name", "sip_profiles", "get_field_name"),
            array("Username", "195", "username", "", "", ""),
//             array("Password", "181", "password", "", "", ""),
            array("Proxy", "130", "proxy", "", "", ""),
            array("Register", "160", "register", "register", "register", "convert_to_ucfirst"),
            array("caller-id-in-form", "130", "caller-id-in-from", "", "", ""),
            array("Status", "110", "status", "status", "status", "get_status"),
            array("Action", "106", "", "", "", array("EDIT" => array("url" => "/freeswitch/fsgateway_edit/", "mode" => "popup"),
                    "DELETE" => array("url" => "/freeswitch/fsgateway_delete/", "mode" => "single")))
                ));
        return $grid_field_arr;
    }

    function build_fdgateway_grid_buttons() {
        $buttons_json = json_encode(array(array("Create","btn btn-line-warning btn","fa fa-plus-circle fa-lg", "button_action", "/freeswitch/fsgateway_add/", "popup"),
	    array("Delete","btn btn-line-danger","fa fa-times-circle fa-lg", "button_action", "/freeswitch/fsgateway_delete_multiple/")
            ));
        return $buttons_json;
    }

    function get_sipprofile_form_fields() {
        $form['forms'] = array(base_url() . 'freeswitch/fssipprofile_save/', array("id" => "fssipprofile_form", "name" => "fssipprofile_form"));
        $form['Basic Information'] = array(
            array('', 'HIDDEN', array('name' => 'id'), '', '', '', ''),
            array('SIP Profile name', 'INPUT', array('name' => 'name', 'size' => '20', 'maxlength' => '15', 'class' => "text field medium"), 'trim|required|min_length[2]|max_length[25]|xss_clean', 'tOOL TIP', 'Please Enter SIP Profile Name'),
            array('sip-ip', 'INPUT', array('name' => 'sip_ip', 'size' => '20', 'maxlength' => '15', 'class' => "text field medium"), 'trim|required|min_length[2]|max_length[16]|xss_clean', 'tOOL TIP', 'Please Enter SIP IP Name'),
            array('sip-port', 'INPUT', array('name' => 'sip_port', 'size' => '20', 'value' => '5060', 'maxlength' => '15', 'class' => "text field medium"), 'trim|required|min_length[2]|max_length[5]|xss_clean', 'tOOL TIP', 'Please Enter SIP Port'),
            array('rtp-ip', 'INPUT', array('name' => 'rtp_ip', 'size' => '20', 'maxlength' => '15', 'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('Dial Plan', 'INPUT', array('name' => 'dialplan', 'size' => '20', 'value' => 'XML', 'maxlength' => '15', 'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('user-agent-string', 'INPUT', array('name' => 'user-agent-string', 'size' => '20', 'value' => 'ASTPP', 'maxlength' => '15', 'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('debug', 'INPUT', array('name' => 'debug', 'size' => '20', 'value' => '0', 'maxlength' => '15', 'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('sip-trace', 'sip-trace', 'SELECT', '', '', 'tOOL TIP', '', '', '', '', 'set_sip_drp_option'),
            array('tls', 'tls', 'SELECT', '', '', 'tOOL TIP', '', '', '', '', 'set_sip_config_options'),
            array('inbound-reg-force-matching-username', 'inbound-reg-force-matching-username', 'SELECT', '', '', 'tOOL TIP', '', '', '', '', 'set_sip_config_option'),
            array('disable-transcoding', 'disable-transcoding', 'SELECT', '', '', 'tOOL TIP', '', '', '', '', 'set_sip_config_option'),
            array('all-reg-options-ping', 'all-reg-options-ping', 'SELECT', '', '', 'tOOL TIP', '', '', '', '', 'set_sip_config_option'),
            array('unregister-on-options-fail', 'unregister-on-options-fail', 'SELECT', '', '', 'tOOL TIP', '', '', '', '', 'set_sip_config_option'),
            array('log-auth-failures', 'log-auth-failures', 'SELECT', '', '', 'tOOL TIP', '', '', '', '', 'set_sip_config_option'),
	    array('Status', 'status', 'SELECT', '', '', 'tOOL TIP', 'Please Select Status', '', '', '', 'set_status'),
        );

        $form['Others Information'] = array(
            array('inbound-bypass-media', 'inbound-bypass-media', 'SELECT', '', '', 'tOOL TIP', '', '', '', '', 'set_sip_config_options'),
            array('inbound-proxy-media', 'inbound-proxy-media', 'SELECT', '', '', 'tOOL TIP', '', '', '', '', 'set_sip_config_options'),
            array('disable-transfer', 'disable-transfer', 'SELECT', '', '', 'tOOL TIP', '', '', '', '', 'set_sip_config_option'),
            array('enable-100rel', 'enable-100rel', 'SELECT', '', '', 'tOOL TIP', '', '', '', '', 'set_sip_config_options'),
            array('rtp-timeout-sec', 'INPUT', array('name' => 'rtp-timeout-sec', 'size' => '20', 'value' => '60', 'maxlength' => '15', 'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('dtmf-duration', 'INPUT', array('name' => 'dtmf-duration', 'size' => '20', 'value' => '2000', 'maxlength' => '15', 'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('manual-redirect','manual-redirect', 'SELECT', '', '', 'tOOL TIP', '', '', '', '', 'set_sip_config_options'),
            array('aggressive-nat-detection', 'aggressive-nat-detection', 'SELECT', '', '', 'tOOL TIP', '', '', '', '', 'set_sip_config_option'),
            array('enable-Timer', 'enable-timer', 'SELECT', '', '', 'tOOL TIP', '', '', '', '', 'set_sip_config_options'),
            array('minimum-session-expires', 'INPUT', array('name' => 'minimum-session-expires', 'value' => '120', 'size' => '20', 'maxlength' => '15', 'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('session-timeout', 'INPUT', array('name' => 'session-timeout-pt', 'value' => '1800', 'size' => '20', 'maxlength' => '15', 'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('auth-calls', 'auth-calls', 'SELECT', '', '', 'tOOL TIP', '', '', '', '', 'set_sip_config_option'),
            array('apply-inbound-acl', 'INPUT', array('name' => 'apply-inbound-acl', 'value' => 'default', 'size' => '20', 'maxlength' => '15', 'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('inbound-codec-prefs', 'INPUT', array('name' => 'inbound-codec-prefs', 'size' => '25', 'maxlength' => '25', 'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('outbound-codec-prefs', 'INPUT', array('name' => 'outbound-codec-prefs', 'size' => '25', 'maxlength' => '25', 'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('inbound-late-negotiation', 'inbound-late-negotiation', 'SELECT', '', '', 'tOOL TIP', '', '', '', '', 'set_sip_config_options'),
            array('inbound-codec-negotiation', 'INPUT', array('name' => 'inbound-codec-negotiation', 'size' => '25', 'maxlength' => '25', 'class' => "text field medium"), '', 'tOOL TIP', ''),
        );
        $form['button_cancel'] = array('name' => 'action', 'content' => 'Cancel', 'value' => 'cancel', 'type' => 'button', 'class' => 'btn btn-line-sky margin-x-10', 'onclick' => 'return redirect_page(\'NULL\')');
        $form['button_save'] = array('content' => 'Save', 'value' => 'save', 'type' => 'button','id'=>'submit', 'class' => 'btn btn-line-parrot');

        return $form;
    }

    function build_fssipprofile_list_for_admin() {
        // array(display name, width, db_field_parent_table,feidname, db_field_child_table,function name);
        $grid_field_arr = json_encode(array(
	    array("<input type='checkbox' name='chkAll' class='ace checkall'/><label class='lbl'></label>", "30", "", "", "", ""),
            array("Profile Name", "200", "name", "", "", ""),
            array("SIP IP", "205", "sip_ip", "", "", ""),
            array("SIP Port", "200", "sip_port", "", "", ""),
           array("Status", "137", "status", "status", "status", "get_status"),
//           array("Profile Status", "150", "status", "status", "status", "get_profile_status"),
             array("Profile Action", "282", "", "", "", array("START" => array("url" => "/freeswitch/fssipprofile_action/start/","mode"=>"single"),
                    "STOP" => array("url" => "/freeswitch/fssipprofile_action/stop/", "mode" => "single"),
                    "RELOAD" => array("url" => "/freeswitch/fssipprofile_action/reload/", "mode" => "single"),
                    "RESCAN" => array("url" => "/freeswitch/fssipprofile_action/rescan/", "mode" => "single"),                    
                    )
                ),  
            array("Action", "202", "", "", "", array("EDIT" => array("url" => "/freeswitch/fssipprofile_edit/", "mode" => ""),
                    "DELETE" => array("url" => "/freeswitch/fssipprofile_delete/", "mode" => "single")))
                ));
        return $grid_field_arr;
    }

    function build_fssipprofile_grid_buttons() {
        $buttons_json = json_encode(array(array("Create","btn btn-line-warning btn","fa fa-plus-circle fa-lg", "button_action", "/freeswitch/fssipprofile_add/",""),
	    array("Delete","btn btn-line-danger","fa fa-times-circle fa-lg","button_action", "/freeswitch/fssipprofile_delete_multiple/")
           ));
        return $buttons_json;
    }

    function build_fssipprofile_params_list_for_admin()
    {
	$grid_field_arr = json_encode(array(
	    array("Name", "450", "name", "", "", ""),
            array("Value", "414", "sip_ip", "", "", ""),
//             array("SIP Port", "250", "sip_port", "", "", ""),
	    array("Action", "400", "", "", "", array("DELETE" => array("url" => "/freeswitch/fssipprofile_delete/", "mode" => "single")))
                ));
        return $grid_field_arr;
    }
    
    function build_fsserver_list() {
        // array(display name, width, db_field_parent_table,feidname, db_field_child_table,function name);
        $grid_field_arr = json_encode(array(
            array("Host", "310", "freeswitch_host", "", "", ""),
            array(" Password", "325", "freeswitch_password", "", "", ""),
            array(" Port", "320", "freeswitch_port", "", "", ""),
            array("Status", "120", "status", "status", "status", "get_status"),
            array("Action", "182", "", "", "", array("EDIT" => array("url" => "/freeswitch/fsserver_edit/", "mode" => "popup"),
                    "DELETE" => array("url" => "/freeswitch/fsserver_delete/", "mode" => "single")))
                ));
        return $grid_field_arr;
    }

    function build_fsserver_grid_buttons() {
        $buttons_json = json_encode(array(array("Create","btn btn-line-warning btn","fa fa-plus-circle fa-lg", "button_action", "/freeswitch/fsserver_add/", "popup"),
array("Delete",  "btn btn-line-danger","fa fa-times-circle fa-lg",  "button_action", "/freeswitch/fssipdevices_delete_multiple/")
            ));
        return $buttons_json;
    }

    function get_form_fsserver_fields() {
        $form['forms'] = array(base_url() . '/freeswitch/fsserver_save/', array("id" => "fsserver_form", "name" => "fsserver_form"));
        $form['Freeswitch Server Information'] = array(
            array('', 'HIDDEN', array('name' => 'id'), '', '', '', ''),
            array(' Host', 'INPUT', array('name' => 'freeswitch_host', 'size' => '20', 'maxlength' => '15', 'class' => "text field medium"), 'trim|required|valid_ip', 'tOOL TIP', 'Please Enter account number'),
            array(' Password', 'INPUT', array('name' => 'freeswitch_password', 'size' => '20', 'maxlength' => '20', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter account number'),
            array(' Port', 'INPUT', array('name' => 'freeswitch_port', 'size' => '20', 'maxlength' => '15', 'class' => "text field medium"), 'trim|required|numeric', 'tOOL TIP', 'Please Enter account number'),
        );

        $form['button_cancel'] = array('name' => 'action', 'content' => 'Cancel', 'value' => 'cancel', 'type' => 'button', 'class' => 'btn btn-line-sky margin-x-10', 'onclick' => 'return redirect_page(\'NULL\')');
        $form['button_save'] = array('name' => 'action', 'content' => 'Save', 'value' => 'save', 'id' => 'submit', 'type' => 'button', 'class' => 'btn btn-line-parrot');

        return $form;
    }

    function get_search_fsserver_form() {
        $form['forms'] = array("", array('id' => "fsserver_search"));
        $form['Search'] = array(
            array(' Host', 'INPUT', array('name' => 'freeswitch_host[freeswitch_host]', '', 'id' => 'first_name', 'size' => '15', 'class' => "text field "), '', 'tOOL TIP', '1', 'freeswitch_host[freeswitch_host-string]', '', '', '', 'search_string_type', ''),
            array(' Port', 'INPUT', array('name' => 'freeswitch_port[freeswitch_port]', 'value' => '', 'size' => '20', 'class' => "text field "), '', 'Tool tips info', '1', 'freeswitch_port[freeswitch_port-string]', '', '', '', 'search_string_type', ''),
		array('Status', 'status', 'SELECT', '', '', 'tOOL TIP', 'Please Select Status', '', '', '', 'set_search_status'),
            array('', 'HIDDEN', 'ajax_search', '1', '', '', ''),
            array('', 'HIDDEN', 'advance_search', '1', '', '', '')
        );
        $form['button_search'] = array('name' => 'action', 'id' => "fsserver_search_btn", 'content' => 'Search', 'value' => 'save', 'type' => 'button', 'class' => 'btn btn-line-parrot pull-right');
        $form['button_reset'] = array('name' => 'action', 'id' => "id_reset", 'content' => 'Clear', 'value' => 'cancel', 'type' => 'reset', 'class' => 'btn btn-line-sky pull-right margin-x-10');

        return $form;
    }

    function build_devices_list_for_customer() {
        // array(display name, width, db_field_parent_table,feidname, db_field_child_table,function name);
        $grid_field_arr = json_encode(array(
	    array("<input type='checkbox' name='chkAll' class='ace checkall'/><label class='lbl'></label>", "30", "", "", "", ""),
            array("User Name", "210", "username", "", "", ""),
            array("Password", "210", "password", "", "", ""),
            array("SIP profile", "160", "sip_profile_id", "name", "sip_profiles", "get_field_name"),
            array("Caller Name", "205", "effective_caller_id_name", "", "", ""),
            array("Caller Number", "180", "effective_caller_id_number", "", "", ""),
            array("Status", "120", "status", "", "", ""),
            array("Action", "139", "", "", "", array("EDIT" => array("url" => "/accounts/fssipdevices_action/edit/", "mode" => "single"),
                    "DELETE" => array("url" => "/accounts/fssipdevices_action/delete/", "mode" => "single")))
                ));
        return $grid_field_arr;
    }

    function fsdevice_form_fields_for_customer($accountid , $id=false) {
    
	// $val = $id > 0 ? 'sip_devices.username.' . $id : 'sip_devices.username';
	$val=$id > 0 ? 'sip_devices.username.'.$id : 'sip_devices.username';   
        $uname_user = $this->CI->common->find_uniq_rendno('10', '', '');
        $password = $this->CI->common->generate_password();
        if ($this->CI->session->userdata("logintype") == '0'  || $this->CI->session->userdata("logintype") == '3') {
            $link = base_url() . 'freeswitch/user_fssipdevices_save/true';
            $form['forms'] = array($link, array("id" => "sipdevices_form", "name" => "sipdevices_form"));
            $form['Sip Devices'] = array(
                array('', 'HIDDEN', array('name' => 'id'), '', '', '', ''),
                array('', 'HIDDEN', array('name' => 'accountcode', 'value' => $accountid), '', '', '', ''),
                   array('Username', 'INPUT', array('name' => 'fs_username', 'size' => '20', 'maxlength' => '15','value'=>$uname_user,'id'=>'username', 'class' => "text field medium"), 'trim|required|min_length[2]|max_length[25]|is_unique['.$val.']|xss_clean', 'tOOL TIP', 'Please Enter account number','<i style="cursor:pointer; font-size: 17px; padding-left:10px; padding-top:6px;" title="Reset Password" class="change_number fa fa-refresh"></i>'),
            array('Password', 'INPUT', array('name' => 'fs_password', 'size' => '20', 'maxlength' => '25','id'=>'password1','value'=>$password, 'class' => "text field medium"), 'trim|required|min_length[5]|max_length[50]|xss_clean', 'tOOL TIP', 'Please Enter Password','<i style="cursor:pointer; font-size: 17px; padding-left:10px; padding-top:6px;" title="Reset Password" class="change_pass fa fa-refresh"></i>'),
                array('Caller Name', 'INPUT', array('name' => 'effective_caller_id_name', 'size' => '20', 'maxlength' => '15', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter account number'),
                array('Caller Number', 'INPUT', array('name' => 'effective_caller_id_number', 'size' => '20', 'maxlength' => '15', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter account number'),
//              array('Sip Profile', 'sip_profile_id', 'SELECT', '','trim|dropdown|xss_clean', 'tOOL TIP', 'Please Enter account number', 'id', 'name', 'sip_profiles', 'build_dropdown', '', ''),
             //   array('Account', 'accountcode', 'SELECT', '','trim|dropdown|xss_clean', 'tOOL TIP', 'Please Enter account number', 'id', 'first_name,last_name,number', 'accounts', 'build_concat_dropdown', 'where_arr', array("reseller_id" => "0","type"=>"0", "deleted" => "0")),
                 array('Status', 'status', 'SELECT', '', '', 'tOOL TIP', 'Please Select Status', '', '', '', 'set_status'),
//                 array('Context::', 'INPUT', array('name' => 'context', 'size' => '20', 'maxlength' => '15', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter account number'),
            );
            
        } else {
             if($accountid){
	            $account_Arr=null;
	     }else{
	            $account_Arr=array('Account', 'accountcode', 'SELECT', '','trim|dropdown|xss_clean', 'tOOL TIP', 'Please Enter account number', 'id', 'first_name,last_name,number', 'accounts', 'build_concat_dropdown', 'where_arr', array("reseller_id" => "0","type"=>"0", "deleted" => "0"));
	     }       
            if ($this->CI->session->userdata("logintype") == '1') {
		$sip_pro =null;
                $link = base_url() . 'freeswitch/customer_fssipdevices_save/true';
            }else{
		$sip_pro =array('Sip Profile', 'sip_profile_id', 'SELECT', '','trim|dropdown|xss_clean', 'tOOL TIP', 'Please Enter account number', 'id', 'name', 'sip_profiles', 'build_dropdown', '', '');
                $link = base_url() . 'freeswitch/fssipdevices_save/true';
            }
            $form['forms'] = array($link, array("id" => "sipdevices_form", "name" => "sipdevices_form"));
            $form['Sip Devices'] = array(
                array('', 'HIDDEN', array('name' => 'id'), '', '', '', ''),
                array('', 'HIDDEN', array('name' => 'accountcode', 'value' => $accountid), '', '', '', ''),
                 array('Username', 'INPUT', array('name' => 'fs_username', 'size' => '20', 'maxlength' => '15','value'=>$uname_user,'id'=>'username', 'class' => "text field medium"), 'trim|required|min_length[2]|max_length[25]|is_unique['.$val.']|xss_clean', 'tOOL TIP', 'Please Enter account number','<i style="cursor:pointer; font-size: 17px; padding-left:10px; padding-top:6px;" title="Reset Password" class="change_number fa fa-refresh"></i>'),
             array('Password', 'INPUT', array('name' => 'fs_password', 'size' => '20', 'maxlength' => '25','id'=>'password1','value'=>$password, 'class' => "text field medium"), 'trim|required|min_length[5]|max_length[50]|xss_clean', 'tOOL TIP', 'Please Enter Password','<i style="cursor:pointer; font-size: 17px; padding-left:10px; padding-top:6px;" title="Reset Password" class="change_pass fa fa-refresh"></i>'),
                array('Caller Name', 'INPUT', array('name' => 'effective_caller_id_name', 'size' => '20', 'maxlength' => '15', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter account number'),
                array('Caller Number', 'INPUT', array('name' => 'effective_caller_id_number', 'size' => '20', 'maxlength' => '15', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter account number'),
		$account_Arr,
              //  array('Sip Profile', 'sip_profile_id', 'SELECT', '','trim|dropdown|xss_clean', 'tOOL TIP', 'Please Enter account number', 'id', 'name', 'sip_profiles', 'build_dropdown', '', ''),
                $sip_pro,
                array('Status', 'status', 'SELECT', '', '', 'tOOL TIP', 'Please Select Status', '', '', '', 'set_status'),
//                 array('Rate Group', 'pricelist_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'name', 'pricelists', 'build_dropdown', 'reseller_id', '0'),
//                 array('Context::', 'INPUT', array('name' => 'context', 'size' => '20', 'maxlength' => '15', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter account number'),
            );
        }

        $form['button_cancel'] = array('name' => 'action', 'content' => 'Cancel', 'value' => 'cancel', 'type' => 'button', 'class' => 'btn btn-line-sky margin-x-10', 'onclick' => 'return redirect_page(\'NULL\')');
        $form['button_save'] = array('name' => 'action', 'content' => 'Save', 'value' => 'save', 'id' => 'submit', 'type' => 'button', 'class' => 'btn btn-line-parrot');

        return $form;
    }

}

?>
