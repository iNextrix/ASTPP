<script type="text/javascript">
		$(document).ready(function() {
		// validate signup form on keyup and submit
		$("#frm_payment_process").validate({
			rules: {
				refilldollars: "required"
			}
		});
		});
</script>
<div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">
  <div class="portlet-header ui-widget-header">Process Payment<span class="ui-icon ui-icon-circle-arrow-s"></span></div>
  <div class="portlet-content">
  <form method="post" action="<?=base_url()?>accounts/payment_process" id="frm_payment_process" enctype="multipart/form-data">  
  <?php if(isset($status_msg)){ ?>
              <div class="response-msg alert" style="width: 90%">
                  <br/>
                  <?=$status_msg?>
                  <br/>
              </div>	
  <?php }?>	
  <input type="hidden" name="number" value="<?=$account['number']?>"/>
  <ul style="width:600px;">
      <li>
          <label class="desc">Account:</label>
          <label class="desc"><?=$account['number']?></label>
      </li>
      <li>
          <label class="desc">Payment in <?=$account['currency']?>:</label>
          <input class="text field medium" type="text" name="refilldollars"  size="8" />
          <input type="hidden" name="account_currency" value="<?=$account['currency'];?>" />
      </li>		
  </ul>
  <div style="width:100%; float:left; height:50px; margin-top:20px;">
      <input class="ui-state-default float-right ui-corner-all ui-button" type="submit" name="action" value="Refill..." />
  </div>
  </form>
  </div>
</div>