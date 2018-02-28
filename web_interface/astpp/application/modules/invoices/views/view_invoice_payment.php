<? extend('master.php') ?>
<? startblock('extra_head') ?>

 <link href="<?= base_url() ?>assets/css/invoice.css" rel="stylesheet">
<script type="text/javascript">
    $(document).ready(function() {
      
        $("#gross_amount").change(function(e){
	    var amt = $("#gross_amount").val();

            var Final_amt = parseFloat(amt);
            var from_cur = "<?= $from_currency?>";
            var to_cur = "<?= $system_currency?>";
            var new_amt= '0';
            $.ajax({
                type:'POST',
                url: "<?= base_url()?>/user/user_payment/GET_AMT/",
                data:"value="+Final_amt, 
                success: function(response) {
///		    alert(response);
                    $("#amt_in_currency").html(Final_amt +" "+from_cur + " To " + response +" "+to_cur);
                    $("#amount").val(response.trim());
                }
            })
        })
    })
  
</script>
<?php endblock() ?>
<?php startblock('page-title') ?>
<?= $page_title ?>
<?php endblock() ?>
<?php startblock('content') ?>


<div class="container">
  <div class="row">
  <section class="slice color-three padding-b-20 col-xs-push-2 col-md-8">
  <div class="col-md-12">
  <br/>
  </h2>	        	        	            
  </div>
  <div class="col-md-12" style="border: 1px solid #ccc; border-radius: 5px;">    
    <?php  if ($logintype == 0) { ?>  
    <form name="payment_form" id="payment_form"  method="POST" >
  	<?php } else {?>
            <form name="admin_form" id="admin_form" action="/invoices/invoice_admin_payment/" method="POST">
   		<?php } ?>
	<br/>
  <table class="invoice_table" width="100%" border="0" >
    <tr class="form_invoice_head">
	
         <td width="50%">
	  <table class="invoice_table1">
		<tr style="color:#163B80;">
			<th><h4><b>Customer Details  </b></h4></th>
		</tr>
		<tr>
			<td>
		 <font style="font-weight:bold; " >Account Number : </font> <font ><span style="font-size:bold;color:green;"><?php echo $accountdata['number']; ?></span></font></font> 

		</td>
		</tr>
		<tr>
			<td>
		 <font style="font-weight:bold; " >Name : </font> <font color="#a09d9d"><?php echo $accountdata['first_name']; ?> <?php echo $accountdata['last_name']; ?> </font></font> 

		</td>
		</tr>		
		<tr>
			<td>
	 		 <font style="font-weight:bold;" >Email :</font> <font style="color:#a09d9d;" >
				 <?php  
				if ($accountdata['email'] != "") {
					$attac_exp = explode(",", $accountdata['email']);
					foreach ($attac_exp as $key=>$value) {
						if ($value != '') {
							echo "<br/>".$value;
						}
					}
				}				
				 ?> 

			 </font>
		 </td>
		</tr>
	  </table>



</td>
	    <td width="50%">
		 <table class="invoice_table1  pull-right">
		<tr style="color:#163B80;";><th><h4><b>Invoice Details </b></h4></th></tr>
		<tr>
		 <td>
		   <b>Invoice Number : </b><span style="color:#a09d9d;"><?php echo $invoice_prefix; ?><?php echo $prefix_id; ?> </span> 
		 </td>
	        </tr>
	<tr>
		<td>
		 		<b>From Date : </b><span style="color:#a09d9d;"> <?php echo date('Y-m-d', strtotime($from_date)) ; ?></span>
		 </td>
	</tr>
	<tr>
		<td>
 		<b>Due Date : </b><span style="color:#a09d9d;"><?php 
				echo date('Y-m-d', strtotime($payment_due_date)) ;
		?></span>
		 </td>
	</tr>
		</table>
</td>
	</tr>
	</table>
<table width="100%"  border="1" colspan="2" class="invoice_table3 pull-right">
</table>
<table width="50%"  border="1" colspan="2" class="invoice_table4 pull-right">
		<tr style="">
		 <td><b >Invoice Amount :</b></td>
