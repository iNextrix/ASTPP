<? extend('master.php') ?>
<? startblock('extra_head') ?>
<script type="text/javascript" language="javascript">
    $(document).ready(function() {
        $('#tabs').tabs();
        build_grid("chrges_grid","<?php echo base_url(); ?>accounts/customer_details_json/package/<?= $account_data[0]['id']; ?>",<? echo $charges_grid_field ?>,"");

        build_grid("ipmap_grid","<?php echo base_url(); ?>accounts/customer_ipmap_json/<?= $account_data[0]['id']; ?>/customer/",<? echo json_encode($ipmap_grid_field) ?>,"");

        build_grid("ANI_map_grid","<?php echo base_url(); ?>accounts/customer_animap_json/<?= $account_data[0]['id']; ?>",<? echo json_encode($animap_grid_field) ?>,"");

        build_grid("sip_iax_grid","<?php echo base_url(); ?>accounts/customer_details_json/freeswitch/<?= $account_data[0]['id']; ?>",<? echo $sipiax_grid_field ?>,<? echo $fs_grid_buttons; ?>);

        build_grid("opensips_grid","<?php echo base_url(); ?>accounts/customer_details_json/opensips/<?= $account_data[0]['id']; ?>",<? echo $opensips_grid_field ?>,<? echo $opensips_grid_buttons; ?>);
	
        build_grid("did_grid","<?php echo base_url(); ?>accounts/customer_details_json/did/<?= $account_data[0]['id']; ?>",<? echo $did_grid_fields ?>,"");

        build_grid("invoice_grid","<?php echo base_url(); ?>accounts/customer_details_json/invoices/<?= $account_data[0]['id']; ?>",<? echo $invoice_grid_fields ?>,"");

        build_grid("pattern_grid","<?php echo base_url(); ?>accounts/customer_details_json/pattern/<?= $account_data[0]['id']; ?>",<? echo $pattern_grid_fields ?>, <?= $pattern_grid_buttons ?>);
        
        build_grid("cdrs_grid","<?php echo base_url(); ?>accounts/customer_details_json/reports/<?= $account_data[0]['id']; ?>",<? echo $cdrs_grid_fields ?>,"");        

        $.validator.addMethod('IP4Checker', function(value) {
            var pattern = /^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/;
            return pattern.test(value);
        }, 'Invalid IP address');

        $('#ip_map').validate({
            rules: {
                ip: {
                    required: true,
                    IP4Checker: true
                }
            }
        });
        $("#ani_map").validate({
            rules: {
                ANI: {
                    required: true
                }
            }
        });  

//        $(".invoice_day").hide();
//        $('label[for="Billing Day"]').hide()
        $(".sweep_id").change(function(e){
            if(this.value != 0){
                $.ajax({
                    type:'POST',
                    url: "<?= base_url()?>/accounts/customer_invoice_option",
                    data:"sweepid="+this.value, 
                    success: function(response) {
                        $(".invoice_day").html(response);
                        $('.invoice_day').show();
                        $('label[for="Billing Day"]').show()
                    }
                });
            }else{
                $('label[for="Billing Day"]').hide()
                $('.invoice_day').css('display','none');                
            }
        })


    });
</script>

<style>
    fieldset{
        text-align: center;

    }
    .error{
    float:right;
    margin-left:15px;
    }
</style>	
<? endblock() ?>

<? startblock('page-title') ?>
<?= $page_title ?><br/>
<? endblock() ?>

<? startblock('content') ?>        
<div id="tabs">
    <ul>
        <li><a href="#customer_details">Customer Details</a></li>
        <li><a href="#accounts">IP & SIP Settings</a></li>
        <li><a href="#packages">Charges</a></li>
        <li><a href="#did">DID</a></li>
        <li><a href="#invoices">Invoices</a></li>
        <li><a href="#block_prefixes">Block Prefixes</a></li>
        <li><a href="#cdrs">Cdrs</a></li>
    </ul>	


    <div id="customer_details">
        <div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">
            <div class="portlet-header ui-widget-header"><!--< ?php echo isset($account)?"Edit":"Create New";?> Account-->
                <?= @$page_title ?>
                <span class="ui-icon ui-icon-circle-arrow-s"></span></div>
            <div style="color:red;margin-left: 60px;">
                <?php if (isset($validation_errors)) {
                    echo $validation_errors;
                } ?> 
            </div>
