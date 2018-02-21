<? extend('master.php') ?>
<?php error_reporting(E_ERROR);?>
	<? startblock('extra_head') ?>
	<link rel="stylesheet" href="<?=base_url()?>css/flexigrid.css" type="text/css" />
	<script type="text/javascript" src="<?=base_url()?>js/flexigrid.js"></script>	
    
	<script type="text/javascript" src="<?=base_url()?>js/validate.js"></script>
	<script type="text/javascript">
		$().ready(function() {
		// validate signup form on keyup and submit
		$("#signupForm").validate({
			rules: {
				name: "required",
				accountno: "required",
				username: {
					required: true,
					minlength: 2
				},
				password: {
					required: true,
					minlength: 5
				},
				password1: {
					required: true,
					minlength: 5,
					equalTo: "#password"
				},
				email: {
					required: true,
					email: true
				},
				topic: {
					required: "#newsletter:checked",
					minlength: 2
				},
			messages: {
				firstname: "Please enter your firstname",
				lastname: "Please enter your lastname",
				username: {
					required: "Please enter a username",
					minlength: "Your username must consist of at least 2 characters"
				},
				password: {
					required: "Please provide a password",
					minlength: "Your password must be at least 5 characters long"
				},
				confirm_password: {
					required: "Please provide a password",
					minlength: "Your password must be at least 5 characters long",
					equalTo: "Please enter the same password as above"
				},
				email: "Please enter a valid email address",
				agree: "Please accept our policy"
			}
			}
		});
		});
	</script>
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
	<? endblock() ?>

    <? startblock('page-title') ?>
        <?=$page_title?><br/>
    <? endblock() ?>
    
	<? startblock('content') ?>

<input type="hidden" value="<?=$account['number']?>" />
<div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">
  <div class="portlet-header ui-widget-header">Account Details<span class="ui-icon ui-icon-circle-arrow-s"></span></div>
  <div class="portlet-content">
  <div class="hastable">
  <table class="details_table">  
  <tr>
    	<th>Account Number</th><td><?=$account['number']?></td>
        <th>Balance</th><td><?=$balance;?></td>
        <th>Account Type</th><td><?=@$user_type[$account['type']]?></td>
        <th>Name</th><td><?=$account['first_name']?></td>
  </tr>
  <tr>
  	 <th>Company</th><td><?=$account['company_name']?></td>
  	 <th>Address</th><td><?=$account['address_1'].'<br/>'.$account['address_2']?></td>  	 
  	 <th>Language</th><td><?=common_model::$global_config['language_list'][$account['language']];?></td>
     <th>City</th><td><?=$account['city']?></td>
  </tr>
  <tr>
      <th>Province/State</th><td><?=$account['province']?></td>
      <th>Zip/Postal Code</th><td><?=$account['postal_code']?></td>
      <th>Country</th><td><?=$account['country']?> </td>
      <th>Pricelist</th><td><?=$account['pricelist']?></td>
  </tr>
  <tr>
      <th>Billing Schedule</th><td><?=ucfirst($sweeplist[$account['sweep']]);?></td>
      <th>Credit Limit in</th><td><?=$credit_limit;?></td>
      <th>Timezone</th><td><?=$account['tz']?></td>      
      <th>Max Channels</th><td><?=$account['maxchannels']?></td>
  </tr>
  <tr>
<!--       <th>Pin</th><td><?=$account['pin']?></td> -->
      <th>Email</th><td><?=$account['email']?></td>
      <th>Dialed Number Mods</th><td><?=$account['dialed_modify']?></td>
      <th>IP Address</th><td>dynamic</td>
      <th>Telephone</th><td><?=$account['telephone_1']?></td>
  </tr>
  <!--<tr>      
      <th>Email</th><td><?=$account['email']?></td>-->
