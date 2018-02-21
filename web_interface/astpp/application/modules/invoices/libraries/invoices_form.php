<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
class invoices_form{
    
     function get_invoiceconf_form_fields()
    {
	$form['forms'] = array(base_url() . 'invoices/invoice_conf/',array('id'=>'invoice_conf_form','method'=>'POST','name'=>'invoice_conf_form'));
        $form['Invoice Configuration '] = array(
            array('', 'HIDDEN', array('name'=>'id'),'', '', '', ''), 
            array('', 'HIDDEN', array('name'=>'accountid'),'', '', '', ''),
            array('Company name', 'INPUT', array('name' => 'company_name','size' => '20', 'maxlength' => '100', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter account number'),
                array('Address', 'INPUT', array('name' => 'address','size' => '20', 'maxlength' => '200', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter account number'),
                array('City', 'INPUT', array('name' => 'city','size' => '20', 'maxlength' => '35', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter account number'),
                array('Province', 'INPUT', array('name' => 'province','size' => '20', 'maxlength' => '40', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter account number'),
                array('Country', 'INPUT', array('name' => 'country','size' => '20', 'maxlength' => '40', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter account number'),
                array('Zipcode', 'INPUT', array('name' => 'zipcode','size' => '20', 'maxlength' => '20', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter account number'),
                array('Telephone', 'INPUT', array('name' => 'telephone','size' => '20', 'maxlength' => '25', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter account number'),
                array('Fax', 'INPUT', array('name' => 'fax','size' => '20', 'maxlength' => '25', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter account number'),
                array('Email Address', 'INPUT', array('name' => 'emailaddress','size' => '20', 'maxlength' => '100', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter account number'),
                array('Website', 'INPUT', array('name' => 'website','size' => '20', 'maxlength' => '100', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter account number')
                
         );
        $form['button_save'] = array('name' => 'action', 'content' =>'Save' , 'value' => 'save', 'type' => 'submit', 'class' => 'ui-state-default float-right ui-corner-all ui-button');
        
        return $form;
        
    }
    
    
    
    function build_invoices_list_for_admin(){
      // array(display name, width, db_field_parent_table,feidname, db_field_child_table,function name);
      $grid_field_arr  = json_encode(array(array("<input type='checkbox' name='chkAll' class='checkall'/>","30","","","",""),
          array("Invoice Number","278","id","","",""),
				    array("Account Number","270","accountid","number","accounts","get_field_name"),
				    array("Invoice Date","270","invoice_date","","",""),
				    array("From Date","260","from_date","","",""),
// 				    array("Invoice Total","150","value","","",""),
// 				    array("Paid Status","200","status","status","status","get_paid_status"),
            array("Action","60","","","",array(
            "DOWNLOAD"=>array("url"=>"/invoices/invoice_download/","mode"=>"single"),
            "DELETE"=>array("url"=>"/invoices/delete_invoice/","mode"=>"single")))
			));
      return $grid_field_arr;
    }
        function get_invoice_search_form()
    {
        
        $form['forms'] = array("",array('id'=>"trunk_search"));
        
        $form['Search Account Invoice'] = array(
            array('', 'HIDDEN', 'ajax_search','1', '', '', ''),    
            array('', 'HIDDEN', 'advance_search','1', '', '', ''),    
            array('Account number', 'INPUT', array('name' => 'accountid[accountid]','','size' => '20','maxlength' => '15', 'class' => "text field"), '', 'tOOL TIP', '1', 'accountid[accountid-string]', '', '','', 'search_string_type', ''),
            array('Invoice date', 'INPUT', array('name' => 'date[]','id'=>'customer_cdr_from_date','size' => '20','maxlength' => '15', 'class' => "text field "), '', 'tOOL TIP', '', 'date[date-date]'),
            array('Invoice total', 'INPUT', array('name' => 'value[value]', 'value' => '', 'size' => '20', 'maxlength' => '15', 'class' => "text field "), '', 'Tool tips info', '1', 'value[value-integer]', '', '', '', 'search_int_type', ''),
          );
        
        $form['button_search'] = array('name' => 'action', 'id'=>"trunk_search_btn",'content' => 'Search', 'value' => 'save', 'type' => 'button', 'class' => 'ui-state-default float-right ui-corner-all ui-button');
        $form['button_reset'] = array('name' => 'action','id'=>"id_reset", 'content' => 'Clear Search Filter', 'value' => 'cancel', 'type' => 'reset', 'class' => 'ui-state-default float-right ui-corner-all ui-button');
        
        return $form;
    }
    
    function build_grid_buttons(){
	$buttons_json = json_encode(array(array("DELETE","delete","button_action","/charges/periodiccharges_delete/"),
				    array("Refresh","reload","/accounts/clearsearchfilter/")));
	return $buttons_json;
    }
    
//    function set_grid_action_buttons($invoiceid) {                
//        $ret_url = '';
//        $ret_url .= '<a href="'.base_url().'invoices/view_invoice/'.$invoiceid.'/" class="icon details_image" title="View Invoice">&nbsp;&nbsp;</a>';
//        $ret_url .= '<a href="'.base_url().'invoices/download_invoice/'.$invoiceid.'/" class="icon pdf_image" title="Download Invoice">&nbsp;&nbsp;</a>';
//	$ret_url .= '<a href="'.base_url().'invoices/delete_invoice/'.$invoiceid.'/" class="icon delete_image" title="Delete" onClick="return get_alert_msg();">&nbsp;</a>';
//        return $ret_url;
//    }
    
}
?>
