<? extend('master.php') ?>
<?php error_reporting(E_ERROR);?>
<? startblock('extra_head') ?>
	<link rel="stylesheet" type="text/css" href="images/style.css">
	<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/markup/markitup/skins/markitup/style.css">
	<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/markup/markitup/sets/default/style.css">
	<script type="text/javascript" src="<?php echo base_url();?>assets/markup/markitup/jquery.markitup.js"></script>
	<script type="text/javascript" src="<?php echo base_url();?>assets/markup/markitup/sets/default/set.js"></script>
<?php endblock() ?>
<?php startblock('page-title') ?>
<?=$page_title?>
<br/>
<?php endblock() ?>
<?php startblock('content')?>


<div>
  <div>
    <section class="slice color-three no-margin">
	<div class="w-section inverse no-padding">
            <div style="color:red;margin-left: 60px;">
                <?php if (isset($validation_errors)) echo $validation_errors; ?> 
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

<script type="text/javascript">
$(function() {
	$('#template').markItUp(mySettings);

});
</script>
