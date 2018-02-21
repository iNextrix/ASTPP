<script type="text/javascript">
  $(document).ready(function() {
  // validate signup form on keyup and submit
	$("#frm_fssi").validate({
		rules: {
			fs_username: "required",
			fs_password: "required",
			vm_password: "required"
		}
	});
  });
</script>
<div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">                        
            <div class="portlet-header ui-widget-header">FSS IP Devices<span class="ui-icon ui-icon-circle-arrow-s"></span></div>
            <div class="portlet-content">
            <form method="post" action="<?=base_url()?><?=isset($switch)?"switchconfig/fssipdevices/edit":"switchconfig/fssipdevices/add"?>" id="frm_fssi" enctype="multipart/form-data">
            <?php if(isset($switch)){?>
            <input type="hidden" name="directory_id" value="<?php echo @$switch['directory_id'];?>"  />
            <?php }?>
            <ul style="width:600px">
            <fieldset  style="width:585px;">
            <legend><span style="font-size:14px; font-weight:bold; color:#000;">FREESWITCH(TM) SIP DEVICES Information</span></legend>
            <li>
            <label class="desc">User Name:</label><input class="text field medium" type="text" value="<?php echo @$switch['fs_username'];?>" name="fs_username"  size="20" />
            </li>
            <li>
            <label class="desc">Password:</label><input class="text field medium" type="password" value="<?php echo @$switch['fs_password'];?>" name="fs_password"  size="20" />
            </li>
            <li>
            <label class="desc">Account Code:</label>
            <input class="text field medium" type="text" name="accountcode" value="<?php echo @$switch['accountcode'];?>"  size="8" />
            </li>            
            <li>
            <label class="desc">VM Password:</label>
            <input class="text field medium" type="text" name="vm_password"  value="<?php echo @$switch['vm_password'];?>" size="8" />
            </li>
            <li>
            <label class="desc">Context:</label>
            <input class="text field medium" type="text" name="context" value="<?php echo @$switch['context'];?>"  size="8" />
            </li>
            </fieldset>
			</ul>
            <div style="width:100%;float:left;height:50px;margin-top:20px;">
            <input class="ui-state-default float-right ui-corner-all ui-button" type="submit" name="action" value="Save..." />
            </div>
            </form>            
            </div>
</div>