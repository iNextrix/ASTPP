<script type="text/javascript">
  $(document).ready(function() {
  // validate signup form on keyup and submit
	$("#frm_fssi").validate({
		rules: {
			username: "required",
			password: "required",
		
		}
	});
  });
</script>
<div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">                        
            <div class="portlet-header ui-widget-header">Opensip Devices<span class="ui-icon ui-icon-circle-arrow-s"></span></div>
            <div class="portlet-content">
            <form method="post" action="<?=base_url()?><?=isset($opensips)?"opensipsconfig/opensipdevice/edit/$edit_id":"opensipsconfig/opensipdevice/add"?>" id="frm_fssi" enctype="multipart/form-data">
            <?// ECHO "<pre>";print_r($opensips);
            //echo $opensips['0']['password'];?>
            <ul style="width:600px">
            <fieldset  style="width:585px;">
            <legend><span style="font-size:14px; font-weight:bold; color:#000;">Opensips Device</span></legend>
            <li>
            <label class="desc">User Name:</label><input class="text field medium" type="text" value="<?php echo @$opensips['0']['username'];?>" name="username"  size="20" />
            </li>
            <li>
            <label class="desc">Password:</label><input class="text field medium" type="password" value="<?php echo @$opensips['0']['password'];?>" name="password"  size="20" />
            </li>
            <li>
            <label class="desc">Account Code:</label>
            <input class="text field medium" type="text" name="accountcode" value="<?php echo @$opensips['0']['accountcode'];?>"  size="8" />
            </li> 
            <li>
            <label class="desc">Domain:</label>
            <input class="text field medium" type="text" name="domain" value="<?php echo (!isset($opensips['0']['domain']))?Common_model::$global_config['system_config']['opensips_domain']:$opensips['0']['domain'];;?>"  size="8" />
            </li>            
            
        
            </fieldset>
			</ul>
            <div style="width:100%;float:left;height:50px;margin-top:20px;">
            <input class="ui-state-default float-right ui-corner-all ui-button" type="submit" name="action" value="Save..." />
            </div>
            </form>            
            </div>
</div>