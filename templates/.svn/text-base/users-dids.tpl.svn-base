<form method="post" action="/cgi-bin/astpp/astpp-users.cgi?mode=DIDs" enctype="multipart/form-data">
<table class="default">
	<tr class="header">
		<td><input type="hidden" name="mode" value="DIDs"/>Order DID</td>
	</tr>
	<tr>
		<td><TMPL_VAR NAME="order_dids"></td>
		<td><input type="submit" name="action" value="Purchase DID" /></td>
	</tr>
</table>
<table class="default">
	<tr class="header">
		<td colspan=12>All costs are in 1/100 of a penny</td>
	</tr>
	<tr class="header">
		<td><input type="hidden" name="mode" value="DIDs"/><input type="hidden" name="did" value="<TMPL_VAR NAME="number">"/>Number</td>
		<td>Country</td>
		<td>Province</td>
		<td>City</td>
		<td><acronym title="To dial a sip address set it to: SIP/ipaddress.  To dial a pstn number just enter the telephone number here.">Dialstring</acronym></td>
		<td>Action</td>
	</tr>
	<tr>
		<td><TMPL_VAR NAME="number"></td>
		<td><TMPL_VAR NAME="country"></td>
		<td><TMPL_VAR NAME="province"></td>
		<td><TMPL_VAR NAME="city"></td>
		<td><input type="text" name="extensions"  size="20" default="<TMPL_VAR NAME="extensions">"/></td>
		<td><input type="submit" name="action" value="Save..." /></td>
	</tr>
</table>
<table class="default">
	<tr class="header">
		<td>Number</td>
		<td>Connect Fee</td>
		<td>Included Seconds</td>
		<td>Cost</td>
		<td>Monthly Fee</td>
		<td>Country</td>
		<td>Province/State</td>
		<td>City</td>
		<td>Extension to dial</td>
		<td colspan=2>Action</td>
	</tr>
	<TMPL_LOOP NAME="did_list">
		<tr>
			<td><TMPL_VAR NAME="number"></td>
			<td><TMPL_VAR NAME="connect_fee"></td>
			<td><TMPL_VAR NAME="included"></td>
			<td><TMPL_VAR NAME="cost"></td>
			<td><TMPL_VAR NAME="montly_cost"></td>
			<td><TMPL_VAR NAME="country"></td>
			<td><TMPL_VAR NAME="province"></td>
			<td><TMPL_VAR NAME="city"></td>
			<td><TMPL_VAR NAME="extension"></td>
			<td><a href="astpp-users.cgi?mode=DIDs&limit=0&did=<TMPL_VAR NAME="number">&action=Edit...">Edit...</a>
			<a href="astpp-users.cgi?mode=DIDs&limit=0&did=<TMPL_VAR NAME="number">&action=Remove...">Remove...</a></td>
		</tr>
	</TMPL_LOOP>
</table>
<TMPL_VAR NAME="pagination">
<TMPL_VAR NAME="status">
