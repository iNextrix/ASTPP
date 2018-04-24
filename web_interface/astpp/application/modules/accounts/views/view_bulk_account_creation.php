<?php include(FCPATH.'application/views/popup_header.php'); ?>
<script type="text/javascript">
$(document).ready(function() {
 <?php if ($entity_name != 'admin' && $entity_name != 'subadmin') { ?>
   document.getElementsByName("sweep_id")[0].selectedIndex = <?=1?>;

		$("#sweep_id").change(function(e){
			//end
			var id_mass = document.getElementById("sweep_id").value;
            if(id_mass != 0){
                $.ajax({
                    type:'POST',
                    url: "<?= base_url()?>/accounts/customer_invoice_option/",
                    data:"sweepid="+id_mass, 
                    success: function(response) {
						response = response.replace('col-md-5', 'col-md-6');
                        $('.invoice_day').selectpicker('show');
                        $(".invoice_day").html(response);
                        $('.selectpicker').selectpicker('refresh');
                        $('.invoice_day').show();
                        $('label[for="Billing Day"]').show();
                    }
                });
            }else{
                $('label[for="Billing Day"]').hide();
                $('.invoice_day').css('display','none');   
                             
            }
        });
        $(".sweep_id").change();
        <?php } ?> 
document.getElementsByName("currency_id")[0].selectedIndex = <?=$currency_id - 1?>;
document.getElementsByName("timezone_id")[0].selectedIndex = <?=$timezone_id - 1?>;
document.getElementsByName("country_id")[0].selectedIndex = <?=$country_id - 2?>;
$('.selectpicker').selectpicker('refresh');

    $("#submit").click(function(){
        submit_form("customer_bulk_form","<?php echo base_url(); ?>accounts/customer_bulk_save/");
    });
});
</script>

<section class="slice gray no-margin">
 <div class="w-section inverse no-padding">
   <div>
     <div>
        <div class="col-md-12 no-padding margin-t-15 margin-b-10">
	        <div class="col-md-10"><b><? echo $page_title; ?></b></div>
	  </div>
     </div>
    </div>
  </div>    
</section>


<div>
          <div class="">
            <section class="slice color-three no-margin">
                <div class="w-section inverse no-padding"> 
                    <div style="color:red;margin-left: 60px;">
                        <?php
						if (isset($validation_errors)) {
						   $validation_array=json_decode($validation_errors);
						   if(is_object($validation_array)){
						   $validation_array = get_object_vars($validation_array);
						   foreach($validation_array as $key=>$value)
					  echo $value."<br/>";
						   }
						   else
					  echo $validation_errors;
                           
						}
						?>
                    </div>
        <?php echo $form; ?>
                </div>      
            </section>        
          </div>
        </div>    
