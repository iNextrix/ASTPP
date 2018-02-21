<div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">
  <div class="portlet-header ui-widget-header">Account Details<span class="ui-icon ui-icon-circle-arrow-s"></span></div>
  <div class="portlet-content">
  <div class="hastable">
  <table class="details_table">  
  <tr>
    	<th>Account Number</th><td><?=$account['number']?></td>
        <th>Balance</th><td><?=$balance;?></td>
        <th>Account Type</th><td><?=@$user_type[$account['type']]?></td>
        <th>Name</th><td><?=$account['first_name']?></td>
  </tr>
  <tr>
  	 <th>Company</th><td><?=$account['company_name']?></td>
  	 <th>Address</th><td><?=$account['address_1'].'<br/>'.$account['address_2']?></td>
  	 <th>Language</th><td><?=$account['language']?></td>
     <th>City</th><td><?=$account['city']?></td>
  </tr>
  <tr>
      <th>Province/State</th><td><?=$account['province']?></td>
      <th>Zip/Postal Code</th><td><?=$account['postal_code']?></td>
      <th>Country</th><td><?=$account['country']?> </td>
      <th>Pricelist</th><td><?=$account['pricelist']?></td>
  </tr>
  <tr>
      <th>Billing Schedule</th><td><?=$account['sweep']?></td>
      <th>Credit Limit in</th><td><?=$credit_limit;?></td>
      <th>Timezone</th><td><?=$account['tz']?></td>      
      <th>Max Channels</th><td><?=$account['maxchannels']?></td>
  </tr>
  <tr>
      <th>Pin</th><td><?=$account['pin']?></td>
      <th>Dialed Number Mods</th><td><?=$account['dialed_modify']?></td>
      <th>IP Address</th><td>dynamic</td>
      <th>Telephone</th><td><?=$account['telephone_1']?></td>
  </tr>
  <tr>      
      <th>Email</th><td><?=$account['email']?></td>
<!--       <th>Fascimile</th><td><?=$account['fascimile']?></td> -->
      <th>&nbsp;</th><td>&nbsp;</td>
      <th>&nbsp;</th><td>&nbsp;</td>
  </tr>
  </table>
  </div>
  </div>
</div>