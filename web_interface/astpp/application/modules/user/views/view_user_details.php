<? extend('master.php') ?>
<? startblock('extra_head') ?>

<script type="text/javascript" language="javascript">
 $(document).ready(function() {
 $(".change_pass").click(function(){

            $.ajax({type:'POST',
                url: "<?= base_url()?>user/user_generate_password/",
                success: function(response) {
                    $('#password').val(response.trim());
                }
            });
        })
        $(".change_number").click(function(){
            $.ajax({type:'POST',
                url: "<?= base_url()?>user/user_generate_number/"+10,
                success: function(response) {
                    var data=response.replace('-',' ');
                    $('#number').val(data.trim());
                }
            });
        })
         });
</script>
<? endblock() ?>
<? startblock('page-title') ?>
<?= $page_title ?><br/>
<? endblock() ?>

<? startblock('content') ?>        


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
Filter by
<? endblock() ?>
<? end_extend() ?>  
