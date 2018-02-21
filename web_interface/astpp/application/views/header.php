<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>ASTPP - Open Source Voip Billing Solution</title>
    <script language="javascript" type="text/javascript">
		var base_url = '<?php echo base_url();?>';
    </script>
	<script type="text/javascript" src="<?php echo base_url();?>js/jquery-1.7.1.js"></script>
	<!--<script type="text/javascript" src="<?php echo base_url();?>js/jquery-1.6.2.js"></script>-->
	<script type="text/javascript" src="<?php echo base_url();?>js/ui/ui.core.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>js/ui/ui.datepicker.js"></script>
	<script type="text/javascript" src="<?php echo base_url();?>js/ui/ui.widget.js"></script>
	<script type="text/javascript" src="<?php echo base_url();?>js/ui/ui.mouse.js"></script>
	<script type="text/javascript" src="<?php echo base_url();?>js/superfish.js"></script>
	<script type="text/javascript" src="<?php echo base_url();?>js/live_search.js"></script>
	<script type="text/javascript" src="<?php echo base_url();?>js/tooltip.js"></script>
	<script type="text/javascript" src="<?php echo base_url();?>js/cookie.js"></script>
	<script type="text/javascript" src="<?php echo base_url();?>js/ui/ui.sortable.js"></script>
	<script type="text/javascript" src="<?php echo base_url();?>js/ui/ui.draggable.js"></script>
	<script type="text/javascript" src="<?php echo base_url();?>js/ui/ui.resizable.js"></script>
	<script type="text/javascript" src="<?php echo base_url();?>js/ui/ui.position.js"></script>
	<script type="text/javascript" src="<?php echo base_url();?>js/ui/ui.button.js"></script>
	<script type="text/javascript" src="<?php echo base_url();?>js/ui/ui.dialog.js"></script>
	<script type="text/javascript" src="<?php echo base_url();?>js/custom.js"></script>
	<script type="text/javascript" src="<?php echo base_url();?>js/fg.menu.js"></script>
    
	<script type="text/javascript" src="<?php echo base_url();?>js/facebox.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>js/validate.js"></script>
    
    <script type="text/javascript" src="<?php echo base_url();?>js/ui/ui-1.8.16.custom.min.js"></script>
	<script type="text/javascript" src="<?php echo base_url();?>js/ui/ui-timepicker-addon.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>js/ui/ui-sliderAccess.js"></script>
    
             <script type="text/javascript" src="<?php echo base_url();?>ckeditor/ckeditor.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>ckeditor/source/core/ckeditor.js"></script>
  
  <link rel="stylesheet" media="all" type="text/css" href="<?php echo base_url();?>css/ui/ui-1.8.16.custom.css" />
<!--	<script type="text/javascript" src="/js/ui/ui.tabs.js"></script>-->
		
			
    
	<link href="<?php echo base_url();?>css/ui/ui.base.css" rel="stylesheet" media="all" />

	<link href="<?php echo base_url();?>css/themes/apple_pie/ui.css" rel="stylesheet" title="style" media="all" />
	<link href="<?php echo base_url();?>css/themes/apple_pie/ui.css" rel="stylesheet" media="all" />
    <link href="<?php echo base_url();?>css/ui/ui.datepicker.css" rel="stylesheet" media="all" />
    <link href="<?php echo base_url();?>css/facebox.css" rel="stylesheet" media="all" />
