<?php include(FCPATH.'application/views/popup_header.php'); ?>
<script type="text/javascript">
    $("#submit").click(function(){
		$("[name='action']").prop('disabled', 'disabled');
		submit_form("system_form");
	});
	$("input").keyup(function(){  
		$("[name='action']").prop('disabled', false);
	 });
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
