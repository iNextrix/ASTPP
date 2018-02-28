<? extend('master.php') ?>
<? startblock('extra_head') ?>

<script type="text/javascript" src="<?php echo base_url(); ?>assets/tinymce/tinymce.min.js">

</script>
<script type="text/javascript">

tinymce.init({
  selector: 'textarea',
  height: 300,
  width: 700,
  theme: 'modern',
  plugins: [
    'advlist autolink lists link image charmap print preview hr anchor pagebreak',
    'searchreplace wordcount visualblocks visualchars code fullscreen',
    'insertdatetime media nonbreaking save table contextmenu directionality',
    'emoticons template paste textcolor colorpicker textpattern imagetools'
  ],
  toolbar1: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
  toolbar2: 'print preview media | forecolor backcolor emoticons',
  image_advtab: true,
  templates: [
    { title: 'Test template 1', content: 'Test 1' },
    { title: 'Test template 2', content: 'Test 2' }
  ],
  content_css: [
    '<?php echo base_url(); ?>assets/css/tinymce_fast_font.css',
    '<?php echo base_url(); ?>assets/css/tinymce_codepen_min.css'
  ]
 });
</script>

<?php endblock() ?>
<?php startblock('page-title') ?>
<?=$page_title?>
<?php endblock() ?>
<?php startblock('content')?>

<div>
  <div>
    <section class="slice color-three no-margin">
	<div class="w-section inverse no-padding">
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
