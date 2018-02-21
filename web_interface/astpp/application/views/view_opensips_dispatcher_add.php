<script type="text/javascript">
  $(document).ready(function() {
  // validate signup form on keyup and submit
	$("#frm_fssi").validate({
		rules: {
			destination: "required",
			
		
		}
	});
  });
</script>
<div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">                        
            <div class="portlet-header ui-widget-header">Dispatcher<span class="ui-icon ui-icon-circle-arrow-s"></span></div>
            <div class="portlet-content">
            <form method="post" action="<?=base_url()?><?=isset($dispatcher)?"opensipsconfig/dispatcher/edit/$edit_id":"opensipsconfig/dispatcher/add"?>" id="frm_fssi" enctype="multipart/form-data">
            <? //ECHO "<pre>";print_r($dispatcher);
            //echo $opensips['0']['password'];?>
            <ul style="width:600px">
            <fieldset  style="width:585px;">
            <legend><span style="font-size:14px; font-weight:bold; color:#000;">Dispatcher Information</span></legend>
            <li>
            <label class="desc">Setid:</label><input class="text field medium" type="text" value="<?echo @$dispatcher['0']['setid'];?>" name="setid"  size="20" />
            </li>
            <li>
            <label class="desc">Destination:</label><input class="text field medium" type="text" value="<?echo @$dispatcher['0']['destination'];?>" name="destination"  size="20" /><br/><span style="font-size:12px;font-family:arial;padding-left:116px;font-weight:bold;">Format : sip:192.168.1.4</span>
            </li>
            <li>
            <label class="desc">Flags:</label><input class="text field medium" type="text" value="<?echo @$dispatcher['0']['flags'];?>" name="flags"  size="20" />
            </li>            
            <li>
            <label class="desc">Weight:</label><input class="text field medium" type="text" value="<?php echo @$dispatcher['0']['weight'];?>" name="weight"  size="20" />
            </li>
            <li>
            <label class="desc">Attrs:</label><input class="text field medium" type="text" value="<?echo @$dispatcher['0']['attrs'];?>" name="attrs"  size="20" />
            </li>
            <li>
            <label class="desc">Description:</label>
            <input class="text field medium" type="text" name="description" value="<?php echo @$dispatcher['0']['description'];?>"  size="8" />
            </li> 
            
        
            </fieldset>
			</ul>
            <div style="width:100%;float:left;height:50px;margin-top:20px;">
            <input class="ui-state-default float-right ui-corner-all ui-button" type="submit" name="action" value="Save..." />
            </div>
            </form>            
            </div>
</div>