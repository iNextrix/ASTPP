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
<div>
  <div>
    <section class="slice color-three no-margin">
	<div class="w-section inverse no-padding">
 	<div style="margin-left:390px;margin-right:280px;padding-left:90px;border:1px solid;">
	<table >
		<tr>
		    <td>#NAME# = This tag use to print Firstname + Lastname </td>
		</tr>
		<tr>
		    <td>#USERNAME# = This tag use to print user number </td>
		</tr>
		<tr>
		    <td>#PASSWORD# = This tag use to print password</td>
		</tr>
		<tr>
		    <td>#COMPANY_EMAIL# =This tag use to print company email id</td>
		</tr>
		<tr>
		    <td>#COMPANY_NAME#  =This tag use to print company name</td>
		</tr>
		<tr>
		    <td>#BALANCE# = This tag use to print user balance</td>
		</tr>
		<tr>
		    <td>#COMPANY_WEBSITE# =This tag use to print company website link</td>
		</tr>
		<tr>
		    <td>#PIN# =This tag use to print user pin numbner</td>
		</tr>
	</table>
	</div>

        </div>      
    </section>
  </div>
</div>
<br><br>
<? endblock() ?>
<? startblock('sidebar') ?>
<? endblock() ?>
<? end_extend() ?>

<script type="text/javascript">
$(function() {
	$('#template').markItUp(mySettings);

});
</script>
