<script type="text/javascript">
  $(document).ready(function() {
  // validate signup form on keyup and submit
	$("#frm-config").validate({
		rules: {
			name: "required",
			value: "required",
		}
	});
  }); 
  
  function brands_update(value)
  {
	  $.ajax({url:'<?=base_url()?>systems/update_brands_select/'+value,
		  type: "GET",
		  success: function(data){
			  /*var s = $('<select />');
			  s.attr("name","brands");
			  s.attr("id","brands");
			  s.attr("class","select field medium");
			  
			  var data = data.split("#,#");
			  
			  for(var val in data) {
				  alert(data[val]);
				  $('<option />', {value: data[val], text: data[val]}).appendTo(s);
			  }*/
			  
			  $("#brands").replaceWith(data);
		  }
	  });
  }
</script>
<div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">                        
    <div class="portlet-header ui-widget-header">Add System Configurations<span class="ui-icon ui-icon-circle-arrow-s"></span></div>
    <div class="portlet-content">
    <form method="POST" action="<?=base_url()?><?=isset($config)?"systems/configuration/edit":"systems/configuration/add"?>" enctype="multipart/form-data" id="frm-config">
    <input name="id" value="<?=@$config['id']?>" type="hidden">
    <ul style="width:600px;">
    <fieldset  style="width:585px;">
            <legend><span style="font-size:14px; font-weight:bold; color:#000;">System Configuration Information</span></legend>
        <li>
        <label class="desc">Reseller:</label>
        <select class="select field medium" onchange="brands_update(this.value)" name="reseller">
		<option value=""></option>
		<?=$resellers?>
        </select>
        </li>
    
        <li>
        <label class="desc" >Brand:</label>
        <select name="brand" id="brands" class="select field medium">
		<option value=""></option>
		<?=$brands?>
        </select>
        </li>
    
        <li>
        <label class="desc">Name:</label>
        <input class="text field medium" name="name" size="20" type="text" value="<?=@$config['name']?>" <?echo (isset($config))?'readonly':'';?>>
        </li>
    
        <li>
        <label class="desc">Value:</label>
        <input class="text field medium" name="value" size="20" type="text" value="<?=@$config['value']?>">
        </li>
    
        <li>
        <label class="desc">Comment:</label>
        <input class="text field medium" name="comment" size="20" type="text" value="<?=@$config['comment']?>" >
        </li>
        </fieldset>
    </ul>
    <div style="width:100%; float:left; height:50px; margin-top:20px;">
        <input class="ui-state-default float-right ui-corner-all ui-button" name="action" type="submit" value="<?=isset($config)?"Save Item":"Add Item"?>">
    </div>
    </form>
    </div>
</div>
