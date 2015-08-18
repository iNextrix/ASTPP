<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
class Opensips_form{
    function __construct() {
        $this->CI = & get_instance();
    }
    
    function get_opensips_form_fields($id=false) {
        $accountinfo = $this->CI->session->userdata("accountinfo");
     
      $uname_user = $this->CI->common->find_uniq_rendno('10', '', '');
        $password = $this->CI->common->generate_password();
   $val = $id > 0 ? 'subscriber.username.' . $id : 'subscriber.username';
           
    // echo '<pre>'; print_r($val); exit;
            $loginid=$this->CI->session->userdata('logintype')==2 ? 0:$accountinfo['id'];
        $form['forms'] = array(base_url() . 'opensips/opensips_save/',array("id"=>"opensips_form","name"=>"opensips_form"));
        $form['Opensips Device'] = array(
            array('', 'HIDDEN', array('name'=>'id'),'', '', '', ''), 
       array('Username', 'INPUT', array('name' => 'username', 'size' => '30', 'maxlength' => '30','value'=>$uname_user,'id'=>'username', 'class' => "text field medium"), 'trim|required|min_length[2]|max_length[25]|xss_clean', 'tOOL TIP', 'Please Enter account number','<i style="cursor:pointer; font-size: 17px; padding-left:10px; padding-top:6px;" title="Reset Password" class="change_number fa fa-refresh"></i>'),
            array('Password', 'INPUT', array('name' => 'password', 'size' => '30', 'maxlength' => '50','value'=>$password ,'id'=>'password','class' => "text field medium"), 'trim|required|min_length[5]|max_length[50]|xss_clean', 'tOOL TIP', 'Please Enter Password','<i style="cursor:pointer; font-size: 17px; padding-left:10px; padding-top:6px;" title="Reset Password" class="change_pass fa fa-refresh"></i>'),
             array('Account', 'accountcode', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'number', 'number', 'accounts', 'build_dropdown', 'where_arr', array("reseller_id" => $loginid,"type"=>"GLOBAL", "deleted" => "0")),
            array('Rate Group', 'pricelist_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'name', 'pricelists', 'build_dropdown', 'where_arr',array("reseller_id" => $loginid,'status'=>0)),
            array('Domain', 'INPUT', array('name' => 'domain','size' => '20', 'maxlength' => '15', 'class' => "text field medium"), '', 'tOOL TIP', '')
            
         );
        $form['button_save'] = array('name' => 'action', 'content' =>'Save' , 'value' => 'save', 'type' => 'button','id'=>'submit', 'class' => 'btn btn-line-parrot');
        $form['button_cancel'] = array('name' => 'action', 'content' => 'Cancel', 'value' => 'cancel', 'type' => 'button', 'class' => 'btn btn-line-sky margin-x-10', 'onclick' => 'return redirect_page(\'NULL\')');
      //  echo '<pre>'; print_r($form); exit;
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
        $form['button_save'] = array('name' => 'action', 'content' =>'Save' , 'value' => 'save', 'type' => 'button','id'=>'submit', 'class' => 'btn btn-line-parrot');
        $form['button_cancel'] = array('name' => 'action', 'content' => 'Cancel', 'value' => 'cancel', 'type' => 'button', 'class' => 'btn btn-line-sky margin-x-10', 'onclick' => 'return redirect_page(\'/opensips/dispatcher_list/\')');
        return $form;
    }

function get_search_dispatcher_form()
    {
        $form['forms'] = array("",array('id'=>"dispatcher_search"));
        $form['Search'] = array(
             array('Description', 'INPUT', array('name' => 'description[description]','','size' => '20', 'class' => "text field "), '', 'tOOL TIP', '1', 'description[description-string]', '', '','', 'search_string_type', ''), 
            array('', 'HIDDEN', 'ajax_search','1', '', '', ''),    
            array('', 'HIDDEN', 'advance_search','1', '', '', ''),               
            );
        
        $form['button_search'] = array('name' => 'action', 'id'=>"opensipsdispatcher_search_btn",'content' => 'Search', 'value' => 'save', 'type' => 'button', 'class' => 'btn btn-line-parrot pull-right');
        $form['button_reset'] = array('name' => 'action','id'=>"id_reset", 'content' => 'Clear', 'value' => 'cancel', 'type' => 'reset', 'class' => 'btn btn-line-sky pull-right margin-x-10');
        
        return $form;
    }




     function get_search_opensips_form()
    {
        $form['forms'] = array("",array('id'=>"opensips_list_search"));
        $form['Search'] = array(    
             array('Username', 'INPUT', array('name' => 'username[username]','','size' => '20', 'class' => "text field "), '', 'tOOL TIP', '1', 'username[username-string]', '', '','', 'search_string_type', ''),
            array('', 'HIDDEN', 'ajax_search','1', '', '', ''),    
            array('', 'HIDDEN', 'advance_search','1', '', '', ''),            
            );
        
        $form['button_search'] = array('name' => 'action', 'id'=>"opensipsdevice_search_btn",'content' => 'Search', 'value' => 'save', 'type' => 'button', 'class' => 'btn btn-line-parrot pull-right');
        $form['button_reset'] = array('name' => 'action','id'=>"id_reset", 'content' => 'Clear', 'value' => 'cancel', 'type' => 'reset', 'class' => 'btn btn-line-sky pull-right margin-x-10');
        
        return $form;
    }
       
    
    function build_opensips_list(){
      // array(display name, width, db_field_parent_table,feidname, db_field_child_table,function name);
      $grid_field_arr  = json_encode(array(
				    array("Username","340","username","","",""),
                                    array("Password","340","password","","",""),
                                    array("Domain","410","domain","","",""), 
                                 array("Action", "200", "", "", "", array("EDIT" => array("url" => "/opensips/opensips_edit/", "mode" => "popup"),
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
	$buttons_json = json_encode(array(array("Create","btn btn-line-warning btn","fa fa-plus-circle fa-lg","button_action","/opensips/opensips_add/",'popup'),
//				    array("Refresh","reload","/accounts/clearsearchfilter/")
));
	return $buttons_json;
    }
    
    function build_grid_dispatcherbuttons(){
	$buttons_json = json_encode(array(array("Create","btn btn-line-warning btn","fa fa-plus-circle fa-lg","button_action","/opensips/dispatcher_add/","popup"),
				  //  array("Refresh","reload","/accounts/clearsearchfilter/")
));
	return $buttons_json;
    }
      function get_opensips_form_fields_for_customer($accountid , $id=false){
	 $val = $id > 0 ? 'subscriber.username.' . $id : 'subscriber.username';
	 $uname_user = $this->CI->common->find_uniq_rendno('10', '', '');
            $password = $this->CI->common->generate_password();
	 if ($this->CI->session->userdata("logintype") == '0'  || $this->CI->session->userdata("logintype") == '3') {    
         $link = base_url().'opensips/user_opensips_save/true';       
         $form['forms'] = array($link,array("id"=>"opensips_form","name"=>"opensips_form"));
         $form['Opensips Device'] = array(
            array('', 'HIDDEN', array('name'=>'id'),'', '', '', ''), 
            array('', 'HIDDEN', array('name' => 'accountcode','value'=>$this->CI->common->get_field_name('number','accounts',array('id'=>$accountid))), '', '', '', ''),
            
		 array('Username', 'INPUT', array('name' => 'fs_username', 'size' => '20', 'maxlength' => '25','id'=>'username','value'=>$uname_user, 'class' => "text field medium"), 'trim|required|min_length[2]|max_length[25]|xss_clean', 'tOOL TIP', 'Please Enter account number','<i style="cursor:pointer; font-size: 17px; padding-left:10px; padding-top:6px;" title="Reset Password" class="change_number fa fa-refresh"></i>'),
                array('Password', 'PASSWORD', array('name' => 'fs_password', 'size' => '20', 'maxlength' => '25','id'=>'password1','value'=>$password, 'class' => "text field medium"), 'trim|required|min_length[5]|max_length[50]|xss_clean', 'tOOL TIP', 'Please Enter Password','<i style="cursor:pointer; font-size: 17px; padding-left:10px; padding-top:6px;" title="Reset Password" class="change_pass fa fa-refresh"></i>'),
            array('Rate Group', 'pricelist_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'name', 'pricelists', 'build_dropdown', 'where_arr',array("reseller_id" => $val,'status'=>0)),
            array('Domain', 'INPUT', array('name' => 'domain','size' => '20', 'maxlength' => '15', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter account number')   
         );

  } else {
            $link = base_url() . 'opensips/opensips_save/true';
            $form['forms'] = array($link,array("id"=>"opensips_form","name"=>"opensips_form"));
            $form['Opensips Device'] = array(
            array('', 'HIDDEN', array('name'=>'id'),'', '', '', ''), 
            array('', 'HIDDEN', array('name' => 'accountcode','value'=>$this->CI->common->get_field_name('number','accounts',array('id'=>$accountid))), '', '', '', ''),
              array('Username', 'INPUT', array('name' => 'fs_username', 'size' => '20', 'maxlength' => '25','id'=>'username','value'=>$uname_user,  'class' => "text field medium"), 'trim|required|min_length[2]|max_length[25]|xss_clean', 'tOOL TIP', 'Please Enter account number','<i style="cursor:pointer; font-size: 17px; padding-left:10px; padding-top:6px;" title="Reset Password" class="change_number fa fa-refresh"></i>'),
                array('Password', 'INPUT', array('name' => 'fs_password', 'size' => '20', 'maxlength' => '25','value'=>$password, 'class' => "text field medium",'id'=>'password1'), 'trim|required|min_length[5]|max_length[25]|xss_clean', 'tOOL TIP', 'Please Enter Password','<i style="cursor:pointer; font-size: 17px; padding-left:10px; padding-top:6px;" title="Reset Password" class="change_pass fa fa-refresh"></i>'),
            array('Rate Group', 'pricelist_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'name', 'pricelists', 'build_dropdown', 'reseller_id', '0'),
            
            array('Domain', 'INPUT', array('name' => 'domain','size' => '20', 'maxlength' => '15', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter account number')
            
         );

        }



        $form['button_save'] = array('name' => 'action', 'content' =>'Save' , 'value' => 'save', 'type' => 'submit','id'=>'submit', 'class' => 'btn btn-line-parrot');
        $form['button_cancel'] = array('name' => 'action', 'content' => 'Cancel', 'value' => 'cancel', 'type' => 'button', 'class' => 'btn btn-line-sky margin-x-10', 'onclick' => 'return redirect_page(\'NULL\')');
        return $form;
    }

function user_opensips(){
      // array(display name, width, db_field_parent_table,feidname, db_field_child_table,function name);
      $grid_field_arr  = json_encode(array(		
		array("Username","130","username","","",""),
                array("Password","130","password","","",""),
                array("Domain","130","domain","","",""), 
                array("Action", "120", "", "", "", 
		   array("EDIT" => array("url" => 'user/user_opensips_action/edit/', "mode" => "popup"),
                         "DELETE" => array("url" => 'user/user_opensips_action/delete/', "mode" => "popup")
                ))));           
      return $grid_field_arr;
    }
	 function opensips_customer_build_grid_buttons($accountid) {
		$buttons_json = json_encode(array(array("Add Devices", "btn btn-line-warning btn" , "fa fa-plus-circle fa-lg", "button_action", "/opensips/customer_opensips_add/$accountid/","popup"),
		    //array("Refresh", "reload", "/accounts/clearsearchfilter/")
	));
		return $buttons_json;
	    }
function opensips_customer_build_opensips_list($accountid){
//echo $accountid;
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

 function build_grid_buttons_for_user() {
        $buttons_json = json_encode(array(array("Create","btn btn-line-warning btn","fa fa-plus-circle fa-lg", "button_action", "/user/user_opensips_action/add/", "popup"),
			array("Delete",  "btn btn-line-danger","fa fa-times-circle fa-lg", "button_action", "/opensips/user_opensips_delete_multiple/"),
                       ));
        return $buttons_json;
    }

}
?>
