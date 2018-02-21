<script type="text/javascript">
  $(document).ready(function() {
  // validate signup form on keyup and submit
	$("#frm_trunk").validate({
		rules: {
			pattern: "required",
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
        <div class="portlet-header ui-widget-header">Add New Termination Rate<span class="ui-icon ui-icon-circle-arrow-s"></span></div>
        <div class="portlet-content">
        <form method="post" action="<?php echo base_url();?><?=isset($outbound)?"lcr/outbound/edit/":"lcr/outbound/add/"?>" enctype="multipart/form-data">
        <br /><input type="hidden" name="id" value="<?=@$outbound['id']?>" />
        <ul style="width:600px">
        <fieldset  style="width:585px;">
            <legend><span style="font-size:14px; font-weight:bold; color:#000;">Termination Rate Information</span></legend>
        <li>
        <label class="desc">Code</label>
        <input class="text field medium" type="text" name="pattern" value="<?=@$outbound['pattern']?>"  size="20" /> <b>(Example : ^44.*)</b>
        </li>
        <li>
        <label class="desc">Prepend</label>
        <input class="text field medium" type="text" name="prepend" value="<?=@$outbound['prepend']?>"  size="20" />
        </li>
        <li>
        <label class="desc">Destination</label>
        <input class="text field medium" type="text" name="comment" value="<?=@$outbound['comment']?>"  size="20" />
        </li>
        <li>
        <label class="desc">Trunk</label>
        <select class="select field medium" name="trunk" >
        <?=$trunks?>
        </select>
        </li>
        <li>
        <label class="desc">Increment</label>
        <input class="text field medium" type="text" name="inc" value="<?=@$outbound['inc']?>"  size="4" />
        </li>
        <li>
        <label class="desc">Connect Charge</label>
        <input class="text field medium" type="text" name="connectcost" value="<?=$this->common_model->calculate_currency(@$outbound['connectcost'],'','',true,false)?>"  size="20" />
        </li>
        <li>
        <label class="desc">Included Seconds</label>
        <input class="text field medium" type="text" name="includedseconds" value="<?=@$outbound['includedseconds']?>"  size="20" />
        </li>
        <li>
        <label class="desc">Cost per Additional Minute</label>
        <input class="text field medium" type="text" name="cost" value="<?=$this->common_model->calculate_currency(@$outbound['cost'],'','',true,false)?>"  size="20" />
        </li>
        <li>
        <label class="desc">Precedence</label>
        <input class="text field medium" type="text" name="precedence" value="<?=@$outbound['precedence']?>"  size="2" />
        </li>
        <li>
        <label class="desc">Resellers</label><label>
        <?php if(isset($sellersList) && is_array($sellersList) && count($sellersList)> 0){ foreach($sellersList as $seller){?>
        <input type="checkbox" name="reseller-<?=$seller?>" <?php if(in_array($seller,$sellers_list)){ echo "checked='checked'";}?> value="1" />reseller-<?=$seller?></label><br />        
        <?php }}?>
        </li>
        </fieldset>
        
        </ul><br />
        <input class="ui-state-default float-right ui-corner-all ui-button" type="submit" name="action" value="<?=isset($outbound)?"Save...":"Insert..."?>" />
        </form>
        </div>
</div>
