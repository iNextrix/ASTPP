<? extend('master.php') ?>

	<? startblock('extra_head') ?>

<!--flexigrid css & js-->
<link rel="stylesheet" href="<?=base_url()?>css/flexigrid.css" type="text/css" />
<script type="text/javascript" src="<?=base_url()?>js/flexigrid.js"></script>

<script type="text/javascript" language="javascript">
function get_alert_msg(id)
{
    confirm_string = 'are you sure to delete?';
    var answer = confirm(confirm_string);
    return answer // answer is a boolean
}
</script>

<script type="text/javascript">
$(document).ready(function() {
	
		var showOrHide=false;
	$("#search_bar").toggle(showOrHide);
	
$("#flex1").flexigrid({
    url: "<?php echo base_url();?>cdrReports/resellerReport_grid/",
    method: 'GET',
    dataType: 'json',
	colModel : [
<!--        {display: '<input type="checkbox" onclick="toggleChecked(this.checked)">', name: '', width: 20, align: 'center'},\
-->
	{display: 'Date', name: 'Date', width:75, sortable: false, align: 'center'},
        {display: 'CallerID', name: 'country', width: 100, sortable: false, align: 'center'},
        {display: 'Called Number', name: 'province', width: 80, sortable: false, align: 'center'},
        {display: 'Account Number', name: 'city', width: 90, sortable: false, align: 'center'},        
        {display: 'BillSec', name: 'status', width: 50, sortable: false, align: 'center'},
        {display: 'Disposition', name: 'calls', width: 100, sortable: false, align: 'center'},
        {display: 'Debit', name: 'vm', width: 60, sortable: false, align: 'center'},
        {display: 'Cost', name: 'failed', width: 60, sortable: false, align: 'center'},	
	{display: 'Trunk', name: 'failed', width: 80, sortable: false, align: 'center'},
	{display: 'Provider', name: 'failed', width: 80, sortable: false, align: 'center'},	
	{display: 'Pricelist', name: 'minutes', width: 70, sortable: false, align: 'center'},
	{display: 'Code', name: 'xfers', width: 50, sortable: false, align: 'center'},
        {display: 'Destination', name: 'na', width: 100, sortable: false, align: 'center'},
	{display: 'Call Type', name: 'na', width: 80, sortable: false, align: 'center'},
		],
    buttons : [
		{name: 'Refresh', bclass: 'reload', onpress : reload_button},
		{separator: true},
		{name: 'Remove Search Filter', bclass: 'reload', onpress : clear_filter},
		],
	nowrap: false,
			
	showToggleBtn: false,
    sortname: "id",
	sortorder: "asc",
	usepager: true,
	resizable: false,
	useRp: true,
	rp: 10,
	showTableToggleBtn: false,
	width: "auto",
	height: 250,
    pagetext: 'Page',
    outof: 'of',
    nomsg: 'No items',
    procmsg: 'Processing, please wait ...',
    pagestat: 'Displaying {from} to {to} of {total} items',
    onSuccess: function(data){
        //alert(data);
    },
    onError: function(){
        alert("Request failed");
    },
});


$("#reseller_cdrs_search").click(function(){
	$.ajax({type:'POST', url: '<?=base_url()?>cdrReports/resellerReport_search', data:$('#form9').serialize(), success: function(response) {
    $('#flex1').flexReload();
}});
	});
	
	$("#id_reset").click(function(){
		$.ajax({url:'<?=base_url()?>cdrReports/clearsearchfilter_resellerReports', success:function(){
		$('#flex1').flexReload();	}
		});
	});

$("#show_search").click(function(){
	$("#search_bar").toggle();
	});

});

function add_button()
{
    window.location = '/callingcards/add/';
}
function clear_filter()
{
	window.location = '<?php echo base_url();?>cdrReports/clearsearchfilter_resellerReports/';
}

function delete_button()
{
	confirm_string = '{% trans " you are hiding & stopping a campaign" %}';
    if( confirm("are you sure to delete?") == true)
	    $('#ListForm').submit();
}
function reload_button()
{
    $('#flex1').flexReload();
}

</script>			
		
	<? endblock() ?>

    <? startblock('page-title') ?>
        <?=$page_title?><br/>
    <? endblock() ?>
    
	<? startblock('content') ?>
