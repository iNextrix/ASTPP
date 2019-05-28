<?php include(FCPATH.'application/views/popup_header.php'); ?>
<script type="text/javascript">
	$(document).ready(function() {
		$("#submit").click(function(){
			submit_form("customer_bulk_form","<?php echo base_url(); ?>accounts/customer_bulk_save/");
			$('#submit').prop('disabled', true);
			$('input[type="text"]').keyup(function() {
				if($(this).val() != '') {
					$('#submit').prop('disabled', false);
				}
			});
		});
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
    $validation_array = json_decode($validation_errors);
    if (is_object($validation_array)) {
        $validation_array = get_object_vars($validation_array);
        foreach ($validation_array as $key => $value)
            echo $value . "<br/>";
    } else
        echo $validation_errors;
}
?>   
		<?php echo $form; ?>
	</div>
</section>

<script type="text/javascript" language="javascript">
	$(document).ready(function() {
		$("input[type='hidden']").parents('li.form-group').addClass("d-none");
	});
</script>
