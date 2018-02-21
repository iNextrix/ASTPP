<input name="mode" value="View Details" type="hidden">
<table class="default">
	<tr class="header">
		<td>Either select or enter the account number:</td>
		<td>Action</td>
	</tr>
	<tr class="rowone">
		<td><TMPL_VAR NAME="accountlist">  <input name="accountnum" value="<TMPL_VAR NAME="accountnum">" size="20" type="text"></td>
		<td><input name="action" value="Information" type="submit"></td>
	</tr>
</table>

<table class="default">
	<tr class="header">
		<td>Account Number</td>
		<td>Name</td>
		<td>Company</td>
		<td>Telephone</td>
		<td>Telephone 2</td>
		<td>Fax</td>
	</tr>
	<tr>
		<td><TMPL_VAR NAME="accountnum"></td>
		<td><TMPL_VAR NAME="first_name"> <TMPL_VAR NAME="middle_name"> <TMPL_VAR NAME="last_name"></td>
		<td><TMPL_VAR NAME="company"></td>
		<td><TMPL_VAR NAME="telephone_1"></td>
		<td><TMPL_VAR NAME="telephone_2"></td>
		<td><TMPL_VAR NAME="fascimilie"></td>
	</tr>
	<tr class="header">
		<td>Address Line 1</td>
		<td>Address Line 2</td>
		<td>Address Line 3</td>
		<td>Email</td>
		<td>City</td>
		<td>Province</td>
		<td>Postal Code</td>
		<td>Country</td>
	</tr>
	<tr>
		<td><TMPL_VAR NAME="address_1"></td>
		<td><TMPL_VAR NAME="address_2"></td>
		<td><TMPL_VAR NAME="address_3"></td>
		<td><TMPL_VAR NAME="email"></td>
		<td><TMPL_VAR NAME="city"></td>
		<td><TMPL_VAR NAME="province"></td>
		<td><TMPL_VAR NAME="postal_code"></td>
		<td><TMPL_VAR NAME="country"></td>
	</tr>
	<tr class="header">
		<td colspan=2>Dialed Number Mods</td>
		<td>Max Channels</td>
		<td>Pin</td>
		<td>Balance</td>
		<td>Credit Limit</td>
	</tr>
	<tr>
		<td colspan=2><TMPL_VAR NAME="dialed_modify"></td>
		<td><TMPL_VAR NAME="maxchannels"></td>
		<td><TMPL_VAR NAME="pin"></td>
		<td><TMPL_VAR NAME="balance"> <TMPL_VAR NAME="currency"></td>
		<td><TMPL_VAR NAME="credit_limit"> <TMPL_VAR NAME="currency"></td>
	</tr>
</table>
<table class="default">
	<tr class="header">
		<td colspan=5>Charges</td>
	</tr>
	<tr class="header">
		<td>Action</td>
		<td>ID</td>
		<td>Description</td>
		<td>Cycle</td>
		<td>Amount</td>
	</tr>
	<TMPL_LOOP NAME="chargelist">
		<tr>
			<td><a href="astpp-admin.cgi?mode=View Details&chargeid=<TMPL_VAR NAME="id">&accountnum=<TMPL_VAR NAME="accountnum">&action=Remove Charge...">Remove Charge...</a></td>
			<td><TMPL_VAR NAME="id"></td>
			<td><TMPL_VAR NAME="description"></td>
			<td><TMPL_VAR NAME="sweep"></td>
			<td>$<TMPL_VAR NAME="cost"></td>
		</tr>	
	</TMPL_LOOP>
</table>
<table class="default">
	<tr class="header">
		<td colspan=3>Add Charge</td>
	</tr>
	<tr>
		<td><TMPL_VAR NAME="applyable_charges"></td>
		<td><input name="id" size="3" type="text"></td>
		<td><input name="action" value="Add Charge..." type="submit"></td>
	</tr>
</table>
<table class="default">
	<tr class="header">
		<td colspan=3>DIDs</td>
	</tr>
	<tr class="header">
		<td>Number</td>
		<td>Monthly Fee</td>
		<td>Action</td>
	</tr>
	<TMPL_LOOP NAME="account_did_list">
		<tr>
			<td><TMPL_VAR NAME="number"></td>
			<td>$<TMPL_VAR NAME="cost"></td>
			<td><a href="astpp-admin.cgi?mode=View Details&accountnum=<TMPL_VAR NAME="accountnum">&DID=<TMPL_VAR NAME="number">&action=Remove DID">Remove DID</a></td>
		</tr>
	</TMPL_LOOP>
	<tr>
		<td><TMPL_VAR NAME="available_dids"></td>
		<td><input name="action" value="Purchase DID" type="submit"></td>
	</tr>
