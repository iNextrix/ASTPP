<?php include(FCPATH.'application/views/popup_header.php'); ?>
<script type="text/javascript">
    $("#submit").click(function(){
        submit_form("calltype_form");
    })
</script>
<section class="slice no-margin">
	<div class="w-section inverse no-padding">
		<div>
			<div>
				<div class="col-md-12 no-padding">
					<h3 class="text-dark fw4 pl-4 pr-4 pt-4 rounded-top"><? echo $page_title; ?></h3 class="bg-secondary text-light p-3 rounded-top">
				</div>
			</div>
		</div>
	</div>
</section>

<div>
	<div>
		<section class="slice no-margin">
			<div class="w-section inverse no-padding">
				<div style="">
             <?php echo $form; ?>
                <?php

if (isset($validation_errors)) {
                    echo $validation_errors;
                }
                ?> 
            </div>

			</div>
		</section>
	</div>
</div>
<script type="text/javascript" language="javascript">
$(document).ready(function() {
    $("input[type='hidden']").parents('li.form-group').addClass("d-none");
  
});
</script>
