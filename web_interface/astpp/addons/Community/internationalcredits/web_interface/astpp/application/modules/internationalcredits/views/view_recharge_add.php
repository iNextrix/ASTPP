<?php include(FCPATH.'application/views/popup_header.php'); ?>
<script type="text/javascript">
$(document).ready(function() {
	
	$("input[name='int_credit_limit']").parent('li.col-md-12.form-group').after("<Span class='col-md-12 error_int_credit_limit' style='font-size:11px;font-weight: 600;color:red;' id='credit_limit_err'></span>");
	$("input[name='int_balance']").parent('li.col-md-12.form-group').after("<Span class='col-md-12 error_int_balance' style='font-size:11px;font-weight: 600;color:red;' id='balance_err'></span>");
	var globalalt='';
	
		$("#reseller_id_drp").change(function(){
			if(this.value != ""){
				$.ajax({
					type:'POST',
					url: "<?= base_url()?>/internationalcredits/customer_customerlist_for_internation_credit/",
					data:"reseller_id="+this.value, 
					async:true,
					success: function(response) {
						$("#accountid_search_drp").html(response);
						$('.selectpicker').selectpicker('refresh');
						$("#accountid_search_drp").change();
					}
				});
			}else{
				$("#accountid_search_drp").html("");
			}	
		});
		
		$("#accountid_search_drp").change(function() {
			var accountid =  $(this).val();
			if(accountid != '') {
				$.ajax({
					type: "POST",
					url: "<?= base_url()?>/internationalcredits/internationalcredits_account_status/",
					data:{accountid:accountid},
					async:true,
					success:function(alt) {
						globalalt=alt;
						if(alt == 1) {
							$("label[for='International Balance']").parents('li.form-group').addClass("d-none");
							$("input[name='int_balance']").parents('li.form-group').addClass("d-none");
							$("label[for='International Credit Limit']").parents('li.form-group').removeClass("d-none");
							$("input[name='int_credit_limit']").parents('li.form-group').removeClass("d-none");
							$("#balance_err").hide();
							$("#credit_limit_err").show();
						} else {
							$("label[for='International Balance']").parents('li.form-group').removeClass("d-none");
							$("input[name='int_balance']").parents('li.form-group').removeClass("d-none");
							$("label[for='International Credit Limit']").parents('li.form-group').addClass("d-none");
							$("input[name='int_credit_limit']").parents('li.form-group').addClass("d-none");
							$("#balance_err").show();
							$("#credit_limit_err").hide();
						}
						$('.selectpicker').selectpicker('refresh');
					}
				});
	  		}
		});
		$("#accountid_search_drp").change();
	$("#submit").click(function() {
		var intRegex = /^-?\d*(\.\d+)?$/;
		$('.error_int_balance').text('');
		$('.error_int_credit_limit').text('');
		if(globalalt == 1) {
			if($("input[name='int_credit_limit']").val() == '') {
				$("#credit_limit_err").text("International Credit Limit is required.");
				return false;
			} else {
				var str1=$("input[name='int_credit_limit']").val();
				if($("input[name='int_credit_limit']").val() < 0 || !intRegex.test(str1)) {
					$("#credit_limit_err").text("Enter valid International Credit Limit.");
					return false;
				} else {
					submit_form("internationalcredits_add_form","<?php echo base_url(); ?>internationalcredits/internationalcredits_save/");
				}
			}
		} else {
			if($("input[name='int_balance']").val() == '') {
				$("#balance_err").text("International Balance is required.");
				return false;
			}else {
				var str=$("input[name='int_balance']").val();
				if($("input[name='int_balance']").val() < 0 || !intRegex.test(str)) {
					$("#balance_err").text("Enter valid International Balance.");
					return false;
				} else {
					submit_form("internationalcredits_add_form","<?php echo base_url(); ?>internationalcredits/internationalcredits_save/");
				}
			}
		}
	});
	$("#reseller_id_drp").change();
});
</script>
<section class="slice m-0">
	<div class="w-section inverse p-0 card-header">
	        <div class="col-md-12 p-0 card-header">
		        <h3 class="fw4 p-4 m-0">
				<? echo $page_title; ?>
			</h3 class="bg-secondary text-light p-3 rounded-top">
		</div>
	</div>    
</section>	
<section class="slice m-0">
	<div class="w-section inverse p-4">
		<?php
			if (isset($validation_errors)) {
				$validation_array=json_decode($validation_errors);
				if(is_object($validation_array)) {
					$validation_array = get_object_vars($validation_array);
					foreach($validation_array as $key=>$value)
						echo $value."<br/>";
				} else {
					  echo $validation_errors;
				}
			}
		?>
		<?php echo $form;?>
	</div>
</section>
