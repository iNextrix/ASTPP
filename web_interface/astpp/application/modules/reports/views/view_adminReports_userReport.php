<? extend('master.php') ?>
<? startblock('extra_head') ?>

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
    url: "<?php echo base_url();?>reports/userReport_grid/",
    method: 'GET',
    dataType: 'json',
	colModel : [
        {display: 'User', name: 'Number', width:170,  sortable: false, align: 'center'},
        {display: 'Code', name: 'country',width:100, sortable: false, align: 'center'},
        {display: 'Destination', name: 'province',width:170, sortable: false, align: 'center'},
        {display: 'Attempted Calls', name: 'province',width:100, sortable: false, align: 'center'},
        {display: 'Complete Calls', name: 'completecall',width:80, sortable: false, align: 'center'},
        {display: 'ASR', name: 'province',width:50, sortable: false, align: 'center'},
        {display: 'ACD', name: 'province',width:50, sortable: false, align: 'center'},
        {display: 'MCD', name: 'city', width:50, sortable: false, align: 'center'},
//        {display: 'Actual',width:80, name: 'provider',  sortable: false, align: 'center'},
        {display: 'Billable', width:80,name: 'status',  sortable: false, align: 'center'},
        {display: 'Price',width:80, name: 'calls',  sortable: false, align: 'center'},
        {display: 'Cost', width:80,name: 'province',  sortable: false, align: 'center'},
        {display: 'Profit', width:80,name: 'profit',  sortable: false, align: 'center'},
		],
		 buttons : [
		{name: 'Remove Search Filter', bclass: 'reload', onpress : clear_filter},
		],
	nowrap: false,
	showToggleBtn: false,
    sortname: "id",
	sortorder: "asc",
	usepager: true,
	resizable: true,
	title: '',
	useRp: true,
	rp: 10,
	showTableToggleBtn: true,
	height: "auto",	
	width: "auto",	
    pagetext: 'Page',
    outof: 'of',
    nomsg: 'No items',
    procmsg: 'Processing, please wait ...',
    pagestat: 'Displaying {from} to {to} of {total} items',
    onSuccess: function(data){
        //alert(data);
        //format();
    },
    onError: function(){
        alert("Request failed");
    }
});
    $("#id_reset").click(function(){
        window.location="<?=base_url()?>reports/userReport_clear_search_sum_Report/";
    });
    $("#show_search").click(function(){
        $("#search_bar").toggle();
    });

});

function clear_filter()
{
	window.location = '<?php echo base_url();?>adminReports/clearsearchfilter_user/';
}
function add_button()
{
    window.location = '<?php echo base_url();?>callingcards/add/';
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
     <script>
	  $(document).ready(function() {
		$("#start_date").datepicker({ dateFormat: 'yy-mm-dd' });
		
		$("#end_date").datepicker({ dateFormat: 'yy-mm-dd' });
	  });
	  </script> 
        <div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">                        
            <div class="portlet-header ui-widget-header"><span id="show_search" style="cursor:pointer">Search</span><span class="ui-icon ui-icon-circle-arrow-s"></span></div>
            <div class="portlet-content" id="search_bar">
            <script>
		  $(document).ready(function() {
			$("#user_from_date").datetimepicker({ dateFormat: 'yy-mm-dd' });		
			$("#user_to_date").datetimepicker({ dateFormat: 'yy-mm-dd' });			
		  });
		  </script> 
         <form action="<?=base_url()?>reports/userReport" id="search_form22" name="form22" method="POST" enctype="multipart/form-data" style="display:block">
         <ul style=" list-style:none;">
          <fieldset  >
            <legend><span style="font-size:14px; font-weight:bold; color:#000;">Search User Report</span></legend>
            <li>
                <div class="float-left" style="width:30%">
                <span>
                    <label>Start Date & Time  :</label>
                    <input size="20" class="text field" name="start_date" id="user_from_date"> &nbsp;<img src="<?=base_url()?>images/calendar.png" border="0">
                </span>
                </div>
                <div class="float-left" style="width:30%">
                <span>
                <label>End Date & Time:</label>
                <input size="20" class="text field" name="end_date" id="user_to_date">  &nbsp;<img src="<?=base_url()?>images/calendar.png" border="0">          
                </span>
                </div>
                <div class="float-left" style="width:30%">
                    <span><label>Account:</label>
                        <?=$account;?>
                </span>
                </div>                
            </li>
            
          
            </fieldset>
          </ul>
           <br />
        <input type="button" id="id_reset" class="ui-state-default float-right ui-corner-all ui-button" name="reset" value="Clear Search Filter">&nbsp;  
        <input type="submit" class="ui-state-default float-right ui-corner-all ui-button" name="action" value="Search" id="search_userreport" style="margin-right:22px;" />
        <br><br>
         </form>
            </div>
        </div>        


<div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">                        
    <div class="portlet-header ui-widget-header">Customer Summary Report<span class="ui-icon ui-icon-circle-arrow-s"></span></div>
    <div class="portlet-content">         
		<form method="POST" action="del/0/" enctype="multipart/form-data" id="ListForm">
			<table id="flex1" align="left" style="display:none;"></table>
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