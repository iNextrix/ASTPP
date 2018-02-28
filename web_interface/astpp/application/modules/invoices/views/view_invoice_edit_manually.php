<? extend('master.php') ?>
<? startblock('extra_head') ?>

 <link href="<?= base_url() ?>assets/css/invoice.css" rel="stylesheet">
<?php endblock() ?>
<?php startblock('page-title') ?>
<?= $page_title ?>
<?php endblock() ?>
<?php startblock('content') ?>
<style>
table.datatable {
    border-collapse: separate;
    border-spacing: 1px;
}
table.datatable th {
    padding: 4px;
    background-color: #375c7c;
    font-weight: bold;
    text-align: center;
}
</style>
<script type="text/javascript" language="javascript">
 function form_confirm() {
       var confirm_string = 'Are you sure want to confirm this invoice ? once you confirm it, can not able to edit invoice again.';
        var answer = confirm(confirm_string);
        if(answer){
	    document.getElementById("payment_form").submit();
	}
	else{
		return false;
	}

}
 function calculate() {
	var sum = 0;
	var row_count= '<?= $row_count ?>';
	for (j = 1; j <= row_count; j++) {
		var amt = document.getElementById('invoice_amount_'+j).value;
        $('#invoice_amount_'+j).keypress(function (event) {
            return isNumber(event, this)
        });
		if(amt != ''){
			sum += parseFloat(amt);
		}
	}
	var taxe_rate=0;
        var taxes_acc='<?= $taxes_to_accounts; ?>';
	var tax_per = '<?= $total_tax ?>';
	var total_tax=sum*tax_per/100;
	$("#total_tax").html(total_tax.toFixed(2));
	document.getElementById("total_tax_input").value = total_tax.toFixed(2);
	$("#amount_val").html(sum.toFixed(2));
	document.getElementById("total_val").value = sum.toFixed(2);
	var abc= 0;
	var tax_cnt = '<?= $taxes_count; ?>';
	for (a = 0; a < tax_cnt; a++) {
		var taxes_rate = document.getElementById('total_tax_input_'+a).value;
		var tax_cut =  sum*taxes_rate/100;
		abc += tax_cut;
		$("#total_tax_"+a).html(tax_cut.toFixed(2));
		document.getElementById("abc_total_tax_input_"+a).value = tax_cut.toFixed(2);
	}
	var final_amt =sum+abc;
	$("#amount_val_final").html(final_amt.toFixed(2));
	document.getElementById("total_val_final").value = final_amt.toFixed(2);

    } 
     $(document).ready(function() {
	var row_count= '<?= $row_count ?>';
for (i = 1; i <= row_count; i++) {
         jQuery("#invoice_from_date_"+i).datetimepicker({format:'Y-m-d h:m:i'});		
	
}
    });
        function isNumber(evt, element) {
            var charCode = (evt.which) ? evt.which : event.keyCode
                    
            if ((charCode != 45 || $(element).val().indexOf('-') != -1) && (charCode != 46 || $(element).val().indexOf('.') != -1) && ((charCode < 48 && charCode != 8) || charCode > 57)){
                 return false;
                    
            }
            else {
                return true;
            }
            
        } 
</script>

<section class="slice color-three padding-b-20">
	<div class="w-section inverse no-padding">
    	<div class="container">
  <div class="row">
  <section class="slice color-three padding-b-20 col-xs-push-2 col-md-8">
  <div class="col-md-12">
  <br/>
  </h2>	        	        	            
  </div>
  <div class="col-md-12" style="border: 1px solid #ccc; border-radius: 5px;">    
            <form name="payment_form" id="payment_form" action="/invoices/invoice_manually_payment_edit_save/" method="POST">
	<br/>
	<input type='hidden' name="row_count" value="<?= $row_count ?>">
	<input type='hidden' name="taxes_count" value="<?= $taxes_count; ?>">
	<input type='hidden' name="count" value="<?= $count; ?>">
	<input type="hidden" id="total_tax_input" name="total_tax_input" value="<?= $total_tax_dis; ?>">
	<input type="hidden" id="total_val_final" name="total_val_final" value="<?= $amount; ?>">
	<input type="hidden" id="total_val" name="total_val" value="<?= $total_credit_dis; ?>" >
	<input type='hidden' name="invoiceid" value="<?= $id; ?>">
	<input type='hidden' name="accountid" value="<?= $accountdata['id']; ?>">
	<input type='hidden' name="reseller_id" value="<?= $accountdata['reseller_id']; ?>">
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
							echo $value;
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
		<tr style='border:2px;'>
		 <td height=35px style="padding-left:5px;"><b >Invoice Amount :</b></td>
		 <td><span class="pull-right" style="color:#3278b6"><b><?php echo $this->common->currency_decimal($amount); ?> <?php  echo $to_currency; ?></b> </span></td>
		 <input type="hidden" name="total_amount" id="total_amount" class="article" value="<?php echo $amount; ?>" >
		  <input type="hidden" readonly name="recharge" value="paypal_invoice">
	 </tr> 
