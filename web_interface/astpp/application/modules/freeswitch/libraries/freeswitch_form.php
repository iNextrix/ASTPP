<?php

###############################################################################
# ASTPP - Open Source VoIP Billing Solution
#
# Copyright (C) 2016 iNextrix Technologies Pvt. Ltd.
# Samir Doshi <samir.doshi@inextrix.com>
# ASTPP Version 3.0 and above
# License https://www.gnu.org/licenses/agpl-3.0.html
#
# This program is free software: you can redistribute it and/or modify
# it under the terms of the GNU Affero General Public License as
# published by the Free Software Foundation, either version 3 of the
# License, or (at your option) any later version.
# 
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU Affero General Public License for more details.
# 
# You should have received a copy of the GNU Affero General Public License
# along with this program.  If not, see <http://www.gnu.org/licenses/>.
###############################################################################

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
	      $sip_pro=array('SIP Profile', 'sip_profile_id', 'SELECT', '', 'trim|dropdown|xss_clean', 'tOOL TIP', 'Please Enter account number', 'id', 'name', 'sip_profiles', 'build_dropdown', '', '');

	}  
	$val=$id > 0 ? 'sip_devices.username.'.$id : 'sip_devices.username';   
	  $uname_user = $this->CI->common->find_uniq_rendno('10', '', '');
        $password = $this->CI->common->generate_password();
        /*Edit functionality*/
        $form['forms'] = array(base_url() . 'freeswitch/fssipdevices_save/', array("id" => "sipdevices_form", "name" => "sipdevices_form"));
        $form['Device Information'] = array(
            array('', 'HIDDEN', array('name' => 'id'), '', '', '', ''),
             array('Username', 'INPUT', array('name' => 'fs_username', 'size' => '20', 'value'=>$uname_user,'id'=>'username1', 'class' => "text field medium"), 'trim|required|is_unique['.$val.']|xss_clean', 'tOOL TIP', 'Please Enter account number','<i style="cursor:pointer; color:#1BCB61 !important; font-size:14px;    padding-left:5px;    padding-top:8px;    float:left;" title="Reset Password" class="change_number  fa fa-refresh"></i>'),
            array('Password', 'INPUT', array('name' => 'fs_password', 'size' => '20', 'value'=>$password ,'id'=>'password1','class' => "text field medium"), 'trim|required|xss_clean', 'tOOL TIP', 'Please Enter Password','<i style="cursor:pointer; color:#1BCB61 !important; font-size:14px;    padding-left:5px;    padding-top:8px;    float:left;" title="Reset Password" class="change_pass fa fa-refresh"></i>'), 
			array('Account', 'accountcode', 'SELECT', '','trim|dropdown|xss_clean', 'tOOL TIP', 'Please Enter account number', 'id', 'first_name,last_name,number', 'accounts', 'build_concat_dropdown', 'where_arr', array("reseller_id" => "0","type"=>"0", "deleted" => "0")),
            array('Caller Name', 'INPUT', array('name' => 'effective_caller_id_name', 'size' => '20',  'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter account number'),
            array('Caller Number', 'INPUT', array('name' => 'effective_caller_id_number', 'size' => '20', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter account number'),
            array('Status', 'status', 'SELECT', '', '', 'tOOL TIP', 'Please Select Status', '', '', '', 'set_status'),
	   $sip_pro,
        );
/******
ASTPP  3.0 
Voicemail add edit
******/
		
        $form['Voicemail Options'] = array(
            array('Enable', 'voicemail_enabled', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', '', '', '', 'set_sip_config_option'),
            
			array('Password', 'INPUT', array('name' => 'voicemail_password', 'size' => '20', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter account number'),
			array('Mail To', 'INPUT', array('name' => 'voicemail_mail_to', 'size' => '20',  'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter account number'),
            
            array('Attach File', 'voicemail_attach_file', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', '', '', '', 'custom_status'),
            
            
            array('Local After Email', 'vm_keep_local_after_email', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', '', '', '', 'custom_status'),
            
            
            
            array('Send all Message', 'vm_send_all_message', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', '', '', '', 'custom_status'),


        );
/**************************/
        $form['button_cancel'] = array('name' => 'action', 'content' => 'Close', 'value' => 'cancel', 'type' => 'button', 'class' => 'btn btn-line-sky margin-x-10', 'onclick' => 'return redirect_page(\'NULL\')');
        $form['button_save'] = array('name' => 'action', 'content' => 'Save', 'value' => 'save', 'id' => 'submit', 'type' => 'button', 'class' => 'btn btn-line-parrot');
//         echo "<pre>";print_r($form);exit; 
        return $form;
    }

    function get_freeswith_search_form() {
        $form['forms'] = array("", array('id' => "freeswith_search"));
        $form['Search'] = array(
            
             array('SIP Profile', 'sip_profile_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'name', 'sip_profiles', 'build_dropdown', 'where_arr', ''),
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
            array('Username', 'INPUT', array('name' => 'username[username]', '', 'size' => '20',  'class' => "text field"), '', 'tOOL TIP', '1', 'username[username-string]', '', '', '', 'search_string_type', ''),
            array('SIP Profile', 'sip_profile_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'name', 'sip_profiles', 'build_dropdown', 'where_arr', ''),
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
            
            array('Name', 'INPUT', array('name' => 'name[name]', '', 'size' => '20', 'class' => "text field"), '', 'tOOL TIP', '1', 'name[name-string]', '', '', '', 'search_string_type', ''),
//             array('Gateway Name', 'id', 'SELECT', '', '', 'tOOL TIP', 'Please select gateway first', 'id', 'name', 'gateways', 'build_dropdown','where_arr', ''), 
            array('SIP Profile', 'sip_profile_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'name', 'sip_profiles', 'build_dropdown', 'where_arr', ''),
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
            
             array('Name', 'INPUT', array('name' => 'name[name]', '', 'size' => '20', 'class' => "text field"), '', 'tOOL TIP', '1', 'name[name-string]', '', '', '', 'search_string_type', ''),
             array('SIP IP', 'INPUT', array('name' => 'sip_ip[sip_ip]', 'value' => '', 'size' => '20', 'class' => "text field "), '', 'Tool tips info', '1', 'sip_ip[sip_ip-string]', '', '', '', 'search_string_type', ''),
             array('SIP Port', 'INPUT', array('name' => 'sip_port[sip_port]', 'value' => '', 'size' => '20', 'class' => "text field "), '', 'Tool tips info', '1', 'sip_port[sip_port-string]', '', '', '', 'search_string_type', ''),
             
             array('Status', 'status', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', '', '', '', 'set_search_status'),  
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
            array('SIP Profile', 'sip_profile_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'name', 'sip_profiles', 'build_dropdown', 'where_arr', ''),
            array('Account', 'accountid', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'first_name,last_name,number', 'accounts', 'build_concat_dropdown', 'where_arr', array("reseller_id" => "0","type"=>"GLOBAL", "deleted" => "0")),
            array('Status', 'status', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', '', '', '', 'set_search_status'),  
            //array('Voicemail', 'vm-enabled', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', '', '', '', 'set_voicemail_status'),
            array('', 'HIDDEN', 'ajax_search', '1', '', '', ''),array('', 'HIDDEN', 'advance_search', '1', '', '', ''));
        $form['button_search'] = array('name' => 'action', 'id' => "fssipdevice_search_btn", 'content' => 'Search', 'value' => 'save', 'type' => 'button', 'class' => 'btn btn-line-parrot pull-right');
        $form['button_reset'] = array('name' => 'action', 'id' => "id_reset", 'content' => 'Clear', 'value' => 'cancel', 'type' => 'reset', 'class' => 'btn btn-line-sky pull-right margin-x-10');
        return $form;
    }
    /*
    ASTPP  3.0
    Changes in gried size
    */
    function build_system_list_for_admin() {
        $grid_field_arr = json_encode(array(
	    array("<input type='checkbox' name='chkAll' class='ace checkall'/><label class='lbl'></label>", "50", "", "", "", "","","false","center"),
            array("User Name", "100", "username", "", "", "","","true","center"),
            array("Password", "100", "password", "", "", "","","true","center"),
            array("SIP Profile", "100", "sip_profile_id", "name", "sip_profiles", "get_field_name","","true","center"),
            array("Account", "150", "accountid", "first_name,last_name,number", "accounts", "build_concat_string","","true","center"),
            array("Caller Name", "100", "effective_caller_id_name", "", "", "","","true","center"),
            array("Caller Number", "100", "effective_caller_id_number", "", "", "","","true","center"),
            array("Voicemail", "100", "voicemail_enabled", "", "", "","","true","center"),	
            array("Status", "100", "status", "status", "sip_devies", "get_status","","true","center"),
            array("Created Date", "130", "creation_date", "creation_date", "creation_date", "convert_GMT_to","","true","center"),
            array("Modified Date", "130", "last_modified_date", "last_modified_date", "last_modified_date", "convert_GMT_to","","true","center"),
            array("Action", "107", "", "", "", array("EDIT" => array("url" => "/freeswitch/fssipdevices_edit/", "mode" => "single","layout"=>"medium"),
                    "DELETE" => array("url" => "/freeswitch/fssipdevices_delete/", "mode" => "single")))
                ));
        return $grid_field_arr;
    }
    /*********************************/


    function build_grid_buttons() {
        $buttons_json = json_encode(array(array("Create","btn btn-line-warning btn","fa fa-plus-circle fa-lg", "button_action", "/freeswitch/fssipdevices_add/", "popup","medium"),
			array("Delete",  "btn btn-line-danger","fa fa-times-circle fa-lg",  "button_action", "/freeswitch/fssipdevices_delete_multiple/")   
                        ));
        return $buttons_json;
    }
    
    function fsdevices_build_grid_buttons($accountid) {
        $buttons_json = json_encode(array(array("Create","btn btn-line-warning btn","fa fa-plus-circle fa-lg", "button_action", "/freeswitch/customer_fssipdevices_add/$accountid/", "popup","medium"),
	    array("Delete","btn btn-line-danger","fa fa-times-circle fa-lg", "button_action", "/freeswitch/customer_fssipdevices_delete_multiple/")
            ));
        return $buttons_json;
    }

    function get_gateway_form_fields() {

        $form['forms'] = array(base_url() . 'freeswitch/fsgateway_save/', array("id" => "gateway_form", "name" => "gateway_form"));
        $form['Basic Information'] = array(
            array('', 'HIDDEN', array('name' => 'id'), '', '', '', ''),
            array('Name', 'INPUT', array('name' => 'name', 'size' => '20', 'class' => "text field medium"), 'trim|required|xss_clean', 'tOOL TIP', 'Please Enter Gateway Name'),
            array('SIP Profile', 'sip_profile_id', 'SELECT', '', '', 'tOOL TIP', '', 'id', 'name', 'sip_profiles', 'build_dropdown', '', ''),
            array('Username', 'INPUT', array('name' => 'username', 'size' => '20',  'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter user name'),
           array('Password', 'INPUT', array('name' => 'password', 'size' => '20',  'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter Password'),
            array('Proxy', 'INPUT', array('name' => 'proxy', 'size' => '20',  'class' => "text field medium"), 'trim|required|xss_clean', 'tOOL TIP', ''),
            array('Outbound-<br/>Proxy', 'INPUT', array('name' => 'outbound-proxy', 'size' => '20',  'class' => "text field medium"), 'trim|xss_clean', 'tOOL TIP', ''),
			array('Register', array('name' => 'register', 'class' => 'add_settings'), 'SELECT', '', '', 'tOOL TIP', '', '', '', '', 'set_sip_config_option'),
            array('Caller-id-in-from', array('name' => 'caller-id-in-from', 'class' => 'add_settings'), 'SELECT', '', '', 'tOOL TIP', '', '', '', '', 'set_sip_config_option'),
                array('Status', 'status', 'SELECT', '', '', 'tOOL TIP', 'Please Select Status', '', '', '', 'set_status'),
            
        );
        $form['Optional Information'] = array(
            array('From- Domain', 'INPUT', array('name' => 'from_domain', 'size' => '20',  'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('From User', 'INPUT', array('name' => 'from_user', 'size' => '20',  'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('Realm', 'INPUT', array('name' => 'realm', 'size' => '20',  'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('Extension', 'INPUT', array('name' => 'extension', 'size' => '20', 'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('Expire Seconds', 'INPUT', array('name' => 'expire-seconds', 'size' => '20', 'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('Reg-<br/>Transport', 'INPUT', array('name' => 'register-transport', 'size' => '20','class' => "text field medium"), '', 'tOOL TIP', ''),
            array('Contact Params', 'INPUT', array('name' => 'contact-params', 'size' => '20', 'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('Ping', 'INPUT', array('name' => 'ping', 'size' => '20', 'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('Retry-<br/>Seconds', 'INPUT', array('name' => 'retry-seconds', 'size' => '20',  'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('Register-<br/>Proxy', 'INPUT', array('name' => 'register-proxy', 'size' => '20',  'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('Channel', 'INPUT', array('name' => 'channel', 'size' => '20', 'class' => "text field medium"), '', 'tOOL TIP', ''),
               /**
               ASTPP  3.0 
                              put one variable with the name of dialplan variable 
              **/ 
              array('Dialplan Variable', 'TEXTAREA', array('name' => 'dialplan_variable', 'size' => '20','cols'=>'10','rows'=>'5', 'class' => "col_md-5 form-control", 'style'=>"width: 200px; height: 100px;font-family: Open sans,sans-serif !important;"), '', 'tOOL TIP', ''),
             /*********************************************************************************************************/
        );

        $form['button_cancel'] = array('name' => 'action', 'content' => 'Close', 'value' => 'cancel', 'type' => 'button', 'class' => 'btn btn-line-sky margin-x-10', 'onclick' => 'return redirect_page(\'NULL\')');
        $form['button_save'] = array('name' => 'action', 'content' => 'Save', 'value' => 'save', 'id' => 'submit', 'type' => 'button', 'class' => 'btn btn-line-parrot');

        return $form;
    }

    /*
    ASTPP  3.0 
     changes in grid size
    */
    function build_fsgateway_list_for_admin() {
        // array(display name, width, db_field_parent_table,feidname, db_field_child_table,function name);
        $grid_field_arr = json_encode(array(
	    array("<input type='checkbox' name='chkAll' class='ace checkall'/><label class='lbl'></label>", "30", "", "", "", "","","false","center"),
/**
ASTPP  3.0 
For Gateway Edit on Gateway name
**/
            array("Name", "140", "name", "", "", "","EDITABLE","true","center"),
/**********************************/
            array("SIP Profile", "115", "sip_profile_id", "name", "sip_profiles", "get_field_name","","true","center"),
            array("Username", "140", "username", "", "", "","","true","center"),
//             array("Password", "181", "password", "", "", ""),
            array("Proxy", "145", "proxy", "", "", "","","true","center"),
            array("Register", "120", "register", "register", "register", "convert_to_ucfirst","","true","center"),
            array("Caller-Id-In-Form", "130", "caller-id-in-from", "caller-id-in-from", "caller-id-in-from", "convert_to_ucfirst","","true","center"),
             /*
            ASTPP  3.0 
             creation field show in grid
            */
            array("Status", "110", "status", "status", "gateways", "get_status","","true","center"),
            array("Created Date", "100", "created_date", "creation_date", "creation_date", "convert_GMT_to","","true","center"),
             array("Modified Date", "130", "last_modified_date", "last_modified_date", "last_modified_date", "convert_GMT_to","","true","center"),
            /********************************************************************/
           /*
            ASTPP  3.0 
            
            status show active or inactive
            */
           
            /******************************************/
            array("Action", "106", "", "", "", array("EDIT" => array("url" => "/freeswitch/fsgateway_edit/", "mode" => "popup","layout"=>"medium"),
                    "DELETE" => array("url" => "/freeswitch/fsgateway_delete/", "mode" => "single")))
                ));
        return $grid_field_arr;
    }
    /******************************************************/

    function build_fdgateway_grid_buttons() {
        $buttons_json = json_encode(array(array("Create","btn btn-line-warning btn","fa fa-plus-circle fa-lg", "button_action", "/freeswitch/fsgateway_add/", "popup","medium"),
	    array("Delete","btn btn-line-danger","fa fa-times-circle fa-lg", "button_action", "/freeswitch/fsgateway_delete_multiple/")
            ));
        return $buttons_json;
    }

    function get_sipprofile_form_fields() {
        $form['forms'] = array(base_url() . 'freeswitch/fssipprofile_save/', array("id" => "fssipprofile_form", "name" => "fssipprofile_form"));
        $form['Basic Information'] = array(
            array('', 'HIDDEN', array('name' => 'id'), '', '', '', ''),
            array('Profile name', 'INPUT', array('name' => 'name', 'size' => '20',  'class' => "text field medium"), 'trim|required|xss_clean', 'tOOL TIP', 'Please Enter Name'),
            array('sip-ip', 'INPUT', array('name' => 'sip_ip', 'size' => '20',  'class' => "text field medium"), 'trim|required|xss_clean', 'tOOL TIP', 'Please Enter SIP IP Name'),
            array('sip-port', 'INPUT', array('name' => 'sip_port', 'size' => '20', 'value' => '5060',  'class' => "text field medium"), 'trim|required|xss_clean', 'tOOL TIP', 'Please Enter SIP Port'),
            array('rtp-ip', 'INPUT', array('name' => 'rtp_ip', 'size' => '20', 'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('Dial Plan', 'INPUT', array('name' => 'dialplan', 'size' => '20', 'value' => 'XML',  'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('user-agent-string', 'INPUT', array('name' => 'user-agent-string', 'size' => '20', 'value' => 'ASTPP',  'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('debug', 'INPUT', array('name' => 'debug', 'size' => '20', 'value' => '0',  'class' => "text field medium"), '', 'tOOL TIP', ''),
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
            array('rtp-timeout-sec', 'INPUT', array('name' => 'rtp-timeout-sec', 'size' => '20', 'value' => '60', 'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('dtmf-duration', 'INPUT', array('name' => 'dtmf-duration', 'size' => '20', 'value' => '2000', 'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('manual-redirect','manual-redirect', 'SELECT', '', '', 'tOOL TIP', '', '', '', '', 'set_sip_config_options'),
            array('aggressive-nat-detection', 'aggressive-nat-detection', 'SELECT', '', '', 'tOOL TIP', '', '', '', '', 'set_sip_config_option'),
            array('enable-Timer', 'enable-timer', 'SELECT', '', '', 'tOOL TIP', '', '', '', '', 'set_sip_config_options'),
            array('minimum-session-expires', 'INPUT', array('name' => 'minimum-session-expires', 'value' => '120', 'size' => '20', 'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('session-timeout', 'INPUT', array('name' => 'session-timeout-pt', 'value' => '1800', 'size' => '20', 'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('auth-calls', 'auth-calls', 'SELECT', '', '', 'tOOL TIP', '', '', '', '', 'set_sip_config_option'),
            array('apply-inbound-acl', 'INPUT', array('name' => 'apply-inbound-acl', 'value' => 'default', 'size' => '20',  'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('inbound-codec-prefs', 'INPUT', array('name' => 'inbound-codec-prefs', 'size' => '25',  'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('outbound-codec-prefs', 'INPUT', array('name' => 'outbound-codec-prefs', 'size' => '25', 'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('inbound-late-negotiation', 'inbound-late-negotiation', 'SELECT', '', '', 'tOOL TIP', '', '', '', '', 'set_sip_config_options'),
            array('inbound-codec-negotiation', 'INPUT', array('name' => 'inbound-codec-negotiation', 'size' => '25', 'class' => "text field medium"), '', 'tOOL TIP', ''),
        );
        $form['button_cancel'] = array('name' => 'action', 'content' => 'Close', 'value' => 'cancel', 'type' => 'button', 'class' => 'btn btn-line-sky margin-x-10', 'onclick' => 'return redirect_page(\'NULL\')');
        $form['button_save'] = array('content' => 'Save', 'value' => 'save', 'type' => 'button','id'=>'submit', 'class' => 'btn btn-line-parrot');

        return $form;
    }

    function build_fssipprofile_list_for_admin() {
        // array(display name, width, db_field_parent_table,feidname, db_field_child_table,function name);
        $grid_field_arr = json_encode(array(
	    array("<input type='checkbox' name='chkAll' class='ace checkall'/><label class='lbl'></label>", "30", "", "", "", "","","false","center"),
/**
ASTPP  3.0
For Sip Profile edit on Profile name
**/
            array("Name", "190", "name", "", "", "","EDITABLE","true","center"),
/************************************/
            array("SIP IP", "205", "sip_ip", "", "", "","","true","center"),
            array("SIP Port", "200", "sip_port", "", "", "","","true","center"),
           /*
            ASTPP  3.0 
            status show active or inactive
            */
            array("Status", "160", "status", "status", "sip_profiles", "get_status","","true","center"),
           /*******************************************************/
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
    /*
    ASTPP  3.0 
    changes in gried size
    */
    function build_fsserver_list() {
        // array(display name, width, db_field_parent_table,feidname, db_field_child_table,function name);
        $grid_field_arr = json_encode(array(
            array("Host", "200", "freeswitch_host", "", "", "","","true","center"),
            array(" Password", "200", "freeswitch_password", "", "", "","","true","center"),
            array(" Port", "200", "freeswitch_port", "", "", "","","true","center"),
            /*
            ASTPP  3.0 .3 creation field show in grid
            */
            array("Status", "145", "status", "status", "freeswich_servers", "get_status","","true","center"),
            array("Created Date", "170", "creation_date", "creation_date", "creation_date", "convert_GMT_to","","true","center"),
             array("Modified Date", "170", "last_modified_date", "last_modified_date", "last_modified_date", "convert_GMT_to","","true","center"),
            /********************************************************************/
           
            array("Action", "182", "", "", "", array("EDIT" => array("url" => "/freeswitch/fsserver_edit/", "mode" => "popup"),
                    "DELETE" => array("url" => "/freeswitch/fsserver_delete/", "mode" => "single")))
                ));
        return $grid_field_arr;
    }
    /**************************************************/

    function build_fsserver_grid_buttons() {
        $buttons_json = json_encode(array(array("Create","btn btn-line-warning btn","fa fa-plus-circle fa-lg", "button_action", "/freeswitch/fsserver_add/", "popup"),
//array("Delete",  "btn btn-line-danger","fa fa-times-circle fa-lg",  "button_action", "/freeswitch/fssipdevices_delete_multiple/")
            ));
        return $buttons_json;
    }

    function get_form_fsserver_fields() {
        $form['forms'] = array(base_url() . '/freeswitch/fsserver_save/', array("id" => "fsserver_form", "name" => "fsserver_form"));
        $form['Freeswitch Server Information'] = array(
            array('', 'HIDDEN', array('name' => 'id'), '', '', '', ''),
            array(' Host', 'INPUT', array('name' => 'freeswitch_host', 'size' => '20', 'class' => "text field medium"), 'trim|required|valid_ip', 'tOOL TIP', 'Please Enter account number'),
            array(' Password', 'INPUT', array('name' => 'freeswitch_password', 'size' => '20',  'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter account number'),
            array(' Port', 'INPUT', array('name' => 'freeswitch_port', 'size' => '20',  'class' => "text field medium"), 'trim|required|numeric', 'tOOL TIP', 'Please Enter account number'),
        );

        $form['button_cancel'] = array('name' => 'action', 'content' => 'Close', 'value' => 'cancel', 'type' => 'button', 'class' => 'btn btn-line-sky margin-x-10', 'onclick' => 'return redirect_page(\'NULL\')');
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
	/*
	ASTPP  3.0 
	CHanges in grid size
	*/
    function build_devices_list_for_customer() {
        $grid_field_arr = json_encode(array(
			array("<input type='checkbox' name='chkAll' class='ace checkall'/><label class='lbl'></label>", "30", "", "", "", "","","false","center"),
            array("User Name", "100", "username", "", "", "","","true","center"),
            array("Password", "100", "password", "", "", "","","true","center"),
            array("SIP Profile", "90", "sip_profile_id", "name", "sip_profiles", "get_field_name","","true","center"),
            array("Caller Name", "100", "effective_caller_id_name", "", "", "","","true","center"),
            array("Caller Number", "100", "effective_caller_id_number", "", "", "","","true","center"),
            array("Voicemail", "100", "voicemail_enabled", "", "", "","","true","center"),
            //array("Call Waiting", "105", "call_waiting", "", "", ""),
            array("Status", "100", "status", "", "", "","","true","center"),
			array("Created Date", "100", "creation_date", "creation_date", "creation_date", "convert_GMT_to","","true","center"),
            array("Modified Date", "100", "last_modified_date", "last_modified_date", "last_modified_date", "convert_GMT_to","","true","center"),
            array("Action", "110", "", "", "", array("EDIT" => array("url" => "/accounts/fssipdevices_action/edit/", "mode" => "single"),
                    "DELETE" => array("url" => "/accounts/fssipdevices_action/delete/", "mode" => "single")))
                ));
        return $grid_field_arr;
    }
    
    /******************8*/

    function fsdevice_form_fields_for_customer($accountid , $id=false) {
    
	// $val = $id > 0 ? 'sip_devices.username.' . $id : 'sip_devices.username';
	$val=$id > 0 ? 'sip_devices.username.'.$id : 'sip_devices.username';   
        $uname_user = $this->CI->common->find_uniq_rendno('10', '', '');
        $password = $this->CI->common->generate_password();
        if ($this->CI->session->userdata("logintype") == '0'  || $this->CI->session->userdata("logintype") == '3') {
            $link = base_url() . 'freeswitch/user_fssipdevices_save/true';
            $form['forms'] = array($link, array("id" => "sipdevices_form", "name" => "sipdevices_form"));
            $form['Device Information'] = array(
                array('', 'HIDDEN', array('name' => 'id'), '', '', '', ''),
                array('', 'HIDDEN', array('name' => 'accountcode', 'value' => $accountid), '', '', '', ''),
                array('Username', 'INPUT', array('name' => 'fs_username', 'size' => '20', 'value'=>$uname_user,'id'=>'username', 'class' => "text field medium"), 'trim|required|is_unique['.$val.']|xss_clean', 'tOOL TIP', 'Please Enter account number','<i style="cursor:pointer;color:#1BCB61 !important;font-size:14px; padding-left:5px; padding-top:8px; float:left; " title="Reset Password" class="change_number fa fa-refresh"></i>'),
                array('Password', 'INPUT', array('name' => 'fs_password', 'size' => '20','id'=>'password1','value'=>$password, 'class' => "text field medium"), 'trim|required|xss_clean', 'tOOL TIP', 'Please Enter Password','<i style="cursor:pointer;color:#1BCB61 !important;  font-size:14px;    padding-left:5px;    padding-top:8px;    float:left;" title="Reset Password" class="change_pass fa fa-refresh"></i>'),
                array('Caller Name', 'INPUT', array('name' => 'effective_caller_id_name', 'size' => '20',  'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter account number'),
                array('Caller Number', 'INPUT', array('name' => 'effective_caller_id_number', 'size' => '20', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter account number'),
                //array('Call Waiting', 'call_waiting', 'SELECT', '', '', 'tOOL TIP', 'Please Select Status', '', '', '', 'set_call_waiting'),
                array('Status', 'status', 'SELECT', '', '', 'tOOL TIP', 'Please Select Status', '', '', '', 'set_status'),
            );
 /******
ASTPP  3.0 
Voicemail add edit
******/
       $form['Voicemail Options'] = array(
            
            array('Enable', 'voicemail_enabled', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', '', '', '', 'set_sip_config_option'),
            
            
			array('Password', 'INPUT', array('name' => 'voicemail_password', 'size' => '20', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter account number'),
			array('Mail To', 'INPUT', array('name' => 'voicemail_mail_to', 'size' => '20', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter account number'),
			
			array('Attach File', 'voicemail_attach_file', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', '', '', '', 'custom_status_true'),	
			array('Local After Email', 'vm_keep_local_after_email', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', '', '', '', 'custom_status_true'),
			
			array('Send all Message', 'vm_send_all_message', 'CHECKBOX', array('name' => 'vm_send_all_message', 'value' => 'on', 'checked' => false), '', 'tOOL TIP', 'Please Select Status', 'custom_status_true', '', '', ''),
        
        );   
/*******************/           
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
		$sip_pro =array('SIP Profile', 'sip_profile_id', 'SELECT', '','trim|dropdown|xss_clean', 'tOOL TIP', 'Please Enter account number', 'id', 'name', 'sip_profiles', 'build_dropdown', '', '');
                $link = base_url() . 'freeswitch/fssipdevices_save/true';
            }
            $form['forms'] = array($link, array("id" => "sipdevices_form", "name" => "sipdevices_form"));
            /*Add sipdevice*/
            $form['Device Information'] = array(
                array('', 'HIDDEN', array('name' => 'id'), '', '', '', ''),
                array('', 'HIDDEN', array('name' => 'accountcode', 'value' => $accountid), '', '', '', ''),
                 array('Username', 'INPUT', array('name' => 'fs_username', 'size' => '20', 'value'=>$uname_user,'id'=>'username1', 'class' => "text field medium"), 'trim|required|is_unique['.$val.']|xss_clean', 'tOOL TIP', 'Please Enter account number','<i style="cursor:pointer; color:#1BCB61; font-size:14px; padding-left:5px;  padding-top:8px; float:left;" title="Reset Password" class="change_number fa fa-refresh"></i>'),
             array('Password', 'INPUT', array('name' => 'fs_password', 'size' => '20', 'id'=>'password1','value'=>$password, 'class' => "text field medium"), 'trim|required|xss_clean', 'tOOL TIP', 'Please Enter Password','<i style="cursor:pointer; color:#1BCB61; font-size:14px; padding-left:5px;padding-top:8px; float:left;" title="Reset Password" class="change_pass fa fa-refresh"></i>'),
                array('Caller Name', 'INPUT', array('name' => 'effective_caller_id_name', 'size' => '20', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter account number'),
                array('Caller Number', 'INPUT', array('name' => 'effective_caller_id_number', 'size' => '20', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter account number'),
		$account_Arr,
                array('Status', 'status', 'SELECT', '', '', 'tOOL TIP', 'Please Select Status', '', '', '', 'set_status'),
                 $sip_pro,
            );
/******
ASTPP  3.0 
Voicemail add edit
******/
	 $form['Voicemail Options'] = array(
            array('Enable', 'voicemail_enabled', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', '', '', '', 'set_sip_config_option'),
			array('Password', 'INPUT', array('name' => 'voicemail_password', 'size' => '20', 'class' => "text field medium"), '', 'tOOL TIP', 'PleaseEnter account number'),
			array('Mail To', 'INPUT', array('name' => 'voicemail_mail_to', 'size' => '20',  'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter account number'),
			array('Attach File', 'voicemail_attach_file', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', '', '', '', 'custom_status'),
			//array('Local After Email', 'vm_keep_local_after_email', 'CHECKBOX', array('name' => 'vm_keep_local_after_email', 'value' => 'on', 'checked' => false), '', 'tOOL TIP', 'Please Select Status', 'custom_status_true', '', '', ''),
			array('Local After Email', 'vm_keep_local_after_email', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', '', '', '', 'custom_status'),
			//array('Send all Message', 'vm_send_all_message', 'CHECKBOX', array('name' => 'vm_send_all_message', 'value' => 'on', 'checked' => false), '', 'tOOL TIP', 'Please Select Status', 'custom_status_true', '', '', ''),
			
			array('Send all Message', 'vm_send_all_message', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', '', '', '', 'custom_status'),
	 
        
        );
/********************/
        }

        $form['button_cancel'] = array('name' => 'action', 'content' => 'Close', 'value' => 'cancel', 'type' => 'button', 'class' => 'btn btn-line-sky margin-x-10', 'onclick' => 'return redirect_page(\'NULL\')');
        $form['button_save'] = array('name' => 'action', 'content' => 'Save', 'value' => 'save', 'id' => 'submit', 'type' => 'button', 'class' => 'btn btn-line-parrot');

        return $form;
    }

}

?>
