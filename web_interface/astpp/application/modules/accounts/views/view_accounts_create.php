<? extend('master.php') ?>
<?php error_reporting(E_ERROR); ?>
<? startblock('extra_head') ?>
<script type="text/javascript">
    $(document).ready(function() {
    
    
    
    $(".change_pass").click(function(){

            $.ajax({type:'POST',
                url: "<?= base_url()?>accounts/customer_generate_password",
                success: function(response) {
                    $('#password').val(response.trim());
                }
            });
        })
         $(".change_number").click(function(){
            
            $.ajax({type:'POST',
                url: "<?= base_url()?>accounts/customer_generate_number",
                success: function(response) {
                    var data=response.replace('-',' ');
                    $('#number').val(data.trim());
                }
            });
        })
        $(".digit_length").change(function(){
            var digit=this.value;
            $.ajax({type:'POST',
                url: "<?= base_url()?>accounts/customer_generate_number/"+digit,
                success: function(response) {
                    $('#number').val(response.trim());
                }
            });
        });
    
    
   $(".country_id").val(<?=$country_id?>);
   $(".timezone_id").val(<?=$timezone_id?>);
   $(".currency_id").val(<?=$currency_id?>);
   <?php if($entity_name != 'admin' && $entity_name !='subadmin'){ ?>
   document.getElementsByName("sweep_id")[0].selectedIndex = <?=1?>;

	 $(".sweep_id").change(function(e){
            if(this.value != 0){
                $.ajax({
                    type:'POST',
                    url: "<?= base_url()?>/accounts/customer_invoice_option",
                    data:"sweepid="+this.value, 
                    success: function(response) {
                        $(".invoice_day").html(response);
                        $('.invoice_day').show();
                        $('label[for="Billing Day"]').show()
                    }
                });
            }else{
                $('label[for="Billing Day"]').hide()
                $('.invoice_day').css('display','none');                
            }
        });
        $(".sweep_id").change();
        <?php } ?> 
        });
       
</script>
<?php endblock() ?>
<?php startblock('page-title') ?>
<?= $page_title ?>
<br/>
<?php endblock() ?>
<?php startblock('content') ?>
<div class="container">
        <div class="row">
		<section class="slice color-three">
			<div class="w-section inverse no-padding">
				     <?php echo $form; ?>
				     <?php
					if(isset($validation_errors) && $validation_errors != ''){ ?>
					    <script>
						var ERR_STR = '<?php echo $validation_errors; ?>';
						print_error(ERR_STR);
					    </script>
				     <? } ?>

<!--                                <?php
                                $data_errrors = json_decode($validation_errors);
                                foreach ($data_errrors as $key => $value) {
                                    echo $value . "<br/>";
                                }
                                ?> 
                          </div>
                        <?php echo $form; ?> -->
                          </div>  
	        </section>
	</div>
</div>
<? endblock() ?>
<? startblock('sidebar') ?>
<? endblock() ?>
<? end_extend() ?>
