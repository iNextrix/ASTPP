<input name="mode" value="Calling Cards" type="hidden">
<form method="post" action="/cgi-bin/astpp/astpp-users.cgi?mode=Calling%20Cards" enctype="multipart/form-data">
View Card: <TMPL_VAR NAME="card_number">
<table class="default">
	<tr class="header">
		<td>Destination</td>
	        <td>Disposition</td>
	        <td>CallerID</td>
	        <td>Starting Time</td>
	       	<td>Length in Seconds</td>
	        <td>Cost</td>
	</tr>
	<TMPL_LOOP NAME="cdr_list">
		<tr>
                        <td><TMPL_VAR NAME="destination"></td> 
                        <td><TMPL_VAR NAME="disposition"></td> 
                        <td><TMPL_VAR NAME="callerid"></td> 
                        <td><TMPL_VAR NAME="callstart"></td> 
                        <td><TMPL_VAR NAME="billseconds"></td> 
                        <td><TMPL_VAR NAME="cost"></td> 
		</tr>
	</TMPL_LOOP>
</table>

List Calling Cards<br>
<table class="default">
	<tr class="header">
		<td>Card Number</td>
		<td>Pin</td>
		<td>Pricelist</td>
		<td>Value</td>
		<td>Used</td>
		<td>Days Valid For</td>
		<td>Creation</td>
		<td>First Use</td>
		<td>Expiration</td>
		<td>In Use?</td>
		<td>Status</td>
	</tr>
	<TMPL_LOOP NAME="card_list">
		<tr>
 			<td><a href="astpp-users.cgi?mode=Calling Cards&number=<TMPL_VAR NAME="number">&action=View Card"><TMPL_VAR NAME="number"></a></td>
                        <td><TMPL_VAR NAME="pin"></td> 
                        <td><TMPL_VAR NAME="brand"></td> 
                        <td><TMPL_VAR NAME="value"></td> 
                        <td><TMPL_VAR NAME="used"></td> 
                        <td><TMPL_VAR NAME="validfordays"></td> 
                        <td><TMPL_VAR NAME="created"></td> 
                        <td><TMPL_VAR NAME="firstused"></td> 
                        <td><TMPL_VAR NAME="expiry"></td> 
                        <td><TMPL_VAR NAME="inuse"></td> 
                        <td><TMPL_VAR NAME="cardstat"></td>
		</tr>
	</TMPL_LOOP>
</table>
<TMPL_VAR NAME="pagination">
<table>
      <tr>
	<td><TMPL_VAR NAME="status"></td>
      </tr>
  </table>
</form>
