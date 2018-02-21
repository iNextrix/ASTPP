<?php

class Login extends MX_Controller {

    function Login() {
        parent::__construct();
        $this->load->helper('form');
        $this->load->library('astpp/permission');
        $this->load->library('encrypt');        
        $this->load->model('Auth_model');
        $this->load->model('db_model');
    }

    function index() {
        if ($this->session->userdata('user_login') == FALSE) {
            if (!empty($_POST)) {// AND $_SERVER['SERVER_ADDR'] == $_SERVER['REMOTE_ADDR'])
                $this->load->model('system_model');
                $config = $this->system_model->getAuthInfo();
                $config_info = @$config[0];

                $user_valid = $this->Auth_model->verify_login($_POST['username'], $_POST['password']);
                if ($user_valid == 1) {
                    $this->session->set_userdata('user_login', TRUE);
		    $where = "number = '".$this->db->escape_str($_POST['username'])."' OR email = '".$this->db->escape_str($_POST['username'])."'";
                    $result = $this->db_model->getSelect("*", "accounts",$where);
                    $result = $result->result_array();
                    $result = $result[0];
                    $this->session->set_userdata('logintype', $result['type']);
                    $this->session->set_userdata('userlevel_logintype', $result['type']);
                    $this->session->set_userdata('username', $_POST['username']);
                    $this->session->set_userdata('accountinfo', $result);
                    if ($result['type'] == 0 || $result['type'] == 1) {
                        $menu_list = $this->permission->get_module_access($result['type']);
                        $this->session->set_userdata('mode_cur', 'user');
                        if($result['type'] == 1){
                            redirect(base_url() . 'dashboard/');
                        }else{
                            redirect(base_url() . 'user/user');
                        }
                    } else {
                        $menu_list = $this->permission->get_module_access($result['type']);
                        $this->session->set_userdata('mode_cur', 'admin');
                        redirect(base_url() . 'dashboard/');
                    }
                } else {
		    //print_r($config_info)
                    if ($_POST['username'] == "" && $config_info->value == $_POST['password']) {
                        $this->session->set_userdata('user_login', TRUE);
                        $this->session->set_userdata('logintype', 2);
                        $this->session->set_userdata('userlevel_logintype', -1);
                        $this->session->set_userdata('mode_cur', 'admin');
                        $menu_list = $this->permission->get_module_access(-1);
                        redirect(base_url() . '/dashboard/');
                    } else {
                        $data['astpp_errormsg'] = "Login Failed! Try Again..";
                    }
                }
            }

            $this->session->set_userdata('user_login', FALSE);
            $data['app_name'] = 'ASTPP - Open Source Billing Solution';
            $this->load->view('view_login', $data);
        }else {
	    if ($this->session->userdata('logintype') == '2') {
		redirect(base_url() . 'dashboard/');
	    } else {
		redirect(base_url().'user/user/');
	    }
        }
    }

    function logout() {
        $this->session->sess_destroy();
        redirect(base_url());
    }
    function paypal_response(){
//      echo "<pre>"; print_r($_POST); exit;
      if(count($_POST)>0)
      {
        $response_arr=$_POST;
//	$fp=fopen("/var/log/astpp_payment.log","w+");
//	$date = date("Y-m-d H:i:s");
//	fwrite($fp,"====================".$date."===============================\n");
//	foreach($response_arr as $key => $value){	  
//		fwrite($fp,$key.":::>".$value."\n");
//	}

        if($response_arr["payment_status"] == "Pending" || $response_arr["payment_status"] == "Complete"){

            $paypal_tax = $this->db_model->getSelect("value", "system", array("name" => "paypal_tax","group_title"=>"paypal"));
            $paypal_tax = $paypal_tax->result();
            $paypal_tax = $paypal_tax[0]->value;
            if($paypal_tax != 0 && $paypal_tax != ""){
                $balance_amt = $response_arr["payment_gross"] - (($response_arr["payment_gross"]*$paypal_tax)/100);
            }else{
                $balance_amt = $response_arr["payment_gross"];
            }
            $paypal_fee = $this->db_model->getSelect("value", "system", array("name" => "paypal_fee","group_title"=>"paypal"));
            $paypal_fee = $paypal_fee->result();
            $paypal_fee = $paypal_fee[0]->value;

            if($paypal_fee == 0){
                $paypalfee = 0;
            }else{
                $paypalfee =$response_arr["mc_gross"];
//                $balance_amt = $balance_amt - $paypalfee;
            }            
            
            $account_data = $this->db_model->getSelect("*", "accounts", array("id" => $response_arr["item_number"]));
            $account_data = $account_data->result_array();
            $account_data = $account_data[0];

            $currency = $this->db_model->getSelect('currency,currencyrate', 'currency', array("id"=>$account_data["currency_id"]));
            $currency = $currency->result_array();
            $currency =$currency[0];
            
            $date = date('Y-m-d H:i:s');
            $payment_trans_array = array("accountid"=>$response_arr["item_number"],"amount"=>$response_arr["payment_gross"],
                "tax"=>"1","payment_method"=>"Paypal","actual_amount"=>$balance_amt,"paypal_fee"=>$paypalfee,
                "user_currency"=>$currency["currency"],"currency_rate"=>$currency["currencyrate"],"transaction_details"=>json_encode($response_arr),"date"=>$date);
            $this->db->insert('payment_transaction',$payment_trans_array);
            $paymentid = $this->db->insert_id();
            
            $payment_arr = array("accountid"=> $response_arr["item_number"],"payment_mode"=>"1","credit"=>$balance_amt,
                    "type"=>"PAYPAL","payment_by"=>"1","notes"=>"Payment Made by Paypal on date:-".$date,"paypalid"=>$paymentid);
            $this->db->insert('payments', $payment_arr);
            
            if($account_data["reseller_id"] != "" && $account_data["reseller_id"] != 0){
                $comm_rate = $this->common->get_field_name('commission_rate', 'accounts', $account_data["reseller_id"]);
                if($comm_rate != "" && $comm_rate != 0){
                    $comm_amt = ($balance_amt*$comm_rate)/100;
                    $commission_arr = array("accountid"=>$response_arr["item_number"],"reseller_id"=>$account_data["reseller_id"],
                        "amount"=>$comm_amt,"description"=>"Paypal Commission to agent against customer account recharge.",
                        "payment_id"=>$paymentid,"commission_percent"=>"1","date"=>$date);
                    $this->db->insert('commission',$commission_arr);
                    $this->db_model->update_balance($balance_amt,$account_data["reseller_id"],"credit");            
                }
            }
            $this->db_model->update_balance($balance_amt,$response_arr["item_number"],"credit");            
            redirect(base_url() . 'user/user/');
        }
      }         
    }
}

?>
