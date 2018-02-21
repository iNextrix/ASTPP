<? extend('master.php') ?>

	<? startblock('extra_head') ?>
		
<!--flexigrid css & js-->
<link rel="stylesheet" href="/css/flexigrid.css" type="text/css" />
<script type="text/javascript" src="/js/flexigrid.js"></script>

<script type="text/javascript" language="javascript">
function get_alert_msg(id)
{
    confirm_string = 'are you sure to delete?';
    var answer = confirm(confirm_string);
    return answer // answer is a boolean
}
</script>

	

<style>
    fieldset{
        text-align: center;
        
    }
</style>
	<? endblock() ?>

    <? startblock('page-title') ?>
        <?=$page_title?><br/>
    <? endblock() ?>
    
	<? startblock('content') ?>  
     <br>

<div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">                        
    <div class="portlet-header ui-widget-header">&nbsp;&nbsp;Booth Name: <?=@$booth_name?><span class="ui-icon ui-icon-circle-arrow-s"></span></div>
    <div class="portlet-content">         
	 <div class="hastable">
        <form method="POST" enctype="multipart/form-data" id="ListForm" action="<?=base_url()?>callshops/booth_action">
		<input type="hidden" name="booth_list" value="<?=@$booth_name?>" />
        <input type="hidden" size="20" name="booth_name" value="<?=@$booth_name?>">
		<table class="details_table" width="100%">
	<tr class="header">
		<td>Balance</td>
		<td>Unrated CDRs</td>
		<td colspan=10 align=center>Actions</td>
	</tr>
	<tr>
		<td><?=@$balance?></td>
		<td><?=@$unrated?></td>
		<td colspan=10 align=center>
		<input name="action" value="Generate Invoice" type="submit">
		<input name="action" value="Remove CDRs" type="submit"></td>
	</tr>
	<tr class="header">
	  <td colspan="11">&nbsp;</td>
	  </tr>
	<tr class="header">
       		 <td>UniqueID</td>
       		 <td>Date & Time</td>
        	<td>Caller*ID</td>
        	<td>Called Number</td>
        	<td>Disposition</td>
        	<td>Billable Seconds</td>
        	<td>Charge</td>
        	<td>Credit</td>
        	<td>Notes</td>    
        	<td>Cost</td>    
		<td>Profit</td>
      </tr>
      <?php 
	  foreach($cdrs as $key => $value)
	  	{?>
            <TR>
               <TD><?=$value['uniqueid']?></TD>
               <TD><?=$value['callstart']?></TD>
               <TD><?=$value['callerid']?></TD>           
               <TD><?=$value['callednum']?></TD>
               <TD><?=$value['disposition']?></TD>
               <TD><?=$value['billseconds']?></TD>
               <TD><?=$value['debit']?></TD>
               <TD><?=$value['credit']?></TD>
               <TD><?=$value['notes']?></TD>
               <TD><?=$value['cost']?></TD>
               <TD><?=$value['profit']?></TD>
             </TR>
      <? } ?>
</table>
<br /><br />
        <table class="details_table" width="50%" align="center">
            <tr class="header">
                <td colspan=4>VOIP Connection Info</td>
          </tr>
            <tr class="header">
                <td>SIP Username</td>
                <td>SIP Password</td>
                <td>IAX2 Username</td>
                <td>IAX2 PAssword</td>
            </tr>
           <tr>
           <td><?=@$sip_login['name']?></td>
           <td><?=@$sip_login['secret']?></td>
           <td><?=@$iax_login['name']?></td>
           <td><?=@$iax_login['secret']?></td>
           </tr>
           
        </table>
     
	  </form>
      </div>
	</div>
</div>  
    <? endblock() ?>	
    <? startblock('sidebar') ?>						
		<br/><br/><br/><br/><br/><br/>    	

    <? endblock() ?>
    
<? end_extend() ?>  
