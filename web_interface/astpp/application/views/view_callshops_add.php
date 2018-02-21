<script type="text/javascript">
	$(document).ready(function() {
		// validate signup form on keyup and submit
		$("#frm_callshop").validate({
			rules: {
				callshop_name: "required",
				accountpassword: {
					required: true,
					minlength: 6
				},
				credit_limit: "required",
				osc_site: "required",
				osc_dbname: "required",
				osc_dbhost: "required",
				osc_dbpass: "required",
				osc_dbuser: "required"
			}
		});
	});
</script>
<div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">                        
        <div class="portlet-header ui-widget-header">Add New Calls Shop<span class="ui-icon ui-icon-circle-arrow-s"></span></div>
        <div class="portlet-content">
        <form action="<?=base_url()?>callshops/add" id="frm_callshop" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="mode" value="Create CallShop"/>
        Please enter the following information to create a new callshop<br><br>
      
            <ul style="width:675px; list-style:none;">
            
       <fieldset  style="width:660px;">
            <legend><span style="font-size:14px; font-weight:bold; color:#000;">Callshop Information</span></legend>
                <li>   
                  <label class="desc">Call Shop Name:</label>
                  <input size="20" class="text field medium" name="callshop_name"></label>
                </li>
                <li>
                  <label class="desc">Login Password:</label>
                  <input size="20" class="text field medium" name="accountpassword">
                </li>
                <li>
                  <label class="desc">Credit Limit:</label>
                  <input size="20" class="text field medium" name="credit_limit">
                </li>
                <li>
                  <label class="desc">Sweep:</label>
                  <?=form_select_default('sweep',$sweepList,'',array("class"=>"select field medium"))?>
                </li>
                <li>
                  <label class="desc">Language:</label>
                  <?=form_languagelist('language', 'en',array("class"=>"select field medium"))?>
                </li>
                <li>
                  <label class="desc">Currency:</label>
                  <?=form_select_default('currency',$currency_list,'',array("class"=>"select field medium"))?>
                </li>
                <li>
                  <label class="desc">Link to OSCommerce Site:</label>
                  <input size="60" class="text field medium" value="http://www.companysite.com/store/" name="osc_site">
                </li>
                <li>
                  <label class="desc">OSCommerce Database Name:</label>
                  <input name="osc_dbname" size="30" class="text field medium">
                </li>
                <li>
                  <label class="desc">OSCommerce Database Host:</label>
                  <input name="osc_dbhost" size="30" class="text field medium">
                </li>
                <li>
                  <label class="desc">OSCommerce Database Password:</label>
                  <input name="osc_dbpass" type="password" class="text field medium">
                </li>
                <li>
                  <label class="desc">OSCommerce Database Username:</label>
                  <input name="osc_dbuser" size="30" class="text field medium">
                </li>
                <li>
                  <label class="desc"></label>      
                </li>
                <li>
                  <label class="desc"></label>
                </li>
                
               	</fieldset>
                      
            </ul>
        <br />
        <input type="submit" class="ui-state-default float-right ui-corner-all ui-button" name="action" value="Add..." />
        <br>
        
        <TMPL_VAR NAME= "status">
        </form>
        </div>
</div>