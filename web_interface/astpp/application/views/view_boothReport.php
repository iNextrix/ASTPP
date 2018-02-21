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
    url: "<?php echo base_url();?>callshops/boothReport/grid/",
    method: 'GET',
    dataType: 'json',
	colModel : [
		{display: 'Booth', name: 'Number', width:80,  sortable: false, align: 'center'},
        {display: 'Destination', name: 'country',width:100, sortable: false, align: 'center'},
        {display: 'IDD Code', name: 'province',width:120, sortable: false, align: 'center'},
		{display: 'Attempted Calls', name: 'province',width:120, sortable: false, align: 'center'},
		{display: 'ASR', name: 'province',width:50, sortable: false, align: 'center'},
		{display: 'ACD', name: 'province',width:50, sortable: false, align: 'center'},
        {display: 'MCD', name: 'city', width:50, sortable: false, align: 'center'},
        {display: 'Actual',width:80, name: 'provider',  sortable: false, align: 'center'},
        {display: 'Billable', width:80,name: 'status',  sortable: false, align: 'center'},
        {display: 'Price',width:50, name: 'calls',  sortable: false, align: 'center'},
        {display: 'Cost', width:50,name: 'province',  sortable: false, align: 'center'},

		],
		 buttons : [
		{name: 'Remove Search Filter', bclass: 'reload', onpress : clear_filter},
		],
	nowrap: false,
	showToggleBtn: false,
    sortname: "id",
	sortorder: "asc",
	usepager: true,
	resizable: false,
	title: '',
	useRp: true,
	rp: 20,
	showTableToggleBtn: true,
	height: 300,
	width: 975,	
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
        
function format() {
	
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

	var start_date = ($("#start_date").val()=='' ? 'NULL' : $("#start_date").val());
	var end_date = ($("#end_date").val()=='' ? 'NULL' : $("#end_date").val());
	var Reseller = $("#Booth").val();
	var destination = $("#destination").val();
	var pattern = $("#pattern").val();
	
	//var flex_url = "<?php echo base_url();?>adminReports/resellerReport/grid/?"+encodeURIComponent("filter_ok=1&start_date="+start_date+"&end_date="+end_date+"&Reseller="+Reseller+"&destination="+destination+"&pattern="+pattern);
	flex_url = "<?php echo base_url();?>callshops/boothReport/grid/"+start_date+"/"+end_date+"/"+Reseller+"/"+destination+"/"+pattern+"/";
	
	$('#flex1').flexOptions({url: flex_url}).flexReload();
	
});


$("#search_callshopboothreport").click(function(){	

	$.ajax({type:'POST', url: '<?=base_url()?>callshops/booth_search', data:$('#search_form23').serialize(), success: function(response) {
    $('#flex1').flexReload();
}});
	});
	
	$("#id_reset").click(function(){
		$.ajax({url:'<?=base_url()?>callshops/clearsearchfilter_booth/', success:function(){
		$('#flex1').flexReload(); }
		});
	});


});

