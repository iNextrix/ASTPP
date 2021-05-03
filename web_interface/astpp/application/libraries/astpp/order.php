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
class order {
	function __construct($library_name = '') {
		$this->CI = & get_instance ();
		$this->CI->load->model ( 'db_model' );
		$this->CI->load->library ('email');
		$this->CI->load->library ( 'session' );	
		$this->CI->load->library ('astpp/payment');
		$this->CI->load->library ('astpp/invoice');
		$this->CI->load->model ( 'common_model' );
	}

	
	function confirm_product_order($productarr,$account_id){ 
		$orderobjArr = array();
		$parent_order_id = 0;
		$this->get_account_info($orderobjArr,$account_id);

		foreach($orderobjArr['accounts'] as $key => $accountdata){
			if($productarr['product_category'] == 3){
				$product_info = $this->CI->db_model->getSelect("*"," products",array("id"=>$productarr['id'],"status"=>0,"is_deleted"=>0));
				$product_info  = (object)$product_info->result_array()[0];
			}else{
				$product_info = $this->get_account_product_info($orderobjArr,$accountdata,$productarr);
			}


			if(isset($product_info->id) && $product_info->id > 0){
				$account_currency_info = $this->CI->db_model->getSelect("*","currency",array("id"=>$accountdata->currency_id));
				if($account_currency_info->num_rows > 0){
					$account_currency_info = (array)$account_currency_info->result_array();
					$account_currency_info = $account_currency_info[0];
				}	
				$product_info->payment_status = "PENDING";
				$product_info->payment_by = $productarr['payment_by'] == 0 ? "paypal" : "card";
				$product_info->quantity = $productarr['quantity'];

				$parent_order_id = $this->generate_order($product_info,$accountdata,(array)$accountdata,$parent_order_id,$account_currency_info);


			}
		}
		return $parent_order_id ;
	}
	
	function get_account_info(&$orderobjArr,$account_id,$is_parent_billing=true){ 
		$accountarr = array();
		$tempkeyarr = array();
		$select = "id,number,reseller_id,type,balance,credit_limit,email,posttoexternal,currency_id,is_distributor";
		$temparray = $this->CI->db_model->getSelect($select,"accounts",array("id"=>$account_id,"status"=>"0","deleted"=>"0"));
		if($temparray->num_rows > 0){
			$temparray = $temparray->first_row();

			$accountarr[$temparray->id] = $temparray;
			$tempkeyarr[] = $temparray->id;
			$resellerid = $temparray->reseller_id;
			while($resellerid > 0 && $is_parent_billing == 'true'){
				$temparray = $this->CI->db_model->getSelect($select,"accounts",array("id"=>$resellerid,"status"=>"0","deleted"=>"0"));
				if($temparray->num_rows > 0){ 
					$temparray = $temparray->first_row();
					$accountarr[$temparray->id] = $temparray;
					$tempkeyarr[] = $temparray->id;
					$resellerid = $temparray->reseller_id;
				}
			}
			array_multisort($tempkeyarr, SORT_ASC, $accountarr);
			return $orderobjArr['accounts'] = $accountarr;

		} 
	}	

