<?php include(FCPATH.'application/views/popup_header.php'); ?>
<script type="text/javascript" language="javascript">
$(document).ready(function() {
    $("input[type='hidden']").parents('li.form-group').addClass("d-none");
});
</script>
<script type="text/javascript">
    $("#submit").click(function(){
        submit_form("cron_form");
    });
    <?php

if (isset($next_execution_date) && $next_execution_date != "") {
        ?>
		$(document).ready(function() {
		 	 var date=new Date();
		 	 var next_execution_date="<?php echo $next_execution_date; ?>";
		 	 $("#exeution_date").datetimepicker({
				value:next_execution_date,
				uiLibrary: 'bootstrap4',
				iconsLibrary: 'fontawesome',
				minDate:date,
				modal:true,
				format: 'yyyy-mm-dd HH:MM:ss',
				footer:true
			});
		});
	<?php }else{?>
    $(document).ready(function() {
	 var date=new Date(new Date().getFullYear(), new Date().getMonth(), new Date().getDate()); 
	  $("#exeution_date").datetimepicker({
            uiLibrary: 'bootstrap4',
            iconsLibrary: 'fontawesome',
            minDate:date,
            modal:true,
            format: 'yyyy-mm-dd HH:MM:ss',
            footer:true
         }); 	
    });
	<?php } ?>
</script>
<style>
.gj-modal {
	z-index: 99999;
}
</style>
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