</table>
<table class="default">
	<tr class="header">
		<td colspan=3>ANI & Prefix Mapping - Either enter prefix or ANI/CLID</td>
	</tr>
	<tr class="header">
		<td>ANI/CLID/PREFIX</td>
		<td>Context - Blank = default</td>
		<td>Action</td>
	<TMPL_LOOP NAME="account_ani_list">
		<tr>
			<td><TMPL_VAR NAME="number"></td>
			<td><TMPL_VAR NAME="context"></td>
			<td><a href=\"astpp-admin.cgi?mode=View Details&accountnum=<TMPL_VAR NAME="accountnum">&ANI=<TMPL_VAR NAME="number">&action=Remove ANI">Remove ANI</a></td>
		</tr>
	</TMPL_LOOP>
	<tr>
		<td><input name="ANI" size="20" type="text"></td>
		<td><input name="context" size="20" type="text"></td>
		<td><input name="action" value="Map ANI" type="submit"></td>
	</tr>
</table>

<table class="default">
	<tr class="header">
		<td colspan=4>IP Address Mapping</td>
	</tr>
	<tr class="header">
		<td>IP Address</td>
		<td>Prefix</td>
		<td>Context - blank = default</td>
		<td>Action</td>
	</tr>
	<TMPL_LOOP NAME="account_ip_list">
		<tr>
			<td><TMPL_VAR NAME="ip"></td>
			<td><TMPL_VAR NAME="prefix"></td>
			<td><TMPL_VAR NAME="context"></td>
			<td><a href="astpp-admin.cgi?mode=View Details&accountnum=<TMPL_VAR NAME="accountnum">&prefix=<TMPL_VAR NAME="prefix">&ip=<TMPL_VAR NAME="ip">&action=Remove IP">Remove IP</a></td>
		</tr>
	</TMPL_LOOP>
	<tr>
		<td><input name="ip" size="16" type="text"></td>
		<td><input name="prefix" size="16" type="text"></td>
		<td><input name="ipcontext" size="16" type="text"></td>
		<td><input name="action" value="Map IP" type="submit"></td>
	</tr>
</table>
<table class="default">
	<tr class="header">
		<td colspan=3>Post Charge to Account</td>
	</tr>
	<tr class="header">
		<td>Description</td>
		<td>Charge in <TMPL_VAR NAME="currency"></td>
		<td>Action</td>
	</tr>
	<tr>
		<td><input name="desc" size="16" type="text"></td>
		<td><input name="amount" size="8" type="text"></td>
		<td><input name="action" value="Post Charge..." type="submit"></td>
	</tr>
</table>

<table class="default">
	<tr class="header">
		<td colspan=5>IAX2 & SIP Accounts</td>
	</tr>
	<tr class="header">
		<td>Tech</td>
		<td>Type</td>
		<td>Username</td>
		<td>Password</td>
		<td>Context</td>
	</tr>
	<TMPL_LOOP NAME="account_device_list">
		<tr>
			<td><TMPL_VAR NAME="tech"></td>
			<td><TMPL_VAR NAME="type"></td>
			<td><TMPL_VAR NAME="username"></td>
			<td><TMPL_VAR NAME="secret"></td>
			<td><TMPL_VAR NAME="context"></td>
		</tr>
	</TMPL_LOOP>
</table>
<table class="default">
	<tr class="header">
		<td colspan=5>Invoices</td>
	</tr>
	<tr class="header">
		<td>Invoice Number</td>
		<td>Invoice Date</td>
		<td>Invoice Total</td>
		<td>HTML View</td>
		<td>PDF View</td>
	</tr>
	<TMPL_LOOP NAME="account_invoice_list">
		<tr>
			<td><TMPL_VAR NAME="invoiceid"></td>
			<td><TMPL_VAR NAME="date"></td>
			<td><TMPL_VAR NAME="value"></td>
			<td><a href="astpp-admin.cgi?mode=View Invoice&format=html&invoiceid=<TMPL_VAR NAME="invoiceid">">View</a></td>
			<td><a href="astpp-admin.cgi?mode=View Invoice&format=pdf&invoiceid=<TMPL_VAR NAME="invoiceid">">View</a></td>
		</tr>
	</TMPL_LOOP>
	
</table>
	
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
	<td>Cost</td>
	<td>Profit</td>
      </tr>
      <TMPL_LOOP NAME="account_cdr_list">
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
               <TD><TMPL_VAR NAME="cost"></TD>
               <TD><TMPL_VAR NAME="profit"></TD>
             </TR>
      </TMPL_LOOP>
</table>

<table>
      <tr>
	<td><TMPL_VAR NAME="pagination"></td>
	<td><TMPL_VAR NAME="status"></td>
      </tr>
  </table>
</form>
