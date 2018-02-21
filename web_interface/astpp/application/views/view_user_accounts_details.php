<? extend('user_master.php') ?>
<?php error_reporting(E_ERROR);?>
	<? startblock('extra_head') ?>
	<link rel="stylesheet" href="/css/flexigrid.css" type="text/css" />
	<script type="text/javascript" src="/js/flexigrid.js"></script>	
    
	<script type="text/javascript" src="/js/validate.js"></script>
	
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
        <th>Account Type</th><td><?=  ucfirst(Common_model::$global_config['userlevel'][$account['type']]);?></td>
        <th>Name</th><td><?=$account['first_name']?></td>
  </tr>
  <tr>
  	 <th>Company</th><td><?=$account['company_name']?></td>
  	 <th>Address</th><td><?=$account['address_1'].'<br/>'.$account['address_2']?></td>
  	 <th>Language</th><td><?=  ucfirst(Common_model::$global_config['language_list'][$account['language']]);?></td>
        <th>City</th><td><?=$account['city']?></td>
  </tr>
  <tr>
      <th>Province/State</th><td><?=$account['province']?></td>
      <th>Zip/Postal Code</th><td><?=$account['postal_code']?></td>
      <th>Country</th><td><?=$account['country']?> </td>
      <th>Pricelist</th><td><?=$account['pricelist']?></td>
  </tr>
  <tr>
      <th>Billing Schedule</th><td><?=  ucfirst(Common_model::$global_config['sweeplist'][$account['sweep']]);?></td>
      <th>Credit Limit in</th><td><?=$credit_limit?></td>
      <th>Timezone</th><td><?=$account['tz']?></td>      
      <th>Max Channels</th><td><?=$account['maxchannels']?></td>
  </tr>
  <tr>
<!--      <th>Pin</th><td><?=$account['pin']?></td>-->
      <th>Dialed Number Mods</th><td><?=$account['dialed_modify']?></td>
      <th>IP Address</th><td>dynamic</td>
      <th>Telephone</th><td><?=$account['telephone_1']?></td>
      <th>Email</th><td><?=$account['email']?></td>
  </tr>
<!--  <tr>      
      
      <th>Fascimile</th><td><?=$account['fascimile']?></td>
      <th>&nbsp;</th><td>&nbsp;</td>
      <th>&nbsp;</th><td>&nbsp;</td>
  </tr>-->
  </table>
  </div>
      
  </div>

          
        <a rel="facebox" href="<?=base_url()?>user/edit_account/<?=$this->session->userdata('username')?>">
							<input class="ui-state-default float-right ui-corner-all ui-button" rel="facebox"  type="submit" name="action" value="Edit Account" />
			
						</a>
  
  
  


</div>
<br/>

<script type="text/javascript">
$(document).ready(function() {
	
$("#flex2").flexigrid({
    url: "<?php echo base_url();?>user/chargelist_json/",
    method: 'GET',
    dataType: 'json',
	colModel : [
		{display: 'ID', name: 'ID', width:94,  sortable: false, align: 'center'},
        {display: 'Description', name: 'Description',width:94, sortable: false, align: 'center'},
        {display: 'Cycle', name: 'Cycle',width:106, sortable: false, align: 'center'},
		{display: 'Amount', name: 'Amount',width:106, sortable: false, align: 'center'},		

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
          <!--<div class="content-box content-box-header ui-corner-all float-left full">
                <div class="ui-state-default ui-corner-top ui-box-header">
                    <span class="ui-icon float-left ui-icon-signal"></span>
                    Add Charge
                </div>
                <div class="content-box-wrapper">
                
                 </div>
          </div>-->
         </div>
        </div>       
    </div>
    
  <script type="text/javascript">
$(document).ready(function() {
	
$("#flex3").flexigrid({
    url: "<?php echo base_url();?>user/userdids_json/",
    method: 'GET',
    dataType: 'json',
	colModel : [			
		{display: 'Number', name: 'Number', width:172,  sortable: false, align: 'center'},
        {display: 'Monthly Fee', name: 'MonthlyFee',width:172, sortable: false, align: 'center'},
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
				$cost = money_format('%.2n', $row['monthlycost']/10000);
			  ?>
              <tr><td><?=$row['number']?></td><td><?=$cost?></td><td><a href="/accounts/did_remove/<?=$row['number']?>">remove</a></td></td></tr>
              <?
			  }?>
          </table>-->
          </div>
		  <!--<div class="content-box content-box-header ui-corner-all float-left full">
                  <div class="ui-state-default ui-corner-top ui-box-header">
                      <span class="ui-icon float-left ui-icon-signal"></span>
                      Add DID
                  </div>
                  <div class="content-box-wrapper">
                 
                  </div>
          </div>-->

          </div>
      </div>         
    </div>
</div>
<script type="text/javascript">
$(document).ready(function() {
	
$("#flex1").flexigrid({
    url: "<?php echo base_url();?>user/account_detail_json/",
    method: 'GET',
    dataType: 'json',
	colModel : [
		{display: 'UniqueID', name: 'UniqueID', width:190,  sortable: false, align: 'center'},
        {display: 'Date & Time', name: 'DateTime',width:155, sortable: false, align: 'center'},
        {display: 'Caller*ID', name: 'CallerID',width:90, sortable: false, align: 'center'},
		{display: 'Called Number', name: 'CalledNumber',width:90, sortable: false, align: 'center'},
		{display: 'Disposition', name: 'Disposition',width:90, sortable: false, align: 'center'},
		{display: 'Billable Seconds', name: 'BillableSeconds',width:90, sortable: false, align: 'center'},
        {display: 'Charge', name: 'Charge', width:90, sortable: false, align: 'center'},
        {display: 'Credit', name: 'Credit', width:90,  sortable: false, align: 'center'},
        {display: 'Notes', width:120,name: 'Notes',  sortable: false, align: 'center'}/*,*/
//         {display: 'Cost',width:60, name: 'Cost',  sortable: false, align: 'center'},
//         {display: 'Profit', width:100,name: 'Profit',  sortable: false, align: 'center'},
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
              
              <?php foreach($cdrlist as $row){
				  ?>
                  <tr>
                  <td><?=$row['uniqueid']?></td>
                  <td><?=$row['callstart']?></td>
                  <td><?=$row['callerid']?></td>
                  <td><?=$row['callednum']?></td>
                  <td><?=$row['disposition']?></td>
                  <td><?=$row['billseconds']?></td>
                  <td><?=$row['debit']?></td>
                  <td><?=$row['credit']?></td>
                  <td><?=$row['notes']?></td>    
                  <td><?=$row['cost']?></td>
                  <td><?=$row['profit']?></td>
              </tr>
                  <?
			  }?>
           </thead>
              <?=@$account_cdr_list?>	
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
