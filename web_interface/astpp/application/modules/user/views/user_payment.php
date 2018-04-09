<? extend('master.php') ?>
<? startblock('extra_head') ?>
<style>
body{
	
	background-color: #B7C1CF !important;
}
</style>
<script type="text/javascript">
    $(document).ready(function() {
      
        $("#gross_amount").change(function(e){
	    var amt = $("#gross_amount").val();
            var paypal_tax = '<?=$paypal_tax?>';
            var tax = (amt*paypal_tax)/100;
            var Final_amt = parseInt(amt)+parseFloat(tax);
            var from_cur = "<?= $from_currency?>";
            var to_cur = "<?= $to_currency?>";
            var new_amt= '0';
            
            $.ajax({
                type:'POST',
                url: "<?= base_url()?>/user/user_convert_amount/"+amt,
                data:"value="+new_amt, 
                success: function(response) {
		    
                    $("#custom").val(response);
                }
            })
            
            $.ajax({
                type:'POST',
                url: "<?= base_url()?>/user/user_payment/GET_AMT/",
                data:"value="+Final_amt, 
                success: function(response) {
					response = response.replace(",","");
                    $("#amt_in_currency").html(Final_amt +" "+from_cur + " To " + response +" "+to_cur);
                    $("#tax_amount").val(tax);
                    $("#tax_amount").val(tax);
                    $("#amount").val(response.trim());
                }
            })
        })
    })
function form_submit(){
    if($("#amount").val() > 0){
        return true;
    }else{
        alert("please enter recharge value");
    }

return false;
}    
</script>

<?php endblock() ?>
<?php startblock('page-title') ?>
<?=$page_title?>
<?php endblock() ?>
<?php startblock('content')?>

<div class="col-md-4 col-md-offset-4 margin-t-20">
<fieldset style="border:7px solid rgba(87, 127, 141, 0.33); border-radius: 4px; background-color:#fff;box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.16);"> 
	<div class="w-section inverse no-padding">
	  <div class="container">
        	<div class="row">
				
            <div class="col-md-12" style='margin-top:15px;' > 
		    <div style="color:red;text-align:center;">
			<?php if (isset($validation_errors)) {
	echo $validation_errors;
}
?> 
		    </div>
      
			<div class='col-md-12'>
				<h1 style="color: #30A1E9;font-size: 2em;text-align: center;"><?php echo gettext('Account Recharge')?></h1>
			</div>
			
          <div class='col-md-7 no-padding margin-t-10'>		
				  <label style=" float: left;"><?php echo gettext('Enter Recharge Amount In')?> <?= $from_currency?>:</label>
		  </div> 
		  <div class='col-md-5 margin-t-10'>
				  <input type="text" name="gross_amount" id="gross_amount" value="0" class="form-control">
		  </div> 
          <div class='col-md-7 no-padding'>	  
				  <label style=" float: left;"><?php echo gettext('Tax Rate:')?> (<?= $paypal_tax; ?>%):</label> 	 
		  </div>
		  <div class='col-md-5'>	  
				  <input type="text" name="tax_amount" id="tax_amount" readonly value="0"  class="form-control">	 
		  </div>		
          <div class='col-md-7 no-padding'>
				  <label style=" float: left;"><?php echo gettext('Your Amount In')?> <?= $to_currency?>:</label>
		  </div>			
		  <div class='col-md-5'>
				  <span id="amt_in_currency" style="color:green;text-align:left !important;font-size:11px; ">0 <?= $from_currency?> is equals to 0 <?= $to_currency?></span>
	      </div>
    
    <form name="payment_form" id="payment_form" action="<?=$paypal_url?>" method="POST" onSubmit="return form_submit();">
          <div class='col-md-7 no-padding  margin-t-10'>
				  <label style=" float: left;"><?php echo gettext('Net Payable Amount in')?> <?= $to_currency?>:</label>
		  </div>
		  <div class='col-md-5  margin-t-10'>
				  <input type="text" name="amount" readonly id="amount" value="0"  class="form-control">	 
		  </div>
          <div> 
			  <input type="hidden" readonly name="cmd" value="_xclick">
			  <input type="hidden" readonly  name="business" value="<?=$paypal_email_id?>">
			  <input type="hidden" readonly name="item_name" value="Billing Store">
			  <input type="hidden" readonly name="item_number" value="<?=$item_number?>"> 
			  <input type="hidden" readonly name="LC" value="US">
			  <input type="hidden" readonly name="country" value="USA">
			  <input type="hidden" readonly name="quantity" value="1"> 
			  <input type="hidden" readonly name="rm" value="2">
			  <input type="hidden" readonly name="no_shipping" value="1">
			  <input type="hidden" readonly name="PHPSESSID" value="<?=session_id(); ?>">
			  <input type="hidden" readonly name="currency_code" value="<?=$to_currency?>">
			  <input type="hidden" readonly name="notify_url" value="<?= base_url()?>login/paypal_response/">
			  <input type="hidden" readonly name="return" value="<?= base_url()?>login/paypal_response/">
			  <input type="hidden" readonly name="cancel_return" value="<?= base_url()?>/user/user_payment/">
			  <input type="hidden" readonly name="custom" id='custom' value="">
          </div>
 
		  <div class='col-md-12' align="center">	                 
			     <div class='col-lg-12 padding-t-10 padding-b-10'>
					 <input class="btn btn-line-parrot" name="action" value="Recharge" type="submit">
			     </div>
		  </div>
	  
		  <div class="col-md-12 margin-b-10 margin-t-10">
		      <div style="text-align:center;">
			    <img src="<?php echo base_url(); ?>/assets/images/paypal_logo11.png" alt="paypal">
		      </div>			
		  </div>

    </form>
</div>      
    
    </section>
 </div></div></div>  
    </section>
    </fieldset>
  </div>
  </div>
</div>

<? endblock() ?>
<? startblock('sidebar') ?>
<? endblock() ?>
<? end_extend() ?> 
