<? extend('master.php') ?>
<? startblock('extra_head') ?>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/module_js/generate_grid.js"></script>
<script type="text/javascript" language="javascript">
    $(document).ready(function() {
        $('#tabs').tabs();
        build_grid("chrges_grid","<?php echo base_url(); ?>accounts/customer_chargelist_json/<?= $account_data[0]['id']; ?>",<? echo json_encode($charges_grid_field) ?>,"");

        build_grid("ipmap_grid","<?php echo base_url(); ?>accounts/customer_ipmap_json/<?= $account_data[0]['id']; ?>",<? echo json_encode($ipmap_grid_field) ?>,"");

        build_grid("ANI_map_grid","<?php echo base_url(); ?>accounts/customer_animap_json/<?= $account_data[0]['id']; ?>",<? echo json_encode($animap_grid_field) ?>,"");

        build_grid("sip_iax_grid","<?php echo base_url(); ?>accounts/customer_iax_sip_json/<?= $account_data[0]['id']; ?>",<? echo json_encode($sipiax_grid_field) ?>,"");

        build_grid("did_grid","<?php echo base_url(); ?>accounts/customer_did_json/<?= $account_data[0]['id']; ?>",<? echo json_encode($did_grid_fields) ?>,"");

    });
</script>

<? endblock() ?>

<? startblock('page-title') ?>
<?= $page_title ?><br/>
<? endblock() ?>

