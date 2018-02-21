<? extend('master.php') ?>
<? startblock('extra_head') ?>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/module_js/generate_grid.js"></script>
<script type="text/javascript" language="javascript">
    $(document).ready(function() {
        $('#tabs').tabs();
        build_grid("invoices_grid","<?php echo base_url(); ?>accounts/provider_details_json/invoices/<?= $account_data[0]['id']; ?>",<? echo $invoice_grid_fields ?>,"");

        build_grid("cdrs_grid","<?php echo base_url(); ?>accounts/customer_details_json/reports/<?= $account_data[0]['id']; ?>",<? echo $cdrs_grid_fields ?>,"");        

        build_grid("ipmap_grid","<?php echo base_url(); ?>accounts/customer_ipmap_json/<?= $account_data[0]['id']; ?>/provider/",<? echo $ipmap_grid_field ?>,"");

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
<? endblock() ?>

<? startblock('page-title') ?>
<?= $page_title ?><br/>
<? endblock() ?>

<? startblock('content') ?>        
<div id="tabs">
    <ul>
        <li><a href="#customer_details">Provider Details</a></li>
        <li><a href="#accounts">IP Settings</a></li>        
        <li><a href="#invoices">Invoices</a></li>
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
                                    <form method="post" name="ip_map" id="ip_map" action="<?= base_url() ?>accounts/customer_ipmap_action/add/<?= $account_data[0]['id'] ?>/provider/" enctype="multipart/form-data">
                                        <div><label>Name: </label><input class="text field large" name="name" size="16" type="text"></div>                                        
                                        <div><label>IP: </label><input class="text field large" name="ip" size="16" type="text"></div>
                                        <div style="width:60px;"><input class="ui-state-default ui-corner-all ui-button" name="action" value="Map IP" type="submit"></div>
                                    </form>
                                </div>
                            </div>	      
                        </div>
                    </div>           
                </div>
            </div>

        </div>
        <!--   IAX & SIP Table End   --> 
    </div>
    <!--Account Tab End-->

    
    <div id='invoices'>
        <div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">
            <div class="two-column" style="float:left;width: 100%;">
                <!--                <div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">-->
                <div class="portlet-header ui-widget-header">Invoices<span class="ui-icon ui-icon-circle-arrow-s"></span></div>
                <div class="portlet-content">          
                    <table id="invoices_grid" align="left" style="display:none;"></table>                 
                </div>
                <!--                </div>-->
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
