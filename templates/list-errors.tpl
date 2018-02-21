<table class="viewcdrs">
	<tr>
		<td colspan=7><form method="post" action="/cgi-bin/astpp-admin/astpp-admin.cgi?mode=List%20Errors" enctype="multipart/form-data"></td>
	</tr>
	<tr class="header">
		<td>UniqueID</td>
		<td>Date</td>
		<td>CallerID</td>
		<td>Source</td>
		<td>Dest</td>
		<td>Dest.Context</td>
		<td>Channel</td>
		<td>Dest.Channel</td>
		<td>Last App </td>
		<td>Last Data</td>
		<td>Duration</td>
		<td>BillSec</td>
		<td>Disposition</td>
		<td>AMAFlags</td>
		<td>AccountCode</td>
		<td>UniqueID</td>
		<td>UserField</td>
		<td>Cost</td>
		<td>Action</td>
	</tr>
	<TMPL_LOOP NAME="cdrlist">
		<tr class="<TMPL_VAR NAME="class">">
			<td><TMPL_VAR NAME="uniqueid"></td>
			<td><TMPL_VAR NAME="calldate"></td>
			<td><TMPL_VAR NAME="clid"></td>
			<td><TMPL_VAR NAME="src"></td>
			<td><TMPL_VAR NAME="dst"></td>
			<td><TMPL_VAR NAME="dcontext"></td>
			<td><TMPL_VAR NAME="channel"></td>
			<td><TMPL_VAR NAME="dstchannel"></td>
			<td><TMPL_VAR NAME="lastapp"></td>
			<td><TMPL_VAR NAME="lastdata"></td>
			<td><TMPL_VAR NAME="duration"></td>
			<td><TMPL_VAR NAME="billsec"></td>
			<td><TMPL_VAR NAME="disposition"></td>
			<td><TMPL_VAR NAME="amaflags"></td>
			<td><TMPL_VAR NAME="accountcode"></td>
			<td><TMPL_VAR NAME="uniqueid"></td>
			<td><TMPL_VAR NAME="userfield"></td>
			<td><TMPL_VAR NAME="cost"></td>
			<td><a href="astpp-admin.cgi?mode=List Errors&uniqueid=<TMPL_VAR NAME="uniqueid">&action=Deactivate...">Deactivate...</a></td>
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
