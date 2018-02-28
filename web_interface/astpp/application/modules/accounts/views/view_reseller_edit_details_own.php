<? extend('master.php') ?>
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

    
//         $(".invoice_day").hide();
//         $('label[for="Billing Day"]').hide()
       
document.getElementsByName("currency_id")[0].selectedIndex = <?=$currency_id - 1?>;
document.getElementsByName("timezone_id")[0].selectedIndex = <?=$timezone_id - 1?>;
document.getElementsByName("country_id")[0].selectedIndex = <?=$country_id - 1?>;
document.getElementsByName("sweep_id")[0].selectedIndex = <?=2?>;
 
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
        });
</script>
<?php endblock() ?>
<?php startblock('page-title') ?>
<?= $page_title ?>
<br/>
<?php endblock() ?>
<?php startblock('content') ?>

<section class="slice color-three">
 <div class="w-section inverse no-padding">
				     <?php echo $form; ?>
				     <?php
					if (isset($validation_errors) && $validation_errors != '') { ?>
					    <script>
						var ERR_STR = '<?php echo $validation_errors; ?>';
						print_error(ERR_STR);
					    </script>
				     <?php } ?>

                          </div> 
</section>
<? endblock() ?>
<? startblock('sidebar') ?>
<? endblock() ?>
<? end_extend() ?>
