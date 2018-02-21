<? extend('master.php') ?>

	<? startblock('extra_head') ?>
		<script type="text/javascript" src="js/ui/ui.tabs.js"></script>
		<script type="text/javascript">
		$(document).ready(function() {
			// Tabs
			$('#tabs, #tabs2, #tabs5').tabs();
		});
		</script>
	<? endblock() ?>

    <? startblock('page-title') ?>
        ASTPP - Open Source VOIP Billing Solution 
    <? endblock() ?>
    
	<? startblock('content') ?>
				
		ACCOUNTS HOME PAGE
				
   <? endblock() ?>
	<? startblock('sidebar') ?>
    <? endblock() ?>
    
<? end_extend() ?>  
