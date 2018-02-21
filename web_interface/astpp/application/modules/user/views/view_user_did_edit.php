<div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">                        
    <div class="portlet-header ui-widget-header">Edit Did<span class="ui-icon ui-icon-circle-arrow-s"></span></div>
    <div class="portlet-content">
        <form action="<?= base_url() ?>user/user_dids_action/edit/" id="frm_callshop" method="POST" enctype="multipart/form-data">
            <ul style="width:600px;">
                <input type="hidden" readonly name="didid" id="didid" value="<?= @$didinfo['id']; ?>">
                <li style="line-height:30px;">   
                    <span style="width:130px;">Number:</span>     
                    <label><?= @$didinfo['number'] ?></label> 
                </li>
                <li style="line-height:30px;">   
                   <span style="width:130px;">Country:</span>
                    <label>  <?= @$didinfo['country'] ?> </label>
                </li>
                <li style="line-height:30px;">   
                    <span style="width:130px;">Province</span>
                    <label><?= @$didinfo['province'] ?> </label>
                </li>
                <li style="line-height:30px;">   
                   <span style="width:130px;">City</span>
                    <label><?= @$didinfo['city'] ?></label>
                </li>
                <li style="line-height:30px;">   
                   <span style="width:130px;">Increment:</span>
                    <label><?= @$didinfo['inc'] ?> </label>
                </li><li style="line-height:30px;">   
                   <span style="width:130px;">Cost:</span>
                    <label><?= @$didinfo['cost'] ?> </label>
                </li>                
                <li style="line-height:30px;">   
                    <span style="width:130px;">Included Second:</span>
                    <label><?= @$didinfo['includedseconds'] ?>  </label>
                </li>
                <li style="line-height:30px;">   
                   <span style="width:130px;">Setup Fee:</span>
                    <label><?= @$didinfo['setup'] ?> </label>
                </li>
                <li style="line-height:30px;">      
                    <span style="width:130px;">Monthly fee:</span>
                    <label>  <?= @$didinfo['monthlycost'] ?>  </label>
                </li>
                <li style="line-height:30px;">   
                   <span style="width:130px;">Connection Fee:</span>
                    <label><?= @$didinfo['connectcost'] ?> </label>
                </li>
                <li style="line-height:30px;">   
                   <span style="width:130px;">Disconnection Fee:</span>
                    <label><?= @$didinfo['disconnectionfee'] ?> </label>
                </li>
                <li style="line-height:30px;">   
                   <span style="width:130px;">Call Type</span>
                   <label style="margin-left:-10px;"><? echo $call_type; ?></label>
                </li>
                <li style="line-height:30px;">   
                    <span style="width:130px;">Dialstring</span>
                     <label  style="margin-left:-10px;"><input size="20" class="text field medium" name="extension" value="<?= @$didinfo['extensions'] ?>"></label>
                </li>
            </ul>
            <input type="submit" class="ui-state-default float-right ui-corner-all ui-button" name="action" value="Save..." />
            <br>
            <br><hr>
            <TMPL_VAR NAME= "status">
        </form>
    </div>
</div>
