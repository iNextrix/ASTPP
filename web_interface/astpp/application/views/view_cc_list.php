<? extend('master.php') ?>
	<? startblock('extra_head') ?>
<link rel="stylesheet" href="<?=base_url()?>css/flexigrid.css" type="text/css" />
<script type="text/javascript" src="<?=base_url()?>js/flexigrid.js"></script>
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
	
	var showOrHide=false;
	$("#search_bar").toggle(showOrHide);
	
$("#flex1").flexigrid({
    url: "<?php echo base_url();?>callingcards/manage_json/",
    method: 'GET',
    dataType: 'json',
	colModel : [
		{display: 'Account Number', name: 'Number', width: 100, sortable: false, align: 'center'},
        {display: 'Sequence', name: 'country', width: 60, sortable: false, align: 'center'},
        {display: 'Card Number', name: 'province', width: 80, sortable: false, align: 'center'},
        {display: 'Pin', name: 'city', width: 50, sortable: false, align: 'center'},
        {display: 'Brand', name: 'provider', width: 80, sortable: false, align: 'center'},
        {display: 'Balance*', name: 'status', width: 50, sortable: false, align: 'center'},
        {display: 'Used Balance', name: 'calls', width: 50, sortable: false, align: 'center'},
        {display: 'Days Valid For', name: 'live', width: 70, sortable: false, align: 'center'},
        {display: 'Creation', name: 'vm', width: 120, sortable: false, align: 'center'},
        {display: 'First Use', name: 'failed', width: 120, sortable: false, align: 'center'},
        {display: 'Expiration', name: 'na', width: 120, sortable: false, align: 'center'},
        {display: 'In Use?', name: 'minutes', width: 50, sortable: false, align: 'center'},
        {display: 'Status', name: 'xfers', width: 50, sortable: false, align: 'center'},
        {display: 'Action', name: '', width : 80, align: 'center', formatter:'showlink', formatoptions:{baseLinkUrl:'', }, }, 
		],

    buttons : [
		{name: 'Add', bclass: 'add', onpress : add_button},
		{separator: true},		
		{name: 'Update Status', bclass: 'update_status', onpress : update_status_button},		
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
$("#search_cdrs").click(function(){
	$.ajax({type:'POST', url: '<?=base_url()?>callingcards/cards_search', data:$('#search_form4').serialize(), success: function(response) {
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
	jQuery.facebox({ ajax: '<?php echo base_url();?>callingcards/add/' });
}
function clear_filter()
{
	window.location = '<?php echo base_url();?>callingcards/clearsearchfilter/';
}
function update_status_button()
{
	jQuery.facebox({ ajax: '<?php echo base_url();?>callingcards/update_status/' })
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
            <div class="portlet-content"  id="search_bar">
      <script>
	  $(document).ready(function() {
		$("#first_from_date").datetimepicker({ dateFormat: 'yy-mm-dd' });		
		$("#first_to_date").datetimepicker({ dateFormat: 'yy-mm-dd' });	
		$("#creation_from_date").datetimepicker({ dateFormat: 'yy-mm-dd' });		
		$("#creation_to_date").datetimepicker({ dateFormat: 'yy-mm-dd' });			
	  });
	  </script>
            <form action="<?=base_url()?>callingcards/cards_search" id="search_form4" name="form4" method="POST" enctype="multipart/form-data" style="display:block">
             <input type="hidden" name="ajax_search" value="1">
         <input type="hidden" name="advance_search" value="1">
         <ul style=" list-style:none;">
          <fieldset>
            <legend><span style="font-size:14px; font-weight:bold; color:#000;">Search Calling Card</span></legend>
            	<li>
                	 <div class="float-left" style="width:30%">
						<span>
                         <label >Account Number:</label>
                		 <input size="20" class="text field" name="account_nummber" id="account_number">
                		<a onclick="window.open('<?=base_url()?>accounts/search_callingcard_account_list/' , 'AccountList','scrollbars=1,width=650,height=330,top=20,left=100,scrollbars=1');" href="#"><img src="<?=base_url()?>images/icon_arrow_orange.gif" border="0"></a>
                        </span>
                     </div>
                     <div class="float-left" style="width:30%">
						<span>
                          <label>Card Number:</label>
                		  <input size="20" class="text field" name="card_nummber" id="card_number" />&nbsp;
                          <select name="card_number_operator" class="field select">
                          <option value="1">contains</option>
                          <option value="2">doesn't contain</option>
                          <option value="3">is equal to</option>
                          <option value="4">is not equal to</option>
                          </select>              
                        </span>
                     </div>
                     <div class="float-left" style="width:30%">
						<span>
                          <label>Brand:</label>
              			  <?=form_select_default('brand',$brands,"",array("class"=>"select field", "style"=>"width:307px;"),'--Select Brand--')?>
            
                        </span>
                     </div>
                     
                </li>
                
                <li>
                	 <div class="float-left" style="width:30%">
						<span>
                          <label>Balance :</label>
               			  <input size="20" class="text field" name="balance"> &nbsp;
                          <select name="balance_operator"  class="field select " style="width:132px;">
                          <option value="1">is equal to</option>
                          <option value="2">is not equal to</option>
                          <option value="3">greater than</option>
                          <option value="4">less than</option>
                          <option value="5">greather or equal than</option>
                          <option value="6">less or equal than</option>
                          </select>
                        </span>
                     </div>  
                	 <div class="float-left" style="width:30%">
						<span>
                        <label >Balance Used:</label>
               			<input size="20" class="text field" name="balance_used"> &nbsp;
                        <select name="balance_used_operator"  class="field select" style="width:132px;">
                        <option value="1">is equal to</option>
                        <option value="2">is not equal to</option>
                        <option value="3">greater than</option>
                        <option value="4">less than</option>
                        <option value="5">greather or equal than</option>
                        <option value="6">less or equal than</option>
                        </select>
                        </span>
                     </div>   
                	<div class="float-left" style="width:30%">
						<span>
                         <label>In Use :</label>
                          <select name="inuse" class="select field" style="width:307px;" >
                            <option value="1" >Yes</option>
                            <option value="0" >No</option>          
                          </select>
                        </span>
                     </div>
                    
                     
                     
                     
                      
                </li>
                
                <li>
                 <div class="float-left" style="width:30%">
						<span>
                          <label >Creation Start Date:</label>
			              <input size="20" class="text field" name="creation_start_date" id="creation_from_date">&nbsp;<img src="<?=base_url()?>images/calendar.png" border="0"> 
                        </span>
                     </div>
                	  <div class="float-left" style="width:30%">
						<span>
                          <label>Creation End Date:</label>
            			<input size="20" class="text field" name="creation_end_date" id="creation_to_date"> &nbsp;<img src="<?=base_url()?>images/calendar.png" border="0">      
                        </span>
                     </div>
                </li>
                <li>
                  <div class="float-left" style="width:30%">
						<span>
                           <label >Used Start Date :</label>
            			    <input size="20" class="text field" name="first_used_start_date" id="first_from_date">&nbsp;<img src="<?=base_url()?>images/calendar.png" border="0">  
                        </span>
                     </div>  
                	 <div class="float-left" style="width:30%">
						<span>
                          <label>Used End Date:</label>
            			  <input size="20" class="text field" name="first_used_end_date" id="first_to_date">  &nbsp;<img src="<?=base_url()?>images/calendar.png" border="0">   
                        </span>
                     </div>	
                </li>
                
          
              
            </fieldset>
        </ul>
         <br />
        <input type="button" id="id_reset" class="ui-state-default float-right ui-corner-all ui-button" name="reset" value="Clear Search Filter">&nbsp;    
        <input type="button" class="ui-state-default float-right ui-corner-all ui-button" name="action" value="Search" id="search_cdrs" style="margin-right:22px;" />
        <br><br>
        </form>
         
                   
            </div>
        </div>
	<div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">                        
            <div class="portlet-header ui-widget-header">Cards List<span class="ui-icon ui-icon-circle-arrow-s"></span></div>
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