<!--       <th>Fascimile</th><td><?//=$account['fascimile']?></td> -->
  <!--    <th>&nbsp;</th><td>&nbsp;</td>
      <th>&nbsp;</th><td>&nbsp;</td>
  </tr>-->
  </table>
  </div>
  </div>
</div>
<br/>

<script type="text/javascript">
$(document).ready(function() {
	
$("#flex2").flexigrid({
    url: "<?php echo base_url();?>accounts/chargelist_json/<?=$account_number?>/",
    method: 'GET',
    dataType: 'json',
	colModel : [
		{display: 'Action',width:31, name: '', align: 'center', formatter:'showlink', formatoptions:{baseLinkUrl:'', }, }, 		
		{display: 'ID', name: 'ID', width:94,  sortable: false, align: 'center'},
        {display: 'Description', name: 'Description',width:94, sortable: false, align: 'center'},
        {display: 'Cycle', name: 'Cycle',width:106, sortable: false, align: 'center'},
		{display: 'Amount', name: 'Amount',width:141, sortable: false, align: 'center'},		

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
        

});


</script>

<div class="two-column" style="float:left;width: 100%;">
	<div class="column">
      <div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">
        <div class="portlet-header ui-widget-header">Charges<span class="ui-icon ui-icon-circle-arrow-s"></span></div>
        <div class="portlet-content">
         <div class="hastable" style="margin-bottom:10px;">
         <table id="flex2" align="left" style="display:none;"></table>    
              
         </div>
          <div class="content-box content-box-header ui-corner-all float-left full">
                <div class="ui-state-default ui-corner-top ui-box-header">
                    <span class="ui-icon float-left ui-icon-signal"></span>
                    Add Charge
                </div>
                <div class="content-box-wrapper">
                 <form method="post" action="<?=base_url()?>accounts/account_detail_add" enctype="multipart/form-data">
                 <input name="mode" value="View Details" type="hidden">
                 <input type="hidden" name="accountnum" value="<?=$account['number']?>" />
                 <div class="sub-form">
                 <div>
                 <label class="desc">Applyable Charges</label>
                 <select class="select field large" name="applyable_charges"><? //=$applyable_charges?>
                 <?php 
				 foreach($chargelist as $key => $value){
					 ?>
                     <option value="<?=$key?>"><?=$value?></option>
                     <?
				 }?>
                 </select>
                 </div>
                 <div>
                 <label class="desc">ID</label>
                 <input class="text field large" name="id" size="3" type="text">
                 </div>
                 <div style="margin-top:14px;">
                 <input class="ui-state-default ui-corner-all ui-button" name="action" value="Add Charge..." type="submit">
                 </div>
                 </div>
                 </form>
                 </div>
          </div>
         </div>
        </div>       
    </div>
    
  <script type="text/javascript">
$(document).ready(function() {
	
$("#flex3").flexigrid({
    url: "<?php echo base_url();?>accounts/dids_json/<?=$account_number?>/",
    method: 'GET',
    dataType: 'json',
	colModel : [			
		{display: 'Number', name: 'Number', width:172,  sortable: false, align: 'center'},
        {display: 'Monthly Fee', name: 'MonthlyFee',width:174, sortable: false, align: 'center'},
        {display: 'Action',width:172, name: '', align: 'center', formatter:'showlink', formatoptions:{baseLinkUrl:'', }, }, 	
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
        

});
</script>  
    <div class="column column-right">
      <div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">
        <div class="portlet-header ui-widget-header">DIDs<span class="ui-icon ui-icon-circle-arrow-s"></span></div>
        <div class="portlet-content">      
          <div class="hastable" style="margin-bottom:10px;">
           <table id="flex3" align="left" style="display:none;"></table>    
         <!-- <table>
          <thead>
              <tr>
                  <td>Number</td>
                  <td>Monthly Fee</td>
                  <td>Action</td>
              </tr>
          </thead>
          
              <? //$account_did_list?>	
              <?php 
			  foreach($account_did_list as $row){
				$cost = $this->common_model->calculate_currency($row['monthlycost']);
			  ?>
              <tr><td><?=$row['number']?></td><td><?=$cost?></td><td><a href="/accounts/did_remove/<?=$row['number']?>">remove</a></td></td></tr>
              <?
			  }?>
          </table>-->
          </div>
		  <div class="content-box content-box-header ui-corner-all float-left full">
                  <div class="ui-state-default ui-corner-top ui-box-header">
                      <span class="ui-icon float-left ui-icon-signal"></span>
                      Add DID
                  </div>
                  <div class="content-box-wrapper">
                  <div class="sub-form"> 
                 <form method="post" action="<?=base_url()?>accounts/account_detail_add" enctype="multipart/form-data">
                   <input name="mode" value="View Details" type="hidden">
                 	<input type="hidden" name="accountnum" value="<?=$account['number']?>" />         
                          <div><label class="desc">Number</label>
                          <select class="select field large" name="did_list">
                          <?php 
						  foreach($availabledids as $key => $value){
							  foreach($value as $newval) {
							  ?>
                              <option value="<?=@$newval?>"><?=@$newval?></option>
                              <?
							  }
							  
						  }?>
						  <? //=$available_dids?>
                         
                          </select></div>
                         <!-- <div><label class="desc">Monthly Fee</label><input class="text field large" name="id" size="3" type="text"></div>-->
                          <div style="margin-top:14px;"><input class="ui-state-default ui-corner-all ui-button" name="action" value="Purchase DID" type="submit"></div>
                          </form>
                  </div>
                  </div>
          </div>

          </div>
      </div>         
    </div>
</div>
<script type="text/javascript">
$(document).ready(function() {
	
$("#flex4").flexigrid({
    url: "<?php echo base_url();?>accounts/ani_json/<?=$account_number?>/",
    method: 'GET',
    dataType: 'json',
	colModel : [			
		{display: 'ANI/CLID/PREFIX', name: 'ANI/CLID/PREFIX', width:154,  sortable: false, align: 'center'},
        {display: 'Context - Blank = default', name: 'ContextBlankdefault',width:194, sortable: false, align: 'center'},
        {display: 'Action',width:172, name: '', align: 'center', formatter:'showlink', formatoptions:{baseLinkUrl:'', }, }, 	
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
        

});
</script>
<div class="two-column" style="float:left;width: 100%;">
	<div class="column">        
      <div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">
        <div class="portlet-header ui-widget-header">ANI & Prefix Mapping - Either enter prefix or ANI/CLID<span class="ui-icon ui-icon-circle-arrow-s"></span></div>
        <div class="portlet-content">          
          <div class="hastable" style="margin-bottom:10px;">
          <table id="flex4" align="left" style="display:none;"></table>    
          <!--<table>
          <thead>    
              <tr>
                  <td>ANI/CLID/PREFIX</td>
                  <td>Context - Blank = default</td>
                  <td>Action</td>
              </tr>	
           </thead>
           		
              <? //$account_ani_list?>
              <?php
			  foreach($account_ani_list as $row)
			  {
				  ?>
                  <tr><td><?=$row['number']?></td><td><?=$row['context']?></td><td><a href="">remove</a></td></tr>
                  <?
			  }
              ?>
          </table>-->
          </div>
          <div class="content-box content-box-header ui-corner-all float-left full" style="position:relative; z-index:9999">
                <div class="ui-state-default ui-corner-top ui-box-header">
                    <span class="ui-icon float-left ui-icon-signal"></span>
                    Map ANI
                </div>
                <div class="content-box-wrapper"> 
                <div class="sub-form">   
                <form method="post" action="<?=base_url()?>accounts/account_detail_add" enctype="multipart/form-data">
                  <input type="hidden" name="accountnum" value="<?=$account['number']?>" />     
                  <div><label class="desc">ANI</label><input class="text field large" name="ANI" size="20" type="text"></div>
                  <div><label class="desc">Context</label><input class="text field large" name="context" size="20" type="text"></div>
                  <div style="margin-top:14px;"><input class="ui-state-default ui-corner-all ui-button" name="action" value="Map ANI" type="submit"></div>
                </form>  
                </div>
                </div>
           </div>
        </div>
      </div>       
    </div>
    
 <script type="text/javascript">
$(document).ready(function() {
	
$("#flex5").flexigrid({
    url: "<?php echo base_url();?>accounts/ip_json/<?=$account_number?>/",
    method: 'GET',
    dataType: 'json',
	colModel : [			
		{display: 'IP Address', name: 'IPAddress', width:80,  sortable: false, align: 'center'},
		{display: 'Prefix', name: 'Prefix',width:80, sortable: false, align: 'center'},
		{display: 'Context - blank = default', name: 'Contexblankdefault',width:100, sortable: false, align: 'center'},
		{display: 'Created Date', name: 'Created_date',width:100, sortable: false, align: 'center'},
		{display: 'Action',width:120, name: '', align: 'center', formatter:'showlink', formatoptions:{baseLinkUrl:'', }, }, 	
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
        

});
</script>   
<script type="text/javascript">
		$(document).ready(function() {
		// validate signup form on keyup and submit
		$("#ip_map").validate({
                	rules: {
                                    ip: "required",
        			},
                          messages: {
                                    ip: "Please enter Vallid ip address",
                                }
                	});
		});
 </script>

    <div class="column column-right">
      <div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">
        <div class="portlet-header ui-widget-header">IP Address Mapping<span class="ui-icon ui-icon-circle-arrow-s"></span></div>
        <div class="portlet-content">            
            <div class="hastable" style="margin-bottom:10px;">
             <table id="flex5" align="left" style="display:none;"></table>    
           <!-- <table>
            <thead>
                <tr>
                    <td>IP Address</td>
                    <td>Prefix</td>
                    <td>Context - blank = default</td>
                    <td>Action</td>
                </tr>
            </thead>
                <? //$account_ip_list?>	
                <?php 
				foreach($account_ip_list as $row){
				?>
                 <tr>
                    <td><?=$row['ip']?></td>
                    <td><?=$row['prefix']?></td>
                    <td><?=$row['context']?></td>
		    <td><?=$row['created_date']?></td>
                    <td>Action</td>
                </tr>   
                 <?
				}?>
            </table>-->
            </div>
            <div class="content-box content-box-header ui-corner-all float-left full">
                  <div class="ui-state-default ui-corner-top ui-box-header">
                      <span class="ui-icon float-left ui-icon-signal"></span>
                      Map IP
                  </div>
                  <div class="content-box-wrapper"> 
                  <div class="sub-form">
		<form method="post" name="ip_map" id="ip_map" action="<?=base_url()?>accounts/account_detail_add" enctype="multipart/form-data">
                  <input type="hidden" name="accountnum" value="<?=$account['number']?>" />
                      <div><label class="desc">IP</label><input class="text field large" name="ip" size="16" type="text"></div>
                      <div><label class="desc">Prefix</label><input class="text field large" name="prefix" size="16" type="text"></div>
                      <div><label class="desc">IP Context</label><input class="text field large" name="ipcontext" size="16" type="text"></div>
                      <div style="margin-top:14px; width:80px;"><input class="ui-state-default ui-corner-all ui-button" name="action" value="Map IP" type="submit"></div>
                  </form>
                  </div>
                  </div>
             </div>
                      
        </div>
      </div>           
    </div>
</div>
<div class="two-column" style="float:left;width: 100%;">
<script type="text/javascript">
$(document).ready(function() {
	
$("#flex7").flexigrid({
    url: "<?php echo base_url();?>accounts/iax_sip_json/<?=$account_number?>/",
    method: 'GET',
    dataType: 'json',
	colModel : [			
		{display: 'Tech', name: 'Tech', width:77,  sortable: false, align: 'center'},
        {display: 'Type', name: 'Type',width:118, sortable: false, align: 'center'},
		{display: 'Username', name: 'Username',width:83, sortable: false, align: 'center'},
		{display: 'Password', name: 'Password',width:110, sortable: false, align: 'center'},
		{display: 'Context', name: 'Context',width:79, sortable: false, align: 'center'},

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
        

});
</script>

	<div class="column">
      <div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">
        <div class="portlet-header ui-widget-header">Post Charge to Account<span class="ui-icon ui-icon-circle-arrow-s"></span></div>
        <div class="portlet-content">          
          <div class="hastable" style="margin-bottom:10px;">
         <!-- <table>
          <thead>    
              <tr>
                  <td>Description</td>
                  <td>Charge in <TMPL_VAR NAME="currency"></td>
                  <td>Action</td>
              </tr>
          </thead>
          </table>-->
          </div>          
            <div class="content-box content-box-header ui-corner-all float-left full">
                  <div class="ui-state-default ui-corner-top ui-box-header">
                      <span class="ui-icon float-left ui-icon-signal"></span>
                      Post Charge
                  </div>
                  <div class="content-box-wrapper"> 
                  <div class="sub-form">
                <form method="post" action="<?=base_url()?>accounts/account_detail_add" enctype="multipart/form-data">
                  <input type="hidden" name="accountnum" value="<?=$account['number']?>" />
                  <div><label class="desc">Description</label><input class="text field large" name="desc" size="16" type="text"></div>
                  <div><label class="desc">Amount</label><input class="text field large" name="amount" size="8" type="text"></div>
                  <div style="margin-top:14px;"><input class="ui-state-default ui-corner-all ui-button" name="action" value="Post Charge..." type="submit"></div>
                  </form>
                  </div>
                  </div>
             </div>
        </div>
      </div>         
    </div>
    
<script type="text/javascript">
$(document).ready(function() {
	
$("#flex7").flexigrid({
    url: "<?php echo base_url();?>accounts/iax_sip_json/<?=$account_number?>/",
    method: 'GET',
    dataType: 'json',
	colModel : [			
		{display: 'Tech', name: 'Tech', width:77,  sortable: false, align: 'center'},
        {display: 'Type', name: 'Type',width:118, sortable: false, align: 'center'},
		{display: 'Username', name: 'Username',width:83, sortable: false, align: 'center'},
		{display: 'Password', name: 'Password',width:110, sortable: false, align: 'center'},
		{display: 'Context', name: 'Context',width:79, sortable: false, align: 'center'},

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
        

});
</script>     
    <div class="column column-right">
      <div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">
        <div class="portlet-header ui-widget-header">IAX2 & SIP Accounts<span class="ui-icon ui-icon-circle-arrow-s"></span></div>
        <div class="portlet-content">          
           <div class="hastable" style="margin-bottom:10px;">
            <table id="flex7" align="left" style="display:none;"></table>       
          <!-- <table>
           <thead>
              <tr>
                  <td>Tech</td>
                  <td>Type</td>
                  <td>Username</td>
                  <td>Password</td>
                  <td>Context</td>
              </tr>
           </thead>
              < ?=$account_device_list?>
           </table>  -->      
           </div>
        </div>
      </div>
      
 <script type="text/javascript">
$(document).ready(function() {
	
$("#flex6").flexigrid({
    url: "<?php echo base_url();?>accounts/invoice_json/<?=$account_number?>/",
    method: 'GET',
    dataType: 'json',
	colModel : [			
		{display: 'Invoice Number', name: 'InvoiceNumber', width:81,  sortable: false, align: 'center'},
        {display: 'Invoice Date', name: 'Invoice Date',width:71, sortable: false, align: 'center'},
		{display: 'Invoice Total', name: 'InvoiceTotal',width:123, sortable: false, align: 'center'},
		{display: 'HTML View', name: 'HTMLView',width:95, sortable: false, align: 'center'},
		{display: 'PDF View', name: 'PDFView',width:88, sortable: false, align: 'center'},

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
        

});
</script>     
      
      <div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">
        <div class="portlet-header ui-widget-header">Invoices<span class="ui-icon ui-icon-circle-arrow-s"></span></div>
        <div class="portlet-content">          
           <div class="hastable" style="margin-bottom:10px;">   
            <table id="flex6" align="left" style="display:none;"></table>       
           <!--  <table>
             <thead>
                <tr>
                    <td>Invoice Number</td>
                    <td>Invoice Date</td>
                    <td>Invoice Total</td>
                    <td>HTML View</td>
                    <td>PDF View</td>
                </tr>
             </thead>
                <?=$account_invoice_list?>
             </table>    -->    
           </div>
        </div>
      </div> 
    </div>
</div>


           <script type="text/javascript">
$(document).ready(function() {
	
$("#flex1").flexigrid({
    url: "<?php echo base_url();?>accounts/account_detail_json/<?=$account_number?>/",
    method: 'GET',
    dataType: 'json',
	colModel : [
		{display: 'UniqueID', name: 'UniqueID', width:80,  sortable: false, align: 'center'},
        {display: 'Date & Time', name: 'DateTime',width:80, sortable: false, align: 'center'},
        {display: 'Caller*ID', name: 'CallerID',width:90, sortable: false, align: 'center'},
		{display: 'Called Number', name: 'CalledNumber',width:90, sortable: false, align: 'center'},
		{display: 'Disposition', name: 'Disposition',width:90, sortable: false, align: 'center'},
		{display: 'Billable Seconds', name: 'BillableSeconds',width:90, sortable: false, align: 'center'},
        {display: 'Charge', name: 'Charge', width:90, sortable: false, align: 'center'},
        {display: 'Credit', name: 'Credit', width:90,  sortable: false, align: 'center'},
        {display: 'Notes', width:120,name: 'Notes',  sortable: false, align: 'center'},
        {display: 'Cost',width:60, name: 'Cost',  sortable: false, align: 'center'},
        {display: 'Profit', width:100,name: 'Profit',  sortable: false, align: 'center'},
        
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
});


function reload_button()
{
    $('#flex1').flexReload();
}

</script>	
<div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all float-left full">
        <div class="portlet-header ui-widget-header">CDR List<span class="ui-icon ui-icon-circle-arrow-s"></span></div>
        <div class="portlet-content">
           <div class="hastable" style="margin-bottom:10px;">       
           <form method="POST" action="del/0/" enctype="multipart/form-data" id="ListForm">
			<table id="flex1" align="left" style="display:none;"></table>
			</form>
          <!-- <table>
           <thead>
              <tr>
                  <td>UniqueID</td>
                  <td>Date & Time</td>
                  <td>Caller*ID</td>
                  <td>Called Number</td>
                  <td>Disposition</td>
                  <td>Billable Seconds</td>
                  <td>Charge</td>
                  <td>Credit</td>
                  <td>Notes</td>    
                  <td>Cost</td>
                  <td>Profit</td>
              </tr>
              
              < ?php foreach($cdrlist as $row){
				  ?>
                  <tr>
                  <td>< ?=$row['uniqueid']?></td>
                  <td>< ?=$row['callstart']?></td>
                  <td>< ?=$row['callerid']?></td>
                  <td>< ?=$row['callednum']?></td>
                  <td>< ?=$row['disposition']?></td>
                  <td>< ?=$row['billseconds']?></td>
                  <td>< ?=$row['debit']?></td>
                  <td>< ?=$row['credit']?></td>
                  <td>< ?=$row['notes']?></td>    
                  <td>< ?=$row['cost']?></td>
                  <td>< ?=$row['profit']?></td>
              </tr>
                  < ?
			  }?>
           </thead>
              < ?=@$account_cdr_list?>	
           </table>-->
           </div>     
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
