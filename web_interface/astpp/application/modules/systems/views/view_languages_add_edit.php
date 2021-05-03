<script type="text/javascript">
	$("#submit").click(function(){
		$("[name='action']").prop('disabled', 'disabled');
		submit_form("system_form");
	})
	$("input").keyup(function(){  
		$("[name='action']").prop('disabled', false);
   	 }); 
</script>
<section class="slice m-0">
 <div class="w-section inverse p-0 card-header">
        <div class="col-md-12 p-0 card-header">
	        <h3 class="fw4 p-4 m-0"><? echo $page_title; ?></h3 class="bg-secondary text-light p-3 rounded-top">
		</div>
  </div>    
</section>
    <section class="slice m-0">
		<div class="w-section inverse p-4">
                <?php if (isset($validation_errors)) {
					echo $validation_errors;
				}
				?>
            <?php echo $form; ?>
        </div>
    </section>
<script type="text/javascript" language="javascript">
$(document).ready(function() {
    $("input[type='hidden']").parents('li.form-group').addClass("d-none");
	$("input[name='locale']").parent('li.col-md-12.form-group').after("<Span class='col-md-12 error_int_credit_limit' style='font-size:11px;font-weight: 600;color:red;' id='credit_limit_err'></span>");
	$("#credit_limit_err").text("Please be patient until the whole process is complete.");
});
</script>