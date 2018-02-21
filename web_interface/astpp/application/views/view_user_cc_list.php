<? extend('user_master.php') ?>
	<? startblock('extra_head') ?>
<link rel="stylesheet" href="/css/flexigrid.css" type="text/css" />
<script type="text/javascript" src="/js/flexigrid.js"></script>
<script type="text/javascript" language="javascript">
function get_alert_msg(type)
{
	if(type == "delete")
    confirm_string = 'are you sure to delete?';
	else
	confirm_string = 'are you sure to reset?';
    var answer = confirm(confirm_string);
    return answer // answer is a boolean
}
</script>
<script type="text/javascript">
$(document).ready(function() {
$("#flex1").flexigrid({
    url: "<?php echo base_url();?>user/manage_json/",
    method: 'GET',
    dataType: 'json',
	colModel : [
	     {display: 'Card Number', name: 'CardNumber', width: 80, sortable: false, align: 'center'},

        {display: 'Pin', name: 'Pin', width: 50, sortable: false, align: 'center'},

        {display: 'Brand', name: 'Brand', width: 50, sortable: false, align: 'center'},

        {display: 'Value*', name: 'Value', width: 50, sortable: false, align: 'center'},

        {display: 'Used', name: 'Used', width: 50, sortable: false, align: 'center'},

        {display: 'Days Valid For', name: 'DaysValidFor', width: 70, sortable: false, align: 'center'},

        {display: 'Creation', name: 'Creation', width: 120, sortable: false, align: 'center'},

        {display: 'First Use', name: 'FirstUse', width: 120, sortable: false, align: 'center'},

        {display: 'Expiration', name: 'Expiration', width: 120, sortable: false, align: 'center'},

        {display: 'In Use?', name: 'InUse', width: 50, sortable: false, align: 'center'},

        {display: 'Status', name: 'Status', width: 50, sortable: false, align: 'center'},

       
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

    //preProcess: formatContactResults,

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

            <div class="portlet-header ui-widget-header">Cards List<span class="ui-icon ui-icon-circle-arrow-s"></span></div>

            <div class="portlet-content">

              <form method="POST" action="del/0/" enctype="multipart/form-data" id="ListForm">            

              <table id="flex1" align="left" style="display:none;"></table>

              </form>

            </div>

	</div>


<script type="text/javascript">
$(document).ready(function() {
$("#flex2").flexigrid({
    url: "<?php echo base_url();?>user/viewcard_json/",
    method: 'GET',
    dataType: 'json',
	colModel : [
	     {display: 'Destination', name: 'Destination', width: 120, sortable: false, align: 'center'},

        {display: 'Disposition', name: 'Disposition', width: 120, sortable: false, align: 'center'},

        {display: 'CallerID', name: 'CallerID', width: 120, sortable: false, align: 'center'},

        {display: 'Starting Time', name: 'StartingTime', width: 120, sortable: false, align: 'center'},

        {display: '	Length in Seconds', name: '	LengthinSeconds', width: 120, sortable: false, align: 'center'},

        {display: 'Cost', name: 'Cost', width: 120, sortable: false, align: 'center'},

           
		],

    buttons : [

		{name: 'Refresh', bclass: 'reload', onpress : reload_button2},

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

function reload_button2()

{

    $('#flex2').flexReload();

}
</script>
<div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">                        

            <div class="portlet-header ui-widget-header">View Card<span class="ui-icon ui-icon-circle-arrow-s"></span></div>

            <div class="portlet-content">

              <form method="POST" action="del/0/" enctype="multipart/form-data" id="ListForm">            

              <table id="flex2" align="left" style="display:none;"></table>

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

