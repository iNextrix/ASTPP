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
    url: "<?php echo base_url();?>switchconfig/acl_grid/",
    method: 'GET',
    dataType: 'json',
	colModel : [
                {display: 'Account', name: 'Account', width: 150, sortable: false, align: 'center'},
                {display: 'IP', name: 'ipadd', width: 150, sortable: false, align: 'center'},
                {display: 'Prefix', name: 'prefix', width: 150, sortable: false, align: 'center'},
                {display: 'Context', name: 'contex', width: 150, sortable: false, align: 'center'},
                {display: 'Created Date', name: 'Create Date', width: 150, sortable: false, align: 'center'},
                {display: 'Action', name: '', width : 80, align: 'center', formatter:'showlink', formatoptions:{baseLinkUrl:'', }, },

		],
    buttons : [
//		{name: 'Delete', bclass: 'delete', onpress : delete_button},
//		{separator: true},
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

//function add_button()
//{
//	jQuery.facebox({ ajax: '<?php //echo base_url();?>switchconfig/fssipdevices/add'});
//}
function delete_button()
{
	confirm_string = '{% trans " you are hiding & stopping a campaign" %}';
    if( confirm("are you sure to delete?") == true)
	return true;
	else 
	return false;
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
            <div class="portlet-header ui-widget-header">Access Control List (ACL)<span class="ui-icon ui-icon-circle-arrow-s"></span></div>
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
