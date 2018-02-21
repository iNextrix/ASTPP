<?php include('header_advance_search.php');?>
<!--flexigrid css & js-->
<link rel="stylesheet" href="<?=base_url()?>css/flexigrid.css" type="text/css" />
<script type="text/javascript" src="<?=base_url()?>js/flexigrid.js"></script>

<script type="text/javascript">
$(document).ready(function() {
	
$("#flex1").flexigrid({
    url: "<?php echo base_url();?>accounts/search_trunks_reseller_list/grid/",
    method: 'GET',
    dataType: 'json',
	colModel : [
		{display: 'Card Number', name: 'Number', width:80,  sortable: false, align: 'center'},
        {display: 'Account<br/>Number', name: 'accountnumber',width:80, sortable: false, align: 'center'},
     	{display: 'First Name', name: 'firstname',width:90, sortable: false, align: 'center'},
		{display: 'Last Name', name: 'lastname',width:90, sortable: false, align: 'center'},
		{display: 'Company', name: 'company',width:100, sortable: false, align: 'center'},
		{display: 'Country', name: 'country',width:100, sortable: false, align: 'center'},
        {display: 'Action',width:110, name: '', align: 'center', formatter:'showlink', formatoptions:{baseLinkUrl:'', }, }, 
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
	title: '',
	useRp: true,
	rp: 10,
	showTableToggleBtn: true,
	height: 300,
	width: "auto",	
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
    }
});
        
function format() {
	
    var gridContainer = this.Grid.closest('.flexigrid');
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
	
	var account = ($("#id_account").val()=='' ? 'NULL' : $("#id_account").val());
	var company = ($("#id_company").val()=='' ? 'NULL' : $("#id_company").val());
	var fname =   ($("#id_fname").val() == '' ? 'NULL' : $("#id_fname").val());
	var lname =   ($("#id_lname").val() =='' ? 'NULL': $("#id_lname").val()) ;
	
	//var flex_url = "<?php echo base_url();?>accounts/account_list/grid/?"+encodeURIComponent("filter_ok=1&account="+account+"&company="+company+"&fname="+fname+"&lname="+lname);
	flex_url = "<?php echo base_url();?>accounts/search_trunks_reseller_list/grid/"+account+"/"+company+"/"+fname+"/"+lname;
	//alert(flex_url);
	$('#flex1').flexOptions({url: flex_url}).flexReload();
});
$("#id_reset").click(function(){
	$("#id_account").val('');
	$("#id_company").val('');
	$("#id_fname").val('');
	$("#id_lname").val('');
});

});

function reload_button()
{
    $('#flex1').flexReload();
}

</script>	
<SCRIPT LANGUAGE="javascript">
<!-- Begin
function sendValue(selvalue, othervalue){
	window.opener.document.form9.reseller.value = selvalue;
	if(othervalue && window.opener.document.form9.accountcode){
			window.opener.document.form9.accountcode.value = othervalue;
	}
	window.close();
}

// End -->

</SCRIPT>

<style>
    fieldset{
        text-align: center;
        
    }
</style>	
	      
        <div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all" id="searchbar">                        
            <div class="portlet-header ui-widget-header">Search<span class="ui-icon ui-icon-circle-arrow-s"></span></div>
            <div class="portlet-content">
              <fieldset >
                      <div>
                      <ul id="search-filters">
                      <li>
                      
                      <label class="Searchdesc">Account Number:</label><input class="field text" type="text" size="10" id="id_account">
                      </li>
                      <li>
                      <label class="Searchdesc">Company:</label><input class="field text" type="text" size="10" id="id_company">
                      </li>
                      <li>
                      <label class="Searchdesc">First Name:</label><input class="field text" type="text" size="10" id="id_fname">
                      </li>
                      <li>
                      <label class="Searchdesc">Last Name:</label><input class="field text" type="text" size="10" id="id_lname">
                      </li>
                      
                      <li style="width:60px; margin-top:17px">
                      <input type="button" id="id_reset" class="ui-state-default ui-corner-all ui-button" name="reset" value="Clear">
		      </li>
		      <li style="width:60px; margin-top:17px; margin-left: 22px;">
                      <input type="button" id="id_filter" value="Search" class="ui-state-default ui-corner-all ui-button" />&nbsp;
                      </li>
                      </ul>
                      <br/>
                  
                      </div>
              </fieldset>           
            </div>
        </div>        


<div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">                        
    <div class="portlet-header ui-widget-header">Reseller List<span class="ui-icon ui-icon-circle-arrow-s"></span></div>
    <div class="portlet-content">         
		<form method="POST" action="del/0/" enctype="multipart/form-data" id="ListForm">
			<table id="flex1" align="left" style="display:none;"></table>
		</form>
	</div>
</div>  