</table>
</br></br>
      	<div class="col-md-12 no-padding" style="width: 50%; margin-top: -2.3%;">
	  <div class="col-md-4 no-padding"><h5><b style="color:#163B80;";>Invoice Note:</b></h5></div>
	  <div  class="col-md-8"><textarea id="invoice_notes" name="invoice_notes" style='height:38px;width:250px;' class="form-control col-md-3"><?= $invoice_notes; ?></textarea></div></div>
<br/>
      	<th><h5><b style="color:#163B80;";>Invoice Item</b></h5></th>
<table class="datatable" width="100%" border="0" cellspacing="1" cellpadding="3">
<tr style='background-color:#375c7c;color:#fff;height:40px;'>
	<th width="25%">Date</th>
	<th width="50%">Description</th>
	<th >Amount</th>
</tr> 
<?php
for ($i = 1; $i <= $row_count; $i++) {
if ($get_data['invoice_amount_'.$i] > 0) {
$invoice_amt = $this->common->currency_decimal($this->common_model->calculate_currency($get_data['invoice_amount_'.$i]));
} else {
$invoice_amt = '';
}
?>
<tr>
<td width="20" align="center">
<input id="invoice_from_date_<?= $i ?>" name="invoice_from_date_<?= $i ?>" type='text' class='form-control col-md-3'  value="<?= $get_data['invoice_from_date_'.$i]; ?>"  >
</td>
<td><textarea id="invoice_description_<?= $i ?>" name="invoice_description_<?= $i ?>" style='height:35px;' class="form-control"><?= $get_data['invoice_description_'.$i]; ?></textarea>
</td>
<td style="text-align:right;">
<input type='text' id="invoice_amount_<?= $i ?>" name="invoice_amount_<?= $i ?>" onkeyup="calculate()" class='form-control col-md-3'  style="text-align:right;" value="<?= $invoice_amt ?>" >
</td>

</tr>
<?php } ?>
<tr style='background-color:#375c7c;color:#fff;height:30px;'>
<td colspan="2" style="text-align:right;">Sub Total:&nbsp;
</td>
<td style="text-align:right;"><div id='amount_val' name='amount_val'><?= $this->common->currency_decimal($this->common_model->calculate_currency($total_credit_dis)); ?></div>
</td>
</tr>
<?php
$taxi = 0;
foreach ($taxes_to_accounts as $tax_val) {

?>
	<input type="hidden" id="total_tax_input_<?= $taxi; ?>" name="total_tax_input_<?= $taxi; ?>" value="<?= $tax_val['taxes_rate']; ?>" >
	<input type="hidden" id="abc_total_tax_input_<?= $taxi; ?>" name="abc_total_tax_input_<?= $taxi; ?>" value="<?= $this->common->currency_decimal($total_tax_dis[$taxi]); ?>" >
	<input type="hidden" id="description_total_tax_input_<?= $taxi; ?>" name="description_total_tax_input_<?= $taxi; ?>" value="<?= $tax_val['taxes_description']; ?>" >

<tr style='background-color:#375c7c;color:#fff;height:30px;'>
<td colspan="2" style="text-align:right;">Tax ( <?= $tax_val['taxes_description'] ?> ):&nbsp; 
</td>

<td style="text-align:right;"><div id="total_tax_<?= $taxi; ?>" name="total_tax_<?= $taxi; ?>"><?= $this->common->currency_decimal($total_tax_dis[$taxi]); ?></div>
</td>
</tr>

<? $taxi++; } ?>

<tr style='background-color:#375c7c;color:#fff;height:30px;'>
<td colspan="2" style="text-align:right;">Total Due:&nbsp;
</td>
<td style="text-align:right;"><div id='amount_val_final' name='amount_val_final'><?= $this->common->currency_decimal($amount); ?></div>
</td>
</tr>
    	</table>
</br></br>
   <center>  	
<div class="col-md-12 padding-b-20">
 <div  class="margin-t-20 ">
  <input class="btn btn-line-parrot search_generate_bar" name="save" id="save" value="Save" type="submit"> 
  <input class="btn btn btn-warning search_generate_bar margin-x-10" name="confirm" id="save" value="Confirm" type="button" onclick='form_confirm()'> 
<a href = "../invoice_list/">
<input id="ok" class="btn btn-line-sky" type="button" value="Cancel" name="action">
</a>
<h6><font color="#375c7c"><b>NOTE</b> : Once you confirm the invoice, you will no longer able to update it again.</font></h6>
  </div>
 </div>
 </center>
<br/>
</form>
        </div>
    </div>
</section>
<? endblock() ?>
<? startblock('sidebar') ?>
<? endblock() ?>
<? end_extend() ?>
