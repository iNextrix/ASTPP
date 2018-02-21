<script type="text/javascript">
  $(document).ready(function() {
  // validate signup form on keyup and submit
	$("#frm_trunk").validate({
		rules: {
			name: "required",
			path: "required",
		}
	});
  });
</script>
<?php
$sellers_list =  array(); 
if(isset($trunk)){
	$sellers_list = explode("',",$trunk['resellers']);
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
        <div class="portlet-header ui-widget-header">Add New Trunck<span class="ui-icon ui-icon-circle-arrow-s"></span></div>
        <div class="portlet-content">
        <form method="post" action="<?php echo base_url();?><?=isset($trunk)?"lcr/trunks/edit/":"lcr/trunks/add/"?>" id="frm_trunk" enctype="multipart/form-data">       	
        <ul style="width:600px">
        <fieldset  style="width:585px;">
            <legend><span style="font-size:14px; font-weight:bold; color:#000;">Truk Information</span></legend>
        <li>
        <label class="desc">Trunk Name:</label>
        <input class="text field medium" type="text" name="name" value="<?=@$trunk['name']?>" <?=isset($trunk)?"readonly='readonly'":""?> size="20" />
        </li>
        <li>
        <label class="desc">Protocol:</label>
        <select name="tech"  class="select field medium">
        <option value="SIP" <?php if(@$trunk['tech'] == "SIP"){ echo "selected='selected'";}?> >SIP</option>
        <option value="IAX2" <?php if(@$trunk['tech'] == "IAX2"){ echo "selected='selected'";}?> >IAX2</option>
        <option value="Zap" <?php if(@$trunk['tech'] == "Zap"){ echo "selected='selected'";}?> >Zap</option>
        <option value="Local" <?php if(@$trunk['tech'] == "Local"){ echo "selected='selected'";}?> >Local</option>
        <option value="OH323" <?php if(@$trunk['tech'] == "OH323"){ echo "selected='selected'";}?> >OH323</option>
        <option value="OOH323C" <?php if(@$trunk['tech'] == "OOH323C"){ echo "selected='selected'";}?> >OOH323C</option>
        </select>
        </li>
        <li>
        <label class="desc">Peer Name:</label><input class="text field medium" type="text" name="path" value="<?=@$trunk['path']?>"  size="20" />
        </li>
        <li>
        <label class="desc">Provider:</label>
        <select name="provider" class="select field medium" >
        <?=$providers?>
        </select>
        </li>
        <li>
        <label class="desc">Max Channels:</label><input class="text field medium" type="text" value="<?=@$trunk['maxchannels']?>" name="maxchannels"  size="4" />
        </li>
        <li>
        <label class="desc">Dialed Number Mods (CSV,Regex):</label><input class="text field medium" value='<?=@$trunk['dialed_modify']?>' type="text" name="dialed_modify"  size="20" />
        </li>
        <li>
        <label class="desc">Precedence:</label><input class="text field medium" type="text" value="<?=@$trunk['precedence']?>" name="precedence"  size="2" />
        </li>
        <label class="desc">Resellers:</label><label>
        <div style="float:left; width:275px; margin-left:6px;">
        <li>
        
        <?php if(isset($sellersList) && is_array($sellersList) && count($sellersList)> 0){ foreach($sellersList as $seller){?>
        <input type="checkbox" name="reseller-<?=$seller?>" <?php if(in_array($seller,$sellers_list)){ echo "checked='checked'";}?> value="1" />reseller-<?=$seller?></label><br />        
        <?php }}?>
        </li>  
        </div>
        </fieldset>              
        </ul>
        <div style="width:100%;float:left;height:50px;margin-top:20px;">
        <input class="ui-state-default float-right ui-corner-all ui-button" type="submit" name="action" value="<?=isset($trunk)?"Save...":"Insert..."?>" />
        </div>
        </form>
        </div>
</div>
