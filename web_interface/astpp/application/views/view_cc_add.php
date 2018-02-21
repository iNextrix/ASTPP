<script type="text/javascript">
  $(document).ready(function() {
  // validate signup form on keyup and submit
	$("#add_account").validate({
		rules: {
			/*account: "required",*/
			brand: "required",
			value: "required",
			count: "required"
		}
	});
  });
	</script>
  <style type="text/css" >
  table.details_table th{
	  text-align:left;
  }
  table.details_table td{
	  text-align:center;
  }
  </style> 
<div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">
  <div class="portlet-header ui-widget-header"><?php if(!isset($cc)){?> Add New Calling Card <? } else{?>  View Calling Card <? } ?><span class="ui-icon ui-icon-circle-arrow-s"></span></div>
  <div class="portlet-content">
  <?php if(!isset($cc)){?>
    <form action="<?=base_url()?>callingcards/add" id="add_account" method="POST" enctype="multipart/form-data">
      <ul style="width:600px; list-style:none;">
      <fieldset  style="width:585px;">
            <legend><span style="font-size:14px; font-weight:bold; color:#000;">Card Information</span></legend>
        <li>
          <label class="desc">Account Number:</label>
          <input type="text" name="account" class="text field medium"  size="20"   value="<?=@$cc['account']?>"/>
        </li>
        <li>
          <label class="desc">Brand:</label>
          <?=form_select_default('brand',$brands,@$cc['brand'],array("class"=>"select field medium"))?>
        </li>
        <li>
          <label class="desc">Balance:</label>
          <input type="text" name="value" class="text field medium"  size="10"   value="<?=$this->common_model->calculate_currency(@$cc['value'],'','',true,false)?>"/>
        </li>
        <li>
          <label class="desc">Quantity:</label>
          <input type="text" name="count" class="text field medium"  size="5"   value="<?=@$cc['count']?>"/>
        </li>
        <li>
          <label class="desc">Status:</label>
          <select name="status" class="select field medium" >
            <option value="1" <?php if(@$cc['status'] == "1"){ echo "selected='selected'";}?> >ACTIVE</option>
            <option value="0" <?php if(@$cc['status'] == "0"){ echo "selected='selected'";}?> >INACTIVE</option>
            <option value="2" <?php if(@$cc['status'] == "2"){ echo "selected='selected'";}?> >DELETED</option>
          </select>
        </li>
       </fieldset>
        
      </ul>
      <div style="width:100%; float:left; height:50px; margin-top:20px;">
      <input type="submit" class="ui-state-default float-right ui-corner-all ui-button" name="action" value="<?php if(@$action=='update..') echo 'Update';else echo 'Generate Card(s)';?>" />
      </div>
    </form>
    <?php } else{?>
      <table class="details_table" style="width:700px;">        
        <tr>      
          <th>Account Number</th><td><?=$cc['account']?></td>      
          <th>Card Number</th><td><?=$cc['cardnumber']?></td>      
          <th>Sequence</th><td><?=$cc['id']?></td>      
          <th>Pin</th><td><?=$cc['pin']?></td>      
        </tr>      
        <tr>      
           <th>Brand</th><td><?=$cc['brand']?></td>      
           <th>Value</th><td><?=$this->common_model->calculate_currency($cc['value'])?></td>      
           <th>Used</th><td><?=$this->common_model->calculate_currency($cc['used'])?></td>      
           <th>Days Valid For</th><td><?=$cc['validfordays']?></td>      
        </tr>      
        <tr>      
            <th>Creation</th><td><?=$cc['created']?></td>      
            <th>First Use</th><td><?=$cc['firstused']?></td>      
            <th>Expiration</th><td><?=$cc['expiry']?> </td>      
            <th>In Use?</th><td><?=($cc['inuse'] == 0)?"No":"Yes"?></td>      
        </tr>      
        <tr>      
            <th>Status</th><td><?php if($cc['status'] == 0){ echo "Inactive";} elseif($cc['status'] == 1){ echo "Active"; } else{ echo "Deleted";}?></td>      
        </tr>     
        </table>
        
        <?php if(is_array($cdrs)){?>
        <table class="details_table" style="width:700px;">        
        <tr>      
          <th>Destination</th>
          <th>Disposition</th>
          <th>CallerID</th>
          <th>Starting Time</th>
          <th>Length in Seconds</th>
          <th>Cost(CAD)</th>
        </tr>
        <?php foreach($cdrs as $cdr){?>
        <tr>
          <td><?=$cdr['destination']?></td>
          <td><?=$cdr['disposition']?></td>
          <td><?=$cdr['clid']?></td>
          <td><?=$cdr['callstart']?></td>
          <td><?=$cdr['seconds']?></td>
          <td><?=($cdr['debit']/10000)?></td>
        </tr>
        <?php }?>
        </table>
        <?php }?>
    <?php }?>
  </div>
</div>