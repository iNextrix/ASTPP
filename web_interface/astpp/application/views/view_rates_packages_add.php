<script type="text/javascript">
  $(document).ready(function() {
  // validate signup form on keyup and submit
	$("#frm_routes").validate({
		rules: {
			name: "required",
			pattern: "required"
		}
	});
  });
</script>
<div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">                        
            <div class="portlet-header ui-widget-header">Add/Edit Package<span class="ui-icon ui-icon-circle-arrow-s"></span></div>
            <div class="portlet-content">
            <form method="post" action="<?=base_url()?><?=isset($package)?"rates/packages/edit":"rates/packages/add"?>" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo @$package['id'];?>"  />
            <ul style="width:600px">
            <fieldset  style="width:585px;">
            <legend><span style="font-size:14px; font-weight:bold; color:#000;">Packages Information</span></legend>
            <li>
            <label class="desc">Packages Name:</label><input class="text field medium"  value="<?php echo @$package['name'];?>" type="text" name="name"  size="20" />
            </li>
            <li>
            <label class="desc">Pricelist:</label>
            <select class="select field medium" name="pricelist" >
            <?=$pricelists?>
            </select>
            </li>
            <li>
            <label class="desc">Pattern:</label><input class="text field medium" type="text" value="<?php echo @$package['pattern'];?>" name="pattern"  size="20" />
            </li>
            <li>
            <label class="desc">Included in Seconds:</label>
            <input class="text field medium" type="text" name="includedseconds" value="<?php echo @$package['includedseconds'];?>"  size="8" />
            </li>
            </fieldset>
			</ul>
            <div style="width:100%;float:left;height:50px;margin-top:20px;">
            <input class="ui-state-default float-right ui-corner-all ui-button" type="submit" name="action" value="<?=isset($package)?"Save...":"Insert..."?>" />
            </div>
            </form>            
            </div>
</div>