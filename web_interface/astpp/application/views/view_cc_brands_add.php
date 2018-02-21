<script type="text/javascript">
  $(document).ready(function() {
	  // validate signup form on keyup and submit
	  $("#brands_add").validate({
		  rules: {
			  brandname: "required",
			  validdays: {
				  required: true,
				  minlength: 1				 
			  }
		  }
	  });
  });
</script>

<div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">
  <div class="portlet-header ui-widget-header">Add New Calling Card Brand<span class="ui-icon ui-icon-circle-arrow-s"></span></div>
  <div class="portlet-content" style="width:600px;">
    <form action="<?=base_url()?><?php echo isset($brand)?"callingcards/brands/edit/":"callingcards/brands_add";?>" id="brands_add" method="POST" enctype="multipart/form-data">
      <input type="hidden" name="mode" value="CC Brands">
      <ul style="width:600px; list-style:none;">
        <fieldset  style="width:585px;">
          <legend><span style="font-size:14px; font-weight:bold; color:#000;">CC Brand Setting</span></legend>
          <li>
            <label class="desc">CC Brand Name:</label>
            <input type="text" <?php echo isset($brand)?"name='name' readonly='readonly'":"name='brandname'";?> class="text field medium"  size="20"   value="<?=@$brand['name']?>"/>
          </li>
          <li>
            <label class="desc">Pin Required:</label>
            <select name="pin" class="select field medium" >
              <option <?php if(@$brand['pin'] == "1"){?> selected="selected"<?php }?> value="1">YES</option>
              <option <?php if(@$brand['pin'] == "0"){?> selected="selected"<?php }?> value="0">NO</option>
            </select
        >
          </li>
          <li>
            <label class="desc">Pricelist:</label>
            <?=form_select_default('pricelist',$pricelist,@$brand['pricelist'],array("class"=>"select field medium"))?>
          </li>
          <li>
            <label class="desc">Language:</label>
            <?=form_languagelist('language',@$brand['language'],array("class"=>"select field small"))?>
          </li>
          <li>
            <label class="desc">Days Valid For:</label>
            <input type="text" name="validdays" class="text field medium"  size="10"   value="<?=@$brand['validfordays']?>"/>
          </li>
        </fieldset>
        <fieldset  style="width:585px;">
          <legend><span style="font-size:14px; font-weight:bold; color:#000;">CC Brand Setting</span></legend>
          <li>
            <label class="desc">Maintenance Fee:</label>
            <input type="text" name="maint_fee_pennies" class="text field medium"  size="5"   value="<?=$this->common_model->calculate_currency(@$brand['maint_fee_pennies'],'','',true,false)?>"/>
          </li>
          <li>
            <label class="desc">Days Between Maintain Fee:</label>
            <input type="text" name="maint_fee_days" class="text field medium"  size="5"   value="<?=@$brand['maint_fee_days']?>"/>
          </li>
          <li>
            <label class="desc">Disconnect Fee:</label>
            <input type="text" name="disconnect_fee_pennies" class="text field medium"  size="5"   value="<?=$this->common_model->calculate_currency(@$brand['disconnect_fee_pennies'],'','',true,false)?>"/>
          </li>
          <li>
            <label class="desc">Charge after X minutes:</label>
            <input type="text" name="minute_fee_pennies" class="text field medium"  size="5"   value="<?=$this->common_model->calculate_currency(@$brand['minute_fee_pennies'],'','',true,false)?>"/>
          </li>
          <li>
            <label class="desc">Minutes used before charge:</label>
            <input type="text" name="minute_fee_minutes" class="text field medium"  size="5"   value="<?=@$brand['minute_fee_minutes']?>"/>
          </li>
          <li>
            <label class="desc">Minimum length thats not charged extra (minutes):</label>
            <input type="text" name="min_length_minutes" class="text field medium"  size="5"   value="<?=@$brand['min_length_minutes']?>"/>
          </li>
          <li>
            <label class="desc">Extra charge for short calls:</label>
            <input type="text" name="min_length_pennies" class="text field medium"  size="5"   value="<?=$this->common_model->calculate_currency(@$brand['min_length_pennies'],'','',true,false)?>"/>
          </li>
        </fieldset>
      </ul>
      <div style="width:100%; float:left; height:50px; margin-top:20px;">
        <input type="submit" class="ui-state-default float-right ui-corner-all ui-button" name="action" value="<?php echo isset($brand)?"Save...":"Insert...";?>" />
      </div>
    </form>
  </div>
</div>
