<script type="text/javascript">
    $("#submit").click(function(){
        submit_form("chrges_form");
    })
</script>

<div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">
  <div class="portlet-header ui-widget-header"><!--< ?php echo isset($account)?"Edit":"Create New";?> Account-->
    <?=@$page_title?>
    <span class="ui-icon ui-icon-circle-arrow-s"></span></div>
     <?php echo form_error('description'); ?>
    <?php echo $form;?>
</div>
