<?php include(FCPATH.'application/views/popup_header.php'); ?>
<script type="text/javascript">
  $("#submit").click(function(){
		submit_form("default_language_form");
	});
	$("#name_error_div").parent('li.col-md-12.form-group').after("<Span class='col-md-12 error_int_credit_limit' style='font-size:11px;font-weight: 600;color:red;' id='languages_alert'></span>");
	$("#languages_alert").text("<?php echo gettext('Please Re-login to get Impact of selected language.'); ?>");
</script>
<section class="slice m-0">
 <div class="w-section inverse p-0">
   <div>
     <div>
        <div class="col-md-12 p-0 card-header">
	        <h3 class="fw4 p-4 m-0"><? echo $page_title; ?></h3 class="text-light p-3 rounded-top">
		</div>
     </div>
    </div>
  </div>    
</section>
<div>
  <div>
  <section class="slice m-0">
		 <div class="w-section inverse p-4">
            <div style="">
                <?php if (isset($validation_errors)) {
					echo $validation_errors;
				}
				?> 
            </div>
            <?php echo $form; ?>
        </div>      
    </section>
  </div>
</div>
<script type="text/javascript" language="javascript">
$(document).ready(function() {
    $("input[type='hidden']").parents('li.form-group').addClass("d-none");
  
});

</script>
