<? extend('master.php') ?>
<?php error_reporting(E_ERROR);?>
<? startblock('extra_head') ?>
<script type="text/javascript">
		$().ready(function() {
		
		// validate signup form on keyup and submit
		$("#createAccount").validate({
			rules: {
				customnum: {
					required: true,
					minlength: 5
				},
				credit_limit: "required",
				context: "required",
				accountpassword: {
					required: true,
					minlength: 6
				},
				email: {
					required: true,
					email: true
				},
				firstname: {
					required: true
				}
			}
		});
		});
	</script>
<style>
fieldset {
	width: 609px;
}
</style>
<?php endblock() ?>
<?php startblock('page-title') ?>
<?=$page_title?>
<br/>
<?php endblock() ?>
<?php startblock('content')?>

<div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">
  <div class="portlet-header ui-widget-header"><!--< ?php echo isset($account)?"Edit":"Create New";?> Account-->
    <?=@$page_title?>
    <span class="ui-icon ui-icon-circle-arrow-s"></span></div>
  <div class="portlet-content">
    <form action="<?php echo base_url();?><?=($flag =="create")?"accounts/create/":"accounts/edit/"?>" id="createAccount" method="POST" enctype="multipart/form-data">
      <div style="margin:0 auto; width:1050px">
      <div style="width:500px; float:left">
        <div style="width:500px; margin-bottom:10px;">
          <fieldset  style="width:500px;">
            <legend><span style="font-size:14px; font-weight:bold; color:#000; padding:5px;"> Client Panel Access </span></legend>
            <div class="content-box-wrapper">
              <ul>
                <li>
                  <label class="desc">Account Number:</label>
                  <input name="<?php echo ($flag=='create')?"customnum":"item";?>" type="text" class="text field medium" id="customnum" <?=($flag =="create")?"":"readonly='readonly'"?>  value="<?=@$account['number']?>"  size="20"/>
                </li>
                <li>
                  <label class="desc">Password:</label>
                  <input name="accountpassword" type="password" class="text field medium" id="accountpassword"  value="<?=@$account['password']?>"  size="20"/>
                </li>                
              </ul>
            </div>
          </fieldset>
        </div>
        `
        <div style="width:500px; margin-bottom:10px;">
          <fieldset  style="width:500px;">
            <legend><span style="font-size:14px; font-weight:bold; color:#000; padding:5px;"> Customer Profile</span></legend>
            <div class="content-box-wrapper">
              <ul>
                <?php if($flag == "edit"){?>
                <li>
                  <label class="desc">Reseller:</label>
                  <select name="reseller" class="select field medium">
                    <option value=""></option>
                    <?=$resellers?>
                  </select>
                </li>
                <?php }?>
                <li>
                  <label class="desc">Language:</label>
                  <?=form_languagelist('language',@$account['language'],array("class"=>"select field small"))?>
                </li>
                <li>
                  <label class="desc">Company:</label>
                  <input type="text" class="text field medium" name="company"  size="50" value="<?=@$account['company_name']?>" />
                </li>
                <li>
                  <label class="desc">First Name:</label>
                  <input name="firstname" type="text" class="text field medium" id="firstname"  value="<?=@$account['first_name']?>"  size="50"/>
                </li>
                <li>
                  <label class="desc">Last Name:</label>
                  <input name="lastname" type="text" class="text field medium" id="lastname"  value="<?=@$account['last_name']?>"  size="50"/>
                </li>
                <li>
                  <label class="desc">Telephone1:</label>
                  <input name="telephone1" type="text"  class="text field medium" id="telephone1"  value="<?=@$account['telephone_1']?>"  size="20"/>
                </li>
                <li>
                  <label class="desc">Telephone2:</label>
                  <input name="telephone2" type="text"  class="text field medium" id="telephone2"  value="<?=@$account['telephone_2']?>"  size="20"/>
                </li>
                <li>
                  <label class="desc">Email:</label>
                  <input class="text field medium" type="text" name="email"  size="50"  value="<?=$account['email']?>"/>
                </li>
                <li>
                <li>
                  <label class="desc">Address 1:</label>
                  <input name="address1" type="text" class="text field medium" id="address1"  value="<?=@$account['address_1']?>"  size="50"/>
                </li>
                <label class="desc">Address 2:</label>
                <input name="address2" type="text" class="text field medium" id="address2"  value="<?=@$account['address_2']?>"  size="50"/>
                </li>
                <li>
                  <label class="desc">City:</label>
                  <input class="text field medium" type="text" name="city"  size="20"  value="<?=$account['city']?>"/>
                </li>
                <li>
                  <label class="desc">Province/State:</label>
                  <input class="text field medium" type="text" name="province"  size="20"  value="<?=@$account['province']?>"/>
                </li>
                <li>
                  <label class="desc">Zip/Postal Code:</label>
                  <input type="text" name="postal_code" class="text field medium"  size="20"  value="<?=@$account['postal_code']?>"/>
                </li>
                <li>
                  <label class="desc">Country:</label>
                  <?=form_countries('country',@$account['country'],array("class"=>"select field small"))?>
                </li>
                <li>
                  <label class="desc">Timezone:</label>
                  <?=form_timezone('timezone',$account['tz'],array("class"=>"select field medium"))?>
                </li>
              </ul>
            </div>
          </fieldset>
        </div>
      </div>
      <div style="float:left; width:500px; margin-left:30px;">
        <div style="width:500px; margin-bottom:10px;">
          <fieldset  style="width:500px;">
            <legend><span style="font-size:14px; font-weight:bold; color:#000; padding:5px;"> Account Settings</span></legend>
            <div class="content-box-wrapper">
              <ul>
                <li>
                  <?php
					  $attributes = array("class"=>"select field medium");
					  if($flag == "edit")
					  {
					  	$attributes['readonly'] = "readonly"; 
						$attributes['disabled'] = "disabled"; 
					  }
					  
					  ?>
                  <label class="desc">Account Type:</label>
                  <?=form_select_default('accounttype',$user_types,@$account['type'],$attributes)?>
                </li>
                <? //echo "<pre>";print_r($account);echo "</pre>";?>
                <li>
                <label class="desc">Account Status:</label>
                  <select class="select field medium" name="status">
                    <option value="1" <?php if(@$account['status'] == 1){ echo "selected='selected'";}?>>Active</option>
                    <option value="2" <?php if(@$account['status'] == 2){ echo "selected='selected'";}?>>Inactive</option>
                  </select>
		</li>
                <?php if(!isset($account)){?>
                <li>
                  <label class="desc">Add VOIP Friend:</label>
                  <span>
                  <label for="SIP">
                    <input type="checkbox" name="SIP" value="on" />
                    SIP</label>
                  </span><span>
                  <label for="IAX2">
                    <input type="checkbox" name="IAX2" value="on" />
                    IAX2</label>
                  </span> </li>
                <?php }?>
                <li>
                  <label class="desc">Device Type:</label>
                  <?=form_devicetype('devicetype','friend',array("class"=>"select field medium"))?>
                </li>
                <li>
                  <label class="desc">Context:</label>
                  <input class="field text medium" type="text" name="context" size="20"  value="<?=$config['default_context']?>"/>
                  <!-- default_context --> 
                </li>
                <!--<li>
                      <label class="desc">Fascimile</label><input class="field text medium" type="text" name="fascimile" size="20"  value="<?=$account['fascimile']?>"/>
                      </li>-->
                <li>
                  <label class="desc">IP Address:</label>
                  <input type="text" class="field text medium" name="ipaddr" value="dynamic" size="20" />
                </li>
                <li>
                  <label class="desc">Max Channels:</label>
                  <input type="text" class="text field medium" name="maxchannels"  size="4"  value="<?=$account['maxchannels']?>"/>
                </li>
                <li>
                  <label class="desc">Dialed Number Mods:</label>
                  <input class="text field medium" type="text" name="dialed_modify"  size="20"  value='<?=$account['dialed_modify']?>'/>
                </li>
              </ul>
            </div>
          </fieldset>
        </div>
        <div style="width:500px; margin-bottom:10px;">
          <fieldset  style="width:500px;">
            <legend><span style="font-size:14px; font-weight:bold; color:#000; padding:5px;"> Billing Information </span></legend>
            <div class="content-box-wrapper">
              <ul>
                <li>
                  <label class="desc">Pricelist:</label>
                  <?=form_select_default('pricelist',$pricelist,@$account['pricelist'],array("class"=>"select field medium"))?>
                </li>
                <li>
                  <label class="desc">Billing Schedule:</label>
                  <?=form_select_default('sweep',$sweeplist,@$account['sweep'],array("class"=>"select field medium"))?>
                </li>
                <li>
                  <label class="desc">Currency:</label>
                  <?=form_select_default('currency',$currency_list,@$account['currency'],array("class"=>"select field medium"))?>
                </li>
                <li>
                  <label class="desc">Credit Limit:</label>
                  <input class="text field medium" type="text" name="credit_limit"  size="6"   value="<?=$this->common_model->calculate_currency($account['credit_limit'],'','',true,false)?>"/>
                </li>
                <li>
                  <label class="desc">P.T.E:</label>
                  <select class="select field medium" name="posttoexternal">
                    <option value="1" <?php if(@$account['posttoexternal'] == 1){ echo "selected='selected'";}?>>YES</option>
                    <option value="0" <?php if(@$account['posttoexternal'] == 0){ echo "selected='selected'";}?>>NO</option>
                  </select>
                </li>
              </ul>
            </div>
          </fieldset>
        </div>
      </div>
      <div style="width:100%; float:left;height:40px;margin-top:20px;">
        <input class="ui-state-default float-right ui-corner-all ui-button" type="submit" name="action" value="<?php echo ($flag=='create')?"Generate Account":"Save..."?>" />
      </div>
    </form>
  </div>
</div>
<?php 
	//echo $form;
?>
<? endblock() ?>
<? startblock('sidebar') ?>
<? endblock() ?>
<? end_extend() ?>
