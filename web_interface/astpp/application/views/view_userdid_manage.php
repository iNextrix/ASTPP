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

<style>
    fieldset{
        width: 609px;
    }
    
</style>		

<script type="text/javascript">
$(document).ready(function() {
$("#flex1").flexigrid({
    url: "<?php echo base_url();?>userdid/manage_json/",
    method: 'GET',
    dataType: 'json',
	colModel : [
<!--        {display: '<input type="checkbox" onclick="toggleChecked(this.checked)">', name: '', width: 20, align: 'center'},\
-->
		{display: 'Number', name: 'Number', width: 90, sortable: false, align: 'center'},
        {display: 'Connect Fee', name: 'ConnectFee', width: 90, sortable: false, align: 'center'},
        {display: 'Included Seconds', name: 'IncludedSeconds', width: 90, sortable: false, align: 'center'},
		 {display: 'Cost', name: 'Cost', width: 90, sortable: false, align: 'center'},
        {display: 'Monthly Fee', name: 'MonthlyFee', width: 90, sortable: false, align: 'center'},
        {display: 'Country', name: 'Country', width: 90, sortable: false, align: 'center'},
        {display: 'Province/State', name: 'ProvinceState', width: 90, sortable: false, align: 'center'},
        {display: 'City', name: 'City', width: 90, sortable: false, align: 'center'},
        {display: '	Extension to dial', name: '	Extensiontodial', width: 90, sortable: false, align: 'center'},
       {display: 'Action', name: '', width : 50, align: 'center', formatter:'showlink', formatoptions:{baseLinkUrl:'', }, },

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
	height: 300,
    pagetext: 'Page',
    outof: 'of',
    nomsg: 'No items',
    procmsg: 'Processing, please wait ...',
    pagestat: 'Displaying {from} to {to} of {total} items',
    onSuccess: function(data){
        $('a[rel*=facebox]').facebox({
        	loadingImage : '<?php echo base_url();?>/images/loading.gif',
        	closeImage   : '<?php echo base_url();?>/images/closelabel.png'
      	});
    },
    onError: function(){
        alert("Request failed");
    },
});
});

function add_button()
{
    jQuery.facebox({ ajax: '<?php echo base_url();?>did/manage/add/'});
}
function import_button()
{
    jQuery.facebox({ ajax: '<?php echo base_url();?>did/import/'});
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
<div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">                        
        <div class="portlet-header ui-widget-header">DIDs<span class="ui-icon ui-icon-circle-arrow-s"></span></div>
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
