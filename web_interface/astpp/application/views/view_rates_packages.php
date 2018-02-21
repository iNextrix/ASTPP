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
    url: "<?php echo base_url();?>rates/packages_grid/",
    method: 'GET',
    dataType: 'json',
	colModel : [
		{display: 'Package Name', name: 'Number', width: 80, sortable: false, align: 'center'},
        {display: 'Pricelist', name: 'country', width: 100, sortable: false, align: 'center'},
        {display: 'Pattern', name: 'province', width: 100, sortable: false, align: 'center'},
        {display: 'Included Seconds', name: 'city', width: 100, sortable: false, align: 'center'},
        {display: 'Action', name: '', width : 50, align: 'center', formatter:'showlink', formatoptions:{baseLinkUrl:'', }, },

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

$("#rates_packages_search").click(function(){
	$.ajax({type:'POST', url: '<?=base_url()?>rates/packages_search', data:$('#search_form14').serialize(), success: function(response) {
    $('#flex1').flexReload();
		}});
	});
	
	$("#id_reset").click(function(){
		$.ajax({url:'<?=base_url()?>rates/clearsearchfilter_packages/', success:function(){
		$('#flex1').flexReload(); }
		});
	});
	
	
$("#show_search").click(function(){
	$("#search_bar").toggle();
	});

});

function add_button()
{
    jQuery.facebox({ ajax: '<?php echo base_url();?>rates/packages/add'});
}
function clear_filter()
{
	window.location = '<?php echo base_url();?>rates/clearsearchfilter_packages/';
}
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
<div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all" id="searchbar">                        
            <div class="portlet-header ui-widget-header"><span id="show_search" style="cursor:pointer">Search</span><span class="ui-icon ui-icon-circle-arrow-s"></span></div>
            <div class="portlet-content" id="search_bar">
           
	         <form action="<?=base_url()?>rates/packages_search" id="search_form14" name="form14" method="POST" enctype="multipart/form-data" style="display:block">
                <input type="hidden" name="ajax_search" value="1">
         <input type="hidden" name="advance_search" value="1">
         <ul style=" list-style:none;">
          <fieldset >
            <legend><span style="font-size:14px; font-weight:bold; color:#000;">Packages</span></legend>
            	<li>
                 <div class="float-left" style="width:30%">
                 <span>
                   <label >Package Name:</label>
            	   <input size="20" class="text field" name="package_name"> &nbsp;
                   <select name="package_name_operator" class="field select">
                   <option value="1">contains</option>
                   <option value="2">doesn't contain</option>
                   <option value="3">is equal to</option>
                   <option value="4">is not equal to</option>
                   </select>	
                 </span>
                 </div>
                  <div class="float-left" style="width:30%">
                 <span>
                  <label >Pricelist :</label>
                 <?=form_select_default('pricelist',@$pricelist,"",array("class"=>"select field", "style"=>"width:307px;"), '--Select PriceList--')?>
                 </span>
                 </div>
                 
                  <div class="float-left" style="width:30%">
                 <span>
                   <label >Pattern:</label>
                   <input size="20" class="text field" name="pattern"> &nbsp;
                   <select name="pattern_operator" class="field select">
                   <option value="1">contains</option>
                   <option value="2">doesn't contain</option>
                   <option value="3">is equal to</option>
                   <option value="4">is not equal to</option>
                   </select>
                 </span>
                 </div>
                 
                </li>
                
                <li>
                	  <div class="float-left" style="width:30%">
                 		<span>
                          <label>Included Seconds:</label>
           			      <input size="20" class="text field" name="included_seconds"> &nbsp;
                          <select name="included_seconds_operator" class="field select" style="width:132px;">
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
        <input type="button" class="ui-state-default float-right ui-corner-all ui-button" name="action" value="Search" id="rates_packages_search" style="margin-right:22px;" />
        <br><br>
         </form>
                      
          </div>
        </div>
<div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">                        
            <div class="portlet-header ui-widget-header">Packages<span class="ui-icon ui-icon-circle-arrow-s"></span></div>
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
