<div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">
  <div class="portlet-header ui-widget-header">ADD Calling Card CallerID<span class="ui-icon ui-icon-circle-arrow-s"></span></div>
  <div class="portlet-content">
  
      <form method="post" action="<?=base_url()?>callingcards/add_callerid" id="frm_payment_process" enctype="multipart/form-data">  
    <?php if(isset($status_msg)){ ?>
              <div class="response-msg alert" style="width: 90%">
                  <br/>
                  <?=$status_msg?>
                  <br/>
              </div>	
  <?php }?>	
                <input type="hidden" name="cardnumber" value="<? echo $cardnumber;?>"/>
  <ul style="width:600px;">
      <li>
          <label class="desc">Card Number:</label>
          <label class="desc" style="text-align: left; padding-left: 5px;"><? echo $cardnumber; ?></label>
      </li>
      <li>
          <label class="desc">Status:</label>
          <input type="checkbox" name="status" <?if(isset($status)){ echo "checked";}?>/>
      </li>
      <li>
          <label class="desc">Caller Id Name:</label>
          <input class="text field medium" type="text" name="callerid_name"  size="8" value="<?if(isset ($callerid_name)) { echo $callerid_name; }?>"/>
      </li>		
      <li>
          <label class="desc">Caller Id Number:</label>
          <input class="text field medium" type="text" name="callerid_number"  size="8" value="<?if(isset ($callerid_number)) { echo $callerid_number; }?>"/>
      </li>		
  </ul>
  <div style="width:100%; float:left; height:50px; margin-top:20px;">
      <input class="ui-state-default float-right ui-corner-all ui-button" type="submit" name="action" value="<?if(isset ($status)){ echo "Save...";} else{ echo "Insert...";}?>"/>
  </div>
  </form>
  </div>
</div>