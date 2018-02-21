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

<script type="text/javascript">
$(document).ready(function() {
	
		
	var showOrHide=false;
	$("#search_bar").toggle(showOrHide);
	
$("#flex1").flexigrid({
    url: "<?php echo base_url();?>statistics/viewfscdrs/grid",
    method: 'GET',
    dataType: 'json',
	colModel : [
		{display: 'Date', name: 'Number', width: 80, sortable: false, align: 'center'},
        {display: 'CallerID', name: 'country', width: 80, sortable: false, align: 'center'},
        {display: 'Source', name: 'country', width: 65, sortable: false, align: 'center'},
        {display: 'Dest', name: 'province', width: 80, sortable: false, align: 'center'},
        {display: 'D.Context', name: 'country', width: 50, sortable: false, align: 'center'},
        {display: 'Chan', name: 'country', width: 50, sortable: false, align: 'center'},
        {display: 'D.Chan', name: 'country', width: 50, sortable: false, align: 'center'},
        {display: 'Last App', name: 'country', width: 50, sortable: false, align: 'center'},
        {display: 'Last Data', name: 'country', width: 50, sortable: false, align: 'center'},
        {display: 'Duration', name: 'country', width: 50, sortable: false, align: 'center'},
        {display: 'BillSec', name: 'country', width: 50, sortable: false, align: 'center'},
        {display: 'Disposition', name: 'country', width: 80, sortable: false, align: 'center'},
        {display: 'AMAFlags', name: 'country', width: 50, sortable: false, align: 'center'},
        {display: 'AccountCode', name: 'country', width: 70, sortable: false, align: 'center'},
        {display: 'U-ID', name: 'country', width: 50, sortable: false, align: 'center'},
        {display: 'UserField', name: 'country', width: 60, sortable: false, align: 'center'},
        {display: 'Cost', name: 'country', width: 50, sortable: false, align: 'center'},
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
	width: "auto",
	height: 600,
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

$("#id_filter").click(function(){

	var start_date = ($("#start_date").val()=='' ? 'NULL' : $("#start_date").val());
	var end_date = ($("#end_date").val()=='' ? 'NULL' : $("#end_date").val());
	var answered = $("#answered").val();
	var accountcode = $("#accountcode").val();
	var trunk = $("#trunk").val();
	
	var start_hour = $("#start_hour").val();
	var start_minute = $("#start_minute").val();
	var start_second = $("#start_second").val();
	var end_hour = $("#end_hour").val();
	var end_minute = $("#end_minute").val();
	var end_second = $("#start_hour").val();
		
	flex_url = "<?php echo base_url();?>statistics/viewcdrs/grid/"+start_date+"/"+end_date+"/"+answered+"/"+accountcode+"/"+trunk+"/"+start_hour+"/"+start_minute+"/"+start_second+"/"+end_hour+"/"+end_minute+"/"+end_second;	
	
	$('#flex1').flexOptions({url: flex_url}).flexReload();
	
});

$("#statistics_fscdrs_search").click(function(){
	$.ajax({type:'POST', url: '<?=base_url()?>statistics/fscdrs_search', data:$('#search_form18').serialize(), success: function(response) {
    $('#flex1').flexReload();
		}});
	});
	
	$("#id_reset").click(function(){
		$.ajax({url:'<?=base_url()?>statistics/clearsearchfilter_fscdrs/', success:function(){
		$('#flex1').flexReload(); }
		});
	});
	
	$("#show_search").click(function(){
	$("#search_bar").toggle();
	});

});

function add_button()
{
    window.location = '<?php echo base_url();?>/outbound/add/';
}

function clear_filter()
{
	window.location = '<?php echo base_url();?>statistics/clearsearchfilter_fscdrs/';
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
    <script>
	  $(document).ready(function() {
		$("#start_date").datepicker({ dateFormat: 'yy-mm-dd' });
		
		$("#end_date").datepicker({ dateFormat: 'yy-mm-dd' });
	  });
	  </script> 
<br/>




<div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">                        
    <div class="portlet-header ui-widget-header"><span id="show_search" style="cursor:pointer">Search</span><span class="ui-icon ui-icon-circle-arrow-s"></span></div>
    <div class="portlet-content"  id="search_bar">
     <script>
	  $(document).ready(function() {
		$("#view_free_from_date").datetimepicker({ dateFormat: 'yy-mm-dd' });		
		$("#view_free_to_date").datetimepicker({ dateFormat: 'yy-mm-dd' });			
	  });
	  </script> 
         <form action="<?=base_url()?>statistics/fscdrs_search" id="search_form18" name="form18" method="POST" enctype="multipart/form-data" style="display:block">
               <input type="hidden" name="ajax_search" value="1">
         <input type="hidden" name="advance_search" value="1">
         <ul style="list-style:none;">
          <fieldset  >
            <legend><span style="font-size:14px; font-weight:bold; color:#000;">Search View Freeswitch CDRs</span></legend>      
            	<li>
                	<div class="float-left" style="width:30%">
                 	<span>
                    <label >Start Date:</label>
           			<input size="20" class="text field" name="start_date" id="view_free_from_date"> &nbsp;<img src="<?=base_url()?>images/calendar.png" border="0">      
                    </span>
                    </div>
                    
                    <div class="float-left" style="width:30%">
                 	<span>
                     <label >End Date:</label>
           			 <input size="20" class="text field " name="end_date" id="view_free_to_date"> &nbsp;<img src="<?=base_url()?>images/calendar.png" border="0">  
                    </span>
                    </div>
                    
                    <div class="float-left" style="width:30%">
                 	<span>
                     <label >Source :</label>
           			 <input size="20" class="text field" name="source"> &nbsp;
                     <select name="source_operator"  class="field select ">
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
                     <label >Dst :</label>
            		 <input size="20" class="text field" name="dst"> &nbsp;
                     <select name="dst_operator"  class="field select ">
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
                    <select name="duration_operator"  class="field select " style="width:132px;">
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
                     <label >Bill Sec :</label>
            		 <input size="20" class="text field" name="bill_sec"> &nbsp;
                     <select name="bill_sec_operator"  class="field select " style="width:132px;">
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
                      <label >Cost:</label>
            		  <input size="20" class="text field" name="cost"> &nbsp;
           			  <select name="cost_operator"  class="field select " style="width:132px;">
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
                       <label >Account Number:</label>
                	   <input size="20" class="text field" name="account_nummber" id="account_number">
                	   <a onclick="window.open('<?=base_url()?>accounts/search_fscdrs_account_list/' , 'AccountList','scrollbars=1,width=650,height=330,top=20,left=100,scrollbars=1');" href="#"><img src="<?=base_url()?>images/icon_arrow_orange.gif" border="0"></a>
                    </span>
                    </div>
                </li>         
             
          </fieldset>
        </ul>
          <br />
          <input type="button" id="id_reset" class="ui-state-default float-right ui-corner-all ui-button" name="reset" value="Clear Search Filter">&nbsp;     
        <input type="button" class="ui-state-default float-right ui-corner-all ui-button" name="action" value="Search" id="statistics_fscdrs_search" style="margin-right:22px;" />
        <br><br>
       </form>  
    </div>
</div>

<div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">                        
    <div class="portlet-header ui-widget-header">FreeSwitch CDRs List<span class="ui-icon ui-icon-circle-arrow-s"></span></div>
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
