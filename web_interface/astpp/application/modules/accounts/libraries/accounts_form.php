<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Accounts_form {

    /**
     * adds a field Daynmic using form Library .
     * @param array $field array of data for a field
     * For generate random number using find_uniq_rendno(min no ,max no ,database field name which match the number if ,table name)
     * action arguments['action,name,type'];
     * First key is Field set lagend name 
     * name Name of the field,
     * type html field type and 3rd field is array of attribute
     *  if select then 3rd field ,
     * set rules (form validation rule)
     * tooltip
     * custom error message if exists
     * if select type then add 4 arguments like below
     * function name,id,name,tablename will fetch data
     * BUtton array which shows name, id , and also class attribute   
     */
    function __construct($library_name = '') {
        $this->CI = & get_instance();
    }

       function get_customer_form_fields($entity_type=false,$id=false) {
	$expiry_date = date('Y-m-d H:i:s', strtotime('+10 years'));
        $readable=FALSE;
        $logintype = $this->CI->session->userdata('logintype');
        if ($logintype == 1 || $logintype == 5) {
            $account_data = $this->CI->session->userdata("accountinfo");
            $loginid = $account_data['id'];

        }else{
            $loginid = "0";
        }
          $sip_device=null;
          $opensips_device=null;
         if(!$entity_type){
             $entity_type ='customer';
         }
         if ($id > 0){
	    $readable='disabled';
            $val = 'accounts.email.' . $id;
            
        }else{
            $val ='accounts.email';
            if(common_model::$global_config['system_config']['opensips']== 1){
	      $opensips_device =array('Create Opensips Device', 'opensips_device_flag', 'CHECKBOX', array('name' => 'opensips_device_flag', 'value' => 'on', 'checked' => false), '', 'tOOL TIP', '');
            }
            else{
	      $sip_device =array('Create SIP Device', 'sip_device_flag', 'CHECKBOX', array('name' => 'sip_device_flag', 'value' => 'on', 'checked' => false), '', 'tOOL TIP', '');
            }
         }
         $type= $entity_type == 'customer' ? 0 : 3;
        $uname = $this->CI->common->find_uniq_rendno_customer(common_model::$global_config['system_config']['cardlength'], 'number', 'accounts');
           $uname_user = $this->CI->common->find_uniq_rendno('10', 'number', 'accounts');
	$password = $this->CI->common->generate_password();
        $form['forms'] = array(base_url() . 'accounts/'. $entity_type.'_save/', array("id" => "customer_form", "name" => "customer_form"));
        $form['Client Panel Access'] = array(
            array('', 'HIDDEN', array('name' => 'id'), '', '', '', ''),
            array('', 'HIDDEN', array('name' => 'type', 'value' => $type), '', '', ''),
            array('Account', 'INPUT', array('name' => 'number', 'value' => $uname, 'size' => '20', 'readonly' => true, 'maxlength' => '15', 'class' => "text field medium"), 'required|max_length[15]', 'tOOL TIP', ''),
            array('Password', 'INPUT', array('name' => 'password', 'value'=>$password,'size' => '20', 'maxlength' => '20', 'class' => "text field medium",'id'=>'password'), 'required|min_length[5]|max_length[20]', 'tOOL TIP', '','<i style="cursor:pointer; font-size: 17px; padding-left:10px; padding-top:6px;" title="Reset Password" class="change_pass fa fa-refresh" ></i>'),
            array('Pin', 'INPUT', array('name' => 'pin', 'size' => '20', 'maxlength' => '20', 'class' => "text field medium"), 'max_length[20]', 'tOOL TIP', ''));

        $form['Account Settings'] = array(
            array('Status', 'status', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', '', '', '', 'set_status'),
            array('Max Channels', 'INPUT', array('name' => 'maxchannels', 'size' => '20', 'maxlength' => '4', 'class' => "text field medium"), 'numeric', 'tOOL TIP', ''),
            array('Interval', 'INPUT', array('name' => 'interval', 'size' => '20', 'maxlength' => '6', 'class' => "text field medium"), 'numeric', 'tOOL TIP', ''),
            array('Number Translation', 'INPUT', array('name' => 'dialed_modify', 'size' => '20', 'maxlength' => '200', 'class' => "text field medium"), '', 'tOOL TIP', ''),
             array('First Used', 'INPUT', array('name' => 'first_used', 'size' => '20', 'readonly' => true, 'maxlength' => '200', 'class' => "text field medium",'value'=>'0000-00-00 00:00:00'), '', 'tOOL TIP', ''),
            array('Expiry Date', 'INPUT', array('name' => 'expiry', 'size' => '20', 'maxlength' => '200', 'class' => "text field medium",'value'=>$expiry_date,'id'=>'expiry'), '', 'tOOL TIP', ''),
            array('Valid Days', 'INPUT', array('name' => 'validfordays', 'size' => '20', 'maxlength' => '7', 'class' => "text field medium"), 'trim|numeric|min_length[1]|max_length[4]|xss_clean', 'tOOL TIP', ''),  
            $sip_device,
            $opensips_device
        );

        $form[  ucfirst($entity_type). ' Profile'] = array(
          //  array('Language', 'language_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'languagename', 'language', 'build_dropdown', '', ''),

            array('First Name', 'INPUT', array('name' => 'first_name', 'id' => 'first_name', 'size' => '15', 'maxlength' => '40', 'class' => "text field medium"), 'required|alpha_numeric', 'tOOL TIP', ''),
            array('Last Name', 'INPUT', array('name' => 'last_name', 'size' => '15', 'maxlength' => '40', 'class' => "text field medium"), 'trim|alpha_dash|xss_clean', 'tOOL TIP', ''),
            array('Company', 'INPUT', array('name' => 'company_name', 'maxlength' => '40', 'size' => '15', 'class' => 'text field medium'), 'trim|xss_clean', 'tOOL TIP', ''),
            array('Telephone 1', 'INPUT', array('name' => 'telephone_1', 'size' => '15', 'maxlength' => '20', 'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('Telephone 2', 'INPUT', array('name' => 'telephone_2', 'size' => '15', 'maxlength' => '20', 'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('Email', 'INPUT', array('name' => 'email', 'size' => '50', 'maxlength' => '80', 'class' => "text field medium"), 'required|valid_email|is_unique['.$val.']', 'tOOL TIP', ''),
            array('Address 1', 'INPUT', array('name' => 'address_1', 'size' => '15', 'maxlength' => '80', 'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('Address 2', 'INPUT', array('name' => 'address_2', 'size' => '15', 'maxlength' => '80', 'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('City', 'INPUT', array('name' => 'city', 'size' => '20', 'maxlength' => '20', 'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('Province/State', 'INPUT', array('name' => 'province', 'size' => '15', 'maxlength' => '20', 'class' => "text field medium"),'', 'tOOL TIP', ''),
            array('Zip/Postal Code', 'INPUT', array('name' => 'postal_code', 'size' => '15', 'maxlength' => '12', 'class' => "text field medium"), 'trim|xss_clean', 'tOOL TIP', ''),
            array('Country',array('name'=>'country_id','class'=>'country_id'), 'SELECT', '',array("name"=>"country_id","rules"=>"required"), 'tOOL TIP', 'Please Enter account number', 'id', 'country', 'countrycode', 'build_dropdown', '', ''),
            array('Timezone',array('name'=>'timezone_id','class'=>'timezone_id'), 'SELECT', '', array("name"=>"timezone_id","rules"=>"required"), 'tOOL TIP', 'Please Enter account number', 'id', 'gmtzone', 'timezone', 'build_dropdown', '', '')
        );
        $form['Billing Information'] = array(
            array('Rate Group', 'pricelist_id', 'SELECT', '',array("name"=>"pricelist_id","rules"=>"dropdown"), 'tOOL TIP', 'Please Enter account number', 'id', 'name', 'pricelists', 'build_dropdown', 'where_arr', array("status" => "0","reseller_id" => $loginid)),
            array('Billing Schedule',array('name'=> 'sweep_id','class'=>'sweep_id','id'=>'sweep_id'), 'SELECT', '', '', 'tOOL TIP', '', 'id', 'sweep', 'sweeplist', 'build_dropdown', '', ''),
            array('Billing Day',array("name"=>'invoice_day',"class"=>"invoice_day"), 'SELECT', '', '', 'tOOL TIP', '', '', '', '', 'set_invoice_option'),
            array('Currency',array('name'=>'currency_id','class'=>'currency_id'), 'SELECT', '',array("name"=>"currency_id","rules"=>"required"), 'tOOL TIP', 'Please Enter account number', 'id', 'currencyname,currency', 'currency', 'build_concat_dropdown', '', array()),
            array('Account Type',array('name' => 'posttoexternal', 'disabled' => $readable,'class' => 'posttoexternal', 'id' => 'posttoexternal'), 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', '', '', '', 'set_account_type'),
            array('Credit Limit', 'INPUT', array('name' => 'credit_limit', 'size' => '20', 'maxlength' => '15', 'class' => "text field medium"), 'valid_decimal', 'tOOL TIP', ''),
	      array('Tax','tax_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'taxes_description', 'taxes', 'build_dropdown', 'where_arr',array('status'=>0,"reseller_id" => $loginid), 'multi'),
            );
            

        $form['Low Balance Alert Email'] = array(
            array('Low Balance Alert Level', 'INPUT', array('name' => 'notify_credit_limit', 'size' => '20', 'maxlength' => '11', 'class' => "text field medium"), 'valid_decimal', 'tOOL TIP', ''),
            array('Enable Email Alerts ?', 'notify_flag', 'SELECT', '', '', 'tOOL TIP', '', '', '', '', 'set_allow'),
            array('Email Address', 'INPUT', array('name' => 'notify_email', 'size' => '50', 'maxlength' => '80', 'class' => "text field medium"), 'valid_email', 'tOOL TIP', ''),
        );        
        $form['button_save'] = array('name' => 'action', 'content' => 'Save', 'value' => 'save', 'type' => 'submit', 'class' => 'btn btn-line-parrot');
        $form['button_cancel'] = array('name' => 'action', 'content' => 'Cancel', 'value' => 'cancel', 'type' => 'button', 'class' => 'btn btn-line-sky margin-x-10',  'onclick' => 'return redirect_page(\'/accounts/customer_list/\')');
        return $form;
    }
        function customer_bulk_generate_form(){
    $logintype = $this->CI->session->userdata('logintype');
    $sip_device=null;
    $opensips_device=null;
        if ($logintype == 1 || $logintype == 5) {
            $account_data = $this->CI->session->userdata("accountinfo");
            $loginid = $account_data['id'];

        }else{
            $loginid = "0";
            if(common_model::$global_config['system_config']['opensips']== 1){
	      $opensips_device =array('Create Opensips Device', 'opensips_device_flag', 'CHECKBOX', array('name' => 'opensips_device_flag', 'value' => 'on', 'checked' => false), '', 'tOOL TIP', '');
            }
            else{
	      $sip_device =array('Create SIP Device', 'sip_device_flag', 'CHECKBOX', array('name' => 'sip_device_flag', 'value' => 'on', 'checked' => false), '', 'tOOL TIP', '');
            }
        }
        $form['forms'] = array(base_url().'accounts/customer_bulk_save/', array("id" => "customer_bulk_form", "name" => "customer_bulk_form"));
        $form['General Details'] = array(
        
            array('Customer Count', 'INPUT', array('name' => 'count', 'size' => '20', 'maxlength' => '5', 'class' => "text field medium"), 'trim|required|min_length[1]|max_length[5]|numeric|xss_clean', 'tOOL TIP', ''), 
            array('Start prefix', 'INPUT', array('name' => 'prefix', 'size' => '20', 'maxlength' => '3', 'class' => "text field medium"), 'trim|required|min_length[1]|numeric|max_length[3]|xss_clean', 'tOOL TIP', ''), 
            array('Account Length', 'INPUT', array('name' => 'account_length', 'size' => '20', 'maxlength' => '2', 'class' => "text field medium"), 'trim|required|min_length[1]|max_length[2]|numeric|xss_clean', 'tOOL TIP', ''), 
            array('Company', 'INPUT', array('name' => 'company_name', 'maxlength' => '40', 'size' => '15', 'class' => 'text field medium'), 'trim|required|xss_clean', 'tOOL TIP', ''),
           array('Country',array('name'=>'country_id','class'=>'country_id'), 'SELECT', '',array("name"=>"country_id","rules"=>"required"), 'tOOL TIP', 'Please Enter account number', 'id', 'country', 'countrycode', 'build_dropdown', '', ''),
            array('Timezone',array('name'=>'timezone_id','class'=>'timezone_id'), 'SELECT', '', array("name"=>"timezone_id","rules"=>"required"), 'tOOL TIP', 'Please Enter account number', 'id', 'gmtzone', 'timezone', 'build_dropdown', '', ''),
            array('Pin', 'pin', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', '', '', '', 'set_allow'),
	    $sip_device,
	    $opensips_device
        );
        $form['Profile Details'] = array(
            array('Rate Group',array('name'=>'pricelist_id','class'=>'pricelist_id'), 'SELECT', '',"required", 'tOOL TIP', 'Please Enter account number', 'id', 'name', 'pricelists', 'build_dropdown', 'where_arr', array("status" => "0","reseller_id" => $loginid)),
            array('Currency',array('name'=>'currency_id','class'=>'currency_id'), 'SELECT', '',array("name"=>"currency_id","rules"=>"required"), 'tOOL TIP', 'Please Enter account number', 'id', 'currencyname,currency', 'currency', 'build_concat_dropdown', '', array()),
            array('Balance', 'INPUT', array('name' => 'balance', 'size' => '20', 'maxlength' => '15', 'class' => "text field medium"), 'trim|currency_decimal|xss_clean', 'tOOL TIP', ''),
            array('Credit Limit', 'INPUT', array('name' => 'credit_limit', 'size' => '20', 'maxlength' => '15', 'class' => "text field medium"), 'trim|currency_decimal|xss_clean', 'tOOL TIP', ''),
            array('Account Type', 'posttoexternal', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', '', '', '', 'set_account_type'),
            array('Valid Days', 'INPUT', array('name' => 'validfordays', 'size' => '20', 'maxlength' => '7', 'class' => "text field medium"), 'trim|numeric|min_length[1]|max_length[4]|xss_clean', 'tOOL TIP', ''),
            );
        $form['button_save'] = array('name' => 'action', 'content' => 'Save', 'value' => 'save', 'id' => 'submit', 'type' => 'button', 'class' => 'btn btn-line-parrot');
        $form['button_cancel'] = array('name' => 'action', 'content' => 'Cancel', 'value' => 'cancel', 'type' => 'button', 'class' => 'btn btn-line-sky margin-x-10', 'onclick' => 'return redirect_page(\'NULL\')');

        return $form;
    }
    function get_customer_callerid_fields() {
        $form['forms'] = array(base_url() . 'accounts/customer_add_callerid/', array("id" => "callerid_form"));
        $form['callerid'] = array(
            array('', 'HIDDEN', array('name' => 'flag'), '', '', '', ''),
            array('Account Number', 'INPUT', array('name' => 'accountid', 'size' => '20', 'readonly' => true, 'maxlength' => '15', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter account number'),
            array('Status', 'status', 'CHECKBOX', array('name' => 'status'), '', 'tOOL TIP', ''),
            array('Caller Id Name', 'INPUT', array('name' => 'callerid_name', 'size' => '20', 'maxlength' => '15', 'class' => "text field medium"), 'trim|required', 'tOOL TIP', ''),
            array('Caller Id Number', 'INPUT', array('name' => 'callerid_number', 'size' => '20', 'maxlength' => '15', 'class' => "text field medium"), 'trim|numeric', 'tOOL TIP', '')
        );
        $form['button_save'] = array('name' => 'action', 'content' => 'Save', 'value' => 'save',"id"=>"submit", 'type' => 'button', 'class' => 'btn btn-line-parrot');
        return $form;
    }

        function get_customer_payment_fields($currency, $number, $currency_id, $id) {
        $form['forms'] = array(base_url() . '/accounts/customer_payment_save/', array('id' => 'accounts_conf_form', 'method' => 'POST', 'name' => 'accounts_conf_form'));
        $form['Process Payment'] = array(
            array('', 'HIDDEN', array('name' => 'id', 'value' => $id), '', '', '', ''),
            array('', 'HIDDEN', array('name' => 'account_currency', 'value' => $currency_id), '', '', ''),
            array('Account ', 'INPUT', array('name' => 'accountid', 'size' => '20', 'value' => $number, 'readonly' => true, 'maxlength' => '15', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter account number'),
            array('Payment' , 'INPUT', array('name' => 'credit', 'size' => '20', 'maxlength' => '8', 'class' => "text field medium"), 'trim|required', 'tOOL TIP', ''),
	    array('Type', 'payment_type', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', '', '', '', 'set_payment_type'),
		array('Note', 'TEXTAREA', array('name' => 'notes', 'size' => '20','cols'=>'63','rows'=>'5', 'class' => "text field medium", 'style'=>"width: 532px; height: 128px;"), '', 'tOOL TIP', ''));
        $form['button_save'] = array('name' => 'action', 'content' => 'Save', 'value' => 'save','id'=>"submit",'type' => 'button', 'class' => 'btn btn-line-parrot');
        return $form;
    }

    function get_form_reseller_fields($id=false) {
        $readable=false;
        $logintype = $this->CI->session->userdata('logintype');
        if ($logintype == 1 || $logintype == 5) {
            $account_data = $this->CI->session->userdata("accountinfo");
            $loginid = $account_data['id'];

        }else{
            $loginid = "0";
        }
         if ($id > 0){
            $val = 'accounts.email.' . $id;
            $readable='disabled';
        } else
            $val ='accounts.email';
        $uname = $this->CI->common->find_uniq_rendno(common_model::$global_config['system_config']['cardlength'], 'number', 'accounts');
          $password = $this->CI->common->generate_password();
        $form['forms'] = array(base_url() . 'accounts/reseller_save/', array("id" => "reseller_form", "name" => "reseller_form"));
        $form['Client Panel Access'] = array(
            array('', 'HIDDEN', array('name' => 'id'), '', '', '', ''),
            array('', 'HIDDEN', array('name' => 'type', 'value' => '1'), '', '', ''),
            array('Account', 'INPUT', array('name' => 'number', 'value' => $uname, 'size' => '20', 'readonly' => true, 'maxlength' => '20', 'class' => "text field medium"), 'required|max_length[15]', 'tOOL TIP', 'Please Enter account number'),
           array('Password', 'INPUT', array('name' => 'password', 'value'=>$password,'size' => '20', 'maxlength' => '20', 'class' => "text field medium",'id'=>'password'), 'required|min_length[5]|max_length[20]', 'tOOL TIP', '','<i style="cursor:pointer; font-size: 17px; padding-left:10px; padding-top:6px;" title="Reset Password" class="change_pass fa fa-refresh" ></i>'),
            );

        $form['Billing Information'] = array(
            array('Rate Group', 'pricelist_id', 'SELECT', '',array("name"=>"pricelist_id",'rules'=>'required'), 'tOOL TIP', 'Please Enter account number', 'id', 'name', 'pricelists', 'build_dropdown', 'where_arr', array("status" => "0","reseller_id" => "0")),
            array('Billing Schedule',array('name'=> 'sweep_id','class'=>'sweep_id'), 'SELECT', '', '', 'tOOL TIP', '', 'id', 'sweep', 'sweeplist', 'build_dropdown', '', ''),
            array('Billing Day',array("name"=>'invoice_day',"class"=>"invoice_day"), 'SELECT', '', '', 'tOOL TIP', '', '', '', '', 'set_invoice_option'),
             array('Currency',array('name'=>'currency_id','class'=>'currency_id'), 'SELECT', '',array("name"=>"currency_id","rules"=>"required"), 'tOOL TIP', 'Please Enter account number', 'id', 'currencyname,currency', 'currency', 'build_concat_dropdown', '', array()),
            
            array('Commission Rate in (%)', 'INPUT', array('name' => 'commission_rate', 'size' => '20', 'maxlength' => '11', 'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('Account Type',array('name' => 'posttoexternal', 'disabled' => $readable,'class' => 'posttoexternal', 'id' => 'posttoexternal'), 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', '', '', '', 'set_account_type'),
	    array('Credit Limit', 'INPUT', array('name' => 'credit_limit', 'size' => '20', 'maxlength' => '15', 'class' => "text field medium"), '', 'tOOL TIP', ''),
	    array('Tax','tax_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'taxes_description', 'taxes', 'build_dropdown', 'where_arr',array('status'=>0,'reseller_id'=>$loginid), 'multi'),
	  );

        $form['Reseller Profile'] = array(
         //   array('Language', 'language_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'languagename', 'language', 'build_dropdown', '', ''),
           
            array('First Name', 'INPUT', array('name' => 'first_name', 'id' => 'first_name', 'size' => '50', 'maxlength' => '40', 'class' => "text field medium"), 'trim|required|max_length[20]|alpha_numeric|xss_clean', 'tOOL TIP', ''),
            array('Last Name', 'INPUT', array('name' => 'last_name', 'size' => '15', 'maxlength' => '40', 'class' => "text field medium"), 'trim|alpha_dash|xss_clean', 'tOOL TIP', ''),
	    array('Company', 'INPUT', array('name' => 'company_name', 'maxlength' => '40', 'size' => '50', 'class' => 'text field medium'), 'trim|xss_clean', 'tOOL TIP', ''),
            array('Telephone 1', 'INPUT', array('name' => 'telephone_1', 'size' => '15', 'maxlength' => '20', 'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('Telephone 2', 'INPUT', array('name' => 'telephone_2', 'size' => '15', 'maxlength' => '20', 'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('Email', 'INPUT', array('name' => 'email', 'size' => '50', 'maxlength' => '100', 'class' => "text field medium"), 'required|valid_email|is_unique['.$val.']', 'tOOL TIP', ''),
            array('Address 1', 'INPUT', array('name' => 'address_1', 'size' => '15', 'maxlength' => '100', 'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('Address 2', 'INPUT', array('name' => 'address_2', 'size' => '15', 'maxlength' => '100', 'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('City', 'INPUT', array('name' => 'city', 'size' => '20', 'maxlength' => '25', 'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('Province/State', 'INPUT', array('name' => 'province', 'size' => '15', 'maxlength' => '25', 'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('Zip/Postal Code', 'INPUT', array('name' => 'postal_code', 'size' => '15', 'maxlength' => '15', 'class' => "text field medium"), 'trim|xss_clean', 'tOOL TIP', ''),
             array('Country',array('name'=>'country_id','class'=>'country_id'), 'SELECT', '',array("name"=>"country_id","rules"=>"required"), 'tOOL TIP', 'Please Enter account number', 'id', 'country', 'countrycode', 'build_dropdown', '', ''),
            array('Timezone',array('name'=>'timezone_id','class'=>'timezone_id'), 'SELECT', '', array("name"=>"timezone_id","rules"=>"required"), 'tOOL TIP', 'Please Enter account number', 'id', 'gmtzone', 'timezone', 'build_dropdown', '', ''),
            array('Account Status', 'status', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', '', '', '', 'set_status'),
        );
        $form['Low Balance Alert Email'] = array(
            array('Low Balance Alert Level', 'INPUT', array('name' => 'notify_credit_limit', 'size' => '20', 'maxlength' => '15', 'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('Enable Email Alerts ?', 'notify_flag', 'SELECT', '', '', 'tOOL TIP', '', '', '', '', 'set_allow'),
            array('Email Address', 'INPUT', array('name' => 'notify_email', 'size' => '50', 'maxlength' => '100', 'class' => "text field medium"), 'valid_email', 'tOOL TIP', ''),
        );        
        
        $form['button_cancel'] = array('name' => 'action', 'content' => 'Cancel', 'value' => 'cancel', 'type' => 'button', 'class' => 'btn btn-line-sky margin-x-10', 'onclick' => 'return redirect_page(\'/accounts/reseller_list/\')');
        $form['button_save'] = array('name' => 'action', 'content' => 'Save', 'value' => 'save', 'type' => 'submit', 'class' => 'btn btn-line-parrot');

        return $form;
    }

    function get_form_provider_fields($values = '') {
        $uname = $this->CI->common->find_uniq_rendno(common_model::$global_config['system_config']['cardlength'], 'number', 'accounts');
           $password = $this->CI->common->generate_password();
        $form['forms'] = array(base_url() . 'accounts/provider_save/', array("id" => "provider_form", "name" => "provider_form"));
        $form['Client Panel Access'] = array(
            array('', 'HIDDEN', array('name' => 'id'), '', '', '', ''),
            array('', 'HIDDEN', array('name' => 'type', 'value' => '3'), '', '', ''),
            array('Account', 'INPUT', array('name' => 'number', 'value' => $uname, 'size' => '20', 'readonly' => true, 'maxlength' => '15', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter account number'),
          array('Password', 'INPUT', array('name' => 'password', 'value'=>$password,'size' => '20', 'maxlength' => '20', 'class' => "text field medium",'id'=>'password'), 'required|min_length[5]|max_length[20]', 'tOOL TIP', '','<i style="cursor:pointer; font-size: 17px; padding-left:10px; padding-top:6px;" title="Reset Password" class="change_pass fa fa-refresh" ></i>'),
          );

        $form['Account & Billing Information'] = array(
            array('Account Status', 'status', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', '', '', '', 'set_status'),
            array('Billing Schedule',array('name'=> 'sweep_id','class'=>'sweep_id'), 'SELECT', '', '', 'tOOL TIP', '', 'id', 'sweep', 'sweeplist', 'build_dropdown', '', ''),
            array('Billing Day',array("name"=>'invoice_day',"class"=>"invoice_day"), 'SELECT', '', '', 'tOOL TIP', '', '', '', '', 'set_invoice_option'),
            array('Currency',array('name'=>'currency_id','class'=>'currency_id'), 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'currencyname,currency', 'currency', 'build_concat_dropdown', '', array()),
            array('Tax','tax_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'taxes_description', 'taxes', 'build_dropdown',  'where_arr',array('status'=>0), 'multi'),
        );

        $form['Provider Profile'] = array(
          //  array('Language', 'language_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'languagename', 'language', 'build_dropdown', '', ''),
            
            array('First Name', 'INPUT', array('name' => 'first_name', 'id' => 'first_name', 'size' => '25', 'maxlength' => '50', 'class' => "text field medium"), 'trim|required|max_length[20]|alpha_numeric|xss_clean', 'tOOL TIP', ''),
            array('Last Name', 'INPUT', array('name' => 'last_name', 'size' => '15', 'maxlength' => '50', 'class' => "text field medium"), 'trim|alpha_dash|xss_clean', 'tOOL TIP', ''),
	    array('Company', 'INPUT', array('name' => 'company_name', 'maxlength' => '150', 'size' => '50', 'class' => 'text field medium'), 'trim|xss_clean', 'tOOL TIP', ''),
            array('Telephone 1', 'INPUT', array('name' => 'telephone_1', 'size' => '15', 'maxlength' => '20', 'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('Telephone 2', 'INPUT', array('name' => 'telephone_2', 'size' => '15', 'maxlength' => '20', 'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('Email', 'INPUT', array('name' => 'email', 'size' => '50', 'maxlength' => '100', 'class' => "text field medium"), 'required|valid_email', 'tOOL TIP', ''),
            array('Address 1', 'INPUT', array('name' => 'address_1', 'size' => '15', 'maxlength' => '100', 'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('Address 2', 'INPUT', array('name' => 'address_2', 'size' => '15', 'maxlength' => '100', 'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('City', 'INPUT', array('name' => 'city', 'size' => '20', 'maxlength' => '25', 'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('Province/State', 'INPUT', array('name' => 'province', 'size' => '15', 'maxlength' => '25', 'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('Zip/Postal Code', 'INPUT', array('name' => 'postal_code', 'size' => '15', 'maxlength' => '15', 'class' => "text field medium"), 'trim|xss_clean', 'tOOL TIP', ''),
             array('Country',array('name'=>'country_id','class'=>'country_id'), 'SELECT', '',array("name"=>"country_id","rules"=>"required"), 'tOOL TIP', 'Please Enter account number', 'id', 'country', 'countrycode', 'build_dropdown', '', ''),
            array('Timezone',array('name'=>'timezone_id','class'=>'timezone_id'), 'SELECT', '', array("name"=>"timezone_id","rules"=>"required"), 'tOOL TIP', 'Please Enter account number', 'id', 'gmtzone', 'timezone', 'build_dropdown', '', ''),
        );

        $form['button_cancel'] = array('name' => 'action', 'content' => 'Cancel', 'value' => 'cancel', 'type' => 'button', 'class' => 'btn btn-line-sky margin-x-10', 'onclick' => 'return redirect_page(\'/accounts/customer_list/\')');
        $form['button_save'] = array('name' => 'action', 'content' => 'Save', 'value' => 'save', 'type' => 'submit', 'class' => 'btn btn-line-parrot');


        return $form;
    }

    function get_user_form_fields($id=false) {
	 if ($id > 0)
            $val = 'accounts.email.' . $id;
        else
            $val ='accounts.email';
        $uname = $this->CI->common->find_uniq_rendno(common_model::$global_config['system_config']['cardlength'], 'number', 'accounts');
       //   $password = $this->CI->common->generate_password();
            $password = $this->CI->common->generate_password();
        $form['forms'] = array(base_url() . 'user/user_edit_account/', array("id" => "user_form", "name" => "user_form"));

        $form['User Profile'] = array(
            array('', 'HIDDEN', array('name' => 'id'), '', '', '', ''),
            array('', 'HIDDEN', array('name' => 'type', 'value' => '0'), '', '', ''),
            array('Account Number', 'INPUT', array('name' => 'number', 'value' => $uname, 'size' => '20', 'readonly' => true, 'maxlength' => '15', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter account number'),
         array('Password', 'INPUT', array('name' => 'password', 'value'=>$password,'size' => '20', 'maxlength' => '20', 'class' => "text field medium",'id'=>'password'), 'required|min_length[5]|max_length[20]', 'tOOL TIP', '','<i style="cursor:pointer; font-size: 17px; padding-left:10px; padding-top:6px;" title="Reset Password" class="change_pass fa fa-refresh" ></i>'),
            array('Pin', 'INPUT', array('name' => 'pin', 'size' => '20', 'maxlength' => '20', 'class' => "text field medium"), 'max_length[20]', 'tOOL TIP', ''),
         //   array('Language', 'language_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'languagename', 'language', 'build_dropdown', '', ''),
            array('Company', 'INPUT', array('name' => 'company_name', 'maxlength' => '150', 'size' => '15', 'class' => 'text field medium'), 'trim|xss_clean', 'tOOL TIP', ''),
            array('First Name', 'INPUT', array('name' => 'first_name', 'id' => 'first_name', 'size' => '15', 'maxlength' => '50', 'class' => "text field medium"), 'trim|required|max_length[20]|xss_clean', 'tOOL TIP', 'Please Enter account number'),
            array('Last Name', 'INPUT', array('name' => 'last_name', 'size' => '15', 'maxlength' => '50', 'class' => "text field medium"), 'trim|alpha_dash|xss_clean', 'tOOL TIP', 'Please Enter Password'),
            array('Telephone 1', 'INPUT', array('name' => 'telephone_1', 'size' => '15', 'maxlength' => '20', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter Password'),
            array('Telephone 2', 'INPUT', array('name' => 'telephone_2', 'size' => '15', 'maxlength' => '20', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter Password'),
            array('Email', 'INPUT', array('name' => 'email', 'size' => '50', 'maxlength' => '100', 'class' => "text field medium"), 'required|valid_email|is_unique['.$val.']', 'tOOL TIP', 'Please Enter Password'),
            array('Address 1', 'INPUT', array('name' => 'address_1', 'size' => '15', 'maxlength' => '100', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter Password'),
            array('Address 2', 'INPUT', array('name' => 'address_2', 'size' => '15', 'maxlength' => '100', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter Password'),
            array('City', 'INPUT', array('name' => 'city', 'size' => '20', 'maxlength' => '25', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter Password'),
            array('Province/State', 'INPUT', array('name' => 'province', 'size' => '15', 'maxlength' => '25', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter Password'),
            array('Zip/Postal Code', 'INPUT', array('name' => 'postal_code', 'size' => '15', 'maxlength' => '15', 'class' => "text field medium"), 'trim|xss_clean', 'tOOL TIP', 'Please Enter Password'),
            array('Country',array('name'=>'country_id','class'=>'country_id'), 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'country', 'countrycode', 'build_dropdown', '', ''),
            array('Timezone',array('name'=>'timezone_id','class'=>'timezone_id'), 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'gmtzone', 'timezone', 'build_dropdown', '', '')
        );
        $form['Low Balance Alert Email'] = array(
            array('Low Balance Alert Level', 'INPUT', array('name' => 'notify_credit_limit', 'size' => '20', 'maxlength' => '15', 'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('Enable Email Alerts ?', 'notify_flag', 'SELECT', '', '', 'tOOL TIP', '', '', '', '', 'set_allow'),
            array('Email Address', 'INPUT', array('name' => 'notify_email', 'size' => '50', 'maxlength' => '100', 'class' => "text field medium"), 'valid_email', 'tOOL TIP', ''),
        );        
        
        $form['button_cancel'] = array('name' => 'action', 'content' => 'Cancel', 'value' => 'cancel', 'type' => 'button', 'class' => 'btn btn-line-sky margin-x-10', 'onclick' => 'return redirect_page(\'/user/user/\')');
        $form['button_save'] = array('name' => 'action', 'content' => 'Save', 'value' => 'save', 'type' => 'submit', 'class' => 'btn btn-line-parrot');

        return $form;
    }

    function get_reseller_own_form_fields() {

        $uname = $this->CI->common->find_uniq_rendno(common_model::$global_config['system_config']['cardlength'], 'number', 'accounts');
          $password = $this->CI->common->generate_password();
        $form['forms'] = array(base_url() . 'accounts/reseller_edit_account/', array("id" => "user_form", "name" => "user_form"));

        $form['Reseller Profile'] = array(
            array('', 'HIDDEN', array('name' => 'id'), '', '', '', ''),
            array('', 'HIDDEN', array('name' => 'type', 'value' => '0'), '', '', ''),
            array('Account Number', 'INPUT', array('name' => 'number', 'value' => $uname, 'size' => '20', 'readonly' => true, 'maxlength' => '15', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter account number'),
           array('Password', 'INPUT', array('name' => 'password', 'value'=>$password,'size' => '20', 'maxlength' => '20', 'class' => "text field medium",'id'=>'password'), 'required|min_length[5]|max_length[20]', 'tOOL TIP', '','<i style="cursor:pointer; font-size: 17px; padding-left:10px; padding-top:6px;" title="Reset Password" class="change_pass fa fa-refresh" ></i>'),
        //    array('Language', 'language_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'languagename', 'language', 'build_dropdown', '', ''),
            array('Company', 'INPUT', array('name' => 'company_name', 'maxlength' => '150', 'size' => '15', 'class' => 'text field medium'), 'trim|xss_clean', 'tOOL TIP', ''),
            array('First Name', 'INPUT', array('name' => 'first_name', 'id' => 'first_name', 'size' => '15', 'maxlength' => '50', 'class' => "text field medium"), 'trim|required|max_length[20]|alpha_numeric|xss_clean', 'tOOL TIP', 'Please Enter account number'),
            array('Last Name', 'INPUT', array('name' => 'last_name', 'size' => '15', 'maxlength' => '50', 'class' => "text field medium"), 'trim|alpha_dash|xss_clean', 'tOOL TIP', 'Please Enter Password'),
            array('Telephone 1', 'INPUT', array('name' => 'telephone_1', 'size' => '15', 'maxlength' => '20', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter Password'),
            array('Telephone 2', 'INPUT', array('name' => 'telephone_2', 'size' => '15', 'maxlength' => '20', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter Password'),
            array('Email', 'INPUT', array('name' => 'email', 'size' => '50', 'maxlength' => '100', 'class' => "text field medium"), 'required|valid_email', 'tOOL TIP', 'Please Enter Password'),
            array('Address 1', 'INPUT', array('name' => 'address_1', 'size' => '15', 'maxlength' => '100', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter Password'),
            array('Address 2', 'INPUT', array('name' => 'address_2', 'size' => '15', 'maxlength' => '100', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter Password'),
            array('City', 'INPUT', array('name' => 'city', 'size' => '20', 'maxlength' => '25', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter Password'),
            array('Province/State', 'INPUT', array('name' => 'province', 'size' => '15', 'maxlength' => '25', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter Password'),
            array('Zip/Postal Code', 'INPUT', array('name' => 'postal_code', 'size' => '15', 'maxlength' => '15', 'class' => "text field medium"), 'trim|xss_clean', 'tOOL TIP', 'Please Enter Password'),
            array('Country',array('name'=>'country_id','class'=>'country_id'), 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'country', 'countrycode', 'build_dropdown', '', ''),
            array('Timezone', array('name'=>'timezone_id','class'=>'timezone_id'), 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'gmtzone', 'timezone', 'build_dropdown', '', '')
        );
        $form['Low Balance Alert Email'] = array(
            array('Low Balance Alert Level', 'INPUT', array('name' => 'notify_credit_limit', 'size' => '20', 'maxlength' => '15', 'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('Enable Email Alerts ?', 'notify_flag', 'SELECT', '', '', 'tOOL TIP', '', '', '', '', 'set_allow'),
            array('Email Address', 'INPUT', array('name' => 'notify_email', 'size' => '50', 'maxlength' => '100', 'class' => "text field medium"), 'valid_email', 'tOOL TIP', ''),
        );        
        
        $form['button_cancel'] = array('name' => 'action', 'content' => 'Cancel', 'value' => 'cancel', 'type' => 'button', 'class' => 'btn btn-line-sky margin-x-10', 'onclick' => 'return redirect_page(\'/dashboard/\')');
        $form['button_save'] = array('name' => 'action', 'content' => 'Save', 'value' => 'save', 'type' => 'submit', 'class' => 'btn btn-line-parrot');

        return $form;
    }

    
    function get_form_admin_fields($entity_type = '',$id=false) {
       if ($id > 0)
            $val = 'accounts.email.' . $id;
        else
            $val ='accounts.email';
    	
        $uname = $this->CI->common->find_uniq_rendno(common_model::$global_config['system_config']['cardlength'], 'number', 'accounts');
        $type= $entity_type == 'admin' ? 2 : 4;
        $form['forms'] = array(base_url() . 'accounts/'.$entity_type.'_save/', array("id" => "admin_form", "name" => "admin_form"));
        $form['Client Panel Access'] = array(
            array('', 'HIDDEN', array('name' => 'id'), '', '', '', ''),
            array('', 'HIDDEN', array('name' => 'type', 'value' => $type), '', '', ''),
            array('Account', 'INPUT', array('name' => 'number', 'value' => $uname, 'size' => '20', 'readonly' => true, 'maxlength' => '20', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter account number'),
            array('Password', 'PASSWORD', array('name' => 'password', 'size' => '20', 'maxlength' => '20', 'class' => "text field medium"), 'trim|required|min_length[5]|max_length[20]|xss_clean', 'tOOL TIP', 'Please Enter Password'),
            );
        $form[$entity_type.' Profile'] = array(
          //  array('Language', 'language_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'languagename', 'language', 'build_dropdown', '', ''),
            
            array('First Name', 'INPUT', array('name' => 'first_name', 'id' => 'first_name', 'size' => '15', 'maxlength' => '40', 'class' => "text field medium"), 'trim|required|max_length[20]|alpha_numeric|xss_clean', 'tOOL TIP', 'Please Enter account number'),
            array('Last Name', 'INPUT', array('name' => 'last_name', 'size' => '15', 'maxlength' => '40', 'class' => "text field medium"), 'trim|alpha_dash|xss_clean', 'tOOL TIP', 'Please Enter Password'),
	    array('Company', 'INPUT', array('name' => 'company_name', 'maxlength' => '40', 'size' => '15', 'class' => 'text field medium'), 'trim|xss_clean', 'tOOL TIP', ''),
            array('Telephone 1', 'INPUT', array('name' => 'telephone_1', 'size' => '15', 'maxlength' => '20', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter Password'),
            array('Telephone 2', 'INPUT', array('name' => 'telephone_2', 'size' => '15', 'maxlength' => '20', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter Password'),
            array('Email', 'INPUT', array('name' => 'email', 'size' => '50', 'maxlength' => '80', 'class' => "text field medium"), 'required|valid_email|is_unique['.$val.']', 'tOOL TIP', ''),
            array('Address 1', 'INPUT', array('name' => 'address_1', 'size' => '15', 'maxlength' => '80', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter Password'),
            array('Address 2', 'INPUT', array('name' => 'address_2', 'size' => '15', 'maxlength' => '80', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter Password'),
            array('City', 'INPUT', array('name' => 'city', 'size' => '20', 'maxlength' => '20', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter Password'),
            array('Province/State', 'INPUT', array('name' => 'province', 'size' => '15', 'maxlength' => '20', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter Password'),
            array('Zip/Postal Code', 'INPUT', array('name' => 'postal_code', 'size' => '15', 'maxlength' => '12', 'class' => "text field medium"), 'trim|xss_clean', 'tOOL TIP', 'Please Enter Password'),
            array('Country', array('name'=>'country_id','class'=>'country_id'), 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'country', 'countrycode', 'build_dropdown', '', ''),
            array('Timezone',array('name'=> 'timezone_id','class'=>'timezone_id'), 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'gmtzone', 'timezone', 'build_dropdown', '', ''),
            array('Account Status', 'status', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', '', '', '', 'set_status'),
             array('Currency',array('name'=>'currency_id','class'=>'currency_id'), 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'currencyname,currency', 'currency', 'build_concat_dropdown', '', array()),
//             array('Tax','tax_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'taxes_description', 'taxes', 'build_dropdown', 'where_arr',array('status'=>0), 'multi'),
        );

        $form['button_cancel'] = array('name' => 'action', 'content' => 'Cancel', 'value' => 'cancel', 'type' => 'button', 'class' => 'btn btn-line-sky margin-x-10', 'onclick' => 'return redirect_page(\'/accounts/admin_list/\')');
        $form['button_save'] = array('name' => 'action', 'content' => 'Save', 'value' => 'save', 'type' => 'submit', 'class' => 'btn btn-line-parrot');

        return $form;
    }

    function get_form_subadmin_fields($values = '') {
        $uname = $this->CI->common->find_uniq_rendno(common_model::$global_config['system_config']['cardlength'], 'number', 'accounts');
        $form['forms'] = array(base_url() . 'accounts/subadmin_save/', array("id" => "subadmin_form", "name" => "subadmin_form"));
        $form['Client Panel Access'] = array(
            array('', 'HIDDEN', array('name' => 'id'), '', '', '', ''),
            array('', 'HIDDEN', array('name' => 'type', 'value' => '4'), '', '', ''),
            array('Account', 'INPUT', array('name' => 'number', 'value' => $uname, 'size' => '20', 'readonly' => true, 'maxlength' => '15', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter account number'),
            array('Password', 'PASSWORD', array('name' => 'password', 'size' => '20', 'maxlength' => '20', 'class' => "text field medium"), 'trim|required|min_length[5]|max_length[20]|xss_clean', 'tOOL TIP', 'Please Enter Password'));
        $form['Subadmin Profile'] = array(
          //  array('Language', 'language_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'languagename', 'language', 'build_dropdown', '', ''),
            
            array('First Name', 'INPUT', array('name' => 'first_name', 'id' => 'first_name', 'size' => '25', 'maxlength' => '40', 'class' => "text field medium"), 'trim|required|max_length[20]|alpha_numeric|xss_clean', 'tOOL TIP', 'Please Enter account number'),
            array('Last Name', 'INPUT', array('name' => 'last_name', 'size' => '15', 'maxlength' => '40', 'class' => "text field medium"), 'trim|alpha_dash|xss_clean', 'tOOL TIP', 'Please Enter Password'),
	    array('Company', 'INPUT', array('name' => 'company_name', 'maxlength' => '40', 'size' => '15', 'class' => 'text field medium'), 'trim|alpha_numeric_space|xss_clean', 'tOOL TIP', ''),
            array('Telephone 1', 'INPUT', array('name' => 'telephone_1', 'size' => '15', 'maxlength' => '20', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter Password'),
            array('Telephone 2', 'INPUT', array('name' => 'telephone_2', 'size' => '15', 'maxlength' => '20', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter Password'),
            array('Email', 'INPUT', array('name' => 'email', 'size' => '50', 'maxlength' => '80', 'class' => "text field medium"), 'required|valid_email', 'tOOL TIP', 'Please Enter Password'),
            array('Address 1', 'INPUT', array('name' => 'address_1', 'size' => '15', 'maxlength' => '80', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter Password'),
            array('Address 2', 'INPUT', array('name' => 'address_2', 'size' => '15', 'maxlength' => '80', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter Password'),
            array('City', 'INPUT', array('name' => 'city', 'size' => '20', 'maxlength' => '20', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter Password'),
            array('Province/State', 'INPUT', array('name' => 'province', 'size' => '15', 'maxlength' => '35', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter Password'),
            array('Zip/Postal Code', 'INPUT', array('name' => 'postal_code', 'size' => '15', 'maxlength' => '12', 'class' => "text field medium"), 'trim|xss_clean', 'tOOL TIP', 'Please Enter Password'),
            array('Country', array('name'=>'country_id','class'=>'country_id'), 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'country', 'countrycode', 'build_dropdown', '', ''),
            array('Timezone',array('name'=> 'timezone_id','class'=>'timezone_id'), 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'gmtzone', 'timezone', 'build_dropdown', '', ''),
            array('Account Status', 'status', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', '', '', '', 'set_status'),
            array('Currency', 'currency_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'currencyname,currency', 'currency', 'build_concat_dropdown', '', array()),
            array('Tax','tax_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'taxes_description', 'taxes', 'build_dropdown','where_arr',array('status'=>0), 'multi'),
        );

        $form['button_cancel'] = array('name' => 'action', 'content' => 'Cancel', 'value' => 'cancel', 'type' => 'button', 'class' => 'btn btn-line-sky margin-x-10', 'onclick' => 'return redirect_page(\'/accounts/admin_list/\')');
        $form['button_save'] = array('name' => 'action', 'content' => 'Save', 'value' => 'save', 'type' => 'submit', 'class' => 'btn btn-line-parrot');

        return $form;
    }

    
    function get_search_customer_form() {
        $form['forms'] = array("", array('id' => "account_search"));
        $form['Search'] = array(
            
            array('Account', 'INPUT', array('name' => 'number[number]', '', 'size' => '20', 'class' => "text field "), '', 'tOOL TIP', '1', 'number[number-string]', '', '', '', 'search_string_type', ''),
            
            array('First Name', 'INPUT', array('name' => 'first_name[first_name]', '', 'id' => 'first_name', 'size' => '15', 'class' => "text field "), '', 'tOOL TIP', '1', 'first_name[first_name-string]', '', '', '', 'search_string_type', ''),
            array('Last Name', 'INPUT', array('name' => 'last_name[last_name]', 'value' => '', 'size' => '20', 'class' => "text field "), '', 'Tool tips info', '1', 'last_name[last_name-string]', '', '', '', 'search_string_type', ''),
	
            array('Company', 'INPUT', array('name' => 'company_name[company_name]', 'value' => '', 'size' => '20', 'class' => "text field "), '', 'Tool tips info', '1', 'company_name[company_name-string]', '', '', '', 'search_string_type', ''),
            array('Entity Type', 'type', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', '', '', '', 'set_entity_type_customer'),
		array('Rate Group', 'pricelist_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'name', 'pricelists', 'build_dropdown', 'where_arr', array("status" => "0","reseller_id" => "0")),
		array('Account Type', 'posttoexternal', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', '', '', '', 'set_account_type_search'),

            //array('Balance', 'INPUT', array('name' => 'balance[balance]', 'value' => '', 'size' => '20', 'class' => "text field "), '', 'Tool tips info', '1', 'balance[balance-integer]', '', '', '', 'search_int_type', ''),
             array('Balance', 'INPUT', array('name' => 'balance[balance]', 'value' => '', 'size' => '20', 'class' => "text field "), '', 'Tool tips info', '1', 'balance[balance-integer]', '', '', '', 'search_int_type', ''),
            array('Credit Limit', 'INPUT', array('name' => 'credit_limit[credit_limit]', 'value' => '', 'size' => '20', 'class' => "text field "), '', 'Tool tips info', '1', 'credit_limit[credit_limit-integer]', '', '', '', 'search_int_type', ''),
	array('Status', 'status', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', '', '', '', 'set_search_status'),
          //  array('Billing Cycle', 'sweep_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'sweep', 'sweeplist', 'build_dropdown', '', ''),
          //  array('Country', 'country_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'country', 'countrycode', 'build_dropdown', '', ''),
        //    array('Currency', 'currency_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'currencyname,currency', 'currency', 'build_concat_dropdown', '', array()),
array('', 'HIDDEN', 'ajax_search', '1', '', '', ''),
            array('', 'HIDDEN', 'advance_search', '1', '', '', '')
        );

        $form['button_search'] = array('name' => 'action', 'id' => "account_search_btn", 'content' => 'Search', 'value' => 'save', 'type' => 'button', 'class' => 'btn btn-line-parrot pull-right');
        $form['button_reset'] = array('name' => 'action', 'id' => "id_reset", 'content' => 'Clear', 'value' => 'cancel', 'type' => 'reset', 'class' => 'btn btn-line-sky pull-right margin-x-10');

        return $form;
    }
    /*transfer*/
	 function get_customer_transfer_fields($currency, $number, $currency_id, $id) {
        $form['forms'] = array(base_url() . 'accounts/customer_transfer_save/', array('id' => 'transfer_form', 'method' => 'POST', 'name' => 'transfer_form'));
        $form['Fund Transfer'] = array(
            array('', 'HIDDEN', array('name' => 'id', 'value' => $id), '', '', '', ''),
            array('', 'HIDDEN', array('name' => 'account_currency', 'value' => $currency_id), '', '', ''),
            array('From Account ', 'INPUT', array('name' => 'fromaccountid', 'size' => '20', 'value' => $number, 'readonly' => true, 'maxlength' => '15', 'class' => "text field medium"), 'required', 'tOOL TIP', 'Please Enter account number'),
	        array('To Account ', 'INPUT', array('name' => 'toaccountid', 'size' => '20', 'maxlength' => '15', 'class' => "text field medium"), 'trim|required|numeric', 'tOOL TIP', 'Please Enter to account number'),
            array('Amount' , 'INPUT', array('name' => 'credit', 'size' => '20', 'maxlength' => '8', 'class' => "text field medium"), 'trim|required|numeric', 'tOOL TIP', ''),
	        array('Note', 'TEXTAREA', array('name' => 'notes', 'size' => '20','cols'=>'63','rows'=>'5', 'class' => "text field medium", 'style'=>"width:515px; height: 128px;"), '', 'tOOL TIP', '')
        );
        $form['button_save'] = array('name' => 'action', 'content' => 'Transfer', 'value' => 'save','id'=>"submit",'type' => 'submit', 'class' => 'btn btn-line-parrot');
        return $form;
    }
    function get_provider_search_form() {
        $form['forms'] = array("", array('id' => "account_search"));
        $form['Search'] = array(
            
            array('Account Number', 'INPUT', array('name' => 'number[number]', '', 'size' => '20', 'class' => "text field "), '', 'tOOL TIP', '1', 'number[number-string]', '', '', '', 'search_string_type', ''),
            array('First Name', 'INPUT', array('name' => 'first_name[first_name]', '', 'id' => 'first_name', 'size' => '15', 'class' => "text field "), '', 'tOOL TIP', '1', 'first_name[first_name-string]', '', '', '', 'search_string_type', ''),
            array('Last Name', 'INPUT', array('name' => 'last_name[last_name]', 'value' => '', 'size' => '20', 'class' => "text field "), '', 'Tool tips info', '1', 'last_name[last_name-string]', '', '', '', 'search_string_type', ''),
            array('Company', 'INPUT', array('name' => 'company_name[company_name]', 'value' => '', 'size' => '20', 'class' => "text field "), '', 'Tool tips info', '1', 'company_name[company_name-string]', '', '', '', 'search_string_type', ''),
            array('Country', 'country_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'country', 'countrycode', 'build_dropdown', '', ''),
            array('Currency', 'currency_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'currencyname,currency', 'currency', 'build_concat_dropdown', '', array()),
array('', 'HIDDEN', 'ajax_search', '1', '', '', ''),
            array('', 'HIDDEN', 'advance_search', '1', '', '', '')
        );
        $form['button_search'] = array('name' => 'action', 'id' => "account_search_btn", 'content' => 'Search', 'value' => 'save', 'type' => 'button', 'class' => 'btn btn-line-parrot pull-right');
        $form['button_reset'] = array('name' => 'action', 'id' => "id_reset", 'content' => 'Clear', 'value' => 'cancel', 'type' => 'reset', 'class' => 'btn btn-line-sky pull-right margin-x-10');
        return $form;
    }
     
      function get_reseller_search_form() {
        $form['forms'] = array("", array('id' => "account_search"));
        $form['Search'] = array(
           
            array('Account', 'INPUT', array('name' => 'number[number]', '', 'size' => '20', 'class' => "text field "), '', 'tOOL TIP', '1', 'number[number-string]', '', '', '', 'search_string_type', ''),
            
            array('First Name', 'INPUT', array('name' => 'first_name[first_name]', '', 'id' => 'first_name', 'size' => '15', 'class' => "text field "), '', 'tOOL TIP', '1', 'first_name[first_name-string]', '', '', '', 'search_string_type', ''),
            array('Last Name', 'INPUT', array('name' => 'last_name[last_name]', 'value' => '', 'size' => '20', 'class' => "text field "), '', 'Tool tips info', '1', 'last_name[last_name-string]', '', '', '', 'search_string_type', ''),
            array('Company', 'INPUT', array('name' => 'company_name[company_name]', 'value' => '', 'size' => '20', 'class' => "text field "), '', 'Tool tips info', '1', 'company_name[company_name-string]', '', '', '', 'search_string_type', ''),
	array('Rate Group', 'pricelist_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'name', 'pricelists', 'build_dropdown', 'where_arr', array("status" => "0","reseller_id" => "0")),
	array('Account Type', 'posttoexternal', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', '', '', '', 'set_account_type_search'),

            array('Balance', 'INPUT', array('name' => 'balance[balance]', 'value' => '', 'size' => '20', 'class' => "text field "), '', 'Tool tips info', '1', 'balance[balance-integer]', '', '', '', 'search_int_type', ''),
            array('Credit Limit', 'INPUT', array('name' => 'credit_limit[credit_limit]', 'value' => '', 'size' => '20', 'class' => "text field "), '', 'Tool tips info', '1', 'credit_limit[credit_limit-integer]', '', '', '', 'search_int_type', ''),
	array('Status', 'status', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', '', '', '', 'set_search_status'),
          //  array('Country', 'country_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'country', 'countrycode', 'build_dropdown', '', ''),
          // array('Currency', 'currency_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'currencyname,currency', 'currency', 'build_concat_dropdown', '', array()),
 array('', 'HIDDEN', 'ajax_search', '1', '', '', ''),
            array('', 'HIDDEN', 'advance_search', '1', '', '', '')
        );

        $form['button_search'] = array('name' => 'action', 'id' => "account_search_btn", 'content' => 'Search', 'value' => 'save', 'type' => 'button', 'class' => 'btn btn-line-parrot pull-right');
        $form['button_reset'] = array('name' => 'action', 'id' => "id_reset", 'content' => 'Clear', 'value' => 'cancel', 'type' => 'reset', 'class' => 'btn btn-line-sky pull-right margin-x-10');

        return $form;
    }

   function get_admin_search_form() {
        $form['forms'] = array("", array('id' => "account_search"));
        $form['Search'] = array(
            
            array('Account', 'INPUT', array('name' => 'number[number]', '', 'size' => '20', 'class' => "text field"), '', 'tOOL TIP', '1', 'number[number-string]', '', '', '', 'search_string_type', ''),
            
            array('First Name', 'INPUT', array('name' => 'first_name[first_name]', '', 'id' => 'first_name', 'size' => '15', 'class' => "text field "), '', 'tOOL TIP', '1', 'first_name[first_name-string]', '', '', '', 'search_string_type', ''),
            array('Last Name', 'INPUT', array('name' => 'last_name[last_name]', 'value' => '', 'size' => '20', 'class' => "text field "), '', 'Tool tips info', '1', 'last_name[last_name-string]', '', '', '', 'search_string_type', ''),
            array('Company', 'INPUT', array('name' => 'company_name[company_name]', 'value' => '', 'size' => '20', 'class' => "text field "), '', 'Tool tips info', '1', 'company_name[company_name-string]', '', '', '', 'search_string_type', ''),
		array('Entity Type', 'type', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', '', '', '', 'set_entity_type_admin'),
            array('Email', 'INPUT', array('name' => 'email[email]', 'value' => '', 'size' => '20', 'class' => "text field "), '', 'Tool tips info', '1', 'email[email-string]', '', '', '', 'search_string_type', ''),
	 array('Phone', 'INPUT', array('name' => 'telephone_1[telephone_1]', 'value' => '', 'size' => '20', 'class' => "text field "), '', 'Tool tips info', '1', 'telephone_1[telephone_1-integer]', '', '', '', 'search_int_type', ''),
	
	    array('Country', 'country_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'country', 'countrycode', 'build_dropdown', '', ''),array('Status', 'status', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', '', '', '', 'set_search_status'),
	   
          //  array('Currency', 'currency_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'currencyname,currency', 'currency', 'build_concat_dropdown', '', array()),
array('', 'HIDDEN', 'ajax_search', '1', '', '', ''),
            array('', 'HIDDEN', 'advance_search', '1', '', '', '')
        );

        $form['button_search'] = array('name' => 'action', 'id' => "account_search_btn", 'content' => 'Search', 'value' => 'save', 'type' => 'button', 'class' => 'btn btn-line-parrot pull-right');
        $form['button_reset'] = array('name' => 'action', 'id' => "id_reset", 'content' => 'Clear', 'value' => 'cancel', 'type' => 'reset', 'class' => 'btn btn-line-sky pull-right margin-x-10');

        return $form;
    }
    function get_subadmin_search_form() {
        $form['forms'] = array("", array('id' => "account_search"));
        $form['Search'] = array(
           
            array('Account Number', 'INPUT', array('name' => 'number[number]', '', 'size' => '20', 'class' => "text field "), '', 'tOOL TIP', '1', 'number[number-string]', '', '', '', 'search_string_type', ''),
            array('First Name', 'INPUT', array('name' => 'first_name[first_name]', '', 'id' => 'first_name', 'size' => '15', 'class' => "text field "), '', 'tOOL TIP', '1', 'first_name[first_name-string]', '', '', '', 'search_string_type', ''),
            array('Last Name', 'INPUT', array('name' => 'last_name[last_name]', 'value' => '', 'size' => '20', 'class' => "text field "), '', 'Tool tips info', '1', 'last_name[last_name-string]', '', '', '', 'search_string_type', ''),
            array('Company', 'INPUT', array('name' => 'company_name[company_name]', 'value' => '', 'size' => '20', 'class' => "text field "), '', 'Tool tips info', '1', 'company_name[company_name-string]', '', '', '', 'search_string_type', ''),
            array('Country', 'country_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'country', 'countrycode', 'build_dropdown', '', ''),
             array('Currency', 'currency_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'currencyname,currency', 'currency', 'build_concat_dropdown', '', array()),
 array('', 'HIDDEN', 'ajax_search', '1', '', '', ''),
            array('', 'HIDDEN', 'advance_search', '1', '', '', '')
        );

        $form['button_search'] = array('name' => 'action', 'id' => "account_search_btn", 'content' => 'Search', 'value' => 'save', 'type' => 'button', 'class' => 'btn btn-line-parrot pull-right');
        $form['button_reset'] = array('name' => 'action', 'id' => "id_reset", 'content' => 'Clear', 'value' => 'cancel', 'type' => 'reset', 'class' => 'btn btn-line-sky pull-right margin-x-10');

        return $form;
    }



    function build_account_list_for_admin() {
        // array(display name, width, db_field_parent_table,feidname, db_field_child_table,function name);
        $grid_field_arr = json_encode(array(array("<input type='checkbox' name='chkAll' class='ace checkall'/><label class='lbl'></label>", "30", "", "", "", ""),
             array("Account", "110", "number", "", "", ""),
	    array("First Name", "120", "first_name", "", "", ""),
            array("Last Name", "130", "last_name", "", "", ""),
            array("Company", "120", "company_name", "", "", ""),
            array("Entity Type", "100", "type", "type", "type", "get_entity_type"),
            array("Email", "210", "email", "", "", ""),
            array("Phone", "150", "telephone_1", "", "", ""),
            array("Country", "110", "country_id", "country", "countrycode", "get_field_name"),
            array("Status", "70", "status", "status", "status", "get_status"),
            array("Action", "100", "", "", "", array(
                    "EDIT" => array("url" => "accounts/admin_edit/", "mode" => "single"),
                    "DELETE" => array("url" => "accounts/admin_delete/", "mode" => "single")
            ))
                ));
        return $grid_field_arr;
    }

    function build_account_list_for_customer() {
        // array(display name, width, db_field_parent_table,feidname, db_field_child_table,function name);
        $grid_field_arr = json_encode(array(array("<input type='checkbox' name='chkAll' class='ace checkall'/><label class='lbl'></label>", "30", "", "", "", ""),
             array("Account", "100", "number", "", "", ""),
            array("First Name", "110", "first_name", "", "", ""),
            array("Last Name", "100", "last_name", "", "", ""),
            array("Company", "101", "company_name", "", "", ""),
            array("Entity Type", "110", "type", "type", "type", "get_entity_type"),
	    array("Rate Group", "104", "pricelist_id", "name", "pricelists", "get_field_name"),
	    array("Account Type", "137", "posttoexternal", "posttoexternal", "posttoexternal", "get_account_type"),
            array("Balance", "110", "balance", "balance", "balance", "convert_to_currency"),
            array("Credit Limit", "110", "credit_limit", "credit_limit", "credit_limit", "convert_to_currency"),
            //array("Cycle", "75", "sweep_id", "sweep", "sweeplist", "get_field_name"),
            array("Status", "80", "status", "status", "status", "get_status"),
            array("Action", "157", "", "", "", array("PAYMENT" => array("url" => "accounts/customer_payment_process_add/", "mode" => "single"),
		    "CALLERID" => array("url" => "accounts/customer_add_callerid/", "mode" => "popup"),
		    "EDIT" => array("url" => "accounts/customer_edit/", "mode" => "single"),
                   // "ANIMAP" => array("url" => "accounts/customer_animap_list/", "mode" => "popup"),
                    "DELETE" => array("url" => "accounts/customer_delete/", "mode" => "single")))
                ));
        return $grid_field_arr;
    }
    function build_account_list_for_reseller() {
        // array(display name, width, db_field_parent_table,feidname, db_field_child_table,function name);
        $grid_field_arr = json_encode(array(array("<input type='checkbox' name='chkAll' class='ace checkall'/><label class='lbl'></label>", "30", "", "", "", ""),
            array("Account", "155", "number", "", "", ""),
            array("First Name", "105", "first_name", "", "", ""),
            array("Last Name", "100", "last_name", "", "", ""),
            array("Company", "180", "company_name", "", "", ""),
            array("Rate Group", "110", "pricelist_id", "name", "pricelists", "get_field_name"),
            array("Account Type", "117", "posttoexternal", "posttoexternal", "posttoexternal", "get_account_type"),
            array("Balance", "110", "balance", "balance", "balance", "convert_to_currency"),
            array("Credit Limit", "110", "credit_limit", "credit_limit", "credit_limit", "convert_to_currency"),
           // array("Cycle", "82", "sweep_id", "sweep", "sweeplist", "get_field_name"),
            array("Status", "90", "status", "status", "status", "get_status"),
            array("Action", "139", "", "", "", array("PAYMENT" => array("url" => "accounts/customer_payment_process_add/", "mode" => "single"),
            "CALLERID" => array("url" => "accounts/customer_add_callerid/", "mode" => 'popup'),
                    "EDIT" => array("url" => "accounts/reseller_edit/", "mode" => "single"),
//                    "TAXES" => array("url" => "accounts/customer_account_taxes/edit/", "mode" => "popup"),
                    "DELETE" => array("url" => "accounts/reseller_delete/", "mode" => "single")
            ))
                ));
        return $grid_field_arr;
    }

    function build_account_list_for_provider() {
        // array(display name, width, db_field_parent_table,feidname, db_field_child_table,function name);
        $grid_field_arr = json_encode(array(array("<input type='checkbox' name='chkAll' class='ace checkall'/><label class='lbl'></label>", "30", "", "", "", ""),
            array("Account Number", "245", "number", "", "", ""),
            array("First Name", "200", "first_name", "", "", ""),
            array("Last Name", "200", "last_name", "", "", ""),
            array("Company", "220", "company_name", "", "", ""),
            array("Account Status", "210", "status", "status", "status", "get_status"),
            array("Action", "140", "", "", "", array(
                    "VIEW" => array("url" => "accounts/provider_edit/", "mode" => "single"),
                    "DELETE" => array("url" => "accounts/provider_delete/", "mode" => "single")
            ))
                ));
        return $grid_field_arr;
    }



    function build_account_list_for_subadmin() {
        $grid_field_arr = json_encode(array(array("<input type='checkbox' name='chkAll' class='ace checkall'/><label class='lbl'></label>", "30", "", "", "", ""),
            array("Account Number", "245", "number", "", "", ""),
            array("First Name", "190", "first_name", "", "", ""),
            array("Last Name", "190", "last_name", "", "", ""),
            array("Company", "215", "company_name", "", "", ""),
            array("Account Status", "200", "status", "status", "status", "get_status"),
            array("Action", "190", "", "", "", array(
                    "VIEW" => array("url" => "accounts/subadmin_edit/", "mode" => "single"),
                    "DELETE" => array("url" => "accounts/subadmin_delete/", "mode" => "single")
            ))
                ));
        return $grid_field_arr;
    }
    function build_grid_buttons_customer() {
        $logintype=$this->CI->session->userdata('userlevel_logintype');
        $provider=null;
        if($logintype !=1)
	  $provider = array("Create Provider","btn btn-line-blue btn","fa fa-plus-circle fa-lg", "button_action", "/accounts/provider_add/");
        $buttons_json = json_encode(array(
	    array("Create Customer","btn btn-line-warning btn","fa fa-plus-circle fa-lg", "button_action", "/accounts/customer_add/"),
            array("Mass Create","btn btn-line-warning btn","fa fa-plus-circle fa-lg", "button_action", "/accounts/customer_bulk_creation/","popup"),
	    $provider,
	     array("Export","btn btn-xing" ," fa fa-download fa-lg", "button_action", "/accounts/customer_export_cdr_xls", 'single'),
            array("Delete","btn btn-line-danger","fa fa-times-circle fa-lg","button_action", "/accounts/customer_selected_delete/")
           ));
        return $buttons_json;
    }


    function build_grid_buttons_admin() {
        $buttons_json = json_encode(array(
	    array("Create Admin","btn btn-line-warning btn","fa fa-plus-circle fa-lg", "button_action", "/accounts/admin_add/"),
	    array("Create Subadmin","btn btn-line-warning btn","fa fa-plus-circle fa-lg", "button_action", "/accounts/subadmin_add/"),
            array("Delete", "btn btn-line-danger","fa fa-times-circle fa-lg", "button_action", "/accounts/admin_selected_delete/")
            ));
        return $buttons_json;
    }

    function build_grid_buttons_subadmin() {
        $buttons_json = json_encode(array(
	    array("Create","btn btn-line-warning btn","fa fa-plus-circle fa-lg", "button_action", "/accounts/subadmin_add/"),
            array("Delete","btn btn-line-danger","fa fa-times-circle fa-lg", "button_action", "/accounts/subadmin_selected_delete/")
           ));
        return $buttons_json;
    }

    function build_grid_buttons_provider() {
        $buttons_json = json_encode(array(
	    array("Create","btn btn-line-warning btn","fa fa-plus-circle fa-lg", "button_action", "/accounts/provider_add/"),
            array("Delete","btn btn-line-danger","fa fa-times-circle fa-lg", "button_action", "/accounts/provider_selected_delete/")
           ));
        return $buttons_json;
    }

    function build_grid_buttons_reseller() {
        $buttons_json = json_encode(array(
	    array("Create","btn btn-line-warning btn","fa fa-plus-circle fa-lg", "button_action", "/accounts/reseller_add/"),
	    array("Export","btn btn-xing" ," fa fa-download fa-lg", "button_action", "/accounts/reseller_export_cdr_xls", 'single'),
            array("Delete","btn btn-line-danger","fa fa-times-circle fa-lg", "button_action", "/accounts/reseller_selected_delete/")
           ));
        return $buttons_json;
    }

    

    function build_ip_list_for_customer($accountid,$accountype) {
	if($accountype == "customer"){
	  $grid_field_arr = json_encode(array(array("Name", "240", "name", "", "", ""),
	      array("IP Address", "280", "ip", "", "", ""),
	      array("Prefix", "220", "prefix", "", "", ""),
// 	      array("Rate Group", "100", "pricelist_id", "name", "pricelists", "get_field_name"),
  	      array("Action", "150", "", "", "", array("DELETE" => array("url" => "accounts/customer_ipmap_action/delete/$accountid/$accountype/", "mode" => "single")))
		  ));
	}else{
	  $grid_field_arr = json_encode(array(array("Name", "240", "name", "", "", ""),
	      array("IP Address", "280", "ip", "", "", ""),
	      array("Prefix", "220", "prefix", "", "", ""),
	      array("Action", "150", "", "", "", array("DELETE" => array("url" => "accounts/customer_ipmap_action/delete/$accountid/$accountype/", "mode" => "single")))
		  ));
	}
        return $grid_field_arr;
    }

    function build_animap_list_for_customer($accountid) {
        $grid_field_arr = json_encode(array(
	    array("Caller Id", "250", "number", "", "", ""),
	   // array("Context", "200", "context", "", "", ""),
            array("Action", "150", "", "", "", array("DELETE" => array("url" => "accounts/customer_animap_action/delete/$accountid/", "mode" => "single")))
                ));
        return $grid_field_arr;
    }

    function build_animap_list_for_user() {
        $grid_field_arr = json_encode(array(array("Caller Id", "625", "number", "", "", ""),
           // array("Context", "150", "context", "", "", ""),
            array("Action", "625", "", "", "", array("DELETE" => array("url" => "user/user_animap_action/delete/", "mode" => "single")))
                ));
        return $grid_field_arr;
    }

    function build_sipiax_list_for_customer() {
        $grid_field_arr = json_encode(array(array("Tech", "150", "tech", "", "", ""),
            array("Type", "150", "type", "", "", ""),
            array("User Name", "150", "username", "sweep", "sweeplist", "get_field_name"),
            array("Password", "150", "secret", "", "", ""),
            array("Context", "150", "context", "", "", "")));
        return $grid_field_arr;
    }

    function set_block_pattern_action_buttons($id) {
        $ret_url = '';
        $ret_url .= '<a href="/did/delete/' . $id . '/" class="icon delete_image" title="Delete" onClick="return get_alert_msg();">&nbsp;</a>';
        return $ret_url;
    }
    function build_ipmap_for_user(){
        
            $grid_field_arr = json_encode(array(
		array("Name","374", "name", "", "", ""),
                array("Address","360", "ip", "", "", ""),
                array("Prefix","360", "prefix", "", "", ""),
//                 array("Rate Group", "210", "pricelist_id", "name", "pricelists", "get_field_name"),
//                 array("ip context", "220", "context", "", "", ""),
                array("Action", "100", "", "", "", array("DELETE" => array("url" => "user/user_ipmap_action/delete/", "mode" => "single")))
                    ));
            return $grid_field_arr;
        
    }
	
        function build_animap_list() {
        $grid_field_arr = json_encode(array(array("Caller Id", "180", "number", "", "", ""),
           // array("Context", "180", "context", "", "", ""),
            array("status","180","status","status","status","get_status"),                        
	      array("Action", "130", "", "", "",
	      array(
	        "EDIT_ANIMAP" => array("url" => "accounts/callingcards_animap_list_edit/", "mode" => "single"),
                "DELETE_ANIMAP" => array("url" => "accounts/callingcards_animap_list_remove/", "mode" => "single")
                )
                )
                ));
        return $grid_field_arr;
    }
        function build_grid_buttons_destination()
    {
            $buttons_json = json_encode(array(
            ));
        return $buttons_json;
    }

}

?>