<!--<link href="/css/themes/black_rose/ui.css" rel="stylesheet" title="style" media="all" />-->
	
    <link href="<?php echo base_url();?>css/fg.menu.css" rel="stylesheet" media="all" />

	<!--<link href="<?php echo base_url();?>css/blueprint.css" rel="stylesheet" media="all" />-->
    <link href="<?php echo base_url();?>css/ui/ui.forms.css" rel="stylesheet" media="all" />

	<!--[if IE 6]>
	<link href="/css/ie6.css" rel="stylesheet" media="all" />	
	<script src="/js/pngfix.js"></script>
	<script>
	  /* Fix IE6 Transparent PNG */
	  DD_belatedPNG.fix('.logo, ul#dashboard-buttons li a, .response-msg, #search-bar input');

	</script>
	<![endif]-->

	<link rel="stylesheet" href="<?php echo base_url();?>css/astppbilling.css" type="text/css" />
		
	<style>
	    .icon {
	        padding-left: 5px;
	        padding-top: 2px;
	        padding-right: 13px;
	        padding-bottom: 2px;
	        background-repeat:
	        no-repeat;
	    }	
	body {
    font-size: 62.5%;
    font-family: 'Segoe UI', Frutiger, Tahoma, Helvetica, 'Helvetica Neue', Arial, sans-serif;
    /*background: #ECECEC;
    color: #666;*/
	}	  
	  
	</style>
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
    <link rel="stylesheet" href="<?php echo base_url();?>menu.css" type="text/css" media="screen" />
    <!--[if IE 6]>
    <style>
    body {behavior: url("csshover3.htc");}
    #menu li .drop {background:url("img/drop.gif") no-repeat right 8px; 
    </style>
    <![endif]-->
	
	<script type="text/javascript" >	
		var old_submenu_val = '';		
		$(document).ready(function(){
			
			
	
			$("#navigationjj li").removeClass("active");
			
			$("#navigationjj a").each(function(submenuLink){
				if($(this).attr("href") == window.location.href)
				{
					$(this).parent().addClass("active");
				}
			})
			
			$('a[rel*=facebox]').facebox({
        		loadingImage : '<?php echo base_url();?>/images/loading.gif',
        		closeImage   : '<?php echo base_url();?>/images/closelabel.png'
      		})
			
			$('.hasDatepicker').datepicker({
				changeMonth: true,
				changeYear: true,
				showButtonPanel: true
			});
			
			old_submenu_val = $('#navigationjj').html();
			
			$("#top").mouseover(function(){
				if(old_submenu_val != '')
				$('#navigationjj').html(old_submenu_val);
			});	
			
			$("#body_content").mouseover(function(){
				if(old_submenu_val != '')
				$('#navigationjj').html(old_submenu_val);	
			});
					
		});
		
		
		function fix_sub()
		{
			//old_submenu_val = '';
		}
		function hide_sub()
		{
			if(old_submenu_val != '')
				$('#navigationjj').html(old_submenu_val);
		}
		/*function show_sub(no)
		{
			var html = "<ul>";
			if(no==11){
				<?php 
				if ($this->session->userdata('logintype') == 5) 
				{ 
				?>
				html = html + '<li class=""><a href="<?php echo base_url();?>callshops/booths_list/">List Booths</a></li>';	
			
				<? } ?>
			}
			if(no==1)
			{
				<?php 
				if ($this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 2 || $this->session->userdata('logintype') == 4) 
				{ 
				?>
				html = html + '<li class=""><a href="<?php echo base_url();?>accounts/account_list/">List Accounts</a></li>';
				html = html + '<li class=""><a href="<?php echo base_url();?>accounts/create/">Create Account</a></li>';
				<? } ?>
			}
			if(no==2)
			{
				<?php 
				if ($this->session->userdata('logintype') == 1) 
				{ 
				?>
				html = html + '<li class=""><a href="<?php echo base_url();?>callshops/boothReport/">CallShop Report</a></li>';
				html = html + '<li class=""><a href="<?php echo base_url();?>adminReports/resellerReport/">Reseller Report</a></li>';
				html = html + '<li class=""><a href="<?php echo base_url();?>adminReports/userReport/">User Report</a></li>';
				<? } ?>
				<?php 
				if ($this->session->userdata('logintype') == 2) 
				{ 
				?>
				html = html + '<li class=""><a href="<?php echo base_url();?>adminReports/resellerReport/">Reseller Report</a></li>';
				html = html + '<li class=""><a href="<?php echo base_url();?>adminReports/providerReport/">Provider Report</a></li>';
				<?php }?>
			}
			if(no==33)
			{
				html = html + '<li class=""></li>';
				html = html + '<li class=""></li>';
			}
			if(no==3)
			{
				<?php 
				if ($this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 2) 
				{ 
				?>
				html = html + '<li class=""><a href="<?php echo base_url();?>callshops/listAll/">List CallShop</a></li>';	
				<? } ?>
				<?php 
				if ($this->session->userdata('logintype') == 5) 
				{ 
				?>
				html = html + '<li class=""><a href="<?php echo base_url();?>callshops/boothReport/">Booth Report</a></li>';	
				<? } ?>
			}
			if(no==4)
			{
				<?php 
				if ($this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 2 || $this->session->userdata('logintype') == 4 || $this->session->userdata('logintype') == 5) 
				{ 
				?>
				html = html + '<li class=""><a href="<?php echo base_url();?>callingcards/cclist/">List Cards</a></li>';				
				html = html + '<li class=""><a href="<?php echo base_url();?>callingcards/brands/">CC Brands</a></li>';
				<? } ?>
				<?php 
				if ($this->session->userdata('logintype') == 2)
				{ ?>
				html = html + '<li class=""><a href="<?php echo base_url();?>callingcards/cdrs/">Callingcard CDRs</a></li>';
				<?  } ?>
			}
			if(no==5)
			{
				<?php 
				if ($this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 2 || $this->session->userdata('logintype') == 4) 
				{ 
				?>
				html = html + '<li class=""><a href="<?php echo base_url();?>did/manage/">Manage DIDs</a></li>';				
				<!--html = html + '<li class=""><a href="<?php echo base_url();?>userdid/manage/">User Mode DIDs</a></li>';-->
				<? } ?>
			}
			if(no==6)
			{
				<?php 
				if ($this->session->userdata('logintype') == 2) 
				{ 
				?>
				html = html + '<li class=""><a href="<?php echo base_url();?>lcr/providers/">Providers</a></li>';				
				html = html + '<li class=""><a href="<?php echo base_url();?>lcr/trunks/">Trunks</a></li>';
				<? } ?>
				<?php 
				if ($this->session->userdata('logintype') == 2 || $this->session->userdata('logintype') == 4 || $this->session->userdata('logintype') == 3) 
				{ 
				?>
				html = html + '<li class=""><a href="<?php echo base_url();?>lcr/outbound/">Outbound Routes</a></li>';
				<? } ?>
			}
			if(no==7)
			{
				<?php 
				if ($this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 2 || $this->session->userdata('logintype') == 5) 
				{ 
				?>
				html = html + '<li class=""><a href="<?php echo base_url();?>rates/pricelists/">Pricelists</a></li>';
				<? } 
				if ($this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 2 || $this->session->userdata('logintype') == 4) 
				{ 			
				?>
				html = html + '<li class=""><a href="<?php echo base_url();?>rates/calccharge/">Calc Charge</a></li>';
				<? } ?>
				<?
				if ($this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 2 || $this->session->userdata('logintype') == 5) 
				{ 			
				?>
				html = html + '<li class=""><a href="<?php echo base_url();?>rates/routes/">Routes</a></li>';
				<? } ?>
				<?
				if ($this->session->userdata('logintype') == 1 || $this->session->userdata('logintype') == 2 || $this->session->userdata('logintype') == 4) 
				{ 			
				?>
				html = html + '<li class=""><a href="<?php echo base_url();?>rates/periodiccharges/">Periodic Charges</a></li>';
				html = html + '<li class=""><a href="<?php echo base_url();?>rates/packages/">Packages</a></li>';
				html = html + '<li class=""><a href="<?php echo base_url();?>rates/counters/">Counters</a></li>';
				<? } ?>
			}
			if(no==8)
			{
				<?php 
				if ($this->session->userdata('logintype') == 2 || $this->session->userdata('logintype') == 4) 
				{ 
				?>
				html = html + '<li class=""><a href="<?php echo base_url();?>statistics/listerrors/">List Errors</a></li>';				
				<? } ?>
				<?php 
				if ($this->session->userdata('logintype') == 2 || $this->session->userdata('logintype') == 3 || $this->session->userdata('logintype') == 4 ) 
				{ 
				?>
				html = html + '<li class=""><a href="<?php echo base_url();?>statistics/trunkstats/">Trunk stats</a></li>';
// 				html = html + '<li class=""><a href="<?php echo base_url();?>statistics/viewcdrs/">View CDRs</a></li>';
				<? } ?>
				<?php 
				if ($this->session->userdata('logintype') == 2 ) 
				{ 
				?>
				html = html + '<li class=""><a href="<?php echo base_url();?>statistics/viewfscdrs/">View FreeSwitch(TM) CDRs</a></li>';
				<? } ?>
			}
			if(no==9)
			{
				<?php 
				if ($this->session->userdata('logintype') == 2 ) 
				{ 
				?>
				html = html + '<li class=""><a href="<?php echo base_url();?>switchconfig/fssipdevices/">Freeswitch(TM) SIP Devices</a></li>';
				html = html + '<li class=""><a href="<?php echo base_url();?>switchconfig/acl_list/">Access Control List (ACL)</a></li>';                               <? } ?>
			}
			if(no==10)
			{
				<?php 
				if ($this->session->userdata('logintype') == 2 ) 
				{ 
				?>
				html = html + '<li class=""><a href="<?php echo base_url();?>systems/purgedeactivated/">Purge Deactivated</a></li>';				
				html = html + '<li class=""><a href="<?php echo base_url();?>systems/configuration/">Configuration</a></li>';			
				html = html + '<li class=""><a href="<?php echo base_url();?>systems/taxes/">Taxes</a></li>';
				<? } ?>
			}
			
			html = html + '</ul>';
			
			$('#navigationjj').html(html);
		}*/
	</script>
	<? start_block_marker('extra_head') ?>
	<? end_block_marker() ?>
	
