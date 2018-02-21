<table class="default" width='90%'>
	<tr>
		<td colspan=7><form method="post" action="/cgi-bin/astpp-admin/astpp-admin.cgi?mode=List%20Errors" enctype="multipart/form-data"></td>
	</tr>
	<tr class="header">
		<td>Card Number</td>
		<td>UniqueID</td>
		<td>Date</td>
		<td>CallerID</td>
		<td>Dest</td>
		<td>BillSec</td>
		<td>Disposition</td>
		<td>Debit</td>
		<td>Credit</td>
		<td>Notes</td>
		<td>Pricelist</td>
		<td>Pattern</td>
	</tr>
	<TMPL_LOOP NAME="cdrlist">
		<tr class="<TMPL_VAR NAME="class">">
			<td><TMPL_VAR NAME="cardnumber"></td>
			<td><TMPL_VAR NAME="uniqueid"></td>
			<td><TMPL_VAR NAME="callstart"></td>
			<td><TMPL_VAR NAME="clid"></td>
			<td><TMPL_VAR NAME="destination"></td>
			<td><TMPL_VAR NAME="seconds"></td>
			<td><TMPL_VAR NAME="disposition"></td>
			<td><TMPL_VAR NAME="debit"></td>
			<td><TMPL_VAR NAME="credit"></td>
			<td><TMPL_VAR NAME="notes"></td>
			<td><TMPL_VAR NAME="pricelist"></td>
			<td><TMPL_VAR NAME="pattern"></td>
		</tr>
	</TMPL_LOOP>
</table>

<table>
      <tr>
	<td><TMPL_VAR NAME="pagination"></td>
	<td><TMPL_VAR NAME="status"></td>
      </tr>
  </table>
</form>
