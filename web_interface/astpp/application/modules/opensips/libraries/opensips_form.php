<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
class Opensips_form{
    function __construct() {
        $this->CI = & get_instance();
    }
    
    function get_opensips_form_fields() {

        $form['forms'] = array(base_url() . 'opensips/opensips_save/',array("id"=>"opensips_form","name"=>"opensips_form"));
        $form['Opensips Device'] = array(
            array('', 'HIDDEN', array('name'=>'id'),'', '', '', ''), 
            array('Username', 'INPUT', array('name' => 'username','size' => '20', 'maxlength' => '15', 'class' => "text field medium"), 'trim|required', 'tOOL TIP', ''),
            array('password', 'PASSWORD', array('name' => 'password','size' => '20', 'maxlength' => '15', 'class' => "text field medium"), 'trim|required', 'tOOL TIP', ''),
            array('Account', 'accountcode', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'number', 'number', 'accounts', 'build_dropdown', 'where_arr', array("reseller_id" => "0","type"=>"0", "deleted" => "0")),
            array('Rate Group', 'pricelist_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'name', 'pricelists', 'build_dropdown', 'reseller_id', '0'),
            array('Domain', 'INPUT', array('name' => 'domain','size' => '20', 'maxlength' => '15', 'class' => "text field medium"), '', 'tOOL TIP', '')
            
         );
        $form['button_save'] = array('name' => 'action', 'content' =>'Save' , 'value' => 'save', 'type' => 'button','id'=>'submit', 'class' => 'ui-state-default float-right ui-corner-all ui-button');
        $form['button_cancel'] = array('name' => 'action', 'content' => 'Cancel', 'value' => 'cancel', 'type' => 'button', 'class' => 'ui-state-default float-right ui-corner-all ui-button', 'onclick' => 'return redirect_page(\'NULL\')');
        return $form;
    }
    
     function get_dispatcher_form_fields() {

        $form['forms'] = array(base_url() . 'opensips/dispatcher_save/',array("id"=>"opensips_dispatcher_form","name"=>"opensips_dispatcher_form"));
        $form['Dispatcher Information'] = array(
            array('', 'HIDDEN', array('name'=>'id'),'', '', '', ''), 
            array('Setid', 'INPUT', array('name' => 'setid','size' => '20', 'maxlength' => '15', 'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('Destination', 'INPUT', array('name' => 'destination','size' => '20', 'maxlength' => '15', 'class' => "text field medium"), 'trim|required', 'tOOL TIP', ''),
            array('Flags', 'INPUT', array('name' => 'flags','size' => '20', 'maxlength' => '15', 'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('Weight', 'INPUT', array('name' => 'weight','size' => '20', 'maxlength' => '15', 'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('Attrs', 'INPUT', array('name' => 'attrs','size' => '20', 'maxlength' => '15', 'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('Description', 'INPUT', array('name' => 'description','size' => '100', 'maxlength' => '100', 'class' => "text field medium"), '', 'tOOL TIP', ''),
            
         );
        $form['button_save'] = array('name' => 'action', 'content' =>'Save' , 'value' => 'save', 'type' => 'button','id'=>'submit', 'class' => 'ui-state-default float-right ui-corner-all ui-button');
        $form['button_cancel'] = array('name' => 'action', 'content' => 'Cancel', 'value' => 'cancel', 'type' => 'button', 'class' => 'ui-state-default float-right ui-corner-all ui-button', 'onclick' => 'return redirect_page(\'/opensips/dispatcher_list/\')');
        return $form;
    }
     function get_search_opensips_form()
    {
        $form['forms'] = array("",array('id'=>"device_search"));
        $form['Search Devices'] = array(
            array('', 'HIDDEN', 'ajax_search','1', '', '', ''),    
            array('', 'HIDDEN', 'advance_search','1', '', '', ''),    
             array('Username', 'INPUT', array('name' => 'username[username]','','size' => '20','maxlength' => '15', 'class' => "text field "), '', 'tOOL TIP', '1', 'username[username-string]', '', '','', 'search_string_type', ''),            
            );
        
        $form['button_search'] = array('name' => 'action', 'id'=>"opensipsdevice_search_btn",'content' => 'Search', 'value' => 'save', 'type' => 'button', 'class' => 'ui-state-default float-right ui-corner-all ui-button');
        $form['button_reset'] = array('name' => 'action','id'=>"id_reset", 'content' => 'Clear Search Filter', 'value' => 'cancel', 'type' => 'reset', 'class' => 'ui-state-default float-right ui-corner-all ui-button');
        
        return $form;
    }
       function get_search_dispatcher_form()
    {
        $form['forms'] = array("",array('id'=>"dispatcher_search"));
        $form['Search Dispatcher'] = array(
            array('', 'HIDDEN', 'ajax_search','1', '', '', ''),    
            array('', 'HIDDEN', 'advance_search','1', '', '', ''),    
             array('Username', 'INPUT', array('name' => 'username[username]','','size' => '20','maxlength' => '15', 'class' => "text field "), '', 'tOOL TIP', '1', 'username[username-string]', '', '','', 'search_string_type', ''),            
            );
        
        $form['button_search'] = array('name' => 'action', 'id'=>"opensipsdispatcher_search_btn",'content' => 'Search', 'value' => 'save', 'type' => 'button', 'class' => 'ui-state-default float-right ui-corner-all ui-button');
        $form['button_reset'] = array('name' => 'action','id'=>"id_reset", 'content' => 'Clear Search Filter', 'value' => 'cancel', 'type' => 'reset', 'class' => 'ui-state-default float-right ui-corner-all ui-button');
        
        return $form;
    }
    
    function build_opensips_list(){
      // array(display name, width, db_field_parent_table,feidname, db_field_child_table,function name);
      $grid_field_arr  = json_encode(array(
				    array("Username","130","username","","",""),
                                    array("Password","130","password","","",""),
                                    array("Domain","130","domain","","",""), 
                                 array("Action", "120", "", "", "", array("EDIT" => array("url" => "/opensips/opensips_edit/", "mode" => "popup"),
                    "DELETE" => array("url" => "/opensips/opensips_remove/", "mode" => "single")))
                ));
      return $grid_field_arr;
    }
      function build_opensipsdispatcher_list(){
      // array(display name, width, db_field_parent_table,feidname, db_field_child_table,function name);
      $grid_field_arr  = json_encode(array(
				    array("Set id","130","setid","","",""), 
                                    array("Destination","130","destination","","",""),
                                    array("Flags","130","flags","","",""),
                                    array("Weight","130","weight","","",""),
                                    array("Attrs","130","attrs","","",""), 
                                    array("Description","130","description","","",""),
                                  array("Action", "120", "", "", "", array("EDIT" => array("url" => "/opensips/dispatcher_edit/", "mode" => "popup"),
                    "DELETE" => array("url" => "/opensips/dispatcher_remove/", "mode" => "single")))
                ));
      return $grid_field_arr;
    }
    
    function build_grid_buttons(){
	$buttons_json = json_encode(array(array("Add Devices","add","button_action","/opensips/opensips_add/",'popup'),
				    array("Refresh","reload","/accounts/clearsearchfilter/")));
	return $buttons_json;
    }
    
    function build_grid_dispatcherbuttons(){
	$buttons_json = json_encode(array(array("Add Dispatcher","add","button_action","/opensips/dispatcher_add/","popup"),
				    array("Refresh","reload","/accounts/clearsearchfilter/")));
	return $buttons_json;
    }
      function get_opensips_form_fields_for_customer($accountid){
// 	echo $this->CI->session->userdata("logintype");
          if($this->CI->session->userdata("logintype")== '0' || $this->CI->session->userdata("logintype") == '1'){
            $link = base_url().'opensips/customer_opensips_save/true';
        }else{
            $link = base_url().'opensips/opensips_save/true';
        }
         $form['forms'] = array($link,array("id"=>"opensips_form","name"=>"opensips_form"));
         $form['Opensips Device'] = array(
            array('', 'HIDDEN', array('name'=>'id'),'', '', '', ''), 
            array('', 'HIDDEN', array('name' => 'accountcode','value'=>$this->CI->common->get_field_name('number','accounts',array('id'=>$accountid))), '', '', '', ''),
            array('Username', 'INPUT', array('name' => 'username','size' => '20', 'maxlength' => '15', 'class' => "text field medium"), 'trim|required', 'tOOL TIP', 'Please Enter account number'),
            array('password', 'PASSWORD', array('name' => 'password','size' => '20', 'maxlength' => '15', 'class' => "text field medium"), 'trim|required', 'tOOL TIP', 'Please Enter account number'),
            array('Rate Group', 'pricelist_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'name', 'pricelists', 'build_dropdown', 'reseller_id', '0'),
            
            array('Domain', 'INPUT', array('name' => 'domain','size' => '20', 'maxlength' => '15', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter account number')
            
         );
        $form['button_save'] = array('name' => 'action', 'content' =>'Save' , 'value' => 'save', 'type' => 'button','id'=>'submit', 'class' => 'ui-state-default float-right ui-corner-all ui-button');
        $form['button_cancel'] = array('name' => 'action', 'content' => 'Cancel', 'value' => 'cancel', 'type' => 'button', 'class' => 'ui-state-default float-right ui-corner-all ui-button', 'onclick' => 'return redirect_page(\'NULL\')');
        return $form;
    }
    function opensips_customer_build_grid_buttons($accountid) {
        $buttons_json = json_encode(array(array("Add Devices", "add", "button_action", "/opensips/customer_opensips_add/$accountid/","popup"),
            array("Refresh", "reload", "/accounts/clearsearchfilter/")));
        return $buttons_json;
    }
       function opensips_customer_build_opensips_list($accountid){
      // array(display name, width, db_field_parent_table,feidname, db_field_child_table,function name);
      $grid_field_arr  = json_encode(array(
				    array("Username","130","username","","",""),
                                    array("Password","130","password","","",""),
                                    array("Domain","130","domain","","",""), 
                                 array("Action", "120", "", "", "", array("EDIT" => array("url" => '/accounts/customer_opensips_action/edit/'.$accountid.'/', "mode" => "popup"),
                    "DELETE" => array("url" => '/accounts/customer_opensips_action/delete/'.$accountid."/", "mode" => "popup")))
                ));
      return $grid_field_arr;
    }
}
?>
