
<script type="text/javascript">
window.addEventListener('DOMContentLoaded', function(){
         document.getElementById("payment_form").submit();
     });
      



</script>

<div class ="loader_page_bg">
<div class="container">
	<div class="content" ><br/><br/><br/>
		<h2 class="text-center"><i class="fa fa-cog fa-spin" style="font-size:72px;color:#1ed7d1"></i></h2><br/>
		<h3 class="text-center" style ="color:#fff">Please wait, we are redirecting you to the payment gateway...</h3>
		<form action="<?php echo $paypal_url; ?>" method="post" id="payment_form">


			
			<input type="hidden" readonly name="amount"  id="amount" value="<?php echo $total_amt; ?>">
	        	  <input type="hidden" readonly name="cmd" value="_xclick">
			  <input type="hidden" readonly  name="business" value="<?=$paypal_email_id;?>">
			  <input type="hidden" readonly name="item_name" value= <?php echo (isset($item_name) && $item_name !='')?$item_name:$product_info['name']; ?> >
			
			  <input type="hidden" readonly name="item_number" value="<?php echo $order_id;?>"> 
			  <input type="hidden" readonly name="LC" value="US">
			  <input type="hidden" readonly name="country" value="USA">
			  <input type="hidden" readonly name="quantity" value="1"> 
			  <input type="hidden" readonly name="rm" value="2">
			  <input type="hidden" readonly name="no_shipping" value="1">
			  <input type="hidden" readonly name="PHPSESSID" value="<?=session_id(); ?>">
	
			  <?php
			 $accountinfo = $this->session->userdata ( 'token' );
			 $accountinfo = ((isset($accountinfo)) && $accountinfo != '')?$accountinfo:$this->session->userdata ( "accountinfo" );
			 $system_config = common_model::$global_config ['system_config'];
			 $reseller_id = ($accountinfo['reseller_id'] > 0) ? $accountinfo['reseller_id']: 0;
			 $paypal_info= $this->db_model->getSelect("*","system",array("sub_group"=>'Paypal',"reseller_id"=>$reseller_id));
			 $accountinfo['from_currency'] = $this->common->get_field_name ( 'currency', 'currency', $accountinfo ["currency_id"] );
			 $accountinfo['is_supported'] = $this->common->get_field_name ( 'is_supported', 'currency', $accountinfo ["currency_id"] );
			 if($accountinfo['is_supported'] == 0){ ?>
				<input type="hidden" readonly name="currency_code" value="<?php echo $accountinfo['from_currency']; ?>">
			 <?php } else { ?>
			<input type="hidden" readonly name="currency_code" value="USD">
			 <?php } ?>
			  <input type="hidden" readonly name="notify_url" value="<?php echo (isset($notify_url) && $notify_url !='')?$notify_url: base_url().'pages/paypal_response/'; ?>"> 
			  <input type="hidden" readonly name="return" value="<?php echo (isset($return) && $return !='')? $return:base_url().'pages/paypal_response/';?>">
			  <!-- ASTPPCOM-727 Ashish start -->
			  <input type="hidden" readonly name="cancel_return" value="<?php echo(isset($cancel_return) && $cancel_return !='')?$cancel_return:base_url().'user/user_available_products/'; ?>">
			  <!-- ASTPPCOM-727 end -->
			  <input type="hidden" readonly name="custom" id='custom' value=<?php echo (isset($account_id) && $account_id > 0)?$account_id:$product_info['product_category']; ?>>
          		</div>
		</form>
	</div>
</div>
</div>
