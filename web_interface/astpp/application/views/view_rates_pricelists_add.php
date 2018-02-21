<script type="text/javascript">
  $(document).ready(function() {
  // validate signup form on keyup and submit
	$("#frm_pricelist").validate({
		rules: {
			name: "required",
			inc: "required",
			markup: "required"
		}
	});
  });
</script>
<?php 
$sellers_list = array();
if(isset($outbound)){
	$sellers_list = explode("',",$outbound['resellers']);
	foreach($sellers_list as $key=>$value)
	{
		if($value == "") 
		{ 
			unset($sellers_list[$key]); 
			continue;
		}		
		$sellers_list[$key] = substr($value,1);
	}	
}
?>
<div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">                        
        <div class="portlet-header ui-widget-header">Add Price List<span class="ui-icon ui-icon-circle-arrow-s"></span></div>
        <div class="portlet-content">
        <form method="post" id="frm_pricelist" action="<?=base_url()?><?=isset($pricelist)?"rates/pricelists/edit":"rates/pricelists/add"?>" enctype="multipart/form-data">
        <input type="hidden" name="oldname" value="<?=@$pricelist['name']?>" />
        <ul style="width:600px;">
        <fieldset  style="width:585px;">
            <legend><span style="font-size:14px; font-weight:bold; color:#000;">Add Price List Information</span></legend>
        
        <li>
        <label class="desc">Pricelist Name:</label>
        <input class="text field medium" type="text" value="<?=@$pricelist['name']?>" name="name"  size="20" />
        </li>
        <li>
        <label class="desc">Default Increment:</label>
        <input class="text field medium" type="text" name="inc" value="<?=@$pricelist['inc']?>"  size="4" />
        </li>
        <li>
        <label class="desc">Markup in 1/100 of 1%:</label>
        <input class="text field medium" type="text" name="markup" value="<?=@$pricelist['markup']?>"  size="6" />
        </li>
        </fieldset>
        </ul>
        <div style="width:100%; float:left; height:50px; margin-top:20px;">
        <input type="submit" class="ui-state-default float-right ui-corner-all ui-button" name="action" value="<?=isset($pricelist)?"Save...":"Insert..."?>" />
        </div>
        </form>
        </div>
</div>