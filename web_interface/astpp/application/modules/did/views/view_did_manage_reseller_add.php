<script type="text/javascript" src="/js/validate.js"></script>

<script type="text/javascript">

    $(document).ready(function() {

        // validate signup form on keyup and submit

        $("#frm_manage_did").validate({

            rules: {
                number: "required",
                limittime: "required"
            }

        });

    });

</script>
<? extend('master.php') ?>
<?php error_reporting(E_ERROR); ?>
<? startblock('extra_head') ?>
<script type="text/javascript">

</script>
<?php endblock() ?>
<?php startblock('page-title') ?>
<?= $page_title ?>
<br/>
<?php endblock() ?>
<?php startblock('content') ?>


<div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">                        

    <div class="portlet-header ui-widget-header"><?= isset($did) ? "Edit" : "Add New" ?> DID<span class="ui-icon ui-icon-circle-arrow-s"></span></div>

    <div class="portlet-content">

        <form action="<?= base_url() ?><?= isset($did) ? "did/did_reseller_edit/edit" : "did/did_reseller_edit/add" ?>" id="frm_manage_did" method="POST" enctype="multipart/form-data">

            <ul style="width:50%;">        
                <li class="input_marg">
                    <label class="desc">Number :</label>
                    <?php if (isset($did)) { ?>
                        <label class="value_bold"><?= @$did ?></label>
                        <input type="hidden" name="number" value="<?= @$did ?>" />
                    <?php } ?>
                </li>      
                <li class="input_marg">
                    <label class="desc">Country :</label>
                    <label class="value_bold"><?= @$didinfo['country'] ?></label>
                    <input type="hidden" name="country" value="<?= @$didinfo['country'] ?>" />
                </li>        

                 <li class="input_marg">
                    <label class="desc">Province :</label>     
                    <label class="value_bold"><?= @$didinfo['province'] ?></label>
                    <input type="hidden" name="province" value="<?= @$didinfo['province'] ?>" />
                </li>

                <li class="input_marg">
                    <label class="desc">City :</label>
                    <label class="value_bold"><?= @$didinfo['city'] ?></label>        
                    <input type="hidden" name="city" value="<?= @$didinfo['city'] ?>" />
                </li>       	
                 <li class="input_marg">
                    <label class="desc">Provider :</label>
                    <label class="value_bold"><?= @$didinfo['provider'] ?></label>
                    <input type="hidden" name="Provider" value="<?= @$didinfo['provider_id'] ?>" />
                    <input type="hidden" name="did_id" value="<?= @$didinfo['id'] ?>" />
                </li>

                <li class="input_marg">
                    <label class="desc">Account :</label>        
                    <label class="value_bold"><?
                    if ($didinfo['accountid'] == '0') {
                        echo "";
                    }
                    ?></label>        
                </li>        
                 <li class="input_marg">
                    <label class="desc">Dialstring :</label>
                    <label class="value_bold">&nbsp;<?= @$didinfo['extensions'] ?></label>
                    <input type="text" class="text field" name="extension"  size="20"  value="<?= @$reseller_didinfo['extensions'] ?>" />
                </li>
                 <li class="input_marg">
                    <label class="desc">Setup Fee :</label>
                    <label class="value_bold">&nbsp;<?= @$didinfo['setup'] ?></label>
                    <input type="text" class="text field" name="setup"  size="20"   value="<?= @$reseller_didinfo['setup'] ?>"/>
                </li>
                <li class="input_marg">
                    <label class="desc">Disconnection Fee:</label>
                    <label class="value_bold">&nbsp;<?= @$didinfo['disconnectionfee'] ?></label>
                    <input type="text" class="text field" name="disconnectionfee"  size="20"  value="<?= @$reseller_didinfo['disconnectionfee'] ?>" />
                </li>     
                 <li class="input_marg">
                    <label class="desc">Monthly :</label>
                    <label class="value_bold">&nbsp;<?= @$didinfo['monthlycost'] ?></label>
                    <input type="text" class="text field" name="monthlycost"  size="20"  value="<?= @$reseller_didinfo['monthlycost'] ?>" />
                </li>        
                 <li class="input_marg">
                    <label class="desc">Connect :</label>
                    <label class="value_bold">&nbsp;<?= @$didinfo['connectcost'] ?></label>
                    <input type="text" class="text field" name="connectcost"  size="20"  value="<?= @$reseller_didinfo['connectcost'] ?>"/>
                </li>        
                 <li class="input_marg">
                    <label class="desc">Included :</label>
                    <label class="value_bold">&nbsp;<?= @$didinfo['includedseconds'] ?></label>
                    <input type="text" class="text field" name="included"  size="20"  value="<?= @$reseller_didinfo['includedseconds'] ?>"/>
                </li>        
                 <li class="input_marg">
                    <label class="desc">Cost :</label>
                    <label class="value_bold">&nbsp;<?= @$didinfo['cost'] ?></label>
                    <input type="text" class="text field" name="cost"  size="20"  value="<?= @$reseller_didinfo['cost'] ?>"/>
                </li>        
                 <li class="input_marg">
                    <label class="desc">Increments :</label>
                    <label class="value_bold">&nbsp;<?= @$didinfo['inc'] ?></label>
                    <input type="text" class="text field" name="inc"  size="20"  value="<?= @$reseller_didinfo['inc'] ?>"/>
                </li>                


                 <li class="input_marg">
                    <label class="desc">Prorate :</label>
                    <label class="value_bold">&nbsp;<?php if (@$didinfo['prorate'] == 1) {
                        echo "YES";
                    } else {
                        echo "NO";
                    } ?></label>
                    <select name="prorate" class="select field small" >
                        <option value="1" <?php if (@$reseller_didinfo['prorate'] == "1") {
                        echo "selected='selected'";
                    } ?> >YES</option>
                        <option value="0" <?php if (@$reseller_didinfo['prorate'] == "0") {
                        echo "selected='selected'";
                    } ?> >NO</option>
                    </select>
                </li>

                 <li class="input_marg">
                    <label class="desc">Dial As :</label>
                    <label class="value_bold">&nbsp;<?= @$didinfo['dial_as'] ?></label>
                    <input type="text" class="text field" name="dial_as"   value="<?= @$did['reseller_didinfo'] ?>"/>
                </li>                        
            </ul>        

            <div style="width:100%;float:left;height:50px;margin-top:20px;">
                <input type="submit" class="ui-state-default float-right ui-corner-all ui-button" name="action" value="<?= isset($did) ? "Save..." : "Insert..."; ?>" /> 
            </div>
        </form>
    </div>
</div>
<? endblock() ?>
<? startblock('sidebar') ?>
<? endblock() ?>
<? end_extend() ?>