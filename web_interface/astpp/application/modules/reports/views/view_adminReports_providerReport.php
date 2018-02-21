<? extend('master.php') ?>
<? startblock('extra_head') ?>

<script type="text/javascript">
$(document).ready(function() {

var showOrHide=false;
$("#search_bar").toggle(showOrHide);
	
$("#provider_summary_report").flexigrid({
    url: "<?php echo base_url();?>reports/provider_sum_Report/",
    method: 'GET',
    dataType: 'json',
	colModel : [
		{display: 'Provider', name: 'Number', width:170,  sortable: false, align: 'center'},
		{display: 'Code', name: 'code',width:110, sortable: false, align: 'center'},	
		{display: 'Destination', name: 'province',width:170, sortable: false, align: 'center'},
		{display: 'Attempted Calls', name: 'province',width:120, sortable: false, align: 'center'},
		{display: 'Completed Calls', name: 'CompletedCalls',width:100, sortable: false, align: 'center'},			
		{display: '<acronym title="Answer Seizure Rate.">ASR</acronym>', name: 'province',width:80, sortable: false, align: 'center'},		
		{display: '<acronym title="Average Call Duration">ACD</acronym>',width:80, name: 'city',  sortable: false, align: 'center'},     		
		{display: '<acronym title="Maximum Call Duration">MCD</acronym>',width:85, name: 'city',  sortable: false, align: 'center'},
		{display: 'Billable', width:100,name: 'status',  sortable: false, align: 'center'},
		{display: 'Cost', width:100,name: 'province',  sortable: false, align: 'center'},
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
        
$("#id_filter").click(function(){
	var start_date = ($("#start_date").val()=='' ? 'NULL' : $("#start_date").val());
	var end_date = ($("#end_date").val()=='' ? 'NULL' : $("#end_date").val());
	var Provider = $("#Reseller").val();
	var destination = $("#destination").val();
	var pattern = $("#pattern").val();
	
	var start_hour = $("#start_hour").val();
	var start_minute = $("#start_minute").val();
	var start_second = $("#start_second").val();
	var end_hour = $("#end_hour").val();
	var end_minute = $("#end_minute").val();
	var end_second = $("#start_hour").val();
	
	//var flex_url = "<?php echo base_url();?>adminReports/resellerReport/grid/?"+encodeURIComponent("filter_ok=1&account="+account+"&company="+company+"&fname="+fname+"&lname="+lname);
	flex_url = "<?php echo base_url();?>adminReports/providerReport/grid/"+start_date+"/"+end_date+"/"+Provider+"/"+destination+"/"+encodeURIComponent(pattern)+"/"+start_hour+"/"+start_minute+"/"+start_second+"/"+end_hour+"/"+end_minute+"/"+end_second;
	$('#flex1').flexOptions({url: flex_url}).flexReload();
});
$("#id_reset").click(function(){
	$("#start_date").val('');
	$("#end_date").val('');
});

$("#id_reset").click(function(){
    window.location = "<?=base_url()?>reports/provider_clear_search_sum_Report";
});
	
    $("#show_search").click(function(){
        $("#search_bar").toggle();
    });

});

function add_button()
{
    window.location = '<?php echo base_url();?>callingcards/add/';
}

function clear_filter()
{
	window.location = '<?php echo base_url();?>adminReports/clearsearchfilter_provider/';
}

function delete_button()
{
	confirm_string = '{% trans " you are hiding & stopping a campaign" %}';
    if( confirm("are you sure to delete?") == true)
	    $('#ListForm').submit();
}
function reload_button()
{
    $('#provider_summary_report').flexReload();
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
    <div class="portlet-header ui-widget-header">Provider Summary Report<span class="ui-icon ui-icon-circle-arrow-s"></span></div>
    <div class="portlet-content">         
        <form method="POST" action="del/0/" enctype="multipart/form-data" id="ListForm">
            <table id="provider_summary_report" align="left" style="display:none;"></table>
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