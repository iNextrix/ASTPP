<?php include(FCPATH.'application/views/popup_header.php'); ?>
<?php
if(isset($trunk_id) and !empty($trunk_id)){
	$trunk_id_json=json_encode((array)$trunk_id);
}else{
	$trunk_id= array();
	$trunk_id_json=json_encode((array)$trunk_id);
}
if(isset($percentage) and !empty($percentage)){
	$percentage_json=json_encode((array)$percentage);
}else{
	$percentage= array();
	$percentage_json=json_encode((array)$percentage);
}
?>
<script type="text/javascript" language="javascript">
	$(document).ready(function() {
		var reseller_id = "<?php echo $reseller_id?>";
		$(".reseller_id_drp").val(reseller_id);    
		$('.selectpicker').selectpicker('refresh');
		$("input[type='hidden']").parents('li.form-group').addClass("d-none");
		var trunk_count='<?= $trunk_count ?>';
		for(i=1;i <= trunk_count;i++){
			$("#trunk_percentage_"+i).parents('li.form-group').addClass("d-none");
		}
	});

	function account_change_add(val){
		$.ajax({
			type: "POST",
			url: "<?= base_url()?>/custom_rates/customer_account_change/"+val,
			data:'',
			success:function(alt) { 
				$("#account_id").html(alt);    
				$('.selectpicker').selectpicker('refresh');
			}
		});
	}


</script>

<script type="text/javascript">
	$(document).ready(function() {
		var routing_type='<?= $routing_type ?>';
		var trunk_count='<?= $trunk_count ?>';
		if(routing_type == 0){
			$("#trunk_id").parents('li.form-group').addClass("d-none"); 
			$('label[for="Trunks"]').hide();
			var fromPHP=<? echo $trunk_id_json ?>;
			for(i=1;i <= trunk_count;i++){
				var min_amt = 1; 
				var key = (i-min_amt);
				yourValue=fromPHP[key];
				if(yourValue!=undefined){
					$('.trunk_id_'+i).val(yourValue);
					$('.trunk_id_'+i).selectpicker('refresh');
				}
				$('.selectpicker').selectpicker('refresh'); 
				
			}
		}else{
			$("#trunk_id").addClass("d-none"); 
			$('label[for="Trunks"]').hide();
			var fromPHP=<? echo $trunk_id_json ?>;
			var fromPHPnew=<? echo $percentage_json ?>;
			for(i=1;i <= trunk_count;i++){
				var min_amt = 1; 
				var key = (i-min_amt);
				yourValue=fromPHP[key];
				$('.trunk_id_'+i).val(yourValue);
				$("#trunk_percentage_"+i).parents('li.form-group').removeClass("d-none");
				yourValuenew=fromPHPnew[key];
				$('#trunk_percentage_'+i).val(yourValuenew);
				$('.selectpicker').selectpicker('refresh'); 
			}
		}
		
	});
	
	function reseller_id_change(){
		var reseller_id=$("#reseller").val();
		$.ajax({
			type:'POST',
			url: "<?= base_url()?>/custom_rates/customer_customerlist_customrates/",
			data:"reseller_id="+reseller_id,  
			success: function(response) {
				$("#accountid1").html(response);
				$('.selectpicker').selectpicker('refresh');
			}
		}); 
	}
	
	
	function trunk_change(routing_type){
		if(routing_type == 0){
			$("#trunk_id").parents('li.form-group').addClass("d-none");  
			$('label[for="Trunks"]').hide();
			var trunk_count='<?= $trunk_count ?>';
			for(i=1;i <= trunk_count;i++){
				$(".trunk_id_"+i).parents('li.form-group').removeClass("d-none");
				$(".trunk_percentage_"+i).parents('li.form-group').addClass("d-none");
			}
			for(i=1;i <= trunk_count;i++){
				var trunk_name= "Trunks"+i;
				$("label[for="+trunk_name+"]").show(); 
				$("#trunk_percentage_"+i).parents('li.form-group').addClass("d-none"); 
			}
		}else{
			$("#trunk_id").parents('li.form-group').addClass("d-none");  
			$('label[for="Trunks"]').hide();
			$(".selectpicker").parents('li.form-group').removeClass("col-md-5");  
			$(".selectpicker").parents('li.form-group').addClass("col-md-2"); 
			var trunk_count='<?= $trunk_count ?>';
			for(i=1;i <= trunk_count;i++){
				$(".trunk_id_"+i).parents('li.form-group').removeClass("d-none");
				$("#trunk_percentage_"+i).parents('li.form-group').removeClass("d-none");
			}
			for(i=1;i <= trunk_count;i++){
				var trunk_name= "Trunks"+i;
				$("label[for="+trunk_name+"]").show();
			}
		}
	}
	$("#submit").click(function(){
		submit_form("custom_rate_form");
	});
	
</script>
<section class="slice m-0">
	<div class="w-section inverse p-0">
		<div>
			<div>
				<div class="col-md-12 p-0 card-header">
					<h3 class="fw4 p-4 m-0"><? echo gettext($page_title); ?></h3 class="text-light p-3 rounded-top">
				</div>
			</div>
		</div>
	</div>    
</section>
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
