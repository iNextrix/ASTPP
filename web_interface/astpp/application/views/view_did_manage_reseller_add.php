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

<div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">                        

        <div class="portlet-header ui-widget-header"><?=isset($did)?"Edit":"Add New"?> DID<span class="ui-icon ui-icon-circle-arrow-s"></span></div>

        <div class="portlet-content">

        <form action="<?=base_url()?><?=isset($did)?"did/manage/edit":"did/manage/add"?>" id="frm_manage_did" method="POST" enctype="multipart/form-data">

        <ul style="width:600px">        
        <li>
        <label class="desc">Number</label>
		<?php if(isset($did)){?>
        	<label class="desc"><?=@$did?></label>
            <input type="hidden" name="number" value="<?=@$did?>" />
        <?php }?>
        </li>      
        
        <li>
        <label class="desc">Country</label>
       	<label class="desc"><?=@$didinfo['country']?></label>
        </li>        

        <li>
        <label class="desc">Province</label>     
       	<label class="desc"><?=@$didinfo['province']?></label>
        </li>
        
        <li>		
        <label class="desc">City</label>
       	<label class="desc"><?=@$didinfo['city']?></label>        
        </li>       	
        <li>
        <label class="desc">Provider</label>
		<label class="desc"><?=@$didinfo['provider']?></label>
        </li>
              
        <li>
        <label class="desc">Account</label>        
        <label class="desc"><?=@$didinfo['account']?></label>        
        </li>        
         <li>
        <label class="desc">Dialstring</label>
        <label class="desc">&nbsp;<?=@$didinfo['extensions']?></label>
        <input type="text" class="text field medium" name="extension"  size="20"  value="<?=@$reseller_didinfo['extensions']?>" />
        </li>
        <li>
        <label class="desc">Setup Fee</label>
         <label class="desc">&nbsp;<?=@$didinfo['setup']?></label>
        <input type="text" class="text field medium" name="setup"  size="20"   value="<?=@$reseller_didinfo['setup']?>"/>
        </li>
        <li>
        <label class="desc">Disconnection Fee</label>
		<label class="desc">&nbsp;<?=@$didinfo['disconnectionfee']?></label>
        <input type="text" class="text field medium" name="disconnectionfee"  size="20"  value="<?=@$reseller_didinfo['disconnectionfee']?>" />
        </li>        
        <li>
        <label class="desc">Monthly</label>
		<label class="desc">&nbsp;<?=@$didinfo['monthlycost']?></label>
        <input type="text" class="text field medium" name="monthlycost"  size="20"  value="<?=@$reseller_didinfo['monthlycost']?>" />
        </li>        
       <li>
        <label class="desc">Connect</label>
		<label class="desc">&nbsp;<?=@$didinfo['connectcost']?></label>
        <input type="text" class="text field medium" name="connectcost"  size="20"  value="<?=@$reseller_didinfo['connectcost']?>"/>
        </li>        
        <li>
        <label class="desc">Included</label>
		<label class="desc">&nbsp;<?=@$didinfo['includedseconds']?></label>
        <input type="text" class="text field medium" name="included"  size="20"  value="<?=@$reseller_didinfo['includedseconds']?>"/>
        </li>        
        <li>
        <label class="desc">Cost</label>
		<label class="desc">&nbsp;<?=@$didinfo['cost']?></label>
        <input type="text" class="text field medium" name="cost"  size="20"  value="<?=@$reseller_didinfo['cost']?>"/>
        </li>        
         <li>
        <label class="desc">Increments</label>
        <label class="desc">&nbsp;<?=@$didinfo['inc']?></label>
        <input type="text" class="text field medium" name="inc"  size="3"  value="<?=@$reseller_didinfo['inc']?>"/>
        </li>                
          
      
        <li>
        <label class="desc">Prorate</label>
         <label class="desc">&nbsp;<?php if(@$didinfo['prorate']==1) { echo "YES";} else{ echo "NO";}?></label>
        <select name="prorate" class="select field medium" >
        <option value="1" <?php if(@$reseller_didinfo['prorate'] == "1"){ echo "selected='selected'";}?> >YES</option>
        <option value="0" <?php if(@$reseller_didinfo['prorate'] == "0"){ echo "selected='selected'";}?> >NO</option>
        </select>
        </li>
              
        <li>
        <label class="desc">Dial As</label>
         <label class="desc">&nbsp;<?=@$didinfo['dial_as']?></label>
        <input type="text" class="text field medium" name="dial_as"   value="<?=@$did['reseller_didinfo']?>"/>
        </li>                        
        </ul>        

        <div style="width:100%;float:left;height:50px;margin-top:20px;">
        <input type="submit" class="ui-state-default float-right ui-corner-all ui-button" name="action" value="<?=isset($did)?"Save...":"Insert...";?>" /> 
        </div>
        </form>
        </div>
</div>
