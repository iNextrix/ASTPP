<?php extend('master.php') ?>
<?php startblock('extra_head') ?>
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
				if(isset($validation_errors) && $validation_errors != ''){ ?>
                    <script>
                        var ERR_STR = '<?php echo $validation_errors; ?>';
                        print_error(ERR_STR);
                    </script>
             <?php } ?>
        </div>      
    </section>        
  </div>
</div>



<?php endblock() ?>
<?php startblock('sidebar') ?>
<?php endblock() ?>
<?php end_extend() ?>
