<? extend('master.php') ?>

	<? startblock('extra_head') ?>
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
    url: "<?php echo base_url();?>did/manage_json/",
    method: 'GET',
    dataType: 'json',
	colModel : [
		{display: 'Number', name: 'Number', width: 70, sortable: false, align: 'center'},
        {display: 'Country', name: 'country', width: 70, sortable: false, align: 'center'},
//         {display: 'Province', name: 'province', width: 80, sortable: false, align: 'center'},
//         {display: 'City', name: 'city', width: 80, sortable: false, align: 'center'},
        {display: 'Provider', name: 'provider', width: 80, sortable: false, align: 'center'},
        {display: 'Account', name: 'status', width: 50, sortable: false, align: 'center'},
        {display: 'Limit<br/>Length', name: 'calls', width: 40, sortable: false, align: 'center'},
        {display: 'Dialstring', name: 'live', width: 90, sortable: false, align: 'center'},
        {display: 'Setup<br/>Fee', name: 'vm', width: 40, sortable: false, align: 'center'},
        {display: 'Disconnection<br/>Fee', name: 'failed', width: 80, sortable: false, align: 'center'},
        {display: 'Monthly', name: 'na', width: 45, sortable: false, align: 'center'},
        {display: 'Connect', name: 'minutes', width: 45, sortable: false, align: 'center'},
        {display: 'Included', name: 'xfers', width: 50, sortable: false, align: 'center'},
        {display: 'Cost', name: 'agents', width: 40, sortable: false, align: 'center'},
        {display: 'Increments', name: 'rate', width: 70, sortable: false, align: 'center'},
        {display: 'Prorate', name: 'prorate', width: 40, sortable: false, align: 'center'},
//         {display: 'Variables', name: 'rate', width: 50, sortable: false, align: 'center'},
        {display: 'Bill on Allocation', name: 'rate', width: 90, sortable: false, align: 'center'},
        {display: 'Max Channels', name: 'rate', width: 80, sortable: false, align: 'center'},
       {display: 'Action', name: '', width : 45, align: 'center', formatter:'showlink', formatoptions:{baseLinkUrl:'', }, },

		],
    buttons : [
		{name: 'Add', bclass: 'add', onpress : add_button},
		{separator: true},
		{name: 'Import', bclass: 'import', onpress : import_button},
		{separator: true},
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

$("#did_search").click(function(){
	$.ajax({type:'POST', url: '<?=base_url()?>did/did_search', data:$('#search_form7').serialize(), success: function(response) {
    $('#flex1').flexReload();
		}});
	});
	
	$("#id_reset").click(function(){
		$.ajax({url:'<?=base_url()?>did/clearsearchfilter_did', success:function(){
		$('#flex1').flexReload(); }
		});
	});
	
	$("#show_search").click(function(){
	$("#search_bar").toggle();
	});
	
});

function add_button()
{
    jQuery.facebox({ ajax: '<?php echo base_url();?>did/manage/add/'});
}
function clear_filter()
{
	window.location = '<?php echo base_url();?>did/clearsearchfilter_did/';
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
		 <div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all" id="searchbar">                        
            <div class="portlet-header ui-widget-header"><span id="show_search" style="cursor:pointer">Search</span><span class="ui-icon ui-icon-circle-arrow-s"></span></div>
            <div class="portlet-content"  id="search_bar">
           
	         <form action="<?=base_url()?>did/did_search" id="search_form7" name="form7" method="POST" enctype="multipart/form-data" style="display:block">
             <input type="hidden" name="ajax_search" value="1">
         	 <input type="hidden" name="advance_search" value="1">
             <ul style=" list-style:none;">
             <fieldset>
             <legend><span style="font-size:14px; font-weight:bold; color:#000;">Search DID</span></legend>
             	<li>
                	<div class="float-left" style="width:30%">
					<span>
                    <label >Number:</label>
             		<input size="20" class="text field" name="number"> &nbsp;
                    <select name="number_operator"  class="field select">
                    <option value="1">contains</option>
                    <option value="2">doesn't contain</option>
                    <option value="3">is equal to</option>
                    <option value="4">is not equal to</option>
                    </select>
                    </span>
                    </div>
                    
                    <div class="float-left" style="width:30%">
					<span>
                      <label >Country:</label>
              		  <?=form_countries('country',"",array("class"=>"select field", "style"=>"width:307px;"), '--Select Country--')?>
                    </span>
                    </div>
                    
                    <div class="float-left" style="width:30%">
					<span>
                      <label>Provider:</label>
               		  <input size="20" class="text field" name="reseller" id="reseller">
               		  <a onclick="window.open('<?=base_url()?>accounts/search_did_provider_list/' , 'ProviderList','scrollbars=1,width=650,height=330,top=20,left=100,scrollbars=1');" href="#"><img src="<?=base_url()?>images/icon_arrow_orange.gif" border="0"></a>
                    </span>
                    </div>
                    
                </li>
                
                <li>
                	 <div class="float-left" style="width:30%">
					<span>
                     <label >Account Number:</label>
                	<input size="20" class="text field" name="account_nummber" id="account_number">
                <a onclick="window.open('<?=base_url()?>accounts/search_did_account_list/' , 'AccountList','scrollbars=1,width=650,height=330,top=20,left=100,scrollbars=1');" href="#"><img src="<?=base_url()?>images/icon_arrow_orange.gif" border="0"></a>
                    </span>
                    </div>
                </li>           
              
          </fieldset>
        </ul>
          <br />
           <input type="button" id="id_reset" class="ui-state-default float-right ui-corner-all ui-button" name="reset" value="Clear Search Filter">&nbsp; 
        <input type="button" class="ui-state-default float-right ui-corner-all ui-button" name="action" value="Search" id="did_search" style="margin-right:22px;" />
        <br><br>
        </form>             
          </div>
        </div> 
        
		<div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">                        
            <div class="portlet-header ui-widget-header">Manage DIDs<span class="ui-icon ui-icon-circle-arrow-s"></span></div>
            <div class="portlet-content">
            <form method="POST" action="del/0/" enctype="multipart/form-data" id="ListForm">        
            <table id="flex1" align="left" style="display:none;"></table>
            </form>
            </div>
		</div>
    <? endblock() ?>
	
    <? startblock('sidebar') ?>
    Filter by
    <? endblock() ?>
    
<? end_extend() ?>  
