<? extend('master.php') ?>

	<? startblock('extra_head') ?>
<script type="text/javascript">
  $(document).ready(function() {
  // validate signup form on keyup and submit
	$("#frm_calc").validate({
		rules: {
			phonenumber: "required",
			length: "required"
		}
	});
  });
</script>
	<? endblock() ?>

    <? startblock('page-title') ?>
        <?=$page_title?><br/>
    <? endblock() ?>
    
	<? startblock('content') ?>
<div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">                        
            <div class="portlet-header ui-widget-header">Calculate Charges<span class="ui-icon ui-icon-circle-arrow-s"></span></div>
            <div class="portlet-content">
            <form method="post" id="frm_calc" action="<?=base_url()?>rates/calccharge/" enctype="multipart/form-data">
            <ul >  
            <fieldset  style="width:585px;">
            <legend><span style="font-size:14px; font-weight:bold; color:#000;">Calculate Charges Information</span></legend>         
            <li>
            <label class="desc">Phone Number:</label><input class="text field medium"  value="" type="text" name="phonenumber"  size="20" />
            </li>
            <li>
            <label class="desc">Length (Minutes):</label><input class="text field medium" type="text" value="" name="length"  size="20" />
            </li>
            <li>
            <label class="desc">Pricelist:</label>
            <select class="select field medium" name="pricelist" >
            <?=$pricelists?>
            </select>
            </li>
            </fieldset>
            			</ul>
            
            <div style="width:100%;float:left;height:50px;margin-top:20px;">
            <input class="ui-state-default float-right ui-corner-all ui-button" type="submit" name="action" value="Price Call..." />
            </div>
            </form>            
            </div>
</div>
    <? endblock() ?>
	
    <? startblock('sidebar') ?>
    Filter by
    <? endblock() ?>
    
<? end_extend() ?>  
