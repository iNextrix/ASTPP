<? extend('master.php') ?>
<? startblock('extra_head') ?>
<script type="text/javascript">
$(document).ready(function() {
  var showOrHide=false;
  $("#search_bar").toggle(showOrHide);
  $("#flex1").flexigrid({
    url: "<?php echo base_url();?>freeswitch/livecall_report_json/",
    method: 'GET',
    dataType: 'json',
	colModel : [
		{display: 'Call Date', name: 'Call Date', width: 125, sortable: false, align: 'center'},
		{display: 'CID Name', name: 'CID Name', width: 120, sortable: false, align: 'center'},
		{display: 'CID Number', name: 'CID Number', width: 120, sortable: false, align: 'center'},
		{display: 'IP Address', name: 'IP Address', width: 120, sortable: false, align: 'center'},
		{display: 'Destination', name: 'Destination', width: 120, sortable: false, align: 'center'},
		{display: 'Bridge', name: 'Bridge', width: 220, sortable: false, align: 'center'},
		{display: 'Read codec', name: 'Read Codec', width: 80, sortable: false, align: 'center'},
		{display: 'Write codec', name: 'Write Codec', width: 80, sortable: false, align: 'center'},
		{display: 'Call State', name: 'Call State', width: 100, sortable: false, align: 'center'},
		],
	buttons : [
		{name: 'Refresh', bclass: 'reload', onpress : reload_button}	
		],
	nowrap: true,
	showToggleBtn: false,
	sortname: "id",
	sortorder: "asc",
	usepager: true,
	resizable: true,
	useRp: true,
	rp: 500,
	showTableToggleBtn: false,
	width: "auto",
	height: "auto",
	pagetext: 'Page',
	outof: 'of',
	nomsg: 'No items',
	procmsg: 'Processing, please wait ...',
	pagestat: 'Displaying {from} to {to} of {total} items',
	//preProcess: formatContactResults,
	onSuccess: function(data){
	  $('a[rel*=facebox]').facebox({
		    loadingImage : '<?php echo base_url();?>/images/loading.gif',
		    closeImage   : '<?php echo base_url();?>/images/closelabel.png'
	    });
	},
	onError: function(){
	    alert('Sorry, we are unable to connect to freeswitch!!!');
	}
});
});

function reload_button()
{
    $('#flex1').flexReload();
    
}
</script>

<script type="text/javascript">
setInterval( "refreshAjax();", 10000 );  ///////// 20 seconds

$(function() {
  refreshAjax = function(){$("#flex1").flexReload();
}
});
</script>

	<? endblock() ?>

    <? startblock('page-title') ?>
        <?=$page_title?><br/>
    <? endblock() ?>
    
	<? startblock('content') ?>
<div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">                        
    <div class="portlet-header ui-widget-header">Live Call Report<span class="ui-icon ui-icon-circle-arrow-s"></span></div>
    <div class="portlet-content">
    <form method="POST" action="del/0/" enctype="multipart/form-data" id="ListForm">    
    <table id="flex1" align="left" style="display:none;"></table>
    </form>
    </div>
</div>
<? endblock() ?>
<? end_extend() ?>  
