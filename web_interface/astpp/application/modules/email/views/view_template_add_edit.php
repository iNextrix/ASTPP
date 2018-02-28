<? extend('master.php') ?>
<? startblock('extra_head') ?>

<!--
ASTPP  3.0 
For Email Template Changes
-->
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

<!--***************************************************************-->


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
                <?php if (isset($validation_errors)) {
	echo $validation_errors;
}
?> 
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
