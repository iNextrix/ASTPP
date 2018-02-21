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
    url: "<?php echo base_url();?>callshops/manage_booths_json/grid",
    method: 'GET',
    dataType: 'json',
	colModel : [
		{display: 'Booth Name', name: 'BoothName', width:80,  sortable: false, align: 'center'},
        {display: 'Balance', name: 'Balance',width:80, sortable: false, align: 'center'},
        {display: 'Currency', name: 'Currency',width:90, sortable: false, align: 'center'},
		{display: 'Call Count', name: 'CallCount',width:90, sortable: false, align: 'center'},
		{display: 'In Use', name: 'InUse',width:90, sortable: false, align: 'center'},
		{display: 'Duration', name: 'Duration',width:90, sortable: false, align: 'center'}, 
		{display: 'Last Update', name: 'LastUpdate',width:90, sortable: false, align: 'center'}, 
		{display: 'Number', name: 'Number',width:90, sortable: false, align: 'center'}, 
		{display: 'Status', name: 'Status',width:90, sortable: false, align: 'center'}, 			   
        {display: 'Action',width:311, name: '', align: 'center', formatter:'showlink', formatoptions:{baseLinkUrl:'', }, }
		],
    buttons : [
		{name: 'Create Booth', bclass: 'add', onpress : add_button},
		{separator: true},
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
	height: 300,
	width: "auto",	
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
    }
});
        
function format() 
{	
    var gridContainer = this.Grid.closest('.flexigrid');
    //alert(gridContainer);
    var headers = gridContainer.find('div.hDiv table tr:first th:not(:hidden)');
    var drags = gridContainer.find('div.cDrag div');
    var offset = 0;
    var firstDataRow = this.Grid.find('tr:first td:not(:hidden)');
    var columnWidths = new Array( firstDataRow.length );
    this.Grid.find( 'tr' ).each( function() {
    	
        $(this).find('td:not(:hidden)').each( function(i) {
            var colWidth = $(this).outerWidth();
            if (!columnWidths[i] || columnWidths[i] < colWidth) {
                columnWidths[i] = colWidth;
            }
        });
    });
    for (var i = 0; i < columnWidths.length; ++i) {
        var bodyWidth = columnWidths[i];
		alert(bodyWidth);
        var header = headers.eq(i);
        var headerWidth = header.outerWidth();

        var realWidth = bodyWidth > headerWidth ? bodyWidth : headerWidth;

        firstDataRow.eq(i).css('width',realWidth);
        header.css('width',realWidth);            
        drags.eq(i).css('left',  offset + realWidth );
        offset += realWidth;
    }
}

$("#id_filter").click(function(){
	var callshop_name = $("#id_callshop").val();
	//var company = $("#id_company").val();
	//var fname = $("#id_fname").val();
	//var lname = $("#id_lname").val();
	//var flex_url = "<?php echo base_url();?>accounts/account_list/grid/?"+encodeURIComponent("filter_ok=1&account="+account+"&company="+company+"&fname="+fname+"&lname="+lname);
	flex_url = "<?php echo base_url();?>callshops/listAll/grid/"+callshop_name;
	$('#flex1').flexOptions({url: flex_url}).flexReload();
});
$("#id_reset").click(function(){
	$("#id_callshop").val('');
	//$("#id_company").val('');
	//$("#id_fname").val('');
	//$("#id_lname").val('');
});

});

function add_button()
{
	jQuery.facebox({ ajax: '<?php echo base_url();?>callshops/add_booth/' });
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

<style>
    fieldset{
        text-align: center;
        
    }
</style>
	<? endblock() ?>

    <? startblock('page-title') ?>
        <?=$page_title?><br/>
    <? endblock() ?>
    
	<? startblock('content') ?>  
     <br>

<div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">                        
    <div class="portlet-header ui-widget-header">List Booths<span class="ui-icon ui-icon-circle-arrow-s"></span></div>
    <div class="portlet-content">          
			<form method="POST" action="del/0/" enctype="multipart/form-data" id="ListForm">
			<table id="flex1" align="left" style="display:none;"></table>
		</form>
	</div>
</div>  
    <? endblock() ?>	
    <? startblock('sidebar') ?>						
		<br/><br/><br/><br/><br/><br/>    	

    <? endblock() ?>
    
<? end_extend() ?>  
