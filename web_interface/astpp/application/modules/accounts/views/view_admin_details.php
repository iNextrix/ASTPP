<?php extend('master.php') ?>
<?php startblock('extra_head') ?>
<script type="text/javascript" language="javascript">
    $(document).ready(function() {
        $('#tabs').tabs();
    });
</script>
<?php endblock() ?>

<?php startblock('page-title') ?>
<?= $page_title ?>
<?php endblock() ?>

<?php startblock('content') ?>        
<div class="container">
          <div class="row">
            <section class="slice color-three no-margin">
  <div class="w-section inverse no-padding">
				     <?php echo $form; ?>
				     <?php
					if (isset($validation_errors) && $validation_errors != '') { ?>
					    <script>
						var ERR_STR = '<?php echo $validation_errors; ?>';
						print_error(ERR_STR);
					    </script>
				     <?php } ?>

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
<?php endblock() ?>	
<?php startblock('sidebar') ?>
Filter by
<?php endblock() ?>
<?php end_extend() ?>  
