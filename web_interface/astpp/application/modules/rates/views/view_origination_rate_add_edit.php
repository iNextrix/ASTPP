<?php include(FCPATH.'application/views/popup_header.php'); ?>
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
<script type="text/javascript" language="javascript">
$(document).ready(function() {
    $("input[type='hidden']").parents('li.form-group').addClass("d-none");
    var trunk_count='<?= $trunk_count ?>';
	for(i=1;i <= trunk_count;i++){
		$("#trunk_percentage_"+i).parents('li.form-group').addClass("d-none");
	}      
});
</script>

<script type="text/javascript">
	$(document).ready(function() {
	
	
    
    $(".reseller_id_drp").change(function(){
                var reseller_id=this.value;
                if(reseller_id!=undefined){
					if(reseller_id==0){
							$(".routing_type").parents('li.form-group').removeClass("d-none");
							$(".trunk_selectpicker").parents('li.form-group').removeClass("d-none");
						}else{
							$(".routing_type").parents('li.form-group').addClass("d-none");
							$(".trunk_selectpicker").parents('li.form-group').addClass("d-none");
							var trunk_count='<?= $trunk_count ?>';
							for(i=1;i <= trunk_count;i++){
									$('#trunk_percentage_'+i).parents('li.form-group').addClass("d-none");
							}	
					}
					
					$.ajax({
						type:'POST',
						url: "<?= base_url()?>/accounts/customer_pricelist/",
						data:"reseller_id="+reseller_id,  
						success: function(response) {
							if(response){
								$("#pricelist_id_drp").html(response);
								$("#pricelist_id_drp").prepend("<option value='' selected='selected'>--Select--</option>");
								$('.selectpicker').selectpicker('refresh'); 
							}else{
								$("#pricelist_id_drp").html("<option value='' selected='selected'>--Select--</option>");
								$('.selectpicker').selectpicker('refresh'); 
							}	 
							
						}
					});
				}	
                
       });
        
     $(".reseller_id").change();
     get_reseller_rategroup();
      
 });
	function get_reseller_rategroup(){
		var reseller_id='<?= $reseller_id ?>';
		if(reseller_id!=undefined){
					if(reseller_id==0){
							$(".routing_type").parents('li.form-group').removeClass("d-none");
							$(".trunk_selectpicker").parents('li.form-group').removeClass("d-none");
						}else{
							$(".routing_type").parents('li.form-group').addClass("d-none");
							$(".trunk_selectpicker").parents('li.form-group').addClass("d-none");
							var trunk_count='<?= $trunk_count ?>';
							for(i=1;i <= trunk_count;i++){
									$('#trunk_percentage_'+i).parents('li.form-group').addClass("d-none");
							}	
					}
		}
		
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
        submit_form("origination_rate_form");
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
