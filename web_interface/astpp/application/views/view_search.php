<style type="text/css"> 			
/*#ui-datepicker-div, .ui-datepicker{ font-size: 80%; }*/
/* css for timepicker */
.ui-timepicker-div .ui-widget-header { margin-bottom: 8px; }
.ui-timepicker-div dl { text-align: left; }
.ui-timepicker-div dl dt { height: 25px; margin-bottom: -25px; }
.ui-timepicker-div dl dd { margin: 0 10px 10px 65px; }
.ui-timepicker-div td { font-size: 90%; }
.ui-tpicker-grid-label { background: none; border: none; margin: 0; padding: 0; }	

</style> 

<script language="javascript">
var selection = 0;
$('#search').change(function() {
	
  $('#form' + selection).hide();
  selection = $(this).val();
  $('#form' + selection).show();
});

/*jQuery(document).ready(function() {
     jQuery('a[rel*=facebox]').facebox()
 })*/

</script>
 <script>
	  $(document).ready(function() {
		$("#from_date").datetimepicker({ dateFormat: 'yy-mm-dd' });		
		$("#to_date").datetimepicker({ dateFormat: 'yy-mm-dd' });		
		$("#first_from_date").datetimepicker({ dateFormat: 'yy-mm-dd' });		
		$("#first_to_date").datetimepicker({ dateFormat: 'yy-mm-dd' });	
		$("#creation_from_date").datetimepicker({ dateFormat: 'yy-mm-dd' });		
		$("#creation_to_date").datetimepicker({ dateFormat: 'yy-mm-dd' });			
	  });
	  </script> 
