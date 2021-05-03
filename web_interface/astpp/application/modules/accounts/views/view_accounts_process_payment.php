<script>
  $("#submit").click(function(){
    submit_form("acccount_charges_form");
    $('#submit').prop('disabled', true);
    $('input[type="text"]').keyup(function() {
     if($(this).val() != '') {
      $('#submit').prop('disabled', false);
    }
  });
  })
  $('.selectpicker').selectpicker();
  $("input[type='hidden']").parents('li.form-group').addClass("d-none");  
  $("textarea").parents('li.form-group').addClass("h-auto");  
</script>
<section class="slice m-0">
	<div class="w-section inverse p-0">
		<div>
			<div>
				<div class="col-md-12 p-0 card-header">
					<h3 class="fw4 p-4 m-0"><? echo $page_title; ?></h3>
				</div>
			</div>
		</div>
	</div>
</section>


<section class="slice m-0">
	<div class="w-section inverse p-4">
		<div style="">
    <?php

if (isset($validation_errors)) {
        echo $validation_errors;
    }
    ?> 
  </div>
  <?php echo $form; ?>
</div>
</section>