<br/>
<div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all" id="searchbar">                        
            <div class="portlet-header ui-widget-header"><span id="show_search" style="cursor:pointer">Search</span><span class="ui-icon ui-icon-circle-arrow-s"></span></div>
            <div class="portlet-content" id="search_bar">
      
              <script>
	  $(document).ready(function() {
		$("#reseller_cdr_from_date").datetimepicker({ dateFormat: 'yy-mm-dd' });		
		$("#reseller_cdr_to_date").datetimepicker({ dateFormat: 'yy-mm-dd' });			
	  });
	  </script> 
          <form action="<?=base_url()?>cdrReports/resellerReport_search" id="form9" name="form9" method="POST" enctype="multipart/form-data" style="display:block">
            <input type="hidden" name="ajax_search" value="1">
         <input type="hidden" name="advance_search" value="1">
         <ul style="list-style:none;">
          <fieldset  >
            <legend><span style="font-size:14px; font-weight:bold; color:#000;">Search Reseller Call Report </span></legend>
            	<li>
                     
                      <div class="float-left" style="width:30%">
                 	 <span>
                     	   <label> From date & Time :</label>
           				   <input size="20" class="text field" name="start_date" id="reseller_cdr_from_date"> &nbsp;<img src="<?=base_url()?>images/calendar.png" border="0"> 
                     </span>
                     </div>
                     
                      <div class="float-left" style="width:30%">
                 	 <span>
                     	   <label >To date & Time :</label>
            				<input size="20" class="text field" name="end_date" id="reseller_cdr_to_date"> &nbsp;<img src="<?=base_url()?>images/calendar.png" border="0">        
                     </span>
                     </div>
                     
                      <div class="float-left" style="width:30%">
                 	 <span>
                      <label >Caller ID:</label>
            		  <input size="20" class="text field" name="caller_id"> &nbsp;
            		  <select name="caller_id_operator" class="field select">
                      <option value="1">contains</option>
                      <option value="2">doesn't contain</option>
                      <option value="3">is equal to</option>
                      <option value="4">is not equal to</option>
                      </select>	
                     </span>
                     </div>
                     
                     
                </li>
                <li>

                      <div class="float-left" style="width:30%">
                 	 <span>
                       <label >Called Number:</label>
           			   <input size="20" class="text field" name="dest"  > &nbsp;
                       <select name="dest_operator" class="field select">
                       <option value="1">contains</option>
                       <option value="2">doesn't contain</option>
                       <option value="3">is equal to</option>
                       <option value="4">is not equal to</option>
                       </select>	
                     </span>
                     </div>
                     
                      <div class="float-left" style="width:30%">
                 	 <span>
                     	   <label>Bill Sec  :</label>
              			   <input size="20" class="text field" name="bill_sec"> &nbsp;
                           <select name="bill_sec_operator" style="width:132px;" class="field select">
                           <option value="1">is equal to</option>
                           <option value="2">is not equal to</option>
                           <option value="3">greater than</option>
                           <option value="4">less than</option>
                           <option value="5">greather or equal than</option>
                           <option value="6">less or equal than</option>
                           </select>
                     </span>
                     </div>          
                     <div class="float-left" style="width:30%">
                 	 <span>
                     	 <label>Disposition :</label>
                  <?=form_disposition('disposition','',array("class"=>"select field" , "style"=>"width:307px;"))?>
                     </span>
                     
                </li>
                <li>
                	
                <div class="float-left" style="width:30%">
					<span>
                     <label >Account Number:</label>
                	<input size="20" class="text field" name="reseller" id="reseller">
                <a onclick="window.open('<?=base_url()?>accounts/search_did_account_list/' , 'AccountList','scrollbars=1,width=650,height=330,top=20,left=100,scrollbars=1');" href="#"><img src="<?=base_url()?>images/icon_arrow_orange.gif" border="0"></a>
                    </span>
                    </div>                    		  
		  
		  
		  	 <div class="float-left" style="width:30%">
                <span>
                  <label >Trunk</label>
                  <select class="select field" name="trunk" style="width:307px;" >
                  <?=$trunks?>
                  </select>
                </span>
                </div>
		  
		    <div class="float-left" style="width:30%">
				      <span>
		    <label>Provider:</label>
			<input size="20" class="text field" name="provider" id="provider">
		    <a onclick="window.open('<?=base_url()?>accounts/search_trunks_provider_list/' , 'ProviderList','scrollbars=1,width=650,height=330,top=20,left=100,scrollbars=1');" href="#"><img src="<?=base_url()?>images/icon_arrow_orange.gif" border="0"></a>
		  </span>
		  </div>
                </li>
            
           		<li>
           		
           		<div class="float-left" style="width:30%">
                 	 <span>
                     	  <label>Debit:</label>
               			  <input size="20" class="text field" name="debit"> &nbsp;
                          <select name="debit_operator" class="field select" style="width:132px;">
                          <option value="1">is equal to</option>
                          <option value="2">is not equal to</option>
                          <option value="3">greater than</option>
                          <option value="4">less than</option>
                          <option value="5">greather or equal than</option>
                          <option value="6">less or equal than</option>
                          </select>                          
                     </span>
                     </div>
                     
                     <div class="float-left" style="width:30%">
                 	 <span>
                     	  <label>Cost:</label>
               			  <input size="20" class="text field" name="cost"> &nbsp;
                          <select name="cost_operator" class="field select" style="width:132px;">
                          <option value="1">is equal to</option>
                          <option value="2">is not equal to</option>
                          <option value="3">greater than</option>
                          <option value="4">less than</option>
                          <option value="5">greather or equal than</option>
                          <option value="6">less or equal than</option>
                          </select>                          
                     </span>
                     </div>
           		
                	 <div class="float-left" style="width:30%">
                 	 <span>
                      <label >Pricelist :</label>
			  <?=form_select_default('pricelist',@$pricelist,"",array("class"=>"select field", "style"=>"width:307px;"), '--Select PriceList--');?>	
                     </span>
                     </div>	
                   </li>
                   <li>
                     
                      <div class="float-left" style="width:30%">
			<span>
                       <label >Code:</label>
            		   <input size="20" class="text field" name="pattern"> &nbsp;
                       <select name="pattern_operator" class="field select">
                       <option value="1">contains</option>
                       <option value="2">doesn't contain</option>
                       <option value="3">is equal to</option>
                       <option value="4">is not equal to</option>
                       </select>	
                     </span>
                     </div>
                     
                     <div class="float-left" style="width:30%">
			<span>
                       <label >Destination:</label>
            		   <input size="20" class="text field" name="notes"> &nbsp;
                       <select name="notes_operator" class="field select">
                       <option value="1">contains</option>
                       <option value="2">doesn't contain</option>
                       <option value="3">is equal to</option>
                       <option value="4">is not equal to</option>
                       </select>	
                     </span>
                     </div>
                     
                     <div class="float-left" style="width:30%">
			<span>
                       <label>Call Type:</label>            		   
                       <select name="calltype" class="field select">
                       <option value="">--Select Call Type--</option>
                       <option value="STANDARD">STANDARD</option>
                       <option value="DID">DID</option>                       
                       </select>	
                     </span>
                     </div>
                     
                </li>            
          </fieldset>
         </ul>
         
          <br />
           <input type="button" id="id_reset" class="ui-state-default float-right ui-corner-all ui-button" name="reset" value="Clear Search Filter">&nbsp; 
        <input type="button" class="ui-state-default float-right ui-corner-all ui-button" name="action" value="Search" id="reseller_cdrs_search" style="margin-right:22px;" />
        <br><br>
        
        </form>    
         
                   
            </div>
        </div>
<div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">                        
    <div class="portlet-header ui-widget-header">Reseller Call Detail Report<span class="ui-icon ui-icon-circle-arrow-s"></span></div>
    <div class="portlet-content">
    <form method="POST" action="del/0/" enctype="multipart/form-data" id="ListForm">    
    <table id="flex1" align="left" style="display:none;"></table>
    </form>
    </div>
</div>
<div style="float:right;"><strong><a href="/cdrReports/export_cdr_reseller_xls">Export XLS <img src="/images/file_tree/xls.png" alt='XLS'/></a> | <a href="/cdrReports/export_cdr_reseller_pdf">Export PDF <img src="/images/file_tree/pdf.png" alt='PDF'/></a></strong></div>
<br/><br/>

<?php 
	//echo $form;
?>
    <? endblock() ?>
	
    <? startblock('sidebar') ?>
    Filter by
    <? endblock() ?>
    
<? end_extend() ?>  
