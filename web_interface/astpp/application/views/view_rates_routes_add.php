<script type="text/javascript">
  $(document).ready(function() {
  // validate signup form on keyup and submit
	$("#frm_routes").validate({
		rules: {
			pattern: "required"
		}
	});
  });
</script>
<div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">                        
        <div class="portlet-header ui-widget-header">Origination Rate<span class="ui-icon ui-icon-circle-arrow-s"></span></div>
        <div class="portlet-content">
<br />
        <form method="post" id="frm_routes" action="<?=base_url()?><?=isset($route)?"rates/routes/edit":"rates/routes/add"?>" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?=@$route['id']?>" />
        <ul style="width:600px">
        <fieldset  style="width:585px;">
            <legend><span style="font-size:14px; font-weight:bold; color:#000;">Origination Rate Information</span></legend>
        <li>
       	<label class="desc">Code:</label>
        <input class="text field medium" value="<?=@$route['pattern']?>" type="text" name="pattern"  size="20" /> <b>(Example : ^44.*)</b>
        </li>
        <li>
        <label class="desc">Pricelist(s):</label>
        <select name="pricelist" class="select field medium" >
        <?=$pricelists?>
        </select>
        </li>
        <li>
        <label class="desc">Destination:</label><input class="text field medium" type="text" value="<?=@$route['comment']?>" name="comment"  size="20" />
        </li>
        <li>
        <label class="desc">Connect Charge:</label><input class="text field medium" type="text" name="connectcharge" value="<?=$this->common_model->calculate_currency(@$route['connectcost'],'','',true,false)?>"  size="8" />
        </li>
        <li>
        <label class="desc">Included Seconds:</label><input class="text field medium" type="text" value="<?=@$route['includedseconds']?>" name="incseconds"  size="8" />
        </li>
        <li>
        <label class="desc">Cost Per Add. Minute:</label><input class="text field medium" type="text" value="<?=$this->common_model->calculate_currency(@$route['cost'],'','',true,false)?>" name="cost"  size="8" />
        </li>
        <li>
        <label class="desc">Increments:</label><input class="text field medium" type="text" value="<?=@$route['inc']?>" name="inc"  size="8" />
        </li>
        </fieldset>
        </ul>
        <div style="width:100%; float:left; height:50px; margin-top:20px;">
        <input type="submit" class="ui-state-default float-right ui-corner-all ui-button" name="action" value="<?=isset($route)?"Save...":"Insert..."?>" />
        </div>
        </form>
        </div>
</div>
