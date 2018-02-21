<? extend('master.php') ?>	
<? startblock('page-title') ?>       
<? endblock() ?>    
<? startblock('content') ?>  
<div class="inner-page-title">			
    <h2>Dashboard</h2>
</div>

<div class="clear"></div>
<? if ($this->session->userdata['logintype'] == '1' || $this->session->userdata['logintype'] == '2') { ?>                
    <div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">
        <div class="portlet-header ui-widget-header">System Overview<span class="ui-icon ui-icon-circle-arrow-s"></span></div>
        <div class="portlet-content">
            <div class="hastabledasb">
                <table class="default" width='60%'>
                    <tr class="rowone"  style="background:#4476AF;" >

                        <td><img src="<?= base_url() ?>assets/images/dashboardicons/admins-icon.png" width="24" height="24" /><div class="summary_title">Admins</div></td>
                        <td><img src="<?= base_url() ?>assets/images/dashboardicons/resellers-icon.png" width="24" height="24" /><div class="summary_title">Resellers</div></td>
                        <td ><img src="<?= base_url() ?>assets/images/dashboardicons/customers-icon.png" width="24" height="24" /><div class="summary_title">Customers</div></td>
                        <td><img src="<?= base_url() ?>assets/images/dashboardicons/call shops-icon.png" width="24" height="24" /><div class="summary_title">Call Shops</div></td>
                        <td><img src="<?= base_url() ?>assets/images/dashboardicons/providers-icon.png" width="24" height="24" /><div class="summary_title">Providers</div></td>
                    </tr>
                    <tr class="rowone">
                        <td><?//= @$admin_count ?></td>
                        <td><?//= @$reseller_count ?></td>
                        <td><?//= @$customer_count ?></td>
                        <td><?//= @$callshop_count ?></td>
                        <td><?//= @$vendor_count ?></td>
                    </tr>
                    <tr class="rowone" style="background:#4476AF;">
                        <td><img src="<?= base_url() ?>assets/images/dashboardicons/funds receivable-icon.png" width="24" height="24" /><div class="summary_title">Funds Receivable</div></td>
                        <td><img src="<?= base_url() ?>assets/images/dashboardicons/funds payable-icon.png" width="24" height="24" /><div class="summary_title">Funds Payable</div></td>
                        <td><img src="<?= base_url() ?>assets/images/dashboardicons/Phone-icon.png" width="24" height="24" /><div class="summary_title">DID Numbers</div></td>
                        <td><img src="<?= base_url() ?>assets/images/dashboardicons/unbilled cdrs-icon.png" width="24" height="24" /><div class="summary_title">Unbilled CDRs</div></td>
                        <td><img src="<?= base_url() ?>assets/images/dashboardicons/uptime.png" width="24" height="24" /><div class="summary_title">system Uptime</div></td>
                <!--     <td>&nbsp;</td> -->
                    </tr>
                    <tr class="rowone">
                        <td><?//= @$total_owing ?></td>
                        <td><?//= @$total_due ?></td>
                        <td align=center><a href="#mode=Manage%20DIDs"><?//= @$dids ?></a></td>
                        <td align=center><a href="#mode=List%20Errors"><?//= @$unbilled_cdrs ?></a></td>
                        <td align=center><a href="#mode=List%20Errors"><? system("uptime | \
sed s/^.*up// | \
awk -F, '{ if ( $3 ~ /user/ ) { print $1 $2 } else { print $1 }}' | \
sed -e 's/:/\ hours\ /' -e 's/ min//' -e 's/$/\ minutes/' | \
sed 's/^ *//'"); ?></a></td>
                <!--     <td>&nbsp;</td> -->
                    </tr>
                    <tr class="rowone" style="background:#4476AF;">
                        <td><img src="<?= base_url() ?>assets/images/dashboardicons/calling cards in use-icon.png" width="24" height="24" /><div class="summary_title">Calling Cards in use</div></td>
                        <td><img src="<?= base_url() ?>assets/images/dashboardicons/calling cards in use-icon.png" width="24" height="24" /><div class="summary_title">Total Active Cards</div></td>
                        <td><img src="<?= base_url() ?>assets/images/dashboardicons/unused card balance-icon.png" width="24" height="24" /><div class="summary_title">Unused Card Balance</div></td>
                        <td><img src="<?= base_url() ?>assets/images/dashboardicons/used card balance-icon.png" width="24" height="24" /><div class="summary_title">Used Card Balance</div></td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr class="rowone">
                        <td align=center><a href="#List%20Cards"><?//= @$calling_cards_in_use ?></a></td>
                        <td align=center><a href="#List%20Cards"><?//= @$calling_cards_active ?></a></td>
                        <td align=center><a href="#List%20Cards"><?//= @$calling_cards_unused ?></a></td>
                        <td align=center><a href="#List%20Cards"><?//= @$calling_cards_used ?></a></td>
                        <td>&nbsp;</td>
                    </tr>

                </table>
            </div>
        </div>
    </div>
    <br/>
<?
}
if ($this->session->userdata['logintype'] == 3) {
    ?>		  
    <span style="font-size:17px;font-weight:bold;color:black;">Upcoming Features : <br/><br/></span>

    <ul >
        <li style="line-height:30px;">1. Detailed Call Report with Graph</li>
        <li style="line-height:30px;">2. Rate Batch update</li>
        <li style="line-height:30px;">3. Quick Search</li>
        <li style="line-height:30px;">4. My Account Modification</li>
        <li style="line-height:30px;">5. Change Password</li>
        <li style="line-height:30px;">6. Support & Ticket Module</li>
        <li style="line-height:30px;">7. View Dashboard</li>
    </ul><br/><br/>
    <?
}
?>

<div class="clear"></div>				
<? endblock() ?>
<? startblock('sidebar') ?>
<? endblock() ?>
<? end_extend() ?>  
