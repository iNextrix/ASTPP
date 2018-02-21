<br>Welcome to ASTPP - The Open Source Voip Billing Solution
<br>Please select a function from the menu above.
<table class="default" width='60%'>
<tr class="header" align='center'><th colspan=5>System Overview</th></tr>
<!--<tr class="header"><td colspan=5>Account Counts</td></tr>-->
<tr class="header">
	<td>Customers</td>
	<td>Resellers</td>
	<td>Providers</td>
	<td>Admins</td>
	<td>Call Shops</td>
</tr>
<tr class="rowone">
	<td><TMPL_VAR NAME="customer_count"></td>
	<td><TMPL_VAR NAME="reseller_count"></td>
	<td><TMPL_VAR NAME="vendor_count"></td>
	<td><TMPL_VAR NAME="callshop_count"></td>
	<td><TMPL_VAR NAME="admin_count"></td>
</tr>
<tr class="header">
	<td>Total Funds Owed Me</td>
	<td>Total Funds I Owe</td>
	<td>DIDs</td>
	<td>Unbilled CDRs</td>
</tr>
<tr class="rowone">
	<td>$<TMPL_VAR NAME="total_owing"></td>
	<td>$<TMPL_VAR NAME="total_due"></td>
	<td align=center><a href="/cgi-bin/astpp-admin/astpp-admin.cgi?mode=Manage%20DIDs"><TMPL_VAR NAME="dids"></a></td>
	<td align=center><a href="/cgi-bin/astpp-admin/astpp-admin.cgi?mode=List%20Errors"><TMPL_VAR NAME="unbilled_cdrs"></a></td>
</tr>
<tr class="header">
	<td>Calling Cards in use</td>
	<td>Total Active Cards</td>
	<td>Unused Card Balance</td>
	<td>Used Card Balance</td>
</tr>
<tr class="rowone">
	<td align=center><a href="/cgi-bin/astpp-admin/astpp-admin.cgi?mode=List%20Cards"><TMPL_VAR NAME="calling_cards_in_use"></a></td>
	<td align=center><a href="/cgi-bin/astpp-admin/astpp-admin.cgi?mode=List%20Cards"><TMPL_VAR NAME="calling_cards_active"></a></td>
	<td align=center><a href="/cgi-bin/astpp-admin/astpp-admin.cgi?mode=List%20Cards"><TMPL_VAR NAME="calling_cards_unused"></a></td>
	<td align=center><a href="/cgi-bin/astpp-admin/astpp-admin.cgi?mode=List%20Cards"><TMPL_VAR NAME="calling_cards_used"></a></td>
</tr>
<tr><td><br/><br/><br/></td></tr>
</table>
