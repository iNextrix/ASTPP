<?php include(FCPATH.'application/views/popup_header.php'); ?>
<script type="text/javascript">
    $("#submit").click(function(){
        submit_form("ipmap_form");
    });
function account_change_add(val){  
	      	$.ajax({
		type: "POST",
		url: "<?= base_url()?>/accounts/customer_account_change/"+val,
		data:'',
		success:function(alt) { 
			 $("#account_drp").html(alt); 
			 $('.selectpicker').selectpicker('refresh');
			     
		}
	});
}
</script>

<section class="slice m-0">
	<div class="w-section inverse p-0">
		<div>
			<div>
				<div class="col-md-12 p-0 card-header">
					<h3 class="text-dark fw4 p-4 m-0"><? echo $page_title; ?></h3 class="bg-secondary text-light p-3 rounded-top">
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
                <?php

if (isset($validation_errors)) {
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