<?php echo $form; ?>
        </div>
    </div>

    <!--Accounts Tab Start-->
    <div id='accounts'>
        <div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">
            <!-- Ip Map Table	 Start-->
            <div class="two-column" style="float:left;width: 100%;">
                <!--             <div class="column"> -->
                <div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">      
                    <div class="portlet-header ui-widget-header">IP Mapping<span class="ui-icon ui-icon-circle-arrow-s"></span></div>
                    <div class="portlet-content">            
                        <!--<div class="hastable" style="margin-bottom:10px;">-->
                        <table id="ipmap_grid" align="left" style="display:none;"></table> 
                        <!--</div>-->
                        <br />
                        <div class="content-box content-box-header ui-corner-all float-left full">
                            <div class="ui-state-default ui-corner-top ui-box-header">
                                <span class="ui-icon float-left ui-icon-signal"></span>IP MAP
                            </div>
                            <div class="content-box-wrapper"> 
                                <div class="sub-form">
                                    <form method="post" name="ip_map" id="ip_map" action="<?= base_url() ?>accounts/customer_ipmap_action/add/<?= $account_data[0] ['id'] ?>/customer/" enctype="multipart/form-data">
                                        <div><label>Name: </label><input class="text field large" name="name" size="16" type="text"></div>                                        
                                        <div><label>IP: </label><input class="text field large" name="ip" size="16" type="text"></div>
                                        <div><label>Prefix: </label><input class="text field large" name="prefix" size="16" type="text"></div>			
                                        <div style="width:20%"><label>Rate Group: </label><?= $ip_pricelist; ?></div>
                                        <div style="width:60px;"><input class="ui-state-default ui-corner-all ui-button" name="action" value="Map IP" type="submit"></div>
                                    </form>
                                </div>
                            </div>	      
                        </div>
                    </div>           
                </div>
            </div>

	<!-- ANI/CLID Table Starts -->
	<div class="two-column" style="float:left;width: 100%;">
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
                          <form method="post" name="ani_map" id="ani_map" action="<?=base_url()?>accounts/customer_animap_action/add/<?=$account_data[0]['id']?>" enctype="multipart/form-data">
                            <div  style="width:20%"><label>ANI </label><input class="text field large" name="ANI" id="ANI" size="20" type="text"></div>
                            <div><input class="ui-state-default ui-corner-all ui-button" name="action" value="Map ANI" type="submit"></div>
                          </form>  
                      </div>
                  </div>
             </div>
          </div>
          </div>       
        </div>        <!-- ANI/CLID Table	 Completed-->    
            
            
            <!--   IAX & SIP Table Starts   -->
            <div class="two-column" style="float:left;width: 100%;">
                <div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">
                    <div class="portlet-header ui-widget-header">
                        <?
                        if (common_model::$global_config['system_config']['opensips'] == 0) {
                            echo "SIP Devices";
                        } else {
                            echo "Opensips Devices";
                        }
                        ?>
			<span id="error_msg" class=" success"></span>
                        <span class="ui-icon ui-icon-circle-arrow-s"></span></div>
                    <div class="portlet-content">          
                        <? if (common_model::$global_config['system_config']['opensips'] == 0) { ?>
                            <table id="sip_iax_grid" align="left" style="display:none;"></table>        
                        <? } else { ?>
                            <table id="opensips_grid" align="left" style="display:none;"></table>                 
                        <? } ?>
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
                <div class="column column-right" style="padding: 5px 7px 0px 0px">
                    <div class="content-box content-box-header ui-corner-all float-left full">
                        <div class="portlet-header ui-widget-header">Post Charges<span class="ui-icon ui-icon-circle-arrow-s"></span></div>
                        <div class="portlet-content">
                            <div class="sub-form">
                                <form method="post" action="<?= base_url() ?>accounts/customer_add_postcharges/customer/<?= $account_data[0]['id'] ?>" enctype="multipart/form-data">
                                    <div><label class="desc">Description</label><input class="text field large" name="desc" size="16" type="text"></div>
                                    <div><label class="desc">Amount</label><input class="text field large" name="amount" size="8" type="text"></div>
                                    <div style="margin-top:12px;"><input class="ui-state-default ui-corner-all ui-button" name="action" value="Post Charge..." type="submit"></div>
                                </form>
                            </div>                            
                        </div>
                    </div>
                    <!-- Post charge table completed -->
                </div>
                <!-- Post charge table started -->
                <div class="two-column" style="float:left;width: 100%;">
                    <div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">
                        <div class="portlet-header ui-widget-header">Charge To ADD Account<span class="ui-icon ui-icon-circle-arrow-s"></span></div>
                        <div class="portlet-content">      
                            <!-- charge table completed -->
                            <div class="column">
                                <div class="content-box content-box-header ui-corner-all float-left full">
                                    <div class="ui-state-default ui-corner-top ui-box-header">
                                        <span class="ui-icon float-left ui-icon-signal"></span>
                                        Add Charge
                                    </div>
                                    <div class="content-box-wrapper">
                                        <form method="post" action="<?= base_url() ?>accounts/customer_charges_action/add/<?= $account_data[0]['id'] ?>/customer/" enctype="multipart/form-data">
                                            <div class="sub-form">
                                                <div style="width:50%">
                                                    <label class="desc">Applyable Charges</label>
                                                    <? echo $chargelist; ?>
                                                </div>
                                                <div>
                                                    <input class="ui-state-default ui-corner-all ui-button" name="action" value="Add Charge..." type="submit">
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>     
                            </div>
                            <!-- Charge table completed -->
                        </div>
                    </div>         
                </div>
            </div>
        </div>
    </div>
    <div id='did'>
        <div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">
            <div class="two-column" style="float:left;width: 100%;">
                <div class="portlet-header ui-widget-header">DIDs<span class="ui-icon ui-icon-circle-arrow-s"></span></div>
                <!--<div class="column">-->            