</head>
<body>
<?php 
	$logged_in = $this->session->userdata('user_login');	
	$logged_user = $this->session->userdata('user_name');
	
	$menu_no = 1;
	if(isset($cur_menu_no)) $menu_no = $cur_menu_no;
	$submenu_no = 1;
	if(isset($cur_submenu_no)) $submenu_no = $cur_menu_no;
	
?>
	<div id="page_wrapper">
		<div id="page-header">
			<div id="page-header-wrapper" style="height: 140px;">            
				<div id="top" style="height: 78px;" onmouseover="hide_sub()">
					<a href="<?=base_url()?>astpp/dashboard" class="logo" title="<?=$app_name?>" ><?=$app_name?></a>
					<?php if($logged_in==TRUE):?>
					<div class="welcome">
						<span class="note" style="color: black;">Welcome <a href="#" title="Welcome  <?=$logged_user?>"><?=ucfirst($logged_user)?><? //echo $this->session->userdata('logintype').$this->session->userdata('mode_cur');?></a></span>
						
						<? if ($this->session->userdata('logintype') == 4){?>
						<? if ($this->session->userdata('mode_cur') == 'admin'){?>
						<!--<a class="btn ui-state-default ui-corner-all" href="#"  style="background-color: transparent;">
							<span class="ui-icon ui-icon-wrench"></span>
							User Mode 
						</a>-->
						<? }else{ ?>
							<!--<span class="note " style="background-color: transparent;color: black;">
							<br />User Mode
							</span>-->
						<?php }} ?>
						
						<? if ($this->session->userdata('logintype') == 4){?>
						<? if ($this->session->userdata('mode_cur') == 'user'){?>
						<!--<a class="btn ui-state-default ui-corner-all" href="#"  style="background-color: transparent;">
							<span class="ui-icon ui-icon-person"></span>
							Admin Mode
						</a>-->
						<? }else{ ?>
							<!--<span class="note " style="background-color: transparent;color: black;">
							<br />Admin Mode
							</span>-->
						<?php }} ?>
						<!--<a class="btn ui-state-default ui-corner-all" href="#">
							<span class="ui-icon ui-icon-wrench"></span>
							Settings
						</a>-->
                        <?php if($this->session->userdata('username') != ""){?>
						<a rel="facebox" class="btn ui-state-default ui-corner-all" href="<?=base_url()?>accounts/accountDetailsPopup/<?=$this->session->userdata('username')?>">
							<span class="ui-icon ui-icon-person"></span>
							My account
						</a>
                        <?php }?>
						<a class="btn ui-state-default ui-corner-all" href="<?php echo base_url();?>astpp/logout" style="background-color: transparent;">
							<span class="ui-icon ui-icon-power"></span>
							Logout
						</a>						
					</div>
					<?php endif;?>
				</div>
				<?php if($logged_in==TRUE):?>   
                <!--id="navigationj" -->
				<div  style="height: 34px; left: 0;   padding-top: 10px;   position: absolute;   top: 38px; width: 100%;">
                <?php 
				if ($this->session->userdata('logintype') == 2 ) 
				{ 
				?>
				<ul id="menu">
    
    <li style="-moz-border-radius: 5px 5px 5px 5px;-webkit-border-radius: 5px 5px 5px 5px;border-radius: 5px 5px 5px 5px;"><a href="<?=base_url()?>astpp/dashboard"><img src="<?=base_url()?>menu_icons/Home.png" border="0" width="16" height="16"  />&nbsp;&nbsp;Home</a>      
    
    </li>
	<li><a href="#" class="drop_menu">Manage Accounts</a>
    
    <div class="dropdown_2columns">
        
                <div class="col_1">
               
                    <ul class="simple">
                        <li><a href="<?php echo base_url();?>accounts/account_list/"><img src="<?=base_url()?>menu_icons/Accounts/ListAccounts.png" border="0" width="16" height="16" />&nbsp;&nbsp;List Accounts</a></li>
                        <li><a href="<?php echo base_url();?>accounts/create/"><img src="<?=base_url()?>menu_icons/Accounts/add_user.png" border="0" width="16" height="16" />&nbsp;&nbsp;Create Account</a></li>
                       
                    </ul>   
                     
                </div>
                
		</div>
    
    </li>
    <li><a href="#" class="drop_menu">Accounting</a>
     <div class="dropdown_4columns">
         	  <div class="col_1">            
                <h3>Subscriptions</h3>
                <ul>
                     <li><a href="<?php echo base_url();?>rates/periodiccharges/"><img src="<?=base_url()?>menu_icons/Rates/PeriodicCharges.png" border="0" width="16" height="16" />&nbsp;&nbsp;Periodic Charges</a></li>                  
                    </ul>   
             </div>
              <div class="col_1">            
                <h3>Manage Invoices</h3>
                <ul >
		    <li><a href="<?php echo base_url();?>accounting/invoice_list/"><img src="<?=base_url()?>menu_icons/Accounting/InvoiceList.png" border="0" width="16" height="16" />&nbsp;&nbsp;List Invoices</a></li>                  
		    <li><a href="<?php echo base_url();?>accounting/invoiceconf/"><img src="<?=base_url()?>menu_icons/Accounting/InvoiceConf.png" border="0" width="16" height="16" />&nbsp;&nbsp;Invoice Configuration</a></li>                 
                </ul>
              </div>
              
               <div class="col_1">            
                <h3>Manage Taxes</h3>
                <ul >
                   <li><a href="<?php echo base_url();?>systems/taxes/"><img src="<?=base_url()?>menu_icons/System/Taxes.png" border="0" width="16" height="16" />&nbsp;&nbsp;Taxes</a></li>
                   <li><a href="<?php echo base_url();?>accounting/account_taxes/"><img src="<?=base_url()?>menu_icons/Accounting/AccountTaxes.png" border="0" width="16" height="16" />&nbsp;&nbsp;Account Taxes</a></li>                    
                </ul>
              </div>    
      </div>          
    </li>
     
    <li>
    <a href="#" class="drop_menu">Services</a>
    
    <div class="dropdown_3columns">
		    <div class="col_1">            
                <h3>Calling Cards</h3>
                 <ul class="simple">
                        <li><a href="<?php echo base_url();?>callingcards/cclist/"><img src="<?=base_url()?>menu_icons/Modules/CallingCards/ListCards.png" border="0" width="16" height="16" />&nbsp;&nbsp;List Cards</a></li>
                   	    <li><a href="<?php echo base_url();?>callingcards/brands/"><img src="<?=base_url()?>menu_icons/Modules/CallingCards/CCBand.png" border="0" width="16" height="16" />&nbsp;&nbsp;CC Brands</a></li>
                        <li><a href="<?php echo base_url();?>callingcards/cdrs/"><img src="<?=base_url()?>menu_icons/Modules/CallingCards/CallingCardCDR's.png" border="0" width="16" height="16" />&nbsp;&nbsp;Callingcard CDRs</a></li> 
                       
                    </ul>  
                
            </div>
    		 <div class="col_1">            
                <h3>Call Shop</h3>
                <ul class="simple">
                     <li><a href="<?php echo base_url();?>callshops/listAll/"><img src="<?=base_url()?>menu_icons/Modules/CallShop/ListCallshop.png" border="0" width="16" height="16"  />&nbsp;&nbsp;List Call Shop</a></li>
                </ul>  
             </div>   
                
    </div>
    
    </li>
   
   
    <li>
    <a href="#" class="drop_menu">DIDs</a>
     <div class="dropdown_2columns">
        
                <div class="col_1">
                
                    <ul class="simple">
                         <li><a href="<?php echo base_url();?>did/manage/"><img src="<?=base_url()?>menu_icons/DID's/ManageDIDs.png" border="0" width="16" height="16" />&nbsp;&nbsp;Manage   DID's</a></li>
                   	  
                    </ul>   
                     
                </div>
                
		</div>  
  
    </li>
    <li><a href="#" class="drop_menu">Routing</a>
    	 <div class="dropdown_3columns">
		    <div class="col_1">            
                <h3>Providers</h3>
                 <ul class="simple">
                       <li><a href="<?php echo base_url();?>lcr/providers/"><img src="<?=base_url()?>menu_icons/LCR/Providers.png" border="0" width="16" height="16" />&nbsp;&nbsp;Provider Details</a></li>
                        <li><a href="<?php echo base_url();?>lcr/trunks/"><img src="<?=base_url()?>menu_icons/LCR/Trunks.png" border="0" width="16" height="16" />&nbsp;&nbsp;Trunks</a></li>
                   	  	 <li><a href="<?php echo base_url();?>lcr/outbound/"><img src="<?=base_url()?>menu_icons/LCR/OutboundRoutes.png" border="0" width="16" height="16" />&nbsp;&nbsp;Termination Rates</a></li>
                       
                    </ul>  
                
            </div>
    		 <div class="col_1">            
                <h3>Clients</h3>
                <ul class="simple">
                         <li><a href="<?php echo base_url();?>rates/pricelists/"><img src="<?=base_url()?>menu_icons/Rates/pricelist.png" border="0" width="16" height="16" />&nbsp;&nbsp;PriceLists</a></li>
                         <li><a href="<?php echo base_url();?>rates/routes/"><img src="<?=base_url()?>menu_icons/Rates/Routes.png" border="0" width="16" height="16"  />&nbsp;&nbsp;Origination Rates</a></li>
                        <li><a href="<?php echo base_url();?>rates/calccharge/"><img src="<?=base_url()?>menu_icons/Rates/CalcCharge.png" border="0" width="16" height="16" />&nbsp;&nbsp;Calc Charge</a></li>
                        <li><a href="<?php echo base_url();?>rates/packages/"><img src="<?=base_url()?>menu_icons/Rates/packages.png" border="0" width="16" height="16" />&nbsp;&nbsp;Packages</a></li>
                        <li><a href="<?php echo base_url();?>rates/counters/"><img src="<?=base_url()?>menu_icons/Rates/Counters.png" border="0" width="16" height="16" />&nbsp;&nbsp;Counters</a></li>
                </ul>  
             </div>   
                
    </div>
    </li>
 
     <li>
    <a href="#" class="drop_menu">Reports</a>
     <div class="dropdown_3columns">
	      
	      <div class="col_1">            
                <h3>Call Detail Reports</h3>
                <ul>
                  <li><a href="<?php echo base_url();?>cdrReports/customerReport/"><img src="<?=base_url()?>menu_icons/CDR/cdr.png" border="0" width="16" height="16" />&nbsp;&nbsp;Customer Report</a></li>
                  <li><a href="<?php echo base_url();?>cdrReports/resellerReport/"><img src="<?=base_url()?>menu_icons/CDR/cdr.png" border="0" width="16" height="16" />&nbsp;&nbsp;Reseller Report</a></li>      
                  <li><a href="<?php echo base_url();?>cdrReports/providerReport/"><img src="<?=base_url()?>menu_icons/CDR/cdr.png" border="0" width="16" height="16" />&nbsp;&nbsp;Provider Report</a></li>      
                </ul>
              </div>  
	      
	      
              <div class="col_1">            
                <h3>Switch Reports</h3>
                <ul>
                 <li><a href="<?php echo base_url();?>statistics/listerrors/"><img src="<?=base_url()?>menu_icons/Statistic/ListErrors.png" border="0" width="16" height="16" />&nbsp;&nbsp;List Errors</a></li>
                        <li><a href="<?php echo base_url();?>statistics/trunkstats/"><img src="<?=base_url()?>menu_icons/Statistic/TrunkStats.png" border="0" width="16" height="16" />&nbsp;&nbsp;Trunk Stats</a></li>
                   	  	<li><a href="<?php echo base_url();?>statistics/viewfscdrs/"><img src="<?=base_url()?>menu_icons/Statistic/ViewFreeSwitch(TM)CDRs.png" border="0" width="16" height="16" />&nbsp;&nbsp;View FreeSwitch CDRs</a></li>
                </ul>
              </div>  
              
              <div class="col_1">            
                <h3>Summary Reports</h3>
                <ul>
                  <li><a href="<?php echo base_url();?>adminReports/resellerReport/"><img src="<?=base_url()?>menu_icons/AdminReports/ResellerReports.png" border="0" width="16" height="16" />&nbsp;&nbsp;Reseller Report</a></li>
                        <li><a href="<?php echo base_url();?>adminReports/providerReport/"><img src="<?=base_url()?>menu_icons/AdminReports/ResellerReports.png" border="0" width="16" height="16" />&nbsp;&nbsp;Provider Report</a></li>
                </ul>
              </div>  
              
      </div>        
    
    </li>
    
    <li>
    <a href="#" class="drop_menu">System Configuration</a>
    	 <div class="dropdown_3columns">
         	  <div class="col_1">            
                <h3>Switch Config</h3>
		      <ul>
			<? if(Common_model::$global_config['system_config']['users_dids_freeswitch']=='1'){ ?>
                        <li><a href="<?php echo base_url();?>switchconfig/fssipdevices/"><img src="<?=base_url()?>menu_icons/SwitchConfig/Devices.png" border="0" width="16" height="16" />&nbsp;&nbsp;Freeswitch SIP Devices</a></li>
                        <?}?>
                        <li><a href="<?php echo base_url();?>switchconfig/acl_list/"><img src="<?=base_url()?>menu_icons/SwitchConfig/AccessControlList(ACL).png" border="0" width="16" height="16" />&nbsp;&nbsp;Access Control List</a></li>
                    </ul>  
              </div>                              
              
               <div class="col_1">
            
                <h3>System</h3>
                 <ul >
                        <li><a href="<?php echo base_url();?>systems/configuration/"><img src="<?=base_url()?>menu_icons/System/Configurations.png" border="0" width="16" height="16" />&nbsp;&nbsp;Configuration</a></li>
                     
                   	    <li><a href="<?php echo base_url();?>systems/purgedeactivated/"><img src="<?=base_url()?>menu_icons/System/PurgeDeactivated.png" border="0" width="16" height="16" />&nbsp;&nbsp;Purge Deactivated</a></li>
                    <li><a href="<?php echo base_url();?>systems/template/"><img src="<?=base_url()?>menu_icons/System/TemplateManagement.png" border="0" width="16" height="16" />&nbsp;&nbsp;Email Template</a></li>
                    </ul>  
                
             </div> 
             
             <?php if((Common_model::$global_config['system_config']['opensips']) == '1')
                 {?>
                     <div class="col_1">            
                <h3>Opensips Config Alfa</h3>
		      <ul>
 			
                        <li><a href="<?php echo base_url();?>opensipsconfig/opensipdevice/"><img src="<?=base_url()?>menu_icons/OpensipsConfig/Devices.png" border="0" width="16" height="16" />&nbsp;&nbsp;Opensips Devices</a></li>
                        <li><a href="<?php echo base_url();?>opensipsconfig/dispatcher/"><img src="<?=base_url()?>menu_icons/OpensipsConfig/Dispatcher.png" border="0" width="16" height="16" />&nbsp;&nbsp;Dispatcher List</a></li>
                      
                    </ul>  
              </div> 
                     <?}?>
         </div>
    </li>
   
    <li style="-moz-border-radius: 5px 5px 5px 5px;-webkit-border-radius: 5px 5px 5px 5px;border-radius: 5px 5px 5px 5px;" class="menu_right"><a href="<?=base_url()?>astpp/search" rel="facebox" onclick="return false;">Quick Search</a>
    </li>
   
    </ul>
    			<? } ?> 
    			<?php 
				if ($this->session->userdata('logintype') == 1 ) 
				{ 
				?>
				<ul id="menu">
					
					<li style="-moz-border-radius: 5px 5px 5px 5px;-webkit-border-radius: 5px 5px 5px 5px;border-radius: 5px 5px 5px 5px;"><a href="<?=base_url()?>astpp/dashboard"><img src="<?=base_url()?>menu_icons/Home.png" border="0" width="16" height="16"  />&nbsp;&nbsp;Home</a>      
					
					</li>
					<li><a href="#" class="drop_menu">Manage Accounts</a>
					
					<div class="dropdown_2columns">
						
								<div class="col_1">
							   
									<ul class="simple">
										<li><a href="<?php echo base_url();?>accounts/account_list/"><img src="<?=base_url()?>menu_icons/Accounts/ListAccounts.png" border="0" width="16" height="16" />&nbsp;&nbsp;List Accounts</a></li>
										<li><a href="<?php echo base_url();?>accounts/create/"><img src="<?=base_url()?>menu_icons/Accounts/add_user.png" border="0" width="16" height="16" />&nbsp;&nbsp;Create Account</a></li>
									   
									</ul>   
									 
								</div>
								
						</div>
					
					</li>
					<li><a href="#" class="drop_menu">Accounting</a>
					 <div class="dropdown_4columns">
							  <div class="col_1">            
								<h3>Subscription</h3>
								<ul >
									 <li><a href="<?php echo base_url();?>rates/periodiccharges/"><img src="<?=base_url()?>menu_icons/Rates/PeriodicCharges.png" border="0" width="16" height="16" />&nbsp;&nbsp;Periodic Charges</a></li>                  
									</ul>   
							 </div>
							  <div class="col_1">            
								<h3>Manage Invoices</h3>
								<ul >
								    <li><a href="<?php echo base_url();?>accounting/invoice_list/"><img src="<?=base_url()?>menu_icons/Accounting/InvoiceList.png" border="0" width="16" height="16" />&nbsp;&nbsp;List Invoices</a></li>                  
								    <li><a href="<?php echo base_url();?>accounting/invoiceconf/"><img src="<?=base_url()?>menu_icons/Accounting/InvoiceConf.png" border="0" 		width="16" height="16" />&nbsp;&nbsp;Invoice Configuration</a></li>                 
								</ul>
							  </div>
							  <div class="col_1">            
							  <h3>Manage Taxes</h3>
							  <ul >
							    <li><a href="<?php echo base_url();?>accounting/account_taxes/"><img src="<?=base_url()?>menu_icons/Accounting/AccountTaxes.png" border="0" width="16" height="16" />&nbsp;&nbsp;Account Taxes</a></li>                    
							  </ul>
							</div>    
							  
								   
					  </div>          
					</li>
					 
					<li>
					<a href="#" class="drop_menu">Services</a>
					
					<div class="dropdown_3columns">
							<div class="col_1">            
								<h3>Calling Cards</h3>
								 <ul class="simple">
										<li><a href="<?php echo base_url();?>callingcards/cclist/"><img src="<?=base_url()?>menu_icons/Modules/CallingCards/ListCards.png" border="0" width="16" height="16" />&nbsp;&nbsp;List Cards</a></li>
										<li><a href="<?php echo base_url();?>callingcards/brands/"><img src="<?=base_url()?>menu_icons/Modules/CallingCards/CCBand.png" border="0" width="16" height="16" />&nbsp;&nbsp;CC Brands</a></li>
										<li><a href="<?php echo base_url();?>callingcards/cdrs/"><img src="<?=base_url()?>menu_icons/Modules/CallingCards/CallingCardCDR's.png" border="0" width="16" height="16" />&nbsp;&nbsp;Callingcard CDRs</a></li> 
															  
									</ul>  
								
							</div>
							 <div class="col_1">            
								<h3>Call Shop</h3>
								<ul class="simple">
									 <li><a href="<?php echo base_url();?>callshops/listAll/"><img src="<?=base_url()?>menu_icons/Modules/CallShop/ListCallshop.png" border="0" width="16" height="16"  />&nbsp;&nbsp;List Call Shop</a></li>
								</ul>  
							 </div>   
								
					</div>
					
					</li>
				   
				   
					<li>
					<a href="#" class="drop_menu">DIDs</a>
					 <div class="dropdown_2columns">
						
								<div class="col_1">
								
									<ul class="simple">
										 <li><a href="<?php echo base_url();?>did/manage/"><img src="<?=base_url()?>menu_icons/DID's/ManageDIDs.png" border="0" width="16" height="16" />&nbsp;&nbsp;Manage   DID's</a></li>
									  
									</ul>   
									 
								</div>
								
						</div>  
				  
					</li>
					<li><a href="#" class="drop_menu">Routing</a>
						 <div class="dropdown_2columns">
							
							 <div class="col_1">            
								<h3>Clients</h3>
								<ul class="simple">
								<li><a href="<?php echo base_url();?>rates/pricelists/"><img src="<?=base_url()?>menu_icons/Rates/pricelist.png" border="0" width="16" height="16" />&nbsp;&nbsp;PriceLists</a></li>
								<li><a href="<?php echo base_url();?>rates/routes/"><img src="<?=base_url()?>menu_icons/Rates/Routes.png" border="0" width="16" height="16"  />&nbsp;&nbsp;Origination Rates</a></li>
								<li><a href="<?php echo base_url();?>rates/calccharge/"><img src="<?=base_url()?>menu_icons/Rates/CalcCharge.png" border="0" width="16" height="16" />&nbsp;&nbsp;Calc Charge</a></li>
								<li><a href="<?php echo base_url();?>rates/packages/"><img src="<?=base_url()?>menu_icons/Rates/packages.png" border="0" width="16" height="16" />&nbsp;&nbsp;Packages</a></li>
								<li><a href="<?php echo base_url();?>rates/counters/"><img src="<?=base_url()?>menu_icons/Rates/Counters.png" border="0" width="16" height="16" />&nbsp;&nbsp;Counters</a></li>
								</ul>  
							 </div>   
								
					</div>
					</li>
				 
					 <li>
					<a href="#" class="drop_menu">Reports & Stats</a>
					 <div class="dropdown_3columns">
							   <div class="col_1">            
								<h3>Call Detail Reports</h3>
								    <ul>
								      <!--<li><a href="<?php echo base_url();?>cdrReports/myReport/"><img src="<?=base_url()?>menu_icons/AdminReports/ResellerReports.png" border="0" width="16" 	height="16" />&nbsp;&nbsp;My Report</a></li>  -->
								      <li><a href="<?php echo base_url();?>cdrReports/customerReport/"><img src="<?=base_url()?>menu_icons/CDR/cdr.png" border="0" width="16" height="16" />&nbsp;&nbsp;Customer Report</a></li>
								      <li><a href="<?php echo base_url();?>cdrReports/resellerReport/"><img src="<?=base_url()?>menu_icons/CDR/cdr.png" border="0" width="16" height="16" />&nbsp;&nbsp;Reseller Report</a></li>      
								          
								    </ul>
								
							  </div>  
								
							<div class="col_1">            
								<h3>Summary Reports</h3>
								<ul>
								<li><a href="<?php echo base_url();?>adminReports/userReport/"><img src="<?=base_url()?>menu_icons/AdminReports/ResellerReports.png" border="0" width="16" height="16" />&nbsp;&nbsp;Customer Report</a></li>
								<li><a href="<?php echo base_url();?>adminReports/resellerReport/"><img src="<?=base_url()?>menu_icons/AdminReports/ResellerReports.png" border="0" width="16" height="16" />&nbsp;&nbsp;Reseller Report</a></li>
								<li><a href="<?php echo base_url();?>callshops/boothReport/"><img src="<?=base_url()?>menu_icons/AdminReports/ResellerReports.png" border="0" width="16" height="16" />&nbsp;&nbsp;CallShop Report</a></li>
								</ul>
							   </div>	
							  
					  </div>        
					  <!--<div class="col_1">            
						<h3>Call Detail Reports</h3>
						<ul>
						  <li><a href="<?php echo base_url();?>cdrReports/customerReport/"><img src="<?=base_url()?>menu_icons/AdminReports/ResellerReports.png" border="0" width="16" height="16" />&nbsp;&nbsp;Customer Report</a></li>
						  <li><a href="<?php echo base_url();?>cdrReports/resellerReport/"><img src="<?=base_url()?>menu_icons/AdminReports/ResellerReports.png" border="0" width="16" height="16" />&nbsp;&nbsp;Reseller Report</a></li>      
						  <li><a href="<?php echo base_url();?>cdrReports/providerReport/"><img src="<?=base_url()?>menu_icons/AdminReports/ResellerReports.png" border="0" width="16" height="16" />&nbsp;&nbsp;Provider Report</a></li>      
						</ul>
					      </div>  -->
					</li>
					<li>
					<a href="#" class="drop_menu">Systems</a>
					 <div class="dropdown_2columns">
							  <div class="col_1">            
								<h3>Management</h3>
								<ul>
					    		<li><a href="<?php echo base_url();?>systems/template/"><img src="<?=base_url()?>menu_icons/System/TemplateManagement.png" border="0" width="16" height="16" />&nbsp;&nbsp;Email Template</a></li>
								</ul>
							  </div>  
								
							  
					  </div>        
					
					</li>
					
					
					<li style="-moz-border-radius: 5px 5px 5px 5px;-webkit-border-radius: 5px 5px 5px 5px;border-radius: 5px 5px 5px 5px;" class="menu_right"><a href="<?=base_url()?>astpp/search" rel="facebox" onclick="return false;">Quick Search</a>
					</li>
					</ul>    
				 <? } ?>
                  <?php 
					if ($this->session->userdata('logintype') == 3 ) 
					{ 
					?>
					<ul id="menu">
					
					<li style="-moz-border-radius: 5px 5px 5px 5px;-webkit-border-radius: 5px 5px 5px 5px;border-radius: 5px 5px 5px 5px;"><a href="<?=base_url()?>astpp/dashboard"><img src="<?=base_url()?>menu_icons/Home.png" border="0" width="16" height="16"  />&nbsp;&nbsp;Home</a>      
					
					</li>
					<li><a href="#" class="drop_menu">Routing</a>
					<div class="dropdown_2columns">
					<div class="col_1">            
					<h3>Provider</h3>
					 <ul class="simple">
						  <li><a href="<?php echo base_url();?>lcr/outbound/"><img src="<?=base_url()?>menu_icons/LCR/OutboundRoutes.png" border="0" width="16" height="16" />&nbsp;&nbsp;Termination Rates</a></li>	   
						</ul>  
					</div>
					</div>
					
					</li>
					
					<li>
					<a href="#" class="drop_menu">Statistics</a>
					<div class="dropdown_2columns">
					<div class="col_1">            
					<h3>Switch Reports</h3>
					<ul>
						<li><a href="<?php echo base_url();?>statistics/trunkstats/"><img src="<?=base_url()?>menu_icons/Statistic/TrunkStats.png" border="0" width="16" height="16" />&nbsp;&nbsp;Trunk Stats</a></li>
						
					</ul>
					</div>  
					
					</div>
					</li>
					
					
					<li style="-moz-border-radius: 5px 5px 5px 5px;-webkit-border-radius: 5px 5px 5px 5px;border-radius: 5px 5px 5px 5px;" class="menu_right"><a href="<?=base_url()?>astpp/search" rel="facebox" onclick="return false;">Quick Search</a>
					</li>
					</ul>
					<? } ?> 
					<?php 
                    if ($this->session->userdata('logintype') == 4 ) 
                    { 
                    ?>
                    <ul id="menu">
                    
                    <li style="-moz-border-radius: 5px 5px 5px 5px;-webkit-border-radius: 5px 5px 5px 5px;border-radius: 5px 5px 5px 5px;"><a href="<?=base_url()?>astpp/dashboard"><img src="<?=base_url()?>menu_icons/Home.png" border="0" width="16" height="16"  />&nbsp;&nbsp;Home</a>      
                    
                    </li>
                    <li><a href="#" class="drop_menu">Accounts</a>
                    
                    <div class="dropdown_2columns">
                        
                                <div class="col_1">
                               
                                    <ul class="simple">
                                        <li><a href="<?php echo base_url();?>accounts/account_list/"><img src="<?=base_url()?>menu_icons/Accounts/ListAccounts.png" border="0" width="16" height="16" />&nbsp;&nbsp;List Accounts</a></li>
                                        <li><a href="<?php echo base_url();?>accounts/create/"><img src="<?=base_url()?>menu_icons/Accounts/add_user.png" border="0" width="16" height="16" />&nbsp;&nbsp;Create Account</a></li>
                                       
                                    </ul>   
                                     
                                </div>
                                
                        </div>
                    
                    </li>
                    
                     
                    <li>
                    <a href="#" class="drop_menu">Modules</a>
                    
                    <div class="dropdown_3columns">
                            <div class="col_1">            
                                <h3>Calling Cards</h3>
                                 <ul class="simple">
                                        <li><a href="<?php echo base_url();?>callingcards/cclist/"><img src="<?=base_url()?>menu_icons/Modules/CallingCards/ListCards.png" border="0" width="16" height="16" />&nbsp;&nbsp;List Cards</a></li>
                                        <li><a href="<?php echo base_url();?>callingcards/brands/"><img src="<?=base_url()?>menu_icons/Modules/CallingCards/CCBand.png" border="0" width="16" height="16" />&nbsp;&nbsp;CC Brands</a></li>
                                       
                                    </ul>  
                                
                            </div>
                             <div class="col_1">            
                                <h3>Call Shop</h3>
                                <ul class="simple">
                                     <li><a href="<?php echo base_url();?>callshops/listAll/"><img src="<?=base_url()?>menu_icons/Modules/CallShop/ListCallshop.png" border="0" width="16" height="16"  />&nbsp;&nbsp;List Call Shop</a></li>
                                </ul>  
                             </div>   
                                
                    </div>
                    
                    </li>
                   
                   
                    <li>
                    <a href="#" class="drop_menu">DIDs</a>
                     <div class="dropdown_2columns">
                        
                                <div class="col_1">
                                
                                    <ul class="simple">
                                         <li><a href="<?php echo base_url();?>did/manage/"><img src="<?=base_url()?>menu_icons/DID's/ManageDIDs.png" border="0" width="16" height="16" />&nbsp;&nbsp;Manage   DID's</a></li>
                                      
                                    </ul>   
                                     
                                </div>
                                
                        </div>  
                  
                    </li>
                    <li><a href="#" class="drop_menu">Routing</a>
                         <div class="dropdown_2columns">
                            
                             <div class="col_1">            
                                <h3>Clients</h3>
                                <ul class="simple">
                                         <li><a href="<?php echo base_url();?>rates/pricelists/"><img src="<?=base_url()?>menu_icons/Rates/pricelist.png" border="0" width="16" height="16" />&nbsp;&nbsp;PriceLists</a></li>
                                      
                                </ul>  
                             </div>   
                                
                    </div>
                    </li>
                 
                     <li>
                    <a href="#" class="drop_menu">Statistics</a>
                     <div class="dropdown_2columns">
                                
                              <div class="col_1">            
                                <h3>Switch Reports</h3>
                                <ul>
                                 <li><a href="<?php echo base_url();?>statistics/listerrors/"><img src="<?=base_url()?>menu_icons/Statistic/ListErrors.png" border="0" width="16" height="16" />&nbsp;&nbsp;List Errors</a></li>
                                        <li><a href="<?php echo base_url();?>statistics/trunkstats/"><img src="<?=base_url()?>menu_icons/Statistic/TrunkStats.png" border="0" width="16" height="16" />&nbsp;&nbsp;Trunk Stats</a></li>
                                     
                                </ul>
                              </div>  
                              
                      </div>        
                    
                    </li>
                    
                    <li>
                    <a href="#" class="drop_menu">System Configuration</a>
                         <div class="dropdown_3columns">
                              <div class="col_1">            
                                <h3>Switch Config</h3>
                                     <ul >
                                        <li><a href="<?php echo base_url();?>switchconfig/fssipdevices/"><img src="<?=base_url()?>menu_icons/SwitchConfig/Devices.png" border="0" width="16" height="16" />&nbsp;&nbsp;Freeswitch SIP Devices</a></li>
                                        <li><a href="<?php echo base_url();?>switchconfig/acl_list/"><img src="<?=base_url()?>menu_icons/SwitchConfig/AccessControlList(ACL).png" border="0" width="16" height="16" />&nbsp;&nbsp;Access Control List</a></li>
                                     
                                    
                                    </ul>  
                              </div>  
                              
                               <div class="col_1">
                            
                                <h3>System</h3>
                                 <ul >
                                        <li><a href="<?php echo base_url();?>systems/configuration/"><img src="<?=base_url()?>menu_icons/System/Configurations.png" border="0" width="16" height="16" />&nbsp;&nbsp;Configuration</a></li>
                                     
                                        <li><a href="<?php echo base_url();?>systems/purgedeactivated/">&nbsp;&nbsp;Purge Deactivated</a></li>
                                        <li><a href="<?php echo base_url();?>systems/template/"><img src="<?=base_url()?>menu_icons/System/TemplateManagement.png" border="0" width="16" height="16" />&nbsp;&nbsp;Email Template</a></li>
                                    </ul>  
                                
                             </div> 
                         </div>
                    </li>
                   
                    </ul>
                    
                    <? } ?>
					 <?php 
                    if ($this->session->userdata('logintype') == 5 ) 
                    { 
                    ?>
                    <ul id="menu">
                    
                    <li style="-moz-border-radius: 5px 5px 5px 5px;-webkit-border-radius: 5px 5px 5px 5px;border-radius: 5px 5px 5px 5px;"><a href="<?=base_url()?>astpp/dashboard"><img src="<?=base_url()?>menu_icons/Home.png" border="0" width="16" height="16"  />&nbsp;&nbsp;Home</a>      
                    
                    </li>
                    <li><a href="#" class="drop_menu">Booths</a>
                    
                    <div class="dropdown_2columns">
                    
                    <div class="col_1">
                    
                        <ul class="simple">
                            <li><a href="<?php echo base_url();?>callshops/booths_list/"><img src="<?=base_url()?>menu_icons/Accounts/ListAccounts.png" border="0" width="16" height="16" />&nbsp;&nbsp;List Booths </a></li>
                            
                        </ul>   
                         
                    </div>
                    
                    </div>
                    
                    </li>
                    
                    
                    <li>
                    <a href="#" class="drop_menu">Modules</a>
                    
                    <div class="dropdown_3columns">
                    <div class="col_1">            
                    <h3>Calling Cards</h3>
                     <ul class="simple">
                            <li><a href="<?php echo base_url();?>callingcards/cclist/"><img src="<?=base_url()?>menu_icons/Modules/CallingCards/ListCards.png" border="0" width="16" height="16" />&nbsp;&nbsp;List Cards</a></li>
                            <li><a href="<?php echo base_url();?>callingcards/brands/"><img src="<?=base_url()?>menu_icons/Modules/CallingCards/CCBand.png" border="0" width="16" height="16" />&nbsp;&nbsp;CC Brands</a></li>   
                        </ul>  
                    
                    </div>
                    <div class="col_1">            
                    <h3>Call Shop</h3>
                    <ul class="simple">
                         <li><a href="<?php echo base_url();?>callshops/boothReport/"><img src="<?=base_url()?>menu_icons/Modules/CallShop/ListCallshop.png" border="0" width="16" height="16"  />&nbsp;&nbsp;Booth Report</a></li>
                    </ul>  
                    </div>   
                    
                    </div>
                    
                    </li>
                    
                    
                    
                    <li><a href="#" class="drop_menu">Routing</a>
                    <div class="dropdown_2columns">
                    <div class="col_1">            
                    <h3>Clients</h3>
                    <ul class="simple">
                             <li><a href="<?php echo base_url();?>rates/pricelists/"><img src="<?=base_url()?>menu_icons/Rates/pricelist.png" border="0" width="16" height="16" />&nbsp;&nbsp;PriceLists</a></li>
                            
                            <li><a href="<?php echo base_url();?>rates/routes/"><img src="<?=base_url()?>menu_icons/Rates/Routes.png" border="0" width="16" height="16"  />&nbsp;&nbsp;Origination Rates</a></li>
                           
                            
                    </ul>  
                    </div>   
                    
                    </div>
                    </li>
                    
                    
                    
                    
                    
                    </ul>
                    <? } ?> 
                
				</div>
			
				
<!-- 				
				<div id="search-bar">
					<form method="post" action="http://www.google.com/">
						<input type="text" name="q" value="live search demo" />
					</form>
				</div>
 -->			
				<?php endif;?>
			</div>
		</div>
