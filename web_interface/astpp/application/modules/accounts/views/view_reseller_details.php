<? extend('master.php') ?>
<? startblock('extra_head') ?>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/module_js/generate_grid.js"></script>
<script type="text/javascript" language="javascript">
    $(document).ready(function() {
        $('#tabs').tabs();
        build_grid("chrges_grid","<?php echo base_url(); ?>accounts/reseller_details_json/package/<?= $account_data[0]['id']; ?>",<? echo $charges_grid_field ?>,"");

        build_grid("did_grid","<?php echo base_url(); ?>accounts/reseller_details_json/did/<?= $account_data[0]['id']; ?>",<? echo $did_grid_fields ?>,"");

        build_grid("invoice_grid","<?php echo base_url(); ?>accounts/reseller_details_json/invoices/<?= $account_data[0]['id']; ?>",<? echo $invoice_grid_fields ?>,"");

        build_grid("cdrs_grid","<?php echo base_url(); ?>accounts/customer_details_json/reports/<?= $account_data[0]['id']; ?>",<? echo $cdrs_grid_fields ?>,"");        
    
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
        <li><a href="#customer_details">Reseller Details</a></li>
        <li><a href="#packages">Charges</a></li>
        <li><a href="#did">DID</a></li>
        <li><a href="#invoices">Invoices/Payments</a></li>
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
                    <div class="content-box content-box-header ui-corner-all float-left full">
                        <div class="portlet-header ui-widget-header">Post Charges<span class="ui-icon ui-icon-circle-arrow-s"></span></div>
                        <div class="portlet-content">
                            <form method="post" action="<?= base_url() ?>accounts/customer_add_postcharges/reseller/<?= $account_data[0]['id'] ?>" enctype="multipart/form-data">
                                <div class="sub-form">
                                    <div><label class="desc">Description</label><input class="text field large" name="desc" size="16" type="text"></div>
                                    <div><label class="desc">Amount</label><input class="text field large" name="amount" size="8" type="text"></div>
                                    <div style="margin-top:12px;"><input class="ui-state-default ui-corner-all ui-button" name="action" value="Post Charge..." type="submit"></div>
                                </div>
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
                                    <form method="post" action="<?= base_url() ?>accounts/customer_charges_action/add/<?= $account_data[0]['id'] ?>/reseller/" enctype="multipart/form-data">
                                        <div class="sub-form">
                                            <div style="width:50%;">
                                                <label class="desc">Applyable Charges</label>
<? echo $chargelist; ?>
                                            </div>
                                            <div style="margin-top:1px;">
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


    <div id='did'>
        <div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">
            <div class="two-column" style="float:left;width: 100%;">

                <div class="portlet-header ui-widget-header">DIDs<span class="ui-icon ui-icon-circle-arrow-s"></span></div>
                <div class="portlet-content">
                    <div style="margin-bottom:10px;">
                        <table id="did_grid" align="left" style="display:none;"></table>                 
                    </div>
                </div>
                <div class="content-box content-box-header ui-corner-all float-left full" style="margin-left: 15px;width:98%;">
                    <div class="ui-state-default ui-corner-top ui-box-header">
                        <span class="ui-icon float-left ui-icon-signal"></span>Purchase DIDs</div>
                    <div class="content-box-wrapper"> 
                        <form method="post" action="<?= base_url() ?>accounts/customer_dids_action/add/<?= $account_data[0]['id'] ?>/reseller/" enctype="multipart/form-data">
                            <div class="sub-form">
                                <div style="width:25%">
                                    <label class="desc">Available DIDs</label>
<? echo $didlist; ?>
                                </div>
                                <div>
                                    <div style="margin-top:1px;"><input class="ui-state-default ui-corner-all ui-button" name="action" value="Purchase DID" type="submit"></div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>            
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