<?php
if ( ! empty($invoice_date)) {
 ?>
		 <td><span class="pull-right" style="color:#3278b6"><b><?php echo $this->common->currency_decimal($amount); ?> <?php  echo $to_currency; ?></b> </span></td>
		 <input type="hidden" name="total_amount" id="total_amount" class="article" value="<?php echo $amount; ?>" >
		  <input type="hidden" readonly name="recharge" value="paypal_invoice">
	 </tr> 
</table>
</br></br>
      	<th><h5><b style="color:#163B80;";>RECHARGE HISTORY   </b></h5></th>
        <table width="100%"  border="1" colspan="2" class="invoice_table2">
	 <tr style="background-color:#375c7c;color:#fff;height:40px;">
	   <th width="70%">
	  	Description
	   </th>
	   <th><div  class="pull-right">
	  	Amount
		</div>
	   </th>
	 </tr> 
	 <?php
$paypal=0;
 foreach($invoice_final_query as $value){
 
	if($value['item_type'] != 'INVPAY' && $value['item_type'] != 'PAYMENT')    {
	   $debit=$value['debit'];
	   $credit=$value['credit'];
	   $paypal+=$credit;
	   $created_date=$value['created_date'];
	   $paypalid=$value['item_id'];
	   $outstanding=$amount-$paypal;
	   $amount_visible='0000';
      
      
     
?>	
 	 <tr style="height:20px;">
			 <td><?php echo $value['description']; ?>  </td>
			 <td><div class="pull-right"><?php echo $this->common->currency_decimal($this->common_model->calculate_currency($debit)); ?> <?php  echo $to_currency;  ?></div></td>
			 <input type="hidden" name="new_amount" value="<?php echo $this->common->currency_decimal($this->common_model->calculate_currency($debit)); ?>">
			 
			
	 </tr> 
	 	
    	 <?php } } ?>
    	 	<tr>
    	 	</tr>
    	 
    	</table>  
<br/>    
     <?php  } ?>
      	<th><h5><b style="color:#163B80;";>PAYMENT HISTORY   </b></h5></th>
      
      <table width="100%"  border="1" colspan="2" class="invoice_table2">
    	
	
	 <tr style="background-color:#375c7c;color:#fff;height:40px;">
	
			<th width="20%">
			
			  	 Date
			    
			 </th>
			 <th width="60%">
			  	Description
			    
			 </th>
			  
			  <th><div  class="pull-right">
			  	Amount
				</div>
			  	
			  </th>
			 
			
	 </tr> 
	 <?php
$paypal=0;
if($invoice_date){
 foreach($invoice_total_query as $value){
 
     
	   $debit=$value['debit'];
	   $credit=$value['credit'];
	   $paypal+=$credit;
	   $created_date=$value['created_date'];
	   $paypalid=$value['item_id'];
	   $outstanding=$amount-$paypal;
	   $amount_visible='0000';
      
      
     
?>	
 	 <tr>
			 <td> <?php echo date('Y-m-d	', strtotime($created_date)) ?></td>
			 <td><?php echo $value['description']; ?>  <?php echo $paypalid; ?></td>
			 <td><div class="pull-right"><?php echo $this->common->currency_decimal($this->common_model->calculate_currency($credit)); ?> <?php  echo $to_currency; ?></div></td>
			 <input type="hidden" name="new_amount" value="<?php echo $this->common->currency_decimal($this->common_model->calculate_currency($debit)); ?>">
			 
			
	 </tr> 
	 	
    	 <?php   }
	} ?>
    	 	<tr  style="background-color:#f6f6f6;">
	    	 	<th  colspan="2"  width="30%" >
	    	 	  <span class="pull-right" style="color:#474747;">Paid Amount</span>
	    	 		
	    	 	</th>
	    	 	<td align="right">
	    	 	<!--0-->
	    	 	<b><?php if ($paypal) { echo   $this->common->currency_decimal($this->common_model->calculate_currency($paypal)); } else { echo $this->common->currency_decimal($this->common_model->calculate_currency($amount_visible)); ;}?> <?php  echo $to_currency; ?></b>
	    	 	</td>
	    	 	
	    	 	
	    	 	
    	 	</tr>
    	 	<tr>
	    	 	
	    	 	
	    	 	
	    	 	
    	 	</tr>
    	
    	 	
    	 
    	</table>
    	<table width="50%"  border="1" colspan="2" class="invoice_table4 pull-right">
    
		
		<tr style="">
		 <td><b >Outstanding Amount :</b></td>
	<td><span class="pull-right" style="color:#3278b6"><b><?php if ($debit == '') { echo  $this->common->currency_decimal($outstanding); ?> <?php  echo $to_currency; } else {  echo $this->common->currency_decimal($outstanding); ?> <?php  echo $to_currency; } ?></b> </span></td>
		 <input type="hidden" name="total_amount" id="total_amount" class="article" value="<?php echo  $this->common->currency_decimal($amount); ?>" >
	 </tr> 
</table>
</br></br>
    	
