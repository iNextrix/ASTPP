<? extend('master.php') ?>
<? startblock('extra_head') ?>
<script type="text/javascript">
$(document).ready(function() {
  var showOrHide=false;
  $("#search_bar").toggle(showOrHide);
  $("#flex1").flexigrid({
    url: "<?php echo base_url(); ?>freeswitch/livecall_report_json/",
    method: 'GET',
    dataType: 'json',
 colModel : [
		{display: 'Action', name: 'Action', width: 90, sortable: true, align: 'center'},
		{display: 'Call Date', name: 'Call Date', width: 70, sortable: true, align: 'center'},
		{display: 'CID', name: 'CID Name', width: 70, sortable: true, align: 'center'},
		{display: 'Caller IP', name: 'IP Address', width: 75, sortable: true, align: 'center'},
		{display: 'Customer', name: 'Customer', width: 75, sortable: true, align: 'center'},
		{display: 'Org.<br/>Pefix', name: 'Org. Prefix', width: 60, sortable: true, align: 'center'},
		{display: 'Org.<br/>Destination', name: 'Org. Destination', width: 90, sortable: true, align: 'center'},
		{display: 'Org.<br/>Cost', name: 'Org. Cost', width: 80, sortable: true, align: 'center'},
		{display: 'Term.<br/>Trunk', name: 'Term. Trunk', width: 80, sortable: true, align: 'center'},
		{display: 'Term.<br/>Prefix', name: 'Term. Prefix', width: 60, sortable: true, align: 'center'},
		{display: 'Term.<br/>Destination', name: 'Term. Destination', width: 80, sortable: true, align: 'center'},
		{display: 'Term.<br/>Cost', name: 'Term. Cost', width: 80, sortable: true, align: 'center'},
		{display: 'Destination', name: 'Destination', width: 90, sortable: true, align: 'center'},
		{display: 'Duration', name: 'Duration', width: 70, sortable: true, align: 'center'},
		{display: 'Type', name: 'Type', width: 60, sortable: true, align: 'center'},
		{display: 'Status', name: 'Call State', width: 80, sortable: true, align: 'center'},
        {display: 'Read / Write <br/>codecs', name: 'Codecs', width: 200, sortable: true, align: 'center'}
		],
	nowrap: false,
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
	nomsg: 'No Records',
	procmsg: 'Processing, please wait ...',
	pagestat: 'Displaying {from} to {to} of {total} items',
	//preProcess: formatContactResults,
	onSuccess: function(data){
	  $('a[rel*=facebox]').facebox({
		    loadingImage : '<?php echo base_url(); ?>/images/loading.gif',
		    closeImage   : '<?php echo base_url(); ?>/images/closelabel.png'
	    });
	},
	
});


});

function hangupcall(uuid){
	 $.ajax({
      type: "POST",
      url: "<?php echo base_url(); ?>freeswitch/call_hangup/"+uuid,
      data: '',
      success:function(response) {
         alert(response);
      }
      });
}

function reload_button()
{
    $('#flex1').flexReload();
    
}
</script>

<script type="text/javascript">
setInterval( "refreshAjax();", 10000 ); 

$(function() {
  refreshAjax = function(){$("#flex1").flexReload();
}
});
</script>

<? endblock() ?>

    <? startblock('page-title') ?>
        <?=$page_title?>
    <? endblock() ?>
    
	<? startblock('content') ?>





<section class="slice color-three">
	<div class="w-section inverse no-padding">
		<div class="row">
			<div class="portlet-content" id="search_bar"
				style="cursor: pointer; display: none">
                    	<?php echo $form_search; ?>
    	        </div>
		</div>
	</div>
</section>

<section class="slice color-three pb-4">
	<div class="w-section inverse p-0">
		<div class="card col-md-12 p-4">
			<form method="POST" action="del/0/" enctype="multipart/form-data"
				id="ListForm">
				<table id="flex1" align="left" style="display: none;"></table>
			</form>
		</div>
	</div>
</section>




<? endblock() ?>
<? end_extend() ?>  

