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
$("#flex1").flexigrid({
    url: "<?php echo base_url();?>statistics/viewcdrs/grid",
    method: 'GET',
    dataType: 'json',
	colModel : [
		{display: 'Date', name: 'Number', width: 80, sortable: false, align: 'center'},
        {display: 'CallerID', name: 'country', width: 80, sortable: false, align: 'center'},
        {display: 'Source', name: 'country', width: 50, sortable: false, align: 'center'},
        {display: 'Dest', name: 'province', width: 100, sortable: false, align: 'center'},
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

});

function add_button()
{
    window.location = '<?php echo base_url();?>/outbound/add/';
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
    <div class="portlet-header ui-widget-header">Search<span class="ui-icon ui-icon-circle-arrow-s"></span></div>
    <div class="portlet-content">
    <ul id="search-filters2" style="width:1100px">    
    <li>
    <label class="desc">Start Date</label>
    <input class="text field medium" type="text" name="start_date" value="<?=@$start_date?>" size="10" id="start_date">
  
    </li>
    <li>
    <label class="desc">Start Time</label>
    <input class="text field small" type="text" name="start_hour" id="start_hour" value="<?=@$start_hour?>" size="2">
	<input class="text field small" type="text" name="start_minute" id="start_minute" value="<?=@$start_minute?>" size="2">
	<input class="text field small" type="text" name="start_second" id="start_second" value="<?=@$start_second?>" size="2">
    </li>
    <li>
    <label class="desc">End Date</label>
   <input class="text field medium" type="text" name="end_date" value="<?=@$end_date?>" size="5" id="end_date">
    </li>
    <li>
    <label class="desc">End Time</label>
   <input class="text field small" type="text" name="end_hour" id="end_hour" value="<?=@$end_hour?>" size="5">
	<input class="text field small" type="text" name="end_minute" id="end_minute" value="<?=@$end_minute?>" size="5">
	<input class="text field small" type="text" name="end_second" id="end_second" value="<?=@$end_second?>" size="5">
    </li>
    <li>
     <label class="desc">Answered Calls Only?</label>
     <select name="answered" id="answered">
		<option value="1">YES</option>
		<option value="0">NO</option>
	</select>
    </li>
    <li>
    <label class="desc">AccountCode</label>
    <input type="text" size="10" class="text field" name="accountcode" id="accountcode" />
    </li>
    <li>
    <label class="desc">Select Outbound Trunk?</label>
    <select class="select field" name="trunk"  id="trunk">
    <option value=""></option>
    <?php foreach($trunklist as $value){?>
    <option value="<?=$value?>"><?=$value?></option>
    <? } ?>
    </select>
    </li>
     
    <li style="margin-top: 15px;">
    <input class="ui-state-default ui-corner-all ui-button" type="submit" value="Filter"  id="id_filter">
    </li>
    </ul>
    </div>
</div>

<div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">                        
    <div class="portlet-header ui-widget-header">CDRs List<span class="ui-icon ui-icon-circle-arrow-s"></span></div>
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
