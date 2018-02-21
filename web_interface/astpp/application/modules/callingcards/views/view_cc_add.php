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
  
      <table class="details_table" style="width:700px;">        
        <tr>      
          <th>Account Number</th><td><?=$cc['account']?></td>      
          <th>Card Number</th><td><?=$cc['cardnumber']?></td>      
          <th>Sequence</th><td><?=$cc['id']?></td>      
          <th>Pin</th><td><?=$cc['pin']?></td>      
        </tr>      
        <tr>      
           <th>Brand</th><td><?=$cc['brand_id']?></td>      
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
    
  </div>
</div>