<!--                     <div class="portlet-header ui-widget-header">Charges<span class="ui-icon ui-icon-circle-arrow-s"></span></div> -->
                <div class="portlet-content">
                    <div style="margin-bottom:10px;">
                        <table id="did_grid" align="left" style="display:none;"></table>                 
                    </div>
                </div>
                <!--</div>-->
                <!--<div class="column column-right">-->
                <div class="content-box content-box-header ui-corner-all float-left full" style="margin-left: 15px;width:98%;">
                    <div class="ui-state-default ui-corner-top ui-box-header">
                        <span class="ui-icon float-left ui-icon-signal"></span>Purchase DIDs</div>
                    <div class="content-box-wrapper"> 
                        <form method="post" action="<?= base_url() ?>accounts/customer_dids_action/add/<?= $account_data[0]['id'] ?>/customer/" enctype="multipart/form-data">
                            <div class="sub-form">
                                <div style="width:20%;">
                                    <label class="desc">Available DIDs</label>
                                    <? echo $didlist; ?>
                                </div>
                                <div>
                                    <div><input class="ui-state-default ui-corner-all ui-button" name="action" value="Purchase DID" type="submit"></div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <!--</div>-->            
            </div>
        </div>
    </div>

    <!--  Charges table completed -->
    <div id='invoices'>
        <div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">
            <div class="two-column" style="float:left;width: 100%;">
                <div class="portlet-header ui-widget-header">Invoice List<span class="ui-icon ui-icon-circle-arrow-s"></span></div>
                <div class="portlet-content">          
                    <table id="invoice_grid" align="left" style="display:none;"></table>                 
                </div>
            </div>
        </div>
    </div>

    <div id='block_prefixes'>
        <div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">
            <div class="two-column" style="float:left;width: 100%;">
                <div class="portlet-header ui-widget-header">Block Prefix List<span class="ui-icon ui-icon-circle-arrow-s"></span></div>
                <div class="portlet-content">          
                    <table id="pattern_grid" align="left" style="display:none;"></table>                 
                </div>
            </div>
        </div>
    </div>
    <div id='cdrs'>
        <div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">
            <div class="two-column" style="float:left;width: 100%;">
                <div class="portlet-header ui-widget-header">CDRs Report<span class="ui-icon ui-icon-circle-arrow-s"></span></div>
                <div class="portlet-content">          
                    <table id="cdrs_grid" align="left" style="display:none;"></table>                 
                </div>
            </div>
        </div>
    </div>

</div>
<? endblock() ?>	
<? startblock('sidebar') ?>
Filter by
<? endblock() ?>
<? end_extend() ?>  