	function get_account_product_info(&$orderobjArr,$accountdata,$productdata){ 
		if($accountdata->type == '1'){
			if($accountdata->reseller_id > 0){ 
				$product_query = $this->CI->db_model->getJionQuery('products', 'products.id,products.name,products.release_no_balance,products.product_category,products.buy_cost,products.commission,reseller_products.price,reseller_products.setup_fee,products.billing_type,products.billing_days,reseller_products.free_minutes,products.status,products.last_modified_date,reseller_products.product_id,reseller_products.setup_fee,reseller_products.is_optin', array('products.status'=>0,'reseller_products.product_id'=>$productdata['product_id'],'reseller_products.account_id'=>$accountdata->reseller_id), 'reseller_products','products.id=reseller_products.product_id', 'inner','','','DESC','products.id');
//commented for paypal commission
	
				//$product_query = $this->CI->db_model->getJionQuery('products', 'products.id,products.name,products.release_no_balance,products.product_category,products.buy_cost,products.commission,reseller_products.price,reseller_products.setup_fee,products.billing_type,products.billing_days,reseller_products.free_minutes,products.status,products.last_modified_date,reseller_products.product_id,reseller_products.setup_fee,reseller_products.is_optin', array('products.status'=>0,'reseller_products.product_id'=>$productdata['product_id'],'reseller_products.account_id'=>$accountdata->id), 'reseller_products','products.id=reseller_products.product_id', 'inner','','','DESC','products.id');
			}else{ 
				$product_query = $this->CI->db_model->getJionQuery('products', 'products.id,products.name,products.release_no_balance,products.product_category,products.buy_cost,products.commission,products.price,products.setup_fee,products.billing_type,products.billing_days,products.free_minutes,products.status,products.last_modified_date,reseller_products.product_id,reseller_products.is_optin', array('products.status'=>0,'reseller_products.product_id'=>$productdata['product_id'],'reseller_products.is_optin'=>0,'reseller_products.account_id'=>$accountdata->id), 'reseller_products','products.id=reseller_products.product_id', 'inner','','','DESC','products.id');
			}
			if($product_query->num_rows > 0){
			   $product_info =  $product_query->first_row();
			   $orderobjArr['product'][$accountdata->id] = $product_info;
			   return $orderobjArr['product'][$accountdata->id];
			}else{
				return "Product not found.";
			}

		}else{
			if($accountdata->reseller_id > 0){
				$product_info = $this->CI->db_model->getJionQuery('products', 'products.id,products.name,products.release_no_balance,products.product_category,products.buy_cost,products.can_purchase,products.can_resell,products.commission,reseller_products.price,reseller_products.setup_fee,products.billing_type,products.billing_days,reseller_products.free_minutes,products.status,products.last_modified_date,reseller_products.product_id,reseller_products.setup_fee,reseller_products.is_optin', array('products.status'=>0,'reseller_products.product_id'=>$productdata['product_id'],'reseller_products.account_id'=>$accountdata->reseller_id), 'reseller_products','products.id=reseller_products.product_id', 'inner','','','DESC','products.id');

				if($product_info->num_rows > 0 ){
				    	$product_info = $product_info->first_row();
					$product_info->price = (isset($productdata['price']) && $productdata['price'] > 0 && $productdata['price'] != $product_info->price)?$this->CI->common_model->add_calculate_currency($productdata['price'],'', '', true, false):$product_info->price;
					$product_info->setup_fee = (isset($productdata['setup_fee']) && $productdata['setup_fee']> 0 && $productdata['setup_fee'] != $product_info->setup_fee)?$this->CI->common_model->add_calculate_currency($productdata['setup_fee'],'', '', true, false):$product_info->setup_fee;
					$product_info->billing_days = (isset($productdata['billing_days']) && $productdata['billing_days']!= '')?$productdata['billing_days']:$product_info->billing_days;
					$product_info->billing_type = (isset($productdata['billing_type']) && $productdata['billing_days']!= '')?$productdata['billing_type']:$product_info->billing_type;
					$product_info->free_minutes = (isset($productdata['free_minutes']) && $productdata['free_minutes']> 0)?$productdata['free_minutes']:$product_info->free_minutes;
					
				   	$orderobjArr['product'][$accountdata->id] = $product_info;
				  	 return $orderobjArr['product'][$accountdata->id];
				}else{
					return "Product not found.";
				}

			}else{ 
				$product_info = $this->CI->db_model->getSelect("*","products",array("id"=>$productdata['product_id'],"status"=>"0"));
				if($product_info->num_rows > 0 ){
				    	$product_info = $product_info->first_row();
					$product_info->price = (isset($productdata['price']) && $productdata['price'] > 0 && $productdata['price'] != $product_info->price)?$this->CI->common_model->add_calculate_currency($productdata['price'],'', '', true, false):$product_info->price;

					$product_info->setup_fee = (isset($productdata['setup_fee']) && $productdata['setup_fee']> 0 && $productdata['setup_fee'] != $product_info->setup_fee)?$this->CI->common_model->add_calculate_currency($productdata['setup_fee'],'', '', true, false):$product_info->setup_fee;
					$product_info->billing_days = (isset($productdata['billing_days']) && $productdata['billing_days']!= '')?$productdata['billing_days']:$product_info->billing_days;
					$product_info->billing_type = (isset($productdata['billing_type']) && $productdata['billing_days']!= '')?$productdata['billing_type']:$product_info->billing_type;
					$product_info->free_minutes = (isset($productdata['free_minutes']) && $productdata['free_minutes']> 0)?$productdata['free_minutes']:$product_info->free_minutes;
					$orderobjArr['product'][$accountdata->id] = $product_info;
				  	 return $orderobjArr['product'][$accountdata->id];
				}else{
					return "Product not found.";
				}

		
		   }
		}
	}

