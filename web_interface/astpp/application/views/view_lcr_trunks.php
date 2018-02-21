<? extend('master.php') ?>

	<? startblock('extra_head') ?>
<?php if($page_mode == 'list'){?>		
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
	
		
	var showOrHide=false;
	$("#search_bar").toggle(showOrHide);
	
$("#flex1").flexigrid({
    url: "<?php echo base_url();?>lcr/trunks_grid/",
    method: 'GET',
    dataType: 'json',
	colModel : [
<!--        {display: '<input type="checkbox" onclick="toggleChecked(this.checked)">', name: '', width: 20, align: 'center'},\
-->
		{display: 'Trunk Name', name: 'Number', width: 100, sortable: false, align: 'center'},
        {display: 'Protocol', name: 'country', width: 80, sortable: false, align: 'center'},
        {display: 'Peer Name', name: 'province', width: 100, sortable: false, align: 'center'},
        {display: 'Provider', name: 'country', width: 80, sortable: false, align: 'center'},
        {display: 'Max Channels', name: 'country', width: 100, sortable: false, align: 'center'},
        {display: 'Dialed Number Mods', name: 'country', width: 120, sortable: false, align: 'center'},
        {display: 'Precedence', name: 'country', width: 80, sortable: false, align: 'center'},
        {display: 'Resellers', name: 'country', width: 80, sortable: false, align: 'center'},
        {display: 'Action', name: '', width : 60, align: 'center', formatter:'showlink', formatoptions:{baseLinkUrl:'', }, },

		],
    buttons : [
		{name: 'Add', bclass: 'add', onpress : add_button},
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

$("#lcr_trunk_search").click(function(){
	$.ajax({type:'POST', url: '<?=base_url()?>lcr/trunks_search', data:$('#search_form9').serialize(), success: function(response) {
    $('#flex1').flexReload();
		}});
	});

	$("#id_reset").click(function(){
		$.ajax({url:'<?=base_url()?>lcr/clearsearchfilter_trunks', success:function(){
		$('#flex1').flexReload(); }
		});
	});
	
	$("#show_search").click(function(){
	$("#search_bar").toggle();
	});

});

function add_button()
{
	jQuery.facebox({ ajax: '<?php echo base_url();?>lcr/trunks/add/'}); 
}
function clear_filter()
{
	window.location = '<?php echo base_url();?>lcr/clearsearchfilter_trunks/';
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
<?php }?>		
	<? endblock() ?>

    <? startblock('page-title') ?>
        <?=$page_title?><br/>
    <? endblock() ?>
    
	<? startblock('content') ?>
<br/>

<div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all" id="searchbar">                        
            <div class="portlet-header ui-widget-header"><span id="show_search" style="cursor:pointer">Search</span><span class="ui-icon ui-icon-circle-arrow-s"></span></div>
            <div class="portlet-content"  id="search_bar">
           
	     <form action="<?=base_url()?>lcr/trunks_search" id="search_form9" name="form9" method="POST" enctype="multipart/form-data" style="display:block">
         <input type="hidden" name="ajax_search" value="1">
         <input type="hidden" name="advance_search" value="1">
         <ul style="list-style:none;">
          <fieldset >
            <legend><span style="font-size:14px; font-weight:bold; color:#000;">Search Trunks</span></legend>
            	<li>
                	 <div class="float-left" style="width:30%">
					 <span>
                     <label >Trunk Name :</label>
           			 <input size="20" class="text field" name="trunk_name"> &nbsp;
                     <select name="trunk_name_operator" class="field select">
                     <option value="1">contains</option>
                     <option value="2">doesn't contain</option>
                     <option value="3">is equal to</option>
                     <option value="4">is not equal to</option>
                     </select>
                     </span>
                     </div>
                     
                     <div class="float-left" style="width:30%">
					 <span>
                       <label >Protocol:</label>
                        <select name="tech"  class="select field" style="width:307px;" >
                        <option value="SIP"  >SIP</option>
                        <option value="IAX2" >IAX2</option>
                        <option value="Zap"  >Zap</option>
                        <option value="Local" >Local</option>
                        <option value="OH323" >OH323</option>
                        <option value="OOH323C" >OOH323C</option>
                        </select>
                     </span>
                     </div>
                     
                     <div class="float-left" style="width:30%">
					 <span>
                       <label>Provider:</label>
              		   <input size="20" class="text field" name="provider" id="provider">
                       <a onclick="window.open('<?=base_url()?>accounts/search_trunks_provider_list/' , 'ProviderList','scrollbars=1,width=650,height=330,top=20,left=100,scrollbars=1');" href="#"><img src="<?=base_url()?>images/icon_arrow_orange.gif" border="0"></a>
                     </span>
                     </div>
                     
                     
                </li>
                              
             <li>   
          		  <div class="float-left" style="width:30%">
					 <span>
          			   <label>Reseller:</label>
               		   <input size="20" class="text field" name="reseller" id="reseller">
                       <a onclick="window.open('<?=base_url()?>accounts/search_trunks_reseller_list/' , 'ResellerList','scrollbars=1,width=650,height=330,top=20,left=100,scrollbars=1');" href="#"><img src="<?=base_url()?>images/icon_arrow_orange.gif" border="0"></a>	           
                     </span>
                     </div>
             </li>
             
          
              
          </fieldset>
         </ul>
           <br />
             <input type="button" id="id_reset" class="ui-state-default float-right ui-corner-all ui-button" name="reset" value="Clear Search Filter">&nbsp; 
            <input type="button" class="ui-state-default float-right ui-corner-all ui-button" name="action" value="Search" id="lcr_trunk_search" style="margin-right:22px;" />
            <br><br>  
        </form>             
          </div>
        </div>
<div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">                        
        <div class="portlet-header ui-widget-header">Trunks List<span class="ui-icon ui-icon-circle-arrow-s"></span></div>
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
