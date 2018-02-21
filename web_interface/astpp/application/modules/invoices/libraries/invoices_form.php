<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
class invoices_form{
     function __construct($library_name = '') {
        $this->CI = & get_instance();
    }
     function get_invoiceconf_form_fields()
    {
	$form['forms'] = array(base_url() . 'invoices/invoice_conf/',array('id'=>'invoice_conf_form','method'=>'POST','name'=>'invoice_conf_form'));
        $form['Invoice Configuration '] = array(
            array('', 'HIDDEN', array('name'=>'id'),'', '', '', ''), 
            array('', 'HIDDEN', array('name'=>'accountid'),'', '', '', ''),
            array('Company name', 'INPUT', array('name' => 'company_name','size' => '20', 'maxlength' => '100', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter account number'),
                array('Address', 'INPUT', array('name' => 'address','size' => '20', 'maxlength' => '300', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter account number'),
                array('City', 'INPUT', array('name' => 'city','size' => '20', 'maxlength' => '20', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter account number'),
                array('Province', 'INPUT', array('name' => 'province','size' => '20', 'maxlength' => '20', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter account number'),
                array('Country', 'INPUT', array('name' => 'country','size' => '20', 'maxlength' => '20', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter account number'),
                array('Zipcode', 'INPUT', array('name' => 'zipcode','size' => '20', 'maxlength' => '10', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter account number'),
                array('Telephone', 'INPUT', array('name' => 'telephone','size' => '20', 'maxlength' => '20', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter account number'),
                array('Fax', 'INPUT', array('name' => 'fax','size' => '20', 'maxlength' => '20', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter account number'),
                array('Email Address', 'INPUT', array('name' => 'emailaddress','size' => '20', 'maxlength' => '100', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter account number'),
                array('Website', 'INPUT', array('name' => 'website','size' => '20', 'maxlength' => '100', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter account number')
                
         );
        $form['button_save'] = array('name' => 'action', 'content' =>'Save' , 'value' => 'save', 'type' => 'submit', 'class' => 'btn btn-line-parrot');
        
        return $form;
        
    }
     
     function build_invoices_list_for_admin(){
      $logintype = $this->CI->session->userdata('logintype');
      $url= ($logintype==0 ||$logintype==3 ) ? "/user/user_invoice_download/":'/invoices/invoice_main_download/';
      $grid_field_arr  = json_encode(array(
         // array("Type","148","type","","",""),
          //array("Number","148","id","","",""),
          array("Number","185","id","id,'',type","invoices","build_concat_string"),
          array("Account","285","accountid","first_name,last_name,number","accounts","build_concat_string"),
	  array("From Date","190","from_date","from_date","","get_from_date"),
	  array("Generated Date","175","invoice_date","invoice_date","","get_invoice_date"),
	  array("Amount","300","id","id","id","get_invoice_total"),
          array("Action","120","","","",array(
		     "DOWNLOAD"=>array("url"=>$url,"mode"=>"single"),
		))
      ));
      return $grid_field_arr;
    } 
    function build_invoices_list_for_customer(){
      $url=($this->CI->session->userdata('logintype')==0 )?"/user/user_invoice_download/":'/invoices/invoice_main_download/';
    // array(display name, width, db_field_parent_table,feidname, db_field_child_table,function name);
      $grid_field_arr  = json_encode(array(
          array("Type","220","type","","",""),
          array("Number","220","id","","",""),
	  array("From Date","210","from_date","from_date","","get_from_date"),
	  array("Generated Date","200","invoice_date","invoice_date","","get_invoice_date"),
	  array("Amount","240","id","id","id","get_invoice_total"),
            array("Action","160","","","",array(
            "DOWNLOAD"=>array("url"=>$url,"mode"=>"single"),
            ))
			));
      return $grid_field_arr;
    }
    function get_invoice_search_form()
    {
	$acc_arr= array('Account', 'accountid', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'IF(`deleted`=1,concat( first_name, " ", last_name, " ", "(", number, ")^" ),concat( first_name, " ", last_name, " ", "(", number, ")" )) as number', 'accounts', 'build_dropdown_deleted', 'where_arr', array("reseller_id" => "0","type"=>"GLOBAL"));
  	$logintype = $this->CI->session->userdata('logintype');
        if ($logintype == 1 || $logintype == 5) {
            $account_data = $this->CI->session->userdata("accountinfo");
            $loginid = $account_data['id'];

        }else{
            $loginid = "0";
        }
        if($logintype==0 || $logintype==3){
	    $acc_arr=null;
        }
        $form['forms'] = array("",array('id'=>"invoice_search"));
        $form['Search'] = array(
            array('Number', 'INPUT', array('name' => 'id[id]','','size' => '20', 'class' => "text field"), '', 'tOOL TIP', '1', 'id[id-string]', '', '','', 'search_string_type', ''),
	    array('From Date', 'INPUT', array('name' => 'from_date[0]','id'=>'date','size' => '20', 'class' => "text field"), '', 'tOOL TIP', '', 'from_date[from_date-date]'),
	    array('Generated Date', 'INPUT', array('name' => 'invoice_date[0]','','size' => '20', 'class' => "text field",'id'=>'invoice_date'), '', 'tOOL TIP', '', 'invoice_date[invoice_date-date]'),
	    $acc_arr,
	    array('', 'HIDDEN', 'ajax_search','1', '', '', ''),    
            array('', 'HIDDEN', 'advance_search','1', '', '', ''));
        
        $form['button_search'] = array('name' => 'action', 'id'=>"invoice_search_btn",'content' => 'Search', 'value' => 'save', 'type' => 'button', 'class' => 'btn btn-line-parrot pull-right');
        $form['button_reset'] = array('name' => 'action','id'=>"id_reset", 'content' => 'Clear', 'value' => 'cancel', 'type' => 'reset', 'class' => 'btn btn-line-sky pull-right margin-x-10');
        
        return $form;
    }
    
    function build_grid_buttons(){
	$buttons_json = json_encode(array(
				    ));
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