function clear_filter()
{
	window.location = '<?php echo base_url();?>callshops/clearsearchfilter_booth/';
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
            <div class="portlet-header ui-widget-header">Search<span class="ui-icon ui-icon-circle-arrow-s"></span></div>
            <div class="portlet-content">
               <script>
	  $(document).ready(function() {
		$("#booth_from_date").datetimepicker({ dateFormat: 'yy-mm-dd' });		
		$("#booth_to_date").datetimepicker({ dateFormat: 'yy-mm-dd' });			
	  });
	  </script> 
         <form action="<?=base_url()?>callshops/booth_search" id="search_form23" name="form23" method="POST" enctype="multipart/form-data" style="display:block">
         <input type="hidden" name="advance_search" value="1">
         <ul style="ist-style:none;">
          <fieldset >
            <legend><span style="font-size:14px; font-weight:bold; color:#000;">Search Booth Report</span></legend>
            	<li>
                	<div class="float-left" style="width:30%">
                	<span>
                     <label >Booth:</label>
                <input size="20" class="text field" name="reseller" id="reseller">
                <a onclick="window.open('<?=base_url()?>callshops/callshop_booth_list/' , 'BoothList','scrollbars=1,width=650,height=330,top=20,left=100,scrollbars=1');" href="#"><img src="<?=base_url()?>images/icon_arrow_orange.gif" border="0"></a>
                    </span>
                    </div>
                    
                    <div class="float-left" style="width:30%">
                	<span>
                     <label>Destination:</label>
                      <select class="select field" name="Destination" id="Destination" style="width:307px;">
                        <option value="ALL">ALL</option>
                        <?php foreach($destination as $key => $value)
                            {
                                if($value!="")
                                {
                            
                                ?>
                                <option value="<?php echo $value?>" <?php if(@$Destination==$value) { echo "selected";}?> ><?php echo $value?></option>
                                <?
                                }
                            }?>
                      </select>
                    </span>
                    </div>
                    
                    
                    <div class="float-left" style="width:30%">
                	<span>
                      <label >IDD Code:</label>
                      <select class="select field" name="Pattern" value="IDD Code" id="Pattern" style="width:307px;">
                      <option value="ALL">ALL</option>
                      <?php foreach($pattern as $key => $value)
                            {
                                if($value!="")
                                {
                                ?>
                                <option value="<?php echo $value?>" <?php if(@$Pattern==$value) { echo "selected";}?> ><?php echo $value?></option>
                                <?
                                }
                            }
                     ?>
                      </select>
                    </span>
                    </div>
                    
                </li>
                
                <li>
                	<div class="float-left" style="width:30%">
                	<span>
                       <label >Start Date & Time  :</label>
           			   <input size="20" class="text field" name="start_date" id="booth_from_date"> &nbsp;<img src="<?=base_url()?>images/calendar.png" border="0"> 
                    </span>
                    </div>
                    
                    <div class="float-left" style="width:30%">
                	<span>
                       <label >End Date & Time:</label>
           			   <input size="20" class="text field" name="end_date" id="booth_to_date">   &nbsp;<img src="<?=base_url()?>images/calendar.png" border="0">       
                    </span>
                    </div>
                    
                </li>
            
           
            </fieldset>
          </ul>
           <br />
         <input type="button" id="id_reset" class="ui-state-default float-right ui-corner-all ui-button" name="reset" value="Clear Search Filter">&nbsp;    
         <input type="button" class="ui-state-default float-right ui-corner-all ui-button" name="action" value="Search" id="search_callshopboothreport" style="margin-right:22px;" />
        <br><br>
         </form>
              <!--<fieldset >
                      <div>
                      <ul id="search-filters2" style="width:1260px">
                      <li>
                      <label class="desc">Start date:</label><input class="text field medium" type="text" name="start_date" value="" size="5" id="start_date">
                      </li>
                      <li>
                      <label class="desc">Start time:</label>
                      <input class="text field small" type="text" name="start_time1" value="" size="5">
                      <input class="text field small" type="text" name="start_time2" value="" size="5">
                      <input class="text field small" type="text" name="start_time3" value="" size="5">
                      </li>
                      <li>
                      <label class="desc">End time:</label>
                      <input class="text field small" type="text" name="end_time1" value="" size="5">
                      <input class="text field small" type="text" name="end_time2" value="" size="5">
                      <input class="text field small" type="text" name="end_time3" value="" size="5">
                      </li>
                      <li>
                      <label class="desc">End date:</label><input class="text field medium" type="text" name="end_date" value="" size="5" id="end_date">
                      </li>
                      <li>
                      <label class="desc">Booth:</label>
                      <select class="select field medium" name="Reseller" value="ALL" id="Booth">
                      <option value="ALL">ALL</option>
                      <?php foreach($reseller as $key => $value) {				
						?>
                      <option value="<?php echo $value?>" <?php if($Reseller==$value) { echo "selected";}?> ><?php echo $value?></option>
                      <? } ?>
                      </select> 
                      </li>
                      <li>
                      <label class="desc">Destination:</label>
                      <select class="select field medium" name="destination" value="Destination" id="destination">
						<option value="ALL">ALL</option>
                        <?php foreach($destination as $key => $value)
							{
								if($value!="")
								{
							
								?>
                            	<option value="<?php echo $value?>" <?php if($Destination==$value) { echo "selected";}?> ><?php echo $value?></option>
                            	<?
								}
							}?>
					  </select>
                      </li>
                      <li>
                      <label class="desc">IDD Code:</label>
                      <select class="select field medium" name="pattern" value="IDD Code" id="pattern">
                      <option value="ALL">ALL</option>
                      <?php foreach($pattern as $key => $value)
					  		{
						  		if($value!="")
								{
								?>
                            	<option value="<?php echo $value?>" <?php if($Pattern==$value) { echo "selected";}?> ><?php echo $value?></option>
                            	<?
								}
							}
					 ?>
                      </select>
                      </li>
                      <li style="width:160px; margin-top:17px">                     
                      <input type="button" id="id_filter" value="Search" class="ui-state-default ui-corner-all ui-button" />&nbsp;
                      </li>
                      </ul>
                      <br/>
                  
                      </div>
              </fieldset>-->           
            </div>
        </div>        


<div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">                        
    <div class="portlet-header ui-widget-header">Booth Report<span class="ui-icon ui-icon-circle-arrow-s"></span></div>
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
