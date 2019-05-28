<? extend('master.php') ?>
<? startblock('extra_head') ?>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/ck/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/ck/ckeditor/adapters/jquery.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/jquery.validate.min.js"></script>
<script>
$ (document).ready(function(){
    CKEDITOR.replace('template');
    CKEDITOR.replace('alert_template');
    CKEDITOR.config.width = '100%';
});
</script>
<script type="text/javascript" language="javascript">
$(document).ready(function() {
    $("input[type='hidden']").parents('li.form-group').addClass("d-none");
    $('.Emailtemplate').parents('li').wrap('<div class="col-md-12 form-group h-auto pt-3">').contents().unwrap();
    $('.Emailtemplate2').parents('li').wrap('<div class="col-md-12 form-group h-auto pt-3">').contents().unwrap();
    $('.description').parents('li').wrap('<div class="col-md-12 form-group h-auto pt-3">').contents().unwrap();
	$('.sms_template').parents('li').wrap('<div class="col-md-12 form-group h-auto pt-3">').contents().unwrap();   
});
</script>
<script type="text/javascript">
$(function() {
  	$('#template').markItUp(mySettings);
    $('#cke_template').addClass('col-12');
});
</script>
<?php endblock() ?>
<?php startblock('page-title') ?>
<?=$page_title?>
<?php endblock() ?>
<?php startblock('content')?>

<div class="p-0">
     <section class="slice color-three">
		<div class="w-section inverse p-0">
			<?php echo $form; ?>
		  <?php
			if (isset($validation_errors) && $validation_errors != '') { ?>
			<script>
			  var ERR_STR = '<?php echo $validation_errors; ?>';
			  print_error(ERR_STR);
			</script>
		  <? } ?>   
		</div>
	</section>
</div>
<section class="slice color-three m-0">
        <div class="card">
              <h3 class="bg-secondary text-light p-3 rounded-top m-0"><?php echo gettext('Details') ?></h3>
               <input type="hidden" name="rowcount" class="col-md-5 form-control" id="rowcountid"> 
				<div class="col-md-12 p-4 table-responsive">
						<table width="100%" class="table table-bordered details_table table">
						  <tbody>
									<tr>
										<td> <?php echo gettext('KEY') ?></td><td>  <?php echo gettext('VALUE') ?></td>
									</tr>
									<?php foreach ($template_words as $key => $value) {
										?>
										<tr>
											<td> <?= $key ?></td><td>  <?= $value ?></td>
										</tr>
										<?php
									}
									?>
						  </tbody>
						</table>
				</div>    
        </div>
</section> 
<? endblock() ?>
<? startblock('sidebar') ?>
<? endblock() ?>
<? end_extend() ?>
