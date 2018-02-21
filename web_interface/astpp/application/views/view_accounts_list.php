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
    url: "<?php echo base_url();?>accounts/account_list/grid/",
    method: 'GET',
    dataType: 'json',
	colModel : [
		{display: 'Card Number', name: 'Number', width:80,  sortable: false, align: 'center'},
        {display: 'Account<br/>Number', name: 'country',width:80, sortable: false, align: 'center'},
        {display: 'Pricelist', name: 'province',width:90, sortable: false, align: 'center'},
		{display: 'First Name', name: 'province',width:90, sortable: false, align: 'center'},
		{display: 'Last Name', name: 'province',width:90, sortable: false, align: 'center'},
		{display: 'Company', name: 'province',width:90, sortable: false, align: 'center'},
        {display: 'Balance', name: 'city', width:70, sortable: false, align: 'right'},
        {display: 'Credit<br/>Limit',width:70, name: 'provider',  sortable: false, align: 'right'},
        {display: '<acronym title="Billing Cycle (How frequently this customer is billed.  Only applies to postpaid accounts.">Cycle</acronym>', width:70,name: 'status',  sortable: false, align: 'center'},
        {display: '<acronym title="Post To External (This would be for postpaid customers who\'s cdrs are to be posted to an external billing application such as oscommerce at the intervals specified in the cycle field.">P.T.E.</acronym>',width:50, name: 'calls',  sortable: false, align: 'center'},
        {display: 'Account Type', width:90,name: 'province',  sortable: false, align: 'center'},
	{display: 'Account Status', width:90,name: 'province',  sortable: false, align: 'center'},
        {display: 'Action',width:120, name: '', align: 'center', formatter:'showlink', formatoptions:{baseLinkUrl:'', }, }, 

		],
    buttons : [
		{name: 'Create Account', bclass: 'add', onpress : add_button},
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
	var accounttype =$("#id_accounttype").val();
	//var flex_url = "<?php echo base_url();?>accounts/account_list/grid/?"+encodeURIComponent("filter_ok=1&account="+account+"&company="+company+"&fname="+fname+"&lname="+lname);
	flex_url = "<?php echo base_url();?>accounts/account_list/grid/"+account+"/"+company+"/"+fname+"/"+lname+"/"+accounttype;
	//alert(flex_url);
	$('#flex1').flexOptions({url: flex_url}).flexReload();
});

$("#account_search").click(function(){
	$.ajax({type:'POST', url: '<?=base_url()?>accounts/search', data:$('#search_form1').serialize(), success: function(response) {
    $('#flex1').flexReload();
}});
	});

$("#id_reset").click(function(){
	$.ajax({url:'<?=base_url()?>accounts/clearsearchfilter', success:function(){
	$('#flex1').flexReload();	
	}
	});
	//$("#id_account").val('');
	//$("#id_company").val('');
	//$("#id_fname").val('');
	//$("#id_lname").val('');
});

$("#show_search").click(function(){
	$("#search_bar").toggle();
	});

});

function add_button()
{
    window.location = '<?php echo base_url();?>accounts/create/';
}