<div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">                        
        <div class="portlet-header ui-widget-header">Quick Search<span class="ui-icon ui-icon-circle-arrow-s"></span></div>
        <div class="portlet-content">
         <form action="<?=base_url()?>" id="frm_search" method="POST" enctype="multipart/form-data">
          Please Select Module Name for Search<br><br>
          <select name="search" id="search">          
          <option value="0">--Select Module--</option>
          <?php 
		  if ($this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 2 || $this->session->userdata('logintype') == 4) 
		  { 
		  ?>          
          <option  value="1">Accounts</option>
         <? } ?> 
         <?php 
			if ($this->session->userdata('logintype') == 1) 
			{ 
			?>
            
          <option value="2">Reseller Reports</option>
          <option value="22">User Report</option>
          <? }?>
          <?php 
			if ($this->session->userdata('logintype') == 2) 
			{ 
			?>
           <option value="2">Reseller Reports</option>  
          <option value="3">Provider Report</option>
          <? }?>
         <?php 
			if ($this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 2 ) 
			{ 
			?>
          <option value="4">Calling Card</option>
         <option value="5">Calling Card Brand</option>
          <? } ?>
          <?php 
			if ($this->session->userdata('logintype') == 2)
			{ ?>
          <option value="6">Calling Card CDR</option>
          <? } ?>
          <?php 
		  if ($this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 2 ) 
		  { 
		  ?> 
          <option value="7">DIDs</option>
          <? }?>
          <?php 
			if ($this->session->userdata('logintype') == 2) 
			{ 
			?>
          <option value="8">Providers</option>
          <? } ?>
          <?php 
		  if ($this->session->userdata('logintype') == 2) 
		 {		 
		 ?>
          <option value="9">Trunks</option>
          <? } ?>
            <?php 
	   	   if ($this->session->userdata('logintype') == 2 || $this->session->userdata('logintype') == 3) 
		  { 
		  ?>
          <option value="10">Outbound Routes</option>
          <? } ?>
          <?php 
			if ($this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 2 ) 
			{ 
			?>
          <option value="11">Pricelist</option>
          <? } ?>
           <?php 
			if ($this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 2 ) 
			{ 
			?>
          <option value="12">Routes</option>
          <? } ?>
            <?
			if ($this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 2 ) 
			{	 

			?>
         <option value="13">Periodic Charges</option>                
          <option value="14">Packages</option>
          <option value="15">Counters</option>
           <? }?>
           <?php 
			if ($this->session->userdata('logintype') == 2) 
			{ 
			?>
          <option value="16">List Errors</option>
          <? }?>
            <?php 
			if ($this->session->userdata('logintype') == 2 || $this->session->userdata('logintype') == 3 ) 
			{ 
			?>
          <option value="17">Trunk Stats</option>
          <? }?>
          	<?php 
			if ($this->session->userdata('logintype') == 2 ) 
			{ 
			?>	
          <option value="18">View Freeswitch CDRs</option>         
          <option value="19">Freeswitch SIP Devices</option>
          <option value="20">System Configuration</option>
          <option value="21">Taxes</option>  
	  <option value="23">CallShop Report</option>
	  <option value="24">Account Invoices</option>  
	  <option value="25">Account Taxes</option> 
	  <option value="26">Template</option>  
           <? } ?>        
          </select>
         </form>
         
         <br><br><br>
         <form action="<?=base_url()?>accounts/search" id="form1" name="form1" method="POST" enctype="multipart/form-data" style="display:none">
         <input type="hidden" name="advance_search" value="1">
         <ul style="width:675px; list-style:none;">
          <fieldset  style="width:660px;">
            <legend><span style="font-size:14px; font-weight:bold; color:#000;">Search Account</span></legend>
             <li>   
                  <label class="desc">Account number / Card number :</label>
                  <input size="20" class="text field medium" name="account_number"> &nbsp;<select name="account_number_operator" style="padding:5px;width:132px;"><option value="1">contains</option><option value="2">doesn't contain</option><option value="3">is equal to</option><option value="4">is not equal to</option></select>
             </li>
              <li>
                  <label class="desc">Pricelist :</label>
                 <?=form_select_default('pricelist',@$pricelist,"",array("class"=>"select field medium"), '--Select PriceList--')?>
                </li>
               <li>
               <label class="desc">Firstname:</label>
               <input size="20" class="text field medium" name="first_name"> &nbsp;<select name="first_name_operator" style="padding:5px"><option value="1">contains</option><option value="2">doesn't contain</option><option value="3">is equal to</option><option value="4">is not equal to</option></select>
               </li> 
                <li>
               <label class="desc">Lastname:</label>
               <input size="20" class="text field medium" name="last_name"> &nbsp;<select name="last_name_operator" style="padding:5px"><option value="1">contains</option><option value="2">doesn't contain</option><option value="3">is equal to</option><option value="4">is not equal to</option></select>
               </li> 
                <li>
               <label class="desc">Company:</label>
               <input size="20" class="text field medium" name="company"> &nbsp;<select name="company_operator" style="padding:5px"><option value="1">contains</option><option value="2">doesn't contain</option><option value="3">is equal to</option><option value="4">is not equal to</option></select>
               </li> 
                <li>
               <label class="desc">Balance:</label>
               <input size="20" class="text field medium" name="balance"> &nbsp;<select name="balance_operator" style="padding:5px;width:132px;"><option value="1">is equal to</option><option value="2">is not equal to</option><option value="3">greater than</option><option value="4">less than</option><option value="5">greather or equal than</option><option value="6">less or equal than</option></select>
               </li>
                 <li>
               <label class="desc">CreditLimit:</label>
               <input size="20" class="text field medium" name="creditlimit"> &nbsp;<select name="creditlimit_operator" style="padding:5px;width:132px;"><option value="1">is equal to</option><option value="2">is not equal to</option><option value="3">greater than</option><option value="4">less than</option><option value="5">greather or equal than</option><option value="6">less or equal than</option></select>
               </li>
                <li>
                  <label class="desc">Billing Cycle :</label>
                  <?=form_select_default('sweep',$sweeplist,"",array("class"=>"select field medium"), '--Select Billing Cycle--')?>
                </li>
                 <li>
                  <label class="desc">PTE:</label>
                 <select class="select field medium" name="posttoexternal">
                    <option value="1" >YES</option>
                    <option value="0" >NO</option>
                  </select>
                </li>
                <li>
                <label class="desc">Account Type:</label>
                <?=form_select_default('accounttype',$user_types,"",array("class"=>"select field medium"), '--Select Account Type--')?>
                </li>
                <li>
                <label class="desc">Country:</label>
                <?=form_countries('country',"",array("class"=>"select field small"), '--Select Country--')?>
                </li>
                <li>
                <label class="desc">Currency:</label>
                <?=form_select_default('currency',$currency_list,"",array("class"=>"select field medium"), '--Select Currency--')?>
                </li>
               
            </fieldset>
            
         </ul>
          <br />
        <input type="submit" class="ui-state-default float-right ui-corner-all ui-button" name="action" value="Search" />
        <br><br>
         </form>
         
         <form action="<?=base_url()?>adminReports/reseller_search" id="form2" name="form2" method="POST" enctype="multipart/form-data" style="display:none">
         <input type="hidden" name="advance_search" value="1">
         <ul style="width:675px; list-style:none;">
          <fieldset  style="width:660px;">
            <legend><span style="font-size:14px; font-weight:bold; color:#000;">Search Reseller Report</span></legend>
              <li>
               <label class="desc">Reseller:</label>
                <input size="20" class="text field medium" name="reseller" id="reseller">
                <a onclick="window.open('<?=base_url()?>adminReports/reseller_list/' , 'ResellerList','scrollbars=1,width=650,height=330,top=20,left=100,scrollbars=1');" href="#"><img src="<?=base_url()?>images/icon_arrow_orange.gif" border="0"></a>
              </li>
              
               <li>
              <label class="desc">Destination:</label>
              <select class="select field medium" name="Destination" id="Destination">
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
              </li>
               <li>
              <label class="desc">IDD Code:</label>
              <select class="select field medium" name="Pattern" value="IDD Code" id="Pattern">
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
              </li>
            <li>   
            <label class="desc">Start Date & Time  :</label>
            <input size="20" class="text field medium" name="start_date" id="from_date"> 
            
            </li>
            <li>   
            <label class="desc">End Date & Time:</label>
            <input size="20" class="text field medium" name="end_date" id="to_date">             
            </li>
            </fieldset>
          </ul>
           <br />
        <input type="submit" class="ui-state-default float-right ui-corner-all ui-button" name="action" value="Search" />
        <br><br>
         </form>   
          <script>
	  $(document).ready(function() {
		$("#provider_from_date").datetimepicker({ dateFormat: 'yy-mm-dd' });		
		$("#provider_to_date").datetimepicker({ dateFormat: 'yy-mm-dd' });			
	  });
	  </script> 
         <form action="<?=base_url()?>adminReports/provider_search" id="form3" name="form3" method="POST" enctype="multipart/form-data" style="display:none">
         <input type="hidden" name="advance_search" value="1">
         <ul style="width:675px; list-style:none;">
          <fieldset  style="width:660px;">
            <legend><span style="font-size:14px; font-weight:bold; color:#000;">Search Provider Report</span></legend>
              <li>
               <label class="desc">Provider:</label>
                <input size="20" class="text field medium" name="reseller" id="reseller">
                <a onclick="window.open('<?=base_url()?>adminReports/provider_list/' , 'ProviderList','scrollbars=1,width=650,height=330,top=20,left=100,scrollbars=1');" href="#"><img src="<?=base_url()?>images/icon_arrow_orange.gif" border="0"></a>
              </li>
              
               <li>
              <label class="desc">Destination:</label>
              <select class="select field medium" name="Destination" id="Destination">
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
              </li>
               <li>
              <label class="desc">IDD Code:</label>
              <select class="select field medium" name="Pattern" value="IDD Code" id="Pattern">
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
              </li>
            <li>   
            <label class="desc">Start Date & Time  :</label>
            <input size="20" class="text field medium" name="start_date" id="provider_from_date"> 
            
            </li>
            <li>   
            <label class="desc">End Date & Time:</label>
            <input size="20" class="text field medium" name="end_date" id="provider_to_date">             
            </li>
            </fieldset>
          </ul>
           <br />
        <input type="submit" class="ui-state-default float-right ui-corner-all ui-button" name="action" value="Search" />
        <br><br>
         </form>
         
         
          <script>
	  $(document).ready(function() {
		$("#user_from_date").datetimepicker({ dateFormat: 'yy-mm-dd' });		
		$("#user_to_date").datetimepicker({ dateFormat: 'yy-mm-dd' });			
	  });
	  </script> 
         <form action="<?=base_url()?>adminReports/user_search" id="form22" name="form22" method="POST" enctype="multipart/form-data" style="display:none">
         <input type="hidden" name="advance_search" value="1">
         <ul style="width:675px; list-style:none;">
          <fieldset  style="width:660px;">
            <legend><span style="font-size:14px; font-weight:bold; color:#000;">Search User Report</span></legend>
              <li>
               <label class="desc">User:</label>
                <input size="20" class="text field medium" name="reseller" id="reseller">
                <a onclick="window.open('<?=base_url()?>adminReports/user_list/' , 'UserList','scrollbars=1,width=650,height=330,top=20,left=100,scrollbars=1');" href="#"><img src="<?=base_url()?>images/icon_arrow_orange.gif" border="0"></a>
              </li>
              
               <li>
              <label class="desc">Destination:</label>
              <select class="select field medium" name="Destination" id="Destination">
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
              </li>
               <li>
              <label class="desc">IDD Code:</label>
              <select class="select field medium" name="Pattern" value="IDD Code" id="Pattern">
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
              </li>
            <li>   
            <label class="desc">Start Date & Time  :</label>
            <input size="20" class="text field medium" name="start_date" id="user_from_date"> 
            
            </li>
            <li>   
            <label class="desc">End Date & Time:</label>
            <input size="20" class="text field medium" name="end_date" id="user_to_date">             
            </li>
            </fieldset>
          </ul>
           <br />
        <input type="submit" class="ui-state-default float-right ui-corner-all ui-button" name="action" value="Search" />
        <br><br>
         </form>
         
         
          <script>
	  $(document).ready(function() {
		$("#booth_from_date").datetimepicker({ dateFormat: 'yy-mm-dd' });		
		$("#booth_to_date").datetimepicker({ dateFormat: 'yy-mm-dd' });			
	  });
	  </script> 
         <form action="<?=base_url()?>callshops/booth_search" id="form23" name="form23" method="POST" enctype="multipart/form-data" style="display:none">
         <input type="hidden" name="advance_search" value="1">
         <ul style="width:675px; list-style:none;">
          <fieldset  style="width:660px;">
            <legend><span style="font-size:14px; font-weight:bold; color:#000;">Search Booth Report</span></legend>
              <li>
               <label class="desc">Booth:</label>
                <input size="20" class="text field medium" name="reseller" id="reseller">
                <a onclick="window.open('<?=base_url()?>callshops/callshop_booth_list/' , 'BoothList','scrollbars=1,width=650,height=330,top=20,left=100,scrollbars=1');" href="#"><img src="<?=base_url()?>images/icon_arrow_orange.gif" border="0"></a>
              </li>
              
               <li>
              <label class="desc">Destination:</label>
              <select class="select field medium" name="Destination" id="Destination">
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
              </li>
               <li>
              <label class="desc">IDD Code:</label>
              <select class="select field medium" name="Pattern" value="IDD Code" id="Pattern">
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
              </li>
            <li>   
            <label class="desc">Start Date & Time  :</label>
            <input size="20" class="text field medium" name="start_date" id="booth_from_date"> 
            
            </li>
            <li>   
            <label class="desc">End Date & Time:</label>
            <input size="20" class="text field medium" name="end_date" id="booth_to_date">             
            </li>
            </fieldset>
          </ul>
           <br />
        <input type="submit" class="ui-state-default float-right ui-corner-all ui-button" name="action" value="Search" />
        <br><br>
         </form>
         
          <form action="<?=base_url()?>callingcards/cards_search" id="form4" name="form4" method="POST" enctype="multipart/form-data" style="display:none">
         <input type="hidden" name="advance_search" value="1">
         <ul style="width:675px; list-style:none;">
          <fieldset  style="width:660px;">
            <legend><span style="font-size:14px; font-weight:bold; color:#000;">Search Calling Card</span></legend>
              <li>
               <label class="desc">Account Number:</label>
                <input size="20" class="text field medium" name="account_nummber" id="account_number">
                <a onclick="window.open('<?=base_url()?>accounts/search_callingcard_account_list/' , 'AccountList','scrollbars=1,width=650,height=330,top=20,left=100,scrollbars=1');" href="#"><img src="<?=base_url()?>images/icon_arrow_orange.gif" border="0"></a>
              </li>
              
               <li>
               <label class="desc">Card Number:</label>
                <input size="20" class="text field medium" name="card_nummber" id="card_number" />&nbsp;<select name="card_number_operator" style="padding:5px"><option value="1">contains</option><option value="2">doesn't contain</option><option value="3">is equal to</option><option value="4">is not equal to</option></select>
               
              </li>
              
               <li>
              <label class="desc">Brand:</label>
              <?=form_select_default('brand',$brands,"",array("class"=>"select field medium"))?>
              </li>
              
               <li>
               <label class="desc">Balance :</label>
               <input size="20" class="text field medium" name="balance"> &nbsp;<select name="balance_operator" style="padding:5px;width:132px;"><option value="1">is equal to</option><option value="2">is not equal to</option><option value="3">greater than</option><option value="4">less than</option><option value="5">greather or equal than</option><option value="6">less or equal than</option></select>
               </li> 
               
                <li>
               <label class="desc">Balance Used:</label>
               <input size="20" class="text field medium" name="balance_used"> &nbsp;<select name="balance_used_operator" style="padding:5px;width:132px;"><option value="1">is equal to</option><option value="2">is not equal to</option><option value="3">greater than</option><option value="4">less than</option><option value="5">greather or equal than</option><option value="6">less or equal than</option></select>
               </li> 
                <li>   
            <label class="desc">Creation Start Date:</label>
            <input size="20" class="text field medium" name="creation_start_date" id="creation_from_date"> 
            
            </li>
            <li>   
            <label class="desc">Creation End Date:</label>
            <input size="20" class="text field medium" name="creation_end_date" id="creation_to_date">             
            </li>
              <li>   
            <label class="desc">Used Start Date :</label>
            <input size="20" class="text field medium" name="first_used_start_date" id="first_from_date"> 
            
            </li>
            <li>   
            <label class="desc">Used End Date:</label>
            <input size="20" class="text field medium" name="first_used_end_date" id="first_to_date">             
            </li>
             <li>
          <label class="desc">In Use :</label>
          <select name="inuse" class="select field medium" >
            <option value="1" >Yes</option>
            <option value="0" >No</option>          
          </select>
        </li>
              
            </fieldset>
        </ul>
         <br />
        <input type="submit" class="ui-state-default float-right ui-corner-all ui-button" name="action" value="Search" />
        <br><br>
        </form>  
                 
         
        <form action="<?=base_url()?>callingcards/brands_search" id="form5" name="form5" method="POST" enctype="multipart/form-data" style="display:none">
         <input type="hidden" name="advance_search" value="1">
         <ul style="width:675px; list-style:none;">
          <fieldset  style="width:660px;">
            <legend><span style="font-size:14px; font-weight:bold; color:#000;">Search Calling Card Brand</span></legend>
            <li>   
            <label class="desc">CC Brand:</label>
            <input size="20" class="text field medium" name="cc_brand"> &nbsp;<select name="cc_brand_operator" style="padding:5px"><option value="1">contains</option><option value="2">doesn't contain</option><option value="3">is equal to</option><option value="4">is not equal to</option></select>
             </li>
              <li>
              <label class="desc">Pricelist :</label>
              <?=form_select_default('pricelist',@$pricelist,"",array("class"=>"select field medium"))?>
              </li>
              
              <li>
              <label class="desc">Days validate for:</label>
               <input size="20" class="text field medium" name="days_validate_for"> &nbsp;<select name="days_validate_for_operator" style="padding:5px"><option value="1">contains</option><option value="2">doesn't contain</option><option value="3">is equal to</option><option value="4">is not equal to</option></select>
              </li>
                <li>
                  <label class="desc">Status:</label>
                 <select class="select field medium" name="status">
                    <option value="1" >Active</option>
                    <option value="0" >Inactive</option>
                  </select>
                </li> 
            </fieldset>
            
         </ul>
          <br />
        <input type="submit" class="ui-state-default float-right ui-corner-all ui-button" name="action" value="Search" />
        <br><br>
         </form>
          <script>
	  $(document).ready(function() {
		$("#calling_card_cdr_from_date").datetimepicker({ dateFormat: 'yy-mm-dd' });		
		$("#calling_card_cdr_to_date").datetimepicker({ dateFormat: 'yy-mm-dd' });			
	  });
	  </script> 
          <form action="<?=base_url()?>callingcards/brands_cdrs_search" id="form6" method="POST" enctype="multipart/form-data" style="display:none">
         <input type="hidden" name="advance_search" value="1">
         <ul style="width:675px; list-style:none;">
          <fieldset  style="width:660px;">
            <legend><span style="font-size:14px; font-weight:bold; color:#000;">Search Calling Card CDR </span></legend>
             <li>   
            <label class="desc">Card Number:</label>
            <input size="20" class="text field medium" name="card_number"> &nbsp;<select name="card_number_operator" style="padding:5px"><option value="1">contains</option><option value="2">doesn't contain</option><option value="3">is equal to</option><option value="4">is not equal to</option></select>
             </li>
              <li>   
            <label class="desc">From date & Time :</label>
            <input size="20" class="text field medium" name="start_date" id="calling_card_cdr_from_date"> 
            
            </li>
             <li>   
            <label class="desc">To date & Time :</label>
            <input size="20" class="text field medium" name="end_date" id="calling_card_cdr_to_date">             
            </li>
             <li>   
            <label class="desc">Caller ID:</label>
            <input size="20" class="text field medium" name="caller_id"> &nbsp;<select name="caller_id_operator" style="padding:5px"><option value="1">contains</option><option value="2">doesn't contain</option><option value="3">is equal to</option><option value="4">is not equal to</option></select>
             </li>
              <li>   
            <label class="desc">Dest:</label>
            <input size="20" class="text field medium" name="dest"> &nbsp;<select name="dest_operator" style="padding:5px"><option value="1">contains</option><option value="2">doesn't contain</option><option value="3">is equal to</option><option value="4">is not equal to</option></select>
             </li>
               <li>
               <label class="desc">Bill Sec  :</label>
               <input size="20" class="text field medium" name="bill_sec"> &nbsp;<select name="bill_sec_operator" style="padding:5px;width:132px;"><option value="1">is equal to</option><option value="2">is not equal to</option><option value="3">greater than</option><option value="4">less than</option><option value="5">greather or equal than</option><option value="6">less or equal than</option></select>
               </li> 
               
               <li>
                  <label class="desc">Disposition :</label>
                  <?=form_disposition('disposition','NORMAL_CLEARING',array("class"=>"select field medium"))?>
                </li>
               
                <li>
               <label class="desc">Debit:</label>
               <input size="20" class="text field medium" name="debit"> &nbsp;<select name="debit_operator" style="padding:5px;width:132px;"><option value="1">is equal to</option><option value="2">is not equal to</option><option value="3">greater than</option><option value="4">less than</option><option value="5">greather or equal than</option><option value="6">less or equal than</option></select>
               </li> 
                <li>
               <label class="desc">Credit:</label>
               <input size="20" class="text field medium" name="credit"> &nbsp;<select name="credit_operator" style="padding:5px;width:132px;"><option value="1">is equal to</option><option value="2">is not equal to</option><option value="3">greater than</option><option value="4">less than</option><option value="5">greather or equal than</option><option value="6">less or equal than</option></select>
               </li> 
                <li>
              <label class="desc">Pricelist :</label>
              <?=form_select_default('pricelist',@$pricelist,"",array("class"=>"select field medium"))?>
              </li>
               
               <li>   
            <label class="desc">Pattern:</label>
            <input size="20" class="text field medium" name="pattern"> &nbsp;<select name="pattern_operator" style="padding:5px"><option value="1">contains</option><option value="2">doesn't contain</option><option value="3">is equal to</option><option value="4">is not equal to</option></select>
             </li>
             
          </fieldset>
         </ul>
         
          <br />
        <input type="submit" class="ui-state-default float-right ui-corner-all ui-button" name="action" value="Search" />
        <br><br>
        
        </form>    
         
         <form action="<?=base_url()?>did/did_search" id="form7" name="form7" method="POST" enctype="multipart/form-data" style="display:none">
         <input type="hidden" name="advance_search" value="1">
         <ul style="width:675px; list-style:none;">
          <fieldset  style="width:660px;">
            <legend><span style="font-size:14px; font-weight:bold; color:#000;">Search DID</span></legend>
              <li>   
              <label class="desc">Number:</label>
            <input size="20" class="text field medium" name="number"> &nbsp;<select name="number_operator" style="padding:5px"><option value="1">contains</option><option value="2">doesn't contain</option><option value="3">is equal to</option><option value="4">is not equal to</option></select>
              </li>
              <li>
              <label class="desc">Country:</label>
              <?=form_countries('country',"",array("class"=>"select field small"))?>
              </li>
             <li>
               <label class="desc">Provider:</label>
                <input size="20" class="text field medium" name="reseller" id="reseller">
                <a onclick="window.open('<?=base_url()?>accounts/search_did_provider_list/' , 'ProviderList','scrollbars=1,width=650,height=330,top=20,left=100,scrollbars=1');" href="#"><img src="<?=base_url()?>images/icon_arrow_orange.gif" border="0"></a>
              </li>
               <li>
               <label class="desc">Account Number:</label>
                <input size="20" class="text field medium" name="account_nummber" id="account_number">
                <a onclick="window.open('<?=base_url()?>accounts/search_did_account_list/' , 'AccountList','scrollbars=1,width=650,height=330,top=20,left=100,scrollbars=1');" href="#"><img src="<?=base_url()?>images/icon_arrow_orange.gif" border="0"></a>
              </li>
          </fieldset>
        </ul>
          <br />
        <input type="submit" class="ui-state-default float-right ui-corner-all ui-button" name="action" value="Search" />
        <br><br>
        </form>    
         
         <form action="<?=base_url()?>lcr/provider_search" id="form8" name="form8" method="POST" enctype="multipart/form-data" style="display:none">
         <input type="hidden" name="advance_search" value="1">
         <ul style="width:675px; list-style:none;">
          <fieldset  style="width:660px;">
            <legend><span style="font-size:14px; font-weight:bold; color:#000;">LCR - Providers</span></legend>
            <li>   
            <label class="desc">Provider name :</label>
            <input size="20" class="text field medium" name="provider_name"> &nbsp;<select name="provider_name_operator" style="padding:5px"><option value="1">contains</option><option value="2">doesn't contain</option><option value="3">is equal to</option><option value="4">is not equal to</option></select>
             </li>
               <li>
               <label class="desc">Firstname:</label>
               <input size="20" class="text field medium" name="first_name"> &nbsp;<select name="first_name_operator" style="padding:5px"><option value="1">contains</option><option value="2">doesn't contain</option><option value="3">is equal to</option><option value="4">is not equal to</option></select>
               </li> 
                <li>
               <label class="desc">Lastname:</label>
               <input size="20" class="text field medium" name="last_name"> &nbsp;<select name="last_name_operator" style="padding:5px"><option value="1">contains</option><option value="2">doesn't contain</option><option value="3">is equal to</option><option value="4">is not equal to</option></select>
               </li> 
                <li>
               <label class="desc">Company:</label>
               <input size="20" class="text field medium" name="company"> &nbsp;<select name="company_operator" style="padding:5px"><option value="1">contains</option><option value="2">doesn't contain</option><option value="3">is equal to</option><option value="4">is not equal to</option></select>
               </li> 
               
                 <li>
               <label class="desc">Balance:</label>
               <input size="20" class="text field medium" name="balance"> &nbsp;<select name="balance_operator" style="padding:5px;width:132px;"><option value="1">is equal to</option><option value="2">is not equal to</option><option value="3">greater than</option><option value="4">less than</option><option value="5">greather or equal than</option><option value="6">less or equal than</option></select>
               </li>
                 <li>
               <label class="desc">CreditLimit:</label>
               <input size="20" class="text field medium" name="creditlimit"> &nbsp;<select name="creditlimit_operator" style="padding:5px;width:132px;"><option value="1">is equal to</option><option value="2">is not equal to</option><option value="3">greater than</option><option value="4">less than</option><option value="5">greather or equal than</option><option value="6">less or equal than</option></select>
               </li>
            </fieldset>
            
         </ul>
          <br />
        <input type="submit" class="ui-state-default float-right ui-corner-all ui-button" name="action" value="Search" />
        <br><br>
         </form>
         
        <form action="<?=base_url()?>lcr/trunks_search" id="form9" name="form9" method="POST" enctype="multipart/form-data" style="display:none">
         <input type="hidden" name="advance_search" value="1">
         <ul style="width:675px; list-style:none;">
          <fieldset  style="width:660px;">
            <legend><span style="font-size:14px; font-weight:bold; color:#000;">Search LCR-Trunks</span></legend>
             <li>   
            <label class="desc">Trunk Name :</label>
            <input size="20" class="text field medium" name="trunk_name"> &nbsp;<select name="trunk_name_operator" style="padding:5px"><option value="1">contains</option><option value="2">doesn't contain</option><option value="3">is equal to</option><option value="4">is not equal to</option></select>
             </li>
             <li>
            <label class="desc">Protocol:</label>
            <select name="tech"  class="select field medium">
            <option value="SIP"  >SIP</option>
            <option value="IAX2" >IAX2</option>
            <option value="Zap"  >Zap</option>
            <option value="Local" >Local</option>
            <option value="OH323" >OH323</option>
            <option value="OOH323C" >OOH323C</option>
            </select>
            </li>
            <li>
               <label class="desc">Provider:</label>
               <input size="20" class="text field medium" name="provider" id="provider">
               <a onclick="window.open('<?=base_url()?>accounts/search_trunks_provider_list/' , 'ProviderList','scrollbars=1,width=650,height=330,top=20,left=100,scrollbars=1');" href="#"><img src="<?=base_url()?>images/icon_arrow_orange.gif" border="0"></a>
            </li>
            <li>
               <label class="desc">Reseller:</label>
               <input size="20" class="text field medium" name="reseller" id="reseller">
               <a onclick="window.open('<?=base_url()?>accounts/search_trunks_reseller_list/' , 'ResellerList','scrollbars=1,width=650,height=330,top=20,left=100,scrollbars=1');" href="#"><img src="<?=base_url()?>images/icon_arrow_orange.gif" border="0"></a>
            </li>
              
          </fieldset>
         </ul>
           <br />
            <input type="submit" class="ui-state-default float-right ui-corner-all ui-button" name="action" value="Search" />
            <br><br>  
        </form> 
        
         <form action="<?=base_url()?>lcr/outbound_search" id="form10" name="form10" method="POST" enctype="multipart/form-data" style="display:none">
         <input type="hidden" name="advance_search" value="1">
         <ul style="width:675px; list-style:none;">
          <fieldset  style="width:660px;">
            <legend><span style="font-size:14px; font-weight:bold; color:#000;">Search Outbound Routes </span></legend>
             <li>   
            <label class="desc">Pattern:</label>
            <input size="20" class="text field medium" name="pattern"> &nbsp;<select name="pattern_operator" style="padding:5px"><option value="1">contains</option><option value="2">doesn't contain</option><option value="3">is equal to</option><option value="4">is not equal to</option></select>
             </li>
              <li>   
            <label class="desc">Prepend:</label>
            <input size="20" class="text field medium" name="prepend"> &nbsp;<select name="prepend_operator" style="padding:5px"><option value="1">contains</option><option value="2">doesn't contain</option><option value="3">is equal to</option><option value="4">is not equal to</option></select>
             </li>
             <li>   
            <label class="desc">Comment:</label>
            <input size="20" class="text field medium" name="comment"> &nbsp;<select name="comment_operator" style="padding:5px"><option value="1">contains</option><option value="2">doesn't contain</option><option value="3">is equal to</option><option value="4">is not equal to</option></select>
             </li>
              <li>
              <label class="desc">Trunk</label>
              <select class="select field medium" name="trunk" >
              <?=$trunks?>
              </select>
              </li>
               <li>
               <label class="desc">Increment :</label>
               <input size="20" class="text field medium" name="increment"> &nbsp;<select name="increment_operator" style="padding:5px;width:132px;"><option value="1">is equal to</option><option value="2">is not equal to</option><option value="3">greater than</option><option value="4">less than</option><option value="5">greather or equal than</option><option value="6">less or equal than</option></select>
               </li> 
                <li>
               <label class="desc">Connect Charge :</label>
               <input size="20" class="text field medium" name="connect_charge"> &nbsp;<select name="connect_charge_operator" style="padding:5px;width:132px;"><option value="1">is equal to</option><option value="2">is not equal to</option><option value="3">greater than</option><option value="4">less than</option><option value="5">greather or equal than</option><option value="6">less or equal than</option></select>
               </li> 
                <li>
               <label class="desc">Included Seconds:</label>
               <input size="20" class="text field medium" name="included_seconds"> &nbsp;<select name="included_seconds_operator" style="padding:5px;width:132px;"><option value="1">is equal to</option><option value="2">is not equal to</option><option value="3">greater than</option><option value="4">less than</option><option value="5">greather or equal than</option><option value="6">less or equal than</option></select>
               </li> 
              
                <li>
               <label class="desc">Cost per add. Minutes:</label>
               <input size="20" class="text field medium" name="cost_per_add_minutes"> &nbsp;<select name="cost_per_add_minutes_operator" style="padding:5px;width:132px;"><option value="1">is equal to</option><option value="2">is not equal to</option><option value="3">greater than</option><option value="4">less than</option><option value="5">greather or equal than</option><option value="6">less or equal than</option></select>
               </li>
                <li>
               <label class="desc">Reseller:</label>
               <input size="20" class="text field medium" name="reseller" id="reseller">
               <a onclick="window.open('<?=base_url()?>accounts/search_outbound_reseller_list/' , 'ResellerList','scrollbars=1,width=650,height=330,top=20,left=100,scrollbars=1');" href="#"><img src="<?=base_url()?>images/icon_arrow_orange.gif" border="0"></a>
            </li>
         </fieldset> 
            <br />
            <input type="submit" class="ui-state-default float-right ui-corner-all ui-button" name="action" value="Search" />
            <br><br>  
          </ul>
         </form>   
         
         <form action="<?=base_url()?>rates/pricelist_search" id="search_form11" name="form11" method="POST" enctype="multipart/form-data" style="display:none">
         <input type="hidden" name="advance_search" value="1">
         <ul style="width:675px; list-style:none;">
          <fieldset  style="width:660px;">
            <legend><span style="font-size:14px; font-weight:bold; color:#000;">Search Price List</span></legend>
            <li>   
            <label class="desc">Pricelist name  :</label>
            <input size="20" class="text field medium" name="pricelist_name"> &nbsp;<select name="pricelist_name_operator" style="padding:5px"><option value="1">contains</option><option value="2">doesn't contain</option><option value="3">is equal to</option><option value="4">is not equal to</option></select>
             </li>
               <li>
               <label class="desc">Default increment :</label>
               <input size="20" class="text field medium" name="default_increment"> &nbsp;<select name="default_increment_operator" style="padding:5px;width:132px;"><option value="1">is equal to</option><option value="2">is not equal to</option><option value="3">greater than</option><option value="4">less than</option><option value="5">greather or equal than</option><option value="6">less or equal than</option></select>
               </li>    
                 
            </fieldset>
            
         </ul>
          <br />
        <input type="submit" class="ui-state-default float-right ui-corner-all ui-button" name="action" value="Search" />
        <br><br>
         </form>
         
         <form action="<?=base_url()?>rates/routes_search" id="form12" name="form12" method="POST" enctype="multipart/form-data" style="display:none">
         <input type="hidden" name="advance_search" value="1">
         <ul style="width:675px; list-style:none;">
          <fieldset  style="width:660px;">
            <legend><span style="font-size:14px; font-weight:bold; color:#000;">Search Routes </span></legend>
             <li>   
            <label class="desc">Pattern:</label>
            <input size="20" class="text field medium" name="pattern"> &nbsp;<select name="pattern_operator" style="padding:5px"><option value="1">contains</option><option value="2">doesn't contain</option><option value="3">is equal to</option><option value="4">is not equal to</option></select>
             </li>
             
             <li>   
            <label class="desc">Comment:</label>
            <input size="20" class="text field medium" name="comment"> &nbsp;<select name="comment_operator" style="padding:5px"><option value="1">contains</option><option value="2">doesn't contain</option><option value="3">is equal to</option><option value="4">is not equal to</option></select>
             </li>
             
               <li>
               <label class="desc">Increment :</label>
               <input size="20" class="text field medium" name="increment"> &nbsp;<select name="increment_operator" style="padding:5px;width:132px;"><option value="1">is equal to</option><option value="2">is not equal to</option><option value="3">greater than</option><option value="4">less than</option><option value="5">greather or equal than</option><option value="6">less or equal than</option></select>
               </li> 
                <li>
               <label class="desc">Connect Charge :</label>
               <input size="20" class="text field medium" name="connect_charge"> &nbsp;<select name="connect_charge_operator" style="padding:5px;width:132px;"><option value="1">is equal to</option><option value="2">is not equal to</option><option value="3">greater than</option><option value="4">less than</option><option value="5">greather or equal than</option><option value="6">less or equal than</option></select>
               </li> 
                <li>
               <label class="desc">Included Seconds:</label>
               <input size="20" class="text field medium" name="included_seconds"> &nbsp;<select name="included_seconds_operator" style="padding:5px;width:132px;"><option value="1">is equal to</option><option value="2">is not equal to</option><option value="3">greater than</option><option value="4">less than</option><option value="5">greather or equal than</option><option value="6">less or equal than</option></select>
               </li> 
              
                <li>
               <label class="desc">Cost per add. Minutes:</label>
               <input size="20" class="text field medium" name="cost_per_add_minutes"> &nbsp;<select name="cost_per_add_minutes_operator" style="padding:5px;width:132px;"><option value="1">is equal to</option><option value="2">is not equal to</option><option value="3">greater than</option><option value="4">less than</option><option value="5">greather or equal than</option><option value="6">less or equal than</option></select>
               </li>
               
         </fieldset> 
            <br />
            <input type="submit" class="ui-state-default float-right ui-corner-all ui-button" name="action" value="Search" />
            <br><br>  
          </ul>
         </form>
         
         
          <form action="<?=base_url()?>rates/periodiccharges_search" id="form13" name="form13" method="POST" enctype="multipart/form-data" style="display:none">
         <input type="hidden" name="advance_search" value="1">
         <ul style="width:675px; list-style:none;">
          <fieldset  style="width:660px;">
            <legend><span style="font-size:14px; font-weight:bold; color:#000;">Periodic Charges</span></legend>
            <li>   
            <label class="desc">Description:</label>
            <input size="20" class="text field medium" name="description"> &nbsp;<select name="description_operator" style="padding:5px;width:132px;"><option value="1">is equal to</option><option value="2">is not equal to</option><option value="3">greater than</option><option value="4">less than</option><option value="5">greather or equal than</option><option value="6">less or equal than</option></select>
             </li>
               <li>
                  <label class="desc">Pricelist :</label>
                 <?=form_select_default('pricelist',@$pricelist,"",array("class"=>"select field medium"))?>
                </li>
              <li>   
            <label class="desc">Charge:</label>
            <input size="20" class="text field medium" name="charge"> &nbsp;<select name="charge_operator" style="padding:5px"><option value="1">contains</option><option value="2">doesn't contain</option><option value="3">is equal to</option><option value="4">is not equal to</option></select>
             </li> 
              <li>
                  <label class="desc">Cycle :</label>
                  <?=form_select_default('sweep',$sweeplist,"",array("class"=>"select field medium"))?>
                </li> 
              <li>
              <label class="desc">Status:</label>
              <select class="select field medium" name="status">
              <option value="1" >Active</option>
              <option value="0" >Inactive</option>
              </select>
              </li>   
                 
            </fieldset>
            
         </ul>
          <br />
        <input type="submit" class="ui-state-default float-right ui-corner-all ui-button" name="action" value="Search" />
        <br><br>
         </form>
         
         
         <form action="<?=base_url()?>rates/packages_search" id="form14" name="form14" method="POST" enctype="multipart/form-data" style="display:none">
         <input type="hidden" name="advance_search" value="1">
         <ul style="width:675px; list-style:none;">
          <fieldset  style="width:660px;">
            <legend><span style="font-size:14px; font-weight:bold; color:#000;">Packages</span></legend>
            <li>   
            <label class="desc">Package Name:</label>
            <input size="20" class="text field medium" name="package_name"> &nbsp;<select name="package_name_operator" style="padding:5px"><option value="1">contains</option><option value="2">doesn't contain</option><option value="3">is equal to</option><option value="4">is not equal to</option></select>
             </li>
               <li>
                  <label class="desc">Pricelist :</label>
                 <?=form_select_default('pricelist',@$pricelist,"",array("class"=>"select field medium"))?>
                </li>
             <li>   
            <label class="desc">Pattern:</label>
            <input size="20" class="text field medium" name="pattern"> &nbsp;<select name="pattern_operator" style="padding:5px"><option value="1">contains</option><option value="2">doesn't contain</option><option value="3">is equal to</option><option value="4">is not equal to</option></select>
             </li>
              <li>   
            <label class="desc">Included Seconds:</label>
            <input size="20" class="text field medium" name="included_seconds"> &nbsp;<select name="included_seconds_operator" style="padding:5px;width:132px;"><option value="1">is equal to</option><option value="2">is not equal to</option><option value="3">greater than</option><option value="4">less than</option><option value="5">greather or equal than</option><option value="6">less or equal than</option></select>
             </li>
                            
            </fieldset>
            
         </ul>
          <br />
        <input type="submit" class="ui-state-default float-right ui-corner-all ui-button" name="action" value="Search" />
        <br><br>
         </form>
         
            <form action="<?=base_url()?>rates/counters_search" id="form15" name="form15" method="POST" enctype="multipart/form-data" style="display:none">
         <input type="hidden" name="advance_search" value="1">
         <ul style="width:675px; list-style:none;">
          <fieldset  style="width:660px;">
            <legend><span style="font-size:14px; font-weight:bold; color:#000;">Search Counters</span></legend>
             <li>   
            <label class="desc">Package Name:</label> 
            <select name="packages">
            <?php 
			foreach($packages as $key => $value){
			?>
            <option value="<?=$value?>"><?=$value?></option>
            <?				
			}
			?>
            </select>          
             </li>
             <li>
               <label class="desc">Account Number:</label>
                <input size="20" class="text field medium" name="account_nummber" id="account_number">
                <a onclick="window.open('<?=base_url()?>accounts/search_counters_account_list/' , 'AccountList','scrollbars=1,width=650,height=330,top=20,left=100,scrollbars=1');" href="#"><img src="<?=base_url()?>images/icon_arrow_orange.gif" border="0"></a>
              </li>
               <li>   
            <label class="desc">Seconds Used :</label>
            <input size="20" class="text field medium" name="seconds_used"> &nbsp;<select name="seconds_used_operator" style="padding:5px;width:132px;"><option value="1">is equal to</option><option value="2">is not equal to</option><option value="3">greater than</option><option value="4">less than</option><option value="5">greather or equal than</option><option value="6">less or equal than</option></select>
             </li>
          </fieldset>
        </ul>
          <br />
        <input type="submit" class="ui-state-default float-right ui-corner-all ui-button" name="action" value="Search" />
        <br><br>
        </form>    
         <script>
	  $(document).ready(function() {
		$("#error_from_date").datetimepicker({ dateFormat: 'yy-mm-dd' });		
		$("#error_to_date").datetimepicker({ dateFormat: 'yy-mm-dd' });			
	  });
	  </script> 
         <form action="<?=base_url()?>statistics/error_search" id="form16" name="form16" method="POST" enctype="multipart/form-data" style="display:none">
         <input type="hidden" name="advance_search" value="1">
         <ul style="width:675px; list-style:none;">
          <fieldset  style="width:660px;">
            <legend><span style="font-size:14px; font-weight:bold; color:#000;">Search List Errors</span></legend>
             <li>   
            <label class="desc">Start Date:</label>
            <input size="20" class="text field medium" name="start_date" id="error_from_date"> 
            
            </li>
             <li>   
            <label class="desc">End Date:</label>
            <input size="20" class="text field medium" name="end_date" id="error_to_date">             
            </li>
              <li>   
            <label class="desc">Source :</label>
            <input size="20" class="text field medium" name="source"> &nbsp;<select name="source_operator" style="padding:5px"><option value="1">contains</option><option value="2">doesn't contain</option><option value="3">is equal to</option><option value="4">is not equal to</option></select>
             </li>
             
               <li>   
            <label class="desc">Dst :</label>
            <input size="20" class="text field medium" name="dst"> &nbsp;<select name="dst_operator" style="padding:5px"><option value="1">contains</option><option value="2">doesn't contain</option><option value="3">is equal to</option><option value="4">is not equal to</option></select>
             </li>
              <li>   
            <label class="desc">Duration :</label>
            <input size="20" class="text field medium" name="duration"> &nbsp;<select name="duration_operator" style="padding:5px;width:132px;"><option value="1">is equal to</option><option value="2">is not equal to</option><option value="3">greater than</option><option value="4">less than</option><option value="5">greather or equal than</option><option value="6">less or equal than</option></select>
             </li>
               <li>   
            <label class="desc">Bill Sec :</label>
            <input size="20" class="text field medium" name="bill_sec"> &nbsp;<select name="bill_sec_operator" style="padding:5px;width:132px;"><option value="1">is equal to</option><option value="2">is not equal to</option><option value="3">greater than</option><option value="4">less than</option><option value="5">greather or equal than</option><option value="6">less or equal than</option></select>
             </li>
            <li>
                  <label class="desc">Disposition :</label>
                  <?=form_disposition('disposition','NORMAL_CLEARING',array("class"=>"select field medium"))?>
                </li>
            <li>   
            <label class="desc">Cost:</label>
            <input size="20" class="text field medium" name="cost"> &nbsp;<select name="cost_operator" style="padding:5px;width:132px;"><option value="1">is equal to</option><option value="2">is not equal to</option><option value="3">greater than</option><option value="4">less than</option><option value="5">greather or equal than</option><option value="6">less or equal than</option></select>
             </li>
             
          </fieldset>
         </ul>
           <br />
        <input type="submit" class="ui-state-default float-right ui-corner-all ui-button" name="action" value="Search" />
        <br><br>
        </form>     
          <script>
	  $(document).ready(function() {
		$("#trunk_stat_from_date").datetimepicker({ dateFormat: 'yy-mm-dd' });		
		$("#trunk_stat_to_date").datetimepicker({ dateFormat: 'yy-mm-dd' });			
	  });
	  </script> 
      <form action="<?=base_url()?>statistics/trunkstats_search" id="form17" name="form17" method="POST" enctype="multipart/form-data" style="display:none">
         <input type="hidden" name="advance_search" value="1">
         <ul style="width:675px; list-style:none;">
          <fieldset  style="width:660px;">
            <legend><span style="font-size:14px; font-weight:bold; color:#000;">Search Trunk Stats</span></legend>
             <li>
              <label class="desc">Trunk</label>
              <select class="select field medium" name="trunk" >
              <?=$trunks?>
              </select>
              </li>
               <li>   
            <label class="desc">Start Date:</label>
            <input size="20" class="text field medium" name="start_date" id="trunk_stat_from_date"> 
            
            </li>
             <li>   
            <label class="desc">End Date:</label>
            <input size="20" class="text field medium" name="end_date" id="trunk_stat_to_date">             
            </li>
         </fieldset>
        </ul>
          <br />
        <input type="submit" class="ui-state-default float-right ui-corner-all ui-button" name="action" value="Search" />
        <br><br>
       </form>     
          <script>
	  $(document).ready(function() {
		$("#view_free_from_date").datetimepicker({ dateFormat: 'yy-mm-dd' });		
		$("#view_free_to_date").datetimepicker({ dateFormat: 'yy-mm-dd' });			
	  });
	  </script> 
         <form action="<?=base_url()?>statistics/fscdrs_search" id="form18" name="form18" method="POST" enctype="multipart/form-data" style="display:none">
         <input type="hidden" name="advance_search" value="1">
         <ul style="width:675px; list-style:none;">
          <fieldset  style="width:660px;">
            <legend><span style="font-size:14px; font-weight:bold; color:#000;">Search View Freeswitch CDRs</span></legend>      
             <li>   
            <label class="desc">Start Date:</label>
            <input size="20" class="text field medium" name="start_date" id="view_free_from_date">             
            </li>
             <li>   
            <label class="desc">End Date:</label>
            <input size="20" class="text field medium" name="end_date" id="view_free_to_date">             
            </li>
            <li>   
            <label class="desc">Source :</label>
            <input size="20" class="text field medium" name="source"> &nbsp;<select name="source_operator" style="padding:5px"><option value="1">contains</option><option value="2">doesn't contain</option><option value="3">is equal to</option><option value="4">is not equal to</option></select>
             </li>
               <li>   
            <label class="desc">Dst :</label>
            <input size="20" class="text field medium" name="dst"> &nbsp;<select name="dst_operator" style="padding:5px"><option value="1">contains</option><option value="2">doesn't contain</option><option value="3">is equal to</option><option value="4">is not equal to</option></select>
             </li>
              <li>   
            <label class="desc">Duration :</label>
            <input size="20" class="text field medium" name="duration"> &nbsp;<select name="duration_operator" style="padding:5px;width:132px;"><option value="1">is equal to</option><option value="2">is not equal to</option><option value="3">greater than</option><option value="4">less than</option><option value="5">greather or equal than</option><option value="6">less or equal than</option></select>
             </li>
               <li>   
            <label class="desc">Bill Sec :</label>
            <input size="20" class="text field medium" name="bill_sec"> &nbsp;<select name="bill_sec_operator" style="padding:5px;width:132px;"><option value="1">is equal to</option><option value="2">is not equal to</option><option value="3">greater than</option><option value="4">less than</option><option value="5">greather or equal than</option><option value="6">less or equal than</option></select>
             </li>
            <li>
                  <label class="desc">Disposition :</label>
                  <?=form_disposition('disposition','NORMAL_CLEARING',array("class"=>"select field medium"))?>
                </li>
            <li>   
            <label class="desc">Cost:</label>
            <input size="20" class="text field medium" name="cost"> &nbsp;<select name="cost_operator" style="padding:5px;width:132px;"><option value="1">is equal to</option><option value="2">is not equal to</option><option value="3">greater than</option><option value="4">less than</option><option value="5">greather or equal than</option><option value="6">less or equal than</option></select>
             </li>
              <li>
               <label class="desc">Account Number:</label>
                <input size="20" class="text field medium" name="account_nummber" id="account_number">
                <a onclick="window.open('<?=base_url()?>accounts/search_fscdrs_account_list/' , 'AccountList','scrollbars=1,width=650,height=330,top=20,left=100,scrollbars=1');" href="#"><img src="<?=base_url()?>images/icon_arrow_orange.gif" border="0"></a>
              </li>
             
          </fieldset>
        </ul>
          <br />
        <input type="submit" class="ui-state-default float-right ui-corner-all ui-button" name="action" value="Search" />
        <br><br>
       </form>    
       
       
         <form action="<?=base_url()?>switchconfig/fssipdevices_search" id="form19" name="form19" method="POST" enctype="multipart/form-data" style="display:none">
         <input type="hidden" name="advance_search" value="1">
         <ul style="width:675px; list-style:none;">
          <fieldset  style="width:660px;">
            <legend><span style="font-size:14px; font-weight:bold; color:#000;">Search Freeswitch SIP Devices </span></legend>      
             <li>   
            <label class="desc">Username:</label>
            <input size="20" class="text field medium" name="username"> &nbsp;<select name="username_operator" style="padding:5px"><option value="1">contains</option><option value="2">doesn't contain</option><option value="3">is equal to</option><option value="4">is not equal to</option></select>
             </li>
             <li>
               <label class="desc">Account Number:</label>
                <input size="20" class="text field medium" name="account_nummber" id="account_number">
                <a onclick="window.open('<?=base_url()?>accounts/search_fssipdevices_account_list/' , 'AccountList','scrollbars=1,width=650,height=330,top=20,left=100,scrollbars=1');" href="#"><img src="<?=base_url()?>images/icon_arrow_orange.gif" border="0"></a>
              </li>
             
         </fieldset>
         </ul>
           <br />
        <input type="submit" class="ui-state-default float-right ui-corner-all ui-button" name="action" value="Search" />
        <br><br>
        </form>       
         
         <form action="<?=base_url()?>systems/configuration_search" id="form20" name="form20" method="POST" enctype="multipart/form-data" style="display:none">
         <input type="hidden" name="advance_search" value="1">
         <ul style="width:675px; list-style:none;">
          <fieldset  style="width:660px;">
            <legend><span style="font-size:14px; font-weight:bold; color:#000;">Search System Configuration </span></legend>
              <li>
               <label class="desc">Reseller:</label>
               <input size="20" class="text field medium" name="reseller" id="reseller">
               <a onclick="window.open('<?=base_url()?>accounts/search_configuration_reseller_list/' , 'ResellerList','scrollbars=1,width=650,height=330,top=20,left=100,scrollbars=1');" href="#"><img src="<?=base_url()?>images/icon_arrow_orange.gif" border="0"></a>
            </li>
              <li>
              <label class="desc">Brand:</label>
              <?=form_select_default('brand',$brands,"",array("class"=>"select field medium"))?>
              </li>
                <li>   
            <label class="desc">Name :</label>
            <input size="20" class="text field medium" name="name"> &nbsp;<select name="name_operator" style="padding:5px"><option value="1">contains</option><option value="2">doesn't contain</option><option value="3">is equal to</option><option value="4">is not equal to</option></select>
             </li>
             
              <li>   
            <label class="desc">Value  :</label>
            <input size="20" class="text field medium" name="value"> &nbsp;<select name="value_operator" style="padding:5px"><option value="1">contains</option><option value="2">doesn't contain</option><option value="3">is equal to</option><option value="4">is not equal to</option></select>
             </li>
             
              <li>   
            <label class="desc">Comments:</label>
            <input size="20" class="text field medium" name="comment"> &nbsp;<select name="comment_operator" style="padding:5px"><option value="1">contains</option><option value="2">doesn't contain</option><option value="3">is equal to</option><option value="4">is not equal to</option></select>
             </li>
             
          </fieldset>
         </ul>
        <br />
        <input type="submit" class="ui-state-default float-right ui-corner-all ui-button" name="action" value="Search" />
        <br><br>        
        </form>    
         
         <form action="<?=base_url()?>systems/taxes_search" id="form21" method="POST" enctype="multipart/form-data" style="display:none">
         <input type="hidden" name="advance_search" value="1">
         <ul style="width:675px; list-style:none;">
          <fieldset  style="width:660px;">
            <legend><span style="font-size:14px; font-weight:bold; color:#000;">Taxes</span></legend>
            <li>   
            <label class="desc">Amount :</label>
            <input size="20" class="text field medium" name="amount"> &nbsp;<select name="amount_operator" style="padding:5px;width:132px;"><option value="1">is equal to</option><option value="2">is not equal to</option><option value="3">greater than</option><option value="4">less than</option><option value="5">greather or equal than</option><option value="6">less or equal than</option></select>
             </li>
              
             <li>   
            <label class="desc">Rate :</label>
            <input size="20" class="text field medium" name="rate"> &nbsp;<select name="rate_operator" style="padding:5px;width:132px;"><option value="1">is equal to</option><option value="2">is not equal to</option><option value="3">greater than</option><option value="4">less than</option><option value="5">greather or equal than</option><option value="6">less or equal than</option></select>
             </li>
              <li>   
            <label class="desc">Description :</label>
            <input size="20" class="text field medium" name="description"> &nbsp;<select name="description_operator" style="padding:5px"><option value="1">contains</option><option value="2">doesn't contain</option><option value="3">is equal to</option><option value="4">is not equal to</option></select>
             </li>
                            
            </fieldset>
            
         </ul>
          <br />
        <input type="submit" class="ui-state-default float-right ui-corner-all ui-button" name="action" value="Search" />
        <br><br>
         </form>

         <form action="<?=base_url()?>accounting/search" id="form24" method="POST" enctype="multipart/form-data" style="display:none">
         <input type="hidden" name="advance_search" value="1">
         <ul style="width:675px; list-style:none;">
          <fieldset  style="width:660px;">

                    <legend><span style="font-size:14px; font-weight:bold; color:#000;">Search Account Invoices</span></legend>
                    <li>  
                        <label class="desc">Account Number:</label>
                        <input size="20" class="text field medium" name="account_number"/> &nbsp;                  
                        <a onclick="window.open('<?=base_url()?>accounts/search_callingcard_account_list/' , 'AccountList','scrollbars=1,width=650,height=330,top=20,left=100,scrollbars=1');" href="#"><img src="<?=base_url()?>images/icon_arrow_orange.gif" border="0"></a>                  
                    </li>
                    <li>
                       <label class="desc"> Invoice Date:</label>
                        <input size="20" class="text field medium" name="invoice_date" id="from_date"/> 
                    </li>
                    <li>
                    <label class="desc">Invoice Total:</label>
                       <input size="20" class="text field medium" name="creditlimit"/> &nbsp;
                       <select name="creditlimit_operator"  style="padding:5px;width:132px;" >
                       <option value="1">is equal to</option>
                       <option value="2">is not equal to</option>
                       <option value="3">greater than</option>
                       <option value="4">less than</option>
                       <option value="5">greather or equal than</option>
                       <option value="6">less or equal than</option>
                       </select>
                    </li>
          </fieldset>
                    </ul>
                    <br />
                    <input type="submit" class="ui-state-default float-right ui-corner-all ui-button" name="action" value="Search" />
                    <br><br>
          </form>
          
        <form action="<?=base_url()?>accounting/search_taxes" id="form25" method="POST" enctype="multipart/form-data" style="display:none">
         <input type="hidden" name="advance_search" value="1">
         <ul style="width:675px; list-style:none;">
          <fieldset  style="width:660px;">

                    <legend><span style="font-size:14px; font-weight:bold; color:#000;">Search Account Taxes</span></legend>
                    <li>  
                             <label class="desc">Taxes Priority:</label>
                               <input size="20"  class="text field medium" name="taxes_priority"/> 
                               <select name="taxes_priority_operator"  style="padding:5px;width:132px;" >
                               <option value="1">is equal to</option>
                               <option value="2">is not equal to</option>
                               <option value="3">greater than</option>
                               <option value="4">less than</option>
                               <option value="5">greather or equal than</option>
                               <option value="6">less or equal than</option>
                               </select>
                    </li>
                    <li>
                                <label class="desc"> Account Number:</label>
                                <input size="20" class="text field" name="account_number" id="from_date"/> 
                                <a onclick="window.open('<?=base_url()?>accounts/search_callingcard_account_list/' , 'AccountList','scrollbars=1,width=650,height=330,top=20,left=100,scrollbars=1');" href="#"><img src="<?=base_url()?>images/icon_arrow_orange.gif" border="0"></a>                   
                    </li>
                    <li>
                               <label class="desc">Taxes Rate:</label>
                               <input size="20" class="text field medium" name="taxes_rate"/> &nbsp;
                               <select name="taxes_rate_operator"  style="padding:5px;width:132px;" >
                               <option value="1">is equal to</option>
                               <option value="2">is not equal to</option>
                               <option value="3">greater than</option>
                               <option value="4">less than</option>
                               <option value="5">greather or equal than</option>
                               <option value="6">less or equal than</option>
                               </select>
                    </li>
                    <li>
                               <label class="desc">Taxes Amount:</label>
                               <input size="20" class="text field medium" name="taxes_amount"/> &nbsp;
                               <select name="taxes_amount_operator"  style="padding:5px;width:132px;" >
                               <option value="1">is equal to</option>
                               <option value="2">is not equal to</option>
                               <option value="3">greater than</option>
                               <option value="4">less than</option>
                               <option value="5">greather or equal than</option>
                               <option value="6">less or equal than</option>
                               </select>
                    </li>
                    <li>
                                <label class="desc"> Description:</label>
                               <input size="20" name="taxes_contain" id="Description"  class="text field medium"/> 
                               <select name="taxes_contain_operator" style="padding:5px;width:132px;" >
                               <option value="1" >Contains</option>
                               <option value="2">Does not contains</option>
                               <option value="3">Is equal to</option>
                               <option value="4">Is not equal to</option>
                               </select>
                    </li>
                    </fieldset>
                    </ul>
                    <br />
                    <input type="submit" class="ui-state-default float-right ui-corner-all ui-button" name="action" value="Search" />
                    <br><br>
            </form>

	       <form action="<?=base_url()?>systems/search" id="form26" method="POST" enctype="multipart/form-data" style="display:none">
         <input type="hidden" name="advance_search" value="1">
         <ul style="width:675px; list-style:none;">
          <fieldset  style="width:660px;">
