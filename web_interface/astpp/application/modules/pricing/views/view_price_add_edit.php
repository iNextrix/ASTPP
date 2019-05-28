<?php
include (FCPATH . 'application/views/popup_header.php');
?>
<?php
if (isset($trunk_id) and ! empty($trunk_id)) {
    $trunk_id_json = json_encode((array) $trunk_id);
} else {
    $trunk_id = array();
    $trunk_id_json = json_encode((array) $trunk_id);
}
if (isset($percentage) and ! empty($percentage)) {
    $percentage_json = json_encode((array) $percentage);
} else {
    $percentage = array();
    $percentage_json = json_encode((array) $percentage);
}

?>
<script type="text/javascript">
    $("#submit").click(function(){
        submit_form("pricing_form");
    })
</script>
<script type="text/javascript" language="javascript">
	$(document).ready(function() {
		$(".reseller_id").change(function(){
                var reseller_id=$("#reseller").val();
                if(reseller_id==0){
			$(".routing_type").parents('li.form-group').removeClass("d-none");
			$("select[name='trunk_id[]']").parents('li.form-group').removeClass("d-none");
		}else{
			$(".routing_type").parents('li.form-group').addClass("d-none");
			$("select[name='trunk_id[]']").parents('li.form-group').addClass("d-none");
		}
        });
	});
</script>
<section class="slice m-0">
	<div class="w-section inverse p-0">
		<div class="col-md-12 p-0 card-header">
			<h3 class="fw4 p-4 m-0"><? echo $page_title; ?></h3 class="text-light p-3 rounded-top">
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