function clear_filter()
{
	window.location = '<?php echo base_url();?>accounts/clearsearchfilter/';
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
        <div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all" id="searchbar">                        
            <div class="portlet-header ui-widget-header" ><span id="show_search" style="cursor:pointer">Search</span><span class="ui-icon ui-icon-circle-arrow-s"></span></div>
            <div class="portlet-content"  id="search_bar">
            <?php
			error_reporting(~E_NOTICE);
			$account_search = $this->session->userdata('account_search');			
			?>
         <form action="<?=base_url()?>accounts/search" id="search_form1" name="search_form" method="POST" enctype="multipart/form-data" style="display:block" class="form">
          <input type="hidden" name="ajax_search" value="1">
         <input type="hidden" name="advance_search" value="1">
         <ul>
          <fieldset  >
            <legend><span style="font-size:14px; font-weight:bold; color:#000;">Search Account</span></legend>
             <li>  
             <div class="float-left" style="width:30%">
				<span>
                  <label>Account Number:</label>
				  <input size="20" class="text field" name="account_number" value="<?=@$account_search['account_number']?>" > &nbsp;                  
                  <select name="account_number_operator" class="field select">
                  <option value="1" <?php if(@$account_search['account_number_operator']==1) { echo "selected";}?> >contains</option>
                  <option value="2" <?php if(@$account_search['account_number_operator']==2) { echo "selected";}?>>doesn't contain</option>
                  <option value="3" <?php if(@$account_search['account_number_operator']==3) { echo "selected";}?>>is equal to</option>
                  <option value="4" <?php if(@$account_search['account_number_operator']==4) { echo "selected";}?>>is not equal to</option>
                  </select>
					</span>
				</div> 
                <div class="float-left" style="width:30%">
				<span>
                 <label>Pricelist :</label>
                 <?=form_select_default('pricelist',@$pricelist,@$account_search['pricelist'],array("class"=>"select field", "style"=>"width:307px;"), '--Select PriceList--')?>
                
                </span>
                </div>  
                <div class="float-left" style="width:30%">
				<span>
               <label>Firstname:</label>
               <input size="20" class="text field" name="first_name" value="<?=@$account_search['first_name']?>" > &nbsp;
               <select name="first_name_operator" class="field select">
               <option value="1" <?php if(@$account_search['first_name_operator']==1) { echo "selected";}?>>contains</option>
               <option value="2" <?php if(@$account_search['first_name_operator']==2) { echo "selected";}?>>doesn't contain</option>
               <option value="3" <?php if(@$account_search['first_name_operator']==3) { echo "selected";}?>>is equal to</option>
               <option value="4" <?php if(@$account_search['first_name_operator']==4) { echo "selected";}?>>is not equal to</option>
               </select>
                </span>
                </div>
               
                 
             </li>
             <li>
             	  <div class="float-left" style="width:30%">
                 <span>
                   <label>Lastname:</label>
                   <input size="20" class="text field" name="last_name" value="<?=@$account_search['last_name']?>"  > &nbsp;
                   <select name="last_name_operator"  class="field select">
                   <option value="1" <?php if(@$account_search['last_name_operator']==1) { echo "selected";}?> >contains</option>
                   <option value="2" <?php if(@$account_search['last_name_operator']==2) { echo "selected";}?>>doesn't contain</option>
                   <option value="3" <?php if(@$account_search['last_name_operator']==3) { echo "selected";}?>>is equal to</option>
                   <option value="4" <?php if(@$account_search['last_name_operator']==4) { echo "selected";}?>>is not equal to</option>
                   </select>
                 </span>
                 </div>
             	 <div class="float-left" style="width:30%">
                 <span>
                   <label>Company:</label>
                   <input size="20" class="text field" name="company" value="<?=@$account_search['company']?>" > &nbsp;
                   <select name="company_operator"  class="field select ">
                   <option value="1" <?php if(@$account_search['company_operator']==1) { echo "selected";}?> >contains</option>
                   <option value="2" <?php if(@$account_search['company_operator']==2) { echo "selected";}?>>doesn't contain</option>
                   <option value="3" <?php if(@$account_search['company_operator']==3) { echo "selected";}?> >is equal to</option>
                   <option value="4" <?php if(@$account_search['company_operator']==4) { echo "selected";}?> >is not equal to</option>
                   </select>
                 </span>
                 </div>
                 
                 <div class="float-left" style="width:30%">
                 <span>
                   <label>Balance:</label>
                   <input size="20" class="text field" name="balance" value="<?=@$account_search['balance']?>" > &nbsp;
                   <select name="balance_operator" class="field select" style="width:132px;" >
                   <option value="1" <?php if(@$account_search['balance_operator']==1) { echo "selected";}?> >is equal to</option>
                   <option value="2" <?php if(@$account_search['balance_operator']==2) { echo "selected";}?>>is not equal to</option>
                   <option value="3" <?php if(@$account_search['balance_operator']==3) { echo "selected";}?>>greater than</option>
                   <option value="4" <?php if(@$account_search['balance_operator']==4) { echo "selected";}?>>less than</option>
                   <option value="5" <?php if(@$account_search['balance_operator']==5) { echo "selected";}?>>greather or equal than</option>
                   <option value="6" <?php if(@$account_search['balance_operator']==6) { echo "selected";}?>>less or equal than</option>
                   </select>
                 </span>
                 </div>
                 
                         
		                 
             </li>
             
             <li>
               <div class="float-left" style="width:30%">
                 <span>
                 	  <label>CreditLimit:</label>
                       <input size="20" class="text field" name="creditlimit" value="<?=@$account_search['creditlimit']?>" > &nbsp;
                       <select name="creditlimit_operator" class="field select" style="width:132px;"  >
                       <option value="1" <?php if(@$account_search['creditlimit_operator']==1) { echo "selected";}?> >is equal to</option>
                       <option value="2"  <?php if(@$account_search['creditlimit_operator']==2) { echo "selected";}?>>is not equal to</option>
                       <option value="3"  <?php if(@$account_search['creditlimit_operator']==3) { echo "selected";}?>>greater than</option>
                       <option value="4"  <?php if(@$account_search['creditlimit_operator']==4) { echo "selected";}?>>less than</option>
                       <option value="5"  <?php if(@$account_search['creditlimit_operator']==5) { echo "selected";}?>>greather or equal than</option>
                       <option value="6"  <?php if(@$account_search['creditlimit_operator']==6) { echo "selected";}?>>less or equal than</option>
                       </select>
                 </span>
                 </div>
        			
                  <div class="float-left" style="width:30%">
                  	<span>
                      <label>Billing Cycle :</label>
                      
                      <?=form_select_default('sweep',$sweeplist,@$account_search['sweep'],array("class"=>"select field", "style"=>"width:307px;"), '--Select Billing Cycle--')?>
                    </span>
                  </div>  
               <div class="float-left" style="width:30%">
                <span>
               <label>PTE:</label>
                 <select class="select field" name="posttoexternal" style="width:307px;" >
                 	<option value="" selected="selected" >--Select PTE--</option>
                    <option value="1" <?php if(@$account_search['desc']=='1') { echo "selected";}?> >YES</option>
                    <option value="0" <?php if(@$account_search['desc']=='0') { echo "selected";}?>>NO</option>
                  </select>
               </span>
              </div>
             
                  
             </li>
             
             <li>
               <div class="float-left" style="width:30%">
                <span>
                   <label>Account Type:</label>
                 <?=form_select_default('accounttype',$user_types,@$account_search['accounttype'],array("class"=>"select field", "style"=>"width:307px;"), '--Select Account Type--')?>           
                </span>
              </div>  
              
               <div class="float-left" style="width:30%">
                <span>
                 <label>Country:</label>
                 <?=form_countries('country',@$account_search['country'],array("class"=>"select field", "style"=>"width:307px;"), '--Select Country--')?>
                </span>
               </div> 
               
                <div class="float-left" style="width:30%">
                <span>
                 <label>Currency:</label>
                 <?=form_select_default('currency',$currency_list,@$account_search['currency'],array("class"=>"select field", "style"=>"width:307px;"), '--Select Currency--')?>
                </span>
                </div>
             </li>
            
               
            </fieldset>
            
         </ul>
          <br />
          
         <input type="button" id="id_reset" class="ui-state-default float-right ui-corner-all ui-button" name="reset" value="Clear Search Filter">&nbsp; 
        <input type="button" class="ui-state-default float-right ui-corner-all ui-button" name="action" value="Search" id="account_search" style="margin-right:22px;" />
        <br><br>
         </form>
              <!--<fieldset >
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
                      <li>
                      <label class="Searchdesc">Type:</label>
                      <select class="full field select" id="id_accounttype" >
                      <option value="-1" selected>ALL</option>
					  <?=$typelist?>
                      </select>
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
              </fieldset>-->           
            </div>
        </div>        


<div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">                        
    <div class="portlet-header ui-widget-header">Accounts List<span class="ui-icon ui-icon-circle-arrow-s"></span></div>
    <div class="portlet-content">         
		<form method="POST" action="del/0/" enctype="multipart/form-data" id="ListForm">
			<table id="flex1" align="left" style="display:none;"></table>
		</form>
	</div>
</div>  
<div class="ui-overlay" id="overlay" style="display:none;"><div class="ui-widget-overlay"></div>
  <div class="ui-widget-shadow ui-corner-all" style="width: 302px; height: 152px; position: absolute; left: 50px; top: 30px;">
  	<div style="width: auto; height: auto;margin:0 auto; padding: 10px;" class="ui-widget ui-widget-content ui-corner-all">
    <div class="ui-dialog-content ui-widget-content" style="background: none; border: 0;">
        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
    </div>
    </div>
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
