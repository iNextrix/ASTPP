<? extend('master.php') ?>

	<? startblock('extra_head') ?>
	<? endblock() ?>

    <? startblock('page-title') ?>
        <?=$page_title?><br/>
    <? endblock() ?>
    
	<? startblock('content') ?>
<br/>
<script language="javascript" type="text/javascript">
function confirm_purge()
{
	if(confirm("Are you sure you want to delete all deactivated/deleted records from database?"))
	$("#frm_purge").submit();
}
</script>
<div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">                        
    <div class="portlet-header ui-widget-header">Purge Deactivated<span class="ui-icon ui-icon-circle-arrow-s"></span></div>
    <div class="portlet-content">
    <form method="post" action="<?=base_url()?>systems/purgedeactivated" id="frm_purge" enctype="multipart/form-data">
    Remove records in your system that have been marked as deactivated.<br />
    <br />
    <input type="hidden" name="mode" value="purge"  />    
    <input class="ui-state-default ui-corner-all ui-button" type="button" name="action" onclick="confirm_purge()" value="Drop Deactivated Records" />
    </form>
    </div>
</div>
    <? endblock() ?>
	
    <? startblock('sidebar') ?>
    Filter by
    <? endblock() ?>
    
<? end_extend() ?>  
