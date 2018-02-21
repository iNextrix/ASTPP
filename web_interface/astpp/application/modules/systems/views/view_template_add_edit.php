<? extend('master.php') ?>
<?php error_reporting(E_ERROR);?>
<? startblock('extra_head') ?>
    <script type="text/javascript" src="<?php echo base_url();?>assets/ckeditor/ckeditor.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>assets/ckeditor/source/core/ckeditor.js"></script>

<?php endblock() ?>
<?php startblock('page-title') ?>
<?=$page_title?>
<br/>
<?php endblock() ?>
<?php startblock('content')?>


<div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">
  <div class="portlet-header ui-widget-header">
    <?=@$page_title?>
    <span class="ui-icon ui-icon-circle-arrow-s"></span></div>
<div style="color:red;margin-left: 60px;">
    <?php if(isset($validation_errors))echo $validation_errors; ?> 
    </div>
    <?php echo $form;?>
</div>

<? endblock() ?>
<? startblock('sidebar') ?>
<? endblock() ?>
<? end_extend() ?>
<script type="text/javascript">
                                    CKEDITOR.replace( 'template',
                                    {
                                            skin : 'office2003',
                                            height:"350", width:"880"
                                    });
</script>