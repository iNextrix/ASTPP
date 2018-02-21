<? extend('master.php') ?>

	<? startblock('extra_head') ?>

<?php if($page_type == 'list'){?>		
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
    url: "<?php echo base_url();?>callingcards/brands_grid/",
    method: 'GET',
    dataType: 'json',
	colModel : [
<!--        {display: '<input type="checkbox" onclick="toggleChecked(this.checked)">', name: '', width: 20, align: 'center'},\
-->
		{display: 'CC Brand<br/> Name', name: 'Number', width: 80, sortable: false, align: 'center'},
        {display: 'Pin Required', name: 'country', width: 70, sortable: false, align: 'center'},
        {display: 'Pricelist', name: 'province', width: 80, sortable: false, align: 'center'},
        {display: 'Days Valid For', name: 'city', width: 80, sortable: false, align: 'center'},
        {display: 'Maintenance<br/>Fee', name: 'provider', width: 80, sortable: false, align: 'center'},
        {display: 'Days between<br/>Maint fee', name: 'status', width: 80, sortable: false, align: 'center'},
        {display: 'Disconnect<br/>Fee', name: 'calls', width: 80, sortable: false, align: 'center'},
        {display: 'Charge after<br/>X minutes', name: 'vm', width: 80, sortable: false, align: 'center'},
        {display: 'Minutes used<br/>before charge', name: 'failed', width: 80, sortable: false, align: 'left'},
        {display: 'Minimum length thats <br/>not charged extra (minutes)', name: 'na', width: 160, sortable: false, align: 'center'},
        {display: 'Charge for<br/>short calls', name: 'minutes', width: 120, sortable: false, align: 'center'},
        {display: 'Status', name: 'xfers', width: 50, sortable: false, align: 'center'},
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
	rp: 10,
	showTableToggleBtn: false,
	width: 1261,
	height: 250,
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
      	})
    },
    onError: function(){
        alert("Request failed");
    },
});

$("#brands_search").click(function(){
	$.ajax({type:'POST', url: '<?=base_url()?>callingcards/brands_search', data:$('#search_form5').serialize(), success: function(response) {
    $('#flex1').flexReload();
}});
	});
	
	$("#id_reset").click(function(){
		$.ajax({url:'<?=base_url()?>callingcards/clearsearchfilter/', success:function(){
		$('#flex1').flexReload(); }
		});
	});
	
	$("#show_search").click(function(){
	$("#search_bar").toggle();
	});

});

function add_button()
{
	jQuery.facebox({ ajax: '<?php echo base_url();?>callingcards/brands_add/' });
}

function clear_filter()
{
	window.location = '<?php echo base_url();?>callingcards/clearsearchfilter/';
}

function delete_button(name)
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
		  <form action="<?=base_url()?>callingcards/brands_search" id="search_form5" name="form5" method="POST" enctype="multipart/form-data" style="display:block">
           <input type="hidden" name="ajax_search" value="1">
          <input type="hidden" name="advance_search" value="1">
         <ul style="list-style:none;">
          <fieldset >
            <legend><span style="font-size:14px; font-weight:bold; color:#000;">Search Calling Card Brand</span></legend>
            	<li>
                	<div class="float-left" style="width:30%">
					<span>
                      <label >CC Brand:</label>
            		  <input size="20" class="text field" name="cc_brand"> &nbsp;
                      <select name="cc_brand_operator" class="field select">
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
              		  <?=form_select_default('pricelist',@$pricelist,"",array("class"=>"select field", "style"=>"width:307px;"), '--Select PriceList--');?>
                    </span>
                    </div>
                    
                    <div class="float-left" style="width:30%">
					<span>
                     <label >Days validate for:</label>
                     <input size="20" class="text field" name="days_validate_for"> &nbsp;
                     <select name="days_validate_for_operator" class="field select">
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
                      <label >Status:</label>
                      <select class="select field" name="status" style="width:307px;">
                        <option value="1" >Active</option>
                        <option value="0" >Inactive</option>
                      </select>
                    </span>
                    </div>
                </li>
          
            </fieldset>
            
         </ul>
          <br />
        <input type="button" id="id_reset" class="ui-state-default float-right ui-corner-all ui-button" name="reset" value="Clear Search Filter">&nbsp;   
        <input type="button" class="ui-state-default float-right ui-corner-all ui-button" name="action" value="Search" id="brands_search" style="margin-right:22px;"  />
        <br><br>
         </form>
      		</div>
       </div>    
         
<?php if($page_type == 'list'){?>	
<div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">                        
    <div class="portlet-header ui-widget-header">Calling Cards Brands List<span class="ui-icon ui-icon-circle-arrow-s"></span></div>
    <div class="portlet-content">   
    <table id="flex1" align="left" style="display:none;"></table>
    </div>
</div>
<?php }?>



<?php 
	//echo $form;
?>
    <? endblock() ?>
	
    <? startblock('sidebar') ?>
    Filter by
    <? endblock() ?>
    
<? end_extend() ?>  