	function confirm_order($productdata,$account_id,$created_by_accountinfo){ 
		$parent_array = array();
		$parent_key_arr = array();
		$orderobjArr = array();
		$parent_order_id = 0;
		$parent_commission = false;
		$is_parent_billing = (isset($productdata['is_parent_billing']))?$productdata['is_parent_billing']:'true';
		$this->get_account_info($orderobjArr,$account_id,$is_parent_billing);


		foreach($orderobjArr['accounts'] as $key => $accountdata){

			if(($accountdata->is_distributor == 1 && $accountdata->type == 1) || ($accountdata->type == 0 && $accountdata->reseller_id > 0 && isset($parent_array[$accountdata->reseller_id]) && $parent_array[$accountdata->reseller_id]->is_distributor == 1)){
				$parent_array[$accountdata->id] = $accountdata;
				$parent_key_arr[] = $accountdata->id;
			}
			$product_info = $this->get_account_product_info($orderobjArr,$accountdata,$productdata);
			$orderobjArr['accounts'][$key]->product_info=$product_info;
			if(isset($product_info->id) && $product_info->id > 0){
				
				$account_currency_info = $this->CI->db_model->getSelect("*","currency",array("id"=>$accountdata->currency_id));
				if($account_currency_info->num_rows > 0){
					$account_currency_info = (array)$account_currency_info->result_array();
					$account_currency_info = $account_currency_info[0];
					$orderobjArr['accounts'][$key]->currency_info=$account_currency_info;
				}	
				$product_info->payment_status = "PAID";
				$product_info->payment_by = $productdata['payment_by'] == 0 ? "Account Balance" : "Card";
				$product_info->quantity = (isset($productdata['quantity']) && $productdata['quantity'] > 0 )?$productdata['quantity']:'1';

				$parent_order_id = $this->generate_order($product_info,$accountdata,$created_by_accountinfo,$parent_order_id,$account_currency_info);

				if($parent_order_id){ 
					$product_info->order_item_id= $parent_order_id;
					$product_info->price= ($product_info->price+$product_info->setup_fee);
					$product_info->price= ($product_info->price * $product_info->quantity);
					$product_info->invoice_type = ($product_info->product_category == 3) ? "credit":"debit";
					$product_info->charge_type = $this->CI->common->get_field_name("code","category",array("id"=>$product_info->product_category)); 
					$product_info->description= $product_info->charge_type." (".$product_info->name." X ".$product_info->quantity.") has been added.";
					$product_info->is_apply_tax =($product_info->payment_by == "Account Balance")?"false":"true";

					$last_payment_id=$this->CI->payment->add_payments_transcation((array)$product_info,(array)$accountdata,$account_currency_info);
					$orderobjArr['accounts'][$key]->invoiceid=$last_payment_id;
					if($accountdata->type == 1 && !empty($parent_array)){ 

						if($accountdata->reseller_id > 0){
							$parent_array[$accountdata->reseller_id]->commission = (isset($parent_array[$accountdata->reseller_id]->commission))?$parent_array[$accountdata->reseller_id]->commission:0;
							$parent_commission = (($parent_array[$accountdata->reseller_id]->commission*$product_info->commission)/100);
							$parent_array[$accountdata->reseller_id]->commission = ($parent_array[$accountdata->reseller_id]->commission - $parent_commission);
							$parent_array[$accountdata->id]->commission = $parent_commission;

						}else{
							$parent_commission = (($product_info->price*$product_info->commission)/100);
							$parent_array[$accountdata->id]->commission = $parent_commission;
						}
						$parent_array[$accountdata->id]->currecny_info = $account_currency_info;
						$parent_array[$accountdata->id]->product_info = $product_info;
						$parent_array[$accountdata->id]->order_item_id= $parent_order_id;
						$parent_array[$accountdata->id]->product_id= $product_info->product_id;
						$parent_array[$accountdata->id]->description= "Product (".$product_info->name.") commission has been credited";
// print_r($product_info); 
					}
					if(isset($product_info->is_optin)){
						if($product_info->is_optin == '0' && $accountdata->type == 0 && $product_info->product_category != '4'){
							$parent_commission = true;
						}
					/*if($product_info->is_optin == '0' && $key > 0 && $product_info->product_category != '4' && $accountdata->is_distributor == 0){
						$parent_distributor_arr = $orderobjArr['accounts'][$key-1];
						$parent_distributor_product = $parent_distributor_arr->product_info;

						$parent_distributor_product->price =$product_info->price- $parent_distributor_product->price;

						$parent_distributor_product->INV_DIRECT_PAY = "true";
						$parent_distributor_product->is_update_balance = "true";
						$parent_distributor_product->payment_by = "Manual";
						$parent_distributor_product->invoiceid = $parent_distributor_arr->invoiceid;

//echo "<pre> ACCOUNT ARR ::::"; print_r($orderobjArr);
						$invoiceid=$this->CI->payment->add_payments_transcation((array)$parent_distributor_product,(array)$parent_distributor_arr,$parent_distributor_arr->currency_info);
						
						//echo "<pre> Produyct ARR ::::".$invoiceid; print_r($product_info);
						//echo "<pre> ACCOUNT ARR ::::"; print_r($parent_distributor_arr);
					}*/
				     }
				}
			}
		}
		if($parent_commission && !empty($parent_array)){ 
			$this->product_commission($parent_array,$parent_key_arr);
		}  
	     	return $parent_order_id ;	
	}
	function product_commission($account_info,$parent_key_arr){ 	
		array_multisort($parent_key_arr, SORT_DESC, $account_info);
		foreach($account_info as $accountkey => $accountvalue){ 
			$userdata = $accountvalue; 
			if( $accountvalue->reseller_id > 0){
				$accountvalue = $account_info[$accountkey+1];
				$accountvalue->product_info->invoice_type = "credit";
				$accountvalue->product_info->is_apply_tax = "false";
				$accountvalue->product_info->price = $accountvalue->commission;
				$accountvalue->product_info->description = $accountvalue->description;
				$this->manage_commission($accountvalue,$userdata,$accountvalue->currecny_info);
			}
		}
	} 
	function manage_commission($parent_data,$userdata,$account_currency_info){
		$commissionarr = array("product_id"=>$parent_data->product_info->product_id,
					"order_id"=>$parent_data->order_item_id,
					"accountid"=>$userdata->id,
					"reseller_id"=>$userdata->reseller_id,"payment_id"=>0,
					"amount"=>$userdata->product_info->price,
					"commission"=>$parent_data->commission,
					"notes"=>$parent_data->description,
					"commission_rate"=>$parent_data->product_info->commission,
					"commission_status"=>"PAID","creation_date"=>gmdate('Y-m-d H:i:s'));

		$this->CI->db->insert("commission",$commissionarr);
		$commission_last_id = $this->CI->db->insert_id();
		$parent_data->product_info->charge_type = "COMMISSION";
		$parent_data->product_info->payment_by = "Manual";
		$parent_data->product_info->order_item_id= $parent_data->order_item_id;
		$last_payment_id=$this->CI->payment->add_payments_transcation((array)$parent_data->product_info,(array)$parent_data,$parent_data->currecny_info);
		if($last_payment_id){
				$this->CI->db->where("id",$commission_last_id);
				$this->CI->db->update("commission",array("payment_id"=>$last_payment_id));
		}   
	}
	function generate_order($product_info,$account_info,$created_by_accountinfo,$parent_order_id,$account_currency_info){

		$product_info->quantity = (isset($product_info->quantity) && $product_info->quantity !='' )?$product_info->quantity:1;
		$system_config = common_model::$global_config ['system_config'];
		$from_currency = Common_model::$global_config ['system_config'] ['base_currency'];
		$order_insert_array = array(
				"order_id" =>crc32(uniqid()),
				"parent_order_id" => $parent_order_id,
				"order_date"=>gmdate("Y-m-d H:i:s"),
				"order_generated_by"=>$created_by_accountinfo['id'],
				"payment_gateway"=>$product_info->payment_by,
				"payment_status"=>$product_info->payment_status,
				"accountid"=>$account_info->id,
				"reseller_id"=>$account_info->reseller_id,
				"ip"=>$this->getRealIpAddr()
				);		

		$this->CI->db->insert("orders",$order_insert_array);
		$last_id = $this->CI->db->insert_id();

		$order_item_array = array(
					"order_id" =>$last_id,
					"product_category" =>$product_info->product_category,
					"product_id" =>$product_info->id,
					"quantity"=>$product_info->quantity,
					"price"=>($product_info->quantity*$product_info->price),
					"setup_fee"=>($product_info->quantity*$product_info->setup_fee),
					"billing_type"=>$product_info->billing_type,
					"billing_days"=>$product_info->billing_days,
					"free_minutes"=>$product_info->free_minutes,
					"accountid"=>$account_info->id,
					"reseller_id"=>$account_info->reseller_id,
					"billing_date"=>gmdate("Y-m-d 00:00:01"),
					"next_billing_date"=>($product_info->billing_days == 0)?gmdate('Y-m-d 23:59:59', strtotime('+10 years')):gmdate("Y-m-d 23:59:59",strtotime("+".($product_info->billing_days-1)." days")),
					"is_terminated"=>0,
					"termination_date"=>"",
					"from_currency"=>$from_currency,
					"exchange_rate"=>$account_currency_info['currencyrate'],
					"to_currency"=>$account_currency_info['currency']
					);
	    $this->CI->db->insert("order_items",$order_item_array);
	    $order_item_id = $this->CI->db->insert_id();
	    return $last_id;
	}

