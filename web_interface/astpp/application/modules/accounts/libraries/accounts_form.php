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

    function get_customer_form_fields() {

        $uname = $this->CI->common->find_uniq_rendno($this->CI->config->item('size_number'), 'number', 'accounts');
        $form['forms'] = array(base_url() . '/accounts/customer_save/', array("id" => "customer_form", "name" => "customer_form"));
        $form['Client Panel Access'] = array(
            array('', 'HIDDEN', array('name' => 'id'), '', '', '', ''),
            array('', 'HIDDEN', array('name' => 'type', 'value' => '0'), '', '', ''),
            array('Account Number', 'INPUT', array('name' => 'number', 'value' => $uname, 'size' => '20', 'readonly' => true, 'maxlength' => '15', 'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('Password', 'PASSWORD', array('name' => 'password', 'size' => '20', 'maxlength' => '20', 'class' => "text field medium"), 'trim|required|min_length[5]|max_length[20]|xss_clean', 'tOOL TIP', ''));

        $form['Account Settings'] = array(
            array('Account Status', 'status', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', '', '', '', 'set_status'),
            array('Max Channels', 'INPUT', array('name' => 'maxchannels', 'size' => '20', 'maxlength' => '15', 'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('Number Translation', 'INPUT', array('name' => 'dialed_modify', 'size' => '20', 'maxlength' => '200', 'class' => "text field medium"), '', 'tOOL TIP', '')
        );

        $form['Customer Profile'] = array(
            array('Language', 'language_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'languagename', 'language', 'build_dropdown', '', ''),
            array('Company', 'INPUT', array('name' => 'company_name', 'maxlength' => '150', 'size' => '15', 'class' => 'text field medium'), '', 'tOOL TIP', ''),
            array('First Name', 'INPUT', array('name' => 'first_name', 'id' => 'first_name', 'size' => '15', 'maxlength' => '50', 'class' => "text field medium"), 'trim|required|max_length[20]|xss_clean', 'tOOL TIP', ''),
            array('Last Name', 'INPUT', array('name' => 'last_name', 'size' => '15', 'maxlength' => '50', 'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('Telephone 1', 'INPUT', array('name' => 'telephone_1', 'size' => '15', 'maxlength' => '20', 'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('Telephone 2', 'INPUT', array('name' => 'telephone_2', 'size' => '15', 'maxlength' => '20', 'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('Email', 'INPUT', array('name' => 'email', 'size' => '50', 'maxlength' => '100', 'class' => "text field medium"), 'required|valid_email', 'tOOL TIP', ''),
            array('Address 1', 'INPUT', array('name' => 'address_1', 'size' => '15', 'maxlength' => '100', 'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('Address 2', 'INPUT', array('name' => 'address_2', 'size' => '15', 'maxlength' => '100', 'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('City', 'INPUT', array('name' => 'city', 'size' => '20', 'maxlength' => '35', 'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('Province/State', 'INPUT', array('name' => 'province', 'size' => '15', 'maxlength' => '35', 'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('Zip/Postal Code', 'INPUT', array('name' => 'postal_code', 'size' => '15', 'maxlength' => '15', 'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('Country', 'country_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'country', 'countrycode', 'build_dropdown', '', ''),
            array('Timezone', 'timezone_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'gmtzone', 'timezone', 'build_dropdown', '', '')
        );
        $form['Billing Information'] = array(
            array('Rate Group', 'pricelist_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'name', 'pricelists', 'build_dropdown', 'where_arr', array("status" => "1","reseller_id" => "0")),
            array('Billing Schedule',array('name'=> 'sweep_id','class'=>'sweep_id'), 'SELECT', '', '', 'tOOL TIP', '', 'id', 'sweep', 'sweeplist', 'build_dropdown', '', ''),
            array('Billing Day',array("name"=>'invoice_day',"class"=>"invoice_day"), 'SELECT', '', '', 'tOOL TIP', '', '', '', '', 'set_invoice_option'),
            array('Currency', 'currency_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'currencyname,currency', 'currency', 'build_concat_dropdown', '', array()),
            array('Credit Limit', 'INPUT', array('name' => 'credit_limit', 'size' => '20', 'maxlength' => '15', 'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('Account Type', 'posttoexternal', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', '', '', '', 'set_account_type'));

        $form['Limit Credit Notification'] = array(
            array('Limit Credit Notification', 'INPUT', array('name' => 'notify_credit_limit', 'size' => '20', 'maxlength' => '15', 'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('Permited Notification By Email', 'notify_flag', 'SELECT', '', '', 'tOOL TIP', '', '', '', '', 'set_allow'),
            array('Email Notification', 'INPUT', array('name' => 'notify_email', 'size' => '50', 'maxlength' => '100', 'class' => "text field medium"), 'xss_clean|valid_email', 'tOOL TIP', ''),
        );        
        
        $form['button_cancel'] = array('name' => 'action', 'content' => 'Cancel', 'value' => 'cancel', 'type' => 'button', 'class' => 'ui-state-default float-right ui-corner-all ui-button', 'onclick' => 'return redirect_page(\'/accounts/customer_list/\')');
        $form['button_save'] = array('name' => 'action', 'content' => 'Save', 'value' => 'save', 'type' => 'submit', 'class' => 'ui-state-default float-right ui-corner-all ui-button');

        return $form;
    }

    function get_customer_callerid_fields() {
        $form['forms'] = array(base_url() . '/accounts/customer_add_callerid/', array("id" => "callerid_form"));
        $form['callerid'] = array(
            array('', 'HIDDEN', array('name' => 'flag'), '', '', '', ''),
            array('Account Number', 'INPUT', array('name' => 'accountid', 'size' => '20', 'readonly' => true, 'maxlength' => '15', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter account number'),
            array('Status', 'status', 'CHECKBOX', array('name' => 'status', 'value' => 'on', 'checked' => false), '', 'tOOL TIP', ''),
            array('Caller Id Name', 'INPUT', array('name' => 'callerid_name', 'size' => '20', 'maxlength' => '15', 'class' => "text field medium"), 'trim|required', 'tOOL TIP', ''),
            array('Caller Id Number', 'INPUT', array('name' => 'callerid_number', 'size' => '20', 'maxlength' => '15', 'class' => "text field medium"), 'trim|numeric', 'tOOL TIP', '')
        );
        $form['button_save'] = array('name' => 'action', 'content' => 'Save', 'value' => 'save', 'type' => 'submit', 'class' => 'ui-state-default float-right ui-corner-all ui-button');
        return $form;
    }

    function get_customer_payment_fields($currency, $number, $currency_id, $id) {
        $form['forms'] = array(base_url() . '/accounts/customer_payment_save/', array('id' => 'accounts_conf_form', 'method' => 'POST', 'name' => 'accounts_conf_form'));
        $form['Process Payment'] = array(
            array('', 'HIDDEN', array('name' => 'id', 'value' => $id), '', '', '', ''),
            array('', 'HIDDEN', array('name' => 'account_currency', 'value' => $currency_id), '', '', ''),
            array('Account ', 'INPUT', array('name' => 'accountid', 'size' => '20', 'value' => $number, 'readonly' => true, 'maxlength' => '15', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter account number'),
            array('Payment' , 'INPUT', array('name' => 'credit', 'size' => '20', 'maxlength' => '15', 'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('Cash', 'payment_type[]', 'RADIO', array('name' => 'payment_type[]', 'value' => '0', 'checked' => true), '', 'tOOL TIP', ''),
            array('Cheque', 'payment_type[]', 'RADIO', array('name' => 'payment_type[]', 'value' => '1', 'checked' => false), '', 'tOOL TIP', ''),
            array('Transfer', 'payment_type[]', 'RADIO', array('name' => 'payment_type[]', 'value' => '2', 'checked' => false), '', 'tOOL TIP', ''),
            array('Note:', 'TEXTAREA', array('name' => 'notes', 'size' => '20', 'class' => "text field medium"), '', 'tOOL TIP', '')
        );
        $form['button_save'] = array('name' => 'action', 'content' => 'Save', 'value' => 'save', 'type' => 'submit', 'class' => 'ui-state-default float-right ui-corner-all ui-button');
        return $form;
    }

    function get_form_reseller_fields($values = '') {
        $logintype = $this->CI->session->userdata('logintype');
        if ($logintype == 1 || $logintype == 5) {
            $account_data = $this->CI->session->userdata("accountinfo");
            $loginid = $account_data['id'];

        }else{
            $loginid = "0";
        }

        $uname = $this->CI->common->find_uniq_rendno($this->CI->config->item('size_number'), 'number', 'accounts');
        $form['forms'] = array(base_url() . '/accounts/reseller_save/', array("id" => "reseller_form", "name" => "reseller_form"));
        $form['Client Panel Access'] = array(
            array('', 'HIDDEN', array('name' => 'id'), '', '', '', ''),
            array('', 'HIDDEN', array('name' => 'type', 'value' => '1'), '', '', ''),
            array('Account Number', 'INPUT', array('name' => 'number', 'value' => $uname, 'size' => '20', 'readonly' => true, 'maxlength' => '15', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter account number'),
            array('Password', 'PASSWORD', array('name' => 'password', 'size' => '20', 'maxlength' => '20', 'class' => "text field medium"), 'trim|required|min_length[5]|max_length[20]|xss_clean', 'tOOL TIP', 'Please Enter Password'));

        $form['Billing Information'] = array(
            array('Rate Group', 'pricelist_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'name', 'pricelists', 'build_dropdown', 'where_arr', array("status" => "1","reseller_id" => "0")),
            array('Billing Schedule',array('name'=> 'sweep_id','class'=>'sweep_id'), 'SELECT', '', '', 'tOOL TIP', '', 'id', 'sweep', 'sweeplist', 'build_dropdown', '', ''),
            array('Billing Day',array("name"=>'invoice_day',"class"=>"invoice_day"), 'SELECT', '', '', 'tOOL TIP', '', '', '', '', 'set_invoice_option'),
             array('Currency', 'currency_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'currencyname,currency', 'currency', 'build_concat_dropdown', '', array()),
            array('Credit Limit', 'INPUT', array('name' => 'credit_limit', 'size' => '20', 'maxlength' => '15', 'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('Commission Rate in (%)', 'INPUT', array('name' => 'commission_rate', 'size' => '20', 'maxlength' => '5', 'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('Account Type', 'posttoexternal', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', '', '', '', 'set_account_type'));

        $form['Reseller Profile'] = array(
            array('Language', 'language_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'languagename', 'language', 'build_dropdown', '', ''),
            array('Company', 'INPUT', array('name' => 'company_name', 'maxlength' => '150', 'size' => '50', 'class' => 'text field medium'), '', 'tOOL TIP', ''),
            array('First Name', 'INPUT', array('name' => 'first_name', 'id' => 'first_name', 'size' => '50', 'maxlength' => '25', 'class' => "text field medium"), 'trim|required|max_length[20]|xss_clean', 'tOOL TIP', ''),
            array('Last Name', 'INPUT', array('name' => 'last_name', 'size' => '15', 'maxlength' => '50', 'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('Telephone 1', 'INPUT', array('name' => 'telephone_1', 'size' => '15', 'maxlength' => '20', 'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('Telephone 2', 'INPUT', array('name' => 'telephone_2', 'size' => '15', 'maxlength' => '20', 'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('Email', 'INPUT', array('name' => 'email', 'size' => '50', 'maxlength' => '100', 'class' => "text field medium"), 'required|valid_email', 'tOOL TIP', ''),
            array('Address 1', 'INPUT', array('name' => 'address_1', 'size' => '15', 'maxlength' => '100', 'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('Address 2', 'INPUT', array('name' => 'address_2', 'size' => '15', 'maxlength' => '100', 'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('City', 'INPUT', array('name' => 'city', 'size' => '20', 'maxlength' => '25', 'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('Province/State', 'INPUT', array('name' => 'province', 'size' => '15', 'maxlength' => '25', 'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('Zip/Postal Code', 'INPUT', array('name' => 'postal_code', 'size' => '15', 'maxlength' => '15', 'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('Country', 'country_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'country', 'countrycode', 'build_dropdown', '', ''),
            array('Timezone', 'timezone_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'gmtzone', 'timezone', 'build_dropdown', '', ''),
            array('Account Status', 'status', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', '', '', '', 'set_status'),
        );
        $form['Limit Credit Notification'] = array(
            array('Limit Credit Notification', 'INPUT', array('name' => 'notify_credit_limit', 'size' => '20', 'maxlength' => '15', 'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('Permited Notification By Email', 'notify_flag', 'SELECT', '', '', 'tOOL TIP', '', '', '', '', 'set_allow'),
            array('Email Notification', 'INPUT', array('name' => 'notify_email', 'size' => '50', 'maxlength' => '100', 'class' => "text field medium"), 'valid_email', 'tOOL TIP', ''),
        );        
        
        $form['button_cancel'] = array('name' => 'action', 'content' => 'Cancel', 'value' => 'cancel', 'type' => 'button', 'class' => 'ui-state-default float-right ui-corner-all ui-button', 'onclick' => 'return redirect_page(\'/accounts/resellers_list/\')');
        $form['button_save'] = array('name' => 'action', 'content' => 'Save', 'value' => 'save', 'type' => 'submit', 'class' => 'ui-state-default float-right ui-corner-all ui-button');

        return $form;
    }

    function get_form_provider_fields($values = '') {
        $uname = $this->CI->common->find_uniq_rendno($this->CI->config->item('size_number'), 'number', 'accounts');
        $form['forms'] = array(base_url() . '/accounts/provider_save/', array("id" => "provider_form", "name" => "provider_form"));
        $form['Client Panel Access'] = array(
            array('', 'HIDDEN', array('name' => 'id'), '', '', '', ''),
            array('', 'HIDDEN', array('name' => 'type', 'value' => '3'), '', '', ''),
            array('Account Number', 'INPUT', array('name' => 'number', 'value' => $uname, 'size' => '20', 'readonly' => true, 'maxlength' => '15', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter account number'),
            array('Password', 'PASSWORD', array('name' => 'password', 'size' => '20', 'maxlength' => '20', 'class' => "text field medium"), 'trim|required|min_length[5]|max_length[20]|xss_clean', 'tOOL TIP', 'Please Enter Password'));

        $form['Account & Billing Information'] = array(
            array('Account Status', 'status', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', '', '', '', 'set_status'),
            array('Billing Schedule',array('name'=> 'sweep_id','class'=>'sweep_id'), 'SELECT', '', '', 'tOOL TIP', '', 'id', 'sweep', 'sweeplist', 'build_dropdown', '', ''),
            array('Billing Day',array("name"=>'invoice_day',"class"=>"invoice_day"), 'SELECT', '', '', 'tOOL TIP', '', '', '', '', 'set_invoice_option'),
            array('Currency', 'currency_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'currencyname,currency', 'currency', 'build_concat_dropdown', '', array()),
        );

        $form['Provider Profile'] = array(
            array('Language', 'language_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'languagename', 'language', 'build_dropdown', '', ''),
            array('Company', 'INPUT', array('name' => 'company_name', 'maxlength' => '150', 'size' => '50', 'class' => 'text field medium'), '', 'tOOL TIP', ''),
            array('First Name', 'INPUT', array('name' => 'first_name', 'id' => 'first_name', 'size' => '25', 'maxlength' => '50', 'class' => "text field medium"), 'trim|required|max_length[20]|xss_clean', 'tOOL TIP', ''),
            array('Last Name', 'INPUT', array('name' => 'last_name', 'size' => '15', 'maxlength' => '50', 'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('Telephone 1', 'INPUT', array('name' => 'telephone_1', 'size' => '15', 'maxlength' => '20', 'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('Telephone 2', 'INPUT', array('name' => 'telephone_2', 'size' => '15', 'maxlength' => '20', 'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('Email', 'INPUT', array('name' => 'email', 'size' => '50', 'maxlength' => '100', 'class' => "text field medium"), 'required|valid_email', 'tOOL TIP', ''),
            array('Address 1', 'INPUT', array('name' => 'address_1', 'size' => '15', 'maxlength' => '100', 'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('Address 2', 'INPUT', array('name' => 'address_2', 'size' => '15', 'maxlength' => '100', 'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('City', 'INPUT', array('name' => 'city', 'size' => '20', 'maxlength' => '25', 'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('Province/State', 'INPUT', array('name' => 'province', 'size' => '15', 'maxlength' => '25', 'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('Zip/Postal Code', 'INPUT', array('name' => 'postal_code', 'size' => '15', 'maxlength' => '15', 'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('Country', 'country_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'country', 'countrycode', 'build_dropdown', '', ''),
            array('Timezone', 'timezone_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'gmtzone', 'timezone', 'build_dropdown', '', '')
        );

        $form['button_cancel'] = array('name' => 'action', 'content' => 'Cancel', 'value' => 'cancel', 'type' => 'button', 'class' => 'ui-state-default float-right ui-corner-all ui-button', 'onclick' => 'return redirect_page(\'/accounts/provider_list/\')');
        $form['button_save'] = array('name' => 'action', 'content' => 'Save', 'value' => 'save', 'type' => 'submit', 'class' => 'ui-state-default float-right ui-corner-all ui-button');


        return $form;
    }

    function get_user_form_fields() {

        $uname = $this->CI->common->find_uniq_rendno($this->CI->config->item('size_number'), 'number', 'accounts');
        $form['forms'] = array(base_url() . '/user/user_edit_account/', array("id" => "user_form", "name" => "user_form"));

        $form['User Profile'] = array(
            array('', 'HIDDEN', array('name' => 'id'), '', '', '', ''),
            array('', 'HIDDEN', array('name' => 'type', 'value' => '0'), '', '', ''),
            array('Account Number', 'INPUT', array('name' => 'number', 'value' => $uname, 'size' => '20', 'readonly' => true, 'maxlength' => '15', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter account number'),
            array('Password', 'PASSWORD', array('name' => 'password', 'size' => '20', 'maxlength' => '20', 'class' => "text field medium"), 'trim|required|min_length[5]|max_length[20]|xss_clean', 'tOOL TIP', 'Please Enter Password'),
            array('Language', 'language_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'languagename', 'language', 'build_dropdown', '', ''),
            array('Company', 'INPUT', array('name' => 'company_name', 'maxlength' => '150', 'size' => '15', 'class' => 'text field medium'), '', 'tOOL TIP', ''),
            array('First Name', 'INPUT', array('name' => 'first_name', 'id' => 'first_name', 'size' => '15', 'maxlength' => '50', 'class' => "text field medium"), 'trim|required|max_length[20]|xss_clean', 'tOOL TIP', 'Please Enter account number'),
            array('Last Name', 'INPUT', array('name' => 'last_name', 'size' => '15', 'maxlength' => '50', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter Password'),
            array('Telephone 1', 'INPUT', array('name' => 'telephone_1', 'size' => '15', 'maxlength' => '20', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter Password'),
            array('Telephone 2', 'INPUT', array('name' => 'telephone_2', 'size' => '15', 'maxlength' => '20', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter Password'),
            array('Email', 'INPUT', array('name' => 'email', 'size' => '50', 'maxlength' => '100', 'class' => "text field medium"), 'required|valid_email', 'tOOL TIP', 'Please Enter Password'),
            array('Address 1', 'INPUT', array('name' => 'address_1', 'size' => '15', 'maxlength' => '100', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter Password'),
            array('Address 2', 'INPUT', array('name' => 'address_2', 'size' => '15', 'maxlength' => '100', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter Password'),
            array('City', 'INPUT', array('name' => 'city', 'size' => '20', 'maxlength' => '25', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter Password'),
            array('Province/State', 'INPUT', array('name' => 'province', 'size' => '15', 'maxlength' => '25', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter Password'),
            array('Zip/Postal Code', 'INPUT', array('name' => 'postal_code', 'size' => '15', 'maxlength' => '15', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter Password'),
            array('Country', 'country_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'country', 'countrycode', 'build_dropdown', '', ''),
            array('Timezone', 'timezone_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'gmtzone', 'timezone', 'build_dropdown', '', '')
        );
        $form['Limit Credit Notification'] = array(
            array('Limit Credit Notification', 'INPUT', array('name' => 'notify_credit_limit', 'size' => '20', 'maxlength' => '15', 'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('Permited Notification By Email', 'notify_flag', 'SELECT', '', '', 'tOOL TIP', '', '', '', '', 'set_allow'),
            array('Email Notification', 'INPUT', array('name' => 'notify_email', 'size' => '50', 'maxlength' => '100', 'class' => "text field medium"), 'valid_email', 'tOOL TIP', ''),
        );        
        
        $form['button_cancel'] = array('name' => 'action', 'content' => 'Cancel', 'value' => 'cancel', 'type' => 'button', 'class' => 'ui-state-default float-right ui-corner-all ui-button', 'onclick' => 'return redirect_page(\'/user/user/\')');
        $form['button_save'] = array('name' => 'action', 'content' => 'Save', 'value' => 'save', 'type' => 'submit', 'class' => 'ui-state-default float-right ui-corner-all ui-button');

        return $form;
    }

    function get_reseller_own_form_fields() {

        $uname = $this->CI->common->find_uniq_rendno($this->CI->config->item('size_number'), 'number', 'accounts');
        $form['forms'] = array(base_url() . '/accounts/reseller_edit_account/', array("id" => "user_form", "name" => "user_form"));

        $form['Reseller Profile'] = array(
            array('', 'HIDDEN', array('name' => 'id'), '', '', '', ''),
            array('', 'HIDDEN', array('name' => 'type', 'value' => '0'), '', '', ''),
            array('Account Number', 'INPUT', array('name' => 'number', 'value' => $uname, 'size' => '20', 'readonly' => true, 'maxlength' => '15', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter account number'),
            array('Password', 'PASSWORD', array('name' => 'password', 'size' => '20', 'maxlength' => '20', 'class' => "text field medium"), 'trim|required|min_length[5]|max_length[20]|xss_clean', 'tOOL TIP', 'Please Enter Password'),
            array('Language', 'language_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'languagename', 'language', 'build_dropdown', '', ''),
            array('Company', 'INPUT', array('name' => 'company_name', 'maxlength' => '150', 'size' => '15', 'class' => 'text field medium'), '', 'tOOL TIP', ''),
            array('First Name', 'INPUT', array('name' => 'first_name', 'id' => 'first_name', 'size' => '15', 'maxlength' => '50', 'class' => "text field medium"), 'trim|required|max_length[20]|xss_clean', 'tOOL TIP', 'Please Enter account number'),
            array('Last Name', 'INPUT', array('name' => 'last_name', 'size' => '15', 'maxlength' => '50', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter Password'),
            array('Telephone 1', 'INPUT', array('name' => 'telephone_1', 'size' => '15', 'maxlength' => '20', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter Password'),
            array('Telephone 2', 'INPUT', array('name' => 'telephone_2', 'size' => '15', 'maxlength' => '20', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter Password'),
            array('Email', 'INPUT', array('name' => 'email', 'size' => '50', 'maxlength' => '100', 'class' => "text field medium"), 'required|valid_email', 'tOOL TIP', 'Please Enter Password'),
            array('Address 1', 'INPUT', array('name' => 'address_1', 'size' => '15', 'maxlength' => '100', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter Password'),
            array('Address 2', 'INPUT', array('name' => 'address_2', 'size' => '15', 'maxlength' => '100', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter Password'),
            array('City', 'INPUT', array('name' => 'city', 'size' => '20', 'maxlength' => '25', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter Password'),
            array('Province/State', 'INPUT', array('name' => 'province', 'size' => '15', 'maxlength' => '25', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter Password'),
            array('Zip/Postal Code', 'INPUT', array('name' => 'postal_code', 'size' => '15', 'maxlength' => '15', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter Password'),
            array('Country', 'country_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'country', 'countrycode', 'build_dropdown', '', ''),
            array('Timezone', 'timezone_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'gmtzone', 'timezone', 'build_dropdown', '', '')
        );
        $form['Limit Credit Notification'] = array(
            array('Limit Credit Notification', 'INPUT', array('name' => 'notify_credit_limit', 'size' => '20', 'maxlength' => '15', 'class' => "text field medium"), '', 'tOOL TIP', ''),
            array('Permited Notification By Email', 'notify_flag', 'SELECT', '', '', 'tOOL TIP', '', '', '', '', 'set_allow'),
            array('Email Notification', 'INPUT', array('name' => 'notify_email', 'size' => '50', 'maxlength' => '100', 'class' => "text field medium"), 'valid_email', 'tOOL TIP', ''),
        );        
        
        $form['button_cancel'] = array('name' => 'action', 'content' => 'Cancel', 'value' => 'cancel', 'type' => 'button', 'class' => 'ui-state-default float-right ui-corner-all ui-button', 'onclick' => 'return redirect_page(\'/dashboard/\')');
        $form['button_save'] = array('name' => 'action', 'content' => 'Save', 'value' => 'save', 'type' => 'submit', 'class' => 'ui-state-default float-right ui-corner-all ui-button');

        return $form;
    }

    
    function get_form_admin_fields($values = '') {
        $uname = $this->CI->common->find_uniq_rendno($this->CI->config->item('size_number'), 'number', 'accounts');
        $form['forms'] = array(base_url() . '/accounts/admin_save/', array("id" => "admin_form", "name" => "admin_form"));
        $form['Client Panel Access'] = array(
            array('', 'HIDDEN', array('name' => 'id'), '', '', '', ''),
            array('', 'HIDDEN', array('name' => 'type', 'value' => '2'), '', '', ''),
            array('Account Number', 'INPUT', array('name' => 'number', 'value' => $uname, 'size' => '20', 'readonly' => true, 'maxlength' => '15', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter account number'),
            array('Password', 'PASSWORD', array('name' => 'password', 'size' => '20', 'maxlength' => '20', 'class' => "text field medium"), 'trim|required|min_length[5]|max_length[20]|xss_clean', 'tOOL TIP', 'Please Enter Password'));
        $form['Admin Profile'] = array(
            array('Language', 'language_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'languagename', 'language', 'build_dropdown', '', ''),
            array('Company', 'INPUT', array('name' => 'company_name', 'maxlength' => '150', 'size' => '15', 'class' => 'text field medium'), '', 'tOOL TIP', ''),
            array('First Name', 'INPUT', array('name' => 'first_name', 'id' => 'first_name', 'size' => '15', 'maxlength' => '50', 'class' => "text field medium"), 'trim|required|max_length[20]|xss_clean', 'tOOL TIP', 'Please Enter account number'),
            array('Last Name', 'INPUT', array('name' => 'last_name', 'size' => '15', 'maxlength' => '50', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter Password'),
            array('Telephone 1', 'INPUT', array('name' => 'telephone_1', 'size' => '15', 'maxlength' => '25', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter Password'),
            array('Telephone 2', 'INPUT', array('name' => 'telephone_2', 'size' => '15', 'maxlength' => '25', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter Password'),
            array('Email', 'INPUT', array('name' => 'email', 'size' => '50', 'maxlength' => '50', 'class' => "text field medium"), 'required|valid_email', 'tOOL TIP', 'Please Enter Password'),
            array('Address 1', 'INPUT', array('name' => 'address_1', 'size' => '15', 'maxlength' => '100', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter Password'),
            array('Address 2', 'INPUT', array('name' => 'address_2', 'size' => '15', 'maxlength' => '100', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter Password'),
            array('City', 'INPUT', array('name' => 'city', 'size' => '20', 'maxlength' => '25', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter Password'),
            array('Province/State', 'INPUT', array('name' => 'province', 'size' => '15', 'maxlength' => '25', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter Password'),
            array('Zip/Postal Code', 'INPUT', array('name' => 'postal_code', 'size' => '15', 'maxlength' => '15', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter Password'),
            array('Country', 'country_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'country', 'countrycode', 'build_dropdown', '', ''),
            array('Timezone', 'timezone_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'gmtzone', 'timezone', 'build_dropdown', '', ''),
            array('Account Status', 'status', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', '', '', '', 'set_status'),
             array('Currency', 'currency_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'currencyname,currency', 'currency', 'build_concat_dropdown', '', array()),
        );

        $form['button_cancel'] = array('name' => 'action', 'content' => 'Cancel', 'value' => 'cancel', 'type' => 'button', 'class' => 'ui-state-default float-right ui-corner-all ui-button', 'onclick' => 'return redirect_page(\'/accounts/admin_list/\')');
        $form['button_save'] = array('name' => 'action', 'content' => 'Save', 'value' => 'save', 'type' => 'submit', 'class' => 'ui-state-default float-right ui-corner-all ui-button');

        return $form;
    }

    function get_form_subadmin_fields($values = '') {
        $uname = $this->CI->common->find_uniq_rendno($this->CI->config->item('size_number'), 'number', 'accounts');
        $form['forms'] = array(base_url() . '/accounts/subadmin_save/', array("id" => "subadmin_form", "name" => "subadmin_form"));
        $form['Client Panel Access'] = array(
            array('', 'HIDDEN', array('name' => 'id'), '', '', '', ''),
            array('', 'HIDDEN', array('name' => 'type', 'value' => '4'), '', '', ''),
            array('Account Number', 'INPUT', array('name' => 'number', 'value' => $uname, 'size' => '20', 'readonly' => true, 'maxlength' => '15', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter account number'),
            array('Password', 'PASSWORD', array('name' => 'password', 'size' => '20', 'maxlength' => '20', 'class' => "text field medium"), 'trim|required|min_length[5]|max_length[20]|xss_clean', 'tOOL TIP', 'Please Enter Password'));
        $form['Subadmin Profile'] = array(
            array('Language', 'language_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'languagename', 'language', 'build_dropdown', '', ''),
            array('Company', 'INPUT', array('name' => 'company_name', 'maxlength' => '150', 'size' => '15', 'class' => 'text field medium'), '', 'tOOL TIP', ''),
            array('First Name', 'INPUT', array('name' => 'first_name', 'id' => 'first_name', 'size' => '25', 'maxlength' => '50', 'class' => "text field medium"), 'trim|required|max_length[20]|xss_clean', 'tOOL TIP', 'Please Enter account number'),
            array('Last Name', 'INPUT', array('name' => 'last_name', 'size' => '15', 'maxlength' => '50', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter Password'),
            array('Telephone 1', 'INPUT', array('name' => 'telephone_1', 'size' => '15', 'maxlength' => '25', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter Password'),
            array('Telephone 2', 'INPUT', array('name' => 'telephone_2', 'size' => '15', 'maxlength' => '25', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter Password'),
            array('Email', 'INPUT', array('name' => 'email', 'size' => '50', 'maxlength' => '100', 'class' => "text field medium"), 'required|valid_email', 'tOOL TIP', 'Please Enter Password'),
            array('Address 1', 'INPUT', array('name' => 'address_1', 'size' => '15', 'maxlength' => '100', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter Password'),
            array('Address 2', 'INPUT', array('name' => 'address_2', 'size' => '15', 'maxlength' => '100', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter Password'),
            array('City', 'INPUT', array('name' => 'city', 'size' => '20', 'maxlength' => '25', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter Password'),
            array('Province/State', 'INPUT', array('name' => 'province', 'size' => '15', 'maxlength' => '35', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter Password'),
            array('Zip/Postal Code', 'INPUT', array('name' => 'postal_code', 'size' => '15', 'maxlength' => '35', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter Password'),
            array('Country', 'country_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'country', 'countrycode', 'build_dropdown', '', ''),
            array('Timezone', 'timezone_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'gmtzone', 'timezone', 'build_dropdown', '', ''),
            array('Account Status', 'status', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', '', '', '', 'set_status'),
         array('Currency', 'currency_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'currencyname,currency', 'currency', 'build_concat_dropdown', '', array()),
        );

        $form['button_cancel'] = array('name' => 'action', 'content' => 'Cancel', 'value' => 'cancel', 'type' => 'button', 'class' => 'ui-state-default float-right ui-corner-all ui-button', 'onclick' => 'return redirect_page(\'/accounts/subadmin_list/\')');
        $form['button_save'] = array('name' => 'action', 'content' => 'Save', 'value' => 'save', 'type' => 'submit', 'class' => 'ui-state-default float-right ui-corner-all ui-button');

        return $form;
    }

    function get_form_callshop_fields($values = '') {
        $uname = $this->CI->common->find_uniq_rendno($this->CI->config->item('size_number'), 'number', 'accounts');
        $form['forms'] = array(base_url() . '/accounts/callshop_save/', array("id" => "callshop_form", "name" => "callshop_form"));
        $form['Client Panel Access'] = array(
            array('', 'HIDDEN', array('name' => 'id'), '', '', '', ''),
            array('', 'HIDDEN', array('name' => 'type', 'value' => '5'), '', '', ''),
            array('Account Number', 'INPUT', array('name' => 'number', 'value' => $uname, 'size' => '20', 'readonly' => true, 'maxlength' => '15', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter account number'),
            array('Password', 'PASSWORD', array('name' => 'password', 'size' => '20', 'maxlength' => '20', 'class' => "text field medium"), 'trim|required|min_length[5]|max_length[20]|xss_clean', 'tOOL TIP', 'Please Enter Password'));
        $form['Acconunt & Billing Information'] = array(
            array('Account Status', 'status', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', '', '', '', 'set_status'),
            array('Billing Schedule',array('name'=> 'sweep_id','class'=>'sweep_id'), 'SELECT', '', '', 'tOOL TIP', '', 'id', 'sweep', 'sweeplist', 'build_dropdown', '', ''),
            array('Billing Day',array("name"=>'invoice_day',"class"=>"invoice_day"), 'SELECT', '', '', 'tOOL TIP', '', '', '', '', 'set_invoice_option'),
            array('Credit Limit', 'INPUT', array('name' => 'credit_limit', 'size' => '20', 'maxlength' => '15', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter account number'),
             array('Currency', 'currency_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'currencyname,currency', 'currency', 'build_concat_dropdown', '', array()),
        );
        $form['Call shop Profile'] = array(
            array('Language', 'language_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'languagename', 'language', 'build_dropdown', '', ''),
            array('Company', 'INPUT', array('name' => 'company_name', 'maxlength' => '150', 'size' => '15', 'class' => 'text field medium'), '', 'tOOL TIP', ''),
            array('First Name', 'INPUT', array('name' => 'first_name', 'id' => 'first_name', 'size' => '15', 'maxlength' => '50', 'class' => "text field medium"), 'trim|required|max_length[20]|xss_clean', 'tOOL TIP', 'Please Enter account number'),
            array('Last Name', 'INPUT', array('name' => 'last_name', 'size' => '15', 'maxlength' => '50', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter Password'),
            array('Telephone 1', 'INPUT', array('name' => 'telephone_1', 'id' => 'telephone_1', 'size' => '15', 'maxlength' => '20', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter Password'),
            array('Telephone 2', 'INPUT', array('name' => 'telephone_2', 'id' => 'telephone_2', 'size' => '15', 'maxlength' => '20', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter Password'),
            array('Email', 'INPUT', array('name' => 'email', 'size' => '50', 'maxlength' => '100', 'class' => "text field medium"), 'required|valid_email', 'tOOL TIP', 'Please Enter Password'),
            array('Address 1', 'INPUT', array('name' => 'address_1', 'size' => '15', 'maxlength' => '100', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter Password'),
            array('Address 2', 'INPUT', array('name' => 'address_2', 'size' => '15', 'maxlength' => '100', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter Password'),
            array('City', 'INPUT', array('name' => 'city', 'size' => '20', 'maxlength' => '25', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter Password'),
            array('Province/State', 'INPUT', array('name' => 'province', 'size' => '25', 'maxlength' => '15', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter Password'),
            array('Zip/Postal Code', 'INPUT', array('name' => 'postal_code', 'size' => '15', 'maxlength' => '15', 'class' => "text field medium"), '', 'tOOL TIP', 'Please Enter Password'),
            array('Country', 'country_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'country', 'countrycode', 'build_dropdown', '', ''),
            array('Timezone', 'timezone_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'gmtzone', 'timezone', 'build_dropdown', '', '')
        );

        $form['button_cancel'] = array('name' => 'action', 'content' => 'Cancel', 'value' => 'cancel', 'type' => 'button', 'class' => 'ui-state-default float-right ui-corner-all ui-button', 'onclick' => 'return redirect_page(\'/accounts/callshop_list/\')');
        $form['button_save'] = array('name' => 'action', 'content' => 'Save', 'value' => 'save', 'type' => 'submit', 'class' => 'ui-state-default float-right ui-corner-all ui-button');

        return $form;
    }

    function get_search_customer_form() {
        $form['forms'] = array("", array('id' => "account_search"));
        $form['Search Account'] = array(
            array('', 'HIDDEN', 'ajax_search', '1', '', '', ''),
            array('', 'HIDDEN', 'advance_search', '1', '', '', ''),
            array('Account Number', 'INPUT', array('name' => 'number[number]', '', 'size' => '20', 'maxlength' => '20', 'class' => "text field "), '', 'tOOL TIP', '1', 'number[number-string]', '', '', '', 'search_string_type', ''),
            array('Rate Group', 'pricelist_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'name', 'pricelists', 'build_dropdown', 'where_arr', array("status" => "1","reseller_id" => "0")),
            array('First Name', 'INPUT', array('name' => 'first_name[first_name]', '', 'id' => 'first_name', 'size' => '15', 'maxlength' => '20', 'class' => "text field "), '', 'tOOL TIP', '1', 'first_name[first_name-string]', '', '', '', 'search_string_type', ''),
            array('Last Name', 'INPUT', array('name' => 'last_name[last_name]', 'value' => '', 'size' => '20', 'maxlength' => '25', 'class' => "text field "), '', 'Tool tips info', '1', 'last_name[last_name-string]', '', '', '', 'search_string_type', ''),
            array('Company', 'INPUT', array('name' => 'company_name[company_name]', 'value' => '', 'size' => '20', 'maxlength' => '100', 'class' => "text field "), '', 'Tool tips info', '1', 'company_name[company_name-string]', '', '', '', 'search_string_type', ''),
            array('Balance', 'INPUT', array('name' => 'balance[balance]', 'value' => '', 'size' => '20', 'maxlength' => '15', 'class' => "text field "), '', 'Tool tips info', '1', 'balance[balance-integer]', '', '', '', 'search_int_type', ''),
            array('Credit Limit', 'INPUT', array('name' => 'credit_limit[credit_limit]', 'value' => '', 'size' => '20', 'maxlength' => '20', 'class' => "text field "), '', 'Tool tips info', '1', 'credit_limit[credit_limit-integer]', '', '', '', 'search_int_type', ''),
            array('Billing Cycle', 'sweep_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'sweep', 'sweeplist', 'build_dropdown', '', ''),
            array('Country', 'country_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'country', 'countrycode', 'build_dropdown', '', ''),
            array('Currency', 'currency_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'currencyname,currency', 'currency', 'build_concat_dropdown', '', array()),
        );

        $form['button_search'] = array('name' => 'action', 'id' => "account_search_btn", 'content' => 'Search', 'value' => 'save', 'type' => 'button', 'class' => 'ui-state-default float-right ui-corner-all ui-button');
        $form['button_reset'] = array('name' => 'action', 'id' => "id_reset", 'content' => 'Clear Search Filter', 'value' => 'cancel', 'type' => 'reset', 'class' => 'ui-state-default float-right ui-corner-all ui-button');

        return $form;
    }

    function get_provider_search_form() {
        $form['forms'] = array("", array('id' => "account_search"));
        $form['Search Account'] = array(
            array('', 'HIDDEN', 'ajax_search', '1', '', '', ''),
            array('', 'HIDDEN', 'advance_search', '1', '', '', ''),
            array('Account Number', 'INPUT', array('name' => 'number[number]', '', 'size' => '20', 'maxlength' => '15', 'class' => "text field "), '', 'tOOL TIP', '1', 'number[number-string]', '', '', '', 'search_string_type', ''),
            array('First Name', 'INPUT', array('name' => 'first_name[first_name]', '', 'id' => 'first_name', 'size' => '15', 'maxlength' => '25', 'class' => "text field "), '', 'tOOL TIP', '1', 'first_name[first_name-string]', '', '', '', 'search_string_type', ''),
            array('Last Name', 'INPUT', array('name' => 'last_name[last_name]', 'value' => '', 'size' => '20', 'maxlength' => '25', 'class' => "text field "), '', 'Tool tips info', '1', 'last_name[last_name-string]', '', '', '', 'search_string_type', ''),
            array('Company', 'INPUT', array('name' => 'company_name[company_name]', 'value' => '', 'size' => '20', 'maxlength' => '100', 'class' => "text field "), '', 'Tool tips info', '1', 'company_name[company_name-string]', '', '', '', 'search_string_type', ''),
            array('Country', 'country_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'country', 'countrycode', 'build_dropdown', '', ''),
            array('Currency', 'currency_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'currencyname,currency', 'currency', 'build_concat_dropdown', '', array()),
        );
        $form['button_search'] = array('name' => 'action', 'id' => "account_search_btn", 'content' => 'Search', 'value' => 'save', 'type' => 'button', 'class' => 'ui-state-default float-right ui-corner-all ui-button');
        $form['button_reset'] = array('name' => 'action', 'id' => "id_reset", 'content' => 'Clear Search Filter', 'value' => 'cancel', 'type' => 'reset', 'class' => 'ui-state-default float-right ui-corner-all ui-button');
        return $form;
    }

    function get_reseller_search_form() {
        $form['forms'] = array("", array('id' => "account_search"));
        $form['Search Account'] = array(
            array('', 'HIDDEN', 'ajax_search', '1', '', '', ''),
            array('', 'HIDDEN', 'advance_search', '1', '', '', ''),
            array('Account Number', 'INPUT', array('name' => 'number[number]', '', 'size' => '20', 'maxlength' => '15', 'class' => "text field "), '', 'tOOL TIP', '1', 'number[number-string]', '', '', '', 'search_string_type', ''),
            array('Rate Group', 'pricelist_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'name', 'pricelists', 'build_dropdown', 'where_arr', array("status" => "1","reseller_id" => "0")),
            array('First Name', 'INPUT', array('name' => 'first_name[first_name]', '', 'id' => 'first_name', 'size' => '15', 'maxlength' => '15', 'class' => "text field "), '', 'tOOL TIP', '1', 'first_name[first_name-string]', '', '', '', 'search_string_type', ''),
            array('Last Name', 'INPUT', array('name' => 'last_name[last_name]', 'value' => '', 'size' => '20', 'maxlength' => '15', 'class' => "text field "), '', 'Tool tips info', '1', 'last_name[last_name-string]', '', '', '', 'search_string_type', ''),
            array('Company', 'INPUT', array('name' => 'company_name[company_name]', 'value' => '', 'size' => '20', 'maxlength' => '100', 'class' => "text field "), '', 'Tool tips info', '1', 'company_name[company_name-string]', '', '', '', 'search_string_type', ''),
            array('Country', 'country_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'country', 'countrycode', 'build_dropdown', '', ''),
           array('Currency', 'currency_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'currencyname,currency', 'currency', 'build_concat_dropdown', '', array()),
        );

        $form['button_search'] = array('name' => 'action', 'id' => "account_search_btn", 'content' => 'Search', 'value' => 'save', 'type' => 'button', 'class' => 'ui-state-default float-right ui-corner-all ui-button');
        $form['button_reset'] = array('name' => 'action', 'id' => "id_reset", 'content' => 'Clear Search Filter', 'value' => 'cancel', 'type' => 'reset', 'class' => 'ui-state-default float-right ui-corner-all ui-button');

        return $form;
    }

    function get_admin_search_form() {
        $form['forms'] = array("", array('id' => "account_search"));
        $form['Search Account'] = array(
            array('', 'HIDDEN', 'ajax_search', '1', '', '', ''),
            array('', 'HIDDEN', 'advance_search', '1', '', '', ''),
            array('Account Number', 'INPUT', array('name' => 'number[number]', '', 'size' => '20', 'maxlength' => '15', 'class' => "text field"), '', 'tOOL TIP', '1', 'number[number-string]', '', '', '', 'search_string_type', ''),
            array('First Name', 'INPUT', array('name' => 'first_name[first_name]', '', 'id' => 'first_name', 'size' => '15', 'maxlength' => '15', 'class' => "text field "), '', 'tOOL TIP', '1', 'first_name[first_name-string]', '', '', '', 'search_string_type', ''),
            array('Last Name', 'INPUT', array('name' => 'last_name[last_name]', 'value' => '', 'size' => '20', 'maxlength' => '25', 'class' => "text field "), '', 'Tool tips info', '1', 'last_name[last_name-string]', '', '', '', 'search_string_type', ''),
            array('Company', 'INPUT', array('name' => 'company_name[company_name]', 'value' => '', 'size' => '20', 'maxlength' => '100', 'class' => "text field "), '', 'Tool tips info', '1', 'company_name[company_name-string]', '', '', '', 'search_string_type', ''),
            array('Country', 'country_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'country', 'countrycode', 'build_dropdown', '', ''),
            array('Currency', 'currency_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'currencyname,currency', 'currency', 'build_concat_dropdown', '', array()),
        );

        $form['button_search'] = array('name' => 'action', 'id' => "account_search_btn", 'content' => 'Search', 'value' => 'save', 'type' => 'button', 'class' => 'ui-state-default float-right ui-corner-all ui-button');
        $form['button_reset'] = array('name' => 'action', 'id' => "id_reset", 'content' => 'Clear Search Filter', 'value' => 'cancel', 'type' => 'reset', 'class' => 'ui-state-default float-right ui-corner-all ui-button');

        return $form;
    }

    function get_subadmin_search_form() {
        $form['forms'] = array("", array('id' => "account_search"));
        $form['Search Account'] = array(
            array('', 'HIDDEN', 'ajax_search', '1', '', '', ''),
            array('', 'HIDDEN', 'advance_search', '1', '', '', ''),
            array('Account Number', 'INPUT', array('name' => 'number[number]', '', 'size' => '20', 'maxlength' => '15', 'class' => "text field "), '', 'tOOL TIP', '1', 'number[number-string]', '', '', '', 'search_string_type', ''),
            array('First Name', 'INPUT', array('name' => 'first_name[first_name]', '', 'id' => 'first_name', 'size' => '15', 'maxlength' => '25', 'class' => "text field "), '', 'tOOL TIP', '1', 'first_name[first_name-string]', '', '', '', 'search_string_type', ''),
            array('Last Name', 'INPUT', array('name' => 'last_name[last_name]', 'value' => '', 'size' => '20', 'maxlength' => '25', 'class' => "text field "), '', 'Tool tips info', '1', 'last_name[last_name-string]', '', '', '', 'search_string_type', ''),
            array('Company', 'INPUT', array('name' => 'company_name[company_name]', 'value' => '', 'size' => '20', 'maxlength' => '100', 'class' => "text field "), '', 'Tool tips info', '1', 'company_name[company_name-string]', '', '', '', 'search_string_type', ''),
            array('Country', 'country_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'country', 'countrycode', 'build_dropdown', '', ''),
             array('Currency', 'currency_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'currencyname,currency', 'currency', 'build_concat_dropdown', '', array()),
        );

        $form['button_search'] = array('name' => 'action', 'id' => "account_search_btn", 'content' => 'Search', 'value' => 'save', 'type' => 'button', 'class' => 'ui-state-default float-right ui-corner-all ui-button');
        $form['button_reset'] = array('name' => 'action', 'id' => "id_reset", 'content' => 'Clear Search Filter', 'value' => 'cancel', 'type' => 'reset', 'class' => 'ui-state-default float-right ui-corner-all ui-button');

        return $form;
    }

    function get_callshop_search_form() {
        $form['forms'] = array("", array('id' => "account_search"));
        $form['Search Account'] = array(
            array('', 'HIDDEN', 'ajax_search', '1', '', '', ''),
            array('', 'HIDDEN', 'advance_search', '1', '', '', ''),
            array('Account Number', 'INPUT', array('name' => 'number[number]', '', 'size' => '20', 'maxlength' => '15', 'class' => "text field "), '', 'tOOL TIP', '1', 'number[number-string]', '', '', '', 'search_string_type', ''),
            array('First Name', 'INPUT', array('name' => 'first_name[first_name]', '', 'id' => 'first_name', 'size' => '15', 'maxlength' => '25', 'class' => "text field "), '', 'tOOL TIP', '1', 'first_name[first_name-string]', '', '', '', 'search_string_type', ''),
            array('Last Name', 'INPUT', array('name' => 'last_name[last_name]', 'value' => '', 'size' => '20', 'maxlength' => '25', 'class' => "text field "), '', 'Tool tips info', '1', 'last_name[last_name-string]', '', '', '', 'search_string_type', ''),
            array('Credit Limit', 'INPUT', array('name' => 'credit_limit[credit_limit]', 'value' => '', 'size' => '20', 'maxlength' => '15', 'class' => "text field "), '', 'Tool tips info', '1', 'credit_limit[credit_limit-integer]', '', '', '', 'search_int_type', ''),
            array('Company', 'INPUT', array('name' => 'company_name[company_name]', 'value' => '', 'size' => '20', 'maxlength' => '100', 'class' => "text field "), '', 'Tool tips info', '1', 'company_name[company_name-string]', '', '', '', 'search_string_type', ''),
            array('Billing Cycle', 'sweep_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'sweep', 'sweeplist', 'build_dropdown', '', ''),
            array('Country', 'country_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'country', 'countrycode', 'build_dropdown', '', ''),
            array('Currency', 'currency_id', 'SELECT', '', '', 'tOOL TIP', 'Please Enter account number', 'id', 'currencyname,currency', 'currency', 'build_concat_dropdown', '', array()),
        );

        $form['button_search'] = array('name' => 'action', 'id' => "account_search_btn", 'content' => 'Search', 'value' => 'save', 'type' => 'button', 'class' => 'ui-state-default float-right ui-corner-all ui-button');
        $form['button_reset'] = array('name' => 'action', 'id' => "id_reset", 'content' => 'Clear Search Filter', 'value' => 'cancel', 'type' => 'reset', 'class' => 'ui-state-default float-right ui-corner-all ui-button');

        return $form;
    }

    function build_account_list_for_admin() {
        // array(display name, width, db_field_parent_table,feidname, db_field_child_table,function name);
        $grid_field_arr = json_encode(array(array("<input type='checkbox' name='chkAll' class='checkall'/>", "30", "", "", "", ""),
            array("Account Number", "220", "number", "", "", ""),
            array("First Name", "210", "first_name", "", "", ""),
            array("Last Name", "210", "last_name", "", "", ""),
            array("Company", "200", "company_name", "", "", ""),
            array("Account Status", "195", "status", "status", "status", "get_status"),
            array("Action", "90", "", "", "", array(
                    "VIEW" => array("url" => "accounts/admin_edit//", "mode" => "single"),
                    "TAXES" => array("url" => "accounts/customer_account_taxes/edit/", "mode" => "single"),
                    "DELETE" => array("url" => "accounts/admin_delete/", "mode" => "single")
            ))
                ));
        return $grid_field_arr;
    }

    function build_account_list_for_customer() {
        // array(display name, width, db_field_parent_table,feidname, db_field_child_table,function name);
        $grid_field_arr = json_encode(array(array("<input type='checkbox' name='chkAll' class='checkall'/>", "30", "", "", "", ""),
            array("Account Number", "100", "number", "", "", ""),
            array("Rate Group", "100", "pricelist_id", "name", "pricelists", "get_field_name"),
            array("First Name", "130", "first_name", "", "", ""),
            array("Last Name", "130", "last_name", "", "", ""),
            array("Company", "197", "company_name", "", "", ""),
            array("Balance", "70", "balance", "balance", "balance", "get_account_balance"),
            array("Credit Limit", "70", "credit_limit", "credit_limit", "credit_limit", "convert_to_currency"),
            array("Cycle", "75", "sweep_id", "sweep", "sweeplist", "get_field_name"),
            array("Account Status", "80", "status", "status", "status", "get_status"),
            array("Action", "120", "", "", "", array("PAYMENT" => array("url" => "accounts/customer_payment_process_add/", "mode" => "single"),
                    "VIEW" => array("url" => "accounts/customer_edit/", "mode" => "single"),
                    "TAXES" => array("url" => "accounts/customer_account_taxes/edit/", "mode" => "popup"),
                    "CALLERID" => array("url" => "accounts/customer_add_callerid/", "mode" => "popup"),
                    "DELETE" => array("url" => "accounts/customer_delete/", "mode" => "single")
            ))
                ));
        return $grid_field_arr;
    }

    function build_account_list_for_reseller() {
        // array(display name, width, db_field_parent_table,feidname, db_field_child_table,function name);
        $grid_field_arr = json_encode(array(array("<input type='checkbox' name='chkAll' class='checkall'/>", "30", "", "", "", ""),
            array("Account Number", "110", "number", "", "", ""),
            array("Rate Group", "100", "pricelist_id", "name", "pricelists", "get_field_name"),
            array("First Name", "130", "first_name", "", "", ""),
            array("Last Name", "130", "last_name", "", "", ""),
            array("Company", "180", "company_name", "", "", ""),
            array("Balance", "70", "balance", "balance", "balance", "get_account_balance"),
            array("Credit Limit", "70", "credit_limit", "credit_limit", "credit_limit", "convert_to_currency"),
            array("Cycle", "82", "sweep_id", "sweep", "sweeplist", "get_field_name"),
            array("Account Status", "80", "status", "status", "status", "get_status"),
            array("Action", "120", "", "", "", array("PAYMENT" => array("url" => "accounts/customer_payment_process_add/", "mode" => "single"),
                    "VIEW" => array("url" => "accounts/reseller_edit/", "mode" => "single"),
                    "TAXES" => array("url" => "accounts/customer_account_taxes/edit/", "mode" => "popup"),
//                     "CALLERID" => array("url" => "accounts/customer_add_callerid/", "mode" => "single", 'popup'),
                    "DELETE" => array("url" => "accounts/reseller_delete/", "mode" => "single")
            ))
                ));
        return $grid_field_arr;
    }

    function build_account_list_for_provider() {
        // array(display name, width, db_field_parent_table,feidname, db_field_child_table,function name);
        $grid_field_arr = json_encode(array(array("<input type='checkbox' name='chkAll' class='checkall'/>", "30", "", "", "", ""),
            array("Account Number", "225", "number", "", "", ""),
            array("First Name", "200", "first_name", "", "", ""),
            array("Last Name", "210", "last_name", "", "", ""),
            array("Company", "200", "company_name", "", "", ""),
            array("Account Status", "200", "status", "status", "status", "get_status"),
            array("Action", "90", "", "", "", array(
                    "VIEW" => array("url" => "accounts/provider_edit//", "mode" => "single"),
                    "TAXES" => array("url" => "accounts/customer_account_taxes/edit/", "mode" => "popup"),
                    "DELETE" => array("url" => "accounts/provider_delete/", "mode" => "single")
            ))
                ));
        return $grid_field_arr;
    }

    function build_account_list_for_callshop() {
        $grid_field_arr = json_encode(array(array("<input type='checkbox' name='chkAll' class='checkall'/>", "30", "", "", "", ""),
            array("Account Number", "135", "number", "", "", ""),
            array("First Name", "133", "first_name", "", "", ""),
            array("Last Name", "135", "last_name", "", "", ""),
            array("Company", "185", "company_name", "", "", ""),
            array("Credit Limit", "135", "credit_limit", "", "", ""),
            array("Cycle", "145", "sweep_id", "sweep", "sweeplist", "get_field_name"),
            array("Account Status", "140", "status", "status", "status", "get_status"),
            array("Action", "90", "", "", "", array(
                    "VIEW" => array("url" => "accounts/callshop_edit//", "mode" => "single"),
                    "TAXES" => array("url" => "accounts/customer_account_taxes/edit/", "mode" => "popup"),
                    "DELETE" => array("url" => "accounts/callshop_delete/", "mode" => "single")
            ))
                ));
        return $grid_field_arr;
    }

    function build_account_list_for_subadmin() {
        $grid_field_arr = json_encode(array(array("<input type='checkbox' name='chkAll' class='checkall'/>", "30", "", "", "", ""),
            array("Account Number", "225", "number", "", "", ""),
            array("First Name", "200", "first_name", "", "", ""),
            array("Last Name", "210", "last_name", "", "", ""),
            array("Company", "200", "company_name", "", "", ""),
            array("Account Status", "200", "status", "status", "status", "get_status"),
            array("Action", "90", "", "", "", array(
                    "VIEW" => array("url" => "accounts/subadmin_edit//", "mode" => "single"),
                    "TAXES" => array("url" => "accounts/customer_account_taxes/edit/", "mode" => "popup"),
                    "DELETE" => array("url" => "accounts/subadmin_delete/", "mode" => "single")
            ))
                ));
        return $grid_field_arr;
    }

    function build_grid_buttons_customer() {
        $buttons_json = json_encode(array(array("Create Account", "add", "button_action", "/accounts/customer_add/"),
            array("Delete", "delete", "button_action", "/accounts/customer_selected_delete/"),
            array("Refresh", "reload", "button_action", "/accounts/customer_list_clearsearchfilter/")));
        return $buttons_json;
    }

    function build_grid_buttons_admin() {
        $buttons_json = json_encode(array(array("Create Account", "add", "button_action", "/accounts/admin_add/"),
            array("Delete", "delete", "button_action", "/accounts/admin_selected_delete/"),
            array("Refresh", "reload", "/accounts/clearsearchfilter/")));
        return $buttons_json;
    }

    function build_grid_buttons_subadmin() {
        $buttons_json = json_encode(array(array("Create Account", "add", "button_action", "/accounts/subadmin_add/"),
            array("Delete", "delete", "button_action", "/accounts/subadmin_selected_delete/"),
            array("Refresh", "reload", "/accounts/clearsearchfilter/")));
        return $buttons_json;
    }

    function build_grid_buttons_provider() {
        $buttons_json = json_encode(array(array("Create Account", "add", "button_action", "/accounts/provider_add/"),
            array("Delete", "delete", "button_action", "/accounts/provider_selected_delete/"),
            array("Refresh", "reload", "/accounts/clearsearchfilter/")));
        return $buttons_json;
    }

    function build_grid_buttons_reseller() {
        $buttons_json = json_encode(array(array("Create Account", "add", "button_action", "/accounts/reseller_add/"),
            array("Delete", "delete", "button_action", "/accounts/reseller_selected_delete/"),
            array("Refresh", "reload", "/accounts/clearsearchfilter/")));
        return $buttons_json;
    }

    function build_grid_buttons_callshop() {
        $buttons_json = json_encode(array(array("Create Account", "add", "button_action", "/accounts/callshop_add/"),
            array("Delete", "delete", "button_action", "/accounts/callshop_selected_delete/"),
            array("Refresh", "reload", "/accounts/clearsearchfilter/")));
        return $buttons_json;
    }

    function build_ip_list_for_customer($accountid,$accountype) {
	if($accountype == "customer"){
	  $grid_field_arr = json_encode(array(array("Name", "140", "name", "", "", ""),
	      array("IP Address", "180", "ip", "", "", ""),
	      array("Prefix", "120", "prefix", "", "", ""),
	      array("Rate Group", "100", "pricelist_id", "name", "pricelists", "get_field_name"),
  	      array("Action", "40", "", "", "", array("DELETE" => array("url" => "accounts/customer_ipmap_action/delete/$accountid/$accountype/", "mode" => "single")))
		  ));
	}else{
	  $grid_field_arr = json_encode(array(array("Name", "140", "name", "", "", ""),
	      array("IP Address", "180", "ip", "", "", ""),
	      array("Prefix", "120", "prefix", "", "", ""),
	      array("Action", "40", "", "", "", array("DELETE" => array("url" => "accounts/customer_ipmap_action/delete/$accountid/$accountype/", "mode" => "single")))
		  ));
	}
        return $grid_field_arr;
    }

    function build_animap_list_for_customer($accountid) {
        $grid_field_arr = json_encode(array(array("ANI Number", "150", "number", "", "", ""),
            array("Action", "120", "", "", "", array("DELETE" => array("url" => "accounts/customer_animap_action/delete/$accountid/", "mode" => "single")))
                ));
        return $grid_field_arr;
    }

    function build_animap_list_for_user() {
        $grid_field_arr = json_encode(array(array("IPAddress", "150", "number", "", "", ""),
            array("ipcontext", "150", "context", "", "", ""),
            array("Action", "120", "", "", "", array("DELETE" => array("url" => "user/user_animap_action/delete/", "mode" => "single")))
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
        
            $grid_field_arr = json_encode(array(array("Name", "230", "name", "", "", ""),
                array("Address", "260", "ip", "", "", ""),
                array("Prefix", "218", "prefix", "", "", ""),
                array("Rate Group", "210", "pricelist_id", "name", "pricelists", "get_field_name"),
                array("ip context", "220", "context", "", "", ""),
                array("Action", "30", "", "", "", array("DELETE" => array("url" => "user/user_ipmap_action/delete/", "mode" => "single")))
                    ));
            return $grid_field_arr;
        
    }

}

?>
