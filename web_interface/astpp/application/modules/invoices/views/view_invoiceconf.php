<? extend('master.php') ?>
<?php error_reporting(E_ERROR);?>
<? startblock('extra_head') ?>
<?php endblock() ?>
<?php startblock('page-title') ?>
<?=$page_title?>
<br/>
<?php endblock() ?>
<?php startblock('content')?>
<div class="container">
  <div class="row">
    <section class="slice color-three no-margin">
        <div class="w-section inverse no-padding">
            <div style="color:red;margin-left: 60px;">
                    <?php echo $validation_errors; ?> 
            </div>
            <?php echo $form; ?>
        </div>      
    </section>        
  </div>
</div>


<? endblock() ?>
<? startblock('sidebar') ?>
<? endblock() ?>
<? end_extend() ?>
