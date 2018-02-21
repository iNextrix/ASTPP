
<? extend('master.php') ?>
<?php error_reporting(E_ERROR);?>
<? startblock('extra_head') ?>
<script type="text/javascript">
    $(document).ready(function() {
        $("#gross_amount").change(function(e){
            var amt = $("#gross_amount").val();
            var tax = (amt*1)/100;
            var Final_amt = parseInt(amt)+parseFloat(tax);
            var from_cur = "<?= $from_currency?>";
            var to_cur = "<?= $to_currency?>";
            $.ajax({
                type:'POST',
                url: "<?= base_url()?>/user/user_payment/GET_AMT/",
                data:"value="+Final_amt, 
                success: function(response) {
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


<div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">
    <div class="portlet-header ui-widget-header"><!--< ?php echo isset($account)?"Edit":"Create New";?> Account-->
        <?=$page_title?>
        <span class="ui-icon ui-icon-circle-arrow-s"></span>
    </div>
    <div style="color:red;margin-left: 60px;">
        <?php if(isset($validation_errors))echo $validation_errors; ?> 
    </div>
    <div>
      <table style="margin-left:20px;">
          <tr><td width="50%"  height="40"><lable>Enter Recharge Amount In <?= $from_currency?>:</lable></td>
	  <td width="50%"><input type="text" name="gross_amount" id="gross_amount" value="0" class="field text large "></td></tr>

          <tr><td width="50%" height="40">Tax Rate: (<?= $paypal_tax;?>%)</td>
             <td><input type="text" name="tax_amount" id="tax_amount" readonly value="0"  class="field text large "></td></tr>
          <tr><td width="50%" height="40">Your Amount In <?= $to_currency?>:</td>
              <td><span id="amt_in_currency" style="color:green; font-weight: bold;">0 <?= $from_currency?> is equels to 0 <?= $to_currency?></span></td></tr>          
      </table>
    </div>
    <form name="payment_form" id="payment_form" action="<?=$paypal_url?>" method="POST" onSubmit="return form_submit();">
      <table style="margin-left:20px;">
          <tr><td width="50%" height="40"><b>Net Payable Amount in <?= $to_currency?>:</b></td>
	  <td><input type="text" name="amount" readonly id="amount" value="0"  class="field text large "></td></tr>
	  <tr><td>
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
<!-- 		  <input type="hidden" name="areaid" value="224"> -->
 		  <!--<input type="hidden" name="amount" value="1.00">--> 
	  </td></tr>
	  <tr><td height="40"><input type="submit"  class="ui-state-default float-center ui-corner-all ui-button" value="Submit" name="submit"/></d></tr>
      </table>
    </form>
</div>


<? endblock() ?>
<? startblock('sidebar') ?>
<? endblock() ?>
<? end_extend() ?> 
