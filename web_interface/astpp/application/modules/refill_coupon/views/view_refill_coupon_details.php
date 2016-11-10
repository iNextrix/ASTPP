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
  <div class="portlet-header ui-widget-header"><?= $page_title; ?><span class="ui-icon ui-icon-circle-arrow-s"></span></div>
  <div class="portlet-content">
      <table class="details_table" style="width:700px;">        
        <tr>      
          <th>Refill Coupon Number</th><td><?=$refill_coupon_details['number']?></td>      
          <th>Amount</th><td><?=$refill_coupon_details['amount']?></td>      
          <th>Tag</th><td><?=$refill_coupon_details['tag']?></td>      
          <th>Currency</th><td><?=$refill_coupon_details['currency']?></td>      
        </tr>      
        <tr>      
           <th>Start Date</th><td><?=$refill_coupon_details['start_date']?></td>      
           <th>Expiry Date</th><td><?=$refill_coupon_details['expiry_date']?></td>      
           <th>Callingcard</th><td><?=$refill_coupon_details['callingcard']?></td>      
        </tr>      
            <th>Status</th><td><?php if ($refill_coupon_details['status'] == 0) { echo "Inactive"; } elseif ($refill_coupon_details['status'] == 1) { echo "Active"; } elseif ($refill_coupon_details['status'] == 2) { echo "Inuse"; } else { echo "Expired"; }?></td>      
        </tr>     
        </table>
        
        <?php if (isset($cdrs) && is_array($cdrs)) {?>
        <table class="details_table" style="width:700px;">        
        <tr>      
          <th>Destination</th>
          <th>Disposition</th>
          <th>CallerID</th>
          <th>Starting Time</th>
          <th>Length in Seconds</th>
          <th>Cost(CAD)</th>
        </tr>
        <?php foreach($cdrs as $cdr){
		$debit= '';
		$debit =($cdr['debit']/10000);
		$debit=$debit+0;
		?>
        <tr>
          <td><?=$cdr['destination']?></td>
          <td><?=$cdr['disposition']?></td>
          <td><?=$cdr['clid']?></td>
          <td><?=$cdr['callstart']?></td>
          <td><?=$cdr['seconds']?></td>
          <td><?=$cdr['debit']?></td>
        </tr>
        <?php }?>
        </table>
        <?php }?>
    
  </div>
</div> 
