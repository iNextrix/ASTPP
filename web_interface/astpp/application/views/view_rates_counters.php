<? extend('master.php') ?>

	<? startblock('extra_head') ?>
		
<!--flexigrid css & js-->
<link rel="stylesheet" href="<?=base_url()?>css/flexigrid.css" type="text/css" />
<script type="text/javascript" src="<?=base_url()?>js/flexigrid.js"></script>

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
    url: "<?php echo base_url();?>rates/counters_grid/",
    method: 'GET',
    dataType: 'json',
	colModel : [
		{display: 'ID', name: 'Number', width: 80, sortable: false, align: 'center'},
        {display: 'Package Name', name: 'country', width: 100, sortable: false, align: 'center'},
        {display: 'Account Name', name: 'province', width: 100, sortable: false, align: 'center'},
        {display: 'Seconds Used', name: 'city', width: 100, sortable: false, align: 'center'},
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

$("#rates_counters_search").click(function(){
	$.ajax({type:'POST', url: '<?=base_url()?>rates/counters_search', data:$('#search_form15').serialize(), success: function(response) {
    $('#flex1').flexReload();
		}});
	});
	
	$("#id_reset").click(function(){
		$.ajax({url:'<?=base_url()?>rates/clearsearchfilter_counters/', success:function(){
		$('#flex1').flexReload(); }
		});
	});
	
	$("#show_search").click(function(){
	$("#search_bar").toggle();
	});

});

function add_button()
{
    window.location = '<?php echo base_url();?>rates/counters_add/';
}

function clear_filter()
{
	window.location = '<?php echo base_url();?>rates/clearsearchfilter_counters/';
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
	<div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all" id="searchbar">                        
            <div class="portlet-header ui-widget-header"><span id="show_search" style="cursor:pointer">Search</span><span class="ui-icon ui-icon-circle-arrow-s"></span></div>
            <div class="portlet-content"  id="search_bar">
           
	         <form action="<?=base_url()?>rates/counters_search" id="search_form15" name="form15" method="POST" enctype="multipart/form-data" style="display:block">
                <input type="hidden" name="ajax_search" value="1">
         <input type="hidden" name="advance_search" value="1">
         <ul style="list-style:none;">
          <fieldset >
            <legend><span style="font-size:14px; font-weight:bold; color:#000;">Search Counters</span></legend>
            	<li>
                <div class="float-left" style="width:30%">
                 <span>
                   <label>Package Name:</label> 
                    <select name="packages"  class="select field" style="width:307px;">
                    <?php 
                    foreach($packages as $key => $value){
                    ?>
                    <option value="<?=$value?>"><?=$value?></option>
                    <?				
                    }
                    ?>
                    </select>   
                 </span>
                 </div>
                 
                  <div class="float-left" style="width:30%">
                 <span>
                   <label >Account Number:</label>
                <input size="20" class="text field" name="account_nummber" id="account_number">
                <a onclick="window.open('<?=base_url()?>accounts/search_counters_account_list/' , 'AccountList','scrollbars=1,width=650,height=330,top=20,left=100,scrollbars=1');" href="#"><img src="<?=base_url()?>images/icon_arrow_orange.gif" border="0"></a>
                 </span>
                 </div>
                 
                  <div class="float-left" style="width:30%">
                 <span>
                   <label >Seconds Used :</label>
            	   <input size="20" class="text field" name="seconds_used"> &nbsp;
                   <select name="seconds_used_operator" class="select field" style="width:132px;">
                   <option value="1">is equal to</option>
                   <option value="2">is not equal to</option>
                   <option value="3">greater than</option>
                   <option value="4">less than</option>
                   <option value="5">greather or equal than</option>
                   <option value="6">less or equal than</option>
                   </select>
                 </span>
                 </div>
                 
                </li>
             
          </fieldset>
        </ul>
          <br />
        <input type="button" id="id_reset" class="ui-state-default float-right ui-corner-all ui-button" name="reset" value="Clear Search Filter">&nbsp;   
        <input type="button" class="ui-state-default float-right ui-corner-all ui-button" name="action" value="Search" id="rates_counters_search" style="margin-right:22px;" />
        <br><br>
        </form>             
          </div>
        </div>
        
<div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">                        
            <div class="portlet-header ui-widget-header">Counters<span class="ui-icon ui-icon-circle-arrow-s"></span></div>
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
