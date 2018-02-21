<script type="text/javascript">
    $("#submit").click(function(){
        submit_form("did_form");
    })
</script>

<div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">
    <div class="portlet-header ui-widget-header">
        <?= @$page_title ?>
        <span class="ui-icon ui-icon-circle-arrow-s"></span></div>
    <div style="color:red;margin-left: 60px;">
        <?php if (isset($validation_errors)) echo $validation_errors; ?> 
    </div>
    <?php echo $form; ?>
</div>