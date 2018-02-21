<? extend('user_master.php') ?>

	<? startblock('extra_head') ?>
		<script type="text/javascript" src="/js/ui/ui.tabs.js"></script>
		<script type="text/javascript">
		$(document).ready(function() {
			// Tabs
			$('#tabs, #tabs2, #tabs5').tabs();
		});
		</script>
	<? endblock() ?>



    <? startblock('page-title') ?>
       <!-- ASTPP - Open Source VOIP Billing Solution -->
    <? endblock() ?>
    
	<? startblock('content') ?>
				<div class="inner-page-title">			
          			<h2>Dash Board</h2>
				</div>
				<div class="clear"></div>
				 <span style="font-size:17px;font-weight:bold;color:black;">Upcoming Features : <br/><br/></span>
		    
		      <ul >
			<li style="line-height:30px;">1. Detailed Call Report with Graph</li>
			<li style="line-height:30px;">2. View own pricelist rates</li>
			<li style="line-height:30px;">3. Quick Search</li>
			<li style="line-height:30px;">4. My Account Modification</li>
			<li style="line-height:30px;">5. Change Password</li>
			<li style="line-height:30px;">6. Support & Ticket Module</li>
			<li style="line-height:30px;">7. View Dashboard</li>
			<li style="line-height:30px;">8. Payment Module with history report</li>						
		      </ul><br/><br/>

				
	
    <? endblock() ?>
	<? startblock('sidebar') ?>
	

	
    <? endblock() ?>
    
<? end_extend() ?>  
