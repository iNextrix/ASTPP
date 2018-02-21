
<? extend('master.php') ?>
<?php error_reporting(E_ERROR);?>
<? startblock('extra_head') ?>
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
//		    alert(response);
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
<br/>
<?php endblock() ?>
<?php startblock('content')?>


   <section class="slice color-three">
	<div class="w-section inverse no-padding">
	  <div class="container">
        	<div class="row">
                  <div class="col-md-12" align=center style='margin-top:15px;' > 
		    <div style="color:red;margin-left: 60px;">
			<?php if (isset($validation_errors)) echo $validation_errors; ?> 
		    </div>
      
    <!--      <div class='col-md-12'><div style="width:565px;" ><label style="text-align:right;" class="col-md-6">Enter Recharge Amount In <?= $from_currency?>:</label><input type="text" name="gross_amount" id="gross_amount" value="0" class="col-md-5 form-control"></div>
			
			</div>  -->
          <div class='col-md-12'><div style="width:565px;" ><label style="text-align:right;" class="col-md-6">Enter Recharge Amount In <?= $from_currency?>:</label><input type="text" name="gross_amount" id="gross_amount" value="0" class="col-md-5  form-control"></div>
			
			</div>
          <div class='col-md-12'><div style="width:565px;" ><label style="text-align:right;" class="col-md-6">Tax Rate: (<?= $paypal_tax;?>%):</label><input type="text" name="tax_amount" id="tax_amount" readonly value="0"  class="col-md-5 form-control"></div>
			
			</div>			
          <div class='col-md-12'><div style="width:565px;" ><label style="text-align:right;" class="col-md-6">Your Amount In <?= $to_currency?>:</label><span id="amt_in_currency" class='col-md-5 no-padding' style="color:green; font-weight: bold;text-align:left;">0 <?= $from_currency?> is equals to 0 <?= $to_currency?></span></div>
			
			</div>			

    
    <form name="payment_form" id="payment_form" action="<?=$paypal_url?>" method="POST" onSubmit="return form_submit();">
       <div class='col-md-12'><div style="width:565px;" ><label style="text-align:right;" class="col-md-6">Net Payable Amount in <?= $to_currency?>:</label><input type="text" name="amount" readonly id="amount" value="0"  class="col-md-5 form-control"></div>
			
			</div>
                 <div> 
		  <input type="hidden" readonly name="cmd" value="_xclick">
		  <input type="hidden" readonly  name="business" value="<?=$paypal_email_id?>">
		  <input type="hidden" readonly name="item_name" value="ASTPP Store">
 		  <input type="hidden" readonly name="item_number" value="<?=$accountid?>"> 
		  <input type="hidden" readonly name="LC" value="US">
		  <input type="hidden" readonly name="country" value="USA">
 		  <input type="hidden" readonly name="quantity" value="1"> 
		  <input type="hidden" readonly name="rm" value="2">
		  <input type="hidden" readonly name="no_shipping" value="1">
		  <input type="hidden" readonly name="PHPSESSID" value="<?=session_id();?>">
		  <input type="hidden" readonly name="currency_code" value="USD">
		  <input type="hidden" readonly name="notify_url" value="<?= base_url()?>login/paypal_response/">
		  <input type="hidden" readonly name="return" value="<?= base_url()?>login/paypal_response/">
                  <input type="hidden" readonly name="cancel_return" value="<?= base_url()?>/user/user_payment/">
                  <input type="hidden" readonly name="custom" id='custom' value="">
                  </div>
<!-- 		  <input type="hidden" name="areaid" value="224"> -->
 		  <!--<input type="hidden" name="amount" value="1.00">--> 
	  
	  
	 
	  
	                    
		  <div class='col-md-12'>
		      <div style="width:565px;" >
			    <label style="text-align:right;" class="col-md-6"></label>
			    <img alt="paypal" src="<?php echo base_url();?>/assets/images/paypal_logo11.png" alt="Paypal" class='col-lg-5 no-padding'>
		      </div>			
		  </div>	
	                    
	
		  <div class='col-md-12'>	                 
			     <div class='col-lg-12 padding-t-10 padding-b-10'><input class="btn btn-success" name="action" value="Recharge" type="submit">
			     </div>

		  </div>
      
    </form>
</div>      
    </section>
 </div></div></div>  
    </section>
  </div>
</div>

<? endblock() ?>
<? startblock('sidebar') ?>
<? endblock() ?>
<? end_extend() ?> 
