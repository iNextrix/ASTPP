<input name="mode" value="Account" type="hidden">
<form method="post" action="/cgi-bin/astpp/astpp-users.cgi?mode=Account" enctype="multipart/form-data">
<input type="hidden" name="mode" value="Account"/>
View Account<br>
Account Name: <TMPL_VAR NAME="account_name">
<table class="default">
	<tr class="header">
		<td></td>
	</tr>
	<tr class="header">
		<td colspan=6><a href="astpp-users.cgi?mode=Download" target="_blank">Download CDRs as CSV file (Right Click and select SAVE AS</a></td>
	</tr>
<table class="default">
	<tr class="header">
		<td>Id</td>
		<td>Description</td>
		<td>Sweep</td>
		<td>Amount</td>
	</tr>
        <TMPL_LOOP NAME="charge_list">
		<tr>
			<td><TMPL_VAR NAME="id"></td>
			<td><TMPL_VAR NAME="description"></td>
			<td><TMPL_VAR NAME="sweep"></td>
			<td><TMPL_VAR NAME="charge"></td>
		</tr>
	</TMPL_LOOP>
	<tr bgcolor=ccccff>
		<td colspan=5>DIDs</td>
	</tr>
	<tr bgcolor=ccccff>
		<td>Number</td>
		<td>Monthly Fee</td>
		<td>Action</td>
		<td colspan=2></td>
	</tr>
        <TMPL_LOOP NAME="did_list">
		<tr>
			<td><TMPL_VAR NAME="number"></td>
			<td><TMPL_VAR NAME="charge"></td>
			<td><a href="astpp-users.cgi?mode=Account&did=<TMPL_VAR NAME="number">&action=Remove...">Remove...</a></td>
		</tr>
	</TMPL_LOOP>
	<tr>
		<td>Order DID</td>
	</tr>
	<tr>
		<td><TMPL_VAR NAME="order_dids"></td>
		<td><input type="submit" name="action" value="Purchase DID" /></td>
	</tr>
</table>
<table>
	<tr>
		<td colspan=2><TMPL_VAR NAME="first_name"> <TMPL_VAR NAME="middle_name"> <TMPL_VAR NAME="last_name"></td>
	</tr>
	<tr>
		<td width=400><TMPL_VAR NAME="company_name"></td>
		<td>Phone: <TMPL_VAR NAME="telephone_1"></td>
	</tr>
	<tr>
		<td><TMPL_VAR NAME="address_1"></td>
		<td>Phone 2: <TMPL_VAR NAME="telephone_2"></td>
	</tr>
	<tr>
		<td><TMPL_VAR NAME="address_2"></td>
		<td>Facsimile: <TMPL_VAR NAME="fascimilie"></td>
	</tr>
	<tr>
		<td><TMPL_VAR NAME="address_3"></td>
		<td>Email: <TMPL_VAR NAME="email"></td>
	</tr>
	<tr>
		<td colspan=2><TMPL_VAR NAME="city">,<TMPL_VAR NAME="province">,<TMPL_VAR NAME="country"></td>
	</tr>
	<tr>
		<td colspan=2><TMPL_VAR NAME="postal_code"></td>
	</tr>
</table>
Account: </i><b><TMPL_VAR NAME="account_name"></b><i>balance: </i><b><TMPL_VAR NAME="account_balance"></b></i> with a credit limit of </i><b><TMPL_VAR NAME="account_credit_limit"></b></i>

<table class="default">
      <tr class="header">
        <td>UniqueID</td>
        <td>Date & Time</td>
        <td>Caller*ID</td>
        <td>Called Number</td>
        <td>Disposition</td>
        <td>Billable Seconds</td>
        <td>Charge</td>
        <td>Credit</td>
        <td>Notes</td>    
      </tr>
      <TMPL_LOOP NAME="cdr_list">
            <TR>
               <TD><TMPL_VAR NAME="uniqueid"></TD>
               <TD><TMPL_VAR NAME="callstart"></TD>
               <TD><TMPL_VAR NAME="callerid"></TD>           
               <TD><TMPL_VAR NAME="callednum"></TD>
               <TD><TMPL_VAR NAME="disposition"></TD>
               <TD><TMPL_VAR NAME="billseconds"></TD>
               <TD><TMPL_VAR NAME="debit"></TD>
               <TD><TMPL_VAR NAME="credit"></TD>
               <TD><TMPL_VAR NAME="notes"></TD>
             </TR>
      </TMPL_LOOP>
</table>
<TMPL_VAR NAME="pagination">

<table>
      <tr>
	<td><TMPL_VAR NAME="status"></td>
      </tr>
  </table>
</form>
