<form method="post" action="/cgi-bin/astpp-admin/astpp-admin.cgi?mode=List%20Accounts" enctype="multipart/form-data">
<table>
	<tr>
		<input type="hidden" name="mode" value="List Accounts"/>
		<td colspan=9 align=center><TMPL_VAR NAME="status"></td>
	</tr>
	<tr class="header">
		<td colspan=9><TMPL_VAR NAME="account_types"></td>
	</tr>
	<tr class="header">
		<td colspan=9><input type="submit" name="Refresh" value="Refresh" /></td>
	</tr>
	<tr class="header">
		<td>Card Number</td>
		<td>Account Number</td>
		<td>Pricelist</td>
		<td>Balance</td>
		<td>Credit Limit</td>
		<td><acronym title="Billing Cycle (How frequently this customer is billed.  Only applies to postpaid accounts.">Cycle</acronym></td>
		<td><acronym title="Post To External (This would be for postpaid customers who's cdrs are to be posted to an external billing application such as oscommerce at the intervals specified in the cycle field.">P.T.E.</acronym></td>
		<td>Reseller</td>
	</tr>
	<TMPL_LOOP NAME="account_list">
		<TR>
			<TD><TMPL_VAR NAME="cc"></td>
          <TD><a href="astpp-admin.cgi?mode=View Details&accountnum=<TMPL_VAR NAME="name">&action=Information..."><TMPL_VAR NAME="name"></a></TD>
          <TD><TMPL_VAR NAME="pricelist"></TD>
          <TD><TMPL_VAR NAME="balance"></TD>
          <TD><TMPL_VAR NAME="credit_limit"></TD>
          <TD><TMPL_VAR NAME="sweep"></TD>
          <TD><TMPL_VAR NAME="posttoexternal"></TD>
          <TD><TMPL_VAR NAME="reseller"></TD>
            </TR>
      </TMPL_LOOP>
	<tr bgcolor=ff8800>
		<td colspan=3>Number of Accounts: <TMPL_VAR NAME="account_count"></td>
		<td colspan=6>Total Owing: $ <TMPL_VAR NAME="total_owing"></td>
	</tr>
</table>
