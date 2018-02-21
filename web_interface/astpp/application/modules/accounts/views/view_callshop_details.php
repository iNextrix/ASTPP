<? extend('master.php') ?>
<? startblock('extra_head') ?>
<script type="text/javascript" language="javascript">
    $(document).ready(function() {
        $('#tabs').tabs();
    });
</script>
<? endblock() ?>

<? startblock('page-title') ?>
<?= $page_title ?><br/>
<? endblock() ?>

<? startblock('content') ?>        
<div id="tabs">
    <ul>
        <li><a href="#customer_details">Call Shop Details</a></li>
    </ul>	
    <div id="customer_details">
        <div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">
            <div class="portlet-header ui-widget-header"><!--< ?php echo isset($account)?"Edit":"Create New";?> Account-->
                <?= @$page_title ?>
                <span class="ui-icon ui-icon-circle-arrow-s"></span></div>
            <div style="color:red;margin-left: 60px;">
                <?php if (isset($validation_errors)) {
                    echo $validation_errors;
                } ?> 
            </div>
<?php echo $form; ?>
        </div>
    </div>
</div>
<? endblock() ?>	
<? startblock('sidebar') ?>
Filter by
<? endblock() ?>
<? end_extend() ?>  
