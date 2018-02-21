<? extend('master.php') ?>
<? startblock('extra_head') ?>

<script type="text/javascript">
$(document).ready(function() {

var showOrHide=false;
$("#search_bar").toggle(showOrHide);
	
$("#trunk_stats_report").flexigrid({
    url: "<?php echo base_url();?>statistics/trunkstats_json/",
    method: 'GET',
    dataType: 'json',
	colModel : [
		{display: 'Trunk Name', name: 'Number', width:170,  sortable: false, align: 'center'},
// 		{display: 'Code', name: 'code',width:110, sortable: false, align: 'center'},	
// 		{display: 'Destination', name: 'province',width:170, sortable: false, align: 'center'},
		{display: 'Attempted Calls', name: 'province',width:120, sortable: false, align: 'center'},
		{display: 'Completed Calls', name: 'CompletedCalls',width:100, sortable: false, align: 'center'},			
		{display: '<acronym title="Answer Seizure Rate.">ASR</acronym>', name: 'province',width:80, sortable: false, align: 'center'},		
		{display: '<acronym title="Average Call Duration">ACD</acronym>',width:80, name: 'city',  sortable: false, align: 'center'},     		
		{display: '<acronym title="Maximum Call Duration">MCD</acronym>',width:85, name: 'city',  sortable: false, align: 'center'},
//		{display: 'Billable', width:100,name: 'status',  sortable: false, align: 'center'},
//		{display: 'Cost', width:100,name: 'province',  sortable: false, align: 'center'},
	],
	buttons : [
		{name: 'Remove Search Filter', bclass: 'reload', onpress : clear_filter},
	],
	nowrap: false,
	showToggleBtn: false,
	sortname: "Provider",
	sortorder: "asc",
	usepager: true,
	resizable: true,
	title: '',
	useRp: true,
	rp: 10,
	showTableToggleBtn: false,
	height: "auto",	
	width: "auto",	
	pagetext: 'Page',
	outof: 'of',
	nomsg: 'No items',
	procmsg: 'Processing, please wait ...',
	pagestat: 'Displaying {from} to {to} of {total} items',
	onSuccess: function(data){
	},
	onError: function(){
	  alert("Request failed");
      }
});
$("#id_reset").click(function(){
	$("#start_date").val('');
	$("#end_date").val('');
});

$("#id_reset").click(function(){
    window.location = "<?=base_url()?>statistics/trunkstats_clear_search_sum_Report";
});
	
    $("#show_search").click(function(){
        $("#search_bar").toggle();
    });

});

function clear_filter()
{
	window.location = "<?=base_url()?>statistics/trunkstats_clear_search_sum_Report";
}

function reload_button()
{
    $('#trunk_stats_report').flexReload();
}

</script>	
<script>
    $(document).ready(function() {
        $("#provider_from_date").datetimepicker({ dateFormat: 'yy-mm-dd' });		
        $("#provider_to_date").datetimepicker({ dateFormat: 'yy-mm-dd' });			
    });
</script> 
<? endblock() ?>

<? startblock('page-title') ?>
    <?=$page_title?><br/>
<? endblock() ?>
    
<? startblock('content') ?>  
<div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">                        
    <div class="portlet-header ui-widget-header"><span id="show_search" style="cursor:pointer">Search</span><span class="ui-icon ui-icon-circle-arrow-s"></span></div>
    <div class="portlet-content" id="search_bar">
        <?=$form_search;?>
    </div>
</div>        


<div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">                        
    <div class="portlet-header ui-widget-header">Trunk stats Report<span class="ui-icon ui-icon-circle-arrow-s"></span></div>
    <div class="portlet-content">         
        <form method="POST" action="del/0/" enctype="multipart/form-data" id="ListForm">
            <table id="trunk_stats_report" align="left" style="display:none;"></table>
        </form>
    </div>
</div>  
<? endblock() ?>	
<? startblock('sidebar') ?>
    <ul id="navigation">
            <li><a href="<?php echo base_url();?>accounts/create/">Create Account</a></li>
            <li><a href="<?php echo base_url();?>accounts/account_list/">List Accounts</a></li>							
    </ul>
    <br/><br/><br/><br/><br/><br/>    	
<? endblock() ?>
<? end_extend() ?>  