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
		{display: 'Call Date', name: 'Call Date', width: 110, sortable: false, align: 'center'},
		{display: 'CID Name', name: 'CID Name', width: 120, sortable: false, align: 'center'},
		{display: 'CID Number', name: 'CID Number', width: 120, sortable: false, align: 'center'},
		{display: 'IP Address', name: 'IP Address', width: 120, sortable: false, align: 'center'},
		{display: 'Destination', name: 'Destination', width: 120, sortable: false, align: 'center'},
		{display: 'Bridge', name: 'Bridge', width: 200, sortable: false, align: 'center'},
		{display: 'Read codec', name: 'Read Codec', width: 120, sortable: false, align: 'center'},
		{display: 'Write codec', name: 'Write Codec', width: 120, sortable: false, align: 'center'},
		{display: 'Call State', name: 'Call State', width: 110, sortable: false, align: 'center'},
		{display: 'Duration', name: 'Duration', width: 130, sortable: false, align: 'center'}
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
	/*onError: function(){
	    alert('Sorry, we are unable to connect to freeswitch!!!');
	}*/
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
        <?=$page_title?>
    <? endblock() ?>
    
	<? startblock('content') ?>





<section class="slice color-three">
	<div class="w-section inverse no-padding">
    	<div class="container">
   	    <div class="row">
            	<div class="portlet-content"  id="search_bar" style="cursor:pointer; display:none">
                    	<?php echo $form_search; ?>
    	        </div>
            </div>
        </div>
    </div>
</section>

<section class="slice color-three padding-b-20">
	<div class="w-section inverse no-padding">
    	<div class="container">
        	<div class="row">
                <div class="col-md-12"> 
					<br/>     
                        <form method="POST" action="del/0/" enctype="multipart/form-data" id="ListForm">
                            <table id="flex1" align="left" style="display:none;"></table>
                        </form>
                </div>  
            </div>
        </div>
    </div>
</section>




<? endblock() ?>
<? end_extend() ?>  