<!--              
              <legend><span style="font-size:14px; font-weight:bold; color:#000;">Template</span></legend>
        <input size="20" class="text field medium" name="template_name" value="<?=@$template_search['template_name']?>" />  
                  <select name="template_name_operator" class="field select">
                  <option value="1" <?php if(@$template_search['template_name_operator']==1) { echo "selected";}?> >contains</option>
                  <option value="2" <?php if(@$template_search['template_name_operator']==2) { echo "selected";}?>>doesn't contain</option>
                  <option value="3" <?php if(@$template_search['template_name_operator']==3) { echo "selected";}?>>is equal to</option>
                  <option value="4" <?php if(@$template_search['template_name_operator']==4) { echo "selected";}?>>is not equal to</option>
                  </select>
					</span>
		</div> 
              -->
             
              
              
              
            <legend><span style="font-size:14px; font-weight:bold; color:#000;">Template</span></legend>
            <li>   
            <label class="desc">Template Name :</label>
            <input size="20" class="text field medium" name="template_name" value="<?=@$template_search['template_name']?>"> &nbsp;<select name="template_name_operator" style="padding:5px"><option value="1">contains</option><option value="2">doesn't contain</option><option value="3">is equal to</option><option value="4">is not equal to</option></select>
             </li>
              
             <li>   
            <label class="desc">Subject :</label>
            <input size="20" class="text field medium" name="subject" value="<?=@$template_search['subject']?>"> &nbsp;<select name="subject_operator" style="padding:5px"><option value="1">contains</option><option value="2">doesn't contain</option><option value="3">is equal to</option><option value="4">is not equal to</option></select>
             </li>
              <li>   
            <label class="desc">Template :</label>
            <input size="20" class="text field medium" name="template_desc" value="<?=@$template_search['template_desc']?>"> &nbsp;<select name="template_operator" style="padding:5px"><option value="1">contains</option><option value="2">doesn't contain</option><option value="3">is equal to</option><option value="4">is not equal to</option></select>
             
              </li>
              <li>   
            <label class="desc">Account Number :</label>
            <input size="20" class="text field medium" name="template_desc"> 
             <a onclick="window.open('<?=base_url()?>accounts/search_callingcard_account_list/' , 'AccountList','scrollbars=1,width=650,height=330,top=20,left=100,scrollbars=1');" href="#"><img src="<?=base_url()?>images/icon_arrow_orange.gif" border="0"></a>
             
              </li>
                            
            </fieldset>
             
            
         </ul>
          <br />
        <input type="submit" class="ui-state-default float-right ui-corner-all ui-button" name="action" value="Search" />
        <br><br>
         </form>
          
       


            </div>
</div>