<table class="invoice_table" width="100%" border="0" >
    <tr class="form_invoice_head">
	<td width="50%" valign="top">
	</td>
	<td width="50%" valign="top" style="border-left:2px solid #ccc;">
 <?php  if ($logintype == 0 || $logintype == 3) { ?>
	  <table class="invoice_table1  pull-right">
	    <tr style="border-bottom:2px solid #dfdfe1; color:#474747;"><th colspan="2">Payment Amount :</th></tr>
	      <tr>
	        <td colspan="2" >
	           <label style=" float: left;"> Please Insert Amount:</label>
	 	   <input type="text"  style="margin-top:5px;" name="gross_amount" value="" id="gross_amount" class="form-control col-md-8">
	 	  <span id="une" style=":#3ca7c9;margin-left:0px; margin-top:20px;  color:red;"></span> 
 	        </td>
              </tr>
	      <tr>
		<td class="col-md-6" style="vertical-align:middle;">
		  <label style=" float: left;">Your Amount In <?= $system_currency?>:</label>
		</td>
   	     </tr>	
	     <tr>
		<td colspan="3" >
		  <span id="amt_in_currency" style="color:green; font-weight: bold;text-align:left; font-size: 18px;">0 <?= $to_currency?> is equals to 0 <?= $system_currency?></span>
		</td>
  	     </tr>	
	     <tr>
		<td colspan="2" >
		  <label style=" float: left;">Net Payable Amount in <?= $system_currency?>:</label>
	  	  <input type="text" name="amount" readonly id="amount" value="0"  class="form-control">	 
		</td>
  	     </tr>	
	     <tr style=" ">
		<td class="col-md-6" style="vertical-align:top; ">
		    Special Note :
 	        </td>
		<td colspan="2" >
	 	  <textarea class="form-control col-md-8"  name="item_name" id="description" cols=10" rows="3"></textarea>
		  <span id="description1" style=":#3ca7c9;  color:red;"></span> 
		</td>
	     </tr>
	</table>
<?php }elseif ($logintype == 1) {?>
	  <table class="invoice_table1  pull-right">
	    <tr style="border-bottom:2px solid #dfdfe1; color:#474747;"><th colspan="2">Payment Amount :</th></tr>
	      <tr>
	        <td colspan="2" >
	           <label style=" float: left;"> Please Insert Amount:</label>
	 	   <input type="text"  style="margin-top:5px;" name="gross_amount" value="" id="gross_amount" class="form-control col-md-8">
	  	  <input type="hidden" name="amount" readonly id="amount" value="0"  class="form-control">	 
	 	  <span id="une" style=":#3ca7c9;margin-left:0px; margin-top:20px;  color:red;"></span> 
 	        </td>
              </tr>
	     <tr style=" ">
		<td class="col-md-6" style="vertical-align:top; ">
		    Special Note :
 	        </td>
		<td colspan="2" >
	 	  <textarea class="form-control col-md-8"  name="item_name" id="description" cols=10" rows="3"></textarea>
		  <span id="description1" style=":#3ca7c9;  color:red;"></span> 
		</td>
	     </tr>
	</table>

<?php } else { ?>
	<table class="invoice_table1  pull-right">
  	  <tr style="border-bottom:2px solid #dfdfe1; color:#474747;"><th colspan="2">Payment Amount :</th></tr>
  	    <tr>
		<td class="col-md-6" style="vertical-align:middle;">
			Please Insert Amount : 
		</td>
		<td colspan="2" >
			<input type="text"  style="margin-top:5px;" name="amount" value="<?php echo $this->common->currency_decimal($outstanding); ?>" id="amount" class="form-control col-md-8">
			<span id="une" style=":#3ca7c9;margin-left:0px; margin-top:20px;  color:red;"></span> 
		</td>
	    </tr>
  	    <tr style=" ">
		<td class="col-md-6" style="vertical-align:top; ">
			Special Note :
		</td>
		<td colspan="2" >
			<textarea class="form-control col-md-8"  name="item_name" id="description" cols=10" rows="3"></textarea>
			<span id="description1" style=":#3ca7c9;  color:red;"></span> 
		</td>
	     </tr>
	</table>
<?php } ?>
       </td>
     </tr>
</table>
	

	
	
<div class="col-md-12 padding-b-20">
<div class="" style="margin-left:400px;">
 <?php  if ($logintype == 0) { ?>
<?php
if ($logintype == 0 && $accountdata['paypal_permission'] == 0) { 
?>
  <input class="btn btn-line-parrot search_generate_bar"  name="Paynow" id="Paynow" value="Pay With Paypal" type="button" onclick="return payment('<?php echo $paypal_url; ?>'); "> 
<input id="ok" class="btn btn-line-sky margin-x-10" type="button" value="Cancel" onclick="window.history.back();"; name="action">
 <?php } } ?>
 <div  class="margin-t-20 ">
  <?php  if ($logintype == 2 || $logintype == 1) { ?>
  <input class="btn btn-line-parrot search_generate_bar" name="action" name="save" id="save" value="Pay Now" type="button" onclick="return amidn_pay(); "> 
<input id="ok" class="btn btn-line-sky margin-x-10" type="button" value="Cancel" onclick="window.history.back();" name="action">
  </div>
 </div>
  </div>

 <?php }?>	

 </center>