<? startblock('content') ?>        
<div id="tabs">
    <ul>
        <li><a href="#customer_details">Customer Details</a></li>
        <li><a href="#accounts">Accounts</a></li>
        <li><a href="#packages">Packages</a></li>
        <li><a href="#did">DID</a></li>
        <li><a href="#invoices_payments">Invoices/Payments</a></li>
        <li><a href="#block_prefixes">Block Prefixes</a></li>
    </ul>	

    
    <div id="customer_details">
        <div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">
                 <div class="portlet-header ui-widget-header"><!--< ?php echo isset($account)?"Edit":"Create New";?> Account-->
                     <?=@$page_title?>
                    <span class="ui-icon ui-icon-circle-arrow-s"></span></div>
                    <div style="color:red;margin-left: 60px;">
                        <?php if(isset($validation_errors)){echo $validation_errors; } ?> 
                    </div>
                    <?php echo $form;?>
        </div>
    </div>
    
    <!--Accounts Tab Start-->
    <div id='accounts'>
        <div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">
            <!-- Ip Map Table	 Start-->
            <div class="two-column" style="float:left;width: 100%;">
            <div class="column">
               <div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">      
                    <div class="portlet-header ui-widget-header">IP Mapping<span class="ui-icon ui-icon-circle-arrow-s"></span></div>
                    <div class="portlet-content">            
                        <!--<div class="hastable" style="margin-bottom:10px;">-->
                              <table id="ipmap_grid" align="left" style="display:none;"></table> 
                        <!--</div>-->
                        <div class="content-box content-box-header ui-corner-all float-left full">
                            <div class="ui-state-default ui-corner-top ui-box-header">
                                <span class="ui-icon float-left ui-icon-signal"></span>IP MAP
                            </div>
                            <div class="content-box-wrapper"> 
                                <div class="sub-form">
                                    <form method="post" name="ip_map" id="ip_map" action="<?=base_url()?>accounts/account_detail_add" enctype="multipart/form-data">
                                        <input type="hidden" name="accountnum" value="<?=$account_data[0]['id']?>" />
                                        <div><label class="desc">IP</label><input class="text field large" name="ip" size="16" type="text"></div>
                                        <div><label class="desc">Prefix</label><input class="text field large" name="prefix" size="16" type="text"></div>			
                                        <div><label class="desc">Context</label><input class="text field large" name="ipcontext" size="16" type="text"></div>
                                        <div style="margin-top:14px; width:60px;"><input class="ui-state-default ui-corner-all ui-button" name="action" value="Map IP" type="submit"></div>
                                    </form>
                                </div>
                            </div>	      
                        </div>
                    </div>           
                </div>
            </div>
        <!-- Ip Map Table	 Completed-->                              
	<!-- ANI/CLID Table Starts -->
	<div class="column column-right">
	  <!--<div class="column">        -->
	  <div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">
	  <div class="portlet-header ui-widget-header">ANI Prefix Mapping<span class="ui-icon ui-icon-circle-arrow-s"></span></div>
	  <div class="portlet-content">          
          <!--<div class="hastable" style="margin-bottom:10px;">-->
              <table id="ANI_map_grid" align="left" style="display:none;"></table>              
          <!--</div>-->
            <div class="content-box content-box-header ui-corner-all float-left full" style="position:relative; z-index:9999">
                  <div class="ui-state-default ui-corner-top ui-box-header">
                      <span class="ui-icon float-left ui-icon-signal"></span>Map ANI
                  </div>
                  <div class="content-box-wrapper"> 
                      <div class="sub-form">   
                          <form method="post" action="<?=base_url()?>accounts/account_detail_add" enctype="multipart/form-data">
                            <input type="hidden" name="accountnum" value="<?=$account_data[0]['id']?>" />     
                            <div><label class="desc">ANI</label><input class="text field large" name="ANI" size="20" type="text"></div>
                            <div><label class="desc">Context</label><input class="text field large" name="context" size="20" type="text"></div>
                            <div style="margin-top:14px;"><input class="ui-state-default ui-corner-all ui-button" name="action" value="Map ANI" type="submit"></div>
                          </form>  
                      </div>
                  </div>
             </div>
          </div>
          </div>       
        </div>        <!-- ANI/CLID Table	 Completed-->    
        </div>       
        <!--   IAX & SIP Table Starts   -->
        <div class="two-column" style="float:left;width: 100%;">
        <div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">
            <div class="portlet-header ui-widget-header">IAX2SIP<span class="ui-icon ui-icon-circle-arrow-s"></span></div>
              <div class="portlet-content">          
                  <table id="sip_iax_grid" align="left" style="display:none;"></table>                 
              </div>
            </div>
        </div>
        </div>
        <!--   IAX & SIP Table End   --> 
        </div>
    <!--Account Tab End-->
    
    <!-- Package table started -->
    <div id='packages'>
        <div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">
            <div class="two-column" style="float:left;width: 100%;">
                <div class="column">            
                    <div class="portlet-header ui-widget-header">Charges<span class="ui-icon ui-icon-circle-arrow-s"></span></div>
                    <div class="portlet-content">
                        <div style="margin-bottom:10px;">
                            <table id="chrges_grid" align="left" style="display:none;"></table>    
                        </div>
                    </div>
                </div>
                <!-- Post charge table started -->
                <div class="column column-right">
                     <div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">
                       <div class="portlet-header ui-widget-header">Charge To ADD Account<span class="ui-icon ui-icon-circle-arrow-s"></span></div>
                       <div class="portlet-content">      
                        <!-- charge table completed -->
                        <div class="content-box content-box-header ui-corner-all float-left full">
                            <div class="ui-state-default ui-corner-top ui-box-header">
                                <span class="ui-icon float-left ui-icon-signal"></span>
                                Add Charge
                            </div>
                            <div class="content-box-wrapper">
                                <form method="post" action="<?= base_url() ?>accounts/account_detail_add" enctype="multipart/form-data">
                                    <input name="mode" value="View Details" type="hidden">
                                    <input type="hidden" name="accountnum" value="<?//= $account_data->number ?>" />
                                    <div class="sub-form">
                                        <div>
                                            <label class="desc">Applyable Charges</label>
                                            <select class="select field large" name="applyable_charges"><? //=$applyable_charges  ?>
                                             <?php foreach($chargelist as $key => $value){ ?>
                                                    <option value="<?=$key?>"><?=$value?></option>
                                             <?}?>
                                            </select>
                                        </div>
                                        <div style="margin-top:14px;">
                                            <input class="ui-state-default ui-corner-all ui-button" name="action" value="Add Charge..." type="submit">
                                        </div>
                                    </div>
                                </form>
                            </div>
                          </div>     
                          <!-- Charge table completed -->
                           <!-- Post charge table started -->
                           <div class="content-box content-box-header ui-corner-all float-left full">
                                 <div class="ui-state-default ui-corner-top ui-box-header">
                                     <span class="ui-icon float-left ui-icon-signal"></span>POST Charge
                                 </div>
                                 <div class="content-box-wrapper"> 
                                 <div class="sub-form">
                               <form method="post" action="<?=base_url()?>accounts/account_detail_add" enctype="multipart/form-data">
                                 <input type="hidden" name="accountnum" value="<?=$account_data[0]['id']?>" />
                                 <div><label class="desc">Description</label><input class="text field large" name="desc" size="16" type="text"></div>
                                 <div><label class="desc">Amount</label><input class="text field large" name="amount" size="8" type="text"></div>
                                 <div style="margin-top:14px;"><input class="ui-state-default ui-corner-all ui-button" name="action" value="Post Charge..." type="submit"></div>
                                 </form>
                                 </div>
                                 </div>
                            </div>
                           <!-- Post charge table completed -->
                       </div>
                     </div>         
               </div>
            </div>
        </div>
    </div>
    
    
    <div id='did'>
     <div class="two-column" style="float:left;width: 100%;">
        <div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">
            <div class="portlet-header ui-widget-header">DIDs<span class="ui-icon ui-icon-circle-arrow-s"></span></div>
                <div class="column">            
                    <div class="portlet-header ui-widget-header">Charges<span class="ui-icon ui-icon-circle-arrow-s"></span></div>
                    <div class="portlet-content">
                        <div style="margin-bottom:10px;">
                            <table id="did_grid" align="left" style="display:none;"></table>                 
                        </div>
                    </div>
                </div>
                <div class="column column-right">
                    <div class="content-box content-box-header ui-corner-all float-left full">
                        <div class="ui-state-default ui-corner-top ui-box-header">
                            <span class="ui-icon float-left ui-icon-signal"></span>Purchase DIDs</div>
                        <div class="content-box-wrapper"> 
                            <form method="post" action="<?=base_url()?>accounts/account_detail_add" enctype="multipart/form-data">
                                <input type="hidden" name="accountnum" value="<?//= $account_data->number ?>" />
                                <div class="sub-form">
                                    <div>
                                        <label class="desc">Available DIDs</label>
                                        <select class="select field large" name="applyable_charges"><? //=$applyable_charges  ?>
                                         <?php foreach($chargelist as $key => $value){ ?>
                                                <option value="<?=$key?>"><?=$value?></option>
                                         <?}?>
                                        </select>
                                    </div>
                                    <div>
                                        <div style="margin-top:19px;"><input class="ui-state-default ui-corner-all ui-button" name="action" value="Purchase DID" type="submit"></div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                  </div>            
            </div>
        </div>
    </div>

<!--  Charges table completed -->


</div>
<? endblock() ?>	
<? startblock('sidebar') ?>
Filter by
<? endblock() ?>
<? end_extend() ?>  
