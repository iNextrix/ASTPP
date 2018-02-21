<? extend('master.php') ?>

	<? startblock('extra_head') ?>
		<script type="text/javascript" src="/js/ui/ui.tabs.js"></script>
		<script type="text/javascript">
		$(document).ready(function() {
			// Tabs
			//$('#tabs, #tabs2, #tabs5').tabs();
		});
		</script>
	<? endblock() ?>

    <? startblock('page-title') ?>
       <!-- ASTPP - Open Source VOIP Billing Solution -->
    <? endblock() ?>
    
	<? startblock('content') ?>
				<center>
				<div class="clear"></div>
				<br/><br/><br/>
				<div class="content-box content-box-header ui-corner-all" style="width: 500px;" align="center" >
					<div class="ui-state-default ui-corner-top ui-box-header">
						<span class="ui-icon float-left ui-icon-signal"></span>
						Login&nbsp;  
					</div>
					<div class="content-box-wrapper">
						<?php if(isset($error_msg)):?>
						<div class="response-msg error ui-corner-all">
							<span>Error message</span>
							<?=$error_msg?>
						</div>		
						<?php endif;?>				
						<form action="<?php echo base_url();?>astpp/login" method="POST">							
							<br/><br/>
							<label>Username:</label>
							<input type="text" name="username" /><br/><br/>
							<label>Password:</label>
							<input type="password" name="password"  />
							<br/><br/>
							<input type="submit" name="mode" value="Login"/>
							<br/><br/>
						</form>
					</div>
				</div>
				<br/><br/>
				</center>
   <? endblock() ?>
	<? startblock('sidebar') ?>
    <? endblock() ?>
    
<? end_extend() ?>  
