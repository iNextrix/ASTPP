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
    url: "<?php echo base_url();?>statistics/listerrors/grid",
    method: 'GET',
    dataType: 'json',
	colModel : [
		{display: 'UniqueID', name: 'Number', width: 220, sortable: false, align: 'center'},
        {display: 'Date', name: 'country', width: 150, sortable: false, align: 'center'},
        {display: 'CallerID', name: 'country', width: 180, sortable: false, align: 'center'},
        {display: 'Source', name: 'province', width: 130, sortable: false, align: 'center'},
        {display: 'Dest', name: 'country', width: 150, sortable: false, align: 'center'},
        {display: 'Dest.<br/>Context', name: 'country', width: 150, sortable: false, align: 'center'},
        {display: 'Channel', name: 'country', width: 180, sortable: false, align: 'center'},
        {display: 'Dest.<br/>Channel', name: 'country', width: 180, sortable: false, align: 'center'},
        {display: 'Last<br/>App', name: 'country', width: 150, sortable: false, align: 'center'},
        {display: 'Last<br/>Data', name: 'country', width: 150, sortable: false, align: 'center'},
        {display: 'Duration', name: 'country', width: 150, sortable: false, align: 'center'},
        {display: 'BillSec', name: 'country', width: 150, sortable: false, align: 'center'},
        {display: 'Disposition', name: 'country', width: 170, sortable: false, align: 'center'},
        {display: 'AMAFlags', name: 'country', width: 150, sortable: false, align: 'center'},
        {display: 'AccountCode', name: 'country', width: 170, sortable: false, align: 'center'},
        {display: 'UserField', name: 'country', width: 180, sortable: false, align: 'center'},
        {display: 'Cost', name: 'country', width: 150, sortable: false, align: 'center'},
        {display: 'Action', name: '', width : 150, align: 'center', formatter:'showlink', formatoptions:{baseLinkUrl:'', }, },

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
	rp: 20,
	showTableToggleBtn: false,
	width: 1264,
	height: 300,
    pagetext: 'Page',
    outof: 'of',
    nomsg: 'No items',
    procmsg: 'Processing, please wait ...',
    pagestat: 'Displaying {from} to {to} of {total} items',
    //preProcess: formatContactResults,
    onSuccess: function(data){
        //alert(data);
    },
    onError: function(){
        alert("Request failed");
    },
});

$("#statistics_error_search").click(function(){
	$.ajax({type:'POST', url: '<?=base_url()?>statistics/error_search', data:$('#search_form16').serialize(), success: function(response) {
    $('#flex1').flexReload();
		}});
	});
	
	$("#id_reset").click(function(){
		$.ajax({url:'<?=base_url()?>statistics/clearsearchfilter_error/', success:function(){
		$('#flex1').flexReload(); }
		});
	});
	
	$("#show_search").click(function(){
	$("#search_bar").toggle();
	});

});

function add_button()
{
    window.location = '<?php echo base_url();?>lcr/outbound/add/';
}
function clear_filter()
{
	window.location = '<?php echo base_url();?>statistics/clearsearchfilter_error/';
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
		$("#error_from_date").datetimepicker({ dateFormat: 'yy-mm-dd' });		
		$("#error_to_date").datetimepicker({ dateFormat: 'yy-mm-dd' });			
	  });
	  </script> 
         <form action="<?=base_url()?>statistics/error_search" id="search_form16" name="form16" method="POST" enctype="multipart/form-data" style="display:block">
          <input type="hidden" name="ajax_search" value="1">
         <input type="hidden" name="advance_search" value="1">
         <ul style="list-style:none;">
          <fieldset  >
            <legend><span style="font-size:14px; font-weight:bold; color:#000;">Search List Errors</span></legend>
            	<li>
                 <div class="float-left" style="width:30%">
                 <span>
                  <label>Start Date:</label>
            	  <input size="20" class="text field" name="start_date" id="error_from_date">&nbsp;<img src="<?=base_url()?>images/calendar.png" border="0">  
                 </span>
                 </div>
                 
                 <div class="float-left" style="width:30%">
                 <span>
                   <label>End Date:</label>
            	   <input size="20" class="text field" name="end_date" id="error_to_date"> &nbsp;<img src="<?=base_url()?>images/calendar.png" border="0">     
                 </span>
                 </div>
                 
                 <div class="float-left" style="width:30%">
                 <span>
                  <label >Source :</label>
            	  <input size="20" class="text field" name="source"> &nbsp;
                  <select name="source_operator" style="padding:5px">
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
                          <label>Dst :</label>
            			  <input size="20" class="text field" name="dst"> &nbsp;
            			  <select name="dst_operator" class="field select">
                          <option value="1">contains</option>
                          <option value="2">doesn't contain</option>
                          <option value="3">is equal to</option>
                          <option value="4">is not equal to</option>
                          </select>
                        </span>
                      </div>
                      
                      <div class="float-left" style="width:30%">
                 		<span>
                           <label >Duration :</label>
            			   <input size="20" class="text field" name="duration"> &nbsp;
                           <select name="duration_operator" class="field select" style="width:132px;">
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
                          <label>Bill Sec :</label>
           				  <input size="20" class="text field" name="bill_sec"> &nbsp;
                          <select name="bill_sec_operator" class="field select" style="width:132px;">
                          <option value="1">is equal to</option>
                          <option value="2">is not equal to</option>
                          <option value="3">greater than</option>
                          <option value="4">less than</option>
                          <option value="5">greather or equal than</option>
                          <option value="6">less or equal than</option>
                          </select>
                        </span>
                      </div>  
                </li>
           	<li>
            	 <div class="float-left" style="width:30%">
                 		<span>
                         <label >Disposition :</label>
                 		 <?=form_disposition('disposition','NORMAL_CLEARING',array("class"=>"select field", "style"=>"width:307px;"))?>	
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
                   
                      
            </li>        
           
             
          </fieldset>
         </ul>
           <br />
             <input type="button" id="id_reset" class="ui-state-default float-right ui-corner-all ui-button" name="reset" value="Clear Search Filter">&nbsp;  
        <input type="button" class="ui-state-default float-right ui-corner-all ui-button" name="action" value="Search" id="statistics_error_search" style="margin-right:22px;" />
        <br><br>
        </form>                  
          </div>
        </div>
        
<div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">                        
    <div class="portlet-header ui-widget-header">Errors List<span class="ui-icon ui-icon-circle-arrow-s"></span></div>
    <div class="portlet-content">
    <form method="POST" action="del/0/" enctype="multipart/form-data" id="ListForm">            
    <table id="flex1" align="left" style="display:none;"></table>
    </form>
    </div>
</div>

<?php 
	//echo $form;
?>
    <? endblock() ?>
	
    <? startblock('sidebar') ?>
    Filter by
    <? endblock() ?>
    
<? end_extend() ?>  
