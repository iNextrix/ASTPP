<? extend('user_master.php') ?>

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

<style>
    fieldset{
        width: 609px;
    }
    
</style>		

<script type="text/javascript">
$(document).ready(function() {
$("#flex1").flexigrid({
    url: "<?php echo base_url();?>user/dids_json/",
    method: 'GET',
    dataType: 'json',
	colModel : [
<!--        {display: '<input type="checkbox" onclick="toggleChecked(this.checked)">', name: '', width: 20, align: 'center'},\
-->
		{display: 'Number', name: 'Number', width: 70, sortable: false, align: 'center'},
        {display: 'Connect Fee', name: 'ConnectFee', width: 80, sortable: false, align: 'center'},
        {display: 'Included Seconds', name: 'IncludedSeconds', width: 80, sortable: false, align: 'center'},
        {display: 'Cost', name: 'Cost', width: 80, sortable: false, align: 'center'},
        {display: 'Monthly Fee', name: 'MonthlyFee', width: 80, sortable: false, align: 'center'},
        {display: 'Country', name: 'Country', width: 50, sortable: false, align: 'center'},
        {display: 'Province/State', name: 'ProvinceState', width: 40, sortable: false, align: 'center'},
		 {display: 'City', name: 'City', width: 40, sortable: false, align: 'center'},
        {display: 'Extension to dial', name: 'Extensiontodial', width: 50, sortable: false, align: 'center'},
         {display: 'Action', name: '', width : 50, align: 'center', formatter:'showlink', formatoptions:{baseLinkUrl:'', }, },

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

$("#id_filter").click(function(){
	var did_list = $("#did_list").val();
	//alert(did_list);
	//var company = $("#id_company").val();
	//var fname = $("#id_fname").val();
	//var lname = $("#id_lname").val();
	//var flex_url = "<?php echo base_url();?>accounts/account_list/grid/?"+encodeURIComponent("filter_ok=1&account="+account+"&company="+company+"&fname="+fname+"&lname="+lname);
	flex_url = "<?php echo base_url();?>user/dids_json/"+did_list;
	 window.location.href = flex_url;

	//$('#flex1').flexOptions({url: flex_url});
	//$('#flex1').flexOptions({url: flex_url}).flexReload();
});

});


function reload_button()
{
    $('#flex1').flexReload();
}

function edit_did(id)
{	
   jQuery.facebox({ ajax: '<?php echo base_url();?>user/edit_did/'+id});
}

function edit_did_confirm(did)
{
	 jQuery.facebox({ ajax: '<?php echo base_url();?>user/edit_did/'+did});
}
</script>
	
	<? endblock() ?>

    <? startblock('page-title') ?>
        <?=$page_title?><br/>
    <? endblock() ?>
    
	<? startblock('content') ?>
<div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">                        
            <div class="portlet-header ui-widget-header">Order DID<span class="ui-icon ui-icon-circle-arrow-s"></span></div>
            <div class="portlet-content">
             <div class="sub-form">              
                          <div><label class="desc">Number</label>
                          <select class="select field large" name="did_list" id="did_list"> 
                          <?php
						  foreach($availabledids as $key => $value){
							  foreach($value as $newval){
							  ?>
                              <option value="<?=@$newval?>"><?=@$newval?></option>
                              <?
						  	}
						  }
                          ?>
						  <? //=$available_dids?>
                         
                          </select></div>
                          <div style="margin-top:14px;">
                          <input class="ui-state-default ui-corner-all ui-button" name="action" value="Purchase DID" type="submit" id="id_filter">
                          </div>
                  </div>         
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

<?php 
	//echo $form;
	
?>
    <? endblock() ?>
	
    <? startblock('sidebar') ?>
    Filter by
    <? endblock() ?>
    
<? end_extend() ?>  
