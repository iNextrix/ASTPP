<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Email_form {
    function __construct($library_name = '') {
        $this->CI = & get_instance();
    }

  function get_form_fields_email() {
         $form['forms'] = array(base_url() . 'email/email_re_send/', array('id' => 'commission_form', 'method' => 'POST', 'name' => 'commission_form'));
        $form['Resend Email'] = array(
            array('', 'HIDDEN', array('name' => 'id'), '', '', '', ''),
            array('To', 'INPUT', array('name' => 'to', 'size' => '20', 'maxlength' => '80', 'class' => "text field medium"),'trim|required|xss_clean', 'tOOL TIP', ''),
            array('From', 'INPUT', array('name' => 'from', 'size' => '20', 'maxlength' => '80', 'class' => "text field medium"),'trim|required|xss_clean', 'tOOL TIP', ''),
            array('Subject', 'INPUT', array('name' => 'subject', 'size' => '20', 'maxlength' => '80', 'class' => "text field medium"),'trim|required|xss_clean', 'tOOL TIP', ''),
            array('Body', 'TEXTAREA', array('name' => 'body', 'size' => '20', 'maxlength' => '80', 'class' => "text field medium"),'trim|required|xss_clean', 'tOOL TIP', ''),
            
	    array('Status', 'status', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', '', '', '', 'email_search_status', '', ''),
	           
);
        $form['button_cancel'] = array('name' => 'action', 'content' => 'Cancel', 'value' => 'cancel', 'type' => 'button', 'class' => 'btn btn-line-sky margin-x-10', 'onclick' => 'return redirect_page(\'NULL\')');
        $form['button_save'] = array('name' => 'action', 'content' => 'Save', 'value' => 'save', 'id' => 'submit', 'type' => 'submit', 'class' => 'btn btn-line-parrot');

        return $form;
    }

    function get_form_fields_email_edit() {
	 $readable='disabled';
         $form['forms'] = array(base_url() . 'email/email_resend/', array('id' => 'commission_form', 'method' => 'POST', 'name' => 'commission_form'));
	 $form['Resent Email'] = array(
           array('', 'HIDDEN', array('name' => 'id'), '', '', '', ''),
           array('', 'HIDDEN', array('name' => 'status'), '', '', '', ''),
           array('From', 'TEXTAREA', array('name' => 'from', 'size' => '20','cols'=>50,'rows'=>1, 'readonly' => true, 'maxlength' => '80', 'class' => "text field medium"),'trim|required|xss_clean', 'tOOL TIP', ''),
           array('To', 'TEXTAREA', array('name' => 'to', 'size' => '20','cols'=>50,'rows'=>1, 'readonly' => true, 'class' => "text field medium"),'trim|required|xss_clean', 'tOOL TIP', ''),
	   array('Subject', 'TEXTAREA', array('name' => 'subject', 'size' => '20','cols'=>50,'rows'=>1, 'maxlength' => '', 'class' => "text field medium"),'trim|required|xss_clean', 'tOOL TIP', ''),
           array('Body', 'TEXTAREA', array('name' => 'body', 'size' => '20','cols'=>50,'rows'=>15, 'maxlength' => '', 'class' => "text field medium"),'trim|required|xss_clean', 'tOOL TIP', ''),
	           
	 );
	 $form['button_cancel'] = array('name' => 'action', 'content' => 'Cancel', 'value' => 'cancel', 'type' => 'button', 'class' => 'btn btn-line-sky margin-x-10', 'onclick' => 'return redirect_page(\'NULL\')');
         $form['button_save'] = array('name' => 'action', 'content' => 'Sent', 'value' => 'save', 'id' => 'button', 'type' => 'submit', 'class' => 'btn btn-line-parrot');
         return $form;
    }
    
    function get_form_fields_email_view() {
	    $readable='disabled';
         $form['forms'] = array(base_url() . 'email/email_history_list/', array('id' => 'commission_form', 'method' => 'POST', 'name' => 'commission_form'));
        $form['View Email'] = array(
            array('', 'HIDDEN', array('name' => 'id'), '', '', '', ''),
            array('', 'HIDDEN', array('name' => 'status'), '', '', '', ''),
           array('From', 'TEXTAREA', array('name' => 'from', 'size' => '20','cols'=>50,'rows'=>1, 'readonly' => true, 'maxlength' => '80', 'class' => "text field medium"),'trim|required|xss_clean', 'tOOL TIP', ''),
           array('To', 'TEXTAREA', array('name' => 'to', 'size' => '20','cols'=>50,'rows'=>1, 'readonly' => true, 'class' => "text field medium"),'trim|required|xss_clean', 'tOOL TIP', ''),
	   array('Subject', 'TEXTAREA', array('name' => 'subject', 'size' => '20','cols'=>50,'rows'=>1, 'readonly' => true, 'maxlength' => '80', 'class' => "text field medium"),'trim|required|xss_clean', 'tOOL TIP', ''),
            array('Body', 'TEXTAREA', array('name' => 'body', 'size' => '20','cols'=>50,'rows'=>15, 'readonly' => true, 'maxlength' => '80', 'class' => "text field medium"),'trim|required|xss_clean', 'tOOL TIP', ''),
            
	    array('Status', array('name' => 'status', 'disabled' => $readable, 'maxlength' => '20', 'class' => "text field medium"), 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', '', '', '', 'email_search_status', '', ''),
	           
);
   //    $form['button_cancel'] = array('name' => 'action', 'content' => 'Cancel', 'value' => 'cancel', 'type' => 'button', 'class' => 'btn btn-line-sky margin-x-10', 'onclick' => 'return redirect_page(\'NULL\')');
        $form['button_save'] = array('name' => 'action', 'content' => 'Cancel', 'value' => 'cancel', 'type' => 'button', 'class' => 'btn btn-line-sky margin-x-10', 'onclick' => 'return redirect_page(\'NULL\')');
        return $form;
    }
    function get_form_fields_email_view_cus() {
	    $readable='disabled';
         $form['forms'] = array(base_url() . 'email/email_history_list_cus/', array('id' => 'commission_form', 'method' => 'POST', 'name' => 'commission_form'));
        $form['View Email'] = array(
            array('', 'HIDDEN', array('name' => 'id'), '', '', '', ''),
            array('', 'HIDDEN', array('name' => 'status'), '', '', '', ''),
           array('From', 'TEXTAREA', array('name' => 'from', 'size' => '20','cols'=>50,'rows'=>1, 'readonly' => true, 'maxlength' => '80', 'class' => "text field medium"),'trim|required|xss_clean', 'tOOL TIP', ''),
           array('To', 'TEXTAREA', array('name' => 'to', 'size' => '20','cols'=>50,'rows'=>1, 'readonly' => true, 'class' => "text field medium"),'trim|required|xss_clean', 'tOOL TIP', ''),
	   array('Subject', 'TEXTAREA', array('name' => 'subject', 'size' => '20','cols'=>50,'rows'=>1, 'readonly' => true, 'maxlength' => '80', 'class' => "text field medium"),'trim|required|xss_clean', 'tOOL TIP', ''),
            array('Body', 'TEXTAREA', array('name' => 'body', 'size' => '20','cols'=>50,'rows'=>15, 'readonly' => true, 'maxlength' => '80', 'class' => "text field medium"),'trim|required|xss_clean', 'tOOL TIP', ''),
            
	    array('Status', array('name' => 'status', 'disabled' => $readable, 'maxlength' => '20', 'class' => "text field medium"), 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', '', '', '', 'email_search_status', '', ''),
	           
);
        //$form['button_cancel'] = array('name' => 'action', 'content' => 'Cancel', 'value' => 'cancel', 'type' => 'button', 'class' => 'btn btn-line-sky margin-x-10', 'onclick' => 'return redirect_page(\'NULL\')');
        $form['button_save'] = array('name' => 'action', 'content' => 'Cancel', 'value' => 'cancel', 'type' => 'button', 'class' => 'btn btn-line-sky margin-x-10', 'onclick' => 'return redirect_page(\'NULL\')');
        return $form;
    }
    function get_form_fields_email_view_cus_edit() {
	    $readable='disabled';
         $form['forms'] = array(base_url() . 'email/email_resend_customer/', array('id' => 'commission_form', 'method' => 'POST', 'name' => 'commission_form'));
        $form['Resent Email'] = array(
            array('', 'HIDDEN', array('name' => 'id'), '', '', '', ''),
            array('', 'HIDDEN', array('name' => 'status'), '', '', '', ''),
           array('From', 'TEXTAREA', array('name' => 'from', 'size' => '20','cols'=>50,'rows'=>1, 'readonly' => true, 'maxlength' => '80', 'class' => "text field medium"),'trim|required|xss_clean', 'tOOL TIP', ''),
           array('To', 'TEXTAREA', array('name' => 'to', 'size' => '20','cols'=>50,'rows'=>1, 'readonly' => true, 'class' => "text field medium"),'trim|required|xss_clean', 'tOOL TIP', ''),
	   array('Subject', 'TEXTAREA', array('name' => 'subject', 'size' => '20','cols'=>50,'rows'=>1, 'maxlength' => '', 'class' => "text field medium"),'trim|required|xss_clean', 'tOOL TIP', ''),
            array('Body', 'TEXTAREA', array('name' => 'body', 'size' => '20','cols'=>50,'rows'=>15, 'maxlength' => '', 'class' => "text field medium"),'trim|required|xss_clean', 'tOOL TIP', ''),
            
	//    array('Status', array('name' => 'status', 'disabled' => $readable, 'maxlength' => '20', 'class' => "text field medium"), 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', '', '', '', 'email_search_status', '', ''),
	           
);
        $form['button_cancel'] = array('name' => 'action', 'content' => 'Cancel', 'value' => 'cancel', 'type' => 'button', 'class' => 'btn btn-line-sky margin-x-10', 'onclick' => 'return redirect_page(\'NULL\')');
         $form['button_save'] = array('name' => 'action', 'content' => 'Sent', 'value' => 'save', 'id' => 'button', 'type' => 'submit', 'class' => 'btn btn-line-parrot');
        return $form;
    }

    function build_list_for_email() {
        // array(display name, width, db_field_parent_table,feidname, db_field_child_table,function name);
        $grid_field_arr = json_encode(array(
            array("Date", "110", "date", "", "", ""),
            array("From", "130", "from", "", "", ""),
            array("To", "175", "to", "", "", ""),
            array("Body", "550", "body", "", "", ""),
            array("Attachement", "100", "attachment", "attachment", "attachment", "attachment_icons"),
            array("Status", "70", "status", "status", "status", "email_status"),
            array("Action", "120", "", "", "", array("RESEND" => array("url" => "email/email_resend_edit/", "mode" => "popup"),
		    "VIEW" => array("url" => "email/email_view/", "mode" => "popup"),
                    "DELETE" => array("url" => "/email/email_delete/", "mode" => "single")))
                ));
        return $grid_field_arr;
    }

    function build_list_for_email_customer($accountid) {
        // array(display name, width, db_field_parent_table,feidname, db_field_child_table,function name);
               $grid_field_arr = json_encode(array(
            array("Date", "140", "date", "", "", ""),
            array("From", "160", "from", "", "", ""),
            array("To", "200", "to", "", "", ""),
            array("Body", "420", "body", "", "", ""),
	    array("Attachement", "100", "attachment", "attachment", "attachment", "attachment_icons"),
            array("Status", "100", "status", "status", "status", "email_status"),
            array("Action", "120", "", "", "", array("RESEND" => array("url" => "email/email_resend_edit_customer/", "mode" => "popup"),
		    "VIEW" => array("url" => "email/email_view_customer/", "mode" => "popup"),
                    "DELETE" => array("url" => "/email/email_delete_cus/".$accountid."/", "mode" => "single")))
                ));
        return $grid_field_arr;
    }

    //function build_grid_buttons_email() {
     //   $buttons_json = json_encode(array(array("Create","btn btn-line-warning btn","fa fa-plus-circle fa-lg", "button_action", "/email/email_add/", "popup"),
       //     array("Delete", "btn btn-line-danger","fa fa-times-circle fa-lg", "button_action", "/email/email_delete_multiple/"),
         //   array("import", "btn btn-line-blue","fa fa-upload fa-lg", "button_action", "/email/origination_import/", 'single'),
         //   array("Export","btn btn-xing" ," fa fa-download fa-lg", "button_action", "/email/origination_export_cdr_xls/", 'single')
            
         //   ));
       // return $buttons_json;
   // }

    function build_list_for_email_client_area(){
        $logintype = $this->CI->session->userdata('logintype');
	if ($logintype == 1 || $logintype == 5) {
            $account_data = $this->CI->session->userdata("accountinfo");
            $loginid = $account_data['id'];

        }else{
            $loginid = "0";
        }
         $form['forms'] = array(base_url() . 'email/email_client_area/', array('id' => 'commission_form', 'method' => 'POST', 'name' => 'commission_form'));
        $form['Filter'] = array(
          array('Rate Group', 'pricelist_id', 'SELECT', '',array("name"=>"pricelist_id","rules"=>"required"), 'tOOL TIP', 'Please Enter account number', 'id', 'name', 'pricelists', 'build_dropdown', 'where_arr', array("status" => "0","reseller_id" => $loginid)),
          array('Account Type',array('name' => 'posttoexternal','class' => 'posttoexternal', 'id' => 'posttoexternal'), 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', '', '', '', 'set_account_type'),
          array('Status', 'status', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', '', '', '', 'set_status'),
	  array('Entity Type', 'type', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', '', '', '', 'set_entity_type_customer'),
	);
	$form['Email Template'] = array(
          // array('Email Template', 'temp', 'SELECT', '',array("name"=>"temp","rules"=>"required"), 'tOOL TIP', 'Please Enter account number', 'id', 'name', 'default_templates', 'build_dropdown', 'where_arr', ''),
	array('Email Template', 'temp', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', '', '', '', 'set_search_temp'),
	//   array('From', 'INPUT', array('name' => 'temp', 'size' => '20','value'=>'', 'maxlength' => '80', 'class' => "text field medium"),'trim|required|xss_clean', 'tOOL TIP', ''),
          
	);
$form['button_cancel'] = array('name' => 'action', 'content' => 'Reset', 'value' => 'cancel', 'type' => 'reset', 'class' => 'btn btn-line-sky margin-x-10', 'onclick' => 'return redirect_page(\'NULL\')');
	$form['button_save'] = array('name' => 'action', 'content' => 'Search', 'value' => 'save', 'id' => 'submit', 'type' => 'submit', 'class' => 'btn btn-line-parrot');

	
       
        return $form;
    } 
    function get_form_fields_email_view_client($add_arr) {
	if($add_arr['type'] == ''){
	   $email_add = array('To','email', 'SELECT', '', '', 'tOOL TIP', 'Please Enter receipent Email', 'email', 'email', 'accounts', 'build_dropdown', 'where_arr',array('status'=>$add_arr['status'],'posttoexternal'=>$add_arr['posttoexternal'],'pricelist_id'=>$add_arr['pricelist_id']), 'multi');
	}
	else{
	 $email_add = array('To','email', 'SELECT', '', '', 'tOOL TIP', 'Please Enter receipent Email', 'email', 'email', 'accounts', 'build_dropdown', 'where_arr',array('status'=>$add_arr['status'],'posttoexternal'=>$add_arr['posttoexternal'],'pricelist_id'=>$add_arr['pricelist_id'],'type'=>$add_arr['type']), 'multi');
	}

         $form['forms'] = array(base_url() . 'email/email_send_multipal/', array('id' => 'commission_form', 'method' => 'POST', 'name' => 'commission_form'));
        $form['Compose Email'] = array(
//            array('', 'HIDDEN', array('name' => 'email','value'=>$to_send_mail), '', '', '', ''),
            array('', 'HIDDEN', array('name' => 'accountid'), '', '', '', ''),
            array('', 'HIDDEN', array('name' => 'temp'), '', '', '', ''),
//            array('To','', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'email', 'accounts', 'build_dropdown',  'where_arr',array('status'=>$add_arr['status'],'posttoexternal'=>$add_arr['posttoexternal'],'pricelist_id'=>$add_arr['pricelist_id'],'type'=>$add_arr['type']), 'multi'),
	     array('From', 'INPUT', array('name' => 'from', 'size' => '20','readonly'=>true,'value'=>'', 'maxlength' => '80', 'class' => "text field medium"),'trim|required|xss_clean', 'tOOL TIP', ''),
           $email_add,
            array('Subject', 'INPUT', array('name' => 'subject', 'size' => '20', 'maxlength' => '10000', 'class' => "text field medium"), 'trim|required', 'tOOL TIP', ''),
            array('Body', 'TEXTAREA', array('name' => 'template', 'id' => 'template', 'size' => '20', 'maxlength' => '1000', 'class' => "textarea medium"), 'trim|required', 'tOOL TIP', ''),
         //   array('Global Tag', 'TEXTAREA', array('name' => 'global_key', 'id' => '', 'readonly' => true, 'size' => '20', 'cols' => '73','rows' => '15', 'class' => "textarea medium"), 'trim|required', 'tOOL TIP', ''),

);
	$form['button_cancel'] = array('name' => 'action', 'content' => 'Cancel', 'value' => 'cancel', 'type' => 'button', 'class' => 'btn btn-line-sky margin-x-10',  'onclick' => 'return redirect_page(\'/email/email_mass/\')');
        $form['button_save'] = array('name' => 'action', 'content' => 'Send', 'value' => 'save', 'id' => 'submit', 'type' => 'submit', 'class' => 'btn btn-line-parrot');
        return $form;
    }
    function build_grid_buttons_email(){
	$buttons_json = json_encode(array(
				    ));
	return $buttons_json;
    }
    function get_email_history_search_form() {
        $form['forms'] = array("", array('id' => "email_search"));
        $form['Search'] = array(
	 //   array('Date', 'INPUT', array('name' => 'date[0]','id'=>'date','size' => '20', 'class' => "text field"), '', 'tOOL TIP', '', 'date[date-date]'),
	    array('From Date', 'INPUT', array('name' => 'date[]', 'id' => 'customer_cdr_from_date', 'size' => '20',
 'class' => "text field "), '', 'tOOL TIP', '', 'date[date-date]'),
            array('To Date', 'INPUT', array('name' => 'date[]', 'id' => 'customer_cdr_to_date', 'size' => '20', 'class' => "text field "), '', 'tOOL TIP', '', 'date[date-date]'),
            array('From', 'INPUT', array('name' => 'from[from]', '','id'=>'from', 'size' => '20', 'class' => "text field "), '', 'tOOL TIP', '1', 'from[from-string]', '', '', '', 'search_string_type', ''),

	    array('To', 'INPUT', array('name' => 'to[to]', '','id'=>'to', 'size' => '20', 'class' => "text field "), '', 'tOOL TIP', '1', 'to[to-string]', '', '', '', 'search_string_type', ''),			
 	    array('Body', 'INPUT', array('name' => 'body[body]', '','id'=>'body', 'size' => '20', 'class' => "text field "), '', 'tOOL TIP', '1', 'body[body-string]', '', '', '', 'search_string_type', ''),  
	array('', 'HIDDEN', 'ajax_search', '1', '', '', ''),         
            array('', 'HIDDEN', 'advance_search', '1', '', '', '')
//             array('Body', 'INPUT', array('name' => 'template[template]', '', 'size' => '20', 'class' => "text field "), '', 'tOOL TIP', '1', 'template[template-string]', '', '', '', 'search_string_type', ''),
        );
        $form['button_search'] = array('name' => 'action', 'id' => "email_search_btn", 'content' => 'Search', 'value' => 'save', 'type' => 'button', 'class' => 'btn btn-line-parrot pull-right');
        $form['button_reset'] = array('name' => 'action', 'id' => "id_reset", 'content' => 'Clear', 'value' => 'cancel', 'type' => 'reset', 'class' => 'btn btn-line-sky pull-right margin-x-10');
        return $form;
    }

}

?>
