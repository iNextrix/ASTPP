<? extend('master.php') ?>

	<? startblock('extra_head') ?>

<!--flexigrid css & js-->
<link href="/css/ui/ui.datepicker.css" rel="stylesheet" media="all">
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
    url: "<?php echo base_url();?>statistics/trunkstats/grid",
    method: 'GET',
    dataType: 'json',
	colModel : [
		{display: 'Trunk Name', name: 'Number', width: 100, sortable: false, align: 'center'},
        {display: 'Calls', name: 'country', width: 100, sortable: false, align: 'center'},
        {display: 'ACD<br/>( Average Call Duration )', name: 'country', width: 150, sortable: false, align: 'center'},
        {display: 'ACWT<br/>( Average Call Wait Time )', name: 'province', width: 150, sortable: false, align: 'center'},
        {display: 'Success', name: 'country', width: 100, sortable: false, align: 'center'},
        {display: 'Congestion', name: 'country', width: 100, sortable: false, align: 'center'},
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

$("#id_filter").click(function(){
	var start_date = ($("#start_date").val()=='' ? 'NULL' : $("#start_date").val());
	var end_date = ($("#end_date").val()=='' ? 'NULL' : $("#end_date").val());
	
	var start_hour = $("#start_hour").val();
	var start_minute = $("#start_minute").val();
	var start_second = $("#start_second").val();
	var end_hour = $("#end_hour").val();
	var end_minute = $("#end_minute").val();
	var end_second = $("#start_hour").val();
	
	flex_url = "<?php echo base_url();?>statistics/trunkstats/grid/"+start_date+"/"+end_date+"/"+start_hour+"/"+start_minute+"/"+start_second+"/"+end_hour+"/"+end_minute+"/"+end_second;	
	$('#flex1').flexOptions({url: flex_url}).flexReload();	
});

/*$('#datepicker').datepicker({
			changeMonth: true,
			changeYear: true
		});*/
		
	$("#statistics_trunkstats_search").click(function(){
	$.ajax({type:'POST', url: '<?=base_url()?>statistics/trunkstats_search', data:$('#search_form17').serialize(), success: function(response) {
    $('#flex1').flexReload();
		}});
	});	
	
	$("#id_reset").click(function(){
		$.ajax({url:'<?=base_url()?>statistics/clearsearchfilter_trunkstats/', success:function(){
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
	window.location = '<?php echo base_url();?>statistics/clearsearchfilter_trunkstats/';
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
    <div class="portlet-content" id="search_bar">
   <script language="javascript"> 
      $(document).ready(function() {
		$("#trunk_stat_from_date").datetimepicker({ dateFormat: 'yy-mm-dd' });		
		$("#trunk_stat_to_date").datetimepicker({ dateFormat: 'yy-mm-dd' });			
	  });
	  </script> 
      <form action="<?=base_url()?>statistics/trunkstats_search" id="search_form17" name="form17" method="POST" enctype="multipart/form-data" style="display:block">
        <input type="hidden" name="ajax_search" value="1">
         <input type="hidden" name="advance_search" value="1">
         <ul style="list-style:none;">
          <fieldset  >
            <legend><span style="font-size:14px; font-weight:bold; color:#000;">Search Trunk Stats</span></legend>
            	<li>
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
                        <label >Start Date:</label>
           				<input size="20" class="text field" name="start_date" id="trunk_stat_from_date"> &nbsp;<img src="<?=base_url()?>images/calendar.png" border="0"> 	
                     </span>
                     </div>
                     
                     <div class="float-left" style="width:30%">
                 	 <span>
                       <label >End Date:</label>
            		   <input size="20" class="text field" name="end_date" id="trunk_stat_to_date">&nbsp;<img src="<?=base_url()?>images/calendar.png" border="0">       	
                     </span>
                     </div>
                     
                </li>
            
         </fieldset>
        </ul>
          <br />
           <input type="button" id="id_reset" class="ui-state-default float-right ui-corner-all ui-button" name="reset" value="Clear Search Filter">&nbsp;   
        <input type="button" class="ui-state-default float-right ui-corner-all ui-button" name="action" value="Search" id="statistics_trunkstats_search" style="margin-right:22px;" />
        <br><br>
       </form>  
    </div>
</div>

<div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">                        
    <div class="portlet-header ui-widget-header">Trunks Stats<span class="ui-icon ui-icon-circle-arrow-s"></span></div>
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