	function get_order_details($orderid){

		$order_data = $this->CI->db_model->getJionQuery('orders', 'orders.id,orders.parent_order_id, order_items.product_category,order_items.id as order_item_id,order_items.product_id,
order_items.price,order_items.setup_fee,order_items.accountid,order_items.reseller_id,
order_items.billing_type,order_items.billing_days,order_items.free_minutes,order_items.billing_date,order_items.quantity', array('orders.payment_status'=>'PENDING','orders.id'=>$orderid),'order_items','orders.id=order_items.order_id','inner','', '','','');
		if($order_data->num_rows > 0 ){
  
			return $order_data->result_array()[0];

		}
		return false;
	}

	function update_order_status($orderdata,$parent_order_id,$temp_arr,$payment_status,$userdata){

		$is_apply_commission = "false";
		$total_commission = 0.00;
		$parent_array[$userdata['id']] = (object)$userdata;
		$parent_key_arr[] =$userdata['id'];	

		$temp_orderdata = $orderdata;
		$temp_orderdata['price'] = $orderdata['price']+$orderdata['setup_fee'];

		$description = $temp_arr['description'];
		if($parent_order_id > 0){

			$parent_comm_arr = array();

			while($parent_order_id > 0){
				$order_data = $this->get_order_details($parent_order_id);
				$where = array ('id' => $order_data['accountid']);
				$account_info = ( array ) $this->CI->db->get_where( "accounts", $where )->result_array()[0]; 
				$where = array ('id' => $account_info['currency_id']);
				$currency_info = ( array ) $this->CI->db->get_where( "currency", $where )->result_array()[0];

				$orderdata['charge_type'] = $this->CI->common->get_field_name("code","category",array("id"=>$orderdata['product_category']));
				$temp_arr['invoice_type'] = ($order_data['product_category'] == 3) ? "credit":"debit";
				$temp_arr['description']  = "Order has been generated for Product (".$orderdata['name'].")";
				$order_data['invoice_type'] = ($order_data['product_category'] == 3) ? "credit":"debit";
				$order_data['is_update_balance'] = "true";
				$order_data['payment_by'] = "Paypal";
				$order_data['price']=$order_data['price']+$order_data['setup_fee'];
				$order_data = array_merge($order_data,$temp_arr);
 				$invoiceid =$this->CI->payment->add_payments_transcation($order_data,$account_info,$currency_info);	
				if($order_data['product_category'] != 3 && $account_info['posttoexternal'] == 0){
					$order_data['INV_DIRECT_PAY'] = "true";
					$order_data['invoiceid'] = $invoiceid;
					$order_data['is_update_balance'] = "true";
					$order_data['payment_by'] = "Paypal";
					$order_data['description'] =  "Payment has been received from customer ".$account_info['first_name']." ( ".$account_info['number']." )";
					$invoiceid =$this->CI->payment->add_payments_transcation($order_data,$account_info,$currency_info);
				}
				$parentdata = (object)$account_info;	
				$product_info = $this->get_account_product_info($orderobjArr,(object)$account_info,array('product_id'=>$order_data['product_id']));

				if($parentdata->is_distributor == 1 && $parentdata->type == 1 && $product_info->is_optin == 0){

						$parent_array[$userdata['id']]->product_info = $product_info;

						$parent_array[$parentdata->id] = $parentdata;
						$parent_key_arr[] = $parentdata->id;
						$parent_array[$parentdata->id]->currecny_info = $currency_info;
						$parent_array[$parentdata->id]->product_info = $product_info;
						$parent_array[$parentdata->id]->order_item_id= $order_data['order_item_id'];
						$parent_array[$parentdata->id]->product_id= $order_data['product_id'];
						$parent_array[$parentdata->id]->description= "Product (".$product_info->name.") commission has been credited";
						$product_info->price = ($product_info->price+$product_info->setup_fee);

						if($parentdata->reseller_id > 0){ 
							$tempcomm = 0.00;
							$parent_reseller = $this->CI->common->get_parent_info($parentdata->id, 0);
							$max_count = (count((explode(",",rtrim($parent_reseller,",")))));

							$comm_rate = $product_info->commission;
							$product_amount = ($product_info->price*$orderdata['quantity']);

							for($i=1; $i<=$max_count; $i++){
								$tempcomm = (($product_amount*$comm_rate)/100);
								$product_amount =  $tempcomm;
							}
							$parent_array[$parentdata->id]->commission = $tempcomm;
	
							$total_commission = ($total_commission + $tempcomm);
						}else{  

							$parent_commission = (($product_info->price*$orderdata['quantity']*$product_info->commission)/100);
							$parent_commission = ($parent_commission - $total_commission);
							$parent_array[$parentdata->id]->commission = $parent_commission;
						}

						$is_apply_commission = "true";
					}

				if($account_info['is_distributor'] == 0 && $account_info['type'] == 1 && $product_info->is_optin == 0 ){

					$orderobjArr = array();
					$prev_amount = $temp_orderdata['price'];
					$temp_orderdata = $order_data;
					$order_data['INV_DIRECT_PAY'] = "true";
					$order_data['is_update_balance'] = "true";
					$order_data['payment_by'] = "Paypal";
					$order_data['price'] = ($order_data['quantity'] > 1)?(($order_data['price']*$order_data['quantity'])-$prev_amount):$prev_amount-$order_data['price'];
					$order_data['invoice_type'] = "credit";
					$order_data['invoiceid'] = $invoiceid;
					$order_data['is_apply_tax'] = "false";
					$order_data['description'] =  "Payment has been received from customer ".$account_info['first_name']." ( ".$account_info['number']." )";

					$invoiceid =$this->CI->payment->add_payments_transcation($order_data,$account_info,$currency_info);
					
				}
				$parent_order_id = $order_data['parent_order_id'];
				$temp_arr['description']  = "Order has been generated for Product (".$orderdata['name'].")";
				$this->CI->db_model->update ( "orders",array("payment_status"=>"PAID"),array("id"=>$order_data['id']));
			} 

			if($userdata['is_distributor'] == 1  && $orderdata['product_category'] != '4' && $is_apply_commission == "true"){  				
				$this->product_commission($parent_array,$parent_key_arr);

			}
		}
	}
	 function getRealIpAddr(){

			    if (!empty($_SERVER['HTTP_CLIENT_IP']))  
			    {
			      $ip=$_SERVER['HTTP_CLIENT_IP'];
			    }
			    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   
			    {
			      $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
			    }
			    else
			    {
			      $ip=$_SERVER['REMOTE_ADDR'];
			    }
			    return $ip;
	 }


	function get_product_info($accountdata,$product_id){
		if($accountdata['type'] == '1'){
			$product_query = $this->CI->db_model->getJionQuery('products', 'products.id,products.name,products.product_category,products.buy_cost,products.commission,products.price,products.setup_fee,products.billing_type,products.billing_days,products.free_minutes,products.status,products.last_modified_date,reseller_products.product_id,reseller_products.setup_fee,reseller_products.is_optin', array('products.status'=>0,'reseller_products.product_id'=>$product_id,'reseller_products.is_optin'=>0,'products.is_deleted'=>0), 'reseller_products','products.id=reseller_products.product_id', 'inner','','','DESC','products.id');

			if($product_query->num_rows > 0){
			   return $product_query->result_array()[0];
			}else{
				return array("error"=>"Product not found.");
			}

		}else{
			if($accountdata['reseller_id'] > 0){
				if($accountdata['type'] == 0){
					$product_info = $this->CI->db_model->getJionQuery('products', 'products.id,products.name,products.product_category,products.buy_cost,products.commission,reseller_products.price,reseller_products.setup_fee,products.billing_type,products.billing_days,reseller_products.free_minutes,products.status,products.last_modified_date,reseller_products.product_id,reseller_products.setup_fee,reseller_products.is_optin', array('products.status'=>0,'reseller_products.product_id'=>$product_id,'reseller_products.account_id'=>$accountdata['reseller_id'],'products.is_deleted'=>0), 'reseller_products','products.id=reseller_products.product_id', 'inner','','','DESC','products.id');

				}else{
					$product_info = $this->CI->db_model->getJionQuery('products', 'products.id,products.name,products.product_category,products.buy_cost,products.commission,reseller_products.price,reseller_products.setup_fee,products.billing_type,products.billing_days,reseller_products.free_minutes,products.status,products.last_modified_date,reseller_products.product_id,reseller_products.setup_fee,reseller_products.is_optin', array('products.status'=>0,'reseller_products.product_id'=>$product_id,'reseller_products.is_optin'=>0,'reseller_products.account_id'=>$accountdata['reseller_id'],'products.is_deleted'=>0), 'reseller_products','products.id=reseller_products.product_id', 'inner','','','DESC','products.id');
				}

			}else{ 
				$product_info = $this->CI->db_model->getSelect("*","products",array("id"=>$product_id,"status"=>"0",'is_deleted'=>0));
			}
			
			if($product_info->num_rows > 0 ){
			    	return $product_info->result_array()[0];
			}else{
				return array("error"=>"Product not found.");
			}
		}
	}
	
	function get_parent_product_info($product_id){
				$product_info = $this->CI->db_model->getSelect("*","products",array("id"=>$product_id,"status"=>"0","is_deleted" =>"0"));
			if($product_info->num_rows > 0 ){
			    	return $product_info->result_array()[0];
			}else{
				return array("error"=>"Product not found.");
			}
	}	


	

}
?>
