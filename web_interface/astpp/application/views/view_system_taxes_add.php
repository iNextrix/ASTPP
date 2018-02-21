<script type="text/javascript">
  $(document).ready(function() {
  // validate signup form on keyup and submit
	$("#frm_routes").validate({
		rules: {
			taxes_priority: "required",
			taxes_amount: "required",
			taxes_rate: "required"
		}
	});
  }); 
</script>
<div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">                        
            <div class="portlet-header ui-widget-header">Taxes<span class="ui-icon ui-icon-circle-arrow-s"></span></div>
            <div class="portlet-content">
            <form method="post" action="<?=base_url()?><?=isset($tax)?"systems/taxes/edit":"systems/taxes/add"?>" enctype="multipart/form-data">
            <input type="hidden" name="taxes_id" value="<?php echo @$tax['taxes_id'];?>"  />
            <ul style="width:600px">
             <fieldset  style="width:585px;">
            <legend><span style="font-size:14px; font-weight:bold; color:#000;">Taxes Information</span></legend>
            <li>
            <label class="desc">Priority:</label><input class="text field medium" value="<?php echo @$tax['taxes_priority'];?>" type="text" name="taxes_priority"  size="20" />
            </li>
            <li>
            <label class="desc">Amount:</label><input class="text field medium" type="text" value="<?php echo $this->common_model->calculate_currency(@$tax['taxes_amount'],'','',true,false);?>" name="taxes_amount"  size="20" />
            </li>
            <li>
            <label class="desc">Rate(%):</label>
            <input class="text field medium" type="text" value="<?php echo $this->common_model->format_currency(@$tax['taxes_rate'],'','',true,false);?>" name="taxes_rate"  size="8" />
            </li>
            <li>
            <label class="desc">Description:</label>
            <input class="text field medium" type="text" value="<?php echo @$tax['taxes_description'];?>" name="taxes_description"  size="8" />
            </li>
            </fieldset>
			</ul>
            <div style="width:100%;float:left;height:50px;margin-top:20px;">
            <input class="ui-state-default float-right ui-corner-all ui-button" type="submit" name="action" value="<?=isset($tax)?"Save Item":"Add Item"?>" />
            </div>
            </form>            
            </div>
</div>