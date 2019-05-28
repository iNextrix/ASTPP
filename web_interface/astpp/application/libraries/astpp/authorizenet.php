<?php
// ##############################################################################
// ASTPP - Open Source VoIP Billing Solution
//
// Copyright (C) 2016 iNextrix Technologies Pvt. Ltd.
// Samir Doshi <samir.doshi@inextrix.com>
// ASTPP Version 3.0 and above
// License https://www.gnu.org/licenses/agpl-3.0.html
//
// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU Affero General Public License as
// published by the Free Software Foundation, either version 3 of the
// License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU Affero General Public License for more details.
//
// You should have received a copy of the GNU Affero General Public License
// along with this program. If not, see <http://www.gnu.org/licenses/>.
// ##############################################################################
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Authorizenet {
	function __construct($library_name = '') {

		$this->CI = & get_instance ();
		$this->CI->load->model ( "db_model" );
		$this->CI->load->model ( 'Astpp_common' );
	}

	function user_authorize_defaultcardstatus_change($default_card){



			$default_card = $_POST['defaultcardid'];
			$accountinfo =	$GLOBALS['accountinfo'];
			if($default_card==0){

		    	$account = $this->CI->db_model->getSelect("id,cardtype,cardnumber,month,year,cvvnumber", "card_details",array("accountid"=>$accountinfo["id"]));

			    if ($account->num_rows > 0) {

			    	foreach ($account->result_array() as $value) {

		      			if($value['cardtype'] == 'Visa'){
		      				$data = base_url('/assets/images/rsz_visa.png'); 
		      			}
		      			if($value['cardtype'] == 'MasterCard'){
		      				$data = base_url('/assets/images/rsz_mastercard.png');
		      			}
		      			if($value['cardtype'] == 'Discover'){
		      				$data = base_url('/assets/images/rsz_discover.png');
		      			}
		      			if($value['cardtype'] == 'American Express'){
		      				$data = base_url('/assets/images/rsz_american_express.png');
		      			}

						$query = $this->CI->db->query('SELECT * FROM card_details  where accountid = '.$accountinfo["id"].' ORDER BY id ASC LIMIT 1');
						$query = $query->first_row();

						$checked= '';		      			
		      			if($value["id"] == $query->id){

		      				$checked= 'checked';
		      			}

			    		echo '<td class="pull-left col-md-10 no-padding">
			    		<label class="radio_style">

			    				<input type="radio" name="credit_card_id" id = "credit_card_id" onclick= "radiofunction('.$value["id"].');"
			    				value="'.$value["id"].'" '.$checked.'>
			    				<span class="checkmark"></span>'
			    				.$value["cardtype"].' '.'card number'.' '.$value["cardnumber"].'
			    				'.'expires '.'
								'.' ( '.'
			    				'.$value["month"].'
								'.' / '.'
			    				'.$value["year"].'
			    				'.' ) '.'
			    				'.'<img class="img-responsive pull-right" src="'.$data.'"> '.'
			    				
			    			</label></td>';
			    		$checked= '';
			  	}
			  			echo '<td class="pull-left col-md-10 no-padding">
			    		<label class="radio_style">

			    				<input type="radio" name="credit_card_id" id = "credit_card_id" onclick= "radiofunction(0);"
			    				value="-1"><span class="checkmark"></span>
			    				'.' Use a new card '.'
			    		</label></td>';
			  	}
			  	else{
						echo '<td class="pull-left col-md-10 no-padding">
			    		<label class="radio_style">

			    				<input type="radio" name="credit_card_id" id = "credit_card_id" onclick= "radiofunction(0);"
			    				value="-1" ><span class="checkmark" ></span>
			    				'.' Use a new card '.'
			    		</label></td>';

				    }
			}
		    else{
					echo '<td class="pull-left col-md-10 no-padding">
			    		<label class="radio_style">

			    				<input type="radio" name="credit_card_id" id = "credit_card_id" onclick= "radiofunction(0);"
			    				value="-1"><span class="checkmark"></span>
			    				'.' Use a new card '.'
			    		</label></td>';

		    }
	}

 
	
    function user_authorize_creditcard_change($credit_card_id){

    	$credit_card_id = $_POST['credit_card_id'];

    	if($credit_card_id!=""){

    		$whr= array("id" => $credit_card_id);

    		$customer_profile = $this->CI->db_model->getSelect("*", "card_details",$whr);
    		$customer_profile_details=$customer_profile->result_array();
			
    		$result_array=$this->user_authorize_get_customer_payment_profile($customer_profile_details);

    		$card_details_array=array(

    		 	"cardType"                =>$result_array[0]['paymentProfile']['payment']['creditCard']['cardType'],
    		 	"cardNumber"              =>$result_array[0]['paymentProfile']['payment']['creditCard']['cardNumber'],
    		 	"customerProfileId"       =>$result_array[0]['paymentProfile']['customerProfileId'],
    		 	"customerPaymentProfileId"=>$result_array[0]['paymentProfile']['customerPaymentProfileId'],
    		 	"Month"                   =>$result_array[1][0]['month'],
    		 	"Year"                    =>$result_array[1][0]['year'],
    		 	"CCName"                  =>$result_array[1][0]['ccname'],
    		 	"CVVNumber"               =>$result_array[1][0]['cvvnumber'],
    		 	"Address"                 =>$result_array[1][0]['address'],
    		 	"City"                    =>$result_array[1][0]['city'],
    		 	"State"                   =>$result_array[1][0]['state'],
    		 	"Zipcode"                 =>$result_array[1][0]['zip_code']

    		);

    		$card_details_json_array=json_encode($card_details_array);
    		 	 
    		echo $card_details_json_array;
    	}
    }


      function user_authorize_get_customer_payment_profile($customer_profile_details){
		
			error_reporting(E_ERROR | E_PARSE);
		
			$data= $this->user_setting_authorize_data();
		 
			$xmlContent = '<getCustomerPaymentProfileRequest xmlns="AnetApi/xml/v1/schema/AnetApiSchema.xsd">
			  <merchantAuthentication>
			    <name>'.$data["name"].'</name>
			    <transactionKey>'.$data["transaction_key"].'</transactionKey>
			  </merchantAuthentication>
			  <customerProfileId>'.$customer_profile_details[0]["profileid"].'</customerProfileId>
			  <customerPaymentProfileId>'.$customer_profile_details[0]["payment_profile_id"].'</customerPaymentProfileId>
			  <includeIssuerInfo>true</includeIssuerInfo>
			</getCustomerPaymentProfileRequest>';

	 	
		   $url = $data["authorize_url"];
		   $ch = curl_init();
		   curl_setopt($ch, CURLOPT_URL, $url);
		   curl_setopt($ch, CURLOPT_POST, true);
		   curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
		   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
		   curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlContent);

		   $result = curl_exec($ch);
		   


		   $result_array = json_decode(json_encode((array)simplexml_load_string($result)),true);

	       curl_close($ch);
		   return array($result_array,$customer_profile_details);

	}
	function user_setting_authorize_data(){
		$system_config = common_model::$global_config ['system_config'];
		if ($system_config ["authorize_mode"] == 0) {
			$data ["authorize_url"] = $system_config ["authorize_url"];
			$data ["name"] = $system_config ["authorize_live_name"];
			$data ["transaction_key"] = $system_config ["authorize_live_transaction_key"];
		} else {
			$data ["authorize_url"] = $system_config ["authorize_sandbox_url"];
			$data ["name"] = $system_config ["authorize_sandbox_name"];
			$data ["transaction_key"] = $system_config ["authorize_sandbox_transaction_key"];
		}
		return $data;
	}
	function user_authorization_payment($action = "") {
		$accountinfo =	$GLOBALS['accountinfo'];
		$this->db->where ( array (
			"amount" => "0",
			"accountid"=>$accountinfo['id']
		) );

		$this->db->delete("payment_authorization");		
		$this->load->helper('string');
		$data ['item_number'] = random_string('alnum', 80);
        
        $this->db->insert("payment_authorization", array("accountid"=>$accountinfo['id'],"amount"=>"0","item_number"=>$data ['item_number']));
		
		if (common_model::$global_config ['system_config'] ['authorize_status'] == 1) {
			redirect ( base_url () . 'authorize/authorize/' );
		}

		$this->load->module ( "authorize/payment" );
		
		if ($action == "GET_AMT") {

			$amount = $this->input->post ( "value", true );

			$this->payment->convert_amount ( $amount );

		} else {
			$this->payment->index ();
		}
	}

		function user_authorize_connection($carddata,$account_info){ 
		$message = array();
		error_reporting(E_ERROR | E_PARSE);
		if($carddata['defaultcard'] == '0') { 
			$data= $this->user_setting_authorize_data();
			$xmlContent = '<authenticateTestRequest xmlns="AnetApi/xml/v1/schema/AnetApiSchema.xsd">
		    <merchantAuthentication>
		        <name>'.$data["name"].'</name>
		        <transactionKey>'.$data["transaction_key"].'</transactionKey>
		    </merchantAuthentication>
			</authenticateTestRequest>';
	  
			$url =$data["authorize_url"];
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
			curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlContent);

		  	$result = curl_exec($ch);

			$result_array = json_decode(json_encode((array)simplexml_load_string($result)),true);
			curl_close($ch);
			foreach($result_array as $key=>$value)
			{
				$result=$value['resultCode'];
			}
			if($result=="Ok"){
				$this->user_authorize_payment($data,$carddata,$accouint_info);
				$message = array("status"=>"success","message"=>"Recharge success!");
				return $message;
			} else {
				$message = array("status"=>"fail","message"=>"Recharge fail!");
				return $message;
			}
		}
		else{
			$result_array=$this->authorize_charge_customer_profile($carddata,$account_info);
			$responded_itemnumber=$result_array['transactionResponse']['userFields']['userField']['value'];
			$result=$result_array['messages']['resultCode'];
				if($result=="Ok"){
					$message = array("status"=>"success","message"=>"Recharge success!");
					return $message;

				}else{
					$message = array("status"=>"fail","message"=>"Recharge fail!");
					return $message;
					exit ();
				}
			}
	}


	 function user_setting_authorize($data){
    	$system_config = common_model::$global_config ['system_config'];
			if ($system_config ["authorize_mode"] == 0) {
				$data ["authorize_url"] = $system_config ["authorize_url"];
				$data ["name"] = $system_config ["authorize_live_name"];
				$data ["transaction_key"] = $system_config ["authorize_live_transaction_key"];
			} else {
				$data ["authorize_url"] = $system_config ["authorize_sandbox_url"];
				$data ["name"] = $system_config ["authorize_sandbox_name"];
				$data ["transaction_key"] = $system_config ["authorize_sandbox_transaction_key"];
			}
			return $data;
    }

   function user_authorize_payment($data,$carddata,$accountinfo){
		error_reporting(E_ERROR | E_PARSE);
		$country=$this->CI->common->get_field_name('country','countrycode',$accountinfo['country_id']);
		if($accountinfo['reseller_id']== 0){
			$whr=array(
					"id"=>1
				);
		}
		else{
		     	$whr=array(
		     		"id"=>$accountinfo['reseller_id']
		     	);
		 }
		 $account_details=$this->CI->db_model->getSelect('first_name,last_name,company_name,address_1,address_2,postal_code,city,province,country_id','accounts',$whr);

		 $account_data=$account_details->result_array();
		
		 $account_country=$this->CI->common->get_field_name('country','countrycode',$account_data[0]['country_id']);
$itemnumber = 78;
   		 $xmlContent = '<createTransactionRequest xmlns="AnetApi/xml/v1/schema/AnetApiSchema.xsd">
		  <merchantAuthentication>
		     <name>'.$data["name"].'</name>
			 <transactionKey>'.$data["transaction_key"].'</transactionKey>
		  </merchantAuthentication>

		  <refId>123456</refId>

		  <transactionRequest>

		    <transactionType>authCaptureTransaction</transactionType>
		    <amount>'.$carddata["product_price"].'</amount>
		    <payment>
		      <creditCard>
		        <cardNumber>'.$carddata["card_number"].'</cardNumber>
		        <expirationDate>'.$carddata["ex_year"].'-'.$carddata["ex_month"].'</expirationDate>
		        <cardCode>'.$carddata["cvv_number"].'</cardCode>
		      </creditCard>
		    </payment>

		    <order>
		     <invoiceNumber>INV-147147</invoiceNumber>
		     <description>ASTPP</description>
		    </order>

		    <lineItems>
		      <lineItem>
		        <itemId>1</itemId>
		        <name>Authorize</name>
		        <description>Cannes logo </description>
		        <quantity>1</quantity>
		        <unitPrice>'.number_format((float)$carddata["product_price"], 2, '.', '').'</unitPrice>
		      </lineItem>
		    </lineItems>

		    <poNumber>'.$accountinfo['telephone_1'].'</poNumber>

		    <customer>
		      <id>'.$accountinfo['number'].'</id>
		    </customer>

		    <billTo>
		      <firstName>'.$accountinfo['first_name'].'</firstName>
		      <lastName>'.$accountinfo['last_name'].'</lastName>
		      <company>'.$accountinfo['company_name'].'</company>
		      <address>'.$carddata["address"].'</address>
		      <city>'.$carddata["city"].'</city>
		      <state>'.$carddata["state"].'</state>
		      <country>'.$carddata['country'].'</country>
		    </billTo>

		    <shipTo>
		      <firstName>'.$account_data[0]['first_name'].'</firstName>
		      <lastName>'.$account_data[0]['last_name'].'</lastName>
		      <company>'.$account_data[0]['company_name'].'</company>
		      <address>'.$account_data[0]['address_1'].','.$account_data[0]['address_2'].'</address>
		      <city>'.$account_data[0]['city'].'</city>
		      <state>'.$account_data[0]['province'].'</state>
		      <zip>'.$account_data[0]['postal_code'].'</zip>
		      <country>'.$account_country.'</country>
		    </shipTo>

		 <customerIP>'.$_SERVER['REMOTE_ADDR'].'</customerIP>

		    <userFields>
		      <userField>
		        <name>MerchantDefinedFieldName1</name>
		        <value>'.$carddata['order_id'].'</value>
		      </userField>
		      
		      <userField>
		        <name>favorite_color</name>
		        <value>blue</value>
		      </userField>
		    </userFields>

		  </transactionRequest>
		</createTransactionRequest>';
	
		  $url = $data["authorize_url"];
		  $ch = curl_init();
		  curl_setopt($ch, CURLOPT_URL, $url);
		  curl_setopt($ch, CURLOPT_POST, true);
		  curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
		  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
		  curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlContent);

		$result = curl_exec($ch);
        	$result_array = json_decode(json_encode((array)simplexml_load_string($result)),true);

		$responded_itemnumber=$result_array['transactionResponse']['userFields']['userField'][0]['value'];
		curl_close($ch);
		$result=$result_array['messages']['resultCode'];

		
		if($result=="Ok"){
			$message = array("status"=>"success","message"=>"Card Details added successfully!");
			return $message;
			exit ();
		}
		if($result=="Error")
		{
		
			$error_message=$result_array['transactionResponse']['errors']['error']['errorText'];
			$message = array("status"=>"fail","message"=>"Recharge fail!");
					return $message;
					exit ();
		}
	}	
	function user_authorization_payment_transaction($result_array,$data){
		$accountinfo = $this->CI->session->userdata("accountinfo");
		$accountinfo =	$GLOBALS['accountinfo'];
		$transation_details=json_encode($result_array);
		$currencyrate=$this->CI->common->get_field_name('currencyrate','currency',$accountinfo['currency_id']);
		
		$currency=$this->CI->common->get_field_name('currency','currency',$accountinfo['currency_id']);
		
		$payment_tansaction_details=array(
				'accountid'             => $accountinfo['id'],
				'amount'                => $data['amount'],
				'actual_amount'         => trim($data['actual_amount']),
				'payment_method'        => 'Authorize',
				'user_currency'         => $currency,
				'tax'                   => $data['actual_amount']-$data['amount'],
				'date'                  => gmdate('Y-m-d H:i:s'),
				'currency_rate'         => $currencyrate,
				'transaction_details'   => $transation_details
		);

		$this->CI->db->insert("payment_transaction", $payment_tansaction_details);
		$last_inserted_id = $this->CI->db->insert_id();

       	return $last_inserted_id;
	}


	function user_authorizationpayments($result_array,$data,$payment_id){
		$accountinfo =	$GLOBALS['accountinfo'];
		$parent_id = $accountinfo ['reseller_id'] > 0 ? $accountinfo ['reseller_id'] : '-1';
		$date= gmdate('Y-m-d H:i:s');
		
		$payment_details=array(
			'accountid'    => $accountinfo['id'],
			'credit'       => $data['amount'],
			'payment_mode' => 0,
			'type'         => 'Authorize',
			'payment_by'   => $parent_id,
			'notes'        => 'Payment Made by Authorize on date:-' . $date,
			'payment_date' => $date,
			'paypalid'     => $payment_id,
			'txn_id'       => $result_array['transactionResponse']['transId'],
			'reseller_id'  => $accountinfo['reseller_id'],
			'payment_status'=> 0
		);
		$this->CI->db->insert("payments", $payment_details);
       	return true;
	}

	function user_authorizationpayments_fail($result_array,$data,$payment_id){
		$accountinfo =	$GLOBALS['accountinfo'];
		$parent_id = $accountinfo ['reseller_id'] > 0 ? $accountinfo ['reseller_id'] : '-1';
		$date= gmdate('Y-m-d H:i:s');
		
		$payment_details=array(
			'accountid'    => $accountinfo['id'],
			'credit'       => 0,
			'payment_mode' => 0,
			'type'         => 'Authorize',
			'payment_by'   => $parent_id,
			'notes'        => 'Payment Made by Authoprint_rrize on date:-' . $date,
			'payment_date' => $date,
			'paypalid'     => $payment_id,
			'txn_id'       => 0,
			'reseller_id'  => $accountinfo['reseller_id'],
			'payment_status'=> 1
		);

		$this->CI->db->insert("payments", $payment_details);
       	return true;
	}

	function generate_receipt($accountid, $amount, $accountinfo, $last_invoice_ID, $invoice_prefix, $due_date) {

		$invoice_data = array (
				"accountid"      => $accountid,
				"invoice_prefix" => $invoice_prefix,
				"invoiceid"      => '0000' . $last_invoice_ID,
				"reseller_id"    => $accountinfo ['reseller_id'],
				"invoice_date"   => gmdate ( "Y-m-d H:i:s" ),
				"from_date"      => gmdate ( "Y-m-d H:i:s" ),
				"to_date"        => gmdate ( "Y-m-d H:i:s" ),
				"due_date"       => $due_date,
				"status"         => 1,
				"balance"        => $accountinfo ['balance'],
				"amount"         => $amount,
				"type"           => 'R',
				"confirm"        => '1' 
		);

		$this->CI->db->insert ( "invoices", $invoice_data );
		$invoiceid = $this->CI->db->insert_id ();
		return $invoiceid;
	}


	function user_authorization_invoice_details($result_array,$data){
		$date= gmdate('Y-m-d H:i:s');
		$accountinfo =	$GLOBALS['accountinfo'];
		$balance=$this->CI->common->get_field_name('balance','accounts',$accountinfo['id']);

		$this->CI->db->select ( 'invoiceid' );
		$this->CI->db->order_by ( 'id', 'desc' );
		$this->CI->db->limit ( 1 );
		$last_invoice_result = ( array ) $this->CI->db->get ( 'invoices' )->first_row ();

		$last_invoice_ID = isset ( $last_invoice_result ['invoiceid'] ) && $last_invoice_result ['invoiceid'] > 0 ? $last_invoice_result ['invoiceid'] : 1;

		$reseller_id = $accountinfo ['reseller_id'] > 0 ? $accountinfos ['reseller_id'] : 0;
		
		$where = "accountid IN ('" . $reseller_id . "','1')";
		$this->CI->db->where ( $where );
		$this->CI->db->select ( '*' );
		$this->CI->db->order_by ( 'accountid', 'desc' );
		$this->CI->db->limit ( 1 );
		$invoiceconf = $this->CI->db->get ( 'invoice_conf' );
		$invoiceconf = ( array ) $invoiceconf->first_row ();
		$invoice_prefix = $invoiceconf ['invoice_prefix'];
		
		$due_date = gmdate ( "Y-m-d H:i:s", strtotime ( gmdate ( "Y-m-d H:i:s" ) . " +" . $invoiceconf ['interval'] . " days" ) );
		$invoice_id = $this->generate_receipt ( $accountinfo ['id'], $data['amount'], $accountinfo, $last_invoice_ID + 1, $invoice_prefix, $due_date );

		$details_insert = array (
				'created_date'   => $date,
				'credit'         => $data['amount'],
				'debit'          => '-',
				'accountid'      => $accountinfo ['id'],
				'reseller_id'    => $accountinfo ['reseller_id'],
				'invoiceid'      => $invoice_id,
				'description'    => "Payment Made by Authorize on date:-" . $date,
				'item_type'      => 'PAYMENT',
				'before_balance' => $balance,
				'after_balance'  => $balance + $data['amount'] 
		);
		$this->CI->db->insert ( "invoice_details", $details_insert );
		$this->CI->db_model->update_balance ( $data['amount'], $accountinfo ["id"], "credit" );
	}

	function authorize_charge_customer_profile($carddata,$accouint_info){

		error_reporting(E_ERROR | E_PARSE);
		$data= $this->user_setting_authorize_data();

		$xmlContent = '<createTransactionRequest xmlns="AnetApi/xml/v1/schema/AnetApiSchema.xsd">
		  <merchantAuthentication>
		    <name>'.$data["name"].'</name>
		    <transactionKey>'.$data["transaction_key"].'</transactionKey>
		  </merchantAuthentication>

		  <refId>123456</refId>
		  <transactionRequest>
		    <transactionType>authCaptureTransaction</transactionType>
		    <amount>'.$carddata["product_price"].'</amount>

		    <profile>
		      <customerProfileId>'.$data["customerProfileId"].'</customerProfileId>
		      <paymentProfile>
		        <paymentProfileId>'.$data["customerPaymentProfileId"].'</paymentProfileId>
		      </paymentProfile>
		    </profile>

		    <order>
		      <invoiceNumber>INV-12345</invoiceNumber>
		      <description>Product Description</description>
		    </order>

		    <lineItems>
		      <lineItem>
		        <itemId>1</itemId>
		        <name>vase</name>
		        <description>Cannes logo </description>
		        <quantity>18</quantity>
		        <unitPrice>45.00</unitPrice>
		      </lineItem>
		    </lineItems>

		    <poNumber>456654</poNumber>

		    <shipTo>
		      <firstName>divya</firstName>
		      <lastName>Panchal</lastName>
		      <company>Thyme for Tea</company>
		      <address>12 Main Street</address>
		      <city>Pecan Springs</city>
		      <state>TX</state>
		      <zip>44628</zip>
		      <country>USA</country>
		    </shipTo>
		    <customerIP>'.$_SERVER['REMOTE_ADDR'].'</customerIP>

		     <userFields>
		      <userField>
		        <name>MerchantDefinedFieldName1</name>
		        <value>'.$itemnumber.'</value>
		      </userField>
		     </userFields>
		  </transactionRequest>
		</createTransactionRequest>';

		  $url = $data["authorize_url"];
		  $ch = curl_init();
		  curl_setopt($ch, CURLOPT_URL, $url);
		  curl_setopt($ch, CURLOPT_POST, true);
		  curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
		  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
		  curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlContent);

		  $result = curl_exec($ch);

	      $result_array = json_decode(json_encode((array)simplexml_load_string($result)),true);		
		  curl_close($ch);
		  return $result_array;
	}
	function authorize_list(){
		$data['username'] = $this->CI->session->userdata('user_name');
        $data['page_title'] = gettext('Card List');
	    $data['search_flag'] = false;
        $this->CI->session->set_userdata('advance_search', 0);
        $data['grid_fields'] = $this->CI->user_form->build_authorize_payment_list_for_admin();
        $data["grid_buttons"] = $this->CI->user_form->build_grid_buttons();
        $data['form_search'] = $this->CI->form->build_serach_form($this->CI->user_form->get_authorize_search_form());

        return $data;
	}

	function authorize_list_json() {
        $json_data = array();
        $count_all = $this->CI->db_model->authorize_list(false);
        $paging_data = $this->CI->form->load_grid_config($count_all, $_GET['rp'], $_GET['page']);
        $json_data = $paging_data["json_paging"];
        $query = $this->CI->db_model->authorize_list(true, $paging_data["paging"]["start"], $paging_data["paging"]["page_no"]);
        
        $grid_fields = json_decode($this->CI->user_form->build_authorize_payment_list_for_admin());
        $json_data['rows'] = $this->CI->form->build_grid($query, $grid_fields);
        return $json_data;
    }

    function authorize_create_customer_profile($carddata,$customerdata){ 
		$country=$this->CI->common->get_field_name('country','countrycode',$customerdata['country_id']);
		error_reporting(E_ERROR | E_PARSE);
		$data= $this->user_setting_authorize_data();

		$xmlContent = '<createCustomerProfileRequest xmlns="AnetApi/xml/v1/schema/AnetApiSchema.xsd"> 
		   <merchantAuthentication>
		     <name>'.$data["name"].'</name>
		     <transactionKey>'.$data["transaction_key"].'</transactionKey>
		    </merchantAuthentication>
		   <profile>
		     <merchantCustomerId>Merchant_Customer_ID</merchantCustomerId>
		     <description>Profile description here</description>
		     <email>'.$customerdata['email'].'</email>
		     <paymentProfiles>
		       <customerType>individual</customerType>
		        <payment>
		          <creditCard>
		            <cardNumber>'.$carddata["card_number"].'</cardNumber>
		            <expirationDate>'.$carddata["ex_year"].'-'.$carddata["ex_month"].'</expirationDate>
		          </creditCard>
		         </payment>
		      </paymentProfiles>
		    </profile>
			<validationMode>testMode</validationMode>
		  </createCustomerProfileRequest>';

		
		  $url = $data["authorize_url"];
		  $ch = curl_init();
		  curl_setopt($ch, CURLOPT_URL, $url);
		  curl_setopt($ch, CURLOPT_POST, true);
		  curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
		  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
		  curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlContent);
		  $result = curl_exec($ch);
		  $result_array = json_decode(json_encode((array)simplexml_load_string($result)),true);
		  $result=$result_array['messages']['resultCode'];
		  return $result_array;
	}

	function authorize_create_customer_payment_profile($carddata,$customer_data,$cust_authorize_profile_id){

			$payment_profile_id = $this->CI->common->get_field_name('payment_profile_id','card_details',array("profileid"=>$cust_authorize_profile_id));

			$country=$this->CI->common->get_field_name('country','countrycode',$customer_data['country_id']);
			$number = substr($carddata['card_number'], 8, strlen($carddata['card_number']));
			error_reporting(E_ERROR | E_PARSE);
			$data= $this->user_setting_authorize_data();
		 	$xmlContent ='<createTransactionRequest xmlns="AnetApi/xml/v1/schema/AnetApiSchema.xsd">
				  <merchantAuthentication>
				    <name>'.$data["name"].'</name>
				    <transactionKey>'.$data["transaction_key"].'</transactionKey>
				  </merchantAuthentication>
				  <refId>123456</refId>
				  <transactionRequest>
				    <transactionType>authCaptureTransaction</transactionType>
				    <amount>5</amount>
				    <profile>
				      <customerProfileId>'.$cust_authorize_profile_id.'</customerProfileId>
				      <paymentProfile>
					<paymentProfileId>'.$payment_profile_id.'</paymentProfileId>
				      </paymentProfile>
				    </profile>
				    <lineItems>
				      <lineItem>
					<itemId>'.$carddata['order_id'].'</itemId>
					<name>'.$carddata['product_name'].'</name>
        				
       					 <quantity>1</quantity>
					<unitPrice>'.$carddata['product_price'].'</unitPrice>
				      </lineItem>
				    </lineItems>
				    <poNumber>'.$customer_data['first_name'].'</poNumber>
				    <shipTo>
				      <firstName>'.$customer_data['first_name'].'</firstName>
				      <lastName>'.$customer_data['last_name'].'</lastName>
				      <company>'.$customer_data['company_name'].'</company>
				      <address>'.$carddata['address'].'</address>
				      <city>'.$carddata['city'].'</city>
				      <state>'.$carddata['state'].'</state>
				     
				      <country>'.$carddata['country'].'</country>
				    </shipTo>
				    
				  </transactionRequest>
				</createTransactionRequest>'; 

		  	$url = $data["authorize_url"];
		  	$ch  = curl_init();
		  	curl_setopt($ch, CURLOPT_URL, $url);
		  	curl_setopt($ch, CURLOPT_POST, true);
		  	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
		  	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
		  	curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlContent);
		  	$result = curl_exec($ch);
		  	$result_array = json_decode(json_encode((array)simplexml_load_string($result)),true);
		  	curl_close($ch);
		  	return $result_array;
	}


	    function authorize_customer_profile_save($carddata,$customer_data = array()){ 

    		  $message = array();
		  $cardnumber   =  $carddata['card_number'];
		  $cust_authorize_profile_id    =  $customer_data['cust_authorize_profile_id'];

		  if(!$cust_authorize_profile_id){ 
					$result_array = $this->authorize_create_customer_profile($carddata,$customer_data);
					if($result_array['messages']['resultCode']=="Ok"){
						$customer_data['profileid']=$result_array['customerProfileId'];
	       					$this->CI->db->where("id",  $customer_data ['id']);
	       					$this->CI->db->update("accounts", array('cust_authorize_profile_id'=>$customer_data['profileid']));
						$customer_data['payment_profile_id']=$result_array['customerPaymentProfileIdList']['numericString'];
						$customer_data['cardnumber']= str_repeat("X", strlen($cardnumber) - 4) . substr($cardnumber, -4);

			        		$card_detials_array = $this->add_customer_profile($carddata,$customer_data);
		        			$message = array("status"=>"success","message"=>"Card Details added successfully!");
					 }else{
					 	$message = array("status"=>"fail","message"=> $message = $result_array['messages']['message']['text']);
					 }


        	} else { 
	    		$result_array = $this->authorize_create_customer_payment_profile($carddata,$customer_data,$cust_authorize_profile_id);

	        	if($result_array['messages']['resultCode'] == 'Error'){
	        			$message = array("status"=>"fail","message"=> $message = $result_array['messages']['message']['text']);
	        	}else{

						$customer_data['profileid'] = $result_array['transactionResponse']['profile']['customerProfileId'];
						$customer_data['payment_profile_id'] = $result_array['transactionResponse']['profile']['customerPaymentProfileId'];
						if($result_array['messages']['resultCode'] == 'Ok'){
							$customer_data['cardnumber']= str_repeat("X", strlen($cardnumber) - 4) . substr($cardnumber, -4);
							$query   = $this->add_customer_profile($carddata,$customer_data);

							$message = array("status"=>"success","message"=>"Card Details added successfully!");
					}	
				}	        	
	        }

       	return $message;
	}


	function add_customer_profile($carddata,$customer_data) {

  		  $sql = $this->CI->db->query('SELECT id FROM accounts WHERE cust_authorize_profile_id = '.$customer_data['cust_authorize_profile_id'].'');
    	 	  $sql = $sql->result();
		  $customer_data['accountid'] = $sql['0']->id;
		  $insert_array = array(
				"cardtype"=>$carddata['cardtype'],
				"cardnumber"=>$customer_data['cardnumber'],
				"month"=>$carddata['ex_month'],
				"year"=>$carddata['ex_year'],
				"ccname"=>$carddata['card_holder_name'],
				"address"=>$carddata['address'],
				"city"=>$carddata['city'],
				"state"=>$carddata['state'],
				"country"=>$carddata['country'],
				"profileid"=>$customer_data['profileid'],
				"accountid"=> $customer_data['accountid'],
				"payment_profile_id"=>$customer_data['payment_profile_id']
		);


         	 $this->CI->db->insert("card_details", $insert_array);
          	return true;
	}	


	
	function user_authorize_delete(){

		$authorize_id = $this->CI->uri->segment('3');
		$this->CI->db->select('profileid');
	    $this->CI->db->from('card_details');
	    $this->CI->db->where('id', $authorize_id );
	    $query = $this->CI->db->get();
	    $query = $query->result_array();
	    $query = $this->CI->db_model->countQuery("*", "card_details", 
	    	array(
	    		"profileid"=>$query['0']['profileid']
	    		));
    	if($query==1){
    		$this->authorize_delete_customer_profile($authorize_id);

			$accountinfo =	$GLOBALS['accountinfo'];
    		$this->CI->db->where("id",  $accountinfo['id']);
	       					$this->CI->db->update("accounts", array('cust_authorize_profile_id'=> '0' ));
    	}
    	else{
    		$this->authorize_delete_customer_payment_profile($authorize_id);
    	}  
      $this->CI->db->delete("card_details",array("id"=>$authorize_id));
      $this->CI->session->set_flashdata('astpp_notification', 'Card removed successfully!');
      redirect(base_url() . 'user/user_card_details/');

	}

	function authorize_delete_customer_profile($authorize_id){
		error_reporting(E_ERROR | E_PARSE);
		$data= $this->user_setting_authorize_data();
		$profileid=$this->CI->common->get_field_name('profileid','card_details',array("id"=>$authorize_id));
		$xmlContent = '<deleteCustomerProfileRequest xmlns="AnetApi/xml/v1/schema/AnetApiSchema.xsd">
	    <merchantAuthentication>
	    <name>'.$data["name"].'</name>
	    <transactionKey>'.$data["transaction_key"].'</transactionKey>
	    </merchantAuthentication>
	    <customerProfileId>'.$profileid.'</customerProfileId>
	    </deleteCustomerProfileRequest>';

		  $url = $data["authorize_url"];
		  $ch = curl_init();
		  curl_setopt($ch, CURLOPT_URL, $url);
		  curl_setopt($ch, CURLOPT_POST, true);
		  curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
		  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
		  curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlContent);
		  $result = curl_exec($ch);
		  $result_array = json_decode(json_encode((array)simplexml_load_string($result)),true);
		  curl_close($ch);
	}

	function authorize_delete_customer_payment_profile($selected_value){
		
		$selectedvalue = str_replace("'", "", trim($selected_value));
		error_reporting(E_ERROR | E_PARSE);
		$data= $this->user_setting_authorize_data();
		$profileid=$this->CI->common->get_field_name('profileid','card_details',array("id"=>$selectedvalue));
		$paymentprofileid=$this->CI->common->get_field_name('payment_profile_id','card_details',array("id"=>$selectedvalue));
		$xmlContent = '<deleteCustomerPaymentProfileRequest xmlns="AnetApi/xml/v1/schema/AnetApiSchema.xsd">
  		<merchantAuthentication>
    	<name>'.$data["name"].'</name>
   	 	<transactionKey>'.$data["transaction_key"].'</transactionKey>
  		</merchantAuthentication>
  		<customerProfileId>'.$profileid.'</customerProfileId>
  		<customerPaymentProfileId>'.$paymentprofileid.'</customerPaymentProfileId>
		</deleteCustomerPaymentProfileRequest>';

		  $url = $data["authorize_url"];
		  $ch = curl_init();
		  curl_setopt($ch, CURLOPT_URL, $url);
		  curl_setopt($ch, CURLOPT_POST, true);
		  curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
		  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
		  curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlContent);

	  	$result = curl_exec($ch);
	  	$result_array = json_decode(json_encode((array)simplexml_load_string($result)),true);
    	curl_close($ch);
	}


	

}
?>