<br/>
 

	<div class="msg_error" style="width:70%; margin-left:auto;margin-right:auto;">
		
	</div>

</div>
</li>    

 
  
  
      
         
												<div class="form-group">
						
													 
													 
						  <input type="hidden" readonly name="cmd" value="_xclick">
		  <input type="hidden" readonly  name="business" value="<?=$paypal_email_id?>">
		 <!-- <input type="hidden" readonly name="item_name" value="ASTPP Store">-->
 		  <input type="hidden" readonly name="item_number" value="<?=$id?>"> 
		  <input type="hidden" readonly name="LC" value="US">
		  <input type="hidden" readonly name="country" value="USA">
 		  <input type="hidden" readonly name="quantity" value="1"> 
		  <input type="hidden" readonly name="rm" value="2">
		  <input type="hidden" readonly name="no_shipping" value="1">
		  <input type="hidden" readonly name="PHPSESSID" value="<?=session_id(); ?>">
		  <input type="hidden" readonly name="currency_code" value="USD">
		  <input type="hidden" readonly name="notify_url" value="<?=$notify_url?>">
		  <input type="hidden" readonly name="return" value="<?=$response_url?>">
                  <input type="hidden" readonly name="cancel_return" value="<?=$cancel_return?>">
                  <input type="hidden" readonly name="custom" id='custom' value="<?php echo $accountdata['id']; ?>">
                  <input type="hidden" name="new_amount" id="new_amount" readonly value="<?php echo $amount; ?>" >
                   </form>
                   
	      </div>
	  
	     
	      <br/>
	      <center>
	      
	     
	      
	      
												  </div>
									  
											
         
     </div>
 </div>
  	
      
      
 </div>
  </div>
                </div>     
    </section>        
  </div>
</div>

<script>
function payment(url){
  var text =document.getElementById('amount').value;
  var amount =document.getElementById('new_amount').value;
  var description =document.getElementById('description').value;
    var new_amount=parseFloat(document.getElementById('new_amount').value);
     var total_amount =parseFloat(document.getElementById('amount').value);
  if(text == '' || text <= '0'){
 
  document.getElementById('une').innerHTML = "Please Enter Amount.";
  payment_form.amount.focus();
  return(false);
  }
   else if(isNaN(text))
        {
         document.getElementById('une').innerHTML = "Please only enter numeric characters (Allowed input:0-9).";
      admin_form.amount.focus();
     return(false);
         
        }
        else if(description == ''){

   document.getElementById('description1').innerHTML = "Please Enter Description.";
  payment_form.Paynow.focus();
  return(false);
  }
  
  else if(new_amount >= total_amount){
          $(".search_generate_bar").attr('disabled','disabled');
        //payment_form.submit();
         document.payment_form.action = url;
	document.payment_form.submit();
    }
      
  else{
  document.getElementById('description1').innerHTML = "Payment amount should not be higher than the Invoice Amount.";
     admin_form.Paynow.focus();
    return(false);
 // alert("Payment amount should not be higher than the Invoice Amount. ");
  
  
  }
 
    
}
function amidn_pay(){
     var balance = "<php echo $amount; ?>";
     var text =document.getElementById('amount').value;
     var amount =document.getElementById('new_amount').value;
     var description =document.getElementById('description').value;
     var article = document.getElementsByClassName("article").value;
     var new_amount=parseFloat(document.getElementById('new_amount').value);
     var paypal='<?php echo $paypal; ?>';
     var total_amount =parseFloat(document.getElementById('amount').value);
      if(text == '' || text <= '0'){
     document.getElementById('une').innerHTML = "Please Enter Amount greater than zero.";
     admin_form.amount.focus();
     return(false);
     }

    else if(isNaN(text))
     {
     document.getElementById('une').innerHTML = "Please only enter numeric characters (Allowed input:0-9).";
      admin_form.amount.focus();
     //alert("Please only enter numeric characters (Allowed input:0-9)");
     return(false);
     }
    else if(description == ''){
    document.getElementById('description1').innerHTML = "Please Enter Description.";
    admin_form.Paynow.focus();
    return(false);
    }
    else if(new_amount >= total_amount){
         $(".search_generate_bar").attr('disabled','disabled');
        admin_form.submit();
        //alert('its done');
    }
    else{
        alert("please enter less amount then your current balance ");
       
    }      
}
</script>

<? endblock() ?>
<? startblock('sidebar') ?>
<? endblock() ?>
<? end_extend() ?>
