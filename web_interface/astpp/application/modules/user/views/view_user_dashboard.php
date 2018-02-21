<? extend('master.php') ?>	
<? startblock('page-title') ?>       
<? endblock() ?>    
<? startblock('content') ?>  
<div class="inner-page-title">			
    <h2>Account Details</h2>
</div>

<div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">
    <div class="portlet-content">
        <div class="hastable">
<!--<a href="<?//=base_url()?>user/user_payment/">payment</a>-->            
            <table class="details_table">  
                <tr>
                    <th>Account Number</th><td><?= $account['number'] ?></td>
                    <th>Balance</th><td><?= ($account['balance']); ?></td>
                    <th>Account Type</th><td><?= ucfirst(Common_model::$global_config['userlevel'][$account['type']]); ?></td>
                    <th>Name</th><td><?= $account['first_name'] . " " . $account['last_name'] ?></td>
                </tr>
                <tr>
                    <th>Company</th><td><?= $account['company_name'] ?></td>
                    <th>Address</th><td><?= $account['address_1'] ?></td>
                    <th>City</th><td><?= $account['city'] ?></td>
                    <th>Email</th><td><?= $account['email'] ?></td>
                </tr>
                <tr>
                    <th>Province/State</th><td><?= $account['province'] ?></td>
                    <th>Zip/Postal Code</th><td><?= $account['postal_code'] ?></td>
                    <th>Country</th><td><?= $account['country_id'] ?> </td>
                    <th>Billing Schedule</th><td><?= ucfirst(Common_model::$global_config['sweeplist'][$account['sweep_id']]); ?></td>
                </tr>
                <tr>
                    <th>Credit Limit in</th><td><?= $account['credit_limit'] ?></td>
                    <th>Timezone</th><td><?= $account['timezone_id'] ?></td>      
                    <th>Max Channels</th><td><?= $account['maxchannels'] ?></td>
                    <th>Telephone</th><td><?= $account['telephone_1'] ?></td>
                </tr>
            </table>
        </div>
        <div class="clear"></div>
        <a href="<?= base_url() ?>user/user_payment/"> 
            <input class="ui-state-default float-right ui-corner-all ui-button" type="submit" name="payment" value="Recharge Account" />
        </a>   
        <a href="<?= base_url() ?>user/user_edit_account/">
            <input class="ui-state-default float-right ui-corner-all ui-button" rel="facebox"  type="submit" name="action" value="Edit Account" />
        </a>
        <div class="clear"></div>
    </div>
</div>    
<div class="clear"></div>				
<? endblock() ?>
<? startblock('sidebar') ?>
<? endblock() ?>
<? end_extend() ?>  
