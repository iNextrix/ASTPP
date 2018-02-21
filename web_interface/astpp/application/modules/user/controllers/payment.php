<?php

class Payment extends MX_Controller {

  function Payment() {
      parent::__construct();
      $this->load->helper('template_inheritance');
      $this->load->library('session');
      $this->load->library('encrypt');
      $this->load->helper('form');

  }

  function index(){
      $account_data = $this->session->userdata("accountinfo");
      $data["accountid"] = $account_data["id"];
      $data["accountid"] = $account_data["id"];
      $data["page_title"] = "Account Recharge";      
      
       $system_config = common_model::$global_config['system_config'];
       if($system_config["paypal_mode"]==0){
           $data["paypal_url"] = $system_config["paypal_url"];
           $data["paypal_email_id"] = $system_config["paypal_id"];
       }else{
           $data["paypal_url"] = $system_config["paypal_sandbox_url"];
           $data["paypal_email_id"] = $system_config["paypal_sandbox_id"];
       }
       $data["paypal_tax"] = $system_config["paypal_tax"];

       $data["from_currency"] = $this->common->get_field_name('currency', 'currency', $account_data["currency_id"]);
       $data["to_currency"] = Common_model::$global_config['system_config']['base_currency'];
       $this->load->view("user_payment",$data);
  }
  
  function convert_amount($amount){
       $amount = $this->common_model->add_calculate_currency($amount,"","",true,false);
       echo number_format($amount,2);
  }
}
?> 
